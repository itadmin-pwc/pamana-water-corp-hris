<?php
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("timesheet_obj.php");
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');

	$pager = new AjaxPager(4,'../../../images/');
	
	$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
	$empNo = $_GET['empNo'];
	$empName = $_GET['empName'];
	$empDiv = $_GET['empDiv'];
	$empDept = $_GET['empDept'];
	$empSect = $_GET['empSect'];
	$orderBy = $_GET['orderBy'];
	$payPd = $_GET['payPd'];
	$locType = $_GET['locType'];
	$empBrnCode = $_GET['empBrnCode'];
	$arrPayPd = $inqTSObj->getSlctdPd($compCode,$payPd);
	$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'],  $_SESSION['pay_category']);
	
	if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
	//if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
	if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
	if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
	if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($locType=="S")
		$locType1 = " AND (empLocCode = '{$empBrnCode}')";
	if ($locType=="H")
		$locType1 = " AND (empLocCode = '0001')";
	$empStat = ($_SESSION['pay_category'] !=9) ? " AND empStat NOT IN('RS','IN','TR') ":"";	
	
	$qryIntMaxRec = "SELECT * FROM tblEmpMast 
					 WHERE compCode = '{$sessionVars['compCode']}'
					 AND empPayGrp = '{$_SESSION['pay_group']}'
			     	 AND empPayCat = '{$_SESSION['pay_category']}'
					 $empStat $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $empBrnCode1 $locType1
					 $orderBy1 ";
	
	$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryEmpList = "SELECT  *
			FROM tblEmpMast
			WHERE compCode = '{$sessionVars['compCode']}'
					AND empPayGrp = '{$_SESSION['pay_group']}'
			        AND empPayCat = '{$_SESSION['pay_category']}'
					$empStat $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $empBrnCode1 $locType1
					$orderBy1 Limit $intOffset,$intLimit ";
	
	$resEmpList = $inqTSObj->execQry($qryEmpList);
	$arrEmpList = $inqTSObj->getArrRes($resEmpList);
?>

