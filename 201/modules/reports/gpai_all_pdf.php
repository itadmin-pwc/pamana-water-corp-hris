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
		
		$this->SetFont('Courier','B','9'); 
		$this->Cell(32,4,"RUN DATE       : ",'','');
		$this->SetFont('Courier','','9');
		$this->Cell(50,4,$this->rundate,'','');
		$this->Cell(180,3,'Page '.$this->PageNo().' of {nb}',0,1,'R');	
		$this->SetFont('Courier','B','9');
		$this->Cell(32,4,"COMPANY        : ",'','');
		$this->SetFont('Courier','','9');
		$this->Cell(150,4,$this->company,'','1');
		$this->SetFont('Courier','B','9');
		$this->Cell(32,4,"GROUP          : ",'','');
		$this->SetFont('Courier','','9');
		$this->Cell(150,4,$this->group,'','1');
		$this->SetFont('Courier','B','9');
		$this->Cell(32,4,"PAYROLL DATE   : ",'','');
		$this->SetFont('Courier','','9');
		$this->Cell(150,4,$this->payperiod,'','1');
		$this->SetFont('Courier','B','9');
		$this->Cell(32,4,"CUT-OFF        : ",'','');
		$this->SetFont('Courier','','9');
		$this->Cell(150,4,$this->cutoff,'','1');
		$this->SetFont('Courier','B','9');
		$this->Ln();
		$this->Cell(260,4,"GPAI UPDATE FORM",'','','C');		
		$this->Ln();
	}

