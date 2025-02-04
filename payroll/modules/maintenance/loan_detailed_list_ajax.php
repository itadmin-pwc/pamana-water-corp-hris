<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("main_emp_loans_obj.php");

$inqTSObj = new maintEmpLoanObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$pager = new AjaxPager(8,'../../../images/');

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
$qryIntMaxRec = "Select tblEmpLoansDtlHist.* from tblEmpLoansDtlHist Inner Join tblEmpLoans on tblEmpLoansDtlHist.empNo=tblEmpLoans.empNo and tblEmpLoansDtlHist.lonTypeCd=tblEmploans.lonTypeCd and tblEmpLoansDtlHist.lonRefNo=tblEmploans.lonRefNo where lonSeries={$_GET['lonSeries']} and tblEmploans.compCode='{$_SESSION['company_code']}'  and tblEmpLoansDtlHist.compCode='{$_SESSION['company_code']}'";

$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryPayList = "Select  tblEmpLoansDtlHist.* from tblEmpLoansDtlHist Inner Join tblEmpLoans on tblEmpLoansDtlHist.empNo=tblEmpLoans.empNo and tblEmpLoansDtlHist.lonTypeCd=tblEmploans.lonTypeCd and tblEmpLoansDtlHist.lonRefNo=tblEmploans.lonRefNo where lonSeries={$_GET['lonSeries']} and tblEmploans.compCode='{$_SESSION['company_code']}'  and tblEmpLoansDtlHist.compCode='{$_SESSION['company_code']}' Limit $intOffset,$intLimit";
$resPayList = $inqTSObj->execQry($qryPayList);
$resPayList = $inqTSObj->getArrRes($resPayList);
$arrPayDate= $inqTSObj->getPdPayable();
$empLoanBal = $inqTSObj->getEmpLoanBal($_GET['lonSeries']);
?>

<HTML>
<head>
	<script type='text/javascript' src='timesheet_js.js'></script>
</head>
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> <?=$inqTSObj->getEmpLoanInfo($_GET['lonSeries'])?> 			
              </td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<tr>
						  	  <td colspan="3" align="center" class="gridDtlLbl"><div style="height:0px; visibility:hidden;" align="left" >
						<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">
						In
						<?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('holiday_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">                              
                               </div></td>
					  	  </tr>
						  	<tr>
								<td width="10%" class="gridDtlLbl" align="center">#</td>
								<td width="53%" class="gridDtlLbl" align="center">PAYROLL DATE</td>
								<td width="37%" class="gridDtlLbl" align="center">AMOUNT</td>
							</tr>
							<?
							if(count($resPayList) > 0){
								$i=0;
								foreach ($resPayList as $empPayVal){
								
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
							        <?=$inqTSObj->ViewDate($arrPayDate,$empPayVal)?>
					            </div></td>
						    <td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$empPayVal['trnAmountD'];?>
							    </div></td>
								</tr>
							<?
								}
							$arrPd = $inqTSObj->getOpenPeriodwil();								
							$arrCurDed = $inqTSObj->getUnPostedLoans($arrPd,$empLoanBal,$empLoanBal['empNo']);
							if (count($arrCurDed)>0 and ($intOffset+$intLimit) >= $intMaxRec) {
								foreach($arrCurDed as $valDed) {
								
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
								
								?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$i?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
								  
							      <div align="left"><font class="gridDtlLblTxt">
							        <?=date('M d, Y',strtotime($arrPd['pdPayable'])). ' (Unposted)'?>
						      </font></div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$valDed['trnAmountD'];?>
							    </div></td>
						  </tr>								
								<?
								}
							}
							}
							else{
							?>
							<tr>
								<td colspan="15" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="15" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("loan_detailed_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&lonSeries='.$_GET['lonSeries']);?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$inqTSObj->disConnect();?>
	</BODY>
</HTML>
