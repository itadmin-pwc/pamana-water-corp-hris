<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/dbI.inc.php");
include("../../../includes/commonI.php");
include("reg13thMonth_processing.obj.php");
include_once("reg13thMonth_closing.obj.php");


$reg13thMonthProcObj = new reg13thMonthProcObj($_GET,$_SESSION);
$closereg13thMonth = new closereg13thMonth($_GET,$_SESSION);
$reg13thMonthProcObj->validateSessions('','MODULES');
if(isset($_GET['action'])){
	switch ($_GET['action']){
		case 'procreg13thMonth':
				//$lastPayProcObj->mainProcLastPayroll();
				$chckPeriodTags = $reg13thMonthProcObj->checkPeriodTags();//check timesheet tag processing

				if(trim($chckPeriodTags['computeTag']) == 'Y'){

					if($reg13thMonthProcObj->reProcreg13thMonth() == true){

						if($reg13thMonthProcObj->mainProcreg13thMonth() == true){
								echo 1;//summarized successfully saved
						}
						else{
							echo 2;//summarization failed
						}					
					}
					else{
						echo 3;//failed reprocess
					}
				}
				else{

					if($reg13thMonthProcObj->mainProcreg13thMonth() == true){
						echo 1;//summarized successfully saved
					}
					else{
						echo 2;//summarization failed
					}
				}

			exit();
		break;
		case 'clsRegPay':
				
				if($closereg13thMonth->mainProcClose13thMonth() == true){
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
						13th Month Processing
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
										$compName = $reg13thMonthProcObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
									</font>								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Pay Group</font>								</td>
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
									</font>								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="20%">
									<font class="gridDtlLblTxt">Pay Category</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="payCat"><?
											$andPayCat = "AND payCat = '{$_SESSION['pay_category']}' ";
											$paycatDesc = $reg13thMonthProcObj->getPayCat($_SESSION['company_code'],$andPayCat);
											echo $_SESSION['pay_category']. " - " .$paycatDesc['payCatDesc'];
									?>
									</font>
								</td>
							</tr>	
							<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Payroll Period</font>								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="payPd"><?
											$pdYear = date('Y');
											//ORIGINAL CODE 6/1/2023
											// $andPayPeriod = "AND payGrp = '{$_SESSION['pay_group']}'
											// 				 AND payCat = '{$_SESSION['pay_category']}'
											// 				 AND pdYear='2020'
											// 				 AND pdNumber=25";
											$andPayPeriod = "AND payGrp = '{$_SESSION['pay_group']}'
															 AND payCat = '{$_SESSION['pay_category']}'
															 AND pdYear = $pdYear
															 AND pdNumber=25";
											$arrPayPeriod = $reg13thMonthProcObj->getPayPeriod($_SESSION['company_code'],$andPayPeriod);
											echo $arrPayPeriod['pdNumber']. " - " .$arrPayPeriod['pdYear'];
										?>
									</font>								</td>
							</tr>
							<tr>
								<td class="gridDtlLbl2" align="left" width="25%">
									<font class="gridDtlLblTxt">Date Coverd</font>								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
										<font class="gridDtlLblTxt" id="DteCvrd"><?
											echo $reg13thMonthProcObj->dateFormat($arrPayPeriod['pdFrmDate']). " - " .$reg13thMonthProcObj->dateFormat($arrPayPeriod['pdToDate']);
										?>
										</font>								</td>
							</tr>
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									<?
										if($reg13thMonthProcObj->AuditCheck() == false){
											$disabled = "disabled";
											$open = "$('btnClsProc').disabled=true;";
											$close = "$('btnClsProc').disabled=true;";
										} else {
											$close = "$('btnClsProc').disabled=false;";
											$open = "$('btnClsProc').disabled=true;";
										}
									?>
									<INPUT class="inputs" type="button" name="btnProc" id="btnProc" value="PROCESS" onClick="procRegPayroll('procreg13thMonth')" >
									<INPUT type="button" name="btnClsProc" id="btnClsProc" value="CLOSE"  class="inputs" onClick="procRegPayroll('clsRegPay')">
									<INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';">
									<INPUT type="button" name="btnRfrsh" id="btnRfrsh" value="REFRESH" onClick="location.href='<?=$_SERVER['PHP_SELF']?>'" class="inputs">								</td>
							</tr>
						</TABLE>
					  <div id="tmr" align="center"></div>
					</td>
				</tr>
			</TABLE>
			<?
				$pdPayable = explode("/",$reg13thMonthProcObj->dateFormat($arrPayPeriod['pdPayable']));
			?>
			<INPUT type="hidden" name="pdPayable" id="pdPayable" value="<?=$pdPayable[0]?>">
            <INPUT type="hidden" id="hdnEarningsTag" value="<?=$arrPayPeriod['pdEarningsTag']?>">
			<?
			$reg13thMonthProcObj->disConnectI();
			
			?>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>

	function procRegPayroll(act){

		var comCode = $('compCode').innerHTML.replace(' ','').split('-');
		var payGrp = $('payGrp').innerHTML.replace(' ','').split('-');
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
		
		if(act == 'procreg13thMonth'){
			if(trim($F('hdnEarningsTag')) == "Y"){
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
					break;
					case 2:
						alert('Summarization failed');
					break;
					case 3:
						alert('reprocess failed');
					break;
					case 4:
						alert('13th Month Successfully Closed');
						location.href='<?php echo $_SERVER['PHP_SELF'];?>';
					break;
					case 5:
						alert('13th Month Closing Failed');
					break;
				}
			},
			onCreate : function(){
				timedCount();
				$('btnCancel').disabled=true;
				$('btnProc').disabled=true;
				$('btnRfrsh').disabled=true;
				<?=$open?>		
			},
			onSuccess: function (){
				$('btnProc').value='PROCESS';
				$('btnCancel').disabled=false;
				$('btnProc').disabled=false;
				$('btnRfrsh').disabled=false;
				<?=$close?>		
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