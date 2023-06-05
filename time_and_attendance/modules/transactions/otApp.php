<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("otApp.obj.php");
include("transaction_obj.php");

$otAppObj = new otAppObj();
$otAppObj->ValidateSessions($_GET, $_SESSION);
unset($_SESSION['employeenumber']);
if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		
		case 'addNewOTApp':
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
			$shiftCodeDtl = $otAppObj->getTblData("tblTk_OTApp", " and empNo='".$_GET['empNo']."' and otDate='".date("Y-m-d", strtotime($_GET["dateOt"]))."'", "", "sqlAssoc");			
			
			if($shiftCodeDtl["empNo"] != ''){
				echo "'".$shiftCodeDtl["empNo"]."';";
				echo "alert('Duplicate Entry of OT Application.');";
			}else{
					if($otAppObj->addOTApp() == true) {
						$empno=$_GET['empNo'];
						echo "$('cmbReasons').value=0;";
						echo "$('dateOt').value='';";
						echo "$('txtOtIn').value='';";
						echo "$('txtOtOut').value='';";
						echo "$('txtAddEmpName').value='';";
						echo "$('chkCrossDate').checked=false;";
						//echo 2;//successfuuly saved
						echo "
							var ans = confirm('OT Application has been saved! Would you like to add new OT Application?');
							if(ans==true){
									
									location.href='otApp.php?action=addNewOTApp&empno=$empno';
						}
							else{
									
									location.href='otApp.php';
						}";
						
					}else{
						echo "alert('Saving of OT Application failed.');";
					}
			
			}
			exit();
		break;
	
		case 'getEmpInfo':
			
			$empInfo = $otAppObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');

				$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
				echo "$('txtAddEmpName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";

				$level = $empInfo['empLevel'];
				if ($level > '70'){
					echo "alert('This employee is OT exempted');";
				}
				
			exit();			
		break;
		
		case 'checkShift':
			$shiftCodeDtl = $otAppObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("Y-m-d", strtotime($_GET["dateOt"]))."'", "", "sqlAssoc");
			if($shiftCodeDtl["shftTimeOut"] != ''){
				echo "$('txtOtIn').value='".$shiftCodeDtl["shftTimeOut"]."';";
			}else{
				echo "alert('Employee has no shift schedule for this date.');";
			}
			exit();
		break;
		
		
		case 'Delete':
		
			$chkSeqNo = $_GET["chkseq"];

			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
			$qryDel = "DELETE FROM tblTK_OTApp WHERE seqNo='".$chkSeqNo_val."'";
			$resDel = $otAppObj->execQry($qryDel);
			}

			echo "alert('Selected Overtime Application already deleted.')";
			
		exit();
		break;
		
		case 'Approved':
			
			
			$chkSeqNo = $_GET["chkseq"];
			
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			
			{
				$qryApprove = "UPDATE tblTK_OTApp SET dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."', 
							   otStat='A' WHERE seqNo='".$chkSeqNo_val."';";
				$resApprove = $otAppObj->execQry($qryApprove);
			}

			echo "alert('Selected Overtime Application already approved.')";
			
		exit();
		break;
		
		
		case 'checkEmployee':
			if ($otAppObj->checkEmployee() == true){
				echo "alert('This employee is OT exempted');";
			}
		break;
		
		case "getSeqNo":
		$chkSeqNo = $_GET["chkseq"];
		
		if(sizeof($chkSeqNo)>1)
		{
			echo "alert('Select 1 OT Application to be Modified.')";
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
	<FORM name='frmOtApp' id="frmOtApp" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="otAppCont"></div>
			<div id="indicator1" align="center"></div>
	</FORM>
</BODY>

	
</HTML>

<SCRIPT>
	disableRightClick()
	
	pager('otAppAjaxResult.php','otAppCont','load',0,0,'','','&empNo='<?=$_GET["empNo"]?>,'../../../images/'); 
	
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
	
	function newRef(act){
		
		pager('otAppAjaxResult.php','otAppCont','refresh',0,0,'','','','../../../images/');  
			
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
	
	
	function getEmployee(evt,eleVal){

		var param = '?action=getEmpInfo&empNo='+eleVal;

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

	
	function viewLookup(){

		var RefWin = new Window({
			id: "refWin",
			className : 'mac_os_x',
			width:600, 
			height:400, 
			zIndex: 100, 
			resizable: false, 
			title: "Earnings Reference Look - Up", 
			minimizable:true,
			showEffect:Effect.Appear, 
			destroyOnClose: true,
			maximizable: false,
			hideEffect: Effect.SwitchOff, 
			draggable:true })
			RefWin.setAjaxContent('reference_lookup.php?opnr=earn','','');
			//RefWin.show(true);
			RefWin.showCenter(true);
			
			//$('editEarn').innerHTML="<img src='../../../images/application_form_edit_2.png' class='toolbarImg'>" 
			//$('refLookup').disabled=true;
			
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == RefWin) {
		        RefWin = null;
		        Windows.removeObserver(this);
		        $('refNo').focus();
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}	
		
	function passRefNo(refNoVal){
		Windows.getWindow('refWin').close();
		pager('otAppAjaxResult.php','otAppCont','editRef',0,0,'','','&refNo='+refNoVal,'../../../images/');  
	}	
	
	
	
	function clearFld1(){
		
		$('cmbReasons').value=0;
		$('dateFiled').value=currentTime.getMonth() + 1;
		$('dateOt').value='';
		$('txtOtIn').value='';
		$('txtOtOut').value='';
	}	

pager('otAppAjaxResult.php','otAppCont','load',0,0,'','','','../../../images/');  


function maintOtApp(URL,ele,action,intOffSet,isSearch,txtSearch,cmbSearch,extra,id,empName){
		
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2}|-[\d]+|-[\d]+\.[\d]{1,2})$/;
		var empNo = '';
		var cntrlNo = '';
		var extraParam = '';
		var param = '';

		var arrEle = $('frmOtApp').serialize(true);
		
		if(action == 'addDtl') {
			
		
			if(arrEle['cmbReasons'] == 0){
				alert('Remarks is Required');
				$('txtOtReason').focus();
				return false;		
			}
//			if(arrEle['txtDateFiled'] == ''){
//				alert('Date Filed is Required');
//				$('txtDateFiled').focus();
//				return false;		
//			}
			
			
			if(arrEle['dateOt'] == ''){
				alert('Date of Overtime is Required');
				$('DateOt').focus();
				return false;
			}	
			if(arrEle['txtotIn'] == ''){
				alert('Overtime In is Required');
				$('txtOtIn').focus();
				return false;				
			}
				
			if(arrEle['txtOtOut'] == ''){
				alert('Overtime Out is Required');
				$('txtOtOut').focus();
				return false;
			}
				
			if(arrEle['txtOtOut'] > '24:00') {
				alert('Overtime Out is invalid');
				return false;
			}
			
			if(arrEle['txtOtIn'] > '24:00') {
				alert('Overtime In is invalid');
				return false;
			}
			
			if (arrEle['txtOtOut'].substr(0,2) > '24') {
				alert('OverTime Out is invalid');
				return false;
			}
			
			if(arrEle['txtOtOut'].substr(3,2) > '59') {
				alert('OverTime Out is invalid');
				
				return false;
			}	
			
			if (arrEle['txtOtIn'].substr(0,2) > '24') {
				alert('OverTime Out is invalid');
				return false;
			}
			
			if(arrEle['txtOtIn'].substr(3,2) > '59') {
				alert('OverTime Out is invalid');
				
				return false;
			}

			
			if(arrEle['txtOtOut'] <= arrEle['txtOtIn']) {

			var crossTag = arrEle['chkCrossDate'];
			
				var blnAnswer = confirm("The OT Out encoded is less than the OT In, is it a cross date");
					
				if (blnAnswer){
					$('chkCrossDate').checked=true;
				}else{
					$('chkCrossDate').checked=false;
				}
			}

			//var refNo = arrEle['refNo'];
			var otReason = arrEle['cmbReasons'];
			var empNo = arrEle['txtAddEmpNo'];
			var dateFiled = arrEle['dateFiled'];
			var dateOt = arrEle['dateOt'];
			var OTOut = arrEle['txtOtOut'];
			var OTIn =arrEle['txtOtIn'];
			//var otStat = arrEle['cmbOtStat'];
			
			
			if (($('chkCrossDate').checked)==true) {
				var checked = 'Y'
				var param ='&otReason='+otReason+'&empNo='+empNo+'&dateFiled='+dateFiled+'&dateOt='+dateOt+'&OTOut='+OTOut+'&OTIn='+OTIn+'&otStat=H&checked='+checked;
			}else{
				var param ='&otReason='+otReason+'&empNo='+empNo+'&dateFiled='+dateFiled+'&dateOt='+dateOt+'&OTOut='+OTOut+'&OTIn='+OTIn+'&otStat=H';
			}
			
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
		
		if(action == 'editOtAppDtl'){//deleDedDtl
			var eRefNo = $('txtRefNoV'+id).innerHTML;
			var eEmpNo = $('txtEmpNo'+id).innerHTML;
			var eEmpName = $('txtEmpName'+id).innerHTML;
			var eDateFiled = $('dateFiled'+id).innerHTML;
			var eOtReason = $('txtOtReason'+id).innerHTML;
			var eOtDate = $('txtOtDate'+id).innerHTML;
			var eOtIn = $('txtOtIn'+id).innerHTML;
			var eOtOut = $('txtOtOut'+id).innerHTML;
			//var eOtStat = $('otStats'+id).innerHTML;
			
			
			//extraParam = '&eRefNo='+eRefNo+'&eEmpNo='+eEmpNo;
			
			editOtAppDtl = confirm('Are you sure do you want to edit ?\nReference No.: ' +eEmpNo);
			
			if(editOtAppDtl == false){
				return false;
			}

			var param ='&eRefNo='+eRefNo+'&eEmpNo='+eEmpNo+'&eEmpName='+eEmpName+'&eOtReason='+eOtReason+'&eDateFiled='+eDateFiled+'&eOtDate='+eOtDate+'&eOtIn='+eOtIn+'&eOtOut='+eOtOut;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=editOtAppDtl'+param,{
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
		
		if(action == 'delOtAppDtl'){
		
		
			var dRefNo = $('txtRefNoV'+id).innerHTML;


			deleOtApp = confirm('Do You Want to Delete Ot Application Reference No.: '+dRefNo);
			
			if(deleOtApp == false){
				return false;
			}

			var param ='&dRefNo='+dRefNo;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=delOtAppDtl'+param,{
				method : 'get',
				
				onComplete : function (req){
					eval(req.responseText);
					pager('otAppAjaxResult.php','otAppCont','load',0,0,'','','','../../../images/'); 
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


	function delOtAppDtl(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected OT Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Delete&seqNo='+seqNo;
			
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmOtApp').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('otAppAjaxResult.php','otAppCont','load',0,0,'','','','../../../images/');  
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
	
	function updateOtTran(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to Approve the selected Overtime Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Approved&seqNo='+seqNo;
			
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmOtApp').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('otAppAjaxResult.php','otAppCont','load',0,0,'','','','../../../images/');  
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
	
	function checkAll(field)
	{
		var chkob = document.frmOtApp.elements['chkseq[]'];
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
	
	function getSeqNo()
	{
		var param = '?action=getSeqNo&seqNo=';
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			parameters : $('frmOtApp').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
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
			parameters : $('frmOtApp').serialize(),
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
				   pager('otAppAjaxResult.php','otAppCont','load',0,0,'','','','../../../images/');  
					Windows.removeObserver(this);
				  }
				}
			  }
			  Windows.addObserver(myObserver);
		}
		
	}

	

	
	
	function checkShift(){
	var arrEle = $('frmOtApp').serialize(true);
	var dateOt =(arrEle['dateOt']);
	var empNo = arrEle['txtAddEmpNo'];

	var param = '&empNo='+empNo+'&dateOt='+dateOt;
	
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=checkShift'+param,{
				method : 'get',
				parameters : $('frmOtApp').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					//pager('otAppAjaxResult.php','otAppCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator1').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator1').innerHTML="";
				}
			});	
	
	}
	
//	function getContent(){
//		var arrEle = $('frmOtApp').serialize(true);
//		var dateOt = new Date(arrEle['dateOt']);
//		var tOut = (arrEle['txtOtOut']);
//		var tIn = (arrEle['txtOtIn']);
//		var tInRes = tIn.replace(":",".");
//		var valInRes = parseFloat(tInRes);
//		 
//		var tOutRes = tOut.replace(":",".");
//		var valOutRes = parseFloat(tOutRes);
//		
//		if(dateOt.getDay()==6 || dateOt.getDay()==7){
//			if(valOutRes>12){
//				$('chkCrossDate').checked=true;	
//			}
//			else{
//				$('chkCrossDate').checked=false;		
//			}
//		}
//		else{
//			if(valOutRes>12){
//				$('chkCrossDate').checked=true;	
//			}
//			else{
//				$('chkCrossDate').checked=false;		
//			}
//		}
//		
//		if(valInRes>12){
//			if()
//			if(valOutRes>12){
//				$('chkCrossDate').checked=true;	
//			}
//			else{
//				$('chkCrossDate').checked=false;		
//			}
//		}
//		else{
//			if(valOutRes>12){
//				$('chkCrossDate').checked=true;	
//			}
//			else{
//				$('chkCrossDate').checked=false;		
//			}
//		}
//	}
</SCRIPT>