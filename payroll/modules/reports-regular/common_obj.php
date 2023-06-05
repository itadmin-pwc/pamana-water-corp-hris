<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Common Trans, js, obj, ajax instead of using timesheet 
*/

class inqTSObj extends commonObj {
	
	/*Common*/
	function getAllPeriod($compCode,$groupType,$catType) 
	{
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE compCode = '$compCode' AND 
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
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
	
	function getSlctdPd($compCode,$payPd) 
	{
		$qry = "SELECT * FROM tblPayPeriod 
				WHERE pdSeries = '$payPd' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	
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
}

?>