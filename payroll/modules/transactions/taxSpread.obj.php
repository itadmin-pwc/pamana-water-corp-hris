<?
class taxSpreadobj extends commonObj {
	var $get;
	
	var $session;
	var $arrBasicAdj = array();	
	function getEmpList() {
		$arrPd = $this->currPayPd();
		$this->getBasicAdj();
		$qryEmpList = "
							SELECT     tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblTeu.teuAmt, tblEmpMast.empPrevTag, 
												  tblBranch.minWage, tblPAF_PayrollRelatedhist.old_empDrate, tblPAF_PayrollRelatedhist.new_empDrate 
							FROM         tblEmpMast INNER JOIN
												  tblTeu ON tblEmpMast.empTeu = tblTeu.teuCode INNER JOIN
												  tblBranch ON tblEmpMast.compCode = tblBranch.compCode AND tblEmpMast.empBrnCode = tblBranch.brnCode INNER JOIN
												  tblPAF_PayrollRelatedhist ON tblEmpMast.compCode = tblPAF_PayrollRelatedhist.compCode AND 
												  tblEmpMast.empNo = tblPAF_PayrollRelatedhist.empNo AND tblPAF_PayrollRelatedhist.dateupdated BETWEEN '4/19/2010' AND 
												  '".$this->get['pdToDate']."' AND tblPAF_PayrollRelatedhist.old_empDrate <= tblBranch.minWage AND 
												  tblPAF_PayrollRelatedhist.new_empDrate > tblBranch.minWage
							WHERE     (tblEmpMast.empPayGrp = '".$_SESSION['pay_group']."')  AND tblEmpMast.compCode='".$_SESSION['company_code']."' AND empStat IN ('RG','CN','PR') AND tblEmpMast.empNo Not IN (Select empNo from tblEmpTax where compCode='{$_SESSION['company_code']}')
							group by tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblTeu.teuAmt, tblEmpMast.empPrevTag, 
												  tblBranch.minWage, tblPAF_PayrollRelatedhist.old_empDrate, tblPAF_PayrollRelatedhist.new_empDrate 
							ORDER BY tblEmpMast.empLastName, tblEmpMast.empFirstName		";
			
		$rsGEmpList = $this->execQry($qryEmpList);
		return $this->getArrRes($rsGEmpList);
	}
	function currPayPd() {
		$andPayPeriod = "AND payGrp = '{$_SESSION['pay_group']}'
						 AND payCat = '{$_SESSION['pay_category']}'
						 AND pdStat IN ('O','') ";
		$arrPayPeriod = $this->getPayPeriod($_SESSION['company_code'],$andPayPeriod);
		$this->get = $arrPayPeriod;
	}

	private function getAnnualTax($taxInc)
	{
		$qrycomputeWithTax = "Select * from tblAnnTax where $taxInc between txLowLimit and txUpLimit";
		$rescomputeWithTax = $this->execQry($qrycomputeWithTax);
		$rowcomputeWithTax = $this->getSqlAssoc($rescomputeWithTax);
		$compTax = ((($taxInc-$rowcomputeWithTax["txLowLimit"])*$rowcomputeWithTax["txAddPcent"])+$rowcomputeWithTax["txFixdAmt"]);
		return (float)$compTax;
	}
	
	private function getEmpYtdDataHist($empNo)
	{
		$qrygetEmpYtdDataHist = "Select * from tblYtdDataHist where empNo='".$empNo."' and pdYear='".$this->get['pdYear']."' and compCode='".$_SESSION["company_code"]."'";
		$rsgetEmpYtdDataHist = $this->execQry($qrygetEmpYtdDataHist);
		
		return $this->getSqlAssoc($rsgetEmpYtdDataHist);
	}
	
	private function getPrevEmplr($empNo,$prevfield)
	{
		 if($prevfield!="")
		 {
		 	$qryStat = "sum($prevfield) as $prevfield";
			$prevfield = $prevfield;
		 }
		 else
		 {
		 	$qryStat = "count($empNo) as cntEmp";
			$prevfield = "cntEmp";
		 }
		 
		 $qrygetPrevEmplr = "Select ".$qryStat." from tblPrevEmployer where empNo='".$empNo."' and yearCd='".$this->get['pdYear']."' and compCode='".$this->session['company_code']."' and prevStat='A'";
		 $resgetPrevEmplr = $this->execQry($qrygetPrevEmplr);
		 $rowgetPrevEmplr = $this->getSqlAssoc($resgetPrevEmplr);
		 return $rowgetPrevEmplr[$prevfield];
	}	
	
