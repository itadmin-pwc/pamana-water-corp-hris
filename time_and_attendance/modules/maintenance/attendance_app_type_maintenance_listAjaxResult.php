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

$qryIntMaxRec = "Select * from tblTK_AppTypes 
				where compCode='".$_SESSION["company_code"]."'";
				if($_GET['isSearch'] == 1){
					if($_GET['srchType'] == 0){
						$qryIntMaxRec .= "and tsAppTypeCd LIKE '".trim($_GET['txtSrch'])."%' ";
					}
					
					if($_GET['srchType'] == 1){
						$qryIntMaxRec .= "AND appTypeDesc LIKE '".trim($_GET['txtSrch'])."%' ";
					}
					
					if($_GET['srchType'] == 2){
						$qryIntMaxRec .= "AND appStatus LIKE '".trim($_GET['txtSrch'])."%' ";
					}
        		}
$qryIntMaxRec.=" order by tsAppTypeCd";

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);


$qryAttendCodeList = "Select * from tblTK_AppTypes 
					where compCode='".$_SESSION["company_code"]."'";
					 if($_GET['isSearch'] == 1)
					{
						if($_GET['srchType'] == 0){
							$qryAttendCodeList .= "and tsAppTypeCd LIKE '".trim($_GET['txtSrch'])."%' ";
						}
						
						if($_GET['srchType'] == 1){
							$qryAttendCodeList .= "AND appTypeDesc LIKE '".trim($_GET['txtSrch'])."%' ";
						}
						
						if($_GET['srchType'] == 2){
							$qryAttendCodeList .= "AND appStatus LIKE '".trim($_GET['txtSrch'])."%' ";
						}
        			}
$qryAttendCodeList .=	" order by tsAppTypeCd limit $intOffset,$intLimit";	

$resAttendCodeList = $common->execQry($qryAttendCodeList);
$arrAttendCodeList = $common->getArrRes($resAttendCodeList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Attendance Application Type 
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="10" class="gridToolbar">
						
                         <?php if($_SESSION['user_level']==1){ ?>
                        	<a href="#"  class="anchor" onclick="maintShiftCode('Add','','attendance_app_type_maintenance_pop.php','attAppType',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" ><img class="anchor" src="../../../images/add.gif">Add Application Code </a>|
                        	<FONT class="ToolBarseparator"></font>
                        <? } ?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">In<?=$common->DropDownMenu(array('APP. CODE','ATTEND. APP. DESC.','STATUS'),'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('attendance_app_type_maintenance_listAjaxResult.php','attAppType','Search',0,1,'txtSrch','cmbSrch','&empNo=<?=$_GET['empNo']?>','../../../images/')">
						
					</td>
					<tr>
						<td class="gridDtlLbl" align="center" width="1%">#</td>
						<td class="gridDtlLbl" align="center" width="15%">APP. CODE</td>
                        <td class="gridDtlLbl" align="center" width="30%">ATTEND. APPLICATION DESC.</td>
                        <td class="gridDtlLbl" align="center" width="15%">DEDUCTION TAG</td>
                        <td class="gridDtlLbl" align="center" width="15%">LEAVE TAG</td>
                        <td class="gridDtlLbl" align="center" width="10%">STATUS</td>
                        <td class="gridDtlLbl" align="center">ACTION</td>
					</tr>
					
                    <?php
						if($common->getRecCount($resAttendCodeList) > 0)
						{
							$i=0;
							$ctr=1;
								
							foreach ($arrAttendCodeList as $arrAttendCodeListVal)
							{
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
                   
                                <tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                                    <td class="gridDtlVal" align="center"><?=$ctr?></td>
                                    <td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?=$arrAttendCodeListVal['tsAppTypeCd']?></font></td>
                                    <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$arrAttendCodeListVal["appTypeDesc"]?></font></td>
                                    <td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=($arrAttendCodeListVal["deductionTag"]=='Y'?"Yes":"No")?></font></td>
                                    <td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=($arrAttendCodeListVal["leaveTag"]=='Y'?"Yes":"No")?></font></td>
                                    
                                    <td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?php echo ($arrAttendCodeListVal["appStatus"]=='A'?"ACTIVE":($arrAttendCodeListVal["appStatus"]=='D'?"DELETED":"HELD")); ?></font></td>
                                    <td align="center">
                                    	<?php if($arrAttendCodeListVal["appStatus"]!='D'){?>
                                    		<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_edit.png" title="View/Edit Atten. App. Code Detail" onclick="maintShiftCode('Edit','<?=$arrAttendCodeListVal['tsAppTypeCd']?>','attendance_app_type_maintenance_pop.php','attAppType',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>                                    
                                      		<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Atten. App. Code Detail" onclick="delShiftCode('Delete','<?=$arrAttendCodeListVal['tsAppTypeCd']?>','attendance_app_type_maintenance_pop.php','attAppType',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                                    	<?php }else{ ?>
                                        	<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/edit_prev_emp.png" title="Set to Active" onclick="setActiveAttdnAppCode('setAttdnAppActive','<?=$arrAttendCodeListVal['tsAppTypeCd']?>','attendance_app_type_maintenance_pop.php','attAppType',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
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
						<td colspan="10" align="center" class="childGridFooter">
                        
							<? $pager->_viewPagerButton('attendance_app_type_maintenance_listAjaxResult.php','attAppType',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$_GET['empNo']);?>
						</td>
					</tr>
				</TABLE>
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>

