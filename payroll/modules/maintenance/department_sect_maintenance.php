<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("department.obj.php");

$deptObj = new deptObj($_GET,$_SESSION);
$deptObj->validateSessions('','MODULES');

if($_GET['action'] == 'populateCmbDept'){                  
	echo $deptObj->DropDownMenu($deptObj->makeArr2($deptObj->getDepartment($_SESSION['company_code'],$_GET['divCode'],'','',2
						   					),'divCode','deptCode','deptDesc',''
						   ),'cmbDept',$cmbDept,'class="inputs" onchange="filterDept(document.getElementById(\'cmbDiv\').value,this.value)" style="width:200px;"'
	);
	exit();
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
						<FORM name='frmSect' id="frmSect" method="post" action="<?=$_SERVER['PHP_SELF']?>">
							<div id="sectMasterCont"></div>
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
	pager("department_sect_listAjaxRes.php",'sectMasterCont','load',0,0,'','','','../../../images/');  
	
	function filterDept(divCode,deptCode){
		if($F('cmbDiv') == 0){
			alert('Division is Required');
			$('cmbDept').value=0;
			$('cmbDiv').focus();
			return false;
		}
		else{
			if(deptCode != 0){
				pager("department_sect_listAjaxRes.php",'sectMasterCont','load',0,1,'','','&divCode='+divCode+"&deptCode="+deptCode+"&srchType2=1&srchType3=1",'../../../images/');  
			}
		}
	}	
	
	function populateCmbDept(divCode){
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=populateCmbDept&divCode='+divCode,{
			method : 'get',
			onComplete : function(req){
				$('deptCont').innerHTML=req.responseText;
			},
			onCreate : function(){
				$('deptCont').innerHTML='loading...';
			}
		});
	}
	
	function maintSect(act,sectCode,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		
		
		if($F('cmbDiv') == 0){
			alert('Division is Required');
			$('cmbDiv').focus();
			return false;
		}
		if($F('cmbDept') == 0){
			alert('Department is Required');
			$('cmbDept').focus();
			return false;
		}		
		
		
		var editSect = new Window({
		id: "editSect",
		className : 'mac_os_x',
		width:450, 
		height:165, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Section", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editSect.setURL('department_sect_changes.php?action='+act+"&divCode="+$F('cmbDiv')+"&deptCode="+$F('cmbDept')+"&sectCode="+sectCode);
		editSect.show(true);
		editSect.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editSect) {
		        pager(URL,ele,'sectMasterCont',offset,isSearch,txtSrch,cmbSrch,'&divCode='+$F('cmbDiv')+"&deptCode="+$F('cmbDept')+"&srchType2=1&srchType3=1",'../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
		
	}
</SCRIPT>