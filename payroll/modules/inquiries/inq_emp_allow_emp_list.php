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
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
	</HEAD>
	<BODY>
		<form name="frmEmpList" method="post" action="">
		  <input type="hidden" name="fileName" id="fileName" value="<? echo $_GET['fileName']; ?>">
		  <input type="hidden" name="inputId" id="inputId" value="<? echo $_GET['inputId']; ?>">
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empName" id="empName" value="<? echo $_GET['empName']; ?>">
		  
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
	pager("inq_emp_allow_emp_list_ajax.php","empMastCont",'load',0,0,'','','&fileName='+fileName+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName,'../../../images/');  
	
	
</SCRIPT>