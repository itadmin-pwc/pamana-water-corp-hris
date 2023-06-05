<?
##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$compCode = $_SESSION['company_code'];
$inputId = $_GET['inputId'];
$empNo = $_GET['empNo'];
$empDiv = $_GET['empDiv'];
$empSect = $_GET['empSect'];
$empDept = $_GET['empDept'];
$locType = $_GET['locType'];
$empBrnCode = $_GET['empBrnCode'];
$empName = $_GET['empName'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$hide_payPd = $_GET['hide_payPd'];
$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];
$orderBy = $_GET['orderBy'];

$payPd = $_GET['payPd'];

$thisValue = $_GET['thisValue'];


$arrPayPd = $inqTSObj->getSlctdPd($compCode,$payPd);


switch ($inputId) {
	case "empSearch":		
		##################################################
		if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($groupType<3) {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
		if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
		if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
		if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
		if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		if ($locType=="S")
			$locType1 = " AND (empLocCode = '{$empBrnCode}')";
		if ($locType=="H")
			$locType1 = " AND (empLocCode = '0001')";
		$empStat = ($_SESSION['pay_category'] !=9) ? " AND empStat NOT IN('RS','IN','TR') ":"";
			
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   AND empPayGrp = '{$_SESSION['pay_group']}'
			       AND empPayCat = '{$_SESSION['pay_category']}'
				   $empStat $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $empBrnCode1 $locType1
				   $orderBy1 ";	   
		
		$resEmp = $inqTSObj->execQry($sqlEmp);	
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		/*Get List of Employees*/
		if($numEmp > 1){
			$EmpNoList = $inqTSObj->getArrEmpList($resEmp);
		}
		
		if($numEmp==1){
			$rowEmp = $inqTSObj->getSqlAssoc($resEmp);	
			$EmpNoList = $rowEmp["empNo"];
		}
		
		if ($thisValue=="verifyEmp") {
		
			if ($numEmp == 0) {
				echo "alert('No Employee record found...');";
			} elseif ($numEmp == 1) {
				echo "location.href = '$fileName?hide_option=new_&empNo=".$rowEmp["empNo"]."&orderBy=$orderBy&payPd=$payPd';";
			} elseif ($numEmp > 1) {
				echo "location.href = 'main_emp_list.php?fileName=$fileName&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&payPd=$payPd&empBrnCode=$empBrnCode&locType=$locType';";
			}
				
		}
		
		
		if ($thisValue=="searchTS") { ### TIMESHEET TIMESHEET TIMESHEET TIMESHEET TIMESHEET TIMESHEET TIMESHEET TIMESHEET
				
			if ($numEmp>0) {
				$pdStat = $arrPayPd["pdStat"];
				if($pdStat=='O')
					$reportType = "tblTimeSheet";
				else
					$reportType = "tblTimeSheetHist";
				
				
				$arrTS = $inqTSObj->getTimeSheet($empNo,$arrPayPd['pdFrmDate'],$arrPayPd['pdToDate'],$reportType,$EmpNoList);
				if (count($arrTS) > 0) 
				{
					echo "document.frmTS.action = 'timesheet_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&reportType=$reportType&empBrnCode=$empBrnCode&locType=$locType';";
					echo "document.frmTS.submit();";
				}else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Timesheet Record found...');";
				}
				
				
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}	
		} 
		
		/*
		if ($thisValue=="searchTS2") { ### EARNINGS EARNINGS EARNINGS EARNINGS EARNINGS EARNINGS EARNINGS EARNINGS EARNINGS
			if ($numEmp>0) {
				$arrEarnings = $inqTSObj->getEarnings($compCode,'',$arrPayPd['pdYear'],$arrPayPd['pdNumber']);
				if (count($arrEarnings) > 0) {
					echo "document.frmTS.action = 'earnings_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd';";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Earnings record found...');";
				}
				echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee record found...');";
				echo "document.frmTS.submit();";
			}
		}
		if ($thisValue=="searchTS3") { ### DEDUCTIONS DEDUCTIONS DEDUCTIONS DEDUCTIONS DEDUCTIONS 
			if ($numEmp>0) {
				$arrDeductions = $inqTSObj->getDuductions($compCode,'',$arrPayPd['pdYear'],$arrPayPd['pdNumber']);
				if (count($arrDeductions) > 0) {
					echo "document.frmTS.action = 'deductions_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd';";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Earnings record found...');";
				}
				echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee record found...');";
				echo "document.frmTS.submit();";
			}
		}*/
	break;
	case "empDiv":
		$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrDept = $inqTSObj->makeArr($inqTSObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','');
		echo $inqTSObj->DropDownMenu($arrDept,'empDept',$hide_empDept,$empDept_dis);
	break;
	case "empDept":
		$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrSect = $inqTSObj->makeArr($inqTSObj->getSectArt($compCode,$empDiv,$empDept),'sectCode','deptDesc','');
		echo $inqTSObj->DropDownMenu($arrSect,'empSect',$hide_empSect,$empSect_dis);
	break;
	case "pdType":
		$payPd_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType),'pdSeries','pdPayable','');
		echo $inqTSObj->DropDownMenu($arrPayPd,'payPd',$hide_payPd,$payPd_dis);
	break;
	case "generateLoans":
		$slctdPd = $inqTSObj->getSlctdPd($compCode,$payPd);
		$modSked = $slctdPd['modSked'];
		if ($modSked==0) $skedNo=2; ### 2nd period
		if ($modSked==1) $skedNo=1; ### 1st period
		
		//echo $slctdPd['pdProcessTag'].$slctdPd['pdLoansTag'].$slctdPd['payGrp'].$slctdPd['payCat'].$slctdPd['pdYear'].$slctdPd['pdNumber'];
		if ($slctdPd['pdProcessTag']=="N" || $slctdPd['pdProcessTag']=="" || $slctdPd['pdProcessTag']==" ") {
			if ($slctdPd['pdLoansTag']=="N" || $slctdPd['pdLoansTag']=="" || $slctdPd['pdLoansTag']==" ") {
				$inqTSObj->processLoans($compCode,$skedNo,$slctdPd['pdPayable'],$slctdPd['pdYear'],$slctdPd['pdNumber'],$slctdPd['payCat'],$slctdPd['payGrp'],$slctdPd['pdYear'],$slctdPd['pdNumber'],0);	
				echo "document.frmTS.submit();";
				echo "document.getElementById('btnProcess').value='Re-process';";
				echo "alert('Generate Loan/Recurring Deductions for Payroll Successfully created!')";
			} else {
				$inqTSObj->processLoans($compCode,$skedNo,$slctdPd['pdPayable'],$slctdPd['pdYear'],$slctdPd['pdNumber'],$slctdPd['payCat'],$slctdPd['payGrp'],$slctdPd['pdYear'],$slctdPd['pdNumber'],1);	
				echo "document.frmTS.submit();";
				echo "alert('Re-process Loan/Recurring Deductions for Payroll Successfully created!')";
			}
		} else {
			echo "alert('Payroll Period Processed already!');";
		}
	break;
}

?>