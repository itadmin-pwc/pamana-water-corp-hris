<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");


$maintEmpObj = new maintenanceObj();
switch($_GET['code']) {
	case "Generate":
		if ($maintEmpObj->checkPeriod($_GET['cmbYear']) == 0) {

			if ($maintEmpObj->Generate($_GET['cmbYear']))
				echo "alert('Payroll Period Successfully Generated for {$_GET['cmbYear']}.');";
			else
				echo "alert('Error Generating Payroll Period.');";
				
		} else	{
			echo "alert('Error Generating Payroll Period, Selected Year Already Exist!');";
		}
	
		exit();
	break;
	case "Open":
		$where = " and compCode='{$_SESSION['company_code']}' and payGrp='{$_SESSION['pay_group']}' and payCat='{$_SESSION['pay_category']}' and pdStat='O'";
		$arrPd = $maintEmpObj->getPayPeriod($_SESSION['company_code'],$where);
		$arr['pdNum'] 	= $arrPd['pdNumber'];
		$arr['pdYear'] 	= $arrPd['pdYear'];
		$arr['dtFrm'] 	= $arrPd['pdFrmDate'];
		$arr['dtTo'] 	= $arrPd['pdToDate'];
		$arr['pdMonth'] = date("m", strtotime($arrPd['pdPayable']));

		if ($_SESSION['pay_category'] != 9) {
			include("../Processing/regular_payroll_processing.obj.php");
			$ClosePDObj = new regPayrollProcObj($arr,$_SESSION);
			if ($ClosePDObj->reProcRegPayroll() &&  $maintEmpObj->OpenPeriod($_GET['pdSeries'])) {
				echo "alert('Payroll period successfully opened');\n";
				echo "pager('period_list_ajax.php','TSCont','load',0,0,'','','','../../../images/');";
			} else {
				echo "alert('Opening Payroll period failed');\n";
			}
		} else {
			include("../Processing/lastpay_processing.obj.php");
			$ClosePDObj = new lastPayProcObj($arr,$_SESSION);
			if ($ClosePDObj->reProcLastPayroll() &&  $maintEmpObj->OpenPeriod($_GET['pdSeries']) && $maintEmpObj->ClearLastPayData($arr['pdYear'],$arr['pdNum'])) {
				echo "alert('Payroll period successfully opened');\n";
				echo "pager('period_list_ajax.php','TSCont','load',0,0,'','','','../../../images/');";
			} else {
				echo "alert('Opening Payroll period failed');\n";
			}			
		}
		exit();
	break;
	case "Update":
		if ($maintEmpObj->UpdateCurPayPeriod($_GET)) {
			echo "alert('Payroll period successfully updated!');\n";
		} else {
			echo "alert('Updating Payroll period failed!');\n";
		}
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
	<? if ($_GET['act'] == 'Generate') {?>	
      <table width="430" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td class="gridDtlLbl style2 style3" >Group</td>
          <td class="gridDtlLbl style2 style3">&nbsp;</td>
          <td class="gridDtlVal"><?=$_SESSION['pay_group']?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Year</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><select name="cmbYear" class="inputs" id="cmbYear">
            <? 
			$dtYear = date('Y');
			for ($i=0;$i<10;$i++) {?>
            <option value="<?=$dtYear?>">
              <?=$dtYear;?>
            </option>
            <? $dtYear += 1;
            }?>
          </select>
          <input type="hidden" value="Generate" name="code" id="code"></td>
        </tr>
        <tr>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter"><input type="button" class="inputs" onClick="Generate();" name="save" id="save" value="Generate"></td>
        </tr>
      </table>
      <? } else {?>
      <table width="430" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td class="gridDtlLbl style2 style3" >Pay Group</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=$_SESSION['pay_group']?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Pay Category</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?
		  
		  switch($_SESSION['pay_category']) {
			case 1:
				echo "Executive";
			break ;
			case 2:
				echo "Confidential";
			break ;
			case 3:
				echo "Non-Confidential";
			break ;
			case 9:
				echo "Resigned";
			break ;
		  }?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >PD Payable</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input type="text" id="txtPDPayable" name="txtPDPayable" value="<?=$_GET['pdPayable']?>">
          <a href="#"><img name="imgPD" id="imgPD" type="image" src="../../../images/cal_new.gif" title="Holiday Date" style="cursor: pointer;position:relative;top:3px;border:none;" /></a></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >From Date</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input type="text" id="txtFrDate" name="txtFrDate" value="<?=$_GET['pdFrom']?>">
          <a href="#"><img name="imgFrom" id="imgFrom" type="image" src="../../../images/cal_new.gif" title="Holiday Date" style="cursor: pointer;position:relative;top:3px;border:none;" /></a></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >To Date</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input type="text" id="txtToDate" name="txtToDate" value="<?=$_GET['pdTo']?>">
            <a href="#"><img name="imgTo" id="imgTo" type="image" src="../../../images/cal_new.gif" title="Holiday Date" style="cursor: pointer;position:relative;top:3px;border:none;" /></a>
          <input type="hidden" value="<?=$_GET['pdSeries']?>" name="pdSeries" id="pdSeries"></td>
        </tr>
        <tr>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter"><input type="button" class="inputs" onClick="UpdatePayPeriod();" name="save" id="save" value="Update"></td>
        </tr>
      </table>
      <? } ?>
    </form>
</BODY>
</HTML>
<script>
	function Generate() {
		var empInputs = $('frmbank').serialize(true);     
		params = 'period_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
	
	function UpdatePayPeriod() {
		var ans = confirm("Are you sure you wan to update the current period?");
		if (ans==false) {
			return false;	
		}
		params = 'period_act.php?code=Update';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});		
	}
	<? if ($_GET['act'] == 'Edit') {?>
	Calendar.setup({
				  inputField  : "txtPDPayable",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgPD"       // ID of the button
			}
		)
	Calendar.setup({
				  inputField  : "txtFrDate",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgFrom"       // ID of the button
			}
		)
	Calendar.setup({
				  inputField  : "txtToDate",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgTo"       // ID of the button
			}
		)
	<?}?>			
</SCRIPT>