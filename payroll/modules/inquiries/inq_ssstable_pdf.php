<?
/*	Modified By 	: Genarra Jo - Ann S. Arong
	Date Modified 	: 09 15 2009 1:55pm 
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_ssstable_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');

$inqSSSObj = new inqSSSObj();
$sessionVars = $inqSSSObj->getSeesionVars();
$inqSSSObj->validateSessions('','MODULES');

$compCode = $_SESSION['company_code'];

################ GET TOTAL RECORDS ###############
//$resSearch = $inqSSSObj->getEmpAllowInq();
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
$query = 	"SELECT * FROM tblSssPhic
			ORDER BY sssSeqNo";
//echo $query;
$rs = $inqSSSObj->execQry($query);
$row = $inqSSSObj->getArrRes($rs);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqSSSObj, $compCode, $_GET['orderBy']);
$tempCode = "";
foreach ($row as $sss_list)
{
	
	$pdf->Cell(12,$SPACES,$sss_list['sssSeqNo'],0,0,'C');
	$pdf->Cell(30,$SPACES,$sss_list['sssLowLimit'],0,0,'R');
	$pdf->Cell(40,$SPACES,$sss_list['sssUpLimit'],0,0,'R');
	$pdf->Cell(57,$SPACES,$sss_list['sssEmployer'],0,0,'R');
	$pdf->Cell(53,$SPACES,$sss_list['sssEmployee'],0,0,'R');
	$pdf->Cell(19,$SPACES,$sss_list['EC'],0,0,'R');
	$pdf->Cell(45,$SPACES,$sss_list['sssSalCredit'],0,1,'R');
	
	
	
	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $inqSSSObj, $compCode, $_GET['orderBy']);
	
}
$pdf->Output();


function HEADER_FOOTER($pdf, $inqSSSObj, $compCode, $orderBy) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqSSSObj->currentDateArt();
	$pdf->compName 		= $inqSSSObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: SSSTBL001";
	$pdf->repTitle 		= "SSS Table";
	$pdf->refNo    		= "";
	
	$pdf->dtlLabelUp    = "  #    SSS Low Limit       SSS Up Limit        SSS Employer Share       SSS Employee Share       EC     SSS Salary Credit";
	$pdf->dtlLabelDown  = "";
		
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqSSSObj->getSeesionVars();
	$dispUser = $inqSSSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}

?>
