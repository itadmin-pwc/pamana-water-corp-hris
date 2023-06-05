<?
class inqTSObj extends commonObj {

	var $compCode;
	var $empNo;
	var $empName;
	var $empDiv;
	var $empDept;
	var $empSect;
	var $groupType;
	var $catType;
	var $orderBy;
	
	function getEmpInq() {
		if ($this->empNo>"") {$empNo1 = " AND (empNo LIKE '{$this->empNo}%')";} else {$empNo1 = "";}
		if ($this->empName>"") {$empName1 = " AND (empLastName LIKE '{$this->empName}%' OR empFirstName LIKE '{$this->empName}%' OR empMidName LIKE '{$this->empName}%')";} else {$empName1 = "";}
		if ($this->empDiv>"" && $this->empDiv>0) {$empDiv1 = " AND (empDiv = '{$this->empDiv}')";} else {$empDiv1 = "";}
		if ($this->empDept>"" && $this->empDept>0) {$empDept1 = " AND (empDepCode = '{$this->empDept}')";} else {$empDept1 = "";}
		if ($this->empSect>"" && $this->empSect>0) {$empSect1 = " AND (empSecCode = '{$this->empSect}')";} else {$empSect1 = "";}
		if ($this->groupType<3) {$groupType1 = " AND (empPayGrp = '{$this->groupType}')";} else {$groupType1 = "";}
		if ($this->orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ASC ";} 
		if ($this->orderBy==2) {$orderBy1 = " ORDER BY empNo ASC ";} 
		if ($this->orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ASC ";}
		if ($this->catType>0) {$catType1 = " AND (empPayCat = '{$this->catType}')";} else {$catType1 = "";}
		$qry = "SELECT * FROM tblEmpMast 
						 WHERE compCode = '{$this->compCode}'
						 AND empStat NOT IN('RS','IN','TR') 
						 $empNo1 $empDiv1 $empName1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getEmpTotalByDept($compCode, $empDiv, $empDept, $empSect,$groupType,$CatType) {
		if ($groupType>"") $groupTypeNew = " AND (empPayGrp = '{$groupType}') "; else $groupTypeNew = "";
		if ($catType>"") $catTypeNew = " AND (empPayCat = '{$catType}') "; else $catTypeNew = "";
		$qry = "SELECT TOP 100 PERCENT empDiv,empDepCode,empSecCode,MAX(CONVERT(varchar,empDiv) + '-' + CONVERT(varchar,empDepCode) + '-' + CONVERT(varchar,empSecCode) 
                      	  	+ '-' + empLastName + '-' + empFirstName + '-' + empMidName) AS refMax, 
                          	COUNT(empLastName) AS totRec
						  FROM tblEmpMast
						  WHERE (compCode = '{$compCode}') AND 
                      		(empDiv = '{$empDiv}') AND
							(empDepCode = '{$empDept}') AND
							(empSecCode = '{$empSect}')  
							$groupTypeNew $catTypeNew AND 
						    empStat NOT IN('RS','IN','TR') 
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
		if ($empCat=="") $empCatNew = ""; else $empCatNew = " AND (tblPayCat.payCat = '{$empCat}') AND (tblEmpMast.empPayCat = '{$empCat}') "; 
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
	function getOpenPeriod($compCode,$grp,$cat) {
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdStat = 'O' AND 
					compCode = '$compCode' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getAllPeriod($compCode,$groupType,$catType,$modulo) { // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
		$modulo="";
		if ($modulo>"") $moduloNew = " AND (pdNumber % 2) = $modulo "; else $moduloNew = "";
		$qry = "SELECT  date_format(pdPayable,'%M %Y') AS perMonth, 
				concat(MIN(pdNumber),'-',MAX(pdNumber),'-',MAX(pdYear)) AS pdNumber, MAX(pdYear) AS pdYear
				FROM tblPayPeriod
				WHERE compCode = '".$_SESSION["company_code"]."' AND payGrp = '".$_SESSION["pay_group"]."' AND payCat = '".$_SESSION["pay_category"]."' 
				GROUP BY  date_format(pdToDate,'%M %Y')
				ORDER BY MAX(pdToDate)";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getSlctdPd($compCode,$payPd) {
		$payPd2 = split("-",$payPd); 
		$qry = "SELECT concat(date_format(pdPayable,'%m'), '/' , date_format(pdPayable,'%Y')) AS perMonth, pdPayable  FROM tblPayPeriod 
				WHERE pdNumber >= '$payPd2[0]' AND pdNumber <= '$payPd2[1]' AND pdYear = '$payPd2[2]' 
				GROUP BY concat(date_format(pdPayable,'%m'), '/' , date_format(pdPayable,'%Y')),pdPayable ";
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
		$qry = "SELECT * FROM tblDeductionsHist 
				WHERE compCode = '$compCode' AND pdYear = '$year' AND pdNumber = '$number' 
					$empNoNew ORDER BY empNo ASC ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getDeductionsTotal($compCode,$empNo,$year,$number) {
		if ($empNo>"") $empNoNew = " AND (empNo = '$empNo') "; else $empNoNew = "";
		$qry = "SELECT SUM(trnAmountD) AS totAmt FROM tblDeductionsHist
				WHERE (compCode = '$compCode') $empNoNew AND (pdYear = '$year') AND 
                      (pdNumber = '$number')";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getBasicTotal($compCode,$empNo,$year,$number,$recode) {
		if ($empNo>"") $empNoNew = " AND (tblEarnings.empNo = '$empNo') "; else $empNoNew = "";
		$qry = "SELECT SUM(tblEarnings.trnAmountE) AS totAmt
				FROM tblEarnings INNER JOIN
                tblPayTransType ON tblEarnings.trnCode = tblPayTransType.trnCode
				WHERE (tblPayTransType.compCode = '$compCode') AND (tblEarnings.compCode = '$compCode') $empNoNew AND 
                (tblEarnings.pdYear = '$year') AND (tblEarnings.pdNumber = '$number') AND (tblPayTransType.trnCode = '$recode')
				GROUP BY tblEarnings.empNo";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getWTaxTotal($compCode,$empNo,$year,$number,$recode) {
		$chopMonth = split("-",$number);
		if ($empNo>"") $empNoNew = " AND (tblDeductionsHist.empNo = '$empNo') "; else $empNoNew = "";
		$qry = "SELECT SUM(tblDeductionsHist.trnAmountD) AS totAmt
				FROM tblDeductionsHist INNER JOIN
                tblPayTransType ON tblDeductionsHist.trnCode = tblPayTransType.trnCode
				WHERE (tblPayTransType.compCode = '$compCode') AND (tblDeductionsHist.compCode = '$compCode') $empNoNew AND 
                (tblDeductionsHist.pdYear = '$year') AND (tblDeductionsHist.pdSeries >= '$chopMonth[0]') AND (tblDeductionsHist.pdSeries  <= '$chopMonth[1]') AND (tblPayTransType.trnCode = '$recode')
				GROUP BY tblDeductionsHist.empNo";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	
	
	function countRec($compCode,$empNo,$year,$number,$trnCode,$table) {
		$chopMonth = split("-",$number);
		$where = ($empNo!=""?" AND empNo = '$empNo' GROUP BY empNo":"");
		
		 $qry = "SELECT empNo FROM tblDeductions
				WHERE compCode = '$compCode' AND pdYear = '$chopMonth[2]' AND pdNumber IN('$chopMonth[0]','$chopMonth[1]') AND trnCode = '$trnCode' 
				$where
				ORDER BY empNo ASC ";
		$res = $this->execQry($qry);
		
		if(mysql_num_rows($res)>0){
			return $this->execQry($qry);
		}else{
			 $qryhist = "SELECT empNo FROM tblDeductionsHist
				WHERE compCode = '$compCode' AND pdYear = '$chopMonth[2]' AND pdNumber IN('$chopMonth[0]','$chopMonth[1]') AND trnCode = '$trnCode' 
				$where
				ORDER BY empNo ASC ";
				
			 $reshist = $this->execQry($qryhist);
			 return $this->execQry($qryhist);
		}
		
	}
	
	function getOpenPer($pdNum,$pdYear)
	{
		 $qrypd = "Select * from tblPayPeriod where pdYear = '".$pdYear."' and pdNumber='".$pdNum."' and compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."'";
		$respd = $this->execQry($qrypd);
		return $this->getSqlAssoc($respd);
	}
	
	function getYTDData($payPd,$And="") {
		$arrPd = explode("-",$payPd);
		$pdYear = $arrPd[2];
		$pdNumber = $arrPd[0] . "," . $arrPd[1];
		$pdNumber = ($pdNumber == '23,25') ? '23,24,25':$pdNumber;
		if ($pdYear==2010 and $pdNumber == '9,10') {
			$AndpayGrp = ' And payGrp=1 ';
		}
		 $qryYTD = " SELECT     tblDepartment.deptDesc, SUM(tblPayrollSummaryHist.taxWitheld) AS tax, SUM(tblPayrollSummaryHist.taxableEarnings) + SUM(tblPayrollSummaryHist.minwage_taxableEarnings) AS grossearnings, 
                      SUM(tblPayrollSummaryHist.empEcola) AS ecola, SUM(tblPayrollSummaryHist.emp13thMonthNonTax) AS N13thNontax, 
                      SUM(tblPayrollSummaryHist.emp13thMonthTax) AS N13thTax ,   
                      tblBranch.brnDesc, sum(yearEndTax) AS YearEnd 
FROM         tblPayrollSummaryHist INNER JOIN
                      tblBranch ON tblPayrollSummaryHist.compCode = tblBranch.compCode AND 
                      tblPayrollSummaryHist.empBrnCode = tblBranch.brnCode LEFT OUTER JOIN
                      tblDepartment ON tblPayrollSummaryHist.compCode = tblDepartment.compCode AND 
                      tblPayrollSummaryHist.empDivCode = tblDepartment.divCode AND tblPayrollSummaryHist.empDepCode = tblDepartment.deptCode AND 
                      tblDepartment.deptLevel = '2'
					WHERE 
					pdYear='$pdYear'
					AND pdNumber IN ($pdNumber) 
					AND  tblPayrollSummaryhist.compCode='{$_SESSION[company_code]}' $And $AndpayGrp
					GROUP BY tblDepartment.deptDesc, tblDepartment.deptLevel, tblBranch.brnDesc
					order by brnDesc,tblDepartment.deptDesc";
		return $this->getArrRes($this->execQry($qryYTD));
	}
	
	function getYTDYearly($Year) {
		$Year = ($Year !="") ? $Year : date('Y');
		$qryYTDYearly = "SELECT SUM(taxWitheld) AS tax, 
						SUM(taxableEarnings+minwage_taxableEarnings) AS grossearnings, 
						SUM(empEcola) AS ecola, 
						SUM(emp13thMonthNonTax) AS N13thNontax, 
						SUM(emp13thMonthTax) AS N13thTax,  pdNumber, sum(yearEndTax) AS YearEnd 
						FROM tblPayrollSummaryHist
						WHERE (pdYear = '$Year') 
						AND compCode='{$_SESSION[company_code]}' 
						Group by pdNumber
						";
		return $this->getArrRes($this->execQry($qryYTDYearly));				
	}
	function GetYear() {
		$qryYear = "select distinct pdYear from tblPayPeriod where compCode='{$_SESSION['company_code']}' order by pdYear";
		return $this->getArrRes($this->execQry($qryYear));	
	}
	function getYTDYearlybyPayreg($Year,$pdMonth="") {
		$Year = ($Year !="") ? $Year : date('Y');
		$pdMonth = ($pdMonth !="") ? " AND pdNumber IN ($pdMonth)" : "";
		$qryYTDYearly = "SELECT SUM(tblPayrollSummaryHist.taxWitheld) AS tax, SUM(tblPayrollSummaryHist.taxableEarnings) + SUM(tblPayrollSummaryHist.minwage_taxableEarnings) AS grossearnings, 
                      	SUM(tblPayrollSummaryHist.empEcola) AS ecola, SUM(tblPayrollSummaryHist.emp13thMonthNonTax) AS N13thNontax, 
                     	SUM(tblPayrollSummaryHist.emp13thMonthTax) AS N13thTax,  tblPayrollSummaryHist.pdNumber, tblPayCat.payCatDesc,tblPayrollSummaryHist.payGrp, sum(yearEndTax) AS YearEnd 
						FROM tblPayrollSummaryHist INNER JOIN
                      	tblPayCat ON tblPayrollSummaryHist.compCode = tblPayCat.compCode AND 
                      	tblPayrollSummaryHist.payCat = tblPayCat.payCat
						WHERE (pdYear = '$Year') 
						AND tblPayrollSummaryHist.compCode='{$_SESSION[company_code]}' $pdMonth 
						GROUP BY tblPayrollSummaryHist.pdNumber, tblPayrollSummaryHist.payGrp, tblPayrollSummaryHist.payCat, tblPayCat.payCatDesc
						order by tblPayrollSummaryHist.pdNumber, tblPayrollSummaryHist.payGrp, tblPayrollSummaryHist.payCat";
		
		return $this->getArrRes($this->execQry($qryYTDYearly));				
	}	
	function GetMonthYear() {
		$qryMonthYear = "select distinct month(pdpayable) as mnth,pdYear,cast(Month(pdPayable) as varchar(4))+','+cast(pdYear as varchar(4)) as pdMonth,
						pdMonthName = 
						Case Month(pdPayable)
							WHEN 1 THEN 'JAN ' + +cast(pdYear as varchar(4))
							WHEN 2 THEN 'FEB ' + +cast(pdYear as varchar(4))
							WHEN 3 THEN 'MAR ' + +cast(pdYear as varchar(4))
							WHEN 4 THEN 'APR ' + +cast(pdYear as varchar(4))
							WHEN 5 THEN 'MAY ' + +cast(pdYear as varchar(4))
							WHEN 6 THEN 'JUN ' + +cast(pdYear as varchar(4))
							WHEN 7 THEN 'JUL ' + +cast(pdYear as varchar(4))
							WHEN 8 THEN 'AUG ' + +cast(pdYear as varchar(4))
							WHEN 9 THEN 'SEP ' + +cast(pdYear as varchar(4))
							WHEN 10 THEN 'OCT ' + +cast(pdYear as varchar(4))
							WHEN 11 THEN 'NOV ' + +cast(pdYear as varchar(4))
							WHEN 12 THEN 'DEC ' + +cast(pdYear as varchar(4))
						END from tblPayPeriod where compCode='{$_SESSION['company_code']}' order by pdyear,month(pdpayable)";
		return $this->getArrRes($this->execQry($qryMonthYear));				
	}


	function getYTDMonthlybyPayreg($payPd) {
		$payPd = explode(",",$payPd);
		$pdYear = $payPd[1];
		$pdMonth = $payPd[0];
		$qryYTDYearly = "SELECT SUM(tblPayrollSummaryHist.taxWitheld) AS tax, SUM(tblPayrollSummaryHist.taxableEarnings) + SUM(tblPayrollSummaryHist.minwage_taxableEarnings) AS grossearnings, 
                      	SUM(tblPayrollSummaryHist.empEcola) AS ecola, SUM(tblPayrollSummaryHist.emp13thMonthNonTax) AS N13thNontax, 
                     	SUM(tblPayrollSummaryHist.emp13thMonthTax) AS N13thTax,  tblPayrollSummaryHist.pdNumber, tblPayCat.payCatDesc,tblPayrollSummaryHist.payGrp, sum(yearEndTax) AS YearEnd 
						FROM tblPayrollSummaryHist INNER JOIN
                      	tblPayCat ON tblPayrollSummaryHist.compCode = tblPayCat.compCode AND 
                      	tblPayrollSummaryHist.payCat = tblPayCat.payCat
						WHERE (pdYear = '$pdYear')
						AND pdNumber IN (Select pdNumber from tblPayperiod where compCode='{$_SESSION[company_code]}' and Month(pdPayable)='$pdMonth')
						AND tblPayrollSummaryHist.compCode='{$_SESSION[company_code]}' 
						GROUP BY tblPayrollSummaryHist.pdNumber, tblPayrollSummaryHist.payGrp, tblPayrollSummaryHist.payCat, tblPayCat.payCatDesc
						order by tblPayrollSummaryHist.pdNumber, tblPayrollSummaryHist.payGrp, tblPayrollSummaryHist.payCat";
		return $this->getArrRes($this->execQry($qryYTDYearly));				
	}
	

	function MonthlyResignedEmp($payPd) {
		$payPd = explode(",",$payPd);
		$pdYear = $payPd[1];
		$pdMonth = $payPd[0];
		$qryResignedEmp = "Exec sp_ResignedEmp $pdYear,$pdMonth,9,{$_SESSION[company_code]}";
		return $this->getArrRes($this->execQry($qryResignedEmp));		
	}	
	function getCutOffPeriod($pdNum){
		if((int)trim((int)trim($pdNum))%2){
			return  '1st Payroll';
		}
		else{
			return '2nd Payroll';
		}	
	}
	
	function GetSumbyDept($payPd) {
		$payPd = explode(",",$payPd);
		$pdYear = $payPd[1];
		$pdMonth = $payPd[0];
		$sqlSum ="SELECT     SUM(tblPayrollSummaryHist.taxWitheld) AS tax, SUM(tblPayrollSummaryHist.taxableEarnings) + SUM(tblPayrollSummaryHist.minwage_taxableEarnings) AS grossearnings, 
                      SUM(tblPayrollSummaryHist.empEcola) AS ecola, SUM(tblPayrollSummaryHist.emp13thMonthNonTax) AS N13thNontax, 
                      SUM(tblPayrollSummaryHist.emp13thMonthTax) AS N13thTax, sum(yearEndTax) AS YearEnd, 
                      tblPayrollSummaryHist.empDivCode, tblPayrollSummaryHist.empDepCode, tblDepartment.deptShortDesc
						FROM tblPayrollSummaryHist INNER JOIN
											  tblDepartment ON tblPayrollSummaryHist.compCode = tblDepartment.compCode AND 
											  tblPayrollSummaryHist.empDivCode = tblDepartment.divCode AND tblPayrollSummaryHist.empDepCode = tblDepartment.deptCode
						WHERE tblPayrollSummaryHist.pdYear = '$pdYear' 
						AND tblPayrollSummaryHist.compCode = '{$_SESSION[company_code]}' 
						AND tblPayrollSummaryHist.pdNumber IN
												  (SELECT pdNumber
													FROM tblPayperiod
													WHERE compCode = '{$_SESSION[company_code]}' AND Month(pdPayable) = '$pdMonth')
							AND (tblPayrollSummaryHist.payCat = '9') AND (tblDepartment.deptLevel = '2')
						GROUP BY tblPayrollSummaryHist.empDivCode, tblPayrollSummaryHist.empDepCode,tblPayrollSummaryHist.payCat, tblDepartment.deptShortDesc
						ORDER BY tblDepartment.deptShortDesc";	
				
		return $this->getArrRes($this->execQry($sqlSum));						
	}
	function getpdPayDate($pdYear) {
		$sqlpdPayDate = "Select  CONVERT(VARCHAR(10),pdFrmDate, 101) +'-'+CONVERT(VARCHAR(10),pdToDate, 101) AS pdPayable,payGrp,pdNumber from tblPayPeriod where pdYear='$pdYear'";
		return $this->getArrRes($this->execQry($sqlpdPayDate));
	}

	function getMonthly_JE($payPd,$type) {
		$arrPd = explode("-",$payPd);
		$pdYear = $arrPd[2];
		$pdNumber = $arrPd[0] . "," . $arrPd[1];
		$pdNumber = ($pdNumber == '23,25') ? '23,24,25':$pdNumber;
		if ($type==2) {
			$minCode = " AND tblPayJournal.minCode = '007'";
			$majCode = " AND tblPayJournal.majCode2 = '350'";
		} else {
			$majCode = " AND tblPayJournal.majCode2 like  '710%'";
		}
		 
		
		  $qryMonthly_JE = " SELECT     CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(3), tblPayJournal.strCode) + 
		 			SUBSTRING(CONVERT(varchar(4),tblPayJournal.pdYear), 3, 2) + CONVERT(varchar(3), tblPayJournal.pdNumber) + 
					CONVERT(varchar(3),tblPayJournal.payGrp) + CONVERT(varchar(3),tblPayJournal.payCat) AS payRegID,
					tblPayJournal.strCode2, CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(3),tblPayJournal.majCode2)
					+ CONVERT(varchar(3), tblPayJournal.minCode2) + '00' + CONVERT(varchar(3),tblPayJournal.strCode2) AS 
					Account,tblGLCodes.glCodeDesc, sum(tblPayJournal.Amount) as Amount, tblBranch.brnDesc,pdNumber,payGrp
			FROM tblPayJournal INNER JOIN
                      tblBranch ON tblPayJournal.compCode = tblBranch.compCode AND tblPayJournal.strCode = tblBranch.glCodeStr 
					  LEFT OUTER JOIN tblGLCodes ON tblPayJournal.compGLCode = tblGLCodes.compGLCode AND tblPayJournal.majCode2 = 
					  tblGLCodes.majCode AND tblPayJournal.minCode2 = tblGLCodes.minCode AND tblPayJournal.strCode2 = 
					  tblGLCodes.strCode
			WHERE (tblPayJournal.pdNumber IN ($pdNumber)) AND pdYear='$pdYear' $majCode  $minCode
					and payCat IN (2,3,1) AND brnStat in ('A','T') group by CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(3), tblPayJournal.strCode) + 
		 			SUBSTRING(CONVERT(varchar(4),tblPayJournal.pdYear), 3, 2) + CONVERT(varchar(3), tblPayJournal.pdNumber) + 
					CONVERT(varchar(3),tblPayJournal.payGrp) + CONVERT(varchar(3),tblPayJournal.payCat),
					tblPayJournal.strCode2, CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(3),tblPayJournal.majCode2)
					+ CONVERT(varchar(3), tblPayJournal.minCode2) + '00' + CONVERT(varchar(3),tblPayJournal.strCode2),tblGLCodes.glCodeDesc, tblBranch.brnDesc,pdNumber,payGrp
			UNION ALL
			SELECT CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(15), tblPayJournal.empNo) AS payRegID,
				tblPayJournal.strCode2, CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(3),tblPayJournal.majCode2) + 
				CONVERT(varchar(3), tblPayJournal.minCode2) + '00' + CONVERT(varchar(3), tblPayJournal.strCode2) AS Account,
				tblGLCodes.glCodeDesc, sum(tblPayJournal.Amount) as Amount, tblBranch.brnDesc,pdNumber,payGrp
			FROM tblPayJournal INNER JOIN
                tblBranch ON tblPayJournal.compCode = tblBranch.compCode AND tblPayJournal.strCode = tblBranch.glCodeStr
				LEFT OUTER JOIN
				tblGLCodes ON tblPayJournal.compGLCode = tblGLCodes.compGLCode AND tblPayJournal.majCode2 = tblGLCodes.majCode AND 
				tblPayJournal.minCode2 = tblGLCodes.minCode AND tblPayJournal.strCode2 = tblGLCodes.strCode
			WHERE (tblPayJournal.pdNumber IN ($pdNumber)) AND pdYear='$pdYear' $majCode  $minCode and payCat IN (9)  AND brnStat in ('A','T')  group by CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(15), tblPayJournal.empNo),
				tblPayJournal.strCode2, CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(3),tblPayJournal.majCode2) + 
				CONVERT(varchar(3), tblPayJournal.minCode2) + '00' + CONVERT(varchar(3), tblPayJournal.strCode2),
				tblGLCodes.glCodeDesc, tblBranch.brnDesc,pdNumber,payGrp order by brnDesc,pdNumber
";
		return $this->getArrRes($this->execQry($qryMonthly_JE));
	}	

	function getAllPayPeriods() {
		$sql = "Select  cast(pdPayable as date)  as pdPayable,pdYear,pdNumber  from tblPayPeriod where payGrp='{$_SESSION['pay_group']}' order by pdYear desc,pdNumber desc";	
		return $this->getArrRes($this->execQry($sql));
	}
	function getpaySummary($brnCode,$frDate,$toDate) {
		$brnCode = ($brnCode == 0)? "":$brnCode;
		echo $sql = "call sp_PaySummary('$frDate','$toDate','$brnCode',{$_SESSION['pay_group']},{$_SESSION['pay_category']})";	
		return $this->getArrRes($this->execQry($sql));
	}
	
	function getMonthlyGovtRemittance($pdYEar,$pdMonth) {
		$sqlMonthlGovtRemittance = "call sp_monthlyremittance($pdYEar,$pdMonth);";
		$arr = $this->getArrResI($this->execQryI($sqlMonthlGovtRemittance));
		$this->next_result();
		return $arr;
	}
	function getMonthlyLoan($type,$pdYEar,$pdMonth) {
		$sqlMonthlLoans = "Exec sp_MonthlyLoans '$type','$pdYEar',$pdMonth";
		return $this->getArrRes($this->execQry($sqlMonthlLoans));
	}
	
	function getMontlyPayPeriod () {
		$sqlPeriods = "SELECT distinct date_format(pdPayable,'%M %Y') AS perMonth, date_format(pdPayable,'%m-1-%Y') AS pdNumber, pdYear,date_format(pdPayable,'%m') FROM tblPayPeriod WHERE compCode = '{$_SESSION[company_code]}' AND payGrp = '2' AND payCat = '3'  ORDER BY pdYear,date_format(pdPayable,'%m')";
		return $this->getArrRes($this->execQry($sqlPeriods));
	}
	
	function getAllPeriodPerCutOff() 
	{
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE compCode = '{$_SESSION[company_code]}' AND 
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
		
	function createPhicTxtFile($or,$amt,$date,$payPd) {
		$arrPd = explode('-',$payPd);
		$pdYear= $arrPd[2];
		$pdMonth= $arrPd[0];
		if ($pdYear==2011 && $pdMonth<=3) 
			$str = "UNION Select 0,'3420789924','POTGIETER','ERNEST FREDERICK','','RS',NULL,NULL,NULL,NULL,0,375,375,27
";
		elseif ($pdYear==2010 && $pdMonth>=6) 
			$str = "UNION Select 0,'3420789924','POTGIETER','ERNEST FREDERICK','','RS',NULL,NULL,NULL,NULL,0,375,375,27
";
		
		$sql = "Select emp.empNo,empphicNo,empLastname,empFirstname,empMidName,empStat,Year(dateHired) as dtYear,Month(dateHired) as dtMonth,Year(dateResigned) as rsYear,Month(dateResigned) as rsMonth,mtdEarnings,phicEmp,phicEmplr,(Select top 1 msb from tblSSSphic where mtdEarnings between sssLowLimit and sssUpLimit) as msb from tblEmpMast emp inner join tblMTDGovthist mtd on emp.empNo=mtd.empNo
 where pdYear='$pdYear' and pdMonth='$pdMonth' $str order by emplastName,empfirstName
";		
		$res = $this->getArrRes($this->execQry($sql));
		$doc_root = $_SERVER['DOCUMENT_ROOT']."govtTextFiles";
		$filename = $this->getCompanyName($_SESSION['company_code']).date('FY',strtotime(str_replace('-','/',$payPd))).".csv";
		if (file_exists("$doc_root/Phic/".$filename)) {
			unlink("$doc_root/Phic/".$filename);
		}
		$str = "REMITTANCE REPORT\r\n\r\n\r\n000000,42012R\r\nMEMBERS\r\n";
		$ctr = $total = 0 ;
		$file = fopen("$doc_root/Phic/".$filename,"a");				
		fwrite($file,$str."\r\n");		

		foreach($res as $val) {
			$ctr++;
			$total += round($val['phicEmp'],2)+round($val['phicEmplr'],2);
			$str = str_replace('-','',$val['empphicNo']) . ',';
			$str .= strtoupper($val['empLastname']) . ',';
			$str .= strtoupper($val['empFirstname']) . ',';
			$str .= strtoupper(substr($val['empMidName'],0,1)) . ',';
			$str .= $this->addZero(8,round($val['mtdEarnings'])). ',';
			switch($pdMonth)
				{ 
					case ($pdMonth < 4): //1, 2, 3 - first qusrter
						$str .= $this->addZero(6,number_format($val['phicEmp'], 2, '', '')). ',';
						$str .= $this->addZero(6,number_format($val['phicEmplr'], 2, '', '')). ',';
						$str .= "000000". ',';
						$str .= "000000". ',';
						$str .= "000000". ',';
						$str .= "000000". ',';
						break;
					case ($pdMonth < 7): // 4, 5, 6 - second quarter
						$str .= "000000". ',';
						$str .= "000000". ',';
						$str .= $this->addZero(6,number_format($val['phicEmp'], 2, '', '')). ',';
						$str .= $this->addZero(6,number_format($val['phicEmplr'], 2, '', '')). ',';
						$str .= "000000". ',';
						$str .= "000000". ',';
						break;
					case ($pdMonth< 10): // 7, 8, 9 - third quarter
						$str .= "000000". ',';
						$str .= "000000". ',';
						$str .= "000000". ',';
						$str .= "000000". ',';
						$str .= $this->addZero(6,number_format($val['phicEmp'], 2, '', '')). ',';
						$str .= $this->addZero(6,number_format($val['phicEmplr'], 2, '', '')). ',';
						break;
					default: // 10, 11, 12 - fourth quarter
						$ctr_tbl_s = 10;
						$ctr_tbl_e = 12;
						break;
				}
			$file = fopen("$doc_root/Phic/".$filename,"a");				
			fwrite($file,$str."\r\n");

		}
		
		$str ="M5-SUMMARY\r\n";			
		$str .= "1". $this->addZero(8,number_format($amt, 2, '', '')).",";			
		$str .= $this->addZero(8,round((float)$or,2)).",";	
		$str .= str_replace("/","",$date).",";	
		$str .= "$ctr\r\n";	
		$str .= "GRAND TOTAL".$this->addZero(10,number_format($total, 2, '', ''))."\r\n";	
		$file = fopen("$doc_root/Phic/".$filename,"a");				
		fwrite($file,$str."\r\n");		
		echo "window.open('http://192.168.200.225/govtTextFiles/Phic/$filename')";
		
		

	}
	
	function addZero($len,$str) {
		for($i=0;$i<($len-strlen($str));$i++) {
			$str = "0$str";
		}
		return	$str;
	}
	
}

?>