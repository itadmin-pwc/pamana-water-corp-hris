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

	//08-09-2023 AUTO EMPLOYEE LOOKUP

	$empInfo = $csObj->getEmployee($_SESSION['company_code'],$_SESSION['employeenumber'],'');

	$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
	$fullname = $empInfo['empLastName'] . ", " . htmlspecialchars(addslashes($empInfo['empFirstName'])) . " " . $midName;
	$cwwTag = $csObj->getTblData("tblTK_EmpShift", " and empNo='".$_SESSION['employeenumber']."'", "", "sqlAssoc");
				
	$deptName = $csObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
	$posName = $csObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
				
	$department = htmlspecialchars(addslashes($deptName['deptDesc'])) . " - " . $posName['posDesc'];
	$paygroup = $empInfo["empPayGrp"];
	$paycat = $empInfo['empPayCat'];

	$openPeriod_OpnPeriodD = $csObj->getOpenPeriod($_SESSION["company_code"],$empInfo["empPayGrp"],$empInfo['empPayCat']); 
				
	$openPeriod = $csObj->getOpenPeriod($_SESSION["company_code"],$empInfo["empPayGrp"],$empInfo['empPayCat']); 
	$payPayable = $openPeriod['pdPayable'];
	$shiftCodeDtl = $csObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("Y-m-d")."'", "", "sqlAssoc");
				
	if($shiftCodeDtl["shftTimeIn"]=="")
	{
		$allowSave = false;
		$schedTimeIn = "Set the Set the Schedule first";
		$shftTimeOut = '';
		$dayType = '';
	}	
	else
	{	
		$allowSave = true;
		$schedTimeIn = $shiftCodeDtl["shftTimeIn"];
		$shftTimeOut = $shiftCodeDtl["shftTimeOut"];
		$dayType = $shiftCodeDtl["dayType"];
	}

	//08-09-2023 END LOOK UP
	$branch = $_SESSION['branchCode'];
	
	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $csObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		//08-08-2023
		// $brnCodelist = " AND empmast.empNo='".$_SESSION['employee_number']."' 
		// 				 AND empbrnCode IN (Select brnCode from tblTK_UserBranch 
		// 				 					where empNo='{$_SESSION['employee_number']}' 
		// 										AND compCode='{$_SESSION['company_code']}')";
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
	
	$resBrnches = $csObj->execQry($queryBrnches);
	$arrBrnches = $csObj->getArrRes($resBrnches);
	$arrBrnch = $csObj->makeArr($arrBrnches,'brnCode','brnDesc','All');
	
	//New Code for Approver 04-25-2024
	$_SESSION['uType'] = "T"; // Time Keeper
	$approverData = $csObj->getTblData("tbltna_approver", " and approverEmpNo='".$_SESSION['employee_number']."' and status='A' AND dateValid >= NOW()", "", "sqlAssoc");
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
	
	$qryIntMaxRec = "SELECT CsApp.refNo, CsApp.csDateFrom, CsApp.csShiftFromIn, CsApp.csShiftFromOut, CsApp.csDateTo, 
							CsApp.csShiftToIn, CsApp.csHiftToOut, CsApp.csReason, CsApp.crossDay, empmast.empLastName, 
							empmast.empFirstName, empmast.empMidName, seqNo, csStat, userApproved, mStat
					 FROM tblTK_CSApp CsApp 
					 INNER JOIN tblEmpMast empmast ON CsApp.empNo = empmast.empNo
					 WHERE (CsApp.compcode = '".$_SESSION["company_code"]."') 
					 		AND (empmast.compCode = '".$_SESSION["company_code"]."') $forApproval
							$brnCodelist";
							
							if($_GET['isSearch'] == 1){
								if($timeKeeperApprover || $timeKeeper) { 
									if($_GET['srchType'] == 0){
										$qryIntMaxRec .= "AND csStat='A' ";
									}
									
									if($_GET['srchType'] == 1){
										$qryIntMaxRec .= "AND csStat='H' ";
									}
								}elseif($managerApporver){
									if($_GET['srchType'] == 0){
										$qryIntMaxRec .= "AND mStat='A' ";
									}
									
									if($_GET['srchType'] == 1){
										$qryIntMaxRec .= "AND mStat='H' ";
									}
								}else{
									if($_GET['srchType'] == 0){
										$qryIntMaxRec .= "AND mStat='A' ";
									}

									if($_GET['srchType'] == 1){
										$qryIntMaxRec .= "AND mStat='H' ";
									}
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
								
								if ($_GET['brnCd']!=0) 
								{
									$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
								}
							}
							
	$qryIntMaxRec.=	"ORDER BY empmast.empLastName, empmast.empFirstName,CsApp.refNo";
	//echo $qryIntMaxRec ;
	$resIntMaxRec = $csObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryCSApp = "SELECT CsApp.refNo, CsApp.csDateFrom, CsApp.csShiftFromIn, CsApp.csShiftFromOut, CsApp.csDateTo, 
						CsApp.csShiftToIn, CsApp.csHiftToOut, CsApp.csReason, CsApp.crossDay, empmast.empLastName, 	
						empmast.empFirstName, empmast.empMidName, seqNo, csStat,userApproved, empmast.empNo, mStat
				 FROM tblTK_CSApp CsApp 
				 INNER JOIN tblEmpMast empmast ON CsApp.empNo = empmast.empNo
				 WHERE (CsApp.compcode = '".$_SESSION["company_code"]."') 
				 		AND (empmast.compCode = '".$_SESSION["company_code"]."') $forApproval
						$brnCodelist ";
							
	if($_GET['isSearch'] == 1){
		if($timeKeeperApprover || $timeKeeper) { 
			if($_GET['srchType'] == 0){
				$qryIntMaxRec .= "AND csStat='A' ";
			}
			
			if($_GET['srchType'] == 1){
				$qryIntMaxRec .= "AND csStat='H' ";
			}
		}elseif($managerApporver){
			if($_GET['srchType'] == 0){
				$qryIntMaxRec .= "AND mStat='A' ";
			}
			
			if($_GET['srchType'] == 1){
				$qryIntMaxRec .= "AND mStat='H' ";
			}
		}else{
			if($_GET['srchType'] == 0){
				$qryIntMaxRec .= "AND mStat='A' ";
			}
	
			if($_GET['srchType'] == 1){
				$qryIntMaxRec .= "AND mStat='H' ";
			}
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
										
		if ($_GET['brnCd']!=0) 
		{
			$qryCSApp .= " AND empbrnCode='".$_GET["brnCd"]."' ";
		}
	}
							
	$qryCSApp.=	"ORDER BY CsApp.csStat desc, empmast.empLastName, empmast.empFirstName,CsApp.refNo limit $intOffset,$intLimit";
	//echo $qryCSApp;
	$resCSAppList = $csObj->execQry($qryCSApp);
	$arrCSAppList = $csObj->getArrRes($resCSAppList);
?>
<input type="hidden" name="shiftDayType" id="shiftDayType" value="<?=$dayType?>" />
<input type="hidden" name="empbrnCode" id="empbrnCode" value="<?=$branch?>" />
<input type="hidden" name="empPayGrp" id="empPayGrp" value="<?=$paygroup?>" />
<input type="hidden" name="empPayCat" id="empPayCat" value="<?=$paycat?>" />

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
                    
                    <td class="hdrInputsLvl" width="10%">&nbsp;</td>
                    
					<td class="hdrInputsLvl" width="5">&nbsp;</td>

					<td class="gridDtlVal" colspan="4">
						<input tabindex="10" class="inputs" type="hidden" name="dateFiled" readonly="readonly" id="dateFiled" size="10"
							 value="<? 	
							 			$format="Y-m-d";
										$strf=date($format);
										echo("$strf"); 
									?>"
										>
                        <input type="hidden" id="hdnCWW" name="hdnCWW" readonly="readonly" value="<?=$cwwTag['CWWTag']?>" />                
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
							if ($_SESSION['user_level'] == 3 || $managerApporver) {
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
							<INPUT tabindex="11" class="inputs" readonly="readonly" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" value="<?=$_SESSION['employeenumber'];?>">
						<?php
							}else{
						?>
							<INPUT tabindex="11" class="inputs" readonly="readonly" type="text" name="txtAddEmpNo" size="15" id="txtAddEmpNo" onkeydown="getEmployee(event,this.value)" value="<?=$_SESSION['employeenumber'];?>">
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
						<INPUT class="inputs" readonly="readonly" type="text" name="txtEmpName" id="txtEmpName" size="40" value="<?=$fullname?>">
					</td>
                    
					<td class="hdrInputsLvl" width="10%">
						Dept. / Position
					</td>
                    
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<INPUT class="inputs" readonly="readonly" type="text" name="txtDeptPost" id="txtDeptPost" size="40" value="<?=$department?>">
					</td>
				</tr>
                
			</TABLE>
			
            <TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
            	<tr style="height:25px;">
                    <td class="gridDtlLbl" align="center" colspan="3">FROM</td>
                    <td class="gridDtlLbl" align="center" colspan="3">TO</td>
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
						<?php
						$empNo = $_GET['empNo'] == '' ? $_SESSION['employeenumber'] : $_GET['empNo'];
						?>
                    	<input tabindex="10" class="inputs" type="text" name="csDateFrom" readonly="readonly" id="csDateFrom" size="10" onfocus="getEmpShift(<?=$empNo?>);"
							 value="<? 	
							 			$format="Y-m-d";
										$strf=date($format);
										echo("$strf"); 
									?>" >
									
										<img src="../../../images/cal_new.png" onClick="displayDatePicker('csDateFrom', this);" style="cursor:pointer;" width="20" height="14">
					
                    </td>
                    <td><input type="text" readonly="readonly" class="inputs" name="schedTimeIn"  id="schedTimeIn" style="width:100%;" value="<?=$schedTimeIn?>" /></td>
                    <td><input type="text" readonly="readonly" class="inputs" name="schedTimeOut"  id="schedTimeOut" style="width:100%;" value="<?=$schedTimeOut?>" /></td>
                  
                  	<td  align="center">
                    	<input tabindex="10" class="inputs" type="text" name="csDateTo" readonly="readonly" id="csDateTo" size="10"
							 value="<? 	
							 			$format="Y-m-d";
										$strf=date($format);
										echo("$strf"); 
									?>" >
                    </td>
                   
                    <td><input type='text' class='inputs' name='csTimeIn' id='csTimeIn'  style='width:100%;' <?=$allowSave ? '' : 'disabled'?> onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value='' onblur="getContent();"></td>
                    <td><input type='text' class='inputs' name='csTimeOut' id='csTimeOut'  style='width:100%;' <?=$allowSave ? '' : 'disabled'?> onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" value=''></td>
                  	<td align="center"><input type="checkbox" name="chkCrossDay" id="chkCrossDay" class="inputs" <?=$allowSave ? '' : 'disabled'?> /></td>
                    <td align="center">
						<?
						$reasons=$csObj->getTblData("tblTK_Reasons "," and stat='A' and changeShift='Y'"," order by reason","sqlArres");
						$arrReasons = $csObj->makeArr($reasons,'reason_id','reason','');
						$csObj->DropDownMenu($arrReasons,'cmbReasons',"","class='inputs'");
						?>
					</td>
                   	<td align="center"><input type="button" class="inputs" name="btnSave" id="btnSave" value='SAVE' <?=$allowSave ? '' : 'disabled'?> onClick="saveCsDetail();"></td>
                </tr>
                
                
			</TABLE>	
            <BR />
            <TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
            <tr style="height:25px;"></tr>
            <tr>
              <td class="hdrLblRow" colspan="12"><font class="hdrLbl"  id="hlprMsg2">Summary of Application</font></td>
            </tr>
            <tr>
              <td colspan="12" class="gridToolbar"> Search
                <input tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs" />
                In
                <?=$csObj->DropDownMenu(array('Approved', 'Held', 'Ref. No.','Employee No.','Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
                <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('csAjaxResult.php','csCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')" />
                <font class="ToolBarseparator">|</font> <a href="#" id="updateEarn" tabindex="3"><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update CS Application" 	onclick="getSeqNo()" /></a>
                <?
                if($_SESSION['user_release']=="Y" || $_SESSION['user_level'] == 2){
                ?>
					<font class="ToolBarseparator">|</font> <a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" id="btnApp" style="visibility:<?=$btAppDel_Dis?>"  src="../../../images/edit_prev_emp.png"  onclick="upObTran('Approved','csCont.php','csCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved CS Application" /></a>
                <?
				}
				?>
                </a> <font class="ToolBarseparator">|</font> <a href="#" id="deleEarn" tabindex="3"><img class="toolbarImg" id="btnDel" style="visibility:<?=$btAppDel_Dis?>"  src="../../../images/application_form_delete.png" title="Delete CS Application" onclick="upObTran('Delete','csAjaxResult.php','csCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" /></a> <font class="ToolBarseparator">|</font>
                <?=$csObj->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');?></td>
            </tr>
            <tr style="height:25px;">
              <td width="1%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" value="1" onclick="this.value=checkAll(this.value);"/></td>
              <td width="5%" class="gridDtlLbl" align="center">EMPLOYEE. NO.</td>
              <td width="15%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
              <td width="8%" class="gridDtlLbl" align="center">CS DATE FROM</td>
              <td width="6%" class="gridDtlLbl" align="center">CS FROM IN</td>
              <td width="6%" class="gridDtlLbl" align="center">CS FROM OUT</td>
			  <td width="8%" class="gridDtlLbl" align="center">CS DATE TO</td>
              <td width="6%" class="gridDtlLbl" align="center">CS TO IN</td>
              <td width="6%" class="gridDtlLbl" align="center">CS TO OUT</td>
              <td width="3%" class="gridDtlLbl" align="center">CROSS DATE</td>
              <td width="15%" class="gridDtlLbl" align="center">REASON</td>
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
						if($timeKeeperApprover) {
							$f_color = ($arrCSAppList_val["csStat"]=='A'?"#CC3300":"");
						}elseif($managerApporver) {
							$f_color = ($arrCSAppList_val["mStat"]=='A'?"#CC3300":"");
						}elseif($timeKeeper) {
							$f_color = ($arrCSAppList_val["csStat"]=='A'?"#CC3300":"");
						}elseif($_SESSION['user_level'] == 3){
							$f_color = ($arrCSAppList_val["mStat"]=='A'?"#CC3300":"");
						}
			?>
            <tr style="height:20px;"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
              <td class="gridDtlVal" align="center">
			  <?
				if($timeKeeperApprover || $timeKeeper || $arrCSAppList_val["mStat"]=='H'){
			  ?>
                <input class="inputs" type="checkbox" name="chkseq[]" value="<?=$arrCSAppList_val['seqNo']?>" id="chkseq[]"  />
              <?
				}
			  ?></td>
              <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$arrCSAppList_val["empNo"]?></font></td>
              <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($arrCSAppList_val["empLastName"].", ".$arrCSAppList_val["empFirstName"]." ")?></font></td>
              <td class="gridDtlVal" align="center" ><font color="<?=$f_color?>">
                <?=date("Y-m-d", strtotime($arrCSAppList_val["csDateFrom"]))?>
              </font></td>
              <td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
                <?=$arrCSAppList_val["csShiftFromIn"]?>
              </font></td>
			  <td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
                <?=$arrCSAppList_val["csShiftFromOut"]?>
              </font></td>
              <td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
                <?=date("Y-m-d", strtotime($arrCSAppList_val["csDateTo"]))?>
              </font></td>
              <td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
                <?=$arrCSAppList_val["csShiftToIn"]?>
              </font></td>
              <td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
                <?=$arrCSAppList_val["csHiftToOut"]?>
              </font></td>
              <td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
                <?=($arrCSAppList_val["crossDay"]=="Y"?"YES":"")?>
              </font></td>
              <td class="gridDtlVal" align="left"><font color="<?=$f_color?>">
                <?
					if(is_numeric($arrCSAppList_val["csReason"])){
						$reasonsRes=$csObj->getTblData("tblTK_Reasons "," and stat='A' and reason_id='".$arrCSAppList_val["csReason"]."'"," order by reason","sqlAssoc");
						echo $reasonsRes['reason'];
					}
					else{
						echo strtoupper($arrCSAppList_val["csReason"]);	
					}
				?>
              </font></td>
            </tr>
            	<?php
							$ctr++;
						}
					}
				?>
            <tr>
              <td colspan="11" align="center" class="childGridFooter"><?
                            $pager->_viewPagerButton('csAjaxResult.php','csCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo'].'&brnCd='.$_GET['brnCd']);
                        ?></td>
            </tr>
            </TABLE></td>
	</tr>
</TABLE>
      
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $csObj->disConnect();?>