<?
######### ALLOWANCE MENU ##########
$empNo = $_REQUEST["empNo"];
$empDiv = $_POST["empDiv"];
$empDept = $_POST["empDept"];
$empSect = $_POST["empSect"];
$allowType = $_POST["allowType"];
$orderBy = $_POST["orderBy"];
$groupType = $_POST["groupType"];
$option_menu = $_REQUEST["hide_option"];
$updateFlag = $_POST["updateFlag"];
if ($updateFlag>"") { $allowType = $updateFlag; }
if ($option_menu=="") { $option_menu="refresh_"; } 
if ($groupType=="" || $groupType==0 || $groupType=="0") { $groupType=3; }

switch ($option_menu) {
	case "new_":
		$searchAllow_dis = "class=\"inputs\" ";
		$orderBy_dis = "class=\"inputs\" ";
		$printImgFileName="printer_dis.png";
		$printLoc="";
		$msg="";
		$new_chkd = "checked";
		if ($empNo=="") {
			$groupType_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$emp_lookup = "<img src='../../../images/search.gif' name='img_code' align='absbottom' id='img_code' style='cursor:pointer;' title='Open Employee LookUp' onClick=\"window.open('emp_lookup.php','','width=500,height=500,left=250,top=100')\"/>";
			$delete_dis = "class=\"inputs\" disabled='true'";
			$empNo_dis = "class=\"inputs\"";
			$empName_dis = "class=\"inputs\" ";
			$empDiv_dis = "class=\"inputs\" onChange=\"getEmpDept(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$searchEmp_dis = "class=\"inputs\""; 
			$allowSearch_dis = "class=\"inputs\"";
			$allowType_dis = "class=\"inputs\"";
		} else {
			$groupType_dis = "class=\"inputs\" disabled=\"true\"";
			$allowType_dis = "class=\"inputs\"";
			$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
			$allowSearch_dis = "class=\"inputs\" disabled=\"true\"";
			$delete_dis = "class=\"inputs\" disabled='true'";
			$edit_dis = "class=\"inputs\" onClick=\" getEmpSearchNewEdit('allowType',this.id);\"";
			$empNo_dis = "class=\"inputs\" disabled='true'";
			$empName_dis = "class=\"inputs\" disabled='true'";
			$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
			$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
			$empSect_dis = "class=\"inputs\" disabled=\"true\"";
			###################### get employee data ##########
			$dispEmp = $maintEmpAllowObj->getUserInfo($compCode , $empNo,""); 
			$empNo = $dispEmp['empNo'];
			$empName = $dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'];
			$empDiv = $dispEmp['empDiv'];
			$empDept = $dispEmp['empDepCode'];
			$empSect = $dispEmp['empSecCode'];
			$groupType = $dispEmp['empPayGrp'];
			#####################################################
		}
		break;
	case "refresh_":
		$printImgFileName="printer_dis.png";
		$printLoc="";
		$refresh_chkd = "checked";
		$delete_dis = "class=\"inputs\"  disabled='true'";
		$empNo_dis = "class=\"inputs\" disabled='true'";
		$empName_dis = "class=\"inputs\" disabled='true'";
		$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
		$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
		$empSect_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$allowSearch_dis = "class=\"inputs\" disabled=\"true\"";
		$allowType_dis = "class=\"inputs\" disabled=\"true\"";
		$searchAllow_dis = "class=\"inputs\" disabled=\"true\"";
		$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
		$groupType_dis = "class=\"inputs\" disabled=\"true\"";
		$allowType = "";
		$msg="";
		break;
				
	default :
	break;
}
$new_ = "<input name='option_menu' id='new_' type='radio' value='new_' $new_chkd $new_dis onClick='option_button_click(this.id);'>Inquire";
$refresh_ = "<input name='option_menu' id='refresh_' type='radio' value='refresh_' $refresh_chkd onClick='option_button_click(this.id);'>Refresh";
######## END ALLOWANCE MENU #######
?>