 <?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/dbI.inc.php");
include("../../../includes/commonI.php");
include("gl_booking_entries_df.obj.php");

	
$regBookingObj = new generateBooking();
$regBookingObj->validateSessions('','MODULES');
$arrPd = $regBookingObj->getSlctdPdwil($_GET['curPayPd']);




if(isset($_GET['act'])){
	switch ($_GET['act']){
		case 'GLBooking':
			$arrPayPd_Ref = $regBookingObj->getSlctdPdwil($_GET["curPayPd"]);
			$regBookingObj->hist=($arrPayPd_Ref['pdStat']=='O')?"":"hist";
			//Check if there's employee exist
			if (($regBookingObj->getEmpList(1))>0) 
			{
				if ($regBookingObj->mainGLBooking())
				{
					/*Payroll Tables Recon*/
					
					$pdNumber = $arrPayPd_Ref["pdNumber"];
					$pdYear = $arrPayPd_Ref["pdYear"];
					$pdStat = $arrPayPd_Ref["pdStat"];
			
					//$arrPayRecon = $regBookingObj->payReconObj($pdNumber,$pdYear);
					//if($arrPayRecon == 0)
						echo 0;
					//else
					//	echo 2;

				} 
				else 
				{
					echo 1;
				}
			}
			else
			{
				echo 4;
			}
			exit();
		break;
		case "CreatetxtFile":
			if ($regBookingObj->CreateTextOracleTextFile())
				{
					if(in_array($_SESSION['company_code'],array(1,2,4,5)) && $_SESSION['pay_category'] != 9) {
						if ($regBookingObj->CreateAccrualFile())
							echo 0;
						else
							echo 1;
					} else {
						echo 0;
					}
				} 
				else 
				{
					echo 1;
				}
			exit();
		break;
	}
	
}
$arrPayPeriod = $regBookingObj->getOpenPeriod($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category']); 
$payPd = $arrPayPeriod['pdSeries'];
$coutGL = $regBookingObj->CheckGL($payPd);
$checkdisButton = $_SESSION["company_code"];

/*if(($checkdisButton==1) || ($checkdisButton==2))
{
*/	
	if ($coutGL == 0) {
		//$dis_upload = " disabled";
	}
/*}
else
{
	$dis_upload = " disabled";
}*/
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
	</HEAD>
	<BODY>
		<FORM name='frmProcRegPay' id="frmProcRegPay" action="<?=$_SERVER['PHP_SELF']?>" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" >
				<tr>
					<td width="555" height="20" class="parentGridHdr">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Generate G/L Booking Entries</td>
			  </tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE width="98%" border="0" align="center" cellpadding="0" cellspacing="2" class="childGrid">
<tr>
								<td class="gridDtlLbl2" align="left" width="34%">
									<font class="gridDtlLblTxt">Company</font>								</td>
								<td width="5%" class="gridDtlLbl2" align="center">:</td>
<td width="61%" class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="compCode">
									<?
										$compName = $regBookingObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
									</font>								</td>
						  </tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="34%">
									<font class="gridDtlLblTxt">Pay Group</font>								</td>
								<td width="5%" class="gridDtlLbl2" align="center">:</td>
<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="payGrp"><?
											if($_SESSION['pay_group'] == '1'){
												echo "1 - One";
											}
											else if($_SESSION['pay_group'] == '2'){
												echo "2 - Two";
											}
											else{
												echo "Invalid group";
											}
									?>
									</font>								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="34%">
									<font class="gridDtlLblTxt">Pay Category</font>								</td>
								<td width="5%" class="gridDtlLbl2" align="center">:</td>
<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="payCat"><?
											$andPayCat = "AND payCat = '{$_SESSION['pay_category']}' ";
											$paycatDesc = $regBookingObj->getPayCat($_SESSION['company_code'],$andPayCat);
											echo $_SESSION['pay_category']. " - " .$paycatDesc['payCatDesc'];
									?>
									</font>								</td>
							</tr>	
							<tr>
								<td class="gridDtlLbl2" align="left" width="34%">
									<font class="gridDtlLblTxt">Payroll Period</font>								</td>
								<td width="5%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2"><div id="pdPay">
								  <input name="hide_payPd" type="hidden" id="hide_payPd" value="<? echo $payPd; ?>">
								  <? 					
								$arrPayPd = $regBookingObj->makeArr($regBookingObj->getAllPeriod(),'pdSeries','pdPayable','');
								$regBookingObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis);
							?>
							    </div></td>
						  </tr>
							
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									
									<INPUT class="inputs" type="button" name="btnProc" id="btnProc" value="GENERATE" onClick="procGL()"></td>
							</tr>
						</TABLE>
					  <div id="tmr" align="center"></div>
					</td>
				</tr>
			</TABLE>
			<INPUT type="hidden" id="hdnTsTag" value="<?=$arrPayPeriod['pdTsTag']?>">
			<INPUT type="hidden" id="hdnLoansTag" value="<?=$arrPayPeriod['pdLoansTag']?>">
			<INPUT type="hidden" id="hdnEarningsTag" value="<?=$arrPayPeriod['pdEarningsTag']?>">
		    <input type="hidden" name="curPayPd" id="curPayPd" value="<?=$payPd?>">
			<?
			$regBookingObj->disConnectI();
			?>
	</FORM>
	</BODY>
