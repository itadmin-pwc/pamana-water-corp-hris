<?
################### INCLUDE FILE #################
	session_start();
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
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200 / 100 / 66
############################ LETTER LANDSCAPE TOTAL WIDTH = 265 / 132 / 88
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310 / 155 / 103
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$pdf = new FPDF('L', 'mm', 'LETTER');
$pdf->SetFont('Courier', '', '8');
$TOTAL_WIDTH   			= 265;
$TOTAL_WIDTH_2 			= 132;
$TOTAL_WIDTH_3 			= 88;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 265;
$pdf->TOTAL_WIDTH_2     = 132;
$pdf->TOTAL_WIDTH_3     = 88;
$pdf->SPACES	       	= 5;
$page                   = 1;
############################ Q U E R Y ##################################

$confaccess=$_SESSION['Confiaccess'];
if($confaccess == 'N'){
	$confi = "and tblEmpMast.empPayCat ='3'";
}elseif ($confaccess == 'Y') {
	$confi = "and tblEmpMast.empPayCat ='2'";
}
else $confi = '';


	   $sqlRD = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, 
	   				tblDepartment.deptDesc, tblPosition.posDesc, tblEmpMast.empEndDate AS empEndDate, 
				    CASE emppaytype WHEN 'M' THEN empmrate WHEN 'D' THEN empdrate END AS salary_ko, 
					CASE emppaytype WHEN 'M' THEN 'Monthly' WHEN 'D' THEN 'Daily' END AS rateMode, tblEmpMast.empStat,
					CASE tblEmpMast.employmentTag when  'RG' then 'REGULAR' when 'CN' then 'CONTRACTUAL' 
					when 'PR' then 'PROBATIONARY' end AS emp_stat, tblEmpMast.dateHired AS dateHired,
					empPayCat,tblEmpMast.dateReg AS dateReg 
				  FROM tblEmpMast 
				  INNER JOIN  tblDepartment ON tblEmpMast.empDiv = tblDepartment.divCode 
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
	$sqlBr = "SELECT brnShortDesc FROM tblBranch WHERE brnCode = $brnCode AND compCode = '{$_SESSION['company_code']}'";
	$resBr = mysql_query($sqlBr);
	$numBr = mysql_num_rows($resBr);
	if ($numBr>0) {
		$brnName = mysql_result($resBr,0,"brnShortDesc");
	} else {
		$brnName = "";
	}

HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,$brnName);
$ctr = 0;
for ($i=0;$i<$num;$i++){ 
	
	if ($tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
		if ($tmpDept!="") {
			$pdf->Cell(30,5,"Total: $ctrDept",0,1);
			$pdf->Cell(30,5,"",0,1);
		}
		$ctrDept = 0;	

		if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,$brnName);
		$pdf->SetFont('Courier', 'B', '9');
		$pdf->Cell(30,5,mysql_result($resGetDealsList,$i,"deptDesc")." DEPARTMENT",0,1);
		$pdf->SetFont('Courier', '', '9');
	}
	$ctrDept++;
	$ctr++;
	if ($tmpEmpStat!=mysql_result($resGetDealsList,$i,"emp_stat") || $tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
		if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,$brnName);
		$pdf->SetFont('Courier', 'B', '9');
		$pdf->Cell(5,5,"",0,0);
		$pdf->Cell(30,5,mysql_result($resGetDealsList,$i,"emp_stat"),0,1);
		$pdf->SetFont('Courier', '', '9');
	}
	$pdf->Cell(10,5,"",0,0);
	$pdf->Cell(60,5,$ctrDept.". ".mysql_result($resGetDealsList,$i,"empLastName").", ".substr(mysql_result($resGetDealsList,$i,"empFirstName"),0,8)." ".substr(mysql_result($resGetDealsList,$i,"empMidName"),0,1).".",0,0);
	$pdf->Cell(55,5,substr(mysql_result($resGetDealsList,$i,"posDesc"),0,24),0,0);
	$pdf->Cell(30,5,date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"dateHired"))),0,0);
	$pdf->Cell(25,5,date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"empEndDate"))),0,0);
	$pdf->Cell(25,5,date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"dateReg"))),0,0);	
	if (in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat'])))  {
		$pdf->Cell(30,5,number_format(mysql_result($resGetDealsList,$i,"salary_ko"),2),0,0); 
		$pdf->Cell(30,5,mysql_result($resGetDealsList,$i,"rateMode"),0,0);
	} else {
		$pdf->Cell(40,5,"--",0,0);
	}	
	$sqlAllow = "SELECT CONCAT(tblAllowType.allowDesc,' (', tblAllowance.allowTag, ')=', tblAllowance.allowAmt) AS allow  FROM tblAllowance INNER JOIN tblAllowType ON tblAllowance.allowCode = tblAllowType.allowCode AND tblAllowance.compCode = tblAllowType.compCode WHERE (tblAllowance.empNo = ".mysql_result($resGetDealsList,$i,"empNo").")";
	$resAllow = mysql_query($sqlAllow);
	$numAllow = mysql_num_rows($resAllow);
	$allow = "";
	if ($numAllow>0 && in_array(mysql_result($resGetDealsList,$i,"empPayCat"),explode(',',$_SESSION['user_payCat']))) {
		for ($j=0;$j<$numAllow;$j++) {
			if ($j+1==$numAllow) {
				$allow = mysql_result($resAllow,$j,"allow");
			} else {
				$allow = mysql_result($resAllow,$j,"allow").", ";
			}
			if ($j<=0) {
				
				$pdf->Cell(225,5,"",0,1);
				if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,$brnName);
				$pdf->Cell(225,5,"",0,0);
				$pdf->Cell(36,5,$allow,0,1,'R');
			} else {
				if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,$brnName);
				$pdf->Cell(225,5,"",0,0);
				$pdf->Cell(36,5,$allow,0,1,'R');
			}
		}
	} else {
		$allow = "";
		if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,$brnName);
		$pdf->Cell(225,5,"",0,1);
	}
	$tmpDept = mysql_result($resGetDealsList,$i,"deptDesc");
	$tmpEmpStat = mysql_result($resGetDealsList,$i,"emp_stat");
	if ($ctr == $num) {
		$pdf->Cell(30,5,"Total: $ctrDept",0,1);
		$pdf->Cell(30,5,"Grand Total: $num",0,1);
	}

	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,$brnName);
}
$pdf->Cell(30,5,"",0,1);
$userId= $inqTSObj->getSeesionVars();
$pdf->Cell(30,5,"",0,1);
$pdf->Cell($TOTAL_WIDTH,5,"* * * End of report. Nothing follows. * * *",0,1,'C');
$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
$pdf->Cell(30,5,$prntdBy,0,1);
$pdf->Output('Manpower Complement.pdf','D');
function HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page,$brnName) {
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	$pdf->AddPage();
	$pdf->Text(11,10,"RUN DATE: ".$newdate);
	$pdf->Text(11,14,"REPORT ID: MANCOM");
	$pdf->Text(120,10,$compName);
	$pdf->Text(120,14,"MANPOWER COMPLEMENT");
	$pdf->Text(220,14, "BRANCH: ".$brnName);
	$pdf->Cell(1,10,"",0,1,'R');
	$pdf->SetFont('Courier', 'B', '9');
	$pdf->Cell(10,5,"",0,0);
	$pdf->Cell(60,5,"EMPLOYEE NAME",0,0);
	$pdf->Cell(55,5,"POSITION",0,0);
	$pdf->Cell(30,5,"DATE HIRED",0,0);
	$pdf->Cell(25,5,"EOC",0,0);
	$pdf->Cell(25,5,"DATE REG.",0,0);
	$pdf->Cell(30,5,"SALARY",0,0);
	$pdf->Cell(30,5,"RATE MODE",0,0);
	$pdf->Cell(36,5,"ALLOWANCE",0,0,'R');
	$pdf->SetFont('Courier', '', '9');
	$pdf->Cell(1,10,"",0,1,'R');
}
?>