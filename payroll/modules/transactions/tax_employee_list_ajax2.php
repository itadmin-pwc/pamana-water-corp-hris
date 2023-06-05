<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("taxSpread.obj.php");

$taxObj = new taxSpreadobj();

$taxObj->validateSessions('','MODULES');
$sessionVars = $taxObj->getSeesionVars();

switch ($_GET['action']){
	case 'save':
			if($taxObj->SaveEmpTax() == true){
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
						&nbsp;<img src="../../../images/grid.png">&nbsp;Tax Spread</td>
			  </tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
							<tr>
								<td class="gridDtlLbl" align="center">&nbsp;</td>
							    <td height="25" align="center" class="gridDtlLbl"><div align="left" class="style1">Emp No.</div></td>
						      <td class="gridDtlLbl" align="center"><div align="left" class="style1">Employee Name</div></td>
							  <td class="gridDtlLbl" align="center"><div align="left" class="style1">
							    <div align="center">Tax Due</div>
							  </div></td>
							  <td width="306" align="center" class="gridDtlLbl"><div align="left" class="style1">
							    <div align="center">Estimated Tax</div>
							  </div></td>
						  </tr>
							<?
								$j=$j+0;
								$a=0;
								foreach ($taxObj->getEmpList() as $empVal){
								$wTax = $taxObj->computeWithTax($empVal['empNo'],$empVal['teuAmt'],$empVal['empPrevTag']);	
								if ($wTax > 0 ){
								$taxDue = ($wTax/($taxObj->get['pdNumber']-1));
								//$estTax = ($taxDue * (24-($taxObj->get['pdNumber']-1)))/(23-($taxObj->get['pdNumber']-1));
								$estTax =  $wTax/($taxObj->get['pdNumber']-1);
								$estTax += $wTax/(23-($taxObj->get['pdNumber']-1));
								//$estTax += $taxDue;
								$bgcolor = ($j%2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
							?>
                            
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal" width="24">
									<INPUT type="checkbox" id="chempNO<?=$j?>" name="chempNO" value="<?=$empVal['empNo']."_".$estTax?>" >								</td>
								<td width="141" class="gridDtlVal" ><font class="gridDtlLblTxt">
								  <?=$empVal['empNo']?>
								</FONT></td>
								<td width="529" class="gridDtlVal" ><font class="gridDtlLblTxt"><?=str_replace('Ñ','&Ntilde;',$empVal['empLastName']. ", " . $empVal['empFirstName'] . " " . $empVal['empMidName'])?></FONT></td>
							    <td width="199" class="gridDtlVal" ><div align="right">
							      <?=number_format($wTax,2)?>
					          </div></td>
						      <td   colspan="2" class="gridDtlVal" >
						        <div align="center">
						          <input name="textfield" type="text" class="gridDtlVal" id="textfield" value="<?=number_format($estTax,2)?>" style="text-align:right;" size="10">
						        
						        </div></td>
							</tr>
							<?
								$a++;
								$j++;
								}
								}
							?>
					  </TABLE>
            <INPUT class="inputs" type="button" value="Check All" onClick="return checkAll(<?=$j?>,<?=$a?>);">
						<INPUT class="inputs" type="button" value="Uncheck All" onClick="return unCheckAll(<?=$j?>,<?=$a?>);">
						<INPUT class="inputs" type="button" name="Submit" id="Submit" value="Save" onClick="EmpTax()">
				  <br></td>
				</tr>
			</TABLE>
			
	    <INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$_GET['transType']?>">
			<INPUT type="hidden" name="moduleCount" id="moduleCount" value="<?=$i?>">
			<INPUT type="hidden" name="hdnChildModuleCnt" id="hdnChildModuleCnt<?=$i?>" value="<?=$j?>">
			<INPUT type="hidden" name="pdNumber" id="pdNumber" value="<?=$arrPd['pdNumber']?>">
			<INPUT type="hidden" name="pdYear" id="pdYear" value="<?=$arrPd['pdYear']?>">
			<?$taxObj->disConnect();?>
	    </FORM>
	</BODY>
</HTML>
