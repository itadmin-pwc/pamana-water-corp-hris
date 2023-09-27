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
	$prName = $_GET['prName'];
	
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
		if ($thisValue!="TotSal") {
			if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		}
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
		
		/*Earnings Register*/
		if ($thisValue=="searchTS2") 
		{ 
			if ($numEmp>0) 
			{
				$tbl = ($periodStat=='O'?"tblEarnings":"tblEarningsHist");
				$arrEarnings = $inqTSObj->getEarnings($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$tbl,$EmpNoList);
				
				if (count($arrEarnings) > 0) 
				{
					echo "document.frmTS.action = 'earnings_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&payPd=$payPd&repType=$tbl&reportType=$reportType&empBrnCode=$empBrnCode&locType=$locType';";
				} 
				else 
				{
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Earnings Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
		
		/*Deduction Register*/
		if ($thisValue=="searchTS3") 
		{ 
			if ($numEmp>0) 
			{
				$tbl = ($periodStat=='O'?"tblDeductions":"tblDeductionsHist");
				$arrDeductions = $inqTSObj->getDuductions($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$tbl,$EmpNoList);
				if (count($arrDeductions) > 0) 
				{
					echo "document.frmTS.action = 'deductions_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&repType=$tbl&empBrnCode=$empBrnCode&locType=$locType';";
				} 
				else 
				{
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Deductions Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
		
		/*Overtime and Night Differential Report*/
		if ($thisValue=="searchTS4") 
		{ 
			if ($numEmp>0) 
			{
				$tbl = ($periodStat=='O'?"tblEarnings":"tblEarningsHist");
				$arrTS = $inqTSObj->getBasicTotal($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],EARNINGS_RECODEOT,$tbl,1,$EmpNoList);
				$arrTS2 = $inqTSObj->getBasicTotal($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],EARNINGS_RECODEND,$tbl,1,$EmpNoList);
				
				
				if (mysql_num_rows($arrTS) > 0 || mysql_num_rows($arrTS2) > 0) 
				{
					echo "document.frmTS.action = 'ot_nd_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&repType=$tbl&empBrnCode=$empBrnCode&locType=$locType';";
				} 
				else 
				{
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Overtime / Night Differential Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
		
		/*Undertime and Tardiness*/
		if ($thisValue=="searchTS5") 
		{
			if ($numEmp>0) 
			{
				$tbl = ($periodStat=='O'?"tblEarnings":"tblEarningsHist");
				$arrTS = $inqTSObj->getUTND($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],EARNINGS_UT,$tbl,$EmpNoList);
				$arrTS2 = $inqTSObj->getUTND($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],EARNINGS_TARD,$tbl,$EmpNoList);
				
				if(($arrTS["totAmt"]!="") || ($arrTS2["totAmt"]!="")) 
				{
					echo "document.frmTS.action = 'ut_tardi_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&repType=$tbl&empBrnCode=$empBrnCode&locType=$locType';";
				} 
				else 
				{
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Undertime/Tardiness Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
		
		/*Deduction Report by Deduction Type*/
		if ($thisValue=="searchTS7") 
		{ 
			if ($numEmp>0) 
			{
				$tbl = ($periodStat=='O'?"tblDeductions":"tblDeductionsHist");
				if($reportType!="0")
					$trnCode=$reportType;
				else
					$trnCode = "";
				
				$arrTS = $inqTSObj->getBasicTotalDed($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$trnCode,$tbl,$EmpNoList);
				
				if (mysql_num_rows($arrTS) > 0) 
				{
					echo "document.frmTS.action = 'deductions_type_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&repType=$tbl&reportType=$reportType&topType=$topType&empBrnCode=$empBrnCode&locType=$locType';";
				} 
				else 
				{
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Deductions Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
		
		/*Allowance Report*/
		if ($thisValue=="searchTS6") 
		{
			if ($numEmp>0) 
			{
				$tbl = ($periodStat=='O'?"tblEarnings":"tblEarningsHist");
				if($reportType!="0")
					$trnCode=$reportType;
				else
					$trnCode = "";
				
				$arrTS = $inqTSObj->getAllowAmt($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$trnCode,$tbl,'N');
				if (mysql_num_rows($arrTS)) 
				{
					echo "document.frmTS.action = 'allowance_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&repType=$tbl&reportType=$reportType&topType=$topType';";
				} 
				else 
				{
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Allowance Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
		
		/*Allowance Report (NON - TAXABLE whose sprtSP='Y'*/
		if ($thisValue=="searchTS9") 
		{
			if ($numEmp>0) 
			{
				$tbl = ($periodStat=='O'?"tblEarnings":"tblEarningsHist");
				if($reportType!="0")
					$trnCode=$reportType;
				else
					$trnCode = "";
				
				$arrTS = $inqTSObj->getAllowAmt($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$trnCode,$tbl,'Y');
				if (mysql_num_rows($arrTS)) 
				{
					echo "document.frmTS.action = 'allowance_list_sp.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&repType=$tbl&reportType=$reportType&topType=$topType';";
				} 
				else 
				{
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Allowance Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
			
		/*Denomination Listing*/
		if ($thisValue=="searchTS8") 
		{ 
			if ($numEmp>0) 
			{
				$reportType =  ($periodStat=='O'?"tblPayrollSummary":"tblPayrollSummaryHist");
				$arrTS = $inqTSObj->getDenom($compCode,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$reportType);
				
				if (count($arrTS) > 0) 
				{
					echo "document.frmTS.action = 'denomination_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&payPd=$payPd&reportType=$reportType&empBrnCode=$empBrnCode&locType=$locType';";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Denomination Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
		
		
		/*Payroll Register*/
		if ($thisValue=="searchTS10") 
		{ 
			if ($numEmp>0) 
			{
	
				$reportType =  ($periodStat=='O'?"0":"1");
				$arrPReg = $inqTSObj->chkEmpPaySumm($empDiv,$empDept,$empSect,$empNo,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$reportType,1,$locType,$empBrnCode);
				
				if ($arrPReg["totEmp"] > 0) 
				{
					echo "document.frmTS.action = 'payregister_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&repType=$tbl&reportType=$reportType&topType=$topType&empBrnCode=$empBrnCode&locType=$locType&prName=$prName';";
				} 
				else 
				{
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Payroll Register Record found...');";
				}
				echo "document.frmTS.submit();";
			} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}
		}
		
		if ($thisValue=="Payslip") {//Pay Slip Printing 
			if ($numEmp>0) {
			
				$arrTS = $inqTSObj->PaySlip($compCode,$_GET['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$payPd,$_GET['empBrnCode'],$_GET['locType']);
				echo count($arrTS)."\n\n\n";

				if (count($arrTS) > 0) {
				$tbl=$_GET['tbl'];
				$act = $_GET['act'];
				echo "window.open( '$act.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&tbl=$tbl&branch=".$_GET['empBrnCode']."&loc=".$_GET['locType']."');";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Pay Slip record found...');";
				}
				echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee record found...');";
				echo "document.frmTS.submit();";
			}		
		}
		
		if ($thisValue=="LastPay") {//Last Pay Printing 
			if ($numEmp>0) {
			
				$arrTS = $inqTSObj->LastPay($compCode,$_GET['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$payPd,$_GET['empBrnCode'],$_GET['locType']);
				if (count($arrTS) > 0) {
				$tbl=$_GET['tbl'];
				echo "window.open( 'lastpay_list_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&payPd=$payPd&branch=".$_GET['empBrnCode']."&loc=".$_GET['locType']."');";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Last Pay record found...');";
				}
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee record found...');";
				echo "document.frmTS.submit();";
			}		
		}
		if ($thisValue=="LastPayExcel") {//Last Pay Printing 
			if ($numEmp>0) {
			
				$arrTS = $inqTSObj->LastPay($compCode,$_GET['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$payPd,$_GET['empBrnCode'],$_GET['locType']);
				if (count($arrTS) > 0) {
				$tbl=$_GET['tbl'];
				echo "window.open( 'lastpay_list_excel.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&payPd=$payPd&branch=".$_GET['empBrnCode']."&loc=".$_GET['locType']."');";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Last Pay record found...');";
				}
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee record found...');";
				echo "document.frmTS.submit();";
			}		
		}		
		if ($thisValue=="rfp") {//Last Pay Printing 
			if ($numEmp>0) {
			
				$arrTS = $inqTSObj->RFP($compCode,$_GET['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber']);
				if (count($arrTS) > 0) {
				$tbl=$_GET['tbl'];
				echo "window.open( 'last_pay_rfp_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&payPd=$payPd&branch=".$_GET['empBrnCode']."&loc=".$_GET['locType']."');";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Last Pay record found...');";
				}
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee record found...');";
				echo "document.frmTS.submit();";
			}		
		}		
		if ($thisValue=="TotSal") {//Total Salary by Bank
			if ($numEmp>0) {
				$salbrnCode=$_GET['salbrnCode'];
				$arrTS = $inqTSObj->TotSal($compCode,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$payPd,$salbrnCode);
				if (count($arrTS) > 0) 
				{
					$tbl=$_GET['tbl'];
					if(($_SESSION["company_code"]=='1') || ($_SESSION["company_code"]=='2'))
						echo "window.open( 'salary_bank_pdf.php?payPd=$payPd&brnCode=$salbrnCode');";
					else
						echo "window.open( 'salary_bank_clarksubic_pdf.php?payPd=$payPd&brnCode=$salbrnCode');";
				} 
				else 
				{
					echo "alert('No Salary record found...');";
				}
			} else { //////open employee list
				echo "alert('No Employee record found...');";
				echo "document.frmTS.submit();";
			}		
		}					
		if ($thisValue=="DedLoans") {//Deducted Loans
			if ($numEmp>0) {
				$lonType=($_GET['lonType'] !="" && $_GET['lonType'] !=0) ? $_GET['lonType']:"";
				$arrDL = $inqTSObj->DedLoans($compCode,'',$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$payPd,$_GET['empBrnCode'],$_GET['locType'],$lonType);
				if (count($arrDL) > 0) {
				$tbl=$_GET['tbl'];
				echo "window.open( 'loans_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&branch=".$_GET['empBrnCode']."&loc=".$_GET['locType']."&lonType=$lonType');";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Loans Deducted found...');";
				}
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee record found...');";
			//	echo "document.frmTS.submit();";
			}		
		}	
		
		if ($thisValue=="LoansReport") {//Loans Report
			if ($numEmp>0) {
				$lonType=($_GET['lonType'] !="" && $_GET['lonType'] !=0) ? $_GET['lonType']:"";
				$arrDL = $inqTSObj->DedLoans($compCode,'',$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$payPd,$_GET['empBrnCode'],$_GET['locType'],$lonType);
				if (count($arrDL) > 0) {
				$tbl=$_GET['tbl'];
				echo "window.open('loans_report_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&branch=".$_GET['empBrnCode']."&loc=".$_GET['locType']."&lonType=$lonType');";
				} else {
					echo "document.getElementById('empNo').disabled=false;";
					echo "document.getElementById('empNo').value='';";
					echo "alert('No Loans found...');";
				}
			} else { //////open employee list
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee record found...');";
			//	echo "document.frmTS.submit();";
			}		
		}			

		//PayRegister by Department
		if ($thisValue=="searchTS11") 
		{ 
			
			/*if ($numEmp>0) 
			{*/
				
				$reportType =  ($periodStat=='O'?"0":"1");
				$arrPReg = $inqTSObj->chkEmpPaySummDept($_GET, 1);
				if ($arrPReg["totEmp"] > 0) 
				{
					echo "document.frmTS.action = 'payreg_by_dept_list.php?inputId=$optionId&fromDate=$fromDate&toDate=$toDate&empDiv=$empDiv&empDept=$empDept&empBrnCode=$empBrnCode&locType=$locType';";
				} 
				else 
				{
					echo "alert('No Payroll Register By Department Record found...');";
				}
				echo "document.frmTS.submit();";
			/*} 
			else 
			{ 
				echo "document.getElementById('updateFlag').value='1';";
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No Employee Record found...');";
				echo "document.frmTS.submit();";
			}*/
		}
		if ($thisValue=="searchTS12") 
		{ 
			
			/*if ($numEmp>0) 
			{*/
				
				$reportType =  ($periodStat=='O'?"0":"1");
				$arrPReg = $inqTSObj->chkEmpPaySummDept($_GET, 1);
				if ($arrPReg["totEmp"] > 0) 
				{
					echo "document.frmTS.action = 'payreg_by_dept_excel.php?inputId=$optionId&fromDate=$fromDate&toDate=$toDate&empDiv=$empDiv&empDept=$empDept&empBrnCode=$empBrnCode&locType=$locType';";
					echo "document.frmTS.target = '_blank';";
					echo "document.frmTS.submit();";
					
//					echo "location.href= 'payreg_by_dept_excel.php?inputId=$optionId&fromDate=$fromDate&toDate=$toDate&empDiv=$empDiv&empDept=$empDept&empBrnCode=$empBrnCode&locType=$locType';";
				} 
				else 
				{
					echo "alert('No Payroll Register By Department Record found...');";
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
	
	case "pdType":
		$payPd_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType),'pdSeries','pdPayable','');
		echo $inqTSObj->DropDownMenu($arrPayPd,'payPd',$hide_payPd,$payPd_dis);
	break;
	
	case "DailyLoan";
		$div =" where compCode='{$_SESSION['company_code']}'";
		if ($empDiv != 0) {
			$div .= " and empDiv = '{$_GET['empDiv']}'";
		} 
		if ($empDept != 0) { 
			$div .= " and empDepCode = '{$_GET['empDept']}'";
		}
		if ($empSect != 0) { 
			$div .= " and empSecCode = '{$_GET['empSect']}'";
		}
		
		if (count($inqTSObj->getDailyLoans($div,$_GET['from'],$_GET['to'])) ==0) {
			echo "alert('No Employee record found...');";
		} else {
			$from =$_GET['from'];
			$to = $_GET['to'];
			echo "location.href='loans_daily_list.php?empDiv=$empDiv&empDept=$empDept&empSect=$empSect&from=$from&to=$to'";
		}
	break;	
	case "GL":
		$locType = $_GET['locType'];
		if (count($inqTSObj->GLEntries($arrPayPd,$empBrnCode)) == 0 ) {
			echo "alert('No GL Entries found...');";
		} 
		else 
		{
			//if(($_SESSION["company_code"]=='1') || ($_SESSION["company_code"]=='2'))
				echo "window.open('gl_booking_entries2_pdf.php?payPd=$payPd&branch=$empBrnCode&loc=$locType');";
			//else
			//	echo "window.open('gl_booking_entries2_clark_subic_pdf.php?payPd=$payPd&branch=$empBrnCode&loc=$locType');";
		}	
	break;	
	
	case "GLExcel":
	   $locType = $_GET['locType'];
		if (count($inqTSObj->GLEntries($arrPayPd,$empBrnCode)) == 0 ) {
			echo "alert('No GL Entries found...');";
		} 
		else 
		{
			echo "window.open('gl_booking_entries_excel.php?payPd=$payPd&branch=$empBrnCode&loc=$locType');";
		}	
	break;
}

?>