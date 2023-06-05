<?

################### INCLUDE FILE #################
session_start();
if ($_SESSION['company_code'] == 3)
	 header("Location: authDebit_lusi_pdf.php?payPd={$_GET['payPd']}");

	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("bank_remit.obj.php");
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
	var $netsallary;
	var $arrPd;
	var $ones = array(
				 "",
				 " one",
				 " two",
				 " three",
				 " four",
				 " five",
				 " six",
				 " seven",
				 " eight",
				 " nine",
				 " ten",
				 " eleven",
				 " twelve",
				 " thirteen",
				 " fourteen",
				 " fifteen",
				 " sixteen",
				 " seventeen",
				 " eighteen",
				 " nineteen"
				);
				
		var $tens = array(
				 "",
				 "",
				 " twenty",
				 " thirty",
				 " forty",
				 " fifty",
				 " sixty",
				 " seventy",
				 " eighty",
				 " ninety"
				);
				
			var $triplets = array(
				 "",
				 " thousand",
				 " million",
				 " billion",
				 " trillion",
				 " quadrillion",
				 " quintillion",
				 " sextillion",
				 " septillion",
				 " octillion",
				 " nonillion"
				);
		function convertTri($num, $tri) {
		  $r = (int) ($num / 1000);
		  $x = ($num / 100) % 10;
		  $y = $num % 100;
		
		  $str = "";
		
		  if ($x > 0)
		   $str = $this->ones[$x] . " hundred";
		
		  if ($y < 20)
		   $str .= $this->ones[$y];
		  else
		   $str .= $this->tens[(int) ($y / 10)] . $this->ones[$y % 10];
		
		  if ($str != "")
		   $str .= $this->triplets[$tri];
		
		  if ($r > 0)
		   return $this->convertTri($r, $tri+1).$str;
		  else
		   return $str;
		}
		
		function convertNum($num) {
		 $num = (int) $num;    
		
		 if ($num < 0)
		  return "negative".$this->convertTri(-$num, 0);
		
		 if ($num == 0)
		  return "zero";
		
		 return $this->convertTri($num, 0);
		}	
	function Content() {
		
		$this->SetStyle("t1","arial","B",12,0);
		$this->SetStyle("t2","arial","BI",12,0);
		
		$this->Image('../../../images/authority_debit.png',67,10,100,20);
		$this->Ln(20);
		$empPos = $EmpInfo['posShortDesc'];
		$this->SetFont('ARIAL', 'B', '14');
		$this->SetMargins(20,0,15);
		$this->Cell(200,6,"AUTHORITY TO DEBIT",0,0,"C");
		$this->Ln(13);
		$this->SetFont('ARIAL', '', '11');
		$this->Cell(140,6,"DATE:",0,0,"L");
		$this->Ln(13);
		$this->SetFont('ARIAL', 'B', '11');
		$this->Cell(200,6,"BDO Unibank Inc",0,1,"L");
		$this->Cell(200,6,"Clark SEZ-Centennial Branch",0,0,"L");		
		$this->Ln(13);
		$this->SetFont('Arial', '', '11');
		$this->Cell(200,5,"ATTENTION: Ricarte R Datu",0,1,"L");
		$this->Cell(200,5,"                      Branch Head",0,1,"L");
		$this->Ln(13);
		$tamount=sprintf("%.2f", $this->netsallary);
		$len=strpos($tamount,".");	
		if (!empty($len)) {
				$awords=ucwords($this->convertNum($tamount)) . " Pesos and " . ucwords($this->convertNum((int)substr($tamount,strlen($tamount)-2,2))) . " centavos only";
		} else {
				$awords=ucwords($this->convertNum((int)$tamount)) . " Pesos only";
		}
		$cutOff = $this->getCutOffPeriod($this->arrPd['pdNumber']);
		if ($this->arrPd['pdNumber']==25) {
			$date = date('12/1/'.(date('Y')-1). '- 11/30/Y');
		} else {
			if ($_SESSION['pay_group'] == 2) {
				if ($cutOff==2) {
					if ($this->arrPd['pdNumber'] < 24)
						$date = date('m/15/Y - m/',strtotime($this->arrPd['pdPayable'])).date("d/", strtotime('-1 second', strtotime('+1 month', strtotime(date("Y-m-01",strtotime($PayPeriod['pdPayable'])))))).date('Y',strtotime($this->arrPd['pdPayable']));     
					else
						$date = date('m/15/Y - m/',strtotime($this->arrPd['pdPayable'])).date("d/", strtotime('-1 second', strtotime('+1 month', strtotime(date("Y-m-01",strtotime($PayPeriod['pdPayable'])))))).date('Y');     
					
				} else {
					$date = date('m/1/Y - m/15/Y',strtotime($this->arrPd['pdPayable']));
				}
			} else {
				if ($cutOff==2) {
					$date = date('m/11/Y - m/25/Y',strtotime($this->arrPd['pdPayable']));
				} else { 				
						$date = date('m/26/Y',strtotime($this->arrPd['pdFrmDate']))."-".(date('m',strtotime($this->arrPd['pdFrmDate']))+1).date('/10/Y',strtotime($this->arrPd['pdFrmDate']));
				}
			}
		}
		$this->MultiCellTag(170,6,"This is to authorize Banco de Oro Universal Bank (\"Bank\") to debit <t1>Puregold Duty Free, Inc.</t1> CA/SA No. __________________________ with the Bank's Clark SEZ -Centennial Branch for the amount of PESOS: ". strtoupper($awords). " (Php".number_format($this->netsallary,2).") corresponding to the Payroll File sent on _______________ . This said payroll file period from $date details as per attached Payroll Prooflist transmitted electronically shall be effected on __________________. 

This authority to debit is issued pursuant to and subject to the terms and conditions of the Company's Payroll Service Agreement with the Bank. 
				 
Very truly yours,           
                                       
<t2>Authorized Company Signatory</t2>
___________________________
												   
												   
<t2>Authorized Company Signatory</t2>
___________________________
				 ",0,"J",0,true);
$this->Cell(200,6,"(Nothing follows) ",0,0,"L");				 
		
	}

	function getCutOffPeriod($pdNumber){

		if((int)trim((int)trim($pdNumber))%2){
			return  1;
		}
		else{
			return 2;
		}	
	}

}	
$bankRemitObj = new bankRemitObj($_SESSION,$_GET);
$type = $_GET['type'];
$pdf=new PDF();
$pdf->Open();
$pdf->FPDF($orientation='P',$unit='mm',$format='LETTER');	

$payPdSlctd = $bankRemitObj->getPayPeriod($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdPayable = '{$_GET['payPd']}'");
if ($payPdSlctd['pdStat']=="C") {
	$hist = "hist";
}
$qry = " Select sum(netSalary+sprtAllow) AS netSalary from tblPayrollSummary$hist where payGrp = '{$_SESSION['pay_group']}' and pdYear='{$payPdSlctd['pdYear']}' and pdNumber = '{$payPdSlctd['pdNumber']}' and empBnkCd=4 and payCat IN (2,3) and compCode='{$_SESSION['company_code']}' and empbrncode<>0001";
$arr = $bankRemitObj->getSqlAssoc($bankRemitObj->execQry($qry));

$pdf->netsallary = $arr['netSalary'];
$pdf->arrPd = $payPdSlctd;
$pdf->AddPage();	
$pdf->Content();
$pdf->SetMargins(20,100,10);
$pdf->Output('authoDebit.pdf','D');



?>
