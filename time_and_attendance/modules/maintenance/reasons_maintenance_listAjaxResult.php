<?
/*
	Created By 	:	Arong, Genarra Jo-Ann S.
	Date Created:	07/23/2010 Friday
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
$pager = new AjaxPager(10,'../../../images/');

$sessionVars = $common->getSeesionVars();

$qryIntMaxRec = "Select * from tblTK_Reasons 
				where compCode='".$_SESSION["company_code"]."'";
				if($_GET['isSearch'] == 1){
					if($_GET['srchType'] == 0){
						$qryIntMaxRec .= "and reason_id LIKE '".trim($_GET['txtSrch'])."%' ";
					}
					
					if($_GET['srchType'] == 1){
						$qryIntMaxRec .= "AND reason LIKE '".trim($_GET['txtSrch'])."%' ";
					}
        		}
$qryIntMaxRec.=" order by reason_id";

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);


$qryReasonList = "Select * from tblTK_Reasons 
					where compCode='".$_SESSION["company_code"]."'";
					 if($_GET['isSearch'] == 1)
					{
						if($_GET['srchType'] == 0){
							$qryReasonList .= "and reason_id LIKE '".trim($_GET['txtSrch'])."%' ";
						}
						
						if($_GET['srchType'] == 1){
							$qryReasonList .= "AND reason LIKE '".trim($_GET['txtSrch'])."%' ";
						}
						
        			}
$qryReasonList .=	" order by reason_id limit $intOffset,$intLimit";	

$resReasons = $common->execQry($qryReasonList);
$arReasons = $common->getArrRes($resReasons);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Reasons 
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="9" class="gridToolbar">
						
                        <?php if($_SESSION['user_level']==1){ ?>
                        	<a href="#"  class="anchor" onclick="maintShiftCode('Add','','reasons_maintenance_pop.php','reasons',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" ><img class="anchor" src="../../../images/add.gif">Add Reason </a>|
                        	<FONT class="ToolBarseparator"></font>
                        <?php } ?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">In<?=$common->DropDownMenu(array('REASON CODE','REASON DESC.'),'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('reasons_maintenance_listAjaxResult.php','reasons','Search',0,1,'txtSrch','cmbSrch','&empNo=<?=$_GET['empNo']?>','../../../images/')">
						
					</td>
					<tr>
						<td class="gridDtlLbl" align="center" width="5%">#</td>
						<td class="gridDtlLbl" align="center" width="15%">REASON CODE</td>
                        <td class="gridDtlLbl" align="center" width="54%">REASON DESCRIPTION</td>
                        <td class="gridDtlLbl" align="center" width="16%">STATUS</td>
                        <td width="10%" align="center" class="gridDtlLbl">&nbsp;</td>
					</tr>
					
                    <?php
						if($common->getRecCount($resReasons) > 0)
						{
							$i=0;
							$ctr=1;
								
							foreach ($arReasons as $arReasonsVal)
							{
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
                   
                                <tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                                    <td class="gridDtlVal" align="center"><?=$ctr?></td>
                                    <td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?=$arReasonsVal['reason_id']?></font></td>
                                    <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$arReasonsVal["reason"]?></font></td>
                                   
                                    <td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?php echo ($arReasonsVal["stat"]=='A'?"ACTIVE":($arReasonsVal["stat"]=='D'?"DELETED":"HELD")); ?></font></td>
                                    <td align="center">
                                    	<?php if($arReasonsVal["stat"]!='D'){?>
                                    		<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_edit.png" title="View/Edit Reason" onclick="maintShiftCode('Edit','<?=$arReasonsVal['reason_id']?>','reasons_maintenance_pop.php','reasons',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>                                    
                                      		<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Reason" onclick="delShiftCode('Delete','<?=$arReasonsVal['reason_id']?>','reasons_maintenance_pop.php','reasons',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                                    	<?php }else{ ?>
                                        	<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/edit_prev_emp.png" title="Set to Active" onclick="setReasonActive('setReasonActive','<?=$arReasonsVal['reason_id']?>','violation_app_type_maintenance_pop.php','vioAppType',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                                    	<?php } ?>
                                    </td>
                                
                                </tr>
                    <?php
								$i++;
								$ctr++;
							}
						}
					?>
					<tr>
						<td colspan="9" align="center" class="childGridFooter">
                        
							<? $pager->_viewPagerButton('reasons_maintenance_listAjaxResult.php','reasons',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$_GET['empNo']);?>
						</td>
					</tr>
				</TABLE>
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>

