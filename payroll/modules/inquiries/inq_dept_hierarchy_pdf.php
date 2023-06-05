<?
/*	Modified By 	: Genarra Jo - Ann S. Arong
	Date Modified 	: 09142009 2:38pm 
*/
################### INCLUDE FILE #################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_allow_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$inqEmpAllowObj = new inqEmpAllowObj();
$sessionVars = $inqEmpAllowObj->getSeesionVars();
$inqEmpAllowObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$inqEmpAllowObj->compCode      = $compCode;
$inqEmpAllowObj->empNo         = $_GET['empNo'];
$inqEmpAllowObj->empDiv        = $_GET['empDiv'];
$inqEmpAllowObj->empDept       = $_GET['empDept'];
$inqEmpAllowObj->empSect       = $_GET['empSect'];
$inqEmpAllowObj->allowType      = $_GET['allowType'];
$inqEmpAllowObj->groupType    = $_GET['groupType'];
################ GET TOTAL RECORDS ###############
//$resSearch = $inqEmpAllowObj->getEmpAllowInq();
//$numRec = count($resSearch);
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
############################ Q U E R Y ##################################

$qryAllowList = 	"SELECT * FROM tblDepartment
					WHERE (divCode > 0) AND (deptCode = 0) AND (sectCode = 0) AND (deptLevel = 1) AND (compCode = '$compCode') AND (deptStat='A')
					ORDER BY deptDesc ";
$resAllowList = $inqEmpAllowObj->execQry($qryAllowList);
$arrAllowList = $inqEmpAllowObj->getArrRes($resAllowList);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $_GET['orderBy']);
foreach ($arrAllowList as $allowListVal)
{
	$pdf->Cell(21,$SPACES,$allowListVal['deptDesc'],0,1);
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $_GET['orderBy']);
	
	$query_dept = 	"SELECT * FROM tblDepartment
					WHERE divCode='".$allowListVal['divCode']."' and (deptCode>0) and (sectCode=0) and (deptLevel=2) AND (compCode = '$compCode')
					AND (deptStat='A')
					ORDER BY deptDesc ";
	$rs_dept = $inqEmpAllowObj->execQry($query_dept);
	$row_dept = $inqEmpAllowObj->getArrRes($rs_dept);
	
	foreach ($row_dept as $list_dept)
	{
		$pdf->Cell(62,$SPACES,"",0,0);
		$pdf->Cell(21,$SPACES,$list_dept['deptDesc'],0,1);
		if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $_GET['orderBy']);
		
		$query_section = 	"SELECT * FROM tblDepartment
							WHERE divCode='".$allowListVal['divCode']."' and (deptCode='".$list_dept['deptCode']."') and (sectCode>0) and (deptLevel=3) AND (compCode = '$compCode')
							AND (deptStat='A')
							ORDER BY deptDesc ";
		$rs_section = $inqEmpAllowObj->execQry($query_section);
		$row_section = $inqEmpAllowObj->getArrRes($rs_section);
		
		foreach ($row_section as $list_section)
		{
			$pdf->Cell(83,$SPACES,"",0,0);
			$pdf->Cell(55,$SPACES,"",0,0);
			
			$pdf->Cell(21,$SPACES,$list_section['deptDesc'],0,1);
			if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $_GET['orderBy']);
		}
	}
	
	$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());
}
$pdf->Output();


function HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $orderBy) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqEmpAllowObj->currentDateArt();
	$pdf->compName 		= $inqEmpAllowObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: DEPLST001";
	$pdf->repTitle 		= "List of Departments";
	$pdf->refNo    		= "";
	
	$pdf->dtlLabelUp    = " Division                     Department                          Section";
		
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqEmpAllowObj->getSeesionVars();
	$dispUser = $inqEmpAllowObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}


/*
$STR1 = "ARTHUR MACADINI Genarra Jo - Ann S. Arong art mac ann arong jo";
$STR_LEN = strlen($STR1); ////41
$ARRAY_STR1 = split(" ",$STR1); 
$ARRAY_COUNT = count($ARRAY_STR1); /////8
$TOTAL_STR_LEN = 0;
$STR_UP = "";
$STR_DOWN = "";
for($ctr=0; $ctr<$ARRAY_COUNT; $ctr++) {
	
	$STR_LEN_ARRAY = strlen($ARRAY_STR1[$ctr]);
	$TOTAL_STR_LEN = $TOTAL_STR_LEN + $STR_LEN_ARRAY;
	if ($TOTAL_STR_LEN>10) {
		$STR_DOWN = $STR_DOWN . " " . $ARRAY_STR1[$ctr];
	
	} else {
		$STR_UP = $STR_UP . " " . $ARRAY_STR1[$ctr];
	} 
	
	
}
echo $STR_UP."<br>";
echo $STR_DOWN;
*/



?>
