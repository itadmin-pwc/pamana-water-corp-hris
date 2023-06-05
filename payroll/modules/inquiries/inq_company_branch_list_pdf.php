<?
/*	Modified By 	: Genarra Jo - Ann S. Arong
	Date Modified 	: 09 16 2009 9:14am 
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_company_list_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');

$inqCompObj = new inqCompObj();
$sessionVars = $inqCompObj->getSeesionVars();
$inqCompObj->validateSessions('','MODULES');

$compCode = $_SESSION['company_code'];

################ GET TOTAL RECORDS ###############
//$resSearch = $inqCompObj->getEmpAllowInq();
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
$query = 	"SELECT * FROM tblBranch
			WHERE compCode='".$_GET["Comp_Cd"]."' 
			AND brnStat='A' 
			ORDER BY brnDesc";

$rs = $inqCompObj->execQry($query);
$row = $inqCompObj->getArrRes($rs);
$numRec = count($row);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqCompObj, $compCode);
$tempCode = "";
$compName = $inqCompObj->getCompanyArt($_GET["Comp_Cd"]);
$pdf->SetFont('Courier', 'B', '10');
$pdf->Cell(1,$SPACES," ",0,0,'L');
$pdf->Cell(97,$SPACES,$compName['compName'],0,0);
$pdf->Cell(60,$SPACES,$compName['compShort'],0,0);
$pdf->Cell(40,$SPACES,$compName['compAddr1'],0,1);
$pdf->SetFont('Courier', '', '10');
foreach ($row as $sss_list)
{
	$pdf->Cell(1,$SPACES," ",0,0,'L');
	$pdf->Cell(97,$SPACES,$sss_list['brnDesc'],0,0,'L');
	$pdf->Cell(60,$SPACES,$sss_list['brnShortDesc'],0,0,'L');
	$pdf->Cell(40,$SPACES,$sss_list['brnAddr1'],0,1,'L');

	
	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $inqCompObj, $compCode);
	
}
if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $inqCompObj, $compCode);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Branches = ".$numRec,0,1);
$pdf->Output();
#############################################################################

function HEADER_FOOTER($pdf, $inqCompObj, $compCode) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqCompObj->currentDateArt();
	$pdf->compName 		= $inqCompObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: BRNCHLST001";
	$pdf->repTitle 		= "List of Branches";
	$pdf->refNo    		= "";
	
	$pdf->dtlLabelUp    = " Company/Branch                                 Alias                       Address";
	$pdf->dtlLabelDown  = "";
		
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqCompObj->getSeesionVars();
	$dispUser = $inqCompObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}

?>
