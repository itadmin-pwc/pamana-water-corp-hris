<?
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
				 tblEmpLoansDtl.trnAmountD,tblEmpLoansDtl.dedTag, tblEmpLoansDtl.lonLastPay,tblEmpLoansDtl.seqNo
				 FROM tblEmpLoansDtl INNER JOIN
                      tblEmpLoans ON tblEmpLoansDtl.compCode = tblEmpLoans.compCode AND 
                      tblEmpLoansDtl.lonRefNo = tblEmpLoans.lonRefNo 
					  AND tblEmpLoansDtl.lonTypeCd = tblEmpLoans.lonTypeCd 
					  AND tblEmpLoansDtl.empNo = tblEmpLoans.empNo
				WHERE  tblEmpLoansDtl.dedTag = 'Y' 
				AND tblEmpLoansDtl.empNo IN 
				 (SELECT empNo
							FROM tblEmpMast 
							WHERE compCode = '{$this->session['company_code']}'
							AND empPayGrp = '{$this->session['pay_group']}'
							AND empPayCat = '{$this->session['pay_category']}'
							AND empStat IN ('RG','PR','CN'))
				AND tblEmpLoans.lonSked IN (3,{$this->getCutOffPeriod()})			
							";
		return $this->getArrRes($this->execQry($qrylist));
	}

	function getlastinfoloans($empNo,$lonTypeCd,$lonRefNo){
		$qrylastinfo="Select lonWidInterst,lonPayments,lonPaymentNo,lonCurbal from tblEmpLoans 
					  where (tblEmpLoans.compCode = '" . $this->session['company_code'] . "') 
					  AND (tblEmpLoans.empNo = '$empNo')
					  AND (tblEmpLoans.lonTypeCd = '$lonTypeCd')
					  AND (tblEmpLoans.lonRefNo = '$lonRefNo')
					  AND lonSked IN (3,{$this->getCutOffPeriod()})
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
						AND (tblEmpLoans.lonSked IN (3,{$this->getCutOffPeriod()}))";
		return $this->execQry($qryupdateloans);	
	}
	
	function deleteemploans($seqNo) {
		$qrycloseloans="Delete from tblEmpLoansDtl 
						where  seqNo IN ($seqNo)";
		return $this->execQry($qrycloseloans);
	}
	
	function deleteotherdeductions() {
		$qryotherdeductions="Delete from tblDedTranDtl where processtag='Y' and empNo IN
						(SELECT empNo
							FROM tblEmpMast 
							WHERE compCode = '{$this->session['company_code']}'
							AND empPayGrp = '{$this->session['pay_group']}'
							AND empPayCat = '{$this->session['pay_category']}'
							AND empStat IN ('RG','PR','CN'))
						AND trnCode IN (SELECT trnCode FROM tblPayTransType where trnApply in (3,{$this->getCutOffPeriod()}))	
							";
		return $this->execQry($qryotherdeductions);
	}
	
	function closeotherdeduction() {
		$qryotherdeductions="Insert into tblDedTranDtlHist 
					(compCode, refNo, empNo, trnCntrlNo, trnCode, trnPriority, 
					trnAmount, payGrp, payCat, processtag, dedStat,remarks)  
					SELECT tblDedTranDtl.compCode, tblDedTranDtl.refNo, tblDedTranDtl.empNo, tblDedTranDtl.trnCntrlNo, 
					tblDedTranDtl.trnCode, tblDedTranDtl.trnPriority, tblDedTranDtl.trnAmount, tblDedTranDtl.payGrp, 
					tblDedTranDtl.payCat, tblDedTranDtl.processtag,tblDedTranDtl.dedStat, tblDedTranHeader.dedRemarks 
					FROM tblDedTranDtl INNER JOIN
                    tblDedTranHeader ON tblDedTranDtl.refNo = tblDedTranHeader.refNo
					WHERE tblDedTranDtl.processtag = 'Y' 
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
		$qrycloseloans="Insert into tblEmpLoansDtlHist 
						(compCode, empNo, lonTypeCd, lonRefNo, pdYear, pdNumber, trnCat, trnGrp, trnAmountD, dedTag, lonLastPay) 
						Select 
						 compCode, empNo, lonTypeCd, lonRefNo, pdYear, pdNumber, trnCat, trnGrp, trnAmountD, dedTag, lonLastPay 
						 from tblEmpLoansDtl where seqNo IN ($seqNo)						 
						
						
						 ";
		return $this->execQry($qrycloseloans);
	}		
	
	######################################################END OF CLOSING DEDCUTIONS###################################################################
	######################################################BEGIN OF CLOSING EARNINGS#################################################################
	private function getEarnTranDtl(){
		$qryGetTranDtl = "SELECT dtl.compCode, dtl.refNo, dtl.empNo, dtl.trnCntrlNo, dtl.trnCode, dtl.trnAmount, dtl.payGrp, dtl.payCat, dtl.earnStat, dtl.trnTaxCd, hdr.earnRem
						  FROM tblEarnTranDtl AS dtl LEFT JOIN tblEarnTranHeader AS hdr 
						  ON dtl.compCode = hdr.compCode AND dtl.refNo = hdr.refNo 
						  WHERE dtl.compCode = '{$this->session['company_code']}'
						  AND dtl.payGrp = '{$this->session['pay_group']}'
						  AND dtl.payCat = '{$this->session['pay_category']}'
						  AND dtl.earnStat = 'A'
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
							         emp.empPayGrp,emp.empPayCat
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
								  emp.empPayGrp,emp.empPayCat
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
								  emp.empPayGrp,emp.empPayCat
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
		 $qryGetYtdData = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,ytd.pdNumber,
							emp.empPayGrp,emp.empPayCat 
							FROM tblYtdData as ytd LEFT JOIN tblEmpMast as emp
							ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
							WHERE ytd.compCode = '{$this->session['company_code']}'
						    AND  emp.empPayGrp = '{$this->session['pay_group']}'
						    AND emp.empPayCat = '{$this->session['pay_category']}'
						    AND ytd.pdYear = '{$this->get['pdYear']}'
						    AND ytd.pdNumber = '{$this->get['pdNum']}'";
		$resGetYtdData = $this->execQry($qryGetYtdData);
		return $this->getArrRes($resGetYtdData);
		
	}
	
	private function checkYtdData($empNo){
		$qryCheckYtdData = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,ytd.pdNumber,
							emp.empPayGrp,emp.empPayCat 
							FROM tblYtdData as ytd LEFT JOIN tblEmpMast as emp
							ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
							WHERE ytd.compCode = '{$this->session['company_code']}'
						    AND  emp.empPayGrp = '{$this->session['pay_group']}'
						    AND emp.empPayCat = '{$this->session['pay_category']}'
						    AND ytd.pdYear = '{$this->get['pdYear']}'
						    AND ytd.pdNumber = '{$this->get['pdNum']}'
						    AND ytd.empNo = '{$empNo}'";
		$resCheckYtdData = $this->execQry($qryCheckYtdData);
		return $this->getRecCount($resCheckYtdData);
	}

	
	private function getDataToYtdDataHist($empNo){
		$qryGetDataToYtdHist = "SELECT ytd.compCode,ytd.pdYear,ytd.empNo,ytd.YtdGross,ytd.YtdTaxable,ytd.YtdGovDed,ytd.YtdTax,ytd.YtdNonTaxAllow,ytd.Ytd13NBonus,ytd.YtdTx13NBonus,ytd.payGrp,ytd.pdNumber,
							emp.empPayGrp,emp.empPayCat 
							FROM tblYtdData as ytd LEFT JOIN tblEmpMast as emp
							ON ytd.compCode = emp.compCode AND ytd.empNo = emp.empNo
							WHERE ytd.compCode = '{$this->session['company_code']}'
						    AND  emp.empPayGrp = '{$this->session['pay_group']}'
						    AND emp.empPayCat = '{$this->session['pay_category']}'
						    AND ytd.pdYear = '{$this->get['pdYear']}'
						    AND ytd.pdNumber = '{$this->get['pdNum']}'
						    AND ytd.empNo = '{$empNo}'";
		$resGetDataToYtdHist = $this->execQry($qryGetDataToYtdHist);	
		return 	$this->getSqlAssoc($resGetDataToYtdHist);
	}
	
	private function ClossingPaySummary() {
		$qryClossingSummary="Insert into tblPayrollSummaryHist 
							(compCode, pdYear, pdNumber, empNo, payGrp, payCat, empLocCode, empBrnCode, empBnkCd, grossEarnings,
							 taxableEarnings, totDeductions,nonTaxAllow, netSalary, taxWitheld, empDivCode, empDepCode,empSecCode)
							 
							 Select  compCode, pdYear, pdNumber, empNo, payGrp, payCat, empLocCode, empBrnCode, empBnkCd, 
							 grossEarnings, taxableEarnings, totDeductions, nonTaxAllow, netSalary, taxWitheld, empDivCode, 
							 empDepCode, empSecCode from tblPayrollSummary 
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
							Set pdStat = 'H',
								pdClosedBy = '{$this->session['empNo']}',
								pdDateClosed = '$pdDateClosed'
							WHERE compCode = '{$this->session['company_code']}'
							 AND payGrp = '{$this->session['pay_group']}'
							 AND payCat = '{$this->session['pay_category']}'
							 AND pdYear = '{$this->get['pdYear']}'
							 AND pdNumber = '{$this->get['pdNum']}'
							";
	}
	
	######################################################END OF CLOSING EARNINGS#################################################################	
	public function mainProcCloseRegPay(){
		
		$resupdateloans = $this->loanslist();
		$lastpaydate = $this->getpayrolldate();
		$seqNo = "";
		$Trns = $this->beginTran();
		
		foreach ((array)$this->getEarnTranDtl() as $earnTrnDtlVal){
			$qryToEarnTrnDtlHist = "INSERT INTO tblEarnTranDtlHist(compCode,refNo,empNo,trnCntrlNo,trnCode,trnAmount,payGrp,payCat,
																   earnStat,trnTaxCd,earnRem)
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
															       '{$earnTrnDtlVal['earnRem']}')";
			if($Trns){
				$Trns = $this->execQry($qryToEarnTrnDtlHist);
			}			
		}
		
		$qryDeleEarnTrnDtl = "DELETE FROM tblEarnTranDtl 
							  WHERE compCode = '{$this->session['company_code']}'
							  AND empPayGrp = '{$this->session['pay_group']}'
						  	  AND empPayCat = '{$this->session['pay_category']}'
						      AND earnStat = 'A'
						      AND trnCode IN (
												SELECT trnCode 
						  						WHERE compCode = '{$this->session['company_code']}'
						  						AND trnApply = '".$this->getCutOffPeriod()."'
						  						AND trnStat = 'A'						      
						      				 )";
