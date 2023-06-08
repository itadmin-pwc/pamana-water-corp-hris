<?
session_start();
include("../index.obj.php");
include("../includes/common.php");

$common = new commonObj();

$sessionVars = $common->getSeesionVars();

$moduleName = $common->getModuleName($sessionVars['moduleId']);

$common->validateSessions('','MODULES');

$arrUser = $common->getUserInfo($sessionVars['compCode'],$sessionVars['empNo'],'');

if($_GET['action'] == 'lockSys'){
	$_SESSION['system_lock'] = 1;
	exit();
}
if($_GET['action'] == 'unlockSys'){
	$qryLogIn = "SELECT * FROM tblUsers 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND   empNo    = '{$_SESSION['employee_number']}'
				AND   userPass = '".base64_encode($_GET['pass'])."'
				AND   userStat = 'A'";
	$resLogIn = $common->execQry($qryLogIn);	
	if($common->getRecCount($resLogIn) > 0){
		$_SESSION['system_lock'] = 0;
		echo 1;
	}
	else{
		echo 0;
	}
	exit();
}
?>
<html>
	<head>
	<title><?=SYS_TITLE?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<LINK rel="SHORTCUT ICON" href="../images/logo-si.png">
	<!--ext lib start -->
	<link rel="stylesheet" type="text/css" href="../js/extjs/resources/css/ext-all.css" />
	<!--<link rel="stylesheet" type="text/css" href="../js/extjs/examples/shared/examples.css" />-->
	<script type="text/javascript" src="../js/extjs/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="../js/extjs/ext-all.js"></script>
	<script type="text/javascript" src="../includes/jSLib.js"></script>
	<!--ext lib end   -->
	
	<SCRIPT type="text/javascript" src="../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
	<script type="text/javascript" src="../js/extjs/adapter/prototype/effects.js"></script>
	<script type="text/javascript" src="../js/extjs/adapter/prototype/window.js"></script>
	<script type="text/javascript" src="../js/extjs/adapter/prototype/window_effects.js"></script>
    
	<link href="../js/themes/alert.css" rel="stylesheet" type="text/css" >	 </link>
	<SCRIPT type="text/javascript" src="../includes/jSLib.js"></SCRIPT>
		
	<STYLE>@import url('../js/themes/default.css');</STYLE>
	<STYLE>@import url('../js/themes/alphacube.css');</STYLE>	
	<STYLE>@import url("../js/themes/mac_os_x.css");</STYLE>
	<STYLE>@import url('style/index_payroll.css');</STYLE>
    
    <style>
		.x-panel-body {
			overflow-y: auto;
			overflow-x: hidden;
		}
	</style>
	  
	<script type="text/javascript">
	//disableRightClick();	
	
	Ext.onReady(function(){
	   Ext.BLANK_IMAGE_URL = '../js/extjs/resources/images/default/s.gif';

		var menuTreePanel = new Ext.tree.TreePanel({
	    	id: 'tree-panel',
	    	title: '<center>MENU</center>',
	        region:'west',
	        split: true,
	        height: 260,
			width: 180,
	        minSize: 180,
			maxSize: 250,
	        autoScroll: false,
			rootVisible: false,
	        lines: true,
			singleExpand: true,
	        useArrows: true,
			animCollapse : true,
			animate: true,
			collapsible : true,
	        loader: new Ext.tree.TreeLoader({
	            dataUrl:'menu/201_menu.php'
	        }),
	        root: new Ext.tree.AsyncTreeNode()
	    });
		
		menuTreePanel.on('click', function(n){
	    	var sn = this.selModel.selNode || {}; // selNode is null on initial selection
	    	if(n.leaf){  // ignore clicks on folders and currently selected node 
	    		
	    		document.getElementById('contentFrame').src=n.id;
	    		document.getElementById('moduleName').innerHTML=n.text.toUpperCase();
				//displayDesc(n.parentNode);
	    	}
	    });
	    
		
		//reportFormPanel.render('mainFormLayer');
		new Ext.Viewport({
	        layout: 'border',
			title: 'POS',
			cls: 'testCSS',
			items: [{
				xtype: 'box',
				region: 'north',
				applyTo: 'header',
				height: 60
			},
			menuTreePanel,
		      {
	        id: 'content-panel',
			region: 'center', 
			layout: 'card',
			margins: '0 0 0 -5',
			activeItem: 0,
			border: false,
				 items: {
	             title: ' ',
	             html: '<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><td width="99%"><iframe id="contentFrame" scrolling="auto" src="" width="100%" frameborder="0"  height="100%" /></iframe></td></tr></table>'
	          }
	       }]
	    });	
	   	    
	});	
	
	</SCRIPT>
	</head>
	<body onLoad="startTime()">
		<div id="header">
        	
			<table  border="0" cellpadding="0" cellspacing="1"  align="left" width="100%">
				<tr>
					<td width="175" rowspan="4">
						<?
							if($moduleName == '201'){
						?>
						<img src="../images/201 logo.JPG" width="178" height="77" class="pgLogo">						<?
							}
						?>					</td>
			  </tr>
				<tr>
					<td width="110">
						<font  class="headerLabel">MODULE NAME</font>					</td>	
					<td width="5">
						<font  class="headerLabel">:</font>					</td>
					<td width="340">
						<font  class="headerLabelVal" id="moduleName">&nbsp;</font>					</td>	
					<td width="100">
						<font  class="headerLabel"></font>					</td>	
					<td >
						<font  class="headerLabel"></font>					</td>
					<td >
						<font  class="headerLabelVal" id="moduleName"></font>					</td>				
				</tr>
				<tr>
					<td>
						<font class="headerLabel">USERNAME</font>					</td>
					<td>
						<font class="headerLabel">:</font>					</td>	
					<td>
						<font class="headerLabelVal"><?=$arrUser['empLastName'] . ", " . $arrUser['empFirstName'] . " " . substr($arrUser['empMidName'],0,1);?></font>					</td>
					<td >
						<font  class="headerLabel">DATE</font>					</td>	
					<td >
						<font  class="headerLabel">:</font>					</td>
					<td >
						<font  class="headerLabelVal"><?=strtoupper(date("F d, Y"))?></font>					</td>
                    
                    <td width="250" align="center">
                    	<? if($_SESSION["user_level"]!='3'){  ?>
							<font class="headerLabelVal"><BLINK><img class="headerLabelVal" src="../images/icon-small-warning.gif">&nbsp;<a id="LogOut" href="#" class="headerLabelVal" onClick="listofReminders()">REMINDERS</BLINK></a><a href="#" class="headerLabelVal"></font>					
                    	<? } ?>
                    </td>				
				</tr>
				<tr>
					<td>
						<font  class="headerLabel">COMPANY</font>					</td>	
					<td>
						<font  class="headerLabel">:</font>					</td>
					<td>
						<font  class="headerLabelVal" id="compName"><?=strtoupper($common->getCompanyName($sessionVars['compCode']))?></font>					</td>	
					<td>
						<font class="headerLabel">TIME</font>					</td>
					<td>
						<font class="headerLabel">:</font>					</td>	
					<td>
						<font class="headerLabelVal" id="currTime">&nbsp;</font>					</td>	
					<td width="250" align="center">
						<font class="logOutLabel"><a id="LogOut" href="#" class="logOut" onClick="Dolock(0)">LOCK</a><a href="#" class="logOut">|</a><a href="#" id="LogOut" class="logOut" onClick="DologOut()">LOG OUT</a></font>					</td>						
				</tr>
			</table>
	</div>
		<div id="qlockSysContDiv">
			<div id="qlockSysCont">
				<TABLE align="center" border="0" width="100%">
					<TR>
						<td align="center">
							<font class='cnfrmLbl'>LOCK SYSTEM?</font>
						</td>
					</TR>
					<TR>
						<td align="center">
							<br>
							<INPUT tabindex="1" class="inputsLock" type="button" name="btnLock" id="btnLock" value="LOCK" onClick="lockSys()">
							&nbsp;&nbsp;
							<INPUT tabindex="2" class="inputsLock" type="button" name="btnCancelLock" id="btnCancelLock" value="CANCEL" onClick="cnclLockSys()">
						</td>
					</TR>
				</TABLE>
			</div>
		</div>
		<div id="passLock">
			<TABLE align="center" border="0" width="100%">
				<TR>
					<td align="center">
						<font class='cnfrmLbl'>PASSWORD</font>
					</td>
				</TR>
				<TR>
					<td align="center">
						<INPUT tabindex="1" type="password" name="txtPassLock" id="txtPassLock">
					</td>
				</TR>
				<TR>
					<td align="center">
						<br>
						<INPUT tabindex="2" class="inputsLock" type="button" name="btnUnLock" id="btnUnLock" value="UN LOCK" onClick="unlockSys()">
					</td>
				</TR>
			</TABLE>			
		</div>
		<?$common->disConnect();?>
	</body>
