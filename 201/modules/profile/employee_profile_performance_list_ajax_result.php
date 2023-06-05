<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("employee_profile_performance_obj.php");
$empProfilePerformanceObj = new empProfilePerformanceObj($_GET);
//$sessionVars = $empProfilePerformanceObj->getSeesionVars();
$empProfilePerformanceObj->validateSessions('','MODULES');
$common = new commonObj();
$pager = new AjaxPager(15,'../../../images/');
$sessionVars = $common->getSeesionVars();

$empProfilePerformanceObj->compCode = $sessionVars['compCode'];
$empProfilePerformanceObj->empNo    = $_SESSION['strprofile'];

switch ($_GET['action']){
	case 'delete':
		$deleEmpPerformance = $empProfilePerformanceObj->deleteEmpPerformance(" where performance_Id='{$_GET['performanceid']}'");
		if($deleEmpPerformance){
			echo "alert('Performance Successfully Deleted');";
		}
		else{
			echo "alert('Performance Deletion Failed');";
		}
		exit();
	break;
}

$qryIntMaxRec = "SELECT performance_Id, empNo, compCode FROM tblPerformance where compCode='".$empProfilePerformanceObj->compCode."' and empNo='".$empProfilePerformanceObj->empNo."' ";
$qryIntMaxRec .= "ORDER BY performance_Id ";
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;
$qryEmpPerformance = "SELECT performance_Id, performanceFrom, performanceTo, performanceNumerical, performanceAdjective, performancePurpose, empNo, compCode, date_Added, user_Added FROM tblPerformance
			       WHERE  compCode='{$empProfilePerformanceObj->compCode}' and  empNo='{$empProfilePerformanceObj->empNo}' "; 
$qryEmpPerformance .= " limit $intOffset,$intLimit";
$resEmpPerformance = $common->execQry($qryEmpPerformance);
$arrEmpPerformance = $common->getArrRes($resEmpPerformance);
?>
<html>
<head>
<style type="text/css">
<!--
.style1 {
	font-family: verdana;
	font-size: 11px;
	font-weight: bold;
}
.style2 {font-size: 8px}
-->
</style>
</head>
<body>    
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" >
    	<tr><td height="10" colspan="4"></td></tr>
		<tr>
			<td colspan="4">&nbsp;<img src="../../../images/grid.png"><span class="style1">&nbsp;Employee Performance List</span></td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					  <td colspan="8" class="gridToolbar"><? //=$intOffset?>
						<a href="#"  class="anchor" onClick="empPerformance('Add','<?=$empProfilePerformanceObj->empNo?>','','employee_profile_performance_list_ajax_result.php','','',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'','')">
                        <img class="anchor" src="../../../images/add.gif">Add Performance</a></td>
					<tr>
						<td width="3%" height="22" align="center" class="gridDtlLbl">#</td>
						<td class="gridDtlLbl" align="center" width="12%">FROM</td>
						<td  class="gridDtlLbl" align="center" width="12%">TO</td>
						<td  class="gridDtlLbl" align="center" width="17%">NUMERICAL RATING</td>
						<!--<td  class="gridDtlLbl" align="center" width="10%">TAX TAG</td>-->
						<td  class="gridDtlLbl" align="center" width="17%">ADJECTIVE RATING</td>
						<td  class="gridDtlLbl" align="center" width="29%">PURPOSE</td>
						<td width="10%" align="center" class="gridDtlLbl">ACTION</td>
					</tr>
					<?
					if($empProfilePerformanceObj->getRecCount($resEmpPerformance) > 0){
						$i=0;
						foreach ($arrEmpPerformance as $empPerformances=> $employeePerformance){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empProfilePerformanceObj->dateFormat($employeePerformance['performanceFrom']);?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empProfilePerformanceObj->dateFormat($employeePerformance['performanceTo']);?></font></td>
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<?
                                if($employeePerformance['performanceNumerical']==1){
									echo "96% - 100%";
									}
								elseif($employeePerformance['performanceNumerical']==2){
									echo "91% - 95%";
									}	
								elseif($employeePerformance['performanceNumerical']==3){
									echo "85% - 90%";
									}
								elseif($employeePerformance['performanceNumerical']==4){
									echo "80% - 84%";
									}
								elseif($employeePerformance['performanceNumerical']==5){
									echo "80% and below";
									}
								?>
							</font>						</td>
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
                            	<?
                                if($employeePerformance['performanceAdjective']==1){
									echo "Outstanding";
									}
								elseif($employeePerformance['performanceAdjective']==2){
									echo "Above Average";
									}	
								elseif($employeePerformance['performanceAdjective']==3){
									echo "Average";
									}
								elseif($employeePerformance['performanceAdjective']==4){
									echo "Below Average";
									}
								elseif($employeePerformance['performanceAdjective']==5){
									echo "Poor";
									}
								?>
							</font>						</td>
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
                            	<?
                                if($employeePerformance['performancePurpose']==1){
									echo "Probationary";
									}
								elseif($employeePerformance['performancePurpose']==2){
									echo "Regularization";
									}	
								elseif($employeePerformance['performancePurpose']==3){
									echo "Merit Increase";
									}
								elseif($employeePerformance['performancePurpose']==4){
									echo "Salary Alignment";
									}
								elseif($employeePerformance['performancePurpose']==5){
									echo "Promotion";
									}
								?>
							</font>						</td>
					  <td class="gridDtlVal" align="center">
                     
						  <a href="#" onClick="empPerformance('Edit','<?=$employeePerformance['empNo']?>','<?=$employeePerformance['performance_Id']?>','employee_profile_performance_list_ajax_result.php','','','<?=$intOffset?>','','<?=$_GET['isSearch']?>','','')"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit"></a>
                          <a href="#"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete" onClick="delePerformance('employee_profile_performance_list_ajax_result.php','','<?=$employeePerformance['empNo']?>','<?=$employeePerformance['performance_Id']?>',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'','','')"></a>
                          </td>
					</tr>
					<?  
						}
					}
					else{						
					?>
					<tr>
						<td colspan="8" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font></td>
					</tr>
					<?}?>
					<tr>
						<td colspan="8" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton('employee_profile_performance_list_ajax_result.php','Performance',$intOffset,$_GET['isSearch'],'','','&empNo='.$empProfilePerformanceObj->empNo);?>						</td>
					</tr>
				</TABLE>
		  </td>
		</tr>
  </TABLE>
</div>
</body>
</html>
<?$common->disConnect();?>
