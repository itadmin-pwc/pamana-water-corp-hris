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
					<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;YTD for the Month</td>
				</tr>
	
    			<tr>
					<td class="parentGridDtl" >
			  			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                            <tr > 
                                <td class="gridToolbar" colspan="7"> 
                                    <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
                                    <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
                                    <input name='fileName' type='hidden' id='fileName' value="tax.php">                                </td>
                            </tr>
                            
                            <tr> 
                            	<td width="18%" class="gridDtlLbl">Division </td>
                            	<td width="1%" class="gridDtlLbl">:</td>
                                <td width="66%" class="gridDtlVal"> 
									<? 					
                                        $arrDept = $inqTSObj->makeArr($inqTSObj->getDivArt($compCode),'divCode','deptDesc','');
                                        $inqTSObj->DropDownMenu($arrDept,'empDiv',$empDiv,$empDiv_dis);
                                    ?>                                <input name="report_type" type="hidden" id="report_type"></td>
                            <td width="15%" class="gridDtlVal"><div id="deptSect">
                                  <input name="empSect" type="hidden" id="empSect" value="<? echo $empDept; ?>">
                                  <input name="hide_empSect" type="hidden" id="hide_empSect">
                                </div></td>
                            </tr>
                            <tr> 
                            <td class="gridDtlLbl">Department </td>
                            <td class="gridDtlLbl">:</td>
                            <td colspan="2" class="gridDtlVal"> <div id="deptDept"> 
                            <input name="hide_empDept" type="hidden" id="hide_empDept" value="<? echo $empDept; ?>">
                            <? 					
                            $arrDept = $inqTSObj->makeArr($inqTSObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','');
                            $inqTSObj->DropDownMenu($arrDept,'empDept',$empDept,$empDept_dis);
                            ?>
                            <input name="hide_empSect" type="hidden" id="hide_empSect" value="<? echo $empDept; ?>">
                            
                            <input name="empNo" type="hidden" id="empNo" value="<? echo $empDept; ?>">
                            <input name="empName" type="hidden" id="empName" value="<? echo $empDept; ?>">
                            </div></td>
                            </tr>
          
          <tr > 
            <td  class="gridToolbarWithColor" colspan="7"><center>
              </center></td>
          </tr>
          
          
		  <tr> 
            <td class="gridDtlLbl">Payroll Period (Monthly)</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="2" class="gridDtlVal"> <div id="pdPay"> 
                <input name="hide_payPd" type="hidden" id="hide_payPd" value="<? echo $payPd; ?>">
                <? 					
								$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType,"0"),'pdNumber','perMonth',''); // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
								$inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis);
							?>
                <input name="orderBy" type="hidden" id="orderBy">
              </div></td>
          </tr>
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="monthly_ytd" id="monthly_ytd" <? echo $searchTS_dis; ?> value="  PDF  " onClick="YTDRepType('pdf');valSearchTS(this.id);">
                <input type="button" name="monthly_ytd" id="monthly_ytd" <? echo $searchTS_dis; ?> value="EXCEL" onClick="YTDRepType('excel');valSearchTS(this.id);">
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