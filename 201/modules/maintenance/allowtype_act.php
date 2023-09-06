<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
if ($_GET['act'] == "EditAllowanceType") {
	$info = $maintEmpObj->getAllowTypeinfo($_GET['allowCode']);
}

switch($_GET['code']) {
	case "AddAllowanceType":
		if ($maintEmpObj->CheckAllow($_GET['cmbtrnCode'],'trnCd')!=0) {
			echo "alert('Trans Type Already Exist.');";
			exit();
		}
		
		if ($maintEmpObj->AllowType("Add",$_GET))
			echo "alert('Allowance Type Successfully Added.');";
		else
			echo "alert('Error Adding Allowance Type.');";
	
		exit();
	break;
	case "EditAllowanceType":
		if ($maintEmpObj->CheckAllow($_GET['cmbtrnCode'],'trnCd')!=0) {
			echo "alert('Trans Type Already Exist.');";
			exit();
		}
	
		if ($maintEmpObj->AllowType("Edit",$_GET))
			echo "alert('Allowance Type Successfully Updated.');";
		else
			echo "alert('Error Updating Allowance Type.');";

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
      <table width="414" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td class="gridDtlLbl style2 style3" >Description</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input value="<?=$info['allowDesc']?>" type="text" name="txtdesc" id="txtdesc" class="inputs" size="30">
          <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code">
          <input type="hidden" name="aCode" value="<?=$_GET['allowCode']?>" id="aCode"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style3">Attendance Tag</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><? $maintEmpObj->DropDownMenu(array('Y'=>'Yes','N'=>'No'),'cmbAttntag',$info['attnBase'],'class="inputs"'); ?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style3">Separate Pay Slip</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><?
          if ($info['sprtPS'] == 'Y') {
		  	$ttag = $info['sprtPS'];
		  } else {
		  	$ttag = NULL;
		  }
		  $maintEmpObj->DropDownMenu(array('Y'=>'Yes',NULL=>'No'),'cmbTaxtag',$ttag,'class="inputs"'); ?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style3">Trans Type</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getTransCode('E'),'trnCode','trnDesc',''),'cmbtrnCode',$info['trnCode'],'class="inputs" style="width:222px;"'); ?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style3">Schedule</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><? $maintEmpObj->DropDownMenu(array('','1st Payroll of Month','2nd Payroll of Month','Attendance based'),'cmbschedule',$info['allowSked_type'],'class="inputs" style="width:222px;"'); ?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Status</span></td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><? $maintEmpObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$info['allowTypeStat'],'class="inputs"'); ?>
            <span class="gridDtlVal">
            <input type="hidden" name="txttrnCode" value="<?=$info['trnCode']?>" id="txttrnCode">
          </span></td>
        </tr>
        <tr>
          <td class="childGridFooter"><div id="dvtest">&nbsp;</div></td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
          <?php if($_SESSION['user_level']==1){ ?>
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="saveallowtype();" name="save" id="save" value="Submit">
          <?php } ?>
          </td>
        </tr>
      </table>
    </form>
    </BODY>
</HTML>
<script>
	function saveallowtype() {
		var empInputs = $('frmbank').serialize(true);
		if (empInputs['txtdesc'] == "") {
			alert('Description is required.');
			$('txtdesc').focus();
            return false;		
		} 
		if (empInputs['cmbtrnCode'] == 0) {
			alert('Trans Type is required.');
			$('cmbtrnCode').focus();
            return false;		
		}
		if (empInputs['cmbStat'] == 0) {
			alert('Status is required.');
			$('cmbStat').focus();
            return false;		
		}		 		       
		params = 'allowtype_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
</script>
