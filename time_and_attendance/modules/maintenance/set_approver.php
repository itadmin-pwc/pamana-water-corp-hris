<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("set_approver.obj.php");
include("maintenance_obj.php");

$AppObj = new ApprObj();
$AppObj->validateSessions($_GET, $_SESSION);
unset($_SESSION['employeenumber']);
$_SESSION['employeenumber']=$_SESSION['employee_number'];

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		
		case 'addNewApprover':
			$empno=$_GET['empno'];
			if($empno!=''){
				$_SESSION['employeenumber']=$empno;
			}
			else{
				unset($_SESSION['employeenumber']);
			}
		break;
	
		case ('addDtl'): //save OT App
						//validate if record is existing  		
			$shiftCodeDtl = $AppObj->getTblData("tbltna_approver", " and approverEmpNo='".$_GET['approverEmpNo']."' and subordinateEmpNo='".$_GET['subordinateEmpNo']."' ", "", "sqlAssoc");			
			
			if($shiftCodeDtl["approverEmpNo"] != ''){
				echo "'".$shiftCodeDtl["approverEmpNo"]."';";
				echo "alert('Duplicate Approver Record.');";
			}else{
					if($AppObj->addApprover() == true) {
						$empno=$_GET['approverEmpNo'];
						echo "$('txtAddEmpNo2').value='';";
						echo "$('txtAddEmpName2').value='';";
						//echo 2;//successfuuly saved
						echo "
							var ans = confirm('Approver has been saved! Would you like to add new Approver Record?');
							if(ans==true){
								location.href='set_approver.php?action=addNewApprover&empno=$empno';
							}
							else{
								location.href='set_approver.php';
							}";
						
					}else{
						echo "alert('Saving of OT Application failed.');";
					}
			
			}
			exit();
		break;
	
		case 'getEmpInfo':
			
			$empInfo = $AppObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');

			$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
			if($_GET['type'] == 'sub') {
				echo "$('txtAddEmpName2').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";
			}else{
				echo "$('txtAddEmpName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";
			}

			// $level = $empInfo['empLevel'];
			// if ($level > '70'){
			// 	echo "alert('This employee is OT exempted');";
			// }
				
			exit();			
		break;
		
		case 'Delete':
			$chkSeqNo = $_GET["chkseq"];

			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryDel = "UPDATE tbltna_approver SET status='V' WHERE ID='".$chkSeqNo_val."'";
				$resDel = $AppObj->execQry($qryDel);
			}

			echo "alert('Selected Approver Successfully Voided.')";
			
		exit();
		break;
		
		case 'Active':
			
			
			$chkSeqNo = $_GET["chkseq"];
			
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			
			{
				$qryApprove = "UPDATE tbltna_approver SET status='A', updatedAt='".date("Y-m-d H:i:s")."', updatedBy='".$_SESSION["employee_number"]."' WHERE ID=".$chkSeqNo_val.";";
				$resApprove = $AppObj->execQry($qryApprove);
			}

			echo "alert('Selected approver record successfully as Active.')";
			
		exit();
		break;
		
		case "getSeqNo":
			$chkSeqNo = $_GET["chkseq"];
			
			if(sizeof($chkSeqNo)>1)
			{
				echo "alert('Select 1 Approver Record to be updated.')";
			}
			else
			{
				foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
				{
					$inputTypeSeqNo = $chkSeqNo_val;
				}
				
				echo "UpdateRdTran('".$inputTypeSeqNo."');";
			}
			exit();
		break;
	
	default:
		
	break;
	}
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
		
		<script type="text/javascript" src="../../../includes/calendar.js"></script>	
		<STYLE>@import url('../../../includes/calendar.css');</STYLE>
	</HEAD>
<BODY>
	<FORM name='frmApprover' id="frmApprover" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		<div id="approverCont"></div>
		<div id="indicator1" align="center"></div>
	</FORM>
</BODY>

</HTML>

