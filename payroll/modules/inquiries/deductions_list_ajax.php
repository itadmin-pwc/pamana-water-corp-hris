<?
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("timesheet_obj.php");
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$pager = new AjaxPager(10,'../../../images/');
	
	$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
	$empNo = $_GET['empNo'];
	$empName = $_GET['empName'];
	$empDiv = $_GET['empDiv'];
	$empDept = $_GET['empDept'];
	$empSect = $_GET['empSect'];
	$orderBy = $_GET['orderBy'];
	$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $_SESSION['pay_category']);
	$payPd = $_GET['payPd'];
	$arrPayPd = $inqTSObj->getSlctdPd($compCode,$payPd);
	$tbl = $_GET["repType"];
	$reportType = $_GET["reportType"];
	$locType = $_GET['locType'];
	$empBrnCode = $_GET['empBrnCode'];
	
	
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
		
	
	$qryIntMaxRec = "SELECT * FROM tblEmpMast 
					 WHERE compCode = '{$sessionVars['compCode']}'
					 AND empStat NOT IN('RS','IN','TR') 
					 AND empPayGrp = '{$_SESSION['pay_group']}'
					 AND empPayCat = '{$_SESSION['pay_category']}'			     
					 $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 
					  $empBrnCode1 $locType1
					 $orderBy1 ";
	
	$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryEmpList = "SELECT TOP $intLimit *
			FROM tblEmpMast
			WHERE empNo NOT IN
				(SELECT TOP $intOffset empNo FROM tblEmpMast WHERE empStat NOT IN('RS','IN','TR') 
				AND empPayGrp = '{$_SESSION['pay_group']}'
				AND empPayCat = '{$_SESSION['pay_category']}'
				$empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $empBrnCode1 $locType1"; 
	$qryEmpList .= " 
				AND compCode = '{$sessionVars['compCode']}' 
				$orderBy1) 
				AND compCode = '{$sessionVars['compCode']}'
				AND empStat NOT IN('RS','IN','TR') 
				AND empPayGrp = '{$_SESSION['pay_group']}'
				AND empPayCat = '{$_SESSION['pay_category']}'
				$empNo1 $empName1 $empDiv1 $empDept1 $empSect1  
				 $empBrnCode1 $locType1
				$orderBy1 ";
	$resEmpList = $inqTSObj->execQry($qryEmpList);
	$arrEmpList = $inqTSObj->getArrRes($resEmpList);
	$tbl =$_GET['repType'];
?>

