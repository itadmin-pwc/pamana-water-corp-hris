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
	var $col=0;
	//Ordinate of column start
	var $y=0;
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
	function Content($tamount,$arrData,$arrPd) {
		
		
		$this->SetStyle("p","times","",11,"130,0,30");
		$this->SetStyle("pb","times","B",11,"130,0,30");
		$this->SetStyle("t1","arial","B",10,0);
		$this->SetStyle("t0","arial","BU",10,0);
		$this->SetStyle("t2","arial","",10,0);
		$this->SetStyle("t3","times","B",14,"203,0,48");
		$this->SetStyle("t4","arial","BI",11,"0,151,200");
		$this->SetStyle("hh","times","B",11,"255,189,12");
		$this->SetStyle("ss","arial","",7,"203,0,48");
		$this->SetStyle("font","helvetica","",10,"0,0,255");
		$this->SetStyle("style","helvetica","BI",10,"0,0,220");
		$this->SetStyle("size","times","BI",13,"0,0,120");
		$this->SetStyle("color","times","BI",13,"0,255,255");
		switch($_SESSION['company_code']) {
			case 7:
				$compName 	= "GANT DIAMOND CORPORATION";
				$bnkAcct 	= "2070430815";
			break;	
			case 8:
				$compName 	= "GANT DIAMOND III CORPORATION";
				//$bnkAcct 	= "2070430310";
				$bnkAcct 	= "2070438859";
			break;
			case 9:
				$compName 	= "SUPER RETAIL XV CORPORATION";
				$bnkAcct 	= "";
			break;
			case 10:
				$compName 	= "SUPER AGORA X CORPORATION";
				//$bnkAcct 	= "2070430116";
				$bnkAcct 	= "2070288834";
			break;	
			case 11:
				$compName 	= "SUPER RETAIL VIII CORPORATION";
				//$bnkAcct 	= "2070430213";
				$bnkAcct 	= "2070288889";
			break;	
			case 12:
				$compName 	= "S-CV CORPORATION";
				$bnkAcct 	= "";
			break;
			default:
				$compName 	= "PUREGOLD";
				$bnkAcct 	= "XXXX";
			break;	
		}
		$AmtWords = strtoupper($this->convertNum($tamount)) . " AND " . (int)substr(number_format($tamount,2),strlen(number_format($tamount,2))-2,2) . "/100";
		$pdFrom = date("F d, Y",strtotime($arrPd['pdFrmDate']));
		$pdTo = date("F d, Y",strtotime($arrPd['pdToDate']));
		
		$this->SetFont('Arial', '', '10');
		$this->Cell(100,8,date("F d, Y"),0,0);
		$this->Ln(10);
		$this->MultiCellTag(170,5,"<t2>Attention:</t2><t1>MS. GLADYS SANTOS</t1>",0,"J",0,true);
		$this->MultiCellTag(170,5,"               <t1>Chinabanking Corporation</t1>",0,"J",0,true);
		$this->Ln();
		$this->MultiCellTag(190,5,"This is to authorize your bank to debit the amount of <t1>PESOS$AmtWords ONLY (Php ".number_format($tamount,2).")</t1> under the account no. <t1>_________________</t1> of <t1>$compName</t1> on <t0>_______________</t0>  and to be credited for amounts indicated opposite their account numbers representing payroll period <t1>$pdFrom</t1> to <t1>$pdTo</t1>",0,"J",0,true);
		$this->Ln(10);
		$this->SetFont('Arial', 'B', '10');
		$this->Cell(70,8,"Employee Name",0,0,"L");
		$this->Cell(60,8,"Bank Account Number",0,0,"C");
		$this->Cell(60,8,"Amount",0,1,"R");
		$this->SetFont('Arial', '', '10');
		$ctr = 1;
		$GAmtTotal = 0;
		foreach($arrData as $val) {
			if ((float)$val['netSalary']>0) {
				$this->Cell(70,5,"$ctr. ".$val['lname'] . ", " . $val['fname'] . " " .$val['mname'][0].".",0,0);
				$this->Cell(60,5,$val['empAcctNo'],0,0,"C");
				$this->Cell(60,5,number_format($val['netSalary'],2),0,1,"R");			
				$ctr++;
				$GAmtTotal = $GAmtTotal+$val['netSalary'];
			}
		}
		$this->SetFont('Arial', 'B', '10');
		$this->Cell(70,5,"",0,0);
		$this->Cell(60,5,"TOTAL",0,0,"C");
		$this->Cell(60,5,number_format($GAmtTotal,2),0,1,"R");
		$this->SetFont('Arial', '', '10');
		$this->Ln(5);
		$this->MultiCellTag(190,5,"Please make sure that no copy of this bank advise shall be given to any company personnel without the express permission of the undersigned.",0,"J",0,true);
		$this->Ln(10);
/*		$this->Cell(130,5,"",0,0);
		$this->Cell(60,5,"PREPARED BY:",0,1,"L");
		$this->Ln(5);
		$this->Cell(130,5,"",0,0);
		$this->Cell(60,5,"____________________________",0,1,"R");
		$this->Cell(118,5,"",0,0);
		$this->Cell(60,5,"",0,1,"R");
		$this->Ln(5);*/
		$this->Cell(130,5,"",0,0);
		$this->Cell(60,5,"NOTED BY:",0,1,"L");
		$this->Ln(5);
		$this->Cell(130,5,"",0,0);
		$this->Cell(60,5,"____________________________",0,1,"R");
		$this->Cell(147,5,"",0,0);
		$this->Cell(120,5,"Account Signatory",0,0);
		$this->Cell(60,5,"",0,1,"R");
		$this->Cell(115,5,"",0,0);
		$this->Cell(60,5,"",0,1,"R");
	}
	function Header() {
		$this->Image("../../../images/parco_header.JPG", 10, 0 , '190' , '38' , 'JPG', '');
		$this->Ln(30);
	}
}	
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$arrPayPeriod = $inqTSObj->getPeriodWil($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category'],"AND pdPayable = '".$inqTSObj->dateFormat($_GET['payPd'])."'");
if ($arrPayPeriod['pdStat']=="C") {
	$hist = "hist";
}
$qryGetPaySummry = "SELECT ps.compCode, ps.empNo, ps.netSalary+ps.sprtAllow as netSalary, Replace(emp.empAcctNo,'-','') as empAcctNo,emp.empLastName as lname, emp.empFirstName AS fname, emp.empMidName AS mname, brn.brnLoc, brn.coCtr
					FROM tblPayrollSummary$hist ps LEFT OUTER JOIN tblEmpMast emp 
					ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo 
					LEFT OUTER JOIN tblBranch brn ON ps.compCode = brn.compCode AND brn.brnCode = ps.empBrnCode
					WHERE ps.compCode = '{$_SESSION['company_code']}'
					AND (ps.payGrp    = '{$_SESSION['pay_group']}') 
					AND (ps.pdYear    = '{$arrPayPeriod['pdYear']}') 
					AND (ps.pdNumber  = '{$arrPayPeriod['pdNumber']}') 
					AND (ps.empBnkCd  = '7')
					and payCat<>9 order by empLastName,empfirstName,empMidName ";
$resGetPaySummry = $inqTSObj->execQry($qryGetPaySummry);
$arrData = $inqTSObj->getArrRes($resGetPaySummry);

$totalAmt = $inqTSObj->getCompTotalCBC($arrPayPeriod['pdYear'],$arrPayPeriod['pdNumber'],$hist);
$type = $_GET['type'];
$pdf= new PDF();
$pdf->Open();
$pdf->FPDF($orientation='P',$unit='mm',$format='LETTER');	
$pdf->AddPage();	
$pdf->Content($totalAmt,$arrData,$arrPayPeriod);
$pdf->SetMargins(20,0,10);
$pdf->Output('cbc.pdf','D');



?>
