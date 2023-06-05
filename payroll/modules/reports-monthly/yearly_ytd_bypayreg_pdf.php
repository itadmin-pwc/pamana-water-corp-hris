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
			$this->Cell(100,5,"Report ID: YEARLYPAYREGYTD");
			$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
			$this->Cell(184,5,'YTD by Pay Register for the Year '.$Year);
			$this->Ln();
			
			$this->SetFont('Courier','B','9'); 
			$this->Cell(80,6,'',0);
			$this->Cell(40,6,'GROSS INCOME',0,0,'R');
			$this->Cell(40,6,'W/ TAX',0,0,'R');
			$this->Cell(40,6,'PREV YR WTAX ADJ',0,0,'R');
			$this->Cell(40,6,'ECOLA',0,0,'R');
			$this->Cell(40,6,'13TH MONTH NON TAX',0,0,'R');
			$this->Cell(40,6,'13TH MONTH TAX',0,1,'R');
		}
		function Data($payReg,$gross,$wtax,$yetax,$ecola,$n13thNT,$n13thT) {
			
			$this->SetFont('Courier','','9'); 
			$this->Cell(20,6,'',0,0);
			$this->Cell(60,6,$payReg,0,0);
			$this->Cell(40,6,number_format($gross,2),0,0,'R');
			$this->Cell(40,6,number_format($wtax,2),0,0,'R');
			$this->Cell(40,6,number_format($yetax,2),0,0,'R');
			$this->Cell(40,6,number_format($ecola,2),0,0,'R');
			$this->Cell(40,6,number_format($n13thNT,2),0,0,'R');
			$this->Cell(40,6,number_format($n13thT,2),0,1,'R');			
			
		}
		function TotalData($label,$gross,$wtax,$yetax,$ecola,$n13thNT,$n13thT) {
			
			$this->SetFont('Courier','B','9'); 
			$this->Cell(20,6,'',0,0);
			$this->Cell(60,6,$label,0,0);
			$this->Cell(40,6,number_format($gross,2),0,0,'R');
			$this->Cell(40,6,number_format($wtax,2),0,0,'R');
			$this->Cell(40,6,number_format($yetax,2),0,0,'R');
			$this->Cell(40,6,number_format($ecola,2),0,0,'R');
			$this->Cell(40,6,number_format($n13thNT,2),0,0,'R');
			$this->Cell(40,6,number_format($n13thT,2),0,1,'R');			
			
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
	$pdf->AliasNbPages();
	$pdf->company = $inqTSObj->getCompanyName($_SESSION['company_code']);
	$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
	$pdf->rundate=$inqTSObj->currentDateArt();
	$pdf->AddPage();
		$GtotGross 		= 0;
		$GtotTax 		= 0;
		$GtotYETax 		= 0;
		$GtotEcola 		= 0;
		$Gtot13thNT 	= 0;
		$Gtot13thT 		= 0;	
		for($q=1;$q<13;$q++) {
			$totGross 	= 0;
			$totTax 	= 0;
			$totYETax 	= 0;
			$totEcola 	= 0;
			$tot13thNT 	= 0;
			$tot13thT 	= 0;
			switch($q) {
				case 1:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'1,2');
					if (count($arrYTD)!=0) {
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;					
					}	
				break;
				case 2:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'3,4');
					if (count($arrYTD)!=0) {
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
												foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;		
					}	
				break;				
				case 3:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'5,6');
					if (count($arrYTD)!=0) {
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
												foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;								
					}	
				break;
				case 4:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'7,8');
					if (count($arrYTD)!=0) {
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;								
					}	

				break;
				case 5:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'9,10');
					if (count($arrYTD)!=0) {
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;							
					}
				break;
				case 6:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'11,12');
					if (count($arrYTD)!=0) {
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;								
					}	

				break;
				case 7:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'13,14');
					if (count($arrYTD)!=0) {
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;							
					}

				break;
				case 8:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'15,16');
					if (count($arrYTD)!=0) {
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;								
					}
				break;
				case 9:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'17,18');
					if (count($arrYTD)!=0) {					
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;								
					}
				break;
				case 10:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'19,20');
					if (count($arrYTD)!=0) {					
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;								
					}
				break;
				case 11:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'21,22');
					if (count($arrYTD)!=0) {					
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;								
					}	

				break;
				case 12:
					$arrYTD=$inqTSObj->getYTDYearlybyPayreg($Year,'23,24');
					if (count($arrYTD)!=0) {
						$pdf->Cell(20,6,$pdf->getMonth($q),0,1);
						foreach($arrYTD as $valYTD){
							$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
							$pdf->Data($payReg,$valYTD['grossearnings'],$valYTD['tax'],$valYTD['YearEnd'],$valYTD['ecola'],$valYTD['N13thNontax'],$valYTD['N13thTax']);
							$totGross 	+= round($valYTD['grossearnings'],2);
							$totTax 	+= round($valYTD['tax'],2);
							$totYETax 	+= round($valYTD['YearEnd'],2);
							$totEcola 	+= round($valYTD['ecola'],2);
							$tot13thNT 	+= round($valYTD['N13thNontax'],2);
							$tot13thT 	+= round($valYTD['N13thTax'],2);
						}
						$pdf->TotalData('Month Total',$totGross ,$totTax,$totYETax,$totEcola,$tot13thNT,$tot13thT);
						$GtotGross 		+= $totGross;
						$GtotTax 		+= $totTax;
						$GtotYETax 		+= $totYETax;
						$GtotEcola 		+= $totEcola;
						$Gtot13thNT 	+= $tot13thNT;
						$Gtot13thT 		+= $tot13thT;								
					}
				break;	
			}
		}
		if ($GtotGross !=0) {
			$pdf->Ln();
			$pdf->TotalData('Grand Total',$GtotGross ,$GtotTax,$GtotYETax,$GtotEcola,$Gtot13thNT,$Gtot13thT);	
		}	
	
	$pdf->Output();
?>