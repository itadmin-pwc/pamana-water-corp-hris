<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$bnkStat = 'A';
if ($_GET['act'] == "EditBank") {
	$bankinfo = $maintEmpObj->getbankInfo($_GET['bankCd']);
	$bnkStat = $bankinfo['bankStat'];
}

switch($_GET['code']) {
	case "AddRef":

		if ($maintEmpObj->LonRef("Add",$_GET))
			echo "alert('Ref. No. Successfully Added.');";
		else
			echo "alert('Error Adding Ref. No.');";
	
		exit();
	break;
	case "EditRef":
		if ($maintEmpObj->LonRef("Edit",$_GET))
			echo "alert('Ref. No. Successfully Updated.');";
		else
			echo "alert('Error Updating Ref. No.');";

		exit();
	break;
	case "DeleteRef":
		if ($maintEmpObj->LonRef("Delete",$_GET))
			echo "alert('Ref. No. Successfully Deleted.');";
		else
			echo "alert('Error Deleting Ref. No.');";

		exit();
	break;
}
$arrRef = $maintEmpObj->getRefNo($_GET['id']);
?>

<HTML>
<head>
<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
<STYLE>@import url('../../style/payroll.css');</STYLE>
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
          <td class="gridDtlLbl style2 style3" >Ref. No.</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input value="<?=$arrRef['lonRefNo']?>" type="text" name="txtRefNo" id="txtRefNo" class="inputs" size="30">
          <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code">
          <input type="hidden" name="hdnID" value="<?=$_GET['id']?>" id="hdnID"></td>
        </tr>
        <tr>
          <td class="childGridFooter"><div id="dvtest">&nbsp;</div></td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
            <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="savebank();" name="save" id="save" value="Submit"></td>
        </tr>
      </table>
    </form>
    </BODY>
</HTML>
<script>
	function savebank() {
		var empInputs = $('frmbank').serialize(true);
		if (empInputs['txtRefNo'] == "") {
			alert('Ref. No. is required.');
			$('txtRefNo').focus();
            return false;		
		}        
		    
		params = 'lonref_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
</script>
