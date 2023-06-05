<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_employee.Obj.php");

$maintEmpObj = new maintEmpObj();
$maintEmpObj->validateSessions('','MODULES');
if(isset($_GET['btnCreateAccts']) == 'SUBMIT'){
	if($maintEmpObj->validateStrCode($_GET['txtStrCode']) == 0){
		echo 1;//invalid password for current user
	}
	else{
		if($maintEmpObj->createAccts($_GET['txtStrCode'])){
			echo 2;//successfully created
		}
		else{
			echo 3;//error creating acct
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
		<FORM name='frmchart' id="frmchart" action="<?=$_SERVER['PHP_SELF']?>" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Create Chart of Accounts</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
					
						<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
							<tr>
								<td class="gridDtlLbl2" align="left" width="200">
									<font class="gridDtlLblTxt">Store Code</font>
								</td>
								<td width="2%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal" width="250">
									<font class="gridDtlLblTxt">
										<INPUT type="text" class="inputs" name="txtStrCode" id="txtStrCode" tabindex="1">
									</font>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									<INPUT class="inputs" type="button" name="btnCreateAccts" id="btnCreateAccts" value="SUBMIT" onClick="createChart()" tabindex="4">
									<INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';" tabindex="5">
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
$('txtStrCode').focus();

function createChart(){
	
	var frm = $('frmchart').serialize(true);
	
	if(trim(frm['txtStrCode']) == ''){
		alert('Store Code is Required');
		$('txtStrCode').focus();
		return false;
	}
	
	
	new Ajax.Request('<?=$_SERVER['PHP_SELF']?>',{
		method :'get',
		parameters : $('frmchart').serialize(),
		onComplete : function (req){
			var resTxt = parseInt(req.responseText);
			if(resTxt == 1){
				alert('Invalid Store Code');
				$('txtStrCode').focus();
			}
			if(resTxt == 2){
				alert('Accounts for this Store are Successfully Created');
				$('txtStrCode').value='';
				$('txtStrCode').focus();
			}
			if(resTxt == 3){
				alert('Error Creating Accounts');
			}
		},
		onCreate : function (){
			$('btnCreateAccts').value='Loading...';
			$('btnCreateAccts').value='';
			$('btnCreateAccts').disabled=true;
			$('btnCancel').disabled=true;
		},
		onSuccess : function (){
			$('btnCreateAccts').value='SUBMIT';
			$('btnCreateAccts').disabled=false;
			$('btnCancel').disabled=false;			
		}
	});
}
</SCRIPT>