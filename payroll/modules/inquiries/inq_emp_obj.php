<?
class inqEmpObj extends commonObj {

	var $compCode;
	var $empNo;
	var $empDiv;
	var $empDept;
	var $empSect;
	var $groupType;
	var $catType;
	var $orderBy;
	
	function getEmpInq() {
		if ($this->empNo>"") {$empNo1 = " AND (empNo LIKE '{$this->empNo}%')";} else {$empNo1 = "";}
		if ($this->empDiv>"" && $this->empDiv>0) {$empDiv1 = " AND (empDiv = '{$this->empDiv}')";} else {$empDiv1 = "";}
		if ($this->empDept>"" && $this->empDept>0) {$empDept1 = " AND (empDepCode = '{$this->empDept}')";} else {$empDept1 = "";}
		if ($this->empSect>"" && $this->empSect>0) {$empSect1 = " AND (empSecCode = '{$this->empSect}')";} else {$empSect1 = "";}
		if ($this->groupType<3) {$groupType1 = " AND (empPayGrp = '{$this->groupType}')";} else {$groupType1 = "";}
		if ($this->orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ASC ";} 
		if ($this->orderBy==2) {$orderBy1 = " ORDER BY empNo ASC ";} 
		if ($this->orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ASC ";}
		if ($this->catType>0) {$catType1 = " AND (empPayCat = '{$this->catType}')";} else {$catType1 = "";}
		$qry = "SELECT * FROM tblEmpMast 
						 WHERE compCode = '{$this->compCode}'
						 AND empStat NOT IN('RS','IN','TR') 
						 $empNo1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getEmpTotalByDept($compCode, $empDiv, $empDept, $empSect,$groupType,$CatType) {
		if ($groupType>"") $groupTypeNew = " AND (empPayGrp = '{$groupType}') "; else $groupTypeNew = "";
		if ($catType>"") $catTypeNew = " AND (empPayCat = '{$catType}') "; else $catTypeNew = "";
		$qry = "SELECT TOP 100 PERCENT empDiv,empDepCode,empSecCode,MAX(CONVERT(varchar,empDiv) + '-' + CONVERT(varchar,empDepCode) + '-' + CONVERT(varchar,empSecCode) 
                      	  	+ '-' + empLastName + '-' + empFirstName + '-' + empMidName) AS refMax, 
                          	COUNT(empLastName) AS totRec
						  FROM tblEmpMast
						  WHERE (compCode = '{$compCode}') AND 
                      		(empDiv = '{$empDiv}') AND
							(empDepCode = '{$empDept}') AND
							(empSecCode = '{$empSect}')  
							$groupTypeNew $catTypeNew AND 
						    empStat NOT IN('RS','IN','TR') 
						  GROUP BY empDiv,empDepCode,empSecCode";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEmpTotalByDiv() {
		if ($this->empDiv>"" && $this->empDiv>0) {$empDiv1 = " AND (tblEmpMast.empDiv = '{$this->empDiv}')  AND (tblDepartment.divCode = '{$this->empDiv}') ";} else {$empDiv1 = "";}
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND 
                (tblEmpMast.compCode = '{$this->compCode}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') 
				$empDiv1";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	function getEmpTotalByCat($empDiv) {
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec, tblPayCat.payCat, tblPayCat.payCatDesc
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv INNER JOIN
                tblPayCat ON tblEmpMast.empPayCat = tblPayCat.payCat
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND (tblPayCat.payCatStat = 'A') AND
                (tblEmpMast.compCode = '{$this->compCode}') AND (tblPayCat.compCode = '{$this->compCode}') AND 
				(tblEmpMast.empDiv = '{$empDiv}')  AND (tblDepartment.divCode = '{$empDiv}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') 
				GROUP BY tblDepartment.divCode, tblPayCat.payCat, tblPayCat.payCatDesc
				ORDER BY tblDepartment.divCode, tblPayCat.payCat, tblPayCat.payCatDesc";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	function getEmpTotalByGrp($empDiv,$empCat,$empGrp) {
		if ($empCat=="") $empCatNew = ""; else $empCatNew = " AND (tblPayCat.payCat = '{$empCat}') AND (tblEmpMast.empPayCat = '{$empCat}') "; 
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv INNER JOIN
                tblPayCat ON tblEmpMast.empPayCat = tblPayCat.payCat
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND 
                (tblEmpMast.compCode = '{$this->compCode}') AND (tblPayCat.compCode = '{$this->compCode}') AND (tblDepartment.divCode = '{$empDiv}') AND (tblEmpMast.empDiv = '{$empDiv}') AND 
                (tblPayCat.payCatStat = 'A') AND (tblEmpMast.empPayGrp = '{$empGrp}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') $empCatNew";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	function get_list_holiday($compCode)
	{
	
		$query_list_holiday = 	"SELECT DISTINCT YEAR(holidayDate) as Holiday_Year
				 				FROM tblHolidayCalendar
				 				WHERE compCode='".$compCode."' 
				 				ORDER BY YEAR(holidayDate) DESC";
		
								
		$rs_list_holiday = $this->execQry($query_list_holiday);
		return $this->getArrRes($rs_list_holiday);
	}
	function update_upload($compCode,$empNo,$data)
	{
		$qry = "SELECT * FROM tblEmpMastImage WHERE compCode = '$compCode' AND empNo = '$empNo' ";
		$res = $this->execQry($qry);
		$arr = $this->getArrRes($res);
		if (count($arr)<=0 || count($arr)=="") {
			$qry = "Insert into tblEmpMastImage (compCode, empNo, empImage) values ($compCode, '$empNo', $data)";
			echo $qry;
			$res = $this->execQry($qry);
		} else {
			$qry = "DELETE tblEmpMastImage WHERE compCode = '$compCode' AND empNo = '$empNo' ";
			$res = $this->execQry($qry);
			$qry = "Insert into tblEmpMastImage (compCode, empNo, empImage) values ($compCode, '$empNo', $data)";
			$res = $this->execQry($qry);
		}
	}
	function showCam($compCode,$empNo) {
		$qry = "UPDATE tblShowCam SET shwStat = 'Y', compCode = '$compCode', empNo = '$empNo' ";
		$res = $this->execQry($qry);
	}

}

?>