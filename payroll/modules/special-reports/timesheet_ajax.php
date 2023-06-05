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
$empName = $_GET['empName'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$hide_payPd = $_GET['hide_payPd'];

$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];
$orderBy = $_GET['orderBy'];
$groupType = $_SESSION['pay_group'];
$catType = $_SESSION['pay_category'];


$conType = $_GET["conType"];
$monthto = $_GET["monthto"];
$monthfr = $_GET["monthfr"];

/*
$chopMonthto = split("-",$monthto);
$chopMonthfr = split("-",$monthfr);*/

$filter_mfr = date("m",strtotime($monthfr));
$filter_mto = date("m",strtotime($monthto));
$filter_yfr = date("Y",strtotime($monthfr));
$filter_yto = date("Y",strtotime($monthto));


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
		if ($groupType<3) {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
		if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
		if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
		if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
		if ($groupType<3 && $groupType!="") {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
		if ($catType>0) {$catType1 = " AND (empPayCat = '{$catType}')";} else {$catType1 = "";}
		$sqlEmp = "SELECT * FROM tblEmpMast  WHERE (compCode = '{$compCode}') 
				   $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1
				   AND empStat NOT IN('RS','IN','TR') 
				   $orderBy1 ";	   
		$resEmp = $inqTSObj->execQry($sqlEmp);		   
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		if ($thisValue=="verifyEmp") {
		
			if ($numEmp == 0) {
				echo "alert('No Employee Record found...');";
			} elseif ($numEmp == 1) {
				echo "location.href = '$fileName?hide_option=new_&empNo=$empNo&conType=$conType&monthto=$monthto&monthfr=$monthfr';";
			} elseif ($numEmp > 1) {
				echo "location.href = 'main_emp_list.php?fileName=$fileName&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&conType=$conType&monthto=$monthto&monthfr=$monthfr';";
			}
				
		}
		
		
		if ($thisValue=="btnempCert") 
		{ 

			/*Check the To Month filter if nag eeexists sya sa tblMTDGovt, kung wala mag look up kana kay Hist*/
			$chkMtdGovt = $inqTSObj->chkMonMtdGov($filter_mto,$filter_yto,$empNo);
			if($chkMtdGovt>0)
			{
				echo "location.href = 'emp_certification_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&conType=$conType&monthto=$monthto&monthfr=$monthfr';";
			}
			else
			{
				$chkMtdGovtHist = $inqTSObj->chkMonMtdGovHist($monthfr,$monthto,$empNo);
				if($chkMtdGovtHist>0)
				{
					echo "location.href = 'emp_certification_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&conType=$conType&monthto=$monthto&monthfr=$monthfr';";
				}
				else
				{
					echo "alert('No Employee Record found...');";
				}
			}
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