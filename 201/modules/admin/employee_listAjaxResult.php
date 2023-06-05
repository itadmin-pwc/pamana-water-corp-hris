<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("module_access_right.obj.php");

$moduleAccRights = new moduleAccessRightsObj($_SESSION,$_GET);
$moduleAccRights->validateSessions('','MODULES');
$sessionVars = $moduleAccRights->getSeesionVars();

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');
$common->validateSessions('','MODULES');
$sessionVars = $common->getSeesionVars();

$empAccessRight=$moduleAccRights->getSqlAssoc($moduleAccRights->chkEmpUser($_SESSION['company_code'],$_SESSION['employee_number']));
if($empAccessRight['userLevel']==1){
	$userAccess="";	
}
else{
	$userAccess="and (tblUsers.userLevel IN (2,3) or userlevel is null) and tblEmpMast.empDiv='4'";	
}


$srchType = 0;
$arrBrnch = $common->makeArr($common->getBrnchArt($_SESSION["company_code"]),'brnCode','brnDesc','All');
$qryIntMaxRec = "SELECT tblEmpMast.compCode, tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, 
				tblEmpMast.empMidName, tblEmpMast.empBrnCode, tblUsers.userLevel, tblEmpMast.empStat, tblBranch.brnDesc
				FROM tblEmpMast 
				LEFT OUTER JOIN tblUsers ON tblEmpMast.empNo = tblUsers.empNo
				LEFT OUTER JOIN tblBranch ON tblEmpMast.empBrnCode=tblBranch.brnCode
			   	WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblEmpMast.empStat NOT IN('RS','TR') ".$userAccess;
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND tblEmpMast.empNo LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND tblEmpMast.empLastName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND tblEmpMast.empFirstName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
			if ($_GET['brnCd']!=0) 
			{
				$qryIntMaxRec.= " AND tblEmpMast.empbrnCode='".$_GET["brnCd"]."' ";
			}
        }
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT tblEmpMast.compCode, tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, 
				tblEmpMast.empMidName, tblEmpMast.empBrnCode, tblUsers.userLevel, tblEmpMast.empStat, tblBranch.brnDesc
				FROM tblEmpMast 
				LEFT OUTER JOIN tblUsers ON tblEmpMast.empNo = tblUsers.empNo
				LEFT OUTER JOIN tblBranch ON tblEmpMast.empBrnCode=tblBranch.brnCode
				WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}' and tblEmpMast.empStat NOT IN('RS','TR') ".$userAccess; 

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND tblEmpMast.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= "AND tblEmpMast.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= "AND tblEmpMast.empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if ($_GET['brnCd']!=0) 
			{
				$qryEmpList .= " AND tblEmpMast.empbrnCode='".$_GET["brnCd"]."' ";
			}
        }
$qryEmpList .=	"ORDER BY tblEmpMast.empLastName limit $intOffset, $intLimit";
$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);



?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Module Access Rights / Maintenance
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl">
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="5" class="gridToolbar">
						<?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu(array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME'),'cmbSrch',$srchType,'class="inputs"');?>
						<?php echo  "Branch |";?> <? echo $common->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');?>
						
                        <INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('employee_listAjaxResult.php','empList','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')">
					</td>
					<tr>
						<td width="2%" class="gridDtlLbl" align="center">#</td>
						<td width="15%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="35%" class="gridDtlLbl" align="center">NAME</td>
						<td width="34%" align="center" class="gridDtlLbl">BRANCH</td>
						<td width="14%" align="center" class="gridDtlLbl">ACTION</td>
					</tr>
					<?
					if($common->getRecCount($resEmpList) > 0){
						$i=0;
						foreach ($arrEmpList as $empListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?= htmlentities($empListVal['empLastName']). ", " . htmlentities($empListVal['empFirstName']) . " " . htmlentities($empListVal['empMidName']);?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['brnDesc'];?>
						</font></td>
						<td class="gridDtlVal" align="center">
                        	<table border="0" width="70%">
                            	<tr>
                                	<?php
                                    //Check if the Employee Already Exists in the User Table
                                    $rsUsrExists = $moduleAccRights->chkUser($empListVal['compCode'],$empListVal['empNo']);
									$chkUsrExists = $moduleAccRights->getRecCount($rsUsrExists);
									if($chkUsrExists!=1)
									{
                                    ?>
                                    <td width="30%" align="center"><a href="#" onclick="createUserAccount('<?php echo $empListVal['empNo']; ?>','<?=$empListVal['compCode']?>')"><img class="toolbarImg" src="../../../images/application_add.png" title="Create User Account"></a></td>
                                    <?
									}
									if($chkUsrExists==1)
									{
									?>
                                    	<td width="30%" align="center"><a href="#" onclick="editUserAccount('Edit Branch','<?php echo $empListVal['empNo']; ?>','<?=$empListVal['compCode']?>')"><img class="toolbarImg" src="../../../images/allowance_list.png" title="Edit Branch Access"></a></td>
                                		<td width="30%" align="center"><a href="#" onclick="editUserAccount('Edit','<?php echo $empListVal['empNo']; ?>','<?=$empListVal['compCode']?>')"><img class="toolbarImg" src="../../../images/allowance_list.png" title="Edit User Access"></a></td>
                                		<td width="30%" align="center"><a href="#" onclick="editUserAccount('Edit Level','<?php echo $empListVal['empNo']; ?>','<?=$empListVal['compCode']?>')"><img class="toolbarImg" src="../../../images/allowance_list.png" title="Edit User Access Level"></a></td>

                                		<td width="30%" align="center"><a href="#" onclick="location.href='edit_access_rights.php?transType=edit&empNo=<?=$empListVal['empNo']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Menu Access"></a></td>
                                        <td width="30%" align="center"><a href="#" onclick="editPassword('employee_change_password_change.php','Edit User Password','<?=$empListVal['empNo']?>','<?=$empListVal['compCode']?>')"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit User Password"></a></td>
                                        <td width="30%" align="center"><a href="#" onclick="location.href='employee_transfer_access_rights.php?empNo=<?=$empListVal['empNo']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_get.png" title="Create access rights to another company"></a></td>                                    <?php
									}
									?>                                    
                                </tr>
                            </table>
                     
							
                        </td>
					</tr>
					<?
						}
					}
					else {
					?>
					<tr>
						<td colspan="5" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?}?>
					<tr>
						<td colspan="5" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("employee_listAjaxResult.php","empList",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&brnCd='.$_GET["brnCd"],'');?>
						</td>
					</tr>
				</TABLE>
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>
<form name="frmTS" method="post">
<input type="hidden" name="brnCd" id="brnCd" value="<?php echo $_GET['brnCd']; ?>">
</form>