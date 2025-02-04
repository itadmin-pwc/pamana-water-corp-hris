<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
/*if ($_SESSION['user_level'] == 3) {
	$userinfo = $common->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
	
	if($userinfo['empLocCode']=='0001'){
		$brnCode = " $and empLocCode='".$userinfo['empLocCode']."'";
		$brnCodelist = " AND  empLocCode='".$userinfo['empLocCode']."'";
	}
	
	
	else{
		if($userinfo['empLocCode']!='0001'){
			$brnCode = " $and empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
			$brnCodelist = " AND empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
			$brnCode_View = 1;
		}
	}
}*/
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER','ACCT. NO.');

if($brnCode_View ==""){
	$arrBrnch = $common->makeArr($common->getBranchByCompGrp(" and brnDefGrp='".$_SESSION["pay_group"]."' and compCode='".$_SESSION["company_code"]."'"),'brnCode','brnDesc','All');
}
if ($_SESSION['pay_category'] != 9) {
	$empStat = " AND empStat NOT IN('RS','IN','TR','USER')";
}
$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}' 
				 AND empPayGrp='".$_SESSION["pay_group"]."'
				 $empStat $brnCodelist
				 and empPayCat<>0 and empPayCat='{$_SESSION['pay_category']}'";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND empNo LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND empLastName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND empFirstName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 3){
        		$qryIntMaxRec .= "AND replace(empAcctNo,'-','') LIKE '%".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
			
			if ($_GET['brnCd']!=0 || !empty($_GET['brnCd'])) 
			{
				$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT  *
		FROM tblEmpMast
		WHERE  empPayCat<>0 $empStat and empPayCat='{$_SESSION['pay_category']}'
		AND empPayGrp='".$_SESSION["pay_group"]."'
		AND compCode = '{$sessionVars['compCode']}'  $brnCodelist";
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
        	if($_GET['srchType'] == 3){
        		$qryEmpList .= "AND replace(empAcctNo,'-','') LIKE '%".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			
			if ($_GET['brnCd']!=0 || !empty($_GET['brnCd'])) 
			{
				$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }
$qryEmpList .=	"ORDER BY empLastName limit $intOffset, $intLimit";
$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Employee Master List / Personal Profile 
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="7" class="gridToolbar">
						
                         <FONT class="ToolBarseparator">&nbsp;</font>
						<?
					
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
							
							
					
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$srchType,'class="inputs"');?>
						<?php if($brnCode_View==""){echo  "Branch |";}?> <? if($brnCode_View ==""){echo $common->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');}?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('profile_list_ajax.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')">
					
                    </td>
					<tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="70%" class="gridDtlLbl" align="center">NAME</td>
						<td class="gridDtlLbl" align="center" colspan="4">ACTION</td>
					</tr>
					<?
					if($common->getRecCount($resEmpList) > 0){
						$i=0;
						foreach ($arrEmpList as $empListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("�","&Ntilde;",$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName'])?></font></td>
						<td class="gridDtlVal" align="center">
							<a href="#" onClick="location.href='profile.php?act=Edit&empNo=<?=$empListVal['empNo']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_form_magnify.png" title="View/Edit Employee Information" /></a>
                            
							<?php 
								if (($_SESSION['user_level'] == 1)||($_SESSION['user_level'] == 2)) {
						    ?>
                            <a href="#" onClick="location.href='employee_allowance.php?transType=list&empNo=<?=$empListVal['empNo']?>'"><img class="toolbarImg" src="../../../images/allowance_list.png" title="Employee Allowance"></a>
							<?php } ?>
                        </td> 
						
					</tr>
					<tr id="trPrevEmpCont<?=$empListVal['empNo']?>" style="display:none;">
						<td colspan="7" >
							
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
							<? $pager->_viewPagerButton("profile_list_ajax.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&brnCd='.$_GET["brnCd"],'');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>