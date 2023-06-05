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
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empName" id="empName" value="<? echo $_GET['empName']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		  <input type="hidden" name="groupType" id="groupType" value="<? echo $_GET['groupType']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		  <input type="hidden" name="catType" id="catType" value="<? echo $_GET['catType']; ?>">
		  <input type="hidden" name="payPd" id="payPd" value="<? echo $_GET['payPd']; ?>">
		  <input type="hidden" name="table" id="table" value="<? echo $_GET['table']; ?>">
		</form>
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	var empNo=document.frmTS.empNo.value;
	var empName=document.frmTS.empName.value;
	var empDiv=document.frmTS.empDiv.value;
	var empDept=document.frmTS.empDept.value;
	var empSect=document.frmTS.empSect.value;
	var groupType=document.frmTS.groupType.value;
	var orderBy=document.frmTS.orderBy.value;
	var catType=document.frmTS.catType.value;
	var payPd=document.frmTS.payPd.value;
	var table=document.frmTS.table.value;
	pager("pagibig_loan_list_ajax.php","TSCont",'load',0,0,'','','&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType+'&payPd='+payPd+'&table='+table,'../../../images/');  
</SCRIPT>