<?php

	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("profile_userdef.obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$mainUserDefObjObj = new  mainUserDefObj();
	
	############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
	############################ LETTER LANDSCAPE TOTAL WIDTH = 265
	############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
	####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
	####################### FOOTER PORTRATE LETTER ONLY       = 260
	####################### HEADER 10.0012
	$pdf = new FPDF('P', 'mm', 'LETTER');
	$pdf->SetFont('Courier', '', '9');
	
	$TOTAL_WIDTH   			= 200;
	$TOTAL_WIDTH_2 			= 100;
	$TOTAL_WIDTH_3 			= 66;
	$SPACES        			= 5;
	$pdf->TOTAL_WIDTH       = 200;
	$pdf->TOTAL_WIDTH_2     = 100;
	$pdf->TOTAL_WIDTH_3     = 66;
	$pdf->SPACES	       	= 5;
	
	
	//HEADER_FOOTER($pdf, $inqTSObj, $compCode, $orderBy,$arrPayPd, $catName,$groupName);
	$pdf->Cell(38,20,'vincent',0,0,'R');
	$pdf->Output();
	
	
	/*function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $orderBy,$arrPayPd, $catName,$groupName) 
	{
	############################ H E A D E R ################################
		//$pdf->currDate 		 = "Run Date: ".$mainUserDefObjObj->getDate(); 
		//$pdf->compName 		 = $mainUserDefObjObj->getCompanyName($compCode); 
		$pdf->reppages 		 = "";
		$pdf->repId    		 = "Report ID: OTNDR001";
		$pdf->repTitle  	 = "Undertime/Night Diff. Report for the Period of ";
		$pdf->refNo     	 = "";
		$pdf->dtlLabelUp     = "";
		$pdf->dtlLabelDown   = " EMP.NO.    EMPLOYEE NAME                                            UNDERTIME      TARDINESS";
		$pdf->Header();
		########################### F O O T E R  ################################
		
		$pdf->Footer();
		$pdf->Ln(18);
	}*/
?>