<?
######### L O A N  M E N U ##########
$empNo = $_REQUEST["empNo"];
$empDiv = $_POST["empDiv"];
$empDept = $_POST["empDept"];
$empSect = $_POST["empSect"];
$loanType = $_POST["loanType"];
$loanTypeAll = $_POST["loanTypeAll"];
$loanStart = $_POST["loanStart"];
$loanEnd = $_POST["loanEnd"];
$loanStatus = $_POST["loanStatus"];
$orderBy = $_POST["orderBy"];

$option_menu = $_REQUEST["hide_option"];
$updateFlag = $_POST["updateFlag"];
if ($updateFlag>"") { $loanType = $updateFlag; }
if ($option_menu=="") { $option_menu="refresh_loan"; } 
if ($loanStatus=="" || $loanStatus==0 || $loanStatus=="0") { $loanStatus="A"; }
if ($loanTypeAll=="" || $loanTypeAll==0 || $loanTypeAll=="0") { $loanTypeAll=4; }


switch ($option_menu) {
	case "new_loan":
		$searchLoan_dis = "class=\"inputs\" ";
		$loanStatus_dis = "class=\"inputs\" ";
		$orderBy_dis = "class=\"inputs\" ";
		$loanTypeAll_dis = "class=\"inputs\" onChange=\"getLoanType();\"";
		$printImgFileName="printer_dis.png";
		$printLoc="";
		$msg="";
		$new_chkd = "checked";
		$loanRefNo = "";
		$loanPrinc = "0";
		$loanInt = "0";
		$loanStart = "";
		$loanEnd = "";
		$loanTerms = "0";
		$loanDedEx = "0";
		$loanDedIn = "0";
		$loanPay = "0";
		$loanPayNo = "0";
		$loanBal = "0";
		$loanLastPay ="";
		if ($empNo=="") {
			$emp_lookup = "<img src='../../../images/search.gif' name='img_code' align='absbottom' id='img_code' style='cursor:pointer;' title='Open Employee LookUp' onClick=\"window.open('emp_lookup.php','','width=500,height=500,left=250,top=100')\"/>";
			$delete_dis = "class=\"inputs\" disabled='true'";
			$empNo_dis = "class=\"inputs\"";
			$empName_dis = "class=\"inputs\" ";
			$empDiv_dis = "class=\"inputs\" onChange=\"getEmpDept(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$searchEmp_dis = "class=\"inputs\""; 
			$loanSearch_dis = "class=\"inputs\"";
			$loanType_dis = "class=\"inputs\"";
			
		} else {
			$groupType_dis = "class=\"inputs\" disabled=\"true\"";
			$loanType_dis = "class=\"inputs\"";
			$searchEmp_dis = "class=\"inputs\" disabled=\"true\"";
			$loanSearch_dis = "class=\"inputs\" disabled=\"true\"";
			$delete_dis = "class=\"inputs\" disabled='true'";
			$edit_dis = "class=\"inputs\" onClick=\" getEmpSearchNewEdit('loanType',this.id);\"";
			$empNo_dis = "class=\"inputs\" disabled='true'";
			$empName_dis = "class=\"inputs\" disabled='true'";
			$empDiv_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpDept(this.id);\"";
			$empDept_dis = "class=\"inputs\" disabled=\"true\" onChange=\"getEmpSect(this.id);\"";
			$empSect_dis = "class=\"inputs\" disabled=\"true\"";
			###################### get employee data ##########
			$dispEmp = $maintEmpLoanObj->getEmpInfo($empNo); 
			$empNo = $dispEmp['empNo'];
			$empName = $dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'];
			$empDiv = $dispEmp['empDiv'];
			$empDept = $dispEmp['empDepCode'];
			$empSect = $dispEmp['empSecCode'];
		
			#####################################################
		}
		break;
	case "refresh_loan":
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
		$loanSearch_dis = "class=\"inputs\" disabled=\"true\"";
		$loanType_dis = "class=\"inputs\" disabled=\"true\"";
		$searchLoan_dis = "class=\"inputs\" disabled=\"true\"";
		$loanStatus_dis = "class=\"inputs\" disabled=\"true\"";
		$orderBy_dis = "class=\"inputs\" disabled=\"true\"";
	
		$loanTypeAll_dis = "class=\"inputs\" disabled=\"true\"";
		$loanType = "";
		$loanRefNo = "";
		$loanPrinc = "0";
		$loanInt = "0";
		$loanStart = "";
		$loanEnd = "";
		$loanPeriod = "";
		$loanTerms = "0";
		$loanDedEx = "0";
		$loanDedIn = "0";
		$loanPay = "0";
		$loanPayNo = "0";
		$loanBal = "0";
		$loanLastPay = "";
		$msg="";
		break;
				
	default :
	break;
}
$new_loan = "<input name='option_menu' id='new_loan' type='radio' value='new_loan' $new_chkd $new_dis onClick='option_button_click(this.id);'>Inquire";
//$edit_loan = "<input name='option_menu' id='edit_loan' type='radio' value='edit_loan' $edit_chkd $edit_dis onClick='option_button_click(this.id);'>Edit";
//$delete_loan = "<input name='option_menu' id='delete_loan' type='radio' value='delete_loan' $delete_chkd $delete_dis onClick='valDeleteLoan();'>Delete";
$refresh_loan = "<input name='option_menu' id='refresh_loan' type='radio' value='refresh_loan' $refresh_chkd onClick='option_button_click(this.id);'>Refresh";
######## E N D  L O A N   M E N U #######
?>