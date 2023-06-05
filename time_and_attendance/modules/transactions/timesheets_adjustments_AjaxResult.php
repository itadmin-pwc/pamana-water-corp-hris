<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/pager.inc.php");
include("../../../includes/common.php");
include("timesheets_adjustments_obj.php");

$srchType=0;
$timesheetsadjustmentsObj = new timesheetAdjustmentsObj($_GET,$_SESSION);
$timesheetsadjustmentsObj->validateSessions('','MODULES');

$pager = new AjaxPager(10,'../../../images/');

//get users branch access
	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $timesheetsadjustmentsObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		$brnCodelist = " AND emp.empNo<>'".$_SESSION['employee_number']."' and emp.empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND emp.empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}

	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblTK_UserBranch tblUB, tblBranch as tblbrn
				 where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
				 and empNo='".$_SESSION['employee_number']."'
				 order by brnDesc";
	
	$resBrnches = $timesheetsadjustmentsObj->execQry($queryBrnches);
	$arrBrnches = $timesheetsadjustmentsObj->getArrRes($resBrnches);
	$arrBrnch = $timesheetsadjustmentsObj->makeArr($arrBrnches,'brnCode','brnDesc','All');


//count records
$qryIntMaxRec = "SELECT taj.empNo
				 FROM tblTK_TimesheetAdjustment as taj LEFT JOIN tblEmpMast as emp
				 ON taj.compCode = emp.CompCode
				 AND taj.empNo = emp.empNo 
				 WHERE taj.compCode = '{$_SESSION['company_code']}' $brnCodelist
				 ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND taj.tsStat='O'";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND taj.tsStat='P'";
        	}
			if($_GET['srchType'] == 3){
        		$qryIntMaxRec .= "AND taj.tsStat='A'";
        	}
        	if($_GET['srchType'] == 4){
        		$qryIntMaxRec .= "AND taj.empNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 5){
        		$qryIntMaxRec .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if ($_GET['brnCd']!=0){
				$qryIntMaxRec .= " AND emp.empbrnCode='".$_GET["brnCd"]."' ";
			}
			
        }
        $qryIntMaxRec .= "ORDER BY emp.empLastName ";
		
$resIntMaxRec = $timesheetsadjustmentsObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

if(empty($intOffset)){
	$intOffset = 0;
}

