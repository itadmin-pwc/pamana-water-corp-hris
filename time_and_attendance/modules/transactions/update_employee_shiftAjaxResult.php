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


	$updateEmpShiftObj = new transactionObj($_GET,$_SESSION);
	$updateEmpShiftObj->validateSessions('','MODULES');
	
	$pager = new AjaxPager(10,'../../../images/');
	
	switch($_GET["action"])
	{
		case 'getEmpInfo':
				$empInfo = $updateEmpShiftObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');
				
				$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
				$fld_txtEmpName = $empInfo[empLastName].", ".htmlspecialchars(addslashes($empInfo['empFirstName']))." ".$midName;
				
				$deptName = $updateEmpShiftObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
				$posName = $updateEmpShiftObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
				
				$fld_txtDeptPost = htmlspecialchars(addslashes($deptName["deptDesc"]))." - ".$posName["posDesc"];
				
				$arr_EmpTsInfo =  $updateEmpShiftObj->getTblData("tblTK_Timesheet", " and empNo='".$_GET['empNo']."'", " order by tsDate", "");
					
		break;
	}
	
	
	$weekName_arr = array('','FIRST WEEK', 'SECOND WEEK', 'THIRD WEEK');
	
?>

<input type="hidden" name="empBrnCode" id="empBrnCode" value="<?=$empInfo["empBrnCode"]?>" />
<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Update Employee Shift Application
		</td>
	</tr>
    
    
	<!--<tr>
		<td colspan="6" class="gridToolbar">&nbsp;
		&nbsp;
			<input type="radio" name="rdnSelType" id="rdnSelType" class="inputs" value="1"  /> By Individual
			&nbsp;<FONT class="ToolBarseparator">|</font>
			<input type="radio" name="rdnSelType" id="rdnSelType" class="inputs"  value="0" onclick="maintChoice('Add','','update_employee_shift.php','frmUpdateEmpShift','','','','txtSrch','cmbSrch')" /> By Mass Update	
        </td>
	</tr>-->
    
	<tr>
		<td class="parentGridDtl" valign="top">
			<!--header-->					
			<TABLE width="100%" cellpadding="1" cellspacing="1" border="0" class="hdrTable">
				<tr>
					<td class="hdrLblRow" colspan="15">
						<FONT class="hdrLbl"  id="hlprMsg">Application Detail</font>
					<input type="hidden" name="hd_txtIn" id="hd_txtIn" value="" />
			        <input type="hidden" name="hd_txtLout" id="hd_txtLout" value="" />
			        <input type="hidden" name="hd_txtLin" id="hd_txtLin" value="" />
			        <input type="hidden" name="hd_txtBout" id="hd_txtBout" value="" />
			        <input type="hidden" name="hd_txtBin" id="hd_txtBin" value="" />
			        <input type="hidden" name="hd_txtOut" id="hd_txtOut" value="" />                        
					</td>
				</tr>
                
                
				
                <tr>
					<td style="height:25px;" class="hdrInputsLvl" width="15%">
						<a href="#" onclick="empLookup('../../../includes/employee_lookup_tna.php')">Employee  No.</a>
					</td>
                    
					<td class="hdrInputsLvl" width="1%">
						:
					</td>
                    
					<td class="gridDtlVal">
						<INPUT tabindex="11" class="inputs" readonly="readonly" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" value="<?=$_GET["empNo"]?>" onkeydown="getEmployee(event,this.value)" >
					</td>
                    
					<td class="hdrInputsLvl" width="15%">
						Employee Name
					</td>
                    
					<td class="hdrInputsLvl" width="1%">
						:
					</td>

					<td class="gridDtlVal" colspan="4%">
						<INPUT class="inputs" readonly="readonly" type="text" name="txtEmpName" id="txtEmpName" style="width:100%;" value="<?=$fld_txtEmpName?>">
					</td>
                    
					<td class="hdrInputsLvl" width="15%">
						Dept. / Position
					</td>
                    
					<td class="hdrInputsLvl" width="1%">
						:
					</td>

					<td class="gridDtlVal" colspan="4%">
						<INPUT class="inputs" readonly="readonly" type="text" name="txtDeptPost" id="txtDeptPost" style="width:100%;" value="<?=$fld_txtDeptPost?>">
					</td>
				</tr>
                
                
                
                
                
                 <tr>
					<td style="height:25px;" class="hdrInputsLvl">
						Update Emp. Sched. By
					</td>
                    
					<td class="hdrInputsLvl">
						:
					</td>
                    
					<td class="gridDtlVal">
                    	<?=$updateEmpShiftObj->DropDownMenu(array('1'=>'Per Day','2'=>'Per Week'),'updateSchedBy',$updateSchedBy,'onChange=getUpdateSched(); disabled'); ?>
					
					</td>
                    
					<td class="hdrInputsLvl" width="10%">
						Shift Codes
					</td>
                    
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
                    	<?php
							$arrShifts = $updateEmpShiftObj->makeArr($updateEmpShiftObj->getListShift(),'shiftCode','shiftDesc','');
                            $updateEmpShiftObj->DropDownMenu($arrShifts,'shiftcode',$shiftcode,'onChange=getShiftCodeDetail(); disabled' );
						?>
					</td>
                    
					<td class="hdrInputsLvl" width="10%">
						
					</td>
                    
					<td class="hdrInputsLvl" width="5">
						
					</td>

					<td class="gridDtlVal" colspan="4" align="center">
						<input type="button" name="btnSave" id="btnSave" class="inputs" value="SAVE" onclick="saveUpdateEmpShiftDetail();" />
					</td>
				</tr>
                
			</TABLE>
			
            <TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
            	<tr style="height:25px;">
                	<td width="5%" class="gridDtlLbl" align="center" >SELECT</td>
                    <td width="9%" class="gridDtlLbl" align="center">DATE</td>
                    <td width="9%"class="gridDtlLbl" align="center" >DAY TYPE</td>		
                    <td width="9%" class="gridDtlLbl" align="center">IN</td>
                    <td width="9%" class="gridDtlLbl" align="center">LUNCH<br />OUT</td>
                    <td width="9%" class="gridDtlLbl" align="center">LUNCH<br />IN</td>
                    <td width="9%" class="gridDtlLbl" align="center">BRK <br />OUT</td>
                    <td width="9%" class="gridDtlLbl" align="center">BRK<br />IN</td>
                    <td width="6%" class="gridDtlLbl" align="center">OUT</td>
                    <td width="8%" class="gridDtlLbl" align="center">TS APP. TYPE</td>
                    <td width="3%" class="gridDtlLbl" align="center">CROSS DAY</td>
                    <td width="5%" class="gridDtlLbl" align="center">&nbsp;</td>

				</tr>
                
				
				<?php
					$weekCnt = 1;
                	$cntWeek_sel =1;
					
				?>
                		
                        
                <?php		
						if($arr_EmpTsInfo!="")
						{
							$ctrDays = 1;
							foreach($arr_EmpTsInfo as $arr_EmpTsInfo_val)
							{
				?>
                					
                <?php					
								if($weekCnt>7)
								{
									$weekCnt = 1;
				?>
                					
                <?php					
								}
								else
								{
									$weekCnt = $weekCnt;
								}
								
								if($weekCnt==1)
								{
                ?>
                					<tr>
                                    	<td><font style="font-family : Arial, Helvetica, sans-serif; color:#FF0000; font-size:10px; font-weight:bold" ><?=$weekName_arr[$cntWeek_sel]?></font></td>
                                    </tr>
                <?php
									$cntWeek_sel++;
								}				
				?>
                					
                                   
                                   <tr>
                                    	
                                        <td align="center"><input type="checkbox" class="inputs" name="chkDayEnabled<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>" onclick="enabledFields(<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>)" /></td>
                                        <td width="9%" align="center" class='gridDtlVal'><input type="text" class="inputs" readonly="readonly" name='txttsDate<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' id='txttsDate<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' value="<?=date("Y-m-d", strtotime($arr_EmpTsInfo_val["tsDate"]))?>" /></td>
                                         <td width="9%" align="left" class='gridDtlVal'><input type="text" class="inputs" readonly="readonly" value="<?=" ".strtoupper(date("l", strtotime($arr_EmpTsInfo_val["tsDate"])))?>" /></td>
                                         
                                        <td width="9%" align="center" class='gridDtlVal'><input type='text' class='inputs'  readonly="readonly" name='txtEtimeIn<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' id='txtEtimeIn<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>'  style='width:70%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsInfo_val["shftTimeIn"]?>'></td>
                                        <td width="9%" align="center" class='gridDtlVal'><input type='text' class='inputs' readonly="readonly" name='txtElunchOut<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' id='txtElunchOut<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>'  style='width:70%;  color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsInfo_val["shftLunchOut"]?>'></td>
                                        <td width="9%" align="center" class='gridDtlVal'><input type='text' class='inputs' readonly="readonly" name='txtElunchIn<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' id='txtElunchIn<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' style='width:70%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsInfo_val["shftLunchIn"]?>'></td>
                                        <td width="9%" align="center" class='gridDtlVal'><input type='text' class='inputs' readonly="readonly" name='txtEbrkOut<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' id='txtEbrkOut<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' style='width:70%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsInfo_val["shftBreakOut"]?>'></td>
                                        <td width="9%" align="center" class='gridDtlVal'><input type='text' class='inputs' readonly="readonly" name='txtEbrkIn<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' id='txtEbrkIn<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' style='width:70%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsInfo_val["shftBreakIn"]?>'></td>
                                        <td width="9%" align="center" class='gridDtlVal'><input type='text' class='inputs' readonly="readonly" name='txtEtimeOut<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' id='txtEtimeOut<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' style='width:70%; color:<?=$font_editedTs?>;' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='<?=$arr_EmpTsInfo_val["shftTimeOut"]?>'></td>
                                   		<?php
                                        	$appTypeDesc = $updateEmpShiftObj->getTblData("tblTK_AppTypes", " and tsAppTypeCd='".$arr_EmpTsInfo_val["tsAppTypeCd"]."'", "", "sqlAssoc");
										?>		
                                        <td width="6%" align="center" class='gridDtlVal'><input type='text' class='inputs' readonly="readonly" style='width:50%; color:<?=$font_editedTs?>;'  value='<?=$appTypeDesc["appTypeShortDesc"]?>'></td>
                                   		
                                        <input type="hidden" name='restDayTag<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>'	id='restDayTag<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>' value="" />
                                   		<td width="3%" align="center" class='gridDtlVal'><input type="checkbox" disabled name="chkCrossDay<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>" <?=($arr_EmpTsInfo_val["crossDay"]=='Y'?"checked":"")?>  /></td>
										<td width="3%" align="center" class='gridDtlVal'><span class="gridDtlLbl" style="cursor:pointer" onclick="copySched(<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>)"><img src="../../../images/copy.png" /></span> <span class="gridDtlLbl" style="cursor:pointer" onclick="pasteSched(<?=$ctrDays.date("w", strtotime($arr_EmpTsInfo_val["tsDate"]))?>)"><img src="../../../images/paste.png" /></span></td>                                   		
                                   </tr>
                <?php
								$ctrDays++;
								$weekCnt++;
							}
						}
						
                	
					
                ?>  
                        
                <input type="hidden" name="cntArray" id="cntArray" value="<?=sizeof($arr_EmpTsInfo)?>" />
                <input type="hidden" name="rdnSelected" id="rdnSelected" value="" />
                
			</TABLE>	
           
		
		</td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $updateEmpShiftObj->disConnect();?>

<script>
	function checkTag()
	{
		alert("GENARRA");
	}
</script>