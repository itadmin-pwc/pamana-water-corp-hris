<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$pager = new AjaxPager(10,'../../../images/');

$arrSrch = array('TRANS.CODE.','DESCRIPTION','EARNINGS (category)','DEDUCTIONS (category)');
if($_GET['isSearch'] == 1){
	if($_GET['srchType'] == 0) $transCode = " AND trnCode = '{$_GET['txtSrch']}' "; else { $transCode = ""; $transCat = ""; }
	if($_GET['srchType'] == 1) $transDesc = " AND trnDesc LIKE '{$_GET['txtSrch']}%' "; else { $transDesc = ""; $transCat = ""; }
	if($_GET['srchType'] == 2) $transCat = " AND trnCat = 'E' "; 
	if($_GET['srchType'] == 3) $transCat = " AND trnCat = 'D' "; 
}
$qryIntMaxRec = "SELECT * FROM tblPayTransType 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     $transCode $transDesc $transCat AND trnStat = 'A' 
				 ORDER BY trnCode, trnRecode ASC ";
$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryTransList = "SELECT TOP $intLimit *
		FROM tblPayTransType
		WHERE seqNo NOT IN
        (SELECT TOP $intOffset seqNo FROM tblPayTransType WHERE compCode = '{$sessionVars['compCode']}' 
			      $transCode $transDesc $transCat AND trnStat = 'A' 
				 ORDER BY trnCode, trnRecode ASC) 
				AND compCode = '{$sessionVars['compCode']}' 
			      $transCode $transDesc $transCat AND trnStat = 'A' 
				 ORDER BY trnCode, trnRecode ASC ";
$resTransList = $inqTSObj->execQry($qryTransList);
$arrTransList = $inqTSObj->getArrRes($resTransList);
?>

<HTML>
<head>
	<script type='text/javascript' src='timesheet_js.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>		
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Transactions Type List</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
							<td colspan="8" class="gridToolbar" align="">Search
						  	<INPUT type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">In<?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						  	<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('inq_trans_type_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
                &nbsp;<font class="ToolBarseparator">|</font> <a href="#" onclick="printTransList();">
                <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Transactions Type List"></a>
							</td>
							<tr>
								<td width="10%" class="gridDtlLbl" align="center">CODE</td>
								<td width="30%" class="gridDtlLbl" align="center">DESCRIPTION</td>
								<td width="10%" class="gridDtlLbl" align="center">CATEGORY</td>
								<td width="10%" class="gridDtlLbl" align="center">PAY PERIOD</td>
								<td width="30%" class="gridDtlLbl" align="center">REGISTER GROUP</td>
								<td width="10%" class="gridDtlLbl" align="center">PRIORITY</td>
								<td width="10%" class="gridDtlLbl" align="center">TAXABLE</td>
							</tr>
							<?
							if($inqTSObj->getRecCount($resTransList) > 0){
								$i=0;
								foreach ($arrTransList as $transListVal){
								if ($transListVal['trnCat']=="E") $trnCat = "EARNINGS";
								if ($transListVal['trnCat']=="D") $trnCat = "DEDUCTIONS";
								if ($transListVal['trnApply']=="1") $trnPayPeriod = "1ST ";
								if ($transListVal['trnApply']=="2") $trnPayPeriod = "2ND";
								if ($transListVal['trnApply']=="3") $trnPayPeriod = "BOTH";
								if ($transListVal['trnTaxCd']=="Y") $trnTax = "YES";
								if ($transListVal['trnTaxCd']=="N") $trnTax = "NO";
								if ($transListVal['trnTaxCd']=="") $trnTax = "---";
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$transListVal['trnCode']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$transListVal['trnDesc']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$trnCat?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$trnPayPeriod?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$inqTSObj->getTransTypeDescArt($sessionVars['compCode'],$transListVal['trnRecode'])?></font></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$transListVal['trnPriority']?></font></td>
								<td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?=$trnTax?></font></td>
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
								<td colspan="8" align="center" class="childGridFooter">
									<?$pager->_viewPagerButton("inq_trans_type_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
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
		</form>
	</BODY>
</HTML>
