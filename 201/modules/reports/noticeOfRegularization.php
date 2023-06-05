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
	var $branchadd;
	function Content() {
		if($_SESSION['company_code']==1){
			$this->Image(PG_LOGO, 33, 28 , '15' , '15' , 'JPG', '');
		}
		elseif($_SESSION['company_code']==2){
			$this->Image(PG_LOGO, 50, 28 , '15' , '15' , 'JPG', '');
		}
		elseif($_SESSION['company_code']==4){
			$this->Image(PG_LOGO, 39, 28 , '15' , '15' , 'JPG', '');
		}
		elseif($_SESSION['company_code']==5){
			$this->Image(PG_LOGO, 41, 28 , '15' , '15' , 'JPG', '');
		}
		$this->SetStyle("p","times","",11,"130,0,30");
		$this->SetStyle("pb","times","B",11,"130,0,30");
		$this->SetStyle("t1","times","B",11,0);
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
		$add=$this->branchadd;
		//$this->Image('../../../images/pg.jpg',67,10,5,7);
		$compName = $this->compName['compName'];
		$compAdd = ucwords(mb_strtolower($add));
		$empPos = $EmpInfo['posShortDesc'];
		$signatory = $this->signatory;
		$title = $this->title;
		
		if($EmpInfo['employmentTag']=="Contractual"){
			$regdate = date("F d, Y", strtotime('+11 month',strtotime($EmpOtherInfo['dateHired'])));	
		}
		elseif($EmpInfo['employmentTag']=="Probationary"){
			$regdate = date("F d, Y", strtotime('+6 month',strtotime($EmpOtherInfo['dateHired'])));	
		}
		else{
			$regdate = date("F d, Y", strtotime($EmpInfo['dateReg']));		
		}
		
		$dateHired = date("F d, Y",strtotime($EmpOtherInfo['dateHired']));
		//$dateResigned = date("F d, Y",strtotime('-1 day', strtotime($EmpOtherInfo['dateResigned'])));

		$this->SetMargins(20,0,20);
		$this->Ln(27);
		$this->SetFont('times', 'B', '16');
		$this->Cell(175,8,$compName,0,1,"C");
		$this->SetFont('times', 'B', '10');		
		$this->MultiCellTag(175,7,$compAdd,0,"C",0,true);
		$this->Ln(15);
		$this->SetFont('times', 'B', '12');
		$this->Cell(25,5,"EMP. NO.   : ",0,0,"L");
		$this->Cell(175,5,$EmpInfo['empNo'],0,1,"L");
		$this->Cell(25,5,"NAME         : ",0,0,"L");
		$this->Cell(175,5,strtoupper(mb_strtolower($EmpInfo['empLastName'].", ".$EmpInfo['empFirstName']." ".$EmpInfo['empMidName'])),0,1,"L");
		$this->Cell(25,5,"SECTION   : ",0,0,"L");
		$this->Cell(175,5,strtoupper(mb_strtolower($EmpInfo['deptDesc'])),0,1,"L");
		$this->Ln(13);
		$this->SetFont('times', 'BI', '12');
		$this->Cell(175,5,"NOTICE OF REGULARIZATION",0,1,"C");
		$this->Ln(10);
		$this->SetFont('times', '', '12');
		$this->MultiCell(175,0,"",0,"J",0);
		$this->MultiCellTag(175,5,"	We wish to inform you of your regular appointment as  <t1>$empPos</t1> effective <t1>$regdate</t1>.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"	Your duties and responsibilities are enumerated in the attached position description. Management, however, in exercising its prerogatives based on the assessment and perception of your qualifications, attitudes and competence, may transfer you from one position to another; assign to different shift or work schedule or move you around in the various areas in the Company's operation in order to ascertain where you will function with maximum benefit to the Company. Furthermore, you are expected to abide by the pertinent conditions of your employment enumerated below:",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"1.) Confidentiality - confidential information acquired during the course of your employment shall not be divulged to anybody without the prior written approval or consent of the management.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"2.) Other Employment - you shall not be employed gainfully or otherwise by any person including yourself, government or company whether governmental, public or private without the prior written approval of the comapny.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"3.) Benefits Entitlement - you are entitled to the benefits as per prevailing Company policies befitting your position.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"4.) Compliance of Code of Employee Discipline - you shall continue to comply with the Company's Code of Employee Discipline, which you have received early in your employment with us, and which has been taught and explain to you by the Management, and which you have understood and promise to observe while employed with us.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"5.) Any other terms and conditions not stipulated herein shall be treated in accordance with the prevailing policies.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5," We expect you to do your best to attain the standards required of your position and improve attitude towards your work and strive for efficiency and productivity in your job performance.",0,"J",0,true);

		$this->Ln(15);
		$this->Cell(115,4,"",0,0);	
		$this->Cell(50,2,$signatory,0,1,'C');				
		$this->Cell(115,2,"",0,0);	
		$this->Cell(50,4,"_______________________",0,1);	
		$this->Cell(115,4,"",0,0);	
		$this->SetFont('times', '', '10');
		$this->Cell(50,4,$title,0,1,'C');	
		$this->Ln(15);
		$this->SetFont('times', '', '12');
		$this->Cell(30,4,"ACCEPTED		:",0,0);
		$this->Cell(37,4,"___________________________________",0,1);	
		$this->Cell(30,4,"",0,0);
		$this->Cell(74,4,"NAME IN PRINT / SIGNATURE / DATE",0,1);			
	}
}	
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$type = $_GET['type'];
$pdf=new PDF();
$pdf->Open();
$pdf->FPDF($orientation='P',$unit='mm',$format='LEGAL');	
$pdf->compName = $inqTSObj->getCompany($_SESSION['company_code']);
$pdf->EmpInfo = $inqTSObj->empOtherInfos($_GET['empno']);
$pdf->EmpOtherInfo = $inqTSObj->getEmpCOEInfo($_GET['empno']);
$arrBranhInfo = $inqTSObj->getBrnchInfo($pdf->EmpOtherInfo['empBrnCode']);
//if($pdf->EmpOtherInfo['empBrnCode']!='0001'){
//	$pdf->signatory=$arrBranhInfo['coeSignatory'];
//	$pdf->title =strtoupper(mb_strtolower($arrBranhInfo['coeSignatoryTitle']));
//}
//else{
	$pdf->signatory="";
	$pdf->title ="CHAIRMAN";
//}
$pdf->branchadd=$arrBranhInfo['brnAddr1'];
$pdf->AddPage();	
$pdf->Content();
$pdf->SetMargins(20,0,10);
$pdf->Output('NOR.pdf','D');
?>
