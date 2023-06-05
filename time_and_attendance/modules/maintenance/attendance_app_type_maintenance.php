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

$maintAttnAppCodeObj = new maintenanceObj();
$sessionVars = $maintAttnAppCodeObj->getSeesionVars();
$maintAttnAppCodeObj->validateSessions('','MODULES');

switch($_GET['action']) 
{
	case "Save":
		
		//Check Duplicate Leave App. Type
		$chkDuplicate = $maintAttnAppCodeObj->getShiftInfo("tblTK_AppTypes", " and appTypeDesc='".$_GET["txtAppTypeDesc"]."'", " ");
		if($chkDuplicate["appTypeDesc"]!="")
		{
			echo "alert('Attendance Application Type already exists.');";
		}
		else
		{
			if($maintAttnAppCodeObj->maint_attdn_type("Add",$_GET))
				echo "alert('Attendance Application Code Successfully Added.');";
			else
				echo "alert('Error in Adding the Attendance Application Code.');";
		}
		
		exit();
		
	break;
	
	case "Update":
		
		if($maintAttnAppCodeObj->maint_attdn_type("Update",$_GET))
				echo "alert('Attendance Application Code Successfully Updated.');";
			else
				echo "alert('Error in Updating the Attendance Application Code Detail.');";
		exit();
	break;
	
	case "Delete":
		if($maintAttnAppCodeObj->maint_attdn_type("Delete",$_GET))
			echo "alert('Attendance Application Code Successfully Deleted.');";
		else
			echo "alert('Error in Deleting the Selected Attendance Application Code.');";
		exit();
	break;
	
	case "setAttdnAppActive":
		if($maintAttnAppCodeObj->maint_attdn_type("setAttdnAppActive",$_GET))
			echo "alert('Attendance Application Code Successfully Updated to Active.');";
		else
			echo "alert('Error in Updating the Selected Attendance Application Code to Active.');";
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
		<div id="attAppType"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager('attendance_app_type_maintenance_listAjaxResult.php','attAppType','load',0,0,'','','','../../../images/');  
	
	function maintShiftCode(act,attdnCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:500, 
		height:200, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Attendance App. Detail", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('attendance_app_type_maintenance_pop.php?&modAction='+act+'&attdnCode='+attdnCode);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		        pager('attendance_app_type_maintenance_listAjaxResult.php','attAppType','load',0,0,'','','','../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function delShiftCode(act,attdnCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected Attendance Application Code?');
		if(deleShiftCode == true){
			var param = '?action=Delete&attnAppCode='+attdnCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('attendance_app_type_maintenance_listAjaxResult.php','attAppType','load',0,0,'','','','../../../images/');  
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
	
	
	function setActiveAttdnAppCode(act,attdnCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to set the Attendance Application Code into Active?');
		if(deleShiftCode == true){
			var param = '?action=setAttdnAppActive&attnAppCode='+attdnCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('attendance_app_type_maintenance_listAjaxResult.php','attAppType','load',0,0,'','','','../../../images/');  
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