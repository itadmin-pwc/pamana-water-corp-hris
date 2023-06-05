<?
class commonObj extends dbHandler {
	function getBranchPayGroup($where){
		if($where!=""){
			$wheres=$where;
			}
		else{
			$wheres="";
			}	
		$qrygetBranch = "SELECT * FROM tblBranch 
						$wheres order by brnDesc";
		return $this->execQry($qrygetBranch);
	}
	
	function getUserHeaderInfo($empNo,$empId){
		$qryGetUserInfo = "SELECT * FROM tblEmpMast 
						   WHERE empNo    = '".trim($empNo)."'
						   AND id = '{$empId}'
						   AND   empStat NOT IN('RS','IN','TR') ";
		$resGetUserInfo = $this->execQry($qryGetUserInfo);
		return $this->getSqlAssoc($resGetUserInfo);		
	}
	
	function getUserLogInInfoForMenu($empNo){
		
		$qryUserLogInInfo = "SELECT * FROM tblUsers 
					WHERE  empNo    = '{$empNo}'
					AND   userStat = 'A'";
				
		$resUserLogInInfo = $this->execQry($qryUserLogInInfo);
		return $this->getSqlAssoc($resUserLogInInfo);
	}
	
	function getUserInfo($compCode,$empNo,$where){

		$qryGetUserInfo = "SELECT * FROM tblEmpMast 
						   WHERE compCode = '{$compCode}'
						   AND   empNo    = '".trim($empNo)."'
						   AND   empStat NOT IN('RS','IN','TR') ";
						   
		if($where != ""){
			$qryGetUserInfo .= $where;
		}
		$resGetUserInfo = $this->execQry($qryGetUserInfo);
		return $this->getSqlAssoc($resGetUserInfo);
		
	}
	
	function getEmployeeList($compCode,$where){
		$qryGetEmplist = "SELECT * FROM tblEmpMast 
						   WHERE compCode = '{$compCode}' ";
		if($where != ""){
			$qryGetEmplist .= $where;
		}
		
		$resGetEmplist = $this->execQry($qryGetEmplist);
	
		if($resGetEmplist){
			if($this->getRecCount($resGetEmplist) == 1){
				return $this->getSqlAssoc($resGetEmplist);
			}
			else{
				return $this->getArrRes($resGetEmplist);
			}
		}
		else{
			return 0;
		}		
	}
	
	function getEmployee($compCode,$empNo,$where){
		
		$qryGetUserInfo = "SELECT * FROM tblEmpMast 
						   WHERE compCode = '{$compCode}'
						   AND   empNo    = '{$empNo}' ";
		if($where != ""){
			$qryGetUserInfo .= $where;
		}
		$resGetUserInfo = $this->execQry($qryGetUserInfo);
		if($this->getRecCount($resGetUserInfo) > 0){
			return $this->getSqlAssoc($resGetUserInfo);
		}
		else{
			return 0;
		}
	}	
	
	function getUserLogInInfo($compCode,$empNo){
		
		$qryUserLogInInfo = "SELECT * FROM tblUsers 
					WHERE compCode = '{$compCode}'
					AND   empNo    = '{$empNo}'
					AND   userStat = 'A'";
		$resUserLogInInfo = $this->execQry($qryUserLogInInfo);
		return $this->getSqlAssoc($resUserLogInInfo);
	}
	
	function getCompany($compCode){
		
		$qry = "SELECT * FROM tblCompany WHERE compStat = 'A' ";
		if($compCode != ""){
			$qry .= "AND compCode = '{$compCode}'";
		}
		$res = $this->execQry($qry);
		if($compCode != ""){
			return $this->getSqlAssoc($res);
		}
		else{
			return $this->getArrRes($res);
		}
	}
	
	function getCompanyName($compCode){
		
		$qry = "SELECT compName FROM tblCompany WHERE compStat = 'A' AND compCode = '{$compCode}' ";
		$res = $this->execQry($qry);
		$row = $this->getSqlAssoc($res);
		return  $row['compName'];
	}	
	
	function getPayCat($compCode,$where){
		
		$qryGetPayCat = "SELECT * FROM tblPayCat 
						 WHERE compCode = '{$compCode}'
						 AND payCatStat = 'A' ";
		if(!empty($where) || $where != ''){
			$qryGetPayCat .= $where;
		}		
		$resGetPayCat = @$this->execQry($qryGetPayCat);

		if($this->getRecCount($resGetPayCat) == 1){
			return $this->getSqlAssoc($resGetPayCat);
		}
		else{
			return $this->getArrRes($resGetPayCat);
		}
	}

	function getPayPeriod($compCode,$where){
		
		$qryGetPayPeriod = "SELECT * FROM tblPayPeriod
							WHERE compCode = '{$compCode}' ";
		if(!empty($where)){
			$qryGetPayPeriod .= $where;
		}
		$resGetPayPeriod = $this->execQry($qryGetPayPeriod);
		if($this->getRecCount($resGetPayPeriod) == 1){
			return $this->getSqlAssoc($resGetPayPeriod);
		}
		else{
			return $this->getArrRes($resGetPayPeriod);
		}
	}
	
