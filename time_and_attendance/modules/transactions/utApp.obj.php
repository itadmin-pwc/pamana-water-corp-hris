<?
include("../../../includes/dateClass.php");

class utAppObj extends dateDiff {

	var $get;
	
	var $session;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	function getLastRefNo()
	{
		$qryLastRefNo = "Select refNo as lastRefNo from tblTK_UTApp order by seqNo desc";
		$rsLastRefNo = $this->execQry($qryLastRefNo);
		
		return $this->getSqlAssoc($rsLastRefNo);
	}
	
	
	function addUtApp() {	
		
		$arr_lastRefNo = $this->getLastRefNo();
		$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;
		
		$qryaddUtApp = "INSERT INTO tblTK_utApp (compCode,empNo,utDate,refNo,dateFiled,utReason,offTimeOut,utTimeOut,
						utStat,userAdded,dateAdded)
						VALUES ('$_SESSION[company_code]','$_GET[empNo]','$_GET[dateUt]','".$lastRefNo."',
								 '$_GET[dateFiled]','$_GET[utReason]','$_GET[offTime]','$_GET[UTOut]','$_GET[utStat]',
								 '$_SESSION[employee_number]','".date('Y-m-d')."')";
		
		$resaddUtApp = $this->execQry($qryaddUtApp);
		
		if($resaddUtApp){
			return true;
		}
		else {
			return false;
			
		}	
	}
	
	function getPerEmpBranch() {
	
		$empNum = $_SESSION['employee_number'];
		
		$qryGetPerEmpBranch = "SELECT * FROM tblUSerBranch 
							   WHERE compCode = '{$_SESSION['company_code']}' and empNo = {$empNum}";
							   
		if ($resGetPerEmpBranch) {
			return true;
		}
		else{
			return false;
		}
	
	}
	
	function checkUtAppDtl(){
		
		$qryCheckUtAppDtl = "SELECT empNo FROM tblTK_utApp
							WHERE compCode = '{$this->session['company_code']}'
							AND refNo = '{$this->get['eRefNo']}'
							";
		$resCheckUtAppDtl = $this->execQry($qryCheckUtAppDtl);
		if($resCheckUtAppDtl){
			return $this->getRecCount($resCheckUtAppDtl) ;		
		}
		else {
			return -1;
		}
	}

	function delUtAppDtl(){
		$qryDelOtAppDtl = "DELETE FROM tblTK_utApp 
						  WHERE compCode = '{$this->session['company_code']}'
						  AND refNo = '{$this->get['dRefNo']}'";
						  
		$resDelUtAppDtl = $this->execQry($qryDelUtAppDtl);
		
		if($resDelUtAppDtl){
			return true;
		}
		else {
			return false;
		}
		
	}
	
	function getTblData($tbl, $cond, $orderBy, $ouputType)
	{
		$qryTblInfo = "Select * from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
		//echo $qryTblInfo;
		$resTblInfo = $this->execQry($qryTblInfo);
		if($ouputType == 'sqlAssoc')
			return $this->getSqlAssoc($resTblInfo);
		else
			return $this->getArrRes($resTblInfo);
	}

	function updateUTDtl(){
		
		$qryUpdateUTDtl = "UPDATE tblTK_UTApp SET utDate='".date("Y-m-d", strtotime($_GET["dateUt"]))."', offTimeOut='$_GET[txtSched]', 
			              utTimeOut='$_GET[txtUtOut]',utReason='$_GET[cmbReasons]' WHERE seqNo='$_GET[inputTypeSeqNo]'";

		$resUpdateUTDtl = $this->execQry($qryUpdateUTDtl);
		
		if ($resUpdateUTDtl){
			return true;
		}else{
			return false; 
		}
	
}

}
?>