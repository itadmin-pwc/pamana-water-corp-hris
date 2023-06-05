<?php

class closeRegPayroll extends commonObj {
	
	var $get;//method
	var $session;//session variables
	/**
	 * pass all the get variables and session variables 
	 *
	 * @param string $method
	 * @param array variable  $sessionVars
	 */
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	private function getCutOffPeriod(){

		if((int)trim((int)trim($this->get['pdNum']))%2){
			return  1;
		}
		else{
			return 2;
		}	
	}	
	
	function OpenPayPeriod() {
		if ((int)$this->get['pdNum']==24) {
			$pdYear=(int)$this->get['pdYear'] + 1;
			$pdNum=1;
		}
		else {
			$pdYear=(int)$this->get['pdYear'];
			$pdNum=(int)$this->get['pdNum'] + 1;
		}
		$qryOpen="Update tblPayPeriod set pdStat='O' 
			where (compCode = '" . $this->session['company_code'] . "') 
			AND (pdYear = '" . $pdYear . "') 
			AND (pdNumber = '" . $pdNum . "')
			AND (payGrp='" . $this->session['pay_group'] . "')
			AND (payCat='" . $this->session['pay_category'] . "')
		";
		return $this->execQry($qryOpen);
	}
	######################################################BEGIN OF CLOSING DEDCUTIONS#################################################################
	
	function getpayrolldate() {
		$qrypayrolldate="Select pdPayable from tblPayPeriod
						where (compCode = '" . $this->session['company_code'] . "') 
						AND (pdYear = '" . $this->get['pdYear'] . "') 
						AND (pdNumber = '" . $this->get['pdNum'] . "')
						AND (payGrp='" . $this->session['pay_group'] . "')
						AND (payCat='" . $this->session['pay_category'] . "')";
		return $this->getSqlAssoc($this->execQry($qrypayrolldate));				
	}	
	
	function loanslist() {
		$qrylist="SELECT tblEmpLoansDtl.compCode, tblEmpLoansDtl.empNo, tblEmpLoansDtl.lonTypeCd,tblEmpLoansDtl.lonRefNo,
				 tblEmpLoansDtl.pdYear, tblEmpLoansDtl.pdNumber, tblEmpLoansDtl.trnCat, tblEmpLoansDtl.trnGrp,
				 tblEmpLoansDtl.ActualAmt,tblEmpLoansDtl.trnAmountD,tblEmpLoansDtl.dedTag, tblEmpLoansDtl.lonLastPay,tblEmpLoansDtl.seqNo
				 FROM tblEmpLoansDtl INNER JOIN
                      tblEmpLoans ON tblEmpLoansDtl.compCode = tblEmpLoans.compCode 
					  AND tblEmpLoansDtl.lonRefNo = tblEmpLoans.lonRefNo 
					  AND tblEmpLoansDtl.lonTypeCd = tblEmpLoans.lonTypeCd 
					  AND tblEmpLoansDtl.empNo = tblEmpLoans.empNo
				WHERE  tblEmpLoansDtl.dedTag IN ('Y','P')
				AND tblEmpLoansDtl.compCode = '{$this->session['company_code']}'
				AND tblEmpLoansDtl.pdYear ='{$this->get['pdYear']}'
				AND tblEmpLoansDtl.pdNumber ='{$this->get['pdNum']}'
				AND tblEmpLoansDtl.trnCat ='{$this->session['pay_category']}'
				AND tblEmpLoansDtl.trnGrp = '{$this->session['pay_group']}'
				AND tblEmpLoansDtl.empNo IN 
				 (SELECT empNo
							FROM tblEmpMast 
							WHERE compCode = '{$this->session['company_code']}'
							AND empPayGrp = '{$this->session['pay_group']}'
							AND empPayCat = '{$this->session['pay_category']}'
							AND empStat IN ('RG','PR','CN'))
				AND (tblEmpLoansDtl.lonRefNo IN
                          (SELECT lonRefNo FROM tblEmpLoans
                            WHERE lonsked IN (3, {$this->getCutOffPeriod()})))			
							";
		return $this->getArrRes($this->execQry($qrylist));
	}

	function getlastinfoloans($empNo,$lonTypeCd,$lonRefNo){
		$qrylastinfo="Select lonWidInterst,lonPayments,lonPaymentNo,lonCurbal from tblEmpLoans 
					  where (tblEmpLoans.compCode = '" . $this->session['company_code'] . "') 
					  AND (tblEmpLoans.empNo = '$empNo')
					  AND (tblEmpLoans.lonTypeCd = '$lonTypeCd')
					  AND (tblEmpLoans.lonRefNo = '$lonRefNo')
					  AND lonSked IN (3,".$this->getCutOffPeriod().")
					   ";
		return $this->getSqlAssoc($this->execQry($qrylastinfo));	
	}
	
	function updateemploan($empNo,$lonCd,$lonPayments,$lonPaymentNo,$lonCurbal,$lonLastPay,$lonStat,$lonRefNo) {
		$qryupdateloans="Update tblEmpLoans set lonPayments='$lonPayments',lonPaymentNo='$lonPaymentNo',
						lonCurbal='$lonCurbal',lonLastPay='$lonLastPay',lonStat='$lonStat' 
						WHERE (tblEmpLoans.compCode = '" . $this->session['company_code'] . "') 
						AND (lonTypeCd='$lonCd')
						AND (tblEmpLoans.empNo = '$empNo')
						AND (lonRefNo='$lonRefNo')
						AND (tblEmpLoans.lonSked IN (3,".$this->getCutOffPeriod()."))";
		return $this->execQry($qryupdateloans);	
	}
	
	function deleteemploans() {
		$qrycloseloans="Delete from tblEmpLoansDtl 
						where trnGrp='" . $this->session['pay_group'] . "' 
						and trnCat='" . $this->session['pay_category'] . "' 
						AND pdYear = '" . $this->get['pdYear'] . "' 
						AND pdNumber = '" . $this->get['pdNum'] . "'						
						";
		return $this->execQry($qrycloseloans);
	}
	
	function deleteotherdeductions() {
		$qryotherdeductions="Delete from tblDedTranDtl where 
						compCode='" . $this->session['company_code'] . "' 


						and dedStat='A' 
						and payGrp='" . $this->session['pay_group'] . "' 
						and payCat='" . $this->session['pay_category'] . "' 
						and processtag = 'Y'
						and (trnCode in 
							   (SELECT trnCode FROM tblPayTransType 
							   	where trnApply in (3,{$this->getCutOffPeriod()})))	
							";
		return $this->execQry($qryotherdeductions);
	}
	
