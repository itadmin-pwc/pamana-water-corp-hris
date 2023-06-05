<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("lastPay.obj.php");

$lastPayObj = new lastPayObj($_SESSION,$_GET);

$lastPayObj->validateSessions('','MODULES');
$sessionVars = $lastPayObj->getSeesionVars();

switch ($_GET['action']){
	case 'resigned':
			if($lastPayObj->ResignedEmp() == true){
				echo 1;
			}
			else{
				echo 2;
			}
		exit();
	break;
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>  
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>
		@import url('../../style/payroll.css');.style1 {font-size: 11px}
        </STYLE>
	</HEAD>
	<BODY>
		<FORM name='frmAccessRights' id="frmAccessRights" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td colspan="4" class="parentGridHdr">
						&nbsp;<img src="../../../images/grid.png">&nbsp;Resigned Employees (
						<?
						$arrPd = $lastPayObj->currPayPd();
						echo date('m/d/Y',strtotime($arrPd['pdFrmDate'])) . " - " . date('m/d/Y',strtotime($arrPd['pdToDate']));?>)					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
							<tr>
								<td class="gridDtlLbl" align="center">&nbsp;</td>
							    <td height="25" align="center" class="gridDtlLbl"><div align="left" class="style1">Emp No.</div></td>
						      <td class="gridDtlLbl" align="center"><div align="left" class="style1">Employee Name</div></td>
							</tr>
							<?
								$j=$j+0;
								$a=0;
								foreach ($lastPayObj->getResignedEmp() as $empVal){
									
								$bgcolor = ($j%2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
							?>
                            
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal" width="20">
									<INPUT type="checkbox" id="chempNO<?=$j?>" name="chempNO" value="<?=$empVal['empNo']?>" >								</td>
								<td width="82" class="gridDtlVal" ><font class="gridDtlLblTxt">
								  <?=$empVal['empNo']?>
								</FONT></td>
								<td width="956" class="gridDtlVal" ><font class="gridDtlLblTxt"><?=str_replace('Ñ','&Ntilde;',$empVal['empLastName']. ", " . $empVal['empFirstName'] . " " . $empVal['empMidName'])?></FONT></td>
							</tr>
							<?
								$a++;
								$j++;
								}
							?>
					  </TABLE>
						<INPUT class="inputs" type="button" value="Check All" onClick="return checkAll(<?=$j?>,<?=$a?>);">
						<INPUT class="inputs" type="button" value="Uncheck All" onClick="return unCheckAll(<?=$j?>,<?=$a?>);">
						<INPUT class="inputs" type="button" name="Submit" id="Submit" value="Submit" onClick="ResigneEmp()">
				  <br></td>
				</tr>
			</TABLE>
			
	    <INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$_GET['transType']?>">
			<INPUT type="hidden" name="moduleCount" id="moduleCount" value="<?=$i?>">
			<INPUT type="hidden" name="hdnChildModuleCnt" id="hdnChildModuleCnt<?=$i?>" value="<?=$j?>">
			<INPUT type="hidden" name="pdNumber" id="pdNumber" value="<?=$arrPd['pdNumber']?>">
			<INPUT type="hidden" name="pdYear" id="pdYear" value="<?=$arrPd['pdYear']?>">
			<?$lastPayObj->disConnect();?>
	    </FORM>
	</BODY>
</HTML>
