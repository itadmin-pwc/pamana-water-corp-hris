<?
class genloansObj extends commonObj {

	function getOpenPeriod($compCode,$grp,$cat) {
		$qry = "SELECT tblPayPeriod.pdLoansTag,tblPayPeriod.compCode, tblPayPeriod.pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, 
                      tblPayPeriod.pdSeries, tblPayPeriod.payGrp, tblPayPeriod.payCat, tblPayPeriod.pdYear, tblPayPeriod.pdNumber, 
                      tblPayPeriod.pdFrmDate, tblPayPeriod.pdToDate, tblPayCat.payCatDesc,pdTsTag
				FROM  tblPayPeriod INNER JOIN
                      tblPayCat ON tblPayPeriod.compCode = tblPayCat.compCode 
				AND   tblPayPeriod.payCat = tblPayCat.payCat
				WHERE pdStat = 'O' 
				AND   tblPayPeriod.compCode = '$compCode' 
				AND   tblPayPeriod.payCat=$cat 
				AND   tblPayPeriod.payGrp=$grp";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getAllPeriod($compCode,$groupType,$catType) {
		if ($groupType>"") $groupType = " AND payGrp = '$groupType' "; else $groupType = "";
		if ($catType>"") $catType = " AND payCat = '$catType' "; else $catType = "";
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, 
				pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate , 
				convert(varchar,pdFrmDate,101) + ' - ' + convert(varchar,pdToDate,101) AS dateCovered,pdLoansTag,pdProcessTag 
				FROM tblPayPeriod 
				WHERE compCode = '$compCode' $groupType $catType ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getSlctdPd($compCode,$payPd) {
		$qry = "SELECT *,pdNumber % 2 AS modSked FROM tblPayPeriod 
				WHERE pdSeries = '$payPd' ";
				
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getAllPeriod2($compCode,$groupType){ 
		$qry = "SELECT TOP 100 PERCENT MAX(CONVERT(VARCHAR, pdPayable, 101)) AS pdPayable,  CONVERT(varchar, MAX(pdFrmDate), 101) + '-' + CONVERT(varchar, 
                      MAX(pdToDate), 101) AS pdFrmToDate
			   FROM tblPayPeriod
			   WHERE (compCode = '$compCode') AND (payGrp = '$groupType')
			   GROUP BY pdPayable
			   ORDER BY MAX(pdPayable)";
			   
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function processLoans($compCode,$lonSked,$pdPeriod,$re_proc) {
		$Trns = $this->beginTran();
		if ($re_proc==1) {
			$earnsTag = "N";
			if($Trns){
				$Trns = $this->execQry("DELETE from tblEmpLoansDtl WHERE compCode = '".$compCode."' AND trnGrp = '" .$pdPeriod['payGrp'] . "' AND trnCat = '" . $pdPeriod['payCat'] . "' AND pdYear = '" .$pdPeriod['pdYear'] . "' AND pdNumber = '" .$pdPeriod['pdNumber'] . "'");
				}
		} 
		$qry = "SELECT *
				FROM tblEmpLoans INNER JOIN
                tblEmpMast ON tblEmpLoans.empNo = tblEmpMast.empNo
				WHERE (tblEmpLoans.compCode = '$compCode') AND (tblEmpLoans.lonSked IN ('$lonSked',3)) AND 
				(tblEmpLoans.lonStart <= '" . $pdPeriod['pdToDate'] . "') AND 
                      (tblEmpLoans.lonStat = 'O') AND (tblEmpLoans.lonCurbal > 0) AND  
			   		  (tblEmpMast.compCode = '$compCode') AND (tblEmpMast.empPayGrp = '" . $pdPeriod['payGrp'] . "') AND 
                      (tblEmpMast.empPayCat = '" . $pdPeriod['payCat'] . "')
					  AND empStat IN ('RG', 'PR', 'CN')";
		
		$res = $this->execQry($qry);
		$arr = $this->getArrRes($res);
		foreach ($arr as $val){
			$currentbal = $val['lonCurbal'];
			$dedperperiod = $val['lonDedAmt2'];
			
			if ($dedperperiod > $currentbal) {
				$dedAmt = $currentbal;
				$lastPay = 1;
			} else {
				$dedAmt = $dedperperiod;
				$lastPay = 0;
			}
			$qryDtl              = "INSERT INTO tblEmpLoansDtl(
									compCode,empNo,lonTypeCd,
									lonRefNo,pdYear,pdNumber,
									trnCat,trnGrp,trnAmountD,
									dedTag,lonLastPay
								  ) VALUES(
									'$compCode','{$val['empNo']}','{$val['lonTypeCd']}',
									'{$val['lonRefNo']}','" .$pdPeriod['pdYear'] . "','" .$pdPeriod['pdNumber'] . "',
									'" .$pdPeriod['payCat'] . "','" .$pdPeriod['payGrp'] . "',$dedAmt,'',
									'".date('Y-m-d',strtotime($val['lonLastPay']))."'
								  )";
			if($Trns){
				$Trns = $this->execQry($qryDtl);
			}	
		}
		if ($pdPeriod['payGrp']==1 && $pdPeriod['payCat']==3 && $pdPeriod['pdNumber']==14 and $pdPeriod['pdYear']==2010) {
			$sqlLoansAdj = "INSERT INTO tblEmpLoansDtl(
									compCode,empNo,lonTypeCd,
									lonRefNo,pdYear,pdNumber,
									trnCat,trnGrp,trnAmountD,
									dedTag,lonLastPay
								  )
							SELECT compCode, empNo, lonTypeCd, 
								lonRefNo, pdYear, pdNumber, 
								trnCat, trnGrp, trnAmountD, 
								'', lonLastPay 
							FROM tblLoansAdj";
			if($Trns){
				$Trns = $this->execQry($sqlLoansAdj);
			}							
		}		
		$qryUpdtPd = "UPDATE tblPayPeriod SET ";
						$qryUpdtPd .= "pdLoansTag = 'Y', ";
						$qryUpdtPd .= "pdEarningsTag = '$earnsTag' ";
						$qryUpdtPd .= "WHERE compCode = '".$compCode."' AND payGrp = '" .$pdPeriod['payGrp'] . "' AND payCat = '" .$pdPeriod['payCat'] . "' AND pdYear = '" .$pdPeriod['pdYear'] . "' AND pdNumber = '" .$pdPeriod['pdNumber'] . "'";	
		$Trns = $this->execQry($qryUpdtPd);
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}
	}
	
	function processLastPayLoans($compCode,$pdPeriod,$re_proc) {
		$Trns = $this->beginTran();
		if ($re_proc==1) {
			$earnsTag = "N";
			if($Trns){
				$Trns = $this->execQry("DELETE tblEmpLoansDtl WHERE compCode = '".$compCode."' AND trnGrp = '" .$pdPeriod['payGrp'] . "' AND pdYear = '" .$pdPeriod['pdYear'] . "' AND pdNumber = '" .$pdPeriod['pdNumber'] . "' 
										AND empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$_SESSION['company_code']}' 
											AND pdYear = '{$pdPeriod['pdYear']}'
											AND pdNumber = '{$pdPeriod['pdNumber']}'
                            				)");
				}
		} 
		$qry = "SELECT *
				FROM tblEmpLoans INNER JOIN
                tblEmpMast ON tblEmpLoans.empNo = tblEmpMast.empNo
				WHERE (tblEmpLoans.compCode = '$compCode') AND 
				(tblEmpLoans.lonTypeCd like '3%') AND
				(tblEmpLoans.lonTypeCd NOT IN (38,32)) AND 
                      (tblEmpLoans.lonStat = 'O') AND (tblEmpLoans.lonCurbal > 0) AND  
			   		  (tblEmpMast.compCode = '$compCode') AND (tblEmpMast.empPayGrp = '" . $pdPeriod['payGrp'] . "') AND 
                      (tblEmpMast.empNo IN (
							  				SELECT empNo FROM tblLastPayEmp
                            				WHERE compCode = '{$_SESSION['company_code']}' 
											AND pdYear = '{$pdPeriod['pdYear']}'
											AND pdNumber = '{$pdPeriod['pdNumber']}'
                            				))";
		
		if($Trns){
			$Trns = $res = $this->execQry($qry);
			$arr = $this->getArrRes($res);
		}
		foreach ($arr as $val){
			$currentbal = $val['lonCurbal'];
			$dedAmt = $currentbal;
			$lp=$val['lonLastPay'];
			if ($lp !="") {
				$lonlstpay=$lp;
			}else{

				$lonlstpay= $pdPeriod['pdPayable'];
			}
			$qryDtl              = "INSERT INTO tblEmpLoansDtl(
									compCode,empNo,lonTypeCd,
									lonRefNo,pdYear,pdNumber,
									trnCat,trnGrp,trnAmountD,
									dedTag,lonLastPay
								  ) VALUES(
									'$compCode','{$val['empNo']}','{$val['lonTypeCd']}',
									'{$val['lonRefNo']}','" .$pdPeriod['pdYear'] . "','" .$pdPeriod['pdNumber'] . "',
									'9','" .$pdPeriod['payGrp'] . "',$dedAmt,'',
									'".date('Y-m-d',strtotime($lonlstpay))."'
								  )";
			if($Trns){
				$Trns = $this->execQry($qryDtl);
			}	
		}
		$qryUpdtPd = "UPDATE tblPayPeriod SET ";
						$qryUpdtPd .= "pdLoansTag = 'Y', ";
						$qryUpdtPd .= "pdEarningsTag = '$earnsTag' ";
						$qryUpdtPd .= "WHERE compCode = '".$compCode."' AND payGrp = '" .$pdPeriod['payGrp'] . "' AND payCat = '" .$pdPeriod['payCat'] . "' AND pdYear = '" .$pdPeriod['pdYear'] . "' AND pdNumber = '" .$pdPeriod['pdNumber'] . "'";	
		if($Trns){
			$Trns = $this->execQry($qryUpdtPd);
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
}

?>