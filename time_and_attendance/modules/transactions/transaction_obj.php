<?
/*
	Date Created	:	08032010
	Created By		:	Genarra Arong
*/


class transactionObj extends commonObj {
	
	//Common Function
	function getTblData($tbl, $cond, $orderBy, $ouputType)
	{
		$qryTblInfo = "Select * from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
		$resTblInfo = $this->execQry($qryTblInfo);
		if($ouputType == 'sqlAssoc')
			return $this->getSqlAssoc($resTblInfo);
		else
			return $this->getArrRes($resTblInfo);
	}
	
	function getOpenPeriod($compCode,$grp,$cat) {
		$qry = "SELECT compCode, pdTSStat as pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdTSStat = 'O' AND 
			    compCode = '$compCode' AND
				payGrp = '$grp' AND 
				payCat = '$cat' ";
		
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	
	function getPeriodGtOpnPer($compCode, $payGrp, $payCat, $opnPeriod)
	{
		$qryOpnPeriod = "Select compCode, pdTSStat as pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate from tblPayPeriod where compCode='".$compCode."' and paygrp='".$payGrp."' and payCat='".$payCat."' and pdYear='".date("Y", strtotime($opnPeriod))."' and pdPayable>='".date("Y-m-d", strtotime($opnPeriod))."'";
		
		$resOpnPeriod = $this->execQry($qryOpnPeriod);
		return $this->getArrRes($resOpnPeriod);				
	}
	
	function getLastRefNo($fld)
	{
		$qryLastRefNo = "Select $fld as lastRefNo from tblTK_RefNo";
		$rsLastRefNo = $this->execQry($qryLastRefNo);
		
		return $this->getSqlAssoc($rsLastRefNo);
	}
	function updateLastRefNo($newrefNo,$fld){
		//SET otrefNo = '$newrefNo'  
		 $qryUpdateLastRefNo = "UPDATE tblTK_RefNo 
							SET $fld = '$newrefNo'  
							WHERE compCode = {$_SESSION['company_code']}";
		$resUpdateLastRefNo = $this->execQry($qryUpdateLastRefNo);
		if($resUpdateLastRefNo){
			return true;
		}
		else {
			return false;
		}
		
	}
	
	function getListShift()
	{
		$qrygetListShift = "Select * from tblTK_ShiftHdr where compCode='".$_SESSION["company_code"]."' and status='A' order by shiftCode";
		$resgetListShift = $this->execQry($qrygetListShift);

		return $this->getArrRes($resgetListShift);
	}
	
	
	
	/*
		Module Name :	View / Edit Employee Timesheet
	*/
	
	
	//Check Important Fields
	function chkImpFields($array)
	{
		$arrTypesTime = array(1=>'Time In',2=>'Lunch Out',3=>'Lunch In',4=>'Break Out',5=>'Break In',6=>'Time Out');
		
		//Convert All Time to Strtotime	
		$time[1] = date("Y-m-d")." ".$array["txtEtimeIn"];
		$time[2] = date("Y-m-d")." ".$array["txtElunchOut"];
		$time[3] = date("Y-m-d")." ".$array["txtElunchIn"];
		$time[4] = date("Y-m-d")." ".$array["txtEbrkOut"];
		$time[5] = date("Y-m-d")." ".$array["txtEbrkIn"];
		$time[6] = date("Y-m-d")." ".$array["txtEtimeOut"];
		
		if($array["txtDayTypeCd"]=='01')//Regular Day
		{
			
			if(($array["txtAppType"]=='12') || ($array["txtAppType"]=='14'))//For Half Day Leave With/Without Pay AM
			{
				if((date("H:i", strtotime($time[3]))=="00:00") || (date("H:i", strtotime($time[6]))=="00:00"))
					return "1-"."Lunch In and Time Out is required.";
				elseif((date("H:i", strtotime($time[1]))!="00:00") || (date("H:i", strtotime($time[2]))!="00:00"))
					return "1-"."Time In and Lunch Out is not required.";
				elseif((date("H:i", strtotime($time[4]))!="00:00") || (date("H:i", strtotime($time[5]))!="00:00"))
					return $this->chkOvrLapping($array, "HLFDAYAM", "4-5" );
				else
					return $this->chkOvrLapping($array, "", "" );
			}
			elseif(($array["txtAppType"]=='13') || ($array["txtAppType"]=='15'))//For Half Day Leave With/Without Pay PM
			{
				if((date("H:i", strtotime($time[1]))=="00:00") || (date("H:i", strtotime($time[2]))=="00:00"))
					return "1-"."Time In and Lunch Out is required.";
				elseif((date("H:i", strtotime($time[3]))!="00:00") || (date("H:i", strtotime($time[4]))!="00:00") || (date("H:i", strtotime($time[5]))!="00:00") || (date("H:i", strtotime($time[6]))!="00:00"))
					return "1-"."Lunch In/Break Out /Break In/Time Out is not required.";
				else
					return $this->chkOvrLapping($array, "", "" );
			}
			elseif(($array["txtAppType"]=='07') || ($array["txtAppType"]=='08'))//For Whole Day Leave = WDLWP / WDLWOP
			{
				for($tInday=1; $tInday<=6; $tInday++)
				{
					if(date("H:i", strtotime($time[$tInday]))!="00:00")
						return "1-"."Employee has filed an Whole Day Leave. No Time Details required.";
				}
			}
			else
			{
				for($tInday=1; $tInday<=6; $tInday++)
				{
					if(date("H:i", strtotime($time[$tInday]))=="00:00")
					{	
						if((substr($time[$tInday], 11, 10)==":") || (substr($time[$tInday], 11, 10)=="") || (substr($time[$tInday], 11, 2)>=25))
						{
							if($array["txtempBranchCode"]!='0001')
							{
								if(($tInday==1) || ($tInday==2) || ($tInday==3) || ($tInday==6))
								{
									if($array["violationCd"]==FAIL_ABSENT)
									{}
									else
									{	
										if($arrTypesTime[$tInday]=='Time In')
										{
											if($array["violationCd"]==FAIL_LOGIN) 
											{}
											else
												return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
										}
										elseif($arrTypesTime[$tInday]=='Lunch Out')
										{
											if(($array["violationCd"]==FAIL_LCHOUT) || ($array["violationCd"]==FAIL_LCHINOUT) || ($array["violationCd"]==FAIL_SKIPLUNCH))
											{}
											else
												return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
										}
										elseif($arrTypesTime[$tInday]=='Lunch In')
										{
											if(($array["violationCd"]==FAIL_LCHIN) || ($array["violationCd"]==FAIL_LCHINOUT) || ($array["violationCd"]==FAIL_SKIPLUNCH))
											{}
											else
												return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
										}
										elseif($arrTypesTime[$tInday]=='Time Out')
										{
											if($array["violationCd"]==FAIL_LOGOUT) 
											{}
											else
												return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
										}
										else
										{
											return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
										}
									}
								}
							}
							else
							{
								if(($array["txtcrossDay"]!="Y")||($array["txtCheckTag"]!=""))
								{
									if(($tInday==1) || ($tInday==2) || ($tInday==3) || ($tInday==6))
									{
										if(date("D", strtotime($array["txttsDate"]))!='Sat')
										{
											if($array["violationCd"]==FAIL_ABSENT)
											{}
											else
											{	
												if($arrTypesTime[$tInday]=='Time In')
												{
													if($array["violationCd"]==FAIL_LOGIN) 
													{}
													else
														return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
												}
												elseif($arrTypesTime[$tInday]=='Lunch Out')
												{
													if(($array["violationCd"]==FAIL_LCHOUT) || ($array["violationCd"]==FAIL_LCHINOUT) || ($array["violationCd"]==FAIL_SKIPLUNCH))
													{}
													else
														return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
												}
												elseif($arrTypesTime[$tInday]=='Lunch In')
												{
													if(($array["violationCd"]==FAIL_LCHIN) || ($array["violationCd"]==FAIL_LCHINOUT))
													{}
													else
														return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
												}
												elseif($arrTypesTime[$tInday]=='Time Out')
												{
													if($array["violationCd"]==FAIL_LOGOUT) 
													{}
													else
														return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
												}
												else
												{
													return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
												}
											}
										}
									}
									elseif((date("H:i", strtotime($time[4]))!="00:00") || (date("H:i", strtotime($time[5]))!="00:00"))
										return $this->chkOvrLapping($array, "REGDAY", "4-5" );
								}
								else
								{
									if(($tInday==1) || ($tInday==6))
										return "1-"."Day Type is Regular Day : ".$arrTypesTime[$tInday]." is required.";
									elseif((date("H:i", strtotime($time[2]))!="00:00") || (date("H:i", strtotime($time[3]))!="00:00"))
									{
										$lunchInErr = $this->chkOvrLapping($array, "REGDAY", "2-3" );
										if($lunchInErr!="")
											return 	$lunchInErr;
										elseif((date("H:i", strtotime($time[4]))!="00:00") || (date("H:i", strtotime($time[5]))!="00:00"))
											return $this->chkOvrLapping($array, "REGDAY", "4-5" );
											
									}
								}
							}
						}
					}
				}
				
				return $this->chkOvrLapping($array, "", "" );
			}
		}
		else//RestDay , Legal and Special, Legal and Special Holiday on a Restday
		{
			if((date("H:i", strtotime($time[1]))=="00:00") || date("H:i", strtotime($time[6]))=="00:00")
			{
				return "1-"."Time In and Time Out is required.";
				break;
			}
			else
			{
			
				return $this->chkOvrLapping($array, "", "" );
			}
			
		}
	}
	
	
	
	function chkOvrLapping($array, $remarks, $fval)
	{
		$arrTypesTime = array(1=>'Time In',2=>'Lunch Out',3=>'Lunch In',4=>'Break Out',5=>'Break In',6=>'Time Out');
		
		//Convert All Time to Strtotime	
		$time[1] = date("Y-m-d")." ".$array["txtEtimeIn"];
		$time[2] = date("Y-m-d")." ".$array["txtElunchOut"];
		$time[3] = date("Y-m-d")." ".$array["txtElunchIn"];
		$time[4] = date("Y-m-d")." ".$array["txtEbrkOut"];
		$time[5] = date("Y-m-d")." ".$array["txtEbrkIn"];
		$time[6] = date("Y-m-d")." ".$array["txtEtimeOut"];
		
		//PartNer
		$ftime[2] = $time[3]."-3";
		$ftime[3] = $time[2]."-2";
		$ftime[4] = $time[5]."-5";
		$ftime[5] = $time[4]."-4";
		
		if($remarks!="")
		{
			$fval = explode("-", $fval);
			
			for($fOne=0; $fOne<=1; $fOne++)
			{
				$expfTime = explode("-", $ftime[$fval[$fOne]]);
				if(date("H:i", strtotime($expfTime[0]))=="00:00")
				{
					return "1-".$arrTypesTime[$expfTime[1]]." is required.";
					break;
				}
				isset($expfTime);
			}
		}
//		else
//		{
//			for($t=1; $t<=7; $t++)
//			{	
//				if(date("H:i", strtotime($time[$t]))!="00:00")
//				{
//					for($t_g=6; $t_g>$t; $t_g--)
//					{
//						if(date("H:i", strtotime($time[$t]))!="00:00")
//						{
//							if((strtotime($time[$t_g])<strtotime($time[$t])) && ($array["txtcrossDay"]!='Y') && ($array["txtCheckTag"]==""))
//							{
//								if((date("H:i", strtotime($time[$t_g]))!="00:00")&&(date("H:i", strtotime($time[$t]))!="00:00"))
//								{
//									return "1-"."Invalid Time Detail on ".$arrTypesTime[$t_g]." : ".date("H:i", strtotime($time[$t_g]))." should not be less than ".$arrTypesTime[$t]." : ".date("H:i", strtotime($time[$t]))."";
//									break;
//								}
//							}
//						}
//					}
//				}
//			}
//		}
	}
	
	function tran_ViewEdit_Ts($action, $array)
	{	
		$time[1] = $array["txtEtimeIn"];
		$time[2] = $array["txtElunchOut"];
		$time[3] = $array["txtElunchIn"];
		$time[4] = $array["txtEbrkOut"];
		$time[5] = $array["txtEbrkIn"];
		$time[6] = $array["txtEtimeOut"];
		
		for($ctr=1; $ctr<=6; $ctr++)
		{
			$time[$ctr] = str_replace(" ","",stripslashes($time[$ctr]));
			
			if(($time[$ctr]=="") || ($time[$ctr]==":"))
				$time[$ctr] = "NULL";
			else
				$time[$ctr] = "'".$time[$ctr]."'";
			
			
		}
		
		switch($action)
		{
			case "Add":
				$Qry_TsCorr = "Insert into tblTK_TimeSheetCorr(compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, breakOut,breakIn, timeOut, crossTag, editReason, encodeDate, encodedBy, stat, logsExceeded) 
								values('".$_SESSION["company_code"]."', '".$array["empNo"]."', '".$array["txttsDate"]."', ".$time[1].", ".$time[2].", 
									   ".$time[3].", ".$time[4].", ".$time[5].",  ".$time[6].", ".($array["txtCheckTag"]!=""?"'Y'":"NULL").", ".($array["violationCd"]==0?"NULL":"'".$array["violationCd"]."'").", '".date("Y-m-d")."'
										, '".$_SESSION['employee_number']."', 'A', ".($array["txtlogsExceeded"]=='Y'?"'Y'":"NULL").");";
				 $Qry_TsCorr.= "Update tblTK_TimeSheet set checkTag='C', dateEdited='".date("Y-m-d")."', editedBy='".$_SESSION['employee_number']."', logsExceeded=NULL where compCode='".$_SESSION["company_code"]."' and empNo='".$array["empNo"]."' and tsDate='".$array["txttsDate"]."';";
			break;
			
			case "Update":
				$arr_EmpTsInfo =  $this->getTblData("tblTK_TimeSheetCorr", " and empNo='".$array["empNo"]."' and tsDate = '".date("Y-m-d", strtotime($array["txttsDate"]))."'", " ", "sqlAssoc");

				$Qry_TsCorr = "Insert into tblTK_TimeSheetCorr_original(compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, breakOut,breakIn, timeOut, crossTag, editReason, encodeDate, encodedBy, stat, logsExceeded) 
								values('".$_SESSION["company_code"]."', '".$array["empNo"]."', '".date("Y-m-d", strtotime($arr_EmpTsInfo["tsDate"]))."', ".($arr_EmpTsInfo["timeIn"]!=""?"'".$arr_EmpTsInfo["timeIn"]."'":"NULL").", ".($arr_EmpTsInfo["lunchOut"]!=""?"'".$arr_EmpTsInfo["lunchOut"]."'":"NULL").", 
									   ".($arr_EmpTsInfo["lunchIn"]!=""?"'".$arr_EmpTsInfo["lunchIn"]."'":"NULL").", ".($arr_EmpTsInfo["breakOut"]!=""?"'".$arr_EmpTsInfo["breakOut"]."'":"NULL").", ".($arr_EmpTsInfo["breakIn"]!=""?"'".$arr_EmpTsInfo["breakIn"]."'":"NULL").",  ".($arr_EmpTsInfo["timeOut"]!=""?"'".$arr_EmpTsInfo["timeOut"]."'":"NULL").", 
									   ".($arr_EmpTsInfo["crossTag"]!=""?"'Y'":"NULL").", ".($arr_EmpTsInfo["editReason"]==0?"NULL":"'".$arr_EmpTsInfo["editReason"]."'").", '".date("Y-m-d", strtotime($arr_EmpTsInfo["encodeDate"]))."'
										, '".$arr_EmpTsInfo["encodedBy"]."', '".$arr_EmpTsInfo["stat"]."', ".($arr_EmpTsInfo["logsExceeded"]=='Y'?"'".$arr_EmpTsInfo["logsExceeded"]."'":"NULL").");";

				$Qry_TsCorr.= "Update tblTK_TimeSheetCorr set timeIn=".$time[1].", lunchOut=".$time[2].", lunchIn=".$time[3].", 
																breakOut=".$time[4].", breakIn=".$time[5].", timeOut=".$time[6].", 
																crossTag=".($array["txtCheckTag"]!=""?"'Y'":"NULL").",
																editReason=".($array["violationCd"]==0?"NULL":"'".$array["violationCd"]."'").", 
																encodeDate='".date("Y-m-d")."', encodedBy='".$_SESSION['employee_number']."',
																logsExceeded = NULL
																where compCode='".$_SESSION["company_code"]."' and empNo='".$array["empNo"]."'
																and tsDate='".date("Y-m-d", strtotime($array["txttsDate"]))."';";
			break;
			
			
			default:
			break;
			
		}
		return $this->execQry($Qry_TsCorr);
		
	}
	
	
	/*Module Name : OB Application 08252010 Wednesday*/
	function checkOBEntryValidation($array)
	{
		$arr_ObRec_Checking = $this->getTblData("tblTK_OBApp", " and empNo='".$array["txtAddEmpNo"]."' and obDate='".date("Y-m-d", strtotime($array["obDate"]))."'", "order by obActualTimeIn,obActualTimeOut", "");
		
		$encObActualTimeIn = date("Y-m-d", strtotime($array["obDate"]))." ".$array["txtobTimeIn"];
		
		foreach($arr_ObRec_Checking as $arr_ObRec_Checking_val)
		{
			$recObActualTimeIn = date("Y-m-d", strtotime($arr_ObRec_Checking_val["obDate"]))." ".$arr_ObRec_Checking_val["obActualTimeIn"];
			$recObActualTimeOut = date("Y-m-d", strtotime($arr_ObRec_Checking_val["obDate"]))." ".$arr_ObRec_Checking_val["obActualTimeOut"];
			
			
			if((strtotime($encObActualTimeIn)>strtotime($recObActualTimeIn)) && (strtotime($encObActualTimeIn)>=strtotime($recObActualTimeOut)))
				$error = "";
			else
				$error.= $arr_ObRec_Checking_val["obActualTimeIn"]." - ".$arr_ObRec_Checking_val["obActualTimeOut"]." and ";
		}
		
		if($error!="")
			$error = substr($error,0,strlen($error)-4);
		
		
		return $error;
	}
	
	function tran_Ob($array,$action)
	{
		$arr_lastRefNo = $this->getLastRefNo("obrefNo");
		if($arr_lastRefNo["lastRefNo"]==""){
			$lastRefNo = 1;	
		}
		else{
			$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;	
		}
		
		
		$arr_checkifDataExists = $this->getTblData("tblTK_OBApp", " and empNo='".$array["txtAddEmpNo"]."' and obDate='".date("Y-m-d", strtotime($array["obDate"]))."' and obDestination='".$array["obdestination"]."' and obStat='P'", "", "sqlAssoc");
	
		if($arr_checkifDataExists["empNo"]!="")
		{
			return "Duplicate Entry of OB Application on the same OB Date and Destination.";
		}
		else
		{
			switch($action)
			{
				case "Add":
					$qryIns = "Insert into tblTK_OBApp(compCode,empNo,refNo,obDate,obDestination,dateFiled,obSchedIn,obSchedOut,obActualTimeIn,obActualTimeOut,obReason,hrs8Deduct,dateAdded,addedBy,obStat,crossDay) 
								  values('".$_SESSION["company_code"]."','".$array["txtAddEmpNo"]."','".$lastRefNo."','".date("Y-m-d", strtotime($array["obDate"]))."'
								  ,'".$array["obdestination"]."','".date("Y-m-d", strtotime($array["dateFiled"]))."','".$array["schedTimeIn"]."','".$array["schedTimeOut"]."','".$array["txtobTimeIn"]."','".$array["txtobTimeOut"]."',
								  '".strtoupper($array["cmbReasons"])."',".($array["rdnDeduct8"]==""?"NULL":"'Y'").", '".date("Y-m-d")."','".$_SESSION['employee_number']."','H',".($array["crossDay"]==""?"NULL":"'Y'").");";
				
	
			if($this->execQry($qryIns))
				//return "OB Application successfully saved.";
				if($this->updateLastRefNo($lastRefNo,"obrefNo")){
				return true;
			}

			else
				//return "OB Application failed.";
				return false;	
		

				break;
				
				case "Update":
					$qryIns = "Update tblTK_OBApp set dateFiled='".date("Y-m-d", strtotime($array["dateFiled"]))."', obDestination='".$array["obdestination"]."', 
								obActualTimeIn='".$array["txtobTimeIn"]."', obActualTimeOut='".$array["txtobTimeOut"]."', obReason='".strtoupper($array["obreason"])."', obSchedIn='".$array["schedTimeIn2"]."', obSchedOut='".$array["schedTimeOut2"]."',  
								hrs8Deduct=".($array["rdnDeduct8"]==""?"NULL":"'Y'").", dateUpdated='".date("Y-m-d")."', updatedBy='".$_SESSION['employee_number']."',crossDay=".($array["crossDay"]==""?"NULL":"'Y'")."
								where seqNo='".$array["inputTypeSeqNo"]."'";
				
			if($this->execQry($qryIns))
				//return "OB Application successfully saved.";	
				return true;

			else
				//return "OB Application failed.";
				return false;



				break;
		
			}
					
				
		}
			
			
	}
	
	/*Module Name : CS Application 1:57PM Tuesday*/
	/*Modify Date :	10/30/2010*/
	
/*	function validateEncodedTime($encShft, $whereClause)
	{
		$qryvalidateEncodedTime = "exec  sp_TK_checkShiftTimeIn ".$_SESSION['company_code'].",'".$encShft."', '".$whereClause."' ";
		$resvalidateEncodedTime = $this->execQry($qryvalidateEncodedTime);
		return $this->getArrRes($resvalidateEncodedTime);
		
	}
*/
	function validateEncodedTime($encShft, $whereClause)
	{
		$qryvalidateEncodedTime = "Select $whereClause from tblTK_ShiftDtl 
		where compCode='".$_SESSION['company_code']."' and $whereClause='".$encShft."'";
		$resvalidateEncodedTime = $this->execQry($qryvalidateEncodedTime);
		return $this->getArrRes($resvalidateEncodedTime);		
	}


	function tran_Cs($array,$action)
	{
		$arr_lastRefNo = $this->getLastRefNo("csrefNo");
		$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;
		
		switch($action)
		{
			case "Add":
				$qryIns = "Insert into tblTk_CsApp(compcode,empNo,refNo,dateFiled,csDateFrom,csShiftFromIn,csShiftFromOut,
												  csDateTo,csShiftToIn,csHiftToOut,csReason,dateAdded,addedBy,csStat,crossDay)
						   values('".$_SESSION["company_code"]."','".$array["txtAddEmpNo"]."','".$lastRefNo."',
						   		'".date("Y-m-d", strtotime($array["dateFiled"]))."',
								'".date("Y-m-d", strtotime($array["csDateFrom"]))."','".$array["schedTimeIn"]."',
								'".$array["schedTimeOut"]."','".date("Y-m-d", strtotime($array["csDateTo"]))."',
						   		'".$array["csTimeIn"]."','".$array["csTimeOut"]."',
								'".strtoupper(htmlspecialchars(addslashes($array["cmbReasons"])))."','".date("Y-m-d")."',
								'".$_SESSION["employee_number"]."','H',".($array["chkCrossDay"]!=""?"'Y'":"NULL").")";
			
			if($this->execQry($qryIns))
			//return "CS Application successfully saved.";
			if($this->updateLastRefNo($lastRefNo,"csrefNo")){
				return true;
			}
			else
			return false;
			//return "CS Application failed.";

			break;
			
			case "Update":
				$qryIns = "Update tblTk_CsApp set dateFiled='".date("Y-m-d", strtotime($array["dateFiled"]))."', 
							csDateTo='".date("Y-m-d", strtotime($array["csDateTo"]))."', 
							csShiftToIn='".$array["csTimeIn"]."',csHiftToOut='".$array["csTimeOut"]."',
							csReason='".strtoupper(htmlspecialchars(addslashes($array["cmbReasons"])))."', 
							dateUpdated='".date("Y-m-d")."',updatedBy='".$_SESSION["employee_number"]."', 
							crossDay=".($array["chkCrossDay"]!=""?"'Y'":"NULL")." where seqNo='".$array["inputTypeSeqNo"]."'
							";
			if($this->execQry($qryIns))
			//return "CS Application successfully saved.";
			return true;
		else
			return false;
			//return "CS Application failed.";
			break;
		}
		
		
	}
	
	
	function validateTran_Cd($array, $action)
	{
		
		$dateTIn = $array["csDateTo"]." ".$array["csTimeIn"];
		$dateTOut = $array["csDateTo"]." ".$array["csTimeOut"];
		$shiftOut = $array["csDateTo"]." ".$array["schedTimeOut"];
		
		$getPdNum_NxtCutOff = $this->getTblData("tblPayPeriod", " and payGrp='".$array["empPayGrp"]."' and payCat='".$array["empPayCat"]."'  and  pdTSStat='O' ", "", "sqlAssoc");
		
		if($getPdNum_NxtCutOff["pdNumber"] == 24)
		{
			$pdNum = '1';
			$pdYear = $getPdNum_NxtCutOff["pdYear"] + 1;
		}
		else
		{
			$pdNum = $getPdNum_NxtCutOff["pdNumber"]+1;
			$pdYear =  $getPdNum_NxtCutOff["pdYear"];
		}
			
		$chkPayPeriod =  $this->getTblData("tblPayPeriod", " and payGrp='".$array["empPayGrp"]."' and payCat='".$array["empPayCat"]."' and '".date('Y-m-d',strtotime($array["csDateTo"]))."' between pdFrmDate and pdToDate and pdTSStat='O' ", "", "sqlAssoc");
		
		if($chkPayPeriod["pdSeries"]!="")
		{
			
			if(((strtotime($dateTOut))<=(strtotime($dateTIn)))&&($array["chkCrossDay"]==""))
			{
				return "CS Time Out should not be less than or equal to CS Time In.";
			
			}
			else
			{
				$arr_CsRec = $this->getTblData("tblTK_CSApp", " and empNo='".$array["txtAddEmpNo"]."' and csDateFrom='".date("Y-m-d", strtotime($array["csDateFrom"]))."' and csStat='P'", "", "sqlAssoc");
		
				if($arr_CsRec["empNo"]!="")
				{
					return "Transaction already exist.";
					
				}
				else
				{
					$arr_getDayType = $this->detDayType(date("Y-m-d", strtotime($array["csDateTo"])), " and brnCode in ('".$array["empbrnCode"]."','0')");
				
					if($array["shiftDayType"]=='01')
					{
						if($arr_getDayType["dayType"]!="")
							return "Invalid Change Shift To Date : Selected Change Shift To Date is set as a Holiday.";
						else
						{
							//$insRecCsTran = $this->tran_Cs($array,$action);
							//return $insRecCsTran;
							$insRecCsTran = $this->tran_Cs($array,$action);
							if($insRecCsTran){
								return true;
							}
							else{
								return false;	
							}

						}
					}
					else
					{
						
						//$insRecCsTran = $this->tran_Cs($array,$action);
						//return $insRecCsTran;
						$insRecCsTran = $this->tran_Cs($array,$action);
						if($insRecCsTran){
							return true;	
						}
						else{
							return false;	
						}
					}
					
				}
			}
			
		}
		else
		{
			$getPdNum_Open2 = $this->getTblData("tblPayPeriod", " and payGrp='".$array["empPayGrp"]."' and payCat='".$array["empPayCat"]."'   and '".date('Y-m-d',strtotime($array["csDateTo"]))."' between pdFrmDate and pdToDate and pdNumber='".$pdNum."' and pdYear='".$pdYear."' ", "", "sqlAssoc");
			if($getPdNum_Open2["pdSeries"]!="")
			{
				if(((strtotime($dateTOut))<=(strtotime($dateTIn)))&&($array["chkCrossDay"]==""))
					return "CS Time Out should not be less than or equal to CS Time In.";
				else
				{
					$arr_getDayType = $this->detDayType(date("Y-m-d", strtotime($array["csDateTo"])), " and brnCode in ('".$array["empbrnCode"]."','0')");
				
					if($array["shiftDayType"]=='01')
					{
						if($arr_getDayType["dayType"]!="")
							return "Invalid Change Shift To Date : Selected Change Shift To Date is set as a Holiday.";
						else
						{
							//$insRecCsTran = $this->tran_Cs($array,$action);
							//return $insRecCsTran;
							$insRecCsTran = $this->tran_Cs($array,$action);
							if($insRecCsTran){
								return true;	
							}
							else{
								return false;	
							}
						}
					}
					else
					{
						
						//$insRecCsTran = $csObj->tran_Cs($array,$action);
						//return $insRecCsTran;
						$insRecCsTran = $this->tran_Cs($array,$action);
						if($insRecCsTran){
							return true;	
						}
						else{
							return false;	
						}
					}
				}
			}
			else
			{
				return "Selected CS Schedule is not part of the Current nor the Advance Cut Off.";
			}
		}
	}
	