</HTML>
<SCRIPT>
<? if ($_GET['act']=='true') {?>
alert('AP Sucessfully Migrated')
<? } elseif ($_GET['act']=='false') {?>
alert('AP Migration error.')
<?
} ?>
	function procGL(){

		var curPayPd = $('payPd').value;
			
		var mainProcTS = confirm('Do you want to Generate GL Booking entries?');
		if(mainProcTS == false){
			return false;
		}

		params = "?act=GLBooking&curPayPd="+curPayPd;
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+params,{
			method : 'get',
			onComplete : function(req){
				eval(req.responseText);
				rsTxt = parseInt(req.responseText);		
				switch (rsTxt){
					case 0:
						alert('Successfully Generated');
					break;
					case 1:
						alert('Process failed');
					break;
					case 2:
						alert('Problem in Payroll Recon.');
					break;
					case 4:
						alert('No existing Employee based on the processed Company, Group and Category.');
					break;
				}
			},
			onCreate : function(){
				timedCount();
				$('btnProc').disabled=true;
			},
			onSuccess: function (){
				$('btnProc').value='GENERATE';
				$('btnProc').disabled=false;
				$('tmr').innerHTML="";
				stopCount();
			}
		});
	}

	var m=0;
	var s=0;
	var t;	

	function timedCount(){

		if(s == 60){
			m = m+1;
		}	
		if(s == 60){
			s =0;
		}

		$('tmr').innerHTML="<font size='2'>"+m+":"+s+ " <blink>Loading...</blink></font> " +'<br><img src="../../../images/progress2.gif">';
		s=s+1;
		t=setTimeout("timedCount()",1000);
	}
	
	function stopCount(){
		clearTimeout(t);
	}
	
function UploadAP() {
		var curPayPd = $('curPayPd').value;
		params = "?act=CreatetxtFile&curPayPd="+curPayPd;
		<? if(in_array($_SESSION["company_code"],array(3,7,8,9,10,11,12,13,15))) {?>
				location.href="http://<?=getenv("REMOTE_ADDR")?>/ap_upload.php?compCode=<?=$_SESSION['company_code']?>&payGrp=<?=$_SESSION['pay_group']?>&payCat=<?=$_SESSION['pay_category']?>&payPd=<?=$arrPayPeriod['pdPayable'];?>";
		<? } else {?>
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+params,{
			method : 'get',
			onComplete : function(req){
				eval(req.responseText);
				rsTxt = parseInt(req.responseText);		
				switch (rsTxt){
					case 0:
						<? if(($_SESSION["company_code"]=='3') || ($_SESSION["company_code"]=='15')  || ($_SESSION["company_code"]=='2')) {?>
						location.href="http://<?=getenv("REMOTE_ADDR")?>/ap_upload.php?compCode=<?=$_SESSION['company_code']?>&payGrp=<?=$_SESSION['pay_group']?>&payCat=<?=$_SESSION['pay_category']?>&payPd=<?=$arrPayPeriod['pdPayable'];?>";
						<? } else {?>
							alert('Successfully Created');
						<? }?>
					break;
					case 1:
						alert('Error Creating Text file');
					break;
					case 4:
						alert('No existing Employee based on the processed Company, Group and Category.');
					break;
				}
			},
			onCreate : function(){
				timedCount();
				$('btnCancel').disabled=true;
				$('btnUpload').disabled=true;
				$('btnProc').disabled=true;
				$('btnRfrsh').disabled=true;
			},
			onSuccess: function (){
				$('btnProc').value='GENERATE';
				$('btnUpload').disabled=false;
				$('btnCancel').disabled=false;
				$('btnProc').disabled=false;
				$('btnRfrsh').disabled=false;
				$('tmr').innerHTML="";
				stopCount();
			}
		});	
		<? } ?>	
	}
</SCRIPT>