<?php
	/*
		Created By	:	Genarra Jo-Ann S. Arong
		Date Created : 	08252010
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("transaction_obj.php");

	$Obj = new transactionObj($_GET,$_SESSION);
	$Obj->validateSessions('','MODULES');
	
	$pager = new AjaxPager(10,'../../../images/');
	
	$branch = $_SESSION['branchCode'];

	//08-09-2023 AUTO EMPLOYEE LOOKUP
	$empInfo = $Obj->getEmployee($_SESSION['company_code'],$_SESSION['employeenumber'],'');
	$arr_ViolationDesc = $Obj->makeArr($Obj->getTblData("tblTK_ViolationType", "", " order by violationDesc", ""), 'violationCd','violationDesc','');
	$allowSave = true;
	$disabled = $allowSave ? '' : 'disabled';
?>

<input type="hidden" name="empPayGrp" id="empPayGrp" value="<?=$paygroup?>" />
<input type="hidden" name="empPayCat" id="empPayCat" value="<?=$paycat?>" />
<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Timesheet Correction Application
		</td>
	</tr>
    
	<tr>
		<td colspan="6" class="gridToolbar">
			&nbsp;
			<!--<a href="#" id="newEarn" tabindex="1"><IMG class="toolbarImg" src="../../../images/application_form_add.png"  onclick="newRef('NEWREFNO'); validateMod('NEWREFNO');" title="New OB Record"></a>
			
			<FONT class="ToolBarseparator">|</font>-->
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.png'  onclick="pager('ts_correction_application_AjaxResult.php','frmTSA','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
			
        </td>
	</tr>
    
	<tr>
		<td class="parentGridDtl" valign="top">
			<!--header-->					
			<TABLE width="100%" cellpadding="1" cellspacing="1" border="0" class="hdrTable">
				<tr>
					<td class="hdrLblRow" colspan="15">
						<FONT class="hdrLbl"  id="hlprMsg">Application Detail</font>
					</td>
				</tr>
                
				<tr>
					<td class="hdrInputsLvl" width="92">&nbsp;</td>
					<td class="hdrInputsLvl" width="7">&nbsp;</td>
					<td class="gridDtlVal" colspan="4">
						<input tabindex="10" class="inputs" type="hidden" name="dateFiled" readonly="readonly" id="dateFiled" size="10"
							 value="<? 	
							 			$format="Y-m-d";
										$strf=date($format);
										echo("$strf"); 
									?>"
										></td>
					<td class="hdrInputsLvl" width="96">
						
					</td>
					<td class="hdrInputsLvl" width="172">
						
					</td>
					<td class="gridDtlVal" colspan="4">
						
					</td>
				</tr>
                <tr>
					<td class="hdrInputsLvl">
						<?php
							if ($_SESSION['user_level'] == 3 || $managerApporver)  {
						?>
							Employee No.
						<?php
							}else{
						?>
							<a href="#" onclick="empLookup('../../../includes/employee_lookup_tna.php')">Employee  No.</a>
						<?php
							}
						?>
					</td>
                    
					<td class="hdrInputsLvl">
						:
					</td>
                    
					<td width="132" class="gridDtlVal">
						<?php
							if ($_SESSION['user_level'] == 3 || $managerApporver)  {
						?>
							<INPUT tabindex="11" class="inputs" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" value="<?=$_SESSION['employeenumber'];?>" readonly>
						<?php
							}else{
						?>
							<INPUT tabindex="11" class="inputs" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" onkeydown="getEmployee(event,this.value)" value="<?=$_SESSION['employeenumber'];?>" readonly>
						<?php
							}
						?>
				  </td>
                    
					<td class="hdrInputsLvl" width="102">
						Employee Name
					</td>
                    
					<td class="hdrInputsLvl" width="7">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<INPUT class="inputs" readonly="readonly" type="text" name="txtEmpName" id="txtEmpName" size="40" value="<?=$fullname?>">
			    </td>
                    
					<td class="hdrInputsLvl" width="98">
						Dept. / Position
					</td>
                    
					<td class="hdrInputsLvl" width="7">
						:
					</td>

					<td width="338" colspan="4" class="gridDtlVal">
						<INPUT class="inputs" readonly="readonly" type="text" name="txtDeptPost" id="txtDeptPost" size="50" value="<?=$position?>">
					</td>
				</tr>
                
			</TABLE>
			<br>
            <TABLE width="100%" cellpadding="0" cellspacing="1" class="" border="1px solid black" align="center">
				<tr height="25px">
                	<td class="gridDtlLbl" align="center">TS DATE</td>
                    <td class="gridDtlLbl" align="center">Time In</td>
                    <td class="gridDtlLbl" align="center">Lunch Out</td>
                    <td class="gridDtlLbl" align="center">Lunch In</td>
                    <td class="gridDtlLbl" align="center">Time Out</td>
                    <td width="25%" class="gridDtlLbl" align="center">Remarks</td>
                    <td class="gridDtlLbl" align="center">Action</td>
				</tr>
				<tr>
					<td class="gridDtlVal" align="center">
						<input tabindex="10" class="inputs" type="text" name="obDate" readonly="readonly" id="obDate" size="10" onfocus="getEmpShift(<?=$_GET['empNo']?>);" 
						value="<? 	
							 	$format="Y-m-d";
								$strf=date($format);
								echo("$strf"); 
						?>" >
						<img src="../../../images/cal_new.png" onClick="displayDatePicker('obDate', this);" style="cursor:pointer;" width="20" height="14">
						</td>
					<td class="gridDtlVal" align="center">
						<input name='timeIn' type='text' style="text-align:center;" <?=$disabled?> class='inputs' id='timeIn'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='' size="5">
                      	<span class="gridDtlVal"></span>
					</td>
					<td class="gridDtlVal" align="center">
						<input name='lunchOut' type='text' style="text-align:center;" <?=$disabled?> class='inputs' id='lunchOut'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='' size="5">
						<span class="gridDtlVal"></span>
					</td>
					<td class="gridDtlVal" align="center">
						<input name='lunchIn' type='text' style="text-align:center;" <?=$disabled?> class='inputs' id='lunchIn'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='' size="5">
						<span class="gridDtlVal"></span>
					</td>
					<td class="gridDtlVal" align="center">
						<input name='timeOut' type='text' style="text-align:center;" <?=$disabled?> class='inputs' id='timeOut'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='' size="5">
						<span class="gridDtlVal"></span>
					</td>
					<td class="gridDtlVal" align="center">
						<?php $Obj->DropDownMenu($arr_ViolationDesc,'violationCd',$arr_EmpTsCorrInfo["editReason"], ' style="width:100%;" class="gridDtlVal" onChange=getShiftCodeDetail(this.value);'); ?>
					</td>
					<td class="gridDtlVal" align="center">
						<input type='button' class= 'inputs' name='btnSave' id="btnSave" value='SAVE' <?=$disabled?> onClick="saveTSADetail();" >
					</td>
				</tr>
			</TABLE>	
			<br>
            <TABLE width="100%" cellpadding="0" cellspacing="1" border="1px solid black" class="" align="center">
				<tr>
                	<td class="gridDtlLbl" align="center" rowspan="2">TS DATE</td>
                    <td height="20px" class="gridDtlLbl" align="center" colspan="4">Schedule</td>
                    <td height="20px" class="gridDtlLbl" align="center" colspan="4" style="background-color:mistyrose;">Actual</td>
				</tr>
				<tr>
                	<!-- <td width="12%" class="gridDtlLbl" align="center">TS DATE</td> -->
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Time In</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Lunch Out</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Lunch In</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Time Out</td>
					<td height="20px width="13%" class="gridDtlLbl" align="center" style="background-color:mistyrose;">Time In</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center" style="background-color:mistyrose;">Lunch Out</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center" style="background-color:mistyrose;">Lunch In</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center" style="background-color:mistyrose;">Time Out</td>
				</tr>
				<tr>
					<td class="gridDtlVal" align="center">00/00/0000</td>
					<td class="gridDtlVal" align="center">00:00</td>
					<td class="gridDtlVal" align="center">00:00</td>
					<td class="gridDtlVal" align="center">00:00</td>
					<td class="gridDtlVal" align="center">00:00</td>
					<td class="gridDtlVal" align="center">00:00</td>
					<td class="gridDtlVal" align="center">00:00</td>
					<td class="gridDtlVal" align="center">00:00</td>
					<td class="gridDtlVal" align="center">00:00</td>
				</tr>
			</TABLE>
      <BR />
			<TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
            	<tr>
					<td class="hdrLblRow" colspan="21">
						<FONT class="hdrLbl"  id="hlprMsg">Summary of Application</font>
					</td>
				</tr>
                
                <tr>
                    <td colspan="21" class="gridToolbar">
                                Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
                        In 
                        <?=$Obj->DropDownMenu(array('','Approved', 'Held', 'Ref. No.','Employee No.','Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
                        <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('ts_correction_application_AjaxResult.php','frmTSA','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
                        <FONT class="ToolBarseparator">|</font>	
						<a href="#"  id="btnEdit"onClick=""><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update OB Application" onclick="getSeqNo()"></a> 
						<?
                        if($_SESSION['user_release']=="Y" || $_SESSION['user_level'] == 2){
                        ?>
                        <FONT class="ToolBarseparator">|</font>
                        <a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" id="btnApp" src="../../../images/edit_prev_emp.png"  onclick="upObTran('Approve','ts_correction_application_AjaxResult.php','frmTSA',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved OB Application" ></a>	
                        <?
						}
						?>						                   
                        <FONT class="ToolBarseparator">|</font>    
                            <a href="#" id="deleEarn" tabindex="3"><img class="toolbarImg" id="btnDel" src="../../../images/application_form_delete.png" title="Delete OB Application" onclick="delObTran('Delete','ts_correction_application_AjaxResult.php','frmTSA',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a> <font class="ToolBarseparator">|</font>
                <?=$Obj->DropDownMenu($brn,'brnCd',$_GET['brnCd'],'class="inputs"');?>
                    </td>
         		</tr>
                
                <tr style="height:25px;">
                	<td width="2%" class="gridDtlLbl" align="center" rowspan="2">
						<input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/>
					</td>
                	<td class="gridDtlLbl" align="center" rowspan="2">EMPLOYEE NO.</td>
                	<td class="gridDtlLbl" align="center" rowspan="2">EMPLOYEE</td>
                    <td class="gridDtlLbl" align="center" rowspan="2">TS DATE</td>
                    <td class="gridDtlLbl" align="center" colspan="4">Actual</td>
                    <td class="gridDtlLbl" align="center" colspan="4">Request</td>
					<td class="gridDtlLbl" align="center" rowspan="2">PURPOSE</td>
                    <td class="gridDtlLbl" align="center" rowspan="2">&nbsp;</td>
				</tr>
				<tr style="height:25px;">
					<!--checkbox-->
					<!--empNo-->
					<!--empName-->
					<td height="20px width="13%" class="gridDtlLbl" align="center">Time In</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Lunch Out</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Lunch In</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Time Out</td>
					<td height="20px width="13%" class="gridDtlLbl" align="center">Time In</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Lunch Out</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Lunch In</td>
                    <td height="20px width="13%" class="gridDtlLbl" align="center">Time Out</td>
					<!--purpose-->
					<!--&nbsp-->
				</tr>
                
                <?php
					if($Obj->getRecCount($resOBAppList) > 0)
					{
						$i=0;
						$ctr=1;
							
						foreach ($arrOBAppList as $arrOBAppList_val)
						{
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							if($timeKeeperApprover) {
								$f_color = ($arrOBAppList_val["obStat"]=='A'?"#CC3300":"");
							}elseif($managerApporver) {
								$f_color = ($arrOBAppList_val["mStat"]=='A'?"#CC3300":"");
							}elseif($timeKeeper) {
								$f_color = ($arrOBAppList_val["obStat"]=='A'?"#CC3300":"");
							}elseif($_SESSION['user_level'] == 3){
								$f_color = ($arrOBAppList_val["mStat"]=='A'?"#CC3300":"");
							}
							
							$obDestination = $Obj->getBranchesString($arrOBAppList_val["compCode"],explode(',', $arrOBAppList_val["obDestination"]));
				?>
                			<tr style="height:20px;" title="<?=($arrOBAppList_val["obStat"]=='A'?"APPROVED":"HELD");?>"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                            	<td class="gridDtlVal" align="center">
                            	<?php 
									if($timeKeeperApprover || $timeKeeper || $arrOBAppList_val["mStat"]=='H')
									{
								?>
                            			<input class="inputs" type="checkbox" name="chkseq[]" value="<?=$arrOBAppList_val['seqNo']?>" id="chkseq[]" />
                                <?php
									}
								?>	
                                </td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrOBAppList_val["empNo"]?></td>
                                <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($arrOBAppList_val["empLastName"].", ".$arrOBAppList_val["empFirstName"]." ")?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("Y-m-d", strtotime($arrOBAppList_val["obDate"]))?></td>
                                 <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($obDestination)?></td>
                                 <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=($arrOBAppList_val["crossDay"]=="Y"?"YES":"");?></td>
                                 <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=($arrOBAppList_val["hrs8Deduct"]=="Y"?"YES":"");?></td>
                                <td class="gridDtlVal" align="left"><font color="<?=$f_color?>">
								 <?
								if(is_numeric($arrOBAppList_val["obReason"])){
									$OBRes=$Obj->getTblData("tblTK_Reasons "," and stat='A' and reason_id='".$arrOBAppList_val["obReason"]."'"," order by reason","sqlAssoc");
									echo $OBRes['reason'];	
								}
								else{
									echo strtoupper($arrOBAppList_val["obReason"]);	
								}
								 ?></td>
                                 <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrOBAppList_val["obActualTimeIn"]?></td>
                                 <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrOBAppList_val["obActualTimeOut"]?></td>
                                 <td class="gridDtlVal" align="center">
								 <?
									if($arrOBAppList_val["obStat"]=="A")
									{
								 ?>
                                	<a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" id="btnApp" src="../../../images/edit_prev_emp.png"  onclick="disObTran('<?=$arrOBAppList_val['seqNo']?>','ts_correction_application_AjaxResult.php','frmTSA',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Set to Active OB Application" ></a>     	
                                 <?
									}
								 ?>
                                 </td>
                               
                            </tr>
                <?php
							$ctr++;
						}
					}
				?>
                 <tr> 
                      <td colspan="14" align="center" class="childGridFooter"> 
                        <?
                            $pager->_viewPagerButton('ts_correction_application_AjaxResult.php','frmTSA',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo']);
                        ?>
                      </td>
                </tr>
            </TABLE>
		
		</td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $Obj->disConnect();?>

<script>
	
</script>

