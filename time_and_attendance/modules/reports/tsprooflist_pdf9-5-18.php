<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("ts_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$compCode = $_SESSION['company_code'];


class PDF extends FPDF
{
	var	$arrOvertimes = array();
	var	$arrDeductions = array();
	var $compname;
	var $branch;
	
	function Header() {
		switch($_GET['cat']) {
			case 1:
				$cat = "Executive";
			break;	
			case 2:
				$cat = "Confidential";
			break;	
			case 3:
				$cat = "Non Confidential";
			break;	
			case 9:
				$cat = "Resigned";
			break;	
		}
		$this->branch = $this->getEmpBranchArt();	
		$this->compname = $this->getCompName();
		$this->SetFont('Courier','',10);
		$this->Cell(70,5,'Run Date: '.$this->currentDateArt(),0);
		$this->Cell(240,5,$this->compname,0);
		$this->Cell(30,5,'Page '.$this->PageNo().'/{nb}',0,1);
		$this->Cell(70,5,'Report ID: TSPROOFLST',0);
		$this->Cell(90,5,'Timesheet Prooflist  (' . $this->branch.') ' . $cat,0,1);
		$this->Cell(335,4,"   DATE    DAY TYPE   AP. TYPE   SHIFT   SHIFT   SHIFT   SHIFT   ACTUAL   ACTUAL   ACTUAL  ACTUAL   OT IN   OT OUT   OT<8  OT>8   ND   ND<8  TARDY  UT    HRS",'T',1);
		$this->Cell(335,4,"                                  IN     L-OUT   L-IN     OUT     IN      L-OUT     L-IN     OUT                                  REG                     WRK",'B',1);
		$this->Ln();
		
	}
	
	
	function Footer() {
		$this->SetFont('Courier','',10);
	    $this->SetY(-12);
		$dispUser = $this->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$user = $dispUser["empFirstName"]." ".$dispUser["empLastName"];
		$this->Cell(335,7,"Printed By: $user                                                    Approved by: " ,'T',0);
	}
	
	function Main($arrTS = array(),$division,$department) {
		$this->SetFont('Courier','',10);
		$this->AddPage();
		$this->getOTs();
		$this->getDeductions();
		$SPACES = 4.7;
		$tmp_empName="";
		$ctr = 0;
		if($division['deptDesc']!="" && $department['deptDesc']!=""){
			$this->SetFont('Courier','B',10);
			$this->Cell(246,'3','Division = '.$division['deptDesc']." / ".'Department = '.$department['deptDesc'],0,'1','L');
			$this->SetFont('Courier','',10);
		}
		foreach ($arrTS as $val){
			$appType="";
			$lpay="";
			$arrOT = $this->getempOTsDeds($val['empNo'],$val['tsDate'],'OT');
			$arrDed = $this->getempOTsDeds($val['empNo'],$val['tsDate'],'Ded');
			if($val['empBrnCode']=="0001"){
				$cww = $this->getEmpShifts($val['empNo']);	
				$cwwtag = " / CWW: ".($cww['CWWTag']!="Y"?"No":"Yes");
			}
			else{
				$cwwtag = "";
			}
			
			if ($val['hrsWorked']>0) {
				if ((float)$val['hrsRequired']<(float)$val['hrsWorked'])
					$hrsWrked = $val['hrsRequired'];
				else
					$hrsWrked = $val['hrsWorked'];
			} else {
				$hrsWrked = 0;
			}

/*			
			$hrsWrked = 0;
			if ($val['hrsRequired']==8) {
				if ($val['hrsWorked']>=8) 
					$hrsWrked = 8;	
				else
					$hrsWrked = $val['hrsWorked'];
			} else {
				if ($val['hrsWorked']>0) {
					if ($val['hrsRequired']==3.5) {
						if ($val['hrsWorked']>=3.5) 
							$hrsWrked = 8;	
						else
							$hrsWrked = $val['hrsWorked']+4.5;
					} else {
						$hrsWrked = $val['hrsWorked'];
					}
				}
			
			}	*/
			if ($tmp_empName!="" && $val['empName'] != $tmp_empName) {
					$this->SetFont('Courier', 'B', '10');
					$this->Cell(246,$SPACES,'Total',0,0,'C');
					$this->Cell(13,$SPACES,number_format($hrsOTLe8,2),0,0,'C');
					$this->Cell(13,$SPACES,number_format($hrsOTGt8,2),0,0,'C');
					$this->Cell(13,$SPACES,number_format($hrsRegNDLe8,2),0,0,'C');
					$this->Cell(13,$SPACES,number_format($hrsNDLe8,2),0,0,'C');
					$this->Cell(13,$SPACES,number_format($hrsTardy,2),0,0,'C');
					$this->Cell(13,$SPACES,number_format($hrsUT,2),0,0,'C');
					$this->Cell(13,$SPACES,number_format($hrsWrk,2),0,0,'C');
					$this->Ln();
					$this->SetFont('Courier', '', '10');
					$hrsOTLe8	= 0;
					$hrsOTGt8	= 0;
					$hrsNDLe8	= 0;
					$hrsRegNDLe8= 0;
					$hrsTardy	= 0;
					$hrsUT		= 0;
					$hrsWrk		= 0;			
			}	
			$hrsOTLe8	+= $arrOT['hrsOTLe8'];
			$hrsOTGt8	+= $arrOT['hrsOTGt8'];
			$hrsNDLe8	+= $arrOT['hrsNDLe8'];
			$hrsRegNDLe8+= $arrOT['hrsRegNDLe8'];
			$hrsTardy	+= $arrDed['hrsTardy'];
			$hrsUT		+= $arrDed['hrsUT'];
			$hrsWrk		+= $hrsWrked;
		
			if ($val['empName'] != $tmp_empName) {
				$ctr++;
				$ch++;
				if ($ch==3) {
					$this->AddPage();
					$ch=0;
				}
				$payType = ($val['empPayType']=='M')? "Monthly":"Daily";
				$this->Cell(40,$SPACES, $val['empName'] . " " . $val['empNo']  . " / Rank: " . $val['empRank']  . " / Level: " . $val['empLevel']. " / Pay Type: " . $payType . $cwwtag,0,1,'L');
			} 	
			$appType = $val['appTypeShortDesc'];
			if ($val['obTag']=='Y') {
				$appType = ($appType=='') ? "OB" :"$appType,OB";
			}
			if ($val['csTag']=='Y') {
				$appType = ($appType=='') ? "CS" :"$appType,CS";
			}
			if ($val['crdTag']=='Y') {
				$appType = ($appType=='') ? "CRD" :"$appType,CRD";
			}
			if($val['dayType']=="03" || $val['dayType']=="05"){
				if($val['legalPayTag']=="Y"){
					$lpay = "*";
				}
			}
			if($val['satPayTag']=="Y"){
				$lpay = "*";
			}
			
			$this->Cell(24,$SPACES,date('Y-m-d',strtotime($val['tsDate'])),0,0);
			$this->SetFont('Courier', '', '8');
			$this->Cell(24,$SPACES,$this->DayType($val['dayType']).$lpay,0,0);
			$this->Cell(18,$SPACES,str_replace(' ','',$appType),0,0);
			$this->SetFont('Courier', '', '10');
			$this->Cell(18,$SPACES,$val['shftTimeIn'],0,0,'C');
			$this->Cell(18,$SPACES,$val['shftLunchOut'],0,0,'C');
			$this->Cell(18,$SPACES,$val['shftLunchIn'],0,0,'C');
			$this->Cell(18,$SPACES,$val['shftTimeOut'],0,0,'C');
			$this->Cell(18,$SPACES,$val['timeIn'],0,0,'C');
			$this->Cell(18,$SPACES,$val['lunchOut'],0,0,'C');
			$this->Cell(18,$SPACES,$val['lunchIn'],0,0,'C');
			$this->Cell(18,$SPACES,$val['timeOut'],0,0,'C');
			$this->Cell(18,$SPACES,$val['otIn'],0,0,'C');
			$this->Cell(18,$SPACES,$val['otOut'],0,0,'C');
			$this->Cell(13,$SPACES,($arrOT['hrsOTLe8']==0)? "":$arrOT['hrsOTLe8'],0,0,'C');
			$this->Cell(13,$SPACES,($arrOT['hrsOTGt8']==0)? "":$arrOT['hrsOTGt8'],0,0,'C');
			$this->Cell(13,$SPACES,($arrOT['hrsRegNDLe8']==0)? "":$arrOT['hrsRegNDLe8'],0,0,'C');
			$this->Cell(13,$SPACES,($arrOT['hrsNDLe8']==0)? "":$arrOT['hrsNDLe8'],0,0,'C');
			$this->Cell(13,$SPACES,($arrDed['hrsTardy']==0)? "":$arrDed['hrsTardy'],0,0,'C');
			$this->Cell(13,$SPACES,($arrDed['hrsUT']==0)? "":$arrDed['hrsUT'],0,0,'C');
		
			$this->Cell(13,$SPACES,number_format($hrsWrked,2),0,0,'C');
			$this->Ln();
		
			$tmp_empName = $val['empName'];
			//if ($this->GetY() > 195) HEADER_FOOTER($this, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
		
		}
		#########################################################################
		//if ($this->GetY() > 185) HEADER_FOOTER($this, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
		$this->SetFont('Courier', 'B', '10');
		$this->Cell(246,$SPACES,'Total',0,0,'C');
		$this->Cell(13,$SPACES,number_format($hrsOTLe8,2),0,0,'C');
		$this->Cell(13,$SPACES,number_format($hrsOTGt8,2),0,0,'C');
		$this->Cell(13,$SPACES,number_format($hrsRegNDLe8,2),0,0,'C');
		$this->Cell(13,$SPACES,number_format($hrsNDLe8,2),0,0,'C');
		$this->Cell(13,$SPACES,number_format($hrsTardy,2),0,0,'C');
		$this->Cell(13,$SPACES,number_format($hrsUT,2),0,0,'C');
		$this->Cell(13,$SPACES,number_format($hrsWrk,2),0,0,'C');
		$this->Cell(1,$SPACES,"",0,1,'L');
		$this->Ln();
		$this->Cell(259,$SPACES,"Total No. of Employees: $ctr",0,0);
		$this->SetFont('Courier', '', '10');
		$this->Ln();
		$this->Cell(335,$SPACES,"*****End of Report*****",0,0,'C');

	
	}

	function getempOTsDeds($empNo,$tsDate,$cat) {
		//$res = array();
		switch($cat) {
			case "OT":
				foreach($this->arrOvertimes as $val) {
				

					if ($empNo==$val['empNo'] && $tsDate==$val['tsDate']) {
						$res = $val;
					}
				}
			break;
			case "Ded":
				foreach($this->arrDeductions as $val) {
					if ($empNo==$val['empNo'] && $tsDate==$val['tsDate']) {
						$res = $val;
					}
				}
			break;
		}
		$res;
		return $res;
	}

	function getEmpShifts($empno){
		$qry = "Select CWWTag from tblTK_EmpShift where empNo='{$empno}'";
		return $this->getSqlAssocI($this->execQryI($qry));			  
	}
	
	

	function getOTs() {
		$sql = "SELECT * FROM tblTK_Overtime{$_GET['hist']} Where compCode='{$_SESSION['company_code']}' AND empNo IN  (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empbrnCode IN (Select brnCode from tblTK_UserBranch Where compCode='{$_SESSION['company_code']}' AND empNo='{$_SESSION['employee_number']}'))";
		$this->arrOvertimes = $this->getArrResI($this->execQryI($sql));
	}
	function getDeductions() {
		$sql = "SELECT * FROM tblTK_Deductions{$_GET['hist']} Where compCode='{$_SESSION['company_code']}' AND empNo IN  (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empbrnCode IN (Select brnCode from tblTK_UserBranch Where compCode='{$_SESSION['company_code']}' AND empNo='{$_SESSION['employee_number']}'))";
		$this->arrDeductions =  $this->getArrResI($this->execQryI($sql));
	}
	function DayType($dayType) {
		$desc = "";
		switch($dayType) {
			case '01':
				$desc = "Reg. Day";
			break;
			case '02':
				$desc = "Rest Day";
			break;
			case '03':
				$desc = "Leg Hol";
			break;
			case '04':
				$desc = "Spe Hol";
			break;
			case '05':
				$desc = "LH-Rest Day";
			break;
			case '06':
				$desc = "SH-Rest Day";
			break;
		}
		return $desc;
	}	
	
	function getCompName(){
		$qry = "SELECT compName FROM tblCompany WHERE compStat = 'A' AND compCode = '{$_SESSION['company_code']}' ";
		$res = $this->execQryI($qry);
		$row = $this->getSqlAssocI($res);
		//if (in_array($compCode,array(1,7,8,9,10,11,12))){
		//	$compname = 'PUREGOLD PRICE CLUB, INC.';
		//}
		//else{
			$compname = $row['compName'];
		//}
		return $compname;
	}
	
	function getEmpBranchArt(){
		$qry = "SELECT brnDesc FROM tblBranch
					     WHERE compCode = '{$_SESSION['company_code']}' 
						 AND brnCode = '{$_GET['branch']}' 
						 AND brnStat = 'A'";
		$res = $this->getSqlAssocI($this->execQryI($qry));
		$brn = $res['brnDesc'];
		return $brn;
	}
	
	function getUserHeaderInfo($empNo,$empId){
		$qryGetUserInfo = "SELECT empLastName, empFirstName, empMidName, empNo FROM tblEmpMast 
						   WHERE empNo    = '".trim($empNo)."'
						   AND id = '{$empId}'
						   AND   empStat NOT IN('RS','TR') ";
		$resGetUserInfo = $this->execQryI($qryGetUserInfo);
		return $this->getSqlAssocI($resGetUserInfo);		
	}
	
	
}
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$inqTSObj = new inqTSObj(); 
$div = $inqTSObj->getDivDescArt($_SESSION['company_code'],$_GET['divcode']);
$dept = $inqTSObj->getDeptDescArt($_SESSION['company_code'],$_GET['divcode'],$_GET['deptcode']);

if($_GET['divcode']!="" && $_GET['deptcode']!=""){
	$arrEventLogs = $inqTSObj->TSProofListReport($_GET['empNo'],$_GET['branch'],$_GET['hist'],$_GET['from'],$_GET['to'],$_GET['group'],$_GET['divcode'],$_GET['deptcode'],$_GET['cat']);
}
else{
	$arrEventLogs = $inqTSObj->TSProofList($_GET['empNo'],$_GET['branch'],$_GET['hist'],$_GET['from'],$_GET['to'],$_GET['group'],$_GET['cat']);
}
$pdf=new PDF();
$pdf->FPDF('l', 'mm', 'legal');
//$pdf->SetMargins(5,5,5);
$pdf->AliasNbPages(); 
$pdf->Main($arrEventLogs,$div,$dept);
$pdf->Output('ts_prooflist.pdf','D');
//$pdf->Output();
?>
