<?
class lastPayProcObj extends commonObj {
	
	var $get;//method
	
	var $session;//session variables
	var $arrEmpForAdj = array();
	
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
	
	private function lastPayData($empNo='') {
		if ($empNo != "") {
			$filter = " AND empNo='$empNo'";
		}
		$qrylastPay = "Select * from tblLastPayData where empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
											$filter
                            				)";
		if ($empNo == "")
			return $this->getArrRes($this->execQry($qrylastPay));
		else
			return $this->getSqlAssoc($this->execQry($qrylastPay));
	}
	private function summarizeCorrection(){
		
		 $qrySummrzeCrrctns = "SELECT compCode,empNo,SUM(amtAbsent)*-1 as sumAmtAbsnt,SUM(AmtTardy)*-1 as sumAmtTardy ,SUM(AmtUt)*-1 as sumAmtUt
							  FROM tblTimeSheet
							  WHERE compCode = '{$this->session['company_code']}'
						      AND empPayGrp = '{$this->session['pay_group']}'
							  AND empPayCat = '{$this->session['pay_category']}'
							  AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)
							 AND tsDate between '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}' 
							  GROUP BY compCode, empNo  \n\n";

		// $qrySummrzeCrrctns ="SELECT compCode,empNo,SUM(amtAbsent)*-1 as sumAmtAbsnt,SUM(AmtTardy)*-1 as sumAmtTardy ,SUM(AmtUt)*-1 as sumAmtUt
		// 					  FROM tblTimeSheet
		// 					  WHERE compCode = '1'
		// 				      AND empPayGrp = '1'
		// 					  AND empPayCat = '9'
		// 					  AND empNo IN (
		// 					  				SELECT empNo FROM tblLastPayEmp
  //                           				WHERE compCode = '1' 
		// 									AND pdYear = '2020'
		// 									AND pdNumber = '1'
  //                                           AND payGrp = '1'
  //                           				)
		// 					 AND tsDate between '2020-01-01' AND '2020-01-15' 
		// 					  GROUP BY compCode, empNo ";
	
		$resSummrzeCrrctns = $this->execQry($qrySummrzeCrrctns);
		return $this->getArrRes($resSummrzeCrrctns);
	}
	
	function getEmpForAdj() {
		$datehired = ($_SESSION['pay_group']==1) ? "dateHired<='4/17/2011'":"dateHired<='4/22/2011'";
		$sqlEmpList = "Select empNo from tblEmpmast where $datehired and empPayType='M' AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)";
		$res = $this->execQry($sqlEmpList);
		$res = $this->getArrRes($res);
		foreach($res as $val) {
			$this->arrEmpForAdj[] = $val['empNo'];	
		}
		
										
	}
	
	private function summarizeOtAndNd(){
		
		$qrySummrzeOtAndNd = "SELECT compCode, empNo, dayType, SUM(amtOtLe8) AS sumAmtOtLe8, SUM(amtOtGt8) AS sumAmtOtGt8, SUM(amtNdLe8) AS sumAmtNdLe8, SUM(amtNdGt8) 
		                      AS sumAmtNdGt8, trnOtLe8, trnOtGt8, trnNdLe8, trnNdGt8
							  FROM  tblTimeSheet
							  WHERE (compCode = '{$this->session['company_code']}') 
							  AND (empPayGrp = '{$this->session['pay_group']}') 
							  AND (empPayCat = '{$this->session['pay_category']}')
							  AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
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
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)
							  AND refNo in 	(Select refNo from tblEarnTranHeader where compCode='{$_SESSION['company_code']}' and earnStat='A' 
											   AND pdYear = '{$this->get['pdYear']}'
											   AND pdNumber = '{$this->get['pdNum']}')
							  GROUP BY empNo, trnCode, trnTaxCd ";
		if($sign == "<"){
			$qryPostAdjOthrs .= "ORDER BY empNo,sumEarnAmnt DESC";
		}
		//Secho $qryPostAdjOthrs."\n\n";
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
							AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)";
		
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
		
		$emp_regDays = $amountDailiesBasic = $amountLegalBasic = $emp_legHolDays = 0;
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
					$emp_legHolDays++;
					$emp_regDays++;
					$emp_allowDays++;
				}
				else
				{
					$emp_legHolDays++;
					$emp_legDays++;
					$emp_allowDays++;
				}
			}
		}
		
		$amountDailiesBasic = 	$emp_regDays * (float)$dailyRate;
		$amountLegalBasic = 	$emp_legDays * (float)$dailyRate;
		
		//echo $empNo."\nDaily Rate=".$dailyRate."\n"."Regular Days = ".$emp_regDays."\nLegal Days=".$emp_legDays."\nAllowance Days = ".$emp_allowDays."\nAmount Daily Basic=".$amountDailiesBasic."\nAmount Legal Pay = ".$amountLegalBasic."\n";
		
		return $trnCode."-".sprintf("%01.2f",$amountDailiesBasic)."-".$emp_allowDays."-".$trnCode_LegPay."-".sprintf("%01.2f",$amountLegalBasic)."-$emp_legHolDays";
		
		
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
		$resGetLstAbsent  = $this->execQryI($qryGetLstAbsent );
		$rowGetLstAbsent  = $this->getArrResI($resGetLstAbsent );
		
		foreach($rowGetLstAbsent as $rowGetLstAbsent_Val)
		{
			if($rowGetLstAbsent_Val["dayType"]=='01'){
			
				if($rowGetLstAbsent_Val["hrsAbsent"]=='4'){
					//$cntAbsent+=0.5;
					$cntAbsent+=$rowGetLstAbsent_Val["amtAbsent"];
				}
				
				if($rowGetLstAbsent_Val["hrsAbsent"]=='8'){
					//$cntAbsent++;
					$cntAbsent+=$rowGetLstAbsent_Val["amtAbsent"];
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
			//$empAmntAbsent = $cntAbsent*$empProRateDaily;
			$empAmntAbsent = $cntAbsent;

			$empAmntAbsent = sprintf("%01.2f",$empAmntAbsent);
		}
		
		return $empAmntAbsent;
		//echo $empNo."=Hrs. Absent = ".$cntAbsent."\nPresent = ".$cntPresent."\nAmount Absent = ".$empAmntAbsent."\nCalendar Days = ".$calDays."\nRegular Days = ".$cntRDLG."\nNo. Reg. Days = ".$noRegDays."\nBasic Pay = ".$basicPay."\nPro Rate Basic = ".$empProRateDaily."\nAmount Absent = ".$empAmntAbsent."\n\n";

		// $cntAbsent = $cntPresent = $cntRDLG= $empProRateDaily = 0;
		// $qryGetLstAbsent = "SELECT * FROM tblTimeSheet
		// 					  WHERE compCode = '{$this->session['company_code']}'
		// 					  AND empNo = '{$empNo}' 
		// 					  AND tsDate >= '{$this->get['dtFrm']}'
		// 					  AND tsDate <= '{$this->get['dtTo']}' 
		// 					  AND empPayCat='".$_SESSION["pay_category"]."'
		// 					  AND empPayGrp='".$_SESSION["pay_group"]."'";
		// $resGetLstAbsent  = $this->execQry($qryGetLstAbsent );
		// $rowGetLstAbsent  = $this->getArrRes($resGetLstAbsent );
		
		// foreach($rowGetLstAbsent as $rowGetLstAbsent_Val)
		// {
		// 	if($rowGetLstAbsent_Val["dayType"]=='01'){
			
		// 		if($rowGetLstAbsent_Val["hrsAbsent"]=='4'){
		// 			$cntAbsent+=0.5;
		// 		}
				
		// 		if($rowGetLstAbsent_Val["hrsAbsent"]=='8'){
		// 			$cntAbsent++;
		// 		}
				
		// 		if($rowGetLstAbsent_Val["hrsAbsent"]=='0'){
		// 			$cntPresent++;
		// 		}
		// 	}
		// 	elseif(($rowGetLstAbsent_Val["dayType"]=='02')||($rowGetLstAbsent_Val["dayType"]=='03')){
		// 		$cntRDLG++;
		// 	}	
		// }
		// if (in_array($empNo,$this->arrEmpForAdj)) {
		// 	if ($cntPresent >= 2)
		// 		 $cntAbsent = $cntAbsent+2;
		// 	elseif ($cntPresent == 1)
		// 		$cntAbsent++;
		// }
		
		
		// if($cntPresent==0){
		// 	$empAmntAbsent = $basicPay;
		// }else{
		// 	$calDays = $this->getCalendarDays($this->get['dtFrm'], $this->get['dtTo'])+1;
		// 	$noRegDays = $calDays - $cntRDLG;
		// 	//$empProRateDaily = $basicPay/$noRegDays;
		// 	$empProRateDaily = $basicPay/13;
		// 	$empAmntAbsent = $cntAbsent*$empProRateDaily;
		// 	$empAmntAbsent = sprintf("%01.2f",$empAmntAbsent);
		// }
		
		// return $empAmntAbsent;
		// //echo $empNo."=Hrs. Absent = ".$cntAbsent."\nPresent = ".$cntPresent."\nAmount Absent = ".$empAmntAbsent."\nCalendar Days = ".$calDays."\nRegular Days = ".$cntRDLG."\nNo. Reg. Days = ".$noRegDays."\nBasic Pay = ".$basicPay."\nPro Rate Basic = ".$empProRateDaily."\nAmount Absent = ".$empAmntAbsent."\n\n";
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

			$qryGetEmpAllow = "SELECT $table.allowTag, $table.compCode, $table.empNo, $table.allowCode, $table.allowAmt, $table.allowSked, $table.allowTaxTag,
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
						  AND (tblEmpMast.empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)) 						 
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
	
		private function getArrAllow($emp_AccruedDays,$emp_AmntTardy,$emp_AmntUt,$emp_LegalDays){
		
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
									$LegalOthrs = $this->getLegalOThrs($allowArrVal['empNo']);
									$dai_allowAmnt  = ($allowArrVal['allowTag']=='D'?$allowArrVal['allowAmt']:$allowArrVal['allowAmt']/$noCompDays);

									$totalAllowAmnt = $totalAllowAmnt + (($dai_allowAmnt/8) * $LegalOthrs);
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
						if ($empEarnings_numerator !=0 && $empEarnings_denominator != 0) {
							$totalAllowAmnt = $dai_allowAmnt * ($empEarnings_numerator/$empEarnings_denominator);
							
							if ((int)$emp_LegalDays[$allowArrVal['empNo']] >0) {
								$LegalOthrs = $this->getLegalOThrs($allowArrVal['empNo']);
								$dai_allowAmnt  = $allowArrVal['allowAmt']/$noCompDays;
								$totalAllowAmnt = $totalAllowAmnt + (($dai_allowAmnt/8) * $LegalOthrs);
							}							
						} else {
							$totalAllowAmnt = 0;
						}
							
						$totalAllowAmnt = sprintf("%01.2f", $totalAllowAmnt);
					}
					else
					{
						if(trim($allowArrVal['attnBase'])=='Y')
							$empTardUt = (float)$empTardUt*-1;
						else
							$empTardUt = 0;
						
						$totalAllowAmnt =	$dai_allowAmnt * ($emp_AccruedDays[$allowArrVal['empNo']] - ($empTardUt/$allowArrVal['empDrate']));

						if ((int)$emp_LegalDays[$allowArrVal['empNo']]>0) 
							$totalAllowAmnt = $totalAllowAmnt + $dai_allowAmnt * $emp_LegalDays[$allowArrVal['empNo']];


						$LegalOthrs = $this->getLegalOThrs($allowArrVal['empNo']);
						$dai_allowAmnt  = $allowArrVal['allowAmt'];
						$totalAllowAmnt = $totalAllowAmnt + (($dai_allowAmnt/8) * $LegalOthrs);

					}
					
					$totalAllowAmnt = ($totalAllowAmnt!=0?sprintf("%01.2f",$totalAllowAmnt):0);
					
					
				break;
			}
		
			$arrChecker[] = $allowArrVal['empNo']."-".$allowArrVal['trnCode'];
			//echo $allowArrVal['empNo']."=".$totalAllowAmnt;
			if(in_array($allowArrVal['empNo']."-".$allowArrVal['trnCode'],$arrChecker)){
				$arrAllow[ $allowArrVal['empNo']."-".$allowArrVal['trnCode']."-".$allowArrVal['allowCode'] ] += $totalAllowAmnt;
			}
			
			unset($totalAllowAmnt,$withTardy,$allowAmnt);		
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


	private function getLegalOThrs($empNo) {
		$sqlLeaglOThrs = "Select sum(hrsOTLe8) as otHrs from tblTimesheet where empNo='$empNo' and daytype IN (3,5) and hrsOTLe8>0";	
		$res = $this->getSqlAssoc($this->execQry($sqlLeaglOThrs));		
		return (float)$res['otHrs'];
	}


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
										   '{$tranCode}','".sprintf("%01.2f",$tranAmount)."','{$finalTaxTag}','{$separatePS}')\n\n";
		
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
								AND tblEmpMast.empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)
								group by tblEmpMast.empNo, tblEmpMast.empTeu, tblEmpMast.empMrate, tblEmpMast.empBrnCode, tblEmpMast.
