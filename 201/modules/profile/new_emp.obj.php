<?
include("../../../includes/db.inc.php");
include("../../../includes/201common.php");
session_start();

switch ($_GET['code']) {
	case "cddivision":
			$common=new commonObj();
			$pos = $common->getpositionwil(" and posCode='{$_GET['posCode']}'",2);
			$Div = $common->getDivDescArt($_GET['company_code'],$pos['divCode']);
			$Dept = $common->getDeptDescArt($_GET['company_code'], $pos['divCode'],$pos['deptCode']);
			$Sect = $common->getSectDescArt($_GET['company_code'], $pos['divCode'],$pos['deptCode'],$pos['sectCode']);
			echo "$('txtDiv').value='{$pos['divCode']}';";
			echo "$('txtDept').value='{$pos['deptCode']}';";
			echo "$('txtSect').value='{$pos['sectCode']}';";
			echo "$('txtRank').value='{$pos['rank']}';";
			echo "$('txtLevel').value='{$pos['level']}';";
			echo "$('divdivision').innerHTML='{$Div['deptDesc']}';";
			echo "$('divdpt').innerHTML='{$Dept['deptDesc']}';";
			echo "$('divsection').innerHTML='{$Sect['deptDesc']}';";
			echo "$('dvrank').innerHTML='Rank {$pos['rank']}';";
			echo "$('dvlevel').innerHTML='Level {$pos['level']}';";
		exit();
	break;
	case "cdpaycat":
			$common=new commonObj();
			$common->DropDownMenu($common->makeArr($common->getPayCat($_GET['id'],''),'payCat','payCatDesc',''),'cmbCategory','','class="inputs" style="width:222px;"');	
	break;
	case "cdbranch":	
		$brhobj=new commonObj();
		$x= $brhobj->getBranch($_GET['id']);
		$brhobj->DropDownMenu($brhobj->makeArr($x,'brnCodes','brnDescs',''),'cmbbranch','cmbbranch','class="inputs" style="width:222px;" onchange="loadPayGroup(this.value);"');
		exit();
	break;
	case "cddept":
		$arr = explode(",",$_GET['id']);
		$brhobj=new commonObj();
		$countarray=count($arr);
			if ($countarray==1) {
				$x= $brhobj->getdepartmenttwil("Where compCode='" .$arr[0] . "' and deptLevel='1'");
				$value =$arr[0].",";
				$brhobj->DropDownMenu($brhobj->makeArr($x,'divCode','deptDesc',''),'cmbdivision','','class="inputs" style="width:222px;" onchange="getresult(\''.$arr[0].',\'+this.value,\'profile.obj.php\',\'cddept\',\'divdpt\');"');
				exit();
			}
			
			if ($countarray==2) {
				$value =$arr[0].",".$arr[1].",";
				$x= $brhobj->getdepartmenttwil("Where compCode='" .$arr[0] . "' and divCode='" . $arr[1] . "' and deptLevel='2'");
				$brhobj->DropDownMenu($brhobj->makeArr($x,'deptCode','deptDesc',''),'cmbdepartment','','class="inputs" style="width:222px;" onchange="getresult(\''.$arr[0].','.$arr[1].',\'+this.value,\'profile.obj.php\',\'cddept\',\'divsection\');"');
				exit();
			}
			if ($countarray==3) {
				$x= $brhobj->getdepartmenttwil("Where compCode='" .$arr[0] . "' and divCode='" . $arr[1] . "' and deptCode='" . $arr[2] . "' and deptLevel='3'");
				$brhobj->DropDownMenu($brhobj->makeArr($x,'deptCode','deptDesc',''),'cmbsection','','class="inputs" style="width:222px;" onchange="getresult(\' and divCode=' . $arr[1] . ' and deptCode=' . $arr[2] . ' and compCode=' . $arr[0] . ' and sectCode=\' + this.value+\'&compCode='.$arr[0].'\',\'profile.obj.php\',\'cdposition\',\'dvposition\');"');
				exit();
			}
	break;
	case "cdprevemp":
		if ($_GET['id']=="Y")
			echo '<div id="tab6" class="tab6" onClick="focusTab(6); viewTabSix();">Prev. Emp.</div>';
		else
			echo '<div id="tab6" class="tab6">Prev. Emp.</div>';
			
		exit();	
	break;
	case "cdshift":	
		$brhobj=new commonObj();
		$x= $brhobj->getshiftwil($_GET['id']);
		$brhobj->DropDownMenu($brhobj->makeArr($x,'shiftId','shiftDesc',''),'cmbshift','cmbshift','class="inputs" style="width:222px;"');
		exit();
	break;

	case "cdrank":	
		$brhobj=new commonObj();
		$where = 'where posCode='.$_GET['id'];
		$x=$brhobj->arrRank('where posCode='.$_GET['id'],1); 
		$brhobj->DropDownMenu($x,'cmbrank','cmbrank','class="inputs" style="width:222px;" onchange="getresult(\''. str_replace("'","\'",$where).' and rank=\'+this.value,\'profile.obj.php\',\'cdlevel\',\'dvlevel\');"');
		exit();
	break;
	case "cdlevel":
		$brhobj=new commonObj();
		$rank=$_GET['id'];
		$x=$brhobj->arrRank($_GET['id'],0);
		$brhobj->DropDownMenu($x,'cmblevel','cmblevel','class="inputs" style="width:222px;"');
		exit();
	break;
	case "cdposition":
		$brhobj=new commonObj();
		$poswhere=" and tblPosition.compCode='" . $_GET['id'] . "'";
		$x= $brhobj->getpositionmer($poswhere,1);
		$brhobj->DropDownMenu($brhobj->makeArr($x,'posCode','pp1',''),'cmbposition','cmbposition','class="inputs" style="width:222px;" onchange="getPosInfo(this.value);"');
		exit();
	break;
	case "cdsalary":
				$brhobj=new commonObj();
				$getCompInfo = $brhobj->getCompany($_GET['compcode']);
				if ($_GET['cat']=="1") {
					$Mrate = sprintf('%01.2f',(float)$_GET['Rate']);
					$Drate = sprintf('%01.2f',$Mrate/(float)$getCompInfo['compNoDays']);
					$Hrate =  sprintf('%01.2f',$Drate/8);
				
				}
				else {
					$Mrate = sprintf('%01.2f',(float)$_GET['Rate']*(float)$getCompInfo['compNoDays']);
					$Drate = sprintf('%01.2f',(float)$_GET['Rate']);
					$Hrate =  sprintf('%01.2f',$Drate/8);
				
				}
/*				$Mrate = number_format($Mrate,2);
				$Drate = number_format($Drate,2);
				$Hrate = number_format($Hrate,2);
*/
				if ($_GET['cat']=="1") {
					echo "$('txtdailyrate').value='$Drate';";
					echo "$('txthourlyrate').value='$Hrate';";
				}	
				elseif ($_GET['cat']=="0") {
					echo "$('txtsalary').value='$Mrate';";
					echo "$('txthourlyrate').value='$Hrate';";					
				}
			exit();
	break;
	case "cdsalarycmb":
	
		$ccode=$_GET['id'];
		$salary=$_GET['rate'];
		echo '<input class="inputs" type="text" value="'.$salary.'"  name="txtsalary" onKeyUp="return computeRates(this.value,'.$ccode.',1);" maxlength="9" id="txtsalary" readonly />';
		exit();
	break;
	case "cddratecmb":
		$ccode=$_GET['id'];
		$dailyrate=$_GET['rate'];
		echo '<input class="inputs" type="text" value="'.$dailyrate.'"  name="txtdailyrate" onKeyUp="return computeRates(this.value,'.$ccode.',0);" maxlength="9" readonly id="txtdailyrate" />';
		exit();
	break;
}
//getPayCat($compCode,$where)
class ProfileObj extends commonObj {

