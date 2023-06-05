<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$pager = new AjaxPager(20,'../../../images/');

$arrSrch = array('','REF NO.','ACTIVE (status)','HELD (status)','PROCESSED (status)');
if($_GET['isSearch'] == 1){
	if($_GET['srchType'] == 1) { $refNo = " AND refNo = '{$_GET['txtSrch']}' "; } else { $refNo = ""; $statusType = ""; }
	if($_GET['srchType'] == 2) $statusType = " AND earnStat = 'A' ";
	if($_GET['srchType'] == 3) $statusType = " AND earnStat = 'H' ";
	if($_GET['srchType'] == 4) $statusType = " AND earnStat = 'P' ";
	
	if ($_GET['payPd']!=0) {
		$arrPayPd = $inqTSObj->getSlctdPd($_SESSION['company_code'],$_GET['payPd']);
		$statusType .= " AND pdYear='".$arrPayPd['pdYear']."' and pdNumber='".$arrPayPd['pdNumber']."' ";
	}
}
$qryIntMaxRec = "SELECT * FROM tblEarnTranHeader 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     $refNo $statusType 
				 ORDER BY refNo DESC ";
$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEarnList = "SELECT  *
		FROM tblEarnTranHeader
		WHERE  compCode = '{$sessionVars['compCode']}' 
			     $refNo $statusType 
				 ORDER BY refNo DESC Limit $intOffset,$intLimit";
$resEarnList = $inqTSObj->execQry($qryEarnList);
$arrEarnList = $inqTSObj->getArrRes($resEarnList);
?>

<HTML>
<head>
	<script type='text/javascript' src='timesheet_js.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>		
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Earnings Proof List</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
							<td colspan="6" class="gridToolbar" align="">Search
						  	<INPUT type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">In<?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?> <? 					
								$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category']),'pdSeries','pdPayable','');
								$inqTSObj->DropDownMenu($arrPayPd,'payPd',$_GET['payPd'],"");
							?>
						  	<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('earnings_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','&payPd='+document.getElementById('payPd').value,'../../../images/')">
                &nbsp;<font class="ToolBarseparator">|</font> <a href="#" onClick="printEarningsList();">
                <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Earnings Proof List"></a>
							</td>
							<tr>
								<td width="10%" class="gridDtlLbl" align="center">REF.NO.</td>
								<td width="30%" class="gridDtlLbl" align="center">TRANSACTION</td>
								<td width="10%" class="gridDtlLbl" align="center">AMOUNT</td>
								<td width="30%" class="gridDtlLbl" align="center">REMARKS</td>
								<td width="10%" class="gridDtlLbl" align="center">STATUS</td>
								<td class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<?
							if($inqTSObj->getRecCount($resEarnList) > 0){
								$i=0;
								foreach ($arrEarnList as $earnListVal){
								$arrTotal = $inqTSObj->getTranEarningsTotal($sessionVars['compCode'],$earnListVal['refNo']);
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$earnListVal['refNo']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$inqTSObj->getTransTypeDescArt($sessionVars['compCode'],$earnListVal['trnCode'])?></font></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$arrTotal['totAmt']?></font></td>
								<td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?=$earnListVal['earnRem']?></font></td>
								<td class="gridDtlVal" align="center"><font class="gridDtlLblTxt">
										<? 
											if ($earnListVal['earnStat']=="P") echo "PROCESSED";
											if ($earnListVal['earnStat']=="A") echo "ACTIVE";
											if ($earnListVal['earnStat']=="H") echo "HELD";
										?>
									</font>
								</td>
              					<td class="gridDtlVal" align="center">
									<a href="#" onClick="viewDetails('Earnings','<?=$earnListVal['refNo']?>','earnings_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="actionImg" src="../../../images/application_get.png" title="View Details"></a>
								</td>
							</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="6" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="6" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("earnings_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
			
			
		</div>
		<?$inqTSObj->disConnect();?>
		<form name="frmTSko" method="post">
			<input type="hidden" name="txtSrch2" id="txtSrch2" value="<?php echo $_GET['txtSrch']; ?>">
			<input type="hidden" name="isSearch2" id="isSearch2" value="<?php echo $_GET['isSearch']; ?>">
			<input type="hidden" name="srchType2" id="srchType2" value="<?php echo $_GET['srchType']; ?>">
			<input type="hidden" name="payPd2" id="payPd2" value="<?php echo $_GET['payPd']; ?>">
	</form>
	</BODY>
</HTML>
