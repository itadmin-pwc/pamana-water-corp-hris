<?
/*	Modified By 	: Genarra Jo - Ann S. Arong
	Date Modified 	: 09 15 2009 4:01pm 
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_ottable_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');

$inqOtObj = new inqOtObj();
$sessionVars = $inqOtObj->getSeesionVars();
$inqOtObj->validateSessions('','MODULES');

$compCode = $_SESSION['company_code'];

################ GET TOTAL RECORDS ###############
//$resSearch = $inqOtObj->getEmpAllowInq();
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
$query = 	"SELECT * from tblOtPrem
					 ORDER BY dayType ASC ";
//echo $query;
$rs = $inqOtObj->execQry($query);
$row = $inqOtObj->getArrRes($rs);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqOtObj, $compCode);
$tempCode = "";
$i=0;
foreach ($row as $sss_list)
{
	$i++;
	$pdf->Cell(15,$SPACES,$i,0,0,'C');
	$pdf->Cell(60,$SPACES,$inqOtObj->getDayTypeDescArt($sss_list['dayType']),0,0);
	$pdf->Cell(25,$SPACES,$sss_list['otPrem8'],0,0,'R');
	$pdf->Cell(25,$SPACES,$sss_list['otPremOvr8'],0,0,'R');
	$pdf->Cell(25,$SPACES,$sss_list['ndPrem8'],0,0,'R');
	$pdf->Cell(25,$SPACES,$sss_list['ndPremOvr8'],0,1,'R');
	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $inqOtObj, $compCode);
}
$pdf->Output();


function HEADER_FOOTER($pdf, $inqOtObj, $compCode) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqOtObj->currentDateArt();
	$pdf->compName 		= $inqOtObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: OTTBL001";
	$pdf->repTitle 		= "Overtime Table";
	$pdf->refNo    		= "";
	 
	$pdf->dtlLabelUp    = "   #    Day Type                         OT Not >     OT >       ND Not >     ND > ";
	$pdf->dtlLabelDown  = "                                         8 Hrs.       8 Hrs.     8 Hrs.       8 Hrs.";
		
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqOtObj->getSeesionVars();
	$dispUser = $inqOtObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
} 

?>
