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
			
			$this->Cell(70,5,"Report ID: EARNGS002");
			$hTitle = " Earnings Register for the Period of ".$this->pdHeadTitle;
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			$this->SetFont('Courier','','10');
			$this->Cell(45,5,'NAME','','','L');
			
			
			$this->Cell(19,5,'BASIC','','','R');
			$this->Cell(21,5,'OT','','','R');
			$this->Cell(19,5,'ND','','','R');
			$this->Cell(21,5,'HOLIDAY','','','R');
			$this->Cell(18,5,'VL','','','R');
			$this->Cell(19,5,'VL W/','','','R');
			$this->Cell(19,5,'SL W/','','','R');
			$this->Cell(21,5,'ADJS','','','R');
			$this->Cell(21,5,'ALLOW','','','R');
			$this->Cell(19,5,'OTHERS','','','R');
			$this->Cell(22,5,'TOTAL','','','R');
			
			$this->Ln();
			$this->Cell(45,5,'EMP. NO','','','L');
			$this->Cell(19,5,'','','','R');
			$this->Cell(21,5,'','','','R');
			$this->Cell(19,5,'','','','R');
			$this->Cell(21,5,'','','','R');
			
			$this->Cell(18,5,'ENCASH','','','R');
			$this->Cell(19,5,'PAY','','','R');
			$this->Cell(19,5,'PAY','','','R');
			$this->Cell(21,5,'','','','R');
			$this->Cell(21,5,'','','','R');
			$this->Cell(19,5,'','','','R');
			$this->Cell(22,5,'','','','R');
			$this->Ln();
		}
		
		function getEarningsData($recode)
		{
			$qryEarn = "SELECT empNo,SUM(".$this->reportType.".trnAmountE) AS totAmt
						FROM ".$this->reportType." INNER JOIN
						tblPayTransType ON ".$this->reportType.".trnCode = tblPayTransType.trnCode
						WHERE (tblPayTransType.compCode = '".$_SESSION["company_code"]."') AND (".$this->reportType.".compCode = '".$_SESSION["company_code"]."') 
						AND 
						(".$this->reportType.".pdYear = '".$this->pdYear."') AND (".$this->reportType.".pdNumber ='".$this->pdNumber."') AND (tblPayTransType.trnRecode = '$recode')
						and ".$this->reportType.".trnCode not in (Select trnCode from tblAllowType where compCode='".$_SESSION["company_code"]."' and allowTypeStat='A' and sprtPS='Y')
						GROUP BY ".$this->reportType.".empNo,empNo";
			
			
			$resEarn = $this->execQry($qryEarn);
			$resEarn = $this->getArrRes($resEarn);
			return $resEarn;
		}
		
		
		function displayContent($arrBrnCode, $arrQry,$getLocCodes,$getBrnCodes)
		{
			$this->SetFont('Courier','','10'); 
			$this->Ln();
			
			$empBasicTotal = $this->getEarningsData(EARNINGS_RECODEBASIC);
			$empOtTotal = $this->getEarningsData(EARNINGS_RECODEOT);
			$empNdTotal = $this->getEarningsData(EARNINGS_RECODEND);
			$empHolidayTotal = $this->getEarningsData(EARNINGS_RECODEHP);
			$empVLEncashTotal = $this->getEarningsData(EARNINGS_RECODEVLENCASH);
			$empVLWPayTotal = $this->getEarningsData(EARNINGS_RECODEVLWPAY);
			$empSLWPayTotal = $this->getEarningsData(EARNINGS_RECODESLWPAY);
			$empAdjTotal = $this->getEarningsData(EARNINGS_RECODEADJ);
			$empAllowTotal = $this->getEarningsData(EARNINGS_RECODEALLOW);
			$empOthersTotal = $this->getEarningsData(EARNINGS_RECODEOTHERS);
			$cntGrnEmp = 0;
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
					$brnBasicTotals = 0;
					$brnOtTotals=0;
					$brnNdTotals=0;
					$brnHolidayTotals=0;
					$brnVLEncashTotals=0;
					$brnVLWPayTotals=0;
					$brnSLWPayTotals=0;
					$brnAdjTotals=0;
					$brnAllowTotals=0;
					$brnOthersTotals=0;
					$brnEarnTotals=0;
					$this->Ln();
					$ctr_brn++;
				}
				
				$this->Cell(5,5,'','0','','L');
				
				/*Display Per Location Code*/
				$cntLocEmp = 0;
				$cntBrnEmp = 0;
				$this->Cell(70,5,"LOCATION = ".$arrBrnCode_val["brn_DescLoc"],'0','','L');
				$locBasicTotals = 0;
				$locOtTotals=0;
				$locNdTotals=0;
				$locHolidayTotals=0;
				$locVLEncashTotals=0;
				$locVLWPayTotals=0;
				$locSLWPayTotals=0;
				$locAdjTotals=0;
				$locAllowTotals=0;
				$locOthersTotals=0;
				$locEarnTotals=0;

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
								
								/*Employee Basic Pay*/
								foreach ($empBasicTotal  as $empBasicTotal_Val) {
									if ($empBasicTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpBasic=$empBasicTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(19,5,($rowEmpBasic!=0?number_format($rowEmpBasic,2):""),'0','','R');
								
								/*Employee OT Pay*/
								foreach ($empOtTotal  as $empOtTotal_Val) {
									if ($empOtTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOt=$empOtTotal_Val["totAmt"];
									}
								}
								$this->Cell(21,5,($rowEmpOt!=0?number_format($rowEmpOt,2):""),'0','','R');
								
								/*Employee Nd Pay*/
								foreach ($empNdTotal  as $empNdTotal_Val) {
									if ($empNdTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNd=$empNdTotal_Val["totAmt"];
									}
								}
								$this->Cell(19,5,($rowEmpNd!=0?number_format($rowEmpNd,2):""),'0','','R');
								
								/*Employee Holiday Pay*/
								foreach ($empHolidayTotal  as $empHolidayTotal_Val) {
									if ($empHolidayTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpHoliday=$empHolidayTotal_Val["totAmt"];
									}
								}
								$this->Cell(21,5,($rowEmpHoliday!=0?number_format($rowEmpHoliday,2):""),'0','','R');
					
								/*Employee VL Encash*/
								foreach ($empVLEncashTotal  as $empVLEncashTotal_Val) {
									if ($empVLEncashTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpVLEncash=$empVLEncashTotal_Val["totAmt"];
									}
								}
								$this->Cell(18,5,($rowEmpVLEncash!=0?number_format($rowEmpVLEncash,2):""),'0','','R');
								
								/*Employee VL With Pay*/
								foreach ($empVLWPayTotal  as $empVLWPayTotal_Val) {
									if ($empVLWPayTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpVLWPay=$empVLWPayTotal_Val["totAmt"];
									}
								}
								$this->Cell(19,5,($rowEmpVLWPay!=0?number_format($rowEmpVLWPay,2):""),'0','','R');
								
								/*Employee SL With Pay*/
								foreach ($empSLWPayTotal  as $empSLWPayTotal_Val) {
									if ($empSLWPayTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpSLWPay=$empSLWPayTotal_Val["totAmt"];
									}
								}
								$this->Cell(19,5,($rowEmpSLWPay!=0?number_format($rowEmpSLWPay,2):""),'0','','R');
								
								/*Employee ADJS*/
								foreach ($empAdjTotal  as $empAdjTotal_Val) {
									if ($empAdjTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpAdj=$empAdjTotal_Val["totAmt"];
									}
								}
								$this->Cell(21,5,($rowEmpAdj!=0?number_format($rowEmpAdj,2):""),'0','','R');
								
								/*Employee ALLOWANCE not Sperate payslip*/
								foreach ($empAllowTotal  as $empAllowTotal_Val) {
									if ($empAllowTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpAllow=$empAllowTotal_Val["totAmt"];
									}
								}
								$this->Cell(21,5,($rowEmpAllow!=0?number_format($rowEmpAllow,2):""),'0','','R');
								
								/*Employee Others*/
								foreach ($empOthersTotal  as $empOthersTotal_Val) {
									if ($empOthersTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOthers=$empOthersTotal_Val["totAmt"];
									}
								}
								$this->Cell(19,5,($rowEmpOthers!=0?number_format($rowEmpOthers,2):""),'0','','R');
								
								$empEarnTotal = $rowEmpBasic+$rowEmpOt+$rowEmpNd+$rowEmpHoliday+$rowEmpVLEncash+$rowEmpVLWPay+$rowEmpSLWPay+$rowEmpAdj+$rowEmpAllow+$rowEmpOthers;
								$this->Cell(22,5,($empEarnTotal!=0?number_format($empEarnTotal,2):""),'0','','R');
								$this->Ln();
								$this->Cell(47,3,$resQryValue["empNo"],'0','1','L');
								$this->Ln();
								$locBasicTotals+=$rowEmpBasic;
								$locOtTotals+=$rowEmpOt;
								$locNdTotals+=$rowEmpNd;
								$locHolidayTotals+=$rowEmpHoliday;
								$locVLEncashTotals+=$rowEmpVLEncash;
								$locVLWPayTotals+=$rowEmpVLWPay;
								$locSLWPayTotals+=$rowEmpSLWPay;
								$locAdjTotals+=$rowEmpAdj;
								$locAllowTotals+=$rowEmpAllow;
								$locOthersTotals+=$rowEmpOthers;
								$locEarnTotals+=$empEarnTotal;
								
								$brnBasicTotals+=$rowEmpBasic;
								$brnOtTotals+=$rowEmpOt;
								$brnNdTotals+=$rowEmpNd;
								$brnHolidayTotals+=$rowEmpHoliday;
								$brnVLEncashTotals+=$rowEmpVLEncash;
								$brnVLWPayTotals+=$rowEmpVLWPay;
								$brnSLWPayTotals+=$rowEmpSLWPay;
								$brnAdjTotals+=$rowEmpAdj;
								$brnAllowTotals+=$rowEmpAllow;
								$brnOthersTotals+=$rowEmpOthers;
								$brnEarnTotals+=$empEarnTotal;
								
								$grnBasicTotals+=$rowEmpBasic;
								$grnOtTotals+=$rowEmpOt;
								$grnNdTotals+=$rowEmpNd;
								$grnHolidayTotals+=$rowEmpHoliday;
								$grnVLEncashTotals+=$rowEmpVLEncash;
								$grnVLWPayTotals+=$rowEmpVLWPay;
								$grnSLWPayTotals+=$rowEmpSLWPay;
								$grnAdjTotals+=$rowEmpAdj;
								$grnAllowTotals+=$rowEmpAllow;
								$grnOthersTotals+=$rowEmpOthers;
								$grnEarnTotals+=$empEarnTotal;
								
								unset($rowEmpBasic,$rowEmpOt,$rowEmpNd,$rowEmpHoliday,$rowEmpVLEncash,$rowEmpVLWPay,$rowEmpSLWPay,$rowEmpAdj,$rowEmpAllow,$rowEmpOthers,$empEarnTotal);
							
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
				$this->Cell(35,5,'LOCATION TOTALS = '.$cntLocEmp,'0','0','L');
				$this->Cell(24,5,($locBasicTotals!=0?number_format($locBasicTotals,2):""),'0','0','R');
				$this->Cell(21,5,($locOtTotals!=0?number_format($locOtTotals,2):""),'0','0','R');
				$this->Cell(19,5,($locNdTotals!=0?number_format($locNdTotals,2):""),'0','0','R');
				$this->Cell(21,5,($locHolidayTotals!=0?number_format($locHolidayTotals,2):""),'0','0','R');
				$this->Cell(18,5,($locVLEncashTotals!=0?number_format($locVLEncashTotals,2):""),'0','0','R');
				$this->Cell(19,5,($locVLWPayTotals!=0?number_format($locVLWPayTotals,2):""),'0','0','R');
				$this->Cell(19,5,($locSLWPayTotals!=0?number_format($locSLWPayTotals,2):""),'0','0','R');
				$this->Cell(21,5,($locAdjTotals!=0?number_format($locAdjTotals,2):""),'0','0','R');
				$this->Cell(21,5,($locAllowTotals!=0?number_format($locAllowTotals,2):""),'0','0','R');
				$this->Cell(19,5,($locOthersTotals!=0?number_format($locOthersTotals,2):""),'0','0','R');
				$this->Cell(22,5,($locEarnTotals!=0?number_format($locEarnTotals,2):""),'0','0','R');
				
				if($ctr_loc==$sizeofLoc)
				{
					$this->Ln();
					$this->Cell(40,5,'BRANCH TOTALS = '.$cntBrnEmp,'0','','L');
					$this->Cell(24,5,($brnBasicTotals!=0?number_format($brnBasicTotals,2):""),'0','0','R');
					$this->Cell(21,5,($brnOtTotals!=0?number_format($brnOtTotals,2):""),'0','0','R');
					$this->Cell(19,5,($brnNdTotals!=0?number_format($brnNdTotals,2):""),'0','0','R');
					$this->Cell(21,5,($brnHolidayTotals!=0?number_format($brnHolidayTotals,2):""),'0','0','R');
					$this->Cell(18,5,($brnVLEncashTotals!=0?number_format($brnVLEncashTotals,2):""),'0','0','R');
					$this->Cell(19,5,($brnVLWPayTotals!=0?number_format($brnVLWPayTotals,2):""),'0','0','R');
					$this->Cell(19,5,($brnSLWPayTotals!=0?number_format($brnSLWPayTotals,2):""),'0','0','R');
					$this->Cell(21,5,($brnAdjTotals!=0?number_format($brnAdjTotals,2):""),'0','0','R');
					$this->Cell(21,5,($brnAllowTotals!=0?number_format($brnAllowTotals,2):""),'0','0','R');
					$this->Cell(19,5,($brnOthersTotals!=0?number_format($brnOthersTotals,2):""),'0','0','R');
					$this->Cell(22,5,($brnEarnTotals!=0?number_format($brnEarnTotals,2):""),'0','0','R');
					if($ctr_brn!=$sizeofBrn)
						$this->AddPage();
				}
			
				$tmpBrnCode = $arrBrnCode_val["empBrnCode"];
				$this->Ln();
				$ctr_loc++;
			}
			$this->Cell(40,5,'GRAND TOTALS = '.$cntGrnEmp,'0','','L');
			$this->Cell(24,5,($grnBasicTotals!=0?number_format($grnBasicTotals,2):""),'0','0','R');
			$this->Cell(21,5,($grnOtTotals!=0?number_format($grnOtTotals,2):""),'0','0','R');
			$this->Cell(19,5,($grnNdTotals!=0?number_format($grnNdTotals,2):""),'0','0','R');
			$this->Cell(21,5,($grnHolidayTotals!=0?number_format($grnHolidayTotals,2):""),'0','0','R');
			$this->Cell(18,5,($grnVLEncashTotals!=0?number_format($grnVLEncashTotals,2):""),'0','0','R');
			$this->Cell(19,5,($grnVLWPayTotals!=0?number_format($grnVLWPayTotals,2):""),'0','0','R');
			$this->Cell(19,5,($grnSLWPayTotals!=0?number_format($grnSLWPayTotals,2):""),'0','0','R');
			$this->Cell(21,5,($grnAdjTotals!=0?number_format($grnAdjTotals,2):""),'0','0','R');
			$this->Cell(21,5,($grnAllowTotals!=0?number_format($grnAllowTotals,2):""),'0','0','R');
			$this->Cell(19,5,($grnOthersTotals!=0?number_format($grnOthersTotals,2):""),'0','0','R');
			$this->Cell(22,5,($grnEarnTotals!=0?number_format($grnEarnTotals,2):""),'0','0','R');	
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
	$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$pdf->pdHeadTitle	=	$inqTSObj->valDateArt($arrPayPd['pdPayable'])." (Group ".$_SESSION[pay_group].", ".$catName['payCatDesc'].")";
	if (strlen(strpos($_GET['tbl'],'Hist'))==0) 
		$PaySum = "tblPayrollSummary";
	else 
		$PaySum = "tblPayrollSummaryhist";
	
	if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
	if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
	
	$EmpList = $inqTSObj->qryListOfEmployees($empNo,$empDiv, $empDept, $empSect, $orderBy,$empBrnCode,$locType);
	
	  $qryTS = "SELECT * FROM tblEmpMast where empNo in 
			 	(Select empNo from ".$_GET['tbl']." where compCode='".$_SESSION["company_code"]."'
				 and pdYear='{$arrPayPd['pdYear']}' and pdNumber='{$arrPayPd['pdNumber']}' 
				 and trnCode in 
						(Select trnCode from tblPayTransType where compCode ='".$_SESSION["company_code"]."' and trnCat='E' and trnStat='A' ))
				 and empPayGrp='".$_SESSION["pay_group"]."' 
				 AND empNo IN 
				 				(Select empNo from $PaySum where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )
			  	$orderBy1";
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
	$pdf->Output('earnings_list.pdf','D');
?>