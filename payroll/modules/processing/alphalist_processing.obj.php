<?
/*
	Arrange / Modified  :
		-> Date : 10/18/2010 Monday 2:45PM
		-> By : Genarra Arong
*/

class regPayrollProcObj extends commonObj 
{
	
	var $get;//method
	
	var $session;//session variables
	
	/**
	 * pass all the get variables and session variables 
	 * @param string $method
	 * @param array variable  $sessionVars
	 */
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	

	/*DATA RETRIEVE*/
	function getTblData($tbl, $cond, $orderBy, $ouputType)
	{
		$qryTblInfo = "Select * from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond." ".$orderBy."";
		$resTblInfo = $this->execQry($qryTblInfo);
		if($ouputType == 'sqlAssoc')
			return $this->getSqlAssoc($resTblInfo);
		else
			return $this->getArrRes($resTblInfo);
	}
	
	function getTblData_sum($tbl, $cond)
	{
		$qryTblInfo = "Select sum(trnAmountE) as AmountE from ".$tbl." where compCode='".$_SESSION["company_code"]."' ".$cond."";
		$resTblInfo = $this->execQry($qryTblInfo);
		$arrtrnAmountE = $this->getSqlAssoc($resTblInfo);
		return ($arrtrnAmountE["AmountE"]!=0?$arrtrnAmountE["AmountE"]:0);
		
	}
	
	
	public function getEmpList($restag="")
	{
		$qryListEmp = "Select ytdHist.compCode, pdYear, ytdHist.empNo, empLastName, empFirstName, empMidName, empBrnCode, brnRegion, empTin, empTeu, teuAmt, empAddr1, empAddr2, empBday, empWageTag, empPayType, dateHired, dateResigned,empStat, empDrate, empMrate, empHrate, YtdGross, YtdTaxable, YtdGovDed, YtdTax,Ytd13NBonus,
					YTd13NAdvance, YtdTx13NBonus, YtdBasic, sprtAdvance, empPayGrp
					from tblYtdDataHist ytdHist, tblEmpMast empMast, tblTeu, tblBranch brnch
					where ytdHist.compCode='".$_SESSION["company_code"]."' and empMast.compCode='".$_SESSION["company_code"]."' and brnch.compCode='".$_SESSION["company_code"]."'
					and ytdHist.empNo = empMast.empNo 
					and empmast.empTeu = tblTeu.teuCode
					and empMast.empBrnCode=brnCode
					and empPayCat='".$_SESSION["pay_category"]."' and empPayGrp='".$_SESSION["pay_group"]."'";
		
		if($restag!="")			
			$qryListEmp.="and ytdHist.empNo in (Select empNo from tblLastPayEmp)";
		
		if($_GET["empList"]!="")
			$qryListEmp.="and ytdHist.empNo in (".$_GET["empList"].")";

		
		$qryListEmp.= "	order by empLastName";
		
		$resListEmp = $this->execQry($qryListEmp);
		return $arrListEmp = $this->getArrRes($resListEmp);
	}
	
	private function getAnnualTax($taxInc)
	{
		$qrycomputeWithTax = "Select * from tblAnnTax where $taxInc between txLowLimit and txUpLimit";
		$rescomputeWithTax = $this->execQry($qrycomputeWithTax);
		$rowcomputeWithTax = $this->getSqlAssoc($rescomputeWithTax);
		$compTax = ((($taxInc-$rowcomputeWithTax["txLowLimit"])*$rowcomputeWithTax["txAddPcent"])+$rowcomputeWithTax["txFixdAmt"]);
		
		return sprintf("%01.2f", $compTax);
	}
	
	/*END OF DATA RETRIEVE*/
	
	/*RE PROCESS SCRIPT*/
	public function mainAlphareProcess()
	{
		
		$qryDelAlphaDtl = "Delete from alphadtl where empNo in (".$_GET["empList"].")";
		$rsDelAlphaDtl = $this->execQry($qryDelAlphaDtl); 
		
		if(!$rsDelAlphaDtl)
			return false;
		else
			return true;	
	}
	/*END OF RE PROCESS SCRIPT*/
	
