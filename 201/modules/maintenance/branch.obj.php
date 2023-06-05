<?
class branchObj extends commonObj {
	
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
	
	function getGLStoreList(){
		$qry = "SELECT * FROM tblGLStoreAcct 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function checkBrnch(){
		$qry = "SELECT brnCode FROM tblBranch 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND brnCode = '{$this->get['brnCode']}'";
		$res = $this->execQry($qry);
		return $this->getRecCount($res);
	}
	
	function getBranch(){
		$qry = "SELECT * FROM tblBranch 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND brnCode = '{$this->get['brnCode']}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function toTblBranch(){
		$qry = "INSERT INTO tblBranch (compCode,brnCode,brnDesc,
									  brnShortDesc,brnAddr1,brnAddr2,
									  brnAddr3,minWage,brnSignatory,
									  brnSignTitle,brnDefGrp,brnStat,
									  brnLoc,coCtr,ecola,GrpCode, 
									  coeSignatory,coeSignatoryTitle)
								VALUES('{$_SESSION['company_code']}',
								'{$this->get['brnCode']}',
								'".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
								'".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."',
								'".str_replace("'","''",stripslashes(strtoupper($this->get['add1'])))."',
								'".str_replace("'","''",stripslashes(strtoupper($this->get['add2'])))."',
								'".str_replace("'","''",stripslashes(strtoupper($this->get['add3'])))."',
								'{$this->get['minWage']}',
								'".str_replace("'","''",stripslashes(strtoupper($this->get['signatory'])))."',
								'".str_replace("'","''",stripslashes($this->get['sgnTitle']))."',
								'{$this->get['brnGrp']}',
								'{$this->get['brnStat']}',
								'{$this->get['brnLoc']}',
								'{$this->get['coCtr']}',
								'{$this->get['ecolaAmnt']}',
								'{$this->get['brnCode']}',
								'".str_replace("'","''",stripslashes(strtoupper($this->get['coesignatory'])))."',
								'".str_replace("'","''",stripslashes($this->get['coesignatorytitle']))."'
								)";
		return $this->execQry($qry);
	}
	
	function updateBranch(){
		$qry = "UPDATE tblBranch SET brnDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."', 
									 brnShortDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."',
									 brnAddr1 = '".str_replace("'","''",stripslashes(strtoupper($this->get['add1'])))."',
									 brnAddr2 = '".str_replace("'","''",stripslashes(strtoupper($this->get['add2'])))."',
									 brnAddr3 = '".str_replace("'","''",stripslashes(strtoupper($this->get['add3'])))."',
									 minWage = '{$this->get['minWage']}',
									 brnSignatory = '".str_replace("'","''",stripslashes(strtoupper($this->get['signatory'])))."',
									 brnSignTitle = '".str_replace("'","''",stripslashes($this->get['sgnTitle']))."',
									 brnDefGrp = '{$this->get['brnGrp']}',
									 brnStat = '{$this->get['brnStat']}',
									 brnLoc = '{$this->get['brnLoc']}',
									 coCtr = '{$this->get['coCtr']}',
									 ecola = '{$this->get['ecolaAmnt']}',
									 GrpCode = '{$this->get['cmbGrp']}',
									 coeSignatory = '".str_replace("'","''",stripslashes(strtoupper($this->get['coesignatory'])))."',
									 coeSignatoryTitle = '".str_replace("'","''",stripslashes($this->get['coesignatorytitle']))."'
									 WHERE compCode = '{$_SESSION['company_code']}'
									 AND brnCode = '{$this->get['brnCode']}'";
//		$qryUpdateMinWage = "Update tblempmast set empWageTag='Y' 
//									where empNo IN (Select emp.empNo from tblempmast emp 
//									inner join tblbranch br on emp.empbrnCode = br.brnCode 
//									where emp.empDrate<=br.minWage and emp.empStat='RG' and emp.empbrnCode='{$this->get['brnCode']}')";
		$qryUpdateMinWage = "Update tblempmast set empWageTag='Y' 
									where empNo IN (Select empNo from (Select emp.empNo From tblempmast emp 
																		inner join tblbranch br on emp.empbrnCode = br.brnCode 
																		where emp.empDrate<=br.minWage and emp.empStat='RG' 
																			and emp.empbrnCode='{$this->get['brnCode']}') as c)";
		$this->execQry($qry);														
		return $this->execQry($qryUpdateMinWage);
	}
	
	function getGLInfo($tbl,$glCode){
		
		$qry = "SELECT * FROM $tbl
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctCde = '{$glCode}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}	
	
	function getBrnGrp(){
		$qryGrp = "Select * from tblBranchGrp where compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qryGrp);
		return $this->getArrRes($res);
	}
}
?>