//display records
$qrygetTimesheetAdjustmentsDtl = "SELECT taj.compCode,taj.empNo,taj.tsDate,taj.dayType,emp.empFirstName,
									 emp.empMidName,emp.empLastName,emp.empNo,taj.seqNo,taj.adjBasic,taj.adjOt,
									 taj.adjNd,taj.adjHp,taj.adjEcola,taj.adjCtpa,taj.adjAdv,taj.tsStat, taj.userApproved
				   				FROM tblTK_TimesheetAdjustment taj 
				   				INNER JOIN tblEmpMast emp ON taj.compCode = emp.compCode 
									AND taj.empNo = emp.empNo 
				   				WHERE taj.compCode = '{$_SESSION['company_code']}' "; 
	if($_GET['isSearch'] == 1){
	       	if($_GET['srchType'] == 1){
	        	$qrygetTimesheetAdjustmentsDtl .= "AND taj.tsStat='O'";
	        }
	       	if($_GET['srchType'] == 2){
	        	$qrygetTimesheetAdjustmentsDtl .= "AND taj.tsStat='P'";
	        }
			if($_GET['srchType'] == 3){
        		$qrygetTimesheetAdjustmentsDtl .= "AND taj.tsStat='A'";
        	}
			if($_GET['srchType'] == 4){
        		$qrygetTimesheetAdjustmentsDtl .= "AND taj.empNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 5){
        		$qrygetTimesheetAdjustmentsDtl .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if ($_GET['brnCd']!=0){
				$qrygetTimesheetAdjustmentsDtl .= " AND emp.empbrnCode='".$_GET["brnCd"]."' ";
			}
	 }
	
	$qrygetTimesheetAdjustmentsDtl .= " $brnCodelist ORDER BY emp.empLastName limit $intOffset,$intLimit";
	
	$resgetTimesheetAdjustmentsDtl = $timesheetsadjustmentsObj->execQry($qrygetTimesheetAdjustmentsDtl);
	$arrgetTimesheetAdjustmentsDtl = $timesheetsadjustmentsObj->getArrRes($resgetTimesheetAdjustmentsDtl);

?>

<TABLE border ="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		
    <td height="4" colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Timesheet Adjustment</td>
	</tr>
	<tr>
		
		<td colspan="6" class="gridToolbar">
			&nbsp;
			
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.gif'  onclick="pager('timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
		</td>
	
	</tr>
	<tr>
	  <td class="parentGridDtl" valign="top">
			<!--header-->					
	    <TABLE cellpadding="1" cellspacing="1" border="0" class="hdrTable" width="100%">
				<tr>
					<td class="hdrLblRow" colspan="22">
						<FONT class="hdrLbl">Adjustment Detail</font><input type="hidden" id="hdnProcess" name="hdnProcess" /><input type="hidden" id="hdnSeqNo" name="hdnSeqNo" />
					</td>
				</tr>
				<tr>
					<td width="172" height="20" class="hdrInputsLvl">
						<a href="#" onclick="empLookup('../../../includes/employee_lookup_tna.php')">Employee No.</a>
					</td>
					<td width="7" height="20" class="hdrInputsLvl">
						:
					</td>
					<td width="337" height="20" class="gridDtlVal">
						<INPUT tabindex="9" class="inputs" type="text" name="txtAddEmpNo" id="txtAddEmpNo" onkeydown="getEmployee(event,this.value)"></td>
					<td width="409" height="20" colspan="6" class="gridDtlVal"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
					    <td width="23%"><span class="hdrInputsLvl" style="font-weight:bold">Current Period</span></td>
					    <td width="2%" class="hdrInputsLvl">:</td>
					    <td><input name="txtCurrentPeriod" type="text" class="inputs" id="txtCurrentPeriod" size="40" readonly="readonly"></td>
				      </tr>
				    </table></td>
				</tr>
			    <tr>
			      <td height="20" class="hdrInputsLvl">Employee Name</td>
			      <td height="20" class="hdrInputsLvl">:</td>
			      <td height="20" class="gridDtlVal"><input name="txtName" type="text" class="inputs" id="txtName" size="45" readonly="readonly"></td>
			      <td height="20" colspan="6"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			        <tr>
			          <td width="23%"><span class="hdrInputsLvl" style="font-weight:bold">Previous Period</span></td>
			          <td width="2%" class="hdrInputsLvl">:</td>
			          <td><span class="gridDtlVal">
			            <input name="txtPeriodToPost" type="text" class="inputs" id="txtPeriodToPost" size="40" readonly="readonly">
		              <input name="hdnPDYear" type="hidden" id="hdnPDYear" size="5" readonly="readonly">
			          </span><span class="gridDtlVal">
			          <input name="hdnPDNumber" type="hidden" id="hdnPDNumber" size="5" readonly="readonly">
			          </span></td>
		            </tr>
		          </table></td>
	      </tr>
			    <tr>
			      <td height="20" class="hdrInputsLvl">Payroll Group</td>
			      <td height="20" class="hdrInputsLvl">:</td>
			      <td height="20" class="gridDtlVal"><input name="txtPayGrp" type="text" class="inputs" id="txtPayGrp" size="20" readonly="readonly">
		          <input name="hdnPayGrp" type="hidden" id="hdnPayGrp" size="5" readonly="readonly"></td>
			      <td height="20" colspan="6" class="hdrInputsLvl"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			        <tr>
			          <td width="23%" class="hdrInputsLvl">Include  Allowances</td>
			          <td width="2%" class="hdrInputsLvl">:</td>
			          <td width="13%"><?=$timesheetsadjustmentsObj->DropDownMenu(array(''=>'No','Y'=>'Yes'),'cmbAllowance',$_GET['srchType'],'class="inputs" tabindex="16"');?></td>
			          <td width="3%">&nbsp;</td>
			          <td width="22%" class="hdrInputsLvl">Include  Advances </td>
			          <td width="2%" class="hdrInputsLvl">:</td>
			          <td width="35%"><?=$timesheetsadjustmentsObj->DropDownMenu(array(''=>'No','Y'=>'Yes'),'cmbAdvances',$_GET['srchType'],'class="inputs" tabindex="16"');?></td>
		            </tr>
		          </table></td>
	      </tr>
			    <tr>
			      <td height="20" class="hdrInputsLvl">Payroll Category</td>
			      <td height="20" class="hdrInputsLvl">:</td>
			      <td height="20" class="gridDtlVal"><input name="txtPayCat" type="text" class="inputs" id="txtPayCat" readonly="readonly">
		          <input name="hdnPayCat" type="hidden" id="hdnPayCat" size="5" readonly="readonly"></td>
			      <td width="121" height="20" class="hdrInputsLvl">Data  to Enter </td>
			      <td width="9" height="20" class="hdrInputsLvl">:</td>
			      <td height="20" colspan="4" class="gridDtlVal"><?=$timesheetsadjustmentsObj->DropDownMenu(array(''=>'','H'=>'Hours','A'=>'Amount'),'cmbAdjustmentType',$_GET['srchType'],'class="inputs" tabindex="16" onChange="setAdjustmentType(this.value);"');?></td>
	      </tr>
          		<tr>
                  <td colspan="6" height="15"></td>
                </tr>
				</TABLE>
<TABLE width="100%" cellpadding="1" cellspacing="1" border="0" align="center">
		  <tr>
          			<td align="center" bgcolor="#CCCCCC"></td>
       			</tr>
                <tr>
                	<td height="5"></td>
                </tr>
				<tr>
          			<td class="hdrLblRow"><FONT class="hdrLbl">Adjustment  Data in Hours</font></td>
       			</tr>
				<tr>
                  <td colspan="3"><table width="100%" border="0" cellspacing="1" cellpadding="1" class="childGrid">
                    <tr>
                      <td height="20" colspan="8" align="left" class="gridToolbar"><input type="button" name="btnSaveHrs" id="btnSaveHrs" value="Process Hours" disabled onClick="processHrs();"></td>
                    </tr>
                    <tr>
                      <td width="13%" class="gridDtlLbl" align="center" height="20">Date</td>
                      <td width="16%" class="gridDtlLbl" align="center" height="20">Day Type</td>
                      <td width="12%" class="gridDtlLbl" align="center" height="20">Hrs. Reg.</td>
                      <td width="12%" class="gridDtlLbl" align="center" height="20">Hrs. OT not &gt;8</td>
                      <td width="12%" class="gridDtlLbl" align="center" height="20">Hrs. OT &gt;8</td>
                      <td width="12%" class="gridDtlLbl" align="center" height="20">Hrs. ND</td>
                      <td width="12%" class="gridDtlLbl" align="center">Hrs. ND&gt;8</td>
                      <td width="11%" class="gridDtlLbl" align="center" height="20">Status</td>
                    </tr>
                    <tr align="center">
                      <td><input name="txtTSDate" type="text" disabled class="inputs" id="txtTSDate" onFocus=" validateDayType(this.id);" size="10">&nbsp;<img src="../../../images/cal_new.gif" onClick="displayDatePicker('txtTSDate', this);" style="cursor:pointer; visibility:hidden;" width="20" height="14" id="imgCalendar"></td>
                      <td><?=$timesheetsadjustmentsObj->DropDownMenu($timesheetsadjustmentsObj->makeArr($timesheetsadjustmentsObj->getDayType(),'dayType','dayTypeDesc','Select Day Type'),'cmbDayType',$_GET['srchType'],'class="inputs" tabindex="16" onChange="changeDayType(this.value);" disabled');?><input name="hdnDayType" type="hidden" id="hdnDayType" size="5" readonly="readonly"></td>
                      <td><input name="txtHrsReg" type="text" disabled class="inputs" id="txtHrsReg" size="15" onKeyDown="javascript:return dFilter (event.keyCode, this, '##.##');"></td>
                      <td><input name="txtHrsOTNG8" type="text" disabled class="inputs" id="txtHrsOTNG8" size="15" onKeyDown="javascript:return dFilter (event.keyCode, this, '##.##');"></td>
                      <td><input name="txtOTG8" type="text" disabled class="inputs" id="txtOTG8" size="15" onKeyDown="javascript:return dFilter (event.keyCode, this, '##.##');"></td>
                      <td><input name="txtHrsND" type="text" disabled class="inputs" id="txtHrsND" size="15" onKeyDown="javascript:return dFilter (event.keyCode, this, '##.##');"></td>
                      <td><input name="txtHrsNDG8" type="text" disabled class="inputs" id="txtHrsNDG8" size="15" onKeyDown="javascript:return dFilter (event.keyCode, this, '##.##');"></td>
                      <td><span class="gridDtlVal">
                        <?=$timesheetsadjustmentsObj->DropDownMenu(array(''=>'','A'=>'Approve','P'=>'Posted','O'=>'Open'),'cmbStat',$_GET['srchType'],'class="inputs" tabindex="16" disabled');?>
                      </span></td>
                    </tr>
                    <tr>
                      <td height="5"></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                      <td height="15"></td>
                </tr>
				<tr>
          			<td class="hdrLblRow"><FONT class="hdrLbl">Adjustment  Data in Amount</font></td>
       			</tr>
				<tr>
                  <td colspan="3"><table width="100%" border="0" cellspacing="1" cellpadding="1" class="childGrid">
                    <tr>
                      <td height="20" colspan="9" align="left" class="gridToolbar"><input type="button" name="btnSaveAmnt" id="btnSaveAmnt" value="Process Adjustment" disabled onClick="processAdjustment();"></td>
                    </tr>
                    <tr>
                      <td width="13%" class="gridDtlLbl" align="center" height="20">Date</td>
                      <td width="11%" class="gridDtlLbl" align="center">Basic</td>
                      <td width="11%" class="gridDtlLbl" align="center" height="20">Over Time</td>
                      <td width="11%" class="gridDtlLbl" align="center" height="20">Night  Diff</td>
                      <td width="11%" class="gridDtlLbl" align="center" height="20">HP Amount</td>
                      <td width="11%" class="gridDtlLbl" align="center" height="20">ECOLA</td>
                      <td width="11%" class="gridDtlLbl" align="center">CTPA</td>
                      <td width="10%" class="gridDtlLbl" align="center" height="20">Advances</td>
                      <td width="11%" class="gridDtlLbl" align="center" height="20">Status</td>
                    </tr>
                    <tr align="center">
                      <td><input name="txtTSAmntDate" type="text" disabled class="inputs" id="txtTSAmntDate" onFocus=" validateDayType(this.id);" size="10">&nbsp;<img src="../../../images/cal_new.gif" name="imgCalendarAmnt" width="20" height="14" id="imgCalendarAmnt" style="cursor:pointer; visibility:hidden;" onClick="displayDatePicker('txtTSAmntDate', this);"></td>
                      <td><input name="txtBasicAmnt" type="text" disabled class="inputs" id="txtBasicAmnt" size="10"></td>
                      <td><input name="txtOTAmnt" type="text" disabled class="inputs" id="txtOTAmnt" size="10"></td>
                      <td><input name="txtNDAmnt" type="text" disabled class="inputs" id="txtNDAmnt" size="10"></td>
                      <td><input name="txtHPAmnt" type="text" disabled class="inputs" id="txtHPAmnt" size="10"></td>
                      <td><input name="txtECOLAAmnt" type="text" disabled class="inputs" id="txtECOLAAmnt" size="10"></td>
                      <td><input name="txtCTPAAmnt" type="text" disabled class="inputs" id="txtCTPAAmnt" size="10"></td>
                      <td><input name="txtAdvancesAmnt" type="text" disabled class="inputs" id="txtAdvancesAmnt" size="10"></td>
                      <td><span class="gridDtlVal">
                        <?=$timesheetsadjustmentsObj->DropDownMenu(array(''=>'','A'=>'Approve','P'=>'Posted','O'=>'Open'),'cmbStatAmnt',$_GET['srchType'],'class="inputs" tabindex="16" disabled');?>
                      </span></td>
                    </tr>
                    <tr>
                      <td height="5"></td>
                    </tr>
                  </table></td>
                </tr>
                
                <tr>
				  <td colspan="4">&nbsp;</td>
				</tr>
				<tr><td class="hdrLblRow" colspan="4">
					  <FONT class="hdrLbl">Summary of Adjustments</font>
				</td></tr>
								
</TABLE>
	
	<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
        
<tr>
			<td colspan="13" class="gridToolbar">
						Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
            In 
            <?=$timesheetsadjustmentsObj->DropDownMenu(array('','Open','Posted','Approved','Employee Number','Lastname','Branch'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
            <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['seqNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
         	<FONT class="ToolBarseparator">|</font>	
			<a href="#"  id="btnEdit"onClick=""><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update Timesheet Adjustment" onclick="getSeqNo()"></a>                                     		
			<FONT class="ToolBarseparator">|</font>
            <a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" src="../../../images/edit_prev_emp.png"  onclick="updateLvTran('updateLvTran','timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approve Timesheet Adjustment" ></a>
            <FONT class="ToolBarseparator">|</font>
            <a href="#" id="deleTimesheestAdj" tabindex="3"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Timesheet Adjustment" onclick="delTimesheetAdj('delTimesheetAdj','timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
			<FONT class="ToolBarseparator">|</font>
			<?=$timesheetsadjustmentsObj->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');?>
			
		 </td>
		  
        <tr> 
		  <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
		  <td width="8%" align="center" class="gridDtlLbl">EMPLOYEE NO.</td>
		  <td width="17%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
		  <td width="9%" class="gridDtlLbl" align="center">TIMESHEET DATE</td>
          <td width="9%" class="gridDtlLbl" align="center">BASIC</td>
          <td width="9%" class="gridDtlLbl" align="center"> OVER TIME</td>
          <td width="9%" class="gridDtlLbl" align="center">NIGHT DIFF</td>
          <td width="9%" class="gridDtlLbl" align="center">HOLIDAY PAY</td>
          <td width="10%" class="gridDtlLbl" align="center">ALLOWANCE</td>
          <td width="9%" class="gridDtlLbl" align="center">ADVANCES</td>
		  <td width="9%" class="gridDtlLbl" align="center">STATUS</td>
          
        </tr>
		
		<?
					if(@$timesheetsadjustmentsObj->getRecCount($resgetTimesheetAdjustmentsDtl) > 0)
					{
						$i=0;
						$ctr=1;
							
						foreach (@$arrgetTimesheetAdjustmentsDtl as $timesheetAdjustmentsDtlVal)
						{
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							$f_color = ($timesheetAdjustmentsDtlVal["tsStat"]=='A'?"#CC3300":"");
				?>
                			<tr style="height:20px;"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                            	<td class="gridDtlVal" align="center">
                                <?php
									if((($timesheetAdjustmentsDtlVal["tsStat"]=='O') || (($timesheetAdjustmentsDtlVal["userApproved"]==$_SESSION['employee_number']) && ($timesheetAdjustmentsDtlVal["tsStat"]=='A'))))
									{
										
								?>
                                		<input class="inputs" type="checkbox" name="chkseq[]" value="<?=$timesheetAdjustmentsDtlVal['seqNo']?>" id="chkseq[]" />
                                <?php
									}	
								?>
          						</td>
                               
                               
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$timesheetAdjustmentsDtlVal["empNo"]?></td>
								<td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($timesheetAdjustmentsDtlVal["empLastName"].", ".$timesheetAdjustmentsDtlVal["empFirstName"]." ".substr($timesheetAdjustmentsDtlVal["empMidName"],0,1).".")?></td>
								<td class="gridDtlVal" align="CENTER"><font color="<?=$f_color?>"><?=date("Y-m-d", strtotime($timesheetAdjustmentsDtlVal["tsDate"]));?></td>
                                <td class="gridDtlVal" align="right"><font color="<?=$f_color?>"><?=number_format($timesheetAdjustmentsDtlVal["adjBasic"],2)?></td>
                                <td class="gridDtlVal" align="right"><font color="<?=$f_color?>"><?=number_format($timesheetAdjustmentsDtlVal["adjOt"],2)?></td>
                                <td class="gridDtlVal" align="right"><font color="<?=$f_color?>"><?=number_format($timesheetAdjustmentsDtlVal["adjNd"],2)?></td>
                                <td class="gridDtlVal" align="right"><font color="<?=$f_color?>"><?=number_format($timesheetAdjustmentsDtlVal["adjHp"],2)?></td>
                                <td class="gridDtlVal" align="right"><font color="<?=$f_color?>"><?=number_format($timesheetAdjustmentsDtlVal["adjEcola"]+$timesheetAdjustmentsDtlVal["adjCtpa"],2)?></td>
                                <td class="gridDtlVal" align="right"><font color="<?=$f_color?>"><?=number_format($timesheetAdjustmentsDtlVal["adjAdv"],2)?></td>
                            	<td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
									<?
										if ($timesheetAdjustmentsDtlVal['tsStat'] == 'O'){
											echo "Open";
										}elseif ($timesheetAdjustmentsDtlVal['tsStat'] == 'P'){
											echo "Posted";
										}elseif ($timesheetAdjustmentsDtlVal['tsStat'] == 'A'){
											echo "Approved";
										}
									?>			
								</td>
                            </tr>
                <?
							$ctr++;
						}
					}
				?>
                 <tr> 
                      <td colspan="13" align="center" class="childGridFooter"> 
                        <?
                            $pager->_viewPagerButton('timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['seqNo'].'&brnCd='.$_GET['brnCd']);
                        ?>
                      </td>
                </tr>
          </TABLE>
	  </td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $timesheetsadjustmentsObj->disConnect();?>
