<?
class commonObj extends dbHandler {
	
	function getUserHeaderInfo($empNo,$empId){
		$qryGetUserInfo = "SELECT * FROM tblEmpMast 
						   WHERE empNo    = '".trim($empNo)."'
						   AND id = '{$empId}'
						   AND   empStat NOT IN('RS','TR') ";
		$resGetUserInfo = $this->execQryI($qryGetUserInfo);
		return $this->getSqlAssocI($resGetUserInfo);		
	}
	
	function getUserLogInInfoForMenu($empNo){
		
		$qryUserLogInInfo = "SELECT tblUsers.*,substring(pagesPayroll,1,255) as pages1,substring(pagesPayroll,256,255) as pages2 
					FROM tblUsers 
					WHERE  empNo    = '{$empNo}'
					AND   userStat = 'A'";
				
		$resUserLogInInfo = $this->execQryI($qryUserLogInInfo);
		return $this->getSqlAssocI($resUserLogInInfo);
	}
	
	function getUserInfo($compCode,$empNo,$where){

		$qryGetUserInfo = "SELECT * FROM tblEmpMast 
						   WHERE compCode = '{$compCode}'
						   AND   empNo    = '".trim($empNo)."'
						   AND   empStat NOT IN('RS','TR') ";
						   
		if($where != ""){
			$qryGetUserInfo .= $where;
		}
		$resGetUserInfo = $this->execQryI($qryGetUserInfo);
		return $this->getSqlAssocI($resGetUserInfo);
		
	}

	function getAllEmployees($compCode,$empNo,$where){
		$empname = $this->getEmployee($compCode,$empNo,"");
		$compcode = $empname["compCode"];
		$paygroup = $empname["empPayGrp"];
		$paycat = $empname["empPayCat"];
		$pdstat =  " AND pdTSStat='O'";
		$pd = $this->getPeriodWil($compcode, $paygroup, $paycat, $pdstat); 
		$pdseries = " and pdSeries='".$pd['pdSeries']."'";

		$arr_period_Info = $this->getPeriodWil($compcode, $paygroup, $paycat,$pdseries);

		$qryGetUserInfo = "SELECT * FROM tblEmpMast 
						   WHERE compCode = '{$compCode}'
						   AND   empNo    = '".trim($empNo)."'";				   
		$resGetUserInfo = $this->execQryI($qryGetUserInfo);
		$empstat = $this->getSqlAssocI($resGetUserInfo);

		if($empstat['empStat']=='RS'){
			$qry = "SELECT * FROM tblEmpMast 
							   WHERE compCode = '{$compCode}'
							   AND   empNo    = '".trim($empNo)."'
							   AND (dateResigned between '".$arr_period_Info['pdFrmDate']."' and '".$arr_period_Info['pdToDate']."' 
							   or endDate between '".$arr_period_Info['pdFrmDate']."' and '".$arr_period_Info['pdToDate']."') ";				   
			$resQry = $this->execQryI($qry);
			return $this->getSqlAssocI($resQry);
		}
		else{
			$qryEmp = "SELECT * FROM tblEmpMast 
							   WHERE compCode = '{$compCode}'
							   AND   empNo    = '".trim($empNo)."'";				   
			$resEmp = $this->execQryI($qryEmp);
			return $this->getSqlAssocI($resEmp);
		}
	}		

	function getInfoBranch($brn,$cmp){
		$qryInfoBranch = "SELECT * 
							FROM tblBranch
							WHERE brnCode ='".$brn."' and compCode='".$cmp."'";
		$resGetInfoBranch = $this->execQryI($qryInfoBranch);
		$resBrn=$this->getSqlAssocI($resGetInfoBranch);
		return $resBrn['brnDesc'];
	}

	function getEmpBranch($compCode,$where){
		$qryEmpBranch = "SELECT * FROM tblEmpMast 
						   WHERE compCode = '{$compCode}' ";
			if($where != ""){
				$qryEmpBranch .= $where;
			}
			return $this->getArrResI($this->execQryI($qryEmpBranch));
		}
	
