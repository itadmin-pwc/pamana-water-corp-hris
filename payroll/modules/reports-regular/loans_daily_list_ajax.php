<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');

$pager = new AjaxPager(20,'../../../images/');

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
$from = $_GET['from'];
$to = $_GET['to'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
		$div =" where compCode='{$_SESSION['company_code']}' and lonStat='O'";
		if ($empDiv != 0) {
			$div .= " and empDiv = '{$_GET['empDiv']}'";
		} 
		if ($empDept != 0) { 
			$div .= " and empDepCode = '{$_GET['empDept']}'";
		}
		if ($empSect != 0) { 
			$div .= " and empSecCode = '{$_GET['empSect']}'";
		}
		if (!empty($from) && !empty($to)) {
			$dt = "$from - $to";
			$from = date('Y-m-d',strtotime($from));
			$to = date('Y-m-d',strtotime($to));
			$div .= " and cast(dateadded as date) between '$from' and '$to'";
		} else {
			$today = date('m/d/Y');
			$dt = "$today";
			$today = date('Y-m-d');
			$div .= " and cast(dateadded as date) = '$today'";
		}
		
$qryIntMaxRec = "Select * from view_loansDailReport $div";

$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "Select * from view_loansDailReport $div  Limit $intOffset,$intLimit"; 
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
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> NEW LOANS DAILY REPORT (
			    <?=$dt;?>)</td>
			  </tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	    <td colspan="27" class="gridToolbar" align=""> &nbsp; <a href="loans_daily_pdf.php?empDiv=<?=$_GET['empDiv']?>&empDept=<?=$_GET['empDept']?>&empSect=<?=$_GET['empSect']?>&from=<?=$_GET['from']?>&to=<?=$_GET['to']?>" target="_blank" title="Print Daily Loans Report"> 
							<img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Daily Loans Report"> NEW LOANS DAILY REPORT</a>&nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; 
							<input name="back" type="button" id="back" value="Back" onClick="location.href='loans_daily_report.php';"> &nbsp; 
                <FONT class="ToolBarseparator">|</font> &nbsp;</td>
							<tr>
								<td width="1%" class="gridDtlLbl" align="center">#</td>
								<td width="5%" class="gridDtlLbl" align="center">EMP.NO.</td>
								<td width="10%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
								<td width="5%" class="gridDtlLbl" align="center">LOAN TYPE</td>
								<td width="5%" class="gridDtlLbl" align="center">REF NO.</td>
								<td width="5%" class="gridDtlLbl" align="center">LOAN AMOUNT</td>
								<td width="5%" class="gridDtlLbl" align="center">LOAN AMT INC. INTEREST</td>
								<td width="5%" class="gridDtlLbl" align="center">LOAN START</td>
								<td width="5%" class="gridDtlLbl" align="center">LOAN END</td>
								<td width="5%" class="gridDtlLbl" align="center">SCHEDULE</td>
								<td width="5%" class="gridDtlLbl" align="center">DED. PER SCHEDULE</td>
								<td width="5%" class="gridDtlLbl" align="center">TOTAL PAYMENTS TO-DATE</td>
								<td width="5%" class="gridDtlLbl" align="center">CUR. BAL.</td>
							    <td width="5%" class="gridDtlLbl" align="center">LAST PAYMENT</td>
					          <td width="5%" class="gridDtlLbl" align="center">SETUP DATE</td>
							</tr>
							<?
							if($inqTSObj->getRecCount($resEmpList) > 0){
								$i=0;
								$ch=0;
								foreach ($arrEmpList as $empListVal){
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
								if ($empListVal['empNo'] != $empNo2) {
									$name = $empListVal['empLastName']. " " . $empListVal['empFirstName'][0] . "." . $empListVal['empMidName'][0].".";
									$empNox = $empListVal['empNo'];
								} else {
									$name="";
									$empNox="";
								}
								$empNo2 = $empListVal['empNo'];
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal"><?=$i?></td>
								<td class="gridDtlVal"><?=$empNox?></td>
								<td class="gridDtlVal"><?=$name?></td>
								<td class="gridDtlVal" align="right"><div align="left"><font class="gridDtlLblTxt">
							    <?=$empListVal['lonTypeShortDesc']?>
							    </font></div></td>
								<td class="gridDtlVal" align="right"><div align="center"><font class="gridDtlLblTxt">
							    <?=$empListVal['lonRefNo']?>
							    </font></div></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$empListVal['lonAmt']?></font></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$empListVal['lonWidInterst']?></font></td>
								<td class="gridDtlVal" align="right"><div align="center"><font class="gridDtlLblTxt">
							    <?=date('m/d/Y',strtotime($empListVal['lonStart']))?>
							    </font></div></td>
								<td class="gridDtlVal" align="right"><div align="center"><font class="gridDtlLblTxt">
							    <?=date('m/d/Y',strtotime($empListVal['lonEnd']))?>
							    </font></div></td>
								<td class="gridDtlVal" align="right"><div align="center"><font class="gridDtlLblTxt">
							    <?
                                switch($empListVal['lonSked']) {
									case "1":
										echo "1st Period";
									break;
									case "2":
										echo "2nd Period";
									break;
									case "3":
										echo "Both Period";
									break;
								}
								?>
							    </font></div></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$empListVal['lonDedAmt2']?></font></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$empListVal['lonPayments']?></font></td>
								<td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=$empListVal['lonCurbal']?></font></td>
								<td class="gridDtlVal" align="right"><div align="center">
								  <? if (!empty($empListVal['lonLastPay'])) {
								echo date('m/d/Y',strtotime($empListVal['lonLastPay']));
								}?>
							    </div></td>
								<td class="gridDtlVal" align="right"><div align="center"><font class="gridDtlLblTxt">
							    <?=date('m/d/Y',strtotime($empListVal['dateadded']))?>
							    </font></div></td>
							</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="27" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="27" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("loans_daily_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&from='.$from."&to=".$to."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect);?>								</td>
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
		  <input type="hidden" name="groupType" id="groupType" value="<? echo $_GET['groupType']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		  <input type="hidden" name="catType" id="catType" value="<? echo $_GET['catType']; ?>">
		  <input type="hidden" name="payPd" id="payPd" value="<? echo $_GET['payPd']; ?>">
		</form>
	</BODY>
</HTML>
