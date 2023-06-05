<?
##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$maintEmpObj = new inqEmpObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');
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
$pdf->currDate = "Run Date: ".$maintEmpObj->currentDateArt();
$pdf->compName = $maintEmpObj->getCompanyName($compCode);
$pdf->reppages = 1;
$pdf->repId    = "Report ID: EMPMF004";
$pdf->repTitle = "Employee Personel Information (Confidentials)";
$pdf->refNo    = "";
$pdf->dtlLabelUp    = "";
$pdf->dtlLabelDown  = "E M P L O Y E E   I N F O R M A T I O N";
$pdf->Header();
#########################################################################
$pdf->Ln(18);
#########################################################################
$dispEmp = $maintEmpObj->getUserInfo($compCode , $empNo,""); 
$dispDivDesc = $maintEmpObj->getDivDescArt($compCode, $dispEmp['empDiv']);
$dispDeptDesc = $maintEmpObj->getDeptDescArt($compCode, $dispEmp['empDiv'], $dispEmp['empDepCode']);
$dispSectDesc = $maintEmpObj->getSectDescArt($compCode, $dispEmp['empDiv'], $dispEmp['empDepCode'], $dispEmp['empSecCode']);

if ($dispEmp['empPayGrp']==1) { $grpName = "Group 1"; } 
if ($dispEmp['empPayGrp']==2) { $grpName = "Group 2"; }
$catName = $maintEmpObj->getEmpCatArt($sessionVars['compCode'], $dispEmp['empPayCat']);
$locName = $maintEmpObj->getEmpBranchArt($sessionVars['compCode'], $dispEmp['empLocCode']);
$brnchName = $maintEmpObj->getEmpBranchArt($sessionVars['compCode'], $dispEmp['empBrnCode']);
if ($dispEmp['empStat']=="RG") { $empStat = "Regular"; } 
if ($dispEmp['empStat']=="PR") { $empStat = "Probationary"; }
if ($dispEmp['empStat']=="CN") { $empStat = "Contractual"; }
$taxName = $maintEmpObj->getEmpTeuArt($dispEmp['empTeu']);
if ($dispEmp['empSex']=="M") { $gender = "Male"; } 
if ($dispEmp['empSex']=="F") { $gender = "Female"; }
$bankName = $maintEmpObj->getEmpBankArt($sessionVars['compCode'], $dispEmp['empBankCd']);
if ($dispEmp['empPayType']=="D") { $payStatus = "Daily"; } 
if ($dispEmp['empPayType']=="M") { $payStatus = "Monthly"; }
$pdf->Cell(40,$SPACES,"Employee Number :",0,0);
$pdf->Cell(60,$SPACES,$dispEmp['empNo'],0,0);
$pdf->Cell(50,$SPACES,"Branch              :",0,0);
$pdf->Cell(60,$SPACES,$brnchName['brnShortDesc'],0,1);

$pdf->Cell(40,$SPACES,"Name            :",0,0);
$pdf->Cell(60,$SPACES,$dispEmp['empLastName']." ".$dispEmp['empFirstName']." ".$dispEmp['empMidName'],0,0);
$pdf->Cell(50,$SPACES,"Position            :",0,0);
$pdf->Cell(60,$SPACES,$dispEmp['empPosDesc'],0,1);

$pdf->Cell(40,$SPACES,"Division        :",0,0);
$pdf->Cell(60,$SPACES,$dispDivDesc['deptDesc'],0,0);
$pdf->Cell(50,$SPACES,"Date Hired          :",0,0);
$pdf->Cell(60,$SPACES,$maintEmpObj->valDateArt($dispEmp['dateHired']),0,1);

$pdf->Cell(40,$SPACES,"Department      :",0,0);
$pdf->Cell(60,$SPACES,$dispDeptDesc['deptDesc'],0,0);
$pdf->Cell(50,$SPACES,"Employee Status     :",0,0);
$pdf->Cell(60,$SPACES,$empStat,0,1);

