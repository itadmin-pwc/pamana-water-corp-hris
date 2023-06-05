<?php
	/*
		Created By		:	Genarra Arong
		Date Created	:	09192010
		Reason			:	Report for the Employee Listing of Shift Codes
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
			
			$this->Cell(70,5,"Report ID: EMPLISTSHIFT001");
			$hTitle = "Employee Listing of Shift Codes";
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			
			$this->SetFont('Courier','B','10');
			
			$this->Cell(50,5,'Emp. No','1','','C');
			$this->Cell(95,5,'Employee Name','1','','C');
			$this->Cell(100,5,'Branch','1','1','C');
			
			$this->Ln();
		}
		
		function displayContent($arrShift)
		{
			foreach($arrShift as $arrShift_val)
			{
				if($arrShift_val["shiftCode"]!=$strShftCode)
				{
					$this->SetFont('Courier','B','10');
					$this->Cell(240,7,strtoupper($arrShift_val["shiftDesc"]),'0','1','L');
				}
				
				$this->SetFont('Courier','','9');
				$this->Cell(50,5,$arrShift_val["empNo"],'1','','C');
				$this->Cell(95,5,$arrShift_val["empLastName"].", ".$arrShift_val["empFirstName"]." ".$arrShift_val["empMidName"][1].".",'1','','L');
				$this->Cell(100,5,$arrShift_val["brnDesc"],'1','1','L');
				
				$strShftCode = $arrShift_val["shiftCode"];
				
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
	
	$qryShiftCode = "SELECT shftHdr.shiftCode, shftHdr.shiftDesc, empShift.empNo, empLastName, empFirstName,empMidName, brnDesc, empBrnCode from
					 tblTK_ShiftHdr shftHdr , tblTK_EmpShift empShift, tblEmpMast empMast, tblBranch brnch
					 WHERE shftHdr.compCode='".$_SESSION["company_code"]."' and empShift.compCode='".$_SESSION["company_code"]."' and empMast.compCode='".$_SESSION["company_code"]."'
					 and brnch.compCode='".$_SESSION["company_code"]."'
					 and shftHdr.shiftCode=empShift.shiftCode and empShift.empNo=empMast.empNo and empMast.empBrnCode=brnch.brnCode
					 ".($_GET["branchCode"]=='0'?"":" and empMast.empBrnCode='".$_GET["branchCode"]."'")." 
					 ".($_GET["shiftCode"]=='0'?"":" and shftHdr.shiftCode='".$_GET["shiftCode"]."'")." 
					 ORDER BY shftHdr.shiftCode, shftHdr.shiftDesc,brnDesc, empLastName, empFirstName "; 
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