	function closeotherdeduction() {
		 $qryotherdeductions="Insert into tblDedTranDtlHist 
					(compCode, refNo, empNo, trnCntrlNo, trnCode, trnPriority, 
					trnAmount, ActualAmt, payGrp, payCat, processtag, dedStat,remarks,dedtoAdv)  
					SELECT tblDedTranDtl.compCode, tblDedTranDtl.refNo, tblDedTranDtl.empNo, tblDedTranDtl.trnCntrlNo, 
					tblDedTranDtl.trnCode, tblDedTranDtl.trnPriority, tblDedTranDtl.trnAmount,ActualAmt, tblDedTranDtl.payGrp, 
					tblDedTranDtl.payCat, tblDedTranDtl.processtag,tblDedTranDtl.dedStat, tblDedTranHeader.dedRemarks,dedtoAdv 
					FROM tblDedTranDtl INNER JOIN
                    tblDedTranHeader ON tblDedTranDtl.refNo = tblDedTranHeader.refNo
					WHERE tblDedTranDtl.processtag IN ('Y','P')
						AND tblDedTranDtl.compCode ='{$this->session['company_code']}'
						AND tblDedTranDtl.payGrp = '{$this->session['pay_group']}'
						AND tblDedTranDtl.payCat ='{$this->session['pay_category']}'					
						AND tblDedTranHeader.pdYear='{$this->get['pdYear']}'
					    AND tblDedTranHeader.pdNumber = '{$this->get['pdNum']}'
						AND empNo IN
						(SELECT empNo
							FROM tblEmpMast 
							WHERE compCode = '{$this->session['company_code']}'
							AND empPayGrp = '{$this->session['pay_group']}'
							AND empPayCat = '{$this->session['pay_category']}'
							AND empStat IN ('RG','PR','CN'))
					AND tblDedTranDtl.trnCode IN 
					(SELECT trnCode FROM tblPayTransType where trnApply in (3,{$this->getCutOffPeriod()}))		
							";
							
		return $this->execQry($qryotherdeductions);
	}


	function closeloans($seqNo) {
		$seqNo = ($seqNo!=""?$seqNo:0);
		$qrycloseloans="Insert into tblEmpLoansDtlHist 
						(compCode, empNo, lonTypeCd, lonRefNo, pdYear, pdNumber, trnCat, trnGrp, trnAmountD ,ActualAmt, dedTag, lonLastPay, dedtoAdv) 
						Select 
						 compCode, empNo, lonTypeCd, lonRefNo, pdYear, pdNumber, trnCat, trnGrp, trnAmountD, ActualAmt, dedTag, lonLastPay, dedtoAdv 
						 from tblEmpLoansDtl where seqNo IN ($seqNo)";
		return $this->execQry($qrycloseloans);
	}		
	
	######################################################END OF CLOSING DEDCUTIONS###################################################################
	######################################################BEGIN OF CLOSING EARNINGS#################################################################
	private function getEarnTranDtl(){
		$qryGetTranDtl = "SELECT dtl.compCode, dtl.refNo, dtl.empNo, dtl.trnCntrlNo, dtl.trnCode, dtl.trnAmount, dtl.payGrp, dtl.payCat, dtl.earnStat, dtl.trnTaxCd, hdr.earnRem,hdr.pdYear,hdr.pdNumber
						  FROM tblEarnTranDtl AS dtl LEFT JOIN tblEarnTranHeader AS hdr 
						  ON dtl.compCode = hdr.compCode AND dtl.refNo = hdr.refNo 
						  WHERE dtl.compCode = '{$this->session['company_code']}'
						  AND dtl.payGrp = '{$this->session['pay_group']}'
						  AND dtl.payCat = '{$this->session['pay_category']}'
						  AND dtl.earnStat = 'A'

						  AND (processTag='Y' or processTag='U')
						  AND hdr.pdYear='{$this->get['pdYear']}'
						  AND hdr.pdNumber = '{$this->get['pdNum']}'
						  AND dtl.trnCode IN (
						  						SELECT trnCode FROM tblPayTransType
						  						WHERE compCode = '{$this->session['company_code']}'
						  						AND trnApply IN (".$this->getCutOffPeriod().",3)
						  						AND trnStat = 'A'
						  					 )";
		
		$resGetTranDtl = $this->execQry($qryGetTranDtl);
		return $this->getArrRes($resGetTranDtl);
	}
	
	private function getAllowBrkDwm(){
		 $qryGetAllowBrkDwn = "SELECT brkDwn.compCode,brkDwn.empNo,brkDwn.allowCode,brkDwn.allowAmt,brkDwn.allowSked,brkDwn.allowTaxTag,brkDwn.allowPayTag,brkDwn.allowStart,brkDwn.allowEnd,brkDwn.allowStat,brkDwn.allowSeries,brkDwn.pdYear,brkDwn.pdNumber,
							         emp.empPayGrp,emp.empPayCat,brkDwn.sprtPS, allowTag
							  FROM tblAllowanceBrkDwn as brkDwn LEFT JOIN tblEmpMast as emp
							  ON brkDwn.compCode = emp.compCode AND brkDwn.empNo = emp.empNo
							  WHERE brkDwn.compCode = '{$this->session['company_code']}'
							  AND  emp.empPayGrp = '{$this->session['pay_group']}'
							  AND emp.empPayCat = '{$this->session['pay_category']}'
							  AND brkDwn.pdYear = '{$this->get['pdYear']}'
							  AND brkDwn.pdNumber = '{$this->get['pdNum']}'
							  AND brkDwn.allowSked IN (".$this->getCutOffPeriod().",3)";
		$resGetAllowBrkDwn = $this->execQry($qryGetAllowBrkDwn);
		return $this->getArrRes($resGetAllowBrkDwn);
	}
	
	private function getEanings(){
		$qryGetEarnings = "SELECT ern.compCode,ern.pdYear,ern.pdNumber,ern.empNo,ern.trnCode,ern.trnAmountE,ern.trnTaxCd,
								  emp.empPayGrp,emp.empPayCat, ern.sprtPS
								  FROM tblearnings as ern LEFT JOIN tblEmpMast as emp
								  ON ern.compCode = emp.compCode AND ern.empNo = emp.empNo
								  WHERE ern.compCode = '{$this->session['company_code']}'
								  AND  emp.empPayGrp = '{$this->session['pay_group']}'
								  AND emp.empPayCat = '{$this->session['pay_category']}'
								  AND ern.pdYear = '{$this->get['pdYear']}'
								  AND ern.pdNumber = '{$this->get['pdNum']}'
								  AND ern.trnCode IN (
														SELECT trnCode FROM tblPayTransType
								  						WHERE compCode = '{$this->session['company_code']}'
								  						AND trnStat = 'A'
								  						AND trnCat = 'E'
								  					 )";
		$resGetEarnings = $this->execQry($qryGetEarnings);
		return $this->getArrRes($resGetEarnings);
	}
	
