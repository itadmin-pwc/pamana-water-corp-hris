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
	
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
			
			
			$this->SetFont('Courier','','10'); 
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$this->Cell(140,5,$this->compName,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(70,5,"Report ID: DEDUCT003");
			$hTitle = " Deductions Register for the Period of ".$this->pdHeadTitle;
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			$this->SetFont('Courier','','10');
			
			$this->Cell(45,5,'NAME','','','L');
			$this->Cell(31,5,'W/TAX','','','R');
			$this->Cell(28,5,'SSS','','','R');
			$this->Cell(28,5,'PAG - IBIG','','','R');
			$this->Cell(31,5,'PHILHEALTH','','','R');
			$this->Cell(35,5,'LOANS','','','R');
			$this->Cell(35,5,'OTHER DED.','','','R');
			$this->Cell(33,5,'TOTAL','','','R');
			
			$this->Ln();
			$this->Cell(45,5,'EMP. NO','','','L');
			
			$this->Ln();
		}
		
		function getDeductionsData($recode)
		{
			$qryDed = "SELECT empNo,SUM(".$this->reportType.".trnAmountD) AS totAmt
						FROM ".$this->reportType." INNER JOIN
						tblPayTransType ON ".$this->reportType.".trnCode = tblPayTransType.trnCode
						WHERE (tblPayTransType.compCode = '".$_SESSION["company_code"]."') AND (".$this->reportType.".compCode = '".$_SESSION["company_code"]."') 
						AND 
						(".$this->reportType.".pdYear = '".$this->pdYear."') AND (".$this->reportType.".pdNumber ='".$this->pdNumber."') AND (tblPayTransType.trnRecode = '$recode')
						GROUP BY ".$this->reportType.".empNo,empNo";
			
			$resDed = $this->execQry($qryDed);
			$resDed = $this->getArrRes($resDed);
			return $resDed;
		}
		
		
		function getloans() 
		{
			 
			 $qry = "SELECT empNo,SUM(".$this->reportType.".trnAmountD) AS totAmt
					FROM ".$this->reportType."
					WHERE  (".$this->reportType.".compCode = '".$_SESSION["company_code"]."') AND 
					(".$this->reportType.".pdYear = '".$this->pdYear."') AND (".$this->reportType.".pdNumber = '".$this->pdNumber."') 
					AND (".$this->reportType.".trnCode IN (Select trnCode from tblLoanType where compCode = '".$_SESSION["company_code"]."' and lonTypeStat='A'))
					GROUP BY ".$this->reportType.".empNo,empNo";
			$res = $this->execQry($qry);
			return $this->getArrRes($res);
			
		}
		
		function getotherdeductions() 
		{
			  if($this->reportType=='tblDeductionsHist')
			 	$dedTrandtl = "tblDedTranDtlHist";
			 else
			 	$dedTrandtl = "tblDedTranDtl";
			 
			 $qry = "SELECT empNo,SUM(".$this->reportType.".trnAmountD) AS totAmt
					FROM ".$this->reportType."
					WHERE  (".$this->reportType.".compCode = '".$_SESSION["company_code"]."')  AND 
					(".$this->reportType.".pdYear = '".$this->pdYear."') AND (".$this->reportType.".pdNumber = '".$this->pdNumber."') 
					AND (".$this->reportType.".trnCode IN (Select trnCode from ".$dedTrandtl."
					where compCode = '".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."'
					and payCat='".$_SESSION["pay_category"]."' and dedStat='A'))
					GROUP BY ".$this->reportType.".empNo,empNo";
			
			$res = $this->execQry($qry);
			return $this->getArrRes($res);
		}
		
		function displayContent($arrBrnCode, $arrQry,$getLocCodes,$getBrnCodes)
		{
			$this->SetFont('Courier','','10'); 
			$this->Ln();
			
			$empWTotal = $this->getDeductionsData(WTAX);
			$empSSSTotal = $this->getDeductionsData(SSS_CONTRIB);
			$empPAGTotal = $this->getDeductionsData(PAGIBIG_CONTRIB);
			$empPHICTotal = $this->getDeductionsData(PHILHEALTH_CONTRIB);
			$empLOANSTotal = $this->getloans();
			$empOTHDEDTotal = $this->getotherdeductions();
			
			$cntGrnEmp = 0;
			$grndDedTotal = 0;
			$sizeofBrn = sizeof(explode(",",$getBrnCodes))-1;
			foreach($arrBrnCode as $arrBrnCode_val)
			{
				/*Display Per Branch Code*/
				if($arrBrnCode_val["empBrnCode"]!=$tmpBrnCode)
				{
					$ctr_loc = 1;
					
					
					$sizeofLoc = sizeof(explode(",",$getLocCodes[$arrBrnCode_val["empBrnCode"]] = substr($getLocCodes[$arrBrnCode_val["empBrnCode"]],0,strlen($getLocCodes[$arrBrnCode_val["empBrnCode"]]) - 1)));
					$this->SetFont('Courier','','8'); 
					$this->Cell(47,5,"BRANCH = ".$arrBrnCode_val["brn_Desc"],'','','L');
					$brnWTotals = 0;
					$brnSSSTotals=0;
					$brnPAGTotals=0;
					$brnPHICTotals=0;
					$brnLOANSTotals=0;
					$brnOTHDEDotals=0;
					$brnDedTotal = 0;
					$this->Ln();
					$ctr_brn++;
				}
				
				$this->Cell(5,5,'','0','','L');
				
				/*Display Per Location Code*/
				$cntLocEmp = 0;
				$cntBrnEmp = 0;
				$this->Cell(70,5,"LOCATION = ".$arrBrnCode_val["brn_DescLoc"],'0','','L');
				$locWTotals = 0;
				$locSSSTotals=0;
				$locPAGTotals=0;
				$locPHICTotals=0;
				$locLOANSTotals=0;
				$locOTHDEDotals=0;
				$locDedTotal = 0;
				
				$this->Ln();
				
				/*Display Per Employees*/
				foreach($arrQry as $resQryValue)
				{
					/*Check if the Branch Code of the Employee is part of the Displayed Branch*/
						if($arrBrnCode_val["empBrnCode"] == $resQryValue["empBrnCode"])
						{
							$locBasicTotals+=$rowEmpBasic;
							if($arrBrnCode_val["empLocCode"] == $resQryValue["empLocCode"])
							{
								
								$this->SetFont('Courier','','10'); 
								$this->Cell(45,5,$resQryValue["empLastName"].", ".$resQryValue["empFirstName"][0].".".$resQryValue["empMidName"][0].'.','0','','L');
								
								/*Employee WTAX Pay*/
								foreach ($empWTotal  as $empWTotal_Val) {
									if ($empWTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpW=$empWTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(31,5,($rowEmpW!=0?number_format($rowEmpW,2):""),'0','','R');
								
								/*Employee SSS */
								foreach ($empSSSTotal  as $empSSSTotal_Val) {
									if ($empSSSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpSSS=$empSSSTotal_Val["totAmt"];
									}
								}
								$this->Cell(28,5,($rowEmpSSS!=0?number_format($rowEmpSSS,2):""),'0','','R');
								
								/*Employee PAG */
								foreach ($empPAGTotal  as $empPAGTotal_Val) {
									if ($empPAGTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpPAG=$empPAGTotal_Val["totAmt"];
									}
								}
								$this->Cell(28,5,($rowEmpPAG!=0?number_format($rowEmpPAG,2):""),'0','','R');
								
								/*Employee PHIC */
								foreach ($empPHICTotal  as $empPHICTotal_Val) {
									if ($empPHICTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpPHIC=$empPHICTotal_Val["totAmt"];
									}
								}
								$this->Cell(31,5,($rowEmpPHIC!=0?number_format($rowEmpPHIC,2):""),'0','','R');
					
								/*Employee LOANS */
								foreach ($empLOANSTotal  as $empLOANSTotal_Val) {
									if ($empLOANSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpLOANS=$empLOANSTotal_Val["totAmt"];
									}
								}
								$this->Cell(35,5,($rowEmpLOANS!=0?number_format($rowEmpLOANS,2):""),'0','','R');
								
								/*Employee OTH DED*/
								foreach ($empOTHDEDTotal  as $empOTHDEDTotal_Val) {
									if ($empOTHDEDTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTHDED=$empOTHDEDTotal_Val["totAmt"];
									}
								}
								$this->Cell(33,5,($rowEmpOTHDED!=0?number_format($rowEmpOTHDED,2):""),'0','','R');
								
								$empDedTotal = $rowEmpW+$rowEmpSSS+$rowEmpPAG+$rowEmpPHIC+$rowEmpLOANS+$rowEmpOTHDED;
								$this->Cell(35,5,($empDedTotal!=0?number_format($empDedTotal,2):""),'0','','R');
								
								
								$this->Cell(22,5,'','0','','R');
								$this->Ln();
								$this->Cell(47,3,$resQryValue["empNo"],'0','1','L');
								$this->Ln();
								
								$locWTotals+=$rowEmpW;
								$locSSSTotals+=$rowEmpSSS;
								$locPAGTotals+=$rowEmpPAG;
								$locPHICTotals+=$rowEmpPHIC;
								$locLOANSTotals+=$rowEmpLOANS;
								$locOTHDEDTotals+=$rowEmpOTHDED;
								$locDedTotal+=$empDedTotal;
								
								$brnWTotals+=$rowEmpW;
								$brnSSSTotals+=$rowEmpSSS;
								$brnPAGTotals+=$rowEmpPAG;
								$brnPHICTotals+=$rowEmpPHIC;
								$brnLOANSTotals+=$rowEmpLOANS;
								$brnOTHDEDTotals+=$rowEmpOTHDED;
								$brnDedTotal+=$empDedTotal;
								
								
								$grndWTotals+=$rowEmpW;
								$grndSSSTotals+=$rowEmpSSS;
								$grndPAGTotals+=$rowEmpPAG;
								$grndPHICTotals+=$rowEmpPHIC;
								$grndLOANSTotals+=$rowEmpLOANS;
								$grndOTHDEDTotals+=$rowEmpOTHDED;
								$grndDedTotal+=$empDedTotal;
								
								unset($rowEmpW,$rowEmpSSS,$rowEmpPAG,$rowEmpPHIC,$rowEmpLOANS,$rowEmpOTHDED,$empDedTotal);
							
								$cntLocEmp++;
								$cntGrnEmp++;
								
							}
							$cntBrnEmp++;			
						}
					/*End of Check if the Branch Code of the Employee is part of the Displayed Branch*/
				}
				
				$this->Ln();
				$this->SetFont('Courier','','8'); 
				$this->Cell(5,5,'','0','','L');
				$this->Cell(40,5,'LOCATION TOTALS = '.$cntLocEmp,'0','0','L');
				$this->Cell(31,5,($locWTotals!=0?number_format($locWTotals,2):""),'0','0','R');
				$this->Cell(28,5,($locSSSTotals!=0?number_format($locSSSTotals,2):""),'0','0','R');
				$this->Cell(28,5,($locPAGTotals!=0?number_format($locPAGTotals,2):""),'0','0','R');
				$this->Cell(31,5,($locPHICTotals!=0?number_format($locPHICTotals,2):""),'0','0','R');
				$this->Cell(35,5,($locLOANSTotals!=0?number_format($locLOANSTotals,2):""),'0','0','R');
				$this->Cell(33,5,($locOTHDEDTotals!=0?number_format($locOTHDEDTotals,2):""),'0','0','R');
				$this->Cell(35,5,($locDedTotal!=0?number_format($locDedTotal,2):""),'0','0','R');
				
				if($ctr_loc==$sizeofLoc)
				{
					$this->Ln();
					$this->Cell(45,5,'BRANCH TOTALS = '.$cntBrnEmp,'0','','L');
					$this->Cell(31,5,($brnWTotals!=0?number_format($brnWTotals,2):""),'0','0','R');
					$this->Cell(28,5,($brnSSSTotals!=0?number_format($brnSSSTotals,2):""),'0','0','R');
					$this->Cell(28,5,($brnPAGTotals!=0?number_format($brnPAGTotals,2):""),'0','0','R');
					$this->Cell(31,5,($brnPHICTotals!=0?number_format($brnPHICTotals,2):""),'0','0','R');
					$this->Cell(35,5,($brnLOANSTotals!=0?number_format($brnLOANSTotals,2):""),'0','0','R');
					$this->Cell(33,5,($brnOTHDEDTotals!=0?number_format($brnOTHDEDTotals,2):""),'0','0','R');
					$this->Cell(35,5,($brnDedTotal!=0?number_format($brnDedTotal,2):""),'0','0','R');
					if($ctr_brn!=$sizeofBrn)
						$this->AddPage();
				}
			
				$tmpBrnCode = $arrBrnCode_val["empBrnCode"];
				$this->Ln();
				$ctr_loc++;
			}
			$this->Cell(45,5,'GRAND TOTALS = '.$cntGrnEmp,'0','','L');
			$this->Cell(31,5,($grndWTotals!=0?number_format($grndWTotals,2):""),'0','0','R');
			$this->Cell(28,5,($grndSSSTotals!=0?number_format($grndSSSTotals,2):""),'0','0','R');
			$this->Cell(28,5,($grndPAGTotals!=0?number_format($grndPAGTotals,2):""),'0','0','R');
			$this->Cell(31,5,($grndPHICTotals!=0?number_format($grndPHICTotals,2):""),'0','0','R');
			$this->Cell(35,5,($grndLOANSTotals!=0?number_format($grndLOANSTotals,2):""),'0','0','R');
			$this->Cell(33,5,($grndOTHDEDTotals!=0?number_format($grndOTHDEDTotals,2):""),'0','0','R');
			$this->Cell(35,5,($grndDedTotal!=0?number_format($grndDedTotal,2):""),'0','0','R');
			$this->Ln();
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
	$pdf->reportType	= 	$_GET['tbl'];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	$arrPayPd 			= 	$inqTSObj->getSlctdPd($_SESSION["company_code"],$_GET['payPd']);
	$pdf->pdYear		=	$arrPayPd['pdYear'];
	$pdf->pdNumber		=	$arrPayPd['pdNumber'];
	$empNo         		= 	$_GET['empNo'];
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$orderBy       		= 	$_GET['orderBy'];
	$empLoc 			= 	$_GET['empLoc'];
	$empBrnCode 		= 	$_GET['empBrnCode'];
	if (strlen(strpos($_GET['tbl'],'Hist'))==0) 
		$PaySum = "tblPayrollSummary";
	else 
		$PaySum = "tblPayrollSummaryhist";
		
	$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$pdf->pdHeadTitle	=	$inqTSObj->valDateArt($arrPayPd['pdPayable'])." (Group ".$_SESSION[pay_group].", ".$catName['payCatDesc'].")";
	if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
	if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
	
	
	$qryTS = "SELECT * FROM tblEmpMast where empNo in 
			 	(Select empNo from ".$_GET['tbl']." where compCode='".$_SESSION["company_code"]."'
				 and pdYear='".$arrPayPd['pdYear']."' and pdNumber='".$arrPayPd['pdNumber']."' 
				 and trnCode in 
						(Select trnCode from tblPayTransType where compCode ='".$_SESSION["company_code"]."' and trnCat='D' and trnStat='A' ))
				 and empPayGrp='".$_SESSION["pay_group"]."' 
				 AND empNo IN 
				 				(Select empNo from $PaySum where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )
			     $orderby1";
				
	
	$resTS = $inqTSObj->execQry($qryTS);
	$arrTS = $inqTSObj->getArrRes($resTS);
	$getListofBranch = $inqTSObj->getBrnCodes($arrTS);
	$getLocCodes = $inqTSObj->getLocTotals($getListofBranch);
	$getBrnCodes = $inqTSObj->getBrnTotals($getListofBranch);
	
	if($inqTSObj->getRecCount($resTS)>0)
	{
		
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($getListofBranch,$arrTS,$getLocCodes,$getBrnCodes);
	}
	$pdf->Output('deductions_list.pdf','D');
	
?>