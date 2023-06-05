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
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$loanTypeAll = $_GET['loanTypeAll'];
$loanType = $_GET['loanType'];
$loanStatus = $_GET['loanStatus'];
$orderBy = $_GET['orderBy'];

$urlPara = "&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&loanTypeAll=$loanTypeAll&loanType=$loanType&loanStatus=$loanStatus&orderBy=$orderBy&empNo=$empNo";
$url = "inq_emp_loans_list_ajax.php";
if ($empNo > "") $empNoNew = " AND empNo = '{$empNo}' "; else $empNoNew = "";
if ($empDiv > 0) $empDivNew = " AND tblEmpMast.empDiv LIKE '{$empDiv}' "; else $empDivNew = "";
if ($empDept > 0) $empDeptNew = " AND tblEmpMast.empDepCode LIKE '{$empDept}' "; else $empDeptNew = "";
if ($empSect > 0) $empSectNew = " AND tblEmpMast.empSecCode LIKE '{$empSect}' "; else $empSectNew = "";

if ($loanTypeAll < 4) $loanTypeAllNew = " AND tblEmpLoans.lonTypeCd LIKE '{$loanTypeAll}%' AND tblEmpLoans.lonTypeCd LIKE '{$loanTypeAll}%' "; else $loanTypeAllNew = "";
if ($loanType > 0) $loanTypeNew = " AND tblEmpLoans.lonTypeCd = '{$loanType}' AND tblEmpLoans.lonTypeCd = '{$loanType}' "; else $loanTypeNew = "";
if ($groupType < 3) $groupTypeNew = " AND tblEmpMast.empPayGrp = $groupType "; else $groupTypeNew = "";
$lonstr = "AND empNo IN (Select empNo from tblEmpLoans where compCode='{$_SESSION['company_code']}' $empNoNew $loanTypeAllNew $loanTypeNew)";
$qryIntMaxRec = "Select empNo,empLastName,empFirstName,empMidName,empDiv from tblEmpMast
				   WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblEmpMast.empPayGrp='".$_SESSION['pay_group']."' 
				   $empNoNew $lonstr";

$resIntMaxRec = $inqEmpLoanObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
 $qryLoanList = "SELECT  empNo,empLastName,empFirstName,empMidName ,empDiv
			   FROM tblEmpMast WHERE  tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblEmpMast.empPayGrp='".$_SESSION['pay_group']."' $empNoNew $lonstr";
$qryLoanList.=" ORDER BY tblEmpMast.empLastName,tblEmpMast.empFirstName,tblEmpMast.empMidName limit $intOffset,$intLimit";
	
$resLoanList = $inqEmpLoanObj->execQry($qryLoanList);
$arrLoanList = $inqEmpLoanObj->getArrRes($resLoanList);