	//General Tab
	var $oldcompCode;	
	var $empNo;
	var $compCode;
	var $lName;
	var $fName;
	var $mName;
	var $branch;
	var $location;
	var $position;
	var $strprofile;
	var $paycat;
	var $level;
	
	//Contact Tab
	var $Addr1;
	var $Addr2;
	/*
	Removed from previous by Nhomer requester by HR with document
	var $Addr3;
	var $City;
	*/
	/*
	Added by Nhomer requested by HR with document
	*/
	var $Municipality;
	var $ECPerson;
	var $ECNumber;
	var $provinceCd;
	
	//Personal Tab
	var $sex;
	var $NickName;
	var $Bplace;
	var $dateOfBirth;
	var $maritalStat;
	var $Height;
	var $Weight;
	var $CitizenCd;
	var $Religion;
	var $BloodType;
	/*
	Removed from previous by Nhomer requested by HR with document	
	var $Build;
	var $Complexion;
	var $EyeColor;
	var $Hair;
	var $Spouse;	
	*/
	
	//ID No. Tab
	var $SSS;
	var $PhilHealth;
	var $TIN;
	var $HDMF;
	var $bank;
	var $bankAcctNo;
	
	//Employment Tab
	var $DepCode;
	var $divCode;
	var $secCode;
	//var $Shift;
	var $Status;
	var $Group;
	var $Effectivity;
	var $Regularization;
	var $EndDate;
	var $RestDay;
	var $prevtag;
	var $empRank;
	
	//Prev Employment Tab
	var $prevEmplr;
	var $prevAddr1;
	var $prevAddr2;
	var $prevAddr3;
	var $emplrTin;
	var $prevEarnings="0.00";
	var $prevTaxes="0.00";
	var $grossNonTax="0.00";
	var $nonTax13th="0.00";
	var $nonTaxSss="0.00";
	var $Tax13th="0.00";
	
	//Payroll Tab
	var $Salary="0.00";
	var $Drate="0.00";
	var $Hrate="0.00";
	var $PStatus;
	var $Exemption;
	var $Absences;
	var $Lates;
	var $Undertime;
	var $Overtime;
	var $Release;
	var $hdnCompCode;//hidden text field
	var $hdnEmpNo;//hidden text field
	var $sunLine;
	var $globeLine;
	var $smartLine;
	
	
	function getBranchPayGroup($where){
		if($where!=""){
			$wheres=$where;
			}
		else{
			$wheres="";
			}	
		$qrygetBranch = "SELECT * FROM tblBranch 
						$wheres order by brnDesc";
		return $this->execQry($qrygetBranch);
	}
	
