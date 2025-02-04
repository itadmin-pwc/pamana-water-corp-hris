<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("../maintenance/maintenance_employee.Obj.php");

$maintEmpObj = new maintEmpObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
$empNo = $_GET['empNo'];
$empName = $_GET['empName'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$groupType = $_GET['groupType'];
$orderBy = $_GET['orderBy'];
$catType = $_GET['catType'];

if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
if ($groupType<3) {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
if ($catType>0) {$catType1 = " AND (empPayCat = '{$catType}')";} else {$catType1 = "";}
$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     AND empStat NOT IN('RS','IN','TR')  and empPayGrp='{$_SESSION['pay_group']}' and empPayCat='{$_SESSION['pay_category']}'
				 $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT  *
		FROM tblEmpMast
		WHERE  compCode = '{$sessionVars['compCode']}'
				AND empStat NOT IN('RS','IN','TR')  and empPayGrp='{$_SESSION['pay_group']}' and empPayCat='{$_SESSION['pay_category']}'
				$empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 ";
				
$qryEmpList .=	"ORDER BY empLastName limit $intOffset,$intLimit";
$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);

?>

<HTML>
<head>
	<script type='text/javascript' src='inq_emp_js.js'></script>
</head>
	<BODY>
		
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
							
              <td colspan="11" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printEmpList();"> 
                <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Employee List">Employee List</a>&nbsp; 
                <FONT class="ToolBarseparator">|</font> &nbsp; <a href="#" onClick="printEmpStat();"> 
                <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Employee Statistical Report">Statistical Report</a>&nbsp; 
                <FONT class="ToolBarseparator">|</font> &nbsp; 
                <input name="back" type="button" id="back" value="Back" onClick="location.href='inq_emp.php';">
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
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("�","&Ntilde;",$empListVal['empLastName']. ", " . $empListVal['empFirstName'] . " " . $empListVal['empMidName'])?></font></td>
								
					<td class="gridDtlVal" align="center"> 
						<a href="#" onClick="location.href='inq_emp.php?hide_option=new_&empNo=<?=$empListVal['empNo']?>'">
							<img class="actionImg" src="../../../images/application_get.png" title="Select Employee"></a>
						<a href="#" onClick="location.href='inq_emp_prev_employer_list.php?prevEmpNo=<?=$empListVal['empNo']?>&empNo=<?=$empNo?>&empName=<?=$empName?>&empDiv=<?=$empDiv?>&empSect=<?=$empSect?>&groupType=<?=$groupType?>&orderBy=<?=$orderBy?>&catType=<?=$catType?>'">
							<?php
								$getPrevEmp = $maintEmpObj->getcount($sessionVars['compCode'],$empListVal['empNo']);
							?>
							<img  id="imgPrevEmp" class="actionImg" src="../../../images/application_side_contract.png"  title="Previous Employer (<?php echo $getPrevEmp;?>)">
						</a> 
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
									<?$pager->_viewPagerButton("inq_emp_list_ajax.php","empMastCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&groupType=".$groupType."&orderBy=".$orderBy."&catType=".$catType);?>
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
			<span id="trail" style="visibility:hidden"></span>
		</div>
		<?$common->disConnect();?>
		<form name="frmEmpList" method="post" >
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empName" id="empName" value="<? echo $_GET['empName']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		  <input type="hidden" name="groupType" id="groupType" value="<? echo $_GET['groupType']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		  <input type="hidden" name="catType" id="catType" value="<? echo $_GET['catType']; ?>">
		</form>
	</BODY>
</HTML>
