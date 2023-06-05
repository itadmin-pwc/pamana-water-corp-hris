<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');

$pager = new AjaxPager(20,'../../../images/');

$arrSrch = array('ARTICLE','SECTION');
$qryIntMaxRec = "Select * from tblArticle where compCode='{$_SESSION['company_code']}'";

$resIntMaxRec = $maintEmpObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryArticleList = "Select *,CASE stat WHEN 'A' THEN 'Active' WHEN 'H' THEN 'Held' END as ArticleStat 
				   from tblArticle 
				   where compCode='{$_SESSION['company_code']}' ";
         if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryArticleList .= " AND article LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryArticleList .= "AND sections LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%'";
        	}			
        }	
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;		
$qryArticleList .="order by article limit $intOffset,$intLimit";
$resArticleList = $maintEmpObj->execQry($qryArticleList);
$arrArticleList = $maintEmpObj->getArrRes($resArticleList);
?>

<HTML>
<head>


</head>
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">ARTICLE/SECTION/VIOLATION</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<tr>
						  	  <td colspan="6" align="center" class="gridToolbar">
                              <div align="left">
                       			<?php if($_SESSION['user_level']==1){ ?>
                                <a href="#" onClick="PopUp('article_act.php?act=AddArticle','ADD ARTICLE','<? //=$dedListVal['recNo']?>','article_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Article/Section/Violation</a> |
                      
                                  <FONT class="ToolBarseparator">|</font>
                                  <?php } ?>
						          <?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
							
							
					
						?>
						Search
						<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">
						In
						<?=$maintEmpObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('article_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">                              
                                </div></td>
					  	  </tr>
						  	<tr>
								<td width="2%" class="gridDtlLbl" align="center">#</td>
								<td width="11%" class="gridDtlLbl" align="center">ARTICLE</td>
								<td width="14%" class="gridDtlLbl" align="center">SECTION</td>
								<td width="57%" height="20" align="center" class="gridDtlLbl">VIOLATION</td>
								<td width="7%" class="gridDtlLbl" align="center">STATUS</td>
								<td width="9%" class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<?
							if(count($arrArticleList) > 0){
								$i=0;
								foreach ($arrArticleList as $empArticleVal){
								
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$i?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
								  
							      <div align="left">
							        <?=$empArticleVal['article']?>
						            </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  <div align="left">
                                    <?=$empArticleVal['sections']?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  <div align="left">
                                    <?=$empArticleVal['violation']?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$empArticleVal['ArticleStat']?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center"><a href="#" onClick="PopUp('article_act.php?act=EditArticle&articleId=<?=$empArticleVal['article_Id']?>','EDIT ARTICLE','<? //=$dedListVal['recNo']?>','article_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_edit.png" border="0" class="actionImg" title="Edit Article" /></a></div></td>
								</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="18" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="18" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton('article_list_ajax.php','TSCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$maintEmpObj->disConnect();?>
	</BODY>
</HTML>
