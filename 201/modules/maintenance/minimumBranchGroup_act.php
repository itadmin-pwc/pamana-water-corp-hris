<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("minimumGroup.obj.php");

$deptObj = new deptObj($_GET,$_SESSION);
$deptObj->validateSessions('','MODULES');

if($_GET['action'] == 'EDIT'){
	$arr = $deptObj->getBranchGroup($_GET['branchMinimumGroupID']);
	$Groupname = $arr['minGroupID'];
	$BranchCode = $arr['brnCode'];
}

if($_GET['btnMaint'] == 'ADD'){
//	if($deptObj->checkDept() > 0){
//		echo 1;//already exist
//	}
//	else{
		if($deptObj->toBranchGroup()){
			echo 2;//successfully saved
		}
		else{
			echo 3;//saving failed
		}
//	}
	exit();
}
if($_GET['btnMaint'] == 'EDIT'){
	if($deptObj->updtBranchGroup() == true){
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
						<td class="gridDtlLbl" align="left" width="30%">Group	Name					</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
						<?
                        $deptObj->DropDownMenu($deptObj->makeArr($deptObj->getBranchGroupName($_SESSION['company_code']),'minGroupID','minGroupName',''),'cmbGroupName',$Groupname,'class="inputs"');
                        ?>
                        </td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >Branch						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><?
                        $deptObj->DropDownMenu($deptObj->makeArr($deptObj->getBranch($_SESSION['company_code']),'brnCode','brnDesc',''),'cmbBranch',$BranchCode,'class="inputs"');
                        ?></td>
					</tr>
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
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateDiv(this.value,'<?=$_GET['branchMinimumGroupID']?>')">						</td>
					</tr>
                    <?php } ?>
				</TABLE>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function validateDiv(act,branchMinimumGroupID){
		frm = $('frmMaintDept').serialize(true);
		if(trim(frm['cmbGroupName']) == 0){
			alert('Group Name is Required');
			$('cmbGroupName').focus();
			return false;
		}
		if(trim(frm['cmbBranch']) == 0){
			alert('Branch is Required');
			$('cmbBranch').focus();
			return false;
		}
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?branchMinimumGroupID='+branchMinimumGroupID,{
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