<?
class extractTSObj extends commonObj {
	
	var $get;//method
	
	var $session;//session variables
	
	var $oneDay = 8;//8 hours is equal to one day
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	function checkPeriodTimeSheetTag(){
		
		$qryChckPdTSTag = "SELECT * FROM tblPayPeriod 
							WHERE compCode = '{$this->session['company_code']}'
							AND payGrp = '{$this->session['pay_group']}'
							AND payCat = '{$this->session['pay_category']}'
							AND pdYear = '{$this->get['pdYear']}'
							AND pdNumber = '{$this->get['pdNum']}'
							AND pdTsTag = 'Y'";
		
		$resChckPdTSTag = $this->execQry($qryChckPdTSTag);
		return $this->getSqlAssoc($resChckPdTSTag);
	
	}
	
	function getEmplistForTS(){
		if ($this->session['pay_category'] != 9) {
			$extractEmployees = $this->getEmployeeList($this->session['company_code'],
								"AND empPayGrp = '{$this->session['pay_group']}'
								 AND empPayCat = '{$this->session['pay_category']}'
								 AND empstat NOT IN('RS','IN','TR')");
		} else {	
			 $qryEmpList = "SELECT '9' AS empPayCat,tblEmpMast.empPayGrp,tblLastPayEmp.compCode,tblLastPayEmp.empNo,tblEmpMast.empLocCode, tblEmpMast.empRestDay, tblEmpMast.empDrate, tblEmpMast.empHrate 
							FROM  tblLastPayEmp INNER JOIN tblEmpMast ON tblLastPayEmp.compCode = tblEmpMast.compCode 
							AND tblLastPayEmp.empNo = tblEmpMast.empNo
							WHERE tblLastPayEmp.compCode='{$_SESSION['company_code']}'
							AND empPayGrp='{$_SESSION['pay_group']}'
							AND pdYear='{$this->get['pdYear']}'
							AND pdNumber='{$this->get['pdNum']}'";
			$extractEmployees = $this->getArrRes($this->execQry($qryEmpList));
		}	
		return $extractEmployees;
	}
	
	function getUserInfoTS($compCode,$empNo,$where){
		if ($this->session['pay_category'] != 9) {
			$qryGetUserInfo = "SELECT * FROM tblEmpMast 
							   WHERE compCode = '{$compCode}'
							   AND   empNo    = '".trim($empNo)."'
							   AND   empStat NOT IN('RS','IN','TR') ";
		} else {
			$qryGetUserInfo = "SELECT * FROM tblEmpMast 
							   WHERE compCode = '{$compCode}'
							   AND   empNo    = '".trim($empNo)."'
							   AND   empStat = 'RS' ";
		
		}
		
		$resGetUserInfo= $this->execQry($qryGetUserInfo);
		return $this->getSqlAssoc($resGetUserInfo);;
	}

	function writeToWGroupCat(){
		$array_single_emp = $this->getEmplistForTS();
		if($array_single_emp["empNo"]!="")
		{
			$extrctEmpVal = $this->getEmplistForTS();
			$qryWrteToWGrpCat = "INSERT INTO wEmpGrpCat
										(compCode,empPayGrp,empPayCat,EmpNo,
										 pdYear,pdNumber,empRestDay,empDrate,
										 empHrate,empLocCode)
									 VALUES
										('{$extrctEmpVal['compCode']}','{$extrctEmpVal['empPayGrp']}','{$extrctEmpVal['empPayCat']}','{$extrctEmpVal['empNo']}',
										 '{$this->get['pdYear']}','{$this->get['pdNum']}','{$extrctEmpVal['empRestDay']}','{$extrctEmpVal['empDrate']}',
										 '{$extrctEmpVal['empHrate']}','{$extrctEmpVal['empLocCode']}')";	
			$resWrteToWGrpCat = $this->execQry($qryWrteToWGrpCat);
		}
		else
		{
			foreach ((array)$this->getEmplistForTS() as $extrctEmpVal){
	
			$qryWrteToWGrpCat = "INSERT INTO wEmpGrpCat
									(compCode,empPayGrp,empPayCat,EmpNo,
									 pdYear,pdNumber,empRestDay,empDrate,
									 empHrate,empLocCode)
								 VALUES
									('{$extrctEmpVal['compCode']}','{$extrctEmpVal['empPayGrp']}','{$extrctEmpVal['empPayCat']}','{$extrctEmpVal['empNo']}',
									 '{$this->get['pdYear']}','{$this->get['pdNum']}','{$extrctEmpVal['empRestDay']}','{$extrctEmpVal['empDrate']}',
									 '{$extrctEmpVal['empHrate']}','{$extrctEmpVal['empLocCode']}')";	
			$resWrteToWGrpCat = $this->execQry($qryWrteToWGrpCat);
			}
		}
	}	
		
	function getTimeRecord(){
		
		$qryGetTimeRec = "SELECT tsP.compCode, tsP.empNo, tsP.tsDate, tsP.hrsAbsent, 
								 tsP.hrsTardy, tsP.hrsUt, tsP.hrsOtLe8, tsP.hrsOtGt8, 
								 tsP.hrsNdLe8, tsP.hrsNdGt8, tsP.tsRemarks, w.empPayGrp, 
								 w.empPayCat, w.pdYear, w.pdNumber, w.empRestDay, 
								 w.empDrate, w.empHrate, w.empLocCode
						 FROM    tblTsParadox tsP LEFT OUTER JOIN wEmpGrpCat w 
						 ON      tsP.compCode = w.compCode AND tsP.empNo = w.empNo
					     WHERE   (tsP.compCode = '{$this->session['company_code']}') 
					     AND     (w.empPayGrp = '{$this->session['pay_group']}') 
					     AND     (w.empPayCat = '{$this->session['pay_category']}') 
					     AND     (w.pdYear = '{$this->get['pdYear']}') 
					     AND     (w.pdNumber = '{$this->get['pdNum']}') 
					     AND     (tsP.tsDate BETWEEN '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}')";

		$resGetTimeRec = $this->execQry($qryGetTimeRec);
		return $this->getArrRes($resGetTimeRec);
	}
	
	function initializeTimeSheets(){
		
		foreach ((array)$this->getTimeRecord() as $tsVAl){
				
			$tmpHrsAbsent = (!empty($tsVAl['hrsAbsent'])) ? $tsVAl['hrsAbsent'] : 0;
			$tmpHrsTardy  = (!empty($tsVAl['hrsTardy'])) ? $tsVAl['hrsTardy'] : 0;
			$tmpHrsUt     = (!empty($tsVAl['hrsUt'])) ? $tsVAl['hrsUt'] : 0;
			$tmpHrsOtLe8  = (!empty($tsVAl['hrsOtLe8'])) ? $tsVAl['hrsOtLe8'] : 0;
			$tmpHrsOtGt8  = (!empty($tsVAl['hrsOtGt8'])) ? $tsVAl['hrsOtGt8'] : 0;
			$tmpHrsNdLe8  = (!empty($tsVAl['hrsNdLe8'])) ? $tsVAl['hrsNdLe8'] : 0;
			$tmpHrsNdGt8  = (!empty($tsVAl['hrsNdGt8'])) ? $tsVAl['hrsNdGt8'] : 0;
			
			$qryInitializeTS = "INSERT INTO tblTimeSheet
								(compCode,empNo,tsDate,hrsAbsent,
								 hrsTardy,hrsUt,hrsOtLe8,hrsOtGt8,
								 hrsNdLe8,hrsNdGt8,tsRemarks,empPayGrp,
								 empPayCat,dayType,amtAbsent,amtTardy,
								 amtUt,amtOtLe8,amtOtGt8,amtNdLe8,
								 amtNdGt8,trnOtLe8,trnOtGt8,trnNdLe8,
								 trnNdGt8,tsRem,tsStat)
								 VALUES
								 ('{$tsVAl['compCode']}','{$tsVAl['empNo']}','".$this->dateFormat($tsVAl['tsDate'])."','{$tmpHrsAbsent}',
								  '{$tmpHrsTardy}','{$tmpHrsUt}','{$tmpHrsOtLe8}','{$tmpHrsOtGt8}',
								  '{$tmpHrsNdLe8}','{$tmpHrsNdGt8}','{$tsVAl['tsRemarks']}','{$tsVAl['empPayGrp']}',
								  '{$tsVAl['empPayCat']}','0','0','0',
								  '0','0','0','0',
								  '0','0','0','0',
								  '0','','I')";
			
			$resInitializeTS = $this->execQry($qryInitializeTS);
		}
	}
	
	private function isHoliday($compCode,$tsDate,$empLocCode){
		
		$qrycheckDayType = "SELECT holidayDate,brnCode,dayType 
							FROM tblHolidayCalendar 
							WHERE compCode = '{$compCode}' 
							AND   holidayDate = '{$tsDate}' 
							AND  (brnCode = '0'
							OR   brnCode = '{$empLocCode}')
							and holidayStat='A'";
		$rescheckDayType = $this->execQry($qrycheckDayType);
		if($this->getRecCount($rescheckDayType) > 0){
			return $this->getSqlAssoc($rescheckDayType);
		}
		else{
			return 0;
		}
	}
	
/*	function isRestDay($date,$restDay){
		
		 $tmpDate = strtotime(date("m/d/Y",strtotime($date)));
		 $getWDay = getdate($tmpDate);

		 if($getWDay['wday'] == 0){
		     $tmpWDay = 7;
		 }else{
			 $tmpWDay = $getWDay['wday'];
		 }

		 if((int)$tmpWDay == (int)$restDay){
		     return true;
		 }	
		 else{
			 return false;
		 }
	}	*/
/*	function isRestDay($date,$empNO){
		
		$qryIsRestDay = "SELECT restDay FROM tblEmpRestDay 
						 WHERE compCode = '{$_SESSION['company_code']}'
						 AND empNo = '{$empNO}'
						 AND restDay = '".$this->dateFormat($date)."' 
						 AND empPayGrp = '{$_SESSION['pay_group']}'
						 AND<H2></H2> empPayCat = '{$_SESSION['pay_category']}'";
		$resIsRestDay = $this->execQry($qryIsRestDay);
		if($this->getRecCount($resIsRestDay) > 0){
			return true;
		}
		else {
			return false;
		}
	}*/
	function isRestDay($tsDate,$restDays){
		
		$iTmpTsDate = strtotime(date("m/d/Y",strtotime(trim($tsDate))));
		$arrTmpRestDays = explode(",",trim($restDays)); 
		
		$i=0;
		foreach ($arrTmpRestDays as $dTmpRestDaysVal){
			
			$iTmpRestDaysVal =  strtotime(date("m/d/Y",strtotime($dTmpRestDaysVal)));
			if($iTmpTsDate == $iTmpRestDaysVal){
			
				$i++;	
			}
		}
		
		if($i > 0){
			return true;
		}
		else{
			return false;
		}
	}	
	
	//compute hours absent
	private function computeAmntAbsent($hrsAbsent,$dRate,$hRte){
		
		
		$amtAbsent = 0;
		
		if(!empty($hrsAbsent) || (float)$hrsAbsent != 0){
			if((float)$hrsAbsent == (float)$this->oneDay){
				$amtAbsent = $dRate;
				return sprintf("%01.2f",$amtAbsent);
			}
			else{
				$amtAbsent = (float)$hrsAbsent*(float)$hRte;
				return sprintf("%01.2f",$amtAbsent);
			}
		}
		else{
			return 0;
		}
	}
	
	//compute amount tardiness
	private function computeAmntTardy($hrsTardy,$hRate){
		$amntTardy = 0;
		
		if(!empty($hrsTardy) || (float)$hrsTardy != 0){
			if((float)$hrsTardy > 0){
				$amntTardy = (float)$hrsTardy*(float)$hRate;
				return sprintf("%01.2f",$amntTardy);
			}
			else{
				return 0;
			}
		}
	}

	//compute amount unsertime
	private function computeAmntUnderTime($hrsUt,$hRate){
		$amntUt = 0;
	
		if(!empty($hrsUt) || (float)$hrsUt != 0){
			if((float)$hrsUt > 0){
				$amntUt = (float)$hrsUt*(float)$hRate;
				return sprintf("%01.2f",$amntUt);
			}
			else{
				return 0;
			}
		}
	}
	
	private function getOverTimePremium($dayType){
		$qryGetOTPrem = "SELECT * FROM tblOtPrem
						 WHERE dayType = '".trim($dayType)."'";
		$resGetOTPrem = $this->execQry($qryGetOTPrem);
		return $this->getSqlAssoc($resGetOTPrem);
	}
	
	private function computeOverTimeLe8($hrsOtLe8,$hRate,$otPrem){
		$amntOtle8 =0;
		$tmpOtPrem = (!empty($otPrem) || $otPrem != 0) ? $otPrem : 0;
		
		if(!empty($hrsOtLe8) || (float)$hrsOtLe8 != 0){
			if((float)$hrsOtLe8 > 0){
				$amntOtle8 = (float)$hrsOtLe8*(float)$hRate*(float)$tmpOtPrem;
				return sprintf("%01.2f",$amntOtle8);
			}
			else{
				return 0;
			}
		}
	}
	
	private function computeOvertimeGt8($hrsOtGt8,$hRate,$otPrem){
		$amntOtGt8 =0;
		$tmpOtPrem = (!empty($otPrem) || $otPrem != 0) ? $otPrem : 0;
		
		if(!empty($hrsOtGt8) || (float)$hrsOtGt8 != 0){
			if((float)$hrsOtGt8 > 0){
				$amntOtGt8 = (float)$hrsOtGt8*(float)$hRate*(float)$tmpOtPrem;
				return sprintf("%01.2f",$amntOtGt8);
			}
			else{
				return 0;
			}
		}		
	}
	
	private function computeNightDefirentialLe8($hrsNdLe8,$hRate,$otPrem){
		
		$amntNdLe8 = 0;
		$tmpOtPrem = (!empty($otPrem) || $otPrem != 0) ? $otPrem : 0;
		
		if(!empty($hrsNdLe8) || (float)$hrsNdLe8 != 0){
			if((float)$hrsNdLe8 > 0){
				$tmpOtPrem = (float)$hrsNdLe8*(float)$hRate*(float)$tmpOtPrem;
				return sprintf("%01.2f",$tmpOtPrem);
			}
			else{
				return 0;
			}			
		}
	}
	
	private function computeNightDiferentialGt8($hrsNdGt8,$hRate,$otPrem){
		
		$amntNdGt8 = 0;
		$tmpOtPrem = (!empty($otPrem) || $otPrem != 0) ? $otPrem : 0;
		
		if(!empty($hrsNdGt8) || (float)$hrsNdGt8 != 0){
			if((float)$hrsNdGt8 > 0){
				$amntNdGt8 = (float)$hrsNdGt8*(float)$hRate*(float)$tmpOtPrem;
				return sprintf("%01.2f",$amntNdGt8);
			}
			else{
				return 0;
			}			
		}
	}
	
	function mainProc(){
		
	    $qryGetTimeSheet = "SELECT     
		 							ts.compCode, ts.empNo, ts.tsDate, ts.hrsAbsent, ts.hrsTardy, 
			                        ts.hrsUt, ts.hrsOtLe8, ts.hrsOtGt8, ts.hrsNdLe8, ts.hrsNdGt8, 
              	 			        ts.tsRemarks, ts.tsStat, wegc.empPayGrp, wegc.empPayCat, wegc.pdYear, 
                      			    wegc.pdNumber, wegc.empRestDay, wegc.empDrate, wegc.empHrate, 
                      				wegc.empLocCode
							FROM    tblTimeSheet as ts LEFT JOIN wEmpGrpCat wegc 
							ON      ts.compCode = wegc.compCode AND ts.empNo = wegc.empNo
							WHERE   (ts.compCode    = '{$this->session['company_code']}')
							AND     (wegc.empPayGrp = '{$this->session['pay_group']}')
							AND     (wegc.empPayCat = '{$this->session['pay_category']}')
						    AND     (wegc.pdYear    = '{$this->get['pdYear']}') 
						    AND     (wegc.pdNumber  = '{$this->get['pdNum']}') 
							AND     (ts.tsDate BETWEEN '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}') ";
		
		$resGetTimeSheet = $this->execQry($qryGetTimeSheet);
		
		foreach ((array)$this->getArrRes($resGetTimeSheet) as $arrGetTsVal){
			
			//if transaction date is holiday
			$isHoliday = $this->isHoliday($arrGetTsVal['compCode'],$arrGetTsVal['tsDate'],$arrGetTsVal['empLocCode']);
			$empInfo =  $this->getUserInfoTS($arrGetTsVal['compCode'] ,$arrGetTsVal['empNo'],""); 
			
			if($isHoliday == 0)
			{
					if($this->isRestDay($arrGetTsVal['tsDate'],$arrGetTsVal['empRestDay']) == true){
						$dayType = '02';
					}
					else{
						$dayType = '01';
					}				
			}
			else{
			//day type
			
				if(trim($isHoliday['dayType']) == '03'){
					
					if($this->isRestDay($arrGetTsVal['tsDate'],$arrGetTsVal['empRestDay']) == true){
						$dayType = '05';
						
					}
					else{
						$dayType = '03';
					}
					
				}
				else{
					if($this->isRestDay($arrGetTsVal['tsDate'],$arrGetTsVal['empRestDay']) == true){
						$dayType = '06';
					}
					else{
						$dayType = '04';
					}				
				}
			}
			
			//Compute for the Daily Pay
			
			
			$amtAbsent     = ($empInfo["empPayType"]=='M'?$this->computeAmntAbsent($arrGetTsVal['hrsAbsent'],$arrGetTsVal['empDrate'],$arrGetTsVal['empHrate']):"0");	
			//echo $arrGetTsVal['empNo']."=".$empInfo["empPayType"]."=".$arrGetTsVal['hrsAbsent'].",".$amtAbsent.",".$arrGetTsVal['empDrate'].",".$arrGetTsVal['empHrate']."\n";
			
			$amntTardy     = $this->computeAmntTardy($arrGetTsVal['hrsTardy'],$arrGetTsVal['empHrate']);
			$amntUnderTime = $this->computeAmntUnderTime($arrGetTsVal['hrsUt'],$arrGetTsVal['empHrate']);
			$arrOtPrem     = $this->getOverTimePremium($dayType);
			$amntOtLe8     = $this->computeOverTimeLe8($arrGetTsVal['hrsOtLe8'],$arrGetTsVal['empHrate'],$arrOtPrem['otPrem8']);
			$amntOtGt8     = $this->computeOvertimeGt8($arrGetTsVal['hrsOtGt8'],$arrGetTsVal['empHrate'],$arrOtPrem['otPremOvr8']);
			$amntNdLe8     = $this->computeNightDefirentialLe8($arrGetTsVal['hrsNdLe8'],$arrGetTsVal['empHrate'],$arrOtPrem['ndPrem8']);
			$amntndGt8     = $this->computeNightDiferentialGt8($arrGetTsVal['hrsNdGt8'],$arrGetTsVal['empHrate'],$arrOtPrem['ndPremOvr8']);
			
			$qryUpdateAmntTimeSheet = "UPDATE tblTimeSheet SET
										dayType   = '{$dayType}',
										amtAbsent = '{$amtAbsent}',
										amtTardy  = '{$amntTardy}',
										amtUt     = '{$amntUnderTime}',
										amtOtLe8  = '{$amntOtLe8}',
										amtOtGt8  = '{$amntOtGt8}',
										amtNdLe8  = '{$amntNdLe8}',
										amtNdGt8  = '{$amntndGt8}',
										trnOtLe8  = '{$arrOtPrem['trnOtPrem8']}',
										trnOtGt8  = '{$arrOtPrem['trnOtPremOvr8']}',
										trnNdLe8  = '{$arrOtPrem['trnNdPrem8']}',
										trnNdGt8  = '{$arrOtPrem['trnNdPremOvr8']}',
										tsStat = 'A'
										WHERE compCode = '{$arrGetTsVal['compCode']}'
										AND empNo = '{$arrGetTsVal['empNo']}'
										AND tsDate = '".$this->dateFormat($arrGetTsVal['tsDate'])."' ";
			
			
			$resUpdateAmntTimeSheet = $this->execQry($qryUpdateAmntTimeSheet);
			unset($amtAbsent,$amntTardy,$amntUnderTime,$arrOtPrem,$amntOtLe8,$amntOtGt8,$amntNdLe8,$amntndGt8,$dayType,$isHoliday);	
		}
	}
	
	function updateTimeSheetTag(){
		$qryUpdtTsTag = "UPDATE tblPayPeriod 
						 SET pdTsTag = 'Y'
						 WHERE compCode = '{$this->session['company_code']}'
						 AND payGrp     = '{$this->session['pay_group']}'
						 AND payCat     = '{$this->session['pay_category']}'
						 AND pdYear     = '{$this->get['pdYear']}'
						 AND pdNumber   = '{$this->get['pdNum']}'";
		$resUpdtTsTag = $this->execQry($qryUpdtTsTag);
	}
	
	function deletewGroupCat()
	{
		$qrydeletewGroupCat = "DELETE FROM wEmpGrpCat
						 	WHERE compCode='".$this->session['company_code']."' AND empPayGrp = '{$this->session['pay_group']}'
							 AND empPayCat = '{$this->session['pay_category']}'
							 AND pdYear     = '{$this->get['pdYear']}'
							 AND pdNumber   = '{$this->get['pdNum']}'";
		$resdeletewGroupCat = $this->execQry($qrydeletewGroupCat);
	}
	
	function deleTimsheetToReproc(){
		$qryDeleTsprdx = "DELETE FROM tblTimeSheet
						  WHERE compCode = '{$this->session['company_code']}'
						  AND tsDate BETWEEN '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}'
						  AND empPayGrp = '{$this->session['pay_group']}'
						  AND empPayCat = '{$this->session['pay_category']}'";
		return $this->execQry($qryDeleTsprdx);
	}
}


?>