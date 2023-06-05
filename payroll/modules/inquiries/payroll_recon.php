<?
	##################################################
	session_start(); 
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("payroll_recon_obj.php");
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	
	if($_POST["payPd"]!="")
	{
		$arrPayPd_Ref = $inqTSObj->getSlctdPd($_POST["compCode"],$_POST["payPd"]);
		$pdNumber = $arrPayPd_Ref["pdNumber"];
		$pdYear = $arrPayPd_Ref["pdYear"];
		$pdStat = $arrPayPd_Ref["pdStat"];
		
		$arrchkPayRecon = $inqTSObj->payReconHistObj($pdNumber,$pdYear);
		
		if(($pdStat=='O')&&(sizeof($arrchkPayRecon)==1))
		{
			$arrPayRecon = $inqTSObj->payReconObj($pdNumber,$pdYear);
		}
		else
		{
			$disCheckButton = 1;
			$txtRemarks = $arrchkPayRecon["remarks"];
			$grand_hCount = $arrchkPayRecon["tot_headCount"];
			$grand_tblEarnGross = $arrchkPayRecon["tot_tblEarn_gross"];
			$grand_tblPaySumGross = $arrchkPayRecon["tot_tblSummary_grossEarnings"];
			$grand_tblYtdGross = $arrchkPayRecon["tot_tblYtdData_ytdGross"];
			
			$grand_tblEarnTaxEarn = $arrchkPayRecon["tot_tblEarn_taxableEarn"];
			$grand_tblEarnMinWage = $arrchkPayRecon["tot_tblEarn_taxEarn_minWage"];
			
			$grand_tblPaySumTaxEarn = $arrchkPayRecon["tot_tblSummary_taxableEarn"];
			$grand_tblYtdTaxEarn = $arrchkPayRecon["tot_tblYtdData_ytdTaxable"];
			$grand_tblDedDed = $arrchkPayRecon["tot_tblDed_totDed"];
			$grand_tblPaySumDed =  $arrchkPayRecon["tot_tblSummary_totDed"];
			$grand_tblDedGov = $arrchkPayRecon["tot_governmentals"];
			$grand_tblMtdGovt =  $arrchkPayRecon["tot_tblMtdGovt_govern"];
			$grand_tblNoGovt = $arrchkPayRecon["tot_tblMtdGovt_govern_minWage"];
			$grand_tblYtdGovDed =  $arrchkPayRecon["tot_tblYtdData_ytdGovDed"];
			$grand_tblDedTax =  $arrchkPayRecon["tot_withTax"];
			$grand_tblPaySumTax =  $arrchkPayRecon["tot_tblSummary_taxWitheld"];
			$grand_tblYtdTax =  $arrchkPayRecon["tot_tblYtdData_ytdTax"];
			$grand_tblEaningsSprtAllow =  $arrchkPayRecon["tot_tblEarn_sprtAllow"];
			$grand_tblDeductionSprtAllow = $arrchkPayRecon["tot_tblDed_sprtAllow"];
			$grand_tblPaySumSprtAllow = $arrchkPayRecon["tot_tblSummary_sprtAllow"];
			$grand_tblYtdSprtAllow =  $arrchkPayRecon["tot_tblYtdData_sprtAllow"];
			$grand_tblEaningsSprtAdvances = $arrchkPayRecon["tot_tblEarn_sprtAdvances"];
			$grand_tblPaySumSprtAdvances =  $arrchkPayRecon["tot_tblSummary_sprtAdvances"];
			$grand_tblYtdDataSprtAdvances =  $arrchkPayRecon["tot_tblYtdData_sprtAdvances"];
			$grand_tblEarnEmpBasic =  $arrchkPayRecon["tot_tblEarn_empBasic"];
			$grand_tblPaySumEmpBasic =  $arrchkPayRecon["tot_tblSummary_empBasic"];
			$grand_tblYtdEmpBasic =  $arrchkPayRecon["tot_tblYtdData_empBasic"];
			$grand_tblPaySumNetSal = $arrchkPayRecon["tot_tblSummary_netSalary"];
		
		}
		
		
		
		
			
	}
	else
	{
		$disSavedButton = 1;
	}
	
	if(($_POST["btnCheck"]!="") || ($_POST["btnSave"]!=""))
	{
		$grand_hCount = $grand_tblPaySumGross = 0;
		$grand_hCount = $arrPayRecon["HeadCount1"] + $arrPayRecon["HeadCount2"] + $arrPayRecon["HeadCount3"] + $arrPayRecon["HeadCount9"]; 
		$grand_tblEarnGross = $arrPayRecon["tblEarnGross1"] + $arrPayRecon["tblEarnGross2"] + $arrPayRecon["tblEarnGross3"] + $arrPayRecon["tblEarnGross9"]; 
		$grand_tblPaySumGross = $arrPayRecon["tblPaySumGross1"] + $arrPayRecon["tblPaySumGross2"] + $arrPayRecon["tblPaySumGross3"] + $arrPayRecon["tblPaySumGross9"]; 
		$grand_tblYtdGross = $arrPayRecon["tblYtdGross1"] + $arrPayRecon["tblYtdGross2"] + $arrPayRecon["tblYtdGross3"] + $arrPayRecon["tblYtdGross9"]; 
		$grand_tblEarnTaxEarn = $arrPayRecon["tblEarnTaxEarn1"] + $arrPayRecon["tblEarnTaxEarn2"] + $arrPayRecon["tblEarnTaxEarn3"] + $arrPayRecon["tblEarnTaxEarn9"]; 
		$grand_tblEarnMinWage = $arrPayRecon["tblEarnMinWage1"] + $arrPayRecon["tblEarnMinWage2"] + $arrPayRecon["tblEarnMinWage3"] + $arrPayRecon["tblEarnMinWage9"]; 
		$grand_tblPaySumTaxEarn = $arrPayRecon["tblPaySumTaxEarn1"] + $arrPayRecon["tblPaySumTaxEarn2"] + $arrPayRecon["tblPaySumTaxEarn3"] + $arrPayRecon["tblPaySumTaxEarn9"]; 
		$grand_tblYtdTaxEarn = $arrPayRecon["tblYtdTaxEarn1"] + $arrPayRecon["tblYtdTaxEarn2"] + $arrPayRecon["tblYtdTaxEarn3"] + $arrPayRecon["tblYtdTaxEarn9"]; 
		$grand_tblDedDed = $arrPayRecon["tblDedDed1"] + $arrPayRecon["tblDedDed2"] + $arrPayRecon["tblDedDed3"] + $arrPayRecon["tblDedDed9"]; 
		$grand_tblPaySumDed = $arrPayRecon["tblPaySumDed1"] + $arrPayRecon["tblPaySumDed2"] + $arrPayRecon["tblPaySumDed3"] + $arrPayRecon["tblPaySumDed9"]; 
		$grand_tblDedGov = $arrPayRecon["tblDedGov1"] + $arrPayRecon["tblDedGov2"] + $arrPayRecon["tblDedGov3"] + $arrPayRecon["tblDedGov9"]; 
		$grand_tblMtdGovt = $arrPayRecon["tblMtdGovt1"] + $arrPayRecon["tblMtdGovt2"] + $arrPayRecon["tblMtdGovt3"] + $arrPayRecon["tblMtdGovt9"]; 
		$grand_tblNoGovt = $arrPayRecon["tblNoGov1"] + $arrPayRecon["tblNoGov2"] + $arrPayRecon["tblNoGov3"] + $arrPayRecon["tblNoGov9"]; 
		$grand_tblYtdGovDed = $arrPayRecon["tblYtdGovDed1"] + $arrPayRecon["tblYtdGovDed2"] + $arrPayRecon["tblYtdGovDed3"] + $arrPayRecon["tblYtdGovDed9"]; 
		$grand_tblDedTax = $arrPayRecon["tblDedTax1"] + $arrPayRecon["tblDedTax2"] + $arrPayRecon["tblDedTax3"] + $arrPayRecon["tblDedTax9"]; 
		$grand_tblPaySumTax = $arrPayRecon["tblPaySumTax1"] + $arrPayRecon["tblPaySumTax2"] + $arrPayRecon["tblPaySumTax3"] + $arrPayRecon["tblPaySumTax9"]; 
		$grand_tblYtdTax = $arrPayRecon["tblYtdTax1"] + $arrPayRecon["tblYtdTax2"] + $arrPayRecon["tblYtdTax3"] + $arrPayRecon["tblYtdTax9"]; 
		$grand_tblEaningsSprtAllow = $arrPayRecon["tblEarnSprtAllow1"] + $arrPayRecon["tblEarnSprtAllow2"] + $arrPayRecon["tblEarnSprtAllow3"] + $arrPayRecon["tblEarnSprtAllow9"]; 
		$grand_tblDeductionSprtAllow = $arrPayRecon["tblDedSprtAllow1"] + $arrPayRecon["tblDedSprtAllow2"] + $arrPayRecon["tblDedSprtAllow3"] + $arrPayRecon["tblDedSprtAllow9"]; 
		$grand_tblPaySumSprtAllow = $arrPayRecon["tblPaySumSprtAllow1"] + $arrPayRecon["tblPaySumSprtAllow2"] + $arrPayRecon["tblPaySumSprtAllow3"] + $arrPayRecon["tblPaySumSprtAllow9"]; 
		$grand_tblYtdSprtAllow = $arrPayRecon["tblYtdSprtAllow1"] + $arrPayRecon["tblYtdSprtAllow2"] + $arrPayRecon["tblYtdSprtAllow3"] + $arrPayRecon["tblYtdSprtAllow9"]; 
		$grand_tblEaningsSprtAdvances = $arrPayRecon["tblEarnSprtAdvances1"] + $arrPayRecon["tblEarnSprtAdvances2"] + $arrPayRecon["tblEarnSprtAdvances3"] + $arrPayRecon["tblEarnSprtAdvances9"]; 
		$grand_tblPaySumSprtAdvances = $arrPayRecon["tblPaySumSprtAdvances1"] + $arrPayRecon["tblPaySumSprtAdvances2"] + $arrPayRecon["tblPaySumSprtAdvances3"] + $arrPayRecon["tblPaySumSprtAdvances9"]; 
		$grand_tblYtdDataSprtAdvances = $arrPayRecon["tblYtdSprtAdvances1"] + $arrPayRecon["tblYtdSprtAdvances2"] + $arrPayRecon["tblYtdSprtAdvances3"] + $arrPayRecon["tblYtdSprtAdvances9"]; 
		$grand_tblEarnEmpBasic = $arrPayRecon["tblEarnEmpBasic1"] + $arrPayRecon["tblEarnEmpBasic2"] + $arrPayRecon["tblEarnEmpBasic3"] + $arrPayRecon["tblEarnEmpBasic9"]; 
		$grand_tblPaySumEmpBasic = $arrPayRecon["tblPaySumEmpBasic1"] + $arrPayRecon["tblPaySumEmpBasic2"] + $arrPayRecon["tblPaySumEmpBasic3"] + $arrPayRecon["tblPaySumEmpBasic9"]; 
		$grand_tblYtdEmpBasic = $arrPayRecon["tblYtdEmpBasic1"] + $arrPayRecon["tblYtdEmpBasic2"] + $arrPayRecon["tblYtdEmpBasic3"] + $arrPayRecon["tblYtdEmpBasic9"]; 
		$grand_tblPaySumNetSal = $arrPayRecon["tblPaySumNetSal1"] + $arrPayRecon["tblPaySumNetSal2"] + $arrPayRecon["tblPaySumNetSal3"] + $arrPayRecon["tblPaySumNetSal9"]; 
	
	
		
		$grand_arrPayRegHC = $arrPayRegHC1 + $arrPayRegHC2 + $arrPayRegHC3 + $arrPayRegHC9; 
		$font_grand_arrPayRegHC = ($grand_hCount!=$grand_arrPayRegHC?"red":"");
		
		$grand_arrPayRegNonTaxEarn = $arrPayRegNonTaxEarn1 + $arrPayRegNonTaxEarn2 + $arrPayRegNonTaxEarn3 + $arrPayRegNonTaxEarn9 + $arrPayRegTaxEarn1 + $arrPayRegTaxEarn2 + $arrPayRegTaxEarn3 + $arrPayRegTaxEarn9;
		
		if(($grand_tblEarnGross!=$grand_tblPaySumGross) ||($grand_tblEarnGross!=$grand_tblYtdGross) ||($grand_tblEarnGross!=$grand_arrPayRegNonTaxEarn) ||($grand_tblPaySumGross!=$grand_tblYtdGross) ||($grand_tblPaySumGross!=$grand_arrPayRegNonTaxEarn) ||($grand_tblYtdGross!=$grand_arrPayRegNonTaxEarn))
			 $font_grand_tblEarnGross = "red";
		
		
		$grand_minWageTax = $grand_tblEarnTaxEarn-$grand_tblEarnMinWage;
		$grand_arrPayRegTaxEarn = $arrPayRegTaxEarn21 + $arrPayRegTaxEarn22 + $arrPayRegTaxEarn23 + $arrPayRegTaxEarn29;
		
	
		if(($grand_tblPaySumTaxEarn!=$grand_tblYtdTaxEarn)||($grand_tblPaySumTaxEarn!=$grand_arrPayRegTaxEarn)||($grand_tblYtdTaxEarn!=$grand_arrPayRegTaxEarn))
			$font_grand_tblPaySumTaxEarn = "red";
		
		$arrPayReg_totLoans = $arrPayRegLoans1 + $arrPayRegLoans2 + $arrPayRegLoans3 + $arrPayRegLoans9;
		$arrPayReg_totGov = $arrPayRegGov1 + $arrPayRegGov2 + $arrPayRegGov3 + $arrPayRegGov9;
		$arrPayReg_totOthDed = $arrPayRegOthDed1 + $arrPayRegOthDed2 + $arrPayRegOthDed3 + $arrPayRegOthDed9;
		$arrPayReg_totSprtOthDed = $arrPayRegSprtOthDed1 + $arrPayRegSprtOthDed2 + $arrPayRegSprtOthDed3 + $arrPayRegSprtOthDed9;
		$arrPayReg_totEncCsBond = $arrPayRegEncCsBond1 + $arrPayRegEncCsBond2 + $arrPayRegEncCsBond3 + $arrPayRegEncCsBond9;
		$arrPayReg_totEncTax = $arrPayRegTax1 + $arrPayRegTax2 + $arrPayRegTax3 + $arrPayRegTax9;
		
		$grand_Deductions = $arrPayRegGov1 + $arrPayRegLoans1 + $arrPayRegOthDed1 + $arrPayRegSprtOthDed1 + $arrPayRegEncCsBond1 + 
						   $arrPayRegGov2 + $arrPayRegLoans2 + $arrPayRegOthDed2 + $arrPayRegSprtOthDed2 + $arrPayRegEncCsBond2 +
						   $arrPayRegGov3 + $arrPayRegLoans3 + $arrPayRegOthDed3 + $arrPayRegSprtOthDed3 + $arrPayRegEncCsBond3 +
						   $arrPayRegGov9 + $arrPayRegLoans9 + $arrPayRegOthDed9 + $arrPayRegSprtOthDed9 + $arrPayRegEncCsBond9 ;
		
		if(($grand_tblDedDed!=$grand_tblPaySumDed) || ($grand_tblDedDed!=$grand_Deductions) ||	($grand_tblPaySumDed!=$grand_Deductions))
			$font_grand_grandDeductions = "red";
	
	
		$grand_minWageMtdGovt = $grand_tblDedGov - $grand_tblNoGovt;
		$grand_MtdGovt = $arrPayRegSss1 + $arrPayRegPag1 + $arrPayRegPhic1 + $arrPayRegSss2 + $arrPayRegPag2 + $arrPayRegPhic2 + $arrPayRegSss3 + $arrPayRegPag3 + $arrPayRegPhic3;
		
		if(($grand_tblDedGov!=$grand_tblMtdGovt) || ($grand_tblDedGov!=($grand_minWageMtdGovt+$grand_tblNoGovt)) || ($grand_tblDedGov!=$grand_MtdGovt) || ($grand_tblMtdGovt!=($grand_minWageMtdGovt+$grand_tblNoGovt)) || ($grand_tblMtdGovt!=$grand_MtdGovt) || (($grand_minWageMtdGovt+$grand_tblNoGovt)!=$grand_MtdGovt))
			$font_grand_grandMtdGovt = "red";
		
			
		$grand_tax = $arrPayRegTax1 + $arrPayRegTax2 + $arrPayRegTax3;
		if(($grand_tblDedTax!=$grand_tblPaySumTax) || ($grand_tblDedTax!=$grand_tblYtdTax) || ($grand_tblDedTax!=$grand_tax) || ($grand_tblPaySumTax!=$grand_tblYtdTax) || ($grand_tblPaySumTax!=$grand_tax) || ($grand_tblYtdTax!=$grand_tax))
			$font_grand_tax = "red";
		
		$grand_Prevtax  = $arrPayRegPrevTax1 + $arrPayRegPrevTax2 + $arrPayRegPrevTax3 + $arrPayRegPrevTax9;
		
		$grand_sprtAllow =   ($arrPayRegSprtAllow1 +  $arrPayRegSprtAllow2 + $arrPayRegSprtAllow3 + $arrPayRegSprtAllow9)  -   ($arrPayRegSprtAllowDed1  + $arrPayRegSprtAllowDed2 +  + $arrPayRegSprtAllowDed3 + $arrPayRegSprtAllow9);
		if(($grand_tblPaySumSprtAllow!=$grand_tblYtdSprtAllow) || ($grand_tblPaySumSprtAllow!=$grand_sprtAllow) || ($grand_tblYtdSprtAllow!=$grand_sprtAllow))
			$font_grand_sprtAllow = "red";
		
		if(($grand_tblEarnEmpBasic!=$grand_tblPaySumEmpBasic) ||  ($grand_tblEarnEmpBasic!=$grand_tblYtdEmpBasic) || ($grand_tblPaySumEmpBasic!=$grand_tblYtdEmpBasic))
			$font_grand_empbasic = "red";
		
		$grandNetSal = $arrPayRegNetSal1 + $arrPayRegNetSal2 + $arrPayRegNetSal3 + $arrPayRegNetSal9;
		
		if($grand_tblPaySumNetSal!=$grandNetSal)
			$font_grand_NetSal = "red";
		
		$grand_net_sprt[1] = 	$arrPayRecon["tblPaySumNetSal1"] + $arrPayRecon["tblYtdSprtAllow1"];
		$grand_net_sprt[2] = 	$arrPayRecon["tblPaySumNetSal2"] + $arrPayRecon["tblYtdSprtAllow2"];
		$grand_net_sprt[3] =	$arrPayRecon["tblPaySumNetSal3"] + $arrPayRecon["tblYtdSprtAllow3"];
		$grand_net_sprt[9] =	$arrPayRecon["tblPaySumNetSal9"] + $arrPayRecon["tblYtdSprtAllow9"];
		
		$grand_net_sprt_total = $grand_net_sprt[1] + $grand_net_sprt[2] + $grand_net_sprt[3] + $grand_net_sprt[9];
		
		
		$grand_AUB =  $arrPayRegAUB1 + $arrPayRegAUB2 + $arrPayRegAUB3 + $arrPayRegAUB9;
		$grand_MBTC = $arrPayRegMBTC1 + $arrPayRegMBTC2 + $arrPayRegMBTC3 + $arrPayRegMBTC9;
		$grand_CASH = $arrPayRegCASH1 + $arrPayRegCASH2 + $arrPayRegCASH3 + $arrPayRegCASH9;
		$grand_BOC = $arrPayRegBOC1 + $arrPayRegBOC2 + $arrPayRegBOC3 + $arrPayRegBOC9;
		$grand_BANK  = $grand_AUB + $grand_MBTC + $grand_CASH + $grand_BOC;
		
		if($grand_net_sprt_total!=$grand_BANK)
			$font_BANK = "red";
		
		if(($grand_tblEaningsSprtAdvances!=$grand_tblPaySumSprtAdvances) || ($grand_tblEaningsSprtAdvances!=$grand_tblYtdDataSprtAdvances) || ($grand_tblPaySumSprtAdvances!=$grand_tblYtdDataSprtAdvances))
			$font_SprtAdvances = "red";
		
		
		$arrPayRegtot1 =  $arrPayRegAUB1 + $arrPayRegMBTC1 +  $arrPayRegCASH1  + $arrPayRegBOC1;
		$arrPayRegtot2 =  $arrPayRegAUB2 + $arrPayRegMBTC2 +  $arrPayRegCASH2  + $arrPayRegBOC2;
		$arrPayRegtot3 =  $arrPayRegAUB3 + $arrPayRegMBTC3 +  $arrPayRegCASH3  + $arrPayRegBOC3;
		$arrPayRegtot9 =  $arrPayRegAUB9 + $arrPayRegMBTC9 +  $arrPayRegCASH9  + $arrPayRegBOC9;
		$grand_PayReg_tot = $arrPayRegtot1 + $arrPayRegtot2 + $arrPayRegtot3 + $arrPayRegtot9;
		
		$arrPayRegtot1 = round($arrPayRegtot1,2);
		$arrPayRegtot2 = round($arrPayRegtot2,2);
		$arrPayRegtot3 = round($arrPayRegtot3,2);
		$arrPayRegtot9 = round($arrPayRegtot9,2);
		$grand_PayReg_tot = round($grand_PayReg_tot,2);
		
		if(($font_grand_arrPayRegHC!="") || ($font_grand_tblEarnGross!="")||($font_grand_tblPaySumTaxEarn!="")||($font_grand_grandDeductions!="")||($font_grand_grandMtdGovt!="")||($font_grand_tax!="")||($font_grand_sprtAllow!="")||($font_grand_empbasic!="")||($font_grand_NetSal!=""))
			$disSavedButton = 1;
		elseif($_POST["payPd"]=="")
			$disSavedButton = 1;
		elseif($_POST["payPd"]==0)
			$disSavedButton = 1;
		else
			$disSavedButton = 0;
			
		
		if($grand_PayReg_tot!=$grand_net_sprt_total)
			$font_PayReg_tot = "red";
			
		if($_POST["btnSave"]!="")
		{
			if($disSavedButton==0)
			{
$arr_payreg_fields = $txtRemarks."=".$grand_hCount."=".$grand_tblEarnGross."=".$grand_tblPaySumGross."=".$grand_tblYtdGross."=".$grand_tblEarnTaxEarn."=".$grand_tblEarnMinWage."=".$grand_tblPaySumTaxEarn."=".$grand_tblYtdTaxEarn."=".$arrPayReg_totLoans."=".$arrPayReg_totOthDed."=".$arrPayReg_totSprtOthDed."=".$arrPayReg_totGov."=".$arrPayReg_totEncTax."=".$arrPayReg_totEncCsBond."=".$grand_tblDedDed."=".$grand_tblPaySumDed."=".($grand_tblDedDed+$grand_tblDedTax)."=".$grand_tblPaySumTax."=".$grand_tblYtdTax."=".$grand_tblMtdGovt."=".$grand_tblNoGovt."=".$grand_tblYtdGovDed."=".$grand_tblEaningsSprtAllow."=".$grand_tblDeductionSprtAllow."=".$grand_tblPaySumSprtAllow."=".$grand_tblYtdSprtAllow."=".$grand_tblEaningsSprtAdvances."=".$grand_tblPaySumSprtAdvances."=".$grand_tblYtdDataSprtAdvances."=".$grand_tblEarnEmpBasic."=".$grand_tblPaySumEmpBasic."=".$grand_tblYtdEmpBasic."=".$grand_tblPaySumNetSal."=".($grand_tblPaySumNetSal+$grand_tblYtdSprtAllow)."=".$grand_Prevtax."=".($grand_tblDedDed+$grand_tblDedTax);
				$arr_savePayRecon =  $inqTSObj->savePayRecon($pdNumber,$pdYear, $arr_payreg_fields);
					if($arr_savePayRecon==0)
					{
						echo "<script language='javascript'>alert('Payroll Recon. successfully saved.');</script>";
						$disSavedButton = 1;
					}
					else
						echo "<script language='javascript'>alert('Error Occur.');</script>";
			}
		}		
	}
	else
	{
		$disSavedButton = 1;
	}
	##################################################
