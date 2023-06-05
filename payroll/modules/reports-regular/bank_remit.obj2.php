<?
class bankRemitObj extends commonObj {
	
	var $get;
	
	var $session;
	
	public function __construct($session,$method){
		$this->session = $session;
		$this->get = $method;
	}
	
	function getPeriod($compCode,$groupType,$catType,$andCondition) {
		 $qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE compCode = '$compCode' AND 
					payGrp = '$groupType' AND 
					payCat = '$catType' ";
		 if($andCondition != ""){
		 	$qry .= $andCondition;
		 }
		$res = $this->execQry($qry);
		if($this->getRecCount($res) > 1){
			return $this->getArrRes($res);
		}
		else{
			return $this->getSqlAssoc($res);
		}
	}
	
	function getBankRemitData($act="") {
		
	$payPdSlctd = $this->getPayPeriod($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdPayable = '{$this->get['payPd']}'");		
	if ($payPdSlctd['pdStat']=="H") {
		$hist = "hist";
	}
			 $qryGetPaySum = "SELECT ps.sprtAllow,ps.empNo,ps.netSalary,emp.empLastName,emp.empMidName,emp.empFirstName,emp.empAcctNo,emp.compCode
								  FROM tblPayrollSummary$hist as ps LEFT JOIN tblEmpMast as emp
								  ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
							  WHERE ps.payGrp = '{$_SESSION['pay_group']}'
							  AND ps.payCat = '{$_SESSION['pay_category']}'
							  AND ps.pdYear = '{$payPdSlctd['pdYear']}'
							  AND ps.pdNumber = '{$payPdSlctd['pdNumber']}' 
							  AND ps.empBnkCd = '{$this->get['cmbBank']}' 
							  AND ps.compCode = '{$_SESSION['company_code']}' ";
			if(trim($this->get['txtEmpNo']) != ""){
				$qryGetPaySum .= "AND ps.empNo = '{$this->get['txtEmpNo']}' ";
			}
			if(trim($this->get['txtEmpName']) != ""){
				if($this->get['nameType'] == 1){
					$qryGetPaySum .= "AND emp.empLastName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 2){
					$qryGetPaySum .= "AND emp.empFirstName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 3){
					$qryGetPaySum .= "AND emp.empMidName LIKE '{$this->get['txtEmpName']}%' ";
				}
			}
			if($this->get['cmbDiv'] != 0){
				$qryGetPaySum .= "AND ps.empDivCode = '{$this->get['cmbDiv']}%' ";
			}
			if($this->get['cmbDept'] != 0){
				$qryGetPaySum .= "AND ps.empDepCode = '{$this->get['cmbDept']}%' ";
			}
			if($this->get['cmbSect'] != 0){
				$qryGetPaySum .= "AND ps.empSecCode = '{$this->get['cmbSect']}%' ";
			}
			if($this->get['orderBy'] == 1){
			 $qryGetPaySum .= "ORDER BY emp.empLastName ";
			}
			if($this->get['orderBy'] == 2){
			 $qryGetPaySum .= "ORDER BY emp.empFirstName ";
			}
			if($this->get['orderBy'] == 3){
			 $qryGetPaySum .= "ORDER BY ps.empNo ";
			}
			if($this->get['orderBy'] == 4){
			 $qryGetPaySum .= "ORDER BY ps.empDepCode ";
			}
			
			$resGetPaySum = $this->execQry($qryGetPaySum);
			if ($act=="") {
				return $this->getRecCount($resGetPaySum);
			}
			else {
				return $this->getArrRes($resGetPaySum);
			}
	}	

	function getBankRemitDataMTC($act="",$bankCode=""){
	$payPdSlctd = $this->getPayPeriod($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdPayable = '{$this->get['payPd']}'");		
	if ($payPdSlctd['pdStat']=="H") {
		$hist = "hist";
	}		
	$payPdSlctd = $this->getPayPeriod($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdPayable = '{$this->get['payPd']}'");		
		
			$qryGetPaySum = "SELECT ps.empNo,(ps.netSalary+ps.sprtAllow) as netSalary,emp.empLastName,emp.empMidName,emp.empFirstName,emp.empAcctNo,emp.compCode
								  FROM tblPayrollSummary$hist as ps LEFT JOIN tblEmpMast as emp
								  ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
							  WHERE ps.payGrp = '{$_SESSION['pay_group']}'
							  AND ps.payCat = '{$_SESSION['pay_category']}'
							  AND ps.pdYear = '{$payPdSlctd['pdYear']}'
							  AND ps.pdNumber = '{$payPdSlctd['pdNumber']}' 
							  AND ps.empBnkCd = '$bankCode' ";
			if(trim($this->get['txtEmpNo']) != ""){
				$qryGetPaySum .= "AND ps.empNo = '{$this->get['txtEmpNo']}' ";
			}
			if(trim($this->get['txtEmpName']) != ""){
				if($this->get['nameType'] == 1){
					$qryGetPaySum .= "AND emp.empLastName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 2){
					$qryGetPaySum .= "AND emp.empFirstName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 3){
					$qryGetPaySum .= "AND emp.empMidName LIKE '{$this->get['txtEmpName']}%' ";
				}
			}
			if($this->get['cmbDiv'] != 0){
				$qryGetPaySum .= "AND ps.empDivCode = '{$this->get['cmbDiv']}%' ";
			}
			if($this->get['cmbDept'] != 0){
				$qryGetPaySum .= "AND ps.empDepCode = '{$this->get['cmbDept']}%' ";
			}
			if($this->get['cmbSect'] != 0){
				$qryGetPaySum .= "AND ps.empSecCode = '{$this->get['cmbSect']}%' ";
			}
			if($this->get['orderBy'] == 1){
			 $qryGetPaySum .= "ORDER BY emp.empLastName ";
			}
			if($this->get['orderBy'] == 2){
			 $qryGetPaySum .= "ORDER BY emp.empFirstName ";
			}
			if($this->get['orderBy'] == 3){
			 $qryGetPaySum .= "ORDER BY ps.empNo ";
			}
			if($this->get['orderBy'] == 4){
			 $qryGetPaySum .= "ORDER BY ps.empDepCode ";
			}
			$resGetPaySum = $this->execQry($qryGetPaySum);
			if ($act=="") {
				return $this->getRecCount($resGetPaySum);
			}
			else {
				return $this->getArrRes($resGetPaySum);
			}
	}		
	function checkAllowance($arrAllow) {
		$ctr=0;
		foreach($arrAllow as $val) {
			if ($val['ALLOW']!=0) {
				$ctr++;
			}
		}
		return $ctr;
	}
	function toDbEncrypt($empAcctNo,$empFullName,$netSalary,$coCtr,$db) {
		$qryDbEncrypt = "Insert into tblEncrypt ([AcctNo],[Name],[Salary],[CoCntr]) values ('$empAcctNo','$empFullName',$netSalary,$coCtr)";
		return $db->Execute($qryDbEncrypt);		
	}
}

?>