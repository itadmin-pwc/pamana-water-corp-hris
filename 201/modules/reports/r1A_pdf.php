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
			
			$this->Image(RA1_HEADER, 25, 5 , '310' , '42' , 'JPG', '');
			$this->SetFont('Arial','B','10'); 
			$this->Ln(16);
			$this->Cell(15,4,"",'0','','L',0);
			$this->Cell(60.5,4,$this->compSssNo,'0','','L',0);
			$this->Cell(192,4,$this->compName,'0','','L',0);
			$this->Cell(57,4,$this->compTin,'0','1','L',0);
			$this->Ln(3);
			$this->Cell(15,4,"",'0','','L',0);
			$this->Cell(277.5,4,$this->compAdd,'0','0','L',0);
			$this->Cell(32,4,$this->compZipCode,'0','1','L',0);
			$this->SetFont('Arial','B','9'); 
			$this->Ln(10.5);
			
		}
				
		
		function r1ADetails($arrsqlEmp)
		{
			$this->SetFont('Arial','B','9'); 
			$cntRecords = count($arrsqlEmp);
			$cnt = 1;
			$emp_cnt = 1;
			
			
			foreach($arrsqlEmp as $arrsqlEmp_val)
			{
				if($cnt==21)
				{
					$cnt = 1;
					$this->AddPage();
				}
				$this->Cell(15,4,"",'0','','L',0);
				$this->Cell(43.5,6,$arrsqlEmp_val["empSssNo"],'1','0','C',0);
				$this->Cell(10,6,$cnt.".",'TB','0','',0);
				$this->Cell(32,6,substr($arrsqlEmp_val["empLastName"],0,14),'TB','0','',0);
				$this->Cell(34.5,6,substr($arrsqlEmp_val["empFirstName"],0,18),'TB','0','',0);
				$this->Cell(8.7,6,$arrsqlEmp_val["empMidName"][0].".",'TB','0','C',0);
				$this->Cell(36.9,6,($arrsqlEmp_val["empBday"]!=""?date('m/d/Y', strtotime($arrsqlEmp_val["empBday"])):""),'1','0','C',0);
				
				$arrprintedby_pos = $this->getpositionwil(" where posCode='".$arrsqlEmp_val["empPosId"]."'",2);

				$this->Cell(51.5,6,substr($arrprintedby_pos["posDesc"],0,23),'1','0','',0);
				$this->Cell(25.3,6,number_format($arrsqlEmp_val["empMrate"],2),'1','0','R',0);
				$this->Cell(35.5,6,($arrsqlEmp_val["dateHired"]!=""?date('m/d/Y', strtotime($arrsqlEmp_val["dateHired"])):""),'1','0','C',0);
				$this->Cell(31.7,6,'','1','1','',0);
				
				if($emp_cnt==$cntRecords)
				{
					
					if($cnt<20)
					{
						$rem_cnt_emp = 20-$cnt;
						$nothing = 1;
						for($add_line = 1; $add_line<=$rem_cnt_emp; $add_line++)
						{
								$this->Cell(15,4,"",'0','','L',0);
								$this->Cell(43.5,6,'','1','0','C',0);
								$this->Cell(10,6,'','B','0','',0);
								if($nothing<=1)
								{
									$this->SetFont('Arial','B','7'); 
									$this->Cell(75.2,6,'NOTHING FOLLOWS','B','0','C',0);
									$this->SetFont('Arial','','7'); 
								}
								else
								{
								$this->Cell(32,6,'','B','0','',0);
								$this->Cell(35,6,'','B','0','',0);
								$this->Cell(8.2,6,'','B','0','C',0);
								$this->SetFont('Arial','','7'); 
								}
								$this->Cell(37,6,'','1','0','C',0);
								
								$this->Cell(51.5,6,'','1','0','',0);
								$this->Cell(25.3,6,'','1','0','R',0);
								$this->Cell(35.5,6,'','1','0','C',0);
								$this->Cell(31.7,6,'','1','1','',0);
							
							$nothing++;
						}
						
					}
					$this->Footer_Page('1',$emp_cnt);	
				}
				else
				{
					if($cnt==20)
					{
						$this->Footer_Page('2',$cnt);
					}
				}
				$cnt++;
				$emp_cnt++;
			}
		}
		
		function Footer_Page($cnt_lp, $emp_cnt)
		{
			$this->Ln(2
			
			);
			
			//$this->Ln(20);
			
			$this->SetFont('Arial','B','9'); 
			$this->Image(RA1_FOOTER, 25, 167 , '310' , '28' , 'JPG', '');
			$this->Cell(15,4,"",'0','','L',0);
			$this->Cell(33,6,'','0','0','',0);
			$this->Cell(10,6,$emp_cnt,'0','1','',0);
			$this->Cell(15,4,"",'0','','L',0);
			$this->Cell(43,4,'','0','0','',0);
			$this->Cell(67,4,substr($this->userbrnch,0,25),'0','1','C',0);
			$this->Ln(2);
			$this->Cell(15,4,"",'0','','L',0);
			$this->Cell(9,4,'','0','0','',0);
			$this->Cell(8,4,$this->PageNo(),'0','0','',0);
			$this->Cell(4,4,'','0','0','',0);
			$this->Cell(10,4,'{nb}','0','1','',0);
			$this->Cell(15,4,"",'0','','L',0);
			$this->Cell(43,4,'','','0','',0);
			$this->SetFont('Arial','B','7'); 
			$this->Cell(44,4,substr($this->userbrnchpos,0,25),'0','0','C',0);
			$this->Cell(18,4,date('m/d/Y'),'0','0','',0);
			$this->SetFont('Arial','B','9'); 
			
		}
		
	}
	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$compCode = $_GET["compCode"];
	$monthfr =  $_GET["monthfr"];
	$monthto =  $_GET["monthto"];
	$payGroup = $_GET['cmbgroup'];
	if($payGroup==3){
		$payGrp=" and empPayGrp='0' or empPayGrp='1' or empPayGrp='2' order by empLastName";	
	}
	else{
		$payGrp=" and empPayGrp='".$payGroup."' order by empLastName"; 	
	}


	$arrcompName = $pagRemObj->getCompany($compCode);
	$pdf->compSssNo = $arrcompName["compSssNo"];
	$pdf->compName = substr($arrcompName["compName"], 0, 53);
	$pdf->compAdd = substr($arrcompName["compAddr1"]." ".$arrcompName["compAddr2"], 0, 53);
	$pdf->compTin = $arrcompName["compTin"];
	$pdf->compZipCode = substr($arrcompName["compZipCode"], 0, 4);
	

	$confaccess=$_SESSION['Confiaccess'];
		if($confaccess == 'N'){
			$confi = "and tblEmpMast.empPayCat ='3'";
		}elseif ($confaccess == 'Y') {
			$confi = "and tblEmpMast.empPayCat ='2'";
		}
		else $confi = '';

	$sqlEmp = "SELECT * FROM  tblEmpMast where  compCode='".$compCode."' and empPayCat<>'0' and empstat<>'RS' and dateHired between '".date("Y-m-d", strtotime($monthfr))."' and '".date("Y-m-d", strtotime($monthto))."'$confi $payGrp ";
	$ressqlEmp = $pagRemObj->execQry($sqlEmp);
	$arrsqlEmp = $pagRemObj->getArrRes($ressqlEmp);
	if(count($arrsqlEmp)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$arrprintedby = $pagRemObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		
		$arrSignatory = $pagRemObj-> getCompanyArt($compCode);
		$pdf->userbrnch = "";//$arrSignatory["compSignatory"];
		$pdf->userbrnchpos = "";//$arrSignatory["compSignTitle"];		
		
//		$arrSignatory = $pagRemObj-> getEmpBranchArt($compCode,$arrprintedby["empBrnCode"]);
//		$pdf->userbrnch = $arrSignatory["brnSignatory"];
//		$pdf->userbrnchpos = $arrSignatory["brnSignTitle"];
		$pdf->printedby= $arrprintedby["empLastName"].", ".$arrprintedby['empFirstName']." ".$arrprintedby['empMidName'][0].".";
		
		/*$arrprintedby_pos = $pagRemObj->getpositionwil(" where divCode='".$arrprintedby["empDiv"]."' and deptCode='".$arrprintedby["empDepCode"]."' and sectCode='".$arrprintedby["empSecCode"]."'",2);
		$pdf->printedby_pos= trim(substr($arrprintedby_pos["posDesc"], 0, 18));
		*/
		$pdf->r1ADetails($arrsqlEmp);
	}
	
	$pdf->Output();
?>