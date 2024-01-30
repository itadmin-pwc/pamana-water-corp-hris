<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
if ($_GET['act'] == "EditCompany") {
	$compInfo = $maintEmpObj->getCompanyInfo($_GET['compCode']);
}

switch($_GET['code']) {
	case "AddCompany":

		if ($maintEmpObj->Company("Add",$_GET))
			echo "alert('Company Successfully Added.');";
		else
			echo "alert('Error Adding Company.');";
	
		exit();
	break;
	case "EditCompany":
		if ($maintEmpObj->Company("Edit",$_GET))
			echo "alert('Company Successfully Updated.');";
		else
			echo "alert('Error Updating Company.');";

		exit();
	break;
	case "DeleteCompany":
		if ($maintEmpObj->Company("Delete",$_GET))
			echo "alert('Company Successfully Deleted.');";
		else
			echo "alert('Error Deleting Company.');";

		exit();
	break;
}

?>

<HTML>
<head>
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
      <table width="419" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        <tr>
          <td class="gridDtlLbl style2 style3" >Name</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compName']?>" type="text" name="txtname" id="txtname" class="inputs" size="30">
          <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code">
          <input type="hidden" name="compCode" value="<?=$_GET['compCode']?>" id="compCode"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Short Name</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compShort']?>" type="text" name="txtshortname" id="txtshortname" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Address 1</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compAddr1']?>" type="text" name="txtadd1" id="txtadd1" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Address 2</span></td>
          <td class="gridDtlLbl">&nbsp;</td>
          <td class="gridDtlVal"><input value="<?=$compInfo['compAddr2']?>" type="text" name="txtadd2" id="txtadd2" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Tax ID No.</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compTin']?>" type="text" onKeyPress="return isNumberInputEmpNoOnly(this, event);" name="txttin" id="txttin" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">SSS No.</span></td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compSssNo']?>" type="text" onKeyPress="return isNumberInputEmpNoOnly(this, event);" name="txtsss" id="txtsss" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >HDMF</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compPagibig']?>" type="text" onKeyPress="return isNumberInputEmpNoOnly(this, event);" name="txthdmf" id="txthdmf" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Phil Health</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compPHealth']?>" type="text" onKeyPress="return isNumberInputEmpNoOnly(this, event);" name="txtphil" id="txtphil" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">No. of Days</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compNoDays']?>" type="text" onKeyPress="return isNumberInputEmpNoOnly(this, event);" name="txtdays" id="txtdays" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >% Earnings Retention</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compEarnRetain']?>" type="text" onKeyPress="return isNumberInputEmpNoOnly(this, event);" name="txtretention" id="txtretention" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Non tax Bonus</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['nonTaxBonus']?>" onKeyPress="return isNumberInputEmpNoOnly(this, event);" type="text" name="txtbonus" id="txtbonus" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Pay Sign</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['compPaySign']?>" type="text" name="txtpaysign" id="txtpaysign" class="inputs" size="30"></td>
        </tr>
        <!-- 
		<tr>
          <td class="gridDtlLbl"><span class="headertxt">GL Code</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal">
          <input value="<?=$compInfo['gLCode']?>" type="text" name="txtglcode" id="txtglcode" class="inputs" size="30">
		  </td>
        </tr>
		-->
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Status</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal">
          <?$maintEmpObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$compInfo['compStat'],'class="inputs"'); ?></td>
        </tr>
        
        <tr>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter"><input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="savecompany();" name="save" id="save" value="Submit"></td>
        </tr>
      </table>
    </form>
</BODY>
</HTML>
<script>
	function savecompany() {
		var empInputs = $('frmbank').serialize(true);
		if (empInputs['txtname'] == "") {
			alert('Name is required.');
			$('txtname').focus();
            return false;		
		}        
		if (empInputs['txtshortname'] == "") {
			alert('Short Name is required.');
			$('txtshortname').focus();
            return false;		
		}        

		if (empInputs['txtadd1'] == "") {
			alert('Address 1 is required.');
			$('txtadd1').focus();
            return false;		
		}        
		if (empInputs['txttin'] == "") {
			alert('Tax ID No. is required.');
			$('txttin').focus();
            return false;		
		}   
		if (empInputs['txtsss'] == "") {
			alert('SSS No. is required.');
			$('txtsss').focus();
            return false;		
		}        
		if (empInputs['txthdmf'] == "") {
			alert('HDMF is required.');
			$('txthdmf').focus();
            return false;		
		}        

		if (empInputs['txtphil'] == "") {
			alert('Phil Health is required.');
			$('txtphil').focus();
            return false;		
		}        
		if (empInputs['txtdays'] == "") {
			alert('No. of Days is required.');
			$('txtdays').focus();
            return false;		
		}   
		if (empInputs['txtretention'] == "") {
			alert('% Retention is required.');
			$('txtretention').focus();
            return false;		
		}   				     
		if (empInputs['cmbStat'] == 0) {
			alert('Status is required.');
			$('cmbStat').focus();
            return false;		
		}   				     
		
		params = 'company_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	

	function isNumberInputEmpNoOnly(field, event) {
	  var key, keyChar;
	
	  if (window.event)
		key = window.event.keyCode;
	  else if (event)
		key = event.which;
	  else
		return true;
	  // Check for special characters like backspace
	  if (key == null || key == 0 || key == 8 || key == 27 || key == 13) {
		return true;
	  }
	  // Check to see if it's a number
	  keyChar =  String.fromCharCode(key);
	  if (/\d/.test(keyChar)) 
		{
		 window.status = "";
		 return true;
		} 
	  else 
	   {
		window.status = "Field accepts numbers only.";
		return false;
	   }
	}
</SCRIPT>