<HTML>
<head>
	<script type='text/javascript' src='timesheet_js.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;TRANSACTIONS TIMESHEET PROOFLIST REPORT :: <?php echo "GROUP - ".$_SESSION["pay_group"];?> :: <?=strtoupper($catName['payCatDesc']);?> :: PAYROLL PERIOD=<?=$inqTSObj->valDateArt($arrPayPd['pdPayable'])?> :: FROM=<?=$inqTSObj->valDateArt($arrPayPd['pdFrmDate'])?> :: TO=<?=$inqTSObj->valDateArt($arrPayPd['pdToDate'])?>
				<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
				  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
				  <?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
				</div></td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<td colspan="11" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printTSList();" title="Timesheet List"> 
							<img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Timesheet List">Timesheet List
							</a>&nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; 
							<input class="inputs" name="back" type="button" id="back" value="BACK" onClick="location.href='timesheet.php';">
            				</td>
							
							<?
							
							if($inqTSObj->getRecCount($resEmpList) > 0){
								$i=0;
								foreach ($arrEmpList as $empListVal){
								$arrTotal = $inqTSObj->getTimeSheetTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdFrmDate'],$arrPayPd['pdToDate']);
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal">(<?=$i?>)&nbsp;<?=$empListVal['empLastName']. " " . $empListVal['empFirstName'] . ", " . $empListVal['empMidName']?>&nbsp;<?=$empListVal['empNo']?></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt">&nbsp;</font></td>
								
					
							  
              <td class="gridDtlVal" align="center"> <a href="#" onClick="location.href='timesheet.php?hide_option=new_&empNo=<?=$empListVal['empNo']?>'"> 
                </a></td>
							</tr>
							<tr> 
								<td colspan="4" align="center">
										<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid2" >
											<tr>
												<td width="5%" class="gridDtlLbl" align="left">DATE</td>
												<td width="10%" class="gridDtlLbl" align="left">ABSENCE</td>
												<td width="10%" class="gridDtlLbl" align="left">TARDY</td>
												<td width="10%" class="gridDtlLbl" align="left">UNDERTIME</td>
												<td width="10%" class="gridDtlLbl" align="left">OT</td>
												<td width="10%" class="gridDtlLbl" align="left">OT > 8</td>
												<td width="10%" class="gridDtlLbl" align="left">ND REG</td>
												<td width="10%" class="gridDtlLbl" align="left">ND OT</td>
											</tr>
											<?
											
											$qryTSList = "SELECT * FROM ".$_GET['reportType']." 
														  WHERE compCode = '{$sessionVars['compCode']}' AND 
																empPayGrp = '{$_SESSION['pay_group']}' AND 
																empPayCat = '{$_SESSION['pay_category']}' AND 
																tsStat = 'A' AND  
																empNo = '{$empListVal['empNo']}' AND 
																tsDate >= '{$arrPayPd['pdFrmDate']}' AND tsDate <= '{$arrPayPd['pdToDate']}' 
																ORDER BY tsDate ASC ";
											$resTSList = $inqTSObj->execQry($qryTSList);
											$arrTSList = $inqTSObj->getArrRes($resTSList);
											if($inqTSObj->getRecCount($resTSList) > 0){
												$ii=0;
												foreach ($arrTSList as $TSVal){
												$bgcolor2 = ($ii++ % 2) ? "#FFFFFF" : "#F8F8FF";
												$on_mouse2 = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
												. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor2  . '\';"';						
											?>
                                           
											<tr bgcolor2="<?php echo $bgcolor2; ?>">
                                            	
												<td width="5%" class="gridDtlVal"><font class="gridDtlLblTxt"><? echo $inqTSObj->valDateArt($TSVal['tsDate']); ?></font></td>
												<td width="10%" class="gridDtlVal" align="left"><font class="gridDtlLblTxt" ><?=$TSVal['hrsAbsent']. " / " . $TSVal['amtAbsent']?></font></td>
												<td width="10%" class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=$TSVal['hrsTardy']. " / " . $TSVal['amtTardy']?></font></td>
												<td width="10%" class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=$TSVal['hrsUt']. " / " . $TSVal['amtUt']?></font></td>
												<td width="10%" class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=$TSVal['hrsOtLe8']. " / " . $TSVal['amtOtLe8']?></font></td>
												<td width="10%" class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=$TSVal['hrsOtGt8']. " / " . $TSVal['amtOtGt8']?></font></td>
												<td width="10%" class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=$TSVal['hrsRegNDLe8']. " / " . $TSVal['amtRegNDLe8']?></font></td>
												<td width="10%" class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><?=$TSVal['hrsNdLe8']. " / " . $TSVal['amtNdLe8']?></font></td>
                                               
											</tr>
                                            
											<?
												}
												?>
                                                
												<tr bgcolor2="<?php echo $bgcolor2; ?>">
													<td class="gridDtlVal"><font class="gridDtlLblTxt"><b>TOTAL</b></font></td>
													<td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><b><?=$arrTotal['totHrsAbsent']. " / " . $arrTotal['totAmtAbsent']?></b></font></td>
													<td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><b><?=$arrTotal['totHrsTardy']. " / " . $arrTotal['totAmtTardy']?></b></font></td>
													<td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><b><?=$arrTotal['totHrsUt']. " / " . $arrTotal['totAmtUt']?></b></font></td>
													<td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><b><?=$arrTotal['totHrsOtLe8']. " / " . $arrTotal['totAmtOtLe8']?></b></font></td>
													<td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><b><?=$arrTotal['totHrsOtGt8']. " / " . $arrTotal['totAmtOtGt8']?></b></font></td>
													<td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><b><?=$arrTotal['tothrsRegNDLe8']. " / " . $arrTotal['totamtRegNDLe8']?></b></font></td>
													<td class="gridDtlVal" align="left"><font class="gridDtlLblTxt"><b><?=$arrTotal['totHrsNdLe8']. " / " . $arrTotal['totAmtNdLe8']?></b></font></td>
												</tr>
                                               	
											<?
											}
											else{
											?>
											<tr style="height:25px;">
												<td colspan="9" align="center" class="gridDtlVal"><font class="gridDtlLblTxt"><b>NOTHING TO DISPLAY</b></font></td>
											</tr>
											<?}?>
										</TABLE>
								</td>
							</tr>
							<?
								}
							}
							else{
							?>
							<tr style="height:25px;">
												<td colspan="9" align="center" class="gridDtlVal"><font class="gridDtlLblTxt"><b>NOTHING TO DISPLAY</b></font></td>
											</tr>
							<? } ?>
							<tr>
								<td colspan="4" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("timesheet_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&orderBy=".$orderBy."&payPd=".$payPd."&reportType=".$_GET["reportType"]."&empBrnCode=".$empBrnCode."&locType=".$locType."&empLoc=".$empLoc);?>
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>
		</div>
		<?$inqTSObj->disConnect();?>
		<form name="frmEmpList" method="post">
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empName" id="empName" value="<? echo $_GET['empName']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		  <input type="hidden" name="payPd" id="payPd" value="<? echo $_GET['payPd']; ?>">
          <input type="hidden" name="reportType" id="reportType" value="<? echo $_GET['reportType']; ?>">
		</form>
	</BODY>
</HTML>
