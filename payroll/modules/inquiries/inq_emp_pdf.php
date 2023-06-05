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
	function Header()
	{
		$this->Cell(66,5,"Run Date: ".$this->curdate);
		$this->Cell(90,5,$this->compName);
		$this->Cell(66,5,'Page '.$this->PageNo().' of {nb}',0,1);
		$this->Cell(66,5,"Report ID: EMPLN001");
		$this->Cell(66,5,"Employee Personel Information",0,1);
		$this->Ln(5);
	}
	function empInfo($resEmp,$otherInfo) {
		$this->Cell(195,8,"E M P L O Y E E    I N F O R M A T I O N",'TB',1);
		$this->Cell(66,5,"Employee Number           :",0,0);
		$this->Cell(66,5,$resEmp['empNo'],0,1);
		$this->Cell(66,5,"Name                      :",0,0);
		$this->Cell(66,5,$resEmp['empLastName']." ".$resEmp['empFirstName']." ".$resEmp['empMidName'],0,1);
		$this->Cell(66,5,"Division                  :",0,0);
		$this->Cell(66,5,$this->dispDivDesc['deptDesc'],0,1);
		$this->Cell(66,5,"Department                :",0,0);
		$this->Cell(66,5,$this->dispDeptDesc['deptDesc'],0,1);
		$this->Cell(66,5,"Section                   :",0,0);
		$this->Cell(66,5,$this->dispSectDesc['deptDesc'],0,1);
		$this->Cell(66,5,"Group                     :",0,0);
		$this->Cell(66,5,$otherInfo['empPayGrp'],0,1);
		$this->Cell(66,5,"Category                  :",0,0);
		$this->Cell(66,5,$this->catName['payCatDesc'],0,1);
		$this->Cell(66,5,"Location                  :",0,0);
		$this->Cell(66,5,$this->locName['brnShortDesc'],0,1);
		$this->Cell(66,5,"Branch                    :",0,0);
		$this->Cell(66,5,$this->brnchName['brnShortDesc'],0,1);
		$this->Cell(66,5,"Position                  :",0,0);
		$this->Cell(66,5,$this->postion['posShortDesc'],0,1);
		$this->Cell(66,5,"Date Hired                :",0,0);
		$this->Cell(66,5,$this->valDate($resEmp['dateHired']),0,1);
		$this->Cell(66,5,"Employee Status           :",0,0);
		$this->Cell(66,5,$otherInfo['empStat'],0,1);
		$this->Cell(66,5,"Regularization Date       :",0,0);
		$this->Cell(66,5,$this->valDate($resEmp['dateReg']),0,1);
		$this->Cell(66,5,"Pay Status Type           :",0,0);
		$this->Cell(66,5,$otherInfo['empPayType'],0,1);
		$this->Ln();

		$this->Cell(195,8,"C O N T A C T   I N F O R M A T I O N",'TB',1);
		$this->Cell(66,5,"Address                   :",0,0);
		$this->Cell(66,5,$resEmp['empAddr1'],0,1);
		$this->Cell(66,5,"                           ",0,0);
		$this->Cell(66,5,$resEmp['empAddr2'],0,1);
		$this->Cell(66,5,"                           ",0,0);
		$this->Cell(66,5,$resEmp['empAddr3'],0,1);
		
		foreach ($this->empContacts as $empContactsValue) {
			$DescLen=26-strlen($empContactsValue['contactDesc']);
			$DescSpace="";
			for ($ctr=0; $ctr<$DescLen; $ctr++) {
				$DescSpace .=" ";
			}
			$this->Cell(66,5,$empContactsValue['contactDesc']."$DescSpace:",0,0);
			$this->Cell(66,5,$empContactsValue['contactName']."1",0,1);
		}
		$this->Ln();
		
		$this->Cell(195,8,"P E R S O N A L   I N F O R M A T I O N",'TB',1);		
		$this->Cell(66,5,"Gender                    :",0,0);
		$this->Cell(66,5,$otherInfo['empSex'],0,1);
		$this->Cell(66,5,"Nick Name                 :",0,0);
		$this->Cell(66,5,$resEmp['empNickName'],0,1);
		$this->Cell(66,5,"Birth Place               :",0,0);
		$this->Cell(66,5,$resEmp['empBplace'],0,1);
		$this->Cell(66,5,"Birthday                  :",0,0);
		$this->Cell(66,5,$this->valDate($resEmp['empBday']),0,1);
		$this->Cell(66,5,"Civil Status              :",0,0);
		$this->Cell(66,5,$otherInfo['teuDesc'],0,1);
		$this->Cell(66,5,"Spouse                    :",0,0);
		$this->Cell(66,5,$resEmp['empSpouseName'],0,1);
		$this->Cell(66,5,"Height                    :",0,0);
		$this->Cell(66,5,$resEmp['empHeight'],0,1);
		$this->Cell(66,5,"Weight                    :",0,0);
		$this->Cell(66,5,$resEmp['empWeight'],0,1);
		$this->Cell(66,5,"Citizenship               :",0,0);
		$this->Cell(66,5,$otherInfo['citizenDesc'],0,1);
		$this->Cell(66,5,"Religion                  :",0,0);
		$this->Cell(66,5,$otherInfo['relDesc'],0,1);
		$this->Cell(66,5,"Build                     :",0,0);
		$this->Cell(66,5,$resEmp['empBuildDesc'],0,1);
		$this->Cell(66,5,"Complexion                :",0,0);
		$this->Cell(66,5,$resEmp['empComplexDesc'],0,1);
		$this->Cell(66,5,"Eye Color                 :",0,0);
		$this->Cell(66,5,$resEmp['empEyeColorDesc'],0,1);
		$this->Cell(66,5,"Hair                      :",0,0);
		$this->Cell(66,5,$resEmp['empHairDesc'],0,1);
		$this->Cell(66,5,"Blood Type                :",0,0);
		$this->Cell(66,5,$resEmp['empBloodType'],0,1);
		$this->Ln();	
		
		$this->Cell(195,8,"I D   N U M B E R S",'TB',1);	
		$this->Cell(66,5,"SSS Number                :",0,0);
		$this->Cell(66,5,$resEmp['empSssNo'],0,1);
		$this->Cell(66,5,"Phil Health Number        :",0,0);
		$this->Cell(66,5,$resEmp['empPhicNo'],0,1);
		$this->Cell(66,5,"TIN Number                :",0,0);
		$this->Cell(66,5,$resEmp['empTin'],0,1);
		$this->Cell(66,5,"Pag-ibig Number           :",0,0);
		$this->Cell(66,5,$resEmp['empPagibig'],0,1);
		$this->Cell(66,5,"Bank Name                 :",0,0);
		$this->Cell(66,5,$otherInfo['bankDesc'],0,1);
		$this->Cell(66,5,"Bank Account Number       :",0,0);
		$this->Cell(66,5,$resEmp['empAcctNo'],0,1);
		$this->Cell(195,5,"*** End of Report ****",0,1,'C');
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
//Column titles
//Data loading
$empNo = $_GET['empNo'];
$arrEmpInfo = $psObj->getUserInfo($_SESSION['company_code'], $_GET['empNo'],"");
$otherInfo = $psObj->empOtherInfos($arrEmpInfo['empNo']);
$pdf->arrEmpInfo = $psObj->getUserInfo($_SESSION['company_code'] , $empNo,""); 
$pdf->dispDivDesc = $psObj->getDivDescArt($_SESSION['company_code'], $arrEmpInfo['empDiv']);
$pdf->dispDeptDesc = $psObj->getDeptDescArt($_SESSION['company_code'], $arrEmpInfo['empDiv'], $arrEmpInfo['empDepCode']);
$pdf->compName=$psObj->getCompanyName($_SESSION['company_code']);
$pdf->curdate=$psObj->currentDateArt();
$pdf->dispSectDesc = $psObj->getSectDescArt($_SESSION['company_code'], $arrEmpInfo['empDiv'], $arrEmpInfo['empDepCode'], $arrEmpInfo['empSecCode']);
$pdf->otherInfo = $psObj->empOtherInfos($arrEmpInfo['empNo']);
$pdf->catName = $psObj->getEmpCatArt($_SESSION['company_code'], $arrEmpInfo['empPayCat']);
$pdf->locName = $psObj->getEmpBranchArt($_SESSION['company_code'], $arrEmpInfo['empLocCode']);
$pdf->brnchName = $psObj->getEmpBranchArt($_SESSION['company_code'], $arrEmpInfo['empBrnCode']);
$pdf->postion=$psObj->getpositionwil("where level=".(int)$arrEmpInfo['empLevel'],2);
$pdf->empContacts=$psObj->empContactswil($arrEmpInfo['empNo']);
$pdf->AliasNbPages();
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();
$pdf->AddPage();
$pdf->empInfo($arrEmpInfo,$otherInfo);
$pdf->Output();

?>
