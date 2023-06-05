<?php
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
include("../../../includes/pdf/fpdf.php");
class PDF extends FPDF
{
	var $printedby;
	var $company;
	var $rundate;
	var $table;
	var $reportlabel;
	var $arrPayPd;
	function Header()
	{
		$this->SetFont('Arial','','9'); 
		$this->Cell(70,5,"Run Date: " . $this->rundate);
		$this->Cell(200,5,$this->company);
		$this->Cell(35,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
		$this->Ln();
		$this->Cell(70,5,"Report ID:TOTSALBYBANK");
		$this->Cell(200,5,'Total Salary by Bank ' . $this->reportlabel);
		$this->Ln();
		
	}

	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(195,1,'','T');
		$this->Ln();
		$this->SetFont('Arial','B',9);
		$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}
}

$pdf=new PDF('P', 'mm', 'LETTER');
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();
$payPd = $_GET['payPd'];
$groupName = ($groupType==1?"GROUP 1":"GROUP 2");
$catName = $psObj->getEmpCatArt($_SESSION['company_code'], $catType);
$arrPayPd = $psObj->getSlctdPd($_SESSION['company_code'],$payPd);


$arrSalaryList = $psObj->TotSal($_SESSION['company_code'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$payPd,$_GET['brnCode']);
$arrTotalSalaryList = $psObj->TotSal($_SESSION['company_code'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$payPd,$_GET['brnCode'],1);
$arrBranch =  $psObj->BranchGrp($payPd,$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$_GET['brnCode']);
$pdf->AliasNbPages();
$pdf->table = $reportType;
$pdf->arrPayPd=$arrPayPd;
switch($_SESSION['pay_category']) {
	case 1: 
		$payCat = "(Executive)";
	break;
	case 2: 
		$payCat = "(Confidential)";
	break;
	case 3: 
		$payCat = "(Non Confidential)";
	break;
	case 9: 
		$payCat = "(Resigned)";
	break;
}
$pdf->reportlabel = "Pay Period ".date("m/d/Y",strtotime($arrPayPd['pdPayable']))." Group {$_SESSION['pay_group']} $payCat";
$pdf->company = $psObj->getCompanyName($_SESSION['company_code']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();


$totSal = $allow = $branchTotal = 0;
$branch="";
$pdf->AddPage();
$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,6,'BRANCH',1,'','C');
$pdf->Cell(20,6,'AUB',1,'','C');
$pdf->Cell(20,6,'MBTC',1,'','C');
//$pdf->Cell(20,6,'BOC',1,'','C');
$pdf->Cell(20,6,'CASH',1,'','C');
$pdf->Cell(20,6,'BDO',1,'','C');
$pdf->Cell(20,6,'EBC',1,'','C');
$pdf->Cell(20,6,'CBC',1,'','C');
$pdf->Cell(32,6,'TOTAL',1,'','C');
$AmtAUB = $AmtMBTC = $AmtBOC = $AmtCASH = $AmtCBC =0;
$totAmtAUB = $totAmtMBTC = $totAmtBOC = $totAmtCASH = $totAmtCBC = 0;
$pdf->Ln();
foreach($arrBranch as $valBranch) { 

	foreach ($arrSalaryList as $valSalary) {
		if ($valBranch['brnCode'] == $valSalary['empBrnCode']) {
			switch ($valSalary['bankCd']) {
				case 1: //MetroBank
						$AmtMBTC = $valSalary['Salary'] + $valSalary['Allow'];
				break;
				case 2: //AUB
						$AmtAUB = $valSalary['Salary'] + $valSalary['Allow'];
				break;
				case 3: //Cash
						$AmtCASH = $valSalary['Salary'] + $valSalary['Allow'];
				break;
				case 6: //BOC
						$AmtBOC = $valSalary['Salary'] + $valSalary['Allow'];
				break;
				case 4: //BDO
						$AmtBDO = $valSalary['Salary'] + $valSalary['Allow'];
				break;
				
				case 5: //BDO
						$AmtEBC = $valSalary['Salary'] + $valSalary['Allow'];
				break;
				case 7: //BDO
						$AmtCBC = $valSalary['Salary'] + $valSalary['Allow'];
				break;
			}
		}
	}
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(45,6,$valBranch['brnShortDesc'],1,'','L');
	$pdf->Cell(20,6,number_format($AmtAUB,2),1,'','R');
	$pdf->Cell(20,6,number_format($AmtMBTC,2),1,'','R');
	//$pdf->Cell(20,6,number_format($AmtBOC,2),1,'','R');
	$pdf->Cell(20,6,number_format($AmtCASH,2),1,'','R');
	$pdf->Cell(20,6,number_format($AmtBDO,2),1,'','R');
	$pdf->Cell(20,6,number_format($AmtEBC,2),1,'','R');
	$pdf->Cell(20,6,number_format($AmtCBC,2),1,'','R');
	$pdf->Cell(32,6,number_format($AmtCASH+$AmtMBTC+$AmtAUB+$AmtBDO+$AmtEBC+$AmtCBC,2),1,'','R');
	$pdf->Ln();
	$totAmtAUB	+= $AmtAUB;
	$totAmtMBTC += $AmtMBTC;
	$totAmtBOC	+= $AmtBOC;
	$totAmtCASH	+= $AmtCASH;
	$totAmtBDO	+= $AmtBDO;
	$totAmtEBC	+= $AmtEBC;
	$totAmtCBC	+= $AmtCBC;
	$AmtAUB = $AmtMBTC = $AmtBOC = $AmtCASH = $AmtBDO = $AmtEBC = $AmtCBC = 0;	
}	
$pdf->SetFont('Arial','B',9);
$pdf->Cell(45,6,'GRAND TOTAL',1,'','L');
$pdf->Cell(20,6,number_format($totAmtAUB,2),1,'','R');
$pdf->Cell(20,6,number_format($totAmtMBTC,2),1,'','R');
//$pdf->Cell(20,6,number_format($totAmtBOC,2),1,'','R');
$pdf->Cell(20,6,number_format($totAmtCASH,2),1,'','R');
$pdf->Cell(20,6,number_format($totAmtBDO,2),1,'','R');
$pdf->Cell(20,6,number_format($totAmtEBC,2),1,'','R');
$pdf->Cell(20,6,number_format($totAmtCBC,2),1,'','R');
$pdf->Cell(32,6,number_format($totAmtCASH+$totAmtMBTC+$totAmtAUB+$totAmtBDO+$totAmtEBC+$totAmtCBC,2),1,'','R');
$pdf->Ln();
$pdf->Output('salary_by_bank.pdf','D');

?>
