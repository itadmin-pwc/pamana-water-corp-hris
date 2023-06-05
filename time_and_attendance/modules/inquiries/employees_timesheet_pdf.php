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
			
			$this->Cell(80,5,"Report ID: EMPTIMESHEET");
			$hTitle = "Current Employees Timesheet";
			$this->Cell(170,5,$hTitle,'0','0','C');
			$this->Ln(5);
			$this->Ln();
		
			$arrHeaderShift = array('T-IN','LUN-OUT', 'LUN-IN', 'BRK.OUT', 'BRK.IN', 'T-OUT');
			
			$this->SetFont('Arial','B','10');
			$this->Cell(45,7,'','0','','C');
			$this->Cell(134,7,'SHIFT SCHEDULE','1','','C');
			$this->Cell(114,7,'ACTUAL LOGS','1','1','C');
			
			//$this->Cell(70,7,'EMP. INFO','1','','C');
			$this->Cell(25,7,'TS DATE','1','','C');
			$this->Cell(20,7,'DAY TYPE','1','','C');
			foreach($arrHeaderShift as $arrHeaderShift_val)
			{
				$this->Cell(19,7,$arrHeaderShift_val,'1','','C');
			}
			$this->Cell(20,7,'APP. TYPE','1','','C');
			foreach($arrHeaderShift as $arrHeaderShift_val)
			{
				$this->Cell(19,7,$arrHeaderShift_val,'1','','C');
			}
			$this->Cell(16,7,'OT-IN','1','','C');
			$this->Cell(16,7,'OT-OUT','1','','C');
			$this->Cell(15,7,'C.TAG','1','1','C');
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
			$arrShiftFields = array('shftTimeIn', 'shftLunchOut', 'shftLunchIn', 'shftBreakOut', 'shftBreakIn', 'shftTimeOut');	
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
						
						if($arrQry_val["empNo"]!=$empNoExists)
						{
							$this->Cell(5,7,'','0','0');
							$this->Cell(70,10,$arrQry_val["empNo"]." - ".$arrQry_val["empLastName"].", ".$arrQry_val["empFirstName"][0].".".$arrQry_val["empMidName"][0].".",'0','1','L');
						}
						$this->Cell(25,7,date("m/d/Y", strtotime($arrQry_val["tsDate"])),'1','','C');
						$DayTypeDesc = $this->getDayTypeDescArt($arrQry_val["dayType"]);
						$this->SetFont('Arial','','7');
						$this->Cell(20,7,$DayTypeDesc,'1','0','C');
						$this->SetFont('Arial','','10');
						
						foreach($arrShiftFields as $arrShiftFields_val)
						{
							$this->Cell(19,7,$arrQry_val[$arrShiftFields_val],'1','','C');
						}
						
						$appTypeDesc = $this->getTblData("tblTK_AppTypes", " and tsAppTypeCd='".$arrQry_val["tsAppTypeCd"]."'", "", "sqlAssoc");
													
													
						$this->Cell(20,7,$appTypeDesc["appTypeShortDesc"],'1','0','C');
						
						foreach($arrLogsFields as $arrLogsFields_val)
						{
							$this->Cell(19,7,$arrQry_val[$arrLogsFields_val],'1','','C');
						}
						
						$this->Cell(16,7,$arrQry_val["otIn"],'1','0','C');
						$this->Cell(16,7,$arrQry_val["otOut"],'1','0','C');
						$this->Cell(16,7,($arrQry_val["checkTag"]=='Y'?"YES":""),'1','1','C');
						$empNoExists = 	$arrQry_val["empNo"];	
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
	$shiftCode 			= $_GET['shiftCode'];
	$payGrp				= $_GET['payGrp'];
	
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	
	if ($empNo>"") {$empNo1 = " AND (empTs.empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($shiftCode!="0") {$shiftCode1 = " AND (shiftCode = '{$shiftCode}')";} else {$shiftCode1 = "";}
	if ($payGrp!="0") {$payGrp1 = " AND (empPayGrp = '{$payGrp}')";} else {$payGrp1 = "";}
	
	$qryEmpSchedule = "Select empTs.empNo, empBrnCode, empLastName, empFirstName, empMidName, *
						from tblTk_Timesheet empTs, tblEmpmast empMast
						where empTs.compCode='".$_SESSION["company_code"]."' and empMast.compCode='".$_SESSION["company_code"]."'
						and empTs.empNo=empMast.empNo  $empNo1 $payGrp1 $empBrnCode1 $empDiv1 $empDept1 $empSect1 $shiftCode1
						order by empLastName, tsDate" ;
	
	$resEmpSchedule = $inqTSObj->execQry($qryEmpSchedule);
	$arrEmpSchedule = $inqTSObj->getArrRes($resEmpSchedule);
	
	if(sizeof($arrEmpSchedule)>=1)
	{
		$arrListofBranch = $inqTSObj->getBrnCodes($arrEmpSchedule);
		
		if($inqTSObj->getRecCount($resEmpSchedule)>0)
		{
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displayContent($arrEmpSchedule, $arrListofBranch);
		}
	}
	$pdf->Output();
?>
