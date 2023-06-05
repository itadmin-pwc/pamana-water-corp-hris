<?
//class for file maintenance
class maintenanceObj extends commonObj {
	var $all;
//function for Minimum wage maintenance	
	function minimumWage($act,$Array){
		$trns=$this->beginTran();
			if($Array['txtMinimumWage_Old']==""){
				$oldmin=0;	
			}
			else{
				$oldmin=$Array['txtMinimumWage_Old'];	
			}
			
			if($Array['txtecola_Old']==""){
				$oldcola=0;	
			}
			else{
				$oldcola=$Array['txtecola_Old'];	
			}		
		switch($act){
			
			case "Add":
				if($Array['cmbbranch']==0){
					$qry=$this->getBranchGroup(" where minGroupID='{$Array['cmbbranchgroup']}' and compCode='{$_SESSION['company_code']}'");
					foreach($qry as $sqlval=>$values){	
						$qryMinimumWage.="Insert into tblMinimumWage (compCode,brnCode,minimumWage_New,eCola_New,effectiveDate,
											stat,userCreated,dateAdded,minimumWage_Old,eCola_Old,minGroupID)
										  values('{$_SESSION['company_code']}','{$values['brnCode']}','{$Array['txtMinimumNew']}',
										  	'{$Array['txtecolanew']}','{$Array['txtEffectiveDate']}','A','{$_SESSION['user_id']}',
											'".date("m/d/Y")."','{$oldmin}','{$oldcola}','{$Array['cmbbranchgroup']}');";
					}
				}
				else{
						$qryMinimumWage="Insert into tblMinimumWage (compCode,brnCode,minimumWage_New,eCola_New,effectiveDate,
											stat,userCreated,dateAdded,minimumWage_Old,eCola_Old,minGroupID)
										 values('{$_SESSION['company_code']}','{$Array['cmbbranch']}','{$Array['txtMinimumNew']}',
										 	'{$Array['txtecolanew']}','{$Array['txtEffectiveDate']}','A','{$_SESSION['user_id']}',
											'".date("m/d/Y")."','{$oldmin}','{$oldcola}','{$Array['cmbbranchgroup']}')";					
				}	
			break;
			
			case "Edit":
				$qryMinimumWage="Update tblMinimumWage set brnCode='{$Array['cmbbranch']}',
									minimumWage_New='{$Array['txtMinimumNew']}',eCola_New='{$Array['txtecolanew']}',
									effectiveDate='{$Array['txtEffectiveDate']}',stat='A',
									userUpdated='{$_SESSION['user_id']}',dateUpdated='".date("m/d/Y")."',
									minimumWage_Old='{$Array['txtMinimumWage_Old']}',eCola_Old='{$Array['txtecola_Old']}' 
								where minimumWageId='{$Array['mCode']}'";
			break;
		}	
		if($trns){
			$trns=$this->execQry($qryMinimumWage);
			}
		if(!$trns){
			$trns=$this->rollbackTran();
			return false;
			}	
		else{
			$trns=$this->commitTran();
			return true;
			}	
	}
	
	function checkMinimumWage(){
		
	}
	
	function getMinimumWage($minimumwageid){
		$qryminimum="Select * from tblMinimumWage where minimumWageId='{$minimumwageid}'";
		return $this->getSqlAssoc($this->execQry($qryminimum));	
	}	
	
	function getBranchMinimumWage($brncode){
		$qrybranchminimum="Select * from tblBranch where brnCode='{$brncode}'";
		return $this->getSqlAssoc($this->execQry($qrybranchminimum));	
	}
	
	function getGroupBranchMinimumWage($grpcode){
		$qrygroupbranchminimum="SELECT TOP 1 dbo.tblBranchMinimumGroup.brnCode, dbo.tblBranchMinimumGroup.minGroupID, 
								tblBranch.minWage, tblBranch.ecola 
								FROM  dbo.tblBranchMinimumGroup 
								INNER JOIN  tblBranch 
								ON dbo.tblBranchMinimumGroup.brnCode = tblBranch.brnCode 
								WHERE dbo.tblBranchMinimumGroup.minGroupID = '{$grpcode}'";
		return $this->getSqlAssoc($this->execQry($qrygroupbranchminimum));	
	}
	
