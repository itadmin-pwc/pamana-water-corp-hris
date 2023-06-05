<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("bank_remit.obj.php");
	include('../../../includes/pdf/fpdf.php');
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$inqTSObj = new bankRemitObj($_SESSION,$_GET);
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$compCode = $_SESSION['company_code'];
	$inqTSObj->compCode     = $compCode;

############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
	$pdf = new FPDF('P', 'mm', 'LETTER');
	$pdf->SetFont('Courier', '', '10');
	$TOTAL_WIDTH   			= 200;
	$TOTAL_WIDTH_2 			= 53;
	$TOTAL_WIDTH_3 			= 88;
	$SPACES        			= 5;
	$pdf->TOTAL_WIDTH       = 200;
	$pdf->TOTAL_WIDTH_2     = 53;
	$pdf->TOTAL_WIDTH_3     = 88;
	$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
$payPdSlctd = $inqTSObj->getPayPeriod($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdPayable = '".date('Y-m-d',strtotime($_GET['payPd']))."'");

if ($payPdSlctd['pdStat']!="O") {
	$hist = "hist";
}
if ($_GET['cmbBranch']!=0) {
	$branch = " AND brnCode='{$_GET['cmbBranch']}'";
}

$qryBranch = " SELECT tblBranch.brnDesc, tblBranch.brnCode,coCtr
			  FROM tblBranch Where  brnStat='A' $branch and brnCode IN (Select empBrnCode from tblPayrollSummary$hist ps where ps.payGrp = '{$_SESSION['pay_group']}'
				  AND ps.payCat = '{$_SESSION['pay_category']}'
				  AND ps.pdYear = '{$payPdSlctd['pdYear']}'
				  AND ps.pdNumber = '{$payPdSlctd['pdNumber']}' 
				  AND ps.empBnkCd = '{$_GET['cmbBank']}') order by coCtr,brnDesc
";
$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($qryBranch));
 $qryGetPaySum = "SELECT ps.empBrnCode,ps.empNo,ps.netSalary+sprtAllow AS netSalary,emp.empLastName,emp.empMidName,emp.empFirstName,emp.empAcctNo,sprtAllow
					  FROM tblPayrollSummary$hist as ps LEFT JOIN tblEmpMast as emp
					  ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
				  WHERE ps.compCode = '{$_SESSION['company_code']}'
				  AND ps.payGrp = '{$_SESSION['pay_group']}'
				  AND ps.payCat = '{$_SESSION['pay_category']}'
				  AND ps.pdYear = '{$payPdSlctd['pdYear']}'
				  AND ps.pdNumber = '{$payPdSlctd['pdNumber']}' 
				  AND ps.empBnkCd = '{$_GET['cmbBank']}' 
				  ";
				  
if(trim($_GET['txtEmpNo']) != ""){
	$qryGetPaySum .= "AND ps.empNo = '{$_GET['txtEmpNo']}' ";
}
if(trim($_GET['txtEmpName']) != ""){
	if($_GET['nameType'] == 1){
		$qryGetPaySum .= "AND emp.empLastName LIKE '{$_GET['txtEmpName']}%' ";
	}
	if($_GET['nameType'] == 2){
		$qryGetPaySum .= "AND emp.empFirstName LIKE '{$_GET['txtEmpName']}%' ";
	}
	if($_GET['nameType'] == 3){
		$qryGetPaySum .= "AND emp.empMidName LIKE '{$_GET['txtEmpName']}%' ";
	}
}
if($_GET['cmbDiv'] != 0){
	$qryGetPaySum .= "AND ps.empDivCode = '{$_GET['cmbDiv']}%' ";
}
if($_GET['cmbDept'] != 0){
	$qryGetPaySum .= "AND ps.empDepCode = '{$_GET['cmbDept']}%' ";
}
if($_GET['cmbSect'] != 0){
	$qryGetPaySum .= "AND ps.empSecCode = '{$_GET['cmbSect']}%' ";
}
if($_GET['orderBy'] == 1){
 $qryGetPaySum .= "ORDER BY emp.empLastName ";
}
if($_GET['orderBy'] == 2){
 $qryGetPaySum .= "ORDER BY emp.empFirstName ";
}
if($_GET['orderBy'] == 3){
 $qryGetPaySum .= "ORDER BY ps.empNo ";
}
if($_GET['orderBy'] == 4){
 $qryGetPaySum .= "ORDER BY ps.empDepCode ";
}

