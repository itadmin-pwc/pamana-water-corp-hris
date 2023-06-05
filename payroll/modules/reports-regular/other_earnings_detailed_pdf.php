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
		$this->Cell(200,5,$this->reportlabel.' Detailed Employee Other Earnings');
		$this->Ln();
		
		$this->Cell(335,3,'','B');
		$this->Ln();
		$this->SetFont('Courier','B','9'); 
		$this->Cell(8,6,'#',1,'','C');
		$this->Cell(20,6,'EMP. NO.',1);
		$this->Cell(57,6,'EMPLOYEE NAME',1);
		$arrEarningsList=$this->countotherearnings();
		$wd=(225/(int)count($arrEarningsList));
		foreach ($arrEarningsList as $EarningListValue) {
				$this->Cell($wd,6,$EarningListValue['trnShortDesc'],1,'','C');
		}
		$this->Cell(25,6,'TOTAL',1,'','C');
		$this->Ln();
	}
	function otherearnings($resEmpList) {
		$this->SetFont('Courier','','9'); 
		$totalEarnings=0;
		$arrEarningsList=$this->countotherearnings();
		$wd=(225/(int)count($arrEarningsList));
		$ctr=1;
		foreach($resEmpList as $empValue) {
			$this->Cell(8,6,$ctr,1,'','C');
			$this->Cell(20,6,$empValue['empNo'],1);
			$this->Cell(57,6,$empValue['empLastName'] . " ". $empValue['empFirstName'] . " ". $empValue['empMidName'],1);
			$empEarnsum=0;
			$arrOtherEarnings=$this->getempotherearnings($empValue['empNo']);
			foreach ($arrEarningsList as $EarningListValue) {
				$empEarning=0;
				foreach ($arrOtherEarnings as $empEarnings) {
					if ($EarningListValue['trnCode']==$empEarnings['trnCode']) {
						$empEarning=(float)$empEarnings['trnAmount'];
						$empEarnsum +=(float)$empEarnings['trnAmount'];
						$arrOthEarn[$EarningListValue['trnCode']] +=(float)$empEarnings['trnAmount'];
					}
				}
				
				$this->Cell($wd,6,number_format((float)$empEarning,2),1,'','R');
			}
			$this->Cell(25,6,number_format($empEarnsum,2),1,'','R');
			$totalEarnings +=(float)$empEarnsum;
			$this->Ln();
			$ctr++;
		}	
		$arrEarningsList=$this->countotherearnings();
		$wd=(225/(int)count($arrEarningsList));
		$this->SetFont('Courier','B','9');
		$this->Cell(8,6,'');
		$this->Cell(77,6,'TOTAL',1,'','C');
		foreach ($arrEarningsList as $EarningListValue) {
			$this->Cell($wd,6,number_format((float)$arrOthEarn[$EarningListValue['trnCode']],2),1,'','R');		
		}
		$this->Cell(25,6,number_format((float)$totalEarnings,2),1,'','R');
	}
	function getempotherearnings($empNo) {
		$qry="SELECT  SUM(trnAmount) AS trnAmount,trnCode
					  FROM tblEarnTranDtl{$this->table} 
					  where compCode='" . $_SESSION['company_code'] . "' 
					  AND payCat = '" . $_SESSION['pay_category'] . "' 
					  AND payGrp = '" . $_SESSION['pay_group'] . "'
					  AND empNo='$empNo'
					  AND earnStat='A'
					  Group By trnCode";
		
		$resdeductions = $this->getArrRes($this->execQry($qry));
		return $resdeductions;
	}
	
	function countotherearnings() {
		$qrycount="SELECT trnCode, trnShortDesc 
					FROM tblPayTransType 
							WHERE (trnCode IN (SELECT trnCode  
									FROM tblEarnTranDtl{$this->table} WHERE earnStat = 'A' 
									AND compCode = '" . $_SESSION['company_code'] . "'
									AND payCat = '" . $_SESSION['pay_category'] . "' 
									AND payGrp = '" . $_SESSION['pay_group'] . "')) 
							AND (compCode = '" . $_SESSION['company_code'] . "')
							AND trnApply IN (3,{$this->getCutOffPeriod()})
							ORDER BY trnCode";
		
		$rescountotherearnings = $this->getArrRes($this->execQry($qrycount));
		return $rescountotherearnings;
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
				 and empNo IN (Select empNo from tblEarnTranDtl$reportType
				  				where earnStat='A' 
								AND compCode = '" . $_SESSION['company_code'] . "'
								AND payCat = '" . $_SESSION['pay_category'] . "' 
								AND payGrp = '" . $_SESSION['pay_group'] . "')
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
$pdf->otherearnings($arrEmpList);
$pdf->Output('other_earnings_detailed.pdf','D');

?>
