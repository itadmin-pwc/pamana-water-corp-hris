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
			$this->Image(SSS_HEADER, 10, 5 , '260' , '40' , 'JPG', '');
			$this->SetFont('Courier','B','8'); 
			$this->Ln(13);
			
			$month = $_GET["pdMonth"]."/".date("d")."/".$_GET["pdYear"];
			$this->Cell(1,4,'','0','','L',0);
			for($grid_sss=0; $grid_sss<=12; $grid_sss++)
			{
				$this->Cell(4.09,4,$this->compSssNo[$grid_sss],'1','','C',0);
			}
			$this->SetFont('Courier','B','10'); 
			$this->Cell(146,5,$this->compName,'0','','L',0);
			$this->Cell(30,5,'','0','','L',0);
			$this->Cell(27,5,date("m", strtotime($month))." ".date("Y", strtotime($month)),'0','','C',0);
			$this->Ln(6);
			$this->Cell(1,5,'','0','','R',0);
			$this->Cell(53,5,$this->compTel,'0','','C',0);
			$this->Cell(146,5,$this->cFompAdd,'0','','L',0);
			$this->Ln(16);
			
		}
		
		function pagRemDetails($arrmtdGovtHist)
		{
			$pdMonth = $this->pdMonth;
			$this->SetFont('Courier','','7'); 
			$cntRecords = count($arrmtdGovtHist);
			$cnt = 1;
			$emp_cnt = 1;
			$tot_Sss = $tot_EmpEc = 0;
			
			foreach($arrmtdGovtHist as $arrmtdGovtHist_val)
			{
				$empSssNo = $EmpDResigned = "";
				$EmpShare=$EmpCont=$EmpEc =  0;
				
				if($cnt==21)
				{
					$cnt = 1;
					$tot_Sss = $tot_EmpEc = 0;
					$this->AddPage();
				}
				
				$this->SetFont('Courier','','7'); 
				$empSssNo = str_replace("-", '',$arrmtdGovtHist_val["empSssNo"]);
				$EmpShare = empty($arrmtdGovtHist_val["sssEmp"])?0:$arrmtdGovtHist_val["sssEmp"];
				$EmrShare = empty($arrmtdGovtHist_val["sssEmplr"])?0:$arrmtdGovtHist_val["sssEmplr"];
				$EmpEc    = empty($arrmtdGovtHist_val["ec"])?0:$arrmtdGovtHist_val["ec"];
/*				if (in_array($arrmtdGovtHist_val["empNo"],array('010002727','0107000005')) && $arrmtdGovtHist_val["pdYear"] == 2012 && $arrmtdGovtHist_val["pdMonth"] == 1) {
					if ($arrmtdGovtHist_val["empNo"]=='010002727') {
						$EmpShare = round($EmpShare-500,2);
						$EmrShare = round($EmrShare-1060,2);
						$EmpEc    = number_format($EmpEc-30,2);
					} else {
						$EmpShare = round($EmpShare-333.30,2);
						$EmrShare = round($EmrShare-706.70,2);
						$EmpEc    = number_format($EmpEc-10,2);
					}
				}*/
				
								
				$EmpCont = $EmpShare + $EmrShare;
				$EmpCont = $EmpCont;
				$tot_Sss+=$EmpCont;
				$EmpEc = $EmpEc;
				$tot_EmpEc+=$EmpEc;
				$EmpDResigned = ($arrmtdGovtHist_val["dateResigned"]!=""?date("m d Y", strtotime($arrmtdGovtHist_val["dateResigned"])):"");
				$grand_Sss+=$EmpCont;
				$grand_EmpEc+=$EmpEc;
				$this->SetFont('Courier','','7'); 
				
				$this->Cell(4.1,5,$empSssNo[0],'1','0','',0);
				$this->Cell(4.1,5,$empSssNo[1],'1','0','',0);
				$this->Cell(4.1,5,$empSssNo[2],'1','0','',0);
				$this->Cell(4.1,5,$empSssNo[3],'1','0','',0);
				$this->Cell(4.1,5,$empSssNo[4],'1','0','',0);
				$this->Cell(4.1,5,$empSssNo[5],'1','0','',0);
				$this->Cell(4.1,5,$empSssNo[6],'1','0','',0);
				$this->Cell(4.1,5,$empSssNo[7],'1','0','',0);
				$this->Cell(4.1,5,$empSssNo[8],'1','0','',0);
				$this->Cell(4.1,5,$empSssNo[9],'1','0','',0);
				
				$this->Cell(81,5,substr($emp_cnt.".",0,5).$this->Space(5-strlen($emp_cnt)).trim(substr($arrmtdGovtHist_val["empLastName"], 0, 19)).",".$this->Space(19-strlen($arrmtdGovtHist_val["empLastName"])).trim(substr($arrmtdGovtHist_val["empFirstName"], 0, 19)).$this->Space(19-strlen($arrmtdGovtHist_val["empFirstName"])).$arrmtdGovtHist_val["empMidName"][0].".",'1','0','',0);
				
				if($pdMonth==1 or $pdMonth==4 or $pdMonth==7 or $pdMonth==10) 
				{
					$EmpCont = $this->Space(5-strlen(round($EmpCont,0))).round($EmpCont,0);
					
				}
				elseif($pdMonth==2 or $pdMonth==5 or $pdMonth==8 or $pdMonth==11) 
				{
					$EmpCont = $this->Space(10-strlen(round($EmpCont,0))).round($EmpCont,0);
					$EmpEc = $this->Space(3-strlen(round($EmpEc,0))).round($EmpEc,0);
				}
				else
				{
					$EmpCont = $this->Space(15-strlen(round($EmpCont,0))).round($EmpCont,0);
					$EmpEc = $this->Space(6-strlen(round($EmpEc,0))).round($EmpEc,0);
				}
				
				for($grid=0; $grid<=14; $grid++)
				{
					$this->Cell(4.56,5,$EmpCont[$grid] ,'1','0','',0);						
				}
				
				
				if($pdMonth==1 or $pdMonth==4 or $pdMonth==7 or $pdMonth==10) 
				{
					$EmpEc = $this->Space(3-strlen(round($EmpEc,0))).round($EmpEc,0);
					for($grid=0; $grid<=2; $grid++)
					{
						$this->Cell(4.50,5,$EmpEc[$grid],'1','0','',0);							
					}
					
					for($grid=0; $grid<=5; $grid++)
					{
						$this->Cell(4,5,'','1','0','',0);						
					}
				}
				else
				{
					for($grid=0; $grid<=2; $grid++)
					{
						$this->Cell(4.50,5,'','1','0','',0);	
					}
					
					for($grid=0; $grid<=5; $grid++)
					{
						$this->Cell(4,5,$EmpEc[$grid],'1','0','',0);						
					}
				}
				
				
				
				$this->Cell(32.2,5,$EmpDResigned,'1','1','C',0);
				
				if($emp_cnt==$cntRecords)
				{
					
					if($cnt<20)
					{
						$rem_cnt_emp = 20-$cnt;
						for($add_line = 1; $add_line<=$rem_cnt_emp; $add_line++)
						{
							$this->Cell(4.1,5,'','1','0','',0);
							$this->Cell(4.1,5,'','1','0','',0);
							$this->Cell(4.1,5,'','1','0','',0);
							$this->Cell(4.1,5,'','1','0','',0);
							$this->Cell(4.1,5,'','1','0','',0);
							$this->Cell(4.1,5,'','1','0','',0);
							$this->Cell(4.1,5,'','1','0','',0);
							$this->Cell(4.1,5,'','1','0','',0);
							$this->Cell(4.1,5,'','1','0','',0);
							$this->Cell(4.1,5,'','1','0','',0);
							
							$this->Cell(81,5,'','1','0','',0);
							$this->Cell(23,5,'','1','0','',0);
							$this->Cell(22.60,5,'','1','0','',0);
							$this->Cell(23,5,'','1','0','',0);
							$this->Cell(13.3,5,'','1','0','',0);
							$this->Cell(12,5,'','1','0','',0);
							$this->Cell(12,5,'','1','0','',0);
							$this->Cell(32.2,5,'','1','1','',0);
						}
					}
					$this->Footer_Page(1,$tot_Sss,$tot_EmpEc,$grand_Sss,$grand_EmpEc);	
				}
				else
				{
					if($cnt==20)
					{
						$this->Footer_Page(2,$tot_Sss,$tot_EmpEc,'','');
					}
				}
				
				$cnt++;
				$emp_cnt++;
			}
			
		}
		
		function Footer_Page($ln,$tot_Sss,$tot_EmpEc,$grand_Sss,$grand_EmpEc)
		{
			$this->Ln(0);
			$this->SetFont('Courier','B','7'); 
			$this->Image(SSS_FOOTER, 10, 151 , '260' , '45' , 'JPG', '');
			$this->Cell(122,6,'','1','','R',0);
			$sum_grand = $grand_Sss+$grand_EmpEc;
			$sum_grand = ($grand_Sss!=""?$sum_grand:"");
			
			$pdMonth = $this->pdMonth;
			
			if($pdMonth==1 or $pdMonth==4 or $pdMonth==7 or $pdMonth==10) 
			{
				$tot_Sss = $this->Space(5-strlen(round($tot_Sss,0))).round($tot_Sss,0);
			}
			elseif($pdMonth==2 or $pdMonth==5 or $pdMonth==8 or $pdMonth==11) 
			{
				$tot_Sss = $this->Space(10-strlen(round($tot_Sss,0))).round($tot_Sss,0);
				$tot_EmpEc = $this->Space(3-strlen(round($tot_EmpEc,0))).round($tot_EmpEc,0);
			}
			else
			{
				$tot_Sss = $this->Space(15-strlen(round($tot_Sss,0))).round($tot_Sss,0);
				$tot_EmpEc = $this->Space(6-strlen(round($tot_EmpEc,0))).round($tot_EmpEc,0);
			}
			
			
			
			for($grid=0; $grid<=14; $grid++)
			{
				$this->Cell(4.56,6,$tot_Sss[$grid],'1','0','',0);						
			}
			
			if($pdMonth==1 or $pdMonth==4 or $pdMonth==7 or $pdMonth==10) 
			{
				$tot_EmpEc = $this->Space(3-strlen(round($tot_EmpEc,0))).round($tot_EmpEc,0);
				for($grid=0; $grid<=2; $grid++)
				{
					$this->Cell(4.50,6,$tot_EmpEc[$grid],'1','0','',0);							
				}
				
				for($grid=0; $grid<=5; $grid++)
				{
					$this->Cell(4,6,'','1','0','',0);						
				}
				
				
			}
			else
			{
				for($grid=0; $grid<=2; $grid++)
				{
					$this->Cell(4.50,6,'','1','0','',0);	
				}
				
				for($grid=0; $grid<=5; $grid++)
				{
					$this->Cell(4,6,$tot_EmpEc[$grid],'1','0','',0);						
				}
				
			}
			
			$this->Ln(14);
			
			$this->Cell(187,4,'','0','','R',0);
			$this->SetFont('Courier','B','6'); 
			$this->Cell(40,4,$this->printedby,'0','','C',0);
			$this->SetFont('Courier','B','7'); 
			$this->Cell(31,4,$this->PageNo(),'0','','C',0);
			
			if($pdMonth==1 or $pdMonth==4 or $pdMonth==7 or $pdMonth==10) 
			{
				$lastPage = "{nb}";
				$pageNo = $this->PageNo();
			
				$this->Ln(4);
				
				$this->Cell(38,4,$grand_Sss,'0','','R',0);
				$this->Cell(21,4,$grand_EmpEc,'0','','R',0);
				$this->Cell(23,4,$sum_grand,'0','','R',0);
				
				$this->Ln(4);
				$this->Cell(82,4,'','0','','R',0);
				$this->SetFont('Courier','B','6'); 
				$this->Cell(105,4,'','0','','R',0);
				$this->Cell(23,4,$this->printedby_pos,'0','','R',0);
				$this->Cell(17,4,date("m/d/Y"),'0','','C',0);
				$this->SetFont('Courier','B','7');
				$this->Cell(31,4,'{nb}','0','','C',0);
				
				$this->Ln(4);
				$this->Cell(82,4,'','0','','R',0);
			}
			elseif($pdMonth==2 or $pdMonth==5 or $pdMonth==8 or $pdMonth==11) 
			{
				$this->Ln(4);
				$this->Cell(82,4,'','0','','R',0);
				
				$this->Ln(3);
				$this->Cell(38,4,$grand_Sss,'0','','R',0);
				$this->Cell(21,4,$grand_EmpEc,'0','','R',0);
				$this->Cell(23,4,$sum_grand,'0','','R',0);
				$this->SetFont('Courier','B','6'); 
				$this->Cell(105,4,'','0','','R',0);
				$this->Cell(23,4,$this->printedby_pos,'0','','R',0);
				$this->Cell(16,4,date("m/d/Y"),'0','','C',0);
				$this->SetFont('Courier','B','7');
				$this->Cell(31,4,'{nb}','0','','C',0);
				
				$this->Ln(4);
				$this->Cell(82,4,'','0','','R',0);
			}
			else
			{
				$this->Ln(4);
				$this->Cell(82,4,'','0','','R',0);
				
				$this->Ln(3);
				$this->Cell(82,4,'','0','','R',0);
				$this->SetFont('Courier','B','6'); 
				$this->Cell(105,4,'','0','','R',0);
				$this->Cell(23,4,$this->printedby_pos,'0','','R',0);
				$this->Cell(16,4,date("m/d/Y"),'0','','C',0);
				$this->SetFont('Courier','B','7');
				$this->Cell(31,4,'{nb}','0','','C',0);
				
				$this->Ln(4);
				$this->Cell(38,4,$grand_Sss,'0','','R',0);
				$this->Cell(21,4,$grand_EmpEc,'0','','R',0);
				$this->Cell(23,4,$sum_grand,'0','','R',0);
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
	$pdf->compSssNo = substr(str_replace("-","",$compSSSNo), 0, 10);
	
	$pdYear = $_GET["pdYear"];
	$pdf->pdYear = $pdYear;
	
	$pdMonth = $_GET["pdMonth"];
	$pdf->pdMonth = $pdMonth;
	$location  = ($_SESSION['company_code'] == 4) ? $_GET['location']:"";
	
	$qrymtdGovtHist= "Select tblmtd.empNo, empLastName, empFirstName, empMidName, empSssNo,dateHired, dateResigned,sssEmp, sssEmplr,ec,empTin, empPhicNo,empPagibig,empBday,mtdEarnings, phicEmp, phicEmplr,hdmfEmp,hdmfEmplr,pdYear,pdMonth 
						from tblMtdGovtHist tblmtd,tblEmpMast tblEmp
						where 
						tblmtd.empNo = tblEmp.empNo
						and (tblmtd.compCode='".$_SESSION["company_code"]."' or tblmtd.compCode='21')
						and pdYear='".$pdYear."'
						and pdMonth='".$pdMonth."' and tblmtd.empNo Not IN (SELECT empNo FROM tblNonEmpGov WHERE 	(cat = 'sss'))
						and empBrnCode like '%$location'
						order by empLastName, empFirstName,empMidName";
	
	$resmtdGovtHist = $pagRemObj->execQry($qrymtdGovtHist);
	$arrmtdGovtHist = $pagRemObj->getArrRes($resmtdGovtHist);
	if(count($arrmtdGovtHist)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$arrprintedby = $pagRemObj->getUserInfo($_SESSION["company_code"],$arrcompName['compRemSign_EmpNo'],''); 
		$pdf->printedby= $arrprintedby["empLastName"].", ".$arrprintedby['empFirstName']." ".$arrprintedby['empMidName'][0].".";
		
		$arrprintedby_pos = $pagRemObj->getpositionwil(" where divCode='".$arrprintedby["empDiv"]."' and deptCode='".$arrprintedby["empDepCode"]."' and sectCode='".$arrprintedby["empSecCode"]."' and posCode='".$arrprintedby["empPosId"]."'",2);
		$pdf->printedby_pos= trim(substr($arrprintedby_pos["posDesc"], 0, 18));
		
		$pdf->pagRemDetails($arrmtdGovtHist);
	}
	
	
	$pdf->Output();
?>