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
$headerBorder    = $workbook->addFormat(array('border' => 1.5,'Size'=>10,'Color'=>'red','Align'=>'Center'));
$detailBorder   = $workbook->addFormat(array('border' => 1,'Align'=>'Left'));
$detailBorder2   = $workbook->addFormat(array('border' => 1,'Align'=>'Center'));
$branchHeader = $workbook->addFormat(array('Size'=>10,
									'bold'=>1,
									'Align'=>'Left',
									'Color'=>'red'
									));
$detailrBorderAlignRight   = $workbook->addFormat(array('Align' => 'right'));
$filename = "managementreport".$todaynewdate.".xls";
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('Management Report');
$worksheet->setLandscape();
$worksheet->freezePanes(array(6, 0));
//$worksheet->setColumn(0,0,50);
//$worksheet->setColumn(2,9,20);
## SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL SET UP EXCEL

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$gmt = time() + (8 * 60 * 60);
$today = date("m/d/Y", $gmt);
$worksheet->write(0, 0, $compName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(0, $j, "",$headerFormat); }
$worksheet->write(1, 0, "MANAGEMENT REPORT",$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(1, $j, "",$headerFormat); }
//$worksheet->write(2, 0, $brnName,$headerFormat); for ($j=1; $j<=9; $j++) { $worksheet->write(2, $j, "",$headerFormat); }
$worksheet->write(3, 0, "RUN DATE: ".$today); 
$worksheet->write(4, 0, "REPORT ID: LSTMANAGEMENT"); for ($j=1; $j<=3; $j++) { $worksheet->write(0, $j, "",""); } 
$worksheet->write(5, 0, "EMPLOYEE No.",$headerBorder);
$worksheet->write(5, 1, "EMPLOYEE NAME",$headerBorder);
$worksheet->write(5, 2, "POSITION",$headerBorder);
$worksheet->write(5, 3, "LEVEL",$headerBorder);
$worksheet->write(5, 4, "DEPARTMENT",$headerBorder);
$worksheet->write(5, 5, "SECTION",$headerBorder);
$worksheet->write(5, 6, "GENDER",$headerBorder);
$worksheet->write(5, 7, "TAX STATUS",$headerBorder);
$worksheet->write(5, 8, "BIRTHDAY",$headerBorder);
$worksheet->write(5, 9, "ADDRESS",$headerBorder);
$worksheet->write(5, 10, "BLOOD TYPE",$headerBorder);
$worksheet->write(5, 11, "COMPANY",$headerBorder);
$worksheet->write(5, 12, "BRANCH",$headerBorder);
$worksheet->write(5, 13, "DATE HIRED",$headerBorder);
$worksheet->write(5, 14, "DATE REGULARIZED",$headerBorder);
$worksheet->write(5, 15, "EMPLOYMENT STATUS",$headerBorder);
$worksheet->write(5, 16, "YEARS OF SERVICE",$headerBorder);
$worksheet->write(5, 17, "AGE",$headerBorder);
$worksheet->write(5, 18, "RANK",$headerBorder);
$worksheet->write(5, 19, "TIN",$headerBorder);
$worksheet->write(5, 20, "SSS No",$headerBorder);
$worksheet->write(5, 21, "Pag Ibig No",$headerBorder);
$worksheet->write(5, 22, "Philhealth No",$headerBorder);

//$worksheet->write(5, 12, "EMPLOYMENT STATUS",$headerBorder);

## HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER HEADER
$lastRow = 6;
	for($b=0;$b<$numBranches;$b++){
			$sqlRD = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, 
			tblPosition.posDesc, tblRankType.rankDesc, tblDepartment.deptDesc, empLevel ,tblEmpMast.empMrate, 
			v_emp_allowance_m.allowAmt, (v_emp_allowance_d.allowAmt * 26) as daily, tblEmpMast.empSex, tblEmpMast.empTeu,
			tblEmpMast.empBday, CASE tblEmpMast.empBloodType when '0' then ' ' else tblEmpMast.empBloodType end as empBloodType,
			tblEmpMast.compCode, tblEmpMast.empBrnCode,tblEmpMast.empTin,tblEmpMast.empsssNo,tblEmpMast.empPhicNo,tblEmpMast.emppagibig, tblEmpMast.empAddr1, tblMunicipalityRef.municipalityDesc,
			tblEmpMast.empMunicipalityCd, tblProvinceRef.provinceDesc, tblEmpMast.empProvinceCd, tblTeu.teuDesc,
			date_format(tblEmpMast.empbday, '%m/%d/%Y') as d, tblCompany.compName, tblBranch.brnDesc, 
			date_format(tblEmpMast.dateHired, '%m/%d/%Y') as datehired, 
			date_format(tblEmpMast.dateReg, '%m/%d/%Y') as dateregularized, 
			CASE tblEmpMast.employmentTag when 'RG' then 'Regular' when 'PR' then 'Probationary' when 'CN' then 'Contractual' end AS employmentTag,
			tblEmpMast.empDiv, tblEmpMast.empDepCode, tblEmpMast.empSecCode, 
			floor(datediff(CURDATE(),tblEmpMast.dateHired)/365.25) as empTenure, 
			floor(datediff(CURDATE(),tblEmpMast.empbday)/365.25) as empAge  
			FROM    tblEmpMast 
			LEFT JOIN  v_emp_allowance_m ON tblEmpMast.empNo = v_emp_allowance_m.empNo
			LEFT JOIN  tblRankType ON tblEmpMast.empRank = tblRankType.rankCode
			LEFT JOIN  v_emp_allowance_d ON tblEmpMast.empNo = v_emp_allowance_d.empNo
			LEFT JOIN tblPosition on tblEmpMast.empPosId=tblPosition.posCode
			LEFT JOIN tblDepartment on tblEmpMast.empDiv=tblDepartment.divCode and tblEmpMast.empdepCode=tblDepartment.deptCode
			LEFT JOIN tblMunicipalityRef on tblEmpMast.empMunicipalityCd=tblMunicipalityRef.municipalityCd
			LEFT JOIN tblProvinceRef ON tblEmpMast.empProvinceCd=tblProvinceRef.provinceCd
			LEFT JOIN tblCompany on tblCompany.compCode=tblEmpMast.compCode
			LEFT JOIN tblBranch on tblEmpMast.empBrnCode=tblBranch.brnCode
			LEFT JOIN tblTeu on tblEmpMast.empTeu=tblTeu.teuCode
			WHERE tblEmpMast.employmentTag IN ('RG', 'PR', 'CN') 
			and tblEmpMast.empStat ='RG'
			and tblDepartment.deptLevel='2'
			and tblEmpMast.empBrnCode='".mysql_result($resBr,$b,"brnCode")."'
			order by tblDepartment.deptDesc, tblEmpMast.empLastName"; 

				$resGetDealsList = mysql_query($sqlRD);
				$num = mysql_num_rows($resGetDealsList);
				$sqlBranches = "SELECT brnDesc FROM tblBranch WHERE compCode = '{$_SESSION['company_code']}' and brnCode = '".mysql_result($resBr,$b,"brnCode")."'";
				$resBranches=mysql_query($sqlBranches);
				for ($i=0;$i<$num;$i++){ 
					$sect = $inqTSObj->getSectDescArt(mysql_result($resGetDealsList,$i,"compCode"),mysql_result($resGetDealsList,$i,"empDiv"),mysql_result($resGetDealsList,$i,"empDepCode"),mysql_result($resGetDealsList,$i,"empSecCode"));
					$worksheet->write($lastRow, 0, " ".mysql_result($resGetDealsList,$i,"empNo"),$detailBorder);
					$worksheet->write($lastRow, 1, mysql_result($resGetDealsList,$i,"empLastName").", ".mysql_result($resGetDealsList,$i,"empFirstName")." ".mysql_result($resGetDealsList,$i,"empMidName"),$detailBorder);
					$worksheet->write($lastRow, 2, mysql_result($resGetDealsList,$i,"posDesc"),$detailBorder);
					$worksheet->write($lastRow, 3, mysql_result($resGetDealsList,$i,"empLevel"),$detailBorder2);
					$worksheet->write($lastRow, 4, mysql_result($resGetDealsList,$i,"deptDesc"),$detailBorder);
					$worksheet->write($lastRow, 5, $sect['deptDesc'],$detailBorder);
					$worksheet->write($lastRow, 6, mysql_result($resGetDealsList,$i,"empSex"),$detailBorder2);
					$worksheet->write($lastRow, 7, mysql_result($resGetDealsList,$i,"teuDesc"),$detailBorder);
					$worksheet->write($lastRow, 8, mysql_result($resGetDealsList,$i,"d"),$detailBorder);
					$worksheet->write($lastRow, 9, mysql_result($resGetDealsList,$i,"empAddr1")." ".mysql_result($resGetDealsList,$i,"municipalityDesc")." ".mysql_result($resGetDealsList,$i,"provinceDesc"),$detailBorder);
					$worksheet->write($lastRow, 10, mysql_result($resGetDealsList,$i,"empBloodType"),$detailBorder2);
					$worksheet->write($lastRow, 11, mysql_result($resGetDealsList,$i,"compName"),$detailBorder);
					$worksheet->write($lastRow, 12, mysql_result($resGetDealsList,$i,"brnDesc"),$detailBorder);
					$worksheet->write($lastRow, 13, mysql_result($resGetDealsList,$i,"datehired"),$detailBorder);
					$worksheet->write($lastRow, 14, mysql_result($resGetDealsList,$i,"dateregularized"),$detailBorder);
					$worksheet->write($lastRow, 15, mysql_result($resGetDealsList,$i,"employmentTag"),$detailBorder);
					$worksheet->write($lastRow, 16, " ".mysql_result($resGetDealsList,$i,"empTenure"),$detailBorder2);
					$worksheet->write($lastRow, 17, " ".mysql_result($resGetDealsList,$i,"empAge"),$detailBorder2);
					$worksheet->write($lastRow, 18, mysql_result($resGetDealsList,$i,"rankDesc"),$detailBorder);
					$worksheet->write($lastRow, 19," ". mysql_result($resGetDealsList,$i,"empTin"),$detailBorder);
					$worksheet->write($lastRow, 20," ". mysql_result($resGetDealsList,$i,"empsssNo"),$detailBorder);
					$worksheet->write($lastRow, 21, " ".mysql_result($resGetDealsList,$i,"emppagibig"),$detailBorder);
					$worksheet->write($lastRow, 22," ". mysql_result($resGetDealsList,$i,"empPhicNo"),$detailBorder);	
								
					$lastRow++;
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
$worksheet->write($lastRow, 1, $prntdBy,"");
$workbook->close();
?>