<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("branch.obj.php");

$brnchObj = new branchObj($_GET,$_SESSION);
$pager = new AjaxPager(18,'../../../images/');

$qryIntMaxRec = "SELECT brnCode FROM tblBranch 
				WHERE (compCode = '{$_SESSION['company_code']}') ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND brnCode = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND brnDesc LIKE '{$_GET['txtSrch']}%' ";
        	}

        }

$resIntMaxRec = $brnchObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryBrnList = "SELECT  brnCode,brnDesc,compglCode,glCodeStr,glCodeHO,brnStat,minWage FROM tblBranch
		        WHERE compCode = '{$_SESSION['company_code']}'";
				
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryBrnList .= "AND brnCode = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryBrnList .= "AND brnDesc LIKE '{$_GET['txtSrch']}%' ";
        	}

        }
		
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;		
$qryBrnList .=	"ORDER BY brnDesc limit $intOffset,$intLimit";
$resBrnList = $brnchObj->execQry($qryBrnList);
$arrBrnList = $brnchObj->getArrRes($resBrnList);


?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="5" class="parentGridHdr">
				&nbsp;&nbsp;BRANCH</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="9" class="gridToolbar">
                    	 <?php if($_SESSION['user_level']==1){ ?>
						<a href="#"  class="anchor" onclick="maintBranch('ADD','','branch_listAjaxRes.php','branchMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
                        <img class="anchor" src="../../../images/add.gif">Add Branch</a> 
                        <FONT class="ToolBarseparator">|</font>
						<?php  } ?>
                        <!--
                        <a href="#" onclick="location.href='view_edit_employee.php?transType=add'" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Employee<a> <FONT class="ToolBarseparator">|</font>
						-->
						<?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
						?>
						Search<INPUT style="margin-bottom: 3px;" type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$brnchObj->DropDownMenu(array("","Code","Description"),'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT style="margin-bottom: 3px;"" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('branch_listAjaxRes.php','branchMasterCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
					</td>
					<tr>
						<td width="3%" class="gridDtlLbl" align="center">#</td>
						<td width="10%" class="gridDtlLbl" align="center">CODE</td>
						<td width="40%" class="gridDtlLbl" align="center">DESCRIPTION</td>
						<td width="10%" class="gridDtlLbl" align="center">GL CODE STORE / DESCRIPTION</td>
                        <td width="10%" class="gridDtlLbl" align="center">MIN. WAGE</td>
						<td width="15%" class="gridDtlLbl" align="center">STATUS</td>
						<td width="10%"class="gridDtlLbl" align="center" colspan="42">ACTION</td>
					</tr>
					<?
					if($brnchObj->getRecCount($resBrnList) > 0){
						$i=0;
						foreach ($arrBrnList as $brnLsitVal){
						$arrGLInfo = $brnchObj->getGLInfo('tblGLStoreAcct',$brnLsitVal['glCode']);	
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$brnLsitVal['brnCode']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=htmlentities($brnLsitVal['brnDesc']);?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$brnLsitVal['glCodeStr']." - ".$arrGLInfo['acctDesc']; ?></font></td>
                        <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$brnLsitVal['minWage']?></font></td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<?=($brnLsitVal['brnStat']=='A') ? "Active" : 'Deleted'?>
							</font>
						</td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<a href="#" onclick="maintBranch('EDIT','<?=$brnLsitVal['brnCode']?>','branch_listAjaxRes.php','branchMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Branch"></a>
							</font>
						</td>
					</tr>
					<?
                    	}
					}
					?>
					<tr>
						<td colspan="8" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("branch_listAjaxRes.php",'branchMasterCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$brnchObj->disConnect();?>