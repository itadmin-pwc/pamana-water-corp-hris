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
$maintEmpObj->empDiv        = $_GET['empDiv'];
$empDiv        				= $_GET['empDiv'];
################ GET TOTAL RECORDS ###############
$totRec = $maintEmpObj->getEmpTotalByDiv();
$numRec = $totRec['totRec'];
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
$pdf->TOTAL_WIDTH       = 200;
$pdf->TOTAL_WIDTH_2     = 100;
$pdf->TOTAL_WIDTH_3     = 66;
$pdf->SPACES	       	= 5;
HEADER_FOOTER($pdf, $maintEmpObj, $compCode);
############################ Q U E R Y ##################################
if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
$qryDivList = "SELECT TOP 100 PERCENT COUNT(*) AS totRec, tblDepartment.divCode, tblDepartment.deptDesc
			   FROM tblDepartment INNER JOIN
					tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv
			   WHERE (tblDepartment.compCode = '{$compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND 
					(tblEmpMast.compCode = '{$compCode}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') 
					$empDiv1 
			GROUP BY tblDepartment.divCode, tblDepartment.deptDesc
			ORDER BY tblDepartment.divCode, tblDepartment.deptDesc";
$resDivList = $maintEmpObj->execQry($qryDivList);
$arrDivList = $maintEmpObj->getArrRes($resDivList);
############################### LOOPING THE PAGES ###########################
$tempCode = "";
foreach ($arrDivList as $divListVal){
	//if ($tempCode!=$divListVal['empDiv']) {
	$pdf->Cell(40,$SPACES,$divListVal['deptDesc'],0,1);
	$arrCatList = $maintEmpObj->getEmpTotalByCat($divListVal['divCode']);
	foreach ($arrCatList as $catListVal){
		$arrCatList = $maintEmpObj->getEmpTotalByCat($divListVal['divCode']);
		$pdf->Cell(40,$SPACES,"",0,0);
		$pdf->Cell(72,$SPACES,$catListVal['payCatDesc'],0,0);
		
		$totGrp1 = $maintEmpObj->getEmpTotalByGrp($divListVal['divCode'],$catListVal['payCat'],1);
		$totGrp2 = $maintEmpObj->getEmpTotalByGrp($divListVal['divCode'],$catListVal['payCat'],2);
		$pdf->Cell(15,$SPACES,$totGrp1['totRec'],0,0,'C');
		$pdf->Cell(15,$SPACES,$totGrp2['totRec'],0,0,'C');
		$pdf->Cell(15,$SPACES,"",0,0,'C');
		$pdf->Cell(20,$SPACES,$catListVal['totRec'],0,1);
	}
	$grndTotGrp1 = $maintEmpObj->getEmpTotalByGrp($divListVal['divCode'],"",1);
	$grndTotGrp2 = $maintEmpObj->getEmpTotalByGrp($divListVal['divCode'],"",2);
	$pdf->SetFont('Courier', 'B', '10');
	$pdf->Cell(40,$SPACES,"SUB TOTAL",0,0);
	$pdf->Cell(72,$SPACES,"",0,0);
	$pdf->Cell(15,$SPACES,$grndTotGrp1['totRec'],0,0,'C');
	$pdf->Cell(15,$SPACES,$grndTotGrp2['totRec'],0,0,'C');
	$pdf->Cell(15,$SPACES,"",0,0,'C');
	$pdf->Cell(20,$SPACES,$divListVal['totRec'],0,1);
	$pdf->SetFont('Courier', '', '10');
	$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
	if ($pdf->GetY() > 230) HEADER_FOOTER($pdf, $maintEmpObj, $compCode);
}
#####################################################################
if ($pdf->GetY() > 240) HEADER_FOOTER($pdf, $maintEmpObj, $compCode);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Employees = ".$numRec,0,1);
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $maintEmpObj, $compCode) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$pdf->currDate 		= "Run Date: ".$maintEmpObj->currentDateArt();
	$pdf->compName 		= $maintEmpObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: EMPMF003";
	$pdf->repTitle 		= "Employee Statistical Report";
	$pdf->refNo    		= "";
	$pdf->dtlLabelUp    = " Division           Category                          Group  Group       TOTAL";
	$pdf->dtlLabelDown  = "                                                        1      2";
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $maintEmpObj->getSeesionVars();
	$dispUser = $maintEmpObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
