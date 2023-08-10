<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheets_adjustments_obj.php");

$timesheetsadjustmentsObj = new timesheetAdjustmentsObj($_GET,$_SESSION);
$timesheetsadjustmentsObj->validateSessions('','MODULES');

if (isset($_GET['action'])) {
	
	switch ($_GET['action']) {
		
		//validate selected date from previous cut off
		case ('validatedaytype'):
			if($_GET['empno']==''){
				echo "$('".$_GET['id']."').value='';";
				echo "$('txtAddEmpNo').focus();";
				echo "alert('Please select employee!');";
				exit();	
			}	
			
			$tdate = date('Y-m-d',strtotime($_GET['tsdate']));
//			$period = $timesheetsadjustmentsObj->getOpenPeriod(" and compCode='".$_SESSION['company_code']."' AND payGrp='".$_GET['hdnPayGrp']."' AND payCat='".$_GET['hdnPayCat']."'");
//			$openperiod = $period['pdSeries'];
//			
//			$series = $timesheetsadjustmentsObj->getPaySeries(" and compCode='".$_SESSION['company_code']."' AND payGrp='".$_GET['hdnPayGrp']."' AND payCat='".$_GET['hdnPayCat']."' and '".$tdate."' between pdFrmDate and pdToDate");
//			$closeperiod = $series['pdSeries'];
//			
//			
//			$autorizedPeriod = (float)$openperiod - (float)$closeperiod;
			//echo $autorizedPeriod;
			$daytype = $timesheetsadjustmentsObj->getTimeSheetDayType(" Where empNo='{$_GET['empno']}' and tsDate='{$tdate}' and compcode='{$_SESSION['company_code']}'");
			$dcode = $daytype['dayType'];
			$pdseries = $daytype['pdSeries'];
			
			$dtypedesc = $timesheetsadjustmentsObj->getDayTypeDescArt($dcode);
			
			$RR = $timesheetsadjustmentsObj->getOpenPeriod(" and payCat='3' and payGrp='".$_GET['hdnPayGrp']."' and '".$tdate."' between pdFrmDate and pdToDate");
		
			if($dcode!=''){
				if($dcode=="01"){
					if($RR['pdNumber']!=""){
						
						echo "document.getElementById('imgCalendar').style.visibility='hidden';";	
						echo "document.getElementById('imgCalendarAmnt').style.visibility='visible';";	
						echo "$('cmbAdjustmentType').value='A';";
						echo "$('cmbAllowance').disabled=true;";
						echo "$('cmbAdvances').disabled=true;";
						echo "$('txtTSAmntDate').disabled=false;";
						echo "$('txtTSAmntDate').readOnly=true;";
						echo "$('txtBasicAmnt').disabled=false;";
						echo "$('txtOTAmnt').disabled=false;";
						echo "$('txtNDAmnt').disabled=false;";
						echo "$('txtHPAmnt').disabled=false;";
						echo "$('txtECOLAAmnt').disabled=false;";
						echo "$('txtCTPAAmnt').disabled=false;";
						echo "$('txtAdvancesAmnt').disabled=false;";
						echo "$('cmbStatAmnt').disabled=false;";
						echo "$('btnSaveAmnt').disabled=false;";
						echo "$('cmbStatAmnt').value='O';";
						
						echo "$('txtTSDate').value='';";
						echo "$('hdnDayType').value='';";
						echo "$('cmbDayType').value='';";
						echo "$('cmbDayType').disabled=true;";
						echo "$('txtHrsReg').disabled=true;";
						echo "$('txtHrsOTNG8').disabled=true;";
						echo "$('txtOTG8').disabled=true;";
						echo "$('txtHrsND').disabled=true;";
						echo "$('txtHrsNDG8').disabled=true;";
						echo "$('cmbStat').disabled=true;";
						echo "$('btnSaveHrs').disabled=true;";
						echo "$('cmbDayType').value='';";
						echo "$('txtHrsReg').value='';";
						echo "$('txtHrsOTNG8').value='';";
						echo "$('txtOTG8').value='';";
						echo "$('txtHrsND').value='';";
						echo "$('txtHrsNDG8').value='';";
						echo "$('cmbStat').value='';";
					}
					else{
						if($_GET['id']=="txtTSDate"){			
							echo "$('hdnDayType').value='$dcode';";
							echo "$('cmbDayType').value='$dcode';";
							echo "$('cmbDayType').disabled=false;";
							echo "$('txtHrsReg').disabled=false;";
							echo "$('txtHrsOTNG8').disabled=false;";
							echo "$('txtOTG8').disabled=false;";
							echo "$('txtHrsND').disabled=false;";
							echo "$('txtHrsNDG8').disabled=false;";
							echo "$('cmbStat').disabled=false;";
							echo "$('btnSaveHrs').disabled=false;";
							echo "$('cmbStat').value='O';";
						}
						else{
							echo "$('txtBasicAmnt').disabled=false;";
							echo "$('txtOTAmnt').disabled=false;";
							echo "$('txtNDAmnt').disabled=false;";
							echo "$('txtHPAmnt').disabled=false;";
							echo "$('txtECOLAAmnt').disabled=false;";
							echo "$('txtCTPAAmnt').disabled=false;";
							echo "$('txtAdvancesAmnt').disabled=false;";
							echo "$('cmbStatAmnt').disabled=false;";
							echo "$('btnSaveAmnt').disabled=false;";
							echo "$('cmbStatAmnt').value='O';";
						}
					}
				}
				if($dcode!="01"){	

					if($RR['pdNumber']!=""){
						echo "document.getElementById('imgCalendar').style.visibility='hidden';";	
						echo "document.getElementById('imgCalendarAmnt').style.visibility='visible';";
						echo "$('cmbAllowance').disabled=true;";
						echo "$('cmbAdvances').disabled=true;";
						echo "$('cmbAdjustmentType').value='A';";
						echo "$('txtTSAmntDate').disabled=false;";
						echo "$('txtTSAmntDate').readOnly=true;";
						echo "$('txtBasicAmnt').disabled=false;";
						echo "$('txtOTAmnt').disabled=false;";
						echo "$('txtNDAmnt').disabled=false;";
						echo "$('txtHPAmnt').disabled=false;";
						echo "$('txtECOLAAmnt').disabled=false;";
						echo "$('txtCTPAAmnt').disabled=false;";
						echo "$('txtAdvancesAmnt').disabled=false;";
						echo "$('cmbStatAmnt').disabled=false;";
						echo "$('btnSaveAmnt').disabled=false;";
						echo "$('cmbStatAmnt').value='O';";
						
						echo "$('txtTSDate').value='';";
						echo "$('hdnDayType').value='';";
						echo "$('cmbDayType').value='';";
						echo "$('cmbDayType').disabled=true;";
						echo "$('txtHrsReg').disabled=true;";
						echo "$('txtHrsOTNG8').disabled=true;";
						echo "$('txtOTG8').disabled=true;";
						echo "$('txtHrsND').disabled=true;";
						echo "$('txtHrsNDG8').disabled=true;";
						echo "$('cmbStat').disabled=true;";
						echo "$('btnSaveHrs').disabled=true;";
						echo "$('cmbDayType').value='';";
						echo "$('txtHrsReg').value='';";
						echo "$('txtHrsOTNG8').value='';";
						echo "$('txtOTG8').value='';";
						echo "$('txtHrsND').value='';";
						echo "$('txtHrsNDG8').value='';";
						echo "$('cmbStat').value='';";
						
					}
					else{
				
						if($_GET['id']=="txtTSDate"){			
							echo "$('hdnDayType').value='$dcode';";
							echo "$('cmbDayType').value='$dcode';";
							echo "$('txtHrsReg').value='';";
							echo "$('cmbDayType').disabled=false;";
							echo "$('txtHrsReg').disabled=true;";
							echo "$('txtHrsOTNG8').disabled=false;";
							echo "$('txtOTG8').disabled=false;";
							echo "$('txtHrsND').disabled=false;";
							echo "$('txtHrsNDG8').disabled=false;";
							echo "$('cmbStat').disabled=false;";
							echo "$('btnSaveHrs').disabled=false;";
							echo "$('cmbAllowance').value='';";
							echo "$('cmbAdvances').value='';";
							echo "$('cmbStat').value='O';";
						}
						else{
							echo "$('txtBasicAmnt').disabled=false;";
							echo "$('txtOTAmnt').disabled=false;";
							echo "$('txtNDAmnt').disabled=false;";
							echo "$('txtHPAmnt').disabled=false;";
							echo "$('txtECOLAAmnt').disabled=false;";
							echo "$('txtCTPAAmnt').disabled=false;";
							echo "$('txtAdvancesAmnt').disabled=false;";
							echo "$('cmbStatAmnt').disabled=false;";
							echo "$('btnSaveAmnt').disabled=false;";
							echo "$('cmbStatAmnt').value='O';";
						}
					}
				}
			}
			else{
				if($RR['pdNumber']!=""){
					echo "document.getElementById('imgCalendar').style.visibility='hidden';";	
					echo "document.getElementById('imgCalendarAmnt').style.visibility='visible';";	
					echo "$('cmbAllowance').disabled=true;";
					echo "$('cmbAdvances').disabled=true;";
					echo "$('cmbAdjustmentType').value='A';";
					echo "$('txtTSAmntDate').disabled=false;";
					echo "$('txtTSAmntDate').readOnly=true;";
					echo "$('txtBasicAmnt').disabled=false;";
					echo "$('txtOTAmnt').disabled=false;";
					echo "$('txtNDAmnt').disabled=false;";
					echo "$('txtHPAmnt').disabled=false;";
					echo "$('txtECOLAAmnt').disabled=false;";
					echo "$('txtCTPAAmnt').disabled=false;";
					echo "$('txtAdvancesAmnt').disabled=false;";
					echo "$('cmbStatAmnt').disabled=false;";
					echo "$('btnSaveAmnt').disabled=false;";
					echo "$('cmbStatAmnt').value='O';";
					
					echo "$('txtTSDate').value='';";
					echo "$('hdnDayType').value='';";
					echo "$('cmbDayType').value='';";
					echo "$('cmbDayType').disabled=true;";
					echo "$('txtHrsReg').disabled=true;";
					echo "$('txtHrsOTNG8').disabled=true;";
					echo "$('txtOTG8').disabled=true;";
					echo "$('txtHrsND').disabled=true;";
					echo "$('txtHrsNDG8').disabled=true;";
					echo "$('cmbStat').disabled=true;";
					echo "$('btnSaveHrs').disabled=true;";
					echo "$('cmbDayType').value='';";
					echo "$('txtHrsReg').value='';";
					echo "$('txtHrsOTNG8').value='';";
					echo "$('txtOTG8').value='';";
					echo "$('txtHrsND').value='';";
					echo "$('txtHrsNDG8').value='';";
					echo "$('cmbStat').value='';";
					
				}
				else{
					if($_GET['id']=="txtTSDate"){
						echo "$('hdnDayType').value='';";
						echo "$('cmbDayType').value='';";
						echo "$('cmbDayType').disabled=true;";
						echo "$('txtHrsReg').disabled=true;";
						echo "$('txtHrsOTNG8').disabled=true;";
						echo "$('txtOTG8').disabled=true;";
						echo "$('txtHrsND').disabled=true;";
						echo "$('txtHrsNDG8').disabled=true;";
						echo "$('cmbStat').disabled=true;";
						echo "$('btnSaveHrs').disabled=true;";
						echo "$('cmbDayType').value='';";
						echo "$('txtHrsReg').value='';";
						echo "$('txtHrsOTNG8').value='';";
						echo "$('txtOTG8').value='';";
						echo "$('txtHrsND').value='';";
						echo "$('txtHrsNDG8').value='';";
						echo "$('cmbStat').value='';";
						echo "$('txtTSAmntDate').value='';";
						echo "$('txtBasicAmnt').value='';";
						echo "$('txtOTAmnt').value='';";
						echo "$('txtNDAmnt').value='';";
						echo "$('txtHPAmnt').value='';";
						echo "$('txtECOLAAmnt').value='';";
						echo "$('txtCTPAAmnt').value='';";
						echo "$('txtAdvancesAmnt').value='';";
					}
					else{
						echo "$('txtTSAmntDate').value='Invalid date!';";
						echo "$('txtBasicAmnt').value='';";
						echo "$('txtOTAmnt').value='';";
						echo "$('txtNDAmnt').value='';";
						echo "$('txtHPAmnt').value='';";
						echo "$('txtECOLAAmnt').value='';";
						echo "$('txtCTPAAmnt').value='';";
						echo "$('txtAdvancesAmnt').value='';";
					}
				}
			}			
		exit();
		break;
		
		//compute entered hours
		case ('processHours'):
			$timesheetsadjustmentsObj->processHrs();
		exit();
		break;
	
		//process entered timesheet adjustments	
		case ('processTimesheetAdjustment'):
			$timesheetchecker = $timesheetsadjustmentsObj->recordChecker("Select * from tblTK_TimesheetAdjustment where compcode='".$_SESSION['company_code']."' and empNo='".$_GET['txtAddEmpNo'] ."' and tsDate='".($_GET['txtTSDate']==""?$_GET['txtTSAmntDate']:$_GET['txtTSDate'])."' and tsStat='O'");
			if(!$timesheetchecker){
				if($timesheetsadjustmentsObj->processAdjustments()){
					echo "alert('Timesheet Adjustment has been added!');";	
					echo "location.href='timesheets_adjustments.php';";
				}
				else{
					echo "alert('Failed to save the Timesheet Adjusment!');";	
				}
			}
			else{
				echo "alert('Select Date already exist! Kindly edit the existing timesheet adjustment for additional entries.');";	
			}
		exit();
		break;
		
		//update selected timesheet adjustments
		case ('updateTimesheetAdjustment'):
			$seqno = $_GET['hdnSeqNo'];
			$tsdate = $_GET['txtTSAmntDate'];
			$empno = $_GET['txtAddEmpNo'];
			$timesheetchecker = $timesheetsadjustmentsObj->recordChecker("Select * from tblTK_TimesheetAdjustment where tsDate='{$tsdate}' and empNo='{$empno}' and  seqNo<>'{$seqno}'");
			if(!$timesheetchecker){
				if($timesheetsadjustmentsObj->updateAdjustments()){
					echo "alert('Timesheet Adjustment has been updated!');";	
					echo "location.href='timesheets_adjustments.php';";
				}
				else{
					echo "alert('Failed to update the Timesheet Adjusment!');";	
				}
			}
			else{
				echo "alert('Select Date already exist! Kindly edit the existing timesheet adjustment for additional entries.');";	
			}
		exit();
		break;
	
		//show selected employee informations	
		case ('getEmpInfo'):
			//$sqlGrp = "Select payGrp from tblProcGrp where compCode='{$_SESSION['company_code']}' and status='A'";
			$sqlGrp = "Select payGrp from tblProcGrp where compCode='{$_SESSION['company_code']}'";
			$res = $timesheetsadjustmentsObj->getSqlAssoc($timesheetsadjustmentsObj->execQry($sqlGrp));		
			$paygroup = $res['payGrp'];	
			$qryPayperiod = $timesheetsadjustmentsObj->execQry("Select pdFrmDate, DATE_ADD(pdToDate, INTERVAL 5 DAY) as  pdToDate
											  from tblPayPeriod 
											  where compCode='{$_SESSION['company_code']}' and payGrp='{$paygroup}' 
												and pdYear='".date("Y")."' and pdTSStat='O'");
			$payperiod = $timesheetsadjustmentsObj->getSqlAssoc($qryPayperiod);
			
//184000006
			$qryEmpList = "SELECT  *
							FROM tblEmpMast
							WHERE compCode= '{$_SESSION['company_code']}'
							AND empNo='{$_GET['empNo']}'
							AND empPayGrp='{$paygroup}'
							AND empBrnCode IN (SELECT brnCode FROM tblTK_UserBranch 
									WHERE compCode ='{$_SESSION['company_code']}'and empNo = '{$_SESSION['employee_number']}')
							AND ((empStat='RG') 
							OR (((dateResigned between '".date("Y-m-d",strtotime($payperiod['pdFrmDate']))."' 
								AND '".date("Y-m-d",strtotime($payperiod['pdToDate']))."') 
							OR (endDate between '".date("Y-m-d",strtotime($payperiod['pdFrmDate']))."' 
								AND '".date("Y-m-d",strtotime($payperiod['pdToDate']))."'))))"; 
									
			$resEmpList = $timesheetsadjustmentsObj->execQry($qryEmpList);
			$empInfo = $timesheetsadjustmentsObj->getSqlAssoc($resEmpList);						
			
			//$empInfo = $timesheetsadjustmentsObj->getUserInfo($_SESSION['company_code'],$_GET['empNo'],'');
				if($empInfo['empNo']!=''){
					$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
					if($empInfo['empPayGrp']==1){
						$pgrp = "Group 1";	
					}
					if($empInfo['empPayGrp']==2){
						$pgrp = "Group 2";	
					}
					$pcat = $timesheetsadjustmentsObj->getPayCat($_SESSION['company_code']," and payCat='".$empInfo['empPayCat']."'");
					$paycat = $pcat['payCatDesc'];
					echo "$('txtName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";
					echo "$('txtPayGrp').value='$pgrp';";
					echo "$('txtPayCat').value='$paycat';";
					echo "$('hdnPayGrp').value='$empInfo[empPayGrp]';";
					echo "$('hdnPayCat').value='$empInfo[empPayCat]';";
				
					$payperiod = $timesheetsadjustmentsObj->getPayPeriod($_SESSION['company_code'], " and pdStat='O' 
															and payGrp='{$empInfo['empPayGrp']}' and payCat='{$empInfo['empPayCat']}'");	
					if($payperiod['pdFrmDate']!='' && $payperiod['pdToDate']!=''){ 
						$cperiod = date('F d, Y', strtotime($payperiod['pdFrmDate'])) . " - " . date('F d, Y', strtotime($payperiod['pdToDate']));
						$pdnumber = $payperiod['pdNumber'];
						$pdyear = $payperiod['pdYear'];
						$cseries = $payperiod['pdSeries']-1;
						echo "$('txtCurrentPeriod').value='$cperiod';";
						echo "$('hdnPDYear').value='$pdyear';";
						echo "$('hdnPDNumber').value='$pdnumber';";
					}
					else{
						echo "$('txtCurrentPeriod').value='';";
					}
		
					$pperiod = $timesheetsadjustmentsObj->getPayPeriod($_SESSION['company_code'], " and pdSeries='{$cseries}'");	
					if($pperiod['pdFrmDate']!='' && $pperiod['pdToDate']!=''){
						$pperiod = date('F d, Y', strtotime($pperiod['pdFrmDate'])) . " - " . date('F d, Y', strtotime($pperiod['pdToDate']));
						echo "$('txtPeriodToPost').value='$pperiod';";
					}
					else{
						echo "$('txtPeriodToPost').value='';";
					}
				}
				else{
					echo "alert('No Record Found!');";	
					echo "$('txtAddEmpNo').value='';";
					echo "$('txtName').value='';";
					echo "$('txtPayGrp').value='';";
					echo "$('txtPayCat').value='';";
					echo "$('hdnPayGrp').value='';";
					echo "$('hdnPayCat').value='';";
					echo "$('imgCalendar').style.visibility='hidden';";
					echo "$('txtPeriodToPost').value='';";
					echo "$('txtCurrentPeriod').value='';";
					echo "$('hdnPDYear').value='';";
					echo "$('hdnPDNumber').value='';";
				}
			exit();
		break;
		
		
		//delete selected timesheet adjustments
		case 'Delete':
		
			$chkSeqNo = $_GET["chkseq"];
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryDel = "DELETE FROM tblTK_TimesheetAdjustment where seqNo='".$chkSeqNo_val."'";
				$resDel = $timesheetsadjustmentsObj->execQry($qryDel);
			}

			echo "alert('Selected Timesheet Adjustment already deleted.');";
			echo "location.href='timesheets_adjustments.php';";
			
		exit();
		break;
	
		//approve selected timesheet adjustments
		case 'Approved':
		
			$chkSeqNo = $_GET["chkseq"];

			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryApprove = "UPDATE tblTK_TimesheetAdjustment 
							   SET dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."', 
							   tsStat='A' WHERE seqNo='".$chkSeqNo_val."';";
				$resApprove = $timesheetsadjustmentsObj->execQry($qryApprove);
			}

			echo "alert('Selected Timesheet Adjustment already approved.')";
			
		exit();
		break;
		
		//show selected timesheet adjustments for editing
		case "getSeqNo":
			$chkSeqNo = $_GET["chkseq"];		
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$inputTypeSeqNo = $chkSeqNo_val;
			}
			
			$qry = $timesheetsadjustmentsObj->execQry("SELECT ts.compcode, ts.empNo, ts.tsDate, ts.dayType, ts.payGrp, 
															ts.payCat, ts.pdYear, ts.pdNumber, ts.entryTag, ts.includeAllowTag, 
															ts.includeAdvTag, ts.hrsReg, ts.hrsOtLe8, ts.hrsOtGt8, ts.hrsNd, 
															ts.hrsNdGt8, ts.adjBasic, ts.adjOt, ts.adjNd, ts.adjHp, 
															ts.adjEcola, ts.adjCtpa, ts.adjAdv, ts.tsStat, ts.seqNo, emp.empLastName, 
															emp.empFirstName, emp.empMidName
														FROM tblTK_TimesheetAdjustment ts 
														INNER JOIN tblEmpMast emp ON ts.empNo = emp.empNo 
														where ts.seqNo='".$inputTypeSeqNo."'");
			$resQry = $timesheetsadjustmentsObj->getSqlAssoc($qry);
			$name = htmlspecialchars(addslashes($resQry['empLastName'].", ".$resQry['empFirstName']." ".$resQry['empMidName']));
			if($resQry['payGrp']==1){
				$pgrp = "Group 1";	
			}
			if($resQry['payGrp']==2){
				$pgrp = "Group 2";	
			}
			$pcat = $timesheetsadjustmentsObj->getPayCat($_SESSION['company_code']," and payCat='".$resQry['payCat']."'");
			$paycat = $pcat['payCatDesc'];
			echo "$('hdnProcess').value='1';";
			echo "$('hdnSeqNo').value='{$resQry['seqNo']}';";
			echo "$('txtAddEmpNo').value='{$resQry['empNo']}';";
			echo "$('txtName').value='{$name}';";
			echo "$('txtPayGrp').value='{$pgrp}';";
			echo "$('hdnPayGrp').value='{$resQry['payGrp']}';";
			echo "$('txtPayCat').value='{$paycat}';";
			echo "$('hdnPayCat').value='{$resQry['payCat']}';";
			
			$payperiod = $timesheetsadjustmentsObj->getPayPeriod($_SESSION['company_code'], " and pdStat='O' 
													and payGrp='{$resQry['payGrp']}' and payCat='{$resQry['payCat']}'");	
			if($payperiod['pdFrmDate']!='' && $payperiod['pdToDate']!=''){ 
				$cperiod = date('F d, Y', strtotime($payperiod['pdFrmDate'])) . " - " . date('F d, Y', strtotime($payperiod['pdToDate']));
				$pdnumber = $payperiod['pdNumber'];
				$pdyear = $payperiod['pdYear'];
				$cseries = $payperiod['pdSeries']-1;
				echo "$('txtCurrentPeriod').value='$cperiod';";
				echo "$('hdnPDYear').value='$pdyear';";
				echo "$('hdnPDNumber').value='$pdnumber';";
			}
			else{
				echo "$('txtCurrentPeriod').value='';";
			}

			$pperiod = $timesheetsadjustmentsObj->getPayPeriod($_SESSION['company_code'], " and pdSeries='{$cseries}'");	
			if($pperiod['pdFrmDate']!='' && $pperiod['pdToDate']!=''){
				$pperiod = date('F d, Y', strtotime($pperiod['pdFrmDate'])) . " - " . date('F d, Y', strtotime($pperiod['pdToDate']));
				echo "$('txtPeriodToPost').value='$pperiod';";
			}
			else{
				echo "$('txtPeriodToPost').value='';";
			}
			
			$dtypedesc = $timesheetsadjustmentsObj->getDayTypeDescArt($resQry['dayType']);
			
			echo "$('txtTSDate').disabled=false;";
			echo "$('btnSaveHrs').disabled=false;";
			
			echo "$('cmbAllowance').value='{$resQry['includeAllowTag']}';";
			echo "$('cmbAdvances').value='{$resQry['includeAdvTag']}';";
			echo "$('cmbAdjustmentType').value='{$resQry['entryTag']}';";
			echo "$('cmbDayType').value='{$resQry['dayType']}';";
			echo "$('hdnDayType').value='{$resQry['dayType']}';";
			echo "$('txtHrsReg').value='{$resQry['hrsReg']}';";
			echo "$('txtHrsOTNG8').value='{$resQry['hrsOtLe8']}';";
			echo "$('txtOTG8').value='{$resQry['hrsOtGt8']}';";
			echo "$('txtHrsND').value='{$resQry['hrsNd']}';";
			echo "$('txtHrsNDG8').value='{$resQry['hrsNdGt8']}';";
			echo "$('txtTSAmntDate').value='".date("Y-m-d", strtotime($resQry['tsDate']))."';";
			echo "$('txtBasicAmnt').value='{$resQry['adjBasic']}';";
			echo "$('txtOTAmnt').value='{$resQry['adjOt']}';";
			echo "$('txtNDAmnt').value='{$resQry['adjNd']}';";
			echo "$('txtHPAmnt').value='{$resQry['adjHp']}';";
			echo "$('txtECOLAAmnt').value='{$resQry['adjEcola']}';";
			echo "$('txtCTPAAmnt').value='{$resQry['adjCtpa']}';";
			echo "$('txtAdvancesAmnt').value='{$resQry['adjAdv']}';";
			echo "$('cmbStatAmnt').value='{$resQry['tsStat']}';";
			
			if($resQry['entryTag']=="H"){
				echo "$('txtTSDate').value='".date("Y-m-d", strtotime($resQry['tsDate']))."';";
				echo "$('cmbStat').value='{$resQry['tsStat']}';";
				echo "$('imgCalendar').style.visibility='visible';";
				echo "$('imgCalendarAmnt').style.visibility='hidden';";
				echo "$('cmbAllowance').disabled=false;";
				echo "$('cmbAdvances').disabled=false;";				
				echo "$('btnSaveAmnt').disabled=true;";
				echo "$('txtTSAmntDate').disabled=true;";
				echo "$('txtBasicAmnt').disabled=true;";
				echo "$('txtOTAmnt').disabled=true;";
				echo "$('txtNDAmnt').disabled=true;";
				echo "$('txtHPAmnt').disabled=true;";
				echo "$('txtECOLAAmnt').disabled=true;";
				echo "$('txtCTPAAmnt').disabled=true;";
				echo "$('txtAdvancesAmnt').disabled=true;";
				echo "$('cmbStatAmnt').disabled=true;";
				
				if($resQry['dayType']=="01"){
					echo "$('txtHrsReg').disabled=false;";	
					echo "$('txtHrsOTNG8').disabled=true;";
					echo "$('txtOTG8').disabled=true;";
					echo "$('txtHrsND').disabled=true;";
					echo "$('txtHrsNDG8').disabled=true;";
					echo "$('cmbStat').disabled=false;";
				}
				else{
					echo "$('txtHrsReg').disabled=true;";	
					echo "$('txtHrsOTNG8').disabled=false;";
					echo "$('txtOTG8').disabled=false;";
					echo "$('txtHrsND').disabled=false;";
					echo "$('txtHrsNDG8').disabled=false;";
					echo "$('cmbStat').disabled=false;";	
				}
			}
			else{
				echo "$('txtTSDate').value='';";
				echo "$('cmbStat').value='';";
				echo "$('imgCalendar').style.visibility='hidden';";
				echo "$('imgCalendarAmnt').style.visibility='visible';";
				echo "$('cmbAllowance').disabled=true;";
				echo "$('cmbAdvances').disabled=true;";
				echo "$('btnSaveHrs').disabled=true;";
				echo "$('txtTSDate').disabled=true;";
				echo "$('txtHrsReg').disabled=true;";	
				echo "$('txtHrsOTNG8').disabled=true;";
				echo "$('txtOTG8').disabled=true;";
				echo "$('txtHrsND').disabled=true;";
				echo "$('txtHrsNDG8').disabled=true;";
				echo "$('cmbStat').disabled=true;";
				
				echo "$('btnSaveAmnt').disabled=false;";
				echo "$('txtTSAmntDate').disabled=false;";
				echo "$('txtBasicAmnt').disabled=false;";
				echo "$('txtOTAmnt').disabled=false;";
				echo "$('txtNDAmnt').disabled=false;";
				echo "$('txtHPAmnt').disabled=true;";
				echo "$('txtECOLAAmnt').disabled=false;";
				echo "$('txtCTPAAmnt').disabled=false;";
				echo "$('txtAdvancesAmnt').disabled=false;";
				echo "$('cmbStatAmnt').disabled=false;";
				
			}
			
		exit();
		break;
	
	default:
	}
}
?>



<HTML>

	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		
		<!--calendar lib-->
		<!--<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar.js"></script>	
		<STYLE>@import url('../../../includes/calendar.css');</STYLE>
		
	</HEAD>
	
<BODY>
	<FORM name='frmTimesheetAdjustments' id="frmTimesheetAdjustments" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="timesheetAdjustmentCont"></div>
			<div id="indicator1" align="center"></div>
	</FORM>
</BODY>

	
</HTML>

<script type="text/javascript">
	disableRightClick()
	
	function validateDayType(id){
		var param ='&empno='+$('txtAddEmpNo').value+'&tsdate='+$(id).value+'&id='+id;
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=validatedaytype'+param,{
				method : 'get',
				parameters : $('frmTimesheetAdjustments').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
				},
			});	
	}
	
	function processHrs(){
		if($('hdnDayType').value==01 || $('hdnDayType').value==02){
			if($('txtHrsReg').value=='' && $('txtHrsOTNG8').value=='' && $('txtOTG8').value=='' && $('txtHrsND').value=='' && $('txtHrsNDG8').value==''){
				alert('Hour is Required!');
				return false;	
			}
		}
		
		if($('cmbStat').value==''){
			alert('Status is Required!');
			$('cmbStat').focus();
			return false;	
		}
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=processHours',{
			method : 'GET',
			parameters : $('frmTimesheetAdjustments').serialize(),
			onComplete : function(req){
				eval(req.responseText);	
			},	
		})
	}
	
	function setAdjustmentType(type){
		var name = $('txtName').value;
		if(name==""){
			$('cmbAdjustmentType').value='';
			alert('Employee data not valid!');
			return false;	
		}
		if(type=='A'){
			document.getElementById('imgCalendarAmnt').style.visibility='visible';	
			document.getElementById('imgCalendar').style.visibility='hidden';	
			$('txtTSAmntDate').disabled=false;
			$('txtTSAmntDate').readOnly=true;
			$('txtBasicAmnt').value='';
			$('txtOTAmnt').value='';
			$('txtNDAmnt').value='';
			$('txtHPAmnt').value='';
			$('txtECOLAAmnt').value='';
			$('txtCTPAAmnt').value='';
			$('txtAdvancesAmnt').value='';
			$('cmbStatAmnt').value='';
	
			$('txtTSDate').value='';
			$('cmbDayType').value=0;
			$('txtHrsReg').value='';
			$('txtHrsOTNG8').value='';
			$('txtOTG8').value='';
			$('txtHrsND').value='';
			$('txtHrsNDG8').value='';
			$('cmbStat').value='';	
	
			$('cmbAllowance').disabled=true;
			$('cmbAdvances').disabled=true;
			$('btnSaveHrs').disabled=true;
			$('txtTSDate').disabled=true;
			$('cmbDayType').disabled=true;
			$('hdnDayType').value='';
			$('txtHrsReg').disabled=true;
			$('txtHrsOTNG8').disabled=true;
			$('txtOTG8').disabled=true;
			$('txtHrsND').disabled=true;
			$('txtHrsNDG8').disabled=true;
			$('cmbStat').disabled=true;						
		}
		if(type=='H'){
			document.getElementById('imgCalendar').style.visibility='visible';	
			document.getElementById('imgCalendarAmnt').style.visibility='hidden';	
			$('cmbAllowance').disabled=false;
			$('cmbAdvances').disabled=false;			
			$('txtTSDate').disabled=false;
			$('txtTSDate').readOnly=true;
			$('cmbDayType').disabled=true;
			//$('txtDayType').readOnly=true;
			$('txtTSAmntDate').disabled=true;	
			//$('txtDayType').disabled=true;	
			$('txtHrsReg').disabled=true;	
			$('txtHrsOTNG8').disabled=true;	
			$('txtOTG8').disabled=true;	
			$('txtHrsND').disabled=true;	
			$('txtHrsNDG8').disabled=true;	
			$('cmbStat').disabled=true;			
			
			$('btnSaveAmnt').disabled=true;
			$('txtTSAmntDate').disabled=true;
			$('txtBasicAmnt').disabled=true;
			$('txtOTAmnt').disabled=true;
			$('txtNDAmnt').disabled=true;
			$('txtHPAmnt').disabled=true;
			$('txtECOLAAmnt').disabled=true;
			$('txtCTPAAmnt').disabled=true;
			$('txtAdvancesAmnt').disabled=true;
			$('cmbStatAmnt').disabled=true;		
			
			$('txtTSAmntDate').value='';
			$('txtBasicAmnt').value='';
			$('txtOTAmnt').value='';
			$('txtNDAmnt').value='';
			$('txtHPAmnt').value='';
			$('txtECOLAAmnt').value='';
			$('txtCTPAAmnt').value='';
			$('txtAdvancesAmnt').value='';
			$('cmbStatAmnt').value='';

			$('txtTSDate').value='';
			$('cmbDayType').value='';
			$('txtHrsReg').value='';
			$('txtHrsOTNG8').value='';
			$('txtOTG8').value='';
			$('txtHrsND').value='';
			$('txtHrsNDG8').value='';
			$('cmbStat').value='';	
			
		}
	}
	
	function processAdjustment(){
		var daytype = $('hdnDayType').value;
		var proc = $('hdnProcess').value;
		var adjustmentType = $('cmbAdjustmentType').value;
		var hrsOTNG = removeSpaces($('txtHrsOTNG8').value);
		var hrsOTG8 = removeSpaces($('txtOTG8').value);
		var hrsND = removeSpaces($('txtHrsND').value);
		var hrsNDG8 = removeSpaces($('txtHrsNDG8').value);
		var param;
		
		if(adjustmentType=="A"){
			if(daytype==''){
				if($('txtBasicAmnt').value=='' && $('txtOTAmnt').value=='' && $('txtNDAmnt').value=='' && $('txtHPAmnt').value=='' && $('txtECOLAAmnt').value=='' && $('txtCTPAAmnt').value=='' && $('txtAdvancesAmnt').value==''){
					alert('Amount is Required!');
					return false;	
				}
				
				if($('cmbStatAmnt').value==''){
					alert('Status is Required!');
					$('cmbStatAmnt').focus();
					return false;	
				}
			}
		}
		else{
			if($('hdnDayType').value==01){
				if($('txtHrsReg').value!="" && ($('txtBasicAmnt').value=='' || $('txtBasicAmnt').value==0.00)){
					alert('Please Re-Process Hours to compute Regular Hours Adjustment!');
					return false;	
				}
			}
			else{
				if((hrsOTNG.replace(".","")!='' || hrsOTG8.replace(".","")!='') && ($('txtOTAmnt').value=='' || $('txtOTAmnt').value=='0.00')){
					alert('Please Re-Process Hours to compute Overtime Hours Adjustment!');
					return false;	
				}
				if((hrsND.replace(".","")!='' || hrsNDG8.replace(".","")!='') && ($('txtNDAmnt').value=='' || $('txtNDAmnt').value=='0.00')){
					alert('Please Re-Process Hours to compute Night Diff. Hours Adjustment!');
					return false;	
				}
				if($('cmbStat').value==''){
					alert('Status is Required!');
					$('cmbStat').focus();
					return false;	
				}
			}
		}
		
		if(proc==1){
			param = "action=processTimesheetAdjustment&action=updateTimesheetAdjustment";
		}
		else{
			param = "action=processTimesheetAdjustment";	
		}
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?'+param,{
			method : 'GET',
			parameters : $('frmTimesheetAdjustments').serialize(),
			onComplete : function(req){
				eval(req.responseText);
			}	
		})
	}

	function removeSpaces(val) {
	   return val.split(' ').join('');
	}
	
	function validateMod(mode){
		if(mode == 'EDITRENO'){
			$('newLeave').innerHTML="<img src='../../../images/application_add_2.png' class='toolbarImg'>";
			$('deleEarn').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
			$('btnUpdtHdr').disabled=true;
			
			$('refLookup').disabled=false;
			$('btnSaveAddDtl').disabled=true;
			$('refNo').focus();
		}
		
		if(mode == 'REFRESH'){
			Windows.getWindow('refWin').close();
		}
	}
	
	function newRef(act){
		
		pager('timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont','refresh',0,0,'','','','../../../images/');  
			
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+act,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
				$('cmbTrnType').focus();
				$('editEarn').innerHTML="<img src='../../../images/application_form_edit_2.png' class='toolbarImg'>";
				$('deleEarn').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
			},
			onCreate : function(){
				$('refNoCont').innerHTML='Loading...';
			},
			onSuccess : function(){
				$('refNoCont').innerHTML='';
			}
		});
	}

	
	function getEmployee(evt,eleVal){
		var evnt = evt.keyCode;
		var param ='&empNo='+eleVal;
		
		if(evnt==13) {
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=getEmpInfo'+param,{
					method : 'get',
					parameters : $('frmTimesheetAdjustments').serialize(),
					onComplete : function (req){
						eval(req.responseText);	
					},
					onCreate : function (){
						$('indicator1').src="../../../images/wait.gif";
					},
					onSuccess : function (){
						$('indicator1').innerHTML="";
					}
				});	
		}
		
	}
	
	
	function viewLookup(){

		var RefWin = new Window({
			id: "refWin",
			className : 'mac_os_x',
			width:600, 
			height:400, 
			zIndex: 100, 
			resizable: false, 
			title: "Earnings Reference Look - Up", 
			minimizable:true,
			showEffect:Effect.Appear, 
			destroyOnClose: true,
			maximizable: false,
			hideEffect: Effect.SwitchOff, 
			draggable:true })
			RefWin.setAjaxContent('reference_lookup.php?opnr=earn','','');
			//RefWin.show(true);
			RefWin.showCenter(true);
			
			//$('editEarn').innerHTML="<img src='../../../images/application_form_edit_2.png' class='toolbarImg'>" 
			//$('refLookup').disabled=true;
			
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == RefWin) {
		        RefWin = null;
		        Windows.removeObserver(this);
		        $('refNo').focus();
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}	
		
	function passRefNo(refNoVal){
		Windows.getWindow('refWin').close();
		pager('timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont','editRef',0,0,'','','&refNo='+refNoVal,'../../../images/');  
	}	
		

