<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');

$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}' ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND empNo LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND empLastName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND empFirstName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        }

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT *
		FROM tblEmpMast
		WHERE compCode = '{$sessionVars['compCode']}'  "; 
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        }
$qryEmpList .=	"ORDER BY empLastName limit $intOffset,$intLimit";
$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;EMPLOYEE MASTER LIST</td>
	  </tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="7" class="gridToolbar">
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
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$srchType,'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('employeeAjaxResult.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
					</td>
					<tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="70%" class="gridDtlLbl" align="center">NAME</td>
						<td class="gridDtlLbl" align="center" colspan="4">ACTION</td>
					</tr>
					<?
					if($common->getRecCount($resEmpList) > 0){
						$i=0;
						foreach ($arrEmpList as $empListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empLastName']. ", " . $empListVal['empFirstName'] . " " . $empListVal['empMidName']?></font></td>
						<td class="gridDtlVal" align="center">
							<a href="#" onclick="location.href='view_edit_employee.php?transType=view&empNo=<?=$empListVal['empNo']?>'"><img class="toolbarImg" src="../../../images/application_form_magnify.png" title="View Employee Information"></a>
							
                        </td>
						<td class="gridDtlVal" align="center">
							<!--	
                            <a href="#" onclick="location.href='view_edit_employee.php?transType=edit&empNo=<?=$empListVal['empNo']?>'"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Employee Information "></a>
							-->
                        </td>
						<td class="gridDtlVal" align="center">
							<?
							if($empListVal['empStat'] != 'RS' && $empListVal['empStat'] != 'IN' && $empListVal['empStat'] != 'TR'){
							?>
								<a href="#" onclick="location.href='employee_allowance.php?transType=list&empNo=<?=$empListVal['empNo']?>'"><img class="toolbarImg" src="../../../images/allowance_list.png" title="Employee Allowance"></a>
							<?}else{?>
								<img class="toolbarImg" src="../../../images/allowance_list_2.png" title="Disabled">
							<?}?>
						</td>
                        <!--
						<td class="gridDtlVal" align="center">
							<?
							if($empListVal['empPrevTag'] =='Y'){
							?>
								<img id="imgViewPrevEmp" class="toolbarImg" src="../../../images/application_side_contract.png" title="Previous Employer Maintenance" onclick="viewPrevEmp('<?=$empListVal['empNo']?>')" style="cursor:pointer;">
							<?}else{?>
								<img class="toolbarImg" src="../../../images/application_side_contract_2.png" title="Disabled">
							<?}?>
						</td>
                        -->
					</tr>
                    
					<tr id="trPrevEmpCont<?=$empListVal['empNo']?>" style="display:none;">
						<td colspan="7" >
							<DIV id="prevEmpCont<?=$empListVal['empNo']?>" style="display:none;">
								<TABLE border="0" width="100%" cellpadding="0" cellspacing="1" class="tblPrevEmp">
									<tr>
										<td colspan="5" align="left" class="gridToolbar">
											<img src="../../../images/arrow_ltr_2.png" class="prevEmpArrw">Previous Employer List
											<FONT class="ToolBarseparator">|</font>
											<a href="#" class="anchor" onclick="maintPrevEmp('Add','<?=$empListVal['empNo']?>','<?=$preEmplyrVal['seqNo']?>','employeeAjaxResult.php','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" >
											<img class="toolbarImg" src="../../../images/add_small.gif">Add Previous Employer</a>
										</td>
									</tr>
									<tr>
										<td width="35%" class="gridDtlLbl" align="center">NAME</td>
										<td width="15%" class="gridDtlLbl" align="center">TIN NO.</td>
										<td width="15%" class="gridDtlLbl" align="center">EARNINGS</td>
										<td width="15%" class="gridDtlLbl" align="center">TAXES</td>
										<td width="5%" class="gridDtlLbl" align="center">ACTION</td>
									</tr>					
									<?
									
									$empPreEmplyrList = $common->getPrevEmployer($sessionVars['compCode'],'AND empNo = '.$empListVal['empNo']);
									if($empPreEmplyrList != 0){
										foreach ($empPreEmplyrList as $preEmplyrVal){
										?>
										<tr class="rowDtlEmplyrLst">
											<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$preEmplyrVal['prevEmplr'];?></a></td>
											<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$preEmplyrVal['emplrTin'];?></a></td>
											<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$preEmplyrVal['prevEarnings'];?></a></td>
											<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$preEmplyrVal['prevTaxes'];?></a></td>
											<td class="gridDtlVal" align="center">
												<img onclick="maintPrevEmp('Edit','<?=$empListVal['empNo']?>','<?=$preEmplyrVal['seqNo']?>','employeeAjaxResult.php','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" 
												src="../../../images/edit_prev_emp.png" width="15" height="15" title="Edit Previous Employer">
												<img onclick="delePrevEmp('<?=$empListVal['empNo']?>','<?=$preEmplyrVal['seqNo']?>','employeeAjaxResult.php','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch','<?=htmlspecialchars(addslashes($preEmplyrVal['prevEmplr']));?>')" 
												src="../../../images/prev_emp_dele.png" width="15" height="15" title="Delete Previous Employer">
											</td>
										</tr>
										<?
										}
									}else{
									?>
									<tr class="rowDtlEmplyrLst">
										<td align="center" colspan="5">
											<FONT class="prevEmpZeroMsg">NOTHING TO DISPLAY</font>
										</td>
									</tr>
									<?}?>
								</TABLE>
							</DIV>
						</td>
					</tr>
					<?
						}
					}
					else{
					?>
					<tr>
						<td colspan="7" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?}?>
					<tr>
						<td colspan="7" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("employeeAjaxResult.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>