<?
class inqEmpLoanObj extends commonObj {

	var $compCode;
	var $empNo;
	var $empDiv;
	var $empDept;
	var $empSect;
	var $loanTypeAll;
	var $loanType;
	var $loanStatus;
	
	
	function getEmpLoanInq() {
		if ($this->empNo > "") $empNoNew = " AND tblEmpMast.empNo = '{$this->empNo}' "; else $empNoNew = "";
		if ($this->empDiv > 0) $empDivNew = " AND tblEmpMast.empDiv LIKE '{$this->empDiv}' "; else $empDivNew = "";
		if ($this->empDept > 0) $empDeptNew = " AND tblEmpMast.empDepCode LIKE '{$this->empDept}' "; else $empDeptNew = "";
		if ($this->empSect > 0) $empSectNew = " AND tblEmpMast.empSecCode LIKE '{$this->empSect}' "; else $empSectNew = "";
		
		if ($this->loanTypeAll < 4) $loanTypeAllNew = " AND tblEmpLoans.lonTypeCd LIKE '{$this->loanTypeAll}%' AND tblLoanType.lonTypeCd LIKE '{$this->loanTypeAll}%' "; else $loanTypeAllNew = "";
		if ($this->loanType > 0) $loanTypeNew = " AND tblEmpLoans.lonTypeCd = '{$this->loanType}' AND tblLoanType.lonTypeCd = '{$this->loanType}' "; else $loanTypeNew = "";
		
		
		 	$qryEmpLoanInq = "SELECT tblEmpMast.empNo, tblEmpMast.empDiv, tblEmpMast.empDepCode, tblEmpMast.empSecCode, tblEmpLoans.lonTypeCd, 
                   tblLoanType.lonTypeDesc, tblEmpLoans.lonRefNo, tblEmpLoans.lonAmt, tblEmpLoans.lonWidInterst, tblEmpLoans.lonStart, 
                   tblEmpLoans.lonEnd, tblEmpLoans.lonSked, tblEmpLoans.lonNoPaymnts, tblEmpLoans.lonDedAmt1, tblEmpLoans.lonDedAmt2, 
                   tblEmpLoans.lonPayments, tblEmpLoans.lonPaymentNo, tblEmpLoans.lonCurbal, tblEmpLoans.lonLastPay
				   FROM tblEmpLoans INNER JOIN
                   tblEmpMast ON tblEmpLoans.empNo = tblEmpMast.empNo INNER JOIN
                   tblLoanType ON tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd
				   WHERE tblEmpLoans.compCode = '{$this->compCode}' AND tblEmpMast.compCode = '{$this->compCode}' AND tblLoanType.compCode = '{$this->compCode}' 
				   AND tblEmpMast.empPayGrp='".$_SESSION['pay_group']."' 
				   $empNoNew $empDivNew $empDeptNew $empSectNew $loanTypeAllNew $loanTypeNew $loanStatusNew  ";
		$resqryEmpLoanInq = $this->execQry($qryEmpLoanInq);
		return $this->getArrRes($resqryEmpLoanInq);
	}
	function getEmpLoanTotalByEmp($compCode, $empNo, $groupType) {
		$qryEmpLoanInq = "SELECT TOP 100 PERCENT tblEmpMast.empLastName AS Expr1, tblEmpMast.empFirstName AS Expr2, tblEmpMast.empMidName AS Expr3, 
                          	MAX(CONVERT(varchar, tblLoanType.lonTypeDesc) + '-' + CONVERT(varchar, tblEmpLoans.lonRefNo) + '-' + CONVERT(varchar, tblEmpLoans.lonTypeCd)) AS refMax,
							SUM(tblEmpLoans.lonWidInterst) AS totAmt, SUM(tblEmpLoans.lonPayments) AS totPaymnts, SUM(tblEmpLoans.lonCurbal) AS totCurbal,
							COUNT(tblEmpMast.empLastName) AS totRec
						  FROM tblEmpLoans INNER JOIN 
                     	  	tblEmpMast ON tblEmpLoans.empNo = tblEmpMast.empNo INNER JOIN 
                          	tblLoanType ON tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd 
                          WHERE (tblEmpLoans.empNo = '{$empNo}') 
						  	AND tblEmpLoans.compCode = '{$compCode}' AND tblEmpMast.compCode = '{$compCode}' AND tblLoanType.compCode = '{$compCode}' 
                          GROUP BY tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName";
		$resqryEmpLoanInq = $this->execQry($qryEmpLoanInq);
		return $this->getSqlAssoc($resqryEmpLoanInq);
	}
	function getEmpLoanTotalByLoan($compCode, $loanType, $groupType) {
		$qryEmpLoanInq = "SELECT TOP 100 PERCENT tblEmpLoans.lonTypeCd, MAX(CONVERT(varchar, tblLoanType.lonTypeDesc) + '-' + CONVERT(varchar, 
                      	  	tblEmpLoans.lonRefNo) + '-' + CONVERT(varchar, tblEmpLoans.lonTypeCd) 
                      	  	+ '-' + tblEmpMast.empLastName + '-' + tblEmpMast.empFirstName + '-' + tblEmpMast.empMidName) AS refMax, 
                          	SUM(tblEmpLoans.lonWidInterst) AS totAmt, SUM(tblEmpLoans.lonPayments) AS totPaymnts, 
						  	SUM(tblEmpLoans.lonCurbal) AS totCurbal, COUNT(tblEmpLoans.lonTypeCd) AS totRec
						  FROM tblEmpLoans INNER JOIN
                      		tblEmpMast ON tblEmpLoans.empNo = tblEmpMast.empNo INNER JOIN
                      		tblLoanType ON tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd
						  WHERE (tblEmpLoans.lonTypeCd = '{$loanType}') AND (tblEmpLoans.compCode = '{$compCode}') AND (tblEmpMast.compCode = '{$compCode}') AND 
                      		(tblLoanType.compCode = '{$compCode}')
						  GROUP BY tblEmpLoans.lonTypeCd";
		$resqryEmpLoanInq = $this->execQry($qryEmpLoanInq);
		return $this->getSqlAssoc($resqryEmpLoanInq);
	}
	function getLoanDesc($compCode,$loanCode){
		$qry = "SELECT * FROM tblLoanType
					     WHERE compCode = '{$compCode}' 
						 AND lonTypeCd LIKE '$loanCode' 
						 AND lonTypeStat = 'A'";
		$res = $this->execQry($qry);
		$lnDesc = $this->getSqlAssoc($res);
		return $lnDesc['lonTypeDesc'];
	}
	function getPayPd($compCode,$pdYear,$pdNumber,$trnCat,$trnGrp){
		$qry = "SELECT * FROM tblPayPeriod
					     WHERE compCode = '{$compCode}' 
						 AND pdYear = '$pdYear' 
						 AND pdNumber = '$pdNumber'
						 AND payCat = '$trnCat'
						 AND payGrp = '$trnGrp'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);

	}
	function getEmpLoanBal($compCode,$empNo,$lonTypeCd,$lonRefNo){
		$qry = "SELECT * FROM tblEmpLoans
					     WHERE compCode = '{$compCode}' 
						 AND empNo = '$empNo' 
						 AND lonTypeCd = '$lonTypeCd'
						 AND lonRefNo = '$lonRefNo'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);

	}
	
	function getEmpLoans($And) {
		$qryLoans = "Select tblEmpLoans.*,lonTypeShortDesc from tblEmpLoans Inner Join tblLoanType on tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd where tblEmpLoans.compCode='{$_SESSION['company_code']}' and tblLoanType.compCode='{$_SESSION['company_code']}' $And";
		$res = $this->execQry($qryLoans);
		return $this->getArrRes($res);
	}
	
	function getGovDed($act,$where) {
		$qryGov = "Select empNo from tblMtdGovt $where";
		$qryGovHist = "Select empNo from tblMtdGovtHist $where";
		$res = $this->execQry($qryGov);
		$resHist = $this->execQry($qryGovHist);
		$ctrGovded = $this->getRecCount($res) + $this->getRecCount($resHist);
		if ($act == 'Count') {
			return $ctrGovded;
		} else {
			return $this->getArrRes($res);	
		}	
	}
	
	function getEmpInfo($empNo) {
		$qry = "Select empNo,empLastName,empFirstName,empMidName,empDiv,empDepCode,empSecCode from tblEmpMast where empNo='$empNo' and compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
		
	}

}

?>