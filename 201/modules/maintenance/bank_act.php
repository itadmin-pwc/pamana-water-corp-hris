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
	case "AddBank":

		if ($maintEmpObj->Bank("Add",$_GET))
			echo "alert('Bank Successfully Added.');";
		else
			echo "alert('Error Adding Bank.');";
	
		exit();
	break;
	case "EditBank":
		if ($maintEmpObj->Bank("Edit",$_GET))
			echo "alert('Bank Successfully Updated.');";
		else
			echo "alert('Error Updating Bank.');";

		exit();
	break;
	case "DeleteBank":
		if ($maintEmpObj->Bank("Delete",$_GET))
			echo "alert('Bank Successfully Deleted.');";
		else
			echo "alert('Error Deleting Bank.');";

		exit();
	break;
}

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
          <td class="gridDtlLbl style2 style3" >Bank Description</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input value="<?=$bankinfo['bankDesc']?>" type="text" name="txtbank" id="txtbank" class="inputs" size="30">
          <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code">
          <input type="hidden" name="txtbankCd" value="<?=$_GET['bankCd']?>" id="txtbankCd"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Bank Branch</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input value="<?=$bankinfo['bankBrn']?>" type="text" name="txtbranch" id="txtbranch" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Address 1</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input value="<?=$bankinfo['bankAddr1']?>" type="text" name="txtadd1" id="txtadd1" class="inputs" size="40">
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Address 2</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input value="<?=$bankinfo['bankAddr2']?>" type="text" name="txtadd2" id="txtadd2" class="inputs" size="40">
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Address 3</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input value="<?=$bankinfo['bankAddr3']?>" type="text" name="txtadd3" id="txtadd3" class="inputs" size="40">
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Status</span></td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><?$maintEmpObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$bnkStat,'class="inputs"'); ?></td>
        </tr>
        <tr>
          <td class="childGridFooter"><div id="dvtest">&nbsp;</div></td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
          <?php if($_SESSION['user_level']==1){ ?>
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="savebank();" name="save" id="save" value="Submit">
          <?php } ?>
          </td>
        </tr>
      </table>
    </form>
    </BODY>
</HTML>
<script>
	function savebank() {
		var empInputs = $('frmbank').serialize(true);
		if (empInputs['txtbank'] == "") {
			alert('Bank Description is required.');
			$('txtbank').focus();
            return false;		
		}        
		if (empInputs['cmbStat'] == 0) {
			alert('Status is required.');
			$('cmbStat').focus();
            return false;		
		}        
		params = 'bank_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
</script>
