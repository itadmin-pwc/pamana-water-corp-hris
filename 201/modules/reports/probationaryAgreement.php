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
	var $allowance;
	var $ecola;
	
	function Content() {
		if($_SESSION['company_code']==1){
			$this->Image(PG_LOGO, 33, 16 , '15' , '15' , 'JPG', '');
		}
		elseif($_SESSION['company_code']==2){
			$this->Image(PG_LOGO, 50, 16 , '15' , '15' , 'JPG', '');
		}
		elseif($_SESSION['company_code']==4){
			$this->Image(PG_LOGO, 39, 16 , '15' , '15' , 'JPG', '');
		}
		elseif($_SESSION['company_code']==5){
			$this->Image(PG_LOGO, 41, 16 , '15' , '15' , 'JPG', '');
		}
		$this->SetStyle("p","times","",11,"130,0,30");
		$this->SetStyle("pb","times","B",11,"130,0,30");
		$this->SetStyle("t1","times","B",11,0);
		$this->SetStyle("t3","times","B",14,"203,0,48");
		$this->SetStyle("t4","arial","BI",11,"0,151,200");
		$this->SetStyle("hh","times","B",11,"255,189,12");
		$this->SetStyle("ss","arial","",7,0);
		$this->SetStyle("font","helvetica","",10,"0,0,255");
		$this->SetStyle("style","helvetica","BI",10,"0,0,220");
		$this->SetStyle("size","times","BI",13,"0,0,120");
		$this->SetStyle("color","times","BI",13,"0,255,255");
		
		$EmpInfo = $this->EmpInfo;
		$EmpOtherInfo = $this->EmpOtherInfo;
		$add = $this->branchadd;
		$signatory = $this->signatory;
		$title =  $this->title;
		$allowance = $this->allowance;
		$ecola = $this->ecola;
		//$this->Image('../../../images/pg.jpg',67,10,5,7);
		$compName = $this->compName['compName'];
		$compAdd = ucwords(mb_strtolower($add));
		$empPos = $EmpInfo['posShortDesc'];
		$enddate = date("F d, Y", strtotime('+5 month', strtotime($EmpOtherInfo['dateHired'])));
		$dateHired = date("F d, Y",strtotime($EmpOtherInfo['dateHired']));
		//$dateResigned = date("F d, Y",strtotime('-1 day', strtotime($EmpOtherInfo['dateResigned'])));
		if($EmpInfo['empSex']=="Male"){
			$gender="Dear Mr.";	
		}
		else{
			$gender="Dear Ms.";	
		}
		
		
		if($EmpInfo['empPayGrp']=="Group 1"){
			$paygroup="10<ss ypos='1.1'>th</ss> and 25<ss ypos='1.1'>th</ss>";
		}
		elseif($EmpInfo['empPayGrp']=="Group 2"){
			$paygroup="15<ss ypos='1.1'>th</ss> and end";
		}
		
		if($allowance!=""){
			$salary="P ".number_format($EmpInfo['empMrate'],2)." per month (basic) plus "."P ".number_format($allowance,2)." per month (allowance) ";	
		}
		elseif($ecola!=""){
			$salary="P ".number_format($EmpInfo['empDrate'],2)." plus "."P ".number_format($ecola=$ecola/26,2)." COLA per day ";			
		}
		else{
			if($EmpInfo['empPayType']=="Monthly"){
				$salary="P ".number_format($EmpInfo['empMrate'],2)." per month ";	
			}
			elseif($EmpInfo['empPayType']=="Daily"){
				$salary="P ".number_format($EmpInfo['empDrate'],2)." per day ";		
			}
		}
		$sss = $EmpInfo['empSssNo'];
		$tin = $EmpInfo['empTin'];
		$this->SetMargins(23,20,23);
		$this->Ln(15);
		$this->SetFont('times', 'B', '16');
		$this->Cell(170,8,$compName,0,1,"C");
		$this->SetFont('times', 'B', '10');		
		$this->MultiCellTag(170,7,$compAdd,0,"C",0,true);
		$this->Ln(20);
		$this->SetFont('times', 'B', '12');
		$this->Cell(170,5,"PROBATIONARY EMPLOYMENT AGREEMENT",0,1,"C");
		$this->Ln(13);		
		$this->SetFont('times', 'B', '12');
		$this->Cell(170,5,strtoupper($EmpInfo['empLastName'].", ".$EmpInfo['empFirstName']." ".$EmpInfo['empMidName']),0,1,"L");
		$this->Cell(170,5,ucwords(mb_strtolower($EmpInfo['empAddr1']))." ".ucwords(mb_strtolower($EmpInfo['municipalityDesc']))." ".ucwords(mb_strtolower($EmpInfo['provinceDesc'])),0,1,"L");
		$this->Ln(13);
		$this->Cell(170,5,$gender." ".strtoupper($EmpInfo['empLastName']).",",0,1,"L");
		$this->Ln(10);
		$this->SetFont('times', '', '12');
		$this->MultiCell(170,0,"",0,"J",0);
		$this->MultiCellTag(170,5.5,"	  We are pleased to confirm your employment with the company on a probationary status, with the designation of <t1>$empPos</t1> under the following terms and conditions:",0,"J",0,true);
		$this->Ln(5);		
		$this->Cell(12,5,"1.",0,0,"L");
		$this->MultiCellTag(160,5.5,"The probationary employment is for the period not to exceed six (6) months, commencing on <t1>$enddate</t1> unless we notify you in writing on or before that latter date that your services will be continued and your name transferred to the permanent payroll. However, we reserve the right to terminate your employment upon notice of any cause or if performance is unsatisfactory, in which case you will be paid up to the last date of your actual sercvice.",0,"J",0,true);
		$this->Ln(5);	
		$this->Cell(12,5,"2.",0,0,"L");	
		$this->MultiCellTag(160,5.5,"For giving your entire time and attention to the work assigned to you, you shall be paid a salary/wage of <t1>$salary</t1> which you shall receive on the $paygroup of each month. Your salary/wage already includes compensation for unworked legal holiday.",0,"J",0,true);
		$this->Ln(5);
		$this->Cell(12,5,"3.",0,0,"L");		
		$this->MultiCellTag(160,5.5,"You are required to render six (6) working days in a week in accordance with the working hours/shifts/department to which you may be assigned, transferred and re-assigned from time to time at the discretion of the Management. Further, you shall not accept work in any other establishments while employed with the Company, without prior notice or written permission from the Management.",0,"J",0,true);
		$this->Ln(5);
		$this->Cell(12,5,"4.",0,0,"L");		
		$this->MultiCellTag(160,5.5,"As a probationary employee, unless prescribed by law or management, you are not entitled to enjoy any benefit or privilege accorded to regular employees.",0,"J",0,true);
		$this->Ln(5);
		$this->Cell(12,5,"5.",0,0,"L");		
		$this->MultiCellTag(160,5.5,"Any and all expenses you may incur in the execution of the duties of your position shall be reimbursed to you, provided that such expenses have been duly authorized by the Company.",0,"J",0,true);
		$this->Ln(5);
		$this->Cell(12,5,"6.",0,0,"L");		
		$this->MultiCellTag(160,5.5,"You agree that all records, documents and properties of the Company or its clients in your custody shall be immediately surrendered to the Company, if requested during the employment period, and at the termination thereof, whether or not requested.",0,"J",0,true);
		$this->Ln(5);
		$this->Cell(12,5,"7.",0,0,"L");		
		$this->MultiCellTag(160,5.5,"All information, document and records that will come to your knowledge and information or possession during your employment with us are strictly confidential and shall not to be reproduced, disclosed or used by you for personal or other purposes during and after your employment with us. The company reserves the right to all intellectual property rights over all said information, document and records.",0,"J",0,true);
		$this->Ln(40);
		$this->Cell(12,5,"8.",0,0,"L");		
		$this->MultiCellTag(160,5.5,"In the event you are sent for special training, relative to your position, you agree that the training expenses to be incurred by the Company are in the nature of an investment for the use of your future services to the Company. As such, you agree to sign a Training Contract each time you are sent for special training with specifications on your exclusive services to the Company after each training. Pursuant to this understanding, you agree to reimburse the Company, among other remedies available to it, a prorata portion of the training expenses incurred for your training, should you leave the Company before the end of the Training Contract you have duly accepted.",0,"J",0,true);
		$this->Ln(5);
		$this->Cell(12,5,"9.",0,0,"L");		
		$this->MultiCellTag(160,5.5,"You have been furnished with a copy of the company's Code of Discipline now in effect and have been oriented thereon. It is your essential duty and responsibility to follow the company's rule of discipline and with regulations that the company may issue from time to time.",0,"J",0,true);
		$this->Ln(5);
		$this->Cell(12,5,"10.",0,0,"L");		
		$this->MultiCellTag(160,5.5,"It is expressedly agreed and understood that there are no verbal agreements or understanding, between you and the Company affecting this agreement and that no alterations or variations of the terms hereof shall be binding upon either party to this agreement unless the same are reduced in writing and signed by you and the Company.",0,"J",0,true);
		$this->Ln(15);
		$this->Cell(13,5,"",0,0,"L");		
		$this->MultiCellTag(160,5,"Kindly signify your conformity by signing below.",0,"J",0,true);

		$this->Ln(35);	
		$this->Cell(95,4,"",0,0);	
		$this->Cell(40,4,"Very truly yours,",0,1);	
		$this->Ln(15);	
		$this->Cell(83,4,"",0,0);	
		$this->SetFont('times', 'B', '12');
		$this->Cell(60,4,$signatory,0,1,"C");
		$this->Cell(83,4,"",0,0);	
		$this->SetFont('times', '', '10');
		$this->Cell(60,4,$title,0,1,"C");	
		$this->Ln(15);
		$this->SetFont('times', '', '12');
		$this->MultiCellTag(170,5.5,"	  I certify that I have read the foregoing and its contents have been fully translated and explained to me in Tagalog, dialect that I know, speak and understand. I have received a copy of the Code of Discipline and have been oriented thereof. I have fully understood the terms and conditions of my probationary employment and I now hereby affix my signature as evidence that I fully agree to all of them.",0,"J",0,true);
		$this->Ln(15);
		$this->Cell(95,4,"",0,0);	
		$this->Cell(40,4,"________________________",0,1);
		$this->Cell(95,4,"",0,0);	
		$this->Cell(40,4,"Signature Over Printed Name",0,1);
		$this->Ln(10);
		$this->Cell(95,4,"",0,0);	
		$this->Cell(40,4,"________________________",0,1);
		$this->Cell(115,4,"",0,0);	
		$this->Cell(40,5,"Date",0,1);
		$this->SetFont('times', 'B', '12');
		$this->Cell(12,5,"SSS :",0,0);
		$this->SetFont('times', '', '12');
		$this->Cell(50,5,$sss,0,1);
		$this->SetFont('times', 'B', '12');
		$this->Cell(12,5,"TIN :",0,0);
		$this->SetFont('times', '', '12');
		$this->Cell(50,5,$tin,0,1);
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
$pdf->signatory=str_replace("Ã'","Ñ",($arrBranhInfo['brnSignatory']));
//$pdf->title=strtoupper(mb_strtolower($arrBranhInfo['brnSignTitle']));
$pdf->title=$arrBranhInfo['brnSignTitle'];
$pdf->branchadd=$arrBranhInfo['brnAddr1'];
$pdf->ecola=$inqTSObj->empAllowanceDaily($_GET['empno']);
$pdf->allowance=$inqTSObj->empAllowanceMonthly($_GET['empno']);
$pdf->AddPage();	
$pdf->Content();
$pdf->SetMargins(20,0,20);
$pdf->Output('NOR.pdf','D');
?>