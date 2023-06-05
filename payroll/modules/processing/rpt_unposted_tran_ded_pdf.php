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
				$this->Cell(70,5,"Run Date: " . $newdate);
				$this->Cell(140,5,$this->company,'0','','C');
				$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
				$this->Ln();
				
				if($this->reportType==1){
					$this->Cell(70,5,"Report ID: UNTRANOTHDED01");
					$this->Cell(140,5,'Unposted Payroll Transactions for Other Deductions','0','','C');
				}
				else{
					$this->Cell(70,5,"Report ID: UNTRANOTHEARN01");
					$this->Cell(140,5,'Unposted Payroll Transactions for Other Earnings','0','','C');
				}
				
				$this->Ln();
				
				$this->Cell(335,3,'','');
				$this->Ln();
				$this->SetFont('Courier','B','9');
				$this->Cell(40,6,'',0); 
				$this->Cell(37,6,'BRANCH',0,'','L');
				$this->Cell(30,6,'LOCATION',0,'','L');
				$this->Cell(20,6,'EMP. NO.',0,'','L');
				$this->Cell(40,6,'EMPLOYEE NAME',0,'','L');
				$this->Cell(25,6,'CNTRL. NO.',0,'','L');
				$this->Cell(25,6,'AMOUNT',0,'','R');
				$this->Cell(25,6,'AMT. DED.',0,'','R');
				$this->Cell(25,6,'AMT. DIFF.',0,'','R');
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
			$sumDiffUnposted = 0;
			$grandsumamt = 0;
			$grandDiffUnposted = 0;
			$arrDedList=$this->countotherded();
			
			foreach ($arrDedList as $dedListValue) {
					$this->SetFont('Courier','B','9');
					$this->Cell(40,6,strtoupper($dedListValue['trnDesc']),0,'','L');
					$this->Ln();
					$this->SetFont('Courier','','9'); 
					$sumTrnAmt = 0;
					$diffUnposted = 0;
					foreach($resEmpList as $empValue) {
						
						if($empValue["trnCode"]==$dedListValue['trnCode'])
						{
							/*if($tmp_Code!=$dedListValue['trnCode']){
								$tmp_Brnch = "";
								if($tmp_Brnch!=$empValue['brnDesc']){
									$dispBrnch = $empValue['brnDesc'];
								}
								else{
									$dispBrnch = "";
								}
							}
							else{
									$dispBrnch = "";
								}*/
								
							if($tmp_Code!=$dedListValue['trnCode']){
								$tmp_Brnch = "";
								if($tmp_Brnch!=$empValue['brnDesc']){
									$dispBrnch = $empValue['brnDesc'];
								}
								else{
									$dispBrnch = "";
								}
								
							}
							else
							{
								if($tmp_Brnch!=$empValue['brnDesc']){
									$dispBrnch =$empValue['brnDesc'];
								}
								else{
									$dispBrnch = "";
								}
								
							}
							
							$empDedsum=0;
							$this->Cell(40,6,$ctr,0,'','C');
							$this->Cell(37,6,$dispBrnch,0);
							$this->Cell(30,6,$empValue['locDesc'],0);
							$this->Cell(20,6,$empValue['empNo'],0);
							$this->Cell(40,6,$empValue['empLastName'] . ", ". $empValue['empFirstName'][0].". ". $empValue['empMidName'][0].". " ,0);
							$this->Cell(25,6,$empValue['trnCntrlNo'],0);
							$this->Cell(25,6,number_format($empValue["trnAmt"],2),0,'','R');
							$this->Cell(25,6,number_format($empValue["trnActualAmt"],2),0,'','R');
							$diffUnposted = $empValue["trnAmt"] - $empValue["trnActualAmt"];
							$this->Cell(25,6,sprintf("%01.2f",$diffUnposted),0,'','R');
							$sumTrnAmt+=$empValue["trnActualAmt"];
							$sumDiffUnposted+=$diffUnposted;
							$this->Ln();
							$tmp_Brnch = $empValue['brnDesc'];
							$tmp_Code = $dedListValue['trnCode'];
							
						}
					}
					$this->Cell(77,6,'',0); 
					$this->SetFont('Courier','B','9');
						$this->Cell(140,6,'SUB - TOTAL',0,'','L');
						$this->Cell(25,6,number_format($sumTrnAmt,2),0,'','R');
						$this->Cell(25,6,number_format($sumDiffUnposted,2),0,'','R');
						$grandsumamt+=$sumTrnAmt;
						$grandDiffUnposted+=$sumDiffUnposted;
					$this->SetFont('Courier','','9'); 
					$this->Ln();
			}
					$this->Cell(77,6,'',0); 
					$this->SetFont('Courier','B','9');
						$this->Cell(140,6,'GRAND TOTAL',0,'','L');
						$this->Cell(25,6,number_format($grandsumamt,2),0,'','R');
						$this->Cell(25,6,number_format($grandDiffUnposted,2),0,'','R');
					$this->SetFont('Courier','','9'); 
		}
		
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(260,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','',9);



			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
		
	}
	
	
	$pdf = new PDF('L', 'mm', 'LETTER');
	$pdf->company = $UnpostedTranObj->getCompanyName($_SESSION['company_code']);
	
	$pdNumber = $_GET["pdNum"];
	$pdYear = $_GET["pdYear"];
	$pdf->pdNumber = $pdNumber;
	$pdf->pdYear = $pdYear;
	$pdf->reportType = 1;
	
	
	$qryIntMaxRec	= 	"Select distinct empmast.empNo, empLastName,empFirstName,empMidName, unpostTran.trnCode, trnAmt, trnActualAmt, trnCntrlNo, trnDesc,empBrnCode,empLocCode, tblBrnDesc.brnShortDesc as brnDesc, tblLocDesc.brnShortDesc as locDesc 
						from tblEmpMast empmast,tblUnpostedTran unpostTran, tblPayTransType ptTrans, tblBranch tblBrnDesc, tblBranch tblLocDesc 
						where 
						empmast.empNo=unpostTran.empNo 
						AND empBrnCode=tblBrnDesc.brnCode
						AND empLocCode=tblLocDesc.brnCode
						AND empmast.compCode = '".$_SESSION['company_code']."'
						AND empStat NOT IN('RS','IN','TR') 
						AND emppayCat = '" . $_SESSION['pay_category'] . "' 
						AND emppayGrp = '" . $_SESSION['pay_group'] . "'
						AND pdNumber='".$pdNumber."' 
						AND pdYear='".$pdYear."' 
						AND unpostTran.trnCode=ptTrans.trnCode 
						AND ptTrans.compCode='".$_SESSION['company_code']."'
						AND trnCat='D'
						ORDER BY brnDesc, locDesc,trnDesc,empLastName, empFirstName ";
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
	
	$qryIntMaxRec	= "Select distinct empmast.empNo, empLastName,empFirstName,empMidName, trnCode, trnAmt, trnCntrlNo,empBrnCode,empLocCode, tblBrnDesc.brnShortDesc as brnDesc, tblLocDesc.brnShortDesc as locDesc 
						from tblEmpMast empmast,tblUnpostedTran unpostTran, tblBranch tblBrnDesc, tblBranch tblLocDesc 
						where 
							empmast.empNo=unpostTran.empNo
							AND empBrnCode=tblBrnDesc.brnCode
							AND empLocCode=tblLocDesc.brnCode
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
							ORDER BY brnDesc, locDesc,empLastName, empFirstName";
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



