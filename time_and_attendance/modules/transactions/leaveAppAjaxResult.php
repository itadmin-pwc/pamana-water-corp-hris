<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/pager.inc.php");
include("../../../includes/common.php");
include("leaveApp.obj.php");

$srchType=0;
$leaveAppObj = new leaveAppObj($_GET,$_SESSION);
$leaveAppObj->validateSessions('','MODULES');

$pager = new AjaxPager(15,'../../../images/');

$getLeaveApp      = $leaveAppObj->getLeaveAppDtl;
$refNo            = $getLeaveApp['refNo'];
$lvReason         = $getLeaveApp['lvReason'];
$empNo            = $getLeaveApp['empNo'];
$dateFiled        = $getLeaveApp['dateFiled'];
$dateLvFrom 	  = $getLeaveApp['lvDateFrom'];
$dateFromAMPM 	  = $getLeaveApp['lvFromAMPM'];
$dateLvTo	      = $getLeaveApp['lvDateTo'];
$dateToAMPM 	  = $getLeaveApp['lvToAMPM'];
$tsAppTypeCd      = $getLeaveApp['tsApptypeCd'];
//$dateReturn       = $getLeaveApp['lvDateReturn'];
//$dateReturnAMPM   = $getLeaveApp['lvReturnAMPM'];
$lvStat 	      = $getLeaveApp['lvStat'];
//$lvReliever       = $getLEaveApp['lvReliever'];
$branch = $_SESSION['branchCode'];


$empInfo = $leaveAppObj->getEmployee($_SESSION['company_code'],$_SESSION['employee_number'],'');

$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
$fullname = $empInfo['empLastName'] . ", " . htmlspecialchars(addslashes($empInfo['empFirstName'])) . " " . $midName;

	//get users branch access
	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $leaveAppObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		//08-08-2023
		// $brnCodelist = " AND emp.empNo<>'".$_SESSION['employee_number']."' 
		// 				and empbrnCode IN (Select brnCode from tblTK_UserBranch 
		// 									where empNo='{$_SESSION['employee_number']}' 
		// 									AND compCode='{$_SESSION['company_code']}')";
		$brnCodelist = " AND emp.empNo='".$_SESSION['employee_number']."' 
						and empbrnCode ='".$branch."'";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblTK_UserBranch 
											where empNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}')";
	}

	$queryBrnches = "SELECT empNo,tblUB.brnCode as brnCode, brnDesc 
					 FROM tblTK_UserBranch tblUB, tblBranch as tblbrn
					 WHERE tblUB.brnCode=tblbrn.brnCode 
						and tblUB.compCode='".$_SESSION["company_code"]."' 
						and tblbrn.compCode='".$_SESSION["company_code"]."'
						and empNo='".$_SESSION['employee_number']."'
					 ORDER BY brnDesc";
	
	$resBrnches = $leaveAppObj->execQry($queryBrnches);
	$arrBrnches = $leaveAppObj->getArrRes($resBrnches);
	$arrBrnch = $leaveAppObj->makeArr($arrBrnches,'brnCode','brnDesc','All');


	$qryIntMaxRec = "SELECT dtl.empNo
					 FROM tblTK_LeaveApp as dtl 
					 LEFT JOIN tblEmpMast as emp ON dtl.compCode = emp.CompCode AND dtl.empNo = emp.empNo 
					 WHERE dtl.compCode = '{$_SESSION['company_code']}' 
						$brnCodelist";
						
			if($_GET['isSearch'] == 1){
				if($_GET['srchType'] == 0){
					$qryIntMaxRec .= "AND dtl.lvStat='A'";
				}
				if($_GET['srchType'] == 1){
					$qryIntMaxRec .= "AND dtl.lvStat='H'";
				}
				if($_GET['srchType'] == 2){
					$qryIntMaxRec .= "AND dtl.refNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
				}
				if($_GET['srchType'] == 3){
					$qryIntMaxRec .= "AND dtl.empNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
				}
				if($_GET['srchType'] == 4){
					$qryIntMaxRec .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
				}
				if ($_GET['brnCd']!=0){
					$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
				}
				
			}
			$qryIntMaxRec .= "ORDER BY emp.empLastName, dtl.lvdateFrom ";
		
	$resIntMaxRec = $leaveAppObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	if(empty($intOffset)){
		$intOffset = 0;
	}
	
	$qrygetLeaveAppDtl = "SELECT dtl.compCode,dtl.empNo,dtl.refNo,dtl.dateFiled,dtl.lvDateFrom,dtl.lvFromAMPM,dtl.lvDateTo,
							dtl.lvToAMPM, dtl.tsAppTypeCd, dtl.lvDateReturn, lvReturnAMPM, dtl.lvReason, dtl.lvReliever, dtl.lvAuthorized, 
							dtl.lvStat, dtl.seqNo, dtl.userApproved,tblTK_AppTypes.appTypeShortDesc, emp.empFirstName,emp.empMidName,
							emp.empLastName, emp.empNo
						  FROM tblTK_LeaveApp dtl 
						  INNER JOIN tblTK_AppTypes ON dtl.tsAppTypeCd = tblTK_AppTypes.tsAppTypeCd 
						  LEFT OUTER JOIN tblEmpMast emp ON dtl.compCode = emp.compCode AND dtl.empNo = emp.empNo
						  WHERE dtl.compCode = '{$_SESSION['company_code']}'  
						  $brnCodelist
						  ";
	
		if($_GET['isSearch'] == 1){
				if($_GET['srchType'] == 0){
					$qrygetLeaveAppDtl .= "AND lvStat='A'";
				}
				if($_GET['srchType'] == 1){
					$qrygetLeaveAppDtl .= "AND lvStat='H'";
				}
				if($_GET['srchType'] == 2){
					$qrygetLeaveAppDtl .= "AND dtl.refNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
				}
				if($_GET['srchType'] == 3){
					$qrygetLeaveAppDtl .= "AND dtl.empNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
				}
				if($_GET['srchType'] == 4){
					$qrygetLeaveAppDtl .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
				}
				if ($_GET['brnCd']!=0){
					$qrygetLeaveAppDtl.= " AND empbrnCode='".$_GET["brnCd"]."' ";
				}
		 }
		
		$qrygetLeaveAppDtl .= "ORDER BY emp.empLastName, dtl.lvdateFrom limit $intOffset,$intLimit";
		
		$resgetLeaveAppDtl = $leaveAppObj->execQry($qrygetLeaveAppDtl);
		$arrgetLeaveAppDtl = $leaveAppObj->getArrRes($resgetLeaveAppDtl);

