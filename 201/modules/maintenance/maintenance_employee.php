<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_employee.Obj.php");

$maintEmpObj = new maintEmpObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');

$maintPrevEmp = new maintPrevEmplyr($_GET,$sessionVars);


if(isset($_GET['action'])){
	
	switch ($_GET['action']){
		case 'delePrevEmp':
			if($maintPrevEmp->delePrevEmp() == true){
				echo "alert('Previous Employer Successfully Deleted');";
			}
			else{
				echo "alert('Previous Employer Deletion Failed');";
			}
			exit();
		break;
		case 'Add':
			if($maintPrevEmp->checkPrevEmp() != -1){
				
				if($maintPrevEmp->checkPrevEmp() > 0){
					echo "alert('Previous Employer Already Exist');";
				}else{
					if($maintPrevEmp->addPrevEmp() == 'true'){
						echo "alert('Previous Employer Successfully Saved');";
					}
					else{
						echo "alert('Previous Employer Failed Saved');";
					}
				}
			}
			else{
				echo "alert('Error Query Selecting Previous Employer');";
			}
			exit();
		break;
		case 'Edit':
			if($maintPrevEmp->checkPrevEmp() != -1){
				
					if($maintPrevEmp->checkPrevEmp() > 0){
						echo "alert('Previous Employer Already Exist');";
					}else{
					if($maintPrevEmp->editPrevEmp() == true){
						echo "alert('Previous Employer Successfully Updated');";
					}
					else{
						echo "alert('Previous Employer Update Failed');";
					}
				}
			}
			exit();
		break;
	}

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
	pager("employeeAjaxResult.php",'empMastCont','load',0,0,'','','','../../../images/');  
	
	
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
	
	function validatePrevEmplyr(empNo){
		
		var frmPrvEmp = $('frmMaintPrevEmp').serialize(true);
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2})$/;
		var tinExp     = /^[0-9]{3,3}-[0-9]{3,3}-[0-9]{3,3}$/;
		
		if(frmPrvEmp['emplyrName'] == ''){
			alert('Employer Name is required');
			$('emplyrName').focus();
			return false;
		}
		if(frmPrvEmp['emplyrAdd1'] == ''){
			alert('Address 1 is required');
			$('emplyrAdd1').focus();
			return false;
		}

		if(frmPrvEmp['emplyrTinNo'] == ''){
			alert('TIN Number is required');
			$('emplyrTinNo').focus();
			return false;
		}
		if(frmPrvEmp['emplyrTinNo'] != ''){
			if(!frmPrvEmp['emplyrTinNo'].match(tinExp)){
				alert('Invalid TIN Number\nvalid : 123-123-123');
				$('emplyrTinNo').focus();
				return false;
			}			
		}
	
		if(frmPrvEmp['emplyrPrevEarn'] == ''){
			alert('Previous Earnings is required');
			$('emplyrPrevEarn').focus();
			return false;
		}
		if(frmPrvEmp['emplyrPrevEarn'] != ''){
			if(!frmPrvEmp['emplyrPrevEarn'].match(numericExpWdec)){
				alert('Invalid Previous Earnings\nvalid : Numbers Only with two(2) decimal or without decimal');
				$('emplyrPrevEarn').focus();
				return false;
			}			
		}
	
		if(frmPrvEmp['emplyrPrevTax'] == ''){
			alert('Previous Taxes is required');
			$('emplyrPrevTax').focus();
			return false;
		}
		if(frmPrvEmp['emplyrPrevTax'] != ''){
			if(!frmPrvEmp['emplyrPrevTax'].match(numericExpWdec)){
				alert('Invalid Previous Taxes\nvalid : Numbers Only with two(2) decimal or without decimal');
				$('emplyrPrevTax').focus();
				return false;
			}			
		}
				
		var param = '?action='+frmPrvEmp['btnMaintPrevEmplyr']+"&empNo="+empNo;
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			parameters : $('frmMaintPrevEmp').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			},
			onCreate : function (){
				$('btnMaintPrevEmplyr').value='Loading...';
				$('btnMaintPrevEmplyr').disabled=true;
			},
			onSuccess : function (){
				$('btnMaintPrevEmplyr').value=frmPrvEmp['btnMaintPrevEmplyr'];
				$('btnMaintPrevEmplyr').disabled=false;
			}
			
		});
	}
</SCRIPT>