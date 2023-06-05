<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("earnings.obj.php");


$earningsObj = new earningsObj($_GET,$_SESSION);
$earningsObj->validateSessions('','MODULES');

$pager = new AjaxPager(15,'../../../images/');

$getEarnTranHeader = $earningsObj->getEarnTranHEader();
$refNo       = $getEarnTranHeader['refNo'];
$earnRem     = $getEarnTranHeader['earnRem'];
$trnType     = $getEarnTranHeader['trnCode'];
$EarnStat    = $getEarnTranHeader['earnStat'];
$hdnTrnsType = $getEarnTranHeader['trnCode'];
$cmbPeriod = $getEarnTranHeader['pdYear']."-".$getEarnTranHeader['pdNumber'];

$qryIntMaxRec = "SELECT dtl.empNo
				 FROM tblEarnTranDtl as dtl LEFT JOIN tblEmpMast as emp
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
$resIntMaxRec = $earningsObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
if(empty($intOffset)){
	$intOffset = 0;
}

$qryGetEarnDtl = "SELECT TOP $intLimit 
				   dtl.compCode,dtl.refNo,dtl.empNo,dtl.trnCntrlNo,dtl.trnAmount,dtl.payCat,
				   emp.empFirstName,emp.empMidName,emp.empLastName,emp.empPayGrp,emp.empPayCat
				   FROM tblEarnTranDtl as dtl LEFT JOIN tblEmpMast as emp
				   ON dtl.compCode = emp.CompCode
				   AND dtl.empNo = emp.empNo
				   WHERE dtl.seqNo
										 NOT IN(
			    						 SELECT TOP $intOffset dtl.seqNo
										 FROM tblEarnTranDtl as dtl LEFT JOIN tblEmpMast as emp
				   ON dtl.compCode = emp.CompCode
				   AND dtl.empNo = emp.empNo 
				   and dtl.payGrp='".$_SESSION["pay_group"]."'
				  and dtl.payCat = '".$_SESSION["pay_category"]."'
				   WHERE dtl.compCode = '{$_SESSION['company_code']}'
				   "; 
	        if($_GET['isSearch'] == 1){
	        	if($_GET['srchType'] == 0){
	        		$qryGetEarnDtl .= "AND emp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
	        	}
	        	if($_GET['srchType'] == 1){
	        		$qryGetEarnDtl .= "AND emp.empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	        	}
	        	if($_GET['srchType'] == 2){
	        		$qryGetEarnDtl .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	        	}
	        } 
$qryGetEarnDtl .= "
				  and dtl.payGrp='".$_SESSION["pay_group"]."'
				 and dtl.payCat = '".$_SESSION["pay_category"]."'
				  AND dtl.refNo = '{$_GET['refNo']}'
				  ORDER BY emp.empLastName) ";
	        if($_GET['isSearch'] == 1){
	        	if($_GET['srchType'] == 0){
	        		$qryGetEarnDtl .= "AND emp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
	        	}
	        	if($_GET['srchType'] == 1){
	        		$qryGetEarnDtl .= "AND emp.empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	        	}
	        	if($_GET['srchType'] == 2){
	        		$qryGetEarnDtl .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
	        	}
	        }
$qryGetEarnDtl .=	"	and dtl.payGrp='".$_SESSION["pay_group"]."'
				 		and dtl.payCat = '".$_SESSION["pay_category"]."'
						AND dtl.compCode = '{$_SESSION['company_code']}'
						AND dtl.refNo = '{$_GET['refNo']}'
						ORDER BY emp.empLastName ";

$resGetEarnDtl = $earningsObj->execQry($qryGetEarnDtl);
$arrGetEarnDtl = $earningsObj->getArrRes($resGetEarnDtl);


if(($_GET['action'] == 'addHdrDtlMid') || ($_GET['action'] == 'updtHdr' ) || ($_GET['action'] == 'Search' && !empty($_GET['refNo'])) 
	|| ($_GET['action'] == 'UpthdrAddDtl') || ($_GET['action'] =='editRef') 
	|| ($_GET['action'] == 'deleEarnDtl') || (($_GET['action'] == 'refresh') && (!empty($_GET['refNo']))) || ($_GET['action'] == 'Next')
	|| ($_GET['action'] == 'Prev') || ($_GET['action'] == 'Last') || ($_GET['action'] == 'First') || ($_GET['action'] == 'getPage' && !empty($_GET['refNo']))){
	 $disabled = 'disabled';
}
?>
<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Earnings Entry
		</td>
	</tr>
	<tr>
		<td colspan="6" class="gridToolbar">
			&nbsp;
			<a href="#" id="newEarn" tabindex="1"><IMG class="toolbarImg" src="../../../images/application_form_add.png"  onclick="newRef('NEWREFNO'); validateMod('NEWREFNO');" title="New"></a>
			<FONT class="ToolBarseparator">|</font>
			<a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" src="../../../images/application_form_edit.png"  onclick="validateMod('EDITRENO');" title="Edit" ></a>
			<FONT class="ToolBarseparator">|</font>
			<?
				if(($_GET['action'] == 'load') || (($_GET['action'] == 'refresh') && (empty($_GET['refNo'])))){
			?>
				<a href="#" id="deleEarn"><img class="toolbarImg" src="../../../images/application_form_delete_2.png"></a>
			<?}else{?>
				<a href="#" id="deleEarn" tabindex="3"><img class="toolbarImg" src="../../../images/application_form_delete.png" onclick="maintEarnings('earnings.php','earningsCont','deleEarn','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')"></a>
			<?}?>
			<FONT class="ToolBarseparator">|</font>
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.gif'  onclick="pager('earningsAjaxResult.php','earningsCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
		</td>
	</tr>
	<tr>
		<td class="parentGridDtl" valign="top">
			<!--header-->					
			<TABLE width="100%" cellpadding="1" cellspacing="1" border="0" class="hdrTable">
				<tr>
					<td class="hdrLblRow" colspan="15">
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
						<INPUT tabindex="6" type="button" name="refLookup" id="refLookup" class="inputs" title="Reference Lookup" disabled onclick="viewLookup()" >
						<font id="refNoCont"></font>
					</td>
					<td class="hdrInputsLvl" width="10%">
						Remarks
					</td>
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<INPUT tabindex="8" class="inputs" type="text" name="earnRem" id="earnRem" size="40" value="<?=htmlspecialchars($earnRem)?>">
					</td>
					<td class="hdrInputsLvl" width="10%">
						Period
					</td>
					<td class="hdrInputsLvl" width="5">
						:
					</td>

					<td class="gridDtlVal" colspan="4">
						<?
							 $earningsObj->DropDownMenu(array('2010-25'=>'12/08/2010'),'cmbPeriod',$cmbPeriod,'class="inputs" tabindex="7" '.$disabled); 
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
							$earningsObj->DropDownMenu(
								$earningsObj->makeArr(
									$earningsObj->getTransType($_SESSION['company_code'],'earnings',' and trnCode in (0807,8018)'),'trnCode','trnDesc',''
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
					<td class="gridDtlVal" width="26%">
						<?
							$earningsObj->DropDownMenu(array('A'=>'Active','H'=>'Held'),'cmbEarnStat',$EarnStat,'class="inputs" tabindex="9"');
						?>
					</td>
					<td class="hdrInputsLvl" align="right" colspan="6">
						<?
							if(($_GET['action'] == 'load') || (($_GET['action'] == 'refresh') && (empty($_GET['refNo']))) || (($_GET['action'] == 'Search') && (empty($_GET['refNo'])))){
								$disabled2 = 'disabled';
							}
						?>
						Action:<INPUT tabindex="10" class="inputs" type="button" name="btnUpdtHdr" id="btnUpdtHdr" value="SAVE" <?=$disabled2?> 
						onclick="maintEarnings('earnings.php','earningsCont','updtHdr','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')">
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
							if($_GET['action'] == 'load' || ($_GET['action'] == 'refresh' && empty($_GET['refNo']))){ 
								$action = 'addHdrDtlMid'; }
							else{                          
								$action = 'UpthdrAddDtl'; } 
						?>
						<INPUT tabindex="14" class="inputs" type="button" name="btnSaveAddDtl" id="btnSaveAddDtl" value="SAVE" onclick="maintEarnings('earnings.php','earningsCont','<?=$action?>','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')">
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
						Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">In
						<?=$earningsObj->DropDownMenu(array('Employee No.','First Name','Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
						<INPUT tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('earningsAjaxResult.php','earningsCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>','../../../images/')">
					</td>
				</tr>

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
				if(@$earningsObj->getRecCount($resGetEarnDtl) > 0){

					$i=0;
					foreach (@$arrGetEarnDtl as $earnDtlVal){

						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
				?>
				<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
					<td class="gridDtlVal" >
						<font class="gridDtlLblTxt" id="txtEmpNo<?=$i?>"><?=$earnDtlVal['empNo']?></font>
					</td>
					<td class="gridDtlVal">
						<?
							$empMidName = (!empty($earnDtlVal['empMidName'])) ? substr($earnDtlVal['empMidName'],0,1)."." : '';
						?>
						<font class="gridDtlLblTxt"><?=$earnDtlVal['empFirstName']." ".$empMidName." ".$earnDtlVal['empLastName']?></font>
					</td>
					<td class="gridDtlVal">
						<font class="gridDtlLblTxt"><?php echo ((int)$earnDtlVal['empPayGrp'] == 1?"1-One" : "2-Two"); ?></font>
					</td>
					<td class="gridDtlVal">
						<?
							$payCat = $earningsObj->getPayCat($_SESSION['company_code'],"AND payCat = '{$earnDtlVal['payCat']}' ");
						?>
						<font class="gridDtlLblTxt"><?=$payCat['payCat']."-".$payCat['payCatDesc']?></font>
					</td>
					<td class="gridDtlVal" >
						<font class="gridDtlLblTxt" id="txtCntrlNo<?=$i?>"><?=$earnDtlVal['trnCntrlNo']?></font>
					</td>
					<td class="gridDtlVal">
						<font class="gridDtlLblTxt"><?=$earnDtlVal['trnAmount']?></font>
					</td>
					<td class="gridDtlVal" align="center">
						<a href="#" tabindex="18"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Detail" onclick="maintEarnings('earnings.php','earningsCont','deleEarnDtl','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','<?=$i?>','<?=htmlspecialchars(addslashes($earnDtlVal['empFirstName']." ".$empMidName." ".$earnDtlVal['empLastName']))?>')"></a>
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
							$pager->_viewPagerButton('earningsAjaxResult.php','earningsCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo']);
						?>
					</td>
				</tr>
			</TABLE>
		
		</td>
	</tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<?$earningsObj->disConnect();?>


