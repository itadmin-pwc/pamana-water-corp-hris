<?
	/*
		Date Created	:	08032010
		Created By		:	Genarra Arong
	*/
	
	session_start();
	include("../../../includes/userErrorHandler.php");
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	
	include("transaction_obj.php");

	$emptimesheetObj = new transactionObj();
	$sessionVars = $emptimesheetObj->getSeesionVars();
	$emptimesheetObj->validateSessions('','MODULES');
	
	
	
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
    
	<BODY>
    	<div id="empMastCont"></div>
        <div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager('view_edit_employee_timesheet_listAjaxResult.php','empMastCont','load',0,0,'','','&url=<?=$_GET["url"]?>','../../../images/');  
	
	
</SCRIPT>