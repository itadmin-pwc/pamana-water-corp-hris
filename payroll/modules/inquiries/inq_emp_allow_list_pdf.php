<?
################### INCLUDE FILE #################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_allow_obj.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$inqEmpAllowObj = new inqEmpAllowObj();
$sessionVars = $inqEmpAllowObj->getSeesionVars();
$inqEmpAllowObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$inqEmpAllowObj->compCode      = $compCode;
$inqEmpAllowObj->empNo         = $_GET['empNo'];
$inqEmpAllowObj->empDiv        = $_GET['empDiv'];
$inqEmpAllowObj->empDept       = $_GET['empDept'];
$inqEmpAllowObj->empSect       = $_GET['empSect'];
$inqEmpAllowObj->allowType      = $_GET['allowType'];

################ GET TOTAL RECORDS ###############
$resSearch = $inqEmpAllowObj->getEmpAllowInq();
$numRec = count($resSearch);
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 200
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$pdf = new FPDF('L', 'mm', 'LETTER');
$pdf->SetFont('Courier', '', '10');
$TOTAL_WIDTH   			= 265;
$TOTAL_WIDTH_2 			= 132;
$TOTAL_WIDTH_3 			= 88;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 265;
$pdf->TOTAL_WIDTH_2     = 132;
$pdf->TOTAL_WIDTH_3     = 88;
$pdf->SPACES	       	= 5;
############################ Q U E R Y ##################################
if ($_GET['empNo'] > "") $empNoNew = " AND tblEmpMast.empNo = '{$empNo}' "; else $empNoNew = "";
if ($_GET['empDiv'] > 0) $empDivNew = " AND tblEmpMast.empDiv LIKE '{$empDiv}' "; else $empDivNew = "";
if ($_GET['empDept'] > 0) $empDeptNew = " AND tblEmpMast.empDepCode LIKE '{$empDept}' "; else $empDeptNew = "";
if ($_GET['empSect'] > 0) $empSectNew = " AND tblEmpMast.empSecCode LIKE '{$empSect}' "; else $empSectNew = "";
if ($_GET['allowType'] > 0) $allowTypeNew = " AND tblAllowance.allowCode = '{$allowType}' AND tblAllowType.allowCode = '{$allowType}' "; else $allowTypeNew = "";

if ($_GET['orderBy']==1) $orderByNew = " ORDER BY tblEmpMast.empLastName,tblEmpMast.empFirstName,tblEmpMast.empMidName, tblAllowType.allowDesc "; else $orderByNew = " ORDER BY tblAllowType.allowDesc, tblEmpMast.empLastName,tblEmpMast.empFirstName,tblEmpMast.empMidName ";
$qryAllowList = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName,tblEmpMast.empMidName, tblEmpMast.empDiv, tblEmpMast.empDepCode, tblEmpMast.empSecCode, tblAllowType.allowDesc, 
				  tblAllowance.allowAmt, tblAllowance.allowSked, tblAllowance.allowTaxTag, tblAllowance.allowPayTag, tblAllowance.allowStart, 
				  tblAllowance.allowEnd, tblAllowance.allowCode
				  FROM tblEmpMast INNER JOIN 
				  tblAllowance ON tblEmpMast.empNo = tblAllowance.empNo INNER JOIN 
				  tblAllowType ON tblAllowance.allowCode = tblAllowType.allowCode 
			     WHERE tblAllowance.compCode = '{$sessionVars['compCode']}' AND tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblAllowType.compCode = '{$sessionVars['compCode']}' 
			      AND tblEmpMast.empPayGrp='".$_SESSION['pay_group']."' 
			   	  $empNoNew $empDivNew $empDeptNew $empSectNew $allowTypeNew  $orderByNew ";
