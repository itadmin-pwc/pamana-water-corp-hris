<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');

$pager = new AjaxPager(24,'../../../images/');

$arrSrch = array('NAME','ADDRESS');
$qryIntMaxRec = "SELECT * from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payGrp='{$_SESSION['pay_group']}' and payCat='{$_SESSION['pay_category']}' ";

$resIntMaxRec = $maintEmpObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryPDList = "Select *, CASE pdStat
								  WHEN 'H' THEN 'Held'
								  WHEN 'O' THEN 'Open'
								  WHEN 'C' THEN 'Closed'
								END as status from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payGrp='{$_SESSION['pay_group']}' and payCat='{$_SESSION['pay_category']}' order by pdyear  desc,payCat , pdNumber Limit $intOffset,$intLimit";
$resPDList = $maintEmpObj->execQry($qryPDList);
$resPDList = $maintEmpObj->getArrRes($resPDList);
$userList = $maintEmpObj->getUserName();

?>

<HTML>
<head>


</head>
	<BODY>
   		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> PAYROLL PERIOD</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<tr>
						  	  <td colspan="13" align="center" class="gridToolbar">
                       
                                <div align="left">
                                <?php //if($_SESSION['user_level']==1){ ?>
                                <a href="#" onClick="PopUp('period_act.php?act=Generate','GENERATE PAY PERIOD','','period_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Generate Period </a><span style="visibility:hidden;">
                      			<FONT class="ToolBarseparator">|</font>
                                <?php //} ?>
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
                                </span></div></td>
					  	  </tr>
						  	<tr>
								<td width="2%" class="gridDtlLbl" align="center">#</td>
								<td width="6%" class="gridDtlLbl" align="center">PAY CAT</td>
								<td width="4%" class="gridDtlLbl" align="center">YEAR</td>
							  <td width="4%" class="gridDtlLbl" align="center">NUMBER</td>
								<td width="7%" class="gridDtlLbl" align="center">PAYROLL DATE</td>
								<td width="7%" height="20" align="center" class="gridDtlLbl">FROM</td>
								<td width="6%" class="gridDtlLbl" align="center">TO</td>
							  <td width="9%" class="gridDtlLbl" align="center"> DATE PROCESSED</td>
								<td width="12%" class="gridDtlLbl" align="center">PROCESSED BY</td>
							  <td width="6%" class="gridDtlLbl" align="center">DATE CLOSED</td>
							  <td width="11%" class="gridDtlLbl" align="center">CLOSED BY</td>
								<td width="6%" class="gridDtlLbl" align="center">STATUS</td>
								<td width="6%" class="gridDtlLbl" align="center">ACTION</td>
					      </tr>
							<?
							if(count($resPDList) > 0){
								$i=0;
								foreach ($resPDList as $pdVal){
								
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';			
								$empName_processed = $empName_close ="";
								if(trim($pdVal['pdClosedBy'])!="")
								{
									$dispEmp =$maintEmpObj->getUserInfo($_SESSION["company_code"] , $pdVal['pdClosedBy'], ""); 
									$empName_close = $dispEmp['empLastName']." ".$dispEmp['empFirstName'][0].". ".$dispEmp['empMidName'][0].".";
								}
								
								
								if(trim($pdVal['pdProcessedBy'])!="")
								{
									$dispEmp =$maintEmpObj->getUserInfo($_SESSION["company_code"] , $pdVal['pdProcessedBy'], ""); 
									$empName_processed = $dispEmp['empLastName']." ".$dispEmp['empFirstName'][0].". ".$dispEmp['empMidName'][0].".";
								}
													
							?>
                            <?php
							  	echo "<tr bgcolor=".$bgcolor." ".$on_mouse.">";
							?>
                                <tr>
                                  <td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$i?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
                                  <?= $pdVal['payCat'];?>
                                </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
									
						          <div align="center">
						            <?= $pdVal['pdYear'];?>
					              </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
								  
						          <div align="center">
						            <?=$pdVal['pdNumber']?>
					              </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  
                                  <div align="center">
                                    <?=date("m/d/Y", strtotime($pdVal['pdPayable']))?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  
                                  <div align="center">
                                    <?=date("m/d/Y", strtotime($pdVal['pdFrmDate']))?> 
                                    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
								  <div align="center">
								    <?=date("m/d/Y", strtotime($pdVal['pdToDate']))?> 
						          </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=($pdVal['pdProcessDate']!=""?date("m/d/Y", strtotime($pdVal['pdProcessDate'])):"")?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
								  <div align="left">
								    <?=$empName_processed;?>
						        </div></td>
						    <td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=($pdVal['pdDateClosed']!=""?date("m/d/Y", strtotime($pdVal['pdDateClosed'])):"")?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">								  
								  <div align="left">
								    <?=$empName_close;?>
						        </div></td>
							  <td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
							    <?=$pdVal['status']?>
						      </div></td>
							  <td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
                              	<? if ($pdVal['status'] == 'Held') {?>
							    <img src="../../../images/application_get.png" style="cursor:pointer" onClick="Activate(<?=$pdVal['pdSeries']?>);" >
                                <? }?>
                                <? if ($pdVal['status'] == 'Open') {?>
                                <img src="../../../images/edit_prev_emp.png" style="cursor:pointer" onClick="PopUp('period_act.php?act=Edit&pdSeries=<?=$pdVal['pdSeries']?>&pdFrom=<?=date("m/d/Y", strtotime($pdVal['pdFrmDate']))?>&pdTo=<?=date("m/d/Y", strtotime($pdVal['pdToDate']))?>&pdPayable=<?=date("m/d/Y", strtotime($pdVal['pdPayable']))?>','EDIT PAY PERIOD','','period_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"> </div></td>
                                <? }?>
								</tr>
							<?
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
									<? $pager->_viewPagerButton('period_list_ajax.php','TSCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		  <?$maintEmpObj->disConnect();?>
		
</BODY>
</HTML>
