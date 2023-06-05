<?
class reg13thMonthProcObj extends commonObj 
{
	
	var $get;//method
	
	var $session;//session variables
	var $ResidualEarningsList = array();
	var $postAdjustmentOthers = array();
	var $arrPrevEmpEarnings = array();
	
	/**
	 * pass all the get variables and session variables 
	 *
	 * @param string $method
	 * @param array variable  $sessionVars
	 */
	 
	function __construct($method,$sessionVars)
	{
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	################################################################################################################################################
	
	/*COMMON FUNCTIONS USED*/
		public function checkPeriodTags()
		{
			$qryChkPeriodTags = "SELECT computeTag FROM tbl13thCheck
								WHERE compCode = '{$this->session['company_code']}'
								AND payGrp = '{$this->session['pay_group']}'
								AND payCat = '{$this->session['pay_category']}'
								AND pdyear='".date('Y')."'";
			$resChkPeriodTags = $this->execQryI($qryChkPeriodTags) ;
			return $this->getSqlAssocI($resChkPeriodTags);
		}
	
		private function getPreviousCutOff($pdYear, $pdNum)
		{
			if($pdNum == 1)
			{
				$pdNum = '24';
				$pdYear = $pdYear - 1;
			}
			else
			{
				$pdNum = $pdNum - 1;
			}
			
			$qryPrevCutOff = "Select * from tblPayperiod where 
								compCode='".$_SESSION["company_code"]."'
								and payGrp='".$_SESSION["pay_group"]."'
								and payCat='".$_SESSION["pay_category"]."' 
								and pdYear='".$pdYear."' 
								and pdNumber='".$pdNum."'
								and pdProcessTag='Y'";
			
			$resPrevCutOff = $this->execQryI($qryPrevCutOff);
			
			return $this->getSqlAssocI($resPrevCutOff);
		}
	
		private function getTrnTaxCode($compCode,$trnCode,$type)
		{
			$trnTaxCd = "SELECT trnTaxCd FROM tblPayTransType 
						 WHERE compCode = '{$compCode}'
						 AND trnCat = '{$type}' ";					
			$trnTaxCd .= "AND trnCode = '".trim($trnCode)."' ";
			$resTaxCd = $this->execQryI($trnTaxCd);
			return  $this->getSqlAssocI($resTaxCd);			
		}
	
		private function getTaxExemption($empTeu)
		{
			$qryGetTaxExempt = "Select teuAmt from tblTeu where teuCode='".$empTeu."'";
			$resGetTaxExempt = $this->execQryI($qryGetTaxExempt);
			$rowGetTaxExempt = $this->getSqlAssocI($resGetTaxExempt);
			return $rowGetTaxExempt['teuAmt'];
		}
		
		private function getAnnualTax($taxInc)
		{
			$qrycomputeWithTax = "Select * from tblAnnTax where $taxInc between txLowLimit and txUpLimit";
			$rescomputeWithTax = $this->execQryI($qrycomputeWithTax);
			$rowcomputeWithTax = $this->getSqlAssocI($rescomputeWithTax);
			$compTax = ((($taxInc-$rowcomputeWithTax["txLowLimit"])*$rowcomputeWithTax["txAddPcent"])+$rowcomputeWithTax["txFixdAmt"]);
			return (float)$compTax;
		}
	/*END OF COMMON FUNCTIONS*/
	
	/*RE - PROCESS THE PAYROLL*/
		function reProcreg13thMonth()
		{
			$TrnsA = $this->beginTranI();
		
			$qryDeleEan = "DELETE FROM tblEarnings 
						   WHERE compCode = '{$this->session['company_code']}'
						   AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
										) ";
			if($TrnsA)
				$TrnsA = $this->execQryI($qryDeleEan);
			

			$qryDeleDeductions = "DELETE FROM tblDeductions 
							WHERE compCode = '{$this->session['company_code']}'
							AND pdYear = '{$this->get['pdYear']}'
							AND pdNumber = '{$this->get['pdNum']}'
							AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
							  )";
			if($TrnsA)
				$TrnsA = $this->execQryI($qryDeleDeductions);
			


