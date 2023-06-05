<?
/*	Modified By 	: Genarra Jo - Ann S. Arong
	Date Modified 	: 09 15 2009 4:01pm 
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_taxtable_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');

$inqTaxObj = new inqTaxObj();
$sessionVars = $inqTaxObj->getSeesionVars();
$inqTaxObj->validateSessions('','MODULES');

$compCode = $_SESSION['company_code'];

################ GET TOTAL RECORDS ###############
//$resSearch = $inqTaxObj->getEmpAllowInq();
//$numRec = count($resSearch);
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310

####################### FOOTER LANDSCAPE LETTER AND LEGAL = 200
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
############################ Q U E R Y ##################################
$query = 	"SELECT * FROM tblAnnTax
			ORDER BY txLowLimit";
//echo $query;
$rs = $inqTaxObj->execQry($query);
$row = $inqTaxObj->getArrRes($rs);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqTaxObj, $compCode);
$tempCode = "";
$i=0;
foreach ($row as $sss_list)
{
	$i++;
	$pdf->Cell(15,$SPACES,$i,0,0,'C');
	$pdf->Cell(38,$SPACES,$sss_list['txLowLimit'],0,0,'R');
	$pdf->Cell(55,$SPACES,$sss_list['txUpLimit'],0,0,'R');
	$pdf->Cell(70,$SPACES,$sss_list['txFixdAmt'],0,0,'R');
	$pdf->Cell(69,$SPACES,$sss_list['txAddPcent'],0,1,'R');

	
	
	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $inqTaxObj, $compCode);
	
}
$pdf->Output();


function HEADER_FOOTER($pdf, $inqTaxObj, $compCode) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqTaxObj->currentDateArt();
	$pdf->compName 		= $inqTaxObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: TAXTBL001";
	$pdf->repTitle 		= "Annual Tax Table";
	$pdf->refNo    		= "";
	 
	$pdf->dtlLabelUp    = "   #        Tax Low Limit              Tax Up Limit                        Fixed Amt                    Additional %";
	$pdf->dtlLabelDown  = "";
		
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqTaxObj->getSeesionVars();
	$dispUser = $inqTaxObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
} 

?>
