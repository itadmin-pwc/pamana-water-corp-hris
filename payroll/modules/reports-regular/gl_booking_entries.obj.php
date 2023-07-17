<?
class generateBooking extends commonObj {
	
	var $get;//method
	var $session;//session variables
	var $empList;
	var $hist;
	/**
	 * pass all the get variables and session variables 
	 *
	 * @param string $method
	 * @param array variable  $sessionVars
	 */

	
	private function getCutOffPeriod(){

		if((int)trim((int)trim($this->get['pdNumber']))%2){
			return  1;
		}
		else{
			return 2;
		}	
	}	
	
	function OpenPayPeriod() {
		if ((int)$this->get['pdNumber']==24) {
			$pdYear=(int)$this->get['pdYear'] + 1;
			$pdNum=1;
		}
		else {
			$pdYear=(int)$this->get['pdYear'];
			$pdNum=(int)$this->get['pdNumber'] + 1;
		}
		$qryOpen="Update tblPayPeriod set pdStat='O', odTSStat='O' 
			where (compCode = '" . $this->session['company_code'] . "') 
			AND (pdYear = '" . $pdYear . "') 
			AND (pdNumber = '" . $pdNum . "')
			AND (payGrp='" . $this->session['pay_group'] . "')
			AND (payCat='" . $this->session['pay_category'] . "')
		";
		return $this->execQry($qryOpen);
	}
	
