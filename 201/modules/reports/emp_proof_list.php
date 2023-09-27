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
<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
</HEAD>
<BODY>
<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;New Employee Proof List</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
              <input name='fileName' type='hidden' id='fileName' value="salary.php?code=<?=$code?>"></td>
          </tr>
          
          <tr>
            <td width="18%" class="gridDtlLbl">Status</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><font class="byOrder">
              <?
					$inqTSObj->DropDownMenu(array(''=>'','R'=>'RELEASED','H'=>'HELD'),'ReportType',$orderBy,$orderBy_dis); 
			  ?>
            </font></td>
            <td class="gridDtlVal">&nbsp;</td>
          </tr>
          <tr>
            <td class="gridDtlLbl">Group </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><font class="byOrder">
              <?
			  $inqTSObj->DropDownMenu(array('0'=>'','1'=>'Group 1'),'cmbGrp',$orderBy,$orderBy_dis);  ?>
            </font></td>
            <td class="gridDtlVal">&nbsp;</td>
          </tr>
          <tr>
            <td class="gridDtlLbl">From</td>
            <td class="gridDtlLbl">:</td>
            <td width="23%" class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmTS.txttoDate.value);" class='inputs' name='txtfrDate' id='txtfrDate' maxLength='10' readonly size="10"/>
              <a href="#"><img name="imgfrDate" id="imgfrDate" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
            <td width="58%" class="gridDtlVal">&nbsp;</td>
          </tr>
          <tr>
            <td class="gridDtlLbl">To</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(document.frmTS.txtfrDate.value,document.frmTS.txtfrDate.id,this.value);" class='inputs' name='txttoDate' id='txttoDate' maxLength='10' readonly size="10"/>
              <a href="#"><img name="imgtoDate" id="imgtoDate" src="../../../images/cal_new.png" title="To Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
            <td class="gridDtlVal">&nbsp;</td>
            </tr>
          <tr>
            <td class="gridDtlLbl">Division </td>
            <td class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><? 					
								$arrDept = $inqTSObj->makeArr($inqTSObj->getDivArt($compCode),'divCode','deptDesc','');
								$inqTSObj->DropDownMenu($arrDept,'empDiv',$empDiv,$empDiv_dis);
							?>            </td>
            </tr>            
          <tr> 
            <td class="gridDtlLbl">Department </td>
            <td class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"> <div id="deptDept"> 
                <input name="hide_empDept" type="hidden" id="hide_empDept" value="<? echo $empDept; ?>">
                <? 					
								$arrDept = $inqTSObj->makeArr($inqTSObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','');
								$inqTSObj->DropDownMenu($arrDept,'empDept',$empDept,$empDept_dis);
							?>
              </div></td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Section </td>
            <td class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"> <div id="deptSect"> 
                <input name="hide_empSect" type="hidden" id="hide_empSect" value="<? echo $empDept; ?>">
                <? 					
								$arrDept = $inqTSObj->makeArr($inqTSObj->getSectArt($compCode,$empDiv,$empDept),'sectCode','deptDesc','');
								$inqTSObj->DropDownMenu($arrDept,'empSect',$empSect,$empSect_dis);
							?>
                <input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="hidden" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);">
                <input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="hidden" size="25" maxlength="50" onKeyPress="getEmpSearch(event);">
                <input class="inputs" name="orderBy" id="orderBy" <? echo $empName_dis; ?> type="hidden" size="25" maxlength="50" onKeyPress="getEmpSearch(event);">
            </div></td>
          </tr>          
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="new_emp" id="new_emp" class="inputs" <? echo $searchTS4_dis; ?> value="Print New Employee Proof List to PDF" onClick="valSearchTS(this.id);">
                <input type="button" name="new_emp_excel" class="inputs" id="new_emp_excel" <? echo $searchTS4_dis; ?> value="Export New Employee Proof List to Excel" onClick="valSearchTS(this.id);">
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
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:white; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
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