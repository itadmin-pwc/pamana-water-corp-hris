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
	$inqTSObj->compCode      = $compCode;
	$inqTSObj->empNo         = $_GET['empNo'];
	$inqTSObj->empName       = $_GET['empName'];
	$inqTSObj->empDiv        = $_GET['empDiv'];
	$inqTSObj->empDept       = $_GET['empDept'];
	$inqTSObj->empSect       = $_GET['empSect'];
	$inqTSObj->orderBy       = $_GET['orderBy'];
	$empNo         			= $_GET['empNo'];
	$empName       			= $_GET['empName'];
	$empDiv        			= $_GET['empDiv'];
	$empDept       			= $_GET['empDept'];
	$empSect       			= $_GET['empSect'];
	$orderBy				= $_GET['orderBy'];
	$catName 				= $inqTSObj->getEmpCatArt($sessionVars['compCode'], $_SESSION['pay_category']);
	$payPd       			= $_GET['payPd'];
	$arrPayPd 				= $inqTSObj->getSlctdPd($compCode,$payPd);
	$dt						= $arrPayPd['pdPayable'];
	$loanType				= $_GET['loanType'];
	$branch 				= $_GET['branch'];
	$loc 					= $_GET['loc'];
################ GET TOTAL RECORDS ###############
	$resSearch = $inqTSObj->getEmpInq();

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
	if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
	//if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
	if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==2) {$orderBy1 = " ORDER BY empNo, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode, empLastName, empFirstName, empMidName ";}
	if (!$inqTSObj->getPeriod($payPd)) {
		$hist = "hist";
	}
	if ($loanType != "" && $loanType !=0) { $loanType = " AND tblEmpLoansDtl$hist.lonTypeCd like '$loanType%' ";} else {$loanType = "";}
	if ($branch != 0 && $empNo=="") {
		if ($loc == 1) {
			$branch = " AND empBrnCode = '$branch' AND empLocCode='0001'";
		} elseif ($loc == 2) {
			$branch = " AND empBrnCode = '$branch' AND empLocCode='$branch'";
		} else {
			$branch = " AND empBrnCode = '$branch'";
		}
	} else {
		$branch = "";
	}	
	 $sqlLoans = "Select *, tblLoanType.lonTypeShortDesc 
														FROM tblEmpLoansDtl$hist INNER JOIN tblLoanType ON 
														tblEmpLoansDtl$hist.compCode = tblLoanType.compCode 
														AND tblEmpLoansDtl$hist.lonTypeCd = tblLoanType.lonTypeCd 
														where pdNumber='{$arrPayPd['pdNumber']}' 
														AND pdYear='{$arrPayPd['pdYear']}' AND trnCat='{$_SESSION['pay_category']}' 
														AND trnGrp='{$_SESSION['pay_group']}' and dedTag IN ('Y','P')
														AND tblEmpLoansDtl$hist.compCode='{$_SESSION['company_code']}'  $empNo1 $loanType
														order by empNo";
	$arrLoans = $inqTSObj->getArrRes($inqTSObj->execQry($sqlLoans));
	
	$qryEmpList = "SELECT * FROM tblEmpMast
					WHERE compCode = '{$sessionVars['compCode']}'  
					AND empPayGrp = '{$_SESSION['pay_group']}'
					AND empNo IN 
				 				(Select empNo from tblPayrollSummary$hist where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )
					AND empNo IN (Select empNo from tblEmpLoansDtl$hist where pdNumber='{$arrPayPd['pdNumber']}' and pdYear='{$arrPayPd['pdYear']}' and trnCat='{$_SESSION['pay_category']}' and trnGrp='{$_SESSION['pay_group']}' and compCode='{$_SESSION['company_code']}' $empNo1 and dedTag='Y')
					$branch $empNo1 $empName1 $empDiv1 $empName1 $empDept1 $empSect1 $orderBy1 ";
	$resEmpList = $inqTSObj->execQry($qryEmpList);
	$arrEmpList = $inqTSObj->getArrRes($resEmpList);
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
$GTot = 0;
############################### LOOPING THE PAGES ###########################
foreach ($arrEmpList as $val){
	foreach ($arrLoans as $valLoans) {
		if ($valLoans['empNo'] == $val['empNo']) {
			if ($tempCode != $val['empNo']) { 
				$nameInit = $val['empLastName'] . " " . $val['empFirstName'][0].".".$val['empMidName'][0].".";			
				$pdf->Cell(8,$SPACES,$ctr,0,0,"C");
				$pdf->Cell(29,$SPACES,$val['empNo'],0,0);
				$pdf->Cell(35,$SPACES,$nameInit,0,0);
				$ctr++;
			} else {
				$pdf->Cell(8,$SPACES,"",0,0,"C");
				$pdf->Cell(29,$SPACES,"",0,0);
				$pdf->Cell(35,$SPACES,"",0,0);
			}	
			$pdf->Cell(25,$SPACES,$valLoans['lonTypeShortDesc'],0,0);
			$pdf->Cell(25,$SPACES,$valLoans['lonRefNo'],0,0);
			$pdf->Cell(27,$SPACES,number_format($valLoans['trnAmountD'],2),0,0,'R');
			$pdf->Cell(6,$SPACES,"",0,0,'R');
			$pdf->Cell(27,$SPACES,number_format($valLoans['ActualAmt'],2),0,1,'R');
			
			$tempCode=$val['empNo'];
			$GTot += (float)$valLoans['ActualAmt'];
			if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
			
		}
	}
}
			$pdf->SetFont('Courier', 'B', '9');
			$pdf->Cell(147,$SPACES,"GRAND TOTAL",0,0,'R');
			$pdf->Cell(35,$SPACES,number_format($GTot,2),0,1,'R');
			$pdf->SetFont('Courier', '', '9');
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".($ctr-1),0,1);
#########################################################################
$pdf->Output('gov_loans.pdf','D');


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$loan 	 	    = ($_GET['loanType']==1) ? "(SSS Loans)" : "(PAGIBIG LOANS)";
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: EMPGOVLOANS";
	$repTitle 		= "GOVERMENT LOANS REMITTANCE $loan";
	$refNo    		= ""; 
	$dtlLabelDown   = "  #  Emp. No.       Employee         Loan Type       Ref. No.       Ded. Schedule     Actual Ded.";
	$dtlLabelDown2   = "";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(170,15,"P. Date: ".date("m/d/Y",strtotime($dt)));
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
