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
			$this->Cell(100,5,"Report ID: YEARLYYTD");
			$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
			$this->Cell(184,5,'YTD by Month for the Year '.$Year);
			$this->Ln();
			
			$this->SetFont('Courier','B','9'); 
			$this->Cell(80,6,'MONTH',0);
			$this->Cell(40,6,'GROSS INCOME',0,0,'R');
			$this->Cell(40,6,'W/ TAX',0,0,'R');
			$this->Cell(40,6,'PREV YR WTAX ADJ',0,0,'R');
			$this->Cell(40,6,'ECOLA',0,0,'R');
			$this->Cell(40,6,'13TH MONTH NON TAX',0,0,'R');
			$this->Cell(40,6,'13TH MONTH TAX',0,1,'R');
		}
		function Data($gross,$wtax,$wyetax,$ecola,$n13thNT,$n13thT) {
			$this->AddPage();
			$this->SetFont('Courier','','9'); 
			$totGross 	= 0;
			$totTax 	= 0;
			$totYETax 	= 0;
			$totEcola 	= 0;
			$tot13thNT 	= 0;
			$tot13thT 	= 0;
			for($i=1; $i<13;$i++) {
				$this->Cell(80,6,$this->getMonth($i),0,0);
				$this->Cell(40,6,number_format($gross[$i],2),0,0,'R');
				$this->Cell(40,6,number_format($wtax[$i],2),0,0,'R');
				$this->Cell(40,6,number_format($wyetax[$i],2),0,0,'R');
				$this->Cell(40,6,number_format($ecola[$i],2),0,0,'R');
				$this->Cell(40,6,number_format($n13thNT[$i],2),0,0,'R');
				$this->Cell(40,6,number_format($n13thT[$i],2),0,1,'R');
				$totGross 	+= round($gross[$i],2);
				$totTax 	+= round($wtax[$i],2);
				$totYETax 	+= round($wyetax[$i],2);
				$totEcola 	+= round($ecola[$i],2);
				$tot13thNT 	+= round($n13thNT[$i],2);
				$tot13thT 	+= round($n13thT[$i],2);

			}
			$this->SetFont('Courier','B','9'); 
			$this->Cell(80,6,'GRAND TOTAL',0,0);
			$this->Cell(40,6,number_format($totGross,2),0,0,'R');
			$this->Cell(40,6,number_format($totTax,2),0,0,'R');
			$this->Cell(40,6,number_format($totYETax,2),0,0,'R');
			$this->Cell(40,6,number_format($totEcola,2),0,0,'R');
			$this->Cell(40,6,number_format($tot13thNT,2),0,0,'R');
			$this->Cell(40,6,number_format($tot13thT,2),0,1,'R');			
			
		}
		


		function getMonth($Month) {
			switch($Month) {
				case 1:
					$pdMonth = "January";
				break;
				case 2:
					$pdMonth = "February";
				break;
				case 3:
					$pdMonth = "March";
				break;
				case 4:
					$pdMonth = "April";
				break;
				case 5:
					$pdMonth = "May";
				break;
				case 6:
					$pdMonth = "June";
				break;
				case 7:
					$pdMonth = "July";
				break;
				case 8:
					$pdMonth = "August";
				break;
				case 9:
					$pdMonth = "September";
				break;
				case 10:
					$pdMonth = "October";
				break;
				case 11:
					$pdMonth = "November";
				break;
				case 12:
					$pdMonth = "December";
				break;
			}
			return $pdMonth;		
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
	$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
	$arrYTD = $inqTSObj->getYTDYearly($Year);
	$pdf->AliasNbPages();
	$pdf->company = $inqTSObj->getCompanyName($_SESSION['company_code']);
	$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
	$pdf->rundate=$inqTSObj->currentDateArt();
		foreach($arrYTD as $valYTD){
			switch($valYTD['pdNumber']) {
				case 1:
					$Gross[1] 		+= $valYTD['grossearnings'];
					$tax[1] 		+= $valYTD['tax'];
					$yetax[1] 		+= $valYTD['YearEnd'];
					$ecola[1]		+= $valYTD['ecola'];
					$N13thNontax[1] += $valYTD['N13thNontax'];
					$N13thTax[1] 	+= $valYTD['N13thTax'];
				break;
				case 2:
					$Gross[1] 		+= $valYTD['grossearnings'];
					$tax[1] 		+= $valYTD['tax'];
					$yetax[1] 		+= $valYTD['YearEnd'];
					$ecola[1]		+= $valYTD['ecola'];
					$N13thNontax[1] += $valYTD['N13thNontax'];
					$N13thTax[1] 	+= $valYTD['N13thTax'];
				break;				
				case 3:
					$Gross[2] 		+= $valYTD['grossearnings'];
					$tax[2] 		+= $valYTD['tax'];
					$yetax[2] 		+= $valYTD['YearEnd'];
					$ecola[2]		+= $valYTD['ecola'];
					$N13thNontax[2] += $valYTD['N13thNontax'];
					$N13thTax[2] 	+= $valYTD['N13thTax'];
				break;
				case 4:
					$Gross[2] 		+= $valYTD['grossearnings'];
					$tax[2] 		+= $valYTD['tax'];
					$yetax[2] 		+= $valYTD['YearEnd'];
					$ecola[2]		+= $valYTD['ecola'];
					$N13thNontax[2] += $valYTD['N13thNontax'];
					$N13thTax[2] 	+= $valYTD['N13thTax'];
				break;
				case 5:
					$Gross[3] 		+= $valYTD['grossearnings'];
					$tax[3] 		+= $valYTD['tax'];
					$yetax[3] 		+= $valYTD['YearEnd'];
					$ecola[3]		+= $valYTD['ecola'];
					$N13thNontax[3] += $valYTD['N13thNontax'];
					$N13thTax[3] 	+= $valYTD['N13thTax'];
				break;
				case 6:
					$Gross[3] 		+= $valYTD['grossearnings'];
					$tax[3] 		+= $valYTD['tax'];
					$yetax[3] 		+= $valYTD['YearEnd'];
					$ecola[3]		+= $valYTD['ecola'];
					$N13thNontax[3] += $valYTD['N13thNontax'];
					$N13thTax[3] 	+= $valYTD['N13thTax'];
				break;
				case 7:
					$Gross[4] 		+= $valYTD['grossearnings'];
					$tax[4] 		+= $valYTD['tax'];
					$yetax[4] 		+= $valYTD['YearEnd'];
					$ecola[4]		+= $valYTD['ecola'];
					$N13thNontax[4] += $valYTD['N13thNontax'];
					$N13thTax[4] 	+= $valYTD['N13thTax'];
				break;
				case 8:
					$Gross[4] 		+= $valYTD['grossearnings'];
					$tax[4] 		+= $valYTD['tax'];
					$yetax[4] 		+= $valYTD['YearEnd'];
					$ecola[4]		+= $valYTD['ecola'];
					$N13thNontax[4] += $valYTD['N13thNontax'];
					$N13thTax[4] 	+= $valYTD['N13thTax'];
				break;
				case 9:
					$Gross[5] 		+= $valYTD['grossearnings'];
					$tax[5] 		+= $valYTD['tax'];
					$yetax[5] 		+= $valYTD['YearEnd'];
					$ecola[5]		+= $valYTD['ecola'];
					$N13thNontax[5] += $valYTD['N13thNontax'];
					$N13thTax[5] 	+= $valYTD['N13thTax'];
				break;
				case 10:
					$Gross[5] 		+= $valYTD['grossearnings'];
					$tax[5] 		+= $valYTD['tax'];
					$yetax[5] 		+= $valYTD['YearEnd'];
					$ecola[5]		+= $valYTD['ecola'];
					$N13thNontax[5] += $valYTD['N13thNontax'];
					$N13thTax[5] 	+= $valYTD['N13thTax'];
				break;
				case 11:
					$Gross[6] 		+= $valYTD['grossearnings'];
					$tax[6] 		+= $valYTD['tax'];
					$yetax[6] 		+= $valYTD['YearEnd'];
					$ecola[6]		+= $valYTD['ecola'];
					$N13thNontax[6] += $valYTD['N13thNontax'];
					$N13thTax[6] 	+= $valYTD['N13thTax'];
				break;
				case 12:
					$Gross[6] 		+= $valYTD['grossearnings'];
					$tax[6] 		+= $valYTD['tax'];
					$yetax[6] 		+= $valYTD['YearEnd'];
					$ecola[6]		+= $valYTD['ecola'];
					$N13thNontax[6] += $valYTD['N13thNontax'];
					$N13thTax[6] 	+= $valYTD['N13thTax'];
				break;	
				case 13:
					$Gross[7] 		+= $valYTD['grossearnings'];
					$tax[7] 		+= $valYTD['tax'];
					$yetax[7] 		+= $valYTD['YearEnd'];
					$ecola[7]		+= $valYTD['ecola'];
					$N13thNontax[7] += $valYTD['N13thNontax'];
					$N13thTax[7] 	+= $valYTD['N13thTax'];
				break;
				case 14:
					$Gross[7] 		+= $valYTD['grossearnings'];
					$tax[7] 		+= $valYTD['tax'];
					$yetax[7] 		+= $valYTD['YearEnd'];
					$ecola[7]		+= $valYTD['ecola'];
					$N13thNontax[7] += $valYTD['N13thNontax'];
					$N13thTax[7] 	+= $valYTD['N13thTax'];
				break;
				case 15:
					$Gross[8] 		+= $valYTD['grossearnings'];
					$tax[8] 		+= $valYTD['tax'];
					$yetax[8] 		+= $valYTD['YearEnd'];
					$ecola[8]		+= $valYTD['ecola'];
					$N13thNontax[8] += $valYTD['N13thNontax'];
					$N13thTax[8] 	+= $valYTD['N13thTax'];
				break;
				case 16:
					$Gross[8] 		+= $valYTD['grossearnings'];
					$tax[8] 		+= $valYTD['tax'];
					$yetax[8] 		+= $valYTD['YearEnd'];
					$ecola[8]		+= $valYTD['ecola'];
					$N13thNontax[8] += $valYTD['N13thNontax'];
					$N13thTax[8] 	+= $valYTD['N13thTax'];
				break;
				case 17:
					$Gross[9] 		+= $valYTD['grossearnings'];
					$tax[9] 		+= $valYTD['tax'];
					$yetax[9] 		+= $valYTD['YearEnd'];
					$ecola[9]		+= $valYTD['ecola'];
					$N13thNontax[9] += $valYTD['N13thNontax'];
					$N13thTax[9] 	+= $valYTD['N13thTax'];
				break;
				case 18:
					$Gross[9] 		+= $valYTD['grossearnings'];
					$tax[9] 		+= $valYTD['tax'];
					$yetax[9] 		+= $valYTD['YearEnd'];
					$ecola[9]		+= $valYTD['ecola'];
					$N13thNontax[9] += $valYTD['N13thNontax'];
					$N13thTax[9] 	+= $valYTD['N13thTax'];
				break;
				case 19:
					$Gross[10] 			+= $valYTD['grossearnings'];
					$tax[10] 			+= $valYTD['tax'];
					$yetax[10] 			+= $valYTD['YearEnd'];
					$ecola[10]			+= $valYTD['ecola'];
					$N13thNontax[10]	+= $valYTD['N13thNontax'];
					$N13thTax[10] 		+= $valYTD['N13thTax'];
				break;
				case 20:
					$Gross[10] 			+= $valYTD['grossearnings'];
					$tax[10] 			+= $valYTD['tax'];
					$yetax[10] 			+= $valYTD['YearEnd'];
					$ecola[10]			+= $valYTD['ecola'];
					$N13thNontax[10]	+= $valYTD['N13thNontax'];
					$N13thTax[10] 		+= $valYTD['N13thTax'];
				break;	
				case 21:
					$Gross[11] 			+= $valYTD['grossearnings'];
					$tax[11] 			+= $valYTD['tax'];
					$yetax[11] 			+= $valYTD['YearEnd'];
					$ecola[11]			+= $valYTD['ecola'];
					$N13thNontax[11] 	+= $valYTD['N13thNontax'];
					$N13thTax[11] 		+= $valYTD['N13thTax'];
				break;
				case 22:
					$Gross[11] 			+= $valYTD['grossearnings'];
					$tax[11] 			+= $valYTD['tax'];
					$yetax[11] 			+= $valYTD['YearEnd'];
					$ecola[11]			+= $valYTD['ecola'];
					$N13thNontax[11] 	+= $valYTD['N13thNontax'];
					$N13thTax[11] 		+= $valYTD['N13thTax'];
				break;	
				case 23:
					$Gross[12] 			+= $valYTD['grossearnings'];
					$tax[12] 			+= $valYTD['tax'];
					$yetax[12] 			+= $valYTD['YearEnd'];
					$ecola[12]			+= $valYTD['ecola'];
					$N13thNontax[12] 	+= $valYTD['N13thNontax'];
					$N13thTax[12] 		+= $valYTD['N13thTax'];
				break;
				case 24:
					$Gross[12] 			+= $valYTD['grossearnings'];
					$tax[12] 			+= $valYTD['tax'];
					$yetax[12] 			+= $valYTD['YearEnd'];
					$ecola[12]			+= $valYTD['ecola'];
					$N13thNontax[12] 	+= $valYTD['N13thNontax'];
					$N13thTax[12] 		+= $valYTD['N13thTax'];
				break;										
			}
		}
		$pdf->Data($Gross,$tax,$yetax,$ecola,$N13thNontax,$N13thTax);
	
	$pdf->Output();
?>