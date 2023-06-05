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
			
			$this->Cell(70,5,"Report ID: ORNDR001");
			$hTitle = " Overtime/Night Diff. Report for the Period of ".$this->pdHeadTitle;
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			$this->SetFont('Courier','','10');
			
			$this->Cell(40,4,'NAME','0','','L');
			$this->Cell(8,4,'OT','0','','R');
			$this->Cell(18,4,'REG','0','','R');
			$this->Cell(18,4,'REST','0','','R');
			$this->Cell(18,4,'LEGAL','0','','R');
			$this->Cell(18,4,'SPECIAL','0','','R');
			$this->Cell(18,4,'LEGAL','0','','R');
			$this->Cell(18,4,'SPECIAL','0','','R');
			$this->Cell(18,4,'REST>','0','','R');
			$this->Cell(18,4,'LEGAL','0','','R');
			$this->Cell(18,4,'SPECIAL','0','','R');
			$this->Cell(18,4,'LEGAL+','0','','R');
			$this->Cell(18,4,'SPECIAL+','0','','R');
			$this->Cell(20,4,'TOTAL','0','','R');
			
			
			$this->Ln();
			$this->Cell(40,4,'EMP. NO','','','L');
			$this->Cell(8,4,'ND','0','','R');
			$this->Cell(18,4,'','','','R');
			$this->Cell(18,4,'','','','R');
			$this->Cell(18,4,'','','','R');
			$this->Cell(18,4,'','','','R');
			$this->Cell(18,4,'+REST','','','R');
			$this->Cell(18,4,'+REST','','','R');
			$this->Cell(18,4,'8HRS.','','','R');
			$this->Cell(18,4,'>8HRS.','','','R');
			$this->Cell(18,4,'>8HRS.','','','R');
			$this->Cell(18,4,'R>8HRS.','','','R');
			$this->Cell(18,4,'R>8HRS.','','','R');
			$this->Cell(20,4,'','','','R');
			
			$this->Ln();
		}
		
		function getEmpOTND($trnCode)
		{
			$qryOTND = "SELECT empNo,SUM(".$this->reportType.".trnAmountE) AS totAmt
						FROM ".$this->reportType." INNER JOIN
						tblPayTransType ON ".$this->reportType.".trnCode = tblPayTransType.trnCode
						WHERE (tblPayTransType.compCode =  '".$_SESSION["company_code"]."') AND (".$this->reportType.".compCode = '".$_SESSION["company_code"]."')
						AND (".$this->reportType.".pdYear = '".$this->pdYear."') AND (".$this->reportType.".pdNumber ='".$this->pdNumber."') 
						and tblPayTransType.trnCode = '$trnCode'
						GROUP BY ".$this->reportType.".empNo, empNo";
				
			$resOTND = $this->execQry($qryOTND);
			$resOTND = $this->getArrRes($resOTND);
			return $resOTND;
		}
		
		function displayContent($arrBrnCode, $arrQry,$getLocCodes,$getBrnCodes)
		{
			$this->SetFont('Courier','','10'); 
			$this->Ln();
			
			$empOTREGTotal = $this->getEmpOTND(OTRG);
			$empOTRESTTotal = $this->getEmpOTND(OTRD);
			$empOTLEGALTotal = $this->getEmpOTND(OTLH);
			$empOTSPECIALTotal = $this->getEmpOTND(OTSH);
			$empOTLRESTTotal = $this->getEmpOTND(OTLHRD);
			$empOTSRESTTotal = $this->getEmpOTND(OTSPRD);
			$empOTRGT8HRSTotal = $this->getEmpOTND(OTRDGT8);
			$empOTLGT8HRSTotal = $this->getEmpOTND(OTLHGT8);
			$empOTSPGT8HRSTotal = $this->getEmpOTND(OTSPGT8);
			$empOTLRGT8HRSTotal = $this->getEmpOTND(OTLHRDGT8);
			$empOTSRGT8HRSTotal = $this->getEmpOTND(OTSPRDGT8);
			
			$empNDREGTotal = $this->getEmpOTND(NDRG);
			$empNDRESTTotal = $this->getEmpOTND(NDRD);
			$empNDLEGALTotal = $this->getEmpOTND(NDLH);
			$empNDSPECIALTotal = $this->getEmpOTND(NDSP);
			$empNDLRESTTotal = $this->getEmpOTND(NDLHRD);
			$empNDSRESTTotal = $this->getEmpOTND(NDSPRD);
			$empNDRGT8HRSTotal = $this->getEmpOTND(NDRDGT8);
			$empNDLGT8HRSTotal = $this->getEmpOTND(NDLHGT8);
			$empNDSPGT8HRSTotal = $this->getEmpOTND(NDSHGT8);
			$empNDLRGT8HRSTotal = $this->getEmpOTND(NDLHRDGT8);
			$empNDSRGT8HRSTotal = $this->getEmpOTND(NDSPRDGT8);
			
			$cntGrnEmp = 0;
			$grndDedTotal = 0;
			$ctr_brn = 0;
			$sizeofBrn = sizeof(explode(",",$getBrnCodes))-1;
			foreach($arrBrnCode as $arrBrnCode_val)
			{
				
				/*Display Per Branch Code*/
				if($arrBrnCode_val["empBrnCode"]!=$tmpBrnCode)
				{
					$ctr_loc = 1;
					$sizeofLoc = sizeof(explode(",",$getLocCodes[$arrBrnCode_val["empBrnCode"]] = substr($getLocCodes[$arrBrnCode_val["empBrnCode"]],0,strlen($getLocCodes[$arrBrnCode_val["empBrnCode"]]) - 1)));
					
					$this->SetFont('Courier','','8'); 
					$this->Cell(47,5,"BRANCH = ".$arrBrnCode_val["brn_Desc"],'0','','L');
					unset($brnrowEmpOTREG,$brnrowEmpOTREST,$brnrowEmpOTLEGAL,$brnrowEmpOTPECIAL,$brnrowEmpOTLREST,$brnrowEmpOTSREST,$brnrowEmpOTRGT8HRS,$brnrowEmpOTLGT8HRS,$brnrowEmpOTSPGT8HRS,
						  $brnrowOTLRGT8HRS,$brnrowEmpOTSRGT8HRS,$brnempOTTotal,$brnrowEmpNDREG,$brnrowEmpNDREST,$brnrowEmpNDLEGAL,$brnrowEmpNDPECIAL,$brnrowEmpNDLREST,$brnrowEmpNDSREST,$brnrowEmpNDRGT8HRS,$brnrowEmpNDLGT8HRS,$brnrowEmpNDSPGT8HRS,
						  $brnrowNDLRGT8HRS,$brnrowEmpNDSRGT8HRS,$brnempNDTotal);
					$this->Ln();
					$ctr_brn++;
				}
				
				$this->Cell(5,5,'','0','','L');
				
				/*Display Per Location Code*/
				$cntLocEmp = 0;
				$cntBrnEmp = 0;
				$this->Cell(70,5,"LOCATION = ".$arrBrnCode_val["brn_DescLoc"],'0','','L');
				unset($locrowEmpOTREG,$locrowEmpOTREST,$locrowEmpOTLEGAL,$locrowEmpOTPECIAL,$locrowEmpOTLREST,$locrowEmpOTSREST,$locrowEmpOTRGT8HRS,$locrowEmpOTLGT8HRS,$locrowEmpOTSPGT8HRS,
					  $locrowOTLRGT8HRS,$locrowEmpOTSRGT8HRS,$locempOTTotal,$locrowEmpNDREG,$locrowEmpNDREST,$locrowEmpNDLEGAL,$locrowEmpNDPECIAL,$locrowEmpNDLREST,$locrowEmpNDSREST,$locrowEmpNDRGT8HRS,$locrowEmpNDLGT8HRS,$locrowEmpNDSPGT8HRS,
					  $locrowNDLRGT8HRS,$locrowEmpNDSRGT8HRS,$locempNDTotal);
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
								$this->Cell(40,5,$resQryValue["empLastName"].", ".$resQryValue["empFirstName"][0].".".$resQryValue["empMidName"][0],".",'0','','L');
								
								
								/*Employee OT REG*/
								foreach ($empOTREGTotal  as $empOTREGTotal_Val) {
									if ($empOTREGTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTREG=$empOTREGTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(26,5,($rowEmpOTREG!=0?number_format($rowEmpOTREG,2):""),'0','','R');
								
								/*Employee OT REST*/
								foreach ($empOTRESTTotal  as $empOTRESTTotal_Val) {
									if ($empOTRESTTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTREST=$empOTRESTTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpOTREST!=0?number_format($rowEmpOTREST,2):""),'0','','R');
								
								/*Employee OT LEGAL*/
								foreach ($empOTLEGALTotal  as $empOTLEGALTotal_Val) {
									if ($empOTLEGALTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTLEGAL=$empOTLEGALTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpOTLEGAL!=0?number_format($rowEmpOTLEGAL,2):""),'0','','R');
								
								/*Employee OT SPECIAL*/
								foreach ($empOTSPECIALTotal  as $empOTSPECIALTotal_Val) {
									if ($empOTSPECIALTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTPECIAL=$empOTSPECIALTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpOTPECIAL!=0?number_format($rowEmpOTPECIAL,2):""),'0','','R');
								
								/*Employee LEGAL RESTDAY*/
								foreach ($empOTLRESTTotal  as $empOTLRESTTotal_Val) {
									if ($empOTLRESTTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTLREST=$empOTLRESTTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpOTLREST!=0?number_format($rowEmpOTLREST,2):""),'0','','R');
								
								/*Employee OT SP REST DAY*/
								foreach ($empOTSRESTTotal  as $empOTSRESTTotal_Val) {
									if ($empOTSRESTTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTSREST=$empOTSRESTTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpOTSREST!=0?number_format($rowEmpOTSREST,2):""),'0','','R');
								
								/*Employee OTRGT8HRS*/
								foreach ($empOTRGT8HRSTotal  as $empOTRGT8HRSTotal_Val) {
									if ($empOTRGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTRGT8HRS=$empOTRGT8HRSTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpOTRGT8HRS!=0?number_format($rowEmpOTRGT8HRS,2):""),'0','','R');
								
								/*Employee OTLGT8HRS*/
								foreach ($empOTLGT8HRSTotal  as $empOTLGT8HRSTotal_Val) {
									if ($empOTLGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTLGT8HRS=$empOTLGT8HRSTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpOTLGT8HRS!=0?number_format($rowEmpOTLGT8HRS,2):""),'0','','R');
							
								/*Employee OTSPGT8HRST*/
								foreach ($empOTSPGT8HRSTotal  as $empOTSPGT8HRSTotal_Val) {
									if ($empOTSPGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTSPGT8HRS=$empOTSPGT8HRSTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpOTSPGT8HRS!=0?number_format($rowEmpOTSPGT8HRS,2):""),'0','','R');
								
								/*Employee OTLRGT8HRS*/
								foreach ($empOTLRGT8HRSTotal  as $empOTLRGT8HRSTotal_Val) {
									if ($empOTLRGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowOTLRGT8HRS=$empOTLRGT8HRSTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowOTLRGT8HRS!=0?number_format($rowOTLRGT8HRS,2):""),'0','','R');
								
								
								/*Employee OTSPGT8HRST*/
								foreach ($empOTSRGT8HRSTotal  as $empOTSRGT8HRSTotal_Val) {
									if ($empOTSRGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpOTSRGT8HRS=$empOTSRGT8HRSTotal_Val["totAmt"];
									}
								}
								$this->Cell(18,5,($rowEmpOTSRGT8HRS!=0?number_format($rowEmpOTSRGT8HRS,2):""),'0','','R');
								
								$empOTTotal = "";
								$empOTTotal = $rowEmpOTREG + $rowEmpOTREST + $rowEmpOTLEGAL + $rowEmpOTPECIAL + $rowEmpOTLREST + 
								              $rowEmpOTSREST + $rowEmpOTRGT8HRS + $rowEmpOTLGT8HRS + $rowEmpOTSPGT8HRS + 
								      		  $rowOTLRGT8HRS + $rowEmpOTSRGT8HRS;
												
								$this->Cell(20,5,($empOTTotal!=0?number_format($empOTTotal,2):""),'0','','R');
								
								
								$this->Cell(5,5,'','0','','R');
								$this->Ln();
								$this->Cell(40,3,$resQryValue["empNo"],'0','0','L');
								
								/*Employee ND REG*/
								foreach ($empNDREGTotal  as $empNDREGTotal_Val) {
									if ($empNDREGTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDREG=$empNDREGTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(26,5,($rowEmpNDREG!=0?number_format($rowEmpNDREG,2):""),'0','','R');
								
								/*Employee ND REST*/
								foreach ($empNDRESTTotal  as $empNDRESTTotal_Val) {
									if ($empNDRESTTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDREST=$empNDRESTTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpNDREST!=0?number_format($rowEmpNDREST,2):""),'0','','R');
								
								/*Employee ND LEGAL*/
								foreach ($empNDLEGALTotal  as $empNDLEGALTotal_Val) {
									if ($empNDLEGALTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDLEGAL=$empNDLEGALTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpNDLEGAL!=0?number_format($rowEmpNDLEGAL,2):""),'0','','R');
								
								/*Employee ND SPECIAL*/
								foreach ($empNDSPECIALTotal  as $empNDSPECIALTotal_Val) {
									if ($empNDSPECIALTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDPECIAL=$empNDSPECIALTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpNDPECIAL!=0?number_format($rowEmpNDPECIAL,2):""),'0','','R');
								
								/*Employee LEGAL RESTDAY*/
								foreach ($empNDLRESTTotal  as $empNDLRESTTotal_Val) {
									if ($empNDLRESTTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDLREST=$empNDLRESTTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpNDLREST!=0?number_format($rowEmpNDLREST,2):""),'0','','R');
								
								/*Employee ND SP REST DAY*/
								foreach ($empNDSRESTTotal  as $empNDSRESTTotal_Val) {
									if ($empNDSRESTTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDSREST=$empNDSRESTTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpNDSREST!=0?number_format($rowEmpNDSREST,2):""),'0','','R');
								
								/*Employee NDRGT8HRS*/
								foreach ($empNDRGT8HRSTotal  as $empNDRGT8HRSTotal_Val) {
									if ($empNDRGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDRGT8HRS=$empNDRGT8HRSTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpNDRGT8HRS!=0?number_format($rowEmpNDRGT8HRS,2):""),'0','','R');
								
								/*Employee NDLGT8HRS*/
								foreach ($empNDLGT8HRSTotal  as $empNDLGT8HRSTotal_Val) {
									if ($empNDLGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDLGT8HRS=$empNDLGT8HRSTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpNDLGT8HRS!=0?number_format($rowEmpNDLGT8HRS,2):""),'0','','R');
								
								/*Employee NDSPGT8HRST*/
								foreach ($empNDSPGT8HRSTotal  as $empNDSPGT8HRSTotal_Val) {
									if ($empNDSPGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDSPGT8HRS=$empNDSPGT8HRSTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowEmpNDSPGT8HRS!=0?number_format($rowEmpNDSPGT8HRS,2):""),'0','','R');
								
								/*Employee NDLRGT8HRS*/
								foreach ($empNDLRGT8HRSTotal  as $empNDLRGT8HRSTotal_Val) {
									if ($empNDLRGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowNDLRGT8HRS=$empNDLRGT8HRSTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(18,5,($rowNDLRGT8HRS!=0?number_format($rowNDLRGT8HRS,2):""),'0','','R');
								
								
								/*Employee NDSPGT8HRST*/
								foreach ($empNDSRGT8HRSTotal  as $empNDSRGT8HRSTotal_Val) {
									if ($empNDSRGT8HRSTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpNDSRGT8HRS=$empNDSRGT8HRSTotal_Val["totAmt"];
									}
								}
								$this->Cell(18,5,($rowEmpNDSRGT8HRS!=0?number_format($rowEmpNDSRGT8HRS,2):""),'0','','R');
								
								$empNDTotal = "";
								$empNDTotal = $rowEmpNDREG + $rowEmpNDREST + $rowEmpNDLEGAL + $rowEmpNDPECIAL + $rowEmpNDLREST + 
								              $rowEmpNDSREST + $rowEmpNDRGT8HRS + $rowEmpNDLGT8HRS + $rowEmpNDSPGT8HRS + 
								      		  $rowNDLRGT8HRS + $rowEmpNDSRGT8HRS;
												
								$this->Cell(20,5,($empNDTotal!=0?number_format($empNDTotal,2):""),'0','','R');
								
								
								$locrowEmpOTREG+=$rowEmpOTREG;
								$locrowEmpOTREST+=$rowEmpOTREST;
								$locrowEmpOTLEGAL+=$rowEmpOTLEGAL;
								$locrowEmpOTPECIAL+=$rowEmpOTPECIAL;
								$locrowEmpOTLREST+=$rowEmpOTLREST;
								$locrowEmpOTSREST+=$rowEmpOTSREST;
								$locrowEmpOTRGT8HRS+=$rowEmpOTRGT8HRS;
								$locrowEmpOTLGT8HRS+=$rowEmpOTLGT8HRS;
								$locrowEmpOTSPGT8HRS+=$rowEmpOTSPGT8HRS;
								$locrowOTLRGT8HRS+=$rowOTLRGT8HRS;
								$locrowEmpOTSRGT8HRS+=$rowEmpOTSRGT8HRS;
								$locempOTTotal+=$empOTTotal;
								$locrowEmpNDREG+=$rowEmpNDREG;
								$locrowEmpNDREST+=$rowEmpNDREST;
								$locrowEmpNDLEGAL+=$rowEmpNDLEGAL;
								$locrowEmpNDPECIAL+=$rowEmpNDPECIAL;
								$locrowEmpNDLREST+=$rowEmpNDLREST;
								$locrowEmpNDSREST+=$rowEmpNDSREST;
								$locrowEmpNDRGT8HRS+=$rowEmpNDRGT8HRS;
								$locrowEmpNDLGT8HRS+=$rowEmpNDLGT8HRS;
								$locrowEmpNDSPGT8HRS+=$rowEmpNDSPGT8HRS;
								$locrowNDLRGT8HRS+=$rowNDLRGT8HRS;
								$locrowEmpNDSRGT8HRS+=$rowEmpNDSRGT8HRS;
								$locempNDTotal+=$empNDTotal;
								
								$brnrowEmpOTREG+=$rowEmpOTREG;
								$brnrowEmpOTREST+=$rowEmpOTREST;
								$brnrowEmpOTLEGAL+=$rowEmpOTLEGAL;
								$brnrowEmpOTPECIAL+=$rowEmpOTPECIAL;
								$brnrowEmpOTLREST+=$rowEmpOTLREST;
								$brnrowEmpOTSREST+=$rowEmpOTSREST;
								$brnrowEmpOTRGT8HRS+=$rowEmpOTRGT8HRS;
								$brnrowEmpOTLGT8HRS+=$rowEmpOTLGT8HRS;
								$brnrowEmpOTSPGT8HRS+=$rowEmpOTSPGT8HRS;
								$brnrowOTLRGT8HRS+=$rowOTLRGT8HRS;
								$brnrowEmpOTSRGT8HRS+=$rowEmpOTSRGT8HRS;
								$brnempOTTotal+=$empOTTotal;
								$brnrowEmpNDREG+=$rowEmpNDREG;
								$brnrowEmpNDREST+=$rowEmpNDREST;
								$brnrowEmpNDLEGAL+=$rowEmpNDLEGAL;
								$brnrowEmpNDPECIAL+=$rowEmpNDPECIAL;
								$brnrowEmpNDLREST+=$rowEmpNDLREST;
								$brnrowEmpNDSREST+=$rowEmpNDSREST;
								$brnrowEmpNDRGT8HRS+=$rowEmpNDRGT8HRS;
								$brnrowEmpNDLGT8HRS+=$rowEmpNDLGT8HRS;
								$brnrowEmpNDSPGT8HRS+=$rowEmpNDSPGT8HRS;
								$brnrowNDLRGT8HRS+=$rowNDLRGT8HRS;
								$brnrowEmpNDSRGT8HRS+=$rowEmpNDSRGT8HRS;
								$brnempNDTotal+=$empNDTotal;
								
								$grdrowEmpOTREG+=$rowEmpOTREG;
								$grdrowEmpOTREST+=$rowEmpOTREST;
								$grdrowEmpOTLEGAL+=$rowEmpOTLEGAL;
								$grdrowEmpOTPECIAL+=$rowEmpOTPECIAL;
								$grdrowEmpOTLREST+=$rowEmpOTLREST;
								$grdrowEmpOTSREST+=$rowEmpOTSREST;
								$grdrowEmpOTRGT8HRS+=$rowEmpOTRGT8HRS;
								$grdrowEmpOTLGT8HRS+=$rowEmpOTLGT8HRS;
								$grdrowEmpOTSPGT8HRS+=$rowEmpOTSPGT8HRS;
								$grdrowOTLRGT8HRS+=$rowOTLRGT8HRS;
								$grdrowEmpOTSRGT8HRS+=$rowEmpOTSRGT8HRS;
								$grdempOTTotal+=$empOTTotal;
								$grdrowEmpNDREG+=$rowEmpNDREG;
								$grdrowEmpNDREST+=$rowEmpNDREST;
								$grdrowEmpNDLEGAL+=$rowEmpNDLEGAL;
								$grdrowEmpNDPECIAL+=$rowEmpNDPECIAL;
								$grdrowEmpNDLREST+=$rowEmpNDLREST;
								$grdrowEmpNDSREST+=$rowEmpNDSREST;
								$grdrowEmpNDRGT8HRS+=$rowEmpNDRGT8HRS;
								$grdrowEmpNDLGT8HRS+=$rowEmpNDLGT8HRS;
								$grdrowEmpNDSPGT8HRS+=$rowEmpNDSPGT8HRS;
								$grdrowNDLRGT8HRS+=$rowNDLRGT8HRS;
								$grdrowEmpNDSRGT8HRS+=$rowEmpNDSRGT8HRS;
								$grdempNDTotal+=$empNDTotal;

								if(($rowEmpNDREG!=0) ||($rowEmpNDREST!=0) ||($rowEmpNDLEGAL!=0) ||($rowEmpNDPECIAL!=0) ||($rowEmpNDLREST!=0) ||($rowEmpNDSREST!=0) ||($rowEmpNDRGT8HRS!=0) ||($rowEmpNDLGT8HRS!=0) ||($rowEmpNDSPGT8HRS!=0) ||
								    ($rowNDLRGT8HRS!=0) ||($rowEmpNDSRGT8HRS!=0) ||($empNDTotal!=0))
									$this->Ln();
								
								$this->Ln();
			
								unset($rowEmpOTREG,$rowEmpOTREST,$rowEmpOTLEGAL,$rowEmpOTPECIAL,$rowEmpOTLREST,$rowEmpOTSREST,$rowEmpOTRGT8HRS,$rowEmpOTLGT8HRS,$rowEmpOTSPGT8HRS,
								      $rowOTLRGT8HRS,$rowEmpOTSRGT8HRS,$empOTTotal,$rowEmpNDREG,$rowEmpNDREST,$rowEmpNDLEGAL,$rowEmpNDPECIAL,$rowEmpNDLREST,$rowEmpNDSREST,$rowEmpNDRGT8HRS,$rowEmpNDLGT8HRS,$rowEmpNDSPGT8HRS,
								      $rowNDLRGT8HRS,$rowEmpNDSRGT8HRS,$empNDTotal);
							
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
				$this->Cell(26,5,($locrowEmpOTREG!=0?number_format($locrowEmpOTREG,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpOTREST!=0?number_format($locrowEmpOTREST,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpOTLEGAL!=0?number_format($locrowEmpOTLEGAL,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpOTPECIAL!=0?number_format($locrowEmpOTPECIAL,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpOTLREST!=0?number_format($locrowEmpOTLREST,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpOTSREST!=0?number_format($locrowEmpOTSREST,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpOTRGT8HRS!=0?number_format($locrowEmpOTRGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpOTLGT8HRS!=0?number_format($locrowEmpOTLGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpOTSPGT8HRS!=0?number_format($locrowEmpOTSPGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($locrowOTLRGT8HRS!=0?number_format($locrowOTLRGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpOTSRGT8HRS!=0?number_format($locrowEmpOTSRGT8HRS,2):""),'0','0','R');
				$this->Cell(20,5,($locempOTTotal!=0?number_format($locempOTTotal,2):""),'0','0','R');
				$this->Ln();
				$this->Cell(5,5,'','0','','L');
				$this->Cell(35,5,'','0','0','L');
				$this->Cell(26,5,($locrowEmpNDREG!=0?number_format($locrowEmpNDREG,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpNDREST!=0?number_format($locrowEmpNDREST,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpNDLEGAL!=0?number_format($locrowEmpNDLEGAL,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpNDPECIAL!=0?number_format($locrowEmpNDPECIAL,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpNDLREST!=0?number_format($locrowEmpNDLREST,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpNDSREST!=0?number_format($locrowEmpNDSREST,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpNDRGT8HRS!=0?number_format($locrowEmpNDRGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpNDLGT8HRS!=0?number_format($locrowEmpNDLGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpNDSPGT8HRS!=0?number_format($locrowEmpNDSPGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($locrowNDLRGT8HRS!=0?number_format($locrowNDLRGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($locrowEmpNDSRGT8HRS!=0?number_format($locrowEmpNDSRGT8HRS,2):""),'0','0','R');
				$this->Cell(20,5,($locempNDTotal!=0?number_format($locempNDTotal,2):""),'0','0','R');
				$this->Ln();
				
				if($ctr_loc==$sizeofLoc)
				{
					$this->Ln();
					$this->Cell(40,5,'BRANCH TOTALS = '.$cntBrnEmp,'0','','L');
					$this->Cell(26,5,($brnrowEmpOTREG!=0?number_format($brnrowEmpOTREG,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpOTREST!=0?number_format($brnrowEmpOTREST,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpOTLEGAL!=0?number_format($brnrowEmpOTLEGAL,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpOTPECIAL!=0?number_format($brnrowEmpOTPECIAL,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpOTLREST!=0?number_format($brnrowEmpOTLREST,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpOTSREST!=0?number_format($brnrowEmpOTSREST,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpOTRGT8HRS!=0?number_format($brnrowEmpOTRGT8HRS,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpOTLGT8HRS!=0?number_format($brnrowEmpOTLGT8HRS,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpOTSPGT8HRS!=0?number_format($brnrowEmpOTSPGT8HRS,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowOTLRGT8HRS!=0?number_format($brnrowOTLRGT8HRS,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpOTSRGT8HRS!=0?number_format($brnrowEmpOTSRGT8HRS,2):""),'0','0','R');
					$this->Cell(20,5,($brnempOTTotal!=0?number_format($brnempOTTotal,2):""),'0','0','R');
					$this->Ln();
					$this->Cell(5,5,'','0','','L');
					$this->Cell(35,5,'','0','0','L');
					$this->Cell(26,5,($brnrowEmpNDREG!=0?number_format($brnrowEmpNDREG,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpNDREST!=0?number_format($brnrowEmpNDREST,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpNDLEGAL!=0?number_format($brnrowEmpNDLEGAL,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpNDPECIAL!=0?number_format($brnrowEmpNDPECIAL,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpNDLREST!=0?number_format($brnrowEmpNDLREST,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpNDSREST!=0?number_format($brnrowEmpNDSREST,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpNDRGT8HRS!=0?number_format($brnrowEmpNDRGT8HRS,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpNDLGT8HRS!=0?number_format($brnrowEmpNDLGT8HRS,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpNDSPGT8HRS!=0?number_format($brnrowEmpNDSPGT8HRS,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowNDLRGT8HRS!=0?number_format($brnrowNDLRGT8HRS,2):""),'0','0','R');
					$this->Cell(18,5,($brnrowEmpNDSRGT8HRS!=0?number_format($brnrowEmpNDSRGT8HRS,2):""),'0','0','R');
					$this->Cell(20,5,($brnempNDTotal!=0?number_format($brnempNDTotal,2):""),'0','0','R');
					
					if($ctr_brn!=$sizeofBrn)
						$this->AddPage();
				}
		
				$tmpBrnCode = $arrBrnCode_val["empBrnCode"];
				$this->Ln();
				$ctr_loc++;
			
			}
				
				$this->Cell(40,5,'GRAND TOTALS = '.$cntGrnEmp,'0','','L');
				$this->Cell(26,5,($grdrowEmpOTREG!=0?number_format($grdrowEmpOTREG,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpOTREST!=0?number_format($grdrowEmpOTREST,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpOTLEGAL!=0?number_format($grdrowEmpOTLEGAL,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpOTPECIAL!=0?number_format($grdrowEmpOTPECIAL,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpOTLREST!=0?number_format($grdrowEmpOTLREST,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpOTSREST!=0?number_format($grdrowEmpOTSREST,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpOTRGT8HRS!=0?number_format($grdrowEmpOTRGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpOTLGT8HRS!=0?number_format($grdrowEmpOTLGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpOTSPGT8HRS!=0?number_format($grdrowEmpOTSPGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowOTLRGT8HRS!=0?number_format($grdrowOTLRGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpOTSRGT8HRS!=0?number_format($grdrowEmpOTSRGT8HRS,2):""),'0','0','R');
				$this->Cell(20,5,($grdempOTTotal!=0?number_format($grdempOTTotal,2):""),'0','0','R');
				$this->Ln();
				$this->Cell(5,5,'','0','','L');
				$this->Cell(35,5,'','0','0','L');
				$this->Cell(26,5,($grdrowEmpNDREG!=0?number_format($grdrowEmpNDREG,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpNDREST!=0?number_format($grdrowEmpNDREST,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpNDLEGAL!=0?number_format($grdrowEmpNDLEGAL,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpNDPECIAL!=0?number_format($grdrowEmpNDPECIAL,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpNDLREST!=0?number_format($grdrowEmpNDLREST,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpNDSREST!=0?number_format($grdrowEmpNDSREST,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpNDRGT8HRS!=0?number_format($grdrowEmpNDRGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpNDLGT8HRS!=0?number_format($grdrowEmpNDLGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpNDSPGT8HRS!=0?number_format($grdrowEmpNDSPGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowNDLRGT8HRS!=0?number_format($grdrowNDLRGT8HRS,2):""),'0','0','R');
				$this->Cell(18,5,($grdrowEmpNDSRGT8HRS!=0?number_format($grdrowEmpNDSRGT8HRS,2):""),'0','0','R');
				$this->Cell(20,5,($grdempNDTotal!=0?number_format($grdempNDTotal,2):""),'0','0','R');
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
	$arrPayPd 			= 	$inqTSObj->getSlctdPd($_SESSION["company_code"],$payPd);
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
	
	$EmpList = $inqTSObj->qryListOfEmployees($empNo,$empDiv, $empDept, $empSect, $orderBy,$empBrnCode,$locType);
	
	$qryTS = "SELECT * FROM tblEmpMast where empNo in 
			 	(Select empNo from ".$_GET['tbl']." where compCode='".$_SESSION["company_code"]."'
				 and pdYear='".$arrPayPd['pdYear']."' and pdNumber='".$arrPayPd['pdNumber']."' 
				 and trnCode in 
						(Select trnCode from tblPayTransType where compCode ='".$_SESSION["company_code"]."' and trnCat='E' and trnStat='A' )
				 and empPayGrp='".$_SESSION["pay_group"]."' and empPayCat='".$_SESSION["pay_category"]."'
				 and empNo in ($EmpList)) 
			   order by empLastName, empFirstName";
	$resTS = $inqTSObj->execQry($qryTS);
	$arrTS = $inqTSObj->getArrRes($resTS);
	$getListofBranch = $inqTSObj->getBrnCodes($inqTSObj->execQry($qryTS));
	$getLocCodes = $inqTSObj->getLocTotals($getListofBranch);
	$getBrnCodes = $inqTSObj->getBrnTotals($getListofBranch);
	
	if($inqTSObj->getRecCount($resTS)>0)
	{
		
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($getListofBranch,$arrTS,$getLocCodes,$getBrnCodes);
	}
	$pdf->Output();
?>