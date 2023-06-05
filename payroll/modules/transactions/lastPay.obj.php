<?
class lastPayObj extends commonObj {
	var $get;
	
	var $session;
	function __construct($sessionVars,$method){
		$this->get = $method;
		$this->session = $sessionVars;
	}
	
	function getResignedEmp() {
		$arrPd = $this->currPayPd();
		//$qryEmpList = "Select empNo,dbo.ReplaceName(empLastName) as empLastName,dbo.ReplaceName(empFirstName) as empFirstName,dbo.ReplaceName(empMidName) as empMidName from tblEmpMast where dateResigned between '{$arrPd['pdFrmDate']}' and '{$arrPd['pdToDate']}' and empNo NOT IN (Select empNo from tblLastPayEmp) AND empPayGrp='{$_SESSION['pay_group']}' order by empLastName,empFirstName";
		// $qryEmpList = "Select empNo,dbo.ReplaceName(empLastName) as empLastName,dbo.ReplaceName(empFirstName) as empFirstName,dbo.ReplaceName(empMidName) as empMidName from tblEmpMast where (dateResigned >'05/09/2010' or endDate >'05/09/2010') and empStat IN ('RS','EOC','AWOL') and empNo NOT IN (Select empNo from tblLastPayEmp) AND empPayGrp='{$_SESSION['pay_group']}' order by empLastName,empFirstName";
		
		$qryEmpList = "Select empNo, empLastName,  empFirstName, empMidName  from tblEmpMast where (empNo IN (Select empNo from tblPAF_EmpStatushist where new_nos IN (Select natureCode from tblNatures where natureCode<>'4' and natureCode is not Null) and dateupdated between '05/09/2010' AND '{$arrPd['pdToDate']}') or dateResigned between '05/09/2010' AND '{$arrPd['pdToDate']}' or endDate between '05/09/2010' AND '{$arrPd['pdToDate']}') AND empPayGrp='{$_SESSION['pay_group']}' AND empNo Not IN (Select empNo from tblLastPayEmp where reHire is null) AND compCode='{$_SESSION['company_code']}' AND empStat in ('RS','TR','AWOL') order by emplastname";
		$rsGEmpList = $this->execQry($qryEmpList);
		return $this->getArrRes($rsGEmpList);
	}
	function currPayPd() {
		$andPayPeriod = "AND payGrp = '{$_SESSION['pay_group']}'
						 AND payCat = '{$_SESSION['pay_category']}'
						 AND pdStat IN ('O','') ";
		$arrPayPeriod = $this->getPayPeriod($_SESSION['company_code'],$andPayPeriod);
		return $arrPayPeriod;
	}
	function getPrevPayPd() {
		$arrPayPd = $this->currPayPd();
		if ($arrPayPd['pdNumber']==1) {
			$pdNum = 24;
			$pdYear = $arrPayPd['pdYear']-1;
		} else {
			$pdNum = $arrPayPd['pdNumber']-1;
			$pdYear = $arrPayPd['pdYear'];
		}
		$andPayPeriod = " And pdNumber='$pdNum' AND pdYear='$pdYear' AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}'";
		return $this->getPayPeriod($_SESSION['company_code'],$andPayPeriod);
		
	}	
	function ResignedEmp() {
		$arrEmpList = $this->get['empList'];
		$arrEmpList = explode(",",$arrEmpList);
		$qryUpdateEarn = $qryUpdateTS = "";
		for($i=0; $i<count($arrEmpList); $i++) {
			$qryInsert .= "Insert into tblLastPayEmp (compCode,empNo,pdYear,pdNumber,payGrp) values ('{$_SESSION['company_code']}','{$arrEmpList[$i]}','{$this->get['pdYear']}','{$this->get['pdNumber']}','{$_SESSION['pay_group']}');";
			$qryUpdate .="Update tblEmpmast set empPayCat='9' where empNo='{$arrEmpList[$i]}' and compCode='{$_SESSION['company_code']}'";
			$qryUpdateEarn .= "Update tblEarnTranDtl set payCat='9' where empNo='{$arrEmpList[$i]}' And compCode='{$_SESSION['company_code']}'; ";
			$qryUpdateTS .="Update tblTimesheethist set empPayCat='9' where compCode='{$_SESSION['company_code']}' AND empNo='{$arrEmpList[$i]}' AND tsDate between '{$arrPrevPayPd['pdFrmDate']}' AND '{$arrPrevPayPd['pdToDate']}'; \n";
		}
		$Trns = $this->beginTran();
		if($Trns){
			$Trns = $this->execQry($qryUpdate);
		}
		if($Trns){
			$Trns = $this->execQry($qryInsert);
		}
		if($Trns){
			$Trns = $this->execQry($qryUpdateEarn);
		}
		if($Trns){
			$Trns = $this->execQry($qryUpdateTS);
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
	
	function ResignedEmpList($empNo="") {
		if ($empNo != "") {
			$filter = " AND tblLastPayEmp.empNo='$empNo'";
		}
		$arrPd = $this->currPayPd();
		$sqlEmpList = "Select tblLastPayEmp.*,  empLastName, empFirstName,  empMidName,empDrate from tblLastPayEmp Inner Join tblEmpMast ON
						tblLastPayEmp.empNo=tblEmpMast.empNo
						AND tblLastPayEmp.compCode= tblEmpMast.compCode
						where pdYear='{$arrPd['pdYear']}' 
						AND pdNumber = '{$arrPd['pdNumber']}'
						AND empPayGrp='{$_SESSION['pay_group']}' 
						$filter
						order by empLastName,empFirstName
		";
		$rsGEmpList = $this->execQry($sqlEmpList);
		if ($filter == "")
			return $this->getArrRes($rsGEmpList);			
		else
			return $this->getSqlAssoc($rsGEmpList);			
	}
	
	function DelEmp($empNo) {
		$arrPd = $this->currPayPd();
		$qryDel = "Delete from tblLastPayEmp where empNo = '$empNo' 
						AND pdYear='{$arrPd['pdYear']}' 
						AND pdNumber = '{$arrPd['pdNumber']}'
						AND compCode='{$_SESSION['company_code']}'
						 ";
		$qryDelData	= "Delete from tblLastPayData where empNo = '$empNo' AND compCode='{$_SESSION['company_code']}'";			 
		$Trns = $this->beginTran();
		if($Trns){
			$Trns = $this->execQry($qryDelData);
		}
		if($Trns){
			$Trns = $this->execQry($qryDel);
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
	function Leaves($empNo) {
		$qryLeaves = "Select * from tblLastPayData where compCode='{$_SESSION['company_code']}' and empNo='$empNo'";
		return $this->getSqlAssoc($this->execQry($qryLeaves));	
	}
	function SaveLeaves($act) {
		$arrPd = $this->currPayPd();
		if ($act=="Add") 
			$qryLeaves = "Insert into tblLastPayData (compCode,empNo,pdYear,pdNumber,leaveDays,leaveAmt,cashBond,Stat) values ('{$_SESSION['company_code']}','{$this->get['empNo']}','{$arrPd['pdYear']}','{$arrPd['pdNumber']}','{$this->get['txtLeaves']}','{$this->get['txtLeavesAmt']}','{$this->get['txtCashBond']}','A')";
		else	
			$qryLeaves = "Update tblLastPayData set leaveDays='{$this->get['txtLeaves']}',leaveAmt='{$this->get['txtLeavesAmt']}',cashBond='{$this->get['txtCashBond']}' where empNo ='{$this->get['empNo']}' and compCode='{$_SESSION['company_code']}'";

		return $this->execQry($qryLeaves);
	}
}
?>