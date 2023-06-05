<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("module_access_right.obj.php");
$comObj=new commonObj();
$comObj->validateSessions('','MODULES');

$cPword = new moduleAccessRightsObj($_SESSION,$_GET);

if($_GET['Submit']!=""){
	$passwordChanged=$cPword->processPassword($_GET['compCode'],$_GET['empNo']);	
	if($passwordChanged){
		echo "alert('Password has been changed.');";	
	}
	else{
		echo "alert('Failed to change password.')";	
	}
	exit();
}
?>
<html>
	<head>
	<TITLE><?=SYS_TITLE?></TITLE>
	<script type="text/javascript" src="../../../includes/prototype.js"></script>
    <script type="text/javascript" src="../../../includes/jSLib.js"></script>
    <style>@import url('../../style/payroll.css');</style>
    </head>

<body>
<form id="frmChangePword" name="frmChangePword" action="">
	<table width="100%" border="0" cellpadding="1" cellspacing="1" class="childGrid">
    	<tr>
        	<td width="49%" class="gridDtlLbl">Enter New Password</td>
            <td width="1%" class="gridDtlLbl">:</td>
    		<td width="50%"><input type="password" maxlength="12" name="txtNewPword" id="txtNewPword" value="<?=$_POST['txtNewPword'];?>"/></td>
        </tr>
        <tr>
        	<td class="gridDtlLbl">Confirm New Password</td>
            <td class="gridDtlLbl">:</td>
            <td><input type="password" maxlength="12" name="txtConfirmNewPword" id="txtConfirmNewPword" value="<?=$_POST['txtConfirmNewPword'];?>"/></td>
        </tr>
        <tr><td colspan="3" height="5"></td></tr>
        <tr>
        	<td colspan="3" height="25" align="center" class="childGridFooter"><input type="reset" name="Reset" id="Reset" value="Reset"/>
       	    <input type="button" name="Submit" id="Submit" value="Submit" onClick="validateData('<?=$_GET['compCode']?>','<?=$_GET['empNo']?>')"></td>
        </tr>
    </table>
</form>
</body>
</html>
<script>
function validateData(compcode,empno){
	var empInputs=$('frmChangePword').serialize(true);
	
	if(trim(empInputs['txtNewPword'])==""){
		alert('New password is required.');
		$('txtNewPword').focus();
		return false;	
	}
	
	if(trim(empInputs['txtNewPword'])!=""){
		if(trim(empInputs['txtConfirmNewPword'])==""){
			alert('Confirm password is requried.');
			$('txtConfirmNewPword').focus();
			return false;
		}
	}
	
	if(trim(empInputs['txtNewPword']) !=  trim(empInputs['txtConfirmNewPword'])){
		alert('New Password does not match to Confirm New Password.')
		$('txtConfirmNewPword').focus();
		return false;
	}
	
	var params = 'employee_change_password_change.php?compCode='+compcode+'&empNo='+empno;
	new Ajax.Request(params,{
		method : 'get',
		parameters : $('frmChangePword').serialize(),
		onComplete : function (req){
			eval(req.responseText);			
		}	
	});
}
</script>