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


	$csObj = new transactionObj($_GET,$_SESSION);
	$csObj->validateSessions('','MODULES');
	
	$pager = new AjaxPager(10,'../../../images/');
	
	
	
	$qryIntMaxRec = "SELECT     CsApp.refNo, CsApp.csDateFrom, CsApp.csShiftFromIn, CsApp.csShiftFromOut, CsApp.csDateTo, CsApp.csShiftToIn, CsApp.csHiftToOut, 
							CsApp.csReason, CsApp.crossDay, empmast.empLastName, empmast.empFirstName, empmast.empMidName, seqNo,csStat,userApproved
							FROM         tblTK_CSApp CsApp INNER JOIN
							tblEmpMast empmast ON CsApp.empNo = empmast.empNo
							WHERE     (CsApp.compcode = '".$_SESSION["company_code"]."') AND (empmast.compCode = '".$_SESSION["company_code"]."')
							";
							
							if($_GET['isSearch'] == 1){
								if($_GET['srchType'] == 0){
									$qryCSApp .= "AND csStat='A'";
								}
								
								if($_GET['srchType'] == 1){
									$qryCSApp .= "AND csStat='H'";
								}
								
								if($_GET['srchType'] == 2){
									$qryIntMaxRec .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
								}
								
								if($_GET['srchType'] == 3){
									$qryIntMaxRec .= "AND CsApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
								}
								
								if($_GET['srchType'] == 4){
									$qryIntMaxRec .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
								}
							}
							
	$qryIntMaxRec.=			"ORDER BY CsApp.refNo, CsApp.csDateFrom, CsApp.csDateTo, empmast.empLastName, empmast.empFirstName";
	
	$resIntMaxRec = $csObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryCSApp = "SELECT     TOP $intLimit CsApp.refNo, CsApp.csDateFrom, CsApp.csShiftFromIn, CsApp.csShiftFromOut, CsApp.csDateTo, CsApp.csShiftToIn, CsApp.csHiftToOut, 
							CsApp.csReason, CsApp.crossDay, empmast.empLastName, empmast.empFirstName, empmast.empMidName, seqNo, csStat,userApproved
							FROM         tblTK_CSApp CsApp INNER JOIN
							tblEmpMast empmast ON CsApp.empNo = empmast.empNo
							WHERE     (CsApp.compcode = '".$_SESSION["company_code"]."') AND (empmast.compCode = '".$_SESSION["company_code"]."')
							AND seqNo not in 
									(Select TOP $intOffset seqNo from tblTK_CsApp  CsApp
									WHERE (CsApp.compCode = '".$_SESSION["company_code"]."') "; 
									if($_GET['isSearch'] == 1){
										if($_GET['srchType'] == 0){
											$qryCSApp .= "AND csStat='A'";
										}
										
										if($_GET['srchType'] == 1){
											$qryCSApp .= "AND csStat='H'";
										}
										
										if($_GET['srchType'] == 2){
											$qryCSApp .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 3){
											$qryCSApp .= "AND CsApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 4){
											$qryCSApp .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
										}
									}
		$qryCSApp .=				"ORDER BY CsApp.refNo, CsApp.csDateFrom, CsApp.csDateTo, empmast.empLastName, empmast.empFirstName)";
							
							if($_GET['isSearch'] == 1){
										if($_GET['srchType'] == 0){
											$qryCSApp .= "AND csStat='A'";
										}
										
										if($_GET['srchType'] == 1){
											$qryCSApp .= "AND csStat='H'";
										}
										
										if($_GET['srchType'] == 2){
											$qryCSApp .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 3){
											$qryCSApp .= "AND CsApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 4){
											$qryCSApp .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
										}
									}
							
	$qryCSApp.=			"ORDER BY CsApp.refNo, CsApp.csDateFrom, CsApp.csDateTo, empmast.empLastName, empmast.empFirstName;";
	
	$resCSAppList = $csObj->execQry($qryCSApp);
	$arrCSAppList = $csObj->getArrRes($resCSAppList);
	
	
	if($_SESSION['user_level']==3){
		$btAppDel_Dis = "hidden";
	}
	

	
?>
<input type="hidden" name="shiftDayType" id="shiftDayType" value="" />
<input type="hidden" name="empbrnCode" id="empbrnCode" value="" />
<input type="hidden" name="empPayGrp" id="empPayGrp" value="" />
<input type="hidden" name="empPayCat" id="empPayCat" value="" />

<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Change Shift Application
		</td>
	</tr>
    
	<tr>
		<td colspan="6" class="gridToolbar">
			&nbsp;
			<!--<a href="#" id="newEarn" tabindex="1"><IMG class="toolbarImg" src="../../../images/application_form_add.png"  onclick="newRef('NEWREFNO'); validateMod('NEWREFNO');" title="New OB Record"></a>
			
			<FONT class="ToolBarseparator">|</font>-->
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.png'  onclick="pager('csAjaxResult.php','csCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
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
					<!--<td class="hdrInputsLvl" width="10%">
						Reference NO.
					</td>
                    
					<td class="hdrInputsLvl" width="5">
						:
					</td>
                    
					<td class="gridDtlVal" width="18%">
						<INPUT tabindex="5" class="inputs" type="text" name="refNo" id="refNo" size="10" value="<?=$refNo?>" readonly onkeyup="return editRefNo('editRef',this.value,event)">
						<font id="refNoCont"></font>
					</td>-->
                    
                    <td class="hdrInputsLvl" width="10%">
						Date Filed
					</td>
                    
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<input tabindex="10" class="inputs" type="text" name="dateFiled" readonly="readonly" id="dateFiled" size="10"
							 value="<? 	
							 			$format="m/d/Y";
										$strf=date($format);
										echo("$strf"); 
									?>"
										>
									
										<img src="../../../images/cal_new.png" onClick="displayDatePicker('dateFiled', this);" style="cursor:pointer;" width="20" height="14">
					</td>
                    
                    <td class="hdrInputsLvl" width="10%">
					</td>
                    
					<td class="hdrInputsLvl" width="5">
					</td>
                    
					<td class="hdrInputsLvl" width="18%" colspan="4">
						<!--<input type="radio" name="chkStat" id="chkStat" class="inputs" />Permanent
                        <input type="radio" name="chkStat" id="chkStat"  class="inputs" />Temporary
                        <input type="radio" name="chkStat" id="chkStat" class="inputs" />Once Only-->
					</td>
                </tr>
                
                <tr>
					<td class="hdrInputsLvl">
						<a href="#" onclick="empLookup('../../../includes/employee_lookup.php')">Employee  No.</a>
					</td>
                    
					<td class="hdrInputsLvl">
						:
					</td>
                    
					<td class="gridDtlVal">
						<INPUT tabindex="11" class="inputs" readonly="readonly" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" onkeydown="getEmployee(event,this.value)" >
					</td>
                    
					<td class="hdrInputsLvl" width="10%">
						Employee Name
					</td>
                    
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<INPUT class="inputs" readonly="readonly" type="text" name="txtEmpName" id="txtEmpName" size="40" value="">
					</td>
                    
					<td class="hdrInputsLvl" width="10%">
						Dept. / Position
					</td>
                    
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<INPUT class="inputs" readonly="readonly" type="text" name="txtDeptPost" id="txtDeptPost" size="40" value="">
					</td>
				</tr>
                
			</TABLE>
			
            <TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
            	<tr style="height:25px;">
                    <td class="gridDtlLbl" align="center" colspan="3">OFFICIAL SHIFT</td>
                    <td class="gridDtlLbl" align="center" colspan="3">CS SCHEDULE</td>
                    <!--<td class="gridDtlLbl" align="center" >PAYROLL PERIOD COVERED</td>	-->
                    <td class="gridDtlLbl" align="center" >CROSS DATE</td>
                    <td class="gridDtlLbl" align="center" >REMARKS / REASON(S)</td>		
                    <td  class="gridDtlLbl" align="center">ACTION</td>
				</tr>
                
				<tr style="height:20px;">
                    <td width="10%" class="gridDtlLbl" align="center">DATE</td>
                    <td width="6%" class="gridDtlLbl" align="center">IN</td>		
                    <td width="6%" class="gridDtlLbl" align="center">OUT</td>
                    <td width="10%" class="gridDtlLbl" align="center">DATE</td>
                    <td width="6%" class="gridDtlLbl" align="center">IN</td>		
                    <td width="6%" class="gridDtlLbl" align="center">OUT</td>
                   <!-- <td width="8%" class="gridDtlLbl" align="center"></td>-->
                   <td width="5%" class="gridDtlLbl" align="center"></td>
                    <td width="15%" class="gridDtlLbl" align="center"></td>
                    <td width="5%" class="gridDtlLbl" align="center"></td>
				</tr>
                
                <tr>
                	<td  align="center">
                    	<input tabindex="10" class="inputs" type="text" name="csDateFrom" readonly="readonly" id="csDateFrom" size="10" onfocus="getEmpShift(<?=$_GET["empNo"]?>);"
							 value="<? 	
							 			$format="m/d/Y";
										$strf=date($format);
										echo("$strf"); 
									?>" >
									
										<img src="../../../images/cal_new.png" onClick="displayDatePicker('csDateFrom', this);" style="cursor:pointer;" width="20" height="14">
					
                    </td>
                    <td><input type="text" readonly="readonly" class="inputs" name="schedTimeIn"  id="schedTimeIn" style="width:100%;" value="" /></td>
                    <td><input type="text" readonly="readonly" class="inputs" name="schedTimeOut"  id="schedTimeOut" style="width:100%;" value="" /></td>
                  
                  	<td  align="center">
                    	<input tabindex="10" class="inputs" type="text" name="csDateTo" readonly="readonly" id="csDateTo" size="10"
							 value="<? 	
							 			$format="m/d/Y";
										$strf=date($format);
										echo("$strf"); 
									?>" >
									
										<img src="../../../images/cal_new.png" onClick="displayDatePicker('csDateTo', this);" style="cursor:pointer;" width="20" height="14">
					
                    </td>
                    
                     
                   
                    <td><input type='text' class='inputs' name='csTimeIn' id='csTimeIn'  style='width:100%;' disabled="disabled" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value=''></td>
                    <td><input type='text' class='inputs' name='csTimeOut' id='csTimeOut'  style='width:100%;' disabled="disabled" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value=''></td>
                  	<td align="center"><input type="checkbox" name="chkCrossDay" id="chkCrossDay" class="inputs" disabled="disabled" /></td>
                  
                   <!-- <td>
                    	<?php
							
							//$arrPayPd = $csObj->makeArr($csObj->getPeriodGtOpnPer($_SESSION["company_code"],$_GET["empPayGrp"],$_GET['empPayCat'],$_GET["payPayable"]),'pdSeries','pdPayable','');
							//$csObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis.'class="inputs" style="width:100%;" ');
						?>
                   	
                    </td>-->
                    <td><input type="text" class="inputs" name="csreason"  id="csreason" style="width:100%;" value="" disabled="disabled" /></td>
                   	<td align="center"><input type="button" class="inputs" name="btnSave" id="btnSave" value='SAVE' disabled="disabled" onClick="saveCsDetail();"></td>
                </tr>
                
                
			</TABLE>	
            <BR />
			<TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
            	<tr>
					<td class="hdrLblRow" colspan="11">
						<FONT class="hdrLbl"  id="hlprMsg">Summary of Application</font>
					</td>
				</tr>
                
                <tr>
                    <td colspan="11" class="gridToolbar">
                                Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
                        In 
                        <?=$csObj->DropDownMenu(array('Approved', 'Held', 'Ref. No.','Employee No.','Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
                          <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('csAjaxResult.php','csCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>','../../../images/')"> 
                    	<FONT class="ToolBarseparator">|</font>
                        <a href="#" id="updateEarn" tabindex="3"><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update CS Application" 	onclick="getSeqNo()"></a>
                        <FONT class="ToolBarseparator">|</font>
                        <a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" id="btnApp" style="visibility:<?=$btAppDel_Dis?>"  src="../../../images/edit_prev_emp.png"  onclick="upObTran('Approved','csCont.php','csCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved CS Application" ></a>
                        <FONT class="ToolBarseparator">|</font>
                        <a href="#" id="deleEarn" tabindex="3"><img class="toolbarImg" id="btnDel" style="visibility:<?=$btAppDel_Dis?>"  src="../../../images/application_form_delete.png" title="Delete CS Application" onclick="upObTran('Delete','csAjaxResult.php','csCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');"></a>
                     	
                       
                       </td>	
                   
         		</tr>
                     
                  
                <tr style="height:25px;">
                	<td width="1%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
                	<td width="3%" class="gridDtlLbl" align="center">REF. NO</td>
                    <td width="8%" class="gridDtlLbl" align="center">CS DATE FROM</td>
                    <td width="8%" class="gridDtlLbl" align="center">CS FROM IN</td>
					<td width="8%" class="gridDtlLbl" align="center">CS FROM OUT</td>
                    <td width="8%" class="gridDtlLbl" align="center">CS DATE TO</td>
                    <td width="8%" class="gridDtlLbl" align="center">CS TO IN</td>
					<td width="8%" class="gridDtlLbl" align="center">CS TO OUT</td>
          			<td width="2%" class="gridDtlLbl" align="center">CROSS DATE</td>
                    <td width="15%" class="gridDtlLbl" align="center">REASON</td>
                    <td width="15%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
				</tr>
                
                <?php
					if($csObj->getRecCount($resCSAppList) > 0)
					{
						$i=0;
						$ctr=1;
							
						foreach ($arrCSAppList as $arrCSAppList_val)
						{
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							$f_color = ($arrCSAppList_val["csStat"]=='A'?"#CC3300":"");
				?>
                			<tr style="height:20px;"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                            	<?php
								
									if(($arrCSAppList_val["csStat"]=='H') || (($arrCSAppList_val["userApproved"]==$_SESSION['employee_number']) && ($arrCSAppList_val["csStat"]=='A')))
									{
								?>
                                		<td class="gridDtlVal" align="center"><input class="inputs" type="checkbox" name="chkseq[]" value="<?=$arrCSAppList_val['seqNo']?>" id="chkseq[]"  /></td>
                                <?php
									}
									else
									{
								?>
                                		<td class="gridDtlVal" align="center"></td>
                           		<?php } ?>
                           	
                                <td class="gridDtlVal" align="center" ><font color="<?=$f_color?>"><?=$arrCSAppList_val["refNo"]?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("m/d/Y", strtotime($arrCSAppList_val["csDateFrom"]))?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrCSAppList_val["csShiftFromIn"]?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrCSAppList_val["csShiftFromOut"]?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("m/d/Y", strtotime($arrCSAppList_val["csDateTo"]))?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrCSAppList_val["csShiftToIn"]?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrCSAppList_val["csHiftToOut"]?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=($arrCSAppList_val["crossDay"]=="Y"?"YES":"")?></td>
                                <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($arrCSAppList_val["csReason"])?></td>
                                <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($arrCSAppList_val["empLastName"].", ".$arrCSAppList_val["empFirstName"]." ")?></td>
                            </tr>
                <?php
							$ctr++;
						}
					}
				?>
                 <tr> 
                      <td colspan="11" align="center" class="childGridFooter"> 
                        <?
                            $pager->_viewPagerButton('csAjaxResult.php','csCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo']);
                        ?>
                      </td>
                </tr>
            </TABLE>
		
		</td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $csObj->disConnect();?>

