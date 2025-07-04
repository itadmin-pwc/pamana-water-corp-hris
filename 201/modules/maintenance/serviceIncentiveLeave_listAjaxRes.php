<?
header('Content-Type: text/html; charset=iso-8859-1');

session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');
$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');

if($brnCode_View ==""){
	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblUserBranch tblUB, tblBranch as tblbrn
						where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
						and empNo='".$_SESSION['employee_number']."'
						order by brnDesc";
	
	$resBrnches = $common->execQry($queryBrnches);
	$arrBrnches = $common->getArrRes($resBrnches);
	$arrBrnch = $common->makeArr($arrBrnches,'brnCode','brnDesc','All');
}

$qryEmpList = "SELECT * FROM view_leave_credit 
			    WHERE compCode = '{$sessionVars['compCode']}' 
				";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			
			if ($_GET['brnCd']!=0) 
			{
				$qryEmpList .= " AND empBrnCode='".$_GET["brnCd"]."' ";
			}
        }
$resIntMaxRec = $common->execQry($qryEmpList);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
$qryEmpList .=	" ORDER BY empLastName limit $intOffset, $intLimit";

$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);
$payGrp = $common->getProcGrp();
?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Employee List (Service Incentive Leave)
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar">
                        <!-- <a href="#"  class="anchor" onclick="maintBranch('ADD','','branch_listAjaxRes.php','branchMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
                        <img class="anchor" src="../../../images/add.gif">Generate SIL</a> 
                        <FONT class="ToolBarseparator">|</font> -->
						<?
					/*	if($_GET['action']=='Search'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}*/
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
							
							
					
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<?php if($brnCode_View==""){echo  "Branch |";}?> <? if($brnCode_View ==""){
							$branchDesc = $common->DropDownMenu(str_replace("�","N",$arrBrnch),'brnCd',$_GET['brnCd'],'class="inputs"');
							echo $brnDes =  $branchDesc;
							}?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('serviceIncentiveLeave_listAjaxRes.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')">
					
                    </td>
					<tr>
						<td width="2%" class="gridDtlLbl" align="center">#</td>
						<td width="11%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="33%" class="gridDtlLbl" align="center">NAME</td>
						<td class="gridDtlLbl" align="center">Year</td>
						<td class="gridDtlLbl" align="center">Leave Credit</td>
						<td class="gridDtlLbl" align="center">Leave Used</td>
						<td class="gridDtlLbl" align="center">Year</td>
						<td class="gridDtlLbl" align="center">Leave Credit</td>
						<td class="gridDtlLbl" align="center">Leave Used</td>
					</tr>
					<?
					if($common->getRecCount($resEmpList) > 0){
						$i=0;
						foreach ($arrEmpList as $empListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
						$red = ($empListVal['credit_previous_expiry_date'] < date('Y-m-d') && $empListVal['credit_previous_year'] > 0) ? 'red' : '';
					?>
					<tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt" color="<?=$red?>"><?=$empListVal['credit_previous_year']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt" color="<?=$red?>"><?=$empListVal['credit_grant_previous_year']?></font></td>
                        <td class="gridDtlVal"><font class="gridDtlLblTxt" color="<?=$red?>"><?=$empListVal['credit_used_previous_year']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['credit_current_year']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['credit_grant_current_year']?></font></td>
                        <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['credit_used_current_year']?></font></td>
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
						<td colspan="8" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("serviceIncentiveLeave_listAjaxRes.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&brnCd='.$_GET["brnCd"],'');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>
<form name="frmTS" method="post">
<input type="hidden" name="brnCd" id="brnCd" value="<?php echo $_GET['brnCd']; ?>">
</form>