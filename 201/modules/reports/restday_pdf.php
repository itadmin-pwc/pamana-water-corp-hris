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
		$brnCodeFilter = " AND empBrnCode = '$brnCode'";
	}
	if ($_GET['group'] !="") {
		$group = " AND empPayGrp = '{$_GET['group']}'";
	}
		
	$sqlRD = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName,
		 	 tblEmpMast.empRestDay, tblBranch.brnDesc,empPayGrp,brnCode
			 FROM tblEmpMast 
			 INNER JOIN tblBranch ON tblEmpMast.compCode = tblBranch.compCode 
			 AND tblEmpMast.empBrnCode = tblBranch.brnCode 
			 AND empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' 
			 AND empNo='{$_SESSION['employee_number']}') 
			 AND empPayGrp<>0 $brnCodeFilter  
			 AND empNo not IN (Select empNo from tblLastPayEmp 
			 WHERE compCode='{$_SESSION['company_code']}' 
			 AND reHire<>'Y') 
			 AND tblEmpMast.employmentTag IN ('RG','PR','CN')
			 AND tblEmpMast.empStat = 'RG'
			 $group
			 ORDER BY tblBranch.brnDesc, empPayGrp,tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName ";
	$arrRD = $inqTSObj->getArrRes($inqTSObj->execQry($sqlRD));
$ctr=1;
$GTot = 0;
$brnName="";
$grp ="";
############################### LOOPING THE PAGES ###########################
foreach ($arrRD as $val){
	if ($brnName != $val['brnDesc']) {
		HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
		$pdf->SetFont('Courier','B','9'); 
		$pdf->Cell(25,$SPACES,$val['brnDesc'],0,0);
		$pdf->Ln();
		$pdf->SetFont('Courier','','9'); 
	}
	$brnName=$val['brnDesc'];
	$name = $val['empLastName'] . ", " . $val['empFirstName']." ".$val['empMidName'][0].".";			
	$pdf->Cell(25,$SPACES,$val['empNo'],0,0);
	$pdf->Cell(60,$SPACES,$name,0,0);
	$pdf->Cell(60,$SPACES,str_replace(',',', ',$val['empRestDay']),0,0);
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
$pdf->Output('employee_status.pdf','D');


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: EMPRD";
	if ($_GET['group'] !="") {
		$group = " Group {$_GET['group']}";
	}	
	$repTitle 		= "Rest Day Report $group";
	$refNo    		= ""; 
	$dtlLabelDown   = "  Emp. No.       Employee                     Rest Day(s)      ";
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
