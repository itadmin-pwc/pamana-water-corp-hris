<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','D:\wamp\php\PEAR');
	require_once 'Spreadsheet/Excel/Writer.php';
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$compCode = $_SESSION['company_code'];
	$inqTSObj->compCode     = $compCode;
	$brnCode         		= $_POST['branch'];
	$compName 		= $inqTSObj->getCompanyName($compCode);
############################ Q U E R Y ##################################

$confaccess=$_SESSION['Confiaccess'];
if($confaccess == 'N'){
	$confi = "and tblEmpMast.empPayCat ='3'";
}else {
	$confi = "and tblEmpMast.empPayCat ='2'";
}

	$sqlRD = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, 
	   				tblDepartment.deptDesc, tblPosition.posDesc, tblEmpMast.empEndDate AS empEndDate, 
				    CASE emppaytype WHEN 'M' THEN empmrate WHEN 'D' THEN empdrate END AS salary_ko, 
					CASE emppaytype WHEN 'M' THEN 'Monthly' WHEN 'D' THEN 'Daily' END AS rateMode, tblEmpMast.empStat,
					CASE tblEmpMast.employmentTag when  'RG' then 'REGULAR' when 'CN' then 'CONTRACTUAL' 
					when 'PR' then 'PROBATIONARY' end AS emp_stat, tblEmpMast.dateHired AS dateHired,
					empPayCat,tblEmpMast.dateReg AS dateReg 
			  FROM tblEmpMast 
			  INNER JOIN tblDepartment ON tblEmpMast.empDiv = tblDepartment.divCode 
			  AND tblEmpMast.empDepCode = tblDepartment.deptCode 
			  AND (tblDepartment.deptLevel = 2) 
			  INNER JOIN tblPosition ON tblEmpMast.empPosId = tblPosition.posCode 
			  AND tblempmast.compcode = tblposition.compcode 
			  WHERE  (tblEmpMast.empBrnCode = $brnCode) 
			  AND  (tblEmpMast.compCode = '{$_SESSION['company_code']}') 
			  AND (tblDepartment.compCode = '{$_SESSION['company_code']}') 
			  AND tblEmpMast.employmentTag IN ('RG', 'CN', 'PR') 
			  AND tblEmpMast.empStat = 'RG' $confi
			  AND empPayGrp<>0 
			  AND empPayCat<>0 
			  ORDER BY tblDepartment.deptDesc, tblEmpMast.employmentTag, tblEmpMast.empLastName, tblEmpMast.empFirstName";
	
	$resGetDealsList = mysql_query($sqlRD);
	$num = mysql_num_rows($resGetDealsList);
	$sqlBr = "SELECT brnDesc FROM tblBranch WHERE brnCode = $brnCode AND compCode = '{$_SESSION['company_code']}'";
	$resBr = mysql_query($sqlBr);
	$numBr = mysql_num_rows($resBr);
	if ($numBr>0) {
		$brnName = mysql_result($resBr,0,"brnDesc");
	} else {
		$brnName = "";
	}
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL
$workbook = new Spreadsheet_Excel_Writer();
$workbook->setCustomColor(12,183,219,255);
$workbook->setCustomColor(13,155,205,255);
$deptHeader = $workbook->addFormat(array('Size' => 10,
								  'Color' => 'blue',
								  'bold'=> 1));
$headerFormat = $workbook->addFormat(array('Size' => 10,
								  'Color' => 'red',
								  'bold'=> 1,
								  'Align' => 'merge'));
$headerBorder    = $workbook->addFormat(array('Size' => 10, 'bold' => 1));

$detailBorder   = $workbook->addFormat(array('Size' => 10));
$detailBorder2   = $workbook->addFormat(array('Size' => 10,
										  'Align' => 'right'));

$detailBorderAlignRight   = $workbook->addFormat(array('Size' => 10,'bold' => 1));
$headerFormat->setFontFamily('Calibri');
$headerBorder->setFontFamily('Calibri');
$detailBorder->setFontFamily('Calibri');
$detailBorder2->setFontFamily('Calibri');
$detailBorder2->setNumFormat('0.00');
$detailBorderAlignRight->setFontFamily('Calibri');

