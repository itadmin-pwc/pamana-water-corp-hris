<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("branch.obj.php");

$brnchObj = new branchObj($_GET,$_SESSION);
$brnchObj->validateSessions('','MODULES');

if($_GET['btnMaint'] == 'ADD'){
		if($brnchObj->checkBrnch() > 0){
			echo 1;//already exist
		}
		else{
			if($brnchObj->toTblBranch() == true){
				echo 2;//successfully saved
			}else{
				echo 3;//saving failed
			}
		}
	exit();
}
if($_GET['btnMaint'] == 'EDIT'){
	if($brnchObj->updateBranch() == true){
		echo 4;//successfully updated
	}
	else{
		echo 5;//updating failed
	}
	exit();
}

if($_GET['action'] == 'EDIT'){
	$disabled = 'readOnly';
	$arrBrnch = $brnchObj->getBranch();
	$code = $arrBrnch['brnCode'];
  
	$Desc = $arrBrnch['brnDesc'];
	$shrtDesc = $arrBrnch['brnShortDesc'];
	$add1 = $arrBrnch['brnAddr1'];
	$add2 = $arrBrnch['brnAddr2'];
	$add3 = $arrBrnch['brnAddr3'];
	$minWage = $arrBrnch['minWage'];
	$signatory = $arrBrnch['brnSignatory'];
	$sgnTitle = $arrBrnch['brnSignTitle'];
	$brnGrp = $arrBrnch['brnDefGrp'];
	$cmbGLStore = $arrBrnch['glCodeStr'];
	$brnLoc = $arrBrnch['brnLoc'];
	$ecolaAmnt = $arrBrnch['ecola'];
	$brnStat = $arrBrnch['brnStat'];
	$coCtr = $arrBrnch['coCtr'];
	$cmbGrp = $arrBrnch['GrpCode'];
	$coesignatory = $arrBrnch['coeSignatory'];
	$coesignatorytitle = $arrBrnch['coeSignatoryTitle'];
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
		<FORM name="frmBrnch" id="frmBrnch" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td class="gridDtlLbl" align="left" width="30%">
							Code						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="brnCode" id="brnCode" class="inputs" value="<?=$code?>" <?=$disabled?>>						</td>
					</tr>
					<!-- <tr>
					  <td class="gridDtlLbl" align="left" >Group</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><?
								$brnchObj->DropDownMenu($brnchObj->makeArr(
										$brnchObj->getBrnGrp(),'GrpCode','GrpDesc',''
									),
									'cmbGrp',$cmbGrp,'class="inputs"' 
								);
							?></td>
				  </tr> -->
					<tr>
						<td class="gridDtlLbl" align="left" >
							Description						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="Desc" id="Desc" class="inputs" size="50" value="<?=htmlspecialchars($Desc)?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Short Description						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="shrtDesc" id="shrtDesc" class="inputs" size="50" value="<?=htmlspecialchars($shrtDesc)?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Address 1						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="add1" id="add1" class="inputs" size="50" value="<?=htmlspecialchars($add1)?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Address 2						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="add2" id="add2" class="inputs" size="50" value="<?=htmlspecialchars($add2)?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Address 3						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="add3" id="add3" class="inputs" size="50" value="<?=htmlspecialchars($add3)?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" width="30%">
							Minimum Wage						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="minWage" id="minWage" class="inputs" value="<?=$minWage?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" width="30%">
							Signatory						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT name="signatory" type="text" class="inputs" id="signatory" value="<?=htmlspecialchars($signatory)?>" size="40">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" width="30%">
							Sign Title						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT name="sgnTitle" type="text" class="inputs" id="sgnTitle" value="<?=htmlspecialchars($sgnTitle)?>" size="40">						</td>
					</tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >COE Signatory</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><INPUT name="coesignatory" type="text" class="inputs" id="coesignatory" value="<?=htmlspecialchars($coesignatory)?>" size="40"></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >COE Signatory Title</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><INPUT name="coesignatorytitle" type="text" class="inputs" id="coesignatorytitle" value="<?=htmlspecialchars($coesignatorytitle)?>" size="40"></td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Pay Group						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$brnchObj->DropDownMenu(array('1'=>'GROUP 1'),'brnGrp',$brnGrp,'class="inputs" ');
							?>						</td>
					</tr>
                    <?
                    if($_GET['action'] == 'EDIT'){
					?>
					<tr>
						<td class="gridDtlLbl" align="left" height="20">
							GL Code Store						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">&nbsp;<label class="inputs"><?=$cmbGLStore;?></label></td>
					</tr>
					<?
					}
					?>
                    <tr>
						<td class="gridDtlLbl" align="left" >
							Location						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$brnchObj->DropDownMenu(array('ST'=>'BRANCH','HO'=>'HEAD OFFICE'),'brnLoc',$brnLoc,'class="inputs" ');
							?>						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" width="30%">
							ECOLA						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="ecolaAmnt" id="ecolaAmnt" class="inputs" value="<?=$ecolaAmnt?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Status						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$brnchObj->DropDownMenu(array('A'=>'OPEN','D'=>'DELETED'),'brnStat',$brnStat,'class="inputs" ');
							?>						</td>
					</tr>
					<!-- <tr>
						<td class="gridDtlLbl" align="left" width="30%">
							Company Counter						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="coCtr" id="coCtr" class="inputs" value="<?=$coCtr?>" size="5"> <FONT color="red">AUB Purpose Only</font>						</td>
					</tr> -->
                    <?php if($_SESSION['user_level']==1 || $_SESSION['user_level']==2){ ?>
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
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateBranch(this.value);">						</td>
					</tr>
                    <?php } ?>
				</TABLE>
		  <INPUT type="hidden" name="hdnBrnCode" id="hdnBrnCode" value="<?=$code?>">
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function validateBranch(act){
		
	
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2}|-[\d]+|-[\d]+\.[\d]{1,2})$/;
		numExp = /^[\d]+$/;
		
		frm = $('frmBrnch').serialize(true);
		
		if(trim(frm['brnCode']) == ''){
			alert("branch Code is Required");
			$('brnCode').focus();
			return false;
		}
		if(!trim(frm['brnCode']).match(numExp)){
			alert("INVALID branch Code Numbers Only");
			$('brnCode').value='';
			$('brnCode').focus();
			return false;			
		}
		if(trim(frm['Desc']) == ''){
			alert("Description is Required");
			$('Desc').focus();
			return false;
		}		
		if(trim(frm['shrtDesc']) == ''){
			alert("Short Description is Required");
			$('shrtDesc').focus();
			return false;
		}
		if(trim(frm['add1']) == ''){
			alert("Address 1 is Required");
			$('add1').focus();
			return false;
		}
		if(trim(frm['minWage']) == ''){
			alert("Minimum Wage is Required");
			$('minWage').focus();
			return false;
		}
		if(!trim(frm['minWage']).match(numericExpWdec)){
			alert("INVALID Minimum Wage Numbers Only with or without 2 decimal\nExample : 123 or 123.45");
			$('minWage').focus();
			return false;			
		}
		if(trim(frm['brnGrp']) == 0){
			alert("Group is Required");
			$('brnGrp').focus();
			return false;
		}
		if(trim(frm['brnLoc']) == 0){
			alert("Location is Required");
			$('brnLoc').focus();
			return false;
		}
		if(trim(frm['ecolaAmnt']) == ""){
			alert("ECOLA is Required");
			$('ecolaAmnt').focus();
			return false;				
		}
	
		if(trim(frm['ecolaAmnt']) != ""){
			if(!trim(frm['ecolaAmnt']).match(numericExpWdec)){
				alert("INVALID ECOLA Numbers Only with or without 2 decimal\nExample : 123 or 123.45");
				$('ecolaAmnt').focus();
				return false;			
			}
		}
		if(trim(frm['coCtr']) != ''){
			if(!trim(frm['coCtr']).match(numExp)){
				alert("INVALID Company Counter Numbers Only");
				$('brnCode').value='';
				$('coCtr').focus();
				return false;			
			}
		}
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>',{
			method : 'get',
			parameters : $('frmBrnch').serialize(),
			onComplete : function(req){
				var resTxt = parseInt(req.responseText);
				switch(resTxt){
					case 1: alert('Branch Already Exist'); break;
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