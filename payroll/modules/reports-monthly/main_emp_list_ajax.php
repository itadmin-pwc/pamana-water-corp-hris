<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");

$maintEmpLoanObj = new inqTSObj();
$sessionVars = $maintEmpLoanObj->getSeesionVars();
$maintEmpLoanObj->validateSessions('','MODULES');

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
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

$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     AND empStat NOT IN('RS','IN','TR')  and empPayGrp='{$_SESSION['pay_group']}' and empPayCat='{$_SESSION['pay_category']}'
				 $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 ";

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT TOP $intLimit *
		FROM tblEmpMast
		WHERE empNo NOT IN
        (SELECT TOP $intOffset empNo FROM tblEmpMast WHERE empStat NOT IN('RS','IN','TR') and empPayGrp='{$_SESSION['pay_group']}' and empPayCat='{$_SESSION['pay_category']}'
		$empNo1 $empName1 $empDiv1 $empDept1 $empSect1 "; 
$qryEmpList .= " 
				AND compCode = '{$sessionVars['compCode']}' 
				ORDER BY empNo) 
				AND compCode = '{$sessionVars['compCode']}'
				AND empStat NOT IN('RS','IN','TR') and empPayGrp='{$_SESSION['pay_group']}' and empPayCat='{$_SESSION['pay_category']}'
				$empNo1 $empName1 $empDiv1 $empDept1 $empSect1 ";
      	
$qryEmpList .=	"ORDER BY empLastName";
$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);

$fileName = $_GET['fileName'];
$inputId = $_GET['inputId'];

$topType = $_GET['topType'];
$payPd = $_GET['payPd'];

$search = "&payPd=".$payPd."&reportType=".$reportType."&topType=".$topType."&orderBy=".$orderBy."";
?>


		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Employee 
				Master List
				<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
				  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
				  <?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
				</div></td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="4" class="gridToolbar"> 
						<a href="#" onclick="location.href='view_edit_employee.php?transType=add'" class="anchor" >
					</td>
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
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace('Ñ','&Ntilde;',$empListVal['empLastName']. ", " . $empListVal['empFirstName'] . " " . $empListVal['empMidName'])?></font></td>
								
					<td class="gridDtlVal" align="center"> <a href="#" onclick="location.href='<?=$fileName?>?hide_option=new_&empNo=<?=$empListVal['empNo']?><?php echo $search; ?>'"><img class="actionImg" src="../../../images/application_get.png" title="Select Employee"></a> 
					</td>
							</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="4" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="4" align="center" class="childGridFooter">
									<?$pager->_viewPagerButton("main_emp_list_ajax.php","empMastCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&fileName=".$fileName."&inputId=".$inputId."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect);?>
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
		</div>
		<?$common->disConnect();?>
