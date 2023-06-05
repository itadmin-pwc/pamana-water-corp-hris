<?php
session_start();
ini_set("max_execution_time","0");

include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
include("../../../includes/pdf/fpdf.php");
class PDF extends FPDF
{
	var $arrPayPd;
	var $arrBasic;
	var $arrPD;
	var $netPay;
	var $hist;
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
	function Head($empInfo) {
			$this->SetFont('Arial','B',11);
			switch($_SESSION['company_code']) {
				case 1: 
					$compName = "PUREGOLD JUNIOR SUPERMARKET, INC.";
				break;	
				case 2:
					$compName = "PUREGOLD PRICE CLUB, INC.";
				break;
				case 4:
					$compName = "PUREGOLD DUTY FREE  CLARK INC.";
				break;
				case 5:
					$compName = "PUREGOLD DUTY FREE SUBIC INC.";
				break;
				case 7:
					$compName 	= "GANT DIAMOND CORPORATION";
				break;	
				case 8:
					$compName 	= "GANT DIAMOND III CORPORATION";
				break;
				case 9:
					$compName 	= "SUPER RETAIL XV CORPORATION";
				break;
				case 10:
					$compName 	= "SUPER AGORA X CORPORATION";
				break;	
				case 11:
					$compName 	= "SUPER RETAIL VII CORPORATION";
				break;	
				case 12:
					$compName 	= "S-CV CORPORATION";
				break;
				case 15:
					$compName 	= "COMPANY E CORPORATION";
				break;
			}
			$this->Cell(190,8,$compName,'LTR',0,'C');
			$this->Ln();
			$this->SetFont('Arial','B',9);
			$this->Cell(190,5,'Request for Payment','LR',1,'C');
			$this->SetFont('Arial','',7);
			$this->Cell(190,5,$empInfo['brnShortDesc'],'LBR',1,'C');
			$this->PayData($empInfo);
	}

