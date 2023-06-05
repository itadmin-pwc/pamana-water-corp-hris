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
            <input type="hidden" name="empDiv" id="empDiv" value="0">
            <input type="hidden" name="empDept" id="empDept" value="0">
            <input type="hidden" name="hide_empDept" id="hide_empDept" value="0">
            <input type="hidden" name="empSect" id="empSect" value="0">
            <input type="hidden" name="hide_empSect" id="hide_empSect" value="0">
            <input type="hidden" name="orderBy" id="orderBy" value="0">
            
            <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
           	 	<tr>
            		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Employee Certification of Government Contribution</td>
            	</tr>
            
            	<tr>
            		<td class="parentGridDtl" >
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
            				<tr > 
            					<td class="gridToolbar" colspan="6"> 
                                	<input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
            						<? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
            						<input name='fileName' type='hidden' id='fileName' value="emp_certification.php">            
                                </td>
            				</tr>
                            
                           
                            <tr> 
                                <td width="18%" class="gridDtlLbl">Emp. #</td>
                                <td width="1%" class="gridDtlLbl">:</td>
                                <td width="81%" class="gridDtlVal">
                                    <input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event,'searchTS2');"> 
                                </td>
                            </tr>
                            
                            <tr> 
                            	<td class="gridDtlLbl">Employee Name </td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal">
                                	<input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="text" size="25" maxlength="50">
                                	
                                </td>
                            </tr>
            
                            <tr > 
                                <td  class="gridToolbarWithColor" colspan="6">
                                    <center></center>
                                </td>
                            </tr>
                            
                            <tr> 
                                <td class="gridDtlLbl">Contribution Type </td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal" colspan="4"> 
                                <?  
                                    $inqTSObj->DropDownMenu(array('S'=>'Sss','PAG'=>'Pag-Ibig','PH'=>'Philhealth'),'conType',$conType,$conType_dis); 
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="gridDtlLbl">From</td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmTS.monthfr.value);" class='inputs' name='monthfr' id='monthfr' maxLength='10' readonly size="10"/>
                                  <a href="#"><img name="imgfrDate" id="imgfrDate" src="../../../images/cal_new.gif" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                            </tr>
                            
                             <tr>
                                <td class="gridDtlLbl">To</td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmTS.monthto.value);" class='inputs' name='monthto' id='monthto' maxLength='10' readonly size="10"/>
                                  <a href="#"><img name="imgtoDate" id="imgtoDate" src="../../../images/cal_new.gif" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                            </tr>
                            <!--<tr> 
                                <td class="gridDtlLbl">Month Coverage</td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"> 
                                	<div id="pdPay"> 
                                        <input name="hide_payPd" type="hidden" id="hide_payPd" value="<? echo $payPd; ?>">
                                        <? 					
                                            $arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType,"0"),'pdNumber','perMonth',''); // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
                                            $inqTSObj->DropDownMenu($arrPayPd,'monthfr',$monthfr,$monthfr_dis);
                                        ?>
                                        &nbsp;To&nbsp;
                                        <? 					
                                            $arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType,"0"),'pdNumber','perMonth',''); // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
                                            $inqTSObj->DropDownMenu($arrPayPd,'monthto',$monthto,$monthto_dis);
                                        ?>
                                	</div>
                                </td>
                            </tr>-->
            
                            
            			</table>
            			<br>
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
            				<tr>
            					<td>
                                    <CENTER>
                                    	<input type="button" name="btnempCert" id="btnempCert" <? echo $btnempCert_dis; ?> value="Generate Certification Report" onClick="valSearchTS(this.id);">
                                    </CENTER>
            					</td>
            				</tr>
            			</table> 
            		</td>
            	</tr> 
            
       </table>
   </form>
</BODY>
</HTML>
<script>
	Calendar.setup({
			  inputField  : "monthfr",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgfrDate"       // ID of the button
		}
	)	
	Calendar.setup({
			  inputField  : "monthto",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgtoDate"       // ID of the button
		}
	)	
</script>