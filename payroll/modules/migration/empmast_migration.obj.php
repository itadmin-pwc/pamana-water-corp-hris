<?php
class migEmpMastObj extends commonObj {

	function checkDuplicateEmpNo($empNo,$compCode)
	{
		$qryCheckEmp = "Select count(*) as empCnt from tblEmpMast where empNo='".$empNo."' and compCode='".$compCode."'";
		$rsCheckEmp = $this->execQry($qryCheckEmp);
		$rowCheckEmp = $this->getSqlAssoc($rsCheckEmp);
		$empExists = ($rowCheckEmp["empCnt"]>=1?1:0);
		return $empExists;
	}
	
	function getempStatDef($empStat)
	{
		
		switch($empStat)
		{
			case "REGULAR":
				$empStat = "RG";
				break;
			
			case "PROBATIONARY":
				$empStat = "PR";
				break;
			
			case "CONTRACTUAL":
				$empStat = "CN";
				break;
			default:
				$empStat = "UNKNOWN";
				break;
		}
		
		return $empStat; 
	}
	
	function checkSssNo($empSssNo)
	{
		///trim first the SSS No
		$empSssNo = str_replace("-","",$empSssNo);
		
		//check Length of SssNo
		$empSssNoLength = strlen($empSssNo);
		
		$empSssNo = ($empSssNoLength!=10?0:$empSssNo);
		return $empSssNo;
	}
	
	function getBankDef($bankName,$compCode)
	{
		switch($bankName)
		{
			case "ASIA UNITED BANK":
				$bankCd = "2";
				break;
			
			case "BDO-CLARK":
				$bankCd = "4";
				break;	
			
			case "CASH":
				$bankCd = "3";
				break;	
			
			case "EBC-CLARK":
				$bankCd = "5";
				break;	
			
			case "EBC-DIVISORIA":
				$bankCd = "5";
				break;	
					
			case "MBTC-DUTY FREE":
				$bankCd = "1";
				break;
				
			case "MBTC-INT'L TRADING CORP.":
				$bankCd = "1";
				break;
					
			case "MBTC-PRICE CLUB":
				$bankCd = "1";
				break;
			
			case "BANKOFCOMMERCE":
				$bankCd = "6";
				break;
			
			case "BDO-CLARK":
				$bankCd = "4";
			
			default:
				$bankCd = "0";
				break;
		}
		
		return $bankCd; 
	}
	
	function getRateDef($rateType)
	{
		switch($rateType)
		{
			case "PER MONTH":
				$rateType = "M";
				break;
			
			case "PER DAY":
				$rateType = "D";
				break;
			
			default:
				$rateType = 0;
				break;
		}
		
		return $rateType;
	}
	
	function getRank($empRankType,$compCode)
	{
		$qryRank = "Select rankCode from tblRankType where compCode='".$compCode."' and rankDesc like '%".$empRankType."%'";
		$rsRank = $this->execQry($qryRank);
		$rowRank = $this->getSqlAssoc($rsRank);
		
		$rankCode = ($rowRank["rankCode"]!=""?$rowRank["rankCode"]:0);
		return $rankCode;
	}
	
	function getMarStatDef($empTeu)
	{
		$single = array('HF','HF1','HF2','HF3','HF4','S','Z');
			
		if(in_array($empTeu,$single))
		{
			$empStat = "SG";
		}
		else
		{
			$empStat = "ME";
		}
		
		return $empStat;
			
	}
	
	function getComputedDRate($empMRate,$compCode)
	{
		$qryComp = "Select compNoDays from tblCompany where compCode='".$compCode."'";
		$rsComp = $this->execQry($qryComp);
		$rowComp = $this->getSqlAssoc($rsComp);
		
		$empDrate = $empMRate/$rowComp["compNoDays"];
		$empDrate = sprintf("%01.2f",$empDrate);
		return 	$empDrate;
	}
	
