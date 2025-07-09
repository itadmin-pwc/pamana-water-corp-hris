<?
	/*
		Date Created	:	07272010
		Created By		:	Genarra Arong
		Edited By 		: 	Nhomer Cabico
			*Edited process
				= show active employees and resigned between the current cut off
	*/

	session_start();
	include("../../../includes/userErrorHandler.php");
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("maintenance_obj.php");
	
	$common = new commonObj();
	$empShiftMaint = new maintenanceObj();
	$pager = new AjaxPager(20,'../../../images/');
	$sessionVars = $common->getSeesionVars();

	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $common->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		$brnCodelist = " AND empNo<>'".$_SESSION['employee_number']."' 
						and empbrnCode IN (Select brnCode 
							from tblTK_UserBranch 
							where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}
	elseif ($_SESSION['user_level'] == 2)
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}

//new codes
	//$paygroup = $common->getProcGrp();
	$sqlGrp = "Select payGrp from tblProcGrp where compCode='{$_SESSION['company_code']}' and status='A'";
	$res = $common->getSqlAssoc($common->execQry($sqlGrp));		
	$paygroup = $res['payGrp'];	
	$qryPayperiod = $common->execQry("Select pdFrmDate, pdToDate 
									  from tblPayPeriod 
									  where compCode='{$_SESSION['company_code']}' and payGrp='{$paygroup}' 
									  	and pdYear='".date("Y")."' and pdStat='O'");
	$payperiod = $common->getSqlAssoc($qryPayperiod);								

	//variable declaration 
	$preEmplyrVal = 0;
	$srchType = 0;
	
	$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
	
	if($brnCode_View ==""){
		$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblTK_UserBranch tblUB, tblBranch as tblbrn
							where tblUB.brnCode=tblbrn.brnCode 
							and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
							and empNo='".$_SESSION['employee_number']."'
							order by brnDesc";
		
		$resBrnches = $common->execQry($queryBrnches);
		$arrBrnches = $common->getArrRes($resBrnches);
		$arrBrnch = $common->makeArr($arrBrnches,'brnCode','brnDesc','All');
		
	}
	
//new codes	
	$qryEmpList = "SELECT * FROM tblEmpMast
					WHERE compCode= '{$sessionVars['compCode']}'
					and ((empStat='RG') 
					OR (dateResigned between '".date("Y-m-d",strtotime($payperiod['pdFrmDate']))."' 
						AND '".date("Y-m-d",strtotime($payperiod['pdToDate']))."') 
					OR endDate between '".date("Y-m-d",strtotime($payperiod['pdFrmDate']))."'
						AND '".date("Y-m-d",strtotime($payperiod['pdToDate']))."') $brnCodelist "; 

					if($_GET['isSearch'] == 1){
						if($_GET['srchType'] == 2){
							$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
						}
						if($_GET['srchType'] == 0){
							$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
						}
						if($_GET['srchType'] == 1){
							$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
						}
						
						if ($_GET['brnCd']!=0) 
						{
							$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
						}
					}
			
	$resIntMaxRec = $common->execQry($qryEmpList);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

	$qryEmpList .=	"ORDER BY empBrnCode, empLastName limit $intOffset,$intLimit";
	//echo $qryEmpList;
	$resEmpList = $common->execQry($qryEmpList);
	$arrEmpList = $common->getArrRes($resEmpList);
	
	

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Employee Shift Maintenance
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar">
                         <FONT class="ToolBarseparator">&nbsp;</font>
						<?
							if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
								if(isset($_GET['srchType']) ){ 
									$srchType = $_GET['srchType'];
								}
							}
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<?php if($brnCode_View==""){echo  "Branch |";}?> <? if($brnCode_View ==""){echo $common->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');}?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('employee_shift_maintenance_listAjaxResult.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')">
						<a href="#" class="" style="float: right; margin: 3px; color: blue;" onclick="event.preventDefault(); maintShiftCodeByBranch('Add', document.getElementById('brnCd').value)"> [<b>Add Employee Shift</b>]</a>
						<b style="float: right; margin: 3px;">/</b>
						<a href="#" class="" style="float: right; margin: 3px; color: green;" onclick="event.preventDefault(); maintShiftCodeByBranch('Edit', document.getElementById('brnCd').value)"> [<b>Update Employee Shift</b>]</a>
						<span style="float: right; margin: 3px;"><b>BY BRANCH : </b> </span>
                      <?
					  $chkEmpShift = $empShiftMaint->getShiftInfo("tblTK_EmpShift", " ", " ");
					  $chkEmpTS = $empShiftMaint->getShiftInfo("tblTK_Timesheet", " ", " ");
                      if($_SESSION['company_code']=="15" && $chkEmpShift["shiftCode"]!="" && $chkEmpTS["empNo"]==""){
					  ?>  
					  <input class="inputs" type="submit" name="btnClearAll" id="btnClearAll" value="CLEAR ALL EMPLOYEES SHIFT" onclick="clearAllEmpShift();" />
                      <?
					  }
					  ?>
					</td>
					
                    <tr>
						<td width="2%" class="gridDtlLbl" align="center">#</td>
						<td width="16%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="37%" class="gridDtlLbl" align="center">NAME</td>
						<td width="34%" align="center" class="gridDtlLbl">BRANCH</td>
						<td width="11%" align="center" class="gridDtlLbl">ACTION</td>
					</tr>
				
                	<?
					if($common->getRecCount($resEmpList) > 0)
					{
						$i=0;
						foreach ($arrEmpList as $empListVal)
						{
							$disabledButtons = "";
							$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
							$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
							. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
							
							//$arr_tna = $empShiftMaint->getShiftInfo("tblPayPeriod", " and payGrp='".$empListVal['empPayGrp']."' and payCat='".$empListVal['empPayCat']."' and pdYear='".date("Y")."' and '".date("m/d/Y")."' between pdFrmDate and pdToDate", "");						
							$arr_ObRec_Checking = $empShiftMaint->getShiftInfo("tblTK_Timesheet", " and empNo='".$empListVal['empNo']."'", "");
							
							
							$tnapdFrmDate = date("m/d/Y", strtotime($arr_tna["pdFrmDate"]));
							$mkTime = mktime(0,0,0,date("m", strtotime($tnapdFrmDate)),date("d", strtotime($tnapdFrmDate))+1,date("Y", strtotime($tnapdFrmDate)));
							
							//Check if Current Date is <= First Day of the Cut Off + 1 day;
							
							
								if($arr_ObRec_Checking["tsDate"]!='')
									$disabledButtons = "Y";
							
							
														//echo sizeof($arr_ObRec_Checking)."GEN";
							//echo $empListVal["empPayGrp"]."===".$disabledButtons."GEn";
					?>
                            <tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                                <td class="gridDtlVal" height="22"><?=$i?></td>
                                <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
                                <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("�","&Ntilde;",$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName'])?></font></td>
                                <td class="gridDtlVal" align="left"><?=$empShiftMaint->getBranchName($empListVal['compCode'],$empListVal['empBrnCode']);?></td>
                                <td class="gridDtlVal" align="center">
                                	<table border="0" width="90%" style="border-collapse:collapse;">
                                    	<tr>
                                        	<?php
												if($disabledButtons!="Y")
												{
											?>
                                        	<td width="21%">
                                            	 <?php
												 	
														$chkDuplicate = $empShiftMaint->getShiftInfo("tblTK_EmpShift", " and empNo='".$empListVal['empNo']."'", " ");
														
														if($chkDuplicate["shiftCode"]=="")
														{
												?>
                                            				<a href="#"  onClick="" ><img class="toolbarImg" src="../../../images/application_form_add.png" title="Add Employee Shift" onclick="maintShiftCode('Add','<?=$empListVal['empNo']?>','employee_shift_maintenance_pop.php','empMastCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch','<?=$empListVal['empBrnCode']?>')"></a> 
                                           		 <?php 	} ?>
                                           		
                                            </td>
                                            <td width="22%">
                                            	<?php 
														
														if(($chkDuplicate["shiftCode"]!="")&&($chkDuplicate["status"]!="D"))
														{
												?>
                                            				<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_edit.png" title="View/Edit Employee Shift" <?php echo $disabledButtons; ?> onclick="maintShiftCode('Edit','<?=$empListVal['empNo']?>','employee_shift_maintenance_pop.php','empMastCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch','<?=$empListVal['empBrnCode']?>')"></a>
                                            	<? 		}elseif($chkDuplicate["status"]=="D"){ ?>
                                                			<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/edit_prev_emp.png" title="Set Employee Shift to Active" <?php echo $disabledButtons; ?> onclick="setEmpShiftActive('Delete','<?=$empListVal['empNo']?>','employee_shift_maintenance_pop.php','empMastCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                                            	<? 		}?>
                                            </td>
                                            <td width="21%">
                                            	<?php 
														if(($chkDuplicate["shiftCode"]!="")&&($chkDuplicate["status"]!="D"))
														{
												?>
                                            				<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete  Employee Shift" <?php echo $disabledButtons; ?> onclick="delEmpShift('Delete','<?=$empListVal['empNo']?>','employee_shift_maintenance_pop.php','empMastCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                                            	<? 		}
												?>
                                            </td>
                                          <? }
										  	 else
											 { 
										  ?>
                                          		<td width="50%" colspan="3" class="gridDtlVal" style="color:#F00">With Time Sheet</td>
                                          <?
										  	  }
										  ?>
                                        </tr>
                                    </table>
                                   
                                		                                   
                                   
                                    
                                    
                                </td>
                            </tr>
					<?
						}
					}
					else
					{
					?>
                        <tr>
                            <td colspan="8" align="center">
                                <FONT class="zeroMsg">NOTHING TO DISPLAY</font>
                            </td>
                        </tr>
					<?
                    }
					?>
					
                    <tr>
						<td colspan="8" align="center" class="childGridFooter">
                        	<? $pager->_viewPagerButton("employee_shift_maintenance_listAjaxResult.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&brnCd='.$_GET["brnCd"],'');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>
<form name="frmTS" method="post">
	<input type="hidden" name="brnCd" id="brnCd" value="<?php echo $_GET['brnCd']; ?>">
</form>