<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','D:\wamp\php\PEAR');
	require_once 'Spreadsheet/Excel/Writer.php';
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("ts_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
		$cutfrom=$_GET['from'];
		$cutto=$_GET['to'];
		$hist=$_GET['hist'];
		$todaynewdate=$cutfrom ." - " . $cutto;
		$common= new commonObj();
	
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL
$workbook = new Spreadsheet_Excel_Writer();
$deptHeader = $workbook->addFormat(array('Size' => 10,
								  'Color' => 'blue',
								  'bold'=> 1));
$headerFormat = $workbook->addFormat(array('Size' => 10,
								  'Color' => 'red',
								  'bold'=> 1,
								  'Align' => 'merge'));
$headerBorder    = $workbook->addFormat(array('border' => 1.5,'Size'=>10,'Color'=>'red','Align'=>'Center'));
$detailBorder   = $workbook->addFormat(array('border' => 1,'Align'=>'Left'));
$detailBorder2   = $workbook->addFormat(array('border' => 1,'Align'=>'Center'));
$branchHeader = $workbook->addFormat(array('Size'=>10,
									'bold'=>1,
									'Align'=>'Left',
									'Color'=>'red'
									));
$detailrBorderAlignRight   = $workbook->addFormat(array('Align' => 'right'));
$filename = "Timesheet SUmmary".$todaynewdate.".xls";
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('Management Report');
$worksheet->setLandscape();
$worksheet->freezePanes(array(6, 0));

## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(0, 0, $compName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(0, $j, "",$headerFormat); }
$worksheet->write(1, 0, "TIMESHEET SUMMARY REPORT",$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(1, $j, "",$headerFormat); }
//$worksheet->write(2, 0, $brnName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(2, $j, "",$headerFormat); }
$worksheet->write(3, 0, "RUN DATE: ".$today); 
$worksheet->write(4, 0, "CUT-OFF:" . $_GET['from']." to ".$_GET['to']); for ($j=1; $j<=3; $j++) { $worksheet->write(0, $j, "",""); } 
$worksheet->write(5, 0, "EMPLOYEE No.",$headerBorder);
$worksheet->write(5, 1, "EMPLOYEE NAME",$headerBorder);
$worksheet->write(5, 2, "DEPARTMENT",$headerBorder);
$worksheet->write(5, 3, "BRANCH",$headerBorder);
$worksheet->write(5, 4, "CATEGORY",$headerBorder);
$worksheet->write(5, 5, "OTLT8",$headerBorder);
$worksheet->write(5, 6, "OTGT8",$headerBorder);
$worksheet->write(5, 7, "HRSND",$headerBorder);
$worksheet->write(5, 8, "HrsRegND",$headerBorder);
$worksheet->write(5, 9, "TARDY",$headerBorder);
$worksheet->write(5, 10, "UT",$headerBorder);
$worksheet->write(5, 11, "HRSWORK",$headerBorder);


## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
if ($hist=='hist'){
$tsqry=	"SELECT
tbltk_timesheet$hist.tsDate AS tsDate,
tblempmast.empLastName AS empLastName,
tblempmast.empFirstName AS empFirstName,
tbltk_timesheet$hist.empNo AS empNo,
Sum(tbltk_overtime$hist.hrsOTLe8) AS OTL8,
Sum(tbltk_overtime$hist.hrsOTGt8) AS OTGT8,
Sum(tbltimesheet$hist.hrsRegNDLe8) AS HrsND,
Sum(tbltk_deductions$hist.hrsTardy) AS trdy,
Sum(tbltk_deductions$hist.hrsUT) AS ut,
Sum(tbltk_timesheet$hist.hrsWorked) AS hrswrk,
tblbranch.brnDesc AS brnDesc,
tbldepartment.deptDesc AS deptDesc,
tblpaycat.payCatDesc AS payCatDesc
FROM
((((((tblempmast
LEFT JOIN tbltk_timesheet$hist ON ((tbltk_timesheet$hist.empNo = tblempmast.empNo)))
LEFT JOIN tbltk_overtime$hist ON (((tbltk_overtime$hist.empNo = tbltk_timesheet$hist.empNo) AND (tbltk_overtime$hist.tsDate = tbltk_timesheet$hist.tsDate))))
LEFT JOIN tbltk_deductions$hist ON (((tbltk_deductions$hist.empNo = tbltk_timesheet$hist.empNo) AND (tbltk_deductions$hist.tsDate = tbltk_timesheet$hist.tsDate))))
JOIN tblbranch ON ((tblempmast.empBrnCode = tblbranch.brnCode)))
JOIN tbldepartment ON (((tblempmast.empDiv = tbldepartment.divCode) AND (tblempmast.empDepCode = tbldepartment.deptCode) AND (tblempmast.empSecCode = tbldepartment.sectCode))))
JOIN tblpaycat ON ((tblempmast.empPayCat = tblpaycat.payCat)))
INNER JOIN tbltimesheet$hist ON tbltimesheet$hist.empNo = tbltk_timesheet$hist.empNo AND tbltimesheet$hist.tsDate = tbltk_timesheet$hist.tsDate
where (`tbltk_timesheet$hist`.`tsDate` between '$cutfrom' and '$cutto')
group by `tbltk_timesheet$hist`.`empNo`";
}else{
$tsqry=	"SELECT
tbltk_timesheet$hist.tsDate AS tsDate,
tblempmast.empLastName AS empLastName,
tblempmast.empFirstName AS empFirstName,
tbltk_timesheet$hist.empNo AS empNo,
Sum(tbltk_overtime$hist.hrsOTLe8) AS OTL8,
Sum(tbltk_overtime$hist.hrsOTGt8) AS OTGT8,
Sum(tbltk_overtime$hist.hrsNDLe8) AS HrsND,
Sum(tbltk_overtime$hist.hrsRegNDLe8) AS HrsRegND,
Sum(tbltk_deductions$hist.hrsTardy) AS trdy,
Sum(tbltk_deductions$hist.hrsUT) AS ut,
Sum(tbltk_timesheet$hist.hrsWorked) AS hrswrk,
tblbranch.brnDesc AS brnDesc,
tbldepartment.deptDesc AS deptDesc,
tblpaycat.payCatDesc AS payCatDesc
from ((((((`tblempmast` left join `tbltk_timesheet$hist` on((`tbltk_timesheet$hist`.`empNo` = `tblempmast`.`empNo`))) left join `tbltk_overtime$hist` on(((`tbltk_overtime$hist`.`empNo` = `tbltk_timesheet$hist`.`empNo`) and (`tbltk_overtime$hist`.`tsDate` = `tbltk_timesheet$hist`.`tsDate`)))) left join `tbltk_deductions$hist` on(((`tbltk_deductions$hist`.`empNo` = `tbltk_timesheet$hist`.`empNo`) and (`tbltk_deductions$hist`.`tsDate` = `tbltk_timesheet$hist`.`tsDate`)))) join `tblbranch` on((`tblempmast`.`empBrnCode` = `tblbranch`.`brnCode`))) join `tbldepartment` on(((`tblempmast`.`empDiv` = `tbldepartment`.`divCode`) and (`tblempmast`.`empDepCode` = `tbldepartment`.`deptCode`) and (`tblempmast`.`empSecCode` = `tbldepartment`.`sectCode`)))) join `tblpaycat` on((`tblempmast`.`empPayCat` = `tblpaycat`.`payCat`)))
where (`tbltk_timesheet$hist`.`tsDate` between '$cutfrom' and '$cutto')
group by `tbltk_timesheet$hist`.`empNo`";
}
$arrRD= $common->execQry($tsqry);
$arrTSlist=$common->getArrRes($arrRD);

$lastRow = 6;

	foreach ($arrTSlist  as  $tslist) {	
	$worksheet->write($lastRow, 0,$tslist['empNo'],$detailBorder);
	$worksheet->write($lastRow, 1,$tslist['empLastName']." ".$tslist['empFirstName'],$detailBorder);
	$worksheet->write($lastRow, 2,$tslist['deptDesc'],$detailBorder);
	$worksheet->write($lastRow, 3,$tslist['brnDesc'],$detailBorder);
	$worksheet->write($lastRow, 4,$tslist['payCatDesc'],$detailBorder);
	$worksheet->write($lastRow, 5,$tslist['OTL8'],$detailBorder);
	$worksheet->write($lastRow, 6,$tslist['OTGT8'],$detailBorder);
	$worksheet->write($lastRow, 7,$tslist['HrsND'],$detailBorder);
	$worksheet->write($lastRow, 8,$tslist['HrsRegND'],$detailBorder);
	$worksheet->write($lastRow, 9,$tslist['trdy'],$detailBorder);
	$worksheet->write($lastRow, 10,$tslist['ut'],$detailBorder);
	$worksheet->write($lastRow, 11,$tslist['hrswrk'],$detailBorder);

	$lastRow++;
}

$workbook->close();
?>