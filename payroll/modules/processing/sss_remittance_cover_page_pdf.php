<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$pagRemObj = new inqTSObj();
	$sessionVars = $pagRemObj->getSeesionVars();
	$pagRemObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
				//195
			$this->SetFont('Arial','B','10'); 
			$this->Cell(40,5,'EMPLOYER NAME:','0','','L',0);
			$this->Cell(77,5,$this->compName,'0','','L',0);
			$this->Cell(50,5,'DATE TRANSMITTED:','0','','L',0);
			$this->Cell(30,5,date("m/d/Y"),'0','1','L',0);
			
			$this->Cell(40,5,'EE ID NUMBER:','0','','L',0);
			$this->Cell(77,5,$this->compSssNo,'0','','L',0);
			$this->Cell(50,5,'APPLICATION QUARTER:','0','','L',0);
			$this->Cell(30,5,$this->appquter,'0','1','L',0);
			$this->Ln();
			$this->Ln();
			$this->Ln();
		}
		
		function pagRemDetails($arrmtdGovtHist,$arrSSSExempt)
		{
			$tot1 = 0;
			$tot2 = 0;
			$cntEmpNo = 0;
			foreach($arrmtdGovtHist as $arrmtdGovtHist_val)
			{
				if($arrmtdGovtHist_val["sssEmp"]!=0 && !in_array($arrmtdGovtHist_val["empNo"],$arrSSSExempt))
				{
					$arrEmp[] = $arrmtdGovtHist_val["empNo"];
					$tot1 += $arrmtdGovtHist_val["sssEmp"];
					$tot2 += $arrmtdGovtHist_val["sssEmplr"];
					$EmpShare = empty($arrmtdGovtHist_val["sssEmp"])?0:$arrmtdGovtHist_val["sssEmp"];
					$EmrShare = empty($arrmtdGovtHist_val["sssEmplr"])?0:$arrmtdGovtHist_val["sssEmplr"];
					$EmpEc    = empty($arrmtdGovtHist_val["ec"])?0:$arrmtdGovtHist_val["ec"];
					
//					echo date('Y') ."== 2011 &&". date('m'). " == 11 && ". $arrmtdGovtHist_val["empNo"]." == 010002723<br />";


					$EmpCont+= $EmpShare + $EmrShare;
					$EmpContEc+= $EmpEc;
					$cntEmpNo++;
				}
			}
								
/*					if (date('Y') == 2012 && date('m') == 2) {
						$EmpCont = $EmpCont-1560-1040;
						$EmpContEc    = $EmpContEc-30-10;
					}*/

			$grandTotal = $EmpCont+$EmpContEc;
			
			$this->SetFont('Arial','B','10'); 
			$this->Cell(37,5,'','0','','L',0);
			$this->Cell(32,5,'SSS','0','','R',0);
			$this->Cell(32,5,'MEDICARE','0','','R',0);
			$this->Cell(20,5,'EC','0','','R',0);
			$this->Cell(32,5,'TOTAL','0','0','R',0);
			$this->Cell(42,5,'SBR NUMBER / OR#','0','1','R',0);
			
			if($this->line_Row == '1')
			{
				$this->Cell(37,5,'FIRST MONTH','0','','L',0);
				$this->Cell(32,5,number_format($EmpCont,2),'0','','R',0);
				$this->Cell(32,5,'0.00','0','','R',0);
				$this->Cell(20,5,number_format($EmpContEc,2),'0','','R',0);
				$this->Cell(32,5,number_format($grandTotal,2),'0','0','R',0);
				$this->Cell(42,5,'','0','1','R',0);
				
				$this->secondPage();
				
				$this->thirdPage();
			}
			elseif($this->line_Row == '2')
			{
				$this->firstPage();
				
				$this->Cell(37,5,'SECOND MONTH','0','','L',0);
				$this->Cell(32,5,number_format($EmpCont,2),'','','R',0);
				$this->Cell(32,5,'0.00','','','R',0);
				$this->Cell(20,5,number_format($EmpContEc,2),'','','R',0);
				$this->Cell(32,5,number_format($grandTotal,2),'','0','R',0);
				$this->Cell(42,5,'','','1','R',0);
				
				$this->thirdPage();
			}
			else
			{
				$this->firstPage();
				
				$this->secondPage();
				
				$this->Cell(37,5,'THIRD MONTH','0','','L',0);
				$this->Cell(32,5,number_format($EmpCont,2),'0','','R',0);
				$this->Cell(32,5,'0.00','0','','R',0);
				$this->Cell(20,5,number_format($EmpContEc,2),'0','','R',0);
				$this->Cell(32,5,number_format($grandTotal,2),'0','0','R',0);
				$this->Cell(42,5,'','','1','R',0);
			}
			
			$this->Cell(37,2,'','0','','L',0);
			$this->Cell(116,5,'__________________________________________________________','0','1','R',0);
			
			
			
			$this->Cell(37,5,'','','','L',0);
			$this->Cell(32,5,number_format($EmpCont,2),'','','R',0);
			$this->Cell(32,5,'0.00','','','R',0);
			$this->Cell(20,5,number_format($EmpContEc,2),'','','R',0);
			$this->Cell(32,5,number_format($grandTotal,2),'','0','R',0);
			$this->Cell(42,5,'','','1','R',0);
			
			$this->Ln();
			$this->Ln();
			
			$this->Cell(100,5,'TOTAL EMPLOYEES REPORTED IN THIS DISKETTE','0','','L',0);
			$this->Cell(2,5,':','0','','C',0);
			$this->Cell(10,5,$cntEmpNo ,'0','1','C',0);
			
			//echo $cntEmpNo."<br>";
			
			$this->Ln();
			$this->Ln();
			$this->Ln();
			
			$this->Cell(30,5,'RECEIVED BY :','0','','L',0);
			$this->Cell(107,5,'________________________________________','0','','L',0);
			$this->Cell(60,5,'CERTIFIED CORRECT AND PAID:','0','1','L',0);
			$this->Ln();
			$this->Ln();
			$this->Cell(30,5,'DATE RECEIVED :','0','','L',0);
			$this->Cell(107,5,'________________________________________','0','','L',0);
			$this->Cell(60,5,'____________________________','0','1','L',0);
		}
		
		function firstPage()
		{
			$this->Cell(37,5,'FIRST MONTH','0','','L',0);
			$this->Cell(32,5,'0.00','0','','R',0);
			$this->Cell(32,5,'0.00','0','','R',0);
			$this->Cell(20,5,'0.00','0','','R',0);
			$this->Cell(32,5,'0.00','0','0','R',0);
			$this->Cell(42,5,'','0','1','R',0);
		}
		
		function secondPage()
		{
			$this->Cell(37,5,'SECOND MONTH','0','','L',0);
			$this->Cell(32,5,'0.00','0','','R',0);
			$this->Cell(32,5,'0.00','0','','R',0);
			$this->Cell(20,5,'0.00','0','','R',0);
			$this->Cell(32,5,'0.00','0','0','R',0);
			$this->Cell(42,5,'','0','1','R',0);
		}
		
		function thirdPage()
		{
			$this->Cell(37,5,'THIRD MONTH','0','','L',0);
			$this->Cell(32,5,'0.00','0','','R',0);
			$this->Cell(32,5,'0.00','0','','R',0);
			$this->Cell(20,5,'0.00','0','','R',0);
			$this->Cell(32,5,'0.00','0','0','R',0);
			$this->Cell(42,5,'','0','1','R',0);
		}
		
		function Footer_Page($ln,$tot_Sss,$tot_EmpEc,$grand_Sss,$grand_EmpEc)
		{
		}
		
		
	}
	
	$pdf = new PDF('P', 'mm', 'LETTER');
	
	$compCode = $_GET["compCode"];
	$arrcompName = $pagRemObj->getCompany($compCode);
	if ($_SESSION['company_code'] == 4) {
		if ($_GET['location'] == '0001') {
			$compSSSNo = "03-9080013-0";
			$pdf->compAdd = substr("3RD FLR  TABACALERA BLDG., 9000 D. ROMUALDEZ ST. ERMITA MANILA", 0, 53);
			$pdf->compTin = $arrcompName["compTin"];
			$pdf->compTel = substr($arrcompName["compTelNo"], 0, 14);
			$pdf->compName = substr("PUREGOLD DUTY FREE, INC. MANILA", 0, 53);
		}else{
			$compSSSNo = $arrcompName["compSssNo"];
			$pdf->compAdd = substr($arrcompName["compAddr1"]." ".$arrcompName["compAddr2"], 0, 53);
			$pdf->compTin = $arrcompName["compTin"];
			$pdf->compTel = substr($arrcompName["compTelNo"], 0, 14);
			$pdf->compName = substr($arrcompName["compName"], 0, 53);
		}
	} else {
		$compSSSNo = $arrcompName["compSssNo"];
		$pdf->compAdd = substr($arrcompName["compAddr1"]." ".$arrcompName["compAddr2"], 0, 53);
		$pdf->compTin = $arrcompName["compTin"];
		$pdf->compTel = substr($arrcompName["compTelNo"], 0, 14);
		$pdf->compName = substr($arrcompName["compName"], 0, 53);
	}	
	$pdf->compSssNo = $compSSSNo;
	
	
	$pdYear = $_GET["pdYear"];
	$pdf->pdYear = $pdYear;
	
	$pdMonth = $_GET["pdMonth"];
	$pdf->pdMonth = $pdMonth;
	
	
	if($_GET['pdMonth']==1 or $_GET['pdMonth']==2 or $_GET['pdMonth']==3) 
		$pdf->appquter = '03/31/'.date("Y");
	elseif($_GET['pdMonth']==4 or $_GET['pdMonth']==5 or $_GET['pdMonth']==6)
		$pdf->appquter = '06/30/'.date("Y");
	elseif($_GET['pdMonth']==7 or $_GET['pdMonth']==8 or $_GET['pdMonth']==9)
		$pdf->appquter = '09/31/'.date("Y");
	else 
		$pdf->appquter = '12/31/'.date("Y");
	
	if($pdMonth==1 or $pdMonth==4 or $pdMonth==7 or $pdMonth==10) //Falls on Jan, Apr, Jul, or Oct
		$pdf->line_Row = 1;
	elseif($pdMonth==2 or $pdMonth==5 or $pdMonth==8 or $pdMonth==11) //falls on FEB, MAY, AUG, & NOV
		$pdf->line_Row = 2;
	else  // falls on MAR, JUN, SEP, & DEC
		$pdf->line_Row = 3;
	
	
	$location  = ($_SESSION['company_code'] == 4) ? ",'{$_GET['location']}'":"";

	$qrymtdGovtHist = "Select * from view_MTDGovthist where pdYEar=$pdYear and pdMonth=$pdMonth";
	
	$resmtdGovtHist = $pagRemObj->execQry($qrymtdGovtHist);
	$arrmtdGovtHist = $pagRemObj->getArrRes($resmtdGovtHist);
	$arrSSSExempt = $pagRemObj->getSSSExemptEmployee();
	if(count($arrmtdGovtHist)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->pagRemDetails($arrmtdGovtHist,$arrSSSExempt);
	}
	
	
	$pdf->Output();
?>