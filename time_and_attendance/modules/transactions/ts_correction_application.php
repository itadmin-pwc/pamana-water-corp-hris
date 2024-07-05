<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("transaction_obj.php");


$Obj = new transactionObj($_GET,$_SESSION);
$Obj->validateSessions('','MODULES');

$_SESSION['employeenumber']=$_SESSION['employee_number'];
$approverData = $Obj->getTblData("tbltna_approver", " and approverEmpNo='".$_SESSION['employee_number']."' and subordinateEmpNo='".$_SESSION['employee_number']."' and status='A' AND dateValid >= now()", "", "sqlAssoc");
$selfApprove = $approverData["approverEmpNo"] == $_SESSION['employee_number'];

switch($_GET["action"]) {
	case 'addNewApp':
		$empno=$_GET['empno'];
		if($empno!=''){
			$_SESSION['employeenumber']=$empno;
		}
		else{
			unset($_SESSION['employeenumber']);
		}
	break;
	
	case "NEWREFNO":
		/*$arr_lastRefNo = $Obj->getLastRefNo("tblTK_OBApp");
		$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;
		echo "$('refNo').value=$lastRefNo;";*/
		
		
		echo "$('cmbReasons').value=0;";
		
		echo "$('txtAddEmpNo').value='';";
		echo "$('shiftSched').value='';";
		echo "$('schedTimeIn').value='';";
		echo "$('schedTimeOut').value='';";
		
		echo "$('txtobTimeIn').value='';";
		echo "$('txtobTimeOut').value='';";
		echo "$('cmbTINAMPM').value=0;";
		echo "$('cmbTOUTAMPM').value=0;";
		
		
		
		echo "document.frmOB.cmbReasons.disabled=false; document.frmOB.rdnDeduct8.disabled=false; document.frmOB.obdestination.disabled=false; document.frmOB.txtobTimeIn.disabled=false; document.frmOB.txtobTimeOut.disabled=false; document.frmOB.cmbTINAMPM.disabled=false; document.frmOB.cmbTOUTAMPM.disabled=false; document.frmOB.btnSave.disabled=false;";

		exit();		
	break;
	
	case 'getEmpInfo':
		$empInfo = $Obj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');
		$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
		echo "$('txtEmpName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";
		$deptName = $Obj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
		$posName = $Obj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
		echo "$('txtDeptPost').value='".htmlspecialchars(addslashes($deptName["deptDesc"]))." - ".$posName["posDesc"]."';";
		
		echo "$('timeIn').value='';";
		echo "$('lunchOut').value='';";
		echo "$('lunchIn').value='';";
		echo "$('timeOut').value='';";
		echo "$('violationCd').value='0';";
		echo "$('sched_timeIn').value='';";
		echo "$('sched_lunchOut').value='';";
		echo "$('sched_lunchIn').value='';";
		echo "$('sched_timeOut').value='';";
		echo "$('actual_timeIn').value='';";
		echo "$('actual_lunchOut').value='';";
		echo "$('actual_lunchIn').value='';";
		echo "$('actual_timeOut').value='';";

		$shiftCodeDtl = $Obj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("Y-m-d", strtotime($_GET["tsaDate"]))."'", "", "sqlAssoc");
	
		if($shiftCodeDtl["shftTimeIn"]=="") {
			echo "document.frmTSA.violationCd.disabled=true;document.frmTSA.btnSave.disabled=true;";
		}
		else
		{
			echo "document.frmTSA.violationCd.disabled=false;document.frmTSA.btnSave.disabled=false;";
			echo "$('timeIn').value='{$shiftCodeDtl["timeIn"]}';";
			echo "$('lunchOut').value='{$shiftCodeDtl["lunchOut"]}';";
			echo "$('lunchIn').value='{$shiftCodeDtl["lunchIn"]}';";
			echo "$('timeOut').value='{$shiftCodeDtl["timeOut"]}';";
			echo "$('sched_timeIn').value='{$shiftCodeDtl["shftTimeIn"]}';";
			echo "$('sched_lunchOut').value='{$shiftCodeDtl["shftLunchOut"]}';";
			echo "$('sched_lunchIn').value='{$shiftCodeDtl["shftLunchIn"]}';";
			echo "$('sched_timeOut').value='{$shiftCodeDtl["shftTimeOut"]}';";
			echo "$('actual_timeIn').value='{$shiftCodeDtl["timeIn"]}';";
			echo "$('actual_lunchOut').value='{$shiftCodeDtl["lunchOut"]}';";
			echo "$('actual_lunchIn').value='{$shiftCodeDtl["lunchIn"]}';";
			echo "$('actual_timeOut').value='{$shiftCodeDtl["timeOut"]}';";
		}

		exit();			
	break;
	
	case "getEmpShiftCode":
		$shiftCodeDtl = $Obj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("Y-m-d", strtotime($_GET["tsaDate"]))."'", "", "sqlAssoc");
	
		if($shiftCodeDtl["shftTimeIn"]=="") {
			echo "document.frmTSA.violationCd.disabled=true;document.frmTSA.btnSave.disabled=true;";
			echo "$('timeIn').value='';";
			echo "$('lunchOut').value='';";
			echo "$('lunchIn').value='';";
			echo "$('timeOut').value='';";
			echo "$('violationCd').value='0';";
			echo "$('sched_timeIn').value='';";
			echo "$('sched_lunchOut').value='';";
			echo "$('sched_lunchIn').value='';";
			echo "$('sched_timeOut').value='';";
			echo "$('actual_timeIn').value='';";
			echo "$('actual_lunchOut').value='';";
			echo "$('actual_lunchIn').value='';";
			echo "$('actual_timeOut').value='';";
		}
		else
		{
			echo "document.frmTSA.violationCd.disabled=false;document.frmTSA.btnSave.disabled=false;";
			echo "$('timeIn').value='{$shiftCodeDtl["timeIn"]}';";
			echo "$('lunchOut').value='{$shiftCodeDtl["lunchOut"]}';";
			echo "$('lunchIn').value='{$shiftCodeDtl["lunchIn"]}';";
			echo "$('timeOut').value='{$shiftCodeDtl["timeOut"]}';";
			echo "$('sched_timeIn').value='{$shiftCodeDtl["shftTimeIn"]}';";
			echo "$('sched_lunchOut').value='{$shiftCodeDtl["shftLunchOut"]}';";
			echo "$('sched_lunchIn').value='{$shiftCodeDtl["shftLunchIn"]}';";
			echo "$('sched_timeOut').value='{$shiftCodeDtl["shftTimeOut"]}';";
			echo "$('actual_timeIn').value='{$shiftCodeDtl["timeIn"]}';";
			echo "$('actual_lunchOut').value='{$shiftCodeDtl["lunchOut"]}';";
			echo "$('actual_lunchIn').value='{$shiftCodeDtl["lunchIn"]}';";
			echo "$('actual_timeOut').value='{$shiftCodeDtl["timeOut"]}';";
		}

		exit();
	break;
	
	
	case "saveTSASched":
		//$shiftOut = $_GET["dateFiled"]." ".$_GET["schedTimeOut"];
		$chkPayPeriod =  $Obj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."' and '".$_GET["tsaDate"]."' between pdFrmDate and pdToDate and pdTSStat='O' ", "", "sqlAssoc");
		
		if($chkPayPeriod["pdSeries"]!="")
		{
			$empno = $_GET["txtAddEmpNo"];
			if($_GET["Edited"]=="")
			{
				//Check no. of Records
				$arr_Rec = $Obj->getTblData("tbltk_ts_corr_app", " and empNo='".$_GET["txtAddEmpNo"]."' and obDate='".date("Y-m-d", strtotime($_GET["obDate"]))."'", "", "sqlAssoc");
			
				if($arr_Rec["empNo"]!="")
				{
					$insRecObTran = $Obj->tran_tsa($_GET,"Add");
					if($insRecObTran){
						echo "
							var ans = confirm('TS Correction Application has been saved! Would you like to add new Application?');
							if(ans==true){
								location.href='ts_correction_application.php?action=addNewApp&empno=$empno';
							}
							else{
								location.href='ts_correction_application.php';	
						}";
					}
					else{
						echo "alert('OB Application failed!');";	
					}
				}
				else
				{
					echo "alert('Record Already Exists.');";
				}
			}
			else{
				$insRecObTran = $Obj->tran_tsa($_GET,"Update");
//						  echo "alert('".$insRecObTran."')";	
				if($insRecObTran){
					echo "alert('Selected OB application has been updated!');";
					echo "location.href='ts_correction_application.php';";
				}
				else{
					echo "alert('OB Application failed!');";	
				}
			}
		}
		else
		{
			echo "alert('Selected Date is not part of the Current Cut Off.');";
		}	
		
		exit();	
	break;
	
	case "Delete":
		$chkSeqNo = $_GET["chkseq"];
		if(sizeof($chkSeqNo)>=1)
		{
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryDel = "Delete from tblTk_ObApp where seqNo='".$chkSeqNo_val."'";
				$resDel = $Obj->execQry($qryDel);
			}
			
			echo "alert('Selected OB Application already deleted.')";
		}
		else
		{
			echo "alert('Select OB Application to be deleted.')";
		}
		exit();
	break;
	
	case "Approved":
		$chkSeqNo = $_GET["chkseq"];
		if(sizeof($chkSeqNo)>=1)
		{
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$tkData = $Obj->getTblData("tblTk_ObApp", " and seqNo='".$chkSeqNo_val."'", "", "sqlAssoc");
				if($tkData['empNo'] == $_SESSION['employee_number']) {
					if($selfApprove) {
						if($_SESSION['uType'] == "T") {
							$qryApp = "Update  tblTk_ObApp set dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."',obStat='A' where seqNo='".$chkSeqNo_val."';";
							$resApp = $Obj->execQry($qryApp);
						}elseif($_SESSION['uType'] == "TA") {
							$qryApp = "Update  tblTk_ObApp set dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."',obStat='A',mApproverdBy='" . $_SESSION["employee_number"] . "',mStat='A',mDateApproved='".date("Y-m-d")."' where seqNo='".$chkSeqNo_val."';";
							$resApp = $Obj->execQry($qryApp);
						}else{
							$qryApp = "Update  tblTk_ObApp set mApproverdBy='" . $_SESSION["employee_number"] . "',mStat='A',mDateApproved='".date("Y-m-d")."' where seqNo='".$chkSeqNo_val."';";
							$resApp = $Obj->execQry($qryApp);
						}
					}
				}else{
					if($_SESSION['uType'] == "T") {
						$qryApp = "Update  tblTk_ObApp set dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."',obStat='A' where seqNo='".$chkSeqNo_val."';";
						$resApp = $Obj->execQry($qryApp);
					}elseif($_SESSION['uType'] == "TA") {
						$qryApp = "Update  tblTk_ObApp set dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."',obStat='A',mApproverdBy='" . $_SESSION["employee_number"] . "',mStat='A',mDateApproved='".date("Y-m-d")."' where seqNo='".$chkSeqNo_val."';";
						$resApp = $Obj->execQry($qryApp);
					}else{
						$qryApp = "Update  tblTk_ObApp set mApproverdBy='" . $_SESSION["employee_number"] . "',mStat='A',mDateApproved='".date("Y-m-d")."' where seqNo='".$chkSeqNo_val."';";
						$resApp = $Obj->execQry($qryApp);
					}
				}
			}
			
			echo "alert('Selected OB Application already Approved.')";
		}
		else
		{
			echo "alert('Select OB Application to be Approved.')";
		}
		exit();
	break;
	
	case "disapprove":
			$id = $_GET["id"];
				$qryDis = "Update  tblTk_ObApp set obStat='H', dateApproved = Null, userApproved = Null where seqNo='".$id."';";
				$resDis = $Obj->execQry($qryDis);
				if($resDis){
					$remarks = 'Cancelled approved application';	
					$userId = $_SESSION["employee_number"];
					$qryTransData = "Insert into tblTK_ObApphist (compCode, empNo, refNo, obDate, obDestination, dateFiled, obSchedIn, 
									obSchedOut, obActualTimeIn, obActualTimeOut, obReason, hrs8Deduct, dateApproved, userApproved, dateAdded, 
									addedBy, dateUpdated, updatedBy, obStat, remarks, userCancelled, otherDetails) 
									Select compCode, empNo, refNo, obDate, obDestination, dateFiled, obSchedIn, obSchedOut, obActualTimeIn, 
									obActualTimeOut, obReason, hrs8Deduct, dateApproved, userApproved, dateAdded, addedBy, dateUpdated, 
									updatedBy, obStat, '$remarks', '$userId', otherDetails from tblTK_ObApp where seqNo='{$id}'";
					$resTransData = $Obj->execQry($qryTransData);				
					echo "alert('Selected OB Application has been disapproved.')";	
				}
				else{
					echo "alert('Error occured! Cannot continue process.')";
				}
		exit();
	break;
	
	case "getSeqNo":
		$chkSeqNo = $_GET["chkseq"];
		
		if(sizeof($chkSeqNo)==0)
		{
			echo "alert('Select 1 OB Application to be Modified.')";
		}
		else
		{
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$inputTypeSeqNo = $chkSeqNo_val;
			}
			echo "UpdateOBTran('".$inputTypeSeqNo."');";
		}
		exit();
	break;
	
	default:
		
	break;
}

//if($_GET['action']=="saveObSched"){
//	echo "nhomer";	
//	exit();
//}
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

	function saveTSADetail()
	{
		var tsaFields = $('frmTSA').serialize(true);
		
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