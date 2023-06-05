<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("movement.trans.php");
##################################################
$user=$inqTSObj->getUserLogInInfoForMenu($_SESSION['employee_number']);
$ulevel=$user['userLevel'];

if ($_GET['code']=="") {
	$code = $_POST['code'];
} else {
	$code = $_GET['code'];
}
if($ulevel!=3){
	$restrict="";
}
else{
	$restrict="disabled='disabled'";	
}

?>
<HTML>
	<HEAD>
<TITLE><?=SYS_TITLE?></TITLE>
<style>@import url('../../style/reports.css');</style>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
<script>
function managementReport() {
//	if (document.frmTS.branch.value>0) {	
//		alert('nhomer');
		var obj = document.getElementById('branch').value;
		var n = <? echo $ulevel;?>;
		if(n==3){
			if(obj==0){
				alert('Please select branch.');
				return false;	
			}
		}	
		document.frmTS.action = "management_report_excel.php";
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "management_report.php";
		document.frmTS.target = "_self";
}
function managementReportS() {
//	if (document.frmTS.branch.value>0) {	
//		alert('nhomer');		
		document.frmTS.action = "management_report_salary_excel.php";
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "management_report.php";
		document.frmTS.target = "_self";
}

</script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Management Report</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'>
              <input type="hidden" name="userlevel" id="userlevel" value="<?=$ulevel;?>"></td>
          </tr>
          
          <tr>
            <td width="18%" class="gridDtlLbl">Branch</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 	
			$sqlBranch = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' and brnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')";				
			$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($sqlBranch));
								if($ulevel==3){
									$arrBranch = $inqTSObj->makeArr($arrBranch,'brnCode','brnDesc','');	
								}
								else{
									$arrBranch = $inqTSObj->makeArr($arrBranch,'brnCode','brnDesc','All');
								}
								$inqTSObj->DropDownMenu($arrBranch,'branch','','');
							?>            </td>
            </tr>            
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="managementReports" id="managementReports" <? echo $searchTS4_dis; ?> value="Print to Excel" onClick="managementReport();">
					  <input type="button" name="managementReportsSalary" id="managementReportsSalary" <? echo $searchTS4_dis; echo $restrict;?> value="Print to Excel(With Salary)" onClick="managementReportS();">
					  </CENTER>
					</td>
				  </tr>
			  </table> 
	</td>
	</tr> 
	<tr > 
		<td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#c3daf9; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>
