<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$pager = new AjaxPager(4,'../../../images/');

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
$refNo = $_GET['refNo'];
$earnHdr = $inqTSObj->getEarnTranHdr($sessionVars['compCode'], $refNo);
$qryIntMaxRec = "SELECT * FROM tblEarnTranDtl 
			     WHERE compCode = '{$sessionVars['compCode']}' AND refNo = '{$refNo}'";

$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEarnList = "SELECT * FROM tblEarnTranDtl
				WHERE compCode = '{$sessionVars['compCode']}' AND refNo = '{$refNo}' limit $intOffset,$intLimit";
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
					
			  <td colspan="6" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">Ref.No.: <? echo $refNo;?> (<? echo $inqTSObj->getTransTypeDescArt($sessionVars['compCode'],$earnHdr['trnCode'])?>)
				<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
				  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
				  <?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
				</div></td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
							<tr>
								<td width="1%" class="gridDtlLbl" align="center">#</td>
								<td width="10%" class="gridDtlLbl" align="center">EMP.NO.</td>
								<td width="25%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
								<td width="10%" class="gridDtlLbl" align="center">CONTRL.NO.</td>
								<td width="20%" class="gridDtlLbl" align="center">AMOUNT</td>
								<td width="20%" class="gridDtlLbl" align="center">TAXABLE</td>
							</tr>
							<?
							if($inqTSObj->getRecCount($resEarnList) > 0){
								$i=0;
								foreach ($arrEarnList as $earnListVal){
								if ($earnListVal['trnTaxCd']=="Y") $earnTax =  "YES";
								if ($earnListVal['trnTaxCd']=="N") $earnTax =  "NO";
								if ($earnListVal['trnTaxCd']=="") $earnTax =  "---";
								$employee = $inqTSObj->getUserInfo($sessionVars['compCode'],$earnListVal['empNo'],"");
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal"><?=$i?></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$earnListVal['empNo']?></font></td>
								<td class="gridDtlVal">
									<font class="gridDtlLblTxt">
										<?
											echo str_replace("Ñ","&Ntilde;",$employee['empLastName']. ", " . $employee['empFirstName'][0] .".". $employee['empMidName'][0].".");
										?>
									</font>
								</td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$earnListVal['trnCntrlNo']?></font></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$earnListVal['trnAmount']?></font></td>
								<td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?=$earnTax?></font></td>
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
									<?$pager->_viewPagerButton("earnings_list_details_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$refNo);?>
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
		</div>
		<?$inqTSObj->disConnect();?>
		<form name="frmEmpList" method="post">
		  <input type="hidden" name="refNo" id="refNo" value="<? echo $_GET['refNo']; ?>">
		</form>
	</BODY>
</HTML>