	function getSeesionVars(){
		
		$sessionVars = array(
			'moduleId' => $_SESSION['module_id'],
			'compCode' => $_SESSION['company_code'],
			'empNo'    => $_SESSION['employee_number'],
			'userLvl'  => $_SESSION['user_level'],
			'payGrp'   => $_SESSION['pay_group'],
			'payCat'   => $_SESSION['pay_category']
		);
		return $sessionVars;
	}	
	
	function validateSessions($isLogOut,$form){
		
		$sessionVars = $this->getSeesionVars();
		
		if($form == 'LOGIN'){
			if($isLogOut == 1){
				echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/".SYS_NAME."'</script>";
				session_destroy();		
			}
			if(isset($sessionVars['moduleId']) && isset($sessionVars['empNo'])){
				if($sessionVars['moduleId'] == 1){

					echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/".SYS_NAME."/".SYS_NAME_TNA."'<//script>";
				}
				if($sessionVars['moduleId'] == 2){
					echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/".SYS_NAME."/".SYS_NAME_201."'<//script>";
				}
				if($sessionVars['moduleId'] == 3){
					echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/".SYS_NAME."/".SYS_NAME_PAYROLL."'</script>";
				}
			}
		}
		
		if($form == 'MODULES'){
			if(!isset($_SESSION['module_id']) && !isset($_SESSION['employee_number'])){

				echo "<script>parent.location.href='http://$_SERVER[HTTP_HOST]/".SYS_NAME."'</script>";
				//header("location: http://$_SERVER[HTTP_HOST]/".SYS_NAME);
				session_destroy();				
			}
		}
	}
		
	function getModuleName($moduleId){
		
		if($moduleId == 1){
			$moduleName = 'TIME AND ATTENDANCE';
		}
		if($moduleId == 2){
			$moduleName = '201';
		}
		if($moduleId == 3) {
			$moduleName = 'PAYROLL';
		}
		return $moduleName;	
	}
	
	function getArrEmpStatus(){
		
		$arrEmpStat = array(
			'',
			'RG'=>'Regular',
			'PR'=>'Probationary',
			'CN'=>'Contractual',
			'RS'=>'Resigned',
			'IN'=>'Inactive',
			'TR'=>'Terminated'
		);
		 return $arrEmpStat;
	}
	
	function getArrRestDay(){
		
		$arrRestDay = array(
			'',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
			'Sunday'
		);
		return $arrRestDay;
	}
	
	function getTEU(){		
		$qryGetTEU = "SELECT * FROM tblTeu";
		$resGetTEU = $this->execQry($qryGetTEU);
		return $this->getArrRes($resGetTEU);
	}
	
	function getPayBank($compCode){

		$qryPayBank = "SELECT * FROM tblPayBank 
						WHERE compCode = '{$compCode}'
						AND bankStat = 'A'";
		$resPayBank = $this->execQry($qryPayBank);
		 return $this->getArrRes($resPayBank);
	}
		
	function getBranch($compCode){
		$qrygetBranch = "SELECT * FROM tblBranch 
						WHERE compCode = '1' order by brnDesc";
		$resgetBranch = $this->execQry($qrygetBranch);
		return $this->getArrRes($resgetBranch);
	}

	function getFilterBranch($compCode){
		$qrygetBranch = "SELECT * FROM tblBranch 
						WHERE compCode = '{$compCode}' and brnCode in (Select brnCode from tblUserBranch where empNo='{$_SESSION['employee_number']}') order by brnDesc";
		$resgetBranch = $this->execQry($qrygetBranch);
		return $this->getArrRes($resgetBranch);
	}


	function makeArr($arrRes,$index,$value,$default){

		$arrResult = array();
		if(!empty($default)){
			$arrResult[0] = $default;
		}
		else{
			$arrResult[0] = "";
		}
		
		foreach ($arrRes as $arrVal){
			$arrResult[$arrVal[$index]] = $arrVal[$value];
		}
		return $arrResult;
	}
	
	function makeArr2($arrRes,$index,$index2,$value){

		$arrResult = array();
		$arrResult[0] = "";
		foreach ($arrRes as $arrVal){
			$arrResult[$arrVal[$index]."-".$arrVal[$index2]] = $arrVal[$value];
		}
		return $arrResult;
	}
	
	function getDepartment($compCode,$divCode="",$deptCode="",$sectCode="",$deptLevel){

		$qryGetDepartment = "SELECT * FROM tblDepartment 
							 WHERE compCode = '{$compCode}' ";
		if($deptLevel == 1){
			$qryGetDepartment .= "AND deptLevel = '{$deptLevel}' ORDER BY divCode ";
			$qryGetDepartment = $this->execQry($qryGetDepartment);
			return $this->getArrRes($qryGetDepartment);
		}
		if($deptLevel == 2){
			if($divCode != ""){
				$qryGetDepartment .= "AND divCode = '{$divCode}' ";
			}
			$qryGetDepartment .= "AND deptLevel = '{$deptLevel}' ORDER BY deptCode ";
			$resGetDepartment = $this->execQry($qryGetDepartment);
			return $this->getArrRes($resGetDepartment);
		}
		if($deptLevel == 3){
			if($divCode != "" && $deptCode != ""){
				$qryGetDepartment .= "AND divCode = '{$divCode}' AND deptCode = '{$deptCode}' ";
			}
			$qryGetDepartment .= "AND deptLevel = '{$deptLevel}' ORDER BY sectCode ";
			$resGetDepartment = $this->execQry($qryGetDepartment);
			return $this->getArrRes($resGetDepartment);
		}
	}
	
