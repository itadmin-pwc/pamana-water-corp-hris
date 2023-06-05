<?
class maintDeduct extends commonObj {
	
	var $get;
	
	var $session;
	
	private $fileName='deductions.obj.php';
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}
	
	function getDedLastRefNo(){
		
		$qryGetLastRefNo = "SELECT dedRefNo FROM tblDedRefNo
							     WHERE compCode = '{$this->session['company_code']}'";
		$resGetLastRefNo = $this->execQry($qryGetLastRefNo);
		 return $this->getSqlAssoc($resGetLastRefNo);
	}
	
	function updateDedLastRefNo($newrefNo){
		 $qryUpdateDedLastRefNo = "UPDATE tblDedRefNo 
							SET dedRefNo = '{$newrefNo}'  
							WHERE compCode = '{$this->session['company_code']}'";
		$resUpdateDedLastRefNo = $this->execQry($qryUpdateDedLastRefNo);
	}
	
	function checkDedHeader(){
		$qrycheckDedHdr = "SELECT refNo FROM tbldedTranHeader 
							WHERE compCode = '{$this->session['company_code']}'
							AND refNo = '{$this->get['refNo']}'";
		$rescheckDedHdr = $this->execQry($qrycheckDedHdr);
		return $this->getRecCount($rescheckDedHdr);
	}
	
	function checkDedDetail(){
		
		$qryCheckDedDtl = "SELECT empNo FROM tblDedTranDtl
							WHERE compCode = '{$this->session['company_code']}'
							AND refNo = '{$this->get['refNo']}'
							AND empNo = '{$this->get['txtAddEmpNo']}'
							AND trnCntrlNo = '{$this->get['txtAddCntrlNo']}'";
		$resCheckDedDtl = $this->execQry($qryCheckDedDtl);
		return $this->getRecCount($resCheckDedDtl);
	}

	function addDedHeader(){
		$trnsType = explode("-",$this->get['cmbTrnType']);
		$period = explode("-",$this->get['cmbPeriod']);
		
		$qryaddDedHeader = "INSERT INTO tbldedTranHeader(compCode,refNo,
													     trnCode,
													     trnPriority,dedRemarks,dedStat,pdYear,pdNumber)
										VALUES('{$this->session['company_code']}','{$this->get['refNo']}',
											   '{$trnsType[0]}','{$trnsType[1]}','".str_replace("'","''",stripslashes($this->get['dedRem']))."','{$this->get['cmbDedStat']}','{$period[0]}','{$period[1]}')";
		$resaddDedHeader = $this->execQry($qryaddDedHeader);
		if($resaddDedHeader){
			return true;
		}
		else {
			return false;
		}	
		
	}
	
	function addDedDetail($numExec){
		$trnsType = ($numExec == 1) ? explode("-",$this->get['cmbTrnType']) : explode("-",$this->get['hdnTrans']); 
		$payGrp = explode("-",$this->get['txtAddPayGrp']);
		$paycat = explode("-",$this->get['txtAddPayCat']);
		
		$qryAddDedDetail = "INSERT INTO tblDedTranDtl(compCode,refNo,empNo,
													  trnCntrlNo,trnCode,trnPriority,
													  trnAmount,payGrp,payCat,dedStat)
							VALUES('{$this->session['company_code']}','{$this->get['refNo']}','{$this->get['txtAddEmpNo']}',
								   '{$this->get['txtAddCntrlNo']}','{$trnsType[0]}','{$trnsType[1]}',
								   '{$this->get['txtAddAmnt']}','{$payGrp[0]}','{$_SESSION['pay_category']}','{$this->get['cmbDedStat']}')";
		$reAddDedDetail = $this->execQry($qryAddDedDetail);
		if($reAddDedDetail){
			return true;
		}
		else {
			return false;
		}	
	}
	
	function updateDedHeader(){
		
		$qryUpdtdedHdr = "UPDATE tbldedTranHeader SET dedRemarks = '".str_replace("'","''",stripslashes($this->get['dedRem']))."',dedStat = '{$this->get['cmbDedStat']}'
							WHERE compCode = '{$this->session['company_code']}'
							AND refNo = '{$this->get['refNo']}' ";
		$resUpdtdedHdr = $this->execQry($qryUpdtdedHdr);
		
		$qryUpdtDtlStat = "UPDATE tblDedTranDtl SET dedStat = '{$this->get['cmbDedStat']}'
							WHERE compCode = '{$this->session['company_code']}'
							AND refNo = '{$this->get['refNo']}'";
		$resUpdtDtlStat = $this->execQry($qryUpdtDtlStat);
		
		if($resUpdtdedHdr && $resUpdtDtlStat){
			return true;
		}
		else {
			return false;
		}	
	}
	
	function getDedTranHEader(){
		
		$qryGetHeader = "SELECT * FROM tblDedTranHeader
						 WHERE compCode = '{$this->session['company_code']}'
						 AND refNo = '{$this->get['refNo']}'";
		$resGetHeader = $this->execQry($qryGetHeader);
		if($this->getRecCount($resGetHeader) == 1){
			return $this->getSqlAssoc($resGetHeader);
		}
		else{
			return $this->getArrRes($resGetHeader);
		}
	}
	
	function deleDedDtl(){
		$qryDeleDedDtl = "DELETE FROM tblDedTranDtl 
						  WHERE compCode = '{$this->session['company_code']}'
						  AND refNo = '{$this->get['refNo']}'
						  AND empNo = '{$this->get['empNo']}'
						  AND trnCntrlNo = '".trim($this->get['cntrlNo'])."'";
		$resDeleDedDtl = $this->execQry($qryDeleDedDtl);
		if($resDeleDedDtl){
			return true;
		}
		else {
			return false;
		}
		
	}
	
	function deleDeduc(){
		$qryDeleDedHdr = "DELETE FROM tblDedTranHeader
						WHERE refNo = '{$this->get['refNo']}'
						AND compCode = '{$this->session['company_code']}'";
		$resDeleDedHdr = $this->execQry($qryDeleDedHdr);
		
		$qryDeleDedDtl = "DELETE FROM tblDedTranDtl 
						WHERE refNo = '{$this->get['refNo']}'
						AND compCode = '{$this->session['company_code']}'";
		$resDeleDedDtl = $this->execQry($qryDeleDedDtl);
		
		if($resDeleDedHdr && $resDeleDedDtl){
			return true;
		}
		else{
			return false;
		}

	}
}

?>