<?
class maintEmpObj extends commonObj {
	
	var $compCode;
	var $empNo;
	var $fName;
	var $mName;
	var $lName;
	var $loc;
	var $branch;
	var $div;
	var $dept;
	var $sect;
	var $position;
	var $dateHired;
	var $empStat;
	var $dateReg;
	var $resDay;
	var $TEU;
	var $TIN;
	var $SSS;
	var $pagIbig;
	var $bankCode;
	var $bankAcctNo;
	var $payGrp;
	var $payType;
	var $payCat;
	var $wageTag;
	var $prevEmpTag;
	var $addr1;
	var $addr2;
	var $addr3;
	var $maritalStat;
	var $sex;
	var $dateOfBirth;
	var $religion;
	var $monthlyrate;
	var $dailyrate;
	var $hourlyRate;
	var $userLevel;
	var $hdnCompCode;//hidden text field
	var $hdnEmpNo;//hidden text field
	
	function checkEmployee(){
		$qryCheckEmployee = "SELECT * FROM tblEmpMast 
							 WHERE compCode = '{$this->compCode}'
							 AND empNo = '".trim($this->empNo)."'
							 AND empStat IN('RG','PR','CN')";
		$resCheckEmployee = $this->execQry($qryCheckEmployee);
		
		return $this->getRecCount($resCheckEmployee);
	}
		
