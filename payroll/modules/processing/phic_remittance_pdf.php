<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$pagRemObj = new inqTSObj();
	$sessionVars = $pagRemObj->getSeesionVars();
	$pagRemObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			$this->SetFont('Arial','B','7'); 
			$this->Cell(75,5,"PhilHealth Remittance form No. 3",'0','0','',0);
			$this->SetFont('Arial','I','7'); 
			$this->Cell(108,5,"Republic of the Philippines",'0','0','C',0);
			$this->SetFont('Arial','U','7'); 
			$this->Cell(15,5,"",'0','0','',0);
			$this->Cell(55,5,"Check Applicable Box",'0','1','',0);
			
			$this->SetFont('Arial','B','8'); 
			$this->Image(PHIC_LOGO, 8, 15 , '30' , '13' , 'JPG', '');
			$this->Cell(30,13,"",'1','0','',0);
			$this->Cell(33,6.5,"Employer Quarterly",'0','0','',0);
			$this->Cell(12,6.5,"",'0','0','',0);
			$this->SetFont('Arial','B','8'); 
			$this->Cell(108,6.5,"PHILIPPINE HEALTH INSURANCE CORPORATION",'0','0','C',0);
			$this->SetFont('Arial','B','6'); 
			$this->Cell(15,6.5,"",'0','0','',0);
			$this->Cell(4.33,6.5,"X",'1','0','C',0);
			$this->Cell(50.67,6.5,"Regular Quarterly Remittance Report",'0','1','',0);
			
			$this->Cell(30,6.5,"",'0','0','',0);
			$this->SetFont('Arial','B','8'); 
			$this->Cell(33,6.5,"Remittance Report",'0','0','',0);
			$this->Cell(120,6.5,"",'0','0','C',0);
			$this->SetFont('Arial','B','6'); 
			$this->Cell(15,6.5,"",'0','0','',0);
			$this->Cell(4.33,6.5,"",'1','0','',0);
			$this->Cell(50.67,6.5,"Addition to previously submitted RF - 1",'0','1','',0);
			$this->Cell(198,6.5,"",'0','0','',0);
			$this->SetFont('Arial','B','6'); 
			$this->Cell(4.33,6.5,"",'1','0','',0);
			$this->Cell(50.67,6.5,"Deduction to previously submitted RF - 1",'0','1','',0);
			$this->Ln(2);
			$this->SetFont('Arial','B','6'); 
			$this->Cell(50,5,"Registered Employer Name : ",'0','0','',0);
			$this->Cell(80,5,$this->compName,'0','0','',0);
			$this->Cell(25,5,"Employer TIN : ",'0','0','',0);
			$this->Cell(43,5,$this->compTin,'0','0','',0);
			if(($this->pdMonth==1) or ($this->pdMonth==2) or ($this->pdMonth==3))
				$this->Cell(4.33,5,"X",'1','0','C',0);
			else
				$this->Cell(4.33,5,"",'1','0','',0);
				
			$this->Cell(35.67,5,"Quarter Ending March",'0','0','',0);
			$this->Cell(15,5,date("Y"),'0','1','',0);
			$this->Cell(50,5,"Complete Mailing Address : ",'0','0','',0);
			$this->Cell(148,5,$this->compAdd1,'0','0','',0);
			if(($this->pdMonth==4) or ($this->pdMonth==5) or ($this->pdMonth==6))
				$this->Cell(4.33,5,"X",'1','0','C',0);
			else
				$this->Cell(4.33,5,"",'1','0','',0);
				
			
			$this->Cell(35.67,5,"Quarter Ending June",'0','0','',0);
			$this->Cell(15,5,date("Y"),'0','1','',0);
			$this->Cell(50,5,"",'0','0','',0);
			$this->Cell(148,5,$this->compAdd2,'0','0','',0);
			
			if(($this->pdMonth==7) or ($this->pdMonth==8) or ($this->pdMonth==9))
				$this->Cell(4.33,5,"X",'1','0','C',0);
			else
				$this->Cell(4.33,5,"",'1','0','',0);
				
			$this->Cell(35.67,5,"Quarter Ending September",'0','0','',0);
			$this->Cell(15,5,date("Y"),'0','1','',0);
			$this->Cell(50,5,"Telephone Numbers : ",'0','0','',0);
			$this->Cell(80,5,$this->compTel,'0','0','',0);
			$this->Cell(25,5,"Employer ID No : ",'0','0','',0);
			$this->Cell(43,5,$this->compPhicNo,'0','0','',0);
			if(($this->pdMonth==10) or ($this->pdMonth==11) or ($this->pdMonth==12))
				$this->Cell(4.33,5,"X",'1','0','C',0);
			else
				$this->Cell(4.33,5,"",'1','0','',0);
				
			$this->Cell(35.67,5,"Quarter Ending December",'0','0','',0);
			$this->Cell(15,5,date("Y"),'0','1','',0);
			$this->SetFont('Arial','B','6'); 
			$this->Ln(2);
			$this->Cell(140.55,4,"",'0','0','C',0);
			$this->Cell(92.46,4,"NHIP Premium Contributions",'1','0','C',0);
			
			$this->Ln(4);
			$this->Cell(140.55,4,"",'0','0','C',0);
			$this->Cell(30.82,4,"1st Month",'1','0','C',0);
			$this->Cell(30.82,4,"2nd Month",'1','0','C',0);
			$this->Cell(30.82,4,"3rd Month",'1','0','C',0);
			$this->Ln(4);
			//Table Header
			
			$this->Cell(37.165,5,"SURNAME",'1','0','C',0);
			$this->Cell(37.165,5,"GIVEN NAME",'1','0','C',0);
			$this->Cell(10,5,"MI",'1','0','C',0);
			$this->Cell(28.11,5,"PhilHealth/SSS ID No.",'1','0','C',0);
			$this->Cell(28.11,5,"Monthly Compensation",'1','0','C',0);
			$this->Cell(15.41,5,"PS",'1','0','C',0);
			$this->Cell(15.41,5,"ES",'1','0','C',0);
			$this->Cell(15.41,5,"PS",'1','0','C',0);
			$this->Cell(15.41,5,"ES",'1','0','C',0);
			$this->Cell(15.41,5,"PS",'1','0','C',0);
			$this->Cell(15.41,5,"ES",'1','0','C',0);
			$this->Cell(20,5,"Remarks",'1','1','C',0);
		}
		
		function getMbc($empCont)
		{
			$qrySssPhic = "Select * from tblSssPhic where phicEmployer='".$empCont."'";
			$resSssPhic = $this->execQry($qrySssPhic);
			$arrSssPhic =  $this->getSqlAssoc($resSssPhic);
			
			return $arrSssPhic["msb"];
		}
		
		function pagRemDetails($arrmtdGovtHist)
		{
			
			$cntRecords = count($arrmtdGovtHist);
			$cnt = 1;
			$emp_cnt = 1;
			$tot_emp_cont = $tot_emr_cont = $grandempcont=$grandemrcont = 0;
			foreach($arrmtdGovtHist as $arrmtdGovtHist_val)
			{
				if($cnt==19)
				{
					$cnt = 1;
					$tot_emp_cont = $tot_emr_cont = 0;
					$this->AddPage();
				}
				
				$empPhicNo = ($arrmtdGovtHist_val["empPhicNo"]!=""?$arrmtdGovtHist_val["empPhicNo"]:$arrmtdGovtHist_val["empSssNo"]);
				
				$this->SetFont('Arial','','6'); 
				$this->Cell(37.165,5,trim(substr($arrmtdGovtHist_val["empLastName"], 0, 22)),'1','0','',0);
				$this->Cell(37.165,5,trim(substr($arrmtdGovtHist_val["empFirstName"], 0, 18)),'1','0','',0);
				$this->Cell(10,5,$arrmtdGovtHist_val["empMidName"][0],'1','0','C',0);
				$this->Cell(28.11,5,$empPhicNo,'1','0','L',0);
				$this->Cell(28.11,5,'','1','0','L',0);
				
				$pdMonth = $this->pdMonth;
				if($arrmtdGovtHist_val["dateHired"]!="")
				{
					$Emp_mDateHired = date("m", strtotime($arrmtdGovtHist_val["dateHired"]));
					$Emp_yDateHired = date("Y", strtotime($arrmtdGovtHist_val["dateHired"]));
					$Emp_DateHired = date("mdy", strtotime($arrmtdGovtHist_val["dateHired"]));
				}
				else
				{
					$Emp_DateHired = $this->Space(6);
				}
				
				if($arrmtdGovtHist_val["dateResigned"]!="")
				{
					$Emp_mDateRes = date("m", strtotime($arrmtdGovtHist_val["dateResigned"]));
					$Emp_yDateRes = date("Y", strtotime($arrmtdGovtHist_val["dateResigned"]));
				}
				
				if(($Emp_mDateHired==sprintf('%02d',$this->pdMonth))&&($Emp_yDateHired==$this->pdYear))
					$empDRes = "NH";
				elseif(($Emp_mDateRes==sprintf('%02d',$this->pdMonth))&&($Emp_yDateRes==$this->pdYear))
					$empDRes = "S";
				elseif($arrmtdGovtHist_val["mtdEarnings"]==0)
					$empDRes = "NE";
				else
					$empDRes = "";
					
				
				switch($pdMonth)
				{
					case ($pdMonth < 4): //1, 2, 3 - first qusrter
						$ctr_tbl_s = 1;
						$ctr_tbl_e = 3;
						break;
					case ($pdMonth < 7): // 4, 5, 6 - second quarter
						$ctr_tbl_s = 4;
						$ctr_tbl_e = 6;
						break;
					case ($pdMonth< 10): // 7, 8, 9 - third quarter
						$ctr_tbl_s = 7;
						$ctr_tbl_e = 9;
						break;
					default: // 10, 11, 12 - fourth quarter
						$ctr_tbl_s = 10;
						$ctr_tbl_e = 12;
						break;
				}
				
				
				$pgEmp = $arrmtdGovtHist_val["phicEmp"];
				$pgEmpLr = $arrmtdGovtHist_val["phicEmplr"];
/*				if (date('Y') == 2012 && date('m') == 2 && in_array($arrmtdGovtHist_val["empNo"],array('010002727','0107000005'))) {				
					if ($arrmtdGovtHist_val["empNo"]=='010002727') {
						$pgEmp 	= number_format($pgEmp-337.50,2);
						$pgEmpLr 	= number_format($pgEmpLr-337.50,2);
					} else {
						$pgEmp 	= number_format($pgEmp-112.50,2);
						$pgEmpLr 	= number_format($pgEmpLr-112.50,2);
					}
				}
*/								
				$tot_emp_cont+=$pgEmp;
				$tot_emr_cont+=$pgEmpLr;
				$grandempcont+=$pgEmp;
				$grandemrcont+=$pgEmpLr;
				
				for($ctr_tbl=$ctr_tbl_s; $ctr_tbl<=$ctr_tbl_e; $ctr_tbl++)
				{
					if($ctr_tbl==$pdMonth)
					{
						$this->Cell(15.41,5,$pgEmp,'1','0','R',0);
						$this->Cell(15.41,5,$pgEmpLr,'1','0','R',0);
					}
					else
					{
						$this->Cell(15.41,5,"",'1','0','C',0);
						$this->Cell(15.41,5,"",'1','0','C',0);
					}
				}
				$this->Cell(20,5,$empDRes,'1','1','C',0);
				
				if($emp_cnt==$cntRecords)
				{
					
					if($cnt<18)
					{
						$rem_cnt_emp = 18-$cnt;
						for($add_line = 1; $add_line<=$rem_cnt_emp; $add_line++)
						{
							$this->Cell(253,5,"",'0','1','C',0);
						}
					}
					$this->Footer_Page($cnt,$tot_emp_cont,$tot_emr_cont,1,$grandempcont,$grandemrcont,$emp_cnt);	
				}
				else
				{
					if($cnt==18)
					{
						$this->Footer_Page($cnt,$tot_emp_cont,$tot_emr_cont,0,$grandempcont,$grandemrcont,'');
					}
				}
				
				$cnt++;
				$emp_cnt++;
				
				unset($empPhicNo);
			}
			
		}
		
		function Footer_Page($cnt,$tot_emp_cont,$tot_emr_cont,$grand_tag,$grandempcont,$grandemrcont,$emp_cnt)
		{
			$this->Ln(2);
			$this->SetFont('Arial','B','7'); 
			$this->Cell(98.55,5,"M-5 SUMMARY OF CONTRIBUTION PAYMENTS",'1','0','L',0);
			$this->Cell(20,5,$cnt,'1','0','C',0);
			$this->Cell(22,5,"Total This Page :",'1','0','L',0);
			$pdMonth = $this->pdMonth;
			switch($pdMonth)
			{
				case ($pdMonth < 4): //1, 2, 3 - first qusrter
					$ctr_tbl_s = 1;
					$ctr_tbl_e = 3;
					break;
				case ($pdMonth < 7): // 4, 5, 6 - second quarter
					$ctr_tbl_s = 4;
					$ctr_tbl_e = 6;
					break;
				case ($pdMonth< 10): // 7, 8, 9 - third quarter
					$ctr_tbl_s = 7;
					$ctr_tbl_e = 9;
					break;
				default: // 10, 11, 12 - fourth quarter
					$ctr_tbl_s = 10;
					$ctr_tbl_e = 12;
					break;
			}
			
			for($ctr_tbl=$ctr_tbl_s; $ctr_tbl<=$ctr_tbl_e; $ctr_tbl++)
			{
				if($ctr_tbl==$pdMonth)
				{
					$this->Cell(15.41,5,number_format($tot_emp_cont,2),'1','0','R',0);
					$this->Cell(15.41,5,number_format($tot_emr_cont,2),'1','0','R',0);
				}
				else
				{
					$this->Cell(15.41,5,'','1','0','R',0);
					$this->Cell(15.41,5,'','1','0','R',0);
				}
			}
			
			$this->Cell(20,5,"Page",'TLR','1','C',0);
			$this->Cell(19.71,4,"QUARTER",'L','0','C',0);
			$this->Cell(19.71,4,"TOTAL",'L','0','C',0);
			$this->Cell(19.71,4,"PBR No.",'L','0','C',0);
			$this->Cell(19.71,4,"",'L','0','C',0);
			$this->Cell(19.71,4,"NO. OF",'L','0','C',0);
			$this->Cell(42,4,"GRAND TOTAL :",'L','0','L',0);
			
			
			for($ctr_tbl=$ctr_tbl_s; $ctr_tbl<=$ctr_tbl_e; $ctr_tbl++)
			{
				if($ctr_tbl==$pdMonth)
				{
					if($grand_tag==1)
					{
						$this->Cell(15.41,5,number_format($grandempcont,2),'1','0','R',0);
						$this->Cell(15.41,5,number_format($grandemrcont,2),'1','0','R',0);
					}
					else
					{
						$this->Cell(15.41,5,"",'1','0','R',0);
						$this->Cell(15.41,5,"",'1','0','R',0);
					}
				}
				else
				{
					$this->Cell(15.41,5,"",'1','0','R',0);
					$this->Cell(15.41,5,"",'1','0','R',0);
				}
			}
			
			$this->Cell(20,4,$this->pageNo(),'LR','1','C',0);
			$this->Cell(19.71,3,"MONTH",'LB','0','C',0);
			$this->Cell(19.71,3,"Contribution",'LB','0','C',0);
			$this->Cell(19.71,3,"OR No.",'LB','0','C',0);
			$this->Cell(19.71,3,"Date",'LB','0','C',0);
			$this->Cell(19.71,3,"EMP",'LB','0','C',0);
			$this->Cell(134.46,3,"Certified Correct :",'L','0','L',0);
			$this->Cell(20,3,"of",'LR','1','C',0);
			$this->Cell(19.71,4,"1ST MONTH",'1','0','C',0);
			$tot_emp_emr = $grandempcont + $grandemrcont;
			
			
			if(($pdMonth==1) or ($pdMonth==4) or ($pdMonth==7) or ($pdMonth==10))
				$this->footer_cont($grand_tag,$tot_emp_emr,$emp_cnt);
			else
				$this->footer_dis_cont();
			
		
			$this->Cell(134.46,4,"",'0','0','L',0);
			$this->Cell(20,4,"{nb}",'LR','1','C',0);
			
			$this->Cell(19.71,4,"2ND MONTH",'1','0','C',0);
			
			if(($pdMonth==2) or ($pdMonth==5) or ($pdMonth==8) or ($pdMonth==11))
				$this->footer_cont($grand_tag,$tot_emp_emr,$emp_cnt);
			else
				$this->footer_dis_cont();
				
			
			$this->SetFont('Arial','U','6'); 
			$this->Cell(114.46,4,$this->printedby." - ".$this->printedby_pos,'0','0','L',0);
			$this->Cell(20,4,date("m/d/Y"),'R','0','C',0);
			$this->Cell(20,4,"",'LR','1','C',0);
			$this->SetFont('Arial','B','7'); 
			$this->Cell(19.71,4,"3RD MONTH",'1','0','C',0);
			
			if(($pdMonth==3) or ($pdMonth==6) or ($pdMonth==9) or ($pdMonth==12))
				$this->footer_cont($grand_tag,$tot_emp_emr,$emp_cnt);
			else
				$this->footer_dis_cont();
			
			$this->SetFont('Arial','','6'); 
			$this->Cell(114.46,4,'Signature Over Printed Name and Designation','B','0','L',0);
			$this->Cell(20,4,'DATE','RB','0','C',0);
			$this->Cell(20,4,"",'LRB','1','C',0);
			
		}
		
		function footer_dis_cont()
		{
				$this->Cell(19.71,4,"",'1','0','C',0);
				$this->Cell(19.71,4,"",'1','0','C',0);
				$this->Cell(19.71,4,"",'1','0','C',0);
				$this->Cell(19.71,4,"",'1','0','C',0);
		}
		
		function footer_cont($grand_tag,$tot_emp_emr,$emp_cnt)
		{
			if($grand_tag==1)
			{
				$this->Cell(19.71,4,number_format($tot_emp_emr, 2),'1','0','C',0);
				$this->Cell(19.71,4,"",'1','0','C',0);
				$this->Cell(19.71,4,date("m/d/Y"),'1','0','C',0);
				$this->Cell(19.71,4,$emp_cnt,'1','0','C',0);
			}
			else
			{
				$this->footer_dis_cont();
			}
		}
	}
	
	$pdf = new PDF('L', 'mm', 'LETTER');
	
	$compCode = $_GET["compCode"];
	$arrcompName = $pagRemObj->getCompany($compCode);
 
			$compSSSNo = $arrcompName["compSssNo"];
			$pdf->compAdd = substr($arrcompName["compAddr1"]." ".$arrcompName["compAddr2"], 0, 53);
			$pdf->compTin = $arrcompName["compTin"];
			$pdf->compTel = substr($arrcompName["compTelNo"], 0, 14);
			$pdf->compName = substr($arrcompName["compName"], 0, 53);
 		$compSSSNo = $arrcompName["compSssNo"];
		$pdf->compAdd = substr($arrcompName["compAddr1"]." ".$arrcompName["compAddr2"], 0, 53);
		$pdf->compTin = $arrcompName["compTin"];
		$pdf->compTel = substr($arrcompName["compTelNo"], 0, 14);
		$pdf->compName = substr($arrcompName["compName"], 0, 53);
 	$pdf->compSssNo = substr(str_replace("-","",$compSSSNo), 0, 10);

	$pdf->compTel = $arrcompName["compTelNo"];
	
	$arrprintedby = $pagRemObj->getUserInfo($_SESSION["company_code"],$arrcompName['compRemSign_EmpNo'],''); 
		$pdf->printedby= $arrprintedby["empLastName"].", ".$arrprintedby['empFirstName']." ".$arrprintedby['empMidName'][0].".";
		
	$arrprintedby_pos = $pagRemObj->getpositionwil(" where divCode='".$arrprintedby["empDiv"]."' and deptCode='".$arrprintedby["empDepCode"]."' and sectCode='".$arrprintedby["empSecCode"]."' and posCode='".$arrprintedby["empPosId"]."'",2);
		$pdf->printedby_pos= trim(substr($arrprintedby_pos["posDesc"], 0, 30));
		
	
	$pdYear = $_GET["pdYear"];
	$pdf->pdYear = $pdYear;
	
	$pdMonth = $_GET["pdMonth"];
	$pdf->pdMonth = $pdMonth;
	
	$location  = ($_SESSION['company_code'] == 4) ? ",'{$_GET['location']}'":"";

		$qrymtdGovtHist = "Select * from view_MTDGovthist where pdYEar=$pdYear and pdMonth=$pdMonth order by empLastName, empFirstName,empMidName";
				
	$resmtdGovtHist = $pagRemObj->execQry($qrymtdGovtHist);
	$arrmtdGovtHist = $pagRemObj->getArrRes($resmtdGovtHist);
	if(count($arrmtdGovtHist)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$pdf->pagRemDetails($arrmtdGovtHist);
	}
	
	
	$pdf->Output();
?>