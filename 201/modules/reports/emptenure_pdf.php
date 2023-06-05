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
	$qry4below = "SELECT count(empNo) as ctr,empDiv,empDepCode from tblEmpMast where datediff(CURDATE(),dateHired) between 0 and 4 $empDiv1 $empDept1 AND (compCode = '{$compCode}') and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}') group by empDiv,empDepCode   ";
	$qry5to9 = "SELECT count(empNo) as ctr,empDiv,empDepCode from tblEmpMast where datediff(CURDATE(),dateHired) between 5 and 10 $empDiv1 $empDept1 AND (compCode = '{$compCode}') and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}') group by empDiv,empDepCode ";
	$qry10up = "SELECT count(empNo) as ctr,empDiv,empDepCode from tblEmpMast where datediff(CURDATE(),dateHired) > 10 $empDiv1 $empDept1 AND (compCode = '{$compCode}') and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}') group by empDiv,empDepCode";
	$res4below = $inqTSObj->getArrRes($inqTSObj->execQry($qry4below));
	$res5to9 = $inqTSObj->getArrRes($inqTSObj->execQry($qry5to9));
	$res10up = $inqTSObj->getArrRes($inqTSObj->execQry($qry10up));
	$resDiv = $inqTSObj->getArrRes($inqTSObj->execQry($sqlDiv));
	$tot4below 	= 0;
	$tot5to9 	= 0;
	$tot10up 	= 0;
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
		$count4below 	= (int)GetValue($res4below,$val['deptCode'],$val['divCode']);
		$count5to9 		= (int)GetValue($res5to9,$val['deptCode'],$val['divCode']);
		$count10up 		= (int)GetValue($res10up,$val['deptCode'],$val['divCode']);
		$tot4below 	+= $count4below;
		$tot5to9 	+= $count5to9;
		$tot10up 	+= $count10up;
		$pdf->Cell(40,$SPACES,$count4below,0,0,'C');
		$pdf->Cell(25,$SPACES,$count5to9,0,0,'C');
		$pdf->Cell(38,$SPACES,$count10up,0,0,'C');
		$pdf->SetFont('Courier', 'B', '9');
		$pdf->Cell(18,$SPACES,($count10up + $count5to9 + $count4below),0,1,'C');
		$pdf->SetFont('Courier', '', '9');
	}	
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);

}
		$gtot = $tot4below + $tot5to9 + $tot10up;
		$tot4belowPer ="(". number_format(($tot4below/$gtot)*100) . "%)";
		$tot5to9Per = "(". number_format(($tot5to9/$gtot)*100) . "%)";
		$tot10upPer = "(". number_format(($tot10up/$gtot)*100) . "%)";
		$pdf->Ln(3);
		$pdf->SetFont('Courier', 'B', '9');
		$pdf->Cell(10,$SPACES,"",0,0);
		$pdf->Cell(65,$SPACES,"GRAND TOTAL",0,0);
		$pdf->Cell(40,$SPACES,$tot4below . $tot4belowPer,0,0,'C');
		$pdf->Cell(25,$SPACES,$tot5to9 . $tot5to9Per,0,0,'C');
		$pdf->Cell(38,$SPACES,$tot10up . $tot10upPer,0,0,'C');
		$pdf->Cell(18,$SPACES,($tot4below + $tot5to9 + $tot10up),0,1,'C');
		$pdf->SetFont('Courier', '', '9');
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
#########################################################################
$pdf->Output('EMPLOYEE_TENURE.pdf','D');


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
	$repId    		= "Report ID: EMPTENURE";
	$repTitle 		= "Employee Tenure $divDesc";
	$refNo    		= ""; 
	$dtlLabelDown   = "  Department                                 <5 Years        5-10 Years        10+ Years       Total";
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