	function getAllowType($compCode){
		$qryGetAllowType = "SELECT * FROM tblAllowType 
							WHERE compCode = '{$compCode}'
							AND allowTypeStat = 'A'";
		$resGetAllowType = $this->execQry($qryGetAllowType);
		return $this->getArrRes($resGetAllowType);
	}
	
	function dateFormat($date){
		return date('Y-m-d',strtotime($date)) ;
	}
	
	function getTransType($compCode,$type,$where){
		
		$qryGetTransType = "SELECT * FROM tblPayTransType 
							WHERE compCode = '{$compCode}' ";
		
		if($type == 'deductions'){
			
			$qryGetTransType .= "AND trnCat = 'D' 
								 AND trnEntry = 'Y' ";
			if(!empty($where)){
				$qryGetTransType .= $where;
			}
		}
		if($type == 'earnings'){
				$qryGetTransType .= "AND trnCat = 'E' 
								 AND trnEntry = 'Y' ";	
			if(!empty($where)){
				$qryGetTransType .= $where;
			}	
		}
		$qryGetTransType .= " AND trnstat = 'A'
						     ORDER BY trnDesc ";
		
		$resGetTransType = $this->execQry($qryGetTransType);
		if($this->getRecCount($resGetTransType) > 0){
			return $this->getArrRes($resGetTransType);
		}
		else{
			return $this->getSqlAssoc($resGetTransType);
		}
	}
	
	function getPrevEmployer($compCode,$where){
		
		$qryGetPrevEmployer = "SELECT * FROM tblPrevEmployer
								WHERE compCode = '{$compCode}' ";
		if(!empty($where)){
			$qryGetPrevEmployer .= $where . " AND prevStat = 'A'" ;
		}
		$resGetPrevEmployer = $this->execQry($qryGetPrevEmployer);
		if($this->getRecCount($resGetPrevEmployer) > 0){
			return $this->getArrRes($resGetPrevEmployer);
		}
	}
	
    function dateDiff($date1,$date2,$format,$return){

  	   $dateDiff    = strtotime(date($format,strtotime($date1))) - strtotime(date($format,strtotime($date2)));
	   $fullDays    = floor($dateDiff/(60*60*24));
	   $fullHours   = floor(($dateDiff-($fullDays*60*60*24))/(60*60));
	   $fullMinutes = floor(($dateDiff-($fullDays*60*60*24)-($fullHours*60*60))/60); 
	  
	   if('D'){
	  	 return  $fullDays;
	   }elseif('H'){
	   	 return  $fullHours;
	   }elseif('M'){
	     return	$fullMinutes;
	   }else{
	   	 return $fullDays." ".$fullHours." ".$fullMinutes;
	   }	  	
    }
    
