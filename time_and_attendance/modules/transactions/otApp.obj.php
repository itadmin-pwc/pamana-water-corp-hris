<?
class otAppObj extends commonObj{

	var $get;
	
	var $session;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	function getLastRefNo()
	{
		$qryLastRefNo = "Select otrefNo as lastRefNo from tblTK_refno";
		$rsLastRefNo = $this->execQry($qryLastRefNo);
		
		return $this->getSqlAssoc($rsLastRefNo);
	}
	
	
	function updateLastRefNo($newrefNo){
		 $qryUpdateLastRefNo = "UPDATE tblTK_RefNo 
							SET otrefNo = '$newrefNo'  
							WHERE compCode = {$_SESSION['company_code']}";
		$resUpdateLastRefNo = $this->execQry($qryUpdateLastRefNo);
		if($resUpdateLastRefNo){
			return true;
		}
		else {
			return false;
		}
		
	}
	
	function addOtApp() {	
		$arr_lastRefNo = $this->getLastRefNo();
		$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;
		
		$qryaddOtApp = "INSERT INTO tblTK_otApp (compCode,empNo,otDate,refNo,dateFiled,otReason,otIn,otOut,crossTag,
						otStat,userAdded,dateAdded)
						VALUES ('$_SESSION[company_code]','$_GET[empNo]','$_GET[dateOt]','".$lastRefNo."',
								 '$_GET[dateFiled]','$_GET[otReason]','$_GET[OTIn]','$_GET[OTOut]','{$_GET['checked']}','$_GET[otStat]',
								 '$_SESSION[employee_number]','".date("Y-m-d")."')";
		
		$resaddOtApp = $this->execQry($qryaddOtApp);
		
		if($resaddOtApp){
				if($this->updateLastRefNo($lastRefNo)){
				return true;
			}
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
	
	function checkOtAppDtl(){
		
		$qryCheckOtAppDtl = "SELECT empNo FROM tblTK_otApp
							WHERE compCode = '{$this->session['company_code']}'
							AND otDate = '{$_GET[dateOt]}' AND empNo = '{$_GET['txtAddEmpNo']}'
							";
		$resCheckOtAppDtl = $this->execQry($qryCheckOtAppDtl);
		if($resCheckOtAppDtl){
			return $this->getSqlAssoc($resCheckOtAppDtl);		
		}
		
	}

	function delOtAppDtl(){
		$qryDelOtAppDtl = "DELETE FROM tblTK_OtApp 
						  WHERE refNo = '{$_GET['dRefNo']}'";
						  
		$resDelOtAppDtl = $this->execQry($qryDelOtAppDtl);
		
		if($resDelOtAppDtl){
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
	
	function updateOTDtl() {
		
		$qryUpdateOTDtl = "UPDATE tblTK_OTApp SET otDate='".date("Y-m-d", strtotime($_GET["otDate"]))."',
			               otIn='$_GET[otIn]',otOut='$_GET[otOut]',otReason='$_GET[cmbReasons]'";

		if ($_GET["checked"] =='Y'){
			$qryUpdateOTDtl .= ", crossTag='Y'";	   
		}else{	
			$qryUpdateOTDtl .= ", crossTag=''";
		}
		
		$qryUpdateOTDtl .="WHERE seqNo='$_GET[inputTypeSeqNo]'";

		$resUpdateOTDtl = $this->execQry($qryUpdateOTDtl);
		
		if ($resUpdateOTDtl){
			return true;
		}else{
			return false; 
		}
	}
}

?>