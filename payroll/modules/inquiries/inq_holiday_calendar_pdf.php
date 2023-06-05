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
$inqHolidayObj = new inqEmpAllowObj();
$sessionVars = $inqHolidayObj->getSeesionVars();
$inqHolidayObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
################ GET TOTAL RECORDS ###############
//$resSearch = $inqHolidayObj->getEmpAllowInq();
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

if($_GET['hol_date']!=0)
	$where = "where YEAR(holidaydate)='".$_GET['hol_date']."' AND compCode = '$compCode'";

	$query = 	"SELECT holidayDate,holidayDesc,dayType FROM tblHolidayCalendar $where Group By holidayDate,holidayDesc,dayType ORDER BY YEAR(holidayDate) desc";
$rs = $inqHolidayObj->execQry($query);
$row = $inqHolidayObj->getArrRes($rs);
#####################################################################
$tempCode = "";
############################### LOOPING THE PAGES ###########################
HEADER_FOOTER($pdf, $inqHolidayObj, $compCode, $_GET['orderBy']);
$tempCode = "";
foreach ($row as $holidays)
{
	if ($tempCode != date("Y", strtotime($holidays['holidayDate'])) && $tempCode!="") 
	{
		
		$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());
	}
	
	$pdf->Cell(60,$SPACES,$inqHolidayObj->valDateArt($holidays['holidayDate']),0,0);
	$pdf->Cell(77,$SPACES,$holidays['holidayDesc'],0,0);
	
	if($holidays['dayType']=='03')
		$hol_type = "LEGAL HOLIDAY";
	else
		$hol_type = "SPECIAL HOLIDAY";
		
	$pdf->Cell(21,$SPACES,$hol_type,0,1);
	
	$tempCode = date("Y", strtotime($holidays['holidayDate']));
	
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqHolidayObj, $compCode, $_GET['orderBy']);
	
}
$pdf->Output();


function HEADER_FOOTER($pdf, $inqHolidayObj, $compCode, $orderBy) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqHolidayObj->currentDateArt();
	$pdf->compName 		= $inqHolidayObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: HOLST001";
	$pdf->repTitle 		= "List of Holidays";
	$pdf->refNo    		= "";
	
	$pdf->dtlLabelUp    = "Holiday                      Holiday                             Holiday";
	$pdf->dtlLabelDown  = "Date                         Description                         Type";
		
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqHolidayObj->getSeesionVars();
	$dispUser = $inqHolidayObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}





?>
