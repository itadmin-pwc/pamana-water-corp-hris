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
			$this->Cell(100,5,"Report ID: PAYSUM");
			$date = $_GET['payPdfr'].'-'.$_GET['payPdto'];
			switch($_SESSION['pay_category']) {
				case 1:
					$payCat = "Executive";	
				break;
				case 2:
					$payCat = "Confidential";	
				break;
				case 3:
					$payCat = "Non Confidential";	
				break;
				case 9:
					$payCat = "Resigned";	
				break;
				
			}
			$this->Cell(184,5,"Payroll Summary - $payCat ($date)".$Year);
			$this->Ln();
			
			$this->SetFont('Courier','B','9'); 
			$this->Cell(30,6,'STORE',0);
			$this->Cell(25,6,'PAYROLL PERIOD',0,0,'R');
			$this->Cell(20,6,'HEADCOUNT',0,0,'R');
			$this->Cell(30,6,'GROSS SALARY',0,0,'R');
			$this->Cell(30,6,'ALLOWANCE',0,0,'R');
			$this->Cell(30,6,'OT',0,0,'C');
			$this->Cell(20,6,'OT%/BASIC',0,0,'R');
			$this->Cell(30,6,'TOTAL',0,0,'R');
			$this->Cell(30,6,'PFI',0,0,'R');
			$this->Cell(30,6,'SSS',0,0,'R');
			$this->Cell(30,6,'HDMF',0,0,'R');
			$this->Cell(30,6,'CSI',0,1,'R');
		}
		function Data($arr) {
			$this->AddPage();
			$this->SetFont('Courier','','9'); 
			$branch = "";
			$totBasic = $totAllow = $totOt = $totpfi = $totsss = $tothdmf = $totcsi = $totEarnings = 0;
			foreach($arr as $val) {
				if ($branch != $val['branch']) {
					$this->Cell(30,6,$val['branch'],0);
				} else {
					$this->Cell(30,6,"",0);
				}
				$branch = $val['branch'];
				$subTot 	= $val['empBasic'] + $val['allowAmt'] + $val['otAmt'];
				$totEarnings+= $subTot;
				$totBasic 	+= $val['empBasic'];
				$totAllow 	+= $val['allowAmt'];
				$totOt 		+= $val['otAmt'];
				$totpfi 	+= $val['pfiAmt'];
				$totsss 	+= $val['sssAmt'];
				$tothdmf 	+= $val['hdmfAmt'];
				$totcsi 	+= $val['csiAmt'];
				$otBasic 	= number_format(($val['otAmt']/$val['empBasic']) * 100,2);
				
				$this->Cell(25,6,date('m/d/Y',strtotime($val['pdPayable'])),0,0,'R');
				$this->Cell(20,6,$val['headCnt'],0,0,'R');
				$this->Cell(30,6,number_format($val['empBasic'],2),0,0,'R');
				$this->Cell(30,6,number_format($val['allowAmt'],2),0,0,'R');
				$this->Cell(30,6,number_format($val['otAmt'],2),0,0,'R');
				$this->Cell(20,6,$otBasic,0,0,'R');
				$this->Cell(30,6,number_format($subTot,2),0,0,'R');
				$this->Cell(30,6,number_format($val['pfiAmt'],2),0,0,'R');
				$this->Cell(30,6,number_format($val['sssAmt'],2),0,0,'R');
				$this->Cell(30,6,number_format($val['hdmfAmt'],2),0,0,'R');
				$this->Cell(30,6,number_format($val['csiAmt'],2),0,1,'R');		
			}
			
				$this->SetFont('Courier','B','9'); 
				$totOtBasic = number_format(($totOt/$totBasic) * 100,2);
				$this->Cell(30,6,"",0);
				$this->Cell(25,6,"TOTAL",0,0,'R');
				$this->Cell(20,6,"",0,0,'R');
				$this->Cell(30,6,number_format($totBasic,2),0,0,'R');
				$this->Cell(30,6,number_format($totAllow,2),0,0,'R');
				$this->Cell(30,6,number_format($totOt,2),0,0,'R');
				$this->Cell(20,6,$totOtBasic,0,0,'R');
				$this->Cell(30,6,number_format($totEarnings,2),0,0,'R');
				$this->Cell(30,6,number_format($totpfi,2),0,0,'R');
				$this->Cell(30,6,number_format($totsss,2),0,0,'R');
				$this->Cell(30,6,number_format($tothdmf,2),0,0,'R');
				$this->Cell(30,6,number_format($totcsi,2),0,1,'R');			
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
	$arrPaySumm = $inqTSObj->getpaySummary($_GET['brnch'],$_GET['payPdfr'],$_GET['payPdto']);
	$pdf->AliasNbPages();
	$pdf->company = $inqTSObj->getCompanyName($_SESSION['company_code']);
	$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
	$pdf->rundate=$inqTSObj->currentDateArt();
	$pdf->Data($arrPaySumm);
	
	$pdf->Output();
?>