/*		if($Trns){
			$Trns = $this->execQry($qryDeleEarnTrnDtl);
		}	*/	

		foreach ((array)$this->getAllowBrkDwm() as $allowVal){
			$qryToAllowBrkDwnHst = "INSERT INTO tblAllowanceBrkDwnHst(compCode,empNo,allowCode,allowAmt,allowSked,allowTaxTag,allowPayTag,allowStart,allowEnd,allowStat,pdYear,pdNumber)
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
									       '{$allowVal['pdNumber']}')";
			if($Trns){
				$Trns = $this->execQry($qryToAllowBrkDwnHst);
			}	
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
/*		if($Trns){
			$Trns = $this->execQry($qryDeleAllowBrkDwn);
		}	*/

		foreach ((array)$this->getEanings() as $earnVal){
			$qryToEarningsHist = "INSERT INTO tblearningsHist
								  (compCode,pdYear,pdNumber,empNo,trnCode,trnAmountE,trnTaxCd)
								  VALUES
								  ('{$earnVal['compCode']}',
								   '{$earnVal['pdYear']}',
								   '{$earnVal['pdNumber']}',
								   '{$earnVal['empNo']}',
								   '{$earnVal['trnCode']}',
								   '{$earnVal['trnAmountE']}',
								   '{$earnVal['trnTaxCd']}')";
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
										SELECT trnCode 
				  						WHERE compCode = '{$this->session['company_code']}'
				  						AND trnStat = 'A'	
				  						AND trnCat = 'E'
					   				  )";
