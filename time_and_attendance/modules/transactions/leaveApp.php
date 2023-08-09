<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("leaveApp.obj.php");

$leaveAppObj = new leaveAppObj();
$leaveAppObj->ValidateSessions($_GET, $_SESSION);

unset($_SESSION['employeenumber']);
$_SESSION['employeenumber']=$_SESSION['employee_number'];
if (isset($_GET['action'])) {
	switch ($_GET['action']) {
	
		case 'addNewLeaveApp':
			$empno=$_GET['empno'];
			if($empno!=''){
				$_SESSION['employeenumber']=$empno;
			}
			else{
				unset($_SESSION['employeenumber']);
			}
		break;
	
		case ('NEWREFNO'): //get ref no.
			$lastrefNo = $leaveAppObj->getLastRefNo();
				if($lastrefNo != false){

					$newrefNo = $lastrefNo['refNo']+1;
							
					if($leaveAppObj->updateLastRefNo($newrefNo) == true){
						echo "$('refNo').value=$newrefNo;";
						echo "$('cmbReasons').value='';";
						echo "$('cmbLeaveApp').value='';";
						//echo "$('txtReliever').value='';";
						//echo "$('cmbAuthorized').value='Y';";
						echo "$('dateLvFrom').value='';";
						echo "$('cmbFromAMPM').value='';";
						echo "$('dateLvTo').value='';";
						echo "$('cmbToAMPM').value='';";
						//echo "$('txtNoOfDays').value='';";
						//echo "$('dateLvRetrn').value='';";
						//echo "$('cmbReturnAMPM').value='';";
						//echo "$('cmbLeaveStat').value='H';";
			
					}else{
						echo "alert('Error Selecting Last Reference Number');";
					}
				}
			
			
			exit();
		break;
		
		case ('addDtl'): //save OT App		
		
			$empno = $_GET['empNo'];
			$shiftCodeDtl = $leaveAppObj->getTblData("tblTk_LeaveApp", " and empNo='".$_GET['empNo']."' and lvDateFrom='".date("Y-m-d", strtotime($_GET["lvDateFrom"]))."' and lvFromAMPM = '".$_GET["lvFromAMPM"]."' and lvDateTo='".date("Y-m-d", strtotime($_GET["lvDateTo"]))."' and lvToAMPM = '".$_GET["lvToAMPM"]."'", "", "sqlAssoc");			
			
			if($shiftCodeDtl['empNo'] != ''){
				echo "'".$shiftCodeDtl["empNo"]."';";
				echo "alert('Duplicate Entry of Leave Application.');";
			}else{

					if($leaveAppObj->addLeaveApp() == true) {
						echo "
							var ans = confirm('Leave Application has been saved! Would you like to add new Leave Application?');
							if(ans==true){
									
									location.href='leaveApp.php?action=addNewLeaveApp&empno=$empno';
						}
							else{
									
									location.href='leaveApp.php';
						}";
//						echo "$('cmbReasons').value='';";
//		
//						echo "$('cmbLeaveApp').value='';";
//						//echo "$('txtReliever').value='';";
//						//echo "$('cmbAuthorized').value='Y';";
//						echo "$('dateLvFrom').value='';";
//						echo "$('cmbFromAMPM').value=0;";
//						echo "$('dateLvTo').value='';";
//						echo "$('cmbToAMPM').value=0;";
//						echo "$('cmbLeaveApp').value='';";
//						
//						//echo "$('dateLvRtrn').value='';";
//						//echo "$('cmbReturnAMPM').value=0;";
//						//echo "$('cmbLeaveStat').value='H';";
//						echo 2;
					}else{
						echo "alert('Saving of Leave Application failed.');";
					}
				
			}

			exit();
		break;
	
		case ('getEmpInfo'):

			$empInfo = $leaveAppObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');

				$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
				echo "$('txtAddEmpName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";

			exit();
		break;
		
		case 'Delete':
		
			$chkSeqNo = $_GET["chkseq"];

			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
			$qryDel = "DELETE FROM tblTK_LeaveApp where seqNo='".$chkSeqNo_val."'";
			$resDel = $leaveAppObj->execQry($qryDel);
			}

			echo "alert('Selected Leave Application already deleted.')";
			
		exit();
		break;
	
	
	case 'Approved':
		
			$chkSeqNo = $_GET["chkseq"];

			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryApprove = "UPDATE tblTK_LeaveApp SET dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."', 
							   lvStat='A' WHERE seqNo='".$chkSeqNo_val."';";
				$resApprove = $leaveAppObj->execQry($qryApprove);
			}

			echo "alert('Selected Leave Application already approved.')";
			
		exit();
		break;
		
		case "getSeqNo":
		$chkSeqNo = $_GET["chkseq"];
		
		if(sizeof($chkSeqNo)>1)
		{
			echo "alert('Select 1 Leave Application to be Modified.')";
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
		
		<!--calendar lib-->
		<!--<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar.js"></script>	
		<STYLE>@import url('../../../includes/calendar.css');</STYLE>
		
	</HEAD>
	
<BODY>
	<FORM name='frmLeaveApp' id="frmLeaveApp" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="leaveAppCont"></div>
			<div id="indicator1" align="center"></div>
	</FORM>
</BODY>

	
</HTML>

<SCRIPT>
	disableRightClick()
	
	function validateMod(mode){
		if(mode == 'EDITRENO'){
			$('newLeave').innerHTML="<img src='../../../images/application_add_2.png' class='toolbarImg'>";
			$('deleEarn').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
			$('btnUpdtHdr').disabled=true;
			
			$('refLookup').disabled=false;
			$('btnSaveAddDtl').disabled=true;
			$('refNo').focus();
		}
		
		if(mode == 'REFRESH'){
			Windows.getWindow('refWin').close();
		}
	}
	
	function newRef(act){
		
		pager('leaveAppAjaxResult.php','leaveAppCont','refresh',0,0,'','','','../../../images/');  
			
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
		var param ='&empNo='+eleVal;
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=getEmpInfo'+param,{
				method : 'get',
				parameters : $('frmLeaveApp').serialize(),
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

	
	
	function editRefNo(act,refVal,evt){
		
		var k = evt.keyCode | evt.which;
		
		param = '?action='+act+"&refNo="+refVal;
		
		if(k == 13){
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					refNo = parseInt(req.responseText);
					if(refNo != 0){
						pager('otAppAjaxResult.php','otAppCont',act,0,0,'','','&refNo='+refNo,'../../../images/');  
					}
					else{
						alert('NO RECORD FOUND');
						return false;
					}
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
		pager('leaveAppAjaxResult.php','leaveAppCont','editRef',0,0,'','','&refNo='+refNoVal,'../../../images/');  
	}	

	function clearFld1(){
	
		$('txtOtReason').value='';
		$('dateFiled').value=currentTime.getMonth() + 1;
		$('dateOt').value='';
		$('txtOtIn').value='';
		$('txtOtOut').value='';
		$('cmbOtStat').value='H';
	}	
		

pager('leaveAppAjaxResult.php','leaveAppCont','load',0,0,'','','','../../../images/');  


function maintLeaveApp(URL,ele,action,intOffSet,isSearch,txtSearch,cmbSearch,extra,id,empName){
		
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2}|-[\d]+|-[\d]+\.[\d]{1,2})$/;
		var empNo = '';
		var cntrlNo = '';
		var extraParam = '';
		var param = '';

		var arrEle = $('frmLeaveApp').serialize(true);
		
		if(action == 'addDtl') {
			
		
			if(arrEle['cmbReasons'] == 0){
				alert('Reason is Required');
				$('cmbReasons').focus();
				return false;		
			}
			
			if(arrEle['txtDateFiled'] == ''){
				alert('Date Filed is Required');
				$('txtDateFiled').focus();
				return false;		
			}
			
			if(arrEle['dateLvFrom'] == ''){
				alert('Date Leave From is required');
				$('dateLvFrom').focus();
				return false;
			}	
			if(arrEle['dateLvTo'] == ''){
				alert('Date Leave To is required');
				$('dateLvTo').focus();
				return false;				
			}
				
			if(arrEle['cmbLeaveApp'] == ' '){
				alert('Leave Application Type is required');
				$('cmbLeaveApp').focus();
				return false;
			}
				
//			if(arrEle['dateLvRtrn'] == '') {
//				alert('Date of Return is required');
//				$('dateLvRtrn').focus();
//				return false;
//			}
			
//			if(arrEle['dateLvRtrn'] < arrEle['dateLvTo'] ) {
//				alert('Date of Return is required');
//				$('dateLvRtrn').focus();
//				return false;
//			}
			
			if(arrEle['dateLvFrom'] > arrEle['dateLvTo'] ) {
				alert('Date Leave From should not be greater than Date Leave To');
				$('dateLvFrom').focus();
				return false;
			}
			
			if(arrEle['dateLvTo'] < arrEle['dateLvFrom'] ) {
				alert('Date Leave To should not be lesser than Date Leave From');
				$('dateLvTo').focus();
				return false;
			}
			
			// ----> start
			var dateLvFrom =new Date(arrEle['dateLvFrom']);
			var dateLvTo = new Date(arrEle['dateLvTo']);
//			var dateLvRetrn = new Date(arrEle['dateLvRtrn']);
			var leaveToAMPM = $('cmbToAMPM').value;
			var leaveFromAMPM = $('cmbFromAMPM').value;
//			var leaveReturnAMPM = $(cmbReturnAMPM).value;
	
//			var dateDiff = (dateLvRetrn.getDate()-dateLvFrom.getDate());
//			var dateDiffToRtrn =(dateLvRetrn.getDate()-dateLvTo.getDate());
			var cmbLvType = $('cmbLeaveApp').value;

			if (cmbLvType != '21') {
				if (dateLvFrom.getDate() == dateLvTo.getDate()){
					if (leaveFromAMPM == 'AM' && leaveToAMPM == 'PM'){
						alert ('Check leave date.');
						return false;
					}
					if (leaveFromAMPM == 'AM' && leaveToAMPM == 'WD'){
						alert('Check leave date.');
						return false;
					}
					if (leaveFromAMPM == 'PM' && leaveToAMPM == 'AM'){
						alert('Check leave date.');
						return false;	
					}
					if (leaveFromAMPM == 'PM' && leaveToAMPM == 'WD'){
						alert('Check leave date.');
						return false;	
					}
					if (leaveFromAMPM == 'WD' && leaveToAMPM == 'AM'){
						alert('Check leave date.');
						return false;	
					}
					if (leaveFromAMPM == 'WD' && leaveToAMPM == 'PM'){
						alert('Check leave date.');
						return false;	
					}
				}
				if(arrEle['dateLvFrom'] > arrEle['dateLvTo']){
				//if (dateLvFrom.getDate() > dateLvTo.getDate()){
						alert ('Check leave date!');
						return false;
				}
				if (dateLvFrom.getDate() < dateLvTo.getDate()){
					if (leaveFromAMPM == 'AM' && leaveToAMPM == 'WD'){
						alert('Check leave date.');
						return false;	
					}
					if (leaveFromAMPM == 'PM' && leaveToAMPM == 'WD'){
						alert('Check leave date.');
						return false;	
					}
					if (leaveFromAMPM == 'WD' && leaveToAMPM == 'AM'){
						alert('Check leave date.');
						return false;	
					}
					if (leaveFromAMPM == 'WD' && leaveToAMPM == 'PM'){
						alert('Check leave date.');
						return false;	
					}
				}
			}
			//----> end
			
			// check entry if leave type is COMBO {1/2 day leave WP and 1/2 day WOP}

//			if (cmbLvType == '21') {
//				
//				if(arrEle['dateLvTo'] != arrEle['dateLvFrom'] ) {
//					alert('Unable to Save application. Check selected Leave Type and Leave Dates.');
//					$('dateLvFrom').focus();
//					return false;
//				}
//				
//				if (leaveFromAMPM == 'WD' || leaveToAMPM == 'WD') {
//					alert('Check Date Entries');
//					return false;
//				} 
//					
//				if (leaveFromAMPM == 'AM' && leaveToAMPM == 'AM') {
//					alert('Check Date Entries');
//					return false;
//				} 
//				
//				if (leaveFromAMPM == 'PM' && leaveToAMPM == 'PM') {
//					alert('Check Date Entries');
//					return false;
//				} 
//			
//			}
			//end
			
			var refNo = arrEle['refNo'];
			var lvReason = arrEle['cmbReasons'];
			var empNo = arrEle['txtAddEmpNo'];
			var dateFiled = arrEle['dateFiled'];
			var lvDateFrom = arrEle['dateLvFrom'];
			var lvFromAMPM = arrEle['cmbFromAMPM'];
			var lvDateTo =arrEle['dateLvTo'];
			var lvToAMPM = arrEle['cmbToAMPM'];
			var tsAppTypeCd = arrEle['cmbLeaveApp'];
			var chkDeduct = arrEle['chkDeduct'];
//			var lvDateReturn = arrEle['dateLvRtrn'];
//			var lvReturnAMPM = arrEle['cmbReturnAMPM'];
			
//			var lvReliever = arrEle['txtReliever'];
//			var lvStat = arrEle['cmbLeaveStat'];
			
			var param ='&lvReason='+lvReason+'&empNo='+empNo+'&dateFiled='+dateFiled+'&lvDateFrom='+lvDateFrom+
					   '&lvFromAMPM='+lvFromAMPM+'&lvDateTo='+lvDateTo+'&lvToAMPM='+lvToAMPM+'&tsAppTypeCd='+tsAppTypeCd+
					   '&lvStat=H&chkDeduct='+chkDeduct;

			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=addDtl'+param,{
				method : 'get',
				
				onComplete : function (req){
					eval(req.responseText);
					var blnAdd = confirm("Add another Leave Application for this employee?");
						if (blnAdd != true){
							pager('leaveAppAjaxResult.php','leaveAppCont','load',0,0,'','','','../../../images/'); 
						}
					
						$('cmbLeaveApp').value = 0;
						$('cmbFromAMPM').value = 'WD';
						$('cmbToAMPM').value = 'WD';
						$('cmbReturnAMPM').value = 'WD';
						
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
			var eOtStat = $('otStats'+id).innerHTML;
			
			
			//extraParam = '&eRefNo='+eRefNo+'&eEmpNo='+eEmpNo;
			
			editOtAppDtl = confirm('Are you sure do you want to edit ?\nReference No.: ' +eEmpNo);
			
			if(editOtAppDtl == false){
				return false;
			}

			var param ='&eRefNo='+eRefNo+'&eEmpNo='+eEmpNo+'&eEmpName='+eEmpName+'&eOtReason='+eOtReason+'&eDateFiled='+eDateFiled+'&eOtDate='+eOtDate+'&eOtIn='+eOtIn+'&eOtOut='+eOtOut+'&eOtStat='+eOtStat;
			
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

				
		
		
		
		if (action == 'updateOtAppDtl'){
		
			var refNo = arrEle['refNo'];
			var otReason = arrEle['txtOtReason'];
			var empNo = arrEle['txtAddEmpNo'];
			var dateFiled = arrEle['dateFiled'];
			var dateOt = arrEle['dateOt'];
			var OTOut = arrEle['txtOtOut'];
			var OTIn =arrEle['txtOtIn'];
			var otStat = arrEle['cmbOtStat'];
			var crossTag = arrEle['chkCrossDate'];
			
			
			//extraParam = '&eRefNo='+eRefNo+'&eEmpNo='+eEmpNo;
			
			saveEditedOt = confirm('Do You Want to Update Ot Application Reference No.: '+refNo);
			
			if(saveEditedOt == false){
				return false;
			}
			
			var param ='&refNo='+refNo+'&otReason='+otReason+'&empNo='+empNo+'&dateFiled='+dateFiled+'&dateOt='+dateOt+'&OTOut='+OTOut+'&OTIn='+OTIn+'&otStat='+otStat+'&crossTag='+crossTag;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=updateOtAppDtl'+param,{
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

		}
		
	}
	
	function delLeaveAppDtl(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected Leave Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Delete&seqNo='+seqNo;
			
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmLeaveApp').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('leaveAppAjaxResult.php','leaveAppCont','load',0,0,'','','','../../../images/');  
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
	
	function updateLvTran(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch){
		var deleShiftCode = confirm('Are you sure you want to Approve the selected Leave Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Approved&seqNo='+seqNo;

			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmLeaveApp').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('leaveAppAjaxResult.php','leaveAppCont','load',0,0,'','','','../../../images/');  
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
		var chkob = document.frmLeaveApp.elements['chkseq[]'];
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
	
	
	
	function countDays(evt,eleVal){
		var arrEle = $('frmLeaveApp').serialize(true);
		var dateLvFrom =new Date(arrEle['dateLvFrom']);
		var dateLvTo = new Date(arrEle['dateLvTo']);
		var dateLvRetrn = new Date(arrEle['dateLvRtrn']);
		var leaveToAMPM = $(cmbToAMPM).value;
		var leaveFromAMPM = $(cmbFromAMPM).value;
		var leaveReturnAMPM = $(cmbReturnAMPM).value;

		var dateDiff = (dateLvRetrn.getDate()-dateLvFrom.getDate());
		var dateDiffToRtrn =(dateLvRetrn.getDate()-dateLvTo.getDate());
		
		
		
		if(dateDiffToRtrn > 1){
			alert('Check Date Return to work ');
			return false;
		}
		
		if(dateLvTo.getDate() < dateLvFrom.getDate()){
			alert('Check Leave Date To');
			return false;
		}
		
		if(dateLvRetrn.getDate() < dateLvTo.getDate() || dateLvRetrn.getDate() < dateLvFrom.getDate()){
			alert('Check Date Return to work ');
			return false;
		}
		
		//alert (dateDiff);
		//check if date from = date to
		if(dateLvFrom.getDate() == dateLvTo.getDate() && dateLvTo.getDate() == dateLvRetrn.getDate() ){

			if (leaveFromAMPM == 'AM' && leaveToAMPM == 'AM' && leaveReturnAMPM == 'PM'){
				$(txtNoOfDays).value = '.5';
			}
			
			if (leaveFromAMPM == 'AM' && leaveToAMPM == 'PM' && leaveReturnAMPM == 'PM'){
				$(cmbFromAMPM).value = 'WD';
				$(cmbToAMPM).value = 'AM';
				$(cmbReturnAMPM).value = 'WD';
				$(txtNoOfDays).value = '.5';
			}
			
			if (leaveFromAMPM == 'PM' && leaveToAMPM == 'PM' && leaveReturnAMPM == 'PM'){
				alert('Check Date of Return to Work');
				return false;
			}
			
		}else{
			var dateDiff = (dateLvRetrn.getDate()-dateLvFrom.getDate());
			if (leaveFromAMPM == 'WD' && leaveToAMPM == 'WD' && leaveReturnAMPM == 'WD'){
				$('txtNoOfDays').value = dateDiff;
			}
			if (leaveFromAMPM == 2 && leaveToAMPM == 0 && leaveReturnAMPM == 0){
				$('txtNoOfDays').value = dateDiff - .5;
			}
			if (leaveFromAMPM == 2 && leaveToAMPM == 1 && leaveReturnAMPM == 0){
				if (dateLvTo.getDate() == dateLvRetrn.getDate()){
					$(cmbReturnAMPM).value = 2;
				}
				$('txtNoOfDays').value = dateDiff - 1;
			}
		}
	}
	
	function getSeqNo()
	{
		var param = '?action=getSeqNo&seqNo=';
		
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			parameters : $('frmLeaveApp').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}
	
	function UpdateRdTran(inputTypeSeqNo)
	{
		if(inputTypeSeqNo==""){
			alert('Please select leave application to modify.');
		}
		else{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:950, 
		height:250, 
		zIndex: 100, 
		resizable: false, 
		parameters : $('frmLeaveApp').serialize(),
		minimizable : true,
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('leave_popup.php?&inputTypeSeqNo='+inputTypeSeqNo);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		       pager('leaveAppAjaxResult.php','leaveAppCont','load',0,0,'','','','../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
		}
	}

	

	
</SCRIPT>