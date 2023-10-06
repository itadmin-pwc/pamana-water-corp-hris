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
	var $user;
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
		$this->SetStyle("color","times","BI",13,"255,0,0");
		$this->Image('../../../images/employee_transfer.jpg',2,0,214,280);
		$this->Image('../../../images/advances.jpg',17.5,182,33,4);
		
		if($_SESSION['company_code']==1){
			$this->Image('../../../images/OWI-LOGO.jpg',10,33,30,15);	
		}
		if($_SESSION['company_code']==2){
			$this->Image('../../../images/ppci_logo.jpg',10,33,50,15);	
		}
		if($_SESSION['company_code']==4 || $_SESSION['company_code']==5){
			$this->Image('../../../images/duty_free_logo.jpg',10,33,50,15);	
		}
		if($_SESSION['company_code']==7 || $_SESSION['company_code']==8 || $_SESSION['company_code']==9 || $_SESSION['company_code']==10 || $_SESSION['company_code']==11 || $_SESSION['company_code']==12 || $_SESSION['company_code']==13){
			$this->Image('../../../images/parco_logo.jpg',10,33,50,15);	
		}

		$this->SetFont('Courier', '', '8');
		$this->Ln(48);
		$this->Cell(51,6,"",0,0,'');
		$this->MultiCellTag(90,6,base64_decode($_GET['name']),0,1,'L');
		$this->Cell(51,6,"",0,0,'');
		$this->Cell(90,6,base64_decode($_GET['position']),0,1,'L');
		$this->Cell(51,6,"",0,0,'');
		$this->Cell(90,6,base64_decode($_GET['department']),0,1,'L');
		$this->Cell(51,6,"",0,0,'');
		$this->Cell(90,6,base64_decode($_GET['datehired']),0,1,'L');
		$this->Cell(51,6,"",0,0,'');
		$this->Cell(90,6,base64_decode($_GET['empstatus']),0,1,'L');
		$this->Cell(51,6,"",0,0,'');
		$this->Cell(90,6,base64_decode($_GET['branch']),0,1,'L');
		$this->Cell(51,6,"",0,0,'');
		$this->Cell(90,6,base64_decode($_GET['reclocation']),0,1,'L');
		$this->Ln(15);
		$this->Cell(51,5,"",0,0,'');
		$this->Cell(80,5,(base64_decode($_GET['tempbranch'])=="0"?"":base64_decode($_GET['tempbranch'])),0,0,'L');
		$this->Cell(90,5,base64_decode($_GET['frmdate']),0,1,'L');
		$this->Cell(175,1,"",0,0,'');
		$this->Cell(50,1,base64_decode($_GET['lperiod']),0,1,'L');
		$this->Cell(51,5,"",0,0,'');
		$this->Cell(80,5,(base64_decode($_GET['temprecloc'])=="0"?"":base64_decode($_GET['temprecloc'])),0,0,'L');
		$this->Cell(90,5,base64_decode($_GET['todate']),0,1,'L');
		$this->Ln(8);
		$this->Cell(51,6,"",0,0,'');
		$this->Cell(93,6,(base64_decode($_GET['nature'])=="0"?"":base64_decode($_GET['nature'])),0,0,'L');
		$this->Cell(90,6,(base64_decode($_GET['company'])=="0"?"":base64_decode($_GET['company'])),0,1,'L');
		$this->Cell(51,6,"",0,0,'');
		$this->Cell(93,6,(base64_decode($_GET['permbranch'])=="0"?"":base64_decode($_GET['permbranch'])),0,0,'L');
		$this->Cell(90,6,(base64_decode($_GET['permdepartment'])=="0"?"":base64_decode($_GET['permdepartment'])),0,1,'L');
		$this->Cell(80,7,"",0,0,'');
		$this->Cell(93,7,base64_decode($_GET['effectivity']),0,1,'L');
		$this->Ln(13);
		$frmpos = strlen(base64_decode($_GET['frmposition']));
		$topos =  strlen(base64_decode($_GET['toposition']));
		if($frmpos>=28){
			$this->Cell(100,3,"",0,0,'');
			$current_y = $this->GetY();
			$current_x = $this->GetX();
			
			$cell_width = 50;
			$this->MultiCell($cell_width, 3, (base64_decode($_GET['frmposition'])=="0"?"":base64_decode($_GET['frmposition'])),0,'T', false,'T');
			
			$this->SetXY($current_x + $cell_width, $current_y);			
		}
		else{
			$this->Cell(100,6,"",0,0,'');
			$this->Cell(50,6,(base64_decode($_GET['frmposition'])=="0"?"":base64_decode($_GET['frmposition'])),0,0,'L');	
		}
		if($topos>=28){
			$this->MultiCellTag(50,3,(base64_decode($_GET['toposition'])=="0"?"":base64_decode($_GET['toposition'])),0,1,'L');	
		}
		else{
			$this->Cell(93,6,(base64_decode($_GET['toposition'])=="0"?"":base64_decode($_GET['toposition'])),0,1,'L');	
		}
		
		$this->Ln(4);
		$this->Cell(100,6,"",0,0,'');
		$this->Cell(50,6,base64_decode($_GET['frmsalary']),0,0,'L');
		$this->Cell(50,6,base64_decode($_GET['tosalary']),0,1,'L');
		$this->Cell(100,6,"",0,0,'');
		$this->Cell(50,5,base64_decode($_GET['frmecola']),0,0,'L');
		$this->Cell(50,5,base64_decode($_GET['toecola']),0,1,'L');
		$this->Cell(100,5,"",0,0,'');
		$this->Cell(60,5,(base64_decode($_GET['reasontype'])=="0"?"":base64_decode($_GET['reasontype'])),0,0,'L');
		$this->Cell(50,5,base64_decode($_GET['prf']),0,0,'L');
		$this->Ln(8);
		$this->Cell(2,3,"",0,0,'');
		$this->MultiCellTag(193,3,base64_decode($_GET['reason']),0,0,'L');		
	}
	
	function Footer()
	{
		
		// Go to 1.5 cm from bottom
		$this->SetY(-52);
		//$this->SetLeftMargin(-30);
		$this->setX(10);
		// Select Arial italic 8
		$this->SetFont('Courier', '', '8');
		$this->Cell(30,10,substr($this->user['empFirstName'],0,1).". ".$this->user['empLastName'],0,0,'C');
		$this->Cell(30,10,"",0,0,'C');
		$this->Cell(40,10,"",0,0,'C');
		$this->Cell(27,10,"",0,0,'C'); //HR Manager
		$this->Cell(32,10,"",0,0,'C'); //President
		$this->Cell(35,10,substr(base64_decode($_GET['fname']),0,1).". ".base64_decode($_GET['lname']),0,0,'C');
		
	}
}	
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$pdf=new PDF();
$pdf->Open();
$pdf->FPDF($orientation='P',$unit='mm',$format='LETTER');	
$pdf->user = $inqTSObj->getUserInfo($_SESSION['company_code'],$_SESSION['employee_number'],"");
$pdf->AddPage();	
$pdf->Content();
$pdf->SetMargins(20,0,10);
$pdf->Output('TRANSFER EMPLOYEE.pdf','D');
?>
