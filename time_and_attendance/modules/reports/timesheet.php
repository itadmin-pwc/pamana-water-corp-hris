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
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Timesheet Prooflist</td>
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
            </span></td>
          </tr>
          

          <tr>
            <td width="18%" class="gridDtlLbl">Branch</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 	
			$sqlBranch = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' and brnCode IN (Select brnCode from tblTK_UserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}') order by brnDesc";				
			$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($sqlBranch));
								$arrBranch = $inqTSObj->makeArr($arrBranch,'brnCode','brnDesc','');
								$inqTSObj->DropDownMenu($arrBranch,'branch',$empDiv,$cmbtable_dis .' onChange="Checkgroup(this.value)"');
							?>            </td>
            </tr>
          <tr>
            <td class="gridDtlLbl">Pay Group </td>
            <td class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"><span class="gridDtlVal style5">
              <? $inqTSObj->DropDownMenu(
				array('','1'=>'Group 1'),'cmbGroup',0,'class="inputs" style="width:100px;" disabled onChange="Checkgroup(document.frmTS.branch.value)"'); ?>
            </span></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">Division </td>
            <td class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"><span class="gridDtlVal style5">
              <? 
			  $arrDiv = $inqTSObj->getDivArt($_SESSION['company_code']);
			  $div = $inqTSObj->makeArr($arrDiv,'divCode','deptDesc','');
			  $inqTSObj->DropDownMenu($div,'cmbDivision','','class="inputs" style="width:300px;" disabled onChange="getDept(this.value);"');
			  ?>
            </span></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">Department </td>
            <td class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"><div id='divDept' name='divDept'>
              <? $inqTSObj->DropDownMenu(
				array('')
				,'cmbDepartment',0,'class="inputs" style="width:200px;" disabled'); ?>
            </div></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">Pay Category</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"><span class="gridDtlVal style5">
              <? $inqTSObj->DropDownMenu(
				array('','1'=>'Executive',
				'2'=>'Confidential',
				'3'=>'Non Confidential',
				'9'=>'Resigned'
				)
				,'cmbCategory',0,'class="inputs" $empNo_dis style="width:100px;"'); ?>
            </span></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">Payroll Period</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"><div id='divpayPd' name='divpayPd'>
              <? $inqTSObj->DropDownMenu(
				array('')
				,'cmbpayPd',0,'class="inputs" style="width:100px;" disabled'); ?>
            </div></td>
          </tr>
          <tr> 
            <td width="18%" class="gridDtlLbl">Emp. #</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"><input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);"> 
              <? //echo $option_menu; ?>            </td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Employee Name </td>
            <td class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"><input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="text" size="25" maxlength="50" onKeyPress="getEmpSearch(event);"></td>
          </tr>                  
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="salary" class="inputs" id="salary" <? echo $searchTS4_dis; ?> value="Timesheet Prooflist" onClick="TSProoflist();">
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
