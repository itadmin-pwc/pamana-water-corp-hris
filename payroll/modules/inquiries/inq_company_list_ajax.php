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

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');

$qryIntMaxRec = "SELECT * FROM tblCompany ORDER BY compName ASC";
$resIntMaxRec = $inqCompObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qry = "SELECT TOP $intLimit * FROM tblCompany
			   WHERE compCode NOT IN 
						  (SELECT TOP $intOffset compCode 
						  	FROM tblCompany ORDER BY compName) 
				  ORDER BY compName ";
$res = $inqCompObj->execQry($qry);
$arr = $inqCompObj->getArrRes($res);
?>
<HTML>
<head>
	<script type='text/javascript'>
		function printCompanyList() {
			document.frmCompList.action = 'inq_company_list_pdf.php';
			document.frmCompList.target = "_blank";
			document.frmCompList.submit();
			document.frmCompList.action = "inq_company_list_ajax.php";
			document.frmCompList.target = "_self";
		}
	</script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
				  	<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Company List
						<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
						  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
						  <?=$inqCompObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					
              <td colspan="11" class="gridToolbar" align=""> &nbsp; <a href="#" onclick="printCompanyList();"> 
                <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Company List"></a></td>
							<tr>
								<td width="1%" class="gridDtlLbl" align="center">#</td>
								<td width="30%" class="gridDtlLbl" align="center">COMPANY</td>
								<td width="30%" class="gridDtlLbl" align="center">ADDRESS 1</td>
								<td width="30%" class="gridDtlLbl" align="center">ADDRESS 2</td>
								<td width="10%" class="gridDtlLbl" align="center">TIN</td>
								<td width="10%" class="gridDtlLbl" align="center">SSS</td>
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
							<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$compListVal['compName']?></font></td>
							<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$compListVal['compAddr1']?></font></td>
							<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$compListVal['compAddr2']?></font></td>
							<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$compListVal['compTin']?></font></td>
							<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$compListVal['compSssNo']?></font></td>
							<td class="gridDtlVal" align="center"> <a href="#" onclick="location.href='inq_company_branch_list.php?compCodeBranch=<?=$compListVal['compCode']?>'"><img class="actionImg" src="../../../images/application_form_magnify.png" title="Company Branches"></a></td>												
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
									<?$pager->_viewPagerButton("inq_company_list_ajax.php","compListCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
		</div>
		<?$inqCompObj->disConnect();?>
		<form name="frmCompList2" method="post">
		</form>
	</BODY>
</HTML>

