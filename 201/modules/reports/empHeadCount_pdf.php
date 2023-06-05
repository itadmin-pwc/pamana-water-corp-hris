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
	
	$compCode 				= $_SESSION['company_code'];
	$inqTSObj->compCode     = $compCode;
	$inqTSObj->empDiv       = $_GET['empDiv'];
	$inqTSObj->empDept      = $_GET['empDept'];
	$empDiv        			= $_GET['empDiv'];
	$empDept       			= $_GET['empDept'];
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
	if ($empDiv>"" && $empDiv>0) {
		$empDiv1 = " AND (empDiv = '{$empDiv}')";
		$div 	 = " AND (divCode = '{$empDiv}')";
	} else {
		$empDiv1 = "";
	}
	if ($empDept>"" && $empDept>0) {
		$empDept1 	= " AND (empDepCode = '{$empDept}')";
		$dept 		= " AND (deptCode = '{$empDept}')";
	} else {
		$empDept1 = "";
	}
	$sqlDiv = "Select deptShortDesc,divCode,deptCode from tblDepartment where deptStat='A' and compCode='{$_SESSION['company_code']}' $div $dept and deptLevel <>3 order by divCode,deptCode";
	$resDiv = $inqTSObj->execQry($sqlDiv);
	$arrDiv = $inqTSObj->getArrRes($resDiv);
	$qryReg = "SELECT count(empNo) as ctr,empDiv,empDepCode from tblEmpMast where empStat= 'RG' $empDiv1 $empDept1 AND (compCode = '{$compCode}') group by empDiv,empDepCode   ";
	$qryProb = "SELECT count(empNo) as ctr,empDiv,empDepCode from tblEmpMast where empStat='PR' $empDiv1 $empDept1 AND (compCode = '{$compCode}') group by empDiv,empDepCode ";
	$qryCon = "SELECT count(empNo) as ctr,empDiv,empDepCode from tblEmpMast where empStat='CN' $empDiv1 $empDept1 AND (compCode = '{$compCode}') group by empDiv,empDepCode";
	$resReg = $inqTSObj->getArrRes($inqTSObj->execQry($qryReg));
	$resProb = $inqTSObj->getArrRes($inqTSObj->execQry($qryProb));
	$resCon = $inqTSObj->getArrRes($inqTSObj->execQry($qryCon));
	$resDiv = $inqTSObj->getArrRes($inqTSObj->execQry($sqlDiv));
	$totReg 	= 0;
	$totProb 	= 0;
	$totCon 	= 0;
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
############################### LOOPING THE PAGES ###########################
foreach ($resDiv as $val){
	if ($val['deptCode'] == 0) { 
		if ($_GET['empDiv'] == "0") {
			$pdf->SetFont('Courier', 'B', '9');
			$pdf->Cell(30,$SPACES,$val['deptShortDesc'],0,1);
			$pdf->SetFont('Courier', '', '9');
		}	
	} else {
		
		$pdf->Cell(10,$SPACES,"",0,0);
		$pdf->Cell(65,$SPACES,$val['deptShortDesc'],0,0);
		$countReg 		= (int)GetValue($resReg,$val['deptCode'],$val['divCode']);
		$countProb 		= (int)GetValue($resProb,$val['deptCode'],$val['divCode']);
		$countCon 		= (int)GetValue($resCon,$val['deptCode'],$val['divCode']);
		$totReg 	+= $countReg;
		$totProb 	+= $countProb;
		$totCon 	+= $countCon;
		$pdf->Cell(40,$SPACES,$countReg,0,0,'C');
		$pdf->Cell(25,$SPACES,$countProb,0,0,'C');
		$pdf->Cell(38,$SPACES,$countCon,0,0,'C');
		$pdf->SetFont('Courier', 'B', '9');
		$pdf->Cell(18,$SPACES,($countReg + $countProb + $countCon),0,1,'C');
		$pdf->SetFont('Courier', '', '9');
	}	
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);

}
		$gtot = $totReg + $totProb + $totCon;
		$totRegPer ="$totReg (". number_format(($totReg/$gtot)*100) . "%)";
		$totProbPer = "$totProb (". number_format(($totProb/$gtot)*100) . "%)";
		$totConPer = "$totCon (". number_format(($totCon/$gtot)*100) . "%)";
		$pdf->Ln(3);
		$pdf->SetFont('Courier', 'B', '9');
		$pdf->Cell(10,$SPACES,"",0,0);
		$pdf->Cell(65,$SPACES,"GRAND TOTAL",0,0);
		$pdf->Cell(40,$SPACES,$tot4below . $totRegPer,0,0,'C');
		$pdf->Cell(25,$SPACES,$tot5to9 . $totProbPer,0,0,'C');
		$pdf->Cell(38,$SPACES,$tot10up . $totConPer,0,0,'C');
		$pdf->Cell(18,$SPACES,($totReg + $totProb + $totCon),0,1,'C');
		$pdf->SetFont('Courier', '', '9');
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
#########################################################################
$pdf->Output('Employee Head Count.pdf','D');


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	if ($_GET['empDiv'] != "0") {
	
		$arrDiv = $inqTSObj->getDivDescArt($compCode, $_GET['empDiv']);
		$divDesc = "(".$arrDiv['deptShortDesc'].")";
	}
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: EMPHEADCOUNT";
	$repTitle 		= "Employee HEAD COUNT $divDesc";
	$refNo    		= ""; 
	$dtlLabelDown   = "  Department                                   Regular      Probationary      Contractual       Total";
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
function GetValue($Array,$DeptCode,$DivCode) {
	foreach($Array as $val) {
		if ($val['empDiv'] == $DivCode && $val['empDepCode'] == $DeptCode) {
			return $val['ctr'];
		}	
	}
}


?>