?>

<TABLE border ="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		
    <td height="4" colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Leave 
      Application</td>
	</tr>
	<tr>
		
		<td colspan="6" class="gridToolbar">
			&nbsp;
			
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.gif'  onclick="pager('leaveAppAjaxResult.php','leaveAppCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
		</td>
	
	</tr>
	<tr>
	  <td class="parentGridDtl" valign="top">
			<!--header-->					
	    <TABLE cellpadding="1" cellspacing="1" border="0" class="hdrTable" width="100%">
				<tr>
					<td class="hdrLblRow" colspan="22">
						<FONT class="hdrLbl">Application Detail</font>
					</td>
				</tr>
				<tr>
					<td class="hdrInputsLvl">
						<?php
							if ($_SESSION['user_level'] == 3)  {
						?>
							Employee No.
						<?php
							}else{
						?>
							<a href="#" onclick="empLookup('../../../includes/employee_lookup_tna.php')">Employee No.</a>
						<?php
							}
						?>
					</td>
					<td class="hdrInputsLvl">
						:
					</td>
					<td class="gridDtlVal">
						<?php
							if ($_SESSION['user_level'] == 3)  {
						?>
							<INPUT tabindex="9" class="inputs" type="text" name="txtAddEmpNo" id="txtAddEmpNo" value="<?=$_SESSION['employeenumber']?>" readonly>
						<?php
							}else{
						?>
							<INPUT tabindex="9" class="inputs" type="text" name="txtAddEmpNo" id="txtAddEmpNo" onkeydown="getEmployee(event,this.value)" value="<?=$_SESSION['employeenumber']?>" readonly>
						<?php
							}
						?>
					</td>
					<td class="hdrInputsLvl" width="124">
						Employee Name
					</td>
					<td class="hdrInputsLvl">
						:
					</td>
					
					<td colspan="4" class="gridDtlVal">
						
						<INPUT class="inputs" type="text" name="txtAddEmpName" id="txtAddEmpName" size="40" value="<?=$fullname?>">
						<span class="grdiDtlVal">
						<input tabindex="10" class="inputs" type="hidden" name="dateFiled" id="dateFiled" size="10"
							 value="<? 	
							 			$format="Y-m-d";
										$strf=date($format);
										echo("$strf"); 
									?>" />
					</span></td>
				</tr>
			    <tr>
					<td class="hdrInputsLvl" width="127">
						Leave Type
					</td>
					<td class="hdrInputsLvl" width="7">
						:
					</td>
					<td class="gridDtlVal" width="287">
						<?
							$leaveAppObj->DropDownMenu($leaveAppObj->makeArr($leaveAppObj->getTsAppType(''),'tsAppTypeCd','appTypeDesc',''),'cmbLeaveApp',$leaveAppObj->appType,'class="inputs" style="width:250px;"');
						?>
					</td>
					<td class="hdrInputsLvl" width="124">Reason for Filing </td>
					<td class="hdrInputsLvl" width="7">: </td>
					<td colspan="4" class="gridDtlVal">
					<?
						$reasons=$leaveAppObj->getTblData("tblTK_Reasons "," and stat='A' and leaveApp='Y'"," order by reason","sqlArres");
						$arrReasons = $leaveAppObj->makeArr($reasons,'reason_id','reason','');
						$leaveAppObj->DropDownMenu($arrReasons,'cmbReasons',"","class='inputs'");
					?></td>
				</tr>
				</TABLE>
