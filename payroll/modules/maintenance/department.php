<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("department.obj.php");

$deptObj = new deptObj($_GET,$_SESSION);
$deptObj->validateSessions('','MODULES');
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
							<FORM name='frmDiv' id="frmDiv" method="post" action="<?=$_SERVER['PHP_SELF']?>">
								<div id="divMasterCont"></div>
								<div id="indicator1" align="center"></div>
							</FORM>
						</td>
					</tr>							
					<tr>
						<td colspan="8" height="25" align="center" class="childGridFooter">
							<INPUT type="button" name="btnDiv" id="btnDiv" value="DIVISION" class="inputs" onclick="location.href='department.php'">
							<INPUT type="button" name="btnDept" id="btnDept" value="DEPARTMENT" class="inputs" onclick="location.href='department_dept_maintenance.php'">
							<INPUT type="button" name="btnSect" id="btnSect" value="SECTION" class="inputs" onclick="location.href='department_sect_maintenance.php'">
						</td>
					</tr>
				</TABLE>

		</div>
		
	</BODY>
</HTML>
<SCRIPT>
	pager("department_div_listAjaxRes.php",'divMasterCont','load',0,0,'','','','../../../images/');  
	
	function maintDiv(act,divCode,URL,ele,offset,isSearch,txtSrch,cmbSrch){

		var editDiv = new Window({
		id: "editDiv",
		className : 'mac_os_x',
		width:450, 
		height:225, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Division", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editDiv.setURL('department_div_changes.php?action='+act+"&divCode="+divCode);
		editDiv.show(true);
		editDiv.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editDiv) {
		        editDiv = null;
		        pager(URL,ele,'divMasterCont',offset,isSearch,txtSrch,cmbSrch,'','../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
</SCRIPT>