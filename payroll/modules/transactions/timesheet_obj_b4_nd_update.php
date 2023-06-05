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
	
	var $compCode2;
	var $empNo2;
	var $tsDate2;
	var $hrsAbsent2;
	var $hrsTardy2;
	var $hrsUt2;
	var $hrsOtLe82;
	var $hrsOtGt82;
	var $ihrsNdLe82;
	var $hrsNdGt82;
	var $tsRemarks2;
	
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
			    compCode = '$compCode' AND
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
					
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
		$qry = "SELECT *,pdNumber % 2 AS modSked FROM tblPayPeriod 
				WHERE pdSeries = '$payPd' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	/*Time Sheet Function*/
	function getTimeSheet($empNo,$from,$to,$reportType,$lstEmpNo) {
		if ($empNo>"") $empNoNew = " AND empNo = '$empNo'"; else $empNoNew = "";
		$inStment = ($lstEmpNo!=""?" and empNo in (".$lstEmpNo.")":""); 
				 
		$qry = "SELECT * FROM ".$reportType." 
				WHERE compCode = '".$_SESSION["company_code"]."' AND 
				empPayGrp = '{$_SESSION['pay_group']}' AND 
				empPayCat = '{$_SESSION['pay_category']}' AND 
				tsStat = 'A' AND (tsDate >= '$from') AND (tsDate <= '$to')
				$inStment
				$empNoNew ORDER BY tsDate ASC ";
		
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	/*End of Time Sheet Functions*/
	
	function getTimeSheetTotal($compCode,$empNo,$from,$to) {
		if ($empNo>"") $empNoNew = " AND (empNo = '$empNo') "; else $empNoNew = "";
		$qry = "SELECT SUM(hrsAbsent) AS totHrsAbsent, SUM(hrsTardy) AS totHrsTardy, SUM(hrsUt) AS totHrsUt, SUM(hrsOtLe8) AS totHrsOtLe8, SUM(hrsOtGt8) 
                      AS totHrsOtGt8, SUM(hrsNdLe8) AS totHrsNdLe8, SUM(hrsNdGt8) AS totHrsNdGt8, SUM(amtAbsent) AS totAmtAbsent, SUM(amtTardy) AS totAmtTardy, 
                      SUM(amtUt) AS totAmtUt, SUM(amtOtLe8) AS totAmtOtLe8, SUM(amtOtGt8) AS totAmtOtGt8, SUM(amtNdLe8) AS totAmtNdLe8, SUM(amtNdGt8) 
                      AS totAmtNdGt8
				FROM ".$_GET["reportType"]."
				WHERE (compCode = '$compCode')  AND empPayGrp = '{$_SESSION['pay_group']}'
			     		   AND empPayCat = '{$_SESSION['pay_category']}' AND (tsStat = 'A') $empNoNew AND (tsDate >= '$from') AND 
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
		$qry = "SELECT * FROM tblDeductions 
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
	function getTranEarningsTotal($compCode,$refNo) {
		if ($refNo>"") $refNoNew = " AND refNo = '$refNo' "; else $refNoNew = "";
		$qry = "SELECT SUM(trnAmount) AS totAmt FROM tblEarnTranDtl
				WHERE (compCode = '$compCode') $refNoNew ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getTranDeductionsTotal($compCode,$refNo) {
		if ($refNo>"") $refNoNew = " AND refNo = '$refNo' "; else $refNoNew = "";
		$qry = "SELECT SUM(trnAmount) AS totAmt FROM tblDedTranDtl
				WHERE (compCode = '$compCode') $refNoNew ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEarnTranHdr($compCode,$refNo) {
		$qry = "SELECT * FROM tblEarnTranHeader
				WHERE (compCode = '$compCode') AND refNo = '$refNo' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getDedTranHdr($compCode,$refNo) {
		$qry = "SELECT * FROM tblDedTranHeader
				WHERE (compCode = '$compCode') AND refNo = '$refNo' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getBranchGroup($compCode){
		$qrygetBranch = "SELECT MAX(brnDefGrp) AS brnDefGrp FROM tblBranch 
						WHERE compCode = '{$compCode}'
						GROUP BY compCode";
		$resgetBranch = $this->execQry($qrygetBranch);
		return $this->getSqlAssoc($resgetBranch);
	}
	
	function getAllPeriod2($compCode,$groupType){ 
		$qry = "SELECT TOP 100 PERCENT MAX(CONVERT(VARCHAR, pdPayable, 101)) AS pdPayable,  CONVERT(varchar, MAX(pdFrmDate), 101) + '-' + CONVERT(varchar, 
                      MAX(pdToDate), 101)+ '-' + CONVERT(varchar, 
                      MAX(pdNumber), 101)+ '-' + CONVERT(varchar, 
                      MAX(pdYear), 101) AS pdFrmToDate
			   FROM tblPayPeriod
			   WHERE (compCode = '$compCode') AND (payGrp = '$groupType')
			   GROUP BY pdPayable
			   ORDER BY MAX(pdPayable)";
			   
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function addParadox(){
		if ($this->hrsAbsent2>"") $hrsA   = $this->hrsAbsent2; else $hrsA   = "0";
		if ($this->hrsTardy2>"")  $hrsT   = $this->hrsTardy2;  else $hrsT   = "0";
		if ($this->hrsUt2>"")     $hrsU   = $this->hrsUt2;     else $hrsU   = "0";
		if ($this->hrsOtLe82>"")  $hrsOtL = $this->hrsOtLe82;  else $hrsOtL = "0";
		if ($this->hrsOtGt82>"")  $hrsOtG = $this->hrsOtGt82;  else $hrsOtG = "0";
		if ($this->hrsNdLe82>"")  $hrsNdL = $this->hrsNdLe82;  else $hrsNdL = "0";
		if ($this->hrsNdGt82>"")  $hrsNdG = $this->hrsNdGt82;  else $hrsNdG = "0";
		$qry                = "INSERT INTO tblTsParadox(
								compCode,empNo,tsDate,
								hrsAbsent,hrsTardy,hrsUt,
								hrsOtLe8,hrsOtGt8,hrsNdLe8,
								hrsNdGt8, tsRemarks
							  )VALUES(
							  	'{$this->compCode2}','{$this->empNo2}','{$this->dateFormat($this->tsDate2)}',
							  	'{$hrsA}','{$hrsT}','{$hrsU}',
							  	'{$hrsOtL}','{$hrsOtG}','{$hrsNdL}',
							  	'{$hrsNdG}','{$this->tsRemarks2}'
							  )";
		$res = $this->execQry($qry);
		/*
		if($res){
			return true;
		}
		else {
			$this->errorLog(mysql_get_last_message(),$qry,__LINE__,'timesheet_obj.php');
			return false;
		}
		*/	
	}
	function getEmpComp($empNo) {
		$qry = "SELECT * FROM tblEmpMast
				WHERE (empNo = '$empNo') ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function valTsParadox($compCode,$empNo,$tsDate) {
		$qry = "SELECT * FROM tblTsParadox
				WHERE (compCode = '$compCode') AND (empNo = '$empNo') AND (tsDate = '$tsDate') ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function processLoans($compCode,$lonSked,$tsDate,$tsYear,$tsNumber,$tsCat,$tsGrp,$tsYear,$tsNumber,$re_proc) {
		if ($re_proc==1) { ///AND (empPayCat = '$tsCat') AND (empPayGrp = '$tsGrp')
			$earnsTag = "N";
			$this->execQry("DELETE tblEmpLoansDtl WHERE compCode = '".$compCode."' AND trnGrp = '{$tsGrp}' AND trnCat = '{$tsCat}' AND pdYear = '{$tsYear}' AND pdNumber = '{$tsNumber}'");
		} 
		$qry = "SELECT *
				FROM tblEmpLoans INNER JOIN
                tblEmpMast ON tblEmpLoans.empNo = tblEmpMast.empNo
				WHERE (tblEmpLoans.compCode = '$compCode') AND (tblEmpLoans.lonSked = '$lonSked' OR
                      tblEmpLoans.lonSked = 3) AND (tblEmpLoans.lonStart >= '$tsDate') AND (tblEmpLoans.lonEnd >= '$tsDate') AND 
                      (tblEmpLoans.lonStat = 'O') AND (tblEmpLoans.lonCurbal > 0) AND  
			   		  (tblEmpMast.compCode = '$compCode') AND (tblEmpMast.empPayGrp = '$tsGrp') AND 
                      (tblEmpMast.empPayCat = '$tsCat')";
		$res = $this->execQry($qry);
		$arr = $this->getArrRes($res);
		foreach ($arr as $val){
			$a = $val['lonCurbal'];
			$b = $val['lonDedAmt2'];
			
			if ($b > $a) {
				$dedAmt = $a;
				$lastPay = 1;
			} else {
				
				$dedAmt = $b;
				$lastPay = 0;
			}
			$qryDtl              = "INSERT INTO tblEmpLoansDtl(
									compCode,empNo,lonTypeCd,
									lonRefNo,pdYear,pdNumber,
									trnCat,trnGrp,trnAmountD,
									dedTag,lonLastPay
								  ) VALUES(
									'$compCode','{$val['empNo']}','{$val['lonTypeCd']}',
									'{$val['lonRefNo']}','{$tsYear}','{$tsNumber}',
									'{$tsCat}','{$tsGrp}',$dedAmt,'',
									'{$this->dateFormat($val['lonLastPay'])}'
								  )";
			$resDtl = $this->execQry($qryDtl); //$this->dateFormat($this->tsDate2)
			#################################################################################################
			/*
			$lonPayments = $val['lonPayments'] + $dedAmt;
			$lonCurbal = $val['lonCurbal'] - $dedAmt;
			$lonPaymentNo = $val['lonPaymentNo'] + 1;
			$qryUpdt = "UPDATE tblEmpLoans SET ";
							if ($lastPay == 1) { #### completed loans
								$qryUpdt .= "lonStat = 'C', ";
							}
							$qryUpdt .= "lonLastPay = '".$this->dateFormat($tsDate)."', ";
							$qryUpdt .= "lonPayments = '".$lonPayments."', ";
							$qryUpdt .= "lonPaymentNo = '".$lonPaymentNo."', ";
							$qryUpdt .= "lonCurbal = '".$lonCurbal."' ";
							$qryUpdt .= "WHERE compCode = '".$compCode."' AND empNo = '{$val['empNo']}' AND lonTypeCd = '{$val['lonTypeCd']}' AND lonRefNo = '{$val['lonRefNo']}'";	
							//echo $qryUpdt . "<br>";
			$resUpdt = $this->execQry($qryUpdt);
			*/
		}
		$qryUpdtPd = "UPDATE tblPayPeriod SET ";
						$qryUpdtPd .= "pdLoansTag = 'Y', ";
						$qryUpdtPd .= "pdEarningsTag = '$earnsTag' ";
						$qryUpdtPd .= "WHERE compCode = '".$compCode."' AND payGrp = '{$tsGrp}' AND payCat = '{$tsCat}' AND pdYear = '{$tsYear}' AND pdNumber = '{$tsNumber}'";	
		$resUpdtPd = $this->execQry($qryUpdtPd);
		//echo $qryUpdtPd . "<br>";
	}
	
	/*Common Function*/
	function getListofBranches()
	{
		$qryBranch = "Select * from tblBranch where compCode='".$_SESSION["company_code"]."' and brnStat='A' order by brnDesc";
		$resBranch = $this->getArrRes($this->execQry($qryBranch));
		return $resBranch;
	}
	
	
	/*Retrieve tblTimeSheetHist of the Employee*/
	function rettblTsHist($empNo, $tsDate)
	{
		$qryRetTsHist = "Select * from tblTimeSheetHist
						where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."'
						and tsDate='".date("Y-m-d", strtotime($tsDate))."' and empPayGrp='".$_SESSION["pay_group"]."' 
						and empPayCat='".$_SESSION["pay_category"]."'";
		$resRetTsHist = $this->execQry($qryRetTsHist);
		return $this->getSqlAssoc($resRetTsHist);
		
	}

	/*Get Employee Restday*/
	function getEmpRestDay($empNo, $tranDate)
	{
		$arr_empInfo = $this->getUserInfo($_SESSION["company_code"],$empNo,'');
		$where = " and (brnCode='".$arr_empInfo["empBrnCode"]."' or brnCode='0')";
		
		//Current Employee RestDay
		$arr_empCurrRD = $this->getUserInfo($_SESSION["company_code"],$empNo,'');
		$arr_empCurrRD =  explode(",",$arr_empCurrRD["empRestDay"]);
		if(in_array($tranDate,$arr_empCurrRD))
		{
			$arr_holRd = $this->detDayType($tranDate, $where);
			if($arr_holRd["dayType"]=='04')
				$empDayType = "06";
			elseif($arr_holRd["dayType"]=='03')
				$empDayType = "05";
			else
				$empDayType = "02";
		}
		else
		{
			
			//Previous Employee TimeSheet
			$qryPrevRd = "Select * from tblEmpRestDayBckUp
							where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' and pdYear='".date("Y", strtotime($tranDate))."'";
			$resPrevRd = $this->execQry($qryPrevRd );
			$arrPrevRd = $this->getArrRes($resPrevRd);
			foreach($arrPrevRd as $arrPrevRd_val)
			{
				$empPrevRd.=$arrPrevRd_val["empRestDay"].",";
			}
			$empPrevRD = substr($empPrevRd,0,strlen($empPrevRd) - 1);
			$empPrevRD =  explode(",",$empPrevRD);
			
			if(in_array($tranDate,$empPrevRD)){
			
				$arr_holRd = $this->detDayType($tranDate, $where);
				
				if($arr_holRd["dayType"]=='04')
					$empDayType = "06";
				elseif($arr_holRd["dayType"]=='03')
					$empDayType = "05";
				else
					$empDayType = "02";
				
			}
			else
			{
				$arr_holRd = $this->detDayType($tranDate, $where);
				if($arr_holRd["dayType"]=='04')
					$empDayType = "04";
				elseif($arr_holRd["dayType"]=='03')
					$empDayType = "03";
				else
					$empDayType = "01";
			}
			
		}
		return $empDayType;
	}
	
	function getDayTypePrem($dayType)
	{
		$qryDayTypePrem = "Select * from tblOtPrem where dayType='".$dayType."'";
		$resDayTypePrem = $this->execQry($qryDayTypePrem);
		return $this->getSqlAssoc($resDayTypePrem);
	}
	
	function checktblTsCorr($empNo, $where)
	{
		$chktblTsCorr = "Select * from tblTsCorr
							where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' ".$where."";
		$restblTsCorr = $this->execQry($chktblTsCorr);
		return $this->getRecCount($restblTsCorr);
	}
	
	function instbltsCorr($typeProc,$empNo, $tsDate, $payPd, $empPayGrp, $empPayCat, $hrsReg, $hrsAbsent, $hrsTardy, $hrsUt, $hrsOtLe8, $hrsOtGt8, $hrsNdLe8, $hrsNdGt8,$tsStat,$empHrate)
	{
		$arrPayPd = $this->getSlctdPd($_SESSION["company_code"],$payPd);
		$dayType = $this->getEmpRestDay($empNo, $tsDate);
		
		if($dayType!=""){
			$empDTypePrem = $this->getDayTypePrem($dayType);
			$empOtLe8Prem = $empDTypePrem["otPrem8"];
			$empOtGt8Prem = $empDTypePrem["otPremOvr8"];
			$empNdLe8Prem = $empDTypePrem["ndPrem8"];
			$empNdGt8Prem = $empDTypePrem["ndPremOvr8"];
		}else{
			$empOtLe8Prem = 0;
			$empOtGt8Prem = 0;
			$empNdLe8Prem = 0;
			$empNdGt8Prem = 0;
		}
		
		$adjBasic 	= $empHrate * ($hrsReg - $hrsAbsent - $hrsTardy - $hrsUt);
		$adjBasic		= sprintf("%01.2f", $adjBasic);
		
		$amtotLe8	= $hrsOtLe8*$empHrate*$empOtLe8Prem;
		$amtotGt8	= $hrsOtGt8*$empHrate*$empOtGt8Prem;
		$adjOt		= $amtotLe8 + $amtotGt8;
		$adjOt		= sprintf("%01.2f", $adjOt);
		
		$amtndLe8	= $hrsNdLe8*$empHrate*$empNdLe8Prem;
		$amtndGt8	= $hrsNdGt8*$empHrate*$empNdGt8Prem;
		$adjNd		= $amtndLe8 + $amtndGt8;
		$adjNd		= sprintf("%01.2f", $adjNd);
		
		
		if($typeProc=='1')
		{
			$qryInstblTsCorr = "Insert into tblTsCorr	(compCode, empNo, tsDate, pdYear, pdNumber, empPayGrp, empPayCat, dayType, 
														hrsReg, hrsAbsent, hrsTardy, hrsUt, hrsOtLe8, hrsOtGt8, hrsNdLe8, hrsNdGt8, 
														adjBasic, adjOt, adjNd, tsStat) 
								values('".$_SESSION["company_code"]."','".$empNo."', '".$tsDate."', '".$arrPayPd["pdYear"]."', '".$arrPayPd["pdNumber"]."', '".$empPayGrp."', '".$empPayCat."', '".$dayType."',
														".($hrsReg!=""?"'".$hrsReg."'":"NULL").", ".($hrsAbsent!=""?"'".$hrsAbsent."'":"NULL").", ".($hrsTardy!=""?"'".$hrsTardy."'":"NULL").", ".($hrsUt!=""?"'".$hrsUt."'":"NULL").", 
														".($hrsOtLe8!=""?"'".$hrsOtLe8."'":"NULL").", ".($hrsOtGt8!=""?"'".$hrsOtGt8."'":"NULL").", ".($hrsNdLe8!=""?"'".$hrsNdLe8."'":"NULL").", ".($hrsNdGt8!=""?"'".$hrsNdGt8."'":"NULL").",
														".($adjBasic!=""?"'".$adjBasic."'":"NULL").", ".($adjOt!=""?"'".$adjOt."'":"NULL").", 
														".($adjNd!=""?"'".$adjNd."'":"NULL").", '".$tsStat."')";
		}
		else
		{
			$qryInstblTsCorr = "Update tblTsCorr set  pdYear='".$arrPayPd["pdYear"]."', pdNumber='".$arrPayPd["pdNumber"]."',  
														hrsReg=".($hrsReg!=""?"'".$hrsReg."'":"NULL").", hrsAbsent=".($hrsAbsent!=""?"'".$hrsAbsent."'":"NULL").", 
														hrsTardy=".($hrsTardy!=""?"'".$hrsTardy."'":"NULL").", hrsUt=".($hrsUt!=""?"'".$hrsUt."'":"NULL").", 
														hrsOtLe8=".($hrsOtLe8!=""?"'".$hrsOtLe8."'":"NULL").", hrsOtGt8=".($hrsOtGt8!=""?"'".$hrsOtGt8."'":"NULL").", 
														hrsNdLe8=".($hrsNdLe8!=""?"'".$hrsNdLe8."'":"NULL").", hrsNdGt8=".($hrsNdGt8!=""?"'".$hrsNdGt8."'":"NULL").", 
														adjBasic=".($adjBasic!=""?"'".$adjBasic."'":"NULL").", adjOt=".($adjOt!=""?"'".$adjOt."'":"NULL").", 
														adjNd=".($adjNd!=""?"'".$adjNd."'":"NULL").", tsStat='".$tsStat."' 
								where empNo='".$empNo."' and tsDate='".$tsDate."'";
		}
		$resInstblTsCorr = $this->execQry($qryInstblTsCorr);
		if($resInstblTsCorr){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getPeriodGtOpnPer($opnPeriod)
	{
		$qryOpnPeriod = "Select compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate from tblPayPeriod where compCode='".$_SESSION["company_code"]."' and paygrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."' and pdYear='".date("Y", strtotime($opnPeriod))."' and pdPayable>='".date("Y-m-d", strtotime($opnPeriod))."'";
		$resOpnPeriod = $this->execQry($qryOpnPeriod);
		return $this->getArrRes($resOpnPeriod);				
	}
	
	function getPdSeries($pdYear,$pdNumber) {
		$qry = "SELECT * FROM tblPayPeriod 
				WHERE compCode='".$_SESSION["company_code"]."'
				and payGrp='".$_SESSION["pay_group"]."'
				and payCat='".$_SESSION["pay_category"]."'
				and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function gettblTsCorrData($empNo, $tsDate)
	{
		$chktblTsCorr = "Select * from tblTsCorr
							where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' and tsDate='".date("Y-m-d", strtotime($tsDate))."'";
		$restblTsCorr = $this->execQry($chktblTsCorr);
		return $this->getSqlAssoc($restblTsCorr);
	}
	
	function deleteTsCorr($empNo, $tsDate)
	{
		$deleteTsCorr = "Delete from tblTsCorr
							where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' and tsDate='".date("Y-m-d", strtotime($tsDate))."'";
		
		$restblTsCorr = $this->execQry($deleteTsCorr);
	}
	
	function getEquivAllwCode($oldAllowCode)
	{
		
		$qry = "SELECT * FROM tblAllowTypeConvTbl 
				WHERE allowCodeOld = '".str_replace("'","''",stripslashes($oldAllowCode))."'";
		$res = $this->execQry($qry);
		
		if($this->getRecCount($res) > 0)
			return $this->getSqlAssoc($res);
		else
			return "0";
	}
	
	function WriteFile($file_name, $str_path, $file_cont)
	{
		$fh = fopen($str_path.'/'.$file_name, 'w') or die('can not write file!');
		fwrite($fh, $file_cont);
		fclose($fh);
	}
}

?>