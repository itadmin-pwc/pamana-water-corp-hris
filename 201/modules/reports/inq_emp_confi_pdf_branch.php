<?php
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
include("../../../includes/pdf/fpdf.php");

class PDF extends FPDF
{
	var $arrEmpInfo; 
	var $dispDivDesc;
	var $dispDeptDesc;
	var $dispSectDesc;
	var $otherInfo;
	var $catName;
	var $locName;
	var $brnchName;
	var $postion;
	var $empContacts;
	var $curdate;
	var $compName;
	var $printedby;
	var $arrEmpOtherInfos;
	var $countOtherInfos;
	var $branch;
	var $empBranch;
	
	function Header()
	{
		$this->Cell(80,5,$this->compName);
		$this->Cell(70,5,"Run Date: ".$this->curdate);
		$this->Cell(30,5,'Page '.$this->PageNo().' of {nb}',0,1);
		//$this->Cell(80,5,$this->branch);
		$this->Cell(150,5,"Employee Personnel Information(Confidentials)",0,0);		
		$this->Cell(40,5,"Report ID: EMPLN001",0,1);
		$this->Ln(5);
	}
	
	function empBranches(){
		foreach($this->empBranch as $employeeBranch){
				$this->AddPage();
				$this->empInfo($employeeBranch,$position);
			}	
	}

