<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Common Trans, js, obj, ajax instead of useing timesheet 
*/

##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("common_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');


$inputId = $_GET['inputId'];
$thisValue = $_GET["thisValue"];

$compCode = $_SESSION['company_code'];
$empBrnCode = $_GET['empBrnCode'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$empNo = $_GET['empNo'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$txtSearch = $_GET["txtSearch"];
$srchType = $_GET["srchType"];
$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];
$hide_payPd = $_GET['hide_payPd'];
	
$fileName = $_GET['fileName'];
$payPd = $_GET['payPd'];
$reportType = $_GET['reportType'];
$topType = $_GET['topType'];
$arrPayPd = $inqTSObj->getSlctdPd($compCode,$payPd);
$periodStat = $arrPayPd["pdStat"];



switch ($inputId) {
	case "empSearch":	
		
		##################################################
		if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		$PaySum = ($periodStat=='O'?"tblPayrollSummary":"tblPayrollSummaryHist");
		
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   AND empPayGrp = '{$_SESSION['pay_group']}'
				   AND empNo IN 
				   				(Select empNo from $PaySum where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								".$empBrnCode1.")
				   $empNo1 $empName1 $empDiv1 $empDept1 $empSect1
				   order by empLastName, empFirstName, empMidName";
				   
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
				echo "location.href = 'main_emp_list.php?fileName=$fileName&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&payPd=$payPd&empBrnCode=$empBrnCode';";
			}
				
		}
		
		/*Deduction Report by Deduction Type*/
		if ($thisValue=="searchTS6") 
		{ 
			if ($numEmp>0) 
			{
				$tbl = ($periodStat=='O'?"tblDeductions":"tblDeductionsHist");
				if($reportType!="0")
					$trnCode=$reportType;
				else
					$trnCode = "";
				
				$arrTS = $inqTSObj->getBasicTotalDed($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$trnCode,$tbl,$EmpNoList);
				
				if (mysql_num_rows($arrTS) > 0) 
				{
					echo "document.frmTS.action = 'deductions_type_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&payPd=$payPd&repType=$tbl&reportType=$reportType&topType=$topType&empBrnCode=$empBrnCode';";
				} 
				else 
				{
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Deductions Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
		
}

?>