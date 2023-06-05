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
			$this->SetFont('ARIAL','','8'); 
			
			$month = $_GET["pdMonth"]."/".date("d")."/".$_GET["pdYear"];
			$this->Cell(48,5,'DRM ML-2 (Rev. 3/98)','0','','');
			$this->SetFont('ARIAL','','10'); 
			$this->Cell(169,5,'Republic of the Philippines',0,'','C');
			$this->Cell(48,5,'','0',1,'');
			$this->SetFont('ARIAL','B','13'); 
			$this->Cell(265,5,'S O C I A L   S E C U R I T Y   S Y S T E M',0,1,'C');
			$this->SetFont('ARIAL','','10'); 
			$this->Cell(265,5,'QUEZON CITY',0,1,'C');
			$this->SetFont('ARIAL','B','13'); 
			$this->Cell(265,5,'COLLECTION LIST',0,1,'C');
			$this->SetFont('ARIAL','','10'); 
			$this->Ln(10);
			$this->Cell(45,5,'Name of Employer :',0,0);
			$this->Cell(45,5,$this->compName,0,0);
			$this->Cell(85,5,'');
			$this->Cell(45,5,'Page '.$this->PageNo()." of {nb} Page(s)",0,1);
			$this->Cell(45,5,'SSS NO. of Employer :',0,0);
			$this->Cell(45,5,substr_replace(substr_replace($this->compSssNo, '-', 2,0),'-',10,0),0,0);
			$this->Cell(85,5,'');
			$arrQEnd = $this->Quarter($_GET["pdMonth"]);
			$this->Cell(45,5,'Quarter Ending : '.$arrQEnd['QuarterEnding'],0,1);
				
		}
		function Quarter($month) {
			if ($month == 1 ||$month == 2 ||$month == 3) {
				$arrQuarter['QuarterEnding'] = "March 31, ".date('Y');
				$arrQuarter['pdMonth'] = "1,2,3";
			} elseif ($month == 4 ||$month == 5 ||$month == 6) {
				$arrQuarter['QuarterEnding'] = "June 30, ".date('Y');
				$arrQuarter['pdMonth'] = "4,5,6";
			} elseif ($month == 7 ||$month == 8 ||$month == 9) {
				$arrQuarter['QuarterEnding'] = "September 30, ".date('Y');
				$arrQuarter['pdMonth'] = "7,8,9";
			} elseif ($month == 10 ||$month == 11 ||$month == 12) {
				$arrQuarter['QuarterEnding'] = "December 31, ".date('Y');
				$arrQuarter['pdMonth'] = "10,11,12";
			}			
			return	$arrQuarter;	
		}
		
		function GetQuartPayments($arrPayments,$empNo) {
			$ptr=1;
			$totPaymnts = 0;
			$arrPayment['m1'] = 0;
			$arrPayment['m2'] = 0;
			$arrPayment['m3'] = 0;
			foreach($arrPayments as $val) {
				if ($val['empNo'] == $empNo) {
					if ($ptr==1) {
						$this->Cell(20,6,number_format($val['ActualAmt'],2),1,'','R');
						$totPaymnts += $val['ActualAmt'];
						$arrPayment['m1'] = $val['ActualAmt'];
						$ptr=2;
					}
					if ($ptr==2) {
						$this->Cell(20,6,number_format($val['ActualAmt'],2),1,'','R');
						$totPaymnts += $val['ActualAmt'];
						$arrPayment['m2'] = $val['ActualAmt'];
						$ptr=3;
					}
					if ($ptr==3) {
						$this->Cell(20,6,number_format($val['ActualAmt'],2),1,'','R');
						$totPaymnts += $val['ActualAmt'];
						$arrPayment['m3'] = $val['ActualAmt'];
						$ptr=4;
					}
				}	
			}
			if ($ptr==1) {	
				$this->Cell(20,6,'0.00',1,'','R');
				$this->Cell(20,6,'0.00',1,'','R');
				$this->Cell(20,6,'0.00',1,'','R');
				$this->Cell(20,6,'0.00',1,'','R');
			} elseif ($ptr==2) {	
				$this->Cell(20,6,'0.00',1,'','R');
				$this->Cell(20,6,'0.00',1,'','R');
				$this->Cell(20,6,number_format($totPaymnts,2),1,'','R');
			} elseif ($ptr==3) {	
				$this->Cell(20,6,'0.00',1,'','R');
				$this->Cell(20,6,'0.00',1,'','R');
				$this->Cell(20,6,number_format($totPaymnts,2),1,'','R');
			} elseif ($ptr==4) {
				$this->Cell(20,6,number_format($totPaymnts,2),1,'','R');
			}
			return $arrPayment;
		}
		
		function PageTotal($arrTotal) {
			$this->Ln(3);		
			$this->Cell(25,5,'','','','C');
			$this->Cell(55,5,'');
			$this->Cell(10,5,'');
			$this->Cell(25,5,'');
			$this->Cell(20,5,'PAGE TOTAL');
			$this->Cell(20,5,number_format($arrTotal['m1'],2),0,'','R');
			$this->Cell(20,5,number_format($arrTotal['m2'],2),0,'','R');
			$this->Cell(20,5,number_format($arrTotal['m3'],2),0,'','R');
			$this->Cell(20,5,number_format($arrTotal['m3']+$arrTotal['m2']+$arrTotal['m1'],2),0,1,'R');
			$this->Cell(190,6,'',0,0,'C');		
			$this->Cell(75,6.5,'CERTIFIED CORRECT:',0,1);		
			
		}
		function label() {
			$this->Ln(5);		
			$this->Cell(25,6,'SSS No.',1,'','C');
			$this->Cell(55,6,'Name of Borrower',1);
			$this->Cell(10,6,'LT',1,'','C');
			$this->Cell(25,6,'Date Granted',1,'','C');
			$this->Cell(20,6,'Amount of Loan',1,'','C');
			$this->Cell(20,6,'1st Mo. of Qtr',1,'','C');
			$this->Cell(20,6,'2nd Mo. of Qtr',1,'','C');
			$this->Cell(20,6,'3rd Mo. of Qtr',1,'','C');
			$this->Cell(20,6,'TOTAL',1,'','C');
			$this->Cell(45,6,'REMARKS',1,'','C');
			$this->Ln(6);		
		}
		function SSSData($arrSSSData,$arrPayments,$printedby,$printedby_pos)
		{
				$this->SetFont('ARIAL','','7'); 
				$this->label();

			$cntRecords = count($arrmtdGovtHist);
			$cnt = 0;
			
			foreach($arrSSSData as $SSSval)
			{
				$sumCnt = 0;
				if($cnt==20)
				{
					$cnt = 1;
					
					$this->PageTotal($arrTotal);
					$arrTotal['m1'] = 0;
					$arrTotal['m2'] = 0;
					$arrTotal['m3'] = 0;
					$this->AddPage();
					$this->label();
				}
				

				$this->Cell(25,6,substr_replace(substr_replace($SSSval['empSssNo'], '-', 2,0),'-',10,0),1,'','C');
				$this->Cell(55,6,$SSSval['empLastName'].", ".$SSSval['empFirstName'] ." " .$SSSval['empMidName'][0].".",1);
				$this->Cell(10,6,'S',1,'','C');
				if ($SSSval['lonGranted'] != "") {
					$this->Cell(25,6,date('m/d/Y',strtotime($SSSval['lonGranted'])),1,'','C');
				} else {
					$this->Cell(25,6,'',1);
				}	
				$this->Cell(20,6,number_format($SSSval['lonAmt'],2),1,'','R');
				$arrEmpPaymnts = $this->GetQuartPayments($arrPayments,$SSSval['empNo']);
				$this->Cell(45,6,'',1);
				$this->Ln(6);
					$arrTotal['m1'] += $arrEmpPaymnts['m1'];
					$arrTotal['m2'] += $arrEmpPaymnts['m1'];
					$arrTotal['m3'] += $arrEmpPaymnts['m1'];
				
				$sumhdmfEmp+=$arrmtdGovtHist_val["hdmfEmp"];
				$sumhdmfEmplr+=$arrmtdGovtHist_val["hdmfEmplr"];
				$sumhdmfEmpEmplr+=$sumCnt;
				
				$grandhdmfEmp+=$arrmtdGovtHist_val["hdmfEmp"];
				$grandhdmfEmplr+=$arrmtdGovtHist_val["hdmfEmplr"];
				$grandhdmfEmpEmplr+=$sumCnt;
				
				$cnt++;
			}
			
			if ($cnt<20) {
/*				for($i=0;$i<=(20-$cnt);$i++) {
					$this->Cell(35,6,'',1);
					$this->Cell(45,6,'',1);
					$this->Cell(10,6,'',1,'','C');
					$this->Cell(25,6,'',1);
					$this->Cell(20,6,'',1,'','R');
					$this->Cell(20,6,'',1,'','R');
					$this->Cell(20,6,'',1,'','R');
					$this->Cell(20,6,'',1,'','R');
					$this->Cell(30,6,'',1,'','R');
					$this->Cell(35,6,'',1);
					$this->Ln(6);				
				}
*/				$this->PageTotal($arrTotal);
			}
		}
		
		function Footer_Page($tot_emp_wpage,$tot_emp,$sumhdmfEmp,$sumhdmfEmplr,$sumhdmfEmpEmplr,$grandhdmfEmp,$grandhdmfEmplr,$grandhdmfEmpEmplr,$printedby,$printedby_pos)
		{
			$this->SetFont('Courier','B','8'); 
			$this->Image(PAG_FOOTER, 8, 228 , '200' , '40' , 'JPG', '');
			$this->Ln(55);
			$this->Cell(20,5,'','0','','',0);
			$this->Cell(30,5,$tot_emp_wpage,'0','','C',0);
			$this->Cell(21,5,'','0','','',0);
			$this->Cell(17,5,$tot_emp,'0','','C',0);
			$this->Cell(42,5,'','0','','',0);
			$this->Cell(19,5,sprintf('%.2f',$sumhdmfEmp),'0','','R',0);
			$this->Cell(22,5,sprintf('%.2f',$sumhdmfEmplr),'0','','R',0);
			$this->Cell(22,5,sprintf('%.2f',$sumhdmfEmpEmplr),'0','','R',0);
			$this->Ln(6);
			$this->Cell(130,5,'','0','','',0);
			$this->Cell(19,5,sprintf('%.2f',$grandhdmfEmp),'0','','R',0);
			$this->Cell(22,5,sprintf('%.2f',$grandhdmfEmplr),'0','','R',0);
			$this->Cell(22,5,sprintf('%.2f',$grandhdmfEmpEmplr),'0','','R',0);
			$this->Ln(13);
			$this->Cell(91,5,'','0','','',0);
			$this->Cell(78,5,$printedby,'0','','',0);
			$this->Cell(24,5,date("m/d/Y"),'0','','',0);
			$this->Ln(5);
			$this->Cell(91,5,'','0','','',0);
			$this->Cell(78,5,$printedby_pos,'0','','',0);
			$this->Cell(24,5,'','0','','',0);
			$this->SetFont('Courier','','7'); 
			
		}
		
	}
	
	$pdf = new PDF('L', 'mm', 'LETTER');
	$pdf->AliasNbPages(); 	
	$compCode = $_GET["compCode"];
	$arrcompName = $pagRemObj->getCompany($compCode);
	$pdf->compName = substr($arrcompName["compName"], 0, 53);
	$pdf->compSssNo = substr(str_replace("-","",$arrcompName["compSssNo"]), 0, 10);
	$pdf->compAdd = substr($arrcompName["compAddr1"]." ".$arrcompName["compAddr2"], 0, 53);
	$pdf->compTin = $arrcompName["compTin"];
	$pdf->compTel = substr($arrcompName["compTelNo"], 0, 14);
	$pdf->compZipCode = substr($arrcompName["compZipCode"], 0, 4);
	
	$pdYear = $_GET["pdYear"];
	$pdMonth = $_GET["pdMonth"];
	
	$arrSSSLoan = $pagRemObj->Loans($compCode, $pdYear, $pdMonth,1);
	$arrMonths = $pdf->Quarter($_GET["pdMonth"]);
	$arrLoanPayments = $pagRemObj->LoanQuartPayments($compCode, $pdYear, $arrMonths['pdMonth'],1);
	if(count($arrSSSLoan)>0)
	{
		$pdf->AddPage();
		$arrprintedby = $pagRemObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$printedby= $arrprintedby["empLastName"].", ".$arrprintedby['empFirstName']." ".$arrprintedby['empMidName'][0].".";
		
		$arrprintedby_pos = $pagRemObj->getpositionwil(" where divCode='".$arrprintedby["empDiv"]."' and deptCode='".$arrprintedby["empDepCode"]."' and sectCode='".$arrprintedby["empSecCode"]."'",2);
		$printedby_pos= trim(substr($arrprintedby_pos["posDesc"], 0, 30));
		
		$pdf->SSSData($arrSSSLoan,$arrLoanPayments,$printedby,$printedby_pos);
	}
	
	$pdf->Output();
?>