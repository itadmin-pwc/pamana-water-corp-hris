<?php
	
	session_start(); 
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	
	$inqTeuObj = new commonObj();
	$sessionVars = $inqTeuObj->getSeesionVars();
	$inqTeuObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	
	
	$pager = new AjaxPager(20,'../../../images/');
	$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
	$qryIntMaxRec = "SELECT posCode from tblPosition where compCode='{$_SESSION['company_code']}'
					";
	$resIntMaxRec = $inqTeuObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryPos = "SELECT TOP $intLimit *,status = CASE Active WHEN 'A' THEN 'Active' WHEN 'H' THEN 'Held' END from tblPosition where compCode='{$_SESSION['company_code']}' and  posCode NOT IN (SELECT TOP $intOffset posCode from tblPosition where compCode='{$_SESSION['company_code']}')
				";
	
	$resPos = $inqTeuObj->execQry($qryPos);
	$PosEmpList = $inqTeuObj->getArrRes($resPos);
	
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
					  <?=$inqTeuObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="parentGridHdr"> 
				&nbsp;<img src="../../../images/grid.png">&nbsp;POSITIONS</td>
			</tr>
			<tr>
				<td class="parentGridDtl">
					<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						
						
						<tr>
						  <td colspan="9" align="center" class="gridToolbar"><div align="left"><span onClick="PopUp('allowtype_act.php?act=AddAllowanceType','Add Allowance Type','<?=$dedListVal['recNo']?>','allowtype_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" style="cursor:pointer;"><img class="anchor" src="../../../images/add.gif">Add Position</span></div></td>
					  </tr>
						<tr>
							<td width="3%" class="gridDtlLbl" align="center">#</td>
							<td width="14%" class="gridDtlLbl" align="center">DIVISION</td>
							<td width="22%" class="gridDtlLbl" align="center">DEPARTMENT</td>
							<td width="15%" class="gridDtlLbl" align="center">SECTION</td>
							<td width="10%" class="gridDtlLbl" align="center">RANK</td>
							<td width="6%" class="gridDtlLbl" align="center">LEVEL</td>
							<td width="17%" class="gridDtlLbl" align="center">POSITION</td>
							<td width="7%" class="gridDtlLbl" align="center">STATUS</td>
							<td width="6%" class="gridDtlLbl" align="center">ACTION</td>
						</tr>
						<?
							if($inqTeuObj->getRecCount($resAllow) > 0)
							{
								$i=0;
								foreach ($PosEmpList as $PosVal)
								{
									$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
									$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
									. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
						?>
								<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
									<td class="gridDtlVal"><div align="center">
									  <?=$i?>
								    </div></td>
									<td class="gridDtlVal" style="text-align: right;">&nbsp;</td>
									<td class="gridDtlVal" style="text-align: right;">&nbsp;</td>
									<td class="gridDtlVal" style="text-align: right;">&nbsp;</td>
									<td class="gridDtlVal" style="text-align: right;">&nbsp;</td>
									<td class="gridDtlVal" style="text-align: right;">&nbsp;</td>
									<td class="gridDtlVal" style="text-align: right;"><div align="center"><font class="gridDtlLblTxt" >
                                      <?=$PosVal['posDesc']?></font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="center"><font class="gridDtlLblTxt" >
                                        <?=$PosVal['status']?>
									  &nbsp;&nbsp;</font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="center"><a href="#" onClick="PopUp('allowtype_act.php?act=EditAllowanceType&allowCode=<?=$PosVal['allowCode']?>','Edit Allowance Type','<?=$dedListVal['recNo']?>','allowtype_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_get.png" border="0" class="actionImg" title="Edit Allowance Type" /></a></div></td>
								</tr>
							<?
								}
							}
							else
							{
							?>
								<tr>
									<td colspan="9" align="center">
										<FONT class="zeroMsg">NOTHING TO DISPLAY</font>									</td>
								</tr>
							<? } ?>
							
							<tr>
								<td colspan="9" align="right" class="childGridFooter">
									<?$pager->_viewPagerButton("allowtype_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</table>
			  </td>
				</tr>
			</table>
		</div>
	<?$inqTeuObj->disConnect();?>
</body>
</html>
