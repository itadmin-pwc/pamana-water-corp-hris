<?
class extractTNATSObj extends commonObj {
	
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
			$this->Emplist = "SELECT empNo FROM tblEmpMast inner join tblBranch on empBrnCode=brnCode
							   WHERE tblEmpMast.compCode = '{$_SESSION['company_code']}'
							   AND   empPayGrp  = '{$_SESSION['pay_group']}'
							   AND 	 empPayCat  = '{$_SESSION['pay_category']}'
							   AND   empStat IN ('RG','PR','CN') 
							   AND	 tnaTag = 'Y'
							   ";
		} else {
			$this->Emplist = " SELECT pay.empNo FROM tblLastPayEmp pay
								inner join tblEmpMast emp on pay.empno=emp.empno
								inner join tblBranch on empBrnCode=brnCode
							   WHERE pay.compCode = '{$_SESSION['company_code']}'
							   AND   payGrp    = '{$_SESSION['pay_group']}'
							   AND   pdNumber = '{$this->pdNumber}' 
							   AND   pdYear = '{$this->pdYear}' 
							   AND	 tnaTag IN ('Y')
							   ";
		
		}
		
	}
	
	function mainExtractTS() {

		$Trns = $this->beginTran();
		$this->getEmpList();
		
		if ($Trns) {
			$Trns = $this->extractTNATS();	
		}
		if ($Trns) {
			$Trns = $this->UpdateEmpMastRestDay();	
		}		
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
	
	function extractTNATS() {
		
		$sql = "
				SELECT
				tk.compCode, 
				tk.empNo, 
				tk.tsDate, 
				tk.hrsWorked, 
				tblTK_Deductions.hrsTardy, 
				tblTK_Deductions.hrsUT, 
				tblTK_Overtime.hrsOTLe8, 
				tblTK_Overtime.hrsOTGt8, 
				tblTK_Overtime.hrsNDLe8, 
				tblTK_Overtime.hrsNDGt8, 
				emp.emppayGrp, 
				emp.emppayCat,
				emp.empPayType,
				tk.dayType, 
				tblTK_Overtime.amtOTLe8, 
				tblTK_Overtime.amtOTGt8, 
				tblTK_Overtime.amtNDLe8, 
				tblTK_Overtime.amtNDGt8, 
				tblTK_Deductions.amtTardy, 
				tblTK_Deductions.amtUT, 
				tblTK_Deductions.trnCodeTardy, 
				tblTK_Deductions.trnCodeUT, 
				tblTK_Deductions.tsStatus,
				emp.empDrate,
				emp.empHrate,
				tsAppTypeCd,
				legalPayTag
				FROM tblTK_Timesheet tk LEFT OUTER JOIN
										tblTK_Overtime ON tk.empNo = tblTK_Overtime.empNo AND tk.tsDate = tblTK_Overtime.tsDate LEFT OUTER JOIN
										tblTK_Deductions ON tk.empNo = tblTK_Deductions.empNo AND tk.tsDate = tblTK_Deductions.tsDate
									 INNER JOIN
										tblEmpMast emp ON tk.empNo=emp.empNo
				WHERE emp.empNo IN ($this->Emplist) AND tk.tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'
		";
		$sql = $this->execQry($sql);
		$row = $this->getArrRes($sql);
		$sqlInsertTS = "";
		$ctr = 0;
		foreach($row as $val) {
				$ctr++;
				$arr 		= $this->getOvertimeTrnCode($val['dayType']);
				$compcode	= $val['compCode'];
				$empNo		= $val['empNo'];
				$tsDate		= $val['tsDate'];
				$hrsAbsent 	= 0;
				
				if ($val['hrsWorked'] == 0) {
					if (!in_array($val['dayType'],array(2,4,5,6)) && $val['empPayType']=='M') {
						if ($val['dayType']==3 && $val['legalPayTag']!='Y') {	
							$hrsAbsent = 8;
						} elseif ($val['dayType']==1) {
							$hrsAbsent = 8;
						}
					} 
					
						
				} else {
					$hrsAbsent	= (in_array($val['tsAppTypeCd'],array(14,15,21))) ? 4:0;
				}
					
				$hrsTardy	= (float)$val['hrsTardy']; 
				$hrsUT		= (float)$val['hrsUT'];
				$hrsOTLe8	= (float)$val['hrsOTLe8'];
				$hrsOTGt8	= (float)$val['hrsOTGt8'];
				$hrsNDLe8	= (float)$val['hrsNDLe8'];
				$hrsNDGt8	= (float)$val['hrsNDGt8'];
				$payGrp		= $val['emppayGrp'];
				$payCat		= $val['emppayCat'];
				$dayType	= $val['dayType'];
				$amtAbsent	= round($hrsAbsent * (float)$val['empHrate'],2);
				$amtTardy	= (float)$val['amtTardy'];
				$amtUT		= (float)$val['amtUT'];
				$amtOTLe8	= (float)$val['amtOTLe8'];
				$amtOTGt8	= (float)$val['amtOTGt8'];
				$amtNDLe8	= (float)$val['amtNDLe8'];
				$amtNDGt8	= (float)$val['amtNDGt8'];
				$trnOtLe8	= $arr['trnOtLe8'];
				$trnOtGt8	= $arr['trnOtGt8']; 
				$trnNdLe8	= $arr['trnNdLe8']; 
				$trnNdGt8	= $arr['trnNdGt8'];	
				if ($val['empPayType']=='D' && $val['hrsWorked']==0 && $val['hrsOTLe8']==0  && $val['legalPayTag']!='Y') {
					$sqlInsertTS .= "";
				} else { 
					if ($val['empPayType']=='D')
						$amtAbsent	= 0;
						
					$sqlInsertTS .= "Insert into tblTimesheet (
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
								empPayGrp, 
								empPayCat, 
								dayType, 
								amtAbsent, 
								amtTardy, 
								amtUt, 
								amtOtLe8, 
								amtOtGt8, 
								amtNdLe8, 
								amtNdGt8, 
								trnOtLe8, 
								trnOtGt8, 
								trnNdLe8, 
								trnNdGt8, 
								tsStat
							) values
							(
								'$compcode',
								'$empNo',
								'$tsDate',
								'$hrsAbsent',
								'$hrsTardy',
								'$hrsUT',
								'$hrsOTLe8',
								'$hrsOTGt8',
								'$hrsNDLe8',
								'$hrsNDGt8',
								'$payGrp',
								'$payCat',
								'$dayType',
								'$amtAbsent',
								'$amtTardy',
								'$amtUT',
								'$amtOTLe8',
								'$amtOTGt8',
								'$amtNDLe8',
								'$amtNDGt8',
								'$trnOtLe8',
								'$trnOtGt8',
								'$trnNdLe8',
								'$trnNdGt8',
								'A'
							);
							\n\n";
				}
		}
		if (in_array($_SESSION['pay_category'],array(2)) && in_array($_SESSION['company_code'],array(5))) {
			if ($sqlInsertTS != "")
				return $this->execQry($sqlInsertTS);
			else
				return true;
		} else {
			
			if ($sqlInsertTS != "") {
				return $this->execQry($sqlInsertTS);
			} else {
				if ($ctr==0)
					return false;
				else
					return true;
				
			}
		}
	}


	function getOvertimeTrnCode($dayType) {
		
		$tranCodeOTND = array(21=>OTRD,22=>OTRDGT8,23=>NDRD,24=>NDRDGT8, 
								  31=>OTLH,32=>OTLHGT8,33=>NDLH,34=>NDLHGT8,
								  41=>OTSH,42=>OTSPGT8,43=>NDSP,44=>NDSHGT8, 
								  51=>OTLHRD,52=>OTLHRDGT8,53=>NDLHRD,54=>NDLHRDGT8,
								  61=>OTSPRD,62=>OTSPRDGT8,63=>NDSPRD,64=>NDSPRDGT8 );		
		switch($dayType) {
			case '01':
				$arr['trnOtLe8'] = '0221';
				$arr['trnOtGt8'] = '0221';
				$arr['trnNdLe8'] = '0327';
				$arr['trnNdGt8'] = '0327';			
			break;	
			case '02':
				$arr['trnOtLe8'] = OTRD;
				$arr['trnOtGt8'] = OTRDGT8;
				$arr['trnNdLe8'] = NDRD;
				$arr['trnNdGt8'] = NDRDGT8;			
			break;
			case '03':
				$arr['trnOtLe8'] = OTLH;
				$arr['trnOtGt8'] = OTLHGT8;
				$arr['trnNdLe8'] = NDLH;
				$arr['trnNdGt8'] = NDLHGT8;			
			break;
			case '04':
				$arr['trnOtLe8'] = OTSH;
				$arr['trnOtGt8'] = OTSPGT8;
				$arr['trnNdLe8'] = NDSP;
				$arr['trnNdGt8'] = NDSHGT8;			
			break;
			case '05':
				$arr['trnOtLe8'] = OTLHRD;
				$arr['trnOtGt8'] = OTLHRDGT8;
				$arr['trnNdLe8'] = NDLHRD;
				$arr['trnNdGt8'] = NDLHRDGT8;			
			break;
			case '06':
				$arr['trnOtLe8'] = OTSPRD;
				$arr['trnOtGt8'] = OTSPRDGT8;
				$arr['trnNdLe8'] = NDSPRD;
				$arr['trnNdGt8'] = NDSPRDGT8;			
			break;
		}
		return $arr;

	}

	function UpdateEmpMastRestDay() {
		$sqlRD = "Select empNo,tsDate from tblTK_Timesheet where dayType IN ('02','05') and empNo IN ($this->Emplist) order by empNo,tsDate";	
		$row = $this->getArrRes($this->execQry($sqlRD));
		$empNo_temp = $sql = "";
		$arrRD = array();
		foreach($row as $val) {
			if ($empNo_temp != $val['empNo']) {
				if (count($arrRD)>0) {
						$sql = $sql . "Update tblEmpmast set empRestDay='".implode(',',$arrRD)."' where empNo='$empNo_temp' \n";
					unset($arrRD);	
				}
			}
			$arrRD[] = date('m/d/Y',strtotime($val['tsDate']));
			$empNo_temp = $val['empNo'];
		}
		if (count($arrRD)>0) {
				$sql = $sql. "Update tblEmpmast set empRestDay='".implode(',',$arrRD)."' where empNo='$empNo_temp' \n";
			unset($arrRD);	
		}
		
		if ($sql != "")	
			return $this->execQry($sql);
		else
			return true;		
		
	}

}


?>