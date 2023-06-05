<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("ts_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$inqTSObj->getOTs();
	$inqTSObj->getDeductions();
	$compCode = $_SESSION['company_code'];
################ GET TOTAL RECORDS ###############

############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
	$pdf = new FPDF('P', 'mm', 'LETTER');
	$pdf->SetFont('Courier', '', '9');
	$TOTAL_WIDTH   			= 200;
	$TOTAL_WIDTH_2 			= 53;
	$TOTAL_WIDTH_3 			= 88;
	$SPACES        			= 5;
	$pdf->TOTAL_WIDTH       = 200;
	$pdf->TOTAL_WIDTH_2     = 53;
	$pdf->TOTAL_WIDTH_3     = 88;
	$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
	$arrEventLogs = $inqTSObj->legalPay($_GET['branch'],$_GET['hist'],$_GET['from'],$_GET['to'],$_GET['group']);
	
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
$GTot = 0;
############################### LOOPING THE PAGES ###########################
$tmp_empName = "";
$arrBranch = $inqTSObj->UserBranch($_GET['branch']);
foreach($arrBranch as $valbranch) {
	$pdf->SetFont('Courier', 'B', '9');
	$pdf->Cell(40,$SPACES,$valbranch['brnDesc'],0,1,'L');
	foreach ($arrEventLogs as $val){
		if ($valbranch['brnCode']==$val['brnCode']) {
			$pdf->SetFont('Courier', '', '9');
			if ($val['empName'] != $tmp_empName) {
				$pdf->Cell(40,$SPACES,$val['empName'] . " " . $val['empNo'],0,1,'L');
			} 
			$pdf->Cell(120,$SPACES,'',0,0);
			$tmp_empName = $val['empName'];
			$pdf->Cell(26,$SPACES,date('m/d/Y',strtotime($val['tsDate'])),0,0);
			$pdf->Cell(1,$SPACES,"",0,1,'L');
			$ctr++;
			if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
		}
	}
}
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(3);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".($ctr-1),0,1);
#########################################################################
$pdf->Output('legalpay.pdf','D');


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: LEGALPAY";
	$repTitle 		= "LEGAL PAY REPORT           $dt";
	$refNo    		= ""; 

	$dtlLabelDown   = "                   EMPLOYEE                                     LEGAL DAY";
	$dtlLabelDown2   = "";
	#########################################################################
	
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(170,15,$refNo);
	$pdf->Text(10,23,$dtlLabelDown);
	########################### F O O T E R  ################################
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	
	$footerHt = 270; //////////////PORTRATE LETTER ONLY
	$pdf->Line(10,$footerHt-6,$TOTAL_WIDTH+6,$footerHt-6);
	$pdf->Text(10,$footerHt,$prntdBy);
	$pdf->Text(160,$footerHt,'Approved By:');
	$pdf->Ln(18);
}


?>