	private function getMtdGov(){
	    $qryGetMtdGov = "SELECT tmp.compCode,tmp.pdYear,tmp.pdMonth,tmp.empNo,tmp.mtdEarnings,tmp.sssEmp,tmp.sssEmplr,tmp.ec,tmp.phicEmp,tmp.phicEmplr,tmp.hdmfEmp,tmp.hdmfEmplr,
							    emp.empPayGrp,emp.empPayCat
						FROM tblMtdGovt as tmp LEFT JOIN tblEmpMast as emp 
						ON tmp.compCode = emp.compCode AND tmp.empNo = emp.empNo
						WHERE tmp.compCode = '{$this->session['company_code']}'
						AND  emp.empPayGrp = '{$this->session['pay_group']}'
						AND emp.empPayCat = '{$this->session['pay_category']}'
						AND tmp.pdYear = '{$this->get['pdYear']}'
						AND tmp.pdMonth = '{$this->get['pdMonth']}'";
		$resGetMtdGov = $this->execQry($qryGetMtdGov);
		return $this->getArrRes($resGetMtdGov);
	}
	
	private function getDeductions(){
		$qryGetDeductions = "SELECT ded.compCode,ded.pdYear,ded.pdNumber,ded.empNo,ded.trnCode,ded.trnAmountD,ded.trnTaxCd,
								  emp.empPayGrp,emp.empPayCat,sprtPS
								  FROM tblDeductions as ded LEFT JOIN tblEmpMast as emp
								  ON ded.compCode = emp.compCode AND ded.empNo = emp.empNo
								  WHERE ded.compCode = '{$this->session['company_code']}'
								  AND  emp.empPayGrp = '{$this->session['pay_group']}'
								  AND emp.empPayCat = '{$this->session['pay_category']}'
								  AND ded.pdYear = '{$this->get['pdYear']}'
								  AND ded.pdNumber = '{$this->get['pdNum']}'
								  AND ded.trnCode IN (
														SELECT trnCode FROM tblPayTransType
								  						WHERE compCode = '{$this->session['company_code']}'
								  						AND trnStat = 'A'
								  						AND trnCat = 'D'
								  						
								  					 )";
		$resGetDeductions = $this->execQry($qryGetDeductions);
		return $this->getArrRes($resGetDeductions);
	}

	private function getYtdData(){
		 $qryGetYtdData = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,
							emp.empPayGrp,emp.empPayCat,ytd.YtdBasic, ytd.sprtAllow,ytd.sprtAdvance,YtdGovDedMinWage,YtdGovDedAbvWage
							FROM tblYtdData as ytd LEFT JOIN tblEmpMast as emp
							ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
							WHERE ytd.compCode = '{$this->session['company_code']}'
						    AND  emp.empPayGrp = '{$this->session['pay_group']}'
						    AND emp.empPayCat = '{$this->session['pay_category']}'
						    AND ytd.pdYear = '{$this->get['pdYear']}'";
		$resGetYtdData = $this->execQry($qryGetYtdData);
		return $this->getArrRes($resGetYtdData);
		
	}
	
	private function checkYtdData($empNo){
		$qryCheckYtdData = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,
							emp.empPayGrp,emp.empPayCat,ytd.YtdBasic,ytd.sprtAdvance,YtdGovDedMinWage,YtdGovDedAbvWage
							FROM tblYtdDataHist as ytd LEFT JOIN tblEmpMast as emp
							ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
							WHERE ytd.compCode = '{$this->session['company_code']}'
						    AND  emp.empPayGrp = '{$this->session['pay_group']}'
						    AND emp.empPayCat = '{$this->session['pay_category']}'
						    AND ytd.pdYear = '{$this->get['pdYear']}'
						    AND ytd.empNo = '{$empNo}'";
		$resCheckYtdData = $this->execQry($qryCheckYtdData);
		
		
		return $this->getRecCount($resCheckYtdData);
	}
	
	private function getDataToYtdDataHist($empNo){
		$qryGetDataToYtdHist = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,ytd.pdNumber,
							emp.empPayGrp,emp.empPayCat,ytd.YtdBasic, ytd.sprtAllow,ytd.sprtAdvance,YtdGovDedMinWage,YtdGovDedAbvWage
							FROM tblYtdDataHist as ytd LEFT JOIN tblEmpMast as emp
							ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
							WHERE ytd.compCode = '{$this->session['company_code']}'
						    AND  emp.empPayGrp = '{$this->session['pay_group']}'
						    AND emp.empPayCat = '{$this->session['pay_category']}'
						    AND ytd.pdYear = '{$this->get['pdYear']}'
						    AND ytd.empNo = '{$empNo}'";
		$resGetDataToYtdHist = $this->execQry($qryGetDataToYtdHist);	
		return 	$this->getSqlAssoc($resGetDataToYtdHist);
	}
	
	private function ClossingPaySummary() {
		 $qryClossingSummary="Insert into tblPayrollSummaryHist 
							(compCode, pdYear, pdNumber, empNo, payGrp, payCat, empLocCode, empBrnCode, empBnkCd, grossEarnings,
							 taxableEarnings,minwage_taxableEarnings, totDeductions,nonTaxAllow, netSalary, taxWitheld, empDivCode, empDepCode,empSecCode,sprtAllow,empBasic,empMinWageTag,empEcola, empTeu,empYtdTaxable,empYtdTax,empYtdGovDed,sprtAllowAdvance,yearEndTax)
							 
							 Select  compCode, pdYear, pdNumber, empNo, payGrp, payCat, empLocCode, empBrnCode, empBnkCd, 
							 grossEarnings, taxableEarnings,minwage_taxableEarnings, totDeductions, nonTaxAllow, netSalary, taxWitheld, empDivCode, 
							 empDepCode, empSecCode,sprtAllow,empBasic,empMinWageTag,empEcola,empTeu,empYtdTaxable,empYtdTax,empYtdGovDed,sprtAllowAdvance,yearEndTax from tblPayrollSummary 
					 		 WHERE compCode = '{$this->session['company_code']}'
							 AND payGrp = '{$this->session['pay_group']}'
							 AND payCat = '{$this->session['pay_category']}'
							 AND pdYear = '{$this->get['pdYear']}'
							 AND pdNumber = '{$this->get['pdNum']}'
							 ";
			return $this->execQry($qryClossingSummary);					 
	}
	
	private function ClosePayPeriod() {
		$pdDateClosed=date("m/d/Y");
		
		$qryClosePayPeriod="Update tblPayPeriod 
							Set 
								pdProcessTag='Y',
								pdProcessDate='$pdDateClosed',
								pdProcessedBy='{$this->session['employee_number']}',
								pdDateClosed = '$pdDateClosed',
								pdClosedBy = '{$this->session['employee_number']}',
								pdStat = 'C'
							 WHERE compCode = '{$this->session['company_code']}'
							 AND payGrp = '{$this->session['pay_group']}'
							 AND payCat = '{$this->session['pay_category']}'
							 AND pdYear = '{$this->get['pdYear']}'
							 AND pdNumber = '{$this->get['pdNum']}'
							";
		return $this->execQry($qryClosePayPeriod);		
	}	
	
