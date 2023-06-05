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
			$hTitle = " UT/ND Report for the Period of ".$this->pdHeadTitle;
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			$this->SetFont('Courier','','10');
			$this->Cell(35,5,'EMP. NO.','0','','L');
			
			
			$this->Cell(50,5,'NAME','0','','L');
			$this->Cell(40,5,'TARDINESS','0','','R');
			$this->Cell(40,5,'UNDERTIME','0','','R');
			
			$this->Ln();
		}
		
		function getTARDUT($trnCode)
		{
			$qryTARDUT = "SELECT empNo,SUM(".$this->reportType.".trnAmountE) AS totAmt
						FROM ".$this->reportType." INNER JOIN
						tblPayTransType ON ".$this->reportType.".trnCode = tblPayTransType.trnCode
						WHERE (tblPayTransType.compCode =  '".$_SESSION["company_code"]."') AND (".$this->reportType.".compCode = '".$_SESSION["company_code"]."')
						AND (".$this->reportType.".pdYear = '".$this->pdYear."') AND (".$this->reportType.".pdNumber ='".$this->pdNumber."') 
						and tblPayTransType.trnCode = '$trnCode'
						GROUP BY ".$this->reportType.".empNo, empNo";
						
			$resTARDUT = $this->execQry($qryTARDUT);
			$resTARDUT = $this->getArrRes($resTARDUT);
			return $resTARDUT;
		}
		
		function displayContent($arrBrnCode, $arrQry,$getLocCodes,$getBrnCodes)
		{
			$this->SetFont('Courier','','10'); 
			$this->Ln();
			
			$getEmpTARDTotal = $this->getTARDUT(EARNINGS_TARD);
			$getEmpUTTotal =  $this->getTARDUT(EARNINGS_UT);
			
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
					$empbrnTARDTotal = 0;
					$empbrnUTTotal = 0;
					$this->Ln();
					$ctr_brn++;
				}
				
				$this->Cell(5,5,'','0','','L');
				
				/*Display Per Location Code*/
				$cntLocEmp = 0;
				$cntBrnEmp = 0;
				$this->Cell(70,5,"LOCATION = ".$arrBrnCode_val["brn_DescLoc"],'0','','L');
				$emplocTARDTotal = 0;
				$emplocUTTotal = 0;

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
								$this->Cell(35,5,$resQryValue["empNo"],'0','0','L');
								$this->Cell(50,5,$resQryValue["empLastName"].", ".$resQryValue["empFirstName"][0].".".$resQryValue["empMidName"][0].'.','0','','L');
								
								/*Employee TARD*/
								foreach ($getEmpTARDTotal  as $getEmpTARDTotal_Val) {
									if ($getEmpTARDTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpTARD=$getEmpTARDTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(40,5,($rowEmpTARD!=0?number_format($rowEmpTARD,2):""),'0','','R');
								
								/*Employee UT*/
								foreach ($getEmpUTTotal  as $getEmpUTTotal_Val) {
									if ($getEmpUTTotal_Val['empNo']==$resQryValue["empNo"]) {
										$rowEmpUT=$getEmpUTTotal_Val["totAmt"];
									}
								}
								
								$this->Cell(40,5,($rowEmpUT!=0?number_format($rowEmpUT,2):""),'0','','R');
								$emplocTARDTotal+=$rowEmpTARD;
								$emplocUTTotal+=$rowEmpUT;
								$empbrnTARDTotal+=$rowEmpTARD;
								$empbrnUTTotal+=$rowEmpUT;
								$empgrdTARDTotal+=$rowEmpTARD;
								$empgrdUTTotal+=$rowEmpUT;
					
								unset($rowEmpTARD,$rowEmpUT);
								$this->Ln();
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
				$this->Cell(80,5,'LOCATION TOTALS = '.$cntLocEmp,'0','0','L');
				$this->Cell(40,5,($emplocTARDTotal!=0?number_format($emplocTARDTotal,2):""),'0','0','R');
				$this->Cell(40,5,($emplocUTTotal!=0?number_format($emplocUTTotal,2):""),'0','0','R');
				
				if($ctr_loc==$sizeofLoc)
				{
					$this->Ln();
					$this->Cell(85,5,'BRANCH TOTALS = '.$cntBrnEmp,'0','','L');
					$this->Cell(40,5,($empbrnTARDTotal!=0?number_format($empbrnTARDTotal,2):""),'0','0','R');
					$this->Cell(40,5,($empbrnUTTotal!=0?number_format($empbrnUTTotal,2):""),'0','0','R');
					if($ctr_brn!=$sizeofBrn)
						$this->AddPage();
				}
			
				$tmpBrnCode = $arrBrnCode_val["empBrnCode"];
				$this->Ln();
				$ctr_loc++;
			}
			$this->Cell(85,5,'GRAND TOTALS = '.$cntGrnEmp,'0','','L');
			$this->Cell(40,5,($empgrdTARDTotal!=0?number_format($empgrdTARDTotal,2):""),'0','0','R');
			$this->Cell(40,5,($empgrdUTTotal!=0?number_format($empgrdUTTotal,2):""),'0','0','R');
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