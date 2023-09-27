<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_obj.php");
$maintEmpObj = new inqEmpObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("inq_emp.trans.php");
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
<script type='text/javascript' src='inq_emp_js.js'></script>
</HEAD>
	<BODY>
<form name="frmEmp" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
<table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
	<tr>
		<td class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Holiday Reference
		</td>
	</tr>
	
	<tr>
		<td class="parentGridDtl" >
			<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
				<tr > 
					<td class="gridToolbar" colspan="6"> &nbsp;
						<a href="#" <? echo $printLoc; ?>></a>
						<a href="#" onclick='printHolidayCalendar();'><img src="../../../images/<? echo $printImgFileName2; ?>" align="absbottom" class="actionImg" title="Print Holiday List"></a> 
					</td>
				</tr>
				
				 <tr> 
					<td class="gridDtlLbl" width="10%">Year </td>
					<td class="gridDtlLbl" width="2%">:</td>
					<td class="gridDtlVal"> 
					  <? 	
							$arr_holiday = $maintEmpObj->makeArr($maintEmpObj->get_list_holiday($compCode),'Holiday_Year','Holiday_Year','All');
							$maintEmpObj->DropDownMenu($arr_holiday,'List_holidays',$List_holidays,"class='inputs'");
					  ?>
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