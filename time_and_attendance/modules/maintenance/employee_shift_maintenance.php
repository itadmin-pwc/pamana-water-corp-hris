<?
	/*
		Date Created	:	07272010
		Created By		:	Genarra Arong
	*/
	
	session_start();
	include("../../../includes/userErrorHandler.php");
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	
	include("maintenance_obj.php");

	$empShiftMaint = new maintenanceObj();
	$sessionVars = $empShiftMaint->getSeesionVars();
	$empShiftMaint->validateSessions('','MODULES');
	
	switch($_GET['action']) 
	{
		case "Delete":
			if($empShiftMaint->maint_EmpShift("Delete",$_GET))
				echo "alert('Employee Shift of the selected Employee Successfully Deleted.');";
			else
				echo "alert('Error in Deleting the Employee Shift of the selected Employee.');";
		
		exit();
		break;
		
		case "setShiftActiveDelete":
			if($empShiftMaint->maint_EmpShift("setToActive",$_GET))
				echo "alert('Employee Shift of the selected Employee Successfully set to Active.');";
			else
				echo "alert('Error in Updating the Status of the selected Employee.');";
		exit();
		break;
		
		case "clearAllShift":
			if($empShiftMaint->maint_EmpShift("clearAllShift",$_GET))
				echo "alert('Employees Shift Successfully Deleted.');";
			else
				echo "alert('Error in Deleting Employees Shift.');";
		exit();
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
    	<div id="empMastCont"></div>
        <div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager('employee_shift_maintenance_listAjaxResult.php','empMastCont','load',0,0,'','','','../../../images/');  
	
	function maintShiftCode(act,empNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch,brnCd)
	{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:750, 
		height:410, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Employee Shift", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('employee_shift_maintenance_pop.php?&action='+act+'&empNo='+empNo);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		        pager('employee_shift_maintenance_listAjaxResult.php','empMastCont','load',0,1,'txtSrch','cmbSrch','&brnCd='+brnCd,'../../../images/');    
				Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}

	function maintShiftCodeByBranch(act,brnCd)
	{
		if(brnCd == "" || brnCd == "0")
		{
			alert('Please select a Branch.');
			return false;
		}
		var shift = new Window({
			id: "editAllw",
			className : 'mac_os_x',
			width:750, 
			height:410, 
			zIndex: 100, 
			resizable: false, 
			minimizable : true,
			title: act+" Employee Shift", 
			showEffect:Effect.Appear, 
			destroyOnClose: true,
			maximizable: false,
			hideEffect: Effect.SwitchOff, 
			draggable:true 
		})
		shift.setURL('employee_shift_maintenance_pop_by_branch.php?&action='+act+'&brnCd='+brnCd);
		shift.show(true);
		shift.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == shift) {
		        shift = null;
		        pager('employee_shift_maintenance_listAjaxResult.php','empMastCont','load',0,1,'txtSrch','cmbSrch','&brnCd='+brnCd,'../../../images/');    
				Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function delEmpShift(act,empNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the Employee Shift of the selected Employee?');
		if(deleShiftCode == true){
			var param = '?action=Delete&empNo='+empNo;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('employee_shift_maintenance_listAjaxResult.php','empMastCont','load',0,0,'','','','../../../images/');  
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
	
	function setEmpShiftActive(act,empNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to set the Employee Shift of the selected Employee to Active?');
		if(deleShiftCode == true){
			var param = '?action=setShiftActiveDelete&empNo='+empNo;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('employee_shift_maintenance_listAjaxResult.php','empMastCont','load',0,0,'','','','../../../images/');  
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
	
	function clearAllEmpShift()
	{
		var deleAllShiftCode = confirm('Are you sure you want to delete All Employees Shift?');
		if(deleAllShiftCode == true){
			var param = '?action=clearAllShift';
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('employee_shift_maintenance_listAjaxResult.php','empMastCont','load',0,0,'','','','../../../images/');  
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