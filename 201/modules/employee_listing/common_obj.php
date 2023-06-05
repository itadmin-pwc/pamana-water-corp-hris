<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Common Trans, js, obj, ajax instead of useing timesheet 
*/

class inqTSObj extends commonObj {

	function selectdpayCat($payCat) {
		$sqlpayCat = "Select * from tblPayCat where compCode = '{$_SESSION['company_code']}' and payCat IN ($payCat)";
		return $this->getArrRes($this->execQry($sqlpayCat));
	}

	function getRankType()
	{
		

		$qryRank =	"SELECT  rankCode, rankDesc FROM tblEmpMast tblEmp, tblRankType 
					WHERE  empRank=rankCode 
					group by rankCode, rankDesc
					order by rankDesc";
		$rsRank = $this->execQry($qryRank);
		return $this->getArrRes($rsRank);
	}

	
	function getListofEmp($empDiv1, $empDept1, $empSect1, $empBrnCode1, $empRankCode1) 
	{
		$empBrnCode = $empBrnCode1;
		$empDiv = $empDiv1;
		$empDept =  $empDept1;
		$empSect = $empSect1;
		$empRankCode =  $empRankCode1;
		
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
		if ($empRankCode>"" && $empRankCode>0) {$empRankCode1 = " AND (empRank = '{$empRankCode}')";} else {$empRankCode1 = "";}
		
		$sqlEmp = "SELECT empmast.compCode,empNo, empLastName, empFirstName, empMidName,empmast.empBrnCode, brnShortDesc,empRank, empDiv, empDepCode, empSecCode, dateHired, empBday, empStat, empPosId, employmentTag
					FROM tblEmpMast empmast, tblBranch brnCode
					WHERE (empmast.compCode = '".$_SESSION["company_code"]."' and brnCode.compCode='".$_SESSION["company_code"]."') 
					$empDiv1 $empDept1 $empSect1 $empBrnCode1  $empRankCode1
					and empmast.empBrnCode=brnCode.brnCode and empStat='RS' and employmentTag IN ('RG', 'PR', 'CN')
					and brnCode.compCode='".$_SESSION["company_code"]."'
					order by brnDesc, empLastName, empFirstName, empMidName; ";		
				
	
		$resEmp = $this->execQry($sqlEmp);	
		return $arrEmp = $this->getArrRes($resEmp);
	}
	
	function getTblData($tbl, $cond, $orderBy, $ouputType)
	{
		$qryTblInfo = "Select * from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
		$resTblInfo = $this->execQry($qryTblInfo);
		if($ouputType == 'sqlAssoc')
			return $this->getSqlAssoc($resTblInfo);
		else
			return $this->getArrRes($resTblInfo);
	}
	
	
}

?>