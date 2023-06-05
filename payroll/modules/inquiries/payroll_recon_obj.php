<?
class inqTSObj extends commonObj
{
	
	
	
	function getAllPeriod() 
	{
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
			WHERE compCode = '".$_SESSION["company_code"]."' AND 
			payGrp = '{$_SESSION['pay_group']}' AND 
			payCat = '{$_SESSION['pay_category']}' ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getSlctdPd($compCode,$payPd) 
	{
		$qry = "SELECT * FROM tblPayPeriod 
				WHERE pdSeries = '$payPd' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}

	function payReconObj($pdNumber,$pdYear)
	{
		/*
		Conventions:
			
		*/
		$payrollvariables = array();
		
		/*Get HeadCount*/
		$qryHCount = "SELECT payCat,COUNT(*) AS empCount
							FROM  tblPayrollSummary
							WHERE  compCode='".$_SESSION["company_code"]."' and (pdYear = '".$pdYear."') AND (pdNumber = '".$pdNumber."') AND (payGrp = '".$_SESSION["pay_group"]."')
							GROUP BY payCat;";
		$resHCount = $this->execQry($qryHCount);
		$arrHCount = $this->getArrRes($resHCount);
		
		foreach($arrHCount as $arrHCount_val)
			$payrollvariables["HeadCount".$arrHCount_val["payCat"]]=$arrHCount_val["empCount"];
		
		/*Get Gross Earnings via tblEarnings*/
		$qrytblEarnGross = "SELECT paySum.payCat, SUM(earn.trnAmountE) AS tblEarnings_GrossEarn
							FROM tblEarnings earn INNER JOIN tblPayrollSummary paySum 
							ON earn.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND (earn.sprtPS IS NULL OR earn.sprtPS = '')
							GROUP BY paySum.payCat";
		$restblEarnGross = $this->execQry($qrytblEarnGross);
		$arrtblEarnGross = $this->getArrRes($restblEarnGross);
		
		foreach($arrtblEarnGross as $arrtblEarnGross_val)
			$payrollvariables["tblEarnGross".$arrtblEarnGross_val["payCat"]]=$arrtblEarnGross_val["tblEarnings_GrossEarn"];
			
		
		/*Get Gross Earnings via tblPayrollSummary*/
		$qrytblPaySumGross = "SELECT  payCat,SUM(grossEarnings) AS tblPaySum_GrossEarn
								FROM  tblPayrollSummary
								WHERE (pdYear = '".$pdYear."') AND (pdNumber =  '".$pdNumber."') AND (payGrp = '".$_SESSION["pay_group"]."')
								GROUP BY payCat";
		$restblPaySumGross = $this->execQry($qrytblPaySumGross);
		$arrtblPaySumGross = $this->getArrRes($restblPaySumGross);
		
		foreach($arrtblPaySumGross as $arrtblPaySumGross)
			$payrollvariables["tblPaySumGross".$arrtblPaySumGross["payCat"]]=$arrtblPaySumGross["tblPaySum_GrossEarn"];	
		
		
		/*Get Gross Earnings via tblYtdData*/
		$qrytblYtdGross = "SELECT paySum.payCat, SUM(YtdGross) AS tblYtdData_GrossEarn
							FROM tblYtdData ytdData INNER JOIN tblPayrollSummary paySum 
							ON ytdData.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							GROUP BY paySum.payCat";
		$restblYtdGross = $this->execQry($qrytblYtdGross);
		$arrtblYtdGross = $this->getArrRes($restblYtdGross);
		
		foreach($arrtblYtdGross as $arrtblYtdGross_val)
			$payrollvariables["tblYtdGross".$arrtblYtdGross_val["payCat"]]=$arrtblYtdGross_val["tblYtdData_GrossEarn"];
		
		
		/*Get Taxable Earnings via tblEarnings*/
		$qrytblEarnTaxEarn = "SELECT paySum.payCat, SUM(earn.trnAmountE) AS tblEarnings_TaxEarn
							FROM tblEarnings earn INNER JOIN tblPayrollSummary paySum 
							ON earn.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND (trnTaxCd = 'Y')
							GROUP BY paySum.payCat";
		$restblEarnTaxEarn = $this->execQry($qrytblEarnTaxEarn);
		$arrtblEarnTaxEarn = $this->getArrRes($restblEarnTaxEarn);
		
		foreach($arrtblEarnTaxEarn as $arrtblEarnTaxEarn_val)
			$payrollvariables["tblEarnTaxEarn".$arrtblEarnTaxEarn_val["payCat"]]=$arrtblEarnTaxEarn_val["tblEarnings_TaxEarn"];
			
		
		/*Get Minimum Wage via tblEarnings*/
		$qrytblEarnMinWage = "SELECT paySum.payCat, SUM(earn.trnAmountE) AS tblEarnings_MinWage
							FROM tblEarnings earn INNER JOIN tblPayrollSummary paySum 
							ON earn.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND (trnTaxCd = 'Y') AND taxableEarnings=0
							GROUP BY paySum.payCat";
		$restblEarnMinWage = $this->execQry($qrytblEarnMinWage);
		$arrtblEarnMinWage = $this->getArrRes($restblEarnMinWage);
		
		foreach($arrtblEarnMinWage as $arrtblEarnMinWage_val)
			$payrollvariables["tblEarnMinWage".$arrtblEarnMinWage_val["payCat"]]=$arrtblEarnMinWage_val["tblEarnings_MinWage"];
				
		/*Get Taxable Earnings via tblPayrollSummary*/
		$qrytblPaySumtaxEarn = "SELECT  payCat,SUM(taxableEarnings) AS tblPaySum_TaxEarn
								FROM  tblPayrollSummary
								WHERE (pdYear = '".$pdYear."') AND (pdNumber =  '".$pdNumber."') AND (payGrp = '".$_SESSION["pay_group"]."')
								GROUP BY payCat";
		$restblPaySumtaxEarn = $this->execQry($qrytblPaySumtaxEarn);
		$arrtblPaySumtaxEarn = $this->getArrRes($restblPaySumtaxEarn);
		
		foreach($arrtblPaySumtaxEarn as $arrtblPaySumtaxEarn_val)
			$payrollvariables["tblPaySumTaxEarn".$arrtblPaySumtaxEarn_val["payCat"]]=$arrtblPaySumtaxEarn_val["tblPaySum_TaxEarn"];	
		
		
		/*Get Tax Earnings via tblYtdData*/
		$qrytblYtdtaxEarn = "SELECT paySum.payCat, SUM(YtdTaxable) AS tblYtdData_TaxEarn
							FROM tblYtdData ytdData INNER JOIN tblPayrollSummary paySum 
							ON ytdData.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							GROUP BY paySum.payCat";
		$restblYtdtaxEarn = $this->execQry($qrytblYtdtaxEarn);
		$arrtblYtdtaxEarn = $this->getArrRes($restblYtdtaxEarn);
		
		foreach($arrtblYtdtaxEarn as $arrtblYtdtaxEarn_val)
			$payrollvariables["tblYtdTaxEarn".$arrtblYtdtaxEarn_val["payCat"]]=$arrtblYtdtaxEarn_val["tblYtdData_TaxEarn"];
		
		
		/*Get Deductions without Tax via tblDeduction*/
		$qrytblDedDed = "SELECT paySum.payCat, SUM(trnAmountD) AS tblDeductions_DedExceptTax
							FROM tblDeductions ded INNER JOIN tblPayrollSummary paySum 
							ON ded.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND trnCode not in ('5100', '8024','8124')
							GROUP BY paySum.payCat";
		$restblDedDed = $this->execQry($qrytblDedDed);
		$arrtblDedDed = $this->getArrRes($restblDedDed);
		
		foreach($arrtblDedDed as $arrtblDedDed_val)
			$payrollvariables["tblDedDed".$arrtblDedDed_val["payCat"]]=$arrtblDedDed_val["tblDeductions_DedExceptTax"];
		
		
		
		/*Get Deductions via tblPayrollSummary*/
		$qrytblPaySumDed = "SELECT  payCat,SUM(totDeductions) AS tblPaySum_Ded
								FROM  tblPayrollSummary
								WHERE (pdYear = '".$pdYear."') AND (pdNumber =  '".$pdNumber."') AND (payGrp = '".$_SESSION["pay_group"]."')
								GROUP BY payCat";
		$restblPaySumDed = $this->execQry($qrytblPaySumDed);
		$arrtblPaySumDed = $this->getArrRes($restblPaySumDed);
		
		foreach($arrtblPaySumDed as $arrtblPaySumDed_val)
			$payrollvariables["tblPaySumDed".$arrtblPaySumDed_val["payCat"]]=$arrtblPaySumDed_val["tblPaySum_Ded"];	
		
		
		/*Get Governmentals via tblDeduction*/
		$qrytblDedGov = "SELECT paySum.payCat, SUM(trnAmountD) AS tblDeductions_Gov
							FROM tblDeductions ded INNER JOIN tblPayrollSummary paySum 
							ON ded.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND trnCode  in ('5200',5300,5400)
							GROUP BY paySum.payCat";
		$restblDedGov = $this->execQry($qrytblDedGov);
		$arrtblDedGov = $this->getArrRes($restblDedGov);
		
		foreach($arrtblDedGov as $arrtblDedGov_val)
			$payrollvariables["tblDedGov".$arrtblDedGov_val["payCat"]]=$arrtblDedGov_val["tblDeductions_Gov"];
		
		
		/*Get Governmentals via tblMtdGovt*/
		$qrytblMtdGov = "SELECT paySum.payCat,  SUM(sssEmp + hdmfEmp + phicEmp) AS tblMtdGovt_Gov
							FROM tblMtdGovt ded INNER JOIN tblPayrollSummary paySum 
							ON ded.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							GROUP BY paySum.payCat";
		$restblMtdGov = $this->execQry($qrytblMtdGov);
		$arrtblMtdGov = $this->getArrRes($restblMtdGov);
		
		foreach($arrtblMtdGov as $arrtblMtdGov_val)
			$payrollvariables["tblMtdGovt".$arrtblMtdGov_val["payCat"]]=$arrtblMtdGov_val["tblMtdGovt_Gov"];
			
			
		/*Get MinWage Governmentals via tblDeduction*/
		$qrytblDedNoGov = "SELECT paySum.payCat, SUM(trnAmountD) AS tblDeductions_NoGov
							FROM tblDeductions ded INNER JOIN tblPayrollSummary paySum 
							ON ded.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							and taxableEarnings=0 AND trnCode  in ('5200',5300,5400)
							GROUP BY paySum.payCat";
		$restblDedNoGov = $this->execQry($qrytblDedNoGov);
		$arrtblDedNoGov = $this->getArrRes($restblDedNoGov);
		
		foreach($arrtblDedNoGov as $arrtblDedNoGov)
			$payrollvariables["tblNoGov".$arrtblDedNoGov["payCat"]]=$arrtblDedNoGov["tblDeductions_NoGov"];
		
		
		/*Get Governmentals via tblYtdData*/
		$qrytblYtdGovDed = "SELECT paySum.payCat, SUM(YtdGovDed) AS tblYtdData_YtdGov
							FROM tblYtdData ytdData INNER JOIN tblPayrollSummary paySum 
							ON ytdData.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							GROUP BY paySum.payCat";
		$restblYtdGovDed = $this->execQry($qrytblYtdGovDed);
		$arrtblYtdGovDed = $this->getArrRes($restblYtdGovDed);
		
		foreach($arrtblYtdGovDed as $arrtblYtdGovDed_val)
			$payrollvariables["tblYtdGovDed".$arrtblYtdGovDed_val["payCat"]]=$arrtblYtdGovDed_val["tblYtdData_YtdGov"];
		
		
		/*Get Witholding Tax via tblDeduction*/
		$qrytblDedTax = "SELECT paySum.payCat, SUM(trnAmountD) AS tblDeductions_Tax
							FROM tblDeductions ded INNER JOIN tblPayrollSummary paySum 
							ON ded.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND trnCode  in (5100,8024)
							GROUP BY paySum.payCat";
		$restblDedTax = $this->execQry($qrytblDedTax);
		$arrtblDedTax = $this->getArrRes($restblDedTax);
		
		foreach($arrtblDedTax as $arrtblDedTax_val)
			$payrollvariables["tblDedTax".$arrtblDedTax_val["payCat"]]=$arrtblDedTax_val["tblDeductions_Tax"];
		
		
		/*Get Tax via tblPayrollSummary*/
		$qrytblPaySumTax = "SELECT  payCat,SUM(taxWitheld) AS tblPaySum_Tax
								FROM  tblPayrollSummary
								WHERE (pdYear = '".$pdYear."') AND (pdNumber =  '".$pdNumber."') AND (payGrp = '".$_SESSION["pay_group"]."')
								GROUP BY payCat";
		$restblPaySumTax = $this->execQry($qrytblPaySumTax);
		$arrtblPaySumTax = $this->getArrRes($restblPaySumTax);
		
		foreach($arrtblPaySumTax as $arrtblPaySumTax_val)
			$payrollvariables["tblPaySumTax".$arrtblPaySumTax_val["payCat"]]=$arrtblPaySumTax_val["tblPaySum_Tax"];	
		
		
		
		/*Get tax Witheld via tblYtdData*/
		$qrytblYtdTax = "SELECT paySum.payCat, SUM(YtdTax) AS tblYtdData_YtdTax
							FROM tblYtdData ytdData INNER JOIN tblPayrollSummary paySum 
							ON ytdData.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							GROUP BY paySum.payCat";
		$restblYtdTax = $this->execQry($qrytblYtdTax);
		$arrtblYtdTax = $this->getArrRes($restblYtdTax);
		
		foreach($arrtblYtdTax as $arrtblYtdTax_val)
			$payrollvariables["tblYtdTax".$arrtblYtdTax_val["payCat"]]=$arrtblYtdTax_val["tblYtdData_YtdTax"];
		
		/*Get SprtAllow via tblEarnings*/	
		$qrytblEarnSprtAllow = "SELECT paySum.payCat, SUM(earn.trnAmountE) AS tblEarnings_SprtAllow
							FROM tblEarnings earn INNER JOIN tblPayrollSummary paySum 
							ON earn.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND (sprtPS = 'Y')
							GROUP BY paySum.payCat";
		$restblEarnSprtAllow = $this->execQry($qrytblEarnSprtAllow);
		$arrtblEarnSprtAllow = $this->getArrRes($restblEarnSprtAllow);
		
		foreach($arrtblEarnSprtAllow as $arrtblEarnSprtAllow_val)
			$payrollvariables["tblEarnSprtAllow".$arrtblEarnSprtAllow_val["payCat"]]=$arrtblEarnSprtAllow_val["tblEarnings_SprtAllow"];
			
			
		/*Get SprtAllow-Deduction via tblDeduction*/			
		$qrytblDedSprtAllow = "SELECT paySum.payCat, SUM(trnAmountD) AS tblDeductions_SprtAllow
							FROM tblDeductions ded INNER JOIN tblPayrollSummary paySum 
							ON ded.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND (sprtPS = '1') 
							GROUP BY paySum.payCat";
		$restblSprtAllow = $this->execQry($qrytblDedSprtAllow);
		$arrtblSprtAllow = $this->getArrRes($restblSprtAllow);
		
		foreach($arrtblSprtAllow as $arrtblSprtAllow_val)
			$payrollvariables["tblDedSprtAllow".$arrtblSprtAllow_val["payCat"]]=$arrtblSprtAllow_val["tblDeductions_SprtAllow"];
		
		
		
										
		/*Get SprtAllow via tblPayrollSummary*/
		$qrytblPaySumSprtAllow = "SELECT  payCat,SUM(sprtAllow) AS tblPaySum_SprtAllow
								FROM  tblPayrollSummary
								WHERE (pdYear = '".$pdYear."') AND (pdNumber =  '".$pdNumber."') AND (payGrp = '".$_SESSION["pay_group"]."')
								GROUP BY payCat";
		$restblPaySumSprtAllow = $this->execQry($qrytblPaySumSprtAllow);
		$arrtblPaySumSprtAllow = $this->getArrRes($restblPaySumSprtAllow);
		
		foreach($arrtblPaySumSprtAllow as $arrtblPaySumSprtAllow_val)
			$payrollvariables["tblPaySumSprtAllow".$arrtblPaySumSprtAllow_val["payCat"]]=$arrtblPaySumSprtAllow_val["tblPaySum_SprtAllow"];	
		
		
		/*Get SprtAllow via tblYtdData*/
		$qrytblYtdSprtAllow = "SELECT paySum.payCat, SUM(ytdData.sprtAllow) AS tblYtdData_SprtAllow
							FROM tblYtdData ytdData INNER JOIN tblPayrollSummary paySum 
							ON ytdData.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							GROUP BY paySum.payCat";
		$restblYtdSprtAllow = $this->execQry($qrytblYtdSprtAllow);
		$arrtblYtdSprtAllow = $this->getArrRes($restblYtdSprtAllow);
		
		foreach($arrtblYtdSprtAllow as $arrtblYtdSprtAllow_val)
			$payrollvariables["tblYtdSprtAllow".$arrtblYtdSprtAllow_val["payCat"]]=$arrtblYtdSprtAllow_val["tblYtdData_SprtAllow"];
		
		/*Get SprtAdvances via tblEarnings*/	
		$qrytblEarnSprtAdvances = "SELECT paySum.payCat, SUM(earn.trnAmountE) AS tblEarnings_SprtAdvances
							FROM tblEarnings earn INNER JOIN tblPayrollSummary paySum 
							ON earn.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND trnCode in ('8101', '8119')
							GROUP BY paySum.payCat";
		$restblEarnSprtAdvances = $this->execQry($qrytblEarnSprtAdvances);
		$arrtblEarnSprtAdvances = $this->getArrRes($restblEarnSprtAdvances);
		
		foreach($arrtblEarnSprtAdvances as $arrtblEarnSprtAdvances_val)
			$payrollvariables["tblEarnSprtAdvances".$arrtblEarnSprtAdvances_val["payCat"]]=$arrtblEarnSprtAdvances_val["tblEarnings_SprtAdvances"];
			
		
		/*Get SprtAdvances via tblPayrollSummary*/
		$qrytblPaySumSprtAdvances = "SELECT  payCat,SUM(sprtAllowAdvance) AS tblPaySum_SprtAdvances
								FROM  tblPayrollSummary
								WHERE (pdYear = '".$pdYear."') AND (pdNumber =  '".$pdNumber."') AND (payGrp = '".$_SESSION["pay_group"]."')
								GROUP BY payCat";
		$restblPaySumSprtAdvances = $this->execQry($qrytblPaySumSprtAdvances);
		$arrtblPaySumSprtAdvances = $this->getArrRes($restblPaySumSprtAdvances);
		
		foreach($arrtblPaySumSprtAdvances as $arrtblPaySumSprtAdvances_val)
			$payrollvariables["tblPaySumSprtAdvances".$arrtblPaySumSprtAdvances_val["payCat"]]=$arrtblPaySumSprtAdvances_val["tblPaySum_SprtAdvances"];	
		
		
		/*Get SprtAdvances via tblYtdData*/
		$qrytblYtdSprtAdvances = "SELECT paySum.payCat, SUM(ytdData.sprtAdvance) AS tblYtdData_SprtAdvances
							FROM tblYtdData ytdData INNER JOIN tblPayrollSummary paySum 
							ON ytdData.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							GROUP BY paySum.payCat";
		$restblYtdSprtAdvances = $this->execQry($qrytblYtdSprtAdvances);
		$arrtblYtdSprtAdvances = $this->getArrRes($restblYtdSprtAdvances);
		
		foreach($arrtblYtdSprtAdvances as $arrtblYtdSprtAdvances_val)
			$payrollvariables["tblYtdSprtAdvances".$arrtblYtdSprtAdvances_val["payCat"]]=$arrtblYtdSprtAdvances_val["tblYtdData_SprtAdvances"];
			
			
		
		/*Get EmpBasic via tblEarnings*/
		$qrytblEarnEmpBasic = "SELECT paySum.payCat, SUM(earn.trnAmountE) AS tblEarnings_EmpBasic
							FROM tblEarnings earn INNER JOIN tblPayrollSummary paySum 
							ON earn.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							AND (trnCode in (0100,0111,0112,0113,0801,0114,0115))
							GROUP BY paySum.payCat";
		$restblEarnEmpBasic = $this->execQry($qrytblEarnEmpBasic);
		$arrtblEarnEmpBasic = $this->getArrRes($restblEarnEmpBasic);
		
		foreach($arrtblEarnEmpBasic as $arrtblEarnEmpBasic_val)
			$payrollvariables["tblEarnEmpBasic".$arrtblEarnEmpBasic_val["payCat"]]=$arrtblEarnEmpBasic_val["tblEarnings_EmpBasic"];
		
		
		/*Get EmpBasic via tblPayrollSummary*/
		$qrytblPaySumEmpBasic = "SELECT  payCat,SUM(empBasic) AS tblPaySum_empBasic
								FROM  tblPayrollSummary
								WHERE (pdYear = '".$pdYear."') AND (pdNumber =  '".$pdNumber."') AND (payGrp = '".$_SESSION["pay_group"]."')
								GROUP BY payCat";
		$restblPaySumEmpBasic = $this->execQry($qrytblPaySumEmpBasic);
		$arrtblPaySumEmpBasic= $this->getArrRes($restblPaySumEmpBasic);
		
		foreach($arrtblPaySumEmpBasic as $arrtblPaySumEmpBasic_val)
			$payrollvariables["tblPaySumEmpBasic".$arrtblPaySumEmpBasic_val["payCat"]]=$arrtblPaySumEmpBasic_val["tblPaySum_empBasic"];	
				
		
		/*Get Empbasic via tblYtdData*/
		$qrytblYtdEmpBasic = "SELECT paySum.payCat, SUM(YtdBasic) AS tblYtdData_empBasic
							FROM tblYtdData ytdData INNER JOIN tblPayrollSummary paySum 
							ON ytdData.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."') 
							GROUP BY paySum.payCat";
		$restblYtdEmpBasic = $this->execQry($qrytblYtdEmpBasic);
		$arrtblYtdEmpBasic = $this->getArrRes($restblYtdEmpBasic);
		
		foreach($arrtblYtdEmpBasic as $arrtblYtdEmpBasic_val)
			$payrollvariables["tblYtdEmpBasic".$arrtblYtdEmpBasic_val["payCat"]]=$arrtblYtdEmpBasic_val["tblYtdData_empBasic"];
		

		/*Get Net Sal via tblPayrollSummary*/
		$qrytblPaySumNetSal = "SELECT  payCat,SUM(netSalary) AS tblPaySum_NetSal
								FROM  tblPayrollSummary
								WHERE (pdYear = '".$pdYear."') AND (pdNumber =  '".$pdNumber."') AND (payGrp = '".$_SESSION["pay_group"]."')
								GROUP BY payCat";
		$restblPaySumNetSal = $this->execQry($qrytblPaySumNetSal);
		$arrtblPaySumNetSal= $this->getArrRes($restblPaySumNetSal);
		
		foreach($arrtblPaySumNetSal as $arrtblPaySumNetSal_val)
			$payrollvariables["tblPaySumNetSal".$arrtblPaySumNetSal_val["payCat"]]=$arrtblPaySumNetSal_val["tblPaySum_NetSal"];	
		
		
		return  $payrollvariables;
	}
	
	function savePayRecon($pdNumber,$pdYear, $arr_fields)
	{
		$arr_payReconFields = explode("=",$arr_fields);
		
		
		$qryInsert = "INSERT into tblPayrollRecon(compCode,pdYear,pdNumber,payGrp,remarks,	
						tot_headCount,tot_tblEarn_gross,	tot_tblSummary_grossEarnings,	tot_tblYtdData_ytdGross,	
						tot_tblEarn_taxableEarn,	tot_tblEarn_taxEarn_minWage,	tot_tblSummary_taxableEarn,tot_tblYtdData_ytdTaxable,
						tot_Loan,	tot_othDed,	tot_sprtAllow_ded,	
						tot_governmentals,	tot_withTax, tot_cashBond, tot_prevYearTax	,
						tot_tblDed_totDed,	tot_tblSummary_totDed,	tot_tblDed_totDedWithTax, tot_tblDed_sum,
						tot_tblSummary_taxWitheld,	tot_tblYtdData_ytdTax,	
						tot_tblMtdGovt_govern,	tot_tblMtdGovt_govern_minWage,	tot_tblYtdData_ytdGovDed,	
						tot_tblEarn_sprtAllow,	tot_tblDed_sprtAllow,	 tot_tblSummary_sprtAllow,tot_tblYtdData_sprtAllow,	
						tot_tblEarn_sprtAdvances,	tot_tblSummary_sprtAdvances,	tot_tblYtdData_sprtAdvances,	
						tot_tblEarn_empBasic,	tot_tblSummary_empBasic,	tot_tblYtdData_empBasic,	
						tot_tblSummary_netSalary,	tot_employee_salary)
					values('".$_SESSION["company_code"]."', '".$pdYear."','".$pdNumber."','".$_SESSION["pay_group"]."','".$arr_payReconFields["0"]."',
					  '".$arr_payReconFields[1]."','".$arr_payReconFields["2"]."','".$arr_payReconFields["3"]."','".$arr_payReconFields["4"]."',
					  '".$arr_payReconFields["5"]."','".$arr_payReconFields["6"]."','".$arr_payReconFields["7"]."','".$arr_payReconFields["8"]."',
					  '".$arr_payReconFields["9"]."','".$arr_payReconFields["10"]."','".$arr_payReconFields["11"]."',
					  '".$arr_payReconFields["12"]."','".$arr_payReconFields["13"]."','".$arr_payReconFields["14"]."','".$arr_payReconFields["35"]."',
					  '".$arr_payReconFields["15"]."','".$arr_payReconFields["16"]."','".$arr_payReconFields["17"]."', '".$arr_payReconFields["36"]."',
					  '".$arr_payReconFields["18"]."','".$arr_payReconFields["19"]."',
					  '".$arr_payReconFields["20"]."','".$arr_payReconFields["21"]."','".$arr_payReconFields["22"]."',
					  '".$arr_payReconFields["23"]."','".$arr_payReconFields["24"]."','".$arr_payReconFields["25"]."','".$arr_payReconFields["26"]."',
					  '".$arr_payReconFields["27"]."','".$arr_payReconFields["28"]."','".$arr_payReconFields["29"]."',
					  '".$arr_payReconFields["30"]."','".$arr_payReconFields["31"]."','".$arr_payReconFields["32"]."',
					  '".$arr_payReconFields["33"]."','".$arr_payReconFields["34"]."')";
		$resInsert = $this->execQry($qryInsert);	
		if(!$resInsert)
			return 1;
		else
			return 0;		  
		
	}
	
	function payReconHistObj($pdNumber,$pdYear)
	{
		$qrypayRecon = "Select * from tblPayrollRecon where compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."' and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'";
		$respayRecon = $this->execQry($qrypayRecon);	
		$arrpayRecon = $this->getSqlAssoc($respayRecon);
	
		return $arrpayRecon;
	}
	

	
}

?>