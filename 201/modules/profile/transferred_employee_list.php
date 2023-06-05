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
		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		
		

		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>		
	</HEAD>
	<BODY>
		<FORM name='frmtransEmp' id="frmtransEmp" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<div id="transEmp"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	pager("transferred_employee_list_ajax.php",'transEmp','load',0,0,'','','','../../../images/');  
	
	function maintPrevEmp(act,empNo,seqNo){

		var winPrevEmp = new Window({
		id: "editPrevEmp",
		className : 'mac_os_x',
		width:450, 
		height:216, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Employee For Transfer", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		winPrevEmp.setAjaxContent('transfer_employer_act.php?transType='+act+'&empNo='+empNo+"&seqNo="+seqNo,'',true,true);
		winPrevEmp.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == winPrevEmp) {
		        winPrevEmp = null;
		       	pager("transferred_employee_list_ajax.php",'transEmp','load',0,0,'','','','../../../images/');  
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
	
	function getcompBranches(compCode) {
		if($('hdfname').value==""){
			alert('No employee to transefer!');
			$('hdfname').focus();
			return false;	
		}
		if($('cmbCompany').value==<?=$_SESSION['company_code']?>){
			alert('Error found! Selected company is equal to current company of the employee.');
			$('spNewBranch').innerHTML="Failed to load branches...";
			$('btnMaint').disabled=true;
			return false;
		}
		else{
			var ans =confirm('Are you sure you want to transfer the employee to '+$('cmbCompany').options[$('cmbCompany').selectedIndex].text+'?');
			if(ans){
				new Ajax.Request('transfer_employer_act.php?action=getCompBranches&compCode='+compCode,{
					  method : 'get',
					  onComplete : function (data){
						  $('spNewBranch').innerHTML=data.responseText;	
						  $('btnMaint').disabled=true;
					  },
					  onCreate : function (){
						  $('spNewBranch').innerHTML="Loading branches under the selected company...";
						  $('btnMaint').disabled=true;
					  }			
				  });
			}
			else{
				$('spNewBranch').innerHTML="Failed to load branches...";
				$('btnMaint').disabled=true;
				return false;			
			}
		}
	}
	
	function getEmpInfo(empNo,event) {
		var key = event.keyCode;
		if (key == 13) {
			new Ajax.Request('transfer_employer_act.php?action=getEmpInfo&empNo='+empNo,{
				  method : 'get',
				  onComplete : function (data){
					eval(data.responseText);	
				  },
				  onCreate : function (){
					  $('btnMaint').disabled = true;
				  }			
			  });
		}
	}
	
	function clearEmpTxt() {
		$('txtempNo').value = '';
	}
	
	function SaveTransEmp() {
		var trans = true;
		if (trim($('txtempNo').value)=='') {
			alert('Employee No. is required!');
			trans = false;
		}
		if ($('cmbCompany').value==0) {
			alert('New company is required!');
			trans = false;
		}		
		if ($('cmbBranch').value==0) {
			alert('New branch is required!');
			trans = false;
		}	
		if($('cmbCompany').value==<?=$_SESSION['company_code']?>){
			alert('Transfer failed. Please select company not equal to current company of the employee.');
			trans = false;	
		}
		if (trans)	{
			var ans = confirm('Continue transfer?');
			if(ans){
				new Ajax.Request('transfer_employer_act.php?action=add',{
					  method : 'get',
					  parameters : $('frmTransEmp').serialize(),
					  onComplete : function (data){
						eval(data.responseText);	
					  },
					  onCreate : function (){
						 $('btnMaint').disabled = true;
					  }			
				  });
			}
			else{
				return false;	
			}
		}
	}
	
	function DeleteTransEmp(seqNo) {
		var	ans = confirm('Are you sure you want to delete this transfer employee?');
		if (ans) {
			new Ajax.Request('transferred_employee_list_ajax.php?action=delete&seqNo='+seqNo,{
				  method : 'get',
				  onComplete : function (data){
					eval(data.responseText);	
					//pager("transferred_employee_list_ajax.php",'transEmp','load',0,0,'','','','../../../images/'); 
				  },
				  onCreate : function (){
				  }			
			  });			
		}
	}
	
	function checkValues(){
		$('frmTransEmp').serialize(true);
		if (trim($('txtempNo').value)=='' || $('cmbCompany').value==0 || $('cmbBranch').value==0) {
			$('btnMaint').disabled = true;
		}
		else{
			$('btnMaint').disabled = false;
		}
	}

	function CheckAll(){
		var cnt = $('chCtr').value;	
		for(i=0; i<=cnt; i++){
			if ($('chAll').checked==false) {
				$('chTran'+i).checked=false;
				//$('checker').value = 0;
			} else {
				$('chTran'+i).checked=true;
				//$('checker').value = 1;
			}
		}
	}

	function check(name) {
		var cnt = $('chCtr').value;
		$('checker').value = 0;
		for(i=0;i<=cnt;i++){
			if ($('chTran'+i).checked==true) {
				$('checker').value = 1;
			} 
		}
	}
	
	function transEmployee(){
		new Ajax.Request('transferred_employee_list_ajax.php?action=emptrans',{
			method : 'get',
			parameters : $('frmMinWage').serialize(),
			onComplete : function (data){
				eval(data.responseText);	
				$('btnTransfer').disabled = false;
			},
			onCreate : function (){
				$('btnTransfer').disabled = true;
			}			
		});
	}

</SCRIPT>
