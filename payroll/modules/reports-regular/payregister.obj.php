<?
class payRegisterObj extends commonObj {
	
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
	
	function payReg($payPdSlctd,$pdNumber,$cmbBank,$empNo,$txtEmpName,$nameType,$cmbDiv,$cmbDept,$cmbSect,$reportType)
	{
		
		if($reportType==1)
		{
			$tblPaySum = "tblPayrollSummaryHist";
			$tblYtdData = "tblYtdDataHist";
		}
		else
		{
			$tblPaySum = "tblPayrollSummary";
			$tblYtdData = "tblYtdData";
		}
		
		$qryGetPaySum 	= "SELECT ps.empNo,ps.netSalary,emp.empLastName,emp.empMidName,emp.empFirstName,emp.empTeu,teuAmt,YtdGross,YtdTax,empBankCd,
									bankDesc,emp.compCode,empDivCode,emp.empDepCode,deptDesc,emp.empSecCode,ps.taxWitheld
									FROM ".$tblPaySum." as ps 
									LEFT JOIN tblEmpMast as emp ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
									LEFT JOIN tblPayBank as bank ON bankCd=ps.empBnkCd and bank.compCode='{$_SESSION['company_code']}'
									LEFT JOIN tblDepartment as dept ON deptCode=ps.empDepCode and dept.compCode='{$_SESSION['company_code']}' 
									and deptCode='0' and sectCode='0'
									LEFT JOIN tblTeu as teu ON teuCode=emp.empTeu
									LEFT JOIN ".$tblYtdData." as ytd ON ytd.empNo=ps.empNo and ytd.compCode='{$_SESSION['company_code']}' and ytd.pdYear='{$payPdSlctd}'
									WHERE ps.compCode = '{$_SESSION['company_code']}'
									AND ps.payGrp = '{$_SESSION['pay_group']}'
									AND ps.payCat = '{$_SESSION['pay_category']}'
									AND ps.pdYear = '{$payPdSlctd}'
									AND ps.pdNumber = '{$pdNumber}'"; 
				
		if(trim($cmbBank) != "0"){
			$qryGetPaySum .= "AND ps.empBnkCd = '{$cmbBank}' ";
		}
							
		if(trim($empNo) != ""){
			$qryGetPaySum .= "AND ps.empNo = '{$empNo}' ";
		}
		
		if(trim($txtEmpName) != ""){
			if($nameType == 1){
				$qryGetPaySum .= "AND emp.empLastName LIKE '{$txtEmpName}%' ";
			}
			if($nameType == 2){
				$qryGetPaySum .= "AND emp.empFirstName LIKE '{$txtEmpName}%' ";
			}
			if($nameType == 3){
				$qryGetPaySum .= "AND emp.empMidName LIKE '{$txtEmpName}%' ";
			}
		}
		
		if($cmbDiv != 0){
			$qryGetPaySum .= "AND ps.empDivCode = '{$cmbDiv}' ";
		}
		
		if($cmbDept != 0){
			$qryGetPaySum .= "AND ps.empDepCode = '{$cmbDept}' ";
		}
		if($cmbSect != 0){
			$qryGetPaySum .= "AND ps.empSecCode = '{$cmbSect}' ";
		}
		
		$qryGetPaySum .=  "ORDER BY bankDesc,deptDesc,emp.empLastName";
		echo $qryGetPaySum;
		$resPaySum = $this->execQry($qryGetPaySum);
		return $resPaySum;
	}
	
	function getDatatblEarnings($trnCode,$pdYear,$pdNumber,$reportType)
	{
		$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
		$qryEarnings = "Select * from ".$tbltable." 
						where trnCode='".$trnCode."' and compCode='{$_SESSION['company_code']}'
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'";
		$resEarnings = $this->execQry($qryEarnings);
		$resEarnings = $this->getArrRes($resEarnings);
		return $resEarnings;
	}
	
	function getDatatblEarningsOTND($trnRec,$pdYear,$pdNumber,$reportType)
	{
		$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
		$qryEarningsOTND = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable." 
						where trnCode in (Select trnCode from tblPayTransType where trnRecode='$trnRec') and compCode='{$_SESSION['company_code']}'
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
						group by empNo
						";
		$resEarningsOTND = $this->execQry($qryEarningsOTND);
		$resEarningsOTND = $this->getArrRes($resEarningsOTND);
		return $resEarningsOTND;
	}
	
