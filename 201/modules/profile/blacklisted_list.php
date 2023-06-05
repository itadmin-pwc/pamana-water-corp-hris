<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/24/2010
	Function		:	Blacklist Module (Main Page) 
*/

session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("blacklisted_obj.php");

$blackListObj = new blackListObj();
if ($_GET['action']=='delete') {
		  if($blackListObj->delEmptblBlackListed($_GET['blackNo'])){
			  echo "alert('Successfully Deleted.');";
		  }else{
			  echo "alert('Deletion Failed.');";
		  }	
	exit();	
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<script src="../../../js/extjs/adapter/prototype/scriptaculous.js" type="text/javascript"></script>
		<script src="../../../js/extjs/adapter/prototype/unittest.js" type="text/javascript"></script>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		
		

		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>		
	</HEAD>
	<BODY>
		<FORM name='frmEmpMast' id="frmEmpMast" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<div id="empMastCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	pager("blacklisted_list_ajax.php",'empMastCont','load',0,0,'','','','../../../images/');  
	
	function blackList_Pop(empNo,mode)
	{
		
		var winPrevEmp = new Window({
		id: "blacklist",
		className : 'mac_os_x',
		width:900, 
		height:415, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: " BlackList Employee", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		winPrevEmp.setURL('blacklisted_pop.php?&empNo='+empNo+'&mode='+mode);
		winPrevEmp.showCenter(true);	
		
		 myObserver = {
			onDestroy: function(eventName, win) {

			  if (win == winPrevEmp) {
				winPrevEmp = null;
				Windows.removeObserver(this);
				pager("blacklisted_list_ajax.php",'empMastCont','load',0,0,'','','','../../../images/');  
			  }
			}
		  }
		  Windows.addObserver(myObserver);
	}
	
	function blackList_Print(empNo,mode)
	{
		var winPrevEmp = new Window({
		id: "blacklist",
		className : 'mac_os_x',
		width:900, 
		height:415, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: " Print Employee Blacklist Information Prooflist", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		winPrevEmp.setURL('blacklisted_print_pop.php?&empNo='+empNo+'&mode='+mode);
		winPrevEmp.showCenter(true);	
		
		 myObserver = {
			onDestroy: function(eventName, win) {

			  if (win == winPrevEmp) {
				winPrevEmp = null;
				Windows.removeObserver(this);
				pager("blacklisted_list_ajax.php",'empMastCont','load',0,0,'','','','../../../images/');  
			  }
			}
		  }
		  Windows.addObserver(myObserver);
	}
	
	function del_Blacklist(blackNo) {
		var ans = confirm('Are you sure you want to delete this record?')
		if (ans) {
			new Ajax.Request('<?php $_SESSION['PHP_SELF'];?>?&action=delete&blackNo='+blackNo,{
				method : 'get',
				onComplete : function(req){
					eval(req.responseText);
					pager("blacklisted_list_ajax.php",'empMastCont','load',0,0,'','','','../../../images/');  
				}
			});		
		}
	}
	
</SCRIPT>