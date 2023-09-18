<?php
	
	session_start(); 
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	
	$inqTeuObj = new commonObj();
	$sessionVars = $inqTeuObj->getSeesionVars();
	$inqTeuObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	
	
	$pager = new AjaxPager(10,'../../../images/');
	$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
	$qryIntMaxRec = "SELECT * from tblLoanType where compCode='{$_SESSION['company_code']}'
					";
	$resIntMaxRec = $inqTeuObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qrySss = "SELECT *,CASE lonTypeStat WHEN 'A' THEN 'Active' WHEN 'H' THEN 'Held' END as status  from tblLoanType where compCode='{$_SESSION['company_code']}' Limit $intOffset,$intLimit				";
	
	$resSss = $inqTeuObj->execQry($qrySss);
	$SssEmpList = $inqTeuObj->getArrRes($resSss);
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?=SYS_TITLE?></title>
	<style>@import url('../../style/main_emp_loans.css');</style>
	<script type='text/javascript' src='../../../includes/jSLib.js'></script>
	<script type='text/javascript' src='../../../includes/prototype.js'></script>
	<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
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
				&nbsp;<img src="../../../images/grid.png">&nbsp;LOAN TYPES</td>
			</tr>
			<tr>
				<td class="parentGridDtl">
					<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						<tr>
						  	  <td colspan="5" align="center" class="gridToolbar">
                        <div align="left" >
                        <?php if($_SESSION['user_level']==1){ ?>
                        <a href="#" onClick="PopUp('loantype_act.php?act=AddLoanType','ADD LOAN TYPE','<?=$dedListVal['recNo']?>','loantype_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Loan Type</a> 
                      
                                  <FONT class="ToolBarseparator">|</font>
                                  <?php } ?>
						         <span style="visibility:hidden;"> <?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
							
							
					
						?>
						Search
						<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">
						In
						<?=$inqTeuObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('holiday_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">                              
                               </span> </div>                               </td>
					  	  </tr>						
						<tr>
							<td width="3%" class="gridDtlLbl" align="center">#</td>
							<td width="14%" class="gridDtlLbl" align="center">LOAN CODE</td>
							<td width="42%" class="gridDtlLbl" align="center">LOAN TYPE</td>
							<td width="19%" class="gridDtlLbl" align="center">STATUS</td>
							<td width="22%" class="gridDtlLbl" align="center">ACTION</td>
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
									<td class="gridDtlVal" style="text-align: right;"><div align="center"><font class="gridDtlLblTxt" >
								    <?=$teuVal['lonTypeCd']?>
								    </font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="left"><font class="gridDtlLblTxt" >
								    &nbsp;&nbsp;
								    <?=$teuVal['lonTypeDesc']?></font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="center"><font class="gridDtlLblTxt" >
                                      <?=$teuVal['status']?>
								    </font></div></td>
									<td class="gridDtlVal" style="text-align: right;"><div align="center">
									  <div align="center"><a href="#" onClick="PopUp('loantype_act.php?act=EditLoanType&loantype=<?=$teuVal['lonTypeCd']?>','EDIT LOAN TYPE','<?=$dedListVal['recNo']?>','loantype_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_magnify.png" border="0" class="actionImg" title="edit Day Type" /></a></div>
									</div></td>
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
									<?$pager->_viewPagerButton("loantype_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</table>
			  </td>
				</tr>
			</table>
		</div>
	<?$inqTeuObj->disConnect();?>
</body>
</html>
