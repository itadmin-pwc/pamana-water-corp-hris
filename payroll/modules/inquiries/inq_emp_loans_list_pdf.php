<?
################### INCLUDE FILE #################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_loans_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$maintEmpLoanObj = new inqEmpLoanObj();
$sessionVars = $maintEmpLoanObj->getSeesionVars();
$maintEmpLoanObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$maintEmpLoanObj->compCode      = $compCode;
$maintEmpLoanObj->empNo         = $_GET['empNo'];
$maintEmpLoanObj->empDiv        = $_GET['empDiv'];
$maintEmpLoanObj->empDept       = $_GET['empDept'];
$maintEmpLoanObj->empSect       = $_GET['empSect'];
$maintEmpLoanObj->loanTypeAll   = $_GET['loanTypeAll'];
$maintEmpLoanObj->loanType      = $_GET['loanType'];
$maintEmpLoanObj->loanStatus    = $_GET['loanStatus'];

################ GET TOTAL RECORDS ###############
$resSearch = $maintEmpLoanObj->getEmpLoanInq();
$numRec = count($resSearch);
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 200
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$pdf = new FPDF('L', 'mm', 'LETTER');
$pdf->SetFont('Courier', '', '9');
$TOTAL_WIDTH   			= 265;
$TOTAL_WIDTH_2 			= 132;
$TOTAL_WIDTH_3 			= 88;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 265;
$pdf->TOTAL_WIDTH_2     = 132;
$pdf->TOTAL_WIDTH_3     = 88;
$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################

if ($_GET['empNo'] > "") $empNoNew = " AND tblEmpMast.empNo = '{$maintEmpLoanObj->empNo }' "; else $empNoNew = "";
if ($_GET['loanTypeAll'] < 4) $loanTypeAllNew = " AND tblEmpLoans.lonTypeCd LIKE '{$maintEmpLoanObj->loanTypeAll}%' AND tblLoanType.lonTypeCd LIKE '{$maintEmpLoanObj->loanTypeAll}%' "; else $loanTypeAllNew = "";
if ($_GET['loanType'] > 0) $loanTypeNew = " AND tblEmpLoans.lonTypeCd = '{$maintEmpLoanObj->loanType}' AND tblLoanType.lonTypeCd = '{$maintEmpLoanObj->loanType}' "; else $loanTypeNew = "";

$qryLoanList = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, 
					tblEmpMast.empDiv, tblEmpMast.empDepCode, tblEmpMast.empSecCode, tblEmpLoans.lonTypeCd, 
					tblLoanType.lonTypeDesc, tblEmpLoans.lonRefNo, tblEmpLoans.lonAmt, tblEmpLoans.lonWidInterst, tblEmpLoans.lonStart, 
					tblEmpLoans.lonEnd, tblEmpLoans.lonSked, tblEmpLoans.lonNoPaymnts, tblEmpLoans.lonDedAmt1, tblEmpLoans.lonDedAmt2, 
					tblEmpLoans.lonPayments, tblEmpLoans.lonPaymentNo, tblEmpLoans.lonCurbal, tblEmpLoans.lonLastPay, tblLoanType.lonTypeShortDesc 
			   FROM tblEmpLoans INNER JOIN 
					tblEmpMast ON tblEmpLoans.empNo = tblEmpMast.empNo INNER JOIN 
					tblLoanType ON tblEmpLoans.lonTypeCd = tblLoanType.lonTypeCd 
			   WHERE tblEmpLoans.compCode = '{$sessionVars['compCode']}' AND tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblLoanType.compCode = '{$sessionVars['compCode']}' 
			   AND tblEmpMast.empPayGrp='".$_SESSION['pay_group']."' 
				$empNoNew $empDivNew $empDeptNew $empSectNew $loanTypeAllNew $loanTypeNew $loanStatusNew ";
