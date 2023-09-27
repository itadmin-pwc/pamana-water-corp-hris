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
	$error = $_FILES["fileUpload"]["error"];
	
	if ($error == UPLOAD_ERR_OK) {
		$tmp_name = $_FILES["fileUpload"]["tmp_name"];
		$name = $_FILES["fileUpload"]["name"];
		$size = $_FILES["fileUpload"]["size"];				
		move_uploaded_file($tmp_name, "empmast.mdb");

	}
	
	instoEmpMast();
	
	
	
}

function instoEmpMast()
{
	extract($GLOBALS);
	global $noError;
	
	
	include("../../../includes/adodb/adodb.inc.php");
	$db =& ADONewConnection('access');
	$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".realpath("empmast.mdb");
	$db->Connect($dsn,'','');
	
	$qryGetData = "SELECT 
					[EMPLOYEE ID#] as empNo, 
					[FIRST NAME] as empFirstName,
					[LAST NAME] as empLastName, 
					[MIDDLE NAME] as empMidName,
					[GENDER] as empGender,
					[CURRENT DEPARTMENT] as empDept, 
					[CURRENT POSISTION] as empPos,
					[TAX STATUS] as empTeu,
					[DATE EMPLOYED] as empDEmp, 
					[DATE TERMINATED] as empDTer,
					[SALARY RATE TYPE] as empRateType,
					[SALARY] as empMRate, 
					[DAILY RATE] as empDRate,
					[PAG-IBIG MEMBER] as empPagNo,
					[BANK ACCT#] as empBankNo, 
					[BANK NAME] as empBankName,
					[SSS NUMBER] as empSssNo,
					[TIN] as empTin, 
					[EMPLOYMENT STATUS] as empStat,
					[EMPLOYMENT TYPE] as empType,
					[CURRENT ADDRESS] as empCurrAdd,
					[CURRENT SECTION] as empSection, 
					[DATE OF BIRTH] as empBDate,
					[PH NUMBER] as empPHICNo,
					[WITHHELD TO PREV# EMP#] as empPrevWitheld,
					[PREVIOUS EMPLOYER] as empPrevCompName, 
					[PREVIOUS TIN] as empPrevTin,
					[GROSS TO PREV# EMPLOYER] as empPrevGross, 
					[PREVIOUS EMPLOYER ADDRESS] as empPrevAdd,
					[PREVIOUS SSS/HDMF/PH] as empPrevSSSHdmfPag, 
					[PREVIOUS TAXED 13TH/BONUS] as empPrevTax13th,
					[PREVIOUS NT 13TH/BONUS] as empPrevNT13th,
					[PREVIOUS TAXED OTHER INC] as empPrevTOtherInc, 
					[PREVIOUS NT OTHER INC] as empPrevNtOtherInc,
					[PREVIOUS EMPLOYER DATE] as empPrevEmpDate,
					[PREVIOUS NT SALARIES] as empPrevNtSalaries
					FROM [empmas]";
	
	
	//echo $qryGetData;
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
	$noPayType = strtoupper("Employees with No Pay Type or Employee Salary Rate :")."\r\n";
	$noPayCatType = strtoupper("Employees with No Pay Cat :")."\r\n";
	$noMRate = strtoupper("Employees with No Monthly Rate :")."\r\n";
	$noprevAdd = strtoupper("Employee with Previous Employer but with no Previous Employer Address : ")."\r\n";
	$noprevTin = strtoupper("Employee with Previous Employer but with no Previous Employer TIN : ")."\r\n";

	
	$good=0;
	$no_emp = 0;
	if ($count>0) 
	{
		while(!$rsGetData->EOF)
		{
			$compCode = 6;
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
					$error_log = 1;
				}
				
				$empLocCode = "1002";
				$empBrnCode = "1002";
				$empDiv = "16";
				$empDepCode = "16";
				$empSecCode = "16";
				$empPosId = "17";
				
				if($rsGetData->fields['empDEmp']!=""){
					$empdateHired = date("m/d/Y", strtotime($rsGetData->fields['empDEmp']));
				}else{
					$noStartDate.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				$empdateReg = ($empdateHired!=""?$empdateHired:"");
				
				if($rsGetData->fields['empStat']!=""){
					$empStat = $migEmpMastObj->getempStatDef($rsGetData->fields['empStat']);
				}else{
					$noempStat.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				$empRestDay = 7; 
				
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
				
				if($rsGetData->fields['empBankNo']!=""){
					$empAcctNo = $rsGetData->fields['empBankNo'];
				}else{
					$noBanNo.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				$empPayType = ($rsGetData->fields['empRateType']!=""?$migEmpMastObj->getRateDef($rsGetData->fields['empRateType']):0);
				
				if($empPayType!=0){
					$noPayType.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}else{
					$empPayType = $empPayType;
				}
				
				$emppayGrp = 2; 
				
				if($rsGetData->fields['empType']!=""){
					$empPayCat = $migEmpMastObj->getPayCat($rsGetData->fields['empType'],$compCode);
				}else{
					$noPayCatType.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				$empAddr1 = ($rsGetData->fields['empCurrAdd']!=""?strtoupper($rsGetData->fields['empCurrAdd']):"");
				$empMarStat = ($empTeu!=""?$migEmpMastObj->getMarStatDef($empTeu):"UNKNOWN");
				$empSex = ($rsGetData->fields['empGender']!="M"?"F":"M");
				$empBday = ($rsGetData->fields['empBDate']!=""?date("m/d/Y", strtotime($rsGetData->fields['empBDate'])):"");
				$empDrate =  ($rsGetData->fields['empDRate']!=""?sprintf("%01.2f",$rsGetData->fields['empDRate']):0);
				$empMrate = ($empPayType==0?$migEmpMastObj->getComputedMRate($empDrate,$compCode):0);
				$empHRate = ($empDrate!="0"?sprintf("%01.2f",$empDrate/8):0);
				$empNickName = ($rsGetData->fields['empFirstName']!=""?strtoupper($rsGetData->fields['empFirstName']):"");
				$empEndDate = ($rsGetData->fields['empDTer']!=""?date("m/d/Y", strtotime($rsGetData->fields['empDTer'])):"");
				
				if($rsGetData->fields['empPHICNo']!=""){
					$empPhicNo =  $rsGetData->fields['empPHICNo'];
				}else{
					$noPHicNo.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."\r\n";
					$error_log = 1;
				}
				
				//echo strpos('MBTC',$rsGetData->fields['empBankName'])."GENARRA";
				
				if($error_log!=1)
				{
					$ins_Script.= "Insert into tblEmpMast
									(compCode,empNo,empLastName,empFirstName,empMidName,
									 empLocCode,empBrnCode,empDiv,empDepCode,empSecCode,
									 empPosId,dateHired,empStat,dateReg,empRestDay,empTeu,
									 empTin,empSssNo,empPagibig,empAcctNo,
									 empPayType,emppayGrp,empPayCat,empAddr1,empMarStat,empSex,
									 empBday,empMrate,empDrate,empHrate,empNickName,empPhicNo) 
									values
									('".$compCode."','".$empNo."','".$empLastName."' ,'".$empFirstName."','".$empMidName."',
									'".$empLocCode."' ,'".$empBrnCode."','".$empDiv."','".$empDepCode."','".$empSecCode."',
									'".$empPosId."','".$empdateHired."','".$empStat."','".$empdateReg."','".$empRestDay."','".$empTeu."',
									'".$empTinNo."','".$empSssNo."','".$empPagibig."','".$empAcctNo."',
									'".$empPayType."','".$emppayGrp."','".$empPayCat."','".$empAddr1."','".$empMarStat."','".$empSex."',
									'".$empBday."','".$empMrate."','".$empDrate."','".$empHRate."','".$empNickName."','".$empPhicNo."');";
					$no_emp++;
				}
				/*if($rsGetData->fields['empPrevCompName']!=""){
					$prevCompCode = $prevCompCode;
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
					}
					
					
					if($rsGetData->fields['empPrevTOtherInc']){
						$prevEmpEarnings+= sprintf("%01.2f",$rsGetData->fields['empPrevTOtherInc']);
					}
					
					if($rsGetData->fields['empPrevNtSalaries']){
						$prevEmpGrossNonTax = sprintf("%01.2f",$rsGetData->fields['empPrevNtSalaries']);
					}
					
					if($rsGetData->fields['empPrevNtOtherInc']){
						$prevEmpGrossNonTax+= sprintf("%01.2f",$rsGetData->fields['empPrevNtOtherInc']);
					}
					
					if($rsGetData->fields['empPrevNT13th']){
						$prevEmpNonTax13th = sprintf("%01.2f",$rsGetData->fields['empPrevNT13th']);
					}
					
					if($rsGetData->fields['empPrevSSSHdmfPag']){
						$prevEmpNonTaxSss = sprintf("%01.2f",$rsGetData->fields['empPrevGross']);
					}
					
					
					if($rsGetData->fields['empPrevTax13th']){
						$prevEmpTax13th = sprintf("%01.2f",$rsGetData->fields['empPrevTax13th']);
					}
					
					if($rsGetData->fields['empPrevEmpDate']){
						$prevEmpyearCd = date("Y", strtotime($rsGetData->fields['empPrevEmpDate']));
					}
					
					$prevTaxPerMonth = 0;
					$prevTaxDeducted = 0;
					$empPrevTag = "Y";
				}*/
				
				
			}
			
			$rsGetData->MoveNext();
		}
	}
	
	
	if($error_log==1)
	{
		$output_err.=$duplicateEmpNo."\r\n".$noLastName."\r\n".$noFirstName."\r\n".$noMidName."\r\n".$noStartDate."\r\n".$noempStat."\r\n".$noTaxExempt."\r\n".$noTinNo."\r\n".$noSssNo."\r\n".$noPagNo."\r\n".$noPHicNo."\r\n".$noBank."\r\n".$noBanNo."\r\n".$noPayType."\r\n".$noPayCatType."\r\n".$noMRate."\r\n".$noprevAdd."\r\n".$noprevTin;
		
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
<form action="<? echo $_SERVER['../transactions/PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="frmTS">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Upload 
        Employee Master File From Paradox</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
		 
		  <tr> 
            <td width="18%" class="gridDtlLbl">File Name </td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="81%" class="gridDtlVal"> <font size="2" face="Arial, Helvetica, sans-serif">
              <input name="fileUpload" type="file" id="fileUpload">
              </font> </td>
          </tr>
        </table>
		<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input name="btnUpload" type="submit" id="btnUpload" value="Upload" onClick="valUpload();" class="inputs">
              </CENTER>
					</td>
				  </tr>
			  </table> 
	</td>
	</tr> 
	<tr > 
		<td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#fff; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>
<?php
/*
		$empLocCode =
		$empBrnCode = 
		$empDiv =
		$empDepCode =
		$empSecCode =
		$empPosId =
		
		//$empBankCd = ($rsGetData->fields['empBankName']!=""?$migEmpMastObj->getBankDef($rsGetData->fields['empBankName'],$compCode)):$noBank.="(".$compCode.") ".$rsGetData->fields['empNo']."->".$rsGetData->fields['empLastName'].", ".$rsGetData->fields['empFirstName']."<br>");
		$empPayGrp = 
		
		
		$empAddr2 =
		$empAddr3 =
		
		$empLevel =
		
		ANNUALIZATION FIELDS -> tblPrevEmployer
		
		if($rsGetData->fields['empPrevGross']){
			$prevEmpTaxes = sprintf("%01.2f",$rsGetData->fields['empPrevGross']);
		}
		
		$prevEmpStat = 
		$annualTag =
		
		
	*/
?>