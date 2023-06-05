<?
################### INCLUDE FILE #################
	session_start();
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
	function Content($arr) {
		
		$this->SetStyle("t1","arial","B",12,0);
		$this->SetStyle("t2","arial","BI",12,0);
		

		$this->Ln(10);
		$empPos = $EmpInfo['posShortDesc'];
		$this->SetFont('ARIAL', 'B', '14');
		$this->SetMargins(20,0,15);
		$this->Cell(200,8,"AUTHORITY TO DEBIT",0,0,"C");
		$this->Ln(13);
		$this->SetFont('ARIAL', '', '11');
		$this->Cell(140,8,"DATE:",0,1,"L");
		$this->Ln();
		$this->SetFont('ARIAL', 'B', '11');
		$this->Cell(200,8,"ASIA UNITED BANK",0,1,"L");
		$this->Cell(200,8,"Joy Nostalg Branch",0,1,"L");
		$this->SetFont('Arial', '', '11');
		$this->Cell(200,8,"ATTENTION:",0,1,"L");
		$this->Cell(200,8,"BRANCH HEAD",0,1,"L");
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
		$date = str_replace("-"," to ",$date);
		$this->MultiCellTag(170,8,"This is to authorize Asia United Bank,  to debit Lusitano Inc. CA/SA No._________________________ for the amount of PESOS: ". strtoupper($awords). ". (<t1>".number_format($this->netsallary,2)."</t1>) and credit to the following account.",0,"J",0,true);
			$ctr = 1;
			$tot = 0;
			$this->Ln(13);
			foreach($arr as $val)		  {
				$this->Cell(50,5,"$ctr. {$val['empAcctNo']}",0,0,"L");
				$this->Cell(50,5,number_format($val['netSalary'],2),0,1,"R");
				$tot += round($val['netSalary'],2);
				$ctr++;
			}
				$this->Cell(50,5,"   TOTAL",0,0,"L");
				$this->Cell(50,5,number_format($tot,2),0,1,"R");
				$this->Ln(20);
				$this->Cell(50,5,"Very truly yours,",0,0,"L");
				$this->Ln(13);
				$this->Cell(50,5,"Authorized  Signatories","T",0,"C");
		
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
$qry = " Select empAcctNo,sum(netSalary+sprtAllow) AS netSalary from tblPayrollSummary$hist pay inner join tblEmpMast emp on pay.empNo=emp.empNo where payGrp = '{$_SESSION['pay_group']}' and pdYear='{$payPdSlctd['pdYear']}' and pdNumber = '{$payPdSlctd['pdNumber']}' and empBnkCd=2 and payCat IN (2,3) and pay.compCode='{$_SESSION['company_code']}' group by empAcctNo";
$qryTot = " Select sum(netSalary+sprtAllow) AS netSalary from tblPayrollSummary$hist pay inner join tblEmpMast emp on pay.empNo=emp.empNo where payGrp = '{$_SESSION['pay_group']}' and pdYear='{$payPdSlctd['pdYear']}' and pdNumber = '{$payPdSlctd['pdNumber']}' and empBnkCd=2 and payCat IN (2,3) and pay.compCode='{$_SESSION['company_code']}'";

$arrTot = $bankRemitObj->getSqlAssoc($bankRemitObj->execQry($qryTot));
$arr = $bankRemitObj->getArrRes($bankRemitObj->execQry($qry));
$pdf->netsallary=$arrTot['netSalary'];

$pdf->arrPd = $payPdSlctd;
$pdf->AddPage();	
$pdf->Content($arr);
$pdf->SetMargins(20,100,10);
$pdf->Output('authoDebit.pdf','D');



?>
