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
	$qryIntMaxRec = "SELECT tblMinimumWage.minimumWageId, tblMinimumWage.compCode, 
					  tblMinimumWage.brnCode, tblMinimumWage.minimumWage_Old, tblMinimumWage.minimumWage_New, 
					  tblMinimumWage.eCola_Old, tblMinimumWage.eCola_New, tblMinimumWage.effectiveDate, 
                      tblMinimumWage.stat, tblBranch.brnDesc, tblBranch.brnShortDesc
					 FROM tblMinimumWage 
					 INNER JOIN tblBranch ON tblMinimumWage.brnCode = tblBranch.brnCode";
	$resIntMaxRec = $inqTeuObj->execQryI($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;
	$qryMin = "SELECT tblMinimumWage.minimumWageId, tblMinimumWage.compCode, tblMinimumWage.brnCode, tblMinimumWage.minimumWage_Old,
                      tblMinimumWage.minimumWage_New, tblMinimumWage.eCola_Old, tblMinimumWage.eCola_New, 
					  tblMinimumWage.effectiveDate, tblMinimumWage.stat, tblBranch.brnDesc, tblBranch.brnShortDesc
			   FROM tblMinimumWage 
			   INNER JOIN tblBranch ON tblMinimumWage.brnCode = tblBranch.brnCode limit $intOffset,$intLimit";
	
	$resMin = $inqTeuObj->execQry($qryMin);
	$MinEmpList = $inqTeuObj->getArrRes($resMin);
	
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
				&nbsp;<img src="../../../images/grid.png">&nbsp;MINIMUM WAGE</td>
			</tr>
			<tr>
				<td class="parentGridDtl">
					<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						
						
						<tr>
						  <td colspan="9" align="center" class="gridToolbar"><div align="left">
                          <?php if($_SESSION['user_level']==1){ ?>
						   <span><a href="#" onClick="PopUp('minimumwage_act.php?act=AddMinimumWage','ADD MINIMUM WAGE','<?=$dedListVal['recNo']?>','minimumwage_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor"><img class="anchor" src="../../../images/add.gif">Add Minimum Wage</a></span><?php }?> </div></td>
					  </tr>
						<tr>
							<td width="2%" class="gridDtlLbl" align="center">#</td>
							<td width="37%" class="gridDtlLbl" align="center">BRANCH</td>
						  <td width="9%" class="gridDtlLbl" align="center">MINIMUM WAGE OLD</td>
						  <td width="9%" class="gridDtlLbl" align="center">MINIMUM WAGE NEW</td>
						  <td width="8%" class="gridDtlLbl" align="center">ECOLA OLD						  </td>
						  <td width="8%" class="gridDtlLbl" align="center">ECOLA NEW</td>
						  <td width="10%" class="gridDtlLbl" align="center">EFFECTIVE DATE</td>
						  <td width="10%" class="gridDtlLbl" align="center">STATUS</td>
						  <td width="7%" class="gridDtlLbl" align="center">ACTION</td>
						</tr>
						<?
							if($inqTeuObj->getRecCount($resMin) > 0)
							{
								$i=0;
								foreach ($MinEmpList as $MinVal)
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
								    <?=$MinVal['brnDesc'];?></font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt" >
								    &nbsp;&nbsp;
								    <?=number_format($MinVal['minimumWage_Old'],2);?>
								    &nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt">
									  <?=number_format($MinVal['minimumWage_New'],2);?>
  &nbsp;&nbsp;</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt">
									  <?=number_format($MinVal['eCola_Old'],2);?>
									</font></td>
									<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt">
									  <?=number_format($MinVal['eCola_New'],2);?>
									</font></td>
									<td class="gridDtlVal" style="text-align: center;"><font class="gridDtlLblTxt">
									  <?=$inqTeuObj->dateFormat($MinVal['effectiveDate']);?>
									</font></td>
									<td class="gridDtlVal" style="text-align: center;"><font class="gridDtlLblTxt">
									  <?
									  if($MinVal['stat']=="A"){
										  echo "ACTIVE";
										  }
									  else{
										  echo "HELD";
										  }	  
									  ?>
									</font></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="center"><a href="#" onClick="PopUp('minimumwage_act.php?act=EditMinimumWage&minimumwageid=<?=$MinVal['minimumWageId']?>','EDIT MINIMUM WAGE','<?=$dedListVal['recNo']?>','minimumwage_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_edit.png" border="0" class="actionImg" title="Edit Minimum Wage" /></a></div></td>
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
									<? $pager->_viewPagerButton("minimumwage_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</table>
      </td>
				</tr>
			</table>
		</div>
	<?$inqTeuObj->disConnect();?>
</body>
</html>
