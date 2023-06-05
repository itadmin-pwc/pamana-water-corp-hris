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
								  'Align' => 'left'));
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
	
	


	$empNo         			= $_GET['empNo'];
	$empName       			= $_GET['empName'];
	$empDiv        			= $_GET['empDiv'];
	$empDept       			= $_GET['empDept'];
	$empSect       			= $_GET['empSect'];
	$orderBy				= $_GET['orderBy'];
	$catName 				= $inqTSObj->getEmpCatArt($sessionVars['compCode'], $_SESSION['pay_category']);
	$dtfrom					= $_GET['from'];
	$dtto					= $_GET['to'];
	$branch					= $_GET['branch'];
	$filename = "loans_per_store_per_type.xls";
	
	
	//Column titles
	//Data loading
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
if ($empNo>"") {$empNo1 = " AND (tblEmpMast.empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
	//if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
	if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==2) {$orderBy1 = " ORDER BY empNo, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode, empLastName, empFirstName, empMidName ";}
	if (!$inqTSObj->getPeriod($payPd)) {
		$hist = "hist";
	}
	if ($branch != 0) {
		$branch = " AND empBrnCode = '$branch'";
	} else {
		$branch = "";
	}
	$sqlBranch = "Select brnCode,brnShortDesc from tblBranch where compCode='{$_SESSION['company_code']}'";
	$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($sqlBranch));;
	$lonType=($_GET['lonType'] !="" && $_GET['lonType'] !=0) ? $_GET['lonType']:"";
	$lonTypeFilter = ($lonType !="") ? " AND loans.lonTypeCd='$lonType'":"";
		 $sqlLoans = "Select emp.empNo,custNo,empLastName,empFirstName,empMidName,lonTypeShortDesc as loanType,lonRefNo,lonwidInterst as loanAmt,loans.dateAdded,brnShortDesc as branch,lonCurbal, lonStat = case lonStat when 'C' then 'Completed' when 'O' then 'Open' when 'T' then 'Terminated' else '' end from tblEmpmast emp 
								inner join tblEmpLoans loans on emp.empNo=loans.empNo
								left join tblCustomerNo cust on emp.empNo=cust.empNo
								inner join tblLoanType ltype on loans.lonTypeCd=ltype.lonTypeCd
								inner join tblBranch on empBrnCode=brnCode
						WHERE  empPayCat='{$_SESSION['pay_category']}' 
							AND empPayGrp='{$_SESSION['pay_group']}' and loans.dateAdded between '$dtfrom' and '$dtto'
							$lonTypeFilter $branch $empName1 $empDiv1 $empName1 $empDept1 $empSect1 
						ORDER BY emp.empBrnCode, emp.empLastName, emp.empFirstName, emp.empMidName	";
	$arrLoans = $inqTSObj->getArrRes($inqTSObj->execQry($sqlLoans));
	//Display Branch
	$workbook->send($filename);
	
	
		$worksheet=&$workbook->addWorksheet("LOANS PER TYPR PER STORE REPORT");
	
			//Set Column
			$worksheet->setColumn(0,0,15);
			$worksheet->setColumn(1,1,15);
			$worksheet->setColumn(1,2,30);
			$worksheet->setColumn(1,3,25);
			$worksheet->setColumn(1,4,25);
			$worksheet->setColumn(1,5,15);
			$worksheet->setColumn(1,6,15);
			$worksheet->setColumn(1,7,15);
	
			$worksheet->write(0,0,$inqTSObj->getCompanyName($_SESSION['company_code']),$headerFormat);
			$worksheet->write(1,0,'LOANS PER TYPR PER STORE REPORT',$headerFormat);
			$worksheet->write(2,0,"Setup Date: ".date("m/d/Y",strtotime($_GET['from'])) . '-' . date("m/d/Y",strtotime($_GET['to'])),$headerFormat);
			$worksheet->write(4,0,'Emp No.',$headerFormat);
			$worksheet->write(4,1,'Cust. No.',$headerFormat);
			$worksheet->write(4,2,'Employee',$headerFormat);
			$worksheet->write(4,3,'Loan Type',$headerFormat);
			$worksheet->write(4,4,'Ref. No.',$headerFormat);
			$worksheet->write(4,5,'Loan Amount',$headerFormat);
			$worksheet->write(4,6,'Cur. Bal',$headerFormat);
			$worksheet->write(4,7,'Status',$headerFormat);
			
			$ctr_cell_pos = 5;
			$branch = "";
			$totLoanAmt = 0;
			$totCurBal = 0;
			$GtotLoanAmt = 0;
			$GtotCurBal = 0;

			foreach ($arrLoans as $val)
			{
				if ($branch != $val['branch']) {
					if ($branch != "") {
						$worksheet->write($ctr_cell_pos,4,"Branch Total",$headerFormat);
						$worksheet->write($ctr_cell_pos,5,number_format($totLoanAmt,2),$headerFormat_right);
						$worksheet->write($ctr_cell_pos,6,number_format($totCurBal,2),$headerFormat_right);
						$totLoanAmt = 0;
						$totCurBal = 0;	
						$ctr_cell_pos++;
						$ctr_cell_pos++;				
					}
					$worksheet->write($ctr_cell_pos,0,$val['branch'],$headerFormat);
					$ctr_cell_pos++;
				}
					$totLoanAmt = $totLoanAmt + $val['loanAmt'];
					$totCurBal = $totCurBal + $val['lonCurbal'];
					$GtotLoanAmt = $GtotLoanAmt + $val['loanAmt'];
					$GtotCurBal = $GtotCurBal + $val['lonCurbal'];
				
					$branch = $val['branch'];
					$nameInit = $val['empLastName'] . ", " . $val['empFirstName']." ".$val['empMidName'][0].".";
					$worksheet->writeString($ctr_cell_pos,0,$val['empNo'],$detail_Format);
					$worksheet->write($ctr_cell_pos,1,$val['custNo'],$detail_Format);
					$worksheet->write($ctr_cell_pos,2,$nameInit,$detail_Format);
					$worksheet->write($ctr_cell_pos,3,$val['loanType'],$detail_Format);
					$worksheet->write($ctr_cell_pos,4,$val['lonRefNo'],$detail_Format);
					$worksheet->write($ctr_cell_pos,5,number_format($val['loanAmt'],2),$detail_right_Format);
					$worksheet->write($ctr_cell_pos,6,number_format($val['lonCurbal'],2),$detail_right_Format);
					$worksheet->write($ctr_cell_pos,7,$val['lonStat'],$detail_Format);
					$ctr_cell_pos++;
			}	
			
			
			$worksheet->write($ctr_cell_pos,4,'Branch Total',$headerFormat);
			$worksheet->write($ctr_cell_pos,5,number_format($totLoanAmt,2),$headerFormat_right);
			$worksheet->write($ctr_cell_pos,6,number_format($totCurBal,2),$headerFormat_right);
			$ctr_cell_pos++;$ctr_cell_pos++;
			$worksheet->write($ctr_cell_pos,4,'Grand Total',$headerFormat);
			$worksheet->write($ctr_cell_pos,5,number_format($GtotLoanAmt,2),$headerFormat_right);
			$worksheet->write($ctr_cell_pos,6,number_format($GtotCurBal,2),$headerFormat_right);
		
	//$worksheet->setLandscape();	
	$workbook->close();

?>