empWageTag
								";

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
		
		$qryGetEmpGrossEarn = "SELECT ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo, SUM(ern.trnAmountE) AS amount
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
		$qryGetEmpGrossEarn	.= "GROUP BY ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo \n\n";


		$rowGetEmpGrossEarn = $this->getSqlAssoc($this->execQry($qryGetEmpGrossEarn));
		return $rowGetEmpGrossEarn['amount'];
	}
	
	private function separatePaySlipAllow($empNo,$and){
		
		 $qryGetSprtPS = "SELECT ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo, SUM(ern.trnAmountE) AS sprtPSAllwAmnt, ern.trnCode
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
		 $qryGetSprtPS	.= "GROUP BY ern.compCode, ern.pdYear, ern.pdNumber, ern.empNo,ern.trnCode \n\n";
		
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
	private function getEmpAddTaxableIncome($empNo)
	{
		$qryAddTaxable = "Select sum(amountToDed) as amountToDed from tblGov_Tax_Added where empNo='".$empNo."'  and addStat='N' ";
		$resAddTaxable = $this->execQry($qryAddTaxable);
		return  $this->getSqlAssoc($resAddTaxable);
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
											  (SELECT empNo FROM tblLastPayEmp
									WHERE compCode = '{$this->session['company_code']}' 
									AND pdYear = '{$this->get['pdYear']}'
									AND pdNumber = '{$this->get['pdNum']}' )) AND
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
		
		if($table=="tblTimeSheetHist")
			return ($emp_cntAbsent!=""?$emp_cntAbsent:12);
		else
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
						$wheretrnCode = "and trnCode in (".EARNINGS_LEGALPAY.",".EARNINGS_BASIC.",".EARNINGS_ABS.",".EARNINGS_UT.",".EARNINGS_TARD.")";
					else
						$wheretrnCode = "and trnCode in (".EARNINGS_LEGALPAY.",".EARNINGS_BASIC.",".EARNINGS_UT.",".EARNINGS_TARD.")";
				}
				else
				{
					$wheretrnCode = "and trnCode in (".EARNINGS_LEGALPAY.",".EARNINGS_BASIC.",".EARNINGS_ABS.",".EARNINGS_UT.",".EARNINGS_TARD.")";
				}
			}
			else
			{
				if($cntAbsentEmp>12)
					$wheretrnCode = "and trnCode in (".EARNINGS_LEGALPAY.",".EARNINGS_BASIC.",".EARNINGS_ABS.")";
				else
					$wheretrnCode = "and trnCode in (".EARNINGS_LEGALPAY.",".EARNINGS_BASIC.")";
			}
		}
		else
		{
			$wheretrnCode = "and trnCode in (".EARNINGS_LEGALPAY.",".EARNINGS_BASIC.")";
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
			//echo $qryGetAllwTs."\n\n";
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
			
		/*$empNoWorkDays = $empNoWorkingDays - ($empRestDay+($empRestDay_prev==0?2:$empRestDay_prev)) - $empHolDay - $empCntOfAbsences - ($empSumTardUt/8);
		
		$empAllowAmt = $d_allowAmnt * $empNoWorkDays;
		
		$empAllowAmt = ($empAllowAmt<0?0:sprintf("%01.2f",$empAllowAmt));*/
		//echo $empNo."=".$noCompDays."=". $empNoWorkingDays."=".$d_allowAmnt."\n";
		$empAllowAmt = ((($noCompDays -  $empCntOfAbsences - ($empSumTardUt/8)) /$noCompDays)*$d_allowAmnt);
		
		
		//echo $empNo."=".$empAllowAmt."\n";
		return (float)$empAllowAmt;
		//echo $empNo."=".($empRestDay+($empRestDay_prev==0?2:$empRestDay_prev))."\n";
		//echo $empNo."\n".$empNoWorkingDays."- (".$empRestDay."+(".$empRestDay_prev."!=0?".$empRestDay_prev.":2)) - ".$empHolDay." - ".$empCntOfAbsences." - (".$empSumTardUt."/8)\n";
		//echo $empNo."\nAllowance Amount = ".$d_allowAmnt."\nCompany Days = ".$noCompDays."\nQuery = ".$qryGetAllwTs."\nQuery Previous = ".$qryGetAllwTs_prev."\nNo. of Absent = ".$empNoAbsences."\nCount of Absences = ".$empCntOfAbsences."\nNo. of RestDays = ".$empRestDay."\nNo of Legal Days = ".$empHolDay."\nSum Tard and UT =".$empSumTardUt."\nWorking Days = ".$empNoWorkingDays."\nFinal Working Days = ".$empNoWorkDays."\nEmp. Allow Amnt = ".$empAllowAmt."\n\n";
		
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
	
	private function writetotblLastPayBal($empNo,$trnCode,$refNo,$Amt) {

		$qrytblLastPaybal = "Insert into tblLastPaybal 
							(compCode,empNo,trnCode,refNo,Amount,pdYear,pdNumber)
							values
							('{$_SESSION['company_code']}','$empNo','$trnCode','$refNo','$Amt','{$this->get['pdYear']}','{$this->get['pdNum']}')
							";
		return $this->execQry($qrytblLastPaybal);
	}
		
	private function getAnnualTax($taxInc)
	{
		$qrycomputeWithTax = "Select * from tblAnnTax where $taxInc between txLowLimit and txUpLimit";
		$rescomputeWithTax = $this->execQry($qrycomputeWithTax);
		$rowcomputeWithTax = $this->getSqlAssoc($rescomputeWithTax);
		$compTax = ((($taxInc-$rowcomputeWithTax["txLowLimit"])*$rowcomputeWithTax["txAddPcent"])+$rowcomputeWithTax["txFixdAmt"]);
		
		return (float)$compTax;
	}
	
	private function computeWithTax($empNo,$gross_Taxable,$empTeu,$minBasicPay)
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
		$arrEmpInfo = $this->getEmpInfo($empNo);
		$empPrevTag = $arrEmpInfo["empPrevTag"];
		$empMinTag = $arrEmpInfo["empWageTag"];
		
		if($empPrevTag=='Y')
		{
			//Get Previous Employer Data to tblPrevEmployer
			$empPrevEarnings = $this->getPrevEmplr($empNo,'prevEarnings');
			$empPrevTaxes = $this->getPrevEmplr($empNo,'prevTaxes');
			//Prev Employer (Puregold Company)
			$empPrevGovDed = $this->getPrevEmplr($empNo,'nonTaxSss');
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
			
/*			$estEarn = 	  (float) $gross_Taxable + (float) $arrYtdDataHist["YtdTaxable"] + (float)$empPrevEarnings -  (float) $arrYtdDataHist["YtdGovDed"] - (float) $arrYtdDataHist["YtdBasic"] 
					 	- (float) $basicPay - (float)$empPrevGovDed;*/
			$estEarn = 0;
		}
		else
		{
			
			$estEarn = 	  (float) $gross_Taxable + (float) $arrYtdDataHist["YtdTaxable"] + (float)$empPrevEarnings -  (float) $arrYtdDataHist["YtdGovDed"] - (float)$empPrevGovDed;

		}
		
/*		if ($empNo=='250000594') {
			echo "$estEarn = $gross_Taxable + (float) {$arrYtdDataHist['YtdTaxable']} + (float)$empPrevEarnings -  (float) {$arrYtdDataHist['YtdGovDed']} - (float)$empPrevGovDed\n";
		}*/		
		//Compute for the Net Taxable Earnings
		$netTaxable = (float) $estEarn - (float) $this->getTaxExemption($empTeu);
/*		if ($empNo=='250000594') {
			echo "$netTaxable\n";
		}*/
		//Compute the Estimated Tax using the Annual Tax Table
		if ($netTaxable <= 0) {
			return $arrYtdDataHist["YtdTax"] - ($arrYtdDataHist["YtdTax"]*2);
		} else {
			$estTaxYear = $this->getAnnualTax($netTaxable);
		
			//Compute Taxes
			$taxPeriod =  $estTaxYear - $empPrevTaxes;
			
				
			if ($taxPeriod<=0) {
				$taxDue = $arrYtdDataHist["YtdTax"];
			} else {
				$taxDue = $taxPeriod-$arrYtdDataHist["YtdTax"];
			}	
			return (float)$taxDue ;
		}
		
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
                            WHERE lonsked IN (1,2,3)))
					".$whereLonType."
					GROUP BY tblEmpLoansDtl.lonTypeCd, tblEmpLoansDtl.empNo, tblLoanType.trnCode,dedtoAdv $lonRefNo 
					ORDER BY tblEmpLoansDtl.empNo, tblEmpLoansDtl.lonTypeCd";

		
		//echo $qryloans."\n\n";
		return $this->getArrRes($this->execQry($qryloans));	
	}
	
	function getotherdeductionlist($empNo,$cat){
		if ($cat==1) {
			$qryother="select ActualAmt,trnAmount,trnCode,seqNo,empNo,refNo from tblDedTranDtl 
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
									";
		return $this->execQry($qryupdateloansdtl);
	}

	private function compute13thmonth($empNo,$curBasic,$curAllow) {
		$datestart=date('Y').'01-01 00:00:00'; 
		// $sqlEmpMast = "Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' and empNo='$empNo' and (date_add(dateHired,INTERVAL 1 month)<=dateResigned or date_add(dateHired,interval 1 month)<=endDate)\n\n";
		$sqlEmpMast ="Select empNo  from tblEmpMast where compCode='{$_SESSION['company_code']}' and empNo='$empNo' and date_add('$datestart',INTERVAL 1 month)<=dateResigned";
		if ($this->getRecCount($this->execQry($sqlEmpMast)) != 0 ){
			  $qry13thcheck = "SELECT empBasic FROM tblPayrollSummaryHist where ((pdYear=Year(curdate()) and pdNumber<23) 
						OR (pdYear=Year(curdate())-1 and pdNumber>22)) AND (empNo='$empNo') and compCode='{$_SESSION['company_code']}' and empNo NOT IN (Select empNo from tblPayrollSummaryhist where compCode='{$_SESSION['company_code']}' AND pdNumber='25' and pdyear=Year(curdate()));\n";
			  $qry13th = "SELECT (SUM(empBasic)+$curBasic)/12 as regular,(SUM(sprtAllowAdvance)+$curAllow)/12 as allow FROM tblPayrollSummaryHist where ((pdYear=Year(curdate()) and pdNumber<23) 
						OR (pdYear=Year(curdate())-1 and pdNumber>22)) AND (empNo='$empNo') and compCode='{$_SESSION['company_code']}' and empNo NOT IN (Select empNo from tblPayrollSummaryhist where compCode='{$_SESSION['company_code']}' AND pdNumber='25' and pdyear=Year(curdate()));\n";
			if ($this->getRecCount($this->execQry($qry13thcheck))>0) {			
				return $this->getSqlAssoc($this->execQry($qry13th));
			} else {
				if ($curBasic>0)
					$arr13thmonth['regular'] = (float)$curBasic/12;
				
				if ($curAllow>0)
					$arr13thmonth['allow'] = (float)$curAllow/12;
					
				return $arr13thmonth;
			}
		} else {
			return 0;
		}		
	}		
	//end of will program		
	###########################end for loans and other dedcutions########################################
	######################################################END OF DEDUCTIONS###################################################################
	
	function reProcLastPayroll(){
		
		$TrnsA = $this->beginTran();

		
		$qryDeleAllowBrkDwn = "DELETE FROM tblAllowanceBrkDwn 
								WHERE compCode = '{$this->session['company_code']}'
								AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)
								AND allowSked IN (".$this->getCutOffPeriod().",3)";
		if($TrnsA){
			$TrnsA = $this->execQry($qryDeleAllowBrkDwn);
		}	

		 $qryDeleEan = "DELETE FROM tblEarnings 
		               WHERE compCode = '{$this->session['company_code']}'
					   AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				) ";
		if($TrnsA){
			$TrnsA = $this->execQry($qryDeleEan);
		}

		$qryDeleDeductions = "DELETE FROM tblDeductions 
							  WHERE compCode = '{$this->session['company_code']}'
							 AND pdYear = '{$this->get['pdYear']}'
						     AND pdNumber = '{$this->get['pdNum']}'
						     AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)";
		if($TrnsA){
			$TrnsA = $this->execQry($qryDeleDeductions);
		}

		$qryDeleMtdGovt = "DELETE FROM tblMtdGovt 
						   WHERE compCode = '{$this->session['company_code']}'
						   AND pdYear = '{$this->get['pdYear']}'
						   AND pdMonth = '{$this->get['pdMonth']}'
						   AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)"; 
		if($TrnsA){
			$TrnsA = $this->execQry($qryDeleMtdGovt);
		}

		$qryDeleYtdData = "DELETE FROM tblYtdData 
							 WHERE compCode = '{$this->session['company_code']}'
							 AND pdYear = '{$this->get['pdYear']}'
						     AND pdNumber = '{$this->get['pdNum']}'
						     AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
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
									AND (trnCat='{$_SESSION['pay_category']}')
									AND (trnGrp='{$_SESSION['pay_group']}')
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
		$qryLastpPayBal = "Delete from tbllastPaybal where compCode='{$_SESSION['company_code']}'  
									AND (pdYear = '" . $this->get['pdYear'] . "') 
							        AND (pdNumber = '" . $this->get['pdNum'] . "')
									AND empNo IN (Select empNo from tblEmpMast where empPayGrp='{$_SESSION['pay_group']}')
