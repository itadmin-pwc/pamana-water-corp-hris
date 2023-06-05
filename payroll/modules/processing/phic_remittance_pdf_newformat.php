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
			$this->SetFont('Courier','B','8'); 
			$this->Image(PHIC_HEADER, 10, 5 , '260' , '50' , 'JPG', '');
			$this->Ln(7);
			$this->Cell(30,3.8,'','0','','',0);
			$this->Cell(5,3.8,$this->compPhicNo[0],'0','','',0);
			$this->Cell(4.5,3.8,$this->compPhicNo[1],'0','','',0);
			$this->Cell(2,3.8,'','0','','',0);
			$this->Cell(5,3.8,$this->compPhicNo[2],'0','','',0);
			$this->Cell(5,3.8,$this->compPhicNo[3],'0','','',0);
			$this->Cell(4,3.8,$this->compPhicNo[4],'0','','',0);
			$this->Cell(5,3.8,$this->compPhicNo[5],'0','','',0);
			$this->Cell(4.5,3.8,$this->compPhicNo[6],'0','','',0);
			$this->Cell(4.5,3.8,$this->compPhicNo[7],'0','','',0);
			$this->Cell(4.5,3.8,$this->compPhicNo[8],'0','','',0);
			$this->Cell(4.5,3.8,$this->compPhicNo[9],'0','','',0);
			$this->Cell(4.5,3.8,$this->compPhicNo[10],'0','','',0);
			$this->Cell(3,3.8,'','0','','',0);
			$this->Cell(4.5,3.8,$this->compPhicNo[11],'0','','',0);
			$this->Cell(70,3.8,'','0','','',0);
			$this->Cell(50,3.8,'','0','','C',0);
			$this->Cell(48,3.8,'','0','','C',0);
			$this->Ln(5);
			$this->Cell(30,3.8,'','1','','',0);
			$this->Cell(5,3.8,$this->compTin[0],'0','','',0);
			$this->Cell(4.5,3.8,$this->compTin[1],'0','','',0);
			$this->Cell(4.5,3.8,$this->compTin[2],'0','','',0);
			$this->Cell(3,3.8,'','0','','',0);
			$this->Cell(5,3.8,$this->compTin[3],'0','','',0);
			$this->Cell(4.5,3.8,$this->compTin[4],'0','','',0);
			$this->Cell(4.5,3.8,$this->compTin[5],'0','','',0);
			$this->Cell(1.5,3.8,'','0','','',0);
			$this->Cell(5,3.8,$this->compTin[6],'0','','',0);
			$this->Cell(4.5,3.8,$this->compTin[7],'0','','',0);
			$this->Cell(4.5,3.8,$this->compTin[8],'0','','',0);
			$this->Cell(1,3.8,'','0','','',0);
			$this->Cell(5,3.8,$this->compTin[9],'0','','',0);
			$this->Cell(4.5,3.8,$this->compTin[10],'0','','',0);
			$this->Cell(4.5,3.8,$this->compTin[11],'0','','',0);
			
			$this->Ln(5);
			$this->Cell(40,3.8,'','0','','',0);
			$this->Cell(70,3.8,$this->compName,'0','','',0);
			$this->Ln(3);
			$this->Cell(40,3.8,'','0','','',0);
			$this->Cell(70,3.8,$this->compAdd1,'0','','',0);
			$this->Ln(4);
			$this->Cell(20,3.8,'','0','','',0);
			$this->Cell(90,3.8,$this->compAdd2,'0','','',0);
			
			$month = $_GET["pdMonth"]."/".date("d")."/".$_GET["pdYear"];
			
			$this->Cell(118,3.8,'','0','','',0);
			$this->Cell(20,3.8,date("F ", strtotime($month)),'0','','',0);
			$this->Cell(5,3.8,'','0','','',0);
			$this->Cell(10,3.8,date("y", strtotime($month)),'0','','',0);
			
			$this->Ln(4);
			$this->Cell(25,3.8,'','0','','',0);
			$this->Cell(45,3.8,$this->compTel,'0','','',0);
			$this->Ln(17);
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
			$tot_emp = 0;
			$tot_emr = 0;
			$grand_emp = 0;
			$grand_emr = 0;
			foreach($arrmtdGovtHist as $arrmtdGovtHist_val)
			{
				if($cnt==16)
				{
					$cnt = 1;
					$tot_emp = 0;
					$tot_emr = 0;
					$this->AddPage();
				}
				
				
					
				$empPhicNo = str_replace("-","", $arrmtdGovtHist_val["empPhicNo"]);
				
				$this->SetFont('Courier','B','7'); 
				$this->Cell(8,6,$emp_cnt.".",'1','0','',0);
				$this->SetFont('Courier','','7'); 
				$this->Cell(35,6,trim(substr($arrmtdGovtHist_val["empLastName"], 0, 22)),'1','0','',0);
				$this->Cell(34.5,6,trim(substr($arrmtdGovtHist_val["empFirstName"], 0, 18)),'1','0','',0);
				$this->Cell(34.5,6,trim(substr($arrmtdGovtHist_val["empMidName"], 0, 15)),'1','0','',0);
				$this->Cell(1.5,6,'','1','0','',0);
				$this->Cell(5,6,$empPhicNo[0],'1','0','C',0);
				$this->Cell(5,6,$empPhicNo[1],'1','0','C',0);
				$this->Cell(2,6,'','1','0','',0);
				
				for($add_grid = 2; $add_grid<=9; $add_grid++)
				{
					$this->Cell(5.1,6,$empPhicNo[$add_grid],'1','0','C',0);
				}
				
				$tot_emp+=$arrmtdGovtHist_val["phicEmp"];
				$tot_emr+=$arrmtdGovtHist_val["phicEmplr"];
				$grand_emp+=$arrmtdGovtHist_val["phicEmp"];
				$grand_emr+=$arrmtdGovtHist_val["phicEmplr"];
			
				$this->Cell(2,6,'','1','0','',0);
				$this->Cell(5,6,$empPhicNo[10],'1','0','',0);
				
				$this->Cell(1.3,6,'','1','0','',0);
				$empMsb = $this->getMbc($arrmtdGovtHist_val["phicEmp"]);
				
				$this->Cell(10.5,6,$empMsb,'1','0','C',0);
				$this->Cell(1.2,6,'','1','0','L',0);
				$this->Cell(20,6,$arrmtdGovtHist_val["phicEmp"],'1','0','R',0);
				$this->Cell(20.9,6,$arrmtdGovtHist_val["phicEmplr"],'1','0','R',0);
				
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
					
				$this->Cell(1.2,6,'','1','0','',0);
				$this->Cell(31.5,6,$empDRes,'1','1','C',0);
				
				
				if($emp_cnt==$cntRecords)
				{
					if($cnt<15)
					{
						$rem_cnt_emp = 15-$cnt;
						for($add_line = 1; $add_line<=$rem_cnt_emp; $add_line++)
						{
							$this->Cell(8,6,'','0','0','',0);
							$this->Cell(35,6,'','0','0','',0);
							$this->Cell(34.5,6,'','0','0','',0);
							$this->Cell(34.5,6,'','0','0','',0);
							$this->Cell(1.5,6,'','0','0','',0);
							$this->Cell(60,6,'','0','0','',0);
							$this->Cell(1.3,6,'','0','0','',0);
							$this->Cell(10.5,6,'','0','0','',0);
							$this->Cell(1.2,6,'','0','0','',0);
							$this->Cell(20,6,'','0','0','',0);
							$this->Cell(20.9,6,'','0','0','',0);
							$this->Cell(1.2,6,'','0','0','',0);
							$this->Cell(31.5,6,'','0','1','',0);
						}
					}
					
					$this->Footer_Page($tot_emp,$tot_emr,$grand_emp,$grand_emr,$emp_cnt);
				}
				else
				{
					if($cnt==15)
					{
						$this->Footer_Page($tot_emp,$tot_emr,'','',$cnt);
					}
				}
				
				
				$cnt++;
				$emp_cnt++;
				unset($empPhicNo,$empMsb,$empDRes,$Emp_mDateHired,$Emp_yDateHired,$Emp_DateHired,$Emp_DateHired,$Emp_mDateRes,$Emp_yDateRes);
			}
		}
		
		function Footer_Page($tot_emp,$tot_emr,$grand_emp,$grand_emr,$emp_cnt)
		{
			$this->Image(PHIC_FOOTER, 10, 170 , '260' , '27' , 'JPG', '');
			$this->SetFont('Courier','B','7'); 
			$this->Ln(25);
			$this->Cell(186,5.5,"",'0','0','',0);
			$this->Cell(20,5.5,$tot_emp,'0','0','R',0);
			$this->Cell(21,5.5,$tot_emr,'0','0','R',0);
			$this->Ln(5);
			$this->Cell(186,2,"",'0','0','',0);
			$this->Cell(42,2,"",'0','0','',0);
			$this->Cell(30,2,$this->printedby,'0','0','',0);
			$this->Ln(2);
			$this->Cell(186,3,"",'0','0','',0);
			$this->Cell(41,3,($tot_emp+$tot_emr),'0','0','R',0);
			$this->Ln(2);
			$this->Cell(186,3,"",'0','0','',0);
			$this->Cell(41,3,'','0','0','R',0);
			$this->Cell(31,3,$this->printedby_pos,'0','0','C',0);
			$this->Ln(2);
			$this->Cell(23,6,"",'0','0','',0);
			$this->Cell(23,6,"",'0','0','',0);
			$this->Cell(27,6,"",'0','0','',0);
			$this->Cell(19,6,date("m/d/Y"),'0','0','',0);
			$this->Cell(20,6,$emp_cnt,'0','0','C',0);
			$this->Cell(74,6,"",'0','0','C',0);
			$this->Cell(20,5.5,$grand_emp,'0','0','R',0);
			$this->Cell(21,5.5,$grand_emr,'0','0','R',0);
			$this->Ln(5);
			$this->Cell(186,5.5,"",'0','0','',0);
			$this->Cell(41,5.5,($grand_emp+$grand_emr),'0','0','R',0);
			$this->Cell(30,2,date("m/d/Y"),'0','0','C',0);
			$this->Ln(5);
			$this->Cell(238,4.5,"",'0','0','',0);
			$this->Cell(7,4.5,$this->PageNo(),'0','0','',0);
			$this->Cell(2,4.5,'','0','0','',0);
			$this->Cell(7,4.5,'{nb}','0','0','',0);
			/*
			$this->Cell(23,6,"GENARRA",'1','0','',0);
			$this->Cell(23,6,"GENARRA",'1','0','',0);
			$this->Cell(27,6,"GENARRA",'1','0','',0);
			*/
		}
	}
	
	$pdf = new PDF('L', 'mm', 'LETTER');
	
	$compCode = $_GET["compCode"];
	$arrcompName = $pagRemObj->getCompany($compCode);
	$pdf->compName = substr($arrcompName["compName"], 0, 53);
	$pdf->compSssNo = substr(str_replace("-","",$arrcompName["compSssNo"]), 0, 10);
	$pdf->compAdd1 = substr($arrcompName["compAddr1"], 0, 20);
	$pdf->compAdd2 = substr($arrcompName["compAddr2"], 0, 35);
	$pdf->compPhicNo = str_replace("-",'',$arrcompName["compPHealth"]);
	$pdf->compTin = str_replace("-",'',$arrcompName["compTin"]);
	$pdf->compTel = $arrcompName["compTelNo"];
	
	$arrprintedby = $pagRemObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
	$pdf->printedby= $arrprintedby["empLastName"].", ".$arrprintedby['empFirstName']." ".$arrprintedby['empMidName'][0].".";
	
	$arrprintedby_pos = $pagRemObj->getpositionwil(" where divCode='".$arrprintedby["empDiv"]."' and deptCode='".$arrprintedby["empDepCode"]."' and sectCode='".$arrprintedby["empSecCode"]."'",2);
	$pdf->printedby_pos= trim(substr($arrprintedby_pos["posDesc"], 0, 18));
	
	
	$pdYear = $_GET["pdYear"];
	$pdf->pdYear = $pdYear;
	
	$pdMonth = $_GET["pdMonth"];
	$pdf->pdMonth = $pdMonth;
	$location  = ($_SESSION['company_code'] == 4) ? ",'{$_GET['location']}'":"";
	$qrymtdGovtHist = "exec sp_RemittanceGovt $pdYear,$pdMonth,$compCode $location";
				
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