<?
################### INCLUDE FILE #################
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
$inqCompObj->compCode      = $compCode;
################ GET TOTAL RECORDS ###############
$resSearch = $inqCompObj->getCompany($_SESSION['company_code']);
$numRec = count($resSearch);
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
$qry = "SELECT * FROM tblCompany 
			     ORDER BY compName ASC ";
$res = $inqCompObj->execQry($qry);
$arr = $inqCompObj->getArrRes($res);
#####################################################################
$tempCode = "";
HEADER_FOOTER($pdf, $inqCompObj, $compCode);
############################### LOOPING THE PAGES ###########################
foreach ($arr as $compListVal){
	$pdf->SetFont('Courier', 'B', '10');
	if ($tempCode!=$compListVal['compCode']) {
		$pdf->Cell(90,$SPACES,$compListVal['compName'],0,0);
		if ($tempCode!="") {
			$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());
		}
	} else {
		$pdf->Cell(90,$SPACES,"",0,0);
	}
	$pdf->Cell(25,$SPACES,$compListVal['compShort'],0,0);
	$pdf->Cell(70,$SPACES,$compListVal['compAddr1'],0,0);
	
	$pdf->Cell(25,$SPACES,$compListVal['compTin'],0,0);
	$pdf->Cell(25,$SPACES,$compListVal['compSssNo'],0,1);
	$pdf->SetFont('Courier', '', '10');
	if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $inqCompObj, $compCode);
	
	$arrChild = $inqCompObj->getBranchListArt($compListVal['compCode']);
	foreach ($arrChild as $branchListVal){
		$pdf->Cell(10,$SPACES,"",0,0);
		$pdf->Cell(80,$SPACES,$branchListVal['brnDesc'],0,0);
		$pdf->Cell(25,$SPACES,$branchListVal['brnShortDesc'],0,0);
		$pdf->Cell(70,$SPACES,$branchListVal['brnAddr1'],0,1);
		if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $inqCompObj, $compCode);
		$totalRec = $inqCompObj->getBranchTotalArt($compListVal['compCode']);
		$newBrnCode = split("/",$totalRec[refMax]);
		if ($newBrnCode[1]==$branchListVal['brnCode']) {
			$pdf->SetFont('Courier', 'B', '10');
			$pdf->Cell(10,$SPACES,"",0,0);
			$pdf->Cell(10,$SPACES,"Total Branches: ".$totalRec['totRec'],0,1);
			$pdf->SetFont('Courier', '', '10');
		}
	}
	$tempCode=$compListVal['compCode'];
}
#########################################################################
if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $inqCompObj, $compCode);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Companies = ".$numRec,0,1);
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $inqCompObj, $compCode) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqCompObj->currentDateArt();
	$pdf->compName 		= $inqCompObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: COMPL001";
	$pdf->repTitle 		= "Company and Branches List";
	$pdf->refNo    		= "";
	$pdf->dtlLabelUp    = " Company/Branch                            Alias       Address                          TIN #       SSS # ";
	$pdf->dtlLabelDown  = "";
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqCompObj->getSeesionVars();
	$dispUser = $inqCompObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id'] ); 
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
