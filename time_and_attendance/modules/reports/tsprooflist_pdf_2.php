<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("ts_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$inqTSObj->getOTs();
	$inqTSObj->getDeductions();
	$compCode = $_SESSION['company_code'];
################ GET TOTAL RECORDS ###############

############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
	$pdf = new FPDF('L', 'mm', 'LEGAL');
	$pdf->SetFont('Courier', '', '8');
	$TOTAL_WIDTH   			= 340;
	$TOTAL_WIDTH_2 			= 53;
	$TOTAL_WIDTH_3 			= 88;
	$SPACES        			= 5;
	$pdf->TOTAL_WIDTH       = 340;
	$pdf->TOTAL_WIDTH_2     = 53;
	$pdf->TOTAL_WIDTH_3     = 88;
	$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
	$arrEventLogs = $inqTSObj->TSProofList($_GET['empNo'],$_GET['branch'],$_GET['Grp']);
	
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
$GTot = 0;
############################### LOOPING THE PAGES ###########################
$tmp_empName = "";
$hrsOTLe8	= 0;
$hrsOTGt8	= 0;
$hrsNDLe8	= 0;
$hrsTardy	= 0;
$hrsUT		= 0;
$hrsWrk		= 0;
foreach ($arrEventLogs as $val){

	$arrOT = $inqTSObj->getempOTsDeds($val['empNo'],$val['tsDate'],'OT');
	$arrDed = $inqTSObj->getempOTsDeds($val['empNo'],$val['tsDate'],'Ded');
	$hrsWrked = 0;
	if ($val['hrsRequired']=8) {
		if ($val['hrsWorked']>=8) 
			$hrsWrked = 8;	
		else
			$hrsWrked = $val['hrsWorked'];
	} else {
		if ($val['hrsRequired']==3.5) {
			if ($val['hrsWorked']>=3.5) 
				$hrsWrked = 8;	
			else
				$hrsWrked = $val['hrsWorked']+4.5;
		} else {
			$hrsWrked = $val['hrsWorked'];
		}
	
	}	

	$hrsOTLe8	+= $arrOT['hrsOTLe8'];
	$hrsOTGt8	+= $arrOT['hrsOTGt8'];
	$hrsNDLe8	+= $arrOT['hrsNDLe8'];
	$hrsTardy	+= $arrDed['hrsTardy'];
	$hrsUT		+= $arrDed['hrsUT'];
	$hrsWrk		+= $hrsWrked;

	if ($tmp_empName!="" && $val['empName'] != $tmp_empName) {
			$pdf->SetFont('Courier', 'B', '8');
			$pdf->Cell(259,$SPACES,'Total',0,0,'C');
			$pdf->Cell(13,$SPACES,number_format($hrsOTLe8,2),0,0,'C');
			$pdf->Cell(13,$SPACES,number_format($hrsOTGt8,2),0,0,'C');
			$pdf->Cell(13,$SPACES,number_format($hrsNDLe8,2),0,0,'C');
			$pdf->Cell(13,$SPACES,number_format($hrsTardy,2),0,0,'C');
			$pdf->Cell(13,$SPACES,number_format($hrsUT,2),0,0,'C');
			$pdf->Cell(13,$SPACES,number_format($hrsWrk,2),0,0,'C');
			$pdf->Cell(1,$SPACES,"",0,1,'L');
			$pdf->SetFont('Courier', '', '8');
			$hrsOTLe8	= 0;
			$hrsOTGt8	= 0;
			$hrsNDLe8	= 0;
			$hrsTardy	= 0;
			$hrsUT		= 0;
			$hrsWrk		= 0;			
	}	
	if ($val['empName'] != $tmp_empName) {
		$pdf->Cell(40,$SPACES,$val['empName'] . " " . $val['empNo'],0,1,'L');
	} 	
	$pdf->Cell(24,$SPACES,date('m/d/Y',strtotime($val['tsDate'])),0,0);
	$pdf->Cell(24,$SPACES,DayType($val['dayType']),0,0);
	$pdf->Cell(15,$SPACES,$val['appTypeShortDesc'],0,0);
	$pdf->Cell(20,$SPACES,$val['shftTimeIn'],0,0,'C');
	$pdf->Cell(20,$SPACES,$val['shftLunchOut'],0,0,'C');
	$pdf->Cell(20,$SPACES,$val['shftLunchIn'],0,0,'C');
	$pdf->Cell(20,$SPACES,$val['shftTimeOut'],0,0,'C');
	$pdf->Cell(20,$SPACES,$val['timeIn'],0,0,'C');
	$pdf->Cell(20,$SPACES,$val['lunchOut'],0,0,'C');
	$pdf->Cell(20,$SPACES,$val['lunchIn'],0,0,'C');
	$pdf->Cell(20,$SPACES,$val['timeOut'],0,0,'C');
	$pdf->Cell(18,$SPACES,$val['otIn'],0,0,'C');
	$pdf->Cell(18,$SPACES,$val['otOut'],0,0,'C');
	$pdf->Cell(13,$SPACES,($arrOT['hrsOTLe8']==0)? "":$arrOT['hrsOTLe8'],0,0,'C');
	$pdf->Cell(13,$SPACES,($arrOT['hrsOTGt8']==0)? "":$arrOT['hrsOTGt8'],0,0,'C');
	$pdf->Cell(13,$SPACES,($arrOT['hrsNDLe8']==0)? "":$arrOT['hrsNDLe8'],0,0,'C');
	$pdf->Cell(13,$SPACES,($arrDed['hrsTardy']==0)? "":$arrDed['hrsTardy'],0,0,'C');
	$pdf->Cell(13,$SPACES,($arrDed['hrsUT']==0)? "":$arrDed['hrsUT'],0,0,'C');

	$pdf->Cell(13,$SPACES,number_format($hrsWrked,2),0,0,'C');
	$pdf->Cell(1,$SPACES,"",0,1,'L');

	$tmp_empName = $val['empName'];
	if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);

}
#########################################################################
if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->SetFont('Courier', 'B', '8');
$pdf->Cell(259,$SPACES,'Total',0,0,'C');
$pdf->Cell(13,$SPACES,number_format($hrsOTLe8,2),0,0,'C');
$pdf->Cell(13,$SPACES,number_format($hrsOTGt8,2),0,0,'C');
$pdf->Cell(13,$SPACES,number_format($hrsNDLe8,2),0,0,'C');
$pdf->Cell(13,$SPACES,number_format($hrsTardy,2),0,0,'C');
$pdf->Cell(13,$SPACES,number_format($hrsUT,2),0,0,'C');
$pdf->Cell(13,$SPACES,number_format($hrsWrk,2),0,0,'C');
$pdf->Cell(1,$SPACES,"",0,1,'L');
$pdf->SetFont('Courier', '', '8');

