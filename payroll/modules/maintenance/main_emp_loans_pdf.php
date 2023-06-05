<?
##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("main_emp_loans_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$maintEmpLoanObj = new maintEmpLoanObj();
$sessionVars = $maintEmpLoanObj->getSeesionVars();
$maintEmpLoanObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$empNo    = $_GET['empNo'];
$loanType = $_GET['loanType'];
##################################################
$pdf = new FPDF('P', 'mm', 'LETTER');
$pdf->SetFont('Courier', '', '10');
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
$TOTAL_WIDTH   			= 200;
$TOTAL_WIDTH_2 			= 100;
$TOTAL_WIDTH_3 			= 66;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 200;
$pdf->TOTAL_WIDTH_2     = 100;
$pdf->TOTAL_WIDTH_3     = 66;
$pdf->SPACES	       	= 5;
########################################################################
$loanTypeSplit = split("-",$loanType);
if ($loanType[0]=="1") $REPORT_TITLE = "SSS Employee Loans Report";
if ($loanType[0]=="2") $REPORT_TITLE = "Pag-ibig Employee Loans Report";
if ($loanType[0]=="3") $REPORT_TITLE = "Others Employee Loans Report";
########################################################################
$pdf->AddPage();
//$pdf->SetTextColor(0,0,255);
############################ H E A D E R ################################
$pdf->currDate = "Run Date: ".$maintEmpLoanObj->currentDateArt();
$pdf->compName = $maintEmpLoanObj->getCompanyName($compCode);
$pdf->reppages = 1;
$pdf->repId    = "Report ID: EMPLN001";
$pdf->repTitle = $REPORT_TITLE." as of ".$maintEmpLoanObj->currentDateNoTimeArt();
$pdf->refNo    = "";
$pdf->dtlLabelUp    = "";
$pdf->dtlLabelDown  = "                          E M P L O Y E E   I N F O R M A T I O N";
$pdf->Header();
#########################################################################
$pdf->Ln(18);
#########################################################################
$dispEmp = $maintEmpLoanObj->getUserInfo($compCode , $empNo,""); 
$dispDivDesc = $maintEmpLoanObj->getDivDescArt($compCode, $dispEmp['empDiv']);
$dispDeptDesc = $maintEmpLoanObj->getDeptDescArt($compCode, $dispEmp['empDiv'], $dispEmp['empDepCode']);
$dispSectDesc = $maintEmpLoanObj->getSectDescArt($compCode, $dispEmp['empDiv'], $dispEmp['empDepCode'], $dispEmp['empSecCode']);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Employee Number           :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empNo'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Employee Name             :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Employee Division         :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispDivDesc['deptDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Employee Department       :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispDeptDesc['deptDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Employee Section          :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispSectDesc['deptDesc'],0,1);
#########################################################################
$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
$pdf->Ln(3);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"                              L O A N   I N F O R M A T I O N",0,1);
$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
//$pdf->Ln(5);
#########################################################################
$loanInfo = $maintEmpLoanObj->getEmpLoanDataArt($compCode , $empNo, $loanTypeSplit[0],$loanTypeSplit[1]); 
$loanDesc = $maintEmpLoanObj->getLoanTypeDataArt($compCode,$loanTypeSplit[0]);

$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Loan Type                 :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$loanTypeSplit[0]." - ".$loanDesc['lonTypeDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Loan Reference Number     :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$loanInfo['lonRefNo'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Total Amount (Principal)  :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,number_format($loanInfo['lonAmt'],2),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Total Amount (Interest)   :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,number_format($loanInfo['lonWidInterst'],2),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Start Date                :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$maintEmpLoanObj->valDateArt($loanInfo['lonStart']),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"End Date                  :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$maintEmpLoanObj->valDateArt($loanInfo['lonEnd']),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Period of Deduction       :",0,0);
if ($loanInfo['lonSked']==1) $loanSked = "1st Period";
if ($loanInfo['lonSked']==2) $loanSked = "2nd Period";
if ($loanInfo['lonSked']==3) $loanSked = "Both";
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$loanSked,0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Total Terms               :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$loanInfo['lonNoPaymnts'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Deduction (Exclusive)     :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,number_format($loanInfo['lonDedAmt1'],2),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Deduction (Inclusive)     :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,number_format($loanInfo['lonDedAmt2'],2),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Loan Payments             :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,number_format($loanInfo['lonPayments']),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Number of Payments Made   :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$loanInfo['lonPaymentNo'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Loan Current Balance      :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,number_format($loanInfo['lonCurbal'],2),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Last Payments             :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$maintEmpLoanObj->valDateArt($loanInfo['lonLastPay']),0,1);
#########################################################################
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"*** End of Report ****",0,0,'C');
########################### F O O T E R  ################################
$userId= $maintEmpLoanObj->getSeesionVars();
$dispUser = $maintEmpLoanObj->getUserInfo($userId["compCode"] , $userId["empNo"],""); 
$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
$pdf->Footer();
#########################################################################
$pdf->Output();
?>
