<?
##################################################

session_start(); 
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("csi_csp_migration.obj.php");

$cspObj = new migCSIObj();
$sessionVars = $cspObj->getSeesionVars();
$cspObj->validateSessions('','MODULES');
$proc = 2;
if(isset($_POST['btnUpload'])) {
	$error = $_FILES["fileUpload"]["error"];
	$curdate=date('mdY');
	if ($error == UPLOAD_ERR_OK && in_array($_FILES["fileUpload"]["type"],array("text/csv","text/plain","application/vnd.ms-excel"))) {
		
		$tmp_name = $_FILES["fileUpload"]["tmp_name"];
		$name = $_FILES["fileUpload"]["name"];
		$size = $_FILES["fileUpload"]["size"];				
		move_uploaded_file($tmp_name, "csi_csp-$curdate.txt");
		if ($cspObj->readLSTxtfile("csi_csp-$curdate.txt")) {
			$proc = 1;
		} else {
			$proc = 0;
		}
	} else {
		$proc = 0;
	}
}
?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/main_emp_loans.css');</style>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='../transactions/timesheet_js.js'></script>
</HEAD>
	<BODY>
<form action="" method="post" enctype="multipart/form-data" name="frmTS">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Upload 
        CSI/CSP Text File</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
		 
		  <tr> 
            <td width="18%" class="gridDtlLbl">File Name</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="81%" class="gridDtlVal"> <font size="2" face="Arial, Helvetica, sans-serif">
              <input name="fileUpload" type="file" id="fileUpload">
              </font> </td>
          </tr>
        </table>
  <br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input name="btnUpload" type="submit" id="btnUpload" value="Upload"  class="inputs">
              </CENTER>
					</td>
				  </tr>
			  </table> 
	</td>
	</tr> 
	<tr > 
		<td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#c3daf9; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>
<?php
if ($proc == 1) {
header("Location: ar_list.php")
?>
<? }
elseif ($proc == 0){?>
<script>alert("Migration failed!");</script>
<? }?>
