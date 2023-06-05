<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");



$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');


switch($_GET["action"])
{
	case "Delete":
		
		$delTsCorr = $inqTSObj->deleteTsCorr($_GET["empNo"], date("Y-m-d", strtotime($_GET["tsDate"])));
		echo "alert('Transaction successfully deleted.');";
		exit();
		
	break;
	
	default:
	break;
}
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
		<div id="empTsList"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager('timesheet_adjustment_emp_list_ajax.php','empTsList','load',0,0,'','','&empNo=<?=$_GET['empNo']?>','../../../images/');  
	
	function TimeSheet_Pop(mode,empNo,tsDate)
	{
		var winPrevEmp = new Window({
		id: "blacklist",
		className : 'mac_os_x',
		width:500, 
		height:355, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: " Time Sheet Adjusment", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		winPrevEmp.setURL('timesheet_adjustment_pop.php?&action='+mode+'&empNo='+empNo+'&tsDate='+tsDate);
		winPrevEmp.showCenter(true);	
		
		
		 myObserver = {
			onDestroy: function(eventName, win) {

			  if (win == winPrevEmp) {
				winPrevEmp = null;
				Windows.removeObserver(this);
				pager("timesheet_adjustment_emp_list_ajax.php",'empTsList','load',0,0,'','','&empNo='+empNo,'../../../images/');  
			  }
			}
		  }
		  Windows.addObserver(myObserver);
	}
	
	function delEmpTsAdj( empNo, tsDate)
	{
		var delEmpTsAdj = confirm('Are you sure you want to delete the selected record? ');
		if(delEmpTsAdj == true)
		{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?&action=Delete&empNo='+empNo+'&tsDate='+tsDate,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager("timesheet_adjustment_emp_list_ajax.php",'empTsList','load',0,0,'','','&empNo='+empNo,'../../../images/');  
				}			
			})
		}
	}
</SCRIPT>