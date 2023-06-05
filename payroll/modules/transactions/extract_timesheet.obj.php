<?
class extractTSObj extends commonObj {
	
	var $get;//method
	
	var $session;//session variables
	
	var $oneDay = 8;//8 hours is equal to one day
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	function checkPeriodTimeSheetTag($pdYear, $pdNum){
		
		$qryChckPdTSTag = "SELECT * FROM tblPayPeriod 
							WHERE compCode = '{$this->session['company_code']}'
							AND payGrp = '{$this->session['pay_group']}'
							AND payCat = '{$this->session['pay_category']}'
							AND pdYear = '".$pdYear."'
							AND pdNumber = '".$pdNum."'
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
										 '{$extrctEmpVal['empHrate']}','{$extrctEmpVal['empBrnCode']}')";	
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
									 '{$extrctEmpVal['empHrate']}','{$extrctEmpVal['empBrnCode']}')";	
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
		
		$arrGetEmpGrpCatInfo = $this->getwEmpGrpCatInfo();
		
		foreach ((array)$this->getTimeRecord() as $tsVAl){
			
			$Emp_RestDay = "";
			
			foreach($arrGetEmpGrpCatInfo as $arrGetEmpGrpCatInfo_val)
			{
				if($arrGetEmpGrpCatInfo_val["empNo"] == $tsVAl['empNo'])
				{
					$Emp_RestDay = $arrGetEmpGrpCatInfo_val["empRestDay"];
				}
			}	
			
			if($this->isRestDay($this->dateFormat($tsVAl['tsDate']),$Emp_RestDay) == true)
			{
				$tmpHrsAbsent =  0;
				$tmpHrsTardy  = 0;
				$tmpHrsUt = 0;
			}
			else
			{
				$tmpHrsAbsent = (!empty($tsVAl['hrsAbsent'])) ? $tsVAl['hrsAbsent'] : 0;
				$tmpHrsTardy  = (!empty($tsVAl['hrsTardy'])) ? $tsVAl['hrsTardy'] : 0;
				$tmpHrsUt     = (!empty($tsVAl['hrsUt'])) ? $tsVAl['hrsUt'] : 0;
			}
			
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
						  AND empPayCat = '{$this->session['pay_category']}' and empNo IN (Select empno from tblEmpMast emp inner join tblBranch br on empBrnCode=brnCode where tnaTag is null)";
		return $this->execQry($qryDeleTsprdx);
	}
	
	function getwEmpGrpCatInfo()
	{
		$qryEmpGrpCat_Info = "Select empNo, empRestDay from wEmpGrpCat where 
								compCode='".$_SESSION["company_code"]."' and 
								empPayGrp='".$_SESSION["pay_group"]."' and 
								empPayCat='".$_SESSION["pay_category"]."' and 
								pdYear='".$this->get['pdYear']."' and pdNumber='".$this->get['pdNum']."'";
								
		$resEmpGrpCat_Info = $this->execQry($qryEmpGrpCat_Info);
		$resEmpGrpCat_Info = $this->getArrRes($resEmpGrpCat_Info);
		return $resEmpGrpCat_Info;
	}
	
	function uploadTS() {
		$Trns = $this->beginTran();
		$compInfo = $this->getCompany($_SESSION['company_code']);
		$pdYear = $this->get['pdYear'];
		$pdNumber = $this->get['pdNum'];
		$earnTran_refNo_BAdj = "BADJ-".$compInfo['compShort'].'-'.$this->get['pdYear'].'-'.$this->get['pdNum'].'-'.$_SESSION["pay_group"];
		$earnTran_refNo_OTAdj = "OTADJ-".$compInfo['compShort'].'-'.$this->get['pdYear'].'-'.$this->get['pdNum'].'-'.$_SESSION["pay_group"];
		$earnTran_refNo_AllowAdj = "ALLOWADJ-".$compInfo['compShort'].'-'.$this->get['pdYear'].'-'.$this->get['pdNum'].'-'.$_SESSION["pay_group"];
		
		$qryDelTsParadox_temp = "Delete from tblTsParadox_temp";
		
		if(($Trns)) {
			$Trns = $this->execQry($qryDelTsParadox_temp);
		}
		$qryDelTsParadox = "Delete from tblTsParadox 
							where tsDate between '".$this->get['dtFrm']."' AND '".$this->get['dtTo']."'
							and empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."'  and empPayGrp='".$_SESSION["pay_group"]."')
							";

		if(($Trns)) {
			
			$Trns = $this->execQry($qryDelTsParadox);
		}



		$qryEarnTranheader.= "Delete from tblEarnTranHeader where compCode='".$_SESSION["company_code"]."' 
								and refNo in ('".$earnTran_refNo_BAdj."','".$earnTran_refNo_OTAdj."')
								and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and earnStat='A';";
		
		$qryEarnTranheader.= "Delete from tblEarnTranDtl where compCode='".$_SESSION["company_code"]."' 
								and refNo in ('".$earnTran_refNo_BAdj."','".$earnTran_refNo_OTAdj."','".$earnTran_refNo_AllowAdj."')
								and payGrp='".$_SESSION["pay_group"]."'  and earnStat='A';";
		
		$qryEarnTranheader.= "Delete from tblEarnTranHeader where compCode='".$_SESSION["company_code"]."' 
								and refNo like ('".$earnTran_refNo_AllowAdj."%')
								and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and earnStat='A';";
		
		$qryEarnTranheader.= "Delete from tblEarnTranDtl where compCode='".$_SESSION["company_code"]."' 
								and refNo like ('".$earnTran_refNo_AllowAdj."%')
								and payGrp='".$_SESSION["pay_group"]."'  and earnStat='A';";

		if(($Trns)) {
			$Trns = $this->execQry($qryEarnTranheader);
		}		
		
		$sqlTS = "SELECT empNo, [DATE] AS tsDate, SUM(DAYS_ABSENT) AS hrsAbsnt, SUM(TARDY_HRS) AS hrsTrdy, SUM(UT_HRS) AS hrsUt, SUM(OT_HOURS) AS hrsOtLe8, 
                      SUM(OT_EXCESS_HOURS) AS hrsOtGt8, SUM(OT_ND) AS hrsNd, SUM(INCOME_AMOUNT) AS othAdj FROM payroll_company..t_templ where [DATE] BETWEEN '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}'  and empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."'  and empPayGrp='".$_SESSION["pay_group"]."') GROUP BY empNo, [DATE]";
		$resTS = $this->execQry($sqlTS);
		$resTS = $this->getArrRes($resTS);
		$arrEmp = array();
		$arrEmp_NotWithinCO = array();
		$test_cnt = 1;
		foreach($resTS as $val) {
			if($tmp_emp!=$val['empNo'])
				$ctr = 1;
			
			$tmp_emp = $val['empNo'];
			
			$tmpOtLe8Hrs = (float)$val['hrsOtLe8'];
			$tmpOtGt8hrs = (float)$val['hrsOtGt8'];
			$tmpNdLe8Hrs = (float)$val['hrsNd'];
			$tmpOthAdj 	 = (float)$val['othAdj'];
			
			$tmpNdGt8Hrs = 0;
			if($tmpOtLe8Hrs > 8)
			{
				$excessOtLe8Hrs = $tmpOtLe8Hrs-8;
				$tmpOtLe8Hrs = $tmpOtLe8Hrs-$excessOtLe8Hrs;
				$tmpOtGt8hrs = $tmpOtGt8hrs+$excessOtLe8Hrs;
			}
			
			if($tmpNdLe8Hrs > 8)
			{
				$excessNdLe8Hrs = $tmpNdLe8Hrs-8;
				$tmpNdLe8Hrs = $tmpNdLe8Hrs-$excessNdLe8Hrs;
				$tmpNdGt8Hrs = $tmpNdGt8Hrs+$excessNdLe8Hrs;
			}
			$qryToNewTsParadaox.= " INSERT INTO tblTsParadox_temp (compCode,empNo,tsDate,hrsAbsent,hrsTardy,hrsUt,hrsOtLe8,hrsOtGt8,hrsNdLe8,hrsNdGt8,tsRemarks)
									VALUES
									('{$_SESSION['company_code']}',
									 '{$val['empNo']}',
									 '{$val['tsDate']}',
									 '".sprintf("%01.2f",(float)$val['hrsAbsnt'])."',
									 '".sprintf("%01.2f",(float)$val['hrsTrdy'])."',
									 '".sprintf("%01.2f",(float)$val['hrsUt'])."',
									 '".sprintf("%01.2f",$tmpOtLe8Hrs)."','".sprintf("%01.2f",$tmpOtGt8hrs)."',
									 '".sprintf("%01.2f",$tmpNdLe8Hrs)."',
									 '".sprintf("%01.2f",$tmpNdGt8Hrs)."',
									 '');\n";
			
			unset($tmpOtHrs,$excessOtHrs,$tmpOtGt8hrs,$tmpNdLe8Hrs,$tmpNdGt8Hrs,$tmpOthAdj,$empCntRD);
			$i++;
			$test_cnt++;
			$testEmp = $val['empNo'];						
		}
		if(($Trns)&&($qryToNewTsParadaox!=""))
			$Trns = $this->execQry($qryToNewTsParadaox);		
			
		unset($qryToNewTsParadaox);
		
		

		$sqlTSAdj = "SELECT empNo, [DATE] AS tsDate, SUM(DAYS_ABSENT) AS hrsAbsnt, SUM(TARDY_HRS) AS hrsTrdy, SUM(UT_HRS) AS hrsUt, SUM(OT_HOURS) AS hrsOtLe8, 
                      SUM(OT_EXCESS_HOURS) AS hrsOtGt8, SUM(OT_ND) AS hrsNd, SUM(INCOME_AMOUNT) AS othAdj, compCode, INCOME_TYPE AS incType, 
                      ALLOW_TYPE AS allowType, ALLOWANCE_AMOUNT as allowAmount FROM payroll_company..t_templ where [DATE] BETWEEN '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}'  and empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."'  and empPayGrp='".$_SESSION["pay_group"]."') GROUP BY empNo, [DATE],[INCOME_TYPE],[ALLOW_TYPE],[ALLOWANCE_AMOUNT],compCode";

		$resTSAdj = $this->execQry($sqlTSAdj);
		$resTSAdj = $this->getArrRes($resTSAdj);
		$cntrlNo = 1;
		$cntrlNo_Allow = 1;
		$cntrlNo_OtAdj = 1;
		$cntrlNo_AllowAdj =1;
		$arrRef = array();
		foreach($resTSAdj as $valAdj) {
			$tmpOthAdj 	 = (float)$valAdj['othAdj'];
			$tmpAllowAdj 	 = (float)$valAdj['allowAmount'];
			
			if(((($tmpOthAdj!=0) || ($tmpOthAdj!="")) && (($valAdj['incType']=='B-ADJ') || ($valAdj['incType']=='BASIC'))))
			{
				$earnTranDtl_BAdj.=  "Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount,payGrp,payCat,earnStat,trnTaxCd)
								  values('".$_SESSION["company_code"]."','".$earnTran_refNo_BAdj."','".$valAdj['empNo']."','".$cntrlNo."','".ADJ_BASIC."','".$tmpOthAdj ."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','A','".ADJ_BASIC_TAXCD."');";
				$cntrlNo++;
			}
			
			if(((($tmpOthAdj!=0) || ($tmpOthAdj!="")) && ($valAdj['incType']=='OT-ADJ')))
			{
				$earnTranDtl_OTAdj.=  "Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount,payGrp,payCat,earnStat,trnTaxCd)
								  values('".$_SESSION["company_code"]."','".$earnTran_refNo_OTAdj."','".$valAdj['empNo']."','".$cntrlNo_OtAdj."','".ADJ_OT."','".$tmpOthAdj ."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','A','".ADJ_OT_TAXCD."');";
				$cntrlNo_OtAdj++;
			}
			
			if(($tmpAllowAdj!=0) || ($tmpAllowAdj!="") )
			{
				$arr_AllowType = $this->getEquivAllwCode($valAdj['allowType']);
				$allow_refNo = $earnTran_refNo_AllowAdj."-".$arr_AllowType["trnCode"];
				
				$earnTranDtl_AllowAdj.=  "Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount,payGrp,payCat,earnStat,trnTaxCd)
								  values('".$_SESSION["company_code"]."','".$allow_refNo."','".$valAdj['empNo']."','".$cntrlNo_AllowAdj."','".$arr_AllowType["trnCode"]."','".$tmpAllowAdj ."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','A','N');";
				$cntrlNo_AllowAdj++;
				
				if($earnTranAllow_refNo!=$allow_refNo)
				{
					if (!in_array($earnTran_refNo_AllowAdj."-".$arr_AllowType["trnCode"],$arrRef)) {
					$ernTranHeader_AllowAdj.= "Insert into tblEarnTranHeader(compCode,refNo, trnCode, earnRem,earnStat, pdYear, pdNumber)
								 values('".$_SESSION["company_code"]."','".$earnTran_refNo_AllowAdj."-".$arr_AllowType["trnCode"]."','".$arr_AllowType["trnCode"]."','Allow-Adj on Hyper TS','A','".$pdYear."','".$pdNumber."');";
								 $arrRef[] = $earnTran_refNo_AllowAdj."-".$arr_AllowType["trnCode"];
					}
				}
					
					$earnTranAllow_refNo = $earnTran_refNo_AllowAdj."-".$arr_AllowType["trnCode"];
			}			
		}
		
		if($earnTranDtl_BAdj!="")
			$ernTranHeader_BAdj.= "Insert into tblEarnTranHeader(compCode,refNo, trnCode, earnRem,earnStat, pdYear, pdNumber)
							 values('".$_SESSION["company_code"]."','".$earnTran_refNo_BAdj."','".ADJ_BASIC."','B-Adj on Hyper TS','A','".$pdYear."','".$pdNumber."');";
		
		if($earnTranDtl_OTAdj!="")
			$ernTranHeader_OTAdj.= "Insert into tblEarnTranHeader(compCode,refNo, trnCode, earnRem,earnStat, pdYear, pdNumber)
							 values('".$_SESSION["company_code"]."','".$earnTran_refNo_OTAdj."','".ADJ_OT."','OT-Adj on Hyper TS','A','".$pdYear."','".$pdNumber."');";
		
		/*if($earnTranDtl_AllowAdj!="")
			$ernTranHeader_AllowAdj.= "Insert into tblEarnTranHeader(compCode,refNo, trnCode, earnRem,earnStat, pdYear, pdNumber)
							 values('".$_SESSION["company_code"]."','".$earnTran_refNo_AllowAdj."','".ADJ_OT."','OT-Adj on Hyper TS','A','".$pdYear."','".$pdNumber."');";
		*/	

		$execQueries.= 	$ernTranHeader_BAdj.$ernTranHeader_OTAdj.$ernTranHeader_AllowAdj.$earnTranDtl_BAdj.$earnTranDtl_OTAdj.$earnTranDtl_AllowAdj;


		//Transactions with no B-Adj Adjustment
		$txtfile_title = "List of Transactions Not Within the Cut Off\r\n";


		$header = strtoupper("EMP. NO.").$this->Space(11).strtoupper("TRAN. DATE").$this->Space(11).strtoupper("HRS. ABSENT").$this->Space(11).strtoupper("HRS. TARDY").$this->Space(11).strtoupper("HRS. UT").$this->Space(11).strtoupper("HRS. OTLE8").$this->Space(11).strtoupper("HRS. OTGT8").$this->Space(11).strtoupper("HRS. ND").$this->Space(11).strtoupper("INCOME TYPE").$this->Space(11).strtoupper("OTH. ADJ").$this->Space(11);
/*		$qryGetParadoxData_NWCutOff_noBadj = "SELECT empNo, [DATE] AS tsDate, SUM(DAYS_ABSENT) AS hrsAbsnt, SUM(TARDY_HRS) AS hrsTrdy, SUM(UT_HRS) AS hrsUt, SUM(OT_HOURS) AS hrsOtLe8, 
                      SUM(OT_EXCESS_HOURS) AS hrsOtGt8, SUM(OT_ND) AS hrsNd, SUM(INCOME_AMOUNT) AS othAdj, compCode, INCOME_TYPE AS incType, 
                      ALLOW_TYPE AS allowType, ALLOWANCE_AMOUNT as allowAmount FROM payroll_company..t_templ where [DATE] NOT BETWEEN '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}' and compCode = '{$_SESSION['company_code']}' and empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."'  and empPayGrp='".$_SESSION["pay_group"]."') GROUP BY empNo, [DATE],[INCOME_TYPE],[ALLOW_TYPE],[ALLOWANCE_AMOUNT], compCode";
		
		$resErrTS = $this->execQry($qryGetParadoxData_NWCutOff_noBadj);
		$resErrTS = $this->getArrRes($resErrTS);
		$txtFile_OutPut = "BRANCH : ".substr($file,0,strlen($file)-23)."\r\n";
		$error_log = 0;
		foreach($resErrTS as $valErrTs)
		{
			$error_log = 1;
			$txtFile_OutPut.=trim(substr($valErrTs["empNo"], 0, 15)).$this->Space(19-strlen($valErrTs["empNo"])).
			($valErrTs["tsDate"]!=""?trim(substr(date("m-d-Y", strtotime($valErrTs["tsDate"])), 0, 15)):$this->Space(21)).$this->Space(21-strlen(date("m-d-Y", strtotime($valErrTs["tsDate"])))).($valErrTs["hrsAbsnt"]!=""?trim(substr($valErrTs["hrsAbsnt"], 0, 15)).$this->Space(22-strlen($valErrTs["hrsAbsnt"])):$this->Space(22)).($valErrTs["hrsTrdy"]!=""?trim(substr($valErrTs["hrsTrdy"], 0, 15)).$this->Space(21-strlen($valErrTs["hrsTrdy"])):$this->Space(21)).($valErrTs["hrsUt"]!=""?trim(substr($valErrTs["hrsUt"], 0, 15)).$this->Space(18-strlen($valErrTs["hrsUt"])):$this->Space(18)).($valErrTs["hrsOtLe8"]!=""?trim(substr($valErrTs["hrsOtLe8"], 0, 15)).$this->Space(21-strlen($valErrTs["hrsOtLe8"])):$this->Space(21)).($valErrTs["hrsOtGt8"]!=""?trim(substr($valErrTs["hrsOtGt8"], 0, 15)).$this->Space(21-strlen($valErrTs["hrsOtGt8"])):$this->Space(21)).($valErrTs["hrsNd"]!=""?trim(substr($valErrTs["hrsNd"], 0, 15)).$this->Space(18-strlen($valErrTs["hrsNd"])):$this->Space(18)).($valErrTs["incType"]!=""?trim(substr($valErrTs["incType"], 0, 15)).$this->Space(22-strlen($valErrTs["incType"])):$this->Space(22)).($valErrTs["othAdj"]!=""?trim(substr($valErrTs["othAdj"], 0, 15)).$this->Space(19-strlen($valErrTs["othAdj"])):$this->Space(19)).
			"\r\n";
		}
	
		if($error_log==1)
		{
			$output_err.=$txtfile_title."\r\n".$header."\r\n".$txtFile_OutPut;
			
			if(file_exists($_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '/'.session_id().'-ERROR-'.$_SESSION["pay_group"].'-'.$pdNumber.'-'.$pdYear.'.txt'))
			{
				//unlink($_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '/'.session_id().'-ERROR.txt');
			}
	
			//$this->WriteFile(session_id().'-ERROR-'.$_SESSION["pay_group"].'-'.$pdNumber.'-'.$pdYear.'.txt', $_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '', $output_err);
			$noError = 1;
		}
*/
		$sqlTransferTS = "
                        INSERT INTO tblTsParadox (
                            compCode,
                            empNo,
                            tsDate,
                            hrsAbsent,
                            hrsTardy,
                            hrsUt,
                            hrsOtLe8,
                            hrsOtGt8,
                            hrsNdLe8,
                            hrsNdGt8,
                            tsRemarks
                            )
                        SELECT
                            compCode,
                            empNo,
                            tsDate,
                            hrsAbsent,
                            hrsTardy,
                            hrsUt,
                            hrsOtLe8,
                            hrsOtGt8,
                            hrsNdLe8,
                            hrsNdGt8,
                            tsRemarks
                        FROM tblTsParadox_temp
                        GROUP BY compCode, empNo, tsDate, hrsAbsent, hrsTardy, hrsUt, hrsOtLe8, hrsOtGt8, hrsNdLe8, hrsNdGt8, tsRemarks";
		if ($Trns)
			$Trns = $this->execQry($sqlTransferTS);
					
		if(($Trns)&&($qryEarnTranheader!=""))
				$Trns = $this->execQry($qryEarnTranheader);
				
		if(($Trns)&&($execQueries!=""))
			$Trns = $this->execQry($execQueries);
		
		$qryUpdateEarnTran = "Update tblEarnTranDtl set payCat='".EXEC."' where empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayCat='".EXEC."' and empPayGrp='".$_SESSION["pay_group"]."'); ";
		$qryUpdateEarnTran.= "Update tblEarnTranDtl set payCat='".CONFI."' where empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayCat='".CONFI."' and empPayGrp='".$_SESSION["pay_group"]."'); ";
		$qryUpdateEarnTran.= "Update tblEarnTranDtl set payCat='".NONCONFI."' where empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayCat='".NONCONFI."' and empPayGrp='".$_SESSION["pay_group"]."'); ";
		
		if(($Trns)&&($qryUpdateEarnTran!=""))
			$Trns = $this->execQry($qryUpdateEarnTran);
		


		if(!$Trns){
			$Trns = $this->rollbackTran();
			if($error_log!=0)
				echo "31";
			else
				echo "3";
		}
		else{
			$Trns = $this->commitTran();
			if($error_log!=0)
				echo "41";
			else
				echo "4";
		}	
		
	}
	
	function getEquivAllwCode($oldAllowCode)
	{
		
		$qry = "SELECT * FROM tblAllowTypeConvTbl 
				WHERE allowCodeOld = '".str_replace("'","''",stripslashes($oldAllowCode))."'";
		$res = $this->execQry($qry);
		
		if($this->getRecCount($res) > 0)
			return $this->getSqlAssoc($res);
		else
			return "0";
	}	
	
	function Space($num)
	{
		$sp = '';
		
		for($i=0; $i<$num; $i++)
			$sp .= ' ';
	
		return $sp;
	}
}


?>