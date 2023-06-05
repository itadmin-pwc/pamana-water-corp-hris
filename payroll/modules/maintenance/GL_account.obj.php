<?

class GLAcctObj extends commonObj {
	
	var $get;
	
	var $session;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}
	
	function getGlAcct(){
		
		$qry = "SELECT * FROM ".$this->get['tbl']."
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctCde = '{$this->get['glCode']}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function checkGLAcct(){
		$qry = "SELECT acctCde FROM ".$this->get['tbl']."
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctCde = '" . str_replace("'","",stripslashes($this->get['GLCode'])). "'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function updtGLAccnt(){
		$qry = "UPDATE ".$this->get['tbl']." 
				SET acctCde = '".str_replace("'","",stripslashes($this->get['GLCode']))."',
					acctDesc = '".$this->strUpper($this->get['GLDesc'])."',
					acctDescShrt = '".$this->strUpper($this->get['GLShrtDesc'])."',
					acctStat = '{$this->get['GlStat']}'
					WHERE compCode = '{$_SESSION['company_code']}'
					AND acctCde = '".$this->strUpper($this->get['hdnGlCod'])."'";
		return $this->execQry($qry);
	}
	
	function toGLAccnt(){
		$qry = "INSERT INTO ".$this->get['tbl']."(compCode,acctCde,acctDesc,acctDescShrt,acctStat)
				VALUES('{$_SESSION['company_code']}',
					   '".str_replace("'","",stripslashes($this->get['GLCode']))."',
					   '".$this->strUpper($this->get['GLDesc'])."',
					   '".$this->strUpper($this->get['GLShrtDesc'])."',
					   '{$this->get['GlStat']}')";
		return $this->execQry($qry);
	}
	
	function getGLMajorList(){
		$qry = "SELECT * FROM tblGLMajorAcct 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getGLMinorList(){
		$qry = "SELECT * FROM tblGLMinorAcct 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getGLStoreList(){
		$qry = "SELECT * FROM tblGLStoreAcct 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND acctStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getGLPayrollAcct(){
		$qry = "SELECT * FROM tblGLPayrollAcct
				WHERE compCode = '{$_SESSION['company_code']}'
				AND majorAcctCde = '{$this->get['glCodeMajor']}'
				AND minorAcctCde = '{$this->get['glCodeMinor']}'
				AND storeAcctCde = '{$this->get['glCodeStore']}' ";
		$res = $this->execQry($qry);
		return  $this->getSqlAssoc($res);
	}
	
	function checkGLPayAcct(){
		$qry = "SELECT majorAcctCde,minorAcctCde,storeAcctCde FROM tblGLPayrollAcct
				WHERE compCode = '{$_SESSION['company_code']}'
				AND majorAcctCde = '{$this->get['cmbGLMajor']}'
				AND minorAcctCde = '{$this->get['cmbGLMinor']}'
				AND storeAcctCde = '{$this->get['cmbGLStore']}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function updtGLPay(){
		$qry = "UPDATE tblGLPayrollAcct 
				SET majorAcctCde = '{$this->get['cmbGLMajor']}',
				minorAcctCde = '{$this->get['cmbGLMinor']}',
				storeAcctCde = '{$this->get['cmbGLStore']}',
				acctDesc = '".$this->strUpper($this->get['GLDesc'])."',
				acctDescShrt = '".$this->strUpper($this->get['GLShrtDesc'])."',
				acctStat = '{$this->get['GlStat']}' 
				WHERE compCode = '{$_SESSION['company_code']}'
				AND majorAcctCde = '{$this->get['hdnGlCdeMajor']}'
				AND minorAcctCde = '{$this->get['hdnGlCdeMinor']}'
				AND storeAcctCde = '{$this->get['hdnGlCdeStore']}'";
		return $this->execQry($qry);
	}
	
	function toGLPayroll(){
		$qry = "INSERT INTO tblGLPayrollAcct(compCode,majorAcctCde,minorAcctCde,storeAcctCde,acctDesc,acctDescShrt,acctStat)
				VALUES('{$_SESSION['company_code']}',
					   '{$this->get['cmbGLMajor']}',
					   '{$this->get['cmbGLMinor']}',
					   '{$this->get['cmbGLStore']}',
					   '".$this->strUpper($this->get['GLDesc'])."',
					   '".$this->strUpper($this->get['GLShrtDesc'])."',
					   '{$this->get['GlStat']}')";
		return $this->execQry($qry);
	}
}
?>