<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	//include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	define('PARAGRAPH_STRING', '~~~'); 
	require_once("../../../includes/pdf/MultiCellTag/class.multicelltag.php"); 
	
################ GET TOTAL RECORDS ###############

############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
class PDF extends fpdf_multicelltag
{
	var $EmpInfo;
	var $empname;
	var $compName;
	var $signatory;
	var $title;
	var $branch;
	var $nos;
	var $department;
	var $series;
	function Content() {
		
		$this->SetStyle("p","times","",11,"130,0,30");
		$this->SetStyle("pb","times","B",11,"130,0,30");
		$this->SetStyle("t1","arial","B",14,0);
		$this->SetStyle("t3","times","B",14,"203,0,48");
		$this->SetStyle("t4","arial","BI",11,"0,151,200");
		$this->SetStyle("hh","times","B",11,"255,189,12");
		$this->SetStyle("ss","arial","",7,"203,0,48");
		$this->SetStyle("font","helvetica","",10,"0,0,255");
		$this->SetStyle("style","helvetica","BI",10,"0,0,220");
		$this->SetStyle("size","times","BI",13,"0,0,120");
		$this->SetStyle("color","times","BI",13,"0,255,255");
		$this->Image('../../../images/clearance.jpg',1,0,214,280);
		$this->Ln(6);
		if($_SESSION['company_code']==1){
			$this->Image('../../../images/ow.jpg',165,15,43,12);	
		}
		if($_SESSION['company_code']==2){
			$this->Image('../../../images/ppci_logo.jpg',165,15,45,12);	
		}
		if($_SESSION['company_code']==4 || $_SESSION['company_code']==5){
			$this->Image('../../../images/duty_free_logo.jpg',165,22,45,12);	
		}
		if($_SESSION['company_code']==7 || $_SESSION['company_code']==8 || $_SESSION['company_code']==9 || $_SESSION['company_code']==10 || $_SESSION['company_code']==11 || $_SESSION['company_code']==12 || $_SESSION['company_code']==13){
			$this->Image('../../../images/parco_logo.jpg',165,15,45,12);	
		}
		$this->empname = str_replace("&Ntilde;","Ñ",$this->EmpInfo['empLastName'] . ", " . $this->EmpInfo['empFirstName'] . " " . $this->EmpInfo['empMidName']);
		
		$this->SetFont('Courier', '', '10');
		$this->Ln(23);
		$this->Cell(25,5,"",0,0,'');
		$this->Cell(71,5,$this->series,0,0,'');
		$this->Cell(75,5,$this->branch,0,0,"L");
		$this->Cell(20,5,date("m/d/Y"),0,0,"L");
		$this->Ln(18);
		$this->Cell(78,5,$this->empname,0,0,"L");	
		$this->Ln(11);
		$this->Cell(65,5,$this->EmpInfo['posShortDesc'],0,0,"L");	
		$this->Cell(54,5,date("M d, Y", strtotime($this->EmpInfo['dateHired'])),0,0,"L");
		if($this->EmpInfo['dateResigned']!="" && $this->EmpInfo['empStat']=="RS"){
			$this->Cell(45,5,date("M d, Y", strtotime($this->EmpInfo['dateResigned'])),0,1,"L");	
		}
		elseif($this->EmpInfo['endDate']!="" && $this->EmpInfo['empStat']=="RS"){
			$this->Cell(45,5,date("M d, Y", strtotime($this->EmpInfo['endDate'])),0,1,"L");	
		}
		else{
			$this->Cell(45,5,"",0,1,"L");		
		}
		
		if($this->nos['natureCode']=="1"){
			$this->Image('../../../images/marker.jpg',165,69,4,4);	
		}
		if($this->nos['natureCode']=="2"){
			$this->Image('../../../images/marker.jpg',165,65,4,4);	
		}
		if($this->nos['natureCode']=="3"){
			$this->Image('../../../images/marker.jpg',165,57,4,4);	
		}
		if($this->nos['natureCode']=="5"){
			$this->Image('../../../images/marker.jpg',165,61,4,4);	
		}
	}
}	
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$pdf=new PDF();
$pdf->Open();
$pdf->FPDF($orientation='P',$unit='mm',$format='LETTER');	
$pdf->EmpInfo = $inqTSObj->empCOEInfos($_GET['empNo'],$_SESSION['company_code']);
$pdf->compName = $inqTSObj->getCompany($_SESSION['company_code']);
$pdf->branch = $inqTSObj->getBranchName($_SESSION['company_code'],$pdf->EmpInfo['empBrnCode']);
$pdf->nos = $inqTSObj->getSeparatedEmployees($_GET['empNo']);
$pdf->department = $inqTSObj->getDepartment($_SESSION['company_code'],$pdf->EmpInfo['empDiv'],$pdf->EmpInfo['empDepCode'],"","2");
$pdf->series = $inqTSObj->reportSeries("clearance");
$pdf->AddPage();	
$pdf->Content();
$pdf->SetMargins(20,0,10);
$pdf->Output('CLEARANCE.pdf','D');
?>
