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
				
				$this->SetFont('Arial','','7'); 
				$this->Cell(50,5,"Run Date: " . $newdate, 0);
				$this->Cell(80,5,$this->company." - Tax Refund Computation" ,'0','','C');
				$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
				$this->Ln();
				
				$this->Ln();
				
				
			
		}	
		
		function getYtdDataHist($empNo)
		{
			$qryEmpAllow = "SELECT     ytdHist.empNo, empMast.empTeu, tblTeu.teuAmt, ytdHist.pdYear, ytdHist.YtdGross, ytdHist.YtdTaxable, ytdHist.YtdGovDed, ytdHist.YtdTax, 
							ytdHist.YtdNonTaxAllow, ytdHist.YtdBasic, ytdHist.sprtAdvance, ytdHist.basicReclass, ytdHist.allowReClass, ytdHist.Ytd13NBonus, 
							ytdHist.sprtAdvance AS Expr1, ytdHist.YTd13NAdvance
							FROM         tblYtdDataHist_Year2010 ytdHist INNER JOIN
							tblEmpMast empMast ON ytdHist.empNo = empMast.empNo INNER JOIN
							tblTeu ON empMast.empTeu = tblTeu.teuCode
							WHERE     (ytdHist.empNo = '".$empNo."') order by pdYear";
			
		
			
			$resEmpAllow = $this->execQry($qryEmpAllow);
			return $this->getSqlAssoc($resEmpAllow);
		}
		
		
		private function getAnnualTax($taxInc)
		{
			$qrycomputeWithTax = "Select * from tblAnnTax where $taxInc between txLowLimit and txUpLimit";
			$rescomputeWithTax = $this->execQry($qrycomputeWithTax);
			$rowcomputeWithTax = $this->getSqlAssoc($rescomputeWithTax);
			$compTax = ((($taxInc-$rowcomputeWithTax["txLowLimit"])*$rowcomputeWithTax["txAddPcent"])+$rowcomputeWithTax["txFixdAmt"]);
			
			return sprintf("%01.2f", $compTax);
		}
		
		private function getMtdGovtHist($empNo, $empPdMonth)
		{
			$qryMtdGovtHist = "Select (sssEmp + phicEmp + hdmfEmp) as govtCont from tblMtdGovtHist where empNo='".$empNo."' and pdYear='2010' and pdMonth='".$empPdMonth."'";
			$resMtdGovtHist= $this->execQry($qryMtdGovtHist);
			return $this->getSqlAssoc($resMtdGovtHist);
		}
		
		function displayContentDetails($arrEmpList)
		{
			$this->Ln();
			$this->SetFont('Arial','','08'); $empCount = 1;
			foreach($arrEmpList as $arrEmpList_val)
			{
				$this->SetFont('Arial','B','08'); $this->Cell(47,5,$empCount.". ".$arrEmpList_val["empNo"]."=".$arrEmpList_val["empLastName"].", ".$arrEmpList_val["empFirstName"]." ".$arrEmpList_val["empMidName"][0]." : Effectivity Date : ".date("m/d/Y", strtotime($arrEmpList_val["empEffectivityDate"])),'','1','L');
				$this->SetFont('Arial','','08'); //Display Details
				$this->Cell(60,5,'PAY PERIOD','1','','L');
				$this->Cell(35,5,'MIN. WAGE AMOUNT','1','','C');
				$this->Cell(35,5,'> MIN. WAGE AMOUNT','1','0','C');
				$this->Cell(35,5,'GOV. AMT. MIN. WAGE','1','','C');
				$this->Cell(35,5,'> GOV. AMT. MIN. WAGE','1','1','C');
				
				//$this->Cell(100,5,'COMPUTATION OF TAX','1','1','C');
				
				//Display Payroll Summary Hist
				$qryPaySumHist = "SELECT     paySumHist.empNo, paySumHist.pdYear, paySumHist.pdNumber, payPeriod.pdPayable,  payPeriod.pdFrmDate, payPeriod.pdToDate, paySumHist.empBrnCode, paySumHist.grossEarnings, 
									paySumHist.taxableEarnings, paySumHist.taxWitheld
									FROM         tblPayrollSummaryHist paySumHist INNER JOIN
									tblPayPeriod payPeriod ON paySumHist.pdNumber = payPeriod.pdNumber AND paySumHist.payGrp = payPeriod.payGrp AND 
									paySumHist.payCat = payPeriod.payCat
									WHERE     (paySumHist.empNo = '".$arrEmpList_val["empNo"]."') AND 
									(paySumHist.pdYear = '2010') AND (payPeriod.pdYear = '2010')
									  order by paySumHist.pdNumber";
				$resPaySumHist = $this->execQry($qryPaySumHist);
				$arrPaySumHist= $this->getArrRes($resPaySumHist);
				$taxableEarningsMinWage = $taxableEarningsGreMinWage = $expectedYtdTaxable = $expectedT  = $empTaxDue = $taxComputed = $empTaxRefund = $govtContMinWage = $govtContAbvWage = 0;
				foreach($arrPaySumHist as $arrPaySumHist_val)
				{
					$this->Cell(60,5,date("m/d/Y", strtotime($arrPaySumHist_val["pdPayable"]))." - (".date("m/d/Y", strtotime($arrPaySumHist_val["pdFrmDate"]))." - ".date("m/d/Y", strtotime($arrPaySumHist_val["pdToDate"])).")",'1','','L');
					
					if(strtotime($arrPaySumHist_val['pdToDate']) < strtotime($arrEmpList_val["empEffectivityDate"]))
					{
						$this->Cell(35,5,number_format($arrPaySumHist_val["taxableEarnings"],2),'1','','R');
						$taxableEarningsMinWage+=$arrPaySumHist_val["taxableEarnings"];
					}
					else
					{
						$this->Cell(35,5,'','1','','R');
					}
					
					if(strtotime($arrPaySumHist_val['pdToDate']) > strtotime($arrEmpList_val["empEffectivityDate"]))
					{
						$this->Cell(35,5,number_format($arrPaySumHist_val["taxableEarnings"],2),'1','0','R');
						$taxableEarningsGreMinWage+=$arrPaySumHist_val["taxableEarnings"];
					}
					else
					{
						$this->Cell(35,5,'','1','0','R');
					}	
					
					if(($arrPaySumHist_val['pdNumber']%2) == 0)
					{
						$arrMtdGovtHist = $this->getMtdGovtHist($arrEmpList_val["empNo"], date("m", strtotime($arrPaySumHist_val["pdPayable"])));
							
						if(strtotime($arrPaySumHist_val['pdToDate']) < strtotime($arrEmpList_val["empEffectivityDate"]))
						{
							$this->Cell(35,5,number_format($arrMtdGovtHist["govtCont"],2),'1','0','R');
							$govtContMinWage+=$arrMtdGovtHist["govtCont"];
						}
						else
							$this->Cell(35,5,'','1','0','R');
						
						if(strtotime($arrPaySumHist_val['pdToDate']) > strtotime($arrEmpList_val["empEffectivityDate"]))
						{
							$this->Cell(35,5,number_format($arrMtdGovtHist["govtCont"],2),'1','1','R');
							$govtContAbvWage+=$arrMtdGovtHist["govtCont"];
						}
						else
							$this->Cell(35,5,'','1','1','R');
					
					}
					else
					{
						$this->Cell(35,5,'','1','0','R');
						$this->Cell(35,5,'','1','1','R');
					}	
					
				}
				$this->SetFont('Arial','B','08'); $this->Cell(60,7,'SUM','1','','L');
				$this->Cell(35,7,number_format($taxableEarningsMinWage,2),'1','','R');
				$this->Cell(35,7,number_format($taxableEarningsGreMinWage,2),'1','0','R');
				$this->Cell(35,7,number_format($govtContMinWage,2),'1','','R');
				$this->Cell(35,7,number_format($govtContAbvWage,2),'1','1','R');
				$this->SetFont('Arial','','08'); $this->Cell(65,7,'TAX COMPUTATION','','1','L');
				
				$arrYtdDataHist = $this->getYtdDataHist($arrEmpList_val["empNo"]);
				
				
				$expectedYtdTaxable = ($arrYtdDataHist["YtdTaxable"] - $taxableEarningsMinWage);
				$this->Cell(70,7,'Expected YTD Taxable = ','','','R');
				$this->Cell(70,7,number_format($arrYtdDataHist["YtdTaxable"],2)." - ".number_format($taxableEarningsMinWage,2) . " = ".number_format($expectedYtdTaxable,2),'','1','R');
				
				
				$expectedT = ($expectedYtdTaxable - $govtContAbvWage);
				
				$this->Cell(70,7,' ','','','R');
				$this->Cell(70,7,$expectedYtdTaxable." - ".$govtContAbvWage." = ".number_format($expectedT,2) ,'','1','R');
				
				$empTaxDue = $expectedT -  $arrYtdDataHist["teuAmt"];
				
				$this->Cell(70,7,'Tax Due = ','','','R');
				$this->Cell(70,7,$expectedT." - ".$arrYtdDataHist["teuAmt"]." = ".number_format($empTaxDue,2) ,'','1','R');
				
				$taxComputed = $this->getAnnualTax($empTaxDue);
				
				$this->Cell(70,7,'Comp. With. Tax = ','','','R');
				$this->Cell(70,7,number_format($taxComputed,2) ,'','1','R');
				
				$this->Ln();
				
				$this->Cell(70,7,'Curr. YTD Tax = ','','','R');
				$this->Cell(70,7,number_format($arrYtdDataHist["YtdTax"],2) ,'','1','R');
				
				$this->Cell(70,7,'Comp. With. Tax = ','','','R');
				$this->Cell(70,7," - ".number_format($taxComputed,2) ,'','1','R');
				
				$empTaxRefund = $arrYtdDataHist["YtdTax"] -  $taxComputed;
				
				$this->Cell(70,7,'Tax Refund = ','','','R');
				$this->Cell(70,7,number_format($empTaxRefund,2) ,'','1','R');
				
				$this->AddPage();
				
				$empCount++;
			}
			
			
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
	
	
	$pdf = new PDF('P', 'mm', 'LETTER');
	$pdf->company = $migEmpMastObj->getCompanyName($_SESSION['company_code']);
	$pdf->brnCode = $_GET["empBrnCode"];
	//List of New Employees
	
	if ($_SESSION['pay_group']==1) {
		$date = '10/19/2010';
	} else {
		$date = '10/24/2010';
	}
	
	
	
	//For Active Employee
	$qryListEmp = "Select * from tblEmpAffected_TaxRefund where empNo is not null order by empLastName";
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
	
	
	
	//For Transfer Employees
	/*$qryListEmp = "Select * from tblEmpMast where compCode='".$_SESSION["company_code"]."' 
				   and empNo in (200001838,320000242,320000404,140001638,140000090) order by empLastName";

*/

	/*$resListEmp = $migEmpMastObj->execQry($qryListEmp);
	$arrListEmp = $migEmpMastObj->getArrRes($resListEmp);
	if(count($arrListEmp)>0)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $migEmpMastObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContentDetails($arrListEmp);
	}
	$pdf->Output();*/
?>


