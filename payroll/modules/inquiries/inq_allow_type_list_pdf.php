<?
/*	Modified By 	: Genarra Jo - Ann S. Arong
	Date Modified 	: 09142009 2:38pm 
*/
################### INCLUDE FILE #################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_allow_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$inqEmpAllowObj = new inqEmpAllowObj();
$sessionVars = $inqEmpAllowObj->getSeesionVars();
$inqEmpAllowObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
################ GET TOTAL RECORDS ###############
//$resSearch = $inqEmpAllowObj->getEmpAllowInq();
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
$query = 	"SELECT * FROM tblAllowType  
			 WHERE compCode = '$compCode' 
			 ORDER BY allowCode, allowDesc ASC";
$rs = $inqEmpAllowObj->execQry($query);
$row = $inqEmpAllowObj->getArrRes($rs);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode);
$tempCode = "";
foreach ($row as $allows)
{
	$pdf->Cell(60,$SPACES,$allows['allowDesc'],0,1);
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode);
}
$pdf->Output();

function HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqEmpAllowObj->currentDateArt();
	$pdf->compName 		= $inqEmpAllowObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: ALLOL001";
	$pdf->repTitle 		= "List of Allowance Types";
	$pdf->refNo    		= "";
	$pdf->dtlLabelUp    = "Allowance Types ";
	$pdf->dtlLabelDown  = "";
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqEmpAllowObj->getSeesionVars();
	$dispUser = $inqEmpAllowObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
