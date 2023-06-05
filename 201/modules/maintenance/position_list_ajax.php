<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("position.obj.php");

$posObj = new positionObj();
$pager = new AjaxPager(15,'../../../images/');

$arrPosCode = explode("-",$_GET['posCode']);

$qryIntMaxRec = "SELECT posCode FROM tblPosition 
				WHERE (compCode = '{$_SESSION['company_code']}')";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND posCode LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND posDesc LIKE '{$_GET['txtSrch']}%' ";
        	}
        }

$resIntMaxRec = $posObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryPosList = "SELECT compCode,rank,level,posCode,posDesc,Active,divCode,deptCode,sectCode FROM tblPosition
		        WHERE compCode = '{$_SESSION['company_code']}' "; 
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryPosList .= "AND posCode = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryPosList .= "AND posDesc LIKE '{$_GET['txtSrch']}%' ";
        	}
        }
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;		
$qryPosList .=	"ORDER BY posDesc limit $intOffset,$intLimit";
$resPosList = $posObj->execQry($qryPosList);
$arrPosList = $posObj->getArrRes($resPosList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;POSITION</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					  <td colspan="9" class="gridToolbar">
						<?php if($_SESSION['user_level']!=3){ ?>
                        <a href="#"  class="anchor" onclick="maintPos('ADD','','position_list_ajax.php','posMasterCont','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch')">
                        <img class="anchor" src="../../../images/add.gif">Add Position</a> 
                        <FONT class="ToolBarseparator">|</font>
                        <?php } ?>
                        </font>
						<!--
                        <a href="#" onclick="location.href='view_edit_employee.php?transType=add'" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Employee<a> <FONT class="ToolBarseparator">|</font>
						-->
						Search
						<INPUT name="txtSrch" type="text" class="inputs" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" size="20">In<?=$posObj->DropDownMenu(array("","Code","Description"),'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('position_list_ajax.php','posMasterCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
					</td>
					<tr>
						<td width="2%" class="gridDtlLbl" align="center">#</td>
						<td width="5%" class="gridDtlLbl" align="center">CODE</td>
						<td width="23%" class="gridDtlLbl" align="center">POSITION</td>
						<td width="23%" class="gridDtlLbl" align="center">DIVISION</td>
						<td width="23%" class="gridDtlLbl" align="center">DEPARTMENT</td>
						<td width="18%" class="gridDtlLbl" align="center">SECTION</td>
						<td width="6%" colspan="42" align="center" class="gridDtlLbl">ACTION</td>
					</tr>
					<?
						if($posObj->getRecCount($resPosList)>0){
						$i=0;
						foreach ($arrPosList as $posListVal=>$posval){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$posval['posCode']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$posval['posDesc']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt">
           				<?
						$divQry=$posObj->getQryDept(" WHERE tblDepartment.divCode = '{$posval['divCode']}' AND tblDepartment.deptLevel='1'");
						if($posObj->getRecCount($divQry)>0){
							$rowDiv=$posObj->getArrRes($divQry);
							foreach($rowDiv as $divtListVal=>$divval){
								echo $divval['deptDesc'];
								}
						}
						?>

                        </font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt">
                        <?
						$deptQry=$posObj->getQryDept(" WHERE tblDepartment.divCode = '{$posval['divCode']}' AND tblDepartment.deptCode = '{$posval['deptCode']}' AND tblDepartment.deptLevel='2'");
						if($posObj->getRecCount($deptQry)>0){
							$rowDept=$posObj->getArrRes($deptQry);
							foreach($rowDept as $deptListVal=>$deptval){
								echo $deptval['deptDesc'];
								}
						}
						?>
						</font></td>
						<td class="gridDtlVal" align="left">
							<font class="gridDtlLblTxt">
						<?
						$sectQry=$posObj->getQryDept("  WHERE tblDepartment.divCode = '{$posval['divCode']}' AND tblDepartment.deptCode = '{$posval['deptCode']}' AND tblDepartment.sectCode = '{$posval['sectCode']}' AND tblDepartment.deptLevel='3'");
						if($posObj->getRecCount($sectQry)>0){
							$rowSect=$posObj->getArrRes($sectQry);
							foreach($rowSect as $sectListVal=>$sectval){
								echo $sectval['deptDesc'];
								}
						}
						?>
							</font>
						</td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<a href="#" onclick="maintPos('EDIT','<?=$posval['posCode']?>','position_list_ajax.php','posMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Position"></a>
							</font>
						</td>
					</tr>
					<?
						}
					}
					?>
					<tr>
						<td colspan="9" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("position_list_ajax.php",'posMasterCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$posObj->disConnect();?>