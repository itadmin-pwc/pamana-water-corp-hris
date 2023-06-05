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
				
				$this->Cell(20,5,'EMP. NO.','1','','C');
				$this->Cell(50,5,'EMP. NAME','1','','C');
				$this->Cell(20,5,'EFF. DATE','1','','C');
				$this->Cell(30,5,'MIN.WAGE AMT','1','','C');
				$this->Cell(30,5,'> MIN.WAGE AMN','1','','C');
				$this->Cell(35,5,'GOV.AMT.MIN.WAGE','1','','C');
				$this->Cell(35,5,'> GOV.AMT.MIN.WAGE','1','','C');
				$this->Cell(30,5,'CURR.YTD TAX','1','','C');
				$this->Cell(30,5,'COMP.YTD TAX','1','','C');
				$this->Cell(40,5,'TAX REFUND','1','1','C');
				
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
			$this->SetFont('Arial','','8'); 
			$empCount = 1;
			foreach($arrEmpList as $arrEmpList_val)
			{
				
				
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
				$taxableEarningsMinWage = $taxableEarningsGreMinWage = $expectedYtdTaxable = $expectedT  = $empTaxDue = $taxComputed = $empTaxRefund =  $govtContMinWage = $govtContAbvWage = 0;
				foreach($arrPaySumHist as $arrPaySumHist_val)
				{
					if(date("m/d/Y", strtotime($arrPaySumHist_val['pdToDate'])) < date("m/d/Y", strtotime($arrEmpList_val["empEffectivityDate"])))
						$taxableEarningsMinWage+=$arrPaySumHist_val["taxableEarnings"];
					
					
					if(date("m/d/Y", strtotime($arrPaySumHist_val['pdToDate'])) > date("m/d/Y", strtotime($arrEmpList_val["empEffectivityDate"])))
						$taxableEarningsGreMinWage+=$arrPaySumHist_val["taxableEarnings"];
					
					if(($arrPaySumHist_val['pdNumber']%2) == 0)
					{
						$arrMtdGovtHist = $this->getMtdGovtHist($arrEmpList_val["empNo"], date("m", strtotime($arrPaySumHist_val["pdPayable"])));
							
						if(strtotime($arrPaySumHist_val['pdToDate']) < strtotime($arrEmpList_val["empEffectivityDate"]))
							$govtContMinWage+=$arrMtdGovtHist["govtCont"];
						
						if(strtotime($arrPaySumHist_val['pdToDate']) > strtotime($arrEmpList_val["empEffectivityDate"]))
							$govtContAbvWage+=$arrMtdGovtHist["govtCont"];
					}
				}
				
				$this->Cell(20,5,$arrEmpList_val["empNo"],'1','','C');
				$this->Cell(50,5,$arrEmpList_val["empLastName"].", ".$arrEmpList_val["empFirstName"]." ".$arrEmpList_val["empMidName"][0],'1','','R');
				$this->Cell(20,5,date("m/d/Y", strtotime($arrEmpList_val["empEffectivityDate"])),'1','','C');
				$this->Cell(30,5,number_format($taxableEarningsMinWage,2),'1','','R');
				$this->Cell(30,5,number_format($taxableEarningsGreMinWage,2),'1','0','R');
				
				$this->Cell(35,5,number_format($govtContMinWage,2),'1','','R');
				$this->Cell(35,5,number_format($govtContAbvWage,2),'1','0','R');
				
				$arrYtdDataHist = $this->getYtdDataHist($arrEmpList_val["empNo"]);
				$expectedYtdTaxable = ($arrYtdDataHist["YtdTaxable"] - $taxableEarningsMinWage);
				$expectedT = ($expectedYtdTaxable - $govtContAbvWage);
				$empTaxDue = $expectedT -  $arrYtdDataHist["teuAmt"];
				$taxComputed = $this->getAnnualTax($empTaxDue);
				$empTaxRefund = $arrYtdDataHist["YtdTax"] -  $taxComputed;
				
				$this->Cell(30,5,number_format($arrYtdDataHist["YtdTax"],2),'1','','R');
				$this->Cell(30,5,number_format($taxComputed,2),'1','0','R');
				$this->Cell(40,5,number_format(($arrYtdDataHist["YtdTax"]-$taxComputed),2),'1','1','R');
				
				$empCount++;
			}
			
			
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Arial','',8);



			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
		
	}
	
	
	$pdf = new PDF('L', 'mm', 'LEGAL');
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


