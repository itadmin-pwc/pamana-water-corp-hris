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
		$this->today = date('m/d/Y');
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
		$qrydel = "Delete from tblPAF_EmpStatus where refNo='{$this->get['refno']}' and compCode='{$this->get['compCode']}' and empNo='{$this->get['empNo']}'";
		$this->execQry($qrydel);		
			$qryempStat = "Insert into tblPAF_EmpStatus (controlNo,compCode,empNo,old_status,new_status,stat,effectivitydate,userid,remarks,refNo,dateadded) values ('{$this->get['ctrlno']}','{$this->get['compCode']}','{$this->get['empNo']}','{$this->get['oldstatus']}','{$this->get['cmbempstatus']}','{$this->get['cmbstatus']}','{$this->get['txtempstatDate']}','{$this->session['user_id']}','{$this->get['txtempstatremarks']}','{$this->get['refno']}','".date('m/d/Y')."')";
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
		$qrybranch = "Insert into tblPAF_Branch (controlNo,compCode,empNo $field ,stat,effectivitydate,userid,remarks,refNo,dateadded) values ('{$this->get['ctrlno']}','{$this->get['compCode']}','{$this->get['empNo']}' $value ,'{$this->get['cmbbrstatus']}','{$this->get['txtbrDate']}','{$this->session['user_id']}','{$this->get['txtbrremarks']}','{$this->get['refno']}','".date('m/d/Y')."')";
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
		$value = "'{$this->get['ctrlno']}','{$this->get['compCode']}','{$this->get['empNo']}','{$this->get['txtprremarks']}'";
		$empfield = " empNo='{$this->get['empNo']}'";
		if ($this->get['cmbpstatus'] !="0" && $this->get['cmbpstatus'] != $this->get['oldpayrollstatus']) {
			$field .= ",old_empPayType,new_empPayType";
			$value .= ",'{$this->get['oldpayrollstatus']}','{$this->get['cmbpstatus']}'";
			
		}
		if ($this->get['txtsalary'] !=0 && $this->get['txtsalary'] !="" && $this->get['txtdailyrate'] !=0 && $this->get['txtdailyrate'] !="" && $this->get['txtsalary'] != $this->get['oldmrate']) {
			$field .= ",new_empMrate,old_empMrate,new_empDrate,old_empDrate,new_empHrate,old_empHrate";
			$value .= ",'" . str_replace(',','',$this->get['txtsalary']) . "','" . str_replace(',','',$this->get['oldmrate']) . "','" . str_replace(',','',$this->get['txtdailyrate']) . "','" . str_replace(',','',$this->get['olddrate']) . "','" . str_replace(',','',$this->get['txthourlyrate']) . "','" . str_replace(',','',$this->get['oldhrate']) . "'";
			
		}
		if ($this->get['cmbexemption'] !="0" && $this->get['cmbexemption'] != $this->get['oldteu']) {
			$field .= ",old_empTeu,new_empTeu";
			$value .= ",'{$this->get['oldteu']}','{$this->get['cmbexemption']}'";
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
		 $qrypayroll = "Insert into tblPAF_PayrollRelated ($field,stat,effectivitydate,userid,refNo,reasonCd,dateadded) values ($value,'" . $this->get['cmbprstatus'] . "','" . $this->get['txtprDate'] . "','" . $this->session['user_id'] . "','" . $this->get['refno'] . "','" . $this->get['cmbreason'] . "','".date('m/d/Y')."')";
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
		$value = "'{$this->get['ctrlno']}','{$this->get['compCode']}','{$this->get['empNo']}','{$this->get['txtothremarks']}'";
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
		if ($this->get['txtadd1'] !="" && $this->get['txtadd1'] != $this->get['old_txtadd1']) {
			$field .= ",old_empAddr1,new_empAddr1";
			$value .= ",'".str_replace("'","''",stripslashes($this->get['old_txtadd1']))."','".str_replace("'","''",stripslashes($this->get['txtadd1']))."'";
		}
		if ($this->get['txtadd2'] !="" && $this->get['txtadd2'] != $this->get['old_txtadd2']) {
			$field .= ",old_empAddr2,new_empAddr2";
			$value .= ",'".str_replace("'","''",stripslashes($this->get['old_txtadd2']))."','".str_replace("'","''",stripslashes($this->get['txtadd2']))."'";
		}
		if ($this->get['cmbcity'] !="0" && $this->get['cmbcity'] != $this->get['old_cmbcity']) {
			$field .= ",old_empCityCd,new_empCityCd";
			$value .= ",'{$this->get['old_cmbcity']}','{$this->get['cmbcity']}'";
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
		$qrydel = "Delete from tblPAF_Others where refNo='{$this->get['refno']}' and compCode='{$this->get['compCode']}' and empNo='{$this->get['empNo']}'";
		$this->execQry($qrydel);			
		$qryothers = "Insert into tblPAF_Others ($field,stat,effectivitydate,userid,refNo,dateadded) values ($value,'{$this->get['cmbothstatus']}','{$this->get['txtothDate']}','{$this->session['user_id']}','{$this->get['refno']}','".date('m/d/Y')."')";
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
		$value = "'{$this->get['ctrlno']}','{$this->get['compCode']}','{$this->get['empNo']}','{$this->get['txtposremarks']}'";
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
		$qryothers = "Insert into tblPAF_Position ($field,stat,effectivitydate,userid,refNo,dateadded) values ($value,'{$this->get['cmbposstatus']}','{$this->get['txtposDate']}','{$this->session['user_id']}','{$this->get['refno']}','".date('m/d/Y')."')";
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
		$Trns = $this->beginTran();

		for($i=0;$i<=(int)$this->get['chCtr'];$i++) {
			if ($this->get["chPAF$i"] !="") {
				$arrStr = explode(',',$this->get["chPAF$i"]);
				$act = $arrStr[0];
				$and = " AND refNo='{$arrStr[1]}'";
				switch ($act) {
					case "empstat":
						$qrydata = "Select * from tblPAF_EmpStatus where  stat='R' and compCode='{$this->get['compCode']}' $and";
						$arrproc = $this->getArrRes($this->execQry($qrydata));
						$qryempstat = "";
						$qryempMast = "";
						foreach ($arrproc as $val) {
							$qryprocess .= "Insert into tblPAF_EmpStatushist (controlNo,compCode,empNo,old_status,new_status,effectivitydate,user_created,user_updated,remarks,dateadded,refNo,dateupdated,datereleased) values ('{$val['controlNo']}','{$val['compCode']}','{$val['empNo']}','{$val['old_status']}','{$val['new_status']}','{$val['effectivitydate']}','{$val['user_id']}','{$this->session['user_id']}','{$val['remarks']}','{$val['dateadded']}','{$val['refNo']}','{$this->today}','{$val['datereleased']}');";
							
							if ($val['new_status'] == 'RS' || $val['new_status'] == 'TR'  || $val['new_status'] == 'AWOL')
								$dtResigned = ",dateResigned='{$val['effectivitydate']}'";
							elseif ($val['new_status'] == 'EOC')
								$dtResigned = ",endDate='{$val['effectivitydate']}'";
							elseif ($val['new_status'] == 'RG')
								$dtResigned = ",dateReg='{$val['effectivitydate']}'";

							$qryempMast .= "Update tblEmpMast set empStat='{$val['new_status']}' $dtResigned where empNo='{$val['empNo']}' and compCode='{$val['compCode']}';";
							unset($dtResigned);
						}
						$qrydel = "delete from tblPAF_EmpStatus where  stat='R'  and compCode='{$this->get['compCode']}' $and";
					break;
					case "payroll":
						$qrydata = "Select * from tblPAF_PayrollRelated where  stat='R' and compCode='{$this->get['compCode']}' $and";
						$qryempMast = "";
						$arrproc = $this->getArrRes($this->execQry($qrydata));
						$field = "compCode";
						$value = "'{$this->get['compCode']}'";
						foreach ($arrproc as $val) {
							$empfield = " compCode='{$this->get['compCode']}'";
							if (trim($val['new_empPayType']) !="") {
								$empfield .= ",empPayType='{$val['new_empPayType']}'";
								
							}
							if ($val['new_empMrate'] !=0 && trim($val['new_empMrate']) != "") {
								$empfield .= ",empMrate={$val['new_empMrate']}, empDrate={$val['new_empDrate']}, empHrate={$val['new_empHrate']}";
								$minWage .= " Exec sp_MinWage '{$val['empNo']}',{$val['new_empDrate']},'{$this->get['compCode']}'";
								
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
							$qryempMast .= "Update tblEmpMast set $empfield where empNo='{$val['empNo']}' and compCode='{$val['compCode']}';";	
						}
						$qryempMast .= $minWage;
						$qryprocess = "	Insert into tblPAF_PayrollRelatedhist 
						(controlNo,compCode, empNo, old_empTeu, old_empBankCd, old_empAcctNo, old_empMrate, 
						old_empDrate, old_empHrate, old_empPayType, old_empPayGrp, old_category ,new_empTeu, 
						new_empBankCd, new_empAcctNo, new_empMrate, new_empDrate, new_empHrate, 
						new_empPayType, new_empPayGrp,new_category ,effectivitydate, remarks, user_created,dateadded,refNo,reasonCd,user_updated,dateupdated,datereleased)
						
						Select controlNo,compCode, empNo, old_empTeu, old_empBankCd, old_empAcctNo, old_empMrate, 
						old_empDrate, old_empHrate, old_empPayType, old_empPayGrp, old_empPayGrp, new_empTeu, new_empBankCd,
						new_empAcctNo, new_empMrate, new_empDrate, new_empHrate, new_empPayType, 
						new_empPayGrp,new_category,effectivitydate, remarks, userid,dateadded,refNo,reasonCd,'{$this->session['user_id']}','{$this->today}',datereleased from tblPAF_PayrollRelated  
						where  stat='R' and compCode='{$this->get['compCode']}' $and;";	
						$qrydel = "delete from tblPAF_PayrollRelated where  stat='R' and compCode='{$this->get['compCode']}' $and;";
					break;
					
					case "others":
						$qrydata = "Select * from tblPAF_Others where  stat='R' and compCode='{$this->get['compCode']}' $and";
						$arrproc = $this->getArrRes($this->execQry($qrydata));
						$qryempstat = "";
						$qryempMast = "";
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
							$qryempMast .= "Update tblEmpMast set $empfield where empNo='{$val['empNo']}' and compCode='{$this->get['compCode']}';";	
						}	
						 $qryprocess = "	Insert into tblPAF_Othershist 
										(controlNo,compCode, empNo, old_empLastName, old_empFirstName, old_empMidName, old_empAddr1, 
										old_empAddr2, old_empCityCd, old_empTin,old_empSssNo, old_empPhicNo, old_empPagibig,
										new_empLastName, new_empFirstName, new_empMidName, new_empAddr1, new_empAddr2,
										new_empCityCd, new_empTin, new_empSssNo, new_empPhicNo, new_empPagibig, stat, 
										effectivitydate, remarks, dateadded, user_created,refNo,user_updated,dateupdated,datereleased)
										
										Select  controlNo,compCode, empNo, old_empLastName, old_empFirstName, old_empMidName, old_empAddr1, 
										old_empAddr2, old_empCityCd, old_empTin,old_empSssNo, old_empPhicNo, old_empPagibig,
										new_empLastName, new_empFirstName, new_empMidName, new_empAddr1, new_empAddr2,
										new_empCityCd, new_empTin, new_empSssNo, new_empPhicNo, new_empPagibig, stat, 
										effectivitydate, remarks, dateadded, userid,refNo,'{$this->session['user_id']}','{$this->today}',datereleased from tblPAF_Others 
										where  stat='R' and compCode='{$this->get['compCode']}' $and;";
						$qrydel = "delete from tblPAF_Others where  stat='R' and compCode='{$this->get['compCode']}' $and;";
					break;
					
					case "position":
						$qrydata = "Select * from tblPAF_Position where  stat='R' and compCode='{$this->get['compCode']}' $and";
						$arrproc = $this->getArrRes($this->execQry($qrydata));
						$qryempstat = "";
						$qryempMast = "";
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
						$qryprocess = "	Insert into tblPAF_Positionhist 
										(controlNo,compCode, empNo, old_divCode, old_deptCode, old_secCode, old_cat, old_level, old_posCode, 
										new_divCode, new_DeptCode, new_secCode, new_cat, new_level, new_posCode, stat, effectivitydate, 
										remarks, dateadded, user_created,refNo,user_updated,dateupdated,datereleased)
										Select  controlNo,compCode, empNo, old_divCode, old_deptCode, old_secCode, old_cat, old_level, old_posCode, 
										new_divCode, new_DeptCode, new_secCode, new_cat, new_level, new_posCode, stat, effectivitydate, 
										remarks, dateadded, userid,refNo,'{$this->session['user_id']}','{$this->today}',datereleased from tblPAF_Position 
										where  stat='R' and compCode='{$this->get['compCode']}';";
						$qrydel = "delete from tblPAF_Position where  stat='R' and compCode='{$this->get['compCode']}' $and;";			
					break;
					
					case "branch":
						$qrydata = "Select * from tblPAF_Branch where stat='R' and compCode='{$this->get['compCode']}' $and";
						$arrproc = $this->getArrRes($this->execQry($qrydata));
						$qryempstat = "";
						$qryempMast = "";
						foreach ($arrproc as $val) {
							$empfield = " compCode='{$this->get['compCode']}'";
							if ($val['old_branchCode'] !="") {
								$empfield .= ",empBrnCode='{$val['new_branchCode']}'";
							}	
							if ($val['old_payGrp'] !="") {
								$empfield .= ",empPayGrp='{$val['new_payGrp']}'";
							}
							$qryempMast .= "Update tblEmpMast set $empfield where empNo='{$val['empNo']}' and compCode='{$this->get['compCode']}';";	
						}	
						$qryprocess = "	Insert into tblPAF_Branchhist 
										(controlNo,compCode, empNo, old_branchCode, old_payGrp, new_branchCode, new_payGrp, stat, 
										effectivitydate, remarks, dateadded, user_created, refNo,user_updated,dateupdated,datereleased)
										Select  controlNo,compCode, empNo, old_branchCode, old_payGrp, new_branchCode, new_payGrp, 
										stat, effectivitydate, remarks, dateadded, userid, refNo,'{$this->session['user_id']}','{$this->today}',datereleased from tblPAF_Branch
										where  stat='R' and compCode='{$this->get['compCode']}' $and;";
						$qrydel = "delete from tblPAF_Branch where  stat='R' and compCode='{$this->get['compCode']}' $and;";			
					break;
					case "allow":
						$qryempstat = "";
						$qryempMast = "";
						$qryAllow = " Select 
									compCode, empNo, allowCode, allowAmt,allowAmtold, allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd, allowStat,
									sprtPS, refNo, controlNo, effectivitydate,'$today','$today',userid,'{$_SESSION['user_id']}',allowTag,stat from tblPAF_Allowance where compCode='{$_SESSION['company_code']}' and stat='R' $and
																	
									";
						$resAllw = $this->getArrRes($this->execQry($qryAllow));
						foreach($resAllw as $val) {
							$qryprocess .= "Insert into tblPAF_Allowancehist  (compCode, empNo, allowCode, allowAmt,allowAmtold, allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd, allowStat, sprtPS, refNo, controlNo, effectivitydate, dateadded, dateupdated, user_created, user_updated,allowTag,stat) values
									('{$val['compCode']}', '{$val['empNo']}', '{$val['allowCode']}', '{$val['allowAmt']}', '{$val['allowAmtold']}', '{$val['allowSked']}', '{$val['allowTaxTag']}', '{$val['allowPayTag']}', '{$val['allowStart']}', '{$val['allowEnd']}', '{$val['allowStat']}', 
									'{$val['sprtPS']}', '{$val['refNo']}', '{$val['controlNo']}', '{$val['effectivitydate']}', '{$this->today}', '{$this->today}', '{$val['userid']}', '{$_SESSION['user_id']}', '{$val['allowTag']}', '{$val['stat']}'); ";
							if ($this->checkEmpAllow($val['empNo'],$val['allowCode'])==0) {
								$qryempMast .= "Insert into tblAllowance
													(compCode, empNo, allowCode, allowAmt, allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd, 
													allowStat, sprtPS,allowTag) values
													('{$val['compCode']}', '{$val['empNo']}', '{$val['allowCode']}', '{$val['allowAmt']}', '{$val['allowSked']}', '{$val['allowTaxTag']}', '{$val['allowPayTag']}', '{$val['allowStart']}', '{$val['allowEnd']}', '{$val['allowStat']}','{$val['sprtPS']}','{$val['allowTag']}');";
							} else {
								$qryempMast .= "Update tblAllowance set allowAmt='{$val['allowAmt']}',allowTag='{$val['allowTag']}',allowSked='{$val['allowSked']}',allowTaxTag='{$val['allowTaxTag']}',sprtPS='{$val['sprtPS']}',allowStat='{$val['allowStat']}',allowPayTag='{$val['allowPayTag']}' where compCode = '{$_SESSION['company_code']}' AND empNo = '{$val['empNo']}'  AND allowCode  = '{$val['allowCode']}'; "; 
							}
						
						}
						
						$qrydel = "Delete from tblPAF_Allowance where compCode='{$_SESSION['company_code']}' and stat='R' $and;";					
					break;
				}
				if ($Trns) {
					$Trns = $this->execQry($qryprocess);
				}
				if ($Trns) {
					$Trns = $this->execQry($qryempMast);
				}
				if ($Trns) {
					$Trns = $this->execQry($qrydel);
				}

			}	
		}	

		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
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
				if (trim($val['new_empAddr1']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Home No, Bldg., Street";
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
					$arrPAF['field'][$i]="Barangay, Municipality";
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
					$arrPAF['field'][$i]="City";
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
					$arrPAF['field'][$i]="Phil Health No.";
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
			}
			unset($res,$val,$qryPAF);
			}
		}
		if (empty($pafType) || $pafType == "allow") {
			if (in_array($empNo,$this->arrAllow)){
				if ($and != "")
					$and2 = str_replace('stat','stat',$and);
					
				$qryPAF = "Select * from tblPAF_Allowance$hist where empNo=$empNo $and2 order by effectivitydate";
			$arrAllow = $this->getAllowType($_SESSION['company_code']);
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				$allowType = "";
				$arrEmpAllow = $this->getEmpAllow($val['empNo'],$val['allowCode']);
				foreach($arrAllow as $valAllow) {
					if ($valAllow['allowCode']==$val['allowCode'])
						$allowType = $valAllow['allowDesc'];
				}
				if ($val['allowAmt'] != $val['allowAmtold']) {
					$arrPAF['type'][$i]='allow,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="$allowType";
					$arrPAF['value1'][$i]=$val['allowAmtold'];
					$arrPAF['value2'][$i]=$val['allowAmt'];
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
					$arrPAF['value1'][$i]=$arrEmpAllow['allowSked'];
					$arrPAF['value2'][$i]=$val['allowSked'];
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
				$department_new = $this->getDeptDescArt($compCode, $pos_new['divCode'],$val['new_DeptCode']);
				$section_new =  $this->getSectDescArt($compCode, $pos_new['divCode'],$val['new_DeptCode'],$val['new_secCode']);
				$rank_new = $this->getRank($val['new_cat']);
				$level_new = "Level " . $val['new_level'];
				
				$division_old = $this->getDivDescArt($compCode, $pos_old['divCode']);
				$department_old = $this->getDeptDescArt($compCode, $pos_old['divCode'],$val['old_DeptCode']);
				$section_old =  $this->getSectDescArt($compCode, $pos_old['divCode'],$val['old_DeptCode'],$val['old_secCode']);
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
				if (trim($val['new_divCode']) !="" && trim($val['new_divCode']) !="0") {
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
				if (trim($val['new_DeptCode']) !="" && trim($val['new_DeptCode']) !=0) {
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
				if (trim($val['new_secCode']) !="" && trim($val['new_secCode']) !="0") {
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
									oldPType= 
									CASE old_empPayType
									  WHEN 'D' THEN 'Daily'
									  WHEN 'M' THEN 'Monthly'
									END,
									newPType= 
									CASE new_empPayType
									  WHEN 'D' THEN 'Daily'
									  WHEN 'M' THEN 'Monthly'
									END
									 from tblPAF_PayrollRelated$hist where empNo=$empNo $and order by effectivitydate";
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				if (trim($val['new_empTeu']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="TEU";
					$arrPAF['value1'][$i]=$val['old_empTeu'];
					$arrPAF['value2'][$i]=$val['new_empTeu'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
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
					$arrPAF['remarks'][$i]=$val['remarks'];
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
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empMrate']) !=0) {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Salary";
					if ($val['oldPType']=='Monthly') {
						$arrPAF['value1'][$i]=number_format($val['old_empMrate'],2) . " /Monthly Rate";
					} elseif ($val['oldPType']=='Daily') {
						$arrPAF['value1'][$i]=number_format($val['old_empDrate'],2) . " /Daily Rate";
					} else {
						$arrchEmp = $this->checkEmpInfo($val['empNo']);
						if ($arrchEmp['empPayType']=='M') 
							$arrPAF['value1'][$i]=number_format($val['old_empMrate'],2) . " /Monthly Rate";
						else
							$arrPAF['value1'][$i]=number_format($val['old_empDrate'],2) . " /Daily Rate";
					}
					if ($val['newPType']=='Monthly') {
						$arrPAF['value2'][$i]=number_format($val['new_empMrate'],2) . " /Monthly Rate";
					} elseif ($val['newPType']=='Daily') {
						$arrPAF['value2'][$i]=number_format($val['new_empDrate'],2) . " /Daily Rate";
					} else {
						$arrchEmp = $this->checkEmpInfo($val['empNo']);
						if ($arrchEmp['empPayType']=='M') 
							$arrPAF['value2'][$i]=number_format($val['new_empMrate'],2) . " /Monthly Rate";
						else
							$arrPAF['value2'][$i]=number_format($val['new_empDrate'],2) . " /Daily Rate";
					}
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
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
					$arrPAF['remarks'][$i]=$val['remarks'];
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
					$arrPAF['remarks'][$i]=$val['remarks'];
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
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
			}			
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
		$arrOthers 		= $this->convertArr("tblPAF_Others", " AND stat='$stat' AND effectivitydate <= '".date('m/d/Y')."'");
		$arrEmpStat		= $this->convertArr("tblPAF_EmpStatus", " AND stat='$stat' AND effectivitydate <= '".date('m/d/Y')."'");
		$arrBranch 		= $this->convertArr("tblPAF_Branch", " AND stat='$stat' AND effectivitydate <= '".date('m/d/Y')."'");
		$arrPosition	= $this->convertArr("tblPAF_Position", " AND stat='$stat' AND effectivitydate <= '".date('m/d/Y')."'");
		$arrPayroll 	= $this->convertArr("tblPAF_PayrollRelated", " AND stat='$stat' AND effectivitydate <= '".date('m/d/Y')."'");
		$arrAllow 		= $this->convertArr("tblPAF_Allowance", " AND stat='$stat' AND effectivitydate <= '".date('m/d/Y')."'");
		$result			= array_unique(array_merge($arrOthers,$arrOthers,$arrEmpStat,$arrBranch,$arrPosition,$arrPayroll,$arrAllow));
		
		return count($result);
	}
	
	function ReleasePAF($stat) {
		$qryupdatePAF = "";
		if ($stat=='R') 
			$datereleased = ",datereleased = '".date('m/d/Y')."'";
		else
			$datereleased = ",datereleased = NULL";
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
						$qryupdatePAF .= "Update tblPAF_Allowance set stat='$stat' $datereleased where refNo='{$arrStr[1]}'; \n";	
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
			}	
				
		}
		return $this->execQry($qryupdatePAF);
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
		$resCheckEmpAllow = $this->execQry($qryCheckEmpAllow);
		return $this->getRecCount($resCheckEmpAllow);
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
}

?>