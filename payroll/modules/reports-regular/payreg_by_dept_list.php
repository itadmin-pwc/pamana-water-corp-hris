<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='timesheet_js.js'></script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
	</HEAD>
	<BODY>
		<form name="frmTS" method="post" action="">
		  <input type="hidden" name="fromDate" id="fromDate" value="<? echo $_GET['fromDate']; ?>">
		  <input type="hidden" name="toDate" id="toDate" value="<? echo $_GET['toDate']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="locType" id="locType" value="<? echo $_GET["locType"]; ?>">
          <input type="hidden" name="empBrnCode" id="empBrnCode" value="<? echo $_GET['empBrnCode']; ?>">
		</form>
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	var fromDate=document.frmTS.fromDate.value;
	var toDate=document.frmTS.toDate.value;
	var empDiv=document.frmTS.empDiv.value;
	var empDept=document.frmTS.empDept.value;
	var locType = document.frmTS.locType.value;
	var empBrnCode=document.frmTS.empBrnCode.value;
	
	pager("payreg_by_dept_list_ajax.php","TSCont",'load',0,0,'','','&fromDate='+fromDate+'&toDate='+toDate+'&empDiv='+empDiv+'&empDept='+empDept+"&empBrnCode="+empBrnCode+"&locType="+locType,'../../../images/');  
</SCRIPT>