	function getGroup($empBrnCode,$compCode)
	{
		$qryBrnch = "Select brnDefGrp from tblBranch where compCode='$compCode' and brnCode='$empBrnCode' and brnStat='A'";
		$rsBrnch = $this->execQry($qryBrnch);
		$rowBrnch = $this->getSqlAssoc($rsBrnch);
		
		$empPayGroup = ($rowBrnch["brnDefGrp"]!=""?$rowBrnch["brnDefGrp"]:0);
		return $empPayGroup;
		
	}
	
	function chkBioNum($bioNum)
	{
		$qryBio = "Select * from tblBioEmp where bioNumber='".$bioNum."'";
		$rsBio = $this->execQry($qryBio);
		$cntBio = $this->getRecCount($rsBio);
		$cntBio = ($cntBio!=""?1:0);
		return $cntBio;
	}
	
	
	
	/*Unposted Data Transaction Object*/
	function unPostedTranOthEarn($pdNumber,$pdYear)
	{

		$qryGetOthEarn =  "SELECT dtl.compCode,empNo, dtl.trnCode, trnAmount AS sumEarnAmnt, pdNumber, pdYear, trnCntrlNo
							FROM tblEarnTranDtl dtl, tblEarnTranHeader hdr
							WHERE (dtl.compCode = '".$_SESSION["company_code"]."') 
							  AND dtl.processtag is null
							  AND (dtl.payGrp = '".$_SESSION["pay_group"]."') 
							  AND (dtl.payCat = '".$_SESSION["pay_category"]."')
							  AND (dtl.earnStat = 'A')
							  AND (dtl.trnCode IN (Select trnCode from tblPayTransType where trnApply IN (".$this->getCutOffPeriod($pdNumber).",3)))
							  AND dtl.refNo=hdr.refNo 
							  AND hdr.pdNumber='".$pdNumber."' 
							  AND hdr.pdYear='".$pdYear."' 
							  AND hdr.earnStat='A'";
							 
		
		$resGetOthEarn = $this->execQry($qryGetOthEarn);
		return $this->getArrRes($resGetOthEarn);
	}
	
	function unPostedTranOthDed($pdNumber,$pdYear)
	{

		$qryGetOthEarn =  "SELECT dtl.compCode,empNo, dtl.trnCode, trnAmount AS sumDedAmnt, pdNumber, pdYear, trnCntrlNo
							FROM tblDedTranDtl dtl, tblDedTranHeader hdr
							WHERE (dtl.compCode = '".$_SESSION["company_code"]."') 
							  AND dtl.processtag is null
							  AND (dtl.payGrp = '".$_SESSION["pay_group"]."') 
							  AND (dtl.payCat = '".$_SESSION["pay_category"]."')
							  AND (dtl.dedStat = 'A')
							  AND (dtl.trnCode IN (Select trnCode from tblPayTransType where trnApply IN (".$this->getCutOffPeriod($pdNumber).",3)))
							  AND dtl.refNo=hdr.refNo 
							  AND hdr.pdNumber='".$pdNumber."' 
							  AND hdr.pdYear='".$pdYear."' 
							  AND hdr.dedStat='A'
							  ";
		$resGetOthEarn = $this->execQry($qryGetOthEarn);
		return $this->getArrRes($resGetOthEarn);
	}
	
	function getCutOffPeriod($pdNumber)
	{
		if((int)trim((int)trim($pdNumber))%2){
			return  1;
		}
		else{
			return 2;
		}	
	}
	
	function writeToTblUnpostedTran($empCompCd,$empId,$trnCd,$trnAt,$pdNum,$pdYr,$trnCntNo,$datePosted)
	{
		$writeToTblUnpostedTran = "INSERT INTO tblUnpostedTran
							  (compCode,empNo,trnCode,trnAmt,pdNumber,pdYear,trnCntrlNo,dateAdded)
							  VALUES('".$empCompCd."','".$empId."','".$trnCd."','".sprintf("%01.2f",$trnAt)."','".$pdNum."','".$pdYr."','".$trnCntNo."','".date("Y-m-d", strtotime($datePosted))."')";
		return $this->execQry($writeToTblUnpostedTran);
	}
	
