<?
class inqTSObj extends commonObj {

	var $compCode;
	var $arrDeductions;
	var $arrOvertimes;
	var $arrpayPd;
	
	
	
	function evenReport($empNo,$fr,$to,$brnCode,$bio,$grp,$div,$dept) {
		if ($fr != '' && $to != '')
			$date = " AND tblTK_EventLogs.EDATE BETWEEN '".date('Ymd',strtotime($fr))."' AND '".date('Ymd',strtotime($to))."'";
		if ($empNo != '')
			$emp = " AND tblTK_EmpShift.empNo='$empNo'";
		if ($brnCode != '0' && $brnCode != '')
			$branch = " AND csToreNum='$brnCode'";
		if ($bio != '')
			$bio = " AND tblTK_EmpShift.bioNo='$bio'";
		if ($grp != '0' && $grp != '')
			$grp1 = " AND tblEmpMast.empPayGrp='$grp'";	
		if ($div != '0' && $div != '')
			$div1 = " AND tblEmpMast.empDiv='$div'";		
		if ($dept != '0' && $dept != '')
			$dept1 = " AND tblEmpMast.empDepCode='$dept'";
		$sql = "SELECT tblTK_EventLogs.EDATE, tblTK_EventLogs.ETIME, tblTK_EmpShift.empNo
				FROM tblTK_EmpShift 
				RIGHT OUTER JOIN tblTK_EventLogs  ON Cast(tblTK_EmpShift.bioNo AS UNSIGNED) = Cast(tblTK_EventLogs.ETAG AS UNSIGNED)
				LEFT JOIN tblEmpMast on tblTK_EmpShift.empNo=tblEmpMast.empNo 
				WHERE tblTK_EmpShift.compCode='{$_SESSION['company_code']}' $date $emp $bio $grp1 $div1 $dept1 
				Group By tblTK_EventLogs.EDATE, tblTK_EventLogs.ETIME, tblTK_EmpShift.empNo
				Order by tblTK_EmpShift.empNo,tblTK_EventLogs.EDATE,tblTK_EventLogs.ETime ";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function evenReportGrp($empNo,$fr,$to,$brnCode,$bio,$grp,$div,$dept) {
		$brn = $this->getSqlAssoc($this->execQry("Select brnDefGrp from tblBranch where brnCode='{$brnCode}'"));
		$sqlpayPd = "Select *,DATEDIFF(pdFrmDate , pdToDate ) as NoDays from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payGrp='{$brn['brnDefGrp']}' AND payCat=3 AND pdTSStat='O'";
		$this->arrpayPd = $arrpayPd = $this->getSqlAssoc($this->execQry($sqlpayPd));
		//$empstat = " AND tblEmpMast.empStat in ('RG','RS')";
		$empstat = " and ((tblEmpMast.empStat='RG' 
						 	OR (((tblEmpMast.dateResigned between '".date('Y-m-d',strtotime($this->arrpayPd['pdFrmDate']))."' AND '".date('Y-m-d',strtotime($this->arrpayPd['pdToDate']))."') 
						 	OR (tblEmpMast.endDate between '".date('Y-m-d',strtotime($this->arrpayPd['pdFrmDate']))."' AND '".date('Y-m-d',strtotime($this->arrpayPd['pdToDate']))."')))))";
		if ($empNo != '')
			$emp = " AND tblTK_EmpShift.empNo='$empNo'";
		if ($brnCode != '0' && $brnCode != '')
			$branch = " AND empBrnCode='$brnCode'";
		if ($grp != '0' && $grp != '')
			$grp1 = " AND tblEmpMast.empPayGrp='$grp'";	
		if ($div != '0' && $div != '')
			$div1 = " AND tblEmpMast.empDiv='$div'";		
		if ($dept != '0' && $dept != '')
			$dept1 = " AND tblEmpMast.empDepCode='$dept'";			
		$sql = "SELECT tblTK_EmpShift.empNo,
					Concat(tblEmpMast.empLastName, ', ',tblEmpMast.empFirstName,' ',tblEmpMast.empMidName,'.') AS empName,  
					tblBranch.brnShortDesc AS brnDesc 
				FROM tblEmpMast 
				INNER JOIN tblBranch ON tblEmpMast.compCode = tblBranch.compCode 
					AND tblEmpMast.empBrnCode = tblBranch.brnCode 
				INNER JOIN 	tblTK_EmpShift ON tblEmpMast.empNo = tblTK_EmpShift.empNo
				Where tblEmpMast.compCode = '{$_SESSION['company_code']}' 
					$emp $branch $empstat  $grp1 $div1 $dept1 
				GROUP BY  tblTK_EmpShift.empNo
				ORDER BY tblEmpMast.empLastName, tblEmpMast.empFirstName,tblEmpMast.empMidName";

		return $this->getArrRes($this->execQry($sql));
	}
	

	function getDates($empNo,$fr,$to,$brnCode) {	
		if ($empNo != '')
			$emp = " and LPAD(ETAG, 9, '0')='$empNo'";
		if ($fr != '' && $to != '')
			$date = " AND EDATE BETWEEN '".date('Ymd',strtotime($fr))."' AND '".date('Ymd',strtotime($to))."'";
			
	
		//$sql = "Select Distinct EDATE from tblTK_EventLogs where cStoreNum='$brnCode' $date $emp order by EDATE";
		$sql = "Select Distinct EDATE from tblTK_EventLogs where 0=0 $date $emp order by EDATE";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function TSProofList($empNo,$brnCode,$hist,$from,$to,$grp){ 
		$grp = (trim($grp)!='' && $grp!='0') ? ",'".$grp."'":",''";
		$brnCode = (trim($brnCode)!='' && $brnCode!='0') ? $brnCode:"";
		$empNo = (trim($empNo)!='' && $empNo!='0') ? ",'".$empNo."'":",''";
		$sql = "CALL sp_TSProofList ('".$brnCode."','".$_SESSION['company_code']."','".$hist."','".$from."','".$to."','".$_GET['cat']."'".$grp."".$empNo.")";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function TSProofListReport($empNo,$brnCode,$hist,$from,$to,$grp,$div,$dept,$cat){ 
		//$grp = (trim($grp)!='' && $grp!='0') ? ",$grp":"";
		//$brnCode = (trim($brnCode)!='' && $brnCode!='0') ? $brnCode:"";
		//$empNo = (trim($empNo)!='' && $empNo!='0') ? ",$empNo":"";
		//$div = (trim($div)!='' && $div!='0') ? ",$div":"";
		//$dept = (trim($dept)!='' && $dept!='0') ? ",$dept":"";
		//echo  "Exec sp_TSProofListReport '$brnCode','{$_SESSION['company_code']}','$hist','$from','$to','$grp','$div','$dept','$cat'";
		$sql = "CALL sp_TSProofListReport ('".$brnCode."','".$_SESSION['company_code']."','".$hist."','".$from."','".$to."','".$grp."','".$div."','".$dept."','".$cat."')";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function getOTs() {
		$sql = "SELECT * FROM tblTK_Overtime Where compCode='{$_SESSION['company_code']}' AND empNo IN  (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empbrnCode IN (Select brnCode from tblTK_UserBranch Where compCode='{$_SESSION['company_code']}' AND empNo='{$_SESSION['employee_number']}'))";
		$this->arrOvertimes = $this->getArrRes($this->execQry($sql));
	}
	
	function getDeductions() {
		$sql = "SELECT * FROM tblTK_Deductions Where compCode='{$_SESSION['company_code']}' AND empNo IN  (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empbrnCode IN (Select brnCode from tblTK_UserBranch Where compCode='{$_SESSION['company_code']}' AND empNo='{$_SESSION['employee_number']}'))";
		$this->arrDeductions =  $this->getArrRes($this->execQry($sql));
	}	
	function getempOTsDeds($empNo,$tsDate,$cat) {
		//$res = array();
		switch($cat) {
			case "OT":
				foreach($this->arrOvertimes as $val) {
					if ($empNo==$val['empNo'] && $tsDate==$val['tsDate']) {
						$res = $val;
					}
				}
			break;
			case "Ded":
				foreach($this->arrDeductions as $val) {
					if ($empNo==$val['empNo'] && $tsDate==$val['tsDate']) {
						$res = $val;
					}
				}
			break;
		}
		$res;
		return $res;
	}
	
	function OB($brnCode="",$hist,$from,$to,$grps){
		$grp = ($brnCode =='0001') ? " AND tblEmpMast.empPayGrp = '$grps'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND tblEmpMast.empbrnCode = '$brnCode'":"";
		//$sql = "SELECT     empBrnCode as brnCode, tblTK_OBApp$hist.empNo, tblEmpMast.empLastName + ', ' + tblEmpMast.empFirstName + ' ' + SUBSTRING(tblEmpMast.empMidName, 1, 1) + '.' AS empName, tblTK_OBApp$hist.refNo, tblTK_OBApp$hist.obDate, tblTK_OBApp$hist.dateFiled, tblTK_OBApp$hist.obSchedIn, tblTK_OBApp$hist.obSchedOut, tblTK_OBApp$hist.obActualTimeIn, tblTK_OBApp$hist.obActualTimeOut, tblTK_OBApp$hist.obDestination, tblTK_OBApp$hist.obReason, tblTK_OBApp$hist.dateApproved, tblTK_OBApp$hist.obStat FROM tblTK_OBApp$hist INNER JOIN tblEmpMast ON tblTK_OBApp$hist.compCode = tblEmpMast.compCode AND tblTK_OBApp$hist.empNo = tblEmpMast.empNo where tblTK_OBApp$hist.compCode='{$_SESSION['company_code']}'  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) $grp AND obDate between '$from' and '$to' order by empLastName,empFirstName,obDate";
		$sql = "SELECT tblEmpMast.empBrnCode as brnCode, tblTK_OBApp$hist.empNo, 
				Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1, 1),'.') AS empName, 
				tblTK_OBApp$hist.refNo, tblTK_OBApp$hist.obDate, tblTK_OBApp$hist.dateFiled, tblTK_OBApp$hist.obSchedIn, 
				tblTK_OBApp$hist.obSchedOut, tblTK_OBApp$hist.obActualTimeIn, tblTK_OBApp$hist.obActualTimeOut, 
				tblTK_OBApp$hist.obDestination, tblTK_OBApp$hist.obReason, tblTK_OBApp$hist.dateApproved, 
				tblTK_OBApp$hist.obStat, tblTK_OBApp$hist.hrs8Deduct, 
				Concat(empUser.empLastName,', ',empUser.empFirstName,' ',SUBSTRING(empUser.empMidName,1,1),'.') as userName 
				FROM tblTK_OBApp$hist 
				INNER JOIN tblEmpMast ON tblTK_OBApp$hist.compCode = tblEmpMast.compCode 
					AND tblTK_OBApp$hist.empNo = tblEmpMast.empNo 
				INNER JOIN tblEmpMast empUser On tblTK_OBApp$hist.addedBy = empUser.empNo 
				where tblTK_OBApp$hist.compCode='{$_SESSION['company_code']}' 
					AND tblEmpMast.empBrnCode IN (SELECT tblTK_UserBranch.brnCode 
						from tblTK_UserBranch where tblTK_UserBranch.compCode='{$_SESSION['company_code']}' 
							and tblTK_UserBranch.empNo='{$_SESSION['employee_number']}' $brnCode) $grp 
				AND tblTK_OBApp$hist.obDate between '$from' and '$to' order by tblEmpMast.empLastName,tblEmpMast.empFirstName,obDate";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function OverBreaks($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "Select compcode, empNo, tsdate, shftLunchOut, shftLunchIn, lunchOut, 
						lunchIn, empname, brnchCd, n, empPayGrp, empbrnCode, empLastname, empFirstName
				from view_overBreak$hist 
				where compcode='{$_SESSION['company_code']}' and brnchCd In (Select tkBranch.brnCode 
					from tblTK_UserBranch tkBranch where tkBranch.compCode='{$_SESSION['company_code']}' 
						and tkBranch.empNo='{$_SESSION['employee_number']}') $brnCode $grp
					and tsDate between '$from' and '$to' 
					and n>'60'
				order by empLastname, empFirstName,tsDate";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function OverBreaks1($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empMast.empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empMast.empbrnCode = '$brnCode'":"";
		$sql = "Select timeSheet.compcode, timeSheet.empNo,CONVERT(NVARCHAR(12),timeSheet.tsDate,107) AS tsdate, 
				timeSheet.shftBreakOut, timeSheet.shftBreakIn,
				timeSheet.breakOut, timeSheet.breakIn,
				empMast.empLastName+', '+empMast.empFirstName+' '+SUBSTRING(empMast.empMidName,1,1)+'.' as empname,
				timeSheet.brnchCd, dateDiff(mi,CAST(timeSheet.breakOut as datetime(8)),CAST(timeSheet.breakIn as datetime(8))) as n1
				from tblTK_Timesheet$hist timeSheet 
				inner join tblEmpMast empMast on timeSheet.empNo=empMast.empNo
				where timeSheet.compcode='{$_SESSION['company_code']}' and timeSheet.brnchCd In (Select tkBranch.brnCode 
				from tblTK_UserBranch tkBranch where tkBranch.compCode='{$_SESSION['company_code']}' 
				and tkBranch.empNo='{$_SESSION['employee_number']}') $brnCode $grp
				and timeSheet.tsDate between '$from' and '$to' 
				and dateDiff(mi,CAST(timeSheet.breakOut as datetime(8)),CAST(timeSheet.breakIn as datetime(8)))>'15'
				order by empMast.empLastname, empMast.empFirstName,timeSheet.tsDate";
		return $this->getArrRes($this->execQry($sql));
	}

	
	function UserBranch($brnCode) {
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND brnCode = '$brnCode'":"";
		$sql = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' AND brnCode IN (Select brnCode from tblTK_UserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) order by brnDesc";
		return $this->getArrRes($this->execQry($sql));
	}
	function OT($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tblEmpMast.empBrnCode AS brnCode, Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ', SUBSTRING(tblEmpMast.empMidName, 1,1),'.') AS empName, tblTK_OTApp$hist.empNo, tblTK_OTApp$hist.otDate, tblTK_OTApp$hist.refNo, tblTK_OTApp$hist.dateFiled, tblTK_OTApp$hist.otReason, tblTK_OTApp$hist.otIn, tblTK_OTApp$hist.otOut, tblTK_OTApp$hist.otStat,  crossTag = CASE tblTK_OTApp$hist.crossTag WHEN 'Y' THEN 'Yes' ELSE 'No' END FROM tblEmpMast INNER JOIN tblTK_OTApp$hist ON tblEmpMast.compCode = tblTK_OTApp$hist.compCode AND tblEmpMast.empNo = tblTK_OTApp$hist.empNo where tblTK_OTApp$hist.compCode='{$_SESSION['company_code']}'  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) $grp AND otDate between '$from' and '$to' order by empLastName,empFirstName,otDate";
		return $this->getArrRes($this->execQry($sql));
	}	

	function TS_Adjustment_with_Amount($from,$to,$grp,$id){
		//$grp = " AND tblEmpMast.empPayGrp = '$grp'";
		$grp = " AND tblEmpMast.empPayGrp = '".$grp."'";
		if($id=="O"){
			$hist = "";	
			$stat = " AND tblTK_TimesheetAdjustment$hist.tsStat='O'";
		}
		elseif($id=="A"){
			$hist = "";
			$stat = " AND tblTK_TimesheetAdjustment$hist.tsStat='A'";	
		}
		elseif($id=="P"){
			$hist = "hist";	
			$stat = " AND tblTK_TimesheetAdjustment$hist.tsStat='A'";	
		}
		//$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tblEmpMast.empLastName, 
			Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1,1),'.') AS empName, 
			tblTK_TimesheetAdjustment$hist.empNo, tblTK_TimesheetAdjustment$hist.tsDate, tblTK_TimesheetAdjustment$hist.dayType,
			tblTK_TimesheetAdjustment$hist.payGrp, tblTK_TimesheetAdjustment$hist.payCat, tblTK_TimesheetAdjustment$hist.pdYear,
			tblTK_TimesheetAdjustment$hist.pdNumber, tblTK_TimesheetAdjustment$hist.entryTag, 
			CASE tblTK_TimesheetAdjustment$hist.includeAllowTag WHEN 'Y' THEN 'Yes' ELSE 'No' END as includeAllowTag, 
			CASE tblTK_TimesheetAdjustment$hist.includeAdvTag WHEN 'Y' THEN 'Yes' ELSE 'No' END as includeAdvTag,
			tblTK_TimesheetAdjustment$hist.hrsReg, tblTK_TimesheetAdjustment$hist.hrsOtLe8, 
			tblTK_TimesheetAdjustment$hist.hrsOtGt8, tblTK_TimesheetAdjustment$hist.hrsNd, 
			tblTK_TimesheetAdjustment$hist.hrsNdGt8, tblTK_TimesheetAdjustment$hist.adjBasic,
			tblTK_TimesheetAdjustment$hist.adjOt, tblTK_TimesheetAdjustment$hist.adjNd, tblTK_TimesheetAdjustment$hist.adjHp,
			tblTK_TimesheetAdjustment$hist.adjEcola, tblTK_TimesheetAdjustment$hist.adjCtpa, tblTK_TimesheetAdjustment$hist.adjAdv		
		FROM tblEmpMast 
		INNER JOIN tblTK_TimesheetAdjustment$hist ON tblEmpMast.compCode = tblTK_TimesheetAdjustment$hist.compCode 
			AND tblEmpMast.empNo = tblTK_TimesheetAdjustment$hist.empNo 
		WHERE tblTK_TimesheetAdjustment$hist.compCode='{$_SESSION['company_code']}'  
			AND tblEmpMast.empBrnCode IN (Select brnCode from tblTK_UserBranch 
				where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}') 
			$grp $stat AND tblTK_TimesheetAdjustment$hist.dateAdded between '$from' and '$to'";
