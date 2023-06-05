<?
class inqTSObj extends commonObj {

	var $compCode;
	var $empName;
	var $empNo;
	var $empDiv;
	var $empDept;
	var $empSect;
	var $orderBy;
	
	function getEmpInq() {
		if ($this->empNo>"") {$empNo1 = " AND (empNo LIKE '{$this->empNo}%')";} else {$empNo1 = "";}
		if ($this->empName>"") {$empName1 = " AND (empLastName LIKE '{$this->empName}%' OR empFirstName LIKE '{$this->empName}%' OR empMidName LIKE '{$this->empName}%')";} else {$empName1 = "";}
		if ($this->empDiv>"" && $this->empDiv>0) {$empDiv1 = " AND (empDiv = '{$this->empDiv}')";} else {$empDiv1 = "";}
		if ($this->empDept>"" && $this->empDept>0) {$empDept1 = " AND (empDepCode = '{$this->empDept}')";} else {$empDept1 = "";}
		if ($this->empSect>"" && $this->empSect>0) {$empSect1 = " AND (empSecCode = '{$this->empSect}')";} else {$empSect1 = "";}
		if ($this->orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ASC ";} 
		if ($this->orderBy==2) {$orderBy1 = " ORDER BY empNo ASC ";} 
		if ($this->orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ASC ";}
		
		$qry = "SELECT * FROM tblEmpMast 
						 WHERE compCode = '{$this->compCode}'
						 AND empStat NOT IN('RS','IN','TR')
						 AND empPayGrp = '{$_SESSION['pay_group']}'
			     		 AND empPayCat = '{$_SESSION['pay_category']}' 
						 $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $orderBy1 ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getEmpTotalByDept($compCode, $empDiv, $empDept, $empSect) {
		
		$qry = "SELECT TOP 100 PERCENT empDiv,empDepCode,empSecCode,MAX(CONVERT(varchar,empDiv) + '-' + CONVERT(varchar,empDepCode) + '-' + CONVERT(varchar,empSecCode) 
                      	  	+ '-' + empLastName + '-' + empFirstName + '-' + empMidName) AS refMax, 
                          	COUNT(empLastName) AS totRec
						  FROM tblEmpMast
						  WHERE (compCode = '{$compCode}') AND 
                      		(empDiv = '{$empDiv}') AND
							(empDepCode = '{$empDept}') AND
							(empSecCode = '{$empSect}')  
							AND empPayGrp = '{$_SESSION['pay_group']}'
			     			AND empPayCat = '{$_SESSION['pay_category']}' 
							AND empStat NOT IN('RS','IN','TR') 
						  GROUP BY empDiv,empDepCode,empSecCode";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEmpTotalByDiv() {
		if ($this->empDiv>"" && $this->empDiv>0) {$empDiv1 = " AND (tblEmpMast.empDiv = '{$this->empDiv}')  AND (tblDepartment.divCode = '{$this->empDiv}') ";} else {$empDiv1 = "";}
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND 
                (tblEmpMast.compCode = '{$this->compCode}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') 
				$empDiv1";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEmpTotalByCat($empDiv) {
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec, tblPayCat.payCat, tblPayCat.payCatDesc
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv INNER JOIN
                tblPayCat ON tblEmpMast.empPayCat = tblPayCat.payCat
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND (tblPayCat.payCatStat = 'A') AND
                (tblEmpMast.compCode = '{$this->compCode}') AND (tblPayCat.compCode = '{$this->compCode}') AND 
				(tblEmpMast.empDiv = '{$empDiv}')  AND (tblDepartment.divCode = '{$empDiv}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') 
				GROUP BY tblDepartment.divCode, tblPayCat.payCat, tblPayCat.payCatDesc
				ORDER BY tblDepartment.divCode, tblPayCat.payCat, tblPayCat.payCatDesc";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getEmpTotalByGrp($empDiv,$empCat,$empGrp) {
		
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv INNER JOIN
                tblPayCat ON tblEmpMast.empPayCat = tblPayCat.payCat
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND 
                (tblEmpMast.compCode = '{$this->compCode}') AND (tblPayCat.compCode = '{$this->compCode}') AND (tblDepartment.divCode = '{$empDiv}') AND (tblEmpMast.empDiv = '{$empDiv}') AND 
                (tblPayCat.payCatStat = 'A') AND (tblEmpMast.empPayGrp = '{$empGrp}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') $empCatNew";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getAllPeriod($compCode,$groupType,$catType) {
	$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE compCode = '$compCode' AND 
					payGrp = '{$_SESSION['pay_group']}' AND 
					payCat = '{$_SESSION['pay_category']}' ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getSlctdPd($compCode,$payPd) {
		$qry = "SELECT * FROM tblPayPeriod 
				WHERE pdSeries = '$payPd' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getTimeSheet($compCode,$groupType,$catType,$empNo,$from,$to) {
		if ($empNo>"") $empNoNew = " AND empNo = '$empNo'"; else $empNoNew = "";
		$qry = "SELECT * FROM tblTimeSheet 
				WHERE compCode = '$compCode' AND 
					empPayGrp = '$groupType' AND 
					empPayCat = '$catType' AND 
					tsStat = 'A' AND (tsDate >= '$from') AND (tsDate <= '$to')
					$empNoNew ORDER BY tsDate ASC ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getTimeSheetTotal($compCode,$groupType,$catType,$empNo,$from,$to) {
		if ($empNo>"") $empNoNew = " AND (empNo = '$empNo') "; else $empNoNew = "";
		$qry = "SELECT SUM(hrsAbsent) AS totHrsAbsent, SUM(hrsTardy) AS totHrsTardy, SUM(hrsUt) AS totHrsUt, SUM(hrsOtLe8) AS totHrsOtLe8, SUM(hrsOtGt8) 
                      AS totHrsOtGt8, SUM(hrsNdLe8) AS totHrsNdLe8, SUM(hrsNdGt8) AS totHrsNdGt8, SUM(amtAbsent) AS totAmtAbsent, SUM(amtTardy) AS totAmtTardy, 
                      SUM(amtUt) AS totAmtUt, SUM(amtOtLe8) AS totAmtOtLe8, SUM(amtOtGt8) AS totAmtOtGt8, SUM(amtNdLe8) AS totAmtNdLe8, SUM(amtNdGt8) 
                      AS totAmtNdGt8
				FROM tblTimeSheet
				WHERE (compCode = '$compCode') AND (empPayGrp = '$groupType') AND (empPayCat = '$catType') AND (tsStat = 'A') $empNoNew AND (tsDate >= '$from') AND 
                      (tsDate <= '$to')";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEarnings($compCode,$empNo,$year,$number) {
		if ($empNo>"") $empNoNew = " AND empNo = '$empNo'"; else $empNoNew = "";
		$qry = "SELECT * FROM tblEarnings 
				WHERE compCode = '$compCode' AND pdYear = '$year' AND pdNumber = '$number' 
					$empNoNew ORDER BY empNo ASC ";
		
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getEarningsTotal($compCode,$empNo,$year,$number) {
		if ($empNo>"") $empNoNew = " AND (empNo = '$empNo') "; else $empNoNew = "";
		$qry = "SELECT SUM(trnAmountE) AS totAmt FROM tblEarnings
				WHERE (compCode = '$compCode') $empNoNew AND (pdYear = '$year') AND 
                      (pdNumber = '$number')";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getDuductions($compCode,$empNo,$year,$number) {
		if ($empNo>"") $empNoNew = " AND empNo = '$empNo'"; else $empNoNew = "";
		if (!$this->getPeriod($payPd)) {
			$hist = "hist";
		}		
		$qry = "SELECT * FROM tblDeductions$hist 
				WHERE compCode = '$compCode' AND pdYear = '$year' AND pdNumber = '$number' 
					$empNoNew ORDER BY empNo ASC ";
		
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getDeductionsTotal($compCode,$empNo,$year,$number) {
		if ($empNo>"") $empNoNew = " AND (empNo = '$empNo') "; else $empNoNew = "";
		$qry = "SELECT SUM(trnAmountD) AS totAmt FROM tblDeductions
				WHERE (compCode = '$compCode') $empNoNew AND (pdYear = '$year') AND 
                      (pdNumber = '$number')";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getTerminatedLoans($div,$from,$to,$loanTypeFilter) {
		if ($from != "" && $to !="") {
			$date = " and convert(datetime,convert(varchar(12), closeddate, 101)) between '$from' and '$to'";
		}	
		 $qryLoans = "Select * from tblEmpLoans where lonStat='T' $loanTypeFilter $date
					and empNo IN (Select empNo from tblEmpMast $div) and compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qryLoans);
		return $this->getArrRes($res);		

	}
	function getUserInfo($userid) {
		$qryUsers = "SELECT tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName FROM tblEmpMast INNER JOIN tblUsers ON tblEmpMast.compCode = tblUsers.compCode AND tblEmpMast.empNo = tblUsers.empNo where userId='$userid'";
		$res = $this->execQry($qryUsers);
		return $this->getSqlAssoc($res);
		
	}
	
}

?>