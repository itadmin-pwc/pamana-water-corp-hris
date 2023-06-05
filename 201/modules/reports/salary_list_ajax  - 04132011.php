<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("movement_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$pager = new AjaxPager(10,'../../../im	ages/');

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
$empNo = $_GET['empNo'];
$empName = $_GET['empName'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$pafType = $_GET['pafType'];
if ($groupType==1) $groupName = "GROUP 1"; else $groupName = "GROUP 2"; 
$orderBy = $_GET['orderBy'];
$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $catType);
if ($empNo>"") {
	$empNo1 = " AND (tblEmpMast.empNo LIKE '{$empNo}%')";
} else {
	$empNo1 = "";
	if ($empName>"") {$empName1 = " AND (tblEmpMast.empLastName LIKE '{$empName}%' OR tblEmpMast.empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
}

if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (tblEmpMast.empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
if ($empDept>"" && $empDept>0) {$empDept1 = " AND (tblEmpMast.empDepCode = '{$empDept}')";} else {$empDept1 = "";}
if ($empSect>"" && $empSect>0) {$empSect1 = " AND (tblEmpMast.empSecCode = '{$empSect}')";} else {$empSect1 = "";}
if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
if ($orderBy==2) {$orderBy1 = " ORDER BY tblEmpMast.empNo ";} 
$type = ($_GET['type']==1) ? "hist" : "";
if ($_GET['from'] != "" && $_GET['to'] != "") {
	$fromdt = $_GET['from'];
	$todt = $_GET['to'];
	$datefilter = " and tblPAF_PayrollRelated$type.dateadded >= '$fromdt' and tblPAF_PayrollRelated$type.dateadded <='$todt'";
}

if ($_GET['code']!=0) {
	$arrReason = $inqTSObj->getReasonCd($_GET['code'],$sessionVars['compCode']);
	$codeName = $arrReason['reasonDesc'];
	$reasonfilter = "and reasonCd = '{$arrReason['reasonCd']}'";
}

$strReason = " AND tblEmpMast.empNo IN (Select empNo from tblPAF_PayrollRelated$type where compCode='{$sessionVars['compCode']}'  $reasonfilter $datefilter and new_empMrate<>0)";
if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
$qryIntMaxRec = "SELECT tblEmpMast.empNo, tblPosition.posDesc, tblEmpMast.empLastName, tblEmpMast.empFirstName, 
                      tblEmpMast.empMidName FROM  tblEmpMast LEFT OUTER JOIN
                      tblPosition ON tblEmpMast.empPosId = tblPosition.posCode AND 
                      tblEmpMast.compCode = tblPosition.compCode LEFT OUTER JOIN
                      tblDepartment ON tblEmpMast.compCode = tblDepartment.compCode AND tblEmpMast.empDiv = tblDepartment.divCode 
			     WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}' and
				 (tblDepartment.deptLevel = '1') AND (tblDepartment.compCode = '{$sessionVars['compCode']}') 
				 AND (tblPosition.compCode = '{$sessionVars['compCode']}') and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
				 $strReason $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";

$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT TOP $intLimit empPayCat,tblEmpMast.empNo, tblPosition.posDesc, tblEmpMast.empLastName, tblEmpMast.empFirstName, 
                      tblEmpMast.empMidName,tblDepartment.deptDesc
FROM         tblEmpMast LEFT OUTER JOIN
                      tblPosition ON tblEmpMast.empPosId = tblPosition.posCode AND 
                      tblEmpMast.compCode = tblPosition.compCode LEFT OUTER JOIN
                      tblDepartment ON tblEmpMast.compCode = tblDepartment.compCode AND tblEmpMast.empDiv = tblDepartment.divCode
		WHERE (tblDepartment.deptLevel = '1') AND (tblDepartment.compCode = '{$sessionVars['compCode']}')  and empNo NOT IN
        (SELECT TOP $intOffset empNo FROM tblEmpMast WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}' 
		$strReason $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 "; 
$qryEmpList .= " 
				$orderBy1) 
				AND tblEmpMast.compCode = '{$sessionVars['compCode']
				}'
				$strReason $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";
$resEmpList = $inqTSObj->execQry($qryEmpList);
$arrEmpList = $inqTSObj->getArrRes($resEmpList);
?>

