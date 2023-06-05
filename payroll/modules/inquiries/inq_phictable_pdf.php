<?
/*	Modified By 	: Genarra Jo - Ann S. Arong
	Date Modified 	: 09 15 2009 4:01pm 
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_phictable_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');

$inqPhicObj = new inqPhicObj();
$sessionVars = $inqPhicObj->getSeesionVars();
$inqPhicObj->validateSessions('','MODULES');

$compCode = $_SESSION['company_code'];

################ GET TOTAL RECORDS ###############
//$resSearch = $inqPhicObj->getEmpAllowInq();
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
$rs = $inqPhicObj->execQry($query);
$row = $inqPhicObj->getArrRes($rs);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqPhicObj, $compCode, $_GET['orderBy']);
$tempCode = "";
foreach ($row as $sss_list)
{
	
	$pdf->Cell(15,$SPACES,$sss_list['sssSeqNo'],0,0,'C');
	$pdf->Cell(38,$SPACES,$sss_list['sssLowLimit'],0,0,'R');
	$pdf->Cell(55,$SPACES,$sss_list['sssUpLimit'],0,0,'R');
	$pdf->Cell(70,$SPACES,$sss_list['phicEmployer'],0,0,'R');
	$pdf->Cell(69,$SPACES,$sss_list['phicEmployee'],0,1,'R');

	
	
	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $inqPhicObj, $compCode, $_GET['orderBy']);
	
}
$pdf->Output();


function HEADER_FOOTER($pdf, $inqPhicObj, $compCode, $orderBy) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqPhicObj->currentDateArt();
	$pdf->compName 		= $inqPhicObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: PHICTBL001";
	$pdf->repTitle 		= "Philhealth Table";
	$pdf->refNo    		= "";
	
	$pdf->dtlLabelUp    = "   #      Phic. Low Limit            Phic. Up Limit             Phic. Employer Share            Phic. Employee Share";
	$pdf->dtlLabelDown  = "";
		
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqPhicObj->getSeesionVars();
	$dispUser = $inqPhicObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}

?>
