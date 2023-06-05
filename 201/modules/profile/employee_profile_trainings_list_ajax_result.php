<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("employee_profile_trainings_obj.php");
$empProfileTrainingsObj = new empProfileTrainingsObj($_GET);
//$sessionVars = $empProfileTrainingsObj->getSeesionVars();
$empProfileTrainingsObj->validateSessions('','MODULES');
$common = new commonObj();
$pager = new AjaxPager(15,'../../../images/');
$sessionVars = $common->getSeesionVars();

$empProfileTrainingsObj->compCode = $sessionVars['compCode'];
$empProfileTrainingsObj->empNo    = $_SESSION['strprofile'];

switch ($_GET['action']){
	case 'delete':
		$deleEmpTraining = $empProfileTrainingsObj->deleteTraining(" where training_Id='{$_GET['trainingid']}'");
		if($deleEmpTraining){
			echo "alert('Training Successfully Deleted');";
		}
		else{
			echo "alert('Training Deletion Failed');";
		}
		exit();
	break;
}

$qryIntMaxRec = "SELECT training_Id, empNo, compCode FROM tblTrainings where compCode='".$empProfileTrainingsObj->compCode."' and empNo='".$empProfileTrainingsObj->empNo."' ";
$qryIntMaxRec .= "ORDER BY training_Id ";
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
$qryEmpTrainings = "SELECT training_Id, trainingFrom, trainingTo, trainingTitle, trainingCost, trainingBond, effectiveFrom, effectiveTo, empNo, compCode, date_Added, user_Added FROM tblTrainings
			       WHERE  compCode='{$empProfileTrainingsObj->compCode}' and  empNo='{$empProfileTrainingsObj->empNo}' "; 
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;				
$qryEmpTrainings .= " limit $intOffset,$intLimit";
$resEmpTrainings = $common->execQry($qryEmpTrainings);
$arrEmpTrainings = $common->getArrRes($resEmpTrainings);
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
			<td colspan="4">&nbsp;<img src="../../../images/grid.png"><span class="style1">&nbsp;Employee Training List</span></td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					    <td colspan="10" class="gridToolbar"><? //=$intOffset?>
						<a href="#"  class="anchor" onClick="empTrainings('Add','<?=$empProfileTrainingsObj->empNo?>','','employee_profile_trainings_list_ajax_result.php','','',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'','')">
                        <img class="anchor" src="../../../images/add.gif">Add Training</a></td>
					<tr>
						<td width="2%" height="22" align="center" class="gridDtlLbl">#</td>
						<td class="gridDtlLbl" align="center" width="10%">FROM</td>
						<td  class="gridDtlLbl" align="center" width="10%">TO</td>
						<td  class="gridDtlLbl" align="center" width="34%">TITLE</td>
						<!--<td  class="gridDtlLbl" align="center" width="10%">TAX TAG</td>-->
						<td  class="gridDtlLbl" align="center" width="11%">COST</td>
						<td  class="gridDtlLbl" align="center" width="9%">BOND</td>
						<td width="10%" align="center" class="gridDtlLbl">EFFECTIVE FROM</td>
						<td width="10%" align="center" class="gridDtlLbl">EFFECTIVE TO</td>
						<td width="6%" align="center" class="gridDtlLbl">ACTION</td>
					</tr>
					<?
					if($empProfileTrainingsObj->getRecCount($resEmpTrainings) > 0){
						$i=0;
						foreach ($arrEmpTrainings as $empTrainings=> $employeeTrainings){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><?=$empProfileTrainingsObj->dateFormat($employeeTrainings['trainingFrom']);?></td>
						<td class="gridDtlVal"><?=$empProfileTrainingsObj->dateFormat($employeeTrainings['trainingTo']);?></td>
						<td class="gridDtlVal"><?=$employeeTrainings['trainingTitle'];?></td>
						<td class="gridDtlVal" align="right"><?=number_format($employeeTrainings['trainingCost'],2);?></td>
						<td class="gridDtlVal" align="center"><?
						if($employeeTrainings['trainingBond']==1){
							echo $employeeTrainings['trainingBond'] . " year";
							}
						elseif($employeeTrainings['trainingBond']>1){
							echo $employeeTrainings['trainingBond'] . " years";
							}	
						?></td>
						<td class="gridDtlVal"><?=$empProfileTrainingsObj->dateFormat($employeeTrainings['effectiveFrom']);?></font></td>
						<td class="gridDtlVal"><?=$empProfileTrainingsObj->dateFormat($employeeTrainings['effectiveTo']);?></td>
					  <td class="gridDtlVal" align="center">
                     
						  <a href="#" onClick="empTrainings('Edit','<?=$employeeTrainings['empNo']?>','<?=$employeeTrainings['training_Id']?>','employee_profile_trainings_list_ajax_result.php','','','<?=$intOffset?>','','<?=$_GET['isSearch']?>','','')"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit"></a>
                          <a href="#"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete" onClick="deleTrainings('employee_profile_trainings_list_ajax_result.php','','<?=$employeeTrainings['empNo']?>','<?=$employeeTrainings['training_Id']?>',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'','','')"></a>
                          </td>
					</tr>
					<?  
						}
					}
					else{						
					?>
					<tr>
						<td colspan="10" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font></td>
					</tr>
					<?}?>
					<tr>
						<td colspan="10" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton('employee_profile_trainings_list_ajax_result.php','Performance',$intOffset,$_GET['isSearch'],'','','&empNo='.$empProfileTrainingsObj->empNo);?>						</td>
					</tr>
				</TABLE>
		  </td>
		</tr>
  </TABLE>
</div>
</body>
</html>
<?$common->disConnect();?>
