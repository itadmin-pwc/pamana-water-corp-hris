<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("department.obj.php");

$deptObj = new deptObj($_GET,$_SESSION);
$deptObj->validateSessions('','MODULES');

if($_GET['action'] == 'ADD'){
	$code =  $deptObj->getNextDeptCode();
}
if($_GET['action'] == 'EDIT'){
	$arrDept = $deptObj->getDept($_GET['divCode'],$_GET['deptCode']);
	$code = $arrDept['deptCode'];
	$Desc = $arrDept['deptDesc'];
	$shrtDesc = $arrDept['deptShortDesc'];
	$cmbGLMinor = $arrDept['deptGlCode'];
	$stat = $arrDept['deptStat'];
}

if($_GET['btnMaint'] == 'ADD'){
	if($deptObj->checkDept() > 0){
		echo 1;//already exist
	}
	else{
		if($deptObj->toDepartmentDept()){
			echo 2;//successfully saved
		}
		else{
			echo 3;//saving failed
		}
	}
	exit();
}
if($_GET['btnMaint'] == 'EDIT'){
	if($deptObj->updtDeptartmentDept() == true){
		echo 4;//successfully updated
	}
	else{
		echo 5;//updating failed
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
		<FORM name="frmMaintDept" id="frmMaintDept" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td class="gridDtlLbl" align="left" width="30%">
							Code						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="deptCode" id="deptCode" class="inputs" maxlength="4" value="<?=$code?>" readonly>						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Description						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="Desc" id="Desc" class="inputs" size="50" value="<?=$Desc?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Short Description						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="shrtDesc" id="shrtDesc" class="inputs" size="50" value="<?=$shrtDesc?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Minor GL Code						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
								$deptObj->DropDownMenu($deptObj->makeArr(
										$deptObj->getGLMinorList(),'acctCde','acctDesc',''
									),
									'cmbGLMinor',$cmbGLMinor,'class="inputs"' 
								);
							?>						</td>
					</tr>
					<tr>
                      <td class="gridDtlLbl" align="left" >Status</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><?$deptObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$stat,'class="inputs"'); ?></td>
				  </tr>
<!--					<tr>
						<td class="gridDtlLbl" align="left" >
							Status
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$deptObj->DropDownMenu(array('A'=>'OPEN','D'=>'DELETED'),'GlStat',$GlStat,'class="inputs" ');
							?>
						</td>
					</tr>-->
                    <?php if($_SESSION['user_level']==1){ ?>
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<?
								if($_GET['action'] == 'EDIT'){
									$btnMaint = 'EDIT';
								}
								if($_GET['action'] == 'ADD'){
									$btnMaint = 'ADD';
								}
							?>
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateDiv(this.value,'<?=$_GET['divCode']?>')">						</td>
					</tr>
                    <?php } ?>
				</TABLE>
		  <INPUT type="hidden" name="hdnGlCod" id="hdnGlCod" value="<?=$_GET['glCode']?>">
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function validateDiv(act,divCode){
		frm = $('frmMaintDept').serialize(true);
		if(trim(frm['Desc']) == ''){
			alert('Description is Required');
			$('Desc').focus();
			return false;
		}
		if(trim(frm['shrtDesc']) == ''){
			alert('Short Description is Required');
			$('shrtDesc').focus();
			return false;
		}
		if(trim(frm['cmbGLMinor']) == 0){
			alert('Gl Code is Required');
			$('cmbGLMinor').focus();
			return false;
		}
		if(trim(frm['cmbStat']) == 0){
			alert('Status is Required');
			$('cmbStat').focus();
			return false;
		}
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?divCode='+divCode,{
			method : 'get',
			parameters : $('frmMaintDept').serialize(),
			onComplete : function(req){
				var resTxt = parseInt(req.responseText);
				switch(resTxt){
					case 1: alert('Division Already Exist'); break;
					case 2: alert('Successfully Saved'); break;
					case 3: alert('Saving Failed'); break;
					case 4: alert('Successfully Updated'); break;
					case 5: alert('Updating Failed'); break;
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