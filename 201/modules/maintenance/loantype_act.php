<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$bnkStat = 'A';
if ($_GET['act'] == "EditLoanType") {
	$info = $maintEmpObj->getLoanTypeinfo($_GET['loantype']);
}

switch($_GET['code']) {
	case "AddLoanType":
		if ($maintEmpObj->CheckLoan($_GET['txtloanCd'],'loanCd')!=0) {
			echo "alert('Loan Code Already Exist.');";
			exit();
		}
		if ($maintEmpObj->CheckLoan($_GET['cmbtrnCode'],'trnCd')!=0) {
			echo "alert('Trans Type Already Exist.');";
			exit();
		}
		
		
		if ($maintEmpObj->LoanType("Add",$_GET))
			echo "alert('Loan Type Successfully Added.');";
		else
			echo "alert('Error Adding Loan Type.');";
	
		exit();
	break;
	case "EditLoanType":
		if ($maintEmpObj->CheckLoan($_GET['txtloanCd'],'loanCd')!=0) {
			echo "alert('Loan Code Already Exist.');";
			exit();
		}
		if ($maintEmpObj->CheckLoan($_GET['cmbtrnCode'],'trnCd')!=0) {
			echo "alert('Trans Type Already Exist.');";
			exit();
		}
	
		if ($maintEmpObj->LoanType("Edit",$_GET))
			echo "alert('Loan Type Successfully Updated.');";
		else
			echo "alert('Error Updating Loan Type.');";

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
          <td class="gridDtlLbl style2 style3" >Loan Code</td>
          <td class="gridDtlLbl style2 style3">&nbsp;</td>
          <td class="gridDtlVal"><input value="<?=$info['lonTypeCd']?>" type="text" name="txtloanCd" id="txtloanCd" class="inputs" size="30"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Loan Type</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input value="<?=$info['lonTypeDesc']?>" type="text" name="txtdesc" id="txtdesc" class="inputs" size="30">
          <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code">
          <input type="hidden" name="txtdaytype" value="<?=$_GET['loantype']?>" id="txtdaytype"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style3">Short Desc</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><span class="gridDtlVal">
            <input value="<?=$info['lonTypeShortDesc']?>" type="text" name="txtdesc2" id="txtdesc2" class="inputs" size="30">
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style3">Trans Type</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getTransCode('D'),'trnCode','trnDesc',''),'cmbtrnCode',$info['trnCode'],'class="inputs" style="width:222px;"'); ?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Status</span></td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><?$maintEmpObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$info['lonTypeStat'],'class="inputs"'); ?>
            <span class="gridDtlVal">
            <input type="hidden" name="txttrnCode" value="<?=$info['trnCode']?>" id="txttrnCode">
            <input type="hidden" name="lCode" value="<?=$info['lonTypeCd']?>" id="lCode">
          </span></td>
        </tr>
        <tr>
          <td class="childGridFooter"><div id="dvtest">&nbsp;</div></td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
          <?php if($_SESSION['user_level']==1){ ?>
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="saveloantype();" name="save" id="save" value="Submit">
          <?php } ?>
          </td>
        </tr>
      </table>
    </form>
    </BODY>
</HTML>
<script>
	function saveloantype() {
		var empInputs = $('frmbank').serialize(true);
		if (empInputs['txtloanCd'] == "") {
			alert('Loan Code is required.');
			$('txtloanCd').focus();
            return false;		
		}        
		if (empInputs['txtdesc'] == "") {
			alert('Loan Type is required.');
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
		params = 'loantype_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
</script>
