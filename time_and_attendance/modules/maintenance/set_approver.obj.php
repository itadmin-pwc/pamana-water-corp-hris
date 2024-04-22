<?
class ApprObj extends commonObj{

	var $get;
	
	var $session;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}
	
	function addApprover() {
		$qryaddOtApp = "INSERT INTO tblTK_otApp (compCode,empNo,otDate,refNo,dateFiled,otReason,otIn,otOut,crossTag,
						otStat,userAdded,dateAdded)
						VALUES ('$_SESSION[company_code]','$_GET[empNo]','$_GET[dateOt]','".$lastRefNo."',
								 '$_GET[dateFiled]','$_GET[otReason]','$_GET[OTIn]','$_GET[OTOut]','{$_GET['checked']}','$_GET[otStat]',
								 '$_SESSION[employee_number]','".date("Y-m-d")."')";
		
		$resaddOtApp = $this->execQry($qryaddOtApp);
		
		if($resaddOtApp){
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