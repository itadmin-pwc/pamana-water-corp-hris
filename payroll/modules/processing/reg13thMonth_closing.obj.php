<?
class closereg13thMonth extends commonObj {
	
	var $get;//method
	var $session;//session variables
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
	
	
	/*COMMON FUNCTION*/
		function getpayrolldate() 
		{
			$qrypayrolldate="SELECT pdEarningsTag,pdProcessTag FROM tblPayPeriod
								WHERE compCode = '{$this->session['company_code']}'
								AND payGrp = '{$this->session['pay_group']}'
								AND payCat = '{$this->session['pay_category']}'
								AND pdStat IN ('O','') 
								AND pdNumber=25 ";
			return $this->getSqlAssocI($this->execQryI($qrypayrolldate));				
		}	
	
	/*END OF COMMON FUNCTIONS*/
	
	/*DATA RETRIEVE*/
		private function getEanings()
		{
			$qryGetEarnings = "SELECT ern.compCode,ern.pdYear,ern.pdNumber,ern.empNo,ern.trnCode,ern.trnAmountE,ern.trnTaxCd,
									  emp.empPayGrp,emp.empPayCat, ern.sprtPS
									  FROM tblearnings as ern LEFT JOIN tblEmpMast as emp
									  ON ern.compCode = emp.compCode AND ern.empNo = emp.empNo
									  WHERE ern.compCode = '{$this->session['company_code']}'
									  AND  emp.empPayGrp = '{$this->session['pay_group']}'
									  AND emp.empPayCat = '{$this->session['pay_category']}'
									  AND ern.pdYear = '{$this->get['pdYear']}'
									  AND ern.pdNumber = '{$this->get['pdNum']}'
									  AND ern.trnCode IN (
															SELECT trnCode FROM tblPayTransType
															WHERE compCode = '{$this->session['company_code']}'
															AND trnStat = 'A'
															AND trnCat = 'E'
														 )";
			$resGetEarnings = $this->execQryI($qryGetEarnings);
			return $this->getArrResI($resGetEarnings);
		}
		
		private function getDeductions()
		{
			$qryGetDeductions = "SELECT ded.compCode,ded.pdYear,ded.pdNumber,ded.empNo,ded.trnCode,ded.trnAmountD,ded.trnTaxCd,
									  emp.empPayGrp,emp.empPayCat,sprtPS
									  FROM tblDeductions as ded LEFT JOIN tblEmpMast as emp
									  ON ded.compCode = emp.compCode AND ded.empNo = emp.empNo
									  WHERE ded.compCode = '{$this->session['company_code']}'
									  AND  emp.empPayGrp = '{$this->session['pay_group']}'
									  AND emp.empPayCat = '{$this->session['pay_category']}'
									  AND ded.pdYear = '{$this->get['pdYear']}'
									  AND ded.pdNumber = '{$this->get['pdNum']}'
									  AND ded.trnCode IN (
															SELECT trnCode FROM tblPayTransType
															WHERE compCode = '{$this->session['company_code']}'
															AND trnStat = 'A'
															AND trnCat = 'D'
															
														 )";
			$resGetDeductions = $this->execQryI($qryGetDeductions);
			return $this->getArrResI($resGetDeductions);
		}
		
		private function getYtdData()
		{
			 $qryGetYtdData = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,
								emp.empPayGrp,emp.empPayCat,ytd.YtdBasic, ytd.sprtAllow,YTd13NAdvance
								FROM tblYtdData as ytd LEFT JOIN tblEmpMast as emp
								ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
								WHERE ytd.compCode = '{$this->session['company_code']}'
								AND  emp.empPayGrp = '{$this->session['pay_group']}'
								AND emp.empPayCat = '{$this->session['pay_category']}'
								AND ytd.pdYear = '{$this->get['pdYear']}'";
			$resGetYtdData = $this->execQryI($qryGetYtdData);
			return $this->getArrResI($resGetYtdData);
			
		}
		
		private function getEmpAddTaxableIncome($empNo)
		{
			$qryAddTaxable = "SELECT     SUM(amountToDed) AS amountToDed, empNo, monthPeriodDate FROM tblGov_Tax_Added WHERE (empNo = '".$empNo."') and addStat='N' GROUP BY empNo, monthPeriodDate";
			$resAddTaxable = $this->execQryI($qryAddTaxable);
			return  $this->getArrResI($resAddTaxable);
		}	
		
