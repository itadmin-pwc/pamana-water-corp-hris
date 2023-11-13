<?
/*
	Date Created	:	072010
	Created By		:	Genarra Arong
*/

class maintenanceObj extends commonObj {
	
	function getMaxShiftCode($tbl, $cond, $orderBy){
		$qryShiftMax = "Select max(CAST(shiftCode as UNSIGNED)) AS shiftCode from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
		//echo $qryShiftInfo."<br>";
		$resShiftMax = $this->execQry($qryShiftMax);
		return $this->getSqlAssoc($resShiftMax);
	}
	
	//Common Function
	function getShiftInfo($tbl, $cond, $orderBy)
	{
		$qryShiftInfo = "Select * from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
		//echo $qryShiftInfo."<br>";
		$resShiftInfo = $this->execQry($qryShiftInfo);
		return $this->getSqlAssoc($resShiftInfo);
	}
	
	function getShiftInfos($tbl, $cond, $orderBy)
	{
		echo $qryShiftInfo = "Select * from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
		//echo $qryShiftInfo."<br>";
		$resShiftInfo = $this->execQry($qryShiftInfo);
		return $this->getSqlAssoc($resShiftInfo);
	}
	
	//Shift Code Maintenance
	
	function validateShiftCodeDtl($array)
	{
		$arr_Day = array(1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday');
		$arrTypesTime = array(1=>'Time In',2=>'Lunch Out',3=>'Lunch In',4=>'Break Out',5=>'Break In',6=>'Time Out');
		
		//Convert All Time to Strtotime	
		for($day = 1; $day<=7; $day++)
		{													
			$time[$day][1] = date("Y-m-d")." ".$array[$day."-t_in"];
			$time[$day][2] = date("Y-m-d")." ".$array[$day."-l_out"];
			$time[$day][3] = date("Y-m-d")." ".$array[$day."-l_in"];
			$time[$day][4] = date("Y-m-d")." ".$array[$day."-b_out"];
			$time[$day][5] = date("Y-m-d")." ".$array[$day."-b_in"];
			$time[$day][6] = date("Y-m-d")." ".$array[$day."-t_out"];
		}
		
		//4 . if Cross Day == Yes, user can encode time which is less than to time in (Sample time in 08:30, lunch out can be 07:30, applicable
		//lunch Out, Break Out) else NO OVERLAPPING OF SCHED (PHP CODE)
		for($tInday = 1; $tInday<=7; $tInday++)
		{
			$sumLunchHrs=$sumBrkHrs=0;
					
			for($t=1; $t<=7; $t++)
			{	
				for($t_g=6; $t_g>$t; $t_g--)
				{
					//echo $time[$tInday][$t_g]."<".$time[$tInday][$t]."==".$array[$tInday."-crossDay"]."\n";
						
						//echo $tInday."=".$time[$tInday][$t_g]."<".$time[$tInday][$t]."=".$array[$tInday."-crossDay"]."\n";
					if((strtotime($time[$tInday][$t_g])<strtotime($time[$tInday][$t])) && ($array[$tInday."-crossDay"]==''))
					{
						//echo $time[$tInday][$t_g]."<".$time[$tInday][$t]."==".$array[$tInday."-crossDay"]."\n";
						if( (date("H:i", strtotime($time[$tInday][1]))=="") || (date("H:i", strtotime($time[$tInday][2]))=="") || (date("H:i", strtotime($time[$tInday][1]))=="00:00") || (date("H:i", strtotime($time[$tInday][2]))=="00:00")) 
						{
							//echo "(((".date("H:i", strtotime($time[$tInday][1]))."==)&&(".date("H:i", strtotime($time[$tInday][2]))."==))||((".date("H:i", strtotime($time[$tInday][1]))."==00:00)&&(".date("H:i", strtotime($time[$tInday][2]))."==00:00)))";
							return "1-"."Invalid Time Detail on Day ".$arr_Day[$tInday]." : ".$arrTypesTime[$t_g]." : ".date("H:i", strtotime($time[$tInday][$t_g]))." should not be less than ".$arrTypesTime[$t]." : ".date("H:i", strtotime($time[$tInday][$t]));
							break;
						}
						
					}
				}
				
				
				//5. Lunch Out can be 1 or 2 hours, get sum of lunch out and in, if the sum of 2 is less than / greater than 1 hours, confirm the user.
				if(($t>=2) && ($t<=5))
				{
					if(($t==2)||($t==3))
					{
						$sumLunchHrs = ((strtotime($time[$tInday][3]) - strtotime($time[$tInday][2]))/3600);
					}
					elseif(($t==4)||($t==5))
					{
						$sumBrkHrs = ((strtotime($time[$tInday][5]) - strtotime($time[$tInday][4]))/3600);
					}
					
					if($sumLunchHrs>1)
					{
						return "2-"."Confirm : Lunch Hr. is greater than 1 hour? On Day ".$arr_Day[$tInday];
						break;
					}
					elseif($sumBrkHrs>0.25)
					{
						return "2-"."Confirm : Break Hr. is greater than 15 mins.? On Day ".$arr_Day[$tInday];
						break;
					}
					
					
				}
			}
				//6. Summation of the 6 time should be 8 hrs (Regular Day) else if (saturday, Time In and Out ang meron) (PHP CODE)
				
				$diff_tInOut = strtotime($time[$tInday][6]) - strtotime($time[$tInday][1]);
				$diff_lInOut = strtotime($time[$tInday][3]) - strtotime($time[$tInday][2]);
				$diff_bInOut = strtotime($time[$tInday][5]) - strtotime($time[$tInday][4]);
				
				$sumHrsDay = 	($diff_tInOut - $diff_lInOut)/3600;
				
				if(($array[$tInday."-t_in"]!="")&&($array[$tInday."-l_out"]!="")&&($array[$tInday."-l_in"]!="")&&($array[$tInday."-b_out"]!="")&&($array[$tInday."-b_in"]!="")&&($array[$tInday."-t_out"]!=""))
				{
					if(($array[$tInday."-t_in"]!="00:00")&&($array[$tInday."-l_out"]!="00:00")&&($array[$tInday."-l_in"]!="00:00")&&($array[$tInday."-b_out"]!="00:00")&&($array[$tInday."-b_in"]!="00:00")&&($array[$tInday."-t_out"]!="00:00"))
					{
						
						if($sumHrsDay<8)
						{
							return "3-"."Confirm : The Set Shift Detail is not equal to 8 hours Worked Day : ".$arr_Day[$tInday];
							break;
						}
					}
				}
		}
		
		//5. Lunch Out can be 1 or 2 hours, get sum of lunch out and in, if the sum of 2 is less than / greater than 1 hours, confirm the user.
	}
	
	function maint_Shift_Code($action, $array)
	{
			$ans=0;
			switch($action)
			{
				case "Add":
					$Trns=$this->beginTran();
					$Qry_ShiftCode ="";
					$hdr_Qry_ShiftCode = "Insert into tblTK_ShiftHdr(compCode,shiftCode,shiftDesc,shiftLongDesc, status,dateAdded,addedBy) values('".$_SESSION["company_code"]."','".$array["txtShiftCode"]."','".str_replace("'","''",stripslashes($array["txtShiftDesc"]))."','".str_replace("'","''",stripslashes($array["txtShiftLongDesc"]))."','A','".date("Y-m-d")."','".$_SESSION['employee_number']."');";
					for($day=1; $day<=7; $day++)
					{
						$Qry_ShiftCode="Insert into tblTK_ShiftDtl(compCode,shftCode,dayCode,shftTimeIn,
											shftLunchOut,shftLunchIn,shftBreakOut,shftBreakIn,shftTimeOut,
											crossDay,RestDayTag,dateAdded,addedBy)
										 values('".$_SESSION["company_code"]."','".$array["txtShiftCode"]."','".$day."',
											'".($array[$day."-t_in"]!=""?$array[$day."-t_in"]:"00:00")."',
											'".($array[$day."-l_out"]!=""?$array[$day."-l_out"]:"00:00")."',
											'".($array[$day."-l_in"]!=""?$array[$day."-l_in"]:"00:00")."',
											'".($array[$day."-b_out"]!=""?$array[$day."-b_out"]:"00:00")."',
											'".($array[$day."-b_in"]!=""?$array[$day."-b_in"]:"00:00")."',
											'".($array[$day."-t_out"]!=""?$array[$day."-t_out"]:"00:00")."',
											'".($array[$day."-crossDay"]==1?"Y":"N")."', '".($array[$day."-restDay"]==1?"Y":"N")."',
											'".date("Y-m-d")."','".$_SESSION['employee_number']."');";
						if($Trns){
							$Trns = $this->execQry($Qry_ShiftCode);
						}
					}
						if($Trns){
							$Trns = $this->execQry($hdr_Qry_ShiftCode);
						}
					if($Trns==true){
						$this->commitTran();
						$ans = 1;	
					}
					else{
						$this->rollbackTran();
						$ans = 0;		
					}
				break;
				
				case "Update":
					$Trns=$this->beginTran();
					$Qry_ShiftCode ="";
					$hdr_Qry_ShiftCode = "Update tblTK_ShiftHdr set shiftDesc='".str_replace("'","''",stripslashes($array["txtShiftDesc"]))."', shiftLongDesc='".str_replace("'","''",stripslashes($array["txtShiftLongDesc"]))."', status='A' where compCode='".$_SESSION["company_code"]."' and shiftCode='".$array["txtShiftCode"]."';";
					for($day=1; $day<=7; $day++)
					{
						$Qry_ShiftCode="Update tblTK_ShiftDtl 
										set shftTimeIn='".($array[$day."-t_in"]!=""?$array[$day."-t_in"]:"00:00")."', 
											shftLunchOut='".($array[$day."-l_out"]!=""?$array[$day."-l_out"]:"00:00")."',
											shftLunchIn='".($array[$day."-l_in"]!=""?$array[$day."-l_in"]:"00:00")."', 
											shftBreakOut='".($array[$day."-b_out"]!=""?$array[$day."-b_out"]:"00:00")."', 
											shftBreakIn='".($array[$day."-b_in"]!=""?$array[$day."-b_in"]:"00:00")."',
											shftTimeOut='".($array[$day."-t_out"]!=""?$array[$day."-t_out"]:"00:00")."', 
											crossDay='".($array[$day."-crossDay"]==1?"Y":"N")."', 
											RestDayTag='".($array[$day."-restDay"]==1?"Y":"N")."' 
										where compCode='".$_SESSION["company_code"]."' and shftCode='".$array["txtShiftCode"]."' 
											and dayCode='".$day."';"; 
						if($Trns){
							$Trns = $this->execQry($Qry_ShiftCode);
						}
					}
						if($Trns){
							$Trns = $this->execQry($hdr_Qry_ShiftCode);
						}
					if($Trns==true){
						$this->commitTran();
						$ans = 1;	
					}
					else{
						$this->rollbackTran();
						$ans = 0;		
					}
				break;
			
				case "Delete":
					$Trns=$this->beginTran();
					$Qry_ShiftCode ="Update tblTK_ShiftHdr set status='D' where compCode='".$_SESSION["company_code"]."' and shiftCode='".$array["shiftCode"]."'";
						if($Trns){
							$Trns = $this->execQry($Qry_ShiftCode);
						}
					if($Trns==true){
						$this->commitTran();
						$ans = 1;	
					}
					else{
						$this->rollbackTran();
						$ans = 0;		
					}
				break;
	
				case "UpdateShift":
					$Trns=$this->beginTran();
					$Qry_ShiftCode ="Update tblTK_ShiftHdr set status='A' where compCode='".$_SESSION["company_code"]."' and shiftCode='".$array["shiftCode"]."'";
						if($Trns){
							$Trns = $this->execQry($Qry_ShiftCode);
						}
					if($Trns==true){
						$this->commitTran();
						$ans = 1;	
					}
					else{
						$this->rollbackTran();
						$ans = 0;		
					}
				break;
			}
		if($ans==1){
			return true;		
		}
		else{
			return false;	
		}
			
	}
	//End of Shift Code Maintenance
	
	
	//Employee Shift Maintenance
	function getListShift()
	{
		$qrygetListShift = "Select * from tblTK_ShiftHdr where compCode='".$_SESSION["company_code"]."' and status='A' order by shiftDesc";
		$resgetListShift = $this->execQry($qrygetListShift);

		return $this->getArrRes($resgetListShift);
	}
	
