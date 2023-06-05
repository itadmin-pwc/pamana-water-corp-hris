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
	case('printTelCo'):
		if($_GET['telco']==1){
			$telco = "emp.empGlobeLine='Y'";	
		}
		if($_GET['telco']==2){
			$telco = "emp.empSunLine='Y'";
		}
		if($_GET['telco']==3){
			$telco = "emp.empSmartLine='Y'";
		}
		$checktelco = $inqTSObj->getTelCo("Where $telco and emp.empBrnCode in(Select brnCode from tblUserBranch where empNo='".$_SESSION['employee_number']."') ORDER BY emp.empLastName, emp.empFirstName");
		if($checktelco){
			$telco = $_GET['telco'];
 			echo "location.href='telephone_line_pdf.php?telco={$telco}';";	
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
function setObjects(id){
	if(id==1){
		$('cmbTelCo').disabled=false;
		$('btnPrint').disabled=false;
	}
	else{
		$('cmbTelCo').value=0;
		$('cmbTelCo').disabled=true;
		$('btnPrint').disabled=true;
	}
}

function printReport() {
	var telco = document.frmTS.cmbTelCo.value;
	if(telco==0){
		alert('Telephone Company is required! Please select telephone company.');
		$('telco').focus();
		return false;	
	}
	else{
		new Ajax.Request('<?php echo $_SERVER['PHP_SELF']?>?action=printTelCo&telco='+telco,
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
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Company Issued Mobile Line Report</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"><label><input type="radio" id="rdoInquire" name="select" value="1" onClick="setObjects(this.value);"/>Inquire</label>&nbsp;&nbsp;<label><input type="radio" id="rdoRefresh" name="select" value="0"/ onClick="setObjects(this.value);">Refresh</label></td>
          </tr>
          

          <tr>
            <td width="18%" class="gridDtlLbl">Mobile Phone Line</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 	
				$arrTelCo = array("","Globe","Sun","Smart");
				$inqTSObj->DropDownMenu($arrTelCo,'cmbTelCo','','disabled');
			?>            </td>
            </tr>            
          <tr>
            <td width="18%" height="5" colspan="3"></td>
            </tr>            

        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" name="btnPrint" id="btnPrint" value="Print Report" onClick="printReport();" disabled>
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
