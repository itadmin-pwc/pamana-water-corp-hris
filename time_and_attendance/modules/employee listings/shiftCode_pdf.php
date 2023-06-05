<?php
	/*
		Created By		:	Genarra Arong
		Date Created	:	09192010
		Reason			:	Report for the List of Active Shift Codes
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pdf/fpdf.php");
	include("employee_listings.obj.php");
	
	$shiftCodeProoflistObj= new employeeListings();
	$sessionVars = $shiftCodeProoflistObj->getSeesionVars();
	
	
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
			
			$this->Cell(70,5,"Report ID: SHFTDTL001");
			$hTitle = "Shift Code Detail";
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			
			$this->SetFont('Courier','B','10');
			
			$this->Cell(26,5,'','','','C');
			$this->Cell(25,5,'','','','C');
			$this->Cell(50,5,'Lunch','1','','C');
			$this->Cell(50,5,'Break','1','','C');
			$this->Cell(25,5,'','0','','C');
			$this->Cell(40,5,'','0','','C');
			$this->Cell(40,5,'','0','1','C');
			
			$this->Cell(26,5,'Day Desc','1','','C');
			$this->Cell(25,5,'Time - In','1','','C');
			$this->Cell(25,5,'Out','1','','C');
			$this->Cell(25,5,'In','1','','C');
			$this->Cell(25,5,'Out','1','','C');
			$this->Cell(25,5,'In','1','','C');
			$this->Cell(25,5,'Time - Out','1','','C');
			$this->Cell(40,5,'Cross Date Tag','1','','C');
			$this->Cell(40,5,'Rest Day Tag','1','1','C');
			$this->Ln();
		}
		
		function displayContent($arrShift)
		{
			$arrayDay = array('1'=>'Mon','2'=>'Tue', '3'=>'Wed', '4'=>'Thu', '5'=>'Fri', '6'=>'Sat', '7'=>'Sun');
			foreach($arrShift as $arrShift_val)
			{
				if($arrShift_val["shftCode"]!=$strShftCode)
				{
					$this->SetFont('Courier','B','10');
					$this->Cell(240,7,strtoupper($arrShift_val["shiftDesc"]),'0','1','L');
				}
					
				$this->SetFont('Courier','','9');
				$this->Cell(26,5,$arrayDay[$arrShift_val["dayCode"]],'1','','C');
				$this->Cell(25,5,$arrShift_val["shftTimeIn"],'1','','C');
				$this->Cell(25,5,$arrShift_val["shftLunchOut"],'1','','C');
				$this->Cell(25,5,$arrShift_val["shftLunchIn"],'1','','C');
				$this->Cell(25,5,$arrShift_val["shftBreakOut"],'1','','C');
				$this->Cell(25,5,$arrShift_val["shftBreakIn"],'1','','C');
				$this->Cell(25,5,$arrShift_val["shftTimeOut"],'1','','C');
				$this->Cell(40,5,$arrShift_val["crossDay"],'1','','C');
				$this->Cell(40,5,$arrShift_val["RestDayTag"],'1','1','C');
				
				$strShftCode = $arrShift_val["shftCode"];
				
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
	$pdf->compName		=	$shiftCodeProoflistObj->getCompanyName($_SESSION["company_code"]);
	$pdf->selShftCode = ($_GET["shiftCode"]=='0'?"":" and shftHdr.shiftCode='".$_GET["shiftCode"]."'");
	
	$qryShiftCode = "SELECT shftDtl.shftCode, shftHdr.shiftDesc, shftDtl.dayCode, shftDtl.shftTimeIn, shftDtl.shftLunchOut, shftDtl.shftLunchIn, 
					 shftDtl.shftBreakOut, shftDtl.shftBreakIn, shftDtl.shftTimeOut, shftDtl.crossDay, shftDtl.RestDayTag 
					 FROM tblTK_ShiftDtl shftDtl,tblTK_ShiftHdr shftHdr 
					 WHERE shftHdr.compCode='".$_SESSION["company_code"]."' and shftDtl.compCode='".$_SESSION["company_code"]."' 
					 and shftDtl.shftCode=shftHdr.shiftCode
					 ".($_GET["shiftCode"]=='0'?"":" and shftHdr.shiftCode='".$_GET["shiftCode"]."'")." 
					 ".($_GET["shiftCode"]=='0'?"":" and shftDtl.shftCode='".$_GET["shiftCode"]."'")."
					  ORDER BY shftHdr.shiftCode, shftHdr.shiftDesc"; 
	
	$resShiftCode = $shiftCodeProoflistObj->execQry($qryShiftCode);
	$arrShiftCode = $shiftCodeProoflistObj->getArrRes($resShiftCode);

	if($shiftCodeProoflistObj->getRecCount($resShiftCode)>0)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $shiftCodeProoflistObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrShiftCode);
	}

	$pdf->Output();
?>



