<?
##################################################
error_reporting(1);
session_start(); 
//echo base64_encode('HRD52002');
//include("../../../includes/userErrorHandler.php");
include("../../../includes/dbI.inc.php");
include("../../../includes/commonI.php");
include("../../../includes/dateClass.php");
include("ts_processing.obj.php");
$TSProcObj = new TSProcessingObj();
$sessionVars = $TSProcObj->getSeesionVars();
$TSProcObj->validateSessions('','MODULES');

switch ($_GET['code']) {
	case "processTS":
/*	for($i=0;$i<10000000;$i++) {
	
	}*/
		$TSProcObj->Group=$_GET['Group'];
		$TSProcObj->GetPayPeriod();
		$TSProcObj->setUserBranch();
		$res = $TSProcObj->ProcessTS();
		if ($res == 1){
			echo "alert('Employee Timesheet Successfully Processed!');";
		}
		elseif ($res == 2){
			echo "alert('There are timesheets with Check Tag!');";
			echo "location.href='../Transactions/view_edit_employee_timesheet.php?url=processing';";
		}
		else{
			echo "alert('Error Processing Employee Timesheet!');";
		}
		exit();
	break;
}

?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<STYLE>@import url('../../style/payroll.css');</STYLE>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../js/extjs/adapter/prototype/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">
@import url("../../../includes/calendar/calendar-blue.css");.style1 {font-size: 13px}
.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style4 {font-size: 13px; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; }
.style5 {font-size: 15px}
.style4 {font-size: 13px; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; }
</STYLE>
<!--end calendar lib-->
</HEAD>
	<BODY>
<form name="frmGenerateloans" id="frmGenerateloans" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="50%">
    <tr>
		
      <td width="100%" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Process 
        Timesheet</td>
	</tr>
	<tr>
		<td height="122" class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          
          <tr>
            <td width="26" height="25" class="style1 style2">Company</td>
            <td class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5"><span class="gridDtlVal2"><font class="gridDtlLblTxt" id="compCode">
              <?
										$compName = $TSProcObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
            </font></span>&nbsp;&nbsp;</td>
          </tr>
          <tr> 
            <td height="25" class="style1 style2">Pay Group</td>
            <td width="2%" class="style1">:</td>
            <td colspan="4" width="72%">&nbsp;<? $TSProcObj->DropDownMenu(
				array('','1'=>'Group 1'),'cmbGroup','1','class="inputs" style="width:100px;"'); ?>            </td>
          </tr>
          <tr> 
            <td height="25" class="style1 style2"></td>
            <td width="2%" class="style1"></td>
            <td colspan="4" width="72%" class="gridDtlLbl">&nbsp;<input type="checkbox" id="chkAll" name="chkAll" value="1" onClick="CheckAll();"/>&nbsp;&nbsp;CHECK ALL</td>
          </tr>
          <tr> 
            <td height="25" class="style1 style2">Branch</td>
            <td width="2%" class="style1">:</td>
            <td colspan="4" width="72%" class="gridDtlVal style5"><div style="overflow-y:scroll; height:250px">
            	<table width="100%" cellpadding="1" cellspacing="2">
                	<?
                    $brnQry = "Select brnCode, brnDesc from tblBranch where brnCode in(Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' and compCode='{$_SESSION['company_code']}') order by brnDesc";
					$brRes = $TSProcObj->getArrResI($TSProcObj->execQryI($brnQry));
					$i=0;
					$q=0;
					foreach($brRes as $valBrn){
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							$f_color = ($arrCSAppList_val["csStat"]=='A'?"#CC3300":"");
				?>
            		<tr style="height:20px;"  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td width="5%"><input type="checkbox" id="chkBrnCode<?=$q?>" name="chkBrnCode<?=$q?>" value="<?=$valBrn['brnCode']?>" onClick="check(this.name);"/></td>
                        <td width="95%" class="gridDtlVal"><?=$valBrn['brnDesc'];?></td>
                    </tr>
                    <?
					$q++;
					}
					?>
                </table></div>
            </td>
          </tr>          
		  <tr>
		    <td height="25" colspan="7" class="childGridFooter">
							<div align="center">
							  <input name="btnProcess" type="button" class="inputs" id="btnProcess" onClick="ProcessTS();" value="Process Timesheet"><input type="hidden" value="<?=$q;?>" name="chCtr" id="chCtr"><input type="hidden" name="checker" id="checker" value="0">
			               </div></td>
		    </tr>
        </table>
<div id="caption" align="center">        </div>				
	</td>
	</tr> 
</table>
</form>
</BODY>
</HTML>
<script type="text/javascript">
// JavaScript Document
	function CheckAll(){
		var cnt = $('chCtr').value;
		for(i=0;i<=cnt;i++){
			if ($('chkAll').checked==false) {
				$('chkBrnCode'+i).checked=false;
				$('checker').value = 0;
			} else {
				$('chkBrnCode'+i).checked=true;
				$('checker').value = 1;
			}
		}
	}

	function check(name) {
		var cnt = $('chCtr').value;
		$('checker').value = 0;
		for(i=0;i<=cnt;i++){
			if ($('chkBrnCode'+i).checked==true) {
				$('checker').value = 1;
			} 
		}
	}


	function ProcessTS() {
		var Group = document.getElementById("cmbGroup").value;
		var chk = document.getElementById("checker").value;
		if(chk==0){
			alert('Branch is required! Please select branch.');
			return false;
		}
		if (Group=="" || Group<0 || Group=="0") {
			alert("Pay Group is required.");
			return false;
		} 
		new Ajax.Request(
		  'ts_processing.php?code=processTS&Group='+Group,
		  {
			 method : 'get', 
			 asynchronous : true,  
			 parameters : $('frmGenerateloans').serialize(),   
			 onComplete   : function (req){
				eval(req.responseText);
			 },
			onCreate : function(){
				timedCount();
				$('btnProcess').disabled=true;
			},
			onSuccess: function (){
				$('btnProcess').disabled=false;
				$('caption').innerHTML="";
				stopCount();
			}				 
		  }
		);
	}
	
	var m=0;
	var s=0;
	var t;	

	function timedCount(){

		if(s == 60){
			m = m+1;
		}	
		if(s == 60){
			s =0;
		}

		$('caption').innerHTML="<font size='2'>"+m+":"+s+ " <blink>Loading...</blink></font> " +'<br><img src="../../../images/progress2.gif">';
		s=s+1;
		t=setTimeout("timedCount()",1000);
	}
	
	function stopCount(){
		clearTimeout(t);
	}	
</script>