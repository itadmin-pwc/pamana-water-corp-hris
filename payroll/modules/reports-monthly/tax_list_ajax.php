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
$empBrnCode = $_GET['empBrnCode'];
$empName = $_GET['empName'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$table = $_GET['table'];
$topType = $_GET['topType'];
$groupType = $_GET['groupType'];
if ($groupType==1) $groupName = "GROUP 1"; else $groupName = "GROUP 2"; 
$orderBy = $_GET['orderBy'];
$catType = $_GET['catType'];
$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $catType);
$payPd = $_GET['payPd'];
$arrPayPd = $inqTSObj->getSlctdPd($compCode,$payPd);

$reportPath = "tax_list_pdf.php?empNo=".$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&orderBy=".$orderBy."&payPd=".$payPd."&topType=".$topType."&tbl=".$tbl."&table=".$table."&empBrnCode=".$empBrnCode."&topType=".$topType;
?>

<HTML>
    <head>
        <script type='text/javascript' src='timesheet_js.js'></script>
    </head>
    
	<BODY>
        <div class="niftyCorner">
        	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
        		<tr>
        			<td colspan="4" class="parentGridHdr"> 
                        &nbsp;<img src="../../../images/grid.png">&nbsp;WITHOLDING TAX REPORT :: <?php echo "GROUP - ".$_SESSION["pay_group"];?> :: <?=strtoupper($catName['payCatDesc']);?> :: PAYROLL PERIOD (MONTHLY) = <?php echo strtoupper(date("F Y", strtotime($arrPayPd["pdPayable"]))); ?>
                        <div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
                            <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
                            <?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
        				</div>
                   </td>
        		</tr>
                <tr>
                	<td class="parentGridDtl">
                    	<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                        	<td colspan="20" class="gridToolbar" align="">
                        		<input name="back" type="button" id="back" value="Back" onClick="location.href='tax.php';">
                        	</td>
                     		<tr>
                            	<iframe src="<?php echo $reportPath; ?>" height="380px;" width="99%">
                                </iframe>
                            </tr>
                            <tr>
                                <td colspan="20" align="center" class="childGridFooter">
                                    <? //$pager->_viewPagerButton("tax_list_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo."&empName=".$empName."&empDiv=".$empDiv."&empDept=".$empDept."&empSect=".$empSect."&groupType=".$groupType."&orderBy=".$orderBy."&catType=".$catType."&payPd=".$payPd."&table=".$table);?>
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
		</form>
	</BODY>
</HTML>
