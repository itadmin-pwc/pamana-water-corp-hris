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
	$journalTable =  ($compCode == 15) ? "tblPayjournal_jda":"tblPayjournal";
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
		if ($branch != 0) {
//				$filter = " AND strCode='".$branchInfo['glCodeHO']."'";
				$filter = " AND $journalTable.strCode='$branch'";
		}	
		if ($branchInfo['brnShortDesc'] != "") {
			$dt['Title']	= "(".$branchInfo['brnShortDesc']."$locDesc)";
		}	
	 
	 if ($_SESSION['pay_category'] != 9) {
		 $qryBranch = " SELECT     tblBranch.brnDesc, tblBranch.glCodeStr,tblBranch.compglCode
						FROM         tblBranch INNER JOIN
											  $journalTable ON tblBranch.compCode = $journalTable.compCode AND tblBranch.glCodeStr = $journalTable.strCode2
						Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}' 
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND $journalTable.compCode='{$_SESSION['company_code']}'  AND brnStat='A'
						  $filter
						  
						GROUP BY tblBranch.brnDesc, tblBranch.glCodeStr,tblBranch.compglCode
		";
		$arrBranch = $inqTSObj->getArrRes($inqTSObj->execQry($qryBranch));
	} else {
		$qryEmp = "Select empLastName,empFirstName,empMidName,emp.empNo,compglCode from tblEmpMast emp inner join $journalTable pay on
						emp.empNo=pay.empNo where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}' 
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND pay.compCode='{$_SESSION['company_code']}' 
					And emp.compCode='{$_SESSION['company_code']}' 
					group by empLastName,empFirstName,empMidName,emp.empNo,compglCode
					order by empLastName,empFirstName,empMidName";
		$arrEmp = $inqTSObj->getArrRes($inqTSObj->execQry($qryEmp));
	}
	 
	 if ($_SESSION['pay_category'] != 9) {
		 $sqlGLpos = "SELECT     tblGLCodes.glCodeDesc AS glDesc, $journalTable.*
	FROM         $journalTable Left JOIN
						  tblGLCodes ON $journalTable.majCode2 = tblGLCodes.majCode AND $journalTable.compGLCode = tblGLCodes.compGLCode AND 
						  $journalTable.strCode2 = tblGLCodes.strCode AND $journalTable.minCode2 = tblGLCodes.minCode 
						  Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}' 
						  AND Amount>0
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND $journalTable.compCode='{$_SESSION['company_code']}'
						  $filter order by glCodeDesc Desc";
		  $sqlGLneg = "SELECT     tblGLCodes.glCodeDesc AS glDesc, $journalTable.*
	FROM         $journalTable Left JOIN
						  tblGLCodes ON $journalTable.majCode2 = tblGLCodes.majCode AND $journalTable.compGLCode = tblGLCodes.compGLCode AND 
						  $journalTable.strCode2 = tblGLCodes.strCode AND $journalTable.minCode2 = tblGLCodes.minCode 
						  Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}'  
						  AND Amount<0 
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND $journalTable.compCode='{$_SESSION['company_code']}'
						 $filter order by glCodeDesc Desc";
	} else {
		  $sqlGLpos = "SELECT tblGLCodes.glCodeDesc AS glDesc,$journalTable.strCode,$journalTable.compGLCode,majCode2,minCode2,strCode2,Amount,empNo
						FROM $journalTable Left JOIN
						  tblGLCodes ON $journalTable.majCode2 = tblGLCodes.majCode AND $journalTable.compGLCode = tblGLCodes.compGLCode AND 
						  $journalTable.strCode2 = tblGLCodes.strCode AND $journalTable.minCode2 = tblGLCodes.minCode 
						  Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}' 
						  AND Amount>0
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND $journalTable.compCode='{$_SESSION['company_code']}'
						  $filter 
						  group by empNo, tblGLCodes.glCodeDesc ,$journalTable.strCode,$journalTable.compGLCode,majCode2,minCode2,strCode2,Amount
						  order by glCodeDesc Desc";
		  $sqlGLneg = "SELECT tblGLCodes.glCodeDesc AS glDesc,$journalTable.strCode,$journalTable.compGLCode,majCode2,minCode2,strCode2,Amount,empNo
						  FROM $journalTable Left JOIN
						  tblGLCodes ON $journalTable.majCode2 = tblGLCodes.majCode AND $journalTable.compGLCode = tblGLCodes.compGLCode AND 
						  $journalTable.strCode2 = tblGLCodes.strCode AND $journalTable.minCode2 = tblGLCodes.minCode 
						  Where payGrp='{$_SESSION['pay_group']}' 
						  AND payCat='{$_SESSION['pay_category']}'  
						  AND Amount<0 
						  AND pdYear='{$arrPayPd['pdYear']}' 
						  AND pdNumber='{$arrPayPd['pdNumber']}'
						  AND $journalTable.compCode='{$_SESSION['company_code']}'
						 $filter 
						 group by empNo, tblGLCodes.glCodeDesc ,$journalTable.strCode,$journalTable.compGLCode,majCode2,minCode2,strCode2,Amount
						 order by glCodeDesc Desc";
	
	}				  
	 $arrGLpos = $inqTSObj->getArrRes($inqTSObj->execQry($sqlGLpos));
	 $arrGLneg = $inqTSObj->getArrRes($inqTSObj->execQry($sqlGLneg));
	 
	
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
$GTotCre = 0;
$GTotDeb = 0;

