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
	var $branchContact;
	var $sign;
	var $poss;
	var $allowance;
	var $allAmount;
	function Content() {
		
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
		$this->SetStyle("u","times","B",11,0);
		
		$EmpInfo = $this->EmpInfo;
		$EmpOtherInfo = $this->EmpOtherInfo;
		$signatory=$this->branchContact;
		$sign=$this->sign;
		$pos=$this->poss;
		$empname = $EmpInfo['empFirstName']." ".$EmpInfo['empMidName']." ".$EmpInfo['empLastName'];
		$emplname = $EmpInfo['empLastName'];
		$empaddress = strtoupper($EmpInfo['empAddr1'])." ".strtoupper($EmpInfo['empAddr2'])." ".strtoupper($EmpInfo['municipalityDesc'])." ".strtoupper($EmpInfo['provinceDesc']);
		$allAmount = number_format($this->allAmount,2);
		//$this->Image('../../../images/pg.jpg',67,10,5,7);
		$compName = $this->compName['compName'];
		$compAdd = $this->compName['compAddr1'] .", " .$this->compName['compAddr2'];
		$empPos = $EmpInfo['posShortDesc'];
		$regdate = date("F d, Y", strtotime($EmpInfo['dateReg']));
		$dateHired = date("F d, Y",strtotime($EmpOtherInfo['dateHired']));
		$dateResigned = date("F d, Y",strtotime('-1 day', strtotime($EmpOtherInfo['dateResigned'])));
		$daynow = date("d");
		$monthnow = date("F");
		$year = date("Y");
		if($EmpInfo['empSex']=="Male"){
			$gender = "he";	
			$sex = "his";
		}
		else{
			$gender = "she";
			$sex = "her";	
		}
		
		
		$this->SetMargins(20,0,20);
		$this->Ln(10);
		$this->SetFont('times', 'B', '14');
		$this->Cell(180,5,"Agreement",0,1,"C");
		$this->Ln(15);
		$this->SetFont('times', '', '12');
		$this->MultiCell(175,0,"",0,"J",0);
		$this->MultiCellTag(175,5,"Know All Men By These Presents:",0,"J",0,true);
		$this->Ln(3);
		//$this->Cell(15,5,"",0,0,"L");
		$this->MultiCellTag(160,5,"	  This Agreement made this <u>$daynow</u> day of <u>$monthnow</u> <u>$year</u> at the City of Manila by and between:",0,"J",0,true);
		$this->Ln(3);
		$this->Cell(13,5,"",0,0,"L");			
		$this->MultiCellTag(150,5,"<t1>$compName</t1> a domestic corporation duly organized and existing under the law of the Republic of the Philippines with office address at 900 Romualdez St., Paco, Manila herein represented by $signatory;",0,"J",0,true);
		$this->Ln(7);
		$this->Cell(13,5,"",0,0,"L");		
		$this->MultiCellTag(150,5,"<u>$empname</u>, Filipino, with residential address at $empaddress.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(180,5,"Witnesseth That:",0,"C",0,true);
		$this->Ln(3);		
		//$this->Cell(13,5,"",0,0,"L");		
		$this->MultiCellTag(175,5,"	  1. <u>$empname</u> is an employee of Puregold with a position of <u>$empPos</u>.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"	  2. In the performance of $sex duties, $gender has to regularly travel from one branch of Puregold to another.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"	  3. To avoid the cumbersome work of asking for cash advances of transportation expense, liquidating and auditing the same, parties have agreed to give <u>Php. $allAmount</u> as fixed transportation allowance per month.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"	  4. <u>$emplname</u> hereby manifest that the said fixed amount of transportation allowance is more than sufficient to cover the actual cost of the transportation that $gender incurs in connection with the business of Puregold.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"	  5. As the transportation allowance is a mere substitute for the actual transportation expense of <u>$emplname</u>, the parties understand and agree that the said allowance does not form part of <u>$emplname</u>'s salary and therefore shall be excluded in the computation of $sex 13<ss ypos='1.1'>th</ss> month pay, SSS, Philhealth, Pag-ibig and withholding tax.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"	  6. The grant of transportation allowance can be revoked by Puregold at any time for any cause such as when the work of <u>$emplname</u> no longer requires regular travel or when Puregold decides to simply advance or require <u>$emplname</u> to seek reimbursement of $sex actual transportation expenses.",0,"J",0,true);
		$this->Ln(3);		
		$this->MultiCellTag(175,5,"	   IN WITNESS WHEREOF, parties have hereunto affixed their signatures this _______ day of __________________ $year at __________________________________.",0,"J",0,true);
		$this->Ln(15);	
		$this->SetFont('times', 'B', '12');	
		$this->Cell(80,5,$compName,0,0,"C");
		$this->Cell(10,5,"",0,0);
		$this->Cell(90,5,$empname,0,1,"C");
		//$this->Cell(30,5,"",0,0);
		$this->SetFont('times', '', '12');
		$this->Cell(80,5,"Employer",0,0,"C");
		$this->Cell(10,5,"",0,0);
		$this->Cell(90,5,"Employee",0,1,"C");
		$this->Ln(10);
		$this->Cell(10,5,"By:",0,1);
		//$this->Cell(10,5,"",0,0);
		$this->SetFont('times', 'B', '12');
		$this->MultiCellTag(80,5,$sign,0,"C",0,true);
		$this->SetFont('times', '', '12');
		//$this->Cell(10,5,"",0,0);
		$this->MultiCellTag(80,5,$pos,0,"C",0,true);
		$this->Ln(15);	
		$this->MultiCellTag(175,5,"Signed in the presence of:",0,"C",0,true);	
		$this->Ln(10);	
		$this->Cell(40,4,"______________________________",0,0);	
		$this->Cell(70,4,"",0,0);	
		$this->Cell(40,4,"______________________________",0,1);	
		
		$this->SetFont('times', '', '12');
		$this->Cell(15,4,"",0,0);	
		$this->Cell(40,4,"Department Manager",0,1);	
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
$arrBranhInfo = $inqTSObj->getBrnchInfo("0001");
$allowance = $inqTSObj->getTranspoAllowance($_GET['empno']);
$pdf->allAmount = $allowance['amnt'];
$pdf->signatory=$arrBranhInfo['brnSignatory'];
$pdf->branchContact=$arrBranhInfo['namePrefix']." ".$arrBranhInfo['coeSignatory'].", ".$arrBranhInfo['coeSignatoryTitle'];
$pdf->sign=$arrBranhInfo['coeSignatory'];
$pdf->poss=$arrBranhInfo['coeSignatoryTitle'];
$pdf->AddPage();	
$pdf->Content();
$pdf->SetMargins(20,0,10);
$pdf->Output('Transportation Agreement.pdf','D');
?>
