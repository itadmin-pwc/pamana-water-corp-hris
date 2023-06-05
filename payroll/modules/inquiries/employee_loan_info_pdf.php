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
			
			
			$this->SetFont('Arial','','10'); 
			$this->Cell(80,5,"Run Date: " . $newdate,"0");
			$this->Cell(170,5,$this->compName,"0",'0','C');
			$this->Cell(85,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			$this->Cell(80,5,"Report ID: LOANINFO1");
			$this->Cell(170,5,'Employee Loan Information','0','0','C');
			
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln(5);
		}
		
		
		
		function displayContent($arrLoanInfoval)
		{
			$this->SetFont('Arial','','10'); 
			$this->Cell(70,8,"Employee No. ",'','0','');
			$this->Cell(2,8,":",'','0','');
			$this->Cell(70,8,$this->empNo,'','1','');
			$this->Cell(70,8,"Employee Name ","");
			$this->Cell(2,8,":",'','0','');
			$this->Cell(70,8,$this->empName,'','1','');
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Cell(80,8,"Loan Type",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,$arrLoanInfoval["lonTypeDesc"],'1','0','');
			$this->Cell(20,8,"",'0','0','');
			$this->Cell(80,8,"Amount of Deduction per Schedule",'','0','');
			$this->Cell(2,8,"",'','0','');
			$this->Cell(70,8,'','','1','');
			
			
			$this->Cell(80,8,"Loan Ref. No",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,$arrLoanInfoval["lonRefNo"],'1','0','');
			$this->Cell(20,8,"",'0','0','');
			$this->Cell(80,8,"Deduction (Exclusive of Interest)",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,number_format($arrLoanInfoval["lonDedAmt1"],2),'1','1','');
			
			$this->Cell(80,8,"Loan Amount (Principal)",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,number_format($arrLoanInfoval["lonAmt"],2),'1','0','');
			$this->Cell(20,8,"",'0','0','');
			$this->Cell(80,8,"Deduction (Inclusive of Interest)",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,number_format($arrLoanInfoval["lonDedAmt2"],2),'1','1','');
			
			$this->Cell(80,8,"Loan Amount Inclusive of Interest",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,number_format($arrLoanInfoval["lonWidInterst"],2),'1','0','');
			$this->Cell(20,8,"",'0','0','');
			$this->Cell(80,8,"Payments",'','0','');
			$this->Cell(2,8,"",'','0','');
			$this->Cell(70,8,'','','1','');
			
			$this->Cell(80,8,"Date Granted",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,date("m/d/Y", strtotime($arrLoanInfoval["lonGranted"])),'1','0','');
			$this->Cell(20,8,"",'0','0','');
			$this->Cell(80,8,"Total Amt. of Payments To - Date",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,number_format($arrLoanInfoval["lonPayments"],2),'1','1','');
			
			$this->Cell(80,8,"Start Date of Deduction",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,date("m/d/Y", strtotime($arrLoanInfoval["lonStart"])),'1','0','');
			$this->Cell(20,8,"",'0','0','');
			$this->Cell(80,8,"No. of Payments Made",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,$arrLoanInfoval["lonPaymentNo"],'1','1','');
			
			$this->Cell(80,8,"End Date of Deduction",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,date("m/d/Y", strtotime($arrLoanInfoval["lonEnd"])),'1','0','');
			$this->Cell(20,8,"",'0','0','');
			$this->Cell(80,8,"Current Loan Balance",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,number_format($arrLoanInfoval["lonCurbal"],2),'1','1','');
			
			$lonSked = $arrLoanInfoval["lonSked"];
			if($lonSked=='1')
				$lonSked = "1st Period";
			elseif($lonSked=='2')
				$lonSked = "2nd Period";
			else
				$lonSked = "BOTH";
			
			$this->Cell(80,8,"Period of Deduction",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,$lonSked,'1','0','');
			$this->Cell(20,8,"",'0','0','');
			$this->Cell(80,8,"Date of Last Payments",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,date("m/d/Y", strtotime($arrLoanInfoval["lonLastPay"])),'1','1','');
			
			
			$lonStat = $arrLoanInfoval["lonStat"];
			if($lonStat=='C')
				$lonStat = "Closed";
			elseif($lonStat=='T')
				$lonStat = "Terminated";
			else
				$lonStat = "Open";

			
			$this->Cell(80,8,"Total No. of Payments",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,$arrLoanInfoval["lonNoPaymnts"],'1','0','');
			$this->Cell(20,8,"",'0','0','');
			$this->Cell(80,8,"Loan Status",'1','0','');
			$this->Cell(2,8,":",'1','0','');
			$this->Cell(70,8,$lonStat,'1','1','');
			
			
			
		}
		
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Arial','B',10);
			$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}

	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	
	$pdf->empNo         		= 	$_GET['empNo'];
	$loanTypeCd        	= 	$_GET['loanTypeCd'];
	$loanTypeRefNo       = 	$_GET['loanRefNo'];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	
					  
	
	 $qryLoanInfo = "SELECT     empLoans.empNo, empLoans.lonTypeCd, lonType.lonTypeDesc, empLoans.lonRefNo, empLoans.lonAmt, empLoans.lonWidInterst, 
                      empLoans.lonGranted, empLoans.lonStart, empLoans.lonEnd, empLoans.lonSked, empLoans.lonNoPaymnts, empLoans.lonDedAmt1, 
                      empLoans.lonDedAmt2, empLoans.lonPayments, empLoans.dateadded, empLoans.closedby AS lonPaymentsNo, empLoans.lonCurbal, 
                      empLoans.lonLastPay, empLoans.lonStat, empLoans.lonPaymentNo
						FROM         tblEmpLoans empLoans INNER JOIN
                      tblLoanType lonType ON empLoans.lonTypeCd = lonType.lonTypeCd
						WHERE     (empLoans.empNo = '".$pdf->empNo."') AND (empLoans.compCode = '".$_SESSION["company_code"]."') AND (empLoans.lonTypeCd = '".$loanTypeCd."') AND (empLoans.lonRefNo = '".$loanTypeRefNo."') AND 
                      (lonType.compCode = '".$_SESSION["company_code"]."')";
					  
					  
	$resLoanInfo = $inqTSObj->execQry($qryLoanInfo);
	$arrLoanInfo = $inqTSObj->getSqlAssoc($resLoanInfo);
	if(count($arrLoanInfo)>=1){
		$empName = $inqTSObj->getEmployeeList($_SESSION["company_code"]," and empNo='".$pdf->empNo."'"); 
		$pdf->empName= $empName["empLastName"].", ".$empName['empFirstName']." ".$empName['empMidName'][0].".";
		
		
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		
		$pdf->displayContent($arrLoanInfo);
	}	
		
		
	$pdf->Output();
?>