	function getEmployeeList($compCode,$where){
		$qryGetEmplist = "SELECT * FROM tblEmpMast 
						   WHERE compCode = '{$compCode}' ";
		if($where != ""){
			$qryGetEmplist .= $where;
		}
	
		$resGetEmplist = $this->execQryI($qryGetEmplist);
	
		if($resGetEmplist){
			if($this->getRecCountI($resGetEmplist) == 1){
				return $this->getSqlAssocI($resGetEmplist);
			}
			else{
				return $this->getArrResI($resGetEmplist);
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
		$resGetUserInfo = $this->execQryI($qryGetUserInfo);
		if($this->getRecCountI($resGetUserInfo) > 0){
			return $this->getSqlAssocI($resGetUserInfo);
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
		$resUserLogInInfo = $this->execQryI($qryUserLogInInfo);
		return $this->getSqlAssocI($resUserLogInInfo);
	}
	
	function getCompany($compCode){
		
		$qry = "SELECT * FROM tblCompany WHERE compStat = 'A' ";
		if($compCode != ""){
			$qry .= "AND compCode = '{$compCode}'";
		}
		$res = $this->execQryI($qry);
		if($compCode != ""){
			return $this->getSqlAssocI($res);
		}
		else{
			return $this->getArrResI($res);
		}
	}
	
	function getChangeCompany($compCode){
		
		$qry = "SELECT * FROM payroll_company..tblCompany WHERE compStat = 'A' ";
		if($compCode != ""){
			$qry .= "AND compCode = '{$compCode}'";
		}
		$res = $this->execQryI($qry);
		if($compCode != ""){
			return $this->getSqlAssocI($res);
		}
		else{
			return $this->getArrResI($res);
		}
	}
	
	function getCompanyName($compCode){
		$qry = "SELECT compName FROM tblCompany WHERE compStat = 'A' AND compCode = '{$compCode}' ";
		$res = $this->execQryI($qry);
		$row = $this->getSqlAssocI($res);
		/*if (in_array($compCode,array(1,7,8,9,10,11,12))){
			return $row['compName'] = 'PUREGOLD PRICE CLUB, INC.';
		}
		else{*/
			return  $row['compName'];
		//}
	}	
	
	function getCompanyInfo($compCode){
		
		$qry = "SELECT * FROM tblCompany WHERE compStat = 'A' AND compCode = '{$compCode}' ";
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}	
	
	function getPayCat($compCode,$where){
		
		 $qryGetPayCat = "SELECT * FROM tblPayCat 
						 WHERE compCode = '{$compCode}'
						 AND payCatStat = 'A' ";
		if(!empty($where) || $where != ''){
			$qryGetPayCat .= $where;
		}		
		$resGetPayCat = @$this->execQryI($qryGetPayCat);

		if($this->getRecCountI($resGetPayCat) == 1){
			return $this->getSqlAssocI($resGetPayCat);
		}
		else{
			return $this->getArrResI($resGetPayCat);
		}
	}

	function getPayPeriod($compCode,$where){
		
		$qryGetPayPeriod = "SELECT * FROM tblPayPeriod
							WHERE compCode = '{$compCode}'  ";
		if(!empty($where)){
			$qryGetPayPeriod .= $where;
		}
		$resGetPayPeriod = $this->execQryI($qryGetPayPeriod);
		if($this->getRecCountI($resGetPayPeriod) == 1){
			return $this->getSqlAssocI($resGetPayPeriod);
		}
		else{
			return $this->getArrResI($resGetPayPeriod);
		}
	}
	
	function getPayPrd($compCode,$where){
		
		$qryGetPayPeriod = "SELECT compCode,payGrp,payCat,pdYear,pdNumber,pdPayable,
							date_format(pdFrmDate,'%Y-%m-%d') as pdFrmDate,
							date_format(pdToDate,'%Y-%m-%d') as pdToDate,pdStat 
							FROM tblPayPeriod
							WHERE compCode = '{$compCode}'  ";
		if(!empty($where)){
			$qryGetPayPeriod .= $where;
		}
		$resGetPayPeriod = $this->execQryI($qryGetPayPeriod);
		return $this->getArrResI($resGetPayPeriod);
	}
	
	function getPayPrdOne($compCode,$where){
		$qryGetPayPeriod = "SELECT compCode,payGrp,payCat,pdYear,pdNumber,pdPayable,
							date_format(pdFrmDate,'%Y-%m-%d') as pdFrmDate,
							date_format(pdToDate,'%Y-%m-%d') as pdToDate,pdStat, pdSeries 
							FROM tblPayPeriod
							WHERE compCode = '{$compCode}'  ";
		if(!empty($where)){
			$qryGetPayPeriod .= $where;
		}
	 	$qryGetPayPeriod .= " limit 1";
		$resGetPayPeriod = $this->execQryI($qryGetPayPeriod);
		return $this->getArrResI($resGetPayPeriod);
	}	
		
	function getPayPeriod_OtherEarn($compCode,$where){		
		$qryGetPayPeriod = "SELECT compCode,payGrp,payCat,pdYear,pdNumber,date_format(pdPayable,'%Y-%m-%d') as pdPayable,
								pdFrmDate,pdToDate,pdGenEmpSched,pdTsTag,pdTSStat,pdLoansTag,pdEarningsTag,pdProcessTag,
								pdProcessDate,pdProcessedBy,pdDateClosed,pdClosedBy,pdStat,pdSeries FROM tblPayPeriod
							WHERE compCode = '{$compCode}'
							";
		if(!empty($where)){
			$qryGetPayPeriod .= $where;
		}
		$qryGetPayPeriod .= "order by pdPayable desc";
		
		$resGetPayPeriod = $this->execQryI($qryGetPayPeriod);
		if($this->getRecCountI($resGetPayPeriod) == 1){
			return $this->getSqlAssocI($resGetPayPeriod);
		}
		else{
			return $this->getArrResI($resGetPayPeriod);
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
		$this->checkModuleAccess();
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
	
	function getArrResItDay(){
		
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
		$resGetTEU = $this->execQryI($qryGetTEU);
		return $this->getArrResI($resGetTEU);
	}
	
	function getPayBank($compCode){

		$qryPayBank = "SELECT * FROM tblPayBank 
						WHERE compCode = '{$compCode}'
						AND bankStat = 'A'";
		$resPayBank = $this->execQryI($qryPayBank);
		 return $this->getArrResI($resPayBank);
	}
		
	function getBranch($compCode){
		$qrygetBranch = "SELECT * FROM tblBranch 
						WHERE compCode = '{$compCode}' order by brnDesc";
		$resgetBranch = $this->execQryI($qrygetBranch);
		return $this->getArrResI($resgetBranch);
	}	
	
	function getBranchName($compCode,$brnCode){
		$qrybrnName = "Select * from tblBranch
						Where compCode='{$compCode}' and brnCode='{$brnCode}'";
		$resbrnName = $this->getSqlAssocI($this->execQryI($qrybrnName));
		return $resbrnName['brnDesc'];					
	}
	
	function getShortBranchName($compCode,$branchCode){
		$qryBranchName = "Select brnShortDesc from tblBranch
						  Where compCode='{$compCode}' and brnCode='{$branchCode}'";
		$resBranchName = $this->getSqlAssocI($this->execQryI($qryBranchName));
		return $resBranchName['brnShortDesc'];	
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
	
	function makeArrDate($arrRes,$index,$index2,$value){

		$arrResult = array();
		$arrResult[0] = "";
		foreach ($arrRes as $arrVal){
			$arrResult[$arrVal[$index]."-".$arrVal[$index2]] = $this->dateFormat($arrVal[$value]);
		}
		return $arrResult;
	}
	
	function getDepartment($compCode,$divCode="",$deptCode="",$sectCode="",$deptLevel){

		$qryGetDepartment = "SELECT * FROM tblDepartment 
							 WHERE compCode = '{$compCode}' ";
		if($deptLevel == 1){
			$qryGetDepartment .= "AND deptLevel = '{$deptLevel}' ORDER BY divCode ";
			$qryGetDepartment = $this->execQryI($qryGetDepartment);
			return $this->getArrResI($qryGetDepartment);
		}
		if($deptLevel == 2){
			if($divCode != ""){
				$qryGetDepartment .= "AND divCode = '{$divCode}' ";
			}
			$qryGetDepartment .= "AND deptLevel = '{$deptLevel}' ORDER BY deptCode ";
			$resGetDepartment = $this->execQryI($qryGetDepartment);
			return $this->getArrResI($resGetDepartment);
		}
		if($deptLevel == 3){
			if($divCode != "" && $deptCode != ""){
				$qryGetDepartment .= "AND divCode = '{$divCode}' AND deptCode = '{$deptCode}' ";
			}
			$qryGetDepartment .= "AND deptLevel = '{$deptLevel}' ORDER BY sectCode ";
			$resGetDepartment = $this->execQryI($qryGetDepartment);
			return $this->getArrResI($resGetDepartment);
		}
		
		echo $qryGetDepartment;
	}
	
	function getAllowType($compCode){
		$qryGetAllowType = "SELECT * FROM tblAllowType 
							WHERE compCode = '{$compCode}'
							AND hrTag='Y'
							AND allowTypeStat = 'A'";
		
		$resGetAllowType = $this->execQryI($qryGetAllowType);
		return $this->getArrResI($resGetAllowType);
	}
	
	function dateFormat($date){
		return date('Y-m-d',strtotime($date)) ;
	}
	
	function getTransType_OtherDed($compCode,$type,$where){
		
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
		$qryGetTransType .= " AND trnstat = 'A' and trnCode not in (Select trnCode from tblLoanType where lonTypeStat='A' and compCode='".$_SESSION["company_code"]."')
						     ORDER BY trnDesc ";
		
		$resGetTransType = $this->execQryI($qryGetTransType);
		if($this->getRecCountI($resGetTransType) > 0){
			return $this->getArrResI($resGetTransType);
		}
		else{
			return $this->getSqlAssocI($resGetTransType);
		}
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
		
		$resGetTransType = $this->execQryI($qryGetTransType);
		if($this->getRecCountI($resGetTransType) > 0){
			return $this->getArrResI($resGetTransType);
		}
		else{
			return $this->getSqlAssocI($resGetTransType);
		}
	}
	
	function getPrevEmployer($compCode,$where){
		
		$qryGetPrevEmployer = "SELECT * FROM tblPrevEmployer
								WHERE compCode = '{$compCode}' ";
		if(!empty($where)){
			$qryGetPrevEmployer .= $where . " AND prevStat = 'A'" ;
		}
		$resGetPrevEmployer = $this->execQryI($qryGetPrevEmployer);
		if($this->getRecCountI($resGetPrevEmployer) > 0){
			return $this->getArrResI($resGetPrevEmployer);
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
		
		$resGetDiv = $this->execQryI($qryGetDiv);
		return $this->getSqlAssocI($resGetDiv);
	}
	function getDeptDescArt($compCode,$empDiv,$empDept){
		$qryGetDept = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 2 
						 AND divCode = '{$empDiv}'
						 AND deptCode = '{$empDept}'
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		$resGetDept = $this->execQryI($qryGetDept);
		return $this->getSqlAssocI($resGetDept);
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
		$resGetSect = $this->execQryI($qryGetSect);
		return $this->getSqlAssocI($resGetSect);
	}
	function getCatArt($compCode){
		$qryGetCat = "SELECT * FROM tblPayCat
					     WHERE compCode = '{$compCode}'
						 AND payCatStat = 'A'
					     ORDER BY payCatDesc ASC";
		$resGetCat = $this->execQryI($qryGetCat);
		return $this->getArrResI($resGetCat);
	}
	function getCatListArt($compCode){
		
		$qryGetPayCat = "SELECT * FROM tblPayCat 
						 WHERE compCode = '{$compCode}'
						 AND payCatStat = 'A'";
		$resGetPayCat = $this->execQryI($qryGetPayCat);
		return $this->getArrResI($resGetPayCat);
	}
	function getCatDataArt($compCode){
		$qryGetCat = "SELECT * FROM tblPayCat
					     WHERE compCode = '{$compCode}'
						 AND payCatStat = 'A'
					     ORDER BY payCat DESC";
		$resGetCat = $this->execQryI($qryGetCat);
		return $this->getSqlAssocI($resGetCat);
	}
	
	function getBrnchArt(){
		$qryGetBranch = "Select * from tblBranch where compCode='".$_SESSION["company_code"]."' and 
					  brnStat='A' order by brnDesc";
		$resGetBranch = $this->execQryI($qryGetBranch);
		return $this->getArrResI($resGetBranch);
	}
	
	
	
	function getDivArt($compCode){
		$qryGetDiv = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 1 
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		$resGetDiv = $this->execQryI($qryGetDiv);
		return $this->getArrResI($resGetDiv);
	}
	function getDeptArt($compCode,$empDiv){
		$qryGetDept = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 2 
						 AND divCode = '{$empDiv}'
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		
		$resGetDept = $this->execQryI($qryGetDept);
		return $this->getArrResI($resGetDept);
	}
	function getSectArt($compCode,$empDiv,$empDept){
		$qryGetSect = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 3 
						 AND divCode = '{$empDiv}' 
						 AND deptCode = '{$empDept}' 
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		$resGetSect = $this->execQryI($qryGetSect);
		return $this->getArrResI($resGetSect);
	}
	function getAllowTypeListArt($compCode){
		$qry = "SELECT * FROM tblAllowType
					     WHERE compCode = '{$compCode}'";
		$qry .=" AND allowTypeStat = 'A'
					     ORDER BY allowDesc ASC";
		$res = $this->execQryI($qry);
		return $this->getArrResI($res);
	}
	function getLoanTypeListArt($compCode,$loanCode){
		$qryGetLoanType = "SELECT * FROM tblLoanType
					     WHERE compCode = '{$compCode}'";
						  if ($loanCode>"") {
		$qryGetLoanType .=" AND lonTypeCd LIKE '$loanCode%' ";
						  }
		$qryGetLoanType .=" AND lonTypeStat = 'A'
					     ORDER BY lonTypeDesc ASC";
		$resGetLoanType = $this->execQryI($qryGetLoanType);
		return $this->getArrResI($resGetLoanType);
	}
	function getLoanTypeDataArt($compCode,$loanCode){
		$qryGetLoanType = "SELECT * FROM tblLoanType
					     WHERE compCode = '{$compCode}' 
						 AND lonTypeCd LIKE '$loanCode%' 
						 AND lonTypeStat = 'A'
					     ORDER BY lonTypeDesc ASC";
		$resGetLoanType = $this->execQryI($qryGetLoanType);
		return $this->getSqlAssocI($resGetLoanType);
	}
	function getEmpLoanListArt($compCode,$empNo,$loanCode){
		$qryGetEmpLoan = "SELECT tblEmpLoans.lonTypeCd, tblLoanType.lonTypeDesc, tblEmpLoans.lonRefNo, tblEmpLoans.lonAmt, 
						  tblEmpLoans.lonWidInterst, tblEmpLoans.lonStart, tblEmpLoans.lonEnd, tblEmpLoans.lonSked,
						  tblEmpLoans.lonNoPaymnts, tblEmpLoans.lonDedAmt1, tblEmpLoans.lonDedAmt2, tblEmpLoans.lonPayments, 
						  tblEmpLoans.lonPaymentNo, tblEmpLoans.lonCurbal, tblEmpLoans.lonLastPay, 
						  concat(tblLoanType.lonTypeDesc,' (Ref.No:' ,tblEmpLoans.lonRefNo,')') AS loanDescRefNo,
						  concat(tblEmpLoans.lonTypeCd, '-' ,tblEmpLoans.lonRefNo) AS loanTypeCdRefNo 
						  FROM tblEmpLoans INNER JOIN tblLoanType ON tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd
					      WHERE tblEmpLoans.compCode = '{$compCode}'
						  AND tblLoanType.compCode = '{$compCode}'
						  AND tblEmpLoans.empNo = '{$empNo}'
						  AND tblLoanType.lonTypeCd LIKE '$loanCode%'
						  AND tblEmpLoans.lonTypeCd LIKE '$loanCode%'
						  AND tblEmpLoans.lonStat = 'O'
						  AND tblLoanType.lonTypeStat = 'A'";
		$resGetEmpLoan = $this->execQryI($qryGetEmpLoan);
		return $this->getArrResI($resGetEmpLoan);
	}
	function getEmpAllowListArt($compCode,$empNo) {
		$qry ="SELECT tblAllowType.allowDesc, tblAllowance.allowStart, tblAllowance.allowEnd, 
	               tblAllowance.allowSked, tblAllowance.allowAmt, tblAllowance.allowTaxTag, tblAllowance.allowPayTag
			   FROM tblAllowance INNER JOIN
            	   tblAllowType ON tblAllowance.allowCode = tblAllowType.allowCode
			   WHERE (tblAllowance.compCode = '{$compCode}') AND (tblAllowType.compCode = '{$compCode}') AND (tblAllowance.allowStat = 'A') AND 
               	   (tblAllowType.allowTypeStat = 'A') AND (tblAllowance.empNo = '{$empNo}')
			   ORDER BY tblAllowType.allowDesc";
		$res = $this->execQryI($qry);
		return $this->getArrResI($res);
	}
	function getEmpLoanDataArt($compCode,$empNo,$lonTypeCd, $lonRefNo){
		$qryGetEmpLoan = "SELECT lonGranted,tblEmpLoans.lonSeries,tblEmpLoans.lonTypeCd, tblLoanType.lonTypeDesc, tblEmpLoans.lonRefNo, tblEmpLoans.lonAmt, 
						  tblEmpLoans.lonWidInterst, tblEmpLoans.lonStart, tblEmpLoans.lonEnd, tblEmpLoans.lonSked,
						  tblEmpLoans.lonNoPaymnts, tblEmpLoans.lonDedAmt1, tblEmpLoans.lonDedAmt2, tblEmpLoans.lonPayments, 
						  tblEmpLoans.lonPaymentNo, tblEmpLoans.lonCurbal, tblEmpLoans.lonLastPay,
								CASE lonStat
								  WHEN 'O' THEN 'Open'
								  WHEN 'C' THEN 'Completed'
								  WHEN 'D' THEN 'Deleted'
								END as lonStat
						  FROM tblEmpLoans INNER JOIN tblLoanType ON tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd
					      WHERE tblEmpLoans.compCode = '{$compCode}'
						  AND tblLoanType.compCode = '{$compCode}'
						  AND tblEmpLoans.empNo = '{$empNo}'
						  AND tblLoanType.lonTypeCd = '{$lonTypeCd}'
						  AND tblEmpLoans.lonTypeCd = '{$lonTypeCd}'
						  AND tblEmpLoans.lonRefNo = '{$lonRefNo}'
						  AND tblEmpLoans.lonStat = 'O'
						  AND tblLoanType.lonTypeStat = 'A'";
		$resGetEmpLoan = $this->execQryI($qryGetEmpLoan);
		return $this->getSqlAssocI($resGetEmpLoan);
	}
	function getEmpCatArt($compCode,$payCat){
		$qry = "SELECT * FROM tblPayCat
					     WHERE compCode = '{$compCode}' 
						 AND payCat = '$payCat' 
						 AND payCatStat = 'A'";
						 
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}
	function getEmpBranchArt($compCode,$branch){
		$qry = "SELECT * FROM tblBranch
					     WHERE compCode = '{$compCode}' 
						 AND brnCode = '$branch' 
						 AND brnStat = 'A'";
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}
	function getEmpTeuArt($taxCode){
		$qry = "SELECT * FROM tblTeu
					     WHERE teuCode = '{$taxCode}'";
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}
	function getEmpBankArt($compCode,$bank){
		$qry = "SELECT * FROM tblPayBank
					     WHERE compCode = '{$compCode}' 
						 AND bankCd = '$bank' 
						 AND bankStat = 'A'";
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}
	function valDateArt($date) {
		if ($date=="") {
			$newDate = "";
		} else {
			$newDate = date("m/d/Y",strtotime($date));
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
		$newdate = date("m/d/Y", $gmt);
		return $newdate;
	}
	function getCompanyArt($compCode) {
		$qry = "SELECT * FROM tblCompany
					     WHERE compCode = '{$compCode}'
						 AND compStat = 'A'";
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}
	function getDayTypeDescArt($dayType) {
		$qry = "SELECT * FROM tblDayType
					     WHERE dayType = '{$dayType}'
						 AND dayStat = 'A'";
		$res = $this->execQryI($qry);
		$row = $this->getSqlAssocI($res);
		return  $row['dayTypeDesc'];
	}
	function getOtPremArt($dayType) {
		$qry = "SELECT * FROM tblOtPrem
					     WHERE dayType = '{$dayType}' ";
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}
	function getTransTypeDescArt($compCode,$trnCode) {
		$qry = "SELECT * FROM tblPayTransType
				WHERE compCode = '$compCode' AND cast(trnCode as unsigned) = '$trnCode' AND trnStat = 'A' ";
		$res = $this->execQryI($qry);
		$row = $this->getSqlAssocI($res);
		return  $row['trnDesc'];
	}
	function getTransTypeArt($compCode,$trnCode) {
		$qry = "SELECT * FROM tblPayTransType
				WHERE compCode = '$compCode' AND trnCode = '$trnCode' AND trnStat = 'A' ";
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}
	
	function getcount($compCode,$empNo){
			$qryPrev = 	"SELECT * FROM tblPrevEmployer 
					 WHERE compCode = '{$compCode}'
					 AND prevStat = 'A' 
					 AND empNo = '{$empNo}' ";
					
			$resPrev = $this->execQryI($qryPrev);		   
			return $this->getRecCountI($resPrev);
	}    
	
	function getLoanTypeListWil($compCode,$loanCode,$empNo){
		$qryGetLoanType = "SELECT * FROM tblLoanType
					     WHERE compCode = '{$compCode}'";
		if ($loanCode>"") {
			$qryGetLoanType .=" AND lonTypeCd LIKE '$loanCode%' ";
		  	if ($loanCode!=2 && $loanCode!=3) {
/*				$qryGetLoanType .=" AND trnCode NOT IN
                          			(SELECT tblLoanType.trnCode FROM tblEmpLoans INNER JOIN
								   	tblLoanType ON tblEmpLoans.compCode = tblLoanType.compCode AND 
									tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd
									WHERE tblEmpLoans.empNo = '$empNo' 
									AND lonCurbal > 0 
									AND lonStat = 'O'
									AND tblEmpLoans.compCode = '{$compCode}'
									GROUP BY tblLoanType.trnCode)";
*/

			}
			elseif ($loanCode==3) {
			} else {
				/*$qryGetLoanType .=" AND lonTypeCd NOT IN (Select lonTypeCd from tblEmpLoans 
									where compCode = '{$compCode}'
									AND lonPaymentNo<6
									AND empNo = '$empNo' AND lonStat = 'O')";*/
			}
		  }
		$qryGetLoanType .=" AND lonTypeStat = 'A'
					     	ORDER BY lonTypeDesc ASC";
		$resGetLoanType = $this->execQryI($qryGetLoanType);
		return $this->getArrResI($resGetLoanType);
	}
	
	function checkEmpLoans($lonTypeCd,$empNo,$compCode) {
		$qryCheckempLoan="Select * from tblEmpLoans 



							WHERE tblEmpLoans.empNo = '$empNo' 
							AND lonTypeCd='$lonTypeCd'
							AND lonCurbal > 0 
							AND lonStat = 'O'
							AND tblEmpLoans.compCode = '{$compCode}'";
		return	$this->getArrResI($this->execQryI($qryCheckempLoan));		
	}
  
/*wil's function*/
	
	function getbranchwil($compCode=""){
		if ($compCode!="") {
			$filter=" where compCode='$compCode'";
		}
		$qrygetBranch = "SELECT * FROM tblBranch $filter";
		$resgetBranch = $this->execQryI($qrygetBranch);
		return $this->getArrResI($resgetBranch);
	}

	function getreligionwil(){
		$qryreligions="Select * from tblReligionRef order by relDesc";
		$res= $this->execQryI($qryreligions);
		return $this->getArrResI($res);
	}	

	function getcitizenshipwil(){
		$citizenship="Select * from tblCitizenshipRef order by citizenDesc";
		$res= $this->execQryI($citizenship);
		return $this->getArrResI($res);
	}	

	function getbankwil(){
		$bank="Select * from tblPayBank order by bankDesc";
		$res= $this->execQryI($bank);
		return $this->getArrResI($res);
	}	

	function getshiftwil($compCode=""){
		$shift="Select * from tblTimeShiftRef where compCode='$compCode' and active='1' order by shiftDesc";
		$res= $this->execQryI($shift);
		return $this->getArrResI($res);
	}	

	function getdepartmenttwil($compcode){
		$department="Select * from tblDepartment $compcode order by deptDesc";
		$res= $this->execQryI($department);
		return $this->getArrResI($res);
	}

	function getcontacttypeswil(){
		$contacttype="Select * from tblContactTypeRef order by contactDesc";
		$res= $this->execQryI($contacttype);
		return $this->getArrResI($res);
	}

	function getcitywil($where=""){
		$city="Select * from tblCityRef $where order by cityDesc";
		$res= $this->execQryI($city);
		if ($this->getRecCountI($res) > 1)
			return $this->getArrResI($res);
		else	
			return $this->getSqlAssocI($res);
	}
		
	function createstrwil($length = "11") {
		  $code = md5(uniqid(rand(), true));
			  if ($length != "") return substr($code, 0, $length);
			  else return $code;
	}
	function getDesc($table,$field,$fieldvalue,$desc) {
		$qryDesc="Select $desc from $table where $field='$fieldvalue'";
		$res=$this->execQryI($qryDesc);
		$res=$this->getSqlAssocI($res);
		return $res;
	}
	function getlevelwil($rank,$and="") {
		$qrylevel="Select distinct level,('Level ' + convert(varchar, level)) as levelname from tblPosition where rank='$rank' and active='1' $and";
		$res=$this->execQryI($qrylevel);
		$res=$this->getArrResI($res);
		return $res;		
	}
	
	function getpositionwil($where,$act) {
		$sqlpos="Select * from tblPosition $where order by posDesc";
		$res=$this->execQryI($sqlpos);
		if ($act==1) {
			$res=$this->getArrResI($res);
		}	
		elseif ($act==2) {	
			$res=$this->getSqlAssocI($res);
		}
		return $res;		
	}	  
	 
	//function for Employee Address by nhomer
	function empAddress($empNo){
		$qryempAddress="SELECT tblEmpMast.compCode,tblEmpMast.empNo,tblEmpMast.empProvinceCd,tblEmpMast.empECPerson,
						tblEmpMast.empECNumber, tblEmpMast.empMunicipalityCd,tblMunicipalityRef.municipalityDesc,
						tblProvinceRef.provinceDesc,tblEmpMast.empDepCode,tblEmpMast.empSecCode,tblEmpMast.empPosId,
						tblEmpMast.empAddr1,tblEmpMast.empAddr2 
						FROM tblEmpMast 
						LEFT JOIN tblMunicipalityRef ON tblEmpMast.empMunicipalityCd = tblMunicipalityRef.municipalityCd 
						LEFT JOIN tblProvinceRef ON tblEmpMast.empProvinceCd = tblProvinceRef.provinceCd
						WHERE tblEmpMast.empNo='$empNo'";
		return $this->getArrResI($this->execQryI($qryempAddress));					
	}  
	
	
	//function for Employee Contacts  
	function empContactswil($empNo) {
		$qryempContacts="Select contactName,contactDesc from tblContactMast inner join 
						tblContactTypeRef on tblContactMast.contactCd = tblContactTypeRef.contactCd 
						where empNo='$empNo' order by contactDesc";
		return $this->getArrResI($this->execQryI($qryempContacts));		
	}
	
	function empOtherInfos($empNo,$compCode="") {
		if ($compCode == "") {
			$compCode = $_SESSION['company_code'];
		}
/* query before */		
/*		$qryempOtherInfos="SELECT tblCityRef.cityDesc, tblCitizenshipRef.citizenDesc, tblReligionRef.relDesc, 
					  tblPayBank.bankDesc, tblEmpMast.empNo,tblPosition.posShortDesc,empPayCat,
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
*/
		$qryempOtherInfos="SELECT tblEmpMast.empMidName,tblEmpMast.empFirstName,tblEmpMast.empLastName,
		tblMunicipalityRef.municipalityDesc,tblProvinceRef.provinceDesc, tblCitizenshipRef.citizenDesc, tblReligionRef.relDesc, 
		tblPayBank.bankDesc, tblEmpMast.empNo,tblPosition.posShortDesc,tblEmpMast.dateReg,empPayCat,tblEmpMast.empEndDate, tblEmpMast.empMrate,
		tblEmpMast.empDrate, tblPosition.posDesc,
							 	CASE empSex 
								  WHEN 'M' THEN 'Male'
								  WHEN 'F' then 'Female'
								END as empSex,
							 	CASE empMarStat
								  WHEN 'SG' THEN 'Single'
								  WHEN 'ME' THEN 'Married'
								  WHEN 'SP' THEN 'Separated'
								  WHEN 'WI' THEN 'Widow(er)'
								END as empMarStat,
							 	CASE empPayType
								  WHEN 'D' THEN 'Daily'
								  WHEN 'M' THEN 'Monthly'
								END as empPayType,
							 	CASE employmentTag
								  WHEN 'RG' THEN 'Regular'
								  WHEN 'PR' THEN 'Probationary'
								  WHEN 'CN' THEN 'Contractual'
								END as employmentTag,
							 	CASE empPayGrp
								  WHEN '1' THEN 'Group 1'
								  WHEN '2' THEN 'Group 2'
								END as empPayGrp,
							 	CASE empNOS
									WHEN 'RS' THEN 'Resigned'
									WHEN 'EOC' THEN 'End of contract'
									WHEN 'AWOL' THEN 'Absent without leave'
									WHEN 'TR' THEN 'Terminated for a cause'
								END as empNOS,													   
                      tblTeu.teuDesc, tblTimeShiftRef.shiftDesc, tblEmpMast.empStat, tblEmpMast.empAddr1, 
					  tblMunicipalityRef.municipalityDesc, tblProvinceRef.provinceDesc, tblEmpMast.dateHired, tblEmpMast.empSssNo, 
					  tblEmpMast.empTin, tblDepartment.deptDesc, tblEmpMast.compCode, tblEmpMast.empBrnCode, tblEmpMast.empLocCode
					  FROM tblEmpMast 
					  LEFT OUTER JOIN tblTimeShiftRef ON tblEmpMast.compCode = tblTimeShiftRef.compCode 
					  	AND tblEmpMast.empShiftId = tblTimeShiftRef.shiftId 
					  LEFT OUTER JOIN tblPosition ON tblEmpMast.empPosId = tblPosition.posCode 
					  	AND tblEmpMast.compCode = tblPosition.compCode 
					  LEFT OUTER JOIN tblTeu ON tblEmpMast.empTeu = tblTeu.teuCode 
					  LEFT OUTER JOIN tblPayBank ON tblEmpMast.compCode = tblPayBank.compCode 
					  	AND tblEmpMast.empBankCd = tblPayBank.bankCd 
					  LEFT OUTER JOIN tblReligionRef ON tblEmpMast.empReligion = tblReligionRef.relCd 
					  LEFT OUTER JOIN tblCitizenshipRef ON tblEmpMast.empCitizenCd = tblCitizenshipRef.citizenCd 
					  LEFT OUTER JOIN tblMunicipalityRef ON tblEmpMast.empMunicipalityCd = tblMunicipalityRef.municipalityCd 
					  LEFT OUTER JOIN tblProvinceRef on tblEmpMast.empProvinceCd=tblProvinceRef.provinceCd 
					  LEFT OUTER JOIN tblDepartment ON tblEmpMast.empDepCode=tblDepartment.deptCode 
					  	and tblEmpMast.empDiv=tblDepartment.divCode
					  WHERE tblEmpMast.empNo='$empNo' 
					  and tblEmpMast.compCode='$compCode' 
					  and tblEmpMast.empStat IN ('RG','PR','CN') 
					  and tblDepartment.deptLevel='2'";

		return $this->getSqlAssocI($this->execQryI($qryempOtherInfos));			  
	}	
	function getPayReason($compCode) {
		$qry = "Select * from tblPayReason where compCode='$compCode'";
		return $this->getArrResI($this->execQryI($qry));
	}
	function getPeriod($payPd) {
		$qryperiod = "Select pdSeries FROM tblPayPeriod WHERE (compCode = '{$_SESSION['company_code']}') AND (payGrp = {$_SESSION['pay_group']}) AND (payCat = {$_SESSION['pay_category']}) AND (pdStat = 'O')";
		$res = $this->getSqlAssocI($this->execQryI($qryperiod));
		if ($res['pdSeries'] == $payPd) {
			return true;
		} else {
			return false;
		}	
			
	}
	
	function getLoantrnCode($loanTypeCd) {
		$sqltrnCode = "Select trnApply from tblLoanType Inner Join tblPayTransType on tblLoanType.trnCode = tblPayTransType.trnCode where lonTypeCd=$loanTypeCd and tblLoanType.compCode='{$_SESSION['company_code']}' and tblPayTransType.compCode='{$_SESSION['company_code']}'";
		$res = $this->getSqlAssocI($this->execQryI($sqltrnCode));
		return $res['trnApply'];
		
	}
	function getOpenPeriodwil() {
		$compCode = $_SESSION['company_code'];
		$grp = $_SESSION['pay_group'];
		$cat = $_SESSION['pay_category'];
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdStat = 'O' AND 
					compCode = '$compCode' AND payGrp = '$grp' AND payCat = '$cat' ";
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}
	function getUnPostedLoans($arrPd,$arrLoan,$empNo){
		$qryLoans = "Select * from tblEmpLoansDtl where empNo='$empNo' AND compCode='".$_SESSION['company_code']."' 
					AND lonTypeCd='".$arrLoan['lonTypeCd']."' AND lonRefNo='".$arrLoan['lonRefNo']."'
					AND trnGrp='".$_SESSION['pay_group']."' AND trnCat='".$_SESSION['pay_category']."'
					AND pdNumber='".$arrPd['pdNumber']."' AND pdYear='".$arrPd['pdYear']."' AND dedTag='Y'";
		return $this->getArrResI($this->execQryI($qryLoans));	
		
	}
	function strUpper($text) {
		return strtoupper(str_replace("'","''",stripslashes(strtoupper($text))));
	}
	function getTransCode($cat) {
		$qry = "Select * from tblPayTransType where trnCat='$cat' and compCode='{$_SESSION['company_code']}'";
		return $this->getArrResI($this->execQryI($qry));	
	}	
	function arrRank($where,$act) {
		$array = array(0=>'');
		if ($act==1) {
			$qryRank = "Select rank from tblPosition $where";
			$arrRes = $this->getArrResI($this->execQryI($qryRank));
			foreach($arrRes as $val) {
				$rank = (int)$val['rank'];
				$array = array('',"$rank"=>"Rank $rank");
			}
		} else 	{
			$qryLevel = "Select level from tblPosition $where";
			$arrRes = $this->getArrResI($this->execQryI($qryLevel));
			foreach($arrRes as $val) {
				$level = $val['level'];
				$array= array('',"$level"=>"Level $level");
			}
		}
		return $array;
	}	
	/*Gen Function*/

	function getPayMonth($pdNum, $pdYear)
	{
		$qrypdMon = "SELECT     date_format(pdPayable,'%m') as pdPayable
					FROM         tblPayPeriod
					WHERE     (payGrp = '".$_SESSION["pay_group"]."') AND (payCat = '".$_SESSION["pay_category"]."') AND (pdYear = '".$pdYear."') 
					AND (pdNumber IN ($pdNum))
					group by date_format(pdPayable,'%m') ;
					";
		$resMon = $this->execQryI($qrypdMon);	
		$arrMon =  $this->getSqlAssocI($resMon);
		return $arrMon["pdPayable"];
	}

	
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
		$resEmp = $this->execQryI($qryEmpList);
		$arrEmpList = $this->getArrResI($resEmp);
		foreach($arrEmpList as $arrEmpList_val)
		{
			$empNoList.=$arrEmpList_val["empNo"].",";
		}
		$empNoList = substr($empNoList,0,strlen($empNoList) - 1);
		return $empNoList;
	}
	
	
	
	function getBrnCodes($ArrEmp)
	{
		foreach($ArrEmp as $arrEmpList_val)
		{
			$empNoList.=$arrEmpList_val["empNo"].",";
		}
		$empNoList = substr($empNoList,0,strlen($empNoList) - 1);
		
		$qryBrnCodes = "Select empBrnCode, brnch.brnDesc as brn_Desc, empLocCode, brnLoc.brnDesc as brn_DescLoc from tblEmpMast empMast, tblBranch brnch, tblBranch brnLoc 
						where empMast.empBrnCode=brnch.brnCode and empMast.empLocCode=brnLoc.brnCode
						and empNo in ($empNoList) 
						group by empLocCode, empBrnCode, brnch.brnDesc,  brnLoc.brnDesc order by brnch.brnDesc, brnLoc.brnDesc ";
		
		$resBranch = $this->execQryI($qryBrnCodes);
		$resBranch = $this->getArrResI($resBranch);
		return $resBranch;
	}
	
	function getArrEmpList($resEmp)
	{	
		$arrEmpList = $this->getArrResI($resEmp);
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
	
	function getCalendarDays($frDate, $toDate)
	{
		$dateDiff_frDate = gregoriantojd (date("m", strtotime($frDate)),date("d", strtotime($frDate)),date("Y", strtotime($frDate)));
		$dateDiff_toDate = gregoriantojd (date("m", strtotime($toDate)),date("d", strtotime($toDate)),date("Y", strtotime($toDate)));
		
		$dateDiff = $dateDiff_toDate - $dateDiff_frDate;
		
		return $dateDiff;
	}

	/*Determine the DayType of the Date*/
	function detDayType($tranDate, $where)
	{
		$qrydetDayType = "Select * from tblHolidayCalendar
							where compCode='".$_SESSION["company_code"]."' and holidayDate='".$tranDate."'  and holidayStat='A'";
		$qrydetDayType.= ($where!=""?$where:"");
		$resdetDayType = $this->execQryI($qrydetDayType);
		return $this->getSqlAssocI($resdetDayType);
	}
	
	function checkModuleAccess() {
		if (strpos($_SERVER['PHP_SELF'],'modules') != 0) {
			if (strpos($_SERVER['PHP_SELF'],'payroll') != 0) {
				$tbl = "tblPayrollMenu";
				$field = "pagesPayroll";
			} elseif (strpos($_SERVER['PHP_SELF'],'201') != 0) {
				$tbl = "tbl201Menu";
				$field = "Pages201";
			} else {
				$tbl = "tblTmeInAttendanceMenu";
				$field = "pagesTNA";
			}
			$module = substr($_SERVER['PHP_SELF'],strpos($_SERVER['PHP_SELF'],'modules'));
			 $sqlModule = "Select moduleId from $tbl where page='$module'";
			$arrModInfo =  $this->getSqlAssocI($this->execQryI($sqlModule));
			$sqlUserAccess = "Select $field from tblUsers where userId='{$_SESSION['user_id']}' and compCode='{$_SESSION['company_code']}'";
			$arrUserAccess =  $this->getSqlAssocI($this->execQryI($sqlUserAccess));
			if ($arrModInfo['moduleid'] !="") {
				if (!in_array($arrModInfo['moduleId'],explode(",",$arrUserAccess[$field]))) {
					header("Location: ../../../accessdenied.html");
				}
			}
		}	
	}


	function getDeptDescGen($compCode,$empDiv,$empDept){
		$qryGetDept = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND deptLevel = 2 
						 AND divCode = '{$empDiv}'
						 AND deptCode = '{$empDept}'
						 AND deptStat = 'A'
					     ORDER BY deptDesc ASC";
		
		$resGetDept = $this->execQryI($qryGetDept);
		return $this->getSqlAssocI($resGetDept);
	}

	function getBranchByCompGrp($where)	{
		$qrybrnch = "Select * from tblBranch where brnStat = 'A' ".$where." order by brnDesc";
		$resGetBrn = $this->execQryI($qrybrnch);
		return $this->getArrResI($resGetBrn);
	}	
	
	function getAllBranch()
	{
		$qrybrnch = "Select * from tblBranch where brnStat = 'A' order by brnDesc";
		$resGetBrn = $this->execQryI($qrybrnch);
		return $this->getArrResI($resGetBrn);
	}

	
	function getEmpInfo($empNo)
	{
		$qryEmpInfo = "Select * from tblEmpMast where empNo='".$empNo."'";
		$resEmpInfo = $this->execQryI($qryEmpInfo);
		return $this->getSqlAssocI($resEmpInfo);
	}
	
	function Space($num,$char=' ')
	{
		$sp = '';
		for($i=0; $i<$num; $i++)
			$sp .= $char;
	
		return $sp;
	}
	
	function WriteFile($file_name, $str_path, $file_cont)
	{
		$fh = fopen($str_path.'/'.$file_name, 'w') or die('can not write file!');
		fwrite($fh, $file_cont);
		fclose($fh);
	}
	
	function getPeriodWil($compCode,$groupType,$catType,$andCondition) {
		 $qry = "SELECT compCode, pdStat, date_format(pdPayable,'%Y-%m-%d') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE compCode = '$compCode' AND 
					payGrp = '$groupType' AND 
					payCat = '$catType' ";
		 if($andCondition != ""){
		 	$qry .= $andCondition;
		 }
		$res = $this->execQryI($qry);
		if($this->getRecCountI($res) > 1){
			return $this->getArrResI($res);
		}
		else{
			return $this->getSqlAssocI($res);
		}
	}
	
	function getBrnchInfo($brnCode){
		$qryGetBranch = "Select * from tblBranch where  
					  brnStat='A'  and brnCode='".$brnCode."'";
		$resGetBranch = $this->execQryI($qryGetBranch);
		return $this->getSqlAssocI($resGetBranch);
		
		
	}
	
	function EmpStat($stat) {
		switch($stat) {
			case "RG":
				return "Regular";
			break;
			case "PR":
				return "Probationary";
			break;
			case "CN":
				return "Contractual";
			break;
			case "RS":
				return "Resigned";
			break;
			case "TR":
				return "Terminated";
			break;
			case "IN":
				return "Inactive";
			break;
			case "AP":
				return "Applicant";
			break;	
			case "EOC":
				return "End of Contract";
			break;						
			case "AWOL":
				return "AWOL";
			break;						
		}	
	}
	
	function EmpNOS($stat) {
		switch($stat) {
			case "RS":
				return "Resigned";
			break;
			case "TR":
				return "Terminated for a cause";
			break;
			case "IN":
				return "Inactive";
			break;
			case "AP":
				return "Applicant";
			break;	
			case "EOC":
				return "End of Contract";
			break;						
			case "AWOL":
				return "Absent without leave";
			break;						
		}	
	}
	
	function getNatures($nature){
		$qryGetNatures = "Select * from tblNatures where natureStat='A'  and natureCode='".$nature."'";
		$resGetNatures = $this->execQryI($qryGetNatures);
		$res = $this->getSqlAssocI($resGetNatures);
		return $res['Description'];
	}
	
	function setNatures($natureCode){
		switch($natureCode){
			case "1":
				return "AWOL";
			break;
			case "2":
				return "EOC";
			break;	
			case "3":
				return "RS";
			break;	
			case "4":
				return "IN";
			break;
			case "5":
				return "TR";
			break;		
		}	
	}	
	
	function EmpCivilStat($stat){
		switch($stat){
			case "SG":
				return "SINGLE";
			break;
			case "ME":
				return "MARRIED";
			break;
			case "WI":
				return "WIDOW(ER)";
			break;				
		}	
	}
	
	function EmpTEU($stat){
		switch($stat){
			case "S":
				return "SINGLE";
			break;
			case "M";
				return "MARRIED";
			break;
			case "M1":
				return "MARRIED W/ 1 DEPENDENT";
			break;
			case "M2":
				return "MARRIED W/ 2 DEPENDENT";
			break;
			case "M3":
				return "MARRIED W/ 3 DEPENDENT";
			break;
			case "M4":
				return "MARRIED W/ 4 DEPENDENT";
			break;
			case "S1":
				return "SINGLE  W/ 1 DEPENDENT";
			break;
			case "S2":
				return "SINGLE  W/ 2 DEPENDENT";
			break;
			case "S3":
				return "SINGLE  W/ 3 DEPENDENT";
			break;
			case "S4":
				return "SINGLE  W/ 4 DEPENDENT";
			break;
			case "Z":
				return "ZERO EXEMPTION";
			break;												
		}	
	}
		
	function getRank($rank) {
		$sqlRank = "Select * from tblRankType where compCode='{$_SESSION['company_code']}' AND rankStat='A' AND rankCode='$rank'";
		return $this->getSqlAssocI($this->execQryI($sqlRank));		
	}
	function getProcGrp() {
		$sqlGrp = "Select payGrp from tblProcGrp where compCode='{$_SESSION['company_code']}' and status='A'";
		$res = $this->getSqlAssocI($this->execQryI($sqlGrp));		
		return $res['payGrp'];
		
	}
	
	//added by Nhomer Cabico
	//added function for province query 
	function getProvince(){
		$sqlProvince="Select provinceCd,provinceDesc from tblProvinceRef order by provinceDesc";
		$res=$this->execQryI($sqlProvince);
		if($this->getRecCountI($res)>1)
				return $this->getArrResI($res);
		else
				return $this->getSqlAssocI($res);
	}
	
	//added function for municipality and or city
	function getMunicipality($where=""){
		$sqlMunicipality="Select municipalityCd,municipalityDesc,provinceCd from tblmunicipalityRef $where order by municipalityDesc";
		$res=$this->execQryI($sqlMunicipality);
		return $this->getArrResI($res);
//		if($this->getRecCountI($res)>1)
//			return $this->getArrResI($res);
//		else
//			return $this->getSqlAssocI($res);		
	}
	//added function DefLookUp
	function getDefLookup($wheredef=""){
		if($wheredef!=""){
			$wheredef=$wheredef;
			}
		else{
			$wheredef="";
			}	
		$sqlIncentive="Select seqId,type,typeDesc from tblUserDefLookUp $wheredef order by typeDesc";
		$res=$this->execQryI($sqlIncentive);
		if($this->getRecCountI($res)>1)
			return $this->getArrResI($res);
		else
			return $this->getSqlAssocI($res);	
	}
	
	function recordChecker($sqlQry){
		$resultChecker=$this->execQryI($sqlQry);
		if($this->getRecCountI($resultChecker)>0)
			return true;
		else
			return false;		
	}

	//function added by nhomer
	function getEducationalBackground($empNo){
		$qryEduc="SELECT tblEducationalBackground.type,tblEducationalBackground.schoolId,
					tblEducationalBackground.dateStarted,tblEducationalBackground.dateCompleted,
					tblEducationalBackground.empNo,tblEducationalBackground.catCode,
					tblEducationalBackground.licenseNumber,tblEducationalBackground.licenseName,
					tblEducationalBackground.dateIssued, tblEducationalBackground.dateExpired, 
					tblUserDefLookUp.typeDesc, tblUserDefLookUp_1.typeDesc AS schoolType 
					FROM tblEducationalBackground 
					INNER JOIN tblUserDefLookUp ON tblEducationalBackground.schoolId = tblUserDefLookUp.seqId 
					INNER JOIN tblUserDefLookUp tblUserDefLookUp_1 ON tblEducationalBackground.type = tblUserDefLookUp_1.seqId
					WHERE tblEducationalBackground.empNo='{$empNo}'";
		return $this->execQryI($qryEduc);
		}
	
	function getEmploymentHistory($empNo){
		$qryEmployment="SELECT employeeDataId,companyName,employeePosition,startDate,endDate,empNo,catCode 
						FROM tblEmployeeDataHistory
						WHERE empNo='{$empNo}'";
		return $this->execQryI($qryEmployment);				
		}
		
	function getDisciplinary($empNo){
		$qryDisciplinary="SELECT tblDisciplinaryAction.da_Id, 
					  tblDisciplinaryAction.date_commit, 
					  tblDisciplinaryAction.date_serve, 
					  tblDisciplinaryAction.article_id, 
                      tblDisciplinaryAction.section_id, 
					  tblDisciplinaryAction.offense, 
					  tblDisciplinaryAction.sanction, 
					  tblDisciplinaryAction.suspensionFrom, 
                      tblDisciplinaryAction.suspensionTo, 
					  tblDisciplinaryAction.empNo, 
					  tblDisciplinaryAction.catCode, 
					  tblArticle.sections, 
					  tblArticle.violation
					  FROM tblDisciplinaryAction 
					  INNER JOIN tblArticle ON tblDisciplinaryAction.section_id = tblArticle.article_Id
					  WHERE tblDisciplinaryAction.empNo='{$empNo}'";
		return $this->execQryI($qryDisciplinary);				
		}	
		
	function getArticle($sqlQry){
		$qry=$this->execQryI($sqlQry);
		return $this->getArrResI($qry);
		}	
	
	function getPerformance($empNo){
		$qryPerformance="SELECT performanceFrom, 
					performanceTo, 
					performanceNumerical, 
					performanceAdjective, 
					performancePurpose, 
					empNo, 
					compCode,
					old_empDrate,
					new_empDrate,
					remarks
					FROM tblPerformance
					where empNo='{$empNo}'";
		return $this->execQryI($qryPerformance);
		}	
	function getTrainings($empNo){
		$qryTrainings="SELECT trainingFrom, 
					trainingTo, 
					trainingTitle, 
					trainingCost, 
					trainingBond, 
					effectiveFrom, 
					effectiveTo, 
					empNo 
					FROM tblTrainings
					where empNo='{$empNo}'";
		return $this->execQryI($qryTrainings);
	}

	function empProvince($val){
		$qry="Select provinceCd,provinceDesc from tblProvinceRef where provinceCd='{$val}'";
		$res=$this->getSqlAssocI($this->execQryI($qry));
		return $res['provinceDesc'];	
	}
	
	function empMunicipality($val){
		$qry="Select municipalityCd,municipalityDesc from tblmunicipalityRef where municipalityCd='{$val}'";
		$res=$this->getSqlAssocI($this->execQryI($qry));
		return $res['municipalityDesc'];	
	}	

	function empAllowanceDaily($val){
		$qrys="SELECT * FROM tblAllowance where empNo='{$val}' and allowStat='A' and allowTag='D' and allowCode='7' and allowPayTag='P'";
		$ress=$this->getArrResI($this->execQryI($qrys));
		$dailyvalues=0;
		$dailyamnts=0;
		foreach($ress as $resVals=>$valuess){
			$dailyamnts=26*$valuess['allowAmt'];	
			$dailyvalues=$dailyvalues+$dailyamnts;
		}
		return $dailyvalues;
	}
	
	function empAllowanceMonthly($val){
		$qry="SELECT * FROM tblAllowance where empNo='{$val}' and allowStat='A' and allowTag='M' and allowCode='2' and allowPayTag='P'";
		$res=$this->getArrResI($this->execQryI($qry));
		$monthlyvalue=0;
		foreach($res as $resVal=>$values){
			$monthlyvalue=$monthlyvalue+$values['allowAmt'];	
		}
		return $monthlyvalue;
	}
	
	function getBranchGroupName($compcode){
		$sql="Select * from tblMinGroup where compCode='{$compcode}' and stat='A'";
		$res=$this->getArrResI($this->execQryI($sql));
		return $res;	
	}
	
	function getBranchGroup($where){
		if($where!=""){
			$where=$where;	
		}	
		else{
			$where="";	
		}
		$sql="SELECT tblBranchMinimumGroup.branchMinimumGroupID, tblBranchMinimumGroup.minGroupID, 
				tblBranchMinimumGroup.brnCode, tblBranch.compCode, tblBranch.brnDesc 
			  FROM tblBranchMinimumGroup 
			  INNER JOIN tblBranch ON tblBranchMinimumGroup.brnCode = tblBranch.brnCode $where";
		$res=$this->getArrResI($this->execQryI($sql));
		return $res;
	}	
	
	function getTranspoAllowance($empno){
		$sql="SELECT  empNo, SUM(allowAmt) AS amnt FROM tblAllowance WHERE (allowCode = '12') and empNo= $empno GROUP BY empNo";
		$res =$this->getSqlAssocI($this->execQryI($sql));
		return $res;	
	}
	
	function empCOEInfos($empNo,$compCode="") {
		if ($compCode == "") {
			$compCode = $_SESSION['company_code'];
		}
		$qrycoeinfos="SELECT tblEmpMast.empMidName,tblEmpMast.empFirstName,tblEmpMast.empLastName,
		tblEmpMast.empBrnCode,tblEmpMast.dateHired,tblEmpMast.dateResigned, tblEmpMast.endDate,
		tblMunicipalityRef.municipalityDesc,tblProvinceRef.provinceDesc, tblCitizenshipRef.citizenDesc, tblReligionRef.relDesc, 
		tblPayBank.bankDesc, tblEmpMast.empNo,tblPosition.posShortDesc,tblEmpMast.dateReg,empPayCat,tblEmpMast.empEndDate, tblEmpMast.empMrate,
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
							employmentTag = 
								CASE employmentTag
								  WHEN 'RG' THEN 'Regular'
								  WHEN 'PR' THEN 'Probationary'
								  WHEN 'CN' THEN 'Contractual'
								END,
							empPayGrp = 
								CASE empPayGrp
								  WHEN '1' THEN 'Group 1'
								  WHEN '2' THEN 'Group 2'
								END,											   
                      tblTeu.teuDesc, tblTimeShiftRef.shiftDesc, tblEmpMast.empStat, tblEmpMast.empAddr1, 
					  tblMunicipalityRef.municipalityDesc, tblProvinceRef.provinceDesc, tblEmpMast.empMRate, tblEmpMast.empSssNo, 
					  tblEmpMast.empTin, tblEmpMast.empDiv, tblEmpMast.empDepCode
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
                      tblMunicipalityRef ON tblEmpMast.empMunicipalityCd = tblMunicipalityRef.municipalityCd LEFT OUTER JOIN tblProvinceRef on tblEmpMast.empProvinceCd=tblProvinceRef.provinceCd
					  WHERE tblEmpMast.empNo='$empNo' and tblEmpMast.compCode='$compCode'";

		return $this->getSqlAssocI($this->execQryI($qrycoeinfos));			  
	}	

	function getSeparatedEmployees($empno){
		$qry = "Select * from tblSeparatedEmployees where empNo='{$empno}'";
		return $this->getSqlAssocI($this->execQryI($qry));			  
	}
	
	function getEmpShift($empno){
		$qry = "Select * from tblTK_EmpShift where empNo='{$empno}'";
		return $this->getSqlAssocI($this->execQryI($qry));			  
	}
	
	function sqlQry($where){
		if($where!=""){
			$where=$where;	
		}	
		else{
			$where="";	
		}
		$sql=$wher;
		$res=$this->getArrResI($this->execQryI($sql));
		return $res;
	}	
}
?>