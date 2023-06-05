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
	var $arrOthers 		= array();
	var $arrEmpStat 	= array();
	var $arrBranch 		= array();
	var $arrPosition 	= array();
	var $arrPayroll 	= array();
	var $arrAllow 		= array();
	
	function getEmpInq() {
		if ($this->empNo>"") {$empNo1 = " AND (empNo LIKE '{$this->empNo}%')";} else {$empNo1 = "";}
		if ($this->empName>"") {$empName1 = " AND (empLastName LIKE '{$this->empName}%' OR empFirstName LIKE '{$this->empName}%' OR empMidName LIKE '{$this->empName}%')";} else {$empName1 = "";}
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
						 $empNo1 $empDiv1 $empName1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";
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
	function convertArr($table,$And) {
		$array = array();
		$qry = "SELECT $table.empNo from tblEmpMast INNER JOIN $table ON tblEmpMast.compCode = $table.compCode AND tblEmpMast.empNo = $table.empNo where tblEmpMast.compCode='{$_SESSION['company_code']}' $And ";
		$res = $this->getArrRes($this->execQry($qry));
		foreach($res as $val) {
			$array[] = $val['empNo']; 
		}
		return $array;	
	}
	
function getPAF_others($empNo,$pafType,$and="",$hist="") {
		$i=0;
		$compCode = $_SESSION['company_code'];
		if (empty($pafType) || $pafType == "others") {
			if (in_array($empNo,$this->arrOthers)){
				$qryPAF = "Select compCode, empNo, old_empLastName, old_empFirstName, old_empMidName, old_empAddr1, old_empAddr2, old_empCityCd, old_empTin, 
                      old_empSssNo, old_empPhicNo, old_empPagibig, new_empLastName, new_empFirstName, new_empMidName, new_empAddr1, new_empAddr2, 
                      new_empCityCd, new_empTin, new_empSssNo, new_empPhicNo, new_empPagibig, stat, effectivitydate, remarks, dateadded, dateupdated, 
                      user_created, user_updated, refNo, controlNo, datereleased, dateposted from tblPAF_Others$hist where empNo=$empNo $and  GROUP BY compCode, empNo, old_empLastName, old_empFirstName, old_empMidName, old_empAddr1, old_empAddr2, old_empCityCd, old_empTin, 
                      old_empSssNo, old_empPhicNo, old_empPagibig, new_empLastName, new_empFirstName, new_empMidName, new_empAddr1, new_empAddr2, 
                      new_empCityCd, new_empTin, new_empSssNo, new_empPhicNo, new_empPagibig, stat, effectivitydate, remarks, dateadded, dateupdated, 
                      user_created, user_updated, refNo, controlNo, datereleased, dateposted order by effectivitydate";
			$res = $this->getArrRes($this->execQry($qryPAF));
			$arrPAF =array('field','value1','value2','effdate','refno');
			foreach($res as $val) {
				if (trim($val['new_empLastName']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Last Name";
					$arrPAF['value1'][$i]=$val['old_empLastName'];
					$arrPAF['value2'][$i]=$val['new_empLastName'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empFirstName']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="First Name";
					$arrPAF['value1'][$i]=$val['old_empFirstName'];
					$arrPAF['value2'][$i]=$val['new_empFirstName'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empMidName']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Middle Name";
					$arrPAF['value1'][$i]=$val['old_empMidName'];
					$arrPAF['value2'][$i]=$val['new_empMidName'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empAddr1']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Home No, Bldg., Street";
					$arrPAF['value1'][$i]=$val['old_empAddr1'];
					$arrPAF['value2'][$i]=$val['new_empAddr1'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empAddr2']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Barangay, Municipality";
					$arrPAF['value1'][$i]=$val['old_empAddr2'];
					$arrPAF['value2'][$i]=$val['new_empAddr2'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if ($val['new_empCityCd'] !=0) {
					$old_city = $this->getcitywil(" where CityCd='{$val['old_empCityCd']}'");
					$new_city = $this->getcitywil(" where CityCd='{$val['new_empCityCd']}'");
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="City";
					$arrPAF['value1'][$i]=$old_city['cityDesc'];
					$arrPAF['value2'][$i]=$new_city['cityDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empSssNo']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="SSS No.";
					$arrPAF['value1'][$i]=$val['old_empSssNo'];
					$arrPAF['value2'][$i]=$val['new_empSssNo'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empPhicNo']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Phil Health No.";
					$arrPAF['value1'][$i]=$val['old_empPhicNo'];
					$arrPAF['value2'][$i]=$val['new_empPhicNo'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empTin']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Tax ID No.";
					$arrPAF['value1'][$i]=$val['old_empTin'];
					$arrPAF['value2'][$i]=$val['new_empTin'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empPagibig']) !="") {
					$arrPAF['type'][$i]='others,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="HDMF No.";
					$arrPAF['value1'][$i]=$val['old_empPagibig'];
					$arrPAF['value2'][$i]=$val['new_empPagibig'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
			}
			unset($res,$val,$qryPAF);
			}
		}
		if (empty($pafType) || $pafType == "empstat") {
			if (in_array($empNo,$this->arrEmpStat)){
				$qryPAF = "Select compCode, empNo, old_status, new_status, effectivitydate, remarks, dateadded, dateupdated, user_created, user_updated, refNo, controlNo, 
                      datereleased, stat, dateposted from tblPAF_EmpStatus$hist where empNo=$empNo $and 
					  GROUP BY compCode, empNo, old_status, new_status, effectivitydate, remarks, dateadded, dateupdated, user_created, user_updated, refNo, controlNo, 
                      datereleased, stat, dateposted
					  order by effectivitydate";
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				if (trim($val['new_status']) !="") {
					$arrPAF['type'][$i]='empstat,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Employment Status";
					$arrPAF['value1'][$i]=$this->EmpStat($val['old_status']);
					$arrPAF['value2'][$i]=$this->EmpStat($val['new_status']);
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
			}
			unset($res,$val,$qryPAF);
			}
		}
		if (empty($pafType) || $pafType == "allow") {
			if (in_array($empNo,$this->arrAllow)){
				if ($and != "")
					$and2 = str_replace('stat','stat',$and);
					
				$qryPAF = "Select compCode, empNo, allowCode, allowAmt, allowAmtold, allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd, allowStat, sprtPS, 
                      refNo, controlNo, effectivitydate, dateadded, dateupdated, user_created, user_updated, datereleased, allowTag, dateposted from tblPAF_Allowance$hist where empNo=$empNo $and2 
						GROUP BY compCode, empNo, allowCode, allowAmt, allowAmtold, allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd, allowStat, sprtPS, 
                      refNo, controlNo, effectivitydate, dateadded, dateupdated, user_created, user_updated, datereleased, allowTag, dateposted,stat
					  order by effectivitydate";
			$arrAllow = $this->getAllowType($_SESSION['company_code']);
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				$allowType = "";
				foreach($arrAllow as $valAllow) {
					if ($valAllow['allowCode']==$val['allowCode'])
						$allowType = $valAllow['allowDesc'];
				}
				$arrPAF['type'][$i]='allow,'.$val['refNo'];
				$arrPAF['stat'][$i]=$val['stat'];
				$arrPAF['field'][$i]="$allowType";
				$arrPAF['value1'][$i]=$val['allowAmtold'];
				$arrPAF['value2'][$i]=$val['allowAmt'];
				$arrPAF['effdate'][$i]=$val['effectivitydate'];
				$arrPAF['dateupdated'][$i]=$val['dateupdated'];
				$arrPAF['refno'][$i]=$val['refNo'];
				$arrPAF['remarks'][$i]=$val['remarks'];
				$i++;
			}
			unset($res,$val,$qryPAF);
			}
		}		
		if (empty($pafType) || $pafType == "branch") {
			if (in_array($empNo,$this->arrBranch)){
				$qryPAF = "Select compCode, empNo, old_branchCode, old_payGrp, new_branchCode, new_payGrp, stat, effectivitydate, remarks, dateadded, dateupdated, user_created, 
                      user_updated, refNo, controlNo, datereleased, dateposted from tblPAF_Branch$hist where empNo=$empNo $and 
					  GROUP BY compCode, empNo, old_branchCode, old_payGrp, new_branchCode, new_payGrp, stat, effectivitydate, remarks, dateadded, dateupdated, 
                      user_created, user_updated, refNo, controlNo, datereleased, dateposted
					  order by effectivitydate";
				$res = $this->getArrRes($this->execQry($qryPAF));
				foreach($res as $val) {
					if (trim($val['new_branchCode']) !="") {
						$old_branch = $this->getEmpBranchArt($_SESSION['company_code'],$val['old_branchCode']);		
						$new_branch = $this->getEmpBranchArt($_SESSION['company_code'],$val['new_branchCode']);			
						$arrPAF['type'][$i]='branch,'.$val['refNo'];
						$arrPAF['stat'][$i]=$val['stat'];
						$arrPAF['field'][$i]="Branch";
						$arrPAF['value1'][$i]=$old_branch['brnDesc'];
						$arrPAF['value2'][$i]=$new_branch['brnDesc'];
						$arrPAF['effdate'][$i]=$val['effectivitydate'];
						$arrPAF['dateupdated'][$i]=$val['dateupdated'];
						$arrPAF['refno'][$i]=$val['refNo'];
						$arrPAF['remarks'][$i]=$val['remarks'];
						$i++;
					}			
					if (trim($val['new_payGrp']) !="") {
						$arrPAF['type'][$i]='branch,'.$val['refNo'];
						$arrPAF['stat'][$i]=$val['stat'];
						$arrPAF['field'][$i]="Pay Group";
						$arrPAF['value1'][$i]="Group " . $val['old_payGrp'];
						$arrPAF['value2'][$i]="Group " . $val['new_payGrp'];
						$arrPAF['effdate'][$i]=$val['effectivitydate'];
						$arrPAF['dateupdated'][$i]=$val['dateupdated'];
						$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
						$i++;
					}			
				}	
				unset($res,$val,$qryPAF);
			}	
		}
		if (empty($pafType) || $pafType == "position") {
			if (in_array($empNo,$this->arrPosition)){
			$qryPAF = "Select compCode, empNo, old_divCode, old_deptCode, old_secCode, old_cat, old_level, old_posCode, new_divCode, new_DeptCode, new_secCode, new_cat, 
                      new_level, new_posCode, stat, effectivitydate, remarks, dateadded, dateupdated, user_created, user_updated, refNo, controlNo, datereleased, 
                      dateposted from tblPAF_Position$hist where  empNo=$empNo $and 
			GROUP BY compCode, empNo, old_divCode, old_deptCode, old_secCode, old_cat, old_level, old_posCode, new_divCode, new_DeptCode, new_secCode, 
                      new_cat, new_level, new_posCode, stat, effectivitydate, remarks, dateadded, dateupdated, user_created, user_updated, refNo, controlNo, 
                      datereleased, dateposted
			order by effectivitydate";
			$compCode = $_SESSION['company_code'];
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				$pos_old = $this->getpositionwil("where compCode='$compCode' and posCode='{$val['old_posCode']}'",2);
				$pos_new = $this->getpositionwil("where compCode='$compCode' and posCode='{$val['new_posCode']}'",2);
				$division_new = $this->getDivDescArt($compCode, $pos_new['divCode']);
				$department_new = $this->getDeptDescArt($compCode, $pos_new['divCode'],$val['new_DeptCode']);
				$section_new =  $this->getSectDescArt($compCode, $pos_new['divCode'],$val['new_DeptCode'],$val['new_secCode']);
				$rank_new = $this->getRank($val['new_cat']);
				$level_new = "Level " . $val['new_level'];
				
				$division_old = $this->getDivDescArt($compCode, $pos_old['divCode']);
				$department_old = $this->getDeptDescArt($compCode, $pos_old['divCode'],$val['old_DeptCode']);
				$section_old =  $this->getSectDescArt($compCode, $pos_old['divCode'],$val['old_DeptCode'],$val['old_secCode']);
				$rank_old = $this->getRank($val['old_cat']);
				$level_old = "Level " . $val['old_level'];
				if (trim($val['new_posCode']) !="" && trim($val['new_posCode']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Position";
					$arrPAF['value1'][$i]=$pos_old['posDesc'];
					$arrPAF['value2'][$i]=$pos_new['posDesc'];;
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}				
				if (trim($val['new_divCode']) !="" && trim($val['new_divCode']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Division";
					$arrPAF['value1'][$i]=$division_old['deptDesc'];
					$arrPAF['value2'][$i]=$division_new['deptDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
				if (trim($val['new_DeptCode']) !="" && trim($val['new_DeptCode']) !=0) {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Department";
					$arrPAF['value1'][$i]=$department_old['deptDesc'];
					$arrPAF['value2'][$i]=$department_new['deptDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}	
				if (trim($val['new_secCode']) !="" && trim($val['new_secCode']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Section";
					$arrPAF['value1'][$i]=$section_old['deptDesc'];
					$arrPAF['value2'][$i]=$section_new['deptDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_cat']) !="" && trim($val['new_cat']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Rank ";
					$arrPAF['value1'][$i]=$rank_old['rankDesc'];
					$arrPAF['value2'][$i]=$rank_new['rankDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}									
				if (trim($val['new_level']) !="" && trim($val['new_level']) !="0") {
					$arrPAF['type'][$i]='position,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Level";
					$arrPAF['value1'][$i]=$level_old;
					$arrPAF['value2'][$i]=$level_new;
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}									
			}
			unset($res,$val,$qryPAF);
			}
		}
		if (empty($pafType) || $pafType == "payroll") {
			if (in_array($empNo,$this->arrPayroll)){
				$qryPAF = "Select  compCode, empNo, old_empTeu, old_empBankCd, old_empAcctNo, old_empMrate, old_empDrate, old_empHrate, old_empPayType, old_empPayGrp, 
                      old_category, new_empTeu, new_empBankCd, new_empAcctNo, new_empMrate, new_empDrate, new_empHrate, new_empPayType, new_empPayGrp, 
                      new_category, reasonCd, stat, effectivitydate, dateadded, remarks, dateupdated, user_created, user_updated, refNo, controlNo, datereleased, 
                      dateposted,
									oldPType= 
									CASE old_empPayType
									  WHEN 'D' THEN 'Daily'
									  WHEN 'M' THEN 'Monthly'
									END,
									newPType= 
									CASE new_empPayType
									  WHEN 'D' THEN 'Daily'
									  WHEN 'M' THEN 'Monthly'
									END
									 from tblPAF_PayrollRelated$hist where empNo=$empNo $and 
						GROUP BY compCode, empNo, old_empTeu, old_empBankCd, old_empAcctNo, old_empMrate, old_empDrate, old_empHrate, old_empPayType, old_empPayGrp, 
                      old_category, new_empTeu, new_empBankCd, new_empAcctNo, new_empMrate, new_empDrate, new_empHrate, new_empPayType, new_empPayGrp, 
                      new_category, reasonCd, stat, effectivitydate, dateadded, remarks, dateupdated, user_created, user_updated, refNo, controlNo, datereleased, 
                      dateposted, CASE old_empPayType  WHEN 'D' THEN 'Daily'   WHEN 'M' THEN 'Monthly' END,  CASE new_empPayType
									  WHEN 'D' THEN 'Daily'
									  WHEN 'M' THEN 'Monthly'
									END									 
									 order by effectivitydate";
			$res = $this->getArrRes($this->execQry($qryPAF));
			foreach($res as $val) {
				if (trim($val['new_empTeu']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="TEU";
					$arrPAF['value1'][$i]=$val['old_empTeu'];
					$arrPAF['value2'][$i]=$val['new_empTeu'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
				if (trim($val['new_empBankCd']) !="") {
					$bank_old =$this->getEmpBankArt($compCode,$val['new_empBankCd']);
					$bank_new =$this->getEmpBankArt($compCode,$val['new_empBankCd']);
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Bank";
					$arrPAF['value1'][$i]=$bank_old['bankDesc'];
					$arrPAF['value2'][$i]=$bank_new['bankDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}		
				if (trim($val['new_empAcctNo']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Account No.";
					$arrPAF['value1'][$i]=$val['old_empAcctNo'];
					$arrPAF['value2'][$i]=$val['new_empAcctNo'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}
				if (trim($val['new_empMrate']) !=0) {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Salary";
					if ($val['oldPType']=='Monthly') {
						$arrPAF['value1'][$i]=number_format($val['old_empMrate'],2) . " /Monthly Rate";
					} elseif ($val['oldPType']=='Daily') {
						$arrPAF['value1'][$i]=number_format($val['old_empDrate'],2) . " /Daily Rate";
					} else {
						$arrchEmp = $this->checkEmpInfo($val['empNo']);
						if ($arrchEmp['empPayType']=='M') 
							$arrPAF['value1'][$i]=number_format($val['old_empMrate'],2) . " /Monthly Rate";
						else
							$arrPAF['value1'][$i]=number_format($val['old_empDrate'],2) . " /Daily Rate";
					}
					if ($val['newPType']=='Monthly') {
						$arrPAF['value2'][$i]=number_format($val['new_empMrate'],2) . " /Monthly Rate";
					} elseif ($val['newPType']=='Daily') {
						$arrPAF['value2'][$i]=number_format($val['new_empDrate'],2) . " /Daily Rate";
					} else {
						$arrchEmp = $this->checkEmpInfo($val['empNo']);
						if ($arrchEmp['empPayType']=='M') 
							$arrPAF['value2'][$i]=number_format($val['new_empMrate'],2) . " /Monthly Rate";
						else
							$arrPAF['value2'][$i]=number_format($val['new_empDrate'],2) . " /Daily Rate";
					}
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}	
				if (trim($val['new_empPayType']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Rate Mode";
					$arrPAF['value1'][$i]=$val['oldPType'];
					$arrPAF['value2'][$i]=$val['newPType'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}									
				if (trim($val['new_payGrp']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrPAF['field'][$i]="Pay Group";
					$arrPAF['value1'][$i]="Group " . $val['old_payGrp'];
					$arrPAF['value2'][$i]="Group " . $val['new_payGrp'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
				if (trim($val['new_category']) !="") {
					$arrPAF['type'][$i]='payroll,'.$val['refNo'];
					$arrPAF['stat'][$i]=$val['stat'];
					$arrOldCat = $this->getPayCat($_SESSION['company_code'],"and payCat='".$val['old_category']."'");
					$arrNewCat = $this->getPayCat($_SESSION['company_code'],"and payCat='".$val['new_category']."'");
					$arrPAF['field'][$i]="Pay Category";
					$arrPAF['value1'][$i]=$arrOldCat['payCatDesc'];
					$arrPAF['value2'][$i]=$arrNewCat['payCatDesc'];
					$arrPAF['effdate'][$i]=$val['effectivitydate'];
					$arrPAF['dateupdated'][$i]=$val['dateupdated'];
					$arrPAF['refno'][$i]=$val['refNo'];
					$arrPAF['remarks'][$i]=$val['remarks'];
					$i++;
				}			
			}			
			}
		}				
		return $arrPAF;
	}	
	
	function getReasonCd($ReasonCd,$compCode) {
		$qryReason = "Select * from tblPayReason where compCode='$compCode' and reasonCd='$ReasonCd'";
		$res = $this->execQry($qryReason);
		return $this->getSqlAssoc($res);
	}
	function getSalaryData($type,$where){
	 	 $qrygetSalary = "SELECT tblPAF_PayrollRelated$type.*, tblPAF_PayrollRelated$type.new_empMrate - tblPAF_PayrollRelated$type.old_empMrate AS amtincrease,(((new_empMrate - old_empMrate) / old_empMrate) * 100) as percentincrease, tblPAF_Position$type.new_posCode, tblPAF_Position$type.new_divCode, tblPAF_Position$type.old_posCode, tblPAF_Position$type.old_divCode FROM tblPAF_PayrollRelated$type LEFT OUTER JOIN tblPAF_Position$type ON tblPAF_PayrollRelated$type.refNo = tblPAF_Position$type.refNo $where";
		$res = $this->execQry($qrygetSalary);
		return $this->getArrRes($res);
	}
	function getDivisionDesc(){
		$qryDiv = "Select divCode,deptDesc from tblDepartment where compCode='{$_SESSION['company_code']}' and deptLevel=1";
		$res = $this->execQry($qryDiv);
		return $this->getArrRes($res);
	}
	
	function getPositionDesc(){
		$qryPos = "Select posCode,posDesc from tblPosition where compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qryPos);
		return $this->getArrRes($res);
	}
	function getDesc($fielddesc,$fieldCd,$value,$array) {
		foreach($array as $val) {
			if ($val[$fieldCd] == $value) {
				$desc = $val[$fielddesc];
			}
		}
		return $desc;
	}
	function getReason() {
		$qryReason = "Select * from tblPayReason where compCode='{$_SESSION['company_code']}'";
		$res = $this->execQry($qryReason);
		return $this->getArrRes($res);
	}
	
	function restDay($brnCode="") {
		if ($brnCode !="" && $brnCode !="0") {
			$brnCodeFilter = " where empBrnCode = '$brnCode'";
		}
		$sqlRD = "Select empNo,empLastName,empFirstName,empMidName,empRestDay from tblEmpMast $brnCodeFilter";
		$res = $this->execQry($sqlRD);
		return $this->getArrRes($res);
	}
	function checkEmpInfo($empNo) {
		 $sql = "Select empPayType from tblEmpMast where empNo='$empNo' and compCode='{$_SESSION['company_code']}'";
		return $this->getSqlAssoc($this->execQry($sql));
		
	}

}

?>