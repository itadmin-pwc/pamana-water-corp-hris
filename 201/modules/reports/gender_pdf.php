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
	$brnCode         		= $_POST['branch'];
	$compName 		= $inqTSObj->getCompanyName($compCode);
############################ Q U E R Y ##################################
	if($brnCode==0){
		$sqlBr = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' and brnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')";
	}
	else{
		$sqlBr = "SELECT * FROM tblBranch WHERE compCode = '{$_SESSION['company_code']}' and brnCode = '{$brnCode}'";
	}
	$resBr = mysql_query($sqlBr);
	$numBranches = mysql_num_rows($resBr);

//	$sqlRD = "Exec sp_GenderCount $brnCode";
//	
//	$resGetDealsList = mysql_query($sqlRD);
//	$num = mysql_num_rows($resGetDealsList);
//	$sqlBr = "SELECT brnDesc FROM tblBranch WHERE brnCode = $brnCode AND compCode = '{$_SESSION['company_code']}'";
	if ($numBr>0) {
		$brnName = mysql_result($resBr,0,"brnDesc");
	} else {
		$brnName = "";
	}
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL
$workbook = new Spreadsheet_Excel_Writer();
$deptHeader = $workbook->addFormat(array('Size' => 10,
								  'Color' => 'blue',
								  'bold'=> 1));
$headerFormat = $workbook->addFormat(array('Size' => 10,
								  'Color' => 'red',
								  'bold'=> 1,
								  'Align' => 'merge'));
$headerBorder    = $workbook->addFormat(array('border' => 4));
$detailrBorder   = $workbook->addFormat(array('border' => 2));
$branchHeader = $workbook->addFormat(array('Size'=>10,
									'bold'=>1,
									'Align'=>'Left',
									'Color'=>'red'
									));
$detailrBorderAlignRight   = $workbook->addFormat(array('Align' => 'right'));
$filename = "gender".$todaynewdate.".xls";
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('Gender Report');
$worksheet->setLandscape();
$worksheet->freezePanes(array(6, 0));
$worksheet->setColumn(0,0,50);
$worksheet->setColumn(2,9,20);
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(0, 0, $compName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(0, $j, "",$headerFormat); }
$worksheet->write(1, 0, "LIST OF GENDER REPORT",$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(1, $j, "",$headerFormat); }
//$worksheet->write(2, 0, $brnName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(2, $j, "",$headerFormat); }
$worksheet->write(3, 0, "RUN DATE: ".$today); 
$worksheet->write(4, 0, "REPORT ID: LSTGENDER"); 
$worksheet->write(5, 2, "MALE",$headerBorder);
$worksheet->write(5, 3, "FEMALE",$headerBorder);
$worksheet->write(5, 4, "TOTAL",$headerBorder);
## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$lastRow = 6;
	for($b=0;$b<$numBranches;$b++){
		if($tmpBranch!=mysql_result($resBr,$b,"brnDesc")){
			if($tmpBranch!=""){
				$lastRow++;
			}
			$worksheet->write($lastRow, 0, mysql_result($resBr,$b,"brnDesc"),$branchHeader); for ($j=1; $j<=3; $j++) { $worksheet->write($lastRow, $j, "",$branchHeader); }
			$lastRow++;	
		}
				$sqlRD = "Exec sp_GenderCount ".mysql_result($resBr,$b,"brnCode");
				$resGetDealsList = mysql_query($sqlRD);
				$num = mysql_num_rows($resGetDealsList);

				$sqlBranches = "SELECT brnDesc FROM tblBranch WHERE compCode = '{$_SESSION['company_code']}' and brnCode = '".mysql_result($resBr,$b,"brnCode")."'";
				$resBranches=mysql_query($sqlBranches);
				for ($i=0;$i<$num;$i++){ 
					if ($tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
						if ($tmpDept!="") {
							$lastRow++;
						}
						$worksheet->write($lastRow, 0, "     ".mysql_result($resGetDealsList,$i,"deptDesc")." DEPARTMENT");
						$lastRow++;
					}
					$worksheet->write($lastRow, 2, mysql_result($resGetDealsList,$i,"maleCtr"),$detailBorder);
					$worksheet->write($lastRow, 3, mysql_result($resGetDealsList,$i,"femaleCtr"),$detailBorder);
					$worksheet->write($lastRow, 4, mysql_result($resGetDealsList,$i,"totalCtr"),$detailBorder);
					$lastRow++;
					$tmpDept = mysql_result($resGetDealsList,$i,"deptDesc");
					//$tmpStat=mysql_result($resGetDealsList,$i,"empStat");
				}
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