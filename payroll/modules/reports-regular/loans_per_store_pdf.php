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
	$dtfrom					= $_GET['from'];
	$dtto					= $_GET['to'];
	$branch					= $_GET['branch'];
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
	if ($branch != 0) {
		$branch = " AND empBrnCode = '$branch'";
	} else {
		$branch = "";
	}
	$sqlBranch = "Select brnCode,brnShortDesc from tblBranch where compCode='{$_SESSION['company_code']}'";
	$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($sqlBranch));;
	$lonType=($_GET['lonType'] !="" && $_GET['lonType'] !=0) ? $_GET['lonType']:"";
	$lonTypeFilter = ($lonType !="") ? " AND loans.lonTypeCd='$lonType'":"";
		 $sqlLoans = "Select emp.empNo,custNo,empLastName,empFirstName,empMidName,lonTypeShortDesc as loanType,lonRefNo,lonwidInterst as loanAmt,loans.dateAdded,brnShortDesc as branch,lonCurbal, lonStat = case lonStat when 'C' then 'Completed' when 'O' then 'Open' when 'T' then 'Terminated' else '' end from tblEmpmast emp 
								inner join tblEmpLoans loans on emp.empNo=loans.empNo
								left join tblCustomerNo cust on emp.empNo=cust.empNo
								inner join tblLoanType ltype on loans.lonTypeCd=ltype.lonTypeCd
								inner join tblBranch on empBrnCode=brnCode
						WHERE  empPayCat='{$_SESSION['pay_category']}' 
							AND empPayGrp='{$_SESSION['pay_group']}' and loans.dateAdded between '$dtfrom' and '$dtto'
							$lonTypeFilter $branch $empName1 $empDiv1 $empName1 $empDept1 $empSect1 
						ORDER BY emp.empBrnCode, emp.empLastName, emp.empFirstName, emp.empMidName	";
	$arrLoans = $inqTSObj->getArrRes($inqTSObj->execQry($sqlLoans));
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$ctr=0;
$GTot = 0;
############################### LOOPING THE PAGES ###########################
$branch = "";
$refNo = "";
$tot = 0;
$totLoanAmt = 0;
$totCurBal = 0;
$GtotLoanAmt = 0;
$GtotCurBal = 0;
foreach($arrLoans as $val) {
	$ctr++;
	if ($branch != $val['branch']) {
		$pdf->SetFont('Courier', 'B', '9');
		if ($branch != "") {
			$pdf->Cell(175,$SPACES,"Branch Total    ",0,0,'R');
			$pdf->Cell(27,$SPACES,number_format($totLoanAmt,2),0,0,'R');
			$pdf->Cell(27,$SPACES,number_format($totCurBal,2),0,1,'R');
			$totLoanAmt = 0;
			$totCurBal = 0;
		}
		$pdf->Cell(8,$SPACES,$val['branch'],0,0,"L");
		$pdf->SetFont('Courier', '', '9');
		$pdf->Ln();		
	}
	$totLoanAmt = $totLoanAmt + $val['loanAmt'];
	$totCurBal = $totCurBal + $val['lonCurbal'];
	$GtotLoanAmt = $GtotLoanAmt + $val['loanAmt'];
	$GtotCurBal = $GtotCurBal + $val['lonCurbal'];

	$branch = $val['branch'];
	$nameInit = $val['empLastName'] . ", " . $val['empFirstName']." ".$val['empMidName'][0].".";
	$pdf->Cell(23,$SPACES,$val['empNo'],0,0);
	$pdf->Cell(17,$SPACES,$val['custNo'],0,0);
	$pdf->Cell(55,$SPACES,$nameInit,0,0);
	$pdf->Cell(25,$SPACES,$val['loanType'],0,0);
	$pdf->Cell(25,$SPACES,date("m/d/Y",strtotime($val['dateAdded'])),0,0);
	$pdf->Cell(30,$SPACES,$val['lonRefNo'],0,0);
	$pdf->Cell(27,$SPACES,number_format($val['loanAmt'],2),0,0,'R');
	$pdf->Cell(27,$SPACES,number_format($val['lonCurbal'],2),0,0,'R');
	$pdf->Cell(27,$SPACES,"   ".$val['lonStat'],0,1,'L');
	
	if ($pdf->GetY() > 182) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
}
			$pdf->SetFont('Courier', 'B', '9');
			$pdf->Cell(175,$SPACES,"Branch Total    ",0,0,'R');
			$pdf->Cell(27,$SPACES,number_format($totLoanAmt,2),0,0,'R');
			$pdf->Cell(27,$SPACES,number_format($totCurBal,2),0,1,'R');
			$pdf->Cell(175,$SPACES,"Grand Total    ",0,0,'R');
			$pdf->Cell(27,$SPACES,number_format($GtotLoanAmt,2),0,0,'R');
			$pdf->Cell(27,$SPACES,number_format($GtotCurBal,2),0,1,'R');

#########################################################################
if ($pdf->GetY() > 186) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".($ctr),0,1);
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
	$repId    		= "Report ID: LOANSPERTYPE";
	$repTitle 		= "LOANS PER TYPR PER STORE REPORT";
	$refNo    		= ""; 
	$dtlLabelDown   = "Emp. No.    Cust. No.     Employee               Loan Type     Date Created   Ref. No.          Loan Amount    Cur. Bal.    Status";
	$dtlLabelDown2   = "";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"        Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(198,15,"Date Added: ".date("m/d/Y",strtotime($_GET['from'])) . '-' . date("m/d/Y",strtotime($_GET['to'])));
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
