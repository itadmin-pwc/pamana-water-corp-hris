<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$pagRemObj = new inqTSObj();
	$sessionVars = $pagRemObj->getSeesionVars();
	$pagRemObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
		
			$this->SetFont('Courier','','10'); 
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$hTitle = " Employee Listing - Updated Minimum Wage Tag";
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(70,5,"Report ID: EMPLIST-MINWAGE");
			$this->Cell(140,5,$this->compName,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			
			$this->SetFont('Courier','B','10');
			$this->Cell(30,5,'EMP. NO.','0','','L');
			$this->Cell(40,5,'LAST NAME','0','','L');
			$this->Cell(40,5,'FIRST NAME','0','','L');
			$this->Cell(70,5,'EMP. BRANCH','0','','L');
			if($_SESSION['user_level']!=3)
				$this->Cell(40,5,'DAILY RATE','0','','L');
			$this->Cell(35,5,'BRANCH MIN. WAGE','0','','L');
			
			$this->Ln();
			
		}
		
		function EmpListMinWage($arrsqlEmp)
		{
			$this->SetFont('Arial','','9'); 
			$cntRecords = count($arrsqlEmp);
			$cnt = 1;
			$emp_cnt = 1;
			
			
			foreach($arrsqlEmp as $arrsqlEmp_val)
			{
				
				$this->Cell(30,6,$arrsqlEmp_val["empNo"],'0','0','L',0);
				$this->Cell(40,6,$arrsqlEmp_val["empLastName"],'0','0','L',0);
				$this->Cell(40,6,$arrsqlEmp_val["empFirstName"],'0','0','L',0);
				$this->Cell(70,6,$arrsqlEmp_val["brnDesc"],'0','0','L',0);
				if($_SESSION['user_level']!=3)
					$this->Cell(40,6,$arrsqlEmp_val["empDrate"],'0','0','R',0);
				$this->Cell(35,6,$arrsqlEmp_val["brnMinWage"],'0','1','R',0);
				
				
			}
		}
		
		function Footer_Page($cnt_lp, $emp_cnt)
		{
			$this->Ln(2
			
			);
			
			//$this->Ln(20);
			
			$this->SetFont('Arial','B','9'); 
			$this->Image(RA1_FOOTER, 10, 167 , '310' , '28' , 'JPG', '');
			
			$this->Cell(33,6,'','0','0','',0);
			$this->Cell(10,6,$emp_cnt,'0','1','',0);
			
			$this->Cell(43,4,'','0','0','',0);
			$this->Cell(67,4,substr($this->userbrnch,0,25),'0','1','C',0);
			$this->Ln(2);
			$this->Cell(9,4,'','0','0','',0);
			$this->Cell(8,4,$this->PageNo(),'0','0','',0);
			$this->Cell(4,4,'','0','0','',0);
			$this->Cell(10,4,'{nb}','0','1','',0);
			$this->Cell(43,4,'','','0','',0);
			$this->SetFont('Arial','B','7'); 
			$this->Cell(44,4,substr($this->userbrnchpos,0,25),'0','0','',0);
			$this->Cell(18,4,date('m/d/Y'),'0','0','',0);
			$this->SetFont('Arial','B','9'); 
			
		}
		
	}
	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$compCode = $_GET["compCode"];
	$monthfr =  $_GET["monthfr"];
	$monthto =  $_GET["monthto"];
	$arrcompName = $pagRemObj->getCompany($compCode);
	$pdf->compName = $arrcompName["compName"];
	
	$sqlEmp = "SELECT     wageHist.empNo, empMast.empLastName, empMast.empFirstName, wageHist.empDrate, wageHist.empBrnCode, brnch.brnDesc, wageHist.brnMinWage, wageHist.dateUpdated
				FROM   tblEmpMinWageHist wageHist INNER JOIN
					   tblEmpMast empMast ON wageHist.empNo = empMast.empNo INNER JOIN
					   tblBranch brnch ON wageHist.empBrnCode = brnch.brnCode
				WHERE     (wageHist.compCode = '".$_SESSION["company_code"]."') AND (empMast.compCode = '".$_SESSION["company_code"]."') AND (brnch.compCode = '".$_SESSION["company_code"]."')
						AND wageHist.dateUpdated between '".date("Y-m-d", strtotime($monthfr))."' and '".date("Y-m-d", strtotime($monthto))."' ";
	$ressqlEmp = $pagRemObj->execQry($sqlEmp);
	$arrsqlEmp = $pagRemObj->getArrRes($ressqlEmp);
	if(count($arrsqlEmp)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$arrprintedby = $pagRemObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		
		$pdf->EmpListMinWage($arrsqlEmp);
	}
	
	$pdf->Output('Employee Lists of Minimum Wage.pdf','D');
?>