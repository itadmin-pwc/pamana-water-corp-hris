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
	</HEAD>
	<BODY>
		<form name="frmEmpList" method="post" action="">
		  <input type="hidden" name="fileName" id="fileName" value="<? echo $_GET['fileName']; ?>">
		  <input type="hidden" name="inputId" id="inputId" value="<? echo $_GET['inputId']; ?>">
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empName" id="empName" value="<? echo $_GET['empName']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		 
		</form>
		<div id="empMastCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	var fileName=document.frmEmpList.fileName.value;
	var inputId=document.frmEmpList.inputId.value;
	var empNo=document.frmEmpList.empNo.value;
	var empName=document.frmEmpList.empName.value;
	var empDiv=document.frmEmpList.empDiv.value;
	var empDept=document.frmEmpList.empDept.value;
	var empSect=document.frmEmpList.empSect.value;
	
	pager("inq_emp_loans_emp_list_ajax.php","empMastCont",'load',0,0,'','','&fileName='+fileName+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,'../../../images/');  
	
	
</SCRIPT>