$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".($ctr-1),0,1);
#########################################################################
$pdf->Output('ts_prooflist.pdf','D');


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: TSPROOFLIST";
	$repTitle 		= "TIMESHEET PROOF LIST           $dt";
	$refNo    		= ""; 

	$dtlLabelDown   = "   DATE       DAY TYPE    APP. TYPE      SHIFT      SHIFT       SHIFT      SHIFT       ACTUAL      ACTUAL     ACTUAL      ACTUAL      OT IN     OT OUT     OT<8   OT>8    ND<8   TARDY   UT     HRS";
	$dtlLabelDown2   = "                                        TIME IN   LUNCH OUT   LUNCH IN    TIME OUT    TIME IN    LUNCH OUT   LUNCH IN    TIME OUT                                                               WORK";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(170,15,$refNo);
	$pdf->Text(10,22,$dtlLabelDown);
	$pdf->Text(10,25,$dtlLabelDown2);
	########################### F O O T E R  ################################
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	
	$footerHt = 208; //////////////PORTRATE LETTER ONLY
	$pdf->Line(10,$footerHt-6,$TOTAL_WIDTH+6,$footerHt-6);
	$pdf->Text(10,$footerHt,$prntdBy);
	$pdf->Text(160,$footerHt,'Approved By:');
	$pdf->Ln(22);
}
function DayType($dayType) {
	$desc = "";
	switch($dayType) {
		case '01':
			$desc = "Reg. Day";
		break;
		case '02':
			$desc = "Rest Day";
		break;
		case '03':
			$desc = "Legal Holiday";
		break;
		case '04':
			$desc = "Special Holiday";
		break;
		case '05':
			$desc = "LH-Rest Day";
		break;
		case '06':
			$desc = "SH-Rest Day";
		break;
	}
	return $desc;
}

?>
