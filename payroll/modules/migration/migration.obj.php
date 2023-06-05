<?
class mirationObj extends commonObj {

	/*var $get;
	
	var $session;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}*/
	
	function getAllowlist($db){

		$qryGetAllowList = "SELECT HRMASTER.[EMPLOYEE ID#] as empNo, H_ALLOW.[ALLOW TYPE] as allowCode, H_ALLOW.AMOUNT as allowAmnt, P_ATYPE_PERSONA.FREQUENCY as allwFreq
							FROM (HRMASTER INNER JOIN H_ALLOW ON HRMASTER.[EMPLOYEE ID#] = H_ALLOW.[EMPLOYEE ID#]) 
							INNER JOIN P_ATYPE_PERSONA 
							ON H_ALLOW.[ALLOW TYPE] = P_ATYPE_PERSONA.[ALLOWANCE TYPE]
							ORDER BY HRMASTER.[EMPLOYEE ID#], H_ALLOW.[ALLOW TYPE], P_ATYPE_PERSONA.FREQUENCY";
		
		return $db->Execute($qryGetAllowList);		
	}
	
	function checkEmpNoToEmpMast($empNo){

		$qryCheckEmpNo = "SELECT compCode,empNo FROM tblEmpMast 
						  WHERE empNo = '{$empNo}' and companyCode='".$_SESSION["company_code"]."'
						  AND empStat NOT IN('RS','IN','TR')";
		
		$resCheckEmpNo = $this->execQry($qryCheckEmpNo);	
		$rowCheckEmpNo = $this->getSqlAssoc($resCheckEmpNo);
		if($this->getRecCount($resCheckEmpNo) > 0){
			return $rowCheckEmpNo;
		}
		else{
			return "0";
		}
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
	
	function checkEmpAllow($compCode,$empNo,$allowCode){
		$qryChck = "SELECT empNo FROM tblAllowance
					WHERE compCode = '{$compCode}'
					AND empNo = '{$empNo}'
					AND allowCode = '{$allowCode}'";
		$resChck = $this->execQry($qryChck);
		return $this->getRecCount($resChck);
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
	
	function deltblAllow_Paradox($empNo)
	{
		$qryDel = "Delete from tblAllowance_Paradox where empNo = '".$empNo."'";
		$resDel = $this->execQry($qryDel);	
		
	}
	
	function checkEmpNoToEmpMast_Updated($empNo, $brnCode){

		$qryCheckEmpNo = "SELECT compCode,empNo FROM tblEmpMast 
						  WHERE empNo = '{$empNo}' 
						  and compCode='".$_SESSION["company_code"]."'
						  and empBrnCode='".$brnCode."'
						  AND empStat NOT IN('RS','IN','TR')";
		$resCheckEmpNo = $this->execQry($qryCheckEmpNo);	
		$rowCheckEmpNo = $this->getSqlAssoc($resCheckEmpNo);
		if($this->getRecCount($resCheckEmpNo) > 0){
			return $rowCheckEmpNo;
		}
		else{
			return "0";
		}
	}
}
?>