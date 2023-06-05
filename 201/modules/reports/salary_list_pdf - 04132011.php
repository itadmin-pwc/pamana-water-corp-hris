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
	var $title;
	var $reportlabel;
	var $codeName;
	function Header()
	{
		$this->SetFont('Courier','','9'); 
		$this->Cell(80,5,"Run Date: " . $this->rundate);
		$this->Cell(94,5,$this->company);
		$this->Cell(35,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
		$this->Ln();
		$this->Cell(80,5,"Report ID: EMPSALRYINC");
		if ($_POST['from'] != "" && $_POST['to'] != "") {
			$fromdt = $_POST['from'];
			$todt = $_POST['to'];
			$date = "$fromdt-$todt";
		} 
		$this->Cell(78	,5,$this->title . " " .$date);
		$this->Cell(60,5,$this->reportlabel);
		$this->Ln();
		if ($_POST['code'] == "3" || $_POST['code'] == "2") {
			$this->SetFont('Courier','B','9'); 
			$this->Cell(8,6,'#',0,'','C');
			$this->Cell(20,6,'EMP. NO.',0);
			$this->Cell(30,6,'EMPLOYEE NAME',0);
			$this->Cell(30,6,'EFF. DATE',0);
			$this->Cell(30,6,'POSITION',0);
			$this->Cell(30,6,'OLD SALARY',0);
			$this->Cell(30,6,'NEW SALARY',0);
			$this->Cell(30,6,'AMT INCREASE',0);
			$this->Ln();
		} elseif ($_POST['code'] == "4") {
			$this->SetFont('Courier','B','9'); 
			$this->Cell(8,6,'#',0,'','C');
			$this->Cell(20,6,'EMP. NO.',0);
			$this->Cell(30,6,'EMPLOYEE NAME',0);
			$this->Cell(30,6,'EFF. DATE',0);
			$this->Cell(30,6,'OLD SALARY',0);
			$this->Cell(30,6,'NEW SALARY',0);
			$this->Cell(30,6,'AMT INCREASE',0);
			$this->Cell(30,6,'% Increase',0);
			$this->Ln();
		} elseif ($_POST['code'] == "2") {
			$this->SetFont('Courier','B','9'); 
			$this->Cell(8,6,'#',0,'','C');
			$this->Cell(20,6,'EMP. NO.',0);
			$this->Cell(30,6,'EMPLOYEE NAME',0);
			$this->Cell(30,6,'EFF. DATE',0);
			$this->Cell(40,6,'OLD POSITION',0);
			$this->Cell(40,6,'NEW POSITION',0);
			$this->Cell(40,6,'OLD DIVISION',0);
			$this->Cell(45,6,'NEW DIVISION',0);
			$this->Cell(20,6,'OLD SALARY',0);
			$this->Cell(10,6,'',0,0,'R');
			$this->Cell(20,6,'NEW SALARY',0);
			$this->Ln();
		}
	}
	
	function Data_CBA_MERIT($ctr,$empNo,$empName,$payCat,$effectivitydate,$position,$old_salary,$new_salary,$amtincrease) {
		$this->SetFont('Courier','','9'); 
		$this->Cell(8,6,$ctr,0,'','C');
		$this->Cell(20,6,$empNo,0);
		$this->Cell(30,6,$empName,0);
		$this->Cell(30,6,date("m/d/Y",strtotime($effectivitydate)),0);
		$this->Cell(30,6,$position,0);
		if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
			if ($payCat == 1) 
				$old_salary = $new_salary = $amtincrease = "--";
		}
		
		$this->Cell(25,6,$old_salary,0,0,'R');
		$this->Cell(25,6,$new_salary,0,0,'R');
		$this->Cell(30,6,$amtincrease,0,0,'R');
		$this->Ln();	
	}
	function Data_Salary($ctr,$empNo,$empName,$payCat,$effectivitydate,$position,$old_salary,$new_salary,$amtincrease,$percntincrease) {
		$this->SetFont('Courier','','9'); 
		$this->Cell(8,6,$ctr,0,'','C');
		$this->Cell(20,6,$empNo,0);
		$this->Cell(30,6,$empName,0);
		$this->Cell(30,6,date("m/d/Y",strtotime($effectivitydate)),0);
		$percntincrease = number_format($percntincrease,2) . "%";
		if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
			if ($payCat == 1) 
				$old_salary = $new_salary = $amtincrease = $percntincrease = "--";
		}
		$this->Cell(25,6,$old_salary,0,0,'R');
		$this->Cell(25,6,$new_salary,0,0,'R');
		$this->Cell(30,6,$amtincrease,0,0,'R');
		$this->Cell(30,6,$percntincrease ,0,0,'R');
		$this->Ln();	
	}
	function Data_Promotion($ctr,$empNo,$empName,$payCat,$effectivitydate,$old_position,$new_position,$old_div,$new_div,$old_salary,$new_salary) {
		$this->SetFont('Courier','','9'); 
		$this->Cell(8,6,$ctr,0,'','C');
		$this->Cell(20,6,$empNo,0);
		$this->Cell(30,6,$empName,0);
		$this->Cell(30,6,date("m/d/Y",strtotime($effectivitydate)),0);
		$this->Cell(40,6,$old_position,0,0);
		$this->Cell(40,6,$new_position,0,0);
		$this->Cell(40,6,$old_div,0,0);
		if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
			if ($payCat == 1) 
				$old_salary = $new_salary = "--";
		}
		$this->Cell(45,6,$new_div,0,0);
		$this->Cell(20,6,$old_salary,0,0,'R');
		$this->Cell(10,6,'',0,0,'R');
		$this->Cell(20,6,$new_salary,0,0,'R');
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
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();
if ($_POST['code']!=0) {
	$arrReason = $psObj->getReasonCd($_POST['code'],$sessionVars['compCode']);
	$codeName = $arrReason['reasonDesc'];
	$reasonfilter = "and reasonCd = '{$arrReason['reasonCd']}'";
}
 $type = ($_POST['type']==1) ? "hist" : "";
