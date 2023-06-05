<?
##################################################
session_start(); 

//include("../../../includes/userErrorHandler.php");
include("../../../includes/dbI.inc.php");
include("../../../includes/commonI.php");
include("../../../includes/dateClass.php");
include("ts_posting.obj.php");
$TSPostObj = new TSPostingObj();
$sessionVars = $TSPostObj->getSeesionVars();
$TSPostObj->validateSessions('','MODULES');

switch ($_GET['code']) {
	case "processTS":
		$TSPostObj->Group=$_GET['Group'];
		$TSPostObj->GetPayPeriod();
		if ($TSPostObj->PostTS())
			echo "alert('Employee Timesheet Successfully Posted!')";
		else
			echo "alert('Error Posting Employee Timesheet!')";
		exit();
	break;
	case "checkErrors":
		$TSPostObj->Group=$_GET['Group'];
		$TSPostObj->GetPayPeriod();
		$arrError = $TSPostObj->checkErrorTag();
		echo "$('errors').value = {$arrError['ctr']}";
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
<STYLE TYPE="text/css" MEDIA="screen">
@import url("../../../includes/calendar/calendar-blue.css");.style1 {font-size: 13px}
.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style4 {font-size: 13px; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; }
.style5 {font-size: 15px}
</STYLE>
</HEAD>
	<BODY>
<form name="frmGenerateloans" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="50%">
    <tr>
		
      <td width="100%" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Post 
        to Payroll</td>
	</tr>
	<tr>
		<td height="122" class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          
          <tr>
            <td width="26%" height="25" class="style1 style2">Company</td>
            <td class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5"><span class="gridDtlVal2"><font class="gridDtlLblTxt" id="compCode">
              <?
										$compName = $TSPostObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
            </font></span></td>
          </tr>
          <tr> 
            <td height="25" class="style1 style2">Pay Group</td>
            <td width="2%" class="style1">:</td>
            <td colspan="4" width="72%" class="gridDtlVal style5">&nbsp;<? $TSPostObj->DropDownMenu(
				array('','1'=>'Group 1'),'cmbGroup','1','class="inputs" style="width:100px;" onChange="checkerror();"'); ?>            </td>
          </tr>
          
		  <tr>
		    <td height="25" colspan="7" class="childGridFooter">
							<div align="center">
							  <input name="btnProcess" type="button" class="inputs" id="btnProcess" onClick="ProcessTS();" value="Post Timesheet">
		                      <input type="hidden" name="errors" id="errors">
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
	function checkerror() {
		var Group=document.getElementById("cmbGroup").value;
		new Ajax.Request(
		  'ts_posting.php?code=checkErrors&Group='+Group,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 },
			onCreate : function(){
				$('btnProcess').disabled=true;
			},
			onSuccess: function (){
				$('btnProcess').disabled=false;
			}				 
		  }
		);
	}
	
	function ProcessTS() {
		var Group=document.getElementById("cmbGroup").value;
		var errors=document.getElementById("errors").value;
		if (Group=="" || Group<0 || Group=="0") {
			alert("Pay Group is required.");
			return false;
		} 
/*		if (errors > 0) {
			alert('There are timesheets with check tag. Please check the error(s) and reprocess the timesheet.');
			return false;
		}*/
		new Ajax.Request(
		  'ts_posting.php?code=processTS&Group='+Group,
		  {
			 asynchronous : true,     
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