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
	
	
	if (strlen(strpos($tbl,'Hist'))==0) {
		$PaySum = "tblPayrollSummary";
	} else {
		$PaySum = "tblPayrollSummaryhist";
	}
	$reportType = $_GET["reportType"];
	$locType = $_GET['locType'];
	$empBrnCode = $_GET['empBrnCode'];
	
	if($reportType==0)
		$reportType_Desc = "REGULAR AND ALLOWANCE";
	elseif($reportType==1)
		$reportType_Desc = "REGULAR";
	else
		$reportType_Desc = "ALLOWANCE";
	
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
					 AND empPayGrp = '{$_SESSION['pay_group']}'
					 AND empNo IN 
				   				(Select empNo from $PaySum where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )
					 $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 
					 $empBrnCode1 $locType1
					 $orderBy1 ";
	//echo $qryIntMaxRec;
	$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryEmpList = "SELECT *
			FROM tblEmpMast
			WHERE compCode = '{$sessionVars['compCode']}'
					AND empNo IN 
				   				(Select empNo from $PaySum where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )
					 AND empPayGrp = '{$_SESSION['pay_group']}'
					$empNo1 $empName1 $empDiv1 $empDept1 $empSect1  
					$empBrnCode1 $locType1
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
                	<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
                		&nbsp;EARNINGS REPORT <?php echo "(".$reportType_Desc.")"; ?> :: <?php echo "GROUP - ".$_SESSION["pay_group"];?> :: <?=strtoupper($catName['payCatDesc']);?> :: PAYROLL PERIOD=<?=$inqTSObj->valDateArt($arrPayPd['pdPayable'])?> :: FROM=<?=$inqTSObj->valDateArt($arrPayPd['pdFrmDate'])?> :: TO=<?=$inqTSObj->valDateArt($arrPayPd['pdToDate'])?>
                		<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
                            <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
                            <?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
                		</div>
                   </td>
                </tr>
                
                <tr>
                    <td class="parentGridDtl">
                    	<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                    		<td colspan="15" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printEarningsList('<?=$_GET['repType']?>','<?=$tbl?>');" title="Print Earnings Register"> 
                    			<img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Earnings Register">Earnings Register
                    			</a>&nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; 
                    			<input name="back" type="button" id="back" value="Back" onClick="location.href='earnings_register.php';">
                    		</td>
                
                			<tr>
								<?php
                                
									$td_cont = array
													("BASIC"=>array(EARNINGS_RECODEBASIC),
													  "OT"=>array(EARNINGS_RECODEOT),
													  "ND"=>array(EARNINGS_RECODEND),
													  "HOLIDAY"=>array(EARNINGS_RECODEHP),
													  "VL ENCASH."=>array(EARNINGS_RECODEVLENCASH),
													  "VL W/ PAY"=>array(EARNINGS_RECODEVLWPAY),
													  "SL W/ PAY"=>array(EARNINGS_RECODESLWPAY),
													  "ADJS."=>array(EARNINGS_RECODEADJ),
													  "ALLOWANCE ".($reportType==0?"":($reportType==1?"<br>(TAXABLE)":"<br>(NON - TAXABLE)")).""=>array(EARNINGS_RECODEALLOW),
													  "OTHERS"=>array(EARNINGS_RECODEOTHERS)
													 );
									
									echo "<td width='1%' class='gridDtlLbl' align='left'>#</td>";
									echo "<td width='6%' class='gridDtlLbl' align='left'>EMP. NO.</td>";
									echo "<td width='10%' class='gridDtlLbl' align='left'>EMPLOYEE <br> NAME</td>";
									
									$td_cont_size = 77/sizeof($td_cont);
									foreach($td_cont as $td_cont_val=>$trnCode_val)
									{
										echo "<td width='$td_cont_size%' class='gridDtlLbl' align='right'>".$td_cont_val."</td>";
									}
									echo "<td width='6%' class='gridDtlLbl' align='right'>TOTAL</td>";
                                ?>
                			</tr>
                
                			<?php
								$td_colspan = sizeof($td_cont)+4;
								if($inqTSObj->getRecCount($resEmpList) > 0)
								{
									$i=0;
									foreach ($arrEmpList as $empListVal)
									{
										$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
										$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
										. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';		
										
										echo "<tr bgcolor='".$bgcolor."' '".$on_mouse."'>";
										echo "<td class='gridDtlVal'>".$i."</td>";
										echo "<td class='gridDtlVal'>".$empListVal["empNo"]."</td>";
										echo "<td class='gridDtlVal'>".$empListVal["empLastName"].", ".$empListVal["empFirstName"][0].".".$empListVal["empMidName"][0]."."."</td>";
										
										foreach($td_cont as $td_cont_val=>$trnCode_val)
										{
											$trnAmt = $inqTSObj->getBasicTotal2($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],$trnCode_val[0],$tbl);
											echo "<td class='gridDtlVal' align='right'><font class='gridDtlLblTxt'>".($trnAmt["totAmt"]!=0?number_format($trnAmt["totAmt"],2):"")."</font></td>";
											$sum_amt+=$trnAmt["totAmt"];
										}
										echo "<td class='gridDtlVal' align='right'><font class='gridDtlLblTxt'>".($sum_amt!=0?number_format($sum_amt,2):"")."</font></td>";
										unset($trnAmt,$sum_amt);
										echo "</tr>";    
									}
								}
								else
								{
									echo '<tr>
										<td colspan="'.$td_colspan.'" align="center">
											<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
										</td>
									</tr>';
								}
                			?>
                
                			<tr>
                				<td colspan="<?php echo $td_colspan; ?>" align="center" class="childGridFooter">
                					<? $pager->_viewPagerButton("earnings_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&orderBy=".$orderBy."&payPd=".$payPd."&repType=$_GET[repType]&tbl=$tbl&reportType=".$reportType."&empLoc=".$empLoc."&empBrnCode=".$empBrnCode."&locType=".$locType);?>
                				</td>
                			</tr>
                		</TABLE>
                	</td>
                </tr>
            </TABLE>
        </div>
		<? $inqTSObj->disConnect();?>
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
          <input type="hidden" name="reportType" id="reportType" value="<?php echo $_GET["reportType"]; ?>">
          <input type="hidden" name="locType" id="locType" value="<? echo $_GET["locType"]; ?>">
           <input type="hidden" name="empBrnCode" id="empBrnCode" value="<? echo $_GET['empBrnCode']; ?>">
		</form>
	</BODY>
</HTML>
