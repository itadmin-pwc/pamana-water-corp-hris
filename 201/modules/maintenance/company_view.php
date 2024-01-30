<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$compInfo = $maintEmpObj->getCompanyInfo($_GET['compCode']);



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
          <td width="38%" class="gridDtlLbl style2 style3" >Name</td>
          <td width="4%" class="gridDtlLbl style2 style3">:</td>
          <td width="58%" class="gridDtlVal"><?=$compInfo['compName']?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Short Name</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=$compInfo['compShort']?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Address 1</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal"><?=$compInfo['compAddr1'] . ", " . $compInfo['compAddr2']?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Zip Code</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal"><?=$compInfo['compZipCode']?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Tax ID No.</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal"><?=$compInfo['compTin']?></td>
        </tr>
        
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">SSS No.</span></td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=$compInfo['compSssNo']?></td>
        </tr>
        <tr>
          <td width="38%" class="gridDtlLbl style2 style3" >HDMF</td>
          <td width="4%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=$compInfo['compPagibig']?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Phil Health</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=$compInfo['compPHealth']?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">No. of Days</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal"><?=$compInfo['compNoDays']?></td>
        </tr>
       
        <tr>
          <td class="gridDtlLbl style2 style3" >Non tax Bonus</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><?=$compInfo['nonTaxBonus']?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Pay Sign</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal"><?=$compInfo['compPaySign']?></td>
        </tr>
        <!--
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">GL Code</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal"><?=$compInfo['gLCode']?></td>
        </tr>
        -->
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Status</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td class="gridDtlVal"><?=$compInfo['status']?></td>
        </tr>
      </table>
    </form>
</BODY>
</HTML>
