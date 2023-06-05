<?
class regPayrollProcObj extends commonObj {
	
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
	
	public function checkPeriodTags(){
		
		$qryChkPeriodTags = "SELECT pdTsTag,pdLoansTag,pdEarningsTag,pdProcessTag FROM tblPayPeriod
							WHERE compCode = '{$this->session['company_code']}'
							AND payGrp = '{$this->session['pay_group']}'
							AND payCat = '{$this->session['pay_category']}' ";
		$resChkPeriodTags = $this->execQry($qryChkPeriodTags) ;
		return $this->getSqlAssoc($resChkPeriodTags);
	}
	
	private function summarizeCorrection(){
		
		$qrySummrzeCrrctns = "SELECT compCode,empNo,SUM(amtAbsent)*-1 as sumAmtAbsnt,SUM(AmtTardy)*-1 as sumAmtTardy ,SUM(AmtUt)*-1 as sumAmtUt
							  FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
						      AND empPayGrp = '{$this->session['pay_group']}'
							  AND empPayCat = '{$this->session['pay_category']}'
							  GROUP BY compCode, empNo  ";
		$resSummrzeCrrctns = $this->execQry($qrySummrzeCrrctns);
		return $this->getArrRes($resSummrzeCrrctns);
	}
	
	private function summarizeOtAndNd(){
		
		$qrySummrzeOtAndNd = "SELECT compCode, empNo, dayType, SUM(amtOtLe8) AS sumAmtOtLe8, SUM(amtOtGt8) AS sumAmtOtGt8, SUM(amtNdLe8) AS sumAmtNdLe8, SUM(amtNdGt8) 
		                      AS sumAmtNdGt8, trnOtLe8, trnOtGt8, trnNdLe8, trnNdGt8
							  FROM  tblTimeSheet
							  WHERE (compCode = '{$this->session['company_code']}') 
							  AND (empPayGrp = '{$this->session['pay_group']}') 
							  AND (empPayCat = '{$this->session['pay_category']}')
							  GROUP BY compCode, empNo, dayType, trnOtLe8, trnOtGt8, trnNdLe8, trnNdGt8";
		$resSummrzeOtAndNd = $this->execQry($qrySummrzeOtAndNd);
		return $this->getArrRes($resSummrzeOtAndNd);
	}

	private function postAdjustmentOthers(){

		$qryPostAdjOthrs = "SELECT empNo, trnCode, SUM(trnAmount) AS sumEarnAmnt, trnTaxCd
							FROM tblEarnTranDtl 
							WHERE (compCode = '{$this->session['company_code']}') 
							  AND (payGrp = '{$this->session['pay_group']}') 
							  AND (payCat = '{$this->session['pay_category']}')
							  AND (earnStat = 'A')
							  GROUP BY empNo, trnCode, trnTaxCd";
		$resPostAdjOthrs = $this->execQry($qryPostAdjOthrs);
		return $this->getArrRes($resPostAdjOthrs);
	}
	
	private function getTrnTaxCode($compCode,$trnCode){
		$trnTaxCd = "SELECT trnTaxCd FROM tblPayTransType 
					 WHERE compCode = '{$compCode}'
					 AND trnCat = 'E' ";					
		$trnTaxCd .= "AND trnCode = '{$trnCode}' ";
		$resTaxCd = $this->execQry($trnTaxCd);
		return  $this->getSqlAssoc($resTaxCd);			
	}
	
	private function getEmpToProcPayBasic(){
		
		$qryGetEmpToProc = "SELECT compCode,empNo,dateHired,
								   empStat,empPayType,empMrate,empDrate
							FROM tblEmpMast 
							WHERE compCode = '{$this->session['company_code']}'
							AND empPayGrp = '{$this->session['pay_group']}'
							AND empPayCat = '{$this->session['pay_category']}'
							AND empStat IN ('RG','PR','CN')";
		$resGetEmpToProc = $this->execQry($qryGetEmpToProc);
		return $this->getArrRes($resGetEmpToProc);
	}
	
