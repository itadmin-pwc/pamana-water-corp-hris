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
			
			$this->Cell(80,5,"Report ID: EMPSCHEDULES");
			$hTitle = "Current Employee Schedule";
			$this->Cell(170,5,$hTitle,'0','0','C');
			$this->Ln(5);
			$this->Ln();
		
			
			
			$this->SetFont('Arial','B','10');
			$this->Cell(100,7,'','0','','C');
			$this->Cell(95,7,'TIME EXEMPTIONS','1','','C');
			$this->Cell(140,7,'SHIFT DETAIL','1','1','C');
			
			$this->Cell(70,7,'EMP. INFO','1','','C');
			$this->Cell(30,7,'BIO - NUMBER','1','','C');
			
			$this->Cell(18,7,'ABSENT','1','','C');
			$this->Cell(20,7,'TARD. HRS','1','','C');
			$this->Cell(17,7,'UT. HRS','1','','C');
			$this->Cell(18,7,'OT. HRS','1','','C');
			$this->Cell(22,7,'LUNCH. HRS','1','','C');
			
			$this->Cell(20,7,'DAY','1','','C');
			$this->Cell(20,7,'TIME-IN','1','','C');
			$this->Cell(20,7,'LUNCH-OUT','1','','C');
			$this->Cell(20,7,'LUNCH-IN','1','','C');
			$this->Cell(20,7,'BREAK-IN','1','','C');
			$this->Cell(20,7,'BREAK-OUT','1','','C');
			$this->Cell(20,7,'TIME-OUT','1','1','C');
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
			foreach($arrBranch as $arrBranch_val)
			{
				$this->SetFont('Arial','B','10');
				$this->Cell(260,10,$arrBranch_val["brn_Desc"],'0','1');
				
				foreach($arrQry as $arrQry_val)
				{
					$this->SetFont('Arial','','10');
					if($arrQry_val["empBrnCode"]==$arrBranch_val["empBrnCode"])
					{
						$this->Cell(70,7,$arrQry_val["empNo"]." - ".$arrQry_val["empLastName"].", ".$arrQry_val["empFirstName"][0].".".$arrQry_val["empMidName"][0].".",'1','0','L');
						$this->Cell(30,7,$arrQry_val["bioNo"],'1','0','C');
						$this->Cell(18,7,$arrQry_val["absentExempt"],'1','','C');
						$this->Cell(20,7,$arrQry_val["trdHrsExempt"],'1','','C');
						$this->Cell(17,7,$arrQry_val["utHrsExempt"],'1','','C');
						$this->Cell(18,7,$arrQry_val["otExempt"],'1','','C');
						$this->Cell(22,7,$arrQry_val["lunchHrsExempt"],'1','','C');
						
						$arr_ShiftCode_Dtl = $this->getTblData("tblTK_ShiftDtl", " and shftCode='".$arrQry_val["shiftCode"]."'", " order by dayCode", "");
						$ctr = 0;
						foreach($arr_ShiftCode_Dtl as $arr_ShiftCode_Dtl_val)
						{
							if($ctr==0)
							{
								$this->Cell(20,7,$dayDesc[$arr_ShiftCode_Dtl_val["dayCode"]],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftTimeIn"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftLunchOut"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftLunchIn"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftBreakOut"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftBreakIn"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftTimeOut"],'1','1','C');
							
							}
							else
							{
								$this->Cell(195,7,'','0','0','C');
								$this->Cell(20,7,$dayDesc[$arr_ShiftCode_Dtl_val["dayCode"]],'1','0','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftTimeIn"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftLunchOut"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftLunchIn"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftBreakOut"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftBreakIn"],'1','','C');
								$this->Cell(20,7,$arr_ShiftCode_Dtl_val["shftTimeOut"],'1','1','C');
							}
							$ctr++;
						}
							
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
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	
	if ($empNo>"") {$empNo1 = " AND (empShift.empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($shiftCode!="0") {$shiftCode1 = " AND (shiftCode = '{$shiftCode}')";} else {$shiftCode1 = "";}
	
	$qryEmpSchedule = "Select empShift.empNo, empBrnCode, empLastName, empFirstName, empMidName, shiftCode, bioNo, absentExempt, trdHrsExempt, utHrsExempt, otExempt, lunchHrsExempt
						from tblTk_EmpShift empShift, tblEmpmast empMast
						where empShift.compCode='".$_SESSION["company_code"]."' and empMast.compCode='".$_SESSION["company_code"]."'
						and empShift.empNo=empMast.empNo and status='A' $empNo1 $empBrnCode1 $empDiv1 $empDept1 $empSect1 $shiftCode1
						order by empLastName" ;
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
