<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("movement.trans.php");
##################################################
if ($_GET['code']=="") {
	$code = $_POST['code'];
} else {
	$code = $_GET['code'];
}

?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/reports.css');</style>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Employee Tenure</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
              <input name='fileName' type='hidden' id='fileName' value="empStat.php">
              <input name='orderBy' type='hidden' id='orderBy'>
              <input name='txtfrDate' type='hidden' id='txtfrDate'>
                <input name='txttoDate' type='hidden' id='txttoDate'>
                <input type="hidden" name="empNo" id="empNo">
                <input type="hidden" name="empName" id="empName">
                <input type="hidden" name="empSect" id="empSect"></td>
          </tr>
          

          <tr>
            <td width="18%" class="gridDtlLbl">Division </td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><? 					
								$arrDept = $inqTSObj->makeArr($inqTSObj->getDivArt($compCode),'divCode','deptDesc','');
								$inqTSObj->DropDownMenu($arrDept,'empDiv',$empDiv,$empDiv_dis);
							?>            </td>
            </tr>            
          <tr> 
            <td class="gridDtlLbl">Department </td>
            <td class="gridDtlLbl">:</td>
            <td width="79%" colspan="2" class="gridDtlVal"> <div id="deptDept"> 
                <input name="hide_empDept" type="hidden" id="hide_empDept" value="<? echo $empDept; ?>">
                <? 					
								$arrDept = $inqTSObj->makeArr($inqTSObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','');
								$inqTSObj->DropDownMenu($arrDept,'empDept',$empDept,$empDept_dis);
							?>
              </div></td>
          </tr>
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="salary" id="salary" <? echo $searchTS4_dis; ?> value=" Pdf " onClick="EmpTenure('Pdf');">
                <input type="button" name="salary2" id="salary2" <? echo $searchTS4_dis; ?> value="Chart" onClick="EmpTenure('chart');">
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
