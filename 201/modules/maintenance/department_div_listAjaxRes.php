<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("department.obj.php");

$deptObj = new deptObj($_GET,$_SESSION);
$pager = new AjaxPager(15,'../../../images/');

$qryIntMaxRec = "SELECT * FROM tblDepartment 
				WHERE (compCode = '{$_SESSION['company_code']}') 
				AND (deptLevel = '1') 
				";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND divCode LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND deptDesc LIKE '{$_GET['txtSrch']}%' ";
        	}
        }

$resIntMaxRec = $deptObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryDivList = "SELECT  * FROM tblDepartment
		        WHERE compCode = '{$_SESSION['company_code']}'
		        AND (deptLevel = '1') 
				"; 

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryDivList .= "AND divCode LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryDivList .= "AND deptDesc LIKE '{$_GET['txtSrch']}%' ";
        	}
        }
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;		
$qryDivList .=	"ORDER BY deptDesc limit $intOffset,$intLimit";

$resDivList = $deptObj->execQry($qryDivList);
$arrDivList = $deptObj->getArrRes($resDivList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;DIVISION</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar">
                    	<?php if($_SESSION['user_level']==1){ ?>
						<a href="#"  class="anchor" onclick="maintDiv('ADD','','department_div_listAjaxRes.php','divMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
                        <img class="anchor" src="../../../images/add.gif">Add Division</a> 
                        <FONT class="ToolBarseparator">|</font>
						<?php } ?>
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
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$deptObj->DropDownMenu(array("Code","Description"),'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('department_div_listAjaxRes.php','divMasterCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
					</td>
					<tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="10%" class="gridDtlLbl" align="center">CODE</td>
						<td width="60%" class="gridDtlLbl" align="center">DESCRIPTION</td>
						<td width="22%" class="gridDtlLbl" align="center">GL MINOR CODE / DESCRIPTION</td>
						<td width="22%" class="gridDtlLbl" align="center">STATUS</td>
						<td class="gridDtlLbl" align="center" colspan="42">ACTION</td>
					</tr>
					<?
					if($deptObj->getRecCount($resDivList) > 0){
						$i=0;
						foreach ($arrDivList as $divListVal){
							
						$rowGLInfo = $deptObj->getGLInfo('tblGLMinorAcct',$divListVal['deptGlCode']);
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$divListVal['divCode']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$divListVal['deptDesc']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$divListVal['deptGlCode']." - ".$rowGLInfo['acctDesc']?></font></td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<?=($divListVal['deptStat']=='A') ? "Active" : 'Deleted'?>
							</font>
						</td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<a href="#" onclick="maintDiv('EDIT','<?=$divListVal['divCode']?>','department_div_listAjaxRes.php','divMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Division"></a>
							</font>
						</td>
					</tr>
					<?
                    	}
					}
					?>
					<tr>
						<td colspan="8" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("department_div_listAjaxRes.php",'divMasterCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$deptObj->disConnect();?>