	function PayData($arrempNo) {
		$this->SetFont('Arial','B',9);
		$this->Cell(13,8,'Payee:','LTB',0);
		$this->SetFont('Arial','',9);
		$this->Cell(177,8,ucwords($arrempNo['empLastName'] . ", " . $arrempNo['empFirstName'] . " " . $arrempNo['empMidName']),'TBR',1,'L');
		$this->SetFont('Arial','B',9);
		$this->Cell(28,8,'Mode of Payment:','L',0);
		$this->SetFont('Arial','',9);
		$this->Cell(67,8,' Check','R',0);
		$this->SetFont('Arial','B',8);
		$this->Cell(30,8,'Type of Transaction:',0,0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(30,8,' Last Pay',0,0,'');
		$this->SetFont('Arial','B',8);
		$this->Cell(15,8,'Due Date:','',0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(20,8,date('m/d/Y',strtotime($arrempNo['dateResigned'])),'R',1,'L');
		$this->SetFont('Arial','B',8);
		$this->Cell(95,8,'Earnings',1,0,'C');
		$this->Cell(95,8,'Deductions',1,1,'C');
		
		$totEarn = 0;
		$totDed = 0;
		$arrEarn = $this->Earnings($arrempNo['empNo']);
		$arrDed = $this->Deductions($arrempNo['empNo']);
		
				
		
if($arrempNo['empNo'] == '230001169')
{
	$this->SetFont('Arial','',6);
	for ($i=0;$i<14;$i++) {
			if (!empty($arrEarn[$i]['trnShortDesc'])) {
				$this->Cell(55,3,'   '.strtoupper($arrEarn[$i]['trnShortDesc']),'L',0);
				$this->Cell(40,3,number_format($arrEarn[$i]['trnAmountE'],2).'   ','R',0,'R');
				$totEarn += $arrEarn[$i]['trnAmountE'];
			} else {
				$this->Cell(55,3,'','L',0);
				$this->Cell(40,3,'','R',0,'R');
			}
			if (!empty($arrDed[$i]['trnShortDesc'])) {
				$this->Cell(55,3,'   '.strtoupper($arrDed[$i]['trnShortDesc']),'',0);
				$this->Cell(40,3,number_format($arrDed[$i]['trnAmountD'],2).'   ','R',1,'R');
				$totDed += $arrDed[$i]['trnAmountD'];			
			} else {
				$this->Cell(55,3,'',0,0);
				$this->Cell(40,3,'','R',1,'R');
			}

		}
}
else
{
	$this->SetFont('Arial','',7);
	for ($i=0;$i<14;$i++) {
			if (!empty($arrEarn[$i]['trnShortDesc'])) {
				$this->Cell(55,3,'   '.strtoupper($arrEarn[$i]['trnShortDesc']),'L',0);
				$this->Cell(40,3,number_format($arrEarn[$i]['trnAmountE'],2).'   ','R',0,'R');
				$totEarn += $arrEarn[$i]['trnAmountE'];
			} else {
				$this->Cell(55,3,'','L',0);
				$this->Cell(40,3,'','R',0,'R');
			}
			if (!empty($arrDed[$i]['trnShortDesc'])) {
				$this->Cell(55,3,'   '.strtoupper($arrDed[$i]['trnShortDesc']),'',0);
				$this->Cell(40,3,number_format($arrDed[$i]['trnAmountD'],2).'   ','R',1,'R');
				$totDed += $arrDed[$i]['trnAmountD'];			
			} else {
				$this->Cell(55,3,'',0,0);
				$this->Cell(40,3,'','R',1,'R');
			}

		}
}
		
		
		
		

		$this->SetFont('Arial','B',8);
		$this->Cell(55,8,'Total Earnings:','LT',0);
		$this->SetFont('Arial','',8);
		$this->Cell(40,8,number_format($totEarn,2),'TR',0,'R');
		$this->SetFont('Arial','B',8);
		$this->Cell(55,8,'Total Deducations:','LT',0);
		$this->SetFont('Arial','',8);
		$this->Cell(40,8,number_format($totDed,2),'TR',1,'R');
		$tamount=sprintf("%.2f", $totEarn-$totDed);
		$len=strpos($tamount,".");	
		if (!empty($len)) {
				$awords=ucwords($this->convertNum($tamount)) . " Pesos and " . ucwords($this->convertNum((int)substr($tamount,strlen($tamount)-2,2))) . " centavos only";
		} else {
				$awords=ucwords($this->convertNum((int)$tamount)) . " Pesos only";
		}
		$this->SetFont('Arial','B',8);
		$this->Cell(12,8,"Amount: ",'LBT',0,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(143,8,"$awords",'BTR',0,'L');
		$this->SetFont('Arial','B',8);
		$this->Cell(35,8,'Php '.number_format($totEarn-$totDed,2),1,1,'L');
		$arruser = $this->UserPrinted();
		$this->Cell(95,5,'Requested by:','LTR',0,'L');
		$this->Cell(95,5,'Approved by:','LTR',1,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(95,5,''.$arruser['empFirstName'] . " " . substr($arruser['empMidName'],0,1).". " . $arruser['empLastName'],'LBR',0,'C');
		$this->Cell(95,5,PAYROLLDEPT_SIGNATORY,'LBR',1,'C');

		//$this->Cell(80,5,$arruser['empFirstName'] . " " . substr($arruser['empMidName'],0,1).". " . $arruser['empLastName'],0,0,'L');
		$this->Cell(8,5,'',0,0,'C');
		//$this->Cell(20,5,'GEMA I. MELENDREZ ',0,0,'L');
		$this->Ln(8);
	}
	function Header() {
	
	}
	function UserPrinted() {
		$sql="Select empLastName,empFirstName,empMidName from tblEmpmast where empNo='{$_SESSION['employee_number']}'";
		return  $this->getSqlAssoc($this->execQry($sql));
	}
	function Earnings($empNo) {
		 $qryEarnings = "SELECT tblPayTransType.trnShortDesc, tblEarnings{$this->hist}.trnAmountE FROM tblEarnings{$this->hist} INNER JOIN tblPayTransType ON tblEarnings{$this->hist}.compCode = tblPayTransType.compCode AND tblEarnings{$this->hist}.trnCode = tblPayTransType.trnCode where empNo='$empNo' and tblEarnings{$this->hist}.compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $this->arrPayPd['pdYear']. "' and pdNumber='" . $this->arrPayPd['pdNumber'] . "'";
		return  $this->getArrRes($this->execQry($qryEarnings));
	}
	function Deductions($empNo) {
		$qryEarnings = "SELECT tblPayTransType.trnShortDesc, tblDeductions{$this->hist}.trnAmountD FROM tblDeductions{$this->hist} INNER JOIN tblPayTransType ON tblDeductions{$this->hist}.compCode = tblPayTransType.compCode AND tblDeductions{$this->hist}.trnCode = tblPayTransType.trnCode where empNo='$empNo' and tblDeductions{$this->hist}.compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $this->arrPayPd['pdYear']. "' and pdNumber='" . $this->arrPayPd['pdNumber'] . "'";
		return  $this->getArrRes($this->execQry($qryEarnings));
	}
	
}

$pdf=new PDF();
$pdf->FPDF($orientation='P',$unit='mm',$format='payslip');
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();
//Column titles
//Data loading
$empNo		= $_GET['empNo'];
$empName	= $_GET['empName'];
$empDiv		= $_GET['empDiv'];
$empDept	= $_GET['empDept'];
$empSect 	= $_GET['empSect'];
$branch		= $_GET['branch'];
$loc		= $_GET['loc'];
$groupType 	= $_SESSION['pay_group'];
if ($groupType==1) $groupName = "GROUP 1"; else $groupName = "GROUP 2"; 
$orderBy 	= $_GET['orderBy'];
$catType	= $_SESSION['pay_category'];
$catName 	= $psObj->getEmpCatArt($sessionVars['compCode'], $catType);
$payPd 		= $_GET['payPd'];
$arrPayPd 	= $psObj->getSlctdPd($compCode,$payPd);
$pdf->arrPayPd = $arrPayPd;
if (!$pdf->getPeriod($payPd)) {
	$pdf->hist = "hist";
}

if ($empNo>"") {
	$empNo1 = " AND (tblEmpMast.empNo LIKE '{$empNo}%')";
} else {
	$empNo1 = "";
	if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
}
if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
if ($groupType<3) {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}

if (!$pdf->getPeriod($payPd)) {
	$pdf->hist = "hist";
}
if ($branch != 0 && $empNo=="") {
	if ($loc == 1) {
		$branch = " AND empBrnCode = '$branch' AND empLocCode='0001'";
	} elseif ($loc == 2) {
		$branch = " AND empBrnCode = '$branch' AND empLocCode='$branch'";
	} else {
		$branch = " AND empBrnCode = '$branch'";
	}
} else {
	$branch = "";
}

 $qryIntMaxRec = "SELECT empNo,empLastName,empFirstName,empMidName,dateResigned,brnShortDesc
				 FROM tblEmpMast INNER JOIN tblBranch ON
				 tblEmpMast.compCode = tblBranch.compCode AND  tblEmpMast.empBrnCode = tblBranch.brnCode
			     WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}' 
				 
			     AND tblEmpMast.empNo  IN (Select empNo from  tblLastPayEmp where compCode='{$_SESSION['company_code']}' and pdYear='" . $arrPayPd['pdYear']. "' and pdNumber='" . $arrPayPd['pdNumber'] . "')
				 $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $branch
				  order by empBrnCode,empLastName ";
$resEmpList = $psObj->execQry($qryIntMaxRec);
$arrEmpList = $psObj->getArrRes($resEmpList);
foreach($arrEmpList as $empInfo) {
	$pdf->AddPage();
	$pdf->Head($empInfo);
}	
$userId= $psObj->getSeesionVars();
$psObj->getUserHeaderInfo($userId['empNo'],$_SESSION['employee_id']); 
$pdf->Output('lastpay.pdf','D');
?>
