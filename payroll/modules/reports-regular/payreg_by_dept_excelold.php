<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','C:\wamp\bin\php\php5.2.6\PEAR\pear');
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
if(date("m", strtotime($_GET["fromDate"]))==date("m", strtotime($_GET["toDate"]))){
	$dateHeader	= 	date("F ", strtotime($_GET["fromDate"])).", ".date("Y", strtotime($_GET["fromDate"]));
}
else{
	$dateHeader	= 	date("F ", strtotime($_GET["fromDate"])). " - " .date("F ", strtotime($_GET["toDate"])).", ".date("Y", strtotime($_GET["fromDate"]));
}

if ($empDiv!="0") {
	$empDiv1 = " AND (tblPaySum.empDivCode = '{$empDiv}')"; 
} else {
	$empDiv1 = "";
}
if ($empDept!="0") {
	$empDept1 = " AND (tblPaySum.empDepCode = '{$empDept}')"; 
} else {
	$empDept1 = "";
}

if ($empBrnCode!="0") 
{
	$empBrnCode1 = " AND (tblPaySum.empBrnCode = '{$empBrnCode}')";
} 
else 
{
	$empBrnCode1 = "";
}

$where_empmast = $empDiv1.$empDept1.$empBrnCode1;

	$qrypdNum = "SELECT DISTINCT  pdNumber FROM tblPayPeriod WHERE pdPayable BETWEEN '".date("m/d/Y", strtotime($fromDate))."' 
			AND '".date("m/d/Y", strtotime($toDate))."' AND payGrp = '".$_SESSION["pay_group"]."' AND pdProcessTag='Y'";

	$tbl = "tblPayrollSummaryHist";
	$tblytd = "tblYtdDataHist";
	$tblEarn = "tblEarningsHist";
	$tblDed = "tblDeductionsHist";	
	$tblBrn = "tblBranch";

$empQry = "Select 
				brn.brnDesc, brn.brnCode
				from $tbl tblPaySum
				left join tblEmpMast tblEmp on tblPaySum.empNo=tblEmp.empNo
				left join tblTeu teu on tblEmp.empTeu=teu.teuCode
				left join $tblytd tblytd on tblytd.empNo=tblPaySum.empNo
				inner join $tblBrn brn on tblPaySum.empBrnCode=brn.brnCode
				where tblPaySum.compCode='".$_SESSION["company_code"]."'
				AND tblPaySum.payGrp = '".$_SESSION["pay_group"]."'
				AND tblPaySum.pdYear = '".date("Y", strtotime($fromDate))."'
				AND tblPaySum.pdNumber in (".$qrypdNum.")
				$where_empmast
				group by brn.brnDesc, brn.brnCode
				order by brn.brnDesc";
$resBr = mysql_query($empQry);
$numBranches = mysql_num_rows($resBr);	

