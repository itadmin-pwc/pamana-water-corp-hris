<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("timesheet.trans.php");
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
<script type='text/javascript' src='timesheet_js.js'></script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;TERMINATED LOANS</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="6"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
              <input name='fileName' type='hidden' id='fileName' value="timesheet.php">            </td>
          </tr>
          
          <tr>
            <td class="gridDtlLbl">Loan Type</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><?  
					$inqTSObj->DropDownMenu(array('1'=>'SSS Loans','2'=>'Pag-ibig Loans','3'=>'Other Loans','4'=>'ALL'),'loanTypeAll',$loanTypeAll,$empDiv_dis); 
			  ?></td>
          </tr>
          <tr> 
            <td width="18%" class="gridDtlLbl">Division </td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td  width="81%" class="gridDtlVal"> 
              <? 					
								$arrDept = $inqTSObj->makeArr($inqTSObj->getDivArt($compCode),'divCode','deptDesc','');
								$inqTSObj->DropDownMenu($arrDept,'empDiv',$empDiv,$empDiv_dis);
							?>
              <input type="hidden" name="empNo" id="empNo">
              <input type="hidden" name="empName" id="empName"></td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Department </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"> <div id="deptDept"> 
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
            <td class="gridDtlVal"> <div id="deptSect"> 
                <input name="hide_empSect" type="hidden" id="hide_empSect" value="<? echo $empDept; ?>">
                <? 					
								$arrDept = $inqTSObj->makeArr($inqTSObj->getSectArt($compCode,$empDiv,$empDept),'sectCode','deptDesc','');
								$inqTSObj->DropDownMenu($arrDept,'empSect',$empSect,$empSect_dis);
							?>
              </div></td>
          </tr>
		  
		 
          <tr> 
		  
		  
		   <tr> 
            <td class="gridDtlLbl">From</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmTS.txttoDate.value);" class='inputs' name='txtfrDate' id='txtfrDate' maxLength='10' readonly size="10"/>
              <a href="#"><img name="imgfrDate" id="imgfrDate" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
          </tr>
		   <tr>
		     <td class="gridDtlLbl">To</td>
		     <td class="gridDtlLbl">:</td>
		     <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(document.frmTS.txtfrDate.value,document.frmTS.txtfrDate.id,this.value);" class='inputs' name='txttoDate' id='txttoDate' maxLength='10' readonly size="10"/>
               <a href="#"><img name="imgtoDate" id="imgtoDate" src="../../../images/cal_new.png" title="To Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
		     </tr>
        </table>
		<br>
<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="DailyLoansReport" id="DailyLoansReport" <? echo $searchTS2_dis; ?> value="Terminated Loans" onClick="TerminatedLoans();">
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