	function addEmployee(){
		
		$qryAddEmployee = "INSERT INTO tblEmpMast
							(compCode,empNo,empLastName,empFirstName,empMidName,
							 empLocCode,empBrnCode,empDiv,empDepCode,empSecCode,
							 empPosDesc,dateHired,empStat,dateReg,empRestDay,
							 empTeu,empTin,empSssNo,empPagibig,empBankCd,
							 empAcctNo,empPayGrp,empPayType,empPayCat,empWageTag,
							 empPrevTag,empAddr1,empAddr2,empAddr3,empMarStat,
							 empSex,empBday,empReligion,empMrate,empDrate,empHrate)
					  VALUES('{$this->compCode}','".trim($this->empNo)."','".str_replace("'","''",stripslashes($this->lName))."','".str_replace("'","''",stripslashes($this->fName))."','".str_replace("'","''",stripslashes($this->mName))."',
					  	     '{$this->loc}','{$this->branch}','{$this->div}','{$this->dept}','{$this->sect}',
					  	     '".str_replace("'","''",stripslashes($this->position))."','{$this->dateHired}','{$this->empStat}','{$this->dateReg}','{$this->resDay}',
					  	     '{$this->TEU}','{$this->TIN}','{$this->SSS}','{$this->pagIbig}','{$this->bankCode}',
					  	     '{$this->bankAcctNo}','{$this->payGrp}','{$this->payType}','{$this->payCat}','{$this->wageTag}',
					  	     '{$this->prevEmpTag}','".str_replace("'","''",stripslashes($this->addr1))."','".str_replace("'","''",stripslashes($this->addr2))."','".str_replace("'","''",stripslashes($this->addr3))."','{$this->maritalStat}',
					  	     '{$this->sex}','{$this->dateOfBirth}','".str_replace("'","''",stripslashes($this->religion))."','{$this->monthlyrate}','{$this->dailyrate}','{$this->hourlyRate}')";
		$resAddEmployee = $this->execQry($qryAddEmployee);
		if($resAddEmployee){
			if($this->addUserLogInInfo() == true){
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}	
	}
	
	function updateEmployee(){
		
		$qryUpdateEnployee = "UPDATE tblEmpMast SET 
												empLastName  = '".str_replace("'","''",stripslashes($this->lName))."',
												empFirstName = '".str_replace("'","''",stripslashes($this->fName))."',
												empMidName   = '".str_replace("'","''",stripslashes($this->mName))."',
												empLocCode   = '{$this->loc}',
												empBrnCode   = '{$this->branch}',
												empDiv       = '{$this->div}',
												empDepCode   = '{$this->dept}',
												empSecCode   = '{$this->sect}',
												empPosDesc   = '".str_replace("'","''",stripslashes($this->position))."',
												dateHired    = '{$this->dateHired}',
												empStat      = '{$this->empStat}',
												dateReg      = '{$this->dateReg}',
												empRestDay   = '{$this->resDay}',
												empTeu       = '{$this->TEU}',
												empTin       = '{$this->TIN}',
												empSssNo     = '{$this->SSS}',
												empPagibig   = '{$this->pagIbig}',
												empBankCd    = '{$this->bankCode}',
												empAcctNo    = '{$this->bankAcctNo}',
												empPayGrp    = '{$this->payGrp}',
												empPayType   = '{$this->payType}',
												empPayCat    = '{$this->payCat}',
												empWageTag   = '{$this->wageTag}',
												empPrevTag   = '{$this->prevEmpTag}',
												empAddr1     = '".str_replace("'","''",stripslashes($this->addr1))."',
												empAddr2     = '".str_replace("'","''",stripslashes($this->addr2))."',
												empAddr3     = '".str_replace("'","''",stripslashes($this->addr3))."',
												empMarStat   = '{$this->maritalStat}',
												empSex       = '{$this->sex}',
												empBday      = '{$this->dateOfBirth}',
												empReligion  = '".str_replace("'","''",stripslashes($this->religion))."',
												empMrate     = '{$this->monthlyrate}',
												empDrate     = '{$this->dailyrate}',
												empHrate     = '{$this->hourlyRate}'
												WHERE compCode = '{$this->hdnCompCode}'
												AND empNo      = '".trim($this->hdnEmpNo)."'";
		$resUpdateEnployee = $this->execQry($qryUpdateEnployee);
		
		$qryUpdtUserLogInInfo = "UPDATE tblUsers SET userLevel = '{$this->userLevel}',dateUpdt = '".date("m/d/Y")."'
							     WHERE compCode = '{$this->hdnCompCode}'
							     AND empNo = '".trim($this->empNo)."' ";
		$resUpdtUserLogInInfo = $this->execQry($qryUpdtUserLogInInfo);
		if($resUpdateEnployee && $resUpdtUserLogInInfo){
			return true;
		}
		else {
			return false;
		}	
	}
	
	private function genInitUserPass(){
		//do not edit 
		$initPass =	substr(strtolower(trim($this->fName)),0,1).substr(str_ireplace(' ','',trim(strtolower($this->lName))),0,2).rand(1,date('gis')).'x'.date('ny');
		$tmpInitPass = base64_encode($initPass);
		return $tmpInitPass;
	}
	
	function addUserLogInInfo(){
				
		$qryUserLogInInfo = "INSERT INTO tblUsers(compCode,empNo,
												  userPass,userLevel,
												  dateEnt,userStat)
										 VALUES('{$this->compCode}','".trim($this->empNo)."',
										        '{$this->genInitUserPass()}','{$this->userLevel}',
										        '".date("m/d/Y")."','A') ";
		
		$resUserLogInInfo = $this->execQry($qryUserLogInInfo);
		if($resUserLogInInfo){
			return true;
		}
		else {
			return false;
		}		
	}
}


class employeeAllowanceObj extends commonObj {
		
	var $compCode;
	var $method;

	function __construct($method){
		$this->method = $method;
	}
	
	function getSpecificEmpAllow($compCode,$empNo,$allowCode,$code){
		if ($code != 1) {
			$table = "tblPAF_Allowance";
		} else {
			$table = "tblAllowance";
		}
		$qryGetSpcfcAllw = "SELECT * FROM $table 
							WHERE compCode = '{$compCode}'
							AND empNo = '{$empNo}'
							AND allowCode = '{$allowCode}'";
	
		$resGetSpcfcAllw = $this->execQry($qryGetSpcfcAllw);
		return $this->getSqlAssoc($resGetSpcfcAllw);
	}
	
