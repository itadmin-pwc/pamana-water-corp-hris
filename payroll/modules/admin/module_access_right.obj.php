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
								 dateUpdt = '".date('Y-m-d')."'
								 WHERE compCode = '1'
								 AND empNo = '{$this->get['empNo']}' ";
			
								 					 								 									
		$this->execQry($qryUpdtUserLogInInfo);	
		$sql = "
		
			
			Insert into  tblUsers (compCode,userPass,pagesTNA,pages201,pagesPayroll,userStat,category)
			select 1,userPass,pagesTNA,pages201,pagesPayroll,userStat,category from tblUsers  where empNo not in (Select empNo from tblUsers) and empNo='{$this->get['empNo']}' ;
			
			
			";
			return  $this->execQry($sql);	

	}
	
	function chkUser($compCode,$empNo){
		$qryChkUsers = "Select * from tblUsers where compCode='".$compCode."' and empNo='".$empNo."'";
		
		$resqryChkUsers = $this->execQry($qryChkUsers);	
		
		return $resqryChkUsers;
	}
	
	function insNewUserAcct($compCode,$empNo,$conV_userPass){
		switch($compCode) {
			case 1:
				$qryEmpMast = "Insert into pg_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '2', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into dfclark_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '4', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into dfsubic_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '5', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";
			break;	

			case 2:
				$qryEmpMast = "Insert into pgjr_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '1', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into dfclark_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '4', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into dfsubic_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '5', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";
				
				$qryEmpMast .= "Insert into LUSITANO..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '3', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";
				$qryEmpMast .= "Insert into PARCO_GANT_DIAMOND..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '7', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";
				$qryEmpMast .= "Insert into PARCO_GANT_D3..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '8', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into PARCO_SUPER_RETAIL_XV..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '9', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into PARCO_SUPER_AGORA..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '10', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into PARCO_SUPER_RETAIL_VII..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '11', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into PARCO_SCV..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '12', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into PG_SUBIC..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '13', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into COMPANY_E_PAYROLL..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '15', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

			break;	

			case 4:
				$qryEmpMast = "Insert into pgjr_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '1', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into pg_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '2', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into dfsubic_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '5', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";
			break;	
			case 5:
				$qryEmpMast = "Insert into pgjr_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '1', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into pg_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '2', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";

				$qryEmpMast .= "Insert into dfclark_payroll..tblEmpMast ( compCode, empNo, empLastName, empFirstName, empMidName, empBrnCode, empStat, empPayGrp, empPayCat,id) Select '4', empNo, empLastName, empFirstName, empMidName, empBrnCode, 'USER', '0', '0',id from tblEmpMast where empNo='$empNo' and compCode='$compCode'; \n";
			break;	

		}
		$Trns = $this->beginTran();
		if($Trns){
			//$Trns = $this->execQry($qryEmpMast);
		}

		$insQry = "Insert into  tblUsers (compCode,empNo,userPass,userLevel,dateEnt,userStat)
				   values('1','".$empNo."','".$conV_userPass."','3','".date("Y-m-d")."','A')";
		
		if($Trns){
			$Trns = $this->execQry($insQry);
		}
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return 0;
		}
		else{
			$Trns = $this->commitTran();
			return 1;	
		}						
	}
	
	function updateUserAcct($compCode,$empNo, $passWord)
	{
		$qryUpdateUserAcct = "Update tblUsers set userPass='".$passWord."' where compCode='1' and empNo='".$empNo."' \n";
		
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
		$qryUpdate = "Update tblUsers set category=".$empCat." where empNo='".$empNo."' and compCode='1' \n";
		

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