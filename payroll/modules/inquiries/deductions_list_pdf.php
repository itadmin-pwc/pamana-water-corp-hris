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
$inqTSObj->compCode      = $compCode;
$inqTSObj->empNo         = $_GET['empNo'];
$inqTSObj->empName       = $_GET['empName'];
$inqTSObj->empDiv        = $_GET['empDiv'];
$inqTSObj->empDept       = $_GET['empDept'];
$inqTSObj->empSect       = $_GET['empSect'];
$inqTSObj->orderBy       = $_GET['orderBy'];

$empNo         = $_GET['empNo'];
$empName       = $_GET['empName'];
$empDiv        = $_GET['empDiv'];
$empDept       = $_GET['empDept'];
$empSect       = $_GET['empSect'];
$reportType 	= $_GET['reportType'];
if ($_SESSION['pay_group']==1) $groupName = "GROUP 1"; else $groupName = "GROUP 2"; 
$orderBy       = $_GET['orderBy'];
$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $_SESSION['pay_category']);
$payPd       = $_GET['payPd'];
$arrPayPd = $inqTSObj->getSlctdPd($compCode,$payPd);
################ GET TOTAL RECORDS ###############
$resSearch = $inqTSObj->getEmpInq();
$numRec = count($resSearch);
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
if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName, empDiv, empDepCode, empSecCode ";} 
if ($orderBy==2) {$orderBy1 = " ORDER BY empNo, empDiv, empDepCode, empSecCode ";} 
if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode, empLastName, empFirstName, empMidName ";}

$qryEmpList = "SELECT * FROM tblEmpMast
			   WHERE compCode = '{$sessionVars['compCode']}' AND 
				empStat NOT IN('RS','IN','TR') 
				AND empPayGrp = '{$_SESSION['pay_group']}'
				AND empPayCat = '{$_SESSION['pay_category']}'
				$empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $orderBy1 ";