$pdf->Cell(40,$SPACES,"Section         :",0,0);
$pdf->Cell(60,$SPACES,$dispSectDesc['deptDesc'],0,0);
$pdf->Cell(50,$SPACES,"Regularization Date :",0,0);
$pdf->Cell(60,$SPACES,$maintEmpObj->valDateArt($dispEmp['dateReg']),0,1);

$pdf->Cell(40,$SPACES,"Group           :",0,0);
$pdf->Cell(60,$SPACES,$grpName,0,0);
$pdf->Cell(50,$SPACES,"Pay Status Type     :",0,0);
$pdf->Cell(60,$SPACES,$payStatus,0,1);

$pdf->Cell(40,$SPACES,"Category        :",0,0);
$pdf->Cell(60,$SPACES,$catName['payCatDesc'],0,0);
$pdf->Cell(50,$SPACES,"Location            :",0,0);
$pdf->Cell(60,$SPACES,$locName['brnShortDesc'],0,1);
#########################################################################
$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
$pdf->Ln(3);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"C O N F I D E N T I A L   I N F O R M A T I O N",0,1);
$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
#########################################################################
$pdf->Ln(3);
$pdf->Cell(40,$SPACES,"Monthly Rate    :",0,0);
$pdf->Cell(60,$SPACES,number_format($dispEmp['empMrate'],2),0,1);
$pdf->Cell(40,$SPACES,"Daily Rate      :",0,0);
$pdf->Cell(60,$SPACES,number_format($dispEmp['empDrate'],2),0,1);
$pdf->Cell(40,$SPACES,"Hourly Rate     :",0,0);
$pdf->Cell(60,$SPACES,number_format($dispEmp['empHrate'],2),0,1);
$pdf->Ln(3);
$pdf->Cell(5, 0, '', 0, 0);
$pdf->Cell(180, 0, '', 1, 1);
$pdf->Cell(5,3,"",0,0);
$pdf->Cell(25,3,"                             Start       End         Pay        Tax-              ",0,1);
$pdf->Cell(5,3,"",0,0);
$pdf->Cell(25,3,"Allowance                     Date       Date       Period      able        Amount",0,1);
$pdf->Cell(5, 0, '', 0, 0);
$pdf->Cell(180, 0, '', 1, 1);
$arrAllowList = $maintEmpObj->getEmpAllowListArt($compCode,$empNo);
foreach ($arrAllowList as $allowListVal){
	if ($allowListVal['allowSked']==1) $allowSked = "1st Period";
	if ($allowListVal['allowSked']==2) $allowSked = "2nd Period";
	if ($allowListVal['allowSked']==3) $allowSked = "Both Period";
	$pdf->Cell(5,$SPACES,"",0,0);
	$pdf->Cell(55,$SPACES,$allowListVal['allowDesc'],0,0);
	if ($allowListVal['allowPayTag']=="T") {
		$pdf->Cell(25,$SPACES,$maintEmpObj->valDateArt($allowListVal['allowStart']),0,0);
		$pdf->Cell(25,$SPACES,$maintEmpObj->valDateArt($allowListVal['allowEnd']),0,0);
	} else {
		$pdf->Cell(25,$SPACES,"",0,0);
		$pdf->Cell(25,$SPACES,"",0,0);
	}
	$pdf->Cell(25,$SPACES,$allowSked,0,0);
	if ($allowListVal['allowTaxTag']=="Y") {
		$pdf->Cell(20,$SPACES,"YES",0,0,'C');
	} else {
		$pdf->Cell(20,$SPACES,"NO",0,0,'C');
	}
	$pdf->Cell(25,$SPACES,number_format($allowListVal['allowAmt'],2),0,1,'R');
}
#########################################################################
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"*** End of Report ****",0,0,'C');
########################### F O O T E R  ################################
$userId= $maintEmpObj->getSeesionVars();
$dispUser = $maintEmpObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
$pdf->Footer();
#########################################################################
$pdf->Output();
?>
