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
	$compName 		= $inqTSObj->getCompanyName($compCode);
############################ Q U E R Y ##################################
	$sqlRD = "Select emp.empNo, emp.empLastName, emp.empFirstName, emp.empMidName, dept.deptDesc, emp.empStat, emp.dateResigned, emp.endDate
			from tblEmpMast emp
			Inner join tblDepartment dept on emp.empDiv=dept.divCode and emp.empDepCode=dept.deptCode
			where empBrnCode='0001' and dept.divCode='5' and dept.deptCode='1'  and dept.deptLevel='2' 
				and (emp.empStat='RG' or (emp.dateResigned between '01/01/2013' and '12/31/2012') or (emp.endDate between '01/01/2013' and '12/31/2012'))
			order by empLastName ";	
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
//$worksheet->freezePanes(array(6, 0));
$worksheet->setColumn(0,1,5);
$worksheet->setColumn(2,9,20);
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(0, 0, $compName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(0, $j, "",$headerFormat); }
//$worksheet->write(1, 0, "gfdghsdf",$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(1, $j, "",$headerFormat); }
//$worksheet->write(3, 0, "RUN DATE: ".$today); 
//$worksheet->write(4, 0, "REPORT ID: LSTUMWB");
$worksheet->write(5, 3, "JANUARY"); 
$worksheet->write(5, 4, "FEBRUARY"); 
$worksheet->write(5, 5, "MARCH"); 
$worksheet->write(5, 6, "APRIL"); 
$worksheet->write(5, 7, "MAY"); 
$worksheet->write(5, 8, "JUNE"); 
$worksheet->write(5, 9, "JULY"); 
$worksheet->write(5, 10, "AUGUST"); 
$worksheet->write(5, 11, "SEPTEMBER"); 
$worksheet->write(5, 12, "OCTOBER"); 
$worksheet->write(5, 13, "NOVEMBER"); 
$worksheet->write(5, 14, "DECEMBER"); 

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$lastRow = 6;
$res = "";
for($i=0;$i<$num;$i++){
	if(mysql_result($resGetDealsList,$i,"dateResigned")!=""){
		$res = " / Resigned Date = ".mysql_result($resGetDealsList,$i,"dateResigned");
	}
	if(mysql_result($resGetDealsList,$i,"dateResigned")!=""){
		$res = " / End Date = ".mysql_result($resGetDealsList,$i,"endDate");
	}
	$worksheet->write($lastRow, 2, mysql_result($resGetDealsList,$i,"empNo")." - " . mysql_result($resGetDealsList,$i,"empLastName") . ", " .  mysql_result($resGetDealsList,$i,"empFirstName") . " " .  mysql_result($resGetDealsList,$i,"empMidName") . $res,$headerFormat);
	
		$sqlRD1 = "SELECT hist.empNo, emp.empLastName, emp.empFirstName, emp.empMidName, month(pd.pdPayable) as pdpay,hist.pdYear, sum(hist.grossEarnings) as grossEarnings, 
		sum(hist.netSalary) as netSalary, sum(hist.sprtAllowAdvance) as allowance, sum(hist.empEcola) as eCola, gov.sssEmp, 
		gov.phicEmp, gov.hdmfEmp, case  month(pd.pdPayable) when '1' then 'JANUARY' WHEN '2' THEN 'FEBRUARY' WHEN '3' THEN 'MARCH' WHEN '4' THEN 'APRIL' WHEN '5' THEN 'MAY' WHEN '6' THEN 'JUNE' WHEN '7' THEN 'JULY' WHEN '8' THEN 'AUGUST' WHEN '9' THEN 'SPTEMBER' WHEN '10' THEN 'OCTOBER' WHEN '11' THEN 'NOVEMBER' WHEN '12' THEN 'DECEMBER' END AS MM
	FROM tblPayrollSummaryHist hist
	Inner Join tblPayPeriod pd on hist.pdNumber=pd.pdNumber and hist.pdYear=pd.pdYear and hist.payCat=pd.payCat and hist.payGrp=pd.payGrp
	Inner Join tblEmpMast emp on hist.empNo=emp.empNo
	Inner Join tblMtdGovtHist gov on hist.empNo=gov.empNo and hist.pdYear=gov.pdYear and month(pd.pdPayable)=gov.pdMonth
	where hist.pdYear='2013'  and emp.empNo='".mysql_result($resGetDealsList,$i,"empNo")."'
	group by month(pd.pdPayable),hist.pdYear, hist.empNo, emp.empLastName, emp.empFirstName, emp.empMidName, gov.sssEmp, 
		gov.phicEmp, gov.hdmfEmp
	order by month(pd.pdPayable), hist.empNo";
		$resGetDealsList1 = mysql_query($sqlRD1);
		$num1 = mysql_num_rows($resGetDealsList1);
		$lastRow=$lastRow+1;
		for($e=0;$e<$num1;$e++){
			$grossEarnings = mysql_result($resGetDealsList1,$e,"grossEarnings");
			$netSalary = mysql_result($resGetDealsList1,$e,"netSalary");
			$allowance = mysql_result($resGetDealsList1,$e,"allowance");
			$eCola = mysql_result($resGetDealsList1,$e,"eCola");
			$sssEmp = mysql_result($resGetDealsList1,$e,"sssEmp");
			$phicEmp = mysql_result($resGetDealsList1,$e,"phicEmp");
			$hdmfEmp = mysql_result($resGetDealsList1,$e,"hdmfEmp");
			
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="1" && mysql_result($resGetDealsList1,$e,"pdpay")!=""){
					$worksheet->write($lastRow+1, 2, "GROSS INCOME",$detailBorder);	
					$worksheet->write($lastRow+2, 2, "NET INCOME",$detailBorder);	
					$worksheet->write($lastRow+3, 2, "MONTHLY ALLOWANCE",$detailBorder);	
					$worksheet->write($lastRow+4, 2, "ECOLA",$detailBorder);	
					$worksheet->write($lastRow+5, 2, "SSS CONTRIBUTION",$detailBorder);	
					$worksheet->write($lastRow+6, 2, "PHILHEALTH CONTRIBUTION",$detailBorder);	
					$worksheet->write($lastRow+7, 2, "PAGIBIG CONTRIBUTION",$detailBorder);	
					$worksheet->write($lastRow+1, 3, $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow+2, 3, $netSalary,$detailBorder);	
					$worksheet->write($lastRow+3, 3, $allowance,$detailBorder);	
					$worksheet->write($lastRow+4, 3, $eCola,$detailBorder);	
					$worksheet->write($lastRow+5, 3, $sssEmp,$detailBorder);	
					$worksheet->write($lastRow+6, 3, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow+7, 3, $hdmfEmp,$detailBorder);
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="2"){
					$worksheet->write($lastRow, 4,  $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow+1, 4, $netSalary,$detailBorder);	
					$worksheet->write($lastRow+2, 4, $allowance,$detailBorder);	
					$worksheet->write($lastRow+3, 4, $eCola,$detailBorder);	
					$worksheet->write($lastRow+4, 4, $sssEmp,$detailBorder);	
					$worksheet->write($lastRow+5, 4, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow+6, 4, $hdmfEmp,$detailBorder);				
			}	
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="3"){
					$worksheet->write($lastRow-1, 5, $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow, 5, $netSalary,$detailBorder);	
					$worksheet->write($lastRow+1, 5, $allowance,$detailBorder);	
					$worksheet->write($lastRow+2, 5, $eCola,$detailBorder);	
					$worksheet->write($lastRow+3, 5, $sssEmp,$detailBorder);	
					$worksheet->write($lastRow+4, 5, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow+5, 5, $hdmfEmp,$detailBorder);	
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="4"){
					$worksheet->write($lastRow-2, 6, $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow-1, 6, $netSalary,$detailBorder);	
					$worksheet->write($lastRow, 6, $allowance,$detailBorder);	
					$worksheet->write($lastRow+1, 6, $eCola,$detailBorder);	
					$worksheet->write($lastRow+2, 6,  $sssEmp,$detailBorder);	
					$worksheet->write($lastRow+3, 6, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow+4, 6, $hdmfEmp,$detailBorder);	
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="5"){
					$worksheet->write($lastRow-3, 7, $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow-2, 7, $netSalary,$detailBorder);	
					$worksheet->write($lastRow-1, 7, $allowance,$detailBorder);	
					$worksheet->write($lastRow, 7, $eCola,$detailBorder);	
					$worksheet->write($lastRow+1, 7,  $sssEmp,$detailBorder);	
					$worksheet->write($lastRow+2, 7, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow+3, 7, $hdmfEmp,$detailBorder);	
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="6"){
					$worksheet->write($lastRow-4, 8, $grossEarnings,$detailBorder);
					$worksheet->write($lastRow-3, 8, $netSalary,$detailBorder);	
					$worksheet->write($lastRow-2, 8, $allowance,$detailBorder);	
					$worksheet->write($lastRow-1, 8, $eCola,$detailBorder);	
					$worksheet->write($lastRow, 8,  $sssEmp,$detailBorder);	
					$worksheet->write($lastRow+1, 8, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow+2, 8, $hdmfEmp,$detailBorder);	
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="7"){
					$worksheet->write($lastRow-5, 9, $grossEarnings,$detailBorder);
					$worksheet->write($lastRow-4, 9, $netSalary,$detailBorder);	
					$worksheet->write($lastRow-3, 9, $allowance,$detailBorder);	
					$worksheet->write($lastRow-2, 9, $eCola,$detailBorder);	
					$worksheet->write($lastRow-1, 9,  $sssEmp,$detailBorder);	
					$worksheet->write($lastRow, 9, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow+1, 9, $hdmfEmp,$detailBorder);	
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="8"){
					$worksheet->write($lastRow-6, 10, $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow-5, 10, $netSalary,$detailBorder);	
					$worksheet->write($lastRow-4, 10, $allowance,$detailBorder);	
					$worksheet->write($lastRow-3, 10, $eCola,$detailBorder);	
					$worksheet->write($lastRow-2, 10,  $sssEmp,$detailBorder);	
					$worksheet->write($lastRow-1, 10, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow, 10, $hdmfEmp,$detailBorder);	
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="9"){
					$worksheet->write($lastRow-7, 11, $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow-6, 11, $netSalary,$detailBorder);	
					$worksheet->write($lastRow-5, 11, $allowance,$detailBorder);	
					$worksheet->write($lastRow-4, 11, $eCola,$detailBorder);	
					$worksheet->write($lastRow-3, 11,  $sssEmp,$detailBorder);	
					$worksheet->write($lastRow-2, 11, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow-1, 11, $hdmfEmp,$detailBorder);	
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="10"){
					$worksheet->write($lastRow-8, 12, $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow-7, 12, $netSalary,$detailBorder);	
					$worksheet->write($lastRow-6, 12, $allowance,$detailBorder);	
					$worksheet->write($lastRow-5, 12, $eCola,$detailBorder);	
					$worksheet->write($lastRow-4, 12,  $sssEmp,$detailBorder);	
					$worksheet->write($lastRow-3, 12, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow-2, 12, $hdmfEmp,$detailBorder);	
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="11"){
					$worksheet->write($lastRow-9, 13, $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow-8, 13, $netSalary,$detailBorder);	
					$worksheet->write($lastRow-7, 13, $allowance,$detailBorder);	
					$worksheet->write($lastRow-6, 13, $eCola,$detailBorder);	
					$worksheet->write($lastRow-5, 13,  $sssEmp,$detailBorder);	
					$worksheet->write($lastRow-4, 13, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow-3, 13, $hdmfEmp,$detailBorder);	
			}
			if(mysql_result($resGetDealsList1,$e,"pdpay")=="12"){
					$worksheet->write($lastRow-10, 14, $grossEarnings,$detailBorder);	
					$worksheet->write($lastRow-9, 14, $netSalary,$detailBorder);	
					$worksheet->write($lastRow-8, 14, $allowance,$detailBorder);	
					$worksheet->write($lastRow-7, 14, $eCola,$detailBorder);	
					$worksheet->write($lastRow-6, 14,  $sssEmp,$detailBorder);	
					$worksheet->write($lastRow-5, 14, $phicEmp,$detailBorder);	
					$worksheet->write($lastRow-4, 14, $hdmfEmp,$detailBorder);	
			}
			$lastRow++;	
			$grossEarnings = "";
			$netSalary = "";
			$allowance = "";
			$eCola = "";
			$sssEmp = "";
			$phicEmp = "";
			$hdmfEmp = "";
		}
		$lastRow=$lastRow+5;
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
