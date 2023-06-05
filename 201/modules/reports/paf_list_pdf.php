<?php
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
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
		$this->Cell(100,5,"Report ID: EMPMOVEMNT01");
		switch($_POST['pafType']) {
			case "":
				$pafTitle = "";
			break;
			case "empstat":
				$pafTitle = " (Employee Status)";
			break;
			case "branch":
				$pafTitle = " (Branch)";
			break;
			case "position":
				$pafTitle = " (Position)";
			break;
			case "payroll":
				$pafTitle = " (Payroll Related)";
			break;
			case "others":
				$pafTitle = " (Others)";
			break;
			case "allow":
				$pafTitle = " (Allowance)";
			break;
			
		}
		if ($_POST['from'] != "" && $_POST['to'] != "") {
			$fromdt = $_POST['from'];
			$todt = $_POST['to'];
			$date = "$fromdt - $todt";
		} 
		$this->Cell(184,5,$this->reportlabel.'Employee Movement' . $pafTitle);
		$this->Cell(60,5,$this->reportlabel.$date);
		$this->Ln();
		
		$this->SetFont('Courier','B','9'); 
		$this->Cell(8,6,'#',0,'','C');
		$this->Cell(20,6,'EMP. NO.',0);
		$this->Cell(57,6,'EMPLOYEE NAME',0);
		$this->Cell(57,6,'MOVEMENT',0);
		$this->Cell(60,6,'OLD VALUE',0);
		$this->Cell(60,6,'NEW VALUE',0);
		$this->Cell(60,6,'EFFECTIVITY DATE',0);
		$this->Ln();
	}
	function Data($ctr,$empNo,$empName,$Movement,$old_value,$new_value,$effdate) {
		$this->SetFont('Courier','','9'); 
		$this->Cell(8,6,$ctr,0,'','C');
		$this->Cell(20,6,$empNo,0);
		$this->Cell(57,6,$empName,0);
		$this->Cell(57,6,$Movement,0);
		$this->Cell(60,6,$old_value,0);
		$this->Cell(60,6,$new_value,0);
		$this->Cell(60,6,$effdate,0);
		$this->Ln();	
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
$type = ($_POST['type']==1) ? "hist" : "";
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
$cmbDiv = $_POST['empDiv'];
$empDept = $_POST['empDept'];
$empSect = $_POST['empSect'];
$pafType = $_POST['pafType'];
$empNo1 = ($empNo>""?" AND (tblEmpMast.empNo LIKE '{$empNo}%')":"");
$cmbDiv1 = ($cmbDiv>"" && $cmbDiv>0 ? " AND (empDiv = '{$cmbDiv}')":"");
$empDept1 = ($empDept>"" && $empDept>0 ? " AND (empDepCode = '{$empDept}')":"");
$empSect1 = ($empSect>"" && $empSect>0 ? " AND (empSecCode = '{$empSect}')":"");
$empName1=($empName>""?" AND ($nameType LIKE '{$empName}%')":"");
if ($_POST['from'] != "" && $_POST['to'] != "") {
	$fromdt = $_POST['from'];
	$todt = $_POST['to'];
	$datefilter = " and dateupdated >= '$fromdt' and dateupdated <='$todt'";
}
if (empty($pafType) || $pafType =="others") {
	$psObj->arrOthers 		= $psObj->convertArr("tblPAF_Others$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="empstat") {
	$psObj->arrEmpStat 		= $psObj->convertArr("tblPAF_EmpStatus$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="branch") {	
	$psObj->arrBranch 		= $psObj->convertArr("tblPAF_Branch$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="position") {	
	$psObj->arrPosition 		= $psObj->convertArr("tblPAF_Position$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="payroll") {
	$psObj->arrPayroll 		= $psObj->convertArr("tblPAF_PayrollRelated$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="allow") {
	$psObj->arrAllow 		= $psObj->convertArr("tblPAF_Allowance$type", " $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
$arrPAF = array_unique(array_merge($psObj->arrOthers,$psObj->arrOthers,$psObj->arrEmpStat,$psObj->arrBranch,$psObj->arrPosition,$psObj->arrPayroll,$psObj->arrAllow ));
$strPAF = implode(",",$arrPAF);
$strPAF = ($strPAF != "" ? " AND empNo IN ($strPAF)" : "");
$qryIntMaxRec = "SELECT tblEmpMast.empNo,tblEmpMast.empLastName,tblEmpMast.empFirstName,
				tblEmpMast.empMidName,tblDepartment.deptShortDesc 
				FROM tblEmpMast INNER JOIN tblDepartment ON 
				tblEmpMast.compCode = tblDepartment.compCode 
				AND tblEmpMast.empDiv = tblDepartment.divCode 
			    WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}'
			    AND tblDepartment.deptLevel = '1' 
				and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
				$empNo1 $empName1 $cmbDiv1 $empDept1 $empSect1 $strPAF
				order by empDiv,empLastName,empFirstName,empMidName
				 ";

$resEmpList = $psObj->execQry($qryIntMaxRec);
$arrEmpList = $psObj->getArrRes($resEmpList);
$pdf->AliasNbPages();
$pdf->reportlabel = $reportLabel;
$pdf->company = $psObj->getCompanyName($_SESSION['company_code']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();
$pdf->AddPage();
	$no=1;
	$divdesc="";
	foreach($arrEmpList as $empListVal){
		$resArrOthers = $psObj->getPAF_others($empListVal['empNo'],$pafType,$datefilter,$type);
		$ctr=count($resArrOthers['value1']);
		$empNo = $empListVal['empNo'];
		$divdesc2 = $empListVal['deptShortDesc'];
		if ($divdesc != $divdesc2 || $divdesc =="") {
			$pdf->SetFont('Courier','B','9'); 
			$pdf->Cell(293,6,$empListVal['deptShortDesc'],0,'','L');
			$pdf->LN();
		}	
		$divdesc = $empListVal['deptShortDesc'];
		for($x=0;$x<$ctr; $x++) {
			$name = "";
			$empNo = "";
			$q = "";
			if ($x == 0) {
				$q = $no;
				$no++;
				$name = $empListVal['empLastName']. " " . $empListVal['empFirstName'][0] . "." . $empListVal['empMidName'][0].".";
				$empNo = $empListVal['empNo'];
			}		
			$pdf->Data($q,$empNo,$name,$resArrOthers['field'][$x],$resArrOthers['value1'][$x],$resArrOthers['value2'][$x],date("m/d/Y",strtotime($resArrOthers['effdate'][$x])));
		}	
	}


$pdf->Output('PAF.pdf','D');

?>
