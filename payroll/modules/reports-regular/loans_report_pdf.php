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
	$branch					= $_GET['branch'];
	$loc					= $_GET['loc'];
################ GET TOTAL RECORDS ###############
	$resSearch = $inqTSObj->getEmpInq();

############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
	$pdf = new FPDF('L', 'mm', 'LETTER');
	$pdf->SetFont('Courier', '', '9');
	$TOTAL_WIDTH   			= 265;
	$TOTAL_WIDTH_2 			= 53;
	$TOTAL_WIDTH_3 			= 88;
	$SPACES        			= 5;
	$pdf->TOTAL_WIDTH       = 265;
	$pdf->TOTAL_WIDTH_2     = 53;
	$pdf->TOTAL_WIDTH_3     = 88;
	$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
	if ($empNo>"") {$empNo1 = " AND (tblEmpMast.empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
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
	$sqlBranch = "Select brnCode,brnShortDesc from tblBranch where compCode='{$_SESSION['company_code']}'";
	$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($sqlBranch));;
	$lonType=($_GET['lonType'] !="" && $_GET['lonType'] !=0) ? $_GET['lonType']:"";
	$lonTypeFilter = ($lonType !="") ? " AND tblEmpLoansDtl$hist.lonTypeCd='$lonType'":"";
	if ($_GET['lonType'] != 302) {
		 $sqlLoans = "SELECT     tblLoanType.lonTypeShortDesc, tblEmpLoansDtl$hist.*, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, 
						  tblEmpMast.empBrnCode, tblEmpLoans.lonWidInterst, tblEmpLoans.lonCurbal
						FROM         tblEmpLoansDtl$hist INNER JOIN
						  tblLoanType ON tblEmpLoansDtl$hist.lonTypeCd = tblLoanType.lonTypeCd INNER JOIN
						  tblEmpMast ON tblEmpLoansDtl$hist.compCode = tblEmpMast.compCode AND tblEmpLoansDtl$hist.empNo = tblEmpMast.empNo INNER JOIN
						  tblEmpLoans ON tblEmpLoansDtl$hist.compCode = tblEmpLoans.compCode AND tblEmpLoansDtl$hist.empNo = tblEmpLoans.empNo AND 
						  tblEmpLoansDtl$hist.lonTypeCd = tblEmpLoans.lonTypeCd AND tblEmpLoansDtl$hist.lonRefNo = tblEmpLoans.lonRefNo
						WHERE     pdNumber='{$arrPayPd['pdNumber']}' 
							AND pdYear='{$arrPayPd['pdYear']}' AND trnCat='{$_SESSION['pay_category']}' 
							AND trnGrp='{$_SESSION['pay_group']}' and dedTag IN ('Y','P')
							AND (tblEmpLoansDtl$hist.compCode = '{$_SESSION['company_code']}')
							$empNo1 $lonTypeFilter $branch $empName1 $empDiv1 $empName1 $empDept1 $empSect1 
						ORDER BY tblEmpLoansDtl$hist.lonTypeCd,tblEmpLoansDtl$hist.lonRefNo, tblEmpMast.empBrnCode, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName	";
	} else {
		 $sqlLoans = "SELECT     tblLoanType.lonTypeShortDesc, tblEmpLoansDtl$hist.*, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, 
						  tblEmpMast.empBrnCode, tblEmpLoans.lonWidInterst, tblEmpLoans.lonCurbal
						FROM         tblEmpLoansDtl$hist INNER JOIN
						  tblLoanType ON tblEmpLoansDtl$hist.lonTypeCd = tblLoanType.lonTypeCd INNER JOIN
						  tblEmpMast ON tblEmpLoansDtl$hist.compCode = tblEmpMast.compCode AND tblEmpLoansDtl$hist.empNo = tblEmpMast.empNo INNER JOIN
						  tblEmpLoans ON tblEmpLoansDtl$hist.compCode = tblEmpLoans.compCode AND tblEmpLoansDtl$hist.empNo = tblEmpLoans.empNo AND 
						  tblEmpLoansDtl$hist.lonTypeCd = tblEmpLoans.lonTypeCd AND tblEmpLoansDtl$hist.lonRefNo = tblEmpLoans.lonRefNo
						WHERE     pdNumber='{$arrPayPd['pdNumber']}' 
							AND pdYear='{$arrPayPd['pdYear']}' AND trnCat='{$_SESSION['pay_category']}' 
							AND trnGrp='{$_SESSION['pay_group']}' and dedTag IN ('Y','P')
							AND (tblEmpLoansDtl$hist.compCode = '{$_SESSION['company_code']}')
							$empNo1 $lonTypeFilter $branch $empName1 $empDiv1 $empName1 $empDept1 $empSect1 
						ORDER BY tblEmpLoansDtl$hist.lonTypeCd, tblEmpMast.empBrnCode, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName	";
	
	}														
	$arrLoans = $inqTSObj->getArrRes($inqTSObj->execQry($sqlLoans));
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
$GTot = 0;
############################### LOOPING THE PAGES ###########################
$brnCode = "";
$refNo = "";
$tot = 0;
$totLoanAmt = 0;
$totCurBal = 0;
$GtotLoanAmt = 0;
$GtotCurBal = 0;
foreach ($arrLoans as $valLoans) {
		if ($_GET['lonType'] != 302) {
			if ($refNo != $valLoans['lonRefNo']) { 
				if ($refNo != "") {
				
					$pdf->SetFont('Courier', 'B', '9');
					$pdf->Cell(120,$SPACES,"TOTAL",0,0,'R');
					$pdf->Cell(32,$SPACES,number_format($totLoanAmt,2),0,0,'R');
					$pdf->Cell(31,$SPACES,number_format($totCurBal,2),0,0,'R');
					$pdf->Cell(73,$SPACES,number_format($tot,2),0,1,'R');
					$pdf->SetFont('Courier', '', '9');
					$tot = 0;	
					$totLoanAmt = 0;
					$totCurBal = 0;
					
				}
			} 
		} 
		if ($brnCode != $valLoans['empBrnCode']) {
				if ($brnCode != "" && $_GET['lonType'] == 302) {
					$pdf->SetFont('Courier', 'B', '9');
					$pdf->Cell(120,$SPACES,"TOTAL",0,0,'R');
					$pdf->Cell(32,$SPACES,number_format($totLoanAmt,2),0,0,'R');
					$pdf->Cell(31,$SPACES,number_format($totCurBal,2),0,0,'R');
					$pdf->Cell(73,$SPACES,number_format($tot,2),0,1,'R');
					$pdf->SetFont('Courier', '', '9');
					$tot = 0;	
					$totLoanAmt = 0;
					$totCurBal = 0;
					
				}

			foreach($arrBranch as $valBranch) {
				if ($valLoans['empBrnCode'] == $valBranch['brnCode']) {
					$pdf->SetFont('Courier', 'B', '9');
					$pdf->Cell(8,$SPACES,$valBranch['brnShortDesc'],0,0,"L");
					$pdf->SetFont('Courier', '', '9');
					$pdf->Ln();
				}
			}
		}
		$tot 		+= (float)$valLoans['ActualAmt'];
		$totLoanAmt += (float)$valLoans['lonWidInterst'];
		$totCurBal 	+= (float)$valLoans['lonCurbal'];
		
		$brnCode = $valLoans['empBrnCode'];
		$refNo  = $valLoans['lonRefNo'];
		$nameInit = $valLoans['empLastName'] . ", " . $valLoans['empFirstName']." ".$valLoans['empMidName'][0].".";
			$pdf->Cell(20,$SPACES,$valLoans['empNo'],0,0);
			$pdf->Cell(55,$SPACES,$nameInit,0,0);
			$pdf->Cell(25,$SPACES,$valLoans['lonTypeShortDesc'],0,0);
			$pdf->Cell(25,$SPACES,$valLoans['lonRefNo'],0,0);
			$pdf->Cell(27,$SPACES,number_format($valLoans['lonWidInterst'],2),0,0,'R');
			$pdf->Cell(31,$SPACES,number_format($valLoans['lonCurbal'],2),0,0,'R');
			$pdf->Cell(10,$SPACES,"",0,0,'R');
			$pdf->Cell(31,$SPACES,number_format($valLoans['trnAmountD'],2),0,0,'R');
			$pdf->Cell(1,$SPACES,"",0,0,'R');
			$pdf->Cell(31,$SPACES,number_format($valLoans['ActualAmt'],2),0,1,'R');
			
			$tempCode		 = $val['empNo'];
			$GTot 			+= (float)$valLoans['ActualAmt'];
			$GtotLoanAmt 	+= (float)$valLoans['lonWidInterst'];
			$GtotCurBal 	+= (float)$valLoans['lonCurbal'];
			
			if ($pdf->GetY() > 182) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
}
					$pdf->SetFont('Courier', 'B', '9');
					$pdf->Cell(120,$SPACES,"TOTAL",0,0,'R');
					$pdf->Cell(32,$SPACES,number_format($totLoanAmt,2),0,0,'R');
					$pdf->Cell(31,$SPACES,number_format($totCurBal,2),0,0,'R');
					$pdf->Cell(73,$SPACES,number_format($tot,2),0,1,'R');
			$pdf->SetFont('Courier', 'B', '9');
			$pdf->Cell(120,$SPACES,"GRAND TOTAL",0,0,'R');
			$pdf->Cell(32,$SPACES,number_format($GtotLoanAmt,2),0,0,'R');
			$pdf->Cell(31,$SPACES,number_format($GtotCurBal,2),0,0,'R');
			$pdf->Cell(73,$SPACES,number_format($GTot,2),0,1,'R');
			$pdf->SetFont('Courier', '', '9');
