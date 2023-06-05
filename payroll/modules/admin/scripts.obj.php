<?php
class scriptObj extends commonObj {
	
	function getEmp()
	{
		$qryEmp = "Select * from tblEmpMast where 
					empNo IN (010001120,
010002550,
140001651,
200000543,
210000745,
320000561,
320000564,
320000568,
560000021,
630000029,
660000028,
660000029




);";
		$rsEmp = $this->execQry($qryEmp);
		return $this->getArrRes($rsEmp);
	}
	
	function paySumHist($empNo)
	{
		$qryPaySum = "SELECT     pdNumber, empNo, grossEarnings, taxableEarnings
						FROM         tblPayrollSummaryHist
						WHERE     (empNo = '".$empNo."') and pdYear='2010' 
						ORDER BY pdNumber";
		$rsPaySum = $this->execQry($qryPaySum);
		return $this->getArrRes($rsPaySum);
	}
	
	function paySumHistMig($empNo)
	{
		$qryPaySumMig = "SELECT     pdNumber, empNo, grossEarnings, taxableEarnings
						FROM         EmpPayrollSummary$
						WHERE     (empNo = '".$empNo."') and pdYear='2010'
						ORDER BY pdNumber";
		$rsPaySumMig = $this->execQry($qryPaySumMig);
		
		$cntEmpRec = $this->getRecCount($rsPaySumMig);
		
			
		if($cntEmpRec>=1)
			return $this->getArrRes($rsPaySumMig);
		else
		{
			$qryPaySumMigGrp2 = "SELECT     pdNumber, empNo, grossEarnings, taxableEarnings
						FROM         EmpPayrollSummary$
						WHERE     (empNo = '".$empNo."') and pdYear='2010'
						ORDER BY pdNumber";
			$rsPaySumMig2 = $this->execQry($qryPaySumMigGrp2);
			return $this->getArrRes($rsPaySumMig2);
		}
	}
	
	function payYtdData($empNo)
	{
		$qryYtdData = "SELECT     empNo, YtdTaxable
							FROM         tblYtdDataHist
							WHERE     (empNo = '".$empNo."')
							and pdYear='2010'";
		$rsYtdData = $this->execQry($qryYtdData);
		return $this->getArrRes($rsYtdData);
	}
	
	function tblGovAdded($empNo)
	{
		$qryGovAddedData = "SELECT     monthPeriodDate, amtTotal, monthToDed, amountToDed, addStat
						FROM         tblGov_Tax_Added
						WHERE     (empNo = '".$empNo."')
						ORDER BY monthToDed";
		$rsGovAddedData = $this->execQry($qryGovAddedData);
		return $this->getArrRes($rsGovAddedData);
	}
	
	function getTblData($tbl, $cond, $orderBy, $ouputType)
	{
		$qryTblInfo = "Select * from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
		//echo $qryTblInfo."\n";
		$resTblInfo = $this->execQry($qryTblInfo);
		if($ouputType == 'sqlAssoc')
			return $this->getSqlAssoc($resTblInfo);
		else
			return $this->getArrRes($resTblInfo);
	}
	
	function getDatatblEarningsOTND($trnRec,$pdYear,$pdNumber, $empNo)
	{
		$qryEarningsOTND = "Select sum(trnAmountE) as totAmountE,empNo from tblEarningsHist
						where trnCode in (Select trnCode from tblPayTransType where trnRecode='$trnRec') and compCode='{$_SESSION['company_code']}'
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and empNo='".$empNo."'
						group by empNo
						";
		//echo $qryEarningsOTND."<br>";
		$resEarningsOTND = $this->execQry($qryEarningsOTND);
		$resEarningsOTND = $this->getSqlAssoc($resEarningsOTND);
		return $resEarningsOTND;
	}
	
	function getWTaxAndTaxAdj($pdYear,$pdNumber, $empNo)
	{
		$qryWTaxAndTaxAdj = "Select trnAmountD as totTax,empNo from tblDeductionsHist
						where trnCode IN ('8024','5100') 
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and empNo='".$empNo."'
						";
		$resWTaxAndTaxAdj = $this->execQry($qryWTaxAndTaxAdj);
		$resWTaxAndTaxAdj = $this->getSqlAssoc($resWTaxAndTaxAdj);
		return $resWTaxAndTaxAdj;
	}
	
	function getDataLoanstblDeductions($pdYear,$pdNumber,$empNo)
	{
		$qryLoansDeductions = "Select sum(trnAmountD) as totAmountD,empNo from tblDeductionsHist
						where trnCode in (Select trnCode from tblLoanType where compCode='{$_SESSION['company_code']}' and lonTypeStat='A') and compCode='{$_SESSION['company_code']}'
						and sprtPS='0' and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and empNo='".$empNo."'
						group by empNo
						";
		//echo $qryLoansDeductions."<br>";
		$resLoansDeductions = $this->execQry($qryLoansDeductions);
		$resLoansDeductions = $this->getSqlAssoc($resLoansDeductions);
		return $resLoansDeductions;
	}
	
	function getDataOthAdjtblDeductions($pdYear,$pdNumber,$empNo)
	{
		$qryOthAdjDeductions = "Select sum(trnAmountD) as totAmountD,empNo from tblDeductionsHist
						where trnCode NOT IN (8024,8124) and trnCode in (Select trnCode from tblPayTransType 
						where trnCode not in (Select trnCode from tblLoanType where compCode='{$_SESSION['company_code']}') and trnCat='D' and trnStat='A' and trnEntry='Y')
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and sprtPS='0'  and empNo='".$empNo."'
						group by empNo
						";
						
		$resOthAdjDeductions = $this->execQry($qryOthAdjDeductions);
		$resOthAdjDeductions = $this->getSqlAssoc($resOthAdjDeductions);
		return $resOthAdjDeductions;
	}
	
	function getDataOthtblEarnings($pdYear,$pdNumber,$empNo)
	{
		$qryEarningsOth = "Select sum(trnAmountE) as totAmountE,empNo from tblEarningsHist
						where trnCode in (Select trnCode from tblPayTransType where compCode='{$_SESSION['company_code']}' and trnCat='E' and trnStat='A' and trnEntry='Y' and trnCode not in (Select trnCode from tblAllowType where compCode='".$_SESSION["company_code"]."') )
						and compCode='{$_SESSION['company_code']}'
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and empNo='".$empNo."'
						group by empNo
						";
	
		$resEarningsOth = $this->execQry($qryEarningsOth);
		$resEarningsOth = $this->getSqlAssoc($resEarningsOth);
		return $resEarningsOth;
	}
	
	function gettblLastPayDataEmp($empNo)
	{
		$qryEmp = "Select * from tblLastPayEmp where empNo='".$empNo."'";
		$resqryEmp = $this->execQry($qryEmp);
		$resqryEmp = $this->getSqlAssoc($resqryEmp);
		return $resqryEmp;
	}
}
?>