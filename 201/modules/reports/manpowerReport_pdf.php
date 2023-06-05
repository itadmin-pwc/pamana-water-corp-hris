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
	public $company;
	public $rundate;	
	public $dfrom;
	public $dto;
	public $type;
	public $branch;
	public $cuttoff;
	public $payperiod;
####Set up header	
	function Header()
	{
		$this->cuttoff = $_GET['costart'] . " - " . $_GET['coend'];	
		$this->dfrom = $_GET['dfrom'];	
		$this->dto = $_GET['dto'];
		$this->type = $_GET['type'];
		$this->payperiod = $_GET['pddate'];
		
		$this->SetFont('Arial','B','8'); 
		$this->Cell(50,4,'MANPOWER REPORT','','1','');
		$this->Cell(30,4,'COMPANY                 :','','','');		
		$this->SetFont('Arial','','8'); 
		$this->Cell(60,4,$this->company,'','1','');
		$this->SetFont('Arial','B','8'); 
		$this->Cell(30,4,'STORE                       :','','','');
		$this->SetFont('Arial','','8'); 
		$this->Cell(60,4,$this->branch,'','1','');
		$this->SetFont('Arial','B','8'); 		
		$this->Cell(30,4,'GROUP                      :','','','');
		$this->SetFont('Arial','','8'); 
		$this->Cell(60,4,$_GET['pgroup'],'','1','');	
		$this->SetFont('Arial','B','8'); 	
		$this->Cell(30,4,'PAYROLL PERIOD   :','','','');
		$this->SetFont('Arial','','8'); 	
		$this->Cell(60,4,$this->payperiod,'','1','');	
		$this->SetFont('Arial','B','8'); 
		$this->Cell(30,4,'CUT OFF                   :','','','');
		$this->SetFont('Arial','','8'); 
		$this->Cell(60,4,$this->cuttoff,'','1','');		
		$this->Ln();
	}

