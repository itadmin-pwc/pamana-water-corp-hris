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
			
			
			$this->SetFont('Courier','','10'); 
			$this->Cell(80,5,"Run Date: " . $newdate,"0");
			$this->Cell(170,5,$this->compName,"0",'0','C');
			$this->Cell(85,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			
			if($this->fPrint==0)
			{
				$this->Cell(80,5,"Report ID: PAYREG01");
				$hTitle = "Regular";
				$hTitle = $hTitle." Payroll Register for the Period of ".$this->pdHeadTitle;
				$this->Cell(170,5,$hTitle,'0','0','C');
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
				$this->Cell(24,1,'LEGAL PAY','','','R');
				$this->Cell(24,1,'TARDINESS','','','R');
				$this->Cell(24,1,'NIGHT PREM','','','R');
				$this->Cell(24,1,'OTH. INC','','','R');
				$this->Cell(24,1,'GROSS','','','R');
				$this->Cell(24,1,'WTAX ADJ','','','R');
				$this->Cell(24,1,'HDMF','','','R');
				$this->Cell(24,1,'OTHER DED','','','R');
				$this->Cell(24,1,'  ','','','R');
				$this->Cell(24,1,'SALARY','','','R');
				$this->Ln();
				
				$this->Cell(47,6,'  ','','','C');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'YTD - GOV','','','C');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'UT','','','R');
				$this->Cell(24,6,'  ','','','C');
				$this->Cell(24,6,'13TH MTNH','','','C');
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
				$this->SetFont('Courier','B','10');
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
							where trnCode in (Select trnCode from tblAllowType where 
							compCode='{$_SESSION['company_code']}' and allowTypeStat='A' and 
							(sprtPS='N' or sprtPS is null or sprtPS='')
							and trnCode in (Select trnCode from tblPayTransType
							where compCode='".$_SESSION["company_code"]."' and trnCat='E'
							and trnStat='A' and trnRecode='".EARNINGS_RECODEALLOW."' )) and compCode='{$_SESSION['company_code']}'
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
							where trnCode in (Select trnCode from tblPayTransType where compCode='{$_SESSION['company_code']}' and trnCat='E' and trnStat='A' and trnEntry='Y' and trnCode not in (Select trnCode from tblAllowType where compCode='".$_SESSION["company_code"]."') )
							and compCode='{$_SESSION['company_code']}'
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."'
							group by empNo
							";
		
			$resEarningsOth = $this->execQry($qryEarningsOth);
			$resEarningsOth = $this->getArrRes($resEarningsOth);
			return $resEarningsOth;
		}
		function getData13thMonth($trnRec,$pdYear,$pdNumber,$reportType)
		{
			$tbltable = ($reportType==1?"tblEarningsHist":"tblEarnings");
			 $qryEarningsOth = "Select sum(trnAmountE) as totAmountE,empNo from ".$tbltable."
							where trnCode in (Select trnCode from tblPayTransType where compCode='{$_SESSION['company_code']}' and trnCat='E' and trnStat='A' and trnRecode='$trnRec')
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
							where trnCode<>'8024' and trnCode in (Select trnCode from tblPayTransType 
							where trnCode not in (Select trnCode from tblLoanType where compCode='{$_SESSION['company_code']}') and trnCat='D' and trnStat='A' and trnEntry='Y')
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' 
							group by empNo
							";
			$resOthAdjDeductions = $this->execQry($qryOthAdjDeductions);
			$resOthAdjDeductions = $this->getArrRes($resOthAdjDeductions);
			return $resOthAdjDeductions;
		}
		function getWTaxAndTaxAdj($pdYear,$pdNumber,$reportType)
		{
			$tbltable = ($reportType==1?"tblDeductionsHist":"tblDeductions");
			$qryWTaxAndTaxAdj = "Select trnAmountD,trnCode,empNo from ".$tbltable." 
							where trnCode IN ('8024','5100') 
							and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' 
							";
			$resWTaxAndTaxAdj = $this->execQry($qryWTaxAndTaxAdj);
			$resWTaxAndTaxAdj = $this->getArrRes($resWTaxAndTaxAdj);
			return $resWTaxAndTaxAdj;
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
								where  (compCode = '{$_SESSION['company_code']}') AND (trnCat = 'E') AND (trnTaxCd='N' or trnTaxCd is null or trnTaxCd='') 
								AND (trnStat = 'A')) and (sprtPS='N' or sprtPS is null or sprtPS='') AND pdNumber='".$pdNum."' and pdYear='".$pdYear."' 
								group by empNo";
			
			$rescompGrossNonTax = $this->execQry($qrycompGrossNonTax);
			$rescompGrossNonTax = $this->getArrRes($rescompGrossNonTax);
			return $rescompGrossNonTax;
		}
	
		function countBranch($pdYear,$pdNum,$reportType)
		{
			$tbltable = ($reportType=='1'?"tblPayrollSummaryHist":"tblPayrollSummary");
			
			$qryBranch = "Select * from tblBranch
							where compCode='2' and brnStat='A'
							and brnCode in (Select distinct(empbrnCode) from ".$tbltable." as tblPaySum where compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."' 
							and payCat='".$_SESSION["pay_category"]."' and pdYear='".$pdYear."' and pdNumber='".$pdNum."' ".$this->where_paysum.")
							order by brnDesc";
			
			$resBranch = $this->execQry($qryBranch);
			return $this->getRecCount($resBranch);
		}
	
		function dispBranch($pdYear,$pdNum,$reportType)
		{
			$tbltable = ($reportType==1?"tblPayrollSummaryHist":"tblPayrollSummary");
			
			$qryBranch = "Select * from tblBranch
							where compCode='".$_SESSION["company_code"]."' and brnStat='A'
							and brnCode in (Select distinct(empbrnCode) from ".$tbltable." as tblPaySum where compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."' 
							and payCat='".$_SESSION["pay_category"]."' and pdYear='".$pdYear."' and pdNumber='".$pdNum."' ".$this->where_paysum.")
							order by brnDesc";
			
			$resBranch = $this->execQry($qryBranch);
			$resBranch = $this->getArrRes($resBranch);
			return $resBranch;
		}
	
		function dispLocation($pdYear,$pdNum,$reportType, $brnchCd)
		{
			$tbltable = ($reportType==1?"tblPayrollSummaryHist":"tblPayrollSummary");
			
			$qryLoc = "Select * from tblBranch
							where compCode='".$_SESSION["company_code"]."' and brnStat='A'
							and brnCode in (Select distinct(empLocCode) from ".$tbltable." as tblPaySum where compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."' 
							and payCat='".$_SESSION["pay_category"]."' and pdYear='".$pdYear."' and empBrnCode='".$brnchCd."' and pdNumber='".$pdNum."' ".$this->where_paysum.")
							order by brnDesc ";
			
			$resLoc = $this->execQry($qryLoc);
			$resLoc = $this->getArrRes($resLoc);
			return $resLoc;
		}
	
		
		function dispDivision($pdYear,$pdNum,$divCode,$reportType, $locCode, $brnCode)
		{
			$tbltable = ($reportType==1?"tblPayrollSummaryHist":"tblPayrollSummary");
			if($divCode!=0)
				$con = "and empDivCode='".$divCode."'";
				
			$qryDivision = "Select divCode,deptDesc from tblDepartment
							where compCode='".$_SESSION["company_code"]."' and deptLevel='1'
							and deptStat='A'
							and divCode in 
							(Select distinct(empDivCode) from ".$tbltable." where compCode='".$_SESSION["company_code"]."'
							and payGrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."' 
							and pdYear='".$pdYear."' and pdNumber='".$pdNum."' and empLocCode='".$locCode."' and empBrnCode='".$brnCode."' $con)
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
		
		function getEmpYtdDataHist($pdYear, $pdNum)
		{
			$qryEmpYtdDataHist = "Select * from tblYtdDataHist
									where compCode='".$_SESSION["company_code"]."' and pdYear='".$pdYear."'
									and payGrp='".$_SESSION["pay_group"]."'
									";
			$resEmpYtdDataHist = $this->getArrRes($this->execQry($qryEmpYtdDataHist));
			return $resEmpYtdDataHist;						
		}
		
		function displayEarnContent()
		{
			
			if ($_SESSION['pay_category'] !=9) {
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
									".($this->where_empmast2!=""?$this->where_empmast2:"")."
									)
								)";
			} else {
				$where_trnCode = "trnCode in 
								(Select distinct(trnCode) from ".$this->tbl." where
								compCode='".$_SESSION["company_code"]."' 
								and pdYear='".$this->pdYear."' 
								and (sprtPS='Y')
								and pdNumber='".$this->pdNum."' and empNo in 
									(Select empNo from tblEmpMast 
									where compCode='".$_SESSION["company_code"]."'
									AND empPayCat='".$_SESSION["pay_category"]."' AND empPayGrp='".$_SESSION["pay_group"]."'
									".($this->where_empmast2!=""?$this->where_empmast2:"")."
									)
								)";
			
			}
					
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
				$this->SetFont('Courier','','10'); 
				$arrEarnType=$this->displayEarnContent();
				$grandsumamt = 0;
				foreach ($arrEarnType as $arrEarnTypeValue) 
				{
					$this->SetFont('Courier','B','10');
					$this->Cell(60,6,strtoupper($arrEarnTypeValue['trnDesc']),0,'','L');
					$this->Ln();
					$this->SetFont('Courier','','10'); 
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
					$this->SetFont('Courier','B','10');
						$this->Cell(82,6,'SUB - TOTAL',0,'','L');
						$this->Cell(25,6,number_format($subTotal,2),0,'','R');
						$grandsumamt+=$subTotal;
					$this->SetFont('Courier','','10'); 
					$this->Ln();
				}
				
				$this->Cell(60,6,'',0); 
				$this->SetFont('Courier','B','10');
					$this->Cell(82,6,'GRAND TOTAL',0,'','L');
					$this->Cell(25,6,number_format($grandsumamt,2),0,'','R');
				$this->SetFont('Courier','','10'); 
				$this->Ln();
				$this->Cell(335,6,'* * * End of Report * * *','0','','C');
			}
		
		function displayContent($resQry,$reportType,$pdYear,$pdNum,$divCode,$empDept,$empSec)
		{
			$this->SetFont('Courier','','10'); 
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
			$totemp13th = 0;
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
			$grandemp13th=0;
			$grandGrossNonTaxable=0;
			$grandDeductionsHDMF=0;
			$grandempOthAdj=0;
			$grandempEarningsUT=0;
			$grandempDeductionsPHIC=0;
			$grandtotDed = 0;
			$grandnetPay = 0;
			$totGrandYtdGovDed = 0;
		
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
			$subemp13th=0;
			$subGrossNonTaxable=0;
			$subDeductionsHDMF=0;
			$subempOthAdj=0;
			$subempEarningsUT=0;
			$subempDeductionsPHIC=0;
			$subtotDed = 0;
			$subnetPay = 0;
			
			$getempYtdDataHist = $this->getEmpYtdDataHist($pdYear, $pdNum);
			$getempEarningsSalary = $this->getDatatblEarnings(EARNINGS_BASIC,$pdYear,$pdNum,$reportType);
			$getempEarningsLWOP = $this->getDatatblEarnings(EARNINGS_ABS,$pdYear,$pdNum,$reportType);
			$getempEarningsUT = $this->getDatatblEarnings(EARNINGS_UT,$pdYear,$pdNum,$reportType);
			$getempEarningsTARD = $this->getDatatblEarnings(EARNINGS_TARD,$pdYear,$pdNum,$reportType);
			$getempEarningsOT = $this->getDatatblEarningsOTND(EARNINGS_OT,$pdYear,$pdNum,$reportType);
			$getempEarningsND = $this->getDatatblEarningsOTND(EARNINGS_ND,$pdYear,$pdNum,$reportType);
			$getempEarningsAllow = $this->getDatatblEarningsAllow('',$pdYear,$pdNum,$reportType);
			$getempEarningsOth = $this->getDataOthtblEarnings('',$pdYear,$pdNum,$reportType);
			$getemp13th = $this->getData13thMonth('1000',$pdYear,$pdNum,$reportType);
			$getempDeductionsSSS = $this->getDataOthtblDeductions(SSS_CONTRIB,$pdYear,$pdNum,$reportType);
			$getempDeductionsHDMF = $this->getDataOthtblDeductions(PAGIBIG_CONTRIB,$pdYear,$pdNum,$reportType);
			$getempDeductionsPHIC = $this->getDataOthtblDeductions(PHILHEALTH_CONTRIB,$pdYear,$pdNum,$reportType);
			$getempListLoans = $this->getDataLoanstblDeductions('',$pdYear,$pdNum,$reportType);
			$getempOthAdjDed = $this->getDataOthAdjtblDeductions('',$pdYear,$pdNum,$reportType);
			$getempWTaxAndTaxAdj = $this->getWTaxAndTaxAdj($pdYear,$pdNum,$reportType);
			$getempGrossTaxable = $this->compGrossTaxable($reportType,$pdNum,$pdYear);
			$getempGrossNonTax = $this->compGrossNonTaxable($reportType,$pdNum,$pdYear);
			$getempHolidayPay = $this->getDatatblEarnings(EARNINGS_LEGALPAY,$pdYear,$pdNum,$reportType);
			
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
			$totGrandLegpay=0;
			$totGrandTotTard=0;
			$totGrandTotNightDiff=0;
			$totGrandTotOthInc=0;
			$totGrandTot13th=0;
			$totGrandTotGrossInc=0;
			$totGrandTotHdmf=0;
			$totGrandTotOthDed=0;
			$totGrandTotUt=0;
			$totGrandTotPhic=0;
			$totGrandTotTotDed=0;
			$totGrandTotNetSal=0;
			$totGrandYtdGovDed=0;
			
			/*Count No. of Branches*/
			$cntBranch = $this->countBranch($pdYear,$pdNum,$reportType);
			
			/*Display Per Branch*/
			$arrBrnch = $this->dispBranch($pdYear,$pdNum,$reportType);
			$noOfBrnchDisp = 1;
			foreach($arrBrnch as $arrBrnch_val)
			{
				$ctrEmpBrnch=0;
				$totBrnchYtdGross = 0;
				$totBrnchLWOP=0;
				$totBrnchOT=0;
				$totBrnchAllow=0;
				$totBrnchGross=0;
				$totBrnchWithTax=0;
				$totBrnchSSS=0;
				$totBrnchLoans=0;
				$totBrnchYtdTax=0;
				$totBrnchLegpay =0;
				$totBrnchTard=0;
				$totBrnchNightDiff=0;
				$totBrnchOthInc=0;
				$totBrnch13th=0;
				$totBrnchGrossInc=0;
				$totBrnchHdmf=0;
				$totBrnchOthDed=0;
				$totBrnchUt=0;
				$totBrnchPhic=0;
				$totBrnchTotDed=0;
				$totBrnchNetSal=0;
				$this->SetFont('Courier','B','10'); 
				$this->Cell(47,6,"BRANCH = ".strtoupper($arrBrnch_val["brnDesc"]),0,'','L');
				$this->Ln(7);
				
				/*Display Per Location*/
				$arrLoc = $this->dispLocation($pdYear,$pdNum,$reportType,$arrBrnch_val["brnCode"]);
				foreach($arrLoc as $arrLoc_val)
				{
					$ctrEmpLoc=0;
					$totLocYtdGross = 0;
					$totLocLWOP=0;
					$totLocOT=0;
					$totLocAllow=0;
					$totLocGross=0;
					$totLocWithTax=0;
					$totLocSSS=0;
					$totLocLoans=0;
					$totLocYtdTax=0;
					$totLocLegpay = 0;
					$totLocTard=0;
					$totLocNightDiff=0;
					$totLocOthInc=0;
					$totLoc13th=0;
					$totLocGrossInc=0;
					$totLocHdmf=0;
					$totLocOthDed=0;
					$totLocUt=0;
					$totLocPhic=0;
					$totLocTotDed=0;
					$totLocNetSal=0;
					
					$this->SetFont('Courier','B','10'); 
					$this->Cell(8,6,"",0,'','L');
					$this->Cell(47,6,"LOCATION = ".strtoupper($arrLoc_val["brnDesc"]),0,'','L');
					$this->Ln(7);
					
					/*Display Division*/
					$arrDiv = $this->dispDivision($pdYear,$pdNum,$divCode,$reportType, $arrLoc_val["brnCode"], $arrBrnch_val["brnCode"]);
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
						$totDivLegpay=0;
						$totDivTard=0;
						$totDivNightDiff=0;
						$totDivOthInc=0;
						$totDiv13th=0;
						$totDivGrossInc=0;
						$totDivHdmf=0;
						$totDivOthDed=0;
						$totDivUt=0;
						$totDivPhic=0;
						$totDivTotDed=0;
						$totDivNetSal=0;
						$this->SetFont('Courier','B','10'); 
						$this->Cell(16,6,"",0,'','L');
						$this->Cell(47,6,"DIVISION = ".strtoupper($arrDiv_val["deptDesc"]),0,'','L');
						$this->Ln(7);
					
							/*Display Employees*/
							foreach($resQry as $resQryValue)
							{
								/*Display Per Branch*/
								if($arrBrnch_val["brnCode"]==$resQryValue["empBrnCode"])
								{
									/*Display Per Location*/
									if($arrLoc_val["brnCode"]==$resQryValue["empLocCode"])
									{
										
										/*Display Per Division*/
										if($arrDiv_val["divCode"]==$resQryValue["empDivCode"])
										{
											
											$this->SetFont('Courier','','10'); 
											$this->Cell(47,6,$resQryValue["empLastName"].", ".$resQryValue["empFirstName"][0].".".$resQryValue["empMidName"][0].".",'','','L');
											$this->Cell(24,6,$resQryValue["empTeu"],'','','R');
											
											
											/*YTD Gross*/
											foreach ($getempYtdDataHist  as $getempYtdDataHistValue) {
												if ($getempYtdDataHistValue['empNo']==$resQryValue["empNo"]) {
													$rowEmpYtdGross=$getempYtdDataHistValue["YtdTaxable"];
												}
											}
											
											
											if($reportType=='0')
												$rowEmpYtdGross =  $rowEmpYtdGross + $resQryValue["YtdGross"];
											else
												$rowEmpYtdGross = $rowEmpYtdGross;
											
												
											$this->Cell(24,6,number_format($rowEmpYtdGross,2),'','','R');
											
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
											$rowWithTax = $rowWithTaxAdj = 0;
											foreach($getempWTaxAndTaxAdj as $valWTaxAndTaxAdj) {
												if ($valWTaxAndTaxAdj['empNo']==$resQryValue["empNo"]) {
													if ($valWTaxAndTaxAdj['trnCode']==5100) {
														$rowWithTax= (float)$valWTaxAndTaxAdj['trnAmountD'];
													}	
													if ($valWTaxAndTaxAdj['trnCode']==8024) {
														$rowWithTaxAdj= (float)$valWTaxAndTaxAdj['trnAmountD'];
													}	
												}
											}
											
											
											$grandempWithTax+=$rowWithTax + $rowWithTaxAdj;
											$subempWithTax+=$rowWithTax + $rowWithTaxAdj;
											$totDed =$rowWithTax + $rowWithTaxAdj;
											
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
											
											/*YTD Tax*/
											foreach ($getempYtdDataHist  as $getempYtdDataHistValue) {
												if ($getempYtdDataHistValue['empNo']==$resQryValue["empNo"]) {
													$rowEmpYtdTax=$getempYtdDataHistValue["YtdTax"];
												}
											}
											
											if($reportType=='0')
												$rowEmpYtdTax =  $rowEmpYtdTax + $resQryValue["YtdTax"];
											else
												$rowEmpYtdTax =  $rowEmpYtdTax;
												
												
											$this->Cell(24,0,number_format($rowEmpYtdTax,2),'','','R');
											
											/*Legal Pay*/
											foreach($getempHolidayPay as $getempHolidayPay_val)
											{
												if ($getempHolidayPay_val['empNo']==$resQryValue["empNo"]) {
													$rowEmpHol=$getempHolidayPay_val["trnAmountE"];
												}
											}
											$this->Cell(24,0,number_format($rowEmpHol,2),'','','R');
											
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

											$this->Cell(24,0,number_format($rowWithTaxAdj,2),'','','R');
											
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
											
											/*YTD Gov*/
											foreach ($getempYtdDataHist  as $getempYtdDataHistValue) {
												if ($getempYtdDataHistValue['empNo']==$resQryValue["empNo"]) {
													$rowEmpYtdGovDed=$getempYtdDataHistValue["YtdGovDed"];
												}
											}
											
											if($reportType=='0')
												$rowEmpYtdGovDed =  $rowEmpYtdGovDed + $resQryValue["YtdGovDed"];
											else
												$rowEmpYtdGovDed =  $rowEmpYtdGovDed ;
												
											$this->Cell(24,6,number_format($rowEmpYtdGovDed,2),'','','R');
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
											
											/*13th Month*/
											foreach ($getemp13th  as $getemp13thValue) {
												if ($getemp13thValue['empNo']==$resQryValue["empNo"]) {
													$row13th=$getemp13thValue["totAmountE"];
												}
											}
											$row13th = ($row13th!=""?$row13th:0);
											$grandemp13th+=$row13th;
											$subemp13th+=$row13th;
											
											//$grossIncome = $grossIncome + $row13th;
											
											$this->Cell(24,6,'','','','R');
											$this->Cell(24,6,number_format($row13th,2),'','','R');
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
											
											$totIncome = $rowGrossTaxable + $rowGrossNonTaxable;
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
											$this->Ln(5);
											$totBrnchYtdGross+=$rowEmpYtdGross;
											$totBrnchBasic+=$rowEarningsSalary;
											$totBrnchLWOP+=$rowEarningsLWOP;
											$totBrnchOT+=$rowEarningsOT;
											$totBrnchAllow+=$rowEarningsAllow;
											$totBrnchGross+=$rowGrossTaxable;
											$totBrnchWithTax+=$rowWithTax+ $rowWithTaxAdj;
											$totBrnchSSS+=$rowDeductionsSSS;
											$totBrnchLoans+=$getempLoans;
											
											$totBrnchYtdTax+=$rowEmpYtdTax;
											$totBrnchLegpay+=$rowEmpHol;
											$totBrnchTard+=$rowEarningsTARD;
											$totBrnchNightDiff+=$rowEarningsND;
											$totBrnchOthInc+=$rowEarningsOth;
											$totBrnch13th+=$row13th;
											$totBrnchGrossInc+=$rowGrossNonTaxable;
											$totBrnchHdmf+=$rowDeductionsHDMF;
											$totBrnchOthDed+=$getempOthAdj;
											
											$totBrnchYtdGovDed+=$rowEmpYtdGovDed;
											$totBrnchUt+=$rowEarningsUT;
											$totBrnchPhic+=$rowDeductionsPHIC;
											$totBrnchTotDed+=$totDed;
											$totBrnchNetSal+=$netPay;
											$ctrEmpBrnch++;
											
											$totLocYtdGross+=$rowEmpYtdGross;
											$totLocBasic+=$rowEarningsSalary;
											$totLocLWOP+=$rowEarningsLWOP;
											$totLocOT+=$rowEarningsOT;
											$totLocAllow+=$rowEarningsAllow;
											$totLocGross+=$rowGrossTaxable;
											$totLocWithTax+=$rowWithTax + $rowWithTaxAdj;
											$totLocSSS+=$rowDeductionsSSS;
											$totLocLoans+=$getempLoans;
											
											$totLocYtdTax+=$rowEmpYtdTax;
											$totLocLegpay+=$rowEmpHol;
											$totLocTard+=$rowEarningsTARD;
											$totLocNightDiff+=$rowEarningsND;
											$totLocOthInc+=$rowEarningsOth;
											$totLoc13th+=$row13th;
											$totLocGrossInc+=$rowGrossNonTaxable;
											$totLocHdmf+=$rowDeductionsHDMF;
											$totLocOthDed+=$getempOthAdj;
											
											$totLocYtdGovDed+=$rowEmpYtdGovDed;
											$totLocUt+=$rowEarningsUT;
											$totLocPhic+=$rowDeductionsPHIC;
											$totLocTotDed+=$totDed;
											$totLocNetSal+=$netPay;
											$ctrEmpLoc++;
											
											$totDivYtdGross+=$rowEmpYtdGross;
											$totDivBasic+=$rowEarningsSalary;
											$totDivLWOP+=$rowEarningsLWOP;
											$totDivOT+=$rowEarningsOT;
											$totDivAllow+=$rowEarningsAllow;
											$totDivGross+=$rowGrossTaxable;
											$totDivWithTax+=$rowWithTax + $rowWithTaxAdj;
											$totDivSSS+=$rowDeductionsSSS;
											$totDivLoans+=$getempLoans;
											
											$totDivYtdTax+=$rowEmpYtdTax;
											$totDivLegpay+=$rowEmpHol;
											$totDivTard+=$rowEarningsTARD;
											$totDivNightDiff+=$rowEarningsND;
											$totDivOthInc+=$rowEarningsOth;
											$totDiv13th+=$row13th;
											$totDivGrossInc+=$rowGrossNonTaxable;
											$totDivHdmf+=$rowDeductionsHDMF;
											$totDivOthDed+=$getempOthAdj;
											
											$totDivYtdGovDed+=$rowEmpYtdGovDed;
											$totDivUt+=$rowEarningsUT;
											$totDivPhic+=$rowDeductionsPHIC;
											$totDivTotDed+=$totDed;
											$totDivNetSal+=$netPay;
											$ctrEmpDiv++;
											$this->Ln(5);
											
											$totGrandTotYtdGross+=$rowEmpYtdGross;
											$totGrandTotBasic+=$rowEarningsSalary;
											$totGrandTotLWOP+=$rowEarningsLWOP;
											$totGrandTotOT+=$rowEarningsOT;
											$totGrandTotAllow+=$rowEarningsAllow;
											$totGrandTotGross+=$rowGrossTaxable;
											$totGrandTotWithTax+=$rowWithTax + $rowWithTaxAdj;
											$totGrandTotSSS+=$rowDeductionsSSS;
											$totGrandTotLoans+=$getempLoans;
											
											$totGrandTotYtdTax+=$rowEmpYtdTax;
											$totGrandLegpay+=$rowEmpHol;
											$totGrandTotTard+=$rowEarningsTARD;
											$totGrandTotNightDiff+=$rowEarningsND;
											$totGrandTotOthInc+=$rowEarningsOth;
											$totGrandTot13th+=$row13th;
											$totGrandTotGrossInc+=$rowGrossNonTaxable;
											$totGrandTotHdmf+=$rowDeductionsHDMF;
											$totGrandTotOthDed+=$getempOthAdj;
											
											$totGrandYtdGovDed+=$rowEmpYtdGovDed;
											$totGrandTotUt+=$rowEarningsUT;
											$totGrandTotPhic+=$rowDeductionsPHIC;
											$totGrandTotTotDed+=$totDed;
											$totGrandTotNetSal+=$netPay;
											$ctrEmpGrandTot++;
										}
										/*End of Display Division*/
									}
									/*End of Display Per Location*/
								
								}
								/*End of Display Per Branch*/
								unset($testemp,
										$totDed,
										$netPay,$grossIncome,$lessATardUt,$othincome,$totIncome,$rowEarningsAllow,
										$totDed,$netPay,$rowEarningsOth,
										$rowEarningsSalary,$rowEarningsLWOP,$rowEarningsTARD,$rowEarningsUT,$rowEarningsOT,$rowEarningsND,
										$rowEarningsAllow,$rowEarningsOth,$rowGrossTaxable,$rowGrossNonTaxable,$rowDeductionsSSS,$rowDeductionsHDMF,
										$rowDeductionsPHIC,$getempLoans,$getempOthAdj,$empTeuAmt,$rowEmpYtdGross,$rowEmpYtdTax,$row13th,$rowEmpHol,$rowEmpYtdGovDed);
							}
							/*End of Display Per Employee*/
					
						$this->SetFont('Courier','B','10'); 
						$this->Cell(16,6,"",0,'','L');
						$this->Cell(31,6,'DIVISION TOTALS ','0','','L');
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
						
						$this->Cell(24,0,number_format($totDivLegpay,2),'','','R');
						$this->Cell(24,0,number_format($totDivTard,2),'','','R');
						$this->Cell(24,0,number_format($totDivNightDiff,2),'','','R');
						$this->Cell(24,0,number_format($totDivOthInc,2),'','','R');
						$this->Cell(24,0,number_format($totDivGrossInc,2),'','','R');
						$this->Cell(24,0,'','0','','R');
						$this->Cell(24,0,number_format($totDivHdmf,2),'','','R');
						$this->Cell(24,0,number_format($totDivOthDed,2),'','1','R');
						$this->Cell(71,6,'','0','0','R');
						$this->Cell(24,6,number_format($totDivYtdGovDed,2),'0','','R');
						$this->Cell(24,6,'','0','','R');
						$this->Cell(24,6,number_format($totDivUt,2),'','','R');
						$this->Cell(24,6,'','0','','R');
						$this->Cell(24,6,number_format($totDiv13th,2),'','','R');
						$this->Cell(48,6,'','0','','R');
						$this->Cell(24,6,number_format($totDivPhic,2),'','','R');
						$this->Cell(24,6,'','0','','R');
						$this->Cell(24,6,number_format($totDivTotDed,2),'','','R');
						$this->Cell(24,6,number_format($totDivNetSal,2),'','','R');
						$this->Ln(5);
						
						unset($totDivYtdGross,$totDivBasic,$totDivLWOP,$totDivOT,$totDivAllow,$totDivGross,$totDivWithTax,$totDivSSS,$totDivLoans,$totDivYtdTax,
					 	 $totDivTard,$totDivNightDiff,$totDivOthInc,$totDivGrossInc,$totDivHdmf,$totDivOthDed,$totDivUt,$totDivPhic,$totDivTotDed,$totDivNetSal ,$totDiv13th,$totDivLegpay,$totDivYtdGovDed);
				
					}
					/*End of Display Division*/
					$this->SetFont('Courier','B','10'); 
					$this->Cell(8,6,"",0,'','L');
					$this->Cell(43,6,'LOCATION TOTALS ','0','','L');
					$this->Cell(20,6,$ctrEmpLoc,'','','R');
					$this->Cell(24,6,number_format($totLocYtdGross,2),'','','R');
					$this->Cell(24,6,number_format($totLocBasic,2),'','','R');
					$this->Cell(24,6,number_format($totLocLWOP,2),'','','R');
					$this->Cell(24,6,number_format($totLocOT,2),'','','R');
					$this->Cell(24,6,number_format($totLocAllow,2),'','','R');
					$this->Cell(24,6,number_format($totLocGross,2),'','','R');
					$this->Cell(24,6,number_format($totLocWithTax,2),'','','R');
					$this->Cell(24,6,number_format($totLocSSS,2),'','','R');
					$this->Cell(24,6,number_format($totLocLoans,2),'','1','R');
					$this->Cell(71,0,'','0','','R');
					$this->Cell(24,0,number_format($totLocYtdTax,2),'','','R');
					$this->Cell(24,0,number_format($totLocLegpay,2),'','','R');
					$this->Cell(24,0,number_format($totLocTard,2),'','','R');
					$this->Cell(24,0,number_format($totLocNightDiff,2),'','','R');
					$this->Cell(24,0,number_format($totLocOthInc,2),'','','R');
					$this->Cell(24,0,number_format($totLocGrossInc,2),'','','R');
					$this->Cell(24,0,'','0','','R');
					$this->Cell(24,0,number_format($totLocHdmf,2),'','','R');
					$this->Cell(24,0,number_format($totLocOthDed,2),'','1','R');
					$this->Cell(71,6,'','0','','R');
					$this->Cell(24,6,number_format($totLocYtdGovDed,2),'','','R');
					$this->Cell(24,6,'','0','','R');
					$this->Cell(24,6,number_format($totLocUt,2),'','','R');
					$this->Cell(24,6,'','0','','R');
					$this->Cell(24,6,number_format($totLoc13th,2),'','','R');
					$this->Cell(48,6,'','0','','R');
					$this->Cell(24,6,number_format($totLocPhic,2),'','','R');
					$this->Cell(24,6,'','0','','R');
					$this->Cell(24,6,number_format($totLocTotDed,2),'','','R');
					$this->Cell(24,6,number_format($totLocNetSal,2),'','','R');
					
					unset($totLocYtdGross,$totLocBasic,$totLocLWOP,$totLocOT,$totLocAllow,$totLocGross,$totLocWithTax,$totLocSSS,$totLocLoans,$totLocYtdTax,
						  $totLocTard,$totLocNightDiff,$totLocOthInc,$totLocGrossInc,$totLocHdmf,$totLocOthDed,$totLocUt,$totLocPhic,$totLocTotDed,$totLocNetSal,$totLoc13th,$totLocLegpay,$totLocYtdGovDed);
					$this->Ln();
				}
				/*End of Display Per Location*/
				
				$this->SetFont('Courier','B','10'); 
				$this->Cell(47,6,'BRANCH TOTALS ','0','','L');
				$this->Cell(24,6,$ctrEmpBrnch,'','','R');
				$this->Cell(24,6,number_format($totBrnchYtdGross,2),'','','R');
				$this->Cell(24,6,number_format($totBrnchBasic,2),'','','R');
				$this->Cell(24,6,number_format($totBrnchLWOP,2),'','','R');
				$this->Cell(24,6,number_format($totBrnchOT,2),'','','R');
				$this->Cell(24,6,number_format($totBrnchAllow,2),'','','R');
				$this->Cell(24,6,number_format($totBrnchGross,2),'','','R');
				$this->Cell(24,6,number_format($totBrnchWithTax,2),'','','R');
				$this->Cell(24,6,number_format($totBrnchSSS,2),'','','R');
				$this->Cell(24,6,number_format($totBrnchLoans,2),'','1','R');
				$this->Cell(71,0,'','0','','R');
				$this->Cell(24,0,number_format($totBrnchYtdTax,2),'','','R');
				$this->Cell(24,0,number_format($totBrnchLegpay,2),'','','R');
				$this->Cell(24,0,number_format($totBrnchTard,2),'','','R');
				$this->Cell(24,0,number_format($totBrnchNightDiff,2),'','','R');
				$this->Cell(24,0,number_format($totBrnchOthInc,2),'','','R');
				$this->Cell(24,0,number_format($totBrnchGrossInc,2),'','','R');
				$this->Cell(24,0,'','0','','R');
				$this->Cell(24,0,number_format($totBrnchHdmf,2),'','','R');
				$this->Cell(24,0,number_format($totBrnchOthDed,2),'','1','R');
				$this->Cell(71,6,'','0','','R');
				$this->Cell(24,6,number_format($totBrnchYtdGovDed,2),'','','R');
				$this->Cell(24,6,'','0','','R');
				$this->Cell(24,6,number_format($totBrnchUt,2),'','','R');
				$this->Cell(24,6,'','0','','R');
				$this->Cell(24,6,number_format($totBrnch13th,2),'','','R');
				$this->Cell(48,6,'','0','','R');
				$this->Cell(24,6,number_format($totBrnchPhic,2),'','','R');
				$this->Cell(24,6,'','0','','R');
				$this->Cell(24,6,number_format($totBrnchTotDed,2),'','','R');
				$this->Cell(24,6,number_format($totBrnchNetSal,2),'','','R');
				
				
				unset($totBrnchYtdGross,$totBrnchBasic,$totBrnchLWOP,$totBrnchOT,$totBrnchAllow,$totBrnchGross,$totBrnchWithTax,$totBrnchSSS,$totBrnchLoans,$totBrnchYtdTax,
					  $totBrnchTard,$totBrnchNightDiff,$totBrnchOthInc,$totBrnchGrossInc,$totBrnchHdmf,$totBrnchOthDed,$totBrnchUt,$totBrnchPhic,$totBrnchTotDed,$totBrnchNetSal,$totBrnch13th,$totBrnchLegpay,$totBrnchYtdGovDed);
				
				if($cntBranch!=$noOfBrnchDisp){
					$this->Ln(5);
					$this->AddPage();
				}else{
					$this->Ln(5);
				}
				$noOfBrnchDisp++;
			}
			/*End of Display Per Branch*/
			
			/*Display Grand Total*/
			$this->SetFont('Courier','B','10'); 
			$this->Ln(5);
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
			$this->Cell(24,0,number_format($totGrandLegpay,2),'','','R');
			$this->Cell(24,0,number_format($totGrandTotTard,2),'','','R');
			$this->Cell(24,0,number_format($totGrandTotNightDiff,2),'','','R');
			$this->Cell(24,0,number_format($totGrandTotOthInc,2),'','','R');
			$this->Cell(24,0,number_format($totGrandTotGrossInc,2),'','','R');
			$this->Cell(24,0,'','0','','R');
			$this->Cell(24,0,number_format($totGrandTotHdmf,2),'','','R');
			$this->Cell(24,0,number_format($totGrandTotOthDed,2),'','1','R');
			$this->Cell(71,6,'','0','','R');
			$this->Cell(24,6,number_format($totGrandYtdGovDed,2),'','','R');
			$this->Cell(24,6,'','0','','R');
			$this->Cell(24,6,number_format($totGrandTotUt,2),'','','R');
			$this->Cell(24,6,'','0','','R');
			$this->Cell(24,6,number_format($totGrandTot13th,2),'','','R');
			$this->Cell(48,6,'','0','','R');
			$this->Cell(24,6,number_format($totGrandTotPhic,2),'','','R');
			$this->Cell(24,6,'','0','','R');
			$this->Cell(24,6,number_format($totGrandTotTotDed,2),'','','R');
			$this->Cell(24,6,number_format($totGrandTotNetSal,2),'','','R');
			$this->SetFont('Courier','','10'); 
			$this->Ln(10);
			$this->Cell(335,6,'* * * End of Report * * *','0','','C');
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',10);
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
	$locType 			= $_GET['locType'];
	
	$empBrnCode 		= $_GET['empBrnCode'];
	$topType			= 	$_GET['topType'];
	
	$reportType	= $_GET["reportType"];
	
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	
	if ($empNo>"") {$empNo1 = " AND (tblPaySum.empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (tblPaySum.empDivCode = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (tblPaySum.empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (tblPaySum.empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (tblPaySum.empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($locType=="S")
		$locType1 = " AND (tblPaySum.empLocCode = '{$empBrnCode}')";
	if ($locType=="H")
		$locType1 = " AND (tblPaySum.empLocCode = '0001')";
	
	if ($empNo>"") {$empNo2 = " AND (tblEarn.empNo LIKE '{$empNo}%')"; } else {$empNo2 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv2 = " AND (empDiv= '{$empDiv}')"; } else {$empDiv2 = "";}
	if ($empDept>"" && $empDept>0) {$empDept2 = " AND (empDepCode = '{$empDept}')"; } else {$empDept2 = "";}
	if ($empSect>"" && $empSect>0) {$empSect2 = " AND (empSecCode = '{$empSect}')"; } else {$empSect2 = "";}
	if ($empBrnCode!="0") {$empBrnCode2 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode2 = "";}
	if ($locType=="S")
		$locType2 = " AND (empLocCode = '{$empBrnCode}')";
	if ($locType=="H")
		$locType2 = " AND (empLocCode = '0001')";
	
	if ($empNo>"") {$empNo3 = " AND (empNo LIKE '{$empNo}%')"; } else {$empNo3 = "";}
	
	$pdf->where_empmast = $empNoRep.$empNo1.$empDiv1.$empDept1.$empSect1.$empBrnCode1.$locType1;
	$pdf->where_empmast2 = $empNo3.$empDiv2.$empDept2.$empSect2.$empBrnCode2.$locType2;
	$pdf->where_paysum = $empNo1.$empDiv1.$empDept1.$empSect1.$empBrnCode1.$locType1;
	
	
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
	
	if($reportType == '0')
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
						AND empmast.empNo IN 
								(Select empNo from $tbl where
								pdYear='{$arrPayPd['pdYear']}'
								AND pdNumber = '{$arrPayPd['pdNumber']}'
								AND payGrp = '{$_SESSION['pay_group']}'
								AND payCat = '{$_SESSION['pay_category']}'
								AND compCode = '{$_SESSION['company_code']}'
								    )
						AND ptTrans.compCode ='".$_SESSION["company_code"]."'
						AND trnStat = 'A'
						AND trnRecode='".EARNINGS_RECODEALLOW."'
						AND (sprtPS='Y')
						$where_trnCode
						$empNo2 $empName2 $empDiv2 $empName2 $empDept2 $empSect2 $empBrnCode2 $locType2
						order by trnDesc, empLastName, empFirstName";

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
	
	
$pdf->Output('pay_register.pdf','D');
?>