$filename = "manComp".$todaynewdate.".xls";
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('Manpower Complement');
$worksheet->setLandscape();
$worksheet->freezePanes(array(6, 0));
$worksheet->setColumn(0,1,5);
$worksheet->setColumn(2,16,20);
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(0, 0, $compName,$headerFormat); for ($j=1; $j<=8; $j++) { $worksheet->write(0, $j, "",$headerFormat); }
$worksheet->write(1, 0, "MANPOWER COMPLEMENT REPORT",$headerFormat); for ($j=1; $j<=8; $j++) { $worksheet->write(1, $j, "",$headerFormat); }
$worksheet->write(2, 0, $brnName,$headerFormat); for ($j=1; $j<=8; $j++) { $worksheet->write(2, $j, "",$headerFormat); }
$worksheet->write(3, 0, "RUN DATE: ".$today); 
$worksheet->write(4, 0, "REPORT ID: MANCOMP"); 
$worksheet->write(5, 2, "LAST NAME",$headerBorder);
$worksheet->write(5, 3, "FIRST NAME",$headerBorder);
$worksheet->write(5, 4, "MIDDLE NAME",$headerBorder);
$worksheet->write(5, 5, "POSITION",$headerBorder);
$worksheet->write(5, 6, "DATE HIRED",$headerBorder);
$worksheet->write(5, 7, "END OF CONTRACT",$headerBorder);
$worksheet->write(5, 8, "DATE REGULARIZED",$headerBorder);
$worksheet->write(5, 9, "SALARY",$headerBorder);
$worksheet->write(5, 10, "RATE MODE",$headerBorder);
$worksheet->write(5, 11, "ADVANCES",$headerBorder);
$worksheet->write(5, 12, "REG IV",$headerBorder);
$worksheet->write(5, 13, "ECOLA",$headerBorder);
$worksheet->write(5, 14, "GASOLINE",$headerBorder);
$worksheet->write(5, 15, "TRANSPO",$headerBorder);
$worksheet->write(5, 16, "OTHERS",$headerBorder);





## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$lastRow = 7;


//alejo add for confi report with salary for user 129 only

///end