####Set up details/data	
	function Data($arrNewEmp,$arrSepEmp, $arrData, $arrNewEmpPrev, $arrSepEmpPrev, $arrPrevHC, $arrCurHC){
		$this->Cell(200,5,'I. NEWLY HIRED EMPLOYEES','','1','L');
		$this->Ln(2);
		$this->SetFont('Arial','B','8'); 
		$this->Cell(55,5,'NAME','1','','C');
		$this->Cell(100,5,'POSITION','1','','C');
		$this->Cell(100,5,'COMPANY/BRANCH','1','','C');
		$this->Cell(30,5,'DATE HIRED','1','','C');
		$this->Cell(50,5,'REMARKS','1','1','C');
		$cnt=0;
		foreach($arrNewEmp as $valNewEmp){
			$cnt++;				
			$this->SetFont('Arial','','8'); 
			$this->Cell(5,5,$cnt,'1','','C');
			$this->Cell(50,5,$valNewEmp['empName'],'1','','L');
			$this->Cell(100,5,$valNewEmp['posDesc'],'1','','L');
			$this->Cell(100,5,$valNewEmp['brnDesc'],'1','','L');
			$this->Cell(30,5,$valNewEmp['datehired'],'1','','C');
			$this->Cell(50,5,'','1','1','C');			
		}
		$this->SetFont('Arial','B','8'); 
		$this->Cell(335,5,$cnt." Total Newly Hired Employees",'1','1','L');
		$this->Ln(10);
		
		
		$this->Cell(200,5,'II. EOC/AWOL/TERMINATED EMPLOYEES (For Last Pay Computation)','','1','L');
		$this->Ln(2);
		$this->SetFont('Arial','B','8'); 
		$this->Cell(55,5,'NAME','1','','C');
		$this->Cell(100,5,'POSITION','1','','C');
		$this->Cell(100,5,'COMPANY/BRANCH','1','','C');
		$this->Cell(30,5,'DATE SEPARATED','1','','C');
		$this->Cell(50,5,'REMARKS','1','1','C');
		$cnts=0;
		foreach($arrSepEmp as $valSepEmp){
			$qrySep = "Select tblSeparatedEmployees.empNo, tblSeparatedEmployees.natureCode,tblNatures.Description 
			from tblSeparatedEmployees
			Inner Join tblNatures on tblSeparatedEmployees.natureCode=tblNatures.natureCode
			where tblSeparatedEmployees.empNo='".$valSepEmp['empNo']."'";
			$resQry=$this->getSqlAssoc($this->execQry($qrySep));
			$cnts++;				
			$this->SetFont('Arial','','8'); 
			$this->Cell(5,5,$cnts,'1','','C');
			$this->Cell(50,5,$valSepEmp['empName'],'1','','L');
			$this->Cell(100,5,$valSepEmp['posDesc'],'1','','L');
			$this->Cell(100,5,$valSepEmp['brnDesc'],'1','','L');
			$this->Cell(30,5,($valSepEmp['resDate']==""?$valSepEmp['endDate']:$valSepEmp['resDate']),'1','','C');
			$this->Cell(50,5,$resQry['Description'],'1','1','C');			
		}
		$this->SetFont('Arial','B','8'); 
		$this->Cell(335,5,$cnts." Total Separated Employees",'1','1','L');
		$this->Ln(10);


		$this->Cell(200,5,'III. PERSONNEL UPDATES(PAF)','','1','L');
		$this->Ln(2);
		$this->SetFont('Arial','B','8'); 
		$this->Cell(55,5,'NAME','1','','C');
		$this->Cell(40,5,'UPDATE','1','','C');
		$this->Cell(90,5,'FROM','1','','C');
		$this->Cell(126,5,'TO','1','','C');
		$this->Cell(24,5,'EFFECTIVITY','1','1','C');
		$cntPAF=0;
		foreach((array)$arrData  as $val){
			$cntPAF++;	
			$this->SetFont('Arial','','8'); 
			$this->Cell(5,5,$cntPAF,'1','','C');
			$this->Cell(50,5,$val[1],'1','','L');
			$this->Cell(40,5,$val[2],'1','');
			$this->Cell(90,5,$val[3],'1','');
			$this->Cell(126,5,$val[4],'1','');
			$this->Cell(24,5,$val[5],'1','1','C');			
		}
		$this->SetFont('Arial','B','8'); 
		$this->Cell(335,5,$cntPAF." Total Personnel Updates",'1','1','L');
		$this->Ln(10);


		$this->Cell(200,5,'IV. MANPOWER HEADCOUNT','','1','L');
		$this->Ln(2);
		$this->SetFont('Arial','B','8');
		foreach($arrPrevHC as $valPrevHC){
			$prevHC = $valPrevHC['prevCN'];	
		} 
		foreach($arrCurHC as $valCurHC){
			$curHC = $valCurHC['currCN'];	
		} 
		
		$this->Cell(35,5,'','1','','C');
		$this->Cell(25,5,'HIRED','1','','C');
		$this->Cell(25,5,'SEPARATED','1','','C');
		$this->Cell(30,5,'TOTAL HEAD COUNT','1','1','C');
		$this->Cell(35,5,'PREVIOUS CUT-OFF','1','','C');	
		$this->SetFont('Arial','','8');	
		$this->Cell(25,5,$arrNewEmpPrev['n'],'1','','C');
		$this->Cell(25,5,$arrSepEmpPrev['cnt'],'1','0','C');
		$this->SetFont('Arial','B','8');
		$this->Cell(30,5,"---",'1','1','C');	
		$this->Cell(35,5,'CURRENT CUT-OFF','1','','C');	
		$this->SetFont('Arial','','8');	
		$this->Cell(25,5,$cnt,'1','','C');
		$this->Cell(25,5,$cnts,'1','','C');
		$this->SetFont('Arial','B','8');
		$this->Cell(30,5,(int)$curHC,'1','1','C');
		$this->Ln(6);
		$this->SetFont('Arial','','8');
		$this->Cell(40,10,'Prepared by:','','','L');
		$this->Cell(35,10,'Noted by:','','1','C');
		$this->SetFont('Arial','B','8');
		//$this->Cell(3,1,'','','','');
		$this->Ln(5);
		$this->Cell(35,1,$this->printedby['empFirstName']." ".$this->printedby["empLastName"],'','1','C');
		$this->Cell(3,1,'','','','C');
		$this->Cell(30,1,'_____________________','','','C');
		$this->Cell(10,5,'','','','C');
		$this->Cell(30,1,'_____________________','','','C');
		$this->Cell(10,5,'','','','C');
		$this->Cell(30,1,'_____________________','','1','C');
		$this->Cell(40,6,'HR Supervisor/Assistant','','','L');
		$this->Cell(35,6,'HR Officer','','','C');
		$this->Cell(5,2,'','','','C');
		$this->Cell(35,6,' Timekeeper','','1','L');
		$this->Ln(6);
		$this->Cell(83,5,'','','','C');
		$this->Cell(30,5,'_____________________','','1','C');
		$this->Cell(80,2,'','','','C');
		$this->Cell(35,2,' Training','','','L');
		$this->Ln(6);
		$this->Cell(83,5,'','','','C');
		$this->Cell(30,5,'_____________________','','1','C');
		$this->Cell(80,2,'','','','C');
		$this->Cell(35,2,' Comp&Ben','','','L');
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
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(335,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(335,6,"Generated By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}
}
####Initialize object
$pdf=new PDF('L', 'mm', 'LEGAL');
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

####Query to show new employees(Previous cut-off)
$qryNewEmpPrev = "Select count(empNo) as n
			from tblEmpMast_New
			Inner Join tblPosition on tblEmpMast_New.empPosId=tblPosition.posCode
			Inner Join tblBranch on tblEmpMast_New.empBrnCode=tblBranch.brnCode
			Where tblEmpMast_New.stat='R' and tblEmpMast_New.dateReleased 
			between '".date('Y-m-d',strtotime($_GET['costartPrev']))."' and '".date('Y-m-d',strtotime($_GET['coendPrev']))."' 
			and tblEmpMast_New.empBrnCode='".$_GET['branch']."' and empPayGrp='".$_GET['pgroup']."'";
$resNewEmpPrev = $psObj->execQry($qryNewEmpPrev);
$arrNewEmpPrev = $psObj->getSqlAssoc($resNewEmpPrev);

####Query to show separated employees(Previous cut-off)
$qrySepEmpPrev = "Select count(empNo) as cnt
			from tblEmpMast
			Inner Join tblPosition on tblEmpMast.empPosId=tblPosition.posCode
			Inner Join tblBranch on tblEmpMast.empBrnCode=tblBranch.brnCode
			Where tblEmpMast.empStat in ('RS','IN') and ((tblEmpMast.dateResigned 
			between '".date('Y-m-d',strtotime($_GET['costartPrev']))."' and '".date('Y-m-d',strtotime($_GET['coendPrev']))."')
			or (tblEmpMast.endDate 
			between '".date('Y-m-d',strtotime($_GET['costartPrev']))."' and '".date('Y-m-d',strtotime($_GET['coendPrev']))."')) 
			and tblEmpMast.empBrnCode='".$_GET['branch']."' and empPayGrp='".$_GET['pgroup']."'";
$resSepEmpPrev = $psObj->execQry($qrySepEmpPrev);
$arrSepEmpPrev = $psObj->getSqlAssoc($resSepEmpPrev);

####Query to show new employees
$qryNewEmp = "Select Concat(tblEmpMast_New.empLastName,', ',tblEmpMast_New.empFirstName,' ',SUBSTRING(tblEmpMast_New.empMidName,1,1)) as empName, 
			tblPosition.posDesc, tblBranch.brnDesc, date_format(tblEmpMast_New.dateHired,'%d/%m/%Y') as datehired, 
			tblEmpMast_New.dateReleased, tblEmpMast_New.stat
			from tblEmpMast_New
			Inner Join tblPosition on tblEmpMast_New.empPosId=tblPosition.posCode
			Inner Join tblBranch on tblEmpMast_New.empBrnCode=tblBranch.brnCode
			Where tblEmpMast_New.stat='R' and tblEmpMast_New.dateReleased 
			between '".date('Y-m-d',strtotime($_GET['costart']))."' and '".date('Y-m-d',strtotime($_GET['coend']))."' 
			and tblEmpMast_New.empBrnCode='".$_GET['branch']."' and empPayGrp='".$_GET['pgroup']."'";
$resNewEmp = $psObj->execQry($qryNewEmp);
$arrNewEmp = $psObj->getArrRes($resNewEmp);

####Query to show separated employees
$qrySepEmp = "Select tblEmpMast.empNo,Concat(tblEmpMast.empLastName,', ',tblEmpMast.empFirstName,' ',SUBSTRING(tblEmpMast.empMidName,1,1)) as empName, 
			tblPosition.posDesc, tblBranch.brnDesc, date_format(tblEmpMast.dateResigned,'%d/%m/%Y') as resDate, 
			tblEmpMast.empStat, date_format(tblEmpMast.endDate,'%d/%m/%Y') as endDate
			from tblEmpMast
			Inner Join tblPosition on tblEmpMast.empPosId=tblPosition.posCode
			Inner Join tblBranch on tblEmpMast.empBrnCode=tblBranch.brnCode
			Where tblEmpMast.empStat in ('RS','IN') and ((tblEmpMast.dateResigned 
			between '".date('Y-m-d',strtotime($_GET['costart']))."' and '".date('Y-m-d',strtotime($_GET['coend']))."')
			or (tblEmpMast.endDate between '".date('Y-m-d',strtotime($_GET['costart']))."' 
				and '".date('Y-m-d',strtotime($_GET['coend']))."')) 
			and tblEmpMast.empBrnCode='".$_GET['branch']."' and empPayGrp='".$_GET['pgroup']."'";
$resSepEmp = $psObj->execQry($qrySepEmp);
$arrSepEmp = $psObj->getArrRes($resSepEmp);

#####Previous cut off total head count (active employees)
//$qryPrdPrev = "Select pdYear, pdNumber from tblPayPeriod where pdFrmDate='".$_GET['costartPrev']."' and  pdToDate='".$_GET['coendPrev']."' group by pdNumber,pdYear";
//$qryResPrev = $psObj->execQry($qryPrdPrev);
//$arrResPrev = $psObj->getSqlAssoc($qryResPrev);
//
//$qryPreveHC = "Select count(*) as prevCN from tblPayrollSummaryHist where pdYear='".$arrResPrev['pdYear']."' and pdNumber='".$arrResPrev['pdNumber']."' and empBrnCode='".$_GET['branch']."' and payCat<>'9'";
//$resPrevHC = $psObj->execQry($qryPreveHC);
//$arrPrevHC = $psObj->getArrRes($resPrevHC);


$qryPreveHC = "Select count(tblEmpMast_New.empNo) as prevCN from tblEmpMast_New where tblEmpMast_New.stat='R' 
			and tblEmpMast_New.dateReleased between '".date('Y-m-d',strtotime($_GET['costartPrev']))."' 
				and '".date('Y-m-d',strtotime($_GET['coendPrev']))."' 
			and tblEmpMast_New.empBrnCode='".$_GET['branch']."' and empPayGrp='".$_GET['pgroup']."'";
$resPrevHC = $psObj->execQry($qryPreveHC);
$arrPrevHC = $psObj->getArrRes($resPrevHC);

#####Current cut off total head count (active employees)	
$qryCurHC = "Select count(*) as currCN from tblEmpMast where empStat='RG' and empBrnCode='".$_GET['branch']."' and empPayGrp='".$_GET['pgroup']."'";
$resCurHC = $psObj->execQry($qryCurHC);
$arrCurHC = $psObj->getArrRes($resCurHC);

			
####Query for PAF
$type = "hist";
$group = $_GET['pgroup'];
if ($_GET['costart'] != "" && $_GET['coend'] != "") {
	$fromdt = date("Y-m-d",strtotime($_GET['costart']));
	$todt = date("Y-m-d",strtotime($_GET['coend']));
	$datefilter1 = " and tblPAF_Others$type.dateupdated >= '$fromdt' and tblPAF_Others$type.dateupdated <='$todt'";
	$datefilter2 = " and tblPAF_EmpStatus$type.dateupdated >= '$fromdt' and tblPAF_EmpStatus$type.dateupdated <='$todt'";
	$datefilter3 = " and tblPAF_Branch$type.dateupdated >= '$fromdt' and tblPAF_Branch$type.dateupdated <='$todt'";
	$datefilter4 = " and tblPAF_Position$type.dateupdated >= '$fromdt' and tblPAF_Position$type.dateupdated <='$todt'";
	$datefilter5 = " and tblPAF_PayrollRelated$type.dateupdated >= '$fromdt' and tblPAF_PayrollRelated$type.dateupdated <='$todt'";
	$datefilter6 = " and tblPAF_Allowance$type.dateupdated >= '$fromdt' and tblPAF_Allowance$type.dateupdated <='$todt'";
	$datefilter  = " and dateupdated >= '$fromdt' and dateupdated <='$todt'";
	
}
	$psObj->arrOthers 	=  $psObj->convertArr("tblPAF_Others$type", "  $datefilter1 AND empPayGrp='$group'");
	$psObj->arrEmpStat 	=  $psObj->convertArr("tblPAF_EmpStatus$type", "  $datefilter2 AND empPayGrp='$group'");
	$psObj->arrBranch 	=  $psObj->convertArr("tblPAF_Branch$type", "  $datefilter3 AND empPayGrp='$group'");
	$psObj->arrPosition =  $psObj->convertArr("tblPAF_Position$type", "  $datefilter4 AND empPayGrp='$group'");
	$psObj->arrPayroll 	=  $psObj->convertArr("tblPAF_PayrollRelated$type", "  $datefilter5 AND empPayGrp='$group'");
	$psObj->arrAllow 	=  $psObj->convertArr("tblPAF_Allowance$type", "   $datefilter6 AND empPayGrp='$group'");
$arrPAF = array_unique(array_merge($psObj->arrOthers,$psObj->arrOthers,$psObj->arrEmpStat,$psObj->arrBranch,$psObj->arrPosition,$psObj->arrPayroll,$psObj->arrAllow ));
$strPAF = implode(",",$arrPAF);
$strPAF = ($strPAF != "" ? " AND empNo IN ($strPAF)" : "");
$qryIntMaxRec = "SELECT tblEmpMast.empNo,tblEmpMast.empLastName,tblEmpMast.empFirstName,
				tblEmpMast.empMidName,tblDepartment.deptShortDesc 
				FROM tblEmpMast INNER JOIN tblDepartment ON 
				tblEmpMast.compCode = tblDepartment.compCode 
				AND tblEmpMast.empDiv = tblDepartment.divCode 
			    WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}'
			    AND tblDepartment.deptLevel = '1' AND empPayGrp='".$_GET['pgroup']."'
				and empBrnCode='".$_GET['branch']."' $strPAF
				order by empDiv,empLastName,empFirstName,empMidName
				 ";

$resEmpList = $psObj->execQry($qryIntMaxRec);
$arrEmpList = $psObj->getArrRes($resEmpList);


	$no=1;
	$divdesc="";
	foreach($arrEmpList as $empListVal){
		$resArrOthers = $psObj->getPAF_others($empListVal['empNo'],"",$datefilter."",$type);
		$ctr=count($resArrOthers['value1']);
		$empNo = $empListVal['empNo'];
		for($x=0;$x<$ctr; $x++) {
			$name = "";
			$empNo = "";
			$q = "";
			if ($x == 0) {
				$q = $no;
				$no++;
				$name = $empListVal['empLastName']. ", " . $empListVal['empFirstName'] . " " . $empListVal['empMidName'][0].".";
			}		
			$arrData[] = array($q,$name,$resArrOthers['field'][$x],$resArrOthers['value1'][$x],$resArrOthers['value2'][$x],date("m/d/Y",strtotime($resArrOthers['effdate'][$x])));
		}
		
	}
	
	
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
		$pdf->Data($arrNewEmp, $arrSepEmp, $arrData, $arrNewEmpPrev, $arrSepEmpPrev, $arrPrevHC, $arrCurHC);
		
####Set up to show data	
$pdf->Output('MANPOWER_REPORT.pdf','D');
?>