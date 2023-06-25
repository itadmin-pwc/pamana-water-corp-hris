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
		
		$resChckPdTSTag = $this->execQryI($qryChckPdTSTag);
		$resChckPdTSTag =  $this->getSqlAssocI($resChckPdTSTag);
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
		
		return $this->execQryI($qryChckPdTSTag);
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
		return $this->execQryI($qryOpen);
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

		$Trns = $this->beginTranI();
		$this->getEmpList();
		/*
		if ($Trns) {
			$Trns = $this->extractTNATS();	
		}*/
		
		$ctr = 0;
		foreach($this->getTSfromTNA() as $val) {
				$ctr++;
				$arr 		= $this->getOvertimeTrnCode($val['dayType']);
				$compcode	= $val['compCode'];
				$empNo		= $val['empNo'];
				$tsDate		= $val['tsDate'];
				$hrsAbsent 	= 0;
				
				if ($val['hrsWorked'] == 0) {
					if (!in_array($val['dayType'], array(2,4,5,6)) && $val['empPayType']=='M') {
						if ($val['tsAppTypeCd']==11)
							$hrsAbsent = 8;
						if ($val['dayType']==3 && $val['legalPayTag']!="Y") {	
							$hrsAbsent = 8;
						} elseif ($val['dayType']==1) {
							$hrsAbsent = 8;
						}
					}  else {
						if ($val['tsAppTypeCd']==11)
							$hrsAbsent = 8;
					}	
				} else {
					$hrsAbsent	= (in_array($val['tsAppTypeCd'],array(14,15,21))) ? 4:0;
				}
				$dayCode = date('N',strtotime($val['tsDate']));
				$hrsWorked = $val['hrsWorked'];
				if ($val['CWWTag']=="Y" && $dayCode==6 && $val['dayType']==1) {
					if ($val['satPayTag']=="Y") {
						$hrsAbsent = 0;
						$hrsWorked = 8;
					} else {
						$hrsAbsent = 8;
						$hrsWorked = 0;
					}
				}
				
				$hrsTardy		= (float)$val['hrsTardy']; 
				$hrsUT			= (float)$val['hrsUT'];
				$hrsOTLe8		= (float)$val['hrsOTLe8'];
				$hrsOTGt8		= (float)$val['hrsOTGt8'];
				$hrsNDLe8		= (float)$val['hrsNDLe8'];
				$hrsNDGt8		= (float)$val['hrsNDGt8'];
				$hrsRegNDLe8	= (float)$val['hrsRegNDLe8'];
				$payGrp			= $val['emppayGrp'];
				$payCat			= $val['emppayCat'];
				$dayType		= $val['dayType'];
				$amtAbsent		= round($hrsAbsent * (float)$val['empHrate'],2);
				$amtTardy		= (float)$val['amtTardy'];
				$amtUT			= (float)$val['amtUT'];
				$amtOTLe8		= (float)$val['amtOTLe8'];
				$amtOTGt8		= (float)$val['amtOTGt8'];
				$amtNDLe8		= (float)$val['amtNDLe8'];
				$amtNDGt8		= (float)$val['amtNDGt8'];
				$amtRegNDLe8	= (float)$val['amtRegNDLe8'];
				$trnOtLe8		= $arr['trnOtLe8'];
				$trnOtGt8		= $arr['trnOtGt8']; 
				$trnNdLe8		= $arr['trnNdLe8']; 
				$trnNdGt8		= $arr['trnNdGt8'];	
				if ($hrsWorked==0) {
					$amtUT = $amtTardy = $hrsTardy = $hrsUT = 0;
				}
				if ($val['empPayType']=='D' && $hrsWorked==0 && $val['hrsOTLe8']==0  && $val['legalPayTag']!='Y') {
					$sqlInsertTS .= "";
				} else {
					if ($val['empPayType']=='D') {
						$amtAbsent	= 0;
						if (in_array($val['dayType'],array(4))) {
							$hrsTardy = 8-$val['hrsOTLe8'];
							$amtTardy = round((float)$hrsTardy * ((float)$val['empDrate']/8),2);
						}
					}
					$sqlInsertTS = "Insert into tblTimesheet (
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
								hrsRegNDLe8, 
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
								amtRegNDLe8, 
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
								'$hrsRegNDLe8',
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
								'$amtRegNDLe8',
								'$trnOtLe8',
								'$trnOtGt8',
								'$trnNdLe8',
								'$trnNdGt8',
								'A'
							)";
						if ($Trns) {
							$Trns = $this->execQryI($sqlInsertTS);	
						}
										
				}
		}



		
		if ($Trns) {
			$Trns = $this->moveTK_HrsWorkedRepository();	
		}

		if ($Trns) {
			$Trns = $this->clearTK_HrsWorkedRepository();	
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
			$Trns = $this->populate_Deductions();
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
			$Trns = $this->populate_wOvertime();
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
			$Trns = $this->populate_wtimesheet();
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
/*		if($Trns){
			$Trns = $this->ClearTK_EventLogs();
		}*/
		if($Trns){
			$Trns = $this->MoveAdjustmentsHdr();
		}		

		if($Trns){
			$Trns = $this->MoveAdjustmentDtl();
		}		

		if($Trns){
			$Trns = $this->ClearAdjDtl();
		}		

		if($Trns){
			$Trns = $this->ClearAdjustmentsHdr();
		}		
		
		if($Trns){
			$Trns = $this->MoveTK_AdjDtl();
		}		

		if($Trns){
			$Trns = $this->clearTK_AdjDtl();
		}		

		if($Trns){
			$Trns = $this->TagTSPeriod();
		}										
										
		if($Trns){
			$Trns = $this->OpenTSPayPeriod();
		}
		
	
		if(!$Trns){
			$Trns = $this->rollbackTranI();
			return false;
		}
		else{
			$Trns = $this->commitTranI();
			return true;	
		}		
	}
	
	function CloseTK_RestDay() {
		$sqlRD = "Insert into tblTK_ChangeRDApphist (compCode, empNo, refNo, dateFiled, tsAppTypeCd, cRDDateFrom, cRDDateTo, cRDReason, dateApproved, userApproved, dateAdded, addedBy, 
                      cRDStat, completeTag) Select compCode, empNo, refNo, dateFiled, tsAppTypeCd, cRDDateFrom, cRDDateTo, cRDReason, dateApproved, userApproved, dateAdded, addedBy, 
                      cRDStat, completeTag from tblTK_ChangeRDApp where compCode='{$_SESSION['company_code']}' AND cRDStat='A' AND completeTag='C' AND empNo IN ({$this->Emplist}) ";
		return $this->execQryI($sqlRD);
	}

	function ClearTK_RestDay() {
		// $sqlRD = "Delete from tblTK_ChangeRDApp where compCode='{$_SESSION['company_code']}' AND cRDStat='A' AND completeTag='C' AND empNo IN ({$this->Emplist})";
		$sqlRDclr = "Delete from tblTK_ChangeRDApp where compCode='{$_SESSION['company_code']}' AND cRDStat='A'";
		return $this->execQryI($sqlRDclr);
	}
	
	function CloseTK_ChangeShift() {
		$sqlCS = "Insert into tblTK_CSApphist (compcode, empNo, refNo, dateFiled, csDateTo, csDateFrom, csShiftFromIn, csShiftFromOut, csShiftToIn, csHiftToOut, csReason, dateApproved, 
                      userApproved, dateAdded, addedBy, csStat, crossDay)  SELECT     compcode, empNo, refNo, dateFiled, csDateTo, csDateFrom, csShiftFromIn, csShiftFromOut, csShiftToIn, csHiftToOut, csReason, dateApproved, 
                      userApproved, dateAdded, addedBy, csStat, crossDay
