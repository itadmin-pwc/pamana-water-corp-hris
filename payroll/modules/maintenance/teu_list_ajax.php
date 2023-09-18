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
	$qryIntMaxRec = "SELECT * from tblTeu
					";
	$resIntMaxRec = $inqTeuObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qrySss = "SELECT  * from tblTeu Limit $intOffset,$intLimit";
	
	$resSss = $inqTeuObj->execQry($qrySss);
	$SssEmpList = $inqTeuObj->getArrRes($resSss);
	
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
				&nbsp;<img src="../../../images/grid.png">&nbsp;TAX EXEMPTION UNITS</td>
			</tr>
			<tr>
				<td class="parentGridDtl">
					<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						
						
						<tr>
						  <td colspan="5" align="center" class="gridToolbar"><div align="left">
                          <?php if($_SESSION['user_level']==1){ ?>
						   <span onClick="PopUp('teu_act.php?act=AddTaxExemption','ADD TAX EXEMPTION','<?=$dedListVal['recNo']?>','teu_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" style="cursor:pointer;"><img class="anchor" src="../../../images/add.gif">Add Tax Exemption</span><?php }?> </div></td>
					  </tr>
						<tr>
							<td width="5%" class="gridDtlLbl" align="center">#</td>
							<td width="14%" class="gridDtlLbl" align="center">TEU CODE</td>
						  <td width="45%" class="gridDtlLbl" align="center">DESCRIPTION</td>
						  <td width="21%" class="gridDtlLbl" align="center">AMOUNT</td>
						  <td width="15%" class="gridDtlLbl" align="center">ACTION</td>
						</tr>
						<?
							if($inqTeuObj->getRecCount($resSss) > 0)
							{
								$i=0;
								foreach ($SssEmpList as $teuVal)
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
								    <?=$teuVal['teuCode']?></font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="left"><font class="gridDtlLblTxt" >
								    &nbsp;&nbsp;
								    <?=$teuVal['teuDesc']?>
								    &nbsp;&nbsp;</font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt">
									  <?=$teuVal['teuAmt'] ?>
  &nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="center"><a href="#" onClick="PopUp('teu_act.php?act=EditTaxExemption&teuCode=<?=$teuVal['teuCode']?>','EDIT TAX EXEMPTION','<?=$dedListVal['recNo']?>','teu_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_magnify.png" border="0" class="actionImg" title="Edit Tax Exemption" /></a></div></td>
								</tr>
							<?
								}
							}
							else
							{
							?>
								<tr>
									<td colspan="5" align="center">
										<FONT class="zeroMsg">NOTHING TO DISPLAY</font>									</td>
								</tr>
							<? } ?>
							
							<tr>
								<td colspan="5" align="right" class="childGridFooter">
									<?$pager->_viewPagerButton("teu_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</table>
			  </td>
				</tr>
			</table>
		</div>
	<?$inqTeuObj->disConnect();?>
</body>
</html>