$fileName = $_GET['fileName'];
$inputId = $_GET['inputId'];
$arrEmpLoans =  $inqEmpLoanObj->getEmpLoans(" $loanTypeAllNew $loanTypeNew");
?>
<HTML>
<head>
	<script type='text/javascript' src='inq_emp_loans_js.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
				  	<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Employee Loans Status / Deductions
						<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
						  <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
						  <?=$inqEmpLoanObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					
            <td colspan="12" class="gridToolbar" align=""> &nbsp;
              <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Loans List"  onClick="printLoanList();">
              
              &nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; <input name="back" type="button" id="back" value="Back" onClick="location.href='inq_emp_loans.php';">
            </td>
							<? if ($orderBy==1) {?>
							<tr>
								<td width="1%" class="gridDtlLbl" align="center">#</td>
								<td width="5%" class="gridDtlLbl" align="center">EMP.NO.</td>
								<td width="20%" class="gridDtlLbl" align="center">NAME</td>
							  <td width="15%" class="gridDtlLbl" align="center">DIVISION</td>
								<td width="10%" class="gridDtlLbl" align="center">LOAN TYPE</td>
							  <td width="5%" class="gridDtlLbl" align="center">REF.NO.</td>
								<td width="10%" class="gridDtlLbl" align="center">TOTAL AMT.</td>
								<td width="10%" class="gridDtlLbl" align="center">DEDUCTION</td>
								<td width="5%" align="center" class="gridDtlLbl">PAYMENTS</td>
								<td width="10%" class="gridDtlLbl" align="center">BALANCE</td>
								<td width="10%" class="gridDtlLbl" align="center">STATUS</td>
							  <td class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<? } else {?>
							<tr>
								<td width="1%" class="gridDtlLbl" align="center">#</td>
								<td width="15%" class="gridDtlLbl" align="center">LOAN TYPE</td>
								<td width="5%" class="gridDtlLbl" align="center">REF.NO.</td>
								<td width="10%" class="gridDtlLbl" align="center">TOTAL AMT.</td>
								<td width="10%" class="gridDtlLbl" align="center">DEDUCTION</td>
								<td width="10%" class="gridDtlLbl" align="center">PAYMENTS</td>
								<td width="10%" class="gridDtlLbl" align="center">BALANCE</td>
								<td width="10%" class="gridDtlLbl" align="center">STATUS</td>
								<td width="5%" class="gridDtlLbl" align="center">EMP.NO.</td>
								<td width="15%" class="gridDtlLbl" align="center">NAME</td>
								<td width="15%" class="gridDtlLbl" align="center">DEPARTMENT</td>
								<td class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<? } ?>
							<?
							if($inqEmpLoanObj->getRecCount($resLoanList) > 0){
								$i=0;
								foreach ($arrLoanList as $loanListVal){
									
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
								$div = $inqEmpLoanObj->getDivDescArt($sessionVars['compCode'], $loanListVal['empDiv']);
								$ch=0;
								foreach ($arrEmpLoans as $valLoan) {
									if ($valLoan['empNo'] == $loanListVal['empNo']) {
										if ($ch == 0) {
											$name = $loanListVal['empFirstName']. " " . $loanListVal['empLastName'];
											$empNox = $loanListVal['empNo'];
											$no = $i;
											$ch=1;
										} else {
											$name="";
											$empNox="";
											$no="";
										}
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
							<? if ($orderBy==1) { ?>
								<td class="gridDtlVal"><?=$no?></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empNox?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$name?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$div['deptShortDesc']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$valLoan['lonTypeShortDesc']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$valLoan['lonRefNo']?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=number_format($valLoan['lonWidInterst'],2)?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=number_format($valLoan['lonDedAmt2'],2)?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=number_format($valLoan['lonPayments'])?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=number_format($valLoan['lonCurbal'],2)?></font></td>
								<td class="gridDtlVal" style="text-align: center;"><font class="gridDtlLblTxt">
								<?
                                switch($valLoan['lonStat']) {
									case 'O':
										echo "Open";
									break;
									case 'C':
										echo "Closed";
									break;
									case 'T':
										echo "Terminated";
									break;
								}
								?>
                                </font></td>
								<td class="gridDtlVal" align="center"> 
                                <a href="#" onClick="viewDetails('<?=$urlPara?>','<?=$loanListVal['empNo']?>','<?=$valLoan['lonTypeCd']?>','<?=str_replace("#","_",$valLoan['lonRefNo'])?>','<?=$url?>','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="actionImg" src="../../../images/application_form_magnify.png" title="Deduction Details"></a>
                                <a href="#" onClick="viewLoanInfo('<?=$loanListVal['empNo']?>','<?=$valLoan['lonTypeCd']?>','<?=str_replace("#","_",$valLoan['lonRefNo'])?>')"<img class="actionImg" src="../../../images/printer.png" title="View Loan Information"></a>
                                </td>
							<? } else { ?>
								<td class="gridDtlVal"><?=$i?></td>								
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$valLoan['lonTypeShortDesc']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$valLoan['lonRefNo']?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=number_format($valLoan['lonWidInterst'],2)?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=number_format($valLoan['lonDedAmt2'],2)?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=number_format($valLoan['lonPayments'],2)?></font></td>
								<td class="gridDtlVal" style="text-align: right;"><font class="gridDtlLblTxt"><?=number_format($valLoan['lonCurbal'],2)?></font></td>
<td class="gridDtlVal" style="text-align: center;"><font class="gridDtlLblTxt"><?
                                switch($valLoan['lonStat']) {
									case 'O':
										echo "Open";
									break;
									case 'C':
										echo "Closed";
									break;
									case 'T':
										echo "Terminated";
									break;
								}
								?></font></td>                                
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$loanListVal['empNo']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$loanListVal['empFirstName']. " " . $loanListVal['empLastName']?></font></td>
								<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$div['deptShortDesc']."/".$dept['deptShortDesc']."/".$sect['deptShortDesc']?></font></td>
								<td class="gridDtlVal" align="center"> <a href="#" onClick="viewDetails('<?=$urlPara?>''<?=$loanListVal['empNo']?>','<?=$valLoan['lonTypeCd']?>','<?=str_replace("#","_",$valLoan['lonRefNo'])?>','<?=$url?>','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img class="actionImg" src="../../../images/application_form_magnify.png" title="Deduction Details"></a>
 <a href="#" onClick="viewLoanInfo('<?=$loanListVal['empNo']?>','<?=$valLoan['lonTypeCd']?>','<?=str_replace("#","_",$valLoan['lonRefNo'])?>')"<img class="actionImg" src="../../../images/printer.png" title="View Loan Information"></a>
                               </td>								
							<? } ?>
							</tr>
							<?			}
									}
								}
							}
							else{
							?>
							<tr>
								<td colspan="11" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="12" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("inq_emp_loans_list_ajax.php","empMastCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&loanTypeAll=".$loanTypeAll."&loanType=".$loanType."&loanStatus=".$loanStatus."&orderBy=".$orderBy."&groupType=".$groupType);?>
								</td>
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

