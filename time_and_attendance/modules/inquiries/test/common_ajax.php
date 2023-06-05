<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	10/09/2010
	Function		:	Common Trans, js, obj, ajax instead of using timesheet 
*/

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
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$empBrnCode = $_GET['empBrnCode'];

$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];
$orderBy = $_GET['orderBy'];

$thisValue = $_GET['thisValue'];


$fileName = $_GET['fileName'];
$shiftCode = $_GET['shiftCode'];


switch ($inputId) {
	case "empSearch":	
		
		##################################################
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') $emppayCat
				   $empDiv1 $empDept1 $empSect1 $empBrnCode1 
				   order by empLastName, empFirstName, empMidName ";	   
		$resEmp = $inqTSObj->execQry($sqlEmp);	
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		
		if ($thisValue=="verifyEmp") {
			if ($numEmp == 0) {
				echo "alert('No Employee record found...');";
			} elseif ($numEmp == 1) {
				echo "location.href = '$fileName?hide_option=new_&empNo=".$rowEmp["empNo"]."&shiftCode=$shiftCode';";
			} elseif ($numEmp > 1) {
				echo "location.href = 'main_emp_list.php?fileName=$fileName&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&empBrnCode=$empBrnCode&shiftCode=$shiftCode';";
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
	
	
}

?>