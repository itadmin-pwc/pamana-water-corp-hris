<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("earnings.obj.php");

$srchType=0;
$earningsObj = new earningsObj($_GET,$_SESSION);
$earningsObj->validateSessions('','MODULES');

$pager = new AjaxPager(15,'../../../images/');

$qryIntMaxRec = "SELECT refNo
				 FROM tblEarnTranHeader 
				 WHERE compCode = '{$_SESSION['company_code']}' "; 
				 //and (refNo not like '91_%' and refNo not like '92_%' and refNo not like '93_%' and earnRem not like 'B-ADJ%') ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND refNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND earnRem LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}

        }
        $qryIntMaxRec .= "ORDER BY earnRem ";
$resIntMaxRec = $earningsObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
if(empty($intOffset)){
	$intOffset = 0;
}

$qryGetRefno = "SELECT * FROM tblEarnTranHeader WHERE compCode='".$_SESSION["company_code"]."' ";//and (refNo not like '91_%' and refNo not like '92_%' and refNo not like '93_%' and earnRem not like 'B-ADJ%')
	        if($_GET['isSearch'] == 1){
	        	if($_GET['srchType'] == 0){
	        		$qryGetRefno .= "AND refNo LIKE '".trim($_GET['txtSrch'])."%' ";
	        	}
	        	if($_GET['srchType'] == 1){
	        		$qryGetRefno .= "AND earnRem LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	        	}
	        }
$qryGetRefno .=	"AND compCode = '{$_SESSION['company_code']}'
				ORDER BY  earnRem limit $intOffset,$intLimit"; //and (refNo not like '91_%' and refNo not like '92_%' and refNo not like '93_%' and earnRem not like 'B-ADJ%')

$resGetRefNo = $earningsObj->execQry($qryGetRefno);
$arrGetRefNo = $earningsObj->getArrRes($resGetRefNo);

?>	
<BODY>
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
			<?=$earningsObj->DropDownMenu(array('Reference Number','Remarks'),'cmbSrch',$srchType,'class="inputs"');?>
			<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('earnings_reference_lookupAjaxResult.php','refLukupCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
		</td>
	</td>

	<tr>
		<td width="10%" class="gridDtlLbl" align="center">REF. NO.</td>
		<td width="40%" class="gridDtlLbl" align="center">REMARKS</td>
		<td width="40%" class="gridDtlLbl" align="center">TRANSACTION TYPE</td>
		<td width="10%" class="gridDtlLbl" align="center">STATUS</td>
	</tr>
	<?
	if(@$earningsObj->getRecCount($resGetRefNo) > 0){

		$i=0;
		foreach (@$arrGetRefNo as $RefNoVal){

			$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
			$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
			. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
	?>
	<tr style="cursor:pointer;" bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> onClick="passRefNo('<?=$RefNoVal['refNo']?>')">
		<td class="gridDtlVal" >
			<font class="gridDtlLblTxt" ><?=$RefNoVal['refNo']?></font>
		</td>
		<td class="gridDtlVal">
			<font class="gridDtlLblTxt"><?=$RefNoVal['earnRem'];?></font>
		</td>
		<td class="gridDtlVal">
			<?
				$transType = $earningsObj->getTransType($_SESSION['company_code'],'earnings','AND trnCode = '.$RefNoVal['trnCode']);
			?>
			<font class="gridDtlLblTxt"><?=$transType[0]['trnDesc']?></font>
		</td>
		<td class="gridDtlVal">
			<font class="gridDtlLblTxt"><?=($RefNoVal['earnStat'] == 'A') ? 'Active' : 'Held';?></font>
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
				$pager->_viewPagerButton('earnings_reference_lookupAjaxResult.php','refLukupCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');
			?>
		</td>
	</tr>
</TABLE>
</BODY>
<?
$earningsObj->disConnect();
?>	