	public function mainAlphaProcess()
	{
		$TrnsA = $this->beginTran();
		
		
		$db =& ADONewConnection('access');
		$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".realpath("C:/ALPHALIST.mdb");
		//$dsn="BIR";
		$db->PConnect($dsn,'','');
		
		$iSched[1] = 0;
		$iSched[4] = 0;
		$iSched[5] = 0;
		$iSched[3] = 0;
		
		$ctr_test = $sequence_num	=  0;
		
		//Common Functions 
		$arrCompany = $this->getCompany($_SESSION["company_code"]);
		//Get Common Emp. Info
		$form_type = "1604CF";
		$employer_tin = $arrCompany["compTin"];
		$employer_branch_code = "0000";
		
		$subs_filing = "Y";
		$pres_total_comp =  0;
			
		
			
		//Get list of Employees based on tblYtdDataHist Data
		foreach ((array)$this->getEmpList() as $arrgetEmpList)
		{
			//Set Default of Variables:
			$employment_from	= $employment_to	= date('H:i:s', strtotime(date('m/d/Y')));
				
			//Check if Employee is part of the Last Pay
			$arrLastPay = $this->getTblData('tblLastPayEmp', " and empNo='".$arrgetEmpList["empNo"]."'", '', 'sqlAssoc');

			//Check if the Employee has a Previous Employer Record
			$arrPrevEmployer = $this->getTblData('tblPrevEmployer', " and empNo='".$arrgetEmpList["empNo"]."' and yearCd='2010'", '', 'sqlAssoc');

			//Get Common Emp. Info
			$schedule_num = $schedule_num;
			
			$tin = $arrgetEmpList["empTin"];
			$branch_code = $arrgetEmpList["empBrnCode"]	;
			$last_name	= $arrgetEmpList["empLastName"];
			$first_name	= $arrgetEmpList["empFirstName"];
			$middle_name = $arrgetEmpList["empMidName"];
			
			
			if(strlen($arrgetEmpList["empTeu"]>='3'))
				$exmpn_code = substr($arrgetEmpList["empTeu"],0,1).substr($arrgetEmpList["empTeu"],2,3);
			else
				$exmpn_code = $arrgetEmpList["empTeu"];
			
			
			$exmpn_amt = $arrgetEmpList["teuAmt"];
			$empAddress = $arrgetEmpList["empAddr1"]." ".$arrgetEmpList["empAddr2"];
			$empAddress = str_replace("'","''",stripslashes(strtoupper($empAddress)));
			
			//Get the Schedule
			//Schedule D7.1 : Currently Resigned within the Company
			if((($arrgetEmpList["dateResigned"]!="") or ($arrLastPay["empNo"]!="")) and ($arrgetEmpList["empWageTag"]=='N'))
			{
				$schedule_num = "D7.1";
				$iSched[1] = $iSched[1] + 1;
				$iCtr = $iSched[1];
				
				//Variables
				$employment_from = ($arrgetEmpList["dateHired"]!=""?date("m/d/Y", strtotime($arrgetEmpList["dateHired"])):$employment_from);
				$employment_to = ($arrgetEmpList["dateResigned"]!=""?date("m/d/Y", strtotime($arrgetEmpList["dateResigned"])):$employment_to);
				
				$gross_comp_income = sprintf("%01.2f",$arrgetEmpList["YtdGross"]);
				$total_taxable_comp_income = (($arrgetEmpList["YtdTaxable"]) - ($arrgetEmpList["YtdTx13NBonus"]) - $arrgetEmpList["YtdGovDed"])- ($arrgetEmpList["YtdTx13NBonus"]);
				
				//echo $arrgetEmpList["empNo"]."==>".$arrgetEmpList["YtdTaxable"]."\n";
				
				$total_taxable_comp_income = sprintf("%01.2f",$total_taxable_comp_income);
				$net_taxable_comp_income = $total_taxable_comp_income - $exmpn_amt;
				$net_taxable_comp_income = ($net_taxable_comp_income<0?0:$net_taxable_comp_income);
				$net_taxable_comp_income = sprintf("%01.2f",$net_taxable_comp_income);
				
				

			}
			//Schedule D7.4 : With Previous Employer
			elseif($arrPrevEmployer["empNo"]!="")
			{
				$schedule_num = "D7.4";
				$iSched[4] = $iSched[4] + 1;
				$iCtr = $iSched[4];
				
				//variables
				
				$prev_tax_wthld = sprintf("%01.2f",$arrPrevEmployer["prevTaxes"]);
				$prev_nontax_13th_month = sprintf("%01.2f",$arrPrevEmployer["nonTax13th"]);
				//echo $arrPrevEmployer["empNo"]."=".$prev_nontax_13th_month."\n";
				
				$prev_nontax_sss_gsis_oth_cont = sprintf("%01.2f",$arrPrevEmployer["nonTaxSss"]);
				$prev_total_nontax_comp_income = $prev_nontax_13th_month + $prev_nontax_sss_gsis_oth_cont;
				$prev_total_nontax_comp_income = sprintf("%01.2f",$prev_total_nontax_comp_income);
				$prev_taxable_basic_salary = $arrPrevEmployer["prevEarnings"] - $arrPrevEmployer["nonTaxSss"];
				$prev_taxable_basic_salary = sprintf("%01.2f",$prev_taxable_basic_salary);
				$prev_taxable_13th_month = sprintf("%01.2f",$arrPrevEmployer["tax13th"]);
				$prev_total_taxable = $prev_taxable_basic_salary + $prev_taxable_13th_month ;
				$prev_total_taxable = sprintf("%01.2f",$prev_total_taxable);
				
				$pres_total_nontax_comp_income = ($arrgetEmpList["Ytd13NBonus"]) +  $arrgetEmpList["YtdGovDed"];
				$pres_total_comp =  ($arrgetEmpList["YtdTaxable"]) -  ($arrgetEmpList["YtdTx13NBonus"]) - $arrgetEmpList["YtdGovDed"];
				
				$total_taxable_comp_income = $prev_total_taxable + $pres_total_comp;
				
				$gross_comp_income = $prev_total_nontax_comp_income + $pres_total_nontax_comp_income + $total_taxable_comp_income;
				$gross_comp_income = sprintf("%01.2f",$gross_comp_income);
				
				$net_taxable_comp_income = $total_taxable_comp_income - $exmpn_amt;
				$net_taxable_comp_income = sprintf("%01.2f",$net_taxable_comp_income);
				
				
			}
			//Schedule D7.5 : Minimum Wage Earners
			elseif($arrgetEmpList["empWageTag"]=='Y')
			{
				$schedule_num =  "D7.5";
				$iSched[5] = $iSched[5] + 1;
				$iCtr = $iSched[5];
				
				$employment_from = ($arrgetEmpList["dateHired"]!=""?date("m/d/Y", strtotime($arrgetEmpList["dateHired"])):$employment_from);
				$employment_to = ($arrgetEmpList["dateResigned"]!=""?date("m/d/Y", strtotime($arrgetEmpList["dateResigned"])):'12/31/2010');
				
				//Variables
				$employment_from = ($arrgetEmpList["dateHired"]!=""?date("m/d/Y", strtotime($arrgetEmpList["dateHired"])):date('H:i:s', strtotime(date('m/d/Y'))));
				$employment_to = ($arrgetEmpList["dateResigned"]!=""?date("m/d/Y", strtotime($arrgetEmpList["dateResigned"])):date('H:i:s', strtotime(date('m/d/Y'))));
			
				$pres_nontax_night_diff = $this->getTblData_sum("tblEarningsHist", " and empNo='".$arrgetEmpList["empNo"]."' and pdYear='2010' AND (trnCode IN (0327, 0328, 0329, 0338, 0339))");
				$pres_nontax_holiday_pay = $this->getTblData_sum("tblEarningsHist", " and empNo='".$arrgetEmpList["empNo"]."' and pdYear='2010' AND (trnCode IN (0224, 0225, 0226,  0235, 0236, 0237,  0330, 0331, 0332,  0340, 0341, 0342))");
				$pres_nontax_overtime_pay = $this->getTblData_sum("tblEarningsHist", " and empNo='".$arrgetEmpList["empNo"]."' and pdYear='2010' AND (trnCode IN (0221, 0222, 0223, 0233, 0234))");
				
				
				
				$prev_tax_wthld = sprintf("%01.2f",$arrPrevEmployer["prevTaxes"]);
				$prev_nontax_13th_month = sprintf("%01.2f",$arrPrevEmployer["nonTax13th"]);
				$prev_nontax_sss_gsis_oth_cont = sprintf("%01.2f",$arrPrevEmployer["nonTaxSss"]);
				$prev_total_nontax_comp_income = $prev_nontax_13th_month + $prev_nontax_sss_gsis_oth_cont;
				
				$prev_taxable_13th_month = sprintf("%01.2f",$arrPrevEmployer["tax13th"]);
				$prev_total_taxable = sprintf("%01.2f",$arrPrevEmployer["tax13th"]);
				$net_taxable_comp_income = 0;
				
			}
			else
			{
				$schedule_num = "D7.3";
				$iSched[3] = $iSched[3] + 1;
				$iCtr = $iSched[3];
				
				//Variables
				$gross_comp_income = sprintf("%01.2f",$arrgetEmpList["YtdGross"]);
				$total_taxable_comp_income = ($arrgetEmpList["YtdTaxable"] -  $arrgetEmpList["YtdGovDed"] -  $arrgetEmpList["YtdTx13NBonus"]) + $arrgetEmpList["YtdTx13NBonus"];
				$total_taxable_comp_income = sprintf("%01.2f",$total_taxable_comp_income);
				
				$net_taxable_comp_income = $total_taxable_comp_income - $exmpn_amt;
				$net_taxable_comp_income = ($net_taxable_comp_income<0?0:$net_taxable_comp_income);
				$net_taxable_comp_income = sprintf("%01.2f",$net_taxable_comp_income);
				
				
				
				
			}
			
			$sequence_num = $iCtr;
			
			//Common Variables
			$pres_nontax_13th_month = sprintf("%01.2f",$arrgetEmpList["Ytd13NBonus"]);
			$pres_nontax_de_minimis = $prev_taxable_salaries = $pres_taxable_salaries =  $heath_premium = $prev_nontax_de_minimis = 0;
			$prev_nontax_salaries = $prev_nontax_basic_smw = $prev_nontax_holiday_pay = $prev_nontax_overtime_pay = 0;
			$prev_nontax_night_diff  = $prev_nontax_hazard_pay = $pres_nontax_holiday_pay =$pres_nontax_overtime_pay = $pres_nontax_night_diff = $pres_nontax_hazard_pay = 0;
	
			$pres_nontax_salaries =  sprintf("%01.2f",$arrgetEmpList["YtdempEcola"]);
			
			$pres_nontax_sss_gsis_oth_cont = sprintf("%01.2f",$arrgetEmpList["YtdGovDed"]);
			
			$total_nontax_comp_income = $arrgetEmpList["Ytd13NBonus"] + $arrgetEmpList["YtdGovDed"] + $arrgetEmpList["YtdempEcola"];
			
			$pres_taxable_basic_salary = $arrgetEmpList["YtdTaxable"] - $arrgetEmpList["YtdGovDed"] - $arrgetEmpList["YtdTx13NBonus"];
			$pres_taxable_basic_salary = sprintf("%01.2f",$pres_taxable_basic_salary);
			
			$pres_taxable_13th_month = sprintf("%01.2f",$arrgetEmpList["YtdTx13NBonus"]);
			
			$tax_due = $this->getAnnualTax($net_taxable_comp_income);
			$pres_tax_wthld = sprintf("%01.2f",$arrgetEmpList["YtdTax"]);
			
			
			$over_under = $tax_due - ($pres_tax_wthld + $prev_tax_wthld );
			$amt_wthld_dec = ($over_under>0?$over_under:0);
			$over_wthld = ($over_under>0?0:$over_under*-1);
	
			
			$actual_amt_wthld = ($schedule_num=='D7.5'?0:sprintf("%01.2f",$tax_due));
			
			$prev_nontax_gross_comp_income = ($schedule_num=='D7.5'?sprintf("%01.2f",$arrPrevEmployer["grossNonTax"]):0);
			
			$pres_nontax_basic_smw_day = ($schedule_num=='D7.5'?sprintf("%01.2f",$arrgetEmpList["empDrate"]):0);
			$pres_nontax_basic_smw_month = ($schedule_num=='D7.5'?sprintf("%01.2f",$arrgetEmpList["empMrate"]):0);
			$pres_nontax_basic_smw_year = ($schedule_num=='D7.5'?sprintf("%01.2f",($arrgetEmpList["empMrate"]*12)):0);
			$factor_used = ($schedule_num=='D7.5'?"":26);
			
			$retrn_period = date("Y", strtotime($employment_to))."-12-31";
			
			
			
			if($schedule_num == 'D7.1')
			{
				
				$qryInsAlphadtl.=  "   INSERT INTO alphadtl(empNo,form_type,employer_tin,employer_branch_code,retrn_period,schedule_num,sequence_num,
									registered_name,first_name,last_name,middle_name,tin,branch_code,employment_from,employment_to,atc_code,
									status_code,region_num,subs_filing,exmpn_code,factor_used,actual_amt_wthld,income_payment,pres_taxable_salaries,
									pres_taxable_13th_month,pres_tax_wthld,pres_nontax_salaries,pres_nontax_13th_month,prev_taxable_salaries,
									prev_taxable_13th_month,prev_tax_wthld,prev_nontax_salaries,prev_nontax_13th_month,pres_nontax_sss_gsis_oth_cont,
									prev_nontax_sss_gsis_oth_cont,tax_rate,over_wthld,amt_wthld_dec,exmpn_amt,tax_due,heath_premium,fringe_benefit,
									monetary_value,net_taxable_comp_income,gross_comp_income,prev_nontax_de_minimis,prev_total_nontax_comp_income,
									prev_taxable_basic_salary,pres_nontax_de_minimis,pres_taxable_basic_salary,pres_total_comp,prev_pres_total_taxable,
									pres_total_nontax_comp_income,prev_nontax_gross_comp_income,prev_nontax_basic_smw,prev_nontax_holiday_pay,
									prev_nontax_overtime_pay,prev_nontax_night_diff,prev_nontax_hazard_pay,pres_nontax_gross_comp_income,
									pres_nontax_basic_smw_day,pres_nontax_basic_smw_month,pres_nontax_basic_smw_year,pres_nontax_holiday_pay,
									pres_nontax_overtime_pay,pres_nontax_night_diff,prev_pres_total_comp_income,pres_nontax_hazard_pay,
									total_nontax_comp_income,total_taxable_comp_income,prev_total_taxable,nontax_basic_sal,tax_basic_sal,
									tpclsf,birth_date,address1,address2,child1,
									child2,child3,child4,bday1,bday2,bday3,bday4,other_dep,other_dbday,other_rel)
									VALUES
									('".$arrgetEmpList["empNo"]."','".$form_type."','".str_replace('-','',$employer_tin)."','".$employer_branch_code."','".$retrn_period."','".$schedule_num."','".$sequence_num."',
								   ' ','".$first_name."','".$last_name."','".$middle_name."','".str_replace('-','',$tin)."','".$branch_code."','".$employment_from."','".$employment_to."',' ',
								   ' ',' ','".$subs_filing."','".$exmpn_code."',0,'".$actual_amt_wthld."',0,'".$pres_taxable_salaries."',
								   '".$pres_taxable_13th_month."','".$pres_tax_wthld."','".$pres_nontax_salaries."','".$pres_nontax_13th_month."',0,
								   0,0,0,0,'".$pres_nontax_sss_gsis_oth_cont."',
								   0,0,'".$over_wthld."','".$amt_wthld_dec."','".$exmpn_amt."','".$tax_due."','".$heath_premium."',0,
								   0,'".$net_taxable_comp_income."','".$gross_comp_income."',0,0,
								   0,'".$pres_nontax_de_minimis."','".$pres_taxable_basic_salary."',0,0,
								   0,0,0,0,
								   0,0,0,0,
								   0,0,0,0,
								   0,0,0,0,
								   '".$total_nontax_comp_income."','".$total_taxable_comp_income."',0,0,0,
								   '--','".date("m/d/Y", strtotime($arrgetEmpList["empBday"]))."',
								  '".substr($empAddress,0,30)."','--','--','--','--','--','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."',
								  '".date('H:i:s', strtotime(date('m/d/Y')))."','--','".date('H:i:s', strtotime(date('m/d/Y')))."','--');\n";
			
			}
			elseif($schedule_num == 'D7.3')
			{
				$qryInsAlphadtl.= "INSERT INTO alphadtl(empNo,form_type,employer_tin,employer_branch_code,retrn_period,schedule_num,sequence_num,
									registered_name,first_name,last_name,middle_name,tin,branch_code,employment_from,employment_to,atc_code,
									status_code,region_num,subs_filing,exmpn_code,factor_used,actual_amt_wthld,income_payment,pres_taxable_salaries,
									pres_taxable_13th_month,pres_tax_wthld,pres_nontax_salaries,pres_nontax_13th_month,prev_taxable_salaries,
									prev_taxable_13th_month,prev_tax_wthld,prev_nontax_salaries,prev_nontax_13th_month,pres_nontax_sss_gsis_oth_cont,
									prev_nontax_sss_gsis_oth_cont,tax_rate,over_wthld,amt_wthld_dec,exmpn_amt,tax_due,heath_premium,fringe_benefit,
									monetary_value,net_taxable_comp_income,gross_comp_income,prev_nontax_de_minimis,prev_total_nontax_comp_income,
									prev_taxable_basic_salary,pres_nontax_de_minimis,pres_taxable_basic_salary,pres_total_comp,prev_pres_total_taxable,
									pres_total_nontax_comp_income,prev_nontax_gross_comp_income,prev_nontax_basic_smw,prev_nontax_holiday_pay,
									prev_nontax_overtime_pay,prev_nontax_night_diff,prev_nontax_hazard_pay,pres_nontax_gross_comp_income,
									pres_nontax_basic_smw_day,pres_nontax_basic_smw_month,pres_nontax_basic_smw_year,pres_nontax_holiday_pay,
									pres_nontax_overtime_pay,pres_nontax_night_diff,prev_pres_total_comp_income,pres_nontax_hazard_pay,
									total_nontax_comp_income,total_taxable_comp_income,prev_total_taxable,nontax_basic_sal,tax_basic_sal,
									tpclsf,birth_date,address1,address2,child1,
									child2,child3,child4,bday1,bday2,bday3,bday4,other_dep,other_dbday,other_rel)
									VALUES
									('".$arrgetEmpList["empNo"]."','".$form_type."','".str_replace('-','',$employer_tin)."','".$employer_branch_code."','".$retrn_period."','".$schedule_num."','".$sequence_num."',
								   ' ','".$first_name."','".$last_name."','".$middle_name."','".str_replace('-','',$tin)."','".$branch_code."','".$employment_from."','".$employment_to."',' ',
								   ' ',' ','".$subs_filing."','".$exmpn_code."',0,'".$actual_amt_wthld."',0,'".$pres_taxable_salaries."',
								   '".$pres_taxable_13th_month."','".$pres_tax_wthld."','".$pres_nontax_salaries."','".$pres_nontax_13th_month."',0,
								   0,0,0,0,'".$pres_nontax_sss_gsis_oth_cont."',
								   0,0,'".$over_wthld."','".$amt_wthld_dec."','".$exmpn_amt."','".$tax_due."','".$heath_premium."',0,
								   0,'".$net_taxable_comp_income."','".$gross_comp_income."',0,0,
								   0,'".$pres_nontax_de_minimis."','".$pres_taxable_basic_salary."',0,0,
								   0,0,0,0,
								   0,0,0,0,
								   0,0,0,0,
								   0,0,0,0,
								   '".$total_nontax_comp_income."','".$total_taxable_comp_income."',0,0,0,
								   '--','".date("m/d/Y", strtotime($arrgetEmpList["empBday"]))."',
								  '".substr($empAddress,0,30)."','--','--','--','--','--','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."',
								  '".date('H:i:s', strtotime(date('m/d/Y')))."','--','".date('H:i:s', strtotime(date('m/d/Y')))."','--');\n";
			
			}
			elseif($schedule_num == 'D7.4')
			{
				$qryInsAlphadtl.= "INSERT INTO alphadtl(empNo,form_type,employer_tin,employer_branch_code,retrn_period,schedule_num,sequence_num,
									registered_name,first_name,last_name,middle_name,tin,branch_code,employment_from,employment_to,atc_code,
									status_code,region_num,subs_filing,exmpn_code,factor_used,
								actual_amt_wthld,income_payment,pres_taxable_salaries,pres_taxable_13th_month,pres_tax_wthld,
								pres_nontax_salaries,pres_nontax_13th_month,prev_taxable_salaries,prev_taxable_13th_month,prev_tax_wthld,
								prev_nontax_salaries,prev_nontax_13th_month,pres_nontax_sss_gsis_oth_cont,prev_nontax_sss_gsis_oth_cont,
								tax_rate,over_wthld,amt_wthld_dec,exmpn_amt,tax_due,heath_premium,fringe_benefit,monetary_value,
								net_taxable_comp_income,gross_comp_income,prev_nontax_de_minimis,prev_total_nontax_comp_income,
								prev_taxable_basic_salary,pres_nontax_de_minimis,pres_taxable_basic_salary,pres_total_comp,prev_pres_total_taxable,
								pres_total_nontax_comp_income,prev_nontax_gross_comp_income,prev_nontax_basic_smw,prev_nontax_holiday_pay,prev_nontax_overtime_pay,
								prev_nontax_night_diff,prev_nontax_hazard_pay,pres_nontax_gross_comp_income,pres_nontax_basic_smw_day,pres_nontax_basic_smw_month,
								pres_nontax_basic_smw_year,pres_nontax_holiday_pay,pres_nontax_overtime_pay,pres_nontax_night_diff,prev_pres_total_comp_income,
								
								pres_nontax_hazard_pay,total_nontax_comp_income,total_taxable_comp_income,prev_total_taxable,nontax_basic_sal,tax_basic_sal,
								tpclsf,birth_date,address1,address2,child1,
								child2,child3,child4,bday1,bday2,bday3,bday4,other_dep,other_dbday,other_rel)
								VALUES
							  ('".$arrgetEmpList["empNo"]."','".$form_type."','".str_replace('-','',$employer_tin)."','".$employer_branch_code."','".$retrn_period."','".$schedule_num."','".$sequence_num."',
								   ' ','".$first_name."','".$last_name."','".$middle_name."','".str_replace('-','',$tin)."','".$branch_code."','".$employment_from."','".$employment_to."',' ',
								   ' ',' ','".$subs_filing."','".$exmpn_code."',0,
							   '".$actual_amt_wthld."',0,'".$pres_taxable_salaries."','".$pres_taxable_13th_month."','".$pres_tax_wthld."',
							   '".$pres_nontax_salaries."','".$pres_nontax_13th_month."','".$prev_taxable_salaries."','".$prev_taxable_13th_month."','".$prev_tax_wthld."',
							   '".$prev_nontax_salaries."','".$prev_nontax_13th_month."','".$pres_nontax_sss_gsis_oth_cont."','".$prev_nontax_sss_gsis_oth_cont."',
							   0,'".$over_wthld."','".$amt_wthld_dec."','".$exmpn_amt."','".$tax_due."','".$heath_premium."',0,0,
							   '".$net_taxable_comp_income."','".$gross_comp_income."','".$prev_nontax_de_minimis."','".$prev_total_nontax_comp_income."',
							   '".$prev_taxable_basic_salary."','".$pres_nontax_de_minimis."','".$pres_taxable_basic_salary."','".$pres_total_comp."',0,
							   '".$pres_total_nontax_comp_income."',0,0,0,0,
							   0,0,0,0,0,
							   0,0,0,0,0,
							   0,'".$total_nontax_comp_income."',
							   '".$total_taxable_comp_income."','".$prev_total_taxable."',0,0,
							  '--','".date("m/d/Y", strtotime($arrgetEmpList["empBday"]))."',
							  '".substr($empAddress,0,30)."','--','--','--','--','--','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."',
							  '".date('H:i:s', strtotime(date('m/d/Y')))."','--','".date('H:i:s', strtotime(date('m/d/Y')))."','--');\n";
			
			}
			elseif($schedule_num == 'D7.5')
			{
				$pres_nontax_night_diff = $this->getTblData_sum("tblEarningsHist", " and empNo='".$arrgetEmpList["empNo"]."' and pdYear='2010' AND (trnCode IN (0327, 0328, 0329, 0338, 0339))");
				$pres_nontax_holiday_pay = $this->getTblData_sum("tblEarningsHist", " and empNo='".$arrgetEmpList["empNo"]."' and pdYear='2010' AND (trnCode IN (0224, 0225, 0226,  0235, 0236, 0237,  0330, 0331, 0332,  0340, 0341, 0342))");
				$pres_nontax_overtime_pay = $this->getTblData_sum("tblEarningsHist", " and empNo='".$arrgetEmpList["empNo"]."' and pdYear='2010' AND (trnCode IN (0221, 0222, 0223, 0233, 0234))");
				
				
				
				$nontax_basic_sal = 0;
				
				$pres_nontax_gross_comp_income = $arrgetEmpList["YtdGross"];
				$pres_nontax_gross_comp_income = sprintf("%01.2f",$pres_nontax_gross_comp_income);
				
				
				
				$pres_total_nontax_comp_income = $total_nontax_comp_income + $pres_nontax_night_diff + $pres_nontax_holiday_pay + $pres_nontax_overtime_pay;
				$pres_total_comp = $pres_taxable_basic_salary - $pres_nontax_holiday_pay - $pres_nontax_overtime_pay - $pres_nontax_night_diff;
				$pres_total_comp = sprintf("%01.2f",$pres_total_comp);
				$gross_comp_income = $tax_basic_sal = $pres_total_comp;
				
				
				
				 $qryInsAlphadtl.="INSERT INTO alphadtl_d75_system(empNo,form_type,employer_tin,employer_branch_code,retrn_period,schedule_num,sequence_num,
									registered_name,first_name,last_name,middle_name,tin,branch_code,employment_from,employment_to,atc_code,
									status_code,region_num,subs_filing,exmpn_code,factor_used,
									actual_amt_wthld,income_payment,pres_taxable_salaries,pres_taxable_13th_month,pres_tax_wthld,
									pres_nontax_salaries,pres_nontax_13th_month,prev_taxable_salaries,prev_taxable_13th_month,prev_tax_wthld,
									prev_nontax_salaries,prev_nontax_13th_month,pres_nontax_sss_gsis_oth_cont,prev_nontax_sss_gsis_oth_cont,
									tax_rate,over_wthld,amt_wthld_dec,exmpn_amt,tax_due,heath_premium,fringe_benefit,monetary_value,
									net_taxable_comp_income,gross_comp_income,prev_nontax_de_minimis,prev_total_nontax_comp_income,
									prev_taxable_basic_salary,pres_nontax_de_minimis,pres_taxable_basic_salary,pres_total_comp,prev_pres_total_taxable,
									pres_total_nontax_comp_income,prev_nontax_gross_comp_income,prev_nontax_basic_smw,prev_nontax_holiday_pay,prev_nontax_overtime_pay,
									prev_nontax_night_diff,prev_nontax_hazard_pay,pres_nontax_gross_comp_income,pres_nontax_basic_smw_day,pres_nontax_basic_smw_month,
									pres_nontax_basic_smw_year,pres_nontax_holiday_pay,pres_nontax_overtime_pay,pres_nontax_night_diff,prev_pres_total_comp_income,
									pres_nontax_hazard_pay,total_nontax_comp_income,total_taxable_comp_income,prev_total_taxable,nontax_basic_sal,tax_basic_sal,
									tpclsf,birth_date,address1,address2,child1,
									child2,child3,child4,bday1,bday2,bday3,bday4,other_dep,other_dbday,other_rel)
								VALUES('".$arrgetEmpList["empNo"]."','".$form_type."','".str_replace('-','',$employer_tin)."','".$employer_branch_code."','".$retrn_period."','".$schedule_num."','".$sequence_num."',
								   ' ','".$first_name."','".$last_name."','".$middle_name."','".str_replace('-','',$tin)."','".$branch_code."','".$employment_from."','".$employment_to."',' ',
								    ' ','".$arrgetEmpList["brnRegion"]."','".$subs_filing."','".$exmpn_code."','312',
								  0,0,'".$pres_taxable_salaries."','".$pres_taxable_13th_month."','".$pres_tax_wthld."',
								  '".$pres_nontax_salaries."','".$pres_nontax_13th_month."','".$prev_taxable_salaries."','".$prev_taxable_13th_month."','".$prev_tax_wthld."',
									'".$prev_nontax_salaries."','".$prev_nontax_13th_month."','".$pres_nontax_sss_gsis_oth_cont."','".$prev_nontax_sss_gsis_oth_cont."',
									0,'".$over_wthld."','".$amt_wthld_dec."','".$exmpn_amt."','".$tax_due."','".$heath_premium."',0,0,
									'".$net_taxable_comp_income."','".$gross_comp_income."','".$prev_nontax_de_minimis."','".$prev_total_nontax_comp_income."',
									0,'".$pres_nontax_de_minimis."',0,'".$pres_total_comp."',0,
									'".$pres_total_nontax_comp_income."','".$prev_nontax_gross_comp_income."','".$prev_nontax_basic_smw."','".$prev_nontax_holiday_pay."','".$prev_nontax_overtime_pay."',
									'".$prev_nontax_night_diff."','".$prev_nontax_hazard_pay."','".$pres_nontax_gross_comp_income."','".$pres_nontax_basic_smw_day."','".$pres_nontax_basic_smw_month."',
									'".$pres_nontax_basic_smw_year."','".$pres_nontax_holiday_pay."','".$pres_nontax_overtime_pay."','".$pres_nontax_night_diff."',0,
									'".$pres_nontax_hazard_pay."',0,0,'".$prev_total_taxable."',0,'".$tax_basic_sal."',
									'--','".date("m/d/Y", strtotime($arrgetEmpList["empBday"]))."',
								  '".substr($empAddress,0,30)."','--','--','--','--','--','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."',
								  '".date('H:i:s', strtotime(date('m/d/Y')))."','--','".date('H:i:s', strtotime(date('m/d/Y')))."','--');\n";
			
			}
			else
			{
				//echo "PAPA";
			}
			
			unset($actual_amt_wthld,$income_payment,$pres_taxable_salaries,$pres_taxable_13th_month,$pres_tax_wthld,
			$pres_nontax_salaries,$pres_nontax_13th_month,$prev_taxable_salaries,$prev_taxable_13th_month,
			$prev_tax_wthld,$prev_nontax_salaries,$prev_nontax_13th_month,$pres_nontax_sss_gsis_oth_cont,
			$prev_nontax_sss_gsis_oth_cont,$tax_rate,$over_wthld,$amt_wthld_dec,$exmpn_amt,
			$tax_due,$fringe_benefit,$monetary_value,$net_taxable_comp_income,
			$gross_comp_income,$prev_nontax_de_minimis,$prev_total_nontax_comp_income,$prev_taxable_basic_salary,
			$pres_nontax_de_minimis,$pres_taxable_basic_salary,$prev_pres_total_taxable,
			$pres_total_nontax_comp_income,$prev_nontax_gross_comp_income,$prev_nontax_basic_smw,
			$prev_nontax_holiday_pay,$prev_nontax_overtime_pay,$prev_nontax_night_diff,
			$prev_nontax_hazard_pay,$pres_nontax_gross_comp_income,$pres_nontax_basic_smw_day,
			$pres_nontax_basic_smw_month,$pres_nontax_basic_smw_year,$pres_nontax_holiday_pay,
			$pres_nontax_overtime_pay,$pres_nontax_night_diff,$prev_pres_total_comp_income,
			$total_nontax_comp_income,$total_taxable_comp_income,$prev_total_taxable,$nontax_basic_sal,$tax_basic_sal,$over_under,
			$schedule_num,$empAddress,
			$registered_name,$first_name,$last_name,$middle_name,$tin,
			$branch_code,$atc_code,$status_code,$region_num,
			$exmpn_code,$factor_used);
		}	
		
		
		
		//echo $qryInsAlphadtl;
		$TrnsA = $this->execQry($qryInsAlphadtl); 
		
		if(!$TrnsA){
			$TrnsA = $this->rollbackTran();//rollback regular payroll transaction
			return false;
		}
		else{
			$TrnsA = $this->commitTran();//commit regular payroll transaction
			return true;	
		}
	
		
	}	
	
	public function mainAlphaProcessAlphadtl()
	{
		//Migrate Paradox Data PPCI - RES from Jan - May
		/*$qryAlphadtl = "Select * from alphadtl_d7571_excel where schedule_num='D7.5'";
		$rsqryAlphadtl = $this->execQry($qryAlphadtl);
		$arrgetEmpListarr = $this->getArrRes($rsqryAlphadtl); 
		foreach ($arrgetEmpListarr as $arrgetEmpList)
		{
			$qryInsAlphadtl.= "INSERT INTO alphadtl_d75_paradox(empNo,form_type,employer_tin,employer_branch_code,retrn_period,schedule_num,sequence_num,
									registered_name,first_name,last_name,middle_name,tin,branch_code,employment_from,employment_to,atc_code,
									status_code,region_num,subs_filing,exmpn_code,factor_used,actual_amt_wthld,income_payment,pres_taxable_salaries,
									pres_taxable_13th_month,pres_tax_wthld,pres_nontax_salaries,pres_nontax_13th_month,prev_taxable_salaries,
									prev_taxable_13th_month,prev_tax_wthld,prev_nontax_salaries,prev_nontax_13th_month,pres_nontax_sss_gsis_oth_cont,
									prev_nontax_sss_gsis_oth_cont,tax_rate,over_wthld,amt_wthld_dec,exmpn_amt,tax_due,heath_premium,fringe_benefit,
									monetary_value,net_taxable_comp_income,gross_comp_income,prev_nontax_de_minimis,prev_total_nontax_comp_income,
									prev_taxable_basic_salary,pres_nontax_de_minimis,pres_taxable_basic_salary,pres_total_comp,prev_pres_total_taxable,
									pres_total_nontax_comp_income,prev_nontax_gross_comp_income,prev_nontax_basic_smw,prev_nontax_holiday_pay,
									prev_nontax_overtime_pay,prev_nontax_night_diff,prev_nontax_hazard_pay,pres_nontax_gross_comp_income,
									pres_nontax_basic_smw_day,pres_nontax_basic_smw_month,pres_nontax_basic_smw_year,pres_nontax_holiday_pay,
									pres_nontax_overtime_pay,pres_nontax_night_diff,prev_pres_total_comp_income,pres_nontax_hazard_pay,
									total_nontax_comp_income,total_taxable_comp_income,prev_total_taxable,nontax_basic_sal,tax_basic_sal,
									tpclsf,birth_date,address1,address2,child1,
									child2,child3,child4,bday1,bday2,bday3,bday4,other_dep,other_dbday,other_rel)
									VALUES
									('".$arrgetEmpList['empNo']."','".$arrgetEmpList['form_type']."','".$arrgetEmpList['employer_tin']."','0000',
									'".date('m/d/Y', strtotime($arrgetEmpList['retrn_period']))."','".$arrgetEmpList['schedule_num']."','".$arrgetEmpList['sequence_num']."',
									'".$arrgetEmpList['registered_name']."','".$arrgetEmpList['first_name']."','".$arrgetEmpList['last_name']."',
									'".$arrgetEmpList['middle_name']."','".$arrgetEmpList['tin']."','".$arrgetEmpList['branch_code']."',
									'".date('m/d/Y', strtotime($arrgetEmpList['employment_from']))."','".date('m/d/Y', strtotime($arrgetEmpList['employment_to']))."','".$arrgetEmpList['atc_code']."',
									'".$arrgetEmpList['status_code']."','".$arrgetEmpList['region_num']."','".$arrgetEmpList['subs_filing']."',
									'".$arrgetEmpList['exmpn_code']."','".$arrgetEmpList['factor_used']."','".sprintf("%01.2f",$arrgetEmpList['actual_amt_wthld'])."',
									'".sprintf("%01.2f",$arrgetEmpList['income_payment'])."','".sprintf("%01.2f",$arrgetEmpList['pres_taxable_salaries'])."','".sprintf("%01.2f",$arrgetEmpList['pres_taxable_13th_month'])."',
									'".sprintf("%01.2f",$arrgetEmpList['pres_tax_wthld'])."','".sprintf("%01.2f",$arrgetEmpList['pres_nontax_salaries'])."','".sprintf("%01.2f",$arrgetEmpList['pres_nontax_13th_month'])."',
									'".sprintf("%01.2f",$arrgetEmpList['prev_taxable_salaries'])."','".sprintf("%01.2f",$arrgetEmpList['prev_taxable_13th_month'])."','".sprintf("%01.2f",$arrgetEmpList['prev_tax_wthld'])."',
									'".sprintf("%01.2f",$arrgetEmpList['prev_nontax_salaries'])."','".sprintf("%01.2f",$arrgetEmpList['prev_nontax_13th_month'])."','".sprintf("%01.2f",$arrgetEmpList['pres_nontax_sss_gsis_oth_cont'])."',
									'".sprintf("%01.2f",$arrgetEmpList['prev_nontax_sss_gsis_oth_cont'])."','".$arrgetEmpList['tax_rate']."','".sprintf("%01.2f",$arrgetEmpList['over_wthld'])."',
									'".sprintf("%01.2f",$arrgetEmpList['amt_wthld_dec'])."','".$arrgetEmpList['exmpn_amt']."','".sprintf("%01.2f",$arrgetEmpList['tax_due'])."','".sprintf("%01.2f",$arrgetEmpList['heath_premium'])."',
									'".sprintf("%01.2f",$arrgetEmpList['fringe_benefit'])."','".$arrgetEmpList['monetary_value']."','".sprintf("%01.2f",$arrgetEmpList['net_taxable_comp_income'])."',
									'".sprintf("%01.2f",$arrgetEmpList['gross_comp_income'])."','".sprintf("%01.2f",$arrgetEmpList['prev_nontax_de_minimis'])."','".sprintf("%01.2f",$arrgetEmpList['prev_total_nontax_comp_income'])."',
									'".sprintf("%01.2f",$arrgetEmpList['prev_taxable_basic_salary'])."','".sprintf("%01.2f",$arrgetEmpList['pres_nontax_de_minimis'])."','".sprintf("%01.2f",$arrgetEmpList['pres_taxable_basic_salary'])."',
									'".sprintf("%01.2f",$arrgetEmpList['pres_total_comp'])."','".sprintf("%01.2f",$arrgetEmpList['prev_pres_total_taxable'])."','".sprintf("%01.2f",$arrgetEmpList['pres_total_nontax_comp_income'])."',
									'".sprintf("%01.2f",$arrgetEmpList['prev_nontax_gross_comp_income'])."','".sprintf("%01.2f",$arrgetEmpList['prev_nontax_basic_smw'])."','".sprintf("%01.2f",$arrgetEmpList['prev_nontax_holiday_pay'])."',
									'".sprintf("%01.2f",$arrgetEmpList['prev_nontax_overtime_pay'])."','".sprintf("%01.2f",$arrgetEmpList['prev_nontax_night_diff'])."','".sprintf("%01.2f",$arrgetEmpList['prev_nontax_hazard_pay'])."',
									'".sprintf("%01.2f",$arrgetEmpList['pres_nontax_gross_comp_income'])."','".sprintf("%01.2f",$arrgetEmpList['pres_nontax_basic_smw_day'])."','".sprintf("%01.2f",$arrgetEmpList['pres_nontax_basic_smw_month'])."',
									'".sprintf("%01.2f",$arrgetEmpList['pres_nontax_basic_smw_year'])."','".sprintf("%01.2f",$arrgetEmpList['pres_nontax_holiday_pay'])."','".sprintf("%01.2f",$arrgetEmpList['pres_nontax_overtime_pay'])."',
									'".sprintf("%01.2f",$arrgetEmpList['pres_nontax_night_diff'])."','".sprintf("%01.2f",$arrgetEmpList['prev_pres_total_comp_income'])."','".sprintf("%01.2f",$arrgetEmpList['pres_nontax_hazard_pay'])."',
									'".sprintf("%01.2f",$arrgetEmpList['total_nontax_comp_income'])."','".sprintf("%01.2f",$arrgetEmpList['total_taxable_comp_income'])."','".sprintf("%01.2f",$arrgetEmpList['prev_total_taxable'])."',
									'".sprintf("%01.2f",$arrgetEmpList['nontax_basic_sal'])."','".sprintf("%01.2f",$arrgetEmpList['tax_basic_sal'])."',
									'--','".date('H:i:s', strtotime(date('m/d/Y')))."',
								  '--','--','--','--','--','--','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."','".date('H:i:s', strtotime(date('m/d/Y')))."',
								  '".date('H:i:s', strtotime(date('m/d/Y')))."','--','".date('H:i:s', strtotime(date('m/d/Y')))."','--');\n";			  
		
		}*/
		
		//Migrate alphadtl with the proper sequence num.
		//$qryAlphadtl = "Select * from alphadtl where schedule_num='D7.1' order by last_name, first_name";
		/*$qryAlphadtl = "Select * from alphadtl_d75_combine  order by last_name, first_name";
		$rsqryAlphadtl = $this->execQry($qryAlphadtl);
		$arrgetEmpListarr = $this->getArrRes($rsqryAlphadtl); 
		$sequence_num = 1;
		foreach ($arrgetEmpListarr as $arrgetEmpList)
		{
			$qryInsAlphadtl.= "INSERT INTO alphadtl(empNo, form_type,employer_tin,employer_branch_code,retrn_period,schedule_num,sequence_num,
									registered_name,first_name,last_name,middle_name,tin,branch_code,employment_from,employment_to,atc_code,
									status_code,region_num,subs_filing,exmpn_code,factor_used,actual_amt_wthld,income_payment,pres_taxable_salaries,
									pres_taxable_13th_month,pres_tax_wthld,pres_nontax_salaries,pres_nontax_13th_month,prev_taxable_salaries,
									prev_taxable_13th_month,prev_tax_wthld,prev_nontax_salaries,prev_nontax_13th_month,pres_nontax_sss_gsis_oth_cont,
									prev_nontax_sss_gsis_oth_cont,tax_rate,over_wthld,amt_wthld_dec,exmpn_amt,tax_due,heath_premium,fringe_benefit,
									monetary_value,net_taxable_comp_income,gross_comp_income,prev_nontax_de_minimis,prev_total_nontax_comp_income,
									prev_taxable_basic_salary,pres_nontax_de_minimis,pres_taxable_basic_salary,pres_total_comp,prev_pres_total_taxable,
									pres_total_nontax_comp_income,prev_nontax_gross_comp_income,prev_nontax_basic_smw,prev_nontax_holiday_pay,
									prev_nontax_overtime_pay,prev_nontax_night_diff,prev_nontax_hazard_pay,pres_nontax_gross_comp_income,
									pres_nontax_basic_smw_day,pres_nontax_basic_smw_month,pres_nontax_basic_smw_year,pres_nontax_holiday_pay,
									pres_nontax_overtime_pay,pres_nontax_night_diff,prev_pres_total_comp_income,pres_nontax_hazard_pay,
									total_nontax_comp_income,total_taxable_comp_income,prev_total_taxable,nontax_basic_sal,tax_basic_sal,
									tpclsf,birth_date,address1,address2,child1,
									child2,child3,child4,bday1,bday2,bday3,bday4,other_dep,other_dbday,other_rel)
									VALUES
									('".$arrgetEmpList['empNo']."','".$arrgetEmpList['form_type']."','".$arrgetEmpList['employer_tin']."','".$arrgetEmpList['employer_branch_code']."',
									'".date('m/d/Y', strtotime($arrgetEmpList['retrn_period']))."','".$arrgetEmpList['schedule_num']."','".$sequence_num."',
									'".$arrgetEmpList['registered_name']."','".$arrgetEmpList['first_name']."','".$arrgetEmpList['last_name']."',
									'".$arrgetEmpList['middle_name']."','".$arrgetEmpList['tin']."','".$arrgetEmpList['branch_code']."',
									'".date('m/d/Y', strtotime($arrgetEmpList['employment_from']))."','".date('m/d/Y', strtotime($arrgetEmpList['employment_to']))."','".$arrgetEmpList['atc_code']."',
									'".$arrgetEmpList['status_code']."','".$arrgetEmpList['region_num']."','".$arrgetEmpList['subs_filing']."',
									'".$arrgetEmpList['exmpn_code']."','".$arrgetEmpList['factor_used']."','".$arrgetEmpList['actual_amt_wthld']."',
									'".$arrgetEmpList['income_payment']."','".$arrgetEmpList['pres_taxable_salaries']."','".$arrgetEmpList['pres_taxable_13th_month']."',
									'".$arrgetEmpList['pres_tax_wthld']."','".$arrgetEmpList['pres_nontax_salaries']."','".$arrgetEmpList['pres_nontax_13th_month']."',
									'".$arrgetEmpList['prev_taxable_salaries']."','".$arrgetEmpList['prev_taxable_13th_month']."','".$arrgetEmpList['prev_tax_wthld']."',
									'".$arrgetEmpList['prev_nontax_salaries']."','".$arrgetEmpList['prev_nontax_13th_month']."','".$arrgetEmpList['pres_nontax_sss_gsis_oth_cont']."',
									'".$arrgetEmpList['prev_nontax_sss_gsis_oth_cont']."','".$arrgetEmpList['tax_rate']."','".$arrgetEmpList['over_wthld']."',
									'".$arrgetEmpList['amt_wthld_dec']."','".$arrgetEmpList['exmpn_amt']."','".$arrgetEmpList['tax_due']."','".$arrgetEmpList['heath_premium']."',
									'".$arrgetEmpList['fringe_benefit']."','".$arrgetEmpList['monetary_value']."','".$arrgetEmpList['net_taxable_comp_income']."',
									'".$arrgetEmpList['gross_comp_income']."','".$arrgetEmpList['prev_nontax_de_minimis']."','".$arrgetEmpList['prev_total_nontax_comp_income']."',
									'".$arrgetEmpList['prev_taxable_basic_salary']."','".$arrgetEmpList['pres_nontax_de_minimis']."','".$arrgetEmpList['pres_taxable_basic_salary']."',
									'".$arrgetEmpList['pres_total_comp']."','".$arrgetEmpList['prev_pres_total_taxable']."','".$arrgetEmpList['pres_total_nontax_comp_income']."',
									'".$arrgetEmpList['prev_nontax_gross_comp_income']."','".$arrgetEmpList['prev_nontax_basic_smw']."','".$arrgetEmpList['prev_nontax_holiday_pay']."',
									'".$arrgetEmpList['prev_nontax_overtime_pay']."','".$arrgetEmpList['prev_nontax_night_diff']."','".$arrgetEmpList['prev_nontax_hazard_pay']."',
									'".$arrgetEmpList['pres_nontax_gross_comp_income']."','".$arrgetEmpList['pres_nontax_basic_smw_day']."','".$arrgetEmpList['pres_nontax_basic_smw_month']."',
									'".$arrgetEmpList['pres_nontax_basic_smw_year']."','".$arrgetEmpList['pres_nontax_holiday_pay']."','".$arrgetEmpList['pres_nontax_overtime_pay']."',
									'".$arrgetEmpList['pres_nontax_night_diff']."','".$arrgetEmpList['prev_pres_total_comp_income']."','".$arrgetEmpList['pres_nontax_hazard_pay']."',
									'".$arrgetEmpList['total_nontax_comp_income']."','".$arrgetEmpList['total_taxable_comp_income']."','".$arrgetEmpList['prev_total_taxable']."',
									'".$arrgetEmpList['nontax_basic_sal']."','".$arrgetEmpList['tax_basic_sal']."',
									'".$arrgetEmpList['tpclsf']."','".date('m/d/Y', strtotime($arrgetEmpList['birth_date']))."',
									'".str_replace("'","''",stripslashes($arrgetEmpList['address1']))."','".str_replace("'","''",stripslashes($arrgetEmpList['address2']))."','".$arrgetEmpList['child1']."','".$arrgetEmpList['child2']."',
									'".$arrgetEmpList['child3']."','".$arrgetEmpList['child4']."','".date('m/d/Y', strtotime($arrgetEmpList['bday1']))."','".date('m/d/Y', strtotime($arrgetEmpList['bday2']))."','".date('m/d/Y', strtotime($arrgetEmpList['bday3']))."',
									'".date('m/d/Y', strtotime($arrgetEmpList['bday4']))."','".$arrgetEmpList['other_dep']."','".date('m/d/Y', strtotime($arrgetEmpList['other_dbday']))."','".$arrgetEmpList['other_rel']."');\n";			  
			$sequence_num++;
		}
		
		$TrnsA = $this->execQry($qryInsAlphadtl); 
		*/
		
		
		
		$db =& ADONewConnection('access');
		$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".realpath("C:/ALPHALIST.mdb");
		$db->PConnect($dsn,'','');
		
		$delqryAlphadtl = "DELETE FROM [alphadtl]";
		$rsGetData = $db->Execute($delqryAlphadtl);
		
		$delqryAlphadtl = "DELETE FROM [tp_list]";
		$rsGetData = $db->Execute($delqryAlphadtl);
		
		$qryAlphadtl = "Select * from alphadtl";
		$rsqryAlphadtl = $this->execQry($qryAlphadtl);
		$arrgetEmpListarr = $this->getArrRes($rsqryAlphadtl); 
		foreach ($arrgetEmpListarr as $arrgetEmpList)
		{
			
			$qryInsAlphadtl = "INSERT INTO alphadtl(form_type,employer_tin,employer_branch_code,retrn_period,schedule_num,sequence_num,
									registered_name,first_name,last_name,middle_name,tin,branch_code,employment_from,employment_to,atc_code,
									status_code,region_num,subs_filing,exmpn_code,factor_used,actual_amt_wthld,income_payment,pres_taxable_salaries,
									pres_taxable_13th_month,pres_tax_wthld,pres_nontax_salaries,pres_nontax_13th_month,prev_taxable_salaries,
									prev_taxable_13th_month,prev_tax_wthld,prev_nontax_salaries,prev_nontax_13th_month,pres_nontax_sss_gsis_oth_cont,
									prev_nontax_sss_gsis_oth_cont,tax_rate,over_wthld,amt_wthld_dec,exmpn_amt,tax_due,heath_premium,fringe_benefit,
									monetary_value,net_taxable_comp_income,gross_comp_income,prev_nontax_de_minimis,prev_total_nontax_comp_income,
									prev_taxable_basic_salary,pres_nontax_de_minimis,pres_taxable_basic_salary,pres_total_comp,prev_pres_total_taxable,
									pres_total_nontax_comp_income,prev_nontax_gross_comp_income,prev_nontax_basic_smw,prev_nontax_holiday_pay,
									prev_nontax_overtime_pay,prev_nontax_night_diff,prev_nontax_hazard_pay,pres_nontax_gross_comp_income,
									pres_nontax_basic_smw_day,pres_nontax_basic_smw_month,pres_nontax_basic_smw_year,pres_nontax_holiday_pay,
									pres_nontax_overtime_pay,pres_nontax_night_diff,prev_pres_total_comp_income,pres_nontax_hazard_pay,
									total_nontax_comp_income,total_taxable_comp_income,prev_total_taxable,nontax_basic_sal,tax_basic_sal)
									VALUES
									('".$arrgetEmpList['form_type']."','".$arrgetEmpList['employer_tin']."','0000',
									'".date('m/d/Y', strtotime($arrgetEmpList['retrn_period']))."','".$arrgetEmpList['schedule_num']."','".$arrgetEmpList['sequence_num']."',
									'".$arrgetEmpList['registered_name']."','".str_replace('Ñ', 'N', $arrgetEmpList['first_name'])."','".str_replace('Ñ', 'N', $arrgetEmpList['last_name'])."',
									'".str_replace('Ñ', 'N', $arrgetEmpList['middle_name'])."','".$arrgetEmpList['tin']."','".$arrgetEmpList['branch_code']."',
									'".date('m/d/Y', strtotime($arrgetEmpList['employment_from']))."','".date('m/d/Y', strtotime($arrgetEmpList['employment_to']))."','".$arrgetEmpList['atc_code']."',
									'".$arrgetEmpList['status_code']."','".$arrgetEmpList['region_num']."','".$arrgetEmpList['subs_filing']."',
									'".$arrgetEmpList['exmpn_code']."','".$arrgetEmpList['factor_used']."','".$arrgetEmpList['actual_amt_wthld']."',
									'".$arrgetEmpList['income_payment']."','".$arrgetEmpList['pres_taxable_salaries']."','".$arrgetEmpList['pres_taxable_13th_month']."',
									'".$arrgetEmpList['pres_tax_wthld']."','".$arrgetEmpList['pres_nontax_salaries']."','".$arrgetEmpList['pres_nontax_13th_month']."',
									'".$arrgetEmpList['prev_taxable_salaries']."','".$arrgetEmpList['prev_taxable_13th_month']."','".$arrgetEmpList['prev_tax_wthld']."',
									'".$arrgetEmpList['prev_nontax_salaries']."','".$arrgetEmpList['prev_nontax_13th_month']."','".$arrgetEmpList['pres_nontax_sss_gsis_oth_cont']."',
									'".$arrgetEmpList['prev_nontax_sss_gsis_oth_cont']."','".$arrgetEmpList['tax_rate']."','".$arrgetEmpList['over_wthld']."',
									'".$arrgetEmpList['amt_wthld_dec']."','".$arrgetEmpList['exmpn_amt']."','".$arrgetEmpList['tax_due']."','".$arrgetEmpList['heath_premium']."',
									'".$arrgetEmpList['fringe_benefit']."','".$arrgetEmpList['monetary_value']."','".$arrgetEmpList['net_taxable_comp_income']."',
									'".$arrgetEmpList['gross_comp_income']."','".$arrgetEmpList['prev_nontax_de_minimis']."','".$arrgetEmpList['prev_total_nontax_comp_income']."',
									'".$arrgetEmpList['prev_taxable_basic_salary']."','".$arrgetEmpList['pres_nontax_de_minimis']."','".$arrgetEmpList['pres_taxable_basic_salary']."',
									'".$arrgetEmpList['pres_total_comp']."','".$arrgetEmpList['prev_pres_total_taxable']."','".$arrgetEmpList['pres_total_nontax_comp_income']."',
									'".$arrgetEmpList['prev_nontax_gross_comp_income']."','".$arrgetEmpList['prev_nontax_basic_smw']."','".$arrgetEmpList['prev_nontax_holiday_pay']."',
									'".$arrgetEmpList['prev_nontax_overtime_pay']."','".$arrgetEmpList['prev_nontax_night_diff']."','".$arrgetEmpList['prev_nontax_hazard_pay']."',
									'".$arrgetEmpList['pres_nontax_gross_comp_income']."','".$arrgetEmpList['pres_nontax_basic_smw_day']."','".$arrgetEmpList['pres_nontax_basic_smw_month']."',
									'".$arrgetEmpList['pres_nontax_basic_smw_year']."','".$arrgetEmpList['pres_nontax_holiday_pay']."','".$arrgetEmpList['pres_nontax_overtime_pay']."',
									'".$arrgetEmpList['pres_nontax_night_diff']."','".$arrgetEmpList['prev_pres_total_comp_income']."','".$arrgetEmpList['pres_nontax_hazard_pay']."',
									'".$arrgetEmpList['total_nontax_comp_income']."','".$arrgetEmpList['total_taxable_comp_income']."','".$arrgetEmpList['prev_total_taxable']."',
									'".$arrgetEmpList['nontax_basic_sal']."','".$arrgetEmpList['tax_basic_sal']."');\n";			  
					$rsGetData = $db->Execute($qryInsAlphadtl);
					$qryInstplist ="INSERT INTO tp_list(tin,registered_name,tpclsf,last_name,first_name,middle_name,birth_date,branch_code,address1,address2,child1,
																child2,child3,child4,bday1,bday2,bday3,bday4,other_dep,other_dbday,other_rel)
								  VALUES ('".$arrgetEmpList['tin']."','".$arrgetEmpList['registered_name']."','".$arrgetEmpList['tpclsf']."','".$arrgetEmpList['last_name']."',
									'".$arrgetEmpList['first_name']."','".$arrgetEmpList['middle_name']."','".date('m/d/Y', strtotime($arrgetEmpList['birth_date']))."','".$arrgetEmpList['branch_code']."',
									'".str_replace("'","''",stripslashes($arrgetEmpList['address1']))."','".str_replace("'","''",stripslashes($arrgetEmpList['address2']))."','".$arrgetEmpList['child1']."','".$arrgetEmpList['child2']."',
									'".$arrgetEmpList['child3']."','".$arrgetEmpList['child4']."','".date('m/d/Y', strtotime($arrgetEmpList['bday1']))."','".date('m/d/Y', strtotime($arrgetEmpList['bday2']))."','".date('m/d/Y', strtotime($arrgetEmpList['bday3']))."',
									'".date('m/d/Y', strtotime($arrgetEmpList['bday4']))."','".$arrgetEmpList['other_dep']."','".date('m/d/Y', strtotime($arrgetEmpList['other_dbday']))."','".$arrgetEmpList['other_rel']."');";
					$rsGetData = $db->Execute($qryInstplist);
				
		}
		
	
		
		return true;	
	}
}

?>