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
            		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Generate Monthly Remittance Textfile</td>
            	</tr>
            
            	<tr>
            		<td class="parentGridDtl" >
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
            				<tr > 
            					<td class="gridToolbar" colspan="6"> 
                                	<input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
            						<? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
            						<input name='fileName' type='hidden' id='fileName' value="generate_rem_textfile.php">            
                                </td>
            				</tr>
                           
                            <input type="hidden" name="empNo" id="empNo" value="">
                            <input type="hidden" name="empName" id="empName" value="">
                            <input type="hidden" name="empSect" id="empSect" value="">
                            <input type="hidden" name="empDiv" id="empDiv" value="">
                            <input type="hidden" name="empDept" id="empDept" value="">
                            <input type="hidden" name="empPos" id="empPos" value="">
                            <input type="hidden" name="hide_empSect" id="hide_empSect" value="0">
                            <input type="hidden" name="hide_empDept" id="hide_empDept" value="0">
                            <input type="hidden" name="orderBy" id="orderBy" value="0">

                            
                            <tr> 
                                <td class="gridDtlLbl" width="20%">Company </td>
                                <td class="gridDtlLbl" width="1%">:</td>
                                <td class="gridDtlVal" width="79%"> 
									<? 					
                                        $arrComp = $inqTSObj->makeArr($inqTSObj->getCompany(""),'compCode','compName','');
                                        $inqTSObj->DropDownMenu($arrComp,'selComp',$selComp,$selComp_dis);
                                    ?>            
                                </td>
                            </tr>
            
                          
                             <tr> 
                                <td class="gridDtlLbl">Remittance Type </td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal" colspan="3"> 
                                <?  
                                    $inqTSObj->DropDownMenu(array('S'=>'Sss','SL'=>'Sss Loan','PAG'=>'Pag-Ibig','PAGL'=>'Pag-Ibig Loan','PH'=>'Philhealth'),'topType',$topType,$topType_dis); 
                                ?>
                                </td>
                            </tr>
                            
                            <tr> 
                                <td class="gridDtlLbl">Payroll Period (Monthly)</td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"> 
                                	<div id="pdPay"> 
                                        <input name="hide_payPd" type="hidden" id="hide_payPd" value="<? echo $payPd; ?>">
                                        <? 					
                                            $arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType,"0"),'pdNumber','perMonth',''); // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
                                            $inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis);
                                        ?>
                                	</div>
                                </td>
                            </tr>
            
                           
            			</table>
            			<br>
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
            				<tr>
            					<td>
                                    <CENTER>
                                    	<input type="button" name="searchTS2" id="searchTS2" <? echo $searchTS2_dis; ?> value="Generate Contribution Report" onClick="procRemTextfile(this.id);">
                                    </CENTER>
            					</td>
            				</tr>
            			</table> 
                        <div id="caption" align="center">        
                        </div>		
            		</td>
            	</tr> 
            	
       </table>
   </form>
</BODY>
</HTML>