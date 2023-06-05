<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_employee.Obj.php");
include("GL_account.obj.php");

$glAcctObj = new GLAcctObj($_GET,$_SESSION);
$glAcctObj->validateSessions('','MODULES');

if($_GET['btnMaint'] == 'EDIT'){
	$arrGLCheck = $glAcctObj->checkGLAcct();
	if(trim($arrGLCheck['acctCde']) == ""){
		if($glAcctObj->updtGLAccnt() == true){
			echo 1;//successfully updated
		}
		else{
			echo 2;//updating failed
		}
	}
	else{
		if(trim($arrGLCheck['acctCde']) == trim($_GET['hdnGlCod'])){
			if($glAcctObj->updtGLAccnt() == true){
				echo 1;//successfully updated
			}
			else{
				echo 2;//updating failed
			}			
		}
		else{
			echo 3;//GL Code Already Exist
		}
	}
	exit();
}

if($_GET['btnMaint'] == 'ADD'){
	$arrGLCheck = $glAcctObj->checkGLAcct();
	if(trim($arrGLCheck['acctCde']) == ""){
		if($glAcctObj->toGLAccnt() == true){
			echo 4;//successfully saved
		}
		else{
			echo 5;//saving failed
		}
	}
	else{
		echo 3;//GL Code Already Exist
	}
	exit();
}

if($_GET['action'] == 'EDIT'){
	
	$arrGLAcct = $glAcctObj->getGlAcct();
	$GlCode = $arrGLAcct['acctCde'];
	$GlDesc = $arrGLAcct['acctDesc'];
	$GLSDesc = $arrGLAcct['acctDescShrt']; 
	$GlStat = $arrGLAcct['acctStat']; 
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
		<FORM name="frmMaintEmpAllow" id="frmMaintEmpAllow" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td class="gridDtlLbl" align="left" width="30%">
							GL Code
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="GLCode" id="GLCode" class="inputs" maxlength="4" value="<?=$GlCode?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Description
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="GLDesc" id="GLDesc" class="inputs" size="50" value="<?=$GlDesc?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Short Description 
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="GLShrtDesc" id="GLShrtDesc" class="inputs" size="50" value="<?=$GLSDesc?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Status
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$glAcctObj->DropDownMenu(array('A'=>'OPEN','D'=>'DELETED'),'GlStat',$GlStat,'class="inputs" ');
							?>
						</td>
					</tr>
                    <?php if($_SESSION['user_level']==1){ ?>
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<input name="Reset" type="reset" class="inputs" id="button" value="Reset">
							<?
								if($_GET['action'] == 'EDIT'){
									$btnMaint = 'EDIT';
								}
								if($_GET['action'] == 'ADD'){
									$btnMaint = 'ADD';
								}
							?>
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateGLAcct(this.value,'<?=$_GET['glCode']?>','<?=$_GET['tbl']?>')">
						</td>
				  </tr>
                  <?php } ?>
				</TABLE>
				<INPUT type="hidden" name="hdnGlCod" id="hdnGlCod" value="<?=$_GET['glCode']?>">
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function validateGLAcct(act,glCode,tbl){
		frm = $('frmMaintEmpAllow').serialize(true);
		
		if(trim(frm['GLCode']) == ""){
			alert('Gl Code is Required');
			$('GLCode').focus();
			return false;
		}
		if(trim(frm['GLDesc']) == ""){
			alert('Gl Description is Required');
			$('GLDesc').focus();
			return false;
		}
		if(trim(frm['GLShrtDesc']) == ""){
			alert('Gl Short Description is Required');
			$('GLShrtDesc').focus();
			return false;
		}
		if(trim(frm['GlStat']) == 0){
			alert('Gl Status is Required');
			$('GlStat').focus();
			return false;
		}	
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?tbl='+tbl,{
			method : 'get',
			parameters : $('frmMaintEmpAllow').serialize(),
			onComplete : function(req){
				var resTxt = parseInt(req.responseText);
				switch(resTxt){
					case 1: alert('Succcessfully Updated'); break;
					case 2: alert('Updating Failed'); break;
					case 3: alert('GL Account Already Exist'); break;
					case 4: alert('Successfully Saved'); break;
					case 5: alert('Saving Failed'); break;
				}
			},
			onCreate : function(){
				$('btnMaint').disabled=true;
				$('btnMaint').value='Loading...';
			},
			onSuccess : function(){
				$('btnMaint').disabled=false;
				$('btnMaint').value=act;				
			}
			
		});
	}
</SCRIPT>