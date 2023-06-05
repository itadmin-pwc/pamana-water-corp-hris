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
$resSearch = $maintEmpLoanObj->getEmpLoanInq();
$empInfo = $maintEmpLoanObj->getEmployeeList($sessionVars['compCode']," and empNo ='".$_GET['empNo']."'","");
$empLoanBal = $maintEmpLoanObj->getEmpLoanBal($sessionVars['compCode'],$_GET['empNo'],$_GET['lonTypeCd'],str_replace("_","#",$_GET['lonRefNo']));
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
$qryLoanList = "SELECT * FROM tblEmpLoansDtlHist 
			     WHERE compCode = '$compCode' AND empNo = '{$_GET['empNo']}' AND lonTypeCd = '{$_GET['lonTypeCd']}' AND lonRefNo = '".str_replace("_","#",$_GET['lonRefNo'])."' 
				 ORDER BY pdYear,pdNumber ASC";
$resLoanList = $maintEmpLoanObj->execQry($qryLoanList);
$arrLoanList = $maintEmpLoanObj->getArrRes($resLoanList);
$numRec = count($arrLoanList);
#####################################################################
$tempCode = "";
HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $empLoanBal, $empInfo);
$ctr=1;
$totpayments=0;
$empLoanAmt = $empLoanBal['lonWidInterst'];
$empLoanAmt = $empLoanBal['lonWidInterst'];
############################### LOOPING THE PAGES ###########################
foreach ($arrLoanList as $loanListVal){
	$empLoanAmt =round($empLoanAmt,2) - round($loanListVal['ActualAmt'],2);
	$pdDate = $maintEmpLoanObj->getPayPd($compCode,$loanListVal['pdYear'],$loanListVal['pdNumber'],$loanListVal['trnCat'],$loanListVal['trnGrp']);
	$pdf->Cell(10,$SPACES,$ctr.".",0,0);
	$pdf->Cell(50,$SPACES,date('M d, Y', strtotime($pdDate['pdPayable'])),0,0);
	$pdf->Cell(22,$SPACES,$loanListVal['trnAmountD'],0,0,"R");
	$pdf->Cell(48,$SPACES,$loanListVal['ActualAmt'],0,0,"R");
	$pdf->Cell(43,$SPACES,number_format($empLoanAmt,2),0,1,"R");
	$totpayments +=$loanListVal['ActualAmt'];
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $TOTAL_WIDTH, $empLoanBal, $empInfo);
	$ctr++;
}
$arrPd = $maintEmpLoanObj->getOpenPeriodwil();								
$arrCurDed = $maintEmpLoanObj->getUnPostedLoans($arrPd,$empLoanBal,$_GET['empNo']);
$numRec += count($arrCurDed);
	foreach($arrCurDed as $valDed) {
		$empLoanAmt =round($empLoanAmt,2) - round($valDed['ActualAmt'],2);
		$pdf->Cell(10,$SPACES,$ctr.".",0,0);
		$pdf->Cell(50,$SPACES,date('M d, Y', strtotime($arrPd['pdPayable'])).'(Unposted)',0,0);
		$pdf->Cell(22,$SPACES,$valDed['trnAmountD'],0,0,"R");
		$pdf->Cell(48,$SPACES,$valDed['ActualAmt'],0,0,"R");
		$pdf->Cell(43,$SPACES,number_format($empLoanAmt,2),0,1,"R");
		$totpayments = round($totpayments,2) + round($valDed['trnAmountD'],2);
	}
	$pdf->SetFont('Courier', 'B', '10');
	$pdf->Cell(48,$SPACES,"",0,0);
	$pdf->Cell(47,$SPACES,'Total',0,0,'R');
	if($empLoanAmt > $ActualPaymnets)
		echo number_format($empLoanAmount,2);
	else
	 	echo '0.00';
	$pdf->Cell(35,$SPACES,number_format($totpayments,2),0,1,"R");
	$pdf->SetFont('Courier', '', '10');
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
	$repTitle 		= "Employee Loans Deduction List";
	$refNo    		= ""; 
	$dtlLabelUp		= "                                   DEDUCTIONS BREAK DOWN";
	$dtlLabelDown   = "        Payroll Date         Ded. Sched            Actual Ded            Balance";
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
	$pdf->Text(10,30,"Start         :");
	$pdf->Text(10,35,"End           :");
	$pdf->Text(10,40,"Period of Ded.:");
	$pdf->Text(10,45,"Total Terms   :");
	$pdf->Text(109,25,"Loans Type   :");
	$pdf->Text(109,30,"Ref.No.      :");
	$pdf->Text(109,35,"Total Amt.   :");
	$pdf->Text(109,40,"Payments     :");
	$pdf->Text(109,45,"Current Bal. :");
	$pdf->Text(43,25,$empInfo['empNo']."-".$empInfo['empLastName'].", ".$empInfo['empFirstName'][0].".".$empInfo['empMidName'][0].".");
	$pdf->Text(43,30,$maintEmpLoanObj->valDateArt($empLoanBal['lonStart']));
	$pdf->Text(43,35,$maintEmpLoanObj->valDateArt($empLoanBal['lonEnd']));
				if ($empLoanBal['lonSked']==1) $sked ="1st";
				if ($empLoanBal['lonSked']==2) $sked ="2nd";
				if ($empLoanBal['lonSked']==3) $sked ="Both";
	$pdf->Text(43,40,$sked);
	$pdf->Text(43,45,$empLoanBal['lonNoPaymnts']);
	$pdf->Text(140,25,$maintEmpLoanObj->getLoanDesc($compCode,$empLoanBal['lonTypeCd']));
	$pdf->Text(140,30,$empLoanBal['lonRefNo']);
	$pdf->Text(140,35,number_format($empLoanBal['lonWidInterst'],2));
	$pdf->Text(140,40,number_format($empLoanBal['lonPayments'],2));
	$pdf->Text(140,45,number_format($empLoanBal['lonCurbal'],2));
	$pdf->Line(10,$pdf->GetY()+8,$TOTAL_WIDTH+6,$pdf->GetY()+8);
	$pdf->Text(10,53,$dtlLabelUp);
	$pdf->Text(10,58,$dtlLabelDown);
	$pdf->Line(10,$pdf->GetY()+40,$TOTAL_WIDTH+6,$pdf->GetY()+40);
	$pdf->Line(10,$pdf->GetY()+44,$TOTAL_WIDTH+6,$pdf->GetY()+44);
	########################### F O O T E R  ################################
	$userId= $maintEmpLoanObj->getSeesionVars();
	$dispUser = $maintEmpLoanObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	
	$footerHt = 270; //////////////PORTRATE LETTER ONLY
	$pdf->Line(10,$footerHt-6,$TOTAL_WIDTH+6,$footerHt-6);
	$pdf->Text(10,$footerHt,$prntdBy);
	$pdf->Ln(52);
}
?>