	//Employee Shift Maintenance for branch
	function getListShiftBranch(){
		$qrygetListShift = "Select * from tblTK_ShiftHdr where compCode='".$_SESSION["company_code"]."' and status='A' and shiftHOTag is Null 
							or shiftHOTag = '' order by shiftDesc";
		$resgetListShift = $this->execQry($qrygetListShift);

		return $this->getArrRes($resgetListShift);
	}
	
	function maint_EmpShift($action, $array)
	{
		$gp = 0;
		if (!empty($array["gracePeriod"]) && is_numeric($array["gracePeriod"])) {
			$gp = $array["gracePeriod"];
		}
		$ans = 0;
		switch($action)
		{
			case "Add":
				$Trns = $this->beginTran();
					$Qry_EmpShift = "Insert tblTK_EmpShift(compCode,empNo, shiftCode, bioNo, trdHrsExempt, utHrsExempt, 
										otExempt, dateAdded, addedBy, status, CWWTag, gracePeriod) 
									values('".$_SESSION["company_code"]."','".$array["txtEmpNo"]."','".$array["shiftcode"]."',
										'".$array["txtEmpBio"]."','".($array["chkWrkHrsExemptTag"]!=""?"Y":"N")."',
										'".($array["chkFlexiExemptTag"]!=""?"Y":"N")."','".($array["chkOtExemptTag"]!=""?"Y":"N")."',
										'".date("Y-m-d")."','".$_SESSION['employee_number']."','A',
										".($array["chkCWWTag"]!=""?"'Y'":"NULL").", " . $gp . ")";
				if($Trns){
					$Trns = $this->execQry($Qry_EmpShift);	
				}				 
				if(!$Trns){
					$Trns = $this->rollbackTran();
					$ans = 0;	
				}
				else{
					$Trns = $this->commitTran();
					$ans = 1;	
				}
			break;
			
			case "Update":
					$Trns = $this->beginTran();
					//Insert First to tblTk_EmpShiftHist
						$arr_EmpShiftDtl = $this->getShiftInfo('tblTK_EmpShift', " and empNo='".$array["txtEmpNo"]."'", '');
					
						$Qry_EmpShiftHist = "Insert tblTK_EmpShiftHist(compCode,empNo, shiftCode, bioNo, trdHrsExempt, utHrsExempt, 
												otExempt, dateAdded, addedBy, dateUpdated ,updatedBy, status, CWWTag, gracePeriod)
											 values('".$arr_EmpShiftDtl["compCode"]."','".$array["txtEmpNo"]."',
											 	'".$arr_EmpShiftDtl["shiftCode"]."','".$arr_EmpShiftDtl["bioNo"]."',
												'".$arr_EmpShiftDtl["trdHrsExempt"]."','".$arr_EmpShiftDtl["utHrsExempt"]."',
												'".$arr_EmpShiftDtl["otExempt"]."',
												'".date("Y-m-d", strtotime($arr_EmpShiftDtl["dateAdded"]))."',
												'".$arr_EmpShiftDtl["addedBy"]."','".date("Y-m-d")."',
												'".$_SESSION['employee_number']."','".$arr_EmpShiftDtl["status"]."',
												'".$arr_EmpShiftDtl["CWWTag"]."',
												" . $arr_EmpShiftDtl["gracePeriod"] . ")";
				
