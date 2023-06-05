<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("common_obj.php");
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
			
			$this->SetFont('Arial','','10'); 
			$this->Cell(80,5,"Run Date: " . $newdate,"0");
			$this->Cell(170,5,$this->compName,"0",'0','C');
			$this->Cell(85,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(80,5,"Report ID: EMPVIOLATIONS");
			$hTitle = "Current Employees Violation";
			$this->Cell(170,5,$hTitle,'0','0','C');
			$this->Ln(5);
			$this->Ln();
		
			
			
			$this->SetFont('Arial','B','10');
			$arrHeaderShift = array('T-IN','LUN-OUT', 'LUN-IN', 'BRK.OUT', 'BRK.IN', 'T-OUT');
			
			$this->SetFont('Arial','B','10');
			$this->Cell(70,7,'','0','','C');
			$this->Cell(159,7,'ACTUAL LOGS','1','0','C');
			$this->Cell(106,7,'','0','1','C');
			
			$this->Cell(70,7,'EMP. INFO','1','','C');
			$this->Cell(25,7,'TS DATE','1','','C');
			$this->Cell(20,7,'DAY','1','','C');
			
			foreach($arrHeaderShift as $arrHeaderShift_val)
			{
				$this->Cell(19,7,$arrHeaderShift_val,'1','','C');
			}
			
			$this->Cell(106,7,'VIOLATION DESC','1','1','C');
			$this->SetFont('Arial','','10');
		}
		
		function getTblData($tbl, $cond, $orderBy, $ouputType)
		{
			$qryTblInfo = "Select * from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
			//echo $qryTblInfo."\n";
			$resTblInfo = $this->execQry($qryTblInfo);
			if($ouputType == 'sqlAssoc')
				return $this->getSqlAssoc($resTblInfo);
			else
				return $this->getArrRes($resTblInfo);
		}
		
		
		function displayContent($arrQry, $arrBranch)
		{
			$dayDesc = array('1'=>'Mon', '2'=>'Tue', '3'=>'Wed', '4'=>'Thu', '5'=>'Fri', '6'=>'Sat', '7'=>'Sun');	
			$arrLogsFields = array('timeIn', 'lunchOut', 'lunchIn', 'breakOut', 'breakIn', 'timeOut');	
			
			foreach($arrBranch as $arrBranch_val)
			{
				$this->SetFont('Arial','B','10');
				$this->Cell(260,10,$arrBranch_val["brn_Desc"],'0','1');
				
				foreach($arrQry as $arrQry_val)
				{
					$this->SetFont('Arial','','10');
					if($arrQry_val["empBrnCode"]==$arrBranch_val["empBrnCode"])
					{
						$this->Cell(70,7,$arrQry_val["empNo"]." - ".$arrQry_val["empLastName"].", ".$arrQry_val["empFirstName"][0].".".$arrQry_val["empMidName"][0].".",'1','.','L');
						$this->Cell(25,7,date("m/d/Y", strtotime($arrQry_val["tsDate"])),'1','','C');
						$this->Cell(20,7,substr(date("l", strtotime($arrQry_val["tsDate"])),0,3),'1','0','C');
							
						foreach($arrLogsFields as $arrLogsFields_val)
						{
							$this->Cell(19,7,$arrQry_val[$arrLogsFields_val],'1','','C');
						}
						
						$this->Cell(106,7,$arrQry_val["violationDesc"],'1','1','L');
						
						
					}
				}
				$this->SetFont('Arial','','10');
			}
		}
		
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Arial','B',9);
			$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}

	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$empNo         		= 	$_GET['empNo'];
	$empBrnCode 		= 	$_GET['empBrnCode'];
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$vioCode 			= $_GET['vioCode'];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	
	if ($empNo>"") {$empNo1 = " AND (empTsCorr.empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($vioCode!="0") {$vioCode1 = " AND (editReason = '{$vioCode}')";} else {$vioCode1 = "";}
	
	$qryEmpViolations = "Select *
						from tblTk_TimesheetCorr empTsCorr, tblEmpmast empMast, tblTK_ViolationType vioType 
						where empTsCorr.compCode='".$_SESSION["company_code"]."' and empMast.compCode='".$_SESSION["company_code"]."' 
						and vioType.compCode='".$_SESSION["company_code"]."' and empTsCorr.empNo=empMast.empNo 
						and editReason=violationCd and editReason is not null $empNo1 $empBrnCode1 $empDiv1 $empDept1 $empSect1 $vioCode1
						order by empLastName, tsDate" ;
	
	$resEmpViolations = $inqTSObj->execQry($qryEmpViolations);
	$arrEmpViolations = $inqTSObj->getArrRes($resEmpViolations);
	
	if(sizeof($arrEmpViolations)>=1)
	{
		$arrListofBranch = $inqTSObj->getBrnCodes($arrEmpViolations);
		
		if($inqTSObj->getRecCount($resEmpViolations)>0)
		{
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displayContent($arrEmpViolations, $arrListofBranch);
		}
	}
	$pdf->Output();
?>