if ($codeName == "promotion") {
	$ort = "L";
} else {
	$ort = "P";
}
$pdf=new PDF($ort, 'mm', 'LEGAL');
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
	$datefilter = " and tblPAF_PayrollRelated$type.dateadded >= '$fromdt' and tblPAF_PayrollRelated$type.dateadded <='$todt'";
}


$strReason = " AND tblEmpMast.empNo IN (Select empNo from tblPAF_PayrollRelated$type where compCode='{$sessionVars['compCode']}' $reasonfilter $datefilter and new_empMrate>0)";
 $qryIntMaxRec = "SELECT empPayCat,tblEmpMast.empNo, tblPosition.posDesc, tblEmpMast.empLastName, tblEmpMast.empFirstName, 
                      tblEmpMast.empMidName,deptDesc FROM  tblEmpMast LEFT OUTER JOIN
                      tblPosition ON tblEmpMast.empPosId = tblPosition.posCode AND 
                      tblEmpMast.compCode = tblPosition.compCode LEFT OUTER JOIN
                      tblDepartment ON tblEmpMast.compCode = tblDepartment.compCode AND tblEmpMast.empDiv = tblDepartment.divCode and
				 (tblDepartment.deptLevel = '1')
			     WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}' 
				 and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
				 $strReason $empNo1 $empName1 $cmbDiv1 $empDept1 $empSect1 $strPAF
				 order by empDiv,empLastName,empFirstName,empMidName
				 ";

$resEmpList = $psObj->execQry($qryIntMaxRec);
$arrEmpList = $psObj->getArrRes($resEmpList);
$pdf->AliasNbPages();
$pdf->reportlabel = $reportLabel;
$pdf->company = $psObj->getCompanyName($_SESSION['company_code']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();
$pdf->title = $codeName . " Report";
$pdf->codeName = $codeName;
$pdf->AddPage();
$arrPos = $psObj->getPositionDesc();
$arrDiv = $psObj->getDivisionDesc();
	$no=1;
	$divdesc="";
	foreach($arrEmpList as $empListVal){
		$resArrSalary = $psObj->getSalaryData($type," where tblPAF_PayrollRelated$type.empNo='{$empListVal['empNo']}' $reasonfilter $datefilter and new_empMrate>0");
		$empNo = $empListVal['empNo'];
		$divdesc2 = $empListVal['deptDesc'];
		if ($divdesc != $divdesc2 || $divdesc =="") {
			$pdf->SetFont('Courier','B','9'); 
			$pdf->Cell(293,6,$empListVal['deptDesc'].'',0,'','L');
			$pdf->LN();
		}	
		$divdesc = $empListVal['deptDesc'];
		$x=0;
		foreach($resArrSalary as $val) {
			$name = "";
			$empNo = "";
			$q = "";
			if ($x == 0) {
				$q = $no;
				$no++;
				$name = $empListVal['empLastName']. " " . $empListVal['empFirstName'][0] . "." . $empListVal['empMidName'][0].".";
				$empNo = $empListVal['empNo'];
				$x=1;
			}
			switch($codeName) {
				case "CBA Increase":
					$pdf->Data_CBA_MERIT($q,$empNo,$name,$empListVal['empPayCat'],$val['effectivitydate'],$empListVal['posDesc'],number_format($val['old_empMrate'],2),number_format($val['new_empMrate'],2),number_format($val['amtincrease'],2));
				break;
				case "Merit Increase":
					$pdf->Data_CBA_MERIT($q,$empNo,$name,$empListVal['empPayCat'],$val['effectivitydate'],$empListVal['posDesc'],number_format($val['old_empMrate'],2),number_format($val['new_empMrate'],2),number_format($val['amtincrease'],2));
				break;
				case "Salary Increase":
					$pdf->Data_Salary($q,$empNo,$name,$empListVal['empPayCat'],$val['effectivitydate'],$empListVal['posDesc'],number_format($val['old_empMrate'],2),number_format($val['new_empMrate'],2),number_format($val['amtincrease'],2),$val['percentincrease']);
				break;
				case "Promotion":
				$old_pos = $psObj->getDesc('posDesc','posCode',$val['old_posCode'],$arrPos);
				$new_pos = $psObj->getDesc('posDesc','posCode',$val['new_posCode'],$arrPos);
				$old_div = $psObj->getDesc('deptDesc','divCode',$val['old_divCode'],$arrDiv);
				$new_div = $psObj->getDesc('deptDesc','divCode',$val['new_divCode'],$arrDiv);
					$pdf->Data_Promotion($q,$empNo,$name,$empListVal['empPayCat'],$val['effectivitydate'],$old_pos,$new_pos,$old_div,$new_div,number_format($val['old_empMrate'],2),number_format($val['new_empMrate'],2));
				break;				
			}
		}	
	}


$pdf->Output('SALARY_INCREASE_PROOFLIST.pdf','D');



?>
