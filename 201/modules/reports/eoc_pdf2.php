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
	$frmdate				= date('Y-m-d',strtotime($_POST['txtfrDate']));
	$todate					= date('Y-m-d',strtotime($_POST['txttoDate']));	
	$divcode 				= $_POST['empDiv'];	
	$deptcode 				= $_POST['empDept'];	
	$compName 		= $inqTSObj->getCompanyName($compCode);
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200 / 100 / 66
############################ LETTER LANDSCAPE TOTAL WIDTH = 265 / 132 / 88
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310 / 155 / 103
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$pdf = new FPDF('L', 'mm', 'LETTER');
$pdf->SetFont('Courier', '', '10');
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
				CASE emppaytype when 'M' THEN concat(empmrate, '/mo.')  WHEN 'D' THEN concat(empdrate, '/day') END as salary_ko,
				tblEmpMast.empStat, 
				case tblEmpMast.employmentTag when 'RG' then 'REGULAR' when 'CN' then 'CONTRACTUAL' when 'PR' then 'PROBATIONARY' end as emp_stat, 
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
	
	$brnName = "";
	$tmpDept = "";
HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,'');

for ($i=0;$i<$num;$i++){ 
	if($brnName!=mysql_result($resGetDealsList,$i,"brnDesc")){
		if ($brnName!="") {
			$pdf->Cell(30,5,"",0,1);
		}
		if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,'');
		$pdf->SetFont('Courier', 'B', '11');
		$pdf->Cell(5,5,"",0,0);
		$pdf->Cell(70,5,mysql_result($resGetDealsList,$i,"brnDesc"),0,1);
		$pdf->SetFont('Courier', '', '10');
	}
	if ($tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
		if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,'');
		$pdf->SetFont('Courier', 'B', '11');
		$pdf->Cell(15,5,"",0,0);
		$pdf->Cell(70,5,mysql_result($resGetDealsList,$i,"deptDesc")." DEPARTMENT",0,1);
		$pdf->SetFont('Courier', '', '10');
	}
	
	if ($tmpEmpStat!=mysql_result($resGetDealsList,$i,"emp_stat") || $tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
		if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,'');
		$pdf->SetFont('Courier', 'B', '10');
		$pdf->Cell(20,5,"",0,0);
		$pdf->Cell(70,5,mysql_result($resGetDealsList,$i,"emp_stat"),0,1);
		$pdf->SetFont('Courier', '', '10');
	}
	$pdf->Cell(25,5,"",0,0);
	$pdf->Cell(60,5,mysql_result($resGetDealsList,$i,"empLastName").", ".mysql_result($resGetDealsList,$i,"empFirstName")." ".substr(mysql_result($resGetDealsList,$i,"empMidName"),0,1).".",0,0,'L');
	$pdf->Cell(30,5,date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"dateHired"))),0,0,'C');
	$pdf->Cell(30,5,date('Y-m-d',strtotime(mysql_result($resGetDealsList,$i,"empEndDate"))),0,0,'C');
	$pdf->Cell(35,5,mysql_result($resGetDealsList,$i,"salary_ko"),0,0,'R');
	$pdf->MultiCell(70,5,mysql_result($resGetDealsList,$i,"posDesc"),0,1,'l');
	$tmpDept = mysql_result($resGetDealsList,$i,"deptDesc");
	$tmpEmpStat = mysql_result($resGetDealsList,$i,"emp_stat");
	$brnName = mysql_result($resGetDealsList,$i,"brnDesc");
	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++,'');
}

$pdf->Cell(30,5,"",0,1);
$userId= $inqTSObj->getSeesionVars();
$pdf->Cell(30,5,"",0,1);
$pdf->Cell($TOTAL_WIDTH,5,"* * * End of report. Nothing follows. * * *",0,1,'C');
$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
$pdf->Cell(30,5,$prntdBy,0,1);
$pdf->Output('End of Contract Report.pdf','D');
function HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page,$brnName) {
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	$pdf->AddPage();
	$pdf->Text(11,10,"RUN DATE: ".$newdate);
	$pdf->Text(11,14,"REPORT ID: LSTEOC");
	$pdf->Text(120,10,$compName);
	$pdf->Text(120,14,"LIST OF EOC REPORT");
	$pdf->Text(220,14, "BRANCH: ".$brnName);
	$pdf->Cell(1,10,"",0,1,'R');
	$pdf->SetFont('Courier', 'B', '10');
	$pdf->Cell(25,5,"",0,0);
	$pdf->Cell(60,5,"EMPLOYEE NAME",0,0,'C');
	$pdf->Cell(30,5,"DATE HIRED",0,0,'C');
	$pdf->Cell(30,5,"EOC",0,0,'C');
	$pdf->Cell(35,5,"BASIC RATE",0,0,'C');
	$pdf->Cell(70,5,"POSITION",0,1,'C');
	$pdf->SetFont('Courier', '', '10');
	$pdf->Cell(1,10,"",0,1,'R');
}
?>
