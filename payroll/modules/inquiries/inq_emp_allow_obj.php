<?
class inqEmpAllowObj extends commonObj {

	var $compCode;
	var $empNo;
	var $empDiv;
	var $empDept;
	var $empSect;
	var $allowType;
	
	
	function getEmpAllowInq() {
		if ($this->empNo > "") $empNoNew = " AND tblEmpMast.empNo = '{$this->empNo}' "; else $empNoNew = "";
		if ($this->empDiv > 0) $empDivNew = " AND tblEmpMast.empDiv LIKE '{$this->empDiv}' "; else $empDivNew = "";
		if ($this->empDept > 0) $empDeptNew = " AND tblEmpMast.empDepCode LIKE '{$this->empDept}' "; else $empDeptNew = "";
		if ($this->empSect > 0) $empSectNew = " AND tblEmpMast.empSecCode LIKE '{$this->empSect}' "; else $empSectNew = "";
		
		if ($this->allowType > 0) $allowTypeNew = " AND tblAllowance.allowCode = '{$this->allowType}' AND tblAllowType.allowCode = '{$this->allowType}' "; else $allowTypeNew = "";
		
		$qry = "SELECT tblEmpMast.empNo, tblEmpMast.empDiv, tblEmpMast.empDepCode, tblEmpMast.empSecCode, tblAllowType.allowDesc, 
                      	  tblAllowance.allowAmt, tblAllowance.allowSked, tblAllowance.allowTaxTag, tblAllowance.allowPayTag, tblAllowance.allowStart, 
                      	  tblAllowance.allowEnd
						  FROM tblEmpMast INNER JOIN
                      	  tblAllowance ON tblEmpMast.empNo = tblAllowance.empNo INNER JOIN
                      	  tblAllowType ON tblAllowance.allowCode = tblAllowType.allowCode
				   		  WHERE tblAllowance.compCode = '{$this->compCode}' AND tblEmpMast.compCode = '{$this->compCode}' AND tblAllowType.compCode = '{$this->compCode}' 
				   		  AND tblEmpMast.empPayGrp='".$_SESSION['pay_group']."' 
						  $empNoNew $empDivNew $empDeptNew $empSectNew $allowTypeNew";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getEmpAllowTotalByEmp($compCode, $empNo) {
		$qry = "SELECT TOP 100 PERCENT tblEmpMast.empLastName AS Expr1, tblEmpMast.empFirstName AS Expr2, tblEmpMast.empMidName AS Expr3, 
                          	MAX(CONVERT(varchar, tblallowType.allowDesc) + '-' + CONVERT(varchar, tblAllowance.allowCode)) AS refMax,
							SUM(tblAllowance.allowAmt) AS totAmt,
							COUNT(tblEmpMast.empLastName) AS totRec
						  FROM tblAllowance INNER JOIN 
                     	  	tblEmpMast ON tblAllowance.empNo = tblEmpMast.empNo INNER JOIN 
                          	tblallowType ON tblAllowance.allowCode = tblallowType.allowCode 
                          WHERE (tblAllowance.empNo = '{$empNo}') 
						  	AND tblAllowance.compCode = '{$compCode}' AND tblEmpMast.compCode = '{$compCode}' AND tblallowType.compCode = '{$compCode}' 
                          GROUP BY tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName";
	
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEmpAllowTotalByAllow($compCode, $allowType) {
		$qry = "SELECT TOP 100 PERCENT tblAllowance.allowCode, MAX(CONVERT(varchar, tblallowType.allowDesc) + '-' + CONVERT(varchar, tblAllowance.allowCode) 
                      	  	+ '-' + tblEmpMast.empLastName + '-' + tblEmpMast.empFirstName + '-' + tblEmpMast.empMidName + '-' + tblEmpMast.empNo) AS refMax, 
                          	SUM(tblAllowance.allowAmt) AS totAmt, COUNT(tblAllowance.allowCode) AS totRec
						  FROM tblAllowance INNER JOIN
                      		tblEmpMast ON tblAllowance.empNo = tblEmpMast.empNo INNER JOIN
                      		tblallowType ON tblAllowance.allowCode = tblallowType.allowCode
						  WHERE (tblAllowance.allowCode = '{$allowType}') AND (tblAllowance.compCode = '{$compCode}') AND (tblEmpMast.compCode = '{$compCode}') AND 
                      		(tblallowType.compCode = '{$compCode}')
						  GROUP BY tblAllowance.allowCode";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getAllowDesc($compCode,$allowCode){
		$qry = "SELECT * FROM tblAllowType
					     WHERE compCode = '{$compCode}' 
						 AND allowCode='".$allowCode."' 
						 AND allowTypeStat = 'A'";
		
		$res = $this->execQry($qry);
		$incDesc = $this->getSqlAssoc($res);
		return $incDesc['allowDesc'];
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
	function getEmpAllow($empNo,$AllowCode) {
		$qryempAllow="Select * from tblAllowance where empNo='$empNo' and allowCode='$AllowCode' and compCode='" . $_SESSION['company_code']. "'";
		$res = $this->execQry($qryempAllow);
		return $this->getSqlAssoc($res);		
	}
}

?>