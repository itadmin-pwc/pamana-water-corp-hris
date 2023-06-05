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

$maintReasons = new maintenanceObj();
$sessionVars = $maintReasons->getSeesionVars();
$maintReasons->validateSessions('','MODULES');

switch($_GET['action']) 
{
	case "Save":
		
		//Check Duplicate Leave App. Type
		$chkDuplicate = $maintReasons->getShiftInfo("tblTK_Reasons", " and reason='".$_GET["txtReason"]."'", " ");
		if($chkDuplicate["reason"]!="")
		{
			echo "alert('Reason already exists.');";
		}
		else
		{
			if($maintReasons->maint_reason("Add",$_GET))
				echo "alert('Reason Successfully Added.');";
			else
				echo "alert('Error in Adding the Reason.');";
		}
		
		exit();
		
	break;
	
	case "Update":
		
		if($maintReasons->maint_reason("Update",$_GET))
				echo "alert('Reason Successfully Updated.');";
			else
				echo "alert('Error in Updating the Reason.');";
		exit();
	break;
	
	case "Delete":
		if($maintReasons->maint_reason("Delete",$_GET))
			echo "alert('Reason Successfully Deleted.');";
		else
			echo "alert('Error in Deleting the Selected Reason.');";
		exit();
	break;
	
	case "setReasonActive":
		if($maintReasons->maint_reason("setReasonActive",$_GET))
			echo "alert('Reason Successfully set to Active.');";
		else
			echo "alert('Error in Updating the Selected Reason.');";
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
		<div id="reasons"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager('reasons_maintenance_listAjaxResult.php','reasons','load',0,0,'','','','../../../images/');  
	
	function maintShiftCode(act,resCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:500, 
		height:300, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Reason Detail", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('reasons_maintenance_pop.php?&modAction='+act+'&resCode='+resCode);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		        pager('reasons_maintenance_listAjaxResult.php','reasons','load',0,0,'','','','../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function delShiftCode(act,resCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected Reason?');
		if(deleShiftCode == true){
			var param = '?action=Delete&resCode='+resCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('reasons_maintenance_listAjaxResult.php','reasons','load',0,0,'','','','../../../images/');  
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
	
	
	function setReasonActive(act,resCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to set the Reason into Active?');
		if(deleShiftCode == true){
			var param = '?action=setReasonActive&resCode='+resCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('reasons_maintenance_listAjaxResult.php','reasons','load',0,0,'','','','../../../images/');  
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