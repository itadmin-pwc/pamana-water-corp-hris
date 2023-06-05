<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("module_access_right.obj.php");

$getPassObj = new getPassObj($_SESSION,$_GET);
$getPassObj->validateSessions('','MODULES');

if(isset($_GET['btnGetPass']) == 'SUBMIT'){

	if($getPassObj->getUserPass() != ""){
		echo base64_decode($getPassObj->getUserPass());
	}
	else{
		echo "";
	}
	exit();
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
	</HEAD>
	<BODY>
		<FORM name='frmGetPass' id="frmGetPass" action="<?=$_SERVER['PHP_SELF']?>" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" width="50%">
				<tr>
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Forgot Password
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
					
						<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
							<tr>
								<td class="gridDtlLbl2" align="left" width="30%">
									<font class="gridDtlLblTxt">Company</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal" width="50%">
									<font class="gridDtlLblTxt">
										<?$getPassObj->DropDownMenu($getPassObj->makeArr($getPassObj->getCompany(''),'compCode','compName',''),'cmbCompny','','class="inputs" tabindex="1"');?>
									</font>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left">
									<font class="gridDtlLblTxt">Employee Number</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal">
									<font class="gridDtlLblTxt" >
										<INPUT type="text" class="inputs" name="txtEmpNo" id="txtEmpNo" tabindex="2">
									</font>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left">
									<font class="gridDtlLblTxt">Password</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal">
									<font class="gridDtlLblTxt">
										<INPUT type="text" class="inputsRO" name="txtPass" id="txtPass" readonly >
									</font>
								</td>
							</tr>	
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									<INPUT class="inputs" type="button" name="btnGetPass" id="btnGetPass" value="SUBMIT" onclick="getPass()" tabindex="3">
									<INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onclick="parent.document.getElementById('contentFrame').src='';" tabindex="4">
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
$('cmbCompny').focus();

function getPass(){
	
	var frm = $('frmGetPass').serialize(true);
	var numericExp = /[0-9]+/;
	
	if(trim(frm['cmbCompny']) == 0){
		alert('Company is Required');
		$('cmbCompny').focus();
		return false;
	}
	if(trim(frm['txtEmpNo']) == ''){
		alert('Employee Number is Required');
		$('txtEmpNo').focus();
		return false;
	}
	if(!trim(frm['txtEmpNo']).match(numericExp)){
		alert('Invalid Employee Numbers\nValid : Numbers Only');
		$('txtEmpNo').focus();
		return false;
	}
	
	new Ajax.Request('<?=$_SERVER['PHP_SELF']?>',{
		method :'get',
		parameters : $('frmGetPass').serialize(),
		onComplete : function (req){
			var resTxt = req.responseText;
			if(trim(resTxt) != ""){
				$('txtPass').value=trim(resTxt);
			}
			else{
				alert('No Record Found');
			}
		},
		onCreate : function (){
			$('btnGetPass').value='Loading...';
			$('btnGetPass').disabled=true;
			$('btnCancel').disabled=true;
		},
		onSuccess : function (){
			$('btnGetPass').value='SUBMIT';
			$('btnGetPass').disabled=false;
			$('btnCancel').disabled=false;			
		}
	});
}
</SCRIPT>