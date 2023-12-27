<?
##################################################
session_start(); 
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("generate_employee_sched.obj.php");
$SchedObj = new genSchedObj();
$sessionVars = $SchedObj->getSeesionVars();
$SchedObj->validateSessions('','MODULES');

switch ($_GET['code']) {
	case "generateSched":
		$SchedObj->Group=$_GET['Group'];
		$SchedObj->getPayPeriod();
		if ($SchedObj->GenerateSched()){
			echo "alert('Employee Schedule Successfully Generated!')";
		}
		else{
			echo "alert('Error Generating Employee Schedule!')";
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
</STYLE>
<!--end calendar lib-->
</HEAD>
	<BODY>
<form name="frmGenerateloans" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="50%">
    <tr>
		
      <td width="100%" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Generate 
        Employee Schedule</td>
	</tr>
	<tr>
		<td height="122" class="parentGridDtl">
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          
          <tr>
            <td height="25" class="style1 style2">Company</td>
            <td class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5"><span class="gridDtlVal2"><font class="gridDtlLblTxt" id="compCode">
              <?
										$compName = $SchedObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
            </font></span>&nbsp;&nbsp;</td>
          </tr>
          <tr> 
            <td width="18%" height="25" class="style1 style2">Group</td>
            <td width="1%" class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5">&nbsp;<? $SchedObj->DropDownMenu(
				array('','1'=>'Group 1'),'cmbGrp','1','class="inputs" style="width:100px;"'); ?>
            </td>
          </tr>
          <tr> 
            <td height="25" class="style4">Year </td>
            <td class="style1">:</td>
            <td width="81%" class="gridDtlVal style5">&nbsp;<?
            // if (date('m')==12 and date('d')>=17)
			// 	echo $Year = date('Y')+1;
			// else
			// 	echo $Year = date('Y');
			echo $Year = date('Y');
			
			?><input type="hidden" name="txtYear" value="<?=$Year?>" id="txtYear"></td>
          </tr>
		  <tr>
		    <td height="25" colspan="3" class="childGridFooter style4"><CENTER>
							<input name="btnProcess" type="button" class="inputs" id="btnProcess" onClick="GenerateSched();" value="Generate Schedule">
              </CENTER></td>
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
	function GenerateSched() {
		var Group=document.getElementById("cmbGrp").value;
		if (Group=="" || Group<0 || Group=="0") {
			alert("Group is required.");
			return false;
		} 
		new Ajax.Request(
		  'generate_employee_sched.php?code=generateSched&Group='+Group,
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