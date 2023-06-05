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
			
			$this->SetFont('Courier','','9'); 
			$this->Cell(50,5,"Run Date: " . $newdate,'0','');
			$this->Cell(115,5,$this->compName,'0','','C');
			$this->Cell(30,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
		
			
			
			$this->Cell(50,5,"Report ID: TAXRPT01",'0');
			
			
			
			if($this->fPrint==1)
			{
				$hTitle = "(By Employees) Witholding Tax Report for the Month of ".$this->pdHeadTitle;
				$this->Cell(115,5,$hTitle,'0','','C');
				$this->Ln();
				$this->SetFont('Courier','B','08');
				$this->Ln(3);
				
				$this->Cell(35,6,'EMP. NO.',1,'','L');
				$this->Cell(40,6,'EMPLOYEE NAME',1,'','L');
				$this->Cell(20,6,'TAX STAT',1,'','L');
				$this->Cell(30,6,'TIN NO.',1,'','C');
				$this->Cell(40,6,'GROSS INCOME',1,'','R');
				$this->Cell(30,6,'W/TAX',1,'','R');
			}
			else
			{
				$hTitle = "(By Branch) Witholding Tax Report for the Month of ".$this->pdHeadTitle;
				$this->Cell(115,5,$hTitle,'0','','C');
				$this->Ln();
				$this->SetFont('Courier','B','08');
				$this->Ln(3);
				
				$this->Cell(85,6,'BRANCH NAME',1,'','L');
				$this->Cell(55,6,'TOTAL GROSS EARNINGS',1,'','R');
				$this->Cell(55,6,'TOTAL TAX WITHELD',1,'','R');
			}
			
			$this->Ln(10);
		}
		
		
		function getListofBranch()
		{
						
			$qryBrnch = "SELECT Distinct(empPaySum.empBrnCode) as brnCode, brnDesc
							FROM ".$this->tblPaySum." empPaySum, tblBranch brnch
							WHERE (empPaySum.pdYear = '".$this->payPdYear."') AND (empPaySum.pdNumber IN (".$this->chopMonth[0].",".$this->chopMonth[1].")) and empPaySum.empBrnCode=brnch.brnCode and empPaySum.compCode='".$_SESSION["company_code"]."' and brnch.compCode='".$_SESSION["company_code"]."'
							AND (empPaySum.payGrp = '".$_SESSION["pay_group"]."')  ".$this->whereBrnCode."
							GROUP BY  empPaySum.empBrnCode, brnDesc
							order by brnDesc";
			$resBrnch = $this->execQry($qryBrnch);
			$arrBrnch = $this->getArrRes($resBrnch);
			
			return $arrBrnch;
		}
		
		function displayContent($resQry)
		{
			$this->SetFont('Courier','','8'); 
			
			$arrBranch = $this->getListofBranch();
			$grandErn = $grandTax = 0;
			foreach($arrBranch as $arrBranch_val)
			{
				$this->SetFont('Courier','B','8'); 
				$this->Cell(75,6,$arrBranch_val["brnDesc"],0,'1','L');
				
				$this->SetFont('Courier','','7'); 
				$branchErn = $branchTax = 0;
				foreach($resQry as $resQry_val)
				{
					if($arrBranch_val["brnCode"]==$resQry_val["empBrnCode"])
					{
						$this->Cell(35,6,$resQry_val["empNo"],1,'','L');
						$this->Cell(40,6,$resQry_val["empLastName"].", ".$resQry_val["empFirstName"][0].".".$resQry_val["empMidName"][0].".",1,'','L');
						$this->Cell(20,6,$resQry_val["empTeu"],1,'','L');
						$this->Cell(30,6,$resQry_val["empTin"],1,'','C');
						$this->Cell(40,6,number_format($resQry_val["EmptaxableEarn"],2),1,'','R');
						$this->Cell(30,6,number_format($resQry_val["EmptaxWith"],2),1,'1','R');
						$branchErn+=$resQry_val["EmptaxableEarn"];
						$branchTax+=$resQry_val["EmptaxWith"];
						$grandErn+=$resQry_val["EmptaxableEarn"];
						$grandTax+=$resQry_val["EmptaxWith"];
					}
				}
				
				$this->SetFont('Courier','B','8'); 
				$this->Cell(125,6,'BRANCH TOTAL',1,'','C');
				$this->Cell(40,6,number_format($branchErn,2),1,'','R');
				$this->Cell(30,6,number_format($branchTax,2),1,'1','R');
				$this->SetFont('Courier','','8'); 
				$this->Ln();
			}
			$this->SetFont('Courier','B','8'); 
			$this->Cell(125,6,'GRAND TOTAL',1,'','C');
			$this->Cell(40,6,number_format($grandErn,2),1,'','R');
			$this->Cell(30,6,number_format($grandTax,2),1,'1','R');
			$this->SetFont('Courier','','8'); 
			$this->Ln(10);
			//$this->Cell(200,6,'* * * End of Report * * *','0','','C'); 
		}
		
		function displayContentByBranch($arrTaxWithByBranch)
		{
			$grandErn = $grandTax = 0;
			foreach($arrTaxWithByBranch as $arrTaxWithByBranch_val)
			{
				$this->Cell(85,6,strtoupper($arrTaxWithByBranch_val["brnDesc"]),1,'','L');
				$this->Cell(55,6,number_format($arrTaxWithByBranch_val["EmpTaxableEarn"],2),1,'','R');
				$this->Cell(55,6,number_format($arrTaxWithByBranch_val["EmptaxWith"],2),1,'1','R');
				$grandErn+=$arrTaxWithByBranch_val["EmpTaxableEarn"];
				$grandTax+=$arrTaxWithByBranch_val["EmptaxWith"];
			}
			
			$this->SetFont('Courier','B','8'); 
			$this->Cell(85,6,'GRAND TOTAL',1,'','C');
			$this->Cell(55,6,number_format($grandErn,2),1,'','R');
			$this->Cell(55,6,number_format($grandTax,2),1,'1','R');
			$this->SetFont('Courier','','8'); 
			$this->Ln(10);
			//$this->Cell(200,6,'* * * End of Report * * *','0','','C'); 
		}
		
		
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(195,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','',9);
			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}

	
	$pdf = new PDF('P', 'mm', 'LETTER');
	$pdf->topType		=	$_GET["topType"];
	$payPd      		= 	$_GET['payPd'];
	$pdf->chopMonth 	= 	split("-",$payPd);
	$pdf->payPdYear 	= 	$pdf->chopMonth[2];
	$payPdNum 			= 	$pdf->chopMonth[4];
	$payPdMonthName		= 	$pdf->chopMonth[5];
	//$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$table				= 	$_GET["table"];
	$empBranchCode      = 	$_GET['empBrnCode'];
	$pdf->topType			= 	$_GET['topType'];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	$pdf->tblPaySum		= 	($table=='tblDeductionsHist'?"tblPayrollSummaryHist":"tblPayrollSummary");
	$pdMonthname		=	$inqTSObj-> getPayMonth($pdf->chopMonth[0].",".$pdf->chopMonth[1], $pdf->payPdYear);
	$pdMonthName		=	date("F", strtotime($pdMonthname."/".date("d")."/".$pdf->payPdYear));
	
	$pdf->pdHeadTitle	=	$pdMonthName;
	
	$pdf->groupName 	= 	($_SESSION["pay_group"]==1?"GROUP 1":"GROUP 2");
	//$pdf->catName		=	$catName["payCatDesc"]; 
	$pdf->topType = $topType;
	
	
	$pdf->whereBrnCode = ($empBranchCode!=0?" and emppaySum.empBrnCode='".$empBranchCode."'":"");
	
	if($pdf->topType=='3')
	{
		$RptDetails=1;
		$RptSummary=1;
	}
	else
	{
		if($pdf->topType=='1')
			$RptDetails=1;
		else
			$RptSummary=1;
	}
	
	if($RptDetails=='1')
	{
		$pdf->fPrint = 1;
		
		$qryEmpList =	"SELECT     empPaySum.empNo, empPaySum.empBrnCode, empBrnch.brnDesc, empmast.empLastName, empmast.empFirstName, empmast.empMidName, empmast.empTeu, 
						empmast.empTin, SUM(empPaySum.taxableEarnings) AS EmptaxableEarn, SUM(empPaySum.taxWitheld) AS EmptaxWith
						FROM         ".$pdf->tblPaySum." empPaySum INNER JOIN
						tblEmpMast empmast ON empPaySum.empNo = empmast.empNo INNER JOIN
						tblBranch empBrnch ON empPaySum.empBrnCode = empBrnch.brnCode
						WHERE     (empPaySum.pdYear = '".$pdf->payPdYear."') AND (empPaySum.pdNumber IN (".($pdf->chopMonth[1]=='25'?'23,24,25':$pdf->chopMonth[0].",".$pdf->chopMonth[1]).")) AND (empPaySum.payGrp = '".$_SESSION["pay_group"]."')
						AND (empBrnch.compCode = '".$_SESSION["company_code"]."') ".$pdf->whereBrnCode."
						GROUP BY empPaySum.empNo,  empPaySum.empBrnCode, empBrnch.brnDesc, empmast.empLastName, empmast.empFirstName, empmast.empMidName, empmast.empTeu, 
						empmast.empTin
						ORDER BY empBrnch.brnDesc, empmast.empLastName";
	
		$resEmpList = $inqTSObj->execQry($qryEmpList);
		$arrEmpList = $inqTSObj->getArrRes($resEmpList);
		if(count($arrEmpList)>=1)
		{
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displayContent($arrEmpList);
		}
		
	}
	
	if($RptSummary=='1')
	{
		
		$pdf->fPrint = 0;
		 	$qryEmpList =	"SELECT     empPaySum.empNo, empPaySum.empBrnCode, empBrnch.brnDesc, empmast.empLastName, empmast.empFirstName, empmast.empMidName, empmast.empTeu, 
							empmast.empTin, SUM(empPaySum.taxableEarnings) AS EmptaxableEarn, SUM(empPaySum.taxWitheld) AS EmptaxWith
							FROM         ".$pdf->tblPaySum." empPaySum INNER JOIN
							tblEmpMast empmast ON empPaySum.empNo = empmast.empNo INNER JOIN
							tblBranch empBrnch ON empPaySum.empBrnCode = empBrnch.brnCode
							WHERE     (empPaySum.pdYear = '".$pdf->payPdYear."') AND (empPaySum.pdNumber IN (".($pdf->chopMonth[1]=='25'?'23,24,25':$pdf->chopMonth[0].",".$pdf->chopMonth[1]).")) AND (empPaySum.payGrp = '".$_SESSION["pay_group"]."')
							AND (empBrnch.compCode = '".$_SESSION["company_code"]."') ".$pdf->whereBrnCode."
							GROUP BY empPaySum.empNo,  empPaySum.empBrnCode, empBrnch.brnDesc, empmast.empLastName, empmast.empFirstName, empmast.empMidName, empmast.empTeu, 
							empmast.empTin
							ORDER BY empBrnch.brnDesc, empmast.empLastName";
		
		$resBranchEmpList = $inqTSObj->execQry($qryEmpList);
		$arrBranchEmpListt = $inqTSObj->getArrRes($resBranchEmpList);
		if(count($arrBranchEmpListt)>=1)
		{
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displayContentByBranch($arrBranchEmpListt);
		}
		
	}
	
	$pdf->Output();
?>