					/*$Qry_EmpShift.= "Update tblTK_EmpShift set shiftCode='".$array["shiftcode"]."', bioNo='".$array["txtEmpBio"]."', 
					absentExempt='".($array["chkAbsentExemptTag"]!=""?"Y":"N")."', 
										trdHrsExempt='".($array["chkWrkHrsExemptTag"]!=""?"Y":"N")."', utHrsExempt='".($array["chkFlexiExemptTag"]!=""?"Y":"N")."', otExempt='".($array["chkOtExemptTag"]!=""?"Y":"N")."', 
										lunchHrsExempt='".($array["chkLunchExemptTag"]!=""?"Y":"N")."',
										dateUpdated='".date("m/d/Y")."', updatedBy='".$_SESSION['employee_number']."', status='".$array["cmbShitCodeStat"]."'
									 where compCode='".$_SESSION["company_code"]."' and empNo='".$array["txtEmpNo"]."'"; 
			*/
			
					$Qry_EmpShift = "Update tblTK_EmpShift set shiftCode='".$array["shiftcode"]."', bioNo='".$array["txtEmpBio"]."', 
									 dateUpdated='".date("Y-m-d")."', updatedBy='".$_SESSION['employee_number']."', status='A',
									 CWWTag=".($array["chkCWWTag"]!=""?"'Y'":"NULL").",
									 gracePeriod=" . $gp . "
									 where compCode='".$_SESSION["company_code"]."' and empNo='".$array["txtEmpNo"]."'"; 
					if($Trns){
						$Trns = $this->execQry($Qry_EmpShiftHist);
						$Trns = $this->execQry($Qry_EmpShift);	
					}				 
					if(!$Trns){
						$Trns = $this->rollbackTran();
						$ans = 0;	
					}
					else{
						$Trns = $this->commitTran();
						$ans = 1;	
					}
			
