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
	var $arrPayPd;
	var $arrBasic;
	var $arrPD;
	var $netPay;
	var $arrEmpGov;	
	function Head($empInfo) {
			$this->AddPage();
			$this->SetFont('Arial','B',11);
			$this->Cell(200,5,'C O M P U T A T I O N   O F   L A S T    P A Y',0,0,'C');
			$this->Ln(12);
			$this->SetFont('Arial','',8);
			$this->Cell(52,5,'Employee No.' );
			$this->SetFont('Arial','B',8);
			$this->Cell(50,5,$empInfo['empNo'],0,1);
			$this->SetFont('Arial','',8);
			$this->Cell(52,5,'NAME' );
			$this->SetFont('Arial','B',8);
			$this->Cell(50,5,$empInfo['empLastName'].', '.$empInfo['empFirstName'].' ' . $empInfo['empMidName'][0].'.',0,1);
			$this->SetFont('Arial','',8);
			$this->Cell(52,5,'DATE HIRED');
			$this->Cell(40,5,date("m/d/Y",strtotime($empInfo['dateHired'])),0,1,'R');
			$this->SetFont('Arial','',8);
			$this->Cell(52,5,'DATE RESIGNED');
			switch($empInfo['empStat']) {
				case "RS":
					$dt = date("m/d/Y",strtotime($empInfo['dateResigned']));
				break;
				case "TR":
					$dt = date("m/d/Y",strtotime($empInfo['dateResigned']));
				break;				
				case "EOC":
					$dt = date("m/d/Y",strtotime($empInfo['empEndDate']));
				break;
				case "AWOL":
					$dt = date("m/d/Y",strtotime($empInfo['dateResigned']));
				break;
				
			}
			$this->Cell(40,5,$dt,0,1,'R');
			$this->SetFont('Arial','',8);
			$this->Cell(52,5,'POSITION');
			$this->Cell(40,5,$empInfo['posShortDesc'],0,1,'R');
			$this->Cell(52,5,'DAILY RATE');
			$this->Cell(40,5,number_format($empInfo['empDrate'],2),0,1,'R');
			$this->Cell(52,5,'STATUS');
			$this->Cell(10,5,$empInfo['empTeu'],0,0);
			$this->Cell(30,5,number_format($empInfo['teuAmt'],2),0,1,'R');
			$this->Cell(52,5,'UNUSED LEAVES');
			$this->Cell(40,5,number_format($empInfo['leaveDays'],2),0,1,'R');
			$this->Ln(5);
			$this->Cell(52,5,'TAXABLE EARNINGS',0,1);
			$this->getEmpEearnings($empInfo['empNo'],'Y');

			$this->PayData($empInfo,$empInfo['teuAmt']);

			$this->Ln();
			$this->Cell(52,5,'PREPARED BY');
			$this->Cell(92,5,'');
			$this->Cell(52,5,'AUDITED BY',0,1);
			$this->Ln(10);
			$this->Cell(32,5,'  PAYROLL STAFF','T');
			$this->Cell(112,5,'');
			$this->Cell(32,5,'        AUDITOR','T',1);
			$this->Ln(5);
			$this->Cell(32,5,date('l, F d, Y'),0,1);
	}
	function getBasicValue($empNo,$pdNumber,$pdYear) {
		$trnAmount = 0;
		foreach($this->arrBasic as $valBasic) {
			if ($valBasic['empNo']==$empNo && $valBasic['pdYear']==$pdYear && $valBasic['pdNumber']==$pdNumber) {
				$trnAmount=$valBasic['basic'];
			}
		}
		return $trnAmount;
	}
	function PayData($arrempNo,$teuAmt) {
		$this->Cell(28,5,'MONTH',0,0,'C');
		$this->Cell(28,5,'BASIC',0,0,'C');
		$this->Cell(28,5,'GROSS',0,0,'C');
		$this->Cell(28,5,'W/TAX',0,0,'C');
		$this->Cell(28,5,'SSS',0,0,'C');
		$this->Cell(28,5,'MCR',0,0,'C');
		$this->Cell(28,5,'HDMF',0,1,'C');
		$this->Cell(196,1,'','T',1);
		$totBasic	= 0;
		$totGross	= 0;
		$totSSS		= 0;
		$totMCR		= 0;
		$totHDMF	= 0;
		$totTax		= 0;
		$totGrossCurYr = 0;
		//hist
		$arrGov = $this->getGovData('hist');
		foreach($this->getData($arrempNo['empNo'],'hist') as $valData) {
			foreach($this->arrPD as $valPD) {
					if ($valPD['pdYear']==$valData['pdYear'] && $valPD['pdNumber']==$valData['pdNumber'])
						$this->Cell(25,5,date('M d, Y',strtotime($valPD['pdPayable'])),0,0,'C');
			}
//			$this->Cell(23,5,number_format($this->getBasicValue($valData['empNo'],$valData['pdNumber'],$valData['pdYear']),2),0,0,'R');
			$this->Cell(23,5,number_format($valData['empBasic'],2),0,0,'R');
			$this->Cell(4,5,'',0,0,'R');
			if((int)trim((int)trim($valData['pdNumber']))%2){
				$pd=1;
			} else {
				$pd=2;
			}			
			$SSS = 0;			
			$MCR = 0;			
			$HDMF = 0;	
			foreach($arrGov as $valGov) {
				if ($valGov['empNo']==$valData['empNo'] && $valGov['pdYear']==$valData['pdYear'] && $this->getpdMonth($valData['pdNumber'])==$valGov['pdMonth'] && $pd==2){
						$SSS = $valGov['sssEmp'];			
						$MCR = $valGov['phicEmp'];			
						$HDMF = $valGov['hdmfEmp'];			
				}
			}			
			$taxableEarnings = 0;
			$taxableEarnings = $valData['taxableEarnings'];

				foreach($this->arrEmpGov as $valEmpGovAdj) {
					if ($valEmpGovAdj['empNo']==$valData['empNo'] && $valEmpGovAdj['monthPeriodDate']==$valData['pdNumber']) {
						$taxableEarnings += $valEmpGovAdj['amountToDed'];
					}
				}
/*
			if ($valData['pdYear'] == date('Y')) {
				$totGrossCurYr += $taxableEarnings;
				$wtax		= round($valData['taxWitheld'],2);
				$totTax		+= round($valData['taxWitheld'],2);
				$totSSS 	+= round($SSS,2);
				$totMCR 	+= round($MCR,2);
				$totHDMF 	+= round($HDMF,2);

			} else {
				$taxableEarnings = 0;
				$totTax		= 0;
				$wtax		= 0;
				$SSS 		= 0;			
				$MCR 		= 0;			
				$HDMF 		= 0;	
			}
*/
			$totGrossCurYr += $taxableEarnings;
				$wtax		= round($valData['taxWitheld'],2);
				$totTax		+= round($valData['taxWitheld'],2);
				$totSSS 	+= round($SSS,2);
				$totMCR 	+= round($MCR,2);
				$totHDMF 	+= round($HDMF,2);


			$this->Cell(25,5,number_format($taxableEarnings,2),0,0,'R');
			$this->Cell(3,5,'',0,0,'R');
			$this->Cell(25,5,number_format($wtax,2),0,0,'R');

			$this->Cell(7,5,'',0,0,'R');
			$this->Cell(28,5,number_format($SSS,2),0,0,'C');
			$this->Cell(28,5,number_format($MCR,2),0,0,'C');
			$this->Cell(28,5,number_format($HDMF,2),0,1,'C');
			$totBasic 	+= $valData['empBasic'];
			$totGross	+= round($taxableEarnings,2);
			
			$taxDue 	= $valData['taxWitheld'];
			$netpay		= $valData['netSalary'];

			unset($SSS,$MCR,$HDMF);
		}
		
		//cur
		if ($this->hist =="") {
				$arrGov = $this->getGovData('');
				foreach($this->getData($arrempNo['empNo'],'') as $valData) {
					foreach($this->arrPD as $valPD) {
							if ($valPD['pdYear']==$valData['pdYear'] && $valPD['pdNumber']==$valData['pdNumber'])
								$this->Cell(25,5,date('M d, Y',strtotime($valPD['pdPayable'])),0,0,'C');
					}
//					$this->Cell(23,5,number_format($this->getBasicValue($valData['empNo'],$valData['pdNumber'],$valData['pdYear']),2),0,0,'R');
					$this->Cell(23,5,number_format($valData['empBasic'],2),0,0,'R');
					$this->Cell(4,5,'',0,0,'R');
					$this->Cell(25,5,number_format($valData['taxableEarnings'],2),0,0,'R');
					if ($valData['pdYear'] == date('Y'))
						$totGrossCurYr += $valData['taxableEarnings'];
		
					$this->Cell(3,5,'',0,0,'R');
					$this->Cell(25,5,number_format($valData['taxWitheld'],2),0,0,'R');
					if((int)trim((int)trim($valData['pdNumber']))%2){
						$pd=1;
					} else {
						$pd=2;
					}
					foreach($arrGov as $valGov) {
						if ($valGov['empNo']==$valData['empNo'] && $valGov['pdYear']==$valData['pdYear'] && $this->getpdMonth($valData['pdNumber'])==$valGov['pdMonth'] && $pd==2){
								$SSS = $valGov['sssEmp'];			
								$MCR = $valGov['phicEmp'];			
								$HDMF = $valGov['hdmfEmp'];			
						}
					}
					$this->Cell(7,5,'',0,0,'R');
					$this->Cell(28,5,number_format($SSS,2),0,0,'C');
					$this->Cell(28,5,number_format($MCR,2),0,0,'C');
					$this->Cell(28,5,number_format($HDMF,2),0,1,'C');
					$totBasic 	+= $valData['empBasic'];
					$totGross	+= round($valData['taxableEarnings'],2);
					$totTax		+= round($valData['taxWitheld'],2);
					$totSSS 	+= round($SSS,2);
					$totMCR 	+= round($MCR,2);
					$totHDMF 	+= round($HDMF,2);
					$taxDue 	= $valData['taxWitheld'];
					$netpay		= $valData['netSalary'];
		
					unset($SSS,$MCR,$HDMF);
				}		
		}
		
			$this->Cell(196,1,'','T',1);
			$this->Cell(25,5,'TOTALS',0,0,'C');
			$this->Cell(23,5,number_format($totBasic,2),0,0,'R');
			$this->Cell(4,5,'',0,0,'R');
			$this->Cell(25,5,number_format($totGrossCurYr,2),0,0,'R');
			$this->Cell(3,5,'',0,0,'R');
			$this->Cell(25,5,number_format(round($totTax,2),2),0,0,'R');
			$this->Cell(7,5,'',0,0,'R');
			$this->Cell(28,5,number_format($totSSS,2),0,0,'C');
			$this->Cell(28,5,number_format($totMCR,2),0,0,'C');
			$this->Cell(28,5,number_format($totHDMF,2),0,1,'C');	
			if($arrempNo['empPrevTag']=='Y')
			{
				//Get Previous Employer Data to tblPrevEmployer
				$empPrevEarnings = $this->getPrevEmplr($arrempNo['empNo'],'prevEarnings');
				$empPrevTaxes = $this->getPrevEmplr($arrempNo['empNo'],'prevTaxes');
			}
			else
			{
				$empPrevEarnings = 0;
				$empPrevTaxes = 0;
			}			
			$this->Cell(54,5,'PREVIOUS');
			$this->Cell(23,5,number_format($empPrevEarnings,2),0,0,'R');
			$this->Cell(3,5,'',0,0,'R');
			$this->Cell(25,5,number_format($empPrevTaxes,2),0,1,'R');
			$this->Cell(52,5,'TAXABLE INCOME');
			$this->Cell(25,5,number_format(($totGrossCurYr + $empPrevEarnings)-$teuAmt,2),0,1,'R');

			$this->Cell(52,5,'TAX DUE');
			$this->Cell(23,5,'',0,0,'R');
			$this->Cell(4,5,'',0,0,'R');
			if ($taxDue<0)
				$taxDue = 0;
			$this->Cell(25,5,number_format($taxDue,2),0,1,'R');
			$this->getEmpEearnings($arrempNo['empNo'],'N');
			$this->getEmpDeductions($arrempNo['empNo']);	
			$this->Cell(52,5,'NETPAY');
			$this->Cell(140,5,number_format($this->netPay,2),0,1,'R');
			$this->EmpBal($arrempNo['empNo']);

	}
	
	function EmpBal($empNo) {
		$qryBal = "SELECT tblPayTransType.trnDesc, tblLastPaybal.* FROM tblLastPaybal INNER JOIN  tblPayTransType ON tblLastPaybal.compCode = tblPayTransType.compCode AND tblLastPaybal.trnCode = tblPayTransType.trnCode where empNo='$empNo' and tblLastPaybal.compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $this->arrPayPd['pdYear']. "' and pdNumber='" . $this->arrPayPd['pdNumber'] . "'";
		$ArrEmpBal = $this->getArrRes($this->execQry($qryBal));
		$hdr=0;
		foreach($ArrEmpBal as $valBal) {
			if ($hdr == 0) {
				$this->Ln(3);
				$this->Cell(52,5,'UNPAID BALANCE',0,1);
				$this->Cell(62,5,'TRANSACTION',0,0);
				$this->Cell(33,5,'REF. NO.',0,0);
				$this->Cell(25,5,'AMOUNT',0,1,'R');			
			}
			$hdr=1;
				$this->Cell(62,5,strtoupper($valBal['trnDesc']));
				$this->Cell(33,5,$valBal['refNo'],0,0);
				$this->Cell(25,5,number_format($valBal['Amount'],2),0,1,'R');			
		}
		
	}
	function getEmpEearnings($empNo,$tax) {
			
		$qryearnings="SELECT tblPayTransType.trnDesc, tblEarnings{$this->hist}.* FROM tblEarnings{$this->hist} INNER JOIN  tblPayTransType ON tblEarnings{$this->hist}.compCode = tblPayTransType.compCode AND tblEarnings{$this->hist}.trnCode = tblPayTransType.trnCode where empNo='$empNo' and tblEarnings{$this->hist}.compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $this->arrPayPd['pdYear']. "' and pdNumber='" . $this->arrPayPd['pdNumber'] . "' AND tblEarnings{$this->hist}.trnTaxCd ='$tax' ";
		$researnings = $this->execQry($qryearnings);
		$ArrEarnings = $this->getArrRes($researnings);
		if ($tax=='Y') {
			foreach($ArrEarnings as $valEarn) {
				$this->Cell(52,5,strtoupper($valEarn['trnDesc']));
				if (!empty($valEarn['trnAmountE'])) {
					$GrosPay += round($valEarn['trnAmountE'],2);
					$empearn = number_format($valEarn['trnAmountE'],2);
					if ($empearn<0) {
						$empearn = "($empearn)";
					}
				}			
				$this->Cell(40,5,$empearn,0,1,'R');
			
			}
			$this->netPay = $GrosPay;
			$this->SetFont('Arial','B',8);
			$this->Cell(52,5,'GROSS PAY');
			$this->Cell(40,5,number_format($GrosPay,2),0,1,'R');
			$this->SetFont('Arial','',8);
		} else {
		
			foreach($ArrEarnings as $valEarn) {
				$this->Cell(52,5,strtoupper($valEarn['trnDesc']));
				if (!empty($valEarn['trnAmountE'])) {
					$GrosPay += round($valEarn['trnAmountE'],2);
					$empearn = number_format($valEarn['trnAmountE'],2);
					$this->netPay += $valEarn['trnAmountE'];
					if ($empearn<0) {
						$empearn = "($empearn)";
					}
				}			
				$this->Cell(140,5,$empearn,0,1,'R');
			
			}
		}	
	}
	
	function getEmpDeductions($empNo) {
		$qrydeductions="SELECT tblPayTransType.trnDesc, tblDeductions{$this->hist}.* FROM tblDeductions{$this->hist} INNER JOIN  tblPayTransType ON tblDeductions{$this->hist}.compCode = tblPayTransType.compCode AND tblDeductions{$this->hist}.trnCode = tblPayTransType.trnCode where empNo='$empNo' and tblDeductions{$this->hist}.compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $this->arrPayPd['pdYear']. "' and pdNumber='" . $this->arrPayPd['pdNumber'] . "'";
		$resdeductions = $this->execQry($qrydeductions);
		$arrDed = $this->getArrRes($resdeductions);
		foreach($arrDed as $valDed) {
			$this->Cell(52,5,strtoupper($valDed['trnDesc']));
			if (!empty($valDed['trnAmountD'])) {
				$totDed += round($valDed['trnAmountD'],2);
				$empDed = number_format($valDed['trnAmountD'],2);
				$empDed = "($empDed)";
				$this->netPay -= $valDed['trnAmountD'];
			}			
			$this->Cell(140,5,$empDed,0,1,'R');
		
		}
	}
	
	function getEmpBasic() {
		if ($_GET['tbl']==2) {
			$Not="Not";
		}
		$pdYear = $this->arrPayPd['pdYear'];
		$pdNumber = $this->arrPayPd['pdNumber'];
		$qryearnings="Select empNo from tblPayrollSummary{$this->hist} where
								 payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
									";
		$this->arrBasic = $this->getArrRes($this->execQry($qryearnings));
	}
	function getGross($empNo,$pdNumber,$pdYear) {
		$qryGross="SELECT * FROM tblPayrollSummary{$this->hist} where empNo='$empNo' and compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $pdYear. "' and pdNumber='" . $pdNumber . "' and payCat='{$_SESSION['pay_category']}' AND payGrp='{$_SESSION['pay_group']}'";
		$resGross = $this->getSqlAssoc($this->execQry($qryGross));
		return $resGross['taxableEarnings'];
	}
	
	
	function getData($empNo,$hist) {
		if ($hist == "")
			$payCat = "AND PayCat='{$_SESSION['pay_category']}'";
			
		$qryData="Select * from  tblPayrollSummary$hist where empNo='$empNo'   $payCat AND ((pdYear=Year(getdate())) or (pdYear=(Year(getdate())-1) AND pdNumber>22)) order by pdYear,pdNumber";
		return $this->getArrRes($this->execQry($qryData));	
	}
	
	function getGovData($hist) {
		$qryGovData="Select empNo,sssEmp,phicEmp,hdmfEmp,pdYear,pdMonth from tblMtdGovt$hist where compCode='{$_SESSION['company_code']}'
				AND empNo IN (Select empNo from tblPayrollSummary{$this->hist} where
								pdYear='{$this->arrPayPd['pdYear']}'
								AND pdNumber = '{$this->arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )";
		return $this->getArrRes($this->execQry($qryGovData));	
	}
	function getPayPeriodDate() {
		$qryPayPeriod = "Select pdPayable,pdYear,pdNumber from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payGrp='{$_SESSION['pay_group']}' and payCat='{$_SESSION['pay_category']}'";
		$this->arrPD = $this->getArrRes($this->execQry($qryPayPeriod));
	}
	function getEmpGov() { 
		$sqlGov = "Select * from  tblGov_Tax_Added where compCode='{$_SESSION['company_code']}' and addStat='N'";
		$this->arrEmpGov = $this->getArrRes($this->execQry($sqlGov));
	}
	function getPrevEmplr($empNo,$prevfield){
		 if($prevfield!="")
		 {
		 	$qryStat = "sum($prevfield) as $prevfield";
			$prevfield = $prevfield;
		 }
		 else
		 {
		 	$qryStat = "count($empNo) as cntEmp";
			$prevfield = "cntEmp";
		 }
		 
		 $qrygetPrevEmplr = "Select ".$qryStat." from tblPrevEmployer where empNo='".$empNo."' and yearCd='".$this->get['pdYear']."' and compCode='".$this->session['company_code']."' and prevStat='A'";
		 $resgetPrevEmplr = $this->execQry($qrygetPrevEmplr);
		 $rowgetPrevEmplr = $this->getSqlAssoc($resgetPrevEmplr);
		 
		 return $rowgetPrevEmplr[$prevfield];
	}	
	function getpdMonth($pdNumber) {
		if ($pdNumber == 1 || $pdNumber == 2)
			return 1;
		elseif ($pdNumber == 3 || $pdNumber == 4)
			return 2;
		elseif ($pdNumber == 5 || $pdNumber == 6)
			return 3;
		elseif ($pdNumber == 7 || $pdNumber == 8)
			return 4;
		elseif ($pdNumber == 9 || $pdNumber == 10)
			return 5;
		elseif ($pdNumber == 11 || $pdNumber == 12)
			return 6;
		elseif ($pdNumber == 13 || $pdNumber == 14)
			return 7;
		elseif ($pdNumber == 15 || $pdNumber == 16)
			return 8;
		elseif ($pdNumber == 17 || $pdNumber == 18)
			return 9;
		elseif ($pdNumber == 19 || $pdNumber == 20)
			return 10;
		elseif ($pdNumber == 21 || $pdNumber == 22)
			return 11;
		elseif ($pdNumber == 23 || $pdNumber == 24)
			return 12;
	}

}

