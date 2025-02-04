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

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
$empNoB = $_GET['empNoB'];
$lonTypeCd = $_GET['lonTypeCd'];
$lonRefNo = str_replace("_","#",$_GET['lonRefNo']);

$qryIntMaxRec = "SELECT * FROM tblEmpLoansDtlHist 
			     WHERE compCode = '{$sessionVars['compCode']}' AND empNo = '{$empNoB}' AND lonTypeCd = '{$lonTypeCd}' AND lonRefNo = '{$lonRefNo}' ";

$resIntMaxRec = $inqEmpLoanObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryLnDtlList = "SELECT  *
		FROM tblEmpLoansDtlHist
		WHERE compCode = '{$sessionVars['compCode']}' AND empNo = '{$empNoB}' AND lonTypeCd = '{$lonTypeCd}' AND lonRefNo = '{$lonRefNo}' ORDER BY pdYear,pdNumber ASC limit $intOffset,$intLimit";
$resLnDtlList = $inqEmpLoanObj->execQry($qryLnDtlList);
$arrLnDtlList = $inqEmpLoanObj->getArrRes($resLnDtlList);

$empInfo = $inqEmpLoanObj->getUserInfo($sessionVars['compCode'],$empNoB,"");
$empLoanBal = $inqEmpLoanObj->getEmpLoanBal($sessionVars['compCode'],$empNoB,$lonTypeCd,$lonRefNo);

?>

