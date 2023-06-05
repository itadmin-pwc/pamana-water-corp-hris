<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<script src="../../../js/extjs/adapter/prototype/scriptaculous.js" type="text/javascript"></script>
		<script src="../../../js/extjs/adapter/prototype/unittest.js" type="text/javascript"></script>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<SCRIPT type="text/javascript" src="timesheet_js.js"></SCRIPT>		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		
		

		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>		
	</HEAD>
	<BODY>
		<FORM name='frmEmpMast' id="frmEmpMast" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<div id="empMastCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	pager("transferred_employees_ajax.php",'empMastCont','load',0,0,'','','','../../../images/');  	
</SCRIPT>