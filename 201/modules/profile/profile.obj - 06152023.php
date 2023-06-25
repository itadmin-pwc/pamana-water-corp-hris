<?
include("../../../includes/db.inc.php");
include("../../../includes/201common.php");
session_start();
error_reporting(0);
if($_GET['transtype']=="getpaycat"){
	$maintEmpObj=new commonObj();
	$poslevel="";
	//tblpositions level field
	$poscodes=$maintEmpObj->getpositionwil(" and posCode='{$_GET['poscode']}'",2);
	if($poscodes['level']<=0){
		$poslevel=0;	
	}
	elseif($poscodes['level']<=40){
		$poslevel=3;	
	}
	elseif($poscodes['level']<=90){
		$poslevel=2;
	}
	elseif($poscodes['level']<=92){
		$poslevel=1;
	}
	$paycatqry=$maintEmpObj->makearr($maintEmpObj->getPayCat($_SESSION['company_code'],""),'payCat','payCatDesc','');
	echo $maintEmpObj->DropDownMenu($paycatqry,'cmbCategory',$poslevel,'class="inputs" style="width:145px;" disabled="disabled"');	
	echo '<input type="hidden" name="txtcat" id="txtcat" value="'.$poslevel.'"/>';
	exit();
}
if($_GET['action']=="setpaygroup"){
		$maintEmpObj=new commonObj();
		$resPayGroup = $maintEmpObj->getBranchPayGroup(" where compCode='{$_SESSION['company_code']}' and brnCode='{$_GET['groupid']}'");	
		if($maintEmpObj->getRecCount($resPayGroup)>0){
			$resQry=$maintEmpObj->getArrRes($resPayGroup);
			foreach($resQry as $PayGroup => $payGroupVal){
					$groupId=$payGroupVal['brnDefGrp'];
				}
			}
		echo $maintEmpObj->DropDownMenu(array('','Group 1','Group 2'),'cmbgroup',$groupId,'class="inputs" style="width:145px;"');
	exit();
	}

switch ($_GET['code']) {
	case "cddivision":
			$common=new commonObj();
			$pos = $common->getpositionwil(" and posCode='{$_GET['posCode']}'",2);
			$Div = $common->getDivDescArt($_GET['company_code'],$pos['divCode']);
			$Dept = $common->getDeptDescArt($_GET['company_code'], $pos['divCode'],$pos['deptCode']);
			$Sect = $common->getSectDescArt($_GET['company_code'], $pos['divCode'],$pos['deptCode'],$pos['sectCode']);
			$rank = $common->getRank($pos['rank']);
			echo "$('txtDiv').value='{$pos['divCode']}';";
			echo "$('txtDept').value='{$pos['deptCode']}';";
			echo "$('txtSect').value='{$pos['sectCode']}';";
			echo "$('txtRank').value='{$pos['rank']}';";
			echo "$('txtLevel').value='{$pos['level']}';";
			echo "$('divdivision').innerHTML='{$Div['deptDesc']}';";
			echo "$('divdpt').innerHTML='{$Dept['deptDesc']}';";
			echo "$('divsection').innerHTML='{$Sect['deptDesc']}';";
			echo "$('dvrank').innerHTML='{$rank['rankDesc']}';";
			echo "$('dvlevel').innerHTML='Level {$pos['level']}';";
		exit();
	break;
	case "cdpaycat":
			$common=new commonObj();
			$common->DropDownMenu($common->makeArr($common->getPayCat($_GET['id'],''),'payCat','payCatDesc',''),'cmbCategory','','class="inputs" style="width:145px;"  disabled="disabled"');	
	break;
	case "cdbranch":	
		$brhobj=new commonObj();
		$x= $brhobj->getFilterBranch($_GET['id']);
		$brhobj->DropDownMenu($brhobj->makeArr($x,'brnCode','brnDesc',''),'cmbbranch','cmbbranch','class="inputs" style="width:222px;" onchange="loadPayGroup(this.value);"');
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
		$brhobj->DropDownMenu($brhobj->makeArr($x,'posCode','pp1',''),'cmbposition','cmbposition','class="inputs" style="width:222px;" onchange="setRateMode(); getPosInfo(this.value); getPayCats(this.value); "');
		exit();
	break;
	case "cdsalary":
				$brhobj=new commonObj();
				$rate =  str_replace(',','',$_GET['Rate']);
				$getCompInfo = $brhobj->getCompany($_GET['compcode']);

				//1 monthly
				//0 daily
				if ($_GET['cat']=="1") {
					$Mrate = sprintf('%01.2f',(float)$rate);
					$Drate = sprintf('%01.2f',$Mrate/(float)$getCompInfo['compNoDays']);
					$Hrate =  sprintf('%01.2f',$Drate/8);
				}
				else {
					$Mrate = sprintf('%01.2f',(float)$rate*(float)$getCompInfo['compNoDays']);
					$Drate = sprintf('%01.2f',(float)$rate);
					$Hrate =  sprintf('%01.2f',$Drate/8);
				
				}
/*				$Mrate = number_format($Mrate,2);
				$Drate = number_format($Drate,2);
				$Hrate = number_format($Hrate,2);
*/
			//echo $Mrate."\n".$Drate."\n".$Hrate;
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
		echo '<input class="inputs" type="text" value="'.$salary.'"  name="txtsalary" onKeyPress="return computeRates(this.value,'.$ccode.',1,event);" maxlength="9" id="txtsalary" readonly />';
		exit();
	break;
	case "cddratecmb":
		$ccode=$_GET['id'];
		$dailyrate=$_GET['rate'];
		echo '<input class="inputs" type="text" value="'.$dailyrate.'"  name="txtdailyrate" onKeyPress="return computeRates(this.value,'.$ccode.',0,event);" maxlength="9" readonly id="txtdailyrate" />';
		exit();
	break;
}
//getPayCat($compCode,$where)
class ProfileObj extends commonObj {

