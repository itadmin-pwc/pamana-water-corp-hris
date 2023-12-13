<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("profile_paf_obj.php");

$pafObj = new pafObj($_GET,$_SESSION);
$sessionVars = $pafObj->getSeesionVars();
$pafObj->validateSessions('','MODULES');
$empNo = $_SESSION['strprofile'];

$pager = new AjaxPager(2,'../../../im	ages/');

$hist = "hist";

$pafObj->arrOthers 		= $pafObj->convertArr2("tblPAF_Others$hist", $empNo);
$pafObj->arrEmpStat		= $pafObj->convertArr2("tblPAF_EmpStatus$hist", $empNo);
$pafObj->arrBranch 		= $pafObj->convertArr2("tblPAF_Branch$hist", $empNo);
$pafObj->arrPosition	= $pafObj->convertArr2("tblPAF_Position$hist", $empNo);
$pafObj->arrPayroll 	= $pafObj->convertArr2("tblPAF_PayrollRelated$hist", $empNo);
$pafObj->arrAllow 		= $pafObj->convertArr2("tblPAF_Allowance$hist", $empNo);
$arrPAF = array_unique(array_merge($pafObj->arrOthers,$pafObj->arrEmpStat,$pafObj->arrBranch,$pafObj->arrPosition,$pafObj->arrPayroll,$pafObj->arrAllow));

// $strPAF = implode(",",$arrPAF);
// if ($strPAF != "") {$strPAF = " AND empNo IN ($strPAF)";} else {$strPAF = "";}
$strPAF = " AND empNo = '{$empNo}'";
if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
  $qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}'
				 $strPAF ";
				 
$resIntMaxRec = $pafObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
$payGrp = $pafObj->getProcGrp();

$qryEmpList = "SELECT *, tblBranch.brnDesc
FROM         tblEmpMast INNER JOIN
                      tblBranch ON tblEmpMast.empBrnCode = tblBranch.brnCode
				Where tblEmpMast.compCode = '{$sessionVars['compCode']}' and tblEmpMast.empNo = '{$empNo}'";
$resEmpList = $pafObj->execQry($qryEmpList);
$arrEmpList = $pafObj->getArrRes($resEmpList);
?>
<html>
<head>
<style type="text/css">
<!--
.style1 {
	font-family: verdana;
	font-size: 11px;
	font-weight: bold;
}
.style2 {font-size: 8px}
-->
</style>
</head>
<body>    
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" >
    	<tr><td height="10" colspan="4"></td></tr>
		<tr>
			<td colspan="4">&nbsp;<img src="../../../images/grid.png"><span class="style1">&nbsp;Employee History List</span></td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td class="gridDtlLbl" align="center" width="12%">MOVEMENT TYPE</td>
						<td  class="gridDtlLbl" align="center" width="12%">REF. NO.</td>
						<td  class="gridDtlLbl" align="center" width="17%">OLD VALUE</td>
						<td  class="gridDtlLbl" align="center" width="10%">NEW VALUE</td>
						<td  class="gridDtlLbl" align="center" width="17%">EFFECTIVITY DATE</td>
					</tr>
					<?
						if($pafObj->getRecCount($resEmpList) > 0){
							$x=0;
							$no=1;
							$q=0;
								foreach ($arrEmpList as $empListVal){
										$resArrOthers = $pafObj->getPAF_others2($empListVal['empNo'], '',$hist);
										$ctr=count($resArrOthers['value1']);
										$type = "";
										for($x=0;$x<$ctr; $x++) {
										$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
  		<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> title="<?= htmlentities($empListVal['brnDesc']) . " BRANCH";?>">
			<td class="gridDtlVal" align="right"><div align="left">
			<?=$resArrOthers['field'][$x]?>
			</div></td>
			<td class="gridDtlVal" align="right"><div align="center">
			<?=$resArrOthers['refno'][$x]?>
			</div></td>
			<td class="gridDtlVal" align="right">
				<div align="left">
					<?=ucwords(strtolower(htmlentities($resArrOthers['value1'][$x])))?>
				</div>
			</td>
			<td class="gridDtlVal" align="right">
				<div align="left">
					<?=ucwords(strtolower(htmlentities($resArrOthers['value2'][$x])))?>
				</div>
			</td>
			<td class="gridDtlVal" align="right">
				<div align="center">
					<?=date("Y-m-d",strtotime($resArrOthers['effdate'][$x]))?>	
				</div>
			</td>
  		</tr>
  		<?
			$type = $resArrOthers['type'][$x];
					}
				}
		}
		else{
		?>
  <tr>
    <td colspan="26" align="center"><FONT class="zeroMsg">NOTHING TO DISPLAY</font> </td>
  </tr>
  <?}?>
  <tr>
    <td colspan="26" align="center" class="childGridFooter"><input type="hidden" value="<?=$q;?>" name="chCtr" id="chCtr"></td>
  </tr>
                      </TABLE>
                                                            </form>
					</td>
			  </tr>
			</TABLE>
		</div>
		<?$pafObj->disConnect();?>
</body>
</html>