	function getDatatblEarningsAllow($trnRec,$pdYear,$pdNumber,$reportType)
	{
		$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
		$qryEarningsAllow = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable." 
						where trnCode in (Select trnCode from tblAllowType where compCode='{$_SESSION['company_code']}' and allowTypeStat='A') and compCode='{$_SESSION['company_code']}'
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
						group by empNo
						";
		$resEarningsAllow = $this->execQry($qryEarningsAllow);
		$resEarningsAllow = $this->getArrRes($resEarningsAllow);
		return $resEarningsAllow;
	}
	
	function getDataOthtblEarnings($trnRec,$pdYear,$pdNumber,$reportType)
	{
		$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
		$qryEarningsOth = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable."
						where trnCode in (Select trnCode from tblPayTransType where compCode='{$_SESSION['company_code']}' and trnCat='E' and trnStat='A' and trnEntry='Y' )
						and compCode='{$_SESSION['company_code']}'
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
						group by empNo
						";
		$resEarningsOth = $this->execQry($qryEarningsOth);
		$resEarningsOth = $this->getArrRes($resEarningsOth);
		return $resEarningsOth;
	}
	
	function getDataOthtblDeductions($trnCd,$pdYear,$pdNumber,$reportType)
	{
		$tbltable = ($reportType==1?"tblDeductionsHist":"tblDeductions");
		$qryDeductions = "Select sum(trnAmountD) as totAmountD,empNo from ".$tbltable." where trnCode='".$trnCd."'
						and compCode='{$_SESSION['company_code']}'
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
						group by empNo
						";
		$resDeductions = $this->execQry($qryDeductions);
		$resDeductions = $this->getArrRes($resDeductions);
		return $resDeductions;
	}
	
	function getDataLoanstblDeductions($trnRec,$pdYear,$pdNumber,$reportType)
	{
		$tbltable = ($reportType==1?"tblDeductionsHist":"tblDeductions");
		$qryLoansDeductions = "Select sum(trnAmountD) as totAmountD,empNo from ".$tbltable." 
						where trnCode in (Select trnCode from tblLoanType where compCode='{$_SESSION['company_code']}' and lonTypeStat='A') and compCode='{$_SESSION['company_code']}'
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
						group by empNo
						";
		$resLoansDeductions = $this->execQry($qryLoansDeductions);
		$resLoansDeductions = $this->getArrRes($resLoansDeductions);
		return $resLoansDeductions;
	}
	
	function getDataOthAdjtblDeductions($trnRec,$pdYear,$pdNumber,$reportType)
	{
		$tbltable = ($reportType==1?"tblDeductionsHist":"tblDeductions");
		$qryOthAdjDeductions = "Select sum(trnAmountD) as totAmountD,empNo from ".$tbltable." 
						where trnCode in (Select trnCode from tblPayTransType 
						where trnCode not in (Select trnCode from tblLoanType where compCode='{$_SESSION['company_code']}') and trnCat='D' and trnStat='A' and trnEntry='Y')
						and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' 
						group by empNo
						";
		$resOthAdjDeductions = $this->execQry($qryOthAdjDeductions);
		$resOthAdjDeductions = $this->getArrRes($resOthAdjDeductions);
		return $resOthAdjDeductions;
	}
	
	function compGrossTaxable($reportType,$pdNum,$pdYear)
	{
		$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
		$qrycompGrossTax = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable."
							where trnCode in (SELECT trnCode
							from   tblPayTransType
							where  (compCode = '{$_SESSION['company_code']}') AND (trnCat = 'E') AND (trnTaxCd = 'Y') AND (trnStat = 'A'))
							AND pdNumber='".$pdNum."' and pdYear='".$pdYear."' group by empNo";
		//echo $qrycompGrossTax; 
		$rescompGrossTax = $this->execQry($qrycompGrossTax);
		$rescompGrossTax = $this->getArrRes($rescompGrossTax);
		return $rescompGrossTax;
	}
	
