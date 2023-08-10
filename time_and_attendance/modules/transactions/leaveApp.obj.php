<?
class leaveAppObj extends commonObj{

	var $get;
	
	var $session;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	function getLastRefNo()
	{
		$qryLastRefNo = "Select refNo as lastRefNo from tblTK_refno";
		// $qryLastRefNo = "Select refNo as lastRefNo from tblTK_LeaveApp order by seqNo desc";
		$rsLastRefNo = $this->execQry($qryLastRefNo);
		
		return $this->getSqlAssoc($rsLastRefNo);
	}
	
	function getTsAppType(){
		$appType="Select * from tblTK_AppTypes where leaveTypeTag = 'Y' order by appTypeShortDesc";
		$res= $this->execQry($appType);
		return $this->getArrRes($res);
	}	
	
	function getType(){
		$appTypeCd="SELECT * FROM tblTK_LeaveApp INNER JOIN
                    tblTK_AppTypes ON tblTK_LeaveApp.tsAppTypeCd = tblTK_AppTypes.tsAppTypeCd
					WHERE tblTK_LeaveApp.seqNo='$_GET[inputTypeSeqNo]'";
		$resApp= $this->execQry($appTypeCd);
		return $this->getArrRes($resApp);
	}
	
	function updateLastRefNo($newrefNo){
		 $qryUpdateLastRefNo = "UPDATE tblTK_RefNo 
							SET refNo = '$newrefNo'  
							WHERE compCode = {$_SESSION['company_code']}";
		$resUpdateLastRefNo = $this->execQry($qryUpdateLastRefNo);
		if($resUpdateLastRefNo){
			return true;
		}
		else {
			return false;
		}
		
	}
	
	function addLeaveApp() {	
		$arr_lastRefNo = $this->getLastRefNo();
		$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;
		$qryaddLeaveApp = "INSERT INTO tblTK_LeaveApp (compCode,empNo,refNo,dateFiled,lvDateFrom, lvFromAMPM, lvDateTo, lvToAMPM,
						tsAppTypeCd, lvReason, lvStat, deductTag, userAdded,dateAdded)
						VALUES ('$_SESSION[company_code]','$_GET[empNo]','".$lastRefNo."','$_GET[dateFiled]','$_GET[lvDateFrom]',
								 '$_GET[lvFromAMPM]','$_GET[lvDateTo]','$_GET[lvToAMPM]','$_GET[tsAppTypeCd]',
								 '$_GET[lvReason]','$_GET[lvStat]',".($_GET["chkDeduct"]=="Y"?"'Y'":"NULL").",
								 '$_SESSION[employee_number]','".date("Y-m-d")."')";
									
		$resaddLeaveApp = $this->execQry($qryaddLeaveApp);
		
		if($resaddLeaveApp){
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
	
	function checkLeaveAppDtl(){
		
		$qryCheckLeaveAppDtl = "SELECT empNo FROM tblTK_LeaveApp
							WHERE compCode = '{$this->session['company_code']}'
							AND refNo = '{$this->get['eRefNo']}'
							";
		$resCheckLeaveAppDtl = $this->execQry($qryCheckLeaveAppDtl);
		if($resCheckLeaveAppDtl){
			return $this->getRecCount($resCheckLeaveAppDtl) ;		
		}
		else {
			return -1;
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

	function countRecord($tbl, $cond)
	{
		$qryTblInfo = "Select COUNT(*) as record from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond;
		$resTblInfo = $this->execQry($qryTblInfo);
		$num = $this->getSqlAssoc($resTblInfo);
		return $num['record'];
	}
			
	function updateLeaveDtl() {
		
		$qryUpdateLeaveDtl = "UPDATE tblTK_LeaveApp SET lvDateFrom='".date("Y-m-d", strtotime($_GET["lvDateFrom"]))."', 
							lvFromAMPM = '$_GET[lvFrom]', lvDateTo='".date("Y-m-d", strtotime($_GET["lvDateTo"]))."', 
							lvToAMPM = '$_GET[lvTo]', tsAppTypeCd = '$_GET[tsAppTypeCd]', lvReason='$_GET[lvReason]',
							deductTag = ".($_GET["chkDeduct"]=="Y"?"'Y'":"NULL")." 
						    WHERE seqNo='$_GET[inputTypeSeqNo]'";

		$resUpdateLeaveDtl = $this->execQry($qryUpdateLeaveDtl);
		
		if ($resUpdateLeaveDtl){
			return true;
		}else{
			return false; 
		}
	}

}


?>