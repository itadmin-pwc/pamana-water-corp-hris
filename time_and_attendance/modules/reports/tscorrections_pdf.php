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
	var	$arrReasons = array();

	
	function Header() {
		$arrBranch = $this->getEmpBranchArt($_SESSION['company_code'],$_GET['branch']);
		$this->SetFont('Courier','',10);
		$this->Cell(70,5,'Run Date: '.$this->currentDateArt(),0);
		$this->Cell(240,5,$this->getCompanyName($_SESSION['company_code']),0);
		$this->Cell(30,5,'Page '.$this->PageNo().'/{nb}',0,1);
		$this->Cell(70,5,'Report ID: TSCORRECTIONS',0);
		$this->Cell(90,5,'Timesheet Corrections (' . $arrBranch['brnShortDesc'].')',0,1);
		$this->Cell(335,4,"   DATE                          FROM                                                        TO                                REASON",'T',1);
		$this->Cell(335,4,"               IN      L-OUT    L-IN     BR-IN     BR-OUT    OUT       IN      L-OUT    L-IN     BR-IN   BR-OUT   OUT",'B',1);
		$this->Ln();
		
	}
	
	
	function Footer() {
		$this->SetFont('Courier','',10);
	    $this->SetY(-12);
		$dispUser = $this->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$user = $dispUser["empFirstName"]." ".$dispUser["empLastName"];
		$this->Cell(335,7,"Printed By: $user                                                    Approved by: " ,'T',0);
	}
	
	function Main($arrTS = array()) {
		$this->SetFont('Courier','',10);
		$this->AddPage();
		$this->getReasonsList();
		$SPACES = 4.7;
		$tmp_empName="";
		$ctr = 0;
		foreach ($arrTS as $val){
			$appType="";
			$reason = $this->getTSReason($val['editReason']);
			if ($val['empName'] != $tmp_empName) {
				$ctr++;
				$ch++;
				if ($ch==3) {
					$ch=0;
				}
				$this->Cell(40,$SPACES,$val['empName'],0,1,'L');
			} 	

			$this->Cell(24,$SPACES,date('m/d/Y',strtotime($val['tsDate'])),0,0);
			$this->SetFont('Courier', '', '10');
			$this->Cell(20,$SPACES,$val['timeIn'],0,0,'C');
			$this->Cell(20,$SPACES,$val['lunchOut'],0,0,'C');
			$this->Cell(20,$SPACES,$val['lunchIn'],0,0,'C');
			$this->Cell(20,$SPACES,$val['breakOut'],0,0,'C');
			$this->Cell(20,$SPACES,$val['breakIn'],0,0,'C');
			$this->Cell(20,$SPACES,$val['timeOut'],0,0,'C');
			$this->Cell(20,$SPACES,$val['cor_timeIn'],0,0,'C');
			$this->Cell(20,$SPACES,$val['cor_lunchOut'],0,0,'C');
			$this->Cell(18,$SPACES,$val['cor_lunchIn'],0,0,'C');
			$this->Cell(18,$SPACES,$val['cor_breakOut'],0,0,'C');
			$this->Cell(18,$SPACES,$val['cor_breakIn'],0,0,'C');
			$this->Cell(18,$SPACES,$val['cor_timeOut'],0,0,'C');
			$this->Cell(20,$SPACES,$reason,0,0,'L');
			$this->Ln();
		
			$tmp_empName = $val['empName'];
			//if ($this->GetY() > 195) HEADER_FOOTER($this, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
		
		}
		#########################################################################
		//if ($this->GetY() > 185) HEADER_FOOTER($this, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
		$this->SetFont('Courier', 'B', '10');
		$this->Ln();
		$this->Cell(259,$SPACES,"Total No. of Employees: $ctr",0,0);
		$this->SetFont('Courier', '', '10');
		$this->Ln();
		$this->Cell(335,$SPACES,"*****End of Report*****",0,0,'C');

	
	}
	function getReasonsList() {
		$sql = "SELECT * FROM tblTK_ViolationType where violationStat='A'";
		$this->arrReasons = $this->getArrRes($this->execQry($sql));
	}
	function getTSReason($code) {
		$reason = "";
		foreach($this->arrReasons as $val) {
			if ($val['violationCd'] ==  $code)	{
				$reason = $val['violationDesc'];
			}
		}
		return $reason;
	}
	
}
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$inqTSObj = new inqTSObj(); 
$arrEventLogs = $inqTSObj->TS_Corrections($_GET['branch'],$_GET['hist'],$_GET['from'],$_GET['to'],$_GET['group']);
$pdf=new PDF();
$pdf->FPDF('l', 'mm', 'legal');
//$pdf->SetMargins(5,5,5);
$pdf->AliasNbPages(); 
$pdf->Main($arrEventLogs);
$pdf->Output('ts_prooflist.pdf','D');
//$pdf->Output();
?>
