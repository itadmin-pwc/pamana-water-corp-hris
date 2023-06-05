<?php
	/*
		Created By		:	Genarra Arong
		Date Created	:	01192010
		Reason			:	Report for the Unposted Transactions
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pdf/fpdf.php");
	include("empmast_migration.obj.php");
	
	$migEmpMastObj= new migEmpMastObj();
	$sessionVars = $migEmpMastObj->getSeesionVars();
	
	
	class PDF extends FPDF
	{
		function Header()
		{
				$gmt = time() + (8 * 60 * 60);
				$newdate = date("m/d/Y h:iA", $gmt);
				
				$this->SetFont('Courier','','9'); 
				$this->Cell(70,5,"Run Date: " . $newdate);
				$this->Cell(140,5,$this->company,'0','','C');
				$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
				$this->Ln();
				
				$this->Cell(70,5,"Report ID: CONSOLRPT");
				$this->Cell(140,5,'Consolidation Report','0','','C');
				$this->Ln();
		}	
		
		function dispBranch()
		{
			$qryBranch = "Select brnCode, brnDesc from tblBranch
							where compCode='".$_SESSION["company_code"]."'
							and brnCode in (Select distinct(empBrnCode) from tblEmpMast
							where empPayCat<>0 and compCode='".$_SESSION["company_code"]."' ".($this->brnCode==0?"":"and empBrnCode='".$_GET["empBrnCode"]."'")." and empPayGrp='".$_SESSION["pay_group"]."' and empPayCat='".$_SESSION["pay_category"]."') 
							order by brnDesc;";
			
			$resBranch = $this->execQry($qryBranch);
			$resBranch = $this->getArrRes($resBranch);
			return $resBranch;
		}
		
		function dispAllowance($empNo)
		{
			$qryEmpAllow = "Select tblAllow.allowCode,allowDesc, allowAmt, allowSked, allowStart from tblAllowance tblAllow, tblAllowType tblAllowType
							where tblAllow.compCode='".$_SESSION["company_code"]."' and tblAllowType.compCode='".$_SESSION["company_code"]."' and
							tblAllow.allowCode=tblAllowType.allowCode and empNo='".$empNo."'
							;";
			$resEmpAllow = $this->execQry($qryEmpAllow);
			$arrEmpAllow = $this->getArrRes($resEmpAllow);
			if(count($arrEmpAllow)>=1)
			{
				$this->SetFont('Courier','B','10'); 
				$this->Cell(10,5,'','','','L');
				$this->Cell(320,5,'ALLOWANCE INFORMATION','','1','L');
				$this->Cell(10,5,'','','','L');
				$this->Cell(80,5,'Allowance Type','1','','L');
				$this->Cell(80,5,'Allowance Sked','1','','L');
				$this->Cell(80,5,'Allowance Start Date','1','','L');
				$this->Cell(80,5,'Allowance Amount','1','1','R');
				$this->SetFont('Courier','','10'); 
				foreach($arrEmpAllow as $arrEmpAllow_val)
				{
					$this->Cell(10,5,'','','','L');
					$this->Cell(80,5,$arrEmpAllow_val["allowDesc"],'1','','L');
					
					$this->Cell(80,5,($arrEmpAllow_val["allowSked"]=='1'?"1st Period":"Both"),'1','','L');
					$this->Cell(80,5,date("Y-m-d", strtotime($arrEmpAllow_val["allowStart"])),'1','','L');
					$this->Cell(80,5,number_format($arrEmpAllow_val["allowAmt"],2),'1','1','R');
				}
				$this->Ln();
			}
		}
		
		function dispMtdGovt($empNo)
		{
			$qryEmpAllow = "Select pdYear, pdMonth, mtdEarnings, sssEmp, sssEmplr, ec, phicEmp, phicEmplr, hdmfEmp, hdmfEmplr from tblMtdGovtHist
					 		where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' AND pdYear='".$_GET['pdYear']."' order by pdYear, pdMonth
							;";
			$resEmpAllow = $this->execQry($qryEmpAllow);
			$arrEmpAllow = $this->getArrRes($resEmpAllow);
			if(count($arrEmpAllow)>=1)
			{
				$this->SetFont('Courier','B','10'); 
				$this->Cell(10,5,'','','','L');
				$this->Cell(320,5,'MONTHLY CONTRIBUTION','','1','L');
				$this->Cell(10,5,'','','','L');
				$this->Cell(35.55,5,'Year','1','','L');
				$this->Cell(35.55,5,'Month','1','','L');
				$this->Cell(35.55,5,'Total Earnings','1','','L');
				$this->Cell(35.55,5,'Sss Emp. Cont.','1','','L');
				$this->Cell(35.55,5,'Sss Emplr. Cont.','1','','L');
				$this->Cell(35.55,5,'Hdmf Emp. Cont.','1','','L');
				$this->Cell(35.55,5,'Hdmf Emplr. Cont.','1','','L');
				$this->Cell(35.55,5,'Phic Emp. Cont.','1','','L');
				$this->Cell(35.55,5,'Phic Emplr. Cont.','1','1','L');
				
				$this->SetFont('Courier','','10'); 
				foreach($arrEmpAllow as $arrEmpAllow_val)
				{
					$this->Cell(10,5,'','','','L');
					$this->Cell(35.55,5,$arrEmpAllow_val["pdYear"],'1','','L');
					$this->Cell(35.55,5,$arrEmpAllow_val["pdMonth"],'1','','L');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["mtdEarnings"],2),'1','','R');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["sssEmp"],2),'1','','R');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["sssEmplr"],2),'1','','R');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["hdmfEmp"],2),'1','','R');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["hdmfEmplr"],2),'1','','R');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["phicEmp"],2),'1','','R');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["phicEmplr"],2),'1','1','R');
					
					$empSss+=$arrEmpAllow_val["sssEmp"];
					$empHdmf+=$arrEmpAllow_val["hdmfEmp"];
					$empPhicEmp+=$arrEmpAllow_val["phicEmp"];
					$empContTotals+=$arrEmpAllow_val["sssEmp"] + $arrEmpAllow_val["hdmfEmp"] + $arrEmpAllow_val["phicEmp"];
				}
				
				$this->Cell(10,5,'','','','L');
				$this->Cell(35.55,5,'','1','','L');
				$this->Cell(35.55,5,'','1','L');
				$this->Cell(35.55,5,'','1','','R');
				$this->Cell(35.55,5,number_format($empSss,2),'1','','R');
				$this->Cell(35.55,5,'','1','','R');
				$this->Cell(35.55,5,number_format($empHdmf,2),'1','','R');
				$this->Cell(35.55,5,'','1','','R');
				$this->Cell(35.55,5,number_format($empPhicEmp,2),'1','','R');
				$this->SetFont('Courier','BU','10'); 
				$this->Cell(35.55,5,number_format($empContTotals,2),'1','1','R');
				$this->SetFont('Courier','','10'); 
				unset($empSss, $empHdmf, $empPhicEmp,$empContTotals);
				
				$this->Ln();
			}
		}
		
		function dispPaySum($empNo)
		{
			$qryEmpAllow = "SELECT     pdYear, pdNumber,grossEarnings, taxableEarnings, minwage_taxableEarnings, totDeductions, sprtAllow, netSalary, taxWitheld, sprtAllowAdvance, empBasic, empEcola
							FROM         tblPayrollSummaryHist where compCode='".$_SESSION["company_code"]."' and pdYear='".($_GET['pdYear']-1)."' and pdNumber IN (23,24) and empNo='".$empNo."' order by pdYear, pdNumber";;
			$resEmpAllow = $this->execQry($qryEmpAllow);
			$arrEmpAllow = $this->getArrRes($resEmpAllow);
			if(count($arrEmpAllow)>=1)
			{
				$this->SetFont('Courier','B','10'); 
				$this->Cell(10,5,'','','','L');
				$this->Cell(320,5,'PAYROLL SUMMARY FOR hist '.($_GET['pdYear']-1),'','1','L');
				$this->Cell(10,5,'','','','L');
				$this->Cell(20,5,'Year','1','','L');
				$this->Cell(20,5,'PdNumber','1','','L');
				$this->Cell(25.99,5,'Gross Earn.','1','','R');
				$this->Cell(27.99,5,'(Abv)Tax Earn','1','','R');
				$this->Cell(27.99,5,'(Min)Tax Earn','1','','R');
				$this->Cell(35.99,5,'Tot. Deductions','1','','R');
				$this->Cell(28.99,5,'Tax Witheld','1','','R');
				$this->Cell(27.99,5,'Net Salary','1','0','R');
				
				$this->Cell(28.99,5,'Sprt. Allow','1','','R');
				$this->Cell(41.99,5,'Sprt. Advan. Allow','1','','R');
				$this->Cell(28.99,5,'Emp. Basic','1','','R');
				$this->Cell(15.99,5,'Ecola','1','1','R');
				
				
				
				$this->SetFont('Courier','','10'); 
				foreach($arrEmpAllow as $arrEmpAllow_val)
				{
					$this->Cell(10,5,'','','','L');
					$this->Cell(20,5,$arrEmpAllow_val["pdYear"],'1','','L');
					$this->Cell(20,5,$arrEmpAllow_val["pdNumber"],'1','','L');
					$this->Cell(25.99,5,number_format($arrEmpAllow_val["grossEarnings"],2),'1','','R');
					$this->Cell(27.99,5,number_format($arrEmpAllow_val["taxableEarnings"],2),'1','','R');
					$this->Cell(27.99,5,number_format($arrEmpAllow_val["minwage_taxableEarnings"],2),'1','','R');
					$this->Cell(35.99,5,number_format($arrEmpAllow_val["totDeductions"],2),'1','','R');
					$this->Cell(28.99,5,number_format($arrEmpAllow_val["taxWitheld"],2),'1','','R');
					$this->Cell(27.99,5,number_format($arrEmpAllow_val["netSalary"],2),'1','0','R');
					
					$this->Cell(28.99,5,number_format($arrEmpAllow_val["sprtAllow"],2),'1','','R');
					$this->Cell(41.99,5,number_format($arrEmpAllow_val["sprtAllowAdvance"],2),'1','','R');
					$this->Cell(28.99,5,number_format($arrEmpAllow_val["empBasic"],2),'1','','R');
					$this->Cell(15.99,5,number_format($arrEmpAllow_val["empEcola"],2),'1','1','R');
					
					
					$empGrossPay+=$arrEmpAllow_val["grossEarnings"];
					$empMinTaxEarn+=$arrEmpAllow_val["minwage_taxableEarnings"];
					$empTaxEarn+=$arrEmpAllow_val["taxableEarnings"];
					$empdedTax+=$arrEmpAllow_val["totDeductions"];
					$empTaxWitheld+=$arrEmpAllow_val["taxWitheld"];
					$empNetSal+=$arrEmpAllow_val["netSalary"];
					$empSprtAllow+=$arrEmpAllow_val["sprtAllow"];
					$empSprtAllowAdvance+=$arrEmpAllow_val["sprtAllowAdvance"];
					$empBasic+=$arrEmpAllow_val["empBasic"];
					$empEcola+=$arrEmpAllow_val["empEcola"];
					
					
					
					
				}
					$this->SetFont('Courier','B','10'); 
					$this->Cell(10,5,'','','','L');
					$this->Cell(40,5,'TOTAL','1','','L');
					$this->Cell(25.99,5,number_format($empGrossPay,2),'1','','R');
					$this->Cell(27.99,5,number_format($empTaxEarn,2),'1','','R');
					$this->Cell(27.99,5,number_format($empMinTaxEarn,2),'1','','R');
					$this->Cell(35.99,5,number_format($empdedTax,2),'1','','R');
					$this->Cell(28.99,5,number_format($empTaxWitheld,2),'1','','R');
					$this->Cell(27.99,5,number_format($empNetSal,2),'1','','R');
					$this->Cell(28.99,5,number_format($empSprtAllow,2),'1','','R');
					$this->Cell(41.99,5,number_format($empSprtAllowAdvance,2),'1','','R');
					$this->Cell(28.99,5,number_format($empBasic,2),'1','0','R');
					$this->Cell(15.99,5,number_format($empEcola,2),'1','1','R');
					$this->SetFont('Courier','','10'); 
					unset($empGrossPay,$empTaxEarn,$empdedTax,$empEcola,$empBasic,$empTaxWitheld,$empNetSal,$empSprtAllow, $empSprtAllowAdvance,$empMinTaxEarn);
				
				$this->Ln();
			}
		}
		
		function dispPaySum_cur($empNo)
		{
			$qryEmpAllow = "SELECT     pdYear, pdNumber,grossEarnings, taxableEarnings, minwage_taxableEarnings, totDeductions, sprtAllow, netSalary, taxWitheld, sprtAllowAdvance, empBasic, empEcola
							FROM         tblPayrollSummaryHist where compCode='".$_SESSION["company_code"]."' and pdYear='".$_GET['pdYear']."' and empNo='".$empNo."' order by pdYear, pdNumber";;
			$resEmpAllow = $this->execQry($qryEmpAllow);
			$arrEmpAllow = $this->getArrRes($resEmpAllow);
			if(count($arrEmpAllow)>=1)
			{
				$this->SetFont('Courier','B','10'); 
				$this->Cell(10,5,'','','','L');
				$this->Cell(320,5,'PAYROLL SUMMARY FOR cur '.$_GET['pdYear'],'','1','L');
				$this->Cell(10,5,'','','','L');
				$this->Cell(20,5,'Year','1','','L');
				$this->Cell(20,5,'PdNumber','1','','L');
				$this->Cell(25.99,5,'Gross Earn.','1','','R');
				$this->Cell(27.99,5,'(Abv)Tax Earn','1','','R');
				$this->Cell(27.99,5,'(Min)Tax Earn','1','','R');
				$this->Cell(35.99,5,'Tot. Deductions','1','','R');
				$this->Cell(28.99,5,'Tax Witheld','1','','R');
				$this->Cell(27.99,5,'Net Salary','1','0','R');
				
				$this->Cell(28.99,5,'Sprt. Allow','1','','R');
				$this->Cell(41.99,5,'Sprt. Advan. Allow','1','','R');
				$this->Cell(28.99,5,'Emp. Basic','1','','R');
				$this->Cell(15.99,5,'Ecola','1','1','R');
				
				
				
				$this->SetFont('Courier','','10'); 
				foreach($arrEmpAllow as $arrEmpAllow_val)
				{
					$this->Cell(10,5,'','','','L');
					$this->Cell(20,5,$arrEmpAllow_val["pdYear"],'1','','L');
					$this->Cell(20,5,$arrEmpAllow_val["pdNumber"],'1','','L');
					$this->Cell(25.99,5,number_format($arrEmpAllow_val["grossEarnings"],2),'1','','R');
					$this->Cell(27.99,5,number_format($arrEmpAllow_val["taxableEarnings"],2),'1','','R');
					$this->Cell(27.99,5,number_format($arrEmpAllow_val["minwage_taxableEarnings"],2),'1','','R');
					$this->Cell(35.99,5,number_format($arrEmpAllow_val["totDeductions"],2),'1','','R');
					$this->Cell(28.99,5,number_format($arrEmpAllow_val["taxWitheld"],2),'1','','R');
					$this->Cell(27.99,5,number_format($arrEmpAllow_val["netSalary"],2),'1','0','R');
					
					$this->Cell(28.99,5,number_format($arrEmpAllow_val["sprtAllow"],2),'1','','R');
					$this->Cell(41.99,5,number_format($arrEmpAllow_val["sprtAllowAdvance"],2),'1','','R');
					$this->Cell(28.99,5,number_format($arrEmpAllow_val["empBasic"],2),'1','','R');
					$this->Cell(15.99,5,number_format($arrEmpAllow_val["empEcola"],2),'1','1','R');
					
					
					$empGrossPay+=$arrEmpAllow_val["grossEarnings"];
					$empMinTaxEarn+=$arrEmpAllow_val["minwage_taxableEarnings"];
					$empTaxEarn+=$arrEmpAllow_val["taxableEarnings"];
					$empdedTax+=$arrEmpAllow_val["totDeductions"];
					$empTaxWitheld+=$arrEmpAllow_val["taxWitheld"];
					$empNetSal+=$arrEmpAllow_val["netSalary"];
					$empSprtAllow+=$arrEmpAllow_val["sprtAllow"];
					$empSprtAllowAdvance+=$arrEmpAllow_val["sprtAllowAdvance"];
					$empBasic+=$arrEmpAllow_val["empBasic"];
					$empEcola+=$arrEmpAllow_val["empEcola"];
					
					
					
					
				}
					$this->SetFont('Courier','B','10'); 
					$this->Cell(10,5,'','','','L');
					$this->Cell(40,5,'TOTAL','1','','L');
					$this->Cell(25.99,5,number_format($empGrossPay,2),'1','','R');
					$this->Cell(27.99,5,number_format($empTaxEarn,2),'1','','R');
					$this->Cell(27.99,5,number_format($empMinTaxEarn,2),'1','','R');
					$this->Cell(35.99,5,number_format($empdedTax,2),'1','','R');
					$this->Cell(28.99,5,number_format($empTaxWitheld,2),'1','','R');
					$this->Cell(27.99,5,number_format($empNetSal,2),'1','','R');
					$this->Cell(28.99,5,number_format($empSprtAllow,2),'1','','R');
					$this->Cell(41.99,5,number_format($empSprtAllowAdvance,2),'1','','R');
					$this->Cell(28.99,5,number_format($empBasic,2),'1','0','R');
					$this->Cell(15.99,5,number_format($empEcola,2),'1','1','R');
					$this->SetFont('Courier','','10'); 
					unset($empGrossPay,$empTaxEarn,$empdedTax,$empEcola,$empBasic,$empTaxWitheld,$empNetSal,$empSprtAllow, $empSprtAllowAdvance,$empMinTaxEarn);
				
				$this->Ln();
			}
		}
		
		function dispYtd($empNo, $empStat)
		{
			
			$qryEmpAllow = "SELECT     pdYear, YtdGross, YtdTaxable, YtdGovDed, YtdTax, sprtAllow, YtdBasic, sprtAdvance, YtdGovDedMinWage, YtdGovDedAbvWage
							FROM         tblYtdDataHist  where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' order by pdYear";
			$resEmpAllow = $this->execQry($qryEmpAllow);
			$arrEmpAllow = $this->getArrRes($resEmpAllow);
			if(count($arrEmpAllow)>=1)
			{
				$this->SetFont('Courier','B','10'); 
				$this->Cell(10,5,'','','','L');
				$this->Cell(320,5,'YTD DATA','','1','L');
				$this->Cell(10,5,'','','','L');
				$this->Cell(25.55,5,'Year','1','','L');
				$this->Cell(25.55,5,'Ytd Gross','1','','L');
				$this->Cell(35.55,5,'Ytd Taxable','1','','R');
				$this->Cell(35.55,5,'Ytd GovDed','1','','R');
				$this->Cell(25.55,5,'Ytd Tax','1','','R');
				$this->Cell(35.55,5,'Ytd SprtAllow','1','','R');
				$this->Cell(55.55,5,'Ytd SprtAllow Advance','1','','R');
				$this->Cell(45.55,5,'Ytd GovDedMinWage','1','0','R');
				$this->Cell(45.55,5,'Ytd GovDedAbvWage','1','1','R');
				
				
				
				$this->SetFont('Courier','','10'); 
				foreach($arrEmpAllow as $arrEmpAllow_val)
				{ 
					$this->Cell(10,5,'','','','L');
					$this->Cell(25.55,5,$arrEmpAllow_val["pdYear"],'1','','L');
					$this->Cell(25.55,5,number_format($arrEmpAllow_val["YtdGross"],2),'1','','L');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["YtdTaxable"],2),'1','','R');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["YtdGovDed"],2),'1','','R');
					$this->Cell(25.55,5,number_format($arrEmpAllow_val["YtdTax"],2),'1','','R');
					$this->Cell(35.55,5,number_format($arrEmpAllow_val["sprtAllow"],2),'1','','R');
					$this->Cell(55.55,5,number_format($arrEmpAllow_val["sprtAdvance"],2),'1','','R');
					$this->Cell(45.55,5,number_format($arrEmpAllow_val["YtdGovDedMinWage"],2),'1','','R');
					$this->Cell(45.55,5,number_format($arrEmpAllow_val["YtdGovDedAbvWage"],2),'1','1','R');
					
					
				}
				$this->Ln();
			}
			if($empStat=='IN')
			{
				$qryEmpAllow = "SELECT     pdYear, YtdGross, YtdTaxable, YtdGovDed, YtdTax, sprtAllow, YtdBasic, sprtAdvance, YtdGovDedMinWage, YtdGovDedAbvWage
							FROM         tblYtdDataHist  where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' and pdYear='".date("Y")."'";
				$resEmpAllow = $this->execQry($qryEmpAllow);
				$arrEmpAllow = $this->getArrRes($resEmpAllow);
				if(count($arrEmpAllow)>=1)
				{
					$this->SetFont('Courier','B','10'); 
					$this->Cell(10,5,'','','','L');
					$this->Cell(320,5,'PREVIOUS EMPLOYER','','1','L');
					$this->Cell(10,5,'','','','L');
					$this->Cell(25,5,'Year','1','','L');
					$this->Cell(47.5,5,'Gross Taxable','1','','L');
					$this->Cell(47.5,5,'Gross Non Taxable','1','','R');
					$this->Cell(47.5,5,'Tax Witheld','1','','R');
					$this->Cell(47.5,5,'SSS, HDMF, PHIC','1','','R');
					$this->Cell(47.5,5,'Emp. Basic (Current)','1','','R');
					$this->Cell(47.5,5,'Emp. Basic (Previous)','1','1','R');
					
					$this->Cell(10,5,'','','','L');
					$this->Cell(25,5,$arrEmpAllow_val["pdYear"],'1','','L');
					$this->Cell(47.5,5,sprintf("%01.2f",$arrEmpAllow_val["YtdTaxable"]),'1','','L');
					$this->Cell(47.5,5,sprintf("%01.2f",$arrEmpAllow_val["YtdGross"]),'1','','R');
					$this->Cell(47.5,5,sprintf("%01.2f",$arrEmpAllow_val["YtdTax"]),'1','','R');
					$this->Cell(47.5,5,sprintf("%01.2f",$arrEmpAllow_val["YtdGovDed"]),'1','','R');
				}	
				
					$qryEmpPaySumHist ="SELECT sum(empBasic) as sumEmpBasic
									from tblPayrollSummaryHist
									where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' and pdYear='".date("Y")."'";
					$arrEmpPaySumHist = $this->getSqlAssoc($this->execQry($qryEmpPaySumHist));
					$this->Cell(47.5,5,sprintf("%01.2f",$arrEmpPaySumHist["sumEmpBasic"]),'1','','R');
					
					$qryEmpPaySumHist ="SELECT sum(empBasic) as sumEmpBasic
									from tblPayrollSummaryHist
									where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' and pdYear='".(date("Y")-1)."' and pdNumber in (23,24)";
					$arrEmpPaySumHist = $this->getSqlAssoc($this->execQry($qryEmpPaySumHist));
					$this->Cell(47.5,5,sprintf("%01.2f",$arrEmpPaySumHist["sumEmpBasic"]),'1','','R');
					
					$this->Ln();
					$this->Ln();
			}
			
		}
		
		
		
		function displayContentDetails($arrEmpList)
		{
			$this->Ln();
			$this->SetFont('Courier','','10'); 
			$arrdispBranch = $this->dispBranch();
			
			foreach($arrdispBranch as $arrdispBranch_val)
			{
				$this->SetFont('Courier','B','10'); 
				$this->Cell(47,6,"BRANCH = ".strtoupper($arrdispBranch_val["brnDesc"]),0,'1','L');
				$this->SetFont('Courier','','10'); 
				$empCtr = 1;
				foreach($arrEmpList as $arrEmpList_val)
				{
					
					if($arrEmpList_val["empBrnCode"]==$arrdispBranch_val["brnCode"])
					{
						$this->Cell(47,6,$empCtr.".".$arrEmpList_val["empNo"]."=".$arrEmpList_val["empLastName"].", ".$arrEmpList_val["empFirstName"]." ".$arrEmpList_val["empMidName"][0].". = ".$arrEmpList_val["empTeu"]." = ".$arrEmpList_val["empMrate"],'','1','L');
						
						//Display Allowance
						$this->dispAllowance($arrEmpList_val["empNo"]);
						
						//Payroll Summary 2013
						$this->dispPaySum($arrEmpList_val["empNo"]);
						
						//Payroll Summary 2013
						$this->dispPaySum_cur($arrEmpList_val["empNo"]);
						
						//Display MTD
						$this->dispMtdGovt($arrEmpList_val["empNo"]);	
						
						//Ytd
						$this->dispYtd($arrEmpList_val["empNo"], $arrEmpList_val["empStat"]);
						
						
						
							
						$empCtr++;				
					}
				}//end of EmpList
				$this->AddPage();
				
			}//end of Branch
			
			
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','',9);



			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
		
	}
	
	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$pdf->company = $migEmpMastObj->getCompanyName($_SESSION['company_code']);
	$pdf->brnCode = $_GET["empBrnCode"];
	//List of New Employees
	$qryListEmp = "Select * from tblEmpMast where empPayGrp='".$_SESSION['pay_group']."' and  compCode='".$_SESSION["company_code"]."' ".($_GET["empBrnCode"]==0?"":"and empBrnCode='".$_GET["empBrnCode"]."'")."
					 order by empLastName";
	$resListEmp = $migEmpMastObj->execQry($qryListEmp);
	$arrListEmp = $migEmpMastObj->getArrRes($resListEmp);
	if(count($arrListEmp)>0)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $migEmpMastObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContentDetails($arrListEmp);
	}
	$pdf->Output();
?>



