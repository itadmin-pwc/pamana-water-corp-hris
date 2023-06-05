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
			
			
			$this->SetFont('Arial','','10'); 
			$this->Cell(80,5,"Run Date: " . $newdate,"0");
			$this->Cell(170,5,$this->compName,"0",'0','C');
			$this->Cell(85,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			
			$this->Cell(80,5,"Report ID: PAYSUMBYDEPT01");
	
			$this->Cell(170,5,'Payroll Summary Report for GROUP :'.' '.$_SESSION["pay_group"],'0','1','C');
			if($this->hTitle!="")
				$this->Cell(330,5,$this->hTitle,'0','1','C');
			
			$this->Cell(330,5,'MONTH / YEAR : '.$this->pdHeadTitle,'0','0','C');
			
			$this->Ln();
			$this->Cell(335,3,'','');
			$this->Ln();
			$this->SetFont('Arial','B','');
			
			//330
			$this->Cell(80,6,'DEPARTMENT','1','','L');
			$this->Cell(31,6,'HEAD COUNT','1','','R');
			$this->Cell(31,6,'BASIC','1','','R');
			$this->Cell(31,6,'ABSENT','1','','R');
			$this->Cell(31,6,'TARDY / UT','1','','R');
			$this->Cell(31,6,'OT / ND','1','','R');
			$this->Cell(31,6,'OTHER INCOME','1','','R');
			$this->Cell(31,6,'ALLOWANCE','1','','R');
			$this->Cell(31,6,'TOTAL','1','','R');
			$this->Ln();
		}
		
		/*Functions for Display*/
		function dispBranch()
		{
			$qryBranch = "Select * from tblBranch
							where compCode='".$_SESSION["company_code"]."' and brnStat='A'
							and brnCode in (Select distinct(empbrnCode) from tblPayrollSummaryHist as tblPaySum 
							where compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."' 
							and pdYear = '".date("Y", strtotime($this->fromDate))."'
							and pdNumber in (".$this->qrypdNum.")   ".$this->where_empmast.")
							order by brnDesc";
			
			$resBranch = $this->execQry($qryBranch);
			$resBranch = $this->getArrRes($resBranch);
			return $resBranch;
		}
		
		function dispDivision($brnCode,$divCode)
		{
			if($divCode!=0)
				$con = "and empDivCode='".$divCode."'";
				
			$qryDivision = "Select divCode,deptShortDesc from tblDepartment
							where compCode='".$_SESSION["company_code"]."' and deptLevel='1'
							and deptStat='A'
							and divCode in 
							(Select distinct(empDivCode) from tblPayrollSummaryHist where compCode='".$_SESSION["company_code"]."'
							and payGrp='".$_SESSION["pay_group"]."'  
							and pdYear = '".date("Y", strtotime($this->fromDate))."' and pdNumber in (".$this->qrypdNum.") 
							and empBrnCode='".$brnCode."' $con)
							order by deptDesc;
							";
			$resDivision = $this->getArrRes($this->execQry($qryDivision));
			return $resDivision;
		}
		
		function dispDept($brnCode, $divCode,$srcDept)
		{
			if($divCode!=0)
				$con = "and empDivCode='".$divCode."'";
			if($srcDept!=0)
				$con.= " and empDepCode='".$srcDept."'";
				
			$qryDept = "Select deptCode,deptShortDesc from tblDepartment
							where compCode='".$_SESSION["company_code"]."' and deptLevel='2'
							and deptStat='A'
							and deptCode in 
							(Select empDepCode from tblPayrollSummaryHist where compCode='".$_SESSION["company_code"]."'
							and payGrp='".$_SESSION["pay_group"]."'  
							and pdYear = '".date("Y", strtotime($this->fromDate))."' 
							and pdNumber in (".$this->qrypdNum.") 
							and empBrnCode='".$brnCode."' $con)
							and divCode='".$divCode."'
							order by deptDesc;
							";
			
			$resDept = $this->getArrRes($this->execQry($qryDept));
			return $resDept;
		}
		
		function dispPosition($brnCode, $divCode,$srcDept)
		{
			if($divCode!=0)
				$con = "and empDivCode='".$divCode."'";
			if($srcDept!=0)
				$con.= " and empDepCode='".$srcDept."'";
				
			$qryPosition = "Select po";
		}
		
		function getHCountDetail($paySumBrnCode, $paySumDivCode, $paySumDeptCode)
		{
			$qrytblHCount = "Select count(empNo) as cntEmp from tblPayrollSummaryHist as tblPaySum 
							where compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."'  
							and pdYear = '".date("Y", strtotime($this->fromDate))."'
							and pdNumber in (".$this->qrypdNum.") and empBrnCode='".$paySumBrnCode."' and empDivCode='".$paySumDivCode."' 
							and empDepCode='".$paySumDeptCode."'";
			$restblHCount = $this->getSqlAssoc($this->execQry($qrytblHCount));
			return $restblHCount;
		}
		
		function getEarningsDetail($paySumBrnCode, $paySumDivCode, $paySumDeptCode, $trnCode, $fieldName, $trnLookUp)
		{
			if($trnLookUp=="1")
				$conTrnCode = ($trnCode!=""?"and trnCode in (Select trnCode from tblPayTransType where compCode='".$_SESSION["company_code"]."' and trnRecode='".$trnCode."' and trnStat='A')":""); 
			elseif($trnLookUp=="2")
				$conTrnCode = "and trnCode in (Select trnCode from tblPayTransType where compCode='".$_SESSION['company_code']."' and trnCat='E' and trnStat='A' and trnEntry='Y' and trnCode not in (Select trnCode from tblAllowType where compCode='".$_SESSION["company_code"]."' and allowTypeStat='A'))"; 
			elseif($trnLookUp=="3")
				$conTrnCode = "and trnCode in (Select trnCode from tblPayTransType where compCode='".$_SESSION['company_code']."' and trnCat='E' and trnStat='A'  and trnCode  in (Select trnCode from tblAllowType where compCode='".$_SESSION["company_code"]."' and allowTypeStat='A'))"; 
			
			else
				$conTrnCode = ($trnCode!=""?"and trnCode='".$trnCode."'":""); 
			
			
			$qrytblEarn = "Select ".$fieldName."
							from tblEarningsHist tblEarn, tblPayrollSummaryHist tblPaySum 
							where 
							tblEarn.compCode='".$_SESSION["company_code"]."' and tblPaySum.compCode='".$_SESSION["company_code"]."' and
							tblEarn.pdYear='".date("Y", strtotime($this->fromDate))."' and tblPaySum.pdYear='".date("Y", strtotime($this->fromDate))."' and
							tblEarn.pdNumber in (".$this->qrypdNum.") and 
							tblPaySum.pdNumber in (".$this->qrypdNum.") and 
							tblEarn.pdNumber = tblPaySum.pdNumber and
							tblEarn.pdYear = tblPaySum.pdYear and
							tblEarn.empNo = tblPaySum.empNo and
							payGrp='".$_SESSION["pay_group"]."' and 
							empBrnCode='".$paySumBrnCode."' and 
							empDivCode='".$paySumDivCode."' and
							empDepCode='".$paySumDeptCode."'
							".$conTrnCode.";";
			 /*$qrytblEarn = "Select ".$fieldName." from tblEarningsHist where compCode='".$_SESSION["company_code"]."'
			  and pdYear='".date("Y", strtotime($this->fromDate))."'
							and pdNumber in (".$this->qrypdNum.") and empNo in (Select empNo from tblPayrollSummaryHist as tblPaySum 
							where compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."' 
							and pdYear = '".date("Y", strtotime($this->fromDate))."'
							and pdNumber in (".$this->qrypdNum.") and empBrnCode='".$paySumBrnCode."' and empDivCode='".$paySumDivCode."' 
							and empDepCode='".$paySumDeptCode."') ".$conTrnCode."; ";
				*/
			$restblEarn = $this->getSqlAssoc($this->execQry($qrytblEarn));
			return $restblEarn;
		}
		
		function displayContent($resQry,$divCode, $deptCode)
		{
			$arrBrnch = $this->dispBranch();
			$noOfBrnchDisp = 1;
			$grandTotalHeadCount = $grandTotalBasic = $grandTotalAbsent = $grandTotaltardUt = $grandTotalOtNd = $total=  $grandTotal = $grandTotalAllow = 0;
		
			/*Display Per Branch*/
			foreach($arrBrnch as $arrBrnch_val)
			{
				$grandTotalHeadCount_Branch = $grandTotalBasic_Branch = $grandTotalAbsent_Branch = $grandTotaltardUt_Branch = $grandTotalOtNd_Branch = $grandTotalOthIncome_Branch = $grandTotal_Branch =$grandTotalAllow_Branch= 0;
			
				$this->SetFont('Arial','B','10'); 
				$this->Cell(47,6,"BRANCH = ".strtoupper($arrBrnch_val["brnDesc"]),0,'','L');
				$this->Ln(7);
				
				/*Display Division*/
				$arrDiv = $this->dispDivision($arrBrnch_val["brnCode"],$divCode);
				$grandTotalHeadCount_Div = $grandTotalBasic_Div = $grandTotaltardUt_Div = $grandTotalOtNd_Div = $grandTotalOthIncome_Div = $grandTotal_Div = $grandTotalAllow_Div = 0;
				
				foreach($arrDiv as $arrDiv_val)
				{
				
					$this->Cell(10,6,"",0,'','L');
					$this->Cell(47,6,"DIVISION = ".strtoupper($arrDiv_val["deptShortDesc"]),0,'','L');
					$this->Ln();
					
					/*Display Department*/
					$arrDept = $this->dispDept($arrBrnch_val["brnCode"],$arrDiv_val["divCode"],$deptCode);
					$grandTotalHeadCount_Dept = $grandTotalBasic_Dept = $grandTotaltardUt_Dept = $grandTotalOtNd_Dept = $grandTotalOthIncome_Dept = $grandTotal_Dept = $grandTotalAllow_Dept = 0;
					foreach($arrDept as $arrDept_val)
					{
					
						$this->SetFont('Arial','','9'); 
						$this->Cell(20,6,"",0,'','L');
						$this->Cell(60,6,"".strtoupper($arrDept_val["deptShortDesc"]),1,'0','L');
						
						$arrgetHCount = $this->getHCountDetail($arrBrnch_val["brnCode"], $arrDiv_val["divCode"], $arrDept_val["deptCode"]);
						$arrgetBasic = $this->getEarningsDetail($arrBrnch_val["brnCode"], $arrDiv_val["divCode"], $arrDept_val["deptCode"], EARNINGS_BASIC, 'sum(trnAmountE) as sumtrnAmountE', '');
						$arrgetAbsent = $this->getEarningsDetail($arrBrnch_val["brnCode"], $arrDiv_val["divCode"], $arrDept_val["deptCode"], EARNINGS_ABS, 'sum(trnAmountE) as sumtrnAmountE', '');
						$arrgetTard = $this->getEarningsDetail($arrBrnch_val["brnCode"], $arrDiv_val["divCode"], $arrDept_val["deptCode"], EARNINGS_TARD, 'sum(trnAmountE) as sumtrnAmountE', '');
						$arrgetUt = $this->getEarningsDetail($arrBrnch_val["brnCode"], $arrDiv_val["divCode"], $arrDept_val["deptCode"], EARNINGS_UT, 'sum(trnAmountE) as sumtrnAmountE', '');
						$sumTardandUt = $arrgetTard["sumtrnAmountE"] + $arrgetUt["sumtrnAmountE"];
						$arrgetOt = $this->getEarningsDetail($arrBrnch_val["brnCode"], $arrDiv_val["divCode"], $arrDept_val["deptCode"], EARNINGS_OT, 'sum(trnAmountE) as sumtrnAmountE', 1);
						$arrgetNd = $this->getEarningsDetail($arrBrnch_val["brnCode"], $arrDiv_val["divCode"], $arrDept_val["deptCode"], EARNINGS_ND, 'sum(trnAmountE) as sumtrnAmountE', 1);
						$sumOtandNd = $arrgetOt["sumtrnAmountE"] + $arrgetNd["sumtrnAmountE"];
						
						$arrgetOthIncome = $this->getEarningsDetail($arrBrnch_val["brnCode"], $arrDiv_val["divCode"], $arrDept_val["deptCode"], '', 'sum(trnAmountE) as sumtrnAmountE', 2);
						$arrgetAllow = $this->getEarningsDetail($arrBrnch_val["brnCode"], $arrDiv_val["divCode"], $arrDept_val["deptCode"], '', 'sum(trnAmountE) as sumtrnAmountE', 3);
						
						$total = $arrgetBasic["sumtrnAmountE"] + $arrgetAbsent["sumtrnAmountE"] + $sumTardandUt + $sumOtandNd + $arrgetOthIncome["sumtrnAmountE"] + $arrgetAllow["sumtrnAmountE"];
						
						
						$this->Cell(31,6,$arrgetHCount["cntEmp"],'1','','R');
						$this->Cell(31,6,number_format($arrgetBasic["sumtrnAmountE"],2),'1','','R');
						$this->Cell(31,6,number_format($arrgetAbsent["sumtrnAmountE"],2),'1','','R');
						$this->Cell(31,6,number_format($sumTardandUt,2),'1','','R');
						$this->Cell(31,6,number_format($sumOtandNd,2),'1','','R');
						$this->Cell(31,6,number_format($arrgetOthIncome["sumtrnAmountE"],2),'1','','R');
						$this->Cell(31,6,number_format($arrgetAllow["sumtrnAmountE"],2),'1','','R');
						$this->Cell(31,6,number_format($total,2),'1','0','R');
						$this->Ln();
						
						
						$grandTotalHeadCount_Div+=$arrgetHCount["cntEmp"];
						$grandTotalBasic_Div+=$arrgetBasic["sumtrnAmountE"];
						$grandTotalAbsent_Div+=$arrgetAbsent["sumtrnAmountE"];
						$grandTotaltardUt_Div+=$sumTardandUt;
						$grandTotalOtNd_Div+=$sumOtandNd;
						$grandTotalOthIncome_Div+=$arrgetOthIncome["sumtrnAmountE"];
						$grandTotalAllow_Div+=$arrgetAllow["sumtrnAmountE"];
						$grandTotal_Div+=$total; 
						
						
						$grandTotalHeadCount_Dept+=$arrgetHCount["cntEmp"];
						$grandTotalBasic_Dept+=$arrgetBasic["sumtrnAmountE"];
						$grandTotalAbsent_Dept+=$arrgetAbsent["sumtrnAmountE"];
						$grandTotaltardUt_Dept+=$sumTardandUt;
						$grandTotalOtNd_Dept+=$sumOtandNd;
						$grandTotalOthIncome_Dept+=$arrgetOthIncome["sumtrnAmountE"];
						$grandTotalAllow_Dept+=$arrgetAllow["sumtrnAmountE"];
						$grandTotal_Dept+=$total; 
						
						
						$grandTotalHeadCount_Branch+=$arrgetHCount["cntEmp"];
						$grandTotalBasic_Branch+=$arrgetBasic["sumtrnAmountE"];
						$grandTotalAbsent_Branch+=$arrgetAbsent["sumtrnAmountE"];
						$grandTotaltardUt_Branch+=$sumTardandUt;
						$grandTotalOtNd_Branch+=$sumOtandNd;
						$grandTotalOthIncome_Branch+=$arrgetOthIncome["sumtrnAmountE"];
						$grandTotalAllow_Branch+=$arrgetAllow["sumtrnAmountE"];
						$grandTotal_Branch+=$total; 
						
						$grandTotalHeadCount+=$arrgetHCount["cntEmp"];
						$grandTotalBasic+=$arrgetBasic["sumtrnAmountE"];
						$grandTotalAbsent+=$arrgetAbsent["sumtrnAmountE"];
						$grandTotaltardUt+=$sumTardandUt;
						$grandTotalOtNd+=$sumOtandNd;
						$grandTotalOthIncome+=$arrgetOthIncome["sumtrnAmountE"];
						$grandTotalAllow+=$arrgetAllow["sumtrnAmountE"];
						$grandTotal+=$total; 
						
					}
					$this->SetFont('Arial','B','9'); 
					$this->Cell(20,6,"",0,'','L');
					$this->Cell(60,6,'DEPARTMENT TOTALS','1','','L');
					$this->Cell(31,6,$grandTotalHeadCount_Dept,'1','','R');
					$this->Cell(31,6,number_format($grandTotalBasic_Dept,2),'1','','R');
					$this->Cell(31,6,number_format($grandTotalAbsent_Dept,2),'1','','R');
					$this->Cell(31,6,number_format($grandTotaltardUt_Dept,2),'1','','R');
					$this->Cell(31,6,number_format($grandTotalOtNd_Dept,2),'1','','R');
					$this->Cell(31,6,number_format($grandTotalOthIncome_Dept,2),'1','','R');
					$this->Cell(31,6,number_format($grandTotalAllow_Dept,2),'1','','R');
					$this->Cell(31,6,number_format($grandTotal_Dept,2),'1','','R');
					$this->Ln();
					/*End Display Department*/
					
				}
				$this->Cell(10,6,"",0,'','L');
				$this->Cell(70,6,'DIVISION TOTALS ','1','','L');
				$this->Cell(31,6,$grandTotalHeadCount_Div,'1','','R');
				$this->Cell(31,6,number_format($grandTotalBasic_Div,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotalAbsent_Div,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotaltardUt_Div,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotalOtNd_Div,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotalOthIncome_Div,2),'1','','R');
				$this->Cell(31,6,number_format(grandTotalAllow_Div,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotal_Div,2),'1','','R');
				$this->Ln();
				/*End Display Division*/
				
				$this->Cell(80,6,'BRANCH TOTAL','1','','L');
				$this->Cell(31,6,$grandTotalHeadCount_Branch,'1','','R');
				$this->Cell(31,6,number_format($grandTotalBasic_Branch,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotalAbsent_Branch,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotaltardUt_Branch,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotalOtNd_Branch,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotalOthIncome_Branch,2),'1','','R');
				$this->Cell(31,6,number_format(grandTotalAllow_Branch,2),'1','','R');
				$this->Cell(31,6,number_format($grandTotal_Branch,2),'1','','R');
				$this->Ln();
				
				$this->Ln();
			}
			/*End Display Per Branch*/
			
			$this->Cell(80,6,'GRAND TOTAL ','1','','L');
			$this->Cell(31,6,$grandTotalHeadCount,'1','','R');
			$this->Cell(31,6,number_format($grandTotalBasic,2),'1','','R');
			$this->Cell(31,6,number_format($grandTotalAbsent,2),'1','','R');
			$this->Cell(31,6,number_format($grandTotaltardUt,2),'1','','R');
			$this->Cell(31,6,number_format($grandTotalOtNd,2),'1','','R');
			$this->Cell(31,6,number_format($grandTotalOthIncome,2),'1','','R');
			$this->Cell(31,6,number_format($grandTotalAllow,2),'1','','R');
			$this->Cell(31,6,number_format($grandTotal,2),'1','','R');
			$this->Ln();
			
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Arial','B',10);
			$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}

	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$empBrnCode 		= 	$_GET['empBrnCode'];
	$pdf->fromDate		= 	$_GET["fromDate"];
	$pdf->toDate		=	$_GET["toDate"];
	
	if(date("m", strtotime($_GET["fromDate"]))==date("m", strtotime($_GET["toDate"])))
		$pdf->pdHeadTitle	= 	date("F ", strtotime($_GET["fromDate"])).", ".date("Y", strtotime($_GET["fromDate"]));
	else
		$pdf->pdHeadTitle	= 	date("F ", strtotime($_GET["fromDate"])). " - " .date("F ", strtotime($_GET["toDate"])).", ".date("Y", strtotime($_GET["fromDate"]));
	
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (tblPaySum.empDivCode = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (tblPaySum.empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empBrnCode!="0") 
	{
		$empBrnCode1 = " AND (tblPaySum.empBrnCode = '{$empBrnCode}')";
		$branchDesc = $inqTSObj->getEmpBranchArt($_SESSION["company_code"],$empBrnCode);
	} 
	else 
	{
		$empBrnCode1 = "";
	}
	
	if ($empDiv>"" && $empDiv>0) 
	{
		$empDiv2 = " AND (empDivCode= '{$empDiv}')"; 
		$divDesc =  $inqTSObj->getDivDescArt($_SESSION["company_code"], $empDiv);
	} 
	else 
	{
		$empDiv2 = "";
	}
	
	if ($empDept>"" && $empDept>0) 
	{
		$empDept2 = " AND (empDepCode = '{$empDept}')"; 
		$deptDesc = $inqTSObj->getDeptDescArt($_SESSION["company_code"], $empDiv,$empDept);
	} 
	else 
	{
		$empDept2 = "";
	}
	
	if ($empBrnCode!="0") {$empBrnCode2 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode2 = "";}
	
	$pdf->where_empmast = $empDiv1.$empDept1.$empBrnCode1;
	
	
	$hTitle = "";
	
	if ($empBrnCode!="0")
		$hTitle.= "  BRANCH :"." ".strtoupper($branchDesc["brnShortDesc"]);
	
	if ($empDiv>"" && $empDiv>0)
		$hTitle.= "   /   DIVISION : "." ".strtoupper($divDesc["deptShortDesc"]);;

	if ($empDept>"" && $empDept>0)
		$hTitle.= "   /   DEPT. : "." ".strtoupper($deptDesc["deptShortDesc"]);
	

	
	$pdf->hTitle =  $hTitle;
	
	$divCode = 	$_GET['empDiv'];
	
	$pdf->qrypdNum = "SELECT    pdNumber
					FROM       tblPayPeriod
					WHERE     (pdPayable BETWEEN '".date("m/d/Y", strtotime($fromDate))."' AND '".date("m/d/Y", strtotime($toDate))."') AND (payGrp = '".$_SESSION["pay_group"]."')";
		
	
	$tbl = "tblPayrollSummaryHist";
	$tblytd = "tblYtdDataHist";
	$tblEarn = "tblEarningsHist";
	$tblDed = "tblDeductionsHist";
	
	$pdf->fPrint = 0;
	$qryPayReg 	= "Select 
				tblPaySum.compCode,tblPaySum.empNo,
				tblPaySum.netSalary,tblPaySum.taxWitheld,
				tblPaySum.empDivCode,tblPaySum.empDepCode,tblPaySum.empSecCode,
				tblPaySum.empLocCode,tblPaySum.empBrnCode,
				tblPaySum.pdYear, tblPaySum.pdNumber,
				tblEmp.empLastName,tblEmp.empFirstName, 
				tblEmp.empMidName,tblEmp.empTeu,
				teuAmt,
				YtdGross,YtdTax, YtdTaxable, YtdGovDed
				from $tbl tblPaySum
				left join tblEmpMast tblEmp on tblPaySum.empNo=tblEmp.empNo
				left join tblTeu teu on tblEmp.empTeu=teu.teuCode
				left join $tblytd tblytd on tblytd.empNo=tblPaySum.empNo
				where tblPaySum.compCode='".$_SESSION["company_code"]."'
				AND tblPaySum.payGrp = '".$_SESSION["pay_group"]."'
				AND tblPaySum.pdYear = '".date("Y", strtotime($pdf->fromDate))."'
				AND tblPaySum.pdNumber in (".$pdf->qrypdNum.")
				$pdf->where_empmast
				order by empLastName;
				"; 
	$resPaySum = $inqTSObj->execQry($qryPayReg);
	$arrPaySum = $inqTSObj->getSqlAssoc($resPaySum);
	/*$qryBranch = "Select * from tblBranch where brnDefGrp='2' and compCode='2' order by brnDesc;";
	$resBranch = $inqTSObj->execQry($qryBranch);
	$arrBranch = $inqTSObj->getArrRes($resBranch);
	
	foreach($arrBranch as $arrBranch_val)
	{
	
		$qryPayReg = "Select sum(trnAmountE) as sumtrnAmt from tblEarningsHist
					where pdYear='2011' and pdNumber in (03,04)
					and empNo in 
						(Select empNo from tblPayrollSummaryHist as tblPaySum where compCode='2' and payGrp='2' and pdYear = '2011' and 
						pdNumber in (SELECT pdNumber FROM tblPayPeriod WHERE (pdPayable BETWEEN '02/02/2011' AND '02/28/2011') 
						AND (payGrp = '2')) and empBrnCode='".$arrBranch_val["brnCode"]."')
					and trnCode ='0100'";
		$resPaySum = $inqTSObj->execQry($qryPayReg);
		$arrPaySum = $inqTSObj->getSqlAssoc($resPaySum);
		
		echo  $arrBranch_val["brnCode"]."(". $arrBranch_val["brnDesc"].")"."=".$arrPaySum["sumtrnAmt"]."<br>";
	}*/
	if(count($arrPaySum)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrPaySum,$divCode,$empDept );
	}
	
	
	/*For Store Operations*/
	/*$pdf->fPrint = 1;
	$divCode = '7';
	$qryPayReg 	= "Select 
				tblPaySum.compCode,tblPaySum.empNo,
				tblPaySum.netSalary,tblPaySum.taxWitheld,
				tblPaySum.empDivCode,tblPaySum.empDepCode,tblPaySum.empSecCode,
				tblPaySum.empLocCode,tblPaySum.empBrnCode,
				tblPaySum.pdYear, tblPaySum.pdNumber,
				tblEmp.empLastName,tblEmp.empFirstName, 
				tblEmp.empMidName,tblEmp.empTeu,
				teuAmt,
				YtdGross,YtdTax, YtdTaxable, YtdGovDed
				from $tbl tblPaySum
				left join tblEmpMast tblEmp on tblPaySum.empNo=tblEmp.empNo
				left join tblTeu teu on tblEmp.empTeu=teu.teuCode
				left join $tblytd tblytd on tblytd.empNo=tblPaySum.empNo
				where tblPaySum.compCode='".$_SESSION["company_code"]."'
				AND tblPaySum.payGrp = '".$_SESSION["pay_group"]."'
				AND tblPaySum.pdYear = '".date("Y", strtotime($pdf->fromDate))."'
				AND tblPaySum.pdNumber in (".$pdf->qrypdNum.")
				and tblPaySum.empDivCode='7'
				$pdf->where_empmast
				order by empLastName;
				"; 
				
	$resPaySum = $inqTSObj->execQry($qryPayReg);
	$arrPaySum = $inqTSObj->getArrRes($resPaySum);
	if(count($arrPaySum)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrPaySum,$divCode,$empDept );
	}*/
	
	//$pdf->Output('pay_register.pdf','D');
$pdf->Output();
?>