		private function checkYtdData($empNo)
		{
			$qryCheckYtdData = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,
								emp.empPayGrp,emp.empPayCat,ytd.YtdBasic 
								FROM tblYtdDataHist as ytd LEFT JOIN tblEmpMast as emp
								ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
								WHERE ytd.compCode = '{$this->session['company_code']}'
								AND  emp.empPayGrp = '{$this->session['pay_group']}'
								AND emp.empPayCat = '{$this->session['pay_category']}'
								AND ytd.pdYear = '{$this->get['pdYear']}'
								AND ytd.empNo = '{$empNo}'";
			$resCheckYtdData = $this->execQryI($qryCheckYtdData);
			return $this->getRecCountI($resCheckYtdData);
		}
		
		private function getDataToYtdDataHist($empNo)
		{
			$qryGetDataToYtdHist = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,ytd.pdNumber,
								emp.empPayGrp,emp.empPayCat,ytd.YtdBasic, ytd.sprtAllow,YTd13NAdvance
	
								FROM tblYtdDataHist as ytd LEFT JOIN tblEmpMast as emp
								ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
								WHERE ytd.compCode = '{$this->session['company_code']}'
								AND  emp.empPayGrp = '{$this->session['pay_group']}'
								AND ytd.pdYear = '{$this->get['pdYear']}'
								AND ytd.empNo = '{$empNo}'";
			$resGetDataToYtdHist = $this->execQryI($qryGetDataToYtdHist);	
			return 	$this->getSqlAssocI($resGetDataToYtdHist);
		}
		
		private function getEarnTranDtl(){
			$qryGetTranDtl = "SELECT dtl.compCode, dtl.refNo, dtl.empNo, dtl.trnCntrlNo, dtl.trnCode, dtl.trnAmount, dtl.payGrp, dtl.payCat, dtl.earnStat, dtl.trnTaxCd, hdr.earnRem,hdr.pdYear,hdr.pdNumber
							  FROM tblEarnTranDtl AS dtl LEFT JOIN tblEarnTranHeader AS hdr 
							  ON dtl.compCode = hdr.compCode AND dtl.refNo = hdr.refNo 
							  WHERE dtl.compCode = '{$this->session['company_code']}'
							  AND dtl.payGrp = '{$this->session['pay_group']}'
							  AND dtl.payCat = '{$this->session['pay_category']}'
							  AND dtl.earnStat = 'A'
	
							  AND (processTag='Y' or processTag='U')
							  AND hdr.pdYear='{$this->get['pdYear']}'
							  AND hdr.pdNumber = '{$this->get['pdNum']}'
							  AND dtl.trnCode IN ('8018', '0807')";
			
			$resGetTranDtl = $this->execQryI($qryGetTranDtl);
			return $this->getArrResI($resGetTranDtl);
		}
	/*END OF DATA RETRIEVE*/
	
