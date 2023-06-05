<?php
class migCSIObj extends commonObj {
var $branchlist = array();

	function readCSITxtfile($filename) { 
		$file = fopen($filename,"r");
			$counter=0;
			$Trns = $this->beginTran();
			$err = true;
			while(! feof($file))
			  {
				 $array_rec = str_replace('"',"",fgets($file));
				 $array_rec = explode(",",$array_rec);
				 $compCode = $_SESSION['company_code'];
				 $verifyEmp = $this->verifyEmp($array_rec[0],$compCode['compCode']);
				 if (strlen(trim($array_rec[2])) > 0 && count($verifyEmp) != 0) {
					$err = false;
					$lonTypeCd = LOAN_CSI_TypeCd;				 
					//$refNo = substr($array_rec[1],8,10);
					$data['empNo']			= $array_rec[0];
					$data['lonTypeCd'] 		= $lonTypeCd;
					$data['lonRefNo'] 		= $array_rec[1];
					$data['lonAmt'] 		= $array_rec[2];
					$data['lonWidInterst'] 	= $array_rec[2];
					$data['lonSked'] 		= 3;			
					$data['lonDedAmt1']		= $array_rec[3];
					$data['lonDedAmt2']		= $array_rec[3];
					$data['lonNoPaymnts'] 	= round($array_rec[2]/$array_rec[3],0);
					$data['lonPaymentNo']	= 0;
					$data['lonCurbal'] 		= $array_rec[2];
					$data['lonStat'] 		= "O";	
					$data['lonGranted']		= date('m/d/Y');
					$data['lonStart'] 		= date('m/d/Y');
					$data['compCode'] 		= $compCode;
						
					if ($Trns) {
						$Trns = $this->insertLoans($data);
					}	
					$counter++;					
				 }
			  }	
			  
		 fclose($file);
		 unlink($filename);
			if($Trns == true && $err == false){
				$Trns = $this->commitTran();
				return true;	
			}
			else{
				$Trns = $this->rollbackTran();
				return false;

			}
	}
	
	function insertLoans($values) {
		$fields = " compCode,
					empNo,
					lonTypeCd,
					lonRefNo,
					lonAmt,
					lonWidInterst,
					lonGranted,
					lonStart,
					lonSked,
					lonDedAmt1,
					lonDedAmt2,
					lonNoPaymnts,
					lonPaymentNo,
					lonCurbal,
					lonStat,
					UploadTag,
					mmsNo,
					dateadded";
		$data = "'{$values['compCode']}',
					'{$values['empNo']}',
					'{$values['lonTypeCd']}',
					'{$values['lonRefNo']}',
					{$values['lonAmt']},
					{$values['lonWidInterst']},
					'{$values['lonGranted']}',
					'{$values['lonStart']}',
					'{$values['lonSked']}',			
					{$values['lonDedAmt1']},			
					{$values['lonDedAmt2']},			
					{$values['lonNoPaymnts']},
					{$values['lonPaymentNo']},
					{$values['lonCurbal']},
					'O',
					1,
					'{$values['mmsNo']}',
					'".date('m/d/Y')."'";			
		$qryinsert = "insert into tblEmpLoans ($fields) values ($data);";
		return $this->execQry($qryinsert);
	}
	function verifyEmp($empNo,$compCode) {
		$qryverify = "Select empNo from tblEmpMast where empNo='$empNo' and compCode='$compCode'";
		$res = $this->getSqlAssoc($this->execQry($qryverify));
		$cnt=0;
		if ($res['empNo'] != "") {
			$cnt=1;
		}
		return $cnt;
	}
	
	function getcompCode($brnCode) {
		$qrycompCode = "Select compCode from tblBranch where brnCode='$brnCode'";
		$res = $this->execQry($qrycompCode);
		return $this->getSqlAssoc($res);
	}
	
	function computeCSIammort($Amount) {
		$noDed = 0;
		if ($Amount < 1001) {
			$Amount = $Amount;
			$noDed = 1;
		}
				  
		if ($Amount > 1000 && $Amount < 2001) {
		 	$Amount = $Amount / 2;
			$noDed = 2;
		}
				   
		if ($Amount > 2000 && $Amount < 4001) {
		 	$Amount = $Amount / 4;
			$noDed = 4;
		}
				  
		if ($Amount > 4000 && $Amount < 7001) {
		  	$Amount = $Amount / 6;
			$noDed = 6;
		}
				   
		if ($Amount > 7000 && $Amount < 10001) {
			$Amount = $Amount / 8;
			$noDed = 8;
		}
				 
		if ($Amount > 10000 && $Amount < 13001) {
		   	$Amount = $Amount / 10;
			$noDed = 10;
		}
				   
		if ($Amount > 13000 && $Amount < 16001) {
		 	$Amount = $Amount / 12;
			$noDed = 12;
		}
				   
		if ($Amount > 16000 && $Amount < 19001) {
		 	$Amount = $Amount / 14;
			$noDed = 14;
		}
				  
		if ($Amount > 19000 && $Amount < 21001) {
		 	$Amount = $Amount / 16;
			$noDed = 16;
		}

		if ($Amount > 21000 && $Amount < 24001) {
		 	$Amount = $Amount / 18;
			$noDed = 18;
		}

		if ($Amount > 24000) {
			$Amount = $Amount / 20;
			$noDed = 20;
		} 
		$Value['Amount'] = 	round($Amount,2);
		$Value['noDed'] = $noDed;
		return $Value;
	}
	
