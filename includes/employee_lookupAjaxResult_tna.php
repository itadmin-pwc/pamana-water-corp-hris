<?
session_start();
include("db.inc.php");
include("common.php");
include("pager.inc.php");

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

if(!empty($_GET['tmpCompCode'])){
	$compCode =  $_GET['tmpCompCode'];
}
else{
	$compCode =  $_SESSION['company_code'];
}
if($brnCode_View ==""){
	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblUserBranch tblUB, tblBranch as tblbrn
						where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
						and empNo='".$_SESSION['employee_number']."'
						order by brnDesc";
	
	$resBrnches = $common->execQry($queryBrnches);
	$arrBrnches = $common->getArrRes($resBrnches);
	$arrBrnch = $common->makeArr($arrBrnches,'brnCode','brnDesc','All');
}
if(!empty($_GET['empType'])) {
	$txtAdd = 'txtAddEmpNo2';
}else{
	$txtAdd = 'txtAddEmpNo';
}

//$sqlGrp = "Select payGrp from tblProcGrp where compCode='{$_SESSION['company_code']}' and status='A'";
$sqlGrp = "Select payGrp from tblProcGrp where compCode='{$_SESSION['company_code']}'";
$res = $common->getSqlAssoc($common->execQry($sqlGrp));		
$paygroup = $res['payGrp'];	
$qryPayperiod = $common->execQry("Select pdFrmDate,DATE_ADD(pdToDate,INTERVAL 3 DAY) as  pdToDate
								  from tblPayPeriod 
								  where compCode='{$_SESSION['company_code']}' and payGrp='{$paygroup}' 
									and pdYear='".date("Y")."' and pdStat='O'");
$payperiod = $common->getSqlAssoc($qryPayperiod);


$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');


//new codes
	$qryEmpList = "SELECT * FROM tblEmpMast
					WHERE compCode= '{$sessionVars['compCode']}'
					AND empBrnCode IN (Select brnCode from tblUserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')
					AND (empStat='RG') 
					AND compCode = '{$compCode}'"; 

	if($_GET['isSearch'] == 1){
		if($_GET['srchType'] == 2){
			$qryEmpList .= "AND empNo LIKE '{$_GET['txtSrch']}%' ";
		}
		if($_GET['srchType'] == 0){
			$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
		}
		if($_GET['srchType'] == 1){
			$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
		}
		
		if ($_GET['brnCd']!=0) 
		{
			$qryEmpList.= " AND empbrnCode='".$_GET["brnCd"]."' ";
		}
	}

$resIntMaxRec = $common->execQry($qryEmpList);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList .= " ORDER BY empLastName limit $intOffset,$intLimit"; 
		
$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);
?>

			
<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
	<td colspan="7" class="gridToolbar">
		Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
		<?php if($brnCode_View==""){echo  "Branch |";}?> <? if($brnCode_View ==""){echo $common->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');}?>
		<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('../../../includes/employee_lookupAjaxResult_tna.php','empLukupCont','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')"></td>
	<tr>
		<td class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
		<td class="gridDtlLbl" align="center">NAME</td>
		<td class="gridDtlLbl" align="center">Branch</td>
	</tr>
	<?
	if($common->getRecCount($resEmpList) > 0){
		$i=0;
		foreach ($arrEmpList as $empListVal){
			
		$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
	?>
	<tr style="cursor:pointer;" bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> onclick="passEmpNo('<?=$txtAdd?>','<?=$empListVal['empNo']?>');">
		<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
		<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace('Ñ','&Ntilde;',$empListVal['empLastName']. ", " . $empListVal['empFirstName'] . " " . $empListVal['empMidName'])?></font></td>
		<td class="gridDtlVal">
			<font class="gridDtlLblTxt">
				<?= $brnch['brnDesc'] = $common->getInfoBranch($empListVal['empBrnCode'],$empListVal['compCode']);?>
			</font>
		</td>
	</tr>
	<?
		}
	}
	else{
	?>
	<tr>
		<td colspan="7" align="center">
			<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
		</td>
	</tr>
	<?}?>
	<tr>
		<td colspan="7" align="center" class="childGridFooter">
			<? 
				$pager->_viewPagerButton('../../../includes/employee_lookupAjaxResult_tna.php','empLukupCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&tmpCompCode='.$_GET['tmpCompCode'].'&empType='.$_GET['empType']);
			?>
		</td>
	</tr>
</TABLE>
				

<?$common->disConnect();?>