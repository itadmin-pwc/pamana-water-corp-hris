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
$pdf = new FPDF('L', 'mm', 'LEGAL');
$pdf->SetFont('Courier', '', '10');
$TOTAL_WIDTH   			= 310;
$TOTAL_WIDTH_2 			= 100;
$TOTAL_WIDTH_3 			= 66;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 0;
$pdf->TOTAL_WIDTH_2     = 100;
$pdf->TOTAL_WIDTH_3     = 66;
$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
$qryLoanList = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, 
                      tblEmpMast.empDiv, tblEmpMast.empDepCode, tblEmpMast.empSecCode, tblLoanType.lonTypeShortDesc, tblEmpLoans.lonRefNo, 
                      tblEmpLoans.lonAmt, tblEmpLoans.lonWidInterst, tblEmpLoans.lonStart, tblEmpLoans.lonEnd, tblEmpLoans.lonSked, 
                      tblEmpLoans.lonNoPaymnts, tblEmpLoans.lonDedAmt2, tblEmpLoans.lonPayments, tblEmpLoans.lonCurbal, tblEmpLoans.lonLastPay, 
                      tblEmpLoans.dateadded, tblEmpLoans.lonStat, tblEmpLoans.lonSeries, tblEmpLoans.compCode, tblEmpLoans.closedby, 
                      tblEmpLoans.closeddate, tblEmpMast.empPayGrp AS payGrp, tblEmpMast.empPayCat AS payCat
FROM         tblEmpLoans INNER JOIN
                      tblEmpMast ON tblEmpLoans.compCode = tblEmpMast.compCode AND tblEmpLoans.empNo = tblEmpMast.empNo INNER JOIN
                      tblLoanType ON tblEmpLoans.compCode = tblLoanType.compCode AND tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd
WHERE UpLoadTag=1 and dateAdded = '".date('m/d/Y')."' and lonStat='O' and tblEmpLoans.compCode = '$compCode'			  
ORDER BY tblEmpLoans.empNo,lonSeries";
$resLoanList = $common->execQry($qryLoanList);
$arrLoanList = $common->getArrRes($resLoanList);
$numRec = count($arrLoanList);
#####################################################################
HEADER_FOOTER($pdf, $common, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
############################### LOOPING THE PAGES ###########################
foreach ($arrLoanList as $val){
	unset($lastpay,$sked);
	switch($val['lonSked']) {
		case "1":
			$sked = "1st";
		break;
		case "2":
			$sked = "2nd";
		break;
		case "3":
			$sked = "Both";
		break;
	}
	if (!empty($val['lonLastPay'])) {
		$lastpay =  date('m/d/Y',strtotime($val['lonLastPay']));
	}
	if ($val['empNo'] != $empNo2) {
		$name = $val['empNo'] . " " . $val['empLastName']. " " . $val['empFirstName'][0] . ".";
		$empNox = $val['empNo'];
		$ch=1;
	} else {
		$name="";
		$empNox="";
		$ch=0;
	}
	$empNo2 = $val['empNo'];
	$pdf->Cell(52,$SPACES,$name,0,0);
	$pdf->Cell(29,$SPACES,$val['lonTypeShortDesc'],0,0);
	$pdf->Cell(22,$SPACES,$val['lonRefNo'],0,0);
	$pdf->Cell(23,$SPACES,number_format($val['lonAmt'],2),0,0,'R');
	$pdf->Cell(27,$SPACES,number_format($val['lonWidInterst'],2),0,0,'R');
	$pdf->Cell(30,$SPACES,date('m/d/Y',strtotime($val['lonStart'])),0,0,'C');
	$pdf->Cell(26,$SPACES,$sked,0,0,'C');
	$pdf->Cell(23,$SPACES,number_format($val['lonDedAmt2'],2),0,0,'R');
	$pdf->Cell(23,$SPACES,number_format($val['lonPayments'],2),0,0,'R');
	$pdf->Cell(24,$SPACES,number_format($val['lonCurbal'],2),0,0,'R');
	$pdf->Cell(26,$SPACES,$lastpay,0,0,'R');
	$pdf->Cell(26,$SPACES,date('m/d/Y',strtotime($val['dateadded'])),0,1,"R");
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $dt);
	$ctr++;
}
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".$numRec,0,1);
#########################################################################
$pdf->Output('daily_loans_list.pdf','D');


function HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$maintEmpLoanObj->currentDateArt();
	$compName 		= $maintEmpLoanObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: EMPDLYLN";
	$repTitle 		= "NEW LOANS DAILY REPORT (".date('m/d/Y').")";
	$refNo    		= ""; 
	$dtlLabelDown   = "Employee                 Loan Type     Ref. No.    Loan Amt   Loan Amt     Loan Start    Schedule    Ded. Per     Total    Cur. Bal.     Last     Setup Date";
	$dtlLabelDown2   = "                        	                                   (w/Interest)                              Sched.    Payments               Payment   ";
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
