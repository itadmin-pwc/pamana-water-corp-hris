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
			$this->Image(PAGLOAN_HEADER, 8, 5 , '200' , '50' , 'JPG', '');
			$this->SetFont('Courier','B','8'); 
			
			$month = $_GET["pdMonth"]."/".date("d")."/".$_GET["pdYear"];
			$this->Ln(16);
			$this->Cell(148,5,'','0','','',0);
			$this->Cell(25,5,date("m", strtotime($month))." - ".date("M", strtotime($month)),'0','','',0);
			$this->Cell(20,5,date("Y", strtotime($month)),'0','','R',0);
			$this->Image(PAGLOAN_BODY, 8, 55 , '200' , '180' , 'JPG', '');
			$this->Ln(8);
			$this->Cell(3,5,'','0','','',0);
			$this->Cell(95,5,$this->compName,'0','','',0);
			$this->Cell(20,5,'','0','','',0);
			$this->Cell(28,5,$this->compSssNo,'0','','R',0);
			$this->Ln(7);
			$this->Cell(3,5,'','0','','',0);
			$this->Cell(95,5,$this->compAdd,'0','','',0);
			$this->Cell(36,5,$this->compTin,'0','','C',0);
			$this->Cell(23,5,$this->compZipCode,'0','','C',0);
			$this->Cell(36,5,$this->compTel,'0','','C',0);
			$this->Ln(14);
		}
		
		function pagRemDetails($arrLoansAdj,$printedby,$printedby_pos)
		{
			
			$this->SetFont('Courier','','8'); 
			$cntRecords = count($arrLoansAdj);
			$cnt = 1;
			$emp_cnt = 1;
			$sumhdmfEmp=$sumhdmfEmplr=$sumhdmfEmpEmplr=$sumCnt=0;
			$grandhdmfEmp=$grandhdmfEmplr=$grandhdmfEmpEmplr=0;
			
			//$arrLoansAdj = substr($arrLoansAdj,0,strlen($arrLoansAdj) - 1);
			foreach($arrLoansAdj as $arrLoansAdj_val)
			{
				
				$arrmtdGovtHist_val = explode("*", $arrLoansAdj_val);
				
				
				$sumCnt = 0;
				if($cnt==56)
				{
					$cnt = 1;
					$sumhdmfEmp=$sumhdmfEmplr=$sumhdmfEmpEmplr=$sumCnt=0;
					$this->AddPage();
				}
			
				$this->Cell(2,3,'','0','0','',0);
				$this->Cell(25,3,($arrmtdGovtHist_val["0"]!=""?$arrmtdGovtHist_val["0"]:date("m/d/Y", strtotime($arrmtdGovtHist_val["1"]))),'0','0','L',0);
				$this->Cell(25,3,$arrmtdGovtHist_val["2"],'0','0','',0);
				$this->Cell(29,3,trim(substr($arrmtdGovtHist_val["3"], 0, 15)),'0','0','',0);
				$this->Cell(29,3,trim(substr($arrmtdGovtHist_val["4"], 0, 15)),'0','0','',0);
				$this->Cell(18,3,trim(substr($arrmtdGovtHist_val["5"], 0, 10)),'0','0','',0);
				
				$this->Cell(21,3,$arrmtdGovtHist_val["6"],'0','0','R',0);
				$this->Cell(22,3,'','0','0','R',0);
				$this->Cell(22,3,'','0','1','R',0);
				
				$sumhdmfEmp+=$arrmtdGovtHist_val["6"];
				$sumhdmfEmplr+=$arrmtdGovtHist_val["hdmfEmplr"];
				$sumhdmfEmpEmplr+=$sumCnt;
				
				$grandhdmfEmp+=$arrmtdGovtHist_val["6"];
				$grandhdmfEmplr+=$arrmtdGovtHist_val["hdmfEmplr"];
				$grandhdmfEmpEmplr+=$sumCnt;
			
			
			
				if($emp_cnt==$cntRecords)
					{
						if($cnt<55)
						{
							$rem_cnt_emp = 55-$cnt;
							for($add_line = 1; $add_line<=$rem_cnt_emp; $add_line++)
							{
								$this->Cell(25,3,'','0','1','',0);
							}
						}
						
						$this->Footer_Page($cnt,$emp_cnt,$sumhdmfEmp,$sumhdmfEmplr,$sumhdmfEmpEmplr,$grandhdmfEmp,$grandhdmfEmplr,$grandhdmfEmpEmplr,$printedby,$printedby_pos);
					}
					else
					{
						if($cnt==55)
						{
							$this->Footer_Page($cnt,'',$sumhdmfEmp,$sumhdmfEmplr,$sumhdmfEmpEmplr,'','','',$printedby,$printedby_pos);
						}
					}
					
					$cnt++;
					$emp_cnt++;
				
			}
			
			
		}
		
		function Footer_Page($tot_emp_wpage,$tot_emp,$sumhdmfEmp,$sumhdmfEmplr,$sumhdmfEmpEmplr,$grandhdmfEmp,$grandhdmfEmplr,$grandhdmfEmpEmplr,$printedby,$printedby_pos)
		{
			$this->SetFont('Courier','B','8'); 
			$this->Image(PAG_FOOTER, 8, 228 , '200' , '40' , 'JPG', '');
			$this->Ln(10);
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
			$this->Cell(12,5,$this->PageNo(),'0','','',0);
			$this->Cell(12,5,'{nb}','0','','',0);
			$this->SetFont('Courier','','7'); 
			
		}
		
	}
	
	$pdf = new PDF('P', 'mm', 'LETTER');
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
	
	$arrmtdGovtHist = $pagRemObj->Loans($_SESSION["company_code"],$pdYear,$pdMonth,2);
	$arrmtdDeductHist = $pagRemObj->loanAdjustment($pdYear,$pdMonth);
	
	foreach($arrmtdGovtHist as $arrmtdGovtHist_val)
		$arrEmpLoans.=$arrmtdGovtHist_val["empPagibig"]."*".$arrmtdGovtHist_val["empBday"]."*".str_replace('*','',$arrmtdGovtHist_val["lonRefNo"])."*".$arrmtdGovtHist_val["empLastName"]."*".$arrmtdGovtHist_val["empFirstName"]."*".$arrmtdGovtHist_val["empMidName"]."*".$arrmtdGovtHist_val["Amount"]."+";
	
	foreach($arrmtdDeductHist as $arrmtdDeductHist_val)
		$arrEmpLoans.=$arrmtdDeductHist_val["empPagibig"]."*".$arrmtdDeductHist_val["empBday"]."*".str_replace('*','',$arrmtdDeductHist_val["lonRefNo"])."*".$arrmtdDeductHist_val["empLastName"]."*".$arrmtdDeductHist_val["empFirstName"]."*".$arrmtdDeductHist_val["empMidName"]."*".$arrmtdDeductHist_val["trnAmountD"]."+";
	
	
	$arrEmpLoans = substr($arrEmpLoans,0,strlen($arrEmpLoans) - 1);
	
	$expEmpLoans = explode("+", $arrEmpLoans);
	
	if(count($expEmpLoans)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$arrprintedby = $pagRemObj->getUserInfo($_SESSION["company_code"],$arrcompName['compRemSign_EmpNo'],''); 
		$printedby= $arrprintedby["empLastName"].", ".$arrprintedby['empFirstName']." ".$arrprintedby['empMidName'][0].".";
		
		$arrprintedby_pos = $pagRemObj->getpositionwil(" where divCode='".$arrprintedby["empDiv"]."' and deptCode='".$arrprintedby["empDepCode"]."' and sectCode='".$arrprintedby["empSecCode"]."' and posCode='".$arrprintedby["empPosId"]."'",2);
		$printedby_pos= trim(substr($arrprintedby_pos["posDesc"], 0, 30));
		
		$pdf->pagRemDetails($expEmpLoans,$printedby,$printedby_pos);
	}
	
	$pdf->Output();
?>