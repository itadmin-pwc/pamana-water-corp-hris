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



function instoEmpMast()
{
	extract($GLOBALS);
	global $noError;
	
	
	include("../../../includes/adodb/adodb.inc.php");
	$db =& ADONewConnection('access');
	$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".realpath("empmast.mdb");
	$db->Connect($dsn,'','');
	
	$selBrnCode = $_POST["empBrnCode"];
	
	
	switch($selBrnCode)
	{
		/*
			case "35":
			$tblLookUp = "[HRMASTER agora]";
			$tblLookUp_EmpMast = "[EMPMAS agora]";
		break;
		
		case "63":
			$tblLookUp = "[HRMASTER angeles]";
			$tblLookUp_EmpMast = "[EMPMAS angeles]";
		break;
		
		case "59":
			$tblLookUp = "[HRMASTER bacoor]";
			$tblLookUp_EmpMast = "[EMPMAS bacoor]";
		break;
		
		case "66":
			$tblLookUp = "[HRMASTER baguio]";
			$tblLookUp_EmpMast = "[EMPMAS baguio]";
		break;
		
		case "31":
			$tblLookUp = "[HRMASTER baliuag]";
			$tblLookUp_EmpMast = "[EMPMAS baliuag]";
		break;
		
		case "23":
			$tblLookUp = "[HRMASTER binan]";
			$tblLookUp_EmpMast = "[EMPMAS binan]";
		break;
		
		case "36":
			$tblLookUp = "[HRMASTER CALOOCAN]";
			$tblLookUp_EmpMast = "[EMPMAS CALOOCAN]";
		break;
		
		case "24":
			$tblLookUp = "[HRMASTER commonwealth]";
			$tblLookUp_EmpMast = "[EMPMAS commonwealth]";
		break;
		
		case "25":
			$tblLookUp = "[HRMASTER cubao]";
			$tblLookUp_EmpMast = "[EMPMAS cubao]";
		break;
		
		case "39":
			$tblLookUp = "[HRMASTER dau]";
			$tblLookUp_EmpMast = "[EMPMAS dau]";
		break;
		
		
		
		case "20":
			$tblLookUp = "[HRMASTER las pinas]";
			$tblLookUp_EmpMast = "[EMPMAS las pinas]";
		break;
		
		case "30":
			$tblLookUp = "[HRMASTER libertad]";
			$tblLookUp_EmpMast = "[EMPMAS libertad]";
		break;
		
		case "21":
			$tblLookUp = "[HRMASTER makati]";
			$tblLookUp_EmpMast = "[EMPMAS makati]";
		break;
		
		case "34":
			$tblLookUp = "[HRMASTER meycauayan]";
			$tblLookUp_EmpMast = "[EMPMAS meycauayan]";
		break;
		
		case "62":
			$tblLookUp = "[HRMASTER novaliches]";
			$tblLookUp_EmpMast = "[EMPMAS novaliches]";
		break;
		
		case "60":
			$tblLookUp = "[HRMASTER paco]";
			$tblLookUp_EmpMast = "[EMPMAS paco]";
		break;
		
		case "32":
			$tblLookUp = "[HRMASTER qi]";
			$tblLookUp_EmpMast = "[EMPMAS qi]";
		break;
		
		case "50":
			$tblLookUp = "[HRMASTER san mateo]";
			$tblLookUp_EmpMast = "[EMPMAS san mateo]";
		break;
		
		case "49":
			$tblLookUp = "[HRMASTER san pablo]";
			$tblLookUp_EmpMast = "[EMPMAS san pablo]";
		break;
		
		case "33":
			$tblLookUp = "[HRMASTER taytay]";
			$tblLookUp_EmpMast = "[EMPMAS taytay]";
		break;
		
		case "40":
			$tblLookUp = "[HRMASTER valenzuela]";
			$tblLookUp_EmpMast = "[EMPMAS valenzuela]";
		break;
		
		case "45":
			$tblLookUp = "[HRMASTER CAINTA]";
			$tblLookUp_EmpMast = "[EMPMAS CAINTA]";
		break;
		
		case "43":
			$tblLookUp = "[HRMASTER DIVISORIA]";
			$tblLookUp_EmpMast = "[EMPMAS DIVISORIA]";
		break;
		
		case "42":
			$tblLookUp = "[HRMASTER GMA]";
			$tblLookUp_EmpMast = "[EMPMAS GMA]";
		break;
		
		case "22":
			$tblLookUp = "[HRMASTER IMUS]";
			$tblLookUp_EmpMast = "[EMPMAS IMUS]";
		break;
		
		case "29":
			$tblLookUp = "[HRMASTER KALENTONG]";
			$tblLookUp_EmpMast = "[EMPMAS KALENTONG]";
		break;
		
		case "28":
			$tblLookUp = "[HRMASTER MALATE]";
			$tblLookUp_EmpMast = "[EMPMAS MALATE]";
		break;
		
		case "41":
			$tblLookUp = "[HRMASTER MALOLOS]";
			$tblLookUp_EmpMast = "[EMPMAS MALOLOS]";
		break;
		
		case "38":
			$tblLookUp = "[HRMASTER PARANAQUE]";
			$tblLookUp_EmpMast = "[EMPMAS PARANAQUE]";
		break;
		
		case "26":
			$tblLookUp = "[HRMASTER PASIG]";
			$tblLookUp_EmpMast = "[EMPMAS PASIG]";
		break;
		
		case "27":
			$tblLookUp = "[HRMASTER SAN PEDRO]";
			$tblLookUp_EmpMast = "[EMPMAS SAN PEDRO]";
		break;
		
		case "3":
			$tblLookUp = "[HRMASTER SHAW]";
			$tblLookUp_EmpMast = "[EMPMAS SHAW]";
		break;
		
		case "46":
			$tblLookUp = "[HRMASTER STA MESA]";
			$tblLookUp_EmpMast = "[EMPMAS STA MESA]";
		break;
		
		case "44":
			$tblLookUp = "[HRMASTER SUCAT]";
			$tblLookUp_EmpMast = "[EMPMAS SUCAT]";
		break;
		
		case "37":
			$tblLookUp = "[HRMASTER TAYUMAN]";
			$tblLookUp_EmpMast = "[EMPMAS TAYUMAN]";
		break;
		
		case "48":
			$tblLookUp = "[HRMASTER ZABARTE]";
			$tblLookUp_EmpMast = "[EMPMAS ZABARTE]";
		break;
		
		*/
		case "0001":
			$tblLookUp = "[HRMASTER HEAD OFFICE]";
			$tblLookUp_EmpMast = "[EMPMAS HEAD OFFICE]";
		break;
		
		case "11":
			$tblLookUp = "[HRMASTER SUBIC]";
			$tblLookUp_EmpMast = "[EMPMAS SUBIC]";
		break;
		
		case "04":
			$tblLookUp = "[HRMASTER CLARK]";
			$tblLookUp_EmpMast = "[EMPMAS CLARK]";
		break;
		
		
			case "67":
			$tblLookUp = "[HRMASTER BALINTAWAK]";
			$tblLookUp_EmpMast = "[EMPMAS BALINTAWAK]";
		break;
		
			case "51":
			$tblLookUp = "[HRMASTER ZAPOTE]";
			$tblLookUp_EmpMast = "[EMPMAS ZAPOTE]";
		break;
		
			case "52":
			$tblLookUp = "[HRMASTER EDSA]";
			$tblLookUp_EmpMast = "[EMPMAS EDSA]";
		break;
		
			case "53":
			$tblLookUp = "[HRMASTER BLUMENTRITT]";
			$tblLookUp_EmpMast = "[EMPMAS BLUMENTRITT]";
		break;
		
			case "54":
			$tblLookUp = "[HRMASTER BOCOBO]";
			$tblLookUp_EmpMast = "[EMPMAS BOCOBO]";
		break;
		
			case "55":
			$tblLookUp = "[HRMASTER TAGAYTAY]";
			$tblLookUp_EmpMast = "[EMPMAS TAGAYTAY]";
		break;
		
			case "64":
			$tblLookUp = "[HRMASTER BF HOMES]";
			$tblLookUp_EmpMast = "[EMPMAS BF HOMES]";
		break;
		
			case "67":
			$tblLookUp = "[HRMASTER BALINTAWAK]";
			$tblLookUp_EmpMast = "[EMPMAS BALINTAWAK]";
		break;
		
			case "68":
			$tblLookUp = "[HRMASTER ST FRANCIS]";
			$tblLookUp_EmpMast = "[EMPMAS ST FRANCIS]";
		break;
		
			case "71":
			$tblLookUp = "[HRMASTER BALIBAGO]";
			$tblLookUp_EmpMast = "[EMPMAS BALIBAGO]";
		break;
		
		
			case "72":
			$tblLookUp = "[HRMASTER STARMALL ANNEX]";
			$tblLookUp_EmpMast = "[EMPMAS STARMALL ANNEX]";
		break;
		
			case "74":
			$tblLookUp = "[HRMASTER CANLUBANG]";
			$tblLookUp_EmpMast = "[EMPMAS CANLUBANG]";
		break;
		
			case "77":
			$tblLookUp = "[HRMASTER QUEZON AVE]";
			$tblLookUp_EmpMast = "[EMPMAS QUEZON AVE]";
		break;
		
			case "78":
			$tblLookUp = "[HRMASTER TANAUAN]";
			$tblLookUp_EmpMast = "[EMPMAS TANAUAN]";
		break;
		
			case "84":
			$tblLookUp = "[HRMASTER BERVILLE]";
			$tblLookUp_EmpMast = "[EMPMAS BERVILLE]";
		break;
		
			case "85":
			$tblLookUp = "[HRMASTER MOTHER IGNACIA]";
			$tblLookUp_EmpMast = "[EMPMAS MOTHER IGNACIA]";
		break;
		
			case "86":
			$tblLookUp = "[HRMASTER SANJOSE]";
			$tblLookUp_EmpMast = "[EMPMAS SANJOSE]";
		break;

			case "87":
			$tblLookUp = "[HRMASTER SAN FERNANDO]";
			$tblLookUp_EmpMast = "[EMPMAS SAN FERNANDO]";
		break;
		
			case "93":
			$tblLookUp = "[HRMASTER BETTER LIVING]";
			$tblLookUp_EmpMast = "[EMPMAS BETTER LIVING]";
		break;
		
			case "94":
			$tblLookUp = "[HRMASTER MARIKINA]";
			$tblLookUp_EmpMast = "[EMPMAS MARIKINA]";
		break;

		
		
		default:
			$tblLookUp = "[hrmaster]";
			$tblLookUp_EmpMast = "[p_empmas]";
		break;
	}
	
	// $array_payCode = array('P03'=>'03');
	
	/*$array_payCode = array('P51'=>'51',
							'P52'=>'52',
							'P53'=>'53',
							'P54'=>'54',
							'P64'=>'64',
							'P67'=>'67',
							'P68'=>'68',
							'P71'=>'71',
							'P72'=>'72',
							'P74'=>'74',
							'P77'=>'77',
							'P78'=>'78',
							'P84'=>'84',
							'P85'=>'85');*/
	
	$array_payCode = array('L01'=>'0001',
							'P01'=>'0001',
							'P04'=>'04',
							'P51'=>'0001',
							'P52'=>'0001',
							'P53'=>'53',
							'P54'=>'54',
							'P55'=>'55',
							'P64'=>'64',
							'P67'=>'67',
							'P68'=>'68',
							'P71'=>'71',
							'P72'=>'72',
							'P74'=>'74',
							'P77'=>'77',
							'P78'=>'78',
							'P84'=>'84',
							'P85'=>'85',
							'P86'=>'86',
							'P87'=>'87',
							'P93'=>'93',
							'P94'=>'94',
							'P11'=>'0001',
							'S11'=>'0001',
							);
							
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
	
	
	foreach($array_payCode as $array_payCode_val=>$index)
	{
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
					FROM ".$tblLookUp_EmpMast." as empmas INNER JOIN ".$tblLookUp." as HRMASTER ON empmas.[EMPLOYEE ID#] = HRMASTER.[EMPLOYEE ID#]
					WHERE HRMASTER.[PAY CODE]='".$array_payCode_val."';";
					
					$rsGetData = $db->Execute($qryGetData);
					$count =  $rsGetData->RecordCount();
					
					
					if ($count>0) 
					{
						$no_emp = 0;
						while(!$rsGetData->EOF)
						{
							$compCode = $_SESSION["company_code"];
							$chkEmpNo = ($rsGetData->fields['empNo']!=""?$migEmpMastObj->checkDuplicateEmpNo($rsGetData->fields['empNo'],$compCode):"") ;
							
							//Check Duplicate Entry of Employee Number Emp Mast
							if($chkEmpNo=='1')
							{
								$duplicateEmpNo.= "(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
								$error_log = 1;
							}
							else
							{
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
									//$error_log = 1;
								}
								
								
								$empLocCode = ($index=='0001'?'0001':$index);
								$empBrnCode = $index;
								
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
								if($rsGetData->fields['empStat']!=""){
									$empStat = $migEmpMastObj->getempStatDef($rsGetData->fields['empStat']);
									
									if($rsGetData->fields['empEndDate']!="")
									{
										
										$dateTest = date("m/d/Y", strtotime($_POST["monthfr"]));
												
										if($rsGetData->fields['empEndDate']>= date("Y-m-d", strtotime($dateTest)))
										{
											$empStat = 'RG';
											$empEndDate = "NULL";
										}else{
											
											if(($rsGetData->fields['empFStatus']=='SEPARATED')&&(substr($rsGetData->fields['empPCode'],0,strlen($rsGetData->fields['empPCode'])-2)=='R'))
											{
												$empStat = 'RS';
												$empEndDate = ($rsGetData->fields['empEndDate']!=""?"'".date("m/d/Y", strtotime($rsGetData->fields['empEndDate']))."'":"NULL");
											}
											else
											{
												
												if(($rsGetData->fields['empFStatus']=='EMPLOYEE')&&(substr($rsGetData->fields['empPCode'],0,strlen($rsGetData->fields['empPCode'])-2)=='R'))
												{
													$empStat = 'RS';
													$empEndDate = ($rsGetData->fields['empEndDate']!=""?"'".date("m/d/Y", strtotime($rsGetData->fields['empEndDate']))."'":"NULL");
											
												}
												else
												{
													$empStat = 'RG';
													$empEndDate = "NULL";
												}
												
											}
											
										}
										
									}
									else
									{
										$empEndDate = "NULL";
									}
									
									
								}
								else
								{
									$noempStat.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
									$error_log = 1;
								}
							
								$empdateReg = ($rsGetData->fields['dateReg']!=""?date("m/d/Y", strtotime($rsGetData->fields['dateReg'])):"");
								$empRestDay = "03/13/2011, 03/20/2011, 03/27/2011";
								
								if($rsGetData->fields['empTeu']!=""){
									$empTeu = $rsGetData->fields['empTeu'];
								}else{
									$noTaxExempt.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
									$error_log = 1;
								}
								
								if($rsGetData->fields['empTin']!=""){
									$empTinNo = $rsGetData->fields['empTin'];
								}else{
									$noTinNo.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
									$error_log = 1;
								}
								
								$empSssNo = ($rsGetData->fields['empSssNo']!=""?$migEmpMastObj->checkSssNo($rsGetData->fields['empSssNo']):0);
								
								if($migEmpMastObj->checkSssNo($rsGetData->fields['empSssNo'])==0){
									$noSssNo.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
									$error_log = 1;
								}
								
								if($rsGetData->fields['empPagNo']!=""){
									$empPagibig = $rsGetData->fields['empPagNo'];
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
								
								//$test_brnCode = substr($rsGetData->fields['empBranchCode'],1,strlen($rsGetData->fields['empBranchCode'])-0);
								
								$empPayGrp = $migEmpMastObj->getGroup($index,$compCode);
								
								$empPayType = ($rsGetData->fields['empRateType']!=""?$migEmpMastObj->getRateDef($rsGetData->fields['empRateType']):0);
								
								if($empPayType!=0){
									$noPayType.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
									$error_log = 1;
								}else{
									$empPayType = $empPayType;
								}
								
								if($rsGetData->fields['empType']!=""){
									$empRankType  = $migEmpMastObj->getRank($rsGetData->fields['empType'],$compCode);
								}else{
									$noPayCatType.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
									$error_log = 1;
								}
								
								$empPayCat = ($empStat!='RS'?$migEmpMastObj->getEmpPayCategory($empRankType):"9");
								
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
								$empBplace = ($rsGetData->fields['empBplace']!=""?strtoupper(str_replace("'",'',$rsGetData->fields['empBplace'])):"");
								$empHeight = ($rsGetData->fields['empHeight']!=""?str_replace("'","",str_replace('"',"", stripslashes($rsGetData->fields['empHeight']))):"");
								$empWeight = ($rsGetData->fields['empWeight']!=""?round($rsGetData->fields['empWeight'],0):"");
								$empBloodType = ($rsGetData->fields['empBloodType']!=""?strtoupper($rsGetData->fields['empBloodType']):"");
								
								
								if($rsGetData->fields['empPHICNo']!=""){
									$empPhicNo =   $rsGetData->fields['empPHICNo'];
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
										$insBioInfo.="Insert into tblBioEmp(compCode,locCode,bioNumber,empNo,bioStat) Values ('".$compCode."','".$empLocCode."','".floor($rsGetData->fields['empbioNum'])."','".$empNo."','A');<br>";
										
										}
									else{
										$dupFingerId.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
										$error_log = 1;
									}
								}
								else{
									$noFingerId.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
									$error_log = 1;
								}
								
								
								
								if($rsGetData->fields['empPrevCompName']!=""){
									
									$prevCompCode = $compCode;
									$prevEmpNo = $rsGetData->fields['empNo'];
									$prevEmployer = strtoupper($rsGetData->fields['empPrevCompName']);
									
									if($rsGetData->fields['empPrevAdd']){
										$prevEmpAdd = strtoupper($rsGetData->fields['empPrevAdd']);
									}else{
										//$noprevAdd.= "(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
										//$error_log = 1;
									}
									
									if($rsGetData->fields['empPrevTin']){
										$prevEmpTin = $rsGetData->fields['empPrevTin'];
									}else{
										//$noprevTin.= "(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
										//$error_log = 1;
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
								
								//if($error_log!=1)
								//{
								$ins_Script.= "Insert into tblEmpMast(
													compCode,empNo,empLastName,empFirstName,empMidName,
													empLocCode,empBrnCode,empDiv,empDepCode,empSecCode,empPosId,
													dateHired,empStat,dateReg,empTeu,empTin,empSssNo,
													empPagibig,empBankCd,empAcctNo,empPayGrp,empPayType,
													empPayCat,empAddr1,empAddr2,empMarStat,empSex,empBday,
													empMrate,empDrate,empHrate,empOtherInfo,empNickName,
													empBplace,empHeight,empWeight,empBloodType,empEndDate,
													empPhicNo,empRank,empRestDay,empWageTag,empPrevTag, dateResigned) VALUES
													('".$compCode."','".$empNo."','".$empLastName."' ,'".$empFirstName."','".$empMidName."',
													'".$empLocCode."' ,'".$empBrnCode."','".$empDiv."','".$empDepCode."','".$empSecCode."','".$empPosId."',
													'".$empdateHired."','".$empStat."','".$empdateReg."','".$empTeu."','".$empTinNo."','".$empSssNo."',
													'".$empPagibig."','".$empBankCd."','".$empAcctNo."','".$empPayGrp."','".$empPayType."',
													'".$empPayCat."','".$empAddr1."','".$empAddr2."','".$empMarStat."','".$empSex."','".$empBday."',
													'".$empMrate."','".$empDrate."','".$empHRate."','".$empOtherInfo."','".$empFirstName."',
													'".$empBplace."','".$empHeight."','".$empWeight."','".$empBloodType."',".$empEndDate.",
													'".$empPhicNo."','".$empRankType."'
													,'".$empRestDay."',".$empMinWageTag .",".$prevEmpTag.",".$empEndDate.");<br><br>";
									/*$ins_Script_license.=$insLicenseInfo;
									$ins_Script_bio.=$insBioInfo;
									$ins_Script_prev.=$ins_Script_prevEmplr;
									$ins_Script.=$qryInsYtdData;
									$ins_Script.=$qryInsPaySum;
									$ins_Script.=$qryInsMtdGovt;
									$ins_Script.=$qryInsAllow;*/
									$no_emp++;
								}
								
							//}
								$rsGetData->MoveNext();
								unset($compCode,$chkEmpNo,$empNo, $empLastName, $test_brnCode,
								$empFirstName, $empMidName, $empLocCode, $empBrnCode, $empDiv, $empDepCode, $empSecCode, $empPosId, 
								$empdateHired,$empStat,$empdateReg, $empRestDay, $empTeu,$empTinNo, $empSssNo, $empPagibig, $emp_BankCd,
								$empBankCd, $empAcctNo, $emp_PayGrpBranchCd,$empPayGrp, $empPayType,$empPayCat, $empAddr1, $empAddr2,
								$empMarStat, $empSex, $empBday, $empMrate, $empDrate,
								$empHRate, $empOtherInfo, $empNickName, $empBplace, $empHeight, $empWeight, $empBloodType, $empEndDate, 
								$empPhicNo,$empLicenseNo, $empBioEmp,
								$prevCompCode,$prevEmpNo,$prevEmployer,$prevEmpAdd,$prevEmpTin,
								$prevEmpEarnings,$prevTaxes,$prevEmpGrossNonTax,$prevEmpNonTax13th,
								$prevEmpNonTaxSss,$prevEmpTax13th,$prevEmpyearCd,$prevTaxPerMonth,$prevTaxDeducted,$numEmpYtdData,$qryInsYtdData,$minWageTag,$qryInsPaySum,$qryInsMtdGovt,$empRankType,$numEmpAllow,$sked,$qryInsAllow);
						}//End of While Loop
					}//End of Count
					
					//echo $ins_Script;
					
					 
					// echo $qryGetData;
	}
					/*if($error_log==1)
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
					{*/
						$noError = 0;
						echo $ins_Script."<br>";
						echo $insLicenseInfo."<br>".$insBioInfo."<br>".$ins_Script_prevEmplr."<br>";
						//$insQry = $migEmpMastObj->execQry($ins_Script);
						
						echo "<script>alert('$no_emp record/s successfully added to the Employee Master File.');</script>";
						
					//}
	$db->Close();
	
	//unlink("empmast.mdb");
}

function insHistorial_allowance()
{
	global $migEmpMastObj;
	
	
	$qryEmpMast = "Select * from tblEmpMast where compCode='".$_SESSION["company_code"]."' order by empLastName";
	$resEmpMast = $migEmpMastObj->execQry($qryEmpMast);
	$arrEmpMast = $migEmpMastObj->getArrRes($resEmpMast);
	foreach($arrEmpMast as $arrEmpMast_val)
	{
		/*Insert to tblAllowance*/
		$resEmpAllow = $migEmpMastObj->getEmpAllowance($arrEmpMast_val['empNo']);
		$numEmpAllow = $migEmpMastObj->getRecCount($resEmpAllow);
		if($numEmpAllow>0)
		{
			$arrEmpAllow = $migEmpMastObj->getArrRes($resEmpAllow);
			foreach($arrEmpAllow as $arrEmpAllow_val)
			{
				//Check if the Allowance Exists in the Current
				
				$getEmpAllowCode = $migEmpMastObj->getEquivAllwCode($arrEmpAllow_val["allowType"]);
				$getAllowPAtype = $migEmpMastObj->getPatypePersona($arrEmpAllow_val["allowType"]);
				$arrgetAllowPAtype = $migEmpMastObj->getSqlAssoc($getAllowPAtype);
				$arrAllowsprtpstag = $migEmpMastObj->getAllowSprtPs($getEmpAllowCode["allowCodeNew"]);
				
				
					
						if($arrgetAllowPAtype["freq"] == '1ST HALF'){
						$sked = '1';
						}
						if($arrgetAllowPAtype["freq"] == '2ND HALF'){
							$sked = '2';
						}
						if($arrgetAllowPAtype["freq"] == 'BOTH'){
							$sked = '3';
						}		
						
					if($arrEmpMast_val['dateHired']!=""){
						$empdateHired = date("m/d/Y", strtotime($arrEmpMast_val['dateHired']));
					}
					
					$empdateHired = ($empdateHired!=""?$empdateHired:"");
					
					$qryInsAllow.= "Insert into tblAllowance(compCode, empNo, allowCode, allowAmt, 
															allowSked, allowPayTag, 
															allowStat, allowEnd,allowStart, sprtPS,allowTag)
												values('".$arrEmpMast_val["compCode"]."','".$arrEmpMast_val['empNo']."','".$getEmpAllowCode["allowCodeNew"]."',".($arrEmpAllow_val["amount"]!=""?"'".$arrEmpAllow_val["amount"]."'":"0").",
													   '".$sked."','P','A',NULL,'".$empdateHired."','".$arrAllowsprtpstag["sprtPS"]."','".($arrEmpAllow_val["amount"]>100?"M":"D")."');<br>";
					
					
				
				
			}
		}
	}
	
	echo $qryInsAllow;
}

