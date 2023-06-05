<?
class earningsObj extends commonObj {

	var $get;
	
	var $session;
	
	var $todayDate;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
		$this->todayDate = date('Y-m-d');
	}	
	
	function getEarnLastRefNo(){
		
		$qryGetLastRefNo = "SELECT earnRefNo FROM tblEarnRefNo
							     WHERE compCode = '{$this->session['company_code']}'";
		$resGetLastRefNo = $this->execQry($qryGetLastRefNo);
		if($resGetLastRefNo){
			return $this->getSqlAssoc($resGetLastRefNo);
		}
		else {
			return false;
		}	
	}	
	
	function updateEarbLastRefNo($newrefNo){
		
		 $qryUpdateEarnLastRefNo = "UPDATE tblEarnRefNo 
							SET earnRefNo = '{$newrefNo}'  
							WHERE compCode = '{$this->session['company_code']}'";
		$resUpdateEarnLastRefNo = $this->execQry($qryUpdateEarnLastRefNo);
		if($resUpdateEarnLastRefNo){
			return true;
		}
		else {
			return false;
		}
	}
	
	function checkEarnHeader(){
		$qrycheckEarnHdr = "SELECT refNo FROM tblEarnTranHeader 
							WHERE compCode = '{$this->session['company_code']}'
							AND refNo = '{$this->get['refNo']}'";
		$rescheckEarnHdr = $this->execQry($qrycheckEarnHdr);
		if($rescheckEarnHdr){
			return $this->getRecCount($rescheckEarnHdr);		
		}
		else {
			return -1;
		}
	}
	
	function checkEarnDetail(){
		
		$qryCheckEarnDtl = "SELECT empNo FROM tblEarnTranDtl
							WHERE compCode = '{$this->session['company_code']}'
							AND refNo = '{$this->get['refNo']}'
							AND empNo = '{$this->get['txtAddEmpNo']}'
							AND trnCntrlNo = '{$this->get['txtAddCntrlNo']}'";
		$resCheckEarnDtl = $this->execQry($qryCheckEarnDtl);
		if($resCheckEarnDtl){
			return $this->getRecCount($resCheckEarnDtl) ;		
		}
		else {
			return -1;
		}
	}
	
	function addEarnHeader(){
				
		$period  = explode("-",$this->get['cmbPeriod']);
		$qryaddEarnHeader = "INSERT INTO tblEarnTranHeader(compCode,refNo,
													     trnCode,earnRem,earnStat,pdYear,pdNumber,dateAdded,userAdded)
										VALUES('{$this->session['company_code']}','{$this->get['refNo']}',
											   '{$this->get['cmbTrnType']}','".str_replace("'","''",stripslashes($this->get['earnRem']))."','{$this->get['cmbEarnStat']}','{$period[0]}','{$period[1]}','{$this->todayDate}','{$this->session['employee_number']}')";
		$resaddEarnHeader = $this->execQry($qryaddEarnHeader);
		if($resaddEarnHeader){
			return true;
		}
		else {
			return false;
		}	
	}
	
	function addEarnDetail($numExc){

		$payGrp = explode("-",$this->get['txtAddPayGrp']);
		$paycat = explode("-",$this->get['txtAddPayCat']);

		if($numExc == 1){
			$trnCode = $this->get['cmbTrnType'];
		}
		else{
			$trnCode = $this->get['hdnTrnsType'];
		}
		
		$taxCd = $this->getTransType($this->session['company_code'],'earnings',"AND trnCode = '".trim($trnCode)."' ");

		$qryAddEarnDetail = "INSERT INTO tblEarnTranDtl(compCode,refNo,empNo,
													  trnCntrlNo,trnCode,
													  trnAmount,payGrp,payCat,earnStat,trnTaxCd)
							VALUES('{$this->session['company_code']}','{$this->get['refNo']}','".trim($this->get['txtAddEmpNo'])."',
								   '{$this->get['txtAddCntrlNo']}','{$trnCode}',
								   '{$this->get['txtAddAmnt']}','{$payGrp[0]}','{$this->get['hdnPayCat']}','{$this->get['cmbEarnStat']}','{$taxCd[0]['trnTaxCd']}')";
		$reAddEarnDetail = $this->execQry($qryAddEarnDetail);
		
		if($reAddEarnDetail){
			return true;
		}
		else {
			return false;
		}	
	}
	
	function getEarnTranHeader(){
		
		$qryGetHeader = "SELECT * FROM tblEarnTranHeader
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
	
	function updateEarnHeader(){
		
		$qryUpdtEarnHdr = "UPDATE tblEarnTranHeader SET earnRem = '".str_replace("'","''",stripslashes($this->get['earnRem']))."', earnStat = '{$this->get['cmbEarnStat']}'
							    WHERE compCode = '{$this->session['company_code']}'
							    AND refNo = '{$this->get['refNo']}' ";
		$resUpdtEarnHdr = $this->execQry($qryUpdtEarnHdr);
		
		$qryUpdateDtlStat = "UPDATE tblEarnTranDtl SET earnStat = '{$this->get['cmbEarnStat']}'
							 WHERE compCode = '{$this->session['company_code']}' 
							 AND refNo = '{$this->get['refNo']}'";
		$resUpdateDtlStat = $this->execQry($qryUpdateDtlStat);
		
		if($resUpdtEarnHdr && $resUpdateDtlStat){
			return true;
		}
		else {
			return false;
		}	
	}
	
	function deleEarnDtl(){
		$qryDeleEarnDtl = "DELETE FROM tblEarnTranDtl 
						  WHERE compCode = '{$this->session['company_code']}'
						  AND refNo = '{$this->get['refNo']}'
						  AND empNo = '{$this->get['empNo']}'
						  AND trnCntrlNo = '".trim($this->get['cntrlNo'])."'";
		$resDeleEarnDtl = $this->execQry($qryDeleEarnDtl);
		if($resDeleEarnDtl){
			return true;
		}
		else {
			return false;
		}
		
	}
	
	function deleEarn(){
		$qryDeleEarnHdr = "DELETE FROM tblEarnTranHeader 
						WHERE refNo = '{$this->get['refNo']}'
						AND compCode = '{$this->session['company_code']}'";
		$resDeleEarnHdr = @$this->execQry($qryDeleEarnHdr);

		$qryDeleDtl = "DELETE FROM tblEarnTranDtl 
						WHERE refNo = '{$this->get['refNo']}'
						AND compCode = '{$this->session['company_code']}'";
		$resDeleDtl = @$this->execQry($qryDeleDtl);

		if($resDeleEarnHdr && $resDeleDtl){
			return true;
		}
		else{
			return false;
		}

	}
	
}

?>