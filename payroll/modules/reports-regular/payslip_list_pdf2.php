<?php
session_start();
ini_set("max_execution_time","0");

include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
include("../../../includes/pdf/fpdf.php");
class PDF extends FPDF
{
	var $hist;
	function BasicTable($resEmpList)
	{
		$this->AddPage();
		$arrEmpWithAllow = $this->getEmpWithAllow();
		$check=0;
		$ctr_payslipreg = 0;
		$net_payslipreg = 0;
		$ctr_payslipregandAllow = 0;
		$net_payslipregandAllow = 0;
		$ctr_payslipallow = 0;
		foreach ($resEmpList as $ArrEmpList) {
			$this->SetFont('Arial','',7);
			$Department=$this->getDeptDescArt($ArrEmpList['compCode'], $ArrEmpList['empDiv'], $ArrEmpList['empDepCode']);
			$PayPeriod=$this->getPayPeriod($_SESSION['company_code']," and pdSeries = '" . $_GET['payPd']."'");
			if ($_GET['tbl']==2) {
				$Not="Not";
			}
			$Earnwhere="where empNo='" . $ArrEmpList['empNo'] . "'
					and compCode='" . $_SESSION['company_code'] . "' 
					and pdYear='" . $PayPeriod['pdYear']. "'
					and pdNumber='" . $PayPeriod['pdNumber'] . "'
					and trnCode $Not In (Select trnCode from tblAllowType where compCode='" . $_SESSION['company_code'] . "' and sprtPS='Y') ";
			$Dedwhere="where empNo='" . $ArrEmpList['empNo'] . "'
					and compCode='" . $_SESSION['company_code'] . "' 
					and pdYear='" . $PayPeriod['pdYear']. "'
					and pdNumber='" . $PayPeriod['pdNumber'] . "'
					";
			$NetPay=0;
			$SumEarnings=0;
			$SumDeductions=0;
			$SumEarnings=$this->SumEarnDed("tblEarnings{$this->hist}",'trnAmountE',$Earnwhere);
			if ($_GET['tbl']==2) {
				$SumDeductions=$this->SumEarnDed("tblDeductions{$this->hist}",'trnAmountD',$Dedwhere);
				$ctr_payslipreg++;
			} else {
				$ctr_payslipallow++;
			}		
			
			$NetPay=(float)$SumEarnings['totamount'] - (float)$SumDeductions['totamount'];
			$net_payslipreg += $NetPay;
			$this->Cell(30,6,'','LTR');
			$this->SetFont('Arial','B'); 
			$this->Cell(14,6,'COMPANY:','LT');
			$this->SetFont('Arial',''); 
			$this->Cell(41,6,$this->getCompanyName($_SESSION['company_code']),'TR');
			$this->SetFont('Arial','B'); 
			$this->Cell(12,6,'DEPT:' ,'LT');
			$this->SetFont('Arial',''); 
			$this->Cell(43,6,$Department['deptShortDesc'] ,'TR');
			$this->SetFont('Arial','B'); 
			$this->Cell(12,6,'EMP No.:','LT',0);
			$this->SetFont('Arial',''); 
			$this->Cell(38,6,$ArrEmpList['empNo'] . " - P" .$ArrEmpList['empBrnCode'],'TR',0);
			$this->Ln();
			$this->SetFont('Arial','B'); 
			$this->Cell(30,6,'','LR');
			$this->Cell(15,6,'EMPLOYEE:','LT');
			$this->SetFont('Arial',''); 
			$this->Cell(95,6,$ArrEmpList['empLastName'] . ", " . $ArrEmpList['empFirstName'] . " " . $ArrEmpList['empMidName'] ,'T');
			$this->SetFont('Arial','B'); 
			$this->Cell(17,6,'BASIC RATE:','LT',0);
			$this->SetFont('Arial',''); 
			$this->Cell(33,6, 'Php ' .number_format($ArrEmpList['empMrate'],2),'TR',0);
			$this->Ln();
			$this->SetFont('Arial','B','10'); 
			$this->Cell(30,6,'PAYSLIP','LRB','','C');
			$this->SetFont('Arial','B','7'); 
			$this->Cell(17,6,'PAY PERIOD:','LTB');
			$this->SetFont('Arial',''); 
			$this->Cell(38,6,$this->dateFormat($PayPeriod['pdFrmDate']) . " - " . $this->dateFormat($PayPeriod['pdToDate']),'TRB');
			$this->SetFont('Arial','B'); 
			$this->Cell(17,6,'TAX STATUS:','TB');
			$this->SetFont('Arial',''); 
			$this->Cell(38,6,$ArrEmpList['empTeu'],'TRB');
			$this->SetFont('Arial','B'); 
			$this->Cell(13,6,'NET PAY:','LTB',0);
			$this->SetFont('Arial',''); 
			$this->Cell(37,6,'Php ' . number_format($NetPay,2),'TRB',0);
			$this->Ln();
			$this->SetFont('Arial','B'); 
			$this->Cell(70,6,'EARNINGS','LRB','','C');
			$this->Cell(70,6,'DEDUCTIONS','LTB','','C');
			$this->Cell(50,6,'','TRB','','C');
			$this->Ln();
			$this->SetFont('Arial',''); 
			$this->Cell(70,6,'','L','','C');
			$this->Cell(70,6,'','L','','C');
			$this->SetFont('Arial','B',7);
			$this->Cell(50,6,'RECEIPT OF PAY','LR','','C');
			$this->Ln();
			$this->SetFont('Arial','',7);		
			$this->Cell(70,5,'','L','','C');
			$this->Cell(70,5,'','LR','','C');
			$this->SetFont('Arial','',7);
			$this->Cell(50,5,'I acknowledge to have received the amount','TR','','C');
			$this->Ln();
			$this->Cell(70,5,'','L','','C');
			$this->Cell(70,5,'','LR','','C');
			$this->SetFont('Arial','BU',7);
			$this->Cell(17,5,'Php ' .number_format($NetPay,2),'','','L');
			$this->SetFont('Arial','',7);
			$this->Cell(33,5,'and have no further claims ','R','','L');
			$this->Ln();
			$this->Cell(70,5,'','L','','C');
			$this->Cell(70,5,'','LR','','C');
			$this->SetFont('Arial','',7);
			$this->Cell(50,5,'for service rendered.','R','','L');
			$this->Ln();
			$this->Cell(190,20,$this->EarningsAndDeductions($ArrEmpList['empNo'],$PayPeriod));
			$this->Cell(50,2,'','R','','C');
			$this->Ln();

			if (in_array($ArrEmpList['empNo'],$arrEmpWithAllow) && $_GET['tbl']==2) {
			$ctr_payslipregandAllow++;
				$this->SetFont('Arial','',7);				
				$Earnwhere="where empNo='" . $ArrEmpList['empNo'] . "'
						and compCode='" . $_SESSION['company_code'] . "' 
						and pdYear='" . $PayPeriod['pdYear']. "'
						and pdNumber='" . $PayPeriod['pdNumber'] . "'
						and trnCode  In (Select trnCode from tblAllowType where compCode='" . $_SESSION['company_code'] . "' and sprtPS='Y') ";
				$Dedwhere="where empNo='" . $ArrEmpList['empNo'] . "'
						and compCode='" . $_SESSION['company_code'] . "' 
						and pdYear='" . $PayPeriod['pdYear']. "'
						and pdNumber='" . $PayPeriod['pdNumber'] . "'
						";
				$NetPay=0;
				$SumEarnings=0;
				$SumDeductions=0;
				$SumEarnings=$this->SumEarnDed("tblEarnings{$this->hist}",'trnAmountE',$Earnwhere);
				
				$NetPay=(float)$SumEarnings['totamount'] - (float)$SumDeductions['totamount'];
				$net_payslipregandAllow += $NetPay;
				$this->Cell(30,6,'','LTR');
				$this->SetFont('Arial','B'); 
				$this->Cell(14,6,'COMPANY:','LT');
				$this->SetFont('Arial',''); 
				$this->Cell(41,6,$this->getCompanyName($_SESSION['company_code']),'TR');
				$this->SetFont('Arial','B'); 
				$this->Cell(12,6,'DEPT:' ,'LT');
				$this->SetFont('Arial',''); 
				$this->Cell(43,6,$Department['deptShortDesc'] ,'TR');
				$this->SetFont('Arial','B'); 
				$this->Cell(12,6,'EMP No.:','LT',0);
				$this->SetFont('Arial',''); 
				$this->Cell(38,6,$ArrEmpList['empNo'] . " - P" . $ArrEmpList['empBrnCode'],'TR',0);
				$this->Ln();
				$this->SetFont('Arial','B'); 
				$this->Cell(30,6,'','LR');
				$this->Cell(15,6,'EMPLOYEE:','LT');
				$this->SetFont('Arial',''); 
				$this->Cell(95,6,$ArrEmpList['empLastName'] . ", " . $ArrEmpList['empFirstName'] . " " . $ArrEmpList['empMidName'] ,'T');
				$this->SetFont('Arial','B'); 
				$this->Cell(17,6,'BASIC RATE:','LT',0);
				$this->SetFont('Arial',''); 
				$this->Cell(33,6, 'Php ' .number_format($ArrEmpList['empMrate'],2),'TR',0);
				$this->Ln();
				$this->SetFont('Arial','B','10'); 
				$this->Cell(30,6,'PAYSLIP','LRB','','C');
				$this->SetFont('Arial','B','7'); 
				$this->Cell(17,6,'PAY PERIOD:','LTB');
				$this->SetFont('Arial',''); 
				$this->Cell(38,6,$this->dateFormat($PayPeriod['pdFrmDate']) . " - " . $this->dateFormat($PayPeriod['pdToDate']),'TRB');
				$this->SetFont('Arial','B'); 
				$this->Cell(17,6,'TAX STATUS:','TB');
				$this->SetFont('Arial',''); 
				$this->Cell(38,6,$ArrEmpList['empTeu'],'TRB');
				$this->SetFont('Arial','B'); 
				$this->Cell(13,6,'NET PAY:','LTB',0);
				$this->SetFont('Arial',''); 
				$this->Cell(37,6,'Php ' . number_format($NetPay,2),'TRB',0);
				$this->Ln();
				$this->SetFont('Arial','B'); 
				$this->Cell(70,6,'EARNINGS','LRB','','C');
				$this->Cell(70,6,'DEDUCTIONS','LTB','','C');
				$this->Cell(50,6,'','TRB','','C');
				$this->Ln();
				$this->SetFont('Arial',''); 
				$this->Cell(70,6,'','L','','C');
				$this->Cell(70,6,'','L','','C');
				$this->SetFont('Arial','B',7);
				$this->Cell(50,6,'RECEIPT OF PAY','LR','','C');
				$this->Ln();
				$this->SetFont('Arial','',7);		
				$this->Cell(70,5,'','L','','C');
				$this->Cell(70,5,'','LR','','C');
				$this->SetFont('Arial','',7);
				$this->Cell(50,5,'I acknowledge to have received the amount','TR','','C');
				$this->Ln();
				$this->Cell(70,5,'','L','','C');
				$this->Cell(70,5,'','LR','','C');
				$this->SetFont('Arial','BU',7);
				$this->Cell(17,5,'Php ' .number_format($NetPay,2),'','','L');
				$this->SetFont('Arial','',7);
				$this->Cell(33,5,'and have no further claims ','R','','L');
				$this->Ln();
				$this->Cell(70,5,'','L','','C');
				$this->Cell(70,5,'','LR','','C');
				$this->SetFont('Arial','',7);
				$this->Cell(50,5,'for service rendered.','R','','L');
				$this->Ln();
				$this->Cell(190,20,$this->EarningsAndDeductions($ArrEmpList['empNo'],$PayPeriod,'1'));
				$this->Cell(50,2,'','R','','C');
				$this->Ln();			
			}
		}
		if ($_GET['tbl']==2) {
			$this->SetFont('Arial','B',7);
			$this->Cell(52,2,'Number of Pay Slips Printed');
			$this->SetFont('Arial','',7);
			$this->Cell(50,2,': ' .$ctr_payslipreg);
			$this->Ln(5);			
			$this->SetFont('Arial','B',7);
			$this->Cell(52,2,'Number of Pay Slips Printed (Allowance)');
			$this->SetFont('Arial','',7);
			$this->Cell(50,2,': ' .$ctr_payslipregandAllow);
			$this->Ln(5);
			$this->SetFont('Arial','B',7);
			$this->Cell(52,2,'Grand Total (Regular Pay Slip)');
			$this->SetFont('Arial','',7);
			$this->Cell(50,2,': ' . number_format($net_payslipreg,2));
			$this->Ln(5);			
			$this->SetFont('Arial','B',7);
			$this->Cell(52,2,'Grand Total (Allowance Pay Slip)');
			$this->SetFont('Arial','',7);
			$this->Cell(35,2,': ' . number_format($net_payslipregandAllow,2));
			$this->Ln();
		} else {
			$this->SetFont('Arial','B',7);
			$this->Cell(52,2,'Number of Pay Slips Printed (Allowance)' );
			$this->SetFont('Arial','',7);
			$this->Cell(50,2,': ' . $ctr_payslipallow);
			$this->Ln(5);
			$this->SetFont('Arial','B',7);
			$this->Cell(52,2,'Grand Total (Allowance Pay Slip)');
			$this->SetFont('Arial','',7);
			$this->Cell(35,2,': ' . number_format($net_payslipreg,2));
			$this->Ln();
		}	

	}
	
