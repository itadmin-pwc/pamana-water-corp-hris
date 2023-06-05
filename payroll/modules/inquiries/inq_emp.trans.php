<?
######### E M P  M E N U ##########
$empNo = $_REQUEST["empNo"];
$empDiv = $_POST["empDiv"];
$empDept = $_POST["empDept"];
$empSect = $_POST["empSect"];
$orderBy = $_POST["orderBy"];
$groupType = $_POST["groupType"];
$catType = $_POST["catType"];
$option_menu = $_REQUEST["hide_option"];
$hideUpload = $_POST["hideUpload"];
if ($hideUpload=="upload") {
	$error = $_FILES["userfile"]["error"];
	if ($error == UPLOAD_ERR_OK) {
		$uploadDir = "../../../images/empImage";
		$tmp_name = $_FILES["userfile"]["tmp_name"];
		$name = $_FILES["userfile"]["name"];
		$size = $_FILES["userfile"]["size"];
		$data=base64_encode(addslashes(fread(fopen($tmp_name, "r"), filesize($tmp_name)))); //binary data
		$maintEmpObj->update_upload($compCode,$empNo,$data);				
		move_uploaded_file($tmp_name, $uploadDir."/".$compCode."-".$empNo.".jpeg");
		
		
		//header('Content-Type: application/exe');
		//header('Content-Length: '.strlen($this->buffer));
		//header('Content-disposition: inline; filename="file:///D|/payrollWebCam.exe"');
	}
}
if ($hideUpload=="viewCam") {
	$maintEmpObj->showCam($compCode,$empNo);
}

if ($option_menu=="") { $option_menu="refresh_"; } 
if ($groupType=="" || $groupType==0 || $groupType=="0") { $groupType=3; }
switch ($option_menu) {
	case "new_":
		$msg="";
		$new_chkd = "checked";
		if ($empNo=="") {
			$printImgFileName="printer_dis.png";
			$printEmpImg="printer_dis.png";
			$printImgFileName2="printer.png";
			$printLoc="";
			$printLoc5=" disabled ";
			$printLoc2="";
			$printLoc3="onclick=\"printDeptHierarchy();\"";
			$printLoc4="onclick=\"printHolidayCalendar();\"";
			$empNo_dis = "class=\"inputs\"";
			$empName_dis = "class=\"inputs\" ";
			$empDiv_dis = "class=\"inputs\" onChange=\"getEmpDept(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$groupType_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$catType_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$orderBy_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$searchEmp_dis = "class=\"inputs\"";
		} else {
			$printImgFileName="printer.png";
			$printEmpImg=$compCode."-".$empNo.".jpeg";
			$printImgFileName2="printer.png";
			$printLoc="onclick=\"printEmpInfo();\"";
			$printLoc5="";
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
			$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
			$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
			###################### get employee data ##########
			$dispEmp = $maintEmpObj->getUserInfo($compCode , $empNo, ""); 
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
		$printLoc3="onclick=\"printDeptHierarchy();\"";
		$printLoc4="onclick=\"printHolidayCalendar();\"";
		$printImgFileName="printer_dis.png";
		$printEmpImg="printer_dis.png";
		$printImgFileName2="printer.png";
		$printLoc="";
		$printLoc5=" disabled ";
		$refresh_chkd = "checked";
		$empNo_dis = "class=\"inputs\" disabled='true'";
		$empName_dis = "class=\"inputs\" disabled='true'";
		$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
		$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
		$empSect_dis = "class=\"inputs\" disabled=\"true\"";
		$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$groupType_dis = "class=\"inputs\" disabled=\"true\"";
		$catType_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$msg="";
		break;
				
	default :
	break;
}
$new_ = "<input name='option_menu' id='new_' type='radio' value='new_' $new_chkd $new_dis onClick='option_button_click(this.id);'>Inquire";
$refresh_ = "<input name='option_menu' id='refresh_' type='radio' value='refresh_' $refresh_chkd onClick='option_button_click(this.id);'>Refresh";
######## E N D  E M P   M E N U #######
?>