	function getProcGrp() {
		$sqlGrp = "Select payGrp from tblProcGrp where compCode='{$_SESSION['company_code']}' and status='A'";
		$res = $this->getSqlAssoc($this->execQry($sqlGrp));		
		return $res['payGrp'];
		
	}	
	function bioseries($no,$type) {
		if ($type==2) {
			switch (strlen($no)){
				case 7:
					$no="0$no";
				break;
				case 6:
					$no="00$no";
				break;
				case 5:
					$no="000$no";
				break;
				case 4:
					$no="0000$no";
				break;
				case 3:
					$no="00000$no";
				break;
				case 2:
					$no="000000$no";
				break;
				case 1:
					$no="0000000$no";
				break;
			}		
		}
		else {
		
		}
		return $no;
	}
	function getEmpID() {
		$sqlID="Select id from tblEmpID where compCode='{$this->compCode}'";
		$res = $this->getSqlAssoc($this->execQry($sqlID));
		$ID = $res['id'] + 1;
		$sqlUpdateID = "Update tblEmpID set id='$ID' where compCode='{$this->compCode}' ";
		$this->execQry($sqlUpdateID);
		return $ID;
	}	
	function createempno($compcode)
	{
		$qryemptype="SELECT tblCompType.typeDesc,tblCompType.lastEmpNo,tblCompType.typeId FROM tblCompany INNER JOIN tblCompType ON tblCompany.typeId = tblCompType.typeId where compCode='{$this->compCode}'";
		$resemptype=$this->getArrRes($this->execQry($qryemptype));
		foreach ($resemptype as $value)  {
			$emptype=$value['typeDesc'];
			$emplastno=$value['lastEmpNo'];
			$emptypeid=$value['typeId'];
		}
		switch ($emptype) {
			case "Employee":
				$empcode=$this->bioseries($emplastno+1,2);
				$empcode="E$empcode";
			break;
				
			case "Applicant":
				$dt=date("Ymd");
				$empcode=$this->bioseries($emplastno+1,1);
				$empcode="$dt$empcode"; 
			break;

			case "Trainee":
				$empcode=$this->bioseries($emplastno+1,2);
				$empcode="T$empcode"; 
			break;	

			case "Agency":
				$empcode=$this->bioseries($emplastno+1,2);
				$empcode="A$empcode"; 
			break;								

			case "Corp merch":
				$empcode=$this->bioseries($emplastno+1,2);
				$empcode="M$empcode"; 
			break;								
		}
		$emplastno=$emplastno+1;
		$sqlempnoupdate="Update tblCompType set lastEmpNo='$emplastno' where typeId='$emptypeid'";
		$this->execQry($sqlempnoupdate);
		return $empcode;
	}		

	function addEmployee($tbl){
//		$newempcode=$this->createempno($this->compCode);
		$genfields="
					empNo,
					empLastName,
					empFirstName,
					empMidName,
					compCode,
					empBrnCode,
					empLocCode,
					empPosId,
					empLevel,
					";
		$genvalues="
					'".str_replace("'","''",stripslashes(strtoupper($this->empNo)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->lName)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->fName)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->mName)))."',
					'{$this->compCode}',
					'{$this->branch}',
					'{$this->location}',
					'{$this->position}',
					'{$this->level}',
					";
					
		$confields="
					empAddr1,
					empAddr2,
					empProvinceCd,
					empECperson,
					empECNumber,
					empMunicipalityCd,
					";
		$convalues="
					'".str_replace("'","''",stripslashes(strtoupper($this->Addr1)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->Addr2)))."',
					'{$this->provinceCd}',
					'".str_replace("'","''",stripslashes(strtoupper($this->ECPerson)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->ECNumber)))."',
					'{$this->Municipality}',
					";		
		$perfields="
					empSex,
					empNickName,
					empBplace,
					empBday,
					empMarStat,
					empHeight,
					empWeight,
					empCitizenCd,
					empReligion,
					empBloodType,
					";		
					/*
					Removed from previous by Nhomer requested by HR with document
					empSpouseName,
					empBuildDesc,
					empComplexDesc,
					empEyeColorDesc,
					empHairDesc,

					*/
		$pervalues="
					'{$this->sex}',
					'".str_replace("'","''",stripslashes(strtoupper($this->NickName)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->Bplace)))."',
					'{$this->dateOfBirth}',
					'{$this->maritalStat}',
					'".str_replace("'","''",stripslashes(strtoupper($this->Height)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->Weight)))."',
					'{$this->CitizenCd}',
					'{$this->Religion}',
					'{$this->BloodType}',
					";
					/*
					Removed from previous by Nhomer requested by HR with document
					'".str_replace("'","''",stripslashes(strtoupper($this->Spouse)))."',
					'{$this->Build}',
					'{$this->Complexion}',
					'{$this->EyeColor}',
					'{$this->Hair}',					
					*/
		$idfields="
					empSssNo,
					empPhicNo,
					empTin,
					empPagibig,
					empBankCd,
					empAcctNo,
					";
		$idvalues="
					'".str_replace("'","''",stripslashes(strtoupper($this->SSS)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->PhilHealth)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->TIN)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->HDMF)))."',
					'{$this->bank}',
					'".str_replace("'","''",stripslashes(strtoupper($this->bankAcctNo)))."',
					";	
		$regfield="";
		$regvalue="";
		if ($this->Regularization !="") { 
			$regfield="dateReg,";
			$regvalue="'".$this->Regularization."',";
		}
		$edfield="";
		$edvalue="";
		if ($this->EndDate !="") { 
			$edfield="empEndDate,";
			$edvalue="'".$this->EndDate."',";
		}
		$rsfield="";
		$rsvalue="";
		if ($this->RSDate !="") { 
			$rsfield="dateResigned,";
			$rsvalue="'".$this->RSDate."',";
		}
		$wageTag= $this->CheckMinWageTag($this->compCode,$this->branch,$this->Drate);
		$empfields="
					empDepCode,
					empDiv,
					empSecCode,
					empRestDay,
					empstat,
					empPayGrp,
					dateHired,
					$regfield
					$edfield
					$rsfield
					empPrevTag,
					annualTag,
					employmentTag,
					empSunLine,
					empGlobeLine,
					empSmartLine,
					";
		$empvalues="
					'{$this->DepCode}',
					'{$this->divCode}',
					'{$this->secCode}',
					'{$this->RestDay}',
					'RG',
					'{$this->Group}',
					'{$this->Effectivity}',
					$regvalue
					$edvalue
					$rsvalue
					'{$this->prevtag}',
					'{$this->prevtag}',
					'{$this->Status}',
					'{$this->empSunLine}',
					'{$this->empGlobeLine}',
					'{$this->empSmartLine}',
					";																												

		$payfields="
					empMrate,
					empPayType,
					empTeu,
					empDrate,
					empHrate,
					empRank,
					empPayCat,
					empWageTag,
					empdateadded,
					id
					";

		$this->computesalary($this->compCode,$this->Salary);
		
		$payvalues="
					'".str_replace("'","''",stripslashes(strtoupper($this->Salary)))."',
					'{$this->PStatus}',
					'{$this->Exemption}',
					'{$this->Drate}',
					'{$this->Hrate}',
					'{$this->empRank}',
					'{$this->paycat}',
					'$wageTag',
					'".date('Y-m-d')."',
					'".$this->getEmpID()."'
					";

		
		$qryAddEmployee = "INSERT INTO $tbl (
							$genfields
							$confields
							$perfields
							$idfields
							$empfields
							$payfields )
					  VALUES(
					  		$genvalues
							$convalues
							$pervalues
							$idvalues
							$empvalues
							$payvalues)";
							
