<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Common Trans, js, obj, ajax instead of useing timesheet 
*/

######### E M P  M E N U ##########
$empNo = $_REQUEST["empNo"];
$empDiv = $_POST["empDiv"];
$empDept = $_POST["empDept"];
$empSect = $_POST["empSect"];


$option_menu = $_REQUEST["hide_option"];

if ($option_menu=="") { $option_menu="refresh_"; } 
switch ($option_menu) {
	case "new_":
		$msg="";
		$new_chkd = "checked";
		$btnempCert_dis = "";
		$btnempra1_dis = "";
		$btnemper2_dis = "";
		$topType_dis = "";
		if ($empNo=="") {
			$empNo_dis = "class=\"inputs\"";
			$empName_dis = "class=\"inputs\" ";
			$empDiv_dis = "class=\"inputs\" onChange=\"getEmpDept(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$empPos_dis = "class=\"inputs\"";
			$searchEmp_dis = "class=\"inputs\"";
			$txtSearch_dis = "class=\"inputs\"";
			$srchType_dis = "class=\"inputs\"";
		} else {
			
			$empNo_dis = "class=\"inputs\" disabled='true'";
			$empName_dis = "class=\"inputs\" disabled='true'";
			$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
			$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
			$empSect_dis = "class=\"inputs\" disabled=\"true\"";
			$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
			$empPos_dis = "class=\"inputs\" disabled=\"true\"";
			$txtSearch_dis = "class=\"inputs\" disabled=\"true\"";
			$srchType_dis = "class=\"inputs\" disabled=\"true\"";
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
		$btnempCert_dis  = "disabled='true'";
		$btnempra1_dis = "disabled='true'";
		$btnemper2_dis = "disabled='true'";
		$refresh_chkd = "checked";
		$empNo_dis = "class=\"inputs\" disabled='true'";
		$empName_dis = "class=\"inputs\" disabled='true'";
		$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
		$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
		$empSect_dis = "class=\"inputs\" disabled=\"true\"";
		$empPos_dis = "class=\"inputs\" disabled=\"true\"";
		$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
		$txtSearch_dis = "class=\"inputs\" disabled=\"true\"";
		$srchType_dis = "class=\"inputs\" disabled=\"true\"";
		$msg="";
		break;
				
	default :
	break;
}
$new_ = "<input name='option_menu' id='new_' type='radio' value='new_' $new_chkd $new_dis onClick='option_button_click(this.id);'>Inquire";
$refresh_ = "<input name='option_menu' id='refresh_' type='radio' value='refresh_' $refresh_chkd onClick='option_button_click(this.id);'>Refresh";
######## E N D  E M P   M E N U #######
?>