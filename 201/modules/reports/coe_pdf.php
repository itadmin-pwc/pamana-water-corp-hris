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
		
		$EmpAllowM =0;
		$EmpAllowD =0;
		$EmpAllowM = $this->EmpAllowMonthly;
		$EmpAllowD = $this->EmpAllowDaily;
		$empallowtotal=0;
		$empallowtotal=$EmpAllowM+$EmpAllowD;
		$EmpInfo = $this->EmpInfo;
		$EmpOtherInfo = $this->EmpOtherInfo;
		//$this->Image('../../../images/pg.jpg',67,10,5,7);
		if ($EmpOtherInfo['empSex']=="M") {
			$empName = "Mr. " .$EmpOtherInfo['empFirstName'] . " " . $EmpOtherInfo['empMidName'][0] . ". " .$EmpOtherInfo['empLastName'];
			$empLName = "Mr. " .$EmpOtherInfo['empLastName'];
			$gender ="his";
			$gender2="He";
		} else {
			$gender ="her";
			$gender2="She";
			//if ($EmpInfo['empMarStat']=="Single") {
				$empName = "Ms. " .$EmpOtherInfo['empFirstName'] . " " . $EmpOtherInfo['empMidName'][0] . ". " .$EmpOtherInfo['empLastName'];
				$empLName = "Ms. " .$EmpOtherInfo['empLastName'];
			//} else {
			//	$empName = "Mrs. " . $EmpOtherInfo['empFirstName'] . " " . $EmpOtherInfo['empMidName'][0] . ". " .$EmpOtherInfo['empLastName'];
			//	$empLName = "Mrs. " .$EmpOtherInfo['empLastName'];
			//}	
		}
		switch($_GET['type']) {
			case 1:
				$type = "for Employment purposes";
				//employment
			break;
			case 2:
				$type = "in connection with $gender bank loan application";
			break;
			case 3:
				$type = "in connection with $gender mobile phone line application";
			break;
			case 4:
				$type = "in connection with $gender car loan application";
			break;
			case 5:
				$type = "in connection with $gender credit card application";
			break;
			case 6:
				$type = "in connection with $gender school requirement for ".$_GET['course']." degree program of ".$_GET['school'];
			break;
			case 7:
				$type = "for Housing Loan application";
			break;
			case 8:
				$type = "for Pag-ibig loan application";
			break;
			case 9:
				$type = "for Emergency loan application";
			break;
			case 10:
				$type = "for Salary loan application ";
			break;
			case 11:
				$type = "for VISA application";
			break;
		}
//		if($_GET['salary']=="Yes"){
//			if($empallowtotal>0){
//				$allowance="  and a monthly allowance of <t1>P".number_format($empallowtotal,2)."</t1>";
//			}
//			$mrate = $EmpInfo['empMrate'];
//			$salaryrate="$gender2 is receiving a monthly basic salary of <t1>P".number_format($mrate,2)."</t1>$allowance.";	
//		}

		if($_GET['salary']=="Yes"){
			if($EmpAllowM>0){
				$allowance=" and a monthly allowance of <t1>P".number_format($EmpAllowM,2)."</t1>";
			}
			if($EmpAllowD>0){
				$allowance=" and a monthly COLA of <t1>P".number_format($EmpAllowD,2)."</t1>";
			}
			$mrate = $EmpInfo['empMrate'];
			$salaryrate=" $gender2 is receiving a monthly basic salary of  <t1>P".number_format($mrate,2)."</t1>$allowance.";	
		}

/*		if($_GET['salary']=="Yes"){
			if($empallowtotal>0){
				$allowance="  and a monthly allowance of <t1>P".number_format($empallowtotal,2)."</t1>";
			}
			$mrate = $EmpInfo['empMrate'];
			$salaryrate="$gender2 is receiving a monthly basic salary of <t1>P".number_format($mrate,2)."</t1>$allowance.";	
		}
*/

		$compName = $this->compName['compName'];
		$compAdd = $this->compName['compAddr1'] .", " .$this->compName['compAddr2'];
		$empPos = $EmpInfo['posShortDesc'];
		$dateHired = date("F d, Y",strtotime($EmpOtherInfo['dateHired']));
		if($EmpOtherInfo['dateResigned']!="" && $EmpInfo['empStat']!="RG"){
			$dateResigned = date("F d, Y",strtotime('-1 day', strtotime($EmpOtherInfo['dateResigned'])));
		}
		else if($EmpOtherInfo['endDate']!="" && $EmpInfo['empStat']!="RG"){
			$dateResigned = date("F d, Y",strtotime('-1 day', strtotime($EmpOtherInfo['endDate'])));
		}
		if ($EmpInfo['empStat'] == "RG")
			$date = "from <t1>$dateHired</t1> up to <t1>present</t1>.";
		else
			$date = "from <t1>$dateHired</t1> to <t1>$dateResigned</t1>.";
		$this->SetFont('Courier', 'B', '14');
		$this->Cell(167,4,'',0,0,"C");
		$this->Ln();
		$this->SetFont('Courier', '', '11');
		$this->Cell(167,4,'',0,0,"C");
		$this->Ln(40);
		$this->SetFont('Arial', 'B', '18');
		$this->SetFont('Arial', 'I', '18');
		$this->Cell(200,8,"C E R T I F I C A T I O N",0,0,"C");
		$this->Ln(30);
		$this->SetFont('Arial', '', '14');
		$this->SetMargins(23,0,20);
		$this->MultiCell(167,0,"",0,"J",0);
		$this->MultiCellTag(167,10,"		This is to certify that <t1>$empName</t1> is employed with <t1>$compName</t1> as <t1>$empPos</t1> $date $salaryrate",0,"J",0,true);
		$this->Ln(10);		
		$this->MultiCellTag(167,10,"		This certification is being issued upon the request of <t1>$empLName</t1> $type.",0,"J",0,true);
		$this->Ln(10);
		$this->MultiCellTag(167,10,"		Issued this <t1>".date ('dS \d\a\y \o\f F, Y')."</t1>" . " ". "at $compAdd ",0,"J",0,true);
		$this->Ln(40);
		$this->Cell(115,4,"",0,0);
		$this->SetFont('Arial', 'B', '14');
		$this->Cell(25,4,'__________________________',0,0,'C');
		//$this->Cell(25,4,$this->signatory,0,0,'C');
		$this->SetFont('Arial', '', '14');
		$this->Ln(6);
		$this->Cell(115,4,"",0,0);
		$this->Cell(25,4,'HR MANAGER',0,0,'C');
		//$this->Cell(25,4,$this->title,0,0,'C');
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
$pdf->EmpAllowMonthly = $inqTSObj->empAllowanceMonthly($_GET['empNo']);
$pdf->EmpAllowDaily = $inqTSObj->empAllowanceDaily($_GET['empNo']);
$pdf->EmpInfo = $inqTSObj->empCOEInfos($_GET['empNo']);
$pdf->EmpOtherInfo = $inqTSObj->getEmpCOEInfo($_GET['empNo']);
$arrBranhInfo = $inqTSObj->getBrnchInfo($pdf->EmpOtherInfo['empBrnCode']);
$pdf->signatory=$arrBranhInfo['brnSignatory'];
$pdf->title=$arrBranhInfo['brnSignTitle'];
$pdf->AddPage();	
$pdf->Content();
$pdf->SetMargins(20,0,10);
$pdf->Output('COE.pdf','D');
?>
