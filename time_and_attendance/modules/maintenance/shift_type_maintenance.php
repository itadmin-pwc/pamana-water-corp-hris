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

$maintShiftCodeObj = new maintenanceObj();
$sessionVars = $maintShiftCodeObj->getSeesionVars();
$maintShiftCodeObj->validateSessions('','MODULES');

switch($_GET['action']) 
{
	case "Save":
		
		
		//Check Duplicate
		$chkDuplicate = $maintShiftCodeObj->getShiftInfo("tblTK_ShiftHdr", " and shiftCode='".$_GET["txtShiftCode"]."'", " order by shiftCode desc");
		
		if($chkDuplicate["shiftCode"]!="")
		{
			echo "alert('Shift Code already exists.');";
		}
		else
		{
			$error = explode("-", $maintShiftCodeObj->validateShiftCodeDtl($_GET));
			
			if($error[0]=='1')
			{
				echo "alert('".$error[1].".')";
			}
			elseif(($error[0]=='2')||($error[0]=='3'))
			{
				echo "conFirmcrossDay('".$error[1]."','crossDayConfirm_Add');";
				exit();
			}
			else
			{
				if($maintShiftCodeObj->maint_Shift_Code("Add",$_GET))
					echo "alert('Shift Code Detail Successfully Added.');";
				else
					echo "alert('Error in Adding the Shift Code Detail.');";
			}
		}
		
		exit();
		
	break;
	
	case "Update":
		$error =explode("-", $maintShiftCodeObj->validateShiftCodeDtl($_GET)) ;
		if($error[0]=='1')
		{
			echo "alert('".$error[1].".')";
		}
		elseif(($error[0]=='2')||($error[0]=='3'))
		{
			echo "conFirmcrossDay('".$error[1]."','crossDayConfirm_Update');";
			exit();
		}
		elseif($error[0]=='3')
		{
			echo "conFirmcrossDay('".$error[1]."','crossDayConfirm_Update');";
			exit();
		}
		else
		{
			if($maintShiftCodeObj->maint_Shift_Code("Update",$_GET))
				echo "alert('Shift Code Detail Successfully Updated.');";
			else
				echo "alert('Error in Updating the Shift Code Detail.');";
		}
		exit();
	break;
	
	case "Delete":
		if($maintShiftCodeObj->maint_Shift_Code("Delete",$_GET))
			echo "alert('Shift Code Detail Successfully Deleted.');";
		else
			echo "alert('Error in Deleting the Selected Shift Code Detail.');";
		
		exit();
	break;
	
	case "setShiftActive":
		if($maintShiftCodeObj->maint_Shift_Code("UpdateShift",$_GET))
			echo "alert('Shift Code Detail already set as to Active.');";
		else
			echo "alert('Error in Updating the Shift Code Detail to Active.');";
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
		<div id="empShiftTypeList"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager('shift_type_maintenance_listAjaxResult.php','empShiftTypeList','load',0,0,'','','','../../../images/');  
	
	function maintShiftCode(act,shiftCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:900, 
		height:340, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Shift Detail", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('shift_type_maintenance_pop.php?&modAction='+act+'&shiftCode='+shiftCode);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		        pager('shift_type_maintenance_listAjaxResult.php','empShiftTypeList','load',0,0,'','','','../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function delShiftCode(act,shiftCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected Shift Code?');
		if(deleShiftCode == true){
			var param = '?action=Delete&shiftCode='+shiftCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('shift_type_maintenance_listAjaxResult.php','empShiftTypeList','load',0,0,'','','','../../../images/');  
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
	
	
	function setActiveShiftCode(act,shiftCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to set the Shift Code into Active?');
		if(deleShiftCode == true){
			var param = '?action=setShiftActive&shiftCode='+shiftCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('shift_type_maintenance_listAjaxResult.php','empShiftTypeList','load',0,0,'','','','../../../images/');  
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