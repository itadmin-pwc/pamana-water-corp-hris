<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','C:\wamp\bin\php\php5.2.6\PEAR\pear');
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
	$frmdate				= $_POST['txtfrDate'];
	$todate					= $_POST['txttoDate'];
	$compName 		= $inqTSObj->getCompanyName($compCode);
############################ Q U E R Y ##################################
	$sqlRD = "SELECT dbo.tblMinimumWageHist.compCode, tblBranch.brnDesc, dbo.tblMinimumWageHist.brnCode, 
	dbo.tblMinimumWageHist.minimumWage_Old, dbo.tblMinimumWageHist.minimumWage_New, 
	dbo.tblMinimumWageHist.eCola_Old, dbo.tblMinimumWageHist.eCola_New, dbo.tblMinimumWageHist.effectiveDate, 
	dbo.tblMinimumWageHist.dateReleased 
	FROM dbo.tblMinimumWageHist 
	INNER JOIN tblBranch ON dbo.tblMinimumWageHist.brnCode = tblBranch.brnCode 
	WHERE dbo.tblMinimumWageHist.dateReleased BETWEEN '".$frmdate."' and '".$todate."'
	ORDER BY dbo.tblMinimumWageHist.dateReleased DESC,tblBranch.brnDesc";
	
	$resGetDealsList = mysql_query($sqlRD);
	$num = mysql_num_rows($resGetDealsList);
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL
$workbook = new Spreadsheet_Excel_Writer();
$deptHeader = $workbook->addFormat(array('Size' => 10,
								  'Color' => 'blue',
								  'bold'=> 1));
$headerFormat = $workbook->addFormat(array('Size' => 10,
								  'Color' => 'red',
								  'bold'=> 1,
								  'Align' => 'merge'));
$headerBorder    = $workbook->addFormat(array('border' => 4,'bold'=>1));
$detailrBorder   = $workbook->addFormat(array('border' => 2));
$detailrBorderAlignRight   = $workbook->addFormat(array('Align' => 'right'));
$headerData = $workbook->addFormat(array('Align'=>'center','bold'=>1,'Size'=>10));
$dataFormat = $workbook->addFormat(array('Align'=>'center'));
$filename = "branchMinWage".$todaynewdate.".xls";
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('Branch Min. Wage');
$worksheet->setLandscape();
$worksheet->freezePanes(array(6, 0));
$worksheet->setColumn(0,1,5);
$worksheet->setColumn(2,9,20);
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(0, 0, $compName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(0, $j, "",$headerFormat); }
$worksheet->write(1, 0, "Branch Listing of Updated Minimum Wage",$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(1, $j, "",$headerFormat); }
//$worksheet->write(2, 0, $brnName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(2, $j, "",$headerFormat); }
$worksheet->write(3, 0, "RUN DATE: ".$today); 
$worksheet->write(4, 0, "REPORT ID: LSTUMWB"); 
$worksheet->write(5, 2, "BRANCH NAME",$headerBorder);
$worksheet->write(5, 3, "OLD MINIMUM WAGE",$headerBorder);
$worksheet->write(5, 4, "NEW MINIMUM WAGE",$headerBorder);
$worksheet->write(5, 5, "OLD ECOLA",$headerBorder);
$worksheet->write(5, 6, "NEW ECOLA",$headerBorder);
$worksheet->write(5, 7, "EFFECTIVE DATE",$headerBorder);
$worksheet->write(5, 8, "DATE PROCESSED",$headerBorder);
## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$lastRow = 6;
for ($i=0;$i<$num;$i++){ 
	$worksheet->write($lastRow, 2, mysql_result($resGetDealsList,$i,"brnDesc"),$detailBorder);
	$worksheet->write($lastRow, 3, mysql_result($resGetDealsList,$i,"minimumWage_Old"),$detailBorder);
	$worksheet->write($lastRow, 4, mysql_result($resGetDealsList,$i,"minimumWage_New"),$detailBorder);
	$worksheet->write($lastRow, 5, mysql_result($resGetDealsList,$i,"eCola_Old"),$detailBorder);
	$worksheet->write($lastRow, 6, mysql_result($resGetDealsList,$i,"eCola_New"),$detailBorder);
	$worksheet->write($lastRow, 7, $inqTSObj->valDateArt(mysql_result($resGetDealsList,$i,"effectiveDate")),$dataFormat);
	$worksheet->write($lastRow, 8, $inqTSObj->valDateArt(mysql_result($resGetDealsList,$i,"dateReleased")),$dataFormat);
	$lastRow++;
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