	private function getTimesheet(){
		$qryGetTs = "SELECT * FROM tblTimeSheet
					  WHERE (compCode = '{$this->session['company_code']}')
					  AND (tsDate BETWEEN '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}')
					  AND (empPayGrp = '{$this->session['pay_group']}')
					  AND (empPayCat = '{$this->session['pay_category']}')
					  AND (tsStat = 'A')";
		$resGetTs = $this->execQry($qryGetTs);
		return $this->getArrRes($resGetTs);
	}
	
	private function chkEmpPaySum($empNo)
	{
		$qrychkEmpPaySum = "Select count(*) as cntEmpPaySum
							from tblPayrollSummaryHist
							where (compCode = '".$this->session['company_code']."') and (pdYear = '".$this->get['pdYear']."') 
							and (pdNumber = '".$this->get['pdNum']."') and (empNo = '".$empNo."') and (payGrp = '".$this->session['pay_group']."') 
							and (payCat = '".$this->session['pay_category']."')";
		$rschkEmpPaySum =  $this->execQry($qrychkEmpPaySum);
		$rowchkEmpPaySum =  $this->getSqlAssoc($rschkEmpPaySum);
		$chkPaySum = ($rowchkEmpPaySum["cntEmpPaySum"]>=1?1:0);
		
		return $chkPaySum;
	}
	
	private function getPrevTaxDeducted($empNo,$field)
	{
		$qrygetTaxDeducted = "Select     ".$field."
							  from        tblPrevEmployer
							  where     (compCode = '".$this->session['company_code']."') AND (prevStat = 'A') AND (yearCd = '".$this->get['pdYear']."')
							  and empNo='".$empNo."'";
		$rsgetTaxDeducted = $this->execQry($qrygetTaxDeducted);
		$rowgetTaxDeducted =  $this->getSqlAssoc($rsgetTaxDeducted);
		$TaxMonthDed= ($rowgetTaxDeducted[$field]!=""?$rowgetTaxDeducted[$field]:0);
		return (float) $TaxMonthDed;
	}
	

	private function computeTax()
	{
		$getempwithPrevEmp = "	Select a.empNo as empNo,prevEarnings,prevTaxes,prevStat,a.yearCd,taxPerMonth,taxDeducted,seqNo
								from tblPayrollSummary b
								left join tblPrevEmployer a
								on b.empNo=a.empNo
								where (a.compCode = '".$this->session['company_code']."') AND (prevStat = 'A') AND (yearCd = '".$this->get['pdYear']."')";
	
		$rsempwithPrevEmp  = $this->execQry($getempwithPrevEmp);
		
		if(mysql_num_rows($rsempwithPrevEmp)>=1)
		{
			while($rowempwithPrevEmp = mysql_fetch_array($rsempwithPrevEmp))
			{
				$prevEmpTax = 0;
				
				if(($rowempwithPrevEmp["taxPerMonth"]==0)&&($rowempwithPrevEmp["taxDeducted"]==0))
				{
					$remMonths = 25 - $this->get['pdNum'];
					$taxPerMon = sprintf("%01.2f",($rowempwithPrevEmp["prevTaxes"]/$remMonths));
				}
				else
				{
					$taxPerMon =  $rowempwithPrevEmp["taxPerMonth"];
				}
				
				$qryUpdtblPrevEmplyr .= "Update tblPrevEmployer
										set taxPerMonth = '".$taxPerMon."', taxDeducted=taxDeducted+".$taxPerMon."
										WHERE     (compCode = '".$this->session['company_code']."') AND 
										(prevStat = 'A') AND (yearCd = '".$this->get['pdYear']."') AND empNo = '".$rowempwithPrevEmp["empNo"]."'
										and seqNo='".$rowempwithPrevEmp["seqNo"]."';";
			}
				return $this->execQry($qryUpdtblPrevEmplyr);	
				//echo $qryUpdtblPrevEmplyr."\n";		
		
		}
		else
		{
			return true;
		}
	}
	
	/*Get Unposted Transactions*/
	private function getUnpostedTran()
	{
		$qryUnpostedTran = "Select * from tblUnpostedTran
							where empNo in (Select empNo from tblEmpMast where
							compCode='".$_SESSION["company_code"]."' and empPayCat='".$_SESSION["pay_category"]."' and empPayGrp='".$_SESSION["pay_group"]."' and 
							empStat IN ('RG','PR','CN')
							and compCode='".$_SESSION["company_code"]."'
							and pdNumber ='".$this->get['pdNum']."' and pdYear='".$this->get['pdYear']."')";
		$resUnpostedTran = $this->execQry($qryUnpostedTran);
		return $this->getArrRes($resUnpostedTran);
	}
	
	/*Get Employee Record*/
	private function getEmployeeRestDay()
	{
		$qryEmpRestD = "SELECT *
							FROM tblEmpMast 
							WHERE compCode = '{$this->session['company_code']}'
							AND empPayGrp = '{$this->session['pay_group']}'
							AND empPayCat = '{$this->session['pay_category']}'
							AND empStat IN ('RG','PR','CN')";
		$resEmpRestD = $this->execQry($qryEmpRestD);
		return $this->getArrRes($resEmpRestD);
	}
	
	private function CloseProcGrp() {
		$sqlClose = "Update tblProcGrp set status='H' where compCode='{$this->session['company_code']}' and payGrp='{$this->session['pay_group']}'";
		return $this->execQry($sqlClose);
	}
	
	private function getEmpAddTaxableIncome($empNo)
	{
		$qryAddTaxable = "Select * from tblGov_Tax_Added where empNo='".$empNo."' and monthToDed='".$this->get['pdNum']."' and addStat='N'";
		$resAddTaxable = $this->execQry($qryAddTaxable);
		return  $this->getSqlAssoc($resAddTaxable);
	}
	