		$resAddEmployee = $this->execQry($qryAddEmployee);
			$qryUpdatePrevEmp = "Update tblPrevEmployer set empNo='".trim($this->empNo)."',compCode='{$this->compCode}' where empNo='{$this->strprofile}'";
			$resUpdatePrevEmp = $this->execQry($qryUpdatePrevEmp);

			$qryUpdateContacts ="Update tblContactMast set empNo='".trim($this->empNo)."',compCode='{$this->compCode}' where empNo='{$this->strprofile}'";
			$resUpdateContacts = $this->execQry($qryUpdateContacts);
			
			$qryUpdateUserDefMst = "Update tblUserDefinedMst set empNo='".trim($this->empNo)."',compCode='{$this->compCode}' where empNo='{$this->strprofile}'";
			$resUpdateUserDefMst = $this->execQry($qryUpdateUserDefMst);
			//Educational Background
			$qryUpdateEducationalBackground="Update tblEducationalBackground set empNo='".trim($this->empNo)."' where empNo='{$this->strprofile}'";
			$resUpdateEducationalBackground=$this->execQry($qryUpdateEducationalBackground);
			 //Trainings 
			 $qryTraining="Update tblTrainings set empNo='".trim($this->empNo)."' where empNo='{$this->strprofile}'";
			 $resTraining=$this->execQry($qryTraining);
			 //Performance
			 $qryPerformance="Update tblPerformance set empNo='".trim($this->empNo)."' where empNo='{$this->strprofile}'";
			 $resPerformance=$this->execQry($qryPerformance);
			 //Disciplinary Action
			 $qryDisciplinary="Update tblDisciplinaryAction set empNo='".trim($this->empNo)."' where empNo='{$this->strprofile}'";
			 $resDisciplinary=$this->execQry($qryDisciplinary);
			 //Employment History
			 $qryEmployment="Update tblEmployeeDataHistory set empNo='".trim($this->empNo)."' where empNo='{$this->strprofile}'";
			 $resEmployment=$this->execQry($qryEmployment);
			 //Allowance
			 $qryAllowance="Update tblAllowance_New set empNo='".trim($this->empNo)."' where empNo='{$this->strprofile}'";
			 $resAllowance=$this->execQry($qryAllowance);
			 
