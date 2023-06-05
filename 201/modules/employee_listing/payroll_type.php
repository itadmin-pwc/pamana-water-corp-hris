<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Blacklist Information Report 
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
        <script type='text/javascript' src='../../../201/modules/employee_listing/common_js.js'></script>
    </HEAD>
	<BODY>
        <form name="frmTS" method="post" action="<? echo $_SERVER['../../../payroll/modules/special-reports/PHP_SELF']; ?>">
            <input type="hidden" name="empNo" id="empNo" value="">
            <input type="hidden" name="empName" id="empName" value="">
            <input type="hidden" name="empPos" id="empPos" value="">
            
            <input type="hidden" name="orderBy" id="orderBy" value="0">
            
            <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
           	 	<tr>
            		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Listing Report By Payroll Type</td>
            	</tr>
            
            	<tr>
            		<td class="parentGridDtl" >
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
            				<tr > 
            					<td class="gridToolbar" colspan="6"> 
                                	<input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
            						<? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
            						<input name='fileName' type='hidden' id='fileName' value="payroll_type.php">            
                                </td>
            				</tr>
                            
                            <tr> 
                            	<td class="gridDtlLbl">Branch </td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal"> 
                              		<? 					
										$arrBranch = $inqTSObj->makeArr($inqTSObj->getAllBranch(),'brnCode','brnDesc','');
										$inqTSObj->DropDownMenu($arrBranch,'empBrnCode',$empBrnCode,$empBrnCode_dis);
									?>
                            	</td>
                            </tr>
                            
                             <tr> 
                            	<td class="gridDtlLbl">Division </td>
                            	<td class="gridDtlLbl">:</td>
                            	<td class="gridDtlVal"> 
                              		<? 		
										$arrDiv = $inqTSObj->makeArr($inqTSObj->getDivArt($_SESSION["company_code"]),'divCode','deptDesc','');
										$inqTSObj->DropDownMenu($arrDiv,'empDiv',$empDiv,$empDiv_dis);
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
										$arrDept = $inqTSObj->makeArr($inqTSObj->getDeptArt($_SESSION["company_code"],$empDiv),'deptCode','deptDesc','');
										$inqTSObj->DropDownMenu($arrDept,'empDept',$empDept,$empDept_dis);
									?>
                                  	</div>
                               </td>
                            </tr>
                            
                           <tr> 
                                <td class="gridDtlLbl">Section </td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"> <div id="deptSect"> 
                                    <input name="hide_empSect" type="hidden" id="hide_empSect" value="<? echo $empDept; ?>">
                                    <? 					
										$arrDept = $inqTSObj->makeArr($inqTSObj->getSectArt($compCode,$empDiv,$empDept),'sectCode','deptDesc','');
										$inqTSObj->DropDownMenu($arrDept,'empSect',$empSect,$empSect_dis);
									?>
                                  </div></td>
                              </tr>
            				
                            <tr > 
                                <td  class="gridToolbarWithColor" colspan="6">
                                    <center></center>
                                </td>
                            </tr>
                            
                           
                           
            			</table>
            			<br>
            			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
            				<tr>
            					<td>
                                    <CENTER>
                                    	<input type="button" name="btnpayrolltype" id="btnpayrolltype" <? echo $btnpayrolltype_dis; ?> value="Generate Listing Report" onClick="valSearchTS(this.id);">
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