	function getbrndivCode($table) {
		 $qryCodes = "Select empNo,empBrnCode,empDiv,empLocCode from tblEmpMast where empNo IN (Select empNo from $table where 
					compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') AND (payGrp='" . $this->session['pay_group'] . "')
			AND (payCat='" . $this->session['pay_category'] . "'))";
		return $this->getArrRes($this->execQry($qryCodes));
	}
	function getcompGlCode() {
		$qrycompGlCode = "Select gLCode from tblCompany where compCode='{$_SESSION['company_code']}'";
		$glCode = $this->getSqlAssoc($this->execQry($qrycompGlCode));
		return $glCode['gLCode'];
	}
	function getMinorCode($divCode) {
		$qryMinorCode = "Select deptGlCode from tblDepartment where divCode='$divCode' and deptLevel=2";
		$glCode = $this->getSqlAssoc($this->execQry($qryMinorCode));
		return $glCode['deptGlCode'];
	}
	function getStoreCode($brnCode,$locCode) {
		$qryStoreCode = "Select compglCode,glCodeStr,glCodeHO,compglCodeHO from tblBranch where compCode='{$_SESSION['company_code']}' and brnCode='$brnCode' \n";
		$glCode = $this->getSqlAssoc($this->execQry($qryStoreCode));
		if ($locCode=="0001") {
			$Code['glstrCode'] = $glCode['glCodeHO'];
			$Code['glcompCode'] = $glCode['compglCodeHO'];
		} else {
			$Code['glstrCode'] = $glCode['glCodeStr'];
			$Code['glcompCode'] = $glCode['compglCode'];
		}
		return $Code;
	}
		
	function getEarnings(){
		 $qryEarnings = "Insert into wPayJournal1 (compCode,empNo,trnCode,Amount,pdYear,pdNumber,payGrp,payCat) Select '{$_SESSION['company_code']}',empNo,trnCode,trnAmountE,'{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}' from tblEarnings{$this->hist} where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and empNo IN ({$this->empList})";
		return $this->execQry($qryEarnings);
	}

	function getEarnMajCode() {
		$qryEarnMajCode = "Insert into wPayJournal2 (compCode,empNo,majCode,pdYear,pdNumber,payGrp,payCat,Amount) 
							Select wPayJournal1.compCode,empNo,trnGlCode,pdYear,pdNumber,payGrp,payCat,sum(Amount) as Amount from wPayJournal1 inner join tblPayTransType on
							wPayJournal1.trnCode = tblPayTransType.trnCode and wPayJournal1.compCode = tblPayTransType.compCode
							group by wPayJournal1.compCode,empNo,trnGlCode,pdYear,pdNumber,payGrp,payCat having wPayJournal1.compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') 
							AND (pdNumber = '{$this->get['pdNumber']}') AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		return $this->execQry($qryEarnMajCode);
	}
	function getEarnOtherCodes() {
		$qryOtherCodes = "Select * from wPayJournal2 where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') 
							AND (pdNumber = '{$this->get['pdNumber']}') AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrEarnOtherCodes =$this->getArrRes($this->execQry($qryOtherCodes));
		$arrCodes = $this->getbrndivCode("wPayJournal2");
		if ($this->getCutOffPeriod()==2  && $_SESSION['pay_category']!=9) {
			$arrEmpGovList = $this->EmGovContList();
		}
		$qryUpdate = "";
		$InsertMtdGoData = "";
		foreach ($arrEarnOtherCodes as $val) {
			foreach($arrCodes as $valCode) {
				if ($val['empNo']==$valCode['empNo']) {
					if ($valCode['empLocCode']=="0001") {
						$brnCode = '3';
						$locCode = '0001';
					} else {
						$brnCode = $valCode['empBrnCode'];
						$locCode = $valCode['empLocCode'];
					}
					
					$qryUpdate .= "Update wPayJournal2 set divCode='".$valCode['empDiv']."',brnCode='".$brnCode."',
								   locCode='".$locCode."' where empNo='".$val['empNo']."' and compCode='{$_SESSION['company_code']}' 
								   AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}')";
					if ($this->getCutOffPeriod()==2 && $_SESSION['pay_category']!=9) {
						foreach($arrEmpGovList as $valGov) {
							if ($val['empNo']==$valGov['empNo']) {
								$InsertMtdGoData .= "Insert into wGovJm 
													(compCode,empNo,pdYear,pdNumber,payGrp,payCat,divCode,brnCode,locCode,sssEmplr,phicEmplr,hdmfEmplr,ec)
													values
													({$_SESSION['company_code']},'{$val['empNo']}','{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}','{$valCode['empDiv']}','$brnCode','$locCode','{$valGov['sssEmplr']}','{$valGov['phicEmplr']}','{$valGov['hdmfEmplr']}','{$valGov['ec']}'); \n\n
													";
							}
						}
					}
				}
			}
		}
		if ($this->getCutOffPeriod()==2  && $_SESSION['pay_category']!=9) {
			$this->execQry($InsertMtdGoData);
		}
		
		return $this->execQry($qryUpdate);
	}
	function summarizeEarningsbyEmp() {
		$qrySummarizeEmp ="Insert into wPayJournal3 (compCode,divCode,brnCode,locCode,majCode,Amount,pdYear,pdNumber,payGrp,payCat) 
						Select compCode,divCode,brnCode,locCode,majCode,sum(Amount) as Amount,pdYear,pdNumber,payGrp,payCat from wPayJournal2 
						group by compCode,divCode,brnCode,locCode,majCode,pdYear,pdNumber,payGrp,payCat having compCode='{$_SESSION['company_code']}' 
						AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';";
		return $this->execQry($qrySummarizeEmp);
	}
 
	function EarnConvertToGLCodes() {
		$qryEarningsData = "Select * from wPayJournal3 where compCode='{$_SESSION['company_code']}' 
						AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrEarningsData = $this->getArrRes($this->execQry($qryEarningsData));
		$qryInsertEarningstoPayJournal = "";
		foreach($arrEarningsData as $val) {
			$minorCode = $this->getMinorCode($val['divCode']);
			$glCode = $this->getStoreCode($val['brnCode'],$val['locCode']);
			$storeCode = $glCode['glstrCode'];
			$compGlCode = $glCode['glcompCode'];
			
			 $qryInsertEarningstoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, minCode, strCode, Amount) values
									('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '{$val['majCode']}', '".$minorCode."', '".$storeCode."', '{$val['Amount']}'); \n\n";
		}
		return  $this->execQry($qryInsertEarningstoPayJournal);
						
	}
	function getDeductions() {
		$qryDeductions = "Insert into wPayJournal2d (compCode,empNo, trnCode, Amount, pdYear, pdNumber, payGrp, payCat) 
						Select compCode,empNo,trnCode,sum(trnAmountD) as trnAmountD,'{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}' from tblDeductions{$this->hist} where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and empNo IN ({$this->empList})
		group By compCode,empNo, pdYear, pdNumber, trnCode";
		return $this->execQry($qryDeductions);
	}
	function getDedOtherCodes() {
		$qryOtherCodes = "Select * from wPayJournal2d where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') 
							AND (pdNumber = '{$this->get['pdNumber']}') AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrDedOtherCodes =$this->getArrRes($this->execQry($qryOtherCodes));
		$arrCodes = $this->getbrndivCode("wPayJournal2d");
		$qryUpdate = "";
		foreach ($arrDedOtherCodes as $val) {
			foreach($arrCodes as $valCode) {
				if ($val['empNo']==$valCode['empNo']) {
					if ($valCode['empLocCode']=="0001") {
						$brnCode = '3';
						$locCode = '0001';
					} else {
						$brnCode = $valCode['empBrnCode'];
						$locCode = $valCode['empLocCode'];
					}
					$qryUpdate .= "Update wPayJournal2d set brnCode='".$brnCode."',
								   locCode='".$locCode."' where empNo='".$val['empNo']."' and compCode='{$_SESSION['company_code']}' 
								   AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}'; ";
				}
			}
		}
		return  $this->execQry($qryUpdate);
	}

	function SummarizeDeductions() {
		$qrySummarizeDed = "Insert into wPayJournal3d (compCode,trnCode,locCode,brnCode,Amount,pdYear,pdNumber,payGrp,payCat) 
						Select compCode,trnCode,locCode,brnCode,sum(Amount) as Amount,pdYear,pdNumber,payGrp,payCat from wPayJournal2d 
						group by compCode,trnCode,locCode,brnCode,pdYear,pdNumber,payGrp,payCat having compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' ;";
		return $this->execQry($qrySummarizeDed);
	}
	
	function InsertDeductiontoPayJournal() {
		 $qrySummarizeDed = "SELECT * from view_PayJournal
							WHERE compCode='{$_SESSION['company_code']}'  AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and payCat='{$_SESSION['pay_category']}' and payGrp='{$_SESSION['pay_group']}'";
		$arrDedData = $this->getArrRes($this->execQry($qrySummarizeDed));
		$qryInsertDedtoPayJournal = "";
		foreach($arrDedData as $val) {
			$minorCode = $val['minCode'];
			$majCode = $val['trnGlCode'];
			$glCode = $this->getStoreCode($val['brnCode'],$val['locCode']);
			$storeCode = $glCode['glstrCode'];
			$compGlCode = $glCode['glcompCode'];
			$Amount = (float)$val['Amount'] * -1;
			$qryInsertDedtoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, minCode, strCode, Amount) values
									('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCode', '".$minorCode."', '".$storeCode."', '$Amount'); \n\n";
		}
	return  $this->execQry($qryInsertDedtoPayJournal);
	}
	function SummarizedEmpGovCont() {
		$sqlQrySummarize = "Insert into wGovJmS (compCode,pdYear,pdNumber,payGrp,payCat,divCode,brnCode,locCode,sssEmplr,phicEmplr,hdmfEmplr,ec)
							Select compCode,pdYear,pdNumber,payGrp,payCat,divCode,brnCode,locCode,sum(sssEmplr) as sssEmplr,sum(phicEmplr) as phicEmplr,sum(hdmfEmplr) as hdmfEmplr,sum(ec) as ec from  wGovJm where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'  group by compCode,pdYear,pdNumber,payGrp,payCat,divCode,brnCode,locCode ";
		return  $this->execQry($sqlQrySummarize);
	}
	function InsertEmpGovConttoPayJournal() {
		$qryEmpGovList = "Select * from wGovJmS where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrEmpGovList = $this->getArrRes($this->execQry($qryEmpGovList));
		$qryInsertGovConttoPayJournal = "";
		foreach($arrEmpGovList as $val) {
			$minorCode 	= $this->getMinorCode($val['divCode']);
			$glCode 	= $this->getStoreCode($val['brnCode'],$val['locCode']);
			$storeCode 	= $glCode['glstrCode'];
			$compGlCode	= $glCode['glcompCode'];
			$majCodePH	= "720";
			$majCodeHDMF= "725";
			$majCodeSSS	= "715";
			$AmtPH		= $val['phicEmplr'];
			$AmtHDMF	= $val['hdmfEmplr'];
			$AmtSSS		= (float)$val['sssEmplr'] + (float)$val['ec'];
			
			//PhilHealth
			$qryInsertGovConttoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, minCode, strCode, Amount) values
									('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCodePH', '".$minorCode."', '".$storeCode."', '$AmtPH'); ";
									
			//HDMF
			$qryInsertGovConttoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, minCode, strCode, Amount) values
									('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCodeHDMF', '".$minorCode."', '".$storeCode."', '$AmtHDMF'); ";
			
			//SSS and ec						
			$qryInsertGovConttoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, minCode, strCode, Amount) values
									('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCodeSSS', '".$minorCode."', '".$storeCode."', '$AmtSSS'); ";
		}
		return  $this->execQry($qryInsertGovConttoPayJournal);
		
		
	}
	function InsertNetPay() {
		$qryNetPay = "Select compGLCode,strCode,Sum(Amount) as Amount from tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' group by compGLCode,strCode";
		$arrNetPay = $this->getArrRes($this->execQry($qryNetPay));
		foreach ($arrNetPay as $val) {
			$Amount = (float)$val['Amount'] * -1;
			$QryInsertNetPayToPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, minCode, strCode, Amount) values
										('{$_SESSION['company_code']}','{$this->get['pdYear']}', '{$this->get['pdNumber']}', '{$_SESSION['pay_group']}', '{$_SESSION['pay_category']}','{$val['compGLCode']}','310','100','{$val['strCode']}','$Amount')";
		}
		return  $this->execQry($QryInsertNetPayToPayJournal);
	}
	function mainGLBooking() {
		$this->get = $this->getSlctdPdwil($_GET['curPayPd']);
		$this->session = $_SESSION;
		$this->getEmpList();
/*		if (!$this->getPeriod($_GET['payPd'])) {
			$this->hist = "hist";
		}*/
		$this->ReBookGL();		
		$Trns = $this->beginTran();
		if($Trns){
			$Trns = $this->getEarnings();
		}
		if($Trns){
			$Trns = $this->getEarnMajCode();
		}
		if($Trns){
			$Trns = $this->getEarnOtherCodes();
		}
		if($Trns){
			$Trns = $this->summarizeEarningsbyEmp();
		}
		if($Trns){
			$Trns = $this->EarnConvertToGLCodes();
		}		
		if($Trns){
			$Trns = $this->getDeductions();
		}		
		if($Trns){
			$Trns = $this->getDedOtherCodes();
		}
		if($Trns){
			$Trns = $this->SummarizeDeductions();
		}
		if($Trns){
			$Trns = $this->InsertDeductiontoPayJournal();
		}	
		if ($this->getCutOffPeriod()==2 && $_SESSION['pay_category'] != 9) {	
			if($Trns){
				$Trns = $this->SummarizedEmpGovCont();
			}		

			if($Trns){
				$Trns = $this->InsertEmpGovConttoPayJournal();
			}		
		}	
		if($Trns){
			$Trns = $this->InsertNetPay();
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
	function GetPeriodsforBooking() {
		$qryPeriods = "Select * from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payGrp='{$_SESSION['pay_group']}' and payCat='{$_SESSION['pay_category']}' and pdStat='O'";
		return $this->getArrRes($this->execQry($qryPeriods));	
	}	
	function getAllPeriod($compCode,$groupType,$catType) {
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE compCode = '$compCode' AND 
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}	
	function getOpenPeriod($compCode,$grp,$cat) 
	{
		$qry = "SELECT  pdTsTag, pdLoansTag, pdEarningsTag,compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdStat = 'O' AND 
			    compCode = '$compCode' AND
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
					
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getSlctdPdwil($payPd)
	{
		$qry = "SELECT * FROM tblPayPeriod WHERE compCode = '{$_SESSION['company_code']}' AND pdSeries = '$payPd'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}	
	
	function getEmpList() {
		$this->empList = "Select empNo from tblEmpMast where empPayGrp='{$_SESSION['pay_group']}'  
							AND empNo  IN (Select empNo from tblPayrollSummary where
								pdYear='{$this->get['pdYear']}'
								AND pdNumber = '{$this->get['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )";
	}
	function ReBookGL() {
		$qryReBook = "delete from wPayJournal1 where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
						
					  delete from wPayJournal2 where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  
					  delete from wPayJournal3 where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  
					  delete from tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  
					  delete from wPayJournal2d where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  
					  delete from wPayJournal3d where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					 
					  delete from wGovJm where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  
					  delete from wGovJmS where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  
					  ";
		$this->execQry($qryReBook);
	}
	function EmGovContList() {
		$qryGov = "Select * from tblMtdGovt{$this->hist} where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdMonth = '".date("m",strtotime($this->get['pdPayable']))."' and empNo IN ({$this->empList})";
		return $this->getArrRes($this->execQry($qryGov));
	}
}

?>