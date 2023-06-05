<?
################### INCLUDE FILE #################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_loans_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$maintEmpLoanObj = new inqEmpLoanObj();
$sessionVars = $maintEmpLoanObj->getSeesionVars();
$maintEmpLoanObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
################ GET TOTAL RECORDS ###############
$empNo = $_GET['empNo'];
$from = $_GET['from'];
$to = $_GET['to'];
$empInfo = $maintEmpLoanObj->getUserInfo($sessionVars['compCode'],$empNo,"");
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
$pdf->TOTAL_WIDTH       = 0;
$pdf->TOTAL_WIDTH_2     = 100;
$pdf->TOTAL_WIDTH_3     = 66;
$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
if ($empNo != "") {
	$empNo = "and empNo='$empNo'";
} else {
	$empNo = "";
}
$qryLoanList = "Select * from tblMtdGovt where (convert(varchar(2),pdMonth) +'/28'+'/'+convert(varchar(4),pdYear)) between convert(datetime,'$from') and convert(datetime,'$to') and  compCode='{$_SESSION['company_code']}' $empNo  ORDER BY pdYear,pdMonth";

$qryLoanListHist = "Select * from tblMtdGovtHist where (convert(varchar(2),pdMonth) +'/28'+'/'+convert(varchar(4),pdYear)) between convert(datetime,'$from') and convert(datetime,'$to') and  compCode='{$_SESSION['company_code']}' $empNo  ORDER BY pdYear,pdMonth";
$resLoanListHist = $maintEmpLoanObj->execQry($qryLoanListHist);
$arrLoanListHist = $maintEmpLoanObj->getArrRes($resLoanListHist);
$resLoanList = $maintEmpLoanObj->execQry($qryLoanList);
$arrLoanList = $maintEmpLoanObj->getArrRes($resLoanList);

$numRec = count($arrLoanListHist) + count($arrLoanList);
#####################################################################
$tempCode = "";
HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $empLoanBal, $empInfo);
$ctr=1;
############################### LOOPING THE PAGES ###########################
foreach ($arrLoanListHist as $val){
	$pdf->Cell(29,$SPACES,date('M Y',strtotime($val['pdMonth'] . '/1/' . $val['pdYear'])),0,0);
	$pdf->Cell(26,$SPACES,number_format($val['sssEmp'],2),0,0);
	$pdf->Cell(22,$SPACES,number_format($val['sssEmplr'],2),0,0);
	$pdf->Cell(17,$SPACES,number_format($val['ec'],2),0,0);
	$pdf->Cell(26,$SPACES,number_format($val['phicEmp'],2),0,0);
	$pdf->Cell(30,$SPACES,number_format($val['phicEmplr'],2),0,0);
	$pdf->Cell(23,$SPACES,number_format($val['hdmfEmp'],2),0,0);
	$pdf->Cell(20,$SPACES,number_format($val['hdmfEmplr'],2),0,1,"R");
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $empLoanBal, $empInfo);
	$ctr++;
}

foreach ($arrLoanList as $val){
	$pdf->Cell(29,$SPACES,date('M Y',strtotime($val['pdMonth'] . '/1/' . $val['pdYear'])),0,0);
	$pdf->Cell(26,$SPACES,number_format($val['sssEmp'],2),0,0);
	$pdf->Cell(22,$SPACES,number_format($val['sssEmplr'],2),0,0);
	$pdf->Cell(17,$SPACES,number_format($val['ec'],2),0,0);
	$pdf->Cell(26,$SPACES,number_format($val['phicEmp'],2),0,0);
	$pdf->Cell(30,$SPACES,number_format($val['phicEmplr'],2),0,0);
	$pdf->Cell(23,$SPACES,number_format($val['hdmfEmp'],2),0,0);
	$pdf->Cell(20,$SPACES,number_format($val['hdmfEmplr'],2),0,1,"R");
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $empLoanBal, $empInfo);
	$ctr++;
}
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $empLoanBal, $empInfo);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".$numRec,0,1);
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $empLoanBal, $empInfo) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$maintEmpLoanObj->currentDateArt();
	$compName 		= $maintEmpLoanObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: EMPLND02";
	$repTitle 		= "Employee Goverment Contributions";
	$refNo    		= ""; 
	$dtlLabelUp		= "                        CONTRIBUTIONS BREAK DOWN";
	$dtlLabelDown   = "Month - Year    SSS      SSS(Emplr)   EC      PHIC      PHIC(Emplr)     HDMF      HDMF(Emplr)";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(170,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(170,15,$refNo);
	$pdf->Line(10,$pdf->GetY()+8,$TOTAL_WIDTH+6,$pdf->GetY()+8);
	$pdf->Text(10,25,"Employee      :");
	$pdf->Text(43,25,$empInfo['empNo']."-".$empInfo['empLastName'].", ".$empInfo['empFirstName'][0].".".$empInfo['empMidName'][0].".");
	$pdf->Text(43,30,$maintEmpLoanObj->valDateArt($empLoanBal['lonStart']));
	$pdf->Line(10,$pdf->GetY()+8,$TOTAL_WIDTH+6,$pdf->GetY()+8);
	$pdf->Text(10,33,$dtlLabelUp);
	$pdf->Text(10,38,$dtlLabelDown);
	$pdf->Line(10,$pdf->GetY()+20,$TOTAL_WIDTH+6,$pdf->GetY()+20);
	$pdf->Line(10,$pdf->GetY()+24,$TOTAL_WIDTH+6,$pdf->GetY()+24);
	########################### F O O T E R  ################################
	$userId= $maintEmpLoanObj->getSeesionVars();
	$dispUser = $maintEmpLoanObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	
	$footerHt = 270; //////////////PORTRATE LETTER ONLY
	$pdf->Line(10,$footerHt-6,$TOTAL_WIDTH+6,$footerHt-6);
	$pdf->Text(10,$footerHt,$prntdBy);
	$pdf->Ln(32);
}
?>
