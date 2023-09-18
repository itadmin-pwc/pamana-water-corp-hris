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


	$obObj = new transactionObj($_GET,$_SESSION);
	$obObj->validateSessions('','MODULES');
	
	$pager = new AjaxPager(10,'../../../images/');
	
	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $obObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		$brnCodelist = " AND empmast.empNo<>'".$_SESSION['employee_number']."' and empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}
	
	$queryBrnches_user = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblTK_UserBranch tblUB, tblBranch as tblbrn
							where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
							and empNo='".$_SESSION['employee_number']."'
							order by brnDesc";
	
	$resBrnches_user = $obObj->execQry($queryBrnches_user);
	$arrBrnches_user = $obObj->getArrRes($resBrnches_user);
	$arrBrnc_user = $obObj->makeArr($arrBrnches_user,'brnCode','brnDesc','All');
	
	$queryBrnches = "Select * from tblBranch as tblbrn where compCode='".$_SESSION["company_code"]."' and brnStat='A'
					order by brnDesc";
		
	$resBrnches = $obObj->execQry($queryBrnches);
	$arrBrnches = $obObj->getArrRes($resBrnches);
	$arrBrnch = $obObj->makeArr($arrBrnches,'brnCode','brnDesc','Others');
	
	
	$qryIntMaxRec = "SELECT     OBApp.refNo, OBApp.obDate,  OBApp.empNo, empMast.empLastName, empMast.empFirstName,obActualTimeOut, obActualTimeIn,userApproved
							FROM         tblTK_OBApp OBApp INNER JOIN
							tblEmpMast empMast ON OBApp.empNo = empMast.empNo
							WHERE     (OBApp.compCode = '".$_SESSION["company_code"]."') 
							AND (empMast.compCode = '".$_SESSION["company_code"]."') $brnCodelist";
							
							if($_GET['isSearch'] == 1){
								if($_GET['srchType'] == 0){
									$qryOBApp .= "AND obStat='A'";
								}
								
								if($_GET['srchType'] == 1){
									$qryOBApp .= "AND obStat='H'";
								}
								
								if($_GET['srchType'] == 2){
									$qryIntMaxRec .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
								}
								
								if($_GET['srchType'] == 3){
									$qryIntMaxRec .= "AND OBApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
								}
								
								if($_GET['srchType'] == 4){
									$qryIntMaxRec .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
								}
								
								if ($_GET['brnCd']!=0) 
								{
									$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
								}
							}
							
	$qryIntMaxRec.=			"ORDER BY empMast.empLastName, empMast.empFirstName, OBApp.obDate, OBApp.refNo";
	
	$resIntMaxRec = $obObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryOBApp = "SELECT     TOP $intLimit OBApp.compCode, OBApp.refNo, hrs8Deduct, OBApp.obDate,OBApp.empNo, empMast.empLastName, empMast.empFirstName,obReason, obActualTimeOut, obActualTimeIn, seqNo,obStat,obDestination,userApproved
							FROM  tblTK_OBApp OBApp INNER JOIN
							tblEmpMast empMast ON OBApp.empNo = empMast.empNo
							WHERE     (OBApp.compCode = '".$_SESSION["company_code"]."') 
							AND (empMast.compCode = '".$_SESSION["company_code"]."') $brnCodelist
							AND seqNo not in 
									(Select TOP $intOffset OBApp.seqNo from tblTK_OBApp  OBApp INNER JOIN
									tblEmpMast empMast ON OBApp.empNo = empMast.empNo
									WHERE (OBApp.compCode = '".$_SESSION["company_code"]."') 	AND (empMast.compCode = '".$_SESSION["company_code"]."') $brnCodelist"; 
									if($_GET['isSearch'] == 1){
										if($_GET['srchType'] == 0){
											$qryOBApp .= "AND obStat='A'";
										}
										
										if($_GET['srchType'] == 1){
											$qryOBApp .= "AND obStat='H'";
										}
										
										if($_GET['srchType'] == 2){
											$qryOBApp .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 3){
											$qryOBApp .= "AND OBApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 4){
											$qryOBApp .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if ($_GET['brnCd']!=0) 
										{
											$qryOBApp.= " AND empbrnCode='".$_GET["brnCd"]."' ";
										}
									}
		$qryOBApp .=				"ORDER BY empMast.empLastName, empMast.empFirstName, OBApp.obDate, OBApp.refNo)";
							
							if($_GET['isSearch'] == 1){
										if($_GET['srchType'] == 0){
											$qryOBApp .= "AND obStat='A'";
										}
										
										if($_GET['srchType'] == 1){
											$qryOBApp .= "AND obStat='H'";
										}
										
										if($_GET['srchType'] == 2){
											$qryOBApp .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 3){
											$qryOBApp .= "AND OBApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if($_GET['srchType'] == 4){
											$qryOBApp .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
										}
										
										if ($_GET['brnCd']!=0) 
										{
											$qryOBApp.= " AND empbrnCode='".$_GET["brnCd"]."' ";
										}
									}
							
	$qryOBApp.=			"ORDER BY empMast.empLastName, empMast.empFirstName,  OBApp.obDate,OBApp.refNo;";
	
	$resOBAppList = $obObj->execQry($qryOBApp);
	$arrOBAppList = $obObj->getArrRes($resOBAppList);
	
	if($_SESSION['user_level']==3){
		$btAppDel_Dis = "hidden";
	}
	
?>

<input type="hidden" name="empPayGrp" id="empPayGrp" value="" />
<input type="hidden" name="empPayCat" id="empPayCat" value="" />
<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Official Business Application
		</td>
	</tr>
    
	<tr>
		<td colspan="6" class="gridToolbar">
			&nbsp;
			<!--<a href="#" id="newEarn" tabindex="1"><IMG class="toolbarImg" src="../../../images/application_form_add.png"  onclick="newRef('NEWREFNO'); validateMod('NEWREFNO');" title="New OB Record"></a>
			
			<FONT class="ToolBarseparator">|</font>-->
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.png'  onclick="pager('obAjaxResult.php','obCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
			
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

					<td class="gridDtlVal" colspan="4">
						
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
                	<td width="10%" class="gridDtlLbl" align="center">OB DATE</td>
                    <td width="5%" class="gridDtlLbl" align="center">OB SCHED. IN</td>
                    <td width="5%" class="gridDtlLbl" align="center">OB SCHED. OUT</td>
                    <td width="10%" class="gridDtlLbl" align="center">DESTINATION</td>
					<td width="20%" class="gridDtlLbl" align="center">PURPOSE</td>
          			<td width="6%" class="gridDtlLbl" align="center">IN</td>		
					<td width="6%" class="gridDtlLbl" align="center">OUT</td>
                    <td width="6%" class="gridDtlLbl" align="center">CREDIT 8 HRS?</td>
					<td width="8%" class="gridDtlLbl" align="center">ACTION</td>
				</tr>
                
                <tr>
                	<td  align="center">
                    	<input tabindex="10" class="inputs" type="text" name="obDate" readonly="readonly" id="obDate" size="10" onfocus="getEmpShift(<?=$_GET["empNo"]?>);"
							 value="<? 	
							 			$format="m/d/Y";
										$strf=date($format);
										echo("$strf"); 
									?>" >
									
										<img src="../../../images/cal_new.png" onClick="displayDatePicker('obDate', this);" style="cursor:pointer;" width="20" height="14">
					
                    </td>
                     <input type="hidden" name="shiftSched" id="shiftSched" value="" />
                   
                    <td><input type="text" disabled="disabled"  class="inputs" name="schedTimeIn"  id="schedTimeIn" style="width:100%;" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value="" /></td>
                     <td><input type="text" disabled="disabled"  class="inputs" name="schedTimeOut"  id="schedTimeOut" style="width:100%;" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');"  value="" /></td>
                    <td> <?=$obObj->DropDownMenu($arrBrnch,'obdestination',$_GET['obdestination'],'class="inputs" disabled style="width:100%;"');?></td>
                    
                    <td><input type="text" class="inputs" name="obreason"  id="obreason" style="width:100%;" value="" disabled="disabled" /></td>
                    <td align="center"><input type='text' class='inputs' name='txtobTimeIn' id='txtobTimeIn'  style='width:100%;' disabled="disabled" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value=''></td>
                	<td align="center"><input type='text' class='inputs' name='txtobTimeOut' id='txtobTimeOut'  style='width:100%;' disabled="disabled" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value=''></td>
 					<td align="center"><input type="checkbox" name="rdnDeduct8" id="rdnDeduct8" disabled="disabled" /></td>
                    <td align="center"><input type='button' class= 'inputs' name='btnSave' id="btnSave" value='SAVE' disabled="disabled" onClick="saveObDetail();"></td>
                </tr>
                
                
			</TABLE>	
            <BR />
			<TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
            	<tr>
					<td class="hdrLblRow" colspan="9">
						<FONT class="hdrLbl"  id="hlprMsg">Summary of Application</font>
					</td>
				</tr>
                
                <tr>
                    <td colspan="9" class="gridToolbar">
                                Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
                        In 
                        <?=$obObj->DropDownMenu(array('Approved', 'Held', 'Ref. No.','Employee No.','Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
                        <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('obAjaxResult.php','obCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
                    	
                        <FONT class="ToolBarseparator">|</font>
                            <a href="#" id="updateEarn" tabindex="3"><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update OB Application" onclick="getSeqNo()"></a>
                        <FONT class="ToolBarseparator">|</font>
                        	<a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" id="btnApp" style="visibility:<?=$btAppDel_Dis?>"  src="../../../images/edit_prev_emp.png"  onclick="upObTran('Approve','obAjaxResult.php','obCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved OB Application" ></a>
                        <FONT class="ToolBarseparator">|</font>
                            <a href="#" id="deleEarn" tabindex="3"><img class="toolbarImg" id="btnDel" style="visibility:<?=$btAppDel_Dis?>"  src="../../../images/application_form_delete.png" title="Delete OB Application" onclick="delObTran('Delete','obAjaxResult.php','obCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                      	<FONT class="ToolBarseparator">|</font> 
                         	<?=$obObj->DropDownMenu($arrBrnc_user,'brnCd',$_GET['brnCd'],'class="inputs"');?>
                    </td>
         		</tr>
                
                <tr style="height:25px;">
                	<td width="1%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
                	<td width="5%" class="gridDtlLbl" align="center">REF. NO</td>
                    <td width="5%" class="gridDtlLbl" align="center">OB DATE</td>
                    <td width="20%" class="gridDtlLbl" align="center">DESTINATION</td>
					<td width="15%" class="gridDtlLbl" align="center">EMPLOYEE</td>
                    <td width="15%" class="gridDtlLbl" align="center">PURPOSE</td>
                    <td width="5%" class="gridDtlLbl" align="center">CREDIT 8 HRS?</td>
                    <td width="5%" class="gridDtlLbl" align="center">ACTUAL IN</td>
                    <td width="5%" class="gridDtlLbl" align="center">ACTUAL OUT</td>
          			
				</tr>
                
                <?php
					if($obObj->getRecCount($resOBAppList) > 0)
					{
						$i=0;
						$ctr=1;
							
						foreach ($arrOBAppList as $arrOBAppList_val)
						{
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							$f_color = ($arrOBAppList_val["obStat"]=='A'?"#CC3300":"");
							
							$obDestination = $obObj->getEmpBranchArt($arrOBAppList_val["compCode"],$arrOBAppList_val["obDestination"]);
							$obDestination = ($obDestination==""?"OTHERS":$obDestination["brnDesc"]);
				?>
                			<tr style="height:20px;"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                            	<td class="gridDtlVal" align="center">
                            	<?php
									if(($arrOBAppList_val["obStat"]=='H') || (($arrOBAppList_val["userApproved"]==$_SESSION['employee_number']) && $arrOBAppList_val["obStat"]=='A') )
									{
								?>
                            			<input class="inputs" type="checkbox" name="chkseq[]" value="<?=$arrOBAppList_val['seqNo']?>" id="chkseq[]" />
                                <?php
									}
								?>		
                                </td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrOBAppList_val["refNo"]?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("m/d/Y", strtotime($arrOBAppList_val["obDate"]))?></td>
                                 <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($obDestination)?></td>
                                <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($arrOBAppList_val["empLastName"].", ".$arrOBAppList_val["empFirstName"]." ")?></td>
                                 <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($arrOBAppList_val["obReason"])?></td>
                                  <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=($arrOBAppList_val["hrs8Deduct"]=='Y'?"YES":"NO")?></td>
                                 <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrOBAppList_val["obActualTimeIn"]?></td>
                                 <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrOBAppList_val["obActualTimeOut"]?></td>
                               
                            </tr>
                <?php
							$ctr++;
						}
					}
				?>
                 <tr> 
                      <td colspan="11" align="center" class="childGridFooter"> 
                        <?
                            $pager->_viewPagerButton('obAjaxResult.php','obCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo'].'&brnCd='.$_GET['brnCd']);
                        ?>
                      </td>
                </tr>
            </TABLE>
		
		</td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $obObj->disConnect();?>

<script>
	function refreshPage()
	{
		alert("GENARRA");
	}
</script>