/*function getEarningsDetail($paySumBrnCode, $paySumDivCode, $paySumDeptCode, $trnCode, $fieldName, $trnLookUp, $fromDate, $qrypdNum)
{
	if($trnLookUp=="1"){
		$conTrnCode = ($trnCode!=""?"and tblEarn.trnCode in (Select trnCode from tblPayTransType where compCode='".$_SESSION["company_code"]."' and trnRecode='".$trnCode."' and trnStat='A')":"");
	}
	elseif($trnLookUp=="2"){
		$conTrnCode = "and tblEarn.trnCode in (Select trnCode from tblPayTransType where compCode='".$_SESSION['company_code']."' and trnCat='E' and trnStat='A' and trnEntry='Y' and trnCode not in (Select trnCode from tblAllowType where compCode='".$_SESSION["company_code"]."' and allowTypeStat='A'))"; 
	}
	elseif($trnLookUp=="3"){
		$conTrnCode = "and tblEarn.trnCode in (Select trnCode from tblPayTransType where compCode='".$_SESSION['company_code']."' and trnCat='E' and trnStat='A'  and trnCode  in (Select trnCode from tblAllowType where compCode='".$_SESSION["company_code"]."' and allowTypeStat='A'))"; 			
	}
	else{
		$conTrnCode = ($trnCode!=""?"and tblEarn.trnCode='".$trnCode."'":""); 
	}
			
			$qrytblEarn = "Select ".$fieldName."
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".$paySumBrnCode."' 
					and tblPaySum.empDivCode='".$paySumDivCode."' and
					tblPaySum.empDepCode='".$paySumDeptCode."'".$conTrnCode;
			$q=mysql_query($qrytblEarn);
			$restblEarn=mysql_fetch_assoc($q);
			return $restblEarn;
}
*/

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
$worksheet=&$workbook->addWorksheet('Payroll Summary Report');
$worksheet->setLandscape();
$worksheet->freezePanes(array(8, 0));
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(1, 0, "RUN DATE: ".$today); 
$worksheet->write(2, 0, "REPORT ID: PSDREPORT"); for ($j=1; $j<=3; $j++) { $worksheet->write(0, $j, "",""); } 
$worksheet->write(3, 0, $compName,$headerFormat); for ($j=1; $j<=11; $j++) { $worksheet->write(3, $j, "",$headerFormat); }
$worksheet->write(4, 0, "Payroll Summary for GROUP: ".$_SESSION["pay_group"],$headerFormat); for ($j=1; $j<=11; $j++) { $worksheet->write(4, $j, "",$headerFormat); }
$worksheet->write(5, 0, "YEAR / MONTH: ".$dateHeader,$headerFormat); for ($j=1; $j<=11; $j++) { $worksheet->write(5, $j, "",$headerFormat); }
$worksheet->write(6, 0, "",$headerFormat); for ($j=1; $j<=11; $j++) { $worksheet->write(6, $j, "",$headerFormat); }

$worksheet->write(7, 0, "BRANCH",$headerBorder);
$worksheet->write(7, 1, "DIVISION",$headerBorder);
$worksheet->write(7, 2, "DEPARTMENT",$headerBorder);
$worksheet->write(7, 3, "HEAD COUNT",$headerBorder);
$worksheet->write(7, 4, "BASIC",$headerBorder);
$worksheet->write(7, 5, "ABSENT",$headerBorder);
$worksheet->write(7, 6, "TARDY / UT",$headerBorder);
$worksheet->write(7, 7, "OT / ND",$headerBorder);
$worksheet->write(7, 8, "ADJ. BASIC",$headerBorder);
$worksheet->write(7, 9, "AD. OT",$headerBorder);
$worksheet->write(7, 10, "ALLOWANCE",$headerBorder);
$worksheet->write(7, 11, "TOTAL",$headerBorder);
$lastRow = 8;
#####################################################content set up ############################################


## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
	$grandTotalHeadCount_Branch = $grandTotalBasic_Branch = $grandTotalAbsent_Branch = $grandTotaltardUt_Branch = $grandTotalOtNd_Branch = $grandTotalOthIncome_Branch = $grandTotal_Branch =$grandTotalAllow_Branch= $grandTotalOthIncomeOt_Branch = 0;


	for($b=0;$b<$numBranches;$b++){
		$worksheet->write($lastRow, 0, mysql_result($resBr,$b,"brnDesc"), $detailBorder);

		if($empDiv!=0){
			$conDiv = "and empDivCode='".$empDiv."'";
		}
		$qryDivision = "Select divCode,deptCode,deptShortDesc from tblDepartment
						where compCode='".$_SESSION["company_code"]."' and deptLevel='1'
						and deptStat='A'
						and divCode in 
						(Select distinct(empDivCode) from tblPayrollSummaryHist where compCode='".$_SESSION["company_code"]."'
						and payGrp='".$_SESSION["pay_group"]."'  
						and pdYear = '".date("Y", strtotime($fromDate))."' and pdNumber in (".$qrypdNum.") 
						and empBrnCode='". mysql_result($resBr,$b,"brnCode")."' $conDiv)
						order by deptDesc";
		
		$resDivision = mysql_query($qryDivision);
		$numDivision = mysql_num_rows($resDivision);
		$grandTotalHeadCount_Div = $grandTotalBasic_Div = $grandTotaltardUt_Div = $grandTotalAbsent_Div = $grandTotalOtNd_Div = $grandTotalOthIncome_Div = $grandTotal_Div = $grandTotalAllow_Div = $grandTotalOthIncomeOt_Div = 0;
		for($d=0;$d<$numDivision;$d++){
			$worksheet->write($lastRow+1, 1, mysql_result($resDivision,$d,"deptShortDesc"), $detailBorder);

			if($empDiv!=0){
				$conDept = "and empDivCode='".$empDiv."'";
			}			
			if($empDept!=0){
				$conDept .= " and empDepCode='".$empDept."'";
			}
			$qryDept = "Select deptCode,deptShortDesc from tblDepartment
							where compCode='".$_SESSION["company_code"]."' and deptLevel='2'
							and deptStat='A'
							and deptCode in 
							(Select empDepCode from tblPayrollSummaryHist where compCode='".$_SESSION["company_code"]."'
							and payGrp='".$_SESSION["pay_group"]."'  
							and pdYear = '".date("Y", strtotime($fromDate))."' 
							and pdNumber in (".$qrypdNum.") 
							and empBrnCode='".mysql_result($resBr,$b,"brnCode")."' $conDept)
							and divCode='".mysql_result($resDivision,$d,"divCode")."' 
							order by deptDesc";
			$resDepartment = mysql_query($qryDept);
			$numDepartment = mysql_num_rows($resDepartment);
			$grandTotalHeadCount_Dept = $grandTotalBasic_Dept =$grandTotalAbsent_Dept = $grandTotaltardUt_Dept = $grandTotalOtNd_Dept = $grandTotalOthIncome_Dept = $grandTotal_Dept = $grandTotalAllow_Dept = $grandTotalOthIncomeOt_Dept = 0;
			for($m=0;$m<$numDepartment;$m++){
				$worksheet->write($lastRow+2, 2, mysql_result($resDepartment,$m,"deptShortDesc"), $detailBorder);
					$qrytblHCount = "Select count(empNo) as cntEmp from tblPayrollSummaryHist as tblPaySum 
									where compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."'  
									and pdYear = '".date("Y", strtotime($fromDate))."'
									and pdNumber in (".$qrypdNum.") and empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
									and empDivCode='".mysql_result($resDivision,$d,"divCode")."' 
									and empDepCode='".mysql_result($resDepartment,$m,"deptCode")."'";
					$resHCount = mysql_query($qrytblHCount);
					$numHCount = mysql_num_rows($resHCount);
					for($h=0;$h<$numHCount;$h++){
					
					$qrytblEarnB = "Select sum(trnAmountE) as sumtrnAmountE
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
					and tblPaySum.empDivCode='".mysql_result($resDivision,$d,"divCode")."' and
					tblPaySum.empDepCode='".mysql_result($resDepartment,$m,"deptCode")."' and tblEarn.trnCode='".EARNINGS_BASIC."'";
					$bA=mysql_query($qrytblEarnB);
					$arrgetBasic=mysql_fetch_assoc($bA);
			
/*							$arrgetBasic = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"), mysql_result($resDepartment,$m,"deptCode"), EARNINGS_BASIC, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
*/						
					$qrytblEarnA = "Select sum(trnAmountE) as sumtrnAmountE
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
					and tblPaySum.empDivCode='".mysql_result($resDivision,$d,"divCode")."' and
					tblPaySum.empDepCode='".mysql_result($resDepartment,$m,"deptCode")."' and tblEarn.trnCode='".EARNINGS_ABS."'";
					$A=mysql_query($qrytblEarnA);
					$arrgetAbsent=mysql_fetch_assoc($A);	
/*							$arrgetAbsent = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),mysql_result($resDepartment,$m,"deptCode"), EARNINGS_ABS, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
*/
					$qrytblEarnT = "Select sum(trnAmountE) as sumtrnAmountE
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
					and tblPaySum.empDivCode='".mysql_result($resDivision,$d,"divCode")."' and
					tblPaySum.empDepCode='".mysql_result($resDepartment,$m,"deptCode")."' and tblEarn.trnCode='".EARNINGS_TARD."'";
					$T=mysql_query($qrytblEarnT);
					$arrgetTard=mysql_fetch_assoc($T);

/*							$arrgetTard = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"), mysql_result($resDepartment,$m,"deptCode"), EARNINGS_TARD, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
*/		
					$qrytblEarnUT = "Select sum(trnAmountE) as sumtrnAmountE
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
					and tblPaySum.empDivCode='".mysql_result($resDivision,$d,"divCode")."' and
					tblPaySum.empDepCode='".mysql_result($resDepartment,$m,"deptCode")."' and tblEarn.trnCode='".EARNINGS_UT."'";
					$UT=mysql_query($qrytblEarnUT);
					$arrgetUt=mysql_fetch_assoc($UT);
					
							$sumTardandUt = $arrgetTard["sumtrnAmountE"] + $arrgetUt["sumtrnAmountE"];
/*					$arrgetUt = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"), mysql_result($resDepartment,$m,"deptCode"), EARNINGS_UT, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
*/
					$qrytblEarnOT = "Select sum(trnAmountE) as sumtrnAmountE
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
					and tblPaySum.empDivCode='".mysql_result($resDivision,$d,"divCode")."' and
					tblPaySum.empDepCode='".mysql_result($resDepartment,$m,"deptCode")."'  
					and tblEarn.trnCode in (Select trnCode from tblPayTransType where compCode='".$_SESSION["company_code"]."' 
					and trnRecode='".EARNINGS_OT."' and trnStat='A')";
					$OT=mysql_query($qrytblEarnOT);
					$arrgetOt=mysql_fetch_assoc($OT);
							
/*							$arrgetOt = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), EARNINGS_OT, 'sum(trnAmountE) as sumtrnAmountE', 1, $fromDate, $qrypdNum);
*/

					$qrytblEarnND = "Select sum(trnAmountE) as sumtrnAmountE
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
					and tblPaySum.empDivCode='".mysql_result($resDivision,$d,"divCode")."' and
					tblPaySum.empDepCode='".mysql_result($resDepartment,$m,"deptCode")."'
					and tblEarn.trnCode in 	(Select trnCode from tblPayTransType where compCode='".$_SESSION["company_code"]."' 
					and trnRecode='".EARNINGS_ND."' and trnStat='A')";
					$ND=mysql_query($qrytblEarnND);
					$arrgetNd=mysql_fetch_assoc($ND);

/*							$arrgetNd = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), EARNINGS_ND, 'sum(trnAmountE) as sumtrnAmountE', 1, $fromDate, $qrypdNum);
*/							
							$sumOtandNd = $arrgetOt["sumtrnAmountE"] + $arrgetNd["sumtrnAmountE"];
			
					$qrytblEarnIN = "Select sum(trnAmountE) as sumtrnAmountE
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
					and tblPaySum.empDivCode='".mysql_result($resDivision,$d,"divCode")."' and
					tblPaySum.empDepCode='".mysql_result($resDepartment,$m,"deptCode")."' and tblEarn.trnCode='".ADJ_BASIC."'";
					$IN=mysql_query($qrytblEarnIN);
					$arrgetOthIncome=mysql_fetch_assoc($IN);
							
/*							$arrgetOthIncome = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), ADJ_BASIC, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
*/

					$qrytblEarnOTIN = "Select sum(trnAmountE) as sumtrnAmountE
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
					and tblPaySum.empDivCode='".mysql_result($resDivision,$d,"divCode")."' and
					tblPaySum.empDepCode='".mysql_result($resDepartment,$m,"deptCode")."' and tblEarn.trnCode='".ADJ_OT."'";
					$OTIN=mysql_query($qrytblEarnOTIN);
					$arrgetOthIncome_OT=mysql_fetch_assoc($OTIN);

/*							$arrgetOthIncome_OT = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), ADJ_OT, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
*/		
					$qrytblEarnALL = "Select sum(trnAmountE) as sumtrnAmountE
					from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
					where 
					tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEarn.pdYear='".date("Y", strtotime($fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($fromDate))."'
					and tblEarn.pdNumber in (".$qrypdNum.") and tblPaySum.pdNumber in (".$qrypdNum.") and 
					tblEarn.pdNumber = tblPaySum.pdNumber and tblEarn.pdYear = tblPaySum.pdYear and tblEarn.empNo = tblPaySum.empNo and
					tblPaySum.payGrp='".$_SESSION["pay_group"]."' and tblPaySum.empBrnCode='".mysql_result($resBr,$b,"brnCode")."' 
					and tblPaySum.empDivCode='".mysql_result($resDivision,$d,"divCode")."' and
					tblPaySum.empDepCode='".mysql_result($resDepartment,$m,"deptCode")."' and tblEarn.trnCode in (Select trnCode 
					from tblPayTransType where compCode='".$_SESSION['company_code']."' and trnCat='E' and trnStat='A'  
					and trnCode  in (Select trnCode from tblAllowType where compCode='".$_SESSION["company_code"]."' and allowTypeStat='A'))";
					$ALL=mysql_query($qrytblEarnALL);
					$arrgetAllow=mysql_fetch_assoc($ALL);
					
/*							$arrgetAllow = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), '', 'sum(trnAmountE) as sumtrnAmountE', 3, $fromDate, $qrypdNum);
*/
						
/*							$arrgetBasic = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"), mysql_result($resDepartment,$m,"deptCode"), EARNINGS_BASIC, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
							
							$arrgetAbsent = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),mysql_result($resDepartment,$m,"deptCode"), EARNINGS_ABS, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
							$arrgetTard = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"), mysql_result($resDepartment,$m,"deptCode"), EARNINGS_TARD, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
							$arrgetUt = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"), mysql_result($resDepartment,$m,"deptCode"), EARNINGS_UT, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
							$sumTardandUt = $arrgetTard["sumtrnAmountE"] + $arrgetUt["sumtrnAmountE"];
							$arrgetOt = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), EARNINGS_OT, 'sum(trnAmountE) as sumtrnAmountE', 1, $fromDate, $qrypdNum);
							$arrgetNd = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), EARNINGS_ND, 'sum(trnAmountE) as sumtrnAmountE', 1, $fromDate, $qrypdNum);
							$sumOtandNd = $arrgetOt["sumtrnAmountE"] + $arrgetNd["sumtrnAmountE"];
							
							$arrgetOthIncome = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), ADJ_BASIC, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
							$arrgetOthIncome_OT = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), ADJ_OT, 'sum(trnAmountE) as sumtrnAmountE', '', $fromDate, $qrypdNum);
							
							$arrgetAllow = getEarningsDetail(mysql_result($resBr,$b,"brnCode"), mysql_result($resDivision,$d,"divCode"),  mysql_result($resDepartment,$m,"deptCode"), '', 'sum(trnAmountE) as sumtrnAmountE', 3, $fromDate, $qrypdNum);
*/							
							$total = $arrgetBasic["sumtrnAmountE"] + $arrgetAbsent["sumtrnAmountE"] + $sumTardandUt + $sumOtandNd + $arrgetOthIncome["sumtrnAmountE"] +  $arrgetOthIncome_OT["sumtrnAmountE"] +  $arrgetAllow["sumtrnAmountE"];
							
							$worksheet->write($lastRow+2, 3, mysql_result($resHCount,$h,"cntEmp"), $detailBorder);	
							$worksheet->write($lastRow+2, 4, number_format($arrgetBasic['sumtrnAmountE'],2) , $detailBorder);
							$worksheet->write($lastRow+2, 5, number_format($arrgetAbsent['sumtrnAmountE'],2) , $detailBorder);
							$worksheet->write($lastRow+2, 6, number_format($sumTardandUt,2), $detailBorder);
							$worksheet->write($lastRow+2, 7, number_format($sumOtandNd,2), $detailBorder);
							$worksheet->write($lastRow+2, 8, number_format($arrgetOthIncome['sumtrnAmountE'],2) , $detailBorder);
							$worksheet->write($lastRow+2, 9, number_format($arrgetOthIncome_OT['sumtrnAmountE'],2) , $detailBorder);
							$worksheet->write($lastRow+2, 10, number_format($arrgetAllow['sumtrnAmountE'],2) , $detailBorder);
							$worksheet->write($lastRow+2, 11, number_format($total,2) , $detailBorder);
							$lastRow++;	
						
							$grandTotalHeadCount_Dept+=mysql_result($resHCount,$h,"cntEmp");
							$grandTotalBasic_Dept+=$arrgetBasic["sumtrnAmountE"];
							$grandTotalAbsent_Dept+=$arrgetAbsent["sumtrnAmountE"];
							$grandTotaltardUt_Dept+=$sumTardandUt;
							$grandTotalOtNd_Dept+=$sumOtandNd;
							$grandTotalOthIncome_Dept+=$arrgetOthIncome["sumtrnAmountE"];
							$grandTotalOthIncomeOt_Dept+=$arrgetOthIncome_OT["sumtrnAmountE"];
							$grandTotalAllow_Dept+=$arrgetAllow["sumtrnAmountE"];
							$grandTotal_Dept+=$total; 						
	
							$grandTotalHeadCount_Div+=mysql_result($resHCount,$h,"cntEmp");
							$grandTotalBasic_Div+=$arrgetBasic["sumtrnAmountE"];
							$grandTotalAbsent_Div+=$arrgetAbsent["sumtrnAmountE"];
							$grandTotaltardUt_Div+=$sumTardandUt;
							$grandTotalOtNd_Div+=$sumOtandNd;
							$grandTotalOthIncome_Div+=$arrgetOthIncome["sumtrnAmountE"];
							$grandTotalOthIncomeOt_Div+=$arrgetOthIncome_OT["sumtrnAmountE"];
							$grandTotalAllow_Div+=$arrgetAllow["sumtrnAmountE"];
							$grandTotal_Div+=$total; 
	
							$grandTotalHeadCount_Branch+=mysql_result($resHCount,$h,"cntEmp");
							$grandTotalBasic_Branch+=$arrgetBasic["sumtrnAmountE"];
							$grandTotalAbsent_Branch+=$arrgetAbsent["sumtrnAmountE"];
							$grandTotaltardUt_Branch+=$sumTardandUt;
							$grandTotalOtNd_Branch+=$sumOtandNd;
							$grandTotalOthIncome_Branch+=$arrgetOthIncome["sumtrnAmountE"];
							$grandTotalOthIncomeOt_Branch+=$arrgetOthIncome_OT["sumtrnAmountE"];
							$grandTotalAllow_Branch+=$arrgetAllow["sumtrnAmountE"];
							$grandTotal_Branch+=$total; 
	
							$grandTotalHeadCount+=mysql_result($resHCount,$h,"cntEmp");
							$grandTotalBasic+=$arrgetBasic["sumtrnAmountE"];
							$grandTotalAbsent+=$arrgetAbsent["sumtrnAmountE"];
							$grandTotaltardUt+=$sumTardandUt;
							$grandTotalOtNd+=$sumOtandNd;
							$grandTotalOthIncome+=$arrgetOthIncome["sumtrnAmountE"];
							$grandTotalOthIncomeOt+=$arrgetOthIncome_OT["sumtrnAmountE"];
							$grandTotalAllow+=$arrgetAllow["sumtrnAmountE"];
							$grandTotal+=$total; 
						
							
					}
			}
						$worksheet->write($lastRow+2, 2, "DEPARTMENT TOTALS", $labelFormat);				
						$worksheet->write($lastRow+2, 3, $grandTotalHeadCount_Dept, $labelFormat);						
						$worksheet->write($lastRow+2, 4, number_format($grandTotalBasic_Dept,2) , $labelFormat);
						$worksheet->write($lastRow+2, 5, number_format($grandTotalAbsent_Dept,2) , $labelFormat);
						$worksheet->write($lastRow+2, 6, number_format($grandTotaltardUt_Dept,2), $labelFormat);
						$worksheet->write($lastRow+2, 7, number_format($grandTotalOtNd_Dept,2), $labelFormat);
						$worksheet->write($lastRow+2, 8, number_format($grandTotalOthIncome_Dept,2) , $labelFormat);
						$worksheet->write($lastRow+2, 9, number_format($grandTotalOthIncomeOt_Dept,2) , $labelFormat);
						$worksheet->write($lastRow+2, 10, number_format($grandTotalAllow_Dept,2) , $labelFormat);
						$worksheet->write($lastRow+2, 11, number_format($grandTotal_Dept,2) , $labelFormat);
						$lastRow+=2;	
		}
						$worksheet->write($lastRow+2, 2, "DIVISION TOTALS", $labelFormat);
						$worksheet->write($lastRow+2, 3, $grandTotalHeadCount_Div, $labelFormat);						
						$worksheet->write($lastRow+2, 4, number_format($grandTotalBasic_Div,2) , $labelFormat);
						$worksheet->write($lastRow+2, 5, number_format($grandTotalAbsent_Div,2) , $labelFormat);
						$worksheet->write($lastRow+2, 6, number_format($grandTotaltardUt_Div,2), $labelFormat);
						$worksheet->write($lastRow+2, 7, number_format($grandTotalOtNd_Div,2), $labelFormat);
						$worksheet->write($lastRow+2, 8, number_format($grandTotalOthIncome_Div,2) , $labelFormat);
						$worksheet->write($lastRow+2, 9, number_format($grandTotalOthIncomeOt_Div,2) , $labelFormat);
						$worksheet->write($lastRow+2, 10, number_format($grandTotalAllow_Div,2) , $labelFormat);
						$worksheet->write($lastRow+2, 11, number_format($grandTotal_Div,2) , $labelFormat);
						$lastRow+=4;
	}
						$worksheet->write($lastRow+2, 2, "BRANCH TOTALS", $labelFormat);				
						$worksheet->write($lastRow+2, 3, $grandTotalHeadCount_Branch, $labelFormat);						
						$worksheet->write($lastRow+2, 4, number_format($grandTotalBasic_Branch,2) , $labelFormat);
						$worksheet->write($lastRow+2, 5, number_format($grandTotalAbsent_Branch,2) , $labelFormat);
						$worksheet->write($lastRow+2, 6, number_format($grandTotaltardUt_Branch,2), $labelFormat);
						$worksheet->write($lastRow+2, 7, number_format($grandTotalOtNd_Branch,2), $labelFormat);
						$worksheet->write($lastRow+2, 8, number_format($grandTotalOthIncome_Branch,2) , $labelFormat);
						$worksheet->write($lastRow+2, 9, number_format($grandTotalOthIncomeOt_Branch,2) , $labelFormat);
						$worksheet->write($lastRow+2, 10, number_format($grandTotalAllow_Branch,2) , $labelFormat);
						$worksheet->write($lastRow+2, 11, number_format($grandTotal_Branch,2) , $labelFormat);
						$lastRow+=3;	

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