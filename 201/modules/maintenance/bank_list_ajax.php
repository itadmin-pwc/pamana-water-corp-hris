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

$arrSrch = array('BANK','BRANCH','ADDRESS');
$qryIntMaxRec = "Select * from tblPaybank where compCode='{$_SESSION['company_code']}'";

$resIntMaxRec = $maintEmpObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryBankList = "Select *,CASE bankStat WHEN 'A' THEN 'Active' WHEN 'H' THEN 'Held' END as Stat 
				from tblPayBank 
				where compCode='{$_SESSION['company_code']}' ";
         if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryBankList .= "AND bankDesc LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryBankList .= "AND bankBrn LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryBankList .= " AND (bankAddr1 LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' OR bankAddr2 LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' OR bankAddr3 LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%')";
        	}
        }
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;
$qryBankList .= " order by bankDesc limit $intOffset,$intLimit";
		
$resBankList = $maintEmpObj->execQry($qryBankList);
$resBankList = $maintEmpObj->getArrRes($resBankList);
?>

<HTML>
<head>


</head>
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> BANKS</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<tr>
						  	  <td colspan="8" align="center" class="gridToolbar">
                              <div align="left">
                       			<?php if($_SESSION['user_level']==1){ ?>
                                <a href="#" onClick="PopUp('bank_act.php?act=AddBank','ADD BANK','<?=$dedListVal['recNo']?>','bank_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Bank</a> |
                      
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
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('bank_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">                              
                                </div></td>
					  	  </tr>
						  	<tr>
								<td width="3%" class="gridDtlLbl" align="center">#</td>
								<td width="20%" class="gridDtlLbl" align="center">BANK DESCRIPTION</td>
								<td width="11%" class="gridDtlLbl" align="center">BANK BRANCH</td>
								<td width="15%" height="20" align="center" class="gridDtlLbl">ADDRESS 1</td>
								<td width="15%" class="gridDtlLbl" align="center">ADDRESS 2</td>
								<td width="29%" class="gridDtlLbl" align="center">ADDRESS 3</td>
								<td width="7%" class="gridDtlLbl" align="center">STATUS</td>
								<td width="7%" class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<?
							if(count($resBankList) > 0){
								$i=0;
								foreach ($resBankList as $empBankVal){
								
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
							        <?=$empBankVal['bankDesc']?>
						            </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  <div align="left">
                                    <?=$empBankVal['bankBrn']?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  <div align="left">
                                    <?=$empBankVal['bankAddr1']?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  <div align="left">
                                    <?=$empBankVal['bankAddr2']?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="left">
                                  <?=$empBankVal['bankAddr3'];?>
                                </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  <div align="center">
                                    <?=$empBankVal['Stat'];?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center"><a href="#" onClick="PopUp('bank_act.php?act=EditBank&bankCd=<?=$empBankVal['bankCd']?>','EDIT BANK','<?=$dedListVal['recNo']?>','bank_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_magnify.png" border="0" class="actionImg" title="View Detailed Info" /></a></div></td>
								</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="20" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="20" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton('bank_list_ajax.php','TSCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$maintEmpObj->disConnect();?>
	</BODY>
</HTML>
