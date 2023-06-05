<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("trans_type.obj.php");

$trnsTypeObj = new trnsTypeObj($_GET,$_SESSION);
$pager = new AjaxPager(18,'../../../images/');

$qryIntMaxRec = "SELECT trnCode FROM tblPayTransType 
				WHERE (compCode = '{$_SESSION['company_code']}') ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND trnCode = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND trnDesc LIKE '{$_GET['txtSrch']}%' ";
        	}
        }

$resIntMaxRec = $trnsTypeObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryTrnsTypeList = "SELECT  TOP $intLimit trnCode,trnDesc,trnCat,trnGlCode,trnStat FROM tblPayTransType
		        WHERE compCode = '{$_SESSION['company_code']}'
				AND trnCode NOT IN (
									SELECT  TOP $intOffset trnCode
									FROM tblPayTransType 
									WHERE  compCode = '{$_SESSION['company_code']}' "; 

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryTrnsTypeList .= "AND trnCode = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryTrnsTypeList .= "AND trnDesc LIKE '{$_GET['txtSrch']}%' ";
        	}

        }
$qryTrnsTypeList .= "ORDER BY trnDesc ) ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 1){
        		$qryTrnsTypeList .= "AND trnCode = '{$_GET['txtSrch']}' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryTrnsTypeList .= "AND trnDesc LIKE '{$_GET['txtSrch']}%' ";
        	}

        }
$qryTrnsTypeList .=	"ORDER BY trnDesc";
$resTrnsTypeList = $trnsTypeObj->execQry($qryTrnsTypeList);
$arrTrnsTypeList = $trnsTypeObj->getArrRes($resTrnsTypeList);


?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;TRANSACTION TYPES</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar">
                    <?php if($_SESSION['user_level']==1){ ?>
						<a href="#"  class="anchor" onclick="maintTrnsType('ADD','','trans_type_listAjaxRes.php','trnsTypeMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
                        <img class="anchor" src="../../../images/add.gif">Add Transaction Type</a> 
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
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$trnsTypeObj->DropDownMenu(array("","Code","Description"),'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('trans_type_listAjaxRes.php','trnsTypeMasterCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
					</td>
					<tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="10%" class="gridDtlLbl" align="center">CODE</td>
						<td width="40%" class="gridDtlLbl" align="center">DESCRIPTION</td>
						<td width="30%" class="gridDtlLbl" align="center">GL CODE / DESCRIPTION</td>
						<td width="20%" class="gridDtlLbl" align="center">CATEGORY</td>
						<td width="22%" class="gridDtlLbl" align="center">STATUS</td>
						<td class="gridDtlLbl" align="center" colspan="42">ACTION</td>
					</tr>
					<?
					if($trnsTypeObj->getRecCount($resTrnsTypeList) > 0){
						$i=0;
						foreach ($arrTrnsTypeList as $trnsTypeListVal){
						$arrGLInfo = $trnsTypeObj->getGLInfo('tblGLMajorAcct',$trnsTypeListVal['trnGlCode']);	
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$trnsTypeListVal['trnCode']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$trnsTypeListVal['trnDesc']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$trnsTypeListVal['trnGlCode']." - ".$arrGLInfo['acctDesc']; ?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=($trnsTypeListVal['trnCat']== 'E') ? 'Earnings' : 'Deductions' ;?></font></td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<?=($trnsTypeListVal['trnStat']=='A') ? "Active" : 'Deleted'?>
							</font>
						</td>
						<td class="gridDtlVal" align="center">
							<font class="gridDtlLblTxt">
								<a onclick="maintTrnsType('EDIT','<?=$trnsTypeListVal['trnCode']?>','trans_type_listAjaxRes.php','trnsTypeMasterCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="toolbarImg" src="../../../images/application_form_magnify.png" title="Edit"></a>
							</font>
						</td>
					</tr>
					<?
                    	}
					}
					?>
					<tr>
						<td colspan="8" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("trans_type_listAjaxRes.php",'trnsTypeMasterCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$trnsTypeObj->disConnect();?>