//			Union All
//			SELECT tblEmpMast.empLastName, tblEmpMast.empLastName + ', ' + tblEmpMast.empFirstName + ' ' + SUBSTRING(tblEmpMast.empMidName, 1,1) + '.' AS empName, tblTK_TimesheetAdjustmenthist.empNo, tblTK_TimesheetAdjustmenthist.tsDate, tblTK_TimesheetAdjustmenthist.dayType, tblTK_TimesheetAdjustmenthist.payGrp, tblTK_TimesheetAdjustmenthist.payCat, tblTK_TimesheetAdjustmenthist.pdYear, tblTK_TimesheetAdjustmenthist.pdNumber, tblTK_TimesheetAdjustmenthist.entryTag, includeAllowTag = CASE tblTK_TimesheetAdjustmenthist.includeAllowTag WHEN 'Y' THEN 'Yes' ELSE 'No' END, includeAdvTag = CASE tblTK_TimesheetAdjustmenthist.includeAdvTag WHEN 'Y' THEN 'Yes' ELSE 'No' END, tblTK_TimesheetAdjustmenthist.hrsReg, tblTK_TimesheetAdjustmenthist.hrsOtLe8, tblTK_TimesheetAdjustmenthist.hrsOtGt8, tblTK_TimesheetAdjustmenthist.hrsNd, tblTK_TimesheetAdjustmenthist.hrsNdGt8,  tblTK_TimesheetAdjustmenthist.adjBasic, tblTK_TimesheetAdjustmenthist.adjOt, tblTK_TimesheetAdjustmenthist.adjNd, tblTK_TimesheetAdjustmenthist.adjHp, tblTK_TimesheetAdjustmenthist.adjEcola, tblTK_TimesheetAdjustmenthist.adjCtpa, tblTK_TimesheetAdjustmenthist.adjAdv		   
//		FROM tblEmpMast 
//		INNER JOIN tblTK_TimesheetAdjustmenthist ON tblEmpMast.compCode = tblTK_TimesheetAdjustmenthist.compCode 
//			AND tblEmpMast.empNo = tblTK_TimesheetAdjustmenthist.empNo 
//		WHERE tblTK_TimesheetAdjustmenthist.compCode='{$_SESSION['company_code']}'  
//			AND tblEmpMast.empBrnCode IN (Select brnCode from tblTK_UserBranch 
//				where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}') 
//			$grp AND tblTK_TimesheetAdjustmenthist.dateAdded between '$from' and '$to'
//		ORDER BY empLastName, tsDate";
		//ORDER BY tblEmpMast.empLastName,tblEmpMast.empFirstName,tblTK_TimesheetAdjustment$hist.dateAdded";
		return $this->getArrRes($this->execQry($sql));
	}	

	function TS_Adjustment($from,$to,$grp,$id){
		$grp = " AND tblEmpMast.empPayGrp = '".$grp."'";
		if($id=="O"){
			$hist = "";	
			$stat = " AND tblTK_TimesheetAdjustment$hist.tsStat='O'";
		}
		elseif($id=="A"){
			$hist = "";
			$stat = " AND tblTK_TimesheetAdjustment$hist.tsStat='A'";	
		}
		elseif($id=="P"){
			$hist = "hist";	
			$stat = " AND tblTK_TimesheetAdjustment$hist.tsStat='A'";	
		}
		
		//$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tblEmpMast.empLastName, 
			Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1,1),'.') AS empName,
			tblTK_TimesheetAdjustment$hist.empNo, tblTK_TimesheetAdjustment$hist.tsDate, tblTK_TimesheetAdjustment$hist.dayType,
			tblTK_TimesheetAdjustment$hist.payGrp, tblTK_TimesheetAdjustment$hist.payCat, tblTK_TimesheetAdjustment$hist.pdYear,
			tblTK_TimesheetAdjustment$hist.pdNumber, tblTK_TimesheetAdjustment$hist.entryTag, 
			CASE tblTK_TimesheetAdjustment$hist.includeAllowTag WHEN 'Y' THEN 'Yes' ELSE 'No' END as includeAllowTag, 
			CASE tblTK_TimesheetAdjustment$hist.includeAdvTag WHEN 'Y' THEN 'Yes' ELSE 'No' END as includeAdvTag,
			tblTK_TimesheetAdjustment$hist.hrsReg, tblTK_TimesheetAdjustment$hist.hrsOtLe8, tblTK_TimesheetAdjustment$hist.hrsOtGt8,
			tblTK_TimesheetAdjustment$hist.hrsNd, tblTK_TimesheetAdjustment$hist.hrsNdGt8, tblTK_TimesheetAdjustment$hist.adjBasic,
			tblTK_TimesheetAdjustment$hist.adjOt, tblTK_TimesheetAdjustment$hist.adjNd, tblTK_TimesheetAdjustment$hist.adjHp,
			tblTK_TimesheetAdjustment$hist.adjEcola, tblTK_TimesheetAdjustment$hist.adjCtpa, tblTK_TimesheetAdjustment$hist.adjAdv		
		FROM tblEmpMast 
		INNER JOIN tblTK_TimesheetAdjustment$hist ON tblEmpMast.compCode = tblTK_TimesheetAdjustment$hist.compCode 
			AND tblEmpMast.empNo = tblTK_TimesheetAdjustment$hist.empNo 
		WHERE tblTK_TimesheetAdjustment$hist.compCode='{$_SESSION['company_code']}'  
			AND tblEmpMast.empBrnCode IN (Select brnCode from tblTK_UserBranch 
				where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}') 
			AND (tblTK_TimesheetAdjustment$hist.hrsReg<>'' or tblTK_TimesheetAdjustment$hist.hrsOtLe8<>'' 
				or tblTK_TimesheetAdjustment$hist.hrsOtGt8<>'' or tblTK_TimesheetAdjustment$hist.hrsNd<>''
				or tblTK_TimesheetAdjustment$hist.hrsNdGt8<>'')	
			$grp $stat AND tblTK_TimesheetAdjustment$hist.dateAdded between '".$from."' and '".$to."'
			ORDER BY empLastName, tsDate";
		//ORDER BY tblEmpMast.empLastName,tblEmpMast.empFirstName,tblTK_TimesheetAdjustment$hist.dateAdded";
		return $this->getArrRes($this->execQry($sql));
	}	


	function Earnings_Adjustment($from,$to,$grp){
		$grp = " AND tblEmpMast.empPayGrp = '$grp'";
		//$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tblEarnTranHeader.compCode, tblEarnTranHeader.refNo, tblEarnTranHeader.trnCode, tblEarnTranHeader.earnRem,
					tblEarnTranHeader.earnStat, tblEarnTranHeader.pdYear, tblEarnTranHeader.pdNumber, tblEarnTranHeader.dateAdded,
					tblEarnTranHeader.userAdded, tblEarnTranDtl.empNo, tblEarnTranDtl.trnCntrlNo, tblEarnTranDtl.trnCode,
					tblEarnTranDtl.trnAmount, tblEarnTranDtl.payGrp, tblEarnTranDtl.earnStat,
					tblEarnTranDtl.trnTaxCd, tblEarnTranDtl.processTag, tblEarnTranDtl.trnCntrlNo,
					Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1,1),'.') AS empName, case tblEarnTranDtl.payCat WHEN '1' then 'Executive' WHEN '1' THEN 'Confidential' WHEN '3' THEN 'Non Confi.' WHEN '9' THEN 'Resigned' end  as payCat
				FROM tblEarnTranHeader 
				INNER JOIN tblEarnTranDtl ON tblEarnTranHeader.refNo = tblEarnTranDtl.refNo 
					AND tblEarnTranHeader.compCode = tblEarnTranDtl.compCode 
					AND tblEarnTranHeader.trnCode = tblEarnTranDtl.trnCode 
				INNER JOIN tblEmpMast ON tblEarnTranDtl.compCode = tblEmpMast.compCode 
					AND tblEarnTranDtl.empNo = tblEmpMast.empNo
				WHERE tblEarnTranHeader.dateAdded between '$from' AND '$to' $grp 
					AND tblEarnTranHeader.userAdded='{$_SESSION['employee_number']}' $grp
				Union All	
				SELECT tblEarnTranHeader.compCode, tblEarnTranHeader.refNo, tblEarnTranHeader.trnCode, tblEarnTranHeader.earnRem,
					tblEarnTranHeader.earnStat, tblEarnTranHeader.pdYear, tblEarnTranHeader.pdNumber, tblEarnTranHeader.dateAdded,
					tblEarnTranHeader.userAdded, tblEarnTranDtlhist.empNo, tblEarnTranDtlhist.trnCntrlNo, tblEarnTranDtlhist.trnCode,
					tblEarnTranDtlhist.trnAmount, tblEarnTranDtlhist.payGrp, tblEarnTranDtlhist.earnStat,
					tblEarnTranDtlhist.trnTaxCd, '', tblEarnTranDtlhist.trnCntrlNo,
					Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1,1),'.') AS empName, case tblEarnTranDtlhist.payCat WHEN '1' then 'Executive' WHEN '1' THEN 'Confidential' WHEN '3' THEN 'Non Confi.' WHEN '9' THEN 'Resigned' end  as payCat
				FROM tblEarnTranHeader 
				INNER JOIN tblEarnTranDtlhist ON tblEarnTranHeader.refNo = tblEarnTranDtlhist.refNo 
					AND tblEarnTranHeader.compCode = tblEarnTranDtlhist.compCode 
					AND tblEarnTranHeader.trnCode = tblEarnTranDtlhist.trnCode 
				INNER JOIN tblEmpMast ON tblEarnTranDtlhist.compCode = tblEmpMast.compCode 
					AND tblEarnTranDtlhist.empNo = tblEmpMast.empNo
				WHERE tblEarnTranHeader.dateAdded between '$from' AND '$to' $grp 
					AND tblEarnTranHeader.userAdded='{$_SESSION['employee_number']}' $grp	
					";
				//ORDER BY tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEarnTranDtl.trnCntrlNo";
		return $this->getArrRes($this->execQry($sql));
	}	


	function CS($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT     tblEmpMast.empBrnCode AS brnCode, Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1,1),'.') AS empName, tblTK_CSApp$hist.empNo, tblTK_CSApp$hist.refNo, tblTK_CSApp$hist.dateFiled, tblTK_CSApp$hist.csDateTo, tblTK_CSApp$hist.csDateFrom, tblTK_CSApp$hist.csShiftFromIn, tblTK_CSApp$hist.csShiftFromOut, tblTK_CSApp$hist.csShiftToIn, tblTK_CSApp$hist.csHiftToOut, tblTK_CSApp$hist.csReason, tblTK_CSApp$hist.crossDay, tblTK_CSApp$hist.csStat FROM tblEmpMast INNER JOIN tblTK_CSApp$hist ON tblEmpMast.compCode = tblTK_CSApp$hist.compcode AND tblEmpMast.empNo = tblTK_CSApp$hist.empNo where tblTK_CSApp$hist.compCode='{$_SESSION['company_code']}'  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) $grp AND csDateFrom between '$from' and '$to' order by empLastName,empFirstName,csDateFrom ";
		return $this->getArrRes($this->execQry($sql));
	}
	function TS_Corrections($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tsOrig.empNo, tsOrig.tsDate, tsOrig.timeIn, tsOrig.lunchOut, tsOrig.lunchIn, tsOrig.breakIn, 
					tsOrig.breakOut, tsOrig.timeOut, tsOrig.otIn, tsOrig.otOut, tsOrig.crossTag,
					tsOrig.logsExceeded, tsCor.timeIn AS cor_timeIn, tsCor.lunchOut AS cor_lunchOut, 
					tsCor.lunchIn AS cor_lunchIn, tsCor.breakIn AS cor_breakIn, tsCor.breakOut AS cor_breakOut, 
					tsCor.timeOut AS cor_timeOut, tsCor.otIn AS cor_otIn, tsCor.otOut AS cor_otOut, 
					tsCor.crossTag AS cor_crossTag, emp.empBrnCode AS brnCode, 
					Concat(emp.empLastName,', ',emp.empFirstName,' ',SUBSTRING(emp.empMidName, 1, 1),'.') AS empName, 
					tsOrig.editReason
				FROM tblTK_TimeSheetCorr_original tsOrig INNER JOIN
                      tblTK_TimeSheetCorr$hist tsCor ON tsOrig.compCode = tsCor.compCode 
					  		AND tsOrig.empNo = tsCor.empNo AND tsOrig.tsDate = tsCor.tsDate INNER JOIN
                      tblEmpMast emp ON tsOrig.compCode = emp.compCode AND tsOrig.empNo = emp.empNo
				WHERE   tsOrig.compCode='{$_SESSION['company_code']}' AND   
						tsOrig.tsDate BETWEEN '$from' AND '$to' AND 
						empBrnCode IN (Select brnCode from tblTK_UserBranch 
										WHERE compCode='{$_SESSION['company_code']}' 
											and empNo='{$_SESSION['employee_number']}' $brnCode) 
				ORDER BY empLastName,empFirstName,tsOrig.tsDate
 				";
		return $this->getArrRes($this->execQry($sql));
	}		
	function OT_Prooflist($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tblEmpMast.empBrnCode AS brnCode, Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1,1),'.') AS empName, tblTK_Overtime$hist.empNo, tblDayType.dayTypeDesc, tblTK_Overtime$hist.hrsOTLe8, tblTK_Overtime$hist.hrsOTGt8, tblTK_Overtime$hist.hrsNDLe8, tblTK_Overtime$hist.tsDate
