
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
		$qryOpen="Update tblPayPeriod set pdStat='O', pdTSStat='O' 
			where (compCode = '" . $this->session['company_code'] . "') 
			AND (pdYear = '" . $pdYear . "') 
			AND (pdNumber = '" . $pdNum . "')
			AND (payGrp='" . $this->session['pay_group'] . "')
			AND (payCat='" . $this->session['pay_category'] . "')
		";
		return $this->execQry($qryOpen);
	}
	
	function getbrndivCode($table) {
		 $qryCodes = "Select empNo,empBrnCode,empDiv,empDepCode,empLocCode from tblEmpMast where empNo IN (Select empNo from $table where 
					compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') AND (payGrp='" . $this->session['pay_group'] . "')
			AND (payCat='" . $this->session['pay_category'] . "'))";
		return $this->getArrRes($this->execQry($qryCodes));
	}
	function getcompGlCode() {
		$qrycompGlCode = "Select gLCode from tblCompany where compCode='{$_SESSION['company_code']}'";
		$glCode = $this->getSqlAssoc($this->execQry($qrycompGlCode));
		return $glCode['gLCode'];
	}
	function getMinorCode($divCode,$deptCode,$brnCode="") {
		$qryMinorCode = "Select deptGlCode from tblDepartment where divCode='$divCode' and deptCode='$deptCode'  and deptLevel=2 and compCode='{$_SESSION['company_code']}'\n\n";
		$glCode = $this->getSqlAssoc($this->execQry($qryMinorCode));
		if ($divCode==7 && $deptCode==2 && $brnCode==1) {
			return "102";
		} elseif ($divCode==7 && $deptCode==2 && $brnCode!=1  && $brnCode!='') {
			//return '104';
			
			if(($_SESSION["company_code"]==1) || ($_SESSION["company_code"]==2))
				return '104';
			else
				return $glCode['deptGlCode'];
		
		} else {
			return $glCode['deptGlCode'];
		}	
	}
	function getStoreCode($brnCode) {
		  $qryStoreCode = "Select compglCode,glCodeStr,glCodeHO,compglCodeHO from tblBranch where compCode='{$_SESSION['company_code']}' and brnCode=$brnCode \n";
		$glCode = $this->getSqlAssoc($this->execQry($qryStoreCode));
			$Code['glstrCode'] = $glCode['glCodeStr'];
			$Code['glcompCode'] = $glCode['compglCode'];
		return $Code;
	}
		
	function getEarnings(){
		 $qryEarnings = "Insert into wPayJournal1 (compCode,empNo,trnCode,Amount,pdYear,pdNumber,payGrp,payCat) Select '{$_SESSION['company_code']}',empNo,trnCode,trnAmountE,'{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}' from tblEarnings{$this->hist} where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and empNo IN ({$this->empList}) \n";
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
		$arrEmp = array();
		foreach ($arrEarnOtherCodes as $val) {
			foreach($arrCodes as $valCode) {
				if ($val['empNo']==$valCode['empNo']) {
/*					if ($valCode['empLocCode']=="0001") {
						$brnCode = '3';
						$locCode = '0001';
					} else {
*/						$brnCode = $valCode['empBrnCode'];
						$locCode = $valCode['empLocCode'];
/*					}*/
					
					$qryUpdate .= "Update wPayJournal2 set deptCode='".$valCode['empDepCode']."',divCode='".$valCode['empDiv']."',brnCode='".$brnCode."',
								   locCode='".$locCode."' where empNo='".$val['empNo']."' and compCode='{$_SESSION['company_code']}' 
								   AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}')";
					if ($this->getCutOffPeriod()==2 && $_SESSION['pay_category']!=9) {
						foreach($arrEmpGovList as $valGov) {
							if ($val['empNo']==$valGov['empNo'] && !in_array($valGov['empNo'],$arrEmp) ) {
								$arrEmp[]=$val['empNo'];
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
		if ($this->getCutOffPeriod()==2  && $_SESSION['pay_category']!=9 && count($arrEmpGovList)!=0) {
			$this->execQry($InsertMtdGoData);
		}
		return $this->execQry($qryUpdate);
	}
	function summarizeEarningsbyEmp() {

		if ($_SESSION['pay_category']!=9) {
			$qrySummarizeEmp ="Insert into wPayJournal3 (compCode,deptCode,divCode,brnCode,locCode,majCode,Amount,pdYear,pdNumber,payGrp,payCat) 
							Select compCode,deptCode,divCode,brnCode,locCode,majCode,sum(Amount) as Amount,pdYear,pdNumber,payGrp,payCat from wPayJournal2 
							group by compCode,divCode,deptCode,brnCode,locCode,majCode,pdYear,pdNumber,payGrp,payCat having compCode='{$_SESSION['company_code']}' 
							AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';";
		} else {
			$qrySummarizeEmp ="Insert into wPayJournal3 (compCode,divCode,deptCode,brnCode,locCode,majCode,Amount,pdYear,pdNumber,payGrp,payCat,empNo) 
							Select compCode,divCode,deptCode,brnCode,locCode,majCode,sum(Amount) as Amount,pdYear,pdNumber,payGrp,payCat,empNo from wPayJournal2 
							group by compCode,divCode,deptCode,brnCode,locCode,majCode,pdYear,pdNumber,payGrp,payCat,empNo having compCode='{$_SESSION['company_code']}' 
							AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';";
		
		}					

		return $this->execQry($qrySummarizeEmp);
	}
 
	function EarnConvertToGLCodes() {
		 $qryEarningsData = "Select * from wPayJournal3 where compCode='{$_SESSION['company_code']}' 
						AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrEarningsData = $this->getArrRes($this->execQry($qryEarningsData));
		$qryInsertEarningstoPayJournal = "";
		foreach($arrEarningsData as $val) {
			$minorCode = $this->getMinorCode($val['divCode'],$val['deptCode'],$val['brnCode']);
			if ($minorCode != "") {
				$glCode = $this->getStoreCode($val['brnCode']);
				$storeCode = $glCode['glstrCode'];
				$compGlCode = $glCode['glcompCode'];
				
				if ($_SESSION['pay_category']!=9) {
					 $qryInsertEarningstoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,deptTag) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '{$val['majCode']}', '{$val['majCode']}', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '{$val['Amount']}',1); \n\n";
				} else {
					 $qryInsertEarningstoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,deptTag,empNo) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '{$val['majCode']}', '{$val['majCode']}', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '{$val['Amount']}',1, '{$val['empNo']}'); \n\n";
				}	
			}						
		}
		if ($qryInsertEarningstoPayJournal !='')
			return  $this->execQry($qryInsertEarningstoPayJournal);
		else
			return true;
			
						
	}
	function getDeductions() {
		$qryDeductions = "Insert into wPayJournal2d (compCode,empNo, trnCode, Amount, pdYear, pdNumber, payGrp, payCat) 
						Select compCode,empNo,trnCode,sum(trnAmountD) as trnAmountD,'{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}' from tblDeductions{$this->hist} where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and empNo IN ({$this->empList}) AND trnCode Not IN (Select trnCode from tblPayTransType where compCode='{$_SESSION['company_code']}' AND deptTag='Y')
		group By compCode,empNo, pdYear, pdNumber, trnCode";
		return $this->execQry($qryDeductions);
	}
	function getDeptDeductions() {
		$qryDeductions = "SELECT     tblDeductions.compCode, tblDeductions.empNo, tblDeductions.trnCode, SUM(tblDeductions.trnAmountD) AS trnAmountD, tblEmpMast.empDiv, 
                      tblEmpMast.empDepCode,empBrnCode as brnCode
FROM         tblDeductions INNER JOIN
                      tblEmpMast ON tblDeductions.compCode = tblEmpMast.compCode AND tblDeductions.empNo = tblEmpMast.empNo where tblDeductions.compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and tblDeductions.empNo IN ({$this->empList}) AND trnCode IN (Select trnCode from tblPayTransType where compCode='{$_SESSION['company_code']}' AND deptTag='Y')
		GROUP BY tblDeductions.compCode, tblDeductions.empNo, tblDeductions.trnCode, tblEmpMast.empDiv, tblEmpMast.empDepCode,tblEmpMast.empDepCode,empBrnCode";
		$sqlInsert = "";
		foreach($this->getArrRes($this->execQry($qryDeductions)) as $val) {
			$minCode = $this->getMinorCode($val['empDiv'],$val['empDepCode'],$val['brnCode']);
			$sqlInsert .= " Insert into wPayJournal2d (compCode,empNo, trnCode, Amount, pdYear, pdNumber, payGrp, payCat, minCode) values (
						'{$_SESSION['company_code']}','{$val['empNo']}','{$val['trnCode']}',{$val['trnAmountD']},'{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}','$minCode'
						);\n
						
						";
		}
		if ($sqlInsert !='') 
			return $this->execQry($sqlInsert);
		else
			return true;
	
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
/*					if ($valCode['empLocCode']=="0001") {
						$brnCode = '3';
						$locCode = '0001';
					} else {
*/						$brnCode = $valCode['empBrnCode'];
						$locCode = $valCode['empLocCode'];
					//}
					$qryUpdate .= "Update wPayJournal2d set brnCode='".$brnCode."',
								   locCode='".$locCode."' where empNo='".$val['empNo']."' and compCode='{$_SESSION['company_code']}' 
								   AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}'; ";
				}
			}
		}
		if($qryUpdate != "")
			return  $this->execQry($qryUpdate);
		else
			return true;
	}

	function SummarizeDeductions() {
		if ($_SESSION['pay_category']!=9) {
			$qrySummarizeDed = "Insert into wPayJournal3d (compCode,trnCode,locCode,brnCode,Amount,pdYear,pdNumber,payGrp,payCat,minCode) 
							Select compCode,trnCode,locCode,brnCode,sum(Amount) as Amount,pdYear,pdNumber,payGrp,payCat,minCode from wPayJournal2d 
							group by compCode,trnCode,locCode,brnCode,pdYear,pdNumber,payGrp,payCat,minCode having compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' ;";
		} else {
			$qrySummarizeDed = "Insert into wPayJournal3d (compCode,trnCode,locCode,brnCode,Amount,pdYear,pdNumber,payGrp,payCat,empNo,minCode) 
							Select compCode,trnCode,locCode,brnCode,sum(Amount) as Amount,pdYear,pdNumber,payGrp,payCat,empNo,minCode from wPayJournal2d 
							group by compCode,trnCode,locCode,brnCode,pdYear,pdNumber,payGrp,payCat,empNo,minCode having compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' ; ";
		}
		
		return $this->execQry($qrySummarizeDed);
	}
	
	function InsertDeductiontoPayJournal() {
		if ($_SESSION['pay_category']!=9) {
			  $qrySummarizeDed = "SELECT * from view_PayJournal
							WHERE compCode='{$_SESSION['company_code']}'  AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and payCat='{$_SESSION['pay_category']}' and payGrp='{$_SESSION['pay_group']}'";
		} else {
			 $qrySummarizeDed = "SELECT * from view_PayJournal2
							WHERE compCode='{$_SESSION['company_code']}'  AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and payCat='{$_SESSION['pay_category']}' and payGrp='{$_SESSION['pay_group']}' \n";
		}
		$arrDedData = $this->getArrRes($this->execQry($qrySummarizeDed));
		$qryInsertDedtoPayJournal = "";
		foreach($arrDedData as $val) {
			if ($val['minCode2'] != "")
				$minorCode = $val['minCode2'];
			else
				$minorCode = $val['minCode'];
			$majCode = trim($val['trnGlCode']);
			$glCode = $this->getStoreCode($val['brnCode']);
			$storeCode = $glCode['glstrCode'];
			$compGlCode = $glCode['glcompCode'];
			$Amount = (float)$val['Amount'] * -1;

			if ($_SESSION['pay_category']!=9) {
				$qryInsertDedtoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode2, minCode, strCode, strCode2, Amount) values
									('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCode', '$majCode', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$Amount'); \n\n";
			} else {
				$qryInsertDedtoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode2, minCode, strCode, strCode2, Amount,empNo) values
									('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCode', '$majCode', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$Amount','{$val['empNo']}'); \n\n";
			}
		}
	if ($qryInsertDedtoPayJournal !="")	
		return  $this->execQry($qryInsertDedtoPayJournal);
	else
		return true;
	}
	function SummarizedEmpGovCont() {
		$sqlQrySummarize = "Insert into wGovJmS (compCode,pdYear,pdNumber,payGrp,payCat,divCode,brnCode,locCode,sssEmplr,phicEmplr,hdmfEmplr,ec,deptCode)
							SELECT     wGovJm.compCode, wGovJm.pdYear, wGovJm.pdNumber, wGovJm.payGrp, wGovJm.payCat, wGovJm.divCode, wGovJm.brnCode, wGovJm.locCode, 
                      SUM(wGovJm.sssEmplr) AS sssEmplr, SUM(wGovJm.phicEmplr) AS phicEmplr, SUM(wGovJm.hdmfEmplr) AS hdmfEmplr, SUM(wGovJm.ec) AS ec, 
                      tblEmpMast.empDepCode FROM wGovJm INNER JOIN tblEmpMast ON wGovJm.compCode = tblEmpMast.compCode AND wGovJm.empNo = tblEmpMast.empNo where wGovJm.compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'  GROUP BY wGovJm.compCode, wGovJm.pdYear, wGovJm.pdNumber, wGovJm.payGrp, wGovJm.payCat, wGovJm.divCode, wGovJm.brnCode, wGovJm.locCode, 
                      tblEmpMast.empDepCode";
		return  $this->execQry($sqlQrySummarize);
	}
	function InsertEmpGovConttoPayJournal() {
		$qryEmpGovList = "Select * from wGovJmS where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrEmpGovList = $this->getArrRes($this->execQry($qryEmpGovList));
		foreach($arrEmpGovList as $val) {
			$minorCode 	= $this->getMinorCode($val['divCode'],$val['deptCode'],$val['brnCode']);
			$glCode 	= $this->getStoreCode($val['brnCode']);
			$storeCode 	= $glCode['glstrCode'];
			$compGlCode	= $glCode['glcompCode'];
			$majCodePH	= "720";
			$majCodeHDMF= "725";
			$majCodeSSS	= "715";
			$AmtPH		= $val['phicEmplr'];
			$AmtHDMF	= $val['hdmfEmplr'];
			$AmtSSS		= (float)$val['sssEmplr'] + (float)$val['ec'];
			$AmtPH2		= $AmtPH *-1;
			$AmtHDMF2	= $AmtHDMF*-1;
			$AmtSSS2	= $AmtSSS*-1;			
			$deptTag=1;
			//PhilHealth
			if ($_SESSION['pay_category']!=9) {
			 	$empNo_field =", empNo";
			 	$empNo_value =", '{$val['empNo']}'";
			 }
			//PhilHealth
			if ($AmtPH != 0) {
/*				$qryInsertGovConttoPayJournal .= "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '002', '002', '".$storeCode."', '".$storeCode."', '-$AmtPH');";
*/					
				$qryInsertGovConttoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode2, minCode, strCode, strCode2, Amount,deptTag $empNo_field) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCodePH', '$majCodePH', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$AmtPH',1 $empNo_value); ";
			}						

			//HDMF
			if ($AmtHDMF != 0) {
/*				$qryInsertGovConttoPayJournal .= "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '003', '003', '".$storeCode."', '".$storeCode."', '-$AmtHDMF');";
*/	
				$qryInsertGovConttoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,deptTag $empNo_field) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCodeHDMF', '$majCodeHDMF', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$AmtHDMF',1 $empNo_value); ";
			}
			//SSS and ec						
			if ($AmtSSS != 0) {
/*				$qryInsertGovConttoPayJournal .= "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '003', '003', '".$storeCode."', '".$storeCode."', '-$AmtSSS');";
*/	
				$qryInsertGovConttoPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode,strCode2, Amount,deptTag $empNo_field) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCodeSSS', '$majCodeSSS', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$AmtSSS',1 $empNo_value); ";
			}
		}
		$sqlAdjustApDump = "SELECT     compCode, pdYear, pdNumber, payGrp, payCat, brnCode, locCode, SUM(sssEmplr) AS sssEmplr, SUM(phicEmplr) AS phicEmplr, SUM(hdmfEmplr) 
                      	AS hdmfEmplr, SUM(ec) AS ec
						FROM         wGovJmS
						Where      (compCode = '{$_SESSION['company_code']}') AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'
						GROUP BY compCode, pdYear, pdNumber, payGrp, payCat, brnCode, locCode";
		$arrAdjustApDump = $this->getArrRes($this->execQry($sqlAdjustApDump));
		foreach($arrAdjustApDump as $val) {
			$AmtPH		= $val['phicEmplr'];
			$AmtHDMF	= $val['hdmfEmplr'];
			$AmtSSS		= (float)$val['sssEmplr'] + (float)$val['ec'];
			$glCode 	= $this->getStoreCode($val['brnCode']);
			$storeCode 	= $glCode['glstrCode'];
			$compGlCode	= $glCode['glcompCode'];
			
				 $qryInsertGovConttoPayJournal .= "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '003', '003', '".$storeCode."', '".$storeCode."', '-$AmtHDMF');\n";

				$qryInsertGovConttoPayJournal .= "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '001', '001', '".$storeCode."', '".$storeCode."', '-$AmtSSS');\n";
		
				$qryInsertGovConttoPayJournal .= "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '002', '002', '".$storeCode."', '".$storeCode."', '-$AmtPH');\n";
		
		}
		
		if (count($arrEmpGovList)>0)
			return  $this->execQry($qryInsertGovConttoPayJournal);
		else
			return true;}
	function InsertNetPay() {
		 if ($_SESSION['pay_category']!=9) 
			 $qryNetPay = "Select compGLCode,strCode,Sum(Amount) as Amount from tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' group by compGLCode,strCode";
		 else
			 $qryNetPay = "Select compGLCode,Sum(Amount) as Amount,empNo,strCode from tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' group by compGLCode,empNo,strCode";


		$arrNetPay = $this->getArrRes($this->execQry($qryNetPay));
		foreach ($arrNetPay as $val) {
			$Amount = (float)$val['Amount'] * -1;
			if ($_SESSION['pay_category']!=9) {
				$QryInsertNetPayToPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
											('{$_SESSION['company_code']}','{$this->get['pdYear']}', '{$this->get['pdNumber']}', '{$_SESSION['pay_group']}', '{$_SESSION['pay_category']}','{$val['compGLCode']}','310','310','100','100','{$val['strCode']}','{$val['strCode']}','$Amount')";
			 } else {
				 $QryInsertNetPayToPayJournal .= "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,empNo) values
											('{$_SESSION['company_code']}','{$this->get['pdYear']}', '{$this->get['pdNumber']}', '{$_SESSION['pay_group']}', '{$_SESSION['pay_category']}','{$val['compGLCode']}','310','310','100','100','{$val['strCode']}','{$val['strCode']}','$Amount','{$val['empNo']}')";
			 }
		}
		return  $this->execQry($QryInsertNetPayToPayJournal);
	}
	function UpdateStrCodeNonShaw() {
		$qryStr = "Select compGLCode, new_compGLCode=
					case compGLCode
						when '102' then '902'
						when '103' then '903'
						when '104' then '904'
						when '105' then '905'
						when '301' then '931'
					end
		 from  tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' AND compGLCode IN (102,103,104,105,301) AND majCode IN (350,35,310)";
		$arrStr = $this->getArrRes($this->execQry($qryStr));
		foreach($arrStr as $valStr) {
			$qryUpdate .= " Update tblPayJournal set strCode2='{$valStr['new_compGLCode']}' where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' AND compGLCode = '{$valStr['compGLCode']}' AND majCode IN (350,35,310); \n\n";
		}
		if (count($arrStr) !=0)
			return $this->execQry($qryUpdate);
		else
			return true;
	}
	function UpdateStrCodeShaw() {
		switch($_SESSION["company_code"])
		{
			case "1":
				$setUpStrCode = '907';
			break;
			
			case "2":
				$setUpStrCode = '901';
			break;
			
			case "4":
				$setUpStrCode = '1001202';
			break;
			
			case "5":
				$setUpStrCode = '1002201';
			break;
				$setUpStrCode = '0';
			default:
				
			break;
		}
	

		 $qryUpdate = "Update tblPayJournal set strCode2='".$setUpStrCode."' where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' AND compGLCode not IN (102,103,104,105,301) AND majCode IN (350,35,310)";
		return  $this->execQry($qryUpdate);
	}
	function UpdateMinCode() {
		switch($_SESSION["company_code"])
		{
			case "1":
				$setUpStrCode = '907';
			break;
			
			case "2":
				$setUpStrCode = '901';
			break;
			
			case "4":
				$setUpStrCode = '1001202';
			break;
			
			case "5":
				$setUpStrCode = '1002201';
			break;
				$setUpStrCode = '0';
			default:
				
			break;
		}
		
		$qryUpdate = "Update tblPayJournal set minCode2='112' where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' AND minCode = 104 and strCode='".$setUpStrCode."'";
		return  $this->execQry($qryUpdate);
	}
	function mainGLBooking() {
		$this->get = $this->getSlctdPdwil($_GET['curPayPd']);
		$this->session = $_SESSION;
		$this->getEmpList();
/*		if (!$this->getPeriod($_GET['payPd'])) {
			$this->hist = "hist";
		}*/
		$this->ReBookGL();		
		if($_SESSION["company_code"]=='1' || $_SESSION["company_code"]=='2')
			$this->ReBookJDAGL();
			
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
			$Trns = $this->getDeptDeductions();
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
		if ($_SESSION['pay_category'] == 9 ) {	
			if($Trns){
				$Trns = $this->GetEmpWithZeroNetPay();
			}
		}
		if ($this->getCutOffPeriod()==2 && $_SESSION['pay_category'] != 9 ) {	
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

		if($Trns){
			if(($_SESSION["company_code"]=='1') || ($_SESSION["company_code"]=='2'))
				$Trns = $this->UpdateStrCodeNonShaw();
		}	
		if($Trns){
			if(($_SESSION["company_code"]=='1') || ($_SESSION["company_code"]=='2'))
				$Trns = $this->UpdateStrCodeShaw();
		}	
		if($Trns){
			$Trns = $this->UpdateMinCode();
		}

		if($Trns){
			if(($_SESSION["company_code"]=='1') || ($_SESSION["company_code"]=='2'))
				$Trns = $this->createJDAJE();
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
	
	function getEmpList($check="") {
		if($check!="")
		{
			$this->get = $this->getSlctdPdwil($_GET['curPayPd']);
			$this->session = $_SESSION;
		}
		
		$this->empList = "Select empNo from tblEmpMast where empPayGrp='{$_SESSION['pay_group']}'  
							AND empNo  IN (Select empNo from tblPayrollSummary where
								pdYear='{$this->get['pdYear']}'
								AND pdNumber = '{$this->get['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}')";
		
		if($check!="")
		{
			$res = $this->execQry($this->empList);
			return count($this->getArrRes($res));
		}
			
	}
	function ReBookGL() {
		$qryReBook = "delete from wPayJournal1 where compCode='{$_SESSION['company_code']}';
						
					  delete from wPayJournal2 where compCode='{$_SESSION['company_code']}';
					  
					  delete from wPayJournal3 where compCode='{$_SESSION['company_code']}';
					  
					  delete from tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  
					  delete from wPayJournal2d where compCode='{$_SESSION['company_code']}';
					  
					  delete from wPayJournal3d where compCode='{$_SESSION['company_code']}';
					 
					  delete from wGovJm where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  
					  delete from wGovJmS where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  
					  ";
		$this->execQry($qryReBook);
	}
	
	function EmGovContList() {
		$qryGov = "Select * from tblMtdGovt{$this->hist} where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdMonth = '".date("m",strtotime($this->get['pdPayable']))."' and empNo IN  ({$this->empList})";
		if ($_SESSION['pay_category'] == 1)	 {
			$qryGov .= " AND empNo not IN (select empNo from tblNonEmpGov where compCode='{$_SESSION['company_code']}' and cat='all')";
		}
	
		return $this->getArrRes($this->execQry($qryGov));
	}
	function CheckGL($payPd){
		$arrPd = $this->getSlctdPdwil($payPd);
		$qryGL = "Select compCode from tblPayJournal WHERE compCode='{$_SESSION['company_code']}' AND pdyear = '{$arrPd['pdYear']}' AND pdNumber= '{$arrPd['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}'";
		return $this->getRecCount($this->execQry($qryGL));
	}
	function GetEmpWithZeroNetPay() {
		$sql = "SELECT tblEmpMast.empNo, tblBranch.compglCode AS compCode, tblBranch.glCodeStr AS strCode, tblDepartment.deptGlCode AS minorCode FROM tblEmpMast INNER JOIN
                      tblBranch ON tblEmpMast.compCode = tblBranch.compCode AND tblEmpMast.empBrnCode = tblBranch.brnCode INNER JOIN
                      tblDepartment ON tblEmpMast.compCode = tblDepartment.compCode AND tblEmpMast.empDiv = tblDepartment.divCode AND 
                      tblEmpMast.empDepCode = tblDepartment.deptCode
				WHERE (tblEmpMast.compCode = '{$_SESSION['company_code']}') AND (tblEmpMast.empNo IN
                      (SELECT empNo FROM tblPayrollSummary WHERE compCode = '{$_SESSION['company_code']}' AND payCat = '{$_SESSION['pay_category']}' AND pdYEar = '{$this->get['pdYear']}' AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' AND empNo NOT IN
                			(SELECT empNo FROM tblPayJournal WHERE compCode = '{$_SESSION['company_code']}' AND payCat = '{$_SESSION['pay_category']}' AND pdYEar = '{$this->get['pdYear']}' AND pdNumber = '{$this->get['pdNumber']}'))) AND (tblDepartment.deptLevel = '2')";
							
		$res = $this->getArrRes($this->execQry($sql));
		$qryInsert = "";
		foreach ($res as $val) {
			$qryInsert .= " Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode2, minCode, strCode, strCode2, Amount,empNo) values
									('{$_SESSION['company_code']}','{$this->get['pdYear']}', '{$this->get['pdNumber']}', '{$_SESSION['pay_group']}', '{$_SESSION['pay_category']}', '{$val['compCode']}', '710', '710', '{$val['minorCode']}', '{$val['minorCode']}', '{$val['strCode']}', '{$val['strCode']}', '0','{$val['empNo']}'); \n\n";
		}
		if ($qryInsert !="") 
			return $this->execQry($qryInsert);	
		else
			return true;
	}

	function createJDAJE() {
		echo $sql = "
		Insert into tblPayJournal_jda (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,deptTag,empNo)
		Select compCode,pdYEar,pdNumber,payGrp,payCat,compGLCode,substring(majCode,1,3) as majCode,substring(majCode2,1,3) as majCode2,minCode,minCode2,strCode,strCode2,sum(Amount),deptTag,empNo from tblPayJournal WHERE compCode = '{$_SESSION['company_code']}' AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdYEar = '{$this->get['pdYear']}' AND pdNumber = '{$this->get['pdNumber']}'
group by compCode,pdYEar,pdNumber,payGrp,payCat,compGLCode,substring(majCode,1,3),substring(majCode2,1,3),minCode,minCode2,strCode,strCode2,deptTag,empNo \n\n";	
		return $this->execQry($sqlS);	
	}
	

	function ReBookJDAGL() {
		$qryReBook = "delete from tblPayJournal_jda where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  ";
		$this->execQry($qryReBook);
	}
}

?>


 