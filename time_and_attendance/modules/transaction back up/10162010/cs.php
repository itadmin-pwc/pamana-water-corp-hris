<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("transaction_obj.php");


$csObj = new transactionObj($_GET,$_SESSION);
$csObj->validateSessions('','MODULES');


switch($_GET["action"])
{
	case "NEWREFNO":
		//$arr_lastRefNo = $csObj->getLastRefNo("tblTK_CSApp");
	//	$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;
		
		//echo "$('refNo').value=$lastRefNo;";
		echo "$('csreason').value='';";
		
		echo "$('txtAddEmpNo').value='';";
		echo "$('shiftSched').value='';";
		echo "$('schedTimeIn').value='';";
		echo "$('schedTimeOut').value='';";
		
		echo "$('txtobTimeIn').value='';";
		echo "$('txtobTimeOut').value='';";
		
		
		echo "document.frmCS.schedTimeIn.disabled=false; document.frmCS.chkCrossDay.disabled=false; document.frmCS.schedTimeOut.disabled=false; document.frmCS.shftTimeOut.disabled=false; document.frmCS.csTimeIn.disabled=false; document.frmCS.csTimeOut.disabled=false; document.frmCS.payPd.disabled=false;  document.frmCS.payPd.disabled=false;  document.frmCS.csreason.disabled=false; document.frmCS.chkStat.disabled=false; document.frmCS.btnSave.disabled=false;";

		exit();		
	break;
	
	case 'getEmpInfo':
		
		$empInfo = $csObj->getUserInfo($_SESSION['company_code'],$_GET['empNo'],'');
		
		/*if($_GET["txtRefNo"]=="")
			echo "alert('Reference No. is required.');";
		else
		{*/
			$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
			echo "$('txtEmpName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";
			
			$deptName = $csObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
			$posName = $csObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
			
			echo "$('txtDeptPost').value='".htmlspecialchars(addslashes($deptName["deptDesc"]))." - ".$posName["posDesc"]."';";
			
			$openPeriod_OpnPeriodD = $csObj->getOpenPeriod($_SESSION["company_code"],$empInfo["empPayGrp"],$empInfo['empPayCat']); 
			
			$openPeriod = $csObj->getOpenPeriod($_SESSION["company_code"],$empInfo["empPayGrp"],$empInfo['empPayCat']); 
			$payPayable = $openPeriod['pdPayable'];
			
			/*$shiftCode = $csObj->getTblData("tblTk_EmpShift", " and empNo='".$_GET["empNo"]."'", "", "sqlAssoc");
			$array_day = array('Mon'=>1 , 'Tue' => 2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6, 'Sun'=>7);
			
			$shiftCodeDtl = $csObj->getTblData("tblTk_ShiftDtl", " and shftCode='".$shiftCode["shiftCode"]."' and dayCode='".$array_day[date("D", strtotime($_GET["csDateFrom"]))]."'", "", "sqlAssoc");
			*/
			$shiftCodeDtl = $csObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("m/d/Y", strtotime($_GET["csDateFrom"]))."'", "", "sqlAssoc");
			
			if($shiftCodeDtl["shftTimeIn"]=="")
			{
				echo "$('schedTimeIn').value='Set the Schedule first.'; $('schedTimeOut').value=''; document.frmCS.chkCrossDay.disabled=true; document.frmCS.csTimeIn.disabled=true;  $('empPayGrp').value='".$empInfo['empPayGrp']."'; $('empPayCat').value='".$empInfo['empPayCat']."';  document.frmCS.csTimeOut.disabled=true; document.frmCS.csreason.disabled=true; document.frmCS.btnSave.disabled=true;";
			}	
			else
			{
				
				echo "$('schedTimeIn').value='".$shiftCodeDtl["shftTimeIn"]."'; $('schedTimeOut').value='".$shiftCodeDtl["shftTimeOut"]."'; $('shiftDayType').value='".$shiftCodeDtl["dayType"]."'; $('empPayGrp').value='".$empInfo['empPayGrp']."'; $('empPayCat').value='".$empInfo['empPayCat']."';  $('empbrnCode').value='".$empInfo['empBrnCode']."'; document.frmCS.csTimeIn.disabled=false; document.frmCS.csTimeOut.disabled=false; document.frmCS.chkCrossDay.disabled=false; document.frmCS.csreason.disabled=false;  document.frmCS.btnSave.disabled=false;  document.frmCS.chkStat.disabled=false; ";
			}
			
			
		//}
		exit();			
	break;
	
	case "getEmpShiftCode":
		/*$shiftCode = $csObj->getTblData("tblTk_EmpShift", " and empNo='".$_GET["empNo"]."'", "", "sqlAssoc");
		$array_day = array('Mon'=>1 , 'Tue' => 2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6, 'Sun'=>7);
		
		$shiftCodeDtl = $csObj->getTblData("tblTk_ShiftDtl", " and shftCode='".$shiftCode["shiftCode"]."' and dayCode='".$array_day[date("D", strtotime($_GET["csDateFrom"]))]."'", "", "sqlAssoc");
		*/
		$shiftCodeDtl = $csObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("m/d/Y", strtotime($_GET["csDateFrom"]))."'", "", "sqlAssoc");
			
		
		if($shiftCodeDtl["shftTimeIn"]=="")
		{
			echo "$('schedTimeIn').value='Set the Schedule first.'; $('schedTimeOut').value=''; document.frmCS.chkCrossDay.disabled=true; document.frmCS.csTimeIn.disabled=true;  document.frmCS.csTimeOut.disabled=true; document.frmCS.csreason.disabled=true; document.frmCS.btnSave.disabled=true;";
		}
		else
		{
			echo "$('schedTimeIn').value='".$shiftCodeDtl["shftTimeIn"]."'; $('schedTimeOut').value='".$shiftCodeDtl["shftTimeOut"]."'; $('shiftDayType').value='".$shiftCodeDtl["dayType"]."'; document.frmCS.csTimeIn.disabled=false; document.frmCS.csTimeOut.disabled=false; document.frmCS.chkCrossDay.disabled=false; document.frmCS.csreason.disabled=false;  document.frmCS.btnSave.disabled=false; document.frmCS.chkStat.disabled=false; ";
	
		}
		exit();
	break;
	
	
	case "saveCsSched":
		$ret_saveCsSched = $csObj->validateTran_Cd($_GET, "Add");
		echo "alert('$ret_saveCsSched')";
		exit();	
	break;
	
	case "Delete":
		$chkSeqNo = $_GET["chkseq"];
		if(sizeof($chkSeqNo)>=1)
		{
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryDel = "Delete from tblTk_CSApp where seqNo='".$chkSeqNo_val."'";
				$resDel = $csObj->execQry($qryDel);
			}
			echo "alert('Selected CS Application already deleted.')";
			
		}
		else
		{
			echo "alert('Select CS Application to be Deleted.')";
		}	
			
		exit();
	break;
	
	case "Approved":
		$chkSeqNo = $_GET["chkseq"];
		
		if(sizeof($chkSeqNo)>=1)
		{
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryApp = "Update  tblTk_CSApp set dateApproved='".date("m/d/Y")."',userApproved='".$_SESSION["employee_number"]."',csStat='A' where seqNo='".$chkSeqNo_val."';";
				$resApp = $csObj->execQry($qryApp);
			}
			
			echo "alert('Selected CS Application already Approved.')";
		}
		else
		{
			echo "alert('Select CS Application to be Approved.')";
		}
		exit();
	break;
	
	
	case "getSeqNo":
		$chkSeqNo = $_GET["chkseq"];
		
		if(sizeof($chkSeqNo)>1)
		{
			echo "alert('Select 1 CS Application to be Modified.')";
		}
		else
		{
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$inputTypeSeqNo = $chkSeqNo_val;
			}
			
			echo "UpdateObTran('".$inputTypeSeqNo."');";
		}
		exit();
	break;
	
	default:
	break;
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
		<FORM name='frmCS' id="frmCS" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        	
			<div id="csCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	
	pager('csAjaxResult.php','csCont','load',0,0,'','','&empNo='<?=$_GET["empNo"]?>,'../../../images/');  

	function newRef(act){
		pager('csAjaxResult.php','csCont','refresh',0,0,'','','','../../../images/');  	
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
		
		var param = '?action=getEmpInfo&empNo='+eleVal;
		
		var k = evt.keyCode | evt.which;
		
		switch(k){
			case 8:
				clearFld();
			break;
			case 13:
				new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
					method : 'get',
					parameters : $('frmCS').serialize(),
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
		var eleVal = document.frmCS.txtAddEmpNo.value;
		var csDateFrom = document.frmCS.csDateFrom.value;
		//var refNo = document.frmCS.refNo.value;
		
	/*	if(refNo=="")
		{
			alert("Generate Reference No. First.");
			return false;
		}
		else */
		
		
		if(eleVal=="")
		{
			alert("Select Employee first.");
			return false;
		}
		else
		{
			var param = '?action=getEmpShiftCode&empNo='+eleVal+'&csDateFrom='+csDateFrom;
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

	function saveCsDetail()
	{
		var csFields = $('frmCS').serialize(true);
		
		
		
		/*if(csFields["refNo"]=="")
		{
			alert("Generate Ref. No. first.");
			$('refNo').focus();
			return false;
		}*/
		
		if(csFields["txtAddEmpNo"]=="")
		{
			alert("Select Employee first.");
			$('txtAddEmpNo').focus();
			return false;
		}
		
	
		
		if(csFields["schedTimeIn"]=="Set the Schedule first.")
		{
			alert("Shift Schedule is required.");
			$('schedTimeIn').focus();
			return false;
		}
		
		if((csFields["csTimeIn"]=="")||(csFields["csTimeIn"]==":"))
		{
			alert("CS Time - In is required.");
			$('csTimeIn').focus();
			return false;
		}
		
		if((csFields["csTimeOut"]=="")||(csFields["csTimeOut"]==":"))
		{
			alert("CS Time - Out is required.");
			$('csTimeOut').focus();
			return false;
		}
		
		if(csFields["csreason"]=="")
		{
			alert("Reason for CS is required.");
			$('csreason').focus();
			return false;
		}
		
		params = 'cs.php?action=saveCsSched';
			
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmCS').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				pager('csAjaxResult.php','csCont','load',0,0,'','','','../../../images/');  
			},
			onCreate : function (){
				$('indicator2').src="../../../images/wait.gif";
				document.frmCS.btnSave.disabled = true;
				document.frmCS.btnApp.style.visibility = 'hidden';
				document.frmCS.btnDel.style.visibility = 'hidden';
			},
			onSuccess : function (){
				$('indicator2').innerHTML="";
				document.frmCS.btnSave.disabled = false;
				document.frmCS.btnApp.style.visibility = 'visible';
				document.frmCS.btnDel.style.visibility = 'visible';
			}	
		});
		
	}
	
	
	function upObTran(act,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		if(act=='Approved')
			var deleShiftCode = confirm('Are you sure you want to Approved the selected CS Application?');
		else
			var deleShiftCode = confirm('Are you sure you want to delete the selected OB Application?');
			
			
		if(deleShiftCode == true){
			var param = '?action='+act;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmCS').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('csAjaxResult.php','csCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmCS.btnSave.disabled = true;
					document.frmCS.btnApp.style.visibility = 'hidden';
					document.frmCS.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmCS.btnSave.disabled = false;
					document.frmCS.btnApp.style.visibility = 'visible';
					document.frmCS.btnDel.style.visibility = 'visible';
				}	
			});	
			
			
		}
	}
	
	function getSeqNo()
	{
		var param = '?action=getSeqNo&seqNo=';
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			parameters : $('frmCS').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}
	
	function UpdateObTran(inputTypeSeqNo)
	{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:750, 
		height:410, 
		zIndex: 100, 
		resizable: false, 
		parameters : $('frmCS').serialize(),
		minimizable : true,
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('transaction_popup.php?&moduleName=ChangeShift&inputTypeSeqNo='+inputTypeSeqNo);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		       	pager('csAjaxResult.php','csCont','load',0,0,'','','','../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
		
	}
	
	function checkAll(field)
	{
		var chkob = document.frmCS.elements['chkseq[]'];
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