FROM tblEmpMast INNER JOIN tblTK_Overtime$hist ON tblEmpMast.compCode = tblTK_Overtime$hist.compCode AND tblEmpMast.empNo = tblTK_Overtime$hist.empNo INNER JOIN tblDayType ON tblTK_Overtime$hist.dayType = tblDayType.dayType where tblTK_Overtime$hist.compCode='{$_SESSION['company_code']}'  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) $grp AND tsDate between '$from' and '$to' order by empLastName,empFirstName,tsDate";
		return $this->getArrRes($this->execQry($sql));
	}	
	function Deductions($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT     tblEmpMast.empBrnCode AS brnCode, Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1,1),'.') AS empName, tblTK_Deductions$hist.empNo, tblTK_Deductions$hist.tsDate, tblTK_Deductions$hist.hrsTardy, tblTK_Deductions$hist.hrsUT FROM tblEmpMast INNER JOIN tblTK_Deductions$hist ON tblEmpMast.compCode = tblTK_Deductions$hist.compCode AND tblEmpMast.empNo = tblTK_Deductions$hist.empNo where tblTK_Deductions$hist.compCode='{$_SESSION['company_code']}'  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) $grp  AND tsDate between '$from' and '$to' order by empLastName,empFirstName,tsDate";
		return $this->getArrRes($this->execQry($sql));
	}	
	
	function Leaves($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tblEmpMast.empBrnCode AS brnCode, Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1, 1),'.') AS empName, tblTK_LeaveApp$hist.empNo, tblTK_LeaveApp$hist.refNo, tblTK_LeaveApp$hist.dateFiled, tblTK_LeaveApp$hist.lvDateFrom,tblTK_LeaveApp$hist.lvDateTo, tblTK_AppTypes.appTypeShortDesc, tblTK_LeaveApp$hist.lvReason,tblTK_LeaveApp$hist.lvReliever FROM tblEmpMast INNER JOIN tblTK_LeaveApp$hist ON tblEmpMast.compCode = tblTK_LeaveApp$hist.compcode AND tblEmpMast.empNo = tblTK_LeaveApp$hist.empNo INNER JOIN tblTK_AppTypes ON tblTK_LeaveApp$hist.tsAppTypeCd = tblTK_AppTypes.tsAppTypeCd AND tblTK_LeaveApp$hist.compcode = tblTK_AppTypes.compCode where tblTK_LeaveApp$hist.compCode='{$_SESSION['company_code']}'  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) $grp AND lvDateFrom between '$from' and '$to' order by empLastName,empFirstName,lvDateFrom\n";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function legalPay($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tblEmpMast.empBrnCode AS brnCode, 
					Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1, 1),'.') AS empName, 
					tblTK_Timesheet$hist.empNo, tblTK_Timesheet$hist.tsDate 
				FROM tblEmpMast 
				INNER JOIN tblTK_Timesheet$hist ON tblEmpMast.compCode = tblTK_Timesheet$hist.compcode 
					AND tblEmpMast.empNo = tblTK_Timesheet$hist.empNo
				WHERE tblTK_Timesheet$hist.compCode='{$_SESSION['company_code']}'  
					AND empBrnCode IN (Select brnCode from tblTK_UserBranch 
						where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) 
							$grp AND tblTK_Timesheet$hist.legalPayTag='Y' AND tblTK_Timesheet$hist.tsDate between '$from' and '$to'
				ORDER BY empLastName,empFirstName,tblTK_Timesheet$hist.tsDate";
		return $this->getArrRes($this->execQry($sql));
	}

	function offSetHour($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tblEmpMast.empBrnCode AS brnCode, 
					Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1, 1),'.') AS empName, 
					tblTK_Timesheet$hist.empNo, tblTK_Timesheet$hist.tsDate, tblTK_Timesheet$hist.hrsRequired 
				FROM tblEmpMast 
				INNER JOIN tblTK_Timesheet$hist ON tblEmpMast.compCode = tblTK_Timesheet$hist.compcode 
					AND tblEmpMast.empNo = tblTK_Timesheet$hist.empNo
				INNER JOIN tblTK_EmpShift ON tblTK_Timesheethist.empNo=tblTK_EmpShift.empNo	
				WHERE tblTK_Timesheet$hist.compCode='{$_SESSION['company_code']}'  
					AND empBrnCode IN (Select brnCode from tblTK_UserBranch 
						where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) 
							$grp AND tblTK_Timesheet$hist.tsDate between '$from' and '$to'
							AND tblTK_EmpShift.CWWTag='Y'	
							AND cast(tblTK_Timesheethist.hrsRequired as unsigned)>9
				ORDER BY empLastName,empFirstName,tblTK_Timesheet$hist.tsDate\n";
		return $this->getArrRes($this->execQry($sql));
	}
	
	
	function RestDay($brnCode="",$hist,$from,$to,$grp){
		$grp = ($brnCode =='0001') ? " AND empPayGrp = '$grp'":"";
		$brnCode = ($brnCode !='0' && $brnCode !='') ? " AND empbrnCode = '$brnCode'":"";
		$sql = "SELECT tblEmpMast.empBrnCode AS brnCode, Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName, 1,1),'.') AS empName, tblTK_ChangeRDApp$hist.empNo, tblTK_ChangeRDApp$hist.refNo, tblTK_ChangeRDApp$hist.cRDDateFrom, tblTK_ChangeRDApp$hist.dateFiled, tblTK_ChangeRDApp$hist.tsAppTypeCd, tblTK_ChangeRDApp$hist.cRDDateTo, tblTK_ChangeRDApp$hist.cRDReason FROM tblEmpMast INNER JOIN tblTK_ChangeRDApp$hist ON tblEmpMast.compCode = tblTK_ChangeRDApp$hist.compCode AND tblEmpMast.empNo = tblTK_ChangeRDApp$hist.empNo where tblTK_ChangeRDApp$hist.compCode='{$_SESSION['company_code']}'  AND empBrnCode IN (Select brnCode from tblTK_UserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}' $brnCode) $grp AND (cRDDateFrom between '$from' and '$to' OR cRDDateTo between '$from' and '$to') order by empLastName,empFirstName,cRDDateFrom  \n";
		return $this->getArrRes($this->execQry($sql));
	}
	
	function getpayPd($brnCode,$grp) {
		if ($brnCode=='0001')
			$sql = "Select date_format(pdPayable, '%Y-%m-%d') as pdPayable,pdSeries from tblpayperiod where payCat=3 and payGrp='$grp' and pdseries <= (select pdseries from tblPayPeriod where pdstat='O' and  payCat=3 and payGrp='".$grp."' ) order by pdSeries desc";
		else
			$sql = "Select date_format(pdPayable, '%Y-%m-%d') as pdPayable,pdSeries from tblpayperiod where payCat=3 and payGrp=(Select brnDefGrp from tblBranch where brnCode='$brnCode') and pdseries <= (select pdseries from tblPayPeriod where pdstat='O' and  payCat=3 and payGrp=(Select brnDefGrp from tblBranch where brnCode='$brnCode')) order by pdSeries desc";
			return $this->getArrRes($this->execQry($sql));
	}
	
	function getOpenPayPd($grp){
		$sql = "Select date_format(pdPayable, '%Y-%m-%d') as pdPayable,pdSeries from tblpayperiod where payCat=3 and payGrp='$grp' and pdseries <= (select pdseries from tblPayPeriod where pdstat='O' and  payCat=3 and payGrp='$grp') order by pdSeries desc";
			return $this->getArrRes($this->execQry($sql));	
	}
	
	function getGrpOpenPeriod($pdSeries) {
		$sql = "Select pdTSStat as pdStat,date_format(pdFrmDate, '%Y-%m-%d') as pdFrmDate,date_format(pdToDate, '%Y-%m-%d') as pdToDate from tblPayperiod where pdSeries='$pdSeries'";
		return $this->getSqlAssoc($this->execQry($sql));
	}
	
	function violationsReport($empNo,$fr,$to,$brnCode,$bio, $violations) {
		if ($fr != '' && $to != '')
			$date = " AND tblTK_EmpViolations.tsDate BETWEEN '".date('Y-m-d',strtotime($fr))."' AND '".date('Y-m-d',strtotime($to))."'";
		if($fr != '' && $to == '')
			$date = " AND tblTK_EmpViolations.tsDate='".date('Y-m-d',strtotime($fr))."'";
		if ($empNo != '')
			$emp = " AND tblTK_EmpViolations.empNo='$empNo'";
		if ($brnCode != '0' && $brnCode != '')
			$branch = " AND tblEmpMast.empBrnCode='$brnCode'";
		if ($bio != '')
			$bio = " AND tblBioEmp.bioNumber='$bio'";
		if ($violations!='0')
			$violation = " AND tblTK_EmpViolations.violationCd='$violations'";	
		
		$sql = "Select DISTINCT tblTK_EmpViolations.compCode, tblTK_EmpViolations.empNo, tblTK_EmpViolations.violationCd, 
					tblTK_EmpViolations.tsDate, tblEmpMast.empBrnCode, tblBioEmp.bioNumber, 
					tblEmpMast.empLastName,tblEmpMast.empFirstName,tblEmpMast.empMidName, tblTK_ViolationType.violationDesc,
					tblDepartment.deptDesc, date_format(tblTK_EmpViolations.tsDate,'%Y-%m-%d') as dateCommited 
				From  tblTK_EmpViolations
				Inner Join tblEmpMast on tblTK_EmpViolations.empNo=tblEmpMast.empNo
				Left Join tblBioEmp on tblEmpMast.empNo=tblBioEmp.empNo
				Inner Join tblTK_ViolationType on tblTK_EmpViolations.violationCd=tblTK_ViolationType.violationCd
				Inner Join tblDepartment on tblEmpMast.empDiv =tblDepartment.divCode 
					and tblEmpMast.empDepCode=tblDepartment.deptCode
				WHERE tblTK_EmpViolations.compCode='{$_SESSION['company_code']}' and tblDepartment.deptLevel='2' 
					$branch $date $emp $bio $violation
				ORDER BY tblEmpMast.empLastName,tblEmpMast.empFirstName,tblEmpMast.empMidName,tblTK_EmpViolations.tsDate";
		
		return $this->getArrRes($this->execQry($sql));
	}

	function violationHeader($violations){
		$sql = "Select * from tblTK_ViolationType where violationCd='$violations'";
		$valViolation = $this->getSqlAssoc($this->execQry($sql));	
		return $valViolation['violationDesc'];
	}
	
	function getTblData($tbl, $cond, $orderBy, $ouputType)
	{
		$qryTblInfo = "Select * from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
		//echo $qryTblInfo;
		$resTblInfo = $this->execQry($qryTblInfo);
		if($ouputType == 'sqlAssoc')
			return $this->getSqlAssoc($resTblInfo);
		else
			return $this->getArrRes($resTblInfo);
	}
	
