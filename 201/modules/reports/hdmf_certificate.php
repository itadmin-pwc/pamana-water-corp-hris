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
	var $EmpOtherInfo;
	var $compName;
	var $signatory;
	var $title;
	var $empstatus;
	var $leavestats;
	var $leavefrom;
	var $leaveto;
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
		
		$EmpInfo = $this->EmpInfo;
		$EmpOtherInfo = $this->EmpOtherInfo;
		$empstatus = $this->empstatus;
		$signatory = $this->signatory;
		$title = $this->title;
		$leavestats = $this->leavestats;
		$leavefrom = $this->leavefrom;
		$leaveto = $this->leaveto;
		//$this->Image('../../../images/pg.jpg',67,10,5,7);
		if ($EmpInfo['empSex']=="Male") {
			$empName = "Mr. " .$EmpOtherInfo['empFirstName'] . " " . $EmpOtherInfo['empMidName'][0] . ". " .$EmpOtherInfo['empLastName'];
			$empLName = "Mr. " .$EmpOtherInfo['empLastName'];
			$gender ="his";
			$gender2="He";
		} else {
			$gender ="her";
			$gender2="She";
				$empName = "Ms. " .$EmpOtherInfo['empFirstName'] . " " . $EmpOtherInfo['empMidName'][0] . ". " .$EmpOtherInfo['empLastName'];
				$empLName = "Ms. " .$EmpOtherInfo['empLastName'];
		}
		$compName = $this->compName['compName'];
		$empPos = $EmpInfo['posShortDesc'];
		$dateHired = date("F d, Y",strtotime($EmpOtherInfo['dateHired']));
		$dateResigned = date("F d, Y",strtotime('-1 day', strtotime($EmpOtherInfo['dateResigned'])));
		$this->SetFont('Courier', 'B', '14');
		$this->Cell(175,4,'',0,0,"C");
		$this->Ln();
		$this->SetFont('Courier', '', '11');
		$this->Cell(175,4,'',0,0,"C");
		$this->Ln(40);
		//$this->SetFont('Times', 'B', '18');
		$this->SetFont('Times', 'BI', '18');
		$this->Cell(200,8,"C E R T I F I C A T I O N",0,0,"C");
		$this->Ln(30);
		$this->SetFont('Arial', '', '14');
		$this->SetMargins(23,0,10);
		$this->MultiCell(170,0,"",0,"J",0);
		$this->MultiCellTag(170,10,"		This is to certify that <t1>$empName</t1> is an employee of <t1>$compName</t1> since <t1>$dateHired</t1> and presently holding a <t1>$empstatus</t1> appointment with a position of <t1>$empPos</t1>.",0,"J",0,true);
		$this->Ln(10);		
		$this->MultiCellTag(170,10,"		Further, this is to certify that <t1>$empLName</t1> is on <t1>$leavestats</t1> from <t1>".date("F d, Y",strtotime($leavefrom))."</t1> to <t1>".date("F d, Y",strtotime($leaveto))."</t1>.",0,"J",0,true);
		$this->Ln(10);
		$this->MultiCellTag(170,10,"		This certification is issued this <t1>".date ('dS \d\a\y \o\f F, Y')."</t1> upon the request of <t1>$empLName</t1> in connection with $gender application for Pag-IBIG Multi-Purpose Loan.",0,"J",0,true);
		$this->Ln(40);
		$this->Cell(115,4,"",0,0);
		$this->SetFont('Times', 'B', '14');
		$this->Cell(25,4,"GIRLIE P. MENDOZA",0,0,'C');
		$this->SetFont('Times', '', '14');
		$this->Ln(6);
		$this->Cell(115,4,"",0,0);
		$this->Cell(25,4,"HR MANAGER",0,0,'C');
	}
}	
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$type = $_GET['type'];
$pdf=new PDF();
$pdf->Open();
$pdf->FPDF($orientation='P',$unit='mm',$format='LETTER');	
$pdf->compName = $inqTSObj->getCompany($_SESSION['company_code']);
$pdf->EmpInfo = $inqTSObj->empOtherInfos($_GET['empno']);
$pdf->EmpOtherInfo = $inqTSObj->getEmpCOEInfo($_GET['empno']);
$arrBranhInfo = $inqTSObj->getBrnchInfo($pdf->EmpOtherInfo['empBrnCode']);
$pdf->signatory=$arrBranhInfo['coeSignatory'];
$pdf->title=$arrBranhInfo['coeSignatoryTitle'];
$pdf->empstatus=$inqTSObj->EmpStat($pdf->EmpInfo['empStat']);


if($type==1){
//	$leavetype = "('04','16')";	
//	$leavetype = $inqTSObj->getLeaveApp("PG_TNA_TEST..tblTK_LeaveAppHist as lvapp","PG_TNA_TEST..tblTK_AppTypes as lvtype",$_GET['empno'],$leavetype);
	$pdf->leavestats = "Sick Leave"; //substr($leavetype['appTypeDesc'],0,10);
}
else{
//	$leavetype = "('06','11')";
//	$leavetype = $inqTSObj->getLeaveApp("PG_TNA_TEST..tblTK_LeaveAppHist as lvapp","PG_TNA_TEST..tblTK_AppTypes as lvtype",$_GET['empno'],$leavetype);
	$pdf->leavestats = "Maternity Leave";// substr($leavetype['appTypeDesc'],0,15);
}
$pdf->leavefrom = $_GET['dfrom']; //$leavetype['lvDateFrom'];
$pdf->leaveto = $_GET['dto']; //$leavetype['lvDateTo'];

$pdf->AddPage();	
$pdf->Content();
$pdf->SetMargins(20,0,10);
$pdf->Output('HDMF Certificate.pdf','D');
?>