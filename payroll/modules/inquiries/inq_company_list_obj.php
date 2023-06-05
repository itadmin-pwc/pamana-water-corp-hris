<?
class inqCompObj extends commonObj {

	function getBranchListArt($compCodeBranch) {
		$qry = "SELECT * FROM tblBranch
			   WHERE compCode = '$compCodeBranch' 
			   ORDER BY brnDesc ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getBranchTotalArt($compCodeBranch) {
		$qry = "SELECT COUNT(brnDesc) AS totRec, MAX(brnDesc + '/' + CONVERT(varchar, brnCode)) AS refMax
				FROM tblBranch
				WHERE (compCode = '$compCodeBranch') ";		
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
}
?>