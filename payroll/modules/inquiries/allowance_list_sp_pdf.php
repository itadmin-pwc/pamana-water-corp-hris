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
			
			$this->Cell(80,5,"Report ID: SUMALLOW01");
			
			if($this->fPrint==0)
			{
				$hTitle = "Details";
				$hTitle = $hTitle." Allowance Report for the Period of ".$this->pdHeadTitle;
				$this->Cell(80,5,$hTitle);
				$this->Ln();
				$this->Cell(335,3,'','');
				$this->Ln();
				$this->SetFont('Courier','B','9');
				$this->Cell(60,6,'',0); 
				$this->Cell(25,6,'EMP. NO.',0);
				$this->Cell(57,6,'EMPLOYEE NAME',0,'','L');
				$this->Cell(25,6,'AMOUNT',0,'','R');
				$this->Ln();
			}
			else
			{
				$hTitle = "Summary";
				$hTitle = $hTitle." Allowance Report for the Period of ".$this->pdHeadTitle;
				$this->Cell(80,5,$hTitle);
				$this->Ln();
				$this->Cell(335,3,'','');
				$this->Ln();
				$this->SetFont('Courier','B','9');
				$this->Cell(60,6,'TRANSACTIONS',0);
				$this->Cell(35,6,'SUM AMT.',0,'','R');
				
			}
				
			
			
				
			
		}
		
		function displayEarnContent()
		{
			
			if($this->reportType!=0)
					$where_trnCode = " trnCode='".$this->reportType."'";
			else
					$where_trnCode = "trnCode in 
									(Select distinct(trnCode) from ".$this->tbl." where
									compCode='".$_SESSION["company_code"]."' 
									and pdYear='".$this->pdYear."' 
									and (sprtPS='Y')
									and pdNumber='".$this->pdNum."' and empNo in 
										(Select empNo from tblEmpMast 
										where compCode='".$_SESSION["company_code"]."'
										AND empStat NOT IN('RS','IN','TR')
										AND empPayCat='".$_SESSION["pay_category"]."' AND empPayGrp='".$_SESSION["pay_group"]."'
										".($this->where_empmast!=""?$this->where_empmast:"")."
										)
									)";
					
					
			$qryDistinctEarnType = "Select trnCode, trnDesc 
									from tblPayTransType 
									where 
									$where_trnCode
									and compCode='".$_SESSION["company_code"]."'
									and trnStat='A'
									and trnRecode='".EARNINGS_RECODEALLOW."'
									order by trnDesc";
			
			$resDisEarnType = $this->getArrRes($this->execQry($qryDistinctEarnType));
			return $resDisEarnType;
			
		}
		
		function displayContent($resqryEarn)
		{
			$this->SetFont('Courier','','9'); 
			$arrEarnType=$this->displayEarnContent();
			$grandsumamt = 0;
			foreach ($arrEarnType as $arrEarnTypeValue) 
			{
				$this->SetFont('Courier','B','9');
				$this->Cell(60,6,strtoupper($arrEarnTypeValue['trnDesc']),0,'','L');
				$this->Ln();
				$this->SetFont('Courier','','9'); 
				$subTotal = 0;
				
				foreach($resqryEarn as $empValue)
				{
					if($arrEarnTypeValue["trnCode"]==$empValue['trnCode'])
					{
						$this->Cell(60,6,$ctr,0,'','C');
						$this->Cell(25,6,$empValue['empNo'],0);
						$this->Cell(57,6,$empValue['empLastName'] . ", ". $empValue['empFirstName'][0].".".$empValue['empMidName'][0].".",0);
						$this->Cell(25,6,number_format($empValue["trnAmountE"],2),0,'','R');
						$this->Ln();
						$subTotal+=$empValue["trnAmountE"];
					}
				}
				$this->Cell(60,6,'',0); 
				$this->SetFont('Courier','B','9');
					$this->Cell(82,6,'SUB - TOTAL',0,'','L');
					$this->Cell(25,6,number_format($subTotal,2),0,'','R');
					$grandsumamt+=$subTotal;
				$this->SetFont('Courier','','9'); 
				$this->Ln();
			}
			
			$this->Cell(60,6,'',0); 
			$this->SetFont('Courier','B','9');
				$this->Cell(82,6,'GRAND TOTAL',0,'','L');
				$this->Cell(25,6,number_format($grandsumamt,2),0,'','R');
			$this->SetFont('Courier','','9'); 
			$this->Ln(10);
			$this->Cell(335,6,'* * * End of Report * * *','0','','C'); 
		}
		
		function displaySum($resSum_Earn)
		{
			$this->SetFont('Courier','','9'); 
			$arrEarnType=$this->displayEarnContent();
			$this->Ln(10);
			
			$this->SetFont('Courier','','9');
			
			foreach($resSum_Earn as $Sum_EarnValue)
			{
				$this->Cell(60,6,strtoupper($Sum_EarnValue['trnDesc']),0,'','L');
				$this->Cell(35,6,number_format($Sum_EarnValue["totAmt"],2),0,'','R');
				$this->Ln();
			}
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
	$arrPayPd 			= 	$inqTSObj->getSlctdPd($compCode,$payPd);
	$pdf->pdNum			=	$arrPayPd["pdNumber"];
	$pdf->pdYear		=	$arrPayPd["pdYear"];
	$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$pdf->pdHeadTitle	=	$inqTSObj->valDateArt($arrPayPd['pdPayable'])." (Group ".$_SESSION[pay_group].", ".$catName['payCatDesc'].")";
	$empNo         		= 	$_GET['empNo'];
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$orderBy       		= 	$_GET['orderBy'];
	$tbl	    		= 	$_GET['tbl'];
	$topType			= 	$_GET['topType'];
	$pdf->tbl  			=  $tbl;
	$pdf->reportType	= $_GET["reportType"];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	if ($empNo>"") {$empNo1 = " AND (tblEarn.empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	if ($orderBy==1) {$orderBy1 = "  empLastName, empFirstName, empMidName, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==2) {$orderBy1 = "  tblEarn.empNo, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==3) {$orderBy1 = "  empDiv, empDepCode, empSecCode, empLastName, empFirstName, empMidName ";}
	
	if ($empNo>"") {$empNoRep = " AND (empNo LIKE '{$empNo}%')"; } else {$empNoRep = "";}
	$pdf->where_empmast = $empNoRep.$empDiv1.$empDept1.$empSect1;
	
	
	if($reportType!=0)
		$where_trnCode = " AND tblEarn.trnCode='".$reportType."'";
	
	if($topType=='SD')
	{
		$RptDetails=1;
		$RptSummary=1;
	}
	else
	{
		if($topType=='S')
			$RptSummary=1;
		else
			$RptDetails=1;
	}
	
	if($RptSummary==1)
	{
		$pdf->fPrint = 1;
		
		$qryEarn = "Select tblEarn.trnCode,
							trnDesc,sum(trnAmountE) as totAmt 
							from $tbl tblEarn,
							tblEmpMast empmast, 
							tblPayTransType ptTrans 
							where
							tblEarn.empNo=empmast.empNo
							AND tblEarn.trnCode=ptTrans.trnCode
							AND tblEarn.compCode='".$_SESSION["company_code"]."'
							AND pdYear='".$pdf->pdYear."'
							AND pdNumber='".$pdf->pdNum."'
							AND empmast.compCode='".$_SESSION["company_code"]."'
							AND empStat NOT IN('RS','IN','TR') 
							AND empPayCat = '".$_SESSION["pay_category"]."'
							AND emppayGrp = '".$_SESSION["pay_group"]."'
							AND ptTrans.compCode ='".$_SESSION["company_code"]."'
							AND trnStat = 'A'
							AND trnRecode='".EARNINGS_RECODEALLOW."'
							AND (sprtPS='Y')
							$where_trnCode
							$empNo1 $empName1 $empDiv1 $empName1 $empDept1 $empSect1
							Group by tblEarn.trnCode,trnDesc
							order by trnDesc";
							
		
		$resEarn = $inqTSObj->execQry($qryEarn);
		$arrSumEarn = $inqTSObj->getArrRes($resEarn);
		if(count($arrSumEarn)>=1)
		{
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displaySum($arrSumEarn);
		}
	}
	
	
	
	
	if($RptDetails==1)
	{
		$pdf->fPrint = 0;
		
		$qryEarn = "Select tblEarn.compCode, 
						tblEarn.trnCode,trnDesc,empmast.empNo, empLastName,
						empFirstName,empMidName,trnAmountE
						from $tbl tblEarn, tblEmpMast empmast, tblPayTransType ptTrans 
						where
						tblEarn.empNo=empmast.empNo
						AND tblEarn.trnCode=ptTrans.trnCode
						AND tblEarn.compCode='".$_SESSION["company_code"]."'
						AND pdYear='".$pdf->pdYear."'
						AND pdNumber='".$pdf->pdNum."'
						AND empmast.compCode='".$_SESSION["company_code"]."'
						AND empStat NOT IN('RS','IN','TR') 
						AND empPayCat = '".$_SESSION["pay_category"]."'
						AND emppayGrp = '".$_SESSION["pay_group"]."'
						AND ptTrans.compCode ='".$_SESSION["company_code"]."'
						AND trnStat = 'A'
						AND trnRecode='".EARNINGS_RECODEALLOW."'
						AND (sprtPS='Y')
						$where_trnCode
						$empNo1 $empName1 $empDiv1 $empName1 $empDept1 $empSect1
						order by trnDesc,$orderBy1";
		$resEarn = $inqTSObj->execQry($qryEarn);
		$arrSumEarn = $inqTSObj->getArrRes($resEarn);
		if(count($arrSumEarn)>=1)
		{
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displayContent($arrSumEarn);
		}
	}
	
	$pdf->Output();
?>