			$qrybio = "Insert into tblBioEmp (compCode,locCode,bioNumber,empNo,bioStat) values ({$this->compCode},{$this->location},'{$this->bio}','{$this->empNo}','A')";
			$this->execQry($qrybio);
		if($resAddEmployee){
		}
		else {
			return false;
		}	
	}	
	
	function employeeaction($act,$type,$desc,$empNO,$recNo) {
		switch ($act) {
			case "Add Contact":
				$qryact="Insert into tblContactmast (compCode,empNo, contactCD, contactName) values ('{$_SESSION['oldcompCode']}','$empNO','$type','$desc')";
				
			break;
			
			case "Edit Contact":
				$qryact="update tblContactmast set contactCD='$type', contactName='$desc' where recNo='$recNo'";
			break;
			
			case "Delete":
				 $qryact="Delete from tblContactmast where recNo='$recNo'";
			break;
		}
		if ($this->execQry($qryact)) {
			return true;
		}
		else {
			return false;
		}
		
		
	}
	function getcontactinfo($recNo) {
		$qryinfo="Select * from tblContactMast where recNo='$recNo'";
		$res=$this->execQry($qryinfo);
		return $this->getArrRes($res);
	}
	
	function computesalary($compCode,$salary) {
		$getCompInfo = $this->getCompany($compCode);
		
		$this->Drate = sprintf('%01.2f',(float)$salary/(float)$getCompInfo['compNoDays']);
		$this->Hrate =  sprintf('%01.2f',$this->Drate/8);
	}
	
	function viewprofile($empNo) {
		$qryviewprofile="Select *,Day(empBday) as empBday_D , Month(empBday) as empBday_M, Year(empBday) as empBday_Y, Day(dateHired) as dateHired_D , Month(dateHired) as dateHired_M, Year(dateHired) as dateHired_Y
		, Day(dateReg) as dateReg_D , Month(dateReg) as dateReg_M, Year(dateReg) as dateReg_Y
		, Day(empEndDate) as empEndDate_D , Month(empEndDate) as empEndDate_M, Year(empEndDate) as empEndDate_Y,employmentTag as status from tblEmpMast_new where empNo='$empNo' and compCode='{$this->oldcompCode}'";
		$res=$this->execQry($qryviewprofile);
		$res=$this->getArrRes($res);
		foreach ($res as $profile) {
			//General Tab	
			$arrBio = $this->getBio($profile['empNo'],$profile['compCode']);
			$this->bio = $arrBio['bioNumber'];
			$this->empNo=$profile['empNo'];
			$this->compCode=$profile['compCode'];
			$this->lName=$profile['empLastName'];
			$this->fName=$profile['empFirstName'];
			$this->mName=$profile['empMidName'];
			$this->branch=$profile['empBrnCode'];
			$this->location=$profile['empLocCode'];
			$this->position=$profile['empPosId'];
			$this->level=$profile['empLevel'];
//			$this->strprofile=$profile[''];
			
			//Contact Tab
			$this->Addr1=$profile['empAddr1'];
			$this->Addr2=$profile['empAddr2'];
			/*
			Removed from previous by Nhomer requested by HR with document
			$this->Addr3=$profile['empAddr3'];
			$this->City=$profile['empCityCd'];
			*/
			/*
			Added by Nhomer requested by HR with document
			*/
			$this->provinceCd=$profile['empProvinceCd'];
			$this->ECPerson=$profile['empECPerson'];
			$this->ECNumber=$profile['empECNumber'];
			$this->Municipality=$profile['empMunicipalityCd'];
			
			//Personal Tab
			$this->sex=$profile['empSex'];
			$this->NickName=$profile['empNickName'];
			$this->Bplace=$profile['empBplace'];
			$this->dateOfBirth=$profile['empBday'];
			$this->maritalStat=$profile['empMarStat'];
			$this->Height=$profile['empHeight'];
			$this->Weight=$profile['empWeight'];
			$this->CitizenCd=$profile['empCitizenCd'];
			$this->Religion=$profile['empReligion'];
			$this->BloodType=$profile['empBloodType'];
			$this->SSS=$profile['empSssNo'];
			$this->PhilHealth=$profile['empPhicNo'];
			$this->TIN=$profile['empTin'];
			$this->HDMF=$profile['empPagibig'];
			$this->bank=$profile['empBankCd'];
			$this->bankAcctNo=$profile['empAcctNo'];
			
			/*
			Removed from previous by Nhomer requested by HR with document
			$this->Spouse=$profile['empSpouseName'];
			$this->Build=$profile['empBuildDesc'];
			$this->Complexion=$profile['empComplexDesc'];
			$this->EyeColor=$profile['empEyeColorDesc'];
			$this->Hair=$profile['empHairDesc'];
			*/
			
			//ID No. Tab
			
			//Employment Tab
			$this->DepCode=$profile['empDepCode'];
			$this->divCode=$profile['empDiv'];
			$this->secCode=$profile['empSecCode'];
			$this->RestDay=$profile['empRestDay'];
			$this->divCode=$profile['empDiv'];
			$this->secCode=$profile['empSecCode'];
			$this->Status=$profile['status'];
			$this->Group=$profile['empPayGrp'];
			$this->Effectivity=$profile['dateHired'];
			$this->Regularization=$profile['dateReg'];
			$this->EndDate=$profile['empEndDate'];
			$this->RSDate= $profile['dateResigned'];
			$this->prevtag=$profile['empPrevTag'];
			$this->empRank=$profile['empRank'];
			//Payroll Tab
			$this->Salary=$profile['empMrate'];
			$this->Drate=$profile['empDrate'];
			$this->Hrate=$profile['empHrate'];			
			$this->PStatus=$profile['empPayType'];
			$this->Exemption=$profile['empTeu'];
			$this->paycat=$profile['empPayCat'];
			$this->sunLine=$profile['empSunLine'];
			$this->globeLine=$profile['empGlobeLine'];
			$this->smartLine=$profile['empSmartLine'];
//			$this->Release=$profile[''];
		}

	}
	
	function updateemployee($empNox) {
		$this->computesalary($this->compCode,$this->Salary);
		$genfields="
					empNo='".str_replace("'","''",stripslashes(strtoupper($this->empNo)))."',
					empLastName='".str_replace("'","''",stripslashes(strtoupper($this->lName)))."',
					empFirstName='".str_replace("'","''",stripslashes(strtoupper($this->fName)))."',
					empMidName='".str_replace("'","''",stripslashes(strtoupper($this->mName)))."',
					compCode='{$this->compCode}',
					empBrnCode='{$this->branch}',
					empLocCode='{$this->location}',
					empPosId='{$this->position}',
					empLevel='{$this->level}',
					";
					
		$confields="
					empAddr1='".str_replace("'","''",stripslashes(strtoupper($this->Addr1)))."',
					empAddr2='".str_replace("'","''",stripslashes(strtoupper($this->Addr2)))."',
					empProvinceCd='{$this->provinceCd}',
					empECPerson='".str_replace("'","''",stripslashes(strtoupper($this->ECPerson)))."',
					empECNumber='".str_replace("'","''",stripslashes(strtoupper($this->ECNumber)))."',
					empMunicipalityCd='{$this->Municipality}',
					";
		$perfields="
					empSex='{$this->sex}',
					empNickName='".str_replace("'","''",stripslashes(strtoupper($this->NickName)))."',
					empBplace='".str_replace("'","''",stripslashes(strtoupper($this->Bplace)))."',
					empBday='{$this->dateOfBirth}',
					empMarStat='{$this->maritalStat}',
					empHeight='".str_replace("'","''",stripslashes(strtoupper($this->Height)))."',
					empWeight='".str_replace("'","''",stripslashes(strtoupper($this->Weight)))."',
					empCitizenCd='{$this->CitizenCd}',
					empReligion='{$this->Religion}',
					empBloodType='{$this->BloodType}',
					empSunLine='{$this->empSunLine}',
					empGlobeLine='{$this->empGlobeLine}',
					empSmartLine='{$this->empSmartLine}',
					";		
					/*
					Removed by Nhomer requested by HR with document
					empSpouseName='".str_replace("'","''",stripslashes(strtoupper($this->Spouse)))."',
					empBuildDesc='{$this->Build}',
					empComplexDesc='{$this->Complexion}',
					empEyeColorDesc='{$this->EyeColor}',
					empHairDesc='{$this->Hair}',
					*/
		$idfields="
					empSssNo='".str_replace("'","''",stripslashes(strtoupper($this->SSS)))."',
					empPhicNo='".str_replace("'","''",stripslashes(strtoupper($this->PhilHealth)))."',
					empTin='".str_replace("'","''",stripslashes(strtoupper($this->TIN)))."',
					empPagibig='".str_replace("'","''",stripslashes(strtoupper($this->HDMF)))."',
					empBankCd='{$this->bank}',
					empAcctNo='".str_replace("'","''",stripslashes(strtoupper($this->bankAcctNo)))."',
					";
		
		$regfield="";
		if ($this->Regularization !="") { 
			$regfield="dateReg='".$this->Regularization."',";
		}
		else {
			$regfield="dateReg=NULL,";
		}
		$edfield="";
		if ($this->EndDate != "") { 
			$edfield="empEndDate='".$this->EndDate."',";
		}
		else {
			$edfield="empEndDate=NULL,";
		}		
		$refield="";
		if ($this->RSDate != "") { 
			$rsfield="dateResigned='".$this->RSDate."',";
		}
		else {
			$rsfield="dateResigned=NULL,";
		}		
		$empfields="
					empDepCode='{$this->DepCode}',
					empDiv='{$this->divCode}',
					empSecCode='{$this->secCode}',
					empRestDay='{$this->RestDay}',
					employmentTag='{$this->Status}',
					empPayGrp='{$this->Group}',
					dateHired='{$this->Effectivity}',
					$regfield
					$edfield
					$rsfield
					empPrevTag='{$this->prevtag}',
					annualTag='{$this->prevtag}',
					";
		$wageTag= $this->CheckMinWageTag($this->compCode,$this->branch,$this->Drate);
		$payfields="
					empMrate='".str_replace("'","''",stripslashes(strtoupper($this->Salary)))."',
					empPayType='{$this->PStatus}',
					empTeu='{$this->Exemption}',
					empDrate='{$this->Drate}',
					empHrate='{$this->Hrate}',
					empAbsencesTag='{$this->Absences}',
					empLatesTag='{$this->Lates}',
					empUtTag='{$this->Undertime}',
					empOtTag='{$this->Overtime}',
					empRank='{$this->empRank}',
					empPayCat='{$this->paycat}',
					empWageTag = '$wageTag'
					";
		$qryupdateemp="Update tblEmpMast_new set $genfields $confields $perfields $idfields $empfields $payfields where empNo='$empNox'  and compCode='{$this->oldcompCode}'";
		$qryUpdatePrevEmp = "Update tblPrevEmployer set compCode='{$this->compCode}' where empNo='{$this->strprofile}' and compCode='{$this->oldcompCode}'";

		$qryUpdateUserDefMst = "Update tblUserDefinedMst set compCode='{$this->compCode}' where empNo='{$this->strprofile}' and compCode='{$this->oldcompCode}'";
		$resUpdatePrevEmp = $this->execQry($qryUpdatePrevEmp);
		$resUpdateUserDefMst = $this->execQry($qryUpdateUserDefMst);
		$resupdateemp = $this->execQry($qryupdateemp);
		$arrBio = $this->getBio($this->empNo,$this->compCode);
		if ($arrBio['bioNumber'] != ""){ 
			$qrybio = "update tblBioEmp set locCode = {$this->location},bioNumber ='{$this->bio}'  where empNo={$this->empNo} and compCode = {$this->compCode}";
		}
		else{
			$qrybio = "Insert into tblBioEmp (compCode,locCode,bioNumber,empNo,bioStat) values ({$this->compCode},{$this->location},'{$this->bio}','{$this->empNo}','A')";
		}
		
		$this->execQry($qrybio);
		
		$resupdateuser = $this->updateuser();
		if($resupdateemp){
		}
		else {
			return false;
		}	
	
	}
	
	function checkPrevEmp($empNox){
		$qryCheckPrevEmp = "SELECT prevEmplr FROM tblPrevEmployer
							 WHERE empNo = '$empNox'
							 AND prevEmplr = '".str_replace("'","''",stripslashes(strtoupper(trim($this->prevEmplr))))."'";
		$resCheckPrevEmp = $this->execQry($qryCheckPrevEmp);
		if(!$resCheckPrevEmp){
			return -1;
		}
		else{
			return $this->getRecCount($resCheckPrevEmp);
		}
	}
	
	function checkno($field,$table,$value,$type){
	$value=str_replace("_"," ",stripslashes($value));
		if ($type=="1") {
			$sqlcheck="Select * from $table where $field='$value' and $field<>''";
		}
		elseif ($type=="0") {
			$sqlcheck="Select * from $table where $field='$value' and $field<>'' and not empNo='" . $_SESSION['strprofile'] . "' ";
		}
		$res=$this->execQry($sqlcheck);
		return $this->getArrRes($res);
	}
	
	function checkblacklist($sssNo) {
		$sqlbl="Select Emp_last from tblBlacklistedEmp where Replace(Emp_sss,'-','')='$sssNo' ";
		
		$res=$this->execQry($sqlbl);
		return $this->getRecCount($res);
	}
	
	function updateuser() {
		$qryupdatuer = "Update tblusers set compCode='{$this->compCode}' where empNo='{$this->empNo}' and compCode='{$this->oldcompCode}'";
		$res=$this->execQry($qryupdatuer);
	}
	
	function restday($empNo,$compCode,$restday) {
		$qryrestday = "Update tblEmpMast_new set empRestDay='$restday' where empNo='$empNo' and compCode='$compCode'";
		return $this->execQry($qryrestday);
	}
	function CheckMinWageTag($compCode,$brnch,$Drate) {
		$sqlWage = "Select minWage from tblBranch where compCode='$compCode' AND brnCode ='$brnch'";
		$res = $this->execQry($sqlWage);
		$res = $this->getSqlAssoc($res);
		if ((float)$res['minWage'] != 0) {
			if ((float)$Drate<=(float)$res['minWage']){
				return 'Y';
			}
			else{
				return 'N';	
			}
		} else {
			return 'N';
		}	
	}
	
	function delNewEmp($empNo,$compCode) {
		//Delete New Employee
		$sqlDel ="Delete from tblEmpMast_new where empNo='$empNo' AND compCode='$compCode'";
		return $this->execQry($sqlDel);
	}
	
	function delEmpOtherInfo($empNo,$tname){
		//Delete Other Info
		$sqlDelEduc="Delete from $tname where empNo='".$empNo."'";
		return $this->execQry($sqlDelEduc);
	}
	
	function releaseEmp($empNo,$compCode) {
		$sqlRelease = "INSERT into tblEmpMast (compCode, empNo, empLastName, empFirstName, empMidName, empLocCode, empBrnCode, 
		empDiv, empDepCode, empSecCode, empPosId, dateHired, empStat, dateReg, dateResigned, empRestDay, empTeu, empTin, 
		empSssNo, empPagibig, empBankCd, empAcctNo, empPayGrp, empPayType, empPayCat, empWageTag, empPrevTag, empAddr1, 
		empAddr2, empAddr3, empMarStat, empSex, empBday, empReligion, empMrate, empDrate, empHrate, empOtherInfo, empNickName, 
		empBplace, empHeight, empWeight, empCitizenCd, empBloodType, empEndDate, empLevel, empSubSection, empCityCd, 
		empSpouseName, empBuildDesc, empComplexDesc, empEyeColorDesc, empHairDesc, empPhicNo, empAbsencesTag, empLatesTag, 
		empUtTag, empOtTag, empPicture, empImageSize, empShiftId, annualTag, empRank,releasedby,empdateadded,id, empProvinceCd, 
		empECPerson, empECNumber, empMunicipalityCd,employmentTag,empSunLine,empGlobeLine,empSmartLine)
		SELECT compCode, empNo, empLastName, empFirstName, empMidName, empLocCode, empBrnCode, 
		empDiv, empDepCode, empSecCode, empPosId, dateHired, empStat, dateReg, dateResigned, empRestDay, empTeu, empTin, 
		empSssNo, empPagibig, empBankCd, empAcctNo, empPayGrp, empPayType, empPayCat, empWageTag, empPrevTag, empAddr1, 
		empAddr2, empAddr3, empMarStat, empSex, empBday, empReligion, empMrate, empDrate, empHrate, empOtherInfo, empNickName, 
		empBplace, empHeight, empWeight, empCitizenCd, empBloodType, empEndDate, empLevel, empSubSection, empCityCd, 
		empSpouseName, empBuildDesc, empComplexDesc, empEyeColorDesc, empHairDesc, empPhicNo, empAbsencesTag, empLatesTag, 
		empUtTag, empOtTag, empPicture, empImageSize, empShiftId, annualTag, empRank,'{$_SESSION['user_id']}', empdateadded, id, empProvinceCd,
		empECPerson, empECNumber, empMunicipalityCd, employmentTag, empSunLine, empGlobeLine, empSmartLine
		FROM tblEmpMast_new 
		WHERE empNo='$empNo' AND compCode='$compCode'";
			
		$sqlUpdateReleased = "Update tblEmpMast_new set stat='R',datereleased='".date('Y-m-d')."',userReleased='{$_SESSION['user_id']}' where empNo='$empNo' AND compCode='$compCode'";
		$this->execQry($sqlRelease);						
		return $this->execQry($sqlUpdateReleased);
	}
			
	function releaseAllowance($empNo,$compCode){
		$sqlAllowRelease="Insert into tblAllowance (compCode,empNo,allowCode,allowAmt,allowSked,allowTaxTag,allowPayTag,allowStart,allowEnd,		allowStat,sprtPS,allowTag) 
		Select compCode,empNo,allowCode,allowAmt,allowSked,allowTaxTag,allowPayTag,allowStart,allowEnd,allowStat,sprtPS,allowTag from tblAllowance_New where empNo='{$empNo}' and compCode='{$compCode}'";
		$this->execQry($sqlAllowRelease);
	}
	
	function updateAllowance($empNo,$compCode){	
		echo $updateAllowanceNew="Update tblAllowance_New set dateReleased='".date('Y-m-d')."',userReleased='{$_SESSION['user_id']}' where empNo='$empNo' and compCode='{$compCode}'";
		$this->execQry($updateAllowanceNew);
	}	

	function getBio($empNo,$compCode) {
		$qryBio = "Select bioNumber from tblBioEmp where empNo='".trim($empNo)."' and compCode='".trim($compCode)."'";
		$res=$this->execQry($qryBio);
		return $this->getSqlAssoc($res);
	}
	
	function checkBio($bio,$type) {
		if ($type==1)
			$qryBio = "Select bioNumber from tblBioEmp where bioNumber='$bio'";
		else
			$qryBio = "Select bioNumber from tblBioEmp where bioNumber='$bio' and not empNo='" . $_SESSION['strprofile'] . "'";
			
		$res=$this->execQry($qryBio);
		return $this->getSqlAssoc($res);
	}	
	
}


