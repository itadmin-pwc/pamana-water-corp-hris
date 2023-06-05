<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("gov_pay.obj.php");

$govPayObj = new govPayObj($_GET);
$sessionVars = $govPayObj->getSeesionVars();
$govPayObj->validateSessions('','MODULES');

if($_GET["action"]=='delGovPay')
{
	$resDelGovPayment = $govPayObj->delGovPayment($_GET["seqNo"]);
	if($resDelGovPayment==true){
		echo "alert('Record was sucessfully deleted.');";
	}else{	
		echo "alert('Record was unsucessfully deleted.');";
	}
	exit();
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>		</HEAD>
	<BODY>
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	//var orderBy=document.frmGovPaymentList.cmbOrderBy.value;
	
	pager('government_payments_list_ajax.php','TSCont','load',0,0,'','','','../../../images/');  
	
	
	function PopUp(nUrl,option,recNo,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		if (option=="Add Government Payments" || option=="Edit Government Payments") {
			var wd = 448;
			var ht = 350;
		} 
		
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
		        pager(URL,ele,'load',offset,isSearch,txtSrch,cmbSrch,'','../../../images/');
				Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function deleGovPayment(seqNo)
	{
		var delGovPay = confirm('Are you sure you want to delete the selected record? ');
		if(delGovPay == true)
		{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?&action=delGovPay&seqNo='+seqNo,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('government_payments_list_ajax.php','TSCont','load',0,0,'','','','../../../images/');  
				}			
			})
		}
	}
	
	
</SCRIPT>