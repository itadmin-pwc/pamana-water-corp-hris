<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("profile_paf_obj.php");

$pafObj = new pafObj($_GET,$_SESSION);
$sessionVars = $pafObj->getSeesionVars();
$pafObj->validateSessions('','MODULES');

$pager = new AjaxPager(2,'../../../im	ages/');

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');

if ($_GET['stat'] !="") {
	$stat = " AND stat='{$_GET['stat']}'";
	$statAllow = " AND stat='{$_GET['stat']}'";
}
if ($_GET['stat'] =="P") {
	$hist ='hist';
	$stat="";
}
$pafObj->arrOthers 		= $pafObj->convertArr("tblPAF_Others$hist", " $stat ");
$pafObj->arrEmpStat		= $pafObj->convertArr("tblPAF_EmpStatus$hist", " $stat ");
$pafObj->arrBranch 		= $pafObj->convertArr("tblPAF_Branch$hist", " $stat ");
$pafObj->arrPosition	= $pafObj->convertArr("tblPAF_Position$hist", " $stat ");
$pafObj->arrPayroll 	= $pafObj->convertArr("tblPAF_PayrollRelated$hist", " $stat ");
$pafObj->arrAllow 		= $pafObj->convertArr("tblPAF_Allowance$hist", " $stat ");
$arrPAF = array_unique(array_merge($pafObj->arrOthers,$pafObj->arrOthers,$pafObj->arrEmpStat,$pafObj->arrBranch,$pafObj->arrPosition,$pafObj->arrPayroll,$pafObj->arrAllow));
$strPAF = implode(",",$arrPAF);
if ($strPAF != "") {$strPAF = " AND empNo IN ($strPAF)";} else {$strPAF = "";}
if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
  $qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}'
				 $strPAF ";

$resIntMaxRec = $pafObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
$payGrp = $pafObj->getProcGrp();
$qryEmpList = "SELECT *
		FROM tblEmpMast
				Where compCode = '{$sessionVars['compCode']}' and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
				And empPayGrp<>'$payGrp'
				$strPAF ";
$resEmpList = $pafObj->execQry($qryEmpList);
$arrEmpList = $pafObj->getArrRes($resEmpList);
switch($_GET['stat']) {
	case "P":
		$dis_hold		= " disabled";
		$dis_release 	= " disabled";
		$dis_post 		= " disabled";
		$dis_releasepost= " disabled";
	break;
	case "R":
		$dis_release 	= " disabled";
		$dis_releasepost= " disabled";
	break;
	case "H":
		$dis_hold 	= " disabled";
		$dis_post= " disabled";
	break;
    
}

?>

<HTML>
<head>
	<script type='text/javascript' src='movement.js'></script>
    <style type="text/css">
<!--
.style1 {
	font-family: verdana;
	font-size: 11px;
}
-->
    </style>
