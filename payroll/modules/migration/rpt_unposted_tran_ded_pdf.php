<?php
	/*
		Created By		:	Genarra Arong
		Date Created	:	01192010
		Reason			:	Report for the Unposted Transactions
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pdf/fpdf.php");
	include("rpt_unposted_tran_obj.php");
	
	$UnpostedTranObj=new rptUnpostedTranObj();
	$sessionVars = $UnpostedTranObj->getSeesionVars();
	
	
	class PDF extends FPDF
	{
		function Header()
		{
			
			
			$arrdedList=$this->countotherded();
			
			if(sizeof($arrdedList)!='0')
			{
				$gmt = time() + (8 * 60 * 60);
				$newdate = date("m/d/Y h:iA", $gmt);
				
				
				$this->SetFont('Courier','','9'); 
				$this->Cell(80,5,"Run Date: " . $newdate);
				$this->Cell(50,5,$this->company);
				$this->Cell(60,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
				$this->Ln();
				
				if($this->reportType==1){
					$this->Cell(80,5,"Report ID: UNTRANOTHDED01");
					$this->Cell(50,5,'Unposted Payroll Transactions for Other Deductions');
				}
				else{
					$this->Cell(80,5,"Report ID: UNTRANOTHEARN01");
					$this->Cell(50,5,'Unposted Payroll Transactions for Other Earnings');
				}
				
				$this->Ln();
				
				$this->Cell(335,3,'','');
				$this->Ln();
				$this->SetFont('Courier','B','9');
				$this->Cell(60,6,'',0); 
				$this->Cell(25,6,'EMP. NO.',1);
				$this->Cell(57,6,'EMPLOYEE NAME',1,'','C');
				$this->Cell(20,6,'CNTRL. NO.',1);
				$this->Cell(25,6,'AMOUNT',1,'','C');
				$this->Ln();
			}
			
		}	
		
		
		function countotherded() {
			
			$qrycount="SELECT trnCode, trnDesc 
						FROM tblPayTransType
								WHERE 	(trnCode IN 
										(Select trnCode from tblUnpostedTran where 
										compCode='".$_SESSION['company_code']."' 
										AND pdNumber='".$this->pdNumber."' 
										AND pdYear='".$this->pdYear."'
										group by trnCode)
								AND compCode='".$_SESSION['company_code']."'
								AND trnCat='".($this->reportType==1?'D':'E')."' and trnStat='A') 
								ORDER BY trnShortDesc";
			
			$rescountotherded = $this->getArrRes($this->execQry($qrycount));
			return $rescountotherded;
		}
		
		
		function otherded($resEmpList) {
			$this->SetFont('Courier','','9'); 
			$sumTrnAmt = 0;
			$grandsumamt = 0;
			$arrDedList=$this->countotherded();
			
			foreach ($arrDedList as $dedListValue) {
					$this->SetFont('Courier','B','9');
					$this->Cell(60,6,strtoupper($dedListValue['trnDesc'])." - (".$dedListValue['trnCode'].")",1,'','L');
					$this->Ln();
					$this->SetFont('Courier','','9'); 
					$sumTrnAmt = 0;
					foreach($resEmpList as $empValue) {
						
						if($empValue["trnCode"]==$dedListValue['trnCode'])
						{
							$empDedsum=0;
							$this->Cell(60,6,$ctr,0,'','C');
							$this->Cell(25,6,$empValue['empNo'],0);
							$this->Cell(57,6,$empValue['empLastName'] . ", ". $empValue['empFirstName'],0);
							$this->Cell(20,6,$empValue['trnCntrlNo'],0);
							$this->Cell(25,6,number_format($empValue["trnAmt"],2),0,'','R');
							$sumTrnAmt+=$empValue["trnAmt"];
							$this->Ln();
						}
					}
					$this->Cell(60,6,'',0); 
					$this->SetFont('Courier','B','9');
						$this->Cell(117,6,'SUB - TOTAL',0,'','L');
						$this->Cell(30,6,number_format($sumTrnAmt,2),0,'','R');
						$grandsumamt+=$sumTrnAmt;
					$this->SetFont('Courier','','9'); 
					$this->Ln();
			}
					$this->Cell(70,6,'',0); 
					$this->SetFont('Courier','B','9');
						$this->Cell(117,6,'GRAND TOTAL',0,'','L');
						$this->Cell(30,6,number_format($grandsumamt,2),0,'','R');
					$this->SetFont('Courier','','9'); 
		}
		
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',9);
			$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
		
	}
	
	
	$pdf = new PDF('P', 'mm', 'LETTER');
	$pdf->company = $UnpostedTranObj->getCompanyName($_SESSION['company_code']);
	
	$pdNumber = 21;
	$pdYear = 2009;
	$pdf->pdNumber = $pdNumber;
	$pdf->pdYear = $pdYear;
	$pdf->reportType = 1;
	
	
	$qryIntMaxRec	= 	"Select empmast.empNo, empLastName,empFirstName, unpostTran.trnCode, trnAmt, trnCntrlNo, trnDesc 
						from tblEmpMast empmast,tblUnpostedTran unpostTran, tblPayTransType ptTrans 
						where 
						empmast.empNo=unpostTran.empNo 
						AND empmast.compCode = '".$_SESSION['company_code']."'
						AND empStat NOT IN('RS','IN','TR') 
						AND emppayCat = '" . $_SESSION['pay_category'] . "' 
						AND emppayGrp = '" . $_SESSION['pay_group'] . "'
						AND pdNumber='".$pdNumber."' 
						AND pdYear='".$pdYear."' 
						AND unpostTran.trnCode=ptTrans.trnCode 
						AND ptTrans.compCode='".$_SESSION['company_code']."'
						AND trnCat='D'
						ORDER BY trnDesc,empLastName, empFirstName ";
	
	$resEmpList = $UnpostedTranObj->execQry($qryIntMaxRec);
	$arrEmpList = $UnpostedTranObj->getArrRes($resEmpList);
	if(count($arrEmpList)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $UnpostedTranObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		
		
		$pdf->otherded($arrEmpList);
	}
	
	$pdf->reportType = 0;
	
	$qryIntMaxRec	= "Select empmast.empNo, empLastName,empFirstName, trnCode, trnAmt, trnCntrlNo
						from tblEmpMast empmast,tblUnpostedTran unpostTran
						where 
							empmast.empNo=unpostTran.empNo
							AND empmast.compCode = '{$sessionVars['compCode']}'
							AND empStat NOT IN('RS','IN','TR') 
							AND emppayCat = '" . $_SESSION['pay_category'] . "' 
							AND emppayGrp = '" . $_SESSION['pay_group'] . "'
							AND pdNumber='".$pdNumber."' 
							AND pdYear='".$pdYear."'
							AND trnCode in 
								(Select trnCode from tblPayTransType 
									where
										compCode = '{$sessionVars['compCode']}' 
										and trnCat='E' 
										and trnStat='A')
							ORDER BY empLastName, empFirstName";
	$resEmpList = $UnpostedTranObj->execQry($qryIntMaxRec);
	$arrEmpList = $UnpostedTranObj->getArrRes($resEmpList);
	if(count($arrEmpList)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $UnpostedTranObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->otherded($arrEmpList);
	}
	
	$pdf->Output();
	
	
	

?>



