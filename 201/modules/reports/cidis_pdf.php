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
	var $company;
	var $store;
	var $group;
	var $payperiod;
	var $cutoff;
	var $branch;
####Set up header	
	function Header()
	{
		$this->cutoff = $_GET['costart'] . " - " . $_GET['coend'];	
		$this->group = $_GET['pgroup'];
		$this->payperiod = $_GET['pddate'];
		$this->Image(CIDIS_HEADER_BG,'10','27','125','6');	
		$this->Image(CIDIS_HEADER_BG,'10','75','125','6');			
		if($_SESSION['company_code']==1){
			$this->Image('../../../images/OWI-LOGO.jpg','10','10','30','10');
		}
		elseif($_SESSION['company_code']==2){
			$this->Image(PPCI_LOGO,'10','10','45','10');
		}
		else{
			$this->Image(DF_LOGO,'10','10','45','10');
		}
		$this->SetFont('Arial','B','9'); 
		$this->Cell(125,4,"Company Identification Information Sheet(CIDIS) ",'','1','R');
		$this->SetFont('Arial','','8');
		$this->Cell(63,4,"",'','');
		$this->Cell(62,6,"Human Resource Department",'','1','C');
		$this->Ln(3);
		$this->SetFont('Arial','I','8'); 
		$this->Cell(125,4,"Instruction: Please print details legibly using BLACK ink only.",'','1','L');
		$this->SetFont('Arial','B','8');
		$this->Cell(125,6,'EMPLOYEE INFORMATION','1','1','C');
	}

####Set up details/data	
	function Data($arrNew) {
		foreach($arrNew as $valEmp){
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'EMPLOYEE NUMBER','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(89,6,$valEmp['empNo'],'1','1','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'NAME','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(89,6,$valEmp['empFirstName']." ".$valEmp['empMidName']." ".$valEmp['empLastName'],'1','1','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'POSITION','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(89,6,$valEmp['posDesc'],'1','1','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'DEPARTMENT','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(89,6,$valEmp['deptDesc'],'1','1','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'DATE HIRED','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(89,6,date("F j, Y",strtotime($valEmp['dateHired'])),'1','1','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'BRANCH','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(35,6,$valEmp['brnShortDesc'],'1','0','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(25,6,'BLOOD TYPE','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(28,6,($valEmp['empBloodType']=="0"?"":$valEmp['empBloodType']),'1','1','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'TIN','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(35,6,$valEmp['empTin'],'1','0','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(25,6,'SSS NUMBER','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(28,6,$valEmp['empSssNo'],'1','1','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(125,6,'CONTACT DETAILS IN CASE OF EMERGENCY','1','1','C');
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'CONTACT PERSON','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(89,6,$valEmp['empECPerson'],'1','1','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'ADDRESS','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(89,6,'','1','1','L');
			$this->SetFont('Arial','B','8');
			$this->Cell(35,6,'CONTACT NUMBER','1','0','L');
			$this->SetFont('Arial','','8');
			$this->Cell(1,6,'','1','0');
			$this->Cell(89,6,$valEmp['empECNumber'],'1','1','L');	
			$this->Ln();
			$this->Cell(42,6,'','0','0','');
			$this->Cell(50,45,'(2x2 Photo)','1','0','C');
			$this->Cell(43,6,'','0','1','');			
			$this->Ln(40);
			$this->Cell(125,6,'SIGNATURE:','0','1','L');	
			$this->Cell(10,6,'','0','0','');
			$this->Cell(105,12,'','1','1','');	
			$this->Ln(2);	
			$this->Cell(10,6,'','0','0','');
			$this->Cell(105,12,'','1','1','');		
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
	
	
####Function to set up footer	
/*	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(195,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(235,6,"Generated By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}
*/	
	
}
####Initialize object
$pdf=new PDF('L', 'mm', 'LETTER');
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();

####Query to limit the output to encoder
$qryuser=$psObj->getUserLogInInfo($_SESSION['company_code'],$_SESSION['employee_number']);
if($qryuser['userLevel']==3){
	$userview = $qryuser['userId'];
	$ulevel="3";
}

####Query to show user
$sqlUsers = "SELECT tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblUsers.userId FROM tblUsers INNER JOIN tblEmpMast ON tblUsers.empNo = tblEmpMast.empNo AND tblUsers.compCode = tblEmpMast.compCode where tblEmpMast.compCode='{$_SESSION['company_code']}'";
$arrUsers = $psObj->getArrRes($psObj->execQry($sqlUsers));

####Query to show details/data from stored procedures
$qryNew = "Select tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblEmpMast.dateHired, 
			tblEmpMast.empBday, tblEmpMast.empPosId, tblPosition.posDesc, tblEmpMast.empBrnCode, tblBranch.brnShortDesc, 
			tblEmpMast.empECPerson, tblEmpMast.empECNumber, tblEmpMast.empBloodType, tblEmpMast.empTin, tblEmpMast.empSssNo,
			tblDepartment.deptDesc
		 	from tblEmpMast 
		 	Inner Join tblPosition on tblEmpMast.empPosId=tblPosition.posCode
			Inner Join tblBranch on tblEmpMast.empBrnCode=tblBranch.brnCode
			Inner Join tblDepartment on tblEmpMast.empDiv=tblDepartment.divCode and tblEmpMast.empDepCode=tblDepartment.deptCode
		 	where tblEmpMast.compCode='".$_SESSION['company_code']."' 
			and tblEmpMast.empNo='".$_GET['empno']."'
			and tblDepartment.deptLevel='2'";
$resNew = $psObj->execQry($qryNew);
$arrNew = $psObj->getArrRes($resNew);

####Set up footer
$pdf->AliasNbPages();
$pdf->reportlabel = $reportLabel;
$pdf->company = $psObj->getCompanyName($_SESSION['company_code']);
$pdf->branch = $psObj->getBranchName($_SESSION['company_code'],$_GET['branch']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();

####Set up for next page
$pdf->AddPage();

####Set up to get all data/details
		$pdf->Data($arrNew);
		
####Set up to show data	
$pdf->Output('CIDIS_PROOFLIST.pdf','D');
?>