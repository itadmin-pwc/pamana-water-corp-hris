<?php
session_start();
ini_set("max_execution_time","0");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pdf/fpdf.php");
include("ts_obj.php");
class PDF extends FPDF
{
	
	function Header() {
		$this->SetFont('Courier','',8);
		$this->Cell(70,5,'Run Date: '.$this->currentDateArt(),0);
		$this->Cell(90,5,$this->getCompanyName($_SESSION['company_code']),0);
		$this->Cell(30,5,'Page '.$this->PageNo().'/{nb}',0,1);
		$this->Cell(70,5,'Report ID: EVENTLOGS',0);
		$this->Cell(90,5,'EVENT LOGS',0,1);
		$this->Cell(195,7,'  DATE          LOGS                                                                       FLOOR         DOOR  ','TB',1);
		
	}
	
	
	function Footer() {
		$this->SetFont('Courier','',8);
	    $this->SetY(-15);
		$dispUser = $this->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$user = $dispUser["empFirstName"]." ".$dispUser["empLastName"];
		$this->Cell(195,7,"Printed By: $user                                                    Approved by: " ,'T',0);
	}
	
	function Main($arrEventLogs = array(),$arrEmpList = array()) {
		$this->SetFont('Courier','',8);
		$this->AddPage('P');
		$branch = $empName = "";
		foreach ($arrEmpList as $val){
			if ($branch == '') {
				$this->SetFont('Courier', 'B', '8');
				$this->Cell(20,5,$val['brnDesc'],0,1,'L');
				$this->SetFont('Courier', '', '8');
				$branch = $val['brnDesc'];
			}
			if ($val['empName'] != $empName) {
				if ($empName !='') {
					$this->Ln();
				}
				$this->Cell(50,5,$val['empName']." " .$val['empNo'],0,1,'L');
				$empName = $val['empName'];
			}
			$this->Cell(2,5,'',0,0,'L');
			$this->Cell(24,5,date('m/d/Y',strtotime($val['EDATE'])),0,0);
			$this->Cell(132,5,str_replace(',',', ',implode(',',$this->getLogs($arrEventLogs,$val['empNo'],$val['EDATE']))),0,0);
			$this->Cell(15,5,$val['EFLOOR'],0,0,'L');
			$this->Cell(25,5,$val['EDOOR'],0,0,'L');
			$this->Cell(1,5,"",0,1,'L');
		}	
	}

	function getLogs($array,$empNo,$tsDate) {
		$Logs = array();
		foreach($array as $val) {
			if($val['empNo']==$empNo && $val['EDATE']==$tsDate) {
				$Logs[] = date('H:i:s',strtotime($val['ETIME']));
			}
		}
		return $Logs;
	}
	
}
$inqTSObj = new inqTSObj();
$arrEventLogs = $inqTSObj->evenReport($_GET['empNo'],$_GET['from'],$_GET['to'],$_GET['branch'],$_GET['bio']);
$arrEmpList  = $inqTSObj->evenReportGrp($_GET['empNo'],$_GET['from'],$_GET['to'],$_GET['branch'],$_GET['bio']);
$pdf=new PDF();
$pdf->FPDF('P', 'mm', 'LETTER');
$pdf->AliasNbPages(); 
$pdf->Main($arrEventLogs,$arrEmpList);
//$pdf->Output('event_report.pdf','D');
$pdf->Output();
?>
