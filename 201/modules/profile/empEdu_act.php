<?
session_start();
include("profile.obj.php");
$maintEmpObj = new commonObj();
$profileobj = new ProfileObj();
$error="1";
$act=$_GET['act'];
$recNo=$_GET['recNo'];
if ($_POST['save']!="") {
	$empNo=$_SESSION['strprofile'];
	$type=$_POST['cmbcontacttype'];
	$desc=$_POST['txtdesc'];
	$error=0;
	$profileobj->employeeaction($act,$type,$desc,$empNo,$recNo);
}
if ($_GET['act']=="Edit Contact") {
	$res=$profileobj->getcontactinfo($recNo);
	foreach ($res as $value) {
		$contactCD=$value['contactCd'];
		$contactdesc=$value['contactName'];
	}
}
if ($_GET['code']=="8"){
	include("profile.obj.php");
	$recNo=$_GET['recNo'];
	$profileobj = new ProfileObj();
	$profileobj->employeeaction("Delete","","","",$recNo);
	echo "<script>alert('Record Deleted!');</script>";
}
?>

<HTML>
<head>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->

<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<script src="../../../includes/validations.js"></script>		
		<STYLE>@import url('../../style/payroll.css');</STYLE>
        		<style type="text/css">
        <!--
        .headertxt {font-family: verdana; font-size: 11px;}
        -->
        </style>

</head>
	<BODY>
      <TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
        <tr>
          <td colspan="4" class="parentGridHdr">&nbsp;<img src="../../../images/grid.png">&nbsp;Educational Background</td>
        </tr>
        <tr>
          <td class="parentGridDtl"><table width="450" border="0" class="childGrid" cellpadding="2" cellspacing="1">
            <tr>
              <td colspan="3" height="15"></td>
            </tr>          
            <tr>
              <td class="headertxt" >Type</td>
              <td class="headertxt" width="1%">:</td>
              <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getcontacttypeswil(),'contactCd','contactDesc',''),'cmbcontacttype',$contactCD,'class="inputs" style="width:222px;"'); ?></td>
            </tr>
            <tr>
              <td class="headertxt" >School</td>
              <td class="headertxt">:</td>
              <td class="gridDtlVal"><input value="<?=$contactdesc;?>" type="text" name="txtdesc" id="txtdesc" class="inputs" size="50"></td>
            </tr>
            <tr>
              <td class="headertxt">Degree</td>
              <td>:</td>
              <td><span class="gridDtlVal">
                <input value="<?=$contactdesc;?>" type="text" name="txtdesc2" id="txtdesc2" class="inputs" size="50">
              </span></td>
            </tr>
            <tr>
              <td class="headertxt">From</td>
              <td>:</td>
              <td><span class="gridDtlVal">
                <input class="inputs" name="fdate" id="fdate" value="<? echo $rdDate; ?>" disabled="true" type="text" size="25" maxlength="50" >
                <a href="#"><img name="imgfDate" id="imgfDate" type="image" src="../../../images/cal_new.gif" title="Start Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></span></td>
            </tr>
            <tr>
              <td class="headertxt">To</td>
              <td>:</td>
              <td><span class="gridDtlVal">
                <input class="inputs" name="tdate" id="tdate" value="<? echo $rdDate; ?>" disabled="true" type="text" size="25" maxlength="50" >
                <a href="#"><img name="imgtDate" id="imgtDate" type="image" src="../../../images/cal_new.gif" title="Start Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></span></td>
            </tr>
            <tr>
              <td><div id="dvtest">&nbsp;</div></td>
              <td>&nbsp;</td>
              <td><input type="button" class="inputs" onClick="savecontact('<?=$_SESSION['strprofile']?>','<?=$act?>','<?=$recNo;?>','profile.obj.php');" name="save" id="save" value="Save"></td>
            </tr>
            <tr>
              <td colspan="3" height="15"></td>
            </tr>
          </table></td>
        </tr>
      </TABLE>
</BODY>
</HTML>
<SCRIPT>
		Calendar.setup({
				  inputField  : "fdate",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgfDate"       // ID of the button
			}
		)
		Calendar.setup({
				  inputField  : "tdate",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgtDate"       // ID of the button
			}
		)		
</SCRIPT>