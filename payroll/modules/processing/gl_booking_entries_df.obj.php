<?
class generateBooking extends commonObj {
	
	var $get;//method
	var $session;//session variables
	var $empList;
	var $hist;
	var $arrBLine = array();
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
		$qryOpen="Update tblPayPeriod set pdStat='O' 
			where (compCode = '" . $this->session['company_code'] . "') 
			AND (pdYear = '" . $pdYear . "') 
			AND (pdNumber = '" . $pdNum . "')
			AND (payGrp='" . $this->session['pay_group'] . "')
			AND (payCat='" . $this->session['pay_category'] . "')
		";
		return $this->execQryI($qryOpen);
	}
	
	function getbrndivCode($table) {
		 $qryCodes = "Select empNo,empBrnCode,empDivCode as empDiv,empDepCode,empLocCode from tblpayrollsummary{$this->hist} where
					compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') AND (payGrp='" . $this->session['pay_group'] . "')
			AND (payCat='" . $this->session['pay_category'] . "')";
			"";
		return $this->getArrResI($this->execQryI($qryCodes));
	}
	function getcompGlCode() {
		$qrycompGlCode = "Select gLCode from tblCompany where compCode='{$_SESSION['company_code']}'";
		$glCode = $this->getSqlAssocI($this->execQryI($qrycompGlCode));
		return $glCode['gLCode'];
	}
	function getMinorCode($divCode,$deptCode,$brnCode="") {
		$qryMinorCode = "Select deptGlCode from tblDepartment where divCode='$divCode' and deptCode='$deptCode'  and deptLevel=2 and compCode='{$_SESSION['company_code']}'\n\n";
		$glCode = $this->getSqlAssocI($this->execQryI($qryMinorCode));
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
		$glCode = $this->getSqlAssocI($this->execQryI($qryStoreCode));
			$Code['glstrCode'] = $glCode['glCodeStr'];
			$Code['glcompCode'] = $glCode['compglCode'];
		return $Code;
	}
		
	function getEarnings(){
		 $qryEarnings = "Insert into wPayJournal1 (compCode,empNo,trnCode,Amount,pdYear,pdNumber,payGrp,payCat) Select '{$_SESSION['company_code']}',empNo,trnCode,trnAmountE,'{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}' from tblEarnings{$this->hist} where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and empNo IN ({$this->empList}) \n";
		return $this->execQryI($qryEarnings);
	}

	function getEarnMajCode() {
		$qryEarnMajCode = "Insert into wPayJournal2 (compCode,empNo,majCode,pdYear,pdNumber,payGrp,payCat,Amount) 
							Select wPayJournal1.compCode,empNo,trnGlCode,pdYear,pdNumber,payGrp,payCat,sum(Amount) as Amount from wPayJournal1 inner join tblPayTransType on
							wPayJournal1.trnCode = tblPayTransType.trnCode and wPayJournal1.compCode = tblPayTransType.compCode
							group by wPayJournal1.compCode,empNo,trnGlCode,pdYear,pdNumber,payGrp,payCat having wPayJournal1.compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') 
							AND (pdNumber = '{$this->get['pdNumber']}') AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		return $this->execQryI($qryEarnMajCode);
	}
	function getEarnOtherCodes() {
		 $qryOtherCodes = "Select * from wPayJournal2 where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') 
							AND (pdNumber = '{$this->get['pdNumber']}') AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrEarnOtherCodes =$this->getArrResI($this->execQryI($qryOtherCodes));
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
					
					$qryUpdate = "Update wPayJournal2 set deptCode='".$valCode['empDepCode']."',divCode='".$valCode['empDiv']."',brnCode='".$brnCode."',
								   locCode='".$locCode."' where empNo='".$val['empNo']."' and compCode='{$_SESSION['company_code']}' 
								   AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}');";
					$this->execQryI($qryUpdate);
					if ($this->getCutOffPeriod()==2 && $_SESSION['pay_category']!=9) {
						foreach($arrEmpGovList as $valGov) {
							if ($val['empNo']==$valGov['empNo'] && !in_array($valGov['empNo'],$arrEmp) ) {
								$arrEmp[]=$val['empNo'];
								$InsertMtdGoData = "Insert into wGovJm 
													(compCode,empNo,pdYear,pdNumber,payGrp,payCat,divCode,brnCode,locCode,sssEmplr,phicEmplr,hdmfEmplr,ec)
													values
													({$_SESSION['company_code']},'{$val['empNo']}','{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}','{$valCode['empDiv']}','$brnCode','$locCode','{$valGov['sssEmplr']}','{$valGov['phicEmplr']}','{$valGov['hdmfEmplr']}','{$valGov['ec']}'); \n\n
													";
								$this->execQryI($InsertMtdGoData);
							}
						}
					}
				}
			}
		}
		/*if ($this->getCutOffPeriod()==2  && $_SESSION['pay_category']!=9 && count($arrEmpGovList)!=0) {
			$this->execQryI($InsertMtdGoData);
		}*/

		return true;//$this->execMultiQryI($qryUpdate);
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

		return $this->execQryI($qrySummarizeEmp);
	}
 
	function EarnConvertToGLCodes() {
		 $qryEarningsData = "Select * from wPayJournal3 where compCode='{$_SESSION['company_code']}' 
						AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrEarningsData = $this->getArrResI($this->execQryI($qryEarningsData));
		$qryInsertEarningstoPayJournal = "";
		foreach($arrEarningsData as $val) {
			$minorCode = $this->getMinorCode($val['divCode'],$val['deptCode'],$val['brnCode']);
			if ($minorCode != "") {
				
				$glCode = $this->getStoreCode($val['brnCode']);
				$storeCode = $glCode['glstrCode'];
				$compGlCode = $glCode['glcompCode'];
				
				if ($_SESSION['pay_category']!=9) {
					 $qryInsertEarningstoPayJournal = "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,deptTag) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '{$val['majCode']}', '{$val['majCode']}', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '{$val['Amount']}',1); \n\n";
				} else {
					 $qryInsertEarningstoPayJournal = "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,deptTag,empNo) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '{$val['majCode']}', '{$val['majCode']}', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '{$val['Amount']}',1, '{$val['empNo']}'); \n\n";
				}
				$this->execQryI($qryInsertEarningstoPayJournal);	
			}						
		}
		/*if ($qryInsertEarningstoPayJournal !='')
			return  $this->execMultiQryI($qryInsertEarningstoPayJournal);
		else*/
			return true;
			
						
	}
	function getDeductions() {
		$qryDeductions = "Insert into wPayJournal2d (compCode,empNo, trnCode, Amount, pdYear, pdNumber, payGrp, payCat) 
						Select compCode,empNo,trnCode,sum(trnAmountD) as trnAmountD,'{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}' from tblDeductions{$this->hist} where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and empNo IN ({$this->empList}) AND trnCode Not IN (Select trnCode from tblPayTransType where compCode='{$_SESSION['company_code']}' AND deptTag='Y')
		group By compCode,empNo, pdYear, pdNumber, trnCode";
		return $this->execQryI($qryDeductions);
	}
	function getDeptDeductions() {
		$qryDeductions = "SELECT     tblDeductions.compCode, tblDeductions.empNo, tblDeductions.trnCode, SUM(tblDeductions.trnAmountD) AS trnAmountD, tblEmpMast.empDiv, 
                      tblEmpMast.empDepCode,empBrnCode as brnCode
FROM         tblDeductions{$this->hist} as tblDeductions INNER JOIN
                      tblEmpMast ON tblDeductions.compCode = tblEmpMast.compCode AND tblDeductions.empNo = tblEmpMast.empNo where tblDeductions.compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and tblDeductions.empNo IN ({$this->empList}) AND trnCode IN (Select trnCode from tblPayTransType where compCode='{$_SESSION['company_code']}' AND deptTag='Y')
		GROUP BY tblDeductions.compCode, tblDeductions.empNo, tblDeductions.trnCode, tblEmpMast.empDiv, tblEmpMast.empDepCode,tblEmpMast.empDepCode,empBrnCode";
		$sqlInsert = "";
		foreach($this->getArrResI($this->execQryI($qryDeductions)) as $val) {
			$minCode = $this->getMinorCode($val['empDiv'],$val['empDepCode'],$val['brnCode']);
			$sqlInsert .= " Insert into wPayJournal2d (compCode,empNo, trnCode, Amount, pdYear, pdNumber, payGrp, payCat, minCode) values (
						'{$_SESSION['company_code']}','{$val['empNo']}','{$val['trnCode']}',{$val['trnAmountD']},'{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}','{$_SESSION['pay_category']}','$minCode'
						);\n
						
						";
		}
		if ($sqlInsert !='') 
			return $this->execQryI($sqlInsert);
		else
			return true;
	
	}
	
	
	function getDedOtherCodes() {
		$qryOtherCodes = "Select * from wPayJournal2d where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') 
							AND (pdNumber = '{$this->get['pdNumber']}') AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrDedOtherCodes =$this->getArrResI($this->execQryI($qryOtherCodes));
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
					$qryUpdate = "Update wPayJournal2d set brnCode='".$brnCode."',
								   locCode='".$locCode."' where empNo='".$val['empNo']."' and compCode='{$_SESSION['company_code']}' 
								   AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}'; ";
					$this->execQryI($qryUpdate);
				}
			}
		}
		/*if($qryUpdate != "")
			return  $this->execMultiQryI($qryUpdate);
		else*/
			return true;
	}

	function SummarizeDeductions() {
		if ($_SESSION['pay_category']!=9) {
			$qrySummarizeDed = "Insert into wPayJournal3d (compCode,trnCode,locCode,brnCode,Amount,pdYear,pdNumber,payGrp,payCat,minCode) 
							Select compCode,trnCode,locCode,brnCode,sum(Amount) as Amount,pdYear,pdNumber,payGrp,payCat,minCode from wPayJournal2d 
							where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' group by compCode,trnCode,locCode,brnCode,pdYear,pdNumber,payGrp,payCat,minCode;";
										
		} else {
			$qrySummarizeDed = "Insert into wPayJournal3d (compCode,trnCode,locCode,brnCode,Amount,pdYear,pdNumber,payGrp,payCat,empNo,minCode) 
							Select compCode,trnCode,locCode,brnCode,sum(Amount) as Amount,pdYear,pdNumber,payGrp,payCat,empNo,minCode from wPayJournal2d 
							group by compCode,trnCode,locCode,brnCode,pdYear,pdNumber,payGrp,payCat,empNo,minCode having compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' ; ";
		}
		
		return $this->execQryI($qrySummarizeDed);
	}

	function InsertDeductiontoPayJournal() {
		if ($_SESSION['pay_category']!=9) {
			  $qrySummarizeDed = "SELECT * from view_PayJournal
							WHERE compCode='{$_SESSION['company_code']}'  AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and payCat='{$_SESSION['pay_category']}' and payGrp='{$_SESSION['pay_group']}'";
		} else {
			 $qrySummarizeDed = "SELECT * from view_PayJournal2
							WHERE compCode='{$_SESSION['company_code']}'  AND (pdYear = '{$this->get['pdYear']}') AND (pdNumber = '{$this->get['pdNumber']}') and payCat='{$_SESSION['pay_category']}' and payGrp='{$_SESSION['pay_group']}' \n";
		}
		
		$arrDedData = $this->getArrResI($this->execQryI($qrySummarizeDed));
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
				$qryInsertDedtoPayJournal = "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode2, minCode, strCode, strCode2, Amount) values
									('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCode', '$majCode', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$Amount'); \n\n";
			} else {
				$qryInsertDedtoPayJournal = "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode2, minCode, strCode, strCode2, Amount,empNo) values
									('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCode', '$majCode', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$Amount','{$val['empNo']}'); \n\n";
			}
			$this->execQryI($qryInsertDedtoPayJournal);
		}
	
		return true;
	}
	function SummarizedEmpGovCont() {
		$sqlQrySummarize = "Insert into wGovJmS (compCode,pdYear,pdNumber,payGrp,payCat,divCode,brnCode,locCode,sssEmplr,phicEmplr,hdmfEmplr,ec,deptCode)
							SELECT     wGovJm.compCode, wGovJm.pdYear, wGovJm.pdNumber, wGovJm.payGrp, wGovJm.payCat, wGovJm.divCode, wGovJm.brnCode, wGovJm.locCode, 
                      SUM(wGovJm.sssEmplr) AS sssEmplr, SUM(wGovJm.phicEmplr) AS phicEmplr, SUM(wGovJm.hdmfEmplr) AS hdmfEmplr, SUM(wGovJm.ec) AS ec, 
                      tblEmpMast.empDepCode FROM wGovJm INNER JOIN tblEmpMast ON wGovJm.compCode = tblEmpMast.compCode AND wGovJm.empNo = tblEmpMast.empNo where wGovJm.compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'  GROUP BY wGovJm.compCode, wGovJm.pdYear, wGovJm.pdNumber, wGovJm.payGrp, wGovJm.payCat, wGovJm.divCode, wGovJm.brnCode, wGovJm.locCode, 
                      tblEmpMast.empDepCode";
		return  $this->execQryI($sqlQrySummarize);
	}
	function InsertEmpGovConttoPayJournal() {
		$qryEmpGovList = "Select * from wGovJmS where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'";
		$arrEmpGovList = $this->getArrResI($this->execQryI($qryEmpGovList));
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
				$qryInsertGovConttoPayJournal = "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode2, minCode, strCode, strCode2, Amount,deptTag $empNo_field) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCodePH', '$majCodePH', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$AmtPH',1 $empNo_value); ";
				$this->execQryI($qryInsertGovConttoPayJournal);
			}						

			//HDMF
			if ($AmtHDMF != 0) {
/*				$qryInsertGovConttoPayJournal .= "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '003', '003', '".$storeCode."', '".$storeCode."', '-$AmtHDMF');";
*/	
				$qryInsertGovConttoPayJournal = "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,deptTag $empNo_field) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCodeHDMF', '$majCodeHDMF', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$AmtHDMF',1 $empNo_value); ";
				$this->execQryI($qryInsertGovConttoPayJournal);
			}
			//SSS and ec						
			if ($AmtSSS != 0) {
/*				$qryInsertGovConttoPayJournal .= "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '003', '003', '".$storeCode."', '".$storeCode."', '-$AmtSSS');";
*/	
				$qryInsertGovConttoPayJournal = "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode,strCode2, Amount,deptTag $empNo_field) values
										('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '$majCodeSSS', '$majCodeSSS', '".$minorCode."', '".$minorCode."', '".$storeCode."', '".$storeCode."', '$AmtSSS',1 $empNo_value); ";
				$this->execQryI($qryInsertGovConttoPayJournal);
			}
		}
		$sqlAdjustApDump = "SELECT     compCode, pdYear, pdNumber, payGrp, payCat, brnCode, locCode, SUM(sssEmplr) AS sssEmplr, SUM(phicEmplr) AS phicEmplr, SUM(hdmfEmplr) 
                      	AS hdmfEmplr, SUM(ec) AS ec
						FROM         wGovJmS
						Where      (compCode = '{$_SESSION['company_code']}') AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}'
						GROUP BY compCode, pdYear, pdNumber, payGrp, payCat, brnCode, locCode";
		$arrAdjustApDump = $this->getArrResI($this->execQryI($sqlAdjustApDump));
		foreach($arrAdjustApDump as $val) {
			$AmtPH		= $val['phicEmplr'];
			$AmtHDMF	= $val['hdmfEmplr'];
			$AmtSSS		= (float)$val['sssEmplr'] + (float)$val['ec'];
			$glCode 	= $this->getStoreCode($val['brnCode']);
			$storeCode 	= $glCode['glstrCode'];
			$compGlCode	= $glCode['glcompCode'];
			
				 $qryInsertGovConttoPayJournal = "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '003', '003', '".$storeCode."', '".$storeCode."', '-$AmtHDMF');\n";
				$this->execQryI($qryInsertGovConttoPayJournal);
				$qryInsertGovConttoPayJournal = "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '001', '001', '".$storeCode."', '".$storeCode."', '-$AmtSSS');\n";
				$this->execQryI($qryInsertGovConttoPayJournal);
				$qryInsertGovConttoPayJournal = "
					Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
					('{$_SESSION['company_code']}','{$val['pdYear']}', '{$val['pdNumber']}', '{$val['payGrp']}', '{$val['payCat']}', '".$compGlCode."', '350', '350', '002', '002', '".$storeCode."', '".$storeCode."', '-$AmtPH');\n";
				$this->execQryI($qryInsertGovConttoPayJournal);
		
		}
		
		if (count($arrEmpGovList)>0)
			return  true;
		else
			return true;}
	function InsertNetPay() {
		 if ($_SESSION['pay_category']!=9) 
			 $qryNetPay = "Select compGLCode,strCode,Sum(Amount) as Amount from tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' group by compGLCode,strCode";
		 else
			 $qryNetPay = "Select compGLCode,Sum(Amount) as Amount,empNo,strCode from tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' group by compGLCode,empNo,strCode";


		$arrNetPay = $this->getArrResI($this->execQryI($qryNetPay));
		foreach ($arrNetPay as $val) {
			$Amount = (float)$val['Amount'] * -1;
			if ($_SESSION['pay_category']!=9) {
				$QryInsertNetPayToPayJournal = "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount) values
											('{$_SESSION['company_code']}','{$this->get['pdYear']}', '{$this->get['pdNumber']}', '{$_SESSION['pay_group']}', '{$_SESSION['pay_category']}','{$val['compGLCode']}','310','310','100','100','{$val['strCode']}','{$val['strCode']}','$Amount')";
			 } else {
				 $QryInsertNetPayToPayJournal = "Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,empNo) values
											('{$_SESSION['company_code']}','{$this->get['pdYear']}', '{$this->get['pdNumber']}', '{$_SESSION['pay_group']}', '{$_SESSION['pay_category']}','{$val['compGLCode']}','310','310','100','100','{$val['strCode']}','{$val['strCode']}','$Amount','{$val['empNo']}')";
			 }
			 $this->execQryI($QryInsertNetPayToPayJournal);
		}
		return  true;
	}
	function UpdateStrCodeNonShaw() {
		$qryStr = "Select compGLCode, new_compGLCode=
					case compGLCode
						when '102' then '902'
						when '103' then '903'
						when '104' then '904'
						when '105' then '905'
						when '301' then '931'
						when '802' then '982'
						when '801' then '981'
						when '806' then '986'
						when '804' then '984'
						when '805' then '985'
						when '803' then '983'
						when '700' then '907'
						when '302' then '303'
					end
		 from  tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' AND compGLCode IN (102,103,104,105,301,802,801,806,804,805,803) AND majCode IN (350,35)";
		$arrStr = $this->getArrResI($this->execQryI($qryStr));
		foreach($arrStr as $valStr) {
			$qryUpdate .= " Update tblPayJournal set strCode2='{$valStr['new_compGLCode']}' where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' AND compGLCode = '{$valStr['compGLCode']}' AND majCode IN (350,35); \n\n";
		}
		if (count($arrStr) !=0)
			return $this->execQryI($qryUpdate);
		else
			return true;
	}
	function UpdateStrCodeShaw() {
		switch($_SESSION["company_code"])
		{
			case "15":
				$setUpStrCode = '989';
			break;
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
	

		 $qryUpdate = "Update tblPayJournal set strCode2='".$setUpStrCode."' where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' AND compGLCode not IN (102,103,104,105,301,802,801,806,804,805,803,700,302) AND majCode IN (350,35)";
		return  $this->execQryI($qryUpdate);
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
			case "7":
				$setUpStrCode = '981';
			break;

			case "8":
				$setUpStrCode = '982';
			break;

			case "9":
				$setUpStrCode = '983';
			break;

			case "10":
				$setUpStrCode = '984';
			break;

			case "11":
				$setUpStrCode = '985';
			break;

			case "12":
				$setUpStrCode = '986';
			break;
			default:
				$setUpStrCode = '0';
			break;
		}
		
		$qryUpdate = "Update tblPayJournal set minCode2='112' where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}' AND minCode = 104 and strCode='".$setUpStrCode."'";
		return  $this->execQryI($qryUpdate);
	}
	function mainGLBooking() {
		$this->get = $this->getSlctdPdwil($_GET['curPayPd']);
		
		$this->session = $_SESSION;
		$this->getEmpList();
/*		if (!$this->getPeriod($_GET['payPd'])) {
			$this->hist = "hist";
		}*/
		$this->ReBookGL();	
		
		$this->ReBookJDAGL();
			
		$Trns = $this->beginTranI();
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
		//$this->check3();
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

		/*if($Trns){
			if(($_SESSION["company_code"]=='1') || ($_SESSION["company_code"]=='2'))
				$Trns = $this->UpdateStrCodeNonShaw();
		}	
		if($Trns){
			if(($_SESSION["company_code"]=='1') || ($_SESSION["company_code"]=='2') || ($_SESSION["company_code"]=='15'))
				$Trns = $this->UpdateStrCodeShaw();
		}	*/
		if($Trns){
			$Trns = $this->UpdateMinCode();
		}
		
		if($Trns){
			if(in_array($_SESSION["company_code"],array(1,2,3,7,8,9,10,11,12,13,15)))
				$Trns = $this->createJDAJE();
		}

		if(!$Trns){
			$Trns = $this->rollbackTranI();
			return false;
		}
		else{
			$Trns = $this->commitTranI();
			return true;	
		}						
				
	}
	function GetPeriodsforBooking() {
		$qryPeriods = "Select * from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payGrp='{$_SESSION['pay_group']}' and payCat='{$_SESSION['pay_category']}' and pdStat='O'";
		return $this->getArrResI($this->execQryI($qryPeriods));	
	}	
	function getAllPeriod() {
		$qry = "SELECT compCode, pdStat, CAST(pdPayable as date) AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE  
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
		$res = $this->execQryI($qry);
		$res =  $this->getArrResI($res);
		return $res;
	}	
	function getOpenPeriod($compCode,$grp,$cat) 
	{
		$qry = "SELECT  pdTsTag, pdLoansTag, pdEarningsTag,compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdStat = 'O' AND 
			    compCode = '$compCode' AND
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
					
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}
	function getSlctdPdwil($payPd)
	{
		$qry = "SELECT * FROM tblPayPeriod WHERE compCode = '{$_SESSION['company_code']}' AND pdSeries = '$payPd'";
		$res = $this->execQryI($qry);
		return $this->getSqlAssocI($res);
	}	
	
	function getEmpList($check="") {
		if($check!="")
		{
			$this->get = $this->getSlctdPdwil($_GET['curPayPd']);
			$this->session = $_SESSION;
		}
		
		$this->empList = "Select empNo from tblEmpMast where empPayGrp='{$_SESSION['pay_group']}'  
							AND empNo  IN (Select empNo from tblPayrollSummary{$this->hist} where
								pdYear='{$this->get['pdYear']}'
								AND pdNumber = '{$this->get['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}')";
		
		if($check!="")
		{
			$res = $this->execQryI($this->empList);
			return count($this->getArrResI($res));
		}
			
	}
	function ReBookGL() {
		$Trns = $this->beginTranI();
		
		$qryReBook = "delete from wPayJournal1 where compCode='{$_SESSION['company_code']}';";
		if($Trns){
			$Trns = $this->execQryI($qryReBook);
		}				
		$qryReBook = "delete from wPayJournal2 where compCode='{$_SESSION['company_code']}';";
		if($Trns){
			$Trns = $this->execQryI($qryReBook);
		}			  
		$qryReBook = "delete from wPayJournal3 where compCode='{$_SESSION['company_code']}';";
		if($Trns){
			$Trns = $this->execQryI($qryReBook);
		}			  
		$qryReBook = "delete from tblPayJournal where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';";
		if($Trns){
			$Trns = $this->execQryI($qryReBook);
		}		  
		$qryReBook = "delete from wPayJournal2d where compCode='{$_SESSION['company_code']}';";
		if($Trns){
			$Trns = $this->execQryI($qryReBook);
		}			  
		$qryReBook = "delete from wPayJournal3d where compCode='{$_SESSION['company_code']}';";
		if($Trns){
			$Trns = $this->execQryI($qryReBook);
		}			 
		$qryReBook = "delete from wGovJm where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';";
		if($Trns){
			$Trns = $this->execQryI($qryReBook);
		}			  
		$qryReBook = "delete from wGovJmS where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';";
		if($Trns){
			$Trns = $this->execQryI($qryReBook);
		}
		
		if(!$Trns){
			$Trns = $this->rollbackTranI();
			return false;
		}
		else{
			$Trns = $this->commitTranI();
			return true;	
		}
	}
	function EmGovContList() {
		$qryGov = "Select * from tblMtdGovt{$this->hist} where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdMonth = '".date("m",strtotime($this->get['pdFrmDate']))."' and empNo IN  ({$this->empList})";
		if ($_SESSION['pay_category'] == 1)	 {
			$qryGov .= " AND empNo not IN (select empNo from tblNonEmpGov where compCode='{$_SESSION['company_code']}' and cat='all')";
		}
	
		return $this->getArrResI($this->execQryI($qryGov));
	}
	function CheckGL($payPd){
		$arrPd = $this->getSlctdPdwil($payPd);
		$qryGL = "Select compCode from tblPayJournal WHERE compCode='{$_SESSION['company_code']}' AND pdyear = '{$arrPd['pdYear']}' AND pdNumber= '{$arrPd['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}'";
		return $this->getRecCountI($this->execQryI($qryGL));
	}
	function GetEmpWithZeroNetPay() {
		$sql = "SELECT tblEmpMast.empNo, tblBranch.compglCode AS compCode, tblBranch.glCodeStr AS strCode, tblDepartment.deptGlCode AS minorCode FROM tblEmpMast INNER JOIN
                      tblBranch ON tblEmpMast.compCode = tblBranch.compCode AND tblEmpMast.empBrnCode = tblBranch.brnCode INNER JOIN
                      tblDepartment ON tblEmpMast.compCode = tblDepartment.compCode AND tblEmpMast.empDiv = tblDepartment.divCode AND 
                      tblEmpMast.empDepCode = tblDepartment.deptCode
				WHERE (tblEmpMast.compCode = '{$_SESSION['company_code']}') AND (tblEmpMast.empNo IN
                      (SELECT empNo FROM tblPayrollSummary WHERE compCode = '{$_SESSION['company_code']}' AND payCat = '{$_SESSION['pay_category']}' AND pdYEar = '{$this->get['pdYear']}' AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' AND empNo NOT IN
                			(SELECT empNo FROM tblPayJournal WHERE compCode = '{$_SESSION['company_code']}' AND payCat = '{$_SESSION['pay_category']}' AND pdYEar = '{$this->get['pdYear']}' AND pdNumber = '{$this->get['pdNumber']}'))) AND (tblDepartment.deptLevel = '2')";
							
		$res = $this->getArrResI($this->execQryI($sql));
		$qryInsert = "";
		foreach ($res as $val) {
			$qryInsert = " Insert into tblPayJournal (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode2, minCode, strCode, strCode2, Amount,empNo) values
									('{$_SESSION['company_code']}','{$this->get['pdYear']}', '{$this->get['pdNumber']}', '{$_SESSION['pay_group']}', '{$_SESSION['pay_category']}', '{$val['compCode']}', '710', '710', '{$val['minorCode']}', '{$val['minorCode']}', '{$val['strCode']}', '{$val['strCode']}', '0','{$val['empNo']}'); \n\n";
			$this->execQryI($qryInsert);
		}
			return true;
	}
	
	function CreateTextOracleTextFile() {
		$this->get = $this->getSlctdPdwil($_GET['curPayPd']);
		$this->getBLineList();
		$this->NetSalStore();
		$dtday 	= date('dmy');
		$arrPayPeriod = $this->getPeriodWil($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category'],"AND pdPayable = '".$this->dateFormat($_GET['payPd'])."'");

		if ($arrPayPeriod['pdStat']=="C") {
			$hist = "hist";
		}
		
		if ($_SESSION['pay_category']!=9) {
		$filterBalintawak = ($_SESSION['company_code'] == 1) ? "and brnCode<>67 ":"";
		
		$qryAP = "SELECT CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(3), tblPayJournal.strCode) + substring(convert(varchar(4),tblPayJournal.pdYear),3,2) + CONVERT(varchar(3), 
							tblPayJournal.pdNumber) + CONVERT(varchar(3), tblPayJournal.payGrp) + CONVERT(varchar(3), tblPayJournal.payCat) AS payRegID, 
							CONVERT(varchar(3), tblPayJournal.compGLCode) + CONVERT(varchar(3), tblPayJournal.majCode2)  + CONVERT(varchar(3), 
							tblPayJournal.minCode2) + '00' + CONVERT(varchar(3), tblPayJournal.strCode2) AS Account, tblGLCodes.glCodeDesc, tblPayJournal.Amount,tblPayJournal.majCode,tblPayJournal.minCode,deptTag,brnShortName as branch, tblPayJournal.strCode,tblPayJournal.compGLCode,extraTag,tblPayJournal.majCode2, tblPayJournal.minCode2
							FROM tblPayJournal LEFT JOIN
							tblGLCodes ON tblPayJournal.compGLCode = tblGLCodes.compGLCode AND tblPayJournal.majCode2 = tblGLCodes.majCode AND 
							tblPayJournal.minCode2 = tblGLCodes.minCode AND tblPayJournal.strCode2 = tblGLCodes.strCode
							INNER JOIN tblBranch ON tblPayJournal.strCode = tblBranch.GLCodeStr
							WHERE (tblPayJournal.pdYear = '{$this->get['pdYear']}') 
							AND (tblPayJournal.pdNumber IN ('{$this->get['pdNumber']}')) 
							AND (tblPayJournal.payCat = '{$_SESSION['pay_category']}')
							AND (tblPayJournal.payGrp = '{$_SESSION['pay_group']}')
							AND ((tblPayJournal.majCode <> 310 or tblPayJournal.minCode <> 100))
							and brnStat='A' $filterBalintawak and oracleTag='Y'
							ORDER BY tblPayJournal.strCode";
		} else {
			
			$qryAP = "Exec sp_JournalEntries '{$this->get['pdYear']}','{$this->get['pdNumber']}',9,{$_SESSION['pay_group']}";
		}
		$resAP =  $this->getArrResI($this->execQryI($qryAP));
		$ctr = 0;
				switch($_SESSION['company_code']) {
					case 2:
						$comp = 'PG';
					break;
					case 1:
						$comp = 'PJ';
					break;	
					case 4:
						$comp = 'DC';
					break;	
					case 5:
						$comp = 'DS';
					break;	
				}
		$dtTime 	= date('dmy')."_";
//		$filename = $comp.$dtTime.".701";
		$doc_root = $_SERVER['DOCUMENT_ROOT'];
		$str_branch = "";
		$arrFiles = array();
		foreach($resAP as $val) {
				$dtHr =$_SESSION['pay_group'] . $_SESSION['pay_category']. $this->AddZero(substr($val['strCode'],0,3));
				$filename = $comp.$dtTime.$dtHr.".701";

				if ($str_branch != $val['strCode']) {
					$ctr = 0;
					$str_branch = $val['strCode'];
				}
					
				
					
				$ctr++;
				
				$payregID = $val['payRegID'];
				$date = date('d-M-Y');
				//PayegID
				
					
				$str = $payregID;

				switch($_SESSION['company_code']) {
					case 2:
						$hdSTr = 'SHAW';
						$vendCode = 101;
						$majCode = $val['majCode2'];
						$compCode = 101;
						if ($val['extraTag'] == 'Y')
							$BL = '006';
					break;
					case 1:
						$hdSTr = 'PG JR';
						$majCode = $val['majCode2'];
						$compCode = $val['compGLCode'];
						$vendCode = 14020;
					break;	
					case 4:
						$hdSTr = 'DCHO';
						$compCode = 101;
						$majCode = $val['majCode2'];
						$vendCode = 'PGDFI';
						$BL = '007';
					break;	
					case 5:
						$hdSTr = 'DSHO';
						$compCode = 101;
						$majCode = ($val['majCode2']==810 ? 710:$val['majCode2']);
						$vendCode = 'PGD02';
						$BL = '007';
					break;	
				}
				$dept = ($val['deptTag']=='1') ? $val['minCode2']:"0";
				if (!in_array($majCode,array(710002,710001,710003,710004)))
					$minCode = ($val['deptTag']=='1') ? "000":$val['minCode2'];
				else
					$minCode = ($val['deptTag']=='1') ? "":$val['minCode2'];
				
/*				if ((float)$val['Amount']>0) {*/

					$type = "STANDARD";
					//Credit CompCode
					$compGLCode = (in_array($val['compGLCode'],array(102,103,104,105))) ?  '101':$val['compGLCode'];
					$credit = "|".$compGLCode;
					
					if (in_array($val['majCode2'],array('350','035'))) {
						//Credit branch
						$credit .= "|".$hdSTr;
						if ($_SESSION['company_code'] !=4 && $_SESSION['company_code'] !=5)
							$BL = $this->getStrBline($hdSTr);
					} else {
						//Credit branch
						$credit .= "|".$val['branch'];
						if ($_SESSION['company_code'] !=4 && $_SESSION['company_code'] !=5)
							$BL = $this->getStrBline($val['branch']);
					}
					
					//Credit Business Line
					$credit .= "|$BL";
					
					//Credit Department
					$credit .= "|$dept";
					
					//Credit 0
					$credit .= "|0";
					
					//Credit Major
					$credit .= "|".$compCode.$majCode.$minCode;
					
					//Credit Minor
					$credit .= "|".$compCode.$majCode.$minCode;
					
					//Credit Amount
					$credit .= "|".$val['Amount'];

				
					$debit = "|";
					$debit .= "|";
					$debit .= "|";
					$debit .= "|";
					$debit .= "|";
					$debit .= "|";
					$debit .= "|";
					$debit .= "|0";
					
/*				} else {
					$type = "STANDARD";

					$credit = "|";
					$credit .= "|";
					$credit .= "|";
					$credit .= "|";
					$credit .= "|";
					$credit .= "|";
					$credit .= "|";
					$credit .= "|";

					//debit CompCode
					$debit = "|".$val['compGLCode'];
					
					//debit branch
					$debit .= "|".$val['branch'];
					
					//debit Business Line
					$debit .= "|$BL";
					
					//debit Department
					$debit .= "|$dept";

					//debit 0
					$debit .= "|0";
					
					//debit Major
					$debit .= "|".$val['majCode2'];
					
					//debit Minor
					$debit .= "|$minCode";
					
					//debit Amount
					$debit .= "|".$val['Amount'];
				}*/
				
				//Type
				$str .= "|$type";
				
				//Trans Date
				$str .= "|$date";
				
				
				if ($_SESSION['pay_category']!=9) {
					$rem = date('m/d/Y',strtotime($this->get['pdFrmDate']))."-".date('m/d/Y',strtotime($this->get['pdToDate']));
					$Misc = "";
				} else {
					$rem = $val['empName'];
					$Misc = $val['empName'];
					$vendCode = '999999';
				}		
						
				//Vendor Code
				$str .= "|$vendCode";
				


				//Store
				$str .= "|".$val['branch'];
				
				$HdrAmount = $this->getStrNetSal($val['strCode'],$val['empNo']);
				//Header Amount
				$str .= "|$HdrAmount";
				
				//Description
				$str .= "|$rem";
				
				//Trans Date
				$str .= "|$date";

				//Trans Date
				$str .= "|$date";

				//Trans Date
				$str .= "|$date";
				
				//AP Type
				$str .= "|PAY";

				//Counter
				$str .= "|$ctr";
				
				//Detail Amt
				$str .= "|".$val['Amount'];
				

				//ITEM
				$str .= "|ITEM";

				//Credit				
				$str .= "$credit";

				//Reference
				$str .= "|";
				
				//blank field
				$str .= "|0";
				
				//Credit				
				$str .= "$debit";

				//Tax Code				
				$str .= "|XX";

				//SKU		
				$str .= "|";
				
				//Quantity
				$str .= "|0";

				//Quantity
				$str .= "|0";
				
				//Currency
				$str .= "|PHP";
				
				//Due Date
				$str .= "|$date";
				
				//Misc Supp
				$str .= "|$Misc";


				//FileName
				$str .= "|$filename|";
				if (file_exists("$doc_root/Oracle_TextFiles/".$filename) && !in_array($filename,$arrFiles)) {
					unlink("$doc_root/Oracle_TextFiles/".$filename);
				}
				$file = fopen("$doc_root/Oracle_TextFiles/".$filename,"a");				
				fwrite($file,$str."\r\n");
				$arrFiles[] = $filename;
		}
		

	// set up basic connection
/*	$conn_id = ftp_connect('192.168.200.136');
				
	// login with username and password
	$login_result = ftp_login($conn_id, 'ppcioracle', 'oracle');
	
	// upload a file
	for($i=0;$i<count($arrFiles);$i++) {
		$remote_file = "".$arrFiles[$i];
		$file = "$doc_root/Oracle_TextFiles/".$arrFiles[$i];
		ftp_put($conn_id, $remote_file, $file, FTP_BINARY);
	}
*/		
		
		if ($ctr > 0) 
			return true;
		else 
			return false;
	}

	function NetSalStore() {
		if ($_SESSION['pay_category']!=9) {
			$sqlNETSalaList = "Select strCode, amount*-1 as amount from tblPayJournal where compCode='".$_SESSION['company_code'] ."' and payGrp = '".$_SESSION['pay_group']."' and payCat = '".$_SESSION['pay_category']."' and (majCode = 310 and minCode = 100) and pdNumber='{$this->get['pdNumber']}' and pdYear='{$this->get['pdYear']}'";
		} else {
			$sqlNETSalaList = "Select empNo, amount*-1 as amount from tblPayJournal where compCode='".$_SESSION['company_code'] ."' and payGrp = '".$_SESSION['pay_group']."' and payCat = '".$_SESSION['pay_category']."' and (majCode = 310 and minCode = 100) and pdNumber='{$this->get['pdNumber']}' and pdYear='{$this->get['pdYear']}'";
			
		}
		$this->arrNetSal = $this->getArrResI($this->execQryI($sqlNETSalaList));
	}
	
	function getStrNetSal($strCode,$empNo) {
		$amount = 0;
		if ($_SESSION['pay_category']!=9) {
			foreach($this->arrNetSal as $val) {
				if ($val['strCode'] == $strCode) {
					$amount = $val['amount'];
				}
			}
		} else {
			
			foreach($this->arrNetSal as $val) {
				if ($val['empNo'] == $empNo) {
					$amount = $val['amount'];
				}
			}
		}
		return $amount;
	}	

	function AddBLineZero($str) {
		$str = trim($str);
		$cnt = strlen($str);
		$new_str = "";
		$ctr = 3-$cnt;
		if ($ctr>0) {
			while ($ctr>0) {
				$new_str .= "0";
				$ctr--;
			}
			$new_str = $new_str . $str;
		} else {
			$new_str = $str;
		}
		return $new_str;
	}
	
	function AddZero($str) {
		$str = trim($str);
		$cnt = strlen($str);
		$new_str = "";
		$ctr = 4-$cnt;
		if ($ctr>0) {
			while ($ctr>0) {
				$new_str .= "0";
				$ctr--;
			}
			$new_str = $new_str . $str;
		} else {
			$new_str = $str;
		}
		return $new_str;
	}

	function createJDAJE() {
		$sql = "
		Insert into tblPayJournal_jda (compCode,pdYear, pdNumber, payGrp, payCat, compGLCode, majCode, majCode2, minCode, minCode2, strCode, strCode2, Amount,deptTag,empNo)
		Select compCode,pdYEar,pdNumber,payGrp,payCat,compGLCode,substring(majCode,1,3) as majCode,substring(majCode2,1,3) as majCode2,minCode,minCode2,strCode,strCode2,sum(Amount),deptTag,empNo from tblPayJournal WHERE compCode = '{$_SESSION['company_code']}' AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdYEar = '{$this->get['pdYear']}' AND pdNumber = '{$this->get['pdNumber']}'
group by compCode,pdYEar,pdNumber,payGrp,payCat,compGLCode,substring(majCode,1,3),substring(majCode2,1,3),minCode,minCode2,strCode,strCode2,deptTag,empNo \n\n";	
		return $this->execQryI($sql);	
	}
	

	function ReBookJDAGL() {
		$qryReBook = "delete from tblPayJournal_jda where compCode='{$_SESSION['company_code']}' AND (pdYear = '{$this->get['pdYear']}') AND pdNumber = '{$this->get['pdNumber']}' AND payGrp='{$_SESSION['pay_group']}' and payCat = '{$_SESSION['pay_category']}';
					  ";
		return $this->execQryI($qryReBook);
	}
	
	function createDebitSWforAccrual() {
		$sqlDebitSWforAccrual = "Insert into tblAccrual (compCode,majCode,minCode,strCode,amount,deptTag,pdYEar,pdNumber,payGrp,payCat,AccruType)
								Select compGLCode,710004,minCode2,strCode,round(sum(Amount)/12,2),'Y',pdYEar,pdNumber,payGrp,payCat,'SW' from tblPayJournal 
								where pdYEar = '{$this->get['pdYear']}' and 
									pdNumber IN ('{$this->get['pdNumber']}') and 
									majCode = 710001 and 
									payCat<>9 AND 
									payGrp='{$_SESSION['pay_group']}' AND 
									payCat = '{$_SESSION['pay_category']}'
									and amount<>0
								group by compGLCode,minCode2,strCode,pdYEar,pdNumber,payGrp,payCat
								UNION
								Select compGLCode,710004,112,glCodeStr,round(sum(empBasic)/12,2)*-1,'Y',pdYear,pdNumber,payGrp,payCat,'SW' from tblPayrollSummary pay
								inner join tblBranch br on empbrnCode=brnCode
								where empNo IN ('010000045','010000044','010001458') and 
									pdYEar = '{$this->get['pdYear']}' and 
									pdNumber IN ('{$this->get['pdNumber']}') AND 
									payGrp='{$_SESSION['pay_group']}' AND 
									payCat = '{$_SESSION['pay_category']}'
									and empBasic<>0
								group by compGLCode,glCodeStr,pdYear,pdNumber,payGrp,payCat";	
		return $this->execQryI($sqlDebitSWforAccrual);
	}
	
	function createCreditSWforAccrual() {
		$sqlCreditSWforAccrual = "Insert into tblAccrual (compCode,majCode,minCode,strCode,amount,pdYEar,pdNumber,payGrp,payCat,AccruType)
									Select compCode,20050,'004',strCode,sum(amount)*-1,pdYEar,pdNumber,payGrp,payCat,'SW' from tblAccrual
									where pdYEar = '{$this->get['pdYear']}' and 
									pdNumber IN ('{$this->get['pdNumber']}') AND 
									payGrp='{$_SESSION['pay_group']}' AND 
									payCat = '{$_SESSION['pay_category']}'
									group by compCode,strCode,pdYear,pdNumber,payGrp,payCat";
		return $this->execQryI($sqlCreditSWforAccrual);
	}

	function createDebitRAforAccrual() {
		$sqlDebitRAforAccrual = "Insert into tblAccrual (compCode,majCode,minCode,strCode,amount,deptTag,pdYEar,pdNumber,payGrp,payCat,AccruType)
								Select compGLCode,712000,minCode2,strCode,round(sum(Amount)/12,2),'Y',pdYEar,pdNumber,payGrp,payCat,'RA' from tblPayJournal 
								where pdYEar = '{$this->get['pdYear']}' and 
									pdNumber IN ('{$this->get['pdNumber']}') and  
									majCode = 711 
									and payCat<>9 AND 
									payGrp='{$_SESSION['pay_group']}' AND 
									payCat = '{$_SESSION['pay_category']}'
									and amount<>0
								group by compGLCode,minCode2,strCode,pdYEar,pdNumber,payGrp,payCat
								UNION
								Select compGLCode,712000,112,glCodeStr,round(sum(sprtAllowAdvance)/12,2)*-1,'Y',pdYear,pdNumber,payGrp,payCat,'RA' from tblPayrollSummary pay
								inner join tblBranch br on empbrnCode=brnCode
								where empNo IN ('010000045','010000044','010001458') and 
									sprtAllowAdvance>0 and 
									pdYEar = '{$this->get['pdYear']}' and 
									pdNumber IN ('{$this->get['pdNumber']}') AND 
									payGrp='{$_SESSION['pay_group']}' AND 
									payCat = '{$_SESSION['pay_category']}' 
									and sprtAllowAdvance<>0
								group by compGLCode,glCodeStr,pdYear,pdNumber,payGrp,payCat";
		return $this->execQryI($sqlDebitRAforAccrual);
	}

	function createCreditRAforAccrual() {
		$sqlCreditRAforAccrual = "Insert into tblAccrual (compCode,majCode,minCode,strCode,amount,pdYEar,pdNumber,payGrp,payCat,AccruType)
									Select compCode,20050,'004',strCode,sum(amount)*-1,pdYEar,pdNumber,payGrp,payCat,'RA' from tblAccrual
									where majCode=712000 and
									pdYEar = '{$this->get['pdYear']}' and 
									pdNumber IN ('{$this->get['pdNumber']}') AND 
									payGrp='{$_SESSION['pay_group']}' AND 
									payCat = '{$_SESSION['pay_category']}'
									group by compCode,strCode,pdYear,pdNumber,payGrp,payCat";
		return $this->execQryI($sqlCreditRAforAccrual);	
	}
	
	function CreateAccrualFile() {
		$this->get = $this->getSlctdPdwil($_GET['curPayPd']);
		$Trns = $this->beginTranI();
		$this->getBLineList();
		if($Trns){
			$Trns = $this->ClearAccrual();
		}
		if($Trns){
			$Trns = $this->createDebitSWforAccrual();
		}
		if($Trns){
			$Trns = $this->createCreditSWforAccrual();
		}
		if($Trns){
			$Trns = $this->createDebitRAforAccrual();
		}
		if($Trns){
			$Trns = $this->createCreditRAforAccrual();
		}
		$sqlAccrual = "Select 
							REPLACE(CONVERT(VARCHAR(11), GETDATE(), 6), ' ', '-') AS Date
							,transType = CASE when amount>0 then 'DEBIT' else 'CREDIT' END
							,compGLCode as compCode
							,br.brnShortname
							, '007' as b_line
							,Department = case deptTag when 'Y' then minCode else '0' end
							,0 as Section
							, acct =  case deptTag when 'Y' then cast(majCode as varchar(9)) else cast(majCode as varchar(9)) +cast(minCode as varchar(9)) end
							, acct2 =  case deptTag when 'Y' then cast(majCode as varchar(9)) else cast(majCode as varchar(9)) +cast(minCode as varchar(9)) end
							,0
							,0
							,''
							,'13thMonthAccrual MAY 2012'
							,'Accrual'
							,'PHP'
							,ABS(amount) as amount
							,'13thMonthAccrual MAY 2012'
							,''
							,''
							,''
							,''
							,''
							,fname = CASE PAY.compCode 
							WHEN 2 THEN 'PG' + REPLACE(CONVERT(VARCHAR(10), GETDATE(), 1), '/', '') + '_' + REPLACE(CONVERT(VARCHAR(12), GETDATE(), 108), ':', '') + '.F01' 
							WHEN 1 THEN  'PJ' + REPLACE(CONVERT(VARCHAR(10), GETDATE(), 1), '/', '') + '_' + REPLACE(CONVERT(VARCHAR(12), GETDATE(), 108), ':', '') + '.F01' 
							WHEN 1001 THEN  'DC' + REPLACE(CONVERT(VARCHAR(10), GETDATE(), 1), '/', '') + '_' + REPLACE(CONVERT(VARCHAR(12), GETDATE(),108), ':', '') + '.F01' 
							WHEN 1002 THEN  'DS' + REPLACE(CONVERT(VARCHAR(10), GETDATE(), 1), '/', '') + '_' + REPLACE(CONVERT(VARCHAR(12), GETDATE(),108), ':', '') + '.F01'
							END,
							extraTag,AccruType
							from tblAccrual pay inner join tblBranch br on strCode=glCodeStr
							where pdYEar = '{$this->get['pdYear']}' and 
									pdNumber IN ('{$this->get['pdNumber']}') AND 
									payGrp='{$_SESSION['pay_group']}' AND 
									payCat = '{$_SESSION['pay_category']}' and oracleTag='Y' order by deptTag Desc";		
		$resAccr =  $this->getArrResI($this->execQryI($sqlAccrual));
		$doc_root = $_SERVER['DOCUMENT_ROOT'];
		
		$dtTime 	= date('dmy')."_";
		switch($_SESSION['company_code']) {
			case 2:
				$compCode = 101;
				$comp = 'PG';
				$BL = '004';
			break;
			case 1:
				$compCode = 700;
				$comp = 'PJ';
				$BL = '005';
			break;	
			case 4:
				$compCode = 101;
				$comp = 'DC';
				$BL = '007';
			break;	
			case 5:
				$compCode = 101;
				$comp = 'DS';
				$BL = '007';
			break;	
		}
		$this->get['pdPayable'] = date('m/d/Y',strtotime($this->get['pdPayable']));
		switch($_SESSION['pay_category']) {
			case 1:
				$cat = "Grp {$_SESSION['pay_group']} Exec {$this->get['pdPayable']}";	
			break;
			case 2:
				$cat = "Grp {$_SESSION['pay_group']} Confi {$this->get['pdPayable']}";	
			break;
			case 3:
				$cat = "Grp {$_SESSION['pay_group']} NonConfi {$this->get['pdPayable']}";	
			break;
		}
		$dtHr_SW = $_SESSION['pay_group'] . $_SESSION['pay_category'].$_SESSION['company_code']."_SW";
		$dtHr_RA = $_SESSION['pay_group'] . $_SESSION['pay_category'].$_SESSION['company_code']."_RA";
		$filename_SW = $comp.$dtTime.$dtHr_SW."2.F01";
		$filename_RA = $comp.$dtTime.$dtHr_RA."2.F01";
		if (file_exists("$doc_root/Oracle_TextFiles/".$filename_SW)) {
			unlink("$doc_root/Oracle_TextFiles/".$filename_SW);
		}
		if (file_exists("$doc_root/Oracle_TextFiles/".$filename_RA)) {
			unlink("$doc_root/Oracle_TextFiles/".$filename_RA);
		}		
		$ctr = 0;
		foreach($resAccr as $val) {
			
			$ctr++;
			if ($_SESSION['company_code'] !=4 && $_SESSION['company_code'] !=5)
				$BL = $this->getStrBline($val['brnShortname']);			

				
			$str = date('d-M-Y') ."|";
			$str .= $val['transType'] ."|";
			$str .= $val['compCode'] ."|";
			$str .= $val['brnShortname'] ."|";
			$str .= "$BL|";
			$str .= $val['Department'] ."|";
			$str .= "0|";
			if ($val['acct'] == '20050004') {
				$str .= $val['acct'] ."|";
				$str .= $val['acct2'] ."|";
			} else {
				$str .= $compCode.$val['acct'] ."|";
				$str .= $compCode.$val['acct2'] ."|";
			}
			$str .= "0|";
			$str .= "0|";
			$str .= "|";
			$str .= "13thAcc {$val['AccruType']} $cat|";
			$str .= 'Accrual|';
			$str .= 'PHP|';
			$str .= $val['amount'] ."|";
			$str .= "13thAcc {$val['AccruType']} $cat|";
			$str .= '|';
			$str .= '|';
			$str .= '|';
			$str .= '|';
			$str .= '|';
			
			if ($val['AccruType'] == 'SW') {
				$str .= $filename_SW ."|";
				$file_SW = fopen("$doc_root/Oracle_TextFiles/".$filename_SW,"a");				
				fwrite($file_SW,$str."\r\n");
			} else {
				$str .= $filename_RA ."|";
				$file_RA = fopen("$doc_root/Oracle_TextFiles/".$filename_RA,"a");				
				fwrite($file_RA,$str."\r\n");
			}
		}
		if ($ctr > 0 && $Trns == true)  {
			$Trns = $this->commitTranI();
			return true;
		} else  {
			$Trns = $this->rollbackTranI();
			return false;	
		}
	}
	function ClearAccrual() {
		$sqlClearArrual = "Delete from tblAccrual where pdYEar = '{$this->get['pdYear']}' and 
									pdNumber IN ('{$this->get['pdNumber']}') AND 
									payGrp='{$_SESSION['pay_group']}' AND 
									payCat = '{$_SESSION['pay_category']}'";
		return $this->execQryI($sqlClearArrual);									
	}

	function getBLineList() {
		$sqlBline = "Select bline,brnShortName as strCode from payroll_company..tblBLine bl inner join tblBranch on glCodeStr=strCode";
		$this->arrBLine = $this->getArrResI($this->execQryI($sqlBline));
	}
	
	function getStrBline($strCode) {
		$bLine = '';
		foreach($this->arrBLine as $val) {
			if ($strCode == $val['strCode'])
				$bLine = $this->AddBLineZero((int)$val['bline']);
		}
		return $bLine;
	}
	
	
	
}

?>


 