	function compGrossNonTaxable($reportType,$pdNum,$pdYear)
	{
		$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
		$qrycompGrossNonTax = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable."
							where trnCode in (SELECT trnCode
							from   tblPayTransType
							where  (compCode = '{$_SESSION['company_code']}') AND (trnCat = 'E') AND (trnTaxCd = 'N') 
							AND (trnStat = 'A')) AND pdNumber='".$pdNum."' and pdYear='".$pdYear."' 
							group by empNo";
		$rescompGrossNonTax = $this->execQry($qrycompGrossNonTax);
		$rescompGrossNonTax = $this->getArrRes($rescompGrossNonTax);
		return $rescompGrossNonTax;
	}
	
	
	function getSlctdPd($compCode,$payPd) 
	{
		$payPd2 = split("-",$payPd); 
		$qry = "SELECT TOP 100 PERCENT CONVERT(varchar, MONTH(pdPayable)) + '/' + CONVERT(varchar, YEAR(pdPayable)) AS perMonth, pdPayable  FROM tblPayPeriod 
				WHERE pdNumber >= '$payPd2[0]' AND pdNumber <= '$payPd2[1]' AND pdYear = '$payPd2[2]' 
				GROUP BY CONVERT(varchar, MONTH(pdPayable)) + '/' + CONVERT(varchar, YEAR(pdPayable)),pdPayable ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getCompName($compCode)
	{
		$qryCompName = "Select * from tblCompany where compCode='".$compCode."'";
		$rsCompName = $this->execQry($qryCompName);
		return $this->getSqlAssoc($rsCompName);
	}
	
	function getempDeptCnt($divList,$reportType)
	{
		$tbltable = ($reportType==1?"tblPayrollSummaryHist":"tblPayrollSummary");
		$qrygetempDeptCnt = "Select empDivCode,count(empNo) as empCnt,empBnkCd from ".$tbltable."
								where empDivCode IN (".substr($divList,1).")
								group by empDivCode,empBnkCd;
								";	
		$resgetempDeptCnt = $this->execQry($qrygetempDeptCnt);
		return  $this->getArrRes($resgetempDeptCnt);
	}
	
	//get info name 
	function getEmpInfo($tblPaySum,$pdYr,$pdNum,$divList,$empNo){
		$qryGetEmpInfo = "SELECT ps.empNo, emp.empLastName, emp.empMidName, emp.empFirstName
						  FROM $tblPaySum ps 
						  LEFT OUTER JOIN tblEmpMast emp 
						  ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo 
						  LEFT OUTER JOIN tblPayBank bank 
						  ON bank.bankCd = ps.empBnkCd AND bank.compCode ='{$this->session['company_code']}' 
						  LEFT OUTER JOIN tblDepartment dept 
						  ON dept.divCode = ps.empDepCode AND dept.compCode = '{$this->session['company_code']}' AND dept.deptCode = '0' AND dept.sectCode = '0'
						WHERE  (ps.compCode = '{$this->session['company_code']}') 
						AND (ps.payGrp = '{$this->session['pay_group']}') 
						AND (ps.payCat = '{$this->session['pay_category']}') 
						AND (ps.pdYear = '{$pdYr}') 
						AND (ps.pdNumber = '{$pdNum}') 
						AND (ps.empDivCode IN (".substr($divList,1).")) 
						AND (ps.empNo = '{$empNo}')";
		$resGetEmpInfo = $this->execQry($qryGetEmpInfo);
		return $this->getSqlAssoc($resGetEmpInfo);
	}
	
	
	//for overtime breakdown report
	function getOvertimeBrkDwnDetails($type,$tblPaySum,$tblEarn,$pdYr,$pdNum,$divList,$empNo,$trnCode){
			
			$qryGetPaySum2 	= "SELECT ps.empNo,emp.empLastName,emp.empMidName,emp.empFirstName,empBankCd,
									bankDesc,emp.compCode,empDivCode,emp.empDepCode,deptDesc,emp.empSecCode,
									ern.trnAmountE,ern.trnCode
									FROM ".$tblPaySum." as ps 
									LEFT JOIN tblEmpMast as emp ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
									LEFT JOIN tblPayBank as bank ON bankCd=ps.empBnkCd and bank.compCode='{$this->session['company_code']}'
									LEFT JOIN tblDepartment as dept ON divCode=ps.empDepCode and dept.compCode='{$this->session['company_code']}' 
									and deptCode='0' and sectCode='0'
									LEFT JOIN $tblEarn as ern 
									ON emp.compCode = ern.compCode AND ern.pdYear = '{$pdYr}' AND ern.pdNumber = '{$pdNum}' AND ps.empNo = ern.empNo
									WHERE ps.compCode = '{$this->session['company_code']}'
									AND ps.payGrp = '{$this->session['pay_group']}'
									AND ps.payCat = '{$this->session['pay_category']}'
									AND ps.pdYear = '{$pdYr}'
									AND ps.pdNumber = '{$pdNum}' 
									AND empDivCode IN  (".substr($divList,1).") 
									AND ern.trnAmountE <> 0
									AND ern.trnCode IN ('".OTRG."',
													'".OTRD."',
													'".OTLH."',
													'".OTSH."',
													'".OTLHRD."',
													'".OTSPRD."',
													'".OTRDGT8."',
													'".OTLHGT8."',
													'".OTSPGT8."',
													'".OTLHRDGT8."',
													'".OTSPRDGT8."') "; 
						
			if(trim($this->get['cmbBank']) != "0"){
				$qryGetPaySum2 .= " AND ps.empBnkCd = '{$this->get['cmbBank']}' ";
			}
								
			if(trim($this->get['empNo']) != ""){
				$qryGetPaySum2 .= " AND ps.empNo = '{$this->get['empNo']}' ";
			}
			
			if(trim($_GET['txtEmpName']) != ""){
				if($this->get['nameType'] == 1){
					$qryGetPaySum2 .= " AND emp.empLastName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 2){
					$qryGetPaySum2 .= " AND emp.empFirstName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 3){
					$qryGetPaySum2 .= " AND emp.empMidName LIKE '{$this->get['txtEmpName']}%' ";
				}
			}
			
			if($this->get['cmbDiv'] != 0){
				$qryGetPaySum2 .= " AND ps.empDivCode = '{$this->get['cmbDiv']}' ";
			}
			
			if($this->get['cmbDept'] != 0){
				$qryGetPaySum2 .= " AND ps.empDepCode = '{$this->get['cmbDept']}' ";
			}
			if($this->get['cmbSect'] != 0){
				$qryGetPaySum2 .= " AND ps.empSecCode = '{$this->get['cmbSect']}' ";
			}
			if($empNo != ''){
				$qryGetPaySum2 .= "AND emp.empNo = '{$empNo}' ";
			}
			if($trnCode != ''){
				$qryGetPaySum2 .= "AND ern.TrnCode = '{$trnCode}' ";
			}
			
			$resGetPaySum2 = $this->execQry($qryGetPaySum2);
			if($type == 'get'){
				return $this->getSqlAssoc($resGetPaySum2);
			}
			if($type == 'check'){
				
				return $this->getRecCount($resGetPaySum2);
			}
	}
	
	function  getDivList($tblPaySum,$tblEarn,$pdYr,$pdNum){
		
			$qryPayRegDiv 	= "SELECT DISTINCT(empDivCode) as divCode
						FROM ".$tblPaySum." as ps 
						LEFT JOIN tblEmpMast as emp ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
						LEFT JOIN tblPayBank as bank ON bankCd=ps.empBnkCd and bank.compCode='{$this->session['company_code']}'
						LEFT JOIN tblDepartment as dept ON divCode=ps.empDepCode and dept.compCode='{$this->session['company_code']}' 
						and deptCode='0' and sectCode='0'
						WHERE ps.compCode = '{$this->session['company_code']}'
						AND ps.payGrp = '{$this->session['pay_group']}'
						AND ps.payCat = '{$this->session['pay_category']}'
						AND ps.pdYear = '{$pdYr}'
						AND ps.pdNumber = '{$pdNum}' "; 
						
			if(trim($this->get['cmbBank']) != "0"){
				$qryPayRegDiv .= "AND ps.empBnkCd = '{$this->get['cmbBank']}' ";
			}
								
			if(trim($this->get['empNo']) != ""){
				$qryPayRegDiv .= "AND ps.empNo = '{$this->get['empNo']}' ";
			}
			
			if(trim($this->get['txtEmpName']) != ""){
				if($this->get['nameType'] == 1){
					$qryPayRegDiv .= "AND emp.empLastName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 2){
					$qryPayRegDiv .= "AND emp.empFirstName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 3){
					$qryPayRegDiv .= "AND emp.empMidName LIKE '{$this->get['txtEmpName']}%' ";
				}
			}
			
			if($this->get['cmbDiv'] != 0){
				$qryPayRegDiv .= "AND ps.empDivCode = '{$this->get['cmbDiv']}' ";
			}
			
			if($this->get['cmbDept'] != 0){
				$qryPayRegDiv .= "AND ps.empDepCode = '{$this->get['cmbDept']}' ";
			}
			if($this->get['cmbSect'] != 0){
				$qryPayRegDiv .= "AND ps.empSecCode = '{$this->get['cmbSect']}' ";
			}
			$resPayRegDiv = $this->execQry($qryPayRegDiv);
			while($rowPayRegDiv = $this->getSqlAssoc($resPayRegDiv)){
			
				if(!empty($rowPayRegDiv['divCode'])){
					$divList = $divList.",".$rowPayRegDiv['divCode'];
				}
			}
			return $divList;
	}
	
	function getAllowanceBrkDwnDetails($type,$tblPaySum,$tblAllw,$pdYr,$pdNum,$divList,$empNo,$trnCode){
		
		$qryGetAllwBrkDwnDtl = "SELECT  ps.empNo, emp.empLastName, emp.empMidName, emp.empFirstName, 
						        emp.empBankCd, bank.bankDesc, emp.compCode, ps.empDivCode, 
                                emp.empDepCode, dept.deptDesc, emp.empSecCode, allw.allowCode, 
                                allw.allowAmt, allw.allowSked, allw.allowPayTag, allw.allowStart,
                                allw.allowEnd, trn.trnCode
								FROM  
								tblPayrollSummary ps LEFT OUTER JOIN tblEmpMast emp 
								ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo 
								LEFT OUTER JOIN tblPayBank bank 
								ON bank.bankCd = ps.empBnkCd AND bank.compCode = emp.compCode 
								LEFT OUTER JOIN tblDepartment dept 
								ON dept.divCode = ps.empDepCode AND dept.compCode = emp.compCode AND dept.deptCode = '0' AND dept.sectCode = '0' 
								RIGHT OUTER JOIN $tblAllw allw 
								ON allw.compCode = emp.compCode AND allw.empNo = emp.empNo 
								LEFT OUTER JOIN tblAllowType trn 
								ON trn.compCode = allw.compCode AND trn.allowCode = allw.allowCode
								WHERE (ps.compCode = '{$this->session['company_code']}') 
								AND (ps.payGrp = '{$this->session['pay_group']}') 
								AND (ps.payCat = '{$this->session['pay_category']}') 
								AND (ps.pdYear = '{$pdYr}') 
								AND (ps.pdNumber = '{$pdNum}') 
								AND (ps.empDivCode IN  (".substr($divList,1).")) 
								AND trn.trnCode IN ('".ALLW_THIRTEEN_MONTH_ALLOWANCE."',
								                    '".ALLW_ADVANCES."',
								                    '".ALLW_ALLOWANCE."',
								                    '".ALLW_BONUS."',
								                    '".ALLW_CASH_BOND."',
								                    '".ALLW_CASHIER_ALLOWANCE."',
								                    '".ALLW_ECOLA."',
								                    '".ALLW_GASOLINE_ALLOWANCE."',
								                    '".ALLW_REVIV_ALLOWANCE."',
								                    '".ALLW_RELOCATION_ALLOWANCE."',
								                    '".ALLW_TRAINING_ALLOWANCE."',
								                    '".ALLW_TRANSPORTATION_ALLOWANCE."') ";
		
			if(trim($this->get['cmbBank']) != "0"){
				$qryGetAllwBrkDwnDtl .= " AND ps.empBnkCd = '{$this->get['cmbBank']}' ";
			}
								
			if(trim($this->get['empNo']) != ""){
				$qryGetAllwBrkDwnDtl .= " AND ps.empNo = '{$this->get['empNo']}' ";
			}
			
			if(trim($_GET['txtEmpName']) != ""){
				if($this->get['nameType'] == 1){
					$qryGetAllwBrkDwnDtl .= " AND emp.empLastName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 2){
					$qryGetAllwBrkDwnDtl .= " AND emp.empFirstName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 3){
					$qryGetAllwBrkDwnDtl .= " AND emp.empMidName LIKE '{$this->get['txtEmpName']}%' ";
				}
			}
			
			if($this->get['cmbDiv'] != 0){
				$qryGetAllwBrkDwnDtl .= " AND ps.empDivCode = '{$this->get['cmbDiv']}' ";
			}
			
			if($this->get['cmbDept'] != 0){
				$qryGetAllwBrkDwnDtl .= " AND ps.empDepCode = '{$this->get['cmbDept']}' ";
			}
			if($this->get['cmbSect'] != 0){
				$qryGetAllwBrkDwnDtl .= " AND ps.empSecCode = '{$this->get['cmbSect']}' ";
			}
			if($empNo != ''){
				$qryGetAllwBrkDwnDtl .= "AND emp.empNo = '{$empNo}' ";
			}
			if($trnCode != ''){
				$qryGetAllwBrkDwnDtl .= "AND trn.trnCode = '{$trnCode}' ";
			}		
			$qryGetAllwBrkDwnDtl .= "ORDER BY emp.empLastName ";
			
			if($type == 'list'){
				return $this->execQry($qryGetAllwBrkDwnDtl);
			}
			if($type == 'get'){
				$resGetAllwBrkDwnDtl = $this->execQry($qryGetAllwBrkDwnDtl);
				return $this->getSqlAssoc($resGetAllwBrkDwnDtl);
			}
			if($type == 'check'){
				$resGetAllwBrkDwnDtl = $this->execQry($qryGetAllwBrkDwnDtl);
				return $this->getRecCount($resGetAllwBrkDwnDtl);
			}	
	}
	
	function getNightDiffBrkDwnDetails($type,$tblPaySum,$tblEarn,$pdYr,$pdNum,$divList,$empNo,$trnCode){
			
			$qryGetPaySum2 	= "SELECT ps.empNo,emp.empLastName,emp.empMidName,emp.empFirstName,empBankCd,
									bankDesc,emp.compCode,empDivCode,emp.empDepCode,deptDesc,emp.empSecCode,
									ern.trnAmountE,ern.trnCode
									FROM ".$tblPaySum." as ps 
									LEFT JOIN tblEmpMast as emp ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
									LEFT JOIN tblPayBank as bank ON bankCd=ps.empBnkCd and bank.compCode='{$this->session['company_code']}'
									LEFT JOIN tblDepartment as dept ON divCode=ps.empDepCode and dept.compCode='{$this->session['company_code']}' 
									and deptCode='0' and sectCode='0'
									LEFT JOIN $tblEarn as ern 
									ON emp.compCode = ern.compCode AND ern.pdYear = '{$pdYr}' AND ern.pdNumber = '{$pdNum}' AND ps.empNo = ern.empNo
									WHERE ps.compCode = '{$this->session['company_code']}'
									AND ps.payGrp = '{$this->session['pay_group']}'
									AND ps.payCat = '{$this->session['pay_category']}'
									AND ps.pdYear = '{$pdYr}'
									AND ps.pdNumber = '{$pdNum}' 
									AND empDivCode IN  (".substr($divList,1).") 
									AND ern.trnAmountE <> 0
									AND ern.trnCode IN ('".NDRG."',
													'".NDRD."',
													'".NDLH."',
													'".NDSP."',
													'".NDLHRD."',
													'".NDSPRD."',
													'".NDRDGT8."',
													'".NDLHGT8."',
													'".NDSHGT8."',
													'".NDLHRDGT8."',
													'".NDSPRDGT8."') "; 
						
			if(trim($this->get['cmbBank']) != "0"){
				$qryGetPaySum2 .= " AND ps.empBnkCd = '{$this->get['cmbBank']}' ";
			}
								
			if(trim($this->get['empNo']) != ""){
				$qryGetPaySum2 .= " AND ps.empNo = '{$this->get['empNo']}' ";
			}
			
			if(trim($_GET['txtEmpName']) != ""){
				if($this->get['nameType'] == 1){
					$qryGetPaySum2 .= " AND emp.empLastName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 2){
					$qryGetPaySum2 .= " AND emp.empFirstName LIKE '{$this->get['txtEmpName']}%' ";
				}
				if($this->get['nameType'] == 3){
					$qryGetPaySum2 .= " AND emp.empMidName LIKE '{$this->get['txtEmpName']}%' ";
				}
			}
			
			if($this->get['cmbDiv'] != 0){
				$qryGetPaySum2 .= " AND ps.empDivCode = '{$this->get['cmbDiv']}' ";
			}
			
			if($this->get['cmbDept'] != 0){
				$qryGetPaySum2 .= " AND ps.empDepCode = '{$this->get['cmbDept']}' ";
			}
			if($this->get['cmbSect'] != 0){
				$qryGetPaySum2 .= " AND ps.empSecCode = '{$this->get['cmbSect']}' ";
			}
			if($empNo != ''){
				$qryGetPaySum2 .= "AND emp.empNo = '{$empNo}' ";
			}
			if($trnCode != ''){
				$qryGetPaySum2 .= "AND ern.TrnCode = '{$trnCode}' ";
			}
			$qryGetAllwBrkDwnDtl .= "ORDER BY emp.empLastName ";
			
			if($type == 'list'){
				return $this->execQry($qryGetPaySum2);
			}
			if($type == 'get'){
				$resGetPaySum2 = $this->execQry($qryGetPaySum2);
				return $this->getSqlAssoc($resGetPaySum2);
			}
			if($type == 'check'){
				$resGetPaySum2 = $this->execQry($qryGetPaySum2);
				return $this->getRecCount($resGetPaySum2);
			}
	}
	function countTotal($GET,$Act){
		$reportType = ($GET['reportType'] == 1?"Hist":"");		
		$empNo		= ($GET['empNo'] > ""?" AND (tblEmpMast.empNo LIKE '{$empNo}%')":"");
		$empName	= ($GET['txtEmpName'] > ""?" AND ($nameType LIKE '{$empName}%')":"");
		$empDiv 	= ($GET['cmbDiv'] > "" && $GET['cmbDiv'] > 0?" AND (empDiv = '{$cmbDiv}')":"");
		$empBank	= ($GET['cmbBank'] != "" && $GET['cmbBank'] > 0?" AND (empBankCd='$empBank')":"");
		$arrPayPd = $this->getPayDay($_SESSION['company_code'],$GET['payPd']);		
		if ($Act == "loans") {
			$subQuery = " and empNo IN 
						(Select empNo from tblEmpLoansDtl$reportType 
							where dedtag='Y' 
							and compCode='" . $_SESSION['company_code'] . "' 
							and pdYear='" . $arrPayPd['pdYear'] . "' 
							and pdNumber='" . $arrPayPd['pdNumber']. "')";
		}
		elseif ($Act == "otherdeductions") {
			$subQuery = "and empNo IN (Select empNo from tblDedTranDtl$reportType
				  				where dedStat='A' 
								AND processtag = 'Y'
								AND payGrp='" . $this->session['pay_group'] . "' 
								AND payCat='" . $this->session['pay_category'] . "' 								
								AND compCode='" . $_SESSION['company_code'] . "' 
				  				)";
		}
		elseif ($Act == "otherearnings") {
			$subQuery = "and empNo IN (Select empNo from tblEarnTranDtl$reportType
				  				where earnStat='A' 
								AND compCode = '" . $_SESSION['company_code'] . "'
								AND payCat = '" . $_SESSION['pay_category'] . "' 
								AND payGrp = '" . $_SESSION['pay_group'] . "')";
		}
		$EmpQry = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '" . $_SESSION['company_code'] . "'
			     AND empStat NOT IN('RS','IN','TR') 
				  $empNo $empName $empDiv $empBank 
				  $subQuery
				  order by empLastName,empFirstName,empMidName
				 ";	
		$res = $this->execQry($EmpQry);
		return $this->getRecCount($res);		 
	
	}
	function getPayDay($compCode,$payPd) {
		$qry = "SELECT * FROM tblPayPeriod WHERE     compCode = '$compCode' AND pdPayable = '$payPd' and payGrp='" . $_SESSION['pay_group'] . "' and payCat='" . $_SESSION['pay_category'] . "'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}	
	
	function getTaxExemption($empTeu)
	{
		$qryGetTaxExempt = "Select teuAmt from tblTeu where teuCode='".$empTeu."'";
		$resGetTaxExempt = $this->execQry($qryGetTaxExempt);
		$rowGetTaxExempt = $this->getSqlAssoc($resGetTaxExempt);
		return $rowGetTaxExempt['teuAmt'];
	}
}

?>