?>
<HTML>
	<HEAD>
        <TITLE>
       	 	<?=SYS_TITLE?>
        </TITLE>
		<style>@import url('../../style/main_emp_loans.css');</style>
        <script type='text/javascript' src='../../../includes/jSLib.js'></script>
        <script type='text/javascript' src='../../../includes/prototype.js'></script>
        <!--calendar lib-->
        <script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
        <STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
        <!--end calendar lib-->
        <script type='text/javascript' src='timesheet_js.js'></script>
        
      
    </HEAD>
	<BODY>

        <form name="frmPayRecon" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
     
		
        <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
            <tr>
            	<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Payroll Registers Recon.</td>
            </tr>
           <tr>
            	<td class="parentGridDtl" >
            		<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                    	<tr style="height:30px;">
                        	<td width="20%">Payroll Period for Group <?=$_SESSION["pay_group"]?></td>
                            <td>
                            	<? 					
									$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$_SESSION["pay_group"],$_SESSION["pay_category"]),'pdSeries','pdPayable','');
									$inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,' onChange = submit();');
								?>
                            </td>
                            
                            <td>Payroll Cut Off</td>
                            <td><input type="text" name="" value="<?=date("m/d/Y", strtotime($arrPayPd_Ref["pdFrmDate"]))." - ".date("m/d/Y", strtotime($arrPayPd_Ref["pdToDate"]))?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                        </tr>
                        <tr style="height:30px;">
                        	<td>Payroll Period Remarks</td>
                            <td colspan="2"><input type="text" name="txtRemarks" value="<?=$txtRemarks?>" class="fntTblHdr"  style="width:100%;"></td>
                            <td><input name="btnCheck" type="submit" id="btnCheck" value="Check"<?=($disCheckButton==1?"disabled":"")?> class="fntTblHdr"><input name="btnSave" type="submit" id="btnSave" value="Save" <?=($disSavedButton==1?"disabled":"")?> onClick="saveRecon(<?=$disSavedButton?>);" class="fntTblHdr"></td>
                        </tr>
                      
                        <tr class="gridToolbar" >
                        	<td colspan="4">
                            	<tABLE border="0" width="70%" cellpadding="1" cellspacing="1" align="left">
                                        <tr style="height:30px;" align="center">
                                            <td width="30%"></td>
                                            <td width="10%" class="gridDtlLbl">EXECUTIVE</td>
                                            <td width="10%" class="gridDtlLbl">CONFIDENTIAL</td>
                                            <td width="10%" class="gridDtlLbl">NON CONFIDENTIAL</td>
                                            <td width="10%" class="gridDtlLbl">RESIGNED</td>
                                            <td width="15%" class="gridDtlLbl">GRAND TOTALS</td>
                                           
                                        </tr>
                                        
                                         <tr style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">EMPLOYEE HEAD COUNT</td>
                                        </tr>
                                        
                                        <tr class="gridToolbar" >
                                            <td  >PaySum. Head Count</td>
                                            <td ><input type="text" name="" value="<?=$arrPayRecon["HeadCount1"]?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=$arrPayRecon["HeadCount2"]?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td ><input type="text" name="" value="<?=$arrPayRecon["HeadCount3"]?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td ><input type="text" name="" value="<?=$arrPayRecon["HeadCount9"]?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_arrPayRegHC?>"><?=$grand_hCount?></font></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar" >
                                            <td class="fntTblHdr" >Pay-Reg. Head Count</td>
                                            <td><input type="text" name="arrPayRegHC1" value="<?=$arrPayRegHC1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegHC2" value="<?=$arrPayRegHC2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegHC3" value="<?=$arrPayRegHC3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegHC9" value="<?=$arrPayRegHC9?>" class="fntTblHdr"  style="width:100%;"></td>
                                             <td align="right"><font color="<?=$font_grand_arrPayRegHC?>"><?=$grand_arrPayRegHC?></font></td>
                                        </tr>
                                        
                                         <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">GROSS EARNINGS</td>
                                        </tr>
                                 
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">tblEarnings Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnGross1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnGross2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnGross3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnGross9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_tblEarnGross?>"><?=number_format($grand_tblEarnGross,2)?></td>
                                        </tr>
                                        
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">tblPaySum Table</td>
                                            
                                            <td class="fntTblHdr"><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumGross1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td class="fntTblHdr"><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumGross2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td class="fntTblHdr"><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumGross3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td class="fntTblHdr"><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumGross9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_tblEarnGross?>"><?=number_format($grand_tblPaySumGross,2)?></td>
                                        </tr>
                                        
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">tblYtdData Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdGross1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdGross2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdGross3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdGross9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_tblEarnGross?>"><?=number_format($grand_tblYtdGross,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg. Taxable</td>
                                            <td><input type="text" name="arrPayRegTaxEarn1" value="<?=$arrPayRegTaxEarn1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegTaxEarn2" value="<?=$arrPayRegTaxEarn2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegTaxEarn3" value="<?=$arrPayRegTaxEarn3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegTaxEarn9" value="<?=$arrPayRegTaxEarn9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td></td>
                                        </tr>
                                        
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg. Non-Taxable</td>
                                            <td><input type="text" name="arrPayRegNonTaxEarn1" value="<?=$arrPayRegNonTaxEarn1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegNonTaxEarn2" value="<?=$arrPayRegNonTaxEarn2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegNonTaxEarn3" value="<?=$arrPayRegNonTaxEarn3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegNonTaxEarn9" value="<?=$arrPayRegNonTaxEarn9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><font color="<?=$font_grand_tblEarnGross?>"><?=number_format($grand_arrPayRegNonTaxEarn,2)?></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">TAXABLE EARNINGS</td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">tblEarnings</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnTaxEarn1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnTaxEarn2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnTaxEarn3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnTaxEarn9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_tblPaySumTaxEarn?>"><?=number_format($grand_tblEarnTaxEarn,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Min.Wage No Tax</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnMinWage1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnMinWage2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnMinWage3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnMinWage9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"<font color="<?=$font_grand_tblPaySumTaxEarn?>"><?=number_format($grand_tblEarnMinWage,2)?></td>
                                        </tr>
                                 
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Payroll Summary</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumTaxEarn1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumTaxEarn2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumTaxEarn3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumTaxEarn9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_tblPaySumTaxEarn?>"><?=number_format($grand_tblPaySumTaxEarn,2)?></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">YTD</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdTaxEarn1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdTaxEarn2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdTaxEarn3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdTaxEarn9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_tblPaySumTaxEarn?>"><?=number_format($grand_tblYtdTaxEarn,2)?></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg Taxable Amt</td>
                                            <td><input type="text" name="arrPayRegTaxEarn21" value="<?=$arrPayRegTaxEarn21?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegTaxEarn22" value="<?=$arrPayRegTaxEarn22?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegTaxEarn23" value="<?=$arrPayRegTaxEarn23?>" class="fntTblHdr"  style="width:100%;"></td>
                                             <td><input type="text" name="arrPayRegTaxEarn29" value="<?=$arrPayRegTaxEarn29?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><font color="<?=$font_grand_tblPaySumTaxEarn?>"><?=number_format($grand_arrPayRegTaxEarn ,2)?></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">DEDUCTION W/O TAX</td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Deduction Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedDed1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedDed2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedDed3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedDed9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_grandDeductions?>"><?=number_format($grand_tblDedDed,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">PaySum Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumDed1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumDed2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumDed3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumDed9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_grandDeductions?>"><?=number_format($grand_tblPaySumDed,2)?></td>
                                        </tr>
                                         
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg Governmentals</td>
                                            <td><input type="text" name="arrPayRegGov1" value="<?=$arrPayRegGov1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegGov2" value="<?=$arrPayRegGov2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegGov3" value="<?=$arrPayRegGov3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegGov9" value="<?=$arrPayRegGov9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td></td>
                                        </tr>
                                      
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg Loans</td>
                                            <td><input type="text" name="arrPayRegLoans1" value="<?=$arrPayRegLoans1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegLoans2" value="<?=$arrPayRegLoans2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegLoans3" value="<?=$arrPayRegLoans3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegLoans9" value="<?=$arrPayRegLoans9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg Other Ded.</td>
                                            <td><input type="text" name="arrPayRegOthDed1" value="<?=$arrPayRegOthDed1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegOthDed2" value="<?=$arrPayRegOthDed2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegOthDed3" value="<?=$arrPayRegOthDed3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegOthDed9" value="<?=$arrPayRegOthDed9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg Sprt. Other Ded</td>
                                            <td><input type="text" name="arrPayRegSprtOthDed1" value="<?=$arrPayRegSprtOthDed1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSprtOthDed2" value="<?=$arrPayRegSprtOthDed2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSprtOthDed3" value="<?=$arrPayRegSprtOthDed3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSprtOthDed9" value="<?=$arrPayRegSprtOthDed9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Encoded Cash Bond</td>
                                            <td><input type="text" name="arrPayRegEncCsBond1" value="<?=$arrPayRegEncCsBond1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegEncCsBond2" value="<?=$arrPayRegEncCsBond2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegEncCsBond3" value="<?=$arrPayRegEncCsBond3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegEncCsBond9" value="<?=$arrPayRegEncCsBond9?>" class="fntTblHdr"  style="width:100%;"></td>
                                           <td align="right"><font color="<?=$font_grand_grandDeductions?>"><?=number_format($grand_Deductions,2)?></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">GOVERNMENTALS</td>
                                        </tr>
                                        
                                         
            
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Deduction Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedGov1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedGov2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedGov3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedGov9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_grandMtdGovt?>"><?=number_format($grand_tblDedGov,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">MtdGovt Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblMtdGovt1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblMtdGovt2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblMtdGovt3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblMtdGovt9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_grandMtdGovt?>"><?=number_format($grand_tblMtdGovt,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">No Governmetals</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblNoGov1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblNoGov2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblNoGov3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblNoGov9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_grandMtdGovt?>"><?=number_format($grand_tblNoGovt,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">YtdData Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdGovDed1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdGovDed2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdGovDed3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdGovDed9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_grandMtdGovt?>"><?=number_format($grand_tblYtdGovDed,2)?></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg SSS</td>
                                            <td><input type="text" name="arrPayRegSss1" value="<?=$arrPayRegSss1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSss2" value="<?=$arrPayRegSss2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSss3" value="<?=$arrPayRegSss3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSss9" value="<?=$arrPayRegSss9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg HDMF</td>
                                            <td><input type="text" name="arrPayRegPag1" value="<?=$arrPayRegPag1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegPag2" value="<?=$arrPayRegPag2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegPag3" value="<?=$arrPayRegPag3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegPag9" value="<?=$arrPayRegPag9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg PHIC.</td>
                                            <td><input type="text" name="arrPayRegPhic1" value="<?=$arrPayRegPhic1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegPhic2" value="<?=$arrPayRegPhic2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegPhic3" value="<?=$arrPayRegPhic3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegPhic9" value="<?=$arrPayRegPhic9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><font color="<?=$font_grand_grandMtdGovt?>"><?=number_format($grand_MtdGovt,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">WITHOLDING TAX</td>
                                        </tr>
                                        
		    
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Deduction Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedTax1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedTax2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedTax3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedTax9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_tax?>"><?=number_format($grand_tblDedTax,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">PaySum Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumTax1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumTax2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumTax3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumTax9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_tax?>"><?=number_format($grand_tblPaySumTax,2)?></td>
                                        </tr>
                                      
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">YtdData Table</td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdTax1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdTax2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdTax3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdTax9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_tax?>"><?=number_format($grand_tblYtdTax,2)?></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg Tax</td>
                                            <td><input type="text" name="arrPayRegTax1" value="<?=$arrPayRegTax1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegTax2" value="<?=$arrPayRegTax2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegTax3" value="<?=$arrPayRegTax3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegTax9" value="<?=$arrPayRegTax9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><font color="<?=$font_grand_tax?>"><?=number_format($grand_tax,2)?></td>
                                        </tr>
                                     
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg Previous Year Tax</td>
                                            <td><input type="text" name="arrPayRegPrevTax1" value="<?=$arrPayRegPrevTax1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegPrevTax2" value="<?=$arrPayRegPrevTax2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegPrevTax3" value="<?=$arrPayRegPrevTax3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegPrevTax9" value="<?=$arrPayRegPrevTax9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><font color="<?=$font_grand_tax?>"><?=number_format($grand_Prevtax,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">SPRT. ALLOW</td>
                                        </tr>
                                     	
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">Earnings Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnSprtAllow1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnSprtAllow2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnSprtAllow3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnSprtAllow9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_sprtAllow?>"><?=number_format($grand_tblEaningsSprtAllow,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Deduction Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedSprtAllow1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedSprtAllow2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedSprtAllow3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblDedSprtAllow9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_sprtAllow?>"><?=number_format($grand_tblDeductionSprtAllow,2)?></td>
                                        </tr>
                                        
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">PaySum Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumSprtAllow1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumSprtAllow2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumSprtAllow3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumSprtAllow9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_sprtAllow?>"><?=number_format($grand_tblPaySumSprtAllow,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">YtdData Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAllow1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAllow2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAllow3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAllow9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_sprtAllow?>"><?=number_format($grand_tblYtdSprtAllow,2)?></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg Sprt. Allow</td>
                                            <td><input type="text" name="arrPayRegSprtAllow1" value="<?=$arrPayRegSprtAllow1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSprtAllow2" value="<?=$arrPayRegSprtAllow2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSprtAllow3" value="<?=$arrPayRegSprtAllow3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSprtAllow9" value="<?=$arrPayRegSprtAllow9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td></td>
                                        </tr>
                                      
                                      <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg (Ded) Sprt. Allow</td>
                                            <td><input type="text" name="arrPayRegSprtAllowDed1" value="<?=$arrPayRegSprtAllowDed1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSprtAllowDed2" value="<?=$arrPayRegSprtAllowDed2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSprtAllowDed3" value="<?=$arrPayRegSprtAllowDed3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegSprtAllowDed9" value="<?=$arrPayRegSprtAllowDed9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><font color="<?=$font_grand_sprtAllow?>"><?=number_format($grand_sprtAllow,2)?></td>
                                        </tr>
                              			
                                        <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">SPRT. ADVANCES</td>
                                        </tr>
                                     	
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">Earnings Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnSprtAdvances1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnSprtAdvances2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnSprtAdvances3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnSprtAdvances9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_SprtAdvances?>"><?=number_format($grand_tblEaningsSprtAdvances,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">PaySum Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumSprtAdvances1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumSprtAdvances2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumSprtAdvances3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumSprtAdvances9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_SprtAdvances?>"><?=number_format($grand_tblPaySumSprtAdvances,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">YTD Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAdvances1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAdvances2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAdvances3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAdvances9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_SprtAdvances?>"><?=number_format($grand_tblYtdDataSprtAdvances,2)?></td>
                                        </tr>
                                        
                  
                                        <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">EMP. BASIC</td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">tblEarnings Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnEmpBasic1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnEmpBasic2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnEmpBasic3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblEarnEmpBasic9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_empbasic?>"><?=number_format($grand_tblEarnEmpBasic,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">PaySum Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumEmpBasic1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumEmpBasic2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumEmpBasic3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumEmpBasic9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_empbasic?>"><?=number_format($grand_tblPaySumEmpBasic,2)?></td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">YTDData Table</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdEmpBasic1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdEmpBasic2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdEmpBasic3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdEmpBasic9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_empbasic?>"><?=number_format($grand_tblYtdEmpBasic,2)?></td>
                                        </tr>
                                        
                                         <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">NET SALARY</td>
                                        </tr>
                                       
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay Sum Table</td>
                                           <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumNetSal1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumNetSal2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumNetSal3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblPaySumNetSal9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><font color="<?=$font_grand_NetSal?>"><?=number_format($grand_tblPaySumNetSal,2)?></td>
                                        </tr>
                                        
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">Sprt Allow</td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAllow1"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAllow2"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAllow3"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                             <td><input type="text" name="" value="<?=number_format($arrPayRecon["tblYtdSprtAllow9"],2)?>" class="fntTblHdr"  style="width:100%;" readonly></td>
                                            <td align="right"><?=number_format($grand_tblYtdSprtAllow,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Pay-Reg Net Sal.</td>
                                            <td><input type="text" name="arrPayRegNetSal1" value="<?=$arrPayRegNetSal1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegNetSal2" value="<?=$arrPayRegNetSal2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegNetSal3" value="<?=$arrPayRegNetSal3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegNetSal9" value="<?=$arrPayRegNetSal9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><font color="<?=$font_grand_NetSal?>"><?=number_format($grandNetSal,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">Net Sal. + Sprt. Allow</td>
                                            <td><input type="text" name="" value="<?=$grand_net_sprt[1]?>" class="fntTblHdr" readonly  style="width:100%;"></td>
                                            <td><input type="text" name="" value="<?=$grand_net_sprt[2]?>" class="fntTblHdr"  readonly style="width:100%;"></td>
                                            <td><input type="text" name="" value="<?=$grand_net_sprt[3]?>" class="fntTblHdr" readonly  style="width:100%;"></td>
                                            <td><input type="text" name="" value="<?=$grand_net_sprt[9]?>" class="fntTblHdr" readonly style="width:100%;"></td>
                                            <td align="right"><?=number_format($grand_net_sprt_total,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar" style="height:30px;">
                                             <td  class="gridDtlLbl" colspan="6">BANK ADVICE</td>
                                        </tr>
                                        
                                       <tr class="gridToolbar">
                                            <td class="fntTblHdr">AUB</td>
                                            <td><input type="text" name="arrPayRegAUB1" value="<?=$arrPayRegAUB1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegAUB2" value="<?=$arrPayRegAUB2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegAUB3" value="<?=$arrPayRegAUB3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegAUB9" value="<?=$arrPayRegAUB9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><?=number_format($grand_AUB,2)?></td>
                                        </tr>
                                        
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">MBTC</td>
                                            <td><input type="text" name="arrPayRegMBTC1" value="<?=$arrPayRegMBTC1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegMBTC2" value="<?=$arrPayRegMBTC2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegMBTC3" value="<?=$arrPayRegMBTC3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegMBTC9" value="<?=$arrPayRegMBTC9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><?=number_format($grand_MBTC,2)?></td>
                                        </tr>
                                        
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">BOC</td>
                                            <td><input type="text" name="arrPayRegBOC1" value="<?=$arrPayRegBOC1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegBOC2" value="<?=$arrPayRegBOC2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegBOC3" value="<?=$arrPayRegBOC3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegBOC9" value="<?=$arrPayRegBOC9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><?=number_format($grand_BOC,2)?></td>
                                        </tr>
                                        
                                         <tr class="gridToolbar">
                                            <td class="fntTblHdr">CASH</td>
                                            <td><input type="text" name="arrPayRegCASH1" value="<?=$arrPayRegCASH1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegCASH2" value="<?=$arrPayRegCASH2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegCASH3" value="<?=$arrPayRegCASH3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="arrPayRegCASH9" value="<?=$arrPayRegCASH9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><?=number_format($grand_CASH,2)?></td>
                                        </tr>
                                        
                                        <tr class="gridToolbar">
                                            <td class="fntTblHdr">GRAND TOTALS </td>
                                            <td><input type="text" name="" value="<?=$arrPayRegtot1?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="" value="<?=$arrPayRegtot2?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="" value="<?=$arrPayRegtot3?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td><input type="text" name="" value="<?=$arrPayRegtot9?>" class="fntTblHdr"  style="width:100%;"></td>
                                            <td align="right"><font color="<?=$font_PayReg_tot?>"><?=number_format($grand_PayReg_tot ,2)?></td>
                                        </tr>
                                    </table>
                            </td>
                        </tr>
                       
            		</table>
                    <br>
                    
                    
                    
            		<br>
                    
           		</td>
            </tr> 
        
        </table>
        </form>
	</BODY>
</HTML>
<script language="javascript">
	function saveRecon($error)
	{
		if($error!=0)
		{
			alert("Error Occur in Reconciliation; Check the Red Font Color.");
			return false;
		}
		
	}
</script>