	function addEmpAllowance(){
		
		 $qryToEmpAllowance = "INSERT INTO tblPAF_Allowance(
								compCode,empNo,allowCode,allowAmtold,
								allowAmt,allowSked,allowTaxTag,
								allowPayTag,
								allowStart,
								allowEnd,
								allowStat,
								refNo,
								controlNo,
								effectivitydate,
								dateadded,
								userid,
								allowTag,
								sprtPS,
								stat
							  )VALUES(
							  	'{$this->compCode}','{$this->method['empNo']}','".($_GET['action']=='EDIT'?$_GET['allow_edit_code']:$this->method['AllowType'])."',
							  	'".(float)$this->method['txtAllwAmountold']."','{$this->method['txtAllwAmount']}','{$this->method['cmbAllwSked']}','{$this->method['cmbAllwTaxTag']}',
							  	'{$this->method['cmbAllwPayTag']}',
							  	'{$this->dateFormat($this->method['txtAllwStart'])}',
							  	'{$this->dateFormat($this->method['txtAllwEnd'])}',
							  	'{$this->method['cmbAllwStat']}',
							  	'{$this->method['refNo']}',
								'{$this->method['controlNo']}',
								'{$this->dateFormat($this->method['effetivitydate'])}',
								'".date('m/d/Y')."',								
								'{$_SESSION['user_id']}',
								'{$this->method['allowTag']}',
								'".$this->getAllowInfo($this->method['AllowType'])."',
								'H'
							  )";
		$resToEmpAllowance = $this->execQry($qryToEmpAllowance);
		if($resToEmpAllowance){
			return true;
		}
		else {
			return false;
		}	
	}
	function getAllowInfo($allowCode) {
		$sql = "Select * from tblAllowType where allowCode='$allowCode' and compCode='".$_SESSION['company_code']."'";
		$res = $this->getSqlAssoc($this->execQry($sql));
		return $res['sprtPS'];
	}	
	function getAllowInfoDetail($allowCode) {
		$sql = "Select * from tblAllowType where allowCode='$allowCode' and compCode='".$_SESSION['company_code']."'";
		$res = $this->getSqlAssoc($this->execQry($sql));
		return $res;
	}	
	function editEmpAllowance(){
		$qryEditEmpAllow = "UPDATE tblPAF_Allowance SET allowAmt    	= '{$this->method['txtAllwAmount']}',
													allowAmtold    		= '".(float)$this->method['txtAllwAmountold']."',
												    allowStart  		= '{$this->dateFormat($this->method['txtAllwStart'])}',
												    allowStat   		= '{$this->method['cmbAllwStat']}',
													refNo  				= '{$this->method['refNo']}',
													controlNo   		= '{$this->method['controlNo']}',
													effectivitydate   	= '{$this->dateFormat($this->method['effetivitydate'])}',
													userid   			= '{$_SESSION['user_id']}',
													allowTag			= '{$this->method['allowTag']}',
													sprtPS				= '".$this->getAllowInfo($this->method['hdnAllowCode'])."'
													
												WHERE compCode  = '{$this->compCode}' 
												AND empNo = '{$this->method['empNo']}'
												AND allowCode = '{$this->method['hdnAllowCode']}'";
		$resEditEmpAllow = $this->execQry($qryEditEmpAllow);
		if($resEditEmpAllow){
			return true;
		}
		else {
			return false;
		}	
	}
	
	function checkEmpAllowance(){
		
		$qryCheckEmpAllow = "SELECT empNo FROM tblPAF_Allowance 
							 WHERE compCode = '{$this->compCode}' 
							 AND empNo      = '{$this->method['empNo']}' 
							 AND allowCode  = '{$this->method['AllowType']}'";
		$resCheckEmpAllow = $this->execQry($qryCheckEmpAllow);
		return $this->getRecCount($resCheckEmpAllow);
	}
	
	function deleteEmpAllowance($compCode){

		$qryDeleEmpAllw = "DELETE FROM tblPAF_Allowance 
						   WHERE compCode = '{$compCode}'
						   AND empNo      = '{$this->method['empNo']}'
						   AND allowCode  = '{$this->method['allwCode']}'";
		$resDeleEmpAllw = $this->execQry($qryDeleEmpAllw);
		if($resDeleEmpAllw){
			return true;
		}
		else {
			return false;
		}
		
	}
	
