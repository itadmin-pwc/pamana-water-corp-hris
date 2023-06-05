<?
/*
	Created By 		:	Genarra Jo - Ann Arong
	Date Created 	:	09 15 2009 10:50am
*/

	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("inq_ssstable_obj.php");
	
	$inqSSSObj = new inqSSSObj();
	$sessionVars = $inqSSSObj->getSeesionVars();
	$inqSSSObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='inq_ssstable_js.js'></script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
	</HEAD>
	<BODY>
		<form name="frmSSSList" method="post" action="">
		 
		</form>
		<div id="SssListCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager("inq_ssstable_ajax.php","SssListCont",'load',0,0,'','','','../../../images/');  	
	
</SCRIPT>