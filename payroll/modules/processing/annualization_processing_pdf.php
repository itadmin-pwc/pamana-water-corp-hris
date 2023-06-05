<?php
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("annualization_processing.obj.php");
include("../../../includes/pdf/fpdf.php");

$annProcObj = new  AnnualProcObj($_SESSION,$_GET);
$sessionVars = $annProcObj->getSeesionVars();

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
	
	function PayRegHeader($compName)
	{
		$lblPosted = "ANNUALIZATION REPORT";
		$lblCurr = "AS OF ".date('l, F d, Y');
		
		$this->SetFont('Arial','B','10'); 
		$this->Cell(335,4,strtoupper($compName),'','1','C');
		
		$this->SetFont('Arial','','9');
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
		$this->Cell(32,6,'  PREV. EMPLOYER','LTR','','C');
		$this->Cell(32,6,'  CURR. EMPLOYER','LTR','','C');
		$this->Cell(32,6,'  TAX','LTR','','C');
		$this->Cell(32,6,'  TAXABLE','LTR','','C');
		$this->Cell(32,6,'  TAX DUE','LTR','','C');
		$this->Cell(32,6,'  TAX WITHELD','LTR','','C');
		$this->Cell(32,6,'  TAX WITHELD','LTR','','C');
		$this->Cell(32,6,'  TAX WITHELD','LTR','','C');
		$this->Cell(32,6,'  OVER','LTR','','C');
		$this->Ln();
		$this->SetFont('Arial','B','8');
		$this->Cell(47,1,'  EMP. NO','LR','','C');
		$this->Cell(32,1,'  INCOME','LR','','C');
		$this->Cell(32,1,'  INCOME','LR','','C');
		$this->Cell(32,1,'  EXEMPTION','LR','','C');
		$this->Cell(32,1,'  INCOME','LR','','C');
		$this->Cell(32,1,'  ','LR','','C');
		$this->Cell(32,1,'  (PREV)','LR','','C');
		$this->Cell(32,1,'  (CURR)','LR','','C');
		$this->Cell(32,1,'  (TOTAL)','LR','','C');
		$this->Cell(32,1,'  UNDER','LR','','C');
		$this->Ln();
		$this->SetFont('Arial','B','8');
		$this->Cell(47,3,'  ','LRB','','C');
		for($i=1; $i<=9; $i++)
		{
			$this->Cell(32,3,'  ','LRB','','C');
		}
		$this->Ln();
	}
}

$pdf=new PDF('L', 'mm', 'LEGAL');
$compName = $annProcObj->getCompName($_SESSION['company_code']);

$pdf->AddPage();

$pdf->PayRegHeader($compName["compName"]);
$pdf->nxttoHeader();

$pdYear = "2009";
		
$compAnnDate = date("m/d/Y",strtotime($annProcObj->getCompAnnDate($_SESSION['company_code'])));
$currDate = "01/01/2010";
//$currDate = date("m/d/Y");