	function ProcessPAF() {
		$today=date('m/d/Y');
		$qryAllow = " Select 
					compCode, empNo, allowCode, allowAmt,allowAmtold, allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd, allowStat,
					sprtPS, refNo, controlNo, effectivitydate,'$today','$today',userid,'{$_SESSION['user_id']}' from tblPAF_Allowance where compCode='{$_SESSION['company_code']}' and effectivitydate='$today'";
	    $resAllw = $this->getArrRes($this->execQry($qryAllow));
		foreach($resAllw as $val) {
			$qryInsertPAF .= "Insert into tblPAF_Allowancehist  (compCode, empNo, allowCode, allowAmt,allowAmtold, allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd, allowStat, sprtPS, refNo, controlNo, effectivitydate, dateadded, dateupdated, user_created, user_updated) values
					('{$val['compCode']}', '{$val['empNo']}', '{$val['allowCode']}', '{$val['allowAmt']}', '{$val['allowAmtold']}', '{$val['allowSked']}', '{$val['allowTaxTag']}', '{$val['allowPayTag']}', '{$val['allowStart']}', '{$val['allowEnd']}', '{$val['allowStat']}', 
					'{$val['sprtPS']}', '{$val['refNo']}', '{$val['controlNo']}', '{$val['effectivitydate']}', '$today', '$today', '{$val['userid']}', '{$_SESSION['user_id']}'); ";
			if ($this->checkEmpAllow($val['empNo'],$val['allowCode'])==0) {
				$qryInsertAllow .= "Insert into tblAllowance
									(compCode, empNo, allowCode, allowAmt, allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd, 
									allowStat, sprtPS) values
									('{$val['compCode']}', '{$val['empNo']}', '{$val['allowCode']}', '{$val['allowAmt']}', '{$val['allowSked']}', '{$val['allowTaxTag']}', '{$val['allowPayTag']}', '{$val['allowStart']}', '{$val['allowEnd']}', '{$val['allowStat']}','{$val['sprtPS']}');";
			} else {
				$qryInsertAllow .= "Update tblAllowance set allowAmt='{$val['allowAmt']}' where compCode = '{$this->compCode}' AND empNo = '{$val['empNo']}'  AND allowCode  = '{$val['allowCode']}'"; 
			}
		
		}
		
		$qryDelPAFAllow = "Delete from tblPAF_Allowance where compCode='{$_SESSION['company_code']}' and effectivitydate='$today'";
		if ($this->execQry($qryInsertPAF)==true && $this->execQry($qryInsertAllow)==true && $this->execQry($qryDelPAFAllow)==true) {
			return true;
		} else {
			return false;
		}
	}
	function checkEmpAllow($empNo,$allowCode){
		
		$qryCheckEmpAllow = "SELECT empNo FROM tblAllowance 
							 WHERE compCode = '{$this->compCode}' 
							 AND empNo      = '$empNo' 
							 AND allowCode  = '$allowCode'";
		$resCheckEmpAllow = $this->execQry($qryCheckEmpAllow);
		return $this->getRecCount($resCheckEmpAllow);
	}	
}


class maintPrevEmplyr extends commonObj {
	
	var $get;
	var $sessionVars;
	

	
	function __construct($method,$sessions){
		$this->get = $method;
		$this->sessionVars = $sessions;
	}	
	
	
	function checkPrevEmp(){
		$qryCheckPrevEmp = "SELECT prevEmplr FROM tblPrevEmployer
							 WHERE compCode = '{$this->sessionVars['compCode']}'
							 AND empNo = '{$this->get['empNo']}'
							 AND prevEmplr = '".str_replace("'","''",stripslashes(trim($this->get['emplyrName'])))."'";
		$resCheckPrevEmp = $this->execQry($qryCheckPrevEmp);
		if(!$resCheckPrevEmp){
			return -1;
		}
		else{
			return $this->getRecCount($resCheckPrevEmp);
		}
	}
	