//Function for Article maintenance	
	function Article($act,$Array){
		$trns=$this->beginTran();
		switch($act){
			case "Add":
				$qryArticle="Insert into tblArticle (article,sections,violation,user_added,
								date_added,compCode,stat)
							 values('".str_replace("'","''",strtoupper($Array['txtarticle']))."',
							 	'".str_replace("'","''",strtoupper($Array['txtsection']))."',
								'".str_replace("'","''",strtoupper($Array['txtviolation']))."',
								'{$_SESSION['user_id']}','".date("m/d/Y")."','{$_SESSION['company_code']}','{$Array['cmbStat']}')";
			break;
			case "Edit":
				$qryArticle="Update tblArticle set article='".str_replace("'","''",strtoupper($Array['txtarticle']))."',
								sections='".str_replace("'","''",strtoupper($Array['txtsection']))."',
								violation='".str_replace("'","''",strtoupper($Array['txtviolation']))."',
								stat='".$Array['cmbStat']."' 
							where compCode='{$_SESSION['company_code']}' and article_Id='{$Array['txtarticleid']}'" ;
			break;
		}	
		if($trns){
			$trns=$this->execQry($qryArticle);
			}	
		if(!$trns){
			$trns=$this->rollbackTran();
			return false;
			}	
		else{
			$trns=$this->commitTran();
			return true;
			}	
	}
//Function for Bank maintenance	
	function Bank($act,$Array){
		switch($act) {
			case "Add":
				$bankCd = $this->getBankCd();
				$qrybank = "Insert into tblPayBank (compCode, bankCd, bankDesc, bankBrn, bankAddr1, 
								bankAddr2, bankAddr3, bankStat) 
							values ('{$_SESSION['company_code']}','$bankCd',
								'".str_replace("'","''",stripslashes(strtoupper($Array['txtbank'])))."',
								'".$this->strUpper($Array['txtbranch'])	."','".$this->strUpper($Array['txtadd1'])."',
								'".$this->strUpper($Array['txtadd2'])."','".$this->strUpper($Array['txtadd3'])."',
								'{$Array['cmbStat']}')";
			break;
			case "Edit":
				$qrybank = "Update tblPayBank set bankDesc = '".$this->strUpper($Array['txtbank'])."', 
								bankBrn = '".$this->strUpper($Array['txtbranch'])."', 
								bankAddr1 = '".$this->strUpper($Array['txtadd1'])."', 
								bankAddr2 = '".$this->strUpper($Array['txtadd2'])."', 
								bankAddr3 = '".$this->strUpper($Array['txtadd3'])."', 
								bankStat = '{$Array['cmbStat']}' 
							where compCode='{$_SESSION['company_code']}' and bankCd='{$Array['txtbankCd']}'";
			break;
			case "Delete":
				$qrybank = "Delete from tblPayBank where compCode='{$_SESSION['company_code']}' and bankCd='{$Array['bankCd']}'";			
			break;
		}
		return $this->execQry($qrybank);
	}
	
//Function to show Branch Group	depends on company used
	function getBrnGrp(){
		$qryGrp = "Select * from tblBranchGrp where compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qryGrp);
		return $this->getArrRes($res);
	}	
//Function to show Branch depends on company used and filtered by branch group
	function getBranchperGrp($grp,$brnchCode=0) {
		if ($brnchCode !=0) {
			$filterBranch = " and brnCode='$brnchCode'";
		}
		$qrybranches = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' and GrpCode='$grp' $filterBranch";
		$res = $this->execQry($qrybranches);
		return $this->getArrRes($res);
	}
	
//Function to get the numeric last value of	bank
	function getBankCd() {
		$qryCode = "Select max(bankCd)+1 as bankCd from tblPayBank where compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qryCode);
		$res = $this->getSqlAssoc($res);
		return $res['bankCd'];
	}
