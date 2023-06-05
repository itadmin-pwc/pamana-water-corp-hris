<?
################### INCLUDE FILE #################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
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
if($_GET['isSearch'] == 1){
	if($_GET['srchType'] == 0) $transCode = " AND trnCode = '{$_GET['txtSrch']}' "; else { $transCode = ""; $transCat = ""; }
	if($_GET['srchType'] == 1) $transDesc = " AND trnDesc LIKE '{$_GET['txtSrch']}%' "; else { $transDesc = ""; $transCat = ""; }
	if($_GET['srchType'] == 2) $transCat = " AND trnCat = 'E' "; 
	if($_GET['srchType'] == 3) $transCat = " AND trnCat = 'D' "; 
}
$qryTransList = "SELECT * FROM tblPayTransType 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     $transCode $transDesc $transCat AND trnStat = 'A' 
				 ORDER BY trnRecode,trnCode ASC ";
$resTransList = $inqTSObj->execQry($qryTransList);
$arrTransList = $inqTSObj->getArrRes($resTransList);
$totRec = count($arrTransList);
#####################################################################
$tempCode = "";
$TOTAL_PAGE=1;
HEADER_FOOTER($pdf, $inqTSObj, $compCode);
############################### LOOPING THE PAGES ###########################
foreach ($arrTransList as $transListVal){
	if ($transListVal['trnCat']=="E") $trnCat = "EARNINGS";
	if ($transListVal['trnCat']=="D") $trnCat = "DEDUCTIONS";
	if ($transListVal['trnApply']=="1") $trnPayPeriod = "1ST ";
	if ($transListVal['trnApply']=="2") $trnPayPeriod = "2ND";
	if ($transListVal['trnApply']=="3") $trnPayPeriod = "BOTH";
	if ($transListVal['trnTaxCd']=="Y") $trnTax = "YES";
	if ($transListVal['trnTaxCd']=="N") $trnTax = "NO";
	if ($transListVal['trnTaxCd']=="") $trnTax = "---";
	
	if ($tempCode!=$transListVal['trnRecode']) {
		if ($tempCode!="") {
			$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());
		}
		$pdf->SetFont('Courier', 'B', '10');
		$pdf->Cell(60,$SPACES,"REGISTER GROUP : ".$inqTSObj->getTransTypeDescArt($sessionVars['compCode'],$transListVal['trnRecode']),0,1);
		$pdf->SetFont('Courier', '', '10');
	}
	$pdf->Cell(15,$SPACES,$transListVal['trnCode'],0,0);
	$pdf->Cell(80,$SPACES,$inqTSObj->getTransTypeDescArt($sessionVars['compCode'],$transListVal['trnCode']),0,0);
	$pdf->Cell(25,$SPACES,$trnCat,0,0);
	$pdf->Cell(20,$SPACES,$trnPayPeriod,0,0);
	$pdf->Cell(20,$SPACES,$transListVal['trnPriority'],0,0);
	$pdf->Cell(20,$SPACES,$trnTax,0,1);
	$tempCode=$transListVal['trnRecode'];
	if ($pdf->GetY() > 250) { HEADER_FOOTER($pdf, $inqTSObj, $compCode); $TOTAL_PAGE++; }
}
#########################################################################
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Ln(5);
$pdf->Cell(50,$SPACES,"Total Record/s = ".$totRec,0,1);
#########################################################################
$pdf->Output();

function HEADER_FOOTER($pdf, $inqTSObj, $compCode) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$pdf->currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$pdf->compName 		= $inqTSObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: TRNSTYPE01";
	$pdf->repTitle 		= "Transactions Type List";
	$pdf->refNo    		= "";
	$pdf->dtlLabelUp    = "                                                          PAY     ";
	$pdf->dtlLabelDown  = " CODE   DESCRIPTION                          CATEGORY    PERIOD   PRIORITY  TAXABLE";	
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
