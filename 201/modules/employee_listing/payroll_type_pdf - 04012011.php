<?php
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	03/24/2010
		Function		:	Blacklist Module (Pop Up) 
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("common_obj.php");
	include("../../../includes/pdf/fpdf.php");
	
	$payrollTypeObj = new inqTSObj();
	$sessionVars = $payrollTypeObj->getSeesionVars();
	$payrollTypeObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
		
			$this->SetFont('Courier','','10'); 
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$hTitle = " Payroll Listing By Payroll Type Report";
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(70,5,"Report ID: PAYLIST");
			
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			
			$this->SetFont('Courier','B','10');
			$this->Cell(20,5,'NO.','0','','L');
			$this->Cell(50,5,'EMPLOYEE NAME','0','','L');
			$this->Cell(47.5,5,'BRANCH','0','','L');
			$this->Cell(47.5,5,'DIVISION','0','','L');
			$this->Cell(47.5,5,'DEPARTMENT','0','','L');
			$this->Cell(47.5,5,'SECTION','0','','L');
			$this->Ln();
			
		}
		
		function getRankType()
		{
			$qryRank =	"SELECT  rankCode, rankDesc FROM tblEmpMast tblEmp, tblRankType 
						WHERE  empRank=rankCode ".$this->where."
						group by rankCode, rankDesc
						order by rankDesc";
			$rsRank = $this->execQry($qryRank);
			return $this->getArrRes($rsRank);
		}
		
		function displayContent($arrEmp)
		{
			$this->Ln(5);
			
			$ctr = 1;
			
			$arrRank = $this->getRankType();
			
			foreach($arrRank as $arrRank_val)
			{
				
				$this->SetFont('Courier','B','9'); 
				$this->Cell(50,5,strtoupper($arrRank_val["rankDesc"]),'0','1','L');
				$this->Cell(260,1,'','T');
				$this->Ln();
				$ctr = 1;
				$sum_emp = 0;
				foreach($arrEmp as $arrEmp_val)
				{
					if($arrEmp_val["empRank"]==$arrRank_val["rankCode"])
					{
						
						$this->SetFont('Courier','','9');
						$this->Cell(20,5,$ctr.".",'0','','L');
						$this->Cell(50,5,$arrEmp_val["empLastName"].", ".$arrEmp_val["empFirstName"][0].".".$arrEmp_val["empMidName"][0].".",'0','0','L');
						$this->Cell(47.5,5,substr($arrEmp_val["brnShortDesc"],0,24),'0','0','L');
						
						$arrdivDesc = $this->getDivDescArt($arrEmp_val["compCode"],$arrEmp_val["empDiv"]);
						$this->Cell(47.5,5,substr($arrdivDesc["deptDesc"],0,24),'0','0','L');
						$arrdeptDesc = $this->getDeptDescGen($arrEmp_val["compCode"],$arrEmp_val["empDiv"],$arrEmp_val["empDepCode"]);
						$this->Cell(47.5,5,substr($arrdeptDesc["deptDesc"],0,24),'0','0','L');
						$arrsectDesc = $this->getSectDescArt($arrEmp_val["compCode"],$arrEmp_val["empDiv"],$arrEmp_val["empDepCode"],$arrEmp_val["empSecCode"]);
						$this->Cell(47.5,5,substr($arrsectDesc["deptDesc"],0,24),'0','1','L');
						$sum_emp++;
						$ctr++;		
					}			
				}
				$this->SetFont('Courier','B','9');
				$this->Cell(20,5,'TOTAL','0','0','L');
				$this->Cell(20,5,$sum_emp,'0','1','L');
				$this->Ln();
			}
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(260,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',9);
			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}
	
	
	
	$pdf = new PDF('L', 'mm', 'LETTER');
	
	$empBrnCode = $_GET['empBrnCode'];
	$empDiv = $_GET['empDiv'];
	$empDept = $_GET['empDept'];
	$empSect = $_GET['empSect'];
	
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	
	$pdf->where = " and (tblEmp.compCode = '".$_SESSION["company_code"]."') $empDiv1 $empDept1 $empSect1 $empBrnCode1";
		
	$sqlEmp = "SELECT empmast.compCode,empNo, empLastName, empFirstName, empMidName,empmast.empBrnCode, brnShortDesc,empRank, empDiv, empDepCode, empSecCode
		FROM tblEmpMast empmast, tblBranch brnCode
		WHERE (empmast.compCode = '".$_SESSION["company_code"]."' and brnCode.compCode='".$_SESSION["company_code"]."') 
	 	$empDiv1 $empDept1 $empSect1 $empBrnCode1 
		and empmast.empBrnCode=brnCode.brnCode and empStat='RG' and empNo not in (Select empNo from tblLastPayEmp) and empRank in (2,3,4)
		order by brnDesc, empLastName, empFirstName, empMidName ";		
				
	/*$sqlEmp = "SELECT * FROM tblEmpMast 
			   WHERE (compCode = '".$_SESSION["company_code"]."') 
			   $empDiv1 $empDept1 $empSect1 $empBrnCode1 
			   order by empLastName, empFirstName, empMidName ";
	*/
	$resEmp = $payrollTypeObj->execQry($sqlEmp);	
	$arrEmp = $payrollTypeObj->getArrRes($resEmp);
	
	if($payrollTypeObj->getRecCount($resEmp)>0)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $payrollTypeObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrEmp);
	}	
	
	$pdf->Output('PAYROLL_TYPE_PROOFLIST.pdf','D');
?>