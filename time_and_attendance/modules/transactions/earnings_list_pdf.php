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
$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $_SESSION['pay_category']);
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
	if($_GET['srchType'] == 1) { $refNo = " AND refNo = '{$_GET['txtSrch']}' "; } else { $refNo = ""; $statusType = ""; }
	if($_GET['srchType'] == 2) $statusType = " AND earnStat = 'A' ";
	if($_GET['srchType'] == 3) $statusType = " AND earnStat = 'H' ";
	if($_GET['srchType'] == 4) $statusType = " AND earnStat = 'P' ";
	if ($_GET['payPd']!=0) {
		$arrPayPd = $inqTSObj->getSlctdPd($_SESSION['company_code'],$_GET['payPd']);
		$statusType .= " AND pdYear='".$arrPayPd['pdYear']."' and pdNumber='".$arrPayPd['pdNumber']."' ";
	}
}
$qryEarnList = "SELECT * FROM tblEarnTranHeader 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     $refNo $statusType 
				 ORDER BY refNo DESC ";
$resEarnList = $inqTSObj->execQry($qryEarnList);
$arrEarnList = $inqTSObj->getArrRes($resEarnList);
$numRec = count($arrEarnList);
#####################################################################
$tempCode = "";
$TOTAL_PAGE=1;
HEADER_FOOTER($pdf, $inqTSObj, $compCode,$catName);
############################### LOOPING THE PAGES ###########################
foreach ($arrEarnList as $earnListVal){
	$arrTotal = $inqTSObj->getTranEarningsTotal($sessionVars['compCode'],$earnListVal['refNo']);
	
	$arrGrandTotal = $arrGrandTotal+$arrTotal['totAmt'];
	$pdf->Cell(15,$SPACES,"REF.NO.      : ".$earnListVal['refNo'],0,1);
	$pdf->Cell(80,$SPACES,"TRANSACTION  : ".$inqTSObj->getTransTypeDescArt($sessionVars['compCode'],$earnListVal['trnCode']),0,1);
	$pdf->Cell(60,$SPACES,"REMARKS      : ".$earnListVal['earnRem'],0,1);
	if ($earnListVal['earnStat']=="P") $earnStat =  "PROCESSED";
	if ($earnListVal['earnStat']=="A") $earnStat =  "ACTIVE";
	if ($earnListVal['earnStat']=="H") $earnStat =  "HELD";
	$pdf->Cell(10,$SPACES,"STATUS       : ".$earnStat,0,1);
	
	$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());
	if ($pdf->GetY() > 240) { HEADER_FOOTER($pdf, $inqTSObj, $compCode,$catName); $TOTAL_PAGE++; }
	
	$qryEarnDtlList = "SELECT * FROM tblEarnTranDtl 
					 WHERE compCode = '{$sessionVars['compCode']}' 
					 AND refNo = '{$earnListVal['refNo']}'  
					 ORDER BY empNo ASC ";
	$resEarnDtlList = $inqTSObj->execQry($qryEarnDtlList);
	$arrEarnDtlList = $inqTSObj->getArrRes($resEarnDtlList);
	$numDtlRec = count($arrEarnDtlList);
	
	foreach ($arrEarnDtlList as $earnDtlListVal){
		$empInfo = $inqTSObj->getUserInfo($sessionVars['compCode'],$earnDtlListVal['empNo'],'');
		$nameInit = $empInfo['empFirstName'][0].".".$empInfo['empMidName'][0].".";
		if ($earnDtlListVal['payGrp']==1) { $grpName = "Group 1"; } 
		if ($earnDtlListVal['payGrp']==2) { $grpName = "Group 2"; }
		$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $earnDtlListVal['payCat']);
		$pdf->Cell(25,$SPACES,$empInfo['empNo'],0,0);
		$pdf->Cell(50,$SPACES,$empInfo['empLastName'].", ".$nameInit,0,0);
		$pdf->Cell(20,$SPACES,$grpName,0,0);
		$pdf->Cell(60,$SPACES,$catName['payCatDesc'],0,0);
		$pdf->Cell(20,$SPACES,$earnDtlListVal['trnAmount'],0,0,'R');
		if ($earnDtlListVal['trnTaxCd']=="Y") $earnTax =  "YES";
		if ($earnDtlListVal['trnTaxCd']=="N") $earnTax =  "NO";
		if ($earnDtlListVal['trnTaxCd']=="") $earnTax =  "---";
		$pdf->Cell(20,$SPACES,$earnTax,0,1,'C');
	
		if ($pdf->GetY() > 240) { HEADER_FOOTER($pdf, $inqTSObj, $compCode,$catName); $TOTAL_PAGE++; }
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
if ($pdf->GetY() > 245) { HEADER_FOOTER($pdf, $inqTSObj, $compCode,$catName); $TOTAL_PAGE++; }
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Ln(5);
$pdf->Cell(50,$SPACES,"Total Reference/s = ".$numRec,0,1);
#########################################################################
$pdf->Output();

function HEADER_FOOTER($pdf, $inqTSObj, $compCode,$catName) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AliasNbPages();
	$pdf->AddPage();
	############################ H E A D E R ################################
	$pdf->currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$pdf->compName 		= $inqTSObj->getCompanyName($compCode);
	$pdf->reppages 		= "{nb}";
	$pdf->repId    		= "Report ID: EARNGS002";
	$pdf->repTitle 		= "Earnings Proof List";
	$pdf->refNo    		= "";
	$pdf->dtlLabelUp    = "                 ";
	$pdf->dtlLabelDown  = " EMP.NO.    EMPLOYEE NAME           GROUP    CATEGORY                       AMOUNT  TAXABLE";	
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserInfo($userId["compCode"] , $userId["empNo"],""); 
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
