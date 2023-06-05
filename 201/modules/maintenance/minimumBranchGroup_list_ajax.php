<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("minimumGroup.obj.php");

$deptObj = new deptObj($_GET,$_SESSION);
$pager = new AjaxPager(15,'../../../images/');

$qryIntMaxRec = "SELECT dbo.tblBranchMinimumGroup.branchMinimumGroupID, dbo.tblBranchMinimumGroup.minGroupID, 
				dbo.tblBranchMinimumGroup.brnCode, tblBranch.brnDesc, dbo.tblMinGroup.minGroupName, 
				dbo.tblMinGroup.compCode, dbo.tblMinGroup.stat
				FROM dbo.tblBranchMinimumGroup 
				INNER JOIN tblBranch ON dbo.tblBranchMinimumGroup.brnCode = tblBranch.brnCode 
				INNER JOIN dbo.tblMinGroup ON dbo.tblBranchMinimumGroup.minGroupID = dbo.tblMinGroup.minGroupID
				WHERE (dbo.tblMinGroup.compCode = '{$_SESSION['company_code']}')";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND brnCode LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND brnDesc LIKE '{$_GET['txtSrch']}%' ";
        	}
        }

$resIntMaxRec = $deptObj->execQryI($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryDivList = "SELECT  dbo.tblBranchMinimumGroup.branchMinimumGroupID, dbo.tblBranchMinimumGroup.minGroupID, 
				dbo.tblBranchMinimumGroup.brnCode, tblBranch.brnDesc, dbo.tblMinGroup.minGroupName, 
				dbo.tblMinGroup.compCode, dbo.tblMinGroup.stat
				FROM dbo.tblBranchMinimumGroup 
				INNER JOIN tblBranch ON dbo.tblBranchMinimumGroup.brnCode = tblBranch.brnCode 
				INNER JOIN dbo.tblMinGroup ON dbo.tblBranchMinimumGroup.minGroupID = dbo.tblMinGroup.minGroupID
		        WHERE dbo.tblMinGroup.compCode = '{$_SESSION['company_code']}"; 

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryDivList .= "AND brnCode LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryDivList .= "AND brnDesc LIKE '{$_GET['txtSrch']}%' ";
        	}
        }
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;		
		
$qryDivList .=	"ORDER BY minGroupName limit $intOffset,$intLimit";

$resDivList = $deptObj->execQry($qryDivList);
$arrDivList = $deptObj->getArrRes($resDivList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;MINIMUM GROUP OF BRANCHES</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="7" class="gridToolbar">
                    	<?php if($_SESSION['user_level']==1){ ?>
						<a href="#"  class="anchor" onclick="maintDiv('ADD','','minimumBranchGroup_list_ajax.php','divMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
                        <img class="anchor" src="../../../images/add.gif">Add Branch</a> 
                        <FONT class="ToolBarseparator">|</font>
						<?php } ?>
						<?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$deptObj->DropDownMenu(array("Code","Description"),'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('minimumBranchGroup_list_ajax.php','divMasterCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
					</td>
					<tr>
						<td width="4%" class="gridDtlLbl" align="center">#</td>
						<td width="31%" class="gridDtlLbl" align="center">GROUP NAME</td>
						<td width="39%" class="gridDtlLbl" align="center">BRANCH</td>
						<td width="13%" class="gridDtlLbl" align="center">STATUS</td>
						<td width="13%" colspan="42" align="center" class="gridDtlLbl">ACTION</td>
					</tr>
					<?
					if($deptObj->getRecCount($resDivList) > 0){
						$i=0;
						foreach ($arrDivList as $divListVal){
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?
						if($gname!=$divListVal['minGroupName']){
							echo $divListVal['minGroupName'];	
						}
						else{
							echo "";	
						}
						$gname=$divListVal['minGroupName'];
						?></font></td>
						<td class="gridDtlVal" align="LEFT"><font class="gridDtlLblTxt">
						  <?=$divListVal['brnDesc']?>
						</font></td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<?=($divListVal['stat']=='A') ? "Active" : 'Held'?>
							</font>
						</td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<a href="#" onclick="maintDiv('EDIT','<?=$divListVal['branchMinimumGroupID']?>','minimumBranchGroup_list_ajax.php','divMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Branch"></a>
							</font>
						</td>
					</tr>
					<?
                    	}
					}
					?>
					<tr>
						<td colspan="7" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("minimumBranchGroup_list_ajax.php",'divMasterCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$deptObj->disConnect();?>