<TABLE width="100%" cellpadding="1" cellspacing="1" border="0" align="center">
				<tr style="height:25px;">
          			<td width="26%" class="gridDtlLbl" align="center">FROM</td>
          			<td width="36%" class="gridDtlLbl" align="center">TO</td>
          			<td width="20%" class="gridDtlLbl" align="center">DEDUCT TAG</td>
					<td width="18%" class="gridDtlLbl" align="center">ACTION</td>
				</tr>
				
				<tr>
					<td class="gridDtlVal" align="center">
						<INPUT name="dateLvFrom" type="text" class="inputs" id="dateLvFrom" tabindex="11" size="15" readonly,>
						<img src="../../../images/cal_new.gif" onClick="displayDatePicker('dateLvFrom', this);" style="cursor:pointer;" width="20" height="14">&nbsp;
						<?
							$leaveAppObj->DropDownMenu(array('WD'=>'WD','AM' => 'HF AM','PM' => 'HF PM'),'cmbFromAMPM',$lvAMPMFrom,'class="inputs" tabindex="16"');
						?>
					</td>
					
					<td class="gridDtlVal" align="center">

						<INPUT name="dateLvTo" type="text" class="inputs" id="dateLvTo" tabindex="11" size="15" readonly,>
            			<img src="../../../images/cal_new.gif" onClick="displayDatePicker('dateLvTo', this);" style="cursor:pointer;" width="20" height="14">&nbsp; 
           				<?
							$leaveAppObj->DropDownMenu(array('WD'=>'WD','AM' => 'HF AM','PM' => 'HF PM'),'cmbToAMPM',$lvAMPMTo,'class="inputs" tabindex="16"');
						?>
          			</td>
					<td class="gridDtlVal" align="center">
				    <?
							$leaveAppObj->DropDownMenu(array(''=>'','Y' => 'Yes'),'chkDeduct','','class="inputs" tabindex="16"');
						?></td>
					
					
          			<td class="gridDtlVal" align="center">

						<?	
							$action = 'addDtl';
						?>

						<INPUT tabindex="16"class="inputs" type="button" name="btnSaveAddDtl" id="btnSaveAddDtl" value="SAVE" onclick="maintLeaveApp('leaveApp.php','leaveAppCont','<?=$action?>','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')">
						
					</td>
				</tr>
                <tr>
				  <td colspan="7">&nbsp;</td>
				</tr>
				<tr><td class="hdrLblRow" colspan="7">
					  <FONT class="hdrLbl">Summary of Application</font>
				</td></tr>
								
