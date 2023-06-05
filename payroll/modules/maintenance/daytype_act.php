<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$bnkStat = 'A';
if ($_GET['act'] == "EditDayType") {
	$info = $maintEmpObj->getDayTypeinfo($_GET['daytype']);
}

switch($_GET['code']) {
	case "AddDayType":

		if ($maintEmpObj->DayType("Add",$_GET))
			echo "alert('Day Type Successfully Added.');";
		else
			echo "alert('Error Adding Day Type.');";
	
		exit();
	break;
	case "EditDayType":
		if ($maintEmpObj->DayType("Edit",$_GET))
			echo "alert('Day Type Successfully Updated.');";
		else
			echo "alert('Error Updating Day Type.');";

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
          <td class="gridDtlLbl style2 style3" >Day Type</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input value="<?=$info['dayTypeDesc']?>" type="text" name="txtdesc" id="txtdesc" class="inputs" size="30">
          <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code">
          <input type="hidden" name="txtdaytype" value="<?=$_GET['daytype']?>" id="txtdaytype"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Status</span></td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><?$maintEmpObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$info['dayStat'],'class="inputs"'); ?></td>
        </tr>
        <tr>
          <td class="childGridFooter"><div id="dvtest">&nbsp;</div></td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
          <?php if($_SESSION['user_level']==1){ ?>
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="savedaytype();" name="save" id="save" value="Submit">
          <?php } ?>
          </td>
        </tr>
      </table>
    </form>
    </BODY>
</HTML>
<script>
	function savedaytype() {
		var empInputs = $('frmbank').serialize(true);
		if (empInputs['txtdesc'] == "") {
			alert('Day Type is required.');
			$('txtdesc').focus();
            return false;		
		}        
		if (empInputs['cmbStat'] == 0) {
			alert('Status is required.');
			$('cmbStat').focus();
            return false;		
		}        
		params = 'daytype_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
</script>
