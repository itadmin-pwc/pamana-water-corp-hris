<?
/*	Modified By 	: Genarra Jo - Ann S. Arong
	Date Modified 	: 09142009 2:38pm 
*/
################### INCLUDE FILE #################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_loans_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$inqEmpLoanObj = new inqEmpLoanObj();
$sessionVars = $inqEmpLoanObj->getSeesionVars();
$inqEmpLoanObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
################ GET TOTAL RECORDS ###############
//$resSearch = $inqEmpLoanObj->getEmpAllowInq();
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
$query = 	"SELECT * FROM tblLoanType  
			 WHERE compCode = '$compCode' 
			 ORDER BY lonTypeCd, lonTypeDesc ASC";
$rs = $inqEmpLoanObj->execQry($query);
$row = $inqEmpLoanObj->getArrRes($rs);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqEmpLoanObj, $compCode);
$tempCode = "";
foreach ($row as $loans)
{
	$templonTypeCd = (string)$loans['lonTypeCd'];
	if ($tempCode != $templonTypeCd[0]) {
		if ($tempCode!="") $pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());
		if ($templonTypeCd[0]=="1") $loanTitle = "SSS Loans";
		if ($templonTypeCd[0]=="2") $loanTitle = "Pag-ibig Loans";
		if ($templonTypeCd[0]=="3") $loanTitle = "Other Loans"; 
		$pdf->Cell(60,$SPACES,$loanTitle,0,0);
	} else {
		$pdf->Cell(60,$SPACES,"",0,0);
	}
	$pdf->Cell(60,$SPACES,$loans['lonTypeDesc'],0,1);
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqEmpLoanObj, $compCode);
	$tempCode = $templonTypeCd[0];
}
$pdf->Output();

function HEADER_FOOTER($pdf, $inqEmpLoanObj, $compCode) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqEmpLoanObj->currentDateArt();
	$pdf->compName 		= $inqEmpLoanObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: LOANL001";
	$pdf->repTitle 		= "List of Loan Types";
	$pdf->refNo    		= "";
	$pdf->dtlLabelUp    = "                             Loan Types ";
	$pdf->dtlLabelDown  = "";
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqEmpLoanObj->getSeesionVars();
	$dispUser = $inqEmpLoanObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