############################### LOOPING THE PAGES ###########################
if ($_SESSION['pay_category'] != 9) {
		foreach($arrBranch as $valbranch) {
			if ($ctr !=1) {
				HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
			}
			$GTotCre = $GTotDeb = 0;
			//$invoiceno = substr($valbranch['compglCode'],0,3)."".substr($valbranch['glCodeStr'],0,3).date('y',strtotime('1/1/'.$arrPayPd['pdYear'])).$arrPayPd['pdNumber'].$_SESSION['pay_group'].$_SESSION['pay_category'];

				$pdf->Cell(12,$SPACES,$valbranch['brnDesc'],0,1);
				$pdf->Ln();
				foreach ($arrGLpos as $val){
					if ($valbranch['glCodeStr'] == $val['strCode']) {
						$pdf->Cell(1,$SPACES,"",0,0,"L");
/*						$pdf->Cell(8,$SPACES,$val['compGLCode'],0,0);
						$pdf->Cell(14,$SPACES,$val['majCode2'],0,0);
						$pdf->Cell(8,$SPACES,$val['minCode2'],0,0);
						$pdf->Cell(14,$SPACES,$val['strCode2'],0,0);
*/						$pdf->Cell(101,$SPACES,$val['glDesc'],0,0);
							$GTotCre += round($val['Amount'],2);
							$pdf->Cell(25,$SPACES,number_format($val['Amount'],2),0,1,'R');
						
						$ctr++;
						if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
					}
				}
				foreach ($arrGLneg as $val){
					if ($valbranch['glCodeStr'] == $val['strCode']) {
						$pdf->Cell(1,$SPACES,"",0,0,"L");
/*						$pdf->Cell(8,$SPACES,$val['compGLCode'],0,0);
						$pdf->Cell(14,$SPACES,$val['majCode2'],0,0);
						$pdf->Cell(8,$SPACES,$val['minCode2'],0,0);
						$pdf->Cell(14,$SPACES,$val['strCode2'],0,0);
	*/					$pdf->Cell(101,$SPACES,$val['glDesc'],0,0);
							$GTotDeb += round($val['Amount'],2);
							$pdf->Cell(25,$SPACES,"",0,0,'R');
							$pdf->Cell(25,$SPACES,number_format(str_replace("-","",$val['Amount']),2),0,1,'R');
						
						$ctr++;
						if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
					}
				}
							$pdf->SetFont('Courier', 'B', '9');
							$pdf->Cell(104,$SPACES,"GRAND TOTAL ",0,0,'R');
							$pdf->Cell(25,$SPACES,number_format($GTotCre,2),0,0,'R');
							$pdf->Cell(25,$SPACES,number_format(str_replace("-","",$GTotDeb),2),0,1,'R');
							$pdf->SetFont('Courier', '', '9');
		}	
} else {
		foreach($arrEmp as $valEmp) {
			if ($ctr !=1) {
				HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
			}
			$GTotCre = $GTotDeb = 0;
				$invoiceno = substr($valEmp['compglCode'],0,3).$valEmp['empNo'];
				$pdf->Cell(12,$SPACES,$valEmp['empLastName'].", ".$valEmp['empFirstName'],0,1);
				$pdf->Cell(12,$SPACES,'Invoice No.: '.$invoiceno,0,1);
				foreach ($arrGLpos as $val){
					if ($valEmp['empNo'] == $val['empNo']) {
						$pdf->Cell(1,$SPACES,"",0,0,"L");
/*						$pdf->Cell(8,$SPACES,$val['compGLCode'],0,0);
						$pdf->Cell(14,$SPACES,$val['majCode2'],0,0);
						$pdf->Cell(8,$SPACES,$val['minCode2'],0,0);
						$pdf->Cell(14,$SPACES,$val['strCode2'],0,0);
*/						$pdf->Cell(101,$SPACES,$val['glDesc'],0,0);
							$GTotCre += round($val['Amount'],2);
							$pdf->Cell(25,$SPACES,number_format($val['Amount'],2),0,1,'R');
						
						$ctr++;
						if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
					}
				}
				foreach ($arrGLneg as $val){
					if ($valEmp['empNo'] == $val['empNo']) {
						$pdf->Cell(1,$SPACES,"",0,0,"L");
/*						$pdf->Cell(8,$SPACES,$val['compGLCode'],0,0);
						$pdf->Cell(14,$SPACES,$val['majCode2'],0,0);
						$pdf->Cell(8,$SPACES,$val['minCode2'],0,0);
						$pdf->Cell(14,$SPACES,$val['strCode2'],0,0);
*/						$pdf->Cell(101,$SPACES,$val['glDesc'],0,0);
							$GTotDeb += round($val['Amount'],2);
							$pdf->Cell(25,$SPACES,"",0,0,'R');
							$pdf->Cell(25,$SPACES,number_format(str_replace("-","",$val['Amount']),2),0,1,'R');
						
						$ctr++;
						if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
					}
				}
							$pdf->SetFont('Courier', 'B', '9');
							$pdf->Cell(104,$SPACES,"GRAND TOTAL ",0,0,'R');
							$pdf->Cell(25,$SPACES,number_format($GTotCre,2),0,0,'R');
							$pdf->Cell(25,$SPACES,number_format(str_replace("-","",$GTotDeb),2),0,1,'R');
							$pdf->SetFont('Courier', '', '9');
		}
}		
#########################################################################
if ($pdf->GetY() > 255) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
#########################################################################
$pdf->Output('gl_entries.pdf','D');


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	switch($_SESSION['pay_category']) {
		case 1:
			$payCat = "Executive";
		break;
		case 2:
			$payCat = "Confidential";
		break;
		case 3:
			$payCat = "Non Confidential";
		break;
		case 9:
			$payCat = "Resigned";
		break;
	}
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: GLENTRIES";
	$repTitle 		= "GL BOOKING ENTRIES ".$dt['Title']." ($payCat)";
	$refNo    		= ""; 
	$dtlLabelDown   = " Description                                              Debit           Credit";
	$dtlLabelDown2  = "  ";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(80,10,$compName);
	if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(325,10,"Page: ".$pdf->page.$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(80,15,$repTitle);
	$pdf->Text(155,15,"Payroll Date: ".date("m/d/Y",strtotime($dt['Pdate'])));
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
