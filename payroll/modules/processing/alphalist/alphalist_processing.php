<?
session_start();
/*if(!isset($_GET['action'])) {
	$_SESSION['company_code']    = $_GET['company_code'];
	$_SESSION['employee_number'] = $_GET['employee_number'];
	$_SESSION['user_level']      = $_GET['user_level'];
	$_SESSION['user_payCat']	= $_GET['user_payCat'];
	$_SESSION['pay_group']    = $_GET['pay_group'];
	$_SESSION['pay_category'] = $_GET['pay_category'];
}*/

//include("PG-HRIS-SYSTEM/includes/userErrorHandler.php");
include("../../../../includes/dbI.inc.php");
include("../../../../includes/commonI.php");
include("alphalist_processing.obj_new.php");

//include("alphalist_processing.obj.php");
//include("../../../../includes/adodb/adodb.inc.php");
$regPayProcObj = new regPayrollProcObj($_GET,$_SESSION);


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
/*$arr = array('010001423','1604CF','201277095','0000','2011-12-31','D7.4','7', ' ','LUCIO','CO','L','','0001','00:00:00','00:00:00',' ', ' ',' ','Y','Z',0, '2237000',0,'0','0','1885000', '0','0','0','0','238333.32', '0','0','0','0', 0,'0','113666.68','0','2237000','0',000,0, '7.1E+6','7.1E+6','0','0', '1100000','0','6000000.00','6000000.00',0, '0',0,0,0,0, 0,0,0,0,0, 0,0,0,0,0, 0,'0', '7100000.00','1100000',0,0, '--','09/14/1954', '1414 UNION ST., PACO, MANILA ','--','--','--','--','--','00:00:00','00:00:00','00:00:00', '00:00:00','--','00:00:00','--');
$arr2 = array('empNo','form_type','employer_tin','employer_branch_code','retrn_period','schedule_num','sequence_num','registered_name','first_name','last_name','middle_name','tin','branch_code','employment_from','employment_to','atc_code','status_code','region_num','subs_filing','exmpn_code','factor_used',' actual_amt_wthld','income_payment','pres_taxable_salaries','pres_taxable_13th_month','pres_tax_wthld','pres_nontax_salaries','pres_nontax_13th_month','prev_taxable_salaries','prev_taxable_13th_month','prev_tax_wthld','prev_nontax_salaries','prev_nontax_13th_month','pres_nontax_sss_gsis_oth_cont','prev_nontax_sss_gsis_oth_cont',' tax_rate','over_wthld','amt_wthld_dec','exmpn_amt','tax_due','heath_premium','fringe_benefit','monetary_value',' net_taxable_comp_income','gross_comp_income','prev_nontax_de_minimis','prev_total_nontax_comp_income','prev_taxable_basic_salary','pres_nontax_de_minimis','pres_taxable_basic_salary','pres_total_comp','prev_pres_total_taxable',' pres_total_nontax_comp_income','prev_nontax_gross_comp_income','prev_nontax_basic_smw','prev_nontax_holiday_pay','prev_nontax_overtime_pay',' prev_nontax_night_diff','prev_nontax_hazard_pay','pres_nontax_gross_comp_income','pres_nontax_basic_smw_day','pres_nontax_basic_smw_month','pres_nontax_basic_smw_year','pres_nontax_holiday_pay','pres_nontax_overtime_pay','pres_nontax_night_diff','prev_pres_total_comp_income',' pres_nontax_hazard_pay','total_nontax_comp_income','total_taxable_comp_income','prev_total_taxable','nontax_basic_sal','tax_basic_sal','tpclsf','birth_date','address1','address2','child1',' child2','child3','child4','bday1','bday2','bday3','bday4','other_dep','other_dbday','other_rel');
for($i=0;$i<count($arr);$i++) {
	$w = $i+1;
	echo $w . " ". $arr[$i] . "=" .  $arr2[$i] . "<br>";
}*/

$arrlistofEmp = $regPayProcObj->getEmpList("Y");
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../../style/payroll.css');</STYLE>
	</HEAD>
	<BODY>
		<FORM name='frmProcRegPay' id="frmProcRegPay" action="<?=$_SERVER['PHP_SELF']?>" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" >
				<tr>
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../../images/grid.png">&nbsp;
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
/*											 foreach($arrlistofEmp as $arrlistofEmp_val)
											 {
											 	echo "<tr class='gridDtlVal' >";
													echo "<td><input type='checkbox' name='empList[]' id='empList[]' value='".$arrlistofEmp_val["empNo"]."'></td>";
													echo "<td>".$arrlistofEmp_val["empNo"]."</td>";
													echo "<td>".$arrlistofEmp_val["empLastName"].", ".$arrlistofEmp_val["empFirstName"]."</td>";
												echo "</tr>";
											 }*/
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
			$regPayProcObj->disConnectI();
			
			?>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>

	function procRegPayroll(act){
		
		
		var frmContent = $('frmProcRegPay').serialize(true);
		var empArray = new Array(frmContent['empList[]']);
		
	
/*		if((empArray == "") && (act=='procRegPay'))
			alert("Select Employee first.");
		else
		{*/
			if(act=='procRegPay')
				var mainProcTS = confirm('Do You Want to Process Alphalist?');
			else
				var mainProcTS = confirm('Do You Want to Migrate the process alphalist to BIR Program?');
			
			
				
			if(mainProcTS == false)
				return false;
						
							
			//params = "?action="+act+"&empList="+frmContent['empList[]']+"<?="&company_code=".$_GET['company_code']?>";
			params = "?action="+act+"<?="&company_code=".$_GET['company_code']?>";
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+params,{
				method : 'get',
				
				onComplete : function(req){
					eval(req.responseText);
					rsTxt = parseInt(req.responseText);		
					switch (rsTxt){
						case 1:
							alert('Successfully Annualized.');
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
		/*}	*/
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

		$('tmr').innerHTML="<font size='2'>"+m+":"+s+ " <blink>Loading...</blink></font> " +'<br><img src="../../../../images/progress2.gif">';
		s=s+1;
		t=setTimeout("timedCount()",1000);
	}
	
	function stopCount(){
		clearTimeout(t);
	}
</SCRIPT>