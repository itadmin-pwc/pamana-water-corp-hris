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
			
			
			$this->SetFont('Courier','','10'); 
			$this->Cell(80,5,"Run Date: " . $newdate,"0");
			$this->Cell(170,5,$this->compName,"0",'0','C');
			$this->Cell(85,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(80,5,"Report ID: DENOMR01");
			$hTitle = " Denomination Listing Report for the Period of ".$this->pdHeadTitle;
			$this->Cell(170,5,$hTitle,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			$this->SetFont('Courier','','10');
			$this->Cell(30,6,'EMP. NO','0','','L');
			$this->Cell(45,6,'NAME','0','','L');
			$this->Cell(25,6,'NET PAY','0','','R');
			
			$arrDenomList = $this->dispDenominations();
			$sizeofDenom = sizeof($arrDenomList);
			$widthtd = 230 / $sizeofDenom;
			
			foreach($arrDenomList as $arrDenomList_val)
			{
				$this->Cell($widthtd,6,$arrDenomList_val["denomination"],'0','','R');
			}
			
			$this->Ln();
		}
		
		
		function dispDenominations()
		{
			$qry = "SELECT * FROM tblDenomList WHERE denTag = 'Y' ORDER BY denomination DESC";
			$res = $this->execQry($qry);
			return $this->getArrRes($res);
		}
		
		function getDenomAmt($amt) 
		{
			$qry = "SELECT * FROM tblDenomList WHERE denTag = 'Y' ORDER BY denomination DESC";
			$res = $this->execQry($qry);
			$arr = $this->getArrRes($res);
			$stack = array();
			
			foreach ($arr as $val)
			{
				if ($amt>=$val['denomination']) 
				{
					$tmpDenom = $amt / $val['denomination'];
					$tmpDenom = floor($tmpDenom);
				} 
				else 
				{
					$tmpDenom = 0;
				}
				array_push($stack, $tmpDenom);
				$amt = $amt - ($tmpDenom * $val['denomination']);
				$amt = sprintf("%01.2f",$amt);
			} 
			return $stack;
		}
		
		function displayContent($arrPaySum)
		{
			$this->SetFont('Courier','','10'); 
			$this->Ln();
			$ctr_emp = $grandSal = 0;
			foreach($arrPaySum as $arrPaySum_val)
			{
				$this->Cell(30,5,$arrPaySum_val["empNo"],'0','','L');
				$this->Cell(45,5,$arrPaySum_val["empLastName"].", ".$arrPaySum_val["empFirstName"][0].".".$arrPaySum_val["empMidName"][0].'.','0','','L');
				$this->Cell(25,5,number_format($arrPaySum_val["netSalary"],2),'0','','R');
				$arrDenomList = $this->dispDenominations();
				$sizeofDenom = sizeof($arrDenomList);
				$widthtd = 230 / $sizeofDenom;
				
				$arrDenom=$this->getDenomAmt($arrPaySum_val["netSalary"]);
				foreach ($arrDenom as $key => $denomVal)
				{  
					$this->Cell($widthtd,5,$denomVal,0,0,'R');
					$grand_Denom[$key]+=$denomVal;
				}
				$this->Ln();
				$ctr_emp++;
				$grandSal+=$arrPaySum_val["netSalary"];
			}
			$this->Ln();
			$this->Cell(75,5,'GRAND TOTAL = '.$ctr_emp.'','0','','L');
			$this->Cell(25,5,number_format($grandSal,2),'0','','R');
			$sizeofDenom = sizeof($grand_Denom);
			$widthtd = 230 / $sizeofDenom;
			
			
			foreach($grand_Denom as $grand_Denom_val)
			{
				$this->Cell($widthtd,5,$grand_Denom_val,'0','','R');
			}
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','',9);
			$this->Cell(335,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}
	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$pdf->reportType	= 	$_GET['tbl'];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	$arrPayPd 			= 	$inqTSObj->getSlctdPd($_SESSION["company_code"],$payPd);
	$pdf->pdYear		=	$arrPayPd['pdYear'];
	$pdf->pdNum		=	$arrPayPd['pdNumber'];
	$empNo         		= 	$_GET['empNo'];
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$orderBy       		= 	$_GET['orderBy'];
	$locType			= 	$_GET['locType'];
	$empBrnCode 		= 	$_GET['empBrnCode'];
	$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$pdf->pdHeadTitle	=	$inqTSObj->valDateArt($arrPayPd['pdPayable'])." (Group ".$_SESSION[pay_group].", ".$catName['payCatDesc'].")";
	
	if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
	if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
	
	if ($empNo>"") {$empNo1 = " AND (tblDed.empNo LIKE '{$empNo}%')"; } else {$empNo1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDivCode = '{$empDiv}')"; } else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')"; } else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')"; } else {$empSect1 = "";}
	if ($orderBy==1) {$orderBy1 = "  brnDesc, locDesc,empLastName, empFirstName, empMidName";} 
	if ($orderBy==2) {$orderBy1 = "  brnDesc, locDesc,tblPaySum.empNo";} 
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (tblPaySum.empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($locType=="S")
		$locType1 = " AND (tblPaySum.empLocCode = '{$empBrnCode}')";
	if ($locType=="H")
		$locType1 = " AND (tblPaySum.empLocCode = '0001')";
		
	$pdf->where_empmast = $empNoRep.$empDiv1.$empDept1.$empSect1.$empBrnCode1.$locType1;

	$qryPaySum 	= "Select tblPaySum.empNo,SUM(netSalary+sprtAllow) as netSalary,empLastName,empFirstName,empMidName,tblPaySum.empbrnCode,tblBrnDesc.brnDesc as brnDesc,tblPaySum.empLocCode,tblLocDesc.brnDesc as locDesc
					from ".$reportType." tblPaySum, tblEmpMast tblEmp, tblBranch tblBrnDesc, tblBranch tblLocDesc
					where 
					tblPaySum.empNo=tblEmp.empNo and
					tblPaySum.empbrnCode = tblBrnDesc.brnCode and
					tblPaySum.empLocCode = tblLocDesc.brnCode and
					tblPaySum.compCode='".$_SESSION["company_code"]."' and
					tblEmp.compCode='".$_SESSION["company_code"]."' and 
					tblBrnDesc.compCode='".$_SESSION["company_code"]."' and 
					tblLocDesc.compCode='".$_SESSION["company_code"]."' 
					AND pdYear='".$pdf->pdYear."'
					AND pdNumber='".$pdf->pdNum."' 
					and payGrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."'
					and empBnkCd='3'
					".$pdf->where_empmast."
					GROUP BY tblPaySum.empNo, tblPaySum.netSalary, tblEmp.empLastName, tblEmp.empFirstName, tblEmp.empMidName, tblPaySum.empBrnCode, 
                    tblBrnDesc.brnDesc, tblPaySum.empLocCode, tblLocDesc.brnDesc 
					order by $orderBy1
					"; 
	$resPaySum = $inqTSObj->execQry($qryPaySum);
	$arrPaySum = $inqTSObj->getArrRes($resPaySum);
	if(count($arrPaySum)>=1)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrPaySum);
	}
	
	$pdf->Output('denomination_list.pdf','D');
?>