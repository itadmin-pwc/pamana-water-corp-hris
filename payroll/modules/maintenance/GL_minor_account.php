<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("GL_account.obj.php");

$glAcctObj = new GLAcctObj($_GET,$_SESSION);
$glAcctObj->validateSessions('','MODULES');

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
		<FORM name='frmGLAcct' id="frmGLAcct" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<div id="GlAcctCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	pager("GL_Minor_Account_List_AjaxRes.php",'GlAcctCont','load',0,0,'','','','../../../images/');  
	

	function maintGLAcct(act,GlCode,URL,ele,offset,isSearch,txtSrch,cmbSrch){

		var editGLAcct = new Window({
		id: "editGlAcct",
		className : 'mac_os_x',
		width:450, 
		height:150, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" GL Account", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editGLAcct.setURL('GL_minor_account_maintenance.php?action='+act+"&glCode="+GlCode+"&tbl=tblGLMinorAcct");
		editGLAcct.show(true);
		editGLAcct.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editGLAcct) {
		        editGLAcct = null;
		        pager(URL,ele,'GlAcctCont',offset,isSearch,txtSrch,cmbSrch,'','../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
</SCRIPT>