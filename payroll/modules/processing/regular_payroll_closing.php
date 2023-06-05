<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include_once("regular_payroll_closing.obj.php");
$closeRegPay = new closeRegPayroll($_GET,$_SESSION);

//$regPayProcObj->validateSessions('','MODULES');

if(isset($_GET['action'])){
	switch ($_GET['action']){
		
		case 'clsRegPay':
				
				if($closeRegPay->mainProcCloseRegPay() == true){
					echo 4;//successfully closed
				}
				else{
					echo 5;//closing failed
				}
//				$closeRegPay->disConnect();
			exit();
		break;
		
	}
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
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Regular Payroll Closing
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid">
							<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Company</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="compCode"><?
										$compName = $closeRegPay->getCompanyName($_SESSION['company_code']);
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
											$paycatDesc = $closeRegPay->getPayCat($_SESSION['company_code'],$andPayCat);
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
											$arrPayPeriod = $closeRegPay->getPayPeriod($_SESSION['company_code'],$andPayPeriod);
											echo $arrPayPeriod['pdNumber']. " - " .$arrPayPeriod['pdYear'];
										?>
									</font>
								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="20%">
									<font class="gridDtlLblTxt">Date Coverd</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
										<font class="gridDtlLblTxt" id="DteCvrd"><?
											echo $closeRegPay->dateFormat($arrPayPeriod['pdFrmDate']). " - " .$closeRegPay->dateFormat($arrPayPeriod['pdToDate']);
										?>
										</font>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									<?
										if(trim($arrPayPeriod['pdProcessTag']) == 'Y'){
											$disabled = "disabled";
										}
									?>
                                    <INPUT type="button" name="btnClsProc" id="btnClsProc" value="CLOSE" disabled class="inputs" onClick="procRegPayroll('clsRegPay')" <?=$disabled?>>
									<INPUT type="button" name="btnRfrsh" id="btnRfrsh" value="REFRESH" onClick="location.href='<?=$_SERVER['PHP_SELF']?>'" class="inputs">
									
								</td>
							</tr>
						</TABLE>
						<div id="tmr" align="center"></div>
					</td>
				</tr>
			</TABLE>
			<?
				$pdPayable = explode("/",$closeRegPay->dateFormat($arrPayPeriod['pdPayable']));
			?>
			<INPUT type="hidden" name="pdPayable" id="pdPayable" value="<?=$pdPayable[0]?>">
			<INPUT type="hidden" id="hdnTsTag" value="<?=$arrPayPeriod['pdTsTag']?>">
			<INPUT type="hidden" id="hdnLoansTag" value="<?=$arrPayPeriod['pdLoansTag']?>">
			<INPUT type="hidden" id="hdnEarningsTag" value="<?=$arrPayPeriod['pdEarningsTag']?>">
			<?
			$closeRegPay->disConnect();
			
			?>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>

	function procRegPayroll(act){

		var comCode = $('compCode').innerHTML.replace(' ','').split('-');
		var payGrp = $('payGrp').innerHTML.replace(' ','').split('-');
		var payCat = $('payCat').innerHTML.replace(' ','').split('-');
		var payPd = $('payPd').innerHTML.replace(' ','').split('-');
		var dteCvrd = $('DteCvrd').innerHTML.replace(' ','').split('-');

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
		
		if(act == 'procRegPay'){
			if(trim($F('hdnTsTag')) == ""){
				alert('Time Sheet\nfor Payroll Group / Category\nNot Yet Extracted or Completed\nJob Aborted');
				return false;
			}
			else if(trim($F('hdnLoansTag')) == ""){
				alert('Loans\nfor Payroll Group / Category\nNot Yet Extracted or Completed\nJob Aborted');
				return false;
			}
			else if(trim($F('hdnEarningsTag')) == "N"){
				alert('Timesheet Or Loans Has been Reprocessed\nThe System will Process the Regular Payroll');
			}
			else if(trim($F('hdnEarningsTag')) == "Y"){
				var mainProcTS = confirm('Do You Want to Reprocess?');
				if(mainProcTS == false){
					return false;
				}			
			}
			else{
				var mainProcTS = confirm('Do You Want to Continue?');
				if(mainProcTS == false){
					return false;
				}
			}
	}
	else{
		if(trim($F('hdnTsTag')) == "" || trim($F('hdnTsTag')) == "N"){
			alert('Time Sheet\nfor Payroll Group / Category\nNot Yet Extracted or Completed\nJob Aborted');
			return false;			
		}
		if(trim($F('hdnLoansTag')) == "" || trim($F('hdnLoansTag')) == "N"){
			alert('Loans\nfor Payroll Group / Category\nNot Yet Extracted or Completed\nJob Aborted');
			return false;
		}
		else if(trim($F('hdnEarningsTag')) == "" || trim($F('hdnEarningsTag')) == "N"){
			alert('Regular Payroll\nfor Payroll Group / Category\nNot Yet Extracted or Completed\nJob Aborted');
			return false;			
		}
		var closePRoc = confirm("Do you want to close this period?");
		if(closePRoc == false){
			return false;	
		}
	}
	

		params = "?action="+act+"&pdNum="+payPd[0]+"&pdYear="+payPd[1].replace(' ','')+"&dtFrm="+dteCvrd[0]+"&dtTo="+dteCvrd[1].replace(' ','')+"&pdMonth="+$F('pdPayable');
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+params,{
			method : 'get',
			onComplete : function(req){
				eval(req.responseText);
				rsTxt = parseInt(req.responseText);		
				switch (rsTxt){
					case 1:
						alert('Successfully Summarized');
						location.href='<?php echo $_SERVER['PHP_SELF'];?>';
					break;
					case 2:
						alert('Summarization failed');
					break;
					case 3:
						alert('reprocess failed');
					break;
					case 4:
						alert('Regular Payroll Successfully Closed');
						location.href='<?php echo $_SERVER['PHP_SELF'];?>';
					break;
					case 5:
						alert('Regular Payroll Closing Failed');
					break;
					case 6:
						var conUnpostTran = confirm('There are Unposted Transaction.\n Do you want to print it?');
						if(conUnpostTran == true)
						{
							window.open('rpt_unposted_tran_ded_pdf.php'+'?&pdNum='+payPd[0]+'&pdYear='+payPd[1]);	
						}
						else
						{
							alert('Successfully Summarized');
							location.href='<?php echo $_SERVER['PHP_SELF'];?>';
						}
						
					break;
				}
			},
			onCreate : function(){
				timedCount();
				$('btnClsProc').disabled=true;
				$('btnRfrsh').disabled=true;
			},
			onSuccess: function (){
				$('btnClsProc').disabled=false;
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
</SCRIPT>