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
			
			
			if($this->fPrint==0)
			{
				$this->Cell(80,5,"Report ID: PAYREG01");
				$hTitle = "Regular";
				$hTitle = $hTitle." Payroll Register for the Period of ".$this->pdHeadTitle;
				$this->Cell(80,5,$hTitle);
				$this->Ln();
				$this->Cell(335,3,'','');
				$this->Ln();
				$this->SetFont('Courier','B','');
				$this->Cell(47,6,'NAME','','','L');
				$this->Cell(24,6,'TAX STAT','','','C');
				$this->Cell(24,6,'YTD - INC','','','R');
				$this->Cell(24,6,'SALARY','','','R');
				$this->Cell(24,6,'LWOP','','','R');
				$this->Cell(24,6,'OVERTIME','','','R');
				$this->Cell(24,6,'ALLOWANCE','','','R');
				$this->Cell(24,6,'TOTAL','','','R');
				$this->Cell(24,6,'WITH TAX','','','R');
				$this->Cell(24,6,'SSS','','','R');
				$this->Cell(24,6,'LOANS','','','R');
				$this->Cell(24,6,'TOTAL DED','','','R');
				$this->Cell(24,6,'NET','','','R');
				$this->Ln();
				$this->Cell(47,1,'ID # RANK','','','L');
				$this->Cell(24,1,'  ','','','C');
				$this->Cell(24,1,'YTD - TAX','','','R');
				$this->Cell(24,1,'  ','','','R');
				$this->Cell(24,1,'TARDINESS','','','R');
				$this->Cell(24,1,'NIGHT PREM','','','R');
				$this->Cell(24,1,'OTH. INC','','','R');
				$this->Cell(24,1,'GROSS','','','R');
				$this->Cell(24,1,'  ','','','R');
				$this->Cell(24,1,'HDMF','','','R');
				$this->Cell(24,1,'OTHER DED','','','R');
				$this->Cell(24,1,'  ','','','R');
				$this->Cell(24,1,'SALARY','','','R');
				$this->Ln();
				
				$this->Cell(47,6,'  ','','','C');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'UT','','','R');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'INCOME','','','R');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'PHIC','','','R');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'  ','','','C');
				$this->Ln();
			}
			else
			{
				$this->Cell(80,5,"Report ID: PAYREG02");
				$hTitle = "Allowance";
				$hTitle = $hTitle." Payroll Register for the Period of ".$this->pdHeadTitle;
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
		}
		
		/*Functions for Display*/
		
		
		
		
		function getDatatblEarnings($trnCode,$pdYear,$pdNumber,$reportType)
		{
			$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
			$qryEarnings = "Select * from ".$tbltable." 
							where trnCode='".$trnCode."' and compCode='{$_SESSION['company_code']}'
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'";
			$resEarnings = $this->execQry($qryEarnings);
			$resEarnings = $this->getArrRes($resEarnings);
			
			return $resEarnings;
		}
		
		function getDatatblEarningsOTND($trnRec,$pdYear,$pdNumber,$reportType)
		{
			$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
			$qryEarningsOTND = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable." 
							where trnCode in (Select trnCode from tblPayTransType where trnRecode='$trnRec') and compCode='{$_SESSION['company_code']}'
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
							group by empNo
							";
			
			$resEarningsOTND = $this->execQry($qryEarningsOTND);
			$resEarningsOTND = $this->getArrRes($resEarningsOTND);
			return $resEarningsOTND;
		}
		
		function getDatatblEarningsAllow($trnRec,$pdYear,$pdNumber,$reportType)
		{
			$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
			$qryEarningsAllow = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable." 
							where trnCode in (Select trnCode from tblAllowType where compCode='{$_SESSION['company_code']}' and allowTypeStat='A' and (sprtPS='N' or sprtPS is null or sprtPS='')) and compCode='{$_SESSION['company_code']}'
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
							group by empNo
							";
			
			$resEarningsAllow = $this->execQry($qryEarningsAllow);
			$resEarningsAllow = $this->getArrRes($resEarningsAllow);
			return $resEarningsAllow;
		}
		
		function getDataOthtblEarnings($trnRec,$pdYear,$pdNumber,$reportType)
		{
			$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
			$qryEarningsOth = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable."
							where trnCode in (Select trnCode from tblPayTransType where compCode='{$_SESSION['company_code']}' and trnCat='E' and trnStat='A' and trnEntry='Y' )
							and compCode='{$_SESSION['company_code']}'
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
							group by empNo
							";
			$resEarningsOth = $this->execQry($qryEarningsOth);
			$resEarningsOth = $this->getArrRes($resEarningsOth);
			return $resEarningsOth;
		}
		
		function getDataOthtblDeductions($trnCd,$pdYear,$pdNumber,$reportType)
		{
			$tbltable = ($reportType==1?"tblDeductionsHist":"tblDeductions");
			$qryDeductions = "Select sum(trnAmountD) as totAmountD,empNo from ".$tbltable." where trnCode='".$trnCd."'
							and compCode='{$_SESSION['company_code']}'
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
							group by empNo
							";
			$resDeductions = $this->execQry($qryDeductions);
			$resDeductions = $this->getArrRes($resDeductions);
			return $resDeductions;
		}
		
		function getDataLoanstblDeductions($trnRec,$pdYear,$pdNumber,$reportType)
		{
			$tbltable = ($reportType==1?"tblDeductionsHist":"tblDeductions");
			$qryLoansDeductions = "Select sum(trnAmountD) as totAmountD,empNo from ".$tbltable." 
							where trnCode in (Select trnCode from tblLoanType where compCode='{$_SESSION['company_code']}' and lonTypeStat='A') and compCode='{$_SESSION['company_code']}'
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
							group by empNo
							";
			$resLoansDeductions = $this->execQry($qryLoansDeductions);
			$resLoansDeductions = $this->getArrRes($resLoansDeductions);
			return $resLoansDeductions;
		}
		
		function getDataOthAdjtblDeductions($trnRec,$pdYear,$pdNumber,$reportType)
		{
			$tbltable = ($reportType==1?"tblDeductionsHist":"tblDeductions");
			$qryOthAdjDeductions = "Select sum(trnAmountD) as totAmountD,empNo from ".$tbltable." 
							where trnCode in (Select trnCode from tblPayTransType 
							where trnCode not in (Select trnCode from tblLoanType where compCode='{$_SESSION['company_code']}') and trnCat='D' and trnStat='A' and trnEntry='Y')
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' 
							group by empNo
							";
			$resOthAdjDeductions = $this->execQry($qryOthAdjDeductions);
			$resOthAdjDeductions = $this->getArrRes($resOthAdjDeductions);
			return $resOthAdjDeductions;
		}
		
		function compGrossTaxable($reportType,$pdNum,$pdYear)
		{
			$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
			$qrycompGrossTax = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable."
								where trnCode in (SELECT trnCode
								from   tblPayTransType
								where  (compCode = '{$_SESSION['company_code']}') AND (trnCat = 'E') AND (trnTaxCd = 'Y') AND (trnStat = 'A'))
								AND pdNumber='".$pdNum."' and pdYear='".$pdYear."' and (sprtPS='N' or sprtPS is null or sprtPS='') group by empNo";
			 
			$rescompGrossTax = $this->execQry($qrycompGrossTax);
			$rescompGrossTax = $this->getArrRes($rescompGrossTax);
			return $rescompGrossTax;
		}
		
		function compGrossNonTaxable($reportType,$pdNum,$pdYear)
		{
			$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
			$qrycompGrossNonTax = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable."
								where trnCode in (SELECT trnCode
								from   tblPayTransType
								where  (compCode = '{$_SESSION['company_code']}') AND (trnCat = 'E') AND (trnTaxCd = 'N') 
								AND (trnStat = 'A')) AND pdNumber='".$pdNum."' and pdYear='".$pdYear."' 
								group by empNo";
			$rescompGrossNonTax = $this->execQry($qrycompGrossNonTax);
			$rescompGrossNonTax = $this->getArrRes($rescompGrossNonTax);
			return $rescompGrossNonTax;
		}
	
		function dispDivision($pdYear,$pdNum,$divCode,$reportType)
		{
			$tbltable = ($reportType==1?"tblPayrollSummaryHist":"tblPayrollSummary");
			if($divCode!=0)
				$con = "and empDivCode='".$divCode."'";
				
			$qryDivision = "Select divCode,deptDesc from tblDepartment
							where compCode='".$_SESSION["company_code"]."' and deptLevel='1'
							and deptStat='A'
							and divCode in 
							(Select empDivCode from ".$tbltable." where compCode='".$_SESSION["company_code"]."'
							and payGrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."' 
							and pdYear='".$pdYear."' and pdNumber='".$pdNum."' $con)
							order by deptDesc;
							";
			$resDivision = $this->getArrRes($this->execQry($qryDivision));
			return $resDivision;
		}
		
		function dispDept($pdYear,$pdNum,$divCode,$reportType,$srcDept)
		{
			$tbltable = ($reportType==1?"tblPayrollSummaryHist":"tblPayrollSummary");
			if($divCode!=0)
				$con = "and empDivCode='".$divCode."'";
			if($srcDept!=0)
				$con.= " and empDepCode='".$srcDept."'";
				
			$qryDept = "Select deptCode,deptDesc from tblDepartment
							where compCode='".$_SESSION["company_code"]."' and deptLevel='2'
							and deptStat='A'
							and deptCode in 
							(Select empDepCode from ".$tbltable." where compCode='".$_SESSION["company_code"]."'
							and payGrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."' 
							and pdYear='".$pdYear."' and pdNumber='".$pdNum."' $con)
							and divCode='".$divCode."'
							order by deptDesc;
							";
			$resDept = $this->getArrRes($this->execQry($qryDept));
			return $resDept;
		}
		
		function dispSect($pdYear,$pdNum,$divCode,$deptCode,$reportType,$srcDept,$srcSect)
		{
			$tbltable = ($reportType==1?"tblPayrollSummaryHist":"tblPayrollSummary");
			if($divCode!=0)
				$con = "and empDivCode='".$divCode."'";
			if($srcDept!=0)
				$con.= " and empDepCode='".$srcDept."'";
			if($srcSect!=0)
				$con.= " and empSecCode='".$srcSect."'";
				
			$qrySect = "Select sectCode,deptDesc from tblDepartment
							where compCode='".$_SESSION["company_code"]."' and deptLevel='3'
							and deptStat='A'
							and sectCode in 
							(Select empSecCode from ".$tbltable." where compCode='".$_SESSION["company_code"]."'
							and payGrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."' 
							and pdYear='".$pdYear."' and pdNumber='".$pdNum."' $con)
							and divCode='".$divCode."' 
							and deptCode='".$deptCode."'
							order by deptDesc;
							";
			
			$resSect = $this->getArrRes($this->execQry($qrySect));
			return $resSect;
		}
		
		function displayEarnContent()
		{
			
			
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
		
		function displayContentDetails($resqryEarn)
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
					$grandsumamt = 0;
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
				$this->Ln();
				$this->Cell(335,6,'* * * End of Report * * *','0','','C');
			}
		
		function displayContent($resQry,$reportType,$pdYear,$pdNum,$divCode,$empDept,$empSec)
		{
			$this->SetFont('Courier','','9'); 
			$grandsumamt = 0;
			
			$deptcd = "";
			$cntEmp = 0;
			$diff_cnt_emp = 0;
			$Empcnt = 0;
			$totempSal = 0;
			$totempAttend = 0;
			$totempLWOP = 0;
			$totempTard = 0;
			$totempUt = 0;
			$totempOT = 0;
			$totempND = 0;
			$totempAllow = 0;
			$totOtherEarn = 0;
			$totempGross = 0;
			$totempIncome = 0;
			$totEmptax = 0;
			$totempSss = 0;
			$totempHDMF = 0;
			$totempPHIC = 0;
			$totempLoans = 0;
			$totempOthDed = 0;
			$totempDed = 0;
			$totempNetPay = 0;
			
			$grandtotemp = 0;
			$grandempEarningsSalary = 0;
			$grandempEarningsLWOP = 0;
			$grandempEarningsOT = 0;
			$grandempEarningsAllow=0;
			$grandGrossTaxable=0;
			$grandempWithTax=0;
			$grandempDeductionsSSS=0;
			$grandempLoans=0;
			$grandEarningsTARD=0;
			$grandempEarningsND=0;
			$grandempEarningsOth=0;
			$grandGrossNonTaxable=0;
			$grandDeductionsHDMF=0;
			$grandempOthAdj=0;
			$grandempEarningsUT=0;
			$grandempDeductionsPHIC=0;
			$grandtotDed = 0;
			$grandnetPay = 0;
		
			$empcntdept = 0;
			$cntqryemp = 0;
			$grandtotemp = 0;
			$subempEarningsSalary = 0;
			$subempEarningsLWOP = 0;
			$subempEarningsOT = 0;
			$subempEarningsAllow=0;
			$subGrossTaxable=0;
			$subempWithTax=0;
			$subempDeductionsSSS=0;
			$subempLoans=0;
			$subEarningsTARD=0;
			$subempEarningsND=0;
			$subempEarningsOth=0;
			$subGrossNonTaxable=0;
			$subDeductionsHDMF=0;
			$subempOthAdj=0;
			$subempEarningsUT=0;
			$subempDeductionsPHIC=0;
			$subtotDed = 0;
			$subnetPay = 0;
			
			
			$getempEarningsSalary = $this->getDatatblEarnings(EARNINGS_BASIC,$pdYear,$pdNum,$reportType);
			$getempEarningsLWOP = $this->getDatatblEarnings(EARNINGS_ABS,$pdYear,$pdNum,$reporType);
			$getempEarningsUT = $this->getDatatblEarnings(EARNINGS_UT,$pdYear,$pdNum,$reporType);
			$getempEarningsTARD = $this->getDatatblEarnings(EARNINGS_TARD,$pdYear,$pdNum,$reporType);
			$getempEarningsOT = $this->getDatatblEarningsOTND(EARNINGS_OT,$pdYear,$pdNum,$reporType);
			$getempEarningsND = $this->getDatatblEarningsOTND(EARNINGS_ND,$pdYear,$pdNum,$reporType);
			$getempEarningsAllow = $this->getDatatblEarningsAllow('',$pdYear,$pdNum,$reporType);
			$getempEarningsOth = $this->getDataOthtblEarnings('',$pdYear,$pdNum,$reporType);
			$getempDeductionsSSS = $this->getDataOthtblDeductions(SSS_CONTRIB,$pdYear,$pdNum,$reporType);
			$getempDeductionsHDMF = $this->getDataOthtblDeductions(PAGIBIG_CONTRIB,$pdYear,$pdNum,$reporType);
			$getempDeductionsPHIC = $this->getDataOthtblDeductions(PHILHEALTH_CONTRIB,$pdYear,$pdNum,$reporType);
			$getempListLoans = $this->getDataLoanstblDeductions('',$pdYear,$pdNum,$reporType);
			$getempOthAdjDed = $this->getDataOthAdjtblDeductions('',$pdYear,$pdNum,$reporType);
			$getempGrossTaxable = $this->compGrossTaxable($reporType,$pdNum,$pdYear);
			$getempGrossNonTax = $this->compGrossNonTaxable($reporType,$pdNum,$pdYear);
			
			$ctrEmpGrandTot=0;
			$totGrandTotYtdGross = 0;
			$totGrandTotLWOP=0;
			$totGrandTotOT=0;
			$totGrandTotAllow=0;
			$totGrandTotGross=0;
			$totGrandTotWithTax=0;
			$totGrandTotSSS=0;
			$totGrandTotLoans=0;
			$totGrandTotYtdTax=0;
			$totGrandTotTard=0;
			$totGrandTotNightDiff=0;
			$totGrandTotOthInc=0;
			$totGrandTotGrossInc=0;
			$totGrandTotHdmf=0;
			$totGrandTotOthDed=0;
			$totGrandTotUt=0;
			$totGrandTotPhic=0;
			$totGrandTotTotDed=0;
			$totGrandTotNetSal=0;
			
			/*Display Division*/
			$arrDiv = $this->dispDivision($pdYear,$pdNum,$divCode,$reportType);
			foreach($arrDiv as $arrDiv_val)
			{
				$ctrEmpDiv=0;
				$totDivYtdGross = 0;
				$totDivLWOP=0;
				$totDivOT=0;
				$totDivAllow=0;
				$totDivGross=0;
				$totDivWithTax=0;
				$totDivSSS=0;
				$totDivLoans=0;
				$totDivYtdTax=0;
				$totDivTard=0;
				$totDivNightDiff=0;
				$totDivOthInc=0;
				$totDivGrossInc=0;
				$totDivHdmf=0;
				$totDivOthDed=0;
				$totDivUt=0;
				$totDivPhic=0;
				$totDivTotDed=0;
				$totDivNetSal=0;
				$this->SetFont('Courier','B','9'); 
				$this->Cell(47,6,"DIVISION = ".strtoupper($arrDiv_val["deptDesc"]),0,'','L');
				$this->Ln(7);
				
				/*Display Department*/
				$arrDept = $this->dispDept($pdYear,$pdNum,$arrDiv_val["divCode"],$reportType,$empDept);
				foreach($arrDept as $arrDept_val)
				{
					$ctrEmpDept=0;
					$totDeptYtdGross = 0;
					$totDeptLWOP=0;
					$totDeptOT=0;
					$totDeptAllow=0;
					$totDeptGross=0;
					$totDeptWithTax=0;
					$totDeptSSS=0;
					$totDeptLoans=0;
					$totDeptYtdTax=0;
					$totDeptTard=0;
					$totDeptNightDiff=0;
					$totDeptOthInc=0;
					$totDeptGrossInc=0;
					$totDeptHdmf=0;
					$totDeptOthDed=0;
					$totDeptUt=0;
					$totDeptPhic=0;
					$totDeptTotDed=0;
					$totDeptNetSal=0;
					
					$this->SetFont('Courier','B','9'); 
					$this->Cell(47,6,"   DEPT. = ".strtoupper($arrDept_val["deptDesc"]),0,'','L');
					$this->Ln();
					
					/*Display Section*/
					$arrSec = $this->dispSect($pdYear,$pdNum,$arrDiv_val["divCode"],$arrDept_val["deptCode"],$reportType,$empDept,$empSec);
					foreach($arrSec as $arrSec_val)
					{
						$ctrEmpSect=0;
						$totSectYtdGross = 0;
						$totSectLWOP=0;
						$totSectOT=0;
						$totSectAllow=0;
						$totSectGross=0;
						$totSectWithTax=0;
						$totSectSSS=0;
						$totSectLoans=0;
						$totSectYtdTax=0;
						$totSectTard=0;
						$totSectNightDiff=0;
						$totSectOthInc=0;
						$totSectGrossInc=0;
						$totSectHdmf=0;
						$totSectOthDed=0;
						$totSectUt=0;
						$totSectPhic=0;
						$totSectTotDed=0;
						$totSectNetSal=0;
						
						$this->SetFont('Courier','B','9'); 
						$this->Cell(47,6,"      SECT. = ".strtoupper($arrSec_val["deptDesc"]),0,'','L');
						$this->Ln();
						
						/*Display Employees*/
						foreach($resQry as $resQryValue)
						{
							/*Display Per Division*/
							if($arrDiv_val["divCode"]==$resQryValue["empDivCode"])
							{
								/*Display Per Department*/
								if($arrDept_val["deptCode"]==$resQryValue["empDepCode"])
								{
									/*Display Per Section*/
									if($arrSec_val["sectCode"]==$resQryValue["empSecCode"])
									{
										$this->SetFont('Courier','','9'); 
										$this->Cell(47,6,$resQryValue["empLastName"].", ".$resQryValue["empFirstName"][0].",".$resQryValue["empMidName"][0],'','','L');
										$this->Cell(24,6,$resQryValue["empTeu"],'','','R');
										$this->Cell(24,6,number_format($resQryValue["YtdGross"],2),'','','R');
										
										/*BASIC SALARY*/
										foreach ($getempEarningsSalary  as $getempEarningsSalaryValue) {
											if ($getempEarningsSalaryValue['empNo']==$resQryValue["empNo"]) {
												$rowEarningsSalary=$getempEarningsSalaryValue["trnAmountE"];
											}
										}
										$rowEarningsSalary = ($rowEarningsSalary!=""?$rowEarningsSalary:0);
										$grossIncome = $rowEarningsSalary;
										$grandempEarningsSalary+=$rowEarningsSalary;
										$subempEarningsSalary+=$rowEarningsSalary;
										$this->Cell(24,6,number_format($rowEarningsSalary,2),'','','R');
										
										/*LWOP*/
										foreach ($getempEarningsLWOP  as $getempEarningsLWOPValue) {
											if ($getempEarningsLWOPValue['empNo']==$resQryValue["empNo"]) {
												$rowEarningsLWOP=$getempEarningsLWOPValue["trnAmountE"];
											}
										}
										$rowEarningsLWOP = ($rowEarningsLWOP!=""?$rowEarningsLWOP:0);
										$grandempEarningsLWOP+=$rowEarningsLWOP;
										$subempEarningsLWOP+=$rowEarningsLWOP;
										$this->Cell(24,6,number_format($rowEarningsLWOP,2),'','','R');
										
										/*OT*/
										foreach ($getempEarningsOT  as $rowEarningsOTValue) {
											if ($rowEarningsOTValue['empNo']==$resQryValue["empNo"]) {
												$rowEarningsOT=$rowEarningsOTValue["totAmountE"];
											}
										}
										$rowEarningsOT = ($rowEarningsOT!=""?$rowEarningsOT:0);
										$grandempEarningsOT+=$rowEarningsOT;
										$subempEarningsOT+=$rowEarningsOT;
										$grossIncome +=$rowEarningsOT;
										$this->Cell(24,6,number_format($rowEarningsOT,2),'','','R');
										
										/*ALLOWANCE*/
										foreach ($getempEarningsAllow  as $getempEarningsAllowValue) {
											if ($getempEarningsAllowValue['empNo']==$resQryValue["empNo"]) {
												$rowEarningsAllow=$getempEarningsAllowValue["totAmountE"];
											}
										}
										
										$rowEarningsAllow = ($rowEarningsAllow!=""?$rowEarningsAllow:0);
										$grandempEarningsAllow+=$rowEarningsAllow;
										$subempEarningsAllow+=$rowEarningsAllow;
										$othincome = $rowEarningsAllow;
										$this->Cell(24,6,number_format($rowEarningsAllow,2),'','','R');
										
										/*GROSS INCOME TAXABLE*/
										foreach($getempGrossTaxable as $getempGrossTaxableValue)
										{
											if ($getempGrossTaxableValue['empNo']==$resQryValue["empNo"]) {
													$rowGrossTaxable=$getempGrossTaxableValue["totAmountE"];
											}
										}
										$rowGrossTaxable = ($rowGrossTaxable!=""?$rowGrossTaxable:0);
										$grandGrossTaxable+=$rowGrossTaxable;
										$subGrossTaxable+=$rowGrossTaxable;
										$this->Cell(24,6,number_format($rowGrossTaxable,2),'','','R');
										
										
										/*WITH TAX*/
										$rowWithTax= ($resQryValue["taxWitheld"]!=""?$resQryValue["taxWitheld"]:0);
										$grandempWithTax+=$rowWithTax;
										$subempWithTax+=$rowWithTax;
										$totDed =$rowWithTax;
										
										$totempIncome += $totIncome;
										$this->Cell(24,6,number_format($rowWithTax,2),'','','R');
										
										/*SSS*/
										foreach($getempDeductionsSSS as $getempDeductionsSSSValue)
										{
											if ($getempDeductionsSSSValue['empNo']==$resQryValue["empNo"]) {
													$rowDeductionsSSS=$getempDeductionsSSSValue["totAmountD"];
											}
										}
										$rowDeductionsSSS = ($rowDeductionsSSS!=""?$rowDeductionsSSS:0);
										$grandempDeductionsSSS+=$rowDeductionsSSS;
										$subempDeductionsSSS+=$rowDeductionsSSS;
										$totDed +=$rowDeductionsSSS;
										$this->Cell(24,6,number_format($rowDeductionsSSS,2),'','','R');
										
										/*LOANS AND OTHER ADJ. WITH THE SAME TRN CODE*/
										foreach($getempListLoans as $getempLoansValue)
										{
											if ($getempLoansValue['empNo']==$resQryValue["empNo"]) {
												$getempLoans=$getempLoansValue["totAmountD"];
											}
										}
										$getempLoans = ($getempLoans!=""?$getempLoans:0);
										$grandempLoans+=$getempLoans;
										$subempLoans+=$getempLoans;
										$totDed +=$getempLoans;
										$this->Cell(24,6,number_format($getempLoans,2),'','','R');
										
										$this->Cell(24,6,'','','','R');
										$this->Cell(24,6,'','','1','R');
										
										$this->Cell(47,0,$resQryValue["empNo"],'','','');
										
										$this->Cell(24,0,number_format($resQryValue["teuAmt"],2),'','','R');
										$this->Cell(24,0,number_format($resQryValue["YtdTax"],2),'','','R');
										$this->Cell(24,0,'','','','R');
										
										/*TARD*/
										foreach ($getempEarningsTARD  as $getempEarningsTARDValue) {
											if ($getempEarningsTARDValue['empNo']==$resQryValue["empNo"]) {
												$rowEarningsTARD=$getempEarningsTARDValue["trnAmountE"];
											}
										}
										$rowEarningsTARD = ($rowEarningsTARD!=""?$rowEarningsTARD:0);
										$grandEarningsTARD+=$rowEarningsTARD;
										$subEarningsTARD+=$rowEarningsTARD;
										
										$this->Cell(24,0,number_format($rowEarningsTARD,2),'','','R');
										
										/*NIGHT DIFF*/
										foreach ($getempEarningsND  as $rowEarningsNDValue) {
											if ($rowEarningsNDValue['empNo']==$resQryValue["empNo"]) {
												$rowEarningsND=$rowEarningsNDValue["totAmountE"];
											}
										}
										$rowEarningsND=($rowEarningsND!=""?$rowEarningsND:0);
										$grandempEarningsND+=$rowEarningsND;
										$subempEarningsND+=$rowEarningsND;
										$grossIncome +=$rowEarningsND;
										$this->Cell(24,0,number_format($rowEarningsND,2),'','','R');
										
										/*OTHER EARNINGS*/
										foreach ($getempEarningsOth  as $getempEarningsOthValue) {
											if ($getempEarningsOthValue['empNo']==$resQryValue["empNo"]) {
												$rowEarningsOth=$getempEarningsOthValue["totAmountE"];
											}
										}
										$rowEarningsOth=($rowEarningsOth!=""?$rowEarningsOth:0);
										$grandempEarningsOth+=$rowEarningsOth;
										$subempEarningsOth+=$rowEarningsOth;
										$othincome+=$rowEarningsOth;
										$this->Cell(24,0,number_format($rowEarningsOth,2),'','','R');
										
										/*GROSS INCOME NON TAXABLE*/
										foreach($getempGrossNonTax as $getempGrossNonTaxValue)
										{
											if ($getempGrossNonTaxValue['empNo']==$resQryValue["empNo"]) {
													$rowGrossNonTaxable=$getempGrossNonTaxValue["totAmountE"];
											}
										}
										$rowGrossNonTaxable = ($rowGrossNonTaxable!=""?$rowGrossNonTaxable:0);
										$grandGrossNonTaxable+=$rowGrossNonTaxable;
										$subGrossNonTaxable+=$rowGrossNonTaxable;
										$this->Cell(24,0,number_format($rowGrossNonTaxable,2),'','','R');
										$this->Cell(24,0,'','','','R');
										
										/*HDMF*/
										foreach($getempDeductionsHDMF as $getempDeductionsHDMFValue)
										{
											if ($getempDeductionsHDMFValue['empNo']==$resQryValue["empNo"]) {
													$rowDeductionsHDMF=$getempDeductionsHDMFValue["totAmountD"];
											}
										}
										$rowDeductionsHDMF = ($rowDeductionsHDMF!=""?$rowDeductionsHDMF:0);
										$grandDeductionsHDMF+=$rowDeductionsHDMF;
										$subDeductionsHDMF+=$rowDeductionsHDMF;
										$totDed +=$rowDeductionsHDMF;
										$this->Cell(24,0,number_format($rowDeductionsHDMF,2),'','','R');
										
										
										/*OTHER DEDUCTIONS*/
										foreach($getempOthAdjDed as $getempOthAdjDedValue)
										{
											if ($getempOthAdjDedValue['empNo']==$resQryValue["empNo"]) {
												$getempOthAdj=$getempOthAdjDedValue["totAmountD"];
											}
										}
										$getempOthAdj = ($getempOthAdj!=""?$getempOthAdj:0);
										$grandempOthAdj+=$getempOthAdj;
										$subempOthAdj+=$getempOthAdj;
										$totDed +=$getempOthAdj;
										$this->Cell(24,0,number_format($getempOthAdj,2),'','','R');
										$this->Cell(24,0,'','','1','R');
										
										$this->Cell(47,6,'','','','R');
										$this->Cell(24,6,'','','','R');
										$this->Cell(24,6,'','','','R');
										$this->Cell(24,6,'','','','R');
										
										/*UT*/
										foreach ($getempEarningsUT  as $getempEarningsUTValue) {
											if ($getempEarningsUTValue['empNo']==$resQryValue["empNo"]) {
												$rowEarningsUT=$getempEarningsUTValue["trnAmountE"];
											}
										}
										$rowEarningsUT = ($rowEarningsUT!=""?$rowEarningsUT:0);
										$grandempEarningsUT+=$rowEarningsUT;
										$subempEarningsUT+=$rowEarningsUT;
										$lessATardUt += $rowEarningsLWOP+$rowEarningsTARD+$rowEarningsUT;
										
										$grossIncome = $grossIncome + $lessATardUt;
										
										$this->Cell(24,6,number_format($rowEarningsUT,2),'','','R');
										
										$this->Cell(24,6,'','','','R');
										$this->Cell(24,6,'','','','R');
										$this->Cell(24,6,'','','','R');
										$this->Cell(24,6,'','','','R');
										
										/*PHIC*/
										foreach($getempDeductionsPHIC as $getempDeductionsPHICValue)
										{
											if ($getempDeductionsPHICValue['empNo']==$resQryValue["empNo"]) {
													$rowDeductionsPHIC=$getempDeductionsPHICValue["totAmountD"];
											}
										}
										$rowDeductionsPHIC = ($rowDeductionsPHIC!=""?$rowDeductionsPHIC:0);
										$grandempDeductionsPHIC+=$rowDeductionsPHIC;
										$subempDeductionsPHIC+=$rowDeductionsPHIC;
										$totIncome = $grossIncome + $othincome;
										$totDed +=$rowDeductionsPHIC;
										$grandtotDed+=$totDed;
										$subtotDed+=$totDed;
										$netPay = $totIncome-$totDed;
										$grandnetPay+=$netPay;
										$subnetPay+=$netPay;
										 
										$this->Cell(24,6,number_format($rowDeductionsPHIC,2),'','','R');
										$this->Cell(24,6,'','','','R');
										$this->Cell(24,6,number_format($totDed,2),'','','R');
										$this->Cell(24,6,number_format($netPay,2),'','','R');
										
										$totSectYtdGross+=$resQryValue["YtdGross"];
										$totSectBasic+=$rowEarningsSalary;
										$totSectLWOP+=$rowEarningsLWOP;
										$totSectOT+=$rowEarningsOT;
										$totSectAllow+=$rowEarningsAllow;
										$totSectGross+=$rowGrossTaxable;
										$totSectWithTax+=$rowWithTax;
										$totSectSSS+=$rowDeductionsSSS;
										$totSectLoans+=$getempLoans;
										
										$totSectYtdTax+=$resQryValue["YtdTax"];
										$totSectTard+=$rowEarningsTARD;
										$totSectNightDiff+=$rowEarningsND;
										$totSectOthInc+=$rowEarningsOth;
										$totSectGrossInc+=$rowGrossNonTaxable;
										$totSectHdmf+=$rowDeductionsHDMF;
										$totSectOthDed+=$getempOthAdj;
										
										$totSectUt+=$rowEarningsUT;
										$totSectPhic+=$rowDeductionsPHIC;
										$totSectTotDed+=$totDed;
										$totSectNetSal+=$netPay;
										$ctrEmpSect++;
										
										$totDeptYtdGross+=$resQryValue["YtdGross"];
										$totDeptBasic+=$rowEarningsSalary;
										$totDeptLWOP+=$rowEarningsLWOP;
										$totDeptOT+=$rowEarningsOT;
										$totDeptAllow+=$rowEarningsAllow;
										$totDeptGross+=$rowGrossTaxable;
										$totDeptWithTax+=$rowWithTax;
										$totDeptSSS+=$rowDeductionsSSS;
										$totDeptLoans+=$getempLoans;
										
										$totDeptYtdTax+=$resQryValue["YtdTax"];
										$totDeptTard+=$rowEarningsTARD;
										$totDeptNightDiff+=$rowEarningsND;
										$totDeptOthInc+=$rowEarningsOth;
										$totDeptGrossInc+=$rowGrossNonTaxable;
										$totDeptHdmf+=$rowDeductionsHDMF;
										$totDeptOthDed+=$getempOthAdj;
										
										$totDeptUt+=$rowEarningsUT;
										$totDeptPhic+=$rowDeductionsPHIC;
										$totDeptTotDed+=$totDed;
										$totDeptNetSal+=$netPay;
										$ctrEmpDept++;
										
										$totDivYtdGross+=$resQryValue["YtdGross"];
										$totDivBasic+=$rowEarningsSalary;
										$totDivLWOP+=$rowEarningsLWOP;
										$totDivOT+=$rowEarningsOT;
										$totDivAllow+=$rowEarningsAllow;
										$totDivGross+=$rowGrossTaxable;
										$totDivWithTax+=$rowWithTax;
										$totDivSSS+=$rowDeductionsSSS;
										$totDivLoans+=$getempLoans;
										
										$totDivYtdTax+=$resQryValue["YtdTax"];
										$totDivTard+=$rowEarningsTARD;
										$totDivNightDiff+=$rowEarningsND;
										$totDivOthInc+=$rowEarningsOth;
										$totDivGrossInc+=$rowGrossNonTaxable;
										$totDivHdmf+=$rowDeductionsHDMF;
										$totDivOthDed+=$getempOthAdj;
										
										$totDivUt+=$rowEarningsUT;
										$totDivPhic+=$rowDeductionsPHIC;
										$totDivTotDed+=$totDed;
										$totDivNetSal+=$netPay;
										$ctrEmpDiv++;
										$this->Ln(5);
										
										$totGrandTotYtdGross+=$resQryValue["YtdGross"];
										$totGrandTotBasic+=$rowEarningsSalary;
										$totGrandTotLWOP+=$rowEarningsLWOP;
										$totGrandTotOT+=$rowEarningsOT;
										$totGrandTotAllow+=$rowEarningsAllow;
										$totGrandTotGross+=$rowGrossTaxable;
										$totGrandTotWithTax+=$rowWithTax;
										$totGrandTotSSS+=$rowDeductionsSSS;
										$totGrandTotLoans+=$getempLoans;
										
										$totGrandTotYtdTax+=$resQryValue["YtdTax"];
										$totGrandTotTard+=$rowEarningsTARD;
										$totGrandTotNightDiff+=$rowEarningsND;
										$totGrandTotOthInc+=$rowEarningsOth;
										$totGrandTotGrossInc+=$rowGrossNonTaxable;
										$totGrandTotHdmf+=$rowDeductionsHDMF;
										$totGrandTotOthDed+=$getempOthAdj;
										
										$totGrandTotUt+=$rowEarningsUT;
										$totGrandTotPhic+=$rowDeductionsPHIC;
										$totGrandTotTotDed+=$totDed;
										$totGrandTotNetSal+=$netPay;
										$ctrEmpGrandTot++;
									}
									/*End of Display Per Section*/
								}
								/*End of Display Per Department*/
							}
							/*End of Display Division*/
						}
						/*End of Display Employees*/
						$this->SetFont('Courier','B','9'); 
						$this->Cell(47,6,'      SECT. TOTALS ','0','','L');
						$this->Cell(24,6,$ctrEmpSect,'','','R');
						$this->Cell(24,6,number_format($totSectYtdGross,2),'','','R');
						$this->Cell(24,6,number_format($totSectBasic,2),'','','R');
						$this->Cell(24,6,number_format($totSectLWOP,2),'','','R');
						$this->Cell(24,6,number_format($totSectOT,2),'','','R');
						$this->Cell(24,6,number_format($totSectAllow,2),'','','R');
						$this->Cell(24,6,number_format($totSectGross,2),'','','R');
						$this->Cell(24,6,number_format($totSectWithTax,2),'','','R');
						$this->Cell(24,6,number_format($totSectSSS,2),'','','R');
						$this->Cell(24,6,number_format($totSectLoans,2),'','1','R');
						$this->Cell(71,0,'','0','','R');
						$this->Cell(24,0,number_format($totSectYtdTax,2),'','','R');
						$this->Cell(24,0,'','0','','R');
						$this->Cell(24,0,number_format($totSectTard,2),'','','R');
						$this->Cell(24,0,number_format($totSectNightDiff,2),'','','R');
						$this->Cell(24,0,number_format($totSectOthInc,2),'','','R');
						$this->Cell(24,0,number_format($totSectGrossInc,2),'','','R');
						$this->Cell(24,0,'','0','','R');
						$this->Cell(24,0,number_format($totSectHdmf,2),'','','R');
						$this->Cell(24,0,number_format($totSectOthDed,2),'','1','R');
						$this->Cell(119,6,'','0','','R');
						$this->Cell(24,6,number_format($totSectUt,2),'','','R');
						$this->Cell(96,6,'','0','','R');
						$this->Cell(24,6,number_format($totSectPhic,2),'','','R');
						$this->Cell(24,6,'','0','','R');
						$this->Cell(24,6,number_format($totSectTotDed,2),'','','R');
						$this->Cell(24,6,number_format($totSectNetSal,2),'','','R');
						$this->Ln(5);
					}
					/*End of Display Section*/
					
					$this->SetFont('Courier','B','9'); 
					$this->Cell(47,6,'   DEPT. TOTALS ','0','','L');
					$this->Cell(24,6,$ctrEmpDept,'','','R');
					$this->Cell(24,6,number_format($totDeptYtdGross,2),'','','R');
					$this->Cell(24,6,number_format($totDeptBasic,2),'','','R');
					$this->Cell(24,6,number_format($totDeptLWOP,2),'','','R');
					$this->Cell(24,6,number_format($totDeptOT,2),'','','R');
					$this->Cell(24,6,number_format($totDeptAllow,2),'','','R');
					$this->Cell(24,6,number_format($totDeptGross,2),'','','R');
					$this->Cell(24,6,number_format($totDeptWithTax,2),'','','R');
					$this->Cell(24,6,number_format($totDeptSSS,2),'','','R');
					$this->Cell(24,6,number_format($totDeptLoans,2),'','1','R');
					$this->Cell(71,0,'','0','','R');
					$this->Cell(24,0,number_format($totDeptYtdTax,2),'','','R');
					$this->Cell(24,0,'','0','','R');
					$this->Cell(24,0,number_format($totDeptTard,2),'','','R');
					$this->Cell(24,0,number_format($totDeptNightDiff,2),'','','R');
					$this->Cell(24,0,number_format($totDeptOthInc,2),'','','R');
					$this->Cell(24,0,number_format($totDeptGrossInc,2),'','','R');
					$this->Cell(24,0,'','0','','R');
					$this->Cell(24,0,number_format($totDeptHdmf,2),'','','R');
					$this->Cell(24,0,number_format($totDeptOthDed,2),'','1','R');
					$this->Cell(119,6,'','0','','R');
					$this->Cell(24,6,number_format($totDeptUt,2),'','','R');
					$this->Cell(96,6,'','0','','R');
					$this->Cell(24,6,number_format($totDeptPhic,2),'','','R');
					$this->Cell(24,6,'','0','','R');
					$this->Cell(24,6,number_format($totDeptTotDed,2),'','','R');
					$this->Cell(24,6,number_format($totDeptNetSal,2),'','','R');
					$this->Ln(5);
				}
				/*End of Display Department*/
				
				$this->SetFont('Courier','B','9'); 
				$this->Cell(47,6,'DIVISION TOTALS ','0','','L');
				$this->Cell(24,6,$ctrEmpDiv,'','','R');
				$this->Cell(24,6,number_format($totDivYtdGross,2),'','','R');
				$this->Cell(24,6,number_format($totDivBasic,2),'','','R');
				$this->Cell(24,6,number_format($totDivLWOP,2),'','','R');
				$this->Cell(24,6,number_format($totDivOT,2),'','','R');
				$this->Cell(24,6,number_format($totDivAllow,2),'','','R');
				$this->Cell(24,6,number_format($totDivGross,2),'','','R');
				$this->Cell(24,6,number_format($totDivWithTax,2),'','','R');
				$this->Cell(24,6,number_format($totDivSSS,2),'','','R');
				$this->Cell(24,6,number_format($totDivLoans,2),'','1','R');
				$this->Cell(71,0,'','0','','R');
				$this->Cell(24,0,number_format($totDivYtdTax,2),'','','R');
				$this->Cell(24,0,'','0','','R');
				$this->Cell(24,0,number_format($totDivTard,2),'','','R');
				$this->Cell(24,0,number_format($totDivNightDiff,2),'','','R');
				$this->Cell(24,0,number_format($totDivOthInc,2),'','','R');
				$this->Cell(24,0,number_format($totDivGrossInc,2),'','','R');
				$this->Cell(24,0,'','0','','R');
				$this->Cell(24,0,number_format($totDivHdmf,2),'','','R');
				$this->Cell(24,0,number_format($totDivOthDed,2),'','1','R');
				$this->Cell(119,6,'','0','','R');
				$this->Cell(24,6,number_format($totDivUt,2),'','','R');
				$this->Cell(96,6,'','0','','R');
				$this->Cell(24,6,number_format($totDivPhic,2),'','','R');
				$this->Cell(24,6,'','0','','R');
				$this->Cell(24,6,number_format($totDivTotDed,2),'','','R');
				$this->Cell(24,6,number_format($totDivNetSal,2),'','','R');
				$this->Ln(5);
				
				$this->Ln();
			}
			/*End of Display Division*/
			
			/*Display Grand Total*/
			$this->SetFont('Courier','B','9'); 
			$this->Cell(47,6,'GRAND TOTAL ','0','','L');
			$this->Cell(24,6,$ctrEmpGrandTot,'','','R');
			$this->Cell(24,6,number_format($totGrandTotYtdGross,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotBasic,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotLWOP,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotOT,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotAllow,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotGross,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotWithTax,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotSSS,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotLoans,2),'','1','R');
			$this->Cell(71,0,'','0','','R');
			$this->Cell(24,0,number_format($totGrandTotYtdTax,2),'','','R');
			$this->Cell(24,0,'','0','','R');
			$this->Cell(24,0,number_format($totGrandTotTard,2),'','','R');
			$this->Cell(24,0,number_format($totGrandTotNightDiff,2),'','','R');
			$this->Cell(24,0,number_format($totGrandTotOthInc,2),'','','R');
			$this->Cell(24,0,number_format($totGrandTotGrossInc,2),'','','R');
			$this->Cell(24,0,'','0','','R');
			$this->Cell(24,0,number_format($totGrandTotHdmf,2),'','','R');
			$this->Cell(24,0,number_format($totGrandTotOthDed,2),'','1','R');
			$this->Cell(119,6,'','0','','R');
			$this->Cell(24,6,number_format($totGrandTotUt,2),'','','R');
			$this->Cell(96,6,'','0','','R');
			$this->Cell(24,6,number_format($totGrandTotPhic,2),'','','R');
			$this->Cell(24,6,'','0','','R');
			$this->Cell(24,6,number_format($totGrandTotTotDed,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotNetSal,2),'','','R');
			$this->SetFont('Courier','','9'); 
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
	
	$topType			= 	$_GET['topType'];
	
	$pdf->reportType	= $_GET["reportType"];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	if ($empNo>"") {$empNo1 = " AND (tblPaySum.empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (tblPaySum.empDivCode = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (tblPaySum.empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (tblPaySum.empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	
	if ($empNo>"") {$empNo2 = " AND (tblEarn.empNo LIKE '{$empNo}%')"; } else {$empNo2 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv2 = " AND (empDiv= '{$empDiv}')"; } else {$empDiv2 = "";}
	if ($empDept>"" && $empDept>0) {$empDept2 = " AND (empDepCode = '{$empDept}')"; } else {$empDept2 = "";}
	if ($empSect>"" && $empSect>0) {$empSect2 = " AND (empSecCode = '{$empSect}')"; } else {$empSect2 = "";}
	
	$pdf->where_empmast = $empNoRep.$empDiv1.$empDept1.$empSect1;
	
	
	
	if($topType=='B')
	{
		$RptAllowance=1;
		$RptRegular=1;
	}
	else
	{
		if($topType=='A')
			$RptAllowance=1;
		else
			$RptRegular=1;
	}
	
	if($reportType == 0)
	{
		$tbl = "tblPayrollSummary";
		$tblytd = "tblYtdData";
		$tblEarn = "tblEarnings";
	}
	else
	{
		$tbl = "tblPayrollSummaryHist";
		$tblytd = "tblYtdDataHist";
		$tblEarn = "tblEarningsHist";
	}
	
	$pdf->tbl  	=  $tblEarn;	
	
	if($RptRegular==1)
	{
		$pdf->fPrint = 0;
		
		$qryPayReg 	= "Select 
					tblPaySum.compCode,tblPaySum.empNo,
					tblPaySum.netSalary,tblPaySum.taxWitheld,
					tblPaySum.empDivCode,tblPaySum.empDepCode,tblPaySum.empSecCode,
					tblPaySum.pdYear, tblPaySum.pdNumber,
					tblEmp.empLastName,tblEmp.empFirstName, 
					tblEmp.empMidName,tblEmp.empTeu,
					teuAmt,
					YtdGross,YtdTax
					from $tbl tblPaySum
					left join tblEmpMast tblEmp on tblPaySum.empNo=tblEmp.empNo
					left join tblTeu teu on tblEmp.empTeu=teu.teuCode
					left join $tblytd tblytd on tblytd.empNo=tblPaySum.empNo
					where tblPaySum.compCode='".$_SESSION["company_code"]."'
					AND tblPaySum.payGrp = '".$_SESSION["pay_group"]."'
					AND tblPaySum.payCat= '".$_SESSION["pay_category"]."'
					AND tblPaySum.pdYear = '".$pdf->pdYear."'
					AND tblPaySum.pdNumber = '".$pdf->pdNum	."'
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
			$pdf->displayContent($arrPaySum,$reportType,$pdf->pdYear,$pdf->pdNum,$empDiv,$empDept,$empSect);
		}
	}
	
	if($RptAllowance==1)
	{
		$pdf->fPrint = 1;
		
		$qryEarn = "Select tblEarn.compCode, 
						tblEarn.trnCode,trnDesc,empmast.empNo, empLastName,
						empFirstName,empMidName,trnAmountE
						from $tblEarn tblEarn, tblEmpMast empmast, tblPayTransType ptTrans 
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
						$empNo2 $empName2 $empDiv2 $empName2 $empDept2 $empSect2
						order by trnDesc";
		$resEarn = $inqTSObj->execQry($qryEarn);
		$arrSumEarn = $inqTSObj->getArrRes($resEarn);
		if(count($arrSumEarn)>0)
		{
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displayContentDetails($arrSumEarn);
		}
	}
	
	
$pdf->Output();
?>