FROM         tblTK_CSApp where compCode='{$_SESSION['company_code']}' AND csStat='A' AND empNo IN ({$this->Emplist}) ";
		return $this->execQryI($sqlCS);
	}
	
	function ClearTK_ChangeShift() {
		$sqlCSclr = "Delete FROM tblTK_CSApp where compCode='{$_SESSION['company_code']}' AND csStat='A'";
		return $this->execQryI($sqlCSclr);	
	}
	
	function CloseTK_Deductions() {
		$sqlDeductions = "Insert into tblTK_Deductionshist (compCode, empNo, tsDate, hrsTardy, hrsUT, amtTardy, amtUT, trnCodeTardy, trnCodeUT, tsStatus)  SELECT compCode, empNo, tsDate, hrsTardy, hrsUT, amtTardy, amtUT, trnCodeTardy, trnCodeUT, tsStatus FROM tblTK_Deductions where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}) ";
		return $this->execQryI($sqlDeductions);	
	}
	
	function Move_Deductions() {
		$sqlMoveDeductions = "
		Delete FROM tblTK_wDeductions where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}); ";
		return $this->execQryI($sqlMoveDeductions);	
	}
	function populate_Deductions() {
		$sqlpopulate_Deductions = "Insert into tblTK_wDeductions (compCode, empNo, tsDate, hrsTardy, hrsUT, amtTardy, amtUT, trnCodeTardy, trnCodeUT, tsStatus)  SELECT compCode, empNo, tsDate, hrsTardy, hrsUT, amtTardy, amtUT, trnCodeTardy, trnCodeUT, tsStatus FROM tblTK_Deductions where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist});";	
		return $this->execQryI($sqlpopulate_Deductions);	
	}

	
	function ClearTK_Deductions() {
		$sqlDeductions = "Delete FROM tblTK_Deductions where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}); ";
		return $this->execQryI($sqlDeductions);
	}
	
	function CloseTK_Leaves() {
		$sqlLeaves = "Insert into tblTK_LeaveApphist (compCode, empNo, refNo, dateFiled, lvDateFrom, lvFromAMPM, lvDateTo, lvToAMPM, tsAppTypeCd, lvDateReturn, lvReturnAMPM, lvReason, lvReliever, lvAuthorized, lvStat, dateApproved, userApproved, dateAdded, userAdded) SELECT     compCode, empNo, refNo, dateFiled, lvDateFrom, lvFromAMPM, lvDateTo, lvToAMPM, tsAppTypeCd, lvDateReturn, lvReturnAMPM, lvReason, lvReliever, lvAuthorized, lvStat, dateApproved, userApproved, dateAdded, userAdded FROM tblTK_LeaveApp where compCode='{$_SESSION['company_code']}' AND  lvDateTo<='{$this->pdToDate}' AND lvStat='A' AND empNo IN ({$this->Emplist}) ";
		return $this->execQryI($sqlLeaves);	
	}
	
	function ClearTK_Leaves() {
		// $sqlLeaves = "Delete FROM tblTK_LeaveApp where compCode='{$_SESSION['company_code']}' AND  lvDateTo<='{$this->pdToDate}' AND lvStat='A' AND empNo IN ({$this->Emplist}) ";
		$sqlLeavesclr = "Delete FROM tblTK_LeaveApp where compCode='{$_SESSION['company_code']}'  AND lvStat='A' ";
		return $this->execQryI($sqlLeavesclr);	
	}
	
	function CloseTK_OB() {
		$sqlOB = "Insert into tblTK_OBApphist (compCode, empNo, refNo, obDate, obDestination, dateFiled, obSchedIn, obSchedOut, obActualTimeIn, obActualTimeOut, obReason, hrs8Deduct, dateApproved, userApproved, dateAdded, addedBy, obStat)  SELECT compCode, empNo, refNo, obDate, obDestination, dateFiled, obSchedIn, obSchedOut, obActualTimeIn, obActualTimeOut, obReason, hrs8Deduct, dateApproved, userApproved, dateAdded, addedBy, obStat FROM tblTK_OBApp where compCode='{$_SESSION['company_code']}'  AND obStat='A' AND empNo IN ({$this->Emplist}) AND obDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQryI($sqlOB);	
	}
	
	function ClearTK_OB() {
		// $sqlOB = "Delete FROM tblTK_OBApp where compCode='{$_SESSION['company_code']}'  AND obStat='A' AND empNo IN ({$this->Emplist}) AND obDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		$sqlOBclr = "Delete FROM tblTK_OBApp where compCode='{$_SESSION['company_code']}'  AND obStat='A' ";
		return $this->execQryI($sqlOBclr);	
	}
	
	function CloseTK_OTApp() {
		$sqlOTApp = "Insert into tblTK_OTApphist (compCode, empNo, otDate, refNo, dateFiled, otReason, otIn, otOut, dateApproved, userApproved, dateAdded, userAdded, otStat, crossTag)  SELECT compCode, empNo, otDate, refNo, dateFiled, otReason, otIn, otOut, dateApproved, userApproved, dateAdded, userAdded, otStat, crossTag FROM tblTK_OTApp where compCode='{$_SESSION['company_code']}'  AND otStat='A' AND empNo IN ({$this->Emplist}) AND otDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQryI($sqlOTApp);	
	}
	
	function ClearTK_OTApp() {
		// $sqlOTApp = "Delete FROM tblTK_OTApp where compCode='{$_SESSION['company_code']}'  AND otStat='A' AND empNo IN ({$this->Emplist}) AND otDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
			$sqlOTAppclr = "Delete FROM tblTK_OTApp where compCode='{$_SESSION['company_code']}'  AND otStat='A'";
		return $this->execQryI($sqlOTAppclr);	
	}
	
	function CloseTK_Overtime() {
//ALEJO EDITED
		$sqlOvertime = "Insert into tblTK_Overtimehist (compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus,hrsRegNDLe8,amtRegNDLe8) SELECT compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus,hrsRegNDLe8,amtRegNDLe8 FROM tblTK_Overtime where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist})";
	//ALEJO EDITED

//OLD CODE
		// $sqlOvertime = "Insert into tblTK_Overtimehist (compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus) SELECT compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus FROM tblTK_Overtime where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist})";
		
//OLD CODE
		return $this->execQryI($sqlOvertime);	
	}

	function Move_Overtime() {
		$sqlMoveOvertime = "
		Delete from tblTK_wOvertime where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist});";
		return $this->execQryI($sqlMoveOvertime);	
	}
	function populate_wOvertime() {
		$sqlPopulate_wovertime = "Insert into tblTK_wOvertime (compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus,hrsRegNDLe8, amtRegNDLe8) SELECT compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8, hrsNDGt8, amtOTLe8, amtOTGt8, amtNDLe8, amtNDGt8, tsStatus,hrsRegNDLe8, amtRegNDLe8 FROM tblTK_Overtime where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist});";	
		return $this->execQryI($sqlPopulate_wovertime);	
	}
	function ClearTK_Overtime() {
		$sqlOvertime = "Delete FROM tblTK_Overtime where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist});";
		return $this->execQryI($sqlOvertime);	
	}
	
	function CloseTK_Timesheet() {
		$sqlTS = "Insert into tblTK_Timesheethist (compcode, empNo, tsDate, bioNo, dayType, shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut, tsAppTypeCd, timeIn,lunchOut, lunchIn, breakOut, breakIn, timeOut, otIn, otOut, otCrossTag, hrsRequired, hrsWorked, legalPayTag, attendType, payGrp, payCat, brnchCd,crossDay, dedTag, otTag, checkTag, hrs8Deduct, dateUploaded, dateEdited, editedBy, dateUnlocked, userUnlocked, datePosted, userPosted,obTag, csTag, crdTag,satPayTag) SELECT compcode, empNo, tsDate, bioNo, dayType, shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut, tsAppTypeCd, timeIn,lunchOut, lunchIn, breakOut, breakIn, timeOut, otIn, otOut, otCrossTag, hrsRequired, hrsWorked, legalPayTag, attendType, payGrp, payCat, brnchCd,crossDay, dedTag, otTag, checkTag, hrs8Deduct, dateUploaded, dateEdited, editedBy, dateUnlocked, userUnlocked, datePosted, userPosted,obTag, csTag, crdTag,satPayTag FROM tblTK_Timesheet where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist})";
		return $this->execQryI($sqlTS);
	}
	
	function Move_Timesheet() {
		$sqlMoveTS = "
		Delete FROM tblTK_wTimesheet where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist});";
		return $this->execQryI($sqlMoveTS);
	}	
	function populate_wtimesheet() {
		$sqlPopulate_wtimesheet = "		Insert into tblTK_wTimesheet (compcode, empNo, tsDate, bioNo, dayType, shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut, tsAppTypeCd, timeIn,lunchOut, lunchIn, breakOut, breakIn, timeOut, otIn, otOut, otCrossTag, hrsRequired, hrsWorked, legalPayTag, attendType, payGrp, payCat, brnchCd,crossDay, dedTag, otTag, checkTag, hrs8Deduct, dateUploaded, dateEdited, editedBy, dateUnlocked, userUnlocked, datePosted, userPosted) SELECT compcode, empNo, tsDate, bioNo, dayType, shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut, tsAppTypeCd, timeIn,lunchOut, lunchIn, breakOut, breakIn, timeOut, otIn, otOut, otCrossTag, hrsRequired, hrsWorked, legalPayTag, attendType, payGrp, payCat, brnchCd,crossDay, dedTag, otTag, checkTag, hrs8Deduct, dateUploaded, dateEdited, editedBy, dateUnlocked, userUnlocked, datePosted, userPosted FROM tblTK_Timesheet where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist});";	
		return $this->execQryI($sqlPopulate_wtimesheet);
	}
	function ClearTK_Timesheet() {
		$sqlTS = "Delete FROM tblTK_Timesheet where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist})";
		return $this->execQryI($sqlTS);
	}

	function CloseTK_TSCorr() {
		$sqlTSCorr= "Insert into tblTK_TimeSheetCorrhist (compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, breakIn, breakOut, timeOut, otIn, otOut, editReason, encodeDate, encodedBy, stat) SELECT compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, breakIn, breakOut, timeOut, otIn, otOut, editReason, encodeDate, encodedBy, stat FROM tblTK_TimeSheetCorr where compCode='{$_SESSION['company_code']}' AND stat='A' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist})";
		return $this->execQryI($sqlTSCorr);
	}

	function ClearTK_TSCorr() {
		$sqlTSCorr= "Delete FROM tblTK_TimeSheetCorr where compCode='{$_SESSION['company_code']}' AND stat='A' AND tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' AND empNo IN ({$this->Emplist})";
		return $this->execQryI($sqlTSCorr);
	}
	
	function CloseTK_UT() {
		$sqlUT = "Insert into tblTK_UTApphist (compCode, empNo, utDate, refNo, dateFiled, offTimeOut, utTimeOut, utReason, dateAdded, userAdded, dateApproved, userApproved, utStat) SELECT compCode, empNo, utDate, refNo, dateFiled, offTimeOut, utTimeOut, utReason, dateAdded, userAdded, dateApproved, userApproved, utStat FROM tblTK_UTApp where compCode='{$_SESSION['company_code']}' AND utStat='A' AND empNo IN ({$this->Emplist}) AND utDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQryI($sqlUT);
	}
	
	function ClearTK_UT() {
		$sqlUT = "Delete FROM tblTK_UTApp where compCode='{$_SESSION['company_code']}' AND utStat='A' AND empNo IN ({$this->Emplist}) AND utDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQryI($sqlUT);
	}		

	function CloseTK_EventLogs() {
		$sqlEventLogs = "Insert into tblTK_EventLogshist (cStoreNum, EDATE, ETIME, EDOOR, EFLOOR, ESABUN, ETAG, ENAME, ELNAME, EPART, EDEP, ESTATUS, EFUNCTION, EINOUT) SELECT cStoreNum, EDATE, ETIME, EDOOR, EFLOOR, ESABUN, ETAG, ENAME, ELNAME, EPART, EDEP, ESTATUS, EFUNCTION, EINOUT FROM tblTK_EventLogs INNER JOIN tblTK_Timesheet ON tblTK_EventLogs.cStoreNum = tblTK_Timesheet.brnchCd AND CAST(tblTK_EventLogs.ETAG AS UNSIGNED) = CAST(tblTK_Timesheet.bioNo AS UNSIGNED) where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}) AND cast(EDATE as datetime) between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQryI($sqlEventLogs);
	}
	
	function ClearTK_EventLogs() {
		echo $sqlEventLogs = "Delete FROM tblTK_EventLogs where cast(etag as unsigned)  IN 
		( SELECT  cast(bioNo as unsigned) FROM tblTK_empShift where compCode='{$_SESSION['company_code']}' AND empNo IN ({$this->Emplist}))";
		//return $this->execQryI($sqlEventLogs);
	}
	
	function checkPeriods($pdNumber, $pdYear)
	{
		$qryPeriod = "";
	}

	function CountErrorTag() {
		$sqlError = "SELECT empNo from tblTK_Timesheet where compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpmast where compCode='{$_SESSION['company_code']}' AND empPayGrp='".$_SESSION["pay_group"]."')
							  	 AND checkTag='Y'";
		return $this->getRecCountI($this->execQryI($sqlError));
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
				tblTK_Overtime.hrsRegNDLe8, 
				emp.emppayGrp, 
				emp.emppayCat,
				emp.empPayType,
				tk.dayType, 
				tblTK_Overtime.amtOTLe8, 
				tblTK_Overtime.amtOTGt8, 
				tblTK_Overtime.amtNDLe8, 
				tblTK_Overtime.amtNDGt8, 
				tblTK_Overtime.amtRegNDLe8, 
				tblTK_Deductions.amtTardy, 
				tblTK_Deductions.amtUT, 
				tblTK_Deductions.trnCodeTardy, 
				tblTK_Deductions.trnCodeUT, 
				tblTK_Deductions.tsStatus,
				emp.empDrate,
				emp.empHrate,
				tsAppTypeCd,
				legalPayTag,satPayTag,CWWTag
				FROM tblTK_Timesheet tk LEFT OUTER JOIN
										tblTK_Overtime ON tk.empNo = tblTK_Overtime.empNo AND tk.tsDate = tblTK_Overtime.tsDate LEFT OUTER JOIN
										tblTK_Deductions ON tk.empNo = tblTK_Deductions.empNo AND tk.tsDate = tblTK_Deductions.tsDate
									 INNER JOIN
										tblEmpMast emp ON tk.empNo=emp.empNo
									INNER JOIN
										tbltk_EmpShift sf on tk.empNo=sf.empNo
				WHERE emp.empNo IN ($this->Emplist) AND tk.tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'
		";
		$sql = $this->execQryI($sql);
		$row = $this->getArrResI($sql);
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
					if (!in_array($val['dayType'],array(2,4,5,6)) && $val['empPayType']=="M") {
						if ($val['dayType']==3 && $val['legalPayTag']!="Y") {	
							$hrsAbsent = 8;
						} elseif ($val['dayType']==1) {
							$hrsAbsent = 8;
						}
					} 
					
						
				} else {
					$hrsAbsent	= (in_array($val['tsAppTypeCd'],array(14,15,21))) ? 4:0;
				}
				$dayCode = date('N',strtotime($val['tsDate']));
				$hrsWorked = $val['hrsWorked'];
				if ($val['CWWTag']=='Y' && $dayCode==6 && $val['dayType']==1) {
					if ($val['satPayTag']=='Y') {
						$hrsAbsent = 0;
						$hrsWorked = 8;
					} else {
						$hrsAbsent = 8;
						$hrsWorked = 0;
					}
				}
				
				$hrsTardy		= (float)$val['hrsTardy']; 
				$hrsUT			= (float)$val['hrsUT'];
				$hrsOTLe8		= (float)$val['hrsOTLe8'];
				$hrsOTGt8		= (float)$val['hrsOTGt8'];
				$hrsNDLe8		= (float)$val['hrsNDLe8'];
				$hrsNDGt8		= (float)$val['hrsNDGt8'];
				$hrsRegNDLe8	= (float)$val['hrsRegNDLe8'];
				$payGrp			= $val['emppayGrp'];
				$payCat			= $val['emppayCat'];
				$dayType		= $val['dayType'];
				$amtAbsent		= round($hrsAbsent * (float)$val['empHrate'],2);
				$amtTardy		= (float)$val['amtTardy'];
				$amtUT			= (float)$val['amtUT'];
				$amtOTLe8		= (float)$val['amtOTLe8'];
				$amtOTGt8		= (float)$val['amtOTGt8'];
				$amtNDLe8		= (float)$val['amtNDLe8'];
				$amtNDGt8		= (float)$val['amtNDGt8'];
				$amtRegNDLe8	= (float)$val['amtRegNDLe8'];
				$trnOtLe8		= $arr['trnOtLe8'];
				$trnOtGt8		= $arr['trnOtGt8']; 
				$trnNdLe8		= $arr['trnNdLe8']; 
				$trnNdGt8		= $arr['trnNdGt8'];	
				if ($hrsWorked==0) {
					$amtUT = $amtTardy = $hrsTardy = $hrsUT = 0;
				}
				if ($val['empPayType']=='D' && $hrsWorked==0 && $val['hrsOTLe8']==0  && $val['legalPayTag']!='Y') {
					$sqlInsertTS .= "";
				} else { 
					if ($val['empPayType']=='D') {
						$amtAbsent	= 0;
						if (in_array($val['dayType'],array(4))) {
							$hrsTardy = 8-$val['hrsOTLe8'];
							$amtTardy = round((float)$hrsTardy * ((float)$val['empDrate']/8),2);
						}
					}
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
								hrsRegNDLe8, 
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
								amtRegNDLe8, 
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
								'$hrsRegNDLe8',
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
								'$amtRegNDLe8',
								'$trnOtLe8',
								'$trnOtGt8',
								'$trnNdLe8',
								'$trnNdGt8',
								'A'
							)";
										
				}

		if (in_array($_SESSION['pay_category'],array(2)) && in_array($_SESSION['company_code'],array(5))) {
			if ($sqlInsertTS != "")
				return $this->execQryI($sqlInsertTS);
			else
				return true;
		} else {
			
			if ($sqlInsertTS != "") {
				return $this->execQryI($sqlInsertTS);
			} else {
			
				if ($ctr==0)
					return false;
				else
					return true;
				
			}
		}


		}
		
		if ($_SESSION['pay_category'] == 9)
			$ctr = 1;
		

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
			case '07':
				$arr['trnOtLe8'] = OTLHSH;
				$arr['trnOtGt8'] = OTLHSHGT8;
				$arr['trnNdLe8'] = NDLHSH;
				$arr['trnNdGt8'] = NDLHSHGT8;			
			break;
			case '08':
				$arr['trnOtLe8'] = OTLHSHRD;
				$arr['trnOtGt8'] = OTLHSHRDGT8;
				$arr['trnNdLe8'] = NDLHSHRD;
				$arr['trnNdGt8'] = NDLHSHRDGT8;			
			break;
		}
		return $arr;

	}

	function UpdateEmpMastRestDay() {
		$sqlRD = "Select empNo,tsDate from tblTK_Timesheet where dayType IN ('02','05','08') and empNo IN ($this->Emplist) order by empNo,tsDate";	
		$row = $this->getArrResI($this->execQryI($sqlRD));
		$empNo_temp = $sql = "";
		$arrRD = array();
		foreach($row as $val) {
			if ($empNo_temp != $val['empNo']) {
				if (count($arrRD)>0) {
						$sql = "Update tblEmpmast set empRestDay='".implode(',',$arrRD)."' where empNo='$empNo_temp' \n";
						$this->execQryI($sql);
					unset($arrRD);	
				}
			}
			$arrRD[] = date('m/d/Y',strtotime($val['tsDate']));
			$empNo_temp = $val['empNo'];
		}
		if (count($arrRD)>0) {
				$sql = "Update tblEmpmast set empRestDay='".implode(',',$arrRD)."' where empNo='$empNo_temp' \n";
				$this->execQryI($sql);
			unset($arrRD);	
		}
		
		return true;		
		
	}
	
	function MoveAdjustmentsHdr() {
		$sqlAdjHdr = "Insert into tblEarnTranHeader (compcode, refNo, trnCode, earnRem, earnStat, pdYear, pdNumber) SELECT compcode, refNo, trnCode, earnRem, earnStat, pdYear, pdNumber FROM tblTK_EarnTranHeader where pdYear='{$this->pdYear}' and pdNumber='{$this->pdNumber}' and refNo not in (Select refNo from tblEarnTranHeader)";
		return $this->execQryI($sqlAdjHdr);
	}

	function MoveAdjustmentDtl() {
		$sqlAdjDtl = "Insert into tblEarnTranDtl (compcode, refNo, empNo, trnCntrlNo, trnCode, trnAmount, payGrp, payCat, earnStat, trnTaxCd, processTag) Select compcode, refNo, empNo, trnCntrlNo, trnCode, trnAmount, '{$this->session['pay_group']}', '{$this->session['pay_category']}', earnStat, trnTaxCd, processTag from tblTK_EarnTranDtl where refNo in (Select refNo from tblEarnTranHeader where pdYear='{$this->pdYear}' and pdNumber='{$this->pdNumber}' ) and empNo in ({$this->Emplist})"	;
		return $this->execQryI($sqlAdjDtl);
	}
	
	function ClearAdjDtl() {
		$sqlClearAdjDtl = "Delete from tblTK_EarnTranDtl where refNo in (Select refNo from tblEarnTranHeader where pdYear='{$this->pdYear}' and pdNumber='{$this->pdNumber}' ) and empNo in ({$this->Emplist}) ";	
		return $this->execQryI($sqlClearAdjDtl);
	}

	function ClearAdjustmentsHdr() {
		$sqlAdjHdr = "Delete FROM tblTK_EarnTranHeader where pdYear='{$this->pdYear}' and pdNumber='{$this->pdNumber}' and refNo not in (Select refNo from tblTK_EarnTranDtl)";
		return $this->execQryI($sqlAdjHdr);
	}

	
	function MoveTK_AdjDtl() {
		$sqlMoveTK_AdjDtlhist = " Insert into tblTK_TimesheetAdjustmenthist (compcode, empNo, tsDate, dayType, payGrp, payCat, pdYear, pdNumber, entryTag, includeAllowTag,includeAdvTag, hrsReg, hrsOtLe8, hrsOtGt8, hrsNd, hrsNdGt8, adjBasic, adjOt, adjNd, adjHp, adjEcola,adjCtpa, adjAdv, userAdded, dateAdded, dateApproved, userApproved, userUpdated, dateUpdated, tsStat) SELECT compcode, empNo, tsDate, dayType, payGrp, payCat, pdYear, pdNumber, entryTag, includeAllowTag,includeAdvTag, hrsReg, hrsOtLe8, hrsOtGt8, hrsNd, hrsNdGt8, adjBasic, adjOt, adjNd, adjHp, adjEcola,adjCtpa, adjAdv, userAdded, dateAdded, dateApproved, userApproved, userUpdated, dateUpdated, tsStat FROM tblTK_TimesheetAdjustment WHERE (tsStat = 'A') and pdYear='{$this->pdYear}' and pdNumber='{$this->pdNumber}'  and empNo in ({$this->Emplist})";	
		return $this->execQryI($sqlMoveTK_AdjDtlhist);
	}
	
	function clearTK_AdjDtl() {
		$sqlMoveTK_AdjDtl = "delete from tblTK_TimesheetAdjustment WHERE (tsStat = 'A') and pdYear='{$this->pdYear}' and pdNumber='{$this->pdNumber}'  and empNo in ({$this->Emplist})";
		return $this->execQryI($sqlMoveTK_AdjDtl);
	} 
	
	function moveTK_HrsWorkedRepository() {
		$sqlmoveTK_HrsWorkedRepository = "Insert into tblTK_HrsWorkedRepositoryHist (compCode,empNo,tsDate,dayCode,hrsWorked,satDate) Select compCode,empNo,tsDate,dayCode,hrsWorked,satDate from tblTK_HrsWorkedRepository where satDate is not null and empNo in ({$this->Emplist}) and satDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQryI($sqlmoveTK_HrsWorkedRepository);
	}
	function clearTK_HrsWorkedRepository() {
		$sqlClearTK_HrsWorkedRepository = "Delete from tblTK_HrsWorkedRepository where satDate is not null and empNo in ({$this->Emplist}) and satDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}'";
		return $this->execQryI($sqlClearTK_HrsWorkedRepository);
	}
	
	function getTSfromTNA() {
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
				tblTK_Overtime.hrsRegNDLe8, 
				emp.emppayGrp, 
				emp.emppayCat,
				emp.empPayType,
				tk.dayType, 
				tblTK_Overtime.amtOTLe8, 
				tblTK_Overtime.amtOTGt8, 
				tblTK_Overtime.amtNDLe8, 
				tblTK_Overtime.amtNDGt8, 
				tblTK_Overtime.amtRegNDLe8, 
				tblTK_Deductions.amtTardy, 
				tblTK_Deductions.amtUT, 
				tblTK_Deductions.trnCodeTardy, 
				tblTK_Deductions.trnCodeUT, 
				tblTK_Deductions.tsStatus,
				emp.empDrate,
				emp.empHrate,
				tsAppTypeCd,
				legalPayTag,satPayTag,CWWTag
				FROM tblTK_Timesheet tk LEFT OUTER JOIN
										tblTK_Overtime ON tk.empNo = tblTK_Overtime.empNo AND tk.tsDate = tblTK_Overtime.tsDate LEFT OUTER JOIN
										tblTK_Deductions ON tk.empNo = tblTK_Deductions.empNo AND tk.tsDate = tblTK_Deductions.tsDate
									 INNER JOIN
										tblEmpMast emp ON tk.empNo=emp.empNo
									INNER JOIN
										tbltk_EmpShift sf on tk.empNo=sf.empNo
				WHERE emp.empNo IN ($this->Emplist) AND tk.tsDate between '{$this->pdFrmDate}' AND '{$this->pdToDate}' 
		";
		$sql = $this->execQryI($sql);
		return $this->getArrResI($sql);		
	}
	function checkPeriodTimeSheetTag($pdYear, $pdNum){
		
		$qryChckPdTSTag = "SELECT * FROM tblPayPeriod 
							WHERE compCode = '{$this->session['company_code']}'
							AND payGrp = '{$this->session['pay_group']}'
							AND payCat = '{$this->session['pay_category']}'
							AND pdYear = '".$pdYear."'
							AND pdNumber = '".$pdNum."'
							AND pdTsTag = 'Y'";
		
		$resChckPdTSTag = $this->execQryI($qryChckPdTSTag);
		return $this->getSqlAssocI($resChckPdTSTag);
	
	}
}


?>