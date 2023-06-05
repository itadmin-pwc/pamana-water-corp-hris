<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("ts_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	class pdf extends FPDF{
		function Header(){
			//if($this->PageNo()==1){
				$this->SetFont('Courier','B',9);
				$this->Cell(115,5,'Run Date: '.$this->currentDateArt(),0);
				$this->SetFont('Courier','B',12);
				$this->Cell(110,5,$this->compName,0,0,'C');
				$this->SetFont('Courier','B',9);
				$this->Cell(115,5,'Page '.$this->PageNo().'/{nb}',0,1,'R');
				$this->Cell(115,5,'Report ID: TSADJUSTMENT',0);
				if($_GET['id']=="O"){
					$this->Cell(100,5,'TIMESHEET ADJUSTMENT PROOFLIST HELD',0,0,'C');	
				}
				else if($_GET['id']=="A"){
					$this->Cell(100,5,'TIMESHEET ADJUSTMENT PROOFLIST POSTED',0,0,'C');
				}
				else if($_GET['id']=="P"){
					$this->Cell(100,5,'TIMESHEET ADJUSTMENT PROOFLIST ALL',0,0,'C');
				}
				//$this->Cell(110,5,'TIMESHEET ADJUSTMENT PROOFLIST',0,0,'C');
				$this->Cell(110,5,'Payroll Group '.$_GET['group'],0,1,'R');
				$this->SetFont('Courier','B',10);
				$this->Cell(125,5,'DETAILS','1','0','C');
				$this->Cell(75,5,'HOURS','1','0','C');
				$this->Cell(135,5,'AMOUNT','1','1','C');
				$this->SetFont('Courier','B',9);
				$this->Cell(25,5,'EMPLOYEE #','1','0','C');		
				$this->Cell(65,5,'NAME','1','0','C');
				$this->Cell(20,5,'TS DATE','1','0','C');
				$this->Cell(15,5,'ENTRY','1','0','C');
				$this->Cell(15,5,'Reg.','1','0','C');
				$this->Cell(15,5,'OT','1','0','C');
				$this->Cell(15,5,'OT >8','1','0','C');	
				$this->Cell(15,5,'ND','1','0','C');	
				$this->Cell(15,5,'ND > 8','1','0','C');	
				$this->Cell(30,5,'BASIC','1','0','C');
				$this->Cell(25,5,'OT','1','0','C');
				$this->Cell(15,5,'ND','1','0','C');	
				$this->Cell(15,5,'HOL.','1','0','C');	
				$this->Cell(15,5,'ECOLA','1','0','C');	
				$this->Cell(15,5,'CTPA','1','0','C');	
				$this->Cell(20,5,'ADVANCES','1','1','C');	
			//}
		}
		function Main($arrTS){
			$this->AddPage();
			$basic=0;
			$ot=0;
			$nd=0;
			$hp=0;
			$ecola=0;
			$ctpa=0;
			$adv=0;
			$empNo="";
			foreach($arrTS as $val){
				$this->SetFont('Courier','',9);
				if($empNo!=$val['empNo']){
					$this->Cell(25,5,$val['empNo'],'1','0','L');	
					$this->Cell(65,5,$val['empName'],'1','0','L');	
					$this->Cell(20,5,date('Y-m-d',strtotime($val['tsDate'])),'1','0','C');
					$this->Cell(15,5,($val['entryTag']=="A"?"Amnt.":"Hrs."),'1','0','C');	
					$this->Cell(15,5,$val['hrsReg'],'1','0','R');
					$this->Cell(15,5,$val['hrsOtLe8'],'1','0','R');
					$this->Cell(15,5,$val['hrsOtGt8'],'1','0','R');	
					$this->Cell(15,5,$val['hrsNd'],'1','0','R');	
					$this->Cell(15,5,$val['hrsNdGt8'],'1','0','R');	
					$this->Cell(30,5,($val['adjBasic']=="0"?"0.00":$val['adjBasic']),'1','0','R');
					$this->Cell(25,5,($val['adjOt']=="0"?"0.00":$val['adjOt']),'1','0','R');
					$this->Cell(15,5,($val['adjNd']=="0"?"0.00":$val['adjNd']),'1','0','R');	
					$this->Cell(15,5,($val['adjHp']=="0"?"0.00":$val['adjHp']),'1','0','R');	
					$this->Cell(15,5,($val['adjEcola']=="0"?"0.00":$val['adjEcola']),'1','0','R');	
					$this->Cell(15,5,($val['adjCtpa']=="0"?"0.00":$val['adjCtpa']),'1','0','R');	
					$this->Cell(20,5,($val['adjAdv']=="0"?"0.00":$val['adjAdv']),'1','1','R');	
				}
				else{
					$this->Cell(25,5,"",'0','0','L');	
					$this->Cell(65,5,"",'0','0','L');	
					$this->Cell(20,5,date('Y-m-d',strtotime($val['tsDate'])),'1','0','C');
					$this->Cell(15,5,($val['entryTag']=="A"?"Amnt.":"Hrs."),'1','0','C');	
					$this->Cell(15,5,$val['hrsReg'],'1','0','R');
					$this->Cell(15,5,$val['hrsOtLe8'],'1','0','R');
					$this->Cell(15,5,$val['hrsOtGt8'],'1','0','R');	
					$this->Cell(15,5,$val['hrsNd'],'1','0','R');	
					$this->Cell(15,5,$val['hrsNdGt8'],'1','0','R');	
					$this->Cell(30,5,($val['adjBasic']=="0"?"0.00":$val['adjBasic']),'1','0','R');
					$this->Cell(25,5,($val['adjOt']=="0"?"0.00":$val['adjOt']),'1','0','R');
					$this->Cell(15,5,($val['adjNd']=="0"?"0.00":$val['adjNd']),'1','0','R');	
					$this->Cell(15,5,($val['adjHp']=="0"?"0.00":$val['adjHp']),'1','0','R');	
					$this->Cell(15,5,($val['adjEcola']=="0"?"0.00":$val['adjEcola']),'1','0','R');	
					$this->Cell(15,5,($val['adjCtpa']=="0"?"0.00":$val['adjCtpa']),'1','0','R');	
					$this->Cell(20,5,($val['adjAdv']=="0"?"0.00":$val['adjAdv']),'1','1','R');	
				}
				$empNo=$val['empNo'];
				$basic = $basic+$val['adjBasic'];
				$ot = $ot+$val['adjOt'];
				$nd = $nd+$val['adjNd'];
				$hp = $hp+$val['adjHp'];
				$ecola = $ecola+$val['adjEcola'];
				$ctpa = $ctpa+$val['adjCtpa'];
				$adv = $adv+$val['adjAdv'];
			}	
				$this->SetFont('Courier','B',9);
				$this->Cell(200,5,'TOTAL','1','0','C');	
				$this->Cell(30,5,number_format($basic,2),'1','0','R');
				$this->Cell(25,5,number_format($ot,2),'1','0','R');
				$this->Cell(15,5,number_format($nd,2),'1','0','R');	
				$this->Cell(15,5,number_format($hp,2),'1','0','R');	
				$this->Cell(15,5,number_format($ecola,2),'1','0','R');	
				$this->Cell(15,5,number_format($ctpa,2),'1','0','R');	
				$this->Cell(20,5,number_format($adv,2),'1','1','R');	
				$this->SetFont('Courier','B',10);
				$this->Cell(200,5,'GRAND TOTAL','1','0','C');	
				$this->Cell(135,5,number_format($basic+$ot+$nd+$hp+$ecola+$ctpa+$adv,2),'1','0','R');	
		}
		function Footer(){
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',9);
			$this->Cell(335,6,"Generated By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}	
	}
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$pdf = new pdf();
	$pdf->FPDF('l','mm','legal');
	$pdf->AliasNbPages();
	$compCode = $_SESSION['company_code'];
	$pdf->compName = $inqTSObj->getCompanyName($compCode);
	$arrTS = $inqTSObj->TS_Adjustment_with_Amount($_GET['frm'],$_GET['to'],$_GET['group'],$_GET['id']);
	$pdf->printedby = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']); 
	$pdf->Main($arrTS);
	$pdf->Output('ts_adjustment.pdf','D');
?>
