<?
class adjustmentObj extends commonObj {
	var $get;
	var $session;
	var $payGrp;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	function ProcessTSAdjustment(){
		$trans = $this->beginTran();
			$qry = "Select compCode,pdYear,pdNumber,transTypeCode 
					from tblTK_tmpTimesheetAdjustment
					where compCode='{$this->session['company_code']}' 
						and payGrp='{$this->payGrp}'
					group by compCode,pdYear,pdNumber,transTypeCode";
					
//			$qry = "Select tmp.compCode,tmp.pdYear,tmp.pdNumber,tmp.transTypeCode 
//					From tblTK_tmpTimesheetAdjustment tmp
//					Inner join tblEmpMast emp on emp.empNo =tmp.empNo
//					Where emp.empBrnCode in (Select brnCode from tblTK_UserBranch 
//											where empNo='{$this->session['employee_number']}' 
//												and compCode='{$this->session['company_code']}')
//						and tmp.compCode='{$this->session['company_code']}' and tmp.payGrp='{$this->payGrp}'
//					Group by tmp.compCode,tmp.pdYear,tmp.pdNumber,tmp.transTypeCode";
			$qryres = $this->execQry($qry);
			if($this->getRecCount($qryres)>0){
//				$this->execQry("Delete from tblTK_EarnTranHeader where processedBy='{$this->session['employee_number']}'");			
//				$this->execQry("Delete from tblTK_EarnTranDtl where processedBy='{$this->session['employee_number']}'");	

				$this->execQry("Delete from tblTK_EarnTranHeader");			
				$this->execQry("Delete from tblTK_EarnTranDtl");	
				
				$res = $this->getArrRes($qryres);
//				$qryEarnHeader = "";
//				$qryEarnDtl = "";

				foreach($res as $val){		
					$refNo = $this->getEarnRefNo();
					$transDesc = $this->getPayTransType($val['transTypeCode']);
					$tranTaxCd = $transDesc['trnTaxCd'];
					$qryEarnHeader = "Insert into tblTK_EarnTranHeader (compCode, refNo, trnCode, earnRem,
											 earnStat, pdYear, pdNumber, processedBy, processedDate)
									   Values ('{$val['compCode']}','{$refNo}','{$val['transTypeCode']}',
									 		'{$transDesc['trnDesc']}','A','{$val['pdYear']}','{$val['pdNumber']}',
											'{$this->session['employee_number']}','".date('Y-m-d')."');"; 					
					if($trans){
						$trans = $this->execQry($qryEarnHeader);	
					}
					$qryCnt = $this->execQry("Select * from tblTK_tmpTimesheetAdjustment 
								where compCode='{$val['compCode']}' and transTypeCode='{$val['transTypeCode']}'");
//					$qryCnt = $this->execQry("Select tmp.compCode,tmp.empNo,tmp.tsDate,tmp.dayType,tmp.payGrp, tmp.payCat, tmp.pdYear, 
//												tmp.pdNumber, tmp.entryTag, tmp.adjAmount, tmp.transTypeCode, tmp.tsStat 
//											  From tblTK_tmpTimesheetAdjustment tmp
//											  Inner join tblEmpMast emp on emp.empNo= tmp.empNo
//											  Where emp.empBrnCode in (Select brnCode from tblTK_UserBranch 
//																		where empNo='{$this->session['employee_number']}' 
//																			and compCode='{$val['compCode']}')
//												and tmp.compCode='{$val['compCode']}' and tmp.transTypeCode='{$val['transTypeCode']}'");
					$cntRes = $this->getRecCount($qryCnt);
					$resCnt = $this->getArrRes($qryCnt);
					$x=1;
					foreach($resCnt as $valResCnt){			
						$qryEarnDtl = "Insert into tblTK_EarnTranDtl (compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount,
											payGrp, payCat, earnStat, trnTaxCd, processedBy, processedDate) 
										Values ('{$valResCnt['compCode']}','{$refNo}','{$valResCnt['empNo']}','{$x}',
											'{$valResCnt['transTypeCode']}','{$valResCnt['adjAmount']}','{$valResCnt['payGrp']}',
											'{$valResCnt['payCat']}','A','{$tranTaxCd}',
											'{$this->session['employee_number']}','".date('Y-m-d')."');";	
						if($trans){
							$trans = $this->execQry($qryEarnDtl);	
						}
						if($x!=$cntRes){
							$x++;	
						}
					}											
				}						
			}	
		
//		if($qryEarnDtl!=""){
//			if($trans){
//				$trans = $this->execQry($qryEarnDtl);	
//			}
//		}		
//		if($qryEarnHeader!=""){
//			if($trans){
//				$trans = $this->execQry($qryEarnHeader);	
//			}
//		}
														 		
		if(!$trans){
			$trans = $this->rollbackTran();
			return false;	
		}
		else{
			$trans = $this->commitTran();
			return true;	
		}
	}
	
	function processAdjustmentSetup(){
			$qryIns = "CALL sp_combineAdjustment ('".$this->payGrp."','".$this->session['company_code']."','".$this->session['employee_number']."')";
			$trns = $this->execQry($qryIns);
				
		if(!$trns){
			return false;	
		}
		else{
			return true;	
		}
	}
	
	function getEarnRefNo(){
		$qry = $this->execQry("Select earnRefNo from tblEarnRefNo where compCode='{$this->session['company_code']}'");
		$qryres = $this->getSqlAssoc($qry);
		$cnt = $qryres['earnRefNo'] + 1;
		$ins = $this->execQry("Update tblEarnRefNo set earnRefNo='{$cnt}'");
		if($ins){
			return $cnt;	
		}	
		else{
			return false;	
		}
	}
	
	function getPayTransType($transcode){
		$qry = $this->execQry("Select trnCode, trnDesc, trnTaxCd from tblPayTransType where trnCode='{$transcode}' and trnStat='A'");	
		$qryRes = $this->getSqlAssoc($qry);
		return $qryRes;
	}
	
}

?>