	private function OneHalfBasic($monthlyRate){
		
		$amntBasic = (float)$monthlyRate/2;
		return sprintf("%01.2f",$amntBasic);
	}
	
	private function proRateBasic($monthlyRate,$dailyRate,$dateHired){
		
		$dateDiff = (int)$this->dateDiff($this->dateFormat($dateHired),$this->get['dtFrm'],'m/d/Y','D');
		
		$tmpDrate = $dateDiff*(float)$dailyRate;
		if($tmpDrate <= 0){
			return 0;
		}
		else{
			$dvsr = $tmpDrate;
			return   sprintf("%01.2f",(((float)$monthlyRate/2)-(float)$dvsr));
		}
	}
	
	/**
	 * return transaction code and transaction amount with delimiter -
	 *
	 * @param string $type
	 * @param int    $empNo
	 * @param float  $dailyRate
	 * @return transcode and transamount
	 */
	private function getDailiesBasic($empNo,$dailyRate){
				
		$qryGetRegDaysToTS = "SELECT COUNT(empNo) as totCnt FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empNo = '{$empNo}' 
							  AND tsDate >= '{$this->get['dtFrm']}'
							  AND tsDate <= '{$this->get['dtTo']}' 
							  AND dayType = '01'";

		$resGetRegDaysToTS = $this->execQry($qryGetRegDaysToTS);
		$rowGetRegDaysToTS = $this->getSqlAssoc($resGetRegDaysToTS);
							
		$trnCode = "0100";
		if($rowGetRegDaysToTS['totCnt'] > 0){
			
			$amountDailiesBasic = (int)$rowGetRegDaysToTS['totCnt']*(float)$dailyRate;
			return $trnCode."-".sprintf("%01.2f",$amountDailiesBasic);
		}
		else{
			return $trnCode."-"."0";
		}
	}
	
	private function getDailiesLegHoliday($empNo,$dailyRate){
		
		$qryGetLegHolToTS = "SELECT COUNT(empNo) as totCnt FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empNo = '{$empNo}' 
							  AND tsDate >= '{$this->get['dtFrm']}'
							  AND tsDate <= '{$this->get['dtTo']}' 
							  AND dayType = '03' 
							  AND amtOtLe8 = 0
							  AND amtOtGt8 = 0";

		$resGetLegHolToTS = $this->execQry($qryGetLegHolToTS);
		$rowGetLegHolToTS = $this->getSqlAssoc($resGetLegHolToTS);
		
		$trnCode = "0410";
		if($rowGetLegHolToTS['totCnt'] > 0){
			
			$amountDailiesLegHol = (int)$rowGetLegHolToTS['totCnt']*(float)$dailyRate;
			return $trnCode."-".sprintf("%01.2f",$amountDailiesLegHol);
		}
		else{
			return $trnCode."-"."0";
		}		
	}
	
	private function getDailiesSpHol($empNo,$dailyRate){
		
		$qryGetSpHolToTS = "SELECT COUNT(empNo) as totCnt FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empNo = '{$empNo}' 
							  AND tsDate >= '{$this->get['dtFrm']}'
							  AND tsDate <= '{$this->get['dtTo']}' 
							  AND dayType = '04' 
							  AND amtOtLe8 = 0
							  AND amtOtGt8 = 0";

		$resGetSpHolToTS = $this->execQry($qryGetSpHolToTS);
		$rowGetSpToTS = $this->getSqlAssoc($resGetSpHolToTS);

		
		$trnCode = "0420";
		if($rowGetSpToTS['totCnt'] > 0){
			
			$amountDailiesSpHol = (int)$rowGetSpToTS['totCnt']*(float)$dailyRate;
			return $trnCode."-".sprintf("%01.2f",$amountDailiesSpHol);
		}
		else{
			return $trnCode."-"."0";
		}		
	}
	
