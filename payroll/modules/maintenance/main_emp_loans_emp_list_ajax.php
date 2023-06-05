<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("main_emp_loans_obj.php");

$maintEmpLoanObj = new maintEmpLoanObj();
$sessionVars = $maintEmpLoanObj->getSeesionVars();
$maintEmpLoanObj->validateSessions('','MODULES');

$common = new commonObj();
$pager = new AjaxPager(18,'../../../images/');
$empNo = $_GET['empNo'];
$empName = $_GET['empName'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];

if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
$arrPd = $common->getOpenPeriodwil();
if ($_SESSION['pay_category'] != 9) {
	$qryFilter = "AND empStat NOT IN('RS','IN','TR')  and empPayGrp='{$_SESSION['pay_group']}' and empPayCat='{$_SESSION['pay_category']}'";
} else {
	$qryFilter = "
					 AND empPayGrp='{$_SESSION['pay_group']}' AND empNo IN (Select empNo from tblLastPayEmp where compCode='{$_SESSION['company_code']}' AND payGrp='{$_SESSION['pay_group']}' AND pdYear='{$arrPd['pdYear']}' AND pdNumber='{$arrPd['pdNumber']}')";

}
$qryIntMaxRec = "SELECT * FROM tblEmpMast 
					 WHERE compCode = '{$_SESSION['company_code']}'
					 $qryFilter
					 $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= " AND empLastName Like '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= " AND empNo = '{$_GET['txtSrch']}' ";
        	}

        }
 $qryIntMaxRec .= "order by empLastName, empFirstName, empMidName";
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT  *
		FROM tblEmpMast
		WHERE compCode='1' " ;
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= " AND empLastName Like '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= " AND empNo = '{$_GET['txtSrch']}' ";
        	}

        }
 $qryEmpList .= " AND compCode = '{$_SESSION['company_code']}' $qryFilter
				$empNo1 $empName1 $empDiv1 $empDept1 $empSect1 order by empLastName, empFirstName, empMidName limit $intOffset,$intLimit";
      	
$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);

 $fileName = $_GET['fileName'];
 $inputId = $_GET['inputId'];


?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Employee 
				Master List
                </td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="4" class="gridToolbar">
                      
						<?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
						?>
						Search
						<INPUT type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu(array("","Last Name","Employee no."),'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('main_emp_loans_emp_list_ajax.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','<?='&fileName='.$_GET['fileName'].'&inputId='.$_GET['inputId']?>','../../../images/')">					</td>
                       </tr> 
						<tr>
								<td width="1%" class="gridDtlLbl" align="center">#</td>
								<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
								<td width="65%" class="gridDtlLbl" align="center">NAME</td>
								<td class="gridDtlLbl" align="center">ACTION</td>
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
								<td class="gridDtlVal"><font class="gridDtlLblTxt">
								  <?=str_replace('Ñ','&Ntilde;',$empListVal['empLastName']. ", " . $empListVal['empFirstName'] . " " . $empListVal['empMidName'])?>
								</font></td>
								
					<td class="gridDtlVal" align="center"> <a href="#" onclick="location.href='<?=$fileName?>?hide_option=<?=$inputId?>&empNo=<?=$empListVal['empNo']?>'"><img class="actionImg" src="../../../images/application_get.png" title="Select Employee"></a>					</td>
							</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="4" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
					<tr>
						<td colspan="4" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("main_emp_loans_emp_list_ajax.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&fileName='.$_GET['fileName'].'&inputId='.$_GET['inputId']);?>						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>