<?
class extractTSObj extends commonObj {
	
	var $pdYear;
	var $pdNumber;
	var $pdFrmDate;
	var $pdToDate;
	var $Emplist;
	var $get;//method
	var $session;//session variables	
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}
		
	function checkPeriod(){
		
		$qryChckPdTSTag = "SELECT * FROM tblPayPeriod 
							WHERE compCode = '{$this->session['company_code']}'
							AND payGrp = '{$this->session['pay_group']}'
							AND payCat = '{$this->session['pay_category']}'
							AND pdStat = 'O'";
		
		$resChckPdTSTag = $this->execQry($qryChckPdTSTag);
		$resChckPdTSTag =  $this->getSqlAssoc($resChckPdTSTag);
		$this->pdNumber = $resChckPdTSTag['pdNumber'];
		$this->pdYear = $resChckPdTSTag['pdYear'];
		$this->pdFrmDate = $resChckPdTSTag['pdFrmDate'];
		$this->pdToDate = $resChckPdTSTag['pdToDate'];
		return $resChckPdTSTag;
	
	}
	
	function TagTSPeriod(){
		
		$qryChckPdTSTag = "Update tblPayPeriod set pdTsTag='Y',pdTSStat='C'
							WHERE compCode = '{$this->session['company_code']}'
							AND payGrp = '{$this->session['pay_group']}'
							AND payCat = '{$this->session['pay_category']}'
							AND pdStat = 'O'";
		
		return $this->execQry($qryChckPdTSTag);
	}	

	function OpenTSPayPeriod() {
		if ((int)$this->get['pdNum']==24) {
			$pdYear=(int)$this->get['pdYear'] + 1;
			$pdNum=1;
		}
		else {
			$pdYear=(int)$this->get['pdYear'];
			$pdNum=(int)$this->get['pdNum'] + 1;
		}
		$qryOpen="Update tblPayPeriod set pdTSStat='O' 
			where (compCode = '" . $this->session['company_code'] . "') 
			AND (pdYear = '" . $pdYear . "') 
			AND (pdNumber = '" . $pdNum . "')
			AND (payGrp='" . $this->session['pay_group'] . "')
			AND (payCat='" . $this->session['pay_category'] . "')
		";
		return $this->execQry($qryOpen);
	}
	
	function getEmpList(){
		if ($this->session['pay_category'] != 9) {
			$this->Emplist = "SELECT empNo FROM tblEmpMast 
							   WHERE compCode = '{$_SESSION['company_code']}'
							   AND   empPayGrp  = '{$_SESSION['pay_group']}'
							   AND 	 empPayCat  = '{$_SESSION['pay_category']}'
							   AND   empStat IN ('RG','PR','CN') ";
		} else {
			$this->Emplist = " SELECT empNo FROM tblLastPayEmp 
							   WHERE compCode = '{$_SESSION['company_code']}'
							   AND   payGrp    = '{$_SESSION['pay_group']}'
							   AND   pdNumber = '{$this->pdNumber}' 
							   AND   pdYear = '{$this->pdYear}' 
							   ";
		
		}
		
	}
	
	function mainExtractTS() {

		$Trns = $this->beginTran();
		$this->getEmpList();
		if($Trns){
			$Trns = $this->CloseTK_RestDay();
		}
		
		if($Trns){
			$Trns = $this->ClearTK_RestDay();
		}
		
		if($Trns){
			$Trns = $this->CloseTK_ChangeShift();
		}		

		if($Trns){
			$Trns = $this->ClearTK_ChangeShift();
		}
		
		if($Trns){
			$Trns = $this->CloseTK_Deductions();
		}
		
		if($Trns){
			$Trns = $this->Move_Deductions();
		}
				
		if($Trns){
			$Trns = $this->ClearTK_Deductions();
		}		
		
		if($Trns){
			$Trns = $this->CloseTK_Leaves();
		}
		
		if($Trns){
			$Trns = $this->ClearTK_Leaves();
		}
		
		if($Trns){
			$Trns = $this->CloseTK_OB();
		}
		
		if($Trns){
			$Trns = $this->ClearTK_OB();
		}		
		

		
		if($Trns){
			$Trns = $this->CloseTK_OTApp();
		}
		
		if($Trns){
			$Trns = $this->ClearTK_OTApp();
		}						
		
		if($Trns){
			$Trns = $this->CloseTK_Overtime();
		}	

		if($Trns){
			$Trns = $this->Move_Overtime();
		}

		if($Trns){
			$Trns = $this->ClearTK_Overtime();
		}	
						
		if($Trns){
			$Trns = $this->CloseTK_Timesheet();
		}
		
		if($Trns){
			$Trns = $this->Move_Timesheet();
		}
				
		if($Trns){
			$Trns = $this->ClearTK_Timesheet();
		}
		
		if($Trns){
			$Trns = $this->CloseTK_TSCorr();
		}
		
		if($Trns){
			$Trns = $this->ClearTK_TSCorr();
		}
		
		if($Trns){
			$Trns = $this->CloseTK_UT();
		}
		
		if($Trns){
			$Trns = $this->ClearTK_UT();
		}
		
		if($Trns){
			$Trns = $this->CloseTK_EventLogs();
		}		

		if($Trns){
			$Trns = $this->ClearTK_EventLogs();
		}		


		if($Trns){
			$Trns = $this->TagTSPeriod();
		}										
										
		if($Trns){
			$Trns = $this->OpenTSPayPeriod();
		}	

		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}		
	}
	
	function CloseTK_RestDay() {
		$sqlRD = "Insert into tblTK_ChangeRDApphist (compCode, empNo, refNo, dateFiled, tsAppTypeCd, cRDDateFrom, cRDDateTo, cRDReason, dateApproved, userApproved, dateAdded, addedBy, 
                      cRDStat, completeTag) Select compCode, empNo, refNo, dateFiled, tsAppTypeCd, cRDDateFrom, cRDDateTo, cRDReason, dateApproved, userApproved, dateAdded, addedBy, 
                      cRDStat, completeTag from tblTK_ChangeRDApp where compCode='{$_SESSION['company_code']}' AND cRDStat='A' AND completeTag='C' AND empNo IN ({$this->Emplist}) ";
		return $this->execQry($sqlRD);
	}

	function ClearTK_RestDay() {
		$sqlRD = "Delete from tblTK_ChangeRDApp where compCode='{$_SESSION['company_code']}' AND cRDStat='A' AND completeTag='C' AND empNo IN ({$this->Emplist})";
		return $this->execQry($sqlRD);
	}
	
	function CloseTK_ChangeShift() {
		$sqlCS = "Insert into tblTK_CSApphist (compcode, empNo, refNo, dateFiled, csDateTo, csDateFrom, csShiftFromIn, csShiftFromOut, csShiftToIn, csHiftToOut, csReason, dateApproved, 
                      userApproved, dateAdded, addedBy, csStat, crossDay)  SELECT     compcode, empNo, refNo, dateFiled, csDateTo, csDateFrom, csShiftFromIn, csShiftFromOut, csShiftToIn, csHiftToOut, csReason, dateApproved, 
                      userApproved, dateAdded, addedBy, csStat, crossDay
