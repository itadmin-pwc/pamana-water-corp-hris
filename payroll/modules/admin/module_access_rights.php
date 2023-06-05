<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("module_access_right.obj.php");

$moduleAccssRghts = new moduleAccessRightsObj($_SESSION,$_GET);
$sessionVars = $moduleAccssRghts->getSeesionVars();
$moduleAccssRghts->validateSessions('','MODULES');



if($_GET["createAcct"]=='createAccount')
{
	$rsEmpInfo = $moduleAccssRghts->getUserInfo($_GET["compCode"],$_GET["employeeNo"],'');
	$userPass = strtoupper(substr($rsEmpInfo["empFirstName"],0,$insval.length + 1)).strtoupper($rsEmpInfo["empLastName"]);
	$conV_userPass = base64_encode($userPass);
	
	//Check if the User exists in the tblusers
	$rsChkUsers = $moduleAccssRghts->chkUser($_GET["compCode"],$_GET["employeeNo"]);
	$cntChkUsers = $moduleAccssRghts->getRecCount($rsChkUsers);
	
	if($cntChkUsers=='1'){
		echo "UpdateAcct('".$_GET["employeeNo"]."','".$_GET["compCode"]."');";
		exit();
	}
	else{
		//Create User Password
		$insNewUsrAcct = $moduleAccssRghts->insNewUserAcct($_GET["compCode"],$_GET["employeeNo"],$conV_userPass);
		if($insNewUsrAcct==1)
			echo "RefreshPage();";
		else
			echo "alert('Password Unsuccessfully Created.');";
		exit();
	}
}

if($_GET["UpdateAccount"]=="UptAcct"){
	$rsEmpInfo = $moduleAccssRghts->getUserInfo($_GET["compCode"],$_GET["employeeNo"],'');
	$userPass = strtoupper(substr($rsEmpInfo["empFirstName"],0,$insval.length + 1)).strtoupper($rsEmpInfo["empLastName"]);
	$conV_userPass = base64_encode($userPass);
	
	$updateUsrAcct = $moduleAccssRghts->updateUserAcct($_GET["compCode"],$_GET["employeeNo"], $conV_userPass);
	if($updateUsrAcct==1)
		echo "alert('User Password Already Updated.');";
	else
		echo "alert('Unsuccessful Password Updated.');";
	
	exit();
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
		<!--<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>-->
	</HEAD>
	<BODY>
		<div id="empList"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager("employee_listAjaxResult.php","empList",'load',0,0,'','','','../../../images/');  
	

	function createUserAccount(employeeNo,compCode)
	{
		var chckIfUserWants = confirm('Are you sure you want to generate the password of the Selected Employee? ');
		if(chckIfUserWants == true)
		{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?&createAcct=createAccount&employeeNo='+employeeNo+'&compCode='+compCode,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
				}			
			})
		}
	}
	
	function UpdateAcct(employeeNo,compCode)
	{
		var chckIfUserWants = confirm('User Account of the Selected Employee, Already Exists!, Do you want to Update his/her Existing User Password?');
		if(chckIfUserWants == true)
		{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?&UpdateAccount=UptAcct&employeeNo='+employeeNo+'&compCode='+compCode,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
				}			
			})
		}
	}
	
	function RefreshPage()
	{
		alert("Password Already Created.");
		pager("employee_listAjaxResult.php","empList",'load',0,0,'','','','../../../images/');  
	}
	
	function editUserAccount(act, empNo, compCode)
	{
		var winPrevEmp = new Window({
		id: "editUserAccess",
		className : 'mac_os_x',
		width:500, 
		height:200, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act + " User Access", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		winPrevEmp.setURL('module_access_pop.php?act='+act+'&empNo='+empNo+'&compCode='+compCode);
		winPrevEmp.showCenter(true);	
		
		 myObserver = {
			onDestroy: function(eventName, win) {

			  if (win == winPrevEmp) {
				winPrevEmp = null;
				Windows.removeObserver(this);
			  }
			}
		  }
		  Windows.addObserver(myObserver);
		
	}

</SCRIPT>