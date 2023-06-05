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
		var $printedby;
		var $company;
		var $rundate;
		var $table;
		var $reportlabel;
		var $arrPayPd;
		function Header()
		{
			$this->SetFont('Courier','','9'); 
			$this->Cell(100,5,"Run Date: " . $this->rundate);
			$this->Cell(200,5,$this->company);
			$this->Cell(35,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			$this->Cell(100,5,"Report ID: MNTHLYYTD");
			$arrPd = explode("-",$_GET['payPd']);
			$pdYear = $arrPd[2];
			
			switch($arrPd[0]) {
				case 1:
					$pdMonth = "January $pdYear";
				break;
				case 3:
					$pdMonth = "February $pdYear";
				break;
				case 5:
					$pdMonth = "March $pdYear";
				break;
				case 7:
					$pdMonth = "April $pdYear";
				break;
				case 9:
					$pdMonth = "May $pdYear";
				break;
				case 11:
					$pdMonth = "June $pdYear";
				break;
				case 13:
					$pdMonth = "July $pdYear";
				break;
				case 15:
					$pdMonth = "August $pdYear";
				break;
				case 17:
					$pdMonth = "September $pdYear";
				break;
				case 19:
					$pdMonth = "October $pdYear";
				break;
				case 21:
					$pdMonth = "November $pdYear";
				break;
				case 23:
					$pdMonth = "December $pdYear";
				break;
			}
			$this->Cell(184,5,'YTD Payroll Reg. for the Month of '.$pdMonth);
			$this->Ln();
			
			$this->SetFont('Courier','B','9'); 
			$this->Cell(80,6,'DEPARTMENT',0);
			$this->Cell(40,6,'GROSS INCOME',0,0,'R');
			$this->Cell(40,6,'W/ TAX',0,0,'R');
			$this->Cell(40,6,'PREV YR WTAX ADJ',0,0,'R');
			$this->Cell(40,6,'ECOLA',0,0,'R');
			$this->Cell(40,6,'13TH MONTH NON TAX',0,0,'R');
			$this->Cell(40,6,'13TH MONTH TAX',0,1,'R');
		}
		function Data($Dept,$gross,$wtax,$ecola,$n13thNT,$n13thT,$taxAdj) {
			$this->SetFont('Courier','','9'); 
			$this->Cell(80,6,$Dept,0,0);
			$this->Cell(40,6,number_format($gross,2),0,0,'R');
			$this->Cell(40,6,number_format($wtax,2),0,0,'R');
			$this->Cell(40,6,number_format($taxAdj,2),0,0,'R');
			$this->Cell(40,6,number_format($ecola,2),0,0,'R');
			$this->Cell(40,6,number_format($n13thNT,2),0,0,'R');
			$this->Cell(40,6,number_format($n13thT,2),0,1,'R');
		}
		
		function Total($gross,$wtax,$ecola,$n13thNT,$n13thT,$taxAdj) {
			$this->SetFont('Courier','B','9'); 
			$this->Cell(80,6,'BRANCH TOTAL',0,0);
			$this->Cell(40,6,number_format($gross,2),0,0,'R');
			$this->Cell(40,6,number_format($wtax,2),0,0,'R');
			$this->Cell(40,6,number_format($taxAdj,2),0,0,'R');
			$this->Cell(40,6,number_format($ecola,2),0,0,'R');
			$this->Cell(40,6,number_format($n13thNT,2),0,0,'R');
			$this->Cell(40,6,number_format($n13thT,2),0,1,'R');
		}

		function GrandTotal($arrBranchGross,$arrBranchTax,$arrBranchEcola,$arrBranch13thNT,$arrBranch13thT,$arrtaxAdj,$arrDept) {
			$this->AddPage();
			$this->SetFont('Courier','B','9');
			$this->Cell(40,6,'All Branches',0,1);					
			$arrDept = array_unique($arrDept);
			$totGross 	= 0;
			$totTax 	= 0;
			$totTaxAdj 	= 0;
			$totEcola 	= 0;
			$tot13thNT 	= 0;
			$tot13thT 	= 0;
			for($i=0;$i<count($arrDept);$i++) {
				$totGross 	+= $arrBranchGross[$arrDept[$i]];
				$totTax 	+= $arrBranchTax[$arrDept[$i]];
				$totTaxAdj 	+= $arrtaxAdj[$arrDept[$i]];
				$totEcola 	+= $arrBranchEcola[$arrDept[$i]];
				$tot13thNT 	+= $arrBranch13thNT[$arrDept[$i]];
				$tot13thT 	+= $arrBranch13thT[$arrDept[$i]];
				$this->SetFont('Courier','','9'); 
				$this->Cell(80,6,$arrDept[$i],0,0);
				$this->Cell(40,6,number_format($arrBranchGross[$arrDept[$i]],2),0,0,'R');
				$this->Cell(40,6,number_format($arrBranchTax[$arrDept[$i]],2),0,0,'R');
				$this->Cell(40,6,number_format($arrtaxAdj[$arrDept[$i]],2),0,0,'R');
				$this->Cell(40,6,number_format($arrBranchEcola[$arrDept[$i]],2),0,0,'R');
				$this->Cell(40,6,number_format($arrBranch13thNT[$arrDept[$i]],2),0,0,'R');
				$this->Cell(40,6,number_format($arrBranch13thT[$arrDept[$i]],2),0,1,'R');
			}
				$this->SetFont('Courier','B','9'); 
				$this->Cell(80,6,'GRAND TOTAL',0,0);
				$this->Cell(40,6,number_format($totGross,2),0,0,'R');
				$this->Cell(40,6,number_format($totTax,2),0,0,'R');
				$this->Cell(40,6,number_format($totTaxAdj,2),0,0,'R');
				$this->Cell(40,6,number_format($totEcola,2),0,0,'R');
				$this->Cell(40,6,number_format($tot13thNT,2),0,0,'R');
				$this->Cell(40,6,number_format($tot13thT,2),0,1,'R');
			
		}

		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',9);
			$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}
	
	$pdf=new PDF('L', 'mm', 'LEGAL');
	$inqTSObj=new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	//Column titles
	//Data loading
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$payPd = $_GET['payPd'];
	$empDiv = $_GET['empDiv'];
	$empSect = $_GET['empSect'];
	$empDept = $_GET['empDept'];
	if ($empDiv>"" && $empDiv>0) {$empDivfilter = " AND (tblPayrollSummaryHist.empDivCode = '{$empDiv}')";} else {$empDivfilter = "";}
	if ($empDept>"" && $empDept>0) {$empDeptfilter = " AND (tblPayrollSummaryHist.empDepCode = '{$empDept}')";} else {$empDeptfilter = "";}	
	$arrYTD = $inqTSObj->getYTDData($payPd,"$empDivfilter $empDeptfilter");
	$pdf->AliasNbPages();
	$pdf->reportlabel = $reportLabel;
	$pdf->company = $inqTSObj->getCompanyName($_SESSION['company_code']);
	$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
	$pdf->rundate=$inqTSObj->currentDateArt();
		$branch="";
		$ctr=0;
		$i=0;
		$totRec = count($arrYTD);
		$GtotGross 		= 0;
		$GtotTax 		= 0;
		$GtotTaxAdj		= 0;
		$GtotEcola 		= 0;
		$Gtot13thNT 	= 0;
		$Gtot13thT 		= 0;
		$arrDept 		= array();
		foreach($arrYTD as $valYTD){
			if ($valYTD['brnDesc'] != $branch) {
				if ($ch == 0) {
					$pdf->Total($totGross,$totTax,$totEcola,$tot13thNT,$tot13thT,$totTaxAdj);
					$ch++;
				}			
				$pdf->AddPage();
				$ch			= 0;
				$totGross 	= 0;
				$totTax 	= 0;
				$totTaxAdj 	= 0;
				$totEcola 	= 0;
				$tot13thNT 	= 0;
				$tot13thT 	= 0;
				$pdf->SetFont('Courier','B','9');
				$pdf->Cell(40,6,$valYTD['brnDesc'],0,1);
				$branch = $valYTD['brnDesc'];
				
			}
			$ctr++;
			$pdf->Data($valYTD['deptDesc'],$valYTD['grossearnings'],$valYTD['tax'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax'],$valYTD['YearEnd']);
			$arrBranchGross[$valYTD['deptDesc']]		+= $valYTD['grossearnings'];
			$arrBranchTax[$valYTD['deptDesc']]		 	+= $valYTD['tax'];
			$arrBranchTaxAdj[$valYTD['deptDesc']]		+= $valYTD['YearEnd'];
			$arrBranchEcola[$valYTD['deptDesc']]	 	+= $valYTD['ecola'];
			$arrBranch13thNT[$valYTD['deptDesc']]	 	+= $valYTD['N13thNontax'];
			$arrBranch13thT[$valYTD['deptDesc']]	 	+= $valYTD['N13thTax'];
			if (!in_array($valYTD['deptDesc'],$arrDept)) {
				$arrDept[] = $valYTD['deptDesc'];
			}	
			$totGross 	+= round($valYTD['grossearnings'],2);
			$totTax 	+= round($valYTD['tax'],2);
			$totTaxAdj 	+= round($valYTD['YearEnd'],2);
			$totEcola 	+= round($valYTD['ecola'],2);
			$tot13thNT 	+= round($valYTD['N13thNontax'],2);
			$tot13thT 	+= round($valYTD['N13thTax'],2);
				if ($ctr == $totRec) {
					$pdf->Total($totGross,$totTax,$totEcola,$tot13thNT,$tot13thT,$totTax,$totTaxAdj);
					$pdf->GrandTotal($arrBranchGross,$arrBranchTax,$arrBranchEcola,$arrBranch13thNT,$arrBranch13thT,$arrBranchTaxAdj,$arrDept);					
				}			
		}
	
	
	$pdf->Output();
?>