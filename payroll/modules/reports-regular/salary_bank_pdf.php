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
$pdf->Cell(40,6,'BRANCH',1,'','C');
$pdf->Cell(40,6,'E-CASH',1,'','C');
$pdf->Cell(40,6,'SAVINGS',1,'','C');
$pdf->Cell(40,6,'CASH',1,'','C');
$pdf->Cell(37,6,'TOTAL',1,'','C');
$AmtBPI = $AmtECASH = $AmtBOC = $AmtCASH = 0;
$totAmtBPI = $totECASH = $totAmtBOC = $totAmtCASH = 0;
$pdf->Ln();
foreach($arrBranch as $valBranch) { 

	foreach ($arrSalaryList as $valSalary) {
		if ($valBranch['brnCode'] == $valSalary['empBrnCode']) {
			switch ($valSalary['bankCd']) {
				case 7: //E Cash
						$AmtECASH = $valSalary['Salary'] + $valSalary['Allow'];
				break;
				case 8: //BPI
						$AmtBPI = $valSalary['Salary'] + $valSalary['Allow'];
				break;
				case 3: //Cash
						$AmtCASH = $valSalary['Salary'] + $valSalary['Allow'];
				break;
			}
		}
	}
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(40,6,$valBranch['brnShortDesc'],1,'','L');
	$pdf->Cell(40,6,number_format($AmtECASH,2),1,'','R');
	$pdf->Cell(40,6,number_format($AmtBPI,2),1,'','R');
	$pdf->Cell(40,6,number_format($AmtCASH,2),1,'','R');
	$pdf->Cell(37,6,number_format($AmtCASH+$AmtECASH+$AmtBPI,2),1,'','R');
	$pdf->Ln();
	$totECASH	+= $AmtECASH;
	$totAmtBPI += $AmtBPI;
	$totAmtCASH	+= $AmtCASH;
	$AmtECASH = $AmtBPI = $AmtCASH = 0;	
}	
$pdf->SetFont('Arial','B',9);
$pdf->Cell(40,6,'GRAND TOTAL',1,'','L');
$pdf->Cell(40,6,number_format($totECASH,2),1,'','R');
$pdf->Cell(40,6,number_format($totAmtBPI,2),1,'','R');
$pdf->Cell(40,6,number_format($totAmtCASH,2),1,'','R');
$pdf->Cell(37,6,number_format($totAmtCASH+$totECASH+$totAmtBPI,2),1,'','R');
$pdf->Ln();
$pdf->Output('salary_by_bank.pdf','D');

?>