<SCRIPT>
	pager('set_approverAjaxResult.php','approverCont','load',0,0,'','','&empNo='<?=$_GET["empNo"]?>,'../../../images/'); 

	function validateMod(mode){
		if(mode == 'EDITRENO'){
			$('newOt').innerHTML="<img src='../../../images/application_add_2.png' class='toolbarImg'>";
			$('deleEarn').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
			$('btnUpdtHdr').disabled=true;
			$('refNo').readOnly=false;
			$('refLookup').disabled=false;
			$('btnSaveAddDtl').disabled=true;
			$('refNo').focus();
		}
		
		if(mode == 'REFRESH'){
			Windows.getWindow('refWin').close();
		}
	}

	function maintApp(URL,ele,action,intOffSet,isSearch,txtSearch,cmbSearch,extra,id,empName){
		
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2}|-[\d]+|-[\d]+\.[\d]{1,2})$/;
		var empNo = '';
		var cntrlNo = '';
		var extraParam = '';
		var param = '';

		var arrEle = $('frmApprover').serialize(true);
		
		if(action == 'addDtl') {
			
			if(arrEle['txtAddEmpNo'] == ''){
				alert('Approver Employee No. is Required.');
				$('txtAddEmpNo').focus();
				return false;
			}

			if(arrEle['txtAddEmpNo2'] == ''){
				alert('Subordinate Employee No. is Required.');
				$('txtAddEmpNo2').focus();
				return false;
			}

			var approverEmpNo = arrEle['txtAddEmpNo'];
			var subordinateEmpNo = arrEle['txtAddEmpNo2'];
			
			var param ='&approverEmpNo='+approverEmpNo+'&subordinateEmpNo='+subordinateEmpNo;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=addDtl'+param,{
				method : 'get',
				
				onComplete : function (req){
					eval(req.responseText);
//					var blnAdd = confirm("Add another OT Application for this employee?");
//					if (blnAdd != true){
//						pager('otAppAjaxResult.php','otAppCont','load',0,0,'','','&empNo='<?//=$_GET["empNo"]?>,'../../../images/'); 
//					}
				},
				onCreate : function (){
					$('indicator1').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator1').innerHTML="";
					
				}
			})
		}
		
		if(action == 'editAppDtl'){
			var approverEmpNo = $('txtAddEmpNo'+id).innerHTML;
			var subordinateEmpNo = $('txtAddEmpNo2_'+id).innerHTML;
			
			editAppDtl = confirm('Are you sure do you want to edit ?\nApprover EmpNo.: ' +approverEmpNo + ' \nSubordinate EmpNo.: ' + subordinateEmpNo);
			
			if(editAppDtl == false){
				return false;
			}

			var param ='&id='+id+'&approverEmpNo='+approverEmpNo+'&subordinateEmpNo='+subordinateEmpNo;;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=editAppDtl'+param,{
				method : 'get',
				
				onComplete : function (req){
					eval(req.responseText);
				},
				onCreate : function (){
					$('refNoCont').innerHTML='Loading...';
				},
				onSuccess : function (){
					$('refNoCont').innerHTML='';
				}
			})
		}//end deleDedDtl
		
		if(action == 'delAppDtl'){
			deleOtApp = confirm('Do You Want to Delete Approver EmpNo.: ' +approverEmpNo + ' and Subordinate EmpNo.: ' + subordinateEmpNo);
			
			if(deleOtApp == false){
				return false;
			}
			var param ='&id='+id;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=delAppDtl'+param,{
				method : 'get',
				
				onComplete : function (req){
					eval(req.responseText);
					pager('set.php','approverCont','load',0,0,'','','','../../../images/'); 
				},
				onCreate : function (){
					$('refNoCont').innerHTML='Loading...';
				},
				onSuccess : function (){
					$('refNoCont').innerHTML='';
				}
			})
		}
	}

	function UpdateRdTran(inputTypeSeqNo)
	{
		if(inputTypeSeqNo==""){
			alert('Please select Over time application to be modified.');
		}
		else{
			var editAllw = new Window({
			id: "editAllw",
			className : 'mac_os_x',
			width:850, 
			height:410, 
			zIndex: 100, 
			resizable: false, 
			parameters : $('frmApprover').serialize(),
			minimizable : true,
			showEffect:Effect.Appear, 
			destroyOnClose: true,
			maximizable: false,
			hideEffect: Effect.SwitchOff, 
			draggable:true })
			editAllw.setURL('overtime_popup.php?&inputTypeSeqNo='+inputTypeSeqNo);
			editAllw.show(true);
			editAllw.showCenter();	
			
			  myObserver = {
				onDestroy: function(eventName, win) {
	
				  if (win == editAllw) {
					editAllw = null;
				   pager('set_approverAjaxResult.php','approverCont','load',0,0,'','','','../../../images/');  
					Windows.removeObserver(this);
				  }
				}
			  }
			  Windows.addObserver(myObserver);
		}
	}

	function updateTran(act,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Sure to set the selected record as active?');
		
		if(deleShiftCode == true){
			var param = '?action=Active';
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmApprover').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('set_approverAjaxResult.php','approverCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
				}
			});	
			
			
		}
	}

	function delAppDtl(act,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var voidApprover = confirm('Are you sure you want to void the selected Approver Record?');
		
		if(voidApprover == true){
			var param = '?action=Delete';
			
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmApprover').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('set_approverAjaxResult.php','approverCont','load',0,0,'','','','../../../images/'); 
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
				}
			});	
			
			
		}
	}

	function getEmployee(evt,eleVal,_type){
		var param = '?action=getEmpInfo&empNo='+eleVal+'&type='+_type;

		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			onComplete : function (req){

				if(parseInt(req.responseText) == 0){
					$('hlprMsg').innerHTML='No Record Found';
					setTimeout(function(){
						$('hlprMsg').innerHTML='List of Approver';
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
				$('hlprMsg').innerHTML='List of Approver';
			}
		})
	}

	function getSeqNo()
	{
		var param = '?action=getSeqNo&seqNo=';
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			parameters : $('frmApprover').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}

	function checkAll(field)
	{
		var chkob = document.frmApprover.elements['chkseq[]'];
		//alert(field);
		if (field=="1") 
		{ 
   			for (var i=0; i<chkob.length; i++)
			{
				chkob[i].checked = true;
    		}
				return "0";  
  		} 
		else 
		{

    		for (var i=0; i<chkob.length; i++)
			{
				chkob[i].checked = false;
    		}
				return "1";  
 		}
	}
</SCRIPT>