	//General Tab
	var $bio;
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
	//var $Addr3;
	//var $City;
	var $Municipality;
	var $provinceCd;
	var $ECPerson;
	var $ECnumber;
	
	//Personal Tab
	var $sex;
	var $NickName;
	var $Bplace;
	var $dateOfBirth;
	var $dateOfBirth_D;
	var $dateOfBirth_M;
	var $dateOfBirth_Y;
	var $maritalStat;
	var $Spouse;
	var $Height;
	var $Weight;
	var $CitizenCd;
	var $Religion;
	var $Build;
	var $Complexion;
	var $EyeColor;
	var $Hair;
	var $BloodType;
	
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
	var $RSDate;
	var $RestDay;
	var $prevtag;
	var $empRank;
	var $empStat;
	
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

	function addEmployee(){
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
					empECPerson,
					empECNumber,
					empMunicipalityCd,
					";
		$convalues="
					'".str_replace("'","''",stripslashes(strtoupper($this->Addr1)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->Addr2)))."',
					'{$this->provinceCd}',
					'{$this->ECPerson}',
					'{$this->ECNumber}',
					'{$this->Municipality}',
					";		
		$perfields="
					empSex,
					empNickName,
					empBplace,
					empBday,
					empMarStat,
					empSpouseName,
					empHeight,
					empWeight,
					empCitizenCd,
					empReligion,
					empBuildDesc,
					empComplexDesc,
					empEyeColorDesc,
					empHairDesc,
					empBloodType,
					";		
		$pervalues="
					'{$this->sex}',
					'".str_replace("'","''",stripslashes(strtoupper($this->NickName)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->Bplace)))."',
					'{$this->dateOfBirth}',
					'{$this->maritalStat}',
					'".str_replace("'","''",stripslashes(strtoupper($this->Spouse)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->Height)))."',
					'".str_replace("'","''",stripslashes(strtoupper($this->Weight)))."',
					'{$this->CitizenCd}',
					'{$this->Religion}',
					'{$this->Build}',
					'{$this->Complexion}',
					'{$this->EyeColor}',
					'{$this->Hair}',
					'{$this->BloodType}',
					";

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
			$edvalue="'".$this->Regularization."',";
		}
		$rsfield="";
		$rsvalue="";
		if ($this->RSDate !="") { 
			$rsfield="dateResigned,";
			$rsvalue="'".$this->RSDate."',";
		}

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
					'{$this->empStat}',
					";																												

