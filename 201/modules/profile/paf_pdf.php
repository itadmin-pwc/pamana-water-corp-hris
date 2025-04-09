<?php
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("profile_paf_obj.php");
include("../../../includes/pdf/fpdf.php");
class PDF extends FPDF
{
	var $printedby;
	var $company;
	var $rundate;
	var $table;
	var $reportlabel;
	var $arrPayPd;
/*	function Header()
	{
		$this->SetFont('Courier','','9'); 
		$this->Cell(100,5,"Run Date: " . $this->rundate);
		$this->Cell(200,5,$this->company);
		$this->Cell(35,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
		$this->Ln();
		$this->Cell(100,5,"Report ID: POSTEDPAF");
		if ($_POST['from'] != "" && $_POST['to'] != "") {
			$fromdt = $_POST['from'];
			$todt = $_POST['to'];
			$date = "$fromdt - $todt";
		} 
		$this->Cell(184,5,$this->reportlabel.'Posted PAF');
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
	}*/
	function empInfo($arrInfo) {
		$this->SetFont('arial','B',10);
		$this->Cell(200,6,$this->company,0,1,'C');
		$this->Cell(200,6,$this->reportlabel,0,1,'C');
		$this->Ln(4);
		$this->SetFont('arial','',9);

		$this->Cell(30,6,'Name');
		$this->Cell(90,6,$arrInfo['empLastName'] . ', ' . $arrInfo['empFirstName'] . ' ' . $arrInfo['empMidName'],'B',0);
		$this->Cell(30,6,' Date Prepared');
		$this->Cell(40,6,date('m/d/Y'),'B',1);

		$this->Cell(30,6,'Position',0,0);
		$this->Cell(90,6,$arrInfo['posDesc'],'B',0);
		$this->Cell(30,6,' Date Hired',0,0);
		$this->Cell(40,6,date('m/d/Y',strtotime($arrInfo['dateHired'])),'B',1);

		$this->Cell(30,6,'Department',0,0);
		$this->Cell(90,6,$arrInfo['deptShortDesc'],'B',0);
		$this->Cell(30,6,' Branch',0,0);
		$this->Cell(40,6,$arrInfo['brnShortDesc'],'B',1);
		$this->Ln(2);
		$this->Cell(200,6,'You are hereby notified of the following actions affecting your employment.',0,1,'C');
		$this->Ln(2);
		$this->SetFont('arial','',8);
		$this->Cell(46,5,'Nature of Action',1,0);
		$this->Cell(67,5,'From',1,0,'C');
		$this->Cell(67,5,'To',1,0,'C');
		$this->Cell(20,5,'Effectivity Date',1,1,'C');
	}
	
	function Data($Movement,$old_value,$new_value,$refNo,$effdate) {
		if($new_value!="Resigned" && $new_value!="End of Contract" && $new_value!="Terminated" && $new_value!="Terminated for a cause" && $new_value!="Absent without leave"){
			$this->Cell(46,5,$Movement,1,0);
			$this->Cell(67,5,$old_value,1,0);
			$this->Cell(67,5,$new_value,1,0);
			$this->Cell(20,5,date('m/d/Y', strtotime($effdate)),1,1,'C');
		}
		else{
			$this->Cell(46,5,"",1,0);
			$this->Cell(67,5,"",1,0);
			$this->Cell(67,5,"",1,0);
			$this->Cell(20,5,"",1,1,'C');
		}
	}
	