	/*PROCESS*/
		public function mainProcClose13thMonth()
		{
			$lastpaydate = $this->getpayrolldate();
			$seqNo = "";
			$Trns = $this->beginTranI();
			
			foreach ((array)$this->getEanings() as $earnVal)
			{
				$qryToEarningsHist = "INSERT INTO tblEarningsHist
									  (compCode,pdYear,pdNumber,empNo,trnCode,trnAmountE,trnTaxCd,sprtPS)
									  VALUES
									  ('{$earnVal['compCode']}',
									   '{$earnVal['pdYear']}',
									   '{$earnVal['pdNumber']}',
									   '{$earnVal['empNo']}',
									   '{$earnVal['trnCode']}',
									   '{$earnVal['trnAmountE']}',
									   '{$earnVal['trnTaxCd']}',
									   '{$earnVal['sprtPS']}')";
				if($Trns)
					$Trns = $this->execQryI($qryToEarningsHist);
			}
			
			$qryDeleEan = "DELETE FROM tblEarnings 
		               WHERE compCode = '{$this->session['company_code']}'
					   AND empNo IN (
										SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
										AND empPayGrp = '{$this->session['pay_group']}'
										AND empPayCat = '{$this->session['pay_category']}'
										AND empStat IN ('RG','PR','CN')
						     			  )
					   AND trnCode IN (
										SELECT trnCode from tblPayTransType
				  						WHERE compCode = '{$this->session['company_code']}'
				  						AND trnStat = 'A'	
				  						AND trnCat = 'E'
					   				  )";
		
			if($Trns)
				$Trns = $this->execQryI($qryDeleEan);
			
			
			foreach ((array)$this->getDeductions() as $deductVal)
			{
			  $qryToDeductionsHist = "INSERT INTO tblDeductionsHist
								    (compCode,pdYear,pdNumber,empNo,trnCode,trnAmountD,trnTaxCd)
								    VALUES('{$deductVal['compCode']}',
								           '{$deductVal['pdYear']}',
								           '{$deductVal['pdNumber']}',
								           '{$deductVal['empNo']}',
								           '{$deductVal['trnCode']}',
								           '{$deductVal['trnAmountD']}',
								           '{$deductVal['trnTaxCd']}')";
				if($Trns)
					$Trns = $this->execQryI($qryToDeductionsHist);
			}
			
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
			if($Trns)
				$Trns = $this->execQryI($qryDeleDeductions);
			
			
			$qryDelTblDedTranHdr = "";
			
			foreach ((array)$this->getEarnTranDtl() as $earnTrnDtlVal){
				$qryToEarnTrnDtlHist = "INSERT INTO tblEarnTranDtlHist(compCode,refNo,empNo,trnCntrlNo,trnCode,trnAmount,payGrp,payCat,earnStat,trnTaxCd,earnRem,pdYear,pdNumber)
																VALUES('{$earnTrnDtlVal['compCode']}',
																	   '{$earnTrnDtlVal['refNo']}',
																	   '{$earnTrnDtlVal['empNo']}',
																	   '{$earnTrnDtlVal['trnCntrlNo']}',
																	   '{$earnTrnDtlVal['trnCode']}',
																	   '{$earnTrnDtlVal['trnAmount']}',
																	   '{$earnTrnDtlVal['payGrp']}',
																	   '{$earnTrnDtlVal['payCat']}',
																	   '{$earnTrnDtlVal['earnStat']}',
																	   '{$earnTrnDtlVal['trnTaxCd']}',
																	   '{$earnTrnDtlVal['earnRem']}',
																	   '{$earnTrnDtlVal['pdYear']}',
																	   '{$earnTrnDtlVal['pdNumber']}')";
				if($Trns){
					$Trns = $this->execQryI($qryToEarnTrnDtlHist);
				}	
			}
			
			$qryDeleEarnTrnDtl = "DELETE FROM tblEarnTranDtl 
								  WHERE compCode = '{$this->session['company_code']}'
								  AND PayGrp = '{$this->session['pay_group']}'
								  AND PayCat = '{$this->session['pay_category']}'
								  AND earnStat = 'A'
								  AND trnCode IN ('8018', '0807')
								 AND refNo IN (SELECT refNo FROM tblEarnTranHeader
											   WHERE compCode = '{$this->session['company_code']}'
											   AND earnStat = 'P'
											   AND pdYear = '{$this->get['pdYear']}'
											   AND pdNumber = '{$this->get['pdNum']}')";
			if($Trns){
				$Trns = $this->execQryI($qryDeleEarnTrnDtl);
			}	
			
			
			foreach ((array)$this->getYtdData() as $ytdVal)
			{
				$arrGetAddTaxable = $this->getEmpAddTaxableIncome($ytdVal['empNo']);
				foreach($arrGetAddTaxable as $valAddTaxable) 
				{
					if($valAddTaxable["amountToDed"]!=0)
					{
						
						$qryUpdatePaySum_Tax = "Update tblPayrollSummaryHist set taxableEarnings=taxableEarnings+".$valAddTaxable["amountToDed"]." where empNo='".$ytdVal['empNo']."'
												and pdNumber='".$valAddTaxable["monthPeriodDate"]."' and pdYear='".$this->get['pdYear']."'";
						if($Trns)
								$Trns = $this->execQryI($qryUpdatePaySum_Tax);
						
						$qryUpdateGov_Tax = "Update tblGov_Tax_Added set addStat='Y' where empNo='".$ytdVal['empNo']."' and monthPeriodDate='".$valAddTaxable["monthPeriodDate"]."' ";
						if($Trns)
								$Trns = $this->execQryI($qryUpdateGov_Tax);
						
					}	
				}			
			
				if($this->checkYtdData($ytdVal['empNo']) > 0)
				{
						$dataToYtdHist = $this->getDataToYtdDataHist($ytdVal['empNo']);
						$newYtdGross       = (float)$dataToYtdHist['YtdGross'] + (float)$ytdVal['YtdGross'];
						$newYtdTaxable     = (float)$dataToYtdHist['YtdTaxable'] + (float)$ytdVal['YtdTaxable'];
						$newYtdGovDed      = (float)$dataToYtdHist['YtdGovDed'] + (float)$ytdVal['YtdGovDed'];
						$newYtdTax         = (float)$dataToYtdHist['YtdTax'] + (float)$ytdVal['YtdTax'];
						$newYtdNonTaxAllow = (float)$dataToYtdHist['YtdNonTaxAllow'] + (float)$ytdVal['YtdNonTaxAllow'];
						$newYtd13NBonus    = (float)$dataToYtdHist['Ytd13NBonus'] + (float)$ytdVal['Ytd13NBonus'];
						$newYtdTx13NBonus  = (float)$dataToYtdHist['YtdTx13NBonus'] + (float)$ytdVal['YtdTx13NBonus'];
						$newYTd13NAdvance  = (float)$dataToYtdHist['YTd13NAdvance'] + (float)$ytdVal['YTd13NAdvance'];
						$newYtdBasic	   = (float)$dataToYtdHist['YtdBasic'] + (float)$ytdVal['YtdBasic'];
						$newsprtPs	   	   = (float)$dataToYtdHist['sprtAllow'] + (float)$ytdVal['sprtAllow'];
						
						
						$qryUpdateYtdDataHist = "UPDATE tblYtdDataHist SET 
												 YtdGross = '".sprintf("%01.2f",$newYtdGross)."',
												 YtdTaxable = '".sprintf("%01.2f",$newYtdTaxable)."',
												 YtdGovDed = '".sprintf("%01.2f",$newYtdGovDed)."',
												 YtdTax = '".sprintf("%01.2f",$newYtdTax)."',
												 Ytd13NBonus = '".sprintf("%01.2f",$newYtd13NBonus)."',
												 YTd13NAdvance = '".sprintf("%01.2f",$newYTd13NAdvance)."',
												 YtdTx13NBonus = '".sprintf("%01.2f",$newYtdTx13NBonus)."',
												 payGrp='".$ytdVal['payGrp']."',
												 pdNumber='".$this->get['pdNum']."',
												 YtdBasic='".sprintf("%01.2f",$newYtdBasic)."',
												 sprtAllow='".sprintf("%01.2f",$newsprtPs)."'
												 where compCode='".$this->session['company_code']."' 
												 and empNo='".$ytdVal['empNo']."'
												 and pdYear='".$this->get['pdYear']."' ";
					if($Trns)
							$Trns = $this->execQryI($qryUpdateYtdDataHist);
						
				}
				else 
				{
					 $qryToYtdDataHist = "INSERT tblYtdDataHist(compCode,pdYear,empNo,YtdGross,YtdTaxable,
															   YtdGovDed,YtdTax,YtdNonTaxAllow,Ytd13NBonus,YtdTx13NBonus,
															   payGrp,pdNumber,sprtAllow)
														  VALUES('{$ytdVal['compCode']}',
																 '{$ytdVal['pdYear']}',
																 '{$ytdVal['empNo']}',
																 '{$ytdVal['YtdGross']}',
																 '{$ytdVal['YtdTaxable']}',
																 '{$ytdVal['YtdGovDed']}',
																 '{$ytdVal['YtdTax']}',
																 '{$ytdVal['YtdNonTaxAllow']}',
																 '{$ytdVal['Ytd13NBonus']}',
																 '{$ytdVal['YtdTx13NBonus']}',
																 '{$ytdVal['payGrp']}',
																 '{$ytdVal['pdNumber']}',
																 '{$ytdVal['sprtAllow']}')";
					if($Trns)
						$Trns = $this->execQryI($qryToYtdDataHist);
				}	
				unset($newYtdGross,$newYtdTaxable,$newYtdGovDed,$newYtdTax,$newYtdNonTaxAllow,$newYtd13NBonus,$newYtdTx13NBonus,$newYtdBasic,$newsprtPs);
			}
			
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
			if($Trns)
				$Trns = $this->execQryI($qryDeleYtdData);
			
