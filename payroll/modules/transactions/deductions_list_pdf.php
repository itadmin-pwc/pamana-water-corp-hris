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
$pdf = new FPDF('L', 'mm', 'LEGAL');
$pdf->SetFont('Courier', '', '10');
$TOTAL_WIDTH   			= 310;
$TOTAL_WIDTH_2 			= 155;
$TOTAL_WIDTH_3 			= 103;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 310;
$pdf->TOTAL_WIDTH_2     = 155;
$pdf->TOTAL_WIDTH_3     = 103;
$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
if($_GET['isSearch'] == 1){
	if($_GET['srchType'] == 0) { $refNo = " AND refNo = '{$_GET['txtSrch']}' "; } else { $refNo = ""; $statusType = ""; }
	if($_GET['srchType'] == 1) $statusType = " AND dedStat = 'A' ";
	if($_GET['srchType'] == 2) $statusType = " AND dedStat = 'H' ";
	if($_GET['srchType'] == 3) $statusType = " AND dedStat = 'P' ";
}
$qryDedList = "SELECT * FROM tblDedTranHeader 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     $refNo $statusType 
				 ORDER BY refNo DESC ";
$resDedList = $inqTSObj->execQry($qryDedList);
$arrDedList = $inqTSObj->getArrRes($resDedList);
$numRec = count($arrDedList);
#####################################################################
$tempCode = "";
$TOTAL_PAGE=1;
HEADER_FOOTER($pdf, $inqTSObj, $compCode);
############################### LOOPING THE PAGES ###########################
foreach ($arrDedList as $dedListVal){
	$arrTotal = $inqTSObj->getTranDeductionsTotal($sessionVars['compCode'],$dedListVal['refNo']);
	$arrGrandTotal = $arrGrandTotal+$arrTotal['totAmt'];
	$pdf->Cell(15,$SPACES,"REF.NO.      : ".$dedListVal['refNo'],0,1);
	$pdf->Cell(80,$SPACES,"TRANSACTION  : ".$inqTSObj->getTransTypeDescArt($sessionVars['compCode'],$dedListVal['trnCode']),0,1);
	$pdf->Cell(60,$SPACES,"REMARKS      : ".$dedListVal['dedRemarks'],0,1);
	if ($dedListVal['dedStat']=="P") $dedStat =  "PROCESSED";
	if ($dedListVal['dedStat']=="A") $dedStat =  "ACTIVE";
	if ($dedListVal['dedStat']=="H") $dedStat =  "HELD";
	$pdf->Cell(10,$SPACES,"STATUS       : ".$dedStat,0,1);
	
	$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());
	if ($pdf->GetY() > 240) { HEADER_FOOTER($pdf, $inqTSObj, $compCode); $TOTAL_PAGE++; }
	
	$qryDedDtlList = "SELECT * FROM tblDedTranDtl 
					 WHERE compCode = '{$sessionVars['compCode']}' 
					 AND refNo = '{$dedListVal['refNo']}'  
					 ORDER BY empNo ASC ";
	$resDedDtlList = $inqTSObj->execQry($qryDedDtlList);
	$arrDedDtlList = $inqTSObj->getArrRes($resDedDtlList);
	$numDtlRec = count($arrDedDtlList);
	
	foreach ($arrDedDtlList as $dedDtlListVal){
		$empInfo = $inqTSObj->getUserInfo($sessionVars['compCode'],$dedDtlListVal['empNo'],'');
		$nameInit = $empInfo['empFirstName'][0].".".$empInfo['empMidName'][0].".";
		if ($dedDtlListVal['payGrp']==1) { $grpName = "Group 1"; } 
		if ($dedDtlListVal['payGrp']==2) { $grpName = "Group 2"; }
		$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $dedDtlListVal['payCat']);
		$pdf->Cell(25,$SPACES,$empInfo['empNo'],0,0);
		$pdf->Cell(50,$SPACES,$empInfo['empLastName'].", ".$nameInit,0,0);
		$pdf->Cell(20,$SPACES,$grpName,0,0);
		$pdf->Cell(60,$SPACES,$catName['payCatDesc'],0,0);
		$pdf->Cell(20,$SPACES,$dedDtlListVal['trnAmount'],0,0,'R');
		$pdf->Cell(20,$SPACES,$dedDtlListVal['trnCntrlNo'],0,1,'C');
	
		if ($pdf->GetY() > 240) { HEADER_FOOTER($pdf, $inqTSObj, $compCode); $TOTAL_PAGE++; }
	}
	$pdf->SetFont('Courier', 'B', '10');
	$pdf->Cell(155,$SPACES,"Sub Total ($numDtlRec item/s) :",0,0,'R');
	$pdf->Cell(20,$SPACES,$arrTotal['totAmt'],0,1,'R');
	$pdf->SetFont('Courier', '', '10');
	$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());
	$pdf->Ln(10);
}
########## GRAND TOTAL TIMESHEET
$pdf->SetFont('Courier', 'B', '10');
$pdf->Cell(155,$SPACES,"GRAND TOTAL: ",0,0,'R');
$pdf->Cell(20,$SPACES,str_replace(",","",number_format($arrGrandTotal,2)),0,1,'R');
$pdf->SetFont('Courier', '', '10');
if ($pdf->GetY() > 245) { HEADER_FOOTER($pdf, $inqTSObj, $compCode); $TOTAL_PAGE++; }
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Ln(5);
$pdf->Cell(50,$SPACES,"Total Reference/s = ".$numRec,0,1);
#########################################################################
$pdf->Output();

function HEADER_FOOTER($pdf, $inqTSObj, $compCode) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AliasNbPages();
	$pdf->AddPage();
	############################ H E A D E R ################################
	$pdf->currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$pdf->compName 		= $inqTSObj->getCompanyName($compCode);
	$pdf->reppages 		= "{nb}";
	$pdf->repId    		= "Report ID: DEDUCTS02";
	$pdf->repTitle 		= "Deductions Proof List";
	$pdf->refNo    		= "";
	$pdf->dtlLabelUp    = "                 ";
	$pdf->dtlLabelDown  = " EMP.NO.    EMPLOYEE NAME           GROUP    CATEGORY                       AMOUNT  CNTRL.NO.";	
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserInfo($userId["compCode"] , $userId["empNo"],""); 
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
