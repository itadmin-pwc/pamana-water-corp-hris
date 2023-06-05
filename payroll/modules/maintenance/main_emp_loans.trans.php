<?
######### L O A N  M E N U ##########
$empNo = $_REQUEST["empNo"];
$empDiv = $_POST["empDiv"];
$empDept = $_POST["empDept"];
$empSect = $_POST["empSect"];
$loanType = $_POST["loanType"];
$loanRefNo = $_POST["loanRefNo"];
$loanPrinc = $_POST["loanPrinc"];
$loanInt = $_POST["loanInt"];
$loanStart = $_POST["loanStart"];
$loanEnd = $_POST["loanEnd"];
$loanPeriod = $_POST["loanPeriod"];
$loanTerms = $_POST["loanTerms"];
$loanDedEx = $_POST["loanDedEx"];
$loanPay = $_POST["loanPay"];
$loanPayNo = $_POST["loanPayNo"];
$loanBal = $_POST["loanBal"];
$loanLastPay = $_POST["loanLastPay"];
$option_menu = $_REQUEST["hide_option"];

$updateFlag = $_POST["updateFlag"];
if ($updateFlag>"") { $loanType = $updateFlag; }
if ($option_menu=="") { $option_menu="refresh_loan"; } 
switch ($option_menu) {
	case "new_loan":
		$printImgFileName="printer_dis.png";
		$viewDetailed = "allowance_list_2.png";;
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
			$delete_dis = "disabled='true'";
			$empNo_dis = "class=\"inputs\"";
			$empName_dis = "class=\"inputs\" ";
			$empDiv_dis = "class=\"inputs\" onChange=\"getEmpDept(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$searchEmp_dis = "class=\"inputs\""; 
			$loanSearch_dis = "class=\"inputs\"";
			$loanType_dis = "class=\"inputs\" readonly onChange=\"getEmpLoanSearch(this.id); valRefNo();\"";
			$updateLoan_dis = "readonly";
		} else {
			$loanType_dis = "class=\"inputs\" onChange=\"getEmpLoanSearch(this.id); valRefNo();\"";
			$searchEmp_dis = "class=\"inputs\" readonly";
			$loanSearch_dis = "class=\"inputs\" readonly";
			$delete_dis = "disabled='true'";
			$edit_dis = "class=\"inputs\" onClick=\" getEmpSearchNewEdit('loanType',this.id);\"";
			$empNo_dis = "class=\"inputs\" disabled='true'";
			$empName_dis = "class=\"inputs\" disabled='true'";
			$empDiv_dis = "class=\"inputs\" readonly onChange=\"getEmpDept(this.id);\"";
			$empDept_dis = "class=\"inputs\" readonly onChange=\"getEmpSect(this.id);\"";
			$empSect_dis = "class=\"inputs\" readonly";
			if ($loanType!="") {
				$updateLoan_dis = "";
			}
			else {
				$updateLoan_dis = "readonly";
			}	
			###################### get employee data ##########
			$sqlEmp = "SELECT * FROM tblEmpMast 
					   WHERE (compCode = '{$compCode}') 
					   AND empNo='$empNo'
					   ";			
			$dispEmp = $maintEmpLoanObj->getSqlAssoc($maintEmpLoanObj->execQry($sqlEmp)); 
			$empNo = $dispEmp['empNo'];
			$empName = $dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'];
			$empDiv = $dispEmp['empDiv'];
			$empDept = $dispEmp['empDepCode'];
			$empSect = $dispEmp['empSecCode'];
			#####################################################
		}
		break;
	case "edit_loan":
		$edit_chkd = "checked";
		if ($empNo=="") {
			$printImgFileName="printer_dis.png";
			$viewDetailed = "allowance_list_2.png";
			$printLoc="";
			$msg="";
			$emp_lookup = "<img src='../../../images/search.gif' name='img_code' align='absbottom' id='img_code' style='cursor:pointer;' title='Open Employee LookUp' onClick=\"window.open('emp_lookup.php','','width=500,height=500,left=250,top=100')\"/>";
			$delete_dis = "disabled='true'";
			$empNo_dis = "class=\"inputs\"";
			$empName_dis = "class=\"inputs\"";
			$empDiv_dis = "class=\"inputs\" onChange=\"getEmpDept(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event);\"";
			$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event);\"";
			$searchEmp_dis = "class=\"inputs\"";
			$loanSearch_dis = "class=\"inputs\"";
			$loanType_dis = "class=\"inputs\" readonly onChange=\"getEmpLoanSearch(this.id); valRefNo();\"";
			$updateLoan_dis = "readonly";
		} else {
			$delete_dis = "disabled='true'";
			$new_dis = "onClick=\"getEmpSearchNewEdit('loanType',this.id);\"";
			$empNo_dis = "class=\"inputs\" disabled='true'";
			$empName_dis = "class=\"inputs\" disabled='true'";
			$empDiv_dis = "class=\"inputs\" readonly onChange=\"getEmpDept(this.id);\"";
			$empDept_dis = "class=\"inputs\" readonly onChange=\"getEmpSect(this.id);\"";
			$empSect_dis = "class=\"inputs\" readonly";
			$searchEmp_dis = "class=\"inputs\" readonly";
			$loanSearch_dis = "class=\"inputs\" readonly";
			$resEmpLoan = $maintEmpLoanObj->getEmpLoanListArt($compCode,$empNo,$loanCode);
			$array_count = count($resEmpLoan);
			if ($array_count>0) {
				
				if (trim($updateFlag=="") || trim($updateFlag<=0)) { $msg = "$array_count Loans found...$updateFlag"; }
				$loanType_dis = "class=\"inputs\" onChange=\"getEmpLoanSearch(this.id); valRefNo();\"";
				$updateLoan_dis = "readonly";
				//$loanRefNo_dis = "readonly";
				if ($loanType=="" || $loanType<=0) {
					$printImgFileName="printer_dis.png";
					$viewDetailed = "allowance_list_2.png";
					$printLoc="";
					$updateLoan_dis = "readonly";
					$delete_dis = "disabled='true'";
					$loanInfo = "";
					$loanRefNo = "";
					$loanPrinc = "0";
					$loanInt = "0";
					$loanStart = "";
					$loanEnd = "";
					$loanPeriod = "0";
					$loanTerms = "0";
					$loanDedEx = "0";
					$loanDedIn = "0";
					$loanPay = "0";
					$loanPayNo = "0";
					$loanBal = "0";
					$loanLastPay ="";
				} else {
					$printImgFileName="printer.png";
					$viewDetailed = "allowance_list.png";
					$printLoc="onclick=\"printEmpLoan();\"";
					$updateLoan_dis = "";
					$delete_dis = "";
					$loanTypeSplit = split("-",$loanType);
					$strLoanCode = $loanTypeSplit[0];
					$loanRefNo_dis = "readonly";
					$strloanType = str_replace("$strLoanCode-","",$loanType);
					$loanInfo = $maintEmpLoanObj->getEmpLoanDataArt($compCode , $empNo, $strLoanCode,$strloanType); 
					$loanSeries ="viewDetails('loan_detailed_list.php?lonSeries={$loanInfo['lonSeries']}')"; 
					$Pre_Terminate_Loan ="onclick=\"PreTerminate('{$loanInfo['lonSeries']}')\""; 
					$loanRefNo = $loanInfo['lonRefNo'];
					$loanPrinc = $loanInfo['lonAmt'];
					$loanInt = $loanInfo['lonWidInterst'];
					$loanStart = $maintEmpLoanObj->valDateArt($loanInfo['lonStart']);
					$loanEnd = $maintEmpLoanObj->valDateArt($loanInfo['lonEnd']);
					$loanPeriod = $loanInfo['lonSked'];
					$loanTerms = $loanInfo['lonNoPaymnts'];
					$loanDedEx = $loanInfo['lonDedAmt1'];
					$loanDedIn = $loanInfo['lonDedAmt2'];
					$loanStat = $loanInfo['lonStat'];
					$loanPay = $loanInfo['lonPayments'];
					$loanPayNo = $loanInfo['lonPaymentNo'];
					$loanBal = $loanInfo['lonCurbal'];
					$dtGranted = ($loanInfo['lonGranted'] != "") ? date('m/d/Y',strtotime($loanInfo['lonGranted'])):"";
					$loanLastPay =$maintEmpLoanObj->valDateArt($loanInfo['lonLastPay']);
					
				}
			} else {
				$printImgFileName="printer_dis.png";
				$viewDetailed = "allowance_list_2.png";
				$printLoc="";
				$loanType_dis = "class=\"inputs\" readonly onChange=\"getEmpLoanSearch(this.id); valRefNo();\"";
				$updateLoan_dis = "readonly";
				$delete_dis = "disabled='true'";
				if (trim($updateFlag=="") || trim($updateFlag<=0)) { $msg = "No Employee Loans found..."; }
			}
			###################### get empDept/empSect ##########
			$sqlEmp = "SELECT * FROM tblEmpMast 
					   WHERE (compCode = '{$compCode}') 
					   AND empNo='$empNo'
					   ";			
			$dispEmp = $maintEmpLoanObj->getSqlAssoc($maintEmpLoanObj->execQry($sqlEmp)); 
			$empNo = $dispEmp['empNo'];
			$empName = $dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'];
			$empDiv = $dispEmp['empDiv'];
			$empDept = $dispEmp['empDepCode'];
			$empSect = $dispEmp['empSecCode'];
			#####################################################
		}
		break;
	case "delete_loan":
		$delete_chkd = "checked";		
		break;
	case "refresh_loan":
		$printImgFileName="printer_dis.png";
		$viewDetailed = "allowance_list_2.png";
		$printLoc="";
		$refresh_chkd = "checked";
		$delete_dis = "disabled='true'";
		$empNo_dis = "class=\"inputs\" disabled='true'";
		$empName_dis = "class=\"inputs\" disabled='true'";
		$empDiv_dis = "class=\"inputs\" readonly onChange=\"getEmpDept(this.id);\"";
		$empDept_dis = "class=\"inputs\" readonly onChange=\"getEmpSect(this.id);\"";
		$empSect_dis = "class=\"inputs\" readonly";
		$searchEmp_dis = "class=\"inputs\" readonly";
		$loanSearch_dis = "class=\"inputs\" readonly";
		$loanType_dis = "class=\"inputs\" readonly onChange=\"getEmpLoanSearch(this.id); valRefNo();\"";
		$updateLoan_dis = "readonly";
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
		$loanPay = "0.00";
		$loanPayNo = "0";
		$loanBal = "0.00";
		$loanLastPay = "";
		$msg="";
		break;
				
	default :
	break;
}
$new_loan = "<input name='option_menu' id='new_loan' type='radio' value='new_loan' $new_chkd $new_dis onClick='option_button_click(this.id);'>New";
$edit_loan = "<input name='option_menu' id='edit_loan' type='radio' value='edit_loan' $edit_chkd $edit_dis onClick='option_button_click(this.id);'>Edit";
$delete_loan = "<input name='option_menu' id='delete_loan' type='radio' value='delete_loan' $delete_chkd $delete_dis onClick='valDeleteLoan();'>Delete";
$refresh_loan = "<input name='option_menu' id='refresh_loan' type='radio' value='refresh_loan' $refresh_chkd onClick='option_button_click(this.id);'>Refresh";
######## E N D  L O A N   M E N U #######
?>