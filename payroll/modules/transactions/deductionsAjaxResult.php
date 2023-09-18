<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("deductions.obj.php");

$deductionsObj = new maintDeduct($_GET,$_SESSION);
$deductionsObj->validateSessions('','MODULES');

$pager = new AjaxPager(15,'../../../images/');

$getDedTranHeader = $deductionsObj->getDedTranHEader();
$refNo    = $getDedTranHeader['refNo'];
$dedRem   = $getDedTranHeader['dedRemarks'];
$trnType  = $getDedTranHeader['trnCode']."-".$getDedTranHeader['trnPriority'];
$DedStat  = $getDedTranHeader['dedStat'];
$cmbPeriod = $getDedTranHeader['pdYear']."-".$getDedTranHeader['pdNumber'];
		
$qryIntMaxRec = "SELECT dtl.empNo
				 FROM tblDedTranDtl as dtl LEFT JOIN tblEmpMast as emp
				 ON dtl.compCode = emp.CompCode
				 AND dtl.empNo = emp.empNo 
				 WHERE dtl.compCode = '{$_SESSION['company_code']}'
				 and dtl.payGrp='".$_SESSION["pay_group"]."'
				 and dtl.payCat = '".$_SESSION["pay_category"]."'
				 AND dtl.refNo = '{$_GET['refNo']}' ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND emp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND emp.empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        }
        $qryIntMaxRec .= "ORDER BY emp.empLastName ";
$resIntMaxRec = $deductionsObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
if(empty($intOffset)){
	$intOffset = 0;
}

