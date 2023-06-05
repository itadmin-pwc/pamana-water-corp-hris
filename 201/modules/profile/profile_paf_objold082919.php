<?
class pafObj extends commonObj {
	var $get;
	var $session;
	var $today;
	var $arrOthers 		= array();
	var $arrEmpStat 	= array();
	var $arrBranch 		= array();
	var $arrPosition 	= array();
	var $arrPayroll 	= array();
	var $arrAllow 		= array();
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
		$this->today = date('Y-m-d');
	}
	function checkECOLA($drate,$empNo,$compCode) {
		$qrycheck = "SELECT tblBranch.ecola FROM tblBranch INNER JOIN tblEmpMast ON tblBranch.compCode = tblEmpMast.compCode AND tblBranch.brnCode = tblEmpMast.empBrnCode where empNo='$empNo' and tblEmpMast.compCode='$compCode' and ecola >=$drate";
		if ($this->getRecCount($this->execQry($qrycheck)) > 0) {
			return true;
		} else {
			return false;
		}
		
	}
	function empStatus() {
		$Trns = $this->beginTran();
		if($this->get['cmbempstatus']!="" && $this->get['cmbempstatus']!=$this->get['oldstatus']){
			$field .= ",old_status,new_status";
			$value .= ",'{$this->get['oldstatus']}','{$this->get['cmbempstatus']}'"; 	
		}
		if($this->get['txtenddate']!="" && $this->get['txtenddate']!=$this->get['oldenddate']){
			$field .= ",old_enddate,new_enddate";
			$value .= ",'{$this->get['oldenddate']}','{$this->get['txtenddate']}'";	
		}
		if($this->get['cmbempnos']!="0" && $this->get['cmbempnos']!=$this->get['oldnos']){
			$field .= ",old_nos,new_nos";
			$value .= ",'{$this->get['oldnos']}','{$this->get['cmbempnos']}'"; 	
		}
		
		$qrydel = "Delete from tblPAF_EmpStatus where refNo='{$this->get['refno']}' and compCode='{$this->get['compCode']}' and empNo='{$this->get['empNo']}'";
		$this->execQry($qrydel);		
			$qryempStat = "Insert into tblPAF_EmpStatus (controlNo,compCode,empNo $field,stat,effectivitydate,userid,remarks,refNo,dateadded) values ('0','{$this->get['compCode']}','{$this->get['empNo']}' $value,'{$this->get['cmbstatus']}','{$this->get['txtempstatDate']}','{$this->session['user_id']}','{$this->get['txtempstatremarks']}','{$this->get['refno']}','".date('Y-m-d')."')";
		if ($Trns) {
			$Trns = $this->execQry($qryempStat);
		}	
/*		if ($this->get['empstattag'] == "1") {
				$Trns = $this->ProcessPAF('empstat');
		}
*/		
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}
		
	}
	
	function branch() {
		$Trns = $this->beginTran();
		if ($this->get['cmbbrgroup'] !="0" && $this->get['cmbbrgroup'] != $this->get['old_payGrp']) {
			$field .= ",old_payGrp,new_payGrp";
			$value .= ",'{$this->get['old_payGrp']}','{$this->get['cmbbrgroup']}'";
			
		}
		if ($this->get['cmbbranch'] !="0" && $this->get['cmbbranch'] != $this->get['old_branchCode']) {
			$field .= ",old_branchCode,new_branchCode";
			$value .= ",'{$this->get['old_branchCode']}','{$this->get['cmbbranch']}'";
			
		}
		$qrydel = "Delete from tblPAF_Branch where refNo='{$this->get['refno']}' and compCode='{$this->get['compCode']}' and empNo='{$this->get['empNo']}'";
		$this->execQry($qrydel);				
		$qrybranch = "Insert into tblPAF_Branch (controlNo,compCode,empNo $field ,stat,effectivitydate,userid,remarks,refNo,dateadded) values ('0','{$this->get['compCode']}','{$this->get['empNo']}' $value ,'{$this->get['cmbbrstatus']}','{$this->get['txtbrDate']}','{$this->session['user_id']}','{$this->get['txtbrremarks']}','{$this->get['refno']}','".date('Y-m-d')."')";
		if ($Trns) {
			$Trns = $this->execQry($qrybranch);
		}	
/*		if ($this->get['brtag'] == "1") {
				$Trns = $this->ProcessPAF('branch');
		}*/
		
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}
		
	}	
	
	function payroll() {
		$Trns = $this->beginTran();
		$field = "controlNo,compCode,empNo,remarks";
		$value = "'0','{$this->get['compCode']}','{$this->get['empNo']}','{$this->get['txtprremarks']}'";
		$empfield = " empNo='{$this->get['empNo']}'";
		if ($this->get['cmbpstatus'] !="0" && $this->get['cmbpstatus'] != $this->get['oldpayrollstatus']) {
			$field .= ",old_empPayType,new_empPayType";
			$value .= ",'{$this->get['oldpayrollstatus']}','{$this->get['cmbpstatus']}'";
			
		}
		if ($this->get['txtsalary'] !=0 && $this->get['txtsalary'] !="" && $this->get['txtdailyrate'] !=0 && $this->get['txtdailyrate'] !="" && $this->get['txtsalary'] != $this->get['oldmrate']) {
			$field .= ",new_empMrate,old_empMrate,new_empDrate,old_empDrate,new_empHrate,old_empHrate";
			$value .= ",'" . str_replace(',','',$this->get['txtsalary']) . "','" . str_replace(',','',$this->get['oldmrate']) . "','" . str_replace(',','',$this->get['txtdailyrate']) . "','" . str_replace(',','',$this->get['olddrate']) . "','" . str_replace(',','',$this->get['txthourlyrate']) . "','" . str_replace(',','',$this->get['oldhrate']) . "'";
			
		}
		if($this->get['taxstat']=="O"){
				$field .= ",old_empTeu,new_empTeu";
				$value .= ",'{$this->get['oldteu']}','{$this->get['cmbexemption']}'";
		}
		else{
			if ($this->get['cmbexemption'] !="0" && $this->get['cmbexemption'] != $this->get['oldteu']) {
				$field .= ",old_empTeu,new_empTeu";
				$value .= ",'{$this->get['oldteu']}','{$this->get['cmbexemption']}'";
			}
		}

		if ($this->get['cmbbank'] !="0" && $this->get['cmbbank'] != $this->get['oldbank']) {
			$field .= ",old_empBankCd,new_empBankCd";
			$value .= ",'{$this->get['oldbank']}','{$this->get['cmbbank']}'";
		}		
		
		if ($this->get['txtbankaccount'] !="" && $this->get['txtbankaccount'] != $this->get['oldaccountno']) {
			$field .= ",old_empAcctNo,new_empAcctNo";
			$value .= ",'{$this->get['oldaccountno']}','{$this->get['txtbankaccount']}'";
		}		
		
		/*if ($this->get['cmbgroup'] !="0" && $this->get['cmbgroup'] != $this->get['oldpaygroup']) {
			$field .= ",old_empPayGrp,new_empPayGrp";
			$value .= ",'{$this->get['oldpaygroup']}','{$this->get['cmbgroup']}'";
		}*/
		if ($this->get['cmbCategory'] !="0" && $this->get['cmbCategory'] != $this->get['oldpaycat']) {
			$field .= ",old_category,new_category";
			$value .= ",'{$this->get['oldpaycat']}','{$this->get['cmbCategory']}'";
		}
		
		$qrydel = "Delete from tblPAF_PayrollRelated where refNo='{$this->get['refno']}' and compCode='{$this->get['compCode']}' and empNo='{$this->get['empNo']}'";
		$this->execQry($qrydel);
		$qrypayroll = "Insert into tblPAF_PayrollRelated ($field,stat,effectivitydate,userid,refNo,reasonCd,dateadded) values ($value,'" . $this->get['cmbprstatus'] . "','" . $this->get['txtprDate'] . "','" . $this->session['user_id'] . "','" . $this->get['refno'] . "','" . $this->get['cmbreason'] . "','".date('Y-m-d')."')";
		if ($Trns) {
			$Trns = $this->execQry($qrypayroll);
		}		
		
/*		if ($this->get['prtag'] == "1") {
				$Trns = $this->ProcessPAF('payroll');
		}*/		
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}
	}
	
	function others() {
		$Trns = $this->beginTran();
		$field = "controlNo,compCode,empNo,remarks";
		$value = "'0','{$this->get['compCode']}','{$this->get['empNo']}','{$this->get['txtothremarks']}'";
		if ($this->get['txtlname'] !="" && $this->get['txtlname'] != $this->get['old_txtlname']) {
			$field .= ",old_empLastName,new_empLastName";
			$value .= ",'{$this->get['old_txtlname']}','{$this->get['txtlname']}'";
		}	
		if ($this->get['txtfname'] !="" && $this->get['txtfname'] != $this->get['old_txtfname']) {
			$field .= ",old_empFirstName,new_empFirstName";
			$value .= ",'{$this->get['old_txtfname']}','{$this->get['txtfname']}'";
		}	
		if ($this->get['txtmname'] !="" && $this->get['txtmname'] != $this->get['old_txtmname']) {
			$field .= ",old_empMidName,new_empMidName";
			$value .= ",'{$this->get['old_txtmname']}','{$this->get['txtmname']}'";
		}
		if ($this->get['txtBioNum'] !="" && (string)$this->get['txtBioNum'] !== (string)$this->get['old_txtBioNum']) {
			$field .= ",old_bioNumber,new_bioNumber";
			$value .= ",'{$this->get['old_txtBioNum']}','{$this->get['txtBioNum']}'";
		}
		
		if ($this->get['txtadd1'] !="" && $this->get['txtadd1'] != $this->get['old_txtadd1']) {
			$field .= ",old_empAddr1,new_empAddr1";
			$value .= ",'".str_replace("'","''",stripslashes($this->get['old_txtadd1']))."','".str_replace("'","''",stripslashes($this->get['txtadd1']))."'";
		}
		if ($this->get['txtadd2'] !="" && $this->get['txtadd2'] != $this->get['old_txtadd2']) {
			$field .= ",old_empAddr2,new_empAddr2";
			$value .= ",'".str_replace("'","''",stripslashes($this->get['old_txtadd2']))."','".str_replace("'","''",stripslashes($this->get['txtadd2']))."'";
		}
		if ($this->get['cmbProvince'] !="0" && $this->get['cmbProvince'] != $this->get['old_cmbcity']) {
			$field .= ",old_empProvinceCd,new_empProvinceCd";
			$value .= ",'{$this->get['old_cmbcity']}','{$this->get['cmbProvince']}'";
		}
		if ($this->get['cmbMunicipality'] !="0" && $this->get['cmbMunicipality'] != $this->get['old_municipality']){
			$field .= ",old_empMunicipalityCd,new_empMunicipalityCd";
			$value .= ",'{$this->get['old_MunicipalityCd']}','{$this->get['cmbMunicipality']}'";
		}
		if ($this->get['txtsss'] !="" && $this->get['txtsss'] != $this->get['old_txtsss']) {
			$field .= ",old_empSssNo,new_empSssNo";
			$value .= ",'{$this->get['old_txtsss']}','{$this->get['txtsss']}'";
		}
		if ($this->get['txtphilhealth'] !="" && $this->get['txtphilhealth'] != $this->get['old_txtphilhealth']) {
			$field .= ",old_empPhicNo,new_empPhicNo";
			$value .= ",'{$this->get['old_txtphilhealth']}','{$this->get['txtphilhealth']}'";
		}
		if ($this->get['txttax'] !="" && $this->get['txttax'] != $this->get['old_txttax']) {
			$field .= ",old_empTin,new_empTin";
			$value .= ",'{$this->get['old_txttax']}','{$this->get['txttax']}'";
		}
		if ($this->get['txthdmf'] !="" && $this->get['txthdmf'] != $this->get['old_txthdmf']) {
			$field .= ",old_empPagibig,new_empPagibig";
			$value .= ",'{$this->get['old_txthdmf']}','{$this->get['txthdmf']}'";
		}
		if($_SESSION['user_telcoaccess']=="Y"){
			if ($this->get['chkSun_0'] !="" && $this->get['chkSun_0'] != $this->get['old_sunline']) {
				$field .= ",old_empSunLine,new_empSunLine";
				$value .= ",'{$this->get['old_sunline']}','{$this->get['chkSun_0']}'";
			}
			if ($this->get['chkSun_1'] !="" && $this->get['chkSun_1'] != $this->get['old_sunline']) {
				$field .= ",old_empSunLine,new_empSunLine";
				$value .= ",'{$this->get['old_sunline']}','{$this->get['chkSun_1']}'";
			}
			if ($this->get['chkGlobe'] !="" && $this->get['chkGlobe'] != $this->get['old_globeline']) {
				$field .= ",old_empGlobeLine,new_empGlobeLine";
				$value .= ",'{$this->get['old_globeline']}','{$this->get['chkGlobe']}'";
			}
			if ($this->get['chkSmart'] !="" && $this->get['chkSmart'] != $this->get['old_smartline']) {
				$field .= ",old_empSmartLine,new_empSmartLine";
				$value .= ",'{$this->get['old_smartline']}','{$this->get['chkSmart']}'";
			}
		}
		if ($this->get['txtContactPerson'] !="" && $this->get['txtContactPerson'] != $this->get['old_contactPerson']) {
			$field .= ",old_empContactPerson,new_empContactPerson";
			$value .= ",'{$this->get['old_contactPerson']}','{$this->get['txtContactPerson']}'";
		}
		if ($this->get['txtContactNumber'] !="" && $this->get['txtContactNumber'] != $this->get['old_contactNumber']) {
			$field .= ",old_empContactNumber,new_empContactNumber";
			$value .= ",'{$this->get['old_contactNumber']}','{$this->get['txtContactNumber']}'";
		}
		if ($this->get['cmbbloodtype'] !="0" && $this->get['cmbbloodtype'] != $this->get['old_bloodType']) {
			$field .= ",old_empBloodType,new_empBloodType";
			$value .= ",'{$this->get['old_bloodType']}','{$this->get['cmbbloodtype']}'";
		}
		
		if($this->get['taxstat']!=""){
			$field .=",old_empMarStat,new_empMarStat";
			$value .=",'{$this->get['old_txtCStat']}','{$this->get['cmbstatus']}'";	
		}
		else{
			if ($this->get['cmbstatus']=='SG' || $this->get['cmbstatus']=="ME" || $this->get['cmbstatus']=="SP"  || $this->get['cmbstatus']=="WI"  && $this->get['cmbstatus'] != $this->get['old_txtCStat']){
				$field .=",old_empMarStat,new_empMarStat";
				$value .=",'{$this->get['old_txtCStat']}','{$this->get['cmbstatus']}'";	
			}
		}
		$qrydel = "Delete from tblPAF_Others where refNo='{$this->get['refno']}' and compCode='{$this->get['compCode']}' and empNo='{$this->get['empNo']}'";
		$this->execQry($qrydel);
		$qryothers = "Insert into tblPAF_Others ($field,stat,effectivitydate,userid,refNo,dateadded) values ($value,'{$this->get['cmbothstatus']}','{$this->get['txtothDate']}','{$this->session['user_id']}','{$this->get['refno']}','".date('Y-m-d')."')";
		if ($Trns) {
			$Trns = $this->execQry($qryothers);
		}		
/*		if ($this->get['othtag'] == "1") {
			$Trns = $this->ProcessPAF('others');
		}*/		
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}
	}
	
	function position() {
		$Trns = $this->beginTran();
		$field = "controlNo,compCode,empNo,remarks";
		$value = "'0','{$this->get['compCode']}','{$this->get['empNo']}','{$this->get['txtposremarks']}'";
		if ($this->get['cmbposition'] !="0" && $this->get['cmbposition'] != $this->get['old_posCode']) {
			$field .= ",old_posCode,new_posCode";
			$value .= ",'{$this->get['old_posCode']}','{$this->get['cmbposition']}'";
		}	
		if ($this->get['new_divCode'] !="" && $this->get['new_divCode'] != $this->get['old_divCode']) {
			$field .= ",old_divCode,new_divCode";
			$value .= ",'{$this->get['old_divCode']}','{$this->get['new_divCode']}'";
			$field .= ",old_deptCode,new_deptCode";
			$value .= ",'{$this->get['old_deptCode']}','{$this->get['new_deptCode']}'";
			$field .= ",old_secCode,new_secCode";
			$value .= ",'{$this->get['old_secCode']}','{$this->get['new_secCode']}'";
			$newDiv = 1;
		}	
		if ($this->get['new_deptCode'] !="" && $this->get['new_deptCode'] != $this->get['old_deptCode'] && $newDiv == "") {
			$field .= ",old_deptCode,new_deptCode";
			$value .= ",'{$this->get['old_deptCode']}','{$this->get['new_deptCode']}'";
			$field .= ",old_secCode,new_secCode";
			$value .= ",'{$this->get['old_secCode']}','{$this->get['new_secCode']}'";
			$newDept = 1;
		}	
		if ($this->get['new_secCode'] !="" && $this->get['new_secCode'] != $this->get['old_secCode'] && $newDept == "" && $newDiv == "") {
			$field .= ",old_secCode,new_secCode";
			$value .= ",'{$this->get['old_secCode']}','{$this->get['new_secCode']}'";
		}	
		if ($this->get['new_cat'] !="" && $this->get['new_cat'] != $this->get['old_cat']) {
			$field .= ",old_cat,new_cat";
			$value .= ",'{$this->get['old_cat']}','{$this->get['new_cat']}'";
		}	
		if ($this->get['new_level'] !="" && $this->get['new_level'] != $this->get['old_level']) {
			$field .= ",old_level,new_level";
			$value .= ",'{$this->get['old_level']}','{$this->get['new_level']}'";
		}	
		$qrydel = "Delete from tblPAF_Position where refNo='{$this->get['refno']}' and compCode='{$this->get['compCode']}' and empNo='{$this->get['empNo']}'";
		$this->execQry($qrydel);
		$qryothers = "Insert into tblPAF_Position ($field,stat,effectivitydate,userid,refNo,dateadded) values ($value,'{$this->get['cmbposstatus']}','{$this->get['txtposDate']}','{$this->session['user_id']}','{$this->get['refno']}','".date('Y-m-d')."')";
		if ($Trns) {
			$Trns = $this->execQry($qryothers);
		}			
/*		if ($this->get['postag'] == "1") {
			$Trns = $this->ProcessPAF('position');
		}*/		
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}	
	}
	
	function ProcessPAF(){
		$this->beginTranI();
		$Trns = "";
		/*if($cnt==1){
			$allstat="R";
		}else{
			$allstat="H";
		}*/
	for($i=0;$i<=(int)$this->get['chCtr'];$i++) {
			if ($this->get["chPAF$i"] !="") {
				$arrStr = explode(',',$this->get["chPAF$i"]);
				$act = $arrStr[0];
				$ref=$arrStr[1];
				$and = " AND refNo='{$arrStr[1]}'";
				switch($arrStr[0]) {
	
					
					case "empstat":
						$qrydata = "Select * from tblPAF_EmpStatus where  stat='R' and compCode='{$this->get['compCode']}' $and";
						$arrproc = $this->getArrResI($this->execQryI($qrydata));
						$qryempstat = "";
						$qryempMast = "";
						$qryReason = "";
						$field = "";
						$value = "";
						$qrySeparated = "";
						$dtResigned = "";
						$qryprocess = "";
						$qrydel = "";
						
						foreach ($arrproc as $val) {
//							if($val['old_enddate']!=""){
//								$field = ",old_enddate";
//								$value =",'{$val['old_enddate']}'";	
//							}
//							if($val['new_enddate']!=""){
//								$field = ",new_enddate";
//								$value = ",'{$val['new_enddate']}'";	
//							}
//							$qryprocess .= "Insert into tblPAF_EmpStatushist (controlNo,stat,compCode,empNo,old_status,new_status,
//							effectivitydate,user_created,user_updated,remarks,dateadded,refNo,dateupdated,datereleased,
//							old_nos,new_nos  $field) 
//							values ('{$val['controlNo']}','{$val['stat']}','{$val['compCode']}','{$val['empNo']}','{$val['old_status']}',
//							'{$val['new_status']}','{$val['effectivitydate']}','{$val['userid']}','{$this->session['user_id']}',
//							'{$val['remarks']}','{$val['dateadded']}','{$val['refNo']}','{$this->today}','{$val['datereleased']}',
//							'{$val['old_nos']}','{$val['new_nos']}' $value);";							
//							$empfield = " compCode='{$this->get['compCode']}'";
//							
//							if($val['new_status']!=""){
//								$empfield .= ",employmentTag='{$val['new_status']}'";
//								if ($val['new_status'] == 'RG'){
//									$dtResigned = ",dateReg='{$val['effectivitydate']}'";
//								}
//							}
//							
//							if($val['new_nos']!=""){
//								$natures = $this->setNatures($val['new_nos']);
//								$qrySeparated = "Insert into tblSeparatedEmployees (empNo,natureCode,[year],reason,dateAdded,
//								dateReleased) values('{$val['empNo']}','{$val['new_nos']}','".date("Y")."','{$val['remarks']}',
//								'{$val['dateadded']}','{$val['datereleased']}');";
//							}
//							if($val['new_enddate']!=""){
//								$empfield .= ",empEndDate='{$val['new_enddate']}'";	
//							}
//							
//							if ($natures == 'RS' || $natures == 'TR'  || $natures == 'AWOL'){
//								$empfield .= ",empStat='RS'";
//								$dtResigned = ",dateResigned='{$val['effectivitydate']}'";
//							}
//							elseif ($natures == 'EOC'){
//								$empfield .= ",empStat='RS'";
//								$dtResigned = ",endDate='{$val['effectivitydate']}'";
//							}
//							elseif ($natures == 'IN'){
//								$empfield .= ",empStat='IN'";	
//								$dtResigned = ",dateResigned='{$val['effectivitydate']}'";
//							}
//							
//							$qryempMast .= "Update tblEmpMast set $empfield $dtResigned 
//							where empNo='{$val['empNo']}' and compCode='{$val['compCode']}';";
//							unset($dtResigned);
							if($val['old_enddate']!=""){
								$field = ",old_enddate";
								$value =",'{$val['old_enddate']}'";	
							}
							if($val['new_enddate']!=""){
								$field = ",new_enddate";
								$value = ",'{$val['new_enddate']}'";	
							}
							
							$qryprocess .= "Insert into tblPAF_EmpStatushist (controlNo,stat,compCode,empNo,old_status,new_status,
							effectivitydate,user_created,user_updated,remarks,dateadded,refNo,dateupdated,datereleased,
							old_nos,new_nos  $field) 
							Select controlNo,stat,compCode,empNo,old_status,new_status,effectivitydate,userid,
							'{$this->session['user_id']}',remarks,dateadded,refNo,'{$this->today}',datereleased,
							old_nos,new_nos $value 
							from tblPAF_EmpStatus where  stat='R' and compCode='{$this->get['compCode']}' $and;";		
												
							$empfield = " compCode='{$this->get['compCode']}'";
							
							if($val['new_status']!=""){
								$empfield .= ",employmentTag='{$val['new_status']}'";
								if ($val['new_status'] == 'RG'){
									$dtResigned = ",dateReg='{$val['effectivitydate']}'";
								}
							}
							

							if(trim($val['new_nos'])!="" || isset($val['new_nos'])){
								if ($val['new_nos'] == 3 || $val['new_nos']=="3"){
									$empfield .= ",empStat='RS'";
									$dtResigned = ",dateResigned='{$val['effectivitydate']}'";
								}
								if ($val['new_nos'] == 5 || $val['new_nos']=="5"){
									$empfield .= ",empStat='RS'";
									$dtResigned = ",dateResigned='{$val['effectivitydate']}'";
								}
								if ($val['new_nos'] == 1 || $val['new_nos']=="1"){
									$empfield .= ",empStat='RS'";
									$dtResigned = ",dateResigned='{$val['effectivitydate']}'";
								}
								if ($val['new_nos'] == 2 || $val['new_nos']=="2"){
									$empfield .= ",empStat='RS'";
									$dtResigned = ",endDate='{$val['effectivitydate']}'";
								}
								if ($val['new_nos'] == 4 || $val['new_nos']=="4"){
									$empfield .= ",empStat='IN'";	
									$dtResigned = ",dateResigned='{$val['effectivitydate']}'";
								}
								
								$qrySeparated .= "Insert into tblSeparatedEmployees (empNo,natureCode,year,reason,dateAdded,
								dateReleased) values('{$val['empNo']}','{$val['new_nos']}','".date("Y")."','{$val['remarks']}',
								'{$val['dateadded']}','{$val['datereleased']}');";
							}
							if($val['new_enddate']!=""){
								$empfield .= ",empEndDate='{$val['new_enddate']}'";	
							}
														
							$qryempMast .= "Update tblEmpMast set $empfield $dtResigned 
							where empNo='{$val['empNo']}' and compCode='{$val['compCode']}';";
							unset($dtResigned);
							unset($field);
							unset($value);
						}
						$qrydel .= "delete from tblPAF_EmpStatus where  stat='R'  and compCode='{$this->get['compCode']}' $and";
					break;
					case "payroll":
						$qrydata = "Select * from tblPAF_PayrollRelated where  stat='R' and compCode='{$this->get['compCode']}' $and";
						$arrproc = $this->getArrResI($this->execQryI($qrydata));
						$qryempMast = "";
						$minWage = "";
						$qryprocess = "";
						$qrydel = "";
						$minWage = "";
						$field = "compCode";
						$value = "'{$this->get['compCode']}'";
						foreach ($arrproc as $val) {
							$empfield = " compCode='{$this->get['compCode']}'";
							if (trim($val['new_empPayType']) !="") {
								$empfield .= ",empPayType='{$val['new_empPayType']}'";
							}
							if ($val['new_empMrate'] !=0 && trim($val['new_empMrate']) != "") {
								$empfield .= ",empMrate={$val['new_empMrate']}, empDrate={$val['new_empDrate']}, empHrate={$val['new_empHrate']}";
								$minWage .= " CALL sp_MinWage ('{$val['empNo']}',{$val['new_empDrate']},'{$this->get['compCode']}')";
								
							}
							if ($val['new_empTeu'] !="") {
								$empfield .= ",empTeu='{$val['new_empTeu']}'";
							}
					
							if ($val['new_empBankCd'] !="" ) {
								$empfield .= ",empBankCd='{$val['new_empBankCd']}'";
							}		
							
							if (trim($val['new_empAcctNo']) !="" ) {
								$empfield .= ",empAcctNo='{$val['new_empAcctNo']}'";
							}		
							
							if ($val['new_empPayGrp'] !="") {
								$empfield .= ",empPayGrp='{$val['new_empPayGrp']}'";
							}
							if ($val['new_category'] !="") {
								$empfield .= ",empPayCat='{$val['new_category']}'";
							}
							$qryempMast .= "Update tblEmpMast set $empfield where empNo='{$val['empNo']}' 
												and compCode='{$val['compCode']}';";	
						}

						$qryprocess .= "Insert into tblPAF_PayrollRelatedhist (controlNo,compCode, empNo, old_empTeu, 
											old_empBankCd, old_empAcctNo, old_empMrate, old_empDrate, old_empHrate, old_empPayType,
											old_empPayGrp, old_category ,new_empTeu, new_empBankCd, new_empAcctNo, new_empMrate, 
											new_empDrate, new_empHrate, new_empPayType, new_empPayGrp,new_category ,effectivitydate, 
											remarks, user_created,dateadded,refNo,reasonCd,user_updated,dateupdated,datereleased)
										Select controlNo,compCode, empNo, old_empTeu, old_empBankCd, old_empAcctNo, old_empMrate,
											old_empDrate, old_empHrate, old_empPayType, old_empPayGrp, old_empPayGrp, new_empTeu, 
											new_empBankCd, new_empAcctNo, new_empMrate, new_empDrate, new_empHrate, new_empPayType, 
											new_empPayGrp,new_category,effectivitydate, remarks, userid,dateadded,refNo,reasonCd,
											'{$this->session['user_id']}','{$this->today}',datereleased 
										from tblPAF_PayrollRelated 
										where  stat='R' and compCode='{$this->get['compCode']}' $and;";	
						$qrydel .= "delete from tblPAF_PayrollRelated where  stat='R' and compCode='{$this->get['compCode']}' $and;";
					break;
					
					case "others":
						$qrydata = "Select * from tblPAF_Others where  stat='R' and compCode='{$this->get['compCode']}' $and";
						$arrproc = $this->getArrResI($this->execQryI($qrydata));
						$qryempstat = "";
						$qryempMast = "";
						$qryempBio = "";
						$qryprocess = "";
						$qrydel = "";
						foreach ($arrproc as $val) {
							$empfield = " compCode='{$this->get['compCode']}'";
							if ($val['old_empLastName'] !="") {
								$empfield .= ",empLastName='{$val['new_empLastName']}'";
							}	
							if ($val['old_empFirstName'] !="") {
								$empfield .= ",empFirstName='{$val['new_empFirstName']}'";
		
							}	
							if ($val['old_empMidName'] !="") {
								$empfield .= ",empMidName='{$val['new_empMidName']}'";
								
							}
							if ($val['old_bioNumber'] !="") {
								$empfield_bio = " bioNumber='{$val['new_bioNumber']}', updatedBy='".$this->session['user_id']."', dateUpdated='".date("Y-m-d")."'";
								
							}
							if ($val['old_empAddr1'] !="") {
								$empfield .= ",empAddr1='{$val['new_empAddr1']}'";
							}
							if ($val['old_empAddr2'] !="") {
								$empfield .= ",empAddr2='{$val['new_empAddr2']}'";
							}
							if ($val['old_empCityCd'] !="" ) {
								$empfield .= ",empCityCd='{$val['new_empCityCd']}'";
							}
							if ($val['old_empSssNo'] !="") {
								$empfield .= ",empSssNo='{$val['new_empSssNo']}'";
							}
							if ($val['old_empPhicNo'] !="") {
								$empfield .= ",empPhicNo='{$val['new_empPhicNo']}'";
							}
							if ($val['old_empTin'] !="") {
								$empfield .= ",empTin='{$val['new_empTin']}'";
							}
							if ($val['old_empPagibig'] !="") {
								$empfield .= ",empPagibig='{$val['new_empPagibig']}'";
							}	
							if(trim($val['old_empProvinceCd'])!=""){
								$empfield .= ",empProvinceCd='{$val['new_empProvinceCd']}'";	
							}
							if(trim($val['old_empMunicipalityCd'])!=""){
								$empfield .= ",empMunicipalityCd='{$val['new_empMunicipalityCd']}'";		
							}
							if($val['old_empContactPerson']!=""){
								$empfield .= ",empECPerson='{$val['new_empContactPerson']}'";		
							}
							if($val['old_empContactNumber']!=""){
								$empfield .= ",empECNumber='{$val['new_empContactNumber']}'";		
							}
							if(trim($val['old_empBloodType'])!=""){
								$empfield .= ",empBloodType='{$val['new_empBloodType']}'";		
							}
							if($val['old_empMarStat']!=""){
								$empfield .=",empMarStat='{$val['new_empMarStat']}'";	
							}
							if($val['old_empSunLine']!=""){
								$empfield .=",empSunLine='{$val['new_empSunLine']}'";	
							}
							if($val['old_empGlobeLine']!=""){
								$empfield .=",empGlobeLine='{$val['new_empGlobeLine']}'";	
							}
							if($val['old_empSmartLine']!=""){
								$empfield .=",empSmartLine='{$val['new_empSmartLine']}'";	
							}
							$qryempMast .= "Update tblEmpMast set $empfield where empNo='{$val['empNo']}' and compCode='{$this->get['compCode']}';";	
							//Check tblbio
							if(($val['old_bioNumber'] !="") || ($val['new_bioNumber'] !=""))
							{
								$arrChckBioEmp = $this->checkBioEmpExist(" and empNo='".trim($val['empNo'])."'");
								if($arrChckBioEmp['empNo']!=""){
									$qryempBio.="Update tblBioEmp set $empfield_bio where empNo='{$val['empNo']}' and compCode='{$this->get['compCode']}';";
								}
								else{
									$qryempBio.="Insert into tblBioEmp(compCode, locCode, bioNumber, empNo, bioStat, addedBy, dateAdded)values('".$this->get['compCode']."','".$this->get['old_biobranchCode']."', '".$val['new_bioNumber']."', '".$val['empNo']."', 'A', '".$this->session['user_id']."', '".date("Y-m-d")."');";
								}
							}
						}	
						
						 $qryprocess .= "Insert into tblPAF_Othershist 
											(controlNo,compCode, empNo, old_empLastName, old_empFirstName, old_empMidName, 
											old_empAddr1, old_empAddr2, old_empCityCd, old_empTin,old_empSssNo, old_empPhicNo,
											old_empPagibig, old_bioNumber, old_empProvinceCd,old_empMunicipalityCd,old_empMarStat,
											old_empContactPerson, old_empContactNumber, old_empBloodType, new_empLastName,
											new_empFirstName, new_empMidName, new_empAddr1, new_empAddr2, new_empCityCd, 
											new_empTin, new_empSssNo, new_empPhicNo, new_empPagibig, new_bioNumber, stat, 
											effectivitydate, remarks, dateadded, user_created, refNo, user_updated,
											dateupdated, datereleased, new_empProvinceCd, new_empMunicipalityCd, new_empMarStat,
											new_empContactPerson, new_empContactNumber, new_empBloodType, old_empSunLine,
											old_empGlobeLine, old_empSmartLine, new_empSunLine, new_empGlobeLine, new_empSmartLine)
										Select  controlNo, compCode, empNo, old_empLastName, old_empFirstName, old_empMidName, 
											old_empAddr1, old_empAddr2, old_empCityCd, old_empTin,old_empSssNo, old_empPhicNo,
											old_empPagibig, old_bioNumber, old_empProvinceCd, old_empMunicipalityCd, old_empMarStat,
											old_empContactPerson, old_empContactNumber, old_empBloodType, new_empLastName, 
											new_empFirstName, new_empMidName, new_empAddr1, new_empAddr2, new_empCityCd, new_empTin, 
											new_empSssNo, new_empPhicNo, new_empPagibig, new_bioNumber, stat, effectivitydate, 
											remarks, dateadded, userid,refNo,'{$this->session['user_id']}', '{$this->today}',
											datereleased,new_empProvinceCd,new_empMunicipalityCd,new_empMarStat, 
											new_empContactPerson, new_empContactNumber, new_empBloodType, old_empSunLine,
											old_empGlobeLine, old_empSmartLine, new_empSunLine, new_empGlobeLine, new_empSmartLine 
										from tblPAF_Others 
										where  stat='R' and compCode='{$this->get['compCode']}' $and;";
						$qrydel .= "delete from tblPAF_Others where  stat='R' and compCode='{$this->get['compCode']}' $and;";
					break;
					
					case "position":
						$qrydata = "Select * from tblPAF_Position where  stat='R' and compCode='{$this->get['compCode']}' $and";
						$arrproc = $this->getArrResI($this->execQryI($qrydata));
						$qryempstat = "";
						$qryempMast = "";
						$qryprocess = "";
						$qrydel = "";
						foreach ($arrproc as $val) {
							$empfield = " compCode='{$this->get['compCode']}'";
							if ($val['new_divCode'] !="") {
								$empfield .= ",empDiv='{$val['new_divCode']}'";
							}	
							if ($val['new_DeptCode'] !="") {
								$empfield .= ",empDepCode='{$val['new_DeptCode']}'";
							}
							if ($val['new_secCode'] !="") {
								$empfield .= ",empSecCode='{$val['new_secCode']}'";
							}
							if ($val['new_cat'] !="") {
								$empfield .= ",empRank='{$val['new_cat']}'";
							}
							if ($val['new_posCode'] !="") {
								$empfield .= ",empPosId='{$val['new_posCode']}'";
							}
							if ($val['new_level'] !="") {
								$empfield .= ",empLevel='{$val['new_level']}'";
							}																					
							$qryempMast .= "Update tblEmpMast set $empfield where empNo='{$val['empNo']}' and compCode='{$this->get['compCode']}';";	
						}	
						$qryprocess .= "Insert into tblPAF_Positionhist (controlNo,compCode, empNo, old_divCode, 
											old_deptCode, old_secCode, old_cat, old_level, old_posCode, new_divCode, new_DeptCode, 
											new_secCode, new_cat, new_level, new_posCode, stat, effectivitydate, remarks,
											dateadded, user_created,refNo,user_updated,dateupdated,datereleased)
										Select  controlNo,compCode, empNo, old_divCode, old_deptCode, old_secCode, old_cat, 
											old_level, old_posCode, new_divCode, new_DeptCode, new_secCode, new_cat, new_level, 
											new_posCode, stat, effectivitydate, remarks, dateadded, userid,refNo,
											'{$this->session['user_id']}','{$this->today}',datereleased 
											from tblPAF_Position 
										where  stat='R' and compCode='{$this->get['compCode']}';";
						$qrydel .= "delete from tblPAF_Position where  stat='R' and compCode='{$this->get['compCode']}' $and;";			
					break;
					
					case "branch":
						$qrydata = "Select * from tblPAF_Branch where stat='R' and compCode='{$this->get['compCode']}' $and";
						$arrproc = $this->getArrResI($this->execQryI($qrydata));
						$qryempstat = "";
						$qryempMast = "";
						$qryprocess = "";
						$qrydel = "";
						$qryempBio = "";
						$qryempCWWTag = "";
						foreach ($arrproc as $val) {
							$empfield = " compCode='{$this->get['compCode']}'";
							$bioField = " compCode='{$this->get['compCode']}'";
							$empCWWTag = " compCode='{$this->get['compCode']}'";
							if ($val['old_branchCode'] !="") {
								$empfield .= ",empBrnCode='{$val['new_branchCode']}',empLocCode='{$val['new_branchCode']}'";
								$bioField .= ",locCode='{$val['new_branchCode']}'";
							}	
							if ($val['old_payGrp'] !="") {
								$empfield .= ",empPayGrp='{$val['new_payGrp']}'";
							}
							
							if ($val['new_branchCode'] !="" && $val['new_branchCode']!="0001") {
								$empCWWTag .= ",CWWTag=NULL";
							}
							
							$valBranch = $this->getSqlAssoc($this->execQry("Select brnCode,compCode,minWage 
																			  from tblBranch 
																			  where brnCode='".$val['new_branchCode']."'
																			  	and compCode='".$this->get['compCode']."'"));
							$valEmp = $this->getSqlAssoc($this->execQry("Select empNo,empWageTag,empDrate 
																		   from tblEmpMast 
																		   where empNo='".$val['empNo']."' 
																		   	and compCode='".$this->get['compCode']."'"));
							
							if($valEmp['empDrate']<=$valBranch['minWage']){
								$empfield .= ",empWageTag='Y'";									
							}
							else{
								$empfield .= ",empWageTag='N'";		
							}
							
							$qryempMast .= "Update tblEmpMast set $empfield where empNo='{$val['empNo']}' 
												and compCode='{$this->get['compCode']}';";	
							$qryempBio .= "Update tblBioEmp set $bioField where empNo='{$val['empNo']}' 
												and compCode='{$this->get['compCode']}';";	
							$qryempCWWTag .= "Update tblTK_EmpShift set $empCWWTag where empNo='{$val['empNo']}' 
												and compCode='{$this->get['compCode']}';";		
						}	
						$qryprocess .= "Insert into tblPAF_Branchhist (controlNo,compCode, empNo, old_branchCode, 
											old_payGrp, new_branchCode, new_payGrp, stat, effectivitydate, remarks, dateadded, 
											user_created, refNo,user_updated,dateupdated,datereleased)
										Select  controlNo,compCode, empNo, old_branchCode, old_payGrp, new_branchCode, 
											new_payGrp, stat, effectivitydate, remarks, dateadded, userid, refNo,
											'{$this->session['user_id']}','{$this->today}',datereleased 
										from tblPAF_Branch
										where  stat='R' and compCode='{$this->get['compCode']}' $and;";
						$qrydel .= "delete from tblPAF_Branch where  stat='R' and compCode='{$this->get['compCode']}' $and;";			
					break;

					case "allow":
					
					$qryempstat = "";
						$qryempMast = "";
						$qryprocess = "";
						$qrydel = "";
						$qryempMastInsert = "";
						$qryempMastUpdate = "";
					
						$qryAllow = "Select compCode, empNo, allowCode, allowAmt,allowAmtold, allowSked, allowTaxTag, 
										allowPayTag, allowStart, allowEnd, allowStat, sprtPS, refNo, controlNo,
										effectivitydate,'$today','$today',userid,'{$_SESSION['user_id']}',allowTag,stat 
									 from tblPAF_Allowance 
									 where compCode='{$_SESSION['company_code']}' and stat='R' $and";
									$sqlqry =$this->execQryI($qryAllow)or die('error qry');
						if($sqlqry->num_rows > 0){
						while($row = mysqli_fetch_assoc($sqlqry)) {
						$compcde=$row['compCode'];
						$empno=$row['empNo'];
						if ($this->checkEmpAllow($row['empNo'],$row['allowCode'])==0) {
								$qryempMastInsert .= "Insert into tblAllowance (compCode, empNo, allowCode, allowAmt, allowSked, 
													allowTaxTag, allowPayTag, allowStart, allowEnd, allowStat, sprtPS,allowTag) 
												values ('$compcde', '$empno', '{$row['allowCode']}', 
													'{$row['allowAmt']}', '{$row['allowSked']}', '{$row['allowTaxTag']}',
													'{$row['allowPayTag']}', '{$row['allowStart']}', '{$row['allowEnd']}',
													'{$row['allowStat']}','{$row['sprtPS']}','{$row['allowTag']}');\n";
							} else {
								$qryempMastUpdate .= "Update tblAllowance set allowAmt='{$row['allowAmt']}',
													allowTag='{$row['allowTag']}',allowSked='{$row['allowSked']}',
													allowTaxTag='{$row['allowTaxTag']}',sprtPS='{$row['sprtPS']}',
													allowStat='{$row['allowStat']}',allowPayTag='{$row['allowPayTag']}' 
												where compCode = '{$_SESSION['company_code']}' AND empNo = '$empno'  
													AND allowCode  = '{$row['allowCode']}';\n "; 


							}}$qrydel .= "Delete from tblPAF_Allowance where compCode='{$_SESSION['company_code']}' and stat='R' $and;";	}
					

/* 						foreach($resAllw as $val) {
							$qryprocess .= "Insert into tblPAF_Allowancehist (compCode, empNo, allowCode, allowAmt,allowAmtold,
												allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd, allowStat, sprtPS, refNo,
												controlNo, effectivitydate, dateadded, dateupdated, user_created, 
												user_updated,allowTag,stat) 
											values ('{$val['compCode']}', '{$val['empNo']}', '{$val['allowCode']}',
												'{$val['allowAmt']}', '{$val['allowAmtold']}', '{$val['allowSked']}',
												'{$val['allowTaxTag']}', '{$val['allowPayTag']}', '{$val['allowStart']}',
												'{$val['allowEnd']}', '{$val['allowStat']}', '{$val['sprtPS']}', '{$val['refNo']}',
												'{$val['controlNo']}', '{$val['effectivitydate']}', '{$this->today}', 
												'{$this->today}', '{$val['userid']}', '{$_SESSION['user_id']}', 
												'{$val['allowTag']}', '{$val['stat']}'); ";
							if ($this->checkEmpAllow($val['empNo'],$val['allowCode'])==0) {
								$qryempMastInsert .= "Insert into tblAllowance (compCode, empNo, allowCode, allowAmt, allowSked, 
													allowTaxTag, allowPayTag, allowStart, allowEnd, allowStat, sprtPS,allowTag) 
												values ('{$val['compCode']}', '{$val['empNo']}', '{$val['allowCode']}', 
													'{$val['allowAmt']}', '{$val['allowSked']}', '{$val['allowTaxTag']}',
													'{$val['allowPayTag']}', '{$val['allowStart']}', '{$val['allowEnd']}',
													'{$val['allowStat']}','{$val['sprtPS']}','{$val['allowTag']}');\n";
							} else {
								$qryempMastUpdate .= "Update tblAllowance set allowAmt='{$val['allowAmt']}',
													allowTag='{$val['allowTag']}',allowSked='{$val['allowSked']}',
													allowTaxTag='{$val['allowTaxTag']}',sprtPS='{$val['sprtPS']}',
													allowStat='{$val['allowStat']}',allowPayTag='{$val['allowPayTag']}' 
												where compCode = '{$_SESSION['company_code']}' AND empNo = '{$val['empNo']}'  
													AND allowCode  = '{$val['allowCode']}';\n "; 


							}
				//if($qryempMastUpdate!=""){
					//$Trns = $this->execQryI($qryempMastUpdate);	
				//}
			
						
						}
						 */
										
					break;
				}
				/* echo $pushNumber;
				echo $allstat."-".$cnt."-".$empno.$sqlqry->num_rows;
			echo $arrStr[1]; */
			
				if($qryempBio!=""){
					$Trns = $this->execQryI($qryempBio);	
				}
				if($qryempCWWTag!=""){
					$Trns = $this->execQryI($qryempCWWTag);	
				}
				if($qryempMastInsert!=""){
					$Trns = $this->execMultiQryI($qryempMastInsert);	
				}
				if($qryempMastUpdate!=""){
					$Trns = $this->execMultiQryI($qryempMastUpdate);	
				}
				if($minWage!=""){
					$Trns = $this->execQryI($minWage);	
				}
				if($qryprocess!=""){
					$Trns = $this->execQryI($qryprocess);
				}
				if ($qryempMast!="") {
					$Trns = $this->execQryI($qryempMast);
				}
				if($qrySeparated!=""){				
					$Trns = $this->execQryI($qrySeparated);
				}
				if ($qrydel!="") {
					$Trns = $this->execMultiQryI($qrydel);
				}
				if($qryempBio!=""){
					$Trns = $this->execQryI($qryempBio);
				}
			}	
		}	
		
		if($Trns==FALSE){
			$this->rollbackTranI();
			return false;
			
				
			
		}
		else{
			$this->commitTranI();
			return true;	
		}
	}
	
	function getRefNo($compCode) {
		$qryRefNo = "Select refno+1 as refno from tblPAF_RefNo where compCode='{$compCode}';";
		$res = $this->getSqlAssoc($this->execQry($qryRefNo));
		$qryrefUpdate = "Update tblPAF_RefNo set refno=refno+1 where compCode='{$compCode}';";
		$this->execQry($qryrefUpdate);
		return $res;
	}
	function getPAFlist($empNo,$compCode,$table) {
		$qryPAF = "Select refNo from $table where empNo='$empNo' and compCode='$compCode'";
		return $this->getArrRes($this->execQry($qryPAF));
	}
	function getPAFvalue($refNo,$table,$compCode) {
		$qryvalue = "Select * from $table where refNo='$refNo' and compCode='$compCode'";
		return $this->getSqlAssoc($this->execQry($qryvalue));
	}
	function delrefNo($empNo,$compCode,$table,$refNo) {
		$qryPAF = "Delete from $table where empNo='$empNo' and compCode='$compCode' and refNo='$refNo'";
		return $this->execQry($qryPAF);
	}
	function getPAF_others($empNo,$pafType,$and="",$hist="") {
		$i=0;
		$compCode = $_SESSION['company_code'];
		if (empty($pafType) || $pafType == "others") {
			if (in_array($empNo,$this->arrOthers)){
				$qryPAF = "Select * from tblPAF_Others$hist where empNo=$empNo $and order by effectivitydate";
			$res = $this->getArrRes($this->execQry($qryPAF));
			$arrPAF =array('field','value1','value2','effdate','refno');
			foreach($res as $val) {
				if (trim($val['new_empLastName']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Last Name";
					$arrPAF['value1'][$i]=$val['old_empLastName'];
					$arrPAF['value2'][$i]=$val['new_empLastName'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empFirstName']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="First Name";
					$arrPAF['value1'][$i]=$val['old_empFirstName'];
					$arrPAF['value2'][$i]=$val['new_empFirstName'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empMidName']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Middle Name";
					$arrPAF['value1'][$i]=$val['old_empMidName'];
					$arrPAF['value2'][$i]=$val['new_empMidName'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_bioNumber']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Bio Number";
					$arrPAF['value1'][$i]=$val['old_bioNumber'];
					$arrPAF['value2'][$i]=$val['new_bioNumber'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empAddr1']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Others";
					$arrPAF['value1'][$i]=$val['old_empAddr1'];
					$arrPAF['value2'][$i]=$val['new_empAddr1'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empAddr2']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Others";
					$arrPAF['value1'][$i]=$val['old_empAddr2'];
					$arrPAF['value2'][$i]=$val['new_empAddr2'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if ($val['new_empCityCd'] !=0) {
					$old_city = $this->getcitywil(" where CityCd='{$val['old_empCityCd']}'");
					$new_city = $this->getcitywil(" where CityCd='{$val['new_empCityCd']}'");
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Others";
					$arrPAF['value1'][$i]=$old_city['cityDesc'];
					$arrPAF['value2'][$i]=$new_city['cityDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empSssNo']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="SSS No.";
					$arrPAF['value1'][$i]=$val['old_empSssNo'];
					$arrPAF['value2'][$i]=$val['new_empSssNo'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empPhicNo']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Philhealth No.";
					$arrPAF['value1'][$i]=$val['old_empPhicNo'];
					$arrPAF['value2'][$i]=$val['new_empPhicNo'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empTin']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Tax ID No.";
					$arrPAF['value1'][$i]=$val['old_empTin'];
					$arrPAF['value2'][$i]=$val['new_empTin'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empPagibig']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="HDMF No.";
					$arrPAF['value1'][$i]=$val['old_empPagibig'];
					$arrPAF['value2'][$i]=$val['new_empPagibig'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if(trim($val['new_empProvinceCd'])!=""){
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Others";
					$arrPAF['value1'][$i]=$this->empProvince($val['old_empProvinceCd']);
					$arrPAF['value2'][$i]=$this->empProvince($val['new_empProvinceCd']);
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
					}			
				if(trim($val['new_empMunicipalityCd'])!=""){
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Others";
					$arrPAF['value1'][$i]=$this->empMunicipality($val['old_empMunicipalityCd']);
					$arrPAF['value2'][$i]=$this->empMunicipality($val['new_empMunicipalityCd']);
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
					}	
				if(trim($val['new_empMarStat'])!=""){
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Civil Status";
					$arrPAF['value1'][$i]=$this->EmpCivilStat($val['old_empMarStat']);
					$arrPAF['value2'][$i]=$this->EmpCivilStat($val['new_empMarStat']);
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;	
				}	
				if(trim($val['new_empContactPerson'])!=""){
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Contact Person";
					$arrPAF['value1'][$i]=$val['old_empContactPerson'];
					$arrPAF['value2'][$i]=$val['new_empContactPerson'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;	
				}	
				if(trim($val['new_empContactNumber'])!=""){
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Contact Number";
					$arrPAF['value1'][$i]=$val['old_empContactNumber'];
					$arrPAF['value2'][$i]=$val['new_empContactNumber'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;	
				}	
				if(trim($val['new_empBloodType'])!=""){
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Blood Type";
					$arrPAF['value1'][$i]=($val['old_empBloodType']=="0")?"":$val['old_empBloodType'];
					$arrPAF['value2'][$i]=$val['new_empBloodType'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;	
				}	
				if(trim($val['new_empSunLine'])!=""){
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Company Issued Mobile Phone Line";
					$arrPAF['value1'][$i]=$val['old_empSunLine'];
					$arrPAF['value2'][$i]=($val['new_empSunLine']=="Y"?"Sun Line":"Remove/Cancel Sun Line");
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;	
				}	
				if(trim($val['new_empGlobeLine'])!=""){
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Company Issued Mobile Phone Line";
					$arrPAF['value1'][$i]=$val['old_empGlobeLine'];
					$arrPAF['value2'][$i]=($val['new_empGlobeLine']=="Y"?"Globe Line":"Remove/Cancel Globe Line");
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;	
				}	
				if(trim($val['new_empSmartLine'])!=""){
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Company Issued Mobile Phone Line";
					$arrPAF['value1'][$i]=$val['old_empSmartLine'];
					$arrPAF['value2'][$i]=($val['new_empSmartLine']=="Y"?"Smart Line":"Remove/Cancel Smart Line");
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;	
				}	
			}
			unset($res,$val,$qryPAF);
			}
		}
		if (empty($pafType) || $pafType == "empstat") {
			if (in_array($empNo,$this->arrEmpStat)){
				$qryPAF = "Select * from tblPAF_EmpStatus$hist where empNo=$empNo $and order by effectivitydate";
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				if (trim($val['new_status']) !="") {
					$arrPAF['type'][$i]='empstat,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Employment Status";
					$arrPAF['value1'][$i]=$this->EmpStat($val['old_status']);
					$arrPAF['value2'][$i]=$this->EmpStat($val['new_status']);
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
				if (trim($val['new_enddate']) !="") {
					$arrPAF['type'][$i]='empstat,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="End Date";
					$arrPAF['value1'][$i]=$this->valDateArt($val['old_enddate']);
					$arrPAF['value2'][$i]=$this->valDateArt($val['new_enddate']);
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
				if (trim($val['new_nos']) !="") {
					$arrPAF['type'][$i]='empstat,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Nature of separation";
					$arrPAF['value1'][$i]=$this->getNatures($val['old_nos']);
					$arrPAF['value2'][$i]=$this->getNatures($val['new_nos']);
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
			}
			unset($res,$val,$qryPAF);
			}
		}
		if (empty($pafType) || $pafType == "branch") {
			if (in_array($empNo,$this->arrBranch)){
				$qryPAF = "Select * from tblPAF_Branch$hist where empNo=$empNo $and order by effectivitydate";
				$res = $this->getArrRes($this->execQry($qryPAF));
				foreach($res as $val) {
					if (trim($val['new_branchCode']) !="") {
						$old_branch = $this->getEmpBranchArt($_SESSION['company_code'],$val['old_branchCode']);		
						$new_branch = $this->getEmpBranchArt($_SESSION['company_code'],$val['new_branchCode']);			
						$arrPAF['type'][$i]='branch,'.$val['refNo'];
						$arrPAF['stat'][$i]=$val['stat'];
						$arrPAF['field'][$i]="Branch";
						$arrPAF['value1'][$i]=$old_branch['brnDesc'];
						$arrPAF['value2'][$i]=$new_branch['brnDesc'];
						$arrPAF['effdate'][$i]=$val['effectivitydate'];
						$arrPAF['dateupdated'][$i]=$val['dateupdated'];
						$arrPAF['refno'][$i]=$val['refNo'];
						$arrPAF['remarks'][$i]=$val['remarks'];
						$i++;
					}			
					if (trim($val['new_payGrp']) !="") {
						$arrPAF['type'][$i]='branch,'.$val['refNo'];
						$arrPAF['stat'][$i]=$val['stat'];
						$arrPAF['field'][$i]="Pay Group";
						$arrPAF['value1'][$i]="Group " . $val['old_payGrp'];
						$arrPAF['value2'][$i]="Group " . $val['new_payGrp'];
						$arrPAF['effdate'][$i]=$val['effectivitydate'];
						$arrPAF['dateupdated'][$i]=$val['dateupdated'];
						$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
						$i++;
					}			
				}	
				unset($res,$val,$qryPAF);
			}	
		}
		if (empty($pafType) || $pafType == "position") {
			if (in_array($empNo,$this->arrPosition)){
			$qryPAF = "Select * from tblPAF_Position$hist where  empNo=$empNo $and order by effectivitydate";
			$compCode = $_SESSION['company_code'];
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				$pos_old = $this->getpositionwil("where compCode='$compCode' and posCode='{$val['old_posCode']}'",2);
				$pos_new = $this->getpositionwil("where compCode='$compCode' and posCode='{$val['new_posCode']}'",2);
				$division_new = $this->getDivDescArt($compCode, $pos_new['divCode']);
				$department_new = $this->getDeptDescArt($compCode, $pos_new['divCode'],$pos_new['deptCode']);
				$section_new =  $this->getSectDescArt($compCode, $pos_new['divCode'],$pos_new['deptCode'],$pos_new['sectCode']);
				$rank_new = $this->getRank($val['new_cat']);
				$level_new = "Level " . $val['new_level'];
				
				$division_old = $this->getDivDescArt($compCode, $pos_old['divCode']);
				$department_old = $this->getDeptDescArt($compCode, $pos_old['divCode'],$pos_old['deptCode']);
				$section_old =  $this->getSectDescArt($compCode, $pos_old['divCode'],$pos_old['deptCode'],$pos_old['sectCode']);
				$rank_old = $this->getRank($val['old_cat']);
				$level_old = "Level " . $val['old_level'];
				if (trim($val['new_posCode']) !="" && trim($val['new_posCode']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Position";
					$arrPAF['value1'][$i]=$pos_old['posDesc'];
					$arrPAF['value2'][$i]=$pos_new['posDesc'];;
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}				
				if (trim($pos_new['divCode']) !="" && trim($pos_new['divCode']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Division";
					$arrPAF['value1'][$i]=$division_old['deptDesc'];
					$arrPAF['value2'][$i]=$division_new['deptDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
				if (trim($pos_new['deptCode']) !="" && trim($pos_new['deptCode']) !=0) {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Department";
					$arrPAF['value1'][$i]=$department_old['deptDesc'];
					$arrPAF['value2'][$i]=$department_new['deptDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}	
				if (trim($pos_new['sectCode']) !="" && trim($pos_new['sectCode']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Section";
					$arrPAF['value1'][$i]=$section_old['deptDesc'];
					$arrPAF['value2'][$i]=$section_new['deptDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_cat']) !="" && trim($val['new_cat']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Rank ";
					$arrPAF['value1'][$i]=$rank_old['rankDesc'];
					$arrPAF['value2'][$i]=$rank_new['rankDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}									
				if (trim($val['new_level']) !="" && trim($val['new_level']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Level";
					$arrPAF['value1'][$i]=$level_old;
					$arrPAF['value2'][$i]=$level_new;
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}									
			}
			unset($res,$val,$qryPAF);
			}
		}
		if (empty($pafType) || $pafType == "payroll") {
			if (in_array($empNo,$this->arrPayroll)){
				$qryPAF = "Select *,
									CASE old_empPayType
									  WHEN 'D' THEN 'Daily'
									  WHEN 'M' THEN 'Monthly'
									END as oldPType,
									CASE new_empPayType
									  WHEN 'D' THEN 'Daily'
									  WHEN 'M' THEN 'Monthly'
									END as newPType
									 from tblPAF_PayrollRelated$hist where empNo=$empNo $and order by effectivitydate";
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				if (trim($val['new_empTeu']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="TAX Exemption";
					//$arrPAF['value1'][$i]=$this->EmpTEU($val['old_empTeu']);
					//$arrPAF['value2'][$i]=$this->EmpTEU($val['new_empTeu']);
					$arrPAF['value1'][$i]=$val['old_empTeu'];
					$arrPAF['value2'][$i]=$val['new_empTeu'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['reasonCd'];
					$i++;
				}			
				if (trim($val['new_empBankCd']) !="") {
					$bank_old =$this->getEmpBankArt($compCode,$val['old_empBankCd']);
					$bank_new =$this->getEmpBankArt($compCode,$val['new_empBankCd']);
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Bank";
					$arrPAF['value1'][$i]=$bank_old['bankDesc'];
					$arrPAF['value2'][$i]=$bank_new['bankDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['reasonCd'];
					$i++;
				}		
				if (trim($val['new_empAcctNo']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Account No.";
					$arrPAF['value1'][$i]=$val['old_empAcctNo'];
					$arrPAF['value2'][$i]=$val['new_empAcctNo'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['reasonCd'];
					$i++;
				}
				if (trim($val['new_empMrate']) !=0) {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Salary";
					if ($val['oldPType']=='Monthly') {
						$arrPAF['value1'][$i]=number_format($val['old_empMrate'],2) . " per month";
					} elseif ($val['oldPType']=='Daily') {
						$arrPAF['value1'][$i]=number_format($val['old_empDrate'],2) . " per day";
					} else {
						$arrchEmp = $this->checkEmpInfo($val['empNo']);
						if ($arrchEmp['empPayType']=='M') 
							$arrPAF['value1'][$i]=number_format($val['old_empMrate'],2) . " per month";
						else
							$arrPAF['value1'][$i]=number_format($val['old_empDrate'],2) . " per day";
					}
					if ($val['newPType']=='Monthly') {
						$arrPAF['value2'][$i]=number_format($val['new_empMrate'],2) . " per month";
					} elseif ($val['newPType']=='Daily') {
						$arrPAF['value2'][$i]=number_format($val['new_empDrate'],2) . " per day";
					} else {
						$arrchEmp = $this->checkEmpInfo($val['empNo']);
						if ($arrchEmp['empPayType']=='M') 
							$arrPAF['value2'][$i]=number_format($val['new_empMrate'],2) . " per month";
						else
							$arrPAF['value2'][$i]=number_format($val['new_empDrate'],2) . " per day";
					}
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['reasonCd'];
					$i++;
				}	
				if (trim($val['new_empPayType']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Rate Mode";
					$arrPAF['value1'][$i]=$val['oldPType'];
					$arrPAF['value2'][$i]=$val['newPType'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['reasonCd'];
					$i++;
				}									
				if (trim($val['new_payGrp']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Pay Group";
					$arrPAF['value1'][$i]="Group " . $val['old_payGrp'];
					$arrPAF['value2'][$i]="Group " . $val['new_payGrp'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['reasonCd'];
					$i++;
				}			
				if (trim($val['new_category']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrOldCat = $this->getPayCat($_SESSION['company_code'],"and payCat='".$val['old_category']."'");
					$arrNewCat = $this->getPayCat($_SESSION['company_code'],"and payCat='".$val['new_category']."'");
					$arrPAF['field'][$i]="Pay Category";
					$arrPAF['value1'][$i]=$arrOldCat['payCatDesc'];
					$arrPAF['value2'][$i]=$arrNewCat['payCatDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['reasonCd'];
					$i++;
				}			
			}	
			unset($res,$val,$qryPAF);		
			}
		}
		if (empty($pafType) || $pafType == "allow") {
			if (in_array($empNo,$this->arrAllow)){
				if ($and != ""){
					$and2 = str_replace('stat','stat',$and);
				}
			//$qryPAF = "Select * from tblPAF_Allowance$hist where empNo=$empNo $and2 order by effectivitydate";
			$qryPAF="Select * from tblPAF_Allowance$hist INNER JOIN tblAllowType ON tblPAF_Allowance$hist.allowCode = tblAllowType.allowCode where empNo=$empNo";
			$arrAllow = $this->getAllowType($_SESSION['company_code']);
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				$allowType = "";
				$arrEmpAllow = $this->getEmpAllow($val['empNo'],$val['allowCode']);
				foreach($arrAllow as $valAllow) {
					if ($valAllow['allowCode']==$val['allowCode'])
						$allowType = $valAllow['allowDesc'];
				}
				
				if($val['allowTag_type']=="D"){
					$allowtagtype=" per day";	
				}
				else{
					$allowtagtype=" per month";		
				}
				
				if($val['allowSked']==1){
					$sked="1st Payroll of the Month";	
				}
				elseif($val['allowSked']==2){
					$sked="2nd Payroll of the Month";	
				}
				else{
					$sked="Attendance Based";	
				}
				
				if($arrEmpAllow['allowSked']==1){
					$skedemp="1st Payroll of the Month";	
				}
				elseif($arrEmpAllow['allowSked']==2){
					$skedemp="2nd Payroll of the Month";	
				}
				else{
					$skedemp="Attendance Based";	
				}

				if ($val['allowAmt'] != $val['allowAmtold']) {
					$arrPAF['type'][$i]='allow,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="$allowType";
					$arrPAF['value1'][$i]=(number_format($val['allowAmtold'],2)=="0.00") ? "0.00":number_format($val['allowAmtold'],2).$allowtagtype;
					$arrPAF['value2'][$i]=(number_format($val['allowAmt'],2)=="0.00") ? "0.00":number_format($val['allowAmt'],2) . $allowtagtype;
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if ($val['allowSked'] != $arrEmpAllow['allowSked'] && $arrEmpAllow['allowSked'] !="") {
					$arrPAF['type'][$i]='allow,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Schedule";
					$arrPAF['value1'][$i]=$skedemp;//$arrEmpAllow['allowSked'];
					$arrPAF['value2'][$i]=$sked;//$val['allowSked'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if ($val['allowTag'] != $arrEmpAllow['allowTag'] && $arrEmpAllow['allowTag'] !="") {
					$arrPAF['type'][$i]='allow,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Allowance Tag";
					$arrPAF['value1'][$i]=($arrEmpAllow['allowTag']=='M') ? "Monthly":"Daily";
					$arrPAF['value2'][$i]=($val['allowTag']=='M') ? "Monthly":"Daily";
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}												
				if ($val['allowStat'] != $arrEmpAllow['allowStat'] && $arrEmpAllow['allowStat'] !="") {
					$arrPAF['type'][$i]='allow,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Allowance Status";
					$arrPAF['value1'][$i]=($arrEmpAllow['allowStat']=='A') ? "Active":"Held";
					$arrPAF['value2'][$i]=($val['allowStat']=='A') ? "Active":"Held";
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if ($val['allowPayTag'] != $arrEmpAllow['allowPayTag'] && $arrEmpAllow['allowPayTag'] !="") {
					$arrPAF['type'][$i]='allow,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Pay Tag";
					$arrPAF['value1'][$i]=($arrEmpAllow['allowPayTag']=='P') ? "Permanent":"Temporary";
					$arrPAF['value2'][$i]=($val['allowPayTag']=='P') ? "Permanent":"Temporary";
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}					
			}
			//unset($res,$val,$qryPAF);
			}
		}								
		return $arrPAF;
	}	
	function convertArr($table,$And) {
		$array = array();
		$qry = "SELECT $table.empNo from tblEmpMast INNER JOIN $table ON tblEmpMast.compCode = $table.compCode AND tblEmpMast.empNo = $table.empNo where tblEmpMast.compCode='{$_SESSION['company_code']}' $And";
		$res = $this->getArrRes($this->execQry($qry));
		foreach($res as $val) {
			$array[] = $val['empNo']; 
		}
		return $array;	
	}	
	
	function CountRec($stat) {
		$arrOthers 		= $this->convertArr("tblPAF_Others", " AND stat='$stat' AND effectivitydate <= '".date('Y-m-d')."'");
		$arrEmpStat		= $this->convertArr("tblPAF_EmpStatus", " AND stat='$stat' AND effectivitydate <= '".date('Y-m-d')."'");
		$arrBranch 		= $this->convertArr("tblPAF_Branch", " AND stat='$stat' AND effectivitydate <= '".date('Y-m-d')."'");
		$arrPosition	= $this->convertArr("tblPAF_Position", " AND stat='$stat' AND effectivitydate <= '".date('Y-m-d')."'");
		$arrPayroll 	= $this->convertArr("tblPAF_PayrollRelated", " AND stat='$stat' AND effectivitydate <= '".date('Y-m-d')."'");
		$arrAllow 		= $this->convertArr("tblPAF_Allowance", " AND stat='$stat' AND effectivitydate <= '".date('Y-m-d')."'");
		$result			= array_unique(array_merge($arrOthers,$arrOthers,$arrEmpStat,$arrBranch,$arrPosition,$arrPayroll,$arrAllow));
		
		return count($result);
	}
	
	function ReleasePAF($stat) {
		$qryupdatePAF = "";
		if ($stat=='R'){ 
			$datereleased = ",datereleased = '".date('Y-m-d')."'";
		}else{
			$datereleased = ",datereleased = NULL";
		}
		for($i=0;$i<=(int)$this->get['chCtr'];$i++) {
			if ($this->get["chPAF$i"] !="") {
				$arrStr = explode(',',$this->get["chPAF$i"]);
				switch($arrStr[0]) {
					case "others":
						$qryupdatePAF .= "Update tblPAF_Others set stat='$stat' $datereleased where refNo='{$arrStr[1]}'; ";	
					break;
					case "empstat":
						$qryupdatePAF .= "Update tblPAF_EmpStatus set stat='$stat' $datereleased where refNo='{$arrStr[1]}'; \n";	
					break;
					case "allow":
						$qryupdatePAF .= "Update tblPAF_Allowance set stat='$stat' $datereleased where refNo='{$arrStr[1]}';\n";	
					break;
					case "branch":
						$qryupdatePAF .= "Update tblPAF_Branch set stat='$stat' $datereleased where refNo='{$arrStr[1]}'; \n";	
					break;
					case "position":
						$qryupdatePAF .= "Update tblPAF_Position set stat='$stat' $datereleased where refNo='{$arrStr[1]}'; \n";	
					break;					
					case "payroll":
						$qryupdatePAF .= "Update tblPAF_PayrollRelated set stat='$stat' $datereleased where refNo='{$arrStr[1]}'; \n";	
					break;					
				}
				$this->execQry($qryupdatePAF);
			}	
				
		}
		//echo "$qryupdatePAF\n";
		//$qryupdatePAF="";
		return true;
	}
	
	function rollbackPAF(){
		for($i=0;$i<=(int)$this->get['chCtr'];$i++){
			if ($this->get["chPAF$i"] !="") {
				$arrStr = explode(',',$this->get["chPAF$i"]);
				switch($arrStr[0]) {
					case "others":
						$qryRollBackPAF .= "Update tblPAF_Others set stat='H', datereleased = NULL 
											where stat='R' and refNo='{$arrStr[1]}'; ";	
					break;
					case "empstat":
						$qryRollBackPAF .= "Update tblPAF_EmpStatus set stat='H', datereleased = NULL 
											where stat='R' and refNo='{$arrStr[1]}'; \n";	
					break;
					case "allow":
						$qryRollBackPAF .= "Update tblPAF_Allowance set stat='H', datereleased = NULL 
											where stat='R' and refNo='{$arrStr[1]}'; \n";	
					break;
					case "branch":
						$qryRollBackPAF .= "Update tblPAF_Branch set stat='H', datereleased = NULL
											where stat='R' and refNo='{$arrStr[1]}'; \n";	
					break;
					case "position":
						$qryRollBackPAF .= "Update tblPAF_Position set stat='H', datereleased = NULL
											where stat='R' and refNo='{$arrStr[1]}'; \n";	
					break;					
					case "payroll":
						$qryRollBackPAF .= "Update tblPAF_PayrollRelated set stat='H', datereleased = NULL
											where stat='R' and refNo='{$arrStr[1]}'; \n";	
					break;					
				}
				 $this->execMultiQryI($qryRollBackPAF);
			}
			
		}
		
	}
	
	function UpdatePAF() {
		for($i=0;$i<=(int)$this->get['chCtr'];$i++) {
			if ($this->get["chPAF$i"] !="") {
				$arrStr = explode(',',$this->get["chPAF$i"]);
				$this->ProcessPAF($arrStr[0]," AND refNo='{$arrStr[1]}'");
			}	
				
		}	
	}
	function checkEmpAllow($empNo,$allowCode){
		
		$qryCheckEmpAllow = "SELECT empNo FROM tblAllowance 
							 WHERE compCode = '{$_SESSION['company_code']}' 
							 AND empNo      = '$empNo' 
							 AND allowCode  = '$allowCode'";
		$resCheckEmpAllow = $this->execQryI($qryCheckEmpAllow);
		return $this->getRecCountI($resCheckEmpAllow);
	}	
	function checkEmpInfo($empNo) {
		 $sql = "Select empPayType from tblEmpMast where empNo='$empNo' and compCode='{$_SESSION['company_code']}'";
		return $this->getSqlAssoc($this->execQry($sql));
		
	}
	function getEmpAllow($empNo,$allowCode){
		
		$qryCheckEmpAllow = "SELECT * FROM tblAllowance 
							 WHERE compCode = '{$_SESSION['company_code']}' 
							 AND empNo      = '$empNo' 
							 AND allowCode  = '$allowCode'";
		$resCheckEmpAllow = $this->execQry($qryCheckEmpAllow);
		return $this->getSqlAssoc($resCheckEmpAllow);
	}	
	function getBio($empNo,$compCode) {
		$qryBio = "Select bioNumber from tblBioEmp where empNo='".trim($empNo)."' and compCode='".trim($compCode)."'";
		$res=$this->execQry($qryBio);
		return $this->getSqlAssoc($res);
	}
	function checkBioEmpExist($where)
	{
		$qryBio = "Select * from tblBioEmp where compCode='".$_SESSION["company_code"]."' and bioStat='A' $where";
		$res=$this->execQryI($qryBio);
		return $this->getSqlAssocI($res);
	}
}
?>