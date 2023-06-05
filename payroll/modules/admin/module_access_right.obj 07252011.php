<?

class moduleAccessRightsObj extends commonObj {

	var $get;
	
	var $sessionVars;
	
	function __construct($sess,$method){
		$this->get = $method;
		$this->sessionVars = $sess;
	}
	
	function getParentModule(){
		  
		$parentModule = array();
		
	    $qryParentModule="select moduleName from tblPayrollMenu 
	      				  WHERE moduleStat = 'A' 
	      				  ORDER BY menuOrder,moduleOrder ASC";
	
	    $resParentModule = $this->execQry($qryParentModule);
	    $arrParentModule = $this->getArrRes($resParentModule);

	    foreach ($arrParentModule as $arrParentModuleVal){
	    	  $parentModule[] = $arrParentModuleVal['moduleName'];
	    }
	    return array_unique($parentModule);
	}
	
	function getChildModule($parentName){
		
		$qryChildModule = "SELECT moduleId,label FROM tblPayrollMenu
							WHERE moduleStat = 'A'
							AND moduleName = '{$parentName}'
							ORDER BY menuOrder,moduleOrder asc";
		$resChildModule = $this->execQry($qryChildModule);
		return $this->getArrRes($resChildModule);
	}
	
	function getPagesPayroll($agesPayroll){
		$arrpagesPayroll = explode(',',$agesPayroll);
		return $arrpagesPayroll;
	}
	
	function updateUserLogInInfo(){

		$qryUpdtUserLogInInfo = "UPDATE tblUsers SET 
								 pagesPayRoll = '{$this->get['chldMdle']}',
								 dateUpdt = '".date('n/d/Y')."'
								 WHERE compCode = '{$this->sessionVars['company_code']}'
								 AND empNo = '{$this->get['empNo']}' ";
		return  $this->execQry($qryUpdtUserLogInInfo);	

	}
	
	function chkUser($compCode,$empNo){
		$qryChkUsers = "Select * from tblUsers where compCode='".$compCode."' and empNo='".$empNo."'";
		
		$resqryChkUsers = $this->execQry($qryChkUsers);	
		
		return $resqryChkUsers;
	}
	
	function insNewUserAcct($compCode,$empNo,$conV_userPass){
		$insQry = "Insert into tblUsers(compCode,empNo,userPass,userLevel,dateEnt,userStat)
				   values('".$compCode."','".$empNo."','".$conV_userPass."','3','".date("Y-m-d")."','A')";
		$resinsQry = $this->execQry($insQry);	
		
		if(!$resinsQry){
			return 0;
		}
		else{
			return 1;
		}
	}
	
	function updateUserAcct($compCode,$empNo, $passWord)
	{
		$qryUpdateUserAcct = "Update tblUsers set userPass='".$passWord."' where compCode='".$compCode."' and empNo='".$empNo."'";
		$resUpdtUserLogInInfo = $this->execQry($qryUpdateUserAcct);	
		
		if(!$resUpdtUserLogInInfo){
			return 0;
		}
		else{
			return 1;
		}
	}
	
	function getPayCategory()
	{
		$qryGetPayCat = "Select * from tblPayCat where compCode='".$_SESSION["company_code"]."' and payCatStat='A'";
		$resGetPayCat = $this->execQry($qryGetPayCat);
		return $this->getArrRes($resGetPayCat);
	}
	
	function updateUsrCat($empCat,$empNo)
	{
		$empCat = ($empCat!="NULL"?"'".$empCat."'":"NULL");
		$qryUpdate = "Update tblUsers set category=".$empCat." where empNo='".$empNo."' and compCode='".$_SESSION["company_code"]."'";
		$resUpdate = $this->execQry($qryUpdate);
		
		if(!$resUpdate){
			return 0;
		}
		else{
			return 1;
		}
	}
	
}

class getPassObj extends commonObj {
	
	var $get;
	
	var $sessionVars;
	
	function __construct($sess,$method){
		$this->get = $method;
		$this->sessionVars = $sess;
	}	
	
	function getUserPass(){
		
		$qryGetPass = "SELECT userPass
						FROM tblUsers 
						WHERE compCode = '{$this->get['cmbCompny']}'
						AND empNo ='{$this->get['txtAddEmpNo']}' ";
		$resGetPass = $this->execQry($qryGetPass);
		$rowGetPass = $this->getSqlAssoc($resGetPass);
		
		if(!empty($rowGetPass['userPass']) && $rowGetPass['userPass'] != ""){
			return  $rowGetPass['userPass'];
		}
		else{
			return "";
		}
	}
	
}


?>