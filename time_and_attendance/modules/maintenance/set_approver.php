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
		
		<script type="text/javascript" src="../../../includes/calendar.js"></script>	
		<STYLE>@import url('../../../includes/calendar.css');</STYLE>
		
	</HEAD>
	
<BODY>
	<FORM name='frmApprover' id="frmApprover" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		<div id="approverCont"></div>
		<div id="indicator1" align="center"></div>
	</FORM>
</BODY>

</HTML>

<SCRIPT>
	pager('set_approverAjaxResult.php','approverCont','load',0,0,'','','&empNo='<?=$_GET["empNo"]?>,'../../../images/'); 
</SCRIPT>