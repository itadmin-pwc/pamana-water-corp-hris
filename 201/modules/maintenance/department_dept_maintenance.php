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
						<FORM name='frmDdept' id="frmDdept" method="post" action="<?=$_SERVER['PHP_SELF']?>">
							<div id="deptMasterCont"></div>
							<div id="indicator1" align="center"></div>
						</FORM>						
					</td>
				</tr>							
				<tr>
					<td colspan="8" height="25" align="center" class="childGridFooter">
						<INPUT type="button" name="btnDiv" id="btnDiv" value="DIVISION" class="inputs" onClick="location.href='department.php'">
						<INPUT type="button" name="btnDept" id="btnDept" value="DEPARTMENT" class="inputs" onClick="location.href='department_dept_maintenance.php'">
						<INPUT type="button" name="btnSect" id="btnSect" value="SECTION" class="inputs" onClick="location.href='department_sect_maintenance.php'">
					</td>
				</tr>
			</TABLE>
		</div>
	</BODY>
</HTML>
<SCRIPT>
	pager("department_dept_listAjaxRes.php",'deptMasterCont','load',0,0,'','','','../../../images/');  
	
	function filterDept(divCode){
	
			pager("department_dept_listAjaxRes.php",'deptMasterCont','load',0,1,'','','&divCode='+divCode+"&srchType2=1",'../../../images/');  
		
	}
	
	function maintDept(act,deptCode,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		
		
		if($F('cmbDiv') == 0){
			alert('Division is Required');
			$('cmbDiv').focus();
			return false;
		}
		
		var editDept = new Window({
		id: "editDept",
		className : 'mac_os_x',
		width:450, 
		height:165, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Department", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editDept.setURL('department_dept_changes.php?action='+act+"&divCode="+$F('cmbDiv')+"&deptCode="+deptCode);
		editDept.show(true);
		editDept.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editDept) {
		        pager(URL,ele,'deptMasterCont',offset,isSearch,txtSrch,cmbSrch,'&divCode='+$F('cmbDiv')+"&srchType2=1",'../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
		
	}
</SCRIPT>