";
		if($TrnsA){
			$TrnsA = $this->execQry($qryLastpPayBal);	
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
	
	
	public function mainProcLastPayroll(){
		
		$sumGov = 0;
		$compEarnretain = $this->getCompany($this->session['company_code']);//company retention
		$earnRetention = (float)$compEarnretain['compEarnRetain'];//earn retention
		$totDedForPeriod =0;
		$totalGovDeducted =0;
		$positiveEarn = 0;
		$emp_AccruedDays =  array();
		$emp_LegalDays =  array();
		$emp_AmntTardy =  array();
		$emp_AmntUt = array();
		$empBasicPay = array();
		
		$Trns = $this->beginTran();//begin regular payroll transaction
		
		$qryDeleYtdData = "DELETE FROM tblYtdData 
							 WHERE compCode = '{$this->session['company_code']}'
							 AND pdYear = '{$this->get['pdYear']}'
						     AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$this->session['company_code']}' 
											AND pdYear = '{$this->get['pdYear']}'
											AND pdNumber = '{$this->get['pdNum']}'
                                            AND payGrp = '{$this->session['pay_group']}'
                            				)";
		if($Trns){

			$Trns = $this->execQry($qryDeleYtdData);
		}		
		foreach((array)$this->lastPayData() as $lastPayVal) { //last pay data
			if ((float)$lastPayVal['leaveAmt'] != 0) {//Unused leave (Amount)
				if($Trns){
						$Trns = $this->writeToTblEarnings('E1',$lastPayVal['empNo'],'8115',$lastPayVal['leaveAmt']);			
				}
			}
		}//end of last pay data
		

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
				$emp_LegalDays[$empToProcPayBaicVal['empNo']] = $arrDailiesBasic[5];

				$trnCodePayLegalDailiesBasic = $arrDailiesBasic[3];
				$trnAmountPayLegDailiesBasic = $arrDailiesBasic[4];
			
				
				//echo $emp_AccruedDays[$empToProcPayBaicVal['empNo']]."\n";
				
				if($trnAmountPayBasicDailiesBasic > 0){
					if($Trns){
						$Trns = $this->writeToTblEarnings('E2',$empToProcPayBaicVal['empNo'],$trnCodePayBasicDailiesBasic,$trnAmountPayBasicDailiesBasic);
					}
				}
				
				if($trnAmountPayLegDailiesBasic > 0){
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
		
		$this->getEmpForAdj();
		foreach ((array)$this->summarizeCorrection() as $arrSumCorrVal){//foreach timesheet	
			if($arrSumCorrVal['sumAmtAbsnt']){
				if((float)$arrSumCorrVal['sumAmtAbsnt'] != 0 || in_array($arrSumCorrVal['empNo'],$this->arrEmpForAdj)){
						//echo $arrSumCorrVal['empNo']."   ". ($arrSumCorrVal['sumAmtAbsnt']*-1) . ">". $empBasicPay[$arrSumCorrVal['empNo']]."\n";
						if(($arrSumCorrVal['sumAmtAbsnt']*-1)>$empBasicPay[$arrSumCorrVal['empNo']]) {
							$trnAmountAbsent = ($this->getProRateAbsent($arrSumCorrVal['empNo'],$empBasicPay[$arrSumCorrVal['empNo']]))*-1;
						} else {
							if (in_array($arrSumCorrVal['empNo'],$this->arrEmpForAdj)) {
								$trnAmountAbsent = ($this->getProRateAbsent($arrSumCorrVal['empNo'],$empBasicPay[$arrSumCorrVal['empNo']]))*-1;
							} else {
								$trnAmountAbsent = $arrSumCorrVal['sumAmtAbsnt'];
							}
							
						}
						
						$computedAbsentAmount = $empBasicPay[$arrSumCorrVal['empNo']] - ($trnAmountAbsent*-1);
						
						if(($computedAbsentAmount<1)&&($computedAbsentAmount!=0))
						{
							$trnAmountAbsent = $empBasicPay[$arrSumCorrVal['empNo']]*-1;
						} 
						
						$empBasicPay[$arrSumCorrVal['empNo']] = $empBasicPay[$arrSumCorrVal['empNo']] + $trnAmountAbsent;
						if($Trns){
							$Trns = $this->writeToTblEarnings('E1',$arrSumCorrVal['empNo'],'0113',$trnAmountAbsent);
						}
				}
			}
			
			if ($empBasicPay[$arrSumCorrVal['empNo']]>0)	{
				if($arrSumCorrVal['sumAmtTardy']){
					if((float)$arrSumCorrVal['sumAmtTardy'] != 0){
						if(($arrSumCorrVal['sumAmtTardy']*-1)>$empBasicPay[$arrSumCorrVal['empNo']])
							$trnAmountTardy = $empBasicPay[$arrSumCorrVal['empNo']] * -1;
						else
							$trnAmountTardy = $arrSumCorrVal['sumAmtTardy'];
						
						$empBasicPay[$arrSumCorrVal['empNo']] = $empBasicPay[$arrSumCorrVal['empNo']] + $trnAmountTardy;
						if($Trns){
							$Trns = $this->writeToTblEarnings('E1',$arrSumCorrVal['empNo'],'0111',$trnAmountTardy);
							$emp_AmntTardy[$arrSumCorrVal['empNo']] = $trnAmountTardy;
						}		
					}	
				}		
			}
			
			if ($empBasicPay[$arrSumCorrVal['empNo']]>0) {
				if($arrSumCorrVal['sumAmtUt']){
					if((float)$arrSumCorrVal['sumAmtUt'] != 0){								
						if($Trns){
							$Trns = $this->writeToTblEarnings('E1',$arrSumCorrVal['empNo'],'0112',$arrSumCorrVal['sumAmtUt']);
							$emp_AmntUt[$arrSumCorrVal['empNo']] = $arrSumCorrVal['sumAmtUt'];
							
							$empBasicPay[$arrSumCorrVal['empNo']] = $empBasicPay[$arrSumCorrVal['empNo']] + $arrSumCorrVal['sumAmtUt'];
						}	
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
						
					if (in_array($arrPostAdjOthrsValNeg['empNo'],array('010002660','570000030','000500001','003600009','870000010')))
						$sumPostvErn = 1;
						

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
		foreach ((array)$this->getArrAllow($emp_AccruedDays,$emp_AmntTardy,$emp_AmntUt,$emp_LegalDays) as $arrAllwIndex => $arrAllwVal){//foeach for allowance
			
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
			$totalTaxDeducted = $empAddedTaxEarn = 0;
			
			
			/*non tax Earnings*/
			$nonTaxEarn = (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND (trn.trnTaxCd = 'N' or trn.trnTaxCd='' or trn.trnTaxCd is null) AND (sprtPS IS NULL or sprtPS ='' or sprtPS='N') ",'');
			
			$taxableGrossEarn = (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND (trn.trnTaxCd = 'Y') AND (sprtPS IS NULL or sprtPS ='' or sprtPS='N') ",$empForDedVal['empWageTag']);
			
			/*If the Branch.Minimum Wage!=0, Get 0100(Basic) of the Employee*/
			
			$emp_BRate 			=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_BASIC."'",'');
			$emp_Tard			=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_TARD."'",'');	
			$emp_Ut				=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_UT."'",'');	
			$emp_Absence		=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_ABS."'",'');	
			$emp_VLOP			=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_VLOP."'",'');	
			$emp_SLOP			=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".EARNINGS_SLOP."'",'');	
			$emp_AdjBasic		=  (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND ern.trnCode='".ADJ_BASIC."'",'');	
			$empMinWage_Basic 	=  (float) (($emp_BRate +  $emp_Tard + $emp_Ut + $emp_Absence + $emp_VLOP + $emp_SLOP) + $emp_AdjBasic);
			$empEcola 			=  $this->getEmpeCola($empForDedVal['empNo']);	
			$empEcola 			= (float)$empEcola['ecola'];

			$arrAllowSprtPS = $this->separatePaySlipAllow($empForDedVal['empNo'],"AND (sprtPS = 'Y') ","");			
			$sprtAllowPSTotAmnt=0;
			$Advances = 0;
			foreach ((array)$arrAllowSprtPS as $sprtAllowVal){
				$sprtAllowPSTotAmnt += (float)$sprtAllowVal['sprtPSAllwAmnt'];
				if (ALLW_ADVANCES == $sprtAllowVal['trnCode'] || ADJ_ADVANCES==$sprtAllowVal['trnCode']) {
					$Advances+= (float)$sprtAllowVal['sprtPSAllwAmnt'];
				}
				
			}
			$amntLimitDed = (float)round($sprtAllowPSTotAmnt,2);

			//computation of 13th Month
			$emp13thMonthNonTax = 0;
			$emp13thMonthTax = 0;
			$emp13thMonthAllow = 0;
			
			$arrAdj13thMonth = $this->separatePaySlipAllow($empForDedVal['empNo'],"AND (trn.trnCode = '0807') ","");			
			foreach ((array)$arrAdj13thMonth as $adj13thVal){
				$emp13thMonthNonTax += (float)$adj13thVal['sprtPSAllwAmnt'];
				
			}			

			$arr13thmonth = $this->compute13thmonth($empForDedVal['empNo'],(float)$empMinWage_Basic,(float)$Advances);
				if ((float)$arr13thmonth['regular'] != 0) {
					$grossEarnings += (float)$arr13thmonth['regular'];
					if ((float)$arr13thmonth['regular'] <= 30000) {
						if($Trns){
							$emp13thMonthNonTax = $arr13thmonth['regular'];
							$Trns = $this->writeToTblEarnings('E1',$empForDedVal['empNo'],'1000',$arr13thmonth['regular']);			
						}
					} else {
						if($Trns){
							$Trns = $this->writeToTblEarnings('E1',$empForDedVal['empNo'],'1000',30000);			
						}
						$emp13thMonthNonTax =  30000;
						$excessAmt = (float)$arr13thmonth['regular'] - 30000;
						$taxableGrossEarn += $excessAmt;
						$emp13thMonthTax = $excessAmt;
						if($Trns){
							$Trns = $this->writeToTblEarnings('E1',$empForDedVal['empNo'],'1010',$excessAmt);			
						}
					}	
					
				}
				if ((float)$arr13thmonth['allow'] != 0) {
					if($Trns){
						$emp13thMonthAllow += $arr13thmonth['allow'];
						$sprtAllowPSTotAmnt += $arr13thmonth['allow'];
						$Trns = $this->writeToTblEarningsAllow('E1',$empForDedVal['empNo'],'1100',$arr13thmonth['allow'],'Y');			
					}	
				}
			$grossEarnings = (float)$this->computeEmpGrossEarnings($empForDedVal['empNo'],"AND (sprtPS IS NULL or sprtPS ='' or sprtPS='N')",'');

			$amntLimitDed += $grossEarnings + $emp13thMonthAllow;
			$amntLimitDed = round($amntLimitDed,2);
			$txbleEarningsPd = $taxableGrossEarn;
			
			//Get Data of Employee in tblGov_Tax_Added for Tax Spread in Group 1
			
			/*
			$arrGetAddTax = $this->getEmpAddTaxableIncome($empForDedVal['empNo']);
			if($arrGetAddTax["amountToDed"]>0)
			{
				$empAddedTaxEarn = (float) $arrGetAddTax["amountToDed"];
			}
			*/
			
			$sum_txbleEarningsPd_addTax = $txbleEarningsPd + $empAddedTaxEarn;
			
			/* 	Computation of Witholding tax
				Created By		:	Genarra Jo - Ann Arong
				Date Created	:	10272009 Tuesday
			*/
		
			//$withTax = $this->computeEmpWithTax($empForDedVal['empNo'],$txbleEarningsPd,$taxableGrossEarn,$empForDedVal["empTeu"],$empForDedVal["empMrate"],$sumGov);
			$resCashBond = $this->lastPayData($empForDedVal['empNo']);
			if ((float)$resCashBond['cashBond'] != 0) {
				$AmtCashBnd =$resCashBond['cashBond'] - ($resCashBond['cashBond']*2); 
				$totDedForPeriod = $AmtCashBnd; 
				$amntLimitDed -= $AmtCashBnd;
				if($Trns){
						$Trns = $this->writeToTblDeduction($empForDedVal['empNo'],'8001',$AmtCashBnd);				
				}
			}
			//Annual Computation
			
			$withTax = $this->computeWithTax($empForDedVal['empNo'],$sum_txbleEarningsPd_addTax,$empForDedVal["empTeu"],$empMinWage_Basic);
			
			if($withTax != 0){
				if($amntLimitDed >= $withTax){
					$amntLimitDed -= round($withTax,2);

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
			
			//for loans deductions
			$resultloans = $this->getdeductionlist($empForDedVal['empNo'],",lonRefNo","");

			foreach($resultloans as $rsloans) {

				if ((float)$amntLimitDed >= (float)$rsloans['sumamount']) {
					$amntLimitDed = (float)$amntLimitDed - (float)$rsloans['sumamount'];
					$totDedForPeriod +=(float)$rsloans['sumamount'];
					if($Trns){
							$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$rsloans['sumamount'],$rsloans['lonRefNo'],'Y');
					}
				} elseif ((float)$amntLimitDed < (float)$rsloans['sumamount'] && (float)$amntLimitDed>0) {
					$UnDeductedAmtLoans = (float)$rsloans['sumamount'] - (float)$amntLimitDed ;
						
					$totDedForPeriod +=$amntLimitDed;
					
					if($Trns){
						$Trns = $this->writetotblLastPayBal($rsloans['empNo'],$rsloans['trnCode'],$rsloans['lonRefNo'],$UnDeductedAmtLoans);
					}
					
					if($Trns){
							$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$amntLimitDed,$rsloans['lonRefNo'],'P');
					}
					$amntLimitDed = 0;						
				} else {
					if($Trns){
						$Trns = $this->writetotblLastPayBal($rsloans['empNo'],$rsloans['trnCode'],$rsloans['lonRefNo'],(float)$rsloans['sumamount'] );
					}
				}
			}
			$resultSumloans = $this->getdeductionlist($empForDedVal['empNo'],"","");			
			foreach($resultSumloans as $valSumLoans) {
				if($Trns){
					
					//echo "Employee Loans = ".$valSumLoans['sumamount']."\n";
					$trns = $this->writeToTblDeduction($valSumLoans['empNo'],$valSumLoans['trnCode'],(float)$valSumLoans['sumamount']);		
				}
			}
				
			//for other deductions
			$resultotherdeduction =$this->getotherdeductionlist($empForDedVal['empNo'],1);
			$totalTaxAdj = 0;
			$YearEndTaxAdj = 0;
			foreach ($resultotherdeduction as $rsotherdeductionlist) {
				if ((float)$amntLimitDed >= (float)$rsotherdeductionlist['trnAmount']) {
					$amntLimitDed=(float)$amntLimitDed-(float)$rsotherdeductionlist['trnAmount'];
					//Wtax Adjustment
					if ($rsotherdeductionlist['trnCode'] == '8024') {
						$totalTaxAdj = (float)$rsotherdeductionlist['trnAmount'];
					}
					if ($rsotherdeductionlist['trnCode'] == '8124') {
						$YearEndTaxAdj = (float)$rsotherdeductionlist['trnAmount'];
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
								if ($rsotherdeductionlist['trnCode'] == '8024') {
									$totalTaxAdj = (float)$rsotherdeductionlist['trnAmount'];
								}	
								if ($rsotherdeductionlist['trnCode'] == '8124') {
									$YearEndTaxAdj = (float)$rsotherdeductionlist['trnAmount'];
								}																		
								if($Trns){
										$Trns = $this->updateotherdeductions($rsotherdeductionlist['seqNo'],$rsotherdeductionlist['trnAmount'],'Y',1);
								}
							} elseif((float)$amntLimitDedForAdvances < (float)$rsotherdeductionlist['trnAmount'] && (float)$amntLimitDedForAdvances > 0) {
								if ($rsotherdeductionlist['trnCode'] == '8024') {
									$totalTaxAdj = (float)$amntLimitDed;
								}		
								if ($rsotherdeductionlist['trnCode'] == '8124') {
									$YearEndTaxAdj = (float)$amntLimitDed;
								}													
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
			$totDedForPeriod -=$totalTaxAdj;
			$totDedForPeriod -=$YearEndTaxAdj;


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
								
								$UnDeductedAmtLoans = (float)$rsloans['sumamount'] - (float)$amntLimitDedForAdvances ;
								if($Trns){
									$Trns = $this->writetotblLastPayBal($rsloans['empNo'],$rsloans['trnCode'],$rsloans['lonRefNo'],$UnDeductedAmtLoans);
								}
								$amntLimitDedForAdvances = 0;							
							}

						} elseif ($amntLimitDedForAdvances <= (float)$amntLimitDed && (float)$amntLimitDed>0) {

							if($Trns){
									$trns = $this->updateloansdtl($rsloans['empNo'],$rsloans['lonTypeCd'],$amntLimitDed,$rsloans['lonRefNo'],'P',0);
							}
							
							$UnDeductedAmtLoans = (float)$rsloans['sumamount'] - round($amntLimitDed,2) ;
							if($Trns){
								$Trns = $this->writetotblLastPayBal($rsloans['empNo'],$rsloans['trnCode'],$rsloans['lonRefNo'],$UnDeductedAmtLoans);
							}
							$amntLimitDed = 0;						
						} else {
				
							if($Trns){
								$Trns = $this->writetotblLastPayBal($rsloans['empNo'],$rsloans['trnCode'],$rsloans['lonRefNo'],(float)$rsloans['sumamount'] );
							}	
						}		
					} 
				} else {
				
					if($Trns){
						$Trns = $this->writetotblLastPayBal($rsloans['empNo'],$rsloans['trnCode'],$rsloans['lonRefNo'],(float)$rsloans['sumamount'] );
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
			$empInfo = $this->getEmpInfo($empForDedVal['empNo']);
			//echo "Total Ded = ".$totDedForPeriod."\n";
			//echo "Sum Gov = ".$sumGov."\n";
			
/*			$arrEmpNonTaxAllowTot = $this->getEmpAllowance('tblAllowanceBrkDwn',$empForDedVal['empNo'],"AND (tblPayTransType.trnTaxCd = 'N' or tblPayTransType.trnTaxCd='' or tblPayTransType.trnTaxCd is null) AND (tblAllowanceBrkDwn.sprtPS IS NULL or tblAllowanceBrkDwn.sprtPS ='' or tblAllowanceBrkDwn.sprtPS='N') ");
			
			foreach ($arrEmpNonTaxAllowTot as $empNonTaxAllowTotVal){
				$totEmpNonTaxAllow += (float)$empNonTaxAllowTotVal['allowAmt'];
			}*/
			
			$totEmpNonTaxAllow = $this->getEmpNonTaxAllow($empForDedVal['empNo']);
			
			//$netsalary = ($grossEarnings+$txbleEarningsPd)-($totDedForPeriod+$totalTaxDeducted);
			/*echo "Gross Earnings = ".$grossEarnings."\n";*/
			
			$totalTaxDeducted += $totalTaxAdj;
			//echo "{$empForDedVal['empNo']} $netsalary = ($grossEarnings)-($totDedForPeriod+$totalTaxDeducted+$YearEndTaxAdj);\n";
			$netsalary = ($grossEarnings)-($totDedForPeriod+$totalTaxDeducted+$YearEndTaxAdj);
			
			
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
															  yearEndTax,
															  empDivCode,
															  empDepCode,
															  empSecCode,
															  sprtAllow,
															  sprtAllowAdvance,
															  empBasic,
															  empMinWageTag,
															  empEcola,
															  emp13thMonthNonTax,
															  emp13thMonthTax,
															  emp13thAdvances,
															  empTeu,
															  empYtdTaxable,
															  empYtdTax,
															  empYtdGovDed)
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
															   '".sprintf("%01.2f",$YearEndTaxAdj)."',
															   '{$empInfo['empDiv']}',
															   '{$empInfo['empDepCode']}',
															   '{$empInfo['empSecCode']}',
															   '".sprintf("%01.2f",$sprtAllowPSTotAmnt)."',
															   '".sprintf("%01.2f",$Advances)."',
															   '".sprintf("%01.2f",$empMinWage_Basic)."',
															   '".$empForDedVal['empWageTag']."',
															   '".sprintf("%01.2f",$empEcola)."',
															   '".sprintf("%01.2f",$emp13thMonthNonTax)."',
															   '".sprintf("%01.2f",$emp13thMonthTax)."',
															   '".sprintf("%01.2f",$emp13thMonthAllow)."',
															   '".$empForDedVal["empTeu"]."',
															   '".sprintf("%01.2f",$txbleEarningsPd)."',
															   '".sprintf("%01.2f",$totalTaxDeducted)."',
															   '".sprintf("%01.2f",$totalGovDeducted)."')";	
															   
														   		
			if($Trns){
				$Trns = $this->execQry($qryToPayrollSum);
			}
				
			
			$qryToYtdData = "INSERT INTO tblYtdData(compCode,pdYear,empNo,YtdGross,YtdTaxable,YtdGovDed,YtdTax,YtdNonTaxAllow,Ytd13NBonus,Ytdtx13NBonus,YTd13NAdvance,sprtAdvance,payGrp,pdNumber,YtdBasic,sprtAllow)
							 VALUES('{$this->session['company_code']}',
							 		'{$this->get['pdYear']}',
							 		'{$empForDedVal['empNo']}',
							 		'".sprintf("%01.2f",$grossEarnings)."',
							 		'".sprintf("%01.2f",$sum_txbleEarningsPd_addTax)."',
							 		'".sprintf("%01.2f",$totalGovDeducted)."',
							 		'".sprintf("%01.2f",$totalTaxDeducted)."',
							 		'".sprintf("%01.2f",$totEmpNonTaxAllow)."',
							 		'".sprintf("%01.2f",$emp13thMonthNonTax)."',
							 		'".sprintf("%01.2f",$emp13thMonthTax)."',
									'".sprintf("%01.2f",$emp13thMonthAllow)."',
									'".sprintf("%01.2f",$Advances)."',
							 		'{$this->session['pay_group']}',
							 		'{$this->get['pdNum']}',
									'".sprintf("%01.2f",$empMinWage_Basic)."',
									'".sprintf("%01.2f",$sprtAllowPSTotAmnt)."')";
						
			if($Trns){
				$Trns = $this->execQry($qryToYtdData);
			}	
			
			unset($grossEarnings,$netPayEarnings,$amntLimitDed,$nonTaxEarn,$taxableGrossEarn,$sumGov,$withTax,$totDedForPeriod,$netsalary,$totalGovDeducted,$arrAllwVal,$totEmpNonTaxAllow,$totalTaxDeducted,$ArSeq,$totaloansandadjustment,$sprtAllowPSTotAmnt,$empMinWage_Basic,$emp_BRate,$emp_Tard,$emp_Ut,$emp_Absence,$emp_VLOP,$emp_SLOP,$emp_AdjBasic,$trnAmountAbsent,$empEcola,$emp13thMonthNonTax,$emp13thMonthTax,$sum_txbleEarningsPd_addTax);
			
		}//end of foreach fo dedcutions	
		
		
		
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
	
	function getEmpInfo($empNo) {
		$qryEmpInfo = "Select * from tblEmpMast where empNo='$empNo' and compCode = '{$_SESSION['company_code']}' 
						AND empNo IN (
									SELECT empNo FROM tblLastPayEmp
									WHERE compCode = '{$this->session['company_code']}' 
									AND pdYear = '{$this->get['pdYear']}'
									AND pdNumber = '{$this->get['pdNum']}'
									)";
		return $this->getSqlAssoc($this->execQry($qryEmpInfo));
	}
	
	function getEmpeCola($empNo) {
		$sqlEcola = "Select sum(trnAmountE) as ecola from tblEarnings 
					 WHERE compCode = '{$this->session['company_code']}'
						  AND pdYear = '{$this->get['pdYear']}'
						  AND pdNumber = '{$this->get['pdNum']}'
						  AND empNo = '{$empNo}'
						  AND trnCode IN (8106,8112,8113,8117) ";
		return  $this->getSqlAssoc($this->execQry($sqlEcola));
	}

	function getEmpNonTaxAllow($empNo) {
		$sqlNonTaxAllow = "Select sum(trnAmountE) as amount from tblEarnings where empNo='$empNo' and trnCode IN (Select allow.trnCode from tblAllowType allow inner join tblPayTransType trans on allow.trnCode = trans.trnCode where (sprtPS IN ('N','') or sprtPS is null) and (trnTaxCd IS NULL or trntaxCd IN ('N',''))) and trnCode not IN (8106,8112,8113,8117) and pdYear='{$this->get['pdYear']}' and pdNumber='{$this->get['pdNum']}'";	
		$resNonTaxAllow = $this->execQry($sqlNonTaxAllow);
		$resNonTaxAllow = $this->getSqlAssoc($resNonTaxAllow);
		return (float)$resNonTaxAllow['amount'];
	}
	
}



?>