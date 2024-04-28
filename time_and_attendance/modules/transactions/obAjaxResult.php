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
	
	$branch = $_SESSION['branchCode'];

	//08-09-2023 AUTO EMPLOYEE LOOKUP
	$empInfo = $obObj->getEmployee($_SESSION['company_code'],$_SESSION['employeenumber'],'');
	
	$paygroup = $empInfo['empPayGrp'];
	$paycat = $empInfo['empPayCat'];

	$reason = '';
	$obTimeIn = '';
	$obTimeOut = '';
	$timeINAM = 0;
	$timeOutOM = 0;
		
	$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
	$fullname = $empInfo['empLastName'] . ", " . htmlspecialchars(addslashes($empInfo['empFirstName'])) . " " . $midName;
		
	$deptName = $obObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
	$posName = $obObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
	$position = htmlspecialchars(addslashes($deptName['deptDesc']))." - ".$posName['posDesc'];
			
	$shiftCodeDtl = $obObj->getTblData("tblTk_TimeSheet", " and empNo='".$_SESSION['employeenumber']."' and tsDate='".date("Y-m-d")."'", "", "sqlAssoc");

	if($shiftCodeDtl["shftTimeIn"]=="") {
		$allowSave = false;
		$shiftSched = 'Set the Schedule first.';
		$shftTimeIn = '';
		$shftTimeOut = '';
	}else{
		$allowSave = true;
		$shiftSched = $shiftCodeDtl['shftTimeIn'] . " - " . $shiftCodeDtl["shftTimeOut"];
		$shftTimeIn = $shiftCodeDtl['shftTimeIn'];
		$shftTimeOut = $shiftCodeDtl["shftTimeOut"];
	}

	$disabled = $allowSave ? '' : 'disabled';
	//08-09-2023

	//get users branch access
	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $obObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		//08-08-2023
		// $brnCodelist = " AND empMast.empNo='".$_SESSION['employee_number']."' 
		// 				and empbrnCode IN (Select brnCode from tblTK_UserBranch 
		// 									where empNo='{$_SESSION['employee_number']}' 
		// 									AND compCode='{$_SESSION['company_code']}')";
		$brnCodelist = " AND empMast.empNo='".$_SESSION['employee_number']."'
						and empbrnCode ='".$branch."'";
											
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblTK_UserBranch 
											where empNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}')";
	}
	
	
	$queryBrnches = "Select * from tblBranch as tblbrn where compCode='".$_SESSION["company_code"]."' and brnStat='A'
					order by brnDesc";	
	$resBrnches = $obObj->execQry($queryBrnches);
	$arrBrnches = $obObj->getArrRes($resBrnches);
	$arrBrnch = $obObj->makeArr($arrBrnches,'brnCode','brnDesc','Others');

	//New Code for Approver 04-25-2024
	$_SESSION['uType'] = "T"; // Time Keeper
	$approverData = $obObj->getTblData("tbltna_approver", " and approverEmpNo='".$_SESSION['employee_number']."' and status='A' AND dateValid >= NOW()", "", "sqlAssoc");
	$forApproval = '';
	$timeKeeperApprover = $approverData["approverEmpNo"] != "" && $_SESSION['user_level'] == 2 && $_SESSION['user_release']=="Y";
	$managerApporver = $approverData["approverEmpNo"] != "" && $_SESSION['user_level'] == 2 && $_SESSION['user_release']!="Y";
	$timeKeeper = $approverData["approverEmpNo"] == "" && $_SESSION['user_level'] == 2 && $_SESSION['user_release']=="Y";
	if($timeKeeperApprover) { 
		$_SESSION['uType'] = "TA"; //Timekeeper Approver
		$forApproval = " AND (empMast.empNo IN (Select subordinateEmpNo from tbltna_approver 
											where approverEmpNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}') OR empMast.empNo = '{$_SESSION['employee_number']}' OR mStat = 'A')";
	}elseif($managerApporver){
		$_SESSION['uType'] = "MA"; //Manager Approver
		$forApproval = " AND (empMast.empNo IN (Select subordinateEmpNo from tbltna_approver 
											where approverEmpNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}') OR empMast.empNo = '{$_SESSION['employee_number']}')";
	}elseif($timeKeeper){
		$forApproval = " AND (mStat = 'A' OR empMast.empNo = '{$_SESSION['employee_number']}')";
	}
	//End New Code for Approver 04-25-2024
	
	$brnQry = "Select empNo,tblUB.brnCode as brnCode, brnDesc 
					 From tblTK_UserBranch tblUB, tblBranch as tblbrn
					 Where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' 
					 		and tblbrn.compCode='".$_SESSION["company_code"]."'
							and empNo='".$_SESSION['employee_number']."'
					 Order by brnDesc";
	
	$resBrn = $obObj->execQry($brnQry);
	$arrBrn = $obObj->getArrRes($resBrn);
	$brn = $obObj->makeArr($arrBrn,'brnCode','brnDesc','All');
	
	$qryIntMaxRec = "SELECT OBApp.refNo, OBApp.obDate,  OBApp.empNo, empMast.empLastName, empMast.empFirstName,obActualTimeOut, 
						obActualTimeIn,userApproved,addedBy, crossDay
					 FROM tblTK_OBApp OBApp 
					 INNER JOIN tblEmpMast empMast ON OBApp.empNo = empMast.empNo
					 WHERE (OBApp.compCode = '".$_SESSION["company_code"]."') 
							AND (empMast.compCode = '".$_SESSION["company_code"]."') $forApproval 
							$brnCodelist";
							
							if($_GET['isSearch'] == 1){
								if($_GET['srchType'] == 0){
									$qryIntMaxRec .= "";
								}

								if($timeKeeperApprover || $timeKeeper) { 
									if($_GET['srchType'] == 1){
										$qryIntMaxRec .= "AND obStat='A'";
									}
									if($_GET['srchType'] == 2){
										$qryIntMaxRec .= "AND obStat='H' ";
									}
								}elseif($managerApporver){
									if($_GET['srchType'] == 1){
										$qryIntMaxRec .= "AND mStat='A'";
									}
									if($_GET['srchType'] == 2){
										$qryIntMaxRec .= "AND mStat='H' ";
									}
								}else{
									if($_GET['srchType'] == 1){
										$qryIntMaxRec .= "AND mStat='A'";
									}
									if($_GET['srchType'] == 2){
										$qryIntMaxRec .= "AND mStat='H' ";
									}
								}
								
								if($_GET['srchType'] == 2){
									$qryIntMaxRec .= "AND obStat='H'";
								}
								
								if($_GET['srchType'] == 3){
									$qryIntMaxRec .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
								}
								
								if($_GET['srchType'] == 4){
									$qryIntMaxRec .= "AND OBApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
								}
								
								if($_GET['srchType'] == 5){
									$qryIntMaxRec .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
								}

								if ($_GET['brnCd']!=0) 
								{
									$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
								}

							}
							
	$qryIntMaxRec.=	"ORDER BY OBApp.obStat DESC, OBApp.refNo, OBApp.obDate, empMast.empLastName, empMast.empFirstName";
	$resIntMaxRec = $obObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

	$qryOBApp = "SELECT OBApp.compCode, OBApp.refNo, OBApp.obDate,OBApp.empNo, empMast.empLastName, 
						empMast.empFirstName,obReason, obActualTimeOut, obActualTimeIn, seqNo, obStat, obDestination, userApproved, 
						addedBy, hrs8Deduct, crossDay, mStat
				 FROM tblTK_OBApp OBApp 
				 INNER JOIN tblEmpMast empMast ON OBApp.empNo = empMast.empNo AND OBApp.compCode=empMast.compCode
				 WHERE (OBApp.compCode = '".$_SESSION["company_code"]."') 
				 AND (empMast.compCode = '".$_SESSION["company_code"]."') $forApproval
				 $brnCodelist
				 ";
							
	if($_GET['isSearch'] == 1){
		if($_GET['srchType'] == 0){
			$qryIntMaxRec .= "";
		}

		if($timeKeeperApprover || $timeKeeper) { 
			if($_GET['srchType'] == 1){
				$qryOBApp .= "AND obStat='A'";
			}
			if($_GET['srchType'] == 2){
				$qryOBApp .= "AND obStat='H' ";
			}
		}elseif($managerApporver){
			if($_GET['srchType'] == 1){
				$qryOBApp .= "AND mStat='A'";
			}
			if($_GET['srchType'] == 2){
				$qryOBApp .= "AND mStat='H' ";
			}
		}else{
			if($_GET['srchType'] == 1){
				$qryOBApp .= "AND mStat='A'";
			}
			if($_GET['srchType'] == 2){
				$qryOBApp .= "AND mStat='H' ";
			}
		}
				
		if($_GET['srchType'] == 3){
			$qryOBApp .= "and refNo LIKE '".trim($_GET['txtSrch'])."%' ";
		}
				
		if($_GET['srchType'] == 4){
			$qryOBApp .= "AND OBApp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
		}
				
		if($_GET['srchType'] == 5){
			$qryOBApp .= "AND empLastName LIKE '".trim($_GET['txtSrch'])."%' ";
		}
				
		if ($_GET['brnCd']!=0) 
		{
			$qryOBApp.= " AND empbrnCode='".$_GET["brnCd"]."' ";
		}
	}
							
	$qryOBApp.=	"ORDER BY  OBApp.obStat DESC, OBApp.refNo, OBApp.obDate, empMast.empLastName, empMast.empFirstName limit $intOffset,$intLimit;";
	
	$resOBAppList = $obObj->execQry($qryOBApp);
	$arrOBAppList = $obObj->getArrRes($resOBAppList);
