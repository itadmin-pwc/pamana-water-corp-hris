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
$Comp_Cd = $_GET['Comp_Cd'];
$branchCode = $_GET['branchCode'];
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
$query = 	"SELECT * FROM tblBranch
			WHERE compCode='$Comp_Cd' 
			AND brnCode='$branchCode' AND brnStat = 'A' 
			ORDER BY brnDesc ASC ";

$rs = $inqEmpObj->execQry($query);
$row = $inqEmpObj->getArrRes($rs);
$numRec = count($row);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqEmpObj, $compCode, $Comp_Cd);
$tempCode = "";
foreach ($row as $brnList)
{
	$pdf->Cell(60,$SPACES,"Branch Name                :",0,0);
	$pdf->Cell(60,$SPACES,$brnList['brnDesc'],0,1);
	$pdf->Cell(60,$SPACES,"Alias                      :",0,0);
	$pdf->Cell(60,$SPACES,$brnList['brnShortDesc'],0,1);
	$pdf->Cell(60,$SPACES,"Address 1                  :",0,0);
	$pdf->Cell(60,$SPACES,$brnList['brnAddr1'],0,1);
	$pdf->Cell(60,$SPACES,"Address 2                  :",0,0);
	$pdf->Cell(60,$SPACES,$brnList['brnAddr2'],0,1);
	$pdf->Cell(60,$SPACES,"Address 3                  :",0,0);
	$pdf->Cell(60,$SPACES,$brnList['brnAddr3'],0,1);
	$pdf->Cell(60,$SPACES,"Minimum Wage               :",0,0);
	$pdf->Cell(60,$SPACES,$brnList['minWage'],0,1);
	$pdf->Cell(60,$SPACES,"Signatory                  :",0,0);
	$pdf->Cell(60,$SPACES,$brnList['brnSignatory'],0,1);
	$pdf->Cell(60,$SPACES,"Title of Branch Signatory  :",0,0);
	$pdf->Cell(60,$SPACES,$brnList['brnSignTitle'],0,1);
	$pdf->Cell(60,$SPACES,"Default Group Code         :",0,0);
	$pdf->Cell(60,$SPACES,$brnList['brnDefGrp'],0,1);
	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $inqEmpObj, $compCode, $Comp_Cd);
}
if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $inqEmpObj, $compCode, $Comp_Cd);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Output();
#############################################################################

function HEADER_FOOTER($pdf, $inqEmpObj, $compCode, $Comp_Cd) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqEmpObj->currentDateArt();
	$pdf->compName 		= $inqEmpObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: BRNCHINF001";
	$pdf->repTitle 		= "Branch of ".$inqEmpObj->getCompanyName($Comp_Cd);
	$pdf->refNo    		= "";
	
	$pdf->dtlLabelUp    = "B R A N C H   I N F O R M A T I O N";
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
