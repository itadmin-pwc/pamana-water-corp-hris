<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','D:\wamp\php\PEAR');
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	require_once 'Spreadsheet/Excel/Writer.php';
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	

	
	$workbook = new Spreadsheet_Excel_Writer();
	$inqTSObj=new inqTSObj();
	$headerFormat = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'merge'));
	$headerFormat->setFontFamily('Calibri'); 
	$headerBorder    = $workbook->addFormat(array('Size' => 10,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'merge'));
	$headerBorder->setFontFamily('Calibri'); 
	$workbook->setCustomColor(13,155,205,255);
	$TotalBorder    = $workbook->addFormat(array('Align' => 'right','bold'=> 1,'border'=>1,'fgColor' => 'white'));
	$TotalBorder->setFontFamily('Calibri'); 
	$TotalBorder->setTop(5); 
	$detailrBorder   = $workbook->addFormat(array('border' =>1,'Align' => 'right'));
	$detailrBorder->setFontFamily('Calibri'); 
	$detailrBorderAlignRight2   = $workbook->addFormat(array('Align' => 'left'));
	$detailrBorderAlignRight2->setFontFamily('Calibri');
	$workbook->setCustomColor(12,183,219,255);
	$detail   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'right'));
	$detail->setFontFamily('Calibri'); 

	$detail2   = $workbook->addFormat(array('Size' => 10,
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'right'));
	$detail2->setFgColor(12); 
	$detail2->setFontFamily('Calibri'); 
	$Dept   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'left'));
	$Dept->setFontFamily('Calibri'); 
	$Dept2   = $workbook->addFormat(array('Size' => 10,
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'left'));
	$Dept2->setFgColor(12); 
	$Dept2->setFontFamily('Calibri'); 

	$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
	$filename = "monthly_ytd.xls";

	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$payPd = $_GET['payPd'];
	$empDiv = $_GET['empDiv'];
	$empSect = $_GET['empSect'];
	$empDept = $_GET['empDept'];
	if ($empDiv>"" && $empDiv>0) {$empDivfilter = " AND (tblPayrollSummaryHist.empDivCode = '{$empDiv}')";} else {$empDivfilter = "";}
	if ($empDept>"" && $empDept>0) {$empDeptfilter = " AND (tblPayrollSummaryHist.empDepCode = '{$empDept}')";} else {$empDeptfilter = "";}	
	$arrYTD = $inqTSObj->getYTDData($payPd,"$empDivfilter $empDeptfilter");
	$workbook->send($filename);
	$arrPd = explode("-",$_GET['payPd']);
	$pdYear = $arrPd[2];
	
	switch($arrPd[0]) {
		case 1:
			$pdMonth = "Jan $pdYear";
		break;
		case 3:
			$pdMonth = "Feb $pdYear";
		break;
		case 5:
			$pdMonth = "Mar $pdYear";
		break;
		case 7:
			$pdMonth = "Apr $pdYear";
		break;
		case 9:
			$pdMonth = "May $pdYear";
		break;
		case 11:
			$pdMonth = "Jun $pdYear";
		break;
		case 13:
			$pdMonth = "Jul $pdYear";
		break;
		case 15:
			$pdMonth = "Aug $pdYear";
		break;
		case 17:
			$pdMonth = "Sep $pdYear";
		break;
		case 19:
			$pdMonth = "Oct $pdYear";
		break;
		case 21:
			$pdMonth = "Nov $pdYear";
		break;
		case 23:
			$pdMonth = "Dec $pdYear";
		break;
	}	
	$worksheet=&$workbook->addWorksheet("Monthly YTD Report $pdMonth");
	$worksheet->setLandscape();
	$worksheet->freezePanes(array(2, 0));
	$worksheet->setRow(0,16);
	$worksheet->write(0, 0, $inqTSObj->getCompanyName($_SESSION['company_code']),$headerFormat);
	for($i=1;$i<6;$i++) {
		$worksheet->write(0, $i, "",$headerFormat);	
	}
	$worksheet->setColumn(0,0,30);
	$worksheet->setColumn(1,8,20);
	$worksheet->setRow(1,16);
	$worksheet->write(1,0,"DEPARTMENT",$headerBorder);
	$worksheet->write(1,1,"GROSS INCOME",$headerBorder);
	$worksheet->write(1,2,"W/ TAX",$headerBorder);
	$worksheet->write(1,3,"PREV YR WTAX ADJ",$headerBorder);
	$worksheet->write(1,4,"ECOLA",$headerBorder);
	$worksheet->write(1,5,"13TH MONTH NON TAX",$headerBorder);
	$worksheet->write(1,6,"13TH MONTH TAX",$headerBorder);	

		$branch="";
		$ctr=2;
		$i=0;
		$q=0;
		$totRec = count($arrYTD);
		$GtotGross 		= 0;
		$GtotTax 		= 0;
		$GtotEcola 		= 0;
		$Gtot13thNT 	= 0;
		$Gtot13thT 		= 0;
		$arrDept 		= array();
		$col=0;
		foreach($arrYTD as $valYTD){
			if ($valYTD['brnDesc'] != $branch) {
				if ($ch == 0 && $branch!="") {
					$worksheet->setRow($ctr,16);
					$worksheet->write($ctr,0,"Branch Total",$TotalBorder);
					$worksheet->write($ctr,1,number_format($totGross,2),$TotalBorder);
					$worksheet->write($ctr,2,number_format($totTax,2),$TotalBorder);
					$worksheet->write($ctr,3,number_format($totTaxAdj,2),$TotalBorder);
					$worksheet->write($ctr,4,number_format($totEcola,2),$TotalBorder);
					$worksheet->write($ctr,5,number_format($tot13thNT,2),$TotalBorder);
					$worksheet->write($ctr,6,number_format($tot13thT,2),$TotalBorder);					
					$ch++;
					$ctr++;
					$ctr++;
				}			
				$ch			= 0;
				$totGross 	= 0;
				$totTax 	= 0;
				$totTaxAdj 	= 0;
				$totEcola 	= 0;
				$tot13thNT 	= 0;
				$tot13thT 	= 0;
				$worksheet->setRow($ctr,16);
				$worksheet->write($ctr,0,$valYTD['brnDesc'],$headerFormat);
				$ctr++;
				$branch = $valYTD['brnDesc'];
				
			}
			$row = ($col==0) ? $detail2:$detail;
			$row2 = ($col==0) ? $Dept2:$Dept;
			$col = ($col==0) ? 1:0;
			$worksheet->setRow($ctr,16);
			$worksheet->write($ctr,0,ucwords(strtolower($valYTD['deptDesc'])),$row2);
			$worksheet->write($ctr,1,number_format($valYTD['grossearnings'],2),$row);
			$worksheet->write($ctr,2,number_format($valYTD['tax'],2),$row);
			$worksheet->write($ctr,3,number_format($valYTD['YearEnd'],2),$row);
			$worksheet->write($ctr,4,number_format($valYTD['ecola'],2),$row);
			$worksheet->write($ctr,5,number_format($valYTD['N13thNontax'],2),$row);
			$worksheet->write($ctr,6,number_format($valYTD['N13thTax'],2),$row);					
			$ctr++;
			$q++;
			$arrBranchGross[$valYTD['deptDesc']]		+= $valYTD['grossearnings'];
			$arrBranchTax[$valYTD['deptDesc']]		 	+= $valYTD['tax'];
			$arrBranchTaxAdj[$valYTD['deptDesc']]		+= $valYTD['YearEnd'];
			$arrBranchEcola[$valYTD['deptDesc']]	 	+= $valYTD['ecola'];
			$arrBranch13thNT[$valYTD['deptDesc']]	 	+= $valYTD['N13thNontax'];
			$arrBranch13thT[$valYTD['deptDesc']]	 	+= $valYTD['N13thTax'];
			if (!in_array($valYTD['deptDesc'],$arrDept)) {
				$arrDept[] = $valYTD['deptDesc'];
			}	
			$totGross 	+= round($valYTD['grossearnings'],2);
			$totTax 	+= round($valYTD['tax'],2);
			$totTaxAdj 	+= round($valYTD['YearEnd'],2);
			$totEcola 	+= round($valYTD['ecola'],2);
			$tot13thNT 	+= round($valYTD['N13thNontax'],2);
			$tot13thT 	+= round($valYTD['N13thTax'],2);
				if ($q == $totRec) {
					$worksheet->setRow($ctr,16);
					$worksheet->write($ctr,0,"Branch Total",$TotalBorder);
					$worksheet->write($ctr,1,number_format($totGross,2),$TotalBorder);
					$worksheet->write($ctr,2,number_format($totTax,2),$TotalBorder);
					$worksheet->write($ctr,3,number_format($totTaxAdj,2),$TotalBorder);
					$worksheet->write($ctr,4,number_format($totEcola,2),$TotalBorder);
					$worksheet->write($ctr,5,number_format($tot13thNT,2),$TotalBorder);
					$worksheet->write($ctr,6,number_format($tot13thT,2),$TotalBorder);					
					$ctr++;
					$ctr++;

					$worksheet->write($ctr,0,'All Branches',$headerFormat);		
					$arrDept = array_unique($arrDept);
					$totGross 	= 0;
					$totTax 	= 0;
					$totTaxAdj 	= 0;
					$totEcola 	= 0;
					$tot13thNT 	= 0;
					$tot13thT 	= 0;
					for($i=0;$i<count($arrDept);$i++) {
						$ctr++;
						$totGross 	+= $arrBranchGross[$arrDept[$i]];
						$totTax 	+= $arrBranchTax[$arrDept[$i]];
						$totTaxAdj 	+= $arrBranchTaxAdj[$arrDept[$i]];
						$totEcola 	+= $arrBranchEcola[$arrDept[$i]];
						$tot13thNT 	+= $arrBranch13thNT[$arrDept[$i]];
						$tot13thT 	+= $arrBranch13thT[$arrDept[$i]];
						$worksheet->setRow($ctr,16);
						$row = ($col==0) ? $detail2:$detail;
						$row2 = ($col==0) ? $Dept2:$Dept;
						$col = ($col==0) ? 1:0;
						$worksheet->write($ctr,0,ucwords(strtolower($arrDept[$i])),$row2);
						$worksheet->write($ctr,1,number_format($arrBranchGross[$arrDept[$i]],2),$row);
						$worksheet->write($ctr,2,number_format($arrBranchTax[$arrDept[$i]],2),$row);
						$worksheet->write($ctr,3,number_format($arrBranchTaxAdj[$arrDept[$i]],2),$row);
						$worksheet->write($ctr,4,number_format($arrBranchEcola[$arrDept[$i]],2),$row);
						$worksheet->write($ctr,5,number_format($arrBranch13thNT[$arrDept[$i]],2),$row);
						$worksheet->write($ctr,6,number_format($arrBranch13thT[$arrDept[$i]],2),$row);	
											
					}
					$ctr++;
					$worksheet->setRow($ctr,16);
					$worksheet->write($ctr,0,"Grand Total",$TotalBorder);
					$worksheet->write($ctr,1,number_format($totGross,2),$TotalBorder);
					$worksheet->write($ctr,2,number_format($totTax,2),$TotalBorder);
					$worksheet->write($ctr,3,number_format($totTaxAdj,2),$TotalBorder);
					$worksheet->write($ctr,4,number_format($totEcola,2),$TotalBorder);
					$worksheet->write($ctr,5,number_format($tot13thNT,2),$TotalBorder);
					$worksheet->write($ctr,6,number_format($tot13thT,2),$TotalBorder);			
				}		
		}
	$workbook->close();
		
	
?>