	function EarningsAndDeductions($empNo,$payPd,$act="") {
		$this->SetFont('Arial','',7);
		$arrYTD=$this->getYTD($empNo);
		$ArrempEarnings=$this->getEmpEearnings($empNo,$payPd,$act);
		if ($_GET['tbl']==2 && $act=="") {
			$ArrempDeductions=$this->getEmpDeductions($empNo,$payPd);
		}
		$totearnings = 0;
		$totdeductions=0;
		for ($i=0;$i<16;$i++) {
			$empearn="";
			$totearnings += (float)$ArrempEarnings[$i]['trnAmountE'];
			$totdeductions += (float)$ArrempDeductions[$i]['trnAmountD'];
			if (!empty($ArrempEarnings[$i]['trnAmountE'])) {
				$empearn = number_format($ArrempEarnings[$i]['trnAmountE'],2);
				if ($empearn<0) {
				$empearn = "($empearn)";
				}
			}
			$this->Cell(35,3,'  ' . $ArrempEarnings[$i]['trnDesc'],'L','','L');
			$this->Cell(35,3, $empearn,'R','','R');
			if ($_GET['tbl']==2 && !empty($ArrempDeductions[$i]['trnAmountD']) && $act =="") {
				$this->Cell(35,3,'  ' . $ArrempDeductions[$i]['trnDesc'],'','','L');
				$this->Cell(35,3,number_format($ArrempDeductions[$i]['trnAmountD'],2),'R','','R');
			}
			else {	
				$this->Cell(35,3,'','','','L');
				$this->Cell(35,3,'','R','','R');
			}
			$this->Cell(50,3,'','R');
			$this->Ln();
		}	
		$this->Cell(70,4,'','L','','C');
		$this->Cell(70,4,'','L','','C');
		$this->SetFont('Arial','B'); 
		$this->Cell(10,4,'','L','','L');
		$this->Cell(30,4,'','T','','L');
		$this->Cell(10,4,'','R','','L');
		$this->Ln();
		if ($_GET['tbl']==2 && $act=="") {
			$this->Cell(70,4,'','L','','C');
			$this->Cell(70,4,'','L','','C');
			$this->SetFont('Arial','B'); 
			$this->Cell(18,4,'YTD EARNINGS:','LT','','L');
			$this->SetFont('Arial',''); 
			$this->Cell(32,4,'Php '. number_format((float)$arrYTD['YtdGross'],2),'TR','','R');
			$this->Ln();
			$this->Cell(70,4,'','L','','C');
			$this->Cell(70,4,'','L','','C');
			$this->SetFont('Arial','B'); 
			$this->Cell(14,4,'YTD TAXES:','L','','L');
			$this->SetFont('Arial',''); 
			$this->Cell(36,4,'Php '.number_format((float)$arrYTD['YtdTax'],2),'R','','R');
			$this->Ln();
		}
		else {
			$this->Cell(70,4,'','L','','C');
			$this->Cell(70,4,'','L','','C');
			$this->SetFont('Arial','B'); 
			$this->Cell(18,4,'','LT','','L');
			$this->SetFont('Arial',''); 
			$this->Cell(32,4,'','TR','','R');
			$this->Ln();
			$this->Cell(70,4,'','L','','C');
			$this->Cell(70,4,'','L','','C');
			$this->SetFont('Arial','B'); 
			$this->Cell(14,4,'','L','','L');
			$this->SetFont('Arial',''); 
			$this->Cell(36,4,'','R','','R');
			$this->Ln();
		}	


		$this->SetFont('Arial','B'); 
		$this->Cell(25,4,'TOTAL EARNINGS:','LTB','L');
		$this->Cell(45,4,'Php ' .number_format($totearnings,2),'TRB','','R');
		$this->Cell(25,4,'TOTAL DEDUCTIONS:','LTB','','L');
		$this->Cell(45,4,'Php ' .number_format($totdeductions,2),'TRB','','R');
		$this->Cell(50,4,' ','LTRB','','L');
		$this->Ln(5);
		$userId= $this->getSeesionVars();
		$dispUser=$this->getUserHeaderInfo($userId['empNo'],$_SESSION['employee_id']); 
		$this->SetFont('Arial','B','5'); 
		$this->Cell(50,4,"Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"],'','','L');
		$this->Cell(50,4,"Run Date: " . $this->currentDateArt(),'','','L');
	}
	
	function getEmpEearnings($empNo,$payPd,$act="") {
		if ($_GET['tbl']==2 && $act=="") {
			$Not="Not";
		}

		$qryearnings="SELECT tblPayTransType.trnDesc, tblEarnings{$this->hist}.* FROM tblEarnings{$this->hist} INNER JOIN  tblPayTransType ON tblEarnings{$this->hist}.compCode = tblPayTransType.compCode AND tblEarnings{$this->hist}.trnCode = tblPayTransType.trnCode where empNo='$empNo' and tblEarnings{$this->hist}.compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $payPd['pdYear']. "' and pdNumber='" . $payPd['pdNumber'] . "' and tblEarnings{$this->hist}.trnCode $Not In (Select trnCode from tblAllowType where compCode='" . $_SESSION['company_code'] . "' and sprtPS='Y') ";
		$researnings = $this->execQry($qryearnings);
		return $this->getArrRes($researnings);
	}
	
	function getEmpDeductions($empNo,$payPd) {
		$qrydeductions="SELECT tblPayTransType.trnDesc, tblDeductions{$this->hist}.* FROM tblDeductions{$this->hist} INNER JOIN  tblPayTransType ON tblDeductions{$this->hist}.compCode = tblPayTransType.compCode AND tblDeductions{$this->hist}.trnCode = tblPayTransType.trnCode where empNo='$empNo' and tblDeductions{$this->hist}.compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $payPd['pdYear']. "' and pdNumber='" . $payPd['pdNumber'] . "'";
		$resdeductions = $this->execQry($qrydeductions);
		return $this->getArrRes($resdeductions);
	
	}
	
	function SumEarnDed($table,$field,$where) {
		$qryearnded="SELECT sum($field) as totamount from $table $where";
		$researnded = $this->execQry($qryearnded);
		return $this->getSqlAssoc($researnded);	
	}
	
	function getYTD($empNo) {
		$qryYTD = "SELECT YtdGross,YtdTax from tblYtdData where empNo='$empNo' and compCode='" . $_SESSION['company_code'] . "'";
		$resYTD = $this->execQry($qryYTD);
		$arrYTD = $this->getSqlAssoc($resYTD);	
		$qryYTDhist = "SELECT YtdGross,YtdTax from tblYtdDatahist where empNo='$empNo' and compCode='" . $_SESSION['company_code'] . "'";
		$resYTDhist = $this->execQry($qryYTDhist);
		$arrYTDhist = $this->getSqlAssoc($resYTDhist);	
		$arrData['YtdGross'] = (float)$arrYTD['YtdGross'] + (float)$arrYTDhist['YtdGross'];
		$arrData['YtdTax'] = (float)$arrYTD['YtdTax'] + (float)$arrYTDhist['YtdTax'];
		return $arrData;
	}
	function getEmpWithAllow() {
		$qryAllow = "Select empNo from tblEarnings{$this->hist} where trnCode IN (Select trnCode from tblAllowType where sprtPS='Y')";
		$resAllow = $this->execQry($qryAllow);
		$empArr = array();
		foreach($this->getArrRes($resAllow) as $valAllow) {
			$empArr[] = $valAllow['empNo'];
		}
		return $empArr;
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

if ($empNo>"") {
	$empNo1 = " AND (empNo LIKE '{$empNo}%')";
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

if ($_GET['tbl']!=2) {
	$payallow = " and trnCode IN (Select trnCode from tblAllowType where compCode='" . $_SESSION['company_code'] . "' and sprtPS='Y')";
}
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
$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     AND empNo  IN (Select empNo from tblPayrollSummary{$pdf->hist} where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )
				 $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $branch
				 AND empNo IN (Select empNo FROM tblEarnings{$pdf->hist} where compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $arrPayPd['pdYear']. "' and pdNumber='" . $arrPayPd['pdNumber'] . "' $payallow)
				  order by empBrnCode,empLastName ";
$resEmpList = $psObj->execQry($qryIntMaxRec);
$arrEmpList = $psObj->getArrRes($resEmpList);

$pdf->BasicTable($arrEmpList);
$userId= $psObj->getSeesionVars();
$psObj->getUserHeaderInfo($userId['empNo'],$_SESSION['employee_id']); 
$pdf->Output('payslip.pdf','D');

?>