	/*Module Name : CRD Application 09/04/2010 2:33PM Saturday*/
	function tran_Crd($array,$action)
	{
		$arr_lastRefNo = $this->getLastRefNo("crdrefNo");
		$lastRefNo = $arr_lastRefNo["lastRefNo"] + 1;
		$i = $array['rdnTran'];
		switch($action)
		{
			case "Add":
				$qryIns = "Insert into tblTK_ChangeRDApp(compCode,empNo,refNo,dateFiled,
							tsAppTypeCd,cRDDateFrom,cRDDateTo,cRDReason,dateAdded,addedBy,cRDStat)
						    values('".$_SESSION["company_code"]."','".$array["txtAddEmpNo"]."','".$lastRefNo."','".date("Y-m-d", strtotime($array["dateFiled"]))."',
							'01','".date("Y-m-d", strtotime($array["rdDateFrom$i"]))."','".date("Y-m-d", strtotime($array["rdDateTo$i"]))."',
						    '".strtoupper(htmlspecialchars(addslashes($array["cmbReasons$i"])))."'
						   ,'".date("Y-m-d")."','".$_SESSION["employee_number"]."','H')";


		 if($this->execQry($qryIns))
			if($this->updateLastRefNo($lastRefNo,"crdrefNo")){
				return "Change Restday Application successfully saved.";
			}

			
		else
			return "Change Restday Application failed.";
			break;
			
			case "Update":
				$qryIns = "Update tblTK_ChangeRDApp set dateFiled='".date("Y-m-d", strtotime($array["dateFiled"]))."', 
							cRDDateTo='".date("Y-m-d", strtotime($array["rdDateTo"]))."', cRDReason='".strtoupper(htmlspecialchars(addslashes($array["cmbReasons"])))."',
							dateUpdated='".date("Y-m-d")."', updatedBy='".$_SESSION["employee_number"]."' 
							where seqNo='".$array["inputTypeSeqNo"]."'";
				if($this->execQry($qryIns))
			return "Change Restday Application successfully saved.";
		else
			return "Change Restday Application failed.";
			break;
		}
		
		
	}
	
	/*Module Name : Change Employee Shift Application 09/06/2010 Monday*/
	function tran_ChngeEmpShft($qryString,$action, $array)
	{
		$Trns = $this->beginTran();
		switch($action)
		{
			case "Update":
			$a = explode('&', $qryString);
			$i = 0;
			while ($i < count($a)) {
				$b = split('=', $a[$i]);
				if(substr(htmlspecialchars(urldecode($b[0])),0,13)=='chkDayEnabled')
				{
					$arr_EmpTsInfo =  $this->getTblData("tblTK_Timesheet", " and empNo='".$array['txtAddEmpNo']."' and tsDate='".date("Y-m-d", strtotime($array["txttsDate".substr(htmlspecialchars(urldecode($b[0])),13)]))."'", "", "sqlAssoc");
					
					$qryIns = "Insert into tblTK_ScheduleHist(compCode,empNo,tsDate,dayType,shftTimeIn,shftLunchOut,
									shftLunchIn,shftBreakOut,shftBreakIn,shftTimeOut, tsAppTypeCd, crossDay, dateEdited,editedBy)
							   values('".$_SESSION["company_code"]."','".$array["txtAddEmpNo"]."', 
							   		'".date("Y-m-d", strtotime($array["txttsDate".substr(htmlspecialchars(urldecode($b[0])),13)]))."',
									'".$arr_EmpTsInfo["dayType"]."', '".$arr_EmpTsInfo["shftTimeIn"]."', 
									'".$arr_EmpTsInfo["shftLunchOut"]."', '".$arr_EmpTsInfo["shftLunchIn"]."', 
									'".$arr_EmpTsInfo["shftBreakOut"]."','".$arr_EmpTsInfo["shftBreakIn"]."',
									'".$arr_EmpTsInfo["shftTimeOut"]."', ".($arr_EmpTsInfo["crossDay"]=='Y'?"'Y'":"NULL").",
									".($arr_EmpTsInfo["tsAppTypeCd"]!=""?"'".$arr_EmpTsInfo["tsAppTypeCd"]."'":"NULL").", 
									'".date("Y-m-d")."', '".$_SESSION["employee_number"]."');";
					if($Trns){
						$Trns = $this->execQry($qryIns);	
					}				
					//Get DayType
					$arr_getDayType = $this->detDayType(date("Y-m-d", strtotime($array["txttsDate".substr(htmlspecialchars(urldecode($b[0])),13)])), " and brnCode in ('".$array["empBrnCode"]."','0')");
					
					
						if(($array["restDayTag".substr(htmlspecialchars(urldecode($b[0])),13)]=='Y') && (($array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00')))
						{	
							if($arr_getDayType["dayType"]=='03')
								$dayType = '05';
							elseif($arr_getDayType["dayType"]=='04')
								$dayType = '06';
							else
								$dayType = '02';
						}
						elseif(($array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00'))
						{
							if($arr_getDayType["dayType"]=='03')
								$dayType = '05';
							elseif($arr_getDayType["dayType"]=='04')
								$dayType = '06';
							elseif($arr_getDayType["dayType"]=='03')
								$dayType = '03';
							elseif($arr_getDayType["dayType"]=='04')
								$dayType = '04';
							else
								$dayType = '02';
						}
						else
						{
							if($arr_getDayType["dayType"]=='03')
								$dayType = '03';
							elseif($arr_getDayType["dayType"]=='04')
								$dayType = '04';
							else
								$dayType = '01';
						}
					
					$qryUpd = "Update tblTK_Timesheet set dayType='".$dayType."', 
									shftTimeIn='".$array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftLunchOut='".$array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftLunchIn='".$array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftBreakOut='".$array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftBreakIn='".$array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftTimeOut='".$array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									dateEdited='".date("Y-m-d")."', editedBy='".$_SESSION["employee_number"]."',
									tsAppTypeCd=".($array["delAppTypeCd"]=='Yes'?"NULL":($arr_EmpTsInfo["tsAppTypeCd"]!=""?"'".$arr_EmpTsInfo["tsAppTypeCd"]."'":"NULL")).",
									crossDay=".($array["chkCrossDay".substr(htmlspecialchars(urldecode($b[0])),13)]!=""?"'Y'":"NULL")."
							  where compCode='".$_SESSION["company_code"]."' and empNo='".$array["txtAddEmpNo"]."'
							  		and tsDate='".date("Y-m-d", strtotime($array["txttsDate".substr(htmlspecialchars(urldecode($b[0])),13)]))."';";
					if($Trns){
						$Trns = $this->execQry($qryUpd);	
					}				
				}		
				$i++;
			}					
			break;
		}
		
//			$qryStatements = $qryIns.$qryUpd;
		if(!$Trns){
			$Trns = $this->rollbackTran();	
			return "Change Employee Shift into Timesheet failed.";	
		}
		else{
			$Trns = $this->commitTran();
			return "Change Employee Shift into Timesheet successfully updated.";
		}	
//		if($this->execQry($qryStatements))
//			return "Change Employee Shift into Timesheet successfully updated.";
//		else
//			return "Change Employee Shift into Timesheet failed.";
	}

	/*Module Name : Update Employee Shift [Update all same shift] 08/06/2023 Sunday*/
	function tran_BulkChngeEmpShft($qryString,$action, $array)
	{
		$Trns = $this->beginTran();
		switch($action)
		{
			case "Update":
			$a = explode('&', $qryString);
			$i = 0;
			while ($i < count($a)) {
				$b = split('=', $a[$i]);
				if(substr(htmlspecialchars(urldecode($b[0])),0,13)=='chkDayEnabled')
				{
					$arr_EmpTsInfo =  $this->getTblData("tblTK_Timesheet", " and empNo='".$array['txtAddEmpNo']."' and tsDate='".date("Y-m-d", strtotime($array["txttsDate".substr(htmlspecialchars(urldecode($b[0])),13)]))."'", "", "sqlAssoc");
					
					$qryIns = "Insert into tblTK_ScheduleHist(compCode,empNo,tsDate,dayType,shftTimeIn,shftLunchOut,
									shftLunchIn,shftBreakOut,shftBreakIn,shftTimeOut, tsAppTypeCd, crossDay, dateEdited,editedBy)
							   values('".$_SESSION["company_code"]."','".$array["txtAddEmpNo"]."', 
							   		'".date("Y-m-d", strtotime($array["txttsDate".substr(htmlspecialchars(urldecode($b[0])),13)]))."',
									'".$arr_EmpTsInfo["dayType"]."', '".$arr_EmpTsInfo["shftTimeIn"]."', 
									'".$arr_EmpTsInfo["shftLunchOut"]."', '".$arr_EmpTsInfo["shftLunchIn"]."', 
									'".$arr_EmpTsInfo["shftBreakOut"]."','".$arr_EmpTsInfo["shftBreakIn"]."',
									'".$arr_EmpTsInfo["shftTimeOut"]."', ".($arr_EmpTsInfo["crossDay"]=='Y'?"'Y'":"NULL").",
									".($arr_EmpTsInfo["tsAppTypeCd"]!=""?"'".$arr_EmpTsInfo["tsAppTypeCd"]."'":"NULL").", 
									'".date("Y-m-d")."', '".$_SESSION["employee_number"]."');";
					if($Trns){
						$Trns = $this->execQry($qryIns);	
					}				
					//Get DayType
					$arr_getDayType = $this->detDayType(date("Y-m-d", strtotime($array["txttsDate".substr(htmlspecialchars(urldecode($b[0])),13)])), " and brnCode in ('".$array["empBrnCode"]."','0')");
					
						if(($array["restDayTag".substr(htmlspecialchars(urldecode($b[0])),13)]=='Y') && (($array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00')))
						{
							if($arr_getDayType["dayType"]=='03')
								$dayType = '05';
							elseif($arr_getDayType["dayType"]=='04')
								$dayType = '06';
							else
								$dayType = '02';
						}
						elseif(($array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00') && ($array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='00:00' || $array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]=='0:00'))
						{
							if($arr_getDayType["dayType"]=='03')
								$dayType = '05';
							elseif($arr_getDayType["dayType"]=='04')
								$dayType = '06';
							elseif($arr_getDayType["dayType"]=='03')
								$dayType = '03';
							elseif($arr_getDayType["dayType"]=='04')
								$dayType = '04';
							else
								$dayType = '02';
						}
						else
						{
							if($arr_getDayType["dayType"]=='03')
								$dayType = '03';
							elseif($arr_getDayType["dayType"]=='04')
								$dayType = '04';
							else
								$dayType = '01';
						}

						//select all employee with same shift
						$branchCode = $array["empBrnCode"];
						$shiptList =  $this->getTblData("tbltk_empshift", " and empNo='".$array['txtAddEmpNo']."' and compCode='" . $_SESSION["company_code"] . "'", "", "sqlAssoc");
						$shiftCode = $shiptList['shiftCode'];
						$employees = $this->getAllEmployeeWithSameShift($shiftCode, $branchCode);

						//echo $branchCode . " == " . $shiftCode . "<br><br>";

						//echo var_dump($employees);

						//foreach loop then do the update
						foreach ($employees as $employee) {

							//echo $employee["empNo"] . " == " . $employee["shiftCode"] . "<br><br>";

							$qryUpd = "Update tblTK_Timesheet set dayType='".$dayType."', 
									shftTimeIn='".$array["txtEtimeIn".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftLunchOut='".$array["txtElunchOut".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftLunchIn='".$array["txtElunchIn".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftBreakOut='".$array["txtEbrkOut".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftBreakIn='".$array["txtEbrkIn".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									shftTimeOut='".$array["txtEtimeOut".substr(htmlspecialchars(urldecode($b[0])),13)]."',
									dateEdited='".date("Y-m-d")."', editedBy='".$_SESSION["employee_number"]."',
									tsAppTypeCd=".($array["delAppTypeCd"]=='Yes'?"NULL":($arr_EmpTsInfo["tsAppTypeCd"]!=""?"'".$arr_EmpTsInfo["tsAppTypeCd"]."'":"NULL")).",
									crossDay=".($array["chkCrossDay".substr(htmlspecialchars(urldecode($b[0])),13)]!=""?"'Y'":"NULL")."
									where compCode='".$_SESSION["company_code"]."' and empNo='".$employee["empNo"]."'
									and tsDate='".date("Y-m-d", strtotime($array["txttsDate".substr(htmlspecialchars(urldecode($b[0])),13)]))."';";
							if($Trns){
								$Trns = $this->execQry($qryUpd);
							}
						}
						//end end foreach
				}		
				$i++;
			}					
			break;
		}
		
		if(!$Trns){
			$Trns = $this->rollbackTran();	
			return "Change Employee Shift into Timesheet failed.";	
		}
		else{
			$Trns = $this->commitTran();
			return "Change Employee Shift into Timesheet successfully updated.";
		}
	}

	/*Module Name : Update Employee Shift [Update all same shift] 08/06/2023 Sunday*/
	function getAllEmployeeWithSameShift($shiftCode, $branchCode){
		$qry = "SELECT tblemp.empBrnCode, tblemp.empLastName, tblemp.empFirstName, empShip.* FROM tbltk_empshift empShip
				LEFT OUTER JOIN tblempmast_new tblemp
				ON  tblemp.empNo = empShip.empNo
								WHERE empShip.compCode = '{$_SESSION['company_code']}'
								AND empShip.shiftCode = '{$shiftCode}'
				AND tblemp.empBrnCode = '{$branchCode}'";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	/*Module Name : Mass Update Shift Schedule 09/15/2010 Wednesday*/
	function MassUpdateSchedule($brnCodelist,$where_empStat,$user_payCat_view,$array)
	{
		$qryUpd = "Update ";
	}
	
	function getRestday($empno,$compcode){
		$qry = "Select tsDate from tblTK_Timesheet where (dayType='02' or dayType='05' or dayType='06') and compcode='{$compcode}' and empNo='{$empno}'";
		$resQry = $this->execQry($qry);
		return $this->getArrRes($resQry);			
	}
	
	function manualEntryTimeSheet($action, $array)
	{
		$Trns = $this->beginTran();
		$time[1] = $array["txtEtimeIn"];
		$time[2] = $array["txtElunchOut"];
		$time[3] = $array["txtElunchIn"];
		$time[4] = $array["txtEbrkOut"];
		$time[5] = $array["txtEbrkIn"];
		$time[6] = $array["txtEtimeOut"];
		
		for($ctr=1; $ctr<=6; $ctr++)
		{
			$time[$ctr] = str_replace(" ","",stripslashes($time[$ctr]));
			
			if(($time[$ctr]=="") || ($time[$ctr]==":"))
				$time[$ctr] = "NULL";
			else
				$time[$ctr] = "'".$time[$ctr]."'";
			
			
		}
		
		switch($action)
		{
			case "Add":
				$Qry_TsCorr = "Insert into tblTK_TimeSheetCorr(compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, 
									breakOut,breakIn, timeOut, crossTag, editReason, encodeDate, encodedBy, stat, logsExceeded) 
							   values('".$_SESSION["company_code"]."', '".$array["empNo"]."', 
							   		'".date('Y-m-d', strtotime($array["txttsDate"]))."', ".$time[1].", 
							   		".$time[2].", ".$time[3].", ".$time[4].", ".$time[5].",  ".$time[6].", 
									".($array["txtCheckTag"]!=""?"'Y'":"NULL").", 
									".($array["violationCd"]==0?"NULL":"'".$array["violationCd"]."'").", 
									'".date("Y-m-d")."', '".$_SESSION['employee_number']."', 'A', 
									".($array["txtlogsExceeded"]=='Y'?"'Y'":"NULL").");";
				if($Trns){
					$Trns = $this->execQry($Qry_TsCorr);	
				}						
			break;
			
			case "Update":
				$arr_EmpTsInfo =  $this->getTblData("tblTK_TimeSheetCorr", " and empNo='".$array["empNo"]."' and tsDate = '".date("Y-m-d", strtotime($array["txttsDate"]))."'", " ", "sqlAssoc");

				$Qry_TsCorrHist = "Insert into tblTK_TimeSheetCorr_original(compCode, empNo, tsDate, timeIn, lunchOut, lunchIn, 
									breakOut,breakIn, timeOut, crossTag, editReason, encodeDate, encodedBy, stat, logsExceeded) 
								values('".$_SESSION["company_code"]."', '".$array["empNo"]."', 
									'".date("Y-m-d", strtotime($arr_EmpTsInfo["tsDate"]))."', 
									".($arr_EmpTsInfo["timeIn"]!=""?"'".$arr_EmpTsInfo["timeIn"]."'":"NULL").", 
									".($arr_EmpTsInfo["lunchOut"]!=""?"'".$arr_EmpTsInfo["lunchOut"]."'":"NULL").", 
									".($arr_EmpTsInfo["lunchIn"]!=""?"'".$arr_EmpTsInfo["lunchIn"]."'":"NULL").", 
									".($arr_EmpTsInfo["breakOut"]!=""?"'".$arr_EmpTsInfo["breakOut"]."'":"NULL").", 
									".($arr_EmpTsInfo["breakIn"]!=""?"'".$arr_EmpTsInfo["breakIn"]."'":"NULL").",  
									".($arr_EmpTsInfo["timeOut"]!=""?"'".$arr_EmpTsInfo["timeOut"]."'":"NULL").", 
									".($arr_EmpTsInfo["crossTag"]!=""?"'Y'":"NULL").", 
									".($arr_EmpTsInfo["editReason"]==0?"NULL":"'".$arr_EmpTsInfo["editReason"]."'").", 
									'".date("Y-m-d", strtotime($arr_EmpTsInfo["encodeDate"]))."', 
									'".$arr_EmpTsInfo["encodedBy"]."', '".$arr_EmpTsInfo["stat"]."', 
									".($arr_EmpTsInfo["logsExceeded"]=='Y'?"'".$arr_EmpTsInfo["logsExceeded"]."'":"NULL").");";

				$Qry_TsCorr = "Update tblTK_TimeSheetCorr set timeIn=".$time[1].", lunchOut=".$time[2].", lunchIn=".$time[3].",
									breakOut=".$time[4].", breakIn=".$time[5].", timeOut=".$time[6].", 
									crossTag=".($array["txtCheckTag"]!=""?"'Y'":"NULL").", 
									editReason=".($array["violationCd"]==0?"NULL":"'".$array["violationCd"]."'").", 
									encodeDate='".date("Y-m-d")."', encodedBy='".$_SESSION['employee_number']."',
									logsExceeded = NULL
							   where compCode='".$_SESSION["company_code"]."' and empNo='".$array["empNo"]."'
							   		and tsDate='".date("Y-m-d", strtotime($array["txttsDate"]))."';";
				if($Trns){
					$Trns = $this->execQry($Qry_TsCorrHist);	
					$Trns = $this->execQry($Qry_TsCorr);	
				}						
			break;
			
			
			default:
			break;
			
		}
		//return $this->execQry($Qry_TsCorr);
		if(!$Trns){
			$Trns = $this->rollbackTran();	
			return false;	
		}
		else{
			$Trns = $this->commitTran();
			return true;
		}	
	}
	
	function saveManagersAttendance($arr){
		$period = $this->getTblData("tblPayPeriod", " and payGrp='1' and payCat = '3' and pdStat IN ('O','')", " ", "sqlAssoc");

		$insertQry = "Insert into tblTK_ManagersAttendance
		 			  	(compcode, empNo, brnCode, userAdded, dateAdded, maxLateTime, LateInImins, LateRemaining, Period)
					  Values('".$_SESSION['company_code']."','".$arr['txtAddEmpNo']."',
					  	'".$arr['hdnBranch']."',
					  	'".$_SESSION['employee_number']."',
						'".date("Y-m-d")."', 
						300, 
						0,
						300,
						".$period['pdNumber'].")";
		if ($this->execQry($insertQry)) {
			return true;
		}
		else{
			return false;	
		}
	}
}

?>