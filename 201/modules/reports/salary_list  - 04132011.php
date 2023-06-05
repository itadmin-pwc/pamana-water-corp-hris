<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("movement_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='movement.js'></script>
		<STYLE>@import url('../../style/reports.css');</STYLE>
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
		  <input type="hidden" name="code" id="code" value="<? echo $_GET['code']; ?>">
		  <input type="hidden" name="from" id="from" value="<? echo $_GET['from']; ?>">
		  <input type="hidden" name="to" id="to" value="<? echo $_GET['to']; ?>">
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
	var code=document.frmTS.code.value;
	var from=document.frmTS.from.value;
	var to=document.frmTS.to.value;
	pager("salary_list_ajax.php","TSCont",'load',0,0,'','','&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&groupType='+groupType+'&orderBy='+orderBy+'&code='+code+'&from='+from+'&to='+to+'&type=<?=$_GET['type']?>','../../../images/');  
</SCRIPT>