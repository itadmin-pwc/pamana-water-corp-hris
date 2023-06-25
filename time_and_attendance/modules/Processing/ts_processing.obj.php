<?
class TSProcessingObj extends dateDiff {
	var $pdFrom;
	var $pdTo;
	var $Group;
	var $arrShiftCodes		= array();
	var $arrOBList			= array();
	var $arrEventLogs		= array();
	var $arrCorrList		= array();
	var $arrUTExempEmpList 	= array();
	var $arrLeaveAppTypes 	= array();
	var $arrUTEmpList	 	= array();
	var $arrHolidays	 	= array();
	function ProcessTS() {
		$Trns = $this->beginTranI();//begin transaction
		$this->resetTKlogs();
		$this->EmpShiftCode();
		$this->getOB();
		$this->getOBGrpd();
		$this->getholiday();
		$this->getOTList();
		$this->resetCheckTag();
		$this->getUTEmpList();
		
		//$this->resetCheckTag();
		//$this->ClearOTDedViolation();
		
		if ($Trns) {
			$Trns = $this->resetCheckTag();
		}
		if ($Trns) {
			$Trns = $this->ClearDeductions();
		}		
		if ($Trns) {
			$Trns = $this->clearOvertime();
		}		
		if ($Trns) {
			$Trns = $this->ClearViolations();
		}		
		
		$this->getTSCorrectionList();
		$this->getLeaveAppTypes();
		$sqlUpdateRD = "";

		$dtFrom	= date('Ymd',strtotime($this->pdFrom));
		$dtTo	= date('Ymd',strtotime($this->pdTo));

		$sqlUnpostedRD = "";
		$arrCRD = $this->getChangeRestDay();
		foreach($arrCRD as $valRD) {
			$dayTypeFrom = $this->checkHolidayDate($valRD['cRDDateFrom'],'',$valRD['brnCode'],$valRD['empdv']);
			$dayTypeTo = $this->checkHolidayDate($valRD['cRDDateTo'],'Y',$valRD['brnCode'],$valRD['empdv']);
			$dtCdFrom	= date('Ymd',strtotime($valRD['cRDDateFrom']));
			$dtCdTo	= date('Ymd',strtotime($valRD['cRDDateTo']));
			if ($valRD['shftTimeIn'] == '') {
				$arr = $this->copySched($this->pdFrom,$this->pdTo,$valRD['empNo']);
			} else {
				$arr = $valRD;
			}
			if ($Trns) {
				$Trns = $this->execQryI(" Update tblTK_Timesheet set crdTag='Y',shftTimeIn='{$arr['shftTimeIn']}', shftLunchOut='{$arr['shftLunchOut']}', shftLunchIn='{$arr['shftLunchIn']}', shftBreakOut='{$arr['shftBreakOut']}', shftBreakIn='{$arr['shftBreakIn']}', shftTimeOut='{$arr['shftTimeOut']}',dayType='{$dayTypeFrom}' where empNo='{$valRD['empNo']}' and cast(tsDate as date)= '".date('Y-m-d',strtotime($valRD['cRDDateFrom']))."' and compCode='".$_SESSION["company_code"]."';");
			} else {
				break; 	
			}			
			if ($Trns) {
				$Trns = $this->execQryI(" Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='{$dayTypeTo}' where compCode='".$_SESSION["company_code"]."' and empNo='{$valRD['empNo']}' and cast(tsDate as date)= '".date('Y-m-d',strtotime($valRD['cRDDateTo']))."';");
			} else {
				break; 	
			}			

			$sqlUpdateRD .= " Update tblTK_Timesheet set crdTag='Y',shftTimeIn='{$arr['shftTimeIn']}', shftLunchOut='{$arr['shftLunchOut']}', shftLunchIn='{$arr['shftLunchIn']}', shftBreakOut='{$arr['shftBreakOut']}', shftBreakIn='{$arr['shftBreakIn']}', shftTimeOut='{$arr['shftTimeOut']}',dayType='$dayTypeFrom' where empNo='{$valRD['empNo']}' and cast(tsDate as date)= '".date('Y-m-d',strtotime($valRD['cRDDateFrom']))."' and compCode='".$_SESSION["company_code"]."';";
			
			$sqlUpdateRD .= " Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='$dayTypeTo' where compCode='".$_SESSION["company_code"]."' and empNo='{$valRD['empNo']}' and cast(tsDate as date)= '".date('Y-m-d',strtotime($valRD['cRDDateTo']))."';";	
			
			
			if ($dtCdTo > $dtTo || $dtCdFrom > $dtTo) { 
				$sqlUnpostedRD .= " Update tblTK_ChangeRDApp  set completeTag='P' where empNo = '{$valRD['empNo']}' AND cast(cRDDateTo as date) = '".date('Y-m-d',strtotime($valRD['cRDDateTo']))."' AND  compCode='{$_SESSION['company_code']}';";
				if ($Trns) {
					$Trns = $this->execQryI(" Update tblTK_ChangeRDApp  set completeTag='P' where empNo = '{$valRD['empNo']}' AND cast(cRDDateTo as date) = '".date('Y-m-d',strtotime($valRD['cRDDateTo']))."' AND  compCode='{$_SESSION['company_code']}';");
				} else {
					break; 	
				}
			} else  {
				if ($Trns) {
					$Trns = $this->execQryI(" Update tblTK_ChangeRDApp  set completeTag='C' where empNo = '{$valRD['empNo']}' AND cast(cRDDateTo as date) = '".date('Y-m-d',strtotime($valRD['cRDDateTo']))."' AND  compCode='{$_SESSION['company_code']}';");
				} else {
					break; 	
				}
				$sqlUnpostedRD .= " Update tblTK_ChangeRDApp  set completeTag='C' where empNo = '{$valRD['empNo']}' AND cast(cRDDateTo as date) = '".date('Y-m-d',strtotime($valRD['cRDDateTo']))."' AND  compCode='{$_SESSION['company_code']}';";
			}
		}

		//Process Change Shift
		$sqlUpdateCS = "";
		if ($Trns) {
			foreach($this->GetChangeShift() as $valCS) {
				$sqlUpdateCS .= "Update tblTK_Timesheet set csTag='Y',shftTimeIn='{$valCS['csShiftToIn']}', shftTimeOut='{$valCS['csHiftToOut']}',crossDay='{$valCS['crossDay']}' where compCode='".$_SESSION["company_code"]."' and empNo='{$valCS['empNo']}' and cast(tsDate as date)= '".date('Y-m-d',strtotime($valCS['tsDate']))."'; \n";
				if ($Trns) {
					$Trns = $this->execQryI("Update tblTK_Timesheet set csTag='Y',shftTimeIn='{$valCS['csShiftToIn']}', shftTimeOut='{$valCS['csHiftToOut']}',crossDay='{$valCS['crossDay']}' where compCode='".$_SESSION["company_code"]."' and empNo='{$valCS['empNo']}' and cast(tsDate as date)= '".date('Y-m-d',strtotime($valCS['tsDate']))."'; \n");
				} else {
					break; 	
				}			
				
			}
		}
		
		//Process Leaves
		if ($Trns) {
			$sqlUpdateLeaves = "";
			foreach($this->getLeaves() as $valLeaves) {
				$appType = $this->valLeaveType($valLeaves);
				$sqlUpdateLeaves .= " Update tblTK_Timesheet set deductTag='{$valLeaves['deductTag']}',tsAppTypeCd='$appType',attendType='{$valLeaves['tsAppTypeCd']}' where empNo='{$valLeaves['empNo']}' and cast(tsDate as date)= '".date('Y-m-d',strtotime($valLeaves['tsDate']))."'; \n";
				if ($Trns) {
					if ($valLeaves['dayType']==1 || $valLeaves['tsAppTypeCd']==11)
						$Trns = $this->execQryI(" Update tblTK_Timesheet set deductTag='{$valLeaves['deductTag']}',tsAppTypeCd='$appType',attendType='{$valLeaves['tsAppTypeCd']}' where empNo='{$valLeaves['empNo']}' and cast(tsDate as date)= '".date('Y-m-d',strtotime($valLeaves['tsDate']))."'; \n");
				} else {
					break; 	
				}			
			}		
		}
		
	
		//procces OT
		if ($Trns) {
			$sqlUpDateOT = "";
			foreach($this->arrOTList as $valOT) {
				$sqlUpDateOT .= " Update tblTK_Timesheet set otIn='{$valOT['otIn']}',otOut='{$valOT['otOut']}',otCrossTag='{$valOT['crossTag']}' where empNo='{$valOT['empNo']}' AND cast(tsDate as date)='".date('Y-m-d',strtotime($valOT['otDate']))."' AND compCode='{$_SESSION['company_code']}'; ";
				if ($Trns) {
					$Trns = $this->execQryI(" Update tblTK_Timesheet set otIn='{$valOT['otIn']}',otOut='{$valOT['otOut']}',otCrossTag='{$valOT['crossTag']}' where empNo='{$valOT['empNo']}' AND cast(tsDate as date)='".date('Y-m-d',strtotime($valOT['otDate']))."' AND compCode='{$_SESSION['company_code']}'; ");
				} else {
					break; 	
				}			
			}
		}
		
		//Process Event Logs
		if ($Trns) {
			$sqlUpdateTS = "";
			$temp_empNo = "";
			//$arrPlotLogs = $this->PlotLogs();
			$arrPlotLogs = $this->ProcessLogs();
			foreach($arrPlotLogs as $valTSList) {
					
					 $arrQry = $this->SetEventLog($valTSList);
					if (count($arrQry)>0) {
						$i=0;
						while($i<count($arrQry)) {
							if ($Trns) {
								//echo $arrQry[$i]."\n";
								$Trns = $this->execQryI($arrQry[$i]);
							} else {
								break; 	
							}
							$i++;
						}
					}
			}
		}
		
	//Process OB
		if ($Trns) {
			$tmp_empNo = $tmp_obDate = $sqlUpdateOB = "";
			foreach($this->arrOBListgrp as $valOB) {
				//$sqlUpdateOB .= $this->getEmpOB($valOB['empNo'],$valOB['obDate'],$valOB['hrs8Deduct']);
				if ($Trns) {
					$Trns = $this->execQryI($this->getEmpOB($valOB['empNo'],$valOB['obDate'],$valOB['hrs8Deduct']));
				} else {
					break; 	
				}
			}
		}
		
		
		
		//Process TS Corrections
		if ($Trns) {
			$arrCorrLogs1 = array();
			$arrCorrLogs2 = array();
			$sqlUpDateTSCor = $sqlTSCorrLog = "";
			foreach($this->arrCorrList as $valCor) {
				$field="";
				if ($valCor['cor_timeIn']!="")
					$field .= ",timeIn='{$valCor['cor_timeIn']}' ";
				if ($valCor['cor_lunchOut']!="")
					$field .= ",lunchOut='{$valCor['cor_lunchOut']}' ";
				if ($valCor['cor_lunchIn']!="")
					$field .= ",lunchIn='{$valCor['cor_lunchIn']}' ";
				if ($valCor['cor_breakIn']!="")
					$field .= ",breakIn='{$valCor['cor_breakIn']}' ";
				if ($valCor['cor_breakOut']!="")
					$field .= ",breakOut='{$valCor['cor_breakOut']}' ";
				if ($valCor['cor_timeOut']!="")
					$field .= ",timeOut='{$valCor['cor_timeOut']}' ";
					
				if ($valCor['cor_crossTag']!="") {
					$field .= ",crossDay='{$valCor['cor_crossTag']}' ";
				} else {
					//	$field .= ",crossDay=NULL ";
				}
					
				if ($valCor['editReason']!="")
					$field .= ",editReason='{$valCor['editReason']}' ";
	
	
					if ($field != "") {
					if ($Trns) {
						$Trns = $this->execQryI(" Update tblTK_Timesheet set empNo='{$valCor['empNo']}',checkTag='P' $field where empNo='{$valCor['empNo']}' AND cast(tsDate as date)='".date('Y-m-d',strtotime($valCor['tsDate']))."' AND compCode='{$_SESSION['company_code']}'; ");
					}  else {
						break; 	
					}				
					$arrCorrLogs1[] = "('{$_SESSION['company_code']}','{$valCor['empNo']}','{$valCor['tsDate']}','{$valCor['cor_timeIn']}','{$valCor['cor_lunchOut']}','{$valCor['cor_lunchIn']}','{$valCor['cor_breakOut']}','{$valCor['cor_breakIn']}','{$valCor['cor_timeOut']}','1','{$valCor['editReason']}','{$valCor['cor_crossTag']}')";
				
	
					if ($Trns) {
						$Trns = $this->execQryI(" DELETE FROM tblTK_TimeSheetCorrLogs WHERE compCode='{$_SESSION['company_code']}' AND empNo= '{$valCor['empNo']}' AND cast(tsDate as date)= '".date('Y-m-d',strtotime($valCor['tsDate']))."' \n");
					} else {
						break; 	
					} 				
	
					$arrCorrLogs2[] = "('{$valCor['cor_crossTag']}','{$_SESSION['company_code']}','{$valCor['empNo']}','{$valCor['tsDate']}','{$valCor['timeIn']}','{$valCor['lunchOut']}','{$valCor['lunchIn']}','{$valCor['breakOut']}','{$valCor['breakIn']}','{$valCor['timeOut']}','0','{$valCor['editReason']}')";

	
				}
			}
		}
							
	if ($Trns && count($arrCorrLogs1)>0) {		
			$Trns = $this->execQryI(" Insert into tblTK_TimeSheetCorrLogs (compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, breakOut, breakIn, timeOut, cat, editReason,crossTag) values ".implode(',', $arrCorrLogs1));
		} 
		if ($Trns && count($arrCorrLogs2)>0) {
			$Trns = $this->execQryI("Insert into tblTK_TimeSheetCorrLogs (crossTag,compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, breakOut, breakIn, timeOut, cat, editReason) values ".implode(',', $arrCorrLogs2));
		} 

//		if($this->CountErrorTag()>0) {
//			return 2;
//		} else {
//			return 1;
//		}
			
		if(!$Trns){
			$Trns = $this->rollbackTranI();//rollback transaction
			return 3;	
		}
		else{
			$Trns = $this->commitTranI();//commit transaction
			if($this->CountErrorTag()>0) {
				return 2;
			} else {
				return 1;
			}
		}		
	}
	
