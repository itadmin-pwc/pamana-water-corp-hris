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
	$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
	$qryIntMaxRec = "SELECT * from tblAllowType where compCode='{$_SESSION['company_code']}' and hrTag='Y'";
	$resIntMaxRec = $inqTeuObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;
	$qryAllow = "SELECT *, CASE allowTypeStat WHEN 'A' THEN 'Active' WHEN 'H' THEN 'Held' END as status from tblAllowType where compCode='{$_SESSION['company_code']}' and hrTag='Y' order by allowTypeStat, allowDesc limit $intOffset,$intLimit";
	
	$resAllow = $inqTeuObj->execQry($qryAllow);
	$AllowEmpList = $inqTeuObj->getArrRes($resAllow);
	
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
				&nbsp;<img src="../../../images/grid.png">&nbsp;ALLOWANCE TYPES</td>
			</tr>
			<tr>
				<td class="parentGridDtl">
					<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						
						
						<tr>
						  <td colspan="6" align="center" class="gridToolbar"><?php if($_SESSION['user_level']==1){ ?><div align="left"><span ><a href="#" onClick="PopUp('allowtype_act.php?act=AddAllowanceType','ADD ALLOWANCE TYPE','<?=$dedListVal['recNo']?>','allowtype_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Allowance Type</a></span></div><?php } ?></td>
					  </tr>
						<tr>
							<td width="5%" class="gridDtlLbl" align="center">#</td>
							<td width="29%" class="gridDtlLbl" align="center">ALLOWANCE TYPE</td>
                            <td width="15%" class="gridDtlLbl" align="center">ATTENDANCE BASE ?</td>
                            <td width="9%" class="gridDtlLbl" align="center">SEPARATE <br> PAYSLIP</td>
							<td width="20%" class="gridDtlLbl" align="center">STATUS</td>
							<td width="22%" class="gridDtlLbl" align="center">ACTION</td>
						</tr>
						<?
							if($inqTeuObj->getRecCount($resAllow) > 0)
							{
								$i=0;
								foreach ($AllowEmpList as $AllowVal)
								{
									$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
									$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
									. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
						?>
								<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
									<td class="gridDtlVal"><div align="center">
									  <?=$i?>
								    </div></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="left"><font class="gridDtlLblTxt" >
								    &nbsp;&nbsp;
								    <?=$AllowVal['allowDesc']?></font></div></td>
                                    <td class="gridDtlVal" style="text-align: right;"><div align="left"><font class="gridDtlLblTxt" >
								    &nbsp;&nbsp;
								    <?php echo ($AllowVal['attnBase']=='N'?"No":"Yes"); ?></font></div></td>
                                    <td class="gridDtlVal" style="text-align: right;"><div align="left"><font class="gridDtlLblTxt" >
								    &nbsp;&nbsp;
								    <?php echo ($AllowVal['sprtPS']!='Y'?"No":"Yes")?></font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="center"><font class="gridDtlLblTxt" >
                                        <?=$AllowVal['status']?>
									  &nbsp;&nbsp;</font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="center"><a href="#" onClick="PopUp('allowtype_act.php?act=EditAllowanceType&allowCode=<?=$AllowVal['allowCode']?>','EDIT ALLOWANCE TYPE','<?=$dedListVal['recNo']?>','allowtype_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_edit.png" border="0" class="actionImg" title="Edit Allowance" /></a></div></td>
								</tr>
							<?
								}
							}
							else
							{
							?>
								<tr>
									<td colspan="6" align="center">
										<FONT class="zeroMsg">NOTHING TO DISPLAY</font>									</td>
								</tr>
							<? } ?>
							
							<tr>
								<td colspan="6" align="right" class="childGridFooter">
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
