<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
if ($_GET['act'] == "EditTaxExemption") {
	$teuinfo = $maintEmpObj->getTEUInfo($_GET['teuCode']);
}

switch($_GET['code']) {
	case "AddTaxExemption":
		if ($maintEmpObj->CheckTeu($_GET['txtteuCode'])!=0) {
			echo "alert('TEU Code Already Exist.');";
			exit();
		}
		if ($maintEmpObj->TEU("Add",$_GET))
			echo "alert('Tax Exemption Successfully Added.');";
		else
			echo "alert('Error Adding Tsx Exemption.');";
	
		exit();
	break;
	case "EditTaxExemption":
		if ($maintEmpObj->CheckTeu($_GET['txtteuCode'])!=0) {
			echo "alert('TEU Code Already Exist.');";
			exit();
		}
		if ($maintEmpObj->TEU("Edit",$_GET))
			echo "alert('Tax Exemption Successfully Updated.');";
		else
			echo "alert('Error Updating Tax Exemption.');";

		exit();
	break;
}

?>

<HTML>
<head>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>	
<STYLE>@import url('../../style/payroll.css');</STYLE>
<STYLE TYPE="text/css" MEDIA="screen">
@import url("../../../includes/calendar/calendar-blue.css");.style3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
}
</STYLE>
<style type="text/css">
<!--
	.headertxt {font-family: verdana; font-size: 11px;}
.style2 {font-family: verdana}
.style3 {font-size: 11px}
-->
</style>

</head>
	<BODY>
	<form action="" method="post" name="frmbank" id="frmbank">
      <table width="430" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td class="gridDtlLbl style2 style3" >TEU Code</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input value="<?=$teuinfo['teuCode']?>" name="txtteuCode" type="text" class="inputs" id="txtteuCode">
          <input type="hidden" value="<?=$_GET['teuCode']?>" name="tCode" id="tCode">
          <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt"> Description</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input value="<?=$teuinfo['teuDesc']?>" type="text" name="txtdesc" id="txtdesc" class="inputs" size="40">
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Amount</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input name="txtAmt" type="text" onKeyPress="return isNumberInput2Decimal(this, event);" value="<?=$teuinfo['teuAmt']?>" class="inputs" id="txtAmt">
          </span></td>
        </tr>
        
        <tr>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
          <?php if($_SESSION['user_level']==1){ ?>
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="saveteu();" name="save" id="save" value="Submit">
          <?php } ?>
          </td>
        </tr>
      </table>
    </form>
</BODY>
</HTML>
<script>
	function saveteu() {
		var empInputs = $('frmbank').serialize(true);
		
		if (empInputs['txtteuCode'] == "") {
			alert('TEU Code is required.');
			$('txtteuCode').focus();
            return false;		
		}        
		if (empInputs['txtteuCode'].length > 3) {
			alert('TEU Code requires 3 or less characters only.');
			$('txtteuCode').focus();
            return false;		
		}        
		
		if (empInputs['txtdesc'] == "") {
			alert('Description is required.');
			$('txtdesc').focus();
            return false;		
		}        

		if (empInputs['txtAmt'] == "") {
			alert('Amount is required.');
			$('txtAmt').focus();
            return false;		
		}        
       
		params = 'teu_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	

</SCRIPT>