$pdf=new PDF();
$pdf->FPDF($orientation='P',$unit='mm',$format='LETTER');
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
$pdf->getEmpGov();
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
 $qryIntMaxRec = "SELECT empPrevTag,tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empNo, tblEmpMast.empMidName, tblEmpMast.empTeu, tblEmpMast.empDrate, 
                      tblEmpMast.dateResigned, tblTeu.teuAmt, tblLastPayData.leaveDays, tblPosition.posShortDesc,dateHired,endDate,empStat, empEndDate
				 FROM tblEmpMast LEFT OUTER JOIN
                      tblPosition ON tblEmpMast.empPosId = tblPosition.posCode AND tblEmpMast.compCode = tblPosition.compCode LEFT OUTER JOIN
                      tblLastPayData ON tblEmpMast.empNo = tblLastPayData.empNo AND tblEmpMast.compCode = tblLastPayData.compCode LEFT OUTER JOIN
                      tblTeu ON tblEmpMast.empTeu = tblTeu.teuCode
			     WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}'
			     AND tblEmpMast.empNo  IN (Select empNo from tblPayrollSummary{$pdf->hist} where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )
				 $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $branch
				  order by empLastName ";
$resEmpList = $psObj->execQry($qryIntMaxRec);
$arrEmpList = $psObj->getArrRes($resEmpList);
$pdf->getEmpBasic();
$pdf->getPayPeriodDate();
foreach($arrEmpList as $empInfo) {
	$pdf->Head($empInfo);
}	
$userId= $psObj->getSeesionVars();
$psObj->getUserHeaderInfo($userId['empNo'],$_SESSION['employee_id']); 
$pdf->Output('lastpay.pdf','D');
?>