	/**
	 * write to table earnings
	 *
	 * @param  string $type
	 * @param  string $empNo
	 * @param  int $tranCode
	 * @param  float $tranAmount
	 * @return write to table earnings
	 */
	private function writeToTblEarnings($type,$empNo,$tranCode,$tranAmount){
		//echo $empNo."  ---   ".$tranAmount."\n";
			
		$taxCd = "";
		$writeToTblEarnings = "";
		$finalTaxTag = "";
		
		$taxCd = $this->getTrnTaxCode($this->session['company_code'],$tranCode);//get tax code	
		if($type == 'E1' || $type == 'E3'){
			$finalTaxTag = $taxCd['trnTaxCd'];
		}elseif ($type == 'E2'){
			$finalTaxTag = 'Y';
		}
		
		$writeToTblEarnings = "INSERT INTO tblEarnings
										  (compCode,pdYear,
										  pdNumber,empNo,
										  trnCode,trnAmountE,trnTaxCd)
										  VALUES
										  ('{$this->session['company_code']}','{$this->get['pdYear']}',
										   '{$this->get['pdNum']}','{$empNo}',
										   '{$tranCode}','{$tranAmount}','{$finalTaxTag}')";
		return $this->execQry($writeToTblEarnings);
	}
	
	public function mainProcRegPayroll(){
		
		$Trns = $this->beginTran();//begin regular payroll transaction
		
		foreach ((array)$this->summarizeCorrection() as $arrSumCorrVal){//foreach timeesheet	
			
			if($arrSumCorrVal['sumAmtAbsnt']){
				if((float)$arrSumCorrVal['sumAmtAbsnt'] != 0){
										
					if($Trns){
						$Trns = $this->writeToTblEarnings('E1',$arrSumCorrVal['empNo'],'0113',$arrSumCorrVal['sumAmtAbsnt']);
					}
				}
			}
				
			if($arrSumCorrVal['sumAmtTardy']){
				if((float)$arrSumCorrVal['sumAmtTardy'] != 0){			
					
					if($Trns){
						$Trns = $this->writeToTblEarnings('E1',$arrSumCorrVal['empNo'],'0111',$arrSumCorrVal['sumAmtTardy']);
						
					}			
				}	
			}		

			if($arrSumCorrVal['sumAmtUt']){
				if((float)$arrSumCorrVal['sumAmtUt'] != 0){								
									
					if($Trns){
						$Trns = $this->writeToTblEarnings('E1',$arrSumCorrVal['empNo'],'0111',$arrSumCorrVal['sumAmtUt']);
					}	
				}	
			}			
		}//end of foreach timeesheet	
		

		foreach ((array)$this->summarizeOtAndNd() as $arrSumOtNNdVal){//foreach overtime and night diferential
			
			if($arrSumOtNNdVal['sumAmtOtLe8']){
				
				if((float)$arrSumOtNNdVal['sumAmtOtLe8'] != 0){
					
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$arrSumOtNNdVal['empNo'],$arrSumOtNNdVal['trnOtLe8'],$arrSumOtNNdVal['sumAmtOtLe8']);
					}						 
				}
			}

			if($arrSumOtNNdVal['sumAmtOtGt8']){
				
				if((float)$arrSumOtNNdVal['sumAmtOtGt8'] != 0){
					
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$arrSumOtNNdVal['empNo'],$arrSumOtNNdVal['trnOtGt8'],$arrSumOtNNdVal['sumAmtOtGt8']);
					}						 
				}
			}	
			
			if($arrSumOtNNdVal['sumAmtNdLe8']){
				
				if((float)$arrSumOtNNdVal['sumAmtNdLe8'] != 0){
					
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$arrSumOtNNdVal['empNo'],$arrSumOtNNdVal['trnNdLe8'],$arrSumOtNNdVal['sumAmtNdLe8']);
					}						 
				}
			}			
	
			if($arrSumOtNNdVal['sumAmtNdGt8']){
				
				if((float)$arrSumOtNNdVal['sumAmtNdGt8'] != 0){
					
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$arrSumOtNNdVal['empNo'],$arrSumOtNNdVal['trnNdGt8'],$arrSumOtNNdVal['sumAmtNdGt8']);
					}						 
				}
			}			
		}//end foreach overtime and night diferential
		