<HTML>
<head>
	<script type='text/javascript' src='movement.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp; <?=$codeName. " Report"?>
				<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
				  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
				  <?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
				</div></td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<td colspan="25" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printSalary();" title="Print <?=$codeName?>"> 
							<img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print <?=$codeName?>">
                            <?=$codeName?>
							</a>&nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; 
							<input name="back" type="button" id="back" value="Back" onClick="location.href='salary.php';">            				</td>
							<tr>
								<td width="2%" class="gridDtlLbl" align="center">#</td>
							  <td width="7%" class="gridDtlLbl" align="center">EMP.NO.</td>
								<td width="12%" align="center" class="gridDtlLbl">EMPLOYEE NAME</td>
								<td width="12%" class="gridDtlLbl" align="center">EFFECTIVITY DATE</td>
							  <td width="15%" class="gridDtlLbl" align="center">POSITION</td>
							  <td width="15%" class="gridDtlLbl" align="center">DIVISION</td>
							  <td width="10%" class="gridDtlLbl" align="center">OLD SALARY</td>
							  <td width="12%" class="gridDtlLbl" align="center">NEW SALARY</td>
							  <?if($codeName != "Promotion") {?>
                              <td width="15%" class="gridDtlLbl" align="center">AMT INCREASE</td>
                              <?}?>
							</tr>
							<?
							if($inqTSObj->getRecCount($resEmpList) > 0){
								$x=0;
								$no=1;
								foreach ($arrEmpList as $empListVal){
										$resArrSalary = $inqTSObj->getSalaryData($type," where tblPAF_PayrollRelated$type.empNo='{$empListVal['empNo']}' $reasonfilter $datefilter and new_empMrate>0");
										$act=0;
										foreach($resArrSalary as $val) {
											$old_salary=$val['old_empMrate'];
											$new_salary=$val['new_empMrate'];
											if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
												
												if ($empListVal['empPayCat'] == 1) 
													$old_salary = $new_salary = "--";
											}
										$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal"><?
								if ($act==0) {
									echo $no;
								}?></td>
								<td class="gridDtlVal"><?
								if ($act==0) {
									echo $empListVal['empNo'];
								}	
								?></td>
								<td class="gridDtlVal"><?
								if ($act==0) {
									echo $empListVal['empLastName']. " " . $empListVal['empFirstName'][0] . "." . $empListVal['empMidName'][0].".";
									$act=1;
									$no++;
								}								
								?></td>
								<td class="gridDtlVal" align="right"><div align="left">
								  <?=$val['effectivitydate']?>
							    </div></td>
								<td class="gridDtlVal" align="right"><div align="left">
								  <?=$empListVal['posDesc']?>
							    </div></td>
								<td class="gridDtlVal" align="right"><div align="left">
								  <?=$empListVal['deptDesc']?>
							    </div></td>
								<td class="gridDtlVal" align="right">
                                  <div align="right">
                                    <?=$old_salary?>
                                  </div></td>
								<td class="gridDtlVal" align="right">
                                  <div align="right">
                                    <?=$new_salary?>
                                  </div></td>
						  <?if($codeName != "Promotion") {?>
								<td class="gridDtlVal" align="right">
							      <div align="right">
							        <?
									if ($new_salary !="--")
										echo number_format(((float)$val['new_empMrate'] - (float)$val['old_empMrate']),2);
									else
										echo "--";
										?>
						            </div></td>
                          <? }?>          
							</tr>
							<?
										}
									}
							}
							else{
							?>
							<tr>
								<td colspan="25" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="25" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("salary_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&to=".$todt."&orderBy=".$orderBy."&catType=".$catType."&code=".$_GET['code']."&from=".$fromdt);?>                                    								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$inqTSObj->disConnect();?>
		<form name="frmEmpList" method="post">
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empName" id="empName" value="<? echo $_GET['empName']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		  <input type="hidden" name="code" id="code" value="<? echo $_GET['code']; ?>">
		  <input type="hidden" name="from" id="from" value="<? echo $_GET['from']; ?>">
	  	  <input type="hidden" name="to" id="to" value="<? echo $_GET['to']; ?>">
	  	  <input type="hidden" name="type" id="type" value="<? echo $_GET['type']; ?>">
        </form>
	</BODY>
</HTML>
