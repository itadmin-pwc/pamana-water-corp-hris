<?php
####Include files
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
include("../../../includes/pdf/fpdf.php");

####Create class 
class PDF extends FPDF
{
####Declare variables	
	var $printedby;
	var $company;
	var $rundate;
	var $table;
	var $reportlabel;
	var $arrPayPd;
	var $arrDept;
	var $ctrs;
	
####Set up header	
	function Header()
	{
		$this->SetFont('Courier','','9'); 
		$this->Cell(80,5,"Run Date: " . $this->rundate);
		$this->Cell(90,5,$this->company);
		$this->Cell(35,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
		$this->Ln();
		$this->Cell(80,5,"Report ID: NEWEMPPROOFLST",'B',0);
		if ($_GET['from'] != "" && $_GET['to'] != "") {
			$fromdt = $_GET['from'];
			$todt = $_GET['to'];
			$date = "$fromdt - $todt";
		} 
		$grp = ($_GET['group'] !='' && $_GET['group'] !='0') ? " Group {$_GET['group']}" : "";
		$this->Cell(74,5,$this->reportlabel.'NEW EMPLOYEE PROOF LIST' . $grp,'B',0);
		$this->Cell(47,5,$this->reportlabel.$date,'B',1);
		$this->Ln();
	}

####Set up details/data	
	function Data($ctr,$arrEmp,$date,$user) {
		$this->SetFont('Courier','B','7'); 
		$this->Cell(26,5,"Date Released :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(75,5,$date,0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(11,5,"User :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(100,5,$user,0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(26,5,"Employee Number :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(75,5,$arrEmp['empNo'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(10,5,"Name :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(100,5,$arrEmp['empLastName']." ".$arrEmp['empFirstName']." ".$arrEmp['empMidName'],0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(16,5,"Position :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(85,5,$arrEmp['posShortDesc'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(18,5,"Division :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->MultiCell(100,5,$this->getDept($arrEmp['empDiv'],'','',1),0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(21,5,"Department :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->setMultiLine(80,5,$this->getDept($arrEmp['empDiv'],$arrEmp['empDepCode'],'',2),0,L);	
		$this->SetFont('Courier','B','7'); 	
		$this->Cell(16,5,"Section :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(43,5,$this->getDept($arrEmp['empDiv'],$arrEmp['empDepCode'],$arrEmp['empSecCode'],3),0,1);
		if(strlen($this->getDept($arrEmp['empDiv'],$arrEmp['empDepCode'],'',1))>50){
			$this->Ln();
		}
		$this->SetFont('Courier','B','7'); 
		$this->Cell(13,5,"Group :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(88,5,$arrEmp['empPayGrp'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(18,5,"Category :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(83,5,$arrEmp['payCatDesc'],0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(10,5,"TEU :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(91,5,$arrEmp['teuDesc'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(30,5,"Employee Status :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(31,5,$arrEmp['employmentTag'],0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(36,5,"Regularization Date :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(65,5,$this->valDate($arrEmp['dateReg']),0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(20,5,"Rate Mode :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(16,5,$arrEmp['empPayType'],0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(18,5,"Location :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(83,5,$arrEmp['brnShortDesc'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(15,5,"Branch :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(48,5,$arrEmp['brnShortDesc'],0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(21,5,"Date Hired :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(80,5,$this->valDate($arrEmp['dateHired']),0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(11,5,"Rate :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(50,5,($arrEmp['empPayType']=="Monthly") ? number_format($arrEmp['empMrate'],2)."/Month": number_format($arrEmp['empDrate'],2)."/Day",0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(30,5,"Employment Type :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(71,5,$arrEmp['rankDesc'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(16,5,"Address :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->MultiCell(75,5,$arrEmp['empAddr1'].', '.$arrEmp['empAddr2'].' '.$this->empMunicipality($arrEmp['empMunicipalityCd']).' '.$this->empProvince($arrEmp['empProvinceCd']),0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(14,5,"Gender :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(87,5,($arrEmp['empSex'] == "M")? "Male":"Female",0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(20,5,"Nick Name :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(43,5,$arrEmp['empNickName'],0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(24,5,"Civil Status :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(77,5,$arrEmp['empMarStat'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(18,5,"Birthday :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(43,5,$this->valDate($arrEmp['empBday']),0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(9,5,"Age :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(92,5,date('Y') - date('Y',strtotime($arrEmp['empBday'])),0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(23,5,"Birth Place :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->MultiCell(75,5,$arrEmp['empBplace'],0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(21,5,"SSS Number :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(80,5,$arrEmp['empSssNo'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(23,5,"Phic Number :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(40,5,$arrEmp['empPhicNo'],0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(21,5,"TIN Number :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(80,5,$arrEmp['empTin'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(30,5,"Pag-ibig Number :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(31,5,$arrEmp['empPagibig'],0,1);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(20,5,"Bank Name :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(81,5,$arrEmp['bankDesc'],0,0);
		$this->SetFont('Courier','B','7'); 
		$this->Cell(37,5,"Bank Account Number :",0,0);
		$this->SetFont('Courier','','7'); 
		$this->Cell(66,5,$arrEmp['empAcctNo'],0,1);
		$arrEmpAllow = $this->empAllow($arrEmp['empNo']);
		$numcnt=count($arrEmpAllow);
		$this->Ln(3);
		if (count($arrEmpAllow) > 0) {
			$this->SetFont('Courier','B','7'); 
			$this->Cell(40,5,'ALLOWANCE TYPE','TB','0','L');
			$this->Cell(25,5,'AMOUNT','TB','0','C');
			$this->Cell(25,5,'ALLOW. TAG','TB','0','L');
			$this->Cell(25,5,'REMARKS','TB',1,'L');			
			$this->SetFont('Courier','','7'); 
			foreach($arrEmpAllow as $valAllow) {
				$allowRem = ($valAllow['allowPayTag']=='P')?"Permanent":"Temporary";
				$allowTag = ($valAllow['allowTag']=='M')?" Monthly":" Daily";
				$this->Cell(40,5,$valAllow['allowDesc'],'0','0','L');
				$this->Cell(25,5,$valAllow['allowAmt'],'0','0','C');
				$this->Cell(25,5,$allowTag,'0','0','L');
				$this->Cell(25,5,$allowRem,'0',1,'L');			
			}
		$this->SetFont('Courier','','8'); 
		$this->Cell(195,5,"- - - - - - - - - - - - - - - - - - - - - - END OF RECORD - - - - - - - - - - - - - - - - - - - - - -",0,1,'C');
		for($i=$numcnt;$i<6;$i++){
			$this->Ln();			
		}
		}
		else{
		$this->SetFont('Courier','','8'); 
		$this->Cell(195,5,"- - - - - - - - - - - - - - - - - - - - - - END OF RECORD - - - - - - - - - - - - - - - - - - - - - -",0,1,'C');
		for($i=0;$i<6;$i++){
			$this->Ln();		
		}
		}
	}
	
####Function to set up multiline cell	
	function setMultiLine($w,$s,$field,$obj,$pos){
		$x=$this->GetX();
		$y=$this->GetY();
		$y1=$this->GetY();
		$this->MultiCell($w,$s,$field,$obj,$pos);
		$y2=$this->GetY();
		$yh=$y2-$y1;
		$this->SetXY($x+$w,$this->GetY()-$yh);	
	}
	
####Function to get username	
	function GetUsername($arrUsers,$uid) {
		if ($uid != "") {
			foreach($arrUsers as $val) {
				if($val['userId'] == $uid)
					$uname = $val['empLastName'] . ", " . $val['empFirstName'];
			}
			return $uname;
		} else {
			return " N/A";
		}
	}
	
####Function to format date	
	function valDate($date) {
		if ($date=="") {
			$newDate = "";
		} else {
			$newDate = date("m/d/Y",strtotime($date));
		}
		return $newDate;
	}	
	
####Function to set up footer	
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(195,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}
	
####Function to get Division,Department,Section	
	function getDept($divCode,$deptCode,$sectCode,$level) {
		foreach($this->arrDept as $valDept) {
			switch($level) {
				case 1:
					if ($valDept['divCode']==$divCode && $valDept['deptLevel']==$level) 
						return $valDept['deptDesc'];
				break;
				case 2:
					if ($valDept['divCode']==$divCode && $valDept['deptCode']==$deptCode && $valDept['deptLevel']==$level) 
						return $valDept['deptDesc'];
				break;
				case 3:
					if ($valDept['divCode']==$divCode && $valDept['deptCode']==$deptCode && $valDept['sectCode']==$sectCode && $valDept['deptLevel']==$level) 
						return $valDept['deptDesc'];
				break;
			}
		}
	}
	
####Function to show employee allowance	
	function empAllow($empNo) {
		$sqlAllow = "SELECT tblAllowType.allowDesc, tblAllowance_new.allowAmt, tblAllowance_new.allowPayTag, tblAllowance_new.allowTag FROM tblAllowance_new INNER JOIN tblAllowType ON tblAllowance_new.compCode = tblAllowType.compCode AND tblAllowance_new.allowCode = tblAllowType.allowCode where empNo='$empNo' and allowStat='A'";
		return $this->getArrRes($this->execQry($sqlAllow));
	}	
}

####Initialize object
$pdf=new PDF('P', 'mm', 'LETTER');
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();

####Variable passing
$type = ($_GET['type']==1) ? "hist" : "";
$empNo = $_GET['empNo'];
$cmbDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$Type = $_GET['type'];

####Query to limit the output to encoder
$qryuser=$psObj->getUserLogInInfo($_SESSION['company_code'],$_SESSION['employee_number']);
if($qryuser['userLevel']==3){
	$userview = $qryuser['userId'];
	$ulevel="3";
}
else{
	$ulevel=$qryuser['userLevel'];
}

####Query to show user
$sqlUsers = "SELECT tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblUsers.userId FROM tblUsers INNER JOIN tblEmpMast ON tblUsers.empNo = tblEmpMast.empNo AND tblUsers.compCode = tblEmpMast.compCode where tblEmpMast.compCode='{$_SESSION['company_code']}'";
$arrUsers = $psObj->getArrResI($psObj->execQryI($sqlUsers));

$grp = ($_GET['group'] !='' && $_GET['group'] !='0') ? ",'{$_GET['group']}'" : "";

####Query to show details/data from stored procedures
$emplist = $psObj->getArrRes($psObj->getEmpProoflist($_SESSION['company_code'],$_GET['status'],date('Y-m-d',strtotime($_GET['from'])),date('Y-m-d',strtotime($_GET['to'])),$_SESSION['employee_number'],$_GET['group'],$userview,$ulevel,$cmbDiv,$empDept,$empSect));


####Query to show Division,Department,Section
$sqlDept = "SELECT divCode, deptCode, sectCode, deptDesc, deptLevel FROM tblDepartment where compCode='{$_SESSION['company_code']}'";
$pdf->arrDept = $psObj->getArrRes($psObj->execQry($sqlDept));


####Set up footer
$pdf->AliasNbPages();
$pdf->reportlabel = $reportLabel;
$pdf->company = $psObj->getCompanyName($_SESSION['company_code']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();

####Set up for next page
$pdf->AddPage();

####Set up to get all data/details
	$no=1;
	$divdesc="";
	foreach($emplist as $empListVal){
		$user = $pdf->GetUsername($arrUsers,$empListVal['userReleased']);
		$empName = $empListVal['empLastName'] . ", " . $empListVal['empFirstName'] . " " . $empListVal['empFirstName'][0].".";
		$Status = ($empListVal['stat'] == "H") ? " Held" : "Released";
		$date = ($empListVal['dateReleased'] != "" ? date('Y-m-d',strtotime($empListVal['dateReleased'])) : "    N/A");
		$pdf->Data($no,$empListVal,$date,$user);
		$no++;
	}
	
####Set up to show data	
$pdf->Output('NEW_EMPLOYEE_PROOFLIST.pdf','D');
?>