	function Separation($Stat,$Remarks,$effDate) {
		$this->Cell(200,2,'','T',1,1);
		$this->SetFont('arial','B',8);
		$this->Cell(200,6,'SEPARATION',1,1,'C');
		$this->Cell(200,2,'','LR',1,'C');
		$this->SetFont('arial','',8);
		$this->Cell(10,3,'','L');	
		if ($Stat=='End of contract') {
			$this->Cell(3,3,$this->box(1));	
		} else {
			$this->Cell(3,3,$this->box(0));	
		}
		$this->Cell(56,3,'Expiration of Contract');	
		if ($Stat=='Resigned') {
			$this->Cell(3,3,$this->box(1));	
		} else {
			$this->Cell(3,3,$this->box(0));	
		}
		$this->Cell(56,3,'Resignation');	
		if ($Stat=='Terminated for a cause') {
			$this->Cell(3,3,$this->box(1));	
		} else {
			$this->Cell(3,3,$this->box(0));	
		}	
		$this->Cell(60,3,'Termination for Cause','R',1,1);	
		$this->Cell(200,2,'','LR',1,1);
		$this->Cell(38,5,'Effectivity Date: ','L',0,'R');
		$this->Cell(150,5,$effDate,'B',0);
		$this->Cell(12,5,'','R',1,'C');
		$this->Cell(200,2,'','LBR',1,1);
		$this->SetFont('arial','B',8);
		$this->Cell(200,6,'PARTICULARS',1,1,'C');
		$this->SetFont('arial','',8);
		
		if($Remarks==1){
			$payreason="Promotion";
		}
		elseif($Remarks==2){
			$payreason="Merit Increase";	
		}
		elseif($Remarks==5){
			$payreason="Gov't Mandate";	
		}
		elseif($Remarks==4){
			$payreason="Salary Increase";	
		}
		elseif($Remarks==6){
			$payreason="Alignment";	
		}
		elseif($Remarks==7){
			$payreason="Regularization";	
		}
		elseif($Remarks==8){
			$payreason="Probationary";	
		}
		else{
			$payreason=$Remarks;	
		}

		$this->MultiCell(200,12,$payreason . ' ('.$arrInfo['remarks'].')',1,'C');
		$this->SetFont('arial','',8);
		
	}
	function box($tag) {
		$this->Cell(3,3,'',1,0,1,$tag);
	}
	function Approval($arrInfo) {
		$signee="";
		$sql=$this->checkDivision($arrInfo['empDiv'],$arrInfo['empDepCode']);
		foreach($sql as $sqlval=>$val){
			if($val['divCode']==7 && $val['deptCode']==1){
				$signee="";	
			}	
			else{
				$signee=$this->getSignatory($val['divCode'],$val['deptCode']);
			}
		}
		
		$this->Cell(200,2,'','T',1,1);
		$this->SetFont('arial','B',8);
		$this->Cell(170,6,'APPROVAL',1,0,'C');
		$this->Cell(30,6,'CONFORME','TBR',1,'C');
		$this->Cell(200,2,'','T',1,1);

		$this->SetFont('arial','',8);
		$this->Cell(35,5,'Dept Manager','LTR',0,'C');
		$this->Cell(33,5,'Branch Manager/Appointee','LTR',0,'C');
		$this->Cell(32,5,'HR Manager','LTR',0,'C');
		$this->Cell(35,5,'President','LTR',0,'C');
		$this->Cell(35,5,'Chairman','LTR',0,'C');
		$this->Cell(30,5,'','LTR',1);

		$this->Cell(35,5,'','LR',0,'C');
		$this->Cell(33,5,'','LR',0,'C');
		$this->Cell(32,5,'','LR',0,'C');
		$this->Cell(35,5,'','LR',0,'C');
		$this->Cell(35,5,'','LR',0,'C');
		$this->Cell(30,5,'','LR',1);
		$this->SetFont('arial','',6);
		$this->Cell(35,5,'','LR',0,'C');
		$this->Cell(33,5,strtoupper($arrInfo['brnSignatory']),'LR',0,'C');
		$this->Cell(32,5,'HERMA MAE M. PAGBILAO','LR',0,'C'); //HR
		$this->Cell(35,5,'PAULA JANE SALON','LR',0,'C'); //President
		$this->Cell(35,5,'','LR',0,'C');
		//$this->Cell(30,5,strtoupper($arrInfo['empFirstName'][0].$arrInfo['empMidName'][0].' '.$arrInfo['empLastName']),'LR',1,'C');
		$this->Cell(30,5,$arrInfo['empFirstName'] . ' ' . substr($arrInfo['empMidName'], 0, 1) . '. ' . $arrInfo['empLastName'],'LR',1,'C');
		
		$this->Cell(35,5,'Signature/Date',1,0,'C');
		$this->Cell(33,5,'Signature/Date',1,0,'C');
		$this->Cell(32,5,'Signature/Date',1,0,'C');
		$this->Cell(35,5,'Signature/Date',1,0,'C');
		$this->Cell(35,5,'Signature/Date',1,0,'C');
		$this->Cell(30,5,'Signature/Date',1,1,'C');
	}
	
	function checkDivision($divcode,$deptcode){
		$sql="Select * from tblDepartment where divCode='{$divcode}' and deptCode='{$deptcode}'";
		$sqlqry=$this->getArrRes($this->execQry($sql));
		return $sqlqry;	
	}	
	function getSignatory($divcode,$deptcode){
		$sqlsig="Select * from tblDepartment where divCode='{$divcode}' and deptCode='{$deptcode}'";
		$qrysig=$this->getSqlAssoc($this->execQry($sqlsig));
		return $qrysig['signatoryName'];	
	}
}