pager('timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont','load',0,0,'','','','../../../images/');  


	function getCheckedBoxes(chkboxName) {
	  var checkboxes = document.getElementsByName(chkboxName);
	  var checkboxesChecked = [];
	  for (var i=0; i<checkboxes.length; i++) {
		 if (checkboxes[i].checked) {
			checkboxesChecked.push(checkboxes[i]);
		 }
	  }
	  return checkboxesChecked.length > 0 ? 1 : 0;
	}
	
	
	
	function delTimesheetAdj(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var checkedBoxes = getCheckedBoxes("chkseq[]");
		if(checkedBoxes==0){
			alert('Please select timesheet adjustment to be deleted!');	
		}
		else{		
			var deleTimesheet = confirm('Are you sure you want to delete the selected Timesheet Adjustment?');
			
			if(deleTimesheet == true){
				var param = '?action=Delete&seqNo='+seqNo;
				
				new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
					method : 'get',
					parameters : $('frmTimesheetAdjustments').serialize(),
					onComplete : function (req){
						eval(req.responseText);	
						pager('timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont','load',0,0,'','','','../../../images/');  
					},
					onCreate : function (){
						$('indicator2').src="../../../images/wait.gif";
					},
					onSuccess : function (){
						$('indicator2').innerHTML="";
					}
				});				
			}
		}
	}
	
	
	
	function updateLvTran(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch){
		var checkedBoxes = getCheckedBoxes("chkseq[]");
		if(checkedBoxes==0){
			alert('Please select timesheet adjustment to be approved!');	
		}
		else{		
		
			var deleShiftCode = confirm('Are you sure you want to Approve the selected Timesheet Adjustment?');
			
			if(deleShiftCode == true){
				var param = '?action=Approved&seqNo='+seqNo;
	
				new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
					method : 'get',
					parameters : $('frmTimesheetAdjustments').serialize(),
					onComplete : function (req){
						eval(req.responseText);	
						pager('timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont','load',0,0,'','','','../../../images/');  
					},
					onCreate : function (){
						$('indicator2').src="../../../images/wait.gif";
					},
					onSuccess : function (){
						$('indicator2').innerHTML="";
					}
				});	
			}
		}
	}
	
	function checkAll(field)
	{
		var chkob = document.frmTimesheetAdjustments.elements['chkseq[]'];
		if (field=="1") 
		{ 
   			for (var i=0; i<chkob.length; i++)
			{
				chkob[i].checked = true;
    		}
				return "0";  
  		} 
		else 
		{

    		for (var i=0; i<chkob.length; i++)
			{
				chkob[i].checked = false;
    		}
				return "1";  
 		}
	}	
	
		
	function getSeqNo()
	{
		var checkedBoxes = getCheckedBoxes("chkseq[]");
		if(checkedBoxes==0){
			alert('Please select timesheet adjustment to be modified!');	
		}
		else{		
			var param = '?action=getSeqNo';
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmTimesheetAdjustments').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
				}
			});	
		}
	}
	
	function changeDayType(id){
		$('hdnDayType').value=id;		
		if(id==01){
			$('txtHrsReg').disabled=false;	
			$('txtHrsOTNG8').disabled=false;	
			$('txtOTG8').disabled=false;	
			$('txtHrsND').disabled=false;
			$('txtHrsNDG8').disabled=false;	
		}
		else{
			$('txtHrsReg').disabled=true;	
			$('txtHrsOTNG8').disabled=false;	
			$('txtOTG8').disabled=false;	
			$('txtHrsND').disabled=false;
			$('txtHrsNDG8').disabled=false;	
		}
	}
	
</SCRIPT>