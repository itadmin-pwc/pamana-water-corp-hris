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
	
	function getBasicTotalDed($compCode,$empNo,$year,$number,$trnCode,$tbl,$lstEmpNo) {
		if ($empNo>"") $empNoNew = " AND ($tbl.empNo = '$empNo') "; else $empNoNew = "";
		$inStment = ($lstEmpNo!=""?" and empNo in (".$lstEmpNo.")":""); 
		$w_pType = ($trnCode!=""?" AND (tblPayTransType.trnCode = '$trnCode')":"");
		
		$qry = "SELECT SUM($tbl.trnAmountD) AS totAmt
				FROM $tbl INNER JOIN
                tblPayTransType ON $tbl.trnCode = tblPayTransType.trnCode
				WHERE (tblPayTransType.compCode = '$compCode') AND ($tbl.compCode = '$compCode') $empNoNew AND 
                ($tbl.pdYear = '$year') AND ($tbl.pdNumber = '$number') 
				$w_pType
				$inStment
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
					empBankCd='03' AND
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
						empBankCd='03' AND
						".$reportType.".pdYear = '$year' AND ".$reportType.".pdNumber = '$number' $empNoNew 
					GROUP BY ".$reportType.".empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName ";
		}
		
		
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	/*End of Denomination Function*/
	
	/*Payroll Register Functions*/
	function chkEmpPaySumm($empDiv,$empDept,$empSect,$empNo,$year,$number,$reportType,$chk)
	{
	
		if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDivCode = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		
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
							$empNo1 $empName1 $empDiv1 $empDept1 $empSect1
							";
		
		$resPayrollSum = $this->execQry($qrychkPayrollSum);
		return $this->getSqlAssoc($resPayrollSum);
	}
	/*End of Payroll Register Functions*/

	/*payroll Register by Department*/
	function chkEmpPaySummDept($array, $chk)
	{
		$from = date('Y-m-d',strtotime($array["fromDate"]));
		$to = date('Y-m-d',strtotime($array["toDate"]));	
		$qrypdNum = "SELECT    pdNumber
					FROM       tblPayPeriod
					WHERE     (pdPayable BETWEEN '$from' AND '$to') AND (payGrp = '".$_SESSION["pay_group"]."')";
					

		if ($array["empBrnCode"]!="0") {$empBrnCode1 = " AND (empBrnCode = '".$array["empBrnCode"]."')";} else {$empBrnCode1 = "";}
		if ($array["empDiv"]>"" && $array["empDiv"]>0) {$empDiv1 = " AND (empDivCode = '".$array["empDiv"]."')";} else {$empDiv1 = "";}
		if ($array["empDept"]>"" && $array["empDept"]>0) {$empDept1 = " AND (empDepCode = '".$array["empDept"]."')";} else {$empDept1 = "";}
		if ($array["empSect"]>"" && $array["empSect"]>0) {$empSect1 = " AND (empSecCode = '".$array["empSect"]."')";} else {$empSect1 = "";}
		
		
			$tbl = "tblPayrollSummaryHist";
		
		if($chk==1)
			$field = "count(empNo) as totEmp";
		else
			$field = "*";
			
		$qrychkPayrollSum = "Select $field from $tbl
							where compCode='".$_SESSION["company_code"]."'
							and pdYear='".date("Y", strtotime($array["fromDate"]))."'
							and pdNumber in ($qrypdNum)
							and payGrp='".$_SESSION["pay_group"]."'
							$empBrnCode1 $empDiv1 $empDept1 $empSect1
							";
		//echo $qrychkPayrollSum ;
		$resPayrollSum = $this->execQry($qrychkPayrollSum);
		return $this->getSqlAssoc($resPayrollSum);	
	}
	/*End of Function of PayRegister per Department*/

	

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
				empStat NOT IN('RS','IN','TR') 
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
                (tblEmpMast.compCode = '{$this->compCode}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') 
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
				(tblEmpMast.empDiv = '{$empDiv}')  AND (tblDepartment.divCode = '{$empDiv}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') 
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
                (tblPayCat.payCatStat = 'A') AND (tblEmpMast.empPayGrp = '{$empGrp}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') $empCatNew";
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
	
	function PaySlip($compCode,$empNo,$pdyear,$pdnumber,$payPd,$branch,$loc) {
		if (!$this->getPeriod($payPd)) {
			$hist = "hist";
		}
		if ($empNo!="") {
			
			$sqlpayslip="Select * from tblPayrollSummary$hist where empNo='$empNo' and pdYear='$pdyear' and pdNumber='$pdnumber' and compCode='$compCode' and payGrp='" . $_SESSION['pay_group'] . "' and payCat='" . $_SESSION['pay_category'] . "'";
		}
		else {
			if ($branch != 0) {
				$filter = "Select empNo from tblEmpMast";
				if ($loc == 1) {
					$filter .= " Where empBrnCode = '$branch' AND empLocCode='0001'";
				} elseif ($loc == 2) {
					$filter .= " Where empBrnCode = '$branch' AND empLocCode='$branch'";
				}
				$filter = " AND empNo IN ($filter)"; 
			}

		
			$sqlpayslip="Select * from tblPayrollSummary$hist where pdYear='$pdyear' and pdNumber='$pdnumber' and compCode='$compCode' and payGrp='" . $_SESSION['pay_group'] . "' and payCat='" . $_SESSION['pay_category'] . "' $filter";
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
				AND empStat NOT IN('RS','IN','TR')
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
			$from = date('Y-m-d',strtotime($from));
			$to = date('Y-m-d',strtotime($to));
			$date = "cast(dateadded as date) between '$from' and '$to'";
		} else {
			$today = date('Y-m-d');
			$date = "cast(dateadded as date) = '$today'";
		}
		$qryLoans = "Select * from tblEmpLoans where $date
					and empNo IN (Select empNo from tblEmpMast $div) and compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qryLoans);
		return $this->getArrRes($res);		

	}	
	function DedLoans($compCode,$empNo,$pdyear,$pdnumber,$payPd,$branch,$loc,$lonType="") {
		if (!$this->getPeriod($payPd)) {
			$hist = "hist";
		}
		if ($empNo != "") {
			$empNo = " and empNo='$empNo' ";
		} else {
			if ($branch != 0) {
				$filter = "Select empNo from tblEmpMast";
				if ($loc == 1) {
					$filter .= " Where empBrnCode = '$branch' AND empLocCode='0001'";
				} elseif ($loc == 2) {
					$filter .= " Where empBrnCode = '$branch' AND empLocCode='$branch'";
				}
				$filter = " AND empNo IN ($filter)"; 
			}		
		}
		$lonTypeFilter = ($lonType !="") ? " AND lonTypeCd='$lonType'":"";
		$sqlLoans = "Select * from tblEmploansDtl$hist where compCode='$compCode' $empNo and pdYear='$pdyear' and pdNumber='$pdnumber' and trnCat='{$_SESSION['pay_category']}' and trnGrp='{$_SESSION['pay_group']}' $filter and dedTag IN ('Y','P') $lonTypeFilter";		
		$res = $this->execQry($sqlLoans);
		return $this->getArrRes($res);
	}
	function GovLoans($compCode,$empNo,$pdyear,$pdnumber,$payPd,$lonTypeCd,$branch,$loc) {
		if (!$this->getPeriod($payPd)) {
			$hist = "hist";
		}
		if ($empNo != "") {
			$empNo = " and empNo='$empNo' ";
		} else {
			if ($branch != 0) {
				$filter = "Select empNo from tblEmpMast";
				if ($loc == 1) {
					$filter .= " Where empBrnCode = '$branch' AND empLocCode='0001'";
				} elseif ($loc == 2) {
					$filter .= " Where empBrnCode = '$branch' AND empLocCode='$branch'";
				}
				$filter = " AND empNo IN ($filter)"; 
			}		
		}
		if ($lonTypeCd != "") {
			$lonTypeCd = " and lonTypeCd like '$lonTypeCd%'";
		} else {
			$lonTypeCd = "";
		}
		$sqlLoans = "Select * from tblEmploansDtl$hist where compCode='$compCode' $empNo $lonTypeCd and pdYear='$pdyear' and pdNumber='$pdnumber' and trnCat='{$_SESSION['pay_category']}' and trnGrp='{$_SESSION['pay_group']}' and dedTag='Y' $filter ";		
		$res = $this->execQry($sqlLoans);
		return $this->getArrRes($res);
	}
	function GLEntries($arrPd,$branch) {
		if ($branch != 0) {
//				$filter = " AND strCode='".$branchInfo['glCodeHO']."'";
				$filter = " AND strCode='$branch'";
		}	
		$qryGL = "Select * from tblPayJournal where compCode='{$_SESSION['company_code']}' 
				AND pdYear='{$arrPd['pdYear']}' 
				AND pdNumber='{$arrPd['pdNumber']}' 
				AND payGrp='{$_SESSION['pay_group']}'
				AND payCat='{$_SESSION['pay_category']}' $filter";
		$res = $this->execQry($qryGL);
		return $this->getArrRes($res);
	}
	
	function LastPay($compCode,$empNo,$pdyear,$pdnumber,$payPd,$branch,$loc) {
		if (!$this->getPeriod($payPd)) {
			$hist = "hist";
		}
		if ($empNo!="") {
			
			$sqlLastPay="Select * from tblLastPayEmp where empNo='$empNo' and pdYear='$pdyear' and pdNumber='$pdnumber' and compCode='$compCode'";
		}
		else {
			if ($branch != 0) {
				$filter = "Select empNo from tblEmpMast";
				if ($loc == 1) {
					$filter .= " Where empBrnCode = '$branch' AND empLocCode='0001'";
				} elseif ($loc == 2) {
					$filter .= " Where empBrnCode = '$branch' AND empLocCode='$branch'";
				}
				$filter = " AND empNo IN ($filter)"; 
			}
			$sqlLastPay="Select * from tblLastPayEmp where pdYear='$pdyear' and pdNumber='$pdnumber' and compCode='$compCode' $filter";
		}
		$res = $this->execQry($sqlLastPay);
		return $this->getArrRes($res);
	}
	function RFP($compCode,$empNo,$pdyear,$pdnumber) {
		if ($empNo!="") {
			$sqlRFP="Select * from tblLastPayEmp where empNo='$empNo' and pdYear='$pdyear' and pdNumber='$pdnumber' and compCode='$compCode'";
		}
		else {
			$sqlRFP="Select * from tblLastPayEmp where pdYear='$pdyear' and pdNumber='$pdnumber' and compCode='$compCode'";
		}
		$res = $this->execQry($sqlRFP);
		return $this->getArrRes($res);
	}	
	function TotSal($compCode,$pdyear,$pdnumber,$payPd,$brnCode,$act="") {
		if ($brnCode !=0) {
			$empBrnCode = " AND empBrnCode='$brnCode'";
		}
		if ($act == "") {
			$grpbybranch = "empBrnCode,";
		}
		if (!$this->getPeriod($payPd)) {
			$hist = "hist";
		}
		 $sqlTotSal="Select $grpbybranch bankDesc,bankCd,sum(netSalary) as  Salary, sum(sprtAllow) as Allow, sum(netSalary) +sum(sprtAllow) as total from tblPayrollSummary$hist inner join tblPaybank on  tblPayrollSummary$hist.compCode=tblPaybank.compCode and tblPayrollSummary$hist.empBnkCd=tblPayBank.bankCd where pdYear='$pdyear' and pdNumber='$pdnumber' and payGrp='{$_SESSION['pay_group']}' and payCat='{$_SESSION['pay_category']}' and tblPayrollSummary$hist.compCode='$compCode' $empBrnCode  group by $grpbybranch BankDesc,bankCd ";
		$res = $this->execQry($sqlTotSal);
		return $this->getArrRes($res);
	}		
	function BranchGrp($payPd,$pdyear,$pdnumber,$brnCode) {
		if (!$this->getPeriod($payPd)) {
			$hist = "hist";
		}	
		if ($brnCode !=0) {
			$empBrnCode = " AND brnCode='$brnCode'";
		}
		$sqlBranch = "Select brnCode,brnShortDesc from tblBranch where  compCode ='{$_SESSION['company_code']}' AND brnCode IN (Select empBrnCode from tblPayrollSummary$hist where compCode='{$_SESSION['company_code']}' AND pdYear='$pdyear' and pdNumber='$pdnumber' and payGrp='{$_SESSION['pay_group']}' and payCat='{$_SESSION['pay_category']}' $empBrnCode) order by brnShortDesc";
		return $this->getArrRes($this->execQry($sqlBranch));

	}
	function getCompTotalCBC($pdyear,$pdnumber,$hist,$act="") {
		$sql = "Select sum(netSalary)+sum(sprtAllow) as total from tblPayrollSummary$hist where pdYear='$pdyear' and pdNumber='$pdnumber' and payGrp='{$_SESSION['pay_group']}' and empBnkCd=7 and payCat<>9";	
		$res = $this->getSqlAssoc($this->execQry($sql));
		return $res['total'];
		
	}
	
	
}

?>