	######################################################END OF CLOSING EARNINGS#################################################################	
	public function mainProcCloseRegPay(){
		
		$resupdateloans = $this->loanslist();
		$lastpaydate = $this->getpayrolldate();
		$seqNo = "";
		$Trns = $this->beginTran();
		
		
		$qryUpdateEarnHeader = "Update tblEarnTranHeader set earnStat='P' where compCode='".$_SESSION["company_code"]."'
						and earnStat='A' and pdYear='".$this->get['pdYear']."' and pdNumber='".$this->get['pdNum']."'
						and refNo  in (Select refNo from tblEarnTranDtl where compCode='".$_SESSION["company_code"]."'
						and payGrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."' and 
						earnStat='A' and (processTag='Y' or processTag='U'))";
						
		if($Trns){
				$Trns = $this->execQry($qryUpdateEarnHeader);
		}
		/*Set EarnStat='P' if All the Transacations (Other Earnings) under that Certain Reference*/
		
		foreach ((array)$this->getEarnTranDtl() as $earnTrnDtlVal){
			$qryToEarnTrnDtlHist = "INSERT INTO tblEarnTranDtlHist(compCode,refNo,empNo,trnCntrlNo,trnCode,trnAmount,payGrp,payCat,earnStat,trnTaxCd,earnRem,pdYear,pdNumber)
															VALUES('{$earnTrnDtlVal['compCode']}',
															       '{$earnTrnDtlVal['refNo']}',
															       '{$earnTrnDtlVal['empNo']}',
															       '{$earnTrnDtlVal['trnCntrlNo']}',
															       '{$earnTrnDtlVal['trnCode']}',
															       '{$earnTrnDtlVal['trnAmount']}',
															       '{$earnTrnDtlVal['payGrp']}',
															       '{$earnTrnDtlVal['payCat']}',
															       '{$earnTrnDtlVal['earnStat']}',
															       '{$earnTrnDtlVal['trnTaxCd']}',
															       '{$earnTrnDtlVal['earnRem']}',
															       '{$earnTrnDtlVal['pdYear']}',
															       '{$earnTrnDtlVal['pdNumber']}')";
			if($Trns){
				$Trns = $this->execQry($qryToEarnTrnDtlHist);
			}	
		}
		
		$qryDeleEarnTrnDtl = "DELETE FROM tblEarnTranDtl 
							  WHERE compCode = '{$this->session['company_code']}'
							  AND PayGrp = '{$this->session['pay_group']}'
						  	  AND PayCat = '{$this->session['pay_category']}'
						      AND earnStat = 'A'
						      AND trnCode IN (
												SELECT trnCode from tblPayTransType
						  						WHERE compCode = '{$this->session['company_code']}'
						  						AND trnApply in ('".$this->getCutOffPeriod()."','3')
						  						AND trnStat = 'A'						      
						      				 )
						     AND refNo IN (SELECT refNo FROM tblEarnTranHeader
						     			   WHERE compCode = '{$this->session['company_code']}'
						     			   AND earnStat = 'P'
										   AND pdYear = '{$this->get['pdYear']}'
										   AND pdNumber = '{$this->get['pdNum']}')";
		if($Trns){
			$Trns = $this->execQry($qryDeleEarnTrnDtl);
		}		
		
		/*$qryDeltblEarnHdr = "DELETE FROM tblEarnTranHeader WHERE 
								refNo in 
								(
									SELECT refNo from tblEarnTranDtl 
									WHERE compCode = '{$this->session['company_code']}'
									AND PayGrp = '{$this->session['pay_group']}'
									AND PayCat = '{$this->session['pay_category']}'
									AND earnStat = 'A'
									AND pdYear = '{$this->get['pdYear']}'
									AND pdNumber = '{$this->get['pdNum']}'
						      		AND trnCode IN 
										(
											SELECT trnCode from tblPayTransType
											WHERE compCode = '{$this->session['company_code']}'
											AND trnApply in ('".$this->getCutOffPeriod()."','3')
											AND trnStat = 'A'						      
						      			)
								)";
		
		if($Trns)
		{
			$Trns = $this->execQry($qryDeltblEarnHdr);
		}*/
		
		foreach ((array)$this->getAllowBrkDwm() as $allowVal){
			$qryToAllowBrkDwnHst = "INSERT INTO tblAllowanceBrkDwnHst(compCode,empNo,allowCode,allowAmt,allowSked,allowTaxTag,allowPayTag,allowStart,allowEnd,allowStat,pdYear,pdNumber,sprtPS,allowTag)
									VALUES('{$allowVal['compCode']}',
									       '{$allowVal['empNo']}',
									       '{$allowVal['allowCode']}',
									       '{$allowVal['allowAmt']}',
									       '{$allowVal['allowSked']}',
									       '{$allowVal['allowTaxTag']}',
									       '{$allowVal['allowPayTag']}',
									       '".$this->dateFormat($allowVal['allowStart'])."',
									       '".$this->dateFormat($allowVal['allowEnd'])."',
									       '{$allowVal['allowStat']}',
									       '{$allowVal['pdYear']}',
									       '{$allowVal['pdNumber']}',
										   '{$allowVal['sprtPS']}',
										   '{$allowVal['allowTag']}')";
			if($Trns){
				$Trns = $this->execQry($qryToAllowBrkDwnHst);
			}	
		}
		
		$qryDeltblEmpLoans = "DELETE FROM tblAllowance WHERE     
							 empNo IN
                          			(
										SELECT empNo FROM tblEmpMast WHERE compCode = '".$this->session['company_code']."'
										AND empPayGrp = '".$this->session['pay_group']."'
										AND empPayCat = '".$this->session['pay_category']."'
										AND empStat IN ('RG','PR','CN')
							 		) 
							 AND compCode = '".$this->session['company_code']."' AND allowPayTag = 'T' AND 
							 allowEnd BETWEEN '".$this->get['dtFrm']."' AND '".$this->get['dtTo']."' AND allowStat = 'A'";
		
		if($Trns)
		{
			$Trns = $this->execQry($qryDeltblEmpLoans);
		}
		
		$qryDeleAllowBrkDwn = "DELETE FROM tblAllowanceBrkDwn 
								WHERE compCode = '{$this->session['company_code']}'
								AND empNo IN (
												SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
												AND empPayGrp = '{$this->session['pay_group']}'
												AND empPayCat = '{$this->session['pay_category']}'
												AND empStat IN ('RG','PR','CN')
											  )
								AND allowSked IN (".$this->getCutOffPeriod().",3)";
		
		if($Trns){
			$Trns = $this->execQry($qryDeleAllowBrkDwn);
		}	

		foreach ((array)$this->getEanings() as $earnVal){
			$qryToEarningsHist = "INSERT INTO tblEarningsHist
								  (compCode,pdYear,pdNumber,empNo,trnCode,trnAmountE,trnTaxCd,sprtPS)
								  VALUES
								  ('{$earnVal['compCode']}',
								   '{$earnVal['pdYear']}',
								   '{$earnVal['pdNumber']}',
								   '{$earnVal['empNo']}',
								   '{$earnVal['trnCode']}',
								   '{$earnVal['trnAmountE']}',
								   '{$earnVal['trnTaxCd']}',
								   '{$earnVal['sprtPS']}')";
			if($Trns){
				$Trns = $this->execQry($qryToEarningsHist);
			}
		}
		
		$qryDeleEan = "DELETE FROM tblEarnings 
		               WHERE compCode = '{$this->session['company_code']}'
					   AND empNo IN (
										SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
										AND empPayGrp = '{$this->session['pay_group']}'
										AND empPayCat = '{$this->session['pay_category']}'
										AND empStat IN ('RG','PR','CN')
					   			    )
					   AND trnCode IN (
										SELECT trnCode from tblPayTransType
				  						WHERE compCode = '{$this->session['company_code']}'
				  						AND trnStat = 'A'	
				  						AND trnCat = 'E'
					   				  )";
		
		if($Trns){
			$Trns = $this->execQry($qryDeleEan);
		}

		if($this->getCutOffPeriod() == 2){
			foreach ((array)$this->getMtdGov() as $mtdGovVal){
				
				$qryToMtvGovtHist = "INSERT INTO tblMtdGovtHist
									 (compCode,pdYear,pdMonth,empNo,mtdEarnings,sssEmp,sssEmplr,ec,phicEmp,phicEmplr,hdmfEmp,hdmfEmplr)
									 VALUES('{$mtdGovVal['compCode']}',
									        '{$mtdGovVal['pdYear']}',
									        '{$mtdGovVal['pdMonth']}',
									        '{$mtdGovVal['empNo']}',
									        '{$mtdGovVal['mtdEarnings']}',
									        '{$mtdGovVal['sssEmp']}',
									        '{$mtdGovVal['sssEmplr']}',
									        '{$mtdGovVal['ec']}',
									        '{$mtdGovVal['phicEmp']}',
									        '{$mtdGovVal['phicEmplr']}',
									        '{$mtdGovVal['hdmfEmp']}',
									        '{$mtdGovVal['hdmfEmplr']}')";
				if($Trns){
					$Trns = $this->execQry($qryToMtvGovtHist);
				}				
			}
		}
		
		$qryDeleMtdGovt = "DELETE FROM tblMtdGovt
						   WHERE compCode = '{$this->session['company_code']}'
						   AND pdYear = '{$this->get['pdYear']}'
						   AND pdMonth = '{$this->get['pdMonth']}'
						   AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
						                )"; 
		if($Trns){
			$Trns = $this->execQry($qryDeleMtdGovt);
		}
		////////////////////////////////////////////////////////////////////////////////////////////////////////

