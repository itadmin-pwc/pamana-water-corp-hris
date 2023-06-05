<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("profile_maintenance_employee.Obj.php");

$maintEmpAllwObj = new employeeAllowanceObj($_GET);

$sessionVars = $maintEmpAllwObj->getSeesionVars();
$maintEmpAllwObj->validateSessions('','MODULES');

switch ($_GET['action']){
	case 'delete':
		$deleEmpAllw = $maintEmpAllwObj->deleteEmpAllowance($sessionVars['compCode']);
		if($deleEmpAllw== true){
			echo "alert('Successfully Deleted');";
		}
		else{
			echo "alert('Deletion Failed');";
		}
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
		<div id="empAllowList"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager('profile_employee_Allowance_listAjaxResult.php','empAllowList','load',0,0,'','','&empNo=<?=$_GET['empNo']?>','../../../images/');  
	
	function deleEmpAllw(URL,ele,empNo,allwCode,offset,maxRec,isSearch,txtSrch,cmbSrch,allwDesc){

		var deleEmpAllw = confirm('Are you sure do you want to delete ?\nAllowance Type : '+allwDesc);
		
		if(deleEmpAllw == true){
			
			var param = '?action=delete&empNo='+empNo+"&allwCode="+allwCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				asynchronous : true ,
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);
					pager(URL,ele,'delete',offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&allwCode="+allwCode,'../../../images/');
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
	
	function vlidatePayTag(payTagVal){
		if(payTagVal == 'T'){
			$('imgAllwStart').style.display='';
			$('imgAllwEnd').style.display='';
		}
		else{
			$('imgAllwStart').style.display='none';
			$('imgAllwEnd').style.display='none';			
		}
	}
		
	function maintAllow(act,empNo,allwCode,URL,ele,allwCode,offset,maxRec,isSearch,txtSrch,cmbSrch){

		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:450, 
		height:250, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Allowance", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('profile_maintain_employee_allowance.php?transType='+act+'&empNo='+empNo+"&allwCode="+allwCode);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		        pager(URL,ele,'maintAllw',offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&allwCode="+allwCode,'../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}


	

	
</SCRIPT>