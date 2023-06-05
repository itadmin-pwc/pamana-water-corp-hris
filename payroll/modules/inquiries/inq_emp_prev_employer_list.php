<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("../maintenance/maintenance_employee.Obj.php");

$maintEmpObj = new maintEmpObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='inq_emp_js.js'></script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
	</HEAD>
	<BODY>
		<form name="frmEmp" method="post" action="">
		  <input type="hidden" name="prevEmpNo" id="prevEmpNo" value="<? echo $_GET['prevEmpNo']; ?>">
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empName" id="empName" value="<? echo $_GET['empName']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		  <input type="hidden" name="groupType" id="groupType" value="<? echo $_GET['groupType']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		  <input type="hidden" name="catType" id="catType" value="<? echo $_GET['catType']; ?>">
		</form>
		<div id="empMastCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	var prevEmpNo=document.frmEmp.prevEmpNo.value;
	var empNo=document.frmEmp.empNo.value;
	var empName=document.frmEmp.empName.value;
	var empDiv=document.frmEmp.empDiv.value;
	var empDept=document.frmEmp.empDept.value;
	var empSect=document.frmEmp.empSect.value;
	var groupType=document.frmEmp.groupType.value;
	var orderBy=document.frmEmp.orderBy.value;
	var catType=document.frmEmp.catType.value;
	pager("inq_emp_prev_employer_list_ajax.php","empMastCont",'load',0,0,'','','&prevEmpNo='+prevEmpNo+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType,'../../../images/');  
</SCRIPT>