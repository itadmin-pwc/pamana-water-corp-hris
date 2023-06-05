<?
##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_loans_obj.php");
$maintEmpLoanObj = new inqEmpLoanObj();
$sessionVars = $maintEmpLoanObj->getSeesionVars();
$maintEmpLoanObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$inputId = $_GET['inputId'];
$empNo = $_GET['empNo'];
$empDiv = $_GET['empDiv'];
$empSect = $_GET['empSect'];
$empDept = $_GET['empDept'];
$empName = $_GET['empName'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];
$loanTypeAll = $_GET['loanTypeAll'];
$hide_loanType = $_GET['hide_loanType'];
$orderBy = $_GET['orderBy'];

$loanType = $_GET['loanType'];
$loanRefNo = $_GET['loanRefNo'];
switch ($inputId) {
	case "empSearch":
		##################################################
		if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
		if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 
				   AND empStat NOT IN('RS','IN','TR')";
		$resEmp = $maintEmpLoanObj->execQry($sqlEmp);		   
		$numEmp = $maintEmpLoanObj->getRecCount($resEmp);
		if ($numEmp>0 && $numEmp<2) {
			$dispEmp = $maintEmpLoanObj->getSqlAssoc($resEmp);
			$dispEmpNo = $dispEmp['empNo'];
			echo "document.getElementById('empNo').disabled=false;";
			echo "document.getElementById('empNo').value='$dispEmpNo';";
			//echo "document.frmEmpLoan.action = 'main_emp_loans_sss.php';";
			echo "document.frmEmpLoan.submit();";
		} else { //////open employee list
			echo "document.getElementById('updateFlag').value='1';";
			if ($numEmp>0) {
				echo "document.frmEmpLoan.action = 'inq_emp_loans_emp_list.php?fileName=$fileName&inputId=$optionId&empNo=$empNo&empName=$empName';";
			} else {
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No record/s found...');";
			}
			echo "document.frmEmpLoan.submit();";
		}	
	break;
	case "searchGovDed":
		$from = $_GET['from'];
		$to = $_GET['to'];
		if ($empNo !="") {
			$empNo = " and empNo='$empNo'";
		}
			$where = " where (convert(varchar(2),pdMonth) +'/28'+'/'+convert(varchar(4),pdYear)) between convert(datetime,'$from') and convert(datetime,'$to') and  compCode='{$_SESSION['company_code']}' $empNo AND empNo IN (Select empNo from tblEmpMast where empPayGrp='".$_SESSION['pay_group']."' AND empPayCat='".$_SESSION['pay_category']."')";

		
 		$resSearch = $maintEmpLoanObj->getGovDed('Count',$where);
		if ($resSearch<=0) {
			echo "alert('No record/s found...');";
		} else {
			echo "window.open('inq_emp_govded_list_pdf.php?empNo=".$_GET['empNo']."&from=$from&to=$to&orderBy=$orderBy');";
			echo "document.frmEmpLoan.submit();";
		}
	break;
}
?>