<?
class minWageObj extends commonObj {
	var $get;
	var $session;


	function __construct($method,$sessionVars){
		$this->get = $method;
	}
	
	
	function getListMinWage($where)
	{
		$qryList = "SELECT     empMast.empNo, empMast.empLastName, empMast.empFirstName, empMast.empMidName, empMast.empMrate, empMast.empDrate, 
                      		   empMast.empHrate, empMast.empWageTag, brnch.brnDesc, brnch.minWage, empStat
					FROM       tblEmpMast empMast INNER JOIN
                      			tblBranch brnch ON empMast.empBrnCode = brnch.brnCode AND empMast.empDrate <= brnch.minWage
					WHERE     (empMast.compCode = '".$_SESSION["company_code"]."') AND (brnch.compCode = '".$_SESSION["company_code"]."') 
							  AND (empMast.empWageTag = 'N') ".$where."
					ORDER BY brnch.brnDesc, empMast.empLastName;";
		return $this->getArrRes($this->execQry($qryList));
	}
	
	function UpdateInsMinWage()
	{
		$Trns = $this->beginTran();
		
		for($i=0;$i<=(int)$this->get['chCtr'];$i++) 
		{
			if ($this->get["chkMinWage$i"] !="") 
			{
				$expChkMinWage = explode("*", $this->get["chkMinWage$i"]);
				$arrGetEmpInfo = $this->getEmployeeList($_SESSION["company_code"]," and empNo='".$expChkMinWage["0"]."'");
				$qryInsHist.= "Insert into tblEmpMinWageHist(compCode, empNo, empDrate, empBrnCode, brnMinWage,  userUpdated, dateUpdated)
								values('".$arrGetEmpInfo["compCode"]."','".$arrGetEmpInfo["empNo"]."', '".$arrGetEmpInfo["empDrate"]."', '".$arrGetEmpInfo["empBrnCode"]."', '".$expChkMinWage[1]."', '".$_SESSION['employee_number']."', '".date("m/d/Y")."');\n";
		
				$qryUpdateEmpMast.= "Update tblEmpMast set empWageTag='Y' where compCode='".$arrGetEmpInfo["compCode"]."' and empNo='".$expChkMinWage["0"]."';";
			}
		}
		
		
		if ($Trns) 
			$Trns = $this->execQry($qryInsHist);
			
		if ($Trns) 
			$Trns = $this->execQry($qryUpdateEmpMast);
		
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}
		
		
	}
}

?>