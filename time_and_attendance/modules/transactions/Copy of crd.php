<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("transaction_obj.php");


$crdObj = new transactionObj($_GET,$_SESSION);
$crdObj->validateSessions('','MODULES');


switch($_GET["action"])
{
	case "NEWREFNO":
		/*$arr_lastRefNo = $crdObj->getLastRefNo("tblTk_ChangeRDApp");
		$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;
		
		echo "$('refNo').value=$lastRefNo;";*/
		
		echo "$('csreason').value='';";
		
		echo "$('txtAddEmpNo').value='';";
		echo "$('shiftSched').value='';";
		echo "$('schedTimeIn').value='';";
		echo "$('schedTimeOut').value='';";
		
		echo "$('txtobTimeIn').value='';";
		echo "$('txtobTimeOut').value='';";
		
		
		echo "document.frmRD.schedTimeIn.disabled=false; document.frmRD.chkCrossDay.disabled=false; document.frmRD.schedTimeOut.disabled=false; document.frmRD.shftTimeOut.disabled=false; document.frmRD.csTimeIn.disabled=false; document.frmRD.csTimeOut.disabled=false; document.frmRD.payPd.disabled=false;  document.frmRD.payPd.disabled=false;  document.frmRD.csreason.disabled=false; document.frmRD.chkStat.disabled=false; document.frmRD.btnSave.disabled=false;";

		exit();		
	break;
	
	

	case "saveRdSched":
			
			//Check no. of Records
			$arr_RdRec = $crdObj->getTblData("tblTK_ChangeRDApp", " and empNo='".$_GET["txtAddEmpNo"]."' and cRDDateTo='".date("m/d/Y", strtotime($_GET["rdDateTo"]))."'", "", "sqlAssoc");
			
			$getPdNum_NxtCutOff = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'  and  pdStat='O' ", "", "sqlAssoc");
			if($getPdNum_NxtCutOff["pdNumber"] == 24)
			{
				$pdNum = '1';
				$pdYear = $getPdNum_NxtCutOff["pdYear"] + 1;
			}
			else
			{
				$pdNum = $getPdNum_NxtCutOff["pdNumber"]+1;
				$pdYear =  $getPdNum_NxtCutOff["pdYear"];
			}
			
			
			
			
			//Check From Date
			$getPdNum_Open = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'  and '".$_GET["rdDateFrom"]."' between pdFrmDate and pdToDate and pdStat='O' ", "", "sqlAssoc");
			$arr_EmpShiftDtl = $crdObj->getTblData("tblTK_EmpShift", " and empNo='".$_GET["txtAddEmpNo"]."' and pdNumber='".$getPdNum_Open["pdNumber"]."' and pdYear='".$getPdNum_Open["pdYear"] ."'", "", "sqlAssoc");
			$arr_ShiftCode_Dtl = $crdObj->getTblData("tblTK_ShiftDtl", " and shftCode='".$arr_EmpShiftDtl["shiftCode"]."' and RestDayTag='Y'", "", "sqlAssoc");
			
			if($arr_ShiftCode_Dtl["dayCode"]=='7')
				$empCurrRd = 0;
			else
				$empCurrRd = $arr_ShiftCode_Dtl["dayCode"];
							
			
			if($getPdNum_Open["pdSeries"]!="")
			{
				//Check To Date
				$getPdNum_OpenToDate = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'  and '".$_GET["rdDateTo"]."' between pdFrmDate and pdToDate and pdStat='O' ", "", "sqlAssoc");
				if($getPdNum_OpenToDate["pdSeries"]!="")
				{
					if($arr_RdRec["empNo"]!="")
						echo "alert('Transaction already exist.')";
					else
					{
						
						if(date("w", strtotime($_GET["rdDateFrom"]))==$empCurrRd)	
						{
							if(date("w", strtotime($_GET["rdDateTo"]))!=$empCurrRd)	
							{
								$insRecRdTran = $crdObj->tran_Crd($_GET,"Add");
								echo "alert('".$insRecRdTran."')";
							}
							else
							{
								echo "alert('You cannot change a restday wherein your to date is already a restday.')";
							}
						}
						else
						{
							echo "alert('You cannot change a restday wherein your from date is not a restday.')";
						}
					}
				}
				else
				{
					$getPdNum_OpenToDate2 = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'   and '".$_GET["rdDateTo"]."' between pdFrmDate and pdToDate and pdNumber='".$pdNum."' and pdYear='".$pdYear."' ", "", "sqlAssoc");
				
					if($getPdNum_OpenToDate2["pdSeries"]!="")
					{
						if($arr_RdRec["empNo"]!="")
							echo "alert('Transaction already exist.')";
						else
						{
							
							if(date("w", strtotime($_GET["rdDateFrom"]))==$empCurrRd)	
							{
								if(date("w", strtotime($_GET["rdDateTo"]))!=$empCurrRd)	
								{
									$insRecRdTran = $crdObj->tran_Crd($_GET,"Add");
									echo "alert('".$insRecRdTran."')";
								}
								else
								{
									echo "alert('You cannot change a restday wherein your to date is already a restday.')";
								}
							}
							else
							{
								echo "alert('You cannot change a restday wherein your from date is not a restday.')";
							}
						}
					}
					else
					{
						echo "alert('Selected Rest Day To Date is not part of the Current nor the Advance Cut Off.');";
					}
				}
			}
			else
			{
				$getPdNum_Open2 = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'   and '".$_GET["rdDateFrom"]."' between pdFrmDate and pdToDate and pdNumber='".$pdNum."' and pdYear='".$pdYear."' ", "", "sqlAssoc");
				
				if($getPdNum_Open2["pdSeries"]!="")
				{
					//Check To Date
					$getPdNum_OpenToDate = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'  and '".$_GET["rdDateTo"]."' between pdFrmDate and pdToDate and pdStat='O' ", "", "sqlAssoc");
					if($getPdNum_OpenToDate["pdSeries"]!="")
					{
						if($arr_RdRec["empNo"]!="")
							echo "alert('Transaction already exist.')";
						else
						{
							if(date("w", strtotime($_GET["rdDateFrom"]))==$empCurrRd)	
							{
								if(date("w", strtotime($_GET["rdDateTo"]))!=$empCurrRd)	
								{
									$insRecRdTran = $crdObj->tran_Crd($_GET,"Add");
									echo "alert('".$insRecRdTran."')";
								}
								else
								{
									echo "alert('You cannot change a restday wherein your to date is already a restday.')";
								}
							}
							else
							{
								echo "alert('You cannot change a restday wherein your from date is not a restday.')";
							}
						}
					}
					else
					{
						$getPdNum_OpenToDate2 = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'   and '".$_GET["rdDateTo"]."' between pdFrmDate and pdToDate and pdNumber='".$pdNum."' and pdYear='".$pdYear."' ", "", "sqlAssoc");
					
						if($getPdNum_OpenToDate2["pdSeries"]!="")
						{
							if($arr_RdRec["empNo"]!="")
								echo "alert('Transaction already exist.')";
							else
							{
								if(date("w", strtotime($_GET["rdDateFrom"]))==$empCurrRd)	
								{
									if(date("w", strtotime($_GET["rdDateTo"]))!=$empCurrRd)	
									{
										$insRecRdTran = $crdObj->tran_Crd($_GET,"Add");
										echo "alert('".$insRecRdTran."')";
									}
									else
									{
										echo "alert('You cannot change a restday wherein your to date is already a restday.')";
									}
								}
								else
								{
									echo "alert('You cannot change a restday wherein your from date is not a restday.')";
								}
							}
						}
						else
						{
							echo "alert('Selected Rest Day To Date is not part of the Current nor the  Advance Cut Off.');";
						}
					}
				}
				else
				{
						echo "alert('Selected Rest Day From Date is not part of the Current nor the Advance Cut Off.');";
				}
				
			}
			
			
		exit();	
	break;
	
	case "Delete":
		$chkSeqNo = $_GET["chkseq"];
		if(sizeof($chkSeqNo)>=1)
		{
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryDel = "Delete from tblTK_ChangeRDApp where seqNo='".$chkSeqNo_val."'";
				$resDel = $crdObj->execQry($qryDel);
			}
			
			echo "alert('Selected RD Application already deleted.')";
		}
		else
		{
			echo "alert('Select RD Application to be Approved.')";
		}
		exit();
	break;
	
	case "Approved":
		$chkSeqNo = $_GET["chkseq"];
		if(sizeof($chkSeqNo)>=1)
		{
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryApp = "Update  tblTK_ChangeRDApp set dateApproved='".date("m/d/Y")."',userApproved='".$_SESSION["employee_number"]."', cRDStat='A' where seqNo='".$chkSeqNo_val."';";
				$resApp = $crdObj->execQry($qryApp);
			}
			
			echo "alert('Selected RD Application already Approved.')";
		}
		else
		{
			echo "alert('Select RD Application to be Approved.')";
		}
		exit();
	break;
	
	case "disabledFields":
		for($x=1; $x<=10; $x++)
		{
			if($x==$_GET["onField"])
			{
				echo "document.frmRD.btnSave".$_GET["onField"].".disabled=false; ";
				echo "document.frmRD.rdreason".$_GET["onField"].".disabled=false; ";
			}
			else
			{
				echo "document.frmRD.btnSave".$x.".disabled=true; ";
				echo "document.frmRD.rdreason".$x.".disabled=true; ";
			}
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
		<FORM name='frmRD' id="frmRD" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        	
			<div id="rdCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	
	pager('crdAjaxResult.php','rdCont','load',0,0,'','','&empNo='<?=$_GET["empNo"]?>,'../../../images/');  

	function newRef(act){
		pager('crdAjaxResult.php','rdCont','refresh',0,0,'','','','../../../images/');  	
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
		
		//var refNo = document.frmRD.refNo.value;
		
		var param = 'crdAjaxResult.php?action=getEmpInfo&empNo='+eleVal;
		
		var k = evt.keyCode | evt.which;
		
		switch(k){
			case 8:
				clearFld();
			break;
			case 13:
				pager('crdAjaxResult.php','rdCont','load',0,0,'','','&action=getEmpInfo&empNo='+eleVal,'../../../images/');  
			break;
		}
	}
	
	function enabledFields(field)
	{
		 params = 'crd.php?action=disabledFields&onField='+field;
				
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmRD').serialize(),
				onComplete : function (req){
					eval(req.responseText);
					
				}	
			});
	}
	
	function getEmpShift()
	{
		var eleVal = document.frmRD.txtAddEmpNo.value;
		var csDateFrom = document.frmRD.csDateFrom.value;
		var refNo = document.frmRD.refNo.value;
		
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

	function saveRdDetail()
	{
		
		var rdFields = $('frmRD').serialize(true);
		
		
		
		
		if(rdFields["txtAddEmpNo"]=="")
		{
			alert("Select Employee first.");
			$('txtAddEmpNo').focus();
			return false;
		}
		
		if(rdFields["rdDateFrom"]=="")
		{
			alert("Select rest day from date.");
			$('rdDateTo').focus();
			return false;
		}
		
		if(rdFields["rdDateTo"]=="")
		{
			alert("Select rest day to date.");
			$('rdDateTo').focus();
			return false;
		}
		
		if(rdFields["rdDateFrom"]==rdFields["rdDateTo"])
		{
			alert("No changes to be made.");
			return false;
		}
		
		if(rdFields["rdreason"]=="")
		{
			alert("Reason for RD is required.");
			$('rdreason').focus();
			return false;
		}
		
		
		var changeRdConfirm = confirm('Are you sure you want to update the existing Rest Day of the Employee?');
		if(changeRdConfirm == true){
			params = 'crd.php?action=saveRdSched';
				
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmRD').serialize(),
				onComplete : function (req){
					eval(req.responseText);
					pager('crdAjaxResult.php','rdCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmRD.btnSave.style.visibility = 'hidden';
					document.frmRD.btnApp.style.visibility = 'hidden';
					document.frmRD.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmRD.btnSave.style.visibility = 'visible';
					document.frmRD.btnApp.style.visibility = 'visible';
					document.frmRD.btnDel.style.visibility = 'visible';
				}	
			});
		}
	}
	
	function delObTran(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected RD Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Delete&seqNo='+seqNo;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmRD').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('crdAjaxResult.php','rdCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmRD.btnSave.style.visibility = 'hidden';
					document.frmRD.btnApp.style.visibility = 'hidden';
					document.frmRD.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmRD.btnSave.style.visibility = 'visible';
					document.frmRD.btnApp.style.visibility = 'visible';
					document.frmRD.btnDel.style.visibility = 'visible';
				}
			});	
			
			
		}
	}
	
	function upObTran(act,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to Approved the selected RD Application?');
		
		if(deleShiftCode == true){
			var param = '?action=Approved';
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				parameters : $('frmRD').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
					pager('crdAjaxResult.php','rdCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
					document.frmRD.btnSave.style.visibility = 'hidden';
					document.frmRD.btnApp.style.visibility = 'hidden';
					document.frmRD.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmRD.btnSave.style.visibility = 'visible';
					document.frmRD.btnApp.style.visibility = 'visible';
					document.frmRD.btnDel.style.visibility = 'visible';
				}
			});	
			
			
		}
	}
	
	
	
	function checkAll(field)
	{
		var chkob = document.frmRD.elements['chkseq[]'];
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