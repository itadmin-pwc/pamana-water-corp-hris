<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<script src="../../../js/extjs/adapter/prototype/scriptaculous.js" type="text/javascript"></script>
		<script src="../../../js/extjs/adapter/prototype/unittest.js" type="text/javascript"></script>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<SCRIPT type="text/javascript" src="timesheet_js.js"></SCRIPT>		
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
	pager("customer_list_ajax.php",'empMastCont','load',0,0,'','','','../../../images/');  
	
	
	
	function viewPrevEmp(id){

		swtch = $('prevEmpCont'+id).style.display;
		if(swtch == 'none'){
			$('trPrevEmpCont'+id).style.display='';
			Effect.SlideDown('prevEmpCont'+id,{duration:1.0}); 
			return false;
		}
		else{
			Effect.SlideUp('prevEmpCont'+id,{duration:1.0});
			Effect.SlideUp('trPrevEmpCont'+id,{duration:1.0});
			return false;
		}
	}
	
	function maintPrevEmp(act,empNo,seqNo,URL,ele,offset,isSearch,txtSrch,cmbSrch){

		var winPrevEmp = new Window({
		id: "editPrevEmp",
		className : 'mac_os_x',
		width:450, 
		height:216, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Allowance", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		winPrevEmp.setAjaxContent('maintain_previous_employer.php?transType='+act+'&empNo='+empNo+"&seqNo="+seqNo,'',true,true);
		winPrevEmp.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == winPrevEmp) {
		        winPrevEmp = null;
		        pager(URL,ele,'maintPrevEmp',offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&seqNo="+seqNo,'../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function delePrevEmp(empNo,seqNo,URL,ele,offset,isSearch,txtSrch,cmbSrch,empyrName){
		var delePrevEmp = confirm('Are you sure do you want to delete?\nEmployer : ' +empyrName);
		if(delePrevEmp == true){
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=delePrevEmp&empNo='+empNo+"&seqNo="+seqNo,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager(URL,ele,'maintPrevEmp',offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&seqNo="+seqNo,'../../../images/');
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator2').innerHTML='';
				}				
			})
		}
	}
	
	function focusHandelr(act,extra){
		qryPrms = extra.toQueryParams();
		
		if(act == 'maintPrevEmp'){
			$('prevEmpCont'+qryPrms['empNo']).style.display='';
			$('trPrevEmpCont'+qryPrms['empNo']).style.display='';
		}
	}
	
	function showRegEmp(nUrl,option,recNo,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		var viewDtl = new Window({
		id: "viewDtl",
		className : 'mac_os_x',
		width:800, 
		height:550, 
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
	
	function showTransEmp(nUrl,option,recNo,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		var viewDtl = new Window({
		id: "viewDtl",
		className : 'mac_os_x',
		width:900, 
		height:450, 
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
	
	
</SCRIPT>