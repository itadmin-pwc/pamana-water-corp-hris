<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("transaction_obj.php");


$updateEmpShiftObj = new transactionObj($_GET,$_SESSION);
$updateEmpShiftObj->validateSessions('','MODULES');


switch($_GET["action"])
{
	
	case "saveUpdateEmpSched":
	break;
	
	case "enableUpdateSched":
		
		echo "document.frmUpdateEmpShift.shiftcode.value='0'; ";
		echo "document.frmUpdateEmpShift.shiftcode.disabled=true; ";
		echo "document.frmUpdateEmpShift.updateSchedBy.disabled=false; ";
		echo "document.frmUpdateEmpShift.updateSchedBy.value='1'; ";
		
		if($_GET["chkDayEnabled".$_GET["enableField"]]!="")
		{
				echo "document.frmUpdateEmpShift.txtEtimeIn".$_GET["enableField"].".readOnly=false; ";
				echo "document.frmUpdateEmpShift.txtElunchOut".$_GET["enableField"].".readOnly=false; ";
				echo "document.frmUpdateEmpShift.txtElunchIn".$_GET["enableField"].".readOnly=false; ";
				echo "document.frmUpdateEmpShift.txtEbrkOut".$_GET["enableField"].".readOnly=false; ";
				echo "document.frmUpdateEmpShift.txtEbrkIn".$_GET["enableField"].".readOnly=false; ";
				echo "document.frmUpdateEmpShift.txtEtimeOut".$_GET["enableField"].".readOnly=false; ";
				echo "document.frmUpdateEmpShift.chkCrossDay".$_GET["enableField"].".disabled=false; ";
			
		}
		else
		{
			echo "document.frmUpdateEmpShift.txtEtimeIn".$_GET["enableField"].".readOnly=true; ";
			echo "document.frmUpdateEmpShift.txtElunchOut".$_GET["enableField"].".readOnly=true; ";
			echo "document.frmUpdateEmpShift.txtElunchIn".$_GET["enableField"].".readOnly=true; ";
			echo "document.frmUpdateEmpShift.txtEbrkOut".$_GET["enableField"].".readOnly=true; ";
			echo "document.frmUpdateEmpShift.txtEbrkIn".$_GET["enableField"].".readOnly=true; ";
			echo "document.frmUpdateEmpShift.txtEtimeOut".$_GET["enableField"].".readOnly=true; ";
			echo "document.frmUpdateEmpShift.chkCrossDay".$_GET["enableField"].".disabled=true; ";
		}
		exit();
		
		
		
	break;
	
	case "disabledFields":
		//echo $_GET["updateSchedBy"]."GEN";
		$a = explode('&', $_SERVER['QUERY_STRING']);
		
		if($_GET["updateSchedBy"]==1)
		{
			echo "document.frmUpdateEmpShift.shiftcode.disabled=true; ";
			$i = 0;
			while ($i < count($a)) {
				$b = split('=', $a[$i]);
				if(substr(htmlspecialchars(urldecode($b[0])),0,13)=='chkDayEnabled')
				{
					echo "document.frmUpdateEmpShift.txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=false; ";
					echo "document.frmUpdateEmpShift.txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=false; ";
					echo "document.frmUpdateEmpShift.txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=false; ";
					echo "document.frmUpdateEmpShift.txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=false; ";
					echo "document.frmUpdateEmpShift.txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=false; ";
					echo "document.frmUpdateEmpShift.txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=false; ";
					echo "document.frmUpdateEmpShift.chkCrossDay".substr(htmlspecialchars(urldecode($b[0])),13).".disabled=false; ";
				}
					
				
				$i++;
			}
		}
		else
		{
			echo "document.frmUpdateEmpShift.shiftcode.disabled=false; ";
			$i = 0;
			while ($i < count($a)) {
				$b = split('=', $a[$i]);
				if(substr(htmlspecialchars(urldecode($b[0])),0,13)=='chkDayEnabled')
				{
					echo "document.frmUpdateEmpShift.txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=true; ";
					echo "document.frmUpdateEmpShift.txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=true; ";
					echo "document.frmUpdateEmpShift.txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=true; ";
					echo "document.frmUpdateEmpShift.txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=true; ";
					echo "document.frmUpdateEmpShift.txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=true; ";
					echo "document.frmUpdateEmpShift.txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13).".readOnly=true; ";
					echo "document.frmUpdateEmpShift.chkCrossDay".substr(htmlspecialchars(urldecode($b[0])),13).".disabled=true; ";
				}
					
				
				$i++;
			}
		}
		
			
			
		exit();
	break;
	
	case "getShiftCodeDtl":
		
		$a = explode('&', $_SERVER['QUERY_STRING']);
		$i = 0;
		while ($i < count($a)) {
			$b = split('=', $a[$i]);
			if(substr(htmlspecialchars(urldecode($b[0])),0,13)=='chkDayEnabled')
			{
				$shft_dayType = (substr(htmlspecialchars(urldecode($b[0])),-1)==0?7:substr(htmlspecialchars(urldecode($b[0])),-1));
				$arr_ShiftCode_Dtl = $updateEmpShiftObj->getTblData("tblTK_ShiftDtl", " and shftCode='".$_GET["shiftcode"]."' and dayCode='".$shft_dayType."'", " order by dayCode", "sqlAssoc");
				
				//echo htmlspecialchars(urldecode($b[0]))."=".substr(htmlspecialchars(urldecode($b[0])),13)."\n";
				echo "document.frmUpdateEmpShift.txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13).".value='{$arr_ShiftCode_Dtl['shftTimeIn']}';";
				echo "document.frmUpdateEmpShift.txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13).".value='{$arr_ShiftCode_Dtl['shftLunchOut']}';";
				echo "document.frmUpdateEmpShift.txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13).".value='{$arr_ShiftCode_Dtl['shftLunchIn']}';";
				echo "document.frmUpdateEmpShift.txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13).".value='{$arr_ShiftCode_Dtl['shftBreakOut']}';";
				echo "document.frmUpdateEmpShift.txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13).".value='{$arr_ShiftCode_Dtl['shftBreakIn']}';";
				echo "document.frmUpdateEmpShift.txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13).".value='{$arr_ShiftCode_Dtl['shftTimeOut']}';";
				echo "document.frmUpdateEmpShift.restDayTag".substr(htmlspecialchars(urldecode($b[0])),13).".value='{$arr_ShiftCode_Dtl['RestDayTag']}';";
				if($arr_ShiftCode_Dtl['crossDay']=='Y')
					echo "document.frmUpdateEmpShift.chkCrossDay".substr(htmlspecialchars(urldecode($b[0])),13).".checked='checked';";
				else
					echo "document.frmUpdateEmpShift.chkCrossDay".substr(htmlspecialchars(urldecode($b[0])),13).".checked='';";
			}
			$i++;
		}		
		
		exit();
	break;
	
	case "saveUpdateSched":
		
		//Save Previous Timesheet Record to tblTk_ScheduleHist
		$saveUpdateSched = $updateEmpShiftObj->tran_ChngeEmpShft($_SERVER['QUERY_STRING'],"Update", $_GET);
		echo "alert('".$saveUpdateSched."')";
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
		<FORM name='frmUpdateEmpShift' id="frmUpdateEmpShift" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        	
			<div id="updateEmpShiftCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	
	pager('update_employee_shiftAjaxResult.php','updateEmpShiftCont','load',0,0,'','','&empNo='<?=$_GET["empNo"]?>,'../../../images/');  

	
	function getEmployee(evt,eleVal){
		
		
		var k = evt.keyCode | evt.which;
		
		switch(k){
			case 8:
				clearFld();
			break;
			case 13:
				pager('update_employee_shiftAjaxResult.php','updateEmpShiftCont','load',0,0,'','','&action=getEmpInfo&empNo='+eleVal,'../../../images/');  
			break;
		}
	}
	
	function clearFld(){
		$('txtEmpName').value='';
	}	
	
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
			$('refNo').readOnly=false;
			$('refNo').focus();
		}
		
		if(mode == 'REFRESH'){
			Windows.getWindow('refWin').close();
		}
	}
	
	function enabledFields(field)
	{	
		params = 'update_employee_shift.php?action=enableUpdateSched&enableField='+field;
				
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmUpdateEmpShift').serialize(),
				onComplete : function (req){
					eval(req.responseText);
					
				}	
			});
	}
	
	function getUpdateSched()
	{
		params = 'update_employee_shift.php?action=disabledFields';
				
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmUpdateEmpShift').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}
	
	function getShiftCodeDetail()
	{
		
		params = 'update_employee_shift.php?action=getShiftCodeDtl';
				
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmUpdateEmpShift').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}
	
	
	

	function saveUpdateEmpShiftDetail()
	{
		var chngeShiftFields = $('frmUpdateEmpShift').serialize(true);
		
		var arrayFields = new Array('txtEtimeIn','txtElunchOut','txtElunchIn','txtEbrkOut','txtEbrkIn','txtEtimeOut');
		var arrayalerts = new Array('Time In','Lunch Out','Lunch In','Break Out','Break In','Time Out');
		
		for(fields=1; fields<=7; fields++)
		{
			for(dayCnt=1; dayCnt<=7; dayCnt++)
			{
					
				if((chngeShiftFields[arrayFields[fields]+chngeShiftFields["rdnSelected"]+dayCnt]=="")||(chngeShiftFields[arrayFields[fields]+chngeShiftFields["rdnSelected"]+dayCnt]==":"))
				{
					alert(arrayalerts[fields]+" is Required.");
					return false;
				}
				
			}
		}	
		
		
		
		var changeUpdateSchedConfirm = confirm('Are you sure you want to update the existing Shift Schedule in the existing Timesheet of the Employee?');
		if(changeUpdateSchedConfirm == true){
			var changeUpdateTsApp = confirm('Do you want to clear the generated TS Application Types in the Timesheet?');
				
				if(changeUpdateTsApp == true){
					params = 'update_employee_shift.php?action=saveUpdateSched&delAppTypeCd=Yes';
				}
				else{
					params = 'update_employee_shift.php?action=saveUpdateSched&delAppTypeCd=No';
				}
				
				new Ajax.Request(params,
				{
					method : 'get',
					parameters : $('frmUpdateEmpShift').serialize(),
					onComplete : function (req){
						eval(req.responseText);
						pager('update_employee_shiftAjaxResult.php','updateEmpShiftCont','load',0,0,'','','','../../../images/');  
					}	
				});
				
		}
	}
	
	function maintChoice(act,shiftCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:900, 
		height:340, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: " ", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('update_employee_shift_pop.php?&modAction='+act+'&shiftCode='+shiftCode);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		       // pager('shift_type_maintenance_listAjaxResult.php','empShiftTypeList','load',0,0,'','','','../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	function copySched(field) {
		var chngeShiftFields = $('frmUpdateEmpShift').serialize(true);
		$('hd_txtIn').value=chngeShiftFields['txtEtimeIn'+field];
		$('hd_txtLout').value=chngeShiftFields['txtElunchOut'+field];
		$('hd_txtLin').value=chngeShiftFields['txtElunchIn'+field];
		$('hd_txtBout').value=chngeShiftFields['txtEbrkOut'+field];
		$('hd_txtBin').value=chngeShiftFields['txtEbrkIn'+field];
		$('hd_txtOut').value=chngeShiftFields['txtEtimeOut'+field];
	}
	function pasteSched(field) {
		var  chngeShiftFields = $('frmUpdateEmpShift').serialize(true);
		$('txtEtimeIn'+field).value=chngeShiftFields['hd_txtIn'];
		$('txtElunchOut'+field).value=chngeShiftFields['hd_txtLout'];
		$('txtElunchIn'+field).value=chngeShiftFields['hd_txtLin'];
		$('txtEbrkOut'+field).value=chngeShiftFields['hd_txtBout'];
		$('txtEbrkIn'+field).value=chngeShiftFields['hd_txtBin'];
		$('txtEtimeOut'+field).value=chngeShiftFields['hd_txtOut'];
	}	
</SCRIPT>