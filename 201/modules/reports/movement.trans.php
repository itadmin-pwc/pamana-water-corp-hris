<?
######### E M P  M E N U ##########
$empNo = $_REQUEST["empNo"];
$empDiv = $_POST["empDiv"];
$empDept = $_POST["empDept"];
$empSect = $_POST["empSect"];
$groupType =  $_SESSION['pay_group'];
$catType = $_SESSION['pay_category'];
$payPd = $_POST["payPd"];
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
		$searchTS4_dis = "";
		$searchTS5_dis = "";
		$searchTS6_dis = "";
		$searchTS7_dis = "";
		$searchTS8_dis = "";
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
			$cmbtable_dis = "class=\"inputs\"";
			$groupType_dis = "class=\"inputs\" onChange=\"getPayPd('pdType');\" onKeyPress=\"getEmpSearch(event);\"";
			$pafType_dis = "class=\"inputs\" style=\"width:180px;\"";
			$payPd_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$searchEmp_dis = "class=\"inputs\"";
			$orderBy_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$signatory = "class=\"inputs\" disabled='true'";
			$position = "class=\"inputs\" disabled='true'";
			
		} else {
			$printImgFileName="printer.png";
			$printImgFileName2="printer.png";
			$printLoc="onclick=\"printEmpInfo();\"";
			$printLoc2="onclick=\"printEmpConfi();\"";
			$printLoc3="onclick=\"printDeptHierarchy();\"";
			$printLoc4="onclick=\"printHolidayCalendar();\"";
			$empNo_dis = "class=\"inputs\" disabled='true'";
			$cmbReportOption="class=\"inputs\" disabled='true'";
			$cmbReportData="";
			$cmbBranches="class=\"inputs\" disabled='true'";
			$empName_dis = "class=\"inputs\" disabled='true'";
			$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
			$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
			$empSect_dis = "class=\"inputs\" disabled=\"true\"";
//			$cmbtable_dis = "class=\"inputs\" disabled=\"true\"";
			$groupType_dis = "class=\"inputs\" disabled=\"true\"";
			$pafType_dis = "class=\"inputs\" disabled=\"true\" style=\"width:180px;\"";
//			$payPd_dis = "class=\"inputs\" disabled=\"true\"";
			$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
			$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
			$school = "class=\"inputs\" disabled='true'";
			$course = "class=\"inputs\" disabled='true'";
			//$signatory = "class=\"inputs\" disabled='true'";
			//$position = "class=\"inputs\" disabled='true'";
			###################### get employee data ##########
			$dispEmp = $inqTSObj->getEmployee($compCode , $empNo, ""); 
			$empNo = $dispEmp['empNo'];
			$empStat = $dispEmp['empStat'];
			$empName = $dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'];
			$empDiv = $dispEmp['empDiv'];
			$empDept = $dispEmp['empDepCode'];
			$empSect = $dispEmp['empSecCode'];
			$groupType = $dispEmp['empPayGrp'];
			$catType = $dispEmp['empPayCat'];
			$coebranchsignatory = $inqTSObj->getEmpBranchArt($dispEmp['compCode'],$dispEmp['empBrnCode']); 
			$coeName=$coebranchsignatory['coeSignatory'];
			$coeTitle=$coebranchsignatory['coeSignatoryTitle'];
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
		$printLoc3="onclick=\"printDeptHierarchy();\"";
		$printLoc4="onclick=\"printHolidayCalendar();\"";
		$printImgFileName="printer_dis.png";
		$printImgFileName2="printer.png";
		$printLoc="";
		$refresh_chkd = "checked";
		$empNo_dis = "class=\"inputs\" disabled='true'";
		$cmbBranches="class=\"inputs\" disabled='true'";
		$cmbReportOption="class=\"inputs\" disabled='true'";
//		$cmbReportOption="class=\"inputs\" value=\" \"";
		$empName_dis = "class=\"inputs\" disabled='true'";
		$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
		$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
		$empSect_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$cmbtable_dis = "class=\"inputs\" disabled=\"true\"";
		$groupType_dis = "class=\"inputs\" disabled=\"true\"";
		$pafType_dis = "class=\"inputs\" disabled=\"true\" style=\"width:180px;\"";
		$payPd_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
		$school = "class=\"inputs\" disabled='true'";
		$course = "class=\"inputs\" disabled='true'";
		$signatory = "class=\"inputs\" disabled='true'";
		$position = "class=\"inputs\" disabled='true'";
		$msg="";
		break;
				
	default :
	break;
}
$new_ = "<input name='option_menu' id='new_' type='radio' value='new_' $new_chkd $new_dis onClick='option_button_click(this.id);'>Inquire";
$refresh_ = "<input name='option_menu' id='refresh_' type='radio' value='refresh_' $refresh_chkd onClick='option_button_click(this.id);'>Refresh";
######## E N D  E M P   M E N U #######

if($_GET['action']=="filterbranches" and $_GET['hide_option']=="new_"){
		$searchTS_dis = "disabled='true'";
		$searchTS2_dis = "disabled='true'";
		$searchTS3_dis = "disabled='true'";
		$searchTS4_dis = "disabled='true'";
		$searchTS5_dis = "disabled='true'";
		$searchTS6_dis = "disabled='true'";
		$searchTS7_dis = "disabled='true'";
		$searchTS8_dis = "disabled='true'";
		$printLoc3="onclick=\"printDeptHierarchy();\"";
		$printLoc4="onclick=\"printHolidayCalendar();\"";
		$printImgFileName="printer_dis.png";
		$printImgFileName2="printer.png";
		$printLoc="onclick=\"printEmpBranch();\"";
		$printLoc2="onclick=\"printEmpConfiBranch();\"";		
		$refresh_chkd = "checked";
		$empNo_dis = "class=\"inputs\" disabled='true'";
		$cmbBranches="class=\"inputs\" disabled='true'";
//		$cmbReportOption="class=\"inputs\" value=\" \"";
		$empName_dis = "disabled";
		$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
		$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
		$empSect_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$cmbtable_dis = "class=\"inputs\" disabled=\"true\"";
		$groupType_dis = "class=\"inputs\" disabled=\"true\"";
		$pafType_dis = "class=\"inputs\" disabled=\"true\" style=\"width:180px;\"";
		$payPd_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
		$school = "class=\"inputs\" disabled='true'";
		$course = "class=\"inputs\" disabled='true'";
		$signatory = "class=\"inputs\" disabled='true'";
		$position = "class=\"inputs\" disabled='true'";
	}
?>