<HTML>
<head>
	<script type='text/javascript' src='inq_emp_loans_js.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			
  <TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
    <tr> 
      <td colspan="6" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"><? echo $empNoB."-".$empInfo['empLastName'].", ".$empInfo['empFirstName'][0].".".$empInfo['empMidName'][0].". ::: ".$inqEmpLoanObj->getLoanDesc($sessionVars['compCode'],$lonTypeCd)." ::: Ref.No.:".$lonRefNo;?> 
        <div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;"> 
          <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
          <?=$inqEmpLoanObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
        </div></td>
    </tr>
    <tr> 
      <td colspan="1" class="parentGridHdr"> 
        <table width="100%" border="0" class="parentGrid" cellpadding="1" cellspacing="0">
          <tr> 
            <td width="16%" class="gridDtlLbl" >Start</td>
            <td width="8%" class="gridDtlLbl"><? echo $inqEmpLoanObj->valDateArt($empLoanBal['lonStart']);?></td>
            <td width="11%" class="gridDtlLbl" >&nbsp;</td>
            <td width="11%" class="gridDtlLbl" >Total Amt.</td>
            <td width="10%" class="gridDtlLbl" align="right"><? echo $empLoanBal['lonWidInterst'];?></td>
            <td width="44%" class="gridDtlLbl" >&nbsp;</td>
          </tr>
          <tr> 
            <td class="gridDtlLbl" >End</td>
            <td class="gridDtlLbl"><? echo $inqEmpLoanObj->valDateArt($empLoanBal['lonEnd']);?></td>
            <td class="gridDtlLbl" >&nbsp;</td>
            <td class="gridDtlLbl" >Payments</td>
            <td class="gridDtlLbl" align="right"><? echo $empLoanBal['lonPayments'];?></td>
            <td class="gridDtlLbl" >&nbsp;</td>
          </tr>
          <tr> 
            <td class="gridDtlLbl" >Period of Ded.</td>
			<?	$empLoanAmount = $empLoanBal['lonWidInterst'];
				if ($empLoanBal['lonSked']==1) $sked ="1st";
				if ($empLoanBal['lonSked']==2) $sked ="2nd";
				if ($empLoanBal['lonSked']==3) $sked ="Both";
			?>
            <td class="gridDtlLbl"><? echo $sked;?></td>
            <td class="gridDtlLbl" align="right">&nbsp;</td>
            <td class="gridDtlLbl" align="right"><div align="left">Current Bal.</div></td>
            <td class="gridDtlLbl" align="right"><? echo $empLoanBal['lonCurbal'];?></td>
            <td class="gridDtlLbl" align="right">&nbsp;</td>
          </tr>
          <tr> 
            <td class="gridDtlLbl" >Total Terms</td>
            <td class="gridDtlLbl"><? echo $empLoanBal['lonNoPaymnts'];?></td>
            <td class="gridDtlLbl" align="right">&nbsp;</td>
            <td class="gridDtlLbl" align="right">&nbsp;</td>
            <td class="gridDtlLbl" align="right">&nbsp;</td>
            <td class="gridDtlLbl" align="right">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td class="parentGridDtl"> <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
	  		<td colspan="13" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printLoanDedList();"> 
              <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Loans Deduction List"></a>            </td>
	  		
          <tr> 
            <td width="2%" class="gridDtlLbl" align="center">#</td>
            <td width="19%" class="gridDtlLbl" align="center">PAYROLL DATE</td>
            <td width="23%" class="gridDtlLbl" align="center">DEDUCTION SCHED</td>
            <td width="22%" class="gridDtlLbl" align="center">ACTUAL DEDUCTION</td>
            <td width="34%" class="gridDtlLbl" align="center">BALANCE</td>
          </tr>
          <?
							if($inqEmpLoanObj->getRecCount($resLnDtlList) > 0){
								$i=0;
								foreach ($arrLnDtlList as $lnDtlListVal){
								$empLoanAmount = round($empLoanAmount,2) - round($lnDtlListVal['ActualAmt'],2);
								$pdDate = $inqEmpLoanObj->getPayPd($sessionVars['compCode'],$lnDtlListVal['pdYear'],$lnDtlListVal['pdNumber'],$lnDtlListVal['trnCat'],$lnDtlListVal['trnGrp']);
								$employee = $inqEmpLoanObj->getUserInfo($sessionVars['compCode'],$lnDtlListVal['empNo'],"");
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
          <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>> 
            <td class="gridDtlVal">
              <?=$i?>            </td>
            <td class="gridDtlVal"><font class="gridDtlLblTxt">
              <?=date('M d, Y',strtotime($pdDate['pdPayable']))?>
              </font></td>
            <td align="center" class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
              <?=$lnDtlListVal['trnAmountD']?>
            </font></div></td>
            <td align="center" class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
              <?=$lnDtlListVal['ActualAmt']?>
            </font></div></td>
            <td class="gridDtlVal" align="center"><div align="right"><font class="gridDtlLblTxt">
              <?=number_format($empLoanAmount,2) ?>
            </font></div></td>
          </tr>
		<?			}
        		}
			?>
          <?
			$arrPd = $inqEmpLoanObj->getOpenPeriodwil();								
			$arrCurDed = $inqEmpLoanObj->getUnPostedLoans($arrPd,$empLoanBal,$empNoB);
			if (count($arrCurDed)>0 and ($intOffset+$intLimit) >= $intMaxRec) {
				foreach($arrCurDed as $valDed) {
				$empLoanAmount = round($empLoanAmount,2) - round($valDed['ActualAmt'],2);				
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							}?>
              <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>> 
                <td class="gridDtlVal">
                  <?=$i?>                </td>
                <td class="gridDtlVal"><font class="gridDtlLblTxt">
                  <?=date('M d, Y',strtotime($arrPd['pdPayable'])). ' (Unposted)'?>
                  </font></td>
                <td align="center" class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
                  <?=$valDed['trnAmountD'] ?>
                </font></div></td>
                <td align="center" class="gridDtlVal"><div align="right"><font class="gridDtlLblTxt">
                <?=$valDed['ActualAmt'] ?>
                </font></div></td>
                <td class="gridDtlVal" align="center"><div align="right"><font class="gridDtlLblTxt">
                  <?=number_format($empLoanAmount,2) ?>
                </font></div></td>
              </tr>
							
							<?		
					}
				if($inqEmpLoanObj->getRecCount($resLnDtlList) == 0 and count($arrCurDed) == 0) {
							?>
          <tr> 
            <td colspan="6" align="center"> <FONT class="zeroMsg">NOTHING TO DISPLAY</font>            </td>
          </tr>
          <?}?>
          <tr> 
            <td colspan="8" align="center" class="childGridFooter"> 
              <?$pager->_viewPagerButton("inq_emp_loans_list_details_ajax.php","empMastCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNoB='.$empNoB.'&lonTypeCd='.$lonTypeCd.'&lonRefNo='.$lonRefNo);?>            </td>
          </tr>
      </TABLE></td>
    </tr>
  </TABLE>
		</div>
		<?$inqEmpLoanObj->disConnect();?>
		<form name="frmEmpLoan" method="post" >
		  <input type="hidden" name="empNoB" id="empNoB" value="<? echo $_GET['empNoB']; ?>">
		  <input type="hidden" name="lonTypeCd" id="lonTypeCd" value="<? echo $_GET['lonTypeCd']; ?>">
		  <input type="hidden" name="lonRefNo" id="lonRefNo" value="<? echo $_GET['lonRefNo']; ?>">
		</form>
	</BODY>
</HTML>
