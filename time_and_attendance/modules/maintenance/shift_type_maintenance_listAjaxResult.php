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
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

$qryIntMaxRec = "Select * from tblTK_ShiftHdr 
				where compCode='".$_SESSION["company_code"]."'";
				if($_GET['isSearch'] == 1){
					if($_GET['srchType'] == 0){
						$qryIntMaxRec .= "and shiftCode LIKE '".trim($_GET['txtSrch'])."%' ";
					}
					
					if($_GET['srchType'] == 1){
						$qryIntMaxRec .= "AND shiftDesc LIKE '".trim($_GET['txtSrch'])."%' ";
					}
					
					if($_GET['srchType'] == 2){
						$qryIntMaxRec .= "AND status LIKE '".trim($_GET['txtSrch'])."%' ";
					}
        		}
$qryIntMaxRec.=" order by shiftCode";
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);


$qryShiftCodeList = "Select * from tblTK_ShiftHdr 
					where compCode='".$_SESSION["company_code"]."'";
					 if($_GET['isSearch'] == 1)
					{
						if($_GET['srchType'] == 0){
							$qryShiftCodeList .= "and shiftCode LIKE '".trim($_GET['txtSrch'])."%' ";
						}
						
						if($_GET['srchType'] == 1){
							$qryShiftCodeList .= "AND shiftDesc LIKE '%".trim($_GET['txtSrch'])."%' ";
						}
						
						if($_GET['srchType'] == 2){
							$qryShiftCodeList .= "AND status LIKE '".trim($_GET['txtSrch'])."%' ";
						}
        			}
$qryShiftCodeList .=	" order by shiftCode limit $intOffset,$intLimit";	
$resShiftCodeList = $common->execQry($qryShiftCodeList);
$arrShiftCodeList = $common->getArrRes($resShiftCodeList);

//Migrate Shift Code

//Hdr
/*$qrygetShiftForHdr = "SELECT     *
					FROM         tblShiftDtl_Hr$
					ORDER BY SHIFT_DESC, DAY_CODE";

$resgetShiftForHdr = $common->execQry($qrygetShiftForHdr);
$arrgetShiftForHdr = $common->getArrRes($resgetShiftForHdr);

$shiftCode = 01;
foreach($arrgetShiftForHdr as $arrgetShiftForHdrDtl)
{
	if($arrgetShiftForHdrDtl["SHIFT_DESC"]!=$shiftDesc)
	{
		echo "Insert into tblTK_ShiftHdr(compCode,shiftCode,shiftDesc,shiftLongDesc, status,dateAdded,addedBy) values('2', '".$shiftCode."','".$arrgetShiftForHdrDtl["SHIFT_DESC"]."' ,'".$arrgetShiftForHdrDtl["SHIFT_DESC"]."', 'A', '08/27/2010', '010002408'); <br>";
		//echo "<br>";
		$shiftCode++;
		$test = $shiftCode - 1;
	}
		
		echo "Insert into tblTK_ShiftDtl(compCode,shftCode,dayCode,shftTimeIn,shftLunchOut,shftLunchIn,shftBreakOut,shftBreakIn,shftTimeOut,crossDay,RestDayTag,
						dateAdded,addedBy) 
						  values('".$_SESSION["company_code"]."','".$test."','".$arrgetShiftForHdrDtl["DAY_CODE"]."',
						  '".$arrgetShiftForHdrDtl["TIME_IN"]."','".$arrgetShiftForHdrDtl["LUNCH_OUT"]."',
						  '".$arrgetShiftForHdrDtl["LUNCH_IN"]."','".$arrgetShiftForHdrDtl["BREAK_OUT"]."',
						  '".$arrgetShiftForHdrDtl["BREAK_IN"]."','".$arrgetShiftForHdrDtl["TIME_OUT"]."',
						  '".$arrgetShiftForHdrDtl["CROSSDAY"]."','".$arrgetShiftForHdrDtl["RESTDAY"]."',
						  '08/27/2010','010002408');<br>";
		
	$shiftDesc = $arrgetShiftForHdrDtl["SHIFT_DESC"];
}*/

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Shift Code
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar">
						
                        <?php if($_SESSION['user_level']==1){ ?>
                        	<a href="#"  class="anchor" onclick="maintShiftCode('Add','','shift_type_maintenance_pop.php','empShiftTypeList',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" ><img class="anchor" src="../../../images/add.gif">Add Shift Code </a>|
                        	<FONT class="ToolBarseparator"></font>
                        <?php } ?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">In<?=$common->DropDownMenu(array('SHIFT CODE','SHIFT DESC.','STATUS'),'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('shift_type_maintenance_listAjaxResult.php','empShiftTypeList','Search',0,1,'txtSrch','cmbSrch','&empNo=<?=$_GET['empNo']?>','../../../images/')">
						
					</td>
					<tr>
						<td class="gridDtlLbl" align="center" width="2%">#</td>
						<td class="gridDtlLbl" align="center" width="10%">SHIFT CODE</td>
                        <td class="gridDtlLbl" align="center" width="29%">SHIFT SHORT DESC.</td>
                        <td class="gridDtlLbl" align="center" width="36%">SHIFT LONG DESC.</td>
                        <td class="gridDtlLbl" align="center" width="11%">STATUS</td>
                        <td width="12%" align="center" class="gridDtlLbl">&nbsp;</td>
					</tr>
					
                    <?php
						if($common->getRecCount($resShiftCodeList) > 0)
						{
							$i=0;
							$ctr=1;
								
							foreach ($arrShiftCodeList as $arrShiftCodeListVal)
							{
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
                   
                                <tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                                    <td class="gridDtlVal" align="center"><?=$ctr?></td>
                                    <td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?=$arrShiftCodeListVal['shiftCode']?></font></td>
                                    <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$arrShiftCodeListVal["shiftDesc"]?></font></td>
                                     <td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=$arrShiftCodeListVal["shiftLongDesc"]?></font></td>
                                    <?php
										$userInfo = $common->getUserInfo($sessionVars['compCode'],$arrShiftCodeListVal["addedBy"],'');
									?>
                                    <td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?php echo ($arrShiftCodeListVal["status"]=='A'?"ACTIVE":($arrShiftCodeListVal["status"]=='D'?"DELETED":"HELD")); ?></font></td>
                                    <td align="center">
                                    	<?php if($arrShiftCodeListVal["status"]!='D'){?>
                                    		<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_edit.png" title="View/Edit Shift Code Detail" onclick="maintShiftCode('Edit','<?=$arrShiftCodeListVal['shiftCode']?>','shift_type_maintenance_pop.php','empShiftTypeList',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>                                    
                                      		<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Shift Code Detail" onclick="delShiftCode('Delete','<?=$arrShiftCodeListVal['shiftCode']?>','shift_type_maintenance_pop.php','empShiftTypeList',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                                    	<?php }else{ ?>
                                        	<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/edit_prev_emp.png" title="Set to Active" onclick="setActiveShiftCode('setShiftActive','<?=$arrShiftCodeListVal['shiftCode']?>','shift_type_maintenance_pop.php','empShiftTypeList',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
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
						<td colspan="8" align="center" class="childGridFooter">
                        
							<? $pager->_viewPagerButton('shift_type_maintenance_listAjaxResult.php','empShiftTypeList',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$_GET['empNo']);?>
						</td>
					</tr>
				</TABLE>
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>
<script>
	
</script>
