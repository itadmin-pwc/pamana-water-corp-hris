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

$arrSrch = array('HOLIDAY','BRANCH','YEAR');
$qryIntMaxRec = "SELECT * from tblDayType";

$resIntMaxRec = $maintEmpObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryDayList = "Select *,CASE dayStat WHEN 'A' THEN 'Active' WHEN 'H' THEN 'Held' END as status from tblDayType Limit $intOffset,$intLimit";
$resDayList = $maintEmpObj->execQry($qryDayList);
$resDayList = $maintEmpObj->getArrRes($resDayList);
?>

<HTML>
<head>


</head>
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> DAY TYPE</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<tr>
						  	  <td colspan="4" align="center" class="gridToolbar">
                       
                                <div align="left" >
                                <?php if($_SESSION['user_level']==1){ ?>
                                <a href="#" onClick="PopUp('daytype_act.php?act=AddDayType','ADD DAY TYPE','<?=$dedListVal['recNo']?>','daytype_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Day Type</a> 
                      			<FONT class="ToolBarseparator">|</font>
                                <?php } ?>
						         <span style="visibility:hidden;"> <?
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
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('holiday_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">                              
                               </span> </div></td>
					  	  </tr>
						  	<tr>
								<td width="4%" height="20" align="center" class="gridDtlLbl">#</td>
								<td width="27%" class="gridDtlLbl" align="center">DAY TYPE</td>
								<td width="24%" class="gridDtlLbl" align="center">STATUS</td>
								<td width="24%" class="gridDtlLbl" align="center">ACTION</td>
						    </tr>
							<?
							if(count($resDayList) > 0){
								$i=0;
								foreach ($resDayList as $empDayVal){
								
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
							        <?=$empDayVal['dayTypeDesc'];?>
						            </div>                                </td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
                                  <?=$empDayVal['status']?>
                                </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center"><a href="#" onClick="PopUp('daytype_act.php?act=EditDayType&daytype=<?=$empDayVal['dayType']?>','EDIT DAY TYPE','<?=$dedListVal['recNo']?>','daytype_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_magnify.png" border="0" class="actionImg" title="Edit day Type" /></a></div></td>
								</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="16" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="16" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton('daytype_list_ajax.php','TSCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$maintEmpObj->disConnect();?>
	</BODY>
</HTML>
