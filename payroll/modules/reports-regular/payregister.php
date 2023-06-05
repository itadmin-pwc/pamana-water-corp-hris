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
        <STYLE TYPE="text/css" MEDIA="screen">
        @import url("../../../includes/calendar/calendar-blue.css");.style1 {font-family: Verdana}
        </STYLE>
        <!--end calendar lib-->
        <script type='text/javascript' src='timesheet_js.js'></script>
    </HEAD>
<BODY>
		<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
            <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
            	<tr>
            		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Payroll Register</td>
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
                              <td class="gridDtlLbl">With Name?</td>
                              <td class="gridDtlLbl">&nbsp;</td>
                              <td class="gridDtlVal"><table width="200">
                                <tr>
                                  <td class="gridDtlVal"><span class="style1">
                                    <label>
                                      <input type="radio" name="chname" value="1" id="chname_0" checked>
                                      Yes</label>
                                  </span></td>
                                </tr>
                                <tr>
                                  <td class="gridDtlVal"><span class="style1">
                                    <label>
                                      <input name="chname" type="radio" id="chname_1" value="0" >
                                      No</label>
                                  </span></td>
                                </tr>
                              </table></td>
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
                            	<td class="gridDtlLbl">Location </td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal"> 
                                	<?  
                                    	$inqTSObj->DropDownMenu(array('A'=>'All','S'=>'Store','H'=>'Head Office'),'locType',$locType,$locType_dis); 
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
                                <td width="81%" class="gridDtlVal">
                                	<input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);"> 
                                </td>
                            </tr>
                            
                            <tr> 
                                <td class="gridDtlLbl">Employee Name </td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal">
                                    <input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="text" size="25" maxlength="50" onKeyPress="getEmpSearch(event);">
                                </td>
                            </tr>
                            
                            <tr > 
                           	 	<td  class="gridToolbarWithColor" colspan="6">
                                	<center></center>
                                </td>
                            </tr>
                            
                            
                            
                            <tr> 
                                <td class="gridDtlLbl">Report Type </td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal" colspan="4"> 
                                <?  
                                    $inqTSObj->DropDownMenu(array('B'=>'Both','R'=>'Regular','A'=>'Allowance'),'topType',$topType,$topType_dis); 
                                ?>
                                <input type="hidden" name="reportType" id="reportType" value="0">
                                <input type="hidden" name="orderBy" id="orderBy" value="">
                                </td>
                            </tr>
                            
                            <tr> 
                                <td class="gridDtlLbl">Payroll Period </td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"> 
                                	<div id="pdPay"> 
                                        <input name="hide_payPd" type="hidden" id="hide_payPd" value="<? echo $payPd; ?>">
                                        <? 					
                                            $arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType),'pdSeries','pdPayable','');
                                            $inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis);
                                        ?>
                                	</div>
                                </td>
                            </tr>
             
                           <!--<tr> 
                                <td class="gridDtlLbl">Order By</td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"> 
                                  <font class="byOrder"> 
                                  <?
                                        //$inqTSObj->DropDownMenu(array('1'=>'EMPLOYEE NAME','2'=>'EMPLOYEE NUMBER','3'=>'DEPARTMENT'),'orderBy',$orderBy,$orderBy_dis); 
                                  ?>
                                  </font> 
                                </td>
                          </tr>-->
            		</table>
            		<br>
                    <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
                      <tr>
                        <td>
                            <CENTER>
                                <input type="button" name="searchTS10" id="searchTS10" <? echo $searchTS9_dis; ?> value="Generate Payroll Register" onClick="valSearchTS(this.id);">
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