</html>
<SCRIPT>
	function DologOut(){
		
		Dialog.confirm("<br><center><img src='../images/icon-question.gif'>&nbsp;&nbsp;<font class='cnfrmLbl'>Do You Want To Log Out</font></center>", {
				width:300, 
				height : 125,
				okLabel: "YES", 
				className: "alphacube",
				cancelLabel : "NO",
				buttonClass: "myButtonClass", 
				id: "myDialogId", 
				cancel:function(win) {
					return false;
				}, 
				ok:function(win) {
					location.href="http://<?=$_SERVER['HTTP_HOST']."/".SYS_NAME."/?logOut=1"?>";
					return true;
				} 
		});
		Windows.getWindow('myDialogId').setZIndex(500);
	}
	
	var sessionLock = parseInt('<?=$_SESSION['system_lock']?>');
	if(sessionLock == 1){
		Dolock('<?=$_SESSION['system_lock']?>');
		$('txtPassLock').focus();
	}
	
	function Dolock(sessLock){
		var winLock = new Window({
			id : "winLcok",
			className: "alphacube", 
			resizable: false, 
			draggable:false, 
			minimizable : false,
			maximizable : false,
			closable 	: false,
			width: 300,
			height : 125
		});
			if(parseInt(sessLock) == 1){
				winLock.setContent('passLock', false, false);				
			}
			else{
				winLock.setContent('qlockSysCont', false, false);
			}
			winLock.setZIndex(500);
			winLock.setDestroyOnClose();
			winLock.showCenter(true);				
	}
	
	function listofReminders()
	{
		var editAllw = new Window({
		id: "lstReminders",
		className : 'mac_os_x',
		width:550, 
		height:350, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: "Reminders", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('reminders.php?transType');
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function lockSys(){
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=lockSys',{
			method : 'get',
			asynchronous : false,
			onComplete : function (req){
				eval(req.responseText);
				$('qlockSysContDiv').appendChild($('qlockSysCont'));
				Windows.getWindow('winLcok').setContent('passLock',false,false);
				$('txtPassLock').focus();
			}
		});
	}
	
	function cnclLockSys(){
		Windows.getWindow('winLcok').close();
	}
	
	function unlockSys(){
		var pass = $F('txtPassLock');
		if(pass == ''){
			alert('Password is Required');
			$('txtPassLock').focus();
			return false;
		}
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=unlockSys&pass='+pass,{
			method : 'get',
			asynchronous : false,
			onComplete : function (req){
				var res = parseInt(req.responseText);
				if(res == 0){
					alert('Invalid Password');
					$('txtPassLock').focus();
					return false;
				}
				else{
					Windows.getWindow('winLcok').close();
					$('txtPassLock').value="";					
				}
			}
		});		
	}
</SCRIPT>