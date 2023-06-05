<?php
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	03/24/2010
		Function		:	Blacklist Module (Pop Up) 
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("common_obj.php");
	include("../../../includes/pdf/fpdf.php");
	
	$payrollTypeObj = new inqTSObj();
	$sessionVars = $payrollTypeObj->getSeesionVars();
	$payrollTypeObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
		
			$this->SetFont('Arial','','10'); 
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$hTitle = " Position Listing Based on Division, Department and Section";
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(70,5,"Report ID: PAYLIST");
			
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			$this->Cell(10,5,'','0','0','L');
			$this->Cell(10,5,'','0','0','L');
			$this->Cell(10,5,'','0','0','L');
			$this->Cell(50,5,'DIVISION','1','0','L');
			$this->Cell(50,5,'DEPARTMENT','1','0','L');
			$this->Cell(40,5,'SECTION','1','0','L');
			$this->Cell(60,5,'POSITION','1','0','L');
			$this->Cell(15,5,'RANK','1','0','L');
			$this->Cell(15,5,'LEVEL','1','1','L');
			$this->Ln();
			
		}
		
	
		
		function displayContent()
		{
			$qrygetDivision = "Select * from tblDepartment where deptLevel='1' order by deptDesc";
			$rsgetDivision = $this->execQry($qrygetDivision);
			$arrgetDivision =  $this->getArrRes($rsgetDivision);
			$ctr_div = 1;
			foreach($arrgetDivision as $arrgetDivision_val)
			{
				$ctr_dept = 1;
				//$this->Cell(10,5,$arrgetDivision_val["divCode"],'0','0','C');
				$this->Cell(10,5,$ctr_div,'0','0','L');
				$this->Cell(100,5,'Division = '.$arrgetDivision_val["deptDesc"],'0','1','L');
				
				$qrygetDept = "Select * from tblDepartment where divCode='".$arrgetDivision_val["divCode"]."' and deptLevel='2' order by deptDesc";
				$rsgetDept = $this->execQry($qrygetDept);
				$arrgetDept =  $this->getArrRes($rsgetDept);
				foreach($arrgetDept as $arrgetDept_val)
				{
					$ctr_sect = 1;
					$this->Cell(10,5,'','0','0','L');
					//$this->Cell(10,5,$arrgetDept_val["deptCode"],'1','0','L');
					$this->Cell(10,5,$ctr_dept.".",'0','0','L');
					$this->Cell(100,5,'Department = '.$arrgetDept_val["deptDesc"],'0','1','L');
					
					$qrygetSect = "Select * from tblDepartment where divCode='".$arrgetDivision_val["divCode"]."' and deptCode='".$arrgetDept_val["deptCode"]."' and deptLevel='3' order by deptDesc";
					$rsgetSect = $this->execQry($qrygetSect);
					$arrgetSect =  $this->getArrRes($rsgetSect);
					foreach($arrgetSect as $arrgetSect_val)
					{
						$this->Cell(10,5,'','0','0','L');
						$this->Cell(10,5,'','0','0','L');
						//$this->Cell(10,5,$arrgetSect_val["sectCode"],'1','0','L');
						$this->Cell(10,5,$ctr_sect.".",'0','0','L');
						$this->Cell(90,5,'Section = '.$arrgetSect_val["deptDesc"],'0','1','L');
						
						$qrygetPos = "Select * from tblPosition where divCode='".$arrgetDivision_val["divCode"]."' and deptCode='".$arrgetDept_val["deptCode"]."' and sectCode='".$arrgetSect_val["sectCode"]."' order by posDesc";
						$rsgetPos = $this->execQry($qrygetPos);
						$arrgetPos =  $this->getArrRes($rsgetPos);
						foreach($arrgetPos as $arrgetPos_val)
						{
							$this->Cell(10,5,'','0','0','L');
							$this->Cell(10,5,'','0','0','L');
							$this->Cell(10,5,'','0','0','L');
							$this->Cell(50,5,$arrgetDivision_val["divCode"]." - ".trim(substr($arrgetDivision_val["deptDesc"], 0,17)),'1','0','L');
							$this->Cell(50,5,$arrgetDept_val["deptCode"]." - ".trim(substr($arrgetDept_val["deptDesc"], 0,17)),'1','0','L');
							$this->Cell(40,5,$arrgetSect_val["sectCode"]." - ".trim(substr($arrgetSect_val["deptDesc"], 0,13)),'1','0','L');
							$this->Cell(60,5,trim(substr($arrgetPos_val["posDesc"], 0,25)),'1','0','L');
							$this->Cell(15,5,$arrgetPos_val["rank"],'1','0','L');
							$this->Cell(15,5,$arrgetPos_val["level"],'1','1','L');
						}
						$this->Ln();
						$ctr_sect++;
					}
					$this->Ln();
					$ctr_dept++;
				}
				$this->Ln();
				
				$ctr_div++;
			}
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(260,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',9);
			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}
	
	
	
	$pdf = new PDF('L', 'mm', 'LETTER');
	

	$pdf->AliasNbPages();
	$pdf->printedby = $payrollTypeObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
	$pdf->AddPage();
	$pdf->displayContent();
	
	
	$pdf->Output();
?>