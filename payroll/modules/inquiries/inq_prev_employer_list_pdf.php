<?
/*	Modified By 	: Genarra Jo - Ann S. Arong
	Date Modified 	: 09 16 2009 9:14am 
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');

$inqEmpObj = new inqEmpObj();
$sessionVars = $inqEmpObj->getSeesionVars();
$inqEmpObj->validateSessions('','MODULES');

$compCode = $_SESSION['company_code'];
$prevEmpNo = $_GET['prevEmpNo'];
################ GET TOTAL RECORDS ###############
//$resSearch = $inqEmpObj->getEmpAllowInq();
//$numRec = count($resSearch);
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310

####################### FOOTER LANDSCAPE LETTER AND LEGAL = 200
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$pdf = new FPDF('P', 'mm', 'LETTER');
$pdf->SetFont('Courier', '', '10');
$TOTAL_WIDTH   			= 200;
$TOTAL_WIDTH_2 			= 100;
$TOTAL_WIDTH_3 			= 66;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 200;
$pdf->TOTAL_WIDTH_2     = 100;
$pdf->TOTAL_WIDTH_3     = 66;
$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
$query = 	"SELECT * FROM tblPrevEmployer
			WHERE compCode='$compCode' 
			AND empNo='$prevEmpNo' AND prevStat = 'A' 
			ORDER BY prevEmplr ASC ";

$rs = $inqEmpObj->execQry($query);
$row = $inqEmpObj->getArrRes($rs);
$numRec = count($row);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
$empName = $inqEmpObj->getUserInfo($compCode, $prevEmpNo,"");
$employee = $empName['empLastName'].", ".$empName['empFirstName']." ".$empName['empMidName'];
HEADER_FOOTER($pdf, $inqEmpObj, $compCode, $employee);
$tempCode = "";
foreach ($row as $prevList)
{
	$pdf->Cell(1,$SPACES," ",0,0,'L');
	$pdf->Cell(53,$SPACES,$prevList['prevEmplr'],0,0,'L');
	$pdf->Cell(20,$SPACES,$prevList['yearCd'],0,0,'L');
	$pdf->Cell(58,$SPACES,$prevList['empAddr1'],0,0,'L');
	$pdf->Cell(30,$SPACES,$prevList['prevEarnings'],0,0,'R');
	$pdf->Cell(30,$SPACES,$prevList['prevTaxes'],0,1,'R');
	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $inqEmpObj, $compCode, $employee);
}
if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $inqEmpObj, $compCode, $employee);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Previous Employer = ".$numRec,0,1);
$pdf->Output();
#############################################################################

function HEADER_FOOTER($pdf, $inqEmpObj, $compCode, $employee) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqEmpObj->currentDateArt();
	$pdf->compName 		= $inqEmpObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: PREEMP002";
	$pdf->repTitle 		= "Previous Employer of ".$employee;
	$pdf->refNo    		= "";
	
	$pdf->dtlLabelUp    = " Previous Employer        Year        Address                       Earnings        Taxes";
	$pdf->dtlLabelDown  = "";
		
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqEmpObj->getSeesionVars();
	$dispUser = $inqEmpObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}

?>
