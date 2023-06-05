<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','D:\wamp\php\PEAR');
	require_once 'Spreadsheet/Excel/Writer.php';
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	include("../../../includes/config.php");
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
############################ Q U E R Y ##################################
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$empBrnCode 		= 	$_GET['empBrnCode'];
	$fromDate			= 	$_GET["fromDate"];
	$toDate				=	$_GET["toDate"];

$compName = $inqTSObj->getCompanyName($_SESSION["company_code"]);
$empNo		= $_GET['empNo'];
$empName	= $_GET['empName'];
$empDiv		= $_GET['empDiv'];
$empDept	= $_GET['empDept'];
$empSect 	= $_GET['empSect'];
$branch		= $_GET['branch'];
$loc		= $_GET['loc'];
$groupType 	= $_SESSION['pay_group'];
if ($groupType==1) $groupName = "GROUP 1"; else $groupName = "GROUP 2"; 
$orderBy 	= $_GET['orderBy'];
$catType	= $_SESSION['pay_category'];
$catName 	= $inqTSObj->getEmpCatArt($sessionVars['compCode'], $catType);
$payPd 		= $_GET['payPd'];
$arrPayPd 	= $inqTSObj->getSlctdPd($compCode,$payPd);
if ($empNo>"") {
	$empNo1 = " AND (tblEmpMast.empNo LIKE '{$empNo}%')";
} else {
	$empNo1 = "";
	if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
}
if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
if ($groupType<3) {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}

if (!$inqTSObj->getPeriod($payPd)) {
	$hist = "hist";
}
if ($branch != 0 && $empNo=="") {
	if ($loc == 1) {
		$branch = " AND empBrnCode = '$branch' AND empLocCode='0001'";
	} elseif ($loc == 2) {
		$branch = " AND empBrnCode = '$branch' AND empLocCode='$branch'";
	} else {
		$branch = " AND empBrnCode = '$branch'";
	}
} else {
	$branch = "";
}
$sql ="Select empPayGrp,emp.empNo,dateHired,endDate= case when dateResigned is null then endDate else dateResigned end
,emplastName+', '+empFirstName+' '+left(empMidName,1)+'.' as empName, brnShortDesc ,posDesc,empStat = case (empStat) when 'RG' then 'Regular' when 'TR' then 'Terminated' when 'IN' then 'Transferred' when 'AWOL' then 'AWOL' when 'RS' then 'Resigned' end
,netSalary
from tblEmpmast emp inner join tblbranch on empBrnCode=brnCode
inner join tblPosition pos on empPosId=posCode 
inner join tblPayrollSummary$hist pay on emp.empNo=pay.empNo 
inner join tblLastPayEmp l on pay.empNo=l.empNo and pay.pdYEar=l.pdYEar and pay.pdNumber=l.pdNumber and pay.payGrp=l.payGrp
where emp.empNo in (Select empNo from tbllastPayEmp) and pay.payCat=9
								AND pay.pdYear='{$arrPayPd['pdYear']}'
								AND pay.pdNumber = '{$arrPayPd['pdNumber']}'
								AND pay.payGrp = '{$_SESSION['pay_group']}'
  $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $branch order by empname
";
$resEmpList = $inqTSObj->execQry($sql);
$arrEmpList = $inqTSObj->getArrRes($resEmpList);
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL
$workbook = new Spreadsheet_Excel_Writer();
$deptHeader = $workbook->addFormat(array('Size' => 10,
								  'Color' => 'blue',
								  'bold'=> 1));
$headerFormat = $workbook->addFormat(array('Size' => 10,
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
$labelFormat = $workbook->addFormat(array('Size' => 10,
								  'bold'=> 1,
								  'Align' => 'Left',
								  'border' => 2));							
$detailrBorderAlignRight   = $workbook->addFormat(array('Align' => 'right'));
$filename = "payrollsummarybydepartmentreport".$todaynewdate.".xls";
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('Last Pay Summary');
$worksheet->setLandscape();
$worksheet->freezePanes(array(8, 0));
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(1, 0, "RUN DATE: ".$today); 
$worksheet->write(2, 0, "REPORT ID: LPSUMMARY"); for ($j=1; $j<=3; $j++) { $worksheet->write(0, $j, "",""); } 
$worksheet->write(3, 0, $compName,$headerFormat); for ($j=1; $j<=11; $j++) { $worksheet->write(3, $j, "",$headerFormat); }
$worksheet->write(4, 0, "LAST PAY SUMMARY for GROUP: ".$_SESSION["pay_group"],$headerFormat); for ($j=1; $j<=11; $j++) { $worksheet->write(4, $j, "",$headerFormat); }
$worksheet->write(5, 0, "PAY PERIOD: ".date('m/d/Y',strtotime($arrPayPd['pdPayable'])),$headerFormat); for ($j=1; $j<=11; $j++) { $worksheet->write(5, $j, "",$headerFormat); }
$worksheet->write(6, 0, "",$headerFormat); for ($j=1; $j<=11; $j++) { $worksheet->write(6, $j, "",$headerFormat); }
$worksheet->setColumn(0,8,20);
$worksheet->write(7, 0, "Pay Group",$headerBorder);
$worksheet->write(7, 1, "Emp No.",$headerBorder);
$worksheet->write(7, 2, "Date hired",$headerBorder);
$worksheet->write(7, 3, "Resigned Date",$headerBorder);
$worksheet->write(7, 4, "Emp. Name",$headerBorder);
$worksheet->write(7, 5, "Branch",$headerBorder);
$worksheet->write(7, 6, "Position",$headerBorder);
$worksheet->write(7, 7, "Status",$headerBorder);
$worksheet->write(7, 8, "Salary",$headerBorder);
$lastRow = 8;
#####################################################content set up ############################################


## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
	$grandTotalHeadCount_Branch = $grandTotalBasic_Branch = $grandTotalAbsent_Branch = $grandTotaltardUt_Branch = $grandTotalOtNd_Branch = $grandTotalOthIncome_Branch = $grandTotal_Branch =$grandTotalAllow_Branch= $grandTotalOthIncomeOt_Branch = 0;

foreach($arrEmpList as $val) {
	  $worksheet->write($lastRow, 0, $val['empPayGrp'], $detailBorder);	
	  $worksheet->write($lastRow, 1, $val['empNo'], $detailBorder);	
	  $worksheet->write($lastRow, 2, date('m/d/Y',strtotime($val['dateHired'])), $detailBorder);	
	  $worksheet->write($lastRow, 3, date('m/d/Y',strtotime($val['endDate'])), $detailBorder);	
	  $worksheet->write($lastRow, 4, $val['empName'] , $detailBorder);
	  $worksheet->write($lastRow, 5, $val['brnShortDesc'], $detailBorder);
	  $worksheet->write($lastRow, 6, $val['posDesc'], $detailBorder);
	  $worksheet->write($lastRow, 7, $val['empStat'], $detailBorder);
	  $worksheet->write($lastRow, 8, number_format($val['netSalary'],2) , $detailBorder);
	  $lastRow++;	
}
						
								

$lastRow=$lastRow+2;
$userId= $inqTSObj->getSeesionVars();
$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
$worksheet->write($lastRow, 0, "* * * End of report. Nothing follows. * * *",$headerFormat);
for ($j=1; $j<=11; $j++) {
	$worksheet->write($lastRow, $j, "",$headerFormat);
}
$lastRow=$lastRow+2;
$worksheet->write($lastRow, 1, $prntdBy,"");
$workbook->close();
?>