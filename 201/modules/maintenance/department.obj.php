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
	
	function getNextDivCode(){
		$qry = "SELECT MAX(divCode) + 1 AS newDivCode
				FROM  tblDepartment
				WHERE (deptLevel = '1') 
				AND (compCode = '{$_SESSION['company_code']}')";
		$res = $this->execQry($qry);
		$row = $this->getSqlAssoc($res);
		if($row['newDivCode']==0){
			return 1;
		}else{
			return $row['newDivCode'];
		}
	}
	
	function getNextDeptCode(){
		$qry = "SELECT MAX(deptCode) + 1 AS newDeptCode
				FROM  tblDepartment
				WHERE (deptLevel = '2') 
				AND (compCode = '{$_SESSION['company_code']}')
				AND divCode = '{$this->get['divCode']}'";
		$res = $this->execQry($qry);
		$row = $this->getSqlAssoc($res);
		if($row['newDeptCode']==0){
			return 1;	
		}else{
			return $row['newDeptCode'];
		}
	}
	
	function getNextSectCode(){
		$arrDeptCode = explode("-",$this->get['deptCode']);
		
		$qry = "SELECT MAX(sectCode) + 1 AS newSectCode
				FROM  tblDepartment
				WHERE (deptLevel = '3') 
				AND (compCode = '{$_SESSION['company_code']}')
				AND divCode = '{$this->get['divCode']}'
				AND deptCode = '{$arrDeptCode[0]}'";
		$res = $this->execQry($qry);
		$row = $this->getSqlAssoc($res);
		if($row['newSectCode']==0){
			return 1;
		}else{
			return $row['newSectCode'];
		}
	}	
	
	function getGLMinorList(){
		$qry = "SELECT * FROM tblGLMinorAcct 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
		
	function getDiv($divCode){
		 $qry = "SELECT * FROM tblDepartment 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND divCode = '{$divCode}'
				AND deptLevel = '1'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);		
	}
	
	function getDept($divCode,$deptCode){
		 $qry = "SELECT * FROM tblDepartment 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND divCode = '{$divCode}'
				AND deptCode = '{$deptCode}'
				AND deptLevel = '2'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);		
	}	
	
	function getSect($divCode,$deptCode,$sectCode){
		 $qry = "SELECT * FROM tblDepartment 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND divCode = '{$divCode}'
				AND deptCode = '{$deptCode}'
				AND sectCode = '{$sectCode}'
				AND deptLevel = '3'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);		
	}
	
	function checkDiv(){
		$qry = "SELECT divCode FROM tblDepartment 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND divCode = '{$this->get['divCode']}',
				AND deptLevel = '1'";
		$res = $this->execQry($qry);
		return $this->getRecCount($res);
	}
	
	function checkDept(){
		$qry = "SELECT deptCode FROM tblDepartment 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND divCode = '{$this->get['divCode']}'
				AND deptCode = '{$this->get['deptCode']}'
				AND deptLevel = '2'";
		$res = $this->execQry($qry);
		return $this->getRecCount($res);
	}

	function checkSect(){
		$qry = "SELECT sectCode FROM tblDepartment 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND divCode = '{$this->get['divCode']}'
				AND deptCode = '{$this->get['deptCode']}'
				AND sectCode = '{$this->get['sectCode']}'
				AND deptLevel = '3'";
		$res = $this->execQry($qry);
		return $this->getRecCount($res);
	}
	
	function toDepartmentDiv(){
		
		$qry = "INSERT INTO tblDepartment(compCode,divCode,deptCode,sectCode,deptDesc,deptShortDesc,deptGlCode,deptLevel,deptStat)
				VALUES('{$_SESSION['company_code']}',
					   '{$this->get['divCode']}',
					   '0','0',
					   '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
					   '".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."',
					   '{$this->get['txtglcode']}','1','".str_replace("'","''",stripslashes(strtoupper($this->get['cmbStat'])))."')";
		return $this->execQry($qry);
	}	
	
	function toDepartmentDept(){
		
		$qry = "INSERT INTO tblDepartment(compCode,divCode,deptCode,sectCode,deptDesc,deptShortDesc,deptGlCode,deptLevel,deptStat)
				VALUES('{$_SESSION['company_code']}',
					   '{$this->get['divCode']}',
					   '{$this->get['deptCode']}','0',
					   '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
					   '".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."',
					   '{$this->get['txtglcode']}','2','".str_replace("'","''",stripslashes(strtoupper($this->get['cmbStat'])))."')";
		return $this->execQry($qry);
	}	
	
	function toDepartmentSect(){
		
		$qry = "INSERT INTO tblDepartment(compCode,divCode,deptCode,sectCode,deptDesc,deptShortDesc,deptGlCode,deptLevel,deptStat)
				VALUES('{$_SESSION['company_code']}',
					   '{$this->get['divCode']}',
					   '{$this->get['deptCode']}','{$this->get['sectCode']}',
					   '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
					   '".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."',
					   '{$this->get['txtglcode']}','3','".str_replace("'","''",stripslashes(strtoupper($this->get['cmbStat'])))."')";
		return $this->execQry($qry);
	}
	
	function updtDeptartmentDiv(){
		$qry = "UPDATE tblDepartment 
				SET deptDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
					deptShortDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."',
					deptGlCode = '{$this->get['txtglcode']}',deptStat='".str_replace("'","''",stripslashes(strtoupper($this->get['cmbStat'])))."'
					WHERE compCode = '{$_SESSION['company_code']}'
					AND divCode = '{$this->get['divCode']}'
					AND deptLevel = '1'";
		return $this->execQry($qry);
	}
	
	function updtDeptartmentDept(){
		$qry = "UPDATE tblDepartment 
				SET deptDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
					deptShortDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."',
					deptGlCode = '{$this->get['txtglcode']}',deptStat='".str_replace("'","''",stripslashes(strtoupper($this->get['cmbStat'])))."'
					WHERE compCode = '{$_SESSION['company_code']}'
					AND divCode = '{$this->get['divCode']}'
					AND deptLevel = '2'
					AND DeptCode = '{$this->get['deptCode']}'";
		return $this->execQry($qry);
	}
	
	function updtDeptartmentSect(){
		$qry = "UPDATE tblDepartment 
				SET deptDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['Desc'])))."',
					deptShortDesc = '".str_replace("'","''",stripslashes(strtoupper($this->get['shrtDesc'])))."',
					deptGlCode = '{$this->get['txtglcode']}',deptStat='".str_replace("'","''",stripslashes(strtoupper($this->get['cmbStat'])))."'
					WHERE compCode = '{$_SESSION['company_code']}'
					AND divCode = '{$this->get['divCode']}'
					AND deptLevel = '3'
					AND DeptCode = '{$this->get['deptCode']}'
					AND sectCode = '{$this->get['sectCode']}'";
		return $this->execQry($qry);
	}	
	
	function getGLInfo($tbl,$glCode){
		
		$qry = "SELECT * FROM $tbl
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctCde = '{$glCode}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
}
?>