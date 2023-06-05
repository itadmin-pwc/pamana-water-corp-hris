<?
include("includes/db.inc.php");

class indexObj extends dbHandler {
	
	var $module;
	
	var $compCode;
	
	var $empNo;
	
	var $userPass;
			
	function validateLogIn(){
		
		$qryLogIn = "SELECT * FROM tblUsers 
					WHERE compCode = '{$this->compCode}'
					AND   empNo    = '{$this->empNo}'
					AND   userPass = '".base64_encode($this->userPass)."'
					AND   userStat = 'A'";
		$resLogIn = $this->execQry($qryLogIn);
		
		if($this->getRecCount($resLogIn) > 0){
			return $this->getSqlAssoc($resLogIn);	 		
		}
		else{
			return 0;
		}
	}
		
	function accessModule(){
		
		if($this->module == 1){
			echo "window.parent.location.href='time_and_attendance';";
		}
		if($this->module == 2){
			echo "window.parent.location.href='201';";
		}
		if($this->module == 3){
			echo "window.parent.location.href='payroll';";
		}		
	}
	
}
?>