$resEmpList = $inqTSObj->execQry($qryEmpList);
$arrEmpList = $inqTSObj->getArrRes($resEmpList);
#####################################################################
$tempCode = "";
$TOTAL_PAGE=1;
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $orderBy,$arrPayPd);
############################### LOOPING THE PAGES ###########################
foreach ($arrEmpList as $empListVal){
	$arrTotal = $inqTSObj->getDeductionsTotal($sessionVars['compCode'],$empListVal['empNo'],$arrPayPd['pdYear'],$arrPayPd['pdNumber']);
	$arrGrandTotal = $arrGrandTotal+$arrTotal['totAmt'];
	$div = $inqTSObj->getDivDescArt($sessionVars['compCode'], $empListVal['empDiv']);
	$dept = $inqTSObj->getDeptDescArt($sessionVars['compCode'], $empListVal['empDiv'], $empListVal['empDepCode']);
	$sect = $inqTSObj->getSectDescArt($sessionVars['compCode'], $empListVal['empDiv'], $empListVal['empDepCode'], $empListVal['empSecCode']);
	$nameInit = $empListVal['empFirstName'][0].".".$empListVal['empMidName'][0].".";
	if ($empListVal['empPayGrp']==1) { $grpName = "Group 1"; } 
	if ($empListVal['empPayGrp']==2) { $grpName = "Group 2"; }
	$catName = $inqTSObj->getEmpCatArt($sessionVars['compCode'], $empListVal['empPayCat']);
	$locName = $inqTSObj->getEmpBranchArt($sessionVars['compCode'], $empListVal['empLocCode']);
	$brnchName = $inqTSObj->getEmpBranchArt($sessionVars['compCode'], $empListVal['empBrnCode']);
	if ($_GET['orderBy'] < 3) {
		$pdf->Cell(22,$SPACES,$empListVal['empNo'],0,0);
		$pdf->Cell(38,$SPACES,$empListVal['empLastName']." ".$nameInit." (Hrly.Rate:".$empListVal['empHrate'].")",0,1);
		$tempCode=$empListVal['empNo'];
		if ($pdf->GetY() > 245) { HEADER_FOOTER($pdf, $inqTSObj, $compCode, $orderBy,$arrPayPd); $TOTAL_PAGE++; }
	} else {
		if ($tempCode!=$empListVal['empDiv'].$empListVal['empDepCode'].$empListVal['empSecCode']) {
			$pdf->Cell(55,$SPACES,$div['deptShortDesc']."/".$dept['deptShortDesc']."/".$sect['deptShortDesc'],0,1);
		}
		$pdf->Cell(5,$SPACES,"",0,0);
		$pdf->Cell(22,$SPACES,$empListVal['empNo'],0,0);
		$pdf->Cell(38,$SPACES,$empListVal['empLastName']." ".$nameInit." (Hrly.Rate:".$empListVal['empHrate'].")",0,1);
		if ($pdf->GetY() > 250) { HEADER_FOOTER($pdf, $inqTSObj, $compCode, $orderBy,$arrPayPd); $TOTAL_PAGE++; }
		$tempCode=$empListVal['empDiv'].$empListVal['empDepCode'].$empListVal['empSecCode'];
	}
	############### EARNINGS
	$qryTSList = "SELECT * FROM ".$reportType." 
				   WHERE compCode = '{$sessionVars['compCode']}' AND 
						empNo = '{$empListVal['empNo']}' AND 
						pdYear = '{$arrPayPd['pdYear']}' AND pdNumber <= '{$arrPayPd['pdNumber']}' 
						ORDER BY trnCode ASC ";
	$resTSList = $inqTSObj->execQry($qryTSList);
	$arrTSList = $inqTSObj->getArrRes($resTSList);
	$total_=0;
	foreach ($arrTSList as $TSVal){
		$total_ = $total_ + $TSVal['trnAmountD'];
		$pdf->Cell(95,3,"",0,0);
		$pdf->Cell(50,3,$inqTSObj->getTransTypeDescArt($sessionVars['compCode'],$TSVal['trnCode']),0,0);
		$pdf->Cell(35,3,$TSVal['trnAmountD'],0,1,'R');
	}
	########## TOTAL PER EMPLOYEE

	$pdf->SetFont('Courier', 'B', '10');
	$pdf->Cell(120,3,"",0,0);
	$pdf->Cell(25,3,"TOTAL: ",0,0,'R');
	$pdf->Cell(35,3,str_replace(",","",number_format($total_,2)),0,1,'R');
	$pdf->SetFont('Courier', '', '10');
	if ($_GET['orderBy'] < 3) {
	} else {
		######################## GRAND TOTAL ########################################################
		$empTotal = $inqTSObj->getEmpTotalByDept($sessionVars['compCode'], $empListVal['empDiv'], $empListVal['empDepCode'], $empListVal['empSecCode'],$groupType,$catType);
		if ($empTotal['refMax'] > "") { 
			$splitDesc = split("-",$empTotal['refMax']);
			if ($splitDesc[3]==$empListVal['empLastName'] && $splitDesc[4]==$empListVal['empFirstName'] && $splitDesc[5]==$empListVal['empMidName']) {
				$pdf->SetFont('Courier', 'B', '10');
				$pdf->Cell(55,$SPACES,"TOTAL: ".$empTotal['totRec']." Employee/s",0,1);
				$pdf->SetFont('Courier', '', '10');
			}
		}
		#############################################################################################
	}
	$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
}
#########################################################################
if ($pdf->GetY() > 245) { HEADER_FOOTER($pdf, $inqTSObj, $compCode, $orderBy,$arrPayPd); $TOTAL_PAGE++; }
########## GRAND TOTAL TIMESHEET
/*
$pdf->Ln(5);
$pdf->SetFont('Courier', 'B', '10');
$pdf->Cell(120,$SPACES,"",0,0);
$pdf->Cell(25,$SPACES,"GRAND TOTAL: ",0,0,'R');
$pdf->Cell(35,$SPACES,str_replace(",","",number_format($arrGrandTotal,2)),0,1,'R');
$pdf->SetFont('Courier', '', '10');
if ($pdf->GetY() > 245) { HEADER_FOOTER($pdf, $inqTSObj, $compCode, $orderBy,$arrPayPd); $TOTAL_PAGE++; }
*/
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Ln(5);
$pdf->Cell(50,$SPACES,"($numRec) Total Employees Process for ".$groupName." And Category ".$catName['payCatDesc'],0,1);
//$pdf->Cell(90,$SPACES,"GROUP    : ".$groupName,0,1);
//$pdf->Cell(50,$SPACES,"TOTAL PAGE/S     : ".$TOTAL_PAGE,0,1);
//$pdf->Cell(90,$SPACES,"CATEGORY : ".$catName['payCatDesc'],0,1);
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $orderBy,$arrPayPd) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$pdf->currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$pdf->compName 		= $inqTSObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: DEDUCT001";
	$pdf->repTitle 		= "(".($_GET['reportType']!='tblDeductions'?"Posted":"Pre Posted").")Current Employee Deductions (P. Period:".$inqTSObj->valDateArt($arrPayPd['pdPayable']).")";
	$pdf->refNo    		= "";
	$pdf->dtlLabelUp    = "                 ";
	$pdf->dtlLabelDown  = "EMPLOYEE                                     TRANSACTION                         AMT   ";	
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
 	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
