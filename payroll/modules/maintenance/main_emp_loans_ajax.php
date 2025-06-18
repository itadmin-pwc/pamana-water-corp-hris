<?
##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("main_emp_loans_obj.php");
$maintEmpLoanObj = new maintEmpLoanObj();
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
$loanType = explode("-",str_replace('_','#',$_GET['loanType']));
$oldLoanRef_No = $_GET["oldLoanRef_No"];
$loanRefNo = str_replace('_','#',$_GET['loanRefNo']);
$maintEmpLoanObj->compCode    = $compCode;
$maintEmpLoanObj->empNo       = $empNo;
$maintEmpLoanObj->loanRefNo   = $loanRefNo;
$maintEmpLoanObj->loanPrinc   = $_GET['loanPrinc'];
$maintEmpLoanObj->loanInt   = $_GET['loanInt'];
$maintEmpLoanObj->loanStart   = date('Y-m-d',strtotime($_GET['loanStart']));
$maintEmpLoanObj->loanEnd     = date('Y-m-d',strtotime($_GET['loanEnd']));
$maintEmpLoanObj->loanPeriod  = $_GET['loanPeriod'];
$maintEmpLoanObj->loanTerms   = $_GET['loanTerms'];
$maintEmpLoanObj->loanDedEx   = $_GET['loanDedEx'];
$maintEmpLoanObj->loanDedIn   = $_GET['loanDedIn'];
$maintEmpLoanObj->loanPay     = $_GET['loanPay'];
$maintEmpLoanObj->loanPayNo   = $_GET['loanPayNo'];
$maintEmpLoanObj->loanBal     = $_GET['loanBal'];
$maintEmpLoanObj->loanLastPay = date('Y-m-d',strtotime($_GET['loanLastPay']));
$maintEmpLoanObj->dtGranted   = date('Y-m-d',strtotime($_GET['dtGranted']));

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
				   AND empPayGrp = '{$_SESSION['pay_group']}'
			       AND empPayCat = '{$_SESSION['pay_category']}'
				   $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 
				   AND empStat NOT IN('RS','IN','TR')";
		
		$resEmp = $maintEmpLoanObj->execQry($sqlEmp);		   
		$numEmp = $maintEmpLoanObj->getRecCount($resEmp);
		if ($numEmp==1) {
			$loanCode=1;
			$dispEmp = $maintEmpLoanObj->getSqlAssoc($resEmp);
			$dispEmpNo = $dispEmp['empNo'];
			echo "document.getElementById('empNo').disabled=false;";
			echo "document.getElementById('empNo').value='$dispEmpNo';";
			echo "document.frmEmpLoan.submit();";
		} 
		elseif ($numEmp>1) { 
				echo "document.frmEmpLoan.action = 'main_emp_loans_emp_list.php?fileName=$fileName&inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect';";		
				echo "document.frmEmpLoan.submit();";
		}
		else {
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No record/s found...');";
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
	case "loanType": 
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   AND (empNo = '{$empNo}') 
				   ";
		$resEmp = $maintEmpLoanObj->execQry($sqlEmp);		   
		$numEmp = $maintEmpLoanObj->getRecCount($resEmp);
		if (substr($loanType,1)=="2") {
			$checkloan=$maintEmpLoanObj->checkEmpLoans($loanType[0],$_GET['empNo'],$compCode);
			if (count($checkloan)>0) {
				echo "alert('Cannot Setup Loan, Employee has a Current Balance of  Php " . $checkloan[0]['lonCurbal'] . ". Please check Loan Records'); option_button_click('refresh_loan');";
			}
		}		
		if ($numEmp==1) {
			
			$dispEmp = $maintEmpLoanObj->getSqlAssoc($resEmp);
			$dispEmpNo = $dispEmp['empNo'];
			$trnCodePer = $maintEmpLoanObj->getLoantrnCode($loanType[0]);
			if (!empty($trnCodePer)) {
				$lonPer = $trnCodePer;
			} else {
				$lonPer = 0;
			}
			echo "document.getElementById('loanPeriod').value=$lonPer;";
			echo "document.getElementById('empNo').disabled=false;";
			echo "document.getElementById('empNo').value='$dispEmpNo';";
			echo "document.getElementById('loanType').value='".$loanType[0]."';";
			echo "document.frmEmpLoan.submit();";
		} 	
	break;
	case "updateLoan":
		 $loanTypeSplit = $loanType;
		if (count($loanTypeSplit)<2) { /////new loan		
			$loanInfo = $maintEmpLoanObj->getEmpLoanDataArt($compCode , $empNo, $loanType[0],$loanRefNo); 
			$newLoanRefNo = $loanInfo['lonRefNo'];
			if ($newLoanRefNo>"") { //// existing reference no
				echo "document.getElementById('loanRefNo').value='';";
				echo "document.getElementById('msg').value='Existing Reference No.';";
			} else { //////////////// available reference no
				$maintEmpLoanObj->loanType = $loanType[0];
				$maintEmpLoanObj->addEmpLoanArt();
				$newLoanType = $loanType."-".$loanRefNo;
				$rsloanDesc = $maintEmpLoanObj->getLoanTypeDataArt($compCode , $loanType[0]); 
				$loanDesc = $rsloanDesc['lonTypeDesc'];
				echo "document.getElementById('hide_option').value='edit_loan';";
				echo "document.getElementById('updateFlag').value='$newLoanType';";
				echo "alert('$loanDesc (Ref.No.:$loanRefNo) successfully saved.');";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('loanType').disabled=false;";
				echo "document.getElementById('empNo').value='$empNo';";
				echo "document.getElementById('loanType').value='$newLoanType';";
				echo "document.frmEmpLoan.submit();";
			}
		} else {////////////////////edit loan
			 $loanTypeSplit[1] = $loanRefNo;
			if ($loanTypeSplit[1]<>$loanRefNo) { ////if changing the reference no
				$loanInfo = $maintEmpLoanObj->getEmpLoanDataArt($compCode, $empNo, $loanTypeSplit[0],$loanRefNo); 
				$newLoanRefNo = $loanInfo['lonRefNo'];
				if ($newLoanRefNo>"") { ////existing
					echo "document.getElementById('loanRefNo').value='';";
					echo "document.getElementById('msg').value='Existing Reference No.';";
					$flag=1;
				} else {
					echo "document.getElementById('msg').value='';";
				}
			} else { ////////////////////////////////if not changing the reference no
				echo "document.getElementById('msg').value='';";
			}
			if ($flag==0) {
				$newLoanType = $loanTypeSplit[0]."-".$loanRefNo;
				$maintEmpLoanObj->loanType = $loanTypeSplit[0];
				$maintEmpLoanObj->loanOldRefNo = $oldLoanRef_No;
				$maintEmpLoanObj->editEmpLoanArt();
				$rsloanDesc = $maintEmpLoanObj->getLoanTypeDataArt($compCode , $loanTypeSplit[0]); 
				$loanDesc = $rsloanDesc['lonTypeDesc'];
				echo "document.getElementById('updateFlag').value='$newLoanType';";
				echo "alert('$loanDesc (Ref.No.:$loanRefNo) successfully saved.');";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('loanType').disabled=false;";
				echo "document.getElementById('empNo').value='$empNo';";
				echo "document.getElementById('loanType').value='$newLoanType';";
				echo "document.getElementById('hide_option').value='edit_loan';";
				echo "document.frmEmpLoan.submit();";
			}
		}
		
	break;
	case "deleteLoan":
		$loanTypeSplit = split("-",$loanType);
		$rsloanDesc = $maintEmpLoanObj->getLoanTypeDataArt($compCode , $loanTypeSplit[0]); 
		$loanDesc = $rsloanDesc['lonTypeDesc'];
		$maintEmpLoanObj->loanType = $loanTypeSplit[0];
		$maintEmpLoanObj->loanOldRefNo = $loanTypeSplit[1];
		$maintEmpLoanObj->deleteEmpLoanArt();
		echo "document.getElementById('updateFlag').value='1';";
		echo "document.getElementById('msg').value='$loanDesc (Ref.No.:$loanRefNo) successfully deleted.';";
		echo "document.getElementById('empNo').disabled=false;";
		echo "document.getElementById('empNo').value='$empNo';";
		echo "document.getElementById('hide_option').value='edit_loan';";
		echo "document.frmEmpLoan.submit();";
	break;
	case "cancelDeleteLoan":
		echo "document.getElementById('empNo').disabled=false;";
		echo "document.getElementById('empNo').value='$empNo';";
		echo "document.getElementById('loanType').value='$loanType';";
		echo "document.frmEmpLoan.submit();";
	break;
	case "valRefNo":
		$loanTypeSplit = split("-",$loanType);
		$array_count = count($loanTypeSplit); 
		$art = $loanTypeSplit[1];
		if ($array_count<2) { /////new loan		
			$loanInfo = $maintEmpLoanObj->getEmpLoanDataArt($compCode , $empNo, $loanType,$loanRefNo); 
			$newLoanRefNo = $loanInfo['lonRefNo'];
			if ($newLoanRefNo>"") { ////existing
				echo "document.getElementById('loanRefNo').value='';";
				echo "document.getElementById('msg').value='Existing Reference No.';";
			} else {
				echo "document.getElementById('msg').value='A';";
			}
		} else {////////////////////edit loan
			if ($loanTypeSplit[1]<>$loanRefNo) { ////if changing the reference no
				$loanInfo = $maintEmpLoanObj->getEmpLoanDataArt($compCode , $empNo, $loanTypeSplit[0],$loanRefNo); 
				$newLoanRefNo = $loanInfo['lonRefNo'];
				if ($newLoanRefNo>"") { ////existing
					echo "document.getElementById('loanRefNo').value='';";
					echo "document.getElementById('msg').value='Existing Reference No.';";
				} else {
					echo "document.getElementById('msg').value='B';";
				}
			} else {
				echo "document.getElementById('msg').value='C';";
			}

		}
	break;
	case "PreTerminate":
		$lonSeries = $_GET['lonSeries'];
		if ($maintEmpLoanObj->PreTerminate($lonSeries)) {
			echo "alert('Loan Terminated.');";
		} else {
			echo "alert('Loan Terminating Error!');";
		}
	break;
}

?>