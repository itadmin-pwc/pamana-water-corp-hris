<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");
$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');


$qryIntMaxRec = "SELECT * FROM tblTsCorr
				 Where empNo='".$_GET["empNo"]."' 
				 
				";
				
if ($_GET['payPd']!=0) 
{
	$arrPayPd = $inqTSObj->getSlctdPd($_SESSION['company_code'],$_GET['payPd']);
	$qryIntMaxRec .= " AND pdYear='".$arrPayPd['pdYear']."' and pdNumber='".$arrPayPd['pdNumber']."' ";
}

$qryIntMaxRec .= " order by tsDate;";
	
$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

	$qryEmpList = "SELECT *
				FROM tblTsCorr
				WHERE empNo='".$_GET["empNo"]."' 
				AND compCode='".$_SESSION["company_code"]."'";
	if ($_GET['payPd']!=0) 
	{
		$arrPayPd = $inqTSObj->getSlctdPd($_SESSION['company_code'],$_GET['payPd']);
		$qryEmpList .= " AND pdYear='".$arrPayPd['pdYear']."' and pdNumber='".$arrPayPd['pdNumber']."' ";
	}
	
		$qryEmpList .= " order by tsDate Limit $intOffset,$intLimit";
	
	$resEmpList = $inqTSObj->execQry($qryEmpList);
	$arrEmpList = $inqTSObj->getArrRes($resEmpList);
	
	$userInfo = $common->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');
?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Time Sheet Adjusment : (<?=$userInfo['empNo'] ." - " . $userInfo['empFirstName'] . " " . $userInfo['empMidName'] . " " . $userInfo['empLastName']?>) (<?=($userInfo['empPayType']=='M') ? 'Monthly' : 'Daily';?>)
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="10" class="gridToolbar">
						<a href="#"  class="anchor" onclick="TimeSheet_Pop('add','<?php echo $_GET['empNo']; ?>','');">
                        <img class="anchor" src="../../../images/add.gif">Add Timesheet Adjusment</a> 
                        <FONT class="ToolBarseparator">|</font>
						 <?
						 	$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category']),'pdSeries','pdPayable','');
							$inqTSObj->DropDownMenu($arrPayPd,'payPd',$_GET['payPd'],"");
						 ?>
                        <INPUT type="hidden" name="txtSrch" id="txtSrch" value="" />
                        <INPUT type="hidden" name="cmbSrch" id="cmbSrch" value="" />
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('timesheet_adjustment_emp_list_ajax.php','empTsList','Search',0,1,'txtSrch','cmbSrch','&empNo=<?=$_GET['empNo']?>&&payPd='+document.getElementById('payPd').value,'../../../images/')">
						<INPUT type="button" name="btnBack" id="btnBack" value="BACK" onclick="location.href='timesheet_adjustment.php'" class="inputs">
					</td>
					<tr>
						<td class="gridDtlLbl" align="center" width="12%">TS DATE</td>
                        <td class="gridDtlLbl" align="center" width="18%">DAY TYPE</td>
                        <td class="gridDtlLbl" align="center" width="12%">ADJ. BASIC</td>
                        <td class="gridDtlLbl" align="center" width="12%">ADJ. OT</td>
                        <td class="gridDtlLbl" align="center" width="12%">ADJ. ND</td>
                        <td class="gridDtlLbl" align="center" width="15%">STATUS</td>
						<td class="gridDtlLbl" align="center">ACTION</td>
					</tr>
					
                    <?
						if($inqTSObj->getRecCount($resEmpList) > 0)
						{
							$i=0;
							foreach ($arrEmpList as $arrEmpList_val)
							{
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
								
								$qryDayType = "Select * from tblDayType where dayType='".$arrEmpList_val["dayType"]."'";
								$resDayType = $inqTSObj->execQry($qryDayType);
								$arrDayType = $inqTSObj->getSqlAssoc($resDayType);
								
								if($arrEmpList_val["tsStat"]=='A')
									$tsStat = "ACTIVE";
								elseif($arrEmpList_val["tsStat"]=='H')
									$tsStat = "HELD";
								else
									$tsStat = "PROCESSED";
					?>
						<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
							<td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?=date("m/d/Y", strtotime($arrEmpList_val['tsDate']));?></font></td>
							<td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=$arrDayType["dayTypeDesc"]?></font></td>
                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$arrEmpList_val["adjBasic"]?></font></td>
                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$arrEmpList_val["adjOt"]?></font></td>
                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$arrEmpList_val["adjNd"]?></font></td>
							<td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?=$tsStat?></font></td>
                            <td class="gridDtlVal" align="center">
                            	<?php if($arrEmpList_val["tsStat"]!='P'){ ?>
                            	<a href="#" onclick="TimeSheet_Pop('edit','<?php echo $_GET['empNo']; ?>','<?php echo date("Y-m-d", strtotime($arrEmpList_val['tsDate']));?>');"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Time Sheet Adjusment"></a>                                    
                                <a href="#" onclick="delEmpTsAdj('<?php echo $_GET['empNo']; ?>','<?php echo date("Y-m-d", strtotime($arrEmpList_val['tsDate']));?>');" ><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Time Sheet Adjusment"></a>                                    
                           		<?php } ?>
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
						<td colspan="10" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton('timesheet_adjusment_emp_list_ajax.php','empTsList',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$_GET['empNo'].'&payPd='.$_GET["payPd"]);?>
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