</TABLE>
	
	<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
        
  <tr>
			<td colspan="11" class="gridToolbar">
						Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
            In 
            <?=$leaveAppObj->DropDownMenu(array('Approved','Held','Ref No.','Employee No.','Las Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
            <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('leaveAppAjaxResult.php','leaveAppCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
         	<FONT class="ToolBarseparator">|</font>	
			<a href="#"  id="btnEdit"onClick=""><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update Leave Application" onclick="getSeqNo()"></a> 
			<?
            if($_SESSION['user_release']=="Y"){
            ?>                                                		
			<FONT class="ToolBarseparator">|</font>
            <a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" src="../../../images/edit_prev_emp.png"  onclick="updateLvTran('updateLvTran','leaveAppAjaxResult.php','leaveCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved Leave Application" ></a>
            <?
			}
			?>
            <FONT class="ToolBarseparator">|</font>
            <a href="#" id="deleLeave" tabindex="3"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Leave Application" onclick="delLeaveAppDtl('delLeaveAppDtl','leaveAjaxResult.php','leaveAppCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
			<FONT class="ToolBarseparator">|</font>
			<?=$leaveAppObj->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');?>
			
		 </td>
		  
        <tr> 
		  <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
		  <td width="8%" align="center" class="gridDtlLbl">EMPLOYEE NO.</td>
		  <td width="22%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
		  <td width="8%" class="gridDtlLbl" align="center">DATE FILED</td>
          <td width="11%" class="gridDtlLbl" align="center">LEAVE TYPE</td>
          <td width="11%" class="gridDtlLbl" align="center"> FROM</td>
          <td width="11%" class="gridDtlLbl" align="center">TO</td>
          <td width="21%" class="gridDtlLbl" align="center">REASON FOR FILING</td>
		  <td width="6%" class="gridDtlLbl" align="center">LEAVE STATUS</td>
          
        </tr>
		
		<?
					if(@$leaveAppObj->getRecCount($resgetLeaveAppDtl) > 0)
					{
						$i=0;
						$ctr=1;
							
						foreach (@$arrgetLeaveAppDtl as $leaveAppDtlVal)
						{
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							$f_color = ($leaveAppDtlVal["lvStat"]=='A'?"#CC3300":"");
				?>
                			<tr style="height:20px;"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                            	<td class="gridDtlVal" align="center">
                                <?php
									if(($leaveAppDtlVal["lvStat"]=='H') || (($leaveAppDtlVal["userApproved"]==$_SESSION['employee_number']) && ($leaveAppDtlVal["lvStat"]=='A')))
									{
										
								?>
                                		<input class="inputs" type="checkbox" name="chkseq[]" value="<?=$leaveAppDtlVal['seqNo']?>" id="chkseq[]" />
                                <?php
									}	
								?>
          						</td>
                               
                               
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$leaveAppDtlVal["empNo"]?></td>
								<td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($leaveAppDtlVal["empLastName"].", ".$leaveAppDtlVal["empFirstName"]." ")?></td>
								<td class="gridDtlVal" align="CENTER"><font color="<?=$f_color?>"><?=date("Y-m-d", strtotime($leaveAppDtlVal["dateFiled"]));?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$leaveAppDtlVal["appTypeShortDesc"]?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("Y-m-d", strtotime($leaveAppDtlVal["lvDateFrom"]))?> <?=$leaveAppDtlVal["lvFromAMPM"]?></td>
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=date("Y-m-d", strtotime($leaveAppDtlVal["lvDateTo"]))?> <?=$leaveAppDtlVal["lvToAMPM"]?></td>
                                <td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?
								if(is_numeric($leaveAppDtlVal["lvReason"])){
									$leaveRes=$leaveAppObj->getTblData("tblTK_Reasons "," and stat='A' and reason_id='".$leaveAppDtlVal["lvReason"]."'"," order by reason","sqlAssoc");
									echo $leaveRes['reason'];
									
								}
								else{
									echo strtoupper($leaveAppDtlVal["lvReason"]);	
								}
								
								?></td>
                            	<td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
									<?
										if ($leaveAppDtlVal['lvStat'] == 'H'){
											echo ($leaveAppDtlVal['lvStat'] =="H"? "Held":"Approved");
										}else{
											echo ($leaveAppDtlVal['lvStat'] =="A"? "Approved":"Held");
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
                      <td colspan="11" align="center" class="childGridFooter"> 
                        <?
                            $pager->_viewPagerButton('leaveAppAjaxResult.php','leaveAppCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo'].'&brnCd='.$_GET['brnCd']);
                        ?>
                      </td>
                </tr>
          </TABLE>
	  </td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $leaveAppObj->disConnect();?>
