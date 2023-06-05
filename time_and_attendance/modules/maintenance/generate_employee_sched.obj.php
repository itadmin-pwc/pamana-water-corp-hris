<?
class genSchedObj extends commonObj {
	var $Group;
	var $arrShiftCodes;
	var $arrHolidays;
	var $arrDate;
	var $arrpayPd;
	function GenerateSched() {
		$Trns = $this->beginTran();//begin transaction
		$this->EmpShiftCode();
		$this->getholiday();
		$stat = "";		
		$this->execQry("CALL sp_update_empshift_biono ('".$_SESSION['employee_number']."','".$_SESSION['company_code']."')");	
		$sqlEmpLists = "SELECT tblTK_EmpShift.empNo, tblTK_EmpShift.shiftCode, tblBioEmp.bioNumber, tblEmpMast.empBrnCode as brnCode,tblEmpMast.empDiv as empdv
					   FROM tblTK_EmpShift 
					   INNER JOIN tblEmpMast ON tblTK_EmpShift.empNo = tblEmpMast.empNo 
					   		AND tblTK_EmpShift.compCode = tblEmpMast.compCode 
					   LEFT JOIN tblBioEmp ON tblTK_EmpShift.empNo = tblBioEmp.empNo
							AND tblTK_EmpShift.compCode = tblBioEmp.compCode 
					   INNER JOIN tblTK_UserBranch on tblempmast.empBrnCode=tblTK_UserBranch.brnCode		
					   WHERE (status = 'A')
						  AND (tblTK_EmpShift.empNo NOT IN (SELECT empNo
							  FROM tblTK_Timesheet
							  WHERE compCode = '".$_SESSION['company_code']."' 
								  AND tsDate between '".date('Y-m-d',strtotime($this->arrpayPd['pdFrmDate']))."' 
								  AND '".date('Y-m-d',strtotime($this->arrpayPd['pdToDate']))."')) 
								  AND (tblTK_EmpShift.empNo NOT IN (SELECT empNo
									  FROM tblTK_Timesheethist
									  WHERE compCode = '".$_SESSION['company_code']."' 
										  AND tsDate between '".date('Y-m-d',strtotime($this->arrpayPd['pdFrmDate']))."' 
										  AND '".date('Y-m-d',strtotime($this->arrpayPd['pdToDate']))."')) 
					  	  AND empPayGrp='".$this->Group."' 
						  AND tblTK_UserBranch.empNo='".$_SESSION['employee_number']."'	
						  AND tblTK_EmpShift.compCode='".$_SESSION['company_code']."' 
						  AND ((tblEmpMast.empStat='RG') 
						 	OR (((tblEmpMast.dateResigned between '".date('Y-m-d',strtotime($this->arrpayPd['pdFrmDate']))."' 
								AND '".date('Y-m-d',strtotime($this->arrpayPd['pdToDate']))."') 
						 	OR (tblEmpMast.endDate between '".date('Y-m-d',strtotime($this->arrpayPd['pdFrmDate']))."' 
								AND '".date('Y-m-d',strtotime($this->arrpayPd['pdToDate']))."'))))";
		$arrEmpList = $this->getArrRes($this->execQry($sqlEmpLists));
		$arrShiftCodes = $arrCheckCode= array();
		$sqlInsertData = "";
		$sqlInsertData1 = "";
		foreach($arrEmpList as $valEmp) {
				if (!in_array($valEmp['shiftCode'],$arrCheckCode)) {
					$arrCheckCode[]=$valEmp['shiftCode'];
					for($day=0; $day<count($this->arrDate); $day++) {
						$tsDate = $this->arrDate[$day];
						$arrShiftInfo = $this->getEmpShift($tsDate,$valEmp['shiftCode']);
						$Union = ($stat=="")? "":"Union";
						$yr=$this->arrpayPd['pdYear'];
						$chkdt=date('Y-m-d',strtotime($tsDate));
						$exemptldinqcday= '2021-07-02';

						if($chkdt==$exemptldinqcday && $valEmp['empdv'] == '6' ){
							$resttag=$arrShiftInfo['RestDayTag'];
						if($resttag != 'Y'){
							$dayType = '04';
						}else{
							$dayType = '06';
						}
						
						}else{
							$dayType = $this->checkHolidayDate($tsDate,$arrShiftInfo['RestDayTag'],$valEmp['brnCode']);
						}	
						$checkTag = ($arrShiftInfo['crossDay']=='Y') ? "'Y'": "NULL";
						$sqlInsertData = "Insert into tblTK_Timesheet (empNo,bioNo,brnchCd,compCode,tsDate,shftTimeIn,shftLunchOut,
											shftLunchIn,shftBreakOut,shftBreakIn,shftTimeOut,dayType,crossDay,checkTag) 
										  Values ('{$valEmp['empNo']}','{$valEmp['bioNumber']}','{$valEmp['brnCode']}',
											'{$_SESSION['company_code']}','".date('Y-m-d',strtotime($tsDate))."',
											'{$arrShiftInfo['shftTimeIn']}','{$arrShiftInfo['shftLunchOut']}',
											'{$arrShiftInfo['shftLunchIn']}','{$arrShiftInfo['shftBreakOut']}',
											'{$arrShiftInfo['shftBreakIn']}','{$arrShiftInfo['shftTimeOut']}',
											'".$dayType."','{$arrShiftInfo['crossDay']}', $checkTag);";
										
						$arrShiftCodes[$valEmp['shiftCode']][$day] = " ,'{$_SESSION['company_code']}',
							'".date('Y-m-d',strtotime($tsDate))."','{$arrShiftInfo['shftTimeIn']}','{$arrShiftInfo['shftLunchOut']}',
							'{$arrShiftInfo['shftLunchIn']}','{$arrShiftInfo['shftBreakOut']}','{$arrShiftInfo['shftBreakIn']}',
							'{$arrShiftInfo['shftTimeOut']}','$dayType','{$arrShiftInfo['crossDay']}',$checkTag";								
						$stat=1;
							if ($Trns) { 
								$Trns=$this->execQry($sqlInsertData);
							}
						unset($RDField,$RDValue);
					}
					
				} 
				else {
					for($cnt=0; $cnt<count($arrShiftCodes[$valEmp['shiftCode']]); $cnt++) {
						$sqlInsertData1 = "Insert into tblTK_Timesheet (empNo,bioNo,brnchCd,compCode,tsDate,shftTimeIn,
											shftLunchOut,shftLunchIn,shftBreakOut,shftBreakIn,shftTimeOut,dayType,crossDay,checkTag)
										   values ('{$valEmp['empNo']}','{$valEmp['bioNumber']}',
										   	 '{$valEmp['brnCode']}'" . $arrShiftCodes[$valEmp['shiftCode']][$cnt]." );";
						if ($Trns) {
							$Trns = $this->execQry($sqlInsertData1);
						} 
					}
				}
			
			
		}

		if(!$Trns){
			$Trns = $this->rollbackTran();//rollback transaction
			return false;
		}
		else{
			$Trns = $this->commitTran();//commit transaction
			return true;	
		}		
	
	}
	function EmpShiftCode() {
		$sqlShiftCode = "SELECT crossDay,tblTK_ShiftDtl.dayCode, tblTK_ShiftDtl.shftTimeIn, 
							tblTK_ShiftDtl.shftLunchOut, tblTK_ShiftDtl.shftLunchIn, tblTK_ShiftDtl.shftBreakOut, 
                      		tblTK_ShiftDtl.shftBreakIn, tblTK_ShiftDtl.shftTimeOut, 
							tblTK_ShiftDtl.shftCode,tblTK_ShiftDtl.RestDayTag 
						FROM tblTK_ShiftHdr 
						INNER JOIN tblTK_ShiftDtl ON tblTK_ShiftHdr.compCode = tblTK_ShiftDtl.compCode 
							AND tblTK_ShiftHdr.shiftCode = tblTK_ShiftDtl.shftCode
						WHERE (tblTK_ShiftHdr.status = 'A')
							 AND tblTK_ShiftHdr.compCode='{$_SESSION['company_code']}'";
		$this->arrShiftCodes = $this->getArrRes($this->execQry($sqlShiftCode));				
	}
	function getEmpShift($tsDate,$shiftCode) {
		//echo "$tsDate=" . date('D',strtotime($tsDate))." \n";
		switch(date('D',strtotime($tsDate))) {
			case 'Mon' :
				$day = 1;
			break;
			case 'Tue' :
				$day = 2;
			break;
			case 'Wed' :
				$day = 3;
			break;
			case 'Thu' :
				$day = 4;
			break;
			case 'Fri' :
				$day = 5;
			break;
			case 'Sat' :
				$day = 6;
			break;
			case 'Sun' :
				$day = 7;
			break;
		}		
		$arrShift = array();	
		foreach($this->arrShiftCodes as $valShift) {
			if ($valShift['shftCode']==$shiftCode && $valShift['dayCode']==$day) {
				$arrShift = $valShift;
				break;
			}
		}
		return $arrShift;
	}
	function getholiday() {
		$sqlHoliday = "Select holidayDate, brnCode, dayType 
					   from tblHolidayCalendar 
					   where compCode='{$_SESSION['company_code']}' 
					   		AND holidayStat='A'";
		$this->arrHolidays = $this->getArrRes($this->execQry($sqlHoliday));		
	}
	
	function checkHolidayDate($tsDate,$RDTag,$brnCode) {
		if ($RDTag=='Y') {
			$dayType='02';
		}
		else{
			$dayType='01';
		}
		foreach($this->arrHolidays as $valHol) {
			if (date('m/d/Y',strtotime($valHol['holidayDate'])) == date('m/d/Y',strtotime($tsDate)) && ($valHol['brnCode'] =='0' || $valHol['brnCode']==$brnCode)) {
				if ($RDTag == 'Y') {
					if ($valHol['dayType']=='03')
						$dayType='05';
					elseif ($valHol['dayType']=='04')
						$dayType='06';
					elseif ($valHol['dayType']=='07')
						$dayType='08';
				} else {
					$dayType=$valHol['dayType'];
				}
				break;
			}
		}
		return $dayType;
	}
	
	function getPayPeriod() {
		$sqlpayPd = "Select *,DATEDIFF(pdToDate,pdFrmDate) as NoDays from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payGrp='{$this->Group}' AND payCat=3 AND pdTSStat='O'";
		$this->arrpayPd = $arrpayPd = $this->getSqlAssoc($this->execQry($sqlpayPd));
		$endDate = date('m/d/Y',strtotime($arrpayPd['pdFrmDate']));
		$arrDate =  array();
		
		$this->arrDate[] =$endDate;
		for($i=1; $i<=(int)$arrpayPd['NoDays'];$i++) {
			$endDate = $this->DateAdd($endDate);
			$this->arrDate[] = $endDate;
		}
	}
	function DateAdd($date) {
		$month = date('m',strtotime($date));
		$day = date('d',strtotime($date));
		$year = date('Y',strtotime($date));
		$Maxdays = cal_days_in_month(CAL_GREGORIAN,$month,$year);
		$day++;
        if ($day>$Maxdays){
			$day = 1;
			$month++;
			if ($month>12) {
				$month = 1;
				$year++;
			} 
		}
		return date('m/d/Y',strtotime("$month/$day/$year"));
	}
}

?>