		$payfields="
					empMrate,
					empPayType,
					empTeu,
					empDrate,
					empHrate,
					empRank,
					empPayCat";

		$this->computesalary($this->compCode,$this->Salary);
		$payvalues="
					'".str_replace("'","''",stripslashes(strtoupper($this->Salary)))."',
					'{$this->PStatus}',
					'{$this->Exemption}',
					'{$this->Drate}',
					'{$this->Hrate}',
					'{$this->empRank}',
					'{$this->paycat}'
					";

		
		$qryAddEmployee = "INSERT INTO tblEmpMast (
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
							
/*		$resAddEmployee = $this->execQry($qryAddEmployee);
			$qryUpdatePrevEmp = "Update tblPrevEmployer set empNo='".trim($this->empNo)."',compCode='{$this->compCode}' where empNo='{$this->strprofile}'";
			$resUpdatePrevEmp = $this->execQry($qryUpdatePrevEmp);

			$qryUpdateContacts ="Update tblContactMast set empNo='".trim($this->empNo)."',compCode='{$this->compCode}' where empNo='{$this->strprofile}'";
			$resUpdateContacts = $this->execQry($qryUpdateContacts);
			
			$qryUpdateUserDefMst = "Update tblUserDefinedMst set empNo='".trim($this->empNo)."',compCode='{$this->compCode}' where empNo='{$this->strprofile}'";
			$resUpdateUserDefMst = $this->execQry($qryUpdateUserDefMst);
			$qrybio = "Insert into tblBioEmp (compCode,locCode,bioNumber,empNo,bioStat) values ({$this->compCode},{$this->location},{$this->bio},'{$this->empNo}','A')";
			$this->execQry($qrybio);
*/		if($resAddEmployee){
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
		, Day(empEndDate) as empEndDate_D , Month(empEndDate) as empEndDate_M, Year(empEndDate) as empEndDate_Y,employmentTag as status from tblEmpMast where empNo='$empNo' and compCode='{$this->oldcompCode}'";
		$res=$this->execQry($qryviewprofile);
		$res=$this->getArrRes($res);
		foreach ($res as $profile) {
			//General Tab	
			$arrBio = $this->getBio($profile['empNo'],$profile['compCode']);
			$this->bio		= $arrBio['bioNumber'];
			$this->empNo		= $profile['empNo'];
			$this->compCode		= $profile['compCode'];
			$this->lName		= $profile['empLastName'];
			$this->fName		= $profile['empFirstName'];
			$this->mName		= $profile['empMidName'];
			$this->branch		= $profile['empBrnCode'];
			$this->location		= $profile['empLocCode'];
			$this->position		= $profile['empPosId'];
			$this->level		= $profile['empLevel'];
//			$this->strprofile=$profile[''];
			
			//Contact Tab
			$this->Addr1		= $profile['empAddr1'];
			$this->Addr2		= $profile['empAddr2'];
			/*
			Removed from previous by Nhomer requested by HR with document
			$this->Addr3		= $profile['empAddr3'];
			$this->City			= $profile['empCityCd'];
			*/
			
			/*
			Added by Nhomer requested by HR with document
			*/
			$this->Municipality	=$profile['empMunicipalityCd'];
			$this->provinceCd	= $profile['empProvinceCd'];
			$this->ECPerson		= $profile['empECPerson'];
			$this->ECNumber		= $profile['empECNumber'];
			
			//Personal Tab
			$this->sex			= $profile['empSex'];
			$this->NickName		= $profile['empNickName'];
			$this->Bplace		= $profile['empBplace'];
			$this->dateOfBirth	= $profile['empBday'];
			$this->maritalStat	= $profile['empMarStat'];
			/*
			Removed from previous by Nhomer requested by HR with document
			$this->Spouse		= $profile['empSpouseName'];
			$this->Build		= $profile['empBuildDesc'];
			$this->Complexion	= $profile['empComplexDesc'];
			$this->EyeColor		= $profile['empEyeColorDesc'];
			$this->Hair			= $profile['empHairDesc'];
			*/
			$this->Height		= $profile['empHeight'];
			$this->Weight		= $profile['empWeight'];
			$this->CitizenCd	= $profile['empCitizenCd'];
			$this->Religion		= $profile['empReligion'];
			$this->BloodType	= $profile['empBloodType'];
			
			//ID No. Tab
			$this->SSS			= $profile['empSssNo'];
			$this->PhilHealth	= $profile['empPhicNo'];
			$this->TIN			= $profile['empTin'];
			$this->HDMF			= $profile['empPagibig'];
			$this->bank			= $profile['empBankCd'];
			$this->bankAcctNo	= $profile['empAcctNo'];
			
			//Employment Tab
			$this->DepCode		= $profile['empDepCode'];
			$this->divCode		= $profile['empDiv'];
			$this->secCode		= $profile['empSecCode'];
			$this->RestDay		= $profile['empRestDay'];
			$this->divCode		= $profile['empDiv'];
			$this->secCode		= $profile['empSecCode'];
			$this->Status		= $profile['empStat'];
			$this->Group		= $profile['empPayGrp'];
			$this->Effectivity	= $profile['dateHired'];
			$this->Regularization=$profile['dateReg'];
			$this->EndDate		= $profile['empEndDate'];
			$this->RSDate		= $profile['dateResigned'];
			$this->prevtag		= $profile['empPrevTag'];
			$this->empRank		= $profile['empRank'];
			$this->empStat		= $profile['status'];
			//Payroll Tab
			$this->Salary		= $profile['empMrate'];
			$this->Drate		= $profile['empDrate'];
			$this->Hrate		= $profile['empHrate'];			
			$this->PStatus		= $profile['empPayType'];
			$this->Exemption	= $profile['empTeu'];
			$this->paycat		= $profile['empPayCat'];
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
					empAddr3='".str_replace("'","''",stripslashes(strtoupper($this->Addr3)))."',
					empCityCd='{$this->City}',
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
					";		
					/* 
					Removed from previous by Nhomer requester by HR with document
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
					employmentTag='{$this->empStat}',
					empPayGrp='{$this->Group}',
					dateHired='{$this->Effectivity}',
					$regfield
					$edfield
					$rsfield
					empPrevTag='{$this->prevtag}',
					annualTag='{$this->prevtag}',
					";
		
		$payfields="
					empMrate='".str_replace("'","''",stripslashes(strtoupper($this->Salary)))."',
					empPayType='{$this->PStatus}',
					empTeu='{$this->Exemption}',
					empDrate='{$this->Drate}',
					empHrate='{$this->Hrate}',
					empRank='{$this->empRank}',
					empPayCat='{$this->paycat}',
					";
		$userfields="
					userUpdated='".$_SESSION['user_id']."',
					dateUpdated='".date('Y-m-d')."'
					";	
		$qryupdateemp="Update tblEmpMast set $genfields $confields $perfields $idfields $empfields $payfields $userfields where empNo='$empNox'  and compCode='{$this->oldcompCode}'";
		$qryUpdatePrevEmp = "Update tblPrevEmployer set compCode='{$this->compCode}' where empNo='{$this->strprofile}' and compCode='{$this->oldcompCode}'";

		$qryUpdateUserDefMst = "Update tblUserDefinedMst set compCode='{$this->compCode}' where empNo='{$this->strprofile}' and compCode='{$this->oldcompCode}'";
		$resUpdatePrevEmp = $this->execQry($qryUpdatePrevEmp);
		$resUpdateUserDefMst = $this->execQry($qryUpdateUserDefMst);
		$resupdateemp = $this->execQry($qryupdateemp);
		$arrBio = $this->getBio($this->empNo,$this->compCode);
		if ($arrBio['bioNumber'] != "") 
			$qrybio = "update tblBioEmp set locCode = {$this->location},bioNumber ='{$this->bio}'  where empNo={$this->empNo} and compCode = {$this->compCode}";
		else
			$qrybio = "Insert into tblBioEmp (compCode,locCode,bioNumber,empNo,bioStat) values ({$this->compCode},{$this->location},'{$this->bio}','{$this->empNo}','A')";		
		
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
		if ($field != "empNo") {
			if ($type=="1") {
				$sqlcheck="Select * from $table where $field='$value' and $field<>'' and  empStat IN ('RG','PR','CN')";
			}
			elseif ($type=="0") {
				$sqlcheck="Select * from $table where $field='$value' and $field<>'' and not empNo='" . $_SESSION['strprofile'] . "' and  empStat IN ('RG','PR','CN')";
			}
			$res=$this->execQry($sqlcheck);
		} else {
			if ($type=="1") {
				$sqlcheck="Select Distinct * from tblEmpMast where $field='$value' and $field<>''";
			}
			elseif ($type=="0") {
				$sqlcheck="Select Distinct * from tblEmpMast where $field='$value' and $field<>'' and not empNo='" . $_SESSION['strprofile'] . "' ";
			}
			$res=$this->execQry($sqlcheck);
			if ($this->getRecCount($res)==0) {
				if ($type=="1") {
					$sqlcheck="Select Distinct * from tblEmpMast_New where $field='$value' and $field<>''";
				}
				elseif ($type=="0") {
					$sqlcheck="Select Distinct * from tblEmpMast_New where $field='$value' and $field<>'' and not empNo='" . $_SESSION['strprofile'] . "' ";
				}
				$res=$this->execQry($sqlcheck);
			}
		}		
		return $this->getSqlAssoc($res);
	}
	
	function checkblacklist($arrSSS) {
		$sqlbl="Select * from tblBlacklistedEmp where (Replace(empSssNo,'-','')='{$arrSSS[0]}') or (empLastName='{$arrSSS[1]}' and empFirstName='{$arrSSS[2]}' and empMidName='{$arrSSS[3]}') ";
		$res=$this->execQry($sqlbl);
		return $this->getArrRes($res);
	}
	
	function updateuser() {
		$qryupdatuer = "Update tblusers set compCode='{$this->compCode}' where empNo='{$this->empNo}' and compCode='{$this->oldcompCode}'";
		$res=$this->execQry($qryupdatuer);
	}
	
	function restday($empNo,$compCode,$restday) {
		$qryrestday = "Update tblempmast set empRestDay='$restday' where empNo='$empNo' and compCode='$compCode'";
		return $this->execQry($qryrestday);
	}
	function getBio($empNo,$compCode) {
		$qryBio = "Select bioNumber from tblBioEmp where empNo='$empNo' and compCode='$compCode'";
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

	function getProcGrp() {
		$sqlGrp = "Select payGrp from tblProcGrp where compCode='{$_SESSION['company_code']}' and status='A'";
		$res = $this->getSqlAssoc($this->execQry($sqlGrp));		
		return $res['payGrp'];
		
	}

//	function getResignReason($empno,$compcode){
//		$sql="Select * from tblResignReason where empNo='{$empno}' and compCode='{$compcode}'";
//		$res=$this->getSqlAssoc($this->execQry($sql));
//		return $res['reason'];
//	}
	
	
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
				if($res['employmentTag']=="PR"){
					$empstat="Probationary";		
				}
				elseif($res['employmentTag']=="CN"){
					$empstat="Contractual";		
				}
				elseif($res['employmentTag']=="RG"){
					$empstat="Regular";		
				}			
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
					alert('$label is already used by $name \\nSTATUS = $empstat');
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
			if($res['employmentTag']=="PR"){
				$empstat="Probationary";		
			}
			elseif($res['employmentTag']=="CN"){
				$empstat="Contractual";		
			}
			elseif($res['employmentTag']=="RG"){
				$empstat="Regular";		
			}
			if ($res[$_GET['field']]!="") {
				echo "alert('$label is already used by $name \\nSTATUS = $empstat');";
				echo "$('$txt').value='1';";
//				echo "	
//					if ($('$txt').value!='1') {
//						alert('$label is already used by $name \\nSTATUS = $empstat');
//					}
//					";
//				echo "$('$txt').value='1';";
			}	
			else {
				echo "$('$txt').value='';";
			}
		}		
}
?>