	function editPrevEmp(){
		$qryEditPrevEmp = "UPDATE tblPrevEmployer 
						  SET prevEmplr = '".str_replace("'","''",stripslashes($this->get['emplyrName']))."',
						  empAddr1 = '".str_replace("'","''",stripslashes($this->get['emplyrAdd1']))."',
						  empAddr2 = '".str_replace("'","''",stripslashes($this->get['emplyrAdd2']))."',
						  empAddr3 = '".str_replace("'","''",stripslashes($this->get['emplyrAdd3']))."',
						  emplrTin = '{$this->get['emplyrTinNo']}',
						  prevEarnings = '{$this->get['emplyrPrevEarn']}',
						  prevTaxes = '{$this->get['emplyrPrevTax']}',
						  prevStat = '{$this->get['cmbStat']}' 
						  WHERE compCode = '{$this->sessionVars['compCode']}'
						  AND empNo = '{$this->get['empNo']}'
						  AND seqNo = '{$this->get['hdnSeqNo']}'";
		$resEditPrevEmp = $this->execQry($qryEditPrevEmp);
		if(!$resEditPrevEmp){
			return false;
		}
		else{
			return true;
		}
	}
	
	function addPrevEmp(){
		$qryAddPrevEmp = "INSERT INTO tblPrevEmployer
						  (compCode,empNo,prevEmplr,empAddr1,empAddr2,empAddr3,emplrTin,prevEarnings,prevTaxes,prevStat)
						  VALUES('{$this->sessionVars['compCode']}',
						  		 '{$this->get['empNo']}',
						  		 '".str_replace("'","''",stripslashes($this->get['emplyrName']))."',
						  		 '".str_replace("'","''",stripslashes($this->get['emplyrAdd1']))."',
						  		 '".str_replace("'","''",stripslashes($this->get['emplyrAdd2']))."',
						  		 '".str_replace("'","''",stripslashes($this->get['emplyrAdd3']))."',
						  		 '{$this->get['emplyrTinNo']}',
						  		 '{$this->get['emplyrPrevEarn']}',
						  		 '{$this->get['emplyrPrevTax']}',
						  		 '{$this->get['cmbStat']}' )";
		$resAddPrevEmp = $this->execQry($qryAddPrevEmp);
		if(!$resAddPrevEmp){
			return false;
		}
		else{
			return true;
		}		
	}
	
	function delePrevEmp(){
		$qryDelePrevEmp = "DELETE FROM tblPrevEmployer
							WHERE compCode = '{$this->sessionVars['compCode']}'
							AND empNo = '{$this->get['empNo']}'
							AND seqNo = '{$this->get['seqNo']}'";
		
		$resDelePrevEmp = $this->execQry($qryDelePrevEmp);
		if(!$resDelePrevEmp){
			return false;
		}
		else{
			return true;
		}
	}
}

class changePassObj extends commonObj {
	
	var $get;
	var $sessionVars;
	

	function __construct($method,$sessions){
		$this->get = $method;
		$this->sessionVars = $sessions;
	}		
	
	function checkOldPass(){
		$qryCheckOldPass = "SELECT userPass FROM tblUsers
							WHERE compCode = '{$this->sessionVars['company_code']}'
							AND empNo = '{$this->sessionVars['employee_number']}'
							AND userPass = '".base64_encode($this->get['txtOldPass'])."'";
		$resCheckOldPass = $this->execQry($qryCheckOldPass);
		if($this->getRecCount($resCheckOldPass) > 0){
			return 1;
		}
		else{
			return 0;
		}
	}
	
	function changePass(){
		$qryChangePass = "UPDATE tblUsers 
						  SET userPass = '".base64_encode($this->get['txtNewPass'])."'
						  WHERE compCode = '{$this->sessionVars['company_code']}'
						  AND empNo = '{$this->sessionVars['employee_number']}'";
		$resChangePass = $this->execQry($qryChangePass);
		if($resChangePass){
			return true;
		}
		else{
			return false;
		}
	}
}
?>
