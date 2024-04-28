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

	//08-09-2023 AUTO EMPLOYEE LOOKUP IF USER LEVEL = 3
	$empInfo = $crdObj->getEmployee($_SESSION['company_code'],$_SESSION['employeenumber'],'');
				
				
	$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
	$fld_txtEmpName = $empInfo['empLastName'].", ".htmlspecialchars(addslashes($empInfo['empFirstName']))." ".$midName;
				
	$deptName = $crdObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
	$posName = $crdObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
		
	$fld_txtDeptPost = htmlspecialchars(addslashes($deptName["deptDesc"]))." - ".$posName["posDesc"];
				
	if($empInfo['empRestDay']!="")
		$empCurrRestDay = explode(",", $empInfo['empRestDay']);

	$branch = $_SESSION['branchCode'];

	//08-09-2023 END LOOK UP
	
	switch($_GET["action"])
	{
		case 'getEmpInfo':
		
				$empInfo = $crdObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');
				
				
				$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
				$fld_txtEmpName = $empInfo['empLastName'].", ".htmlspecialchars(addslashes($empInfo['empFirstName']))." ".$midName;
				
				$deptName = $crdObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
				$posName = $crdObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
				
				$fld_txtDeptPost = htmlspecialchars(addslashes($deptName["deptDesc"]))." - ".$posName["posDesc"];
				
				if($empInfo['empRestDay']!="")
					$empCurrRestDay = explode(",", $empInfo['empRestDay']);
				
						
		break;
	}
	
	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $crdObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';
		$brnCodelist = " AND empMast.empNo='".$_SESSION['employee_number']."' 
		and empbrnCode ='".$branch."'";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblTK_UserBranch 
											where empNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}')";
	}
	
	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc 
					 From tblTK_UserBranch tblUB, tblBranch as tblbrn
					 Where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' 
					 		and tblbrn.compCode='".$_SESSION["company_code"]."'
							and empNo='".$_SESSION['employee_number']."'
					 Order by brnDesc";
	
	$resBrnches = $crdObj->execQry($queryBrnches);
	$arrBrnches = $crdObj->getArrRes($resBrnches);
	$arrBrnch = $crdObj->makeArr($arrBrnches,'brnCode','brnDesc','All');

	//New Code for Approver 04-25-2024
	$_SESSION['uType'] = "T"; // Time Keeper
	$approverData = $crdObj->getTblData("tbltna_approver", " and approverEmpNo='".$_SESSION['employee_number']."' and status='A' AND dateValid >= NOW()", "", "sqlAssoc");
	$forApproval = '';
	$timeKeeperApprover = $approverData["approverEmpNo"] != "" && $_SESSION['user_level'] == 2 && $_SESSION['user_release']=="Y";
	$managerApporver = $approverData["approverEmpNo"] != "" && $_SESSION['user_level'] == 2 && $_SESSION['user_release']!="Y";
	$timeKeeper = $approverData["approverEmpNo"] == "" && $_SESSION['user_level'] == 2 && $_SESSION['user_release']=="Y";
	if($timeKeeperApprover) { 
		$_SESSION['uType'] = "TA"; //Timekeeper Approver
		$forApproval = " AND (empmast.empNo IN (Select subordinateEmpNo from tbltna_approver 
											where approverEmpNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}') OR empmast.empNo = '{$_SESSION['employee_number']}' OR mStat = 'A')";
	}elseif($managerApporver){
		$_SESSION['uType'] = "MA"; //Manager Approver
		$forApproval = " AND (empmast.empNo IN (Select subordinateEmpNo from tbltna_approver 
											where approverEmpNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}') OR empmast.empNo = '{$_SESSION['employee_number']}')";
	}elseif($timeKeeper){
		$forApproval = " AND (mStat = 'A' OR empmast.empNo = '{$_SESSION['employee_number']}')";
	}
	//End New Code for Approver 04-25-2024
	
	$qryIntMaxRec = "SELECT RdApp.refNo, RdApp.dateFiled, RdApp.cRDDateFrom, RdApp.cRDDateTo, RdApp.cRDReason, 
							empmast.empLastName, empmast.empFirstName, empmast.empMidName, seqNo, cRDStat,userApproved, mStat
					 FROM tblTK_ChangeRDApp RdApp 
					 INNER JOIN tblEmpMast empmast ON RdApp.empNo = empmast.empNo
					 WHERE (RdApp.compcode = '".$_SESSION["company_code"]."') 
					 	AND (empmast.compCode = '".$_SESSION["company_code"]."') $forApproval
						$brnCodelist";
							
							if($_GET['isSearch'] == 1){
								if($timeKeeperApprover || $timeKeeper) { 
									if($_GET['srchType'] == 0){
										$qryRdApp .= "AND csStat='A' ";
									}
									
									if($_GET['srchType'] == 1){
										$qryRdApp .= "AND csStat='H' ";
									}
								}elseif($managerApporver){
									if($_GET['srchType'] == 0){
										$qryRdApp .= "AND mStat='A' ";
									}
									
									if($_GET['srchType'] == 1){
										$qryRdApp .= "AND mStat='H' ";
									}
								}else{
									if($_GET['srchType'] == 0){
										$qryRdApp .= "AND mStat='A' ";
									}

									if($_GET['srchType'] == 1){
										$qryRdApp .= "AND mStat='H' ";
									}
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
								
								if ($_GET['brnCd']!=0) 
								{
									$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
								}

							}
							
	$qryIntMaxRec.=	"ORDER BY RdApp.refNo, RdApp.dateFiled, empmast.empLastName, empmast.empFirstName";
	
	$resIntMaxRec = $crdObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
							
	$qryRdApp = "SELECT RdApp.refNo, RdApp.dateFiled, RdApp.cRDDateFrom, RdApp.cRDDateTo, RdApp.cRDReason, 
							empmast.empLastName, empmast.empFirstName, empmast.empMidName, seqNo, cRDStat, userApproved, empmast.empNo,
							mStat
				 FROM tblTK_ChangeRDApp RdApp 
				 INNER JOIN tblEmpMast empmast ON RdApp.empNo = empmast.empNo
				 WHERE (RdApp.compcode = '".$_SESSION["company_code"]."') 
				 		AND (empmast.compCode = '".$_SESSION["company_code"]."') $forApproval
						$brnCodelist
						";
							
							if($_GET['isSearch'] == 1){
								if($timeKeeperApprover || $timeKeeper) { 
									if($_GET['srchType'] == 0){
										$qryRdApp .= "AND csStat='A' ";
									}
									
									if($_GET['srchType'] == 1){
										$qryRdApp .= "AND csStat='H' ";
									}
								}elseif($managerApporver){
									if($_GET['srchType'] == 0){
										$qryRdApp .= "AND mStat='A' ";
									}
									
									if($_GET['srchType'] == 1){
										$qryRdApp .= "AND mStat='H' ";
									}
								}else{
									if($_GET['srchType'] == 0){
										$qryRdApp .= "AND mStat='A' ";
									}

									if($_GET['srchType'] == 1){
										$qryRdApp .= "AND mStat='H' ";
									}
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
										
								if ($_GET['brnCd']!=0) 
								{
									$qryRdApp .= " AND empbrnCode='".$_GET["brnCd"]."' ";
								}
							}
							
	$qryRdApp.=	"ORDER BY RdApp.refNo, RdApp.dateFiled, empmast.empLastName, empmast.empFirstName limit $intOffset,$intLimit;";
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
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.png'  onclick="pager('crdAjaxResult.php','rdCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
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
                    <td class="hdrInputsLvl" width="10%">&nbsp;</td>
                    
					<td class="hdrInputsLvl" width="5">&nbsp;</td>

					<td class="gridDtlVal" colspan="4">
						<input class="inputs" type="hidden" name="dateFiled" readonly="readonly" id="dateFiled" size="10"
							 value="<? 	
							 			$format="Y-m-d";
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
                    
					<td class="gridDtlVal">
						<?php
							if ($_SESSION['user_level'] == 3 || $managerApporver)  {
						?>
							<INPUT tabindex="11" class="inputs" readonly="readonly" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" value="<?=$_SESSION['employeenumber']?>">
						<?php
							}else{
						?>
							<INPUT tabindex="11" class="inputs" readonly="readonly" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" value="<?=$_SESSION['employeenumber']?>" onkeydown="getEmployee(event,this.value)" >
						<?php
							}
						?>
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
					$empNo = $_GET['empNo'] == '' ? $_SESSION['employee_number'] : $_GET['empNo'];
					$resDay = $crdObj->getRestday($empNo,$_SESSION['company_code']);
					if($resDay!="")
					{
						$ctr = 1;
						foreach($resDay as $empCurrRestDay_val)
						{
							$arr_ObRec_Checking = $crdObj->getTblData("tblTK_ChangeRDApp", " and empNo='".$_GET["empNo"]."' and cRDDateFrom='".date("Y-m-d", strtotime($empCurrRestDay_val['tsDate']))."'", "", "sqlAssoc");
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
                                                    $format=date("Y-m-d", strtotime($empCurrRestDay_val['tsDate']));
                                                    $strf=date($format);
                                                    echo("$strf"); 
                                                ?>" >
                                                
                                                   
                                </td>
                                
                                <td  align="center">
                                    <input tabindex="10" class="inputs" type="text" name="rdDateTo<?=$ctr?>" readonly="readonly" id="rdDateTo<?=$ctr?>" 
                                         value="" >
                                                
                                                    <img src="../../../images/cal_new.png" onClick="displayDatePicker('rdDateTo<?=$ctr?>', this);" style="cursor:pointer;" width="20" height="14">
                                
                                </td>
                                
                                <td align="center">
                                <?
									$reasons=$crdObj->getTblData("tblTK_Reasons "," and stat='A' and changeRestDay='Y'"," order by reason","sqlArres");
									$arrReasons = $crdObj->makeArr($reasons,'reason_id','reason','');
									$crdObj->DropDownMenu($arrReasons,'cmbReasons'.$ctr,"","class='inputs'");
								?>
                        </td>
                                <?php
									if($arr_ObRec_Checking["cRDStat"]=='' || $arr_ObRec_Checking["cRDStat"]=='H')
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
                        <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('crdAjaxResult.php','rdCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
                        <FONT class="ToolBarseparator">|</font>
                            <a href="#" id="updateEarn" tabindex="3"><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update RD Application" onclick="getSeqNo()"></a>
						<?
                        if($_SESSION['user_release']=="Y" || $_SESSION['user_level'] == 2){
                        ?>
                        <FONT class="ToolBarseparator">|</font>
                        <a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" id="btnApp" src="../../../images/edit_prev_emp.png"  onclick="upObTran('Approved','crdAjaxResult.php','rdCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved CS Application" ></a>
                        <?
						}
						?>
                        <FONT class="ToolBarseparator">|</font>
                            <a href="#" id="deleEarn" tabindex="3"><img class="toolbarImg" id="btnDel" src="../../../images/application_form_delete.png" title="Delete CS Application" onclick="delObTran('Delete','crdAjaxResult.php','rdCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a> <font class="ToolBarseparator">|</font>
                <?=$crdObj->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');?>
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
							if($timeKeeperApprover) {
								$f_color = ($arrRdAppList_val["cRDStat"]=='A'?"#CC3300":"");
							}elseif($managerApporver) {
								$f_color = ($arrRdAppList_val["mStat"]=='A'?"#CC3300":"");
							}elseif($timeKeeper) {
								$f_color = ($arrRdAppList_val["cRDStat"]=='A'?"#CC3300":"");
							}elseif($_SESSION['user_level'] == 3){
								$f_color = ($arrRdAppList_val["mStat"]=='A'?"#CC3300":"");
							}
				?>
                                <tr style="height:20px;"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                                <td class="gridDtlVal" align="center">
                                    <?php
                                        if($timeKeeperApprover || $timeKeeper || $arrRdAppList_val["mStat"]=='H')
                                        {
                                    ?>
                                    <input class="inputs" type="checkbox" name="chkseq[]" value="<?=$arrRdAppList_val['seqNo']?>" id="chkseq[]" />
                                    <?php
                                        }
                                    ?>
                                </td>  		
                               
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrRdAppList_val["empNo"]?></td>
                                <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($arrRdAppList_val["empLastName"].", ".$arrRdAppList_val["empFirstName"]." ")?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("Y-m-d", strtotime($arrRdAppList_val["dateFiled"]))?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("Y-m-d", strtotime($arrRdAppList_val["cRDDateFrom"]))?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("Y-m-d", strtotime($arrRdAppList_val["cRDDateTo"]))?></td>
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

