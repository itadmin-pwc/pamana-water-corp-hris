<?php
session_start();
ini_set("max_execution_time","0");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pdf/fpdf.php");
include("ts_obj.php");
class PDF extends FPDF
{
	
	function Header() {
		$this->SetFont('Courier','B',9);
		$this->Cell(70,5,'Run Date: '.$this->currentDateArt(),0);
		$this->Cell(90,5,$this->getCompanyName($_SESSION['company_code']),0);
		$this->Cell(30,5,'Page '.$this->PageNo().'/{nb}',0,1);
		//$this->Cell(70,5,'Report ID: VIOLATIONSRPT',0);
		$this->Cell(20,5,'VIOLATION: ',0,0);
		$this->SetFont('Courier','',9);
		$this->Cell(60,5,$_GET['arrValHeader'],0,1);
		$this->SetFont('Courier','B',9);
		$this->Cell(195,7,'EMPLOYEE No.      BIO No.               NAME                                  DATE COMMITED          ','TB',1);
		
	}
	
	
	function Footer() {
		$this->SetFont('Courier','',9);
	    $this->SetY(-15);
		$dispUser = $this->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$user = $dispUser["empFirstName"]." ".$dispUser["empLastName"];
		$this->Cell(195,7,"Printed By: $user                                                    Approved by: " ,'T',0);
	}

	function Main($arrViolations){
		$this->SetFont('Courier','',9);
		$this->AddPage('P');
		$emp="";
		foreach($arrViolations as $valViolations){		
			if($emp!=$valViolations['empNo']){	
			$this->SetFont('Courier','B',9);	
			$this->Cell(35,5,$valViolations['empNo'],0,0,'L');	
			$this->SetFont('Courier','',9);	
			$this->Cell(30,5,$valViolations['bioNumber'],0,0,'L');			
			$this->Cell(80,5,$valViolations['empLastName'].", ".$valViolations['empFirstName']." ".substr($valViolations['empMidName'],0,1).".",0,1,'L');			
			}
			$this->Cell(150,5,"",0,0,'L');
			$this->Cell(30,5,$valViolations['dateCommited'],0,1,'L');
			$emp=$valViolations['empNo'];
		}
	}	
}
$inqTSObj = new inqTSObj();
$arrViolations = $inqTSObj->violationsReport($_GET['empNo'],$_GET['from'],$_GET['to'],$_GET['branch'],$_GET['bio'],$_GET['violations']);
$pdf=new PDF();
$pdf->FPDF('P', 'mm', 'LETTER');
$pdf->AliasNbPages(); 
$pdf->Main($arrViolations);
$pdf->Output('violations_report.pdf','D');
?>