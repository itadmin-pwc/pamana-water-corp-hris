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
          <td colspan="4" class="parentGridHdr">&nbsp;<img src="../../../images/grid.png">&nbsp;Contact </td>
        </tr>
        <tr>
          <td class="parentGridDtl"><table width="390" border="0" class="childGrid" cellpadding="2" cellspacing="1">
            <tr>
              <td colspan="3" height="15"></td>
            </tr>          
            <tr>
              <td class="headertxt" >Contact Type</td>
              <td class="headertxt" width="1%">:</td>
              <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getcontacttypeswil(),'contactCd','contactDesc',''),'cmbcontacttype',$contactCD,'class="inputs" style="width:222px;"'); ?></td>
            </tr>
            <tr>
              <td class="headertxt" >Contact Description</td>
              <td class="headertxt">:</td>
              <td class="gridDtlVal"><input value="<?=$contactdesc;?>" type="text" name="txtdesc" id="txtdesc" class="inputs" size="30"></td>
            </tr>
            <tr>
              <td><div id="dvtest">&nbsp;</div></td>
              <td>&nbsp;</td>
              <td><input type="submit" class="inputs" onClick="savecontact('<?=$_SESSION['strprofile']?>','<?=$act?>','<?=$recNo;?>','profile.obj.php');" name="save" id="save" value="Submit"></td>
            </tr>
            <tr>
              <td colspan="3" height="15"></td>
            </tr>
          </table></td>
        </tr>
      </TABLE>
</BODY>
</HTML>
<?
		  if ($act=="Add" && $error==0)
		  	echo "<script>alert('Record Added!');</script>";
		  elseif ($act=="Edit" && $error==0)
		  	echo "<script>alert('Record Updated!');</script>";;
?>
