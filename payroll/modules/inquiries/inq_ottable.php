<?
/*
	Created By 		:	Genarra Jo - Ann Arong
	Date Created 	:	09 15 2009 4:01 pm
*/

	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("inq_ottable_obj.php");
	
	$inqOtObj = new inqOtObj();
	$sessionVars = $inqOtObj->getSeesionVars();
	$inqOtObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='inq_ottable_js.js'></script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
	</HEAD>
	<BODY>
		<form name="frmOtList" method="post" action="">
		 
		</form>
		<div id="otListCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager("inq_ottable_ajax.php","otListCont",'load',0,0,'','','','../../../images/');  	
	
</SCRIPT>