<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("common_obj.php");
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
			
			$this->Cell(80,5,"Report ID: SUMDED01");
			
			if($this->fPrint==0)
			{
				$hTitle = "Details";
				$hTitle = $hTitle." Deduction Report for the Period of ".$this->pdHeadTitle;
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
				$hTitle = $hTitle." Deduction Report for the Period of ".$this->pdHeadTitle;
				$this->Cell(80,5,$hTitle);
				$this->Ln();
				$this->Cell(335,3,'','');
				$this->Ln();
				$this->SetFont('Courier','B','9');
				$this->Cell(60,6,'TRANSACTIONS',0);

				$this->Cell(35,6,'SUM AMT.',0,'','R');
				
			}
				
			
			
				
			
		}
		
		function displayDedContent()
		{
			if (strlen(strpos($_GET['tbl'],'Hist'))==0) 
				$PaySum = "tblPayrollSummary";
			else 
				$PaySum = "tblPayrollSummaryhist";
				
			if($this->reportType!=0)
					$where_trnCode = " trnCode='".$this->reportType."'";
			else
					$where_trnCode = "trnCode in 
									(Select distinct(trnCode) from ".$this->tbl." where
									compCode='".$_SESSION["company_code"]."' 
									and pdYear='".$this->pdYear."' 
									and pdNumber='".$this->pdNum."' and empNo in 
										(Select empNo from tblEmpMast 
										where compCode='".$_SESSION["company_code"]."'
										AND empNo IN 
											(Select empNo from $PaySum where
											pdYear='{$this->pdYear}'
											AND pdNumber = '{$this->pdNum}'
											AND payGrp = '{$_SESSION['pay_group']}'
											AND payCat = '{$_SESSION['pay_category']}'
											AND compCode = '{$_SESSION['company_code']}'
											".$this->empBrnCode1."
												)
									 AND empPayGrp='".$_SESSION["pay_group"]."'
										".($this->where_empmast!=""?$this->where_empmast:"")."
										)
									)";
					
					
			$qryDistinctDedType = "Select trnCode, trnDesc 
									from tblPayTransType 
									where 
									$where_trnCode
									and compCode='".$_SESSION["company_code"]."'
									and trnStat='A' order by trnDesc";
			
			$resDisDedType = $this->getArrRes($this->execQry($qryDistinctDedType));
			return $resDisDedType;
			
		}
		
		function dispBranch($empNoList)
		{
			if (strlen(strpos($_GET['repType'],'Hist'))==0) 
				$PaySum = "tblPayrollSummary";
			else 
				$PaySum = "tblPayrollSummaryhist";
			
			$qryBranch = "Select * from tblBranch
							where compCode='".$_SESSION["company_code"]."' and brnStat='A'
							and brnCode in (Select distinct(empbrnCode) from tblEmpMast where empNo in (".$empNoList ."))
							order by brnDesc";
			
			$resBranch = $this->execQry($qryBranch);
			$arrBranch = $this->getArrRes($resBranch);
			return $arrBranch;
		}
		
		function displayContent($resqryDed)
		{
			$this->SetFont('Courier','','9'); 
			$arrDedType=$this->displayDedContent();
			$grandsumamt = 0;
			
			foreach($resqryDed as $arrEmpList_val)
			{
				
				$empNoList.=$arrEmpList_val["empNo"].",";
			}
			
			$empNoList = substr($empNoList,0,strlen($empNoList) - 1);
				
			$arrDispBranch = $this->dispBranch($empNoList);	
			
			foreach($arrDispBranch as $arrDispBranch_val)
			{
				$this->SetFont('Courier','B','9');
				$this->Cell(60,6,$arrDispBranch_val["brnDesc"],0,'','L');
				$this->Ln();
			
				foreach ($arrDedType as $arrDedTypeValue) 
				{
					$this->SetFont('Courier','B','9');
					$this->Cell(60,6,$ctr,0,'','C');
					$this->Cell(60,6,strtoupper($arrDedTypeValue['trnDesc']),0,'','L');
					$this->Ln();
					$this->SetFont('Courier','','9'); 
					$subTotal = 0;
					//$grandsumamt = 0;
					foreach($resqryDed as $empValue)
					{
						if($arrDispBranch_val["brnCode"]==$empValue['empBrnCode'])
						{
							if($arrDedTypeValue["trnCode"]==$empValue['trnCode'])
							{
								
								$this->Cell(60,6,$ctr,0,'','C');
								$this->Cell(25,6,$empValue['empNo'],0);
								$this->Cell(57,6,$empValue['empLastName'] . ", ". $empValue['empFirstName'][0].".".$empValue['empMidName'][0].".",0);
								$this->Cell(25,6,number_format($empValue["trnAmountD"],2),0,'','R');
								$this->Ln();
								$subTotal+=$empValue["trnAmountD"];
							}
						}
					}
					$this->Cell(60,6,'',0); 
					$this->SetFont('Courier','B','9');
						$this->Cell(82,6,'SUB - TOTAL',0,'','L');
						$this->Cell(25,6,number_format($subTotal,2),0,'','R');
						$grandsumamt+=$subTotal;
					$this->SetFont('Courier','','9'); 
					$this->Ln(10);
					
				}
					$this->Ln();
					$this->Cell(60,6,'',0); 
					$this->SetFont('Courier','B','9');
					$this->Cell(82,6,'GRAND TOTAL',0,'','L');
					$this->Cell(25,6,number_format($grandsumamt,2),0,'','R');
			
				$this->SetFont('Courier','','9');
				unset($grandsumamt);
				$this->Ln();
			}
			//$this->Ln(10);
			
			//$this->Cell(335,6,'* * * End of Report * * *','0','','C'); 
		}
		
		function displaySum($resSum_Deduct)
		{
			$this->SetFont('Courier','','9'); 
			$arrDedType=$this->displayDedContent();
			$this->Ln(10);
			
			$this->SetFont('Courier','','9');
			
			foreach($resSum_Deduct as $Sum_DeductValue)
			{
				$this->Cell(60,6,strtoupper($Sum_DeductValue['trnDesc']),0,'','L');
				$this->Cell(35,6,number_format($Sum_DeductValue["totAmt"],2),0,'','R');
				$this->Ln();
			}
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
	$arrPayPd 			= 	$inqTSObj->getSlctdPd($compCode,$_GET['payPd']);
	$pdf->pdNum			=	$arrPayPd["pdNumber"];
	$pdf->pdYear		=	$arrPayPd["pdYear"];
	$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$pdf->pdHeadTitle	=	$inqTSObj->valDateArt($arrPayPd['pdPayable'])." (Group ".$_SESSION[pay_group].", ".$catName['payCatDesc'].")";
	$empNo         		= 	$_GET['empNo'];
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$orderBy       		= 	$_GET['orderBy'];
	$tbl	    		= 	$_GET['repType'];
	$topType			= 	$_GET['topType'];
	$pdf->empBrnCode 	= $_GET['empBrnCode'];
	$pdf->tbl  			=  $tbl;
	$pdf->reportType	= $_GET['reportType'];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	if (strlen(strpos($_GET['repType'],'Hist'))==0) 
		$PaySum = "tblPayrollSummary";
	else 
		$PaySum = "tblPayrollSummaryhist";
	
	if ($empNo>"") {$empNo1 = " AND ($tbl.empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	if ($empNo>"") {$empNoRep = " AND (empNo LIKE '{$empNo}%')"; } else {$empNoRep = "";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	
	$pdf->empBrnCode1 = $empBrnCode1;
	$pdf->where_empmast = $empNoRep.$empDiv1.$empDept1.$empSect1;
	
	
	if($pdf->reportType!=0)
		$where_trnCode = " AND $tbl.trnCode='".$pdf->reportType."'";
	
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
	
	
	if($RptDetails==1)
	{
		$pdf->fPrint = 0;
		
		$qryDeduct = "Select $tbl.compCode, 
						$tbl.trnCode,trnDesc,empmast.empNo as empNo, empLastName,
						empFirstName,empMidName,trnAmountD,empBrnCode
						from $tbl , tblEmpMast empmast, tblPayTransType ptTrans 
						where
						$tbl.empNo=empmast.empNo
						AND $tbl.trnCode=ptTrans.trnCode
						AND $tbl.compCode='".$_SESSION["company_code"]."'
						AND pdYear='".$pdf->pdYear."'
						AND pdNumber='".$pdf->pdNum."'
						AND empmast.compCode='".$_SESSION["company_code"]."'
						AND empmast.empNo IN 
				 				(Select empNo from $PaySum where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
			
								   )
						AND emppayGrp = '".$_SESSION["pay_group"]."'
						AND ptTrans.compCode ='".$_SESSION["company_code"]."'
						AND trnStat = 'A'
						$where_trnCode
						$empNo1 $empDiv1 $empDept1 $empSect1 
						order by trnDesc, empLastName,empFirstName,empMidName";
		$resDeduct = $inqTSObj->execQry($qryDeduct);
		$numEmp = $inqTSObj->getRecCount($resDeduct);
		
		$arrDeduct = $inqTSObj->getArrRes($resDeduct);
		if(count($arrDeduct)>=1)
		{
			
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displayContent($arrDeduct);
		}
	}
	
	if($RptSummary==1)
	{
		$pdf->fPrint = 1;
		
		$qry_Sum_Deduct = "Select $tbl.trnCode,
							trnDesc,sum(trnAmountD) as totAmt 
							from $tbl ,
							tblEmpMast empmast, 
							tblPayTransType ptTrans 
							where
							$tbl.empNo=empmast.empNo
							AND $tbl.trnCode=ptTrans.trnCode
							AND $tbl.compCode='".$_SESSION["company_code"]."'
							AND pdYear='".$pdf->pdYear."'
							AND pdNumber='".$pdf->pdNum."'
							AND empmast.compCode='".$_SESSION["company_code"]."'
							AND empmast.empNo IN 
				 				(Select empNo from $PaySum where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								 ".$pdf->empBrnCode1."   )
							AND emppayGrp = '".$_SESSION["pay_group"]."'
							AND ptTrans.compCode ='".$_SESSION["company_code"]."'
							AND trnStat = 'A'
							$where_trnCode
							$empNo1 $empName1 $empDiv1 $empName1 $empDept1 $empSect1
							Group by $tbl.trnCode,trnDesc
							order by trnDesc";
		$resSum_Deduct = $inqTSObj->execQry($qry_Sum_Deduct);
		$arrSum_Deduct = $inqTSObj->getArrRes($resSum_Deduct);
		if(count($arrSum_Deduct)>=1)
		{
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displaySum($arrSum_Deduct);
		}
	}
	
	
$pdf->Output('deduction_type.pdf','D');
?>
