<?php
class migEmpMastObj extends commonObj {

function checkDuplicateEmpNo($empNo,$compCode)
{
	$qryCheckEmp = "Select count(*) as empCnt from tblEmpMast where empNo='".$empNo."' and compCode='".$compCode."'";
	$rsCheckEmp = $this->execQry($qryCheckEmp);
	$rowCheckEmp = $this->getSqlAssoc($rsCheckEmp);
	$empExists = ($rowCheckEmp["empCnt"]>=1?1:0);
	return $empExists;
}

function getempStatDef($empStat)
{
	
	switch($empStat)
	{
		case "REGULAR":
			$empStat = "RG";
			break;
		
		case "PROBATIONARY":
			$empStat = "PR";
			break;
		
		case "CONTRACTUAL":
			$empStat = "CN";
			break;
		default:
			$empStat = "UNKNOWN";
			break;
	}
	
	return $empStat; 
}

function checkSssNo($empSssNo)
{
	///trim first the SSS No
	$empSssNo = str_replace("-","",$empSssNo);
	
	//check Length of SssNo
	$empSssNoLength = strlen($empSssNo);
	
	$empSssNo = ($empSssNoLength!=10?0:$empSssNo);
	return $empSssNo;
}

function getBankDef($bankName,$compCode)
{

	
	
	/*switch($bankName)
	{
		
		case "MBTC-PRICE CLUB":
			$bankName = "RG";
			break;
		
		default:
			$empStat = "UNKNOWN";
			break;
	}
	
	return $empStat; */
}

function getRateDef($rateType)
{
	switch($rateType)
	{
		case "PER MONTH":
			$rateType = "M";
			break;
		
		case "PER DAY":
			$rateType = "D";
			break;
		
		default:
			$rateType = 0;
			break;
	}
	
	return $rateType;
}

function getPayCat($empPayType,$compCode)
{
	$qryPayCat = "Select payCat from tblPayCat where compCode='".$compCode."' and payCatDesc like '%".$empPayType."%'";
	$rsPayCat = $this->execQry($qryPayCat);
	$rowPayCat = $this->getSqlAssoc($rsPayCat);
	
	$empPayCat = ($rowPayCat["payCat"]!=""?$rowPayCat["payCat"]:0);
	return $empPayCat;
}

function getMarStatDef($empTeu)
{
	$single = array('HF','HF1','HF2','HF3','HF4','S','Z');
		
	if(in_array($empTeu,$single))
	{
		$empStat = "SG";
	}
	else
	{
		$empStat = "ME";
	}
	
	return $empStat;
		
}

function getComputedMRate($empDRate,$compCode)
{
	$qryComp = "Select compNoDays from tblCompany where compCode='".$compCode."'";
	$rsComp = $this->execQry($qryComp);
	$rowComp = $this->getSqlAssoc($rsComp);
	
	$empMrate = $empDRate * $rowComp["compNoDays"];
	$empMrate = sprintf("%01.2f",$empMrate);
	return 	$empMrate;
}



function addParadox(){
		if ($this->hrsAbsent2>"") $hrsA   = $this->hrsAbsent2; else $hrsA   = "0";
		if ($this->hrsTardy2>"")  $hrsT   = $this->hrsTardy2;  else $hrsT   = "0";
		if ($this->hrsUt2>"")     $hrsU   = $this->hrsUt2;     else $hrsU   = "0";
		if ($this->hrsOtLe82>"")  $hrsOtL = $this->hrsOtLe82;  else $hrsOtL = "0";
		if ($this->hrsOtGt82>"")  $hrsOtG = $this->hrsOtGt82;  else $hrsOtG = "0";
		if ($this->hrsNdLe82>"")  $hrsNdL = $this->hrsNdLe82;  else $hrsNdL = "0";
		if ($this->hrsNdGt82>"")  $hrsNdG = $this->hrsNdGt82;  else $hrsNdG = "0";
		$qry                = "INSERT INTO tblTsParadox(
								compCode,empNo,tsDate,
								hrsAbsent,hrsTardy,hrsUt,
								hrsOtLe8,hrsOtGt8,hrsNdLe8,
								hrsNdGt8, tsRemarks
							  )VALUES(
							  	'{$this->compCode2}','{$this->empNo2}','{$this->dateFormat($this->tsDate2)}',
							  	'{$hrsA}','{$hrsT}','{$hrsU}',
							  	'{$hrsOtL}','{$hrsOtG}','{$hrsNdL}',
							  	'{$hrsNdG}','{$this->tsRemarks2}'
							  )";
		$res = $this->execQry($qry);
		/*
		if($res){
			return true;
		}
		else {
			$this->errorLog(mysql_get_last_message(),$qry,__LINE__,'timesheet_obj.php');
			return false;
		}
		*/	
	}
}
?>