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

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
$prevEmpNo = $_GET['prevEmpNo'];
$empNo = $_GET['empNo'];
$empName = $_GET['empName'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$groupType = $_GET['groupType'];
$orderBy = $_GET['orderBy'];
$catType = $_GET['catType'];

$qryIntMaxRec = "SELECT * FROM tblPrevEmployer 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     AND prevStat = 'A' 
				 AND empNo = '{$prevEmpNo}' 
				 ORDER BY prevEmplr ASC";

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryPrevList = "SELECT TOP $intLimit *
		FROM tblPrevEmployer
		WHERE seqNo NOT IN
        (SELECT TOP $intOffset seqNo FROM tblPrevEmployer WHERE prevStat = 'A' "; 
$qryPrevList .= " 
				AND compCode = '{$sessionVars['compCode']}' 
				AND empNo = '{$prevEmpNo}' 
				ORDER BY prevEmplr ASC) 
				AND compCode = '{$sessionVars['compCode']}'
				AND prevStat = 'A'  
				AND empNo = '{$prevEmpNo}' 
				ORDER BY prevEmplr ASC ";
$resPrevList = $common->execQry($qryPrevList);
$arrPrevList = $common->getArrRes($resPrevList);
?>

<HTML>
<head>
	<script type='text/javascript' src='inq_emp_js.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;<? $name = $maintEmpObj->getUserInfo($sessionVars['compCode'],$prevEmpNo,""); echo $name['empLastName'].", ".$name['empFirstName']." ".$name['empMidName']?>
				<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
				  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
				  <?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
				</div></td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
							
              <td colspan="12" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printPrevList();" title="Prev.Employer List"> 
                <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Previous Employer List">Prev.Employer List</a>&nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; 
<input name="back" type="button" id="back" value="Back" onClick="returnEmpList();">            				</td>
							<tr>
								<td width="2%" class="gridDtlLbl" align="center">#</td>
								<td width="36%" class="gridDtlLbl" align="center">PREVIOUS EMPLOYER</td>
								<td width="7%" class="gridDtlLbl" align="center">YEAR</td>
								<td width="36%" class="gridDtlLbl" align="center">ADDRESS</td>
								<td width="7%" class="gridDtlLbl" align="center">PREVIOUS EARNINGS</td>
								<td width="7%" class="gridDtlLbl" align="center">PREVIOUS TAXES</td>
								<td width="5%" align="center" class="gridDtlLbl">ACTION</td>
							</tr>
							<?
							if($common->getRecCount($resPrevList) > 0){
								$i=0;
								foreach ($arrPrevList as $prevListVal){
									
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal"><?=$i?></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$prevListVal['prevEmplr']?></font></td>
								<td class="gridDtlVal"><div align="center"><font class="gridDtlLblTxt">
							    <?=$prevListVal['yearCd']?>
							    </font></div></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$prevListVal['empAddr1']?></font></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$prevListVal['prevEarnings']?></font></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$prevListVal['prevTaxes']?></font></td>
								
					
              <td class="gridDtlVal" align="center"> <a href="#" title="Previous Employer Info."onclick="printPrevInfo(<?=$prevListVal['seqNo']?>);"> 
                <img class="actionImg" src="../../../images/printer.png" title="Print Previous Employer Info."></a>              </td>
							</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="7" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="7" align="center" class="childGridFooter">
									<?$pager->_viewPagerButton("inq_emp_prev_employer_list_ajax.php","empMastCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&prevEmpNo='.$prevEmpNo.'&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&groupType=".$groupType."&orderBy=".$orderBy."&catType=".$catType);?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$common->disConnect();?>
		<form name="frmEmpList" method="post">
		  <input type="hidden" name="prevEmpNo" id="prevEmpNo" value="<? echo $_GET['prevEmpNo']; ?>">
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
