<?
######### E M P  M E N U ##########
$empNo = $_REQUEST["empNo"];
$empDiv = $_POST["empDiv"];
$empDept = $_POST["empDept"];
$empSect = $_POST["empSect"];

$payPd = $_POST["payPd"];
$cmbtable=$_POST["cmbtable"];

if($_GET["hide_option"]=='new_')
{
	$payPd = $_GET["payPd"];
	$reportType = $_GET["reportType"];
	$orderBy = $_GET["orderBy"];
}
else
{
	$payPd = $_POST["payPd"];
	$reportType = $_POST["reportType"];
	$orderBy = $_POST["orderBy"];
}

if ($payPd=="") {
	$openPeriod = $inqTSObj->getOpenPeriod($compCode,$_SESSION['pay_group'],$_SESSION['pay_category']); 
	$groupType = $openPeriod['payGrp'];
	$catType = $openPeriod['payCat'];
	$payPd = $openPeriod['pdSeries'];
}
else
{
	$selComp = 0;
}

$option_menu = $_REQUEST["hide_option"];

$topType = $_GET['topType'];

if ($option_menu=="") { $option_menu="refresh_"; } 
if ($groupType=="" || $groupType==0 || $groupType=="0") { $groupType=3; }
switch ($option_menu) {
	case "new_":
		$msg="";
		$new_chkd = "checked";
		$searchTS2_dis = "";
		$topType_dis = "";
		if ($empNo=="") {
			$empNo_dis = "class=\"inputs\"";
			$empName_dis = "class=\"inputs\" ";
			$empDiv_dis = "class=\"inputs\" onChange=\"getEmpDept(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$payPd_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$searchEmp_dis = "class=\"inputs\"";
			$orderBy_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$topType_dis = "class=\"inputs\"";
		} else {
			$empNo_dis = "class=\"inputs\" disabled='true'";
			$empName_dis = "class=\"inputs\" disabled='true'";
			$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
			$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
			$empSect_dis = "class=\"inputs\" disabled=\"true\"";
			$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
			$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
			$topType_dis = "class=\"inputs\" disabled='true'";
			###################### get employee data ##########
			$dispEmp = $inqTSObj->getUserInfo($compCode , $empNo, ""); 
			$empNo = $dispEmp['empNo'];
			$empName = $dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'];
			$empDiv = $dispEmp['empDiv'];
			$empDept = $dispEmp['empDepCode'];
			$empSect = $dispEmp['empSecCode'];
			$groupType = $dispEmp['empPayGrp'];
			$catType = $dispEmp['empPayCat'];
			#####################################################
		}
		break;
	case "refresh_":
		$searchTS2_dis = "disabled='true'";
		$refresh_chkd = "checked";
		$empNo_dis = "class=\"inputs\" disabled='true'";
		$empName_dis = "class=\"inputs\" disabled='true'";
		$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
		$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
		$empSect_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$cmbtable_dis = "class=\"inputs\" disabled=\"true\"";
		$payPd_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
		$topType_dis = "class=\"inputs\" disabled='true'";
		$msg="";
		break;
				
	default :
	break;
}
$new_ = "<input name='option_menu' id='new_' type='radio' value='new_' $new_chkd $new_dis onClick='option_button_click(this.id);'>Inquire";
$refresh_ = "<input name='option_menu' id='refresh_' type='radio' value='refresh_' $refresh_chkd onClick='option_button_click(this.id);'>Refresh";
######## E N D  E M P   M E N U #######
?>