?>

<input type="hidden" name="empPayGrp" id="empPayGrp" value="<?=$paygroup?>" />
<input type="hidden" name="empPayCat" id="empPayCat" value="<?=$paycat?>" />
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
			
            <TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
            	
				<tr height="25px">
                	<td width="12%" class="gridDtlLbl" align="center">OB DATE</td>
                    <td width="13%" class="gridDtlLbl" align="center">OB SCHED. IN</td>
                    <td width="13%" class="gridDtlLbl" align="center">OB SCHED. OUT</td>
					<td width="6%" class="gridDtlLbl" align="center">CROSS DAY?</td>
                    <td width="18%" class="gridDtlLbl" align="center">DESTINATION</td>
					<td width="19%" class="gridDtlLbl" align="center">PURPOSE</td>
          			<td width="6%" class="gridDtlLbl" align="center">IN</td>		
					<td width="6%" class="gridDtlLbl" align="center">OUT</td>
                    <td width="7%" class="gridDtlLbl" align="center">CREDIT 8 HRS?</td>
					<td width="6%" class="gridDtlLbl" align="center">ACTION</td>
				</tr>
                
                <tr>
                	<td  align="center">
                    	<input tabindex="10" class="inputs" type="text" name="obDate" readonly="readonly" id="obDate" size="10" onfocus="getEmpShift(<?=$_GET["empNo"]?>);"
							 value="<? 	
							 			$format="Y-m-d";
										$strf=date($format);
										echo("$strf"); 
									?>" >
									
										<img src="../../../images/cal_new.png" onClick="displayDatePicker('obDate', this);" style="cursor:pointer;" width="20" height="14">
					
                    </td>
                    
                   
                    <td align="center"><input type="text" name="schedTimeIn" id="schedTimeIn" style="width:50px;" class='inputs' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value="<?=$shftTimeIn?>" />
					<?
						$obObj->DropDownMenu(array(''=>'','AM' => 'AM','PM' => 'PM'),'cmbTINAMPM','','class="inputs"');
					?>
					<input type="hidden" readonly="readonly" class="inputs" name="shiftSched"  id="shiftSched" style="width:100%;" value="<?=$shiftSched?>" /></td>
                    <td align="center"><input type="text" name="schedTimeOut"  id="schedTimeOut" style="width:50px;" class='inputs' onkeydown="javascript:return dFilter (event.keyCode, this, '##:##');"  value="<?=$shftTimeOut?>" />
					<?
						$obObj->DropDownMenu(array(''=>'','AM' => 'AM','PM' => 'PM'),'cmbTOUTAMPM','','class="inputs"');
					?>
					</td>
					<td align="center"><input name="crossDay" type="checkbox" id="crossDay" /></td>
                    <td > <?=$obObj->DropDownMenu($arrBrnch,'obdestination',$_GET['obdestination'],'class="inputs" '.$disabled.' style="width:100%;"');?></td>
                    
                    <td align="center"><span class="gridDtlVal">
                      <?
						$reasons=$obObj->getTblData("tblTK_Reasons "," and stat='A' and obApp='Y'"," order by reason","sqlArres");
						$arrReasons = $obObj->makeArr($reasons,'reason_id','reason','');
						$obObj->DropDownMenu($arrReasons,'cmbReasons',"","class='inputs'");
					?>
                  </span></td>
                    <td align="center"><input name='txtobTimeIn' type='text' <?=$disabled?> class='inputs' id='txtobTimeIn'  onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='' size="5">
                      <span class="gridDtlVal">  
                    </span></td>
                	<td align="center"><input name='txtobTimeOut' type='text' <?=$disabled?> class='inputs' id='txtobTimeOut' onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='' size="5">
                	  <span class="gridDtlVal">
                	  
       	        </span></td>
 					<td align="center"><input type="checkbox" name="rdnDeduct8" id="rdnDeduct8" <?=$disabled?>/></td>
                    <td align="center"><input type='button' class= 'inputs' name='btnSave' id="btnSave" value='SAVE' <?=$disabled?> onClick="saveObDetail();" ></td>
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
                        <?=$obObj->DropDownMenu(array('','Approved', 'Held', 'Ref. No.','Employee No.','Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
                        <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('obAjaxResult.php','obCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
                        <FONT class="ToolBarseparator">|</font>	
						<a href="#"  id="btnEdit"onClick=""><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update OB Application" onclick="getSeqNo()"></a> 
						<?
                        if($_SESSION['user_release']=="Y" || $_SESSION['user_level'] == 2){
                        ?>
                        <FONT class="ToolBarseparator">|</font>
                        <a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" id="btnApp" src="../../../images/edit_prev_emp.png"  onclick="upObTran('Approve','obAjaxResult.php','obCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved OB Application" ></a>	
                        <?
						}
						?>						                   
                        <FONT class="ToolBarseparator">|</font>    
                            <a href="#" id="deleEarn" tabindex="3"><img class="toolbarImg" id="btnDel" src="../../../images/application_form_delete.png" title="Delete OB Application" onclick="delObTran('Delete','obAjaxResult.php','obCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a> <font class="ToolBarseparator">|</font>
                <?=$obObj->DropDownMenu($brn,'brnCd',$_GET['brnCd'],'class="inputs"');?>
                    </td>
         		</tr>
                
                <tr style="height:25px;">
                	<td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
                	<td width="8%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
                	<td width="19%" class="gridDtlLbl" align="center">EMPLOYEE</td>
                    <td width="8%" class="gridDtlLbl" align="center">OB DATE</td>
                    <td width="17%" class="gridDtlLbl" align="center">DESTINATION</td>
                    <td width="5%" class="gridDtlLbl" align="center">CROSS DAY</td>
                    <td width="6%" class="gridDtlLbl" align="center">CREDIT 8 HRS.</td>
					<td width="20%" class="gridDtlLbl" align="center">PURPOSE</td>
                    <td width="6%" class="gridDtlLbl" align="center">ACTUAL IN</td>
                    <td width="6%" class="gridDtlLbl" align="center">ACTUAL OUT</td>
                    <td width="3%" class="gridDtlLbl" align="center">&nbsp;</td>
          			
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
							if($timeKeeperApprover) {
								$f_color = ($arrOBAppList_val["obStat"]=='A'?"#CC3300":"");
							}elseif($managerApporver) {
								$f_color = ($arrOBAppList_val["mStat"]=='A'?"#CC3300":"");
							}elseif($timeKeeper) {
								$f_color = ($arrOBAppList_val["obStat"]=='A'?"#CC3300":"");
							}elseif($_SESSION['user_level'] == 3){
								$f_color = ($arrOBAppList_val["mStat"]=='A'?"#CC3300":"");
							}
							
							$obDestination = $obObj->getEmpBranchArt($arrOBAppList_val["compCode"],$arrOBAppList_val["obDestination"]);
							$obDestination = ($obDestination==""?"OTHERS":$obDestination["brnDesc"]);
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
									$OBRes=$obObj->getTblData("tblTK_Reasons "," and stat='A' and reason_id='".$arrOBAppList_val["obReason"]."'"," order by reason","sqlAssoc");
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
                                	<a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" id="btnApp" src="../../../images/edit_prev_emp.png"  onclick="disObTran('<?=$arrOBAppList_val['seqNo']?>','obAjaxResult.php','obCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Set to Active OB Application" ></a>     	
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
                            $pager->_viewPagerButton('obAjaxResult.php','obCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo']);
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