function insHistorial()
{
	global $migEmpMastObj;
	
	$empNo_qry = "SELECT empNo 
					FROM tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayGrp<>0 order by empLastName";
					
	$tblName_Basic = "";
	$tblName_Absences = "";
	$tblName_TardyUt = "";
	$tblName_AdjBasic = "";
	$tblEarn_pdYear = "2011";
	$tblEarn_pdNumber = "5";
	
	$qryEmpMast = "Select * from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayGrp<>0 order by empLastName";
	$resEmpMast = $migEmpMastObj->execQry($qryEmpMast);
	$arrEmpMast = $migEmpMastObj->getArrRes($resEmpMast);
	foreach($arrEmpMast as $arrEmpMast_val)
	{
				/*Insert Earnings Basic*/
				/*$resEmpBasic = $migEmpMastObj->getEmpEarn_Basic($arrEmpMast_val['empNo'],"'".$tblName_Basic."'");
				$numEmpBasic = $migEmpMastObj->getRecCount($resEmpBasic);
				
				if($numEmpBasic>0)
				{
					$arrEmpBasic = $migEmpMastObj->getSqlAssoc($resEmpBasic);
					$qryInsBasicData.= "Insert into tblEarningsHist(compCode,pdYear, pdNumber, empNo,trnCode,trnAmountE,trnTaxCd)
										values(
										'".$arrEmpMast_val["compCode"]."',
										'".$tblEarn_pdYear."',
										'".$tblEarn_pdNumber."',
										'".$arrEmpBasic["empNo"]."',
										'0100',
										'".$arrEmpBasic["amount"]."',
										'Y');<br>";
					$error_log = 0;
				}*/
				
				/*Insert Earnings Absent*/
				/*$resEmpAbsent = $migEmpMastObj->getEmpEarn_Basic($arrEmpMast_val['empNo'],"'".$tblName_Absences."'");
				$numEmpAbsent = $migEmpMastObj->getRecCount($resEmpAbsent);
				
				if($numEmpAbsent>0)
				{
					
					$arrEmpAbsent = $migEmpMastObj->getSqlAssoc($resEmpAbsent);
					if($arrEmpAbsent["amount"]!=0)
					{
						$qryInsAbsentData.= "Insert into tblEarningsHist(compCode,pdYear, pdNumber, empNo,trnCode,trnAmountE,trnTaxCd)
											values(
											'".$arrEmpMast_val["compCode"]."',
											'".$tblEarn_pdYear."',
											'".$tblEarn_pdNumber."',
											'".$arrEmpAbsent["empNo"]."',
											'0113',
											'".$arrEmpAbsent["amount"]."',
											'Y');<br>";
					}
					$error_log = 0;
				}*/
				
				/*Insert Earnings Tard/Ut*/
				/*$resEmpUt = $migEmpMastObj->getEmpEarn_Basic($arrEmpMast_val['empNo'],"'".$tblName_TardyUt."'");
				$numEmpUt = $migEmpMastObj->getRecCount($resEmpUt);
				
				if($numEmpUt>0)
				{
					
					$arrEmpUt = $migEmpMastObj->getSqlAssoc($resEmpUt);
					if($arrEmpUt["amount"]!=0)
					{
						$qryInsUtData.= "Insert into tblEarningsHist(compCode,pdYear, pdNumber, empNo,trnCode,trnAmountE,trnTaxCd)
											values(
											'".$arrEmpMast_val["compCode"]."',
											'".$tblEarn_pdYear."',
											'".$tblEarn_pdNumber."',
											'".$arrEmpUt["empNo"]."',
											'0111',
											'".$arrEmpUt["amount"]."',
											'Y');<br>";
					}
					$error_log = 0;
				}*/
				
				/*Insert Earnings AdjBasic*/
				/*$resEmpAdjBasic = $migEmpMastObj->getEmpEarn_Basic($arrEmpMast_val['empNo'],"'".$tblName_AdjBasic."'");
				$numEmpAdjBasic = $migEmpMastObj->getRecCount($resEmpAdjBasic);
				
				if($numEmpAdjBasic>0)
				{
					
					$arrEmpAdjBasic = $migEmpMastObj->getSqlAssoc($resEmpAdjBasic);
					if($arrEmpAdjBasic["amount"]!=0)
					{
						$qryInsAdjBasicData.= "Insert into tblEarningsHist(compCode,pdYear, pdNumber, empNo,trnCode,trnAmountE,trnTaxCd)
											values(
											'".$arrEmpMast_val["compCode"]."',
											'".$tblEarn_pdYear."',
											'".$tblEarn_pdNumber."',
											'".$arrEmpUt["empNo"]."',
											'0111',
											'".$arrEmpUt["amount"]."',
											'Y');<br>";
					}
					$error_log = 0;
				}*/
				
				/*Insert to Payroll Summary*/
				/*$resEmpPaySum = $migEmpMastObj->getEmpPaySum($arrEmpMast_val['empNo']);
				$numEmpPaySum = $migEmpMastObj->getRecCount($resEmpPaySum);
				if($numEmpPaySum>0)
				{
					
					$arrEmpPaySum = $migEmpMastObj->getArrRes($resEmpPaySum);
					foreach($arrEmpPaySum as $arrPaySum)
					{
						
						$qryInsPaySum.= "Insert into tblPayrollSummaryHist
										(compCode, pdYear, pdNumber, 
										empNo, payGrp, payCat, empLocCode, empBrnCode,
										empBnkCd, grossEarnings, taxableEarnings, minwage_taxableEarnings, totDeductions, 
										nonTaxAllow, netSalary, taxWitheld, yearEndTax, empDivCode, empDepCode, 
										empSecCode, sprtAllow, sprtAllowAdvance,
										empBasic, empMinWageTag, empEcola, emp13thMonthNonTax, emp13thMonthTax,emp13thAdvances, empTeu)
										values('".$arrEmpMast_val["compCode"]."','".$arrPaySum["pdYear"]."','".$arrPaySum["pdNumber"]."',
										'".$arrEmpMast_val['empNo']."','".$arrEmpMast_val["empPayGrp"]."','".$arrEmpMast_val["empPayCat"]."','".$arrEmpMast_val["empLocCode"]."' ,'".$arrEmpMast_val["empBrnCode"]."',
										'".$arrEmpMast_val["empBankCd"]."','".sprintf("%01.2f",$arrPaySum["grossEarnings"])."','".sprintf("%01.2f",$arrPaySum["taxableEarnings"])."','".sprintf("%01.2f",$arrPaySum["minwage_taxableEarnings"])."','".sprintf("%01.2f",$arrPaySum["totDeductions"])."',
										'".sprintf("%01.2f",$arrPaySum["new_nonTaxAllow"])."','".sprintf("%01.2f",$arrPaySum["new_netSalary"])."','".sprintf("%01.2f",$arrPaySum["taxWitheld"])."','".sprintf("%01.2f",$arrPaySum["yearEndTax"])."','0','0',
										'0','".sprintf("%01.2f",$arrPaySum["new_sprtAllow"])."','".sprintf("%01.2f",$arrPaySum["sprtAllowAdvance"])."',
										'".sprintf("%01.2f",$arrPaySum["empBasic"])."',".($arrEmpMast_val["empWageTag"]!=""?"'".$arrEmpMast_val["empWageTag"]."'":"NULL").",'".sprintf("%01.2f",$arrPaySum["empEcola"])."','0','0','0','".$arrEmpMast_val["empTeu"]."');<br>";
					}
					
				}*/
				
				/*Insert Ytd Data*/
				/*$resEmpYtd = $migEmpMastObj->getEmpYtdData($arrEmpMast_val['empNo']);
				$numEmpYtdData = $migEmpMastObj->getRecCount($resEmpYtd);
				
				if($numEmpYtdData>0)
				{
					$arrEmpYtd = $migEmpMastObj->getSqlAssoc($resEmpYtd);
					$qryInsYtdData.= "Insert into tblYtdDataHist(compCode,pdYear,empNo,YtdGross,YtdTaxable,YtdGovDed,YtdTax,payGrp,pdNumber,YtdBasic,sprtAllow, sprtAdvance, YtdGovDedMinWage, YtdGovDedAbvWage)
									  values(
									  '".$arrEmpMast_val["compCode"]."',
									  '".$arrEmpYtd["pdYear"]."',
									  '".$arrEmpYtd["empNo"]."',
									  '".sprintf("%01.2f",$arrEmpYtd["YtdGross"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["YtdTaxable"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["new_YtdGovDed"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["YtdTax"])."',
									  '".$arrEmpMast_val["empPayGrp"]."',
									  '5',
									  '".sprintf("%01.2f",$arrEmpYtd["YtdBasic"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["sprtAllow"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["sprtAllowAdvance"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["YtdGovDedMinWage"])."',
									  '".sprintf("%01.2f",$arrEmpYtd["YtdGovDedAbvWage"])."');<br>";
					$error_log = 0;
				}*/
				
				/*Update tblYtdDataHist sprtAllow and sprtAllowAdvance*/
				/*$resEmpYtdSprt = $migEmpMastObj->getPaySumSprtAllow($arrEmpMast_val['empNo']);
				$numEmpYtdSprt = $migEmpMastObj->getRecCount($resEmpYtdSprt);
				
				if($numEmpYtdSprt>0)
				{
					$arrEmpYtd = $migEmpMastObj->getSqlAssoc($resEmpYtdSprt);
					$qryInsYtdData.= "Update tblYtdDataHist
										set sprtAllow = '".sprintf("%01.2f",$arrEmpYtd["sprtAllow"])."', sprtAdvance='".sprintf("%01.2f",$arrEmpYtd["sprtAllowAdvance"])."'
										where empNo='".$arrEmpMast_val['empNo']."';<br>";
					$error_log = 0;
				}*/
				
				/*Insert to tblMtdGovt -> sabi ni Will*/
				$resEmpMtdGovt = $migEmpMastObj->getEmpMtdGovt($arrEmpMast_val['empNo']);
				$numEmpMtdGovt = $migEmpMastObj->getRecCount($resEmpMtdGovt);
				if($numEmpMtdGovt>0)
				{
					$arrEmpMtdGovt = $migEmpMastObj->getArrRes($resEmpMtdGovt);
					foreach($arrEmpMtdGovt as $arrMtdGovt)
					{
						$qryInsYtdData.= "Insert into tblMtdGovtHist
										(compCode, pdYear, pdMonth, empNo,
										mtdEarnings, sssEmp, sssEmplr, ec, 
										phicEmp, phicEmplr, hdmfEmp, hdmfEmplr)
										values ('".$arrEmpMast_val["compCode"]."','".$arrMtdGovt["pdYear"]."','".$arrMtdGovt["pdMonth"]."','".$arrEmpMast_val['empNo']."',
										'".sprintf("%01.2f",$arrMtdGovt["mtdEarnings"])."','".$arrMtdGovt["sssEmp"]."','".$arrMtdGovt["sssEmplr"]."','".$arrMtdGovt["ec"]."',
										'".$arrMtdGovt["phicEmp"]."','".$arrMtdGovt["phicEmplr"]."','".$arrMtdGovt["hdmfEmp"]."','".$arrMtdGovt["hdmfEmplr"]."');<br>";
					}
					$error_log = 0;
				}
				
				
	}
	
	$ins_Script = $qryInsYtdData;
	
	/*Insert Loans*/
	/*$tblEmpLoan_Paradox ="";
	
	//Inner Join the Table Abouve to Empmast to get the PayCat and PayGrp
	
	$qryGetLoan = "Select * from ".$tblEmpLoan_Paradox." where empNo in (".$empNo_qry.")";
	$resGetLoan = $migEmpMastObj->execQry($qryGetLoan);
	$arrGetLoan = $migEmpMastObj->getArrRes($resGetLoan);
	foreach($arrGetLoan as $arrGetLoan_val)
	{
		$qryGetLoanCode = "";
		$resGetLoanCode = $migEmpMastObj->execQry($qryGetLoanCode);
		$arrGetLoanCode = $migEmpMastObj->getArrRes($resGetLoanCode);
		
		if($arrGetLoan_val["LOANFREQ"]=="BOTH")
			$loanFreq = "3";
		elseif($arrGetLoan_val["LOANFREQ"]=="1ST HALF")
			$loanFreq = "1";
		else
			$loanFreq = "2";
	
		$lonNoPayments = $arrGetLoan_val["LOANAMT"] / $arrGetLoan_val["LOANDEDPAY"];
		$lonNoPayments =($lonNoPayments>=1?"'".sprintf("%01.2f",$lonNoPayments)."'":"NULL") ;
		
	
		$qryInsert_tblEmpLoans.= "Insert into tblEmpLoans(compCode,empNo,lonTypeCd,lonRefNo,lonAmt,
														lonWidInterst,lonGranted,lonStart,lonEnd,lonSked,
														lonNoPaymnts,lonDedAmt1,lonDedAmt2,lonPayments,lonPaymentNo,
														lonCurbal,lonLastPay,lonStat,dateadded,UploadTag)
								  values('".$_SESSION["company_code"]."', '".$arrGetLoan_val["EMPNO"]."', '".$arrGetLoanCode[""]."', '".$arrGetLoan_val["LOANREF"]."', '".sprintf("%01.2f",$arrGetLoan_val["LOANAMT"])."'
								  		, '".$arrGetLoan_val["LOANAMT"]."', ".($arrGetLoan_val["LOANDATEGRANTED"]!=""?"'".date("m/d/Y", strtotime($arrGetLoan_val["LOANDATEGRANTED"]))."'":"NULL").", ".($arrGetLoan_val["LOANDATE"]!=""?"'".date("m/d/Y", strtotime($arrGetLoan_val["LOANDATE"]))."'":"NULL").", NULL, '".$loanFreq."'
										, ".$lonNoPayments.", '".sprintf("%01.2f", $arrGetLoan_val["LOANDEDPAY"])."', '".sprintf("%01.2f", $arrGetLoan_val["LOANDEDPAY"])."', '".sprintf("%01.2f", $arrGetLoan_val["LOANPAYMENTS"])."', '1'
										, '".sprintf("%01.2f", $arrGetLoan_val["LOANBALANCE"])."', '03/15/2011', 'O', '".date("m/d/Y")."', '1')";
		
		$qryInsert_tblEmpLoansDtlHist.= "Insert into tblEmpLoansDtlHist(compCode,empNo,lonTypeCd,lonRefNo,pdYear,
														pdNumber,trnCat,trnGrp,trnAmountD,ActualAmt,
														dedTag,lonLastPay)
										values('".$_SESSION["company_code"]."', '".$arrGetLoan_val["EMPNO"]."', '".$arrGetLoanCode[""]."', '".$arrGetLoan_val["LOANREF"]."', '".date("Y")."', 
											   '5','".$arrGetLoan_val["empPayCat"]."', '".$arrGetLoan_val["empPayGrp"]."', '".sprintf("%01.2f", $arrGetLoan_val["LOANDEDPAY"])."', '".sprintf("%01.2f", $arrGetLoan_val["LOANDEDPAY"])."',
											   'Y', '02/28/2011')";




		unset($loanFreq,$lonNoPayments);
	}*/
	
	//$ins_Script = $qryInsYtdData.$qryInsPaySum.$qryInsMtdGovt.$qryInsBasicData.$qryInsAbsentData.qryInsUtData;
	//Insert into tblMtdGovtHist (compCode, pdYear, pdMonth, empNo, mtdEarnings, sssEmp, sssEmplr, ec, phicEmp, phicEmplr, hdmfEmp, hdmfEmplr) values ('2','2009','12','630000011', '0.00','0','0','0', '0','0','0','0');
	//Insert into tblMtdGovtHist (compCode, pdYear, pdMonth, empNo, mtdEarnings, sssEmp, sssEmplr, ec, phicEmp, phicEmplr, hdmfEmp, hdmfEmplr) values ('2','2009','12','010002184', '11235.79','366.7','777.3','10', '137.5','137.5','100','100');

	//$insQry = $migEmpMastObj->execQry($qryInsPaySum);
	echo $ins_Script;
}


