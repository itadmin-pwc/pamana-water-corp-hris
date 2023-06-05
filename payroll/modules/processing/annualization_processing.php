<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("annualization_processing.obj.php");

$annProcObj = new AnnualProcObj($_GET,$_SESSION);
$annProcObj->validateSessions('','MODULES');

if(isset($_GET['action'])){
	switch ($_GET['action']){
		case 'procRegPay':
				//$annProcObj->mainProcRegPayroll();
				
					if($annProcObj->mainProcRegPayroll() == true){
						echo 1;//summarized successfully saved
					}
					else{
						echo 2;//summarization failed
					}
				

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
		<FORM name='frmAnnProc' id="frmAnnProc" action="<?=$_SERVER['PHP_SELF']?>" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" >
				<tr>
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Annualization Processing
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid">
							<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Company</font>								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="compCode"><?
										$compName = $annProcObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
									</font>								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Annual Date</font>								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="payGrp"><?
									
									$annualDate = $annProcObj->getAnnualDate();
									
									echo date("F d, Y",strtotime($annualDate['annualDate']));?></font>								</td>
							</tr>
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									<?
										if(trim($arrPayPeriod['pdProcessTag']) == 'Y'){
											$disabled = "disabled";
										}
									?>
									<INPUT class="inputs" type="button" name="btnProc" id="btnProc" value="PROCESS" onClick="procRegPayroll('procRegPay')" <?=$disabled?>>
									<INPUT type="button" name="btnAnnPost" id="btnAnnPost" value="POST" class="inputs"  <?=$disabled?>>
									<INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';">
									<INPUT type="button" name="btnRfrsh" id="btnRfrsh" value="REFRESH" onClick="location.href='<?=$_SERVER['PHP_SELF']?>'" class="inputs">								</td>
							</tr>
						</TABLE>
					  <div id="tmr" align="center"></div>
					</td>
				</tr>
			</TABLE>
			<?
				$pdPayable = explode("/",$annProcObj->dateFormat($arrPayPeriod['pdPayable']));
			?>
			<INPUT type="hidden" name="pdPayable" id="pdPayable" value="<?=$pdPayable[0]?>">
			<INPUT type="hidden" id="hdnTsTag" value="<?=$arrPayPeriod['pdTsTag']?>">
			<INPUT type="hidden" id="hdnLoansTag" value="<?=$arrPayPeriod['pdLoansTag']?>">
			<INPUT type="hidden" id="hdnEarningsTag" value="<?=$arrPayPeriod['pdEarningsTag']?>">
			<?
			$annProcObj->disConnect();
			
			?>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>

	function procRegPayroll(act){

		var comCode = $('compCode').innerHTML.replace(' ','').split('-');
/*		var payGrp = $('payGrp').innerHTML.replace(' ','').split('-');
		var payCat = $('payCat').innerHTML.replace(' ','').split('-');
		var payPd = $('payPd').innerHTML.replace(' ','').split('-');
		var dteCvrd = $('DteCvrd').innerHTML.replace(' ','').split('-');
*/
		if(comCode[0] == ''){
			alert('Company is Required');
			return false;
		}
/*		if(payGrp[0] == ''){
			alert('Pay Group is Required');
			return false;
		}
		if(payCat[0] == ''){
			alert('Pay Category is Required');
			return false;
		}
*/		/*if(payPd[0] == ''){
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
		}*/
		
		if(act == 'procRegPay'){
			/*if(trim($F('hdnTsTag')) == ""){
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
			}*/
	}
		params = "";
		//params = "?action="+act+"&pdNum="+payPd[0]+"&pdYear="+payPd[1].replace(' ','')+"&dtFrm="+dteCvrd[0]+"&dtTo="+dteCvrd[1].replace(' ','')+"&pdMonth="+$F('pdPayable');
		
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
						window.open('annualization_processing_pdf.php?');	
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
				}
			},
			onCreate : function(){
				timedCount();
				$('btnCancel').disabled=true;
				$('btnProc').disabled=true;
				$('btnRfrsh').disabled=true;
				$('btnAnnPost').disabled=true;
			},
			onSuccess: function (){
				$('btnProc').value='PROCESS';
				$('btnCancel').disabled=false;
				$('btnProc').disabled=false;
				$('btnRfrsh').disabled=false;
				$('btnAnnPost').disabled=false;
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