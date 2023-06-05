<?
class AnnualProcObj extends commonObj {
	
	var $get;//method
	
	var $session;//session variables
	
	/**
	 * pass all the get variables and session variables 
	 *
	 * @param string $method
	 * @param array variable  $sessionVars
	 */
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	######################################################BEGIN OF EARNINGS##################################################################
	function getAnnualDate() {
		$qryAnnulaDate="Select annualDate from tblCompany where compCode={$this->session['company_code']}";
		return $this->getSqlAssoc($this->execQry($qryAnnulaDate));
	}
	function getCompAnnDate($compCode)
	{
		$qrygetCompAnnDate = "Select * from tblCompany where compCode='".$compCode."'";
		$resgetCompAnnDate = $this->execQry($qrygetCompAnnDate);
		$rowgetCompAnnDate = $this->getSqlAssoc($resgetCompAnnDate);
		
		return $rowgetCompAnnDate["annualDate"];
	}
	
	function getTaxExemption($empTeu)
	{
		$qryGetTaxExempt = "Select teuAmt from tblTeu where teuCode='".$empTeu."'";
		$resGetTaxExempt = $this->execQry($qryGetTaxExempt);
		$rowGetTaxExempt = $this->getSqlAssoc($resGetTaxExempt);
		return $rowGetTaxExempt['teuAmt'];
	}
	
	function getAnnualTax($taxInc)
	{
		$qrycomputeWithTax = "Select * from tblAnnTax where $taxInc between txLowLimit and txUpLimit";
		$rescomputeWithTax = $this->execQry($qrycomputeWithTax);
		$rowcomputeWithTax = $this->getSqlAssoc($rescomputeWithTax);
		$compTax = ((($taxInc-$rowcomputeWithTax["txLowLimit"])*$rowcomputeWithTax["txAddPcent"])+$rowcomputeWithTax["txFixdAmt"]);
		
		return (float)$compTax;
	}
	
	private function computeAnnTaxRegEmp($empNo,$empTeu)
	{
		$pdYear = "2009";
		$prev_gross_income =  0;
		$curr_gross_income = 0;
		$taxableAmount = 0;
		$curr_taxDue = 0;
		$prev_wtax = 0;
		$curr_wtax = 0;
		$annualTax = 0;
		
		$qryCompAnnTaxRegEmp = "Select    *
								from   tblYtdDataHist
								where    (compCode = '".$this->session['company_code']."') and (pdYear = '".$pdYear."') and (empNo = '".$empNo."')";
		$rsCompAnnTaxRegEmp = $this->execQry($qryCompAnnTaxRegEmp);
		$rowCompAnnTaxRegEmp = $this->getSqlAssoc($rsCompAnnTaxRegEmp);
	
		$qrygetPrevEmplr = "Select * from tblPrevEmployer where empNo='".$empNo."' and yearCd='".$pdYear."' and compCode='".$this->session['company_code']."' and prevStat='A'";
		$resgetPrevEmplr = $this->execQry($qrygetPrevEmplr);
		while($rowgetPrevEmplr = mysql_fetch_array($resgetPrevEmplr))
		{
			$prev_gross_income+= (float) ($rowgetPrevEmplr["prevEarnings"]!=0?$rowgetPrevEmplr["prevEarnings"]:0);
			$curr_gross_income+= (float) ($rowCompAnnTaxRegEmp["YtdTaxable"]!=0?$rowCompAnnTaxRegEmp["YtdTaxable"]:0);
		}
			
		$taxableAmount = $prev_gross_income + $curr_gross_income;
		
		$equiTeu = $this->getTaxExemption($empTeu);
		
		$taxableAmount = $taxableAmount - $equiTeu;
		
		$curr_taxDue = $this->getAnnualTax($taxableAmount);
		
		$prev_wtax = (float) ($rowgetPrevEmplr["prevTaxes"]!=0?$rowgetPrevEmplr["prevTaxes"]:0);
		$curr_wtax = (float) ($rowCompAnnTaxRegEmp["YtdTax"]!=0?$rowCompAnnTaxRegEmp["YtdTax"]:0);
		
		$annualTax = $curr_taxDue - $curr_wtax;
		$annualTax = sprintf("%01.2f",$annualTax);
		
		//echo $empNo."\nPrevious Gross Income=".$prev_gross_income ."\nCurrent Gross Income".$curr_gross_income."\nTax Exemption =".$equiTeu."\nTaxable Amount = ".$taxableAmount."\nTax Due = ".$curr_taxDue."\nCurrent Tax=".$curr_wtax."\nOver/Under=".$annualTax."\n\n";
		
		
		return (float) $annualTax;
	}
	
	function getCompName($compCode)
	{
		$qryCompName = "Select * from tblCompany where compCode='".$compCode."'";
		$rsCompName = $this->execQry($qryCompName);
		return $this->getSqlAssoc($rsCompName);
	}
	
	public function mainProcRegPayroll(){
		
		$pdYear = "2009";
		
		$compAnnDate = date("m/d/Y",strtotime($this->getCompAnnDate($this->session['company_code'])));
		$currDate = "01/01/2011";
		//$currDate = date("m/d/Y");
		
		if(strtotime($compAnnDate)==strtotime($currDate))
		{
			
			$qrygetEmp = 	"Select ytdData.compCode,ytdData.pdYear,ytdData.empNo,ytdData.YtdGross,
							ytdData.YtdTaxable,ytdData.YtdGovDed,ytdData.YtdTax,ytdData.payGrp,empTeu 
							from tblYtdDataHist ytdData 
							left join tblEmpMast empMast
							ON ytdData.empNo=empMast.empNo
							where ytdData.compCode='{$this->session['company_code']}'
							and pdYear='".$pdYear."'";
							
			$rsgetEmp = $this->execQry($qrygetEmp);
			
			if(mysql_num_rows($rsgetEmp)>=1)
			{
				$rowgetEmp = $this->getArrRes($rsgetEmp);
				foreach ((array)$rowgetEmp as $arrgetEmp)
				{//foreach for post adjustment and others
					$EmpWithTax = $this->computeAnnTaxRegEmp($arrgetEmp["empNo"],$arrgetEmp["empTeu"]);
				
					/*ISAMA SA PROCESS NA KAPAG NA POST NA, ung Prev Status sa tblPrevEmployer ehhhh, magiging POSTED*/
				
				}//end foreach for post adjustment and others
			}	
			
		}
		
		
		/*
		$qryUpdateEarnTag = "UPDATE tblPayPeriod SET pdEarningsTag = 'Y' 
							 WHERE compCode = '{$this->session['company_code']}'
							 AND payGrp = '{$this->session['pay_group']}'
							 AND payCat = '{$this->session['pay_category']}'
							 AND pdYear = '{$this->get['pdYear']}'
							 AND pdNumber = '{$this->get['pdNum']}'";
		if($Trns){
			$Trns = $this->execQry($qryUpdateEarnTag);
		}		
		
		*/
		if(!$Trns){
			$Trns = $this->rollbackTran();//rollback regular payroll transaction
			return false;
		}
		else{
			$Trns = $this->commitTran();//commit regular payroll transaction
			return true;	
		}
	}
}



?>