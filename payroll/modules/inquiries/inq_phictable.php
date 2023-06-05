<?
/*
	Created By 		:	Genarra Jo - Ann Arong
	Date Created 	:	09 15 2009 4:01 pm
*/

	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("inq_phictable_obj.php");
	
	$inqPhicObj = new inqPhicObj();
	$sessionVars = $inqPhicObj->getSeesionVars();
	$inqPhicObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='inq_phictable_js.js'></script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
	</HEAD>
	<BODY>
		<form name="frmPhicList" method="post" action="">
		 
		</form>
		<div id="PhicListCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager("inq_phictable_ajax.php","PhicListCont",'load',0,0,'','','','../../../images/');  	
	
</SCRIPT>