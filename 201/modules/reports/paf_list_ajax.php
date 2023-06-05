<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("movement_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$pager = new AjaxPager(2,'../../../im	ages/');

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
$empNo = $_GET['empNo'];
$empName = $_GET['empName'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$pafType = $_GET['pafType'];
$type = ($_GET['type']==1) ? "hist" : "";
if ($groupType==1) $groupName = "GROUP 1"; else $groupName = "GROUP 2"; 
$orderBy = $_GET['orderBy'];
$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $catType);
if ($empNo>"") {
	$empNo1 = " AND (tblEmpMast.empNo LIKE '{$empNo}%')";
} else {
	$empNo1 = "";
	if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
}

if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
if ($_GET['from'] != "" && $_GET['to'] != "") {
	$fromdt = $_GET['from'];
	$todt = $_GET['to'];
	$datefilter = " and dateupdated >= '$fromdt' and dateupdated <='$todt'";
}
if (empty($pafType) || $pafType =="others") {
	$inqTSObj->arrOthers 		= $inqTSObj->convertArr("tblPAF_Others$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="empstat") {
	$inqTSObj->arrEmpStat 		= $inqTSObj->convertArr("tblPAF_EmpStatus$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="branch") {	
	$inqTSObj->arrBranch 		= $inqTSObj->convertArr("tblPAF_Branch$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="position") {	
	$inqTSObj->arrPosition 		= $inqTSObj->convertArr("tblPAF_Position$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="payroll") {
	$inqTSObj->arrPayroll 		= $inqTSObj->convertArr("tblPAF_PayrollRelated$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="allow") {
	$inqTSObj->arrAllow 		= $inqTSObj->convertArr("tblPAF_Allowance$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
$arrPAF = array_unique(array_merge($inqTSObj->arrOthers,$inqTSObj->arrOthers,$inqTSObj->arrEmpStat,$inqTSObj->arrBranch,$inqTSObj->arrPosition,$inqTSObj->arrPayroll,$inqTSObj->arrAllow));
$strPAF = implode(",",$arrPAF);
if ($strPAF != "") {$strPAF = " AND empNo IN ($strPAF)";} else {$strPAF = "";}
if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
 $qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}'
				 $strPAF $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";

$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT TOP $intLimit *
		FROM tblEmpMast
		WHERE empNo NOT IN
        (SELECT TOP $intOffset empNo FROM tblEmpMast WHERE compCode = '{$sessionVars['compCode']}' 
		and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
		$strPAF $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 "; 
$qryEmpList .= " 
				$orderBy1) 
				AND compCode = '{$sessionVars['compCode']}' and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
				$strPAF $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";
$resEmpList = $inqTSObj->execQry($qryEmpList);
$arrEmpList = $inqTSObj->getArrRes($resEmpList);

//<?=date("M", strtotime($arrPayPd['perMonth']);
?>

<HTML>
<head>
	<script type='text/javascript' src='movement.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp; EMPLOYEE MOVEMENT
				<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
				  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
				  <?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
				</div></td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<td colspan="23" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printEmpMovement();" title="Print Employee Movement"> 
							<img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Employee Movement">Employee Movement
							</a>&nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; 
							<input name="back" type="button" id="back" value="Back" onClick="location.href='paf.php';">            				</td>
							<tr>
								<td width="2%" class="gridDtlLbl" align="center">#</td>
							  <td width="10%" class="gridDtlLbl" align="center">EMP.NO.</td>
								<td width="16%" align="center" class="gridDtlLbl">EMPLOYEE NAME</td>
								<td width="16%" class="gridDtlLbl" align="center">MOVEMENT TYPE</td>
							  <td width="21%" class="gridDtlLbl" align="center">OLD VALUE</td>
							  <td width="20%" class="gridDtlLbl" align="center">NEW VALUE</td>
							  <td width="15%" class="gridDtlLbl" align="center">Effectivity Date</td>
							</tr>
							<?
							if($inqTSObj->getRecCount($resEmpList) > 0){
								$x=0;
								$no=1;
								foreach ($arrEmpList as $empListVal){
										$resArrOthers = $inqTSObj->getPAF_others($empListVal['empNo'],$pafType,$datefilter,$type);
										$ctr=count($resArrOthers['value1']);
										for($x=0;$x<$ctr; $x++) {
										$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal"><?
								if ($x==0) {
									echo $no;
								}?></td>
								<td class="gridDtlVal"><?
                                if ($x==0) {
								$no++;
								echo $empListVal['empNo'];
								}?></td>
								<td class="gridDtlVal"><?
                                if ($x==0) {
									echo $empListVal['empLastName']. " " . $empListVal['empFirstName'][0] . "." . $empListVal['empMidName'][0].".";
								}
								?></td>
								<td class="gridDtlVal" align="right"><div align="left">
								  <?=$resArrOthers['field'][$x]?>
							    </div></td>
								<td class="gridDtlVal" align="right"><div align="left">
								  <?=$resArrOthers['value1'][$x]?>
							    </div></td>
								<td class="gridDtlVal" align="right"><div align="left">
								  <?=$resArrOthers['value2'][$x]?>
							    </div></td>
								<td class="gridDtlVal" align="right">
								  <div align="center">
								    <?=date("m/d/Y",strtotime($resArrOthers['effdate'][$x]))?>
						          </div></td>
							</tr>
							<?
										}
									}
							}
							else{
							?>
							<tr>
								<td colspan="23" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="23" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("paf_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&to=".$todt."&orderBy=".$orderBy."&catType=".$catType."&pafType=".$pafType."&from=".$fromdt);?>                                  
                                    								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$inqTSObj->disConnect();?>
</BODY>
</HTML>
