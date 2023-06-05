<?php
	/*
		Created By		:	Genarra Arong
		Date Created	:	01192011
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
				
				$this->SetFont('Arial','','9'); 
				$this->Cell(70,5,"Run Date: " . $newdate);
				$this->Cell(140,5,$this->company,'0','','C');
				$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
				$this->Ln();
				
				if($_SESSION["pay_category"]=='1')
					$payCatDesc = "Executive";
				elseif($_SESSION["pay_category"]=='2')
					$payCatDesc = "Confidential";
				else
					$payCatDesc = "Non - Confidential";
					
				
				$this->Cell(70,5,"Report ID: CONSOLRPT");
				$this->Cell(140,5,'13TH Month Consolidation Report for Group - '.$_SESSION["pay_group"]." (".$payCatDesc,'0','','C');
				$this->Ln();
			
		}	
		
		function dispBranch()
		{
			if ($_SESSION['pay_group']==1) {
				$date = '11/18/2011';
			} else {
				$date = '11/23/2011';
			}
			
	
			//For Active Employees
			$qryBranch = "Select brnCode, brnDesc from tblBranch
							where compCode='".$_SESSION["company_code"]."'
							and brnCode in (Select empBrnCode from tblEmpMast where compCode='".$_SESSION["company_code"]."' ".($_GET["empBrnCode"]==0?"":"and empBrnCode='".$_GET["empBrnCode"]."'")."
							and empStat in ('RG','CN', 'PR') AND datehired<='$date'    and empPayGrp='".$_SESSION['pay_group']."'  and empPayCat='".$_SESSION["pay_category"]."' and empNo not in (010001423,010001425,010001426,010001427,010001428,010001459,010001460,010001461,010001462)  ) 
							
							order by brnDesc;";
			
			//For Resigned Employees
			/*$qryBranch = "Select brnCode, brnDesc from tblBranch
							where compCode='".$_SESSION["company_code"]."'
							and brnCode in (Select empBrnCode from tblEmpMast where compCode='".$_SESSION["company_code"]."' 
				   			and empNo in (200001838,320000242,320000404,140001638,140000090)) 
							order by brnDesc;";*/
			
			
			$resBranch = $this->execQry($qryBranch);
			$resBranch = $this->getArrRes($resBranch);
			return $resBranch;
		}
		
		
		
		function getReClassAllow($empNo)
		{
			$qryReclassAllow = "SELECT     totAllowAdj, totRclsAdv
							FROM         tblAllowReclass  where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."'";
			$resReclassAllow = $this->execQry($qryReclassAllow);
			$arrReclassAllow = $this->getSqlAssoc($resReclassAllow);
			
			return 	$arrReclassAllow;
		}
		
		function getReClassAdj($empNo)
		{
			$qryReclassAdj = "SELECT     totBasicAdj, totRclsBasic
							FROM         tblBasicReclass  where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."'";
				
			$resReclassAdj = $this->execQry($qryReclassAdj);
			$arrReclassAdj = $this->getSqlAssoc($resReclassAdj);
			
			return 	$arrReclassAdj;
		}
		
		function getEarnings($empNo, $pdYear, $pdNumber)
		{
			
			$qryEarningsHist = "SELECT     sum(trnAmountE) as trnAmountE,trnCode
							FROM         tblEarningsHist
							where empNo='".$empNo."' and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and trnCode IN (".EARNINGS_BASIC.",".EARNINGS_ABS.",".EARNINGS_TARD.",".EARNINGS_UT.",".ADJ_BASIC.",".ALLW_ADVANCES.",".ADJ_ADVANCES.") group by trnCode";
			//echo $qryEarnings;
			$resEarningsHist = $this->execQry($qryEarningsHist);
			return $this->getArrRes($resEarningsHist);
			
			
		}
		
		function getRcDual($empNo)
		{
			$qrygetRcDual = "SELECT    rcdBasic,rcdAdvances
							FROM         tblRcdualEarnings
							where empNo='".$empNo."' and pdYear='".date("Y")."'";
			$resgetRcDual = $this->execQry($qrygetRcDual);
			$arrgetRcDual = $this->getSqlAssoc($resgetRcDual);
			
			return $arrgetRcDual;
		}
		
		
		function gettblYtdData($empNo)
		{
			$qryEmpYtd = "SELECT     Ytd13NBonus,YTd13NAdvance, YtdTx13NBonus
							FROM         tblYtdData  where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' order by pdYear";
			$resEmpYtd = $this->execQry($qryEmpYtd);
			$arrEmpYtd = $this->getSqlAssoc($resEmpYtd);
			
			return $arrEmpYtd;
		}
		
		function dispPaySum($empNo)
		{
			/*$qryEarnings = "SELECT     pdYear, pdNumber, empNo, trnAmountE, trnCode
							FROM         tblEarningsHist
							where empNo='".$empNo."' and pdYear='".date("Y")."'";*/
			$prevYear = date('Y')-1 ;			
			$qryEarnings =	"
			SELECT     pdYear, pdNumber,empNo, empBasic,sprtAllowAdvance, grossEarnings, empBasic
							FROM         tblPayrollSummaryHist where compCode='".$_SESSION["company_code"]."' and pdYear in ('".$prevYear."') and empNo='".$empNo."' and pdNumber IN (23,24)
			UNION							
			SELECT     pdYear, pdNumber,empNo, empBasic,sprtAllowAdvance, grossEarnings, empBasic
							FROM         tblPayrollSummaryHist where compCode='".$_SESSION["company_code"]."' and pdYear in ('".date('Y')."') and empNo='".$empNo."' order by pdYear, pdNumber";
			$resEarnings = $this->execQry($qryEarnings);
			$arrEarnings = $this->getArrRes($resEarnings);
			
			if(count($arrEarnings)>=1)
			{
				$this->SetFont('Arial','B','8'); 
				$this->Cell(10,5,'','','','L');
				$this->Cell(320,5,'EARNINGS DETAIL','','1','L');
				
				
				$this->Cell(45,5,'','','','C');
				
				$this->Cell(158.3,5,'EMPLOYEE BASIC','1','','C');
				$this->Cell(63.32,5,'EMPLOYEE ADVANCES','1','','C');
				$this->Cell(63.32,5,'TOTAL','1','1','C');
				
				$this->Cell(10,5,'','','','L');
				$this->Cell(15,5,'YEAR','1','','C');
				$this->Cell(20,5,'PD.NUM','1','','C');
				$this->Cell(31.66,5,'BASIC','1','','C');
				$this->Cell(31.66,5,'LWOP','1','','C');
				$this->Cell(31.66,5,'TARD.','1','','C');
				$this->Cell(31.66,5,'UNDERTIME','1','','C');
				$this->Cell(31.66,5,'ADJUST. BASIC','1','','C');
				$this->Cell(31.66,5,'ADVANCES','1','','C');
				$this->Cell(31.66,5,'ADJUSTMENTS','1','','C');
				$this->Cell(31.66,5,'BASIC','1','','C');
				$this->Cell(31.66,5,'ADVANCES','1','1','C');
				
				$this->SetFont('Arial','','8'); 
				
				$empTotBasic = $empTotLwop = $empTotTard = $empTotUt = $empTotAdjBasic = $empTotAdvances = $empTotOrgAdvan = $empTotAdjAdvan = $empTotEmployeeBasic = 0;
				foreach($arrEarnings as $arrEarnings_val)
				{
					$employee_totBasic =  $employee_Basic = $employee_Lwop= $employee_Tard = $employee_Ut = $employee_AdjBasic = $employee_Advance = $employee_OrgAdvance = $employee_AdjAdvance = 0;
					
					$arrEmpEarnings = $this->getEarnings($arrEarnings_val["empNo"], $arrEarnings_val["pdYear"], $arrEarnings_val["pdNumber"]);
					foreach($arrEmpEarnings as $val) {
						switch($val['trnCode']) {
							case EARNINGS_BASIC:
								$employee_Basic = (float)$val['trnAmountE'];
							break;	
							case EARNINGS_ABS:
								$employee_Lwop = (float)$val['trnAmountE'];
							break;	
							case EARNINGS_TARD:
								$employee_Tard = (float)$val['trnAmountE'];
							break;
							case EARNINGS_UT:
								$employee_Ut = (float)$val['trnAmountE'];
							break;
							case ADJ_BASIC:
								$employee_AdjBasic = (float)$val['trnAmountE'];
							break;
							case ALLW_ADVANCES:
								$employee_OrgAdvance = (float)$val['trnAmountE'];
							break;
							case ADJ_ADVANCES:
								$employee_AdjAdvance = (float)$val['trnAmountE'];
							break;																												
						}
					}
					
					$employee_Advance = $employee_OrgAdvance  + 	$employee_AdjAdvance;
					$employee_totBasic =  $employee_Basic + $employee_Lwop + $employee_Tard + $employee_Ut + $employee_AdjBasic;
					$empTotBasic+= $employee_Basic;
					$empTotLwop+= $employee_Lwop;
					$empTotTard+= $employee_Tard;
					$empTotUt+= $employee_Ut;
					$empTotAdjBasic+= $employee_AdjBasic;
					
					$empTotOrgAdvan+= $employee_OrgAdvance;
					$empTotAdjAdvan+= $employee_AdjAdvance;
					
					
					
					
					if($employee_totBasic!=0)
					{
						$this->Cell(10,5,'','','','L');
						$this->Cell(15,5,$arrEarnings_val["pdYear"],'1','','L');
						$this->Cell(20,5,$arrEarnings_val["pdNumber"],'1','','L');
						$this->Cell(31.66,5,number_format($employee_Basic,2),'1','','R');
						$this->Cell(31.66,5,number_format($employee_Lwop,2),'1','','R');
						$this->Cell(31.66,5,number_format($employee_Tard,2),'1','','R');
						$this->Cell(31.66,5,number_format($employee_Ut,2),'1','','R');
						$this->Cell(31.66,5,number_format($employee_AdjBasic,2),'1','','R');
						
						$this->Cell(31.66,5,number_format($employee_OrgAdvance,2),'1','','R');
						$this->Cell(31.66,5,number_format($employee_AdjAdvance,2),'1','','R');
						
						$this->SetFont('Arial','B','8'); 
						$this->Cell(31.66,5,number_format($employee_totBasic,2),'1','','R');
						$this->Cell(31.66,5,($employee_Advance!=0?number_format($employee_Advance,2):number_format($arrEarnings_val["sprtAllowAdvance"],2)),'1','1','R');
						
						$empTotAdvances+=($employee_Advance!=0?$employee_Advance:$arrEarnings_val["sprtAllowAdvance"]);
						$empTotEmployeeBasic+=$employee_totBasic;
						
						//echo "EARNINGS - >".$employee_Advance."<br>";
						$this->SetFont('Arial','','8'); 
					}
					else
					{
							$this->Cell(10,5,'','','','L');
							$this->Cell(15,5,$arrEarnings_val["pdYear"],'1','','L');
							$this->Cell(20,5,$arrEarnings_val["pdNumber"],'1','','L');
							$this->Cell(31.66,5,'','1','','R');
							$this->Cell(31.66,5,'','1','','R');
							$this->Cell(31.66,5,'','1','','R');
							$this->Cell(31.66,5,'','1','','R');
							$this->Cell(31.66,5,'','1','','R');
							
							$this->Cell(31.66,5,'','1','','R');
							$this->Cell(31.66,5,'','1','','R');
							
							$this->SetFont('Arial','B','8'); 
							$this->Cell(31.66,5,number_format($arrEarnings_val["empBasic"],2),'1','','R');
							$this->Cell(31.66,5,number_format($arrEarnings_val["sprtAllowAdvance"],2),'1','1','R');
							$this->SetFont('Arial','','8'); 
							//echo "PAY SUMMARY - >".$arrEarnings_val["sprtAllowAdvance"]."<br>";
							$empTotAdvances+=$arrEarnings_val["sprtAllowAdvance"];
							$empTotEmployeeBasic+=$arrEarnings_val["empBasic"];
					}
				}
				
				$this->SetFont('Arial','B','8'); 
				$this->Cell(10,7,'','','','L');
				
				$this->Cell(35,7,'GRAND TOTAL(S)','1','','C');
				$this->Cell(31.66,7,number_format($empTotBasic,2),'1','','R');
				$this->Cell(31.66,7,number_format($empTotLwop,2),'1','','R');
				$this->Cell(31.66,7,number_format($empTotTard,2),'1','','R');
				$this->Cell(31.66,7,number_format($empTotUt,2),'1','','R');
				$this->Cell(31.66,7,number_format($empTotAdjBasic,2),'1','','R');
				
				$this->Cell(31.66,7,number_format($empTotOrgAdvan,2),'1','','R');
				$this->Cell(31.66,7,number_format($empTotAdjAdvan,2),'1','0','R');
			
				$this->Cell(31.66,7,number_format($empTotEmployeeBasic,2),'1','','R');
				$this->Cell(31.66,7,number_format($empTotAdvances,2),'1','1','R');
				//echo $empTotAdvances;
				//$this->Ln();
			}
			
			
		
			$qryEmpAllow = "SELECT     empNo,pdYear, YtdGross, YtdTaxable, YtdGovDed, YtdTax, YtdNonTaxAllow, YtdBasic, sprtAdvance, basicReclass, allowReClass
							FROM         tblYtdDataHist  where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' order by pdYear";
			
			
			$resEmpAllow = $this->execQry($qryEmpAllow);
			$arrEmpAllow_val = $this->getSqlAssoc($resEmpAllow);
			if(count($arrEmpAllow_val)>=1)
			{
				$this->SetFont('Arial','B','8'); 
				$this->Cell(10,5,'','','','L');
				$this->Cell(320,5,'YTD DATA','','1','L');
				$this->Cell(10,5,'','','','L');
				$this->Cell(40,5,'DEC. - 2010','1','','C');
				$this->Cell(145,5,'JAN. - NOV. 2011','1','0','C');
				$this->Cell(70,5,'TOTAL(S)','1','0','C');
				$this->Cell(25,5,'','0','1','C');
				
				
				$this->Cell(10,5,'','','','L');
				$this->Cell(20,5,'ADVAN.','1','','C');
				$this->Cell(20,5,'EMP. BASIC','1','','C');
				
				$this->Cell(23,5,'ADVAN.','1','','C');
				$this->Cell(23,5,'REC. ADVAN.','1','','C');
				$this->Cell(23,5,'TOTAL ADVAN','1','','C');
				
				$this->Cell(23,5,'BASIC','1','','C');
				$this->Cell(23,5,'REC. BASIC','1','','C');
				$this->Cell(30,5,'TOTAL BASIC','1','','C');
				
				$this->Cell(35,5,'TOTAL EMP. BASIC','1','0','C');
				$this->Cell(35,5,'TOTAL EMP. ADVAN.','1','0','C');
				$this->Cell(30,5,'13TH MONTH N. TAX.','1','0','C');
				$this->Cell(30,5,'13TH MONTH TAX.','1','1','C');
				
				
				$this->SetFont('Arial','','8'); 
				$grandYtdAdvances = $grandYtdEmpBasic = 0;
				
				$thMonthTot  = $totYtdAdvances = $totYtdEmpBasic = $thMonthTaxTot  = 0;
				$this->Cell(10,5,'','','','L');
				
				$arrEmpRcDual =  $this->getRcDual($arrEmpAllow_val["empNo"]);
				$this->Cell(20,5,'','1','','R');
				$this->Cell(20,5,number_format($arrEmpRcDual["rcdBasic"],2),'1','','R');
				$this->Cell(23,5,'','1','','R');
				$this->Cell(23,5,'','1','','R');
				$this->Cell(23,5,'','1','','R');
				
				$this->Cell(23,5,number_format($arrEmpAllow_val["YtdBasic"],2),'1','','R');
				$this->Cell(23,5,number_format($arrEmpAllow_val["basicReclass"],2),'1','0','R');
				$totYtdBasic = $arrEmpAllow_val["YtdBasic"]-$arrEmpAllow_val["basicReclass"];
				$this->Cell(30,5,number_format($totYtdBasic,2),'1','0','R');
		
				$grandYtdEmpBasic = $arrEmpRcDual["rcdBasic"] + $totYtdBasic;
				
				$this->Cell(35,5,number_format($grandYtdEmpBasic,2),'1','0','R');
				$this->Cell(35,5,'','1','0','R');
				
				$arrYtdData = $this->gettblYtdData($arrEmpAllow_val["empNo"]);
				$thMonthTot = $arrYtdData["Ytd13NBonus"];
				$thMonthTaxTot = $arrYtdData["YtdTx13NBonus"];
				$this->SetFont('Arial','B','8'); 
				$this->Cell(30,5,number_format($thMonthTot,2),'1','0','R');
				$this->Cell(30,5,number_format($thMonthTaxTot,2),'1','1','R');
				$this->SetFont('Arial','','8'); 
				
				$thMonthTot  = $totYtdAdvances = $totYtdEmpBasic = $thMonthTotAdvances = $thMonthTaxTot =0;
				$this->Cell(10,5,'','','','L');
				
				$this->Cell(20,5,number_format($arrEmpRcDual["rcdAdvances"],2),'1','','R');
				$this->Cell(20,5,'','1','','R');
				$this->Cell(23,5,number_format($arrEmpAllow_val["sprtAdvance"],2),'1','','R');
				$this->Cell(23,5,number_format($arrEmpAllow_val["allowReClass"],2),'1','','R');
				
				$totYtdAdvances = $arrEmpAllow_val["sprtAdvance"]+$arrEmpAllow_val["allowReClass"];
				$this->Cell(23,5,number_format($totYtdAdvances,2),'1','','R');
				
				$this->Cell(23,5,'','1','','R');
				$this->Cell(23,5,'','1','0','R');
				$this->Cell(30,5,'','1','0','R');
		
				$grandYtdAdvances = $arrEmpRcDual["rcdAdvances"] + $totYtdAdvances;
				
				$this->Cell(35,5,0,'1','0','R');
				$this->Cell(35,5,number_format($grandYtdAdvances,2),'1','0','R');
				
				$thMonthTotAdvances = $arrYtdData["YTd13NAdvance"];
				$this->SetFont('Arial','B','8'); 
				$this->Cell(30,5,number_format($thMonthTotAdvances,2),'1','0','R');
				$this->Cell(30,5,'','1','1','R');
				$this->SetFont('Arial','','8'); 

				$this->Ln();
			}
			$this->AddPage();
		}
		
		
		function tblAllowance($empNo)
		{
			$qryAllow = "Select allowAmt from tblAllowance where empNo='".$empNo."' and allowCode='2'";
			$resqryAllow = $this->execQry($qryAllow);
			return $this->getSqlAssoc($resqryAllow);
		}
		
		function displayContentDetails($arrEmpList)
		{
			$this->Ln();
			$this->SetFont('Arial','','10'); 
			$arrdispBranch = $this->dispBranch();
			
			
			$empTestCont = 1;
			foreach($arrdispBranch as $arrdispBranch_val)
			{
				$this->SetFont('Arial','B','10'); 
				$this->Cell(47,5,"BRANCH = ".strtoupper($arrdispBranch_val["brnDesc"]),0,'1','L');
				
				$this->SetFont('Arial','','10'); 
				$empCount = 1;
				foreach($arrEmpList as $arrEmpList_val)
				{
					$empAllowance = 0;
					if($arrEmpList_val["empBrnCode"]==$arrdispBranch_val["brnCode"])
					{
						$empAllowance = $this->tblAllowance($arrEmpList_val["empNo"]);
						$this->SetFont('Arial','B','10'); 
							$this->Cell(47,5,$empCount.". ".$arrEmpList_val["empNo"]."=".$arrEmpList_val["empLastName"].", ".$arrEmpList_val["empFirstName"]." ".$arrEmpList_val["empMidName"][0].". : PAY - GROUP ".$arrEmpList_val["empPayGrp"]." : ".($arrEmpList_val["empPayType"]=='D'?"Daily":"Monthly")." : ".($arrEmpList_val["empPayType"]=='D'?number_format($arrEmpList_val["empDrate"],2):number_format($arrEmpList_val["empMrate"],2))." : Allowance Amt. ".number_format($empAllowance["allowAmt"],2)." : Date Hired : ".date("m/d/Y", strtotime($arrEmpList_val["dateHired"])),'','1','L');
						$this->SetFont('Arial','','10'); 
						/*
						//Display Allowance
						$this->dispAllowance($arrEmpList_val["empNo"]);
						
						//Display MTD
						$this->dispMtdGovt($arrEmpList_val["empNo"]);	*/
						
						//Payroll Summary
						$this->dispPaySum($arrEmpList_val["empNo"]);
						
						if($empTestCont==4)
						{
							$empTestCont = 0;
							$this->AddPage();
						}
						
						
						$empCount++;
						$empTestCont++;
						//Ytd
				//		$this->dispYtd($arrEmpList_val["empNo"]);					
					}
				//
				}//end of EmpList
					//$this->Cell(47,6,'BRANCH - TOTAL(S)','','1','L');
				//$this->AddPage();
				$empTestCont = 1;
			}//end of Branch
			
			
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Arial','',9);



			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
		
	}
	
	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$pdf->company = $migEmpMastObj->getCompanyName($_SESSION['company_code']);
	$pdf->brnCode = $_GET["empBrnCode"];
	//List of New Employees
	
if ($_SESSION['pay_group']==1) {
		$date = '11/18/2011';
	} else {
		$date = '11/23/2011';
	}
	
	
	
	
	//For Active Employee
	$qryListEmp = "Select * from tblEmpMast where compCode='".$_SESSION["company_code"]."' ".($_GET["empBrnCode"]==0?"":"and empBrnCode='".$_GET["empBrnCode"]."'")."
					and empStat in ('RG','CN', 'PR')  AND datehired<='$date' and empPayGrp='".$_SESSION['pay_group']."'  and empPayCat='".$_SESSION["pay_category"]."'
					and empNo not in (010001423,010001425,010001426,010001427,010001428,010001459,010001460,010001461,010001462) 
					order by empLastName";
	
	
	//For Transfer Employees
	/*$qryListEmp = "Select * from tblEmpMast where compCode='".$_SESSION["company_code"]."' 
				   and empNo in (200001838,320000242,320000404,140001638,140000090) order by empLastName";

*/

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



