
<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
			
			
			$this->SetFont('Courier','','9'); 
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$this->Cell(140,5,$this->compName,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			
			$this->Cell(70,5,"Report ID: SSSRPT01");
			
			$hTitle = "SSS Report for the Period of ".$this->pdHeadTitle;
			$this->Cell(140,5,$hTitle,'','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln();
			$this->SetFont('Courier','B','9');
			
			
			$this->Cell(35,6,'EMP. NO.',0,'','L');
			$this->Cell(40,6,'EMPLOYEE NAME',0,'','L');
			$this->Cell(30,6,'SSS CONT.',0,'','R');
			
			$this->Ln(10);
		}
		
		
		function getTaxes($empNo,$PdNum,$trnCode,$table) 
		{
			$chopMonth = split("-",$PdNum);
			
			$qry = "SELECT sum(trnAmountD) as totAmt FROM tblDeductions
					WHERE compCode = '".$_SESSION["company_code"]."' AND pdYear = '$chopMonth[2]' 
					AND pdNumber IN('$chopMonth[0]','$chopMonth[1]') AND trnCode = '$trnCode' 
					AND empNo = '$empNo' 
					GROUP BY empNo
					ORDER BY empNo ASC ";
			
			$res = $this->execQry($qry);
			return $this->getSqlAssoc($res);
		}
		
		function getTaxesHist($empNo,$PdNum,$trnCode,$table) 
		{
			$chopMonth = split("-",$PdNum);
			
			$qry = "SELECT sum(trnAmountD) as totAmt FROM tblDeductionsHist
					WHERE compCode = '".$_SESSION["company_code"]."' AND pdYear = '$chopMonth[2]' 
					AND pdNumber IN('$chopMonth[0]','$chopMonth[1]') AND trnCode = '$trnCode' 
					AND empNo = '$empNo' 
					GROUP BY empNo
					ORDER BY empNo ASC ";
			
			$res = $this->execQry($qry);
			return $this->getSqlAssoc($res);
		}
		
		function displayContent($resQry,$table,$payPd)
		{
			$this->SetFont('Courier','','9'); 
			$ctr_emp = 0;
			$grandtot = 0;
			foreach($resQry as $resQry_val)
			{
				$rTotal = $this->getTaxes($resQry_val['empNo'],$payPd,SSS_CONTRIB,$table);
				$rTotalHist = $this->getTaxesHist($resQry_val['empNo'],$payPd,SSS_CONTRIB,$table);
				$sumperMontSss =$rTotal["totAmt"] + $rTotalHist["totAmt"];
				if($sumperMontSss!=0)
				{
					$this->Cell(35,6,$resQry_val["empNo"],0,'','L');
					$this->Cell(40,6,$resQry_val["empLastName"].", ".$resQry_val["empFirstName"][0].".".$resQry_val["empMidName"][0].".",0,'','L');
					$this->Cell(30,6,number_format($sumperMontSss ,2),0,'','R');
					$this->Ln();
					$grandtot+=$sumperMontSss ;
					$ctr_emp++;
				}
			}
			/*Grand Total*/
			$this->Ln(2);
			$this->SetFont('Courier','B','9'); 
			$this->Cell(75,6,'GRAND TOTAL : ',0,'','R');
			$this->Cell(30,6,number_format($grandtot,2),0,'','R');
			$this->Ln(10);
			$this->SetFont('Courier','','9'); 
			$this->Cell(50,6,"Total Employees Process = ".$ctr_emp." of ".$this->groupName.", ".$this->catName,0,1);
			$this->Ln(10);
			$this->Cell(200,6,'* * * End of Report * * *','0','','C'); 
		}
		
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(260,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','',9);
			$this->Cell(260,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}

	
	$pdf = new PDF('L', 'mm', 'LETTER');
	$pdf->topType		=	$_GET["topType"];
	$payPd      		= 	$_GET['payPd'];
	$chopMonth 			= 	split("-",$payPd);
	$payPdYear 			= 	$chopMonth[2];
	$payPdNum 			= 	$chopMonth[4];
	$payPdMonthName		= 	$chopMonth[5];
	$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$table				= 	$_GET["table"];
	$empNo         		= 	$_GET['empNo'];
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$orderBy       		= 	$_GET['orderBy'];
	$topType			= 	$_GET['topType'];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	$pdMonthname		=	$inqTSObj-> getPayMonth($chopMonth[0].",".$chopMonth[1], $payPdYear);
	$pdMonthName		=	date("F", strtotime($pdMonthname."/".date("d")."/".$payPdYear));
	
	$pdf->pdHeadTitle	=	$pdMonthName." (Group ".$_SESSION[pay_group].", ".$catName['payCatDesc'].")";
	$pdf->groupName 	= 	($_SESSION["pay_group"]==1?"GROUP 1":"GROUP 2");
	$pdf->catName		=	$catName["payCatDesc"]; 
	$pdf->topType = $topType;
	
	if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	if ($orderBy==1) {$orderBy1 = "  empLastName, empFirstName, empMidName, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==2) {$orderBy1 = "  empNo, empDiv, empDepCode, empSecCode ";} 
	if ($orderBy==3) {$orderBy1 = "  empDiv, empDepCode, empSecCode, empLastName, empFirstName, empMidName ";}

	$qryEmpList = "SELECT * FROM tblEmpMast
					WHERE compCode = '{$sessionVars['compCode']}' AND 
					empStat NOT IN('RS','IN','TR')
					and empPayGrp = '".$_SESSION["pay_group"]."'
					and empPayCat = '".$_SESSION["pay_category"]."' 
					$empNo1 $empName1 $empDiv1 $empName1 $empDept1 $empSect1 
					order by
					$orderBy1 ";
	
	$resEmpList = $inqTSObj->execQry($qryEmpList);
	$arrEmpList = $inqTSObj->getArrRes($resEmpList);
	if(count($arrEmpList)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrEmpList,$table,$payPd);
	}
	
	$pdf->Output();
?>
