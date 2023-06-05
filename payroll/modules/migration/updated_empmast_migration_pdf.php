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
	include("empmast_migration.obj.php");
	
	$migEmpMastObj= new migEmpMastObj();
	$sessionVars = $migEmpMastObj->getSeesionVars();
	
	
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
				
				$this->Cell(70,5,"Report ID: DIFFEMPMAST");
				
				if($this->repType==1)
					$this->Cell(140,5,'List of New Employees in the Updated tblEmpMast','0','','C');
				else
					$this->Cell(140,5,'List of Modifications made to Employees in the Updated tblEmpMast','0','','C');
					
				$this->Ln();
				
				$this->Cell(335,3,'','0');
				$this->Ln();
				$this->SetFont('Courier','B','9');
				$this->Cell(37,6,'BRANCH NAME',0,'0','L');
				$this->SetFont('Courier','','9');
				$this->Cell(100,6,$this->branchName,0,'1','L');
				$this->SetFont('Courier','B','9');
				$this->Cell(37,6,'LOCATION NAME',0,'','L');
				$this->SetFont('Courier','','9');
				$this->Cell(100,6,$this->locName,0,'1','L');
				$this->Ln();
				$this->SetFont('Courier','B','9');
				if($this->repType==1)
				{
					$this->Cell(30,6,'EMP. NO.',0,'0','L');
					$this->Cell(45,6,'NAME',0,'0','L');
					$this->Cell(25,6,'DATE HIRED',0,'0','L');
					$this->Cell(65,6,'DIVISION',0,'0','L');
					$this->Cell(65,6,'DEPARTMENT',0,'0','L');
					$this->Cell(30,6,'PAY TYPE',0,'0','L');
					$this->Cell(30,6,'EMP. TEU',0,'0','L');
					$this->Cell(30,6,'M. RATE',0,'0','L');
					$this->Cell(20,6,'MIN. WAGE',0,'1','L');
				}
				
				
		}	
		
		function displayContentDetails($arr_NewEmp)
		{
			global $empList;
			
			$this->SetFont('Courier','','8');
			if($this->repType==1)
			{
				foreach($arr_NewEmp as $arr_NewEmp_val)
				{
					$empList.=$arr_NewEmp_val["empNo"].",";
					
					$this->Cell(30,6,$arr_NewEmp_val["empNo"],0,'0','L');
					$this->Cell(45,6,$arr_NewEmp_val["empLastName"].", ".$arr_NewEmp_val["empFirstName"][0].". ".$arr_NewEmp_val["empMidName"][0].".",0,'0','L');
					$this->Cell(25,6,date("Y-m-d", strtotime($arr_NewEmp_val["dateHired"])),0,'0','L');
					$empDivDesc = $this->getDivDescArt($arr_NewEmp_val["compCode"], $arr_NewEmp_val["empDiv"]);
					$this->Cell(65,6,$empDivDesc["deptDesc"],0,'0','L');
					
					$empDeptDesc = $this->getDeptDescGen($arr_NewEmp_val["compCode"], $arr_NewEmp_val["empDiv"],$arr_NewEmp_val["empDepCode"]);
					$this->Cell(65,6,$empDeptDesc["deptDesc"],0,'0','L');
					$this->Cell(30,6,($arr_NewEmp_val["empPayType"]=='M'?"MONTHLY":"DAILY"),0,'0','L');
					$this->Cell(30,6,$arr_NewEmp_val["empTeu"],0,'0','L');
					$this->Cell(30,6,number_format($arr_NewEmp_val["empMrate"],2),0,'0','L');
					$this->Cell(20,6,($arr_NewEmp_val["empWageTag"]=='Y'?"YES":"NO"),0,'1','L');
				}
			}
			else
			{
				$this->SetFont('Courier','B','8');
				$this->Cell(335,6,'LIST OF EMPLOYEES WITH CHANGE IN BASIC INFORMATION',0,'1','L');
				$this->Cell(20,6,'EMP. NO.',0,'0','C');
				$this->Cell(70,6,'LAST NAME',0,'0','C');
				$this->Cell(70,6,'FIRST NAME',0,'0','C');
				$this->Cell(70,6,'MIDDLE NAME',0,'0','C');
				$this->Cell(50,6,'PAY TYPE',0,'0','C');
				$this->Cell(40,6,'EMP. STAT',0,'1','C');
				$this->Cell(20,6,'',0,'0','C');
				$this->Cell(35,6,'OLD VAL.',0,'0','C');
				$this->Cell(35,6,'NEW VAL',0,'0','C');
				$this->Cell(35,6,'OLD VAL.',0,'0','C');
				$this->Cell(35,6,'NEW VAL',0,'0','C');
				$this->Cell(35,6,'OLD VAL.',0,'0','C');
				$this->Cell(35,6,'NEW VAL',0,'0','C');
				$this->Cell(25,6,'OLD VAL.',0,'0','C');
				$this->Cell(25,6,'NEW VAL',0,'0','C');
				$this->Cell(20,6,'OLD VAL.',0,'0','C');
				$this->Cell(20,6,'NEW VAL',0,'0','C');
				$this->SetFont('Courier','','8');
				$this->Ln();
				foreach($arr_NewEmp as $arr_NewEmp_val)
				{
					$empList.=$arr_NewEmp_val["empNo"].",";
						if(($arr_NewEmp_val["o_lname"]!=$arr_NewEmp_val["c_lname"]) or ($arr_NewEmp_val["o_fname"]!=$arr_NewEmp_val["c_fname"]) or ($arr_NewEmp_val["o_mname"]!=$arr_NewEmp_val["c_mname"])or($arr_NewEmp_val["o_emppaytype"]!=$arr_NewEmp_val["c_emppaytype"])or($arr_NewEmp_val["o_empstat"]!=$arr_NewEmp_val["c_empstat"]))
						{	
							$this->Cell(20,6,$arr_NewEmp_val["empNo"],0,'0','L');
							$this->Cell(35,6,$arr_NewEmp_val["o_lname"],0,'0','L');
							$this->Cell(35,6,$arr_NewEmp_val["c_lname"],0,'0','L');
							$this->Cell(35,6,$arr_NewEmp_val["o_fname"],0,'0','L');
							$this->Cell(35,6,$arr_NewEmp_val["c_fname"],0,'0','L');
							$this->Cell(35,6,$arr_NewEmp_val["o_mname"],0,'0','L');
							$this->Cell(35,6,$arr_NewEmp_val["c_mname"],0,'0','L');
							$this->Cell(25,6,$arr_NewEmp_val["o_emppaytype"],0,'0','L');
							$this->Cell(25,6,$arr_NewEmp_val["c_emppaytype"],0,'0','L');
							$this->Cell(20,6,$arr_NewEmp_val["o_empstat"],0,'0','L');
							$this->Cell(20,6,$arr_NewEmp_val["c_empstat"],0,'0','L');
							$this->Ln();
						}
				}
				$this->Ln(10);
				
				//Change in Organizational Hierarchy
				$this->SetFont('Courier','B','8');
				$this->Cell(335,6,'LIST OF EMPLOYEES WITH CHANGE IN ORGANIZATION HIERARCHY',0,'1','L');
				$this->Cell(30,6,'EMP. NO.',0,'0','C');
				$this->Cell(40,6,'NAME',0,'0','C');
				$this->Cell(130,6,'DIVISION',0,'0','C');
				$this->Cell(130,6,'DEPARTMENT',0,'1','C');
				$this->Cell(30,6,'',0,'0','C');
				$this->Cell(40,6,'',0,'0','C');
				$this->Cell(65,6,'OLD VAL.',0,'0','C');
				$this->Cell(65,6,'NEW VAL',0,'0','C');
				$this->Cell(65,6,'OLD VAL.',0,'0','C');
				$this->Cell(65,6,'NEW VAL',0,'0','C');
				$this->SetFont('Courier','','8');
				
				$this->Ln();
				foreach($arr_NewEmp as $arr_NewEmp_val)
				{
						if(($arr_NewEmp_val["o_empdiv"]!=$arr_NewEmp_val["c_empdiv"])or($arr_NewEmp_val["o_empdep"]!=$arr_NewEmp_val["c_empdep"]))
						{	
							$this->Cell(30,6,$arr_NewEmp_val["empNo"],0,'0','L');
							$this->Cell(40,6,$arr_NewEmp_val["o_lname"].", ".$arr_NewEmp_val["o_fname"][0].".".$arr_NewEmp_val["o_mname"][0].".",0,'0','L');
							
							$o_empDivDesc = $this->getDivDescArt($arr_NewEmp_val["compCode"], $arr_NewEmp_val["o_empdiv"]);
							$this->Cell(65,6,$arr_NewEmp_val["o_empdiv"]."=".$o_empDivDesc["deptDesc"],0,'0','L');
							
							$c_empDivDesc = $this->getDivDescArt($arr_NewEmp_val["compCode"], $arr_NewEmp_val["c_empdiv"]);
							$this->Cell(65,6,$arr_NewEmp_val["c_empdiv"]."-".$c_empDivDesc["deptDesc"],0,'0','L');
							
							$o_empDeptDesc = $this->getDeptDescGen($arr_NewEmp_val["compCode"],$arr_NewEmp_val["o_empdiv"],$arr_NewEmp_val["o_empdep"]);
							$this->Cell(65,6,$arr_NewEmp_val["o_empdep"]."-".$o_empDeptDesc["deptDesc"],0,'0','L');
					
							$c_empDeptDesc = $this->getDeptDescGen($arr_NewEmp_val["compCode"],$arr_NewEmp_val["c_empdiv"],$arr_NewEmp_val["c_empdep"]);
							$this->Cell(65,6,$arr_NewEmp_val["c_empdep"]."-".$c_empDeptDesc["deptDesc"],0,'0','L');
							$this->Ln();
						}
				}
				
				$this->Ln();
				//Change in Rate
				$this->SetFont('Courier','B','8');
				$this->Cell(335,6,'LIST OF EMPLOYEES WITH CHANGE IN SALARY INFORMATION',0,'1','L');
				$this->Cell(30,6,'EMP. NO.',0,'0','C');
				$this->Cell(40,6,'NAME',0,'0','C');
				$this->Cell(35,6,'EMP. TEU',0,'0','C');
				$this->Cell(30,6,'WAGE TAG',0,'0','C');
				$this->Cell(65,6,'M. RATE',0,'0','C');
				$this->Cell(65,6,'D. RATE',0,'0','C');
				$this->Cell(65,6,'H. RATE',0,'1','C');
				
				$this->Cell(30,6,'',0,'0','C');
				$this->Cell(40,6,'',0,'0','C');
				$this->Cell(17.5,6,'OLD VAL.',0,'0','C');
				$this->Cell(17.5,6,'NEW VAL',0,'0','C');
				$this->Cell(15,6,'OLD VAL.',0,'0','C');
				$this->Cell(15,6,'NEW VAL',0,'0','C');
				$this->Cell(32.5,6,'OLD VAL.',0,'0','C');
				$this->Cell(32.5,6,'NEW VAL',0,'0','C');
				$this->Cell(32.5,6,'OLD VAL.',0,'0','C');
				$this->Cell(32.5,6,'NEW VAL',0,'0','C');
				$this->Cell(32.5,6,'OLD VAL.',0,'0','C');
				$this->Cell(32.5,6,'NEW VAL',0,'0','C');
				$this->SetFont('Courier','','8');
				
				$this->Ln();
				foreach($arr_NewEmp as $arr_NewEmp_val)
				{
						if(($arr_NewEmp_val["o_empteu"]!=$arr_NewEmp_val["c_empteu"])or($arr_NewEmp_val["o_empwagetag"]!=$arr_NewEmp_val["c_empwagetag"])or($arr_NewEmp_val["o_empmrate"]!=$arr_NewEmp_val["c_empmrate"])or($arr_NewEmp_val["o_empdrate"]!=$arr_NewEmp_val["c_empdrate"])or($arr_NewEmp_val["o_emphrate"]!=$arr_NewEmp_val["c_emphrate"]))
						{	
							$this->Cell(30,6,$arr_NewEmp_val["empNo"],0,'0','L');
							$this->Cell(40,6,$arr_NewEmp_val["o_lname"].", ".$arr_NewEmp_val["o_fname"][0].".".$arr_NewEmp_val["o_mname"][0].".",0,'0','L');
							
							$this->Cell(17.5,6,$arr_NewEmp_val["o_empteu"],0,'0','L');
							$this->Cell(17.5,6,$arr_NewEmp_val["c_empteu"],0,'0','L');
							$this->Cell(15,6,$arr_NewEmp_val["o_empwagetag"],0,'0','L');
							$this->Cell(15,6,$arr_NewEmp_val["c_empwagetag"],0,'0','L');
							
							$this->Cell(32.5,6,number_format($arr_NewEmp_val["o_empmrate"],2),0,'0','R');
							$this->Cell(32.5,6,number_format($arr_NewEmp_val["c_empmrate"],2),0,'0','R');
							$this->Cell(32.5,6,number_format($arr_NewEmp_val["o_empdrate"],2),0,'0','R');
							$this->Cell(32.5,6,number_format($arr_NewEmp_val["c_empdrate"],2),0,'0','R');
							$this->Cell(32.5,6,number_format($arr_NewEmp_val["o_emphrate"],2),0,'0','R');
							$this->Cell(32.5,6,number_format($arr_NewEmp_val["c_emphrate"],2),0,'0','R');
							$this->Ln();
						}
				}
			}
		}
	
		function displayEmpNo($empList)
		{
			$arrempList = explode(",", $empList);
			$this->Cell(20,6,'Check Data : ',0,'1','L');
			$this->Cell(20,6,'Select * from tblEmpMast where empNo in (',0,'1','L');
			for($i=0; $i<=sizeof($arrempList)-2; $i++)
			{
				if($i%2)
				{
					$this->Cell(20,6,"'".$arrempList[$i]."'".",",0,'1','L');
					
				}
				else
				{
					$this->Cell(20,6,"'".$arrempList[$i]."'".",",0,'','L');
				}
			}
			$this->Cell(20,6,');',0,'1','L');
			$this->Ln(10);
			$this->Cell(20,6,'Delete Record : ',0,'1','L');
			$this->Cell(20,6,'Delete from tblEmpMast where empNo in (',0,'1','L');
			for($i=0; $i<=sizeof($arrempList)-2; $i++)
			{
				if($i%2)
				{
					$this->Cell(20,6,"'".$arrempList[$i]."'".",",0,'1','L');
					
				}
				else
				{
					$this->Cell(20,6,"'".$arrempList[$i]."'".",",0,'','L');
				}
			}
			$this->Cell(20,6,');',0,'1','L');
			
			
			$this->Ln(10);
			$this->Cell(20,6,'Insert : ',0,'1','L');
			$this->Cell(20,6,'Insert into tblEmpMast(compCode,empNo,empLastName,empFirstName,empMidName,empLocCode,empBrnCode,empDiv,empDepCode
			,empSecCode,empPosId,dateHired,empStat,dateReg,dateResigned,empRestDay,empTeu,',0,'1','L');
			$this->Cell(20,6,'empTin,empSssNo,empPagibig,empBankCd,empAcctNo,empPayGrp,empPayType,empPayCat,empWageTag,empPrevTag,
			empAddr1,empAddr2,empAddr3,empMarStat,empSex,empBday,empReligion,empMrate,empDrate,',0,'1','L');
			$this->Cell(20,6,'empHrate,empOtherInfo,empNickName,empBplace,empHeight,empWeight,empCitizenCd,empBloodType
			,empEndDate,empLevel,empSubSection,empCityCd,empSpouseName,empBuildDesc,empComplexDesc,',0,'1','L');
			$this->Cell(20,6,'empEyeColorDesc,empHairDesc,empPhicNo,empAbsencesTag,empLatesTag,empUtTag,empOtTag,
			empPicture,empImageSize,empShiftId,annualTag,empRank)',0,'1','L');
			$this->Cell(20,6,'Select compCode,empNo,empLastName,empFirstName,empMidName,empLocCode,empBrnCode,empDiv,empDepCode
			,empSecCode,empPosId,dateHired,empStat,dateReg,dateResigned,empRestDay,empTeu,',0,'1','L');
			$this->Cell(20,6,'empTin,empSssNo,empPagibig,empBankCd,empAcctNo,empPayGrp,empPayType,empPayCat,empWageTag,empPrevTag,
			empAddr1,empAddr2,empAddr3,empMarStat,empSex,empBday,empReligion,empMrate,empDrate,',0,'1','L');
			$this->Cell(20,6,'empHrate,empOtherInfo,empNickName,empBplace,empHeight,empWeight,empCitizenCd,empBloodType
			,empEndDate,empLevel,empSubSection,empCityCd,empSpouseName,empBuildDesc,empComplexDesc,',0,'1','L');
			$this->Cell(20,6,'empEyeColorDesc,empHairDesc,empPhicNo,empAbsencesTag,empLatesTag,empUtTag,empOtTag,
			empPicture,empImageSize,empShiftId,annualTag,empRank',0,'1','L');
			$this->Cell(20,6,'from tblEmpMast_Paradox where empNo in(',0,'1','L');
			$this->Cell(20,6,'',0,'1','L');
			for($i=0; $i<=sizeof($arrempList)-2; $i++)
			{
				if($i%2)
				{
					$this->Cell(20,6,"'".$arrempList[$i]."'".",",0,'1','L');
					
				}
				else
				{
					$this->Cell(20,6,"'".$arrempList[$i]."'".",",0,'','L');
				}
			}
			$this->Cell(20,6,');',0,'1','L');
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
	$pdf->company = $migEmpMastObj->getCompanyName($_SESSION['company_code']);
	$arr_branch = $migEmpMastObj->getEmpBranchArt($_SESSION["company_code"],$_GET["empBrnCode"]);
	$pdf->branchName = $arr_branch["brnDesc"];
	$arr_loc = $migEmpMastObj->getEmpBranchArt($_SESSION["company_code"], $_GET["empLocCode"]=='S'?$_GET["empBrnCode"]:"0001");
	$pdf->locName = $arr_loc["brnDesc"];	
	
	//List of New Employees
	$qryListEmp = "Select * from tblEmpMast_Paradox where compCode='".$_SESSION["company_code"]."' 
					and empLocCode='".($_GET["empLocCode"]=='S'?$_GET["empBrnCode"]:"0001")."' 
					and empBrnCode='".$_GET["empBrnCode"]."'
					and empNo not in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' 
										and empLocCode='".($_GET["empLocCode"]=='S'?$_GET["empBrnCode"]:"0001")."' 
										and empBrnCode='".$_GET["empBrnCode"]."')
					order by empLastName";
	$resListEmp = $migEmpMastObj->execQry($qryListEmp);
	$arrListEmp = $migEmpMastObj->getArrRes($resListEmp);
	if(count($arrListEmp)>0)
	{
		$pdf->repType = 1;
		$pdf->AliasNbPages();
		$pdf->printedby =  $migEmpMastObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContentDetails($arrListEmp);
	}
	
	//List of Modifications
	
	
	
	
	$qryListEmp = "Select emp.compCode,emp.empNo,
					emp.empFirstName as o_fname,empPar.empFirstName as c_fname,
					emp.empLastName as o_lname,empPar.empLastName as c_lname, 
					emp.empMidName as o_mname, empPar.empMidName as c_mname ,
					emp.empDiv as o_empdiv, empPar.empDiv as c_empdiv,
					emp.empDepCode as o_empdep,empPar.empDepCode as c_empdep,
					emp.empStat as o_empstat,empPar.empStat as c_empstat,
					emp.empTeu as o_empteu,empPar.empTeu as c_empteu,
					emp.empPayType as o_emppaytype,empPar.empPayType as c_emppaytype,
					emp.empMrate as o_empmrate,empPar.empMrate as c_empmrate,
					emp.empDrate as o_empdrate,empPar.empDrate as c_empdrate,
					emp.empHrate as o_emphrate,empPar.empHrate as c_emphrate,
					emp.empWageTag as o_empwagetag,empPar.empWageTag as c_empwagetag
					from tblEmpMast emp, tblEmpMast_Paradox empPar
					where emp.empNo=empPar.empNo
					and emp.compCode='".$_SESSION["company_code"]."' 
										and emp.empLocCode='".($_GET["empLocCode"]=='S'?$_GET["empBrnCode"]:"0001")."' 
										and emp.empBrnCode='".$_GET["empBrnCode"]."'
					and 
					(
						(emp.empFirstName<>empPar.empFirstName) or 
						(emp.empLastName<>empPar.empLastName) or 
						(emp.empMidName<>empPar.empMidName)or
						(emp.empDiv <> empPar.empDiv) or
						(emp.empDepCode <>empPar.empDepCode) or
						(emp.empStat <>empPar.empStat) or 
						(emp.empTeu <>empPar.empTeu) or
						(emp.empPayType <>empPar.empPayType) or 
						(emp.empMrate <>empPar.empMrate) or
						(emp.empDrate <>empPar.empDrate) or
						(emp.empHrate <>empPar.empHrate) or
						(emp.empWageTag <>empPar.empWageTag)
					)
					order by o_lname";;
	$resListEmp = $migEmpMastObj->execQry($qryListEmp);
	$arrListEmp = $migEmpMastObj->getArrRes($resListEmp);
	if(count($arrListEmp)>0)
	{
		$pdf->repType = 2;
		$pdf->AliasNbPages();
		$pdf->printedby =  $migEmpMastObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContentDetails($arrListEmp);
	}
	
	if($_GET["w_Script"]=="1")
	{
		$pdf->AddPage();
		$pdf->displayEmpNo($empList);
	}
	
	$pdf->Output();
?>



