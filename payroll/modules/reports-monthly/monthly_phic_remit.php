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
            		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Monthly Philhealth Remittance Text File</td>
            	</tr>
            
            	<tr>
            		<td class="parentGridDtl" >
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                        	 <input type="hidden" name="empNo" id="empNo" value="">
                            <input type="hidden" name="empName" id="empName" value="">
                            <input type="hidden" name="empSect" id="empSect" value="">
                            <input type="hidden" name="empDiv" id="empDiv" value="">
                            <input type="hidden" name="empDept" id="empDept" value="">
                            <input type="hidden" name="hide_empSect" id="hide_empSect" value="0">
                            <input type="hidden" name="hide_empDept" id="hide_empDept" value="0">
                            <input type="hidden" name="orderBy" id="orderBy" value="0">
                            
                        	 <tr > 
            					<td class="gridToolbar" colspan="6"> 
            						<input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
            						<? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
            						<input name='fileName' type='hidden' id='fileName' value="tax.php">            
           				 		</td>
            				</tr>
                            <!--
                          
            
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
                            	<td class="gridDtlLbl">Section </td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal"> 
                                	<div id="deptSect"> 
                            			<input name="hide_empSect" type="hidden" id="hide_empSect" value="<? echo $empDept; ?>">
                           		 		<? 					
                            				$arrDept = $inqTSObj->makeArr($inqTSObj->getSectArt($compCode,$empDiv,$empDept),'sectCode','deptDesc','');
                            				$inqTSObj->DropDownMenu($arrDept,'empSect',$empSect,$empSect_dis);
										?>
                            		</div>
                                </td>
                            </tr>
            
                            <tr> 
                            	<td width="18%" class="gridDtlLbl">Emp. #</td>
                            	<td width="1%" class="gridDtlLbl">:</td>
                            	<td width="81%" class="gridDtlVal"><input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly('searchTS', event);"> 
                            	<? //echo $option_menu; ?>            </td>
                            </tr>
                            
                            <tr> 
                            	<td class="gridDtlLbl">Employee Name </td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal"><input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="text" size="25" maxlength="50" onKeyPress="getEmpSearch(event);"></td>
                            </tr>
            				-->
                            <tr>
                              <td class="gridDtlLbl">ME-5 #/OR #</td>
                              <td class="gridDtlLbl">:</td>
                              <td class="gridDtlVal"><input type="text" name="txtOR" id="txtOR"></td>
                            </tr>
                            <tr>
                              <td class="gridDtlLbl">Paid Amount</td>
                              <td class="gridDtlLbl">:</td>
                              <td class="gridDtlVal"><input type="text" name="txtAmount" id="txtAmount"></td>
                            </tr>
                            <tr>
                              <td class="gridDtlLbl">Date Paid</td>
                              <td class="gridDtlLbl">:</td>
                              <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(document.frmTS.txtfrDate.value,document.frmTS.txtfrDate.id,this.value);" class='inputs' name='txtDate' id='txtDate' maxLength='10' readonly size="10"/>
                              <a href="#"><img name="imgtoDate" id="imgtoDate" src="../../../images/cal_new.png" title="To Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                            </tr>
                            <tr> 
            					<td width="23%" class="gridDtlLbl">Payroll Period </td>
            					<td width="3%" class="gridDtlLbl">:</td>
            					<td width="74%" class="gridDtlVal"> 
                                	<div id="pdPay"> 
            							<input name="hide_payPd" type="hidden" id="hide_payPd" value="<? echo $payPd; ?>">
										<? 					
                                            $arrPayPd = $inqTSObj->makeArr($inqTSObj->getMontlyPayPeriod($compCode,$groupType,$catType,"0"),'pdNumber','perMonth',''); // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
                                            $inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis);
                                        ?>
            						</div>
                                </td>
            				</tr>
            				<!--
                            <tr> 
                            	<td class="gridDtlLbl">Order By</td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal"> 
                            		<font class="byOrder"> 
										<?
                                            $inqTSObj->DropDownMenu(array('1'=>'EMPLOYEE NAME','2'=>'EMPLOYEE NUMBER','3'=>'DEPARTMENT'),'orderBy',$orderBy,$orderBy_dis); 
                                        ?>
                            		</font>			
                               	</td>
                            </tr>
                            -->
            			</table>
            <br>
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
            				<tr>
            					<td>
            						<CENTER>
            							<input type="button" name="MonthlyGovt" class="inputs" id="MonthlyGovt" <? echo $searchTS_dis; ?> value="Generate Text File" onClick="genTextFile();">
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

function genTextFile() {
	var orNo=document.frmTS.txtOR.value;
	var amt=document.frmTS.txtAmount.value;
	var date=document.frmTS.txtDate.value;
	var payPd=document.frmTS.payPd.value;
	if (orNo=='') {
		alert("Invalid OR No.");
		return false;
	}
	if (amt=='') {
		alert("Invalid Amount Paid");
		return false;
	}
	if (date=='') {
		alert("Invalid Date Paid");
		return false;
	}
	if (payPd=="" || payPd<0 || payPd=="0" ) {
		alert("Invalid Payroll Period.");
		return false;
	}
	
	new Ajax.Request(
	  'timesheet_ajax.php?inputId=phictxtFile&payPd='+payPd+'&orNo='+orNo+'&amt='+amt+'&date='+date,
	  {
		 asynchronous : true,     
		 onComplete   : function (req){
			eval(req.responseText);
		 }
	  }
	);
}

Calendar.setup({
		  inputField  : "txtDate",      // ID of the input field
		  ifFormat    : "%m/%d/%Y",          // the date format
		  button      : "imgtoDate"       // ID of the button
	}
)
</script>