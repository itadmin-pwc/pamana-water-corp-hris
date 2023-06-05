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
	$frmdate				= date('Y-m-d',strtotime($_POST['txtfrDate']));
	$todate					= date('Y-m-d',strtotime($_POST['txttoDate']));
	$divcode 				= $_POST['empDiv'];	
	$deptcode 				= $_POST['empDept'];	
	$compName 		= $inqTSObj->getCompanyName($compCode);
############################ Q U E R Y ##################################
if($brnCode==0){
	$branch ="";	
}
else{
	$branch = " AND (tblEmpMast.empBrnCode='".$brnCode."')";	
}
if($divcode==0){
	$div = "";	
}
else{
	$div = " AND tblEmpMast.empDiv='".$divcode."'";
}
if($deptcode==0){
	$dept = "";	
}
else{
	$dept = " AND tblEmpMast.empDepCode='".$deptcode."'";
}

	$sqlRD = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, 
				tblDepartment.deptDesc, tblPosition.posDesc, tblEmpMast.empEndDate AS empEndDate, 
				CASE emppaytype WHEN 'M' THEN Concat(empmrate, '/mo.') WHEN 'D' THEN Concat(empdrate, '/day') END as salary_ko,
				tblEmpMast.empStat, Case tblEmpMast.employmentTag When 'RG' then 'REGULAR' when 'CN' then 'CONTRACTUAL' 
					when 'PR' then 'PROBATIONARY' end as emp_stat, 
				tblEmpMast.dateHired AS dateHired, tblBranch.brnDesc
			  FROM tblEmpMast 
			  INNER JOIN tblDepartment ON tblEmpMast.empDiv = tblDepartment.divCode 
			  	AND tblEmpMast.empDepCode = tblDepartment.deptCode 
			  	AND (tblDepartment.deptLevel = 2) 
			  INNER JOIN tblPosition ON tblEmpMast.empPosId = tblPosition.posCode 
			  	AND tblempmast.compcode = tblposition.compcode
			  INNER JOIN tblBranch on tblEmpMast.empBrnCode=tblBranch.brnCode	
			  WHERE (tblEmpMast.compCode = '{$_SESSION['company_code']}') 
			  	AND (tblDepartment.compCode = '{$_SESSION['company_code']}') 
			  	AND (tblEmpMast.empStat='RS')
			  	AND tblEmpMast.empEndDate BETWEEN '".$frmdate."' and '".$todate."'
			  	$branch $div $dept
			  ORDER BY tblBranch.brnDesc Asc, tblDepartment.deptDesc, 
			  	tblEmpMast.empLastName, tblEmpMast.empFirstName";
	
	$resGetDealsList = mysql_query($sqlRD);
	$num = mysql_num_rows($resGetDealsList);
//	$sqlBr = "SELECT brnDesc FROM tblBranch WHERE brnCode = $brnCode AND compCode = '{$_SESSION['company_code']}'";
//	$resBr = mysql_query($sqlBr);
//	$numBr = mysql_num_rows($resBr);
//	if ($numBr>0) {
//		$brnName = mysql_result($resBr,0,"brnDesc");
//	} else {
//		$brnName = "";
//	}
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL
$workbook = new Spreadsheet_Excel_Writer();
$branchHeader = $workbook->addFormat(array('Size' => 10,'Color' => 'red','bold'=> 1));
$deptHeader = $workbook->addFormat(array('Size' => 10,'Color' => 'black','bold'=> 1));
$employmentHeader = $workbook->addFormat(array('Size' => 9,'Color' => 'black','bold'=> 1));
$headerFormat = $workbook->addFormat(array('Size' => 12,'Color' => 'black','bold'=> 1,'Align' => 'merge'));
$headerBorder    = $workbook->addFormat(array('border' => 4, 'Size' => 12,'Color' => 'black','bold'=> 1));
$detailrBorder   = $workbook->addFormat(array('border' => 2));
$detailrBorderAlignRight   = $workbook->addFormat(array('Align' => 'right'));
$filename = "eoc".$todaynewdate.".xls";
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('List of EOC');
$worksheet->setLandscape();
$worksheet->freezePanes(array(6, 0));
$worksheet->setColumn(0,1,5);
$worksheet->setColumn(2,9,20);
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(0, 0, $compName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(0, $j, "",$headerFormat); }
$worksheet->write(1, 0, "LIST OF EOC REPORT",$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(1, $j, "",$headerFormat); }
$worksheet->write(2, 0, $brnName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(2, $j, "",$headerFormat); }
$worksheet->write(3, 0, "RUN DATE: ".$today); 
$worksheet->write(4, 0, "REPORT ID: LSTEOC"); 
$worksheet->write(5, 2, "LAST NAME",$headerBorder);
$worksheet->write(5, 3, "FIRST NAME",$headerBorder);
$worksheet->write(5, 4, "MIDDLE NAME",$headerBorder);
$worksheet->write(5, 5, "DATE HIRED",$headerBorder);
$worksheet->write(5, 6, "END OF CONTRACT",$headerBorder);
$worksheet->write(5, 7, "BASIC RATE",$headerBorder);
$worksheet->write(5, 8, "POSITION",$headerBorder);
## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$lastRow = 6;
$brnName = "";
$tmpDept = "";

for ($i=0;$i<$num;$i++){ 
	if($brnName!=mysql_result($resGetDealsList,$i,"brnDesc")){
		if ($brnName!="") {
			$lastRow++;
		}
		$worksheet->write($lastRow, 0, mysql_result($resGetDealsList,$i,"brnDesc"),$branchHeader);
		$lastRow++;	
	}
	if ($tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
		if ($tmpDept!="") {
			$lastRow++;
		}
		$worksheet->write($lastRow, 0, mysql_result($resGetDealsList,$i,"deptDesc")." DEPARTMENT",$deptHeader);
		$lastRow++;
	}
	if ($tmpStat!=mysql_result($resGetDealsList,$i,"emp_stat") || $tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
		$worksheet->write($lastRow, 1, mysql_result($resGetDealsList,$i,"emp_stat"),$employmentHeader);
		$lastRow++;
	}
	$worksheet->write($lastRow, 2, mysql_result($resGetDealsList,$i,"empLastName"),$detailBorder);
	$worksheet->write($lastRow, 3, mysql_result($resGetDealsList,$i,"empFirstName"),$detailBorder);
	$worksheet->write($lastRow, 4, mysql_result($resGetDealsList,$i,"empMidName"),$detailBorder);
	$worksheet->write($lastRow, 5, date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"dateHired"))),$detailBorder);
	$worksheet->write($lastRow, 6, date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"empEndDate"))),$detailBorder);
	$worksheet->write($lastRow, 7, mysql_result($resGetDealsList,$i,"salary_ko"),$detailBorder);
	$worksheet->write($lastRow, 8, mysql_result($resGetDealsList,$i,"posDesc"),$detailBorder);
	$lastRow++;
	$tmpDept = mysql_result($resGetDealsList,$i,"deptDesc");
	$tmpStat=mysql_result($resGetDealsList,$i,"emp_stat");
	$brnName = mysql_result($resGetDealsList,$i,"brnDesc");
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