<HTML>
    <head>
        <script type='text/javascript' src='timesheet_js.js'></script>
    </head>
    
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
                    	&nbsp;DEDUCTIONS REPORT :: <?php echo "GROUP - ".$_SESSION["pay_group"];?> :: <?=strtoupper($catName['payCatDesc']);?> :: PAYROLL PERIOD=<?=$inqTSObj->valDateArt($arrPayPd['pdPayable'])?> :: FROM=<?=$inqTSObj->valDateArt($arrPayPd['pdFrmDate'])?> :: TO=<?=$inqTSObj->valDateArt($arrPayPd['pdToDate'])?>
              			<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
                            <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
                            <?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						</div>
                    </td>
				</tr>
                
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<td colspan="10" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printDeductionsList('<?=$_GET['repType']?>','<?=$tbl?>');" title="Print Deductions Register"> 
                                <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Deductions Register">Deductions Register
                                </a>&nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; 
                                <input name="back" type="button" id="back" value="Back" onClick="location.href='deductions_register.php';">            				
                            </td>
                            
							<tr>
								<td width="1%" class="gridDtlLbl" align="left">#</td>
								<td width="6%" class="gridDtlLbl" align="left">EMP.NO.</td>
								<td width="10%" class="gridDtlLbl" align="left">EMPLOYEE NAME</td>
								<td width="8%" class="gridDtlLbl" align="right">W.TAX</td>
								<td width="8%" class="gridDtlLbl" align="right">SSS</td>
								<td width="8%" class="gridDtlLbl" align="right">PHILHEALTH</td>
								<td width="8%" class="gridDtlLbl" align="right">PAG - IBIG</td>
								<td width="8%" class="gridDtlLbl" align="right">LOANS</td>
								<td width="8%" class="gridDtlLbl" align="right">OTHER DED</td>
							  <td width="8%" class="gridDtlLbl" align="right">TOTAL</td>
							</tr>
                            
							<?
							if($inqTSObj->getRecCount($resEmpList) > 0)
							{
								$i=0;
								foreach ($arrEmpList as $empListVal)
								{
									$wTaxTotal = $inqTSObj->getWTaxTotal2($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],WTAX,$tbl);//w.tax
									$sssTotal = $inqTSObj->getWTaxTotal2($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],SSS_CONTRIB,$tbl);//sss
									$philhealthTotal = $inqTSObj->getWTaxTotal2($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],PHILHEALTH_CONTRIB,$tbl);//philhealth
									$pagibigTotal = $inqTSObj->getWTaxTotal2($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],PAGIBIG_CONTRIB,$tbl);//pagibig
									$empLoans = $inqTSObj->getloans($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$tbl);//LOANS
									$otherDeductions = $inqTSObj->getotherdeductions($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$tbl);//OTHER DEDUCTIONS
									
									$totalDeductions=0;
									$totalDeductions = $wTaxTotal['totAmt']+$sssTotal['totAmt']+$philhealthTotal['totAmt']+$pagibigTotal['totAmt']+$empLoans['totAmt']+$otherDeductions['totAmt'];
									$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
									$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
									. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
                                    <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                                        <td class="gridDtlVal"><?=$i?></td>
                                        <td class="gridDtlVal"><?=$empListVal['empNo']?></td>
                                        <td class="gridDtlVal"><?=$empListVal['empLastName']. ", " . $empListVal['empFirstName'][0] . "." . $empListVal['empMidName'][0]."."?></td>
                                        <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($wTaxTotal['totAmt']!=0?number_format($wTaxTotal['totAmt'],2):"")?></font></td>
                                        <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($sssTotal['totAmt']!=0?number_format($sssTotal['totAmt'],2):"")?></font></td>
                                        <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($philhealthTotal['totAmt']!=0?number_format($philhealthTotal['totAmt'],2):"")?></font></td>
                                        <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($pagibigTotal['totAmt']!=0?number_format($pagibigTotal['totAmt'],2):"")?></font></td>
                                        <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($empLoans['totAmt']!=0?number_format($empLoans['totAmt'],2):"")?></font></td>
                                        <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otherDeductions['totAmt']!=0?number_format($otherDeductions['totAmt'],2):"")?></font></td>
                                      <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=number_format($totalDeductions,2)?></font></td>
                                    </tr>
							<?
								}
							}
							else
							{
							?>
                                <tr>
                                    <td colspan="10" align="center">
                                        <FONT class="zeroMsg">NOTHING TO DISPLAY</font></td>
                                </tr>
							<? 
							}
							?>
							<tr>
								<td colspan="10" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton("deductions_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&groupType=".$groupType."&orderBy=".$orderBy."&catType=".$catType."&payPd=".$payPd."&repType=$_GET[repType]"."&empLoc=".$empLoc."&empBrnCode=".$empBrnCode."&locType=".$locType);?>								
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
		  <input type="hidden" name="groupType" id="groupType" value="<? echo $_GET['groupType']; ?>">
		  <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
		  <input type="hidden" name="catType" id="catType" value="<? echo $_GET['catType']; ?>">
		  <input type="hidden" name="payPd" id="payPd" value="<? echo $_GET['payPd']; ?>">
          <input type="hidden" name="locType" id="locType" value="<? echo $_GET["locType"]; ?>">
           <input type="hidden" name="empBrnCode" id="empBrnCode" value="<? echo $_GET['empBrnCode']; ?>">
		</form>
	</BODY>
</HTML>
