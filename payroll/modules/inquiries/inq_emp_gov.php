<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_loans_obj.php");
$maintEmpLoanObj = new inqEmpLoanObj();
$sessionVars = $maintEmpLoanObj->getSeesionVars();
$maintEmpLoanObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$loanCode = 0; ///// 1=sss , 2=pagibig , 3=company
include("inq_emp_loans.trans.php");

##################################################
?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/main_emp_loans.css');</style>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='inq_emp_loans_js.js'></script>
</HEAD>
	<BODY>
<form name="frmEmpLoan" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		<td class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Employee Gov't Contribution</td>
	</tr>
	<tr>
		<td class="parentGridDtl" ><TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="3"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_loan; ?>&nbsp;&nbsp;<? echo $refresh_loan; ?>
              <input name='updateFlag' type='hidden' id='updateFlag'> 
              <input name='fileName' type='hidden' id='fileName' value="inq_emp_gov.php">            </td>
          </tr>
          <tr> 
            <td width="18%" class="gridDtlLbl">Emp. #</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="81%" class="gridDtlVal"><input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);"> 
              <? //echo $option_menu; ?>            </td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Employee Name </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="text" size="25" maxlength="50" onKeyPress="getEmpSearch(event);"></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">From</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmEmpLoan.txttoDate.value);" class='inputs' name='txtfrDate' id='txtfrDate' maxLength='10' readonly size="10"/>
              <a href="#"><img name="imgfrDate" id="imgfrDate" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">To</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(document.frmEmpLoan.txtfrDate.value,document.frmEmpLoan.txtfrDate.id,this.value);" class='inputs' name='txttoDate' id='txttoDate' maxLength='10' readonly size="10"/>
              <a href="#"><img name="imgtoDate" id="imgtoDate" src="../../../images/cal_new.png" title="To Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
          </tr>
          
          
        </table>
<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
							<input type="button" name="searchGovDed" id="searchGovDed" value="Search" <? echo $searchLoan_dis; ?> onClick="valSearchGovDed();">	
						</CENTER>
					</td>
				  </tr>
	    </table> </td>
	</tr> 
	<tr> 
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
<script>
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