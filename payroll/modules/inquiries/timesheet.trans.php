<?
######### E M P  M E N U ##########
$empNo = $_REQUEST["empNo"];
$empDiv = $_POST["empDiv"];
$empDept = $_POST["empDept"];
$empSect = $_POST["empSect"];
$groupType = $_POST["groupType"];
$catType = $_POST["catType"];
$payPd = $_POST["payPd"];
$reportType = $_POST["reportType"];

if ($payPd=="") {
	$openPeriod = $inqTSObj->getOpenPeriodwil(); 
	$groupType = $openPeriod['payGrp'];
	$catType = $openPeriod['payCat'];
	$payPd = $openPeriod['pdSeries'];
}
$option_menu = $_REQUEST["hide_option"];
$orderBy = $_POST["orderBy"];
if ($option_menu=="") { $option_menu="refresh_"; } 
if ($groupType=="" || $groupType==0 || $groupType=="0") { $groupType=3; }
switch ($option_menu) {
	case "new_":
		$msg="";
		$new_chkd = "checked";
		$searchTS_dis = "";
		$searchTS2_dis = "";
		$searchTS3_dis = "";
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
			$groupType_dis = "class=\"inputs\" onChange=\"getPayPd('pdType');\" onKeyPress=\"getEmpSearch(event);\"";
			$catType_dis = "class=\"inputs\" onChange=\"getPayPd('pdType');\" onKeyPress=\"getEmpSearch(event);\"";
			$payPd_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$searchEmp_dis = "class=\"inputs\"";
			$orderBy_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$reportType_dis = "class=\"inputs\"";
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
			$groupType_dis = "class=\"inputs\" disabled=\"true\"";
			$catType_dis = "class=\"inputs\" disabled=\"true\"";
			$payPd_dis = "class=\"inputs\" disabled=\"true\"";
			$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
			$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
			$reportType_dis = "class=\"inputs\" disabled=\"true\"";
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
		$searchTS_dis = "disabled='true'";
		$searchTS2_dis = "disabled='true'";
		$searchTS3_dis = "disabled='true'";
		$printLoc3="onclick=\"printDeptHierarchy();\"";
		$printLoc4="onclick=\"printHolidayCalendar();\"";
		$printImgFileName="printer_dis.png";
		$printImgFileName2="printer.png";
		$printLoc="";
		$refresh_chkd = "checked";
		$empNo_dis = "class=\"inputs\" disabled='true'";
		$empName_dis = "class=\"inputs\" disabled='true'";
		$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
		$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
		$empSect_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$groupType_dis = "class=\"inputs\" disabled=\"true\"";
		$catType_dis = "class=\"inputs\" disabled=\"true\"";
		$payPd_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
		$reportType_dis = "class=\"inputs\" disabled=\"true\"";
		$msg="";
		break;
				
	default :
	break;
}
$new_ = "<input name='option_menu' id='new_' type='radio' value='new_' $new_chkd $new_dis onClick='option_button_click(this.id);'>Inquire";
$refresh_ = "<input name='option_menu' id='refresh_' type='radio' value='refresh_' $refresh_chkd onClick='option_button_click(this.id);'>Refresh";
######## E N D  E M P   M E N U #######
?>