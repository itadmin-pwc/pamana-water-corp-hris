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
			$arr_RdRec = $crdObj->getTblData("tblTK_ChangeRDApp", " and empNo='".$_GET["txtAddEmpNo"]."' and cRDDateTo='".date("m/d/Y", strtotime($_GET["rdDateTo"]))."' and cRDStat='P'", "", "sqlAssoc");
			
		
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
			
			
			//Get RestDay from Timesheet
			$arr_EmpTsDtl = $crdObj->getTblData("tblTK_Timesheet", " and empNo='".$_GET["txtAddEmpNo"]."'  and dayType='02'", "", "");
			foreach($arr_EmpTsDtl as $arr_EmpTsDtl_Val)
			{
				$currDateRd.=date("m/d/Y", strtotime($arr_EmpTsDtl_Val["tsDate"])).",";
			}
			
			$currDateRd = explode(",", substr($currDateRd,0,strlen($currDateRd) - 1));
			
			$arr_EmpShiftDtl = $crdObj->getTblData("tblTK_EmpShift", " and empNo='".$_GET["txtAddEmpNo"]."'", "", "sqlAssoc");
			$arr_ShiftCode_Dtl = $crdObj->getTblData("tblTK_ShiftDtl", " and shftCode='".$arr_EmpShiftDtl["shiftCode"]."' and RestDayTag='Y'", "", "sqlAssoc");
			
			if($arr_ShiftCode_Dtl["dayCode"]=='7')
				$empCurrRd = 0;
			else
				$empCurrRd = $arr_ShiftCode_Dtl["dayCode"];
			
			
			//Check From Date
			$getPdNum_Open = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'  and '".$_GET["rdDateFrom"]."' between pdFrmDate and pdToDate and pdStat='O' ", "", "sqlAssoc");
			
			/*Check if the Transaction already Exists*/
			if($arr_RdRec["empNo"]!="")
				echo "alert('Transaction already exist.')";
			else
			{
				/*Check if the From Date is between the Open or 1 Cut Off in Advance*/
				if($getPdNum_Open["pdSeries"]!="")
				{
					 /*Check if the From Date is a Restday*/
					 if(in_array(date("m/d/Y", strtotime($_GET["rdDateFrom"])), $currDateRd))
					 {
					 	 /*Check if the To Date is between the Open Cut Off*/
						$getPdNum_OpenToDate = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'  and '".$_GET["rdDateTo"]."' between pdFrmDate and pdToDate and pdStat='O' ", "", "sqlAssoc");
						if($getPdNum_OpenToDate["pdSeries"]!="")
						{
							if(in_array(date("m/d/Y", strtotime($_GET["rdDateTo"])), $currDateRd))
					 		{
								if($_GET["Edited"]!="")
								{
									$insRecRdTran = $crdObj->tran_Crd($_GET,($_GET["Edited"]!=""?"Update":"Add"));
									echo "alert('".$insRecRdTran."')";
								}
								else
									echo "alert('You cannot change a restday wherein your to date is already a restday.')";
							}
							else
							{
								$insRecRdTran = $crdObj->tran_Crd($_GET,($_GET["Edited"]!=""?"Update":"Add"));
								echo "alert('".$insRecRdTran."')";
							}
						}
						/*Check if the To Date is 1 Cut Off in Advance*/
						else
						{
							$getPdNum_OpenToDate2 = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'   and '".$_GET["rdDateTo"]."' between pdFrmDate and pdToDate and pdNumber='".$pdNum."' and pdYear='".$pdYear."' ", "", "sqlAssoc");
							if($getPdNum_OpenToDate2["pdSeries"]!="")
							{
								if(date("w", strtotime($_GET["rdDateTo"]))!=$empCurrRd)
								{
									$insRecRdTran = $crdObj->tran_Crd($_GET,($_GET["Edited"]!=""?"Update":"Add"));
									echo "alert('".$insRecRdTran."')";
								}
								else
								{
									if($_GET["Edited"]!="")
									{
										$insRecRdTran = $crdObj->tran_Crd($_GET,($_GET["Edited"]!=""?"Update":"Add"));
										echo "alert('".$insRecRdTran."')";
									}
									else
										echo "alert('You cannot change a restday wherein your to date is already a restday.')";
								}
							}
							else
								echo "alert('Selected Rest Day To Date is not part of the Current nor the Advance Cut Off.');";
							
						}
					 }
					 else
					 	echo "alert('You cannot change a restday wherein your from date is not a restday.')";
					 
				}
				else
				{
					$getPdNum_Open2 = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'   and '".$_GET["rdDateFrom"]."' between pdFrmDate and pdToDate and pdNumber='".$pdNum."' and pdYear='".$pdYear."' ", "", "sqlAssoc");
					
					if($getPdNum_Open2["pdSeries"]!="")
					{
						if(date("w", strtotime($_GET["rdDateFrom"]))==$empCurrRd)
						{
							/*Check if the To Date is between the Open Cut Off*/
							$getPdNum_OpenToDate = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'  and '".$_GET["rdDateTo"]."' between pdFrmDate and pdToDate and pdStat='O' ", "", "sqlAssoc");
							if($getPdNum_OpenToDate["pdSeries"]!="")
							{
								if(in_array(date("m/d/Y", strtotime($_GET["rdDateTo"])), $currDateRd))
								{
									if($_GET["Edited"]!="")
									{
										$insRecRdTran = $crdObj->tran_Crd($_GET,($_GET["Edited"]!=""?"Update":"Add"));
										echo "alert('".$insRecRdTran."')";
									}
									else
										echo "alert('You cannot change a restday wherein your to date is already a restday.')";
								}
								else
								{
									$insRecRdTran = $crdObj->tran_Crd($_GET,($_GET["Edited"]!=""?"Update":"Add"));
									echo "alert('".$insRecRdTran."')";
								}
							}
							/*Check if the To Date is 1 Cut Off in Advance*/
							else
							{
								$getPdNum_OpenToDate2 = $crdObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'   and '".$_GET["rdDateTo"]."' between pdFrmDate and pdToDate and pdNumber='".$pdNum."' and pdYear='".$pdYear."' ", "", "sqlAssoc");
								if($getPdNum_OpenToDate2["pdSeries"]!="")
								{
									if(date("w", strtotime($_GET["rdDateTo"]))!=$empCurrRd)
									{
										$insRecRdTran = $crdObj->tran_Crd($_GET,($_GET["Edited"]!=""?"Update":"Add"));
										echo "alert('".$insRecRdTran."')";
									}
									else
									{
										if($_GET["Edited"]!="")
										{
											$insRecRdTran = $crdObj->tran_Crd($_GET,($_GET["Edited"]!=""?"Update":"Add"));
											echo "alert('".$insRecRdTran."')";
										}
										else
											echo "alert('You cannot change a restday wherein your to date is already a restday.')";
									}
								}
								else
									echo "alert('Selected Rest Day To Date is not part of the Current nor the Advance Cut Off.');";
								
							}
						}
						else
							echo "alert('You cannot change a restday wherein your from date is not a restday.')";
					}
					else
					{
						echo "alert('Selected Rest Day From Date is not part of the Current nor the Advance Cut Off.');";
					}
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
	
	
	case "getSeqNo":
		$chkSeqNo = $_GET["chkseq"];
		
		if(sizeof($chkSeqNo)>1)
		{
			echo "alert('Select 1 CRD Application to be Modified.')";
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



?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		   <SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url('../../../js/themes/alphacube.css');</STYLE>	
		
		<SCRIPT type="text/javascript" src="../../../includes/calendar.js"></SCRIPT>
        
        
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		<STYLE>@import url('../../../includes/calendar.css');</STYLE>
        
        

		 <script type="text/javascript">
		function cnclLockSys(){
			Windows.getWindow('winLcok').close();
			$('passLock').style.visibility = 'hidden';
		}	
		function Dolock(){
			var winLock = new Window({
				id : "winLcok",
				className: "alphacube", 
				resizable: false, 
				draggable:false, 
				minimizable : false,
				maximizable : false,
				closable 	: false,
				width: 200,
				height : 80
			});
				$('passLock').style.visibility = 'visible';
				winLock.setContent('passLock', false, false);				
				winLock.setZIndex(500);
				winLock.setDestroyOnClose();
				winLock.showCenter(true);				
		}
		</script>

	</HEAD>
	<BODY>
		<FORM name='frmRD' id="frmRD" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        	
			<div id="rdCont"></div>
			<div id="indicator1" align="center"></div>
            
             <div id="passLock" style="visibility:hidden;" >
                <TABLE align="center" border="0" width="100%">
                    
                    <TR>
                      <td align="center"><img src="../../../images/loading.gif" width="120" height="40"></td>
                  </TR>
                    <TR>
                        <td align="center">
                            <font class='cnfrmLbl style6'><strong>Saving</strong></font></td>
                    </TR>
                </TABLE>			
            </div>  
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
		
		if((rdFields["rdDateFrom"]=="")||(rdFields["rdDateFrom"]==":"))
		{
			alert("Select rest day from date.");
			$('rdDateTo').focus();
			return false;
		}
		
		if((rdFields["rdDateTo"]=="")||(rdFields["rdDateTo"]==":"))
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
					//pager('crdAjaxResult.php','rdCont','load',0,0,'','','','../../../images/');  
				},
				onCreate : function(){
					Dolock();
				},
				onSuccess: function (){
					cnclLockSys();
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
	
	function getSeqNo()
	{
		var param = '?action=getSeqNo&seqNo=';
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
			method : 'get',
			parameters : $('frmRD').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}
	
	function UpdateRdTran(inputTypeSeqNo)
	{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:750, 
		height:410, 
		zIndex: 100, 
		resizable: false, 
		parameters : $('frmRD').serialize(),
		minimizable : true,
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('transaction_popup.php?&moduleName=ChangeRestDay&inputTypeSeqNo='+inputTypeSeqNo);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		       pager('crdAjaxResult.php','rdCont','load',0,0,'','','','../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
		
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
	
	function saveMessage(msg)
	{
		alert(msg);
		document.frmRD.rdDateFrom.value="";
		document.frmRD.rdDateTo.value="";
		document.frmRD.rdreason.value="";
		
	
	}
	
</SCRIPT>