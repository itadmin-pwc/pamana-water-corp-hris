<?php
class employeeListings extends commonObj {

	function getListShift()
	{
		$qrygetListShift = "Select * from tblTK_ShiftHdr where compCode='".$_SESSION["company_code"]."' and status='A' order by shiftCode";
		$resgetListShift = $this->execQry($qrygetListShift);

		return $this->getArrRes($resgetListShift);
	}
	
	function getListVio()
	{
		$qrygetListVio = "Select * from tblTK_ViolationType where compCode='".$_SESSION["company_code"]."' and violationStat='A' order by violationDesc";
		$resgetListVio = $this->execQry($qrygetListVio);

		return $this->getArrRes($resgetListVio);
	}
}
?>