		foreach ($resupdateloans as $rsloans) {
			$lonStat="O";
			$lastinfo    = $this->getlastinfoloans($rsloans['empNo'],$rsloans['lonTypeCd'],$rsloans['lonRefNo']);
			$lonPayments = (float)$lastinfo['lonPayments'] + (float)$rsloans['ActualAmt'];
			$lonPaymentNo = (int)$lastinfo['lonPaymentNo']+1;
			$lonCurbal = (float)$lastinfo['lonCurbal'] - (float)$rsloans['ActualAmt'];
			if ($seqNo=="") {
				$seqNo=$rsloans['seqNo'];
			}
			else {
				$seqNo .="," . $rsloans['seqNo'];
			}
			
			if ((float)$lonPayments==(float)$lastinfo['lonWidInterst']) {
				$lonStat="C";
			}
			if($Trns){
				$Trns = $this->updateemploan($rsloans['empNo'],$rsloans['lonTypeCd'],$lonPayments,$lonPaymentNo,$lonCurbal,$lastpaydate['pdPayable'],$lonStat,$rsloans['lonRefNo']);
			}
		}
		
		if($Trns){
			$Trns = $this->closeloans($seqNo);
		}


		if($Trns){
			$Trns =$this->closeotherdeduction();
		}
		
		$qryUpdateDedHeader = "Update tblDedTranHeader set dedStat='P' where compCode='".$_SESSION["company_code"]."'
							and dedStat='A' and pdYear='".$this->get['pdYear']."' and pdNumber='".$this->get['pdNum']."'
							and refNo in (Select refNo from tblDedTranDtl where compCode='".$_SESSION["company_code"]."'
							and payGrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."' and 
							dedStat='A' and (processTag='Y' or processTag='P'))";
		if($Trns){
				$Trns = $this->execQry($qryUpdateDedHeader);
		}
		
		
		/*$qryDeltblDedHdr = "DELETE FROM tblDedTranHeader WHERE 
								refNo in 
								(
									SELECT refNo from tblDedTranDtl 
									WHERE compCode = '{$this->session['company_code']}'
									AND PayGrp = '{$this->session['pay_group']}'
									AND PayCat = '{$this->session['pay_category']}'
									AND dedStat = 'A'
						      		AND trnCode IN 
										(
											SELECT trnCode from tblPayTransType
											WHERE compCode = '{$this->session['company_code']}'
											AND trnApply in ('".$this->getCutOffPeriod()."','3')
											AND trnStat = 'A'						      
						      			)
								)";
		
		if($Trns)
		{
			$Trns = $this->execQry($qryDeltblDedHdr);
		}*/
		
		
		if($Trns){
			$Trns = $this->deleteotherdeductions();
		}
		if($Trns){
			$Trns = $this->deleteemploans();
		}		
		