	function GetChangeShift() {
		$sqlChangeShift = "SELECT tblTK_CSApp.empNo, tblTK_CSApp.csShiftToIn, tblTK_CSApp.csHiftToOut, tblTK_CSApp.csStat, tblTK_Timesheet.tsDate,tblTK_CSApp.crossDay FROM tblTK_CSApp INNER JOIN
                      	tblTK_Timesheet ON tblTK_CSApp.compcode = tblTK_Timesheet.compcode AND tblTK_CSApp.empNo = tblTK_Timesheet.empNo
						WHERE (tblTK_CSApp.csStat = 'A') AND (tblTK_CSApp.compcode = {$_SESSION['company_code']}) AND (tblTK_Timesheet.tsDate BETWEEN tblTK_CSApp.csDateFrom AND 
                      	tblTK_CSApp.csDateTo) AND (tblTK_Timesheet.tsDate BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}') 
						AND tblTK_CSApp.empNo IN (Select empno from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}'
														AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')							
												)";
		return $this->getArrResI($this->execQryI($sqlChangeShift));				
	}
	function GetPayPeriod() {
		$sqlpayPd = "Select pdFrmDate,pdToDate from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payCat=3 and payGrp='{$this->Group}' AND pdTSStat='O'";
		$res = $this->getSqlAssocI($this->execQryI($sqlpayPd));
		$this->pdFrom = date("Y-m-d",strtotime($res['pdFrmDate']));
		$this->pdTo = date("Y-m-d",strtotime($res['pdToDate']));
	}


	function EmpShiftCode() {
		$sqlShiftCode = "SELECT tblTK_ShiftDtl.dayCode, tblTK_ShiftDtl.shftTimeIn, tblTK_ShiftDtl.shftLunchOut, tblTK_ShiftDtl.shftLunchIn, tblTK_ShiftDtl.shftBreakOut, 
                      	tblTK_ShiftDtl.shftBreakIn, tblTK_ShiftDtl.shftTimeOut, tblTK_ShiftDtl.shftCode,RestDayTag,crossDay FROM tblTK_ShiftHdr INNER JOIN tblTK_ShiftDtl ON tblTK_ShiftHdr.compCode = tblTK_ShiftDtl.compCode AND tblTK_ShiftHdr.shiftCode = tblTK_ShiftDtl.shftCode
						WHERE (tblTK_ShiftHdr.status = 'A') AND tblTK_ShiftHdr.compCode='{$_SESSION['company_code']}'";
		$this->arrShiftCodes = $this->getArrResI($this->execQryI($sqlShiftCode));				
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
		foreach($this->arrShiftCodes as $valShift) {
			if ($valShift['shftCode']==$shiftCode && $valShift['dayCode']==$day) 
			{
				//echo "{$valShift['shftCode']}==$shiftCode && {$valShift['dayCode']}==$day\n\n";;
				return $valShift;
			}
		}
	}
	function PlotLogs() {
		$sqlLogs = "Call sp_TK_SetEventLogs ({$this->Group},'{$this->pdFrom}','{$this->pdTo}',{$_SESSION['company_code']},'{$_SESSION['employee_number']}')\n";
		$arrLogs = $this->getArrResI($this->execQryI($sqlLogs));
		$this->next_result();
		return $arrLogs;
	}
	function getChangeRestDay() {
		$sqlRD = "SELECT     tblTK_ChangeRDApp.empNo, tblTK_ChangeRDApp.cRDDateTo, tblTK_ChangeRDApp.cRDDateFrom, tblEmpMast.empBrnCode AS brnCode, tblTK_Timesheet.shftTimeIn, tblTK_Timesheet.shftLunchOut, tblTK_Timesheet.shftLunchIn, tblTK_Timesheet.shftBreakOut, tblTK_Timesheet.shftBreakIn, tblTK_Timesheet.shftTimeOut, tblEmpMast.empDiv AS empdv FROM tblTK_ChangeRDApp INNER JOIN  tblEmpMast ON tblTK_ChangeRDApp.compCode = tblEmpMast.compCode AND tblTK_ChangeRDApp.empNo = tblEmpMast.empNo LEFT OUTER JOIN tblTK_Timesheet ON tblTK_ChangeRDApp.cRDDateTo = tblTK_Timesheet.tsDate AND tblTK_ChangeRDApp.empNo = tblTK_Timesheet.empNo AND tblTK_ChangeRDApp.compCode = tblTK_Timesheet.compcode 
				  where tblTK_ChangeRDApp.compCode='{$_SESSION['company_code']}' AND cRDStat='A' AND (cRDDateFrom BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}'  OR cRDDateTo BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}')  AND empPayGrp='{$this->Group}'
				  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y') AND (completeTag is null or completeTag  = 'P')
				  ";
		return $this->getArrResI($this->execQryI($sqlRD));
	}
	
	function getOB() {
	$sqlOB = "SELECT tbltk_obapp.empNo,tbltk_obapp.obSchedIn,tbltk_obapp.obSchedOut,tbltk_obapp.obDate,tbltk_obapp.obActualTimeIn,tbltk_obapp.obActualTimeOut,tbltk_obapp.hrs8Deduct,tbltk_obapp.crossDay,tbltk_timesheet.dayType FROM tbltk_obapp 
INNER JOIN tbltk_timesheet ON tbltk_timesheet.empNo = tbltk_obapp.empNo AND tbltk_timesheet.compcode = tbltk_obapp.compCode AND tbltk_timesheet.tsDate = tbltk_obapp.obDate
where tbltk_obapp.compCode='{$_SESSION['company_code']}' AND tbltk_obapp.obDate BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}' AND tbltk_obapp.obStat='A' AND tbltk_obapp.empNo IN (Select empno from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')) order by tbltk_obapp.empNo,tbltk_obapp.obDate,tbltk_obapp.obActualTimeIn \n";

		/*$sqlOB = "Select empNo,obSchedIn,obSchedOut,obDate,obActualTimeIn,obActualTimeOut,hrs8Deduct,crossDay from tblTK_OBApp where compCode='{$_SESSION['company_code']}' AND obDate BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}' AND obStat='A' 
					AND empNo IN (Select empno from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}'
										AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')
									) order by empNo,obDate,obActualTimeIn \n";*/
		$this->arrOBList = $this->getArrResI($this->execQryI($sqlOB));
	}
	function getOBGrpd() {
		$sqlOB = "Select empNo,obDate,hrs8Deduct from tblTK_OBApp where compCode='{$_SESSION['company_code']}' AND obDate BETWEEN '".date('Y-m-d', strtotime($this->pdFrom))."' AND '".date('Y-m-d', strtotime($this->pdTo))."' AND obStat='A' 
					AND empNo IN (Select empno from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}'
										AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')
									) group by  empNo,obDate,hrs8Deduct order by empNo,obDate \n";
		$this->arrOBListgrp = $this->getArrResI($this->execQryI($sqlOB));
	}	
	function getEmpOB($empNo,$obDate,$hrs8Deduct) {
		//echo $empNo.",".$obDate.",".$hrs8Deduct."\n";
		$obIN = $obOut = "";
		$ctr=0;
		//$hrs8Deduct =  ($hrs8Deduct=='') ? "":" ,hrs8Deduct='$hrs8Deduct' ;
		$hrs8Deduct = ($hrs8Deduct!=""?",hrs8Deduct='".$hrs8Deduct."'":"");
		
		$crossDay = "";
		foreach($this->arrOBList as $val) 
		{
			if ($val['empNo']==$empNo && date('Y-m-d',strtotime($val['obDate']))==date('Y-m-d',strtotime($obDate)))
			 {
				if ($crossDay == '' && $val['crossDay'] !='') {
					$crossDay = 'Y';
				}
				if (trim($val['obActualTimeIn']) != "") {
					$ctr++;
					$arrLog[] = $val['obActualTimeIn'];
				}
				if (trim($val['obActualTimeOut']) != "") {
					$ctr++;
					$arrLog[] = $val['obActualTimeOut'];
				}
				$obIN = $val['obSchedIn'];
				$obOut = $val['obSchedOut'];
				$dType=$val['dayType'];
				
					
			}
		}
			
		$checkTag="";
		if (in_array($ctr,array(1,3,5)) || $ctr > 6) {
			$checkTag = ",checkTag='Y'";
			
		} else {
			$checkTag = ",checkTag=NULL";
		}

		if ($obIN !="") {
			return " Update tblTK_Timesheet Set crossDay='$crossDay',obTag='Y',shftTimeIn='$obIN',shftTimeOut='$obOut' $hrs8Deduct $checkTag " . $this->ProcessEventLog($ctr,$arrLog,$dType)." where empNo='$empNo' and compCode='{$_SESSION['company_code']}' and cast(tsDate as date)='".date('Y-m-d',strtotime($obDate))."'; \n";
		//, editReason='".FAIL_SKIPLUNCH."'
		} else {
			return "";
		}
			
	}
	
	function ProcessEventLog($ctr,$arrLog=array(),$appTypeCode = "", $arrTS = "") {
//ALEJO UPATE 2020

$arrPrev = $this->checkifPrevDateisCrossDay($arrTS['empNo'],date('Y-m-d',strtotime($arrTS['tsDate'])),'');
		switch($ctr) {
			
			case 1:

			if ($arrPrev['otCrossTag']=='Y' || $arrPrev['crossDay']=='Y') {
					//if ($arrTS['otCrossTag']=='Y' || $arrTS['crossDay']=='Y') {
				$UpdateTS = " ,timeIn='{$arrLog[1]}',lunchOut=NULL,lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[2]}'";
					// }else{
					// 					$UpdateTS = " ,timeIn='{$arrLog[1]}',lunchOut=NULL,lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut=NULL";

					// }
				}else{
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut=NULL,lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut=NULL";
				}
				break;
			case 2:
				if (in_array($appTypeCode,array(13,15)) || ((float)str_replace(":",".",$arrTS['shftLunchOut'])!=0 && (float)str_replace(":",".",$arrTS['shftTimeOut'])==0))
					$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut=NULL";
				elseif (in_array($appTypeCode,array(12,14)))
					$UpdateTS = " ,timeIn=NULL,lunchOut=NULL,lunchIn='{$arrLog[0]}',breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[1]}'";				
				elseif (!in_array($appTypeCode,array(12,14,13,15))){
					if(in_array($appTypeCode,array(03,04))){
						$otobnobiologs=",otIn='{$arrLog[0]}',otOut='{$arrLog[1]}'";
					}else{
					$otobnobiologs="";
					}
					//$arrPrev = $this->checkifPrevDateisCrossDay($arrTS['empNo'],date('Y-m-d',strtotime($arrTS['tsDate'])),'');
				if ($arrPrev['otCrossTag']=='Y' || $arrPrev['crossDay']=='Y') {
				$UpdateTS = ",timeIn='{$arrLog[1]}',lunchOut=NULL,lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[2]}'";
				}else{
					$UpdateTS = ",timeIn='{$arrLog[0]}',lunchOut=NULL,lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[1]}' $otobnobiologs";
				}
				}
				break;
			case 3:
			//$arrPrev = $this->checkifPrevDateisCrossDay($arrTS['empNo'],date('Y-m-d',strtotime($arrTS['tsDate'])),'');
				if ($arrPrev['otCrossTag']=='Y' || $arrPrev['crossDay']=='Y') {
					if ($arrTS['otCrossTag']=='Y' || $arrTS['crossDay']=='Y') {
						$UpdateTS = " ,timeIn='{$arrLog[1]}',lunchOut='{$arrLog[2]}',lunchIn='{$arrLog[3]}',breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[4]}'";
				}else{
						$UpdateTS = " ,timeIn='{$arrLog[1]}',lunchOut='{$arrLog[2]}',lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[3]}'";
				}				
				}else{
					$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[2]}'";
				}
				break;
			case 4:
				if (!in_array($appTypeCode,array(12,14))) {

					if ($arrPrev['otCrossTag']=='Y' || $arrPrev['crossDay']=='Y') {
					//if ($arrTS['otCrossTag']=='Y' || $arrTS['crossDay']=='Y') {
					$UpdateTS = " ,timeIn='{$arrLog[1]}',lunchOut='{$arrLog[2]}',lunchIn='{$arrLog[3]}',breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[4]}'";
					
				}else{
					$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[3]}'";
				}
				} else {
					$UpdateTS = " ,timeIn=NULL,lunchOut=NULL,lunchIn='{$arrLog[0]}',breakOut='{$arrLog[1]}',breakIn='{$arrLog[2]}',timeOut='{$arrLog[3]}'";
				}
				break;
			case 5:
				if ($arrPrev['otCrossTag']=='Y' || $arrPrev['crossDay']=='Y') {
				
						$UpdateTS = " ,timeIn='{$arrLog[1]}',lunchOut='{$arrLog[2]}',lunchIn='{$arrLog[3]}',breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[4]}'";
					

				}else{
					$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn=NULL,timeOut='{$arrLog[4]}'";					
				}

				
				
				break;

			case 6:
			if ($arrPrev['otCrossTag']=='Y' || $arrPrev['crossDay']=='Y') {
						$UpdateTS = " ,timeIn='{$arrLog[1]}',lunchOut='{$arrLog[2]}',lunchIn='{$arrLog[3]}',breakOut='{$arrLog[4]}',breakIn='{$arrLog[5]}',timeOut='{$arrLog[6]}'";
					

				}else{
						$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn='{$arrLog[4]}',timeOut='{$arrLog[5]}'";
				}
				break;
			case 7:
				if ($arrPrev['otCrossTag']=='Y' || $arrPrev['crossDay']=='Y') {
						$UpdateTS =  " ,timeIn='{$arrLog[1]}',lunchOut='{$arrLog[2]}',lunchIn='{$arrLog[3]}',breakOut='{$arrLog[4]}',breakIn='{$arrLog[5]}',timeOut='{$arrLog[7]}'";
					

				}else{
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn='{$arrLog[4]}',timeOut='{$arrLog[6]}'";
				}
				break;
			case 8:
			if ($arrPrev['otCrossTag']=='Y' || $arrPrev['crossDay']=='Y') {
						$UpdateTS =  " ,timeIn='{$arrLog[1]}',lunchOut='{$arrLog[2]}',lunchIn='{$arrLog[3]}',breakOut='{$arrLog[4]}',breakIn='{$arrLog[5]}',timeOut='{$arrLog[8]}'";
					

				}else{

				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn='{$arrLog[4]}',timeOut='{$arrLog[7]}'";
			}
				break;
			default:
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn='{$arrLog[4]}',timeOut='{$arrLog[6]}'";
		}
		
		return $UpdateTS;