</head>
	<BODY onLoad="" >
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp; PAF LIST</td>
				</tr>
				<tr>
					<td class="parentGridDtl"><form action="" method="post" name="frmPAF" id="frmPAF">
					  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
    <td colspan="25" class="gridToolbar" align="">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;&nbsp;&nbsp;<span class="style1">&nbsp;<span onClick="ChangeStat('H');" title="Held PAF" style="cursor:pointer; color: #0000FF;">Held </span>&nbsp;&nbsp;|&nbsp; &nbsp;<span onClick="ChangeStat('R');" title="Released PAF" style="cursor:pointer; color: #0000FF;">Released &nbsp;&nbsp;|&nbsp; &nbsp;</span></span><span class="style1" style="cursor:pointer; color: #0000FF;" title="Posted PAF" onClick="ChangeStat('P');">Posted</span><span class="style1">&nbsp;
          </span></td>
          <td><div align="right"><span>
          <?php if (($_SESSION['user_level'] == 1)||($_SESSION['user_level'] == 2)) 
				{   	
		   ?>
              <input type="button" class="style1" name="btnHeld" id="btnHeld" <?=$dis_hold?>  onClick="Release('H')" value="Hold">
             <input type="button" class="style1" name="btnRel" id="btnRel" <?=$dis_release?> onClick="Release('R')" value="Release">
            <input class="style1" type="button" name="btnUp" id="btnUp" <?=$dis_post?> onClick="Release('U')"  value="Post">
            <input type="button" class="style1" name="btnRelUp" id="btnRelUp" <?=$dis_releasepost?>  onClick="Release('UP')" value="Release &amp; Post">
           <?php } ?> 
            <input class="style1" type="button" name="btnUp2" onClick="printPAF()" id="btnUp2"  value="Print">
            <input type="hidden" name="checker" id="checker" value="0">
            <input type="hidden" name="pafStat" id="pafStat" value="<?=$_GET['stat']?>">
                </span></div></td>
        </tr>
      </table></td>
  <tr>
    <td colspan="25" class="gridToolbar" align=""><div align="right">
      
    </div></td>
  <tr>
    <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" onChange="CheckAll()"value="1"  name="chAll" id="chAll"></td>
    <td width="6%" class="gridDtlLbl" align="center">EMP.NO.</td>
    <td width="11%" align="center" class="gridDtlLbl">EMPLOYEE NAME</td>
    <td width="11%" class="gridDtlLbl" align="center"><input type="hidden" value="<?=$_SESSION['company_code']?>" name="compCode" id="compCode">
      MOVEMENT TYPE</td>
    <td width="8%" class="gridDtlLbl" align="center">REF. NO.</td>
    <td width="18%" class="gridDtlLbl" align="center">OLD VALUE</td>
    <td width="18%" class="gridDtlLbl" align="center">NEW VALUE</td>
    <td width="11%" class="gridDtlLbl" align="center">Effectivity Date</td>
    <td width="15%" class="gridDtlLbl" align="center">Status</td>
  </tr>
  <?
							if($pafObj->getRecCount($resEmpList) > 0){
								$x=0;
								$no=1;
								$q=0;
								foreach ($arrEmpList as $empListVal){
										$resArrOthers = $pafObj->getPAF_others($empListVal['empNo'],$pafType,"  $stat",$hist);
										$ctr=count($resArrOthers['value1']);
										$type = "";
										for($x=0;$x<$ctr; $x++) {
										$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
  <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
    <td height="20" class="gridDtlVal"><div align="center">
      <? 
								 
								  if ($resArrOthers['type'][$x] != $type) {?>
      <input type="checkbox" value="<?=$resArrOthers['type'][$x];?>" onClick="check(this.name);" name="chPAF<?=$q?>" id="chPAF<?=$q?>">
      <?
                                  	$q++;
								  }?>
    </div></td>
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
    <td class="gridDtlVal" align="right"><div align="center">
      <?=$resArrOthers['refno'][$x]?>
    </div></td>
    <td class="gridDtlVal" align="right"><div align="left">
      <?=$resArrOthers['value1'][$x]?>
    </div></td>
    <td class="gridDtlVal" align="right"><div align="left">
      <?=$resArrOthers['value2'][$x]?>
    </div></td>
    <td class="gridDtlVal" align="right"><div align="center">
      <?=date("m/d/Y",strtotime($resArrOthers['effdate'][$x]))?>
    </div></td>
    <td class="gridDtlVal" align="right"><div align="center">
      <?
	  if ($_GET['stat'] != 'P')
	     	echo ($resArrOthers['stat'][$x]=="H"? "Held":"Released");
	  else
	  		echo "Posted";	  
		  
		  ?>
    </div></td>
  </tr>
  <?
											$type = $resArrOthers['type'][$x];
										}
									}
							}
							else{
							?>
  <tr>
    <td colspan="25" align="center"><FONT class="zeroMsg">NOTHING TO DISPLAY</font> </td>
  </tr>
  <?}?>
  <tr>
    <td colspan="25" align="center" class="childGridFooter"><input type="hidden" value="<?=$q;?>" name="chCtr" id="chCtr"></td>
  </tr>
                      </TABLE>
                                                            </form>
					</td>
			  </tr>
			</TABLE>
		</div>
		<?$pafObj->disConnect();?>
</BODY>
</HTML>
