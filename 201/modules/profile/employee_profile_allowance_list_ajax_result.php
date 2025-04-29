<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("employee_profile_allowance.obj.php");
$empProfileAllowObj = new empProfileAllowanceObj($_GET);
//$sessionVars = $empProfileAllowObj->getSeesionVars();
$empProfileAllowObj->validateSessions('','MODULES');
$common = new commonObj();
$pager = new AjaxPager(2,'../../../images/');
$sessionVars = $common->getSeesionVars();

$empProfileAllowObj->compCode = $sessionVars['compCode'];
$empProfileAllowObj->empNo    = $_SESSION['strprofile'];

switch ($_GET['action']){
	case 'delete':
		$deleEmpAllw = $empProfileAllowObj->deleteEmpProfileAllowance(" where allowSeries='{$_GET['allwSeries']}'");
		if($deleEmpAllw){
			echo "alert('Successfully Deleted');";
		}
		else{
			echo "alert('Deletion Failed');";
		}
		exit();
	break;
}

$qryIntMaxRec = "SELECT allw.compCode, allw.empNo, allw.allowCode, allw.allowAmt, allw.allowSked, allw.allowTaxTag, 
					allw.allowPayTag, allw.allowStart, allw.allowEnd, allw.allowStat, allwTyp.allowDesc, allw.allowSeries 
				 FROM tblAllowance_New allw 
				 LEFT JOIN tblAllowType allwTyp ON allwTyp.compCode = allw.compCode 
				 	AND allwTyp.allowCode = allw.allowCode 
				 where allw.compCode='".$empProfileAllowObj->compCode."' and allw.empNo='".$empProfileAllowObj->empNo."' ";
$qryIntMaxRec .= "ORDER BY allwTyp.allowDesc ";
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
$qryEmpAllwList = "SELECT allw.compCode,allw.empNo,allw.allowCode,allw.allowAmt,allw.allowSked,
						                allw.allowTaxTag,allw.allowPayTag,allw.allowStart,allw.allowEnd,allw.allowStat,
						                allwTyp.allowDesc, allw.allowSeries
				   FROM tblAllowance_New as allw 
				   LEFT JOIN tblAllowType as allwTyp ON allwTyp.compCode = allw.compCode AND allwTyp.allowCode = allw.allowCode
			       WHERE allw.compCode = '{$empProfileAllowObj->compCode}'
			       AND allw.empNo = '{$empProfileAllowObj->empNo}' "; 
//$intLimit = (($intMaxRec-$intOffset)<$intLimit) ? $intMaxRec-$intOffset:$intLimit;		
$qryEmpAllwList .= "ORDER BY allwTyp.allowDesc  limit $intOffset,$intLimit";
$resEmpAllwList = $common->execQry($qryEmpAllwList);
$arrEmpAllwList = $common->getArrRes($resEmpAllwList);

?>
<html>
<head>
<style type="text/css">
.style1 {
	font-family: verdana;
	font-size: 11px;
	font-weight: bold;
}
.style2 {font-size: 8px}
</style>
</head>
<body>    
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" >
    	<tr><td height="10" colspan="4"></td></tr>
		<tr>
			<td colspan="4">&nbsp;<img src="../../../images/grid.png"><span class="style1">&nbsp;Employee Current Allowance List</span></td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar"><? //=$intOffset?>
						<a href="#"  class="anchor" onClick="empProfileAllow('Add','<?=$empProfileAllowObj->empNo?>','','employee_profile_allowance_list_ajax_result.php','','',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'','')">
                        	<img class="anchor" src="../../../images/add.gif">Add Allowance
						</a>
					</td>
					<tr>
						<td class="gridDtlLbl" align="center" width="5%">#</td>
						<td class="gridDtlLbl" align="center" width="18%">CODE-TYPE</td>
						<td  class="gridDtlLbl" align="center" width="8%">AMOUNT</td>
						<td  class="gridDtlLbl" align="center" width="18%">SCHEDULE</td>
						<!--<td  class="gridDtlLbl" align="center" width="10%">TAX TAG</td>-->
						<td  class="gridDtlLbl" align="center" width="9%">PAY TAG</td>
						<td  class="gridDtlLbl" align="center" width="11%">START DATE</td>
						<td width="11%" align="center" class="gridDtlLbl">ACTION</td>
					</tr>
					<?php
					if($empProfileAllowObj->getRecCount($resEmpAllwList) > 0){
						$i=0;
						foreach ($arrEmpAllwList as $empAllowances=> $employeeAllowance){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$employeeAllowance['allowDesc']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=number_format($employeeAllowance['allowAmt'],2)?></font></td>
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<?
									if($employeeAllowance['allowSked'] == 1){
										echo "1st Payroll of Month";
									}
									if($employeeAllowance['allowSked'] == 2){
										echo "2nd Payroll of Month";
									}
									if($employeeAllowance['allowSked'] == 3){
										echo "Attendance Based";
									}
									if($employeeAllowance['allowSked'] == 0 || $employeeAllowance['allowSked']=""){
										echo "---";
									}
								?>
							</font>						</td>
<!--						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<? 
									if($employeeAllowance['allowTaxTag'] == 'Y'){
										echo "Taxable";
									}
									else if($employeeAllowance['allowTaxTag'] == 'N'){
										echo "Not Taxable";
									}
									else{
										echo "---";
									}
								?>
							</font>
						</td>-->
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<?
									if($employeeAllowance['allowPayTag'] == 'P'){
										echo "Permanent";
									}
									else{
										echo "Temporary";
									}
								?>
							</font>						</td>
						<td class="gridDtlVal">
							<font class="gridDtlLblTxt">
								<?
									if(date('Y-m-d',strtotime($employeeAllowance['allowStart'])) == '0000-00-00'){
										echo "---";				
									}
									else{
										echo date('Y-m-d',strtotime($employeeAllowance['allowStart']));
									}
								?>
							</font>						</td>
					  <td class="gridDtlVal" align="center">
                     
						  <a href="#" onClick="empProfileAllow('Edit','<?=$employeeAllowance['empNo']?>','<?=$employeeAllowance['allowSeries']?>','employee_profile_allowance_list_ajax_result.php','','','<?=$intOffset?>','','<?=$_GET['isSearch']?>','','')"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit"></a>
                          <a href="#"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete" onClick="deleEmpAllw('employee_profile_allowance_list_ajax_result.php','','<?=$employeeAllowance['empNo']?>','<?=$employeeAllowance['allowSeries']?>',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'','','<?php echo htmlspecialchars(addslashes($employeeAllowance['allowDesc']))?>')"></a>
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
							<? $pager->_viewPagerButton('employee_profile_allowance_list_ajax_result.php','Allowance',$intOffset,$_GET['isSearch'],'','','&empNo='.$empProfileAllowObj->empNo);?>						</td>
					</tr>
				</TABLE>
		  </td>
		</tr>
  </TABLE>
</div>
</body>
</html>
<?$common->disConnect();?>
