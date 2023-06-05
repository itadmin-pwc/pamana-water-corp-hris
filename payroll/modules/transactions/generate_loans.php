<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("generate_loans.obj.php");
$loansObj = new genloansObj();
$sessionVars = $loansObj->getSeesionVars();
$loansObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$groupType = $_SESSION["pay_group"];
$catType = $_SESSION["pay_category"];
$payPd = $_GET["payPd"];
if ($payPd=="") {
	$openPeriod = $loansObj->getOpenPeriod($compCode,$_SESSION['pay_group'],$_SESSION['pay_category']); 
	$groupType = $openPeriod['payGrp'];
	$catType = $openPeriod['payCatDesc'];
	$paySearies = $openPeriod['pdSeries'];
	$payPayable = $openPeriod['pdPayable'];
	$loansTag= $openPeriod['pdLoansTag'];
	$tsTag= $openPeriod['pdTsTag'];
	$payCovered=$loansObj->dateFormat($openPeriod['pdFrmDate']) . " - " . $loansObj->dateFormat($openPeriod['pdToDate']);
}
##################################################
switch ($_GET['code']) {
	case "generateLoans":
		$slctdPd = $loansObj->getSlctdPd($compCode,$payPd);
		$modSked = $slctdPd['modSked'];
		if ($modSked==0) $skedNo=2; ### 2nd period
		if ($modSked==1) $skedNo=1; ### 1st period
		
		if ($slctdPd['pdProcessTag']=="N" || $slctdPd['pdProcessTag']=="" || $slctdPd['pdProcessTag']==" ") {
			if ($slctdPd['pdLoansTag']=="N" || $slctdPd['pdLoansTag']=="" || $slctdPd['pdLoansTag']==" ") {
				$loanprocessresult=$loansObj->processLoans($compCode,$skedNo,$slctdPd,0);	
				echo "document.getElementById('btnProcess').value='Re-PROCESS LOANS / RECURRING';";
				echo "document.getElementById('loansTag').value='Y';";
				if ($loanprocessresult) {
					echo "alert('Generate Loan/Recurring Deductions for Payroll Successfully created!')";
				}	
				else {
					echo "alert('Generate Loan/Recurring Deductions for Payroll Failed!')";				
				}	
			} else {
				$loanprocessresult=$loansObj->processLoans($compCode,$skedNo,$slctdPd,1);	
				if ($loanprocessresult) {
					echo "alert('Re-process Loan/Recurring Deductions for Payroll Successfully created!');";
				}	
				else {
					echo "alert('Re-process Loan/Recurring Deductions for Payroll Failed!');";				
				}				
			}
		} else {
			echo "alert('Payroll Period Processed already!');";
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
  <table cellpadding="0" cellspacing="1" class="parentGrid">
    <tr>
		
      <td width="365" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Generate 
        Loan/Recurring Deductions for Payroll</td>
	</tr>
	<tr>
		<td height="180" class="parentGridDtl" >
			  <TABLE border="0" width="106%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="6"><input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>">
              <input name='new_' id='new_' type='hidden' value='new_'  onClick='option_button_click(this.id);'>
             <input name='option_menu' id='refresh_' type='hidden' value='refresh_'  onClick='option_button_click(this.id);'>
			   <input name='updateFlag' type='hidden' id='updateFlag'> 
              <input name='fileName' type='hidden' id='fileName' value="timesheet.php">            <input type="hidden" value="<?=trim($loansTag)?>" name="loansTag" id="loansTag">
              <input type="hidden" value="<?=trim($tsTag)?>" name="tsTag" id="tsTag"></td>
          </tr>
          <tr>
            <td height="25" class="style1 style2">Company</td>
            <td class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5"><span class="gridDtlVal2"><font class="gridDtlLblTxt" id="compCode">
              <?
										$compName = $loansObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
            </font></span></td>
          </tr>
          <tr> 
            <td width="18%" height="25" class="style1 style2">Group </td>
            <td width="1%" class="style1">:</td>
            <td colspan="4" class="gridDtlVal style5"> 
              <?="Group $groupType";?></td>
          </tr>
          <tr> 
            <td height="25" class="style4">Category </td>
            <td class="style1">:</td>
            <td width="81%" class="gridDtlVal style5"> 
              <?=$catType;?></td>
          </tr>
          <tr> 
            <td height="25" class="style4">Payroll Period </td>
            <td class="style1">:</td>
            <td class="gridDtlVal"> <div class="style5" id="pdPay"> 
                <input name="payPd" type="hidden" id="payPd" value="<? echo $paySearies; ?>">
                <?=$payPayable;?></div></td>
          </tr>
		  <tr> 
            <td height="25" class="style4">Date Covered</td>
            <td class="style1">:</td>
            <td class="gridDtlVal"> <div class="style5" id="pdPay"> 
                <?=$payCovered;?></div></td>
          </tr>
		  <tr>
		    <td height="25" colspan="3" class="childGridFooter style4"><CENTER>
							<?
								if ($loansTag=="N" || $loansTag=="" || $loansTag==" ") {
									$procVal = "PROCESS LOANS / RECURRING";
								} else {
									$procVal = "Re-PROCESS LOANS / RECURRING";
								}
							?>
							<input name="btnProcess" type="button" class="inputs" id="btnProcess" onClick="processGenerateLoans();" value="<? echo $procVal; ?>">
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
	function processGenerateLoans() {
		var payPd=document.getElementById("payPd").value;
		var loansTag=document.getElementById("loansTag").value;
		var tsTag=document.getElementById("tsTag").value;
		var check=0;
		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		} else {
			if (tsTag == "Y") {
				if (loansTag=="") {
					var	ans = confirm('Are you sure you want to process loans?');
					if (ans==true) {
						check=1;	
					}
				}
				else {
					var	ans = confirm('Are you sure you want to re-process loans?');
					if (ans==true) {
						check=1;	
					}
				}
			} else {
				alert('Please process first the timesheet.');
			}	
			if (check==1) {
				new Ajax.Request(
				  'generate_loans.php?code=generateLoans&payPd='+payPd,
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
		}
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