			$qryDeleYtdData = "DELETE FROM tblYtdData 
							 WHERE compCode = '{$this->session['company_code']}'
							 AND pdYear = '{$this->get['pdYear']}'
							 AND pdNumber = '{$this->get['pdNum']}'
							 AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
										  )";
			if($TrnsA)
				$TrnsA = $this->execQryI($qryDeleYtdData);
			
	
			$qryDelePaySum = "DELETE FROM tblPayrollSummary 
							  WHERE compCode = '{$this->session['company_code']}'
							  AND pdYear = '{$this->get['pdYear']}'
							  AND pdNumber = '{$this->get['pdNum']}'
							  AND payGrp = '{$this->session['pay_group']}'
							  AND payCat = '{$this->session['pay_category']}'";
			if($TrnsA)
				$TrnsA = $this->execQryI($qryDelePaySum);	
			
		
			$qryDeleRcdual = "DELETE FROM tblRcdualEarnings 
							  WHERE compCode = '{$this->session['company_code']}'
							  AND pdYear = '{$this->get['pdYear']}'
							  AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
										  ) ";
			if($TrnsA)
				$TrnsA = $this->execQryI($qryDeleRcdual);	
						
			
			if(!$TrnsA)
			{
				$TrnsA = $this->rollbackTranI();//rollback regular payroll transaction
				return false;
			}
			else
			{
				$TrnsA = $this->commitTranI();//commit regular payroll transaction
				return true;	
			}
		}
	/*END OF RE - PROCESS OF PAYROLL*/
	
	
	/*RETRIEVE OF DATA*/
		function AuditCheck() 
		{
			$sqlAudit = "Select GlTag from tbl13thCheck where compCode= '{$this->session['company_code']}' AND payGrp='{$this->session['pay_group']}' AND payCat='{$this->session['pay_category']}' AND pdYear='".date('Y')."'";
			$res = $this->getSqlAssocI($this->execQryI($sqlAudit));
			
			if ($res['GlTag']=='Y') 
				return true;
			else
				return false;
		}
		
		private function getEmpList() 
		{
			 
				$date = $this->get['pdYear'].'-11-30';
			 
			
			 $sqlEmpList = "SELECT tblEmpMast.empNo, tblEmpMast.empTeu, tblEmpMast.empWageTag, tblEmpMast.empPrevTag, tblYtdDataHist.Ytd13NBonus,tblYtdDataHist.YTd13NAdvance, tblYtdDataHist.YtdTx13NBonus, tblYtdDataHist.YtdBasic, tblYtdDataHist.sprtAdvance,empLocCode,empBrnCode,empBankCd,empDiv,empDepCode,empSecCode,basicReclass,allowReClass FROM tblEmpMast INNER JOIN tblYtdDataHist ON tblEmpMast.compCode = tblYtdDataHist.compCode AND tblEmpMast.empNo = tblYtdDataHist.empNo where tblEmpMast.compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN') AND pdYear='{$this->get['pdYear']}' AND datehired<='$date' and tblEmpMast.empNo not in (010001423,010001425,010001426,010001427,010001428,010001459,010001460,010001461,010001462,010000044,010000045,010001458) ";
			return $this->getArrResI($this->execQryI($sqlEmpList));
		}
		
		private function ResidualEarningsList() 
		{
			$sqlResidualEarnings = "Select * from tblRcdualEarnings where compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN') and empNo not in (010001423,010001425,010001426,010001427,010001428,010001459,010001460,010001461,010001462,010000044,010000045,010001458))";
			$this->ResidualEarningsList = $this->getArrResI($this->execQryI($sqlResidualEarnings));
		}
		
		private function getReClassAllowAdjList() 
		{
			$sqlAllowReclassList = "Select empNo, totAllowAdj, totRclsAdv, AdjAllowOther from tblAllowReclass  where compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN') and empNo not in (010001423,010001425,010001426,010001427,010001428,010001459,010001460,010001461,010001462,010000044,010000045,010001458)) AND recStat IS NULL";
			$this->ReClassAllowAdjList = $this->getArrResI($this->execQryI($sqlAllowReclassList));
		}
		
		private function getReClassBasicAdjList() 
		{
			$sqlBasicReclassList = "Select empNo, totBasicAdj, totRclsBasic, totAdjOt from tblBasicReclass where compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN') and empNo not in (010001423,010001425,010001426,010001427,010001428,010001459,010001460,010001461,010001462)) AND recStat IS NULL";
			$this->ReClassBasicAdjList = $this->getArrResI($this->execQryI($sqlBasicReclassList));
		}
		
		private function postAdjustmentOthers()
		{
			$qryPostAdjOthrs = "SELECT empNo, trnCode, SUM(trnAmount) AS sumEarnAmnt
								FROM tblEarnTranDtl 
								WHERE (compCode = '{$this->session['company_code']}') 
								  AND (payGrp = '{$this->session['pay_group']}') 
								  AND (payCat = '{$this->session['pay_category']}')
								  AND (earnStat = 'A')
								  AND (trnCode IN (Select trnCode from tblEarnTranHeader where compCode='{$_SESSION['company_code']}' and earnStat='A' 
												   AND pdYear = '{$this->get['pdYear']}'
												   AND pdNumber = '{$this->get['pdNum']}'))
								  and trnCode in ('8018', '0807')
								  AND empNo in (SELECT empNo FROM tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN') and empNo not in (010001423,010001425,010001426,010001427,010001428,010001459,010001460,010001461,010001462,010000044,010000045,010001458))
								  AND refNo in 	(Select refNo from tblEarnTranHeader where compCode='{$_SESSION['company_code']}' and earnStat='A' 
												   AND pdYear = '{$this->get['pdYear']}'
												   AND pdNumber = '{$this->get['pdNum']}')
								  GROUP BY empNo, trnCode, trnTaxCd ";
			
			$this->postAdjustmentOthers = $this->getArrResI($this->execQryI($qryPostAdjOthrs));
		}
		
		private function getEmpResidualEarnings($empNo) 
		{
			$res = array();
			foreach($this->ResidualEarningsList as $val) {
				if ($val['empNo']==$empNo) {
					$res = $val;	
				}
			}
			return $res;
		}
		
		private function getEmpAdjustment($empNo, $trnCode) 
		{
			$res = array();
			foreach($this->postAdjustmentOthers as $val) 
			{
				if (($val['empNo']==$empNo)&&($val['trnCode']==$trnCode)) 

				{
					$res = $val;	
				}
			}
		
			return $res;
		}
		
		private function getDataToYtdDataHist($empNo)
		{
			$qryGetDataToYtdHist = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,ytd.pdNumber,
								emp.empPayGrp,emp.empPayCat,ytd.YtdBasic, ytd.sprtAllow
								FROM tblYtdDataHist as ytd LEFT JOIN tblEmpMast as emp
								ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
								WHERE ytd.compCode = '{$this->session['company_code']}'
								AND  emp.empPayGrp = '{$this->session['pay_group']}'
								AND emp.empPayCat = '{$this->session['pay_category']}'
								AND ytd.pdYear = '{$this->get['pdYear']}'
								AND ytd.empNo = '{$empNo}'";
			$resGetDataToYtdHist = $this->execQryI($qryGetDataToYtdHist);	
			return 	$this->getSqlAssocI($resGetDataToYtdHist);
		}
		
		private function getEmpYtdDataHist($empNo)
		{
			$qrygetEmpYtdDataHist = "Select * from tblYtdDataHist where empNo='".$empNo."' and pdYear='".$this->get['pdYear']."' and compCode='".$_SESSION["company_code"]."'";
			$rsgetEmpYtdDataHist = $this->execQryI($qrygetEmpYtdDataHist);
			return $this->getSqlAssocI($rsgetEmpYtdDataHist);
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
			 $resgetPrevEmplr = $this->execQryI($qrygetPrevEmplr);
			 $rowgetPrevEmplr = $this->getSqlAssocI($resgetPrevEmplr);
			 
			 return $rowgetPrevEmplr[$prevfield];
		}
	/*END OF RETRIEVE OF DATA*/
	
	
	/*PROCESS*/
		private function computeWithTax($empNo,$gross_Taxable,$empTeu,$empPrevTag)
		{
			$empMinTag = "";
			$empPrevEarnings = $empPrevTaxes = $estEarn = $netTaxable = $estTaxYear = $taxDue = $taxPeriod = 0;
			
			//Get the tblYtdDataHist of the Employee
			$arrYtdDataHist = $this->getEmpYtdDataHist($empNo);
			
			//Get the Previous Employe Tag / Mimimum Wage Earnner
			
			if($empPrevTag=='Y')
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
				$estEarn = 	  (float) $gross_Taxable + (float) $arrYtdDataHist["YtdTaxable"] + (float)$empPrevEarnings -  (float) $arrYtdDataHist["YtdGovDed"] - (float)$sumGov;
				//echo $gross_Taxable."+". (float) $arrYtdDataHist["YtdTaxable"]."+". (float)$empPrevEarnings."-".(float) $arrYtdDataHist["YtdGovDed"]."-".(float)$sumGov."\n";
				//echo 	$empNo."==".$estEarn."\n";
				$estEarn = (float) $estEarn / 22;
				//echo 	$empNo."==".$estEarn."\n";
				$estEarn = (float) $estEarn * 24 ;
				//echo 	$empNo."==".$estEarn."\n";
			
			
			//Compute for the Net Taxable Earnings
			$netTaxable = (float) $estEarn - (float) $this->getTaxExemption($empTeu);
			//echo 	$empNo."==".$netTaxable."\n";
			
			//Compute the Estimated Tax using the Annual Tax Table
			$estTaxYear = $this->getAnnualTax($netTaxable);
			//echo 	$empNo."==".$estTaxYear."\n";
			
			//Compute Taxes
			$taxDue = ($estTaxYear / 24)* 22;
			//echo 	$empNo."==".$taxDue."\n";
			$taxPeriod = $taxDue -  $arrYtdDataHist["YtdTax"] - $empPrevTaxes;
			//echo 	$empNo."==".$taxPeriod."\n";
			
			$taxPeriod = ($taxPeriod<0?0:$taxPeriod);
			return sprintf("%01.2f", $taxPeriod) ;
			
		}
		
		public function mainProcreg13thMonth()
		{
			$Trns = $this->beginTranI();//begin 13th Month transaction
			
			//get current regular payroll pay period and Tag 13th month audit table
			if($Trns)
				$Trns = $this->TagAuditCheck();
			
			//Close current regular payroll pay period and open pdNumber 25
			if($Trns)
				$Trns = $this->CloseOpenRegPayRoll();
			
			//get Prev Year basic and advances
			//if($Trns)
			//	$Trns = $this->getPrevYrempBasic();
			
			$arrEmp = array();
		
			//get Residual EarningsList
			//$this->ResidualEarningsList();
			$this->getReClassAllowAdjList();
			$this->getReClassBasicAdjList();
			$this->postAdjustmentOthers();
			$this->PrevEmpEarnings();	
			
			foreach($this->getEmpList() as $valEmp) 
			{
				$RegularExcess = $Advances13th = $Regular13th = $withTax = $netsalary = $Adj_Reg13thMonth = $Adj_Advances13thMonth = 0;
					
//$arrEmpresidual = $this->getEmpResidualEarnings($valEmp['empNo']);
				$arrEmp13thMonRegAdj = $this->getEmpAdjustment($valEmp['empNo'], '0807');
				$arrEmp13thMonAdvancesAdj = $this->getEmpAdjustment($valEmp['empNo'], '8018');
				$arrPreEmpEarnings = $this->getPrevEmpEarnings($valEmp['empNo']);
				
				$Regular13th = (((float)$arrPreEmpEarnings['Basic'] + (float)$valEmp['YtdBasic'] + (float)$arrEmpresidual['rcdBasic']  - ((float)$valEmp['basicReclass']))/12)  - (float)$valEmp['Ytd13NBonus']  + $arrEmp13thMonRegAdj["sumEarnAmnt"];
					
				if ($Regular13th>80000) 
				{
					$RegularExcess = $Regular13th-80000;
					$Regular13th = 80000;
				}
				
				if ((float)$valEmp['sprtAdvance']>0 || (float)$arrEmpresidual['rcdAdvances']>0 || (float)$arrEmp13thMonAdvancesAdj["sumEarnAmnt"]>0) 
				{
					$Advances13th = (((float)+$arrPreEmpEarnings['Advances']+(float)$valEmp['sprtAdvance'] + (float)$arrEmpresidual['rcdAdvances']  + (float)$valEmp['allowReClass'])/12) - (float)$valEmp['YTd13NAdvance'] + $arrEmp13thMonAdvancesAdj["sumEarnAmnt"];
					//echo  $valEmp['empNo']."===="."((".(float)$valEmp['sprtAdvance']."+".(float)$arrEmpresidual['rcdAdvances'] ."+". (float)$valEmp['allowReClass'].")/12) - ".(float)$valEmp['YTd13NAdvance']."+".$arrEmp13thMonAdvancesAdj["sumEarnAmnt"]."\n";
				}
				
				if ($Regular13th > 0) 
				{
					if($Trns)
						$Trns = $this->writeToTblEarnings('E1',$valEmp['empNo'],'1000',$Regular13th);			
				}
	
				if ($RegularExcess>0) 
				{
					if($Trns)
						$Trns = $this->writeToTblEarnings('E1',$valEmp['empNo'],'1010',$RegularExcess);			
					
					
					$withTax = $this->computeWithTax($valEmp['empNo'],$RegularExcess,$valEmp["empTeu"],$valEmp['empPrevTag']);
					if($withTax != 0)
					{
						if($Trns)
							$Trns = $this->writeToTblDeduction($valEmp['empNo'],'5100',$withTax);				
					}
				}
			
				if ($Advances13th>0) 
				{
					if($Trns)
						$Trns = $this->writeToTblEarningsAllow('E1',$valEmp['empNo'],'1100',$Advances13th,'Y');		
				}	
			
			
				$netsalary = $Regular13th + $RegularExcess - $withTax;
				$dataToYtdHist 	   = $this->getDataToYtdDataHist($valEmp['empNo']);
				$newYtdTaxable     = (float)$dataToYtdHist['YtdTaxable'] + (float)$RegularExcess;
				$newYtdTax         = (float)$dataToYtdHist['YtdTax'] + (float)$withTax;
				
				$qryToPayrollSum = "INSERT INTO tblPayrollSummary(compCode,
																	  pdYear,
																	  pdNumber,
																	  empNo,
																	  payGrp,
																	  payCat,
																	  empLocCode,
																	  empBrnCode,
																	  empBnkCd,
																	  grossEarnings,
																	  taxableEarnings,
																	  netSalary,
																	  taxWitheld,
																	  empDivCode,
																	  empDepCode,
																	  empSecCode,
																	  sprtAllow,
																	  empMinWageTag,
																	  emp13thMonthNonTax,
																	  emp13thMonthTax,
																	  emp13thAdvances,
																	  empTeu,
																	  empYtdTaxable,
																	  empYtdTax
																	  )
																VALUES('{$this->session['company_code']}',
																	   '{$this->get['pdYear']}',
																	   '{$this->get['pdNum']}',
																	   '{$valEmp['empNo']}',
																	   '{$this->session['pay_group']}',
																	   '{$this->session['pay_category']}',
																	   '{$valEmp['empLocCode']}',
																	   '{$valEmp['empBrnCode']}',
																	   '{$valEmp['empBankCd']}',
																	   '".sprintf("%01.2f",($Regular13th+$RegularExcess))."',
																	   '".sprintf("%01.2f",$RegularExcess)."',
																	   '".sprintf("%01.2f",$netsalary)."',
																	   '".sprintf("%01.2f",$withTax)."',
																	   '{$valEmp['empDiv']}',
																	   '{$valEmp['empDepCode']}',
																	   '{$valEmp['empSecCode']}',
																	   '".sprintf("%01.2f",$Advances13th)."',
																	   '".$valEmp['empWageTag']."',
																	   '".sprintf("%01.2f",$Regular13th)."',
																	   '".sprintf("%01.2f",$RegularExcess)."',
																	   '".sprintf("%01.2f",$Advances13th)."',
																	   '".$valEmp["empTeu"]."',
																	   '".sprintf("%01.2f",$newYtdTaxable)."',
																	   '".sprintf("%01.2f",$newYtdTax)."'
																	   )";				
					
					if($Trns)
						$Trns = $this->execQryI($qryToPayrollSum);
					
							
						
					$qryToYtdData = "INSERT INTO tblYtdData(compCode,pdYear,empNo,YtdGross,YtdTaxable,YtdTax,Ytd13NBonus,Ytdtx13NBonus,YTd13NAdvance,sprtAllow,payGrp,pdNumber)
									 VALUES('{$this->session['company_code']}',
											'{$this->get['pdYear']}',
											'{$valEmp['empNo']}',
											'".sprintf("%01.2f",$Regular13th+$RegularExcess)."',
											'".sprintf("%01.2f",$RegularExcess)."',
											'".sprintf("%01.2f",$withTax)."',
											'".sprintf("%01.2f",$Regular13th)."',
											'".sprintf("%01.2f",$RegularExcess)."',
											'".sprintf("%01.2f",$Advances13th)."',
											'".sprintf("%01.2f",$Advances13th)."',
											'{$this->session['pay_group']}',
											'{$this->get['pdNum']}')";
								
					if($Trns)
						$Trns = $this->execQryI($qryToYtdData);
							
					unset($dataToYtdHist,$newYtdTaxable,$newYtdTax,$Regular13th,$RegularExcess,$RegularExcess,$netsalary,$withTax,$Advances13th,$Regular13th,$RegularExcess,$Advances13th,$newYtdTaxable,$newYtdTax);
				
			}
			
			$qryUpdateEarnTag = "UPDATE tblPayPeriod SET pdEarningsTag = 'Y' 
								 WHERE compCode = '{$this->session['company_code']}'
								 AND payGrp = '{$this->session['pay_group']}'
								 AND payCat = '{$this->session['pay_category']}'
								 AND pdYear = '{$this->get['pdYear']}'
								 AND pdNumber = '{$this->get['pdNum']}'";
			if($Trns)
				$Trns = $this->execQryI($qryUpdateEarnTag);
			
				
			if(!$Trns)
			{
				$Trns = $this->rollbackTranI();//rollback 13th Month transaction
				return false;
			}
			else
			{
				$Trns = $this->commitTranI();//commit 13th Month transaction
				return true;	
			}
		}
	/*END OF PROCESS*/
	
	
	/*INSERT / UPDATE TABLES*/
		private function TagAuditCheck() 
		{
			$sqlCheckPayPd = "Select compCode from tbl13thCheck where compCode= '{$this->session['company_code']}' AND payGrp='{$this->session['pay_group']}' AND payCat='{$this->session['pay_category']}' AND pdYear='".date('Y')."'";
			$cnt = $this->getRecCountI($this->execQryI($sqlCheckPayPd));
			if ($cnt==0) 
			{
				$resCurPaypd = $this->getOpenPeriodwil();			
				
				$sqlInsert = "INSERT INTO tbl13thCheck (compCode, payGrp, payCat, pdYear, pdNumberClosed, computeTag) VALUES ('{$this->session['company_code']}','{$this->session['pay_group']}','{$this->session['pay_category']}','".date('Y')."','".$resCurPaypd['pdNumber']."','Y')";
				return $this->execQryI($sqlInsert);
			}
			else 
			 {
				$sqlUpdate = "Update tbl13thCheck SET GlTag=NULL where compCode= '{$this->session['company_code']}' AND payGrp='{$this->session['pay_group']}' AND payCat='{$this->session['pay_category']}' AND pdYear='".date('Y')."'";
				return $this->execQryI($sqlUpdate);
			}
	
		}
		
		private function CloseOpenRegPayRoll() 
		{
			$sqlPayPd = "Update tblPayPeriod set pdStat='H' where compCode = '{$this->session['company_code']}' AND payGrp='{$this->session['pay_group']}' AND payCat='{$this->session['pay_category']}'  AND pdStat='O';
						Update tblPayPeriod set pdStat='O' where compCode = '{$this->session['company_code']}' AND payGrp='{$this->session['pay_group']}' AND payCat='{$this->session['pay_category']}' AND pdNumber=25 and pdYear='".date('Y')."';";
			return $this->execMultiQryI($sqlPayPd);	
		}
		
		private function PrevEmpEarnings() {
			$sqlPrevEmpEarnings = "SELECT empNo, prevBasic, prevAdvances, prevBasicRE, prevAdvancesRE FROM tblPrevEmployer where yearCd = '{$this->get['pdYear']}'";
			$this->arrPrevEmpEarnings = $this->getArrResI($this->execQryI($sqlPrevEmpEarnings));
		}
		
		function getPrevEmpEarnings($empNo) {
			$arr['Basic'] = $arr['Advances'] = 0;
			foreach($this->arrPrevEmpEarnings as $val) {
				if ($val['empNo'] == $empNo) {
					$arr['Basic'] 		= (float)$val['prevBasic'] + (float)$val['prevBasicRE'];
					$arr['Advances'] 	= (float)$val['prevAdvances'] + (float)$val['prevAdvancesRE'];
				}
			}
			return $arr;
		}
		
		private function getPrevYrempBasic() 
		{
			$pdyear = $this->get['pdYear']-1;
			$slqPrevYrBasic = "Insert INTO tblRcdualEarnings (compCode,pdYear,empNo,rcdBasic,rcdAdvances) SELECT '{$_SESSION['company_code']}','{$this->get['pdYear']}',empNo,sum(empBasic), SUM(case when sprtAllowAdvance = NULL or sprtAllowAdvance = 0 or sprtAllowAdvance IS NULL then '0'  else sprtAllowAdvance end) FROM tblPayrollSummaryHist where compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN') and empNo not in (010001423,010001425,010001426,010001427,010001428,010001459,010001460,010001461,010001462,010000044,010000045,010001458)) AND pdNumber IN (23,24) AND pdYear='$pdyear' group by empNo; ";
			return $this->execQryI($slqPrevYrBasic);
		}
		
		private function writeToTblEarnings($type,$empNo,$tranCode,$tranAmount)
		{
			$taxCd  = $writeToTblEarnings = $finalTaxTag = "";
			$taxCd = $this->getTrnTaxCode($this->session['company_code'],$tranCode,'E');//get tax code	
			
			if($type == 'E1' || $type == 'E3')
				$finalTaxTag = $taxCd['trnTaxCd'];
			elseif ($type == 'E2')
				$finalTaxTag = 'Y';
			
			
			$writeToTblEarnings = "INSERT INTO tblEarnings (compCode,pdYear,pdNumber,empNo,trnCode,trnAmountE,trnTaxCd)
									VALUES ('{$this->session['company_code']}','{$this->get['pdYear']}','{$this->get['pdNum']}','{$empNo}','{$tranCode}','".sprintf("%01.2f",$tranAmount)."','{$finalTaxTag}')";
			return $this->execQryI($writeToTblEarnings);
		}
	
	
		private function writeToTblEarningsAllow($type,$empNo,$tranCode,$tranAmount,$separatePS)
		{
			$taxCd = $writeToTblEarnings = $finalTaxTag = "";
			
			$taxCd = $this->getTrnTaxCode($this->session['company_code'],$tranCode,'E');//get tax code	
			
			if($type == 'E1' || $type == 'E3')
				$finalTaxTag = $taxCd['trnTaxCd'];
			elseif ($type == 'E2')
				$finalTaxTag = 'Y';
			
			
			 $writeToTblEarnings = "INSERT INTO tblEarnings(compCode,pdYear,pdNumber,empNo,trnCode,trnAmountE,trnTaxCd,sprtPS)
									VALUES('{$this->session['company_code']}','{$this->get['pdYear']}','{$this->get['pdNum']}','{$empNo}','{$tranCode}','".sprintf("%01.2f",$tranAmount)."','{$finalTaxTag}','{$separatePS}')\n\n";
			
			return $this->execQryI($writeToTblEarnings);
		}		
	
		private function writeToTblDeduction($empNo,$tranCode,$tranAmount)
		{
			$taxCd = $writeToTblDeductions = $finalTaxTag = "";
			
			$taxCd = $this->getTrnTaxCode($this->session['company_code'],$tranCode,'D');//get tax code	
			$taxCdfnl = $taxCd['trnTaxCd'];
			
			 $writeToTblDeductions = "INSERT INTO tblDeductions(compCode,pdYear,pdNumber,empNo,trnCode,trnAmountD,trnTaxCd)
			 							VALUES('{$this->session['company_code']}','{$this->get['pdYear']}','{$this->get['pdNum']}','{$empNo}','{$tranCode}','".sprintf("%01.2f",$tranAmount)."','{$taxCdfnl}')\n\n";
			
			return $this->execQryI($writeToTblDeductions);
		}	
	/*END INSERT / UPDATE TABLES*/
	
}



?>