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
$table=$_GET['table'];
$empNo = $_GET['empNo'];
$empDiv = $_GET['empDiv'];
$empSect = $_GET['empSect'];
$empDept = $_GET['empDept'];
$empName = $_GET['empName'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$hide_payPd = $_GET['hide_payPd'];
$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];
$orderBy = $_GET['orderBy'];
$groupType = $_SESSION['pay_group'];
$catType = $_SESSION['pay_category'];
$payPd = $_GET['payPd'];
$topType = $_GET['topType'];



$chopMonth = split("-",$payPd);
$payPd1 = $chopMonth[0];
$payPd2 = ($chopMonth[1]==25?'24':$chopMonth[1]);

$arrPayPd1 = $inqTSObj->getOpenPer($payPd1,$chopMonth[2]);
$arrPayPd2 = $inqTSObj->getOpenPer($payPd2,$chopMonth[2]);

$periodStat1 = $arrPayPd1["pdStat"];
$periodStat2 = $arrPayPd2["pdStat"];

$thisValue = $_GET['thisValue'];
switch ($inputId) 
{
	case "empSearch":		
		##################################################
		if ($empNo>"") {
			$empNo1 = " AND (empNo LIKE '{$empNo}%')";
		} else {
			$empNo1 = "";
			if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
		}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($groupType<3) {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
		if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
		if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
		if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
		if ($groupType<3 && $groupType!="") {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
		if ($catType>0) {$catType1 = " AND (empPayCat = '{$catType}')";} else {$catType1 = "";}
		
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   AND empPayGrp = '{$_SESSION['pay_group']}'
			       AND empPayCat = '{$_SESSION['pay_category']}'
				   $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 
				   AND empStat NOT IN('RS','IN','TR') 
				   $orderBy1 ";   
		
		$resEmp = $inqTSObj->execQry($sqlEmp);		   
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		if ($thisValue=="verifyEmp") {
			if ($numEmp == 0) {
				echo "alert('No Employee record found...');";
			} elseif ($numEmp == 1) {
				echo "location.href = '$fileName?hide_option=new_&empNo=$empNo&payPd=$payPd&orderBy=$orderBy';";
			} elseif ($numEmp > 1) {
				echo "location.href = 'main_emp_list.php?fileName=$fileName&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&payPd=$payPd&orderBy=$orderBy';";
			}
				
		}
		
		/*Witholding Tax*/
		if ($thisValue=="searchTS") 
		{ 
			
			if(($periodStat1=='C')&&($periodStat2=='C'))
			{
				$table = "tblDeductionsHist";
			
				if ($numEmp>0) 
				{
					$resTS = $inqTSObj->countRec($compCode,$empNo,'',$payPd,WTAX,$table,$empBrnCode);
					if (mysql_num_rows($resTS) > 0) 
						echo "location.href = 'tax_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&table=$table&empBrnCode=$empBrnCode&topType=$topType';";
					else 
						echo "alert('No Tax Record found...');";
				} 
				else 
				{ 
					echo "alert('No Employee Record found...');";
				}	
			}
			else
			{
				echo "alert('1st and 2nd Period within the Month should be both Closed...');";
			}
		} 
		
		
		/*SSS Contribution*/
		if ($thisValue=="searchTS2") 
		{ 
			if(($periodStat1=='H')&&($periodStat2=='H'))
				$table = "tblDeductionsHist";
			else
				$table = "tblDeductions";
			
			if ($numEmp>0) 
			{
				$resTS = $inqTSObj->countRec($compCode,$empNo,'',$payPd,SSS_CONTRIB,$table);
				if (mysql_num_rows($resTS) > 0) 
					echo "location.href = 'sss_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&table=$table';";
				else 
					echo "alert('No SSS Monthly Contributions Record found...');";
			
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}
		/*Philhealth*/
		if ($thisValue=="searchTS3") 
		{ 
			if(($periodStat1=='H')&&($periodStat2=='H'))
				$table = "tblDeductionsHist";
			else
				$table = "tblDeductions";
				
			if ($numEmp>0) 
			{	
				$resTS = $inqTSObj->countRec($compCode,$empNo,'',$payPd,PHILHEALTH_CONTRIB,$table);
				if (mysql_num_rows($resTS) > 0) 
					echo "location.href = 'philhealth_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&table=$table';";
				else
					echo "alert('No Philhealth Monthly Contributions Record found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}
		
		/*Pag Ibig Contribution*/
		if ($thisValue=="searchTS4") 
		{
			if(($periodStat1=='H')&&($periodStat2=='H'))
				$table = "tblDeductionsHist";
			else
				$table = "tblDeductions";
				
			if ($numEmp>0) {
				$resTS = $inqTSObj->countRec($compCode,$empNo,'',$payPd,PAGIBIG_CONTRIB,$table);
				if (mysql_num_rows($resTS) > 0) 
					echo "location.href = 'pagibig_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&table=$table';";
				else
					echo "alert('No Pagibig Monthly Contributions Record found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}
		
		
		/*SSS Monthly Loans*/
		if ($thisValue=="searchTS5") 
		{ 
			if(($periodStat1=='H')&&($periodStat2=='H'))
				$table = "tblDeductionsHist";
			else
				$table = "tblDeductions";
				
			if ($numEmp>0) 
			{
				$resTS = $inqTSObj->countRec($compCode,$empNo,'',$payPd,LOAN_SSS_SALARY,$table);
				$resTS2 = $inqTSObj->countRec($compCode,$empNo,'',$payPd,LOAN_SSS_CALAMITY,$table);
				
				if ((mysql_num_rows($resTS) > 0) || (mysql_num_rows($resTS2) > 0))
					echo "location.href = 'sss_loan_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&table=$table';";
				else 
					echo "alert('No SSS Monthly Loans Record found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}
		
		/*Pag Ibig Loans*/
		if ($thisValue=="searchTS6") 
		{ 
			if(($periodStat1=='H')&&($periodStat2=='H'))
				$table = "tblDeductionsHist";
			else
				$table = "tblDeductions";
				
			if ($numEmp>0) 
			{
				$arrTS = $inqTSObj->countRec($compCode,'','',$payPd,LOAN_PAGIBIG_SALARY,$table);
				$arrTS2 = $inqTSObj->countRec($compCode,'','',$payPd,LOAN_PAGIBIG_MULTI,$table);
				if ($arrTS > 0 || $arrTS2 > 0) 
					echo "location.href = 'pagibig_loan_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType&payPd=$payPd&table=$table';";
				else
					echo "alert('No Pagibig Monthly Loans Record found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}
		
		//Monthly YTD
		if ($thisValue=="monthly_ytd") 
		{
			if ($empDiv>"" && $empDiv>0) {$empDivfilter = " AND (tblPayrollSummaryHist.empDivCode = '{$empDiv}')";} else {$empDivfilter = "";}
			if ($empDept>"" && $empDept>0) {$empDeptfilter = " AND (tblPayrollSummaryHist.empDepCode = '{$empDept}')";} else {$empDeptfilter = "";}
		
			if ($numEmp>0) {
				$resTS = count($inqTSObj->getYTDData($payPd,"$empDivfilter $empDeptfilter"));
				if ($resTS > 0) 
					echo "window.open('monthly_ytd_{$_GET['report_type']}.php?empDiv=$empDiv&empDept=$empDept&payPd=$payPd');";
				else
					echo "alert('No Monthly YTD Record found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}	
		
		//Monthly JE
		if ($thisValue=="monthly_je") 
		{
			if ($numEmp>0) {
				$resTS = count($inqTSObj->getMonthly_JE($payPd,$_GET['report_type']));
				if ($resTS > 0) 
					echo "window.open('monthly_je_excel.php?report_type={$_GET['report_type']}&payPd=$payPd');";
				else
					echo "alert('No Monthly Journal Entries Record found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}			
		
		
		//Yearly YTD
		if ($thisValue=="yearly_ytd") 
		{
		
			if ($numEmp>0) {
				$resTS = count($inqTSObj->getYTDYearly($payPd));
				if ($resTS > 0) 
					echo "window.open('yearly_ytd_{$_GET['report_type']}.php?empDiv=$empDiv&empDept=$empDept&payPd=$payPd');";
				else
					echo "alert('No Yearly YTD Record found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}	
		if ($thisValue=="yearly_ytd_payreg") 
		{
		
			if ($numEmp>0) {
				$resTS = count($inqTSObj->getYTDYearlybyPayreg($payPd));
				if ($resTS > 0) 
					echo "window.open('yearly_ytd_bypayreg_{$_GET['report_type']}.php?empDiv=$empDiv&empDept=$empDept&payPd=$payPd');";
				else
					echo "alert('No YTD Record found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}	
		if ($thisValue=="monthly_ytd_payreg") 
		{
			if ($numEmp>0) {
				$resTS = count($inqTSObj->getYTDMonthlybyPayreg($_GET['payPd2']));
				if ($resTS > 0) 
					echo "window.open('monthly_ytd_bypayreg_{$_GET['report_type']}.php?empDiv=$empDiv&empDept=$empDept&payPd=".$_GET['payPd2']."');";
				else
					echo "alert('No YTD Record found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}
		if ($thisValue=="resigned_emp") 
		{
			if ($numEmp>0) {
				$resTS = count($inqTSObj->MonthlyResignedEmp($_GET['payPd2']));
				if ($resTS > 0) 
					echo "window.open('resigned_emp_pdf.php?empDiv=$empDiv&empDept=$empDept&payPd=".$_GET['payPd2']."');";
				else
					echo "alert('No Resigned Employee found...');";
			} 
			else 
			{ 
				echo "alert('No Employee Record found...');";
			}
		}
		if ($thisValue=="MonthlyGovt") 
		{
			echo "window.open('monthly_govt_remittance.php?payPd=".$_GET['payPd']."&type=".$_GET['type']."');";
		}											
	break;
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
		$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType,"0"),'pdSeries','pdPayable','');
		echo $inqTSObj->DropDownMenu($arrPayPd,'payPd',$hide_payPd,$payPd_dis);
	break;
	case "MonthlyLoansType":
		if ($_GET['type']==1) {
			$arrPayPd = $inqTSObj->makeArr($inqTSObj->getMontlyPayPeriod($compCode,$groupType,$catType,"0"),'pdNumber','perMonth',''); // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
            $inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis);
		} else {
			$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriodPerCutOff(),'pdSeries','pdPayable',"0");
			echo $inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis);
		}
	break;
	case "phictxtFile":
		$inqTSObj->createPhicTxtFile($_GET['orNo'],$_GET['amt'],$_GET['date'],$_GET['payPd']);
	break;
}

?>