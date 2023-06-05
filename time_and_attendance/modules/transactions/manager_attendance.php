<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("transaction_obj.php");
$transactionObj = new transactionObj();
if (isset($_GET['action'])) {
	switch ($_GET['action']) {	
		case ('getEmpInfo'):
//			$sqlGrp = "Select payGrp from tblProcGrp where compCode='{$_SESSION['company_code']}' and status='A'";
//			$res = $transactionObj->getSqlAssoc($transactionObj->execQry($sqlGrp));		
//			$paygroup = $res['payGrp'];	
//			$qryPayperiod = $transactionObj->execQry("Select pdFrmDate, DATEADD(Day, 3,pdToDate) as  pdToDate
//											  from tblPayPeriod 
//											  where compCode='{$_SESSION['company_code']}' and payGrp='{$paygroup}' 
//												and pdYear='".date("Y")."' and pdStat='O'");
//			$payperiod = $transactionObj->getSqlAssoc($qryPayperiod);
			
		//184000006
//		old code		
//			$qryEmpList = "SELECT  *
//							FROM tblEmpMast
//							WHERE compCode= '{$_SESSION['company_code']}'
//							AND empNo='{$_GET['empNo']}'
//							AND empPayGrp='{$paygroup}'
//							AND empBrnCode IN (SELECT brnCode FROM tblTK_UserBranch 
//									WHERE compCode ='{$_SESSION['company_code']}'and empNo = '{$_SESSION['employee_number']}')
//							AND ((empStat='RG') 
//							OR (((dateResigned between '".date("m/d/Y",strtotime($payperiod['pdFrmDate']))."' 
//								AND '".date("m/d/Y",strtotime($payperiod['pdToDate']))."') 
//							OR (endDate between '".date("m/d/Y",strtotime($payperiod['pdFrmDate']))."' 
//								AND '".date("m/d/Y",strtotime($payperiod['pdToDate']))."'))))"; 
			$qryEmpList = "SELECT  *
							FROM tblEmpMast
							WHERE compCode= '{$_SESSION['company_code']}'
							AND empNo='{$_GET['empNo']}'
							AND empBrnCode IN (SELECT brnCode FROM tblTK_UserBranch 
									WHERE compCode ='{$_SESSION['company_code']}'and empNo = '{$_SESSION['employee_number']}')
							AND (empStat='RG')"; 
									
			$resEmpList = $transactionObj->execQry($qryEmpList);
			$empInfo = $transactionObj->getSqlAssoc($resEmpList);						
			
			//$empInfo = $timesheetsadjustmentsObj->getUserInfo($_SESSION['company_code'],$_GET['empNo'],'');
				if($empInfo['empNo']!=''){
					$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
					if($empInfo['empPayGrp']==1){
						$pgrp = "Group 1";	
					}
					if($empInfo['empPayGrp']==2){
						$pgrp = "Group 2";	
					}
					$position = $transactionObj->getpositionwil(" Where posCode='".$empInfo['empPosId']."'","2");
					$pos = $position['posDesc'];
					$rank = $transactionObj->getRank($empInfo['empRank']);
					$empRank = $rank['rankDesc'];
					$level = $transactionObj->getSqlAssoc($transactionObj->execQry("Select * from tblEmpLevel where empLevel='".$empInfo['empLevel']."'"));
					$emplevel = $level['empLevelDesc'];
					$branch = $empInfo['empBrnCode']; 
					$pcat = $transactionObj->getPayCat($_SESSION['company_code']," and payCat='".$empInfo['empPayCat']."'");
					$paycat = $pcat['payCatDesc'];
					echo "$('txtName').value='$empInfo[empLastName], ".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName ';";
					echo "$('txtPayGrp').value='$pgrp';";
					echo "$('txtPayCat').value='$paycat';";
					echo "$('txtPosition').value='$pos';";
					echo "$('txtRank').value='$empRank';";
					echo "$('txtLevel').value='$emplevel';";	
					echo "$('btnSave').disabled=false;";	
					echo "$('hdnBranch').value='$branch';";		
				}
				else{
					echo "alert('No Record Found!');";	
					echo "$('txtAddEmpNo').value='';";
					echo "$('txtName').value='';";
					echo "$('txtPayGrp').value='';";
					echo "$('txtPayCat').value='';";
					echo "$('txtPosition').value='';";
					echo "$('txtRank').value='';";
					echo "$('txtLevel').value='';";
				}
			exit();
		break;
		
		case ('processManagersAttendance'):
			$recCheck = $transactionObj->recordChecker("Select * from tblTK_ManagersAttendance where compcode='".$_SESSION['company_code']."' and empNo='".$_GET['txtAddEmpNo'] ."'");
			if(!$recCheck){
				if($transactionObj->saveManagersAttendance($_GET)){
					echo "alert('Employee has been added!');";	
					echo "location.href='manager_attendance.php';";
				}
				else{
					echo "alert('Failed to save the Employee!');";	
				}
			}
			else{
				echo "alert('Employee already exist!');";	
			}
			exit();
		break;
		
		case ('Delete'):
			$chkSeqNo = $_GET["chkseq"];
			foreach($chkSeqNo as $indchkSeqNo => $chkSeqNo_val)
			{
				$qryDel = "DELETE FROM tblTK_ManagersAttendance where seqNo='".$chkSeqNo_val."'";
				$resDel = $transactionObj->execQry($qryDel);
			}

			echo "alert('Selected employee already deleted.');";
			echo "location.href='manager_attendance.php';";
		exit();
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
	<FORM name='frmManagersAttendance' id="frmManagersAttendance" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="managersAttendanceCont"></div>
			<div id="indicator1" align="center"></div>
	</FORM>
</BODY>

	
</HTML>

<script type="text/javascript">
	disableRightClick()

	function removeSpaces(val) {
	   return val.split(' ').join('');
	}
	
	
	function getEmployee(evt,eleVal){
		var evnt = evt.keyCode;
		var param ='&empNo='+eleVal;
		
		if(evnt==13) {
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=getEmpInfo'+param,{
					method : 'get',
					parameters : $('frmManagersAttendance').serialize(),
					onComplete : function (req){
						eval(req.responseText);	
					},
					onCreate : function (){
						$('indicator1').src="../../../images/wait.gif";
					},
					onSuccess : function (){
						$('indicator1').innerHTML="";
					}
				});	
		}
	}
	
	function saveEmp(){
		var param = "action=processManagersAttendance";	
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?'+param,{
			method : 'GET',
			parameters : $('frmManagersAttendance').serialize(),
			onComplete : function(req){
				eval(req.responseText);
			}	
		})			
	}
	
	
