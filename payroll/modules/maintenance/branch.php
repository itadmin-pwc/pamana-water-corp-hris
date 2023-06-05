<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("branch.obj.php");
$brnchObj = new branchObj($_GET,$_SESSION);
$brnchObj->validateSessions('','MODULES');
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT src="../../../includes/jSLib.js" type="text/javascript"></SCRIPT>
		<SCRIPT src="../../../js/extjs/adapter/prototype/prototype.js" type="text/javascript"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>	
	</HEAD>
	<BODY>
		<FORM name='frmBranch' id="frmBranch" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<div id="branchMasterCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>			
	</BODY>
</HTML>
<SCRIPT>
	pager("branch_listAjaxRes.php",'branchMasterCont','load',0,0,'','','','../../../images/');  
	
	function maintBranch(act,brnCode,URL,ele,offset,isSearch,txtSrch,cmbSrch){
				
		var editBrnch = new Window({
		id: "editBrnch",
		className : 'mac_os_x',
		width:500, 
		height:400, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" BRANCH", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editBrnch.setURL('branch_maintenance.php?action='+act+"&brnCode="+brnCode);
		editBrnch.show(true);
		editBrnch.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editBrnch) {
		        pager(URL,ele,'branchMasterCont',offset,isSearch,txtSrch,cmbSrch,'','../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
		
	}
	
	
</SCRIPT>