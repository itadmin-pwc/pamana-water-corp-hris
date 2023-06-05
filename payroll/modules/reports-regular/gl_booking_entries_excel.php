<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','D:\wamp\php\PEAR');
	require_once 'Spreadsheet/Excel/Writer.php';
	
	
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$workbook = new Spreadsheet_Excel_Writer();
	$inqTSObj=new inqTSObj();
	
	
	//Set Font Style
	$headerFormat = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 0,
									  'Align' => 'left',
						  			num_format=>0));
	$headerFormat->setFontFamily('Calibri'); 
	$headerFormat->setNumFormat('0.00');
	
	$headerFormat_right = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 0,
									  'Align' => 'right',
						  num_format=>0));
	$headerFormat_right->setFontFamily('Calibri'); 
	$headerFormat_right->setNumFormat('0.00');
	
	$detail_Format = $workbook->addFormat(array('Size' => 11,
								  'Color' => 'black',
								  'bold'=> 0,
								  'border' => 0,
								  'Align' => 'left',
						  num_format=>0));
	$detail_Format->setFontFamily('Calibri');
	
	$detail_right_Format = $workbook->addFormat(array('Size' => 11,
								  'Color' => 'black',
								  'bold'=> 0,
								  'border' => 0,
								  'Align' => 'right',
						  		  'setNumFormat'=>'0.00'));
	$detail_right_Format->setFontFamily('Calibri');
	$detail_right_Format->setNumFormat('0.00');
	
	$headerBorder    = $workbook->addFormat(array('Size' => 10,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'merge'));
	
	$headerBorder->setFontFamily('Calibri'); 
	
	//Set Cell Color
	$workbook->setCustomColor(1,155,205,255);
	
	$TotalBorder    = $workbook->addFormat(array('Align' => 'right','bold'=> 1,'border'=>1,'fgColor' => 'white'));
	$TotalBorder->setFontFamily('Calibri'); 
	$TotalBorder->setTop(5); 
	$detailrBorder   = $workbook->addFormat(array('border' =>1,'Align' => 'right'));
	$detailrBorder->setFontFamily('Calibri'); 
	$detailrBorderAlignRight2   = $workbook->addFormat(array('Align' => 'left'));
	$detailrBorderAlignRight2->setFontFamily('Calibri');
	$workbook->setCustomColor(12,183,219,255);
	
	


 	$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
	$filename = "GL Booking Entry (".$_SESSION["company_code"]."-".$_SESSION["pay_group"]."-".$_SESSION["pay_category"].").xls";
	
	
	//Column titles
	//Data loading
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	/*Get Data*/
	$compCode 				= $_SESSION['company_code'];
	$inqTSObj->compCode     = $compCode;
	$payPd       			= $_GET['payPd'];
	$branch					= $_GET['branch'];
	$loc					= $_GET['loc'];
	$arrPayPd 				= $inqTSObj->getSlctdPd($compCode,$payPd);
	$dt['Pdate']			= $arrPayPd['pdPayable'];
	$resSearch				 = $inqTSObj->getEmpInq();
	switch($_SESSION['pay_category']) {
		case 1:
			$payCat = "Executive";
		break;
		case 2:
			$payCat = "Confidential";
		break;
		case 3:
			$payCat = "Non Confidential";
		break;
		case 9:
			$payCat = "Resigned";
		break;
	}
	
	
	//Get Branch
	if ($branch != 0) 
				$filter = " AND tblPayJournal.strCode='$branch'";
		
	if($branchInfo['brnShortDesc'])
			$dt['Title']	= "(".$branchInfo['brnShortDesc']."$locDesc)";
			
	if ($_SESSION['pay_category'] != 9) 
	{
		 $qryBranch = " SELECT  tblBranch.brnDesc, tblBranch.glCodeStr
						FROM  tblBranch INNER JOIN
						tblPayJournal ON tblBranch.compCode = tblPayJournal.compCode AND tblBranch.glCodeStr = tblPayJournal.strCode2
							Where payGrp='{$_SESSION['pay_group']}' 
						  		  AND payCat='{$_SESSION['pay_category']}' 
						  	      AND pdYear='{$arrPayPd['pdYear']}' 
						          AND pdNumber='{$arrPayPd['pdNumber']}'
						          AND tblPayJournal.compCode='{$_SESSION['company_code']}'  AND brnStat='A'
						          $filter
						GROUP BY tblBranch.brnDesc, tblBranch.glCodeStr";
		$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($qryBranch));
	} 
	else 
	{
		$qryEmp = "Select empLastName,concat(empLastName,', ', empFirstName,' ', empMidName) as fullname,empNo from tblEmpMast where empNo IN
					(
						Select empNo from tblPayJournal
						  Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}' 
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND tblPayJournal.compCode='{$_SESSION['company_code']}' 
					)
					And compCode='{$_SESSION['company_code']}' 
					order by empLastName,empFirstName,empMidName";
		$arrEmp = $inqTSObj->getArrRes($inqTSObj->execQry($qryEmp));
	}
	
	
	//Content Per Branch
	if ($_SESSION['pay_category'] != 9) 
	{
		   $sqlGLpos = "SELECT     tblGLCodes.glCodeDesc AS glDesc, sum(Amount) as Amount,tblGLCodes.strCode
							FROM         tblPayJournal Left JOIN
						  tblGLCodes ON tblPayJournal.majCode2 = tblGLCodes.majCode AND tblPayJournal.compGLCode = tblGLCodes.compGLCode AND 
						  tblPayJournal.strCode2 = tblGLCodes.strCode AND tblPayJournal.minCode2 = tblGLCodes.minCode 
						  Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}' 
						  AND Amount>0
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND tblPayJournal.compCode='{$_SESSION['company_code']}'
						  $filter group by tblGLCodes.glCodeDesc ,tblGLCodes.strCode  order by glCodeDesc";
		  $sqlGLneg = "SELECT     tblGLCodes.glCodeDesc AS glDesc ,sum(Amount*-1) as Amount,tblGLCodes.strCode
							FROM         tblPayJournal Left JOIN
						  tblGLCodes ON tblPayJournal.majCode2 = tblGLCodes.majCode AND tblPayJournal.compGLCode = tblGLCodes.compGLCode AND 
						  tblPayJournal.strCode2 = tblGLCodes.strCode AND tblPayJournal.minCode2 = tblGLCodes.minCode 
						  Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}'  
						  AND Amount<0 
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND tblPayJournal.compCode='{$_SESSION['company_code']}'
						 $filter group by tblGLCodes.glCodeDesc ,tblGLCodes.strCode order by glCodeDesc";
	} 
	else 
	{
		  $sqlGLpos = "SELECT tblGLCodes.glCodeDesc AS glDesc ,sum(Amount) as Amount,empNo
						FROM tblPayJournal Left JOIN
						  tblGLCodes ON tblPayJournal.majCode2 = tblGLCodes.majCode AND tblPayJournal.compGLCode = tblGLCodes.compGLCode AND 
						  tblPayJournal.strCode2 = tblGLCodes.strCode AND tblPayJournal.minCode2 = tblGLCodes.minCode 
						  Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}' 
						  AND Amount>0
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND tblPayJournal.compCode='{$_SESSION['company_code']}'
						  $filter 
						  group by empNo, tblGLCodes.glCodeDesc 
						  order by glCodeDesc Desc";
		  $sqlGLneg = "SELECT tblGLCodes.glCodeDesc AS glDesc ,sum(Amount*-1) as Amount,empNo
						  FROM tblPayJournal Left JOIN
						  tblGLCodes ON tblPayJournal.majCode2 = tblGLCodes.majCode AND tblPayJournal.compGLCode = tblGLCodes.compGLCode AND 
						  tblPayJournal.strCode2 = tblGLCodes.strCode AND tblPayJournal.minCode2 = tblGLCodes.minCode 
						  Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}'  
						  AND Amount<0 
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND tblPayJournal.compCode='{$_SESSION['company_code']}'
						 $filter 
						 group by empNo, tblGLCodes.glCodeDesc  
						 order by glCodeDesc Desc";
	
	}				  
	 $arrGLpos = $inqTSObj->getArrRes($inqTSObj->execQry($sqlGLpos));
	 $arrGLneg = $inqTSObj->getArrRes($inqTSObj->execQry($sqlGLneg));
	 
	 
	//Display Branch
	$workbook->send($filename);
	//
	$worksheet=&$workbook->addWorksheet("JOURNAL ENTRIES");
	$ctr=0;
	$worksheet->setColumn(0,0,80);
			$worksheet->setColumn(1,1,15);
			$worksheet->setColumn(1,2,15);
	
			$worksheet->write(0,0,$inqTSObj->getCompanyName($compCode),$headerFormat);
			$worksheet->write(1,0,'GL BOOKING ENTRIES REPORT ('.$payCat.')',$headerFormat);
			$worksheet->write(2,0,"Payroll Date: ".date("m/d/Y",strtotime($dt['Pdate'])),$headerFormat);
			
	$ctr=4;
	if ($_SESSION['pay_category'] != 9) 
	{
		foreach($arrBranch as $arrBranch_val)
		{
			$worksheet->write($ctr,0,$arrBranch_val["brnDesc"],$headerFormat);
			$ctr++;
			$worksheet->write($ctr,0,'DESCRIPTION',$headerFormat);
			$worksheet->write($ctr,1,'DEBIT',$headerFormat_right);
			$worksheet->write($ctr,2,'CREDIT',$headerFormat_right);
			$ctr++;
			//Set Column
			
			
			$GTotCre = 0;
			foreach ($arrGLpos as $val)
			{
				if ($arrBranch_val['glCodeStr'] == $val['strCode']) 
				{
					$worksheet->write($ctr,0,$val['glDesc'],$detail_Format);
					$worksheet->write($ctr,1,$val['Amount'],$detail_right_Format);
					$GTotCre += round($val['Amount'],2);
					$ctr++;
				}
			}	
			
			
			$GTotDeb = 0;	
			foreach ($arrGLneg as $val)
			{
				if ($arrBranch_val['glCodeStr'] == $val['strCode']) 
				{
					$worksheet->write($ctr,0,$val['glDesc'],$detail_Format);
					$worksheet->write($ctr,2,$val['Amount'],$detail_right_Format);
					$GTotDeb += round($val['Amount'],2);
					$ctr++;
				}
			}
			
			
			$worksheet->write($ctr,0,'BRANCH TOTAL',$headerFormat);
			$worksheet->write($ctr,1,$GTotCre,$headerFormat_right);
			$worksheet->write($ctr,2,str_replace("-","",sprintf('%.2f',$GTotDeb)),$headerFormat_right);
			$ctr +=3;
			
		}
	}
	else
	{
		foreach($arrEmp as $arrBranch_val)
		{
			$worksheet->write($ctr,0,$arrBranch_val["fullname"],$headerFormat);
			$ctr++;
			$worksheet->write($ctr,0,'DESCRIPTION',$headerFormat);
			$worksheet->write($ctr,1,'DEBIT',$headerFormat_right);
			$worksheet->write($ctr,2,'CREDIT',$headerFormat_right);
			$ctr++;
			//Set Column
			
			
			$GTotCre = 0;
			foreach ($arrGLpos as $val)
			{
				if ($arrBranch_val['empNo'] == $val['empNo']) 
				{
					$worksheet->write($ctr,0,$val['glDesc'],$detail_Format);
					$worksheet->write($ctr,1,$val['Amount'],$detail_right_Format);
					$GTotCre += round($val['Amount'],2);
					$ctr++;
				}
			}	
			
			
			$$GTotDeb = 0;	
			foreach ($arrGLneg as $val)
			{
				if ($arrBranch_val['empNo'] == $val['empNo']) 
				{
					$worksheet->write($ctr,0,$val['glDesc'],$detail_Format);
					$worksheet->write($ctr,2,$val['Amount'],$detail_right_Format);
					$GTotDeb += round($val['Amount'],2);
					$ctr++;
				}
			}
			
			
			$worksheet->write($ctr,0,'EMPLOYEE TOTAL',$headerFormat);
			$worksheet->write($ctr,1,$GTotCre,$headerFormat_right);
			$worksheet->write($ctr,2,str_replace("-","",sprintf('%.2f',$GTotDeb)),$headerFormat_right);
			$ctr +=3;
			
		}
	} 
		
	//$worksheet->setLandscape();	
	$workbook->close();

?>