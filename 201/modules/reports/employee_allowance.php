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

switch ($_GET['action']){
	case('printEmployeeAllowance'):
		$checkallowance = $inqTSObj->getEmployeeAllowance("Where tblAllowance.allowCode='".$_GET['allowance']."' and tblBranch.brnCode='".$_GET['brncode']."' and brnCode in(Select brnCode from tblUserBranch where empNo='".$_SESSION['employee_number']."') and tblAllowance.allowStat='A'
ORDER BY tblEmpMast.empLastName, tblEmpMast.empFirstName, tblAllowance.empNo");
		if($checkallowance){
			$branch = $_GET['brncode'];
			$allowance = $_GET['allowance'];
 			echo "location.href='employee_allowance_pdf.php?branch={$branch}&allowance={$allowance}';";	
		}
		else{
			echo "alert('No Record Found!');";	
		}
	exit();
	break;	
}
?>
<HTML>
	<HEAD>
<TITLE><?=SYS_TITLE?></TITLE>
<style>@import url('../../style/reports.css');</style>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
<script>
//function exportExcel () {
//	if (document.frmTS.branch.value>0) {			
//		document.frmTS.action = "manpower_pdf.php";
//		document.frmTS.target = "_blank";
//		document.frmTS.submit();
//		document.frmTS.action = "manpower.php";
//		document.frmTS.target = "_self";
//	} else {
//		alert('branch is required!');
//		return false;
//	}
//}

function setObjects(id){
	if(id==1){
		$('cmbBranch').disabled=false;
		$('cmbAllowance').disabled=false;
		$('btnPrint').disabled=false;
		$('cmbBranch').focus();
	}
	else{
		$('cmbBranch').value=0;
		$('cmbAllowance').value=0;
		$('cmbBranch').disabled=true;
		$('cmbAllowance').disabled=true;
		$('btnPrint').disabled=true;
	}
}

function printEmployeeAllowance() {
	var brn = document.frmTS.cmbBranch.value;
	var allowance = document.frmTS.cmbAllowance.value; 
	if(brn==0){
		alert('Branch is required! Please select branch.');
		$('cmbBranch').focus();
		return false;	
	}
	else if(allowance==0){
		alert('Allowance is required! Please select allowance.');
		$('cmbAllowance').focus();
		return false;	
	}
	else{
		new Ajax.Request('<?php echo $_SERVER['PHP_SELF']?>?action=printEmployeeAllowance&brncode='+brn+'&allowance='+allowance,
		{
			method : 'get',
			onCreate : function(){
				$('btnPrint').disabled=true;	
			},
			onComplete : function(req){
				eval(req.responseText);
				$('btnPrint').disabled=false;		
			}	
				
		})
	}
		
}
</script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Employee's Allowance Report</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"><label><input type="radio" id="rdoInquire" name="select" value="1" onClick="setObjects(this.value);"/>Inquire</label>&nbsp;&nbsp;<label><input type="radio" id="rdoRefresh" name="select" value="0"/ onClick="setObjects(this.value);">Refresh</label></td>
          </tr>
          

          <tr>
            <td width="18%" class="gridDtlLbl">Branch</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 	
			$sqlBranch = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' and brnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}') order by brnDesc";				
			$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($sqlBranch));
								$arrBranch = $inqTSObj->makeArr($arrBranch,'brnCode','brnDesc','');
								$inqTSObj->DropDownMenu($arrBranch,'cmbBranch','','disabled');
							?>            </td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Allowance Type</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 	
			$sqlAllowance = "Select * from tblAllowType where compCode='{$_SESSION['company_code']}' order by allowDesc";				
			$arrAllowance = $inqTSObj->getArrRes($inqTSObj->execQry($sqlAllowance));
								$arrAllowance = $inqTSObj->makeArr($arrAllowance,'allowCode','allowDesc','');
								$inqTSObj->DropDownMenu($arrAllowance,'cmbAllowance','','disabled');
							?>            </td>
            </tr>            
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="btnPrint" id="btnPrint" value="Print Employee Allowance" onClick="printEmployeeAllowance();" disabled>
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