    function getArrPdNumPartner(){
    
	$arrPdNumPartner = array('1'=>'2',
							'2'=>'1',
							'3'=>'4',
							'4'=>'3',
							'5'=>'6',
							'6'=>'5',
							'7'=>'8',
							'8'=>'7',
							'9'=>'10',
							'10'=>'9',
							'11'=>'12',
							'12'=>'11',
							'13'=>'14',
							'14'=>'13',
							'15'=>'16',
							'16'=>'15',
							'17'=>'18',
							'18'=>'17',
							'19'=>'20',
							'20'=>'19',
							'21'=>'22',
							'22'=>'21',
							'23'=>'24',
							'24'=>'23',
		);    
		return $arrPdNumPartner;
    }

######################################################
	################# ARTHUR FUNCTION ####################
	function getDivDescArt($compCode, $empDiv){
		$qryGetDiv = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND divCode = '{$empDiv}' 
						 AND deptLevel = 1 
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		$resGetDiv = $this->execQry($qryGetDiv);
		return $this->getSqlAssoc($resGetDiv);
	}
	function getDeptDescArt($compCode,$empDiv,$empDept){
		$qryGetDept = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 2 
						 AND divCode = '{$empDiv}'
						  AND deptCode = '{$empDept}'
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		$resGetDept = $this->execQry($qryGetDept);
		return $this->getSqlAssoc($resGetDept);
	}
	function getSectDescArt($compCode,$empDiv,$empDept,$empSect){
		$qryGetSect = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 3 
						 AND divCode = '{$empDiv}' 
						 AND deptCode = '{$empDept}' 
						 AND sectCode = '{$empSect}' 
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		$resGetSect = $this->execQry($qryGetSect);
		return $this->getSqlAssoc($resGetSect);
	}
	function getCatArt($compCode){
		$qryGetCat = "SELECT * FROM tblPayCat
					     WHERE compCode = '{$compCode}'
						 AND payCatStat = 'A'
					     ORDER BY payCatDesc ASC";
		$resGetCat = $this->execQry($qryGetCat);
		return $this->getArrRes($resGetCat);
	}
	function getCatListArt($compCode){
		
		$qryGetPayCat = "SELECT * FROM tblPayCat 
						 WHERE compCode = '{$compCode}'
						 AND payCatStat = 'A'";
		$resGetPayCat = $this->execQry($qryGetPayCat);
		return $this->getArrRes($resGetPayCat);
	}
	function getCatDataArt($compCode){
		$qryGetCat = "SELECT * FROM tblPayCat
					     WHERE compCode = '{$compCode}'
						 AND payCatStat = 'A'
					     ORDER BY payCat DESC";
		$resGetCat = $this->execQry($qryGetCat);
		return $this->getSqlAssoc($resGetCat);
	}
	function getDivArt($compCode){
		$qryGetDiv = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 1 
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		$resGetDiv = $this->execQry($qryGetDiv);
		return $this->getArrRes($resGetDiv);
	}
	function getDeptArt($compCode,$empDiv){
		$qryGetDept = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 2 
						 AND divCode = '{$empDiv}'
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		$resGetDept = $this->execQry($qryGetDept);
		return $this->getArrRes($resGetDept);
	}
	function getSectArt($compCode,$empDiv,$empDept){
		$qryGetSect = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 3 
						 AND divCode = '{$empDiv}' 
						 AND deptCode = '{$empDept}' 
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		$resGetSect = $this->execQry($qryGetSect);
		return $this->getArrRes($resGetSect);
	}
	function getAllowTypeListArt($compCode){
		$qry = "SELECT * FROM tblAllowType
					     WHERE compCode = '{$compCode}'";
		$qry .=" AND allowTypeStat = 'A'
					     ORDER BY allowDesc ASC";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getLoanTypeListArt($compCode,$loanCode){
		$qryGetLoanType = "SELECT * FROM tblLoanType
					     WHERE compCode = '{$compCode}'";
						  if ($loanCode>"") {
		$qryGetLoanType .=" AND lonTypeCd LIKE '$loanCode%' ";
						  }
		$qryGetLoanType .=" AND lonTypeStat = 'A'
					     ORDER BY lonTypeDesc ASC";
		$resGetLoanType = $this->execQry($qryGetLoanType);
		return $this->getArrRes($resGetLoanType);
	}
	function getLoanTypeDataArt($compCode,$loanCode){
		$qryGetLoanType = "SELECT * FROM tblLoanType
					     WHERE compCode = '{$compCode}' 
						 AND lonTypeCd LIKE '$loanCode%' 
						 AND lonTypeStat = 'A'
					     ORDER BY lonTypeDesc ASC";
		$resGetLoanType = $this->execQry($qryGetLoanType);
		return $this->getSqlAssoc($resGetLoanType);
	}
	function getEmpLoanListArt($compCode,$empNo,$loanCode){
		$qryGetEmpLoan = "SELECT tblEmpLoans.lonTypeCd, tblLoanType.lonTypeDesc, tblEmpLoans.lonRefNo, tblEmpLoans.lonAmt, 
						  tblEmpLoans.lonWidInterst, tblEmpLoans.lonStart, tblEmpLoans.lonEnd, tblEmpLoans.lonSked,
						  tblEmpLoans.lonNoPaymnts, tblEmpLoans.lonDedAmt1, tblEmpLoans.lonDedAmt2, tblEmpLoans.lonPayments, 
						  tblEmpLoans.lonPaymentNo, tblEmpLoans.lonCurbal, tblEmpLoans.lonLastPay, 
						  CONVERT(VARCHAR,tblLoanType.lonTypeDesc) + ' (Ref.No:' + CONVERT(VARCHAR,tblEmpLoans.lonRefNo) + ')' AS loanDescRefNo,
						  CONVERT(VARCHAR,tblEmpLoans.lonTypeCd) + '-' + CONVERT(VARCHAR,tblEmpLoans.lonRefNo) AS loanTypeCdRefNo 
						  FROM tblEmpLoans INNER JOIN tblLoanType ON tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd
					      WHERE tblEmpLoans.compCode = '{$compCode}'
						  AND tblLoanType.compCode = '{$compCode}'
						  AND tblEmpLoans.empNo = '{$empNo}'
						  AND tblLoanType.lonTypeCd LIKE '$loanCode%'
						  AND tblEmpLoans.lonTypeCd LIKE '$loanCode%'
						  AND tblEmpLoans.lonStat = 'O'
						  AND tblLoanType.lonTypeStat = 'A'";
		$resGetEmpLoan = $this->execQry($qryGetEmpLoan);
		return $this->getArrRes($resGetEmpLoan);
	}
	function getEmpAllowListArt($compCode,$empNo) {
		$qry ="SELECT tblAllowType.allowDesc, tblAllowance.allowStart, tblAllowance.allowEnd, 
	               tblAllowance.allowSked, tblAllowance.allowAmt, tblAllowance.allowTaxTag, tblAllowance.allowPayTag
			   FROM tblAllowance INNER JOIN
            	   tblAllowType ON tblAllowance.allowCode = tblAllowType.allowCode
			   WHERE (tblAllowance.compCode = '{$compCode}') AND (tblAllowType.compCode = '{$compCode}') AND (tblAllowance.allowStat = 'A') AND 
               	   (tblAllowType.allowTypeStat = 'A') AND (tblAllowance.empNo = '{$empNo}')
			   ORDER BY tblAllowType.allowDesc";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getEmpLoanDataArt($compCode,$empNo,$lonTypeCd, $lonRefNo){
		$qryGetEmpLoan = "SELECT tblEmpLoans.lonSeries,tblEmpLoans.lonTypeCd, tblLoanType.lonTypeDesc, tblEmpLoans.lonRefNo, tblEmpLoans.lonAmt, 
						  tblEmpLoans.lonWidInterst, tblEmpLoans.lonStart, tblEmpLoans.lonEnd, tblEmpLoans.lonSked,
						  tblEmpLoans.lonNoPaymnts, tblEmpLoans.lonDedAmt1, tblEmpLoans.lonDedAmt2, tblEmpLoans.lonPayments, 
						  tblEmpLoans.lonPaymentNo, tblEmpLoans.lonCurbal, tblEmpLoans.lonLastPay,
						  lonStat = 
								CASE lonStat
								  WHEN 'O' THEN 'Open'
								  WHEN 'C' THEN 'Completed'
								  WHEN 'D' THEN 'Deleted'
								END 
						  FROM tblEmpLoans INNER JOIN tblLoanType ON tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd
					      WHERE tblEmpLoans.compCode = '{$compCode}'
						  AND tblLoanType.compCode = '{$compCode}'
						  AND tblEmpLoans.empNo = '{$empNo}'
						  AND tblLoanType.lonTypeCd = '{$lonTypeCd}'
						  AND tblEmpLoans.lonTypeCd = '{$lonTypeCd}'
						  AND tblEmpLoans.lonRefNo = '{$lonRefNo}'
						  AND tblEmpLoans.lonStat = 'O'
						  AND tblLoanType.lonTypeStat = 'A'";
		$resGetEmpLoan = $this->execQry($qryGetEmpLoan);
		return $this->getSqlAssoc($resGetEmpLoan);
	}
	function getEmpCatArt($compCode,$payCat){
		$qry = "SELECT * FROM tblPayCat
					     WHERE compCode = '{$compCode}' 
						 AND payCat = '$payCat' 
						 AND payCatStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEmpBranchArt($compCode,$branch){
		$qry = "SELECT * FROM tblBranch
					     WHERE compCode = '{$compCode}' 
						 AND brnCode = '$branch' 
						 AND brnStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEmpTeuArt($taxCode){
		$qry = "SELECT * FROM tblTeu
					     WHERE teuCode = '{$taxCode}'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEmpBankArt($compCode,$bank){
		$qry = "SELECT * FROM tblPayBank
					     WHERE compCode = '{$compCode}' 
						 AND bankCd = '$bank' 
						 AND bankStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function valDateArt($date) {
		if ($date=="") {
			$newDate = "";
		} else {
			$newDate = date("Y-m-d",strtotime($date));
		}
		return $newDate;
	}
	function currentDateArt() {
		$gmt = time() + (8 * 60 * 60);
		$newdate = date("Y-m-d h:iA", $gmt);
		return $newdate;
	}
	function currentDateNoTimeArt() {
		$gmt = time() + (8 * 60 * 60);
		$newdate = date("Y-m-d", $gmt);
		return $newdate;
	}
	function getCompanyArt($compCode) {
		$qry = "SELECT * FROM tblCompany
					     WHERE compCode = '{$compCode}'
						 AND compStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getDayTypeDescArt($dayType) {
		$qry = "SELECT * FROM tblDayType
					     WHERE dayType = '{$dayType}'
						 AND dayStat = 'A'";
		$res = $this->execQry($qry);
		$row = $this->getSqlAssoc($res);
		return  $row['dayTypeDesc'];
	}
	function getOtPremArt($dayType) {
		$qry = "SELECT * FROM tblOtPrem
					     WHERE dayType = '{$dayType}' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getTransTypeDescArt($compCode,$trnCode) {
		$qry = "SELECT * FROM tblPayTransType
				WHERE compCode = '$compCode' AND CONVERT(int,trnCode) = '$trnCode' AND trnStat = 'A' ";
		$res = $this->execQry($qry);
		$row = $this->getSqlAssoc($res);
		return  $row['trnDesc'];
	}
	function getTransTypeArt($compCode,$trnCode) {
		$qry = "SELECT * FROM tblPayTransType
				WHERE compCode = '$compCode' AND trnCode = '$trnCode' AND trnStat = 'A' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getcount($compCode,$empNo){
			$qryPrev = 	"SELECT * FROM tblPrevEmployer 
					 WHERE compCode = '{$compCode}'
					 AND prevStat = 'A' 
					 AND empNo = '{$empNo}' ";
					
			$resPrev = $this->execQry($qryPrev);		   
			return $this->getRecCount($resPrev);
	}    
	
	function getLoanTypeListWil($compCode,$loanCode,$empNo){
		$qryGetLoanType = "SELECT * FROM tblLoanType
					     WHERE compCode = '{$compCode}'";
		if ($loanCode>"") {
			$qryGetLoanType .=" AND lonTypeCd LIKE '$loanCode%' ";
		  	if ($loanCode!=2) {
				$qryGetLoanType .=" AND trnCode NOT IN
                          			(SELECT tblLoanType.trnCode FROM tblEmpLoans INNER JOIN
								   	tblLoanType ON tblEmpLoans.compCode = tblLoanType.compCode AND 
									tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd
									WHERE tblEmpLoans.empNo = '$empNo' 
									AND lonCurbal > 0 
									AND lonStat = 'O'
									AND tblEmpLoans.compCode = '{$compCode}'
									GROUP BY tblLoanType.trnCode)";
			}
			else {
				$qryGetLoanType .=" AND lonTypeCd NOT IN (Select lonTypeCd from tblEmpLoans 
									where compCode = '{$compCode}'
									AND lonPaymentNo<6
									AND empNo = '$empNo')";
			}
		  }
		$qryGetLoanType .=" AND lonTypeStat = 'A'
					     	ORDER BY lonTypeDesc ASC";
		$resGetLoanType = $this->execQry($qryGetLoanType);
		return $this->getArrRes($resGetLoanType);
	}
	
	function checkEmpLoans($lonTypeCd,$empNo,$compCode) {
		$qryCheckempLoan="Select * from tblEmpLoans 
							WHERE tblEmpLoans.empNo = '$empNo' 
							AND lonTypeCd='$lonTypeCd'
							AND lonCurbal > 0 
							AND lonStat = 'O'
							AND tblEmpLoans.compCode = '{$compCode}'";
		return	$this->getArrRes($this->execQry($qryCheckempLoan));		
	}
  
/*wil's function*/
	
	function getbranchwil($compCode=""){
		if ($compCode!="") {
			$filter=" where compCode='$compCode'";
		}
		$qrygetBranch = "SELECT * FROM tblBranch $filter order by brnDesc";
		$resgetBranch = $this->execQry($qrygetBranch);
		return $this->getArrRes($resgetBranch);
	}

	function getreligionwil(){
		$qryreligions="Select * from tblReligionRef order by relDesc";
		$res= $this->execQry($qryreligions);
		return $this->getArrRes($res);
	}	

	function getcitizenshipwil(){
		$citizenship="Select * from tblCitizenshipRef order by citizenDesc";
		$res= $this->execQry($citizenship);
		return $this->getArrRes($res);
	}	

	function getbankwil(){
		$bank="Select * from tblPayBank order by bankDesc";
		$res= $this->execQry($bank);
		return $this->getArrRes($res);
	}	

	function getshiftwil($compCode=""){
		$shift="Select * from tblTimeShiftRef where compCode='$compCode' and active='1' order by shiftDesc";
		$res= $this->execQry($shift);
		return $this->getArrRes($res);
	}	

	function getdepartmenttwil($compcode){
		$department="Select * from tblDepartment $compcode order by deptDesc";
		$res= $this->execQry($department);
		return $this->getArrRes($res);
	}

	function getcontacttypeswil(){
		$contacttype="Select * from tblContactTypeRef order by contactDesc";
		$res= $this->execQry($contacttype);
		return $this->getArrRes($res);
	}

	function getcitywil($where=""){
		$city="Select * from tblCityRef $where order by cityDesc";
		$res= $this->execQry($city);
		if ($this->getRecCount($res) > 1)
			return $this->getArrRes($res);
		else	
			return $this->getSqlAssoc($res);
	}
		
	function createstrwil($length = "11") {
		  $code = md5(uniqid(rand(), true));
			  if ($length != "") return substr($code, 0, $length);
			  else return $code;
	}
	function getDesc($table,$field,$fieldvalue,$desc) {
		$qryDesc="Select $desc from $table where $field='$fieldvalue'";
		$res=$this->execQry($qryDesc);
		$res=$this->getSqlAssoc($res);
		return $res;
	}
	function getlevelwil($rank,$and="") {
		$qrylevel="Select distinct level,('Level ' + convert(varchar, level)) as levelname from tblPosition where rank='$rank' and active='1' $and";
		$res=$this->execQry($qrylevel);
		$res=$this->getArrRes($res);
		return $res;		
	}
	
	function getpositionwil($and,$act) {
		$sqlpos="Select * from tblPosition where Active='A' $and order by posDesc";
		$res=$this->execQry($sqlpos);
		if ($act==1) {
			$res=$this->getArrRes($res);
		}	
		elseif ($act==2) {	
			$res=$this->getSqlAssoc($res);
		}
		return $res;		
	}	    

	function getpositionmer($and,$act) {
		$sqlpos="SELECT tblPosition.posCode,tblPosition.posDesc AS p,tblPosition.sectCode,tblPosition.divCode,
					Concat(tblPosition.posDesc,' (',tblDepartment.deptDesc,')') AS pp1,
					tblPosition.deptCode,tblDepartment.deptDesc 
				FROM tblPosition 
				INNER JOIN tblDepartment ON tblPosition.divCode = tblDepartment.divCode 
					AND tblPosition.deptCode = tblDepartment.deptCode 
					AND tblPosition.sectCode = tblDepartment.sectCode 
				LEFT JOIN tblEmpLevel on tblPosition.level=tblEmpLevel.empLevel
				LEFT JOIN tblRankType on tblPosition.rank=tblRankType.rankCode
				WHERE tblDepartment.deptLevel = '3' and Active='A'  $and order by posDesc";
		$res=$this->execQry($sqlpos);
		if ($act==1) {
			$res=$this->getArrRes($res);
		}	
		elseif ($act==2) {	
			$res=$this->getSqlAssoc($res);
		}
		return $res;		
	}	    

	function empContactswil($empNo) {
		$qryempContacts="Select contactName,contactDesc from tblContactMast inner join 
						tblContactTypeRef on tblContactMast.contactCd = tblContactTypeRef.contactCd 
						where empNo='$empNo' order by contactDesc";
		return $this->getArrRes($this->execQry($qryempContacts));		
	}
	
	function empOtherInfos($empNo,$compCode="") {
		if ($compCode == "") {
			$compCode = $_SESSION['company_code'];
		}
		$qryempOtherInfos="SELECT tblCityRef.cityDesc, tblCitizenshipRef.citizenDesc, tblReligionRef.relDesc, 
					  tblPayBank.bankDesc, tblEmpMast.empNo,tblPosition.posShortDesc,
							empSex = 
								CASE empSex 
								  WHEN 'M' THEN 'Male'
								  WHEN 'F' then 'Female'
								END,
							empMarStat = 
								CASE empMarStat
								  WHEN 'SG' THEN 'Single'
								  WHEN 'ME' THEN 'Married'
								  WHEN 'SP' THEN 'Separated'
								  WHEN 'WI' THEN 'Widow(er)'
								END,
							empPayType = 
								CASE empPayType
								  WHEN 'D' THEN 'Daily'
								  WHEN 'M' THEN 'Monthly'
								END,
							empStat = 
								CASE empStat
								  WHEN 'RG' THEN 'Regular'
								  WHEN 'PR' THEN 'Probationary'
								  WHEN 'CN' THEN 'Contractual'
								  WHEN 'RS' THEN 'Resigned'
								  WHEN 'TR' THEN 'Terminated'
								  WHEN 'IN' THEN 'Inactive'
								  WHEN 'AP' THEN 'Applicant'
								END,
							empPayGrp = 
								CASE empPayGrp
								  WHEN '1' THEN 'Group 1'
								  WHEN '2' THEN 'Group 2'
								END,											   
                      tblTeu.teuDesc, tblTimeShiftRef.shiftDesc
					  FROM tblEmpMast LEFT OUTER JOIN
                      tblTimeShiftRef ON tblEmpMast.compCode = tblTimeShiftRef.compCode AND 
                      tblEmpMast.empShiftId = tblTimeShiftRef.shiftId LEFT OUTER JOIN
                      tblPosition ON tblEmpMast.empPosId = tblPosition.posCode AND 
					  tblEmpMast.compCode = tblPosition.compCode LEFT OUTER JOIN
                      tblTeu ON tblEmpMast.empTeu = tblTeu.teuCode LEFT OUTER JOIN
                      tblPayBank ON tblEmpMast.compCode = tblPayBank.compCode AND 
                      tblEmpMast.empBankCd = tblPayBank.bankCd LEFT OUTER JOIN
                      tblReligionRef ON tblEmpMast.empReligion = tblReligionRef.relCd LEFT OUTER JOIN
                      tblCitizenshipRef ON tblEmpMast.empCitizenCd = tblCitizenshipRef.citizenCd LEFT OUTER JOIN
                      tblCityRef ON tblEmpMast.empCityCd = tblCityRef.cityCd
					  WHERE tblEmpMast.empNo='$empNo' and tblEmpMast.compCode='$compCode'";
		return $this->getSqlAssoc($this->execQry($qryempOtherInfos));			  
	}	
	function getPayReason($compCode) {
		$qry = "Select * from tblPayReason where compCode='$compCode'";
		return $this->getArrRes($this->execQry($qry));
	}
	function getPeriod($payPd) {
		$qryperiod = "Select pdSeries FROM tblPayPeriod WHERE (compCode = '{$_SESSION['company_code']}') AND (payGrp = {$_SESSION['pay_group']}) AND (payCat = {$_SESSION['pay_category']}) AND (pdStat = 'O')";
		$res = $this->getSqlAssoc($this->execQry($qryperiod));
		if ($res['pdSeries'] == $payPd) {
			return true;
		} else {
			return false;
		}	
			
	}
	
function getOpenPeriodwil() {
		$compCode = $_SESSION['company_code'];
		$grp = $_SESSION['pay_group'];
		$cat = $_SESSION['pay_category'];
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%Y-%m-%d') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdStat = 'O' AND 
					compCode = '$compCode' AND payGrp = '$grp' AND payCat = '$cat' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getUnPostedLoans($arrPd,$arrLoan,$empNo){
		$qryLoans = "Select * from tblEmpLoansDtl where empNo='$empNo' AND compCode='".$_SESSION['company_code']."' 
					AND lonTypeCd='".$arrLoan['lonTypeCd']."' AND lonRefNo='".$arrLoan['lonRefNo']."'
					AND trnGrp='".$_SESSION['pay_group']."' AND trnCat='".$_SESSION['pay_category']."'
					AND pdNumber='".$arrPd['pdNumber']."' AND pdYear='".$arrPd['pdYear']."' AND dedTag='Y'";
		return $this->getArrRes($this->execQry($qryLoans));	
		
	}
	function strUpper($text) {
		return strtoupper(str_replace("'","''",stripslashes(strtoupper($text))));
	}
	function getTransCode($cat) {
		$qry = "Select * from tblPayTransType where trnCat='$cat' and compCode='{$_SESSION['company_code']}'";
		return $this->getArrRes($this->execQry($qry));	
	}		
	function getLoantrnCode($loanTypeCd) {
		$sqltrnCode = "Select trnApply from tblLoanType Inner Join tblPayTransType on tblLoanType.trnCode = tblPayTransType.trnCode where lonTypeCd=$loanTypeCd and tblLoanType.compCode='{$_SESSION['company_code']}' and tblPayTransType.compCode='{$_SESSION['company_code']}'";
		$res = $this->getSqlAssoc($this->execQry($sqltrnCode));
		return $res['trnApply'];
		
	}
	
	function arrRank($where,$act) {
		$array = array(0=>'');
		if ($act==1) {
			$qryRank = "Select rank from tblPosition $where";
			$arrRes = $this->getArrRes($this->execQry($qryRank));
			foreach($arrRes as $val) {
				$rank = (int)$val['rank'];
				$array = array('',"$rank"=>"Rank $rank");
			}
		} else 	{
			$qryLevel = "Select level from tblPosition $where";
			$arrRes = $this->getArrRes($this->execQry($qryLevel));
			foreach($arrRes as $val) {
				$level = $val['level'];
				$array= array('',"$level"=>"Level $level");
			}
		}
		return $array;
	}
	//gens functions
function qryListOfEmployees($empNo,$empDiv, $empDept, $empSect, $orderBy,$empBrnCode,$locType)
	{
		if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName, empDiv, empDepCode, empSecCode ";} 
		if ($orderBy==2) {$orderBy1 = " ORDER BY empNo, empDiv, empDepCode, empSecCode ";} 
		if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		if ($locType=="S")
			$locType1 = " AND (empLocCode = '{$empBrnCode}')";
		if ($locType=="H")
			$locType1 = " AND (empLocCode = '0001')";
			
		$qryEmpList = "SELECT * FROM tblEmpMast
					   WHERE compCode = '".$_SESSION['company_code']."' AND 
						empStat NOT IN('RS','IN','TR') 
						AND empPayGrp = '".$_SESSION['pay_group']."'
						AND empPayCat = '".$_SESSION['pay_category']."'
						$empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $empBrnCode1 $locType1
						";
		$resEmp = $this->execQry($qryEmpList);
		$arrEmpList = $this->getArrRes($resEmp);
		foreach($arrEmpList as $arrEmpList_val)
		{
			$empNoList.=$arrEmpList_val["empNo"].",";
		}
		$empNoList = substr($empNoList,0,strlen($empNoList) - 1);
		return $empNoList;
	}
	
	
	
	function getBrnCodes($rsQry)
	{
		$arrEmpList = $this->getArrRes($rsQry);
		foreach($arrEmpList as $arrEmpList_val)
		{
			$empNoList.=$arrEmpList_val["empNo"].",";
		}
		$empNoList = substr($empNoList,0,strlen($empNoList) - 1);
		
		$qryBrnCodes = "Select empBrnCode, brnch.brnDesc as brn_Desc, empLocCode, brnLoc.brnDesc as brn_DescLoc from tblEmpMast empMast, tblBranch brnch, tblBranch brnLoc 
						where empMast.empBrnCode=brnch.brnCode and empMast.empLocCode=brnLoc.brnCode
						and empNo in ($empNoList) 
						group by empLocCode, empBrnCode, brnch.brnDesc,  brnLoc.brnDesc order by brnch.brnDesc, brnLoc.brnDesc ";

		$resBranch = $this->execQry($qryBrnCodes);
		$resBranch = $this->getArrRes($resBranch);
		return $resBranch;
	}
	
	function getArrEmpList($resEmp)
	{	
		$arrEmpList = $this->getArrRes($resEmp);
		foreach($arrEmpList as $arrEmpList_val)
		{
			$empNoList.=$arrEmpList_val["empNo"].",";
		}
		$empNoList = substr($empNoList,0,strlen($empNoList) - 1);
		return $empNoList;
	}
	
	function getLocTotals($arrBranchCd)
	{
		foreach($arrBranchCd as $arrBrnCode_val)
		{
			$BranchCode[$arrBrnCode_val["empBrnCode"]].= $arrBrnCode_val["empLocCode"].",";
		}
		return $BranchCode;
	}
	
	function getBrnTotals($arrBranchCd)
	{
		foreach($arrBranchCd as $arrBrnCode_val)
		{
			if($arrBrnCode_val["empBrnCode"] != $tmpBrnCd){
				$BrnCode.=$arrBrnCode_val["empBrnCode"].",";
			}
			$tmpBrnCd = $arrBrnCode_val["empBrnCode"];
		}
		return $BrnCode;
	}	
	function getRank($rank) {
		$sqlRank = "Select * from tblRankType where compCode='{$_SESSION['company_code']}' AND rankStat='A' AND rankCode='$rank'";
		return $this->getSqlAssoc($this->execQry($sqlRank));		
	}	
	
	//added by Nhomer Cabico
	//added function for province query 
	function getProvince(){
		$sqlProvince="Select provinceCd,provinceDesc from tblProvinceRef order by provinceDesc";
		$res=$this->execQry($sqlProvince);
		return $this->getArrRes($res);
	}
	
	//added function for municipality and or city
	function getMunicipality($where=""){
		if($where!=""){
			$wheres=$where;
			}
		else{
			$wheres="";
			}	
		$sqlMunicipality="Select municipalityCd,municipalityDesc,provinceCd from tblmunicipalityRef $wheres order by municipalityDesc";
		$res=$this->execQry($sqlMunicipality);
		return $this->getArrRes($res);

	}
}
?>