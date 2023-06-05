<?
class inqTSObj extends commonObj {

	var $compCode;
	var $empNo;
	var $empName;
	var $empDiv;
	var $empDept;
	var $empSect;
	var $groupType;
	var $catType;
	var $orderBy;
	
	function getAllPeriod($compCode,$groupType,$catType) 
	{
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE compCode = '$compCode' AND 
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}'";
		
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getSlctdPd($compCode,$payPd) 
	{
		$qry = "SELECT * FROM tblPayPeriod 
				WHERE pdSeries = '$payPd' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function getOpenPeriod() 
	{
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdStat = 'O' AND 
			    compCode = '".$_SESSION["company_code"]."' AND
				payGrp = '{$_SESSION['pay_group']}' AND 
				payCat = '{$_SESSION['pay_category']}' ";
					
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function chkMonMtdGov($monthto,$pdYear,$empNo)
	{
		$where = ($empNo!=""?"and empNo='".$empNo."'":"");
		
		
		$monthto = $monthto."/28/".$pdYear;
		
		
		$qryChk = "Select * from tblMtdGovt where compCode='".$_SESSION["company_code"]."' and convert(datetime,(convert(varchar,pdMonth)+'/28/'+convert(varchar,pdYear)))  < '".$monthto."' $where
					and empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayCat='".$_SESSION["pay_category"]."' and empPayGrp='".$_SESSION["pay_group"]."' and empStat NOT IN('RS','IN','TR'))";
		
		$resChk = $this->execQry($qryChk);
		return $this->getRecCount($resChk);
	}
	
	function chkMonMtdGovHist($monthfr,$monthto,$empNo)
	{
		$where = ($empNo!=""?"and empNo='".$empNo."'":"");
		
		/*$qryChk = "Select * from tblMtdGovtHist
					where compCode='".$_SESSION["company_code"]."' and pdMonth between '".$filter_mfr."' and '".$filter_mto."' 
					and pdYear between '".$filter_yfr."' and '".$filter_yto."' $where
					and empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayCat='".$_SESSION["pay_category"]."' and empPayGrp='".$_SESSION["pay_group"]."' and empStat NOT IN('RS','IN','TR'))";
		*/
		$monthfr = date("m/d/Y", strtotime($monthfr));
		$monthto = date("m/d/Y", strtotime($monthto));
		
		$qryChk = "Select * from tblMtdGovtHist where compCode='".$_SESSION["company_code"]."' and convert(datetime,(convert(varchar,pdMonth)+'/28/'+convert(varchar,pdYear)))  between '".$monthfr."' and '".$monthto."' $where
					and empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayCat='".$_SESSION["pay_category"]."' and empPayGrp='".$_SESSION["pay_group"]."' and empStat NOT IN('RS','IN','TR'))";
		
		
		$resChk = $this->execQry($qryChk);
		return $this->getRecCount($resChk);
	}
	
	function chkUnpostedTran($pdNum, $pdYear, $tbl)
	{
		$qryUnpostedTran = "Select * from ".$tbl."
							where empNo in (Select empNo from tblEmpMast where
							compCode='".$_SESSION["company_code"]."' and empPayCat='".$_SESSION["pay_category"]."' and empPayGrp='".$_SESSION["pay_group"]."' and 
							empStat IN ('RG','PR','CN')
							and compCode='".$_SESSION["company_code"]."'
							and pdNumber ='".$pdNum."' and pdYear='".$pdYear."')";
	
		$resUnpostedTran = $this->execQry($qryUnpostedTran);
		return $this->getRecCount($resUnpostedTran);
	}

	
}	

?>