function WriteFile($file_name, $str_path, $file_cont)
{
	$fh = fopen($str_path.'/'.$file_name, 'w') or die('can not write file!');
	fwrite($fh, $file_cont);
	fclose($fh);
}

function empMasttextFile()
{
	extract($GLOBALS);
	
	$qryEmpMast = "Select * from tblEmpMast where empPayCat='5'";
	$resEmpMast = $migEmpMastObj->execQry($qryEmpMast);
	$arrEmpMast = $migEmpMastObj->getArrRes($resEmpMast);
	
	foreach($arrEmpMast as $arrEmpMast_val)
	{
		$empLoc = ($arrEmpMast_val["empLocCode"]=='0001'?"1":"2");
		$BrnGlCode = $migEmpMastObj->getEmpBranchArt($arrEmpMast_val["compCode"],$arrEmpMast_val["empBrnCode"]);
		$empStat = ($arrEmpMast_val["empStat"]=='RG'?"1":"3");
		
		$incRDheader.= $arrEmpMast_val["empNo"]."	".$arrEmpMast_val["empLastName"]."	".$arrEmpMast_val["empFirstName"]."	 ".$arrEmpMast_val["empMidName"][0]."."."	".$arrEmpMast_val["empRank"]."	".$empLoc."-".$BrnGlCode["glCodeStr"]."	".$empStat."\r\n";
	}
	
	if(file_exists($_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '/'.session_id().'-EMPMAST.txt'))
	{
		unlink($_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '/'.session_id().'-EMPMAST.txt');
	}

	WriteFile(session_id().'-EMPMAST.txt', $_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '', $incRDheader);
	echo "<script>window.open('"."errors/".session_id().'-EMPMAST.txt'."');</script>";
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
<form action="<? echo $_SERVER['../transactions/PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="frmTS">
    <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    	<tr>
    		<td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">
    			&nbsp;Upload Employee Master File From Paradox
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
                            <td class="gridDtlLbl">PayDate </td>
                            <td class="gridDtlLbl">:</td>
                            <td class="gridDtlVal"> 
                               <input value="06/15/2010" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmTS.monthfr.value);" class='inputs' name='monthfr' id='monthfr' maxLength='10' readonly size="10"/>
                                  <a href="#"><img name="imgfrDate" id="imgfrDate" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a>
                            </td>
                        </tr>
                        
                    <tr> 
                        <td width="18%" class="gridDtlLbl">File Name </td>
                        <td width="1%" class="gridDtlLbl">:</td>
                        <td width="81%" class="gridDtlVal"> 
                        	<font size="2" face="Arial, Helvetica, sans-serif">
                        		<input name="fileUpload" type="file" id="fileUpload">
                        	</font> 
                        </td>
    				</tr>
    			</table>
    			<br>
    			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
                  	<tr>
                    	<td>
                        	<CENTER>
                				<input name="btnUpload" type="submit" id="btnUpload" value="Upload" class="inputs">
                			</CENTER>
                    	</td>
                  	</tr>
    			</table> 
    		</td>
    	</tr> 
    	<tr > 
    		<td class="gridToolbarOnTopOnly" colspan="6">
    			<CENTER>
    				
    					<input style="background-color:#fff; height:18px; text-align: center;  border:0px solid;" >
    				
    			</CENTER>	
    		</td>
    	</tr>
    </table>