	function empInfo($employeeBranch,$position){
		$otherInfo = $this->empOtherInfos($employeeBranch['empNo']);
		$position  = $this->getpositionwil(" where posCode='".$employeeBranch['empPosId']."'",2);
		$dispDivDesc = $this->getDivDescArt($employeeBranch['compCode'], $employeeBranch['empDiv']);
		$brnchName = $this->getEmpBranchArt($employeeBranch['compCode'], $employeeBranch['empBrnCode']);
		$dispDeptDesc = $this->getDeptDescArt($employeeBranch['compCode'], $employeeBranch['empDiv'], $employeeBranch['empDepCode']);
		$taxName = $this->getEmpTeuArt($employeeBranch['empTeu']);
		$bankName = $this->getEmpBankArt($employeeBranch['compCode'], $employeeBranch['empBankCd']);
		$dispDeptDesc = $this->getDeptDescArt($employeeBranch['compCode'], $employeeBranch['empDiv'], $employeeBranch['empDepCode']);
		$dispSectDesc = $this->getSectDescArt($employeeBranch['compCode'], $employeeBranch['empDiv'], $employeeBranch['empDepCode'], $employeeBranch['empSecCode']);
		$catName = $this->getEmpCatArt($employeeBranch['compCode'], $employeeBranch['empPayCat']);
		$locName = $this->getEmpBranchArt($employeeBranch['compCode'], $employeeBranch['empLocCode']);
		
		$arrAllowList = $this->getEmpAllowListArt($employeeBranch['compCode'],$employeeBranch['empNo']);
		$bankname = $bankName['bankDesc'];
		$accntno = $employeeBranch['empAcctNo'];
		$MRate = number_format($employeeBranch['empMrate'],2);
		$DRate = number_format($employeeBranch['empDrate'],2);
		$HRate = number_format($employeeBranch['empHrate'],2);
		if ($employeeBranch['empPayGrp']==1) { $grpName = "Group 1"; } 
		if ($employeeBranch['empPayGrp']==2) { $grpName = "Group 2"; }
		if ($employeeBranch['employmentTag']=="RG") { $empStat = "Regular"; } 
		if ($employeeBranch['employmentTag']=="PR") { $empStat = "Probationary"; }
		if ($employeeBranch['employmentTag']=="CN") { $empStat = "Contractual"; }
		if ($employeeBranch['empSex']=="M") { $gender = "Male"; } 
		if ($employeeBranch['empSex']=="F") { $gender = "Female"; }
		if ($employeeBranch['empPayType']=="D") { $payStatus = "Daily"; } 
		if ($employeeBranch['empPayType']=="M") { $payStatus = "Monthly"; }
		if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
			if ($employeeBranch['empPayCat'] == 1) {
				$MRate = '--';
				$DRate = '--';
				$HRate = '--';
			}
		}	
		
		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"E M P L O Y E E    I N F O R M A T I O N",'TB',1);
		$this->SetFont('Courier', '', '10');
		$this->Cell(40,5,"Employee Number :",0,0);
		$this->Cell(65,5,$employeeBranch['empNo'],0,0);
		$this->Cell(40,5,"Branch          :",0,0);
		$this->Cell(85,5,$brnchName['brnShortDesc'],0,1);
		$this->Cell(40,5,"Name            :",0,0);
		$this->Cell(65,5,$employeeBranch['empLastName'] . ", " . $employeeBranch['empFirstName'] . " " . $employeeBranch['empMidName'],0,0);
		$this->Cell(40,5,"Position        :",0,0);
		$this->MultiCell(50,5,$position['posDesc'],0,1);
		$this->Cell(40,5,"Division        :",0,0);
		$this->Cell(65,5,$dispDivDesc['deptShortDesc'],0,0);
		$this->Cell(40,5,"Date Hired      :",0,0);
		$this->Cell(65,5,$this->valDate($employeeBranch['dateHired']),0,1);
		$this->Cell(40,5,"Department      :",0,0);
		$this->Cell(65,5,$dispDeptDesc['deptShortDesc'],0,0);
		$this->Cell(40,5,"Employee Status :",0,0);
		$this->Cell(65,5,$empStat,0,1);
		$this->Cell(40,5,"Section         :",0,0);
		$this->Cell(65,5,$dispSectDesc['deptShortDesc'],0,0);
		$this->Cell(40,5,"Date Regularized:",0,0);
		$this->Cell(65,5,$this->valDate($employeeBranch['dateReg']),0,1);
		$this->Cell(40,5,"Group           :",0,0);
		$this->Cell(65,5,$grpName,0,0);
		$this->Cell(40,5,"Pay Status Type :",0,0);
		$this->Cell(65,5,$payStatus,0,1);
		$this->Cell(40,5,"Category        :",0,0);
		$this->Cell(65,5,$catName['payCatDesc'],0,0);
		$this->Cell(40,5,"Location        :",0,0);
		$this->Cell(65,5,$locName['brnShortDesc'],0,1);
		$this->Ln();

		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"A C C O U N T     I N F O R M A T I O N",'TB',1);
		$this->SetFont('Courier', '', '10');		
		$this->Cell(40,5,"Bank Name       :",0,0);
		$this->Cell(65,5,$bankname,0,1);
		$this->Cell(40,5,"Account Number  :",0,0);
		$this->Cell(65,5,$accntno,0,1);
		$this->Ln();

		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"C O N F I D E N T I A L     I N F O R M A T I O N",'TB',1);
		$this->SetFont('Courier', '', '10');		
		$this->Cell(40,5,"Monthly Rate    :",0,0);
		$this->Cell(65,5,$MRate,0,1);
		$this->Cell(40,5,"Daily Rate      :",0,0);
		$this->Cell(65,5,$DRate,0,1);
		$this->Cell(40,5,"Hourly Rate     :",0,0);
		$this->Cell(65,5,$HRate,0,1);
		$this->Ln();

		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"A L L O W A N C E     I N F O R M A T I O N",'TB',1);
		$this->Cell(5,6,"",0,0);
		$this->Cell(25,6,"ALLOWANCE                 START DATE   END DATE   PAY PERIOD   TAXABLE      AMOUNT",0,1);
		$this->Cell(5, 0, '', 0, 0);
		$this->Cell(180, 0, '', 1, 1);


		foreach ($arrAllowList as $allowListVal){
			if ($allowListVal['allowSked']==1) $allowSked = "1st Period";
			if ($allowListVal['allowSked']==2) $allowSked = "2nd Period";
			if ($allowListVal['allowSked']==3) $allowSked = "Both Period";
			$this->SetFont('Courier', '', '9');		
			$this->Cell(5,5,"",0,0);
			$this->Cell(55,5,$allowListVal['allowDesc'],0,0);
			if ($allowListVal['allowPayTag']=="T") {
				$this->Cell(25,5,$this->valDate($allowListVal['allowStart']),0,0);
				$this->Cell(25,5,$this->valDate($allowListVal['allowEnd']),0,0);
			} else {
				$this->Cell(25,5,"",0,0);
				$this->Cell(25,5,"",0,0);
			}
			$this->Cell(25,5,$allowSked,0,0);
			if ($allowListVal['allowTaxTag']=="Y") {
				$this->Cell(20,5,"YES",0,0,'C');
			} else {
				$this->Cell(20,5,"NO",0,0,'C');
			}
			$AllwAmt = number_format($allowListVal['allowAmt'],2);
			if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
				if ($employeeBranch['empPayCat'] == 1) {
					$AllwAmt = '--';
				}
			}
			$this->SetFont('Courier', 'B', '9');	
			$this->Cell(25,5,$AllwAmt,0,1,'R');
		}
		$this->Ln();
		$this->SetFont('Courier', '', '10');
		$this->Cell(195,5,"*** End of Report ****",0,1,'C');
		$this->Ln();	
		$this->Ln();
		$this->Ln();
		$this->Ln();
	}

	function valDate($date) {
		if ($date=="") {
			$newDate = "";
		} else {
			$newDate = date("m/d/Y",strtotime($date));
		}
		return $newDate;
	}
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(195,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(195,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}	
	
}
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$pdf=new PDF('P', 'mm', 'LEGAL');
$pdf->SetFont('Courier', '', '10');
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();

////Column titles
////Data loading

$pdf->branch 			= $psObj->getInfoBranch($_GET['qryBranch'],$_SESSION['company_code']);
$pdf->empBranch			= $psObj->getEmpBranch($_SESSION['company_code']," and empBrnCode='".$_GET['qryBranch']."' and empStat NOT IN ('RS', 'IN', 'TR')");
$pdf->curdate			= $psObj->currentDateArt();
$pdf->compName			= $psObj->getCompanyName($_SESSION['company_code']);
$pdf->AliasNbPages();
$pdf->printedby 		= $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate			= $psObj->currentDateArt();
$pdf->empBranches();
$pdf->Output('EMPLOYEE_INFORMATION.pdf','D');
?>