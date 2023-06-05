<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();

if($_GET['act']=="generate"){
	$sqlqry="Select holidayId, YEAR(holidayDate) as y,MONTH(holidayDate) as m,DAY(holidayDate) as d from tblLegalHolidays";
	$sqlres=$maintEmpObj->execQry($sqlqry);
	$sqlarr=$maintEmpObj->getArrRes($sqlres);
	foreach($sqlarr as $legalholidays){
		$id=$legalholidays['holidayId'];
		$m=$legalholidays['m'];
		$d=$legalholidays['d'];
		$date=$_GET['cmbyear']."/".$m."/".$d;
		$dates=date($date);
		// echo $dates;
		$sqlupdate="Update tblLegalHolidays set holidayDate='$dates' where holidayId='$id'";
		$maintEmpObj->execQry($sqlupdate);	
	}
	$qry="Call sp_legalHolidays ('{$_SESSION['company_code']}','{$_SESSION['employee_number']}')";
	if($maintEmpObj->execQry($qry)){
		echo "alert('Legal holidays for the year {$_GET['cmbyear']} has been saved.');";	
	}else{


		echo "Not saved";
	}
	exit();
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
	<form action="" method="post" name="frmlegal" id="frmlegal">
      <table width="288" border="0" class="childGrid" cellpadding="2" cellspacing="1">        
        <tr>
          <td class="gridDtlLbl style2 style3" >Select Year</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal">
          <select id="cmbyear" name="cmbyear">
          <option value=""></option>
          <?
          $year=date("Y")+5;
		  for($y=date("Y");$y<$year;$y++){
		  ?>
          <option value="<?=$y?>"><?=$y;?></option>
          <?
		  }
		  ?>
          </select>
          </td>
        </tr>
        <tr>
          <td colspan="3" class="childGridFooter" align="center">&nbsp;
          <?php if(($_SESSION['user_level']==1) || ($_SESSION['user_level']==2)){ ?>
           <input type="button" class="inputs" onClick="savelegalholiday();" name="Save" id="Save" value="Save">
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
         
          <?php } ?>          </td>
        </tr>
      </table>
    </form>
</BODY>
</HTML>
<script>
	function savelegalholiday() {
		var empInputs = $('frmlegal').serialize(true);
		if (empInputs['cmbyear'] == "") {
			alert('Year is required.');
			$('cmbyear').focus();
            return false;		
		}        
		params = 'holiday_legal.php?act=generate';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('cmbyear').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		});
	}	
</SCRIPT>