/*		if($Trns){
			$Trns = $this->execQry($qryDeleEan);
		}*/

		if($this->getCutOffPeriod() == 1){
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
		
		$qryDeleMtdGovt = "DELETE FROM mtdGovt 
						   WHERE compCode = '{$this->session['company_code']}'
						   AND pdYear = '{$this->get['pdYear']}'
						   AND pdMonth = '{$this->get['pdMonth']}'
						   AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
						                )"; 
/*		if($Trns){
			$Trns = $this->execQry($qryDeleMtdGovt);
		}*/
		////////////////////////////////////////////////////////////////////////////////////////////////////////

		foreach ($resupdateloans as $rsloans) {
			$lonStat="O";
			$lastinfo    = $this->getlastinfoloans($rsloans['empNo'],$rsloans['lonTypeCd'],$rsloans['lonRefNo']);
			$lonPayments = (float)$lastinfo['lonPayments'] + (float)$rsloans['trnAmountD'];
			$lonPaymentNo = (int)$lastinfo['lonPaymentNo']+1;
			$lonCurbal = (float)$lastinfo['lonCurbal'] - (float)$rsloans['trnAmountD'];
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
		
		foreach ((array)$this->getDeductions() as $deductVal){
			 $qryToDeductionsHist = "INSERT INTO tblDeductionsHist
								    (compCode,pdYear,pdNumber,empNo,trnCode,trnAmountD,trnTaxCd)
								    VALUES('{$deductVal['compCode']}',
								           '{$deductVal['pdYear']}',
								           '{$deductVal['pdNumber']}',
								           '{$deductVal['empNo']}',
								           '{$deductVal['trnCode']}',
								           '{$deductVal['trnAmountD']}',
								           '{$deductVal['trnTaxCd']}')";
			if($Trns){
				$Trns = $this->execQry($qryToDeductionsHist);
			}		
		}
		
		$qryDeleDeductions = "DELETE FROM tblDedcutions 
							  WHERE compCode = '{$this->session['company_code']}'
							 AND pdYear = '{$this->get['pdYear']}'
						     AND pdNumber = '{$this->get['pdNum']}'
						     AND empNo IN (
											SELECT empNo FROM tblEmpMast WHERE compCode = '{$this->session['company_code']}'
											AND empPayGrp = '{$this->session['pay_group']}'
											AND empPayCat = '{$this->session['pay_category']}'
											AND empStat IN ('RG','PR','CN')
						     			  )";
/*		if($Trns){
			$Trns = $this->execQry($qryDeleDeductions);
		}*/	
		
		foreach ((array)$this->getYtdData() as $ytdVal){
			
			if($this->checkYtdData($ytdVal['empNo']) > 0){
				
					$dataToYtdHist = $this->getDataToYtdDataHist($ytdVal['empNo']);
					$newYtdGross       = (float)$dataToYtdHist['YtdGross'] + (float)$ytdVal['YtdGross'];
					$newYtdTaxable     = (float)$dataToYtdHist['YtdTaxable'] + (float)$ytdVal['YtdTaxable'];
					$newYtdGovDed      = (float)$dataToYtdHist['YtdGovDed'] + (float)$ytdVal['YtdGovDed'];
					$newYtdTax         = (float)$dataToYtdHist['YtdTax'] + (float)$ytdVal['YtdTax'];
					$newYtdNonTaxAllow = (float)$dataToYtdHist['YtdNonTaxAllow'] + (float)$ytdVal['YtdNonTaxAllow'];
					$newYtd13NBonus    = (float)$dataToYtdHist['Ytd13NBonus'] + (float)$ytdVal['Ytd13NBonus'];
					$newYtdTx13NBonus  = (float)$dataToYtdHist['YtdTx13NBonus'] + (float)$ytdVal['YtdTx13NBonus'];
					
					$qryUpdateYtdDataHist = "UPDATE tblYtdDataHist SET 
											 YtdGross = '".sprintf("%01.2f",$newYtdGross)."',
											 YtdTaxable = '".sprintf("%01.2f",$newYtdTaxable)."',
											 YtdGovDed = '".sprintf("%01.2f",$newYtdGovDed)."',
											 YtdTax = '".sprintf("%01.2f",$newYtdTax)."',
											 YtdNonTaxAllow = '".sprintf("%01.2f",$newYtdNonTaxAllow)."',
											 Ytd13NBonus = '".sprintf("%01.2f",$newYtd13NBonus)."',
											 YtdTx13NBonus = '".sprintf("%01.2f",$newYtdTx13NBonus)."'";
					if($Trns){
						$Trns = $this->execQry($qryUpdateYtdDataHist);
					}	
			}
			else {
				$qryToYtdDataHist = "INSERT tblYtdDataHist(compCode,pdYear,empNo,YtdGross,YtdTaxable,
				                                           YtdGovDed,YtdTax,YtdNonTaxAllow,Ytd13NBonus,YtdTx13NBonus,
				                                           payGrp,pdNumber)
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
				                                             '{$ytdVal['pdNumber']}')";
				if($Trns){
					$Trns = $this->execQry($qryToYtdDataHist);
				}		
			}	
			unset($newYtdGross,$newYtdTaxable,$newYtdGovDed,$newYtdTax,$newYtdNonTaxAllow,$newYtd13NBonus,$newYtdTx13NBonus);
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
/*		if($Trns){
			$Trns = $this->execQry($qryDeleYtdData);
		}*/	
		
		if($Trns){
			$Trns = $this->ClossingPaySummary();
		}
		
		if($Trns){
			$Trns = $this->ClosePayPeriod();
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