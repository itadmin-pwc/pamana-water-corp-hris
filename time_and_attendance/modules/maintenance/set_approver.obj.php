<?
class ApprObj extends commonObj{

	var $get;
	
	var $session;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}
	
	function addApprover() {
		$approverEmpNo = addslashes($_GET['approverEmpNo']);
    	$subordinateEmpNo = addslashes($_GET['subordinateEmpNo']);

		$qryaddApp = "INSERT INTO tbltna_approver (approverEmpNo,subordinateEmpNo,status,dateValid,addedBy,addedAt,updatedAt,compCode)
						VALUES ('$approverEmpNo','$subordinateEmpNo','A','2200-12-30','$_SESSION[employee_number]','".date("Y-m-d")."','".date("Y-m-d")."','$_SESSION[company_code]')";
		
		$resaddApp = $this->execQry($qryaddApp);
		
		if($resaddApp){
			return true;
		}
		else {
			return false;
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