	function computeWithTax($empNo,$empTeu,$prevTag)
	{
		$empPrevTag = "";
		$empMinTag = "";
		$empPrevEarnings = 0;
		$empPrevTaxes = 0;
		$estEarn = 0;
		$netTaxable = 0;
		$estTaxYear = 0;
		$taxDue = 0;
		$taxPeriod = 0;
		
		$basicPay = (float)$minBasicPay;
		
		//Get the tblYtdDataHist of the Employee
		$arrYtdDataHist = $this->getEmpYtdDataHist($empNo);
		
		//Get the Previous Employe Tag / Mimimum Wage Earnner
		
		if($prevTag=='Y')
		{
			//Get Previous Employer Data to tblPrevEmployer
			$empPrevEarnings = $this->getPrevEmplr($empNo,'prevEarnings');
			$empPrevTaxes = $this->getPrevEmplr($empNo,'prevTaxes');
		}
		else
		{
			$empPrevEarnings = 0;
			$empPrevTaxes = 0;
		}
		
		//echo 	$empNo."==".$gross_Taxable."\n";
		//Estimate the Total Taxable Earnings for the Year
		$adjBasic = $this->empBasic($empNo);
		//echo "$adjBasic <br />";
		$estEarn = (float) $arrYtdDataHist["YtdTaxable"] + (float)$adjBasic + (float)$empPrevEarnings -  (float) $arrYtdDataHist["YtdGovDed"];
		//echo 	$empNo."==".$estEarn."\n";
		$estEarn = (float) $estEarn / ($this->get['pdNumber']);
		//echo 	$empNo."==".$estEarn."\n";
		$estEarn = (float) $estEarn * 24 ;
			//echo 	$empNo."==".$estEarn."\n";
		
		
		//Compute for the Net Taxable Earnings
		$netTaxable = (float)$estEarn - (float)$empTeu;
		//echo 	$empNo."==".$netTaxable."\n";
		
		//Compute the Estimated Tax using the Annual Tax Table
		$estTaxYear = $this->getAnnualTax($netTaxable);
		//echo 	$empNo."==".$estTaxYear."\n";
		
		//Compute Taxes
		$taxDue = ($estTaxYear / 24) * ($this->get['pdNumber']);
		//echo 	$empNo."==".$taxDue."\n";
		
		$taxPeriod = $taxDue -  $arrYtdDataHist["YtdTax"] - $empPrevTaxes;
		//echo 	$taxPeriod."\n";
		
		$taxPeriod = ($taxPeriod<0?0:$taxPeriod);
		return sprintf("%01.2f", $taxPeriod) ;
		
	}
	
	function SaveEmpTax() {
		$arrEmpList = $_GET['empList'];
		$arrEmpList = explode(",",$arrEmpList);
		
		$qryUpdateEarn = "";
		for($i=0; $i<count($arrEmpList); $i++) {
			
			$empNo = $arrEmpList[$i];
			$empTax = str_replace(",","",$_GET['txt'.$arrEmpList[$i]]);
			$qryInsert .= "Insert into tblEmpTax (compCode,empNo,wtax) values ('{$_SESSION['company_code']}','$empNo','$empTax');\n";
		}
		$Trns = $this->beginTran();
		if($Trns){
			$Trns = $this->execQry($qryInsert);
		}

		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}
	}	
	function getBasicAdj() {
		$sqlbasic = "SELECT  tblEarnTranDtl.trnAmount, tblEarnTranDtl.empNo
FROM         tblEarnTranHeader INNER JOIN
                      tblEarnTranDtl ON tblEarnTranHeader.compCode = tblEarnTranDtl.compCode AND tblEarnTranHeader.refNo = tblEarnTranDtl.refNo where tblEarnTranHeader.compCode='{$_SESSION['company_code']}' AND tblEarnTranDtl.earnStat='A' AND pdYear='{$this->get['pdYear']}' AND pdNumber='{$this->get['pdNumber']}' AND tblEarnTranDtl.trnCode='0801'";
	  $this->arrBasicAdj = $this->getArrRes($this->execQry($sqlbasic));
	}
	function empBasic($empNo) {
		$amount = 0;
		foreach($this->arrBasicAdj as $val) {
			if ($val['empNo']==$empNo) {
				$amount = $val['trnAmount'];
			}
		}
		return $amount;
	}
}
?>