pager('manager_attendance_AjaxResult.php','managersAttendanceCont','load',0,0,'','','','../../../images/');  


	function getCheckedBoxes(chkboxName) {
	  var checkboxes = document.getElementsByName(chkboxName);
	  var checkboxesChecked = [];
	  for (var i=0; i<checkboxes.length; i++) {
		 if (checkboxes[i].checked) {
			checkboxesChecked.push(checkboxes[i]);
		 }
	  }
	  return checkboxesChecked.length > 0 ? 1 : 0;
	}
	
	
	
	function delTimesheetAdj(act,seqNo,URL,ele,offset,maxRec,isSearch,txtSrch,cmbSrch)
	{
		var checkedBoxes = getCheckedBoxes("chkseq[]");
		if(checkedBoxes==0){
			alert('Please select employee to be deleted!');	
		}
		else{		
			var deleTimesheet = confirm('Are you sure you want to delete the selected employee?');
			
			if(deleTimesheet == true){
				var param = '?action=Delete&seqNo='+seqNo;
				
				new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
					method : 'get',
					parameters : $('frmManagersAttendance').serialize(),
					onComplete : function (req){
						eval(req.responseText);	
						pager('manager_attendance_AjaxResult.php','managersAttendanceCont','load',0,0,'','','','../../../images/');  
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
	}
	
	
	
	
	function checkAll(field)
	{
		var chkob = document.frmManagersAttendance.elements['chkseq[]'];
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