if ($_GET['orderBy']==1) {
$qryLoanList.="ORDER BY tblEmpMast.empLastName,tblEmpMast.empFirstName,tblEmpMast.empMidName, tblLoanType.lonTypeDesc, tblEmpLoans.lonRefNo";
} else {
$qryLoanList.="ORDER BY tblLoanType.lonTypeDesc, tblEmpLoans.lonRefNo, tblEmpMast.empLastName,tblEmpMast.empFirstName,tblEmpMast.empMidName ";
}
$resLoanList = $maintEmpLoanObj->execQry($qryLoanList);
$arrLoanList = $maintEmpLoanObj->getArrRes($resLoanList);
#####################################################################
$tempCode = "";
HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $_GET['orderBy']);
############################### LOOPING THE PAGES ###########################
foreach ($arrLoanList as $loanListVal){
	$div = $maintEmpLoanObj->getDivDescArt($sessionVars['compCode'], $loanListVal['empDiv']);
	$dept = $maintEmpLoanObj->getDeptDescArt($sessionVars['compCode'], $loanListVal['empDiv'], $loanListVal['empDepCode']);
	$sect = $maintEmpLoanObj->getSectDescArt($sessionVars['compCode'], $loanListVal['empDiv'], $loanListVal['empDepCode'], $loanListVal['empSecCode']);
	$nameInit = $loanListVal['empFirstName'][0].".".$loanListVal['empMidName'][0].".";
	if ($loanListVal['lonSked']==1) $periodDed = "1st"; if ($loanListVal['lonSked']==2) $periodDed = "2nd"; if ($loanListVal['lonSked']==3) $periodDed = "Both"; 
	if ($_GET['orderBy']==1) {
		if ($tempCode!=$loanListVal['empNo']) {
			$pdf->SetFont('Courier', '', '8');
			$pdf->Cell(21,$SPACES,$loanListVal['empNo'],0,0);
			$pdf->Cell(30,$SPACES,$loanListVal['empLastName']." ".$nameInit,0,0);
			$pdf->Cell(35,$SPACES,$div['deptShortDesc'],0,0);
			$pdf->SetFont('Courier', '', '9');
		} else {
			$pdf->Cell(21,$SPACES,"",0,0);
			$pdf->Cell(30,$SPACES,"",0,0);
			$pdf->Cell(35,$SPACES,"",0,0);
		}
		$pdf->Cell(25,$SPACES,$loanListVal['lonTypeShortDesc'],0,0);
		$pdf->Cell(25,$SPACES,substr($loanListVal['lonRefNo'],0,14),0,0);
		$pdf->Cell(25,$SPACES,$loanListVal['lonWidInterst'],0,0,'R');
		$pdf->Cell(25,$SPACES,$loanListVal['lonDedAmt2'],0,0,'R');
		$pdf->Cell(25,$SPACES,$loanListVal['lonPayments'],0,0,'R');
		$pdf->Cell(20,$SPACES,$periodDed."  ",0,0,'R');
		$pdf->Cell(25,$SPACES,$loanListVal['lonCurbal'],0,1,'R');
		if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $_GET['orderBy']);
		######################## GRAND TOTAL ########################################################
		$loanTotal = $maintEmpLoanObj->getEmpLoanTotalByEmp($sessionVars['compCode'], $loanListVal['empNo'], $groupTypeNew);
		if ($loanTotal['refMax'] > "") { 
			$splitDesc = split("-",$loanTotal['refMax']);
			if ($splitDesc[2]==$loanListVal['lonTypeCd'] && $splitDesc[1]==$loanListVal['lonRefNo']) {
				$pdf->SetFont('Courier', 'B', '9');
				$pdf->Cell(21,$SPACES,"",0,0);
				$pdf->Cell(75,$SPACES,"",0,0); /////"Total for this Loan Type: "
				$pdf->Cell(35,$SPACES,"TOTAL: ".$loanTotal['totRec']." record/s",0,0);
				$pdf->Cell(10,$SPACES,"",0,0);
				$pdf->Cell(20,$SPACES,$loanTotal['totAmt'],0,0,'R');
				$pdf->Cell(30,$SPACES,"",0,0,'R');
				$pdf->Cell(20,$SPACES,$loanTotal['totPaymnts'],0,0,'R');
				$pdf->Cell(25,$SPACES,"",0,0,'C');
				$pdf->Cell(20,$SPACES,$loanTotal['totCurbal'],0,1,'R');
				$pdf->SetFont('Courier', '', '10');
				$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
			}
		}
		#############################################################################################
		$tempCode=$loanListVal['empNo'];
	} else {
		if ($tempCode!=$loanListVal['lonTypeCd']) {
			$pdf->Cell(25,$SPACES,$loanListVal['lonTypeShortDesc'],0,0);
		} else {
			$pdf->Cell(25,$SPACES,"",0,0);
		}
		$pdf->Cell(25,$SPACES,$loanListVal['lonRefNo'],0,0);
		$pdf->Cell(25,$SPACES,number_format($loanListVal['lonWidInterst'],2),0,0,'R');
		$pdf->Cell(25,$SPACES,number_format($loanListVal['lonDedAmt2'],2),0,0,'R');
		$pdf->Cell(25,$SPACES,number_format($loanListVal['lonPayments'],2),0,0,'R');
		$pdf->Cell(15,$SPACES,$periodDed,0,0,'C');
		$pdf->Cell(25,$SPACES,number_format($loanListVal['lonCurbal'],2),0,0,'R');
		$pdf->Cell(25,$SPACES,$loanListVal['empNo'],0,0,"C");
		$pdf->Cell(35,$SPACES,$loanListVal['empLastName']." ".$nameInit,0,0);
		$pdf->Cell(35,$SPACES,$div['deptShortDesc'],0,1);
		if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $_GET['orderBy']);
		######################## GRAND TOTAL ########################################################
		$loanTotal = $maintEmpLoanObj->getEmpLoanTotalByLoan($sessionVars['compCode'], $loanListVal['lonTypeCd'], $groupTypeNew);
		if ($loanTotal['refMax'] > "") { 
			$splitDesc = split("-",$loanTotal['refMax']);
			if ($splitDesc[2]==$loanListVal['lonTypeCd'] && $splitDesc[1]==$loanListVal['lonRefNo']) {
				$pdf->SetFont('Courier', 'B', '9');
				$pdf->Cell(35,$SPACES,"TOTAL: ".$loanTotal['totRec']." record/s",0,0);
				$pdf->Cell(20,$SPACES,"",0,0);
				$pdf->Cell(20,$SPACES,number_format($loanTotal['totAmt'],2),0,0,'R');
				$pdf->Cell(30,$SPACES,"",0,0,'R');
				$pdf->Cell(20,$SPACES,number_format($loanTotal['totPaymnts'],2),0,0,'R');
				$pdf->Cell(20,$SPACES,"",0,0,'C');
				$pdf->Cell(20,$SPACES,number_format($loanTotal['totCurbal'],2),0,0,'R');
				$pdf->Cell(21,$SPACES,"",0,0);
				$pdf->Cell(40,$SPACES,"",0,0);
				$pdf->Cell(55,$SPACES,"",0,1); /////"Total for this Loan Type: "
				$pdf->SetFont('Courier', '', '9');
				$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
			}
		}
		#############################################################################################
		$tempCode=$loanListVal['lonTypeCd'];
	}
}
#########################################################################
if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $_GET['orderBy']);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".$numRec,0,1);
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $maintEmpLoanObj, $compCode, $orderBy) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$pdf->currDate 		= "Run Date: ".$maintEmpLoanObj->currentDateArt();
	$pdf->compName 		= "                                     ".$maintEmpLoanObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: EMPLN002";
	$pdf->repTitle 		= "                                     Employee Loans Register as of ".$maintEmpLoanObj->currentDateNoTimeArt();
	$pdf->refNo    		= "";
	if ($_GET['orderBy']==1) {
		$pdf->dtlLabelUp    = " Emp.No.   Emp. Name        Department        Loan Type       RefNo        Total Amt      Ded Amt       Total      Sked        Cur Bal";
		$pdf->dtlLabelDown  = "                                                                                                       Paymnts";
	} else {
		$pdf->dtlLabelUp    = " Loan Type       RefNo        Total Amt      Ded Amt      Total    Sked       Current     Emp.No.   Employee Name      Department                ";
		$pdf->dtlLabelDown  = "                                                         Paymnts    Ded       Balance";
	}
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $maintEmpLoanObj->getSeesionVars();
	$dispUser = $maintEmpLoanObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
