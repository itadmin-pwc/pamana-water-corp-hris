<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("ts_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("ts.trans.php");
##################################################
if ($_GET['code']=="") {
	$code = $_POST['code'];
} else {
	$code = $_GET['code'];
}

?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/reports.css');</style>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='ts.js'></script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Timesheet Summary</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"> <input  name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'>
              <input name='fileName' type='hidden' id='fileName' value="timesheet.php">
              <span class="gridDtlVal">
              <input value="" type='hidden'  class='inputs' name='txtfrDate' id='txtfrDate' maxLength='10' readonly size="10"/>
              <input value="" type='hidden'   class='inputs' name='txttoDate' id='txttoDate' maxLength='10' readonly size="10"/>
              <input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="hidden" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);">
              <input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="hidden" size="25" maxlength="50" onKeyPress="getEmpSearch(event);">
              <input class="inputs" name="cmbDivision" id="cmbDivision" type="hidden" size="25" maxlength="50">
              <input class="inputs" name="cmbDepartment" id="cmbDepartment" type="hidden" size="25" maxlength="50">
            </span></td>
          </tr>
          

      
          <tr>
            <td class="gridDtlLbl">Group</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><span class="gridDtlVal style5">
              <? $inqTSObj->DropDownMenu(
				array('','1'=>'Group 1'),'cmbGroup',0,'class="inputs" style="width:100px;"  onChange="Checkgroup(0001)"'); ?>
            </span></td>
            </tr> 
          <tr>
            <td class="gridDtlLbl">Payroll Period</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><div id='divpayPd' name='divpayPd'>
                <? $inqTSObj->DropDownMenu(
				array('')
				,'cmbpayPd',0,'class="inputs" style="width:100px;"'); ?>
            </div></td>
            </tr>
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="tssum" id="tssum" <? echo $searchTS4_dis; ?> value="TIMESHEET SUMMARY" onClick="TSSummary();">
					  </CENTER>
					  	<div id="caption" align="center">        </div>	
					</td>
				  </tr>
			  </table> 
	</td>
	</tr> 
	<tr > 

		<!-- <td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#c3daf9; height:18px; text-align: center;  border:0px solid;" value="<? //echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td> -->
	</tr>
</table>
</form>
</BODY>
</HTML>
<script type="text/javascript">




/*	function valDate(valStart,idStart,valEnd) {
		var empInputs = $('frmTS').serialize(true);
		var todayDate = new Date();
		var parseStart = Date.parse(valStart);
		var parseEnd = Date.parse(valEnd);
		var parseTodayDate = Date.parse(todayDate);
		if (empInputs['txt'] != "0.00") {
			if(parseStart > parseEnd) {
				alert("Invalid Date.");
				document.getElementById(idStart).value="";
				return false;
			}
	}*/
	
	Calendar.setup({
			  inputField  : "txtfrDate",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgfrDate"       // ID of the button
		}
	)	
	Calendar.setup({
			  inputField  : "txttoDate",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgtoDate"       // ID of the button
		}
	)	
</script>
