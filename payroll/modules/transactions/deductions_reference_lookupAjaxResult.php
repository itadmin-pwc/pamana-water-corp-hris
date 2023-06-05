<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("deductions.obj.php");

$srchType = 0;

$deductionsObj = new maintDeduct($_GET,$_SESSION);
$deductionsObj->validateSessions('','MODULES');

$pager = new AjaxPager(14,'../../../images/');

$qryIntMaxRec = "SELECT refNo
				 FROM tblDedTranHeader 
				 WHERE compCode = '{$_SESSION['company_code']}' ";
if($_GET['isSearch'] == 1){
	if($_GET['srchType'] == 0){
		$qryIntMaxRec .= "AND refNo LIKE '".trim($_GET['txtSrch'])."%' ";
	}
	if($_GET['srchType'] == 1){
		$qryIntMaxRec .= "AND dedRem LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	}

}
$qryIntMaxRec .= "ORDER BY dedRemarks ";
$resIntMaxRec = $deductionsObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
if(empty($intOffset)){
	$intOffset = 0;
}

$qryGetRefno = "SELECT  *
							 FROM tblDedTranHeader
							 WHERE  compCode = '{$_SESSION['company_code']}'";
if($_GET['isSearch'] == 1){
	if($_GET['srchType'] == 0){
		$qryGetRefno .= "AND refNo LIKE '".trim($_GET['txtSrch'])."%' ";
	}
	if($_GET['srchType'] == 1){
		$qryGetRefno .= "AND dedRemarks LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	}
}
$qryGetRefno .=	" ORDER BY  dedRemarks Limit $intOffset,$intLimit";

$resGetRefNo = $deductionsObj->execQry($qryGetRefno);
$arrGetRefNo = $deductionsObj->getArrRes($resGetRefNo);

?>	
<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
	<tr>
		<td colspan="8" class="gridToolbar">
			<?
			if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
				if(isset($_GET['srchType']) ){ 
					$srchType = $_GET['srchType'];
				}
			}
			?>
			Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In
			<?=$deductionsObj->DropDownMenu(array('Reference Number','Remarks'),'cmbSrch',$srchType,'class="inputs"');?>
			<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('deductions_reference_lookupAjaxResult.php','refLukupCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
		</td>
	</tr>

	<tr>
		<td width="10%" class="gridDtlLbl" align="center">REF. NO.</td>
		<td width="40%" class="gridDtlLbl" align="center">REMARKS</td>
		<td width="40%" class="gridDtlLbl" align="center">TRANSACTION TYPE</td>
		<td width="10%" class="gridDtlLbl" align="center">STATUS</td>
	</tr>
	<?
	if(@$deductionsObj->getRecCount($resGetRefNo) > 0){

		$i=0;
		foreach (@$arrGetRefNo as $RefNoVal){

			$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
			$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
			. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
	?>
	<tr style="cursor:pointer;" bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> onclick="passRefNo('<?=$RefNoVal['refNo']?>')">
		<td class="gridDtlVal" >
			<font class="gridDtlLblTxt" ><?=$RefNoVal['refNo']?></font>
		</td>
		<td class="gridDtlVal">
			<font class="gridDtlLblTxt"><?=$RefNoVal['dedRemarks'];?></font>
		</td>
		<td class="gridDtlVal">
			<?
				$transType = $deductionsObj->getTransType($_SESSION['company_code'],'deductions','AND trnCode = '.$RefNoVal['trnCode'].' and trnPriority='.$RefNoVal['trnPriority'].'' );
			?>
			<font class="gridDtlLblTxt"><?=$transType[0]['trnDesc']?></font>
		</td>
		<td class="gridDtlVal">
			<font class="gridDtlLblTxt"><?=($RefNoVal['dedStat'] == 'A') ? 'Active' : 'Held';?></font>
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
			<?
				$pager->_viewPagerButton('deductions_reference_lookupAjaxResult.php','refLukupCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');
			?>
		</td>
	</tr>
</TABLE>
<?
$deductionsObj->disConnect();
?>	