$pdf=new PDF('P', 'mm', 'LETTER');
$psObj=new pafObj($_GET,$_SESSION);
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
$cmbDiv = $_POST['empDiv'];
$empDept = $_POST['empDept'];
$empSect = $_POST['empSect'];
$pafType = $_POST['pafType'];
$empNo1 = ($empNo>""?" AND (tblEmpMast.empNo LIKE '{$empNo}%')":"");
$cmbDiv1 = ($cmbDiv>"" && $cmbDiv>0 ? " AND (empDiv = '{$cmbDiv}')":"");
$empDept1 = ($empDept>"" && $empDept>0 ? " AND (empDepCode = '{$empDept}')":"");
$empSect1 = ($empSect>"" && $empSect>0 ? " AND (empSecCode = '{$empSect}')":"");
$empName1=($empName>""?" AND ($nameType LIKE '{$empName}%')":"");
$refNo ='0';
for($i=0;$i<$_GET['chCtr'];$i++) {
	$arrRefNo = explode(',',$_GET['chPAF'.$i]);
	if ($arrRefNo[1] != '')
		$refNo .= ','.$arrRefNo[1];
}
if ($_GET['pafStat'] != 'P') {
	$filter = " AND stat='{$_GET['pafStat']}' AND refNo IN ($refNo)";
	$allowfilter = " AND stat='{$_GET['pafStat']}' AND refNo IN ($refNo)";
	$type = "";
	if ($_GET['pafStat'] == 'H') 
		$reportLabel = 'PAF Proof List';
	else
		$reportLabel = 'Released PAF';
} else {
	$filter = " AND  refNo IN ($refNo)";
	$allowfilter = " AND refNo IN ($refNo)";
	$type = "hist";	
	$reportLabel = 'Posted PAF';
}
if (empty($pafType) || $pafType =="others") {
	$psObj->arrOthers 		= $psObj->convertArr("tblPAF_Others$type", " $filter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
	
}
if (empty($pafType) || $pafType =="empstat") {
	$psObj->arrEmpStat 		= $psObj->convertArr("tblPAF_EmpStatus$type", " $filter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="branch") {	
	$psObj->arrBranch 		= $psObj->convertArr("tblPAF_Branch$type", " $filter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="position") {	
	$psObj->arrPosition 		= $psObj->convertArr("tblPAF_Position$type", " $filter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="payroll") {
	$psObj->arrPayroll 		= $psObj->convertArr("tblPAF_PayrollRelated$type", " $filter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
if (empty($pafType) || $pafType =="allow") {
	$psObj->arrAllow 		= $psObj->convertArr("tblPAF_Allowance$type", "  $allowfilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1");
}
$arrPAF = array_unique(array_merge($psObj->arrOthers,$psObj->arrOthers,$psObj->arrEmpStat,$psObj->arrBranch,$psObj->arrPosition,$psObj->arrPayroll,$psObj->arrAllow ));
//print_r(array_values ($arrPAF));
$strPAF = implode(",",$arrPAF);
$strPAF = ($strPAF != "" ? " AND empNo IN ($strPAF)" : "");
 $qryIntMaxRec = "SELECT  tblEmpMast.empDiv,tblEmpMast.empDepCode,tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, 			 
 				tblEmpMast.empMidName, tblDepartment.deptShortDesc, tblEmpMast.dateHired, tblPosition.posDesc, tblEmpMast.empSex,  
				tblBranch.brnShortDesc, brnSignatory
				FROM  tblEmpMast 
				LEFT OUTER JOIN tblDepartment ON tblEmpMast.empDepCode = tblDepartment.deptCode AND 
				tblEmpMast.compCode = tblDepartment.compCode AND tblEmpMast.empDiv = tblDepartment.divCode 
				LEFT OUTER JOIN tblPosition ON tblEmpMast.empPosId = tblPosition.posCode 
				AND tblEmpMast.compCode = tblPosition.compCode 
				LEFT OUTER JOIN tblBranch ON tblEmpMast.compCode = tblBranch.compCode 
				AND tblEmpMast.empBrnCode = tblBranch.brnCode
				WHERE  tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblDepartment.deptLevel = '2'
				$empNo1 $empName1 $cmbDiv1 $empDept1 $empSect1 $strPAF
				order by empDiv,empLastName,empFirstName,empMidName
				 ";
$resEmpList = $psObj->execQry($qryIntMaxRec);
$arrEmpList = $psObj->getArrRes($resEmpList);

$pdf->AliasNbPages();
$pdf->reportlabel = 'PERSONNEL ACTION FORM';
$pdf->company = $psObj->getCompanyName($_SESSION['company_code']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();
	
	foreach($arrEmpList as $empListVal){
		$pdf->AddPage();
		$resArrOthers = $psObj->getPAF_others($empListVal['empNo'],$pafType,$datefilter." $filter",$type);
		$ctr=count($resArrOthers['value1']);
		$pdf->empInfo($empListVal);
		$Stat="";
		$Remarks="";
		$effDate="";
		for($x=0;$x<$ctr; $x++) {
			$pdf->Data($resArrOthers['field'][$x],$resArrOthers['value1'][$x],$resArrOthers['value2'][$x],$resArrOthers['refno'][$x],date("Y-m-d",strtotime($resArrOthers['effdate'][$x])));
			if ($resArrOthers['field'][$x]=="Nature of separation") {
				if ($resArrOthers['value2'][$x]=="Resigned" || $resArrOthers['value2'][$x]=="End of contract"  || $resArrOthers['value2'][$x]=="Terminated for a cause") {
					$Stat=$resArrOthers['value2'][$x];
					$Remarks=$resArrOthers['remarks'][$x];
					$effDate=date("Y-m-d",strtotime($resArrOthers['effdate'][$x]));
				}
			}
			elseif($resArrOthers['field'][$x]=="Salary"){
				$Remarks.=$resArrOthers['remarks'][$x];
			}
		}
		$pdf->Separation($Stat,$Remarks,$effDate);
		$pdf->Approval($empListVal);
	}
$pdf->Output('PAF_LIST_REPORT.pdf','D');
?>