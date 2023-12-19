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
		<STYLE>@import url('../../style/payroll.css');</STYLE>
	</HEAD>
	<BODY>
		<form name="frmTS" method="post" action="">
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empName" id="empName" value="<? echo $_GET['empName']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		  <input type="hidden" name="payPd" id="payPd" value="<? echo $_GET['payPd']; ?>">
          <input type="hidden" name="reportType" id="reportType" value="<? echo $_GET['reportType']; ?>"> 
          <input type="hidden" name="locType" id="locType" value="<? echo $_GET["locType"]; ?>">
          <input type="hidden" name="empBrnCode" id="empBrnCode" value="<? echo $_GET['empBrnCode']; ?>">
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
	var orderBy=document.frmTS.orderBy.value;
	var payPd=document.frmTS.payPd.value;
	var reportType = document.frmTS.reportType.value;
	var locType = document.frmTS.locType.value;
	var empBrnCode=document.frmTS.empBrnCode.value;
	
	pager("timesheet_list_ajax.php","TSCont",'load',0,0,'','','&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&payPd='+payPd+'&reportType='+reportType+'&empBrnCode='+empBrnCode+'&locType='+locType,'../../../images/');  
</SCRIPT>