FROM         tblTK_CSApp where compCode='{$_SESSION['company_code']}' AND csStat='A' AND empNo IN ({$this->Emplist}) ";
		return $this->execQry($sqlCS);
	}
	
	function ClearTK_ChangeShift() {
		$sqlCS = "Delete FROM tblTK_CSApp where compCode='{$_SESSION['company_code']}' AND csStat='A' AND empNo IN ({$this->Emplist})";
		return $this->execQry($sqlCS);	
	}
	
	function CloseTK_Deductions() {
		$sqlDeductions = "Insert into tblTK_Deductionshist (compCode, empNo, tsDate, hrsTardy, hrsUT, amtTardy, amtUT, trnCodeTardy, trnCodeUT, tsStatus)  SELECT compCode, empNo, tsDate, hrsTardy, hrsUT, amtTardy, amtUT, trnCodeTardy, trnCodeUT, tsStatus FROM tblTK_Deductions where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}) ";
		return $this->execQry($sqlDeductions);	
	}
	
	function Move_Deductions() {
		$sqlMoveDeductions = "
		Delete FROM tblTK_wDeductions where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist});
		Insert into tblTK_wDeductions (compCode, empNo, tsDate, hrsTardy, hrsUT, amtTardy, amtUT, trnCodeTardy, trnCodeUT, tsStatus)  SELECT compCode, empNo, tsDate, hrsTardy, hrsUT, amtTardy, amtUT, trnCodeTardy, trnCodeUT, tsStatus FROM tblTK_Deductions where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}); ";
		return $this->execQry($sqlMoveDeductions);	
	}

	
	function ClearTK_Deductions() {
		$sqlDeductions = "Delete FROM tblTK_Deductions where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}) ";
		return $this->execQry($sqlDeductions);
	}
	
	function CloseTK_Leaves() {
		$sqlLeaves = "Insert into tblTK_LeaveApphist (compCode, empNo, refNo, dateFiled, lvDateFrom, lvFromAMPM, lvDateTo, lvToAMPM, tsAppTypeCd, lvDateReturn, lvReturnAMPM, lvReason, lvReliever, lvAuthorized, lvStat, dateApproved, userApproved, dateAdded, userAdded) SELECT     compCode, empNo, refNo, dateFiled, lvDateFrom, lvFromAMPM, lvDateTo, lvToAMPM, tsAppTypeCd, lvDateReturn, lvReturnAMPM, lvReason, lvReliever, lvAuthorized, lvStat, dateApproved, userApproved, dateAdded, userAdded FROM tblTK_LeaveApp where compCode='{$_SESSION['company_code']}' AND  lvDateTo<='{$this->pdToDate}' AND lvStat='A' AND empNo IN ({$this->Emplist}) ";
		return $this->execQry($sqlLeaves);	
	}
	
	function ClearTK_Leaves() {
		$sqlLeaves = "Delete FROM tblTK_LeaveApp where compCode='{$_SESSION['company_code']}' AND  lvDateTo<='{$this->pdToDate}' AND lvStat='A' AND empNo IN ({$this->Emplist}) ";
		return $this->execQry($sqlLeaves);	
	}
	
	function CloseTK_OB() {
		$sqlOB = "Insert into tblTK_OBApphist (compCode, empNo, refNo, obDate, obDestination, dateFiled, obSchedIn, obSchedOut, obActualTimeIn, obActualTimeOut, obReason, hrs8Deduct, dateApproved, userApproved, dateAdded, addedBy, obStat)  SELECT compCode, empNo, refNo, obDate, obDestination, dateFiled, obSchedIn, obSchedOut, obActualTimeIn, obActualTimeOut, obReason, hrs8Deduct, dateApproved, userApproved, dateAdded, addedBy, obStat FROM tblTK_OBApp where compCode='{$_SESSION['company_code']}'  AND obStat='A' AND empNo IN ({$this->Emplist}) AND obDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQry($sqlOB);	
	}
	
	function ClearTK_OB() {
		$sqlOB = "Delete FROM tblTK_OBApp where compCode='{$_SESSION['company_code']}'  AND obStat='A' AND empNo IN ({$this->Emplist}) AND obDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQry($sqlOB);	
	}
	
	function CloseTK_OTApp() {
		$sqlOTApp = "Insert into tblTK_OTApphist (compCode, empNo, otDate, refNo, dateFiled, otReason, otIn, otOut, dateApproved, userApproved, dateAdded, userAdded, otStat, crossTag)  SELECT compCode, empNo, otDate, refNo, dateFiled, otReason, otIn, otOut, dateApproved, userApproved, dateAdded, userAdded, otStat, crossTag FROM tblTK_OTApp where compCode='{$_SESSION['company_code']}'  AND otStat='A' AND empNo IN ({$this->Emplist}) AND otDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQry($sqlOTApp);	
	}
	
	function ClearTK_OTApp() {
		$sqlOTApp = "Delete FROM tblTK_OTApp where compCode='{$_SESSION['company_code']}'  AND otStat='A' AND empNo IN ({$this->Emplist}) AND otDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQry($sqlOTApp);	
	}
	
	function CloseTK_Overtime() {
		$sqlOvertime = "Insert into tblTK_Overtimehist (compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus) SELECT compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus FROM tblTK_Overtime where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist})";
		return $this->execQry($sqlOvertime);	
	}

	function Move_Overtime() {
		$sqlMoveOvertime = "
		Delete from tblTK_wOvertime where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist});
		Insert into tblTK_wOvertime (compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus) SELECT compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus FROM tblTK_Overtime where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist});";
		return $this->execQry($sqlMoveOvertime);	
	}
	
	function ClearTK_Overtime() {
		$sqlOvertime = "Delete FROM tblTK_Overtime where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist});";
		return $this->execQry($sqlOvertime);	
	}
	
	function CloseTK_Timesheet() {
		$sqlTS = "Insert into tblTK_Timesheethist (compcode, empNo, tsDate, bioNo, dayType, shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut, tsAppTypeCd, timeIn,lunchOut, lunchIn, breakOut, breakIn, timeOut, otIn, otOut, otCrossTag, hrsRequired, hrsWorked, legalPayTag, attendType, payGrp, payCat, brnchCd,crossDay, dedTag, otTag, checkTag, hrs8Deduct, dateUploaded, dateEdited, editedBy, dateUnlocked, userUnlocked, datePosted, userPosted,obTag, csTag, crdTag) SELECT compcode, empNo, tsDate, bioNo, dayType, shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut, tsAppTypeCd, timeIn,lunchOut, lunchIn, breakOut, breakIn, timeOut, otIn, otOut, otCrossTag, hrsRequired, hrsWorked, legalPayTag, attendType, payGrp, payCat, brnchCd,crossDay, dedTag, otTag, checkTag, hrs8Deduct, dateUploaded, dateEdited, editedBy, dateUnlocked, userUnlocked, datePosted, userPosted,obTag, csTag, crdTag FROM tblTK_Timesheet where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist})";
		return $this->execQry($sqlTS);
	}
	
	function Move_Timesheet() {
		$sqlMoveTS = "
		Delete FROM tblTK_wTimesheet where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist});
		Insert into tblTK_wTimesheet (compcode, empNo, tsDate, bioNo, dayType, shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut, tsAppTypeCd, timeIn,lunchOut, lunchIn, breakOut, breakIn, timeOut, otIn, otOut, otCrossTag, hrsRequired, hrsWorked, legalPayTag, attendType, payGrp, payCat, brnchCd,crossDay, dedTag, otTag, checkTag, hrs8Deduct, dateUploaded, dateEdited, editedBy, dateUnlocked, userUnlocked, datePosted, userPosted) SELECT compcode, empNo, tsDate, bioNo, dayType, shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut, tsAppTypeCd, timeIn,lunchOut, lunchIn, breakOut, breakIn, timeOut, otIn, otOut, otCrossTag, hrsRequired, hrsWorked, legalPayTag, attendType, payGrp, payCat, brnchCd,crossDay, dedTag, otTag, checkTag, hrs8Deduct, dateUploaded, dateEdited, editedBy, dateUnlocked, userUnlocked, datePosted, userPosted FROM tblTK_Timesheet where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist});";
		return $this->execQry($sqlMoveTS);
	}	

	function ClearTK_Timesheet() {
		$sqlTS = "Delete FROM tblTK_Timesheet where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist})";
		return $this->execQry($sqlTS);
	}

	function CloseTK_TSCorr() {
		$sqlTSCorr= "Insert into tblTK_TimeSheetCorrhist (compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, breakIn, breakOut, timeOut, otIn, otOut, editReason, encodeDate, encodedBy, stat) SELECT compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, breakIn, breakOut, timeOut, otIn, otOut, editReason, encodeDate, encodedBy, stat FROM tblTK_TimeSheetCorr where compCode='{$_SESSION['company_code']}' AND stat='A' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist})";
		return $this->execQry($sqlTSCorr);
	}

	function ClearTK_TSCorr() {
		$sqlTSCorr= "Delete FROM tblTK_TimeSheetCorr where compCode='{$_SESSION['company_code']}' AND stat='A' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist})";
		return $this->execQry($sqlTSCorr);
	}
	
	function CloseTK_UT() {
		$sqlUT = "Insert into tblTK_UTApphist (compCode, empNo, utDate, refNo, dateFiled, offTimeOut, utTimeOut, utReason, dateAdded, userAdded, dateApproved, userApproved, utStat) SELECT compCode, empNo, utDate, refNo, dateFiled, offTimeOut, utTimeOut, utReason, dateAdded, userAdded, dateApproved, userApproved, utStat FROM tblTK_UTApp where compCode='{$_SESSION['company_code']}' AND utStat='A' AND empNo IN ({$this->Emplist}) AND utDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQry($sqlUT);
	}
	
	function ClearTK_UT() {
		$sqlUT = "Delete FROM tblTK_UTApp where compCode='{$_SESSION['company_code']}' AND utStat='A' AND empNo IN ({$this->Emplist}) AND utDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQry($sqlUT);
	}		

	function CloseTK_EventLogs() {
		$sqlEventLogs = "Insert into tblTK_EventLogshist (cStoreNum, EDATE, ETIME, EDOOR, EFLOOR, ESABUN, ETAG, ENAME, ELNAME, EPART, EDEP, ESTATUS, EFUNCTION, EINOUT) SELECT cStoreNum, EDATE, ETIME, EDOOR, EFLOOR, ESABUN, ETAG, ENAME, ELNAME, EPART, EDEP, ESTATUS, EFUNCTION, EINOUT FROM tblTK_EventLogs INNER JOIN tblTK_Timesheet ON tblTK_EventLogs.cStoreNum = tblTK_Timesheet.brnchCd AND CAST(tblTK_EventLogs.ETAG AS int) = CAST(tblTK_Timesheet.bioNo AS int) where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}) AND cast(EDATE as datetime) between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQry($sqlEventLogs);
	}
	
	function ClearTK_EventLogs() {
		$sqlEventLogs = "Delete FROM tblTK_EventLogs where id IN ( SELECT tblTK_EventLogs.id FROM tblTK_EventLogs INNER JOIN tblTK_Timesheet ON tblTK_EventLogs.cStoreNum = tblTK_Timesheet.brnchCd AND CAST(tblTK_EventLogs.ETAG AS int) = CAST(tblTK_Timesheet.bioNo AS int) where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}) AND cast(EDATE as datetime) between '{$this->pdFrmDate}' AND '{$this->pdToDate}')";
		return $this->execQry($sqlEventLogs);
	}
	
	function checkPeriods($pdNumber, $pdYear)
	{
		$qryPeriod = "";
	}

	function CountErrorTag() {
		$sqlError = "SELECT empNo from tblTK_Timesheet where compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpmast where compCode='{$_SESSION['company_code']}' AND empPayGrp='".$_SESSION["pay_group"]."')
							  	 AND checkTag='Y'";
		return $this->getRecCount($this->execQry($sqlError));
	}

}


?>