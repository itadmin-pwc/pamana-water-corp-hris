<?
##################################################
session_start(); 

//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/dateClass.php");
include("adjustment_processing_obj.php");
$tsAdjustmentObj = new adjustmentObj($_GET,$_SESSION);
$sessionVars = $tsAdjustmentObj->getSeesionVars();
$tsAdjustmentObj->validateSessions('','MODULES');

switch ($_GET['code']) {
	case "processTSAdjustment":
		$tsAdjustmentObj->payGrp=$_GET['Group'];
		$qryCheck = $tsAdjustmentObj->recordChecker("Select empNo from tblTK_TimesheetAdjustment 
													where compCode='{$_SESSION['company_code']}' 
														and payGrp='{$tsAdjustmentObj->payGrp}'
														and tsStat='A'");
//		$qryCheck = $tsAdjustmentObj->recordChecker("Select tkAdjust.empNo 
//													 From tblTK_TimesheetAdjustment tkAdjust
//													 Inner Join tblEmpMast emp on emp.empNo=tkAdjust.empNo
//													 Where tkAdjust.compCode='{$_SESSION['company_code']}' 
//														and tkAdjust.payGrp='{$tsAdjustmentObj->payGrp}'
//														and tkAdjust.tsStat='A'
//														and emp.empbrnCode IN (Select brnCode from tblTK_UserBranch 
//															where empNo='{$_SESSION['employee_number']}' 
//																and compCode='{$_SESSION['company_code']}')");
		if($qryCheck){
			if($tsAdjustmentObj->processAdjustmentSetup()){
				if ($tsAdjustmentObj->ProcessTSAdjustment()){
					echo "alert('Timesheet Adjustment Successfully Processed!');";
					echo "location.href='adjustment_processing.php';";
				}
				else{
					echo "alert('Error Processing Timesheet Adjustment!');";
				}
			}
			else{
				echo "alert('Error Processing Timesheet Adjustment!');";
			}
		}
		else{
			echo "alert('No record found for the selected group or no approved adjustment!');";	
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
  <table cellpadding="0" cellspacing="1" class="parentGrid"  width="50%">
    <tr>
		
      <td width="100%" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Timesheet Adjustment</td>
	</tr>
	<tr>
		<td height="122" class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          
          <tr>
            <td width="131" height="25" class="style1 style2">Company</td>
            <td class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5"><span class="gridDtlVal2"><font class="gridDtlLblTxt" id="compCode">
              <?
										$compName = $tsAdjustmentObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
            </font></span></td>
          </tr>
          <tr> 
            <td height="25" class="style1 style2">Pay Group</td>
            <td width="5" class="style1">:</td>
            <td colspan="4" width="343" class="gridDtlVal style5">&nbsp;<? $tsAdjustmentObj->DropDownMenu(
				array('','1'=>'Group 1'),'cmbGroup','1','class="inputs" style="width:100px;"'); ?>            </td>
          </tr>
          
		  <tr>
		    <td height="25" colspan="7" class="childGridFooter">
							<div align="center">
							  <input name="btnProcess" type="button" class="inputs" id="btnProcess" onClick="ProcessTSAdjustment();" value="Process Timesheet Adjustment">
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
	function ProcessTSAdjustment() {
		var Group=document.getElementById("cmbGroup").value;
		if (Group=="" || Group<0 || Group=="0") {
			alert("Pay Group is required.");
			return false;
		} 
		new Ajax.Request(
		  'adjustment_processing.php?code=processTSAdjustment&Group='+Group,
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