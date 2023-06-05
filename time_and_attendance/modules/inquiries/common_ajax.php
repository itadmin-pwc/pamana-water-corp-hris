<?
##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("common_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$compCode = $_SESSION['company_code'];
$inputId = $_GET['inputId'];

$empNo = $_GET['empNo'];
$empDiv = $_GET['empDiv'];
$empSect = $_GET['empSect'];
$empDept = $_GET['empDept'];
$empName = $_GET['empName'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$hide_payPd = $_GET['hide_payPd'];
$empBrnCode = $_GET['empBrnCode'];
$payGrp = $_GET['payGrp'];
$vioCode = $_GET['vioCode'];

$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];

$thisValue = $_GET['thisValue'];
switch ($inputId) {
	case "empSearch":		
		##################################################
		if ($empNo>"") {
			$empNo1 = " AND (empNo LIKE '{$empNo}%')";
		} else {
			$empNo1 = "";
			if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
		}
		
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') $empNo1
				   $empDiv1 $empDept1 $empSect1 $empBrnCode1 
				   order by empLastName, empFirstName, empMidName ";	   
		$resEmp = $inqTSObj->execQry($sqlEmp);	
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		
		if ($thisValue=="verifyEmp") {
			if ($numEmp == 0) {
				echo "alert('No Employee record found...');";
			} elseif ($numEmp == 1) {
				echo "location.href = '$fileName?hide_option=new_&empNo=$empNo&shiftCode=$shiftCode';";
			} elseif ($numEmp > 1) {
				echo "location.href = 'main_emp_list.php?fileName=$fileName&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&empBrnCode=$empBrnCode&shiftCode=$shiftCode&payGrp=$payGrp';";
			}
		}
		
		/*Generate Report for Employee Schedule*/
		if($thisValue=="empSchedule")
		{
			if ($numEmp>0) 
				echo "document.frmTS.action = 'employees_schedule_list_ajax.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&empBrnCode=$empBrnCode&shiftCode=$shiftCode';";
			else 
			{
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
			}
			
			echo "document.frmTS.submit();";
		}
		
		/*Generate Report for Employee Timesheet*/
		if($thisValue=="empTimesheet")
		{
			if ($numEmp>0) 
				echo "document.frmTS.action = 'employees_timesheet_list_ajax.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&empBrnCode=$empBrnCode&shiftCode=$shiftCode&payGrp=$payGrp';";
			else 
			{
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
			}
			
			echo "document.frmTS.submit();";
		}
		
		/*Generate Report for Employee Timesheet*/
		if($thisValue=="empViolation")
		{
			if ($numEmp>0) 
				echo "document.frmTS.action = 'employees_violations_list_ajax.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&empBrnCode=$empBrnCode&shiftCode=$shiftCode&payGrp=$payGrp&vioCode=$vioCode';";
			else 
			{
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
			}
			
			echo "document.frmTS.submit();";
		}
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
		$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType,"0"),'pdSeries','pdPayable','');
		echo $inqTSObj->DropDownMenu($arrPayPd,'payPd',$hide_payPd,$payPd_dis);
	break;
}

?>