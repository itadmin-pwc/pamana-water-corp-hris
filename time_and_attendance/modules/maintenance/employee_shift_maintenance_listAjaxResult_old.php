<?
	/*
		Date Created	:	07272010
		Created By		:	Genarra Arong
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
		$brnCodelist = " AND empNo<>'".$_SESSION['employee_number']."' and empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}
	
	$array_userPayCat = explode(',', $_SESSION['user_payCat']);
	
	if(in_array(9,$array_userPayCat))
	{
		$where_empStat = " AND empStat IN('RS','RG')";
	}
	else
	{
		$where_empStat = " AND empStat='RG'";
	}
	
	$user_payCat_view = " AND empPayCat IN ({$_SESSION['user_payCat']})";


	//variable declaration 
	$preEmplyrVal =0;
	$srchType = 0;
	
	$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
	
	if($brnCode_View ==""){
		$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblTK_UserBranch tblUB, tblBranch as tblbrn
							where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
							and empNo='".$_SESSION['employee_number']."'
							order by brnDesc";
		
		$resBrnches = $common->execQry($queryBrnches);
		$arrBrnches = $common->getArrRes($resBrnches);
		$arrBrnch = $common->makeArr($arrBrnches,'brnCode','brnDesc','All');
	}
	
	
	$qryIntMaxRec = "SELECT * FROM tblEmpMast 
					 WHERE compCode = '{$sessionVars['compCode']}' 
					 $brnCodelist $where_empStat
					 and empPayCat<>0 $user_payCat_view";
					
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
				
				if ($_GET['brnCd']!=0) 
				{
					$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
				}
			}
			
	$resIntMaxRec = $common->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryEmpList = "SELECT TOP $intLimit *
					FROM tblEmpMast
					WHERE compCode= '{$sessionVars['compCode']}'
					and empPayCat<>0 $where_empStat $user_payCat_view and
					empNo NOT IN
					(SELECT TOP $intOffset empNo FROM tblEmpMast WHERE  compCode = '{$sessionVars['compCode']}'  $where_empStat $user_payCat_view and empPayCat<>0 $brnCodelist "; 
	
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
							
							if ($_GET['brnCd']!=0) 
							{
								$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
							}
						}  
	$qryEmpList .= " ORDER BY empLastName) 
					 AND compCode = '{$sessionVars['compCode']}'  $brnCodelist";
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
						
						if ($_GET['brnCd']!=0) 
						{
							$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
						}
					}
	$qryEmpList .=	"ORDER BY empLastName";
	
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
					<td colspan="7" class="gridToolbar">
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
					</td>
					
                    <tr>
						<td width="1%" class="gridDtlLbl" align="center">#</td>
						<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="70%" class="gridDtlLbl" align="center">NAME</td>
						<td class="gridDtlLbl" align="center">ACTION</td>
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
                                <td class="gridDtlVal"><?=$i?></td>
                                <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
                                <td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace("Ñ","&Ntilde;",$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName'])?></font></td>
                                <td class="gridDtlVal" align="center">
                                	<table border="0" width="60%" style="border-collapse:collapse;">
                                    	<tr>
                                        	<?php
												if($disabledButtons!="Y")
												{
											?>
                                        	<td width="30%">
                                            	 <?php
												 	
														$chkDuplicate = $empShiftMaint->getShiftInfo("tblTK_EmpShift", " and empNo='".$empListVal['empNo']."'", " ");
														
														if($chkDuplicate["shiftCode"]=="")
														{
												?>
                                            				<a href="#"  onClick="" ><img class="toolbarImg" src="../../../images/application_form_add.png" title="Add Employee Shift" onclick="maintShiftCode('Add','<?=$empListVal['empNo']?>','employee_shift_maintenance_pop.php','empMastCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a> 
                                           		 <?php 	} ?>
                                           		
                                            </td>
                                            <td width="30%">
                                            	<?php 
														
														if(($chkDuplicate["shiftCode"]!="")&&($chkDuplicate["status"]!="D"))
														{
												?>
                                            				<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/application_form_edit.png" title="View/Edit Employee Shift" <?php echo $disabledButtons; ?> onclick="maintShiftCode('Edit','<?=$empListVal['empNo']?>','employee_shift_maintenance_pop.php','empMastCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                                            	<? 		}elseif($chkDuplicate["status"]=="D"){ ?>
                                                			<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/edit_prev_emp.png" title="Set Employee Shift to Active" <?php echo $disabledButtons; ?> onclick="setEmpShiftActive('Delete','<?=$empListVal['empNo']?>','employee_shift_maintenance_pop.php','empMastCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                                            	<? 		}?>
                                            </td>
                                            <td width="30%">
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
                                          		<td class="gridDtlVal" colspan="3">
                                                
                                                	<a href="#"  onClick=""><img class="toolbarImg" src="../../../images/edit_prev_emp.png" title="Set Employee Shift to Active" <?php echo $disabledButtons; ?> onclick="maintShiftCode('UpdateBio','<?=$empListVal['empNo']?>','employee_shift_maintenance_pop.php','empMastCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
                                            
                                                </td>
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
                            <td colspan="7" align="center">
                                <FONT class="zeroMsg">NOTHING TO DISPLAY</font>
                            </td>
                        </tr>
					<?
                    }
					?>
					
                    <tr>
						<td colspan="7" align="center" class="childGridFooter">
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