//	function getCompName($compCode){
//		$qry = "SELECT compName FROM tblCompany WHERE compStat = 'A' AND compCode = '{$compCode}' ";
//		$res = $this->execQry($qry);
//		$row = $this->getSqlAssoc($res);
////		if (in_array($compCode,array(1,2,7,8,9,10,11,12))){
////			return $row['compName'] = 'PUREGOLD PRICE CLUB, INC.';
////		}
////		else{
//			return  $row['compName'];
////		}
//	}	
	
	function TS_Summary($frm,$to,$hist){
	$tsqry=	"SELECT
tbltk_timesheet$hist.tsDate AS tsDate,
tblempmast.empLastName AS empLastName,
tblempmast.empFirstName AS empFirstName,
tbltk_timesheet$hist.empNo AS empNo,
Sum(tbltk_overtime$hist.hrsOTLe8) AS OTL8,
Sum(tbltk_overtime$hist.hrsOTGt8) AS OTGT8,
Sum(tbltk_overtime$hist.hrsRegNDLe8) AS HrsND,
Sum(tbltk_deductions$hist.hrsTardy) AS trdy,
Sum(tbltk_deductions$hist.hrsUT) AS ut,
Sum(tbltk_timesheet$hist.hrsWorked) AS hrswrk,
tblbranch.brnDesc AS brnDesc,
tbldepartment.deptDesc AS deptDesc,
tblpaycat.payCatDesc AS payCatDesc
from ((((((`tblempmast` left join `tbltk_timesheet$hist` on((`tbltk_timesheet$hist`.`empNo` = `tblempmast`.`empNo`))) left join `tbltk_overtime$hist` on(((`tbltk_overtime$hist`.`empNo` = `tbltk_timesheet$hist`.`empNo`) and (`tbltk_overtime$hist`.`tsDate` = `tbltk_timesheet$hist`.`tsDate`)))) left join `tbltk_deductions$hist` on(((`tbltk_deductions$hist`.`empNo` = `tbltk_timesheet$hist`.`empNo`) and (`tbltk_deductions$hist`.`tsDate` = `tbltk_timesheet$hist`.`tsDate`)))) join `tblbranch` on((`tblempmast`.`empBrnCode` = `tblbranch`.`brnCode`))) join `tbldepartment` on(((`tblempmast`.`empDiv` = `tbldepartment`.`divCode`) and (`tblempmast`.`empDepCode` = `tbldepartment`.`deptCode`) and (`tblempmast`.`empSecCode` = `tbldepartment`.`sectCode`)))) join `tblpaycat` on((`tblempmast`.`empPayCat` = `tblpaycat`.`payCat`)))
where (`tbltk_timesheet$hist`.`tsDate` between '$frm' and '$to')
group by `tbltk_timesheet$hist`.`empNo`";
return $this->getArrRes($this->execQry($tsqry));

	}

}

?>