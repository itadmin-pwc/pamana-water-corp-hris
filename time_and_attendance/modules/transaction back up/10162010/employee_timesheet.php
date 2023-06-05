<?
/*
	Date Created	:	08032010
	Created By		:	Genarra Arong
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("transaction_obj.php");

$emptimesheetObj = new transactionObj();
$sessionVars = $emptimesheetObj->getSeesionVars();
$emptimesheetObj->validateSessions('','MODULES');


//Saving
switch($_GET["action"])
{
	case "Update":
		
		$error = explode("-",$emptimesheetObj->chkImpFields($_GET));
		if($error[1]!="")
			echo "alert('".$error[1]."')";
		else
		{	
			
			$checkCorrDataExists =  $emptimesheetObj->getTblData("tblTK_TimeSheetCorr", " and empNo='".$_GET["empNo"]."' and tsDate='".date("m/d/Y", strtotime($_GET["txttsDate"]))."'", '', 'sqlAssoc');
			if($checkCorrDataExists["empNo"]=="")
			{
				if($emptimesheetObj->tran_ViewEdit_Ts('Add', $_GET))
					echo "alert('Time Sheet correction successfully saved.');";
				else
					echo "alert('Error in saving the timesheet correction.');";
			}
			else
				echo "alert('Record already exists.')";
		}
		exit();
	break;
	
	case "Update_TsCorr":
		$error = explode("-",$emptimesheetObj->chkImpFields($_GET));
		if($error[1]!="")
			echo "alert('".$error[1]."')";
		else
		{	
			if($emptimesheetObj->tran_ViewEdit_Ts('Update', $_GET))
				echo "alert('Time Sheet correction successfully updated.');";
			else
				echo "alert('Error in updating the existing timesheet correction.');";
			
		}
		exit();
	break;
	
	default;
	break;
	
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
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	<BODY>
    	<FORM name="frmEmpTSheet" id="frmEmpTSheet" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <div id="empTimeSheet"></div>
            <div id="indicator1" align="center"></div>
        </FORM>
	</BODY>
</HTML>
<SCRIPT>
	pager('employee_timesheet_listAjaxResult.php','empTimeSheet','load',0,0,'','','&empNo=<?=$_GET['empNo']?>&pdSeries=<?=$_GET["pdSeries"]?>','../../../images/');  
	
	function changePayPeriod()
	{
		var pdSeries = document.frmEmpTSheet.pdSeries.value;
		pager('employee_timesheet_listAjaxResult.php','empTimeSheet','load',0,0,'','','&empNo=<?=$_GET['empNo']?>&pdSeries='+pdSeries,'../../../images/');  
	}
	
	//function maintShiftCode(act,tsDate,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	function maintShiftCode(act,tsDate,empNo,pdSeries,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var editAllw = new Window({
		id: "editAllw",
		className : 'mac_os_x',
		width:750, 
		height:410, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Employee Time Sheet", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editAllw.setURL('employee_timesheet_pop.php?&action='+act+'&tsDate='+tsDate+'&empNo='+empNo);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		        pager('employee_timesheet_listAjaxResult.php','empTimeSheet','load',0,0,'','','&empNo='+empNo+'&pdSeries='+pdSeries,'../../../images/');  
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function delShiftCode(act,shiftCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to delete the selected Shift Code?');
		if(deleShiftCode == true){
			var param = '?action=Delete&shiftCode='+shiftCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('shift_type_maintenance_listAjaxResult.php','empShiftTypeList','load',0,0,'','','','../../../images/');  
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
	
	
	function setActiveShiftCode(act,shiftCode,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var deleShiftCode = confirm('Are you sure you want to set the Shift Code into Active?');
		if(deleShiftCode == true){
			var param = '?action=setShiftActive&shiftCode='+shiftCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager('shift_type_maintenance_listAjaxResult.php','empShiftTypeList','load',0,0,'','','','../../../images/');  
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
	
	
	
</SCRIPT>