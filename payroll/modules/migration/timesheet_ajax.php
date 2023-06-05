<?
##################################################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	$inputId = $_GET['inputId'];
	$empNo = $_GET['empNo'];
	$empDiv = $_GET['empDiv'];
	$empSect = $_GET['empSect'];
	$locType = $_GET['locType'];
	$empBrnCode = $_GET['empBrnCode'];
	
	$hide_empDept = $_GET['hide_empDept'];
	$hide_empSect = $_GET['hide_empSect'];
	$hide_payPd = $_GET['hide_payPd'];
	$optionId = $_GET['optionId'];
	$fileName = $_GET['fileName'];
	$orderBy = $_GET['orderBy'];
	
	$payPd = $_GET['payPd'];
	
	$thisValue = $_GET['thisValue'];
	$reportType = $_GET['reportType'];
	$topType = $_GET['topType'];
	$arrPayPd = $inqTSObj->getSlctdPd($compCode,$payPd);
	$periodStat = $arrPayPd["pdStat"];



switch ($inputId) {
	case "empSearch":	
		
		##################################################
		if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
		//if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
		if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
		if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
		if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		if ($locType=="S")
			$locType1 = " AND (empLocCode = '{$empBrnCode}')";
		if ($locType=="H")
			$locType1 = " AND (empLocCode = '0001')";
		$PaySum = ($periodStat=='O'?"tblPayrollSummary":"tblPayrollSummaryHist");
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   AND empPayGrp = '{$_SESSION['pay_group']}'
				   AND empNo IN 
				   				(Select empNo from $PaySum where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )
				   $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $empBrnCode1 $locType1
				   
				   $orderBy1 ";
		
		$resEmp = $inqTSObj->execQry($sqlEmp);	
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		/*Get List of Employees*/
		if($numEmp > 1){
			$EmpNoList = $inqTSObj->getArrEmpList($resEmp);
		}
		
		if($numEmp==1){
			$rowEmp = $inqTSObj->getSqlAssoc($resEmp);	
			$EmpNoList = $rowEmp["empNo"];
		}
		
		if ($thisValue=="verifyEmp") {
		
			if ($numEmp == 0) {
				echo "alert('No Employee record found...');";
			} elseif ($numEmp == 1) {
				echo "location.href = '$fileName?hide_option=new_&empNo=".$rowEmp["empNo"]."&orderBy=$orderBy&payPd=$payPd';";
			} elseif ($numEmp > 1) {
				echo "location.href = 'main_emp_list.php?fileName=$fileName&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&payPd=$payPd&empBrnCode=$empBrnCode&locType=$locType';";
			}
				
		}
		
		if ($thisValue=="GovLoans") {//Goverment Loans Remittance
			if ($numEmp>0) {
			
				$arrDL = $inqTSObj->GovLoans($compCode,'',$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$payPd,$_GET['loanType'],$_GET['empBrnCode'],$_GET['locType']);
				if (count($arrDL) > 0) {
				$tbl=$_GET['tbl'];
				echo "window.open( 'Govloans_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&loanType=".$_GET['loanType']."&branch=".$_GET['empBrnCode']."&loc=".$_GET['locType']."');";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Goverment Loans Remittance found...');";
				}
				echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee record found...');";
				echo "document.frmTS.submit();";
			}		
		}		break;
	

	
	case "empDiv":
	
		$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrDept = $inqTSObj->makeArr($inqTSObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','');
		echo $inqTSObj->DropDownMenu($arrDept,'empDept',$hide_empDept,$empDept_dis);
	break;
	
	case "empDept":
		$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrSect = $inqTSObj->makeArr($inqTSObj->getSectArt($compCode,$empDiv,$empDept),'sectCode','deptDesc','');
		echo $inqTSObj->DropDownMenu($arrSect,'empSect',$hide_empSect,$empSect_dis);
	break;

	case "ARDload":
		if (count($inqTSObj->ARDload($payPd))== 0 ) {
			echo "alert('No AR found...');";
		} else {
			$curdate = date('mdY');
			$filename = "textfiles/AR-$curdate.txt";
			if ($inqTSObj->CreateARTxtFile($inqTSObj->ARDload($payPd),'AR')) {
				$inqTSObj->UpdateAudit('ARDloadTag',$payPd);
				echo "window.open('txtreport.php?file=$filename');";
			}	
		}	
	break;	
	case "LSDload":
		if (count($inqTSObj->ARDload($payPd,310))== 0 ) {
			echo "alert('No AR found...');";
		} else {
			$curdate = date('mdY');
			$filename = "textfiles/LS-$curdate.txt";
			if ($inqTSObj->CreateARTxtFile($inqTSObj->ARDload($payPd,310),'LS',$payPd)) {
				//$inqTSObj->UpdateAudit('LSDloadTag',$payPd);
				echo "window.open('txtreport.php?file=$filename');";
			}	
		}
	break;
	case "EmpStat":
		if (count($inqTSObj->ResignedEmp($_GET['from'],$_GET['to']))== 0 ) {
			echo "alert('No Resigned Employee found...');";
		} else {
			$curdate = date('mdY');
			$filename = "textfiles/Resigned Employees-$curdate.txt";
			if ($inqTSObj->ResignedEmp($_GET['from'],$_GET['to'])) {
				echo "window.open('txtreport.php?file=$filename');";
			}	
		}
	break;		
}

?>