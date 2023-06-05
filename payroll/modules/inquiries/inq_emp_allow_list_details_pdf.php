<?
################### INCLUDE FILE #################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_allow_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$maintEmpAllowObj = new inqEmpAllowObj();
$sessionVars = $maintEmpAllowObj->getSeesionVars();
$maintEmpAllowObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
################ GET TOTAL RECORDS ###############
$resSearch = $maintEmpAllowObj->getEmpAllowInq();
$empInfo = $maintEmpAllowObj->getUserInfo($sessionVars['compCode'],$_GET['empNo'],"");
$empAllow=$maintEmpAllowObj->getEmpAllow($_GET['empNo'],$_GET['allowCode']);
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
$qryAllowList = "SELECT * FROM tblAllowanceBrkDwnHst 
			     WHERE compCode = '{$sessionVars['compCode']}' AND empNo = '{$empNo}' AND allowCode = '{$allowCode}' 
				 ORDER BY pdYear,pdNumber ASC";
$resAllowList = $maintEmpAllowObj->execQry($qryAllowList);
$arrAllowList = $maintEmpAllowObj->getArrRes($resAllowList);
$numRec = count($arrAllowList);
#####################################################################
$tempCode = "";
HEADER_FOOTER($pdf, $maintEmpAllowObj, $compCode, $TOTAL_WIDTH, $empAllow, $empInfo);
$ctr=1;
############################### LOOPING THE PAGES ###########################
foreach ($arrAllowList as $allowListVal){
	$pdDate = $maintEmpAllowObj->getPayPd($compCode,$allowListVal['pdYear'],$allowListVal['pdNumber'],$_SESSION['pay_category'],$_SESSION['pay_group']);
	$pdf->Cell(50,$SPACES,"",0,0);
	$pdf->Cell(10,$SPACES,$ctr.".",0,0);
	$pdf->Cell(40,$SPACES,$maintEmpAllowObj->valDateArt($pdDate['pdPayable']),0,0);
	$pdf->Cell(22,$SPACES,number_format($allowListVal['allowAmt'],2),0,1,"R");
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpAllowObj, $compCode, $TOTAL_WIDTH, $empAllow, $empInfo);
	$ctr++;
}
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $maintEmpAllowObj, $compCode, $TOTAL_WIDTH, $empAllow, $empInfo);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".$numRec,0,1);
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $maintEmpAllowObj, $compCode, $TOTAL_WIDTH, $empAllow, $empInfo) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$maintEmpAllowObj->currentDateArt();
	$compName 		= $maintEmpAllowObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: EMPALD02";
	$repTitle 		= "Employee Allowances Earnings List";
	$refNo    		= ""; 
	$dtlLabelUp		= "                        EARNINGS BREAK DOWN";
	$dtlLabelDown   = "                             Payroll Date          Amount";
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
	$pdf->Text(109,25,"Allow Type   :");
	$pdf->Text(43,25,$empInfo['empNo']."-".$empInfo['empLastName'].", ".$empInfo['empFirstName'][0].".".$empInfo['empMidName'][0].".");
	$pdf->Text(43,30,$maintEmpAllowObj->valDateArt($empAllow['allowStart']));
	$pdf->Text(43,35,$maintEmpAllowObj->valDateArt($empAllow['allowEnd']));
				if ($empAllow['allowSked']==1) $sked ="1st";
				if ($empAllow['allowSked']==2) $sked ="2nd";
				if ($empAllow['allowSked']==3) $sked ="Both";
	$pdf->Text(43,40,$sked);
	$pdf->Text(140,25,$maintEmpAllowObj->getAllowDesc($compCode,$empAllow['allowCode']));
	$pdf->Line(10,$pdf->GetY()+8,$TOTAL_WIDTH+6,$pdf->GetY()+8);
	$pdf->Text(10,53,$dtlLabelUp);
	$pdf->Text(10,58,$dtlLabelDown);
	$pdf->Line(10,$pdf->GetY()+40,$TOTAL_WIDTH+6,$pdf->GetY()+40);
	$pdf->Line(10,$pdf->GetY()+44,$TOTAL_WIDTH+6,$pdf->GetY()+44);
	########################### F O O T E R  ################################
	$userId= $maintEmpAllowObj->getSeesionVars();
	$dispUser = $maintEmpAllowObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	
	$footerHt = 270; //////////////PORTRATE LETTER ONLY
	$pdf->Line(10,$footerHt-6,$TOTAL_WIDTH+6,$footerHt-6);
	$pdf->Text(10,$footerHt,$prntdBy);
	$pdf->Ln(52);
}
?>