//ALEJO UPDATE 2020


		//OLD CODE
		/*switch($ctr) {
			case 1:
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut=NULL,lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut=NULL";
				break;
			case 2:
				if (in_array($appTypeCode,array(13,15)) || ((float)str_replace(":",".",$arrTS['shftLunchOut'])!=0 && (float)str_replace(":",".",$arrTS['shftTimeOut'])==0))
					$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut=NULL";
				elseif (in_array($appTypeCode,array(12,14)))
					$UpdateTS = " ,timeIn=NULL,lunchOut=NULL,lunchIn='{$arrLog[0]}',breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[1]}'";				
				elseif (!in_array($appTypeCode,array(12,14,13,15))){
					if(in_array($appTypeCode,array(03,04))){
						$otobnobiologs=",otIn='{$arrLog[0]}',otOut='{$arrLog[1]}'";
					}else{
					$otobnobiologs="";
					}
					$UpdateTS = ",timeIn='{$arrLog[0]}',lunchOut=NULL,lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[1]}' $otobnobiologs";
				}
				break;
			case 3:
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn=NULL,breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[2]}'";
				break;
			case 4:
				if (!in_array($appTypeCode,array(12,14))) {
					$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut=NULL,breakIn=NULL,timeOut='{$arrLog[3]}'";
				} else {
					$UpdateTS = " ,timeIn=NULL,lunchOut=NULL,lunchIn='{$arrLog[0]}',breakOut='{$arrLog[1]}',breakIn='{$arrLog[2]}',timeOut='{$arrLog[3]}'";
				}
				break;
			case 5:
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn=NULL,timeOut='{$arrLog[4]}'";
				break;
			case 6:
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn='{$arrLog[4]}',timeOut='{$arrLog[5]}'";
				break;
			case 7:
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn='{$arrLog[4]}',timeOut='{$arrLog[6]}'";
				break;
			case 8:
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn='{$arrLog[4]}',timeOut='{$arrLog[7]}'";
				break;
			default:
				$UpdateTS = " ,timeIn='{$arrLog[0]}',lunchOut='{$arrLog[1]}',lunchIn='{$arrLog[2]}',breakOut='{$arrLog[3]}',breakIn='{$arrLog[4]}',timeOut='{$arrLog[6]}'";
		}
		
		return $UpdateTS;*/ 
		//OLD CODE
	}
	
	function getLeaves() {
		$sqlLeaves = " SELECT lvDateFrom, lvFromAMPM, lvDateTo, lvToAMPM,tblTK_Timesheet.tsDate, tblTK_LeaveApp.empNo, tblTK_LeaveApp.tsAppTypeCd,tblTK_LeaveApp.deductTag,tblTK_Timesheet.dayType FROM tblTK_Timesheet INNER JOIN
                    tblTK_LeaveApp ON tblTK_Timesheet.compcode = tblTK_LeaveApp.compcode AND tblTK_Timesheet.empNo = tblTK_LeaveApp.empNo
					WHERE (tblTK_LeaveApp.compcode = {$_SESSION['company_code']}) AND (tblTK_Timesheet.tsDate BETWEEN tblTK_LeaveApp.lvDateFrom AND tblTK_LeaveApp.lvDateTo)
					AND (tblTK_Timesheet.tsDate BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}')
					AND (tblTK_LeaveApp.empNo IN
                          (SELECT empNo FROM tblEmpmast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}'
							  	AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')
						  )) AND (tblTK_LeaveApp.lvStat = 'A')\n\n";
		return $this->getArrResI($this->execQryI($sqlLeaves));
	}	

	function getEmpList() {
		$sqlEmpList = "SELECT     tblTK_Timesheet.empNo, tblTK_Timesheet.tsDate, tblTK_Timesheet.bioNo, tblTK_Timesheet.dayType, tblTK_Timesheet.shftTimeIn, 
                      tblTK_Timesheet.shftLunchOut, tblTK_Timesheet.shftLunchIn, tblTK_Timesheet.shftBreakOut, tblTK_Timesheet.shftBreakIn, 
                      tblTK_Timesheet.shftTimeOut, tblTK_Timesheet.tsAppTypeCd, tblTK_Timesheet.timeIn, tblTK_Timesheet.lunchOut, tblTK_Timesheet.lunchIn, 
                      tblTK_Timesheet.breakOut, tblTK_Timesheet.breakIn, tblTK_Timesheet.timeOut, tblTK_Timesheet.otIn, tblTK_Timesheet.otOut, 
                      tblTK_Timesheet.attendType, tblTK_Timesheet.brnchCd AS brnCode, tblEmpMast.empPayType,empDiv,empBrnCode,otCrossTag,crossDay
					  FROM tblTK_Timesheet INNER JOIN
                      tblEmpMast ON tblTK_Timesheet.compcode = tblEmpMast.compCode AND tblTK_Timesheet.empNo = tblEmpMast.empNo
					  WHERE tblTK_Timesheet.compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND  empPayGrp='{$this->Group}' AND checkTag=NULL
					  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y') 
					  order by tblTK_Timesheet.empNo,tsDate
											
						";
		return $this->getArrResI($this->execQryI($sqlEmpList));		
	}
	
	
	function getEventLogs($empNo) {
		$pdFrom = date('Y-m-d',strtotime($this->pdFrom));
		$pdTo = date('Y-m-d',strtotime($this->pdTo));
		$sqlLogs = "Exec sp_EventLogs '{$this->Group}','$pdFrom','$pdTo','{$_SESSION['company_code']}','$empNo'";	
		return  $this->getArrResI($this->execQryI($sqlLogs));							
	}
	
	function SetEventLog($arrTS) {
	$NewdayType = "";
	$tsDate = date('Y-m-d',strtotime($arrTS['tsDate']));
		
	$empNo = $arrTS['empNo'];

	$checkTag = "";
	$appTag = "";
		$ctr=0;
		$arrLog = array();
		$RD = ($arrTS['dayType']=="02") ? "Y":"";
		$yr=date('Y',strtotime($tsDate));
		$qcday='2022-08-19';
		//$RD = ($arrTS['dayType']=='01') ? "":"Y";
		$NewdayType = $this->checkHolidayDate($arrTS['tsDate'],$RD,$arrTS['brnCode'],$arrTS['empDiv']);
		if ($NewdayType != $arrTS['dayType']) {
			$dayTypeField = ",dayType='{$NewdayType}'";
			// echo print_r($dayTypeField);
		}
		/*if(date('Y-m-d',strtotime($tsDate)) == $qcday && $arrTS['empdiv'] =='6' ){
			$arrTS['dayType']='01';
				if ($RD=="Y") {
					$dayTypeField = ",dayType='02'";
				}else{
					$dayTypeField = ",dayType='01'";
				}
			}*/
		$trans = 0;
		$ctr = $arrTS['NoLogs'];
		$arrLog[0] = $arrTS['Log1'];
		$arrLog[1] = $arrTS['Log2'];
		$arrLog[2] = $arrTS['Log3'];
		$arrLog[3] = $arrTS['Log4'];
		$arrLog[4] = $arrTS['Log5'];
		$arrLog[5] = $arrTS['Log6'];
		$arrLog[6] = $arrTS['Log7'];
		$arrLog[7] = $arrTS['Log8'];
/*		foreach($arrLogs as $val) {
			if ($arrTS['crossDay']=='Y' || $arrTS['otCrossTag']=='Y') {
				if ($this->DateAdd($tsDate) == date('Y-m-d',strtotime($val['tsDate'])) && $val['empNo'] == $empNo && $trans==0) {
					$arrLog[] = substr($val['tsTime'],0,2) . ":" .substr($val['tsTime'],2,2);
					$trans = 1;
					$ctr++;
				}
			}
			
			if ($val['empNo']==$empNo && date('Y-m-d',strtotime($val['tsDate']))==$tsDate) {
				$arrLog[] = substr($val['tsTime'],0,2) . ":" .substr($val['tsTime'],2,2);
				$ctr++;
			}
			$tmp_empNo = $val['empNo'];
		}
		if ($arrTS['crossDay']=='Y' || $this->checkOTCrossDate($tsDate,$empNo)) {
			$newLog = array();
			for($i=0;$i<count($arrLog);$i++) {
				if ($i>0)
					$newLog[]=$arrLog[$i];
			}
			unset($arrLog);
			$arrLog = $newLog;

		}
*/
		if($arrTS['dayType']=='01') 
		{//regular day
				
			if ($arrTS['brnCode'] == '0001') 
			{//Head Office
				if ($this->IsLeaveAppType($arrTS['tsAppTypeCd'])) 
				{
					
					if(in_array($arrTS['tsAppTypeCd'],array(12,13,14,15))) 
					{//halfday leave
						
						if (!in_array($ctr,array(2,4)))
							$checkTag = ",checkTag='Y'";
					} 
					else 
					{//whole day leave
						if ($ctr != 0) 
							$checkTag = ",checkTag='Y'";
					}
				} 
				else	 
				{
					$timeOut = (float)str_replace(":",".",$arrLog[$ctr-1]);
					$schedtimeOut = (float)str_replace(":",".",$this->gettimeOut($arrTS));
					$utCH = true;
					if ($timeOut<$schedtimeOut && $arrTS['otCrossTag']!='Y') 
					{//check if w/ UT Application
						$utCH = false;
						foreach($this->arrUTEmpList as $valUT) 
						{
							if ($valUT['empNo']==$empNo && date('Y-m-d',strtotime($valUT['utDate']))==$tsDate) {// w/ UT Applicaton
								$utTimeOut = (float)str_replace(":",".",$valUT['utTimeOut']);
								if ($utTimeOut < $timeOut) {
									$timeOut = $utTimeOut;
									$timeOutUT = ",timeOut='".str_replace(".",":",number_format($timeOut,2))."'";
								}
								$utCH = true;
								unset($utTimeOut);
							}
						}
					}


					if (!in_array($ctr,array(2,4,6)) || $utCH == false)  
					{

						$checkTag = ",checkTag='Y'";
						if ($arrTS['obTag']=='') 
						{
							if ($ctr>0) {
								$checkTag = ",checkTag='Y'";
							} else {
								if ((float)str_replace(":",".",$arrTS['shftLunchOut'])!=0 && (float)str_replace(":",".",$arrTS['shftTimeOut'])==0) 
								{
									if ($ctr==2)
										$checkTag = "";
								} 
							}
						} 
						else 
						{
							$checkTag = "";
						}
					}
					else
					{
						if ($ctr == 2)
							$this->AddViolation($empNo,$tsDate,12);
					}
				}

			} 
			else 
			{ //Store
				
				if ($this->IsLeaveAppType($arrTS['tsAppTypeCd'])) 
				{
					
					if(in_array($arrTS['tsAppTypeCd'],array(12,13,14,15))) 
					{//halfday leave
						if (!in_array($ctr,array(2,4)))
							$checkTag = ",checkTag='Y'";
					} else 
					{//whole day leave
						
						if ($ctr != 0) 
							$checkTag = ",checkTag='Y'";
					}
				} 
				else 
				{
					
					$timeOut = (float)str_replace(":",".",$arrLog[$ctr-1]);
					$schedtimeOut = (float)str_replace(":",".",$this->gettimeOut($arrTS));
					$utCH = true;
					if ($timeOut<$schedtimeOut && $arrTS['otCrossTag']!='Y') 
					{//check if w/ UT Application
						$utCH = false;
						foreach($this->arrUTEmpList as $valUT) 
						{
							//echo "{$valUT['empNo']}==$empNo && {$valUT['utDate']}==$tsDate \n";
							if ($valUT['empNo']==$empNo && date('Y-m-d',strtotime($valUT['utDate']))==$tsDate) {// w/ UT Applicaton
								$utTimeOut = (float)str_replace(":",".",$valUT['utTimeOut']);
								if ($utTimeOut < $timeOut) {
									$timeOut = $utTimeOut;
									$timeOutUT = ",timeOut='".str_replace(".",":",number_format($timeOut,2))."'";
								}
								$utCH = true;
								unset($utTimeOut);
							}
						}
						
					}
					if (!in_array($ctr,array(6,4,2)) || $utCH == false) 
					{
						$checkTag = ",checkTag='Y'";
						if ($arrTS['obTag']=='Y')
						{
							if ($ctr>0) {
								$checkTag = ",checkTag='Y'";
							} else {
								if (in_array($ctr,array(2,4,6)))
									$checkTag = "";
							}
						}					
					}
					else
					{
						if ($ctr == 2)
							$this->AddViolation($empNo,$tsDate,12);
					}

					
					
				}
								
			}
			
		} 
		else 
		{
			//check if w/OT Application
			$withOT=false;
			foreach($this->arrOTList as $valOT) 
			{
				if ($valOT['empNo']==$empNo && date('Y-m-d',strtotime($valOT['otDate']))==$tsDate) 
				{
					$withOT=true;
				}
			}
			if ($withOT == true) 
			{
				if (!in_array($ctr,array(2,4,6)))
					$checkTag = ",checkTag='Y'";
			} 
			else 
			{
				if ($arrTS['empBrnCode'] !='0001' && $arrTS['empDiv']==7) 
				//if (in_array($arrTS['empDiv'], array('7'))) 
				{
					if (in_array($arrTS['dayType'],array('02','05','06','08'))) 
					{ 
						if ($ctr!=0)
							$checkTag = ",checkTag='Y'";
					} 
					else 
					{
						if ($ctr!=0) 
						{
							$otOut = $arrLog[$ctr-1];
							$otOut = ((float)str_replace(":",".",$otOut)<(float)str_replace(":",".",$arrTS['shftTimeOut'])) ? $otOut:$arrTS['shftTimeOut'];
							$oTIn = $arrLog[0];
							$oTIn = ((float)str_replace(":",".",$oTIn)<(float)str_replace(":",".",$arrTS['shftTimeIn'])) ? $arrTS['shftTimeIn']:$oTIn;

							$oTcrossDay = $arrTS['crossDay']; 
							$appTag = ",otIn='$oTIn',otOut='$otOut',otCrossTag='$oTcrossDay'";
						}
					}
				} 
				else 
				{
					if ($arrTS['dayType'] !='04') 
					{
						if ($ctr!=0)
							$checkTag = ",checkTag='Y'";
					} 
					else 
					{
						if ($arrTS['empPayType']=='D') 
						{
							if ($ctr!=0) 
							{
								//$appTag = ",otIn='{$arrLog[0]}',otOut='{$arrLog[$ctr-1]}'";					
							}
						} 
						else 
						{
							if ($ctr!=0)
								$checkTag = ",checkTag='Y'";
						}
					}
				}
			}	
		}

		$arrQry = array();
		if ($ctr>0) 
		{
				if($ctr>6)
					$logsExceededTag = ", logsExceeded='Y'";
					
					
				if ($arrTS['obTag']=='') 
				{
						
					if ($timeOutUT == "")	{
						$arrQry[] = "Update tblTK_Timesheet Set empNo='$empNo' $checkTag " . $this->ProcessEventLog($ctr,$arrLog,$arrTS['tsAppTypeCd'],$arrTS)." $dayTypeField $appTag $logsExceededTag where empNo='$empNo' and compCode='{$_SESSION['company_code']}' and tsDate='$tsDate';";
						//$arrQry[] = "Update tblTK_EventLogs set ProcessTag='Y' where cStoreNum='{$arrTS['empBrnCode']}' AND ETAG = '{$arrTS['bioNo']}' AND EDATE='".date('Ymd',strtotime($tsDate))."'";
						//$arrQry[] = "Update tblTK_EventLogs set ProcessTag='Y' where ETAG = '{$arrTS['bioNo']}' AND EDATE='".date('Ymd',strtotime($tsDate))."'";
						return $arrQry;
					} else {
						$arrQry[] = "Update tblTK_Timesheet Set empNo='$empNo' $checkTag " . $this->ProcessEventLog($ctr,$arrLog,$arrTS['tsAppTypeCd'],$arrTS)." $dayTypeField $appTag $logsExceededTag where empNo='$empNo' and compCode='{$_SESSION['company_code']}' and tsDate='$tsDate';";
						$arrQry[] = "Update tblTK_Timesheet Set empNo='$empNo' $timeOutUT where empNo='$empNo' and compCode='{$_SESSION['company_code']}' and tsDate='$tsDate';";
						//$arrQry[] = "Update tblTK_EventLogs set ProcessTag='Y' where cStoreNum='{$arrTS['empBrnCode']}' AND ETAG = '{$arrTS['bioNo']}' AND EDATE='".date('Ymd',strtotime($tsDate))."';";
						//$arrQry[] = "Update tblTK_EventLogs set ProcessTag='Y' where ETAG = '{$arrTS['bioNo']}' AND EDATE='".date('Ymd',strtotime($tsDate))."';";
						return $arrQry;
					}
				} 
				else 
				{
					$timeInOB = ((float)str_replace(":",".",$arrTS['timeIn'])<(float)str_replace(":",".",$arrLog[0])) ? "":",timeIn='{$arrLog[0]}'"; 
					if ($ctr>1)
						$timeOutOB = ((float)str_replace(":",".",$arrTS['timeOut'])>(float)str_replace(":",".",$arrLog[$ctr-1])) ? "":",timeOut='".$arrLog[$ctr-1]."'"; 

					$arrQry[] = "Update tblTK_Timesheet Set empNo='$empNo' $checkTag $timeInOB $timeOutOB $dayTypeField $appTag $logsExceededTag where empNo='$empNo' and compCode='{$_SESSION['company_code']}' and tsDate='$tsDate';";
					//$arrQry[] = "Update tblTK_EventLogs set ProcessTag='Y' where cStoreNum='{$arrTS['empBrnCode']}' AND ETAG = '{$arrTS['bioNo']}' AND EDATE='".date('Ymd',strtotime($tsDate))."'";
					//$arrQry[] = "Update tblTK_EventLogs set ProcessTag='Y' where ETAG = '{$arrTS['bioNo']}' AND EDATE='".date('Ymd',strtotime($tsDate))."'";
					return $arrQry;
				}
				
				
				
		} 
		else 
		{
			$arrQry[] = "Update tblTK_Timesheet Set empNo='$empNo' $dayTypeField $checkTag  where empNo='$empNo' and compCode='{$_SESSION['company_code']}' and tsDate='$tsDate';";
			return $arrQry;
		}
	}
	
	function getholiday() {
		$sqlHoliday = "SELECT holidayDate, brnCode, dayType from tblHolidayCalendar where compCode='{$_SESSION['company_code']}' AND holidayStat='A' AND holidayDate BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}'";
		$this->arrHolidays = $this->getArrResI($this->execQryI($sqlHoliday));		
	}
	function checkHolidayDate($tsDate,$RDTag,$brnCode,$empdiv) {
		// $yr=date('Y',strtotime($tsDate));
		
		// 	if ($RDTag=='Y') 
		// 	$dayType='02';
		// 			else
		// 	$dayType='01';
		
		// foreach($this->arrHolidays as $valHol) {
			
			
		// 	///old code
			
			
			if ($RDTag=='Y') 
			$dayType='02';
		else
			$dayType='01';
		
		foreach($this->arrHolidays as $valHol) {
			if (date('Y-m-d',strtotime($valHol['holidayDate'])) == date('Y-m-d',strtotime($tsDate)) && ($valHol['brnCode'] =='0' || $valHol['brnCode']==$brnCode)) {
				if ($RDTag=="Y") 
				{
					if ($valHol['dayType']=='03')
						$dayType='05';
					elseif ($valHol['dayType']=='04')
						$dayType='06';
						elseif ($valHol['dayType']=='07')
		 				$dayType='08';
				} 
				else 
				{


			
			$qcday='2022-08-19';

			if(date('Y-m-d',strtotime($valHol['holidayDate'])) == date('Y-m-d',strtotime($qcday)) && $empdiv == '6' || date('Y-m-d',strtotime($qcday)) == date('Y-m-d',strtotime($tsDate)) && $empdiv == '6' ){
				if ($RDTag=="Y") {
					$dayType='02';
				}else{
					$dayType='01';
				}
			}else{
				$dayType=$valHol['dayType'];
			}
					
				}
			}
		}
		return $dayType;
			
	
			
			
			
			
			
						
			///old code
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		// 	$qcday='2021-08-19';

		// 	if(date('Y-m-d',strtotime($valHol['holidayDate'])) == date('Y-m-d',strtotime($qcday)) && $empdiv == '6' || date('Y-m-d',strtotime($qcday)) == date('Y-m-d',strtotime($tsDate)) && $empdiv == '6' ){
		// 		if ($RDTag=="Y") {
		// 			$dayType='02';
		// 		}else{
		// 			$dayType='01';
		// 		}
		// 	}else{

		
		// 	if (date('Y-m-d',strtotime($valHol['holidayDate'])) == date('Y-m-d',strtotime($tsDate)) && ($valHol['brnCode'] =='0' || $valHol['brnCode']==$brnCode)) 
		// 	{
		// 		if ($RDTag=="Y") 
		// 		{
		// 			if(	$qcday == date('Y-m-d',strtotime($tsDate)) && $empdiv == '6' ){
		// 				$dayType='02';
		// 				}else{
					
		// 			if ($valHol['dayType']=='03')
		// 				$dayType='05';
		// 			elseif ($valHol['dayType']=='04')
		// 				$dayType='06';
		// 			elseif ($valHol['dayType']=='07')
		// 				$dayType='08';}
		// 		} 
		// 		else 
		// 		{
		// 			if(	date('Y-m-d',strtotime($qcday)) == date('Y-m-d',strtotime($tsDate)) && $empdiv == '6' ){
		// 				$dayType='01';
		// 			}
		// 			else{
		// 			$dtnew=$valHol['dayType'];
		// 			$dayType=$dtnew;

		// 				}
		// 		}
		// 	//}
		// 	//for head office  date('Y-m-d',strtotime($qcday))
		// 	}else {
		// 		if (date('Y-m-d',strtotime($valHol['holidayDate'])) == date('Y-m-d',strtotime($tsDate)) && $empdiv == '6' && $brnCode=='0001'  && ($valHol['brnCode'] =='0001')) 
		// 	{
		// 		if ($RDTag=="Y") 
		// 		{
		// 			if(	$qcday == date('Y-m-d',strtotime($tsDate)) && $empdiv == '6' ){
		// 				$dayType='02';
		// 				}else{
					
		// 			if ($valHol['dayType']=='03')
		// 				$dayType='05';
		// 			elseif ($valHol['dayType']=='04')
		// 				$dayType='06';
		// 			elseif ($valHol['dayType']=='07')
		// 				$dayType='08';
		// 			}
		// 		} 
		// 		else 
		// 		{
		// 			if(date('Y-m-d',strtotime($qcday)) == date('Y-m-d',strtotime($tsDate)) && $empdiv == '6' ){
		// 			$dayType='01';

		// 		}else{

		// 			$dtypenew=$valHol['dayType'];
		// 			$dayType=$dtypenew;
					
		// 		}
		// 		}
		// 	} 
		// }
		//   }
		// }
		// return $dayType;
	}
	
	function getOTList() {
		$sqlOT = "SELECT otDate, empNo, otIn, otOut,crossTag FROM tblTK_OTApp 
						where compCode='{$_SESSION['company_code']}' AND otStat='A' AND otDate BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}'
							AND empNo IN (Select empNo from tblEmpMast where empPayGrp='{$this->Group}' AND compCode='{$_SESSION['company_code']}' 
											AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y'))						
						order by tblTK_OTApp.empNo,otDate";
		$this->arrOTList = $this->getArrResI($this->execQryI($sqlOT));
	}
	
	function getTSCorrectionList() {
		$sqlCorr = "SELECT     tblTK_TimeSheetCorr.compCode, tblTK_TimeSheetCorr.empNo, tblTK_TimeSheetCorr.tsDate, 
                      tblTK_TimeSheetCorr.timeIn AS cor_timeIn, tblTK_TimeSheetCorr.lunchOut AS cor_lunchOut, 
                      tblTK_TimeSheetCorr.lunchIn AS cor_lunchIn, tblTK_TimeSheetCorr.breakOut AS cor_breakOut, 
                      tblTK_TimeSheetCorr.breakIn AS cor_breakIn, tblTK_TimeSheetCorr.timeOut AS cor_timeOut,tblTK_TimeSheetCorr.crossTag as cor_crossTag,crossDay,
                      tblTK_Timesheet.timeIn, tblTK_Timesheet.lunchOut, tblTK_Timesheet.lunchIn, tblTK_Timesheet.breakOut, tblTK_Timesheet.breakIn, 
                      tblTK_Timesheet.timeOut, tblTK_TimeSheetCorr.editReason
FROM         tblTK_TimeSheetCorr INNER JOIN
                      tblTK_Timesheet ON tblTK_TimeSheetCorr.compCode = tblTK_Timesheet.compcode AND 
                      tblTK_TimeSheetCorr.empNo = tblTK_Timesheet.empNo AND tblTK_TimeSheetCorr.tsDate = tblTK_Timesheet.tsDate where tblTK_TimeSheetCorr.compCode='{$_SESSION['company_code']}' AND stat='A' AND tblTK_TimeSheetCorr.tsDate BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}'
						AND tblTK_TimeSheetCorr.empNo IN (Select empNo from tblEmpMast where empPayGrp='{$this->Group}' AND compCode='{$_SESSION['company_code']}' 
											AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y'))	
					 order by tblTK_TimeSheetCorr.empNo,tblTK_TimeSheetCorr.tsDate";
		$this->arrCorrList = $this->getArrResI($this->execQryI($sqlCorr));
	
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
		return date('Y-m-d',strtotime("$month/$day/$year"));
	}
	
	function getUTExemptEmp() {
		$sqlUTExempt = "SELECT tblEmpMast.empNo FROM tblTK_RankLevelTimeExempt INNER JOIN tblEmpMast ON tblTK_RankLevelTimeExempt.compCode = tblEmpMast.compCode AND tblTK_RankLevelTimeExempt.exemptLevelCd = tblEmpMast.empLevel AND  tblTK_RankLevelTimeExempt.exemptRankCd = tblEmpMast.empRank
						WHERE (tblTK_RankLevelTimeExempt.utHrsExempt = 'Y') AND tblTK_RankLevelTimeExempt.compCode='{$_SESSION['company_code']}'";
		$this->arrUTExempEmpList = $this->getArrResI($this->execQryI($sqlUTExempt));				
	}
	
	function checkUTExemp($empNo) {
		$res = false;
		foreach($this->arrUTExempEmpList as $valUT) {
			if ($empNo==$valUT['empNo']) {
				$res = true;
			}
		}
		return $res;
	}
	function getLeaveAppTypes() {
		$sqlLeaveAppTypes = "Select tsAppTypeCd from tblTK_AppTypes where compCode='{$_SESSION['company_code']}' AND leaveTag='Y'";
		$this->arrLeaveAppTypes = $this->getArrResI($this->execQryI($sqlLeaveAppTypes));
	}
	
	function IsLeaveAppType($AppType) {
		$res = false;
		foreach($this->arrLeaveAppTypes as $valType) {
			if ($AppType==$valType['tsAppTypeCd']) {
				$res = true;
			}
		}
		return $res;
	}
	function gettimeOut($arr) {
		if ((float)str_replace(":",".",$arr['shftTimeOut'])!=0) {
			$timeOut = $arr['shftTimeOut'];
		} else  {
			$timeOut = $arr['shftLunchOut'];
		}
		return $timeOut;
	}
	function getUTEmpList() {
		$sqlUTlist = "SELECT empNo, offTimeOut, utTimeOut, utDate FROM tblTK_UTApp Where utStat='A' AND compCode='{$_SESSION['company_code']}' AND utDate BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}' 
						AND empNo IN (Select empNo from tblEmpMast where empPayGrp='{$this->Group}' AND compCode='{$_SESSION['company_code']}' 
											AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y'))
					  order by utDate,empNo;\n
					";
		$this->arrUTEmpList = $this->getArrResI($this->execQryI($sqlUTlist));			
	}
	
	function valLeaveType($arrLv) {
		$appType = $arrLv['tsAppTypeCd'];
		
		if ($arrLv['lvDateFrom']==$arrLv['tsDate'] || $arrLv['lvDateTo']==$arrLv['tsDate']) {
			if ($arrLv['lvDateFrom']==$arrLv['tsDate']) 
				$LeaveAMPMTag = $arrLv['lvFromAMPM'];
			else
				$LeaveAMPMTag = $arrLv['lvToAMPM'];

			if (in_array($arrLv['tsAppTypeCd'],array(14,15,16,17,19,20))) {// leaves w/o pay
				switch($LeaveAMPMTag) {
					case "AM":
						$appType = 14; //half day leave AM w/o pay
					break;
					case "PM":
						$appType = 15; //half day leave PM w/o pay
					break;
				}
			} else { //leave w/ pay                                                     
				switch($LeaveAMPMTag) {
					case "AM":
						$appType = 12; //half day leave AM w/ pay
					break;
					case "PM":
						$appType = 13; //half day leave PM w/ pay
					break;
				}
			}
		}
		return $appType;
	}
	function resetCheckTag() {
		$sqlUpdateCT = " Update tblTK_Timesheet set checkTag=NULL,tsAppTypeCd=NULL,otIn=NULL,otOut=NULL,otCrossTag=NULL,hrsRequired=NULL,hrsWorked=NULL,legalPayTag=NULL,attendType=NULL,dedTag=NULL,otTag=NULL,hrs8Deduct=NULL,csTag=NULL,obTag=NULL,crossDay=NULL,satPayTag=NULL
							WHERE tblTK_Timesheet.compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND empNo IN ( Select empNo from tblEmpMast where empPayGrp='{$this->Group}' AND compCode=  '{$_SESSION['company_code']}' AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y'))		
		";
		return $this->execQryI($sqlUpdateCT);
	}
		function resetTKlogs() {
		$sqlUpdateTK = " Update tblTK_Timesheet set TimeIn=NULL,lunchOut=NULL,lunchIn=NULL,breakOut=NULL,breakIn=NULL,otOut=NULL,otIn=NULL,timeOut=NULL
							WHERE tblTK_Timesheet.compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND empNo IN ( Select empNo from tblEmpMast where empPayGrp='{$this->Group}' AND compCode=  '{$_SESSION['company_code']}' AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y'))		
		";
		return $this->execQryI($sqlUpdateTK);
	}
	
	function checkOTCrossDate($tsDate,$empNo) {
		$res = false;
		foreach($this->arrOTList as $val) {
			if ($this->DateAdd($val['otDate'])==$tsDate && $val['empNo']==$empNo && $val['crossTag']=='Y') {
				$res = true;
			}			
		}
		return $res;
	}
	
	function UnpostedRD() {
		$sqlRD = "SELECT tblTK_EmpShift.shiftCode, tblEmpMast.empBrnCode AS brnCode, tblTK_UnpostedCRD.empNo, tblTK_UnpostedCRD.cRDDateFrom, tblTK_UnpostedCRD.cRDDateTo FROM tblTK_EmpShift INNER JOIN
                      tblTK_UnpostedCRD ON tblTK_EmpShift.compCode = tblTK_UnpostedCRD.compCode AND 
                      tblTK_EmpShift.empNo = tblTK_UnpostedCRD.empNo INNER JOIN
                      tblEmpMast ON tblTK_UnpostedCRD.compCode = tblEmpMast.compCode AND tblTK_UnpostedCRD.empNo = tblEmpMast.empNo
				  where tblTK_UnpostedCRD.compCode='{$_SESSION['company_code']}' AND cRDStat='A' AND cRDDateFrom BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}' AND empPayGrp='{$this->Group}'
				  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')
				  ";
		return $this->getArrResI($this->execQryI($sqlRD));
	}
	function ClearDeductions() {
		$sqlClearDeductions = " Delete from tblTK_Deductions where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' 
						AND empNo IN (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
							AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')); \n";
		return $this->execQryI($sqlClearDeductions);
	}

	function clearOvertime() {
		$sqlClearOvertime = "Delete from tblTK_Overtime where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' 
						AND empNo IN (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
							AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y'));";
		return $this->execQryI($sqlClearOvertime);
	}

	function clearViolations() {
		$sqlClearViolations = " Delete from tblTK_EmpViolations where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' 
						AND empNo IN (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
							AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')) AND process='Posting';";							
		return $this->execQryI($sqlClearViolations);
	}
	function CountErrorTag() {
		$sqlError = "SELECT empNo from tblTK_Timesheet where compCode='{$_SESSION['company_code']}' AND empNo IN (SELECT empNo FROM tblEmpmast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}'
							  	AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y')) AND checkTag='Y'";
		 return $this->getRecCountI($this->execQryI($sqlError));
	}
	function AddViolation($empNo,$tsDate,$violationCode) {
		$proc = "Processing";
		$sqlAddViolation = "Insert into tblTK_EmpViolations ( compCode, empNo, violationCd, tsDate, dateAdded,process) values ('{$_SESSION['company_code']}','$empNo','$violationCode','$tsDate','".date('m-d-Y')."','$proc')";
		$this->execQryI($sqlAddViolation);	
	}	
	
	function copySched($tsFrom,$tsTo,$empNo) {
		$tsFrom = date('Y-m-d',strtotime($tsFrom));
		$tsTo = date('Y-m-d',strtotime($tsTo));
		$sqlcopySched = "SELECT  shftTimeIn, shftLunchOut, shftLunchIn, shftBreakOut, shftBreakIn, shftTimeOut FROM tblTK_Timesheet WHERE     (dayType = '01') AND (cast(tsDate as date) BETWEEN '$tsFrom' AND '$tsTo') AND (empNo = '$empNo') AND (shftTimeIn <> '00:00') AND (shftTimeOut <> '00:00') limit 01";

		return $this->getSqlAssocI($this->execQryI($sqlcopySched));	
	}
	
	function setUserBranch(){
		$this->execQryI("Update tblTK_UserBranch set processTag=Null where empNo='".$_SESSION['employee_number']."'");
		for($i=0;$i<=(int)$_GET['chCtr'];$i++) {
			if ($_GET["chkBrnCode$i"] !="") {
				$arrStr = $_GET["chkBrnCode$i"];
				$qry = "Update tblTK_UserBranch set processTag='Y' where brnCode='".$arrStr."' and empNo='".$_SESSION['employee_number']."';";
				$this->execQryI($qry);
			}
			if ($val['my_rDate'] == '') {
				
			}
		}
	}
	
	function ProcessLogs() {
	//{$this->Group},'{$this->pdFrom}','{$this->pdTo}',{$_SESSION['company_code']},'{$_SESSION['employee_number']}'		
		 $sqlProcessLogs = "SELECT tblTK_Timesheet.compCode,tblTK_Timesheet.empNo, tblTK_Timesheet.tsDate,otCrossTag,crossDay,empbrnCode,bioNo,dayType,tsAppTypeCd, obTag, case dateResigned when null then endDate else  dateResigned end as rDate,tblEmpmast.empDiv as empdiv
						  FROM tblTK_Timesheet INNER JOIN
	                      tblEmpMast ON tblTK_Timesheet.compcode = tblEmpMast.compCode AND tblTK_Timesheet.empNo = tblEmpMast.empNo
						  WHERE tblTK_Timesheet.compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND  empPayGrp= {$this->Group} 
						  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')  
						  order by tblTK_Timesheet.empNo,tsDate";
		$arrProcLogs = $this->getArrResI($this->execQryI($sqlProcessLogs));
		$temp_empNo ='';
		$this->createTempTables();
		$this->transferLogs();
		foreach ($arrProcLogs as $val) {
			if ($temp_empNo != $val['empNo']) {
				$this->getEmpLogs($val['empNo'],$val['bioNo'],$val['empbrnCode']);
				$day1 = true;
			}
			
			$temp_empNo = $val['empNo'];
			$hist = ($day1) ? "hist":"";
			$arrPrev = $this->checkifPrevDateisCrossDay($val['empNo'],date('Y-m-d',strtotime($val['tsDate'])),$hist);
			$qryLogs 	= $this->getDayLogs(date('Ymd',strtotime($val['tsDate'])),$val['empNo']);
			$ctrAll = $ctr 	= $this->getRecCountI($qryLogs);
			$arrLogs 	= $this->getArrResI($qryLogs);
	
			$ch = 0;
			if ($val['otCrossTag']=='Y' || $val['crossDay']=='Y') {
				$qryPrevLogs = $this->getPrevDayLogs(date('Ymd',strtotime($val['tsDate'])),$val['empNo']);
				$arrPrevLogs = $this->getSqlAssocI($qryPrevLogs);
				if ($this->getRecCountI($qryPrevLogs)>0) {
					if ($arrPrev['otCrossTag']=='Y' || $arrPrev['crossDay']=='Y') {
						$ch = 1;
						$ctr++;
						if ($ctr==2)
							$Log2 = $arrPrevLogs['ETIME']; 
						elseif ($ctr==3)
							$Log3 = $arrPrevLogs['ETIME']; 
						elseif ($ctr==4)
							$Log4 = $arrPrevLogs['ETIME']; 
						elseif ($ctr==5)
							$Log5 = $arrPrevLogs['ETIME']; 
						elseif ($ctr==6)
							$Log6 = $arrPrevLogs['ETIME'];           
						elseif ($ctr==7)
							$Log7 = $arrPrevLogs['ETIME'];           
						elseif ($ctr==8)
							$Log8 = $arrPrevLogs['ETIME'];
				 	} else {
						$ch = 1;
						$ctr = $ctr+1;
						$No = $ctr;
						if ($ctr==2)
							$Log2 = $arrPrevLogs['ETIME']; 
						elseif ($ctr==3)
							$Log3 = $arrPrevLogs['ETIME']; 
						elseif ($ctr==4)
							$Log4 = $arrPrevLogs['ETIME']; 
						elseif ($ctr==5)
							$Log5 =$arrPrevLogs['ETIME']; 
						elseif ($ctr==6)
							$Log6 = $arrPrevLogs['ETIME'];
						elseif ($ctr==7)
							$Log7 = $arrPrevLogs['ETIME'];           
						elseif ( $ctr==8)
							$Log8 = $arrPrevLogs['ETIME'];
						
						$ctr = $ctr-1;						
					}
				}
			}
		
			foreach($arrLogs as $valLogs) {
				if ($val['otCrossTag']=='Y' || $val['crossDay']=='Y') {
					$ch = 1;
					if ($ctr==2)
						$Log1 = $valLogs['ETIME'];
					elseif ($ctr==3)
						$Log2 = $valLogs['ETIME']; 
					elseif ($ctr==4)
						$Log3 = $valLogs['ETIME']; 
					elseif ($ctr==5)
						$Log4 = $valLogs['ETIME']; 
					elseif ($ctr==6)
						$Log5== $valLogs['ETIME']; 
					elseif ($ctr==7)
						$Log6 = $valLogs['ETIME']; 
					elseif ($ctr==8)
						$Log7 = $valLogs['ETIME']; 
				} else {
					$ch = 1;
					if($ctr==1)
						$Log1 = $valLogs['ETIME']; 
					elseif ($ctr==2)
						$Log2 = $valLogs['ETIME']; 
					elseif ($ctr==3)
						$Log3 = $valLogs['ETIME']; 
					elseif ($ctr==4)
						$Log4 = $valLogs['ETIME']; 
					elseif ($ctr==5)
						$Log5 = $valLogs['ETIME']; 
					elseif ($ctr==6)
						$Log6 = $valLogs['ETIME']; 
					elseif ($ctr==7)
						$Log7 = $valLogs['ETIME']; 
					elseif ($ctr==8)
						$Log8 = $valLogs['ETIME']; 
				}
				$ctr--;				
			}
			if ($arrPrev['otCrossTag']=='Y') 
				$ctrAll = $ctrAll - 1;


			if ($ctrAll>0 && $ch==1) {
				 $sqlInsertLogs = "INSERT INTO TK_Logs (compCode,empNo,Log1,Log2,Log3,Log4,Log5,Log6,Log7,Log8,NoLogs,tsDate) 
				 SELECT '{$val['compCode']}','{$val['empNo']}', '$Log1' ,'$Log2' ,'$Log3' ,'$Log4' ,'$Log5' ,'$Log6', '$Log7' ,'$Log8', $ctrAll, '{$val['tsDate']}';\n";	
				$this->execQryI($sqlInsertLogs);
			} else {
				$sqlQryCheck = "Select count(tsAppTypeCd) from tblTK_AppTypes where  leaveTag = 'Y' AND leaveTypeTag = 'Y' and tsAppTypeCd='{$val['tsAppTypeCd']}'";
				if ($val['dayType']=='01' && $val['my_obTag']=='' && $this->getRecCountI($sqlQryCheck)==0)
					$this->execQryI("UPDATE tblTK_Timesheet SET checkTag='Y' WHERE empNo='{$val['empNo']}' AND tsDate='{$val['tsDate']}';");

				if ($val['dayType']=='02')
					$this->execQryI("UPDATE tblTK_Timesheet SET checkTag='Y' WHERE empNo='{$val['empNo']}' AND tsDate='{$val['tsDate']}' and otIn is not null and otOut is not null;");

			}		
			$day1 = false;
		}
		$sqlFinalResult = "
		SELECT  tblTK_Timesheet.empNo, tblTK_Timesheet.tsDate, tblTK_Timesheet.bioNo, tblTK_Timesheet.dayType, tblTK_Timesheet.shftTimeIn, 
                     tblTK_Timesheet.shftLunchOut, tblTK_Timesheet.shftLunchIn, tblTK_Timesheet.shftBreakOut, tblTK_Timesheet.shftBreakIn, 
                     tblTK_Timesheet.shftTimeOut, tblTK_Timesheet.tsAppTypeCd, tblTK_Timesheet.timeIn, tblTK_Timesheet.lunchOut, tblTK_Timesheet.lunchIn, 
                     tblTK_Timesheet.breakOut, tblTK_Timesheet.breakIn, tblTK_Timesheet.timeOut, tblTK_Timesheet.otIn, tblTK_Timesheet.otOut, 
                     tblTK_Timesheet.attendType, tblTK_Timesheet.brnchCd AS brnCode, tblEmpMast.empPayType,empDiv,empBrnCode,otCrossTag,crossDay, Log1,Log2,Log3,Log4,Log5,Log6,Log7,Log8,NoLogs 
	        FROM TK_Logs INNER JOIN tblTK_Timesheet ON TK_Logs.empNo =  tblTK_Timesheet.empNo AND TK_Logs.tsDate = tblTK_Timesheet.tsDate AND TK_Logs.compCode = tblTK_Timesheet.compCode
	       INNER JOIN tblEmpMast ON TK_Logs.empNo =  tblEmpMast.empNo AND TK_Logs.compCode = tblEmpMast.compCode  ;		
		";
		$res =  $this->getArrResI($this->execQryI($sqlFinalResult));
		return $res;
	}
	function checkifPrevDateisCrossDay($empNo,$tsDate,$hist) {
		$sql = "SELECT otCrossTag,crossDay  FROM tblTK_Timesheet$hist  WHERE  tsDate =DATE_ADD('$tsDate', INTERVAL -1 DAY)  AND empNo='$empNo'";	
		return $this->getSqlAssocI($this->execQryI($sql));
	}


function createTempTables1111() {
	
		$sql = "Truncate table tbltk_translogs";
		$this->execQryI($sql);

		$sql = "Truncate table TK_Timesheet";
		$this->execQryI($sql);

		$sql = "Truncate table TK_Logs";
		$this->execQryI($sql);	

		$sql = "Truncate table tk_eventlogs";
		$this->execQryI($sql);		

	}

	
	function createTempTables() {
		$sql = "drop table tbltk_translogs";
		$this->execQryI($sql);
		$sql ="
			Create  TABLE tbltk_translogs (
			  empNo varchar(12) DEFAULT NULL,
			  tsDate datetime DEFAULT NULL,
			  ETIME varchar(6) DEFAULT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;		
			";	
		$this->execQryI($sql);
		$sql = "drop table TK_Timesheet";
		$this->execQryI($sql);
		$sql ="
			Create   TABLE TK_Timesheet (
			tsID bigint UNSIGNED NOT NULL AUTO_INCREMENT,
			empNo varchar(12),
			tsdate datetime,
			otCrossTag varchar(2) NULL,
			crossDay varchar(2) NULL,
			brnCode int NULL,
			bio int NULL,
			dayType varchar(2),
			tsAppTypeCd varchar(2) NULL,
			obTag varchar(2) NULL,
			rDate datetime NULL,
			PRIMARY KEY ( tsID ),
			UNIQUE KEY `tsdate` (`tsdate`,`empNo`)
			 );";	
		$this->execQryI($sql);
		$sql = "drop table TK_Logs";
		$this->execQryI($sql);
		$sql ="
			Create   TABLE TK_Logs (
			LogID bigint UNSIGNED NOT NULL AUTO_INCREMENT,
			compCode varchar(2),
			empNo varchar(12),
			tsdate datetime,
			Log1 varchar(6) NULL,
			Log2 varchar(6) NULL,
			Log3 varchar(6) NULL,
			Log4 varchar(6) NULL,
			Log5 varchar(6) NULL,
			Log6 varchar(6) NULL,
			Log7 varchar(6) NULL,
			Log8 varchar(6) NULL,
			NoLogs int NULL,
			PRIMARY KEY ( LogID )
			 );";	
		$this->execQryI($sql);
		$sql = "drop table tk_eventlogs";
		$this->execQryI($sql);		
		$sql = "
			Create  TABLE `tk_eventlogs` (
			  `cStoreNum` varchar(5) DEFAULT NULL,
			  `EDATE` varchar(50) DEFAULT NULL,
			  `ETIME` varchar(50) DEFAULT NULL,
			  `EDOOR` varchar(50) DEFAULT NULL,
			  `EFLOOR` varchar(50) DEFAULT NULL,
			  `ESABUN` varchar(50) DEFAULT NULL,
			  `ETAG` varchar(50) DEFAULT NULL,
			  `ENAME` varchar(50) DEFAULT NULL,
			  `ELNAME` varchar(50) DEFAULT NULL,
			  `EPART` varchar(50) DEFAULT NULL,
			  `EDEP` varchar(50) DEFAULT NULL,
			  `ESTATUS` varchar(50) DEFAULT NULL,
			  `EFUNCTION` varchar(50) DEFAULT NULL,
			  `EINOUT` varchar(10) DEFAULT NULL,
			   `id`  bigint(18) NOT NULL AUTO_INCREMENT,
			  `ProcessTag` varchar(1) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `EDATE` (`EDATE`,`ETIME`,`ETAG`)
			) ;
		
		";//ENGINE=InnoDB DEFAULT CHARSET=latin1
		$this->execQryI($sql);	
	}
	
	function getEmpLogs($empNo,$bioNo,$brnCode) {
		$sqlClearLogs = "truncate table tblTK_TransLogs";
		$this->execQryI($sqlClearLogs);
		//$sqlPopulateLogs = "INSERT INTO tblTK_TransLogs  (empNo,tsDate,ETIME) SELECT '$empNo',CAST(EDATE as datetime),concat(SUBSTRING(ETIME,1,2),':',SUBSTRING(ETIME,3,2)) from tk_eventlogs where cstorenum='$brnCode' AND cast(ETAG as UNSIGNED) = cast('$bioNo' as UNSIGNED) group by edate,etime order by edate,etime";
		$sqlPopulateLogs = "INSERT INTO tblTK_TransLogs  (empNo,tsDate,ETIME) SELECT '$empNo',CAST(EDATE as datetime),concat(SUBSTRING(ETIME,1,2),':',SUBSTRING(ETIME,3,2)) from tk_eventlogs where cast(ETAG as UNSIGNED) = cast('$bioNo' as UNSIGNED) group by edate,etime order by edate,etime";
		$this->execQryI($sqlPopulateLogs);
	}
	
	function getDayLogs($tsDate,$empNo) {
		$sqlDayLogs = "Select * from tblTK_TransLogs where empNo='$empNo' and tsDate='$tsDate' order by cast(etime as time) desc";
		return $this->execQryI($sqlDayLogs);
	}
	function getPrevDayLogs($tsDate,$empNo) {
		$sqlDayLogs = "Select * from tblTK_TransLogs where empNo='$empNo' and tsDate=DATE_ADD('$tsDate', INTERVAL 1 DAY) order by ETIME limit 1";
		return $this->execQryI($sqlDayLogs);
	}
	
	function transferLogs() {
	 $sqlTransferLogs = "Insert into tk_eventlogs (cStoreNum,EDATE,ETIME,EDOOR,EFLOOR,ESABUN,ETAG,ENAME,ELNAME,EPART,EDEP,ESTATUS,EFUNCTION,EINOUT) Select distinct cStoreNum,EDATE,ETIME,EDOOR,EFLOOR,ESABUN,ETAG,ENAME,ELNAME,EPART,EDEP,ESTATUS,EFUNCTION,EINOUT from tbltk_eventlogs where  edate>=date_add(cast('{$this->pdFrom}' as date), INTERVAL -1 day) group by cStoreNum,EDATE,ETIME,EDOOR,EFLOOR,ESABUN,ETAG,ENAME,ELNAME,EPART,EDEP,ESTATUS,EFUNCTION,EINOUT";	

		return $this->execQryI($sqlTransferLogs);
	}
}

?>
