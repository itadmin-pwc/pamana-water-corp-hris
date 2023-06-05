<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("inq_emp_loans_obj.php");

$inqEmpLoanObj = new inqEmpLoanObj();
$sessionVars = $inqEmpLoanObj->getSeesionVars();
$inqEmpLoanObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='inq_emp_loans_js.js'></script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	<BODY>
		<form name="frmEmpList" method="post" action="">
			<input type="hidden" name="empNoB" id="empNoB" value="<? echo $_GET['empNoB']; ?>">
			<input type="hidden" name="lonTypeCd" id="lonTypeCd" value="<? echo $_GET['lonTypeCd']; ?>">
			<input type="hidden" name="lonRefNo" id="lonRefNo" value="<? echo $_GET['lonRefNo']; ?>">
		</form>
		<div id="empMastCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	var empNoB=document.frmEmpList.empNoB.value;
	var lonTypeCd=document.frmEmpList.lonTypeCd.value;
	var lonRefNo=document.frmEmpList.lonRefNo.value;
	pager("inq_emp_loans_list_details_ajax.php","empMastCont",'load',0,0,'','','&empNoB='+empNoB+'&lonTypeCd='+lonTypeCd+'&lonRefNo='+lonRefNo,'../../../images/');  
</SCRIPT>