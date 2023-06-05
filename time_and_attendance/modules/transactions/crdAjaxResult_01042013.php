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


	$crdObj = new transactionObj($_GET,$_SESSION);
	$crdObj->validateSessions('','MODULES');
	
	$pager = new AjaxPager(10,'../../../images/');
	
	switch($_GET["action"])
	{
		case 'getEmpInfo':
		
				$empInfo = $crdObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');
				
				
				$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
				$fld_txtEmpName = $empInfo[empLastName].", ".htmlspecialchars(addslashes($empInfo['empFirstName']))." ".$midName;
				
				$deptName = $crdObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
				$posName = $crdObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
				
				$fld_txtDeptPost = htmlspecialchars(addslashes($deptName["deptDesc"]))." - ".$posName["posDesc"];
				
				if($empInfo['empRestDay']!="")
					$empCurrRestDay = explode(",", $empInfo['empRestDay']);
				
						
		break;
	}
	
	$qryIntMaxRec = "SELECT     RdApp.refNo, RdApp.dateFiled, RdApp.cRDDateFrom, RdApp.cRDDateTo, RdApp.cRDReason, 
							empmast.empLastName, empmast.empFirstName, empmast.empMidName, seqNo, cRDStat,userApproved
							FROM         tblTK_ChangeRDApp RdApp INNER JOIN
							tblEmpMast empmast ON RdApp.empNo = empmast.empNo
							WHERE     (RdApp.compcode = '".$_SESSION["company_code"]."') AND (empmast.compCode = '".$_SESSION["company_code"]."')
							";
							
							if($_GET['isSearch'] == 1){
								if($_GET['srchType'] == 0){
									$qryRdApp .= "AND cRDStat='A'";
								}
								
								if($_GET['srchType'] == 1){
									$qryRdApp .= "AND cRDStat='H'";
								}
								
								if($_GET['srchType'] == 2){
									$qryIntMaxRec .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
								}
								
								if($_GET['srchType'] == 3){
									$qryIntMaxRec .= "AND RdApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
								}
								
								if($_GET['srchType'] == 4){
									$qryIntMaxRec .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
								}
							}
							
	$qryIntMaxRec.=			"ORDER BY RdApp.refNo, RdApp.dateFiled, empmast.empLastName, empmast.empFirstName";
	
	$resIntMaxRec = $crdObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
							
	$qryRdApp = "SELECT     TOP $intLimit RdApp.refNo, RdApp.dateFiled, RdApp.cRDDateFrom, RdApp.cRDDateTo, RdApp.cRDReason, 
							empmast.empLastName, empmast.empFirstName, empmast.empMidName, seqNo, cRDStat,userApproved, empmast.empNo
							FROM         tblTK_ChangeRDApp RdApp INNER JOIN
							tblEmpMast empmast ON RdApp.empNo = empmast.empNo
							WHERE     (RdApp.compcode = '".$_SESSION["company_code"]."') AND (empmast.compCode = '".$_SESSION["company_code"]."')
							AND seqNo not in 
									(Select TOP $intOffset seqNo from tblTK_ChangeRDApp  RdApp
									WHERE (RdApp.compCode = '".$_SESSION["company_code"]."') "; 
									if($_GET['isSearch'] == 1){
										if($_GET['srchType'] == 0){
											$qryRdApp .= "AND cRDStat='A'";
										}
										
										if($_GET['srchType'] == 1){
											$qryRdApp .= "AND cRDStat='H'";
										}
										
										if($_GET['srchType'] == 2){
											$qryRdApp .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 3){
											$qryRdApp .= "AND RdApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 4){
											$qryRdApp .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
										}
									}
		$qryRdApp .=				"ORDER BY RdApp.refNo, RdApp.dateFiled, empmast.empLastName, empmast.empFirstName)";
							
							if($_GET['isSearch'] == 1){
										if($_GET['srchType'] == 0){
											$qryRdApp .= "AND cRDStat='A'";
										}
										
										if($_GET['srchType'] == 1){
											$qryRdApp .= "AND cRDStat='H'";
										}
										
										if($_GET['srchType'] == 2){
											$qryRdApp .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 3){
											$qryRdApp .= "AND RdApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 4){
											$qryRdApp .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
										}
									}
							
	$qryRdApp.=			"ORDER BY RdApp.refNo, RdApp.dateFiled, empmast.empLastName, empmast.empFirstName;";
	$resRdAppList = $crdObj->execQry($qryRdApp);
	$arrRdAppList = $crdObj->getArrRes($resRdAppList);
	
	
?>

<input type="hidden" name="empPayGrp" id="empPayGrp" value="<?=$empInfo["empPayGrp"]?>" />
<input type="hidden" name="empPayCat" id="empPayCat" value="<?=$empInfo["empPayCat"]?>" />
<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Change RestDay Application
		</td>
	</tr>
    
	<tr>
		<td colspan="6" class="gridToolbar">
        &nbsp;
			<!--
			<a href="#" id="newEarn" tabindex="1"><IMG class="toolbarImg" src="../../../images/application_form_add.png"  onclick="newRef('NEWREFNO'); validateMod('NEWREFNO');" title="New OB Record"></a>
			
			<FONT class="ToolBarseparator">|</font>-->
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.gif'  onclick="pager('crdAjaxResult.php','rdCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
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
                    
                    <td class="hdrInputsLvl" width="10%">&nbsp;</td>
                    
					<td class="hdrInputsLvl" width="5">&nbsp;</td>

					<td class="gridDtlVal" colspan="4">
						<input class="inputs" type="hidden" name="dateFiled" readonly="readonly" id="dateFiled" size="10"
							 value="<? 	
							 			$format="m/d/Y";
										$strf=date($format);
										echo("$strf"); 
									?>"
										>
									
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
						<INPUT tabindex="11" class="inputs" readonly="readonly" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" value="<?=$_GET["empNo"]?>" onkeydown="getEmployee(event,this.value)" >
					</td>
                    
					<td class="hdrInputsLvl" width="10%">
						Employee Name
					</td>
                    
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<INPUT class="inputs" readonly="readonly" type="text" name="txtEmpName" id="txtEmpName" size="40" value="<?=$fld_txtEmpName?>">
					</td>
                    
					<td class="hdrInputsLvl" width="10%">
						Dept. / Position
					</td>
                    
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<INPUT class="inputs" readonly="readonly" type="text" name="txtDeptPost" id="txtDeptPost" size="40" value="<?=$fld_txtDeptPost?>">
					</td>
				</tr>
                
			</TABLE>
			
            <TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
            	<tr style="height:25px;">
                	<td class="gridDtlLbl" align="center" >SELECT</td>
                    <td class="gridDtlLbl" align="center" colspan="2">CHANGE</td>
                    <td class="gridDtlLbl" align="center" >REMARKS / REASON(S)</td>		
                    <td  class="gridDtlLbl" align="center">ACTION</td>
				</tr>
                
				<tr style="height:20px;">
                	<td width="5%" class="gridDtlLbl" align="center"></td>		
                    <td width="25%" class="gridDtlLbl" align="center">FROM</td>		
                    <td width="25%" class="gridDtlLbl" align="center">TO</td>
                    <td width="25%" class="gridDtlLbl" align="center"></td>
                    <td width="10%" class="gridDtlLbl" align="center"></td>		
                </tr>
                
                           
                
                <?php
					if($empCurrRestDay!="")
					{
						$ctr = 1;
						foreach($empCurrRestDay as $empCurrRestDay_val)
						{
							$arr_ObRec_Checking = $crdObj->getTblData("tblTK_ChangeRDApp", " and empNo='".$_GET["empNo"]."' and cRDDateFrom='".date("m/d/Y", strtotime($empCurrRestDay_val))."'", "", "sqlAssoc");
							//echo $arr_ObRec_Checking["cRDStat"]."="."<br>";
		
				?>
                            <tr>
                            	<td class="inputs" align="center">
                                	<?php
										if($arr_ObRec_Checking["cRDStat"]=='')
										{
									?>
                                			<input type="radio" name="rdnTran" id="rdnTran<?=$ctr?>"  VALUE="<?=$ctr?>" onclick="enabledFields(<?=$ctr?>);" />
                                    <?php
										}
									?>
                                </td>
                                
                                
                                <td  align="center">
                                    <input tabindex="10" class="inputs" type="text" name="rdDateFrom<?=$ctr?>" readonly="readonly" id="rdDateFrom<?=$ctr?>"  
                                         value="<? 	
                                                    $format=date("m/d/Y", strtotime($empCurrRestDay_val));
                                                    $strf=date($format);
                                                    echo("$strf"); 
                                                ?>" >
                                                
                                                   
                                </td>
                                
                                <td  align="center">
                                    <input tabindex="10" class="inputs" type="text" name="rdDateTo<?=$ctr?>" readonly="readonly" id="rdDateTo<?=$ctr?>" 
                                         value="" >
                                                
                                                    <img src="../../../images/cal_new.gif" onClick="displayDatePicker('rdDateTo<?=$ctr?>', this);" style="cursor:pointer;" width="20" height="14">
                                
                                </td>
                                
                                 
                               
                                <td align="center">
                                <?
									$reasons=$crdObj->getTblData("tblTK_Reasons "," and stat='A' and changeRestDay='Y'"," order by reason","sqlArres");
									$arrReasons = $crdObj->makeArr($reasons,'reason_id','reason','');
									$crdObj->DropDownMenu($arrReasons,'cmbReasons'.$ctr,"","class='inputs'");
								?>
                        </td>
                                <?php
									if($arr_ObRec_Checking["cRDStat"]=='')
									{
								?>
                                        <td align="center">
                                            <input type="button" class="inputs" name="btnSave<?=$ctr?>" disabled="disabled"  id="btnSave<?=$ctr?>" value='SAVE'  onClick="saveRdDetail(<?=$ctr?>);">
                                        </td>
                           		<?php
									}else{
								?>
                                		<td align="center">
                                            <input type="button" class="inputs" name="btnSave<?=$ctr?>" disabled="disabled"  id="btnSave<?=$ctr?>" value='SAVE' >
                                        </td>

                                <?php } ?>
                            </tr>
                  <?php
				  			$ctr++;
				  		}
					}
				  ?>          
                
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
                        <?=$crdObj->DropDownMenu(array('Approved', 'Held', 'Ref. No.','Employee No.','Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
                        <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('crdAjaxResult.php','rdCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>','../../../images/')"> 
                    	<?php if(($_SESSION['user_level']==1)||($_SESSION['user_level']==2)){ ?>
                        <FONT class="ToolBarseparator">|</font>
                            <a href="#" id="updateEarn" tabindex="3"><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update RD Application" onclick="getSeqNo()"></a>
                        <FONT class="ToolBarseparator">|</font>
                        <a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" id="btnApp" src="../../../images/edit_prev_emp.png"  onclick="upObTran('Approve','crdAjaxResult.php','rdCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved CS Application" ></a>
                        <FONT class="ToolBarseparator">|</font>
                            <a href="#" id="deleEarn" tabindex="3"><img class="toolbarImg" id="btnDel" src="../../../images/application_form_delete.png" title="Delete CS Application" onclick="delObTran('Delete','crdAjaxResult.php','rdCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                      	<? } ?>
                    </td>
         		</tr>
                       
                <tr style="height:25px;">
                	<td width="3%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
                	<td width="10%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
                	<td width="22%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
                    <td width="10%" class="gridDtlLbl" align="center">DATE FILED</td>
                    <td width="10%" class="gridDtlLbl" align="center">RD FROM DATE</td>
					<td width="10%" class="gridDtlLbl" align="center">RD TO DATE</td>
                    <td width="35%" class="gridDtlLbl" align="center">REASON</td>
                </tr>
                
                <?php
					if($crdObj->getRecCount($resRdAppList) > 0)
					{
						$i=0;
						$ctr=1;
							
						foreach ($arrRdAppList as $arrRdAppList_val)
						{
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							$f_color = ($arrRdAppList_val["cRDStat"]=='A'?"#CC3300":"");
				?>
                                <tr style="height:20px;"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                                <td class="gridDtlVal" align="center">
                                    <?php
                                        if(($arrRdAppList_val["cRDStat"]=='H') || ($arrRdAppList_val["userApproved"]==$_SESSION['employee_number']))
                                        {
                                    ?>
                                    <input class="inputs" type="checkbox" name="chkseq[]" value="<?=$arrRdAppList_val['seqNo']?>" id="chkseq[]" />
                                    <?php
                                        }
                                    ?>
                                </td>  		
                               
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrRdAppList_val["empNo"]?></td>
                                <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($arrRdAppList_val["empLastName"].", ".$arrRdAppList_val["empFirstName"]." ")?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("m/d/Y", strtotime($arrRdAppList_val["dateFiled"]))?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("m/d/Y", strtotime($arrRdAppList_val["cRDDateFrom"]))?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("m/d/Y", strtotime($arrRdAppList_val["cRDDateTo"]))?></td>
                                <td class="gridDtlVal" align="left"><font color="<?=$f_color?>">
								<?
								if(is_numeric($arrRdAppList_val["cRDReason"])){
									$changeRes=$crdObj->getTblData("tblTK_Reasons "," and stat='A' and reason_id='".$arrRdAppList_val["cRDReason"]."'"," order by reason","sqlAssoc");
									echo $changeRes['reason'];
									
								}
								else{
									echo strtoupper($arrRdAppList_val["cRDReason"]);	
								}
								?></td>
                            </tr>
                <?php
							$ctr++;
						}
					}
				?>
                 <tr> 
                      <td colspan="11" align="center" class="childGridFooter"> 
                        <?
                            $pager->_viewPagerButton('crdAjaxResult.php','rdCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo']);
                        ?>
                      </td>
                </tr>
            </TABLE>
		
		</td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $crdObj->disConnect();?>

<script>
	function refreshPage()
	{
		alert("GENARRA");
	}
</script>

