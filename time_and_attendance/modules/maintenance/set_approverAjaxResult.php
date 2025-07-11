<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/pager.inc.php");
include("../../../includes/common.php");
include("set_approver.obj.php");

$srchType=0;
$AppObj = new ApprObj($_GET, $_SESSION);
$AppObj->validateSessions('','MODULES');

$pager = new AjaxPager(50,'../../../images/');

$branch = $_SESSION['branchCode'];

//08-09-2023 AUTO EMPLOYEE LOOKUP
$empInfo = $AppObj->getEmployee($_SESSION['company_code'],$_SESSION['employeenumber'],'');

$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
$fullname = $empInfo['empLastName'] . ", " . htmlspecialchars(addslashes($empInfo['empFirstName'])) . " " . $midName;
//08-09-2023

	//get users branch access
	if ($_SESSION['user_level'] == 3) 
	{
		$branch = $_SESSION['branchCode'];
		$userinfo = $AppObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		//08-08-2023
		// $brnCodelist = " AND emp.empNo<>'".$_SESSION['employee_number']."' 
		// 				 and emp.empbrnCode IN (Select brnCode from tblTK_UserBranch 
		// 									where empNo='{$_SESSION['employee_number']}' 
		// 										AND compCode='{$_SESSION['company_code']}')";
		$brnCodelist = " AND tna.approverEmpNo='".$_SESSION['employee_number']."' 
						and emp_approver.empbrnCode ='".$branch."'";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND  emp_approver.empbrnCode IN (Select brnCode from tblTK_UserBranch 
											where empNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}')";
	}
	
	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc 
					 from tblTK_UserBranch tblUB, tblBranch as tblbrn
					 where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' 
					 	and tblbrn.compCode='".$_SESSION["company_code"]."'
					 	and empNo='".$_SESSION['employee_number']."'
					 order by brnDesc";
	
	$resBrnches = $AppObj->execQry($queryBrnches);
	$arrBrnches = $AppObj->getArrRes($resBrnches);
	$arrBrnch = $AppObj->makeArr($arrBrnches,'brnCode','brnDesc','All');

	$qryIntMaxRec = "SELECT 
						tna.approverEmpNo, 
						emp_approver.empbrnCode,
						emp_approver.empLastName,
						emp_approver.empFirstName,
						emp_approver.empMidName,
						tna.subordinateEmpNo,
						emp_sub.empLastName as subLastName,
						emp_sub.empFirstName as subFirstName,
						emp_sub.empMidName as subMiddleName,
						tna.status
					FROM tbltna_approver as tna 
					LEFT OUTER JOIN tblEmpMast as emp_approver ON emp_approver.empNo = tna.approverEmpNo
					LEFT OUTER JOIN tblEmpMast as emp_sub ON emp_sub.empNo = tna.subordinateEmpNo
					WHERE tna.compCode = '{$_SESSION['company_code']}' $brnCodelist ";
				 
        if($_GET['isSearch'] == 1){
	       	if($_GET['srchType'] == 0){
	        	$qryGetAppDtl .= "AND tna.status='A'";
	        }
	       	if($_GET['srchType'] == 1){
	        	$qryGetAppDtl .= "AND tna.status='H'";
	        }
	        if($_GET['srchType'] == 2){
        		$qryGetAppDtl .= "AND emp_approver.approverEmpNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 3){
        		$qryGetAppDtl .= "AND emp_approver.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 4){
        		$qryGetAppDtl .= "AND emp_sub.approverEmpNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 5){
        		$qryGetAppDtl .= "AND emp_sub.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
	 		if ($_GET['brnCd']!=0){
				$qryGetAppDtl.= " AND emp_approver.empbrnCode='".$_GET["brnCd"]."' ";
			}
	 	}
        $qryIntMaxRec .= "ORDER BY emp_approver.empLastName, emp_approver.empFirstName";
		
	$resIntMaxRec = $AppObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	if(empty($intOffset)){
		$intOffset = 0;
	}
	
	$qryGetAppDtl = "SELECT 
							tna.ID,
							tna.approverEmpNo, 
							emp_approver.empLastName,
							emp_approver.empFirstName,
							emp_approver.empMidName,
							tna.subordinateEmpNo,
							emp_sub.empLastName as subLastName,
							emp_sub.empFirstName as subFirstName,
							emp_sub.empMidName as subMiddleName,
							tna.status
						FROM tbltna_approver as tna 
						LEFT OUTER JOIN tblEmpMast as emp_approver ON emp_approver.empNo = tna.approverEmpNo
						LEFT OUTER JOIN tblEmpMast as emp_sub ON emp_sub.empNo = tna.subordinateEmpNo
						WHERE tna.compCode = '{$_SESSION['company_code']}' $brnCodelist ";

	if(empty($_GET['srchType'])) {
		$qryGetAppDtl .= "AND tna.status='A'";
	}
	if($_GET['isSearch'] == 1){
	    if($_GET['srchType'] == 0){
	    	$qryGetAppDtl .= "AND tna.status='A'";
	    }
	    if($_GET['srchType'] == 1){
	     	$qryGetAppDtl .= "AND tna.status='V'";
	    }
	    if($_GET['srchType'] == 2){
        	$qryGetAppDtl .= "AND tna.approverEmpNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        }
		if($_GET['srchType'] == 3){
        	$qryGetAppDtl .= "AND emp_approver.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        }
		if($_GET['srchType'] == 4){
        	$qryGetAppDtl .= "AND tna.subordinateEmpNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        }
		if($_GET['srchType'] == 5){
        	$qryGetAppDtl .= "AND emp_sub.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        }
	 	if ($_GET['brnCd']!=0){
			$qryGetAppDtl.= " AND emp_approver.empbrnCode='".$_GET["brnCd"]."' ";
		}
	 }
	
	$qryGetAppDtl .= "ORDER BY emp_approver.empLastName, emp_approver.empFirstName limit $intOffset,$intLimit";

	//echo $qryGetAppDtl;
	
	$resGetAppDtl = $AppObj->execQry($qryGetAppDtl);
	$arrGetAppDtl = $AppObj->getArrRes($resGetAppDtl);
?>

<TABLE border ="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		
    <td height="4" colspan="4" class="parentGridHdr"><img src="../../../images/grid.png">&nbsp;Set Employee Approver</td>
	</tr>
	<tr>
		
		<td colspan="6" class="gridToolbar">
				<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.png'  onclick="pager('set_approverAjaxResult.php','approverCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
		</td>
	
	</tr>
	<tr>
		<td class="parentGridDtl" valign="top">
			<!--header-->					
			<TABLE cellpadding="1" cellspacing="1" border="0" class="hdrTable" width="100%">
				<tr>
					<td class="hdrLblRow" colspan="16">
						<FONT class="hdrLbl">Add Approver</font>
					</td>
				</tr>
				<tr>
					
          		<td class="hdrInputsLvl" width="127">
				  	<?php
						if ($_SESSION['user_level'] == 3)  {
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
					
          		<td class="hdrInputsLvl" width="47">:</td>
          		
         		<td width="203" class="gridDtlVal">
					<?php
						if ($_SESSION['user_level'] == 3)  {
					?>
						<input tabindex="9" class="inputs" type="text" name="txtAddEmpNo" id="txtAddEmpNo" value="<?=$_SESSION['employeenumber'];?>" readonly>
					<?php
						}else{
					?>
						<input tabindex="9" class="inputs" type="text" name="txtAddEmpNo" id="txtAddEmpNo" onKeyDown="getEmployee(event,this.value,'apr')" value="<?=$_SESSION['employeenumber'];?>" readonly>
					<?php
						}
					?>
           		</td>
				<td class="hdrInputsLvl" width="105">
					Approver
				</td>
				<td width="55" class="hdrInputsLvl">
					:
				</td>
				<td width="535" colspan="4" class="gridDtlVal">
					<INPUT class="inputs" readonly="readonly" type="text" name="txtAddEmpName" id="txtAddEmpName" size="50" value="<?=$fullname?>">
					<span class="grdiDtlVal">
					<input tabindex="10" class="inputs" type="hidden" name="app_date" id="app_date" size="10"
							 value="<? 	
							 			$format="Y-m-d";
										$strf=date($format);
										echo("$strf"); 
							?>" />
					</span>
				</td>
			</tr>
				<tr>
					
          		<td class="hdrInputsLvl" width="127">
				  	<?php
						if ($_SESSION['user_level'] == 3)  {
					?>
						Employee No.
					<?php
						}else{
					?>
						<a href="#" onclick="empLookup('../../../includes/employee_lookup_tna.php?empType=subordinate')">Employee  No.</a>
					<?php
						}
					?>
				</td>
					
          		<td class="hdrInputsLvl" width="47">:</td>
          		
         		<td width="203" class="gridDtlVal">
					<?php
						if ($_SESSION['user_level'] == 3)  {
					?>
						<input tabindex="9" class="inputs" type="text" name="txtAddEmpNo2" id="txtAddEmpNo2" readonly>
					<?php
						}else{
					?>
						<input tabindex="9" class="inputs" type="text" name="txtAddEmpNo2" id="txtAddEmpNo2" onKeyDown="getEmployee(event,this.value,'sub')" readonly>
					<?php
						}
					?>
           		</td>
				<td class="hdrInputsLvl" width="105">
					Subordinate
				</td>
				<td width="55" class="hdrInputsLvl">
					:
				</td>
				<td width="360" colspan="1" class="gridDtlVal">
					<INPUT class="inputs" readonly="readonly" type="text" name="txtAddEmpName2" id="txtAddEmpName2" size="50">
					<span class="grdiDtlVal">
					<input tabindex="10" class="inputs" type="hidden" name="dateFiled" id="dateFiled" size="10"
							 value="<? 	
							 			$format="Y-m-d";
										$strf=date($format);
										echo("$strf"); 
							?>" />
					</span>
				</td>
				<td>
					<INPUT tabindex="16"class="inputs" type="button" name="btnSaveAddDtl" id="btnSaveAddDtl" value="SAVE" onclick="maintApp('set_approver.php','approverCont','addDtl','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')">
				</td>
			</tr>
			</TABLE>
	
	<BR />
	<table border="0" width="100%" cellpadding="1" cellspacing="1" class="" >
        	<tr>
				<td class="hdrLblRow" colspan="10">
					<FONT class="hdrLbl"  id="hlprMsg">List of Approver</font>
				</td>
			</tr>
		<tr>
			<td colspan="10" class="gridToolbar">
						Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
            In 
            <?=$AppObj->DropDownMenu(array('Active', 'Voided', 'Approver EmpNo.', 'Approver Last Name', 'Subordinate EmpNo', 'Subordinate Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
            <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('set_approverAjaxResult.php','approverCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
         	<!-- <FONT class="ToolBarSeparator">|</font>
			<a href="#"  id="btnEdit"onClick=""><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update Approver Record" onclick="getSeqNo()"></a>   -->
			<?
            if($_SESSION['user_release']=="Y"){
            ?>                                               	
			<FONT class="ToolBarseparator">|</font>
            <a href="#" id="btnApp" tabindex="2"><img class="toolbarImg" src="../../../images/edit_prev_emp.png"  onclick="updateTran('updateTran','set_approverAjaxResult.php','approverCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Set As Active" ></a>
            <?
			}
			?>
            <FONT class="ToolBarseparator">|</font>
            <a href="#" id="btnDel" tabindex="3"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Approver Record" onclick="delAppDtl('delAppDtl','set_approverAjaxResult.php','approverCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
			<FONT class="ToolBarseparator">|</font>
			<?=$AppObj->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');?>

		 </td>
		  
        <tr> 
		  <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
		  <td width="9%" class="gridDtlLbl" align="center">EMPLOYEE NO</td>
		  <td width="15%" class="gridDtlLbl">APPROVER</td>
		  <td width="9%" class="gridDtlLbl" align="center">EMPLOYEE NO</td>
		  <td width="15%" class="gridDtlLbl">SUBORDINATE</td>
          <td width="6%" class="gridDtlLbl" align="center"> STATUS</td>
         
        </tr>
		
		<?
				if(@$AppObj->getRecCount($resGetAppDtl) > 0){

					$i=0;
					$ctr=1;
					foreach (@$arrGetAppDtl as $otAppDtlVal){

						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
						$f_color = ($otAppDtlVal["status"]=='V'?"#CC3300":"");
		?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>> 
          		<td class="gridDtlVal" align="center">
					<input class="inputs" type="checkbox" name="chkseq[]" value="<?=$otAppDtlVal['ID']?>" id="chkseq[]" />
				</td>           
		  		<td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$otAppDtlVal['approverEmpNo']?></td>
          		<td class="gridDtlVal"> <font color="<?=$f_color?>">
            		<?=$otAppDtlVal['empLastName'].", ".$otAppDtlVal['empFirstName']." "?>
		   		</td>		
          		<td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$otAppDtlVal['subordinateEmpNo']?></td>
          		<td class="gridDtlVal"> <font color="<?=$f_color?>">
            		<?=$otAppDtlVal['subLastName'].", ".$otAppDtlVal['subFirstName']." "?>
		   		</td>
          		<td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
				<?
					echo ($otAppDtlVal['status'] == "A"? "Active":"Voided");
				?>
            	</td>
          
       	    </tr>
        	<?
				$ctr++;
					}
				}
				else{
			?>
          
		  <tr> 
          <td colspan="10" align="center"> <font class="zeroMsg">NOTHING TO DISPLAY</font> 
          </td>
        </tr>
        <?}?>
        <tr> 
          <td colspan="10" align="center" class="childGridFooter"> 
            <?
				$pager->_viewPagerButton('set_approverAjaxResult.php','approverCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo'].'&brnCd='.$_GET['brnCd']);
			?>
          </td>
        </tr>
    
		</TABLE>
	
</TABLE>
<font style="<?=$otExempted ? '' : 'display: none;'?>" color="red"><strong>OT Exempted</strong></font>

<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<?$AppObj->disConnect();?>