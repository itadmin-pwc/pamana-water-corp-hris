<?
session_start();
$domain = $_SERVER['REMOTE_ADDR'];
header("Location: http://$domain/alphalist_processing.php?company_code={$_SESSION['company_code']}&employee_number={$_SESSION['employee_number']}&user_level={$_SESSION['user_level']}&user_payCat={$_SESSION['user_payCat']}&pay_group={$_SESSION['pay_group']}&pay_category={$_SESSION['pay_category']}");
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("alphalist_processing.obj.php");
include("../../../includes/adodb/adodb.inc.php");

$regPayProcObj = new regPayrollProcObj($_GET,$_SESSION);

$regPayProcObj->validateSessions('','MODULES');

if(isset($_GET['action'])){

	switch ($_GET['action']){
		
		
		case 'procRegPay':
				if($regPayProcObj->mainAlphareProcess() == true)
				{
					
					if($regPayProcObj->mainAlphaProcess() == true)
						echo 1;//summarized successfully saved
					else
						echo 2;//summarization failed
				}
				else
				{
						echo 2;//summarization failed
				}
					
				
			
					
			exit();
		break;
		
		case 'clsRegPay':
			if($regPayProcObj->mainAlphaProcessAlphadtl() == true)
				{
					echo 4;//summarized successfully saved
				}
				else
				{
					echo 5;//summarization failed
				}					
					
			exit();
		break;
		
	}
}

$arrlistofEmp = $regPayProcObj->getEmpList("Y");
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
						Alphalist Processing
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
										$compName = $regPayProcObj->getCompanyName($_SESSION['company_code']);
										echo $_SESSION['company_code'] . " - " . $compName;
									?>
									</font>
								</td>
							</tr>
							
							<tr>
								<td class="gridDtlLbl2" align="left" width="20%">
									<font class="gridDtlLblTxt">Year</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal2">
									<font class="gridDtlLblTxt" id="payPd"><?=date("Y")?>
									</font>
								</td>
							</tr>
                            
                            <tr >
                            	<td>&nbsp;
                                	
                                </td>
                            </tr>
                            
                            <tr style="height:30px">
								<td class="gridDtlLbl2" align="left" width="20%" valign="top" colspan="3">
									<font class="gridDtlLblTxt">Select Employee</font>
								</td>
                            </tr>
                            <tr>
								<td class="gridDtlVal2" colspan="3">
									<div style="height: 250px; width:99%; overflow: auto;">
                                    	<table border="1" width="100%" style="border-collapse:collapse">
                                        	<tr class="gridDtlVal"  style="height:30px;">
                                            	<td></td>
                                            	<td>Emp. No.</td>
                                                <td>Emp. Name</td>
                                            </tr>
                                            
                                            <?php
											 foreach($arrlistofEmp as $arrlistofEmp_val)
											 {
											 	echo "<tr class='gridDtlVal' >";
													echo "<td><input type='checkbox' name='empList[]' id='empList[]' value='".$arrlistofEmp_val["empNo"]."'></td>";
													echo "<td>".$arrlistofEmp_val["empNo"]."</td>";
													echo "<td>".$arrlistofEmp_val["empLastName"].", ".$arrlistofEmp_val["empFirstName"]."</td>";
												echo "</tr>";
											 }
											?>
                                            
                                        </table>
                                    </div>
								</td>
							</tr>
							
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									<?
										if(trim($arrPayPeriod['pdProcessTag']) == 'Y'){
											$disabled = "disabled";
										}
									?>
									<INPUT class="inputs" type="button" name="btnProc" id="btnProc" value="PROCESS ALPHALIST" onClick="procRegPayroll('procRegPay')" <?=$disabled?>>
									<INPUT type="button" name="btnClsProc" id="btnClsProc" value="MIGRATE BIR"   class="inputs" onClick="procRegPayroll('clsRegPay')" <?=$disabled?>>

                                    <INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';">
									<INPUT type="button" name="btnRfrsh" id="btnRfrsh" value="REFRESH" onClick="location.href='<?=$_SERVER['PHP_SELF']?>'" class="inputs">
									
								</td>
							</tr>
						</TABLE>
						<div id="tmr" align="center"></div>
					</td>
				</tr>
			</TABLE>
			<?
				$pdPayable = explode("/",$regPayProcObj->dateFormat($arrPayPeriod['pdPayable']));
			?>
			<INPUT type="hidden" name="pdPayable" id="pdPayable" value="<?=$pdPayable[0]?>">
			<INPUT type="hidden" id="hdnTsTag" value="<?=$arrPayPeriod['pdTsTag']?>">
			<INPUT type="hidden" id="hdnLoansTag" value="<?=$arrPayPeriod['pdLoansTag']?>">
			<INPUT type="hidden" id="hdnEarningsTag" value="<?=$arrPayPeriod['pdEarningsTag']?>">
			<?
			$regPayProcObj->disConnect();
			
			?>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>

	function procRegPayroll(act){
		
		
		var frmContent = $('frmProcRegPay').serialize(true);
		var empArray = new Array(frmContent['empList[]']);
		
	
		if((empArray == "") && (act=='procRegPay'))
			alert("Select Employee first.");
		else
		{
			if(act=='procRegPay')
				var mainProcTS = confirm('Do You Want to Process Alphalist?');
			else
				var mainProcTS = confirm('Do You Want to Migrate the process alphalist to BIR Program?');
			
			
				
			if(mainProcTS == false)
				return false;
						
							
			params = "?action="+act+"&empList="+frmContent['empList[]'];
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+params,{
				method : 'get',
				
				onComplete : function(req){
					eval(req.responseText);
					rsTxt = parseInt(req.responseText);		
					switch (rsTxt){
						case 1:
							alert('Successfully Annualized.');
							location.href='<?php echo $_SERVER['PHP_SELF'];?>';
						break;
						case 2:
							alert('Annualization Failed');
						break;
						
						case 4:
							alert('Alphalist Successfully Migrated.');
						break;
						
						case 5:
							alert('Migration Failed');
						break;
					}
				},
				onCreate : function(){
					timedCount();
					$('btnProc').disabled=true;
					$('btnClsProc').disabled=true;
				},
				onSuccess: function (){
					$('btnProc').disabled=false;
					$('btnClsProc').disabled=false;
					$('tmr').innerHTML="";
					stopCount();
				}
			});			
		}	
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