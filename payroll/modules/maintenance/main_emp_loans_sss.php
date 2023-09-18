<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("main_emp_loans_obj.php");
$maintEmpLoanObj = new maintEmpLoanObj();
$sessionVars = $maintEmpLoanObj->getSeesionVars();
$maintEmpLoanObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$loanCode = 1; ///// 1=sss , 2=pagibig , 3=company
include("main_emp_loans.trans.php");
##################################################
?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/main_emp_loans.css');</style>
<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>	
<STYLE TYPE="text/css" MEDIA="screen">
@import url("../../../includes/calendar/calendar-blue.css");.style3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
}
</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='main_emp_loans_js.js'></script>
</HEAD>
	<BODY>
<form name="frmEmpLoan" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		<td class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;SSS Loans Set Up		</td>
	</tr>
	<tr>
		<td class="parentGridDtl" ><table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr >
            <td class="gridToolbar" colspan="6"><input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>">
                <!--<FONT class="ToolBarseparator">|</font>-->
                <? echo $new_loan; ?>&nbsp;&nbsp;<? echo $edit_loan; ?>&nbsp;&nbsp; <? echo $delete_loan; ?>&nbsp;&nbsp;<? echo $refresh_loan; ?>
                <input name='updateFlag' type='hidden' id='updateFlag'>
                <input name='fileName' type='hidden' id='fileName' value="main_emp_loans_sss.php">
                <font class="ToolBarseparator">|</font> <a href="#" <? echo $printLoc; ?>> <img src="../../../images/<? echo $printImgFileName; ?>" align="absbottom" class="actionImg" title="Print Employee Loan"></a> <img onClick="<?=$loanSeries?>" style="cursor:pointer;" title="View Employee Detailed Loan Payments" class="actionImg" src="../../../images/<?=$viewDetailed;?>" align="absbottom"></td>
          </tr>
          <tr>
            <td width="24%" class="gridDtlLbl">Emp. #</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="75%" class="gridDtlVal"><input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);">
                <? //echo $option_menu; ?>            </td>
          </tr>
          <tr>
            <td class="gridDtlLbl">Employee Name </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="text" size="25" maxlength="50" onKeyPress="getEmpSearch(event);"></td>
          </tr>

          <tr >
            <td  class="gridToolbarWithColor" colspan="6"><center>
            </center></td>
          </tr>
          <tr>
            <td colspan="6" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                    <tr class="childGrid">
                      <td width="48%" class="gridDtlLbl">Loan Type </td>
                      <td width="2%" class="gridDtlLbl">:</td>
                      <td width="50%" class="gridDtlVal"><span id="sploantype">
                        <?  
								if ($empNo>"" && $option_menu=="edit_loan") {
									$arrLoan = $maintEmpLoanObj->makeArr($maintEmpLoanObj->getEmpLoanListArt($compCode,$empNo,$loanCode),'loanTypeCdRefNo','loanDescRefNo','');
									$maintEmpLoanObj->DropDownMenu($arrLoan,'loanType',$loanType,$loanType_dis . ' style="width:172px;"');
								} else {
									$arrLoan = $maintEmpLoanObj->makeArr($maintEmpLoanObj->getLoanTypeListWil($compCode,$loanCode,$empNo),'lonTypeCd','lonTypeDesc','');
									$maintEmpLoanObj->DropDownMenu($arrLoan,'loanType',$loanType,$loanType_dis . ' style="width:172px;"'); 
								}
								
							?>
                      </span> </td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">Loan Ref. No. </td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanRefNo" id="loanRefNo" value="<? echo $loanRefNo; ?>" <? echo $loanRefNo_dis; ?> type="text" size="25" maxlength="20"  onChange="valRefNo();" /><input type="hidden" name="oldLoanRef_No" id="oldLoanRef_No" value="<?=$loanRefNo?>"></td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">Loan Amt (Principal) </td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanPrinc" id="loanPrinc" value="<? echo $loanPrinc; ?>" <? echo $loanPrinc_dis; ?> type="text" size="25" maxlength="10" onKeyPress="return isNumberInput2Decimal(this, event);" onChange="val2DecNo(this.value,this.id); valPrincToInt();" /></td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">Loan Amt Inclusive of Interest </td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanInt" id="loanInt" value="<? echo $loanInt; ?>" <? echo $loanInt_dis; ?> type="text" size="25" maxlength="10" onKeyPress="return isNumberInput2Decimal(this, event);" onChange="val2DecNo(this.value,this.id); valPrincToInt();" /></td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">Date Granted</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="dtGranted" id="dtGranted" value="<? echo $dtGranted; ?>" readonly type="text" size="25" maxlength="50" />
                        <a href="#"><img name="imgdtGranted" id="imgdtGranted" type="image" src="../../../images/cal_new.png" title="Date Granted" style="cursor: pointer;position:relative;top:3px;border:none;" /></a></td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">Start Date of Deduction</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanStart" id="loanStart" value="<? echo $loanStart; ?>" readonly type="text" size="25" maxlength="50" />
                        <a href="#"><img name="imgloanStart" id="imgloanStart" type="image" src="../../../images/cal_new.png" title="Start Date" style="cursor: pointer;position:relative;top:3px;border:none;" /></a> </td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">End Date of Deduction</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanEnd" id="loanEnd" value="<? echo $loanEnd; ?>" readonly type="text" size="25" maxlength="50" />
                        <a href="#"><img name="imgloanEnd" id="imgloanEnd" type="image" src="../../../images/cal_new.png" title="End Date" style="cursor: pointer;position:relative;top:3px;border:none;" /></a> </td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">Period of Deduction</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><font class="periodLoan">
                        <?
								if (($loanPeriod=="" || $loanPeriod<=0) && ($loanType=="" || $loanType<=0) && ($empNo>"" || $empNo<=0) && ($option_menu=="new_loan")) { $loanPeriod=3; }
								$maintEmpLoanObj->DropDownMenu(array('','1'=>'1st Period','2'=>'2nd Period','3'=>'Both','4'=>'Hold Deduction'),'loanPeriod',$loanPeriod,'class="inputs"'); 
							?>
                      </font> </td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">Total No. of Payments</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanTerms" id="loanTerms" value="<? echo $loanTerms; ?>" <? echo $loanTerms_dis; ?> type="text" size="25" maxlength="4" onKeyPress="return isNumberInput(this, event);" onChange="valPrincToInt()" /></td>
                    </tr>
                </table></td>
                <td valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                    <tr class="childGrid">
                      <td height="20" colspan="3" class="gridDtlVal"><strong>Amount of Deduction per Schedule</strong></td>
                      </tr>
                    <tr class="childGrid">
                      <td width="48%" class="gridDtlLbl">&nbsp;Deduction (Exclusive of Interest) </td>
                      <td width="2%" class="gridDtlLbl">:</td>
                      <td width="50%" class="gridDtlVal"><input class="inputs" name="loanDedEx" id="loanDedEx"  value="<? echo $loanDedEx; ?>" <? echo $loanDedEx_dis; ?> type="text" size="25" maxlength="10" /></td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">&nbsp;Deduction (Inclusive of Interest) </td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanDedIn" id="loanDedIn"  value="<? echo $loanDedIn; ?>" <? echo $lloanDedIn_dis; ?> type="text" size="25" maxlength="10" onKeyPress="return isNumberInput2Decimal(this, event);" /></td>
                    </tr>
                    
                    <tr class="childGrid">
                      <td height="20" colspan="3" ><span class="style3">Payments</span></td>
                      </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">&nbsp;Total Amt of Payments to-date</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanPay" id="loanPay" value="<? echo $loanPay; ?>" <? echo $loanPay_dis; ?> type="text" size="25" maxlength="10" onKeyPress="return isNumberInput2Decimal(this, event);" onChange="val2DecNo(this.value,this.id); valPrincToInt();" /></td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">&nbsp;No. of Payments Made</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanPayNo" id="loanPayNo" value="<? echo $loanPayNo; ?>" <? echo $loanPayNo_dis; ?> type="text" size="25" maxlength="4" onKeyPress="return isNumberInput(this, event);" onChange="valNullVal(this.value,this.id);" /></td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">&nbsp;Current Loan Balance</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanBal" id="loanBal"  value="<? echo $loanBal; ?>" <? echo $loanBal_dis; ?> type="text" size="25" maxlength="10" onKeyPress="return isNumberInput2Decimal(this, event);" /></td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">&nbsp;Date of Last Payments</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanLastPay" id="loanLastPay"  value="<? echo $loanLastPay; ?>" type="text" size="25" maxlength="50" onChange="valDateToCurrDate(this.value,this.id);" />                      </td>
                    </tr>
                    <tr class="childGrid">
                      <td class="gridDtlLbl">&nbsp;Loan Status</td>
                      <td class="gridDtlLbl">:</td>
                      <td class="gridDtlVal"><input class="inputs" name="loanStat" id="loanStat" value="<? echo $loanStat; ?>" <? echo $loanTerms_dis; ?> type="text" size="25" maxlength="4"  readonly/></td>
                    </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
          
        </table>
	  <br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
							<input type="button" name="updateLoan" id="updateLoan" class="inputs" value="Save" <? echo $updateLoan_dis; ?> onClick="valUpdateLoan();">
                            <?php if (!empty($Pre_Terminate_Loan)) {?>
						    <input type="button" name="button" <?=$Pre_Terminate_Loan?> id="button" class="inputs" value="Pre-Terminate">
                            <?}?>                            
					  </CENTER>					</td>
				  </tr>
	  </table>	</td>
	</tr> 
	<tr > 
		<td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#ffffffff; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>

<SCRIPT>
		Calendar.setup({
				  inputField  : "loanStart",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgloanStart"       // ID of the button
			}
		)
		Calendar.setup({
				  inputField  : "loanEnd",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgloanEnd"       // ID of the button
			}
		)
		Calendar.setup({
				  inputField  : "dtGranted",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgdtGranted"       // ID of the button
			}
		)
</SCRIPT>