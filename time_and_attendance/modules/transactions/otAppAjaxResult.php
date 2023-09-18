<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/pager.inc.php");
include("../../../includes/common.php");
include("otApp.obj.php");

$srchType=0;
$OtAppObj = new otAppObj($_GET,$_SESSION);
$OtAppObj->validateSessions('','MODULES');

$pager = new AjaxPager(15,'../../../images/');

$getOtApp = $OtAppObj->getOtAppDtl;

$branch = $_SESSION['branchCode'];

//08-09-2023 AUTO EMPLOYEE LOOKUP
$empInfo = $OtAppObj->getEmployee($_SESSION['company_code'],$_SESSION['employeenumber'],'');

$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
$fullname = $empInfo['empLastName'] . ", " . htmlspecialchars(addslashes($empInfo['empFirstName'])) . " " . $midName;
$otExempted = false;
$level = $empInfo['empLevel'];
if ($level > '70'){
	$otExempted = true;
}
//08-09-2023

	//get users branch access
	if ($_SESSION['user_level'] == 3) 
	{
		$branch = $_SESSION['branchCode'];
		$userinfo = $OtAppObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		//08-08-2023
		// $brnCodelist = " AND emp.empNo<>'".$_SESSION['employee_number']."' 
		// 				 and emp.empbrnCode IN (Select brnCode from tblTK_UserBranch 
		// 									where empNo='{$_SESSION['employee_number']}' 
		// 										AND compCode='{$_SESSION['company_code']}')";
		$brnCodelist = " AND emp.empNo='".$_SESSION['employee_number']."' 
						and empbrnCode ='".$branch."'";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblTK_UserBranch 
											where empNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}')";
	}
	
	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc 
					 from tblTK_UserBranch tblUB, tblBranch as tblbrn
					 where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' 
					 	and tblbrn.compCode='".$_SESSION["company_code"]."'
					 	and empNo='".$_SESSION['employee_number']."'
					 order by brnDesc";
	
	$resBrnches = $OtAppObj->execQry($queryBrnches);
	$arrBrnches = $OtAppObj->getArrRes($resBrnches);
	$arrBrnch = $OtAppObj->makeArr($arrBrnches,'brnCode','brnDesc','All');

	$qryIntMaxRec = "SELECT dtl.empNo
					 FROM tblTK_otApp as dtl 
					 INNER JOIN tblEmpMast as emp ON dtl.compCode = emp.CompCode AND dtl.empNo = emp.empNo 
					 WHERE (dtl.compCode = '{$_SESSION['company_code']}')
					 	AND (emp.compCode = '{$_SESSION["company_code"]}')
					 	$brnCodelist";
				 
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND dtl.otStat='A'";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND dtl.otStat='H'";
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
        $qryIntMaxRec .= "ORDER BY emp.empLastName,emp.empFirstName ";
		
	$resIntMaxRec = $OtAppObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	if(empty($intOffset)){
		$intOffset = 0;
	}
	
	$qryGetOtAppDtl = "SELECT dtl.compCode, dtl.empNo, dtl.otDate, dtl.refNo, dtl.dateFiled, dtl.otReason, dtl.otIn,
							dtl.otOut, dtl.otStat, dtl.crossTag, dtl.seqNo, dtl.userApproved, emp.empFirstName, emp.empMidName,
							emp.empLastName
					   FROM tblTK_otApp as dtl 
					   INNER JOIN tblEmpMast as emp ON dtl.compCode = emp.CompCode AND dtl.empNo = emp.empNo
					   WHERE dtl.compCode = '{$_SESSION['company_code']}' $brnCodelist ";

	if($_GET['isSearch'] == 1){
	       	if($_GET['srchType'] == 0){
	        	$qryGetOtAppDtl .= "AND otStat='A'";
	        }
	       	if($_GET['srchType'] == 1){
	        	$qryGetOtAppDtl .= "AND otStat='H'";
	        }
	        if($_GET['srchType'] == 2){
        		$qryGetOtAppDtl .= "AND dtl.refNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 3){
        		$qryGetOtAppDtl .= "AND dtl.empNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 4){
        		$qryGetOtAppDtl .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
	 		if ($_GET['brnCd']!=0){
				$qryGetOtAppDtl.= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
	 
	 
	 }
	
	$qryGetOtAppDtl .= "ORDER BY emp.empLastName, emp.empFirstName, dtl.otDate limit $intOffset,$intLimit";
	
	$resGetOtAppDtl = $OtAppObj->execQry($qryGetOtAppDtl);
	$arrGetOtAppDtl = $OtAppObj->getArrRes($resGetOtAppDtl);
?>

<TABLE border ="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid" style="<?=$otExempted ? 'display: none;' : ''?>">
	<tr>
		
    <td height="4" colspan="4" class="parentGridHdr"><img src="../../../images/grid.png">&nbsp;Overtime 
      Application</td>
	</tr>
	<tr>
		
		<td colspan="6" class="gridToolbar">
				<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.png'  onclick="pager('otAppAjaxResult.php','otAppCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
		</td>
	
	</tr>
	<tr>
		<td class="parentGridDtl" valign="top">
			<!--header-->					
			<TABLE cellpadding="1" cellspacing="1" border="0" class="hdrTable" width="100%">
				<tr>
					<td class="hdrLblRow" colspan="16">
						<FONT class="hdrLbl">Application Detail</font>
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
						<input tabindex="9" class="inputs" type="text" name="txtAddEmpNo" id="txtAddEmpNo" onKeyDown="getEmployee(event,this.value)" value="<?=$_SESSION['employeenumber'];?>" readonly>
					<?php
						}
					?>
           		</td>
				<td class="hdrInputsLvl" width="105">
					Employee Name
				</td>
				<td width="55" class="hdrInputsLvl">
					:
				</td>
				<td width="535" colspan="4" class="gridDtlVal">
					<INPUT class="inputs" readonly="readonly" type="text" name="txtAddEmpName" id="txtAddEmpName" size="50" value="<?=$fullname?>">
					<span class="grdiDtlVal">
					<input tabindex="10" class="inputs" type="hidden" name="dateFiled" id="dateFiled" size="10"
							 value="<? 	
							 			$format="Y-m-d";
										$strf=date($format);
										echo("$strf"); 
							?>" />
					</span>
				</td>
				<tr>

				<td class="hdrInputsLvl" width="127" >
						Purpose of Overtime	
					</td>
					<td class="hdrInputsLvl" width="47">
						:
					</td>

					<td class="gridDtlVal" colspan="4"><?
						$reasons=$OtAppObj->getTblData("tblTK_Reasons "," and stat='A' and ovApp='Y'"," order by reason","sqlArres");
						$arrReasons = $OtAppObj->makeArr($reasons,'reason_id','reason','');
						$OtAppObj->DropDownMenu($arrReasons,'cmbReasons',"","class='inputs'");
					?></td>
					
				</tr>

			</TABLE>
			
			<TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
				<tr height="25px">
					<td width="10%" class="gridDtlLbl" align="center">DATE OVERTIME</td>
					<td width="10%" class="gridDtlLbl" align="center">OVERTIME IN</td>
          			<td width="10%" class="gridDtlLbl" align="center">OVERTIME OUT</td>
        			<td width="5%" class="gridDtlLbl" align="center">CROSS DATE</td>		
					<td width="5%" class="gridDtlLbl" align="center">ACTION</td>
				</tr>
				
				<tr>
					<td class="gridDtlVal" align="center">
						<INPUT tabindex="11" class="inputs" type="text" name="dateOt" id="dateOt" onkeydown= "checkShift(event,this.event)",readonly>
						<img src="../../../images/cal_new.png" onClick="displayDatePicker('dateOt', this);" style="cursor:pointer;" width="20" height="14">
					</td>
					
					<td class="gridDtlVal" align="center">

				    <INPUT name="txtOtIn" type="text" class="inputs" id="txtOtIn" tabindex="12" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" size="15"maxlength="5"></td>
					
					<td class="gridDtlVal" align="center">
						<INPUT name="txtOtOut" type="text" class="inputs"  id="txtOtOut" tabindex="13" onKeyDown="javascript:return dFilter (event.keyCode, this, '##:##');" size="15">
					</td>
	
        		<td class="gridDtlVal" align="center"> 
          			<INPUT tabindex="14" class="inputs" type="checkbox" name="chkCrossDate" id="chkCrossDate" disabled="disabled" >
				</td>
					
          			
        		<td class="gridDtlVal" align="center"> 
            	<?
					$action = 'addDtl';
				?>
            		<INPUT tabindex="16"class="inputs" type="button" name="btnSaveAddDtl" id="btnSaveAddDtl" value="SAVE" onclick="maintOtApp('otApp.php','otAppCont','<?=$action?>','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')">
						
					</td>
				</tr>
		</td>
	</tr>
</TABLE>
	
	<BR />
	<table border="0" width="100%" cellpadding="1" cellspacing="1" class="" >
        	<tr>
					<td class="hdrLblRow" colspan="10">
						<FONT class="hdrLbl"  id="hlprMsg">Summary of Application</font>
					</td>
				</tr>
		<tr>
			<td colspan="10" class="gridToolbar">
						Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
            In 
            <?=$OtAppObj->DropDownMenu(array('Approved', 'Held', 'Ref. No.','Employee No.','Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
            <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('otAppAjaxResult.php','otAppCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
         	<FONT class="ToolBarSeparator">|</font>
			<a href="#"  id="btnEdit"onClick=""><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update OT Application" onclick="getSeqNo()"></a>  
			<?
            if($_SESSION['user_release']=="Y"){
            ?>                                               	
			<FONT class="ToolBarseparator">|</font>
            <a href="#" id="btnApp" tabindex="2"><img class="toolbarImg" src="../../../images/edit_prev_emp.png"  onclick="updateOtTran('updateOtTran','otAppAjaxResult.php','otAppCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved OT Application" ></a>
            <?
			}
			?>
            <FONT class="ToolBarseparator">|</font>
            <a href="#" id="btnDel" tabindex="3"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete OT Application" onclick="delOtAppDtl('delOtAppDtl','otAppAjaxResult.php','otAppCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
			<FONT class="ToolBarseparator">|</font>
			<?=$OtAppObj->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');?>

		 </td>
		  
        <tr> 
		  <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
		  <td width="9%" class="gridDtlLbl" align="center">EMPLOYEE NO</td>
		  <td width="27%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
          <td width="9%" class="gridDtlLbl" align="center">DATE OF OVERTIME</td>
          <td width="26%" class="gridDtlLbl" align="center">REASON</td>
          <td width="7%" class="gridDtlLbl" align="center">OT IN</td>
          <td width="7%" class="gridDtlLbl" align="center">OT OUT</td>
		  <td width="7%" class="gridDtlLbl" align="center">CROSS DATE</td>
          <td width="6%" class="gridDtlLbl" align="center"> STATUS</td>
         
        </tr>
		
		<?
				if(@$OtAppObj->getRecCount($resGetOtAppDtl) > 0){

					$i=0;
					$ctr=1;
					foreach (@$arrGetOtAppDtl as $otAppDtlVal){

						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
						$f_color = ($otAppDtlVal["otStat"]=='A'?"#CC3300":"");
		?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>> 
          		<td class="gridDtlVal" align="center">
                	<?php
						if(($otAppDtlVal["otStat"]=='H') || (($otAppDtlVal["userApproved"]==$_SESSION['employee_number']) && ($otAppDtlVal["otStat"]=='A')))
						{
					?>
							<input class="inputs" type="checkbox" name="chkseq[]" value="<?=$otAppDtlVal['seqNo']?>" id="chkseq[]" />
                    <?php
						}
					?>
				</td>           
		  		<td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$otAppDtlVal['empNo']?></td>
          		<td class="gridDtlVal"> <font color="<?=$f_color?>">
           		
            		<?=$otAppDtlVal['empLastName'].", ".$otAppDtlVal['empFirstName']." "?>
		   		</td>
		   		<td class="gridDtlVal" align = "center"><font color="<?=$f_color?>"><? 
		  			echo date("Y-m-d", strtotime($otAppDtlVal['otDate']));
		 		?></td>			
          		<td class="gridDtlVal" align="left"><font color="<?=$f_color?>">
	      <?
				if(is_numeric($otAppDtlVal['otReason'])){
					$OTRes=$OtAppObj->getTblData("tblTK_Reasons "," and stat='A' and reason_id='".$otAppDtlVal['otReason']."'"," order by reason","sqlAssoc");
					echo $OTRes['reason'];	
				}
				else{
					echo strtoupper($otAppDtlVal['otReason']);	
				}
				?></td>
          		<td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$otAppDtlVal['otIn']?></td>
          		<td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$otAppDtlVal['otOut']?></td>
		  		<td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$otAppDtlVal['crossTag']=="Y"?"Yes":""?></td>
          		<td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
				<?
					if ($otAppDtlVal['otStat'] == 'H'){
						echo ($otAppDtlVal['otStat'] =="H"? "Held":"Approved");
					}else if($otAppDtlVal['otStat'] == 'A'){
						echo ($otAppDtlVal['otStat'] =="A"? "Approved":"Held");
					}else{
						echo ($otAppDtlVal['otStat'] =="Posted");
					}
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
				$pager->_viewPagerButton('otAppAjaxResult.php','otAppCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo'].'&brnCd='.$_GET['brnCd']);
			?>
          </td>
        </tr>
    
		</TABLE>
	
</TABLE>
<font style="<?=$otExempted ? '' : 'display: none;'?>" color="red"><strong>OT Exempted</strong></font>

<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<?$OtAppObj->disConnect();?>