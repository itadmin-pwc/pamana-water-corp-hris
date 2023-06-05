<?
session_start();
//if (in_array($_SESSION['company_code'],array(4,5))) {
	header("Location: gl_booking_entries_df.php");	
//}
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("gl_booking_entries.obj.php");
include("payroll_recon_obj.php");
	
$regBookingObj = new generateBooking();
$regBookingObj->validateSessions('','MODULES');
$arrPd = $regBookingObj->getSlctdPdwil($_GET['curPayPd']);

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');



if(isset($_GET['act'])){
	switch ($_GET['act']){
		case 'GLBooking':
			
			//Check if there's employee exist
			if (($regBookingObj->getEmpList(1))>0) 
			{
				if ($regBookingObj->mainGLBooking())
				{
					/*Payroll Tables Recon*/
					$arrPayPd_Ref = $inqTSObj->getSlctdPd($_SESSION["compCode"],$_GET["curPayPd"]);
					$pdNumber = $arrPayPd_Ref["pdNumber"];
					$pdYear = $arrPayPd_Ref["pdYear"];
					$pdStat = $arrPayPd_Ref["pdStat"];
			
					$arrPayRecon = $inqTSObj->payReconObj($pdNumber,$pdYear);
					if($arrPayRecon == 0)
						echo 0;
					else
						echo 2;

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

		
	}
}
$arrPayPeriod = $regBookingObj->getOpenPeriod($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category']); 
$payPd = $arrPayPeriod['pdSeries'];
$coutGL = $regBookingObj->CheckGL($payPd);
$checkdisButton = $_SESSION["company_code"];

if(($checkdisButton==1) || ($checkdisButton==2))
{
	
	if ($coutGL == 0) {
		$dis_upload = " disabled";
	}
}
else
{
	$dis_upload = " disabled";
}
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
					<td width="500" height="20" class="parentGridHdr">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Generate G/L Booking Entries</td>
			  </tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE width="98%" border="0" align="center" cellpadding="0" cellspacing="2" class="childGrid">
<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Company</font>								</td>
								<td width="2%" class="gridDtlLbl2" align="center">:</td>
<td width="73%" class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="compCode">
									<?
										$compName = $regBookingObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
									</font>								</td>
						  </tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Pay Group</font>								</td>
								<td width="2%" class="gridDtlLbl2" align="center">:</td>
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
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Pay Category</font>								</td>
								<td width="2%" class="gridDtlLbl2" align="center">:</td>
<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="payCat"><?
											$andPayCat = "AND payCat = '{$_SESSION['pay_category']}' ";
											$paycatDesc = $regBookingObj->getPayCat($_SESSION['company_code'],$andPayCat);
											echo $_SESSION['pay_category']. " - " .$paycatDesc['payCatDesc'];
									?>
									</font>								</td>
							</tr>	
							<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Payroll Period</font>								</td>
								<td width="2%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2"><? echo $arrPayPeriod['pdPayable'];
								/*$arrPayPd = $regBookingObj->makeArr($regBookingObj->getAllPeriod($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category']),'pdSeries','pdPayable','');
								$regBookingObj->DropDownMenu($arrPayPd,'payPd',$payPd,' class="inputs"');*/
							?></td>
						  </tr>
							
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									
									<INPUT class="inputs" type="button" name="btnProc" id="btnProc" value="GENERATE" onClick="procGL()">
							        <INPUT class="inputs" type="button" id="btnUpload" value="MIGRATE AP" <?=$dis_upload?> onClick="UploadAP()">
			                <INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';">
									<INPUT type="button" name="btnRfrsh" id="btnRfrsh" value="REFRESH" onClick="location.href='<?=$_SERVER['PHP_SELF']?>'" class="inputs">								</td>
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
			$regBookingObj->disConnect();
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

		var curPayPd = $('curPayPd').value;
			if(trim($F('hdnTsTag')) == ""){
				alert('Time Sheet\nfor Payroll Group / Category\nNot Yet Extracted or Completed\nJob Aborted');
				return false;
			}
			else if(trim($F('hdnLoansTag')) == ""){
				alert('Loans\nfor Payroll Group / Category\nNot Yet Extracted or Completed\nJob Aborted');
				return false;
			}
			else if(trim($F('hdnEarningsTag')) == ""){
				alert('Regular Payroll Not Yet Processed or Completed\nJob Aborted');
				return false;
			}
			else if(trim($F('hdnEarningsTag')) == "N"){
				alert('Timesheet Or Loans Has been Reprocessed\nPlease Reprocess the Regular Payroll');
				return false;
			}
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
		location.href="http://<?=getenv("REMOTE_ADDR")?>/ap_upload.php?compCode=<?=$_SESSION['company_code']?>&payGrp=<?=$_SESSION['pay_group']?>&payCat=<?=$_SESSION['pay_category']?>&payPd=<?=$arrPayPeriod['pdPayable'];?>";
	}
</SCRIPT>