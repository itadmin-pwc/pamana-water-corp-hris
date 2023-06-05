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
			
			
			$this->Cell(80,5,"Report ID: EXTRACTTS");
			
			$this->Ln(10);
		}
		
		
		
		function displayContent($arrQry)
		{
			$this->SetFont('Courier','B','9');
			$hTitle = "UPLOADED TIMESHEET(S) REPORT";
			$this->Cell(80,5,$hTitle);
			$this->Ln();
			$this->Cell(335,3,'','');
			$this->Ln();
			
			$this->Cell(80,6,'Branch Desc.',1,'','L');
			$this->Cell(65,6,'TS Count',1,'1','R');
			
			$this->SetFont('Courier','','9'); 
			$grantTotal = 0;
			foreach($arrQry as $arrQry_val)
			{
				$this->Cell(80,6,$arrQry_val["brnDesc"],1,'','L');
				$this->Cell(65,6,$arrQry_val["cntTs"],1,'1','R');
				$grantTotal+=$arrQry_val["cntTs"];
			}
			$this->SetFont('Courier','B','9');
			$this->Cell(80,6,'GRAND TOTAL(S)',1,'','L');
			$this->Cell(65,6,$grantTotal,1,'1','R');
			$this->Ln(10);
			/*$this->Cell(335,6,'* * * End of Report * * *','0','','C'); */
		}
		
		
		function displayContent_Earn($arr_earn_basic, $ctr)
		{
			$this->SetFont('Courier','B','9');
			if($ctr == 1)
				$hTitle = "ENCODED BASIC - ADJUSTMENT";
			elseif ($ctr==2)
				$hTitle = "ENCODED OT - ADJUSTMENT";
			else
				$hTitle = "ENCODED ALLOWANCE - ADJUSTMENT";
				
			$this->Cell(80,5,$hTitle);
			$this->Ln();
			$this->Cell(335,3,'','');
			$this->Ln();
			
			$this->Cell(80,6,'Branch Desc.',1,'','L');
			$this->Cell(65,6,'Count',1,'1','R');
			
			$this->SetFont('Courier','','9'); 
			$grantTotal = 0;
			foreach($arr_earn_basic as $arr_earn_basic_val)
			{
				$this->Cell(80,6,$arr_earn_basic_val["brnDesc"],1,'','L');
				$this->Cell(65,6,$arr_earn_basic_val["cntTs"],1,'1','R');
				
				$grantTotal+=$arr_earn_basic_val["cntTs"];
			}
			$this->SetFont('Courier','B','9');
			$this->Cell(80,6,'GRAND TOTAL(S)',1,'','L');
			$this->Cell(65,6,$grantTotal,1,'1','R');
			$this->Ln(10);
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

	
	$pdf = new PDF('L', 'mm', 'LETTER');
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	$pdFrmDate = $_GET["dtFrm"];
	$pdToDate = $_GET["dtTo"];
	$pdNum  = $_GET["pdNum"];
	$pdYear = $_GET["pdYear"];
	
	
	 $qryTSUploadedCont = "SELECT     brnch.brnDesc, COUNT(brnch.brnDesc) AS cntTs
					FROM         tblTsParadox tsPara INNER JOIN
										  tblEmpMast empMast ON tsPara.empNo = empMast.empNo INNER JOIN
										  tblBranch brnch ON empMast.empBrnCode = brnch.brnCode
					WHERE     (empMast.compCode = '".$_SESSION["company_code"]."') AND (brnch.compCode = '".$_SESSION["company_code"]."')
							  and  empPayGrp='".$_SESSION["pay_group"]."' and tsDate between '".$pdFrmDate."' and '".$pdToDate."'
					GROUP BY brnch.brnDesc
					ORDER BY brnch.brnDesc
					";
	$resTsUploaded = $inqTSObj->execQry($qryTSUploadedCont);
	$arrTsUploaded = $inqTSObj->getArrRes($resTsUploaded);
	if(count($arrTsUploaded)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrTsUploaded);
	}
	
	/*Earn TranDtl - BADJ / OT ADJ / ALLOW*/
	
	$arrTrnCode = array("(tsPara.trnCode = '".ADJ_BASIC."')","(tsPara.trnCode = '".ADJ_OT."')","(tsPara.trnCode not in ('".ADJ_BASIC."','".ADJ_OT."'))" );
	$ctr = 1;
	
	foreach($arrTrnCode as $arrTrnCode_val)
	{
		 $qryEarn_Basic = "SELECT     brnch.brnDesc, COUNT(brnch.brnDesc) AS cntTs
							FROM         tblEarnTranDtl tsPara INNER JOIN
												  tblEmpMast empMast ON tsPara.empNo = empMast.empNo INNER JOIN
												  tblBranch brnch ON empMast.empBrnCode = brnch.brnCode
							WHERE     (empMast.compCode = '".$_SESSION["company_code"]."') AND (brnch.compCode = '".$_SESSION["company_code"]."') 
										AND ".$arrTrnCode_val." and refNo in (Select refNo from tblEarnTranHeader where
										 pdNumber='".$pdNum."' and payGrp='".$_SESSION["pay_group"]."' and pdYear='".$pdYear."' and earnRem like '%on Hyper TS%')
							
							GROUP BY brnch.brnDesc
							ORDER BY brnch.brnDesc;";
		$resEarn_Basic = $inqTSObj->execQry($qryEarn_Basic);
		$arrEarn_Basic= $inqTSObj->getArrRes($resEarn_Basic);
		if(count($arrEarn_Basic)>=1)
		{
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displayContent_Earn($arrEarn_Basic, $ctr);
		}
		
		$ctr++;
	}						
	
	
	$pdf->Output("../../../../TIMESHEETS/TS_BADJ_OTADJ_ALLOW_PDF/TS AND ADJ FOR COMPANY ".$_SESSION["company_code"]." GROUP ".$_SESSION["pay_group"]." PDNUM - ".$pdNum.".pdf",'',"F");
	$pdf->Output();
?>
