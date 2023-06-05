<?
################### INCLUDE FILE #################
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
$maintEmpObj->compCode      = $compCode;
$maintEmpObj->empNo         = $_GET['empNo'];
$maintEmpObj->empDiv        = $_GET['empDiv'];
$maintEmpObj->empDept       = $_GET['empDept'];
$maintEmpObj->empSect       = $_GET['empSect'];
$maintEmpObj->groupType     = $_GET['groupType'];
$maintEmpObj->orderBy       = $_GET['orderBy'];
$maintEmpObj->catType       = $_GET['catType'];
$empNo         = $_GET['empNo'];
$empDiv        = $_GET['empDiv'];
$empDept       = $_GET['empDept'];
$empSect       = $_GET['empSect'];
$groupType     = $_GET['groupType'];
$orderBy       = $_GET['orderBy'];
$catType       = $_GET['catType'];
################ GET TOTAL RECORDS ###############
$resSearch = $maintEmpObj->getEmpInq();
$numRec = count($resSearch);
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$pdf = new FPDF('L', 'mm', 'LETTER');
$pdf->SetFont('Courier', '', '10');
$TOTAL_WIDTH   			= 265;
$TOTAL_WIDTH_2 			= 132;
$TOTAL_WIDTH_3 			= 88;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 265;
$pdf->TOTAL_WIDTH_2     = 132;
$pdf->TOTAL_WIDTH_3     = 88;
$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
if ($groupType<3) {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName, empDiv, empDepCode, empSecCode ";} 
if ($orderBy==2) {$orderBy1 = " ORDER BY empNo, empDiv, empDepCode, empSecCode ";} 
if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode, empLastName, empFirstName, empMidName ";}
if ($catType>0) {$catType1 = " AND (empPayCat = '{$catType}')";} else {$catType1 = "";}
$qryEmpList = "SELECT * FROM tblEmpMast
			   WHERE compCode = '{$sessionVars['compCode']}' AND 
			   		empStat NOT IN('RS','IN','TR') 
					$empNo1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";
$resEmpList = $maintEmpObj->execQry($qryEmpList);
$arrEmpList = $maintEmpObj->getArrRes($resEmpList);
#####################################################################
$tempCode = "";
HEADER_FOOTER($pdf, $maintEmpObj, $compCode, $orderBy);
############################### LOOPING THE PAGES ###########################
foreach ($arrEmpList as $empListVal){
	$div = $maintEmpObj->getDivDescArt($sessionVars['compCode'], $empListVal['empDiv']);
	$dept = $maintEmpObj->getDeptDescArt($sessionVars['compCode'], $empListVal['empDiv'], $empListVal['empDepCode']);
	$sect = $maintEmpObj->getSectDescArt($sessionVars['compCode'], $empListVal['empDiv'], $empListVal['empDepCode'], $empListVal['empSecCode']);
	$nameInit = $empListVal['empFirstName'][0].".".$empListVal['empMidName'][0].".";
	if ($empListVal['empPayGrp']==1) { $grpName = "Group 1"; } 
	if ($empListVal['empPayGrp']==2) { $grpName = "Group 2"; }
	$catName = $maintEmpObj->getEmpCatArt($sessionVars['compCode'], $empListVal['empPayCat']);
	$locName = $maintEmpObj->getEmpBranchArt($sessionVars['compCode'], $empListVal['empLocCode']);
	$brnchName = $maintEmpObj->getEmpBranchArt($sessionVars['compCode'], $empListVal['empBrnCode']);
	if ($_GET['orderBy'] < 3) {
		$pdf->Cell(21,$SPACES,$empListVal['empNo'],0,0);
		$pdf->Cell(40,$SPACES,$empListVal['empLastName']." ".$nameInit,0,0);
		$pdf->Cell(55,$SPACES,$div['deptShortDesc']."/".$dept['deptShortDesc']."/".$sect['deptShortDesc'],0,0);
		$pdf->Cell(19,$SPACES,$grpName,0,0);
		$pdf->Cell(55,$SPACES,$catName['payCatDesc'],0,0);
		$pdf->Cell(20,$SPACES,$locName['brnShortDesc'],0,0);
		$pdf->Cell(20,$SPACES,$brnchName['brnShortDesc'],0,0);
		$pdf->Cell(20,$SPACES,$empListVal['empPosDesc'],0,1);
		$tempCode=$empListVal['empNo'];
		if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $maintEmpObj, $compCode, $orderBy);
	} else {
		if ($tempCode!=$empListVal['empDiv'].$empListVal['empDepCode'].$empListVal['empSecCode']) {
			$pdf->Cell(55,$SPACES,$div['deptShortDesc']."/".$dept['deptShortDesc']."/".$sect['deptShortDesc'],0,0);
		} else {
			$pdf->Cell(55,$SPACES,"",0,0);
		}
		$pdf->Cell(21,$SPACES,$empListVal['empNo'],0,0);
		$pdf->Cell(40,$SPACES,$empListVal['empLastName']." ".$nameInit,0,0);
		$pdf->Cell(19,$SPACES,$grpName,0,0);
		$pdf->Cell(55,$SPACES,$catName['payCatDesc'],0,0);
		$pdf->Cell(20,$SPACES,$locName['brnShortDesc'],0,0);
		$pdf->Cell(20,$SPACES,$brnchName['brnShortDesc'],0,0);
		$pdf->Cell(20,$SPACES,$empListVal['empPosDesc'],0,1);
		if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $maintEmpObj, $compCode, $orderBy);
		######################## GRAND TOTAL ########################################################
		$empTotal = $maintEmpObj->getEmpTotalByDept($sessionVars['compCode'], $empListVal['empDiv'], $empListVal['empDepCode'], $empListVal['empSecCode'],$groupType,$catType);
		if ($empTotal['refMax'] > "") { 
			$splitDesc = split("-",$empTotal['refMax']);
			if ($splitDesc[3]==$empListVal['empLastName'] && $splitDesc[4]==$empListVal['empFirstName'] && $splitDesc[5]==$empListVal['empMidName']) {
				$pdf->SetFont('Courier', 'B', '10');
				$pdf->Cell(55,$SPACES,"TOTAL: ".$empTotal['totRec']." record/s",0,1);
				$pdf->SetFont('Courier', '', '10');
				$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
			}
		}
		#############################################################################################
		$tempCode=$empListVal['empDiv'].$empListVal['empDepCode'].$empListVal['empSecCode'];
	}
}
#########################################################################
if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $maintEmpObj, $compCode, $orderBy);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".$numRec,0,1);
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $maintEmpObj, $compCode, $orderBy) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$pdf->currDate 		= "Run Date: ".$maintEmpObj->currentDateArt();
	$pdf->compName 		= $maintEmpObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: EMPMF002";
	$pdf->repTitle 		= "Employee List Report";
	$pdf->refNo    		= "";
	if ($orderBy<3) {
		$pdf->dtlLabelUp    = " Emp.No.   Employee Name      Department                Group   Category                  Location  Branch   Position";
		$pdf->dtlLabelDown  = "";
	} else {
		$pdf->dtlLabelUp    = " Department                Emp.No.   Employee Name      Group   Category                  Location  Branch   Position";
		$pdf->dtlLabelDown  = "";
	}
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $maintEmpObj->getSeesionVars();
	$dispUser = $maintEmpObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
