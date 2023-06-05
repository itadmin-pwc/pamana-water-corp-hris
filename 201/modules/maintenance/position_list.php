<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("position.obj.php");

$posObj = new positionObj($_GET,$_SESSION);
$posObj->validateSessions('','MODULES');


if($_GET['action']=='refresh'){
	echo $cmbDiv = $_GET['divcode'];
	echo $cmbDept = substr($_GET['deptcode'],-1);//$arrPosCode[1];
}

?>
<HTML>
	<HEAD>
		<title><?=SYS_TITLE?></title>
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
		<div class="niftyCorner">

				<TABLE border="0" width="100%" height="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td colspan="8" height="90%" align="center" valign="top">
							<FORM name='frmPos' id="frmPos" method="post" action="<?=$_SERVER['PHP_SELF']?>">
								<div id="posMasterCont"></div>
								<div id="indicator1" align="center"></div>
							</FORM>
						</td>
					</tr>							
				</TABLE>

		</div>
		
	</BODY>
</HTML>
<SCRIPT>
	pager("position_list_ajax.php",'posMasterCont','load',0,0,'','','','../../../images/');  
		
	function maintPos(act,posCode,URL,ele,offset,isSearch,txtSrch,cmbSrch){
			var editPos = new Window({
			id: "editPos",
			className : 'mac_os_x',
			width:460, 
			height:230, 
			zIndex: 100, 
			resizable: false, 
			minimizable : true,
			title: act+" Position", 
			showEffect:Effect.Appear, 
			destroyOnClose: true,
			maximizable: false,
			hideEffect: Effect.SwitchOff, 
			draggable:true })
			editPos.setURL('position_div_changes.php?action='+act+'&poscode='+posCode);
			editPos.show(true);
			editPos.showCenter();	
			
			  myObserver = {
				onDestroy: function(eventName, win) {
	
				  if (win == editPos) {
					editPos = null;
					pager(URL,ele,'posMasterCont',offset,isSearch,txtSrch,cmbSrch,'','../../../images/');
					Windows.removeObserver(this);
				  }
				}
			  }
			  Windows.addObserver(myObserver);
	}
</SCRIPT>