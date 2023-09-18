<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	04/06/2011
	Function		:	Employee Listing of Minimum Wage Tag
*/

##################################################
	session_start(); 
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("common_obj.php");
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	include("common_trans.php");
##################################################
?>
<HTML>
	<HEAD>
		<TITLE>
			<?=SYS_TITLE?>
		</TITLE>
		<style>@import url('../../../payroll/style/main_emp_loans.css');</style>
        <script type='text/javascript' src='../../../includes/jSLib.js'></script>
        <script type='text/javascript' src='../../../includes/prototype.js'></script>
       <!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
        <STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
        <!--end calendar lib-->
        <script type='text/javascript' src='../../../201/modules/reports/common_js.js'></script>
    </HEAD>
	<BODY>
        <form name="frmTS" method="post" action="<? echo $_SERVER['../../../payroll/modules/special-reports/PHP_SELF']; ?>">
            <input type="hidden" name="empNo" id="empNo" value="">
            <input type="hidden" name="empName" id="empName" value="">
            <input type="hidden" name="empSect" id="empSect" value="">
            <input type="hidden" name="empDiv" id="empDiv" value="">
            <input type="hidden" name="empDept" id="empDept" value="">
            <input type="hidden" name="empPos" id="empPos" value="">
            <input type="hidden" name="hide_empSect" id="hide_empSect" value="0">
            <input type="hidden" name="hide_empDept" id="hide_empDept" value="0">
            <input type="hidden" name="orderBy" id="orderBy" value="0">
            
            <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
           	 	<tr>
            		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Generate Employee List of Updated Minimum Wage Tag</td>
            	</tr>
            
            	<tr>
            		<td class="parentGridDtl" >
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
            				<tr > 
            					<td class="gridToolbar" colspan="6"> 
                                	<input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
            						<? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
            						<input name='fileName' type='hidden' id='fileName' value="blacklist.php">            
                                </td>
            				</tr>
                            
                            <tr> 
                            	<td class="gridDtlLbl">Company </td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal"> 
                              		<? 					
										$arrBranch = $inqTSObj->makeArr($inqTSObj->getCompany(""),'compCode','compName','');
										$inqTSObj->DropDownMenu($arrBranch,'empBrnCode',$empBrnCode,$empBrnCode_dis);
									?>
                            	</td>
                            </tr>
                            
                            
                            <tr > 
                                <td  class="gridToolbarWithColor" colspan="6">
                                    <center></center>
                                </td>
                            </tr>
                            
                           
                            <tr>
                                <td class="gridDtlLbl">Date Updated</td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal">
                                  From Date
                                  <input value="" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmTS.monthfr.value);" class='inputs' name='monthfr' id='monthfr' maxLength='10' readonly size="10"/>
                                  <a href="#"><img name="imgfrDate" id="imgfrDate" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a>
                           		  To Date
                                  <input value="" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmTS.monthto.value);" class='inputs' name='monthto' id='monthto' maxLength='10' readonly size="10"/>
                                  <a href="#"><img name="imgtoDate" id="imgtoDate" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                            </tr>
                            
                            
                            
            			</table>
            			<br>
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
            				<tr>
            					<td>
                                    <CENTER>
                                    	<input type="button" name="btnempra1" id="btnempra1" <? echo $btnempra1_dis; ?> value="Generate Emp. Listing Report" onClick="return generatera1_validate('listMinWage');">
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