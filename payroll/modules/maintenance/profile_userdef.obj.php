<?
	/*
		$col_label 	= Labels (first column of the table)
		$col_field 	= katumbas na field in tblUserDefinedMast
		$order_by 	= order of records
		$input_type = appropriate input types for the field 
		
		List of Input Types
		1 = Textbox
		2 = ComboBox
		3 = Date Textbox
		4 = Textarea
		
		$col_labelval = katumbas n Cat Code ng Cat Desc of tblUserDefRef
		
		$empNo		= Employee No.
		$fieldName	= List of corresponding fields.
		$catCode	= CatCode
		$orderBy	= Order by
		
		$inputCd	= Based on Input Types
		$inputName	= Name of the Input
		$action		= Either Add / Edit
		$rowFields	= katumbas na field sa tblUserDefinedMst
		$recNo		= auto increment in tblUserDefinedMst
		$seqId		= auto increment in tblUserDefLookUp
		
	*/
	class mainUserDefObj extends commonObj
	{
		
		function getNotInc($empNo)
		{
			$qrygetNotInc = 	"SELECT  catCode,catDesc
								FROM  tblUserDefinedRef
								WHERE (catCode NOT IN
							   (SELECT DISTINCT catCode
								FROM  tblUserDefinedMst
								WHERE empNo = '".$empNo."'))
								ORDER BY catDesc";
			$resgetNotInc = $this->execQry($qrygetNotInc);
			return $this->getArrRes($resgetNotInc);
			
		}
		
		
		function getListUsrDefRef($empNo)
		{
			$qryGetList = 		"SELECT DISTINCT a.catCode, b.catDesc
								FROM    tblUserDefinedMst a INNER JOIN tblUserDefinedRef b 
								ON a.catCode = b.catCode
								WHERE empNo='".$empNo."'
								GROUP BY a.catCode, b.catDesc
								ORDER BY b.catDesc";
			$resUserDefRef = $this->execQry($qryGetList);
			return $resUserDefRef;
		}
		
		function getUserDef_ColumnName($col_labelval)
		{
			//Compensation and Benefits
			if($col_labelval=='7')
			{
				$col_label = "Date Performed,Employer Action,Incentive Type,Effectivity Date";
				$col_field = "date1,remarks1,remarks2,date2";
				$order_by = "date1 DESC";
				$input_type = "3,2,2,3";
			}	
			
			//Dependents Information
			if($col_labelval=='12')
			{
				$col_label = "Dependents Name, Relationship, Address, Birthday";
				$col_field = "remarks2,remarks1,remarks3,date1";
				$order_by = "remarks2";
				$input_type = "1,1,4,3";
			}	
			
			//Disciplinary Action / Conduct
			if($col_labelval=='9')
			{
				$col_label = "Employee Action, Penalty, Days of Suspension, Effectivity Date";
				$col_field = "remarks1,remarks2,remarks3,date1";
				$order_by = "date1 DESC";
				$input_type = "2,2,1,3";
			}		
			
			//Educational Background
			if($col_labelval=='1')
			{
				$col_label = "Type, School Name, Date Started, Date Completed";
				$col_field = "remarks1,remarks2,date1,date2";
				$order_by = "date1 DESC";
				$input_type = "2,2,3,3";
			}		
			
			//Employment Data History
			if($col_labelval=='11')
			{
				$col_label = "Company Name, Position, Start Date, End Date";
				$col_field = "remarks1,remarks2,date1,date2";
				$order_by = "date1 DESC";
				$input_type = "1,1,3,3";
			}	
			
			//Language Information
			if($col_labelval=='10')
			{
				$col_label = "Language Description";
				$col_field = "remarks2";
				$order_by = "remarks2";
				$input_type = "2";
			}	
			
			//Licenses
			if($col_labelval=='2')
			{
				$col_label = "License Number, License Name, Date Obtained, Expiry Date";
				$col_field = "remarks2,remarks1,date1,date2";
				$order_by = "date1 DESC";
				$input_type = "1,1,3,3";
			}		
			
			//Medical Records
			if($col_labelval=='8')
			{
				$col_label = "Date Examined, Findings, Condition, Remarks";
				$col_field = "date1,remarks1,remarks2,remarks3";
				$order_by = "date1 DESC";
				$input_type = "3,4,4,4";
			}	
			
			//Organization and Membership
			if($col_labelval=='3')
			{
				$col_label = "Organization Name, Position, Date Joined, Date End";
				$col_field = "remarks1,remarks2,date1,date2";
				$order_by = "date1 DESC";
				$input_type = "1,1,3,3";
			}		
			
			//Performance Records / Appraisal / Evaluation
			if($col_labelval=='6')
			{
				$col_label = "Description, Incentive, Start Date, End Date";
				$col_field = "remarks1,remarks2,date1,date2";
				$order_by = "date1 DESC";
				$input_type = "4,2,3,3";
			}		
			
			//Property Accountability
			if($col_labelval=='5')
			{
				$col_label = "Property Name, Date Issued, Qty, Remarks";
				$col_field = "remarks1,date1,remarks2,remarks3";
				$order_by = "date1 DESC";
				$input_type = "4,3,1,4";
			}	
			
			//Training Records
			if($col_labelval=='4')
			{
				$col_label = "Training Name, Location, Date Started, Date Finished";
				$col_field = "remarks1,remarks2,date1,date2";
				$order_by = "date1 DESC";
				$input_type = "4,4,3,3";
			}	
			
			//BlackList Information
			if($col_labelval=='13')
			{
				$col_label = "Blacklist the Employee,Date,Reason";
				$col_field = "remarks1,date1,remarks2";
				$order_by = "date1 DESC";
				$input_type = "2,3,4";
			}			
			
			$explode_res = $col_label."|".$col_field."|".$order_by."|".$input_type;
			return $explode_res;
		}
		
		function UserDefMast_Con($empNo,$fieldName,$catCode,$orderBy)
		{
			$qryUserDefMast		= 	"SELECT  $fieldName,recNo
									 FROM    tblUserDefinedMst
									 WHERE    empNo='".$empNo."' and (catCode='".$catCode."')
									 ORDER BY $orderBy";
			$resUserDefMast = $this->execQry($qryUserDefMast);
			return $resUserDefMast;
		}
		
		function form_input($inputCd,$inputName,$action,$rowFields)
		{
			
			if($inputCd=='1')
			{
				$input_type = "<input type='text' style='width:95%;' class='inputs' name='".str_replace(" ","",$inputName)."' id='".str_replace(" ","",$inputName)."' value='".($action=='Edit'?$this->getTblUserDefMast($_GET["recNo"],$rowFields):"")."'>";
			}
			
			if($inputCd=='2')
			{
				if($action=='Edit') 
				{
					$val_recordset = $this->getTblUserDefMast($_GET["recNo"],$rowFields);
					$input_type = $this->optionList(str_replace(" ","",$inputName),$val_recordset);
				}
				else
				{
					$input_type = $this->optionList(str_replace(" ","",$inputName),'');
				}
			}
			
			if($inputCd=='3')
			{
				$input_type="<input type='text' style='width:40%;' class='inputs' name='".str_replace(" ","",$inputName)."' id='".str_replace(" ","",$inputName)."' maxLength='10' readonly size='10' value='".($action=='Edit'?$this->getTblUserDefMast($_GET["recNo"],$rowFields):date("m/d/Y"))."'/>"; 
				$input_type.="<a href='#'>";
	    		$input_type.="<img class='btnClendar' name='img".str_replace(" ","",$inputName)."' id='img".str_replace(" ","",$inputName)."' type='image' src='../../../images/cal_new.gif'>";  
			}
			
			if($inputCd=='4')
			{
				$input_type="<textarea name='".str_replace(" ","",$inputName)."' id='".str_replace(" ","",$inputName)."' class='inputs'  style='width:95%;' cols='19' rows='2'>".($action=='Edit'?$this->getTblUserDefMast($_GET["recNo"],$rowFields):"")."</textarea>";
			}
			
			
			
			return $input_type;
		}
		
		function optionList($inputName,$row_fields)
		{
			switch($inputName)
			{
				case "EmployerAction":
					$where = "EmpAction";
				break;
				
				case "IncentiveType":
					$where = "IncentiveType";
				break;
				
				case "EmployeeAction":
					$where = "Articles";
				break;
				
				case "Penalty":
					$where = "Penalty";
				break;
				
				case "Type":
					$where = "EducType";
				break;
				
				case "SchoolName":
					$where = "SchoolName";
				break;
				
				case "Incentive":
					$where = "IncentiveType";
				break;
				
				case "LanguageDescription";
					$where = "Language";
				break;
			}
			
			
			if($where!="")
			{
				$qry = "SELECT * FROM tblUserDefLookUp WHERE type='".$where."'";
				$resqry = $this->execQry($qry);
				$input_type="<select class='inputs' style='width:95%;' name='".str_replace(" ","",$inputName)."'";
				while($row = mysql_fetch_array($resqry))
				{
					$input_type.="<option value='".$row["seqId"]."' '".($row_fields==$row["seqId"]?'selected':'')."'>".$row["typeDesc"]."</option>";
				}
				$input_type.="</select>";
			}
			
			if($inputName=='BlacklisttheEmployee')
			{
				
				$input_type="<select class='inputs' style='width:95%;' name='".str_replace(" ","",$inputName)."'";
					$input_type.="<option value='Y' '".($row_fields=="Y"?'selected':'')."'>Yes</option>";
					$input_type.="<option value='N' '".($row_fields=="N"?'selected':'')."'>No</option>";
				$input_type.="</select>";
			}
			return $input_type;
		}
		
		function chk_ConUserDef($col_field,$catCode,$empNo,$empCompCode,$val)
		{
			$qry_Chk = "SELECT $col_field FROM tblUserDefinedMst where empNo='".$empNo."' and catCode='".$catCode."' and compCode='".$empCompCode."' and $val";
			$res_qry_Chk = $this->execQry($qry_Chk);
			
			return $this->getRecCount($res_qry_Chk);
		}
		
		function addEmp_Info($col_field,$catCode,$empNo,$empCompCode,$val)
		{
			$qry_ins = "INSERT INTO tblUserDefinedMst(empNo,compCode,catCode,$col_field)
							VALUES ('".$empNo."','".$empCompCode."','".$catCode."',$val)";
			
			$res_ins = $this->execQry($qry_ins);
			if($res_ins){
				return true;
			}
			else{
				return false;
			}
			
		}
		
		function getTblUserDefMast($recNo,$rowFields)
		{
			 $qry_getUserDefMst = "SELECT $rowFields FROM tblUserDefinedMst where recNo='".$recNo."'";
			 $res_getContent = $this->execQry($qry_getUserDefMst);
			 $row_getContent = $this->getSqlAssoc($res_getContent);
			
			 if(($rowFields=='date1')||($rowFields=='date2'))
			 {
			 	$row_getContent_val = date("m/d/Y", strtotime($row_getContent[$rowFields]));
			 }
			 else
			 { 
			 	$row_getContent_val = $row_getContent[$rowFields];
			 }
			 
			 return $row_getContent_val;
		}
		
		function UpdateTblDefMast($val)
		{
			$qry_update = "UPDATE tblUserDefinedMst set $val where recNo='".$_GET["tblUserDef_RecNo"]."'";
			$res_update = $this->execQry($qry_update);
			if($res_update){
				return true;
			}
			else{
				return false;
			}
		}
		
		function chk_AgainCon($col_field,$catCode,$empNo,$empCompCode,$val)
		{
			$qry_AgainChk = "SELECT $col_field FROM tblUserDefinedMst where empNo='".$empNo."' and catCode='".$catCode."' and compCode='".$empCompCode."' AND $val AND recNo='".$_GET["tblUserDef_RecNo"]."'";
			$res_qry_AgainChk = $this->execQry($qry_AgainChk);
			
			return $this->getRecCount($res_qry_AgainChk);
		}
		
		function del_UserDefMstRec($recNo)
		{
			$qry_DelRec = "Delete from tblUserDefinedMst where recNo='".$recNo."'";
			$res_DelRec = $this->execQry($qry_DelRec);
			if($res_DelRec)
				return true;
			else
				return false;
		}
		
		function tblLookUp($inputName,$seqId)
		{
			switch($inputName)
			{
				case "EmployerAction":
					$where = "EmpAction";
				break;
				
				case "IncentiveType":
					$where = "IncentiveType";
				break;
				
				case "EmployeeAction":
					$where = "Articles";
				break;
				
				case "Penalty":
					$where = "Penalty";
				break;
				
				case "Type":
					$where = "EducType";
				break;
				
				case "SchoolName":
					$where = "SchoolName";
				break;
				
				case "Incentive":
					$where = "IncentiveType";
				break;
				
				case "LanguageDescription";
					$where = "Language";
				break;
				
				default:
					$where = "";
				break;
			}
			
			if($where!="")
			{
				$qry = "SELECT * FROM tblUserDefLookUp WHERE type='".$where."' and seqId='".$seqId."'";
				$resqry = $this->execQry($qry);
				$rowqry = $this->getSqlAssoc($resqry);
				$rowqry = $rowqry["typeDesc"];
			}
			else
			{
				if($inputName=='BlacklisttheEmployee')
				{
					$rowqry = ($seqId=='Y'?"Yes":"No");
				}
				else
				{
					$rowqry = "";
				}
			}	
			
			return $rowqry;
		}
		
		function getDate() 
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
			return $newdate;
		}
	}
?>