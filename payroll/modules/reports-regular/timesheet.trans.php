<?
######### E M P  M E N U ##########
$empNo = $_REQUEST["empNo"];
$empDiv = $_POST["empDiv"];
$empDept = $_POST["empDept"];
$empSect = $_POST["empSect"];

if($_GET["hide_option"]=='new_')
{
	$payPd = $_GET["payPd"];
	$reportType = $_GET["reportType"];
	$orderBy = $_GET["orderBy"];
}
else
{
	$tbl = $_POST["tblEarnType"];
	$loanType = $_POST["loanType"];
	$cmbBranch = $_POST["empBrnCode"];
	$payPd = $_POST["payPd"];
	$reportType = $_POST["reportType"];
	$orderBy = $_POST["orderBy"];
}

if ($payPd=="") {
	$openPeriod = $inqTSObj->getOpenPeriod($compCode,$_SESSION['pay_group'],$_SESSION['pay_category']); 
	$payPd = $openPeriod['pdSeries'];
}

$option_menu = $_REQUEST["hide_option"];

if ($option_menu=="") { $option_menu="refresh_"; } 
if ($groupType=="" || $groupType==0 || $groupType=="0") 
{
	$groupType=$_SESSION['pay_group']; 
}


switch ($option_menu) {
	case "new_":
		$msg="";
		$new_chkd = "checked";
		$searchTS_dis = "";
		$searchTS2_dis = "";
		$searchTS3_dis = "";
		$searchTS4_dis = "";
		$searchTS5_dis = "";
		$searchTS6_dis = "";
		$searchTS7_dis = "";
		$searchTS8_dis = "";
		$searchTS9_dis = "";
		$searchTS10_dis = "";
		$searchTS11_dis = "";
		$searchTS12_dis = "";
		if ($empNo=="") {
			$printImgFileName="printer_dis.png";
			$printImgFileName2="printer.png";
			$printLoc="";
			$printLoc2="";
			$printLoc3="onclick=\"printDeptHierarchy();\"";
			$printLoc4="onclick=\"printHolidayCalendar();\"";
			$empNo_dis = "class=\"inputs\"";
			$empName_dis = "class=\"inputs\" ";
			$empDiv_dis = "class=\"inputs\" onChange=\"getEmpDept(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$payPd_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$searchEmp_dis = "class=\"inputs\"";
			$orderBy_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$reportType_dis = "class=\"inputs\"";
			$topType_dis = "class=\"inputs\"";
			$cmBranch_dis = "class=\"inputs\"";
			$loanType_dis = "class=\"inputs\"";
		} else {
			$printImgFileName="printer.png";
			$printImgFileName2="printer.png";
			$printLoc="onclick=\"printEmpInfo();\"";
			$printLoc2="onclick=\"printEmpConfi();\"";
			$printLoc3="onclick=\"printDeptHierarchy();\"";
			$printLoc4="onclick=\"printHolidayCalendar();\"";
			$empNo_dis = "class=\"inputs\" disabled='true'";
			$empName_dis = "class=\"inputs\" disabled='true'";
			$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
			$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
			$empSect_dis = "class=\"inputs\" disabled=\"true\"";
			$payPd_dis = "class=\"inputs\" disabled=\"false\"";
			$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
			$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
			$reportType_dis = "class=\"inputs\" disabled=\"true\"";
			$topType_dis = "class=\"inputs\" disabled=\"true\"";
			$cmBranch_dis = "class=\"inputs\" disabled=\"true\"";
			$loanType_dis = "class=\"inputs\" disabled=\"true\"";
			###################### get employee data ##########
			$dispEmp = $inqTSObj->getUserInfo($compCode , $empNo, ""); 
			$empNo = $dispEmp['empNo'];
			$empName = $dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'];
			$empDiv = $dispEmp['empDiv'];
			$empDept = $dispEmp['empDepCode'];
			$empSect = $dispEmp['empSecCode'];
			
			#####################################################
		}
		break;
		
	case "refresh_":
		$searchTS_dis = "disabled='true'";
		$searchTS2_dis = "disabled='true'";
		$searchTS3_dis = "disabled='true'";
		$searchTS4_dis = "disabled='true'";
		$searchTS5_dis = "disabled='true'";
		$searchTS6_dis = "disabled='true'";
		$searchTS7_dis = "disabled='true'";
		$searchTS8_dis = "disabled='true'";
		$searchTS9_dis = "disabled='true'";
		$searchTS10_dis = "disabled='true'";
		$searchTS11_dis = "disabled='true'";
		$searchTS12_dis = "disabled='true'";
		$printLoc3="onclick=\"printDeptHierarchy();\"";
		$printLoc4="onclick=\"printHolidayCalendar();\"";
		$printImgFileName="printer_dis.png";
		$printImgFileName2="printer.png";
		$printLoc="";
		$refresh_chkd = "checked";
		$empNo_dis = "class=\"inputs\" disabled='true'";
		$empName_dis = "class=\"inputs\" disabled='true'";
		$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\" style='width:200px;'";
		$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\" style='width:200px;'";
		$empSect_dis = "class=\"inputs\" disabled=\"true\" style='width:200px;'";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\" ";
		$payPd_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
		$reportType_dis = "class=\"inputs\" disabled=\"true\"";
		$topType_dis = "class=\"inputs\" disabled=\"true\"";
		$cmBranch_dis = "class=\"inputs\" disabled=\"true\"";
		$loanType_dis = "class=\"inputs\" disabled=\"true\"";
		$msg="";
		break;
			
	default :
	break;
}

$new_ = "<input name='option_menu' id='new_' type='radio' value='new_' $new_chkd $new_dis onClick='option_button_click(this.id);'>Inquire";
$refresh_ = "<input name='option_menu' id='refresh_' type='radio' value='refresh_' $refresh_chkd onClick='option_button_click(this.id);'>Refresh";
######## E N D  E M P   M E N U #######
?>