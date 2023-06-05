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
			$this->Cell(40,5,'LAST NAME','0','','L');
			$this->Cell(40,5,'FIRST NAME','0','','L');
			$this->Cell(10,5,'MI','0','','L');
			$this->Cell(67,5,'POSITION TITLE','0','','L');
			$this->Cell(25,5,'DATE HIRED','0','','L');
			$this->Cell(25,5,'BIRTH DATE','0','','L');
			$this->Cell(20.5,5,'EMP. STATUS','0','','L');
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
						$this->Cell(40,5,$arrEmp_val["empLastName"],'0','0','L');
						$this->Cell(40,5,$arrEmp_val["empFirstName"],'0','0','L');
						$this->Cell(10,5,$arrEmp_val["empMidName"][0].".",'0','0','L');
						
						$arrprintedby_pos = $this->getpositionwil(" where divCode='".$arrEmp_val["empDiv"]."' and deptCode='".$arrEmp_val["empDepCode"]."' and sectCode='".$arrEmp_val["empSecCode"]."'",2);
						
						$this->Cell(67,5,trim(substr($arrprintedby_pos["posDesc"], 0,25)),'0','0','L');
						$this->Cell(25,5,date("m/d/Y", strtotime($arrEmp_val["dateHired"])),'0','0','C');
						$this->Cell(25,5,date("m/d/Y", strtotime($arrEmp_val["empBday"])),'0','0','C');
						$this->Cell(20,5,$arrEmp_val["empStat"],'0','1','L');
						
					
						$sum_emp++;
						$ctr++;		
					}			
				}
				$this->SetFont('Courier','B','9');
				$this->Cell(40,5,'TOTAL '.strtoupper($arrRank_val["rankDesc"]),'0','0','L');
				$this->Cell(20,5,$sum_emp,'0','1','L');
				$this->Ln();
				$grand_emp+=$sum_emp;
			}
				$this->SetFont('Courier','B','9');
				$this->Cell(40,5,'GRAND TOTAL ','0','0','L');
				$this->Cell(20,5,$grand_emp,'0','1','L');
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
		
	$sqlEmp = "SELECT empmast.compCode,empNo, empLastName, empFirstName, empMidName,empmast.empBrnCode, brnShortDesc,empRank, empDiv, empDepCode, empSecCode, dateHired, empBday, empStat
		FROM tblEmpMast empmast, tblBranch brnCode
		WHERE (empmast.compCode = '".$_SESSION["company_code"]."' and brnCode.compCode='".$_SESSION["company_code"]."') 
	 	$empDiv1 $empDept1 $empSect1 $empBrnCode1 
		and empmast.empBrnCode=brnCode.brnCode and empNo not in (Select empNo from tblLastPayEmp) and empStat in ('RG', 'PR', 'CN')
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