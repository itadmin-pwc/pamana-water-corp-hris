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

$compCode = $_SESSION["company_code"];
$empBrnCode = $_GET['empBrnCode'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empPos = $_GET['empPos'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$txtSearch = $_GET["txtSearch"];
$srchType = $_GET["srchType"];
$monthfr =  $_GET["monthfr"];
$monthto =  $_GET["monthto"];
$payCat = $_GET['payCat'];
$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];

$empRank = $_GET['empRank'];
$empStatus = $_GET['empStatus'];



switch ($inputId) {
	case "empSearch":		
		##################################################
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		if ($empRank>"" && $empRank>0) {$empRank1 = " AND (empRank = '{$empRank}')";} else {$empRank1 = "";}
		if (($empStatus!="" ) && ($empStatus!="0")) {$empStatus1 = " AND (employmentTag = '{$empStatus}')";} else {$empStatus1 = "";}
		if (($monthfr!="" ) && ($monthto!="")) {$empDateHired = " AND (dateHired between '".date("m/d/Y", strtotime($monthfr))."' and '".date("m/d/Y", strtotime($monthto))."')";} else {$empDateHired = "";}
		
		if ($thisValue=="salary") 
			$emppayCat = ($_GET['payCat'] !=0)? " AND empPayCat='".$_GET['payCat']."'":"";	
		else
			$emppayCat = "";
			
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE empStat='RG' and (compCode = '{$compCode}') $emppayCat
				   $empDiv1 $empDept1 $empSect1 $empBrnCode1 $empRank1 $empStatus1 $empDateHired
				   order by empLastName, empFirstName, empMidName ";	   
		
		$resEmp = $inqTSObj->execQry($sqlEmp);	
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		if ($thisValue=="btnpayrolltype") 
		{
			if ($numEmp == 0) 
				echo "alert('No Employee record found...');";
			elseif ($numEmp >= 1) 
			{
				echo "location.href = 'payroll_type_list_ajax.php?&fileName=$fileName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&empBrnCode=$empBrnCode';";
				//echo "window.open('payroll_type_excel.php');";
			}
		}
		
		if ($thisValue=="salary") 
		{
			if ($numEmp == 0) 
				echo "alert('No Employee record found...');";
			elseif ($numEmp >= 1) 
			{
				echo "location.href = 'salary_list_ajax.php?&fileName=$fileName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&empBrnCode=$empBrnCode&payCat=$payCat';";
				
			}
		}
		
		if(($thisValue=="btnManCompDept") || ($thisValue=="btnManCompDeptExcel"))
		{
			if ($numEmp == 0) 
				echo "alert('No Employee record found...');";
			elseif (($numEmp >= 1) && ($thisValue=="btnManCompDept"))
				echo "location.href = 'manpowercomp_dept_list_ajax.php?&fileName=$fileName&empDiv=$empDiv&empDept=$empDept&empStatus=$empStatus&empRank=$empRank&empBrnCode=$empBrnCode&monthfr=$monthfr&monthto=$monthto';";
			elseif (($numEmp >= 1) && ($thisValue=="btnManCompDeptExcel"))
				echo "location.href = 'manpowercomp_dept_excel.php?&fileName=$fileName&empDiv=$empDiv&empDept=$empDept&empStatus=$empStatus&empRank=$empRank&empBrnCode=$empBrnCode&monthfr=$monthfr&monthto=$monthto';";
			
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