	function readLSTxtfile($filename) { 
		$this->getBranchNamesList();
		$file = fopen($filename,"r");
			$counter=0;
			$Trns = $this->beginTran();
			$err = true;
			if ($Trns) {
				$Clearsql = "Delete from tblARTransData where userID='{$_SESSION['user_id']}'";
				$Trns = $this->execQry($Clearsql);
						
			}			
			while(! feof($file))
			  {
				 
				 $array_rec = str_replace('"',"",fgets($file));
				 $array_rec = explode(",",$array_rec);
				 //$verifyEmp = $this->verifyEmp($array_rec[1],$array_rec[0]);
				 $empNo = $this->getEmpNo(str_replace('1002','',$array_rec[1]));
				 //$empNo = $array_rec[1];
				  
					$err = false;
					switch($array_rec[3]) {
						case 'HS':
							$lonTypeCd = 39;
						break;	
						case 'CS':
							$lonTypeCd = 301;
						break;
						default:
							$lonTypeCd = $array_rec[3];
						break;
					}
					
					$refNo = $array_rec[0];
					$arrAmort = $this->computeCSIammort($array_rec[4]);
					$compCode		= $_SESSION['company_code'];
					$empNo			= $empNo;
					$lonTypeCd 		= $lonTypeCd;
					$lonRefNo 		= trim($array_rec[2]);
					$compGLCode 	= trim($array_rec[0]);
					$lonAmt 		= $array_rec[4];
					$lonWidInterst 	= $array_rec[4];
					$lonSked 		= 3;			
					$lonDedAmt1		= (float)$arrAmort['Amount'];
					$lonDedAmt2		= (float)$arrAmort['Amount'];
					$lonNoPaymnts 	= (int)$arrAmort['noDed'];
					$lonPaymentNo	= 0;
					$lonCurbal 		= $array_rec[4];
					$startDate		= (trim($array_rec[6]) != "") ? date('m/d/Y',strtotime($array_rec[6])):date('m/d/Y');
					$lonGranted		= $startDate;
					$lonStart 		= $startDate;
					$mmsNo 			= $array_rec[5];
					$strName		= (is_numeric(trim($array_rec[8]))) ? $this->getShortName(trim($array_rec[8])):trim($array_rec[8]);
						
					if ($Trns == true && trim($array_rec[1]) != "") {
						$sql = "Insert into tblARTransData (custNo, empno, refNo, transType, amount, dedSked, invoiceNo, NoDed, transDate, userID, fileName,dedAmt,compGLCode,strName) values
								('{$array_rec[1]}','$empNo','$lonRefNo','$lonTypeCd','$lonAmt','$lonSked','$mmsNo','$lonNoPaymnts','$lonGranted','{$_SESSION['user_id']}','$filename','$lonDedAmt1','$compGLCode','$strName')
						";
						
						$Trns = $this->execQry($sql);
						
					}	
					$counter++;	
			  }	
			  
		 fclose($file);
		 
			if($Trns == true && $err == false){
				$Trns = $this->commitTran();
				return true;	
			}
			else{
				$Trns = $this->rollbackTran();
				return false;

			}
	}	
	
	function getOpenPeriod() 
	{
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdStat = 'O' AND 
			    compCode = '{$_SESSION['company_code']}' AND
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
					
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}	
	
	function getEmpNo($custNo) {
		$sql = "Select cust.empNo from tblCustomerNo cust inner join tblEmpmast emp on cust.empNo=emp.empNo where custNo='$custNo' and empStat IN ('RG','CN','PR')";
		$res = $this->execQry($sql);
		$res = $this->getSqlAssoc($res);
		return $res['empNo'];
		
	}
	function getBranchNamesList() {
		$sql = "Select glCodeStr as strCode,brnShortName as sname from tblBranch";	
		$this->branchlist = $this->getArrRes($this->execQry($sql));
	}
	
	function getShortName($strCode) {
		$sname = "";
		foreach($this->branchlist as $val)	 {
			if ($val['strCode'] == $strCode)  {
				$sname = $val['sname']	;
				break;
			}
				
		}
		return $sname;
	}
	
/*	function AddAudit($field) {
		$arrPd = $this->getOpenPeriod();
		$curDate = date('m/d/Y');
		if ($this->CheckAudit() == 0)
			$sqlAdd = "Insert into tblPayExtDataAudit (compCode,pdYear,pdNumber,dateAdded,$field) values ('{$_SESSION['company_code']}','{$arrPd['pdYear']}','{$arrPd['pdNumber']}','$curDate',1)";
		else
			$sqlAdd = "Update tblPayExtDataAudit set $field=1,dateAdded='$curDate' where compCode='{$_SESSION['company_code']}' AND pdYear='{$arrPd['pdYear']}' AND pdNumber='{$arrPd['pdNumber']}'";
		return $this->execQry($sqlAdd);
	}	
	function CheckAudit() {
		$arrPd = $this->getOpenPeriod();
		$sqlCheck = "Select compCode from tblPayExtDataAudit where compCode='{$_SESSION['company_code']}' AND pdYear='{$arrPd['pdYear']}' AND pdNumber='{$arrPd['pdNumber']}'";
		return $this->getRecCount($this->execQry($sqlCheck));
	}
*/}
?>