<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("extract_timesheet.obj.php");

$extrctTSObj = new extractTSObj($_GET,$_SESSION);
$extrctTSObj->validateSessions('','MODULES');



if($_GET['action'] == 'procTS')
{
		//echo 4;
		/*if($extrctTSObj->CountErrorTag()>0)
		{
			echo 5;
		}
		else
		{*/
			$extrctTSObj->checkPeriod();
			if ($extrctTSObj->mainExtractTS())
				echo 2;//"alert('Time Sheet Successfully Extracted');";//success
			else
				echo 3;
		//}
		
			
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
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Extract / Compute Timesheet by Group and Category 
					</td>
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
											$processTs = $extrctTSObj->checkPeriod();
											if($processTs['pdTsTag']=='Y')
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
								<td align="center" colspan="3" class="childGridFooter">
                                	<INPUT class="inputs" type="button" name="btnProc" id="btnProc" value="PROCESS" onClick="procTimeSheet()">
									<INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';">
                                    <INPUT class="inputs" type="button" name="btnCheck" id="btnCheck" value="CHECK" onClick="procTimeSheet()">
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


	function procTimeSheet(){

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
		
		
		/*if(processTS=="N")
		{*/
			
			var mainProcTS = confirm('Do You Want to Continue?');
			if(mainProcTS == false){
				return false;
			}
			params = "?action=procTS&pdNum="+payPd[0]+"&pdYear="+payPd[1].replace(' ','')+"&dtFrm="+dteCvrd[0]+"&dtTo="+dteCvrd[1].replace(' ','');
	/*	}
		else
		{
			alert('Time Sheet for Desired Payroll Group / Category / Period already Completed.');
			return false;
		}*/
		
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
					alert('Error Extracting Time Sheet');
				}
				if(rsTxt == 4){
					window.open('extract_tsprocess_error_pdf.php');
					
				}
				
				if(rsTxt == 5){
					alert('There are timesheets with Check Tag.');
					
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