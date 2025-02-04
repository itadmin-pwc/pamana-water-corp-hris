<?php
	/*
		Created By 		:	Genarra Jo - Ann Arong
		Date Created 	:	09 15 2009 4:01 pm
	*/
	
	session_start(); 
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	
	$inqOtObj = new commonObj();
	$sessionVars = $inqOtObj->getSeesionVars();
	$inqOtObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	
	$pager = new AjaxPager(10,'../../../images/');
	$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
	$qryIntMaxRec = "SELECT * from tblOtPrem
					 ORDER BY dayType ASC ";
	$resIntMaxRec = $inqOtObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryOt = "SELECT * from tblOtPrem
				ORDER BY dayType ASC Limit $intOffset,$intLimit";
	
	$resOt = $inqOtObj->execQry($qryOt);
	$otList = $inqOtObj->getArrRes($resOt);
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?=SYS_TITLE?></title>
	<style>@import url('../../style/main_emp_loans.css');</style>
	<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
	<script type='text/javascript' src='../../../includes/jSLib.js'></script>
	<script type='text/javascript' src='../../../includes/prototype.js'></script>
	<!--calendar lib-->
	<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
	<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
	<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
	<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
	<!--end calendar lib-->
	<script type='text/javascript' src='inq_ottable_js.js'></script>

</head>

<body>
	
	<div class="niftyCorner">
		<table border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
			<tr>				
				<td>
					<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
					  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
					  <?=$inqOtObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="parentGridHdr"> 
					&nbsp;<img src="../../../images/grid.png">&nbsp;OVERTIME/NIGHT DEFFERENTIAL PREMIUMS</td>
			</tr>
			<tr>
				<td class="parentGridDtl">
					<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						
						
						<tr>
							<td width="5%" class="gridDtlLbl" align="center">#</td>
							<td width="20%" class="gridDtlLbl" align="center">DAY TYPE</td>
							<td width="15%" class="gridDtlLbl" align="center">OT NOT > 8 HRS</td>
							<td width="15%" class="gridDtlLbl" align="center">OT > 8 HRS</td>
							<td width="15%" class="gridDtlLbl" align="center">ND NOT > 8 HRS</td>
							<td width="15%" class="gridDtlLbl" align="center">ND > 8 HRS</td>
						</tr>
						<?
							if($inqOtObj->getRecCount($resOt) > 0)
							{
								$i=0;
								foreach ($otList as $otListVal)
								{
									$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
									$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
									. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
						?>
								<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
									<td class="gridDtlVal"><div align="center">
									  <?=$i?>
								    </div></td>
									<td class="gridDtlVal"><font class="gridDtlLblTxt" ><?=$inqOtObj->getDayTypeDescArt($otListVal['dayType'])?></font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$otListVal['otPrem8'] ?>
								    &nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$otListVal['otPremOvr8']?>
								    &nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$otListVal['ndPrem8'] ?>
								    &nbsp;&nbsp;</font></td>
								  <td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$otListVal['ndPremOvr8'] ?>&nbsp;&nbsp;</font></td>
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
									<?$pager->_viewPagerButton("overtime_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</table>
			  </td>
				</tr>
			</table>
		</div>
	<?$inqOtObj->disConnect();?>

</body>
</html>
