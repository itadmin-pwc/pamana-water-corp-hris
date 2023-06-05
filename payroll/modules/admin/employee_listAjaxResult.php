<?
session_start();
//include("../../../includes/userErrorHandler.php");
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



$srchType = 0;
$arrBrnch = $common->makeArr($common->getBrnchArt($_SESSION["company_code"]),'brnCode','brnDesc','All');
$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			   WHERE compCode = '{$sessionVars['compCode']}'
			   AND empStat NOT IN('RS','IN','TR')
			 	";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND empNo LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND empLastName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND empFirstName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
			
			if ($_GET['brnCd']!=0) 
			{
				$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }
		
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT  *
		FROM tblEmpMast
		WHERE compCode='{$_SESSION['company_code']}' AND empStat NOT IN('RS','IN','TR') ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			
			if ($_GET['brnCd']!=0) 
			{
				$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }

$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;
$qryEmpList .=	"ORDER BY empLastName limit $intOffset,$intLimit";

$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);




?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Module Access Rights / Admin
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl">
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="4" class="gridToolbar">
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
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="70%" class="gridDtlLbl" align="center">NAME</td>
						<td class="gridDtlLbl" align="center">ACTION</td>
					</tr>
					<?
					if($common->getRecCount($resEmpList) > 0){
						$chkUsrExists = 0;
						$i=0;
						foreach ($arrEmpList as $empListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("Ñ","&Ntilde;",$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName'])?></font></td>
						<td class="gridDtlVal" align="center">
                        	<table border="0" width="70%">
                            	<tr>
									
                                    <td width="30%" align="center"><a href="#" onclick="createUserAccount('<?php echo $empListVal['empNo']; ?>','<?=$empListVal['compCode']?>')"><img class="toolbarImg" src="../../../images/application_add.png" title="Create User Account"></a></td>
                                	<?php
                                    //Check if the Employee Already Exists in the User Table
                                    $rsUsrExists = $moduleAccRights->chkUser($empListVal['compCode'],$empListVal['empNo']);
									$chkUsrExists = $moduleAccRights->getRecCount($rsUsrExists);
									
										if($chkUsrExists==1)
										{
                                    ?>
                                    	<td width="30%" align="center"><a href="#" onclick="editUserAccount('Edit','<?php echo $empListVal['empNo']; ?>','<?=$empListVal['compCode']?>')"><img class="toolbarImg" src="../../../images/allowance_list.png" title="Edit User Access"></a></td>
                                		<td width="30%" align="center"><a href="#" onclick="location.href='edit_access_rights.php?transType=edit&empNo=<?=$empListVal['empNo']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Menu Access"></a></td>
                                    <?php 
                                        }else{ 
                                    ?>
                                    	<td width="30%"></td>
                                        <td width="30%"></td>
                                    <?php
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
						<td colspan="4" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?}?>
					<tr>
						<td colspan="4" align="center" class="childGridFooter">
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