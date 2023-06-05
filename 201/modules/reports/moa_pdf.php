<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	//include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	define('PARAGRAPH_STRING', '~~~'); 
	require_once("../../../includes/pdf/MultiCellTag/class.multicelltag.php"); 
	
################ GET TOTAL RECORDS ###############

############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
class PDF extends fpdf_multicelltag
{
	var $empName;	
	var $empaddress;
	var $oldcompany;
	var $newcompany;
	var $oldCompSign;
	var $newCompSign;
	var $newSignComp;
	var $dateTransferred;
	var $datehired;
	var $position;
	var $oldSigned;
	var $newSigned;
	var $oldSignedPos;
	var $newSignedPos;
	var $sss;
	var $newSignedIdentityNo;
	var $oldSignedIdentityNo;
	var $edate;
	function Content() {		
		$this->SetStyle("p","times","",11,"130,0,30");
		$this->SetStyle("pb","times","B",11,"130,0,30");
		$this->SetStyle("u","times","B",13,0);
		$this->SetStyle("t3","times","B",14,"203,0,48");
		$this->SetStyle("t4","arial","BI",11,"0,151,200");
		$this->SetStyle("hh","times","B",11,"255,189,12");
		$this->SetStyle("ss","arial","",7,0);
		$this->SetStyle("font","helvetica","",10,"0,0,255");
		$this->SetStyle("style","helvetica","BI",10,"0,0,220");
		$this->SetStyle("size","times","BI",13,"0,0,120");
		$this->SetStyle("color","times","BI",13,"0,255,255");
		
		$edate = date("F d, Y", strtotime($this->edate));
		$employeename = ucwords($this->empName);
		$empaddress = ucwords($this->empaddress);
		$oldcom = ucwords(strtolower($this->oldcompany));
		$newcom	= ucwords(strtolower($this->newcompany));
		$oldCompSign = $this->oldCompSign['compSignatory'].", ".$this->oldCompSign['compSignTitle'];
		$newCompSign = $this->newCompSign['compSignatory'].", ".$this->newCompSign['compSignTitle'];
		$oldSigned = $this->oldCompSign['compSignatory'];
		$newSigned = $this->newCompSign['compSignatory'];
		$oldSignedPos = $this->oldCompSign['compSignTitle'];
		$newSignedPos = $this->newCompSign['compSignTitle'];
		$oldSignedIdentityNo = $this->oldCompSign['compSignIdentityNo'];	
		$newSignedIdentityNo = $this->newCompSign['compSignIdentityNo'];	
		$datehired = date("F d, Y", strtotime($this->datehired));
		$position = $this->position;
		$sss = $this->sss;
		$day = date("d");
		$month = date("F");
		$year = date("Y");
		
		$this->SetMargins(23,20,23);
		$this->Ln(15);
		$this->SetFont('times', 'B', '13');
		$this->Cell(170,5,"Memorandum of Agreement",0,1,"C");
		$this->Ln(13);		
		$this->SetFont('times', '', '13');
		$this->MultiCell(170,0,"",0,"J",0);
		$this->MultiCellTag(170,5.5,"Know All Men By These Presents:",0,"J",0,true);
		$this->Ln(5);		
		$this->MultiCellTag(170,5.5,"	This Memorandum of Agreement made and entered into this ____ of ________________ <u>$year</u> at ____________________________ by and between:",0,"J",0,true);
		$this->Ln(8);	
		$this->Cell(25,5,"",0,0,"L");	
		$this->MultiCellTag(120,5.5,"<u>$oldcom</u>, a corporation duly organized and existing under the laws of the Republic of the Philippines with office address at 900 Romualdez St. Paco, Manila, represented herein by <u>$oldCompSign</u>;",0,"J",0,true);
		$this->Ln(8);
		$this->Cell(25,5,"",0,0,"L");		
		$this->MultiCellTag(120,5.5,"<u>$newcom</u>, corporation duly organized and existing under the laws of the Republic of the Philippines with office address at 900 Romualdez St. Paco, Manila, represented herein by <u>$newCompSign</u>;",0,"J",0,true);
		$this->Ln(8);
		$this->MultiCell(170,0,"-and-",0,"C",0);
		$this->Ln(8);
		$this->Cell(25,5,"",0,0,"L");		
		$this->MultiCellTag(120,5.5,"<u>$employeename</u>, Filipino, of legal age, with residence address at <u>$empaddress</u>, hereafter referred to as the \"Employee\",",0,"J",0,true);
		$this->Ln(8);
		$this->MultiCell(170,0,"-Witnesseth that-",0,"C",0);		
		$this->Ln(10);
		$this->MultiCellTag(170,5.5,"	1.   The Employee has been a <u>$position</u> for <u>$oldcom</u> since <u>$datehired</u>.",0,"J",0,true);
		$this->Ln(6);
		$this->MultiCellTag(170,5.5,"	2.   The parties have agreed that <u>$newcom</u> shall hire the employee effective <u>$edate</u> subject to the following stipulations:",0,"J",0,true);
		$this->Ln(6);
		$this->MultiCellTag(170,5.5,"	a.   <u>$newcom</u> shall absorb and assume all the obligations of <u>$oldcom</u> as the employer of the employee from the time the employee began working with <u>$oldcom</u> including but not limited to earned years of service, tenure, seniority, position, salaries, and the like.",0,"J",0,true);
		$this->Ln(6);
		$this->MultiCellTag(170,5.5,"	b.   The employee's position, salary, duties and responsibilities shall remain the same and no benefits earned through the years shall be diminished by <u>$newcom</u>",0,"J",0,true);
		$this->Ln(6);
		$this->MultiCellTag(170,5.5,"	c.   <u>$oldcom</u> shall cease to be the employer of record of the employee effective <u>$edate</u>.",0,"J",0,true);
		$this->Ln(6);
		$this->MultiCellTag(170,5.5,"  	   IN WITNESS WHEREOF, parties have hereunto affixed their signatures this ____ day of ________________, <u>$year</u>.",0,"J",0,true);
		$this->Ln(55);
		$this->Cell(75,5,ucwords(strtolower($oldcom)),0,0,"C");
		$this->Cell(20,5,"",0,0,"L");
		$this->Cell(75,5,ucwords(strtolower($newcom)),0,0,"C");
		$this->Ln(10);
		$this->Cell(85,5,"By:",0,0,"L");	
		$this->Cell(85,5,"By:",0,0,"L");	
		$this->Ln(17);
		$this->Cell(75,5,strtoupper($oldSigned),0,0,"C");
		$this->Cell(20,5,"",0,0,"L");	
		$this->Cell(75,5,strtoupper($newSigned),0,1,"C");	
		$this->Cell(75,5,$oldSignedPos,0,0,"C");	
		$this->Cell(20,5,"",0,0,"L");
		$this->Cell(75,5,$newSignedPos,0,0,"C");	
		$this->Ln(20);
		$this->Cell(170,5,strtoupper($employeename),0,1,"C");	
		$this->Cell(170,5,"Employee",0,0,"C");	
		$this->Ln(15);
		$this->Cell(170,5,"Signed in the presence of:",0,1,"L");
		$this->Ln(5);
		$this->Cell(75,5,"________________________",0,0,"C");
		$this->Cell(20,5,"",0,0,"L");	
		$this->Cell(75,5,"________________________",0,1,"C");	
		$this->Ln(10);
		$this->Cell(170,5,"A C K N O W L E D G M E N T",0,1,"C");
		$this->Ln(10);
		$this->Cell(170,5,"Republic of the Philippines) S.S.",0,1,"L");
		$this->Cell(170,5,"______________________)",0,1,"L");
		$this->Cell(170,5,"x-------------------------------x",0,1,"L");
		$this->Ln(10);
		$this->MultiCellTag(170,5,"	Before me, a Notary Public for and in the .........................., this ..... day of .................., $year, personally appeared:",0,"J",0,true);
		$this->Ln(5);
		$this->Cell(80,5,"Name",0,0,"L");	
		$this->MultiCellTag(90,5,"Competent Evidence of Identity Number/Date Issued/ Official Agency",0,"C",0,true);	
		$this->Ln(10);
		$this->Cell(95,5,$employeename,0,0,"L");	
		$this->Cell(75,5,"SSS No. ".$sss,0,1,"L");
		$this->Cell(95,5,$newSigned,0,0,"L");	
		$this->Cell(75,5,$newSignedIdentityNo,0,1,"L");
		$this->Cell(95,5,$oldSigned,0,0,"L");	
		$this->Cell(75,5,$oldSignedIdentityNo,0,1,"L");	
		$this->Ln(10);
		$this->MultiCellTag(170,5,"known to me to be the same persons who executed the foregoing ____________________ and acknowledged to me that the same is their free and voluntary act and deed.",0,"J",0,true);				
		$this->Ln(5);
		$this->MultiCellTag(170,5,"	WITNESS MY HAND AND SEAL this ........ day of ...................., $year.",0,"J",0,true);				
		$this->Ln(15);
		$this->Cell(170,6,"Doc. No. __________",0,1,"L");	
		$this->Cell(170,6,"Page No. __________",0,1,"L");	
		$this->Cell(170,6,"Book No. __________",0,1,"L");	
		$this->Cell(170,6,"Series of ".$year,0,0,"L");			
	}
}	
function getTransCompany($compCode) {
	switch($compCode) {
			case 1:
				$compName = "PUREGOLD JUNIOR SUPERMARKET, INC.";
				$db = "pgjr_payroll..";
			break;	
			case 2:
				$compName = "PUREGOLD PRICE CLUB, INC.";
				$db = "pg_payroll..";
			break;	
			case 4:
				$compName = "PUREGOLD DUTY FREE  CLARK INC.";
				$db = "DFClark_payroll..";
			break;				
			case 5:
				$compName = "PUREGOLD DUTY FREE SUBIC INC.";
				$db = "DFSubic_payroll..";
			break;
		default:
			$compName = "";
			$db = "";
		break;
	}
	$arr['compName'] = $compName;
	$arr['db'] = $db;
	return $arr;
}

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
//$type = $_GET['type'];
$pdf=new PDF();
$pdf->Open();
$pdf->FPDF($orientation='P',$unit='mm',$format='LEGAL');
	
	//get effectivity date
	$pdf->edate = $_GET['edate'];
	
	//get transferred data from tblTransferredEmployees
	$qry = $inqTSObj->execQry("Select * from tblEmpMast where empNo='".$_GET['empno']."' and empStat='RG'");
	$resQry = $inqTSObj->getSqlAssoc($qry);
	
	$pdf->empName = mb_strtolower($resQry['empFirstName'])." ". mb_strtolower($resQry['empMidName'])." ". mb_strtolower($resQry['empLastName']);
	
	$qryOldCompany = $inqTSObj->getCompanyArt($resQry['compCode']);
	$pdf->oldcompany = $qryOldCompany['compName'];	
	
	//get new company database name
	$pdf->newSignComp = getTransCompany($_GET['compcode']);
	
	$pdf->newcompany = $pdf->newSignComp['compName'];
	//$pdf->dateTransferred = $resQry['dateTransferred'];
	$oldcompcode=$resQry['compCode'];
	$empno = $resQry['empNo'];	

	//get old company database name 
	$pdf->oldCompSign = $inqTSObj->getCompanyInfo($resQry['compCode']);


	//get new company signatory from tblCompany
	$qryNewCompSign = "Select * from ".$pdf->newSignComp['db']."tblCompany where compCode='".$_GET['compcode']."'";
	$resNewCompSign = $inqTSObj->getSqlAssoc($inqTSObj->execQry($qryNewCompSign)); 
	$pdf->newCompSign = $resNewCompSign;

	//get empoyee data from tblEmpMast of currently user's logged database
	$qryempOtherInfos="SELECT tblEmpMast.empMidName,tblEmpMast.empFirstName,tblEmpMast.empLastName, tblMunicipalityRef.municipalityDesc,
	tblProvinceRef.provinceDesc, tblCitizenshipRef.citizenDesc, tblReligionRef.relDesc, tblPayBank.bankDesc, 
	tblEmpMast.empNo,tblPosition.posShortDesc,tblEmpMast.dateReg,empPayCat,tblEmpMast.empEndDate, tblEmpMast.empMrate,
					empSex = 
						CASE empSex 
						  WHEN 'M' THEN 'Male'
						  WHEN 'F' then 'Female'
						END,
					empMarStat = 
						CASE empMarStat
						  WHEN 'SG' THEN 'Single'
						  WHEN 'ME' THEN 'Married'
						  WHEN 'SP' THEN 'Separated'
						  WHEN 'WI' THEN 'Widow(er)'
						END,
					empPayType = 
						CASE empPayType
						  WHEN 'D' THEN 'Daily'
						  WHEN 'M' THEN 'Monthly'
						END,
					employmentTag = 
						CASE employmentTag
						  WHEN 'RG' THEN 'Regular'
						  WHEN 'PR' THEN 'Probationary'
						  WHEN 'CN' THEN 'Contractual'
						END,
					empPayGrp = 
						CASE empPayGrp
						  WHEN '1' THEN 'Group 1'
						  WHEN '2' THEN 'Group 2'
						END, 
	tblTeu.teuDesc, tblTimeShiftRef.shiftDesc, tblEmpMast.empStat, tblEmpMast.empAddr1, tblEmpMast.empAddr2, tblMunicipalityRef.municipalityDesc, 
	tblProvinceRef.provinceDesc, tblEmpMast.empMRate, tblEmpMast.empSssNo, tblEmpMast.empTin, tblEmpMast.dateHired
	FROM tblEmpMast 
	LEFT OUTER JOIN tblTimeShiftRef ON tblEmpMast.compCode = tblTimeShiftRef.compCode AND tblEmpMast.empShiftId = tblTimeShiftRef.shiftId 
	LEFT OUTER JOIN tblPosition ON tblEmpMast.empPosId = tblPosition.posCode AND tblEmpMast.compCode = tblPosition.compCode 
	LEFT OUTER JOIN tblTeu ON tblEmpMast.empTeu = tblTeu.teuCode 
	LEFT OUTER JOIN tblPayBank ON tblEmpMast.compCode = tblPayBank.compCode AND tblEmpMast.empBankCd = tblPayBank.bankCd 
	LEFT OUTER JOIN tblReligionRef ON tblEmpMast.empReligion = tblReligionRef.relCd 
	LEFT OUTER JOIN tblCitizenshipRef ON tblEmpMast.empCitizenCd = tblCitizenshipRef.citizenCd 
	LEFT OUTER JOIN tblMunicipalityRef ON tblEmpMast.empMunicipalityCd = tblMunicipalityRef.municipalityCd 
	LEFT OUTER JOIN tblProvinceRef on tblEmpMast.empProvinceCd=tblProvinceRef.provinceCd
	WHERE tblEmpMast.empNo='$empno' and tblEmpMast.compCode='$oldcompcode'";
	$ress = $inqTSObj->getSqlAssoc($inqTSObj->execQry($qryempOtherInfos));			  
	$pdf->empaddress = mb_strtolower($ress['empAddr1'])." ".mb_strtolower($ress['empAddr2'])." ".mb_strtolower($ress['municipalityDesc'])." ".mb_strtolower($ress['provinceDesc']);
	$pdf->datehired = $ress['dateHired'];
	$pdf->position = ucwords(strtolower($ress['posShortDesc']));
	$pdf->sss = $ress['empSssNo'];
$pdf->AddPage();	
$pdf->Content();
$pdf->SetMargins(20,0,20);
$pdf->Output('MOA.pdf','D');
?>