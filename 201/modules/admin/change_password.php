<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_employee.Obj.php");

$changePassObj = new changePassObj($_GET,$_SESSION);
$changePassObj->validateSessions('','MODULES');
if(isset($_GET['btnChngPass']) == 'SUBMIT'){
	if($changePassObj->checkOldPass() == 0){
		echo 1;//invalid password for current user
	}
	else{
		if($changePassObj->changePass() == true){
			echo 2;//successfully changed
		}
		else{
			echo 3;//changing password failed
		}
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
		<FORM name='frmChangePass' id="frmChangePass" action="<?=$_SERVER['PHP_SELF']?>" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Change User Password
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
					
						<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
							<tr>
								<td class="gridDtlLbl2" align="left" width="200">
									<font class="gridDtlLblTxt">Old Password</font>
								</td>
								<td width="2%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal" width="250">
									<font class="gridDtlLblTxt">
										<INPUT type="password" class="inputs" name="txtOldPass" id="txtOldPass" tabindex="1">
									</font>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left">
									<font class="gridDtlLblTxt">New Password</font>
								</td>
								<td class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal" >
									<font class="gridDtlLblTxt" >
										<INPUT type="password" class="inputs" name="txtNewPass" id="txtNewPass" maxlength="12" tabindex="2">
									 	<img src="../../../images/help2.png" title="12 Characters Only" style="position:relative;top:2px;">
									</font>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left">
									<font class="gridDtlLblTxt">Confirm New Password</font>
								</td>
								<td class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal">
									<font class="gridDtlLblTxt">
										<INPUT type="password" class="inputs" name="txtCnfrmNewPass" id="txtCnfrmNewPass" maxlength="12" tabindex="3">
									</font>
								</td>
							</tr>	
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									<INPUT class="inputs" type="button" name="btnChngPass" id="btnChngPass" value="SUBMIT" onclick="changePass()" tabindex="4">
									<INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onclick="parent.document.getElementById('contentFrame').src='';" tabindex="5">
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
$('txtOldPass').focus();

function changePass(){
	
	var frm = $('frmChangePass').serialize(true);
	
	if(trim(frm['txtOldPass']) == ''){
		alert('Old Password is Required');
		$('txtOldPass').focus();
		return false;
	}
	if(trim(frm['txtNewPass']) == ''){
		alert('New Password is Required');
		$('txtNewPass').focus();
		return false;
	}
	if(trim(frm['txtCnfrmNewPass']) == ''){
		alert('Confirm New Password is Required');
		$('txtCnfrmNewPass').focus();
		return false;
	}
	if(trim(frm['txtNewPass']) != trim(frm['txtCnfrmNewPass'])){
		alert('New Password does not match to confirm new Password');
		$('txtNewPass').focus();
		return false;		
	}
	
	new Ajax.Request('<?=$_SERVER['PHP_SELF']?>',{
		method :'get',
		parameters : $('frmChangePass').serialize(),
		onComplete : function (req){
			var resTxt = parseInt(req.responseText);
			if(resTxt == 1){
				alert('Invalid Password for this user');
				$('txtOldPass').focus();
			}
			if(resTxt == 2){
				alert('Password Successfully Chnaged');
				$('txtOldPass').value='';
				$('txtNewPass').value='';
				$('txtCnfrmNewPass').value='';
				$('txtOldPass').focus();
			}
			if(resTxt == 3){
				alert('Changing Password Failed');
			}
		},
		onCreate : function (){
			$('btnChngPass').value='Loading...';
			$('btnChngPass').value='';
			$('btnChngPass').disabled=true;
			$('btnCancel').disabled=true;
		},
		onSuccess : function (){
			$('btnChngPass').value='SUBMIT';
			$('btnChngPass').disabled=false;
			$('btnCancel').disabled=false;			
		}
	});
}
</SCRIPT>