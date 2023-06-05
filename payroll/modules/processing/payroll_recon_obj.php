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
		
		$payrollvariables = array();
		
		$qryDeleteContent = "DELETE FROM tblPayrollRecon where compCode='".$_SESSION["company_code"]."' and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and payCat='".$_SESSION["pay_category"]."' and payGrp='".$_SESSION["pay_group"]."'";
		$resDeleteContent  = $this->execQry($qryDeleteContent);
		
		
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
		
		/*Pay-Reg Loans*/
		$qrytblLoans = 	"SELECT paySum.payCat, sum(trnAmountD) as totAmountLoans
							FROM tblDeductions tblDed INNER JOIN tblPayrollSummary paySum 
							ON tblDed.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."')
							AND trnCode IN 
								(SELECT trnCode 
								FROM tblLoanType 
								WHERE compCode='".$_SESSION["company_code"]."' AND lonTypeStat='A') AND tblDed.sprtPS='0' 
							GROUP BY paySum.payCat";
		$restblLoans = $this->execQry($qrytblLoans);
		$arrtblLoans= $this->getArrRes($restblLoans);
		
		foreach($arrtblLoans as $arrtblLoans_val)
			$payrollvariables["tblDedLoans".$arrtblLoans_val["payCat"]]=$arrtblLoans_val["totAmountLoans"];						
		
		/*Other Ded*/
		$qrytblOthDed = 	"SELECT paySum.payCat, sum(trnAmountD) as totAmountOthDed
							FROM tblDeductions tblDed INNER JOIN tblPayrollSummary paySum ON tblDed.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."')
								AND trnCode NOT IN (8024,8124)
								AND sprtPS='0'
								AND trnCode IN 
									(SELECT trnCode 
									 FROM tblPayTransType 
								 	 WHERE trnCode NOT IN 
								 		(SELECT trnCode 
										FROM tblLoanType 
										WHERE compCode='{$_SESSION['company_code']}') 
											AND trnCat='D' AND trnStat='A' AND trnEntry='Y')
							GROUP BY paySum.payCat";
		$restblOthDed = $this->execQry($qrytblOthDed);
		$arrtblOthDed= $this->getArrRes($restblOthDed);
		
		foreach($arrtblOthDed as $arrtblOthDed_val)
			$payrollvariables["tblDedOthDed".$arrtblOthDed_val["payCat"]]=$arrtblOthDed_val["totAmountOthDed"];						
			
		/*Pay-Reg Sprt Ded*/
		$qrytblSprtDed = 	"SELECT paySum.payCat, sum(trnAmountD) as totAmountSprtDed
							FROM tblDeductions tblDed INNER JOIN tblPayrollSummary paySum 
							ON tblDed.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."')
								AND tblDed.sprtPS='1' 
							GROUP BY paySum.payCat";
		$restblDed = $this->execQry($qrytblSprtDed);
		$arrtblDed= $this->getArrRes($restblDed);
		
		foreach($arrtblDed as $arrtblDed_val)
			$payrollvariables["tblSprtDed".$arrtblDed_val["payCat"]]=$arrtblDed_val["totAmountSprtDed"];			
		
		
		/*Pay-Reg Governmentals*/
		$qrytblGov = 	"SELECT paySum.payCat, sum(trnAmountD) as totGov
							FROM tblDeductions tblDed INNER JOIN tblPayrollSummary paySum 
							ON tblDed.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."')
							and trnCode in (5200,5300,5400) 
							GROUP BY paySum.payCat";
		$restblGov= $this->execQry($qrytblGov);
		$arrtblGov= $this->getArrRes($restblGov);
		
		foreach($arrtblGov as $arrtblGov_val)
			$payrollvariables["tblGovernmentals".$arrtblGov_val["payCat"]]=$arrtblGov_val["totGov"];			
		
		
		/*Previous Tax Adjustment*/
		$qrytblPrevYrTax = 	"SELECT paySum.payCat, sum(trnAmountD) as totAmountPrevYrTax
							FROM tblDeductions tblDed INNER JOIN tblPayrollSummary paySum 
							ON tblDed.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."')
								AND trnCode in (8124) 
							GROUP BY paySum.payCat";
		$restblPrevYrTax = $this->execQry($qrytblPrevYrTax);
		$arrtblPrevYrTax= $this->getArrRes($restblPrevYrTax);
		
		foreach($arrtblPrevYrTax as $arrtblPrevYrTax_val)
			$payrollvariables["tblPrevYrTax".$arrtblPrevYrTax_val["payCat"]]=$arrtblPrevYrTax_val["totAmountPrevYrTax"];	
			
		
		/*Cash Bond*/
		$qrytblCashBond = 	"SELECT paySum.payCat, sum(cashBond) as empCashBond
							FROM tblLastPayData tblDed INNER JOIN tblPayrollSummary paySum 
							ON tblDed.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."')
							GROUP BY paySum.payCat";
		$restblCashBond = $this->execQry($qrytblCashBond);
		$arrtblCashBond= $this->getArrRes($restblCashBond);
		
		foreach($arrtblCashBond as $arrtblCashBond_val)
			$payrollvariables["tblCashBond".$arrtblCashBond_val["payCat"]]=$arrtblCashBond_val["empCashBond"];	
			
		
		/*With Tax Adjustment - Tax Witheld*/
		$qrytblTaxAdjTax = 	"SELECT paySum.payCat, sum(trnAmountD) as totTaxAdjTax
							FROM tblDeductions tblDed INNER JOIN tblPayrollSummary paySum 
							ON tblDed.empNo = paySum.empNo
							WHERE (paySum.pdYear = '".$pdYear."') AND (paySum.pdNumber = '".$pdNumber."') AND (paySum.payGrp = '".$_SESSION["pay_group"]."')
								AND trnCode in (5100,8024) 
							GROUP BY paySum.payCat";
		$restblTaxAdjTax = $this->execQry($qrytblTaxAdjTax);
		$arrtblTaxAdjTax= $this->getArrRes($restblTaxAdjTax);
		
		foreach($arrtblTaxAdjTax as $arrtblTaxAdjTax_val)
			$payrollvariables["tblTaxAdjTax".$arrtblTaxAdjTax_val["payCat"]]=$arrtblTaxAdjTax_val["totTaxAdjTax"];	
		
									
		
		$grand_hCount = $payrollvariables["HeadCount1"] + $payrollvariables["HeadCount2"] + $payrollvariables["HeadCount3"] + $payrollvariables["HeadCount9"]; 
		$grand_tblEarnGross = $payrollvariables["tblEarnGross1"] + $payrollvariables["tblEarnGross2"] + $payrollvariables["tblEarnGross3"] + $payrollvariables["tblEarnGross9"]; 
		$grand_tblPaySumGross = $payrollvariables["tblPaySumGross1"] + $payrollvariables["tblPaySumGross2"] + $payrollvariables["tblPaySumGross3"] + $payrollvariables["tblPaySumGross9"]; 
		$grand_tblYtdGross = $payrollvariables["tblYtdGross1"] + $payrollvariables["tblYtdGross2"] + $payrollvariables["tblYtdGross3"] + $payrollvariables["tblYtdGross9"]; 
		$grand_tblEarnTaxEarn = $payrollvariables["tblEarnTaxEarn1"] + $payrollvariables["tblEarnTaxEarn2"] + $payrollvariables["tblEarnTaxEarn3"] + $payrollvariables["tblEarnTaxEarn9"]; 
		$grand_tblEarnMinWage = $payrollvariables["tblEarnMinWage1"] + $payrollvariables["tblEarnMinWage2"] + $payrollvariables["tblEarnMinWage3"] + $payrollvariables["tblEarnMinWage9"]; 
		$grand_tblPaySumTaxEarn = $payrollvariables["tblPaySumTaxEarn1"] + $payrollvariables["tblPaySumTaxEarn2"] + $payrollvariables["tblPaySumTaxEarn3"] + $payrollvariables["tblPaySumTaxEarn9"]; 
		$grand_tblYtdTaxEarn = $payrollvariables["tblYtdTaxEarn1"] + $payrollvariables["tblYtdTaxEarn2"] + $payrollvariables["tblYtdTaxEarn3"] + $payrollvariables["tblYtdTaxEarn9"]; 
		$grand_tblDedDed = $payrollvariables["tblDedDed1"] + $payrollvariables["tblDedDed2"] + $payrollvariables["tblDedDed3"] + $payrollvariables["tblDedDed9"]; 
		$grand_tblPaySumDed = $payrollvariables["tblPaySumDed1"] + $payrollvariables["tblPaySumDed2"] + $payrollvariables["tblPaySumDed3"] + $payrollvariables["tblPaySumDed9"]; 
		$grand_tblDedGov = $payrollvariables["tblDedGov1"] + $payrollvariables["tblDedGov2"] + $payrollvariables["tblDedGov3"] + $payrollvariables["tblDedGov9"]; 
		$grand_tblMtdGovt = $payrollvariables["tblMtdGovt1"] + $payrollvariables["tblMtdGovt2"] + $payrollvariables["tblMtdGovt3"] + $payrollvariables["tblMtdGovt9"]; 
		$grand_tblNoGovt = $payrollvariables["tblNoGov1"] + $payrollvariables["tblNoGov2"] + $payrollvariables["tblNoGov3"] + $payrollvariables["tblNoGov9"]; 
		$grand_tblYtdGovDed = $payrollvariables["tblYtdGovDed1"] + $payrollvariables["tblYtdGovDed2"] + $payrollvariables["tblYtdGovDed3"] + $payrollvariables["tblYtdGovDed9"]; 
		$grand_tblDedTax = $payrollvariables["tblDedTax1"] + $payrollvariables["tblDedTax2"] + $payrollvariables["tblDedTax3"] + $payrollvariables["tblDedTax9"]; 
		$grand_tblPaySumTax = $payrollvariables["tblPaySumTax1"] + $payrollvariables["tblPaySumTax2"] + $payrollvariables["tblPaySumTax3"] + $payrollvariables["tblPaySumTax9"]; 
		$grand_tblYtdTax = $payrollvariables["tblYtdTax1"] + $payrollvariables["tblYtdTax2"] + $payrollvariables["tblYtdTax3"] + $payrollvariables["tblYtdTax9"]; 
		$grand_tblEaningsSprtAllow = $payrollvariables["tblEarnSprtAllow1"] + $payrollvariables["tblEarnSprtAllow2"] + $payrollvariables["tblEarnSprtAllow3"] + $payrollvariables["tblEarnSprtAllow9"]; 
		$grand_tblDeductionSprtAllow = $payrollvariables["tblDedSprtAllow1"] + $payrollvariables["tblDedSprtAllow2"] + $payrollvariables["tblDedSprtAllow3"] + $payrollvariables["tblDedSprtAllow9"]; 
		$grand_tblPaySumSprtAllow = $payrollvariables["tblPaySumSprtAllow1"] + $payrollvariables["tblPaySumSprtAllow2"] + $payrollvariables["tblPaySumSprtAllow3"] + $payrollvariables["tblPaySumSprtAllow9"]; 
		$grand_tblYtdSprtAllow = $payrollvariables["tblYtdSprtAllow1"] + $payrollvariables["tblYtdSprtAllow2"] + $payrollvariables["tblYtdSprtAllow3"] + $payrollvariables["tblYtdSprtAllow9"]; 
		$grand_tblEaningsSprtAdvances = $payrollvariables["tblEarnSprtAdvances1"] + $payrollvariables["tblEarnSprtAdvances2"] + $payrollvariables["tblEarnSprtAdvances3"] + $payrollvariables["tblEarnSprtAdvances9"]; 
		$grand_tblPaySumSprtAdvances = $payrollvariables["tblPaySumSprtAdvances1"] + $payrollvariables["tblPaySumSprtAdvances2"] + $payrollvariables["tblPaySumSprtAdvances3"] + $payrollvariables["tblPaySumSprtAdvances9"]; 
		$grand_tblYtdDataSprtAdvances = $payrollvariables["tblYtdSprtAdvances1"] + $payrollvariables["tblYtdSprtAdvances2"] + $payrollvariables["tblYtdSprtAdvances3"] + $payrollvariables["tblYtdSprtAdvances9"]; 
		$grand_tblEarnEmpBasic = $payrollvariables["tblEarnEmpBasic1"] + $payrollvariables["tblEarnEmpBasic2"] + $payrollvariables["tblEarnEmpBasic3"] + $payrollvariables["tblEarnEmpBasic9"]; 
		$grand_tblPaySumEmpBasic = $payrollvariables["tblPaySumEmpBasic1"] + $payrollvariables["tblPaySumEmpBasic2"] + $payrollvariables["tblPaySumEmpBasic3"] + $payrollvariables["tblPaySumEmpBasic9"]; 
		$grand_tblYtdEmpBasic = $payrollvariables["tblYtdEmpBasic1"] + $payrollvariables["tblYtdEmpBasic2"] + $payrollvariables["tblYtdEmpBasic3"] + $payrollvariables["tblYtdEmpBasic9"]; 
		$grand_tblPaySumNetSal = $payrollvariables["tblPaySumNetSal1"] + $payrollvariables["tblPaySumNetSal2"] + $payrollvariables["tblPaySumNetSal3"] + $payrollvariables["tblPaySumNetSal9"]; 
		$arrPayReg_totLoans = $payrollvariables["tblDedLoans1"] +  $payrollvariables["tblDedLoans2"] +  $payrollvariables["tblDedLoans3"] +  $payrollvariables["tblDedLoans9"];
		$arrPayReg_totOthDed =  $payrollvariables["tblDedOthDed1"] + $payrollvariables["tblDedOthDed2"] + $payrollvariables["tblDedOthDed3"] + $payrollvariables["tblDedOthDed9"];
		$arrPayReg_totSprtOthDed = $payrollvariables["tblSprtDed1"] +  $payrollvariables["tblSprtDed2"] +  $payrollvariables["tblSprtDed3"] +  $payrollvariables["tblSprtDed9"];
		$arrPayReg_totGov = $payrollvariables["tblGovernmentals1"] + $payrollvariables["tblGovernmentals2"] + $payrollvariables["tblGovernmentals3"] + $payrollvariables["tblGovernmentals9"];
		$arrPayReg_totTax = $payrollvariables["tblTaxAdjTax1"] +  $payrollvariables["tblTaxAdjTax2"] +  $payrollvariables["tblTaxAdjTax3"] +  $payrollvariables["tblTaxAdjTax9"];
		
		$arrPayReg_totEncCsBond = $payrollvariables["tblCashBond1"] + $payrollvariables["tblCashBond2"] + $payrollvariables["tblCashBond3"] +  $payrollvariables["tblCashBond9"];  
		
		$grand_Prevtax = $payrollvariables["tblPrevYrTax1"] + $payrollvariables["tblPrevYrTax2"] + $payrollvariables["tblPrevYrTax3"] + $payrollvariables["tblPrevYrTax9"];
	
		$arr_payreg_fields = "-"."=".
		$grand_hCount."=".$grand_tblEarnGross."=".$grand_tblPaySumGross."=".$grand_tblYtdGross."=".
		$grand_tblEarnTaxEarn."=".$grand_tblEarnMinWage."=".$grand_tblPaySumTaxEarn."=".$grand_tblYtdTaxEarn."=".
		$arrPayReg_totLoans."=".$arrPayReg_totOthDed."=".$arrPayReg_totSprtOthDed."=".
		$arrPayReg_totGov."=".$arrPayReg_totTax."=".$arrPayReg_totEncCsBond."=".$grand_Prevtax."=".
		$grand_tblDedDed."=".$grand_tblPaySumDed."=".($grand_tblDedDed+$arrPayReg_totTax)."=".($grand_tblDedDed+$arrPayReg_totTax+$grand_Prevtax)."=".
		$grand_tblPaySumTax."=".$grand_tblYtdTax."=".
		$grand_tblMtdGovt."=".$grand_tblNoGovt."=".$grand_tblYtdGovDed."=".
		$grand_tblEaningsSprtAllow."=".$grand_tblDeductionSprtAllow."=".$grand_tblPaySumSprtAllow."=".$grand_tblYtdSprtAllow."=".
		$grand_tblEaningsSprtAdvances."=".$grand_tblPaySumSprtAdvances."=".$grand_tblYtdDataSprtAdvances."=".
		$grand_tblEarnEmpBasic."=".$grand_tblPaySumEmpBasic."=".$grand_tblYtdEmpBasic."=".
		$grand_tblPaySumNetSal."=".($grand_tblPaySumNetSal+$grand_tblYtdSprtAllow);
		
		$arr_payReconFields = explode("=",$arr_payreg_fields);
		
		$qryInsert = "INSERT into tblPayrollRecon(compCode,pdYear,pdNumber,payGrp,payCat,remarks,	
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
					values('".$_SESSION["company_code"]."', '".$pdYear."','".$pdNumber."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','".$arr_payReconFields["0"]."',
					  '".$arr_payReconFields[1]."','".$arr_payReconFields["2"]."','".$arr_payReconFields["3"]."','".$arr_payReconFields["4"]."',
					  '".$arr_payReconFields["5"]."','".$arr_payReconFields["6"]."','".$arr_payReconFields["7"]."','".$arr_payReconFields["8"]."',
					  '".$arr_payReconFields["9"]."','".$arr_payReconFields["10"]."','".$arr_payReconFields["11"]."',
					  '".$arr_payReconFields["12"]."','".$arr_payReconFields["13"]."','".$arr_payReconFields["14"]."','".$arr_payReconFields["15"]."',
					  '".$arr_payReconFields["16"]."','".$arr_payReconFields["17"]."','".$arr_payReconFields["18"]."',  '".$arr_payReconFields["19"]."',
					  '".$arr_payReconFields["20"]."','".$arr_payReconFields["21"]."',
					  '".$arr_payReconFields["22"]."','".$arr_payReconFields["23"]."','".$arr_payReconFields["24"]."',
					  '".$arr_payReconFields["25"]."','".$arr_payReconFields["26"]."','".$arr_payReconFields["27"]."','".$arr_payReconFields["27"]."',
					  '".$arr_payReconFields["29"]."','".$arr_payReconFields["30"]."','".$arr_payReconFields["31"]."',
					  '".$arr_payReconFields["32"]."','".$arr_payReconFields["33"]."','".$arr_payReconFields["34"]."',
					  '".$arr_payReconFields["35"]."','".$arr_payReconFields["36"]."')";
			
			$resInsert = $this->execQry($qryInsert);	
			if(!$resInsert)
				return 1;
			else
				return 0;		
					  
	/*	echo $qryInsert = "INSERT into tblPayrollRecon(compCode,pdYear,pdNumber,payGrp,remarks,	
						tot_headCount,tot_tblEarn_gross,	tot_tblSummary_grossEarnings,	tot_tblYtdData_ytdGross,	
						tot_tblEarn_taxableEarn,	tot_tblEarn_taxEarn_minWage,	tot_tblSummary_taxableEarn,tot_tblYtdData_ytdTaxable,
						tot_Loan,	tot_othDed,	tot_sprtAllow_ded,	
						tot_governmentals,	tot_withTax, tot_cashBond, tot_prevYearTax	,
						tot_tblDed_totDed,	tot_tblSummary_totDed,	tot_tblDed_totDedWithTax, tot_tblDed_sum,
						tot_tblSummary_taxWitheld,	tot_tblYtdData_ytdTax,	
						ot_tblMtdGovt_govern,	tot_tblMtdGovt_govern_minWage,	tot_tblYtdData_ytdGovDed,	
						tot_tblEarn_sprtAllow,	tot_tblDed_sprtAllow,	 tot_tblSummary_sprtAllow,tot_tblYtdData_sprtAllow,	
						tot_tblEarn_sprtAdvances,	tot_tblSummary_sprtAdvances,	tot_tblYtdData_sprtAdvances,	
						tot_tblEarn_empBasic,	tot_tblSummary_empBasic,	tot_tblYtdData_empBasic,	
						tot_tblSummary_netSalary,	tot_employee_salary)
					values('".$_SESSION["company_code"]."', '".$pdYear."','".$pdNumber."','".$_SESSION["pay_group"]."','".$arr_payReconFields["0"]."',
					  '".$arr_payReconFields[1]."','".$arr_payReconFields["2"]."','".$arr_payReconFields["3"]."','".$arr_payReconFields["4"]."',
					  '".$arr_payReconFields["5"]."','".$arr_payReconFields["6"]."','".$arr_payReconFields["7"]."','".$arr_payReconFields["8"]."',
					  '".$arr_payReconFields["9"]."','".$arr_payReconFields["10"]."','".$arr_payReconFields["11"]."',
					  '".$arr_payReconFields["12"]."','".$arr_payReconFields["13"]."','".$arr_payReconFields["14"]."','".$arr_payReconFields["15"]."',
					  '".$arr_payReconFields["16"]."','".$arr_payReconFields["17"]."','".$arr_payReconFields["18"]."',  '".$arr_payReconFields["19"]."',
					  '".$arr_payReconFields["20"]."','".$arr_payReconFields["21"]."',
					  '".$arr_payReconFields["22"]."','".$arr_payReconFields["23"]."','".$arr_payReconFields["24"]."',
					  '".$arr_payReconFields["25"]."','".$arr_payReconFields["26"]."','".$arr_payReconFields["27"]."','".$arr_payReconFields["27"]."',
					  '".$arr_payReconFields["29"]."','".$arr_payReconFields["30"]."','".$arr_payReconFields["31"]."',
					  '".$arr_payReconFields["32"]."','".$arr_payReconFields["33"]."','".$arr_payReconFields["34"]."',
					  '".$arr_payReconFields["35"]."','".$arr_payReconFields["36"]."')";*/
		
		
	}
	
	function payReconHistObj($pdNumber,$pdYear)
	{
		$qrypayRecon = "Select * from tblPayrollRecon where compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."' and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'";
		$respayRecon = $this->execQry($qrypayRecon);	
		$arrpayRecon = $this->getSqlAssoc($respayRecon);
	
		return $arrpayRecon;
	}
	
	function getResultArr($qrypayRecon)
	{
		$respayRecon = $this->execQry($qrypayRecon);	
		$arrpayRecon = $this->getSqlAssoc($respayRecon);
	
		return $arrpayRecon;
	}
	
	function checkPayReconObj($pdNumber,$pdYear)
	{
		
		if($_SESSION["pay_category"]==9)
			$andPayCat = " and payCat='".$_SESSION["pay_category"]."'";
		
		$qryChkPayRecon = "Select count(*) as empCount from tblPayrollSummary;";
		
		
		/*$qryChkPayRecon.="SELECT     SUM(trnAmountE) AS tblEarnings_GrossEarn_total
							FROM         tblEarnings
							WHERE     (sprtPS IS NULL) OR
												  (sprtPS = '');";
		
		$qryChkPayRecon.="SELECT     SUM(grossEarnings) AS tblPaySum_GrossEarn_total
							FROM         tblPayrollSummary";
							
		$qryChkPayRecon.="SELECT     SUM(YtdGross) AS tblYtdData_GrossEarn_total
							FROM        tblYtdData;";

		$qryChkPayRecon.="SELECT     SUM(trnAmountE) AS tblEarnings_taxableEarn_Total
							FROM         tblEarnings
							WHERE     (trnTaxCd = 'Y');";

		$qryChkPayRecon.="SELECT     tot_tblEarn_taxEarn_minWage as tot_tblEarn_taxEarn_minWage
							FROM         tblPayrolLRecon where pdNumber='".$pdNumber."' and pdYear='".$pdYear."' and payGrp='".$_SESSION["pay_group"]."' ".$andPayCat.";";


		$qryChkPayRecon.="SELECT     tot_tblEarn_taxableEarn - tot_tblEarn_taxEarn_minWage as tot_taxable_earnings
							FROM         tblPayrolLRecon where  pdNumber='".$pdNumber."' and pdYear='".$pdYear."' and payGrp='".$_SESSION["pay_group"]."' ".$andPayCat.";" ;
		*/								  
		$arrpayRecon = $this->getResultArr($qrypayRecon);
		
		//print_r($arrpayRecon);
	}
	
}

?>