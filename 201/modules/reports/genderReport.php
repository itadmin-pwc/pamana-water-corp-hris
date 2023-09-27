<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("movement.trans.php");
##################################################
if ($_GET['code']=="") {
	$code = $_POST['code'];
} else {
	$code = $_GET['code'];
}

?>
<HTML>
	<HEAD>
<TITLE><?=SYS_TITLE?></TITLE>
<style>@import url('../../style/reports.css');</style>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
<script>
function exportExcel () {
//	if (document.frmTS.branch.value>0) {			
		document.frmTS.action = "genderReport_pdf.php";
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "genderReport.php";
		document.frmTS.target = "_self";
//	}
//	else{
//		alert('Branch is Required.');
//		return false;	
//	}	
}
function printPdf () {
//	if (document.frmTS.branch.value>0) {			
		document.frmTS.action = "gender_pdf.php";
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "genderReport.php";
		document.frmTS.target = "_self";
//	}
//	else{
//		alert('Branch is Required.');
//		return false;	
//	}
}

</script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Gender Report</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'></td>
          </tr>
          
          <tr>
            <td width="18%" class="gridDtlLbl">Branch</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 	
			$sqlBranch = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' and brnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')";				
			$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($sqlBranch));
								$arrBranch = $inqTSObj->makeArr($arrBranch,'brnCode','brnDesc','All');
								$inqTSObj->DropDownMenu($arrBranch,'branch',$empDiv,$empDiv_dis);
							?>            </td>
            </tr>            
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="salary2" class="inputs" id="salary2" <? echo $searchTS4_dis; ?> value="Print to Excel" onClick="printPdf();">
                <input type="button" name="salary" class="inputs" id="salary" <? echo $searchTS4_dis; ?> value="Export to PDF" onClick="exportExcel();">
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
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#fff; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>
