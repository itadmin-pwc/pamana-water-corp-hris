<?php
	/*
		Created By 		:	Genarra Jo - Ann Arong
		Date Created 	:	09 15 2009 4:01 pm
	*/
	
	session_start(); 
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("inq_phictable_obj.php");
	
	$inqPhicObj = new inqPhicObj();
	$sessionVars = $inqPhicObj->getSeesionVars();
	$inqPhicObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	
	$pager = new AjaxPager(10,'../../../images/');
	$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
	$qryIntMaxRec = "SELECT * from tblSssPhic
					 ORDER BY sssSeqNo";
	$resIntMaxRec = $inqPhicObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryPhic = "SELECT TOP $intLimit * from tblSssPhic
				WHERE sssSeqNo NOT IN (SELECT TOP $intOffset sssSeqNo FROM tblSssPhic)
				ORDER BY sssSeqNo";
	
	$resPhic = $inqPhicObj->execQry($qryPhic);
	$PhicEmpList = $inqPhicObj->getArrRes($resPhic);
	
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
	<script type='text/javascript' src='inq_phictable_js.js'></script>

</head>

<body>
	
	<div class="niftyCorner">
		<table border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
			<tr>				
				<td>
					<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
					  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
					  <?=$inqPhicObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="parentGridHdr"> 
					&nbsp;<img src="../../../images/grid.png">&nbsp;Philhealth Table
				</td>
			</tr>
			<tr>
				<td class="parentGridDtl">
					<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						<tr > 
							<td class="gridToolbar" colspan="7"> 
								&nbsp;
								<a href="#" onClick="printPhicTable();">
								<img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Philhealth Table"></a>							</td>
						</tr>
						
						<tr>
							<td width="5%" class="gridDtlLbl" align="center">#</td>
							<td width="24%" class="gridDtlLbl" align="center">PHIC. LOW LIMIT</td>
							<td width="24%" class="gridDtlLbl" align="center">PHIC.  UP LIMIT</td>
							<td width="24%" class="gridDtlLbl" align="center">EMPLOYER SHARE</td>
							<td width="24%" class="gridDtlLbl" align="center">EMPLOYEE SHARE</td>
						</tr>
						<?
							if($inqPhicObj->getRecCount($resPhic) > 0)
							{
								$i=0;
								foreach ($PhicEmpList as $PhicListVal)
								{
									$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
									$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
									. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
						?>
								<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
									<td class="gridDtlVal"><div align="center">
									  <?=$PhicListVal['sssSeqNo']?>
								    </div></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt" ><?=$PhicListVal['sssLowLimit']?></font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$PhicListVal['sssUpLimit'] ?></font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$PhicListVal['phicEmployer']?></font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$PhicListVal['phicEmployee'] ?></font></td>
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
									<?$pager->_viewPagerButton("inq_phictable_ajax.php","PhicListCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</table>
			  </td>
				</tr>
			</table>
		</div>
	<?$inqPhicObj->disConnect();?>
</body>
</html>