#########################################################################
if ($pdf->GetY() > 186) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".($ctr-1),0,1);
#########################################################################
$pdf->Output('loans.pdf','D');


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$branch			= $inqTSObj->getEmpBranchArt($compCode,$_GET['branch']);
	if ($branch['brnShortDesc'] != "") {
		if ($_GET['loc']==1)
			$loc = " HO";
		elseif ($_GET['loc']==2)
			$loc = " Str";
					
		$branch 		= "(".$branch['brnShortDesc']."$loc)";
	}
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: LOANSREPORT";
	$repTitle 		= "LOANS REPORT";
	$refNo    		= ""; 
	$dtlLabelDown   = "Emp. No.       Employee                Loan Type     Ref. No.       Loan Amount         Cur. Bal.        Ded. Schedule       Actual Ded.";
	$dtlLabelDown2   = "";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(198,15,"Payroll Date: ".date("m/d/Y",strtotime($dt)));
	$pdf->Text(170,15,$refNo);
	$pdf->Text(10,23,$dtlLabelDown);
	########################### F O O T E R  ################################
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	
	$footerHt = 260; //////////////PORTRATE LETTER ONLY
	$pdf->Line(10,$footerHt-6,$TOTAL_WIDTH+6,$footerHt-6);
	$pdf->Text(10,$footerHt,$prntdBy);
	$pdf->Ln(22);
}


?>
