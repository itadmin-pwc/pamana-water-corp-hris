<?php
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
include("../../../includes/pdf/fpdf.php");
class PDF extends FPDF
{
	var $printedby;
	var $company;
	var $rundate;
	var $table;
	var $reportlabel;
	var $arrPayPd;
	function Header()
	{
		$this->SetFont('Courier','','9'); 
		$this->Cell(100,5,"Run Date: " . $this->rundate);
		$this->Cell(200,5,$this->company);
		$this->Cell(35,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
		$this->Ln();
		$this->Cell(100,5,"Report ID: EMPLOANSR01");
		$this->Cell(200,5,$this->reportlabel.' Detailed Employee Other Deductions');
		$this->Ln();
		
		$this->Cell(335,3,'','B');
		$this->Ln();
		$this->SetFont('Courier','B','9'); 
		$this->Cell(8,6,'#',1,'','C');
		$this->Cell(20,6,'EMP. NO.',1);
		$this->Cell(57,6,'EMPLOYEE NAME',1);
		$arrdeductionList=$this->countdeductions();
		$wd=(225/(int)count($arrdeductionList));
		foreach ($arrdeductionList as $DeductionListValue) {
				$this->Cell($wd,6,$DeductionListValue['trnShortDesc'],1,'','C');
		}
		$this->Cell(25,6,'TOTAL',1,'','C');
		$this->Ln();
	}
	function otherdeductions($resEmpList) {
		$this->SetFont('Courier','','9'); 
		$totalDeductions=0;
		$arrdeductionList=$this->countdeductions();
		$wd=(225/(int)count($arrdeductionList));
		$ctr=1;
		foreach($resEmpList as $empValue) {
			$this->Cell(8,6,$ctr,1,'','C');
			$this->Cell(20,6,$empValue['empNo'],1);
			$this->Cell(57,6,$empValue['empLastName'] . " ". $empValue['empFirstName'] . " ". $empValue['empMidName'],1);
			$empDedsum=0;
			$arrDeductions=$this->getempdeductions($empValue['empNo']);
			foreach ($arrdeductionList as $DeductionListValue) {
				$empDeduction=0;
				foreach ($arrDeductions as $empDeductions) {
					if ($DeductionListValue['trnCode']==$empDeductions['trnCode']) {
						$empDeduction=(float)$empDeductions['trnAmount'];
						$empDedsum +=(float)$empDeductions['trnAmount'];
						$arrDedSum[$DeductionListValue['trnCode']] +=(float)$empDeductions['trnAmount'];
					}
				}
				
				$this->Cell($wd,6,number_format((float)$empDeduction,2),1,'','R');
			}
			$this->Cell(25,6,number_format($empDedsum,2),1,'','R');
			$totalDeductions +=(float)$empDedsum;
			$this->Ln();
			$ctr++;
		}	
		$arrdeductionList=$this->countdeductions();
		$wd=(225/(int)count($arrdeductionList));
		$this->SetFont('Courier','B','9');
		$this->Cell(8,6,'');
		$this->Cell(77,6,'TOTAL',1,'','C');
		foreach ($arrdeductionList as $DeductionListValue) {
			$this->Cell($wd,6,number_format((float)$arrDedSum[$DeductionListValue['trnCode']],2),1,'','R');		
		}
		$this->Cell(25,6,number_format((float)$totalDeductions,2),1,'','R');
	}
	function getempdeductions($empNo) {
		$qry="SELECT  SUM(trnAmount) AS trnAmount,trnCode
					  FROM tblDedTranDtl{$this->table} 
					  where compCode='" . $_SESSION['company_code'] . "' 
					  AND payCat = '" . $_SESSION['pay_category'] . "' 
					  AND payGrp = '" . $_SESSION['pay_group'] . "'
					  AND empNo='$empNo'
					  AND processtag='Y'
					  AND trnCode NOT IN (Select trnCode from tblLoanType 
					  					 where compCode='" . $_SESSION['company_code'] . "' 
										 AND lonTypeStat='A')
					  Group By trnCode";
		
		$resdeductions = $this->getArrRes($this->execQry($qry));
		return $resdeductions;
	}
	
	function countdeductions() {
		$qrycount="SELECT trnCode, trnShortDesc 
					FROM tblPayTransType 
							WHERE (trnCode IN (SELECT trnCode  
									FROM tblDedTranDtl{$this->table} WHERE processtag = 'Y' 
									AND compCode = '" . $_SESSION['company_code'] . "'
									AND payCat = '" . $_SESSION['pay_category'] . "' 
									AND payGrp = '" . $_SESSION['pay_group'] . "')
									AND trnCode NOT IN (Select trnCode from tblLoanType 
					  					 where compCode='" . $_SESSION['company_code'] . "' 
										 AND lonTypeStat='A')) 
							AND (compCode = '" . $_SESSION['company_code'] . "')
							AND trnApply IN (3,{$this->getCutOffPeriod()})
							ORDER BY trnCode";
		$rescountdeductions = $this->getArrRes($this->execQry($qrycount));
		return $rescountdeductions;
	}
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(335,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}

	function getCutOffPeriod(){

		if((int)trim((int)trim($this->arrPayPd['pdNumber']))%2){
			return  1;
		}
		else{
			return 2;
		}	
	}
}

$pdf=new PDF('L', 'mm', 'LEGAL');
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();
//Column titles
//Data loading
$empNo = $_POST['empNo'];
$empName = $_POST['txtEmpName'];
if ($_POST['nameType']=="1") {
	$nameType="empLastName";
}
elseif ($_POST['nameType']=="2") {
	$nameType="empFirstName";
}	
else {
	$nameType="empMidName";
}
$cmbDiv = $_POST['cmbDiv'];
$empDept = $_POST['empDept'];
$empSect = $_POST['empSect'];
$empBank=$_POST['cmbBank'];
$groupType = $_SESSION['pay_group'];
$payPd = $_POST['payPd'];
$catType = $_SESSION['pay_category'];
$reportType=($_POST['reportType']==1?"Hist":"");;
$reportLabel=($_POST['reportType']==1?"(POSTED) ":"(PRE - POSTED)");;
$groupName = ($groupType==1?"GROUP 1":"GROUP 2");
$empNo1 = ($empNo>""?" AND (tblEmpMast.empNo LIKE '{$empNo}%')":"");
$cmbDiv1=($cmbDiv>"" && $cmbDiv>0?" AND (empDiv = '{$cmbDiv}')":"");
$Bank=($empBank!="" && $empBank>0?" AND (empBankCd='$empBank')":"");
$empName1=($empName>""?" AND ($nameType LIKE '{$empName}%')":"");


$catName = $psObj->getEmpCatArt($_SESSION['company_code'], $catType);
$arrPayPd = $psObj->getSlctdPdwil($_SESSION['company_code'],$payPd);

	$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}'
			     AND empStat NOT IN('RS','IN','TR') 
				  $empNo1 $empName1 $cmbDiv1 $Bank 
				  and empNo IN (Select empNo from tblDedTranDtl$reportType
				  				where compCode='" . $_SESSION['company_code'] . "' 
								  AND payCat = '" . $_SESSION['pay_category'] . "' 
								  AND payGrp = '" . $_SESSION['pay_group'] . "'
								  AND processtag='Y'
					 			  AND trnCode NOT IN (Select trnCode from tblLoanType 
					  					 where compCode='" . $_SESSION['company_code'] . "' 
										 AND lonTypeStat='A')
				  				)
				  order by empLastName,empFirstName,empMidName
				 ";

$resEmpList = $psObj->execQry($qryIntMaxRec);
$arrEmpList = $psObj->getArrRes($resEmpList);
$pdf->AliasNbPages();
$pdf->table = $reportType;
$pdf->arrPayPd=$arrPayPd;
$pdf->reportlabel = $reportLabel;
$pdf->company = $psObj->getCompanyName($_SESSION['company_code']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();
$pdf->AddPage();
$pdf->otherdeductions($arrEmpList);
$pdf->Output('other_deductions_detailed.pdf','D');

?>