	function getUnpostedTran($pdNumber,$pdYear,$compCode,$pyGrp,$pyCat,$datePosted)
	{
		
		/*Get Unposted Other Earnings Transaction*/
		foreach ((array)$this->unPostedTranOthEarn($pdNumber,$pdYear) as $arrUnPosTranEarnVal)
		{//foreach for Unposted Other Earnings
			$Trns = $this->writeToTblUnpostedTran($arrUnPosTranEarnVal["compCode"],$arrUnPosTranEarnVal["empNo"],$arrUnPosTranEarnVal["trnCode"],$arrUnPosTranEarnVal["sumEarnAmnt"],$arrUnPosTranEarnVal["pdNumber"],$arrUnPosTranEarnVal["pdYear"],$arrUnPosTranEarnVal["trnCntrlNo"],$datePosted);
		}//end foreach for Unposted Other Earnings
		
		/*Get Unposted Other Deductions Transaction*/
		foreach ((array)$this->unPostedTranOthDed($pdNumber,$pdYear,$compCode,$pyGrp,$pyCat) as $arrUnPosTranDedVal)
		{//foreach for Unposted Other Earnings
			
				$Trns = $this->writeToTblUnpostedTran($arrUnPosTranDedVal["compCode"],$arrUnPosTranDedVal["empNo"],$arrUnPosTranDedVal["trnCode"],$arrUnPosTranDedVal["sumDedAmnt"],$pdNumber,$pdYear,$arrUnPosTranDedVal["trnCntrlNo"],$datePosted);
			
		}//end foreach for Unposted Other Earnings
		
		if($Trns){
			return 1;
		}
	}
	
	function getEmpDivCode($compCode, $divDesc)
	{
		$qryDiv = "Select * from tblTransTable where divDesc = '".$divDesc."' and compCode='".$compCode."'";
		$rsDiv = $this->execQry($qryDiv);
		$arrDiv = $this->getSqlAssoc($rsDiv);
		
		$empDivCode = ($arrDiv["divCode"]!=""?$arrDiv["divCode"]:"0");
		
		return $empDivCode;
		
	}
	
	function getempRestDay($compCode,$empNo)
	{
		$qryRestDay = "Select * from EmpRestDay$ where empNo='".$empNo."' and compCode='".$compCode."'";
		$rsRestDay = $this->execQry($qryRestDay);
		$arrRestDay = $this->getSqlAssoc($rsRestDay);
		
		$empRestDay = ($arrRestDay["empRestDay"]!=""?"'".$arrRestDay["empRestDay"]."'":"NULL");
		//echo $qryRestDay."<br>";
		return $empRestDay;
	}
	
	function getEmpYtdData($empNo)
	{
		$qryEmpYtdData = "Select * from tblYtdDataHist_CLARK where empNo='".$empNo."'";
		return $this->execQry($qryEmpYtdData);
	}
	
	
	function getPaySumSprtAllow($empNo)
	{
		$qrySprtAllow = "Select sum(sprtAllow) as sprtAllow, sum(sprtAllowAdvance) as sprtAllowAdvance
					from tblPayrollSummaryHist
					where pdYear='2012' and empNo='".$empNo."'";
		return $rsSprtAllow = $this->execQry($qrySprtAllow);
		
	}
	
	function getMinWage($empBrnCode,$compCode)
	{
		$qryBrnch = "Select * from tblBranch where  brnCode='$empBrnCode' and brnStat='A'";
		$rsBrnch = $this->execQry($qryBrnch);
		return $this->getSqlAssoc($rsBrnch);
	}
	
