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
		
	    $qryParentModule="select moduleName from tbl201Menu 
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
		
		$qryChildModule = "SELECT moduleId,label FROM tbl201Menu
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
								 pages201 = '{$this->get['chldMdle']}',
								 dateUpdt = '".date('n/d/Y')."'
								 WHERE compCode = '{$this->sessionVars['company_code']}'
								 AND empNo = '{$this->get['empNo']}' ";
		$resUpdtUserLogInInfo = $this->execQry($qryUpdtUserLogInInfo);	
		if(!$resUpdtUserLogInInfo){
			return false;
		}
		else{
			return true;
		}
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
	
	function chkUserBranch($empNo)
	{
		$qryUserBranch = "Select * from tblUserBranch where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."'";
		$resUserBranch = $this->execQry($qryUserBranch);
		return $this->getArrRes($resUserBranch);
	}
	
	function processPassword($compcode,$empno){
		$qryPassword="Update tblUsers set userPass='".base64_encode($this->get['txtNewPword'])."' where empNo='{$empno}' and compCode='{$compcode}'";	
		$res=$this->execQry($qryPassword);
		if($res){
			return true;	
		}
		else{
			return false;	
		}
	}
	
	function chkEmpUser($compCode,$empNo){
		$qryChkUsers = "SELECT tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, 
						tblUsers.userId, tblUsers.compCode, tblUsers.empNo, tblUsers.userPass, 
						tblUsers.pagesTNA, tblUsers.Pages201, tblUsers.pagesPayroll, tblUsers.userLevel, 
						tblUsers.dateEnt, tblUsers.dateUpdt, tblUsers.userStat, tblUsers.category 
						FROM tblEmpMast 
						LEFT OUTER JOIN tblUsers ON tblEmpMast.empNo = tblUsers.empNo 
						where tblUsers.compCode='".$compCode."' and tblUsers.empNo='".$empNo."'";
		$resqryChkUsers = $this->execQry($qryChkUsers);			
		return $resqryChkUsers;
	}
	
	function processTransfer($compcode,$empno,$tocompcode){
		$qryGetEmp=$this->getEmployee($compcode,$empno,"");
		$qryGetUser=$this->getUserLogInInfoForMenu($empno);
		if($tocompcode==1){
			$comp="PGJR_PAYROLL";	
		}
		elseif($tocompcode==2){
			$comp="PG_PAYROLL";	
		}
		elseif($tocompcode==4){
			$comp="DFCLARK_PAYROLL";	
		}
		elseif($tocompcode==5){
			$comp="DFSUBIC_PAYROLL";	
		}
		
		$qryChkID="Select max(ID) as n from ".$comp."..tblEmpID";
		$res=$this->getSqlAssoc($this->execQry($qryChkID));
		$idNum=$res['n']+1;
				
		$qryEmpMast="Insert into ".$comp."..tblEmpMast (id,compCode,empNo,empLastName,empFirstName,empMidName,empStat) values ('".$idNum."','".$tocompcode."','".$qryGetEmp['empNo']."','".$qryGetEmp['empLastName']."','".$qryGetEmp['empFirstName']."','".$qryGetEmp['empMidName']."','USER')";
		$resEmpmast=$this->execQry($qryEmpMast);
		if($resEmpmast){
			$qryUsers="Insert into ".$comp."..tblUsers (compCode,empNo,userPass,pagesTNA,Pages201,pagesPayroll,userLevel,dateEnt,dateUpdt,userStat,category) Select ".$tocompcode.",empNo,userPass,pagesTNA,Pages201,pagesPayroll,userLevel,dateEnt,dateUpdt,userStat,category from tblUsers where empNo='".$qryGetUser['empNo']."'";
			$resUsers=$this->execQry($qryUsers);
			if($resUsers){
				return true;	
			}
			else{
				$qryDelete="Delete from ".$comp."..tblEmpMast where id='".$idNum."' and empNo='".$qryGetEmp['empNo']."' and compCode='".$tocompcode."'";
				$resDel=$this->execQry($qryDelete);
				if($resDel){
					return false;	
				}	
			}
		}
		else{
			return false;	
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
						AND empNo ='{$this->get['txtEmpNo']}' ";
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