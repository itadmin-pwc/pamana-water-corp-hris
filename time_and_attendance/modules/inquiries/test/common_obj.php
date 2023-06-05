<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	10/09/2010
	Function		:	Common Trans, js, obj, ajax instead of using timesheet 
*/

class inqTSObj extends commonObj {

	
	
	function selectdpayCat($payCat) {
		$sqlpayCat = "Select * from tblPayCat where compCode = '{$_SESSION['company_code']}' and payCat IN ($payCat)";
		return $this->getArrRes($this->execQry($sqlpayCat));
	}
	
	function getListShift()
	{
		$qrygetListShift = "Select * from tblTK_ShiftHdr where compCode='".$_SESSION["company_code"]."' and status='A' order by shiftCode";
		$resgetListShift = $this->execQry($qrygetListShift);

		return $this->getArrRes($resgetListShift);
	}
	
	
}

?>