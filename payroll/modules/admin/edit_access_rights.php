<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("module_access_right.obj.php");

$modAccRghrsObj = new moduleAccessRightsObj($_SESSION,$_GET);

$modAccRghrsObj->validateSessions('','MODULES');
$sessionVars = $modAccRghrsObj->getSeesionVars();

switch ($_GET['action']){
	case 'SAVE':
		
			if($modAccRghrsObj->updateUserLogInInfo() == true){
				echo 1;
			}
			else{
				echo 2;
			}
		exit();
	break;
}

$userInfo = $modAccRghrsObj->getUserInfo($_GET['compCode'],$_GET['empNo'],'');
$userLogInInfo = $modAccRghrsObj->getUserLogInInfoForMenu($_GET['empNo']);
?>
<?
$userLogInInfo['pagesPayroll'] = $userLogInInfo['pages1'].$userLogInInfo['pages2'];?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>  
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
	</HEAD>
	<BODY>
		<FORM name='frmAccessRights' id="frmAccessRights" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td colspan="4" class="parentGridHdr">
						&nbsp;<img src="../../../images/grid.png">&nbsp;<?=ucwords($_GET['transType'])?> Module Access Rights (<?=$userInfo['empFirstName'] . " " . $userInfo['empMidName'] . " " . $userInfo['empLastName']?>)
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
					<?
						$j=0;
						$i=0;
						foreach ($modAccRghrsObj->getParentModule() as $moduleNameVal){
					?>
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
							<tr>
								<td colspan="2" class="gridToolbar" align="center">
									<a href="#" name="moduleName<?=$i?>" class="anchor" title="View Module List">
										<?=strtoupper($moduleNameVal)?> 
									</a>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="gridModuleList" id="gridModuleList<?=$i?>">
									<?
										$ctr=0;
											echo "<FONT class='ToolBarseparator'>|</font>";
										foreach ($modAccRghrsObj->getParentModule() as $moduleListVal){
											echo "<a href='#moduleName$ctr' class='anchor'>".$moduleListVal."</a>";
											echo "<FONT class='ToolBarseparator'>|</font>";
											$ctr++;
										}
									?>
								</td>
							</tr>
							<?
								$j=$j+0;
								$a=0;
								$modAccRghrsObj->getChildModule($moduleNameVal);
								foreach ($modAccRghrsObj->getChildModule($moduleNameVal) as $childModuleVal){
									
								$bgcolor = ($j%2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
							?>
                            
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal" width="5">
                                
									<INPUT type="checkbox" id="childModule<?=$j?>" name="childModule" value="<?=$childModuleVal['moduleId']?>" <?
											if(isset($_GET['transType']) == 'edit'){
												$getPagesPayRoll = $modAccRghrsObj->getPagesPayroll($userLogInInfo['pagesPayroll']);

												//for($o=0;$o<=sizeof($getPagesPayRoll)-1;$o++){
													if (in_array($childModuleVal['moduleId'],$getPagesPayRoll)) {
													//if((int)$childModuleVal['moduleId'] == (int)$getPagesPayRoll[$o]){
														echo " checked";
													}
												//}
											}
									?>>
								</td>
								<td class="gridDtlVal" ><font class="gridDtlLblTxt"><?=$childModuleVal['label']?></FONT></td>
							</tr>
							<?
								$a++;
								$j++;
								}
							?>
						</TABLE>
						<INPUT class="inputs" type="button" value="Check All" onClick="return checkAll(<?=$i?>,<?=$j?>,<?=$a?>);">
						<INPUT class="inputs" type="button" value="Uncheck All" onClick="return unCheckAll(<?=$i?>,<?=$j?>,<?=$a?>);">
						<br>
					<?
						$i++;
						}
					?>
					</td>
				</tr>
				<tr>
					<td colspan="4" class="childGridFooter">
						<INPUT class="inputs" type="button" value="Over All Check" align="left" onClick="overAllCheck(<?=$j?>)">
						<INPUT class="inputs" type="button" value="Over All Un Check" align="left" onClick="overAllUnCheck(<?=$j?>)">
						<INPUT align="absmiddle" type="button" name="btnSave" id="btnSave" value="SAVE" class="inputs" onClick="saveModuleAccess('<?=$_GET['compCode']?>','<?=$_GET['empNo']?>')">
						<INPUT align="absmiddle" type="button" name="btnBack" id="btnBack" value="Back" class="inputs" onClick="location.href='module_access_rights.php'">
						<font id="indicator"></font>
					</td>
				</tr>
			</TABLE>
			
			<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$_GET['transType']?>">
			<INPUT type="hidden" name="moduleCount" id="moduleCount" value="<?=$i?>">
			<INPUT type="hidden" name="hdnChildModuleCnt" id="hdnChildModuleCnt<?=$i?>" value="<?=$j?>">
			<?$modAccRghrsObj->disConnect();?>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>	
	function saveModuleAccess(cmpCode,empNo){
		var params = $('frmAccessRights').serialize(true);
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+params['btnSave']+"&compCode="+cmpCode+"&empNo="+empNo+"&chldMdle="+params['childModule'],{
			method : 'get',
			onComplete : function (req){
				intRes = parseInt(req.responseText);
				if(intRes == 1){
					alert('Successfully Saved');
				}
				else{
					alert('Saving Failed');
				}
			},
			onCreate : function (){
				$('btnSave').disabled=true;
				$('btnSave').value='Loading...';
			},
			onSuccess : function (){
				$('btnSave').disabled=false;	
				$('btnSave').value='SAVE';
			}
		});
	}
	
	function checkAll(id,forcnt,chldCnt){
		if(id == 0){
			for(i=0;i<=chldCnt-1;i++){
				$('childModule'+i).checked=true;
			}
		}
		else{
			for(i=0+parseInt(forcnt)-parseInt(chldCnt);i<=forcnt-1;i++){
				$('childModule'+i).checked=true;
			}			
		}
	}
	
	function unCheckAll(id,forcnt,chldCnt){
		if(id == 0){
			for(i=0;i<=chldCnt-1;i++){
				$('childModule'+i).checked=false;
			}
		}
		else{
			for(i=0+parseInt(forcnt)-parseInt(chldCnt);i<=forcnt-1;i++){
				$('childModule'+i).checked=false;
			}			
		}		
	}
	
	function overAllCheck(id){
		for(i=0;i<=parseInt(id)-1;i++){
			$('childModule'+i).checked=true;
		}
	}
	
	function overAllUnCheck(id){
		for(i=0;i<=parseInt(id)-1;i++){
			$('childModule'+i).checked=false;
		}		
	}
</SCRIPT>