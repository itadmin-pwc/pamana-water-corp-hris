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
        	<input type="hidden" name="empNo" id="empNo" value="">
        	<input type="hidden" name="locType" id="locType" value="">
            <input type="hidden" name="empName" id="empName" value="">
            <input type="hidden" name="empSect" id="empSect" value="">
            <input type="hidden" name="hide_empSect" id="hide_empSect" value="0">
            <input type="hidden" name="hide_empDept" id="hide_empDept" value="0">
            <input type="hidden" name="orderBy" id="orderBy" value="0">
            <input type="hidden" name="payPd" id="payPd" value="1">
           
            <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
            	<tr>
            		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Payroll Summary By Department</td>
            	</tr>
            	
                <tr>
            		<td class="parentGridDtl" >
                  		<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
              				<tr > 
                				<td class="gridToolbar" colspan="6"> 
                                	<input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
								  	<? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
                                  	<input name='fileName' type='hidden' id='fileName' value="payregister.php">
                				</td>
              				</tr>
                            
                            <tr> 
                            	<td class="gridDtlLbl">Branch </td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal"> 
                              		<? 					
										$arrBranch = $inqTSObj->makeArr($inqTSObj->getBrnchArt($compCode),'brnCode','brnDesc','');
										$inqTSObj->DropDownMenu($arrBranch,'empBrnCode',$empBrnCode,$empBrnCode_dis);
									?>
                            	</td>
                            </tr>
                            
                            <tr> 
                            	<td class="gridDtlLbl">Division </td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal"> 
                              		<? 					
										$arrDept = $inqTSObj->makeArr($inqTSObj->getDivArt($compCode),'divCode','deptDesc','');
										$inqTSObj->DropDownMenu($arrDept,'empDiv',$empDiv,$empDiv_dis);
									?>
                            	</td>
                            </tr>
                            
                            <tr> 
                                <td class="gridDtlLbl">Department </td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"> 
                                	<div id="deptDept"> 
                                    <input name="hide_empDept" type="hidden" id="hide_empDept" value="<? echo $empDept; ?>">
                                    <? 					
										$arrDept = $inqTSObj->makeArr($inqTSObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','');
										$inqTSObj->DropDownMenu($arrDept,'empDept',$empDept,$empDept_dis);
									?>
                                     	
                                  	</div>
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
                           
            		</table>
            		<br>
                    <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
                      <tr>
                        <td>
                            <CENTER>
                                <input type="button" name="searchTS11" id="searchTS11" <? echo $searchTS11_dis; ?> value="Generate Payroll Register By Department (PDF)" onClick="valSearchTS(this.id);"><input type="button" name="searchTS12" id="searchTS12" <? echo $searchTS12_dis; ?> value="Generate Payroll Register By Department (EXCEL)" onClick="valSearchTS(this.id);">
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
                            <input name="mdsg" id="msdg" type="text" size="100" style="color:RED; background-color:#c3daf9; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
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