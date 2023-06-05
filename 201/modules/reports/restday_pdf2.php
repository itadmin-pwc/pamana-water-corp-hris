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
	$inqTSObj->compCode     = $compCode;
	$brnCode         		= $_GET['branch'];

################ GET TOTAL RECORDS ###############

############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
	$pdf = new FPDF('P', 'mm', 'LETTER');
	$pdf->SetFont('Courier', '', '9');
	$TOTAL_WIDTH   			= 200;
	$TOTAL_WIDTH_2 			= 53;
	$TOTAL_WIDTH_3 			= 88;
	$SPACES        			= 5;
	$pdf->TOTAL_WIDTH       = 200;
	$pdf->TOTAL_WIDTH_2     = 53;
	$pdf->TOTAL_WIDTH_3     = 88;
	$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
	if ($brnCode !="" && $brnCode !="0") {
		$brnCodeFilter = "$brnCode";
	}
	$sqlRD="Exec sp_EmpRD '{$_SESSION['company_code']}','$brnCodeFilter'";
/*	$sqlRD = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblEmpMast.empRestDay, 
             tblBranch.brnDesc
			 FROM tblEmpMast INNER JOIN
             tblBranch ON tblEmpMast.compCode = tblBranch.compCode AND tblEmpMast.empBrnCode = tblBranch.brnCode 
			 and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
			 $brnCodeFilter 
			 ORDER BY tblBranch.brnDesc, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName ";
	
*/
$arrRD = $inqTSObj->getArrRes($inqTSObj->execQry($sqlRD));
$ctr=1;
$GTot = 0;
$brnName="";
$empName="";
############################### LOOPING THE PAGES ###########################
foreach ($arrRD as $val){
	if ($brnName != $val['brnShortDesc']) {
			HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
		$pdf->SetFont('Courier','B','8'); 
		$pdf->Cell(25,$SPACES,$val['brnShortDesc'],0,0);
		$pdf->Ln();
		$pdf->SetFont('Courier','','8'); 
	}
	
	$brnName=$val['brnShortDesc'];
	$name = $val['empLastName'] . ", " . $val['empFirstName']." ".$val['empMidName'][0].".";			
	if ($empName != $name) {
		
		$pdf->Cell(17,$SPACES,$val['empNo'],0,0);
		$pdf->Cell(50,$SPACES,$name,0,0);
		$pdf->Cell(45,$SPACES,'Current',0,0);
		$pdf->Cell(60,$SPACES,str_replace(',',', ',$val['RDCur']),0,0);

	} else {
		$pdf->Cell(17,$SPACES,'',0,0);
		$pdf->Cell(50,$SPACES,'',0,0);
		$pdf->Cell(45,$SPACES,date('m/d/Y',strtotime($val['pdFrmDate'])).' - '.date('m/d/Y',strtotime($val['pdToDate'])),0,0);
		$pdf->Cell(60,$SPACES,str_replace(',',', ',$val['RDhist']),0,0);

	}
	$empName = $name;
	$pdf->Ln();
	$ctr++;
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);

}
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".($ctr-1),0,1);
#########################################################################
$pdf->Output('employee_rest_day.pdf','D');


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: EMPRD";
	$repTitle 		= "Rest Day Report";
	$refNo    		= ""; 
	$dtlLabelDown   = "  Emp. No.       Employee             Payroll Period                   Rest Day(s)      ";
	$dtlLabelDown2   = "";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(170,15,$refNo);
	$pdf->Text(10,23,$dtlLabelDown);
	########################### F O O T E R  ################################
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	
	$footerHt = 270; //////////////PORTRATE LETTER ONLY
	$pdf->Line(10,$footerHt-6,$TOTAL_WIDTH+6,$footerHt-6);
	$pdf->Text(10,$footerHt,$prntdBy);
	$pdf->Ln(22);
}


?>