</form>
</BODY>
</HTML>
<script>
	Calendar.setup({
			  inputField  : "monthfr",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgfrDate"       // ID of the button
		}
	)	
	
	function printPayReg()
	{
		//window.open('rpt_unposted_tran_ded_pdf.php'+'?');	
		alert("GENARRA \n HELLO");
	}	
</script>
<?php

	if(isset($_POST['btnUpload'])) {
//	insHistorial_allowance();
	insHistorial();
	/*if ($error == UPLOAD_ERR_OK) {
			$tmp_name = $_FILES["fileUpload"]["tmp_name"];
			
			if($tmp_name!=""){
				$name = $_FILES["fileUpload"]["name"];
				$size = $_FILES["fileUpload"]["size"];				
				move_uploaded_file($tmp_name, "empmast.mdb");
				instoEmpMast();
				
			}
			else{
				echo "<script language='javascript'>alert('Select the file to be Uploaded.');</script>";
				
			}
		}*/
	//insHistorial();
	//insHistorial_allowance();
	/*Sir Louie Textfile*/
		/*$qryGetEmp = "Select empNo, empLastName, empFirstName, empMidName, empRestDay, empBrnCode, empLocCode from tblEmpMast 
					where compCode='".$_SESSION["company_code"]."' 
					and empPayGrp='".$_SESSION["pay_group"]."' 
					and empPayCat='".$_SESSION["pay_category"]."'
					and empBrnCode in ('40','63')
					order by empBrnCode, empLocCode, empLastName";
		$rsGetEmp = $migEmpMastObj->execQry($qryGetEmp);
		$arrGetEmp =  $migEmpMastObj->getArrRes($rsGetEmp);
		$frDate = "02/04/2010";
		foreach($arrGetEmp as $arrGetEmp_val)
		{
			$Arr_emp_RestDay = explode(",",$arrGetEmp_val["empRestDay"]);
			
			foreach($Arr_emp_RestDay as $Arr_emp_RestDay_val){
				if($Arr_emp_RestDay_val!=""){
					if($Arr_emp_RestDay_val>$frDate){
						$gt_Rd.=$Arr_emp_RestDay_val.",";
					}
				}
			
			}
				if($gt_Rd!="")
				{
					$gt_Rd = substr($gt_Rd,0,strlen($gt_Rd) - 1);
					$updateRdEmpMast = "Update tblEmpMast set empRestDay='".$gt_Rd."' where empNo='".$arrGetEmp_val["empNo"]."'";
					
					//echo $arrGetEmp_val["empNo"]."<br>".$updateRdEmpMast."<br><br>";
				}
			unset($gt_Rd);
		}*/
		
		/*Blacklist Data*/
		/*
		$qryBlacklist = "Select * from tblBlacklisted_Paradox";
		$rsBlacklist = $migEmpMastObj->execQry($qryBlacklist);
		$arrBlacklist =  $migEmpMastObj->getArrRes($rsBlacklist);
		foreach($arrBlacklist as $arrBlacklist_val)
		{
			if($arrBlacklist_val["Emp_store"]!="")
			{
				$qrybrnch = "Select * from tblBranch where brnStat = 'A' and brnCode<>'0001' and brnShortDesc LIKE '%".$arrBlacklist_val["Emp_store"]."'";
				$resGetBrn = $migEmpMastObj->execQry($qrybrnch);
				$arrGetBrn = $migEmpMastObj->getSqlAssoc($resGetBrn);
				if($arrGetBrn["brnDesc"]!="")
					$brnDesc = "'".$arrGetBrn["brnCode"]."'";
				else
					$brnDesc = "NULL";
				
			}
			else
			{
				$brnDesc = "NULL";
			}	
				
			
				$qryInsBlackList.= "Insert into tblBlacklistedEmp
									(compCode, empNo, empLastName, empFirstName, empMidName, 
									empBday, empSssNo, empTin, 
									empDepCode, empBrnCode,empposId, dateHired, 
									dateResigned, reason,agency, 
									dateEncoded, userId, updatedBy,dateUpdated)
									values
									(".($arrBlacklist_val["Company"]!=""?"'".str_replace("'","''",$arrBlacklist_val["Company"])."'":"NULL").",".($arrBlacklist_val["Emp_ID"]!=""?"'".$arrBlacklist_val["Emp_ID"]."'":"'00000000'").",'".str_replace("'","''",$arrBlacklist_val["Emp_last"])."','".str_replace("'","''",$arrBlacklist_val["Emp_first"])."','".$arrBlacklist_val["Emp_middle"]."',
									".($arrBlacklist_val["Emp_bday"]!=""?"'".date("Y-m-d", strtotime($arrBlacklist_val["Emp_bday"]))."'":"NULL").", '".str_replace("-","",$arrBlacklist_val["Emp_sss"])."','".str_replace("-","",$arrBlacklist_val["Emp_tin"])."',
									NULL,".$brnDesc.",NULL,".($arrBlacklist_val["Emp_datehire"]!=""?"'".date("Y-m-d", strtotime($arrBlacklist_val["Emp_datehire"]))."'":"NULL").",
									".($arrBlacklist_val["Emp_date_end"]!=""?"'".date("Y-m-d", strtotime($arrBlacklist_val["Emp_date_end"]))."'":"NULL").",'".str_replace("'","''",$arrBlacklist_val["Reason"])."',".($arrBlacklist_val["Agency"]!=""?"'".str_replace("'","''",$arrBlacklist_val["Agency"])."'":"NULL").",
									".($arrBlacklist_val["Encode_date"]!=""?"'".date("Y-m-d", strtotime($arrBlacklist_val["Encode_date"]))."'":"NULL").",'".str_replace("'","''",$arrBlacklist_val["Username"])."','".$sessionVars['empNo']."','".date("m/d/Y")."');";
			
			
		}

		
		$rsInsBlackList = $migEmpMastObj->execQry($qryInsBlackList);
		*/
		
		/*if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], "empmast.mdb")) {
			print "File is valid, and was successfully uploaded. ";
			print "Here's some more debugging info:\n";
			print_r($_FILES);
		} else {
			print "Possible file upload attack!  Here's some debugging info:\n";
			print_r($_FILES);
		}*/
		

		/*if ($error == UPLOAD_ERR_OK) {
			$tmp_name = $_FILES["fileUpload"]["tmp_name"];
			
			if($tmp_name!=""){
				$name = $_FILES["fileUpload"]["name"];
				$size = $_FILES["fileUpload"]["size"];				
				move_uploaded_file($tmp_name, "empmast.mdb");
				instoEmpMast();
				
			}
			else{
				echo "<script language='javascript'>alert('Select the file to be Uploaded.');</script>";
				
			}
		}
		*/
	
	}
	
	
?>