	function getEmpDeptCode($compCode,$deptDesc)
	{
		$qryDept = "Select * from tblDeptTrantbl where deptDesc = '".$deptDesc."' and compCode='".$compCode."'";
		$rsDept = $this->execQry($qryDept);
		$arrDept = $this->getSqlAssoc($rsDept);
		
		$empDeptCode = ($arrDept["deptCode"]!=""?$arrDept["deptCode"]:"0");
		
		return $empDeptCode;
		
	}
	
	function delTblEmpMastParadox($selBrnCode, $selLocCode)
	{
		$qryDelEmpMastParadox = "Delete from tblEmpMast_Paradox where compCode='".$_SESSION["company_code"]."' and empBrnCode='".$selBrnCode."' and empLocCode='".$selLocCode."' 
								and empPayGrp='".$_SESSION["pay_group"]."' and empPayCat='".$_SESSION["pay_category"]."'";
		$rsDelEmpMastParadox = $this->execQry($qryDelEmpMastParadox);
		
		if($rsDelEmpMastParadox){
			return 1;
		}
		else{
			return 0;
		}
		
	}
	
	function getEmpPaySum($empNo)
	{
		$qryEmpPaySum = "Select * from tblPayrollSummaryHist_CLARK where empNo='".$empNo."' and pdYear='2012'";
		return $this->execQry($qryEmpPaySum);
	}
	
	function getEmpMtdGovt($empNo)
	{
		$qryEmpMtdGovt = "Select * from tblMtdGovtHist_CLARK where empNo='".$empNo."' and pdMonth<>3";
		
		return $this->execQry($qryEmpMtdGovt);
	}
	
	function getEmpPayCategory($emprank)
	{
		switch($emprank)
		{
			case "4":
				$empPayCat = "2";
				break;
			
			case "3":
				$empPayCat = "3";
				break;
			
			case "2":
				$empPayCat = "3";
				break;
			
			case "1":
				$empPayCat = "3";
				break;
			
			default:
				$empPayCat = "0";
				break;
		}
		
		return $empPayCat;
	}
	
	function getEmpAllowance($empNo)
	{
		$qryEmpAllow = "Select * from EmpAllowance$ where empNo='".$empNo."'";
		return $this->execQry($qryEmpAllow);
	}
	
	function getEquivAllwCode($oldAllowCode){
		
		$qry = "SELECT allowCodeNew FROM tblAllowTypeConvTbl 
			 	WHERE allowCodeOld = '".str_replace("'","''",stripslashes($oldAllowCode))."'";
		$res = $this->execQry($qry);
		
		if($this->getRecCount($res) > 0){
			return $this->getSqlAssoc($res);;
		}
		else{
			return "0";
		}
		
	}
	
	function getPatypePersona($oldAllowCode)
	{
		$qryEmpAllowType = "Select * from PATYPEPERSONA$ where allowType='".$oldAllowCode."'";
		return $this->execQry($qryEmpAllowType);
	}
	
	function getAllowSprtPs($allowCode)
	{
		$qrygetAllowSprtPs = "Select * from tblAllowType
							where compCode='".$_SESSION["company_code"]."' and allowCode='".$allowCode."'
							and allowTypeStat='A'";
		$resgetAllowSprtPs = $this->execQry($qrygetAllowSprtPs);
		
		if($this->getRecCount($resgetAllowSprtPs) > 0){
			return $this->getSqlAssoc($resgetAllowSprtPs);;
		}
		else{
			return "";
		}
							
	}
	
	function getEmpEarn_Basic($empNo, $tableHist)
	{
		$qryEmpBasic = "Select * from ".$tableHist." where empNo='".$empNo."'";
		
		return $this->execQry($qryEmpBasic);
	}
	
	function getPdYearList() {
		$sqlYear= "Select distinct pdYear from tblpayperiod order by pdYear";
		return $this->getArrRes($this->execQry($sqlYear));
	}
}
?>