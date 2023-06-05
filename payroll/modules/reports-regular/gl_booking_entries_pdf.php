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
	$payPd       			= $_GET['payPd'];
	$branch					= $_GET['branch'];
	$loc					= $_GET['loc'];
	$arrPayPd 				= $inqTSObj->getSlctdPd($compCode,$payPd);
################ GET TOTAL RECORDS ###############
	$dt['Pdate']					= $arrPayPd['pdPayable'];
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
		$branchInfo = $inqTSObj->getEmpBranchArt($_SESSION['company_code'],$branch);
		if ($branch !=0) {
			if ($loc == 1) {
				$locDesc = " HO";
				$filter = " Where tblPayJournal.strCode='".$branchInfo['glCodeHO']."'";
			} elseif ($loc == 2) {
				$filter = " Where tblPayJournal.strCode='".$branchInfo['glCodeStr']."'";
				$locDesc = " Str";
			} elseif ($loc == 0) {
				if ($branchInfo['glCodeStr'] != "") {
					$glCodes = $branchInfo['glCodeStr'];
				}
	
				if ($branchInfo['glCodeHO'] != "") {
					if ($branchInfo['glCodeStr'] == "") 
						$glCodes = $branchInfo['glCodeHO'];
					else
						$glCodes .= ",".$branchInfo['glCodeHO'];
				}
				$filter = " AND tblPayJournal.strCode IN ($glCodes)";
			}
		}	
		$dt['Title']	= "(".$branchInfo['brnShortDesc']."$locDesc)";
		
	 $sqlGL = "SELECT     tblGLCodes.glCodeDesc AS glDesc, tblPayJournal.*
FROM         tblPayJournal Left JOIN
                      tblGLCodes ON tblPayJournal.majCode = tblGLCodes.majCode AND tblPayJournal.compGLCode = tblGLCodes.compGLCode AND 
                      tblPayJournal.strCode = tblGLCodes.strCode AND tblPayJournal.minCode = tblGLCodes.minCode Where payGrp='{$_SESSION['pay_group']} AND payCat='{$_SESSION['pay_category']}' $filter order by Amount Desc";
	$arrGL = $inqTSObj->getArrRes($inqTSObj->execQry($sqlGL));
	$arrSum = $inqTSObj->getSqlAssoc($inqTSObj->execQry($sqlSum));
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
$GTot = 0;
############################### LOOPING THE PAGES ###########################
foreach ($arrGL as $val){
	$pdf->Cell(1,$SPACES,"",0,0,"L");
	$pdf->Cell(14,$SPACES,$val['compGLCode'],0,0);
	$pdf->Cell(17,$SPACES,$val['majCode'],0,0);
	$pdf->Cell(17,$SPACES,$val['minCode'],0,0);
	$pdf->Cell(15,$SPACES,$val['strCode'],0,0);
	$pdf->Cell(5,$SPACES,"",0,0,"C");
	$pdf->Cell(95,$SPACES,$val['glDesc'],0,0);
	$pdf->Cell(30,$SPACES,number_format($val['Amount'],2),0,1,'R');
	$ctr++;
	if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
}
			$pdf->SetFont('Courier', 'B', '9');
			$pdf->Cell(164,$SPACES,"GRAND TOTAL ",0,0,'R');
			$pdf->Cell(30,$SPACES,number_format($arrSum['Amount'],2),0,1,'R');
			$pdf->SetFont('Courier', '', '9');
#########################################################################
if ($pdf->GetY() > 250) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
#########################################################################
$pdf->Output();


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: GLENTRIES";
	$repTitle 		= "GL BOOKING ENTRIES ".$dt['Title']."";
	$refNo    		= ""; 
	$dtlLabelDown   = " CMP    Major    Minor    Store        Description                                           Amount";
	$dtlLabelDown2  = " Code   Code     Code     Code";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(170,15,"P. Date: ".date("m/d/Y",strtotime($dt['Pdate'])));
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
