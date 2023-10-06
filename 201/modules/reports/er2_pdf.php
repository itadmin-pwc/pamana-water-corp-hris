<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	$comm=new dbHandler();
	$pagRemObj = new inqTSObj();
	$sessionVars = $pagRemObj->getSeesionVars();
	$pagRemObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			$this->Image(ER2_HEADER, 10, 5 , '260' , '38' , 'JPG', '');
			$this->SetFont('Arial','B','9'); 
			$this->Ln(11);
			$this->Cell(60,4,'','0','','L',0);
			$this->Cell(125,4,$this->compName,'0','','L',0);
			$this->Cell(35,4,'','0','','L',0);
			$this->Cell(35,4,$this->compPhicNo,'0','1','L',0);
			
			$this->Ln(2);
			$this->Cell(25,4,'','0','','L',0);
			$this->Cell(112,4,$this->compAdd,'0','0','L',0);
			$this->Cell(40,4,'','0','','L',0);
			$this->Cell(80,4,$this->compEmailAdd,'0','0','L',0);
			
			$this->Ln(16);
			
		}
		
		function prevEmployer($empNo)
		{
			$qryPrevEmplr = "Select * from tblEmployeeDataHistory where empNo='".$empNo."' order by companyName desc";
			$resPrevEmplr = $this->execQry($qryPrevEmplr);
			return $this->getSqlAssoc($resPrevEmplr);
		}
		
		function er2Details($arrEmp)
		{
			$this->SetFont('Arial','','7'); 
			$cntRecords = count($arrEmp);
			$cnt = 1;
			$emp_cnt = 1;
			
			
			foreach($arrEmp as $arrEmp_val)
			{
				if($cnt==28)
				{
					$cnt = 1;
					$this->AddPage();
				}
				
				if($arrEmp_val["empPhicNo"]!="")
				{
					$empPhicNo = $arrEmp_val["empPhicNo"];
				}
				else
				{
					if($arrEmp_val["empSssNo"]!="")
					{
						$empPhicNo = $arrEmp_val["empSssNo"];
					}
				}
				
				$empName = $arrEmp_val["empLastName"].", ".$arrEmp_val["empFirstName"]." ".$arrEmp_val["empMidName"];
				$this->Cell(35.5,5,$empPhicNo,'1','0','C',0);
				$this->Cell(63,5,substr($empName,0,35),'1','0','L',0);
				$arrprintedby_pos = $this->getpositionwil(" where posCode='".$arrEmp_val["empPosId"]."'",2);
				$this->SetFont('Arial','','6'); 		
				$this->Cell(39.3,5,substr($arrprintedby_pos["posDesc"],0,25),'1','0','L',0);
				$this->SetFont('Arial','','7'); 			
				$this->Cell(23.7,5,number_format($arrEmp_val["empMrate"],2),'1','0','R',0);
				$this->Cell(23.5,5,($arrEmp_val["dateHired"]!=""?date('m/d/Y', strtotime($arrEmp_val["dateHired"])):""),'1','0','C',0);
				$this->Cell(23.5,5,'','1','0','C',0);
				$arrEmpPrevEmplr = $this->prevEmployer($arrEmp_val["empNo"]);
				$this->Cell(51.5,5,substr($arrEmpPrevEmplr["companyName"],0,30),'1','1','L',0);
				
				if($emp_cnt==$cntRecords)
				{
					
					if($cnt<27)
					{
						$rem_cnt_emp = 27-$cnt;
						$nothing = 1;
						for($add_line = 1; $add_line<=$rem_cnt_emp; $add_line++)
						{
							$this->Cell(35.5,5,'','1','0','C',0);
							$this->SetFont('Arial','B','7'); 
							if($nothing==1)
								$this->Cell(63,5,'NOTHING FOLLOWS','1','0','C',0);
							else
								$this->Cell(63,5,'','1','0','L',0);
							$this->SetFont('Arial','','7'); 	
							$this->Cell(39,5,'','1','0','L',0);
							$this->Cell(24,5,'','1','0','R',0);
							$this->Cell(23.5,5,'','1','0','C',0);
							$this->Cell(23.5,5,'','1','0','C',0);
							$this->Cell(51.5,5,'','1','1','C',0);
							$nothing++;
						}
					}
					$this->Footer_Page($cnt);	
				}
				else
				{
					if($cnt==27)
					{
						$this->Footer_Page($cnt);
					}
				}
				$cnt++;
				$emp_cnt++;
			}
			$this->SetFont('Arial','','7'); 
		}
		
		function Footer_Page($cnt)
		{
			$this->Ln(4);
			$this->SetFont('Arial','B','7'); 
			$this->Image(ER2_FOOTER, 10, 180 , '260' , '15' , 'JPG', '');
			
			$this->Cell(55,3,'','0','0','C',0);
			$this->Cell(63,3,$cnt,'0','0','C',0);
			$this->Cell(63,3,'','0','0','C',0);
			$this->Cell(75,3,$this->userbrnch,'0','1','C',0);
			$this->Cell(55,3,'','0','0','C',0);
			$this->Cell(63,3,'','0','0','C',0);
			$this->Cell(63,3,'','0','0','C',0);
			$this->Cell(75,3,$this->userbrnchpos,'0','1','C',0);
			$this->Ln(2);
			$this->Cell(137,3,'','0','0','C',0);
			$this->Cell(7,3,$this->PageNo(),'0','0','C',0);
			$this->Cell(4,3,'','0','0','C',0);
			$this->Cell(7,3,'{nb}','0','0','C',0);
			$this->SetFont('Arial','','7'); 
		}
		
	}
	
	$pdf = new PDF('L', 'mm', 'LETTER');
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
	$pdf->compPhicNo = $arrcompName["compPHealth"];
	$pdf->compName = substr($arrcompName["compName"], 0, 53);
	$pdf->compAdd = substr($arrcompName["compAddr1"]." ".$arrcompName["compAddr2"], 0, 53);
	$pdf->compEmailAdd = $arrcompName["compEmailAdd"];
	$pdf->compZipCode = substr($arrcompName["compZipCode"], 0, 4);
	

	$confaccess=$_SESSION['Confiaccess'];
		if($confaccess == 'N'){
			$confi = "and tblEmpMast.empPayCat ='3'";
		}elseif ($confaccess == 'Y') {
			$confi = "and tblEmpMast.empPayCat ='2'";
		}
		else $confi = '';


	$sqlEmp = "SELECT * FROM  tblEmpMast where compCode='".$compCode."' and empPayCat<>'0' and empstat<>'RS' and dateHired between '".date("Y-m-d", strtotime($monthfr))."' and '".date("Y-m-d", strtotime($monthto))."'".$confi.$payGrp;
	$ressqlEmp = $pagRemObj->execQry($sqlEmp);
	$arrsqlEmp = $pagRemObj->getArrRes($ressqlEmp);
	if(count($arrsqlEmp)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$arrprintedby = $pagRemObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		
		$arrSignatory = $pagRemObj-> getCompanyArt($compCode);
		$pdf->userbrnch = "";//$arrSignatory["compSignatory"]; //HR Manager Name
		$pdf->userbrnchpos = "HR Manager";//$arrSignatory["compSignTitle"];		
//		$arrSignatory = $pagRemObj-> getEmpBranchArt($compCode,$arrprintedby["empBrnCode"]);
//		$pdf->userbrnch = $arrSignatory["brnSignatory"];
//		$pdf->userbrnchpos = $arrSignatory["brnSignTitle"];
		$pdf->printedby= $arrprintedby["empLastName"].", ".$arrprintedby['empFirstName']." ".$arrprintedby['empMidName'][0].".";
		
		
		$pdf->er2Details($arrsqlEmp);
	}
	
	$pdf->Output();
?>