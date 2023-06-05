<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pdf/fpdf.php");
define('FPDF_FONTPATH','../../../includes/pdf/font/');
class PDF extends FPDF
{
	var $printedby;
	var $company;
	
	function Header(){
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	$this->SetFont('Courier', '', '10');
	$this->Text(11,10,"RUN DATE: ".$newdate);
	$this->Text(11,14,"REPORT ID: LSTLH");
	$this->Text(120,10,"");
	$this->Text(120,10, "COMPANY: ".$this->company);
	$this->Text(120,14,"LIST OF LEGAL HOLIDAYS");
	$this->Ln(10);
	$this->SetFont('Courier', 'B', '10');
	$this->Cell(260,8,"BRANCH                                  HOLIDAY NAME                                      DAY          HOLIDAY TYPE",'TB',1);
	}
	
	function Data($branch,$holidayname,$date,$holidaytype){
		$this->SetFont('Courier','','9');
		$this->Cell(85,5,$branch,0);
		$this->Cell(100,5,$holidayname,0);		
		$this->Cell(33,5,$date,0);
		$this->Cell(50,5,$holidaytype,0);		
		$this->Ln();
	}
	
	function Footer(){
		$this->SetY(-20);
		$this->Cell(260,1,'','T');
		$this->ln();
		$this->SetFont('Courier', 'B', '10');
		$this->Cell(260,6,"Printed by: ".$this->printedby['empFirstName'].", ".$this->printedby['empLastName'],0,'L',0);
	}
	
}
$pdf=new PDF('L', 'mm', 'LETTER');
$comObj=new commonObj();
$pdf->AliasNbPages();
$pdf->reportlabel = 'LEGAL HOLIDAY';
$pdf->company = $comObj->getCompanyName($_SESSION['company_code']);
$pdf->printedby = $comObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']); 
if($_GET['years']!=""){
	$dates=" and datepart(year,tblHolidayCalendar.holidayDate)='{$_GET['years']}'";	
}
else{
	$dates="";	
}
####Set up for next page
$pdf->AddPage();

$sql="SELECT tblHolidayCalendar.compCode, tblHolidayCalendar.holidayDate, tblHolidayCalendar.brnCode, tblHolidayCalendar.holidayDesc, tblHolidayCalendar.dayType, tblHolidayCalendar.holidayStat, tblBranch.brnDesc, tblBranch.brnShortDesc, tblDayType.dayTypeDesc
FROM tblHolidayCalendar LEFT OUTER JOIN tblDayType ON tblHolidayCalendar.dayType = tblDayType.dayType LEFT OUTER JOIN tblBranch ON tblHolidayCalendar.brnCode = tblBranch.brnCode where tblHolidayCalendar.compCode='{$_SESSION['company_code']}' $dates order by tblHolidayCalendar.holidayDate DESC";	
$res=$comObj->execQry($sql);
$resqry=$comObj->getArrRes($res);
foreach($resqry as $resval=>$values){
	if($values['brnCode']==0){
		$branches="All Branches";		
	}
	else{
		$branches=$values['brnDesc'];
	}
	$holidaynames=$values['holidayDesc'];
	$datess=$comObj->valDateArt($values['holidayDate']);
	$holidaytypes=$values['dayTypeDesc'];
		$pdf->Data($branches,$holidaynames,$datess,$holidaytypes);		
}	

$pdf->Output('LEGAL_HOLIDAY_REPORT.PDF','D');
?>