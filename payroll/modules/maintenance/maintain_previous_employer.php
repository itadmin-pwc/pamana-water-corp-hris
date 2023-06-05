<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_employee.Obj.php");


$sessionVars = maintPrevEmplyr::getSeesionVars();
$maintPrevEmp = new maintPrevEmplyr($_GET,$sessionVars);
$maintPrevEmp->errorLogPath='../../../includes/error_log.txt';

$maintPrevEmp->validateSessions('','MODULES');

$empInfo = $maintPrevEmp->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');

if($_GET['transType'] == 'Edit'){
	$getPrevEmp = "AND empNo = '{$_GET['empNo']}'AND seqNo = '{$_GET['seqNo']}' ";
	$arrPrevEmp = $maintPrevEmp->getPrevEmployer($sessionVars['compCode'],$getPrevEmp);

	$emplyrName     = $arrPrevEmp[0]['prevEmplr'];
	$emplyrAdd1     = $arrPrevEmp[0]['empAddr1'];
	$emplyrAdd2     = $arrPrevEmp[0]['empAddr2'];
	$emplyrAdd3     = $arrPrevEmp[0]['empAddr3'];
	$emplyrTinNo    =  $arrPrevEmp[0]['emplrTin'];
	$emplyrPrevEarn =  $arrPrevEmp[0]['prevEarnings'];
	$emplyrPrevTax  = $arrPrevEmp[0]['prevTaxes'];
	$stat           = $arrPrevEmp[0]['prevStat']; 
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
		<FORM name="frmMaintPrevEmp" id="frmMaintPrevEmp" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
					<tr>
						<td align="center" colspan="3" class="prevEmpHeader">
							<?
								$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."," : '';
								echo $empInfo['empNo'] . " - " . $empInfo['empFirstName'] . " " . $midName . " " . $empInfo['empLastName'];
							?>
						<td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" width="20%">
							Name
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT size="50" class="inputs" type="text" name="emplyrName" id="emplyrName" value="<?=htmlspecialchars($emplyrName)?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Address 1
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT size="50" class="inputs" type="text" name="emplyrAdd1" id="emplyrAdd1" value="<?=htmlspecialchars($emplyrAdd1)?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Address 2
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT size="50" class="inputs" type="text" name="emplyrAdd2" id="emplyrAdd2" value="<?=htmlspecialchars($emplyrAdd2)?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Address 3
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT size="50" class="inputs" type="text" name="emplyrAdd3" id="emplyrAdd3" value="<?=htmlspecialchars($emplyrAdd3)?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							TIN No.
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT size="20" class="inputs" type="text" name="emplyrTinNo" id="emplyrTinNo" value="<?=$emplyrTinNo?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Earnings
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT size="20" class="inputs" type="text" name="emplyrPrevEarn" id="emplyrPrevEarn" value="<?=$emplyrPrevEarn?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Tax
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT size="20" class="inputs" type="text" name="emplyrPrevTax" id="emplyrPrevTax" value="<?=$emplyrPrevTax?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Status
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
								$maintPrevEmp->DropDownMenu(array('A'=>'Active','H'=>'Held'),'cmbStat',$stat,'class="inputs"');
							?>
						</td>
					</tr>
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<INPUT type="button" name="btnMaintPrevEmplyr" id="btnMaintPrevEmplyr" value="<?=$_GET['transType']?>" class="inputs" onclick="validatePrevEmplyr('<?=$_GET['empNo']?>')" >
							<INPUT type="reset" value="Reset" class="inputs">
						<td>
					</tr>
				</TABLE>				
				<INPUT type="hidden" name="hdnSeqNo" id="hdnSeqNo" value="<?=$_GET['seqNo']?>" >
		</FORM>
	</BODY>
</HTML>
