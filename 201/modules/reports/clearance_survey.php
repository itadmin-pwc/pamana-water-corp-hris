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
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
</HEAD>
	<BODY>
<form name="frmTS" id="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table  cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Clearance &amp; Survey Report</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="6"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
              <input name='fileName' type='hidden' id='fileName' value="clearance_survey.php">
              <input name='orderBy' type='hidden' id='orderBy'>
              <input name='txtfrDate' type='hidden' id='txtfrDate'>
                <input name='txttoDate' type='hidden' id='txttoDate'>
                <input name="hide_empDept" type="hidden" id="hide_empDept" value="<? echo $empDept; ?>">
            <input name="hide_empSect" type="hidden" id="hide_empSect" value="<? echo $empDept; ?>">
            <font class="byOrder">
            <input name='empDiv' type='hidden' id='empDiv'>
            <input name="empDept" type="hidden" id="empDept">
            <input name="empSect" type="hidden" id="empSect">
            </font></td>
          </tr>
          
          <tr> 
            <td width="18%" class="gridDtlLbl">Emp. #</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);"> 
              <? //echo $option_menu; ?>            </td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Employee Name </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="text" size="40" maxlength="50" onKeyPress="getEmpSearch(event);"></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">Report Type</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><font class="byOrder">
              <?
			  $inqTSObj->DropDownMenu(array(''=>'','1'=>'Separation Clearance Form','2'=>'Exit Interview Survey'),'cmbType',$_GET['cmbType'],'class="inputs"'); 
			  ?>
            </font></td>
            </tr>       
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="salary" id="salary" class="inputs" <? echo $searchTS4_dis; ?> value="  PRINT SELECTED REPORT  " onClick="CS();">
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
