<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("transaction_obj.php");


$obObj = new transactionObj($_GET,$_SESSION);
$obObj->validateSessions('','MODULES');


switch($_GET["action"])
{
	case "NEWREFNO":
		/*$arr_lastRefNo = $obObj->getLastRefNo("tblTK_OBApp");
		$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;
		echo "$('refNo').value=$lastRefNo;";*/
		
		
		echo "$('obreason').value='';";
		
		echo "$('txtAddEmpNo').value='';";
		echo "$('shiftSched').value='';";
		echo "$('schedTimeIn').value='';";
		echo "$('schedTimeOut').value='';";
		
		echo "$('txtobTimeIn').value='';";
		echo "$('txtobTimeOut').value='';";
		
		
		
		echo "document.frmOB.obreason.disabled=false; document.frmOB.schedTimeIn.disabled=false;   document.frmOB.schedTimeOut.disabled=false;   document.frmOB.rdnDeduct8.disabled=false; document.frmOB.obdestination.disabled=false; document.frmOB.txtobTimeIn.disabled=false; document.frmOB.txtobTimeOut.disabled=false; document.frmOB.btnSave.disabled=false;";

		exit();		
	break;
	
	case 'getEmpInfo':
		
		$empInfo = $obObj->getUserInfo($_SESSION['company_code'],$_GET['empNo'],'');
		
		/*if($_GET["txtRefNo"]=="")
			echo "alert('Reference No. is required.');";
		else
		{*/
		
			echo "$('obreason').value='';";
			echo "$('txtobTimeIn').value='';";
			echo "$('txtobTimeOut').value='';";
			
			$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
			echo "$('txtEmpName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";
			
			$deptName = $obObj->getDeptDescGen($_SESSION["company_code"],$empInfo["empDiv"], $empInfo["empDepCode"]);
			$posName = $obObj->getpositionwil("where compCode='".$_SESSION["company_code"]."' and posCode='".$empInfo["empPosId"]."'",'2');
			
			echo "$('txtDeptPost').value='".htmlspecialchars(addslashes($deptName["deptDesc"]))." - ".$posName["posDesc"]."';";
			
			$shiftCode = $obObj->getTblData("tblTk_EmpShift", " and empNo='".$_GET["empNo"]."'", "", "sqlAssoc");
			$array_day = array('Mon'=>1 , 'Tue' => 2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6, 'Sun'=>7);
			
			$shiftCodeDtl = $obObj->getTblData("tblTk_ShiftDtl", " and shftCode='".$shiftCode["shiftCode"]."' and dayCode='".$array_day[date("D", strtotime($_GET["obDate"]))]."'", "", "sqlAssoc");
			
				
			//$shiftCodeDtl = $obObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("m/d/Y", strtotime($_GET["obDate"]))."'", "", "sqlAssoc");
	
			if($shiftCodeDtl["shftTimeIn"]=="")
				echo "$('obreason').value='Set the Schedule first.'; document.frmOB.obreason.disabled=true; document.frmOB.rdnDeduct8.disabled=true; document.frmOB.obdestination.disabled=true; $('empPayGrp').value='".$empInfo['empPayGrp']."'; $('empPayCat').value='".$empInfo['empPayCat']."'; document.frmOB.txtobTimeIn.disabled=true; document.frmOB.txtobTimeOut.disabled=true; document.frmOB.schedTimeIn.disabled=true;   document.frmOB.schedTimeOut.disabled=true;   document.frmOB.btnSave.disabled=true;";
			else
			{
				echo "$('shiftSched').value='".$shiftCodeDtl["shftTimeIn"]." - ".$shiftCodeDtl["shftTimeOut"]."'; $('schedTimeIn').value='".$shiftCodeDtl["shftTimeIn"]."'; $('empPayGrp').value='".$empInfo['empPayGrp']."'; $('empPayCat').value='".$empInfo['empPayCat']."'; $('schedTimeOut').value='".$shiftCodeDtl["shftTimeOut"]."'; document.frmOB.obreason.disabled=false; document.frmOB.schedTimeIn.disabled=false;   document.frmOB.schedTimeOut.disabled=false;   document.frmOB.rdnDeduct8.disabled=false; document.frmOB.obdestination.disabled=false; document.frmOB.txtobTimeIn.disabled=false; document.frmOB.txtobTimeOut.disabled=false; document.frmOB.btnSave.disabled=false;";
			}
		/*}*/
		exit();			
	break;
	
	case "getEmpShiftCode":
		$shiftCode = $obObj->getTblData("tblTk_EmpShift", " and empNo='".$_GET["empNo"]."'", "", "sqlAssoc");
		$array_day = array('Mon'=>1 , 'Tue' => 2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6, 'Sun'=>7);
		
		$shiftCodeDtl = $obObj->getTblData("tblTk_ShiftDtl", " and shftCode='".$shiftCode["shiftCode"]."' and dayCode='".$array_day[date("D", strtotime($_GET["obDate"]))]."'", "", "sqlAssoc");
		
		//$shiftCodeDtl = $obObj->getTblData("tblTk_TimeSheet", " and empNo='".$_GET['empNo']."' and tsDate='".date("m/d/Y", strtotime($_GET["obDate"]))."'", "", "sqlAssoc");
		
		if($shiftCodeDtl["shftTimeIn"]=="")
			echo "$('obreason').value='Set the Schedule first.';document.frmOB.rdnDeduct8.disabled=true; document.frmOB.schedTimeIn.disabled=true;   document.frmOB.schedTimeOut.disabled=true;   document.frmOB.obreason.disabled=true; document.frmOB.obdestination.disabled=true; document.frmOB.txtobTimeIn.disabled=true; document.frmOB.txtobTimeOut.disabled=true; document.frmOB.btnSave.disabled=true;";
		else
			echo "$('shiftSched').value='".$shiftCodeDtl["shftTimeIn"]." - ".$shiftCodeDtl["shftTimeOut"]."'; document.frmOB.rdnDeduct8.disabled=false; document.frmOB.schedTimeIn.disabled=false;   document.frmOB.schedTimeOut.disabled=false;   $('schedTimeIn').value='".$shiftCodeDtl["shftTimeIn"]."';  $('schedTimeOut').value='".$shiftCodeDtl["shftTimeOut"]."'; document.frmOB.obreason.disabled=false; document.frmOB.obdestination.disabled=false; document.frmOB.txtobTimeIn.disabled=false; document.frmOB.txtobTimeOut.disabled=false; document.frmOB.btnSave.disabled=false;";

		exit();
	break;
	
	
	case "saveObSched":
		$dateTIn = $_GET["dateFiled"]." ".$_GET["txtobTimeIn"];
		$dateTOut = $_GET["dateFiled"]." ".$_GET["txtobTimeOut"];
		$shiftOut = $_GET["dateFiled"]." ".$_GET["schedTimeOut"];
		
		$getPdNum_NxtCutOff = $obObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'  and  pdStat='O' ", "", "sqlAssoc");
		
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
		
		
		$chkPayPeriod =  $obObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."' and '".$_GET["obDate"]."' between pdFrmDate and pdToDate and pdStat='O' ", "", "sqlAssoc");
		
		
		if($chkPayPeriod["pdSeries"]!="")
		{
			if((strtotime($dateTOut))<=(strtotime($dateTIn)))
				echo "alert('OB Time Out should not be less than or equal to OB Time In.');";
			else
			{
				//Check no. of Records
				$arr_ObRec = $obObj->getTblData("tblTK_OBApp", " and empNo='".$_GET["txtAddEmpNo"]."' and obDate='".date("m/d/Y", strtotime($_GET["obDate"]))."'", "", "sqlAssoc");
			
				if($arr_ObRec["empNo"]!="")
				{
					$checking_overlapping = $obObj->checkOBEntryValidation($_GET);
					if(($checking_overlapping!="") && ($_GET["Edited"]==""))
						echo "alert('Overlapping on the Existing OB Record where OB Time In / Out : ".$checking_overlapping.".');";
					else
					{
						$insRecObTran = $obObj->tran_Ob($_GET,($_GET["Edited"]!=""?"Update":"Add"));
						echo "saveMessage('".$insRecObTran."')";
					}
				}
				else
				{
					$insRecObTran = $obObj->tran_Ob($_GET,($_GET["Edited"]!=""?"Update":"Add"));
					echo "saveMessage('".$insRecObTran."')";
				}
			}
		}
		else
		{
			$getPdNum_Open2 = $obObj->getTblData("tblPayPeriod", " and payGrp='".$_GET["empPayGrp"]."' and payCat='".$_GET["empPayCat"]."'   and '".$_GET["obDate"]."' between pdFrmDate and pdToDate and pdNumber='".$pdNum."' and pdYear='".$pdYear."' ", "", "sqlAssoc");
			if($getPdNum_Open2["pdSeries"]!="")
			{
				if((strtotime($dateTOut))<=(strtotime($dateTIn)))
					echo "alert('OB Time Out should not be less than or equal to OB Time In.');";
				else
				{
					//Check no. of Records
					$arr_ObRec = $obObj->getTblData("tblTK_OBApp", " and empNo='".$_GET["txtAddEmpNo"]."' and obDate='".date("m/d/Y", strtotime($_GET["obDate"]))."'", "", "sqlAssoc");
				
					if($arr_ObRec["empNo"]!="")
					{
						if(($checking_overlapping!="") && ($_GET["Edited"]==""))
							echo "alert('Overlapping on the Existing OB Record where OB Time In / Out : ".$checking_overlapping.".');";
						else
						{
							$insRecObTran = $obObj->tran_Ob($_GET,($_GET["Edited"]!=""?"Update":"Add"));
							echo "saveMessage('".$insRecObTran."')";
						}
					}
					else
					{
						$insRecObTran = $obObj->tran_Ob($_GET,($_GET["Edited"]!=""?"Update":"Add"));
						echo "saveMessage('".$insRecObTran."')";
					}
				}
			}
			else
			{
				echo "alert('Selected OB Date is not part of the Current nor the Advance Cut Off.');";
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
				$qryApp = "Update  tblTk_ObApp set dateApproved='".date("m/d/Y")."',userApproved='".$_SESSION["employee_number"]."',obStat='A' where seqNo='".$chkSeqNo_val."';";
				$resApp = $obObj->execQry($qryApp);
			}
			
			echo "alert('Selected OB Application already Approved.')";
		}
		else
		{
			echo "alert('Select OB Application to be Approved.')";
		}
		exit();
	break;
	
	case "getSeqNo":
		$chkSeqNo = $_GET["chkseq"];
		
		if(sizeof($chkSeqNo)>1)
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
		<FORM name='frmOB' id="frmOB" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="obCont"></div>
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
		
		if((obFields["schedTimeIn"]=="")||(obFields["schedTimeIn"]==":"))
		{
			alert("Shift Schedule Time In is required.");
			$('schedTimeIn').focus();
			return false;
		}
		
		
		
		
		if((obFields["schedTimeOut"]=="")||(obFields["schedTimeOut"]==":"))
		{
			alert("Shift Schedule Time Out is required.");
			$('schedTimeOut').focus();
			return false;
		}
		
		
		if(obFields["obreason"]=="")
		{
			alert("Purpose of the OB is required.");
			$('obreason').focus();
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
		
		if(obFields["schedTimeOut"]=="00:00")
		{
			var conUser = confirm("Are you sure OB Sched. Out = 00:00?");
			if(conUser==true)
			{
				params = 'ob.php?action=saveObSched';
				
				new Ajax.Request(params,{
					method : 'get',
					parameters : $('frmOB').serialize(),
					onComplete : function (req){
						eval(req.responseText);
						//pager('obAjaxResult.php','obCont','load',0,0,'','','','../../../images/');  
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
		else
		{
			params = 'ob.php?action=saveObSched';
				
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmOB').serialize(),
				onComplete : function (req){
					eval(req.responseText);
					//pager('obAjaxResult.php','obCont','load',0,0,'','','','../../../images/');  
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
					document.frmOB.btnApp.style.visibility = 'hidden';
					document.frmOB.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmOB.btnSave.disabled = false;
					document.frmOB.btnApp.style.visibility = 'visible';
					document.frmOB.btnDel.style.visibility = 'visible';
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
					document.frmOB.btnApp.style.visibility = 'hidden';
					document.frmOB.btnDel.style.visibility = 'hidden';
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
					document.frmOB.btnSave.disabled = false;
					document.frmOB.btnApp.style.visibility = 'visible';
					document.frmOB.btnDel.style.visibility = 'visible';
				}
			});	
			
			
		}
	}
	
	function getSeqNo()
	{
		var param = '?action=getSeqNo&seqNo=';
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
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:750, 
		height:310, 
		zIndex: 100, 
		resizable: false, 
		parameters : $('frmOB').serialize(),
		minimizable : true,
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('transaction_popup.php?&moduleName=ChangeOB&inputTypeSeqNo='+inputTypeSeqNo);
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
	
	function saveMessage(msg)
	{
		alert(msg);
		document.frmOB.obDate.value="";
		document.frmOB.schedTimeIn.value="";
		document.frmOB.schedTimeOut.value="";
		document.frmOB.obdestination.value="0";
		document.frmOB.rdnDeduct8.value="0";
		document.frmOB.obreason.value="";
		document.frmOB.txtobTimeIn.value="";
		document.frmOB.txtobTimeOut.value="";
	
	}
	
</SCRIPT>