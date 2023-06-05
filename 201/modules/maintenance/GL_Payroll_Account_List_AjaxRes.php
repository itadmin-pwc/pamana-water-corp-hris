<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("GL_account.obj.php");

$glAcctObj = new GLAcctObj($_GET,$_SESSION);
$pager = new AjaxPager(20,'../../../images/');

$qryIntMaxRec = "SELECT tblGLPayrollAcct.majorAcctCde,tblGLPayrollAcct.minorAcctCde, tblGLPayrollAcct.storeAcctCde
		FROM tblGLPayrollAcct LEFT OUTER JOIN
                      tblGLMajorAcct ON tblGLPayrollAcct.compCode = tblGLMajorAcct.compCode AND 
                      tblGLPayrollAcct.majorAcctCde = tblGLMajorAcct.acctCde LEFT OUTER JOIN
                      tblGLMinorAcct ON tblGLPayrollAcct.compCode = tblGLMinorAcct.compCode AND 
                      tblGLPayrollAcct.minorAcctCde = tblGLMinorAcct.acctCde LEFT OUTER JOIN
                      tblGLStoreAcct ON tblGLPayrollAcct.compCode = tblGLStoreAcct.compCode AND 
                      tblGLPayrollAcct.storeAcctCde = tblGLStoreAcct.acctCde
        WHERE tblGLPayrollAcct.compCode = '{$_SESSION['company_code']}'
        AND tblGLPayrollAcct.acctStat = 'A'
        AND tblGLMajorAcct.acctStat = 'A'
        AND tblGLMinorAcct.acctStat = 'A'
        AND tblGLStoreAcct.acctStat = 'A' ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND majorAcctCde = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND minorAcctCde = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND storeAcctCde = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 3){
        		$qryIntMaxRec .= "AND acctDesc LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        }

$resIntMaxRec = $glAcctObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryGLList = "SELECT  TOP $intLimit tblGLPayrollAcct.compCode, tblGLPayrollAcct.majorAcctCde, tblGLMajorAcct.acctDesc AS majorDesc, tblGLPayrollAcct.minorAcctCde, 
                      tblGLMinorAcct.acctDesc AS minorDesc, tblGLPayrollAcct.storeAcctCde, tblGLStoreAcct.acctDesc AS storeDesc, 
                      tblGLPayrollAcct.acctDesc AS payrollAcctDesc, tblGLPayrollAcct.acctStat
		FROM tblGLPayrollAcct LEFT OUTER JOIN
                      tblGLMajorAcct ON tblGLPayrollAcct.compCode = tblGLMajorAcct.compCode AND 
                      tblGLPayrollAcct.majorAcctCde = tblGLMajorAcct.acctCde LEFT OUTER JOIN
                      tblGLMinorAcct ON tblGLPayrollAcct.compCode = tblGLMinorAcct.compCode AND 
                      tblGLPayrollAcct.minorAcctCde = tblGLMinorAcct.acctCde LEFT OUTER JOIN
                      tblGLStoreAcct ON tblGLPayrollAcct.compCode = tblGLStoreAcct.compCode AND 
                      tblGLPayrollAcct.storeAcctCde = tblGLStoreAcct.acctCde
        WHERE tblGLPayrollAcct.compCode = '{$_SESSION['company_code']}'
        AND tblGLPayrollAcct.acctStat = 'A'
        AND tblGLMajorAcct.acctStat = 'A'
        AND tblGLMinorAcct.acctStat = 'A'
        AND tblGLStoreAcct.acctStat = 'A'
        AND tblGLPayrollAcct.seqNo NOT IN (
        									SELECT  TOP $intOffset tblGLPayrollAcct.seqNo
											FROM tblGLPayrollAcct LEFT OUTER JOIN
									                      tblGLMajorAcct ON tblGLPayrollAcct.compCode = tblGLMajorAcct.compCode AND 
									                      tblGLPayrollAcct.majorAcctCde = tblGLMajorAcct.acctCde LEFT OUTER JOIN
									                      tblGLMinorAcct ON tblGLPayrollAcct.compCode = tblGLMinorAcct.compCode AND 
									                      tblGLPayrollAcct.minorAcctCde = tblGLMinorAcct.acctCde LEFT OUTER JOIN
									                      tblGLStoreAcct ON tblGLPayrollAcct.compCode = tblGLStoreAcct.compCode AND 
									                      tblGLPayrollAcct.storeAcctCde = tblGLStoreAcct.acctCde
									        WHERE tblGLPayrollAcct.compCode = '{$_SESSION['company_code']}'
									        AND tblGLPayrollAcct.acctStat = 'A'
									        AND tblGLMajorAcct.acctStat = 'A'
									        AND tblGLMinorAcct.acctStat = 'A'
									        AND tblGLStoreAcct.acctStat = 'A' "; 

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryGLList .= "AND tblGLPayrollAcct.majorAcctCde = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryGLList .= "AND tblGLPayrollAcct.minorAcctCde = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryGLList .= "AND tblGLPayrollAcct.storeAcctCde = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 3){
        		$qryGLList .= "AND tblGLPayrollAcct.acctDesc LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        }
$qryGLList .= " ORDER BY tblGLPayrollAcct.acctDesc ) ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryGLList .= "AND tblGLPayrollAcct.majorAcctCde = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryGLList .= "AND tblGLPayrollAcct.minorAcctCde = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryGLList .= "AND tblGLPayrollAcct.storeAcctCde = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 3){
        		$qryGLList .= "AND tblGLPayrollAcct.acctDesc LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        }
$qryGLList .=	"ORDER BY tblGLPayrollAcct.acctDesc";
$resGLList = $glAcctObj->execQry($qryGLList);
$arrGLList = $glAcctObj->getArrRes($resGLList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;GL PAYROLL ACCOUNT</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar">
						<?php if($_SESSION['user_level']==1){ ?>
                        <a href="#"  class="anchor" onclick="maintGLAcct('ADD','','','','GL_Payroll_Account_List_AjaxRes.php','GlAcctCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
                        <img class="anchor" src="../../../images/add.gif">Add GL Payroll Account</a> 
                        <FONT class="ToolBarseparator">|</font>
						<!--
                        <a href="#" onclick="location.href='view_edit_employee.php?transType=add'" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Employee<a> <FONT class="ToolBarseparator">|</font>
						-->
                        <?php } ?>
						<?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$glAcctObj->DropDownMenu(array("Major GL Code","Minor GL Code","Store GL Code","GL Description"),'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('GL_Payroll_Account_List_AjaxRes.php','GlAcctCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
					</td>
					<tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="22%" class="gridDtlLbl" align="center">MAJOR CODE / DESCRIPTION</td>
						<td width="22%" class="gridDtlLbl" align="center">MINOR CODE / DESCRIPTION</td>
						<td width="22%" class="gridDtlLbl" align="center">STORE CODE / DESCRIPTION</td>
						<td width="22%" class="gridDtlLbl" align="center">DESCRIPTION </td>
						<td width="5%" class="gridDtlLbl" align="center">STATUS</td>
						<td class="gridDtlLbl" align="center">ACTION</td>
					</tr>
					<?
					if($glAcctObj->getRecCount($resGLList) > 0){
						$i=0;
						foreach ($arrGLList as $GLListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$GLListVal['majorAcctCde']."-".$GLListVal['majorDesc']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$GLListVal['minorAcctCde']."-".$GLListVal['minorDesc']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$GLListVal['storeAcctCde']."-".$GLListVal['storeDesc']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$GLListVal['payrollAcctDesc']?></font></td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<?=($GLListVal['acctStat']=='A') ? "Active" : 'Deleted'?>
							</font>
						</td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<a onclick="maintGLAcct('EDIT','<?=$GLListVal['majorAcctCde']?>','<?=$GLListVal['minorAcctCde']?>','<?=$GLListVal['storeAcctCde']?>','GL_Payroll_Account_List_AjaxRes.php','GlAcctCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="toolbarImg" src="../../../images/application_form_magnify.png" title="Edit">
							</font>
						</td>
					</tr>
					<?
                    	}
					}
					?>
					<tr>
						<td colspan="8" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("GL_Payroll_Account_List_AjaxRes.php",'GlAcctCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$glAcctObj->disConnect();?>