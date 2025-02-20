<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','C:\wamp\bin\php\php5.2.6\PEAR\pear');
	require_once 'Spreadsheet/Excel/Writer.php';
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$compCode = $_SESSION['company_code'];
	$empNo         			= $_GET['empNo'];
	$empName       			= $_GET['empName'];
	$empDiv        			= $_GET['empDiv'];
	$empDept       			= $_GET['empDept'];
	$empSect       			= $_GET['empSect'];
	$from					= date('Y-m-d',strtotime($_GET['from']));
	$to						= date('Y-m-d',strtotime($_GET['to']));
	$orderBy       = $_GET['orderBy'];
	
	if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
	//if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
	if ($from != "" && $to!= "" ) {
		$empStatDatefilter = " AND effectivitydate between '$from' AND '$to'";
		$dt = "Resigned Date $from - $to";
	}	
	$qryEmpList = "SELECT DISTINCT tblPAF_EmpStatushist.empNo, tblPAF_EmpStatushist.effectivitydate, tblEmpMast.empLastName, 
					tblEmpMast.empFirstName, tblEmpMast.empMidName, tblEmpMast.dateHired, tblEmpMast.empPayType, tblEmpMast.empDrate, 
					tblEmpMast.empMrate, tblBranch.brnShortDesc, tblPosition.posShortDesc, tblEmpMast.employmentTag
					FROM tblPAF_EmpStatushist 
					INNER JOIN tblEmpMast ON tblPAF_EmpStatushist.compCode = tblEmpMast.compCode 
					AND tblPAF_EmpStatushist.empNo = tblEmpMast.empNo 
					INNER JOIN tblBranch ON tblPAF_EmpStatushist.compCode = tblBranch.compCode 
					AND tblEmpMast.empBrnCode = tblBranch.brnCode 
					INNER JOIN tblPosition ON tblPAF_EmpStatushist.compCode = tblPosition.compCode 
					AND tblEmpMast.empPosId = tblPosition.posCode
					WHERE ((tblEmpMast.empStat = 'RS') or (tblEmpMast.empStat = 'IN')) 
					AND ((tblEmpMast.dateResigned is not null ) 
					OR (tblEmpMast.endDate is not null)) $empStatDatefilter 
					AND (tblPAF_EmpStatushist.compCode = '{$compCode}') 
					AND (tblEmpMast.dateResigned=tblPAF_EmpStatushist.effectivitydate 
						or tblEmpMast.endDate= tblPAF_EmpStatushist.effectivitydate)
					AND empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' 
					AND empNo='{$_SESSION['employee_number']}')
					$empNo1 $status $empStatDatefilter $empName1 $empDiv1 $empName1 $empDept1 $empSect1
					ORDER BY tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName
					 ";
	$resEmpList = mysql_query($qryEmpList);
	$num = mysql_num_rows($resEmpList);
	
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
$detailBorder   = $workbook->addFormat(array('border' => 0, 'Align'=>'left'));
$detailrBorderAlignRight   = $workbook->addFormat(array('Align' => 'right'));
$headerData = $workbook->addFormat(array('Align'=>'center','bold'=>1,'Size'=>10));
$dataFormat = $workbook->addFormat(array('Align'=>'center'));
$filename = "separatedEmployees".$todaynewdate.".xls";
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('Separated Emplyees');
$worksheet->setLandscape();
$worksheet->freezePanes(array(6, 0));
$worksheet->setColumn(0,1,5);
$worksheet->setColumn(2,9,20);
## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(0, 0, $compName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(0, $j, "",$headerFormat); }
$worksheet->write(1, 0, "SEPARATED EMPLOYEES",$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(1, $j, "",$headerFormat); }
$worksheet->write(3, 0, "RUN DATE: ".$today); 
$worksheet->write(4, 0, "REPORT ID: LSTSE"); 
$worksheet->write(5, 2, "BRANCH",$headerBorder);
$worksheet->write(5, 3, "EMPLOYEE NUMBER",$headerBorder);
$worksheet->write(5, 4, "EMPLOYEE NAME",$headerBorder);
$worksheet->write(5, 5, "DATE HIRED",$headerBorder);
$worksheet->write(5, 6, "RESIGNED STATUS",$headerBorder);
$worksheet->write(5, 7, "POSITION",$headerBorder);
$worksheet->write(5, 8, "NATURE OF SEPARATION",$headerBorder);
$worksheet->write(5, 9, "REASON",$headerBorder);
$worksheet->write(5, 10, "EFFECTIVITY",$headerBorder);
## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$lastRow = 6;
for ($i=0;$i<$num;$i++){ 
	if(mysql_result($resEmpList,$i,"employmentTag")=="RG"){
		$emptag="Regular";
	}
	elseif(mysql_result($resEmpList,$i,"employmentTag")=="PR"){
		$emptag="Probationary";
	}
	elseif(mysql_result($resEmpList,$i,"employmentTag")=="CN"){
		$emptag="Contractual";
	}
	else{
		$emptag="--";
	}
	
	$qryNatures = mysql_query("Select * from tblSeparatedEmployees inner join tblNatures on tblSeparatedEmployees.natureCode=tblNatures.natureCode where empNo='".mysql_result($resEmpList,$i,"empNo")."'");
	$num1 = mysql_num_rows($qryNatures);
		for ($x=0; $x<$num1;$x++){
			if(mysql_result($qryNatures,$x,"reason")==1){
				$payreason="Promotion";
			}
			elseif(mysql_result($qryNatures,$x,"reason")==2){
				$payreason="Merit Increase";	
			}
			elseif(mysql_result($qryNatures,$x,"reason")==5){
				$payreason="Gov't Mandate";	
			}
			elseif(mysql_result($qryNatures,$x,"reason")==4){
				$payreason="Salary Increase";	
			}
			elseif(mysql_result($qryNatures,$x,"reason")==6){
				$payreason="Alignment";	
			}
			elseif(mysql_result($qryNatures,$x,"reason")==7){
				$payreason="Regularization";	
			}
			elseif(mysql_result($qryNatures,$x,"reason")==8){
				$payreason="Probationary";	
			}
			else{
				$payreason=mysql_result($qryNatures,$x,"reason");	
			}
			$desc = mysql_result($qryNatures,$x,"Description");
		}
	$empno = (string)mysql_result($resEmpList,$i,"empNo");	
	
	$worksheet->write($lastRow, 2, mysql_result($resEmpList,$i,"brnShortDesc"),$detailBorder);
	$worksheet->write($lastRow, 3, " ".$empno,$detailBorder);
	$worksheet->write($lastRow, 4, mysql_result($resEmpList,$i,"empLastName").", ".mysql_result($resEmpList,$i,"empFirstName")." ".mysql_result($resEmpList,$i,"empMidName"),$detailBorder);
	$worksheet->write($lastRow, 5, date('m/d/Y',strtotime(mysql_result($resEmpList,$i,"dateHired"))),$detailBorder);
	$worksheet->write($lastRow, 6, $emptag,$detailBorder);
	$worksheet->write($lastRow, 7, mysql_result($resEmpList,$i,"posShortDesc"),$detailBorder);
	$worksheet->write($lastRow, 8, $desc,$detailBorder);
	$worksheet->write($lastRow, 9, $payreason,$detailBorder);
	$worksheet->write($lastRow, 10, date('m/d/Y',strtotime(mysql_result($resEmpList,$i,"effectivitydate"))),$dataFormat);
	$lastRow++;
}
$worksheet->write($lastRow, 0, "* * * End of report. Nothing follows. * * *",$headerFormat);
for ($j=1; $j<=9; $j++) {
	$worksheet->write($lastRow, $j, "",$headerFormat);
}
$lastRow=$lastRow+2;
$worksheet->write($lastRow, 1, $prntdBy,$detailBorder);
$workbook->close();

?>
