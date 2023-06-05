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
$qryIntMaxRec = "SELECT compCode, holidayDate, brnCode, holidayDesc, dayType, holidayStat FROM tblHolidayCalendar where compCode='{$_SESSION['company_code']}'";

$resIntMaxRec = $maintEmpObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryHolList = "Select seqno,tblHolidayCalendar.compCode, tblHolidayCalendar.holidayDate, tblHolidayCalendar.brnCode,
				 tblHolidayCalendar.holidayDesc, tblHolidayCalendar.dayType, tblHolidayCalendar.holidayStat, tblBranch.brnDesc,
				 tblDayType.dayTypeDesc
			   FROM tblHolidayCalendar 
			   LEFT JOIN tblBranch ON tblHolidayCalendar.compCode = tblBranch.compCode 
			   	AND tblHolidayCalendar.brnCode = tblBranch.brnCode 
			   LEFT JOIN tblDayType ON tblHolidayCalendar.dayType = tblDayType.dayType 
			   where tblHolidayCalendar.compCode='{$_SESSION['company_code']}' ";
         if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryHolList .= "AND holidayDesc LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryHolList .= "AND brnDesc LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryHolList .= " AND year(holidayDate) LIKE '".(int)str_replace("'","''",trim($_GET['txtSrch']))."%'";
        	}
        }
		//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;
		$qryHolList .= " order by holidayDate DESC,tblBranch.brnDesc limit $intOffset,$intLimit";
	
$resHolList = $maintEmpObj->execQry($qryHolList);
$resHolList = $maintEmpObj->getArrRes($resHolList);
?>
<script>
function printCurrent(){
		
}
</script>

<HTML>
<head>


</head>
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> HOLIDAY CALENDAR</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<tr>
						  	  <td colspan="6" align="center" class="gridToolbar">
                       
                                <div align="left">
                                <?php if(($_SESSION['user_level']==1)||($_SESSION['user_level']==2)){ ?>
                                <a href="#" onClick="PopUp('holiday_act.php?act=AddHoliday','ADD HOLIDAY','<?=$dedListVal['recNo']?>','holiday_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Holiday</a> | <a href="#" onClick="PopUp('holiday_legal.php?act=AddLegelHoliday','ADD LEGAL HOLIDAY','<?=$dedListVal['recNo']?>','holiday_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Legal Holiday for Whole Year</a>
                      
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
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('holiday_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">                              
                                </div></td>
					  	  </tr>
						  	<tr>
								<td width="4%" class="gridDtlLbl" align="center">#</td>
								<td width="27%" class="gridDtlLbl" align="center">BRANCH</td>
								<td width="24%" class="gridDtlLbl" align="center">HOLIDAY DESCRIPTION</td>
								<td width="24%" class="gridDtlLbl" align="center">DATE</td>
								<td width="12%" height="20" align="center" class="gridDtlLbl">TYPE</td>
								<td width="9%" class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<?
							if(count($resHolList) > 0){
								$i=0;
								foreach ($resHolList as $empHolVal){
								
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
							        <?
                                    $branch =$empHolVal['brnDesc'];
									if ($branch != $branch2) {
										echo utf8_encode($empHolVal['brnDesc']);
									}	
									else{
										echo "ALL BRANCHES";
									}
									//$branch2 = $empHolVal['brnDesc'];?>
						            </div>                                
                                </td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
								  
							      <div align="left">
							        <?=htmlentities($empHolVal['holidayDesc']);?>
						            </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  <div align="left">
                                    <?=date("l F dS, Y",strtotime($empHolVal['holidayDate']))?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
                                  <div align="left">
                                    <?=$empHolVal['dayTypeDesc']?>
                                  </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center"><a href="#" onClick="PopUp('holiday_act.php?act=EditHoliday&seqno=<?=$empHolVal['seqno']?>','EDIT HOLIDAY','<?=$dedListVal['recNo']?>','holiday_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_edit.png" border="0" class="actionImg" title="Edit Holiday" /></a></div></td>
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
									<? $pager->_viewPagerButton('holiday_list_ajax.php','TSCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$maintEmpObj->disConnect();?>
	</BODY>
</HTML>
