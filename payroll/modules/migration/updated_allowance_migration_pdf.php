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
	include("migration.obj.php");
	
	$migAllowObj= new mirationObj();
	$sessionVars = $migAllowObj->getSeesionVars();
	$migAllowObj->validateSessions('','MODULES');
	
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
				
				if($this->repType=='1')	
				{
					$this->Cell(70,5,"Report ID: DIFFEMPALLOW");
					$this->Cell(140,5,'List of New Employees with New Allowance','0','','C');
				}
				elseif($this->repType=='2')	
				{
					$this->Cell(70,5,"Report ID: DIFFEMPALLOW1");
					$this->Cell(140,5,'List of Employees with Allowance But No Information in 201','0','','C');
				}
				elseif($this->repType=='3')	
				{
					$this->Cell(70,5,"Report ID: DIFFEMPALLOW1");
					$this->Cell(140,5,'List of Employees with Updates on their Allowance Information','0','','C');
				}
				$this->Ln();
				
				$this->Cell(335,3,'','0');
				$this->Ln();
				$this->SetFont('Courier','B','9');
				$this->Cell(37,6,'BRANCH NAME',0,'0','L');
				$this->SetFont('Courier','','9');
				$this->Cell(100,6,$this->branchName,0,'1','L');
				$this->SetFont('Courier','B','9');
				if($this->repType=='1')
				{
					$this->Cell(35,6,'EMP. NO.',0,'0','C');
					$this->Cell(60,6,'NAME',0,'0','C');
					$this->Cell(60,6,'ALLOW. CODE',0,'0','C');
					$this->Cell(30,6,'ALLOW. PAY TAG',0,'0','C');
					$this->Cell(30,6,'ALLOW. TAG',0,'0','C');
					$this->Cell(30,6,'ALLOW. START',0,'0','C');
					$this->Cell(30,6,'ALLOW. END',0,'0','C');
					$this->Cell(30,6,'ALLOW. AMOUNT',0,'0','C');
				}
				elseif($this->repType=='2')	
				{
					$this->Cell(35,6,'EMP. NO.',0,'0','C');
					$this->Cell(60,6,'ALLOW. CODE',0,'0','C');
					$this->Cell(30,6,'ALLOW. PAY TAG',0,'0','C');
					$this->Cell(30,6,'ALLOW. TAG',0,'0','C');
					$this->Cell(30,6,'ALLOW. START',0,'0','C');
					$this->Cell(30,6,'ALLOW. END',0,'0','C');
					$this->Cell(30,6,'ALLOW. AMOUNT',0,'0','C');
				}
				
				$this->Ln();
				$this->SetFont('Courier','B','9');
		}	
		
		function EmpAllowanceDtl($empNo, $allowCode)
		{
			$empAllowance = "Select * from tblAllowance where compCode='".$_SESSION["company_code"]."' and empNo='".$empNo."' and allowCode='".$allowCode."'";
			return $this->execQry($empAllowance);
		}
		
		function displayContentDetails($arrEmpList)
		{
			$this->SetFont('Courier','','8');
			if($this->repType==1)
			{
				foreach($arrEmpList as $arrEmpList_val)
				{
					$this->Cell(35,6,$arrEmpList_val["c_empNo"],0,'0','L');
					$this->Cell(60,6,$arrEmpList_val["empLastName"].", ".$arrEmpList_val["empFirstName"][0].".".$arrEmpList_val["empMidName"][0].".",0,'0','L');
					$this->Cell(60,6,$arrEmpList_val["allowDesc"],0,'0','L');
					$this->Cell(30,6,($arrEmpList_val["c_allowPayTag"]=='P'?"PERMANENT":"TEMPORARY"),0,'0','L');
					$this->Cell(30,6,($arrEmpList_val[" c_allowTag"]=='M'?"MONTHLY":($arrEmpList_val[" c_allowTag"]=='D'?"DAILY":"NONE")),0,'0','L');
					$this->Cell(30,6,date("Y-m-d", strtotime($arrEmpList_val["c_allowStart"])),0,'0','L');
					$this->Cell(30,6,($arrEmpList_val["c_allowEnd"]!=""?date("Y-m-d", strtotime($arrEmpList_val["c_allowEnd"])):""),0,'0','L');
					$this->Cell(30,6,number_format($arrEmpList_val["c_allowAmt"], 2),0,'0','R');
					$this->Ln();
				}
			}
			elseif($this->repType==2)
			{
				foreach($arrEmpList as $arrEmpList_val)
				{
					$this->Cell(35,6,$arrEmpList_val["c_empNo"],0,'0','L');
					$this->Cell(60,6,$arrEmpList_val["allowDesc"],0,'0','L');
					$this->Cell(30,6,($arrEmpList_val["c_allowPayTag"]=='P'?"PERMANENT":"TEMPORARY"),0,'0','L');
					$this->Cell(30,6,($arrEmpList_val[" c_allowTag"]=='M'?"MONTHLY":($arrEmpList_val[" c_allowTag"]=='D'?"DAILY":"NONE")),0,'0','L');
					$this->Cell(30,6,date("Y-m-d", strtotime($arrEmpList_val["c_allowStart"])),0,'0','L');
					$this->Cell(30,6,($arrEmpList_val["c_allowEnd"]!=""?date("Y-m-d", strtotime($arrEmpList_val["c_allowEnd"])):""),0,'0','L');
					$this->Cell(30,6,number_format($arrEmpList_val["c_allowAmt"], 2),0,'0','R');
					$this->Ln();
				}
			}
			elseif($this->repType==3)
			{
				//Employees with New Set Up of Allowance and with record to tblAllowance
				$this->Cell(335,6,'LIST OF EMPLOYEES WITH NEW SET UP OF ALLOWANCE',0,'1','L');
				$this->Cell(35,6,'EMP. NO.',0,'0','C');
				$this->Cell(60,6,'NAME',0,'0','C');
				$this->Cell(60,6,'ALLOW. CODE',0,'0','C');
				$this->Cell(30,6,'ALLOW. PAY TAG',0,'0','C');
				$this->Cell(30,6,'ALLOW. TAG',0,'0','C');
				$this->Cell(30,6,'ALLOW. START',0,'0','C');
				$this->Cell(30,6,'ALLOW. END',0,'0','C');
				$this->Cell(30,6,'ALLOW. AMOUNT',0,'1','C');
				
				foreach($arrEmpList as $arrEmpList_val)
				{
					$rsEmpAllow = $this->EmpAllowanceDtl($arrEmpList_val["c_empNo"], $arrEmpList_val["c_allowCode"]);
					if($this->getRecCount($rsEmpAllow)>=1)
					{
						
					}
					else
					{
						$this->Cell(35,6,$arrEmpList_val["c_empNo"],0,'0','L');
						$this->Cell(60,6,$arrEmpList_val["empLastName"].", ".$arrEmpList_val["empFirstName"][0].".".$arrEmpList_val["empMidName"][0].".",0,'0','L');
						$this->Cell(60,6,$arrEmpList_val["allowDesc"],0,'0','L');
						$this->Cell(30,6,($arrEmpList_val["c_allowPayTag"]=='P'?"PERMANENT":"TEMPORARY"),0,'0','L');
						$this->Cell(30,6,($arrEmpList_val[" c_allowTag"]=='M'?"MONTHLY":($arrEmpList_val[" c_allowTag"]=='D'?"DAILY":"NONE")),0,'0','L');
						$this->Cell(30,6,date("Y-m-d", strtotime($arrEmpList_val["c_allowStart"])),0,'0','L');
						$this->Cell(30,6,($arrEmpList_val["c_allowEnd"]!=""?date("Y-m-d", strtotime($arrEmpList_val["c_allowEnd"])):""),0,'0','L');
						$this->Cell(30,6,number_format($arrEmpList_val["c_allowAmt"], 2),0,'1','R');
						
					}
					
				}
				
				//Employees with Allowance Data Modification
				$this->Ln();
				$this->Cell(335,6,'LIST OF EMPLOYEES WITH UPDATES ON THEIR ALLOWANCE INFORMATION',0,'1','L');
				$this->Cell(35,6,'EMP. NO.',0,'0','C');
				$this->Cell(40,6,'NAME',0,'0','C');
				$this->Cell(40,6,'ALLOW. CODE',0,'0','C');
				$this->Cell(40,6,'ALLOW. PAY TAG',0,'0','C');
				$this->Cell(30,6,'ALLOW. TAG',0,'0','C');
				$this->Cell(50,6,'ALLOW. START',0,'0','C');
				$this->Cell(50,6,'ALLOW. END',0,'0','C');
				$this->Cell(50,6,'ALLOW. AMOUNT',0,'1','C');
				$this->Cell(35,6,'',0,'0','C');
				$this->Cell(40,6,'',0,'0','C');
				$this->Cell(40,6,'',0,'0','C');
				$this->Cell(20,6,'OLD',0,'0','C');
				$this->Cell(20,6,'NEW',0,'0','C');
				$this->Cell(15,6,'OLD',0,'0','C');
				$this->Cell(15,6,'NEW',0,'0','C');
				$this->Cell(25,6,'OLD',0,'0','C');
				$this->Cell(25,6,'NEW',0,'0','C');
				$this->Cell(25,6,'OLD',0,'0','C');
				$this->Cell(25,6,'NEW',0,'0','C');
				$this->Cell(25,6,'OLD',0,'0','C');
				$this->Cell(25,6,'NEW',0,'1','C');
				
				
				
				foreach($arrEmpList as $arrEmpList_val)
				{
					$rsEmpAllow = $this->EmpAllowanceDtl($arrEmpList_val["c_empNo"], $arrEmpList_val["c_allowCode"]);
					if($this->getRecCount($rsEmpAllow)>=1)
					{
						$arrEmpAllow = $this->getSqlAssoc($rsEmpAllow);
						//if($arrEmpList_val["c_allowEnd"]!=$arrEmpAllow["allowEnd"])
						if
						(
							($arrEmpList_val["c_allowAmt"]!=$arrEmpAllow["allowAmt"])||
							($arrEmpList_val["c_allowSked"]!=$arrEmpAllow["allowSked"])||
							($arrEmpList_val["c_allowPayTag"]!=$arrEmpAllow["allowPayTag"])||
							($arrEmpList_val["c_allowStart"]!=$arrEmpAllow["allowStart"])||
							($arrEmpList_val["c_allowEnd"]!=$arrEmpAllow["allowEnd"])||
							($arrEmpList_val["c_allowTag"]!=$arrEmpAllow["allowTag"])
						)
						{
							$this->Cell(35,6,$arrEmpList_val["c_empNo"],0,'0','L');
							$this->Cell(40,6,$arrEmpList_val["empLastName"].", ".$arrEmpList_val["empFirstName"][0].".".$arrEmpList_val["empMidName"][0].".",0,'0','L');
							$this->Cell(40,6,$arrEmpList_val["allowDesc"],0,'0','L');
							$this->Cell(20,6,$arrEmpAllow["allowPayTag"],0,'0','C');
							$this->Cell(20,6,$arrEmpList_val["c_allowPayTag"],0,'0','C');
							$this->Cell(15,6,$arrEmpAllow["allowTag"],0,'0','C');
							$this->Cell(15,6,$arrEmpList_val["c_allowTag"],0,'0','C');
							$this->Cell(25,6,($arrEmpAllow["allowStart"]!=""?date("Y-m-d", strtotime($arrEmpAllow["allowStart"])):""),0,'0','C');
							$this->Cell(25,6,($arrEmpList_val["c_allowStart"]!=""?date("Y-m-d", strtotime($arrEmpList_val["c_allowStart"])):""),0,'0','C');
							$this->Cell(25,6,($arrEmpAllow["allowEnd"]!=""?date("Y-m-d", strtotime($arrEmpAllow["allowEnd"])):""),0,'0','C');
							$this->Cell(25,6,($arrEmpList_val["c_allowEnd"]!=""?date("Y-m-d", strtotime($arrEmpList_val["c_allowEnd"])):""),0,'0','C');
							$this->Cell(25,6,number_format($arrEmpAllow["allowAmt"],2),0,'0','R');
							$this->Cell(25,6,number_format($arrEmpList_val["c_allowAmt"],2),0,'1','R');
						}
						
					}
					
					
				}
			}
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
	$pdf->company = $migAllowObj->getCompanyName($_SESSION['company_code']);
	$arr_branch = $migAllowObj->getEmpBranchArt($_SESSION["company_code"],$_GET["empBrnCode"]);
	$pdf->branchName = $arr_branch["brnDesc"];
	
	//Employees with New Allowance and no record in tblAllowance
	$qryNotIntblAllow = "Select txAllowP.compCode as c_compCode, txAllowP.empNo as c_empNo, empLastName, empFirstName, empMidName,
						txAllowP.allowCode as c_allowCode,allowDesc,
						txAllowP.allowAmt as c_allowAmt,
						txAllowP.allowPayTag as c_allowPayTag,
						txAllowP.allowStart as c_allowStart,
						txAllowP.allowEnd as c_allowEnd,
						txAllowP.allowTag as c_allowTag
						from tblAllowance_Paradox txAllowP,
						tblEmpMast emp, tblAllowType as allowType
						where txAllowP.empNo=emp.empNo and 
						txAllowP.compCode='".$_SESSION["company_code"]."' 
						and allowType.compCode='".$_SESSION["company_code"]."' and txAllowP.allowCode=allowType.allowCode
						and empBrnCode='".$_GET["empBrnCode"]."' and
						txAllowP.empNo not in (Select empNo from tblAllowance where compCode='".$_SESSION["company_code"]."')";
	$resNotIntblAllow = $migAllowObj->execQry($qryNotIntblAllow);
	$arrNotIntblAllow = $migAllowObj->getArrRes($resNotIntblAllow);
	if(count($arrNotIntblAllow)>0)
	{
		$pdf->repType = 1;
		$pdf->AliasNbPages();
		$pdf->printedby =  $migAllowObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContentDetails($arrNotIntblAllow);
	}
	
	//Employees with New Set Up of Allowance and with record in Employee Allowance
	$qryNotIntblAllow = "Select txAllowP.compCode as c_compCode, txAllowP.empNo as c_empNo, empLastName, empFirstName, empMidName, 
						txAllowP.allowCode as c_allowCode,
						allowDesc, 
						txAllowP.allowAmt as c_allowAmt,
						txAllowP.allowPayTag as c_allowPayTag, 
						txAllowP.allowStart as c_allowStart,
						txAllowP.allowEnd as c_allowEnd, 
						txAllowP.allowSked as c_allowSked, 
						txAllowP.allowTag as c_allowTag 
						from tblAllowance_Paradox txAllowP, tblEmpMast emp, tblAllowType as allowType 
						where 
						txAllowP.empNo=emp.empNo and 
						txAllowP.compCode='".$_SESSION["company_code"]."' and 
						allowType.compCode='".$_SESSION["company_code"]."' and 
						txAllowP.allowCode=allowType.allowCode and 
						empBrnCode='".$_GET["empBrnCode"]."' and 
						txAllowP.empNo in (Select empNo from tblAllowance where compCode='".$_SESSION["company_code"]."')
						order by txAllowP.empNo, c_allowCode";
	$resNotIntblAllow = $migAllowObj->execQry($qryNotIntblAllow);
	$arrNotIntblAllow = $migAllowObj->getArrRes($resNotIntblAllow);
	if(count($arrNotIntblAllow)>0)
	{
		$pdf->repType = 3;
		$pdf->AliasNbPages();
		$pdf->printedby =  $migAllowObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContentDetails($arrNotIntblAllow);
	}
	
	
	//Employees with New Allowance but without in EmpMast
	$qryNotIntblAllow = "Select txAllowP.compCode as c_compCode, txAllowP.empNo as c_empNo, 
						txAllowP.allowCode as c_allowCode,allowDesc,
						txAllowP.allowAmt as c_allowAmt,
						txAllowP.allowPayTag as c_allowPayTag,
						txAllowP.allowStart as c_allowStart,
						txAllowP.allowEnd as c_allowEnd,
						txAllowP.allowTag as c_allowTag
						from tblAllowance_Paradox txAllowP,
						tblAllowType as allowType
						where  
						txAllowP.compCode='".$_SESSION["company_code"]."' 
						and allowType.compCode='".$_SESSION["company_code"]."' and txAllowP.allowCode=allowType.allowCode
						and
						txAllowP.empNo not in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."')";
	$resNotIntblAllow = $migAllowObj->execQry($qryNotIntblAllow);
	$arrNotIntblAllow = $migAllowObj->getArrRes($resNotIntblAllow);
	if(count($arrNotIntblAllow)>0)
	{
		$pdf->repType = 2;
		$pdf->AliasNbPages();
		$pdf->printedby =  $migAllowObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContentDetails($arrNotIntblAllow);
	}
	
	
	$pdf->Output();
?>



