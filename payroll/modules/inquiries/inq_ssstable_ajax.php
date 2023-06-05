<?php
	/*
		Created By 		:	Genarra Jo - Ann Arong
		Date Created 	:	09 15 2009 10:50am
	*/
	
	session_start(); 
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("inq_ssstable_obj.php");
	
	$inqSSSObj = new inqSSSObj();
	$sessionVars = $inqSSSObj->getSeesionVars();
	$inqSSSObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	
	$inqSSSObj = new inqSSSObj();
	$sessionVars = $inqSSSObj->getSeesionVars();
	$inqSSSObj->validateSessions('','MODULES');
	
	$pager = new AjaxPager(10,'../../../images/');
	$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
	$qryIntMaxRec = "SELECT * from tblSssPhic
					 ORDER BY sssSeqNo";
	$resIntMaxRec = $inqSSSObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qrySss = "SELECT TOP $intLimit * from tblSssPhic
				WHERE sssSeqNo NOT IN (SELECT TOP $intOffset sssSeqNo FROM tblSssPhic)
				ORDER BY sssSeqNo";
	
	$resSss = $inqSSSObj->execQry($qrySss);
	$SssEmpList = $inqSSSObj->getArrRes($resSss);
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?=SYS_TITLE?></title>
	<style>@import url('../../style/main_emp_loans.css');</style>
	<script type='text/javascript' src='../../../includes/jSLib.js'></script>
	<script type='text/javascript' src='../../../includes/prototype.js'></script>
	<!--calendar lib-->
	<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
	<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
	<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
	<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
	<!--end calendar lib-->
	<script type='text/javascript' src='inq_ssstable_js.js'></script>

</head>

<body>
	<div class="niftyCorner">
		<table border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
			<tr>				
				<td>
					<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
					  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
					  <?=$inqSSSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="parentGridHdr"> 
					&nbsp;<img src="../../../images/grid.png">&nbsp;SSS Table
				</td>
			</tr>
			<tr>
				<td class="parentGridDtl">
					<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						<tr > 
							<td class="gridToolbar" colspan="7"> 
								&nbsp;
								<a href="#" onclick="printSSSTable();">
								<img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print SSS Table"></a>	
							</td>
						</tr>
						
						<tr>
							<td width="5%" class="gridDtlLbl" align="center">#</td>
							<td width="17%" class="gridDtlLbl" align="center">SSS LOW LIMIT</td>
							<td width="17%" class="gridDtlLbl" align="center">SSS UP LIMIT</td>
							<td width="17%" class="gridDtlLbl" align="center">SSS EMPLOYER SHARE</td>
							<td width="17%" class="gridDtlLbl" align="center">SSS EMPLOYEE SHARE</td>
							<td width="10%" class="gridDtlLbl" align="center">EC</td>
							<td width="15%" class="gridDtlLbl" align="center">SSS SALARY CREDIT</td>
						</tr>
						<?
							if($inqSSSObj->getRecCount($resSss) > 0)
							{
								$i=0;
								foreach ($SssEmpList as $SssListVal)
								{
									$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
									$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
									. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
						?>
								<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
									<td class="gridDtlVal"><?=$SssListVal['sssSeqNo']?></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt" ><?=$SssListVal['sssLowLimit']?></font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['sssUpLimit'] ?></font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['sssEmployer']?></font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['sssEmployee'] ?></font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['EC']?></font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['sssSalCredit'] ?></font></td>
								</tr>
							<?
								}
							}
							else
							{
							?>
								<tr>
									<td colspan="4" align="center">
										<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
									</td>
								</tr>
							<? } ?>
							
							<tr>
								<td colspan="7" align="right" class="childGridFooter">
									<?$pager->_viewPagerButton("inq_ssstable_ajax.php","SssListCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
								</td>
							</tr>
							
						</table>
					</td>
				</tr>
			</table>
		</div>
	<?$inqSSSObj->disConnect();?>
</body>
</html>
