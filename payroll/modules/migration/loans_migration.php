<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
$loansObj = new commonObj();

$sqlLoans = "SELECT *
				FROM tblEmpLoans INNER JOIN
                tblEmpMast ON tblEmpLoans.empNo = tblEmpMast.empNo
				WHERE (tblEmpLoans.compCode = '2') AND (tblEmpLoans.lonSked IN ('1',3)) AND 
				(tblEmpLoans.lonStart <= '1/8/2010') AND 
                      (tblEmpLoans.lonStat = 'O') AND (tblEmpLoans.lonCurbal > 0) AND  (tblEmpLoans.lonDedAmt1 > 0) AND
			   		  (tblEmpMast.compCode = '2')";
$arrLoans = $loansObj->getArrRes($loansObj->execQry($sqlLoans));
foreach($arrLoans as $val) {
	$sqlInsert .= "Insert into tblEmpLoansDtlHist (compCode, empNo, lonTypeCd, lonRefNo, pdYear, pdNumber, trnCat, trnGrp, trnAmountD, ActualAmt, dedTag, lonLastPay)
				values ('2','{$val['empNo']}','{$val['lonTypeCd']}','{$val['lonRefNo']}','2009','1','5','2','{$val['lonPayments']}','{$val['lonPayments']}','Y','1/15/2010') ";
}
$loansObj->execQry($sqlInsert);
?>
