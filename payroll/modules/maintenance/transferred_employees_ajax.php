<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

$qryTransEmp = "Select * from PAYROLL_COMPANY..tblTransferredEmployees where old_compCode='".$sessionVars['compCode']."' and status='T'";
$resIntMaxRec = $common->execQry($qryTransEmp);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],'0');

$qryEmpList = "Select  seqNo, empNo, empLastName, empFirstName, empMidName, company_old, company_new, status, new_compCode 
		from PAYROLL_COMPANY..tblTransferredEmployees 
		where old_compCode='{$sessionVars['compCode']}' 
		AND status='T'		
		ORDER BY empLastName Limit $intOffset,$intLimit"; 
$sqlEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($sqlEmpList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Transferred Employees</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar"></td>
					<tr>
						<td width="2%" class="gridDtlLbl" align="center">#</td>
						<td width="12%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="28%" class="gridDtlLbl" align="center">NAME</td>
						<td width="29%" class="gridDtlLbl" align="center">OLD COMPANY</td>
						<td width="29%" class="gridDtlLbl" align="center">NEW COMPANY</td>
					</tr>
					<? 
					if($common->getRecCount($sqlEmpList) > 0){
						$i=0;
						foreach ($arrEmpList as $empListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt">
						  <?=$empListVal['empNo']?>
						</font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("Ñ","&Ntilde;",htmlentities($empListVal['empLastName']). ", " . htmlentities($empListVal['empFirstName']) ." ". substr($empListVal['empMidName'],0,1) . ".");?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt">
						  <?=$empListVal['company_old']?>
						</font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['company_new']?></font></td>
					</tr>
					<tr id="trPrevEmpCont<?=$empListVal['empNo']?>" style="display:none;">
						<td colspan="8" >
							
						</td>
					</tr>
					<?
						}
					}
					else{
					?>
					<tr>
						<td colspan="8" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?}?>
					<tr>
						<td colspan="8" align="center">
							<? $pager->_viewPagerButton("transferred_employees_ajax.php",'empMastCont',$intOffset,'0','','','','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
</HTML>

<?$common->disConnect();?>
