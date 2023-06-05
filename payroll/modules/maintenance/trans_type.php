<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("trans_type.obj.php");
$trnsTypeObj = new trnsTypeObj($_GET,$_SESSION);
$trnsTypeObj->validateSessions('','MODULES');
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
		<FORM name='frmTransType' id="frmTransType" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<div id="trnsTypeMasterCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>			
	</BODY>
</HTML>
<SCRIPT>
	pager("trans_type_listAjaxRes.php",'trnsTypeMasterCont','load',0,0,'','','','../../../images/');  
	
	function maintTrnsType(act,trnCode,URL,ele,offset,isSearch,txtSrch,cmbSrch){

		var editTrn = new Window({
		id: "editTrn",
		className : 'mac_os_x',
		width:450, 
		height:300, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Transaction Type", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editTrn.setURL('trans_type_changes.php?action='+act+"&trnCode="+trnCode);
		editTrn.show(true);
		editTrn.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editTrn) {
		        pager(URL,ele,'trnsTypeMasterCont',offset,isSearch,txtSrch,cmbSrch,'','../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
</SCRIPT>