$ctr = 0;
for ($i=0;$i<$num;$i++){
		$row = $detailBorder;
		$row2 = $detailBorder2;
	if ($tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
		if ($tmpDept!="") {
			$worksheet->write($lastRow, 0, "Total: $ctrDept",$row);
			$lastRow++;
		}
		$ctrDept = 0;
		$worksheet->write($lastRow, 0, mysql_result($resGetDealsList,$i,"deptDesc")." DEPARTMENT",$detailBorderAlignRight);
		$lastRow++;
	}
	if ($tmpStat!=mysql_result($resGetDealsList,$i,"emp_stat") || $tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
		$worksheet->write($lastRow, 1, mysql_result($resGetDealsList,$i,"emp_stat"),$row);
		$lastRow++;
		$lastRow++;
	}
	$ctrDept++;
	$ctr++;
	$worksheet->write($lastRow, 2, ucwords(strtolower(mysql_result($resGetDealsList,$i,"empLastName"))),$row);
	$worksheet->write($lastRow, 3, ucwords(strtolower(mysql_result($resGetDealsList,$i,"empFirstName"))),$row);
	$worksheet->write($lastRow, 4, ucwords(strtolower(mysql_result($resGetDealsList,$i,"empMidName"))),$row);
	$worksheet->write($lastRow, 5, ucwords(strtolower(mysql_result($resGetDealsList,$i,"posDesc"))),$row);
	$worksheet->write($lastRow, 6, date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"dateHired"))),$row);
	$worksheet->write($lastRow, 7, date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"empEndDate"))),$row);
	$worksheet->write($lastRow, 8, date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"dateReg"))),$row);
	if (in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat'])))  {
		$worksheet->write($lastRow, 9, number_format((float)mysql_result($resGetDealsList,$i,"salary_ko"),2),$row2);
		$worksheet->write($lastRow, 10, mysql_result($resGetDealsList,$i,"rateMode"),$row2);
	} else {
		$worksheet->write($lastRow, 9, "--",$row2);
	}	
		
	$sqlAllow = "SELECT tblAllowType.allowDesc,tblAllowance.allowTag,tblAllowance.allowAmt FROM tblAllowance INNER JOIN tblAllowType ON tblAllowance.allowCode = tblAllowType.allowCode AND tblAllowance.compCode = tblAllowType.compCode WHERE (tblAllowance.empNo = ".mysql_result($resGetDealsList,$i,"empNo").")";
	$resAllow = mysql_query($sqlAllow);
	$numAllow = mysql_num_rows($resAllow);
	$allow="";
		$worksheet->write($lastRow, 11, '',$row2);	
		$worksheet->write($lastRow, 12, '',$row2);	
		$worksheet->write($lastRow, 13, '',$row2);	
		$worksheet->write($lastRow, 14, '',$row2);	
		$worksheet->write($lastRow, 15, '',$row2);	
		$worksheet->write($lastRow, 16, '',$row2);	
	
		for ($j=0;$j<$numAllow;$j++) {
			switch(mysql_result($resAllow,$j,"allowDesc")) {
				case "ADVANCES":
					if (in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat'])))				
						$worksheet->write($lastRow, 11, number_format(mysql_result($resAllow,$j,"allowAmt"),2),$row2);
					else
						$worksheet->write($lastRow, 11, "--",$row2);
						
				break;
				case "REG IV ALLOWANCE":
					if (in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat'])))				
						$worksheet->write($lastRow, 12, number_format(mysql_result($resAllow,$j,"allowAmt"),2),$row2);
					else
						$worksheet->write($lastRow, 12, "--",$row2);
				break;
				case "ECOLA":
					if (in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat'])))				
						$worksheet->write($lastRow, 13, number_format(mysql_result($resAllow,$j,"allowAmt"),2),$row2);
					else
						$worksheet->write($lastRow, 13, "--",$row2);
				break;
				case "ECOLA 3":
					if (in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat'])))				
						$worksheet->write($lastRow, 13, number_format(mysql_result($resAllow,$j,"allowAmt"),2),$row2);
					else
						$worksheet->write($lastRow, 13, "--",$row2);
				break;
				case "GASOLINE ALLOWANCE":
					if (in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat'])))				
						$worksheet->write($lastRow, 14, number_format(mysql_result($resAllow,$j,"allowAmt"),2),$row2);
					else
						$worksheet->write($lastRow, 14, "--",$row2);
				break;
				case "TRANSPORTATION ALLOWANCE":
					if (in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat'])))				
						$worksheet->write($lastRow, 15, number_format(mysql_result($resAllow,$j,"allowAmt"),2),$row2);
					else
						$worksheet->write($lastRow, 15, "--",$row2);
				break;
				case "ALLOWANCES":
					if (in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat'])))				
						$worksheet->write($lastRow, 16, number_format(mysql_result($resAllow,$j,"allowAmt"),2),$row2);
					else
						$worksheet->write($lastRow, 16, "--",$row2);
				break;				
				
			}
		}
	if ($ctr == $num) {
		$lastRow++;
		$worksheet->write($lastRow, 0, "Total: $ctrDept",$row);
		$lastRow++;
		$worksheet->write($lastRow, 0, "Grand Total: $num",$row);
	}
	
	$lastRow++;
	$tmpDept = mysql_result($resGetDealsList,$i,"deptDesc");
	$tmpStat=mysql_result($resGetDealsList,$i,"emp_stat");
}
$lastRow=$lastRow+2;
$userId= $inqTSObj->getSeesionVars();
$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
$worksheet->write($lastRow, 0, "* * * End of report. Nothing follows. * * *",$headerFormat);
for ($j=1; $j<=9; $j++) {
	$worksheet->write($lastRow, $j, "",$headerFormat);
}
$lastRow=$lastRow+2;
$worksheet->write($lastRow, 1, $prntdBy,$detailBorder);
$workbook->close();


?>
