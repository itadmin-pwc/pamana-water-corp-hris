<?
################### INCLUDE FILE #################
session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	//include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	require_once("../../../includes/pdf/MultiCellTag/class.multicelltag.php"); 
	
################ GET TOTAL RECORDS ###############

############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$commonObj = new commonObj();
class PDF extends fpdf_multicelltag
{
	var $emp;
	var $lname;
	var $fname;
	var $mname;
	var $position;
	var $department;
	var $spouse;
	var $age;
	var $municipality;
	var $province;
	var $address;
	var $company;
	var $branch;
		
	function Header(){
		$this->Ln(15);
	}
	

	function displayContent() {

		$this->day = date('d');
		$this->month = date('F');
		$this->year = date('Y');	

		//$tamount = round($arrDtl['totAmt'],2);
		$this->SetStyle("p","times","",11,"130,0,30");
		$this->SetStyle("pb","times","B",11,"130,0,30");
		$this->SetStyle("t1","times","BI",11,0);
		$this->SetStyle("t2","times","B",11,0);
		$this->SetStyle("t3","times","B",14,"203,0,48");
		$this->SetStyle("t4","arial","BI",11,"0,151,200");
		$this->SetStyle("hh","times","BI",11,0);
		$this->SetStyle("ss","arial","",7,"203,0,48");
		$this->SetStyle("font","helvetica","",10,"0,0,255");
		$this->SetStyle("style","helvetica","BI",10,"0,0,220");
		$this->SetStyle("size","times","BI",13,"0,0,120");
		$this->SetStyle("color","times","BI",13,"0,255,255");
		
		$this->SetFont('times', 'B', '12');
		$this->Cell(195,5,"Training Agreement",0,1,"C");
		$this->SetMargins(30,0,30);
		$this->Ln(10);
		$this->SetFont('times', '', '11');
		$this->MultiCell(155,0,"",0,"J",0);
		$this->MultiCellTag(155,5,"Know all men by these presents:",0,"L",0,true);
		$this->Ln();
		$this->MultiCellTag(155,5,"\tThis Training Agreement executed by:",0,"L",0,true);
		$this->Ln();	
		$this->Cell(10,5,"",0,0);
		$this->MultiCellTag(135,5,"\t<t2>".ucwords(strtolower($this->company['compName']))."</t2>, a corporation duly organized and existing under Philippines laws with principal office at No. 900 Romualdez St., Paco, Manila, represented by ___________________________, Senior Manager of Human Resources Department, hereinafter referred to as the ".'"Company;"',0,"J",0,true);
		$this->Ln();	
		$this->MultiCellTag(155,5,"-and-",0,"C",0,true);	
		$this->Ln();	
		$this->Cell(10,5,"",0,0);
		$this->MultiCellTag(135,5,"\t<t2>".ucwords(strtolower($this->fname." ".$this->mname." ".$this->lname))."</t2>, of legal age, married to ".$this->spouse." / single / widow / widower, and resident of ".ucwords(strtolower($this->address))." hereinafter referred to as the ".'"Trainee" ---',0,"J",0,true);
		$this->Ln(9);	
		$this->MultiCellTag(155,5,"Witnesseth:",0,"C",0,true);
		$this->Ln(9);
		$this->MultiCellTag(155,5,"\tWhereas, the Trainee is an employee of the Company whose employment is subject to the terms and conditions of a letter of appointment/contract;",0,'J',0,true); 
		$this->Ln();
		$this->MultiCellTag(155,5,"\tWhereas, the Company provides on-the-job training to the employee to improve his skills in the form of on-the-job briefings, lectures, reading materials, policies and procedures;",0,"J",0,true);  
		$this->Ln();	
		$this->MultiCellTag(155,5,"\tWhereas, the Company provides a specialized or upgraded training to employees who wish to acquire the potential for promotion from their present position and who are willing to abide by the terms and conditions thereof;",0,0,"J");
		$this->Ln();
		$this->MultiCellTag(155,5,"\tWhereas, the Trainee applied for and was granted by the Company with the privilege to enroll in specialized or upgraded training subject to the terms and conditions hereof;",0,'J',0,true); 
		$this->Ln();
		$this->MultiCellTag(155,5,"\tNow, therefore, the parties have agreed:",0,"L",0,true);
		$this->Ln();
		$this->MultiCellTag(155,5,"\t1.  <t1>Scope of the Specialized/Upgraded Training.</t1>---The Training shall, in addition to all the trainings ordinarily provided by the Company, include enrollment, whenever available, in seminars conducted by specialists in or outside of Metro Manila or the Philippines or tours abroad for the purpose of training and exposure to the business and economic development in other countries and the like, including travel expenses by land, water or air, and hotel accommodations.",0,"J",0,true);									
		$this->Ln();
		$this->MultiCellTag(155,5,"\tThe Training shall be made available by the Company at such location, time and manner at its sole discretion.",0,"J",0,true);  
		$this->Ln();
		$this->MultiCellTag(155,5,"\tThe enrollment in the specialized or upgraded Training of the Trainee does not constitute assurance that he shall be promoted. Promotion shall be based on the devotion given by the Trainee to the Training, the results thereof demonstrated by his performance in his present position, the evaluation of the screening committee, and approval by the management.",0,"J",0,true);  
		$this->Ln();
		$this->MultiCellTag(155,5,"\t2.  <t1>Cost of Training.</t1>---The specialized or upgraded training shall be continuous while the Trainee is employed with the company. The cost thereof shall be borne by the company subject to the provisions of paragraph 5. ",0,"J",0,true);  
		$this->Ln();
		$this->MultiCellTag(155,5,"\t3.  <t1>Confidentiality and Non-Compete.</t1>---For and in consideration of his enrollment, the Trainee shall strictly and faithfully keep all the information he will receive during his training in confidence, especially the confidential information which he shall not disclose to any party or use for personal purpose, without the written consent of the company. Confidential information means all information, whether written, oral, electronic or other forms such as manuals, reports, designs, drawings, plans, flowcharts, product information, product plans, sales and marketing plans, leasing plans and tenants lists, pricing, customer and financial information about the company, its sister companies, business partners and clients, except information which are of public record, or in the public domain, or required to be disclosed by law.",0,"J",0,true);  
		$this->Ln();
		$this->MultiCellTag(155,5,"\tFurthermore, if his employment is terminated either by himself by resignation or otherwise, or by the company for cause, the Trainee binds himself not to work, directly or indirectly, either as an employee, officer, partner, shareholder, director, or agent of any company which is a competitor of Pamana Water for a period of two (2) years from the date of his separation.",0,"J",0,true);  
		$this->Ln();
		$this->MultiCellTag(155,5,"\t4.  <t1>Results of Training.</t1>---The Company will periodically evaluate the progress of the Trainee's training. If, at any periodic and at final period, the Trainee has failed to regularly undergo or attend to all the aspects of the training or to pass the tests, or is found guilty of any act or omission which is a ground for termination of employment, it shall be a ground for the company to terminate the employment of the Trainee or not to rehire or extend his hiring for another period. If he passes and qualifies for the position, his employment as such shall be regularized.",0,"J",0,true);  
		$this->Ln();
		$this->MultiCellTag(155,5,"\t5.  <t1>Reimbursement of Cost of Training.</t1>---Should the Trainee resign with or without the approval of the company or be separated from employment for cause, he shall reimburse the company of the actual cost of the training incurred by the company for his benefit plus liquidated damages in the amount of Two Hundred Thousand Pesos (P200,000.00). The company shall have the right to charge or set-off the said cost wholly or partially against whatever amount is still payable to the Trainee as last pay.",0,"J",0,true);  
		$this->Ln();
		$this->MultiCellTag(155,5,"\t6.  <t1>Interpretation.</t1>---The terms and conditions of this Training Agreement shall be in addition to the terms and conditions of all existing letter of appointment, employment contract and other employment documents that the employee has signed.",0,"J",0,true);  
		$this->Ln();
		$this->MultiCellTag(155,5,"\tIn witness whereof, the parties have hereunto affixed their signatures this ___ day of ____________ in Manila.",0,"J",0,true);  
		$this->Ln();
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(180,5,$this->company['compName'],0,1,'L');
		$this->Ln();
		$this->Cell(190,1,"By:",0,1,'l');
		$this->Ln();
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(65,5,"________________________",0,0,'C'); // Elvira D. Gutierrez HRD
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(65,5,ucwords(strtolower($this->fname." ".$this->mname." ".$this->lname)),0,0,'C');
		$this->Cell(10,5,"",0,0,'C');
		$this->Ln();
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(65,5,"Senior Manager, HRD",'T',0,'C');
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(65,5,"Trainee",'T',0,'C');
		$this->Cell(10,5,"",0,0,'C');
		$this->Ln(10);
		$this->Cell(155,5,"Signed in the presence of:",0,0,'C');
		$this->Ln(10);
		$this->Cell(77,5,"___________________________",0,0,'C');
		$this->Cell(78,5,"___________________________",0,'1','C');
		$this->Ln(6);
		$this->MultiCellTag(155,5,"<t2>Acknowledgment</t2>",0,'C',0);
		$this->Ln(5);
		$this->MultiCellTag(155,5,"Republic of the Philippines)",0,'1','L');
		$this->MultiCellTag(155,5,"City of Manila                    )SS.",0,'1','L');
		$this->MultiCellTag(155,5,"x------------------------------------x",0,'1','L');
		$this->Ln(5);
		$this->MultiCellTag(155,5,"\tBefore me, a notary public for and in the City of Manila, this ___ day of ________ 2014 at the City of Manila, personally appeared:",0,"J",0);
		$this->Ln(5);
		$this->Cell(77,5,"Names",0,0,'C');
		$this->Cell(78,5,"Competent Evidence of Identity",0,'1','C');
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(77,5,"",0,0,'L'); // HRD Elvira D. Gutierrez
		$this->Cell(68,5,"_____________________________",0,'1','L');
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(77,5,ucwords(strtolower($this->fname." ".$this->mname." ".$this->lname)),0,0,'L');
		$this->Cell(68,5,"_____________________________",0,'1','L');
		$this->Ln(5);
		$this->MultiCellTag(155,5,"known to me as the same persons who executed the foregoing Training Agreement and they acknowledged before me that the same is their free and voluntary act and deed and of the corporation represented.",0,"J",0);
		$this->Ln(5);
		$this->MultiCellTag(155,5,"\tWitness my hand and seal.",0,"J",0);
		$this->Ln(5);
		$this->Cell(155,5,"Doc. No. _______",0,'1','L');
		$this->Cell(155,5,"Page No. _______",0,'1','L');
		$this->Cell(155,5,"Book No._______",0,'1','L');
		$this->MultiCellTag(155,5,"Series of <t1>$this->year</t1>",0,'1','L');
		
	}
	
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(155,1,'','');
		$this->Ln();
		$this->SetFont('Courier','',9);
//		$this->Cell(77,6,$this->series,0,'L','');
//		$this->Cell(77,6,"".$this->PageNo(),0,'L','');
	}
	
}

	$pdf = new PDF('P', 'mm', 'LEGAL');
	$pdf->company = $commonObj->getCompany($_SESSION['company_code']);
	$pdf->emp = $_GET['empno'];
	$pdf->lname = $_GET['lname'];
	$pdf->fname = $_GET['fname'];
	$pdf->mname = $_GET['mname'];
	$pdf->position = $_GET['position'];
	$pdf->department = $_GET['department'];
	$pdf->address = $_GET['address'];
	$pdf->spouse = $_GET['spouse'];
	$pdf->age = $_GET['age'];
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->displayContent();		
	$pdf->Output('Undertaking Report.pdf','D');

?>
