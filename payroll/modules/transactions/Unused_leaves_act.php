<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("lastPay.obj.php");

$lastPayObj = new lastPayObj($_SESSION,$_GET);
$lastPayObj->validateSessions('','MODULES');
$sessionVars = $lastPayObj->getSeesionVars();
$empNo = $_GET['empNo'];
switch($_GET['code']) {
	case "Leaves":
		$chLeaves = $lastPayObj->Leaves($empNo);
		if ($chLeaves['empNo'] == "") {
			$res = $lastPayObj->SaveLeaves('Add');
			$Act = "Saved";
			$ErrorAct = "Saving";
		} else {
			$res = $lastPayObj->SaveLeaves('Edit');
			$Act = "Updated";
			$ErrorAct = "Updating";
		}
	
		if ($res)
			echo "alert('Unused Leaves Successfully $Act.');";
		else
			echo "alert('Error $ErrorAct Unused Leaves.');";
	
		exit();
	break;
}
$empInfo = $lastPayObj->ResignedEmpList($empNo);
$empLeaves = $lastPayObj->Leaves($empNo);

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
	<form action="" method="post" name="frmLeaves" id="frmLeaves">
      <table width="430" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td class="gridDtlLbl style2 style3" >Employee No.</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=$empInfo['empNo']?>
          <input type="hidden" name="empNo" value="<?=$empNo?>" id="empNo">
          <input name="code" type="hidden" id="code" value="Leaves"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Name</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=str_replace('Ñ','&Ntilde;',$empInfo['empLastName']. ", " . $empInfo['empFirstName'] . " " . $empInfo['empMidName'])?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Unused Leave(s)</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input name="txtLeaves"  onKeyUp="computeLeaveAmt();"  onKeyPress="return isNumberInput2Decimal(this, event);"  onChange="val2DecNo(this.value,this.id); " type="text" value="<?=(float)$empLeaves['leaveDays']?>" class="inputs" id="txtLeaves"> <input name="txtDRate" type="hidden" id="txtDRate" value="<?=$empInfo['empDrate']?>"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Unused Leave(s) Amount</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input name="txtLeavesAmt" onKeyPress="return isNumberInput2Decimal(this, event);"  onChange="val2DecNo(this.value,this.id); " type="text" value="<?=(float)$empLeaves['leaveAmt']?>" class="inputs" id="txtLeavesAmt"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Cash Bond</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input name="txtCashBond" onKeyPress="return isNumberInput2Decimal(this, event);"  onChange="val2DecNo(this.value,this.id); " type="text" value="<?=(float)$empLeaves['cashBond']?>" class="inputs" id="txtCashBond"></td>
        </tr>
        
        <tr>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter"><input type="button" class="inputs" onClick="Leaves();" name="save" id="save" value="Submit"></td>
        </tr>
      </table>
    </form>
</BODY>
</HTML>
<script>

	function Leaves() {
		var empInputs = $('frmLeaves').serialize(true);
		if (empInputs['txtLeaves'] == "") {
			alert('Unused Leaves  is required.');
			$('txtLeaves').focus();
            return false;		
		}        

		params = 'Unused_leaves_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmLeaves').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
	
	function computeLeaveAmt() {
		var Amt = $('txtLeaves').value * $('txtDRate').value;
		Amt = format_number(Amt,2);
		$('txtLeavesAmt').value = Amt;
	}
	

</SCRIPT>