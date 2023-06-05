<?
class maintEmpLoanObj extends commonObj {

	var $compCode;
	var $empNo;
	var $loanType;
	var $loanRefNo;
	var $loanPrinc;
	var $loanInt;
	var $loanStart;
	var $loanEnd;
	var $loanPeriod;
	var $loanTerms;
	var $loanDedEx;
	var $loanDedIn;
	var $loanPay;
	var $loanPayNo;
	var $loanBal;
	var $loanLastPay;
	var $loanOldRefNo;
	var $dtGranted;
	
	
	
	function addEmpLoanArt() {
		$qryAddEmpLoans = "INSERT INTO tblEmpLoans
							(compCode,empNo,lonTypeCd,lonRefNo,lonAmt,
							 lonWidInterst,lonStart,lonEnd,lonSked,lonNoPaymnts,
							 lonDedAmt1,lonDedAmt2,lonPayments,lonPaymentNo,lonCurbal,lonGranted,dateadded,";
							 if ($this->loanLastPay=="") {
							 	$qryAddEmpLoans .= "lonStat)"; 	
							 } else {
							 	$qryAddEmpLoans .= "lonLastPay,lonStat)";
							 }
		$qryAddEmpLoans .= "
					  VALUES('{$this->compCode}','{$this->empNo}','{$this->loanType}','{$this->loanRefNo}','{$this->loanPrinc}',
					  	     '{$this->loanInt}','{$this->loanStart}','{$this->loanEnd}','{$this->loanPeriod}','{$this->loanTerms}',
					  	     '{$this->loanDedEx}','{$this->loanDedIn}','{$this->loanPay}','{$this->loanPayNo}','{$this->loanBal}','{$this->dtGranted}','".date('m/d/Y')."',";
							 if ($this->loanLastPay=="") {
							 	$qryAddEmpLoans .= "'O')"; 	
							 } else {
							 	$qryAddEmpLoans .= "'{$this->loanLastPay}','O')";
							 }
		$resAddEmpLoans = $this->execQry($qryAddEmpLoans);
	}
	function editEmpLoanArt() {
		 $qryEditEmpLoans = "UPDATE tblEmpLoans SET ";
						if ($this->loanLastPay>"") {
							$qryEditEmpLoans .= "lonLastPay = '".$this->loanLastPay."', ";
						}
						$qryEditEmpLoans .= "lonRefNo = '".$this->loanRefNo."', ";
						$qryEditEmpLoans .= "lonAmt = '".$this->loanPrinc."', ";
						$qryEditEmpLoans .= "lonWidInterst = '".$this->loanInt."', ";
						$qryEditEmpLoans .= "lonStart = '".$this->loanStart."', ";
						$qryEditEmpLoans .= "lonEnd = '".$this->loanEnd."', ";
						$qryEditEmpLoans .= "lonSked = '".$this->loanPeriod."', ";
						$qryEditEmpLoans .= "lonNoPaymnts = '".$this->loanTerms."', ";
						$qryEditEmpLoans .= "lonDedAmt1 = '".$this->loanDedEx."', ";
						$qryEditEmpLoans .= "lonDedAmt2 = '".$this->loanDedIn."', ";
						$qryEditEmpLoans .= "lonPayments = '".$this->loanPay."', ";
						$qryEditEmpLoans .= "lonPaymentNo = '".$this->loanPayNo."', ";
						$qryEditEmpLoans .= "lonCurbal = '".$this->loanBal."', ";
						$qryEditEmpLoans .= "lonGranted = '".$this->dtGranted."' ";
						$qryEditEmpLoans .= "WHERE compCode = '".$this->compCode."' AND empNo = '".$this->empNo."' AND lonTypeCd = '".$this->loanType."' AND lonRefNo = '".$this->loanOldRefNo."'";	
		
		$resEditEmpLoans = $this->execQry($qryEditEmpLoans);
	}
	function deleteEmpLoanArt() {
		$qryDeleteEmpLoans = "DELETE FROM tblEmpLoans WHERE compCode = '".$this->compCode."' AND empNo = '".$this->empNo."' AND lonTypeCd = '".$this->loanType."' AND lonRefNo = '".$this->loanOldRefNo."'";
		$resDeleteEmpLoans = $this->execQry($qryDeleteEmpLoans);
	}
	function getPdPayable() {
		$qryPd = "Select pdYear,pdNumber,pdPayable from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payGrp='{$_SESSION['pay_group']}' and payCat='{$_SESSION['pay_category']}'";
		return $this->getArrRes($this->execQry($qryPd));
	}
	function ViewDate($array,$arrvalue) {
		foreach($array as $val) {
			if ($val['pdYear'] == $arrvalue['pdYear'] && $val['pdNumber'] == $arrvalue['pdNumber']) {
				$pdDate = $val['pdPayable'];
			}
		}
		return date('M d, Y', strtotime($pdDate));
	}
	function getEmpLoanInfo($lonSeries) {
		$sqlInfo = "SELECT tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblEmpLoans.lonRefNo,tblLoanType.lonTypeDesc FROM tblEmpLoans INNER JOIN tblEmpMast ON tblEmpLoans.compCode = tblEmpMast.compCode AND tblEmpLoans.empNo = tblEmpMast.empNo INNER JOIN tblLoanType ON tblEmpLoans.compCode = tblLoanType.compCode AND tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd where lonSeries=$lonSeries";
		$res = $this->getSqlAssoc($this->execQry($sqlInfo));
		return $res['empLastName'] . " " . $res['empFirstName'] . " - " . $res['lonTypeDesc'];
	}
	function getEmpLoanBal($lonSeries){
		$qry = "SELECT * FROM tblEmpLoans
					     WHERE lonSeries='$lonSeries'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);

	}
	function PreTerminate($lonSeries) {
		$date=date('m/d/Y');
		$qry = "Update tblEmpLoans set lonStat='T',closedby='{$_SESSION['user_id']}',closeddate='$date' where lonSeries='$lonSeries'";
		return 	$this->execQry($qry);
	}

	
	
}

?>