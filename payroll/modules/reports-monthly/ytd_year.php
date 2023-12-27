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
					<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;YTD for the Year</td>
				</tr>
	
    			<tr>
					<td class="parentGridDtl" >
			  			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                            <tr > 
                                <td class="gridToolbar" colspan="7"> 
                                    <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
                                    <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
                                    <input name='fileName' type='hidden' id='fileName' value="tax.php">                                
									<input name="hide_empDept" type="hidden" id="hide_empDept" value="<? echo $empDept; ?>">
                                    <input name="hide_empSect2" type="hidden" id="hide_empSect2" value="<? echo $empDept; ?>">
                                    <input name="empNo" type="hidden" id="empNo" value="<? echo $empDept; ?>">
                                    <input name="empName" type="hidden" id="empName" value="<? echo $empDept; ?>">
                                    <input name="empDept" type="hidden" id="empDept">
                                    <input name="empDiv" type="hidden" id="empDiv">
                                    <input name="empSect" type="hidden" id="empSect" value="<? echo $empDept; ?>">
                                    <input name="hide_empSect" type="hidden" id="hide_empSect"></td>
                            </tr>
                            


		  <tr> 
            <td width="18%" class="gridDtlLbl">Year</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="162%" colspan="2" class="gridDtlVal"> <div id="pdPay"> 
                <input name="hide_payPd" type="hidden" id="hide_payPd" value="<? echo $payPd; ?>">
                <? 					
								$arrPayPd = $inqTSObj->makeArr($inqTSObj->GetYear(),'pdYear','pdYear',''); 
								$inqTSObj->DropDownMenu($arrPayPd,'payPd',date('Y'),$payPd_dis);
							?>
                <input name="orderBy" type="hidden" id="orderBy">
                <input name="report_type" type="hidden" id="report_type">
            </div></td>
          </tr>
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="yearly_ytd" id="yearly_ytd" class="inputs" <? echo $searchTS_dis; ?> value="  PDF  " onClick="YTDRepType('pdf');valSearchTS(this.id);">
                <input type="button" name="yearly_ytd" id="yearly_ytd" class="inputs" <? echo $searchTS_dis; ?> value="EXCEL" onClick="YTDRepType('excel');valSearchTS(this.id);">
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