//Function to get the numeric last value of company
	function getcompCd() {
		$qryCode = "Select max(compCode)+1 as compCode from tblCompany";
		$res = $this->execQry($qryCode);
		$res = $this->getSqlAssoc($res);
		return $res['compCode'];
	}
//Function to show Article filtered by Article Id and Company Code used		
	function getArticle($articleId){
		$qryArticle="Select * from tblArticle where article_Id='{$articleId}' and compCode='{$_SESSION['company_code']}'";
		$res=$this->execQry($qryArticle);
		return $this->getSqlAssoc($res);	
	}
	
	function getbankInfo($bankCd) {
		$qrybank = "Select * from tblPayBank where bankCd='$bankCd' and compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qrybank);
		return $this->getSqlAssoc($res);
		
	}
	function getDayType() {
		$qry = "SELECT * FROM tblDayType
					     WHERE dayStat = 'A' AND dayType IN (3,4,7)";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}

	function Holiday($act,$Array){
		switch($act) {
			case "Add":
				if ($Array['cmbGrp'] ==0) {
					if ($Array['cmbbranch'] ==0) {
						$qryholiday = "Insert into tblHolidayCalendar (compCode, holidayDate, brnCode, holidayDesc, 
										dayType, holidayStat, createdBy,dateCreated)
									   values ('{$_SESSION['company_code']}','".date('Y-m-d',strtotime($Array['txtdate']))."',
									   	'0','".$this->strUpper($Array['txtdesc'])."','{$Array['cmbday']}','{$Array['cmbStat']}', 
										'".$_SESSION['employee_number']."', '".date("m/d/Y")."');";					
					} else {
						$qryholiday = "Insert into tblHolidayCalendar (compCode, holidayDate, brnCode, holidayDesc, 
										dayType, holidayStat, createdBy,dateCreated) 
									   values ('{$_SESSION['company_code']}','".date('Y-m-d',strtotime($Array['txtdate']))."',
									   	'{$Array['cmbbranch']}','".$this->strUpper($Array['txtdesc'])."','{$Array['cmbday']}',
										'{$Array['cmbStat']}','".$_SESSION['employee_number']."', '".date("m/d/Y")."');";					
					}
				} else {
					$ArrBranches = $this->getBranchperGrp($Array['cmbGrp'],$Array['cmbbranch']);
					foreach($ArrBranches as $valbranch) {
						$qryholiday .= "Insert into tblHolidayCalendar (compCode, holidayDate, brnCode, holidayDesc, 
											dayType, holidayStat, createdBy,dateCreated) 
										values ('{$_SESSION['company_code']}','".date('Y-m-d',strtotime($Array['txtdate']))."',
											'{$valbranch['brnCode']}','".$this->strUpper($Array['txtdesc'])."',
											'{$Array['cmbday']}','{$Array['cmbStat']}', '".$_SESSION['employee_number']."', 
											'".date("m/d/Y")."');";
					}							
				} 	
			break;
			case "Edit":
				 $qryholiday = "Update tblHolidayCalendar set holidayDate = '".date('Y-m-d',strtotime($Array['txtdate']))."', 
				 					holidayDesc = '".$this->strUpper($Array['txtdesc'])."', dayType = '{$Array['cmbday']}', 
									holidayStat = '{$Array['cmbStat']}', updatedBy='".$_SESSION['employee_number']."', 
									dateUpdated='".date("m/d/Y")."' 
								where compCode='{$_SESSION['company_code']}' and seqno='{$Array['txtseqno']}'";
			break;
			case "Delete":
				$qryholiday = "Delete from tblHolidayCalendar where compCode='{$_SESSION['company_code']}' 
								and seqno='{$Array['seqno']}'";			
			break;
		}
		
		return $this->execQry($qryholiday);
	}
	function getHolidayInfo($seqno) {
		$qryholiday = "Select * from tblHolidayCalendar where seqno='$seqno' and compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qryholiday);
		return $this->getSqlAssoc($res);
	}	
	function getCompanyInfo($compCode) {
		$qryInfo = "Select *,CASE compStat
								  WHEN 'A' THEN 'Active'
								  WHEN 'D' THEN 'Deleted'
								  WHEN 'H' THEN 'Held'
								END as status from tblCompany where compCode='$compCode'";
		$res = $this->execQry($qryInfo);
		return $this->getSqlAssoc($res);
	}
	function Company($act,$Array){
		switch($act) {
			case "Add":
				$compCode = $this->getcompCd();
				$qrycompany = "Insert into tblCompany (compCode, compName, compShort, compAddr1, compAddr2, 
								compTin, compSssNo, compPagibig, compPHealth, compNoDays, compEarnRetain, nonTaxBonus, 
								compPaySign, compStat, gLCode) 
							   values ('$compCode','".$this->strUpper($Array['txtname'])."',
							   	'".$this->strUpper($Array['txtshortname'])."','".$this->strUpper($Array['txtadd1'])."',
								'".$this->strUpper($Array['txtadd2'])."','{$Array['txttin']}','{$Array['txtsss']}',
								'{$Array['txthdmf']}','{$Array['txtphil']}','".(int)$Array['txtdays']."',
								'".(int)$Array['txtretention']."','".(int)$Array['txtbonus']."',
								'".$this->strUpper($Array['txtpaysign'])."','{$Array['cmbStat']}','{$Array['txtglcode']}')";
			break;
			case "Edit":
				$qrycompany = "Update tblCompany set compName='".$this->strUpper($Array['txtname'])."', 
								compShort='".$this->strUpper($Array['txtshortname'])."', 
								compAddr1='".$this->strUpper($Array['txtadd1'])."', 
								compAddr2='".$this->strUpper($Array['txtadd2'])."', 
								compTin='{$Array['txttin']}', compSssNo='{$Array['txtsss']}', 
								compPagibig='{$Array['txthdmf']}', compPHealth='{$Array['txtphil']}', 
								compNoDays='".(int)$Array['txtdays']."', compEarnRetain='".(int)$Array['txtretention']."',
								nonTaxBonus='".(int)$Array['txtbonus']."', compPaySign='".$this->strUpper($Array['txtpaysign'])."',
								compStat='{$Array['cmbStat']}', gLCode='{$Array['txtglcode']}' where compCode='{$Array['compCode']}'";
			break;
			case "Delete":
				$qrycompany = "Delete from tblCompany where compCode='{$Array['compCode']}'";			
			break;
		}
		return $this->execQry($qrycompany);
	}
	
	function getUserName() {
		$qryUser = "SELECT tblEmpMast.empLastName, tblEmpMast.empFirstName, tblUsers.userId, tblEmpMast.empMidName 
					FROM tblUsers 
					INNER JOIN tblEmpMast ON tblUsers.compCode = tblEmpMast.compCode AND tblUsers.empNo = tblEmpMast.empNo";
		$res = $this->execQry($qryUser);
		return $this->getArrRes($res);
	}
	
	function displayUserName($arrUser,$userid) {
		foreach($arrUser as $valuser) {
			
			if ($valuser['userId'] == $userid) {
				$uname = str_replace("Ñ","&Ntilde;",$valuser['empLastName'] . " " . $valuser['empFirstName'][0] . ". ". $valuser['empMidName'][0] . ". ");
			}
		}
		return $uname;
	}
	
	
	function checkPeriod($Year) {
		$qryPeriod = "Select pdYear  from tblPayPeriod 
					  where compCode='{$_SESSION['company_code']}' and pdYear='$Year' and payGrp='{$_SESSION['pay_group']}'";
		return $this->getRecCount($this->execQry($qryPeriod));
	}
	function getMaxpdYear() {
		$qrypdYear = "Select max(pdYear) as pdYear from tblPayPeriod 
					  where compCode='{$_SESSION['company_code']}' and payGrp='{$_SESSION['pay_group']}'";
		$res = $this->getSqlAssoc($this->execQry($qrypdYear));
		return $res['pdYear'];

	}
	function Generate($Year) {
		$maxYear = $this->getMaxpdYear();
		$dtYear = $Year -$maxYear;
		$qryyear = "Insert into tblPayPeriod (compCode, payGrp, payCat, pdYear, pdNumber, pdPayable, pdFrmDate, pdToDate, 
						pdTsTag, pdLoansTag, pdEarningsTag, pdProcessTag, pdProcessDate, pdProcessedBy, pdDateClosed, 
						pdClosedBy, pdStat)
					Select compCode, payGrp, payCat, $Year, pdNumber, DATEADD(Year,$dtYear,pdPayable),
						DATEADD(Year,$dtYear,pdFrmDate), DATEADD(Year,$dtYear,pdToDate), NULL, NULL, NULL, NULL, NULL, NULL, 
						NULL, NULL, 'H' from tblPayPeriod 
					where compCode='{$_SESSION['company_code']}' and payGrp='{$_SESSION['pay_group']}' and pdYear=$maxYear";
		return $this->execQry($qryyear);
	}
	function DayType($act,$Array){
		switch($act) {
			case "Add":
			
				$dtype = $this->getMaxDayType();
				$qryDay = "Insert into tblDayType (dayType, dayTypeDesc,dayStat) 
						   values ('$dtype','".$this->strUpper($Array['txtdesc'])."','{$Array['cmbStat']}')";
			break;
			case "Edit":
				$qryDay = "Update tblDayType set dayTypeDesc = '".$this->strUpper($Array['txtdesc'])."', 
							dayStat = '{$Array['cmbStat']}' 
						   where  dayType='{$Array['txtdaytype']}'";
			break;
		}
		return $this->execQry($qryDay);
	}	
	
	function getMaxDayType() {
		$qryday = "Select max(dayType) as dayType from tblDayType";
		$res = $this->getSqlAssoc($this->execQry($qryday));
		$dtype = $res['dayType'] + 1;
		if (strlen($dtype) == 1) {
			return '0' . $dtype;
		} else {
			return $dtype;
		}
	}
	function getDayTypeinfo($dayType) {
		$qryiInfo = "Select * from tblDayType where dayType='$dayType'";
		return  $this->getSqlAssoc($this->execQry($qryiInfo));
	}
	function getLoanTypeinfo($loanType) {
		$qryiInfo = "Select * from tblLoanType where lonTypeCd='$loanType'";
		return  $this->getSqlAssoc($this->execQry($qryiInfo));
	}

	function LoanType($act,$Array){
		switch($act) {
			case "Add": 
				$qryDay = "Insert into tblLoanType (compCode,lonTypeCd, lonTypeDesc,
							lonTypeShortDesc,lonTypeStat,trnCode) 
						   values ('{$_SESSION['company_code']}','{$Array['txtloanCd']}','".$this->strUpper($Array['txtdesc'])."',
						   '".$this->strUpper($Array['txtdesc2'])."','{$Array['cmbStat']}','{$Array['cmbtrnCode']}')";
			break;
			case "Edit":
				$qryDay = "Update tblLoanType set lonTypeDesc = '".$this->strUpper($Array['txtdesc'])."', 
							lonTypeShortDesc = '".$this->strUpper($Array['txtdesc2'])."',
							trnCode = '{$Array['cmbtrnCode']}', lonTypeStat = '{$Array['cmbStat']}' 
							where  lonTypeCd='{$Array['lCode']}' and compCode='{$_SESSION['company_code']}'";
			break;
		}
		return $this->execQry($qryDay);
	}	
	function CheckLoan($Code,$type) {
		if ($type == "loanCd") {
			if ($_GET['code'] == "EditLoanType")
				$not = "and not lonTypeCd='{$_GET['lCode']}' and lonTypeCd='$Code'";	
			else		
				$not = "and lonTypeCd='$Code'";	
				
			$qryLoan = "Select trnCode from tblLoanType where compCode='{$_SESSION['company_code']}' $not";
			return  $this->getRecCount($this->execQry($qryLoan));
		} elseif ($type == "trnCd") {
			if ($_GET['code'] == "EditLoanType")
				$not = "and not trnCode='{$_GET['txttrnCode']}' and trnCode='$Code'";
			else
				$not = "and trnCode='$Code'";
				
			$qrytrans = "Select trnCode from tblLoanType where compCode='{$_SESSION['company_code']}'  $not";
			return $this->getRecCount($this->execQry($qrytrans));
		}
	}
	function getTEUInfo($teuCode) {
		$qryteu = "Select * from tblTeu where teuCode='$teuCode'";
		return  $this->getSqlAssoc($this->execQry($qryteu));
	}
	function TEU($act,$Array){
		switch($act) {
			case "Add":
				$qryDay = "Insert into tblteu (teuCode,teuDesc,teuAmt) 
						   values ('".$this->strUpper($Array['txtteuCode'])."',
						   	'".$this->strUpper($Array['txtdesc'])."','{$Array['txtAmt']}')";
			break;
			case "Edit":
				$qryDay = "Update tblteu set teuCode = '".substr($this->strUpper($Array['txtteuCode']),0,3)."', 
							teuDesc = '".$this->strUpper($Array['txtdesc'])."',teuAmt = '{$Array['txtAmt']}' 
						   where  teuCode='{$Array['tCode']}'";
			break;
		}
		return $this->execQry($qryDay);
	}	
	function CheckTeu($Code) {
		if ($_GET['code'] == "EditTaxExemption")
			$not = "where not teuCode='{$this->strUpper($_GET['tCode'])}' and teuCode='{$this->strUpper($Code)}'";	
		else		
			$not = "where teuCode='{$this->strUpper($Code)}'";	
			
		$qryTeu = "Select teuCode from tblteu $not";
		return  $this->getRecCount($this->execQry($qryTeu));
	}
	function getAllowTypeinfo($allowCode) {
	 	$qryAllow = "Select * from tblAllowType where allowCode='$allowCode' and compCode='{$_SESSION['company_code']}'";
		return  $this->getSqlAssoc($this->execQry($qryAllow));
	}	
	function CheckAllow($Code,$type) {
		if ($_GET['code'] == "EditAllowanceType")
			$not = "and not trnCode='{$this->strUpper($_GET['txttrnCode'])}' and trnCode='{$this->strUpper($Code)}'";
		else
			$not = "and trnCode='{$this->strUpper($Code)}'";
			
		$qrytrans = "Select trnCode from tblAllowType where compCode='{$_SESSION['company_code']}'  $not";
		return $this->getRecCount($this->execQry($qrytrans));
	}	
	function AllowType($act,$Array){
		switch($act) {
			case "Add":
				$qryDay = "Insert into tblAllowType (compCode,allowCode, allowDesc,allowTypeStat,attnBase,
							trnCode,sprtPS,allowSked_type,hrTag) values ('{$_SESSION['company_code']}',
							'{$this->getMaxAllowType()}','{$this->strUpper($Array['txtdesc'])}',
							'{$Array['cmbStat']}','{$Array['cmbAttntag']}','{$Array['cmbtrnCode']}',
							'{$Array['cmbTaxtag']}','{$Array['cmbschedule']}','Y')";
			break;
			case "Edit":
				$qryDay = "Update tblAllowType set allowDesc = '{$this->strUpper($Array['txtdesc'])}', 
							allowTypeStat = '{$Array['cmbStat']}',trnCode = '{$Array['cmbtrnCode']}', 
							attnBase = '{$Array['cmbAttntag']}',sprtPS = '{$Array['cmbTaxtag']}',
							allowSked_type = '{$Array['cmbschedule']}',hrTag='Y' 
						  where  allowCode='{$Array['aCode']}' and compCode='{$_SESSION['company_code']}'";
			break;
		}
		return $this->execQry($qryDay);
	}		
	function getMaxAllowType() {
		$qryday = "Select max(allowCode) as allowCode from tblAllowType where compCode='{$_SESSION['company_code']}'";
		$res = $this->getSqlAssoc($this->execQry($qryday));
		$allowCode = $res['allowCode'] + 1;
		return $allowCode;
	}
	function getAllowTransType($Recode) {
		$qryTrans= "SELECT * FROM tblPayTransType 
					WHERE (compCode = '{$_SESSION['company_code']}') AND (trnCat = 'E') and trnRecode='$Recode'";
	return $this->getArrRes($this->execQry($qryTrans));
	}
	
}
?>