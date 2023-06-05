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
		
		$emp_regDays = $amountDailiesBasic = $amountLegalBasic = 0;
		$trnCode = "0100";
		$trnCode_LegPay = "0410";
		
		//Get Employee Time Sheet where DayType == 01 and 04;
		/*$qryGetRegDaysToTS = "SELECT * FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empNo = '{$empNo}' 
							  AND tsDate >= '{$this->get['dtFrm']}'
							  AND tsDate <= '{$this->get['dtTo']}' 
							  AND dayType in ('01','04')
							  AND empPayCat='".$_SESSION["pay_category"]."'
							  AND empPayGrp='".$_SESSION["pay_group"]."'";*/
		$qryGetRegDaysToTS = "SELECT * FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empNo = '{$empNo}' 
							  AND tsDate >= '{$this->get['dtFrm']}'
							  AND tsDate <= '{$this->get['dtTo']}' 
							  AND empPayCat='".$_SESSION["pay_category"]."'
							  AND empPayGrp='".$_SESSION["pay_group"]."'";
							  			  
		$resGetRegDaysToTS = $this->execQry($qryGetRegDaysToTS);
		$rowGetRegDaysToTS = $this->getArrRes($resGetRegDaysToTS);
		
		foreach($rowGetRegDaysToTS as $rowGetRegDaysToTS_Val)
		{
			/*if($rowGetRegDaysToTS_Val["hrsAbsent"]=='4'){
				$emp_regDays+=0.5;
			}
			
			if($rowGetRegDaysToTS_Val["hrsAbsent"]=='0'){
				$emp_regDays++;
			}*/
			
			/*Regular and Special Holiday*/
			if(($rowGetRegDaysToTS_Val["dayType"]=='01')||($rowGetRegDaysToTS_Val["dayType"]=='04')){
				if($rowGetRegDaysToTS_Val["hrsAbsent"]=='0'){
					$emp_regDays++;
					$emp_allowDays++;
				}
				else
				{
					$emp_regDays+=(8-$rowGetRegDaysToTS_Val["hrsAbsent"])/8;
					$emp_allowDays+=(8-$rowGetRegDaysToTS_Val["hrsAbsent"])/8;
				}
			}
			
			/*Legal Holiday*/
			if($rowGetRegDaysToTS_Val["dayType"]=='03'){
				$sumOt = $rowGetRegDaysToTS_Val["hrsOtLe8"] + $rowGetRegDaysToTS_Val["hrsOtGt8"];
				
				if($sumOt > 0)
				{
					$emp_regDays++;
					$emp_allowDays++;
				}
				else
				{
					$emp_legDays++;
					$emp_allowDays++;
				}
			}
			
			/*Legal Holiday*/
			if($rowGetRegDaysToTS_Val["dayType"]=='05'){
				$sumOt = $rowGetRegDaysToTS_Val["hrsOtLe8"] + $rowGetRegDaysToTS_Val["hrsOtGt8"];
				
				if($sumOt > 0)
				{
					$emp_regDays++;
					$emp_allowDays++;
				}
				else
				{
					$emp_legDays++;
					$emp_allowDays++;
				}
			}
		}
		
		$amountDailiesBasic = 	$emp_regDays * (float)$dailyRate;
		$amountLegalBasic = 	$emp_legDays * (float)$dailyRate;
		
		//echo $empNo."\nDaily Rate=".$dailyRate."\n"."Regular Days = ".$emp_regDays."\nLegal Days=".$emp_legDays."\nAllowance Days = ".$emp_allowDays."\nAmount Daily Basic=".$amountDailiesBasic."\nAmount Legal Pay = ".$amountLegalBasic."\n";
		
		return $trnCode."-".sprintf("%01.2f",$amountDailiesBasic)."-".$emp_allowDays."-".$trnCode_LegPay."-".sprintf("%01.2f",$amountLegalBasic);
		
		
	}
	
	
	private function getDailiesLegHoliday($empNo,$dailyRate){
	
		$legDay = $hrsAbsent = $hrsOtLe8 = $hrsOtGt8 = $legalPay = 0;
		$trnCode = "0410";
		
		/*
			Original Query
			$qryGetLegHolToTS = "SELECT * FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empNo = '{$empNo}' 
							  AND tsDate >= '{$this->get['dtFrm']}'
							  AND tsDate <= '{$this->get['dtTo']}' 
							  AND dayType in ('03','05')
							  AND empPayCat='".$_SESSION["pay_category"]."'
							  AND empPayGrp='".$_SESSION["pay_group"]."'";*/
		
		$qryGetLegHolToTS = "SELECT * FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empNo = '{$empNo}' 
							  AND tsDate >= '{$this->get['dtFrm']}'
							  AND tsDate <= '{$this->get['dtTo']}' 
							  AND dayType in ('05')
							  AND empPayCat='".$_SESSION["pay_category"]."'
							  AND empPayGrp='".$_SESSION["pay_group"]."'";
							  
		$resGetLegHolToTS = $this->execQry($qryGetLegHolToTS);
		$rowGetLegHolToTS = $this->getArrRes($resGetLegHolToTS);
		
		foreach($rowGetLegHolToTS as $rowGetLegHolToTS_Val)
		{
			/*if($rowGetLegHolToTS_Val["hrsAbsent"]!=""){
				$hrsAbsent+=$rowGetLegHolToTS_Val["hrsAbsent"];
			}
			if($rowGetLegHolToTS_Val["hrsOtLe8"]!=0)
				$hrsOtLe8+=$rowGetLegHolToTS_Val["hrsOtLe8"];
			
			if($rowGetLegHolToTS_Val["hrsOtGt8"]!=0)
				$hrsOtGt8+=$rowGetLegHolToTS_Val["hrsOtGt8"];*/
			
			$legDay++;		
		}
		
		
		$legalPay = $legDay * $dailyRate;
		
		if($legalPay>0){
			return $trnCode."-".sprintf("%01.2f",$legalPay);
		}
		else{
			return $trnCode."-"."0";
		}
	}
	
	private function getProRateAbsent($empNo,$basicPay){
	
		$cntAbsent = $cntPresent = $cntRDLG= $empProRateDaily = 0;
		$qryGetLstAbsent = "SELECT * FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empNo = '{$empNo}' 
							  AND tsDate >= '{$this->get['dtFrm']}'
							  AND tsDate <= '{$this->get['dtTo']}' 
							  AND empPayCat='".$_SESSION["pay_category"]."'
							  AND empPayGrp='".$_SESSION["pay_group"]."'";
		$resGetLstAbsent  = $this->execQry($qryGetLstAbsent );
		$rowGetLstAbsent  = $this->getArrRes($resGetLstAbsent );
		
		foreach($rowGetLstAbsent as $rowGetLstAbsent_Val)
		{
			if($rowGetLstAbsent_Val["dayType"]=='01'){
			
				if($rowGetLstAbsent_Val["hrsAbsent"]=='4'){
					$cntAbsent+=0.5;
				}
				
				if($rowGetLstAbsent_Val["hrsAbsent"]=='8'){
					$cntAbsent++;
				}
				
				if($rowGetLstAbsent_Val["hrsAbsent"]=='0'){
					$cntPresent++;
				}
				
			}
			elseif(($rowGetLstAbsent_Val["dayType"]=='02')||($rowGetLstAbsent_Val["dayType"]=='03')){
				$cntRDLG++;
			}	
		}
		
		if($cntPresent==0){
			$empAmntAbsent = $basicPay;
		}else{
			$calDays = $this->getCalendarDays($this->get['dtFrm'], $this->get['dtTo'])+1;
			$noRegDays = $calDays - $cntRDLG;
			$empProRateDaily = $basicPay/$noRegDays;
			$empAmntAbsent = $cntAbsent*$empProRateDaily;
			$empAmntAbsent = sprintf("%01.2f",$empAmntAbsent);
		}
		
		return $empAmntAbsent;
		//echo $empNo."=Hrs. Absent = ".$cntAbsent."\nPresent = ".$cntPresent."\nAmount Absent = ".$empAmntAbsent."\nCalendar Days = ".$calDays."\nRegular Days = ".$cntRDLG."\nNo. Reg. Days = ".$noRegDays."\nBasic Pay = ".$basicPay."\nPro Rate Basic = ".$empProRateDaily."\nAmount Absent = ".$empAmntAbsent."\n\n";
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
							    tblEmpMast.empHrate,tblEmpMast.empStat, tblAllowType.attnBase, tblAllowType.trnCode, tblAllowType.allowTypeStat,tblPayTransType.trnTaxCd,allowTag
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
		$noCompDays = $empEarnings_numerator = $cnt_employee_absent_current = $cnt_employee_absent_previous = $sum_employee_absent= 0;
		
		//Get Previous Cut Off
		$arrDateTimeSheet = $this->getPreviousCutOff($this->get['pdYear'], $this->get['pdNum']);
		$dsDate = ($arrDateTimeSheet["pdFrmDate"]!=""?$arrDateTimeSheet["pdFrmDate"]:"");
		$deDate = ($arrDateTimeSheet["pdToDate"]!=""?$arrDateTimeSheet["pdToDate"]:"");
		
		//Get Company Days
		$arrcompNoDays = $this->getCompAnnDate($_SESSION["company_code"]);
		$compNoDays = ($arrcompNoDays["compNoDays"]!=""?$arrcompNoDays["compNoDays"]:26);
			
			
		foreach ((array)$this->getEmpAllowance('tblAllowance','','') as $allowArrVal){//allowance
			$qryWhereTS = $qryGetTimeSheet = $qryWhereTSPrev = $qryGetTimeSheet_Temp  = $qryGetTimeSheet_Prev_Temp = "";
			$dai_allowAmnt = $totalAllowAmnt = 0;
			
			/*Set Up the Where Condition with regards to the tsDate Between*/
			
			/*Start Date and End Date with regards to tblTimeSheetHist*/
			$emp_sDate = $this->getCalendarDays($this->dateFormat($allowArrVal['allowStart']), $this->dateFormat($deDate));
			$emp_eDate = $this->getCalendarDays($this->dateFormat($allowArrVal['allowEnd']), $this->dateFormat($deDate));
			$emp_cOff = $this->getCalendarDays($this->dateFormat($dsDate), $this->dateFormat($deDate));
			
			/*Start Date and End Date with regards to tblTimeSheet*/
			$emp_sDate_curr = $this->getCalendarDays($this->dateFormat($allowArrVal['allowStart']), $this->get['dtTo']);
			$emp_eDate_curr = $this->getCalendarDays($this->dateFormat($allowArrVal['allowEnd']), $this->get['dtTo']);
			$emp_cOff_prev = $this->getCalendarDays($this->get['dtFrm'], $this->get['dtTo']);
			
			if($emp_sDate_curr>=0){
				$s_EmpDate_curr = ($emp_sDate_curr<=$emp_cOff?$this->dateFormat($allowArrVal['allowStart']):$this->get['dtFrm']);
				
				if($this->dateFormat($allowArrVal['allowEnd'])!='01/01/1970')
					$e_EmpDate_curr = ($emp_eDate_curr>0?$this->dateFormat($allowArrVal['allowEnd']):$this->get['dtTo']);
				else
					$e_EmpDate_curr = $this->get['dtTo'];
					
				$qryWhereTS = "AND tsDate BETWEEN '".$s_EmpDate_curr."' AND '".$e_EmpDate_curr."'";
			}
			
			/*Permanent Allowance*/
			$qryGetTimeSheet		= 	($qryWhereTS!=""?$qryWhereTS:"");
			$qryGetTimeSheet		= 	($qryGetTimeSheet!=""?$qryGetTimeSheet:"");
			
			if($emp_sDate>=0){
				$s_EmpDate = ($emp_sDate<=$emp_cOff?$this->dateFormat($allowArrVal['allowStart']):$this->dateFormat($dsDate));
				
				if($this->dateFormat($allowArrVal['allowEnd'])!='01/01/1970')
					$e_EmpDate = ($emp_eDate>0?$this->dateFormat($allowArrVal['allowEnd']):$this->dateFormat($deDate));
				else
					$e_EmpDate = $this->dateFormat($deDate);
					
				$qryWhereTSPrev = "AND tsDate BETWEEN '".$s_EmpDate."' AND '".$e_EmpDate."'";
			}
			
			
			$cnt_employee_absent_current = $this->getEmployeeCntAbsent('tblTimeSheet', $allowArrVal['empNo'], $this->get['dtFrm'], $this->get['dtTo']);
			if(($dsDate!="") || ($deDate!=""))
				$cnt_employee_absent_previous = $this->getEmployeeCntAbsent('tblTimeSheetHist', $allowArrVal['empNo'], $this->dateFormat($dsDate), $this->dateFormat($deDate));
			
			$sum_employee_absent = $cnt_employee_absent_current+$cnt_employee_absent_previous;
			
			//echo $cnt_employee_absent_current."+".$cnt_employee_absent_previous."\n";
			$arr_empEarnings_curr = $this->getEmpEarningsInfo($_SESSION["company_code"], $_GET["pdYear"], $_GET["pdNum"], $allowArrVal['empNo'],'tblEarnings',1,trim($allowArrVal['attnBase']),$sum_employee_absent,$allowArrVal['allowTag']);
			$arr_empEarnings_hist = $this->getEmpEarningsInfo($_SESSION["company_code"], $arrDateTimeSheet["pdYear"], $arrDateTimeSheet["pdNumber"], $allowArrVal['empNo'],'tblEarningsHist',1,trim($allowArrVal['attnBase']),$sum_employee_absent,$allowArrVal['allowTag']);
			$arr_empEarnings_denom_curr = $this->getEmpEarningsInfo($_SESSION["company_code"], $_GET["pdYear"], $_GET["pdNum"], $allowArrVal['empNo'],'tblEarnings',2,trim($allowArrVal['attnBase']),0,$allowArrVal['allowTag']);
			$arr_empEarnings_denom_hist = $this->getEmpEarningsInfo($_SESSION["company_code"], $arrDateTimeSheet["pdYear"], $arrDateTimeSheet["pdNumber"], $allowArrVal['empNo'],'tblEarningsHist',2,trim($allowArrVal['attnBase']),0,$allowArrVal['allowTag']);
			
			
			$empEarnings_numerator = $arr_empEarnings_curr["sumBasic"] + $arr_empEarnings_hist["sumBasic"];
			$empEarnings_denominator = $arr_empEarnings_denom_curr["sumBasic"] + $arr_empEarnings_denom_hist["sumBasic"];
			
			
			/*Temporary Allowance*/
			$qryGetTimeSheet_Temp		= 	($qryWhereTS!=""?$qryWhereTS:"");
			$qryGetTimeSheet_Temp		= 	($qryGetTimeSheet_Temp!=""?$qryGetTimeSheet_Temp:"");
			
			if(($dsDate!="") && ($deDate!="")){
				$qryGetTimeSheet_Prev		= 	($qryWhereTSPrev!=""?$qryWhereTSPrev:"");
				$qryGetTimeSheet_Prev_Temp	= 	($qryWhereTSPrev!=""?$qryWhereTSPrev:"");
			}
			
			//echo $allowArrVal['empNo']."\nCurrent TS = ".$qryGetTimeSheet_Temp."\nPrev. TS = ".$qryGetTimeSheet_Prev_Temp."\n\n";
			
			switch($allowArrVal['empPayType']) {
				case 'M'://pay type M = monthly
					if(($allowArrVal['allowSked'] == '1')||($allowArrVal['allowSked'] == '2'))
					{
						$noCompDays =  $this->getCalendarDays($this->dateFormat($dsDate),$this->get['dtTo'])+1;
						$noCompDays = ($noCompDays=='31'?27:26);
						//$dai_allowAmnt  = ($allowArrVal['allowTag']=='D'?$allowArrVal['allowAmt']:$allowArrVal['allowAmt']/$noCompDays);
						$dai_allowAmnt  = ($allowArrVal['allowTag']=='D'?$allowArrVal['allowAmt']*$noCompDays:$allowArrVal['allowAmt']);
						switch (trim($allowArrVal['allowPayTag'])){
							case 'P':
								$totalAllowAmnt = $dai_allowAmnt * ($empEarnings_numerator/$empEarnings_denominator);
								$totalAllowAmnt = sprintf("%01.2f", $totalAllowAmnt);
								//echo $dai_allowAmnt." * (".$empEarnings_numerator."/".$empEarnings_denominator.")\n";
							break;
							
							case 'T':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'], $qryGetTimeSheet_Temp, $qryGetTimeSheet_Prev_Temp,trim($allowArrVal['attnBase']),$noCompDays,$dai_allowAmnt);
							break;
						}//end of switch case
					}
					else
					{
						$noCompDays =  $this->getCalendarDays($this->get['dtFrm'],$this->get['dtTo'])+1;
						$noCompDays = ($noCompDays=='16'?13.5:13);
						//$dai_allowAmnt  = ($allowArrVal['allowTag']=='D'?$allowArrVal['allowAmt']:$allowArrVal['allowAmt']/$noCompDays);
						$dai_allowAmnt  = ($allowArrVal['allowTag']=='D'?$allowArrVal['allowAmt']*$noCompDays:$allowArrVal['allowAmt']);
						switch (trim($allowArrVal['allowPayTag'])){
							case 'P':
								$totalAllowAmnt = $dai_allowAmnt * ($arr_empEarnings_curr["sumBasic"]/$arr_empEarnings_denom_curr["sumBasic"]);
								//echo $allowArrVal['empNo']."=".$dai_allowAmnt." * (".$arr_empEarnings_curr["sumBasic"]."/".$arr_empEarnings_denom_curr["sumBasic"].")\n";
								$totalAllowAmnt = sprintf("%01.2f", $totalAllowAmnt);
							break;
							
							case 'T':
								$totalAllowAmnt = $this->GetTimeSheetRecord($allowArrVal['empNo'], $qryGetTimeSheet_Temp,'',trim($allowArrVal['attnBase']),$noCompDays,$dai_allowAmnt);
							break;
						}//end of switch case
					}
					
				break;
				case 'D'://pay type D = daily
					$empTardUt = $emp_AmntTardy[$allowArrVal['empNo']] + $emp_AmntUt[$allowArrVal['empNo']];
					$dai_allowAmnt  = $allowArrVal['allowAmt'];
					if($allowArrVal['allowTag']=='M')
					{
						
						$totalAllowAmnt = $dai_allowAmnt * ($empEarnings_numerator/$empEarnings_denominator);
						$totalAllowAmnt = sprintf("%01.2f", $totalAllowAmnt);
					}
					else
					{
						if(trim($allowArrVal['attnBase'])=='Y')
							$empTardUt = (float)$empTardUt*-1;
						else
							$empTardUt = 0;
						
						$totalAllowAmnt =	$dai_allowAmnt * ($emp_AccruedDays[$allowArrVal['empNo']] - ($empTardUt/$allowArrVal['empDrate']));
					}
					
					$totalAllowAmnt = ($totalAllowAmnt!=0?sprintf("%01.2f",$totalAllowAmnt):0);
					
					
				break;
			}
		
			$arrChecker[] = $allowArrVal['empNo']."-".$allowArrVal['trnCode'];
			//echo $allowArrVal['empNo']."=".$totalAllowAmnt;
			if(in_array($allowArrVal['empNo']."-".$allowArrVal['trnCode'],$arrChecker)){
				$arrAllow[ $allowArrVal['empNo']."-".$allowArrVal['trnCode']."-".$allowArrVal['allowCode'] ] += $totalAllowAmnt;
			}
			
			unset($totalAllowAmnt,$withTardy,$allowAmnt,$noCompDays);		
		}//end of allowance
	
		return $arrAllow;
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
								AND tblEmpMast.empStat IN ('RG','PR','CN') 
								group by tblEmpMast.empNo, tblEmpMast.empTeu, tblEmpMast.empMrate, 
								tblEmpMast.empBrnCode, tblEmpMast.empWageTag";
		
		$resGetEmpForDeduct = $this->execQry($qryGetEmpForDeduct);
		return $this->getArrRes($resGetEmpForDeduct);		
	}

	private function writeToTblDeduction($empNo,$tranCode,$tranAmount,$sprtPS="0"){
		
		$taxCd = "";
		$writeToTblDeductions = "";
		$finalTaxTag = "";
		
		$taxCd = $this->getTrnTaxCode($this->session['company_code'],$tranCode,'D');//get tax code	
		$taxCdfnl = $taxCd['trnTaxCd'];
		
		 $writeToTblDeductions = "INSERT INTO tblDeductions
										  (compCode,pdYear,
										  pdNumber,empNo,
										  trnCode,trnAmountD,trnTaxCd,sprtPS)
										  VALUES
										  ('{$this->session['company_code']}','{$this->get['pdYear']}',
										   '{$this->get['pdNum']}','{$empNo}',
										   '{$tranCode}','".sprintf("%01.2f",$tranAmount)."','{$taxCdfnl}','$sprtPS')\n\n";
		
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
		
		$qryGetSprtPS = "SELECT ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo, SUM(ern.trnAmountE) AS sprtPSAllwAmnt,ern.trnCode
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
		$qryGetSprtPS	.= "GROUP BY ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo,ern.trnCode ";
		
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
								";
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
	private function getEmpAddTaxableIncome($empNo)
	{
		$qryAddTaxable = "Select * from tblGov_Tax_Added where empNo='".$empNo."' and monthToDed='".$this->get['pdNum']."' and addStat='N'";
		$resAddTaxable = $this->execQry($qryAddTaxable);
		return  $this->getSqlAssoc($resAddTaxable);
	}
	
	private function getEmpNoNonGovt()
	{
		$queryExecNonGovt = "Select empNo from tblNonEmpGov where compCode='".$_SESSION["company_code"]."'";
		$resExecNonGovt = $this->execQry($queryExecNonGovt);
		return $this->getArrRes($resExecNonGovt);
	}
	
	private function getDataToYtdDataHist($empNo){
		$qryGetDataToYtdHist = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,ytd.pdNumber,
							emp.empPayGrp,emp.empPayCat,ytd.YtdBasic, ytd.sprtAllow
							FROM tblYtdDataHist as ytd LEFT JOIN tblEmpMast as emp
							ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
							WHERE ytd.compCode = '{$this->session['company_code']}'
						    AND  emp.empPayGrp = '{$this->session['pay_group']}'
						    AND emp.empPayCat = '{$this->session['pay_category']}'
						    AND ytd.pdYear = '{$this->get['pdYear']}'
						    AND ytd.empNo = '{$empNo}'";
		$resGetDataToYtdHist = $this->execQry($qryGetDataToYtdHist);	
		return 	$this->getSqlAssoc($resGetDataToYtdHist);
	}
	
	private function AdjustmentAllowanceProc()
	{
		$qryAllowAdjOthrs = "Select  earnTranDtl.empNo, earnTranDtl.trnCode, SUM(earnTranDtl.trnAmount) AS sumEarnAmnt, earnTranDtl.trnTaxCd, allowType.sprtPS, refNo
							  FROM   tblEarnTranDtl earnTranDtl INNER JOIN
									 tblAllowType allowType ON earnTranDtl.trnCode = allowType.trnCode
							  WHERE  (earnTranDtl.compCode = '{$this->session['company_code']}') AND 
									 (allowType.compCode = '{$this->session['company_code']}') AND 
									 (earnTranDtl.payGrp = '{$this->session['pay_group']}') AND 
									 (earnTranDtl.payCat = '{$this->session['pay_category']}') AND 
									 (earnTranDtl.earnStat = 'A') AND
									 (earnTranDtl.trnCode IN
											  (SELECT     trnCode
												FROM          tblEarnTranHeader
												WHERE      compCode = '{$this->session['company_code']}' AND earnStat = 'A' AND pdYear = '{$this->get['pdYear']}' AND pdNumber = '{$this->get['pdNum']}')) AND
									 (earnTranDtl.trnCode IN
											  (SELECT     trnCode
												FROM       tblAllowType
												WHERE      compCode = '{$this->session['company_code']}' AND adjTag = '1' )) AND
									 (earnTranDtl.empNo IN
											  (SELECT     empNo
												FROM          tblEmpMast
												WHERE      compCode = '{$this->session['company_code']}' AND empPayGrp = '{$this->session['pay_group']}' AND empPayCat = '{$this->session['pay_category']}' AND empStat IN ('RG', 'PR', 'CN'))) AND
									 (earnTranDtl.refNo IN
											  (SELECT     refNo
												FROM          tblEarnTranHeader
												WHERE      compCode = '{$this->session['company_code']}' AND earnStat = 'A' AND pdYear = '{$this->get['pdYear']}' AND pdNumber = '{$this->get['pdNum']}'))
								GROUP BY earnTranDtl.empNo, earnTranDtl.trnCode, earnTranDtl.trnTaxCd, allowType.sprtPS, refNo";
		
		//echo $qryAllowAdjOthrs;
		
		$resqryAllowAdjOthrs = $this->execQry($qryAllowAdjOthrs);
		return $this->getArrRes($resqryAllowAdjOthrs);
	 }
	
	
	
	
	private function getEmployeeCntAbsent($table,$empNo,$dateFrom, $dateTo)
	{
		$emp_cntAbsent = 0;
		$qryTimeSheet = "Select * from ".$table." where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' and tsDate between '".$dateFrom."' and '".$dateTo."' and empPayGrp='".$_SESSION["pay_group"]."' and empPayCat='".$_SESSION["pay_category"]."'";
		$resqryTimeSheet = $this->execQry($qryTimeSheet);
		$rowqryTimeSheet = $this->getArrRes($resqryTimeSheet);
		
		foreach($rowqryTimeSheet as $rowqryTimeSheet_Val)
		{
			if($rowqryTimeSheet_Val["hrsAbsent"]=='8'){
				$emp_cntAbsent++;
			}
			elseif(($rowqryTimeSheet_Val["hrsAbsent"]!=8) && ($rowqryTimeSheet_Val["hrsAbsent"]!='0'))
			{
				$emp_cntAbsent+=(8-$rowqryTimeSheet_Val["hrsAbsent"])/8;
				
			}
		}
		
		return ($emp_cntAbsent!=""?$emp_cntAbsent:0);
		
	}
	
	private function getEmpEarningsInfo($compCode, $pdYear, $pdNum, $empNo, $tbl_Earn, $alltrnCode, $attnBase, $cntAbsentEmp,$allowCode)
	{
		if($alltrnCode==1)
		{
			if($attnBase=='Y')
			{
				if($allowCode=='M')
				{
					if($cntAbsentEmp>12)
						$wheretrnCode = "and trnCode in (".EARNINGS_BASIC.",".EARNINGS_ABS.",".EARNINGS_UT.",".EARNINGS_TARD.")";
					else
						$wheretrnCode = "and trnCode in (".EARNINGS_BASIC.",".EARNINGS_UT.",".EARNINGS_TARD.")";
				}
				else
				{
					$wheretrnCode = "and trnCode in (".EARNINGS_BASIC.",".EARNINGS_ABS.",".EARNINGS_UT.",".EARNINGS_TARD.")";
				}
			}
			else
			{
				if($cntAbsentEmp>12)
					$wheretrnCode = "and trnCode in (".EARNINGS_BASIC.",".EARNINGS_ABS.")";
				else
					$wheretrnCode = "and trnCode in (".EARNINGS_BASIC.")";
			}
		}
		else
		{
			$wheretrnCode = "and trnCode in (".EARNINGS_BASIC.")";
		}
		
		$qryEarnings = "Select empNo, SUM(trnAmountE) AS sumBasic from ".$tbl_Earn." where compCode='".$compCode."' and pdYear='".$pdYear."' 
						and pdNumber='".$pdNum."' and empNo='".$empNo."' ".$wheretrnCode."
						GROUP BY compCode, empNo";
		$resqryEarnings = $this->execQry($qryEarnings);
		return  $this->getSqlAssoc($resqryEarnings);
	}
	
	
	
	/*Allowance Function, Get TimeSheet Record*/
	private function GetTimeSheetRecord($empNo, $where_curr, $where_prev, $attnBase, $noCompDays, $d_allowAmnt)
	{	
		$empNoAbsences = $empRestDay = $empRestDay_prev = $empSumTardUt = $empHolDay = $empNoWorkingDays = $empNoWorkDays = $empAllowAmt = $empCntOfAbsences = 0;
		
		//Current TimeSheet
		if($where_curr!="")
		{
			$qryGetAllwTs = "SELECT tsDate,hrsAbsent,hrsTardy,hrsUt,dayType FROM tblTimeSheet
								WHERE compCode = '".$_SESSION["company_code"]."' 
								AND empNo = '".$empNo."'
								AND empPayGrp = '".$_SESSION["pay_group"]."'
								AND empPayCat = '".$_SESSION["pay_category"]."'";
			if($where_curr!="")
				$qryGetAllwTs.= $where_curr;
			
			$resGetAllwTs = $this->execQry($qryGetAllwTs);	
			
			foreach ((array)$this->getArrRes($resGetAllwTs) as $tsResVal){
			
				if($tsResVal["hrsAbsent"]=='4')
					$empNoAbsences+=0.5;
				
				if($tsResVal["hrsAbsent"]=='8')
					$empNoAbsences++;
				
				if($tsResVal["dayType"]=='2')
					$empRestDay++;
					
				if($tsResVal["dayType"]=='3')
					$empHolDay++;
				
				if($attnBase=="Y"){
					$empSumTardUt+=$tsResVal["hrsTardy"]+$tsResVal["hrsUt"];
				}
				
				$empNoWorkingDays++;	
			}
		}
		
		//Previous TimeSheet
		if($where_prev!="")
		{
			$qryGetAllwTs_prev = "SELECT tsDate,hrsAbsent,hrsTardy,hrsUt,dayType FROM tblTimeSheetHist
								WHERE compCode = '".$_SESSION["company_code"]."' 
								AND empNo = '".$empNo."'
								AND empPayGrp = '".$_SESSION["pay_group"]."'
								AND empPayCat = '".$_SESSION["pay_category"]."'";
			if($where_prev!="")
				$qryGetAllwTs_prev.= $where_prev;
			
			$resGetAllwTs_prev = $this->execQry($qryGetAllwTs_prev);	
			
			foreach ((array)$this->getArrRes($resGetAllwTs_prev) as $tsResVal_prev){
			
				if($tsResVal_prev["hrsAbsent"]=='4')
					$empNoAbsences+=0.5;
				
				if($tsResVal_prev["hrsAbsent"]=='8')
					$empNoAbsences++;
				
				if($tsResVal_prev["dayType"]=='2')
					$empRestDay_prev++;
					
				if($tsResVal_prev["dayType"]=='3')
					$empHolDay++;
				
				if($attnBase=="Y"){
					$empSumTardUt+=$tsResVal_prev["hrsTardy"]+$tsResVal_prev["hrsUt"];
				}
				
				$empNoWorkingDays++;	
			}
		}
		
		
		if($empNoAbsences>12)
			$empCntOfAbsences = $empNoAbsences;
		else
			$empCntOfAbsences = 0;
			
		$empNoWorkDays = $empNoWorkingDays - ($empRestDay+($empRestDay_prev==0?2:$empRestDay_prev)) - $empHolDay - $empCntOfAbsences - ($empSumTardUt/8);
		
		$empAllowAmt = $d_allowAmnt * $empNoWorkDays;
		
		//$empAllowAmt = ($empAllowAmt<0?0:sprintf("%01.2f",$empAllowAmt));
		//echo $empNo."=".$noCompDays."=". $empNoWorkingDays."\n";
		$empAllowAmt = ((($noCompDays -  $empCntOfAbsences - ($empSumTardUt/8)) /$noCompDays)*$d_allowAmnt);
			
		return (float)$empAllowAmt;
		//echo $empNo."=".($empRestDay+($empRestDay_prev==0?2:$empRestDay_prev))."\n";
		//echo $empNo."\n".$empNoWorkingDays."- (".$empRestDay."+(".$empRestDay_prev."!=0?".$empRestDay_prev.":2)) - ".$empHolDay." - ".$empCntOfAbsences." - (".$empSumTardUt."/8)\n";
		//echo $empNo."\nAllowance Amount = ".$d_allowAmnt."\nCompany Days = ".$noCompDays."\nQuery = ".$qryGetAllwTs."\nQuery Previous = ".$qryGetAllwTs_prev."\nNo. of Absent = ".$empNoAbsences."\nCount of Absences = ".$empCntOfAbsences."\nNo. of RestDays = ".$empRestDay."\nNo of Legal Days = ".$empHolDay."\nSum Tard and UT =".$empSumTardUt."\nWorking Days = ".$empNoWorkingDays."\nFinal Working Days = ".$empNoWorkDays."\nEmp. Allow Amnt = ".$empAllowAmt."\n\n";
		
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
							  AND empNo in (Select empNo from tblEmpMast where
								compCode='".$this->session['company_code']."' and 
								empPayCat='".$this->session['pay_category']."' and empPayGrp='".$this->session['pay_group']."' and 
								empStat IN ('RG','PR','CN')) 
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
								AND empNo in (Select empNo from tblEmpMast where
								compCode='".$this->session['company_code']."' and 
								empPayCat='".$this->session['pay_category']."' and empPayGrp='".$this->session['pay_group']."' and 
								empStat IN ('RG','PR','CN')) 

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
	private function TaxAdjsutment() {
		$sqlEmptax = "Select * from tblEmpTax where compCode='{$_SESSION['company_code']}' AND stat IS NULL";
		return $this->getArrRes($this->execQry($sqlEmptax));
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
			//echo 	$empNo."==".$gross_Taxable."+".$arrYtdDataHist["YtdTaxable"]."+".$empPrevEarnings."-".
					//$arrYtdDataHist["YtdGovDed"]."-".$sumGov."\n";
			
			$estEarn = 	  (float) $gross_Taxable + (float) $arrYtdDataHist["YtdTaxable"] + (float)$empPrevEarnings -  (float) $arrYtdDataHist["YtdGovDed"] - (float)$sumGov;
			//echo 	$empNo."==".$estEarn."\n";
			$estEarn = (float) $estEarn / $this->get['pdNum'];
			//echo 	$empNo."==".$estEarn."\n";
			$estEarn = (float) $estEarn * 24 ;
			//echo 	$empNo."==".$estEarn."\n";
		}
		
		
		//Compute for the Net Taxable Earnings
		$netTaxable = (float) $estEarn - (float) $this->getTaxExemption($empTeu);
		//echo 	$empNo."==".$netTaxable."\n";
		
		//Compute the Estimated Tax using the Annual Tax Table
		$estTaxYear = $this->getAnnualTax($netTaxable);
		//echo 	$empNo."==".$estTaxYear."\n";
		
		//Compute Taxes
		$taxDue = ($estTaxYear / 24)* $this->get['pdNum'];
		//echo 	$empNo."==".$taxDue."\n";
		
		$taxPeriod = $taxDue -  $arrYtdDataHist["YtdTax"] - $empPrevTaxes;
		//echo 	$empNo."==".$taxPeriod."\n";
		
		$taxPeriod = ($taxPeriod<0?0:$taxPeriod);
		return sprintf("%01.2f", $taxPeriod) ;
		
	}
	
	
	
	function getTimeSheetAdjusments($empEn)
	{
		if($empEn==1)
		{
			$empField = "empNo,";
			$qryGrp = "group by empNo";
		}
		
		$qryTSAdjust  = "Select $empField  sum(adjBasic) as adjBasic, sum(adjOt) as adjOt, sum(adjNd) as adjNd from tblTsCorr where compCode='".$_SESSION["company_code"]."' and pdYear='".$this->get['pdYear']."' 
						and pdNumber='".$this->get['pdNum']."' and empPayGrp='".$_SESSION["pay_group"]."' 
						and empPayCat='".$_SESSION["pay_category"]."' and tsStat='A'
						$qryGrp";
		
		if($empEn==1)
			return $this->getArrRes($this->execQry($qryTSAdjust));
		else
			return $this->getSqlAssoc($this->execQry($qryTSAdjust));
	}
	
	/*End of Gen's Function*/
	###########################end of taxwitheld computetion########################################
	###########################for loans and other dedcutions########################################
	//will program 
	function getdeductionlist($empNo,$lonRefNo="", $levPrio) {
		if ($lonRefNo != "") {
			$amountfield = "trnAmountD";
			$dedtag = " AND (dedTag not IN ('Y','P'))";
		} else {
			$amountfield = "ActualAmt";
			$dedtag = "AND (dedTag IN ('Y','P'))";
		}
		
		if($levPrio=="")
			$whereLonType = " and tblEmpLoansDtl.lonTypeCd not in (31,305,310)";
		else
			$whereLonType = " and tblEmpLoansDtl.lonTypeCd in (31,305,310)";
			
		$qryloans="SELECT tblEmpLoansDtl.lonTypeCd, SUM(tblEmpLoansDtl.$amountfield) AS sumamount, tblLoanType.trnCode, 
                      tblEmpLoansDtl.empNo,dedtoAdv $lonRefNo
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
					".$whereLonType."
					GROUP BY tblEmpLoansDtl.lonTypeCd, tblEmpLoansDtl.empNo, tblLoanType.trnCode,dedtoAdv $lonRefNo 
					ORDER BY tblEmpLoansDtl.empNo, tblEmpLoansDtl.lonTypeCd";
		
		//echo $qryloans."\n\n";
		return $this->getArrRes($this->execQry($qryloans));	
	}
	
	function getotherdeductionlist($empNo,$cat){
		if ($cat==1) {
			$qryother="select ActualAmt,trnAmount,trnCode,seqNo,empNo,dedtoAdv from tblDedTranDtl 
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
			$qryother="select processtag, SUM(ActualAmt) AS sumamount, empNo, trnCode, compCode, trnPriority,dedtoAdv from tblDedTranDtl 
						where empNo='$empNo' 
						and compCode='" . $this->session['company_code'] . "' 
						and dedStat='A' 
						and payGrp='" . $this->session['pay_group'] . "' 
						and payCat='" . $this->session['pay_category'] . "' 
						and processtag IN ('Y','P')
						and (trnCode in 
										(SELECT trnCode FROM tblPayTransType where trnApply in (3,{$this->getCutOffPeriod()})))
						and (trnCode not in (SELECT trnCode FROM tblLoanType where compCode='" .  $this->session['company_code'] . "'))				
						GROUP BY processtag, empNo, trnCode, compCode, trnPriority,dedtoAdv
						order by trnPriority";
		
		}
		
		return $this->getArrRes($this->execQry($qryother));
	}
	
	function updateotherdeductions($seqNo,$Amount,$tag,$dedtoAdv) {
		$qryupdateotherdeductions="Update tblDedTranDtl set ActualAmt='$Amount',processtag='$tag',dedtoAdv='$dedtoAdv' where seqNo='$seqNo'"; 
		return $this->execQry($qryupdateotherdeductions);
	}
	
	function updateloansdtl($empNo,$lonCd,$Amount,$lonRefNo,$tag,$dedAdv="") {
		$qryupdateloansdtl="Update tblEmpLoansDtl set dedTag='$tag',ActualAmt='$Amount',dedtoAdv='$dedAdv'
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
									AND trnGrp = '{$this->session['pay_group']}'
									AND trnCat = '{$this->session['pay_category']}'
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
		
		/*TblTimeSheet Adjusment*/
		
		$qryDelEarnTranDtl_Ts = "Delete from tblEarnTranHeader where refNo in ('91_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."','92_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."','93_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."');";	
		$qryDelEarnTranDtl_Ts.= "Delete from tblEarnTranDtl where refNo in ('91_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."','92_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."','93_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."');";	
		
		if($TrnsA){
			$TrnsA = $this->execQry($qryDelEarnTranDtl_Ts);	
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
		$sumGov_Deduct = 0;
		$compEarnretain = $this->getCompany($this->session['company_code']);//company retention
		$earnRetention = (float)$compEarnretain['compEarnRetain'];//earn retention
		$totDedForPeriod =0;
		$totalGovDeducted =0;
		$positiveEarn = 0;
		$emp_AccruedDays =  array();
		$emp_AmntTardy =  array();
		$emp_AmntUt = array();
		$empBasicPay = array();
		
		$insEarnTranHeader = $cntrlNum = $insEarnTranDtl = $insStatementHdrDtl= "";
		$earnTrantrnAmount = 0;
		
		$Trns = $this->beginTran();//begin regular payroll transaction
		
		//transaction for TimeSheet Adjustment
		$arrTsAdjusments =$this->getTimeSheetAdjusments(0);
		
		if(($arrTsAdjusments["adjBasic"]!="") and ($arrTsAdjusments["adjBasic"]!='0.00'))
			$insEarnTranHeader.= "Insert into tblEarnTranHeader(compCode, refNo, trnCode, earnRem, earnStat, pdYear, pdNumber)
								  values('".$_SESSION["company_code"]."','91_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."','".ADJ_BASIC."','Time Sheet Adjusment for the period ".$_GET["pdNum"]." year ".$_GET["pdYear"]."','A','".$_GET["pdYear"]."','".$_GET["pdNum"]."');";
		
		if(($arrTsAdjusments["adjOt"]!="") and ($arrTsAdjusments["adjOt"]!='0.00'))
			$insEarnTranHeader.= "Insert into tblEarnTranHeader(compCode, refNo, trnCode, earnRem, earnStat, pdYear, pdNumber)
								  values('".$_SESSION["company_code"]."','92_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."','".ADJ_OT."','Time Sheet Adjusment for the period ".$_GET["pdNum"]." year ".$_GET["pdYear"]."','A','".$_GET["pdYear"]."','".$_GET["pdNum"]."');";
		
		
		if(($arrTsAdjusments["adjNd"]!="") and ($arrTsAdjusments["adjNd"]!='0.00'))
			$insEarnTranHeader.= "Insert into tblEarnTranHeader(compCode, refNo, trnCode, earnRem, earnStat, pdYear, pdNumber)
								  values('".$_SESSION["company_code"]."','93_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."','".ADJ_ND."','Time Sheet Adjusment for the period ".$_GET["pdNum"]." year ".$_GET["pdYear"]."','A','".$_GET["pdYear"]."','".$_GET["pdNum"]."');";
		
		
		if(count($this->getTimeSheetAdjusments(1))>0)
		{
			$cntrlNum = 1;
			foreach ((array)$this->getTimeSheetAdjusments(1) as $getTimeSheetAdjusments_val){//foreach pay basics
				if(($getTimeSheetAdjusments_val["adjBasic"]!="") and ($getTimeSheetAdjusments_val["adjBasic"]!='0'))
				{
					$insEarnTranDtl.="Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount, payGrp, payCat, earnStat, trnTaxCd)
								  values('".$_SESSION["company_code"]."','"."91_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."'".",'".$getTimeSheetAdjusments_val["empNo"]."','".$cntrlNum."','".ADJ_BASIC."',
								  '".sprintf("%01.2f",$getTimeSheetAdjusments_val["adjBasic"])."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','A','".ADJ_BASIC_TAXCD."');";
					


				}
				



				if(($getTimeSheetAdjusments_val["adjOt"]!="") and ($getTimeSheetAdjusments_val["adjOt"]!='0'))
				{
					$insEarnTranDtl.="Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount, payGrp, payCat, earnStat, trnTaxCd)
								  values('".$_SESSION["company_code"]."','"."92_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."'".",'".$getTimeSheetAdjusments_val["empNo"]."','".$cntrlNum."','".ADJ_OT."',
								  '".sprintf("%01.2f",$getTimeSheetAdjusments_val["adjOt"])."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','A','".ADJ_OT_TAXCD."');";
					
				}
				
				if(($getTimeSheetAdjusments_val["adjNd"]!="") and ($getTimeSheetAdjusments_val["adjNd"]!='0'))
				{
						$insEarnTranDtl.="Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount, payGrp, payCat, earnStat, trnTaxCd)
								  values('".$_SESSION["company_code"]."','"."93_".$_SESSION["company_code"]."_".$_SESSION["pay_group"]."_".$_SESSION["pay_category"]."_".$_GET["pdNum"]."".$_GET["pdYear"]."'".",'".$getTimeSheetAdjusments_val["empNo"]."','".$cntrlNum."','".ADJ_ND."',
								  '".sprintf("%01.2f",$getTimeSheetAdjusments_val["adjNd"])."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','A','".ADJ_ND_TAXCD."');";
				}	
					
				
				$cntrlNum++;
			}
			
			if(($insEarnTranHeader!="") and ($insEarnTranDtl!=""))
			{
				if($Trns){
					$insStatementHdrDtl = $insEarnTranHeader.$insEarnTranDtl;
					$Trns = $this->execQry($insStatementHdrDtl);
				}
			}
			
		}//end of transaction for TimeSheet Adjustment
		
		foreach ((array)$this->getEmpToProcPayBasic() as $empToProcPayBaicVal){//foreach pay basics
			
		
			if(trim($empToProcPayBaicVal['empPayType']) == 'M'){//monthly
				$arrDailiesLegHoliday = explode("-",$this->getDailiesLegHoliday($empToProcPayBaicVal['empNo'],$empToProcPayBaicVal['empDrate']));
				$trnCodePayBasicDaliesLegHoliday = $arrDailiesLegHoliday[0];
				$trnAmountPayBasicDaliesLegHoliday = $arrDailiesLegHoliday[1];
				if($trnAmountPayBasicDaliesLegHoliday > 0){
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDaliesLegHoliday,$trnAmountPayBasicDaliesLegHoliday);
					}
				}
			
				if(trim($empToProcPayBaicVal['empStat']) == 'RG'){//regular
					$trnCodePayBasicMreg = '0100';
					$trnAmountPayBasicMreg = $this->OneHalfBasic($empToProcPayBaicVal['empMrate']);	
					$empBasicPay[$empToProcPayBaicVal['empNo']] = $trnAmountPayBasicMreg;
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicMreg,$trnAmountPayBasicMreg);			
					}
				}
				else{//probationary or contractual
					//if datehired is less or equal to date period from do one half basic procedure
					
					$trnCodePayBasicNotReg = '0100';
					$trnAmountPayBasicNotReg = $this->OneHalfBasic($empToProcPayBaicVal['empMrate']);
					$empBasicPay[$empToProcPayBaicVal['empNo']] = $trnAmountPayBasicNotReg;
	
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
				
				$trnCodePayLegalDailiesBasic = $arrDailiesBasic[3];
				$trnAmountPayLegDailiesBasic = $arrDailiesBasic[4];
			
				
				//echo $emp_AccruedDays[$empToProcPayBaicVal['empNo']]."\n";
				
				if($trnAmountPayBasicDailiesBasic > 0){
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDailiesBasic,$trnAmountPayBasicDailiesBasic);
					}
				}
				
				if(($trnAmountPayLegDailiesBasic > 0)&&($trnAmountPayBasicDailiesBasic>0)){
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayLegalDailiesBasic,$trnAmountPayLegDailiesBasic);
					}
				}
				
				$empBasicPay[$empToProcPayBaicVal['empNo']] = $trnAmountPayBasicDailiesBasic;

				//legal holiday
				/*$arrDailiesLegHoliday = explode("-",$this->getDailiesLegHoliday($empToProcPayBaicVal['empNo'],$empToProcPayBaicVal['empDrate']));
				$trnCodePayBasicDaliesLegHoliday = $arrDailiesLegHoliday[0];
				$trnAmountPayBasicDaliesLegHoliday = $arrDailiesLegHoliday[1];
				if($trnAmountPayBasicDaliesLegHoliday > 0){
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDaliesLegHoliday,$trnAmountPayBasicDaliesLegHoliday);
					}
				}*/

				
			}//end of daily
			unset($trnAmountPayBasicNotReg);
		}//end of foreach pay basic	
		
		
		foreach ((array)$this->summarizeCorrection() as $arrSumCorrVal){//foreach timesheet	
			if($arrSumCorrVal['sumAmtAbsnt']){
				if((float)$arrSumCorrVal['sumAmtAbsnt'] != 0){
						if(($arrSumCorrVal['sumAmtAbsnt']*-1)>$empBasicPay[$arrSumCorrVal['empNo']])
							$trnAmountAbsent = ($this->getProRateAbsent($arrSumCorrVal['empNo'],$empBasicPay[$arrSumCorrVal['empNo']]))*-1;
						else
							$trnAmountAbsent = $arrSumCorrVal['sumAmtAbsnt'];
						
						$computedAbsentAmount = $empBasicPay[$arrSumCorrVal['empNo']] - ($trnAmountAbsent*-1);
						
						if(($computedAbsentAmount<1)&&($computedAbsentAmount!=0))
						{
							$trnAmountAbsent = $empBasicPay[$arrSumCorrVal['empNo']]*-1;
						}
						
						
						if($Trns){
							$Trns = $this->writeToTblEarnings('E1',$arrSumCorrVal['empNo'],'0113',$trnAmountAbsent);
						}
				}
			}
			
				
			if($arrSumCorrVal['sumAmtTardy']){
				if((float)$arrSumCorrVal['sumAmtTardy'] != 0){			
					if(($arrSumCorrVal['sumAmtTardy']*-1)>$empBasicPay[$arrSumCorrVal['empNo']])
						$trnAmountTardy = $empBasicPay[$arrSumCorrVal['empNo']] * -1;
					else
						$trnAmountTardy = $arrSumCorrVal['sumAmtTardy'];
					
					if($Trns){
						$Trns = $this->writeToTblEarnings('E1',$arrSumCorrVal['empNo'],'0111',$trnAmountTardy);
						$emp_AmntTardy[$arrSumCorrVal['empNo']] = $trnAmountTardy;
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
					if($sumPostvErn >= 0){
						
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
		
		foreach ((array)$this->AdjustmentAllowanceProc() as $AdjustmentAllowanceProcVal){//foreach for Allowance Adjustment
				if($Trns){
					$qryUpdtProctagOthAdjAllow = "UPDATE tblEarnTranDtl 
									   SET processTag = 'Y' 
									   WHERE compCode = '{$this->session['company_code']}'
									   AND empNo='".$AdjustmentAllowanceProcVal['empNo']."' and trnCode='".$AdjustmentAllowanceProcVal['trnCode']."' 
									   and refNo='".$AdjustmentAllowanceProcVal['refNo']."' and payCat='".$_SESSION["pay_category"]."' and payGrp='".$_SESSION["pay_group"]."'";
					
					$Trns = $this->execQry($qryUpdtProctagOthAdjAllow);
					$Trns = $this->writeToTblEarningsAllow('E1',$AdjustmentAllowanceProcVal['empNo'],$AdjustmentAllowanceProcVal['trnCode'],$AdjustmentAllowanceProcVal['sumEarnAmnt'],$AdjustmentAllowanceProcVal['sprtPS']);
					
				}
		}//end foreach for Adjustment Allowance
		
		foreach ((array)$this->getArrAllow($emp_AccruedDays,$emp_AmntTardy,$emp_AmntUt) as $arrAllwIndex => $arrAllwVal){//foeach for allowance
		
			
			$tmpAllwIndex = explode("-",$arrAllwIndex);
			$arrEmpAllow = $this->getEmpAllowance('tblAllowance',$tmpAllwIndex[0],'AND tblAllowance.allowCode = '.$tmpAllwIndex[2]);
			
			if((float)$arrAllwVal != 0){
				if($Trns){
					
					
					$qryToAllowBrkDwn = "INSERT INTO tblAllowanceBrkDwn(compCode,empNo,allowCode,allowAmt,allowSked,allowTaxTag,allowPayTag,allowStart,allowEnd,allowStat,pdYear,pdNumber,actualAmt,sprtPS, allowTag)
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
											   '{$arrEmpAllow[0]['sprtPS']}',
											   '{$arrEmpAllow[0]['allowTag']}')";	
				
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
		
		
		$arrEmpTaxAdj = $this->TaxAdjsutment();
		foreach ((array)$this->getEmpForDeduction() as $empForDedVal){//foreach for deductions
			$totalTaxDeducted = $empAddedTaxEarn = 0;
			
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
			$empMinWage_Basic 	=  (float) (($emp_BRate +  $emp_Tard + $emp_Ut + $emp_Absence + $emp_VLOP + $emp_SLOP) + $emp_AdjBasic);
			
			/*echo $empForDedVal['empNo']."\n";
				echo $emp_BRate."\n";
				echo $emp_Tard."\n";
				echo $emp_Ut."\n";
				echo $emp_Absence."\n";
				echo $emp_VLOP."\n";
				echo $emp_SLOP."\n";
				echo $emp_AdjBasic."\n";
				echo "Minimum Wage = ".$empMinWage_Basic."\n";*/
			


			$empEcola 	=   (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".ALLW_ECOLA3."'",'');	
			
			$SssEmp = $SssEmplr = $EcEmp = $PhicEmp = $PhicEmplr = $HdmfEmp = $HdmfEmplr = $empGovtDeduct = 0 ;
			
			if((int)$this->getCutOffPeriod() == 2)
			{
				$arr_EmpNoRemittance = $this->getEmpNoNonGovt();
				foreach($arr_EmpNoRemittance as $arr_EmpNoRemittance_val)
				{
					if($empForDedVal['empNo']==$arr_EmpNoRemittance_val["empNo"])
					{
						$empGovtDeduct = 1;
					}
				}
				
				if($empGovtDeduct!='1')
				{
					if($grossEarnings != 0 || $grossEarnings != "")
					{
						$prevEarn = (float)$this->getPrevGrossEarn($empForDedVal['empNo']);
						$monthLyGrossEarn = $grossEarnings+$prevEarn;
						$arrGovDedAmnt = $this->getGovDedAmnt($monthLyGrossEarn);
						if($monthLyGrossEarn != 0 || $monthLyGrossEarn != "")
						{
								if ($arrGovDedAmnt['sssEmployee']!="") {$SssEmp=$arrGovDedAmnt['sssEmployee'];} else {$SssEmp=0;}
								if ($arrGovDedAmnt['sssEmployer']!="") {$SssEmplr=$arrGovDedAmnt['sssEmployer'];} else {$SssEmplr=0;}
								if ($arrGovDedAmnt['EC']!="") {$EcEmp=$arrGovDedAmnt['EC'];} else {$EcEmp=0;}
								if ($arrGovDedAmnt['phicEmployee']!="") {$PhicEmp=$arrGovDedAmnt['phicEmployee'];} else {$PhicEmp=0;}
								if ($arrGovDedAmnt['phicEmployer']!=""){$PhicEmplr=$arrGovDedAmnt['phicEmployee'];} else {$PhicEmplr=0;}
								$HdmfEmp = 100;
								$HdmfEmplr = 100;
								$sumGov = $SssEmp + $PhicEmp + $HdmfEmp;
						}
						
					}
				}//End of if($_SESSION["pay_category"]!='1')
			}
			
			//echo "Sum of Governmentals = ".$sumGov ."\n";
			
			//taxable earnings for the period
			//$txbleEarningsPd = $taxableGrossEarn-(float)$sumGov;
			$txbleEarningsPd = $taxableGrossEarn;
			//Get Data of Employee in tblGov_Tax_Added for Tax Spread in Group 1
			
			$arrGetAddTax = $this->getEmpAddTaxableIncome($empForDedVal['empNo']);
			if($arrGetAddTax["amountToDed"]>0)
			{
				$empAddedTaxEarn = (float) $arrGetAddTax["amountToDed"];
			}
			
			//echo $empForDedVal['empNo']."=".$empAddedTaxEarn."\n";
			$sum_txbleEarningsPd_addTax = $txbleEarningsPd + $empAddedTaxEarn;
			
			//echo "Taxable Earnings = ".$txbleEarningsPd."\n";
			
			/* 	Computation of Witholding tax
				Created By		:	Genarra Jo - Ann Arong
				Date Created	:	10272009 Tuesday
			*/
		
			//$withTax = $this->computeEmpWithTax($empForDedVal['empNo'],$txbleEarningsPd,$taxableGrossEarn,$empForDedVal["empTeu"],$empForDedVal["empMrate"],$sumGov);
			
			//Annual Computation
			
			$withTax = $this->computeWithTax($empForDedVal['empNo'],$sum_txbleEarningsPd_addTax,$empForDedVal["empTeu"],$sumGov,$empMinWage_Basic);
			//tax adjustments
			
			foreach($arrEmpTaxAdj as $valTaxAdj) {
				if ($valTaxAdj['empNo']==$empForDedVal['empNo'])  {
					if ($valTaxAdj['wtax']<$withTax) {
						$withTax=$valTaxAdj['wtax'];
					}
					
				}
			}		
				
			if($withTax != 0){
				if($amntLimitDed >= $withTax){
					$amntLimitDed -= (float)$withTax;
					$totalTaxDeducted += $withTax; 
					if($Trns){
						$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'5100',$withTax);				
					}
				} elseif($amntLimitDed < $withTax && $amntLimitDed > 0){
					$totalTaxDeducted += $amntLimitDed; 
					if($Trns){
						$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'5100',$amntLimitDed);				
					}
					$amntLimitDed = 0;
					
				}
			}


			if((int)$this->getCutOffPeriod() == 2)
			{
					if((float)$SssEmp != 0){
						if($amntLimitDed >= (float)$SssEmp){
							$amntLimitDed -= (float)$SssEmp;
							$totalGovDeducted += (float)$SssEmp;
							if($Trns){
								$SssEmp = $SssEmp;
								$SssEmplr = $SssEmplr;
								$EcEmp = $EcEmp;
								$sumGov_Deduct+=$SssEmp;
								$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'5200',$SssEmp,0);
							}
						}
						else
						{
							$SssEmp = 0;
							$SssEmplr = 0;
							$EcEmp = 0;
						}
					}
					
					if((float)$PhicEmp!= 0){
						if($amntLimitDed >= (float)$PhicEmp){
							$amntLimitDed -= (float)$PhicEmp;
							$totalGovDeducted += (float)$PhicEmp;
							if($Trns){
								$PhicEmp = $PhicEmp;
								$PhicEmplr = $PhicEmplr;
								$sumGov_Deduct+=$PhicEmp;
								$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'5300',$PhicEmp,0);
							}
						}
						else
						{
							$PhicEmp = 0;
							$PhicEmplr = 0;
						}
					}
					
					
					if((float)$HdmfEmp!= 0){
						if($amntLimitDed >= (float)$HdmfEmp){
							$amntLimitDed -= (float)$HdmfEmp;
							$totalGovDeducted += (float)$HdmfEmp;
							if($Trns){ 
								$HdmfEmp = $HdmfEmp;
								$HdmfEmplr = $HdmfEmplr;
								$sumGov_Deduct+=$HdmfEmp;
								$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'5400',$HdmfEmp,0);						
							}
						}
						else
						{
							$HdmfEmp = 0;
							$HdmfEmplr = 0;
						}				
					}
					
				
					
					if(($totalGovDeducted!=0)||($totalGovDeducted!=""))
					{
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
												   '$HdmfEmp',
												   '$HdmfEmplr')";
											 
						if($Trns){
							$Trns = $this->execQry($qryToMtdGov);
						}
					}
				
			}
			
			/*echo "Tax Exemption = ".$empForDedVal["empTeu"]."\n";*/
			//echo "With tax = ".$withTax."\n";
			//echo "Amount Limit Ded = ".$amntLimitDed."\n";
			
			
			
			
			
			//end of wtax Adjustments
			//echo "Amount Limit Ded - With Tax = ".$amntLimitDed."\n\n";
			
			//for loans deductions
			$sprtAllowPSTotAmnt=0;
			$Advances = 0;
			$allowAdvance = 0;
			$amntLimitDedForAdvances = 0;
			$arrAllowSprtPS = $this->separatePaySlipAllow($empForDedVal['empNo'],"AND (sprtPS = 'Y') ","");			
			foreach ((array)$arrAllowSprtPS as $sprtAllowVal){

				$sprtAllowPSTotAmnt += (float)$sprtAllowVal['sprtPSAllwAmnt'];
				if (ALLW_ADVANCES == $sprtAllowVal['trnCode'] || ADJ_ADVANCES==$sprtAllowVal['trnCode']) {
					$allowAdvance += (float)$sprtAllowVal['sprtPSAllwAmnt'];
				}
				
			}
			$amntLimitDedForAdvances = round($sprtAllowPSTotAmnt - ($sprtAllowPSTotAmnt * ($earnRetention/100)),2);

			$resultloans = $this->getdeductionlist($empForDedVal['empNo'],",lonRefNo","");
			foreach($resultloans as $rsloans) {
				if ((float)$amntLimitDed >= (float)$rsloans['sumamount']) {
					$amntLimitDed = (float)$amntLimitDed - (float)$rsloans['sumamount'];
					if($Trns){
							$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$rsloans['sumamount'],$rsloans['lonRefNo'],'Y',0);
					}
				} elseif ((float)$amntLimitDed < (float)$rsloans['sumamount']) {
					if (substr($rsloans['lonTypeCd'],0,1) == 3) {
						if ($amntLimitDedForAdvances > 0 &&  $amntLimitDedForAdvances > (float)$amntLimitDed) {
							if ((float)$amntLimitDedForAdvances >= (float)$rsloans['sumamount']) {
								$amntLimitDedForAdvances -= $rsloans['sumamount'];
								if($Trns){
										$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$rsloans['sumamount'],$rsloans['lonRefNo'],'Y',1);
								}
							} elseif((float)$amntLimitDedForAdvances < (float)$rsloans['sumamount'] && (float)$amntLimitDedForAdvances > 0) {
								if($Trns){
										$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$amntLimitDedForAdvances,$rsloans['lonRefNo'],'P',1);
								}
								$amntLimitDedForAdvances = 0;							
							}

						} elseif ($amntLimitDedForAdvances <= (float)$amntLimitDed && (float)$amntLimitDed>0) {
							if($Trns){
									$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$amntLimitDed,$rsloans['lonRefNo'],'P',0);
							}
							$amntLimitDed = 0;						
						}		
					}
				}
			}
			$resultSumloans = $this->getdeductionlist($empForDedVal['empNo'],"","");			
			$totDedAdv = 0;
			foreach($resultSumloans as $valSumLoans) {
				if($Trns){
					if ($valSumLoans['dedtoAdv']==1)
						$totDedAdv +=(float)$valSumLoans['sumamount'];
					else
						$totDedForPeriod +=(float)$valSumLoans['sumamount'];
					
					//echo "Employee Loans = ".$valSumLoans['sumamount']."\n";
					$trns = $this->writeToTblDeduction($valSumLoans['empNo'],$valSumLoans['trnCode'],(float)$valSumLoans['sumamount'],$valSumLoans['dedtoAdv']);		
				}
			}
		
			//echo "Amount Limit Ded = ".$amntLimitDed."\n";
			//for other deductions
			$resultotherdeduction =$this->getotherdeductionlist($empForDedVal['empNo'],1);
			$totalTaxAdj = 0;
			foreach ($resultotherdeduction as $rsotherdeductionlist) {
				if ((float)$amntLimitDed >= (float)$rsotherdeductionlist['trnAmount']) {
					$amntLimitDed=(float)$amntLimitDed-(float)$rsotherdeductionlist['trnAmount'];
					//Wtax Adjustment
					if ($rsotherdeductionlist['trnCode'] == '8024') {
						$totalTaxAdj = (float)$rsotherdeductionlist['trnAmount'];
					}
					//$totDedForPeriod += (float)$rsotherdeductionlist['trnAmount'];
					//echo "Other Ded POSITIVE = ".(float)$rsotherdeductionlist['trnAmount']."\n";
					if($Trns){
						$Trns = $this->updateotherdeductions($rsotherdeductionlist['seqNo'],$rsotherdeductionlist['trnAmount'],'Y',0);
					}
				} elseif ((float)$amntLimitDed < (float)$rsotherdeductionlist['trnAmount']) {
					//$totDedForPeriod += $amntLimitDed;
					//echo "Other Ded NEGATIVE = ".$amntLimitDed."\n";
					if ($amntLimitDedForAdvances > 0 &&  $amntLimitDedForAdvances > (float)$amntLimitDed) {
							if ((float)$amntLimitDedForAdvances >= (float)$rsotherdeductionlist['trnAmount']) {
								$amntLimitDedForAdvances -= (float)$rsotherdeductionlist['trnAmount'];
								
								if($Trns){
										$Trns = $this->updateotherdeductions($rsotherdeductionlist['seqNo'],$rsotherdeductionlist['trnAmount'],'Y',1);
								}
							} elseif((float)$amntLimitDedForAdvances < (float)$rsotherdeductionlist['trnAmount'] && (float)$amntLimitDedForAdvances > 0) {
								if($Trns){
										$Trns = $this->updateotherdeductions($rsotherdeductionlist['seqNo'],$amntLimitDed,'P',1);
								}
								$amntLimitDedForAdvances = 0;							
							}					
					} else {
						if($Trns){
							$Trns = $this->updateotherdeductions($rsotherdeductionlist['seqNo'],$amntLimitDed,'P',0);
						}
						$amntLimitDed=0;
					}	
				}
			}
			
			//echo "Amount Limit Ded = ".$amntLimitDed."\n";
		
			$resultotherdeduction =$this->getotherdeductionlist($empForDedVal['empNo'],0);
			foreach ($resultotherdeduction as $rsotherdeductionlist) {
				//echo "Write tblDed = ".$rsotherdeductionlist['sumamount']."\n";
					if ($rsotherdeductionlist['dedtoAdv']==1)
						$totDedAdv +=(float)$rsotherdeductionlist['sumamount'];
					else
						$totDedForPeriod +=(float)$rsotherdeductionlist['sumamount'];
				
				if($Trns)
				{
					$Trns = $this->writeToTblDeduction($rsotherdeductionlist['empNo'],$rsotherdeductionlist['trnCode'],$rsotherdeductionlist['sumamount'],$rsotherdeductionlist['dedtoAdv']);
					//echo "Employee Other Ded = ".$rsotherdeductionlist['sumamount']."\n";
				}	
			}

			
			/*
				Modified By : Genarra JoAnn
				Reason	;	Level of Priority of Loans
			*/
			
			$resultloans = $this->getdeductionlist($empForDedVal['empNo'],",lonRefNo","levelPrio");
			foreach($resultloans as $rsloans) {
				if ((float)$amntLimitDed >= (float)$rsloans['sumamount']) {
					$amntLimitDed = (float)$amntLimitDed - (float)$rsloans['sumamount'];
					if($Trns){
							$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$rsloans['sumamount'],$rsloans['lonRefNo'],'Y',0);
					}
				} elseif ((float)$amntLimitDed < (float)$rsloans['sumamount']) {
					if (substr($rsloans['lonTypeCd'],0,1) == 3) {
						if ($amntLimitDedForAdvances > 0 &&  $amntLimitDedForAdvances > (float)$amntLimitDed) {
							if ((float)$amntLimitDedForAdvances >= (float)$rsloans['sumamount']) {
								$amntLimitDedForAdvances -= $rsloans['sumamount'];
								if($Trns){
										$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$rsloans['sumamount'],$rsloans['lonRefNo'],'Y',1);
								}
							} elseif((float)$amntLimitDedForAdvances < (float)$rsloans['sumamount'] && (float)$amntLimitDedForAdvances > 0) {
								if($Trns){
										$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$amntLimitDedForAdvances,$rsloans['lonRefNo'],'P',1);
								}
								$amntLimitDedForAdvances = 0;							
							}

						} elseif ($amntLimitDedForAdvances <= (float)$amntLimitDed && (float)$amntLimitDed>0) {
							if($Trns){
									$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$amntLimitDed,$rsloans['lonRefNo'],'P',0);
							}
							$amntLimitDed = 0;						
						}		
					}
				}
			}
			$resultSumloans = $this->getdeductionlist($empForDedVal['empNo'],"","levelPrio");			
			foreach($resultSumloans as $valSumLoans) {
				if($Trns){
					if ($valSumLoans['dedtoAdv']==1)
						$totDedAdv +=(float)$valSumLoans['sumamount'];
					else
						$totDedForPeriod +=(float)$valSumLoans['sumamount'];
					
					//echo "Employee Loans = ".$valSumLoans['sumamount']."\n";
					$trns = $this->writeToTblDeduction($valSumLoans['empNo'],$valSumLoans['trnCode'],(float)$valSumLoans['sumamount'],$valSumLoans['dedtoAdv']);		
				}
			}
			/*End of Loan with regard to level of priority*/

			
			$sprtAllowPSTotAmnt -= 	$totDedAdv;
			$empInfo = $this->getUserInfo($this->session['company_code'],$empForDedVal['empNo'],'');
			$totDedForPeriod+= (float)$sumGov_Deduct;
			//echo "Total Ded = ".$totDedForPeriod."\n";
			//echo "Sum Gov = ".$sumGov."\n";
			
			$arrEmpNonTaxAllowTot = $this->getEmpAllowance('tblAllowanceBrkDwn',$empForDedVal['empNo'],"AND (tblPayTransType.trnTaxCd = 'N' or tblPayTransType.trnTaxCd='' or tblPayTransType.trnTaxCd is null) AND (tblAllowanceBrkDwn.sprtPS IS NULL or tblAllowanceBrkDwn.sprtPS ='' or tblAllowanceBrkDwn.sprtPS='N') ");
			
			foreach ($arrEmpNonTaxAllowTot as $empNonTaxAllowTotVal){
				$totEmpNonTaxAllow += (float)$empNonTaxAllowTotVal['allowAmt'];
			}
			
		
			
			//$netsalary = ($grossEarnings+$txbleEarningsPd)-($totDedForPeriod+$totalTaxDeducted);
			/*echo "Gross Earnings = ".$grossEarnings."\n";*/
			
			//echo "Tax = ".$totalTaxDeducted."\n";
			
			$netsalary = ($grossEarnings)-($totDedForPeriod+$totalTaxDeducted);
			$totalTaxDeducted += $totalTaxAdj;
			
			$dataToYtdHist 	   = $this->getDataToYtdDataHist($empForDedVal['empNo']);
			$newYtdTaxable     = (float)$dataToYtdHist['YtdTaxable'] + (float)$txbleEarningsPd;
			$newYtdGovDed      = (float)$dataToYtdHist['YtdGovDed'] + (float)$totalGovDeducted;
			$newYtdTax         = (float)$dataToYtdHist['YtdTax'] + (float)$totalTaxDeducted;

			$totDedForPeriod   += $totDedAdv;
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
															  sprtAllow,
															  empBasic,
															  empMinWageTag,
															  empEcola,
															  empTeu,
															  empYtdTaxable,
															  empYtdTax,
															  empYtdGovDed,
															  sprtAllowAdvance)
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
															   '".sprintf("%01.2f",$sprtAllowPSTotAmnt)."',
															   '".sprintf("%01.2f",$empMinWage_Basic)."',
															   '".$empForDedVal['empWageTag']."',
															   '".sprintf("%01.2f",$empEcola)."',
															   '".$empForDedVal["empTeu"]."',
															   '".sprintf("%01.2f",$newYtdTaxable)."',
															   '".sprintf("%01.2f",$newYtdTax)."',
															   '".sprintf("%01.2f",$newYtdGovDed)."',
															   '".sprintf("%01.2f",$allowAdvance)."'
															   )";				
			if($Trns){
				$Trns = $this->execQry($qryToPayrollSum);
			}
				
			
			$qryToYtdData = "INSERT INTO tblYtdData(compCode,pdYear,empNo,YtdGross,YtdTaxable,YtdGovDed,YtdTax,YtdNonTaxAllow,Ytd13NBonus,Ytdtx13NBonus,payGrp,pdNumber,YtdBasic,sprtAllow)
							 VALUES('{$this->session['company_code']}',
							 		'{$this->get['pdYear']}',
							 		'{$empForDedVal['empNo']}',
							 		'".sprintf("%01.2f",$grossEarnings)."',
							 		'".sprintf("%01.2f",$sum_txbleEarningsPd_addTax)."',
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
			
			unset($grossEarnings,$netPayEarnings,$amntLimitDed,$nonTaxEarn,$taxableGrossEarn,$sumGov_Deduct,$sumGov,$withTax,$totDedForPeriod,$netsalary,$totalGovDeducted,$arrAllwVal,$totEmpNonTaxAllow,$totalTaxDeducted,$ArSeq,$totaloansandadjustment,$sprtAllowPSTotAmnt,$empMinWage_Basic,$emp_BRate,$emp_Tard,$emp_Ut,$emp_Absence,$emp_VLOP,$emp_SLOP,$emp_AdjBasic,$empBasicPay,$trnAmountAbsent,$empEcola,$totalTaxAdj,$computedAbsentAmount,$prevEarn,$monthLyGrossEarn,$arrGovDedAmnt,$ArrSumGov,$trnAmountTardy,$sum_txbleEarningsPd_addTax,$empAddedTaxEarn);
			
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