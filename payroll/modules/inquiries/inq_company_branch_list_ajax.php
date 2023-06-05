<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("inq_company_list_obj.php");

$inqCompObj = new inqCompObj();
$sessionVars = $inqCompObj->getSeesionVars();
$inqCompObj->validateSessions('','MODULES');

$pager = new AjaxPager(10,'../../../images/');

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
$compCodeBranch = $_GET['compCodeBranch'];
$qryIntMaxRec = "SELECT * FROM tblBranch WHERE compCode = '$compCodeBranch' ORDER BY brnDesc ASC";
$resIntMaxRec = $inqCompObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qry = "SELECT TOP $intLimit * FROM tblBranch
			   WHERE brnSeries NOT IN 
						  (SELECT TOP $intOffset brnSeries 
						  	FROM tblBranch WHERE compCode = '$compCodeBranch' ORDER BY brnDesc) 
			   AND compCode = '$compCodeBranch' 
			   ORDER BY brnDesc ";
$res = $inqCompObj->execQry($qry);
$arr = $inqCompObj->getArrRes($res);
?>
<HTML>
<head>
	
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
				  	<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;<? echo $inqCompObj->getCompanyName($compCodeBranch);?>
						<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
						  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
						  <?=$inqCompObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
							<td colspan="11" class="gridToolbar" align=""> &nbsp; <a href="#" onclick="printBranchList();" title="Print Branch List"> 
								<img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Branch List">Branch List</a>
								<FONT class="ToolBarseparator">|</font> &nbsp; <input name="back" type="button" id="back" value="Back" onClick="location.href='inq_company_list.php';">
							</td>
							<tr>
								<td width="1%" class="gridDtlLbl" align="center">#</td>
								<td width="40%" class="gridDtlLbl" align="center">BRANCH</td>
								<td width="10%" class="gridDtlLbl" align="center">ALIAS</td>
								<td width="30%" class="gridDtlLbl" align="center">ADDRESS </td>
								<td class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<?
							if($inqCompObj->getRecCount($res) > 0){
								$i=0;
								foreach ($arr as $compListVal){
									
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>						
							<td class="gridDtlVal"><?=$i?></td>								
							<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$compListVal['brnDesc']?></font></td>
							<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$compListVal['brnShortDesc']?></font></td>
							<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$compListVal['brnAddr1']?></font></td>
							
              <td class="gridDtlVal" align="center"> <a href="#" title="Print Info" onclick="printBranchInfo(<?=$compListVal['brnCode']?>);"><img class="actionImg" src="../../../images/printer.png" title="Print Info"></a></td>
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
									<?$pager->_viewPagerButton("inq_company_branch_list_ajax.php","compListCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&compCodeBranch='.$compCodeBranch);?>
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
		</div>
		<?$inqCompObj->disConnect();?>
		<form name="frmCompList2" method="post">
			<input type="hidden" name="compCodeBranch" id="compCodeBranch" value="<? echo $_GET['compCodeBranch']; ?>">
		</form>
	</BODY>
</HTML>

