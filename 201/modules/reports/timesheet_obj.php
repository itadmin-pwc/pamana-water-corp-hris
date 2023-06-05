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
				AND empStat='RG' and employmentTag IN ('RG','PR','CN')  
				$empNo1 $empDiv1 $empName1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	/*Common*/
	function getSlctdPd($compCode,$payPd) 
	{
		$qry = "SELECT * FROM tblPayPeriod 
				WHERE pdSeries = '$payPd' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getOpenPeriod($compCode,$grp,$cat) 
	{
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdStat = 'O' AND 
			    compCode = '$compCode' AND
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
					
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getAllPeriod($compCode,$groupType,$catType) 
	{
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE compCode = '$compCode' AND 
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	//for sprtSP="Y" and N Combobox
	function allowType($compCode,$con)
	{
		if($con=='N')
			$condition = "and (sprtPS='N' or sprtPS is null)";
		else
			$condition = "and sprtPS='Y'";
			
		$qryAllowType = "Select * from tblAllowType
						where compCode='".$compCode."'
						and allowTypeStat='A'
						$condition
						order by allowDesc
						";
		$res = $this->execQry($qryAllowType);
		return $this->getArrRes($res);
	}
	
	//for sprtSP="Y" and N
	function getAllowAmt($compCode,$empNo,$year,$number,$recode,$tbl,$con) {
		if ($empNo>"") $empNoNew = " AND ($tbl.empNo = '$empNo') "; else $empNoNew = "";
		
		if($recode!="")
			$where = "AND (tblPayTransType.trnCode = '$recode')";
		
		if($con=='N')
			$condition = "and (sprtPS='N' or sprtPS is null or sprtPS='')";
		else
			$condition = "and sprtPS='Y'";
			
		$qry = "SELECT SUM($tbl.trnAmountE) AS totAmt
				FROM $tbl INNER JOIN
                tblPayTransType ON $tbl.trnCode = tblPayTransType.trnCode
				WHERE (tblPayTransType.compCode = '$compCode') AND ($tbl.compCode = '$compCode') $empNoNew AND 
                ($tbl.pdYear = '$year') AND ($tbl.pdNumber = '$number') AND trnRecode='".EARNINGS_RECODEALLOW."' 
				$condition $where
				GROUP BY $tbl.empNo";
		
		return $this->execQry($qry);
	}
	
	/*End of Common Function*/
	
	/*Earnings Register/OT and ND Function*/
		function getEarnings($compCode,$empNo,$year,$number,$tbl) 
		{
			if ($empNo>"") $empNoNew = " AND empNo = '$empNo'"; else $empNoNew = "";
			
			$qry = "SELECT * FROM $tbl 
					WHERE compCode = '$compCode' AND pdYear = '$year' AND pdNumber = '$number' 
					$empNoNew ORDER BY empNo ASC ";
			
			$res = $this->execQry($qry);
			return $this->getArrRes($res);
		}
		
		function getBasicTotal2($compCode,$empNo,$year,$number,$recode,$tbl) 
		{
			if ($empNo>"") $empNoNew = " AND ($tbl.empNo = '$empNo') "; else $empNoNew = "";
			if($recode==EARNINGS_RECODEALLOW)
			{
				if($_GET["reportType"]==1)
					$where_allow = "AND ((tblAllowType.sprtPS = 'N')or(tblAllowType.sprtPS is null))";
				elseif($_GET["reportType"]==2)
					$where_allow = "AND (tblAllowType.sprtPS = 'Y')";
					
				$qry = "SELECT SUM($tbl.trnAmountE) AS totAmt
					FROM $tbl 
					INNER JOIN tblPayTransType ON $tbl.trnCode = tblPayTransType.trnCode
					INNER JOIN tblAllowType ON tblPayTransType.trnCode = tblAllowType.trnCode
					WHERE (tblPayTransType.compCode = '$compCode') AND ($tbl.compCode = '$compCode') $empNoNew AND 
					($tbl.pdYear = '$year') AND ($tbl.pdNumber = '$number') AND (tblPayTransType.trnRecode = '$recode')
					AND (tblPayTransType.trnStat = 'A')
					AND (tblAllowType.compCode='$compCode')
					AND (tblAllowType.allowTypeStat = 'A')
					$where_allow
					GROUP BY $tbl.empNo";
			}
			else
			{
				$qry = "SELECT SUM($tbl.trnAmountE) AS totAmt
						FROM $tbl INNER JOIN
						tblPayTransType ON $tbl.trnCode = tblPayTransType.trnCode
						WHERE (tblPayTransType.compCode = '$compCode') AND ($tbl.compCode = '$compCode') $empNoNew AND 
						($tbl.pdYear = '$year') AND ($tbl.pdNumber = '$number') AND (tblPayTransType.trnRecode = '$recode')
						GROUP BY $tbl.empNo";
			}
			
			$res = $this->execQry($qry);
			return $this->getSqlAssoc($res);
		}
	/*End of Earnings Register Function*/

	/*Deductions Register Functions*/
		function getDuductions($compCode,$empNo,$year,$number,$tbl) 
		{
			if ($empNo>"") $empNoNew = " AND empNo = '$empNo'"; else $empNoNew = "";
			
			$qry = "SELECT * FROM $tbl 
					WHERE compCode = '$compCode' AND pdYear = '$year' AND pdNumber = '$number' 
					$empNoNew ORDER BY empNo ASC ";
			
			$res = $this->execQry($qry);
			return $this->getArrRes($res);
		}
		
		function getWTaxTotal2($compCode,$empNo,$year,$number,$recode,$tbl) 
		{
			if ($empNo>"") $empNoNew = " AND ($tbl.empNo = '$empNo') "; else $empNoNew = "";
			  
			$qry = "SELECT SUM($tbl.trnAmountD) AS totAmt
					FROM $tbl
					WHERE  ($tbl.compCode = '$compCode') $empNoNew AND 
					($tbl.pdYear = '$year') AND ($tbl.pdNumber = '$number') AND ($tbl.trnCode = '$recode')";
			$res = $this->execQry($qry);
			return $this->getSqlAssoc($res);
		}
		
		function getloans($compCode,$empNo,$year,$number,$tbl) 
		{
			if ($empNo>"") $empNoNew = " AND ($tbl.empNo = '$empNo') "; else $empNoNew = "";
			 
			 $qry = "SELECT SUM($tbl.trnAmountD) AS totAmt
					FROM $tbl
					WHERE  ($tbl.compCode = '$compCode') $empNoNew AND 
					($tbl.pdYear = '$year') AND ($tbl.pdNumber = '$number') 
					AND ($tbl.trnCode IN (Select trnCode from tblLoanType where compCode = '$compCode'))";
		
			$res = $this->execQry($qry);
			return $this->getSqlAssoc($res);
		}
		
		function getotherdeductions($compCode,$empNo,$year,$number,$tbl) 
		{
			if ($empNo>"") $empNoNew = " AND ($tbl.empNo = '$empNo') "; else $empNoNew = "";
			 
			 if($tbl=='tblDeductionsHist')
			 	$dedTrandtl = "tblDedTranDtlHist";
			 else
			 	$dedTrandtl = "tblDedTranDtl";
			 
			 $qry = "SELECT SUM($tbl.trnAmountD) AS totAmt
					FROM $tbl
					WHERE  ($tbl.compCode = '$compCode') $empNoNew AND 
					($tbl.pdYear = '$year') AND ($tbl.pdNumber = '$number') 
					AND ($tbl.trnCode IN (Select trnCode from ".$dedTrandtl." where compCode = '$compCode'))
									";
			
			
			
			$res = $this->execQry($qry);
			return $this->getSqlAssoc($res);
		}
	/*End of Deduction Register Function*/
	
	/*OT and ND Report Functiont*/
	function getBasicTotal($compCode,$empNo,$year,$number,$recode,$tbl,$chk) 
	{
		if ($empNo>"") $empNoNew = " AND ($tbl.empNo = '$empNo') "; else $empNoNew = "";
		if($chk=='1')
			$cond = "AND (tblPayTransType.trnRecode = '$recode')";
		else
			$cond = "AND (tblPayTransType.trnCode = '$recode')";
			
		$qry = "SELECT SUM($tbl.trnAmountE) AS totAmt
				FROM $tbl INNER JOIN
                tblPayTransType ON $tbl.trnCode = tblPayTransType.trnCode
				WHERE (tblPayTransType.compCode = '$compCode') AND ($tbl.compCode = '$compCode') $empNoNew AND 
                ($tbl.pdYear = '$year') AND ($tbl.pdNumber = '$number') $cond
				GROUP BY $tbl.empNo";
		
		return $this->execQry($qry);
		
	}
	/*End of OT and ND Function*/
	
	/*UT and TARD Function*/
	function getUTND($compCode,$empNo,$year,$number,$trnCode,$tbl)
	{
		if ($empNo>"") $empNoNew = " AND empNo = '$empNo'"; else $empNoNew = "";
		
		$qry = "SELECT trnAmountE as totAmt FROM $tbl
				WHERE compCode = '$compCode' AND pdYear = '$year' AND pdNumber = '$number' AND trnCode = '$trnCode' 
				$empNoNew ORDER BY empNo ASC ";
		
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	/*End of UT and TARD Function*/
	
	/*Deduction by Deduction Type*/
	function getAllAvailDeductType($compCode,$trnCode)
	{
		$where = ($trnCode!=""?"and trnCode='".$trnCode."'":"");
		$qryIntMaxRec = "Select * from tblPayTransType
						 where
						 compCode='".$compCode."' 
						 and trnCat='D' 
						 and trnStat='A'
						 $where
						 order by trnRecode";
		
		$res = $this->execQry($qryIntMaxRec);
		return $this->getArrRes($res);
	}
	
	function getBasicTotalDed($compCode,$empNo,$year,$number,$recode,$tbl) {
		if ($empNo>"") $empNoNew = " AND ($tbl.empNo = '$empNo') "; else $empNoNew = "";
		
		$qry = "SELECT SUM($tbl.trnAmountD) AS totAmt
				FROM $tbl INNER JOIN
                tblPayTransType ON $tbl.trnCode = tblPayTransType.trnCode
				WHERE (tblPayTransType.compCode = '$compCode') AND ($tbl.compCode = '$compCode') $empNoNew AND 
                ($tbl.pdYear = '$year') AND ($tbl.pdNumber = '$number') AND (tblPayTransType.trnCode = '$recode')
				GROUP BY $tbl.empNo";
		
		return $this->execQry($qry);
	}
	/*End of Deduction Type Function*/
	
	/*Denomination Function*/
	function getDenomList() 
	{
		$qry = "SELECT * FROM tblDenomList WHERE denTag = 'Y' ORDER BY denomination DESC";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getDenomAmt($denom,$amt) 
	{
		$qry = "SELECT * FROM tblDenomList WHERE denTag = 'Y' ORDER BY denomination DESC";
		$res = $this->execQry($qry);
		$arr = $this->getArrRes($res);
		$stack = array();
		
		foreach ($arr as $val)
		{
			if ($amt>=$val['denomination']) 
			{
				$tmpDenom = $amt / $val['denomination'];
				$tmpDenom = floor($tmpDenom);
			} 
			else 
			{
				$tmpDenom = 0;
			}
			array_push($stack, $tmpDenom);
			$amt = $amt - ($tmpDenom * $val['denomination']);
			$amt = round($amt, 2); 
		} 
		return $stack;
	}
	
	
	function getDenom($compCode,$empNo,$year,$number,$reportType) 
	{
		if($empNo!='')
		{
			$qry = "SELECT SUM(".$reportType.".netSalary) AS totAmt, ".$reportType.".empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, 
                tblEmpMast.empMidName 
				FROM ".$reportType." INNER JOIN 
                tblEmpMast ON ".$reportType.".empNo = tblEmpMast.empNo 
				WHERE ".$reportType.".compCode = '$compCode' 
					AND ".$reportType.".empNo = '".$empNo."'
					AND tblEmpMast.compCode = '$compCode' AND 
					".$reportType.".pdYear = '$year' AND ".$reportType.".pdNumber = '$number' $empNoNew 
				GROUP BY ".$reportType.".empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName ";
		}
		else
		{
		$qry = "SELECT SUM(".$reportType.".netSalary) AS totAmt, ".$reportType.".empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, 
                tblEmpMast.empMidName 
				FROM ".$reportType." INNER JOIN 
                tblEmpMast ON ".$reportType.".empNo = tblEmpMast.empNo 
				WHERE ".$reportType.".compCode = '$compCode' AND tblEmpMast.compCode = '$compCode' AND 
					".$reportType.".pdYear = '$year' AND ".$reportType.".pdNumber = '$number' $empNoNew 
				GROUP BY ".$reportType.".empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName ";
		}
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	/*End of Denomination Function*/
	
	/*Payroll Register Functions*/
	function chkEmpPaySumm($empDiv,$empDept,$empSect,$empNo,$year,$number,$reportType,$chk,$locType,$empBrnCode)
	{
	
		if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDivCode = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		if ($locType=="S")
			$locType1 = " AND (empLocCode = '{$empBrnCode}')";
		if ($locType=="H")
			$locType1 = " AND (empLocCode = '0001')";
			
		
		if($reportType=='0')
			$tbl = "tblPayrollSummary";
		else
			$tbl = "tblPayrollSummaryHist";
		
		if($chk==1)
			$field = "count(empNo) as totEmp";
		else
			$field = "*";
			
		$qrychkPayrollSum = "Select $field from $tbl
							where compCode='".$_SESSION["company_code"]."'
							and pdYear='".$year."'
							and pdNumber='".$number."'
							and payGrp='".$_SESSION["pay_group"]."'
							and payCat='".$_SESSION["pay_category"]."'
							$empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $empBrnCode1 $locType1
							";
		
		$resPayrollSum = $this->execQry($qrychkPayrollSum);
		return $this->getSqlAssoc($resPayrollSum);
	}
	/*End of Payroll Register Functions*/
	
	/*UNKNOWN FUNCTIONS*/
	function getEmpTotalByDept($compCode, $empDiv, $empDept, $empSect,$groupType,$CatType) 
	{
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
				empStat='RG' and employmentTag IN ('RG','PR','CN') 
				GROUP BY empDiv,empDepCode,empSecCode";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getEmpTotalByDiv() 
	{
		if ($this->empDiv>"" && $this->empDiv>0) {$empDiv1 = " AND (tblEmpMast.empDiv = '{$this->empDiv}')  AND (tblDepartment.divCode = '{$this->empDiv}') ";} else {$empDiv1 = "";}
		
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND 
                (tblEmpMast.compCode = '{$this->compCode}') AND tblEmpMast.empStat='RG' and tblEmpMast.employmentTag IN ('RG','PR','CN') 
				$empDiv1";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getEmpTotalByCat($empDiv) 
	{
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec, tblPayCat.payCat, tblPayCat.payCatDesc
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv INNER JOIN
                tblPayCat ON tblEmpMast.empPayCat = tblPayCat.payCat
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND (tblPayCat.payCatStat = 'A') AND
                (tblEmpMast.compCode = '{$this->compCode}') AND (tblPayCat.compCode = '{$this->compCode}') AND 
				(tblEmpMast.empDiv = '{$empDiv}')  AND (tblDepartment.divCode = '{$empDiv}') AND tblEmpMast.empStat='RG' and tblEmpMast.employmentTag IN ('RG','PR','CN')  
				GROUP BY tblDepartment.divCode, tblPayCat.payCat, tblPayCat.payCatDesc
				ORDER BY tblDepartment.divCode, tblPayCat.payCat, tblPayCat.payCatDesc";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getEmpTotalByGrp($empDiv,$empCat,$empGrp) 
	{
		if ($empCat=="") $empCatNew = ""; else $empCatNew = " AND (tblPayCat.payCat = '{$empCat}') AND (tblEmpMast.empPayCat = '{$empCat}') "; 
		
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv INNER JOIN
                tblPayCat ON tblEmpMast.empPayCat = tblPayCat.payCat
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND 
                (tblEmpMast.compCode = '{$this->compCode}') AND (tblPayCat.compCode = '{$this->compCode}') AND (tblDepartment.divCode = '{$empDiv}') AND (tblEmpMast.empDiv = '{$empDiv}') AND 
                (tblPayCat.payCatStat = 'A') AND (tblEmpMast.empPayGrp = '{$empGrp}') AND tblEmpMast.empStat='RG' and tblEmpMast.employmentTag IN ('RG','PR','CN')  $empCatNew";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getTimeSheet($compCode,$groupType,$catType,$empNo,$from,$to) 
	{
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
	
	function getTimeSheetTotal($compCode,$groupType,$catType,$empNo,$from,$to) 
	{
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
	
	function getEarningsTotal($compCode,$empNo,$year,$number) 
	{
		if ($empNo>"") $empNoNew = " AND (empNo = '$empNo') "; else $empNoNew = "";
		$qry = "SELECT SUM(trnAmountE) AS totAmt FROM tblEarnings
				WHERE (compCode = '$compCode') $empNoNew AND (pdYear = '$year') AND 
                      (pdNumber = '$number')";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getDeductionsTotal($compCode,$empNo,$year,$number) 
	{
		if ($empNo>"") $empNoNew = " AND (empNo = '$empNo') "; else $empNoNew = "";
		
		$qry = "SELECT SUM(trnAmountD) AS totAmt FROM tblDeductions
				WHERE (compCode = '$compCode') $empNoNew AND (pdYear = '$year') AND 
                pdNumber = '$number')";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getWTaxTotal($compCode,$empNo,$year,$number,$recode) 
	{
		if ($empNo>"") $empNoNew = " AND (tblDeductions.empNo = '$empNo') "; else $empNoNew = "";
		
		$qry = "SELECT SUM(tblDeductions.trnAmountD) AS totAmt
				FROM tblDeductions INNER JOIN
                tblPayTransType ON tblDeductions.trnCode = tblPayTransType.trnCode
				WHERE (tblPayTransType.compCode = '$compCode') AND (tblDeductions.compCode = '$compCode') $empNoNew AND 
                (tblDeductions.pdYear = '$year') AND (tblDeductions.pdNumber = '$number') AND (tblPayTransType.trnCode = '$recode')
				GROUP BY tblDeductions.empNo";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getBasic($compCode,$empNo,$year,$number,$trnCode) 
	{
		if ($empNo>"") $empNoNew = " AND empNo = '$empNo'"; else $empNoNew = "";
		
		$qry = "SELECT * FROM tblEarnings
				WHERE compCode = '$compCode' AND pdYear = '$year' AND pdNumber = '$number' AND trnCode = '$trnCode' 
				$empNoNew ORDER BY empNo ASC ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function PaySlip($compCode,$empNo,$pdyear,$pdnumber,$payPd) {
		if (!$this->getPeriod($payPd)) {
			$hist = "hist";
		}
		if ($empNo!="") {
			
			$sqlpayslip="Select * from tblPayrollSummary$hist where empNo='$empNo' and pdYear='$pdyear' and pdNumber='$pdnumber' and compCode='$compCode' and payGrp='" . $_SESSION['pay_group'] . "' and payCat='" . $_SESSION['pay_category'] . "'";
		}
		else {
			$sqlpayslip="Select * from tblPayrollSummary$hist where pdYear='$pdyear' and pdNumber='$pdnumber' and compCode='$compCode' and payGrp='" . $_SESSION['pay_group'] . "' and payCat='" . $_SESSION['pay_category'] . "'";
		}
		$res = $this->execQry($sqlpayslip);
		return $this->getArrRes($res);		
	}	

	
	function getSlctdPdwil($compCode,$payPd)
	{
		$qry = "SELECT * FROM tblPayPeriod WHERE     compCode = '$compCode' AND pdPayable = '$payPd' and payGrp='" . $_SESSION['pay_group'] . "' and payCat='" . $_SESSION['pay_category'] . "'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getListofAllowance()
	{
		$qryAllowType = "Select * from tblPayTransType
							where trnCode in (Select trnCode from tblAllowType
							where compCode='".$_SESSION["company_code"]."' and allowTypeStat='A')
							and compCode='".$_SESSION["company_code"]."' and trnCat='E' and trnStat='A' order by trnShortDesc;";
		$res = $this->execQry($qryAllowType );
		return $this->getArrRes($res);
	}
	
	function getAllDeduction($tbl)
	{
		$qry = "Select distinct(tblDed.trnCode),trnShortDesc
				from $tbl tblDed, tblEmpMast tblEmp, tblPayTransType tblTtype
				where 
				tblDed.empNo=tblEmp.empNo
				AND tblDed.trnCode=tblTtype.trnCode
				AND tblEmp.compCode='".$_SESSION["company_code"]."'
				AND empStat='RG' and employmentTag IN ('RG','PR','CN') 
				AND empPayGrp='".$_SESSION['pay_group']."'
				AND empPayCat='".$_SESSION['pay_category']."'
				AND tblTtype.compCode='".$_SESSION["company_code"]."'
				AND tblTtype.trnStat='A'
				;";
		
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getDailyLoans($div,$from,$to) {
		if (!empty($from) && !empty($to)) {
			$date = "convert(datetime,convert(varchar(12), dateadded, 101)) between '$from' and '$to'";
		} else {
			$today = date('m/d/Y');
			$date = "convert(datetime,convert(varchar(12), dateadded, 101)) = '$today'";
		}
		 $qryLoans = "Select * from tblEmpLoans where $date
					and empNo IN (Select empNo from tblEmpMast $div) and compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qryLoans);
		return $this->getArrRes($res);		

	}	
	
	function empInfoTypes($empNo) {
		$qryTypes = "Select seqId,typeDesc,type from tblUserDefLookUp where convert(varchar,seqId) IN (Select remarks1 from tblUserDefinedMst where empNo='$empNo') or convert(varchar,seqId) IN (Select remarks2 from tblUserDefinedMst where empNo='$empNo') or convert(varchar,seqId) IN (Select remarks3 from tblUserDefinedMst where empNo='$empNo')";
		$res = $this->execQry($qryTypes);
		return $this->getArrRes($res);	
	}
	//function for Other Info
	function empOtherInfo($empNo,$act="") {
		$qryInfos = "SELECT tblUserDefinedMst.empNo, tblUserDefinedMst.date1, tblUserDefinedMst.date2, tblUserDefinedMst.catCode, 
                      tblUserDefinedMst.remarks1, tblUserDefinedMst.remarks2, tblUserDefinedMst.remarks3 FROM tblUserDefinedMst  where empNo='$empNo'";
  		$res = $this->execQry($qryInfos);
		if ($act =="") {
			$arrInfo = $this->getArrRes($res);	
			$arrFields = array('catcode','date1','date2','remarks1','remarks2','remarks3');
			$i=0;
			$arrTypes = $this->empInfoTypes($empNo);
			foreach($arrInfo as $valInfo) {
				foreach($arrTypes as $valType) {
					if($valType['seqId'] == $valInfo['remarks1']) {
						$arrFields['remarks1'][$i] = $valType['typeDesc'];
					}
					if($valType['seqId'] == $valInfo['remarks2']) {
						$arrFields['remarks2'][$i] = $valType['typeDesc'];
					}
					if($valType['seqId'] == $valInfo['remarks3']) {
						$arrFields['remarks3'][$i] = $valType['typeDesc'];
					}
				}
				$arrFields['rem1'][$i]		= $valInfo['remarks1'];
				$arrFields['rem2'][$i] 		= $valInfo['remarks2'];
				$arrFields['rem3'][$i] 		= $valInfo['remarks3'];
				$arrFields['catcode'][$i] 	= $valInfo['catCode'];
				$arrFields['date1'][$i] 	= $valInfo['date1'];
				$arrFields['date2'][$i] 	= $valInfo['date2'];
				$i++;
			}
			return $arrFields;
		} else {
			return $this->getRecCount($res);	
		}	
	}
	function getEmpCOEInfo($empNo) {
		$confaccess=$_SESSION['Confiaccess'];
		if($confaccess == 'N'){
			$confi = "and tblEmpMast.empPayCat ='3'";
		}elseif ($confaccess == 'Y') {
			$confi = "and tblEmpMast.empPayCat ='2'";
		}
		else $confi = '';


		$qryEmpInfo = "Select empFirstName,empMidName,empLastName,dateHired,dateResigned,endDate,empBrnCode,empSex from tblEmpMast where empNo='$empNo' $confi and compCode='{$_SESSION['company_code']}'";
		return $this->getSqlAssoc($this->execQry($qryEmpInfo));	
	}	

	function reportSeries($reportType){
		$year = date("Y");
		$qry = "Select * from tblCSCounter";
		$resVal = $this->getSqlAssoc($this->execQry($qry));
		$resCnt = $this->getRecCount($this->execQry($qry));
		$fieldsUpdate = "";
		$fieldsInsert = "";
		if($reportType=="clearance"){
			$yearData = substr($resVal['ctrClearance'],0,4);
			$ctrData = substr($resVal['ctrClearance'],5,4); 
			if($year==$yearData){
				$ctr = 	$yearData."-".sprintf('%04s',$ctrData+1);
			}
			else{
				$ctr = $year."-0001";	
			}
			$fieldsUpdate.="set ctrClearance='{$ctr}'";
			$fieldsInsert.="(compCode,ctrClearance,ctrSurvey) values('{$_SESSION['company_code']}','{$ctr}','')";
			
		}
		if($reportType=="survey"){
			$yearData = substr($resVal['ctrSurvey'],0,4);
			$ctrData = substr($resVal['ctrSurvey'],5,4); 
			if($year==$yearData){
				$ctr = 	$yearData."-".sprintf('%04s',$ctrData+1);
			}
			else{
				$ctr = $year ."-0001";	
			}
			$fieldsUpdate.="set ctrSurvey='{$ctr}'";
			$fieldsInsert.="(compCode,ctrClearance,ctrSurvey) values('{$_SESSION['company_code']}','','{$ctr}')";	
		}
		
		if($resCnt>0){
			$qryData = "Update tblCSCounter $fieldsUpdate where compCode='{$_SESSION['company_code']}' ";
		}
		else{
			$qryData = "Insert Into tblCSCounter $fieldsInsert";
		}
		
		if($this->execQry($qryData)){
			return $ctr;	
		}
		else{
			return false;	
		}
	}

}
?>