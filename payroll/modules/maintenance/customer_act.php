<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$empInfo = $maintEmpObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],'');

switch($_GET['code']) {
	case "AddCustNo":
		if(!$maintEmpObj->checkCustNo($_GET)){
			echo "alert('Customer No. already exist!');";
		}
		else{
			if ($maintEmpObj->saveCustNo($_GET)){
				echo "alert('Customer No. Successfully Added.');";
			}
			else{
				echo "alert('Error Saving Customer No.');";
			}
		}
		exit();
	break;
	case "EditCustNo":
		if(!$maintEmpObj->checkCustNo($_GET)){
			echo "alert('Customer No. already exist!');";
		}
		else{
			if ($maintEmpObj->saveCustNo($_GET)){
				echo "alert('Customer No. Successfully Updated.');";
			}
			else{
				echo "alert('Error Updating Customer No.');";
			}
		}
		exit();
	break;
}
$code =($_GET['custNo']!='') ? "AddCustNo":"EditCustNo";
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
	<form action="" method="post" name="frmcust" id="frmcust">
      <table width="430" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td class="gridDtlLbl style2 style3" >Employee No.</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=$_GET['empNo']?><input type="hidden" value="<?=$code;?>" name="code" id="code">
          <input type="hidden" name="txtempNo" value="<?=$_GET['empNo']?>" id="txtempNo"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Name</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=$empInfo['empLastName'] . ', '.$empInfo['empFirstName'].' ' . $empInfo['empMidName'][0]?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Customer No.</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input name="txtcustNo" type="text" class="inputs" id="txtcustNo" value="<?=$_GET['custNo'];?>" size="30">
          </span></td>
        </tr>
        <tr>
          <td class="childGridFooter"><div id="dvtest">&nbsp;</div></td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
          <?php 
		  //if($_SESSION['user_level']==1){ 
		  ?>
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="savecustNo();" name="save" id="save" value="Submit">
          <?php 
		  //} 
		  ?>
          </td>
        </tr>
      </table>
    </form>
    </BODY>
</HTML>
<script>
	function savecustNo() {
		var empInputs = $('frmcust').serialize(true);
		if (empInputs['txtcustNo'] == "") {
			alert('Customer No. is required.');
			$('txtcustNo').focus();
            return false;		
		}        
		params = 'customer_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmcust').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
</script>
