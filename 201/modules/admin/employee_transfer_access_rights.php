<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("module_access_right.obj.php");
$common=new commonObj();
$common->validateSessions('','MODULES');
$empTranAccess=new moduleAccessRightsObj();


if($_GET['btnChnangeComp']=='SUBMIT'){
	if($_GET['cmbCompany']==$_SESSION['company_code']){
		echo "alert('You selected the same company! Unable to process.');";	
	}
	else{
		if($_GET['cmbCompany']==1){
			$comp="PGJR_PAYROLL";	
		}
		elseif($_GET['cmbCompany']==2){
			$comp="PG_PAYROLL";	
		}
		elseif($_GET['cmbCompany']==3){
			$comp="LUSITANO";	
		}
		elseif($_GET['cmbCompany']==4){
			$comp="DFCLARK_PAYROLL";	
		}
		elseif($_GET['cmbCompany']==5){
			$comp="DFSUBIC_PAYROLL";	
		}
		elseif($_GET['cmbCompany']==7){
			$comp="PARCO_GANT_DIAMOND";	
		}
		elseif($_GET['cmbCompany']==8){
			$comp="PARCO_GANT_D3";	
		}
		elseif($_GET['cmbCompany']==9){
			$comp="PARCO_SUPER_RETAIL_XV";	
		}
		elseif($_GET['cmbCompany']==10){
			$comp="PARCO_SUPER_AGORA";	
		}
		elseif($_GET['cmbCompany']==11){
			$comp="PARCO_SUPER_RETAIL_VII";	
		}
		elseif($_GET['cmbCompany']==12){
			$comp="PARCO_SCV";	
		}
		elseif($_GET['cmbCompany']==13){
			$comp="PG_SUBIC";	
		}
		elseif($_GET['cmbCompany']==15){
			$comp="COMPANY_E_CORPORATION";	
		}

		$qry="Select empNo,compCode from ".$comp."..tblUsers where empNo='".$_GET['empNo']."' and compCode='".$_GET['cmbCompany']."'";		
		$qryCheck=$empTranAccess->recordChecker($qry);
		if($qryCheck){
			echo "alert('Record already exist to selected company!');";
			exit();	
		}
		else{
			$prosTransAccess=$empTranAccess->processTransfer($_GET['compCode'],$_GET['empNo'],$_GET['cmbCompany']);			
			if($prosTransAccess){
				echo "alert('Transaction has been processed successfully.');";	
				exit();
			}
			else{
				echo "alert('Failed to process transaction!');";	
				exit();
			}
		}
	}
	exit();
}
?>
<html>
<head>
	<title><?=SYS_TITLE?></title>
	<script type="text/javascript" src="../../../includes/prototype.js"></script>
    <script type="text/javascript" src="../../../includes/jSLib.js"></script>
    <style>@import url('../../style/payroll.css');</style>
    <style>@import url('../../style/default.css');</style>
</head>

<body>
<form id="frmAccessRights" name="frmAccessRights" action="">
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" width="45%" >
				<tr>
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Create access rights to anther company
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
					
						<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
							<tr>
								<td class="gridDtlLbl2" align="left" width="29%">
									<font class="gridDtlLblTxt">Company</font>
								</td>
								<td width="7%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal" width="64%">
									<font class="gridDtlLblTxt">
										<? $common->DropDownMenu($common->makeArr($common->getChangeCompany(''),'compCode','compName',''),'cmbCompany',$_SESSION['company_code'],'class="inputs" tabindex="1"');?>
									</font>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									<INPUT class="inputs" type="button" name="btnChnangeComp" id="btnChnangeComp" value="SUBMIT" onClick="genData('<?=$_GET['empNo']?>','<?=$_GET['compCode']?>');" tabindex="2">
									<INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';" tabindex="4">
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
</form>
</body>
</html>
<script>
function genData(empno,compcode){
	var empInputs=$('frmAccessRights').serialize(true);
	
	if(empInputs['cmbCompany']==0){
		alert('Company is required.');
		$('cmbCompany').focus();
		return false;	
	}	
	
	new Ajax.Request('employee_transfer_access_rights.php?empNo='+empno+'&compCode='+compcode,{
		method	: 'get',
		parameters	: $('frmAccessRights').serialize(),
		onComplete	: function (req){
			eval(req.responseText);
			},
		onCreate	: function (){
			$('btnCancel').disabled=true;
			$('btnChnangeComp').value='Loading';
			},
		onSuccess	: function (){
			$('btnCancel').disabled=false;
			$('btnChnangeComp').value='SUBMIT';
		}		
		});
}
</script>