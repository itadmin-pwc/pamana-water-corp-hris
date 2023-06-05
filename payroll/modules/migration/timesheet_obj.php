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
				payCat = '{$_SESSION['pay_category']}'";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	
	
	function ARDload($payPd,$loanCd="") {
		if (!$this->getPeriod($payPd)) {
			$hist = "hist";
		}
		if ($loanCd !="") {
			$loanfilter = " AND tblLoanType.lonTypeCd = '$loanCd'";
		}
		$arrPd = $this->getSlctdPd($_SESSION['company_code'],$payPd);
		 $sqlAR="SELECT tblLoanType.lonTypeShortDesc, tblEmpLoans.lonCurbal, tblEmpLoansDtl$hist.empNo, tblEmpLoansDtl$hist.lonRefNo, 
                      tblEmpLoansDtl$hist.ActualAmt, tblEmpLoans.lonAmt, tblEmpLoans.UploadTag, tblEmpLoansDtl$hist.trnGrp, 
                      tblEmpLoansDtl$hist.trnCat, tblEmpLoansDtl$hist.pdNumber, tblEmpLoansDtl$hist.pdYear, tblEmpLoansDtl$hist.dedTag,lonSeries,tblEmpLoans.compCode,tblEmpLoans.mmsNo 
				FROM tblEmpLoans INNER JOIN
                      tblLoanType ON tblEmpLoans.compCode = tblLoanType.compCode AND 
                      tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd INNER JOIN
                      tblEmpLoansDtl$hist ON tblEmpLoans.compCode = tblEmpLoansDtl$hist.compCode AND 
                      tblEmpLoans.empNo = tblEmpLoansDtl$hist.empNo AND tblEmpLoans.lonTypeCd = tblEmpLoansDtl$hist.lonTypeCd AND 
                      tblEmpLoans.lonRefNo = tblEmpLoansDtl$hist.lonRefNo
				WHERE (tblEmpLoansDtl$hist.dedTag IN ('Y', 'P'))
					AND pdNumber='{$arrPd['pdNumber']}'
					AND pdYear='{$arrPd['pdYear']}'
					AND UploadTag=1
					$loanfilter";
		$res = $this->execQry($sqlAR);
		return $this->getArrRes($res);
	}
	
	
	function CreateARTxtFile($arrAR,$type,$payPd) {
					$curdate = date('mdY');
					$filename = "textfiles/$type-$curdate.txt";
					if (file_exists($filename)) {
						unlink($filename);					
					}
					$arrPd = $this->getSlctdPd($_SESSION['company_code'],$payPd);
					$file = fopen($filename,"x+","");
					$recCount = 0;
					$totSal = 0;
					$series ="0";
					foreach ($arrAR as $valAR) {
						$recCount++;
						//compCode
						$str = $valAR['compCode'].",";
						//employee no.
						$str .= $valAR['empNo'].",";
						
						//loan type
						$str .= $valAR['lonTypeShortDesc'].",";
						
						//ref. no.
						$str .= $valAR['lonRefNo'].",";
						
						//loan amount
						$str .= $valAR['lonAmt'].",";
						
						//loan balance
						$str .= $valAR['lonCurbal'].",";
						
						//Deducted Amt
						$str .= $valAR['ActualAmt'].",";
						
						//mmsNo
						$str .= $valAR['mmsNo'];
						
						$str .=",".date('m/d/Y',strtotime($arrPd['pdPayable']));
						
						fwrite($file,$str."\r\n");
					}
					fclose($file);
					return true;
						
	}
	function UpdateAudit($field,$payPd) {
		$arrPd = $this->getSlctdPd($_SESSION['company_code'],$payPd);
		$curDate = date('m/d/Y');
		$sqlUpdate = "Update tblPayExtDataAudit set $field=1,dateUpdated='$curDate' where compCode='{$_SESSION['company_code']}' AND pdYear='{$arrPd['pdYear']}' AND pdNumber='{$arrPd['pdNumber']}'";
		return $this->execQry($sqlUpdate);
	}
	
	function ResignedEmp($date_fr,$date_to) {
		if ($date_fr !="" && $date_to !="")
			$dateFilter = " AND dateResigned between '$date_fr' AND '$date_to'";
		$sqlRes = "Select empNo,empLastName,empFirstName,empMidname,dateResigned,empStat from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empStat='RS' $dateFilter";
		$res = $this->execQry($sqlRes);
		$curdate = date('mdY');
					$filename = "textfiles/Resigned Employees-$curdate.txt";
					if (file_exists($filename)) {
						unlink($filename);					
					}
					$file = fopen($filename,"x+","");
					$recCount = 0;
					$totSal = 0;
					$series ="0";
					foreach ($this->getArrRes($res) as $valRes) {
						$recCount++;
						//employee no.
						$str = $valRes['empNo'].",";
						
						//employee name
						$str .= $valRes['empFirstName']." " .$valRes['empMidname'][0].". " .$valRes['empLastName'].",";
						
						//date resigned
						$dateResigned = date('m/d/Y',strtotime($valRes['dateResigned']));
						$str .= $dateResigned;
						
						fwrite($file,$str."\r\n");
					}
					fclose($file);
					return true;		
	}
}

?>