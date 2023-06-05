<?php
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("payregister.obj.php");
include("../../../includes/pdf/fpdf.php");

$payRegisterObj = new payRegisterObj($_SESSION,$_GET);
$sessionVars = $payRegisterObj->getSeesionVars();

class PDF extends FPDF
{
	function BasicTable()
	{
		$this->Cell(30,6,'','LTR');
		$this->SetFont('Arial','B'); 
		$this->Cell(14,6,'COMPANY:','LT');
		$this->SetFont('Arial',''); 
		
		$this->Ln();
		$this->Cell(50,30,'','R','','C');
		
	}
	
	function PayRegHeader($compName,$reportType)
	{
		$lblPosted = "".($reportType==1?"POSTED":"PRE - POSTED")." PAYROLL REGISTER #";
		$lblCurr = "AS OF ".date('l, F d, Y');
		
		$this->SetFont('Arial','B','10'); 
		$this->Cell(335,4,strtoupper($compName),'','1','C');
		
		$this->SetFont('Arial','','8');
		$this->Cell(335,4,$lblPosted,'','1','C');
		$this->Cell(335,4,$lblCurr,'','','C');
		$this->Ln();
		$this->Ln();
	}
	
	function nxttoHeader()
	{
		$this->Ln(5);
		$this->SetFont('Arial','B','8');
		$this->Cell(47,6,'  NAME','LTR','','C');
		$this->Cell(24,6,'  TAX STAT','LTR','','C');
		$this->Cell(24,6,'  YTD - INC','LTR','','C');
		$this->Cell(24,6,'  SALARY','LTR','','C');
		$this->Cell(24,6,'  LWOP','LTR','','C');
		$this->Cell(24,6,'  OVERTIME','LTR','','C');
		$this->Cell(24,6,'  ALLOWANCE','LTR','','C');
		$this->Cell(24,6,'  TOTAL GROSS','LTR','','C');
		$this->Cell(24,6,'  WITH TAX','LTR','','C');
		$this->Cell(24,6,'  SSS','LTR','','C');
		$this->Cell(24,6,'  LOANS','LTR','','C');
		$this->Cell(24,6,'  TOTAL DED','LTR','','C');
		$this->Cell(24,6,'  NET ','LTR','','C');
		$this->Ln();
		$this->SetFont('Arial','B','8');
		$this->Cell(47,1,'  ID # RANK','LR','','C');
		$this->Cell(24,1,'  ','LR','','C');
		$this->Cell(24,1,'  YTD - TAX','LR','','C');
		$this->Cell(24,1,'  ','LR','','C');
		$this->Cell(24,1,'  TARDINESS','LR','','C');
		$this->Cell(24,1,'  NIGHT PREM.','LR','','C');
		$this->Cell(24,1,'  OTH. INC.','LR','','C');
		$this->Cell(24,1,'  TOTAL INCOME','LR','','C');
		$this->Cell(24,1,'  ','LR','','C');
		$this->Cell(24,1,'  HDMF','LR','','C');
		$this->Cell(24,1,'  OTHER DED.','LR','','C');
		$this->Cell(24,1,'  ','LR','','C');
		$this->Cell(24,1,'  SALARY','LR','','C');
		$this->Ln();
		$this->SetFont('Arial','B','8');
		$this->Cell(47,6,'  ','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		$this->Cell(24,6,'  UT','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		$this->Cell(24,6,'  PHIC','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		$this->Cell(24,6,'  ','LRB','','C');
		
		
	}
	
	function printBank($bankDesc) {
		$this->Ln();
		$this->SetFont('Arial','B','9');
		$this->Cell(335,4,'BANK : '.strtoupper($bankDesc),'','1','L');
		
	}
	
	function printDept($deptDesc)
	{
		$this->Ln();
		$this->SetFont('Arial','B','9');
		$this->Cell(335,4,'DEPARTMENT         '.strtoupper($deptDesc),'','1','L');	
		
	}
}

$pdf=new PDF('L', 'mm', 'LEGAL');



$compName =  $payRegisterObj->getCompName($_SESSION['company_code']);
$userInfo = $payRegisterObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['user_id']);

$payPdSlctd = $_GET['payPdSlctd'];
$pdNumber = $_GET['pdNumber'];
$cmbBank = $_GET['cmbBank'];
$empNo = $_GET['empNo'];
$txtEmpName = $_GET['txtEmpName'];
$nameType = $_GET['nameType'];
$cmbDiv = $_GET['cmbDiv'];
$cmbDept = $_GET['cmbDept'];
$cmbSect = $_GET['cmbSect'];

if($_GET["reportType"]==1)
{
	$tblPaySum = "tblPayrollSummaryHist";
	$tblYtdData = "tblYtdDataHist";
}
else
{
	$tblPaySum = "tblPayrollSummary";
	$tblYtdData = "tblYtdData";
}


$qryPayRegDiv 	= "SELECT DISTINCT(empDivCode) as divCode
			FROM ".$tblPaySum." as ps 
			LEFT JOIN tblEmpMast as emp ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
			LEFT JOIN tblPayBank as bank ON bankCd=ps.empBnkCd and bank.compCode='{$_SESSION['company_code']}'
			LEFT JOIN tblDepartment as dept ON divCode=ps.empDepCode and dept.compCode='{$_SESSION['company_code']}' 
			and deptCode='0' and sectCode='0'
			LEFT JOIN tblTeu as teu ON teuCode=emp.empTeu
			LEFT JOIN ".$tblYtdData." as ytd ON ytd.empNo=ps.empNo and ytd.compCode='{$_SESSION['company_code']}' and ytd.pdYear='{$payPdSlctd['pdYear']}'
			WHERE ps.compCode = '{$_SESSION['company_code']}'
			AND ps.payGrp = '{$_SESSION['pay_group']}'
			AND ps.payCat = '{$_SESSION['pay_category']}'
			AND ps.pdYear = '{$payPdSlctd}'
			AND ps.pdNumber = '{$pdNumber}' "; 
			
if(trim($_GET['cmbBank']) != "0"){
	$qryPayRegDiv .= "AND ps.empBnkCd = '{$_GET['cmbBank']}' ";
}
					
if(trim($_GET['empNo']) != ""){
	$qryPayRegDiv .= "AND ps.empNo = '{$_GET['empNo']}' ";
}

if(trim($_GET['txtEmpName']) != ""){
	if($nameType == 1){
		$qryPayRegDiv .= "AND emp.empLastName LIKE '{$_GET['txtEmpName']}%' ";
	}
	if($nameType == 2){
		$qryPayRegDiv .= "AND emp.empFirstName LIKE '{$_GET['txtEmpName']}%' ";
	}
	if($nameType == 3){
		$qryPayRegDiv .= "AND emp.empMidName LIKE '{$_GET['txtEmpName']}%' ";
	}
}

if($_GET['cmbDiv'] != 0){
	$qryPayRegDiv .= "AND ps.empDivCode = '{$_GET['cmbDiv']}' ";
}

if($_GET['cmbDept'] != 0){
	$qryPayRegDiv .= "AND ps.empDepCode = '{$_GET['cmbDept']}' ";
}
if($_GET['cmbSect'] != 0){
	$qryPayRegDiv .= "AND ps.empSecCode = '{$_GET['cmbSect']}' ";
}


$resPayRegDiv = $payRegisterObj->execQry($qryPayRegDiv);
while($rowPayRegDiv = $payRegisterObj->getSqlAssoc($resPayRegDiv)){

	if(!empty($rowPayRegDiv['divCode'])){
		$divList = $divList.",".$rowPayRegDiv['divCode'];
	}
}

if($divList!="")
{
	$qryGetPaySum 	= "SELECT ps.empNo,ps.netSalary,emp.empLastName,emp.empMidName,emp.empFirstName,emp.empTeu,teuAmt,YtdGross,YtdTax,empBankCd,
							bankDesc,emp.compCode,empDivCode,emp.empDepCode,deptDesc,emp.empSecCode,ps.taxWitheld
							FROM ".$tblPaySum." as ps 
							LEFT JOIN tblEmpMast as emp ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
							LEFT JOIN tblPayBank as bank ON bankCd=ps.empBnkCd and bank.compCode='{$_SESSION['company_code']}'
							LEFT JOIN tblDepartment as dept ON divCode=ps.empDepCode and dept.compCode='{$_SESSION['company_code']}' 
							and deptCode='0' and sectCode='0'
							LEFT JOIN tblTeu as teu ON teuCode=emp.empTeu
							LEFT JOIN ".$tblYtdData." as ytd ON ytd.empNo=ps.empNo and ytd.compCode='{$_SESSION['company_code']}' and ytd.pdYear='{$payPdSlctd}'
							WHERE ps.compCode = '{$_SESSION['company_code']}'
							AND ps.payGrp = '{$_SESSION['pay_group']}'
							AND ps.payCat = '{$_SESSION['pay_category']}'
							AND ps.pdYear = '{$payPdSlctd}'
							AND ps.pdNumber = '{$pdNumber}' 
							AND empDivCode IN  (".substr($divList,1).")"; 
				
	if(trim($cmbBank) != "0"){
		$qryGetPaySum .= " AND ps.empBnkCd = '{$cmbBank}' ";
	}
						
	if(trim($empNo) != ""){
		$qryGetPaySum .= " AND ps.empNo = '{$empNo}' ";
	}
	
	if(trim($txtEmpName) != ""){
		if($nameType == 1){
			$qryGetPaySum .= " AND emp.empLastName LIKE '{$txtEmpName}%' ";
		}
		if($nameType == 2){
			$qryGetPaySum .= " AND emp.empFirstName LIKE '{$txtEmpName}%' ";
		}
		if($nameType == 3){
			$qryGetPaySum .= " AND emp.empMidName LIKE '{$txtEmpName}%' ";
		}
	}
	
	if($cmbDiv != 0){
		$qryGetPaySum .= " AND ps.empDivCode = '{$cmbDiv}' ";
	}
	
	if($cmbDept != 0){
		$qryGetPaySum .= " AND ps.empDepCode = '{$cmbDept}' ";
	}
	if($cmbSect != 0){
		$qryGetPaySum .= " AND ps.empSecCode = '{$cmbSect}' ";
	}
	
	$qryGetPaySum .=  " ORDER BY bankDesc,deptDesc,emp.empLastName";
	$rspayReg = $payRegisterObj->execQry($qryGetPaySum);
	$payPdSlctd = $payRegisterObj->getPayPeriod($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdPayable = '{$_GET['payPd']}'");
	
	$getempEarningsSalary = $payRegisterObj->getDatatblEarnings('0100',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempEarningsLWOP = $payRegisterObj->getDatatblEarnings('0113',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempEarningsUT = $payRegisterObj->getDatatblEarnings('0112',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempEarningsTARD = $payRegisterObj->getDatatblEarnings('0111',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempEarningsOT = $payRegisterObj->getDatatblEarningsOTND('0200',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempEarningsND = $payRegisterObj->getDatatblEarningsOTND('0300',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempEarningsAllow = $payRegisterObj->getDatatblEarningsAllow('',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempEarningsOth = $payRegisterObj->getDataOthtblEarnings('',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempDeductionsSSS = $payRegisterObj->getDataOthtblDeductions('5200',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempDeductionsHDMF = $payRegisterObj->getDataOthtblDeductions('5400',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempDeductionsPHIC = $payRegisterObj->getDataOthtblDeductions('5300',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempListLoans = $payRegisterObj->getDataLoanstblDeductions('',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempOthAdjDed = $payRegisterObj->getDataOthAdjtblDeductions('',$payPdSlctd["pdYear"],$_GET['pdNumber'],$_GET["reportType"]);
	$getempGrossTaxable = $payRegisterObj->compGrossTaxable($_GET["reportType"],$_GET['pdNumber'],$payPdSlctd["pdYear"]);
	$getempGrossNonTax = $payRegisterObj->compGrossNonTaxable($_GET["reportType"],$_GET['pdNumber'],$payPdSlctd["pdYear"]);
	$getcntemp = $payRegisterObj->getempDeptCnt($divList,$_GET["reportType"]);

	$pdf->AddPage();
	$pdf->PayRegHeader($compName["compName"],$_GET["reportType"]);
	$pdf->nxttoHeader();
	$pdf->Ln(8);
	
	$bankcd = "";
	$deptcd = "";
	$cntEmp = 0;
	$diff_cnt_emp = 0;
	$Empcnt = 0;
	$totempSal = 0;
	$totempAttend = 0;
	$totempLWOP = 0;
	$totempTard = 0;
	$totempUt = 0;
	$totempOT = 0;
	$totempND = 0;
	$totempAllow = 0;
	$totOtherEarn = 0;
	$totempGross = 0;
	$totempIncome = 0;
	$totEmptax = 0;
	$totempSss = 0;
	$totempHDMF = 0;
	$totempPHIC = 0;
	$totempLoans = 0;
	$totempOthDed = 0;
	$totempDed = 0;
	$totempNetPay = 0;
	
	$grandtotemp = 0;
	$grandempEarningsSalary = 0;
	$grandempEarningsLWOP = 0;
	$grandempEarningsOT = 0;
	$grandempEarningsAllow=0;
	$grandGrossTaxable=0;
	$grandempWithTax=0;
	$grandempDeductionsSSS=0;
	$grandempLoans=0;
	$grandEarningsTARD=0;
	$grandempEarningsND=0;
	$grandempEarningsOth=0;
	$grandGrossNonTaxable=0;
	$grandDeductionsHDMF=0;
	$grandempOthAdj=0;
	$grandempEarningsUT=0;
	$grandempDeductionsPHIC=0;
	$grandtotDed = 0;
	$grandnetPay = 0;

	$empcntdept = 0;
	$cntqryemp = 0;
	$grandtotemp = 0;
	$subempEarningsSalary = 0;
	$subempEarningsLWOP = 0;
	$subempEarningsOT = 0;
	$subempEarningsAllow=0;
	$subGrossTaxable=0;
	$subempWithTax=0;
	$subempDeductionsSSS=0;
	$subempLoans=0;
	$subEarningsTARD=0;
	$subempEarningsND=0;
	$subempEarningsOth=0;
	$subGrossNonTaxable=0;
	$subDeductionsHDMF=0;
	$subempOthAdj=0;
	$subempEarningsUT=0;
	$subempDeductionsPHIC=0;
	$subtotDed = 0;
	$subnetPay = 0;
	
	while($rowpayReg = mysql_fetch_array($rspayReg))
	{
		
		if($bankcd!=$rowpayReg["bankDesc"])
		{	
			$pdf->Ln();
			$pdf->printBank($rowpayReg["bankDesc"]);
			$bankcd = $rowpayReg["bankDesc"];
			$pdf->Ln();
		}	
		
		if($deptcd!=$rowpayReg["empDepCode"])
		{
			$empcntdept = 0;
			$subempEarningsSalary = 0;
			$subempEarningsLWOP = 0;
			$subempEarningsOT = 0;
			$subGrossTaxable=0;
			$subempWithTax=0;
			$subempDeductionsSSS=0;
			$subempLoans=0;
			$subEarningsTARD=0;
			$subempEarningsND=0;
			$subempEarningsOth=0;
			$subGrossNonTaxable=0;
			$subDeductionsHDMF=0;
			$subempOthAdj=0;
			$subempEarningsUT=0;
			$subempDeductionsPHIC=0;
			$subtotDed = 0;
			$subnetPay = 0;
			$pdf->printDept($rowpayReg["deptDesc"]);
			$deptcd=$rowpayReg["empDepCode"];
			$pdf->Ln();
		}
		
		$empcntdept++;
		$pdf->SetFont('Arial','','7');
		$pdf->Cell(47,6,$rowpayReg["empLastName"].", ".$rowpayReg["empFirstName"],'LTR','','L');
		$pdf->SetFont('Arial','','7');
		$pdf->Cell(24,6,$rowpayReg["empTeu"],'LTR','','L');
		$pdf->Cell(24,6,number_format($rowpayReg["YtdGross"],2),'LTR','','R');
		
		$grandtotemp++;
		
		/*BASIC SALARY*/
		foreach ($getempEarningsSalary  as $getempEarningsSalaryValue) {
			if ($getempEarningsSalaryValue['empNo']==$rowpayReg["empNo"]) {
				$rowEarningsSalary=$getempEarningsSalaryValue["trnAmountE"];
			}
		}
		$rowEarningsSalary = ($rowEarningsSalary!=""?$rowEarningsSalary:0);
		$grossIncome = $rowEarningsSalary;
		$grandempEarningsSalary+=$rowEarningsSalary;
		$subempEarningsSalary+=$rowEarningsSalary;
		$pdf->Cell(24,6,number_format($rowEarningsSalary,2),'LTR','','R');
		
		/*LWOP*/
		foreach ($getempEarningsLWOP  as $getempEarningsLWOPValue) {
			if ($getempEarningsLWOPValue['empNo']==$rowpayReg["empNo"]) {
				$rowEarningsLWOP=$getempEarningsLWOPValue["trnAmountE"];
			}
		}
		$rowEarningsLWOP = ($rowEarningsLWOP!=""?$rowEarningsLWOP:0);
		$grandempEarningsLWOP+=$rowEarningsLWOP;
		$subempEarningsLWOP+=$rowEarningsLWOP;
		$pdf->Cell(24,6,number_format($rowEarningsLWOP,2),'LTR','','R');
		
		/*OT*/
		foreach ($getempEarningsOT  as $rowEarningsOTValue) {
			if ($rowEarningsOTValue['empNo']==$rowpayReg["empNo"]) {
				$rowEarningsOT=$rowEarningsOTValue["totAmountE"];
			}
		}
		$rowEarningsOT = ($rowEarningsOT!=""?$rowEarningsOT:0);
		$grandempEarningsOT+=$rowEarningsOT;
		$subempEarningsOT+=$rowEarningsOT;
		$grossIncome +=$rowEarningsOT;
		$pdf->Cell(24,6,number_format($rowEarningsOT,2),'LTR','','R');
		
		/*ALLOWANCE*/
		foreach ($getempEarningsAllow  as $getempEarningsAllowValue) {
			if ($getempEarningsAllowValue['empNo']==$rowpayReg["empNo"]) {
				$rowEarningsAllow=$getempEarningsAllowValue["totAmountE"];
			}
		}
		
		$rowEarningsAllow = ($rowEarningsAllow!=""?$rowEarningsAllow:0);
		$grandempEarningsAllow+=$rowEarningsAllow;
		$subempEarningsAllow+=$rowEarningsAllow;
		$othincome = $rowEarningsAllow;
		$pdf->Cell(24,6,number_format($rowEarningsAllow,2),'LTR','','R');
		
		/*GROSS INCOME TAXABLE*/
		foreach($getempGrossTaxable as $getempGrossTaxableValue)
		{
			if ($getempGrossTaxableValue['empNo']==$rowpayReg["empNo"]) {
					$rowGrossTaxable=$getempGrossTaxableValue["totAmountE"];
			}
		}
		$rowGrossTaxable = ($rowGrossTaxable!=""?$rowGrossTaxable:0);
		$grandGrossTaxable+=$rowGrossTaxable;
		$subGrossTaxable+=$rowGrossTaxable;
		$pdf->Cell(24,6,number_format($rowGrossTaxable,2),'LTR','','R');
		
		/*WITH TAX*/
		$rowWithTax= ($rowpayReg["taxWitheld"]!=""?$rowpayReg["taxWitheld"]:0);
		$grandempWithTax+=$rowWithTax;
		$subempWithTax+=$rowWithTax;
		$totDed =$rowWithTax;
		
		
		$totempIncome += $totIncome;
		$pdf->Cell(24,6,number_format($rowWithTax,2),'LTR','','R');
		
		/*SSS*/
		foreach($getempDeductionsSSS as $getempDeductionsSSSValue)
		{
			if ($getempDeductionsSSSValue['empNo']==$rowpayReg["empNo"]) {
					$rowDeductionsSSS=$getempDeductionsSSSValue["totAmountD"];
			}
		}
		$rowDeductionsSSS = ($rowDeductionsSSS!=""?$rowDeductionsSSS:0);
		$grandempDeductionsSSS+=$rowDeductionsSSS;
		$subempDeductionsSSS+=$rowDeductionsSSS;
		$totDed +=$rowDeductionsSSS;
		$pdf->Cell(24,6,number_format($rowDeductionsSSS,2),'LTR','','R');
		
		/*LOANS AND OTHER ADJ. WITH THE SAME TRN CODE*/
		foreach($getempListLoans as $getempLoansValue)
		{
			if ($getempLoansValue['empNo']==$rowpayReg["empNo"]) {
				$getempLoans=$getempLoansValue["totAmountD"];
			}
		}
		$getempLoans = ($getempLoans!=""?$getempLoans:0);
		$grandempLoans+=$getempLoans;
		$subempLoans+=$getempLoans;
		$totDed +=$getempLoans;
		$pdf->Cell(24,6,number_format($getempLoans,2),'LTR','','R');
		$pdf->Cell(24,6,'','LTR','','R');
		$pdf->Cell(24,6,'','LTR','1','R');
		
		$pdf->Cell(47,0,$rowpayReg["empNo"],'LR','','');
		$empTeuAmt = $payRegisterObj->getTaxExemption($rowpayReg["empTeu"]);
		$pdf->Cell(24,0,number_format($empTeuAmt,2),'LR','','L');
		$pdf->Cell(24,0,'','LR','','R');
		$pdf->Cell(24,0,'','LR','','R');
		
		/*TARD*/
		foreach ($getempEarningsTARD  as $getempEarningsTARDValue) {
			if ($getempEarningsTARDValue['empNo']==$rowpayReg["empNo"]) {
				$rowEarningsTARD=$getempEarningsTARDValue["trnAmountE"];
			}
		}
		$rowEarningsTARD = ($rowEarningsTARD!=""?$rowEarningsTARD:0);
		$grandEarningsTARD+=$rowEarningsTARD;
		$subEarningsTARD+=$rowEarningsTARD;
		
		//$lessATardUt += $rowEarningsTARD;
		$pdf->Cell(24,0,number_format($rowEarningsTARD,2),'LR','','R');
		
		
		/*NIGHT DIFF*/
		foreach ($getempEarningsND  as $rowEarningsNDValue) {
			if ($rowEarningsNDValue['empNo']==$rowpayReg["empNo"]) {
				$rowEarningsND=$rowEarningsNDValue["totAmountE"];
			}
		}
		$rowEarningsND=($rowEarningsND!=""?$rowEarningsND:0);
		$grandempEarningsND+=$rowEarningsND;
		$subempEarningsND+=$rowEarningsND;
		$grossIncome +=$rowEarningsND;
		$pdf->Cell(24,0,number_format($rowEarningsND,2),'LR','','R');
		
		/*OTHER EARNINGS*/
		foreach ($getempEarningsOth  as $getempEarningsOthValue) {
			if ($getempEarningsOthValue['empNo']==$rowpayReg["empNo"]) {
				$rowEarningsOth=$getempEarningsOthValue["totAmountE"];
			}
		}
		$rowEarningsOth=($rowEarningsOth!=""?$rowEarningsOth:0);
		$grandempEarningsOth+=$rowEarningsOth;
		$subempEarningsOth+=$rowEarningsOth;
		$othincome+=$rowEarningsOth;
		$pdf->Cell(24,0,number_format($rowEarningsOth,2),'LR','','R');
		
		
		/*GROSS INCOME NON TAXABLE*/
		foreach($getempGrossNonTax as $getempGrossNonTaxValue)
		{
			if ($getempGrossNonTaxValue['empNo']==$rowpayReg["empNo"]) {
					$rowGrossNonTaxable=$getempGrossNonTaxValue["totAmountE"];
			}
		}
		$rowGrossNonTaxable = ($rowGrossNonTaxable!=""?$rowGrossNonTaxable:0);
		$grandGrossNonTaxable+=$rowGrossNonTaxable;
		$subGrossNonTaxable+=$rowGrossNonTaxable;
		$pdf->Cell(24,0,number_format($rowGrossNonTaxable,2),'LR','','R');
		$pdf->Cell(24,0,'','LR','','R');
		
		/*HDMF*/
		foreach($getempDeductionsHDMF as $getempDeductionsHDMFValue)
		{
			if ($getempDeductionsHDMFValue['empNo']==$rowpayReg["empNo"]) {
					$rowDeductionsHDMF=$getempDeductionsHDMFValue["totAmountD"];
			}
		}
		$rowDeductionsHDMF = ($rowDeductionsHDMF!=""?$rowDeductionsHDMF:0);
		$grandDeductionsHDMF+=$rowDeductionsHDMF;
		$subDeductionsHDMF+=$rowDeductionsHDMF;
		$totDed +=$rowDeductionsHDMF;
		$pdf->Cell(24,0,number_format($rowDeductionsHDMF,2),'LR','','R');
		
		/*OTHER DEDUCTIONS*/
		foreach($getempOthAdjDed as $getempOthAdjDedValue)
		{
			if ($getempOthAdjDedValue['empNo']==$rowpayReg["empNo"]) {
				$getempOthAdj=$getempOthAdjDedValue["totAmountD"];
			}
		}
		$getempOthAdj = ($getempOthAdj!=""?$getempOthAdj:0);
		$grandempOthAdj+=$getempOthAdj;
		$subempOthAdj+=$getempOthAdj;
		$totDed +=$getempOthAdj;
		$pdf->Cell(24,0,number_format($getempOthAdj,2),'LR','','R');
		$pdf->Cell(24,0,'','LR','1','R');
		
		$pdf->Cell(47,6,'','LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		
		/*UT*/
		foreach ($getempEarningsUT  as $getempEarningsUTValue) {
			if ($getempEarningsUTValue['empNo']==$rowpayReg["empNo"]) {
				$rowEarningsUT=$getempEarningsUTValue["trnAmountE"];
			}
		}
		$rowEarningsUT = ($rowEarningsUT!=""?$rowEarningsUT:0);
		$grandempEarningsUT+=$rowEarningsUT;
		$subempEarningsUT+=$rowEarningsUT;
		$lessATardUt += $rowEarningsLWOP+$rowEarningsTARD+$rowEarningsUT;
		
		$grossIncome = $grossIncome + $lessATardUt;
		
		$pdf->Cell(24,6,number_format($rowEarningsUT,2),'LRB','','R');
		
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');

		/*PHIC*/
		foreach($getempDeductionsPHIC as $getempDeductionsPHICValue)
		{
			if ($getempDeductionsPHICValue['empNo']==$rowpayReg["empNo"]) {
					$rowDeductionsPHIC=$getempDeductionsPHICValue["totAmountD"];
			}
		}
		$rowDeductionsPHIC = ($rowDeductionsPHIC!=""?$rowDeductionsPHIC:0);
		$grandempDeductionsPHIC+=$rowDeductionsPHIC;
		$subempDeductionsPHIC+=$rowDeductionsPHIC;
		$totIncome = $grossIncome + $othincome;
		$totDed +=$rowDeductionsPHIC;
		$grandtotDed+=$totDed;
		$subtotDed+=$totDed;
		$netPay = $totIncome-$totDed;
		$grandnetPay+=$netPay;
		$subnetPay+=$netPay;
		 
		$pdf->Cell(24,6,number_format($rowDeductionsPHIC,2),'LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,number_format($totDed,2),'LRB','','R');
		
		
		
		foreach ($getcntemp  as $getcntempValue) {
			if ($getcntempValue['empDivCode']==$rowpayReg["empDivCode"]) {
				$cntqryemp=$getcntempValue['empCnt'];
			}
		}
		
		if($empcntdept==$cntqryemp)
		{
			$pdf->Cell(24,6,number_format($netPay,2),'LR','1','R');
			$pdf->SetFont('Arial','B','7'); 
			$pdf->Cell(95,6,$empcntdept,'LR','0','C');
			$pdf->Cell(24,6,number_format($subempEarningsSalary,2),'LR','','R');
			$pdf->Cell(24,6,number_format($subempEarningsLWOP,2),'LR','','R');
			$pdf->Cell(24,6,number_format($subempEarningsOT,2),'LR','','R');
			$pdf->Cell(24,6,number_format($subempEarningsAllow,2),'LR','','R');
			$pdf->Cell(24,6,number_format($subGrossTaxable,2),'LR','','R');
			$pdf->Cell(24,6,number_format($subempWithTax,2),'LR','','R');
			$pdf->Cell(24,6,number_format($subempDeductionsSSS,2),'LR','','R');
			$pdf->Cell(24,6,number_format($subempLoans,2),'LR','','R');
			$pdf->Cell(24,6,'','LR','','R');
			$pdf->Cell(24,6,'','LRT','1','R');
			
			$pdf->Cell(95,0,'','LR','0','C');
			$pdf->Cell(24,0,'','LR','','R');
			$pdf->Cell(24,0,number_format($subEarningsTARD,2),'LR','','R');
			$pdf->Cell(24,0,number_format($subempEarningsND,2),'LR','','R');
			$pdf->Cell(24,0,number_format($subempEarningsOth,2),'LR','','R');
			$pdf->Cell(24,0,number_format($subGrossNonTaxable,2),'LR','','R');
			$pdf->Cell(24,0,'','LR','','R');
			$pdf->Cell(24,0,number_format($subDeductionsHDMF,2),'LR','','R');
			$pdf->Cell(24,0,number_format($subempOthAdj,2),'LR','','R');
			$pdf->Cell(24,0,'','LR','','R');
			$pdf->Cell(24,0,'','LR','1','R');
			
			$pdf->Cell(95,6,'','LRB','0','C');
			$pdf->Cell(24,6,'','LRB','','R');
			$pdf->Cell(24,6,number_format($subempEarningsUT,2),'LRB','','R');
			$pdf->Cell(24,6,'','LRB','','R');
			$pdf->Cell(24,6,'','LRB','','R');
			$pdf->Cell(24,6,'','LRB','','R');
			$pdf->Cell(24,6,'','LRB','','R');
			$pdf->Cell(24,6,number_format($subempDeductionsPHIC,2),'LRB','','R');
			$pdf->Cell(24,6,'','LRB','','R');
			$pdf->Cell(24,6,number_format($subtotDed,2),'LRB','','R');
			$pdf->Cell(24,6,number_format($subnetPay,2),'LRB','','R');
			
		}
		else
		{
			$pdf->Cell(24,6,number_format($netPay,2),'LRB','','R');
		}
	
		unset($testemp,
				$totDed,
				$netPay,$grossIncome,$lessATardUt,$othincome,$totIncome,$rowEarningsAllow,
				$totDed,$netPay,$rowEarningsOth,
				$rowEarningsSalary,$rowEarningsLWOP,$rowEarningsTARD,$rowEarningsUT,$rowEarningsOT,$rowEarningsND,
				$rowEarningsAllow,$rowEarningsOth,$rowGrossTaxable,$rowGrossNonTaxable,$rowDeductionsSSS,$rowDeductionsHDMF,
				$rowDeductionsPHIC,$getempLoans,$getempOthAdj,$empTeuAmt);
		
		$pdf->Ln();
	}
		
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetFont('Arial','B','10'); 
		$pdf->Cell(335,4,'GRAND TOTAL','','1','L');
		$pdf->Ln();
		$pdf->SetFont('Arial','B','7'); 
		$pdf->Cell(95,6,$grandtotemp,'TLR','','C');
		$pdf->Cell(24,6,number_format($grandempEarningsSalary,2),'TLR','','R');
		$pdf->Cell(24,6,number_format($grandempEarningsLWOP,2),'TLR','','R');
		$pdf->Cell(24,6,number_format($grandempEarningsOT,2),'TLR','','R');
		$pdf->Cell(24,6,number_format($grandempEarningsAllow,2),'TLR','','R');
		$pdf->Cell(24,6,number_format($grandGrossTaxable,2),'TLR','','R');
		$pdf->Cell(24,6,number_format($grandempWithTax,2),'TLR','','R');
		$pdf->Cell(24,6,number_format($grandempDeductionsSSS,2),'TLR','','R');
		$pdf->Cell(24,6,number_format($grandempLoans,2),'TLR','','R');
		$pdf->Cell(24,6,'','TLR','','R');
		$pdf->Cell(24,6,'','TLR','1','R');
		$pdf->Cell(95,0,'','LR','','R');
		$pdf->Cell(24,0,'','LR','','R');
		$pdf->Cell(24,0,number_format($grandEarningsTARD,2),'LR','','R');
		$pdf->Cell(24,0,number_format($grandempEarningsND,2),'LR','','R');
		$pdf->Cell(24,0,number_format($grandempEarningsOth,2),'LR','','R');
		$pdf->Cell(24,0,number_format($grandGrossNonTaxable,2),'LR','','R');
		$pdf->Cell(24,0,'','LR','','R');
		$pdf->Cell(24,0,number_format($grandDeductionsHDMF,2),'LR','','R');
		$pdf->Cell(24,0,number_format($grandempOthAdj,2),'LR','','R');
		$pdf->Cell(24,0,'','LR','','R');
		$pdf->Cell(24,0,'','LR','1','R');
		$pdf->Cell(95,6,'','LRB','','C');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,number_format($grandempEarningsUT,2),'LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,number_format($grandempDeductionsPHIC,2),'LRB','','R');
		$pdf->Cell(24,6,'','LRB','','R');
		$pdf->Cell(24,6,number_format($grandtotDed,2),'LRB','','R');
		$pdf->Cell(24,6,number_format($grandnetPay,2),'LRB','','R');
		
		unset($grandtotemp,$totempEarningsSalary,$grandempEarningsLWOP,$grandempEarningsOT,$grandempEarningsAllow,$grandGrossTaxable,
				$grandempWithTax,$grandempDeductionsSSS,$grandempLoans,$grandEarningsTARD,$grandempEarningsND,$grandempEarningsOth,
				$grandGrossNonTaxable,$grandDeductionsHDMF,$grandempOthAdj,$grandempEarningsUT,$grandempDeductionsPHIC,$grandtotDed,
				$grandnetPay,$subempEarningsSalary,$subempEarningsLWOP,$subempEarningsOT,$subempEarningsAllow,
				$subGrossTaxable,$subempWithTax,$subempDeductionsSSS,$subempLoans,$subEarningsTARD,
				$subempEarningsND,$subempEarningsOth,$subGrossNonTaxable,$subDeductionsHDMF,$subempOthAdj,
				$subempEarningsUT,$subempDeductionsPHIC,$subtotDed,$subnetPay);		
}

$pdf->Output();

?>
