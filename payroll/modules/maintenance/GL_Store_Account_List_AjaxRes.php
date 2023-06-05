<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("GL_account.obj.php");

$glAcctObj = new GLAcctObj($_GET,$_SESSION);
$pager = new AjaxPager(20,'../../../images/');


$qryIntMaxRec = "SELECT * FROM tblGLStoreAcct 
			     WHERE compCode = '{$_SESSION['company_code']}' ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND acctCde LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND acctDesc LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        }

$resIntMaxRec = $glAcctObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryGLList = "SELECT TOP $intLimit *
		FROM tblGLStoreAcct
		WHERE compCode = '{$_SESSION['company_code']}'
		AND acctCde NOT IN
        (SELECT TOP $intOffset acctCde FROM tblGLStoreAcct WHERE compCode = '{$_SESSION['company_code']}'  "; 

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryGLList .= "AND acctCde LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryGLList .= "AND acctDesc LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        }
$qryGLList .= " 
				
				ORDER BY acctDesc) ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryGLList .= "AND acctCde LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryGLList .= "AND acctDesc LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        }
$qryGLList .=	"ORDER BY acctDesc";
$resGLList = $glAcctObj->execQry($qryGLList);
$arrGLList = $glAcctObj->getArrRes($resGLList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;GL STORE ACCOUNT</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar">
                    	<?php if($_SESSION['user_level']==1){ ?>
						<a href="#"  class="anchor" onclick="maintGLAcct('ADD','','GL_Store_Account_List_AjaxRes.php','GlAcctCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
                        <img class="anchor" src="../../../images/add.gif">Add GL Store Account</a> 
                        <FONT class="ToolBarseparator">|</font>
						<?php } ?>
                        <!--
                        <a href="#" onclick="location.href='view_edit_employee.php?transType=add'" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Employee<a> <FONT class="ToolBarseparator">|</font>
						-->
						<?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$glAcctObj->DropDownMenu(array("GL Code","GL Description"),'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('GL_Store_Account_List_AjaxRes.php','GlAcctCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
					</td>
					<tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="20%" class="gridDtlLbl" align="center">GL CODE</td>
						<td width="70%" class="gridDtlLbl" align="center">GL DESCRIPTION</td>
						<td width="5%" class="gridDtlLbl" align="center">STATUS</td>
						<td class="gridDtlLbl" align="center" >ACTION</td>
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
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$GLListVal['acctCde']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$GLListVal['acctDesc']?></font></td>
						<td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?=($GLListVal['acctStat']=='A') ? "Active" : 'Deleted'?></font></td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<a onclick="maintGLAcct('EDIT','<?=$GLListVal['acctCde']?>','GL_Store_Account_List_AjaxRes.php','GlAcctCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" style="cursor:pointer;"><img class="toolbarImg" src="../../../images/application_form_magnify.png" title="Edit">
							</font>
						</td>
					</tr>
					<?
                    	}
					}
					?>
					<tr>
						<td colspan="8" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("GL_Store_Account_List_AjaxRes.php",'GlAcctCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$glAcctObj->disConnect();?>