if ($_GET['code']=="cdsavecontact"){
		$brhobj=new ProfileObj();
		$brhobj->employeeaction($_GET['act'],$_GET['contacttype'],$_GET['contactdesc'],$_GET['empNo'],$_GET['recNo']);
}


if ($_GET['code']=="cdcheckno"){
		$brhobj=new ProfileObj();
		$brhobj->oldcompCode=$_SESSION['oldcompCode'];
		$dv=$_GET['dv'];
		$txt=str_replace("dv","ch",$dv);
		$label=$_GET['label'];
		$field=$_GET['field'];
		if ($_GET['field']=="empSssNo") {
			$arrSSS = explode(',',$_GET['value']);
			$res=$brhobj->checkno($_GET['field'],$_GET['table'],$arrSSS[0],$_GET['type']);
			$name=$res['empLastName'] . ", " . $res['empFirstName'];
			if ($res['empSssNo']!="") {
				$arrBL = $brhobj->checkblacklist($arrSSS);
				if (count($arrBL) > 0) {
					foreach($arrBL as $valBL) {
						$Blname .= $valBL['empLastName'].", ".$valBL['empFirstName']." ".$valBL['empMidName'][0].".,";
					}
					$Blname = substr($Blname,0,strlen($Blname)-1);
					echo 'alert("Employee(s) with same SSS No./Name in our Blacklist database.\n'.$Blname.'");';
					echo "$('$txt').value='2';";
				}
				else {

						echo "if ($('$txt').value!=1) {
					alert('$label is already used by $name');
					}";
						echo "$('$txt').value='1';";
				}
			}	
			else {
				$arrBL = $brhobj->checkblacklist($arrSSS);
				if (count($arrBL) > 0) {
					foreach($arrBL as $valBL) {
						$Blname .= $valBL['empLastName'].", ".$valBL['empFirstName']." ".$valBL['empMidName'][0].".,";
					}
					$Blname = substr($Blname,0,strlen($Blname)-1);
					echo 'alert("Employee(s) with same SSS No./Name in our Blacklist database.\n'.$Blname.'");';
					echo "$('$txt').value='2';";
				}
				else {
					echo "$('$txt').value='';";
				}	
			}
		} elseif ($_GET['field']=="bio") {
			$res=$brhobj->checkBio($_GET['value'],$_GET['type']);
			if ($res['bioNumber']!="") {
				echo "
				if ($('chbio').value!=1) {
				alert('Bio Series No. is already used');
				}";
				echo "$('chbio').value='1';";
			}	
			else {
				echo "$('chbio').value='';";
			}
		
		} else {
			$res=$brhobj->checkno($_GET['field'],$_GET['table'],$_GET['value'],$_GET['type']);
			$name=$res['empLastName'] . ", " . $res['empFirstName'];
			if ($res[$_GET['field']]!="") {
				echo "
				if ($('$txt').value!=1) {
				alert('$label is already used by $name');
				}";
				echo "$('$txt').value='1';";
			}	
			else {
				echo "$('$txt').value='';";
			}
		}
}
?>