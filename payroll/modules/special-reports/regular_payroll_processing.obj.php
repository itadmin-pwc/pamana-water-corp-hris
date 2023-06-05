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
	
	######################################################BEGIN OF EARNINGS##################################################################
	
	public function checkPeriodTags(){
		
		$qryChkPeriodTags = "SELECT pdTsTag,pdLoansTag,pdEarningsTag,pdProcessTag FROM tblPayPeriod
							WHERE compCode = '{$this->session['company_code']}'
							AND payGrp = '{$this->session['pay_group']}'
							AND payCat = '{$this->session['pay_category']}'
							AND pdStat IN ('O','') ";
		$resChkPeriodTags = $this->execQry($qryChkPeriodTags) ;
		return $this->getSqlAssoc($resChkPeriodTags);
	}
	
	private function summarizeCorrection(){
		
		$qrySummrzeCrrctns = "SELECT compCode,empNo,SUM(amtAbsent)*-1 as sumAmtAbsnt,SUM(AmtTardy)*-1 as sumAmtTardy ,SUM(AmtUt)*-1 as sumAmtUt
							  FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
						      AND empPayGrp = '{$this->session['pay_group']}'
							  AND empPayCat = '{$this->session['pay_category']}'
							  AND empNo IN (
							  				SELECT empNo FROM tblEmpMast
                            				WHERE compCode = '{$this->session['company_code']}' 
                            				AND empPayGrp = '{$this->session['pay_group']}' 
                            				AND empPayCat = '{$this->session['pay_category']}' 
                            				AND empStat IN ('RG', 'PR', 'CN')
                            				)
							 AND tsDate between '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}' 
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
							  AND empNo IN (
							  				SELECT empNo FROM tblEmpMast
                            				WHERE compCode = '{$this->session['company_code']}' 
                            				AND empPayGrp = '{$this->session['pay_group']}' 
                            				AND empPayCat = '{$this->session['pay_category']}' 
                            				AND empStat IN ('RG', 'PR', 'CN')
                            				)
							  GROUP BY compCode, empNo, dayType, trnOtLe8, trnOtGt8, trnNdLe8, trnNdGt8";
		$resSummrzeOtAndNd = $this->execQry($qrySummrzeOtAndNd);
		return $this->getArrRes($resSummrzeOtAndNd);
	}

	private function postAdjustmentOthers($sign){

		$qryPostAdjOthrs = "SELECT empNo, trnCode, SUM(trnAmount) AS sumEarnAmnt, trnTaxCd
							FROM tblEarnTranDtl 
							WHERE (compCode = '{$this->session['company_code']}') 
							  AND (payGrp = '{$this->session['pay_group']}') 
							  AND (payCat = '{$this->session['pay_category']}')
							  AND (earnStat = 'A')
							  AND (trnCode IN (Select trnCode from tblEarnTranHeader where compCode='{$_SESSION['company_code']}' and earnStat='A' 
											   AND pdYear = '{$this->get['pdYear']}'
											   AND pdNumber = '{$this->get['pdNum']}'))
							  AND (trnCode IN (Select trnCode from tblPayTransType 
							  				           where trnApply IN (".$this->getCutOffPeriod().",3) 
							  				            AND trnCat = 'E'
							  				            AND trnCode NOT IN (
							  				            					SELECT trnCode FROM tblAllowType
							  				  					                   WHERE compCode = '{$this->session['company_code']}'
							  				  					                   AND allowTypeStat = 'A'
							  				  					            )
							  				  		    AND trnEntry = 'Y'
							  				  	)
							      )
							  AND (trnAmount $sign 0)
							  AND empNo IN (
							  				SELECT empNo FROM tblEmpMast
                            				WHERE compCode = '{$this->session['company_code']}' 
                            				AND empPayGrp = '{$this->session['pay_group']}' 
                            				AND empPayCat = '{$this->session['pay_category']}' 
                            				AND empStat IN ('RG', 'PR', 'CN')
                            				)
							  AND refNo in 	(Select refNo from tblEarnTranHeader where compCode='{$_SESSION['company_code']}' and earnStat='A' 
											   AND pdYear = '{$this->get['pdYear']}'
											   AND pdNumber = '{$this->get['pdNum']}')
							  GROUP BY empNo, trnCode, trnTaxCd ";
		if($sign == "<"){
			$qryPostAdjOthrs .= "ORDER BY empNo,sumEarnAmnt DESC";
		}
		//echo $qryPostAdjOthrs."\n\n";
		$resPostAdjOthrs = $this->execQry($qryPostAdjOthrs);
		return $this->getArrRes($resPostAdjOthrs);
	}
	
	private  function getSumPositiveEarnings($empNo){
		
    	$qryGetPosEarn = "SELECT empNo,sum(trnAmountE) as sumPostvEarn FROM tblEarnings
						  WHERE compCode = '{$this->session['company_code']}'
						  AND pdYear = '{$this->get['pdYear']}'
						  AND pdNumber = '{$this->get['pdNum']}'
						  AND empNo = '{$empNo}'
						  AND trnCode IN (Select trnCode from tblPayTransType 
							  				           where trnApply IN (".$this->getCutOffPeriod().",3) 
							  				            AND trnCat = 'E'
							  				            AND trnCode NOT IN (SELECT trnCode FROM tblAllowType
							  				  					                   WHERE compCode = '{$this->session['company_code']}'
							  				  					                   AND allowTypeStat = 'A'
							  				  					            )
							  			)
						  GROUP BY empNo";
		$resGetPosEarn = $this->execQry($qryGetPosEarn);
		return $this->getSqlAssoc($resGetPosEarn);
	}
	
	private function getRefNoOtherAdj($empNo,$trnCode){
		
		$qry = "SELECT dtl.compCode, dtl.refNo, dtl.empNo, dtl.trnCode, dtl.trnAmount, hdr.pdYear, hdr.pdNumber
				FROM  tblEarnTranDtl dtl LEFT OUTER JOIN tblEarnTranHeader hdr 
				ON dtl.compCode = hdr.compCode AND dtl.refNo = hdr.refNo AND dtl.trnCode = hdr.trnCode
				WHERE (dtl.compCode = '{$this->session['company_code']}') 
				AND (dtl.payGrp = '{$this->session['pay_group']}') 
				AND (dtl.payCat = '{$this->session['pay_category']}') 
				AND (hdr.pdYear = '{$this->get['pdYear']}') 
				AND (hdr.pdNumber = '{$this->get['pdNum']}') 
				AND (dtl.trnAmount < 0) 
				AND (dtl.trnCode IN
                          			(SELECT trnCode FROM tblPayTransType
                            		  WHERE compCode = '{$this->session['company_code']}' 
                            		  AND trnCat = 'E' 
                            		  AND trnApply IN (".$this->getCutOffPeriod().", 3) 
                            		  AND trnStat = 'A'
                            		 )
                     ) 
    		  AND (dtl.earnStat = 'A')
    		  AND (dtl.empNo = '{$empNo}')
    		  AND (dtl.trnCode = '{$trnCode}')";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
		
	}
	
	private function getTrnTaxCode($compCode,$trnCode,$type){
		$trnTaxCd = "SELECT trnTaxCd FROM tblPayTransType 
					 WHERE compCode = '{$compCode}'
					 AND trnCat = '{$type}' ";					
		$trnTaxCd .= "AND trnCode = '".trim($trnCode)."' ";
		$resTaxCd = $this->execQry($trnTaxCd);
		return  $this->getSqlAssoc($resTaxCd);			
	}
	
	private function getEmpToProcPayBasic(){
		
		$qryGetEmpToProc = "SELECT compCode,empNo,dateHired,
								   empStat,empPayType,empMrate,empDrate, empRestDay
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
	

	private function proRateBasic($empNo,$dailyRate,$dateHired){
		
		$qryGetProrateBasic = "SELECT tsDate FROM tblTimesheet 
							   WHERE compCode = '{$this->session['company_code']}'
							   AND empNo = '{$empNo}' 
							   AND tsDate >= '".$this->dateFormat($dateHired)."'
							   AND tsDate <= '{$this->get['dtTo']}' ";
		$resGetProrateBasic = $this->execQry($qryGetProrateBasic);
		$cntGetProrateBasic = $this->getRecCount($resGetProrateBasic);
		$prorate = $cntGetProrateBasic*(float)$dailyRate;
		return sprintf("%01.2f",$prorate);
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
		
		$emp_DaysAccru = $amountDailiesBasic = 0;
		$trnCode = "0100";
		
		//Get Employee Time Sheet where DayType == 01;
		$qryGetRegDaysToTS = "SELECT * FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empNo = '{$empNo}' 
							  AND tsDate >= '{$this->get['dtFrm']}'
							  AND tsDate <= '{$this->get['dtTo']}' 
							  AND dayType = '01'";
							  
		$resGetRegDaysToTS = $this->execQry($qryGetRegDaysToTS);
		$rowGetRegDaysToTS = $this->getArrRes($resGetRegDaysToTS);
		
		foreach($rowGetRegDaysToTS as $rowGetRegDaysToTS_Val)
		{
			if($rowGetRegDaysToTS_Val["hrsAbsent"]=='4'){
				$emp_DaysAccru+=0.5;
			}
			
			if($rowGetRegDaysToTS_Val["hrsAbsent"]=='0'){
				$emp_DaysAccru++;
			}
		}
		
		$amountDailiesBasic = 	$emp_DaysAccru * (float)$dailyRate;
		if($amountDailiesBasic>0){
			return $trnCode."-".sprintf("%01.2f",$amountDailiesBasic)."-".$emp_DaysAccru;
		}
		else{
			return $trnCode."-"."0"."-".$emp_DaysAccru;
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
	
	private function getCutOffPeriod(){

		if((int)trim((int)trim($this->get['pdNum']))%2){
			return  1;
		}
		else{
			return 2;
		}	
	}
	
	//for allowance 
	private function getEmpAllowance($table,$empNo,$and){
		
			$qryGetEmpAllow = "SELECT $table.compCode, $table.empNo, $table.allowCode, $table.allowAmt, $table.allowSked, $table.allowTaxTag,
							    $table.allowPayTag, $table.allowStart, $table.allowEnd, $table.allowStat,$table.sprtPS, tblEmpMast.dateHired, 
							    tblEmpMast.empPayGrp, tblEmpMast.empPayType, tblEmpMast.empPayCat, tblEmpMast.empMrate, tblEmpMast.empDrate, 
							    tblEmpMast.empHrate,tblEmpMast.empStat, tblAllowType.attnBase, tblAllowType.trnCode, tblAllowType.allowTypeStat,tblPayTransType.trnTaxCd
						  FROM $table INNER JOIN
							   tblAllowType ON $table.compCode = tblAllowType.compCode AND 
							   $table.allowCode = tblAllowType.allowCode LEFT OUTER JOIN
							   tblEmpMast ON $table.compCode = tblEmpMast.compCode AND $table.empNo = tblEmpMast.empNo
							   LEFT JOIN tblPayTransType 
							   ON tblAllowType.compCode = tblPayTransType.compCode AND tblAllowType.trnCode = tblPayTransType.trnCode
						  WHERE ($table.compCode = '{$this->session['company_code']}') 
						  AND (tblEmpMast.empPayGrp = '{$this->session['pay_group']}') 
						  AND (tblEmpMast.empPayCat = '{$this->session['pay_category']}') 
						  AND (tblEmpMast.empStat IN ('RG', 'PR', 'CN')) 
						  AND ($table.allowSked IN ('".$this->getCutOffPeriod()."','3')) 
						  AND ($table.allowStat = 'A') 
						  AND (tblAllowType.allowTypeStat = 'A') ";
		if($empNo != ""){
			$qryGetEmpAllow .= "AND $table.empNo = '{$empNo}' ";
		}
		if($and != ""){
			$qryGetEmpAllow .= $and;	
		}
		
		
		$resGetEmpAllow = $this->execQry($qryGetEmpAllow);
		
		return $this->getArrRes($resGetEmpAllow);
	}
	
	private function getPreviousCutOff($pdYear, $pdNum)
	{
		/*Check if pdNum will exceed for the previous year*/
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
		
		$resPrevCutOff = $this->execQry($qryPrevCutOff);
		
		return $this->getSqlAssoc($resPrevCutOff);
	}
	
	private function getArrAllow($emp_AccruedDays,$emp_AmntTardy,$emp_AmntUt){
		
		$arrChecker = array();
		$compNoDays = 0;
		$arrDateTimeSheet = $this->getPreviousCutOff($this->get['pdYear'], $this->get['pdNum']);
		$dsDate = ($arrDateTimeSheet["pdFrmDate"]!=""?$arrDateTimeSheet["pdFrmDate"]:"");
		$deDate = ($arrDateTimeSheet["pdToDate"]!=""?$arrDateTimeSheet["pdToDate"]:"");
		
		
		foreach ((array)$this->getEmpAllowance('tblAllowance','','') as $allowArrVal){//allowance
			$allowAmnt = (float)$allowArrVal['allowAmt'];
			$arrcompNoDays = $this->getCompAnnDate($_SESSION["company_code"]);
			
			$compNoDays = ($arrcompNoDays["compNoDays"]!=""?$arrcompNoDays["compNoDays"]:26);
			
			$dailyAmnt = (float)$allowAmnt/$compNoDays;
			$hourlyAmnt = $dailyAmnt/8;
			$totalAllowAmnt = 0;
			$withTardy =0;
			$empAllowAmnt = 0;
			$empTardUt = $emp_DRate= 0;
			
			/*Permanent Allowance*/
			$qryGetTimeSheet		= 	"AND tsDate BETWEEN '".$this->dateFormat($allowArrVal['allowStart'])."' AND '{$this->get['dtTo']}'";
			
			if(($dsDate!="") && ($deDate!=""))
				$qryGetTimeSheet_Prev	= 	"AND tsDate BETWEEN '".$this->dateFormat($dsDate)."' AND '".$this->dateFormat($deDate)."'";
			
			/*Temporary Allowance*/
			$qryGetTimeSheet_Temp		= 	"AND tsDate BETWEEN '".$this->dateFormat($allowArrVal['allowStart'])."' AND '".$this->dateFormat($allowArrVal['allowEnd'])."'";
			
			if(($dsDate!="") && ($deDate!=""))
				$qryGetTimeSheet_Prev_Temp	= 	"AND tsDate BETWEEN '".$this->dateFormat($allowArrVal['allowStart'])."' AND '".$this->dateFormat($allowArrVal['allowEnd'])."'";
				
															  													   						
			switch($allowArrVal['empPayType']) {
				case 'M'://pay type M = monthly
					
					/*Check the Allow Sked, para makuha ung data nya sa tblTimeSheetHist*/
					if(($allowArrVal['allowSked'] == '1')||($allowArrVal['allowSked'] == '2'))
					{
						switch (trim($allowArrVal['allowPayTag'])){
							case 'P':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'],$allowArrVal['empPayGrp'], $allowArrVal['empPayCat'],$qryGetTimeSheet,$dailyAmnt,trim($allowArrVal['attnBase']),$qryGetTimeSheet_Prev,$allowAmnt,$compNoDays);
								
							break;
							case 'T':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'],$allowArrVal['empPayGrp'], $allowArrVal['empPayCat'],$qryGetTimeSheet_Temp,$dailyAmnt,trim($allowArrVal['attnBase']),$qryGetTimeSheet_Prev_Temp,$allowAmnt,$compNoDays);
							break;
						}//end of switch case
					}
					
					/*If the AllowSked = BOTH, di na kailngan ksi BOTH binibigay*/
					else
					{
						switch (trim($allowArrVal['allowPayTag'])){
							case 'P':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'],$allowArrVal['empPayGrp'], $allowArrVal['empPayCat'],$qryGetTimeSheet,$dailyAmnt,trim($allowArrVal['attnBase']),'',(float)($allowAmnt/2),$compNoDays/2);
							break;
							case 'T':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'],$allowArrVal['empPayGrp'], $allowArrVal['empPayCat'],$qryGetTimeSheet_Temp,$dailyAmnt,trim($allowArrVal['attnBase']),'',(float)($allowAmnt/2),$compNoDays/2);
							break;
						}//end of switch case
					}
				
				break;	
				
				case 'D'://pay type D = daily
					$empTardUt = $emp_AmntTardy[$allowArrVal['empNo']] + $emp_AmntUt[$allowArrVal['empNo']];
					
					if(trim($allowArrVal['attnBase'])=='Y')
						$empTardUt = (float)$empTardUt*-1;
					else
						$empTardUt = 0;
					
					$totalAllowAmnt = $allowAmnt * ($emp_AccruedDays[$allowArrVal['empNo']] - ($empTardUt/$allowArrVal['empDrate']));
					$totalAllowAmnt = ($totalAllowAmnt!=0?sprintf("%01.2f",$totalAllowAmnt):0);
					
					//echo $allowArrVal['empNo']."\nAllowance Amnt.=".$allowAmnt."\nAccrued Days=".$emp_AccruedDays[$allowArrVal['empNo']]."\nTARD/UT=".$empTardUt."\nDaily Rate=".$allowArrVal['empDrate']."\nAllowance Amt = ".$totalAllowAmnt."\n\n";
					
					/*Check the Allow Sked, para makuha ung data nya sa tblTimeSheetHist
					if(($allowArrVal['allowSked'] == '1')||($allowArrVal['allowSked'] == '2'))
					{
						switch (trim($allowArrVal['allowPayTag'])){
							case 'P':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'],$allowArrVal['empPayGrp'], $allowArrVal['empPayCat'],$qryGetTimeSheet,$dailyAmnt,trim($allowArrVal['attnBase']),$qryGetTimeSheet_Prev,$allowAmnt,$compNoDays);
							break;
							case 'T':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'],$allowArrVal['empPayGrp'], $allowArrVal['empPayCat'],$qryGetTimeSheet_Temp,$dailyAmnt,trim($allowArrVal['attnBase']),$qryGetTimeSheet_Prev_Temp,$allowAmnt,$compNoDays);
							break;
						}//end of switch case
					}
					
					If the AllowSked = BOTH, di na kailngan ksi BOTH binibigay
					else
					{
						
						switch (trim($allowArrVal['allowPayTag'])){
							case 'P':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'],$allowArrVal['empPayGrp'], $allowArrVal['empPayCat'],$qryGetTimeSheet,$dailyAmnt,trim($allowArrVal['attnBase']),'',(float)($allowAmnt/2),$compNoDays/2);
							break;
							case 'T':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'],$allowArrVal['empPayGrp'], $allowArrVal['empPayCat'],$qryGetTimeSheet_Temp,$dailyAmnt,trim($allowArrVal['attnBase']),'',(float)($allowAmnt/2),$compNoDays/2);
							break;
						}//end of switch case
					}*/
				break;
				
			}
				$arrChecker[] = $allowArrVal['empNo']."-".$allowArrVal['trnCode'];
				if(in_array($allowArrVal['empNo']."-".$allowArrVal['trnCode'],$arrChecker)){
					$arrAllow[ $allowArrVal['empNo']."-".$allowArrVal['trnCode']."-".$allowArrVal['allowCode'] ] += $totalAllowAmnt;
				}
			
			unset($totalAllowAmnt,$withTardy,$allowAmnt);		
		}//end of allowance
	
		return $arrAllow;
		//print_r($arrAllow);
		unset($arrAllow);
	}
	
	/**
	 * 
	 *  to table earnings
	 *
	 * @param  string $type
	 * @param  string $empNo
	 * @param  int    $tranCode
	 * @param  float  $tranAmount
	 * @return write to table earnings
	 */




	private function writeToTblEarnings($type,$empNo,$tranCode,$tranAmount){
			
		$taxCd = "";
		$writeToTblEarnings = "";
		$finalTaxTag = "";
		
		$taxCd = $this->getTrnTaxCode($this->session['company_code'],$tranCode,'E');//get tax code	
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
										   '{$tranCode}','".sprintf("%01.2f",$tranAmount)."','{$finalTaxTag}')";
		
		return $this->execQry($writeToTblEarnings);
	}
	
	
	private function writeToTblEarningsAllow($type,$empNo,$tranCode,$tranAmount,$separatePS){
			
		$taxCd = "";
		$writeToTblEarnings = "";
		$finalTaxTag = "";
		
		$taxCd = $this->getTrnTaxCode($this->session['company_code'],$tranCode,'E');//get tax code	
		if($type == 'E1' || $type == 'E3'){
			$finalTaxTag = $taxCd['trnTaxCd'];
		}elseif ($type == 'E2'){
			$finalTaxTag = 'Y';
		}
		
		$writeToTblEarnings = "INSERT INTO tblEarnings
										  (compCode,pdYear,
										  pdNumber,empNo,
										  trnCode,trnAmountE,trnTaxCd,sprtPS)
										  VALUES
										  ('{$this->session['company_code']}','{$this->get['pdYear']}',
										   '{$this->get['pdNum']}','{$empNo}',
										   '{$tranCode}','".sprintf("%01.2f",$tranAmount)."','{$finalTaxTag}','{$separatePS}')";
		
		return $this->execQry($writeToTblEarnings);
	}	
	######################################################END OF EARNINGS#####################################################################
	
	######################################################BEGIN OF DEDCUTIONS#################################################################
	###########################Government deduction########################################
	private function getEmpForDeduction(){
		$qryGetEmpForDeduct = "SELECT  tblEmpMast.empNo, tblEmpMast.empTeu, tblEmpMast.empMrate, tblEmpMast.empBrnCode, tblEmpMast.empWageTag
								FROM   tblEmpMast LEFT OUTER JOIN
								tblBranch ON tblBranch.brnCode = tblEmpMast.empBrnCode
								WHERE tblEmpMast.compCode = '{$this->session['company_code']}' 
								AND tblEmpMast.empPayGrp = '{$this->session['pay_group']}'
								AND tblEmpMast.empPayCat = '{$this->session['pay_category']}'
								AND tblEmpMast.empStat IN ('RG','PR','CN') ";

		$resGetEmpForDeduct = $this->execQry($qryGetEmpForDeduct);
		return $this->getArrRes($resGetEmpForDeduct);		
	}

	private function writeToTblDeduction($empNo,$tranCode,$tranAmount){
		
		$taxCd = "";
		$writeToTblDeductions = "";
		$finalTaxTag = "";
		
		$taxCd = $this->getTrnTaxCode($this->session['company_code'],$tranCode,'D');//get tax code	
		$taxCdfnl = $taxCd['trnTaxCd'];
		
		 $writeToTblDeductions = "INSERT INTO tblDeductions
										  (compCode,pdYear,
										  pdNumber,empNo,
										  trnCode,trnAmountD,trnTaxCd)
										  VALUES
										  ('{$this->session['company_code']}','{$this->get['pdYear']}',
										   '{$this->get['pdNum']}','{$empNo}',
										   '{$tranCode}','".sprintf("%01.2f",$tranAmount)."','{$taxCdfnl}')\n\n";
		
		return $this->execQry($writeToTblDeductions);
	}	
	
	private function computeEmpGrossEarnings($empNo,$and,$minWage){
		
		$qryGetEmpGrossEarn = "SELECT ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo, SUM(ern.trnAmountE) AS amountE
							   FROM tblEarnings ern 
							   LEFT OUTER JOIN tblPayTransType trn 
							   ON ern.compCode = trn.compCode AND ern.trnCode = trn.trnCode
							   WHERE (ern.compCode = '{$this->session['company_code']}') 
							   AND (ern.pdYear = '{$this->get['pdYear']}') 
							   AND (ern.pdNumber = '{$this->get['pdNum']}') 
							   AND (ern.EmpNo = '{$empNo}') ";
		if($and!= ""){
        	$qryGetEmpGrossEarn.= $and;                  				
		}
		
		
		$qryGetEmpGrossEarn	.= "GROUP BY ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo ";
		
		$resGetEmpGrossEarn = $this->execQry($qryGetEmpGrossEarn);
		$rowGetEmpGrossEarn = $this->getSqlAssoc($resGetEmpGrossEarn);
		
		return $rowGetEmpGrossEarn['amountE'];
	}
	
	private function separatePaySlipAllow($empNo,$and,$minWage){
		
		$qryGetSprtPS = "SELECT ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo, SUM(ern.trnAmountE) AS sprtPSAllwAmnt
						FROM tblEarnings ern 
						LEFT OUTER JOIN tblPayTransType trn 
						ON ern.compCode = trn.compCode AND ern.trnCode = trn.trnCode
						WHERE (ern.compCode = '{$this->session['company_code']}') 
						AND (ern.pdYear = '{$this->get['pdYear']}') 
						AND (ern.pdNumber = '{$this->get['pdNum']}') 
						AND (ern.EmpNo = '{$empNo}') ";
		if($and!= ""){
        	$qryGetSprtPS.= $and;                  				
		}		
		$qryGetSprtPS	.= "GROUP BY ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo ";
		
		$resGetSprtPS = $this->execQry($qryGetSprtPS);
		return $this->getArrRes($resGetSprtPS);
	}	
	
	private function getPrevGrossEarn($empNo){
		
		$getPdPartner = array_search($this->get['pdNum'],$this->getArrPdNumPartner());
		
		$qryGetPrevGrossEarn = "SELECT grossEarnings FROM tblPayrollSummaryHist 
								WHERE compCode = '{$this->session['company_code']}'
								AND pdYear = '{$this->get['pdYear']}'
								AND pdNumber = '{$getPdPartner}'
								AND empNo = '{$empNo}'
								AND payGrp = '{$this->session['pay_group']}'
								AND payCat = '{$this->session['pay_category']}' ";
		$resGetPrevGrossEarn = $this->execQry($qryGetPrevGrossEarn);
		$rowGetPrevGrossEarn = $this->getSqlAssoc($resGetPrevGrossEarn);
		return $rowGetPrevGrossEarn['grossEarnings'];
	}

	private function getGovDedAmnt($monthlyGrossEarn){
		$qryGetGovDed = "SELECT * FROM tblSssPhic WHERE 
						 ($monthlyGrossEarn BETWEEN  sssLowLimit AND sssUpLimit)";
		$resGetGovDed = $this->execQry($qryGetGovDed);
		return $this->getSqlAssoc($resGetGovDed);
	}
	
	private function getSumGov($empNo){
		
		$qryGetSumGov = "SELECT sssEmp,phicEmp,hdmfEmp FROM tblMtdGovt
						 WHERE compCode = '{$this->session['company_code']}'
						 AND pdYear = '{$this->get['pdYear']}'
						 AND pdMonth = '{$this->get['pdMonth']}'
						 AND empNo = '{$empNo}'";
		$resGetSumGov = $this->execQry($qryGetSumGov);
		return  $this->getSqlAssoc($resGetSumGov);

	}
	###########################end of Government deduction########################################
	###########################taxwitheld computetion########################################
	/* Gen's Function */
	
	/*Allowance Function, Get TimeSheet Record*/
	private function GetTimeSheetRecord($empNo,$empPayGrp, $empPayCat, $where,$dailyAmnt,$attnBase,$where_prev, $allwAmnt, $compNoDays)
	{
		
		$totalAllowAmnt = 0;
		$withTardy = $empNoWorkingDays = $empNoAbsences = $empNoHalfDays = $empSumTardUt = $empAllowAmt = 0;
		$sumAllowAmt_WD = $sumAllowAmt_ABS = $sumAllowAmt_HDAY = $sumAllowAmt_STARDUT = $totEmpNoAbsences =  0;
		
		$qryGetAllwTs = "SELECT tsDate,hrsAbsent,hrsTardy,hrsUt FROM tblTimeSheet
							WHERE compCode = '".$_SESSION["company_code"]."' 
							AND empNo = '".$empNo."'
							AND empPayGrp = '".$empPayGrp."'
							AND empPayCat = '".$empPayCat."'";
		if($where!="")
			$qryGetAllwTs.= $where;
		
		$resGetAllwTs = $this->execQry($qryGetAllwTs);	
		
		$arr_empRestDay = $this->getUserInfo($_SESSION["company_code"],$empNo,'');	
		$empRestDay = explode(",",$arr_empRestDay["empRestDay"]);		
		
		foreach ((array)$this->getArrRes($resGetAllwTs) as $tsResVal){
		
			if(!in_array(date("m/d/Y", strtotime($tsResVal["tsDate"])),$empRestDay))
				$empNoWorkingDays++;
			
			if($tsResVal["hrsAbsent"]=='8')
			{
				$empNoAbsences++;
				//echo date("m/d/Y", strtotime($tsResVal["tsDate"]))."\n";
			}
			
			if($tsResVal["hrsAbsent"]=='4')
				$empNoHalfDays++;
			
			if($attnBase=="Y"){
				$empSumTardUt+=$tsResVal["hrsTardy"]+$tsResVal["hrsUt"];
			}	
		}
		
		/*Previous Time Sheet*/
		if($where_prev!=""){
			
			$qryGetAllwTs_Prev = "SELECT tsDate,hrsAbsent,hrsTardy,hrsUt FROM tblTimeSheetHist
								WHERE compCode = '".$_SESSION["company_code"]."' 
								AND empNo = '".$empNo."'
								AND empPayGrp = '".$empPayGrp."'
								AND empPayCat = '".$empPayCat."'";
			$qryGetAllwTs_Prev.= $where_prev;
			
			
			$resGetAllwTs_Prev = $this->execQry($qryGetAllwTs_Prev);
			if($this->getRecCount($resGetAllwTs_Prev)>0){	
				foreach ((array)$this->getArrRes($resGetAllwTs_Prev) as $tsResVal_Prev){
				
					if(!in_array(date("m/d/Y", strtotime($tsResVal_Prev["tsDate"])),$empRestDay))
						$empNoWorkingDays++;
					
					if($tsResVal_Prev["hrsAbsent"]=='8')
					{
						//echo date("m/d/Y", strtotime($tsResVal_Prev["tsDate"]))."\n";
						$empNoAbsences++;
					}
					
					if($tsResVal_Prev["hrsAbsent"]=='4')
						$empNoHalfDays++;
					
					if($attnBase=="Y"){
						$empSumTardUt+=$tsResVal_Prev["hrsTardy"]+$tsResVal_Prev["hrsUt"];
					}	
				}	
			}else{
				$empNoAbsences+= 12;
			}	
		}
		
		
		
		/*$sumAllowAmt_WD = (float) $dailyAmnt * $empNoWorkingDays;
		$sumAllowAmt_STARDUT = (float) ($dailyAmnt/8) * $empSumTardUt;
		$sumAllowAmt_ABS = (float) $dailyAmnt * $empNoAbsences;
		$sumAllowAmt_HDAY = (float) ($dailyAmnt/2) * $empNoHalfDays;*/
		
		/*Computation of Allowances*/
		if($attnBase=="Y")
		{
			$empAllowAmt = ((($compNoDays -  $empNoAbsences - (($empNoHalfDays*4)/8) - ($empSumTardUt/8)) /$compNoDays)*$allwAmnt);
			
		}
		else
		{
			$totEmpNoAbsences = $empNoAbsences + ($empNoHalfDays/2);
			if(($totEmpNoAbsences>12)||($where_prev=="")){
				$empAllowAmt = ((($compNoDays -  $empNoAbsences - (($empNoHalfDays*4)/8)) /$compNoDays)*$allwAmnt);
			}
			else{
				$empAllowAmt = $allwAmnt;
			}
		}	
		
		$empAllowAmt = ($empAllowAmt<0?0:sprintf("%01.2f",$empAllowAmt));
		return (float)$empAllowAmt;
		//echo $empNo."=".$arr_empRestDay["empRestDay"]."\n".$empNoAbsences."\n".$empNoHalfDays."\n".$empSumTardUt."\nAllowance Amt = ".$empAllowAmt."\n\n";
		
	}
	
	/*Unposted Data Transaction Object*/
	private function unPostedTranOthEarn()
	{

		$qryGetOthEarn =  "SELECT dtl.compCode,empNo, dtl.trnCode, trnAmount AS sumEarnAmnt, pdNumber, pdYear, trnCntrlNo,dtl.refNo
							FROM tblEarnTranDtl dtl, tblEarnTranHeader hdr
							WHERE (dtl.compCode = '".$_SESSION["company_code"]."') 
							  AND (dtl.processtag is null or dtl.processtag='U')
							  AND (dtl.payGrp = '".$_SESSION["pay_group"]."') 
							  AND (dtl.payCat = '".$_SESSION["pay_category"]."')
							  AND (dtl.earnStat = 'A')
							  AND (dtl.trnCode IN (Select trnCode from tblPayTransType where trnApply IN (".$this->getCutOffPeriod().",3)))
							  AND dtl.refNo=hdr.refNo 
							  AND hdr.pdNumber='".$this->get['pdNum']."' 
							  AND hdr.pdYear='".$this->get['pdYear']."' 
							  AND hdr.earnStat='A'";
						 
		$resGetOthEarn = $this->execQry($qryGetOthEarn);
		return $this->getArrRes($resGetOthEarn);
	}
	
	function unPostedTranOthDed()
	{

		$qryGetOthEarn =  "SELECT dtl.compCode,empNo, dtl.trnCode, trnAmount AS sumDedAmnt,ActualAmt, pdNumber, pdYear, trnCntrlNo,dtl.refNo
							FROM tblDedTranDtl dtl, tblDedTranHeader hdr
							WHERE (dtl.compCode = '".$_SESSION["company_code"]."') 
							  AND (dtl.processtag is null or dtl.processtag='P')
							  AND (dtl.payGrp = '".$_SESSION["pay_group"]."') 
							  AND (dtl.payCat = '".$_SESSION["pay_category"]."')
							  AND (dtl.dedStat = 'A')
							  AND (dtl.trnCode IN (Select trnCode from tblPayTransType where trnApply IN (".$this->getCutOffPeriod().",3)))
							  AND dtl.refNo=hdr.refNo 
							  AND hdr.pdNumber='".$this->get['pdNum']."' 
							  AND hdr.pdYear='".$this->get['pdYear']."' 
							  AND hdr.dedStat='A'
							  ";
		
		$resGetOthEarn = $this->execQry($qryGetOthEarn);
		return $this->getArrRes($resGetOthEarn);
	}
	
	
	private function writeToTblUnpostedTran($empCompCd,$empId,$trnCd,$trnAt,$ActualAmt,$pdNum,$pdYr,$trnCntNo,$refNo)
	{
		$writeToTblUnpostedTran = "INSERT INTO tblUnpostedTran
							  (compCode,empNo,trnCode,trnAmt,trnActualAmt,pdNumber,pdYear,trnCntrlNo,dateAdded,refNo)
							  VALUES('".$empCompCd."','".$empId."','".$trnCd."','".sprintf("%01.2f",$trnAt)."','".sprintf("%01.2f",$ActualAmt)."','".$pdNum."','".$pdYr."','".$trnCntNo."','".date("m/d/Y")."','".$refNo."')";

		return $this->execQry($writeToTblUnpostedTran);
	}
	
	
	private function getCompAnnDate($compCode)
	{
		$qrygetCompAnnDate = "Select * from tblCompany where compCode='".$compCode."'";
		$resgetCompAnnDate = $this->execQry($qrygetCompAnnDate);
		
		return  $this->getSqlAssoc($resgetCompAnnDate);
	}
	
	private function getEmpYtdDataHist($empNo)
	{
		$qrygetEmpYtdDataHist = "Select * from tblYtdDataHist where empNo='".$empNo."' and pdYear='".$this->get['pdYear']."' and compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."'";
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
	
	private function getTaxExemption($empTeu)
	{
		$qryGetTaxExempt = "Select teuAmt from tblTeu where teuCode='".$empTeu."'";
		$resGetTaxExempt = $this->execQry($qryGetTaxExempt);
		$rowGetTaxExempt = $this->getSqlAssoc($resGetTaxExempt);
		return $rowGetTaxExempt['teuAmt'];
	}
	
	private function getAnnualTax($taxInc)
	{
		$qrycomputeWithTax = "Select * from tblAnnTax where $taxInc between txLowLimit and txUpLimit";
		$rescomputeWithTax = $this->execQry($qrycomputeWithTax);
		$rowcomputeWithTax = $this->getSqlAssoc($rescomputeWithTax);
		$compTax = ((($taxInc-$rowcomputeWithTax["txLowLimit"])*$rowcomputeWithTax["txAddPcent"])+$rowcomputeWithTax["txFixdAmt"]);
		
		return (float)$compTax;
	}
	
	private function computeWithTax($empNo,$gross_Taxable,$empTeu,$sumGov,$minBasicPay)
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
		$arrEmpInfo = $this->getUserInfo($_SESSION["company_code"],$empNo,'');
		$empPrevTag = $arrEmpInfo["empPrevTag"];
		$empMinTag = $arrEmpInfo["empWageTag"];
		
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
		if($empMinTag=='Y')
		{
			
			/*echo 	$empNo."==".$gross_Taxable."+".$arrYtdDataHist["YtdTaxable"]."+".$empPrevEarnings."-".
					$arrYtdDataHist["YtdGovDed"]."-".$arrYtdDataHist["YtdBasic"]."-".$basicPay."-".$sumGov."\n";*/
			
			$estEarn = 	  (float) $gross_Taxable + (float) $arrYtdDataHist["YtdTaxable"] + (float)$empPrevEarnings -  (float) $arrYtdDataHist["YtdGovDed"] - (float) $arrYtdDataHist["YtdBasic"] 
					 	- (float) $basicPay - (float)$sumGov;
			
			$estEarn = (float) $estEarn / $this->get['pdNum'];
			$estEarn = (float) $estEarn * 24 ;
		}
		else
		{
			/*echo 	$empNo."==".$gross_Taxable."+".$arrYtdDataHist["YtdTaxable"]."+".$empPrevEarnings."-".
					$arrYtdDataHist["YtdGovDed"]."-".$sumGov."\n";*/
			
			$estEarn = 	  (float) $gross_Taxable + (float) $arrYtdDataHist["YtdTaxable"] + (float)$empPrevEarnings -  (float) $arrYtdDataHist["YtdGovDed"] - (float)$sumGov;
		
			$estEarn = (float) $estEarn / $this->get['pdNum'];
			$estEarn = (float) $estEarn * 24 ;
		}
		
		
		//Compute for the Net Taxable Earnings
		$netTaxable = (float) $estEarn - (float) $this->getTaxExemption($empTeu);
	
		//Compute the Estimated Tax using the Annual Tax Table
		$estTaxYear = $this->getAnnualTax($netTaxable);
		
		//Compute Taxes
		$taxDue = ($estTaxYear / 24)* $this->get['pdNum'];
		
		$taxPeriod = $taxDue -  $arrYtdDataHist["YtdTax"] - $empPrevTaxes;
		
		$taxPeriod = ($taxPeriod<0?0:$taxPeriod);
		return (float) $taxPeriod ;
		
	}
	
	/*End of Gen's Function*/
	###########################end of taxwitheld computetion########################################
	###########################for loans and other dedcutions########################################
	//will program 
	function getdeductionlist($empNo,$lonRefNo="") {
		if ($lonRefNo != "") {
			$amountfield = "trnAmountD";
			$dedtag = " AND (dedTag not IN ('Y','P'))";
		} else {
			$amountfield = "ActualAmt";
			$dedtag = "AND (dedTag IN ('Y','P'))";
		}
		$qryloans="SELECT tblEmpLoansDtl.lonTypeCd, SUM(tblEmpLoansDtl.$amountfield) AS sumamount, tblLoanType.trnCode, 
                      tblEmpLoansDtl.empNo $lonRefNo
					  FROM tblEmpLoansDtl INNER JOIN
                      tblLoanType ON tblEmpLoansDtl.compCode = tblLoanType.compCode AND 
                      tblEmpLoansDtl.lonTypeCd = tblLoanType.lonTypeCd
					WHERE (tblEmpLoansDtl.compCode = '" . $this->session['company_code'] . "')
					AND (tblEmpLoansDtl.pdYear = '" . $this->get['pdYear'] . "') 
					AND (tblEmpLoansDtl.pdNumber = '" . $this->get['pdNum'] . "')
					AND (tblEmpLoansDtl.empNo = '$empNo') $dedtag
					AND (tblEmpLoansDtl.lonRefNo IN
                          (SELECT lonRefNo FROM tblEmpLoans
                            WHERE lonsked IN (3, {$this->getCutOffPeriod()})))
					GROUP BY tblEmpLoansDtl.lonTypeCd, tblEmpLoansDtl.empNo, tblLoanType.trnCode $lonRefNo 
					ORDER BY tblEmpLoansDtl.empNo, tblEmpLoansDtl.lonTypeCd";
		return $this->getArrRes($this->execQry($qryloans));	
		
	}
	
	function getotherdeductionlist($empNo,$cat){
		if ($cat==1) {
			$qryother="select ActualAmt,trnAmount,trnCode,seqNo,empNo from tblDedTranDtl 
						where empNo='$empNo' 
						and compCode='" . $this->session['company_code'] . "' 
						and dedStat='A' and payGrp='" . $this->session['pay_group'] . "' 
						and payCat='" . $this->session['pay_category'] . "' 
						and processtag is NULL
						and refNo IN (select refNo from tblDedTranHeader 
										where compCode = '".$_SESSION['company_code']."' 
										and (pdYear = '" . $this->get['pdYear'] . "') 
										and (pdNumber = '" . $this->get['pdNum'] . "')										
										)
						and (trnCode in 
												(SELECT trnCode FROM tblPayTransType where trnApply in (3,{$this->getCutOffPeriod()})))
						and (trnCode not in (SELECT trnCode FROM tblLoanType where compCode='" .  $this->session['company_code'] . "'))						
						order by trnPriority";
			
		}
		elseif ($cat==0) {
			$qryother="select processtag, SUM(ActualAmt) AS sumamount, empNo, trnCode, compCode, trnPriority from tblDedTranDtl 
						where empNo='$empNo' 
						and compCode='" . $this->session['company_code'] . "' 
						and dedStat='A' 
						and payGrp='" . $this->session['pay_group'] . "' 
						and payCat='" . $this->session['pay_category'] . "' 
						and processtag IN ('Y','P')
						and (trnCode in 
										(SELECT trnCode FROM tblPayTransType where trnApply in (3,{$this->getCutOffPeriod()})))
						and (trnCode not in (SELECT trnCode FROM tblLoanType where compCode='" .  $this->session['company_code'] . "'))				
						GROUP BY processtag, empNo, trnCode, compCode, trnPriority
						order by trnPriority";
		
		}
		return $this->getArrRes($this->execQry($qryother));
	}
	
	function updateotherdeductions($seqNo,$Amount,$tag) {
		$qryupdateotherdeductions="Update tblDedTranDtl set ActualAmt='$Amount',processtag='$tag' where seqNo='$seqNo'"; 
		return $this->execQry($qryupdateotherdeductions);
	}
	
	function updateloansdtl($empNo,$lonCd,$Amount,$lonRefNo,$tag) {
		$qryupdateloansdtl="Update tblEmpLoansDtl set dedTag='$tag',ActualAmt='$Amount'
							WHERE (tblEmpLoansDtl.compCode = '" . $this->session['company_code'] . "') 
									AND (tblEmpLoansDtl.pdYear = '" . $this->get['pdYear'] . "') 
							        AND (tblEmpLoansDtl.pdNumber = '" . $this->get['pdNum'] . "')
									AND (lonTypeCd='$lonCd')
							        AND (tblEmpLoansDtl.empNo = '$empNo')
							        AND (lonRefNo = '$lonRefNo')
									AND (tblEmpLoansDtl.lonTypeCd IN (SELECT tblEmpLoans.lonTypeCd  FROM tblEmpLoans where
																	 tblEmpLoans.lonSked IN (3,{$this->getCutOffPeriod()})))";
		return $this->execQry($qryupdateloansdtl);
	}
	//end of will program		
	###########################end for loans and other dedcutions########################################
	######################################################END OF DEDUCTIONS###################################################################
	
	function reProcRegPayroll(){
		
		$TrnsA = $this->beginTran();
		
		$qryDeleAllowBrkDwn = "DELETE FROM tblAllowanceBrkDwn 
								WHERE compCode = '{$this->session['company_code']}'
								AND empNo IN (
												SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
												AND empPayGrp = '{$this->session['pay_group']}'
												AND empPayCat = '{$this->session['pay_category']}'
												AND empStat IN ('RG','PR','CN')
											  )
								AND allowSked IN (".$this->getCutOffPeriod().",3)";
		if($TrnsA){
			$TrnsA = $this->execQry($qryDeleAllowBrkDwn);
		}	

		 $qryDeleEan = "DELETE FROM tblEarnings 
		               WHERE compCode = '{$this->session['company_code']}'
					   AND empNo IN (
										SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
										AND empPayGrp = '{$this->session['pay_group']}'
										AND empPayCat = '{$this->session['pay_category']}'
										AND empStat IN ('RG','PR','CN')
					   			    ) ";
		if($TrnsA){
			$TrnsA = $this->execQry($qryDeleEan);
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
		if($TrnsA){
			$TrnsA = $this->execQry($qryDeleDeductions);
		}

		$qryDeleMtdGovt = "DELETE FROM tblMtdGovt 
						   WHERE compCode = '{$this->session['company_code']}'
						   AND pdYear = '{$this->get['pdYear']}'
						   AND pdMonth = '{$this->get['pdMonth']}'
						   AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
						                )"; 
		if($TrnsA){
			$TrnsA = $this->execQry($qryDeleMtdGovt);
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
			if($TrnsA){
			$TrnsA = $this->execQry($qryDeleYtdData);
		}
	
		$qryDelePaySum = "DELETE FROM tblPayrollSummary 
						  WHERE compCode = '{$this->session['company_code']}'
						  AND pdYear = '{$this->get['pdYear']}'
						  AND pdNumber = '{$this->get['pdNum']}'
	  					  AND payGrp = '{$this->session['pay_group']}'
						  AND payCat = '{$this->session['pay_category']}'";
		if($TrnsA){
			$TrnsA = $this->execQry($qryDelePaySum);	
		}		
		
$qryUpdateEmpLoans = "UPDATE tblEmpLoansDtl SET dedTag = ''
							  WHERE (tblEmpLoansDtl.compCode = '" . $this->session['company_code'] . "') 
									AND (tblEmpLoansDtl.pdYear = '" . $this->get['pdYear'] . "') 
							        AND (tblEmpLoansDtl.pdNumber = '" . $this->get['pdNum'] . "')
									AND (dedTag IN ('Y','P'))
									AND (tblEmpLoansDtl.lonTypeCd IN (SELECT tblEmpLoans.lonTypeCd  FROM tblEmpLoans where
																	 tblEmpLoans.lonSked IN (3,{$this->getCutOffPeriod()})))		
									";	
		if($TrnsA){
			$TrnsA = $this->execQry($qryUpdateEmpLoans);	
		}	

		$qryUpdtDedTran = "UPDATE tblDedTranDtl SET processTag = NULL
						where compCode='" . $this->session['company_code'] . "' 
						and dedStat='A' 
						and payGrp='" . $this->session['pay_group'] . "' 
						and payCat='" . $this->session['pay_category'] . "' 
						and processtag IN ('Y','P')
						and (trnCode in 
										(SELECT trnCode FROM tblPayTransType where trnApply in (3,{$this->getCutOffPeriod()})))
						";
		if($TrnsA){
			$TrnsA = $this->execQry($qryUpdtDedTran);	
		}
		
		/*Unposted Transactions*/
		$qryDeleUnpostedTran = 	"Delete from tblUnpostedTran
								where empNo in (Select empNo from tblEmpMast where
								compCode='".$this->session['company_code']."' and 
								empPayCat='".$this->session['pay_category']."' and empPayGrp='".$this->session['pay_group']."' and 
								empStat IN ('RG','PR','CN'))
								and compCode='".$this->session['company_code']."' 
								and pdYear='".$this->get['pdYear']."' and pdNumber='".$this->get['pdNum']."'";
		
		if($TrnsA){
			$TrnsA = $this->execQry($qryDeleUnpostedTran);	
		}		
		
		if(!$TrnsA){
			$TrnsA = $this->rollbackTran();//rollback regular payroll transaction
			return false;
		}
		else{
			$TrnsA = $this->commitTran();//commit regular payroll transaction
			return true;	
		}
	}
	
	
	public function mainProcRegPayroll(){
		
		$sumGov = 0;
		$compEarnretain = $this->getCompany($this->session['company_code']);//company retention
		$earnRetention = (float)$compEarnretain['compEarnRetain'];//earn retention
		$totDedForPeriod =0;
		$totalGovDeducted =0;
		$positiveEarn = 0;
		$emp_AccruedDays =  array();
		$emp_AmntTardy =  array();
		$emp_AmntUt = array();
		
		$Trns = $this->beginTran();//begin regular payroll transaction
		
		
		foreach ((array)$this->summarizeCorrection() as $arrSumCorrVal){//foreach timesheet	
			
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
						$emp_AmntTardy[$arrSumCorrVal['empNo']] = $arrSumCorrVal['sumAmtTardy'];
					}		
				}	
			}		

			if($arrSumCorrVal['sumAmtUt']){
				if((float)$arrSumCorrVal['sumAmtUt'] != 0){								
					if($Trns){
						$Trns = $this->writeToTblEarnings('E1',$arrSumCorrVal['empNo'],'0112',$arrSumCorrVal['sumAmtUt']);
						$emp_AmntUt[$arrSumCorrVal['empNo']] = $arrSumCorrVal['sumAmtUt'];
					}	
				}	
			}			
		}//end of foreach timeesheet	
		

		foreach ((array)$this->summarizeOtAndNd() as $arrSumOtNNdVal){//foreach overtime and night diferential
		
			if($arrSumOtNNdVal['dayType'] == '01'){

				$regOtAmt = (float)$arrSumOtNNdVal['sumAmtOtLe8']+(float)$arrSumOtNNdVal['sumAmtOtGt8'];
				
				if($regOtAmt != 0){

					if($Trns){
						
						$Trns = $this->writeToTblEarnings('E2',$arrSumOtNNdVal['empNo'],'0221',$regOtAmt);
					}				
				}
				
				$regNdAmnt = (float)$arrSumOtNNdVal['sumAmtNdLe8']+(float)$arrSumOtNNdVal['sumAmtNdGt8'];
				if($regNdAmnt != 0){
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$arrSumOtNNdVal['empNo'],'0327',$regNdAmnt);
					}				
				}				
				
			}
			else{
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
			}			
			unset($regOtAmt,$regNdAmnt);
		}//end foreach overtime and night diferential
			
					
		foreach ((array)$this->getEmpToProcPayBasic() as $empToProcPayBaicVal){//foreach pay basics
			
		
			if(trim($empToProcPayBaicVal['empPayType']) == 'M'){//monthly
				if(trim($empToProcPayBaicVal['empStat']) == 'RG'){//regular
					$trnCodePayBasicMreg = '0100';
					$trnAmountPayBasicMreg = $this->OneHalfBasic($empToProcPayBaicVal['empMrate']);	
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicMreg,$trnAmountPayBasicMreg);			
					}
				}
				else{//probationary or contractual
					//if datehired is less or equal to date period from do one half basic procedure
					
					if(strtotime(date('m/d/Y',strtotime($this->dateFormat($empToProcPayBaicVal['dateHired'])))) <= strtotime(date('m/d/Y',strtotime($this->get['dtFrm'])))){
						$trnCodePayBasicNotReg = '0100';
						$trnAmountPayBasicNotReg = $this->OneHalfBasic($empToProcPayBaicVal['empMrate']);
						
					}
					
					else{//if datehired is greater than to date period  from do pro rate basic
						$trnCodePayBasicNotReg = '0100';
						//$trnAmountPayBasicNotReg = $this->proRateBasic($empToProcPayBaicVal['empMrate'],$empToProcPayBaicVal['empDrate'],$empToProcPayBaicVal['dateHired']);				
							$trnAmountPayBasicNotReg = $this->proRateBasic($empToProcPayBaicVal['empNo'],$empToProcPayBaicVal['empDrate'],$empToProcPayBaicVal['dateHired']);
						
					}	
					if($Trns){				
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicNotReg,$trnAmountPayBasicNotReg);	
					}
				}
			}//end of monthly
			else{//daily
				
				//basic	
				$arrDailiesBasic = explode("-",$this->getDailiesBasic($empToProcPayBaicVal['empNo'],$empToProcPayBaicVal['empDrate']));
				$trnCodePayBasicDailiesBasic = $arrDailiesBasic[0];
				$trnAmountPayBasicDailiesBasic = $arrDailiesBasic[1];
				$emp_AccruedDays[$empToProcPayBaicVal['empNo']] = $arrDailiesBasic[2];
				
				if($trnAmountPayBasicDailiesBasic > 0){
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDailiesBasic,$trnAmountPayBasicDailiesBasic);
					}
				}

				//legal holiday
				$arrDailiesLegHoliday = explode("-",$this->getDailiesLegHoliday($empToProcPayBaicVal['empNo'],$empToProcPayBaicVal['empDrate']));
				$trnCodePayBasicDaliesLegHoliday = $arrDailiesLegHoliday[0];
				$trnAmountPayBasicDaliesLegHoliday = $arrDailiesLegHoliday[1];
				if($trnAmountPayBasicDaliesLegHoliday > 0){
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDaliesLegHoliday,$trnAmountPayBasicDaliesLegHoliday);
					}
				}

				//special holiday
				$arrDailiesSpHoliday = explode("-",$this->getDailiesSpHol($empToProcPayBaicVal['empNo'],$empToProcPayBaicVal['empDrate']));
				$trnCodePayBasicDaliesSpHoliday = $arrDailiesSpHoliday[0];
				$trnAmountPayBasicSpHoliday = $arrDailiesSpHoliday[1];
				if($trnAmountPayBasicSpHoliday > 0){
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDaliesSpHoliday,$trnAmountPayBasicSpHoliday);
					}
				}
			}//end of daily
			
			unset($trnAmountPayBasicNotReg);
		}//end of foreach pay basic		
		
		
		foreach ((array)$this->postAdjustmentOthers(">") as $arrPostAdjOthrsValPos){//foreach for post adjustment and others positive sign
			if($Trns){
				$qryUpdtProctagOthAdj = "UPDATE tblEarnTranDtl 
								   SET processTag = 'Y' 
								   WHERE compCode = '{$this->session['company_code']}'
								   AND empNo='".$arrPostAdjOthrsValPos['empNo']."' and trnCode='".$arrPostAdjOthrsValPos['trnCode']."' 
								   and payCat='".$_SESSION["pay_category"]."' and payGrp='".$_SESSION["pay_group"]."'";
				
				$Trns = $this->execQry($qryUpdtProctagOthAdj);
			
				$Trns = $this->writeToTblEarnings('E3',$arrPostAdjOthrsValPos['empNo'],$arrPostAdjOthrsValPos['trnCode'],$arrPostAdjOthrsValPos['sumEarnAmnt']);
			}
		}//end foreach for post adjustment and others positive sign
		
		
		$sumPostvErn = 0;
		$ctr=0;
		$checker = array();
		foreach ((array)$this->postAdjustmentOthers("<") as $arrPostAdjOthrsValNeg){//foreach for post adjustment and others negative sign
					if(!in_array($arrPostAdjOthrsValNeg['empNo'],$checker)){
						$rowSumPosEarn = $this->getSumPositiveEarnings($arrPostAdjOthrsValNeg['empNo']);
						$sumPostvErn = (float)$rowSumPosEarn['sumPostvEarn'];
					}
					$checker[] = $arrPostAdjOthrsValNeg['empNo'];
										
					$sumPostvErn += (float)$arrPostAdjOthrsValNeg['sumEarnAmnt'];
					if($sumPostvErn > 0){
						
						$qryCheckOtherEarn = "SELECT trnAmountE FROM tblEarnings 
											  WHERE compCode = '{$_SESSION['company_code']}' 
											  AND pdYear = '{$this->get['pdYear']}'
											  AND pdNumber = '{$this->get['pdNum']}'
											  AND empNo = '".trim($arrPostAdjOthrsValNeg['empNo'])."'
											  AND trnCode = '{$arrPostAdjOthrsValNeg['trnCode']}'";
						$resCheckOtherEarn = $this->execQry($qryCheckOtherEarn);
						$rowCheckOtherEarn = $this->getSqlAssoc($resCheckOtherEarn);
					
						
						if($this->getRecCount($resCheckOtherEarn) > 0){
							
							$finalAmountE = (float)$arrPostAdjOthrsValNeg['sumEarnAmnt']+(float)$rowCheckOtherEarn['trnAmountE'];
							$qryUpdtTblEarn = "UPDATE tblEarnings SET trnAmountE = '".sprintf("%01.2f",$finalAmountE)."'
											   WHERE compCode = '{$_SESSION['company_code']}'
											   AND pdYear = '{$this->get['pdYear']}'
											   AND pdNumber = '{$this->get['pdNum']}'
											   AND empNo = '{$arrPostAdjOthrsValNeg['empNo']}'
											   AND trnCode = '{$arrPostAdjOthrsValNeg['trnCode']}' ";
							if($Trns){
								$Trns = $this->execQry($qryUpdtTblEarn);
							}
							unset($finalAmountE);
						}
						else{
							$Trns = $this->writeToTblEarnings('E3',$arrPostAdjOthrsValNeg['empNo'],$arrPostAdjOthrsValNeg['trnCode'],$arrPostAdjOthrsValNeg['sumEarnAmnt']);
							
						}
						$qryUpdtProctag = "UPDATE tblEarnTranDtl 
									   SET processTag = 'Y' 
									   WHERE compCode = '{$this->session['company_code']}'
									   AND empNo='".$arrPostAdjOthrsValNeg['empNo']."' and trnCode='".$arrPostAdjOthrsValNeg['trnCode']."' and payCat='".$_SESSION["pay_category"]."' and payGrp='".$_SESSION["pay_group"]."'";
						if($Trns){
							$Trns = $this->execQry($qryUpdtProctag);
						}
					}
					else{
						$arOthrEarnNotDeductd = $this->getRefNoOtherAdj($arrPostAdjOthrsValNeg['empNo'],$arrPostAdjOthrsValNeg['trnCode']);
						foreach ($arOthrEarnNotDeductd as $othrEarnNotDeductdVal){
							$qryUpdtProctag = "UPDATE tblEarnTranDtl 
											   SET processTag = 'U' 
											   WHERE compCode = '{$this->session['company_code']}'
											   AND refNo = '{$othrEarnNotDeductdVal['refNo']}'
											   and empNo='".$arrPostAdjOthrsValNeg['empNo']."'";
							
							if($Trns){
								$Trns = $this->execQry($qryUpdtProctag);
							}
						}
					}
		}//end foreach for post adjustment and others negative sign
		
		
		
		
		foreach ((array)$this->getArrAllow($emp_AccruedDays,$emp_AmntTardy,$emp_AmntUt) as $arrAllwIndex => $arrAllwVal){//foeach for allowance
		
			
			$tmpAllwIndex = explode("-",$arrAllwIndex);
			$arrEmpAllow = $this->getEmpAllowance('tblAllowance',$tmpAllwIndex[0],'AND tblAllowance.allowCode = '.$tmpAllwIndex[2]);
			
			if((float)$arrAllwVal != 0){
				if($Trns){
					
					
					$qryToAllowBrkDwn = "INSERT INTO tblAllowanceBrkDwn(compCode,empNo,allowCode,allowAmt,allowSked,allowTaxTag,allowPayTag,allowStart,allowEnd,allowStat,pdYear,pdNumber,actualAmt,sprtPS)
										VALUES('{$arrEmpAllow[0]['compCode']}',
											   '{$arrEmpAllow[0]['empNo']}',
											   '{$arrEmpAllow[0]['allowCode']}',
											   '".sprintf("%01.2f",$arrAllwVal)."',
											   '{$arrEmpAllow[0]['allowSked']}',
											   '".trim($arrEmpAllow[0]['allowTaxTag'])."',
											   '{$arrEmpAllow[0]['allowPayTag']}',
											   '".$this->dateFormat($arrEmpAllow[0]['allowStart'])."',
											   '".$this->dateFormat($arrEmpAllow[0]['allowEnd'])."',
											   '{$arrEmpAllow[0]['allowStat']}',
											   '{$this->get['pdYear']}',
											   '{$this->get['pdNum']}',
											   '{$arrEmpAllow[0]['allowAmt']}',
											   '{$arrEmpAllow[0]['sprtPS']}')";	
				
					$Trns = $this->execQry($qryToAllowBrkDwn);	
				}
			}
		
			if((float)$arrAllwVal != 0){
				if($Trns){
					$Trns = $this->writeToTblEarningsAllow('E1',$tmpAllwIndex[0],$tmpAllwIndex[1],sprintf("%01.2f",$arrAllwVal),$arrEmpAllow[0]['sprtPS']);
				}
			}
			unset($arrAllwVal,$arrEmpAllow,$tmpAllwIndex);
		}//end of foreach for allowance
		
		
			
		foreach ((array)$this->getEmpForDeduction() as $empForDedVal){//foreach for deductions
			$totalTaxDeducted =0;
			
			//echo $empForDedVal['empNo']."\n";
			
			/*gross earnings = taxable and non taxable except allowance*/
			$grossEarnings = (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND (sprtPS IS NULL or sprtPS ='' or sprtPS='N') ",'');
			
			//echo "Gross Earnings = ".$grossEarnings."\n\n";
			
			/*net pay earnings =company retention/100*gross earnings 
			where :
			retention = company retention
			*/	
									
			$netPayEarnings = ($earnRetention/100)*$grossEarnings;//net pay earnings
			//echo "Net Pay Earnings = ".$netPayEarnings."\n";
			
			/*amount Limit Deductions = Gross Earnings - net pay Earnings*/
			$amntLimitDed = $grossEarnings-$netPayEarnings;
			//echo "Amount Limit Ded = ".$amntLimitDed."\n";
			
			/*non tax Earnings*/
			$nonTaxEarn = (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND (trn.trnTaxCd = 'N' or trn.trnTaxCd='' or trn.trnTaxCd is null) AND (sprtPS IS NULL or sprtPS ='' or sprtPS='N') ",'');
			//echo "Non Taxable Earnings = ".$nonTaxEarn."\n";
			
			/*table gross earnings = gross earnings - nontax gross earnings*/
			$taxableGrossEarn = (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND (trn.trnTaxCd = 'Y') AND (sprtPS IS NULL or sprtPS ='' or sprtPS='N') ",$empForDedVal['empWageTag']);
			//echo "Taxable Earnings = ".$taxableGrossEarn."\n";
			
			/*If the Branch.Minimum Wage!=0, Get 0100(Basic) of the Employee*/
			
			$emp_BRate 			=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_BASIC."'",'');
			$emp_Tard			=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_TARD."'",'');	
			$emp_Ut				=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_UT."'",'');	
			$emp_Absence		=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_ABS."'",'');	
			$emp_VLOP			=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_VLOP."'",'');	
			$emp_SLOP			=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_SLOP."'",'');	
			$emp_AdjBasic		=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".ADJ_BASIC."'",'');	
			$empMinWage_Basic 	=  (float) (($emp_BRate + $emp_Tard + $emp_Ut + $emp_Absence + $emp_VLOP + $emp_SLOP) + $emp_AdjBasic);
			
			/*echo $empForDedVal['empNo']."\n";
				echo $emp_BRate."\n";
				echo $emp_Tard."\n";
				echo $emp_Ut."\n";
				echo $emp_Absence."\n";
				echo $emp_VLOP."\n";
				echo $emp_SLOP."\n";
				echo $emp_AdjBasic."\n";
				echo "Minimum Wage = ".$empMinWage_Basic."\n";*/
			
			
			if((int)$this->getCutOffPeriod() == 2){
				$prevEarn = (float)$this->getPrevGrossEarn($empForDedVal['empNo']);
				$monthLyGrossEarn = $grossEarnings+$prevEarn;
				
				$arrGovDedAmnt = $this->getGovDedAmnt($monthLyGrossEarn);
				
				if($monthLyGrossEarn != 0 || $monthLyGrossEarn != ""){
						
						if ($arrGovDedAmnt['sssEmployee']!="") {$SssEmp=$arrGovDedAmnt['sssEmployee'];} else {$SssEmp=0;}
						if ($arrGovDedAmnt['sssEmployer']!="") {$SssEmplr=$arrGovDedAmnt['sssEmployer'];} else {$SssEmplr=0;}
						if ($arrGovDedAmnt['EC']!="") {$EcEmp=$arrGovDedAmnt['EC'];} else {$EcEmp=0;}
						if ($arrGovDedAmnt['phicEmployee']!="") {$PhicEmp=$arrGovDedAmnt['phicEmployee'];} else {$PhicEmp=0;}
						if ($arrGovDedAmnt['phicEmployer']!=""){$PhicEmplr=$arrGovDedAmnt['phicEmployee'];} else {$PhicEmplr=0;}
											   
						 $qryToMtdGov = "INSERT INTO tblMtdGovt(compCode,pdYear,pdMonth,empNo,mtdEarnings,sssEmp,sssEmplr,ec,phicEmp,phicEmplr,hdmfEmp,hdmfEmplr)
										VALUES('{$this->session['company_code']}',
											   '{$this->get['pdYear']}',
											   '{$this->get['pdMonth']}',
											   '{$empForDedVal['empNo']}',
											   '".sprintf("%01.2f",$monthLyGrossEarn)."',
											   '$SssEmp',
											   '$SssEmplr',
											   '$EcEmp',
											   '$PhicEmp',
											   '$PhicEmplr',
											   '100',
											   '100')";
						if($Trns){
							$Trns = $this->execQry($qryToMtdGov);
						}
					unset($SssEmp,$SssEmplr,$EcEmp,$PhicEmp,$PhicEmplr);
					$arrGovEmpToDeduct = $this->getSumGov($empForDedVal['empNo']);
					
					if((float)$arrGovEmpToDeduct['sssEmp'] != 0){
						if($amntLimitDed >= (float)$arrGovEmpToDeduct['sssEmp']){
							$amntLimitDed -= (float)$arrGovEmpToDeduct['sssEmp'];
							$totalGovDeducted += (float)$arrGovEmpToDeduct['sssEmp'];
							if($Trns){
								$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'5200',$arrGovEmpToDeduct['sssEmp']);
							}
						}
					}
					if((float)$arrGovEmpToDeduct['phicEmp'] != 0){
						if($amntLimitDed >= (float)$arrGovEmpToDeduct['phicEmp']){
							$amntLimitDed -= (float)$arrGovEmpToDeduct['phicEmp'];
							$totalGovDeducted += (float)$arrGovEmpToDeduct['phicEmp'];
							if($Trns){
								$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'5300',$arrGovEmpToDeduct['phicEmp']);
							}
						}
					}
					if((float)$arrGovEmpToDeduct['hdmfEmp'] != 0){
						if($amntLimitDed >= (float)$arrGovEmpToDeduct['hdmfEmp']){
							$amntLimitDed -= (float)$arrGovEmpToDeduct['hdmfEmp'];
							$totalGovDeducted += (float)$arrGovEmpToDeduct['hdmfEmp'];
							if($Trns){ 
								$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'5400',$arrGovEmpToDeduct['hdmfEmp']);						
							}
						}				
					}
					
					$ArrSumGov = $this->getSumGov($empForDedVal['empNo']);//sum of government deductions
					$sumGov = (float)$totalGovDeducted;
					
				}
				unset($prevEarn,$monthLyGrossEarn,$arrGovDedAmnt,$ArrSumGov);
			}
			
			//echo "Sum of Governmentals = ".$sumGov ."\n";
			
			//taxable earnings for the period
			//$txbleEarningsPd = $taxableGrossEarn-(float)$sumGov;
			$txbleEarningsPd = $taxableGrossEarn;
			
			//echo "Taxable Earnings = ".$txbleEarningsPd."\n";
			
			/* 	Computation of Witholding tax
				Created By		:	Genarra Jo - Ann Arong
				Date Created	:	10272009 Tuesday
			*/
		
			//$withTax = $this->computeEmpWithTax($empForDedVal['empNo'],$txbleEarningsPd,$taxableGrossEarn,$empForDedVal["empTeu"],$empForDedVal["empMrate"],$sumGov);
			
			//Annual Computation
			
			$withTax = $this->computeWithTax($empForDedVal['empNo'],$txbleEarningsPd,$empForDedVal["empTeu"],$sumGov,$empMinWage_Basic);
			/*echo "Tax Exemption = ".$empForDedVal["empTeu"]."\n";
			echo "With tax = ".$withTax."\n";
			echo "Amount Limit Ded = ".$amntLimitDed."\n";*/
			
			if($withTax != 0){
				if($amntLimitDed >= $withTax){
					$amntLimitDed -= (float)$withTax;
					$totalTaxDeducted = $withTax; 
					
					if($Trns){
						$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'5100',$withTax);				
					}
				}
			}
			//echo "Amount Limit Ded - With Tax = ".$amntLimitDed."\n\n";
			
			//for loans deductions
			$resultloans = $this->getdeductionlist($empForDedVal['empNo'],",lonRefNo");
			foreach($resultloans as $rsloans) {
				if ((float)$amntLimitDed >= (float)$rsloans['sumamount']) {
					$amntLimitDed = (float)$amntLimitDed - (float)$rsloans['sumamount'];
					
					if($Trns){
							$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$rsloans['sumamount'],$rsloans['lonRefNo'],'Y');
					}
				} elseif ((float)$amntLimitDed < (float)$rsloans['sumamount'] && (float)$amntLimitDed>0) {
					if (substr($rsloans['lonTypeCd'],0,1) == 3) {
						if($Trns){
								$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$amntLimitDed,$rsloans['lonRefNo'],'P');
						}
						$amntLimitDed = 0;						
					}				
				}
			}
			$resultSumloans = $this->getdeductionlist($empForDedVal['empNo'],"");			
			foreach($resultSumloans as $valSumLoans) {
				if($Trns){
					$totDedForPeriod +=(float)$valSumLoans['sumamount'];
					//echo "Employee Loans = ".$valSumLoans['sumamount']."\n";
					$trns = $this->writeToTblDeduction($valSumLoans['empNo'],$valSumLoans['trnCode'],(float)$valSumLoans['sumamount']);		
				}
			}
				
			//echo "Amount Limit Ded = ".$amntLimitDed."\n";
			//for other deductions
			$resultotherdeduction =$this->getotherdeductionlist($empForDedVal['empNo'],1);
			foreach ($resultotherdeduction as $rsotherdeductionlist) {
				if ((float)$amntLimitDed >= (float)$rsotherdeductionlist['trnAmount']) {
					$amntLimitDed=(float)$amntLimitDed-(float)$rsotherdeductionlist['trnAmount'];
					//$totDedForPeriod += (float)$rsotherdeductionlist['trnAmount'];
					//echo "Other Ded POSITIVE = ".(float)$rsotherdeductionlist['trnAmount']."\n";
					if($Trns){
						$Trns = $this->updateotherdeductions($rsotherdeductionlist['seqNo'],$rsotherdeductionlist['trnAmount'],'Y');
					}
				} elseif ((float)$amntLimitDed < (float)$rsotherdeductionlist['trnAmount'] && (float)$amntLimitDed > 0) {
					//$totDedForPeriod += $amntLimitDed;
					//echo "Other Ded NEGATIVE = ".$amntLimitDed."\n";
					if($Trns){
						$Trns = $this->updateotherdeductions($rsotherdeductionlist['seqNo'],$amntLimitDed,'P');
					}
					$amntLimitDed=0;
				}			}
			
			//echo "Amount Limit Ded = ".$amntLimitDed."\n";
		
			$resultotherdeduction =$this->getotherdeductionlist($empForDedVal['empNo'],0);
			foreach ($resultotherdeduction as $rsotherdeductionlist) {
				$totDedForPeriod += (float)$rsotherdeductionlist['sumamount'];
				//echo "Write tblDed = ".$rsotherdeductionlist['sumamount']."\n";
				if($Trns)
				{
					$Trns = $this->writeToTblDeduction($rsotherdeductionlist['empNo'],$rsotherdeductionlist['trnCode'],$rsotherdeductionlist['sumamount']);
					//echo "Employee Other Ded = ".$rsotherdeductionlist['sumamount']."\n";
				}	
			}

			$empInfo = $this->getUserInfo($this->session['company_code'],$empForDedVal['empNo'],'');
			$totDedForPeriod+= (float)$sumGov;
			//echo "Total Ded = ".$totDedForPeriod."\n";
			//echo "Sum Gov = ".$sumGov."\n";
			
			$arrEmpNonTaxAllowTot = $this->getEmpAllowance('tblAllowanceBrkDwn',$empForDedVal['empNo'],"AND (tblPayTransType.trnTaxCd = 'N' or tblPayTransType.trnTaxCd='' or tblPayTransType.trnTaxCd is null) AND (tblAllowanceBrkDwn.sprtPS IS NULL or tblAllowanceBrkDwn.sprtPS ='' or tblAllowanceBrkDwn.sprtPS='N') ");
			
			foreach ($arrEmpNonTaxAllowTot as $empNonTaxAllowTotVal){
				$totEmpNonTaxAllow += (float)$empNonTaxAllowTotVal['allowAmt'];
			}
			
			$arrAllowSprtPS = $this->separatePaySlipAllow($empForDedVal['empNo'],"AND (sprtPS = 'Y') ","");			
			foreach ((array)$arrAllowSprtPS as $sprtAllowVal){
				$sprtAllowPSTotAmnt += (float)$sprtAllowVal['sprtPSAllwAmnt'];
			}
			
			
			//$netsalary = ($grossEarnings+$txbleEarningsPd)-($totDedForPeriod+$totalTaxDeducted);
			/*echo "Gross Earnings = ".$grossEarnings."\n";*/
			
			//echo "Tax = ".$totalTaxDeducted."\n";
			
			$netsalary = ($grossEarnings)-($totDedForPeriod+$totalTaxDeducted);
			
			
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
															  totDeductions,
															  nonTaxAllow,
															  netSalary,
															  taxWitheld,
															  empDivCode,
															  empDepCode,
															  empSecCode,
															  sprtAllow)
														VALUES('{$this->session['company_code']}',
															   '{$this->get['pdYear']}',
															   '{$this->get['pdNum']}',
															   '{$empForDedVal['empNo']}',
															   '{$this->session['pay_group']}',
															   '{$this->session['pay_category']}',
															   '{$empInfo['empLocCode']}',
															   '{$empInfo['empBrnCode']}',
															   '{$empInfo['empBankCd']}',
															   '".sprintf("%01.2f",$grossEarnings)."',
															   '".sprintf("%01.2f",$txbleEarningsPd)."',
															   '".sprintf("%01.2f",$totDedForPeriod)."',
															   '".sprintf("%01.2f",$totEmpNonTaxAllow)."',
															   '".sprintf("%01.2f",$netsalary)."',
															   '".sprintf("%01.2f",$totalTaxDeducted)."',
															   '{$empInfo['empDiv']}',
															   '{$empInfo['empDepCode']}',
															   '{$empInfo['empSecCode']}',
															   '".sprintf("%01.2f",$sprtAllowPSTotAmnt)."')";			
			if($Trns){
				$Trns = $this->execQry($qryToPayrollSum);
			}
				
			
			$qryToYtdData = "INSERT INTO tblYtdData(compCode,pdYear,empNo,YtdGross,YtdTaxable,YtdGovDed,YtdTax,YtdNonTaxAllow,Ytd13NBonus,Ytdtx13NBonus,payGrp,pdNumber,YtdBasic,sprtAllow)
							 VALUES('{$this->session['company_code']}',
							 		'{$this->get['pdYear']}',
							 		'{$empForDedVal['empNo']}',
							 		'".sprintf("%01.2f",$grossEarnings)."',
							 		'".sprintf("%01.2f",$txbleEarningsPd)."',
							 		'".sprintf("%01.2f",$totalGovDeducted)."',
							 		'".sprintf("%01.2f",$totalTaxDeducted)."',
							 		'".sprintf("%01.2f",$totEmpNonTaxAllow)."',
							 		'0',
							 		'0',
							 		'{$this->session['pay_group']}',
							 		'{$this->get['pdNum']}',
									'".sprintf("%01.2f",$empMinWage_Basic)."',
									'".sprintf("%01.2f",$sprtAllowPSTotAmnt)."')";
						
			if($Trns){
				$Trns = $this->execQry($qryToYtdData);
			}	
			
			unset($grossEarnings,$netPayEarnings,$amntLimitDed,$nonTaxEarn,$taxableGrossEarn,$sumGov,$withTax,$totDedForPeriod,$netsalary,$totalGovDeducted,$arrAllwVal,$totEmpNonTaxAllow,$totalTaxDeducted,$ArSeq,$totaloansandadjustment,$sprtAllowPSTotAmnt,$empMinWage_Basic,$emp_BRate,$emp_Tard,$emp_Ut,$emp_Absence,$emp_VLOP,$emp_SLOP,$emp_AdjBasic);
			
		}//end of foreach fo dedcutions	
		
		/*Insert Other Earnings and Other Transactions where ProcessTag is null or ="N"*/
		
		/*Get Unposted Other Earnings Transaction*/
		foreach ((array)$this->unPostedTranOthEarn() as $arrUnPosTranEarnVal)
		{//foreach for Unposted Other Earnings
			if($Trns){
				$Trns = $this->writeToTblUnpostedTran($arrUnPosTranEarnVal["compCode"],$arrUnPosTranEarnVal["empNo"],$arrUnPosTranEarnVal["trnCode"],$arrUnPosTranEarnVal["sumEarnAmnt"],$arrUnPosTranEarnVal["sumEarnAmnt"],$arrUnPosTranEarnVal["pdNumber"],$arrUnPosTranEarnVal["pdYear"],$arrUnPosTranEarnVal["trnCntrlNo"],$arrUnPosTranEarnVal["refNo"]);
			}
		}//end foreach for Unposted Other Earnings
		
		/*Get Unposted Other Deductions Transaction*/
		foreach ((array)$this->unPostedTranOthDed() as $arrUnPosTranDedVal)
		{//foreach for Unposted Other Ded
			if($Trns){
				$Trns = $this->writeToTblUnpostedTran($arrUnPosTranDedVal["compCode"],$arrUnPosTranDedVal["empNo"],$arrUnPosTranDedVal["trnCode"],$arrUnPosTranDedVal["sumDedAmnt"],$arrUnPosTranDedVal["ActualAmt"],$arrUnPosTranDedVal["pdNumber"],$arrUnPosTranDedVal["pdYear"],$arrUnPosTranDedVal["trnCntrlNo"],$arrUnPosTranDedVal["refNo"]);
			}
		}//end foreach for Unposted Other Ded
		/*End of Insert Other Earnings and Other Transactions where ProcessTag is null or ="N"*/
		
		
		
		$qryUpdateEarnTag = "UPDATE tblPayPeriod SET pdEarningsTag = 'Y' 
							 WHERE compCode = '{$this->session['company_code']}'
							 AND payGrp = '{$this->session['pay_group']}'
							 AND payCat = '{$this->session['pay_category']}'
							 AND pdYear = '{$this->get['pdYear']}'
							 AND pdNumber = '{$this->get['pdNum']}'";
		if($Trns){
			$Trns = $this->execQry($qryUpdateEarnTag);
		}		
		
		
		
		if(!$Trns){
			$Trns = $this->rollbackTran();//rollback regular payroll transaction
			return false;
		}
		else{
			$Trns = $this->commitTran();//commit regular payroll transaction
			return true;	
		}
		
		
	}
	
	function chkUnpostedTran()
	{
		$qryUnpostedTran = "Select * from tblUnpostedTran
							where empNo in (Select empNo from tblEmpMast where
							compCode='".$_SESSION["company_code"]."' and empPayCat='".$_SESSION["pay_category"]."' and empPayGrp='".$_SESSION["pay_group"]."' and 
							empStat IN ('RG','PR','CN')
							and compCode='".$_SESSION["company_code"]."'
							and pdNumber ='".$this->get['pdNum']."' and pdYear='".$this->get['pdYear']."')";
		$resUnpostedTran = $this->execQry($qryUnpostedTran);
		return $this->getRecCount($resUnpostedTran);
	}
	
}



?>