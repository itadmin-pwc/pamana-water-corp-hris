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
	include("blacklisted_obj.php");
	include("../../../includes/pdf/fpdf.php");
	
	$blackListObj = new blackListObj();
	$sessionVars = $blackListObj->getSeesionVars();
	$blackListObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
		
			$hTitle = "";
			$this->Cell(80,5,$hTitle);
			$this->Ln();
			$this->Cell(335,3,'','');
			$this->Ln(10);
			$this->SetFont('Courier','B','9');
			
			$this->Cell(193,6,'BLACKLIST INFORMATION REPORT','0','','C');
			$this->Ln(15);
			$this->SetFont('Courier','','9'); 
			
		}
		
		function getInfo()
		{
			$qryChecked = "Select replace(empLastName,'Ñ','N') as emprLname, replace(empFirstName,'Ñ','N') as emprFname, replace(empMidName,'Ñ','N') as emprMidName, * from tblBlacklistedEmp where blacklist_No='".$_GET["blacklistid"]."'";
			$res_Checked  = $this->execQry($qryChecked);
			$arr_Checked= $this->getSqlAssoc($res_Checked);
			
			$compName = $this->getCompanyName($arr_Checked["compCode"]);
			$arrBrnchName =  $this->getEmpBranchArt($arr_Checked["compCode"],$arr_Checked["empBrnCode"]);
			$empBrnchName = $arrBrnchName["brnDesc"];
			
			$arrPosDesc = $this->getpositionwil(" where posCode='".$arr_Checked["empPosId"]."'",2);
			$empPosDesc = $arrPosDesc["posDesc"];
			
			$arrDeptDesc = $this->getDeptDescGen($arrGetInfo["compCode"],$arr_Checked["divCode"],$arr_Checked["deptCode"]);
			$empDeptDesc = $arrDeptDesc["deptDesc"];
			
			$this->SetFont('Courier','','8');
			
			//$this->Cell(195,4,'BLACKLIST INFORMATION REPORT','1','','C');
			$this->Cell(25,4,'Blacklist No. ','0','0','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$arr_Checked["blacklist_No"],'0','1','L');
			$this->Ln();
			$this->Cell(25,4,'Emp. No. ','0','0','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$arr_Checked["empNo"],'0','1','L');
			
			$this->Cell(25,4,'Employee Name ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$arr_Checked["empLastName"].", ".$arr_Checked["empFirstName"]." ".$arr_Checked["empMidName"],'0','1','L');
			
			$this->Cell(25,4,'Company ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$compName,'0','1','L');
			
			$this->Cell(25,4,'Branch ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$empBrnchName,'0','1','L');
			
			$this->Cell(25,4,'Agency ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$arr_Checked["agency"],'0','1','L');
			
			$this->Cell(25,4,'Department ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$empPosDesc,'0','0','L');
			
			$this->Cell(25,4,'Position ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$empPosDesc,'0','1','L');
			
			$this->Cell(25,4,'SSS No. ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$arr_Checked["empSssNo"],'0','0','L');
			
			$this->Cell(25,4,'Tin No. ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,$arr_Checked["empTin"],'0','1','L');
			
			$this->Cell(25,4,'Birth Date ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,($arr_Checked["empBday"]!=""?date("m/d/Y", strtotime($arr_Checked["empBday"])):""),'0','1','L');
			
			$this->Cell(25,4,'Date Hired ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,($arr_Checked["dateHired"]!=""?date("m/d/Y", strtotime($arr_Checked["dateHired"])):""),'0','0','L');
			
			$this->Cell(25,4,'Date Resigned ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,($arr_Checked["dateResigned"]!=""?date("m/d/Y", strtotime($arr_Checked["dateResigned"])):""),'0','1','L');
			
			$this->Ln();
			$this->Cell(25,4,'Reason ','0','','L');
			$this->Cell(5,4,'','0','1','C');
			$this->Cell(10,4,'','0','0','C');
			$this->Cell(190,6,$arr_Checked["reason"],'0','1','L');
			$this->Ln(40);
			$this->Cell(25,4,'Date Encoded ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,($arr_Checked["dateEncoded"]!=""?date("m/d/Y", strtotime($arr_Checked["dateEncoded"])):""),'0','0','L');
			
			$this->Cell(25,4,'Encoded By ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$arrEmpInfo = $this->getUserInfo($arr_Checked["compCode"],$arr_Checked["userId"],'');
			if($arrEmpInfo["empLastName"]!="")
				$empName = $arrEmpInfo["empLastName"].", ".$arrEmpInfo["empFirstName"]." ".$arrEmpInfo["empMidName"];
			else
				$empName = $arrEmpInfo["empLastName"]." ".$arrEmpInfo["empFirstName"]." ".$arrEmpInfo["empMidName"];
			$this->Cell(65,4,$empName,'0','1','L');
			
			$this->Cell(25,4,'Date Updated ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$this->Cell(65,4,($arr_Checked["dateUpdated"]!=""?date("m/d/Y", strtotime($arr_Checked["dateUpdated"])):""),'0','0','L');
			
			$this->Cell(25,4,'Updated By ','0','','L');
			$this->Cell(5,4,':','0','0','C');
			$arrEmpInfo = $this->getUserInfo($arr_Checked["compCode"],$arr_Checked["updatedBy"],'');
			if($arrEmpInfo["empLastName"]!="")
				$empName = $arrEmpInfo["empLastName"].", ".$arrEmpInfo["empFirstName"]." ".$arrEmpInfo["empMidName"];
			else
				$empName = $arrEmpInfo["empLastName"]." ".$arrEmpInfo["empFirstName"]." ".$arrEmpInfo["empMidName"];
			$this->Cell(65,4,$empName,'0','1','L');
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(195,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',9);
			$this->Cell(195,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}
	
	
	
	$pdf = new PDF('P', 'mm', 'LETTER');
	$pdf->compName		=	$blackListObj->getCompanyName($_SESSION["company_code"]);
	$pdf->AliasNbPages();
	$pdf->printedby = $blackListObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
	$pdf->AddPage();
	if($_GET["blacklistid"]!="")
		$pdf->getInfo();
	$pdf->Output();
?>