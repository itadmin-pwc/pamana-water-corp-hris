<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Common Trans, js, obj, ajax instead of useing timesheet 
*/

class inqTSObj extends commonObj {
	
	function getAllBranch(){
		$brnqry="SELECT empBrnCode FROM tblBlacklistedEmp GROUP BY empBrnCode ORDER BY empBrnCode";
		$brnres=$this->execQry($brnqry);
		return $this->getArrRes($brnres);		
	}
}
?>