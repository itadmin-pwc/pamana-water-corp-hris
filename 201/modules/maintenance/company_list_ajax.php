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

$arrSrch = array('NAME','ADDRESS');
$qryIntMaxRec = "SELECT * from tblcompany";

$resIntMaxRec = $maintEmpObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryCompList = "Select *, CASE compStat
								  WHEN 'A' THEN 'Active'
								  WHEN 'D' THEN 'Deleted'
								  WHEN 'H' THEN 'Held'
								END as status from tblCompany";
         if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryCompList .= " AND compName LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryCompList .= " AND (compAddr1 LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' or compAddr2 LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%') ";
        	}
        }
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;
$qryCompList .= " order by compName limit $intOffset,$intLimit";

$resCompList = $maintEmpObj->execQry($qryCompList);
$resCompList = $maintEmpObj->getArrRes($resCompList);
?>

<HTML>
<head>


</head>
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> COMPANY</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<tr>
                             
						  	  <td colspan="9" align="center" class="gridToolbar">
                       			 
                                <div align="left">
                                <?php if($_SESSION['user_level']==1){ ?>
                                <a href="#" onClick="PopUp('company_act.php?act=AddCompany','ADD COMPANY','<?=$dedListVal['recNo']?>','company_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Company </a>|
                      			<FONT class="ToolBarseparator"></font>
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
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('company_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">                              
                                </div></td>
					  	  </tr>
						  	<tr>
								<td width="4%" class="gridDtlLbl" align="center">#</td>
								<td width="18%" class="gridDtlLbl" align="center">COMPANY</td>
								<td width="24%" class="gridDtlLbl" align="center">ADDRESS</td>
								<td width="9%" class="gridDtlLbl" align="center">TAX ID</td>
								<td width="9%" height="20" align="center" class="gridDtlLbl">SSS NO.</td>
								<td width="9%" class="gridDtlLbl" align="center">HDMF</td>
							  <td width="13%" class="gridDtlLbl" align="center">PHILHEALTH</td>
								<td width="6%" class="gridDtlLbl" align="center">STATUS</td>
								<td width="8%" class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<?
							if(count($resCompList) > 0){
								$i=0;
								foreach ($resCompList as $compVal){
								
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
							        <?= $compVal['compName'];?>
						            </div>                                </td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
								  
							      <div align="left">
							        <?=$compVal['compAddr1'] . ", " . $compVal['compAddr2']?>
						            </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  
                                  <div align="center">
                                    <?=$compVal['compTin']?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  <div align="center">
                                    <?=$compVal['compSssNo']?> 
                              </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$compVal['compPagibig']?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$compVal['compPHealth']?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$compVal['status']?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                <div align="center">
                                <?php if($_SESSION['user_level']==1){ ?>
                                <img src="../../../images/application_form_edit.png"onClick="PopUp('company_act.php?act=EditCompany&compCode=<?=$compVal['compCode']?>','EDIT COMPANY','<?=$dedListVal['recNo']?>','company_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" style="cursor:pointer;" border="0" class="actionImg" title="Edit Company" align="absbottom" />&nbsp;
                                <?php } ?>
                                <img src="../../../images/application_form_magnify.png" onClick="PopUp('company_view.php?compCode=<?=$compVal['compCode']?>','DETAILED COMPANY INFO','<?=$dedListVal['recNo']?>','company_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" style="cursor:pointer;" border="0" align="absbottom" class="actionImg" title="View Detailed Info" /></div></td>
								</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="21" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="21" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton('company_list_ajax.php','TSCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$maintEmpObj->disConnect();?>
	</BODY>
</HTML>
