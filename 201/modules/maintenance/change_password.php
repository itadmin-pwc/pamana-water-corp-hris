<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_employee.Obj.php");

$changePassObj = new changePassObj($_GET,$_SESSION);
$changePassObj->validateSessions('','MODULES');
if(isset($_GET['btnChngPass']) == "UPDATE"){
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
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css">
	</HEAD>
	<BODY>
		<FORM name='frmChangePass' id="frmChangePass" action="<?=$_SERVER['PHP_SELF']?>" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td class="parentGridHdr">
						&nbsp;
						CHANGE USER PASSWORD
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
					
						<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
							<tr>
								<td class="gridDtlLbl2" align="left" width="200">
									<font class="gridDtlLblTxt">Current Password</font>
								</td>
								<td class="gridDtlVal" width="250">
									<font class="gridDtlLblTxt">
										<INPUT type="password" class="inputs w-95" name="txtOldPass" id="txtOldPass" tabindex="1">
									</font>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left">
									<font class="gridDtlLblTxt">New Password</font>
								</td>
								<td class="gridDtlVal">
									<font class="gridDtlLblTxt" >
										<INPUT type="password" class="inputs w-95" name="txtNewPass" id="txtNewPass" maxlength="12" tabindex="2">
									</font>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left">
									<font class="gridDtlLblTxt">Confirm New Password</font>
								</td>
								<td class="gridDtlVal">
									<font class="gridDtlLblTxt">
										<INPUT type="password" class="inputs w-95" name="txtCnfrmNewPass" id="txtCnfrmNewPass" maxlength="12" tabindex="3">
									</font>
								</td>
							</tr>	
							<tr>
								<td align="right" colspan="3" class="childGridFooter">
									<INPUT class="inputs" style="margin-right: 8px; width: 75px;" type="button" name="btnChngPass" id="btnChngPass" value="UPDATE" onClick="changePass()" tabindex="4" style="margin-right: 3px;">
									<!-- <INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';" tabindex="5"> -->
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
		alert('Current Password is Required');
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
		alert('New Password and Confirm New Password does not match!');
		$('txtNewPass').focus();
		return false;		
	}
	
	new Ajax.Request('<?=$_SERVER['PHP_SELF']?>',{
		method :'GET',
		parameters : $('frmChangePass').serialize(),
		onComplete : function (req){
			var resTxt = parseInt(req.responseText);
			if(resTxt == 1){
				alert('Current Password is incorrect');
				$('txtOldPass').focus();
			}
			if(resTxt == 2){
				alert('Password Successfully Changed');
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
			$('btnChngPass').value='...';
			$('btnChngPass').disabled=true;
		},
		onSuccess : function (){
			$('btnChngPass').value='UPDATE';
			$('btnChngPass').disabled=false;
			$('btnCancel').disabled=false;			
		}
	});
}
</SCRIPT>