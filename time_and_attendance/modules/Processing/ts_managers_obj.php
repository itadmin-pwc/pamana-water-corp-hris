<?
class processManagersTimesheet extends commonObj{
	var $pdFrom;
	var $pdTo;
	var $Group;
	function ProcessManagersTS(){
		$Trns = $this->beginTran();
		if($this->checkManagersAttendance()==0){
			echo "alert('No record found!');";	
			exit();
		}
		else{
			foreach($this->getManagersAttendance() as $valEmp){
				$this->clearTSCorrection($valEmp['empNo']);
				foreach($this->getManagersSchedule($valEmp['empNo']) as $valEmpSched){
					if(($valEmpSched['shftTimeIn']=="0:00") || ($valEmpSched['shftTimeIn']=="00:00") && ($valEmpSched['shftLunchOut']=="0:00") || ($valEmpSched['shftLunchOut']=="00:00") && ($valEmpSched['shftLunchIn']=="0:00") || ($valEmpSched['shftLunchIn']=="00:00") && ($valEmpSched['shftBreakOut']=="0:00") || ($valEmpSched['shftBreakOut']=="00:00") && ($valEmpSched['shftBreakIn']=="0:00") || ($valEmpSched['shftBreakIn']=="00:00") && ($valEmpSched['shftTimeOut']=="0:00") || ($valEmpSched['shftTimeOut']=="00:00")){
					}
					else{				
						$tsmanagers = " Insert into tblTK_TimeSheetCorr (compCode,empNo,tsDate,timeIn,lunchOut,lunchIn,
											breakIn,breakOut,timeOut,encodeDate,encodedBy,stat) 
										 values('".$_SESSION['company_code']."','".$valEmpSched['empNo']."',
										 	'".date('Y-m-d',strtotime($valEmpSched['tsDate']))."','".$valEmpSched['shftTimeIn']."',
											'".$valEmpSched['shftLunchOut']."','".$valEmpSched['shftLunchIn']."',
											'".$valEmpSched['shftBreakOut']."','".$valEmpSched['shftBreakIn']."',
											'".$valEmpSched['shftTimeOut']."','".date('Y-m-d')."',
											'".$_SESSION['employee_number']."','A');";	
						if ($Trns) {
							$Trns = $this->execQry($tsmanagers);
						}
					}
				}	
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
	}
	
	function getManagersAttendance(){
		$sqlQry = "Select man.empNo 
				   from tblTK_ManagersAttendance man 
				   inner join tblEmpMast emp on man.empNo=emp.empNo 
				   where emp.empPayGrp='".$this->Group."'";
		return $this->getArrRes($this->execQry($sqlQry));		   
	}
	
	function getManagersSchedule($empno){
		$sqlQryTS = "Select empNo, tsDate, dayType, shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut 
				   from tblTK_Timesheet where empNo='{$empno}' and tsDate between '".$this->pdFrom."' and '".$this->pdTo."'
				   order by tsdate";
		return $this->getArrRes($this->execQry($sqlQryTS));		   	
	}

	function GetPayPeriod() {
		$sqlpayPd = "Select pdFrmDate,pdToDate from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payCat=3 and payGrp='{$this->Group}' AND pdTSStat='O'";
		$res = $this->getSqlAssoc($this->execQry($sqlpayPd));
		$this->pdFrom = date('Y-m-d', strtotime($res['pdFrmDate']));
		$this->pdTo = date('Y-m-d', strtotime($res['pdToDate']));
	}
	
	function clearTSCorrection($empno){
		$sqlQryDeleteTS = "Delete from tblTK_TimeSheetCorr where empNo='{$empno}' and tsDate between '".$this->pdFrom."' and '".$this->pdTo."'";
		return $this->execQry($sqlQryDeleteTS);		   	
	}
		
	function checkManagersAttendance(){
		$sqlQryCheck = "Select ts.empNo
						from tblTK_Timesheet ts
						inner join tblEmpMast emp on ts.empNo=emp.empNo
						where ts.empNo in (Select empNo from tblTK_ManagersAttendance)
							and emp.empPayGrp='".$this->Group."'
						group by ts.empNo";
		return $this->getRecCount($this->execQry($sqlQryCheck));					
	}	
} 
?>