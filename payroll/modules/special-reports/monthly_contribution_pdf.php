<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
			
			
			$this->SetFont('Courier','','9'); 
			$this->Cell(80,5,"Run Date: " . $newdate);
			$this->Cell(50,5,$this->compName);
			$this->Cell(101,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			
			$this->Cell(80,5,"Report ID: ".$this->reportid);
			
			$hTitle = $this->heading." Premium Contribution for the Month of ".$this->pdHeadTitle;
			$this->Cell(80,5,$hTitle);
			$this->Ln();
			$this->Cell(335,3,'','');
			$this->Ln();
			$this->SetFont('Courier','B','9');
			
			if($this->topType=='S')
			{ 
				$this->Cell(10,6,'',0);
				$this->Cell(57,6,'EMPLOYEE NAME',0,'','L');
				$this->Cell(35,6,'SSS NUMBER',0,'','R');
				$this->Cell(40,6,'EMPLOYEE',0,'','R');
				$this->Cell(35,6,'EMPLOYER',0,'','R');
				$this->Cell(25,6,'EC',0,'','R');
				$this->Cell(35,6,'TOTAL',0,'','R');
			}
			elseif($this->topType=='PAG')
			{
				$this->Cell(10,6,'',0);
				$this->Cell(57,6,'EMPLOYEE NAME',0,'','L');
				$this->Cell(35,6,'HDMF NUMBER',0,'','R');
				$this->Cell(40,6,'EMPLOYEE',0,'','R');
				$this->Cell(35,6,'EMPLOYER',0,'','R');
				$this->Cell(35,6,'TOTAL',0,'','R');
			}
			else
			{
				$this->Cell(10,6,'',0);
				$this->Cell(57,6,'EMPLOYEE NAME',0,'','L');
				$this->Cell(35,6,'PHIC NUMBER',0,'','R');
				$this->Cell(40,6,'EMPLOYEE',0,'','R');
				$this->Cell(35,6,'EMPLOYER',0,'','R');
				$this->Cell(35,6,'TOTAL',0,'','R');
			}
			
			$this->Ln(10);
		}
		
		
		function displayContent($resQry,$topType)
		{
			$this->SetFont('Courier','','9'); 
			$ctr_emp = 1;
			$grantotemp=0;
			$grantotemr=0;
			$grantotec=0;
			$grantottot=0;
			
			foreach($resQry as $resQry_val)
			{
				$this->Cell(10,6,$ctr_emp." ",0,'','R');
				$this->Cell(57,6,$resQry_val["empLastName"].", ".$resQry_val["empFirstName"][0].".".$resQry_val["empMidName"][0]."."." ",0,'','L');
				$sum = 0;
				if($topType=='S')
				{
					$this->Cell(35,6,$resQry_val["empSssNo"],0,'','R');
					$this->Cell(40,6,$resQry_val["sssEmp"],0,'','R');
					$this->Cell(35,6,$resQry_val["sssEmplr"],0,'','R');
					$this->Cell(25,6,$resQry_val["ec"],0,'','R');
					$sum+=$resQry_val["sssEmp"]+$resQry_val["sssEmplr"]+$resQry_val["ec"];
					$grantotemp+=$resQry_val["sssEmp"];
					$grantotemr+=$resQry_val["sssEmplr"];
					$grantotec+=$resQry_val["ec"];
					$grantottot+=$resQry_val["sssEmp"]+$resQry_val["sssEmplr"]+$resQry_val["ec"];
					$this->Cell(35,6,number_format($sum,2),0,'','R');
				}
				elseif($topType=='PAG')
				{
					$this->Cell(35,6,$resQry_val["empPagibig"],0,'','R');
					$this->Cell(40,6,$resQry_val["hdmfEmp"],0,'','R');
					$this->Cell(35,6,$resQry_val["hdmfEmplr"],0,'','R');
					$sum+=$resQry_val["hdmfEmp"]+$resQry_val["hdmfEmplr"];
					$grantotemp+=$resQry_val["hdmfEmp"];
					$grantotemr+=$resQry_val["hdmfEmplr"];
					$grantottot+=$resQry_val["hdmfEmp"]+$resQry_val["hdmfEmplr"];
					$this->Cell(35,6,number_format($sum,2),0,'','R');
				}
				else
				{
					$this->Cell(35,6,$resQry_val["empPhicNo"],0,'','R');
					$this->Cell(40,6,$resQry_val["phicEmp"],0,'','R');
					$this->Cell(35,6,$resQry_val["phicEmplr"],0,'','R');
					$sum+=$resQry_val["phicEmp"]+$resQry_val["phicEmplr"];
					$grantotemp+=$resQry_val["phicEmp"];
					$grantotemr+=$resQry_val["phicEmplr"];
					$grantottot+=$resQry_val["phicEmp"]+$resQry_val["phicEmp"];
					$this->Cell(35,6,number_format($sum,2),0,'','R');
				}
				$this->Ln();
				$ctr_emp++;
			}
			$this->Ln(5);
			
			/*Grand Totals*/
			$this->SetFont('Courier','B','9'); 
			$this->Cell(102,6,"GRAND TOTAL",0,'','R');
			$this->Cell(40,6,number_format($grantotemp,2),0,'','R');
			$this->Cell(35,6,number_format($grantotemr,2),0,'','R');
			if($topType=='S')
				$this->Cell(25,6,number_format($grantotec,2),0,'','R');
			
			$this->Cell(35,6,number_format($grantottot,2),0,'','R');
			$this->Ln(10);
			$this->Cell(335,6,'* * * End of Report * * *','0','','C'); 
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

	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$pdf->topType		=	$_GET["topType"];
	$payPd      		= 	$_GET['payPd'];
	$chopMonth 			= split("-",$payPd);
	$payPdYear 			= $chopMonth[3];
	$payPdNum 			= $chopMonth[4];
	$payPdMonthName		= $chopMonth[5];
	$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$tbl				= 	$_GET["tbl"];
	$empNo         		= 	$_GET['empNo'];
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$orderBy       		= 	$_GET['orderBy'];
	$topType			= 	$_GET['topType'];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	$pdf->pdHeadTitle	=	"(".$payPdMonthName.", ".$payPdYear.")";
	if($topType=='S')
	{
		$pdf->heading = "SSS";
		$pdf->reportid = "SSSCONT001";
	}
	elseif($topType=='PAG')
	{
		$pdf->heading = "PAG-IBIG";
		$pdf->reportid = "HDMFCONT001";
	}
	else
	{
		$pdf->heading = "PHILHEALTH";
		$pdf->reportid = "PHICCONT001";
	}
	
	$pdf->topType = $topType;
	
	if ($empNo>"") {$empNo1 = " AND (tblGov.empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	if ($orderBy==1) {$orderBy1 = "  empLastName, empFirstName, empMidName, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==2) {$orderBy1 = "  tblGov.empNo, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==3) {$orderBy1 = "  empDiv, empDepCode, empSecCode, empLastName, empFirstName, empMidName ";}
	
	
	$field = "tblGov.compCode,tblGov.pdYear,tblGov.pdMonth,tblGov.empNo,empLastName,empFirstName,empMidName,
				empSssNo,empPhicNo,empPagibig,
				mtdEarnings, sssEmp, sssEmplr, ec, phicEmp,phicEmplr,hdmfEmp,
				hdmfEmplr";

	$qryMonthCont = "Select $field from $tbl tblGov, tblEmpMast tblEmp
					where
					tblGov.empNo=tblEmp.empNo 
					and tblGov.compCode='".$_SESSION["company_code"]."'
					and pdYear='".$payPdYear."'
					and pdMonth='".$payPdNum."'
					and empPayGrp='".$_SESSION["pay_group"]."'
					and empPayCat='".$_SESSION["pay_category"]."'
					and empStat NOT IN('RS','IN','TR')
					$empNo1 $empName1 $empDiv1 $empName1 $empDept1 $empSect1
					order by $orderBy1
					";
	$resMonthCont = $inqTSObj->execQry($qryMonthCont);
	$arrMonthCont = $inqTSObj->getArrRes($resMonthCont);
	if(count($arrMonthCont)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrMonthCont,$topType);
	}
	
	
	$pdf->Output();
?>
