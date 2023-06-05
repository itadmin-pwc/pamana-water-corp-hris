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
	var $status;
	var $municipality;
	var $province;
	var $address;
	var $company;
	var $branch;
	var $sss;
		
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
		$this->SetStyle("t1","times","B",11,0);
		$this->SetStyle("t3","times","B",14,"203,0,48");
		$this->SetStyle("t4","arial","BI",11,"0,151,200");
		$this->SetStyle("hh","times","BI",11,0);
		$this->SetStyle("ss","arial","",7,"203,0,48");
		$this->SetStyle("font","helvetica","",10,"0,0,255");
		$this->SetStyle("style","helvetica","BI",10,"0,0,220");
		$this->SetStyle("size","times","BI",13,"0,0,120");
		$this->SetStyle("color","times","BI",13,"0,255,255");
		
		$this->SetFont('times', 'B', '12');
		$this->Cell(195,5,"Undertaking",0,1,"C");
		$this->SetMargins(30,0,30);
		$this->Ln(6);
		$this->SetFont('times', '', '12');
		$this->MultiCell(155,0,"",0,"J",0);
		$this->MultiCellTag(155,5,"Know all men by these presents:",0,"L",0,true);
		$this->Ln();	
		if($this->status=="SG"){
			$marstat = "single";
		}	
		elseif($this->status=="ME"){
			$marstat = "married";	
		}
		elseif($this->status=="SP"){
			$marstat = "separated";
		}
		elseif($this->status=="WI"){
			$marstat = "widow(er)";
		}
		$this->MultiCellTag(155,5,"\tI, <t1>".ucwords(strtolower($this->fname." ".$this->mname." ".$this->lname))."</t1>, Filipino, of legal age, ".$marstat.", and a resident of ".ucwords(strtolower($this->address))." hereby undertake:",0,"J",0,true);
		$this->Ln();	
		$this->MultiCellTag(155,5,"\t1. When I applied for employment with ".ucwords(strtolower($this->company['compName'])).' ("Puregold" or the "Company")'.", I represented that I will always be loyal to the Company;",0,'J',0,true); 
		$this->Ln();
		$this->MultiCellTag(155,5,"\t2. To formalize this representation, I hereby undertake to put my loyalty into action by giving my very best in performing the duties and responsibilities entrusted to me; observing honesty and good faith in dealing with the Company and its customers; and keeping in strict confidence all information that have been acquired by or disclosed to me in the performance of my duties;",0,"J",0,true);  
		$this->Ln();	
		$this->MultiCellTag(155,5,"\t3. I expressly warrant that I will not disclose any information acquired by or given to me during my employment to any party especially to competitors of the Company, nor will I seek employment with any person or company which is a competitor of Puregold, not only during the period of my employment but also within two (2) years from the time I have ceased to be an employee of Puregold;",0,0,"J");
		$this->Ln();
		$this->MultiCellTag(155,5,"\t4. All the foregoing have been explained to me both in English and in the dialect I understand and speak.",0,'J',0,true); 
		$this->Ln();
		$this->MultiCellTag(155,5,"In witness whereof, I have hereunto affixed my signature this ____ day of __________ at the City of Manila.",0,"J",0,true);  
		$this->Ln(10);
		$this->Cell(80,5,"",0,0,'C');
		$this->Cell(77,5,"___________________",0,0,'C');
		$this->Ln(10);
		$this->Cell(155,5,"Signed in the presence of:",0,0,'C');
		$this->Ln(10);
		$this->Cell(77,5,"___________________________",0,0,'C');
		$this->Cell(78,5,"___________________________",0,'1','C');
		$this->Ln(6);
		$this->MultiCellTag(155,5,"<t1>Acknowledgment</t1>",0,'C',0);
		$this->Ln(5);
		$this->MultiCellTag(155,5,"Republic of the Philippines)",0,'1','L');
		$this->MultiCellTag(155,5,"City of Manila           )SS.",0,'1','L');
		$this->Ln(5);
		$this->MultiCellTag(155,5,"\tBefore me, a Notary Public for and in the City of Manila, this _____ day of _________________, personally appeared:",0,"J",0);
		$this->Ln(5);
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(67,5,"Names",0,0,'C');
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(68,5,"Competent Evidence of Identity",0,'1','C');
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(67,5,ucwords(strtolower($this->fname." ".$this->mname." ".$this->lname)),'B',0,'L');
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(68,5,"SSS ID No.: ".$this->sss,'B','1','L');
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(67,5,"Elvira D. Gutierrez",'B',0,'L');
		$this->Cell(10,5,"",0,0,'C');
		$this->Cell(68,5,"SSS ID No.: ",'B','1','L');
		$this->Ln(15);
		$this->MultiCellTag(155,5,"known to me as the same persons who executed the foregoing Undertaking and he/she acknowledge before me that the same is his/her free voluntary act and deed and of the corporation represented.",0,"J",0);
		$this->Ln(5);
		$this->MultiCellTag(155,5,"\tWitness my hand and seal.",0,"J",0);
		$this->Ln(5);
		$this->Cell(155,5,"Doc. No. _______",0,'1','L');
		$this->Cell(155,5,"Page No. _______",0,'1','L');
		$this->Cell(155,5,"Book No._______",0,'1','L');
		$this->MultiCellTag(155,5,"Series of $this->year",0,'1','L');
		
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
	$pdf->status = $_GET['status'];
	$pdf->sss = $_GET['sss'];
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->displayContent();		
	$pdf->Output('Undertaking Report.pdf','D');

?>