$resGetPaySum = $inqTSObj->getArrRes($inqTSObj->execQry($qryGetPaySum)); 
 
	
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
$branchTot = $GrandTot = 0;
$br = "";
############################### LOOPING THE PAGES ###########################
		foreach($arrBranch as $valbranch) {
			$x = 1;
/*			if ($ctr !=1) {
				HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
			}*/
			$branchTot = 0;
				if ($br != $valbranch['coCtr'] && $br != "") {
					if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
					$pdf->SetFont('Courier', 'B', '10');
					$pdf->Cell(114,$SPACES,"",0,0,'R');
					$pdf->Cell(32,$SPACES,"GRAND TOTAL ",0,0,'L');
					$pdf->Cell(35,$SPACES,number_format(str_replace("-","",$GrandTot),2),0,1,'R');
					$pdf->SetFont('Courier', '', '10');
					$GrandTot = 0;
					HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
					
				}			
				$pdf->SetFont('Courier', 'B', '10');
				if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
				$pdf->Cell(12,$SPACES,$valbranch['brnDesc'],0,1);
				$pdf->SetFont('Courier', '', '10');
				foreach ($resGetPaySum as $val){
					if ($valbranch['brnCode'] == $val['empBrnCode']) {
						$name = "";
						if ($_GET['fname']==1) {
							$name = $val['empLastName'].",".$val['empFirstName'];
							$empNo = $val['empNo'];
						}
						if ($_GET['cmbBank']==3)
							$empNo = $val['empNo'];
							
						$pdf->Cell(10,$SPACES,"$x",0,0,"L");
						$pdf->Cell(62,$SPACES,$name,0,0);
						$pdf->Cell(42,$SPACES,$val['empAcctNo'],0,0);
						$pdf->Cell(32,$SPACES,$empNo,0,0);
						$branchTot += round($val['netSalary'],2);
						$pdf->Cell(35,$SPACES,number_format($val['netSalary'],2),0,1,'R');
						
						$ctr++;
						$x++;
						if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
					}
				}
				$GrandTot += $branchTot;
				$pdf->SetFont('Courier', 'B', '10');
				$pdf->Cell(114,$SPACES,"",0,0,'R');
				if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
				$pdf->Cell(32,$SPACES,"BRANCH TOTAL ",0,0,'L');
				$pdf->Cell(35,$SPACES,number_format(str_replace("-","",$branchTot),2),0,1,'R');
				$pdf->SetFont('Courier', '', '10');
				$br = $valbranch['coCtr'];
		}	
					if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
					$pdf->SetFont('Courier', 'B', '10');
					$pdf->Cell(114,$SPACES,"",0,0,'R');
					$pdf->Cell(32,$SPACES,"GRAND TOTAL ",0,0,'L');
					$pdf->Cell(35,$SPACES,number_format(str_replace("-","",$GrandTot),2),0,1,'R');
					$pdf->SetFont('Courier', '', '10');

#########################################################################
if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	switch($_GET['cmbBank']) {
		case 1:
			$bank = "MBTC";	
		break;
		case 2:
			$bank = "AUB";	
		break;
		case 3:
			$bank = "CASH";	
		break;
		case 4:
			$bank = "BDO";	
		break;
		case 5:
			$bank = "EBC";	
		break;
		case 6:
			$bank = "BOC";	
		break;
		
		
	}
	switch($_SESSION['pay_category']) {
		case 1:
			$payCat = "Executive";
		break;
		case 2:
			$payCat = "Confi";
		break;
		case 3:
			$payCat = "Non Confi";
		break;
		case 9:
			$payCat = "Resigned";
		break;
	}
	$empNo = "       ";
	$name = "             ";
	if ($_GET['fname']==1) {
		$name = 'EMPLOYEE NAME';
		$empNo = "EMP NO,";
		$spc=40;
	}
	if ($_GET['cmbBank']==3)
		$empNo = "EMP NO,";	
		
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: BANKREMIT";
	$repTitle 		= "BANK ADVICE - 	$bank ($payCat)";
	$refNo    		= ""; 
	$dtlLabelDown   = " #   $name                 ACCT NO.           $empNo                AMOUNT";
	$dtlLabelDown2  = " ";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(155,15,"Payroll Date: ".date("m/d/Y",strtotime($_GET['payPd'])));
	$pdf->Text(170,15,$refNo);
	$pdf->Text(10,22,$dtlLabelDown);
	$pdf->Text(10,25,$dtlLabelDown2);
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
