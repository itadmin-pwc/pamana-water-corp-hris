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

switch($_GET["action"])
{
	case 'addNewOBApp':
		$empno=$_GET['empno'];
		if($empno!=''){
			$_SESSION['employeenumber']=$empno;
		}
		else{
			unset($_SESSION['employeenumber']);
		}
	break;
	
	case "NEWREFNO":
		/*$arr_lastRefNo = $obObj->getLastRefNo("tblTK_OBApp");
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
		
		$empInfo = $obObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');
		
		/*if($_GET["txtRefNo"]=="")
			echo "alert('Reference No. is required.');";
		else
		{*/
		
			echo "$('cmbReasons').value=0;";
			echo "$('txtobTimeIn').value='';";
			echo "$('txtobTimeOut').value='';";
			echo "$('cmbTINAMPM').value=0;";
			echo "$('cmbTOUTAMPM').value=0;";
			
			$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
			echo "$('txtEmpName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";
			
			$deptName = $obObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
			$posName = $obObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
			
			echo "$('txtDeptPost').value='".htmlspecialchars(addslashes($deptName["deptDesc"]))." - ".$posName["posDesc"]."';";
			
			/*$shiftCode = $obObj->getTblData("tblTk_EmpShift", " and empNo='".$_GET["empNo"]."'", "", "sqlAssoc");
			$array_day = array('Mon'=>1 , 'Tue' => 2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6, 'Sun'=>7);
			
			$shiftCodeDtl = $obObj->getTblData("tblTk_ShiftDtl", " and shftCode='".$shiftCode["shiftCode"]."' and dayCode='".$array_day[date("D", strtotime($_GET["obDate"]))]."'", "", "sqlAssoc");
			*/
				
			$shiftCodeDtl = $obObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("Y-m-d", strtotime($_GET["obDate"]))."'", "", "sqlAssoc");
	
			if($shiftCodeDtl["shftTimeIn"]=="")
				echo "$('shiftSched').value='Set the Schedule first.'; document.frmOB.cmbReasons.disabled=true; document.frmOB.rdnDeduct8.disabled=true; document.frmOB.obdestination.disabled=true; $('empPayGrp').value='".$empInfo['empPayGrp']."'; $('empPayCat').value='".$empInfo['empPayCat']."'; document.frmOB.txtobTimeIn.disabled=true; document.frmOB.txtobTimeOut.disabled=true; document.frmOB.cmbTINAMPM.disabled=true; document.frmOB.cmbTOUTAMPM.disabled=true; document.frmOB.btnSave.disabled=true;";
			else
			{
				echo "$('shiftSched').value='".$shiftCodeDtl["shftTimeIn"]." - ".$shiftCodeDtl["shftTimeOut"]."'; $('schedTimeIn').value='".$shiftCodeDtl["shftTimeIn"]."'; $('empPayGrp').value='".$empInfo['empPayGrp']."'; $('empPayCat').value='".$empInfo['empPayCat']."'; $('schedTimeOut').value='".$shiftCodeDtl["shftTimeOut"]."'; document.frmOB.cmbReasons.disabled=false; document.frmOB.rdnDeduct8.disabled=false; document.frmOB.obdestination.disabled=false; document.frmOB.txtobTimeIn.disabled=false; document.frmOB.txtobTimeOut.disabled=false; document.frmOB.cmbTINAMPM.disabled=false; document.frmOB.cmbTOUTAMPM.disabled=false; document.frmOB.btnSave.disabled=false;";
			}
		/*}*/
		exit();			
	break;
	
	case "getEmpShiftCode":
		/*$shiftCode = $obObj->getTblData("tblTk_EmpShift", " and empNo='".$_GET["empNo"]."'", "", "sqlAssoc");
		$array_day = array('Mon'=>1 , 'Tue' => 2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6, 'Sun'=>7);
		
		$shiftCodeDtl = $obObj->getTblData("tblTk_ShiftDtl", " and shftCode='".$shiftCode["shiftCode"]."' and dayCode='".$array_day[date("D", strtotime($_GET["obDate"]))]."'", "", "sqlAssoc");
		*/
		$shiftCodeDtl = $obObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("Y-m-d", strtotime($_GET["obDate"]))."'", "", "sqlAssoc");
		
		if($shiftCodeDtl["shftTimeIn"]=="")
			echo "$('shiftSched').value='Set the Schedule first.';document.frmOB.rdnDeduct8.disabled=true; document.frmOB.cmbReasons.disabled=true; document.frmOB.obdestination.disabled=true; document.frmOB.txtobTimeIn.disabled=true; document.frmOB.txtobTimeOut.disabled=true; document.frmOB.cmbTINAMPM.disabled=true; document.frmOB.cmbTOUTAMPM.disabled=true; document.frmOB.btnSave.disabled=true;";
		else
			echo "$('shiftSched').value='".$shiftCodeDtl["shftTimeIn"]." - ".$shiftCodeDtl["shftTimeOut"]."'; document.frmOB.rdnDeduct8.disabled=false; $('schedTimeIn').value='".$shiftCodeDtl["shftTimeIn"]."';  $('schedTimeOut').value='".$shiftCodeDtl["shftTimeOut"]."'; document.frmOB.cmbReasons.disabled=false; document.frmOB.obdestination.disabled=false; document.frmOB.txtobTimeIn.disabled=false; document.frmOB.txtobTimeOut.disabled=false; document.frmOB.cmbTINAMPM.disabled=false; document.frmOB.cmbTOUTAMPM.disabled=false; document.frmOB.btnSave.disabled=false;";

		exit();
	break;
	
	
	case "saveObSched":
		$dateTIn = $_GET["dateFiled"]." ".$_GET["txtobTimeIn"];
		$dateTOut = $_GET["dateFiled"]." ".$_GET["txtobTimeOut"];
		$shiftOut = $_GET["dateFiled"]." ".$_GET["schedTimeOut"];
		$chkPayPeriod =  $obObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."' and '".$_GET["obDate"]."' between pdFrmDate and pdToDate and pdTSStat='O' ", "", "sqlAssoc");
		
		if($chkPayPeriod["pdSeries"]!="")
		{
			$empno = $_GET["txtAddEmpNo"];
//			if(str_replace(":",".",$_GET["txtobTimeOut"])>=24){
//				if((str_replace(":",".",$_GET["txtobTimeOut"]))<=(str_replace(":",".",$_GET["txtobTimeIn"]))){
//					echo "alert('OB Time Out should not be less than or equal to OB Time In.');";
//				}
//				else
//				{
	
					if($_GET["Edited"]=="")
					{		
						//Check no. of Records
						$arr_ObRec = $obObj->getTblData("tblTK_OBApp", " and empNo='".$_GET["txtAddEmpNo"]."' and obDate='".date("Y-m-d", strtotime($_GET["obDate"]))."'", "", "sqlAssoc");
					
						if($arr_ObRec["empNo"]!="")
						{
							$checking_overlapping = $obObj->checkOBEntryValidation($_GET);
							if($checking_overlapping!=""){
								echo "alert('Overlapping on the Existing OB Record where OB Time In / Out : ".$checking_overlapping.".');";
							}
							else
							{
								//$insRecObTran = $obObj->tran_Ob($_GET,"Add");
								//echo "alert('".$insRecObTran."')";
								$insRecObTran = $obObj->tran_Ob($_GET,"Add");
								if($insRecObTran){
									echo "
										var ans = confirm('OB Application has been saved! Would you like to add new OB Application?');
										if(ans==true){
												
												location.href='ob.php?action=addNewOBApp&empno=$empno';
									}
										else{
												
												location.href='ob.php';
									}";
								}
								else{
									echo "alert('OB Application failed!');";	
								}
							}
						}
						else
						{
//							$insRecObTran = $obObj->tran_Ob($_GET,"Add");
//							echo "alert('".$insRecObTran."')";
								$insRecObTran = $obObj->tran_Ob($_GET,"Add");
								if($insRecObTran){
									echo "
										var ans = confirm('OB Application has been saved! Would you like to add new OB Application?');
										if(ans==true){
												
												location.href='ob.php?action=addNewOBApp&empno=$empno';
									}
										else{
												
												location.href='ob.php';
									}";
								}
								else{
									echo "alert('OB Application failed!');";	
								}
						}
					}
					else{
						  $insRecObTran = $obObj->tran_Ob($_GET,"Update");
//						  echo "alert('".$insRecObTran."')";	
						  if($insRecObTran){
							  echo "alert('Selected OB application has been updated!');";
							  echo "location.href='ob.php';";
						  }
						  else{
							  echo "alert('OB Application failed!');";	
						  }

					}
		}
		else
		{
			echo "alert('Selected OB Date is not part of the Current Cut Off.');";
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
				$resDel = $obObj->execQry($qryDel);
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
				$tkData = $obObj->getTblData("tblTk_ObApp", " and seqNo='".$chkSeqNo_val."'", "", "sqlAssoc");
				if($tkData['empNo'] == $_SESSION['employee_number']) {
					if($selfApprove) {
						if($_SESSION['uType'] == "T") {
							$qryApp = "Update  tblTk_ObApp set dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."',obStat='A' where seqNo='".$chkSeqNo_val."';";
							$resApp = $obObj->execQry($qryApp);
						}elseif($_SESSION['uType'] == "TA") {
							$qryApp = "Update  tblTk_ObApp set dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."',obStat='A',mApproverdBy='" . $_SESSION["employee_number"] . "',mStat='A',mDateApproved='".date("Y-m-d")."' where seqNo='".$chkSeqNo_val."';";
							$resApp = $obObj->execQry($qryApp);
						}else{
							$qryApp = "Update  tblTk_ObApp set mApproverdBy='" . $_SESSION["employee_number"] . "',mStat='A',mDateApproved='".date("Y-m-d")."' where seqNo='".$chkSeqNo_val."';";
							$resApp = $obObj->execQry($qryApp);
						}
					}
				}else{
					if($_SESSION['uType'] == "T") {
						$qryApp = "Update  tblTk_ObApp set dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."',obStat='A' where seqNo='".$chkSeqNo_val."';";
						$resApp = $obObj->execQry($qryApp);
					}elseif($_SESSION['uType'] == "TA") {
						$qryApp = "Update  tblTk_ObApp set dateApproved='".date("Y-m-d")."',userApproved='".$_SESSION["employee_number"]."',obStat='A',mApproverdBy='" . $_SESSION["employee_number"] . "',mStat='A',mDateApproved='".date("Y-m-d")."' where seqNo='".$chkSeqNo_val."';";
						$resApp = $obObj->execQry($qryApp);
					}else{
						$qryApp = "Update  tblTk_ObApp set mApproverdBy='" . $_SESSION["employee_number"] . "',mStat='A',mDateApproved='".date("Y-m-d")."' where seqNo='".$chkSeqNo_val."';";
						$resApp = $obObj->execQry($qryApp);
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
				$resDis = $obObj->execQry($qryDis);
				if($resDis){
					$remarks = 'Cancelled approved application';	
					$userId = $_SESSION["employee_number"];
					$qryTransData = "Insert into tblTK_ObApphist (compCode, empNo, refNo, obDate, obDestination, dateFiled, obSchedIn, 
									obSchedOut, obActualTimeIn, obActualTimeOut, obReason, hrs8Deduct, dateApproved, userApproved, dateAdded, 
									addedBy, dateUpdated, updatedBy, obStat, remarks, userCancelled, otherDetails) 
									Select compCode, empNo, refNo, obDate, obDestination, dateFiled, obSchedIn, obSchedOut, obActualTimeIn, 
									obActualTimeOut, obReason, hrs8Deduct, dateApproved, userApproved, dateAdded, addedBy, dateUpdated, 
									updatedBy, obStat, '$remarks', '$userId', otherDetails from tblTK_ObApp where seqNo='{$id}'";
					$resTransData = $obObj->execQry($qryTransData);				
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
		<FORM name='frmOB' id="frmOB" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="obCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	
	pager('obAjaxResult.php','obCont','load',0,0,'','','','../../../images/');  

	function newRef(act){
		pager('obAjaxResult.php','obCont','refresh',0,0,'','','','../../../images/');  	
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
		
		var obDate = document.frmOB.obDate.value;
		//var refNo = document.frmOB.refNo.value;
		
		//var param = '?action=getEmpInfo&empNo='+eleVal+'&obDate='+obDate+'&txtRefNo='+refNo;
		var param = '?action=getEmpInfo&empNo='+eleVal+'&obDate='+obDate;
		
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
		var eleVal = document.frmOB.txtAddEmpNo.value;
		var obDate = document.frmOB.obDate.value;
		/*var refNo = document.frmOB.refNo.value;
		
		if(refNo=="")
		{
			alert("Generate Reference No. First.");
			return false;
		}
		else*/ 
		
		if(eleVal=="")
		{
			alert("Select Employee first.");
			return false;
		}
		else
		{
			var param = '?action=getEmpShiftCode&empNo='+eleVal+'&obDate='+obDate;
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
		var obFields = $('frmOB').serialize(true);
		var obTimeIN = new Date(obFields["obDate"]+' '+obFields["schedTimeIn"]+' '+obFields["cmbTINAMPM"]);
		var obTimeOUT = new Date(obFields["obDate"]+' '+obFields["schedTimeOut"]+' '+obFields["cmbTOUTAMPM"]);
		var obTIN = obTimeIN.getHours();
		var obTOUT = obTimeOUT.getHours();
		/*if(obFields["refNo"]=="")
		{
			alert("Generate Ref. No. first.");
			$('refNo').focus();
			return false;
		}*/
		
		if(obFields["txtAddEmpNo"]=="")
		{
			alert("Select Employee first.");
			$('txtAddEmpNo').focus();
			return false;
		}
		
		
		if(obFields["obDate"]=="")
		{
			alert("OB Date is required.");
			$('obDate').focus();
			return false;
		}
		
		if(obFields["shiftSched"]=="Set the Schedule first.")
		{
			alert("Shift Schedule is required.");
			$('shiftSched').focus();
			return false;
		}
		
		if(obFields["cmbTINAMPM"]==0)
		{
			alert("SCHED TIME IN AM/PM -  is required.");
			$('cmbTINAMPM').focus();
			return false;
		}

		if(obFields["cmbTOUTAMPM"]==0)
		{
			alert("SCHED TIME OUT AM/PM -  is required.");
			$('cmbTOUTAMPM').focus();
			return false;
		}
		
		if(obTOUT<=obTIN && $('crossDay').checked == false){
			alert("SCHED Time Out should not be less than or equal to SCHED Time In! Please check cross day..");
//			$('cmbTOUTAMPM').focus();
			return false;
		}
		
		if(obFields["cmbReasons"]==0)
		{
			alert("Purpose of the OB is required.");
			$('cmbReasons').focus();
			return false;
		}
		
		if(obFields["obdestination"]=="")
		{
			alert("Destination of the OB is required.");
			$('obdestination').focus();
			return false;
		}
		
		if(obFields["txtobTimeIn"]=="")
		{
			alert("OB Time - In is required.");
			$('txtobTimeIn').focus();
			return false;
		}
		
		if(obFields["txtobTimeOut"]=="")
		{
			alert("OB Time - Out is required.");
			$('txtobTimeOut').focus();
			return false;
		}

		//if(obFields["cmbTINAMPM"]==0)
		//{
		//	alert("OB TIME OUT AM/PM -  is required.");
		//	$('cmbTOUTAMPM').focus();
		//	return false;
		//}
		
		
		var params = "ob.php?action=saveObSched&Edited";	
		
		new Ajax.Request(params,
		{
			method : 'get',
			parameters : $('frmOB').serialize(),
			onComplete : function(req){
				eval(req.responseText);
				pager('obAjaxResult.php','obCont','load',0,0,'','','','../../../images/');  
			},
			onCreate : function (){
				$('indicator2').src="../../../images/wait.gif";
				document.frmOB.btnSave.disabled = true;
//				document.frmOB.btnApp.style.visibility = 'hidden';
//				document.frmOB.btnDel.style.visibility = 'hidden';
			},
			onSuccess : function (){
				$('indicator2').innerHTML="";
				document.frmOB.btnSave.disabled = false;
//				document.frmOB.btnApp.style.visibility = 'visible';
//				document.frmOB.btnDel.style.visibility = 'visible';
			}	
		});
		
	}
	
	function delObTran(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected OB Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Delete&seqNo='+seqNo;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmOB').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('obAjaxResult.php','obCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmOB.btnSave.disabled = true;
//					document.frmOB.btnApp.style.visibility = 'hidden';
//					document.frmOB.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmOB.btnSave.disabled = false;
//					document.frmOB.btnApp.style.visibility = 'visible';
//					document.frmOB.btnDel.style.visibility = 'visible';
				}
			});	
			
			
		}
	}
	
	function upObTran(act,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to Approved the selected OB Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Approved';
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmOB').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('obAjaxResult.php','obCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmOB.btnSave.disabled = true;
//					document.frmOB.btnApp.style.visibility = 'hidden';
//					document.frmOB.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmOB.btnSave.disabled = false;
//					document.frmOB.btnApp.style.visibility = 'visible';
//					document.frmOB.btnDel.style.visibility = 'visible';
				}
			});	
			
			
		}
	}
	
	function disObTran(id,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('This process will Cancel the selected Approved OB Application! Continue process?');
		
		if(deleShiftCode == true){
			var param = '?action=disapprove&id='+id;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmOB').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('obAjaxResult.php','obCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmOB.btnSave.disabled = true;
//					document.frmOB.btnApp.style.visibility = 'hidden';
//					document.frmOB.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmOB.btnSave.disabled = false;
//					document.frmOB.btnApp.style.visibility = 'visible';
//					document.frmOB.btnDel.style.visibility = 'visible';
				}
			});	
		}
	}
	
	function checkAll(field)
	{
		var chkob = document.frmOB.elements['chkseq[]'];
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
		var param = '?action=getSeqNo';
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			parameters : $('frmOB').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}

	function UpdateOBTran(inputTypeSeqNo)
	{
		if(inputTypeSeqNo==""){
			alert('Please select OB application to modify.');
		}
		else{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:950, 
		height:250, 
		zIndex: 100, 
		resizable: false, 
		parameters : $('frmOB').serialize(),
		minimizable : true,
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('transaction_popup.php?moduleName=ChangeOB&inputTypeSeqNo='+inputTypeSeqNo);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		       pager('obAjaxResult.php','obCont','load',0,0,'','','','../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
		}
	}
	
	
</SCRIPT>