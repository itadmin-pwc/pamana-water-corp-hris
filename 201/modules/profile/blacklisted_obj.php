<?php
class blackListObj extends commonObj {
	
	function chkEmpNoRecords($empNo)
	{
		$qryChecked = "Select * from tblBlacklistedEmp where blacklist_No='".$empNo."'";
		
		$res_Checked  = $this->execQry($qryChecked);
		
		return $res_Checked;
	}
	
	function getInfo($blacklist_id)
	{
		$qryChecked = "Select empNo,replace(empLastName,'Ñ','N') as emprLname, replace(empFirstName,'Ñ','N') as emprFname, replace(empMidName,'Ñ','N') as emprMidName, tblBlacklistedEmp.* from tblBlacklistedEmp where blacklist_No='".$blacklist_id."'";
		$res_Checked  = $this->execQry($qryChecked);
	
		return $this->getSqlAssoc($res_Checked);
	}
	
	function insEmptblBlackListed($empNo,$reason,$agency,$empencoded,$get_empLname,$get_empFname,$get_empMname,$get_empBDate,$get_empSssNo,$get_empTinNo,$get_empBrnCode,$get_empDateHired,$get_empDateResigned,$get_empCompCode,$get_empDept,$get_empPos)
	{
		if($empNo!="")
		{
			$arrEmpInfo = $this->getEmpInfo($empNo);
			if($empNo==$arrEmpInfo['empNo']){
				$empLname = $arrEmpInfo["empLastName"];
				$empFname = $arrEmpInfo["empFirstName"];
				$empMname = $arrEmpInfo["empMidName"];
				
				$empBDate = ($arrEmpInfo["empBday"]!=""?"'".date("Y-m-d", strtotime($arrEmpInfo["empBday"]))."'":"NULL");
				$empSssNo = $arrEmpInfo["empSssNo"];
				$empTinNo = $arrEmpInfo["empTin"];
				$empDepCode = $get_empDept;
				$empBrnCode = strtoupper($get_empBrnCode);
				$empPosCode = $get_empPos;
				$empDateHired = ($arrEmpInfo["dateHired"]!=""?"'".date("Y-m-d", strtotime($arrEmpInfo["dateHired"]))."'":"NULL");
				$empDateResigned = ($arrEmpInfo["dateResigned"]!=""?"'".date("Y-m-d", strtotime($arrEmpInfo["dateResigned"]))."'":"NULL");
				switch($arrEmpInfo["compCode"]) {
					case 1:
						$empCompCode = "PUREGOLD JUNIOR SUPERMARKET, INC.";
					break;
					case 2:
						$empCompCode = "PUREGOLD PRICE CLUB, INC.";
					break;
					case 4:
						$empCompCode = "PUREGOLD DUTY FREE  CLARK INC.";
					break;
					case 5:
						$empCompCode = "PUREGOLD DUTY FREE SUBIC INC.";
					break;
					case 6:
						$empCompCode = "PUREGOLD REALTY LEASING & MANAGEMENT CORPORATION";
					break;
				}
			}
			else{
				$empNo="";
				$empLname = strtoupper($get_empLname);
				$empFname = strtoupper($get_empFname);
				$empMname = strtoupper($get_empMname);
				$empBDate = ($get_empBDate!=""?"'".date("Y-m-d", strtotime($get_empBDate))."'":"NULL");
				$empSssNo = $get_empSssNo;
				$empTinNo = $get_empTinNo;
				$empDepCode = $get_empDept;	
				$empPosCode = $get_empPos;		
				$empBrnCode = strtoupper($get_empBrnCode);
				$empDateHired = ($get_empDateHired!=""?"'".date("Y-m-d", strtotime($get_empDateHired))."'":"NULL");
				$empDateResigned = ($get_empDateResigned!=""?"'".date("Y-m-d", strtotime($get_empDateResigned))."'":"NULL");
				$empCompCode = strtoupper($get_empCompCode);
			}
		}
		else
		{
			$empLname = strtoupper($get_empLname);
			$empFname = strtoupper($get_empFname);
			$empMname = strtoupper($get_empMname);
			$empBDate = ($get_empBDate!=""?"'".date("Y-m-d", strtotime($get_empBDate))."'":"NULL");
			$empSssNo = $get_empSssNo;
			$empTinNo = $get_empTinNo;
			$empDepCode = $get_empDept;	
			$empPosCode = $get_empPos;		
			$empBrnCode = strtoupper($get_empBrnCode);
			$empDateHired = ($get_empDateHired!=""?"'".date("Y-m-d", strtotime($get_empDateHired))."'":"NULL");
			$empDateResigned = ($get_empDateResigned!=""?"'".date("Y-m-d", strtotime($get_empDateResigned))."'":"NULL");
			$empCompCode = strtoupper($get_empCompCode);
		}
	
		$insQry = "Insert into tblBlacklistedEmp(empNo,empLastName,empFirstName,empMidName,
												 empBday,empSssNo,empTin,empDepCode,empBrnCode,
												 empPosId,dateHired,dateResigned,reason,compCode,
												 agency,dateEncoded,userId)
				   values('".$empNo."','".str_replace("'","''", $empLname)."','".str_replace("'","''",$empFname)."','".str_replace("'","''",$empMname)."',
				   		  ".$empBDate.",'".str_replace("-",'',$empSssNo)."','".str_replace("-",'',$empTinNo)."','".$empDepCode."','".str_replace("'","''", $empBrnCode)."',
						  '".$empPosCode."',".$empDateHired.",".$empDateResigned.",'".strtoupper(str_replace("'","''",stripcslashes($reason)))."','".str_replace("'","''",stripcslashes($empCompCode))."',
						  '".strtoupper(str_replace("'","''",stripcslashes($agency)))."','".date("Y-m-d")."','".str_replace("'","''",$empencoded)."')";
		$res_insQry  = $this->execQry($insQry);
		if($res_insQry){
			return true;
		}
		else{
			return false;
		}
	}
	
	function uptEmptblBlackListed($blackListNo,$reason,$agency,$empencoded)
	{
		$updQry = "Update tblBlacklistedEmp set reason='".strtoupper($reason)."', agency='".strtoupper($agency)."', updatedBy='".$empencoded."', dateUpdated='".date("Y-m-d")."'
					where blacklist_No='".$blackListNo."'";
		$res_updQry  = $this->execQry($updQry);
		if($res_updQry){
			return true;
		}
		else{
			return false;
		}
	}
	
	function delEmptblBlackListed($blackListNo)
	{
		$delQry = "Delete from tblBlacklistedEmp where blacklist_No='".$blackListNo."'";
		$res_delQry  = $this->execQry($delQry);
		if($res_delQry){
			return true;
		}
		else{
			return false;
		}
	}

}
?>