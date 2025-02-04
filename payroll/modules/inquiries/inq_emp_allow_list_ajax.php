<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("inq_emp_allow_obj.php");

$inqEmpAllowObj = new inqEmpAllowObj();
$sessionVars = $inqEmpAllowObj->getSeesionVars();
$inqEmpAllowObj->validateSessions('','MODULES');

$pager = new AjaxPager(10,'../../../images/');

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
$empNo = $_GET['empNo'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$allowType = $_GET['allowType'];
$orderBy = $_GET['orderBy'];

$urlPara = "&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&allowType=$allowType&orderBy=$orderBy&empNo=$empNo";
$url = "inq_emp_allow_list_ajax.php";
if ($empNo > "") $empNoNew = " AND tblEmpMast.empNo = '{$empNo}' "; else $empNoNew = "";
if ($empDiv > 0) $empDivNew = " AND tblEmpMast.empDiv LIKE '{$empDiv}' "; else $empDivNew = "";
if ($empDept > 0) $empDeptNew = " AND tblEmpMast.empDepCode LIKE '{$empDept}' "; else $empDeptNew = "";
if ($empSect > 0) $empSectNew = " AND tblEmpMast.empSecCode LIKE '{$empSect}' "; else $empSectNew = "";

if ($allowType > 0) $allowTypeNew = " AND tblAllowance.allowCode = '{$allowType}' AND tblAllowType.allowCode = '{$allowType}' "; else $allowTypeNew = "";

if ($orderBy==1) $orderByNew = " ORDER BY tblEmpMast.empLastName,tblEmpMast.empFirstName,tblEmpMast.empMidName, tblAllowType.allowDesc "; else $orderByNew = " ORDER BY tblAllowType.allowDesc, tblEmpMast.empLastName,tblEmpMast.empFirstName,tblEmpMast.empMidName ";
$qryIntMaxRec = "SELECT tblEmpMast.empNo, tblEmpMast.empDiv, tblEmpMast.empDepCode, tblEmpMast.empSecCode, tblAllowType.allowDesc, 
				  tblAllowance.allowAmt, tblAllowance.allowSked, tblAllowance.allowTaxTag, tblAllowance.allowPayTag, tblAllowance.allowStart, 
				  tblAllowance.allowEnd
				  FROM tblEmpMast INNER JOIN
				  tblAllowance ON tblEmpMast.empNo = tblAllowance.empNo INNER JOIN
				  tblAllowType ON tblAllowance.allowCode = tblAllowType.allowCode
				  WHERE tblAllowance.compCode = '{$sessionVars['compCode']}' AND tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblAllowType.compCode = '{$sessionVars['compCode']}' 
				  AND tblEmpMast.empPayGrp='".$_SESSION['pay_group']."' 
				  $empNoNew $empDivNew $empDeptNew $empSectNew $allowTypeNew ";
$resIntMaxRec = $inqEmpAllowObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryAllowList = "SELECT TOP $intLimit tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empDiv, tblEmpMast.empDepCode, tblEmpMast.empSecCode, tblAllowType.allowDesc, 
				  tblAllowance.allowAmt, tblAllowance.allowSked, tblAllowance.allowTaxTag, tblAllowance.allowPayTag, tblAllowance.allowStart, 
				  tblAllowance.allowEnd,tblAllowance.allowCode 
				  FROM tblEmpMast INNER JOIN 
				  tblAllowance ON tblEmpMast.empNo = tblAllowance.empNo INNER JOIN 
				  tblAllowType ON tblAllowance.allowCode = tblAllowType.allowCode 
			   WHERE tblAllowance.allowSeries NOT IN 
						  (SELECT TOP $intOffset tblAllowance.allowSeries 
						  	FROM tblEmpMast INNER JOIN 
                      	  	tblAllowance ON tblEmpMast.empNo = tblAllowance.empNo INNER JOIN 
                      	  	tblAllowType ON tblAllowance.allowCode = tblAllowType.allowCode 
				   		  WHERE tblAllowance.compCode = '{$sessionVars['compCode']}' AND tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblAllowType.compCode = '{$sessionVars['compCode']}' 
				   		  	$empNoNew $empDivNew $empDeptNew $empSectNew $allowTypeNew $groupTypeNew $orderByNew ) 
				  AND tblAllowance.compCode = '{$sessionVars['compCode']}' AND tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblAllowType.compCode = '{$sessionVars['compCode']}' 
				   AND tblEmpMast.empPayGrp='".$_SESSION['pay_group']."' 
			   	  $empNoNew $empDivNew $empDeptNew $empSectNew $allowTypeNew  $orderByNew ";
$resAllowList = $inqEmpAllowObj->execQry($qryAllowList);
$arrAllowList = $inqEmpAllowObj->getArrRes($resAllowList);
?>
<HTML>
<head>
	<script type='text/javascript' src='inq_emp_allow_js.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
				  	<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Employee Allowance Status / Deductions
						<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
						  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
						  <?=$inqEmpAllowObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					
            <td colspan="11" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printAllowList();"> 
              <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Allowance List"></a> 
              &nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; <input name="back" type="button" id="back" value="Back" onClick="location.href='inq_emp_allow.php';">
            </td>
							<? if ($orderBy==1) {?>
							<tr>
								<td width="1%" class="gridDtlLbl" align="center">#</td>
								<td width="5%" class="gridDtlLbl" align="center">EMP.NO.</td>
								<td width="15%" class="gridDtlLbl" align="center">NAME</td>
								<td width="20%" class="gridDtlLbl" align="center">DEPARTMENT</td>
								<td width="20%" class="gridDtlLbl" align="center">ALLOWANCE TYPE</td>
								<td width="10%" class="gridDtlLbl" align="center">START.</td>
								<td width="10%" class="gridDtlLbl" align="center">END</td>
								<td width="10%" class="gridDtlLbl" align="center">PAY PERIOD</td>
								<td width="10%" class="gridDtlLbl" align="center">AMOUNT</td>
								<td class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<? } else {?>
							<tr>
								<td width="1%" class="gridDtlLbl" align="center">#</td>
								<td width="20%" class="gridDtlLbl" align="center">ALLOWANCE TYPE</td>
								<td width="10%" class="gridDtlLbl" align="center">START</td>
								<td width="10%" class="gridDtlLbl" align="center">END</td>
								<td width="10%" class="gridDtlLbl" align="center">PAY PERIOD</td>
								<td width="10%" class="gridDtlLbl" align="center">AMOUNT</td>
								<td width="5%" class="gridDtlLbl" align="center">EMP.NO.</td>
								<td width="15%" class="gridDtlLbl" align="center">NAME</td>
								<td width="20%" class="gridDtlLbl" align="center">DEPARTMENT</td>
								<td class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<? } ?>
							<?
							if($inqEmpAllowObj->getRecCount($resAllowList) > 0){
								$i=0;
								foreach ($arrAllowList as $allowListVal){
									
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
								$div = $inqEmpAllowObj->getDivDescArt($sessionVars['compCode'], $allowListVal['empDiv']);
								$dept = $inqEmpAllowObj->getDeptDescArt($sessionVars['compCode'], $allowListVal['empDiv'], $allowListVal['empDepCode']);
								$sect = $inqEmpAllowObj->getSectDescArt($sessionVars['compCode'], $allowListVal['empDiv'], $allowListVal['empDepCode'], $allowListVal['empSecCode']);
								if ($allowListVal['allowSked']=="1") $allowPayTag = "1st Period";
								if ($allowListVal['allowSked']=="2") $allowPayTag = "2nd Period";
								if ($allowListVal['allowSked']=="3") $allowPayTag = "Both Period"; 
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
							<? if ($orderBy==1) { ?>
								<td class="gridDtlVal"><?=$i?></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$allowListVal['empNo']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$allowListVal['empFirstName']. " " . $allowListVal['empLastName']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$div['deptShortDesc']."/".$dept['deptShortDesc']."/".$sect['deptShortDesc']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$allowListVal['allowDesc']?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?php echo (date("m/d/Y", strtotime($allowListVal['allowStart']))!="01/01/1970"?date("m/d/Y", strtotime($allowListVal['allowStart'])):"");?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?php echo (date("m/d/Y", strtotime($allowListVal['allowEnd']))!="01/01/1970"?date("m/d/Y", strtotime($allowListVal['allowEnd'])):""); ?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$allowPayTag?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$allowListVal['allowAmt']?></font></td>
								<td class="gridDtlVal" align="center"> <a href="#" onClick="viewDetails('<?=$urlPara?>','<?=$allowListVal['empNo']?>','<?=$allowListVal['allowCode']?>','<?=$url?>','allowListCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="actionImg" src="../../../images/application_form_magnify.png" title="Allowance Details"></a></td>
							<? } else { ?>
								<td class="gridDtlVal"><?=$i?></td>								
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$allowListVal['allowDesc']?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$inqEmpAllowObj->valDateArt($allowListVal['allowStart'])?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$inqEmpAllowObj->valDateArt($allowListVal['allowEnd'])?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$allowPayTag?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=$allowListVal['allowAmt']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$allowListVal['empNo']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$allowListVal['empFirstName']. " " . $allowListVal['empLastName']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$div['deptShortDesc']."/".$dept['deptShortDesc']."/".$sect['deptShortDesc']?></font></td>
								<td class="gridDtlVal" align="center"> <a href="#" onClick="viewDetails('<?=$urlPara?>','<?=$allowListVal['empNo']?>','<?=$allowListVal['allowCode']?>','<?=$url?>','allowListCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="actionImg" src="../../../images/application_form_magnify.png" title="Allowance Details"></a></td>								
							<? } ?>
							</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="11" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="11" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("inq_emp_allow_list_ajax.php","allowListCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&allowType=".$allowType."&orderBy=".$orderBy);?>
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
		</div>
		<?$inqEmpAllowObj->disConnect();?>
		<form name="frmEmpAllow" method="post">
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		  <input type="hidden" name="allowType" id="allowType" value="<? echo $_GET['allowType']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		
		</form>
	</BODY>
</HTML>

