<?php
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	03/24/2010
		Function		:	Blacklist Module (Pop Up) 
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("common_obj.php");
	include("../../../includes/pdf/fpdf.php");
	
	$blackListObj = new inqTSObj();
	$sessionVars = $blackListObj->getSeesionVars();
	$blackListObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
		
			$this->SetFont('Courier','','10'); 
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$hTitle = " Blacklist Information Report";
			$this->Cell(215,5,$hTitle,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(148,5,"Report ID: BLKLSTALL");
			$this->Cell(100,5,$_GET['empBrnCode']." BRANCH");
			if($_GET["monthfr"]!="" && $_GET["monthto"]!=""){
			$this->Cell(100,5,"Date Covered: ".date("M j Y",strtotime($_GET["monthfr"]))." - ".date("M j Y",strtotime($_GET["monthto"])));
			}
			$this->Cell(0,3,'','');
			$this->Ln(5);
			
			$this->SetFont('Courier','B','10');
			$this->Cell(15,7,'No.','TB','','L');
			$this->Cell(85,7,'Company','TB','','L');
			$this->Cell(70,7,'NAME','TB','','L');
			$this->Cell(30,7,'SSS. NO.','TB','','L');
			$this->Cell(55,7,'POSITION','TB','','L');
			$this->Cell(75,7,'REASON','TB','','L');
			$this->Ln();
			
		}
				
		function displayContent($brn){
			foreach($brn as $values){
				$this->SetFont('Courier','','8');
				$this->Cell(15,5,$values['blacklist_No'],'0','0','L','0');
				$this->Cell(85,5,$values['compCode'],'0','0','L','0');	
				$this->Cell(70,5,$values['empLastName'].", ".$values['empFirstName']." ".$values['empMidName'],'0','0','L','0');	
				$this->Cell(30,5,$values['empSssNo'],'0','0','L','0');	
				$this->Cell(55,5,$values['empPosId'],'0','0','L','0');	
				$this->MultiCell(75,5,$values['reason'],'0','1','L','0');	
			}
			$this->SetFont('Courier','','10');
			$this->Cell(335,5," * * * * * Nothing Follows * * * * * ",'0','0','C');
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(335,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',9);
			$this->Cell(335,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
####Function to set up multiline cell	
	function setMultiLine($w,$s,$field,$obj,$pos){
		$x=$this->GetX();
		$y=$this->GetY();
		$y1=$this->GetY();
		$this->MultiCell($w,$s,$field,$obj,$pos);
		$y2=$this->GetY();
		$yh=$y2-$y1;
		$this->SetXY($x+$w,$this->GetY()-$yh);	
	}
		
	}
	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	$empBrnCode = $_GET['empBrnCode'];
	
	$empDiv = $_GET['empDiv'];
	$empDept = $_GET['empDept'];
	$empPos = $_GET['empPos'];
	$hide_empDept = $_GET['hide_empDept'];
	$hide_empSect = $_GET['hide_empSect'];
	$txtSearch = $_GET["txtSearch"];
	$srchType = $_GET["srchType"];
	$monthfr =  $_GET["monthfr"];
	$monthto =  $_GET["monthto"];

	$and = "AND ";
		
	if(($_GET["srchType"]!="") && ($_GET["srchType"]!=0))
	{
		$where_clause = 1;
		
		if($srchType==1)
			$where = " $and empNo LIKE '".str_replace("'","''",$txtSearch)."%'";
		elseif($srchType==2)
			$where = " $and empLastName LIKE '".str_replace("'","''",$txtSearch)."%' ";
		elseif($srchType==3)
			$where = " $and empFirstName LIKE '".str_replace("'","''",$txtSearch)."%' ";
		elseif($srchType==4)
			$where = " $and empMidName LIKE '".str_replace("'","''",$txtSearch)."%' ";
		elseif($srchType==5)
			$where = " $and empSssNo LIKE '".str_replace("'","''",$txtSearch)."%' ";
		elseif($srchType==6)
			$where = (date("Y-m-d", strtotime($txtSearch))!="1970-01-01"?" $and dateHired LIKE '".date("Y-m-d", strtotime($txtSearch))."%' ":"");
		elseif($srchType==7)
			$where = (date("Y-m-d", strtotime($txtSearch))!="1970-01-01"?" $and dateResigned LIKE '".date("Y-m-d", strtotime($txtSearch))."%' ":"");
		elseif($srchType==8)
			$where = " $and blacklist_No LIKE '".str_replace("'","''",$txtSearch)."%' ";
	}
		
	if ($empBrnCode!="0") {$empBrnCode1 = " where (empBrnCode = '".$empBrnCode."')"; $where_clause = 1;} else {$empBrnCode1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " $and (empDepCode = '{$empDept}')"; $where_clause = 1;} else {$empDept1 = "";}
	if ($empPos>"" && $empPos>0) {$empPos1 = " $and (empPosId = '{$empPos}')"; $where_clause = 1;} else {$empPos1 = "";}
	if (($monthfr!="") && ($monthto!="")) {$dateEncoded1 = " $and dateEncoded between '".date("Y-m-d", strtotime($monthfr))."' and '".date("Y-m-d", strtotime($monthto))."'"; $where_clause = 1;}
	
	$pdf->where_clause = $where.$empBrnCode1.$empDept1.$empPos1.$dateEncoded1;
	
	$sqlEmp = "SELECT * FROM tblBlacklistedEmp where empBrnCode='".$empBrnCode."' $where $empDept1 $empPos1 $dateEncoded1 order by empLastName"; 

	$resEmp = $blackListObj->execQry($sqlEmp);	
	$arrEmp = $blackListObj->getArrRes($resEmp);
	if($blackListObj->getRecCount($resEmp)>0)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $blackListObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrEmp);		
	}
	$pdf->Output('Blacklisted Report.pdf','D');
?>