$qryGetDedDtl = "SELECT  
				   dtl.compCode,dtl.refNo,dtl.empNo,dtl.trnCntrlNo,dtl.trnAmount,dtl.payCat,
				   emp.empFirstName,emp.empMidName,emp.empLastName,emp.empPayGrp,emp.empPayCat
				   FROM tblDedTranDtl as dtl LEFT JOIN tblEmpMast as emp
				   ON dtl.compCode = emp.CompCode
				   AND dtl.empNo = emp.empNo
				   
				   WHERE dtl.compCode='{$_SESSION['company_code']}' ";
	        if($_GET['isSearch'] == 1){
	        	if($_GET['srchType'] == 0){
	        		$qryGetDedDtl .= "AND emp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
	        	}
	        	if($_GET['srchType'] == 1){
	        		$qryGetDedDtl .= "AND emp.empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	        	}
	        	if($_GET['srchType'] == 2){
	        		$qryGetDedDtl .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	        	}
	        }
$qryGetDedDtl .=	"	and dtl.payGrp='".$_SESSION["pay_group"]."'
				 		and dtl.payCat = '".$_SESSION["pay_category"]."'
						AND dtl.compCode = '{$_SESSION['company_code']}'
						AND dtl.refNo = '{$_GET['refNo']}'
						ORDER BY emp.empLastName Limit $intOffset,$intLimit ";

$resGetDedDtl = $deductionsObj->execQry($qryGetDedDtl);
$arrGetDedDtl = $deductionsObj->getArrRes($resGetDedDtl);

if(($_GET['action'] == 'addHdrDtlMid') || ($_GET['action'] == 'updtHdr') || ($_GET['action'] == 'Search' && !empty($_GET['refNo']))
	|| ($_GET['action'] == 'UpthdrAddDtl') || ($_GET['action'] =='editRef') 
	|| ($_GET['action'] == 'deleDedDtl') || ($_GET['action'] == 'refresh' && !empty($_GET['refNo'])) || ($_GET['action'] == 'Next')
	|| ($_GET['action'] == 'Prev') || ($_GET['action'] == 'Last') || ($_GET['action'] == 'First') || ($_GET['action'] == 'getPage' && !empty($_GET['refNo']))){
	 $disabled = 'disabled';
}

?>
<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Deductions Entry
		</td>
	</tr>
	<tr>
		<td colspan="6" class="gridToolbar">
			&nbsp;
			<a href="#" tabindex="1" id="newDeduc"><IMG class="toolbarImg" src="../../../images/application_form_add.png" name="newDeduc" id="newDeduc" onclick="newRef('NEWREFNO'); validateMod('NEWREFNO');" title="New"></a>
			<FONT class="ToolBarseparator">|</font>
			<a href="#" tabindex="2" id="editDeduc"><img class="toolbarImg" src="../../../images/application_form_edit.png" name="newDeduc" id="editDeduc" onclick="validateMod('EDITRENO');" title="Edit" ></a>
			<FONT class="ToolBarseparator">|</font>
			<?
				if($_GET['action'] == 'load' || ($_GET['action'] == 'refresh' && empty($_GET['refNo']))){
			?>
				<a href="#" id="deleDeduc" ><img class="toolbarImg" src="../../../images/application_form_delete_2.png"></a>
			<?}else{?>
				<a tabindex="3" href="#" id="deleDeduc"><img class="toolbarImg" src="../../../images/application_form_delete.png" onclick="maintDeductions('deductions.php','deductionsCont','deleDeduc','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')"></a>
			<?}?>
			<FONT class="ToolBarseparator">|</font>
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.png' name="newDeduc" id="newDeduc" onclick="pager('deductionsAjaxResult.php','deductionsCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>
		</td>
	</tr>
	<tr>
		<td class="parentGridDtl" valign="top">
			<!--header-->					
			<TABLE width="100%" cellpadding="1" cellspacing="1" border="0" class="hdrTable">
				<tr>
					<td class="hdrLblRow" colspan="14">
						<FONT class="hdrLbl">Header</font>
					</td>
				</tr>
				<tr>
					<td class="hdrInputsLvl" width="10%">
						Reference NO.
					</td>
					<td class="hdrInputsLvl" width="5">
						:
					</td>
					<td class="gridDtlVal" width="18%">
						<INPUT tabindex="5" class="inputs" type="text" name="refNo" id="refNo" size="10" value="<?=$refNo?>" readonly onkeyup="return editRefNo('editRef',this.value,event)">
						<INPUT tabindex="6" type="button" name="refLookup" id="refLookup" value="" class="inputs" title="Reference Lookup" disabled onclick="viewLookup()">
						<font id="refNoCont"></font>
					</td>
					<td class="hdrInputsLvl" width="10%">
						Remarks
					</td>
					<td class="hdrInputsLvl" width="5">
						:
					</td>
					<td class="gridDtlVal" colspan="3" width="25%">
						<INPUT tabindex="8" class="inputs" type="text" name="dedRem" id="dedRem" size="40" value="<?=htmlspecialchars($dedRem)?>">
					</td>
					<td class="hdrInputsLvl" width="10%">
						Period
					</td>
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<?
							$opnPrd = $deductionsObj->getPayPeriod_OtherEarn($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdStat = 'O'");
							if($cmbPeriod == "-"){
								$cmbPeriod = $opnPrd['pdYear']."-".$opnPrd['pdNumber'];
							}
							$deductionsObj->DropDownMenu(
								$deductionsObj->makeArrDate(
									$deductionsObj->getPayPeriod_OtherEarn($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdNumber >= '{$opnPrd['pdNumber']}'"),'pdYear','pdNumber','pdPayable',''
								),'cmbPeriod',$cmbPeriod,'class="inputs" tabindex="7" '.$disabled
							);							
						?>
					</td>
				</tr>
				<tr>
					<td class="hdrInputsLvl">
						Transaction Type
					</td>
					<td class="hdrInputsLvl">
						:
					</td>
					<td class="gridDtlVal">
						<?
							$deductionsObj->DropDownMenu(
								$deductionsObj->makeArr2(
									$deductionsObj->getTransType_OtherDed($_SESSION['company_code'],'deductions',''),'trnCode','trnPriority','trnDesc'
								),'cmbTrnType',$trnType,'class="inputs" tabindex="7" '.$disabled
							);
						?>
					</td>
					<td class="hdrInputsLvl">
						Status
					</td>
					<td class="hdrInputsLvl">
						:
					</td>
					<td class="gridDtlVal">
						<?
							$deductionsObj->DropDownMenu(array('A'=>'Active','H'=>'Held'),'cmbDedStat',$DedStat,'class="inputs" tabindex="9"');
						?>
					</td>
					<td class="hdrInputsLvl" align="right" colspan="5">
						<?
							if($_GET['action'] == 'load' || ($_GET['action'] == 'refresh' && empty($_GET['refNo'])) || ($_GET['action'] == 'Search' && empty($_GET['refNo']))){
								$disabled2 = 'disabled';
							}
						?>
						Action:<INPUT tabindex="10" class="inputs" type="button" name="btnUpdtHdr" id="btnUpdtHdr" value="SAVE" <?=$disabled2?> 
						onclick="maintDeductions('deductions.php','deductionsCont','updtHdr','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')">
					</td>
				</tr>
			</TABLE>
			
			<TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="hdrTable">
				<tr>
					<td class="hdrLblRow" colspan="7">
						<FONT class="hdrLbl" id="hlprMsg">Additional Detail</font>
					</td>
				</tr>
				<tr>
					<td width="12%" class="gridDtlLbl" align="center" >
						<a href="#" onclick="empLookup('../../../includes/employee_lookup.php')">EMPLOYEE NO.</a>
					</td>
					<td width="25%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
					<td width="10%" class="gridDtlLbl" align="center">PAY GROUP</td>
					<td width="20%" class="gridDtlLbl" align="center">PAY CATEGORY</td>
					<td width="10%" class="gridDtlLbl" align="center">CONTROL NO.</td>
					<td width="10%" class="gridDtlLbl" align="center">AMOUNT</td>
					<td width="5%" class="gridDtlLbl" align="center">ACTION</td>
				</tr>
				<tr>
					<td class="gridDtlVal" align="center">
						<INPUT tabindex="11" class="inputsAddDtl" type="text" name="txtAddEmpNo" id="txtAddEmpNo" onkeydown="getEmployee(event,this.value)" onclick="clearFld()" onfocus="clearFld()">
					</td>
					<td class="gridDtlVal" align="center">
						<INPUT class="inputsAddDtlRO" type="text" name="txtAddEmpName" id="txtAddEmpName" readonly>
					</td>
					<td class="gridDtlVal" align="center">
						<INPUT class="inputsAddDtlRO" type="text" name="txtAddPayGrp" id="txtAddPayGrp" readonly>
					</td>
					<td class="gridDtlVal" align="center">
						<INPUT class="inputsAddDtlRO" type="text" name="txtAddPayCat" id="txtAddPayCat" readonly>
					</td>
					<td class="gridDtlVal" align="center">
						<INPUT tabindex="12" class="inputsAddDtl" type="text" name="txtAddCntrlNo" id="txtAddCntrlNo" value="0">
					</td>
					<td class="gridDtlVal" align="center">
						<INPUT tabindex="13" class="inputsAddDtl" type="text" name="txtAddAmnt" id="txtAddAmnt">
					</td>
					<td class="gridDtlVal" align="center">
						<?
							if($_GET['action'] == 'load'  || ($_GET['action'] == 'refresh' && empty($_GET['refNo']))){ 
								$action = 'addHdrDtlMid'; }
							else{                          
								$action = 'UpthdrAddDtl'; } 
						?>
						<INPUT tabindex="14" class="inputs" type="button" name="btnSaveAddDtl" id="btnSaveAddDtl" value="SAVE" onclick="maintDeductions('deductions.php','deductionsCont','<?=$action?>','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')">
					</td>
				</tr>
				<tr>
					<td class="hdrLblRow" colspan="8">
						<FONT class="hdrLbl">Detail</font>
					</td>
				</tr>
			</TABLE>
			
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
				<tr>
					<td colspan="8" class="gridToolbar">
						<?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
						?>
						Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In
						<?=$deductionsObj->DropDownMenu(array('Employee No.','First Name','Last Name'),'cmbSrch',$srchType,'class="inputs" tabindex="16"');?>
						<INPUT tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('deductionsAjaxResult.php','deductionsCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>','../../../images/')">
					</td>
				</td>

				<tr>
					<td width="12%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
					<td width="25%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
					<td width="10%" class="gridDtlLbl" align="center">PAY GROUP</td>
					<td width="20%" class="gridDtlLbl" align="center">PAY CATEGORY</td>
					<td width="10%" class="gridDtlLbl" align="center">CONTROL NO.</td>
					<td width="10%" class="gridDtlLbl" align="center">AMOUNT</td>
					<td width="5%" class="gridDtlLbl" align="center">ACTION</td>
				</tr>
				<?
				if(@$deductionsObj->getRecCount($resGetDedDtl) > 0){

					$i=0;
					foreach (@$arrGetDedDtl as $dedDtlVal){

						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
				?>
				<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
					<td class="gridDtlVal" >
						<font class="gridDtlLblTxt" id="txtEmpNo<?=$i?>"><?=$dedDtlVal['empNo']?></font>
					</td>
					<td class="gridDtlVal">
						<?
							$empMidName = (!empty($dedDtlVal['empMidName'])) ? substr($dedDtlVal['empMidName'],0,1)."." : '';
						?>
						<font class="gridDtlLblTxt"><?=$dedDtlVal['empFirstName']." ".$empMidName." ".$dedDtlVal['empLastName']?></font>
					</td>
					<td class="gridDtlVal">
						<font class="gridDtlLblTxt"><?=($dedDtlVal['empPayGrp'] == 1) ? "1-One" : "2-Two";?></font>
					</td>
					<td class="gridDtlVal">
						<?
							$payCat = $deductionsObj->getPayCat($_SESSION['company_code'],"AND payCat = '{$dedDtlVal['payCat']}' ");
						?>
						<font class="gridDtlLblTxt"><?=$payCat['payCat']."-".$payCat['payCatDesc']?></font>
					</td>
					<td class="gridDtlVal" >
						<font class="gridDtlLblTxt" id="txtCntrlNo<?=$i?>"><?=$dedDtlVal['trnCntrlNo']?></font>
					</td>
					<td class="gridDtlVal">
						<font class="gridDtlLblTxt"><?=$dedDtlVal['trnAmount']?></font>
					</td>
					<td class="gridDtlVal" align="center">
						<a href="#" tabindex="18"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Detail" onclick="maintDeductions('deductions.php','deductionsCont','deleDedDtl','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','<?=$i?>','<?=htmlspecialchars(addslashes($dedDtlVal['empFirstName']." ".$empMidName." ".$dedDtlVal['empLastName']))?>')"></a>
					</td>
				</tr>		
				<?
						}
				}
				else{
				?>
				<tr>
					<td colspan="8" align="center">
						<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
					</td>
				</tr>
				<?}?>
				<tr>
					<td colspan="10" align="center" class="childGridFooter">
						<?
							$pager->_viewPagerButton('deductionsAjaxResult.php','deductionsCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo']);
						?>
					</td>
				</tr>
			</TABLE>
			
		</td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrans" id="hdnTrans" value="<?=$trnType?>">
<?$deductionsObj->disConnect();?>


