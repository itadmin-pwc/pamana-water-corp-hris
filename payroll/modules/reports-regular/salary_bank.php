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
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Total Salay By Bank</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="6"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?></td>
          </tr>
		  
		  <tr>
		    <td class="gridDtlLbl">Branch</td>
		    <td class="gridDtlLbl">:</td>
		    <td class="gridDtlVal"><? 	
							$inqTSObj->DropDownMenu(
								$inqTSObj->makeArr($inqTSObj->getBranch($_SESSION['company_code'])
									,'brnCode','brnDesc','')
								,'empBrnCode',$cmbBranch,$cmBranch_dis
							);
						?></td>
		    </tr>
		  <tr> 
            <td width="18%" class="gridDtlLbl">Payroll Period</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="81%" class="gridDtlVal"> <div id="pdPay">
              <input name="hide_payPd" type="hidden" id="hide_payPd" value="<? echo $payPd; ?>">
              <? 					
								$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType),'pdSeries','pdPayable','');
								$inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis);
							?>
            </div></td>
          </tr>
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="Salary" id="Salary" class="inputs" <? echo $searchTS2_dis; ?> value="Total Salary by Bank" onClick="TotSalary();">
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