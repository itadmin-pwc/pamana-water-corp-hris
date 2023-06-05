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
			
			$this->Ln(5);	
			$this->SetFont('ARIAL','B','7'); 	
			$this->Cell(25,6,'SSS No.',1,'','C');
			$this->Cell(55,6,'Name of Borrower',1);
			$this->Cell(10,6,'LT',1,'','C');
			$this->Cell(25,6,'Date Granted',1,'','C');
			$this->Cell(20,6,'Amount of Loan',1,'','C');
			$this->Cell(20,6,'1st Mo. of Qtr',1,'','C');
			$this->Cell(20,6,'2nd Mo. of Qtr',1,'','C');
			$this->Cell(20,6,'3rd Mo. of Qtr',1,'','C');
			$this->Cell(20,6,'TOTAL',1,'','C');
			$this->Cell(45,6,'REMARKS',1,'1','C');
			$this->SetFont('ARIAL','','7'); 	
				
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
		
		
		function SSSData($arrSSSData,$arrPayments,$printedby,$printedby_pos, $arrSSSLoanAdj)
		{
			$this->SetFont('ARIAL','','7'); 
			$ctr = $cnt_grand = 1;
			$cntRecords = count($arrSSSData);
			foreach($arrSSSData as $SSSval)
			{
				//echo $SSSval['empLastName']." = ";
				$this->Cell(25,6,substr_replace(substr_replace($SSSval['empSssNo'], '-', 2,0),'-',10,0),1,'','C');
				$this->Cell(55,6,$SSSval['empLastName'].", ".$SSSval['empFirstName'] ." " .$SSSval['empMidName'][0].".",1);
				$this->Cell(10,6,'S',1,'','C');
			
				if ($SSSval['lonGranted'] != "")
					$this->Cell(25,6,date('m/d/Y',strtotime($SSSval['lonGranted'])),1,'','C');
				else 
					$this->Cell(25,6,'',1);
					
				$this->Cell(20,6,number_format($SSSval['lonAmt'],2),1,'','R');
				
				foreach($arrPayments as $val) 
				{
					$empActualAmt = 0;
					if (($val['empNo'] == $SSSval["empNo"]) && ($val['pdNumber'] == $SSSval["pdNumber"]))
					{
						$empActualAmt = $val['ActualAmt'];
						foreach($arrSSSLoanAdj  as $arrSSSLoanAdj_val)
						{
							if($val['empNo']==$arrSSSLoanAdj_val["empNo"])
							$empActualAmt+=$arrSSSLoanAdj_val["trnAmountD"];
						}
						
						if($this->pdMonth==1 or $this->pdMonth==4 or $this->pdMonth==7 or $this->pdMonth==10) //Falls on Jan, Apr, Jul, or Oct
						{
							$this->Cell(20,6,number_format($empActualAmt,2),1,'','R');
							$this->Cell(20,6,'0.00',1,'','R');
						}
						elseif($this->pdMonth==2 or $this->pdMonth==5 or $pdMonth==8 or $this->pdMonth==11) //falls on FEB, MAY, AUG, & NOV
						{
							$this->Cell(20,6,'0.00',1,'','R');
							$this->Cell(20,6,number_format($empActualAmt,2),1,'','R');
							$this->Cell(20,6,'0.00',1,'','R');
						}
						else  // falls on MAR, JUN, SEP, & DEC
						{
							$this->Cell(20,6,'0.00',1,'','R');
							$this->Cell(20,6,'0.00',1,'','R');
							$this->Cell(20,6,number_format($empActualAmt,2),1,'','R');
							//echo $val['ActualAmt']."=".$SSSval["pdNumber"]."<br>";
						}
						
						$sum_ActualAmt+=$empActualAmt;
						$grand_ActualAmt+=$empActualAmt;
						
						
						
						$this->Cell(20,6,number_format($empActualAmt,2),1,'','R');
					}
				}
				
				$this->Cell(45,6,'',1,1);
				
				if(($cnt==19) or ($cnt_grand==$cntRecords))
				{
					$this->Cell(115,5,'','','','C');
					$this->Cell(20,5,'PAGE TOTAL');
					
					if($this->pdMonth==1 or $this->pdMonth==4 or $this->pdMonth==7 or $this->pdMonth==10) //Falls on Jan, Apr, Jul, or Oct
					{
						$this->Cell(20,6,number_format($sum_ActualAmt,2),1,'','R');
						$this->Cell(20,6,'0.00',1,'','R');
						$this->Cell(20,6,'0.00',1,'','R');
					}
					elseif($this->pdMonth==2 or $this->pdMonth==5 or $pdMonth==8 or $this->pdMonth==11) //falls on FEB, MAY, AUG, & NOV
					{
						$this->Cell(20,6,'0.00',1,'','R');
						$this->Cell(20,6,number_format($sum_ActualAmt,2),1,'','R');
						$this->Cell(20,6,'0.00',1,'','R');
					}
					else  // falls on MAR, JUN, SEP, & DEC
					{
						$this->Cell(20,6,'0.00',1,'','R');
						$this->Cell(20,6,'0.00',1,'','R');
						$this->Cell(20,6,number_format($sum_ActualAmt,2),1,'','R');
						
					}
					
					
					$this->Cell(20,6,number_format($sum_ActualAmt,2),1,'1','R');
					
					if($cnt_grand==$cntRecords)
					{
						$this->Cell(115,5,'','','','C');
						$this->Cell(20,5,'GRAND TOTAL');
						if($this->pdMonth==1 or $this->pdMonth==4 or $this->pdMonth==7 or $this->pdMonth==10) //Falls on Jan, Apr, Jul, or Oct
						{
							$this->Cell(20,6,number_format($grand_ActualAmt,2),1,'','R');
							$this->Cell(20,6,'0.00',1,'','R');
							$this->Cell(20,6,'0.00',1,'','R');
						}
						elseif($this->pdMonth==2 or $this->pdMonth==5 or $pdMonth==8 or $this->pdMonth==11) //falls on FEB, MAY, AUG, & NOV
						{
							$this->Cell(20,6,'0.00',1,'','R');
							$this->Cell(20,6,number_format($grand_ActualAmt,2),1,'','R');
							$this->Cell(20,6,'0.00',1,'','R');
						}
						else  // falls on MAR, JUN, SEP, & DEC
						{
							$this->Cell(20,6,'0.00',1,'','R');
							$this->Cell(20,6,'0.00',1,'','R');
							$this->Cell(20,6,number_format($grand_ActualAmt,2),1,'','R');
							
						}
						$this->Cell(20,6,number_format($grand_ActualAmt,2),1,'1','R');
					}
					
					$this->Cell(215,6.5,'CERTIFIED CORRECT:',0,1,'R');		
					
					$this->Ln();	
					$cnt=$sum_ActualAmt=0;
				}
				
				
				
				$cnt++;
				$cnt_grand++;
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
	$pdf->pdYear = $pdYear;
	
	$pdMonth = $_GET["pdMonth"];
	$pdf->pdMonth = $pdMonth;
	
	
	$arrSSSLoan = $pagRemObj->Loans($compCode, $pdYear, $pdMonth,1);
	$arrSSSLoanAdj = $pagRemObj->loanAdjustment($pdYear,$pdMonth, '5902');
	
	$arrMonths = $pdf->Quarter($_GET["pdMonth"]);
	$arrLoanPayments = $pagRemObj->LoanQuartPayments($compCode, $pdYear, $pdMonth,1);
	if(count($arrSSSLoan)>0)
	{
		$pdf->AddPage();
		$arrprintedby = $pagRemObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$printedby= $arrprintedby["empLastName"].", ".$arrprintedby['empFirstName']." ".$arrprintedby['empMidName'][0].".";
		
		$arrprintedby_pos = $pagRemObj->getpositionwil(" where divCode='".$arrprintedby["empDiv"]."' and deptCode='".$arrprintedby["empDepCode"]."' and sectCode='".$arrprintedby["empSecCode"]."' and posCode='".$arrprintedby["empPosId"]."'",2);
		$printedby_pos= trim(substr($arrprintedby_pos["posDesc"], 0, 30));
		
		$pdf->SSSData($arrSSSLoan,$arrLoanPayments,$printedby,$printedby_pos, $arrSSSLoanAdj);
	}
	
	$pdf->Output();
?>