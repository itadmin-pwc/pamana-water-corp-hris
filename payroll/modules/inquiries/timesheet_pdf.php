<?
##################################################
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
$empNo    = $_GET['empNo'];
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
$pdf->currDate = "Run Date: ".$inqTSObj->currentDateArt();
$pdf->compName = $inqTSObj->getCompanyName($compCode);
$pdf->reppages = 1;
$pdf->repId    = "Report ID: EMPLN001";
$pdf->repTitle = "Employee Personel Information";
$pdf->refNo    = "";
$pdf->dtlLabelUp    = "";
$pdf->dtlLabelDown  = "E M P L O Y E E   I N F O R M A T I O N";
$pdf->Header();
#########################################################################
$pdf->Ln(18);
#########################################################################
$dispEmp = $inqTSObj->getUserInfo($compCode , $empNo,""); 
$dispDivDesc = $inqTSObj->getDivDescArt($compCode, $dispEmp['empDiv']);
$dispDeptDesc = $inqTSObj->getDeptDescArt($compCode, $dispEmp['empDiv'], $dispEmp['empDepCode']);
$dispSectDesc = $inqTSObj->getSectDescArt($compCode, $dispEmp['empDiv'], $dispEmp['empDepCode'], $dispEmp['empSecCode']);

if ($dispEmp['empPayGrp']==1) { $grpName = "Group 1"; } 
if ($dispEmp['empPayGrp']==2) { $grpName = "Group 2"; }
$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $dispEmp['empPayCat']);
$locName = $inqTSObj->getEmpBranchArt($sessionVars['compCode'], $dispEmp['empLocCode']);
$brnchName = $inqTSObj->getEmpBranchArt($sessionVars['compCode'], $dispEmp['empBrnCode']);
if ($dispEmp['empStat']=="RG") { $empStat = "Regular"; } 
if ($dispEmp['empStat']=="PR") { $empStat = "Probationary"; }
if ($dispEmp['empStat']=="CN") { $empStat = "Contractual"; }
$taxName = $inqTSObj->getEmpTeuArt($dispEmp['empTeu']);
if ($dispEmp['empSex']=="M") { $gender = "Male"; } 
if ($dispEmp['empSex']=="F") { $gender = "Female"; }
$bankName = $inqTSObj->getEmpBankArt($sessionVars['compCode'], $dispEmp['empBankCd']);
if ($dispEmp['empPayType']=="D") { $payStatus = "Daily"; } 
if ($dispEmp['empPayType']=="M") { $payStatus = "Monthly"; }
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Employee Number           :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empNo'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Name                      :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Division                  :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispDivDesc['deptDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Department                :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispDeptDesc['deptDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Section                   :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispSectDesc['deptDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Group                     :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$grpName,0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Category                  :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$catName['payCatDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Location                  :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$locName['brnShortDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Branch                    :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$brnchName['brnShortDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Position                  :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empPosDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Date Hired                :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$inqTSObj->valDateArt($dispEmp['dateHired']),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Employee Status           :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$empStat,0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Regularization Date       :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$inqTSObj->valDateArt($dispEmp['dateReg']),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Pay Status Type           :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$payStatus,0,1);
#########################################################################
$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
$pdf->Ln(3);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"P E R S O N A L   I N F O R M A T I O N",0,1);
$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
#########################################################################
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Address                   :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empAddr1'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Gender                    :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$gender,0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Birthday                  :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$inqTSObj->valDateArt($dispEmp['empBday']),0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Civil Status              :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$taxName['teuDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"TIN Number                :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empTin'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"SSS Number                :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empSssNo'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Pag-ibig Number           :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empPagibig'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Bank Name                 :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$bankName['bankDesc'],0,1);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,"Bank Account Number       :",0,0);
$pdf->Cell($TOTAL_WIDTH_3,$SPACES,$dispEmp['empAcctNo'],0,1);

#########################################################################
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"*** End of Report ****",0,0,'C');
########################### F O O T E R  ################################
$userId= $inqTSObj->getSeesionVars();
$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
$pdf->Footer();
#########################################################################
$pdf->Output();
?>
