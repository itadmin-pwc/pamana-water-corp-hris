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
				   ";
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
				echo "document.frmEmpLoan.action = 'inq_emp_loans_emp_list.php?fileName=$fileName&inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect';";
			} else {
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No record/s found...');";
			}
			echo "document.frmEmpLoan.submit();";
		}	
	break;
	case "empDiv":
		$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrDept = $maintEmpLoanObj->makeArr($maintEmpLoanObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','');
		echo $maintEmpLoanObj->DropDownMenu($arrDept,'empDept',$hide_empDept,$empDept_dis);
	break;
	case "empDept":
		$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrSect = $maintEmpLoanObj->makeArr($maintEmpLoanObj->getSectArt($compCode,$empDiv,$empDept),'sectCode','deptDesc','');
		echo $maintEmpLoanObj->DropDownMenu($arrSect,'empSect',$hide_empSect,$empSect_dis);
	break;
	case "getLoanType":
		$loanType_dis = "class=\"inputs\"";
		$arrLoan = $maintEmpLoanObj->makeArr($maintEmpLoanObj->getLoanTypeListArt($compCode,$loanTypeAll),'lonTypeCd','lonTypeDesc','');
		echo $maintEmpLoanObj->DropDownMenu($arrLoan,'loanType',$hide_loanType,$loanType_dis);
	break;
	case "loanSearch":
		$maintEmpLoanObj->compCode      = $compCode;
		$maintEmpLoanObj->empNo         = $empNo;
		$maintEmpLoanObj->empDiv        = $empDiv;
		$maintEmpLoanObj->empDept       = $empDept;
		$maintEmpLoanObj->empSect       = $empSect;
		$maintEmpLoanObj->loanTypeAll   = $loanTypeAll;
		$maintEmpLoanObj->loanType      = $loanType;
		$maintEmpLoanObj->loanStatus    = $loanStatus;
		
 		$resSearch = $maintEmpLoanObj->getEmpLoanInq();
		$array_count = count($resSearch);
		if ($array_count<=0) {
			echo "alert('No record/s found...');";
		} else {
			echo "document.frmEmpLoan.action = 'inq_emp_loans_list.php?empNo=$empNo&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&loanTypeAll=$loanTypeAll&loanType=$loanType&loanStatus=$loanStatus&orderBy=$orderBy';";
			echo "document.frmEmpLoan.submit();";
		}
	break;
	
	case "empLoanInfo":
		echo "window.open('"."employee_loan_info_pdf.php?&empNo=".$_GET["empNo"]."&loanTypeCd=".$_GET["loanCode"]."&loanRefNo=".$_GET["loanRefNo"]."');";
		
	break;
}

?>