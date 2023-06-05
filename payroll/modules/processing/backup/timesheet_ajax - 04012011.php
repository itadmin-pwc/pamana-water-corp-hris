<?
##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
define('DOWNLOAD_PATH_GOV',  SYS_NAME.'/payroll/modules/processing/governmentals_textfiles');

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
$payPd2 = $chopMonth[1];

$payPdYear = $chopMonth[3];
$payPdNum = $chopMonth[4];

$arrPayPd1 = $inqTSObj->getOpenPer($payPd1,$chopMonth[2]);
$arrPayPd2 = $inqTSObj->getOpenPer($payPd2,$chopMonth[2]);

$periodStat1 = $arrPayPd1["pdStat"];
$periodStat2 = $arrPayPd2["pdStat"];

$thisValue = $_GET['thisValue'];
switch ($inputId) {
	case "empSearch":		
		##################################################
		if ($empNo>"") 
		{
			$empNo1 = " AND (empNo LIKE '{$empNo}%')";
		} 
		else 
		{
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
		
		$sqlEmp = "SELECT * FROM tblEmpMast  WHERE (compCode = '{$compCode}') 
				   $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1
				   AND empStat NOT IN('RS','IN','TR') 
				   $orderBy1 ";	   
		$resEmp = $inqTSObj->execQry($sqlEmp);		   
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		if ($thisValue=="verifyEmp") {
			if ($numEmp == 0) {
				echo "alert('No Employee Record found...');";
			} elseif ($numEmp == 1) {
				echo "location.href = '$fileName?hide_option=new_&empNo=$empNo&payPd=$payPd&topType=$topType&orderBy=$orderBy';";
			} elseif ($numEmp > 1) {
				echo "location.href = 'main_emp_list.php?fileName=$fileName&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&payPd=$payPd&topType=$topType&orderBy=$orderBy';";
			}
		}
		
		if ($thisValue=="searchTS2") { ### SSS Monthly Contributions
		
			if(($periodStat1=='C')&&($periodStat2=='C'))
				$table = "tblMtdGovtHist";
			else
				$table = "tblMtdGovt";
			
			if ($empNo>"") 
				$empNo2 = " AND (tblGov.empNo LIKE '{$empNo}%')";
			
			$con = $empNo2.$empDiv1.$empDept1.$empSect1;
			
			if ($numEmp>0) 
			{
				$arrTS =$inqTSObj->chkCont($payPdYear,$payPdNum,$table,$con,0);
				if ($arrTS["cntEmp"] > 0) {
					echo "location.href = 'monthly_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&payPd=$payPd&table=$table&topType=$topType';";
				} else {
					echo "alert('No Monthly Contributions Record found...');";
				}
			} else { //////open employee list
				echo "alert('No Employee Record found...');";
			}
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
	
	case "mtdGovt":
		$selCompCode = $_GET["selComp"];
		$pdYear = $payPdYear;
		$pdMonth = $payPdNum;
		$remType = $_GET["remType"];
		
		$resmtdGovtHist = $inqTSObj->chkEmptblMtdGovtHist($selCompCode, $pdYear, $pdMonth);
		$cntmtdGovtHist = $inqTSObj->getRecCount($resmtdGovtHist);
		
		if($cntmtdGovtHist>=1)
		{
			switch($remType) {
				case "S":
					$genTxtFle = $inqTSObj->Sss_TxtFile($compCode, $pdYear, $pdMonth);
					$path = 'NR3001DK.txt';
					$fName = 'SSS_'.$compCode."_".$pdMonth."_".$pdYear;
					echo "window.open('"."sss_remittance_pdf.php?&compCode=".$selCompCode."&pdYear=".$pdYear."&pdMonth=".$pdMonth."');";
				break;
				case "SL":
					$genTxtFle = $inqTSObj->SSSLoan_TxtFile($compCode, $pdYear, $pdMonth);
					$path = 'NR3002DK';
					$fName = 'SSSLOAN_TEMP_'.$compCode."_".$pdMonth."_".$pdYear;
					echo "window.open('"."sss_loan_pdf.php?&compCode=".$selCompCode."&pdYear=".$pdYear."&pdMonth=".$pdMonth."');";
				break;
				case "PAG":
					echo "window.open('"."gov_remittance_excel.php?&compCode=".$selCompCode."&pdYear=".$pdYear."&pdMonth=".$pdMonth."');";
				break;
				case "PAGL":
					$genTxtFle = $inqTSObj->PagLoan_TxtFile($compCode, $pdYear, $pdMonth);
					$path = 'HDMFLOAN.DBF';
					$fName = 'PAGLOAN_'.$compCode."_".$pdMonth."_".$pdYear;
				break;
				case "PH":
					echo "window.open('"."phic_remittance_pdf.php?&compCode=".$selCompCode."&pdYear=".$pdYear."&pdMonth=".$pdMonth."');";
				break;

			}
			
			
			
			if($path!="")
			{
				if(file_exists($_SERVER['DOCUMENT_ROOT']. SYS_NAME.'/payroll/modules/processing/governmentals_textfiles/'.$fName))
				{
					rmdir("governmentals_textfiles/".$fName);
					mkdir("governmentals_textfiles/".$fName,0700);
				}
				else	
				{
					mkdir("governmentals_textfiles/".$fName,0700);
				}
				
				if(file_exists($_SERVER['DOCUMENT_ROOT']. SYS_NAME.'/payroll/modules/processing/governmentals_textfiles/'.$fName . '/'.$path))
				{
					unlink($_SERVER['DOCUMENT_ROOT']. SYS_NAME.'/payroll/modules/processing/governmentals_textfiles/'.$fName . '/'.$path);
				}
				
				$inqTSObj->WriteFile($path, $_SERVER['DOCUMENT_ROOT']. SYS_NAME.'/payroll/modules/processing/governmentals_textfiles/'.$fName . '', $genTxtFle);
				echo "window.open('txtreport.php?act=&file=$path&ZipfolderName=".$fName."');";
			}
		}
		else
		{
			echo "alert('No Employee Record found...');";
		}
	break;
}

?>