			if($Trns)
				$Trns = $this->ClearAdjBasicReClass();
			
			
			if($Trns)
				$Trns = $this->CloseRcdualEarnings();
			
			if($Trns)
				$Trns = $this->ClearRcdualEarnings();
			
			if($Trns)
				$Trns = $this->ClossingPaySummary();
			
			
			$qryDelPaySumm = "Delete from tblPayrollSummary 
								 WHERE compCode = '{$this->session['company_code']}'
								 AND payGrp = '{$this->session['pay_group']}'
								 AND payCat = '{$this->session['pay_category']}'
								 AND pdYear = '{$this->get['pdYear']}'
								 AND pdNumber = '{$this->get['pdNum']}'";
								 
			if($Trns)
				$Trns =$this->execQryI($qryDelPaySumm );
			
			if($Trns)
				$Trns = $this->ClosePayPeriod();
			
			
			if($Trns)
				$Trns = $this->OpenPayPeriod();
			
	
			if(!$Trns)
			{
				$Trns = $this->rollbackTranI();
				return false;
			}
			else
			{
				$Trns = $this->commitTranI();
				return true;	
			}
		}
	/*END OF PROCESS*/
	
	/*UPDATE TABLE*/
		private function ClearAdjBasicReClass() 
		{
			$sqlClearAdjBasicReClass = "Update tblBasicReclass set recStat = 'C' where compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN'))";
			return $this->execQryI($sqlClearAdjBasicReClass);	
		}
		
		private function CloseRcdualEarnings() 
		{
			$sqlClose = "INSERT INTO tblRcdualEarningshist (compCode, pdYear, empNo, rcdBasic, rcdAdvances) SELECT compCode, pdYear, empNo, rcdBasic, rcdAdvances FROM tblRcdualEarnings WHERE compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN'))";
			return $this->execQryI($sqlClose);	
		}
		
		private function ClearRcdualEarnings() 
		{
			$sqlClear = "DELETE FROM tblRcdualEarnings WHERE compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$_SESSION['pay_group']}' AND empPayCat='{$_SESSION['pay_category']}' AND empStat IN ('RG','PR','CN'))";
			return $this->execQryI($sqlClear);	
		}	
		
		private function ClossingPaySummary() {
		 $qryClossingSummary="Insert into tblPayrollSummaryHist 
							(compCode, pdYear, pdNumber, empNo, payGrp, payCat, empLocCode, empBrnCode, empBnkCd, grossEarnings,
							 taxableEarnings, totDeductions,nonTaxAllow, netSalary, taxWitheld, empDivCode, empDepCode,empSecCode,sprtAllow,empBasic,empMinWageTag,empEcola, empTeu,empYtdTaxable,empYtdTax,empYtdGovDed,sprtAllowAdvance,emp13thMonthNonTax,emp13thMonthTax,emp13thAdvances,yearEndTax)
							 
							 Select  compCode, pdYear, pdNumber, empNo, payGrp, payCat, empLocCode, empBrnCode, empBnkCd, 
							 grossEarnings, taxableEarnings, totDeductions, nonTaxAllow, netSalary, taxWitheld, empDivCode, 
							 empDepCode, empSecCode,sprtAllow,empBasic,empMinWageTag,empEcola,empTeu,empYtdTaxable,empYtdTax,empYtdGovDed,sprtAllowAdvance,emp13thMonthNonTax,emp13thMonthTax,emp13thAdvances,yearEndTax from tblPayrollSummary 
					 		 WHERE compCode = '{$this->session['company_code']}'
							 AND payGrp = '{$this->session['pay_group']}'
							 AND payCat = '{$this->session['pay_category']}'
							 AND pdYear = '{$this->get['pdYear']}'
							 AND pdNumber = '{$this->get['pdNum']}'
							 ";
			return $this->execQryI($qryClossingSummary);					 
	}
	
		
		private function ClosePayPeriod() 
		{
			$pdDateClosed=date("m/d/Y");
			
			$qryClosePayPeriod="Update tblPayPeriod 
								Set 
									pdProcessTag='Y',
									pdProcessDate='$pdDateClosed',
									pdProcessedBy='{$this->session['employee_number']}',
									pdDateClosed = '$pdDateClosed',
									pdClosedBy = '{$this->session['employee_number']}',
									pdStat = 'H'
								 WHERE compCode = '{$this->session['company_code']}'
								 AND payGrp = '{$this->session['pay_group']}'
								 AND payCat = '{$this->session['pay_category']}'
								 AND pdYear = '{$this->get['pdYear']}'
								 AND pdNumber = '{$this->get['pdNum']}'
								";
			return $this->execQryI($qryClosePayPeriod);		
		}	
		
		function OpenPayPeriod() 
		{
			$qryOpen="Update tblPayPeriod set pdStat='O', pdTSStat='O' 
				where (compCode = '" . $this->session['company_code'] . "') 
				AND (pdYear = '" . $this->get['pdYear'] . "') 
				AND (payGrp='" . $this->session['pay_group'] . "')
				AND (payCat='" . $this->session['pay_category'] . "')
				AND pdNumber IN (SELECT pdNumberClosed from tbl13thCheck where compCode= '{$this->session['company_code']}' AND payGrp='{$this->session['pay_group']}' AND payCat='{$this->session['pay_category']}' AND pdYear='".date('Y')."')
			";
			return $this->execQryI($qryOpen);
		}
	/*END OF UPDATE TABLES*/
}

?>