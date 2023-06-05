<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="timesheet_js.js"></SCRIPT>		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>		</HEAD>
	<BODY>
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager('lonref_list_ajax.php','TSCont','load',0,0,'','','','../../../images/');  
</SCRIPT>
<script type="text/javascript"> 
	function DeleteRefNo(id){
		var ans = confirm('Are you sure you want to delete this Ref. No.?');
		if (ans == false) {
			return false;	
		}
		
		params = 'lonref_act.php?code=DeleteRef&id='+id;
		new Ajax.Request(params,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
				pager('lonref_list_ajax.php','TSCont','load',0,0,'','','','../../../images/');  
			}	
		});		
	}
	
	
</script>