			break;
			
			case "Delete":
				$Trns = $this->beginTran();
				//If the Employee is Resigned = Automatically Deleted in the Database, else Set Status = Deleted
				$userInfo = $this->getUserInfo($_SESSION['company_code'],$array["empNo"],'');
				
				$arr_EmpShiftDtl = $this->getShiftInfo('tblTK_EmpShift', " and empNo='".$array["empNo"]."'", '');
					
				$Qry_EmpShift = "Insert tblTK_EmpShiftHist(compCode,empNo, shiftCode, bioNo, trdHrsExempt, utHrsExempt, 
								otExempt, dateAdded, addedBy, dateUpdated ,updatedBy, status, CWWTag) 
							 	values('".$arr_EmpShiftDtl["compCode"]."','".$array["empNo"]."','".$arr_EmpShiftDtl["shiftCode"]."',
								'".$arr_EmpShiftDtl["bioNo"]."','".$arr_EmpShiftDtl["utHrsExempt"]."',
								'".$arr_EmpShiftDtl["utHrsExempt"]."','".$arr_EmpShiftDtl["otExempt"]."',
								'".date("Y-m-d", strtotime($arr_EmpShiftDtl["dateAdded"]))."',
								'".$arr_EmpShiftDtl["addedBy"]."','".date("Y-m-d")."','".$_SESSION['employee_number']."', 
								'".$arr_EmpShiftDtl["status"]."','".$arr_EmpShiftDtl["CWWTag"]."')";
		
				if(($userInfo["empStat"]=='RS') || ($userInfo["empPayCat"]=='9'))
				{
					$Qry_EmpShiftDelete = "Delete from tblTK_EmpShift where compCode='".$_SESSION["company_code"]."' and empNo='".$array["empNo"]."'";
				}
				else
				{
					$Qry_EmpShiftUpdate= "Update tblTK_EmpShift set status='D' where compCode='".$_SESSION["company_code"]."' and empNo='".$array["empNo"]."'";
				}
				
				if($Trns){
					$Trns = $this->execQry($Qry_EmpShift);	
				}
				if($Qry_EmpShiftDelete!=""){
					if($Trns){
						$Trns = $this->execQry($Qry_EmpShiftDelete);	
					}	
				}
				if($Qry_EmpShiftUpdate!=""){
					if($Trns){
						$Trns = $this->execQry($Qry_EmpShiftUpdate);	
					}	
				}
				
				if(!$Trns){
					$Trns = $this->rollbackTran();
					$ans = 0;	
				}
				else{
					$Trns = $this->commitTran();
					$ans = 1;	
				}
				
			break;
			
			case "setToActive":
				$Trns = $this->beginTran();
				$userInfo = $this->getUserInfo($_SESSION['company_code'],$array["empNo"],'');
				
				$arr_EmpShiftDtl = $this->getShiftInfo('tblTK_EmpShift', " and empNo='".$array["empNo"]."'", '');
					
				$Qry_EmpShift = "Insert tblTK_EmpShiftHist(compCode,empNo, shiftCode, bioNo, trdHrsExempt, utHrsExempt, 
								otExempt, dateAdded, addedBy, dateUpdated ,updatedBy, status, CWWTag) 
							 	values('".$arr_EmpShiftDtl["compCode"]."','".$array["empNo"]."','".$arr_EmpShiftDtl["shiftCode"]."',
								'".$arr_EmpShiftDtl["bioNo"]."','".$arr_EmpShiftDtl["wrkHrsExempt"]."',
								'".$arr_EmpShiftDtl["flexiExempt"]."','".$arr_EmpShiftDtl["otExempt"]."',
								'".date("Y-m-d", strtotime($arr_EmpShiftDtl["dateAdded"]))."','".$arr_EmpShiftDtl["addedBy"]."',
								'".date("Y-m-d")."','".$_SESSION['employee_number']."', '".$arr_EmpShiftDtl["status"]."', 
								'".$arr_EmpShiftDtl["CWWTag"]."')";
			
				$Qry_EmpShiftUpdate= "Update tblTK_EmpShift set status='A' where compCode='".$_SESSION["company_code"]."' and empNo='".$array["empNo"]."'";
				if($Trns){
					$Trns = $this->execQry($Qry_EmpShift);	
					$Trns = $this->execQry($Qry_EmpShiftUpdate);	
				}
				if(!$Trns){
					$Trns = $this->rollbackTran();
					$ans = 0;	
				}
				else{
					$Trns = $this->commitTran();
					$ans = 1;	
				}				
			break;
			
			case "clearAllShift":
				$Trns = $this->beginTran();
				$userInfo = $this->getUserInfo($_SESSION['company_code'],$array["empNo"],'');
					
				$Qry_EmpShift = "Insert tblTK_EmpShiftHist(compCode, empNo, shiftCode, bioNo, trdHrsExempt, utHrsExempt, 
									otExempt, dateAdded, addedBy, dateUpdated ,updatedBy, status, CWWTag)
								Select compCode, empNo, shiftCode, bioNo, trdHrsExempt, utHrsExempt,
									otExempt, dateAdded, addedBy, '".date("Y-m-d")."', '".$_SESSION['employee_number']."', 
									status, CWWTag
								from tblTK_EmpShift;"; 	   
								 
				$Qry_EmpShiftDelete= "Delete from tblTK_EmpShift where compCode='".$_SESSION["company_code"]."';";
				if($Trns){
					$Trns = $this->execQry($Qry_EmpShift);	
					$Trns = $this->execQry($Qry_EmpShiftDelete);	
				}
				if(!$Trns){
					$Trns = $this->rollbackTran();
					$ans = 0;	
				}
				else{
					$Trns = $this->commitTran();
					$ans = 1;	
				}				
			break;			
		}
		//echo $Qry_EmpShift;
		//return $this->execQry($Qry_EmpShift);
		if($ans==1){
			return true;	
		}
		else{
			return false;	
		}
		
	}
	
	
	//Attendance Application Maintenance
	function maint_attdn_type($action, $array)
	{
		switch($action)
		{
			case "Add":
				$qryAttdnType = "Insert into tblTK_AppTypes(compCode,tsAppTypeCd,appTypeDesc,appTypeShortDesc, deductionTag,leaveTag,appStatus, leaveTypeTag) values('".$_SESSION["company_code"]."','".$array["txtAppTypeCode"]."','".str_replace("'","''",stripslashes($array["txtAppTypeDesc"]))."','".substr($array["txtAppTypeDesc"],0,5)."','".($array["dedTag"]!='0'?"Y":"N")."','".($array["leaveTag"]!='0'?"Y":"N")."','".$array["cmbAttenAppCodeStat"]."','".($array["leaveTypeTag"]!='0'?"Y":"N")."')";
			break;
			
			case "Update":
				$qryAttdnType = "Update tblTK_AppTypes set deductionTag='".($array["dedTag"]!='0'?"Y":"N")."', leaveTag='".($array["leaveTag"]!='0'?"Y":"N")."', leaveTypeTag='".($array["leaveTypeTag"]!='0'?"Y":"N")."', appStatus='".$array["cmbAttenAppCodeStat"]."' where compCode='".$_SESSION["company_code"]."' and tsAppTypeCd='".$array["txtAppTypeCode"]."'";
			break;
			
			case "Delete":
				$qryAttdnType = "Update tblTK_AppTypes set appStatus='D' where compCode='".$_SESSION["company_code"]."' and tsAppTypeCd='".$_GET["attnAppCode"]."'";
			
			break;
			
			case "setAttdnAppActive":
				$qryAttdnType = "Update tblTK_AppTypes set appStatus='A' where compCode='".$_SESSION["company_code"]."' and tsAppTypeCd='".$_GET["attnAppCode"]."'";
			
			break;
			
			default;
			break;
		}
		return $this->execQry($qryAttdnType);
	}
	
	//Violation Type Maintenance
	function maint_violation_type($action, $array)
	{
		switch($action)
		{
			case "Add":
				$qryVioType = "Insert into tblTK_ViolationType(compCode,violationCd,violationShortDesc,violationDesc,violationStat) values('".$_SESSION["company_code"]."','".$array["txtVioCode"]."','".str_replace("'","''",stripslashes(substr($array["txtVioTypeDesc"],0,5)))."','".str_replace("'","''",stripslashes($array["txtVioTypeDesc"]))."','".$array["cmbVioCodeStat"]."')";
			break;
			
			case "Update":
				$qryVioType = "Update tblTK_ViolationType set violationStat='".$array["cmbVioCodeStat"]."' where compCode='".$_SESSION["company_code"]."' and violationCd='".$array["txtVioCode"]."'";
			break;
			
			case "Delete":
				$qryVioType = "Update tblTK_ViolationType set violationStat='D' where compCode='".$_SESSION["company_code"]."' and violationCd='".$_GET["vioCode"]."'";
			
			break;
			
			case "setVioTypeActive":
				$qryVioType = "Update tblTK_ViolationType set violationStat='A' where compCode='".$_SESSION["company_code"]."' and violationCd='".$_GET["vioCode"]."'";
			break;
			
			default:
			break;
		}
		//echo $qryVioType;
		return $this->execQry($qryVioType);
	}
	
	//Reasons Maintenance
	function maint_reason($action, $array)
	{
		switch($action)
		{
			case "Add":
				$qryRes = "Insert into tblTK_Reasons (compCode,reason,stat,dateAdded,userAdded,changeShift,
				changeRestDay,leaveApp,obApp,ovApp,underTime) values('".$_SESSION["company_code"]."',
				'".str_replace("'","''",stripslashes($array["txtReason"]))."','".$array["cmbResStat"]."',
				'".date("Y-m-d")."','".$_SESSION['employee_number']."','".($array["chkChangeShift"]!=""?"Y":"NULL")."',
				'".($array["chkChangeRestDay"]!=""?"Y":"NULL")."','".($array["chkLeaveApp"]!=""?"Y":"NULL")."',
				'".($array["chkOB"]!=""?"Y":"NULL")."','".($array["chkOT"]!=""?"Y":"NULL")."',
				'".($array["chkUT"]!=""?"Y":"NULL")."')";
			break;
			
			case "Update":
				$qryRes = "Update tblTK_Reasons set stat='".$array["cmbResStat"]."',
				changeShift=".($array["chkChangeShift"]!=""?"'Y'":"NULL").",
				changeRestDay=".($array["chkChangeRestDay"]!=""?"'Y'":"NULL").",
				leaveApp=".($array["chkLeaveApp"]!=""?"'Y'":"NULL").",
				obApp=".($array["chkOB"]!=""?"'Y'":"NULL").",
				ovApp=".($array["chkOT"]!=""?"'Y'":"NULL").",
				underTime=".($array["chkUT"]!=""?"'Y'":"NULL")." 
				where compCode='".$_SESSION["company_code"]."' and reason_id='".$array["txtResCode"]."'";
			break;
			
			case "Delete":
				echo $qryRes = "Update tblTK_Reasons set stat='D' where compCode='".$_SESSION["company_code"]."' and reason_id='".$_GET["resCode"]."'";
			
			break;
			
			case "setReasonActive":
				echo $qryRes = "Update tblTK_Reasons set stat='A' where compCode='".$_SESSION["company_code"]."' and reason_id='".$_GET["resCode"]."'";
			break;
			
			default:
			break;
		}
		//echo $qryVioType;
		return $this->execQry($qryRes);
	}
	
}
?>