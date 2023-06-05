<?
##################################################

session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("empmast_migration.obj.php");
define('DOWNLOAD_PATH',  SYS_NAME.'/payroll/modules/migration/errors');

$migEmpMastObj = new migEmpMastObj();
$sessionVars = $migEmpMastObj->getSeesionVars();
$migEmpMastObj->validateSessions('','MODULES');


if(isset($_POST['btnUpload'])) {
	if ($error == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["fileUpload"]["tmp_name"];
		if($tmp_name!=""){
			$name = $_FILES["fileUpload"]["name"];
			$size = $_FILES["fileUpload"]["size"];				
			move_uploaded_file($tmp_name, "empmast.mdb");
			instoEmpMast();
			$rep_fileName = "updated_empmast_migration_pdf.php?empLocCode=".$_POST["locType"]."&empBrnCode=".$_POST["empBrnCode"]."&w_Script=".$_POST["btnScript"];
		}
		else{
			echo "<script language='javascript'>alert('Select the file to be Uploaded.');</script>";
		}
	}
}

function instoEmpMast()
{
	extract($GLOBALS);
	global $noError;
	
	
	include("../../../includes/adodb/adodb.inc.php");
	$db =& ADONewConnection('access');
	$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".realpath("empmast.mdb");
	$db->Connect($dsn,'','');
	
	$qryGetData = " SELECT 
					HRMASTER.[EMPLOYEE ID#] as empNo, 
					HRMASTER.[LAST NAME] as empLastName, 
					HRMASTER.[FIRST NAME] as empFirstName, 
					HRMASTER.[MIDDLE NAME] as empMidName, 
					HRMASTER.[CURRENT ADDRESS1] as empAddr1, 
					HRMASTER.[CURRENT ADDRESS2] as empAddr2, 
					HRMASTER.[CURRENT PHONE] as contactCd, 
					HRMASTER.GENDER as empGender, 
					HRMASTER.[BIRTH DATE] as empBDate, 
					HRMASTER.[PLACE OF BIRTH] as empBplace, 
					HRMASTER.HEIGHT as empHeight, 
					HRMASTER.[WEIGHT IN LBS#] as empWeight, 
					HRMASTER.[MARITAL STATUS] as empMarStat, 
					HRMASTER.[BLOOD TYPE] as empBloodType, 
					HRMASTER.[SSS NUMBER] as empSssNo, 
					HRMASTER.TIN as empTin,
					HRMASTER.[PAG-IBIG NUMBER] as empPagNo, 
					HRMASTER.[LANGUAGE SPOKEN] as empLanguage, 
					HRMASTER.[DRIVER'S LICENSE NO#] as empLicenseNo, 
					HRMASTER.[HOBBIES AND SPORTS] as empOtherInfo, 
					HRMASTER.[EMPLOYMENT TYPE] as empType, 
					HRMASTER.[DATE EMPLOYED] as empdateHired, 
					HRMASTER.[DATE PERMANENT] as dateReg, 
					HRMASTER.[DATE TERMINATED] as empEndDate, 
					HRMASTER.[EMPLOYMENT STATUS] as empStat, 
					HRMASTER.[CURRENT SALARY] as empMrate, 
					HRMASTER.[SALARY RATE TYPE] as empRateType, 
					HRMASTER.[GRADE LEVEL] as empLevel, 
					HRMASTER.[CURRENT POSITION] as empPosId, 
					HRMASTER.[CURRENT DEPARTMENT] as empDepCode, 
					HRMASTER.[CURRENT DIVISION] as empDiv, 
					HRMASTER.[CURRENT SECTION] as empSecCode, 
					HRMASTER.[CURRENT PLACE ASSIGNED] as empLocCode, 
					HRMASTER.[BANK NAME] as empBankName, 
					HRMASTER.[BANK ACCT#] as empBankNo, 
					HRMASTER.[PAY CODE] as empBranchCode, 
					HRMASTER.[TAX STATUS] as empTeu, 
					HRMASTER.[PH NUMBER] as empPHICNo, 
					HRMASTER.[FINGERPRINT ID] as empbioNum, 
					empmas.[PREVIOUS EMPLOYER] as empPrevCompName,
					empmas.[PREVIOUS TIN] as empPrevTin,
					empmas.[GROSS TO PREV# EMPLOYER] as empPrevGross, 
					empmas.[PREVIOUS EMPLOYER ADDRESS] as empPrevAdd,
					empmas.[WITHHELD TO PREV# EMP#] as empPrevWithTax, 
					empmas.[PREVIOUS NT SALARIES] as empPrevNtSalaries,
					empmas.[PREVIOUS SSS/HDMF/PH] as empPrevSSSHdmfPag, 
					empmas.[PREVIOUS TAXED 13TH/BONUS] as empPrevTax13th,
					empmas.[PREVIOUS NT 13TH/BONUS] as empPrevNT13th,
					empmas.[PREVIOUS TAXED OTHER INC] as empPrevTOtherInc, 
					empmas.[PREVIOUS NT OTHER INC] as empPrevNtOtherInc,
					empmas.[PREVIOUS EMPLOYER DATE] as empPrevEmpDate,
					empmas.[FILE STATUS] as empFStatus,
					empmas.[PAY CODE] as empPCode
					FROM empmas INNER JOIN HRMASTER ON empmas.[EMPLOYEE ID#] = HRMASTER.[EMPLOYEE ID#]
					";



	$rsGetData = $db->Execute($qryGetData);
	$count =  $rsGetData->RecordCount();
	

	$error_log = 0;
	
	$output_err = "List of Errors"."\r\n";
	$duplicateEmpNo = strtoupper("Duplicate Employee Nos. :")."\r\n";
	$noLastName= strtoupper("Employees with No Last Name :")."\r\n";
	$noFirstName = strtoupper("Employees with No First Name :")."\r\n";
	$noMidName = strtoupper("Employees with No Middle Name :")."\r\n";
	$noStartDate = strtoupper("Employees with No Start Date :")."\r\n";
	$noempStat =  strtoupper("Employees with No Employment Status :")."\r\n";
	$noTaxExempt = strtoupper("Employees with No Tax Code :")."\r\n";
	$noTinNo  = strtoupper("Employees with No TIN No. :")."\r\n";
	$noSssNo = strtoupper("Employees with Wrong or No Sss No :")."\r\n";
	$noPagNo = strtoupper("Employees with No Pag Ibig No :")."\r\n";
	$noPHicNo = strtoupper("Employees with No PHIC No. :")."\r\n";
	$noBank = strtoupper("Employees with No Bank Name :")."\r\n";
	$noBanNo = strtoupper("Employees with No Bank No :")."\r\n";
	$noGrp = strtoupper("Employees with Pay Code :")."\r\n";
	$noPayType = strtoupper("Employees with No Pay Type or Employee Salary Rate :")."\r\n";
	$noPayCatType = strtoupper("Employees with No Pay Cat :")."\r\n";
	$noMRate = strtoupper("Employees with No Monthly Rate :")."\r\n";
	$noprevAdd = strtoupper("Employees with Previous Employer but with no Previous Employer Address : ")."\r\n";
	$noprevTin = strtoupper("Employees with Previous Employer but with no Previous Employer TIN : ")."\r\n";
	$dupFingerId = strtoupper("Employees with Duplicate Biometrics Id : ")."\r\n";
	$noFingerId =  strtoupper("Employees with No Biometrics Id : ")."\r\n";
	$dupFingerIdAcc = strtoupper("Employees with Duplicate Biometrics Id in the .mdb File : ")."\r\n";
	
	if ($count>0) 
	{
		//Delete Record
		$delupdatedtblEmpMast = $migEmpMastObj->delTblEmpMastParadox($_POST["empBrnCode"], $_POST["locType"]=='S'?$_POST["empBrnCode"]:"0001");
		if($delupdatedtblEmpMast==1)
		{
			while(!$rsGetData->EOF)
			{
				$compCode = 2;
				$chkEmpNo = ($rsGetData->fields['empNo']!=""?$migEmpMastObj->checkDuplicateEmpNo($rsGetData->fields['empNo'],$compCode):"") ;
				
				//Check Duplicate Entry of Employee Number Emp Mast
				
				$empNo =  $rsGetData->fields['empNo'];
				if($rsGetData->fields['empLastName']!=""){
					$empLastName = strtoupper($rsGetData->fields['empLastName']);
				}else{
					$noLastName.="(".$compCode.") ".$rsGetData->fields['empNo']."\r\n";
					$error_log = 1;
				}
				
				
				if($rsGetData->fields['empFirstName']!=""){
					$empFirstName = strtoupper($rsGetData->fields['empFirstName']);
				}else{
					$noFirstName.="(".$compCode.") ".$rsGetData->fields['empNo']."\r\n";
					$error_log = 1;
				}
				
				if($rsGetData->fields['empMidName']!=""){
					$empMidName = strtoupper($rsGetData->fields['empMidName']);
				}else{
					$noMidName.="(".$compCode.") ".$rsGetData->fields['empNo']."\r\n";
					$error_log = 1;
				}
				
				$empLocCode = ($_POST["locType"]=='S'?$_POST["empBrnCode"]:"0001");
				$empBrnCode = $_POST["empBrnCode"];
				
				/*Division Code*/
				$empDiv = $migEmpMastObj->getEmpDivCode($compCode, $rsGetData->fields['empDiv']);
				
				/*Dept Code*/
				$empDepCode = $migEmpMastObj->getEmpDeptCode($compCode, $rsGetData->fields['empDepCode']);;
				$empSecCode = "0";
				$empPosId = "0";
				
				if($rsGetData->fields['empdateHired']!=""){
					$empdateHired = date("m/d/Y", strtotime($rsGetData->fields['empdateHired']));
				}else{
					$noStartDate.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				$empdateHired = ($empdateHired!=""?$empdateHired:"");
				
				$empEndDate = ($rsGetData->fields['empEndDate']!=""?"'".date("m/d/Y", strtotime($rsGetData->fields['empEndDate']))."'":"NULL");
				
				if($rsGetData->fields['empStat']!=""){
					$empStat = $migEmpMastObj->getempStatDef($rsGetData->fields['empStat']);
					
					if($rsGetData->fields['empEndDate']!="")
					{
						$dateTest = "01/24/2010";
						if($rsGetData->fields['empEndDate']>= date("Y-m-d", strtotime($dateTest)))
						{
							$empStat = 'RG';
							$empEndDate = "NULL";
						}else{
							if(($rsGetData->fields['empFStatus']=='SEPARATED')&&(substr($rsGetData->fields['empPCode'],0,strlen($rsGetData->fields['empPCode'])-2)=='R'))
							{
								$empStat = 'RS';
							}
							else
							{
								if(($rsGetData->fields['empFStatus']=='EMPLOYEE')&&(substr($rsGetData->fields['empPCode'],0,strlen($rsGetData->fields['empPCode'])-2)=='R'))
								{
									$empStat = 'RS';
								}
								else
								{
									$empStat = 'RG';
									$empEndDate = "NULL";
								}
							}
						}
					}
					
					
				}
				else
				{
					$noempStat.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				
				$empdateReg = ($rsGetData->fields['dateReg']!=""?date("m/d/Y", strtotime($rsGetData->fields['dateReg'])):"");
				
				$empRestDay = $migEmpMastObj->getempRestDay($compCode,$rsGetData->fields['empNo']);; 
				//$empRestDay = "";
				if($rsGetData->fields['empTeu']!=""){
					$empTeu = $rsGetData->fields['empTeu'];
				}else{
					$noTaxExempt.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				if($rsGetData->fields['empTin']!=""){
					$empTinNo = str_replace("-", "", $rsGetData->fields['empTin']);
				}else{
					$noTinNo.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				$empSssNo = ($rsGetData->fields['empSssNo']!=""?$migEmpMastObj->checkSssNo($rsGetData->fields['empSssNo']):0);
				$empSssNo =  str_replace("-", "",$empSssNo);
				if($migEmpMastObj->checkSssNo($rsGetData->fields['empSssNo'])==0){
					$noSssNo.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				if($rsGetData->fields['empPagNo']!=""){
					$empPagibig = str_replace("-", "",$rsGetData->fields['empPagNo']);
				}else{
					$noPagNo.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				
				$emp_BankCd = ($rsGetData->fields['empBankName']!=""?$migEmpMastObj->getBankDef($rsGetData->fields['empBankName'],$compCode):0);
				if($emp_BankCd=='0')
				{
					$noBank.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}else{
					$empBankCd = $emp_BankCd;
				}
				
				
				if($rsGetData->fields['empBankNo']!=""){
					$empAcctNo = $rsGetData->fields['empBankNo'];
				}else{
					$noBanNo.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				$emp_PayGrpBranchCd = ($empBrnCode!=""?$migEmpMastObj->getGroup($empBrnCode,$compCode):0);
				if($emp_BranchCd=='0')
				{
					$noGrp.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}else{
					$empPayGrp = $emp_PayGrpBranchCd;
				}
				
				$empPayType = ($rsGetData->fields['empRateType']!=""?$migEmpMastObj->getRateDef($rsGetData->fields['empRateType']):0);
				
				if($empPayType!=0){
					$noPayType.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}else{
					$empPayType = $empPayType;
				}
				
				if($rsGetData->fields['empType']!=""){
					$empPayCat = $migEmpMastObj->getPayCat($rsGetData->fields['empType'],$compCode);
				}else{
					$noPayCatType.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				$empRankType = $empPayCat;
				$empPayCat = 5;
				
				
				$empAddr1 = ($rsGetData->fields['empAddr1']!=""?str_replace("'","''",stripslashes(strtoupper($rsGetData->fields['empAddr1']))):"");
				$empAddr2 = ($rsGetData->fields['empAddr2']!=""?str_replace("'","''",stripslashes(strtoupper($rsGetData->fields['empAddr2']))):"");
				
				$empMarStat = ($empTeu!=""?$migEmpMastObj->getMarStatDef($empTeu):"UNKNOWN");
				$empSex = ($rsGetData->fields['empGender']!="M"?"F":"M");
				$empBday = ($rsGetData->fields['empBDate']!=""?date("m/d/Y", strtotime($rsGetData->fields['empBDate'])):"");
				
				if($empPayType=='M')
				{
					$empMrate =  ($rsGetData->fields['empMrate']!=""?sprintf("%01.2f",$rsGetData->fields['empMrate']):0);
					$empDrate = ($empMrate!=0?$migEmpMastObj->getComputedDRate($empMrate,$compCode):0);
					$empHRate = ($empDrate!="0"?sprintf("%01.2f",$empDrate/8):0);
				}
				else
				{
					$empDrate = ($rsGetData->fields['empMrate']!=""?sprintf("%01.2f",$rsGetData->fields['empMrate']):0);
					$empMrate =  $empDrate * 26;
					$empHRate = ($empDrate!="0"?sprintf("%01.2f",$empDrate/8):0);
				}
				
				$empOtherInfo = ($rsGetData->fields['empOtherInfo']!=""?strtoupper($rsGetData->fields['empOtherInfo']):"");
				$empNickName = ($rsGetData->fields['empFirstName']!=""?strtoupper($rsGetData->fields['empFirstName']):"");
				$empBplace = ($rsGetData->fields['empBplace']!=""?strtoupper($rsGetData->fields['empBplace']):"");
				$empHeight = ($rsGetData->fields['empHeight']!=""?str_replace("'","`",str_replace('"',"", stripslashes($rsGetData->fields['empHeight']))):"");
				$empWeight = ($rsGetData->fields['empWeight']!=""?round($rsGetData->fields['empWeight'],0):"");
				$empBloodType = ($rsGetData->fields['empBloodType']!=""?strtoupper($rsGetData->fields['empBloodType']):"");
				
				
				if($rsGetData->fields['empPHICNo']!=""){
					$empPhicNo =  str_replace("-", "", $rsGetData->fields['empPHICNo']);
				}else{
					$noPHicNo.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				$arrminWageTag = ($empBrnCode!=""?$migEmpMastObj->getMinWage($empBrnCode,$compCode):0);
				if($empDrate<=$arrminWageTag["minWage"])
					$empMinWageTag = "'Y'";
				else
					$empMinWageTag = "'N'";
				
				
				$empLicenseNo = $rsGetData->fields['empLicenseNo'];
				if($empLicenseNo!=""){
					$insLicenseInfo.= "Insert into tblUserDefinedMst(compCode,empNo,catCode,remarks1,remarks2) VALUES ('".$compCode."','".$empNo."','2','DRIVERS LICENSE','".strtoupper($rsGetData->fields['empLicenseNo'])."');";
				}
				
				$empBioEmp = floor($rsGetData->fields['empbioNum']);
				if($empBioEmp!=""){
					$chkBioNum = $migEmpMastObj->chkBioNum(floor($rsGetData->fields['empbioNum']));
					if($chkBioNum!=1){
						$insBioInfo.="Insert into tblBioEmp(compCode,locCode,bioNumber,empNo,bioStat) Values ('".$compCode."','".$empLocCode."','".floor($rsGetData->fields['empbioNum'])."','".$empNo."','A');";
						}
					else{
						$dupFingerId.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
						$error_log = 1;
					}
				}
				else{
					/*$noFingerId.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;*/
				}
				
				/*Insert Ytd Data*/
				$resEmpYtd = $migEmpMastObj->getEmpYtdData($rsGetData->fields['empNo']);
				$numEmpYtdData = $migEmpMastObj->getRecCount($resEmpYtd);
				
				if($numEmpYtdData>0)
				{
					$arrEmpYtd = $migEmpMastObj->getSqlAssoc($resEmpYtd);
					$qryInsYtdData.= "Insert into tblYtdDataHist(compCode,pdYear,empNo,YtdGross,YtdTaxable,YtdGovDed,YtdTax,payGrp,pdNumber,YtdBasic)
									  values(
									  '".$compCode."',
									  '".$arrEmpYtd["pdYear"]."',
									  '".$arrEmpYtd["empNo"]."',
									  '".sprintf("%01.2f",$arrEmpYtd["ytdGross"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["ytdTaxable"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["ytdGovDed"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["ytdTax"])."',
									  '".$empPayGrp."',
									  '2',
									  '".sprintf("%01.2f",$arrEmpYtd["ytdBasic"])."')";
					$error_log = 0;
				}
				
				if($rsGetData->fields['empPrevCompName']!=""){
					
					$prevCompCode = $compCode;
					$prevEmpNo = $rsGetData->fields['empNo'];
					$prevEmployer = strtoupper($rsGetData->fields['empPrevCompName']);
					
					if($rsGetData->fields['empPrevAdd']){
						$prevEmpAdd = strtoupper($rsGetData->fields['empPrevAdd']);
					}else{
						$noprevAdd.= "(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
						$error_log = 1;
					}
					
					if($rsGetData->fields['empPrevTin']){
						$prevEmpTin = $rsGetData->fields['empPrevTin'];
					}else{
						$noprevTin.= "(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
						$error_log = 1;
					}
					
					if($rsGetData->fields['empPrevGross']){
						$prevEmpEarnings = sprintf("%01.2f",$rsGetData->fields['empPrevGross']);
					}else{
						$prevEmpEarnings =0;
					}
					
					
					if($rsGetData->fields['empPrevTOtherInc']){
						$prevEmpEarnings+= sprintf("%01.2f",$rsGetData->fields['empPrevTOtherInc']);
					}else{
						$prevEmpEarnings =0;
					}
					
					
					if($rsGetData->fields['empPrevWithTax']){
						$prevTaxes= sprintf("%01.2f",$rsGetData->fields['empPrevWithTax']);
					}else{
						$prevTaxes=0;
					}
					
					
					if($rsGetData->fields['empPrevNtSalaries']){
						$prevEmpGrossNonTax = sprintf("%01.2f",$rsGetData->fields['empPrevNtSalaries']);
					}else{
						$prevEmpGrossNonTax =0;
					}
					
					if($rsGetData->fields['empPrevNtOtherInc']){
						$prevEmpGrossNonTax+= sprintf("%01.2f",$rsGetData->fields['empPrevNtOtherInc']);
					}else{
						$prevEmpGrossNonTax=0;
					}
					
					
					if($rsGetData->fields['empPrevNT13th']){
						$prevEmpNonTax13th = sprintf("%01.2f",$rsGetData->fields['empPrevNT13th']);
					}else{
						$prevEmpNonTax13th=0;
					}
					
					if($rsGetData->fields['empPrevSSSHdmfPag']){
						$prevEmpNonTaxSss = sprintf("%01.2f",$rsGetData->fields['empPrevSSSHdmfPag']);
					}else{
						$prevEmpNonTaxSss=0;
					}
					
					
					if($rsGetData->fields['empPrevTax13th']){
						$prevEmpTax13th = sprintf("%01.2f",$rsGetData->fields['empPrevTax13th']);
					}else{
						$prevEmpTax13th=0;
					}
					
					if($rsGetData->fields['empPrevEmpDate']){
						$prevEmpyearCd = date("Y", strtotime($rsGetData->fields['empPrevEmpDate']));
					}
					
					$prevTaxPerMonth = 0;
					$prevTaxDeducted = 0;
					
					$ins_Script_prevEmplr.="Insert into tblPrevEmployer 
					(compCode,empNo,prevEmplr,empAddr1,emplrTin,
					prevEarnings,prevTaxes,prevStat,grossNonTax,
					nonTax13th,nonTaxSss,tax13th,yearCd,
					taxPerMonth,taxDeducted)
					VALUES
					('".$prevCompCode."','".$prevEmpNo."','".$prevEmployer."','".$prevEmpAdd."','".$prevEmpTin."',
					'".$prevEmpEarnings."','".$prevTaxes."','A','".$prevEmpGrossNonTax."',
					'".$prevEmpNonTax13th."','".$prevEmpNonTaxSss."','".$prevEmpTax13th."','".$prevEmpyearCd."',
					'".$prevTaxPerMonth."','".$prevTaxDeducted."');
					";
					
					$prevEmpTag = "'Y'";
					
					
				}
				else
				{
					$prevEmpTag = "NULL";
				}
				
				if($error_log!=1)
				{
					 $ins_Script.= "Insert into tblEmpMast_Paradox(
									compCode,empNo,empLastName,empFirstName,empMidName,
									empLocCode,empBrnCode,empDiv,empDepCode,empSecCode,empPosId,
									dateHired,empStat,dateReg,empTeu,empTin,empSssNo,
									empPagibig,empBankCd,empAcctNo,empPayGrp,empPayType,
									empPayCat,empAddr1,empAddr2,empMarStat,empSex,empBday,
									empMrate,empDrate,empHrate,empOtherInfo,empNickName,
									empBplace,empHeight,empWeight,empBloodType,empEndDate,
									empPhicNo,empRank,empRestDay,empWageTag,empPrevTag) VALUES
									('".$compCode."','".$empNo."','".$empLastName."' ,'".$empFirstName."','".$empMidName."',
									'".$empLocCode."' ,'".$empBrnCode."','".$empDiv."','".$empDepCode."','".$empSecCode."','".$empPosId."',
									'".$empdateHired."','".$empStat."','".$empdateReg."','".$empTeu."','".$empTinNo."','".$empSssNo."',
									'".$empPagibig."','".$empBankCd."','".$empAcctNo."','".$empPayGrp."','".$empPayType."',
									'".$empPayCat."','".$empAddr1."','".$empAddr2."','".$empMarStat."','".$empSex."','".$empBday."',
									'".$empMrate."','".$empDrate."','".$empHRate."','".$empOtherInfo."','".$empFirstName."',
									'".$empBplace."','".$empHeight."','".$empWeight."','".$empBloodType."',".$empEndDate.",'".$empPhicNo."','".$empRankType."'
									,".$empRestDay.",".$empMinWageTag .",".$prevEmpTag.");";
					
					/*$ins_Script.=$insLicenseInfo;
					$ins_Script.=$insBioInfo;
					$ins_Script.=$ins_Script_prevEmplr;
					$ins_Script.=$qryInsYtdData;*/
					
					$no_emp++;
				}
				
				$rsGetData->MoveNext();
				unset($compCode,$chkEmpNo,$empNo, $empLastName, 
				$empFirstName, $empMidName, $empLocCode, $empBrnCode, $empDiv, $empDepCode, $empSecCode, $empPosId, 
				$empdateHired,$empStat,$empdateReg, $empRestDay, $empTeu,$empTinNo, $empSssNo, $empPagibig, $emp_BankCd,
				$empBankCd, $empAcctNo, $emp_PayGrpBranchCd,$empPayGrp, $empPayType,$empPayCat, $empAddr1, $empAddr2,
				$empMarStat, $empSex, $empBday, $empMrate, $empDrate,
				$empHRate, $empOtherInfo, $empNickName, $empBplace, $empHeight, $empWeight, $empBloodType, $empEndDate, 
				$empPhicNo,$empLicenseNo, $empBioEmp,$insLicenseInfo,$insBioInfo,$ins_Script_prevEmplr,
				$prevCompCode,$prevEmpNo,$prevEmployer,$prevEmpAdd,$prevEmpTin,
				$prevEmpEarnings,$prevTaxes,$prevEmpGrossNonTax,$prevEmpNonTax13th,
				$prevEmpNonTaxSss,$prevEmpTax13th,$prevEmpyearCd,$prevTaxPerMonth,$prevTaxDeducted,$numEmpYtdData,$qryInsYtdData,$minWageTag);
			}
		}
		else
		{
			echo "<script>alert('Error in Deleting tblEmpMast_Paradox.');</script>";
		}
	}
	
	
	if($error_log==1)
	{
		$output_err.=$duplicateEmpNo."\r\n".$noLastName."\r\n".$noFirstName."\r\n".$noMidName."\r\n".$noStartDate."\r\n".$noempStat."\r\n".$noTaxExempt."\r\n".$noTinNo."\r\n".$noSssNo."\r\n".$noPagNo."\r\n".$noPHicNo."\r\n".$noBank."\r\n".$noBanNo."\r\n".$noPayType."\r\n".$noPayCatType."\r\n".$noMRate."\r\n".$noprevAdd."\r\n".$noprevTin."\r\n".$noFingerId."\r\n".$dupFingerId."\r\n".$dupFingerIdAcc;
		
		if(file_exists($_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '/'.session_id().'-ERROR.txt'))
		{
			unlink($_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '/'.session_id().'-ERROR.txt');
		}

		WriteFile(session_id().'-ERROR.txt', $_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '', $output_err);
		$noError = 1;
		echo "<script>window.open('"."errors/".session_id().'-ERROR.txt'."');</script>";
	}
	else
	{
		$noError = 0;
		
		
		$insQry = $migEmpMastObj->execQry($ins_Script);
		if($insQry)
		{
			echo "<script>alert('$no_emp record/s successfully added to the Employee Master File.');</script>";
		}
	}
	
	
	$db->Close();
	
	unlink("empmast.mdb");
}

function WriteFile($file_name, $str_path, $file_cont)
{
	$fh = fopen($str_path.'/'.$file_name, 'w') or die('can not write file!');
	fwrite($fh, $file_cont);
	fclose($fh);
}

?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/main_emp_loans.css');</style>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='../transactions/timesheet_js.js'></script>
</HEAD>
	<BODY>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="frmTS">
    <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    	<tr>
    		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
    			&nbsp;Upload Updated Employee Master File From Paradox
    		</td>
    	</tr>
    
    	<tr>
    		<td></td>
    	</tr>
    
    	<tr>
    		<td class="parentGridDtl" >
    			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
    					<tr> 
                            <td class="gridDtlLbl">Branch </td>
                            <td class="gridDtlLbl">:</td>
                            <td class="gridDtlVal"> 
                                <? 					
                                    $arrBranch = $migEmpMastObj->makeArr($migEmpMastObj->getBrnchArt($compCode),'brnCode','brnDesc','');
                                    $migEmpMastObj->DropDownMenu($arrBranch,'empBrnCode',$empBrnCode,$empBrnCode_dis);
                                ?>
                            </td>
                        </tr>
                        
                       <tr> 
                            <td class="gridDtlLbl">Location </td>
                            <td class="gridDtlLbl">:</td>
                            <td class="gridDtlVal"> 
                                <?  
                                    $migEmpMastObj->DropDownMenu(array('S'=>'Store','H'=>'Head Office'),'locType',$locType,$locType_dis); 
                                ?>
                            </td>
                        </tr>
                    <tr> 
                        <td width="18%" class="gridDtlLbl">File Name </td>
                        <td width="1%" class="gridDtlLbl">:</td>
                        <td width="81%" class="gridDtlVal"> 
                        	<font size="2" face="Arial, Helvetica, sans-serif">
                        		<input name="fileUpload" type="file" id="fileUpload">
                                <input type="radio" name="btnScript" value="1" <?php echo ($_POST["btnScript"]==1?"checked":""); ?>> With Script
                                <input type="radio" name="btnScript" value="0" <?php echo ($_POST["btnScript"]==0?"checked":""); ?>> Without Script
                                <input name="btnUpload" type="submit" id="btnUpload" value="Upload" onClick="valUpload();" class="inputs">
                                
                            </font> 
                        </td>
    				</tr>
    			</table>
    			<br>
    			<iframe src="<?php echo $rep_fileName; ?>" height="380px;" width="99%">
                	 
                </iframe>
               
    		</td>
    	</tr> 
    	<tr > 
    		<td class="gridToolbarOnTopOnly" colspan="6">
    			<CENTER>
    				<input style="background-color:#c3daf9; height:18px; text-align: center;  border:0px solid;" >
    			</CENTER>	
    		</td>
    	</tr>
    </table>
</form>
</BODY>
</HTML>
