<?php
	/*
		Created By		:	Genarra Arong
		Date Created	:	01192010
		Reason			:	Report for the Unposted Transactions
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pdf/fpdf.php");
	include("empmast_migration.obj.php");
	
	$migEmpMastObj= new migEmpMastObj();
	$sessionVars = $migEmpMastObj->getSeesionVars();
	
	
	class PDF extends FPDF
	{
		function Header()
		{
				$gmt = time() + (8 * 60 * 60);
				$newdate = date("m/d/Y h:iA", $gmt);
				
				$this->SetFont('Arial','','9'); 
				$this->Cell(70,5,"Run Date: " . $newdate);
				$this->Cell(140,5,$this->company,'0','','C');
				$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
				$this->Ln();
				
				$this->Cell(70,5,"Report ID: CONSOLALPHALIST");
				$this->Cell(140,5,'Alphalist Consolidation Report for the Year '.date("Y"),'0','','C');
				$this->Ln();
				$this->Ln();
		}	
		
		function dispBranch()
		{
			$qryBranch = "Select brnCode, brnDesc from tblBranch
							where compCode='".$_SESSION["company_code"]."'
							and brnCode in (Select  empBrnCode
							from tblYtdDataHist ytdHist, tblEmpMast empMast
							where ytdHist.compCode='".$_SESSION["company_code"]."' and empMast.compCode='".$_SESSION["company_code"]."'
							and ytdHist.empNo = empMast.empNo ".($this->brnCode==0?"":"and empBrnCode='".$this->brnCode."'")."
							
							) 
							
							order by brnDesc;";
			//echo $qryBranch ;
			$resBranch = $this->execQry($qryBranch);
			$resBranch = $this->getArrRes($resBranch);
			return $resBranch;
		}
		
		
		function getPrevEmployerData($empNo)
		{
			$qryPrevEmp = "Select * from tblPrevEmployer where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."'";
			$resPrevEmp = $this->execQry($qryPrevEmp);
			return $this->getSqlAssoc($resPrevEmp);
			
		}
		
		private function getAnnualTax($taxInc)
		{
			$qrycomputeWithTax = "Select * from tblAnnTax where $taxInc between txLowLimit and txUpLimit";
			$rescomputeWithTax = $this->execQry($qrycomputeWithTax);
			$rowcomputeWithTax = $this->getSqlAssoc($rescomputeWithTax);
			$compTax = ((($taxInc-$rowcomputeWithTax["txLowLimit"])*$rowcomputeWithTax["txAddPcent"])+$rowcomputeWithTax["txFixdAmt"]);
			
			return sprintf("%01.2f", $compTax);
		}
		
		function displayContentDetails($arrEmpList)
		{
			$this->Ln();
			$this->SetFont('Arial','','10'); 
			$arrdispBranch = $this->dispBranch();
			$empCount = 1;
			
			foreach($arrdispBranch as $arrdispBranch_val)
			{
				$this->SetFont('Arial','B','10'); 
				$this->Cell(47,8,"BRANCH = ".strtoupper($arrdispBranch_val["brnDesc"]),0,'1','L');
				
				$this->SetFont('Arial','','10'); 
				foreach($arrEmpList as $arrEmpList_val)
				{	
					if($arrEmpList_val["empBrnCode"]==$arrdispBranch_val["brnCode"])
					{
						
						$taxableAmt = $taxDue = $empOvrUnder = $aveTaxPay = 0;
						$arrPrevEmplr = $this->getPrevEmployerData($arrEmpList_val["empNo"]);
						
						$taxableAmt = ($arrEmpList_val["YtdTaxable"]  + $arrPrevEmplr["prevEarnings"]) - ($arrEmpList_val["YtdGovDed"] + $arrPrevEmplr["nonTaxSss"]) - ($arrEmpList_val["teuAmt"]);
						//$estEarn = (((($arrEmpList_val["YtdTaxable"]  + $arrPrevEmplr["prevEarnings"]) - $arrEmpList_val["YtdGovDed"]) / ($arrEmpList_val["empPayGrp"]!=1?22:23))*24);
						
						//$taxableAmt = $taxableAmt - $arrEmpList_val["teuAmt"];
						
						$taxDue = $this->getAnnualTax($taxableAmt);
						
						$empOvrUnder = $taxDue - $arrEmpList_val["YtdTax"] - $arrPrevEmplr["prevTaxes"];
						
						if($arrEmpList_val["empWageTag"]=='N')
						{
							//if(round($empOvrUnder,1)>0)
							//{
									$this->SetFont('Arial','B','10'); 
									$dtRes = ($arrEmpList_val["dateResigned"] != "") ? date("m/d/Y", strtotime($arrEmpList_val["dateResigned"])): "N/A";
									$this->Cell(47,6,$empCount.". ".$arrEmpList_val["empNo"]."=".$arrEmpList_val["empLastName"].", ".$arrEmpList_val["empFirstName"]." ".$arrEmpList_val["empMidName"][0].". : Date Hired : ".date("m/d/Y", strtotime($arrEmpList_val["dateHired"]))." : Date Resigned : ".$dtRes." : Min. Wage Tag : ".$arrEmpList_val["empWageTag"]. ": Emp. Pay Type : ".$arrEmpList_val["empPayType"]." : Group : ".$arrEmpList_val["empPayGrp"],'','1','L');
							
							
									$this->SetFont('Arial','B','8'); 
									$this->Cell(10,5,'','','','L');
									$this->Cell(53.2,5,'CURRENT','1','','C');
									$this->Cell(53.2,5,'PREVIOUS','1','1','C');
									
									$this->Cell(10,5,'','','','L');
									$this->Cell(26.6,5,'Earnings','1','','C');
									$this->Cell(26.6,5,'13TH Month','1','','C');
									$this->Cell(26.6,5,'Earnings','1','','C');
									$this->Cell(26.6,5,'13TH Month','1','','C');
									$this->Cell(26.6,5,'Curr. Mandatory','1','','C');
									$this->Cell(26.6,5,'Prev. Mandatory','1','','C');
									
									$this->Cell(26.6,5,'Exemption','1','','C');
									$this->Cell(26.6,5,'Taxable Amount','1','','C');
									$this->Cell(26.6,5,'Tax Due','1','','C');
									$this->Cell(26.6,5,'Curr. Ded.','1','','C');
									$this->Cell(26.6,5,'Prev. Tax','1','','C');
									
									$this->Cell(26.6,5,'Over / Under','1','1','C');
									
									$this->Cell(10,5,'','','','L');
									$this->Cell(26.6,5,number_format(($arrEmpList_val["YtdTaxable"] - $arrEmpList_val["YtdTx13NBonus"]),2),'1','','R');
									$this->Cell(26.6,5,number_format($arrEmpList_val["YtdTx13NBonus"],2),'1','','R');
									$this->Cell(26.6,5,number_format($arrPrevEmplr["prevEarnings"], 2),'1','','R');
									$this->Cell(26.6,5,number_format($arrPrevEmplr["tax13th"], 2),'1','','R');
									$this->Cell(26.6,5,number_format($arrEmpList_val["YtdGovDed"],2),'1','','R');
									$this->Cell(26.6,5,number_format($arrPrevEmplr["nonTaxSss"], 2),'1','','R');
									
									//$this->Cell(26.6,5,number_format($estEarn, 2),'1','','R');
									$this->Cell(26.6,5,$arrEmpList_val["empTeu"]." - ".number_format($arrEmpList_val["teuAmt"],2),'1','','R');
									
									
									
									$this->Cell(26.6,5,number_format($taxableAmt,2),'1','','R');
									$this->Cell(26.6,5,number_format($taxDue,2),'1','','R');
									$this->Cell(26.6,5,number_format($arrEmpList_val["YtdTax"],2),'1','','R');
									$this->Cell(26.6,5,number_format($arrPrevEmplr["prevTaxes"],2),'1','','R');
									$this->Cell(26.6,5,number_format($empOvrUnder,2),'1','1','R');
									//$this->Cell(26.6,5,number_format($aveTaxPay,2),'1','1','R');
									$this->Ln();
									$empCount++;
									
									$sum_empOvrUnder+=$empOvrUnder;
									$sum_aveTaxPay+=$aveTaxPay;
									
									$empNoList.="'".$arrEmpList_val["empNo"]."',";
									
							//}
						}
					}
				
				}//end of EmpList
					//$this->AddPage();
					$empTestCont = 1;
					//$empCount = 1;
			}//end of Branch
			$this->SetFont('Arial','B','8'); 
					$this->Cell(10,5,'','','','L');
					$this->Cell(47,6,'GRAND - TOTAL(S) = '.($empCount-1),'','1','L');
					
					$this->Cell(10,5,'','','','L');
					$this->Cell(266,5,'','1','','C');
					$this->Cell(26.6,5,'Over (+) / Under(-)','1','','C');
					$this->Cell(26.6,5,'Ave. Tax / Payroll','1','1','C');
					
					$this->Cell(10,5,'','','','L');
					$this->Cell(266,5,'','1','','C');
					$this->Cell(26.6,5,number_format($sum_empOvrUnder,2),'1','','R');
					$this->Cell(26.6,5,number_format($sum_aveTaxPay,2),'1','1','R');
					
			//echo $empNoList;
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Arial','',9);
			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
		
	}
	
	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$pdf->company = $migEmpMastObj->getCompanyName($_SESSION['company_code']);
	$pdf->brnCode = $_GET["empBrnCode"];

	//For Active Employee
	$qryListEmp = "Select ytdHist.compCode, pdYear, ytdHist.empNo, empLastName, empFirstName, empMidName, empBrnCode, empTin, empTeu, teuAmt, empWageTag, empPayType, dateHired, dateResigned,YtdGross, YtdTaxable, YtdGovDed, YtdTax,Ytd13NBonus,
					YTd13NAdvance, YtdTx13NBonus, YtdBasic, sprtAdvance, empPayGrp
					from tblYtdDataHist ytdHist, tblEmpMast empMast, tblTeu
					where ytdHist.compCode='".$_SESSION["company_code"]."' and empMast.compCode='".$_SESSION["company_code"]."'
					and ytdHist.empNo = empMast.empNo ".($_GET["empBrnCode"]==0?"":"and empBrnCode='".$_GET["empBrnCode"]."'")."
					and empmast.empTeu = tblTeu.teuCode
					order by empLastName";
					
					
	$resListEmp = $migEmpMastObj->execQry($qryListEmp);
	$arrListEmp = $migEmpMastObj->getArrRes($resListEmp);
	if(count($arrListEmp)>0)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $migEmpMastObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContentDetails($arrListEmp);
	}
	$pdf->Output();
?>



