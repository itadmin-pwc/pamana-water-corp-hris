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
		$this->Cell(200,5,$this->reportlabel.' Detailed Employee Loans');
		$this->Ln();
		
		$this->Cell(335,3,'','B');
		$this->Ln();
		$this->SetFont('Courier','B','9'); 
		$this->Cell(8,6,'#',1,'','C');
		$this->Cell(20,6,'EMP. NO.',1);
		$this->Cell(57,6,'EMPLOYEE NAME',1);
		$arrLoansList=$this->countLoans();
		$wd=(225/(int)count($arrLoansList));
		foreach ($arrLoansList as $LoansListValue) {
				$this->Cell($wd,6,$LoansListValue['lonTypeShortDesc'],1,'','C');
		}
		$this->Cell(25,6,'TOTAL',1,'','C');
		$this->Ln();
	}
	function loans($resEmpList) {
		$this->SetFont('Courier','','9'); 
		$totalLoans=0;
		$arrLoansList=$this->countLoans();
		$wd=(225/(int)count($arrLoansList));
		$ctr=1;
		foreach($resEmpList as $empValue) {
			$this->Cell(8,6,$ctr,1,'','C');
			$this->Cell(20,6,$empValue['empNo'],1);
			$this->Cell(57,6,$empValue['empLastName'] . " ". $empValue['empFirstName'] . " ". $empValue['empMidName'],1);
			$emploansum=0;
			$arrLoans=$this->getemploans($empValue['empNo']);
			foreach ($arrLoansList as $LoansListValue) {
				$empLoan=0;
				foreach ($arrLoans as $empLoans) {
					if ($LoansListValue['trnCode']==$empLoans['trnCode']) {
						$empLoan=(float)$empLoans['trnAmountD'];
						$emploansum +=(float)$empLoans['trnAmountD'];
						$arrLoanSum[$LoansListValue['lonTypeCd']] +=(float)$empLoans['trnAmountD'];
					}
				}
				$this->Cell($wd,6,number_format((float)$empLoan,2),1,'','R');
			}
			$this->Cell(25,6,number_format($emploansum,2),1,'','R');
			$totalLoans +=(float)$emploansum;
			$this->Ln();
			$ctr++;
		}	
		$arrLoansList=$this->countLoans();
		$wd=(225/(int)count($arrLoansList));
		$this->SetFont('Courier','B','9');
		$this->Cell(8,6,'');
		$this->Cell(77,6,'TOTAL',1,'','C');
		foreach ($arrLoansList as $LoansListValue) {
			$this->Cell($wd,6,number_format((float)$arrLoanSum[$LoansListValue['lonTypeCd']],2),1,'','R');		
		}
		$this->Cell(25,6,number_format((float)$totalLoans,2),1,'','R');
	}
	function getemploans($empNo) {
		$qry="Select trnAmountD,trnCode from tblDeductions{$this->table} 
					  where compCode='" . $_SESSION['company_code'] . "' 
					  AND pdYear='" . $this->arrPayPd['pdYear']. "' 
					  AND pdNumber='" . $this->arrPayPd['pdNumber']. "'
					  AND empNo='$empNo'";
		
		$resloans = $this->getArrRes($this->execQry($qry));
		return $resloans;
	}
	
	function countLoans() {
		$qrycount="SELECT lonTypeCd, trnCode, lonTypeShortDesc 
					FROM tblLoanType 
							WHERE (lonTypeCd IN (SELECT lonTypeCd  
									FROM tblEmpLoansDtl{$this->table} WHERE dedtag = 'Y' 
									AND compCode = '" . $_SESSION['company_code'] . "'
									AND trnCat = '" . $_SESSION['pay_category'] . "' 
									AND trnGrp = '" . $_SESSION['pay_group'] . "'
									AND pdYear='" . $this->arrPayPd['pdYear']. "' 
									AND pdNumber='" . $this->arrPayPd['pdNumber']. "'
									)) 
							AND (compCode = '" . $_SESSION['company_code'] . "')
							ORDER BY lonTypeCd";
		$rescountloans = $this->getArrRes($this->execQry($qrycount));
		return $rescountloans;
	}
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(335,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
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
				  and empNo IN (Select empNo from tblEmpLoansDtl$reportType where dedtag='Y' and compCode='" . $_SESSION['company_code'] . "' and pdYear='" . $arrPayPd['pdYear'] . "' and pdNumber='" . $arrPayPd['pdNumber']. "')
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
$pdf->loans($arrEmpList);
$pdf->Output('detailed_loans.pdf','D');

?>
