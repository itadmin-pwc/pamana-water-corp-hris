<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("transaction_obj.php");


$obObj = new transactionObj($_GET,$_SESSION);
$obObj->validateSessions('','MODULES');

$_SESSION['employeenumber']=$_SESSION['employee_number'];
$approverData = $obObj->getTblData("tbltna_approver", " and approverEmpNo='".$_SESSION['employee_number']."' and subordinateEmpNo='".$_SESSION['employee_number']."' and status='A' AND dateValid >= now()", "", "sqlAssoc");
$selfApprove = $approverData["approverEmpNo"] == $_SESSION['employee_number'];

switch($_GET["action"]) {

}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<SCRIPT type="text/javascript" src="../../../includes/calendar.js"></SCRIPT>
        
        
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		<STYLE>@import url('../../../includes/calendar.css');</STYLE>

	</HEAD>
	<BODY>
		<FORM name='frmTSA' id="frmTSA" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="tsaCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	
	pager('ts_correction_application_AjaxResult.php','tsaCont','load',0,0,'','','','../../../images/');  

	function newRef(act){
		pager('ts_correction_application_AjaxResult.php','tsaCont','refresh',0,0,'','','','../../../images/');  	
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+act,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
				$('cmbTrnType').focus();
				$('editEarn').innerHTML="<img src='../../../images/application_form_edit_2.png' class='toolbarImg'>";
				$('deleEarn').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
			},
			onCreate : function(){
				$('refNoCont').innerHTML='Loading...';
			},
			onSuccess : function(){
				$('refNoCont').innerHTML='';
			}
		});
	}
	
	function validateMod(mode){
		if(mode == 'EDITRENO'){
			/*$('refNo').readOnly=false;
			$('refNo').focus();*/
		}
		
		if(mode == 'REFRESH'){
			Windows.getWindow('refWin').close();
		}
	}
	
	function clearFld(){
		$('txtEmpName').value='';
	}	
	
	function getEmployee(evt,eleVal){
		
		var tsaDate = document.frmTSA.tsaDate.value;
		//var refNo = document.frmTSA.refNo.value;
		
		//var param = '?action=getEmpInfo&empNo='+eleVal+'&tsaDate='+tsaDate+'&txtRefNo='+refNo;
		var param = '?action=getEmpInfo&empNo='+eleVal+'&tsaDate='+tsaDate;
		
		var k = evt.keyCode | evt.which;
		
		switch(k){
			case 8:
				clearFld();
			break;
			case 13:
				new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
					method : 'get',
					onComplete : function (req){

						if(parseInt(req.responseText) == 0){
							$('hlprMsg').innerHTML='No Record Found';
							setTimeout(function(){
								$('hlprMsg').innerHTML='Application Detail';
							},5000);
						} 
						else{
							eval(req.responseText);
						}
					},
					onCreate : function (){
						$('hlprMsg').innerHTML='Loading...';
					},
					onSuccess : function (){
						$('hlprMsg').innerHTML='Application Detail';
					}
				})
			break;
		}
	}
	
	function getEmpShift()
	{
		var eleVal = document.frmTSA.txtAddEmpNo.value;
		var tsaDate = document.frmTSA.tsaDate.value;
		
		if(eleVal=="")
		{
			alert("Select Employee first.");
			return false;
		}
		else
		{
			var param = '?action=getEmpShiftCode&empNo='+eleVal+'&tsaDate='+tsaDate;
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
					method : 'get',
					onComplete : function (req){
	
						if(parseInt(req.responseText) == 0){
							$('hlprMsg').innerHTML='No Record Found';
							setTimeout(function(){
								$('hlprMsg').innerHTML='Application Detail';
							},5000);
						} 
						else{
							eval(req.responseText);
						}
					},
					onCreate : function (){
						$('hlprMsg').innerHTML='Loading...';
					},
					onSuccess : function (){
						$('hlprMsg').innerHTML='Application Detail';
					}
				})
		}
	}

	function saveObDetail()
	{
		var tsaFields = $('frmTSA').serialize(true);
		var tsaTimeIN = new Date(tsaFields["tsaDate"]+' '+tsaFields["schedTimeIn"]+' '+tsaFields["cmbTINAMPM"]);
		var tsaTimeOUT = new Date(tsaFields["tsaDate"]+' '+tsaFields["schedTimeOut"]+' '+tsaFields["cmbTOUTAMPM"]);
		var tsaTIN = tsaTimeIN.getHours();
		var tsaTOUT = tsaTimeOUT.getHours();
		
		if(tsaFields["txtAddEmpNo"]=="")
		{
			alert("Select Employee first.");
			$('txtAddEmpNo').focus();
			return false;
		}
		
		var params = "ts_correction_application.php?action=saveTSASched&Edited";	
		
		new Ajax.Request(params,
		{
			method : 'get',
			parameters : $('frmTSA').serialize(),
			onComplete : function(req){
				eval(req.responseText);
				pager('ts_correction_application_AjaxResult.php','tsaCont','load',0,0,'','','','../../../images/');  
			},
			onCreate : function (){
				$('indicator2').src="../../../images/wait.gif";
				document.frmTSA.btnSave.disabled = true;
//				document.frmTSA.btnApp.style.visibility = 'hidden';
//				document.frmTSA.btnDel.style.visibility = 'hidden';
			},
			onSuccess : function (){
				$('indicator2').innerHTML="";
				document.frmTSA.btnSave.disabled = false;
//				document.frmTSA.btnApp.style.visibility = 'visible';
//				document.frmTSA.btnDel.style.visibility = 'visible';
			}	
		});
		
	}
	
	function delTsaTran(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected TS Correction Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Delete&seqNo='+seqNo;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmTSA').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('ts_correction_application_AjaxResult.php','tsaCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmTSA.btnSave.disabled = true;
//					document.frmTSA.btnApp.style.visibility = 'hidden';
//					document.frmTSA.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmTSA.btnSave.disabled = false;
//					document.frmTSA.btnApp.style.visibility = 'visible';
//					document.frmTSA.btnDel.style.visibility = 'visible';
				}
			});	
			
			
		}
	}
	
	function upTSATran(act,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to Approved the selected TS Correction Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Approved';
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmTSA').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('ts_correction_application_AjaxResult.php','tsaCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmTSA.btnSave.disabled = true;
//					document.frmTSA.btnApp.style.visibility = 'hidden';
//					document.frmTSA.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmTSA.btnSave.disabled = false;
//					document.frmTSA.btnApp.style.visibility = 'visible';
//					document.frmTSA.btnDel.style.visibility = 'visible';
				}
			});	
			
			
		}
	}
	
	function disTsaTran(id,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('This process will Cancel the selected Approved TS Correction Application! Continue process?');
		
		if(deleShiftCode == true){
			var param = '?action=disapprove&id='+id;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmTSA').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('ts_correction_application_AjaxResult.php','tsaCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmTSA.btnSave.disabled = true;
//					document.frmTSA.btnApp.style.visibility = 'hidden';
//					document.frmTSA.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmTSA.btnSave.disabled = false;
//					document.frmTSA.btnApp.style.visibility = 'visible';
//					document.frmTSA.btnDel.style.visibility = 'visible';
				}
			});	
		}
	}
	
	function checkAll(field)
	{
		var chktsa = document.frmTSA.elements['chkseq[]'];
		//alert(field);
		if (field=="1") 
		{ 
   			for (var i=0; i<chktsa.length; i++)
			{
				chktsa[i].checked = true;
    		}
				return "0";  
  		} 
		else 
		{
			
    		for (var i=0; i<chktsa.length; i++)
			{
				chktsa[i].checked = false;
    		}
				return "1";  
 		}
	}
	
	function getSeqNo()
	{
		var param = '?action=getSeqNo';
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			parameters : $('frmTSA').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}
</SCRIPT>