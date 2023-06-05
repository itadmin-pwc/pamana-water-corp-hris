<?
class chartObj extends commonObj {

	function getDivisions() {
		$sqlDiv = "Select deptShortDesc,divCode from tblDepartment where deptStat='A' and compCode='{$_SESSION['company_code']}' and deptLevel =1 order by divCode";
		$resDiv = $this->execQry($sqlDiv);
		return $this->getArrRes($resDiv);
	}
	function GetValue($Array,$DivCode) {
		$ctr = 0;
		$tot = 0;
		foreach($Array as $val) {
			$tot += $val['ctr'];
			if ($val['empDiv'] == $DivCode) {
				$ctr = $val['ctr'];
			}	
		}
		//echo $ctr.'/'.$tot.'='.;
		return number_format(($ctr/$tot)*100,2);
	}	
	
	function GetValue2($Array,$DivCode) {
		$ctr = 0;
		$tot = 0;
		foreach($Array as $val) {
			$tot += $val['ctr'];
			if ($val['empDiv'] == $DivCode) {
				$ctr = $val['ctr'];
			}	
		}
		//echo $ctr.'/'.$tot.'='.;
		return $ctr;
	}	
	
}

?>