//		foreach ((array)$this->postAdjustmentOthers() as $arrPostAdjOthrsVal){//foreach for post adjustment and others
//			if($Trns){
//				$Trns = $this->writeToTblEarnings('E3',$arrPostAdjOthrsVal['empNo'],$arrPostAdjOthrsVal['trnCode'],$arrPostAdjOthrsVal['sumEarnAmnt']);
//			}
//		}//end foreach for post adjustment and others
				
//		foreach ((array)$this->getEmpToProcPayBasic() as $empToProcPayBaicVal){//foreach pay basics
//			
//			if(trim($empToProcPayBaicVal['empPayType']) == 'M'){//monthly
//				if(trim($empToProcPayBaicVal['empStat']) == 'RG'){//regular
//					$trnCodePayBasicMreg = '0100';
//					$trnAmountPayBasicMreg = $this->OneHalfBasic($empToProcPayBaicVal['empMrate']);	
//					if($Trns){
//						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicMreg,$trnAmountPayBasicMreg);			
//					}
//				}
//				else{//probationary or contractual
//					//if datehired is less or equal to date period from do one half basic procedure
//					if(strtotime(date('m/d/Y',strtotime($this->dateFormat($empToProcPayBaicVal['dateHired'])))) <= strtotime(date('m/d/Y',strtotime($this->get['dtFrm'])))){
//						$trnCodePayBasicNotReg = '0100';
//						$trnAmountPayBasicNotReg = $this->OneHalfBasic($empToProcPayBaicVal['empMrate']);	
//					}
//					else{//if datehired is greater than to date period  from do pro rate basic
//						$trnCodePayBasicNotReg = '0100';
//						$trnAmountPayBasicNotReg = $this->proRateBasic($empToProcPayBaicVal['empMrate'],$empToProcPayBaicVal['empDrate'],$empToProcPayBaicVal['dateHired']);				
//					}	
//					if($Trns){				
//						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicNotReg,$trnAmountPayBasicNotReg);			
//					}
//				}
//			}//end of monthly
//			else{//daily
//				
//				//basic	
//				$arrDailiesBasic = explode("-",$this->getDailiesBasic($empToProcPayBaicVal['empNo'],$empToProcPayBaicVal['empDrate']));
//				$trnCodePayBasicDailiesBasic = $arrDailiesBasic[0];
//				$trnAmountPayBasicDailiesBasic = $arrDailiesBasic[1];
//				if($trnAmountPayBasicDailiesBasic > 0){
//					if($Trns){
//						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDailiesBasic,$trnAmountPayBasicDailiesBasic);
//					}
//				}
//
//				//legal holiday
//				$arrDailiesLegHoliday = explode("-",$this->getDailiesLegHoliday($empToProcPayBaicVal['empNo'],$empToProcPayBaicVal['empDrate']));
//				$trnCodePayBasicDaliesLegHoliday = $arrDailiesLegHoliday[0];
//				$trnAmountPayBasicDaliesLegHoliday = $arrDailiesLegHoliday[1];
//				if($trnAmountPayBasicDaliesLegHoliday > 0){
//					if($Trns){
//						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDaliesLegHoliday,$trnAmountPayBasicDaliesLegHoliday);
//					}
//				}
//
//				//special holiday
//				$arrDailiesSpHoliday = explode("-",$this->getDailiesSpHol($empToProcPayBaicVal['empNo'],$empToProcPayBaicVal['empDrate']));
//				$trnCodePayBasicDaliesSpHoliday = $arrDailiesSpHoliday[0];
//				$trnAmountPayBasicSpHoliday = $arrDailiesSpHoliday[1];
//				if($trnAmountPayBasicSpHoliday > 0){
//					if($Trns){
//						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDaliesSpHoliday,$trnAmountPayBasicSpHoliday);
//					}
//				}
//			}//end of daily
//		}//end of foreach pay basic		
		
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