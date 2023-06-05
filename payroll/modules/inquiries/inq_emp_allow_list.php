<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("inq_emp_allow_obj.php");

$inqEmpAllowObj = new inqEmpAllowObj();
$sessionVars = $inqEmpAllowObj->getSeesionVars();
$inqEmpAllowObj->validateSessions('','MODULES');
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='inq_emp_allow_js.js'></script>
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
	</HEAD>
	<BODY>
		<form name="frmEmpList" method="post" action="">
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		  <input type="hidden" name="allowType" id="allowType" value="<? echo $_GET['allowType']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		 
		</form>
		<div id="allowListCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	var empNo=document.frmEmpList.empNo.value;
	var empDiv=document.frmEmpList.empDiv.value;
	var empDept=document.frmEmpList.empDept.value;
	var empSect=document.frmEmpList.empSect.value;
	var allowType=document.frmEmpList.allowType.value;
	var orderBy=document.frmEmpList.orderBy.value;
	
	pager("inq_emp_allow_list_ajax.php","allowListCont",'load',0,0,'','','&empNo='+empNo+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&allowType='+allowType+'&orderBy='+orderBy,"../../../images/");  	
</SCRIPT>