$resAllowList = $inqEmpAllowObj->execQry($qryAllowList);
$arrAllowList = $inqEmpAllowObj->getArrRes($resAllowList);
#####################################################################
$tempCode = "";
HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $_GET['orderBy']);
############################### LOOPING THE PAGES ###########################
foreach ($arrAllowList as $allowListVal){
	$div = $inqEmpAllowObj->getDivDescArt($sessionVars['compCode'], $allowListVal['empDiv']);
	$dept = $inqEmpAllowObj->getDeptDescArt($sessionVars['compCode'], $allowListVal['empDiv'], $allowListVal['empDepCode']);
	$sect = $inqEmpAllowObj->getSectDescArt($sessionVars['compCode'], $allowListVal['empDiv'], $allowListVal['empDepCode'], $allowListVal['empSecCode']);
	$nameInit = $allowListVal['empFirstName'][0].".".$allowListVal['empMidName'][0].".";
	if ($allowListVal['allowSked']==1) $periodDed = "1st"; if ($allowListVal['allowSked']==2) $periodDed = "2nd"; if ($allowListVal['allowSked']==3) $periodDed = "Both"; 
	if ($allowListVal['allowTaxTag']=="Y") $taxable = "YES"; else $taxable = "NO";
	if ($_GET['orderBy']==1) {
		if ($tempCode!=$allowListVal['empNo']) {
			$pdf->Cell(21,$SPACES,$allowListVal['empNo'],0,0);
			$pdf->Cell(40,$SPACES,$allowListVal['empLastName']." ".$nameInit,0,0);
			$pdf->Cell(55,$SPACES,$div['deptShortDesc']."/".$dept['deptShortDesc']."/".$sect['deptShortDesc'],0,0);
		} else {
			$pdf->Cell(21,$SPACES,"",0,0);
			$pdf->Cell(40,$SPACES,"",0,0);
			$pdf->Cell(55,$SPACES,"",0,0);
		}
		$pdf->Cell(50,$SPACES,$allowListVal['allowDesc'],0,0);
		if ($allowListVal['allowPayTag']=="T") {
			$pdf->Cell(25,$SPACES,$inqEmpAllowObj->valDateArt($allowListVal['allowStart']),0,0);
			$pdf->Cell(25,$SPACES,$inqEmpAllowObj->valDateArt($allowListVal['allowEnd']),0,0);
		} else {
			$pdf->Cell(25,$SPACES,"",0,0);
			$pdf->Cell(25,$SPACES,"",0,0);
		}
		$pdf->Cell(15,$SPACES,$periodDed,0,0);
		$pdf->Cell(10,$SPACES,$taxable,0,0,'C');
		$pdf->Cell(20,$SPACES,$allowListVal['allowAmt'],0,1,'R');
		if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $_GET['orderBy']);
		######################## GRAND TOTAL ########################################################
		$allowTotal = $inqEmpAllowObj->getEmpAllowTotalByEmp($sessionVars['compCode'], $allowListVal['empNo']);
		if ($allowTotal['refMax'] > "") { 
			$splitDesc = split("-",$allowTotal['refMax']);
			if ($splitDesc[1]==$allowListVal['allowCode']) {
				$pdf->SetFont('Courier', 'B', '10');
				$pdf->Cell(21,$SPACES,"",0,0);
				$pdf->Cell(40,$SPACES,"",0,0);
				$pdf->Cell(55,$SPACES,"",0,0); /////"Total for this Loan Type: "
				$pdf->Cell(50,$SPACES,"TOTAL: ".$allowTotal['totRec']." record/s",0,0);
				$pdf->Cell(25,$SPACES,"",0,0);
				$pdf->Cell(25,$SPACES,"",0,0,'R');
				$pdf->Cell(15,$SPACES,"",0,0,'R');
				$pdf->Cell(10,$SPACES,"",0,0,'R');
				$pdf->Cell(20,$SPACES,$allowTotal['totAmt'],0,1,'R');
				$pdf->SetFont('Courier', '', '10');
				$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
			}
		}
		#############################################################################################
		$tempCode=$allowListVal['empNo'];
	} else {
		if ($tempCode!=$allowListVal['allowCode']) {
			$pdf->Cell(50,$SPACES,$allowListVal['allowDesc'],0,0);
		} else {
			$pdf->Cell(50,$SPACES,"",0,0);
		}
		if ($allowListVal['allowPayTag']=="T") {
			$pdf->Cell(25,$SPACES,$inqEmpAllowObj->valDateArt($allowListVal['allowStart']),0,0);
			$pdf->Cell(25,$SPACES,$inqEmpAllowObj->valDateArt($allowListVal['allowEnd']),0,0);
		} else {
			$pdf->Cell(25,$SPACES,"",0,0);
			$pdf->Cell(25,$SPACES,"",0,0);
		}
		$pdf->Cell(15,$SPACES,$periodDed,0,0);
		$pdf->Cell(10,$SPACES,$taxable,0,0,'C');
		$pdf->Cell(20,$SPACES,$allowListVal['allowAmt'],0,0,'R');
		$pdf->Cell(21,$SPACES,$allowListVal['empNo'],0,0);
		$pdf->Cell(40,$SPACES,$allowListVal['empLastName']." ".$nameInit,0,0);
		$pdf->Cell(55,$SPACES,$div['deptShortDesc']."/".$dept['deptShortDesc']."/".$sect['deptShortDesc'],0,1);
		if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $_GET['orderBy']);
		######################## GRAND TOTAL ########################################################
		$allowTotal = $inqEmpAllowObj->getEmpAllowTotalByAllow($sessionVars['compCode'], $allowListVal['allowCode']);
		if ($allowTotal['refMax'] > "") { 
			$splitDesc = split("-",$allowTotal['refMax']);
			if ($splitDesc[1]==$allowListVal['allowCode'] && $splitDesc[5]==$allowListVal['empNo']) {
				$pdf->SetFont('Courier', 'B', '10');
				$pdf->Cell(50,$SPACES,"TOTAL: ".$allowTotal['totRec']." record/s",0,0);
				$pdf->Cell(25,$SPACES,"",0,0);
				$pdf->Cell(25,$SPACES,"",0,0,'R');
				$pdf->Cell(15,$SPACES,"",0,0,'R');
				$pdf->Cell(10,$SPACES,"",0,0,'R');
				$pdf->Cell(20,$SPACES,$allowTotal['totAmt'],0,0,'R');
				$pdf->Cell(21,$SPACES,"",0,0);
				$pdf->Cell(40,$SPACES,"",0,0);
				$pdf->Cell(55,$SPACES,"",0,1); /////"Total for this Loan Type: "
				$pdf->SetFont('Courier', '', '10');
				$pdf->Line(11,$pdf->GetY(),$TOTAL_WIDTH+6,$pdf->GetY());  /////(X1,Y1,X2,Y2)			  ####### LINE LINE LINE
			}
		}
		#############################################################################################
		$tempCode=$allowListVal['allowCode'];
	}
}
#########################################################################
if ($pdf->GetY() > 180) HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $_GET['orderBy']);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".$numRec,0,1);
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $inqEmpAllowObj, $compCode, $orderBy) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ ################################
	$pdf->currDate 		= "Run Date: ".$inqEmpAllowObj->currentDateArt();
	$pdf->compName 		= $inqEmpAllowObj->getCompanyName($compCode);
	$pdf->reppages 		= "";
	$pdf->repId    		= "Report ID: EMPALL001";
	$pdf->repTitle 		= "Employee Allowance Register as of ".$inqEmpAllowObj->currentDateNoTimeArt();
	$pdf->refNo    		= "";
	if ($_GET['orderBy']==1) {
		$pdf->dtlLabelUp    = " Emp.No.   Employee Name      Department                Allowance Type          Start        End      Pay    Tax-    Amount";
		$pdf->dtlLabelDown  = "                                                                                 Date        Date    Period  able          ";
	} else { 
		$pdf->dtlLabelUp    = " Allowance Type          Start        End      Pay    Tax-    Amount  Emp.No.   Employee Name     Department     ";
		$pdf->dtlLabelDown  = "                          Date        Date    Period  able             ";
	}
	$pdf->Header();
	########################### F O O T E R  ################################
	$userId= $inqEmpAllowObj->getSeesionVars();
	$dispUser = $inqEmpAllowObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$pdf->prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	$pdf->Footer();
	$pdf->Ln(18);
}
?>
