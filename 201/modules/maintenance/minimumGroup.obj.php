<?
class deptObj extends commonObj {
	
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
	//Group Name
	function checkMinGroup(){
		$qry = "SELECT * FROM tblMinGroup 
				WHERE minGroupName = '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."'";
		$res = $this->execQry($qry);
		return $this->getRecCount($res);
	}
	
	function toMinGroup(){
		$qry = "INSERT INTO tblMinGroup (minGroupName,compCode,stat)
				VALUES('".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
     				   '{$_SESSION['company_code']}',
					   '{$this->get['cmbStat']}')";
		return $this->execQry($qry);
	}	
		
	function updtMinGroup(){
		$qry = "UPDATE tblMinGroup 
					SET minGroupName = '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
					stat='{$this->get['cmbStat']}'
					where minGroupID='{$this->get['minGroupID']}'";
		return $this->execQry($qry);
	}
	
	function getMinGroup($minGroupID){
		 $qry = "SELECT * FROM tblMinGroup 
				WHERE minGroupID = '{$this->get['minGroupID']}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);		
	}
	
	//Group Branch
	function toBranchGroup(){
		$qry = "INSERT INTO tblBranchMinimumGroup (minGroupID,brnCode)
				VALUES('{$this->get['cmbGroupName']}',
					   '{$this->get['cmbBranch']}')";
		return $this->execQry($qry);
	}	
	
	function updtBranchGroup(){
		$qry = "UPDATE tblBranchMinimumGroup 
					SET minGroupID = '{$this->get['cmbGroupName']}',
					brnCode='{$this->get['cmbBranch']}' 
					WHERE branchMinimumGroupID = '{$this->get['branchMinimumGroupID']}'";
		return $this->execQry($qry);
	}

	function getBranchGroup($branchMinimumGroupID){
		 $qry = "SELECT * FROM tblBranchMinimumGroup 
				WHERE branchMinimumGroupID = '{$this->get['branchMinimumGroupID']}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);		
	}

}
?>