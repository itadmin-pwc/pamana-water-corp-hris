<?
/*
	Date Created	:	072010
	Created By		:	Genarra Arong
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_obj.php");

$maintVioCodeObj = new maintenanceObj();
$sessionVars = $maintVioCodeObj->getSeesionVars();
$maintVioCodeObj->validateSessions('','MODULES');

switch($_GET['action']) 
{
	case "Save":
		
		//Check Duplicate Leave App. Type
		$chkDuplicate = $maintVioCodeObj->getShiftInfo("tblTK_ViolationType", " and violationDesc='".$_GET["txtVioTypeDesc"]."'", " ");
		if($chkDuplicate["violationDesc"]!="")
		{
			echo "alert('Violation Type already exists.');";
		}
		else
		{
			if($maintVioCodeObj->maint_violation_type("Add",$_GET))
				echo "alert('Violation Type Successfully Added.');";
			else
				echo "alert('Error in Adding the Violation Type.');";
		}
		
		exit();
		
	break;
	
	case "Update":
		
		if($maintVioCodeObj->maint_violation_type("Update",$_GET))
				echo "alert('Violation Type Successfully Updated.');";
			else
				echo "alert('Error in Updating the Violation Type Detail.');";
		exit();
	break;
	
	case "Delete":
		if($maintVioCodeObj->maint_violation_type("Delete",$_GET))
			echo "alert('Violation Type Successfully Deleted.');";
		else
			echo "alert('Error in Deleting the Selected Violation Type.');";
		exit();
	break;
	
	case "setVioTypeActive":
		if($maintVioCodeObj->maint_violation_type("setVioTypeActive",$_GET))
			echo "alert('Violation Type Successfully set to Active.');";
		else
			echo "alert('Error in Updating the Selected Violation Type.');";
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
		<div id="vioAppType"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager('violation_type_maintenance_listAjaxResult.php','vioAppType','load',0,0,'','','','../../../images/');  
	
	function maintShiftCode(act,vioCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:500, 
		height:200, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Violation Code Detail", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('violation_type_maintenance_pop.php?&modAction='+act+'&vioCode='+vioCode);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		        pager('violation_type_maintenance_listAjaxResult.php','vioAppType','load',0,0,'','','','../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function delShiftCode(act,vioCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected Violation Type?');
		if(deleShiftCode == true){
			var param = '?action=Delete&vioCode='+vioCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('violation_type_maintenance_listAjaxResult.php','vioAppType','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
				}
			});	
			
			
		}
	}
	
	
	function setVioTypeActive(act,vioCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to set the Violation Type into Active?');
		if(deleShiftCode == true){
			var param = '?action=setVioTypeActive&vioCode='+vioCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('violation_type_maintenance_listAjaxResult.php','vioAppType','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
				}
			});	
			
			
		}
	}
	
</SCRIPT>