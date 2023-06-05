<?
################### INCLUDE FILE #################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$common = new commonObj();

$compCode = $_SESSION['company_code'];
################ GET TOTAL RECORDS ###############


############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 200
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$pdf = new FPDF('L', 'mm', 'LETTER');
$pdf->SetFont('Courier', '', '10');
$TOTAL_WIDTH   			= 235;
$TOTAL_WIDTH_2 			= 100;
$TOTAL_WIDTH_3 			= 66;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 0;
$pdf->TOTAL_WIDTH_2     = 100;
$pdf->TOTAL_WIDTH_3     = 66;
$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
 $qryLoanList = "SELECT tblARTransData.invoiceNo,tblARTransData.custNo,tblARTransData.id,tblARTransData.empNo, tblLoanType.lonTypeShortDesc as loanType, 
 				tblARTransData.refNo,tblARTransData.amount, tblARTransData.dedAmt, tblARTransData.dedSked, tblARTransData.NoDed, tblARTransData.transDate, tblEmpLoans.lonRefNo
		FROM tblARTransData INNER JOIN
                      tblLoanType ON tblARTransData.transType = tblLoanType.lonTypeCd LEFT OUTER JOIN
                      tblEmpLoans ON tblARTransData.transType = tblEmpLoans.lonTypeCd AND 
                      tblARTransData.refNo  = tblEmpLoans.lonRefNo 
		WHERE userID='{$_SESSION['user_id']}' and status is null
ORDER BY refNo";
$resLoanList = $common->execQry($qryLoanList);
$arrLoanList = $common->getArrRes($resLoanList);
$numRec = count($arrLoanList);
#####################################################################
HEADER_FOOTER($pdf, $common, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
############################### LOOPING THE PAGES ###########################
foreach ($arrLoanList as $val){
	if (trim($val['empNo']) == '') {
		$rem = "Unknown Customer No.";
	} else {
		if ($val['lonRefNo'] !='')	 {
			$rem = "Duplicate Ref. No.";
		}
	}
	$pdf->Cell(10,$SPACES,$ctr,0,0);
	$pdf->Cell(32,$SPACES,$val['custNo'],0,0);
	$pdf->Cell(82,$SPACES,$val['refNo'],0,0);
	$pdf->Cell(52,$SPACES,$val['invoiceNo'],0,0);
	$pdf->Cell(30,$SPACES,number_format($val['amount'],2)."     ",0,0,'R');
	$pdf->Cell(30,$SPACES,$rem,0,1);
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $dt);
	$ctr++;
}
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".$numRec,0,1);
#########################################################################
$pdf->Output('unloadedlist.pdf','D');


function HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$maintEmpLoanObj->currentDateArt();
	$compName 		= $maintEmpLoanObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: UNLOADEDLIST";
	$repTitle 		= "UNLOADED AR EMPLOYEE LIST (".date('m/d/Y').")";
	$refNo    		= ""; 
	$dtlLabelDown   = " #     Cust. NO            Ref. No.                       Invoice No                Amount             Remarks";
	$dtlLabelDown2   = "";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(130,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(130,15,$repTitle);
	$pdf->Text(170,15,$refNo);
	$pdf->Text(10,23,$dtlLabelDown);
	$pdf->Text(10,26,$dtlLabelDown2);
	$pdf->Line(10,$pdf->GetY()+9,$TOTAL_WIDTH+30,$pdf->GetY()+9);
	$pdf->Line(10,$pdf->GetY()+18,$TOTAL_WIDTH+30,$pdf->GetY()+18);
	########################### F O O T E R  ################################
	$userId= $maintEmpLoanObj->getSeesionVars();
	$dispUser = $maintEmpLoanObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	
	$footerHt = 270; //////////////PORTRATE LETTER ONLY
	$pdf->Line(10,$footerHt-6,$TOTAL_WIDTH+6,$footerHt-6);
	$pdf->Text(10,$footerHt,$prntdBy);
	$pdf->Ln(22);
}
?>
