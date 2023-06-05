<?
class trnsTypeObj extends commonObj {
	
	var $get;//method
	var $session;//session variables
	/**
	 * pass all the get variables and session variables 
	 *
	 * @param string $method
	 * @param array variable  $sessionVars
	 */
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}

	function getGLInfo($tbl,$glCode){
		
		$qry = "SELECT * FROM $tbl
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctCde = '{$glCode}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getGLMajorList(){
		$qry = "SELECT * FROM tblGLMajorAcct 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function checkTrnsType($act){
		if ($act=='E') {
			$not = " AND NOT trnCode= '{$this->get['hdtrnCode']}'";
		}
		$qry = "SELECT trnCode FROM tblPayTransType 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND trnCode = '{$this->get['trnCode']}' $not";
		$res = $this->execQry($qry);
		return $this->getRecCount($res);
	}
	
	function getTrnsType(){
		
		$qry = "SELECT * FROM tblPayTransType 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND trnCode = '{$this->get['trnCode']}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function toTblTransType(){
		if(trim($this->get['isEntry']) == 'N'){
			$trnEntry = '';
		}
		else{
			$trnEntry = 'Y';
		}
		$qry = "INSERT INTO tblPayTransType(compCode,trnCode,trnDesc,
											trnShortDesc,trnCat,trnApply,
											trnRecode,trnGlCode,trnPriority,
											trnStat,trnEntry,trnTaxCd)
									VALUES('{$_SESSION['company_code']}','{$this->get['trnCode']}','".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
										   '".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."','{$this->get['brnCat']}','{$this->get['trnApply']}',
										   '{$this->get['recCode']}','{$this->get['cmbGLMajor']}','{$this->get['prior']}',
										   '{$this->get['brnStat']}','{$trnEntry}','{$this->get['taxTag']}')";
		return $this->execQry($qry);
	}
	
	function updateTrnsType(){
		
		if(trim($this->get['isEntry']) == 'N'){
			$trnEntry = '';
		}
		else{
			$trnEntry = 'Y';
		}
		$qry = "UPDATE tblPayTransType SET trnDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
										   trnShortDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."',
										   trnCat = '{$this->get['brnCat']}',
										   trnApply = '{$this->get['trnApply']}',
										   trnRecode =  '{$this->get['recCode']}',
										   trnGlCode = '{$this->get['cmbGLMajor']}',
										   trnPriority = '{$this->get['prior']}',
										   trnStat =  '{$this->get['brnStat']}',
										   trnEntry =  '{$trnEntry}',
										   trnTaxCd = '{$this->get['taxTag']}'
										   WHERE compCode = '{$_SESSION['company_code']}'
										   AND trnCode = '{$this->get['trnCode']}'";
		return $this->execQry($qry);
	}
}
?>