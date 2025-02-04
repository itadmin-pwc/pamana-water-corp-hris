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
	
	$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');
	$empNo = $_GET['empNo'];
	$empName = $_GET['empName'];
	$empDiv = $_GET['empDiv'];
	$empDept = $_GET['empDept'];
	$empSect = $_GET['empSect'];
	$orderBy = $_GET['orderBy'];
	$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'],$_SESSION['pay_category']);
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
	
	if (strlen(strpos($tbl,'Hist'))==0) 
		$PaySum = "tblPayrollSummary";
	else 
		$PaySum = "tblPayrollSummaryhist";
	
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
	
	$resIntMaxRec = $inqTSObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	$qryEmpList = "SELECT *
					FROM tblEmpMast
					WHERE  compCode = '{$sessionVars['compCode']}'
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
					$orderBy1 Limit $intOffset,$intLimit";
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
                    &nbsp;OVERTIME AND NIGHT DIFFERENTIAL REPORT :: <?php echo "GROUP - ".$_SESSION["pay_group"];?> :: <?=strtoupper($catName['payCatDesc']);?> :: PAYROLL PERIOD=<?=$inqTSObj->valDateArt($arrPayPd['pdPayable'])?> :: FROM=<?=$inqTSObj->valDateArt($arrPayPd['pdFrmDate'])?> :: TO=<?=$inqTSObj->valDateArt($arrPayPd['pdToDate'])?>
                    <div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
                    <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
                    <?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
                    </div>
                </td>
            </tr>
            
            <tr>
            	<td class="parentGridDtl">
            		<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
            			<td colspan="27" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printOtNdList('<?=$_GET['repType']?>','<?=$tbl?>');" title="Print Overtime / Night Differential "> 
            				<img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Overtime / Night Differential">Overtime / Night Differential 
            				</a>&nbsp; <FONT class="ToolBarseparator">|</font> &nbsp; 
            				<input name="back" class="inputs" type="button" id="back" value="Back" onClick="location.href='ot_nd.php';"> &nbsp; 
            				<FONT class="ToolBarseparator">|</font> &nbsp; <font color="#FF0000">note: OT/ND</font> </td>
                            <tr>
                                <td width="1%" class="gridDtlLbl" align="left">#</td>
                                <td width="5%" class="gridDtlLbl" align="left">EMP.NO.</td>
                                <td width="10%" class="gridDtlLbl" align="left">EMPLOYEE NAME</td>
                                <td width="5%" class="gridDtlLbl" align="right">REG</td>
                                <td width="5%" class="gridDtlLbl" align="right">REST</td>
                                <td width="5%" class="gridDtlLbl" align="right">LEGAL</td>
                                <td width="5%" class="gridDtlLbl" align="right">SPCIAL</td>
                                <td width="5%" class="gridDtlLbl" align="right">LEGAL +REST</td>
                                <td width="5%" class="gridDtlLbl" align="right">SPCIAL +REST</td>
                                <td width="5%" class="gridDtlLbl" align="right">REST> 8HRS</td>
                                <td width="5%" class="gridDtlLbl" align="right">LEGAL >8HRS</td>
                                <td width="5%" class="gridDtlLbl" align="right">SPCIAL >8HRS</td>
                                <td width="5%" class="gridDtlLbl" align="right">LEGAL+ REST>8HRS</td>
                                <td width="5%" class="gridDtlLbl" align="right">SPCIAL+ REST>8HRS</td>
                                <td width="5%" class="gridDtlLbl" align="right">TOTAL</td>
                            </tr>
							<?
                            	if($inqTSObj->getRecCount($resEmpList) > 0)
								{
                                	$i=0;
                                
									foreach ($arrEmpList as $empListVal)
									{
									
										$otndTotal = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTRG,$tbl,0,''));
										$otndTotal2 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTRD,$tbl,0,''));
										$otndTotal3 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTLH,$tbl,0,''));
										$otndTotal4 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTSH,$tbl,0,''));
										$otndTotal5 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTLHRD,$tbl,0,''));
										$otndTotal6 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTSPRD,$tbl,0,''));
										$otndTotal7 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTRDGT8,$tbl,0,''));
										$otndTotal8 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTLHGT8,$tbl,0,''));
										$otndTotal9 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTSPGT8,$tbl,0,''));
										$otndTotal10 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTLHRDGT8,$tbl,0,''));
										$otndTotal11 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],OTSPRDGT8,$tbl,0,''));
										$otndTotal12 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDRG,$tbl,0,''));
										$otndTotal13 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDRD,$tbl,0,''));
										$otndTotal14 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDLH,$tbl,0,''));
										$otndTotal15 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDSP,$tbl,0,''));
										$otndTotal16 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDLHRD,$tbl,0,''));
										$otndTotal17 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDSPRD,$tbl,0,''));
										$otndTotal18 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDRDGT8,$tbl,0,''));
										$otndTotal19 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDLHGT8,$tbl,0,''));
										$otndTotal20 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDSHGT8,$tbl,0,''));
										$otndTotal21 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDLHRDGT8,$tbl,0,''));
										$otndTotal22 = mysql_fetch_array($inqTSObj->getBasicTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],NDSPRDGT8,$tbl,0,''));
										$otndTotal23 = $inqTSObj->getBasicTotal2($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],EARNINGS_OT,$tbl);
										$otndTotal24 = $inqTSObj->getBasicTotal2($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber'],EARNINGS_ND,$tbl);
										$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
										$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
										. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
                            ?>
                                        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
                                            <td class="gridDtlVal"><?=$i?></td>
                                            <td class="gridDtlVal"><?=$empListVal['empNo']?></td>
                                            <td class="gridDtlVal"><?=$empListVal['empLastName']. ", " . $empListVal['empFirstName'][0] . "." . $empListVal['empMidName'][0]."."?></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal['totAmt']!=0?number_format($otndTotal['totAmt'],2):"")?>/<?=($otndTotal12['totAmt']!=0?number_format($otndTotal12['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal2['totAmt']!=0?number_format($otndTotal2['totAmt'],2):"")?>/<?=($otndTotal13['totAmt']!=0?number_format($otndTotal13['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal3['totAmt']!=0?number_format($otndTotal3['totAmt'],2):"")?>/<?=($otndTotal14['totAmt']!=0?number_format($otndTotal14['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal4['totAmt']!=0?number_format($otndTotal4['totAmt'],2):"")?>/<?=($otndTotal15['totAmt']!=0?number_format($otndTotal15['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal5['totAmt']!=0?number_format($otndTotal5['totAmt'],2):"")?>/<?=($otndTotal16['totAmt']!=0?number_format($otndTotal16['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal6['totAmt']!=0?number_format($otndTotal6['totAmt'],2):"")?>/<?=($otndTotal17['totAmt']!=0?number_format($otndTotal17['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal7['totAmt']!=0?number_format($otndTotal7['totAmt'],2):"")?>/<?=($otndTotal18['totAmt']!=0?number_format($otndTotal18['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal8['totAmt']!=0?number_format($otndTotal8['totAmt'],2):"")?>/<?=($otndTotal19['totAmt']!=0?number_format($otndTotal19['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal9['totAmt']!=0?number_format($otndTotal9['totAmt'],2):"")?>/<?=($otndTotal20['totAmt']!=0?number_format($otndTotal20['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal10['totAmt']!=0?number_format($otndTotal10['totAmt'],2):"")?>/<?=($otndTotal21['totAmt']!=0?number_format($otndTotal21['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal11['totAmt']!=0?number_format($otndTotal11['totAmt'],2):"")?>/<?=($otndTotal22['totAmt']!=0?number_format($otndTotal22['totAmt'],2):"")?></font></td>
                                            <td class="gridDtlVal" align="right"><font class="gridDtlLblTxt"><?=($otndTotal23['totAmt']!=0?number_format($otndTotal23['totAmt'],2):"")?>/<?=($otndTotal24['totAmt']!=0?number_format($otndTotal24['totAmt'],2):"")?></font></td>
                                        </tr>
           			 		<?
                					}
            					}
            					else
								{
            				?>
                                    <tr>
                                        <td colspan="27" align="center">
                                            <FONT class="zeroMsg">NOTHING TO DISPLAY</font>
                                        </td>
                                    </tr>
            				<? 
								}
							?>
                            <tr>
                                <td colspan="27" align="center" class="childGridFooter">
                                    <? $pager->_viewPagerButton("ot_nd_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&groupType=".$groupType."&orderBy=".$orderBy."&catType=".$catType."&payPd=".$payPd."&repType=$_GET[repType]&tbl=$tbl&empLoc=".$empLoc."&empBrnCode=".$empBrnCode."&locType=".$locType);?>
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