		foreach ((array)$this->getDeductions() as $deductVal){
			  $qryToDeductionsHist = "INSERT INTO tblDeductionsHist
								    (compCode,pdYear,pdNumber,empNo,trnCode,trnAmountD,trnTaxCd,sprtPS)
								    VALUES('{$deductVal['compCode']}',
								           '{$deductVal['pdYear']}',
								           '{$deductVal['pdNumber']}',
								           '{$deductVal['empNo']}',
								           '{$deductVal['trnCode']}',
								           '{$deductVal['trnAmountD']}',
								           '{$deductVal['trnTaxCd']}',
										   '{$deductVal['sprtPS']}')
										   ";
			if($Trns){
				$Trns = $this->execQry($qryToDeductionsHist);
			}
		}
		$qryDeleDeductions = "DELETE FROM tblDeductions 
							  WHERE compCode = '{$this->session['company_code']}'
							 AND pdYear = '{$this->get['pdYear']}'
						     AND pdNumber = '{$this->get['pdNum']}'
						     AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
						     			  )";
		if($Trns){
			$Trns = $this->execQry($qryDeleDeductions);
		}
		
		$qryDelTblDedTranHdr = "";
		
		foreach ((array)$this->getYtdData() as $ytdVal){
			
			$arrGetAddTaxable = $this->getEmpAddTaxableIncome($ytdVal['empNo']);
			if($arrGetAddTaxable["amountToDed"]!=0)
			{
				
				$qryUpdatePaySum_Tax = "Update tblPayrollSummaryHist set taxableEarnings=taxableEarnings+".$arrGetAddTaxable["amountToDed"]." where empNo='".$ytdVal['empNo']."'
										and pdNumber='".$arrGetAddTaxable["monthPeriodDate"]."' and pdYear='".$this->get['pdYear']."'";
				if($Trns){
						$Trns = $this->execQry($qryUpdatePaySum_Tax);
				}
				
				$qryUpdateGov_Tax = "Update tblGov_Tax_Added set addStat='Y' where empNo='".$ytdVal['empNo']."'
										and monthPeriodDate='".$arrGetAddTaxable["monthPeriodDate"]."' and monthToDed='".$this->get['pdNum']."'";
				
				if($Trns){
						$Trns = $this->execQry($qryUpdateGov_Tax);
				}
			}
			
			if($this->checkYtdData($ytdVal['empNo']) > 0){
				
					$dataToYtdHist = $this->getDataToYtdDataHist($ytdVal['empNo']);
					$newYtdGross       = (float)$dataToYtdHist['YtdGross'] + (float)$ytdVal['YtdGross'];
					//echo $newYtdGross."==>>".(float)$dataToYtdHist['YtdGross']."===>>".(float)$ytdVal['YtdGross']."\n";
					$newYtdTaxable     = (float)$dataToYtdHist['YtdTaxable'] + (float)$ytdVal['YtdTaxable'];
					$newYtdGovDed      = (float)$dataToYtdHist['YtdGovDed'] + (float)$ytdVal['YtdGovDed'];
					$newYtdTax         = (float)$dataToYtdHist['YtdTax'] + (float)$ytdVal['YtdTax'];
					$newYtdNonTaxAllow = (float)$dataToYtdHist['YtdNonTaxAllow'] + (float)$ytdVal['YtdNonTaxAllow'];
					$newYtd13NBonus    = (float)$dataToYtdHist['Ytd13NBonus'] + (float)$ytdVal['Ytd13NBonus'];
					$newYtdTx13NBonus  = (float)$dataToYtdHist['YtdTx13NBonus'] + (float)$ytdVal['YtdTx13NBonus'];
					$newYtdBasic	   = (float)$dataToYtdHist['YtdBasic'] + (float)$ytdVal['YtdBasic'];
					$newsprtPs	   	   = (float)$dataToYtdHist['sprtAllow'] + (float)$ytdVal['sprtAllow'];
					$sprtAdvance	   = (float)$dataToYtdHist['sprtAdvance'] + (float)$ytdVal['sprtAdvance'];
					$YtdGovDedMinWage	   	   = (float)$dataToYtdHist['YtdGovDedMinWage'] + (float)$ytdVal['YtdGovDedMinWage'];
					$YtdGovDedAbvWage	   = (float)$dataToYtdHist['YtdGovDedAbvWage'] + (float)$ytdVal['YtdGovDedAbvWage'];
					
					$qryUpdateYtdDataHist = "UPDATE tblYtdDataHist SET 
											 YtdGross = '".sprintf("%01.2f",$newYtdGross)."',
											 YtdTaxable = '".sprintf("%01.2f",$newYtdTaxable)."',
											 YtdGovDed = '".sprintf("%01.2f",$newYtdGovDed)."',
											 YtdTax = '".sprintf("%01.2f",$newYtdTax)."',
											 YtdNonTaxAllow = '".sprintf("%01.2f",$newYtdNonTaxAllow)."',
											 Ytd13NBonus = '".sprintf("%01.2f",$newYtd13NBonus)."',
											 YtdTx13NBonus = '".sprintf("%01.2f",$newYtdTx13NBonus)."',
											 payGrp='".$ytdVal['payGrp']."',
											 pdNumber='".$this->get['pdNum']."',
											 YtdBasic='".sprintf("%01.2f",$newYtdBasic)."',
											 sprtAllow='".sprintf("%01.2f",$newsprtPs)."',
											 sprtAdvance='". round($sprtAdvance,2) ."',
											 YtdGovDedMinWage='".$YtdGovDedMinWage."',
											 YtdGovDedAbvWage='".$YtdGovDedAbvWage."'
											 where compCode='".$this->session['company_code']."' 
											 and empNo='".$ytdVal['empNo']."'
											 and pdYear='".$this->get['pdYear']."' ";
				
					if($Trns){
						$Trns = $this->execQry($qryUpdateYtdDataHist);
					}	
			}
			else {
				 $qryToYtdDataHist = "INSERT tblYtdDataHist(compCode,pdYear,empNo,YtdGross,YtdTaxable,
				                                           YtdGovDed,YtdTax,YtdNonTaxAllow,Ytd13NBonus,YtdTx13NBonus,
				                                           payGrp,pdNumber,sprtAllow,YtdBasic,sprtAdvance,YtdGovDedMinWage,YtdGovDedAbvWage)
				                                      VALUES('{$ytdVal['compCode']}',
				                                             '{$ytdVal['pdYear']}',
				                                             '{$ytdVal['empNo']}',
				                                             '{$ytdVal['YtdGross']}',
				                                             '{$ytdVal['YtdTaxable']}',
				                                             '{$ytdVal['YtdGovDed']}',
				                                             '{$ytdVal['YtdTax']}',
				                                             '{$ytdVal['YtdNonTaxAllow']}',
				                                             '{$ytdVal['Ytd13NBonus']}',
				                                             '{$ytdVal['YtdTx13NBonus']}',
				                                             '{$ytdVal['payGrp']}',
				                                             '{$ytdVal['pdNumber']}',
															 '{$ytdVal['sprtAllow']}',
															 '{$ytdVal['YtdBasic']}',
															 '{$ytdVal['sprtAdvance']}',
															 '{$ytdVal['YtdGovDedMinWage']}',
															 '{$ytdVal['YtdGovDedAbvWage']}'
															 )";
				if($Trns){
					$Trns = $this->execQry($qryToYtdDataHist);
				}	
			}	
			unset($newYtdGross,$newYtdTaxable,$newYtdGovDed,$newYtdTax,$newYtdNonTaxAllow,$newYtd13NBonus,$newYtdTx13NBonus,$newYtdBasic,$newsprtPs,$sprtAdvance,$YtdGovDedMinWage,$YtdGovDedAbvWage);
		}

		$qryDeleYtdData = "DELETE FROM tblYtdData 
							 WHERE compCode = '{$this->session['company_code']}'
							 AND pdYear = '{$this->get['pdYear']}'
						     AND pdNumber = '{$this->get['pdNum']}'
						     AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
						     			  )";
		if($Trns){
			$Trns = $this->execQry($qryDeleYtdData);
		}
		
		
		
		
		/*Update tblPrevEmployer*/
		if($Trns){
			$Trns = $this->computeTax();
		}
		
		
		if($Trns){
			$Trns = $this->ClossingPaySummary();
		}
		
		$qryDelPaySumm = "Delete from tblPayrollSummary 
					 		 WHERE compCode = '{$this->session['company_code']}'
							 AND payGrp = '{$this->session['pay_group']}'
							 AND payCat = '{$this->session['pay_category']}'
							 AND pdYear = '{$this->get['pdYear']}'
							 AND pdNumber = '{$this->get['pdNum']}'";
							 
		if($Trns){
			$Trns =$this->execQry($qryDelPaySumm );
		}
						 
		if($Trns){
			$Trns = $this->ClosePayPeriod();
		}

		
		$qryDeleTsHist = "DELETE FROM tblTimeSheetHist
					  WHERE (compCode = '{$this->session['company_code']}')
					  AND (tsDate BETWEEN '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}')
					  AND empNo in (Select empNo from tblTimesheet)
					  ";
		if($Trns){
			$Trns = $this->execQry($qryDeleTsHist);
		}
		

		foreach ((array)$this->getTimesheet() as $tsVal){
			 $qryToTsHist = "INSERT INTO tblTimeSheetHist(compCode,empNo,tsDate,
														 hrsAbsent,hrsTardy,hrsUt,
														 hrsOtLe8,hrsOtGt8,hrsNdLe8,
														 hrsNdGt8,tsRemarks,empPayGrp,
														 empPayCat,dayType,amtAbsent,
														 amtTardy,amtUt,amtOtLe8,
														 amtOtGt8,amtNdLe8,amtNdGt8,
														 tsRem,tsStat,trnOtLe8,trnOtGt8,trnNdLe8,trnNdGt8)
													VALUES('{$tsVal['compCode']}','{$tsVal['empNo']}','".$this->dateFormat($tsVal['tsDate'])."',
													       '{$tsVal['hrsAbsent']}','{$tsVal['hrsTardy']}','{$tsVal['hrsUt']}',
													       '{$tsVal['hrsOtLe8']}','{$tsVal['hrsOtGt8']}','{$tsVal['hrsNdLe8']}',
													       '{$tsVal['hrsNdGt8']}','{$tsVal['tsRemarks']}','{$tsVal['empPayGrp']}',
													       '{$tsVal['empPayCat']}','{$tsVal['dayType']}','{$tsVal['amtAbsent']}',
													       '{$tsVal['amtTardy']}','{$tsVal['amtUt']}','{$tsVal['amtOtLe8']}',
													       '{$tsVal['amtOtGt8']}','{$tsVal['amtNdLe8']}','{$tsVal['amtNdGt8']}',
													       '{$tsVal['tsRem']}','{$tsVal['tsStat']}','{$tsVal['trnOtLe8']}','{$tsVal['trnOtGt8']}','{$tsVal['trnNdLe8']}','{$tsVal['trnNdGt8']}')";		
			if($Trns){
				$Trns = $this->execQry($qryToTsHist);
			}	
		}
	
		$qryDeleTs = "DELETE FROM tblTimeSheet
					  WHERE (compCode = '{$this->session['company_code']}')
					  AND (tsDate BETWEEN '{$this->get['dtFrm']}' AND '{$this->get['dtTo']}')
					  AND (empPayGrp = '{$this->session['pay_group']}')
					  AND (empPayCat = '{$this->session['pay_category']}')
					  AND (tsStat = 'A') ";
		if($Trns){
			$Trns = $this->execQry($qryDeleTs);
		}
		
		/*Unposted Transactions*/
		foreach ((array)$this->getUnpostedTran() as $Unposted_Val){
			$writeToTblUnpostedTran = "INSERT INTO tblUnpostedTranHist
							  			(compCode,empNo,trnCode,trnAmt,trnActualAmt,pdNumber,pdYear,trnCntrlNo,dateAdded,refNo)
							  			VALUES('".$Unposted_Val["compCode"]."','".$Unposted_Val["empNo"]."','".$Unposted_Val["trnCode"]."','".sprintf("%01.2f",$Unposted_Val["trnAmt"])."','".sprintf("%01.2f",$Unposted_Val["trnActualAmt"])."','".$Unposted_Val["pdNumber"]."','".$Unposted_Val["pdYear"]."','".$Unposted_Val["trnCntrlNo"]."','".$Unposted_Val["dateAdded"]."','".$Unposted_Val["refNo"]."')";
			
			if($Trns){
				$Trns = $this->execQry($writeToTblUnpostedTran);
			}	
		}
		
		$qryDelUnpost =  "Delete from tblUnpostedTran
							where empNo in (Select empNo from tblEmpMast where
							compCode='".$_SESSION["company_code"]."' and empPayCat='".$_SESSION["pay_category"]."' and empPayGrp='".$_SESSION["pay_group"]."' and 
							empStat IN ('RG','PR','CN')
							and compCode='".$_SESSION["company_code"]."'
							and pdNumber ='".$this->get['pdNum']."' and pdYear='".$this->get['pdYear']."')";
		if($Trns){
			$Trns = $this->execQry($qryDelUnpost);
		}
	
		/*End of Unposted Tran*/
		
		
		/*Get all Employee RestDay*/
		foreach ((array)$this->getEmployeeRestDay() as $GetEmpRD_Val){
			if($GetEmpRD_Val["empRestDay"]!=""){
				$writeTblEmpRDBckUp = "Insert into tblEmpRestDayBckUp(compCode,pdYear,pdNumber,empNo, empRestDay) values ('".$_SESSION["company_code"]."','".$this->get['pdYear']."','".$this->get['pdNum']."','".$GetEmpRD_Val["empNo"]."','".$GetEmpRD_Val["empRestDay"]."')";
				if($Trns){
					$Trns = $this->execQry($writeTblEmpRDBckUp);
				}
				
				$updateTblEmpRDBckUp = "Update tblEmpMast set empRestDay='04/17/2011, 04/24/2011, 01/01/2011' where empNo='".$GetEmpRD_Val["empNo"]."'";
				if($Trns){
					$Trns = $this->execQry($updateTblEmpRDBckUp);
				}	
			}
		}
		if ($this->session['pay_category'] == 3) {
			$sqlEmpTaxAdj = "Update tblEmptax set stat='C' where empNo IN 
									(SELECT tblEmpTax.empNo FROM  tblEmpTax INNER JOIN
									  tblPayrollSummaryHist ON tblEmpTax.compCode = tblPayrollSummaryHist.compCode AND tblEmpTax.empNo = tblPayrollSummaryHist.empNo AND 
									  tblPayrollSummaryHist.pdNumber = '".$this->get['pdNum']."' AND tblPayrollSummaryHist.pdYear = '".$this->get['pdYear']."' AND tblEmpTax.wtax > tblPayrollSummaryHist.taxWitheld AND tblPayrollSummaryHist.taxWitheld > 0
									  Where tblEmpTax.compCode='".$_SESSION["company_code"]."')";
			if($Trns){
				$Trns = $this->execQry($sqlEmpTaxAdj);
			}
		}
		/*End of Employee RestDay*/
		
		
		if($Trns){
			$Trns = $this->CloseProcGrp();
		}		
		if($Trns){
			$Trns = $this->OpenPayPeriod();
		}

		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}
	}	
}

?>