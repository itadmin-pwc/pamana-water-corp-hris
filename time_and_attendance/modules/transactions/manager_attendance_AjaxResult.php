<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/pager.inc.php");
include("../../../includes/common.php");
include("transaction_obj.php");

$srchType=0;
$transactionObj = new transactionObj();

$pager = new AjaxPager(10,'../../../images/');

//get users branch access
	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $transactionObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		$brnCodelist = " AND emp.empNo<>'".$_SESSION['employee_number']."' and emp.empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND emp.empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}

	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblTK_UserBranch tblUB, tblBranch as tblbrn
				 where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
				 and empNo='".$_SESSION['employee_number']."'
				 order by brnDesc";
	
	$resBrnches = $transactionObj->execQry($queryBrnches);
	$arrBrnches = $transactionObj->getArrRes($resBrnches);
	$arrBrnch = $transactionObj->makeArr($arrBrnches,'brnCode','brnDesc','All');


//count records
$qryIntMaxRec = "Select ma.compCode, ma.brnCode, ma.empNo, emp.empLastName, emp.empFirstName, emp.empMidName, 
					comp.compName, brn.brnDesc, pos.posDesc
				 From tblTK_ManagersAttendance ma
				 Inner Join tblEmpMast emp on ma.empNo=emp.empNo and ma.compCode=emp.compCode
				 Inner Join tblBranch brn on ma.brnCode=brn.brnCode
				 Inner Join tblCompany comp on ma.compCode= comp.compCode
				 Inner Join tblPosition pos on emp.empPosId=pos.posCode
				 WHERE ma.compCode = '{$_SESSION['company_code']}' $brnCodelist
				 ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND ma.empNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if ($_GET['brnCd']!=0){
				$qryIntMaxRec .= " AND emp.empbrnCode='".$_GET["brnCd"]."' ";
			}
			
        }
        $qryIntMaxRec .= "ORDER BY emp.empLastName ";
		
$resIntMaxRec = $transactionObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

if(empty($intOffset)){
	$intOffset = 0;
}

