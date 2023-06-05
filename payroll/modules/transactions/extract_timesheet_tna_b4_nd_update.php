<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("extract_timesheet.obj.php");
include("extract_timesheet_tna.obj.php");
include("timesheet_obj.php");
			


$extrctTSObj = new extractTSObj($_GET,$_SESSION);
$extractTNATSObj = new extractTNATSObj($_GET,$_SESSION);
$extrctTSObj->validateSessions('','MODULES');



if($_GET['action'] == 'procTS'){
	
	if($extrctTSObj->checkPeriodTimeSheetTag($_GET["pdYear"], $_GET["pdNum"]) > 0){
		echo 1;//"alert('Time Sheet Successfully Extracted');";//success
	}
	else{
/*		$extrctTSObj->writeToWGroupCat();
		$extrctTSObj->getTimeRecord();
		$extrctTSObj->initializeTimeSheets();
		$extrctTSObj->mainProc();
		$extrctTSObj->updateTimeSheetTag();
		$extrctTSObj->deletewGroupCat();*/
		$extractTNATSObj->checkPeriod();
		if ($extractTNATSObj->mainExtractTS())
			echo 2;//"alert('Time Sheet Successfully Extracted');";//success
		else
			echo 3;		
		
	}
	exit();
}

if($_GET['action'] == 'reprocTS'){
/*	$extrctTSObj->deleTimsheetToReproc();
	$extrctTSObj->writeToWGroupCat();
	$extrctTSObj->getTimeRecord();
	$extrctTSObj->initializeTimeSheets();
	$extrctTSObj->mainProc();
	$extrctTSObj->updateTimeSheetTag();
	$extrctTSObj->deletewGroupCat();*/
	$extractTNATSObj->checkPeriod();
	if ($extractTNATSObj->mainExtractTS())
		echo 2;//"alert('Time Sheet Successfully Extracted');";//success
	else
		echo 3;	
}

