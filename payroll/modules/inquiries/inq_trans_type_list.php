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
		</form>
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager("inq_trans_type_list_ajax.php","TSCont",'load',0,0,'','','','../../../images/');  
</SCRIPT>