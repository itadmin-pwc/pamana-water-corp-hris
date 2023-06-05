<?
session_start();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='timesheet_js.js'></script>
<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
<STYLE>@import url('../../../js/themes/default.css');</STYLE>
<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<? if ($_SESSION['pay_category'] == '9') {?>
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
<? } else {?><br /><br /><br />
		<div align="center" style=" font-size:20px; color:#F00; font-family:Verdana; "> This Module is available only under "Resigned" Category!</div>
<? }?>
</body>
</body>
</html>
<? if ($_SESSION['pay_category'] == '9') {?>
<SCRIPT>
	pager("resigned_employee_list_ajax.php","TSCont",'load',0,0,'','','','../../../images/');  
	function PopUp(nUrl,option,empNo,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		if(option == "Unused Leaves") {
			var wd = 448;
			var ht = 165;
		} else {
			var wd = 648;
			var ht = 398;
		}
		nUrl = nUrl +'?empNo='+empNo;
		var viewDtl = new Window({
		id: "viewDtl",
		className : 'mac_os_x',
		width:wd, 
		height:ht, 
		zIndex: 100, 
		resizable: false, 
		minimizable : false,
		title: option, 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		viewDtl.setURL(nUrl);
		viewDtl.show(true);
		viewDtl.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == viewDtl) {
		        viewDtl = null;
				pager("resigned_employee_list_ajax.php","TSCont",'load',0,0,'','','','../../../images/');  
				Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}		
</SCRIPT>
<? } ?>