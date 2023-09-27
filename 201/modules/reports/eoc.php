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
	if(document.frmTS.txtfrDate.value==''){
		alert('From Date is Required.');
		return false;
		}
	if(document.frmTS.txttoDate.value==''){
		alert('To Date is Required');
		return false;
		}	
//	if(document.frmTS.empDiv.value!=0){
//		if(document.frmTS.empDept.value==0){
//			alert('Department is Required');
//			return false;
//		}
//	}	
		document.frmTS.action = "eoc_pdf.php";
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "eoc.php";
		document.frmTS.target = "_self";

}
function printPdf () {
	if(document.frmTS.txtfrDate.value==''){
		alert('From Date is Required.');
		return false;
		}
	if(document.frmTS.txttoDate.value==''){
		alert('To Date is Required');
		return false;
		}	
//	if(document.frmTS.empDiv.value!=0){
//		if(document.frmTS.empDept.value==0){
//			alert('Department is Required');
//			return false;
//		}
//	}	
		document.frmTS.action = "eoc_pdf2.php";
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "eoc.php";
		document.frmTS.target = "_self";
//	}
}

</script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;List 
        of EOC Report</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'></td>
          </tr>
          
		  <tr>
          	<td width="18%" class="gridDtlLbl">From</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158%" colspan="3" class="gridDtlVal"><input name="txtfrDate" type="text" class="inputs" id="txtfrDate" size="10" readonly>
                      <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgtxtfrDate" id="imgtxtfrDate" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
          </tr>
          <tr>
          	<td width="18%" class="gridDtlLbl">To</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158%" class="gridDtlVal"><input name="txttoDate" type="text" class="inputs" id="txttoDate" size="10" readonly>
                      <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgtxttoDate" id="imgtxttoDate" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
          </tr>
          <tr>
            <td width="18%" class="gridDtlLbl">Branch</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 	
			$sqlBranch = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' and brnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')";				
			$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($sqlBranch));
								$arrBranch = $inqTSObj->makeArr($arrBranch,'brnCode','brnDesc','All Stores');
								$inqTSObj->DropDownMenu($arrBranch,'branch',$empDiv,$empName_dis);
							?>            </td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Division</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 					
								$arrDept = $inqTSObj->makeArr($inqTSObj->getDivArt($compCode),'divCode','deptDesc','All');
								$inqTSObj->DropDownMenu($arrDept,'empDiv',$empDiv,$empDiv_dis);
							?></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Department</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"> <div id="deptDept"> 
                <input name="hide_empDept" type="hidden" id="hide_empDept" value="<? echo $empDept; ?>">
                <? 					
								$arrDept = $inqTSObj->makeArr($inqTSObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','All');
								$inqTSObj->DropDownMenu($arrDept,'empDept',$empDept,$empDept_dis);
							?>
                <input class="inputs" name="empNo" id="empNo" type="hidden" size="12" maxlength="11">
                <input class="inputs" name="empName" id="empName" type="hidden" size="25" maxlength="50">
                <input class="inputs" name="orderBy" id="orderBy" type="hidden" size="25" maxlength="50">
                <input name="empSect" type="hidden" id="empSect">
            </div> <input name="hide_empSect" type="hidden" id="hide_empSect"></td>
            </tr>            

        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="salary2" class="inputs" id="salary2" <? echo $searchTS4_dis; ?> value="Print to PDF" onClick="printPdf(); valDateStartEnd(document.frmTS.txtfrDate.value,document.frmTS.txtfrDate.id,document.frmTS.txttoDate.value);">
                <input type="button" name="salary" class="inputs" id="salary" <? echo $searchTS4_dis; ?> value="Export to Excel" onClick="exportExcel(); valDateStartEnd(document.frmTS.txtfrDate.value,document.frmTS.txtfrDate.id,document.frmTS.txttoDate.value);">
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
<script>
	Calendar.setup({
	  inputField  : "txtfrDate",      // ID of the input field
	  ifFormat    : "%m/%d/%Y",          // the date format
	  button      : "imgtxtfrDate"       // ID of the button
	}
	)
	Calendar.setup({
	  inputField  : "txttoDate",      // ID of the input field
	  ifFormat    : "%m/%d/%Y",          // the date format
	  button      : "imgtxttoDate"       // ID of the button
	}
	)
	
</script>