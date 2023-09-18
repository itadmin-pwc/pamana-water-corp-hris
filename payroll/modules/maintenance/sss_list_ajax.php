<?php
	
	session_start(); 
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	
	$inqSSSObj = new commonObj();
	$sessionVars = $inqSSSObj->getSeesionVars();
	$inqSSSObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	
	
	$pager = new AjaxPager(10,'../../../images/');
	$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
	$qryIntMaxRec = "SELECT * from tblSssPhic
					 ORDER BY sssSeqNo";
	$resIntMaxRec = $inqSSSObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qrySss = "SELECT * from tblSssPhic
				ORDER BY sssSeqNo Limit $intOffset,$intLimit";
	
	$resSss = $inqSSSObj->execQry($qrySss);
	$SssEmpList = $inqSSSObj->getArrRes($resSss);
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?=SYS_TITLE?></title>
	<style>@import url('../../style/main_emp_loans.css');</style>
	<script type='text/javascript' src='../../../includes/jSLib.js'></script>
	<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
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
					&nbsp;<img src="../../../images/grid.png">&nbsp;SSS/PHILHEALTH PREMIUM TABLE
				</td>
			</tr>
			<tr>
				<td class="parentGridDtl">
					<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						
						
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
									<td class="gridDtlVal"><div align="center">
									  <?=$SssListVal['sssSeqNo']?>
								    </div></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt" ><?=$SssListVal['sssLowLimit']?>&nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['sssUpLimit'] ?>
								    &nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['sssEmployer']?>
								    &nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['sssEmployee'] ?>
								    &nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['EC']?>
								    &nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$SssListVal['sssSalCredit'] ?>
								    &nbsp;&nbsp;</font></td>
								</tr>
							<?
								}
							}
							else
							{
							?>
								<tr>
									<td colspan="4" align="center">
										<FONT class="zeroMsg">NOTHING TO DISPLAY</font>									</td>
								</tr>
							<? } ?>
							
							<tr>
								<td colspan="7" align="right" class="childGridFooter">
									<?$pager->_viewPagerButton("sss_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</table>
			  </td>
				</tr>
			</table>
		</div>
	<?$inqSSSObj->disConnect();?>
</body>
</html>