if(strtotime($compAnnDate)==strtotime($currDate))
{
	
	$qrygetEmp = 	"Select ytdData.compCode,ytdData.pdYear,ytdData.empNo,ytdData.YtdGross,
					ytdData.YtdTaxable,ytdData.YtdGovDed,ytdData.YtdTax,ytdData.payGrp,empTeu,
					empLastName,empFirstName,empMidname 
					from tblYtdDataHist ytdData 
					left join tblEmpMast empMast
					ON ytdData.empNo=empMast.empNo
					where ytdData.compCode='".$_SESSION["company_code"]."'
					and pdYear='".$pdYear."'
					order by empLastName,empFirstName";
				
	$rsgetEmp =$annProcObj->execQry($qrygetEmp);
	
	if(mysql_num_rows($rsgetEmp)>=1)
	{
		
		$rowgetEmp = $annProcObj->getArrRes($rsgetEmp);
		foreach ((array)$rowgetEmp as $arrgetEmp)
		{//foreach for post adjustment and others
			//$EmpWithTax = $annProcObj->computeAnnTaxRegEmp($arrgetEmp["empNo"],$arrgetEmp["empTeu"]);
			$pdf->SetFont('Arial','','7');
			$pdf->Cell(47,6,$arrgetEmp["empLastName"].", ".$arrgetEmp["empFirstName"],'LTR','','L');
			
			/*Previous Employer Record*/
			$qrygetPrevEmplr = "Select * from tblPrevEmployer where empNo='".$arrgetEmp["empNo"]."' and yearCd='".$pdYear."' and compCode='".$_SESSION['company_code']."' and prevStat='A'";
			$resgetPrevEmplr = $annProcObj->execQry($qrygetPrevEmplr);
			if(mysql_num_rows($resgetPrevEmplr)>=1)
			{
				while($rowgetPrevEmplr = mysql_fetch_array($resgetPrevEmplr))
				{
					$prev_gross_income+= (float) ($rowgetPrevEmplr["prevEarnings"]!=0?$rowgetPrevEmplr["prevEarnings"]:0);
				
				}
			}
			
				$curr_gross_income = (float) ($arrgetEmp["YtdTaxable"]!=0?$arrgetEmp["YtdTaxable"]:0);
			
			
			$pdf->Cell(32,6,number_format($prev_gross_income,2),'LTR','','R');
			$pdf->Cell(32,6,number_format($curr_gross_income,2),'LTR','0','R');
			
			$taxableAmount = $prev_gross_income + $curr_gross_income;
		
			$equiTeu = $annProcObj->getTaxExemption($arrgetEmp["empTeu"]);
			
			$pdf->Cell(32,6,number_format($equiTeu,2),'LTR','','R');
			
			$taxableAmount = $taxableAmount - $equiTeu;
			$pdf->Cell(32,6,number_format($taxableAmount,2),'LTR','','R');
			
			$curr_taxDue = $annProcObj->getAnnualTax($taxableAmount);
			
			$pdf->Cell(32,6,number_format($curr_taxDue,2),'LTR','','R');
		
			$prev_wtax = (float) ($rowgetPrevEmplr["prevTaxes"]!=0?$rowgetPrevEmplr["prevTaxes"]:0);
			$curr_wtax = (float) ($arrgetEmp["YtdTax"]!=0?$arrgetEmp["YtdTax"]:0);
			
			$currmprev = $curr_wtax - $prev_wtax;
			
			$pdf->Cell(32,6,number_format($prev_wtax,2),'LTR','','R');
			$pdf->Cell(32,6,number_format($currmprev,2),'LTR','','R');
			$pdf->Cell(32,6,number_format($curr_wtax,2),'LTR','','R');
			
			$annualTax = $curr_taxDue - $curr_wtax;
			$annualTax = sprintf("%01.2f",$annualTax);
			
			$pdf->Cell(32,6,number_format($annualTax,2),'LTR','1','R');
			
			$pdf->Cell(47,3,$arrgetEmp["empNo"],'LRB','','L');
			for($i=1; $i<=8; $i++)
			{
				$pdf->Cell(32,3,'  ','LRB','','C');
			}
			$pdf->Cell(32,3,'  ','LRB','1','C');
			
			$sum_prev_gross_income+=$prev_gross_income;
			$sum_curr_gross_income+=$curr_gross_income;
			$sum_equiTeu+=$equiTeu;
			$sum_taxableAmount+=$taxableAmount;
			$sum_curr_taxDue+=$curr_taxDue;
			$sum_prev_wtax+=$prev_wtax;
			$sum_curr_wtax+=$curr_wtax;
			$sum_currmprev+=$currmprev;
			$sum_annualTax+=$annualTax;
			unset($prev_gross_income,$curr_gross_income,$equiTeu,$taxableAmount,$curr_taxDue,$prev_wtax,$curr_wtax,$currmprev,$annualTax);
		}//end foreach for post adjustment and others
		
		/*SUMMATION*/
		$pdf->SetFont('Arial','B','10'); 
		$pdf->Cell(47,6,'GRAND TOTAL','LTRB','','L');
		$pdf->Cell(32,6,number_format($sum_prev_gross_income,2),'LTRB','','R');
		$pdf->Cell(32,6,number_format($sum_curr_gross_income,2),'LTRB','','R');
		$pdf->Cell(32,6,number_format($sum_equiTeu,2),'LTRB','','R');
		$pdf->Cell(32,6,number_format($sum_taxableAmount,2),'LTRB','','R');
		$pdf->Cell(32,6,number_format($sum_curr_taxDue,2),'LTRB','','R');
		$pdf->Cell(32,6,number_format($sum_prev_wtax,2),'LTRB','','R');
		$pdf->Cell(32,6,number_format($sum_curr_wtax,2),'LTRB','','R');
		$pdf->Cell(32,6,number_format($sum_currmprev,2),'LTRB','','R');
		$pdf->Cell(32,6,number_format($sum_annualTax,2),'LTRB','','R');
	}	
	
}

$pdf->Output();

?>
