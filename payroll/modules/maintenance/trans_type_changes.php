<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("trans_type.obj.php");

$trnsTypeObj = new trnsTypeObj($_GET,$_SESSION);
$trnsTypeObj->validateSessions('','MODULES');

if($_GET['btnMaint'] == 'ADD'){
		if($trnsTypeObj->checkTrnsType('A') > 0){
			echo 1;//already exist
		}
		else{
			if($trnsTypeObj->toTblTransType() == true){
				echo 2;//successfully saved
			}
			else{
				echo 3;//saving failed
			}
		}
	exit();
}
if($_GET['btnMaint'] == 'EDIT'){
	if($trnsTypeObj->checkTrnsType('E') > 0){
			echo 1;//already exist
	} else {
		if($trnsTypeObj->updateTrnsType() == true){
			echo 4;//successlly updated
		}
		else{
			echo 5;//updating failed
		}
	}	
	exit();
}
if($_GET['action'] == 'EDIT'){
	
	$arrPayTrans = $trnsTypeObj->getTrnsType();
	$code = $arrPayTrans['trnCode'];
	$Desc = $arrPayTrans['trnDesc'];
	$shrtDesc = $arrPayTrans['trnShortDesc'];
	$brnCat = $arrPayTrans['trnCat'];
	$trnApply  = $arrPayTrans['trnApply'];
	$recCode  = $arrPayTrans['trnRecode'];
	$cmbGLMajor = $arrPayTrans['trnGlCode'];
	$prior = $arrPayTrans['trnPriority'];
	$isEntry = $arrPayTrans['trnEntry'];
	$taxTag = $arrPayTrans['trnTaxCd'];
	$brnStat = $arrPayTrans['trnStat'];
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
		<FORM name="frmTrnsType" id="frmTrnsType" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td class="gridDtlLbl" align="left" width="30%">
							Code
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="trnCode" onKeyPress="return isNumberInputEmpNoOnly(this, event);" id="trnCode" class="inputs" value="<?=$code?>" <?=$disabled?> maxlength="4">
						<input name="hdtrnCode" type="hidden" id="hdtrnCode" value="<?=$code?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Description
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="Desc" id="Desc" class="inputs" size="50" value="<?=htmlspecialchars($Desc)?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Short Description 
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="shrtDesc" id="shrtDesc" class="inputs" size="50" value="<?=htmlspecialchars($shrtDesc)?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Category
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$trnsTypeObj->DropDownMenu(array('E'=>'EARNINGS','D'=>'DEDUCTIONS'),'brnCat',$brnCat,'class="inputs" onchange="validateTrnCat(this.value)"');
							?>
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Apply
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$trnsTypeObj->DropDownMenu(array('1'=>'1ST PERIOD','2'=>'2ND PERIOD','3'=>'BOTH'),'trnApply',$trnApply,'class="inputs" ');
							?>
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Re-Code
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="recCode" onKeyPress="return isNumberInputEmpNoOnly(this, event);" id="recCode" class="inputs" value="<?=$recCode?>" maxlength="4">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							GL Code Major
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
								$trnsTypeObj->DropDownMenu($trnsTypeObj->makeArr(
										$trnsTypeObj->getGLMajorList(),'acctCde','acctDesc',''
									),
									'cmbGLMajor',$cmbGLMajor,'class="inputs"' 
								);
							?>
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Priority
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
								if($_GET['action'] == 'ADD'){
									$disabledPrior = 'disabled';
								}
								if($_GET['action'] == 'EDIT'){
									if($brnCat == 'E'){
										$disabledPrior = 'disabled';
									}
								}
							?>
							<INPUT type="text" name="prior" id="prior" class="inputs" value="<?=$prior?>" <?=$disabledPrior?> size="5"  maxlength="4">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							is Entry ?
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$trnsTypeObj->DropDownMenu(array('Y'=>'YES','N'=>'NO'),'isEntry',$isEntry,'class="inputs"');
							?>
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Tax Tag
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$trnsTypeObj->DropDownMenu(array('Y'=>'YES','N'=>'NO'),'taxTag',$taxTag,'class="inputs"');
							?>
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Status
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$trnsTypeObj->DropDownMenu(array('A'=>'OPEN','D'=>'DELETED'),'brnStat',$brnStat,'class="inputs" ');
							?>
						</td>
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
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateTrnsType(this.value)">
						</td>
					</tr>
                    <?php } ?>
				</TABLE>
				<INPUT type="hidden" name="hdnBrnCode" id="hdnBrnCode" value="<?=$code?>">
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function validateTrnCat(trnCat){
		if(trnCat == 'E'){
			$('prior').disabled=true;
			$('prior').value='';
		}else{
			$('prior').disabled=false;
		}
	}
	
	function validateTrnsType(act){
		
	
		frm = $('frmTrnsType').serialize(true);
		numExp = /^[\d]+$/;
		
		if(trim(frm['trnCode']) == ''){
			alert('Code is Required');
			$('trnCode').focus();
			return false;
		}
		if(!trim(frm['trnCode']).match(numExp)){
			alert('INVALID Code Numbers Only');
			$('trnCode').focus();
			return false;			
		}
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
		if(trim(frm['recCode']) == ''){
			alert('Re-Code is Required');
			$('recCode').focus();
			return false;
		}
		if(!trim(frm['recCode']).match(numExp)){
			alert('INVALID Re-Code Numbers Only');
			$('recCode').focus();
			return false;			
		}
		// if(trim(frm['cmbGLMajor']) == 0){
		// 	alert('Major GL Code is Required');
		// 	$('cmbGLMajor').focus();
		// 	return false;
		// }
		if(trim(frm['brnCat']) == 'D'){
			if(trim(frm['prior']) == ''){
				alert('Priority is Required');
				$('prior').focus();
				return false;
			}
			if(!trim(frm['prior']).match(numExp)){
				alert('INVALID Priority Numbers Only');
				$('prior').focus();
				return false;			
			}
		}
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>',{
			method : 'get',
			parameters : $('frmTrnsType').serialize(),
			onComplete : function(req){
				var resTxt = parseInt(req.responseText);
				
				switch(resTxt){
					case 1: alert('Code Already Exist'); break;
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
	function isNumberInputEmpNoOnly(field, event) {
	  var key, keyChar;
	
	  if (window.event)
		key = window.event.keyCode;
	  else if (event)
		key = event.which;
	  else
		return true;
	  // Check for special characters like backspace
	  if (key == null || key == 0 || key == 8 || key == 27 || key == 13) {
		return true;
	  }
	  // Check to see if it's a number
	  keyChar =  String.fromCharCode(key);
	  if (/\d/.test(keyChar)) 
		{
		 window.status = "";
		 return true;
		} 
	  else 
	   {
		window.status = "Field accepts numbers only.";
		return false;
	   }
	}	
</SCRIPT>