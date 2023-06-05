<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_allow_obj.php");
$maintEmpAllowObj = new inqEmpAllowObj();
$sessionVars = $maintEmpAllowObj->getSeesionVars();
$maintEmpAllowObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("inq_emp_allow.trans.php");
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
<script type='text/javascript' src='inq_emp_allow_js.js'></script>
</HEAD>
	<BODY>
<form name="frmEmpAllow" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		<td class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Employee Allowances
		</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="6"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
              <input name='fileName' type='hidden' id='fileName' value="inq_emp_allow.php"> 
			  <FONT class="ToolBarseparator">|</font> <a href="#" onClick="printAllowTypeList();">
              <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Allowance Type List">Allowance Type List</a>            </td>
          </tr>
          <tr> 
            <td width="18%" class="gridDtlLbl">Emp. #</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="81%" class="gridDtlVal"><input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);"> 
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
            <td class="gridDtlLbl">Allowance Type </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal" colspan="4"> 
		      <input name="hide_allowType" type="hidden" id="hide_allowType" value="<? echo $allowType; ?>">
			  <div id="typeAllow"> 
              <?  
					$arrAllow = $maintEmpAllowObj->makeArr($maintEmpAllowObj->getallowTypeListArt($compCode),'allowCode','allowDesc','ALL');			
					$maintEmpAllowObj->DropDownMenu($arrAllow,'allowType',$allowType,$allowType_dis);
			  ?>            </td>
          </tr>
		  <tr> 
            <td class="gridDtlLbl">Order By</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"> 
			  <font class="byOrder"> 
              <?
					$maintEmpAllowObj->DropDownMenu(array('1'=>'EMPLOYEE','2'=>'ALLOWANCE TYPE'),'orderBy',$orderBy,$orderBy_dis); 
			  ?>
              </font>			</td>
          </tr>
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
							<input type="button" name="searchAllow" id="searchAllow" value="Search" <? echo $searchAllow_dis; ?> onClick="valSearchAllow();">	
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