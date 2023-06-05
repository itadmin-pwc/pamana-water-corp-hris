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
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$this->Cell(140,5,$this->compName,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(70,5,"Report ID: TIMESHT001");
			$hTitle = " Time Sheet Proof List for the Period of ".$this->pdHeadTitle;
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
			
			$this->SetFont('Courier','','10');
			$this->Cell(47,5,'EMP.NO - NAME','','','L');
			$this->Cell(38,5,'DATE','','','L');
			$this->Cell(25,5,'HRS/AMT','','','R');
			$this->Cell(25,5,'HRS/AMT','','','R');
			$this->Cell(25,5,'HRS/AMT','','','R');
			$this->Cell(25,5,'HRS/AMT','','','R');
			$this->Cell(25,5,'HRS/AMT','','','R');
			$this->Cell(25,5,'HRS/AMT','','','R');
			$this->Cell(25,5,'HRS/AMT','','','R');
			
			$this->Ln();
			$this->Cell(47,5,'DAY TYPE','','','L');
			$this->Cell(38,5,'DESCRIPTION','','','L');
			$this->Cell(25,5,'ABSENT','','','R');
			$this->Cell(25,5,'TARDY','','','R');
			$this->Cell(25,5,'UNDERTIME','','','R');
			$this->Cell(25,5,'OT<8','','','R');
			$this->Cell(25,5,'OT>8','','','R');
			$this->Cell(25,5,'ND<8','','','R');
			$this->Cell(25,5,'ND>8','','','R');
			$this->Ln();
		}
		
		function getEmpTimeSheetData($toDate, $frDate)
		{
			$qryEmpTS = "Select * from ".$this->reportType." where compCode='".$_SESSION["company_code"]."'
						 and tsDate >= '".$frDate."' AND tsDate <= '".$toDate."' 
						 and empPayGrp='".$_SESSION["pay_group"]."' 
						 and empPayCat='".$_SESSION["pay_category"]."'
						 order by tsDate asc";
			$resEmpTS = $this->execQry($qryEmpTS);
			$resEmpTS = $this->getArrRes($resEmpTS);
			return $resEmpTS;
		}
		
		function dayTypeDesc($dayType)
		{
			$qryDayType = "Select * from tblDayType where dayType='".$dayType."'";
			$resDayType = $this->execQry($qryDayType);
			$arrDayType = $this->getSqlAssoc($resDayType);
			return $arrDayType;
		}
		
		function displayContent($arrBrnCode, $arrQry,$toDate, $frDate)
		{
			$this->SetFont('Courier','','10'); 
			$arrEmpGetTS = $this->getEmpTimeSheetData($toDate, $frDate);
			$this->Ln();
			
			foreach($arrBrnCode as $arrBrnCode_val)
			{
				/*Display Per Branch Code*/
				if($arrBrnCode_val["empBrnCode"]!=$tmpBrnCode)
				{
					
					$this->Cell(47,5,$arrBrnCode_val["brn_Desc"],'','','L');
					$this->Ln();
				}
				$this->Cell(5,5,'','0','','L');
				
				/*Display Per Location Code*/
				$this->Cell(70,5,$arrBrnCode_val["brn_DescLoc"],'0','','L');
				$this->Ln();
				/*Display Per Employees*/
				foreach($arrQry as $resQryValue)
				{
					/*Check if the Branch Code of the Employee is part of the Displayed Branch*/
						if($arrBrnCode_val["empBrnCode"] == $resQryValue["empBrnCode"])
						{
							if($arrBrnCode_val["empLocCode"] == $resQryValue["empLocCode"])
							{
								$this->Cell(250,6,$resQryValue["empNo"]." - ".$resQryValue["empLastName"].", ".$resQryValue["empFirstName"][0].".".$resQryValue["empMidName"][0]."."." - ".($resQryValue["empPayType"]=='M'?"Monthly":"Daily"),'','','L');
								$this->Ln();
								$cnt_regday=$cnt_rd=$cnt_legday=$cnt_speday=$cnt_legdayrd=$cnt_specdayrd=0;
								/*Get Employee Time Sheet Record*/
								foreach($arrEmpGetTS as $arrEmpGetTS_val)
								{
									if($resQryValue["empNo"]==$arrEmpGetTS_val["empNo"])
									{
										$getDayTypeDesc = $this->dayTypeDesc($arrEmpGetTS_val["dayType"]);
										if($arrEmpGetTS_val["dayType"]=='01')
											$cnt_regday++;
										else if ($arrEmpGetTS_val["dayType"]=='02')
											$cnt_rd++;
										else if ($arrEmpGetTS_val["dayType"]=='03')
											$cnt_legday++;
										else if ($arrEmpGetTS_val["dayType"]=='04')
											$cnt_speday++;
										else if ($arrEmpGetTS_val["dayType"]=='05')
											$cnt_legdayrd++;
										else if ($arrEmpGetTS_val["dayType"]=='06')
											$cnt_specdayrd++;
										else
											$cnt_none++;
										
										$this->Cell(47,6,$arrEmpGetTS_val["dayType"]." - ".$getDayTypeDesc["dayTypeDesc"],'');
										$this->Cell(38,5,($arrEmpGetTS_val["tsDate"]!=""?date("m/d/Y", strtotime($arrEmpGetTS_val["tsDate"])):""),'','','L');
										$this->Cell(25,5,($arrEmpGetTS_val["hrsAbsent"]>0?$arrEmpGetTS_val["hrsAbsent"]:""),'','','R');
										$this->Cell(25,5,($arrEmpGetTS_val["hrsTardy"]>0?$arrEmpGetTS_val["hrsTardy"]:""),'','','R');
										$this->Cell(25,5,($arrEmpGetTS_val["hrsUt"]>0?$arrEmpGetTS_val["hrsUt"]:""),'','','R');
										$this->Cell(25,5,($arrEmpGetTS_val["hrsOtLe8"]>0?$arrEmpGetTS_val["hrsOtLe8"]:""),'','','R');
										$this->Cell(25,5,($arrEmpGetTS_val["hrsOtGt8"]>0?$arrEmpGetTS_val["hrsOtGt8"]:""),'','','R');
										$this->Cell(25,5,($arrEmpGetTS_val["hrsNdLe8"]>0?$arrEmpGetTS_val["hrsNdLe8"]:""),'','','R');
										$this->Cell(25,5,($arrEmpGetTS_val["hrsNdGt8"]>0?$arrEmpGetTS_val["hrsNdGt8"]:""),'','','R');
										$this->Ln();
										$cnt_ts++;
									}
									if($resQryValue["empNo"]==$arrEmpGetTS_val["empNo"])
									{
										if(($arrEmpGetTS_val["amtAbsent"]!=0) || ($arrEmpGetTS_val["amtTardy"]!=0)  || ($arrEmpGetTS_val["amtUt"]!=0) || ($arrEmpGetTS_val["amtOtLe8"]!=0) || ($arrEmpGetTS_val["amtOtGt8"]!=0) || ($arrEmpGetTS_val["amtNdLe8"]!=0) || ($arrEmpGetTS_val["amtNdLe8"]!=0) || ($arrEmpGetTS_val["amtNdGt8"]!=0) )
										{	
											$this->Cell(47,6,'','');
											$this->Cell(38,5,'','','','L');
											$this->Cell(25,5,($arrEmpGetTS_val["amtAbsent"]!=0?number_format($arrEmpGetTS_val["amtAbsent"],2):""),'','','R');
											$this->Cell(25,5,($arrEmpGetTS_val["amtTardy"]!=0?number_format($arrEmpGetTS_val["amtTardy"],2):""),'','','R');
											$this->Cell(25,5,($arrEmpGetTS_val["amtUt"]!=0?number_format($arrEmpGetTS_val["amtUt"],2):""),'','','R');
											$this->Cell(25,5,($arrEmpGetTS_val["amtOtLe8"]!=0?number_format($arrEmpGetTS_val["amtOtLe8"],2):""),'','','R');
											$this->Cell(25,5,($arrEmpGetTS_val["amtOtGt8"]!=0?number_format($arrEmpGetTS_val["amtOtGt8"],2):""),'','','R');
											$this->Cell(25,5,($arrEmpGetTS_val["amtNdLe8"]!=0?number_format($arrEmpGetTS_val["amtNdLe8"],2):""),'','','R');
											$this->Cell(25,5,($arrEmpGetTS_val["amtNdGt8"]!=0?number_format($arrEmpGetTS_val["amtNdGt8"],2):""),'','','R');
											$this->Ln();
											$tot_AmtAbs+=$arrEmpGetTS_val["amtAbsent"];
											$tot_AmtTard+=$arrEmpGetTS_val["amtTardy"];
											$tot_AmtUt+=$arrEmpGetTS_val["amtUt"];
											$tot_AmtOtLe8+=$arrEmpGetTS_val["amtOtLe8"];
											$tot_AmtOtGt8+=$arrEmpGetTS_val["amtOtGt8"];
											$tot_AmtNdLe8+=$arrEmpGetTS_val["amtNdLe8"];
											$tot_AmtNdGt8+=$arrEmpGetTS_val["amtNdGt8"];
										}
									}
								}
								$this->SetFont('Courier','b','10'); 
								$this->Cell(85,5,'TOTAL','0');
								
								$this->Cell(25,5,number_format($tot_AmtAbs,2),'0','','R');
								$this->Cell(25,5,number_format($tot_AmtTard,2),'0','','R');
								$this->Cell(25,5,number_format($tot_AmtUt,2),'0','','R');
								$this->Cell(25,5,number_format($tot_AmtOtLe8,2),'0','','R');
								$this->Cell(25,5,number_format($tot_AmtOtGt8,2),'0','','R');
								$this->Cell(25,5,number_format($tot_AmtNdLe8,2),'0','','R');
								$this->Cell(25,5,number_format($tot_AmtNdGt8,2),'0','','R');
								
								$this->Ln();
								
								$this->Cell(30,5,'REG DAY = '.$cnt_regday,'0');
								$this->Cell(30,5,'RD = '.$cnt_rd,'0');
								$this->Cell(30,5,'LEG.DAY = '.$cnt_legday,'0');
								$this->Cell(40,5,'LEG.DAY RD = '.$cnt_legdayrd,'0');
								$this->Cell(30,5,'SPE.DAY = '.$cnt_speday,'0');
								
								$this->Cell(40,5,'SPE.DAY RD = '.$cnt_specdayrd,'0','','L');
								$this->SetFont('Courier','','10'); 
								$this->Ln();
								$this->Ln();
								unset($tot_AmtAbs,$tot_AmtTard,$tot_AmtUt,$tot_AmtOtLe8,$tot_AmtOtGt8,$tot_AmtNdLe8,$tot_AmtNdGt);
							}			
						}
					/*End of Check if the Branch Code of the Employee is part of the Displayed Branch*/
				}
				
				$tmpBrnCode = $arrBrnCode_val["empBrnCode"];
				
				
				
				$this->Ln();
			}
			
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
	$pdf->reportType	= $_GET["reportType"];
	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	$arrPayPd 			= 	$inqTSObj->getSlctdPd($_SESSION["company_code"],$_GET['payPd']);
	$empBrnCode 		= 	$_GET['empBrnCode'];
	$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$pdf->pdHeadTitle	=	$inqTSObj->valDateArt($arrPayPd['pdPayable'])." (Group ".$_SESSION[pay_group].", ".$catName['payCatDesc'].")";
	if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
	//if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
	if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
	if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
	if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($locType=="S")
		$locType1 = " AND (empLocCode = '{$empBrnCode}')";
	if ($locType=="H")
		$locType1 = " AND (empLocCode = '0001')";
	$empStat = ($_SESSION['pay_category'] !=9) ? " AND empStat NOT IN('RS','IN','TR') ":"";	
	
	$qryTS = "SELECT * FROM tblEmpMast where empNo in 
			 	(Select empNo from ".$pdf->reportType." where compCode='".$_SESSION["company_code"]."'
				 and tsDate >= '{$arrPayPd['pdFrmDate']}' AND tsDate <= '{$arrPayPd['pdToDate']}'
				 and empPayGrp='".$_SESSION["pay_group"]."' and empPayCat='".$_SESSION["pay_category"]."') 
				$empStat $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $empBrnCode1 $locType1			   
			   	order by empLastName, empFirstName";
	$resTS = $inqTSObj->execQry($qryTS);
	$arrTS = $inqTSObj->getArrRes($resTS);
	$getListofBranch = $inqTSObj->getBrnCodes($arrTS);
	
	if($inqTSObj->getRecCount($resTS)>0)
	{
		
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($getListofBranch,$arrTS,$arrPayPd['pdToDate'],$arrPayPd['pdFrmDate']);
	}
	$pdf->Output();
?>