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
	$frmdate				= $_POST['txtfrDate'];
	$todate					= $_POST['txttoDate'];
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
	
//	$pdf->AddPage();
//	$pdf->Text(11,10,"RUN DATE: ".$newdate);
//	$pdf->Text(11,14,"REPORT ID: LSTUMWB");
//	$pdf->Text(120,10,$compName);
//	$pdf->Text(120,14,"BRANCH LISTING OF UPDATED MINIMUM WAGE REPORT");
//	$pdf->Ln(10);
HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++);	
for ($i=0;$i<$num;$i++){ 
	$pdf->Cell(1,5,"",0,0);
	$pdf->Cell(100,5,mysql_result($resGetDealsList,$i,"brnDesc"),0,0);
	$pdf->Cell(35,5,mysql_result($resGetDealsList,$i,"minimumWage_Old"),0,0);
	$pdf->Cell(30,5,mysql_result($resGetDealsList,$i,"minimumWage_New"),0,0);
	$pdf->Cell(25,5,mysql_result($resGetDealsList,$i,"eCola_Old"),0,0);
	$pdf->Cell(20,5,mysql_result($resGetDealsList,$i,"eCola_New"),0,0);
	$pdf->Cell(27,5,$inqTSObj->valDateArt(mysql_result($resGetDealsList,$i,"effectiveDate")),0,0);
	$pdf->Cell(30,5,$inqTSObj->valDateArt(mysql_result($resGetDealsList,$i,"dateReleased")),0,1);
if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++);
$pdf->Ln(1);
	
}
$pdf->Cell(30,0,"",0,1);
$pdf->Cell($TOTAL_WIDTH,5,"* * * End of report. Nothing follows. * * *",0,1,'C');

$pdf->Cell(30,5,"",0,1);
$userId= $inqTSObj->getSeesionVars();
$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
$pdf->Cell(30,5,$prntdBy,0,1);
$pdf->Output('Branch Minimum Wage Report.pdf','D');
function HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page) {
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	$pdf->AddPage();
	$pdf->Text(11,10,"RUN DATE: ".$newdate);
	$pdf->Text(11,14,"REPORT ID: LSTUMWB");
	$pdf->Text(120,10,$compName);
	$pdf->Text(120,14,"BRANCH LISTING OF UPDATED MINIMUM WAGE REPORT");
	$pdf->Ln(10);	
	$pdf->SetFont('Courier', 'B', '10');
	$pdf->Cell(10,5,"",0,0);
	$pdf->Cell(80,5,"BRANCH NAME",0,0);
	$pdf->Cell(35,5,"OLD MIN. WAGE",0,0);
	$pdf->Cell(35,5,"NEW MIN. WAGE",0,0);
	$pdf->Cell(25,5,"OLD ECOLA",0,0);
	$pdf->Cell(25,5,"NEW ECOLA",0,0);
	$pdf->Cell(30,5,"EFFECTIVITY",0,0);
	$pdf->Cell(30,5,"PROCESSED",0,0);
	$pdf->SetFont('Courier', '', '10');
	$pdf->Cell(1,8,"",0,1,'R');
}
?>
