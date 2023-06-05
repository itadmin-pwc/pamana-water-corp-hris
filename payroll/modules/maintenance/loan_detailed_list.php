<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("main_emp_loans_obj.php");

$maintEmpLoanObj = new maintEmpLoanObj();
$sessionVars = $maintEmpLoanObj->getSeesionVars();
$maintEmpLoanObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='main_emp_loans_js.js'></script>
		<STYLE>@import url('../../style/main_emp_loans.css');</STYLE>
	</HEAD>
	<BODY>
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager("loan_detailed_list_ajax.php","TSCont",'load',0,0,'','','&lonSeries=<?=$_GET['lonSeries']?>','../../../images/');  
</SCRIPT>