####Set up details/data	
	function Data($arrNew,$arrDel) {
		$this->SetFont('Courier','B','8'); 
		$this->Cell(26,5,"ENROLLMENT",0,'1');
		$this->SetFont('Courier','B','8'); 
		$this->Cell(45,5,'SURNAME','1','0','L');
		$this->Cell(45,5,'FIRSTNAME','1','0','L');
		$this->Cell(8,5,'MI','1','0','C');
		$this->Cell(90,5,'POSITION','1','0','C');
		$this->Cell(20,5,'DATE HIRED','1','0','C');
		$this->Cell(20,5,'BIRTH DATE','1','0','C');	
		$this->Cell(32,5,'BRANCH','1','1','C');	
		$ctr=1;
		$cnt=0;
		foreach($arrNew as $valNew){
			if($div!=$valNew['rankDesc']){
				$this->SetFont('Courier','B','8'); 
					$this->Cell(260,5,$valNew['rankDesc'],'1','1');
					$ctr=1;
				//}
			}
			else{
				$ctr++;	
			}
			$this->SetFont('Courier','','8'); 
			$this->Cell(8,3,$ctr,'1','0');
			$this->Cell(37,3,$valNew['empLastName'],'1','0');
			$this->Cell(45,3,$valNew['empFirstName'],'1','0');	
			$this->Cell(8,3,substr($valNew['empMidName'],0,1).".",'1','0','C');
			$this->Cell(90,3,$valNew['posDesc'],'1','0');
			$this->Cell(20,3,date("m/d/Y", strtotime($valNew['dateHired'])),'1','0');
			$this->Cell(20,3,date("m/d/Y", strtotime($valNew['empBday'])),'1','0');
			$this->MultiCell(32,3,$valNew['brnShortDesc'],'1','1');
			$div=$valNew['rankDesc'];	
			$cnt++;		
		}	
		$this->SetFont('Courier','B','8'); 	
		if($cnt<=1){
			$this->Cell(25,5,$cnt . " Total No. of Newly Hired Employee",'0','1');
		}
		else{
			$this->Cell(25,5,$cnt . " Total No. of Newly Hired Employees",'0','1');	
		}
		$this->Ln(5);
		$this->SetFont('Courier','B','8'); 
		$this->Cell(26,5,"DELETION",0,'1');
		$this->SetFont('Courier','B','8'); 
		$this->Cell(45,5,'SURNAME','1','0','L');
		$this->Cell(45,5,'FIRSTNAME','1','0','L');
		$this->Cell(8,5,'MI','1','0','C');
		$this->Cell(70,5,'POSITION','1','0','C');
		$this->Cell(40,5,'REASON FOR SEPARATION','1','0','C');
		$this->Cell(25,5,'DATE SEPARATED','1','0','C');	
		$this->Cell(27,5,'BRANCH','1','1','C');	
		$ctr=1;
		$cnt=0;
		foreach($arrDel as $valDel){
			if($divs!=$valDel['rankDesc']){
				$this->SetFont('Courier','B','8'); 
					$this->Cell(260,5,$valDel['rankDesc'],'1','1');
					$ctr=1;
				//}
			}
			else{
				$ctr++;	
			}
			if($valDel['dateResigned']!=""){
				$dateSeparated = $valDel['dateResigned'];	
			}
			else{
				$dateSeparated = $valDel['endDate'];		
			}
			if($valDel['natureCode']==1){
				$reason = "Absent without leave";	
			}
			elseif($valDel['natureCode']==2){
				$reason = "End of contract";	
			}
			elseif($valDel['natureCode']==3){
				$reason = "Resigned";	
			}
			elseif($valDel['natureCode']==4){
				$reason = "Transferred";	
			}
			elseif($valDel['natureCode']==5){
				$reason = "Terminated for a cause";	
			}
			$this->SetFont('Courier','','8'); 
			$this->Cell(8,3,$ctr,'1','0');
			$this->Cell(37,3,$valDel['empLastName'],'1','0');
			$this->Cell(45,3,$valDel['empFirstName'],'1','0');	
			$this->Cell(8,3,substr($valDel['empMidName'],0,1).".",'1','0','C');
			$this->Cell(70,3,$valDel['posDesc'],'1','0');
			$this->Cell(40,3,$reason,'1','0');
			$this->Cell(25,3,date("m/d/Y", strtotime($dateSeparated)),'1','0');
			$this->MultiCell(27,3,$valDel['brnShortDesc'],'1','1');
			$divs=$valDel['rankDesc'];	
			$cnt++;		
		}	
		$this->SetFont('Courier','B','8'); 
		if($cnt<=1){	
			$this->Cell(25,5,$cnt . " Total No. of Separated Employee",'0','1');
		}
		else{
			$this->Cell(25,5,$cnt . " Total No. of Separated Employees",'0','1');	
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
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(260,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(235,6,"Generated By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}
	
	
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
$qryNew = "Select tblEmpMast_New.empLastName, tblEmpMast_New.empFirstName, tblEmpMast_New.empMidName, tblEmpMast_New.dateHired, 
			tblEmpMast_New.empBday, tblEmpMast_New.empPosId, tblPosition.posDesc, tblEmpMast_New.empRank, tblRankType.rankDesc,
			tblBranch.brnShortDesc
		 	from tblEmpMast_new 
		 	Inner Join tblPosition on tblEmpMast_New.empPosId=tblPosition.posCode
			Inner Join tblRankType on tblEmpMast_New.empRank=tblRankType.rankCode
			Inner Join tblBranch on tblEmpMast_New.empBrnCode=tblBranch.brnCode
		 	where tblEmpMast_New.compCode='".$_SESSION['company_code']."'
			and tblEmpMast_New.empPayGrp='".$_GET['pgroup']."' 
			and tblEmpMast_New.dateReleased between '".date("Y-m-d",strtotime($_GET['costart']))."' 
			and '".date("Y-m-d",strtotime($_GET['coend']))."' 
			and tblEmpMast_New.stat='R'
			order by tblEmpMast_New.empRank Desc,tblEmpMast_New.empLastName";
$resNew = $psObj->execQry($qryNew);
$arrNew = $psObj->getArrRes($resNew);


$qryDel = "Select tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblEmpMast.dateHired, 
			tblEmpMast.empBday, tblEmpMast.empPosId, tblPosition.posDesc, tblEmpMast.empRank, tblRankType.rankDesc, 
			tblEmpMast.empStat, tblEmpMast.dateResigned, tblEmpMast.endDate, tblEmpMast.empNo, 
			tblSeparatedEmployees.reason, tblSeparatedEmployees.natureCode, tblBranch.brnShortDesc
		 	from tblEmpMast 
		 	Inner Join tblPosition on tblEmpMast.empPosId=tblPosition.posCode
			Inner Join tblRankType on tblEmpMast.empRank=tblRankType.rankCode
			Inner Join tblBranch on tblEmpMast.empBrnCode=tblBranch.brnCode
			Left Join tblSeparatedEmployees on  tblEmpMast.empNo=tblSeparatedEmployees.empNo
		 	where tblEmpMast.compCode='".$_SESSION['company_code']."' 
			and tblEmpMast.empPayGrp='".$_GET['pgroup']."' and (tblEmpMast.empStat='RS' or tblEmpMast.empStat='IN')  
			and (tblEmpMast.dateResigned between '".date("Y-m-d",strtotime($_GET['costart']))."' 
			and '".date("Y-m-d",strtotime($_GET['coend']))."' 
			or  tblEmpMast.endDate between '".date("Y-m-d",strtotime($_GET['costart']))."' 
			and '".date("Y-m-d",strtotime($_GET['coend']))."')
			order by tblEmpMast.empRank Desc,tblEmpMast.empLastName";
$resDel = $psObj->execQry($qryDel);
$arrDel = $psObj->getArrRes($resDel);

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
		$pdf->Data($arrNew,$arrDel);
		
####Set up to show data	
$pdf->Output('GPAI_PROOFLIST_ALL.pdf','D');
?>