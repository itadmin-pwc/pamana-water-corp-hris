<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$bnkStat = 'A';
if ($_GET['act'] == "EditHoliday") {
	$holinfo = $maintEmpObj->getHolidayInfo($_GET['seqno']);
	$holStat = $holinfo['holidayStat'];
}
switch($_GET['code']) {
	case "AddHoliday":

		if ($maintEmpObj->Holiday("Add",$_GET))
			echo "alert('Holiday Successfully Added.');";
		else
			echo "alert('Error Adding Holiday.');";
	
		exit();
	break;
	case "EditHoliday":
		if ($maintEmpObj->Holiday("Edit",$_GET))
			echo "alert('Holiday Successfully Updated.');";
		else
			echo "alert('Error Updating Holiday.');";

		exit();
	break;
	case "DeleteHoliday":
		if ($maintEmpObj->Holiday("Delete",$_GET))
			echo "alert('Holiday Successfully Deleted.');";
		else
			echo "alert('Error Deleting Holiday.');";

		exit();
	break;
	case "branch":
		if ($_GET['grp']!=0) {
			$maintEmpObj->DropDownMenu($maintEmpObj->makeArr(
					$maintEmpObj->getBranchperGrp($_GET['grp']),'brnCode','brnDesc',''
				),
				'cmbbranch',$cmbbranch,'class="inputs"' 
			);
		} else {
			$maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranch($_SESSION['company_code']),'brnCode','brnDesc',''),'cmbbranch',$holinfo['brnCode'],'class="inputs" style="width:222px;"');		
		}	
		exit();
	break;	
}
if ($holinfo['holidayDate'] != "") {
	$hdate = $holinfo['holidayDate'];
} else {
	$hdate = date("m/d/Y");
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
      <table width="430" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td class="gridDtlLbl style2 style3" >Group</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?
								$maintEmpObj->DropDownMenu($maintEmpObj->makeArr(
										$maintEmpObj->getBrnGrp(),'GrpCode','GrpDesc',''
									),
									'cmbGrp',$cmbGrp,'class="inputs" onChange="getBranch(this.value);"' 
								);
							?>
            <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code">
            <input type="hidden" name="txtseqno" value="<?=$_GET['seqno']?>" id="txtseqno"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Branch</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal">
            <div align="left" id="divBranch">
              <? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranch($_SESSION['company_code']),'brnCode','brnDesc',''),'cmbbranch',$holinfo['brnCode'],'class="inputs" style="width:222px;"'); ?>
          </div></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Holiday Date</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input class="inputs" name="txtdate" id="txtdate" value="<? echo date("m/d/Y",strtotime($hdate)) ?>" readonly type="text" size="15" maxlength="50" />
          <a href="#"><img name="imgdate" id="imgdate" type="image" src="../../../images/cal_new.png" title="Holiday Date" style="cursor: pointer;position:relative;top:3px;border:none;" /></a> </td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Holiday Description</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input value="<?=$holinfo['holidayDesc']?>" type="text" name="txtdesc" id="txtdesc" class="inputs" size="40">
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Day Type</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getDayType(),'dayType','dayTypeDesc',''),'cmbday',$holinfo['dayType'],'class="inputs" style="width:222px;"'); ?></td>
        </tr>
        
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Status</span></td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><?$maintEmpObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$holStat,'class="inputs"'); ?></td>
        </tr>
        <tr>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
          <?php if($_SESSION['user_level']==1){ ?>
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="saveholiday();" name="save" id="save" value="Submit">
          <?php } ?>
          </td>
        </tr>
      </table>
    </form>
</BODY>
</HTML>
<script>
	function getBranch(grp){
		new Ajax.Request(
		  'holiday_act.php?code=branch&grp='+grp,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$('divBranch').innerHTML=req.responseText;
			 }
		  }
		);		
	}
	function saveholiday() {
		var empInputs = $('frmbank').serialize(true);
		if (empInputs['txtdate'] == "") {
			alert('Holiday Date is required.');
			$('txtdate').focus();
            return false;		
		}        

		if (empInputs['txtdesc'] == "") {
			alert('Holiday Description is required.');
			$('txtdesc').focus();
            return false;		
		}        
		if (empInputs['cmbday'] == 0) {
			alert('Day Type is required.');
			$('cmbday').focus();
            return false;		
		}        
		params = 'holiday_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmbank').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	

		Calendar.setup({
				  inputField  : "txtdate",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgdate"       // ID of the button
			}
		)
</SCRIPT>