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
<script type="text/javascript">
function inq(){
	$('cmbGroup').disabled=false;
	$('txtfrDate').disabled=false;
	$('txttoDate').disabled=false;	
	$('btnPrintEarningsProoflist').disabled=false;	
}
function ref(){
	$('cmbGroup').disabled=true;
	$('txtfrDate').disabled=true;
	$('txttoDate').disabled=true;
	$('btnPrintEarningsProoflist').disabled=true;	
	$('txtfrDate').value="";
	$('txttoDate').value="";	
	$('cmbGroup').value=0;	
}
</script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Earnings Adjustment Report</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"><input type="radio" id="new" name="action" onClick="inq();"><label for="new">Inquire</label>&nbsp;&nbsp; <input type="radio" id="refresh" name="action" onClick="ref();"><label for="refresh">Refresh</label></td>
          </tr>
          <tr>
            <td width="13%" class="gridDtlLbl">Group</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="86%" colspan="3" class="gridDtlVal"><span class="gridDtlVal style5">
              <? $inqTSObj->DropDownMenu(
				array('','1'=>'Group 1'),'cmbGroup',0,'class="inputs" disabled style="width:100px;"'); ?>
            </span></td>
            </tr>
          <tr>
            <td class="gridDtlLbl">Date From</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><input value="" type='text'  class='inputs' name='txtfrDate' id='txtfrDate' maxLength='10' readonly size="10"/></td>
            </tr>
          <tr>
            <td class="gridDtlLbl">Date To</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><input value="" type='text'   class='inputs' name='txttoDate' id='txttoDate' maxLength='10' readonly size="10"/></td>
            </tr>
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="btnPrintEarningsProoflist" class="inputs" id="btnPrintEarningsProoflist" value="Print Earnings Adjustment Prooflist" onClick="Earnings_Adjustment();" disabled>
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
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#fff; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>
<script type="text/javascript">
	Calendar.setup({
			  inputField  : "txtfrDate",      // ID of the input field
			  ifFormat    : "%Y-%m-%d",          // the date format
			  button      : "imgfrDate"       // ID of the button
		}
	)	
	Calendar.setup({
			  inputField  : "txttoDate",      // ID of the input field
			  ifFormat    : "%Y-%m-%d",          // the date format
			  button      : "imgtoDate"       // ID of the button
		}
	)	
</script>
