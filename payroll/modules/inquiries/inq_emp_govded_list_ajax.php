<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("inq_emp_loans_obj.php");

$inqEmpLoanObj = new inqEmpLoanObj();
$sessionVars = $inqEmpLoanObj->getSeesionVars();
$inqEmpLoanObj->validateSessions('','MODULES');

$pager = new AjaxPager(10,'../../../images/');
$arrSrch = array('REF NO.','ACTIVE (status)','HELD (status)','PROCESSED (status)');
$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
$empNo = $_GET['empNo'];
$from = $_GET['from'];
$to = $_GET['to'];
$orderBy = $_GET['orderBy'];

$urlPara = "&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&loanTypeAll=$loanTypeAll&loanType=$loanType&loanStatus=$loanStatus&orderBy=$orderBy&empNo=$empNo";
$url = "inq_emp_loans_list_ajax.php";
$qryIntMaxRec = "Select empNo from tblMtdGovtHist where (convert(varchar(2),pdMonth) +'/30'+'/'+convert(varchar(4),pdYear)) between convert(datetime,'$from') and convert(datetime,'$to') and  compCode='{$_SESSION['company_code']}' and empNo='$empNo'";

$resIntMaxRec = $inqEmpLoanObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
$qryGovList = "SELECT TOP $intLimit *
			   FROM tblMtdGovtHist WHERE seqno NOT IN  (SELECT TOP $intOffset seqno from tblMtdGovtHist
				   WHERE (convert(varchar(2),pdMonth) +'/30'+'/'+convert(varchar(4),pdYear)) between convert(datetime,'$from') and convert(datetime,'$to') and  compCode='{$_SESSION['company_code']}' and empNo='$empNo') AND (convert(varchar(2),pdMonth) +'/30'+'/'+convert(varchar(4),pdYear)) between convert(datetime,'$from') and convert(datetime,'$to') and  compCode='{$_SESSION['company_code']}' and empNo='$empNo'";
$qryGovList.=" ORDER BY pdYear,pdMonth";
	
$resGovList = $inqEmpLoanObj->execQry($qryGovList);
$arrGovList = $inqEmpLoanObj->getArrRes($resGovList);

$fileName = $_GET['fileName'];
$inputId = $_GET['inputId'];
$empInfo = $inqEmpLoanObj->getUserInfo($_SESSION['company_code'],$empNo,'');
?>
<HTML>
<head>
	<script type='text/javascript' src='inq_emp_loans_js.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
				  	<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Employee Govt Contributions - <?=$empInfo['empLastName'] . ", " . $empInfo['empFirstName'] . " " .$empInfo['empMidName'][0] . ". ($from to $to)" ;?></td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					
            <td colspan="9" class="gridToolbar" align=""> &nbsp; <a href="inq_emp_govded_list_pdf.php?<?='empNo=' .$_GET['empNo'].'&from='.$_GET['from'].'&to='.$_GET['to']?>" target="_blank"> 
              <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Loans List"></a> 
              &nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; <input name="back" type="button" id="back" value="Back" onClick="location.href='inq_emp_gov.php';">            </td>

							<tr>
								<td width="3%" class="gridDtlLbl" align="center">#</td>
								<td width="21%" class="gridDtlLbl" align="center">MONTH - YEAR</td>
								<td width="9%" class="gridDtlLbl" align="center">SSS</td>
								<td width="12%" class="gridDtlLbl" align="center">SSS (EMPLOYER)</td>
								<td width="10%" class="gridDtlLbl" align="center">EC</td>
								<td width="12%" class="gridDtlLbl" align="center">PHIC</td>
								<td width="10%" class="gridDtlLbl" align="center">PHIC (EMPLOYER)</td>
								<td width="9%" class="gridDtlLbl" align="center">HDMF.</td>
								<td width="14%" class="gridDtlLbl" align="center">HDMF (EMPLOYER)</td>
							</tr>
							<?
							if($inqEmpLoanObj->getRecCount($resGovList) > 0){
								$i=0;
								foreach ($arrGovList as $val){
									
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
								$div = $inqEmpLoanObj->getDivDescArt($sessionVars['compCode'], $govListVal['empDiv']);
								$ch=0;
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td class="gridDtlVal"><div align="center">
								  <?=$i?>
							    </div></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=date('M Y',strtotime($val['pdMonth'] . '/1/' . $val['pdYear']))?></font></td>
								<td class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
							    <?=number_format($val['sssEmp'],2)?>
							    </font></div></td>
								<td class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
							    <?=number_format($val['sssEmplr'],2)?>
							    </font></div></td>
								<td class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
							    <?=number_format($val['ec'],2)?>
							    </font></div></td>
								<td class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
							    <?=number_format($val['phicEmp'],2)?>
							    </font></div></td>
								<td class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
							    <?=number_format($val['phicEmplr'],2)?>
							    </font></div></td>
								<td class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
							    <?=number_format($val['hdmfEmp'],2)?>
							    </font></div></td>
								<td class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
							    <?=number_format($val['hdmfEmplr'],2)?>
							    </font></div></td>
							</tr>
							<?		}
							}
							else{
							?>
							<tr>
								<td colspan="9" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="9" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("inq_emp_loans_list_ajax.php","empMastCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&loanTypeAll=".$loanTypeAll."&loanType=".$loanType."&loanStatus=".$loanStatus."&orderBy=".$orderBy."&groupType=".$groupType);?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$inqEmpLoanObj->disConnect();?>
		<form name="frmEmpLoan" method="post">
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
		  <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
		  <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
		  <input type="hidden" name="loanTypeAll" id="loanTypeAll" value="<? echo $_GET['loanTypeAll']; ?>">
		  <input type="hidden" name="loanType" id="loanType" value="<? echo $_GET['loanType']; ?>">
		  <input type="hidden" name="loanStatus" id="loanStatus" value="<? echo $_GET['loanStatus']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		  <input type="hidden" name="groupType" id="groupType" value="<? echo $_GET['groupType']; ?>">
		  <input type="hidden" name="txtSrch2" id="txtSrch2" value="<?php echo $_GET['txtSrch']; ?>">
		  <input type="hidden" name="isSearch2" id="isSearch2" value="<?php echo $_GET['isSearch']; ?>">
		  <input type="hidden" name="srchType2" id="srchType2" value="<?php echo $_GET['srchType']; ?>">
		</form>
	</BODY>
</HTML>

