<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("utApp.obj.php");

$utAppObj = new utAppObj($_GET, $_SESSION);
$utAppObj->validateSessions($_GET, $_SESSION);


if (isset($_GET['action'])) {
	switch ($_GET['action']) {
	
		case ('NEWREFNO'): //get ref no.
			$lastrefNo = $utAppObj->getLastRefNo();
				if($lastrefNo != false){

					$newrefNo = $lastrefNo['refNo']+1;
							
					if($utAppObj->updateLastRefNo($newrefNo) == true){
						echo "$('refNo').value=$newrefNo;";
						echo "$('cmbReasons').value=0;";
						echo "$('txtAddEmpNo').value='';";
						echo "$('txtAddEmpName').value='';";
						echo "$('dateUt').value='';";
						echo "$('txtUtOut').value='';";
//						echo "$('cmbUtStat').value='H';";
						echo "
							var blnAdd = confirm('Add another UT Application for this employee?');\n
					
							if (blnAdd != true){\n
								pager('utAppAjaxResult.php','utAppCont','load',0,0,'','','','../../../images/');  \n
							}\n						
						";			
					}else{
						echo "alert('Error Selecting Last Reference Number');";
					}
				}
			
			
			exit();
		break;
		
		case ('addDtl'): //save UT App

			$shiftCodeDtl = $utAppObj->getTblData("tblTk_UTApp", " and empNo='".$_GET['empNo']."' and utDate='".date("Y-m-d", strtotime($_GET["dateUt"]))."'", "", "sqlAssoc");			
			
			if($shiftCodeDtl["empNo"] != ''){
				echo "'".$shiftCodeDtl["empNo"]."';";
				echo "alert('Duplicate Entry of UT Application');";
			}else{
					$hrdiff = round($utAppObj->calDiff("{$_GET['dateUt']} {$_GET['UTOut']}","{$_GET['dateUt']} {$_GET['offTime']}",'m')/60,2);
					if ($hrdiff < 4) { 	
						if($utAppObj->addUtApp() == true) {
							
							echo "$('cmbReasons').value=0;";
							echo "$('dateUt').value='';";
							echo "$('txtUtOut').value='';";
							echo "$('txtSched').value='';";
//							echo "$('cmbUtStat').value='H';";
							echo 2;//successfuuly saved
						}
						else{
							echo "alert('Saving of UT Application failed.');";
						}
					} else {
						echo "alert('Invalid UT Application.');";
					}
					
			}

			exit();
		break;
	
		case ('getEmpInfo'):
				
				$empInfo = $utAppObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');

				$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
				echo "$('txtAddEmpName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";
				

			exit();
		break;
		
		case 'checkShift':
			$shiftCodeDtl = $utAppObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("Y-m-d", strtotime($_GET["dateUt"]))."'", "", "sqlAssoc");
			if($shiftCodeDtl["timeOut"] != ''){
				echo "$('txtSched').value='".$shiftCodeDtl["shftTimeOut"]."';";
			}else{
				echo "alert('Employee has no Shift Time Out on this date.');";
			}
			exit();
		break;
		
		case 'Delete':
		
			$chkSeqNo = $_GET["chkseq"];

			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
			$qryDel = "DELETE FROM tblTK_UTApp WHERE seqNo='".$chkSeqNo_val."'";
			$resDel = $utAppObj->execQry($qryDel);
			}

			echo "alert('Selected Leave Application already deleted.')";
			
		exit();
		break;
		
		case 'Approved':
		
			$chkSeqNo = $_GET["chkseq"];
			
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			
			{
				$qryApprove = "UPDATE tblTK_UTApp SET dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."', 
							   utStat='A' WHERE seqNo='".$chkSeqNo_val."';";
				$resApprove = $utAppObj->execQry($qryApprove);
			}

			echo "alert('Selected Undertime Application already approved.')";
			
		exit();
		break;
		
		case "getSeqNo":
			$chkSeqNo = $_GET["chkseq"];
		
			if(sizeof($chkSeqNo)>1)
			{
				echo "alert('Select 1 UT Application to be Modified.')";
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
	<FORM name='frmUtApp' id="frmUtApp" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="utAppCont"></div>
			<div id="indicator1" align="center"></div>
	</FORM>
</BODY>

	
</HTML>

<SCRIPT>
	disableRightClick()
	
	function validateMod(mode){
		if(mode == 'EDITRENO'){
			$('newUt').innerHTML="<img src='../../../images/application_add_2.png' class='toolbarImg'>";
			$('deleUt').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
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
		
		pager('utAppAjaxResult.php','utAppCont','refresh',0,0,'','','','../../../images/');  
			
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+act,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
				$('txtUtReason').focus();
				//$('editEarn').innerHTML="<img src='../../../images/application_form_edit_2.png' class='toolbarImg'>";
				//$('deleEarn').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
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
				parameters : $('frmUtApp').serialize(),
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
						pager('utAppAjaxResult.php','utAppCont',act,0,0,'','','&refNo='+refNo,'../../../images/');  
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
		pager('utAppAjaxResult.php','utAppCont','editRef',0,0,'','','&refNo='+refNoVal,'../../../images/');  
	}	
	
	
	
	function clearFld1(){
		$('refNo').value='';
		$('cmbReasons').value=0;
		$('dateFiled').value=currentTime.getMonth() + 1;
		$('dateUt').value='';
		$('txtUtOut').value='';
		//$('cmbUtStat').value='H';
	}	
		
	

		
pager('utAppAjaxResult.php','utAppCont','load',0,0,'','','','../../../images/');  


function maintUtApp(URL,ele,action,intOffSet,isSearch,txtSearch,cmbSearch,extra,id,empName){
		
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2}|-[\d]+|-[\d]+\.[\d]{1,2})$/;
		var empNo = '';
		var cntrlNo = '';
		var extraParam = '';
		var param = '';

		var arrEle = $('frmUtApp').serialize(true);
		
		if(action == 'addDtl') {
			
		
//			if(arrEle['txtDateFiled'] == ''){
//				alert('Date Filed is Required');
//				$('txtDateFiled').focus();
//				return false;		
//			}
			if(arrEle['dateUt'] == ''){
				alert('Date of Undertime is Required');
				$('DateOt').focus();
				return false;
			}	
			if(arrEle['txtUtOut'] == ''){
				alert('Time of Departure is Required');
				$('txtOtIn').focus();
				return false;				
			}
			
			if (arrEle['txtUtOut'].substr(0,2) > '24') {
				alert('Departure Time is invalid');
				return false;
			}
			
			if(arrEle['txtUtOut'].substr(3,2) > '59') {
				alert('Departure Time is invalid');
				return false;
			}			
			
			if(arrEle['txtUtOut'].substr(0,2) > arrEle['txtSched'].substr(0,2)) {
				alert('Time of Departure is greater than the Scheduled Time Out');
				
				return false;
			}	
			if(arrEle['cmbReasons'] == 0){
				alert('Reason is Required');
				$('cmbReasons').focus();
				return false;		
			}
			
			var utReason = arrEle['cmbReasons'];
			var empNo = arrEle['txtAddEmpNo'];
			var dateFiled = arrEle['dateFiled'];
			var dateUt = arrEle['dateUt'];
			var offTime = arrEle['txtSched'];
			var UTOut = arrEle['txtUtOut'];
			//var utStat = arrEle['cmbUtStat'];
			
			var param ='&utReason='+utReason+'&empNo='+empNo+'&dateFiled='+dateFiled+'&dateUt='+dateUt+'&offTime='+offTime+'&UTOut='+UTOut+'&utStat=H';
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=addDtl'+param,{
				method : 'get',
				
				onComplete : function (req){
					eval(req.responseText);
					
					var blnAdd = confirm("Add another UT Application for this employee?");
					
					if (blnAdd != true){
						pager('utAppAjaxResult.php','utAppCont','load',0,0,'','','','../../../images/');  
					}
				},
				onCreate : function (){
					$('indicator1').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator1').innerHTML="";
					
				}
			})			

		}
		
		if(action == 'deleUtApp'){//deleDedDtl

			empNo = $('txtEmpNo'+id).innerHTML;
			cntrlNo = $('txtCntrlNo'+id).innerHTML;
			
			extraParam = '&empNo='+empNo+'&cntrlNo='+cntrlNo;
			
			deleDedDtl = confirm('Are you sure do you want to delete ?\nEmployee : ' +empName);
			if(deleDedDtl == false){
				return false;
			}

		}//end deleDedDtl
		
		if(action == 'Search'){
			deleEarn = confirm('Do You Want to Delete Earnings Entry #'+arrEle['refNo']);
			if(deleEarn == false){
				return false;
			}	
		}
	
		if (action == 'editUtAppDtl'){
			
			eRefNo = $('txtRefNo'+id).innerHTML;
			//eDateOt = $('dateOt'+id).innerHTML;
			var param ='&refNo='+eRefNo;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=editUtAppDtl'+param,{
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
			
			extraParam = '&refNo='+eRefNo;
			
			inquiry = confirm('Are you sure you want to edit ?\nReference No:' +eRefNo);
			if (inquiry == false){
				return false;
			}
		}
	}

	function delUtAppDtl(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected Leave Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Delete&seqNo='+seqNo;
			
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmUtApp').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('utAppAjaxResult.php','utAppCont','load',0,0,'','','','../../../images/');  
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
	
	function updateUtTran(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to Approved the selected Leave Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Approved&seqNo='+seqNo;
			
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmUtApp').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('utAppAjaxResult.php','utAppCont','load',0,0,'','','','../../../images/');  
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
			parameters : $('frmUtApp').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}
	
	function UpdateRdTran(inputTypeSeqNo)
	{
		if(inputTypeSeqNo==''){
			alert('Please select Under time application to be modified.');	
		}
		else{
			var editAllw = new Window({
			id: "editAllw",
			className : 'mac_os_x',
			width:850, 
			height:410, 
			zIndex: 100, 
			resizable: false, 
			parameters : $('frmUtApp').serialize(),
			minimizable : true,
			showEffect:Effect.Appear, 
			destroyOnClose: true,
			maximizable: false,
			hideEffect: Effect.SwitchOff, 
			draggable:true })
			editAllw.setURL('undertime_popup.php?&inputTypeSeqNo='+inputTypeSeqNo);
			editAllw.show(true);
			editAllw.showCenter();	
			
			  myObserver = {
				onDestroy: function(eventName, win) {
	
				  if (win == editAllw) {
					editAllw = null;
				   pager('utAppAjaxResult.php','utAppCont','load',0,0,'','','','../../../images/');  
					Windows.removeObserver(this);
				  }
				}
			  }
			  Windows.addObserver(myObserver);
		}
	}

	
	
	
	
	
	function checkShift(){
	var arrEle = $('frmUtApp').serialize(true);
	var dateOt =(arrEle['dateUt']);
	var empNo = arrEle['txtAddEmpNo'];

	var param = '&empNo='+empNo+'&dateUt='+dateOt;
	
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=checkShift'+param,{
				method : 'get',
				parameters : $('frmUtApp').serialize(),
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

	//  End -->
	
</SCRIPT>