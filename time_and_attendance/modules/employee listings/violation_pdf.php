<?php
	/*
		Created By		:	Genarra Arong
		Date Created	:	09192010
		Reason			:	Report for the Violation Type
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pdf/fpdf.php");
	include("employee_listings.obj.php");
	
	$violationTypeProoflistObj= new employeeListings();
	$sessionVars = $violationTypeProoflistObj->getSeesionVars();
	
	
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
			
			$this->SetFont('Courier','','10'); 
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$this->Cell(140,5,$this->compName,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(70,5,"Report ID: VIODTL001");
			$hTitle = "Violation Code Detail";
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			
			$this->SetFont('Courier','B','10');
			
			
			$this->Cell(86.7,5,'Violation Code','1','','C');
			$this->Cell(86.7,5,'Violation Desc','1','','C');
			$this->Cell(86.7,5,'Violation Stat','1','1','C');
			
			$this->Ln();
		}
		
		function displayContent($arrVio)
		{
			foreach($arrVio as $arrVio_val)
			{
				
				$this->SetFont('Courier','','9');
				$this->Cell(86.7,5,$arrVio_val["violationCd"],'1','','C');
				$this->Cell(86.7,5,$arrVio_val["violationDesc"],'1','','L');
				$this->Cell(86.7,5,$arrVio_val["violationStat"],'1','1','C');
				
				
			}
		}
		
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','',9);
			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
		
	}
	
	
	$pdf = new PDF('L', 'mm', 'LETTER');
	$pdf->compName		=	$violationTypeProoflistObj->getCompanyName($_SESSION["company_code"]);
	
	$qryVioCode = "SELECT * from tblTK_ViolationType
					 WHERE compCode='".$_SESSION["company_code"]."' 
					 ".($_GET["vioCode"]=='0'?"":" and violationCd='".$_GET["vioCode"]."'")." 
					  ORDER BY violationDesc"; 
	
	$resVioCode = $violationTypeProoflistObj->execQry($qryVioCode);
	$arrVioCode = $violationTypeProoflistObj->getArrRes($resVioCode);

	if($violationTypeProoflistObj->getRecCount($resVioCode)>0)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $violationTypeProoflistObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrVioCode);
	}

	$pdf->Output();
?>