//display records
$qrygetMADtl = "SELECT ma.compCode, ma.brnCode, ma.empNo, emp.empLastName, emp.empFirstName, 
					emp.empMidName, comp.compName, brn.brnDesc, pos.posDesc, ma.seqNo
				From tblTK_ManagersAttendance ma
				Inner Join tblEmpMast emp on ma.empNo=emp.empNo and ma.compCode=emp.compCode
				Inner Join tblBranch brn on ma.brnCode=brn.brnCode
				Inner Join tblCompany comp on ma.compCode= comp.compCode
				Inner Join tblPosition pos on emp.empPosId=pos.posCode
				   				WHERE ma.compCode = '{$_SESSION['company_code']}' ";

	if($_GET['isSearch'] == 1){
			if($_GET['srchType'] == 1){
        		$qrygetMADtl .= "AND ma.empNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 2){
        		$qrygetMADtl .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if ($_GET['brnCd']!=0){
				$qrygetMADtl .= " AND emp.empbrnCode='".$_GET["brnCd"]."' ";
			}
	 }
	
	$qrygetMADtl .= " $brnCodelist ORDER BY emp.empLastName limit $intOffset,$intLimit";
	
	$resgetMADtl = $transactionObj->execQry($qrygetMADtl);
	$arrgetMADtl = $transactionObj->getArrRes($resgetMADtl);

?>

<TABLE border ="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		
    <td height="4" colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Manager's Attendance</td>
	</tr>
	<tr>
		
		<td colspan="6" class="gridToolbar">
			&nbsp;
			
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.png'  onclick="pager('timesheets_adjustments_AjaxResult.php','timesheetAdjustmentCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
		</td>
	
	</tr>
	<tr>
	  <td class="parentGridDtl" valign="top">
			<!--header-->					
	    <TABLE cellpadding="1" cellspacing="1" border="0" class="hdrTable" width="100%">
				<tr>
					<td class="hdrLblRow" colspan="22">
						<FONT class="hdrLbl">Employee Detail</font><input type="hidden" id="hdnProcess" name="hdnProcess" /><input type="hidden" id="hdnSeqNo" name="hdnSeqNo" />
					</td>
				</tr>
				<tr>
					<td width="172" height="20" class="hdrInputsLvl">
						<a href="#" onclick="empLookup('../../../includes/employee_lookup_tna.php')">Employee No.</a>
					</td>
					<td width="7" height="20" class="hdrInputsLvl">
						:
					</td>
					<td width="337" height="20" class="gridDtlVal">
						<INPUT tabindex="9" class="inputs" type="text" name="txtAddEmpNo" id="txtAddEmpNo" onkeydown="getEmployee(event,this.value)">
						<span class="hdrLblRow">
						<input type="hidden" id="hdnBranch" name="hdnBranch" />
						</span></td>
					<td height="20" colspan="6" class="gridDtlVal"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
					    <td width="16%"><span class="hdrInputsLvl" style="font-weight:bold">Position</span></td>
					    <td width="2%" class="hdrInputsLvl">:</td>
					    <td width="82%"><input name="txtPosition" type="text" class="inputs" id="txtPosition" size="40" readonly="readonly"></td>
				      </tr>
				    </table></td>
				</tr>
			    <tr>
			      <td height="20" class="hdrInputsLvl">Employee Name</td>
			      <td height="20" class="hdrInputsLvl">:</td>
			      <td height="20" class="gridDtlVal"><input name="txtName" type="text" class="inputs" id="txtName" size="45" readonly="readonly"></td>
			      <td height="20" colspan="6"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			        <tr>
			          <td width="16%"><span class="hdrInputsLvl" style="font-weight:bold">Rank</span></td>
			          <td width="2%" class="hdrInputsLvl">:</td>
			          <td width="82%"><span class="gridDtlVal">
			            <input name="txtRank" type="text" class="inputs" id="txtRank" size="40" readonly="readonly">
			          </span></td>
		            </tr>
		          </table></td>
	      </tr>
			    <tr>
			      <td height="20" class="hdrInputsLvl">Payroll Group</td>
			      <td height="20" class="hdrInputsLvl">:</td>
			      <td height="20" class="gridDtlVal"><input name="txtPayGrp" type="text" class="inputs" id="txtPayGrp" size="20" readonly="readonly"></td>
			      <td height="20" colspan="6" class="hdrInputsLvl"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			        <tr>
			          <td width="16%"><span class="hdrInputsLvl" style="font-weight:bold">Level</span></td>
			          <td width="2%" class="hdrInputsLvl">:</td>
			          <td width="82%"><span class="gridDtlVal">
			            <input name="txtLevel" type="text" class="inputs" id="txtLevel" size="40" readonly="readonly" />
			          </span></td>
		            </tr>
		          </table></td>
	      </tr>
			    <tr>
			      <td height="20" class="hdrInputsLvl">Payroll Category</td>
			      <td height="20" class="hdrInputsLvl">:</td>
			      <td height="20" class="gridDtlVal"><input name="txtPayCat" type="text" class="inputs" id="txtPayCat" readonly="readonly"></td>
			      <td width="85" height="20" ><span class="hdrInputsLvl" style="font-weight:bold">Action</span></td>
			      <td width="4" height="20" class="hdrInputsLvl">:</td>
			      <td width="387" height="20" colspan="4" class="gridDtlVal"><input type="button" name="btnSave" id="btnSave" value="SAVE EMPLOYEE" class="inputs" disabled="disabled" onclick="saveEmp();"/></td>
	      </tr>
          		<tr>
                  <td colspan="6" height="15"></td>
                </tr>
				</TABLE>
<TABLE width="100%" cellpadding="1" cellspacing="1" border="0" align="center">
		  <tr>
          			<td align="center" bgcolor="#CCCCCC"></td>
       			</tr>
                <tr>
				  <td colspan="4">&nbsp;</td>
				</tr>
				<tr>
				  <td class="hdrLblRow" colspan="4">
					  <FONT class="hdrLbl">Summary of Managers</font>
				</td></tr>
								
</TABLE>
	
	<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
        
<tr>
			<td colspan="7" class="gridToolbar">
						Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
            In 
            <?=$transactionObj->DropDownMenu(array('','Employee Number','Lastname','Branch'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
            <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('manager_attendance_AjaxResult.php','managersAttendanceCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['seqNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
            <FONT class="ToolBarseparator">|</font>
            <a href="#" id="deleTimesheestAdj" tabindex="3"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Employee" onclick="delTimesheetAdj('delTimesheetAdj','manager_attendance_AjaxResult.php','managersAttendanceCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
			<FONT class="ToolBarseparator">|</font>
			<?=$transactionObj->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');?>
			
		 </td>
		  
        <tr> 
		  <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
		  <td width="10%" align="center" class="gridDtlLbl">EMPLOYEE NO.</td>
		  <td width="21%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
		  <td width="23%" class="gridDtlLbl" align="center">BRANCH</td>
          <td width="24%" class="gridDtlLbl" align="center">POSITION</td>
        </tr>
		
		<?
					if(@$transactionObj->getRecCount($resgetMADtl) > 0)
					{
						$i=0;
						$ctr=1;
							
						foreach (@$arrgetMADtl as $MADtlVal)
						{
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							$f_color = ($MADtlVal["tsStat"]=='A'?"#CC3300":"");
				?>
                			<tr style="height:20px;"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                            	<td class="gridDtlVal" align="center">
                             	<input class="inputs" type="checkbox" name="chkseq[]" value="<?=$MADtlVal['seqNo']?>" id="chkseq[]" />
          						</td>
                               
                               
                                <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$MADtlVal["empNo"]?></td>
								<td class="gridDtlVal" align="left"><font color="<?=$f_color?>"><?=strtoupper($MADtlVal["empLastName"].", ".$MADtlVal["empFirstName"]." ".substr($MADtlVal["empMidName"],0,1).".")?></td>
								<td class="gridDtlVal" align="LEFT"><font color="<?=$f_color?>"><?=$MADtlVal["brnDesc"];?></td>
                                <td class="gridDtlVal" align="LEFT"><font color="<?=$f_color?>"><?=$MADtlVal["posDesc"];?></td>
                            </tr>
                <?
							$ctr++;
						}
					}
				?>
                 <tr> 
                      <td colspan="7" align="center" class="childGridFooter"> 
                        <?
                            $pager->_viewPagerButton('manager_attendance_AjaxResult.php','managersAttendanceCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['seqNo'].'&brnCd='.$_GET['brnCd']);
                        ?>
                      </td>
                </tr>
          </TABLE>
	  </td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $transactionObj->disConnect();?>