if($_GET['action'] == 'uploadTs')
{	
	include("uploadParadox.php");
	//echo "31";
	exit();
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
		<FORM name='frmExtrctTS' id="frmExtrctTS" action="<?=$_SERVER['PHP_SELF']?>" method="post" >
			<TABLE width="440" border="0" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Extract / Compute Timesheet by Group and Category 
					(TNA)</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" >
							<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Company</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="compCode"><?
										$compName = $extrctTSObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
									</font>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="20%">
									<font class="gridDtlLblTxt">Pay Group</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
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
									</font>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="20%">
									<font class="gridDtlLblTxt">Pay Category</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="payCat"><?
											$andPayCat = "AND payCat = '{$_SESSION['pay_category']}' ";
											$paycatDesc = $extrctTSObj->getPayCat($_SESSION['company_code'],$andPayCat);
											echo $_SESSION['pay_category']. " - " .$paycatDesc['payCatDesc'];
									?>
									</font>
								</td>
							</tr>	
							<tr>
								<td class="gridDtlLbl2" align="left" width="20%">
									<font class="gridDtlLblTxt">Payroll Period</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="payPd"><?
											$andPayPeriod = "AND payGrp = '{$_SESSION['pay_group']}'
															 AND payCat = '{$_SESSION['pay_category']}'
															 AND pdStat IN ('O','') ";
											
											$arrPayPeriod = $extrctTSObj->getPayPeriod($_SESSION['company_code'],$andPayPeriod);
											echo $arrPayPeriod['pdNumber']. " - " .$arrPayPeriod['pdYear'];
											
											
										?>
									</font>
                                    	<?php
											$processTs = $extrctTSObj->checkPeriodTimeSheetTag($arrPayPeriod['pdYear'],$arrPayPeriod['pdNumber']);
											if($processTs>0)
												echo "<input type='hidden' name='processTs' id='processTs' value='Y'";
											else
												echo "<input type='hidden' name='processTs' id='processTs' value='N'";
										?>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="20%">
									<font class="gridDtlLblTxt">Date Coverd</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
										<font class="gridDtlLblTxt" id="DteCvrd"><?
											echo $extrctTSObj->dateFormat($arrPayPeriod['pdFrmDate']). " - " .$extrctTSObj->dateFormat($arrPayPeriod['pdToDate']);
										?>
										</font>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="3" class="childGridFooter"><INPUT class="inputs" type="button" name="btnProc" id="btnProc" value="PROCESS" onClick="procTimeSheet('P')">
									<INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';">
								</td>
							</tr>
                            
						</TABLE>
						<div id="tmr" align="center"></div>
					</td>
				</tr>
			</TABLE>
			<?$extrctTSObj->disConnect();?>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>


	function procTimeSheet($procType){

		var comCode = $('compCode').innerHTML.replace(' ','').split('-');
		var payGrp = $('payGrp').innerHTML.replace(' ','').split('-');
		var payCat = $('payCat').innerHTML.replace(' ','').split('-');
		var payPd = $('payPd').innerHTML.replace(' ','').split('-');
		var dteCvrd = $('DteCvrd').innerHTML.replace(' ','').split('-');
		var processTS = document.frmExtrctTS.processTs.value;
		
	

		if(comCode[0] == ''){
			alert('Company is Required');
			return false;
		}
		if(payGrp[0] == ''){
			alert('Pay Group is Required');
			return false;
		}
		if(payCat[0] == ''){
			alert('Pay Category is Required');
			return false;
		}
		if(payPd[0] == ''){
			alert('Period is Required');
			return false;
		}
		if(payPd[1].replace(' ','') == ''){
			alert('Period is Required');
			return false;
		}
		if(dteCvrd[0] == ''){
			alert('Date Covered (From) is Required');
			return false;
		}
		if(dteCvrd[1].replace(' ','') == ''){
			alert('Date Covered (To) is Required');
			return false;
		}
		
		
		if($procType == 'P')
		{
			if(processTS=="N")
			{
				var mainProcTS = confirm('Do You Want to Continue?');
				if(mainProcTS == false){
					return false;
				}
				params = "?action=procTS&pdNum="+payPd[0]+"&pdYear="+payPd[1].replace(' ','')+"&dtFrm="+dteCvrd[0]+"&dtTo="+dteCvrd[1].replace(' ','');
			}
			else
			{
				var mainProcTS = confirm('Time Sheet for Desired Payroll Group / Category / Period already Completed.\n Do you want to reprocess?');
				if(mainProcTS == false){
					return false;
				}
				params = "?action=reprocTS&pdNum="+payPd[0]+"&pdYear="+payPd[1].replace(' ','')+"&dtFrm="+dteCvrd[0]+"&dtTo="+dteCvrd[1].replace(' ','');
			}
		}
		else
		{
			var mainProcTS = confirm('Are you sure you want to Upload/Extract the Hypered Timesheet(s)?');
			if(mainProcTS == false){
				return false;
			}
			params = "?action=uploadTs&pdNum="+payPd[0]+"&pdYear="+payPd[1].replace(' ','')+"&dtFrm="+dteCvrd[0]+"&dtTo="+dteCvrd[1].replace(' ','');
		}
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+params,{
			method : 'get',
			onComplete : function(req){
				rsTxt = parseInt(req.responseText);		
				if(rsTxt == 1){
					alert('Time Sheet for Desired Payroll Group / Category / Period already Completed.');
				}
				
				if(rsTxt == 2){
					alert('Time Sheet Successfully Extracted');
				}
				
				if(rsTxt == 3){
					alert('Error in the Uploaded Time Sheet(s).');
				}
				
				if(rsTxt == 31){
					alert('Error in the Uploaded Time Sheet(s) and There are List of Transaction(s) which is not Within the cut off.');
					window.open("../../../../../TIMESHEETS/TS_NOT_WITHIN_CUTOFF/<?php echo session_id(); ?>-ERROR-<?php echo $_SESSION["pay_group"]."-";?>"+payPd[0]+"-"+payPd[1].replace(' ','')+".txt");
					
				}
				
				if(rsTxt == 4){
					alert('Time Sheet(s) Successfully Uploaded.');
					window.open("extract_timesheet.pdf.php?&dtFrm="+dteCvrd[0]+"&dtTo="+dteCvrd[1]+"&pdNum="+payPd[0]+"&pdYear="+payPd[1].replace(' ',''));
				}
				
				if(rsTxt == 41){
					alert('Time Sheet(s) Successfully Uploaded but There are List of Transaction(s) which is not Within the cut off');
					window.open("../../../../../TIMESHEETS/TS_NOT_WITHIN_CUTOFF/<?php echo session_id(); ?>-ERROR-<?php echo $_SESSION["pay_group"]."-";?>"+payPd[0]+"-"+payPd[1].replace(' ','')+".txt");
					window.open("extract_timesheet.pdf.php?&dtFrm="+dteCvrd[0]+"&dtTo="+dteCvrd[1]+"&pdNum="+payPd[0]+"&pdYear="+payPd[1].replace(' ',''));
				}
				
			},
			onCreate : function(){
				timedCount()
				$('btnCancel').disabled=true;
				$('btnProc').disabled=true;
			},
			onSuccess: function (){
				$('btnProc').value='PROCESS';
				$('btnCancel').disabled=false;
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
</SCRIPT>