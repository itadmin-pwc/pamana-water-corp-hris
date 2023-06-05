<?php
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("common_obj.php");
	include("../../../includes/pdf/fpdf.php");
	
	$payrollTypeObj = new inqTSObj();
	$sessionVars = $payrollTypeObj->getSeesionVars();
	$payrollTypeObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		var $compName;
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
		
			$this->SetFont('Courier','','10'); 
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$this->Cell(140,5,$this->compName,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(70,5,"Report ID: SALARYLIST");
			$hTitle = " Payroll Listing By Salary And Allowance Report";
			$this->Cell(140,5,$hTitle,'0','','C');
			
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			
			$this->SetFont('Courier','B','10');
			$this->Cell(30,5,'EMP. NO.','0','','L');
			$this->Cell(45,5,'EMPLOYEE NAME','0','','L');
			$this->Cell(20,5,'TEU','0','','L');
			$this->Cell(50,5,'------- SALARY -------','0','','C');
			$this->Cell(5,5,'','0','','C');
			$this->Cell(110,5,'--------------------- ALLOWANCE ------------------- ','0','','C');
			$this->Ln();
			$this->Cell(30,5,'','0','','L');
			$this->Cell(45,5,'','0','','L');
			$this->Cell(20,5,'','0','','L');
			$this->Cell(25,5,'MONTHLY','0','','C');
			$this->Cell(25,5,'DAILY','0','','C');
			$this->Cell(5,5,'','0','0','R');

			$this->Cell(40,5,'ALLOWANCE TYPE','0','0','L');
			$this->Cell(25,5,'AMOUNT','0','0','C');
			$this->Cell(25,5,'ALLOW. TAG','0','0','L');
			$this->Cell(25,5,'REMARKS','0','0','L');
			$this->Ln();			
			
		}
		
		function getRankType()
		{
			$qryRank =	"SELECT  rankCode, rankDesc FROM tblEmpMast tblEmp, tblRankType 
						WHERE  empRank=rankCode ".$this->where."
						group by rankCode, rankDesc
						order by rankDesc";
			$rsRank = $this->execQry($qryRank);
			return $this->getArrRes($rsRank);
		}
		
		function displayContent($arrEmp,$arrBranch,$arrAllow)
		{
			$this->Ln(5);
			
			$ctr = 1;
			
			$arrRank = $this->getRankType();
			$brn="";
						
			foreach($arrBranch as $valBranch) {
				$this->AddPage();
				$this->Cell(50,5,strtoupper($valBranch["brnDesc"]),'0','1','L');
					foreach($arrRank as $arrRank_val)
					{
						$this->SetFont('Courier','B','9'); 
						$this->Cell(50,5,strtoupper($arrRank_val["rankDesc"]),'0','1','L');
						$this->Cell(260,1,'','T');
						$this->Ln();
						$ctr = 1;
						$sum_emp = 0;
						foreach($arrEmp as $arrEmp_val)
						{
		/*					if ($brn != $arrEmp_val['empBrnCode']) {
								$this->AddPage();
							}
							$brn=$arrEmp_val['empBrnCode'];	*/			
							if ($valBranch['brnCode']==$arrEmp_val["empBrnCode"]) {
									if($arrEmp_val["empRank"]==$arrRank_val["rankCode"])
									{
										
										$this->SetFont('Courier','','9');
										$this->Cell(30,5,$ctr.". " . $arrEmp_val["empNo"],'0','','L');
										$this->Cell(45,5,$arrEmp_val["empLastName"].", ".$arrEmp_val["empFirstName"][0].".".$arrEmp_val["empMidName"][0].".",'0','0','L');
										$this->Cell(20,5,substr($arrEmp_val["empTeu"],0,28),'0','0','L');
										if (in_array(1,explode(',',$_SESSION['user_payCat']))) {
											$this->Cell(25,5,number_format($arrEmp_val["empMrate"],2),'0','0','R');
											$this->Cell(25,5,number_format($arrEmp_val["empDrate"],2),'0','0','R');
										} else {
											if ($arrEmp_val["empPayCat"] != 1) {
												$this->Cell(25,5,number_format($arrEmp_val["empMrate"],2),'0','0','R');
												$this->Cell(25,5,number_format($arrEmp_val["empDrate"],2),'0','0','R');
											} else {
												$this->Cell(25,5,'--','0','0','R');
												$this->Cell(25,5,'--','0','0','R');
											}	
										}	
										$this->Cell(5,5,'','0','0','R');
										$chAllow=0;
										foreach($arrAllow as $valAllow) {
											if ($valAllow['empNo'] == $arrEmp_val["empNo"]) {
												$allowTag = ($valAllow['allowTag']=='M')?" Monthly":" Daily";
												$allowRem = ($valAllow['allowPayTag']=='P')?"Permanent":"Temporary";
												if ($chAllow == 0) {
													$this->Cell(40,5,$valAllow['allowDesc'],'0','0','L');
													if (in_array(1,explode(',',$_SESSION['user_payCat'])))  {
														$this->Cell(25,5,number_format($valAllow["allowAmt"],2),'0','0','R');
													} else {
														if ($arrEmp_val["empPayCat"] != 1) 
															$this->Cell(25,5,number_format($valAllow["allowAmt"],2),'0','0','R');
														else
															$this->Cell(25,5,'--','0','0','R');
													}	
													$this->Cell(25,5,$allowTag,'0','0','L');
													$this->Cell(25,5,$allowRem,'0','0','L');
													$this->Ln();
												} else {
													$this->Cell(150,5,'','0','0','R');
													$this->Cell(40,5,$valAllow['allowDesc'],'0','0','L');
													if (in_array(1,explode(',',$_SESSION['user_payCat'])))  {
														$this->Cell(25,5,number_format($valAllow["allowAmt"],2),'0','0','R');
													} else {
														if ($arrEmp_val["empPayCat"] != 1) 
															$this->Cell(25,5,number_format($valAllow["allowAmt"],2),'0','0','R');
														else
															$this->Cell(25,5,'--','0','0','R');
													}														
													$this->Cell(25,5,$allowTag,'0','0','L');
													$this->Cell(25,5,$allowRem,'0','0','L');
													$this->Ln();
												}
												$chAllow=1;		
											}	
										}
										if ($chAllow == 0) {
											$this->Ln();
										}
										$sum_emp++;
										$ctr++;		
									}	
								}		
						}
						$this->SetFont('Courier','B','9');
						$this->Cell(20,5,'TOTAL','0','0','L');
						$this->Cell(20,5,$sum_emp,'0','1','L');
						$this->Ln();
					}
			}
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(260,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',9);
			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}
	
	
	
	$pdf = new PDF('L', 'mm', 'LETTER');
	
	$empBrnCode = $_GET['empBrnCode'];
	$empDiv = $_GET['empDiv'];
	$empDept = $_GET['empDept'];
	$empSect = $_GET['empSect'];
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($_GET['payCat'] !=0) { $payCat=" AND empPayCat='".$_GET['payCat']."'"; } else {$payCat = "";}
	$arrComp = $payrollTypeObj->getCompany($_SESSION['company_code']);
	$pdf->where = " and (tblEmp.compCode = '".$_SESSION["company_code"]."') $payCat $empDiv1 $empDept1 $empSect1 $empBrnCode1";
							
	$sqlEmp = "SELECT * FROM tblEmpMast 
			   WHERE (compCode = '".$_SESSION["company_code"]."') 
			   $payCat
			   $empDiv1 $empDept1 $empSect1 $empBrnCode1 
			   order by empBrnCode,empLastName, empFirstName, empMidName ";
	$sqlBranch = "Select brnCode,brnDesc from tblBranch where compCode='{$_SESSION['company_code']}' AND brnCode IN (SELECT empBrnCode FROM tblEmpMast 
			   WHERE (compCode = '".$_SESSION["company_code"]."') 
			   $payCat
			   $empDiv1 $empDept1 $empSect1 $empBrnCode1) order by brnDesc";		   
	$resBranch = $payrollTypeObj->getArrRes($payrollTypeObj->execQry($sqlBranch));
	$resEmp = $payrollTypeObj->execQry($sqlEmp);
	$arrEmp = $payrollTypeObj->getArrRes($resEmp);
	$sqlAllow = "SELECT tblAllowance.empNo, tblAllowType.allowDesc, tblAllowance.allowCode, tblAllowance.allowAmt, tblAllowance.allowPayTag, tblAllowance.allowTag FROM tblAllowance INNER JOIN tblAllowType ON tblAllowance.compCode = tblAllowType.compCode AND tblAllowance.allowCode = tblAllowType.allowCode where empNo IN (SELECT empNo FROM tblEmpMast 
			   WHERE (compCode = '".$_SESSION["company_code"]."') 
			   $payCat
			   $empDiv1 $empDept1 $empSect1 $empBrnCode1)";
	$arrAllow = $payrollTypeObj->getArrRes($payrollTypeObj->execQry($sqlAllow));	
	$pdf->compName = $arrComp['compName'];
	if($payrollTypeObj->getRecCount($resEmp)>0)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $payrollTypeObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->displayContent($arrEmp,$resBranch,$arrAllow);
	}	
	
	$pdf->Output('EMPLOYEES_SALARY.pdf','D');
?>