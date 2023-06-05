<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("lastPay.obj.php");

$lastPayObj = new lastPayObj($_SESSION,$_GET);

$lastPayObj->validateSessions('','MODULES');
$sessionVars = $lastPayObj->getSeesionVars();
switch($_GET['code']) {
	case "delete":
		if ($lastPayObj->DelEmp($_GET['empNo']))
			echo "alert('Employee Sucessfully deleted');";
		else
			echo "alert('Employee Deletion failed');";
		
		exit();	
	break;
}
?>
<HTML>
<HEAD>
<TITLE><?=SYS_TITLE?></TITLE>
		        
	    <style type="text/css">
<!--
.style1 {font-family: verdana}
.style3 {
	font-family: verdana;
	font-size: 11px;
	color: #0033FF;
}
-->
        </style>
</HEAD>
	<BODY>
		<FORM name='frmAccessRights' id="frmAccessRights" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td colspan="4" class="parentGridHdr">
						&nbsp;<img src="../../../images/grid.png">&nbsp;Resigned Employees (
						<?
						$arrPd = $lastPayObj->currPayPd();
						echo date('m/d/Y',strtotime($arrPd['pdFrmDate'])) . " - " . date('m/d/Y',strtotime($arrPd['pdToDate']));?>)					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
							<tr>
							  <td height="25" colspan="4" align="center" ><div align="left"><span align="left" onClick="PopUp('employee_list.php','ADD RESIGNED EMPLOYEE','<?=$dedListVal['recNo']?>','resigned_employee_list.php','TSCont',0,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" style="cursor:pointer;"><img border="0" class="anchor" src="../../../images/add.gif" ><span class="style3">Add Employee</span></span></div></td>
						  </tr>
							<tr>
								<td class="gridDtlLbl" align="center">#</td>
							    <td height="25" align="center" class="gridDtlLbl"><div align="left" class="style1">
							      <div align="center">Emp No.</div>
						      </div></td>
						      <td class="gridDtlLbl" align="center"><div align="left" class="style1">
						        <div align="center">Employee Name</div>
						      </div></td>
							  <td class="gridDtlLbl" align="center">Action</td>
							</tr>
							<?
								$j=1;
								$a=0;
								foreach ($lastPayObj->ResignedEmpList() as $empVal){
									
								$bgcolor = ($j%2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
							?>
                            
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal" width="17" align="center"><?=$j?></td>
						  <td width="72" height="25" class="gridDtlVal" ><div align="center"><font class="gridDtlLblTxt">
						    <?=$empVal['empNo']?>
						    </FONT></div></td>
							  <td width="866" class="gridDtlVal" ><font class="gridDtlLblTxt"><?=$empVal['empLastName'] . ", " . $empVal['empFirstName'] . " " . $empVal['empMidName']?></FONT></td>
							  <td width="100" class="gridDtlVal" ><div align="center"><img onClick="DeleteRec('resigned_employee_list_ajax.php?code=delete&empNo=<?=$empVal['empNo']?>','resigned_employee_list_ajax.php','Employee');" title="Delete" style="cursor:pointer;" src="../../../images/application_form_delete.png" width="16" height="16"> &nbsp;<img title="Unused Leaves" style="cursor:pointer;" onClick="PopUp('Unused_leaves_act.php','Unused Leaves','<?=$empVal['empNo']?>','resigned_employee_list.php','TSCont',0,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" src="../../../images/allowance_list.png" width="16" height="16"></div></td>
							</tr>
							<?
								$a++;
								$j++;
								}
							?>
					  </TABLE>
					  </td>
				</tr>
			</TABLE>
			
	    <INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$_GET['transType']?>">
			<INPUT type="hidden" name="moduleCount" id="moduleCount" value="<?=$i?>">
			<INPUT type="hidden" name="hdnChildModuleCnt" id="hdnChildModuleCnt<?=$i?>" value="<?=$j?>">
			<INPUT type="hidden" name="pdNumber" id="pdNumber" value="<?=$arrPd['pdNumber']?>">
			<INPUT type="hidden" name="pdYear" id="pdYear" value="<?=$arrPd['pdYear']?>">
			<?$lastPayObj->disConnect();?>
	    </FORM>
	</BODY>
</HTML>
