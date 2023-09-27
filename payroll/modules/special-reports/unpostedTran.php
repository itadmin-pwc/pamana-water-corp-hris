<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("timesheet_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');


if($_GET["payPd"]!="")
{
	$arrPayPd = $inqTSObj->getSlctdPd($_SESSION["company_code"],$_GET["payPd"]);
	$pdNum = $arrPayPd["pdNumber"];
	$pdYear = $arrPayPd["pdYear"];
	$pdStat = $arrPayPd["pdStat"];
	
}
else{
	$openPeriod = $inqTSObj->getOpenPeriod($compCode,$_SESSION['pay_group'],$_SESSION['pay_category']); 
	$payPd = $openPeriod['pdSeries'];
}

if($_GET['action']=='chkUnpostedTran'){
	$tbl = ($pdStat=='O'?"tblUnpostedTran":"tblUnpostedTranHist");
	$cntUnpostTran  = $inqTSObj->chkUnpostedTran($pdNum, $pdYear, $tbl);
	if($cntUnpostTran>0)
	{
		echo "location.href = 'unpostedTran.php?viewRpt=1&pdNum=".$pdNum."&pdYear=".$pdYear."&payPd=".$payPd."&tbl=".$tbl."'";
	}
	else{
		echo "alert('No record found.')";
	}
	exit();
}


?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='timesheet_js.js'></script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
	</HEAD>
	<BODY>
    	<BODY>
        <form name="frmUnpostedTran" id="frmUnpostedTran" method="post">
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
                    		&nbsp;PRINT UNPOSTED TRANSACTION
							<div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;">
				  				<INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
				  				<?=$inqTSObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
							</div>
                    </td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                        	
                           
                            <tr>
                                <td  align="">
                                	<table border="0" width="100%">
                                    	<tr>
                                        	 <td class="gridDtlVal" width="8%">
                                            	Payroll Period
                                            </td>	
                                            
                                        	<td class="gridDtlVal" width="10%" valign="middle">
                                            	<div id="pdPay"> 
                                                	<input name="hide_payPd" type="hidden" id="hide_payPd" value="<? echo $payPd; ?>">
                                                    <? 		
                                                        $arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($_SESSION["company_code"],$groupType,$catType),'pdSeries','pdPayable','');
                                                        $inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis);
                                                    ?>
                                                </div>
                                            </td>
                                           
                                            <td>
                                            	<input class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="generateUnpostTran();">
                                            </td>
                                        </tr>
                                    
                                  	</table>
                                </td>
                                
							</tr>
                            
                           
                            <tr>
                                <td  class="gridToolbar" align="">
                                	<?php $reportPath = ($_GET["viewRpt"]==1?"rpt_unposted_tran_ded_pdf.php?&pdNum=".$_GET["pdNum"]."&pdYear=".$_GET["pdYear"]."&tbl=".$_GET["tbl"]."":"");?>
                                   <iframe src="<?php echo $reportPath; ?>" height="380px;" width="99%">
                                   	
                                   </iframe>
                                </td>
							</tr>
                        </TABLE>
					</td>
				</tr>
               
			</TABLE>
		</div>
        </form>
    </BODY>
</HTML>
<script language="javascript">
	function generateUnpostTran()
	{
		var frmUnpostedTran= $('frmUnpostedTran').serialize(true);
		
		if(frmUnpostedTran["payPd"]==0){
			alert('Please select Payroll Period.');
			$('payPd').focus();
			return false;
		}
		
		new Ajax.Request('<?php $_SESSION['PHP_SELF'];?>?&action=chkUnpostedTran&payPd='+frmUnpostedTran["payPd"],{
			method : 'get',
			parameters : frmUnpostedTran,
			onComplete : function(req){
				eval(req.responseText);
			}
		});
		
		

	}
</script>