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
	/*	$where .= "AND empStat NOT IN('RS','IN','TR')";
	$chkemp = $inqTSObj->getEmployee($_SESSION['company_code'],$_GET['empNo'],$where);
	if($chkemp==0){

		echo "location.href = 'emp_certification.php'";

	}*/
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
			
			
			/*$this->SetFont('Arial','B','12'); 
			//$this->Cell(80,5,"Run Date: " . $newdate);
			$this->Cell(195,3,$this->compName,'0','1','C');
			$this->SetFont('Courier','','10'); 
			$this->Cell(195,3,$this->compAdd1,'0','1','C');
			$this->Cell(195,3,$this->compAdd2,'0','1','C');
			//$this->Cell(60,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();*/
			
			
			//$this->Cell(80,5,"Report ID: ".$this->reportid);
			
			$hTitle = "";
			$this->Cell(80,5,$hTitle);
			$this->Ln();
			$this->Cell(335,3,'','');
			$this->Ln(10);
			$this->SetFont('Arial','B','12');
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->Cell(193,6,'CERTIFICATE OF '.$this->heading.' CONTRIBUTION','0','','C');
			$this->Ln(15);
			$this->Ln(15);
			$this->SetFont('Arial','','10'); 
	
			$this->Cell(20,5,'','0','','J');
			$this->Cell(70,5,'This is to certify that '. (($this->empGender)=="her"?"MS.":"MR.").' '.$this->empName.' with '. $this->idNo,'0','1','J');
			$this->Cell(10,5,'','0','','J');
			$this->Cell(185,6,''.' is an employee of'.$this->compName.' '.'Since '.$this->empDateHired.' up to present. ','0','1','J');

			$this->Cell(5,6,'','0','','J');
			$this->Ln();
			$this->Cell(20,5,'','0','','J');
			$this->Cell(170,6,'The '.$this->heading.' premium deducted from '.(($this->empGender)=="her"?"her":"him").' for the period of '.(($this->empGender)=="her"?"her":"his") .' employment and were remitted  ','0','','J');
			$this->Ln();
			$this->Cell(10,6,'','0','','J');
			$this->Cell(185,6,'as per companys'.$this->comidno,'0','1','J');
			$this->Cell(5,6,'','0','','J');			
			$this->Ln(15);
			
			$this->SetFont('Arial','B','10');
			$this->Cell(5,6,'','0','','J');
			$this->Cell(41,6,'YEAR/MONTH',0,'','L');
			$this->Cell(25,6,'OR. NO.',0,'','L');
			$this->Cell(32,6,'DATE PAID',0,'','L');
			$this->Cell(25,6,'EE',0,'','L');
			$this->Cell(10,6,'ER',0,'','L');
			$this->Cell(40,6,'AMOUNT',0,'','R');
			$this->Ln();
			$this->Ln(5);
			
		}
		
		
		function gettblGovPay($agencyCd,$pdYear,$pdMonth)
		{
			$qrytblGov = "Select * from tblGovPayments 
							where compCode='".$_SESSION["company_code"]."'
							and agencyCd='".$agencyCd."'
							and pdYear='".$pdYear."'
							and pdMonth='".$pdMonth."'
							and remStatus='A'";
			//echo $qrytblGov."<br>";
			$restblGov = $this->execQry($qrytblGov);
			return $this->getSqlAssoc($restblGov);
		}
		
		function conParagraph()
		{
			/*$this->SetFont('Arial','','10'); 
			
			$this->Cell(20,5,'','0','','J');
			$this->Cell(170,5,'We hope you find everything in order.','0','1','J');
			$this->Ln();*/
			$this->SetFont('Arial','','10');
			$this->Cell(20,5,'','0','','J');
			$this->Cell(170,6,'This certification is issued upon the request of the above named employee for whatever  ','0','1','J');
			$this->Cell(5,6,'','0','','J');
			$this->Cell(185,6,'legal purpose it may serve.','0','','J');
			$this->Ln();
			$this->Ln();
			$this->Cell(20,5,'','0','','J');
			$this->Cell(170,6,'Done this' . " " .date ('dS \d\a\y \o\f F, Y'). ", Quezon city Philippines");
			$this->Ln();
			$this->Ln();

			$this->Cell(20,5,'','0','','J');
			$this->Cell(170,6,'For in behalf of' . " " .strtoupper($this->compName));
		}
		
		function displayContent($resQry,$agencyCd,$filter_mto,$filter_mfr,$filter_yto,$filter_yfr,$where,$output)
		{
			$this->SetFont('Arial','','10'); 
			$ctr_emp = 1;
			$grantotemp=0;
			$grantotemr=0;
			$grantotec=0;
			$grantottot=0;
			
			foreach($resQry as $resQry_val)
			{
				
				$sumofcont = 0;
				$pdMonth = $resQry_val["pdMonth"].'/'.date('d/Y');
				$this->Cell(8,5,'','0','','J');
				$this->Cell(38,5,$resQry_val["pdYear"].'  '.strtoupper(date('M', strtotime($pdMonth))),0,'','L');
				$arrtblGovPay = $this->gettblGovPay($agencyCd,$resQry_val["pdYear"],$resQry_val["pdMonth"]);
				//$arrtblGovPay["orNo"]
				$this->Cell(25,5,$arrtblGovPay["orNo"],0,'','L');
				$this->Cell(30,5,($arrtblGovPay["datePaid"]!=""?date('M. d, Y', strtotime($arrtblGovPay["datePaid"])):""),0,'','L');
				
					//Alejo additional code start
				if($agencyCd==1){
				$this->Cell(25,5,strtoupper($resQry_val["sssEmp"]),0,'','L');
				$this->Cell(25,5,strtoupper($resQry_val["sssEmplr"]),0,'','L');
			}
				if($agencyCd==2)
				{
				$this->Cell(25,5,strtoupper($resQry_val["hdmfEmp"]),0,'','L');
				$this->Cell(25,5,strtoupper($resQry_val["hdmfEmplr"]),0,'','L');
				}
				if($agencyCd==3)
				{
				$this->Cell(25,5,strtoupper($resQry_val["phicEmp"]),0,'','L');
				$this->Cell(25,5,strtoupper($resQry_val["phicEmplr"]),0,'','L');
				}		//Alejo additional code end


				if($agencyCd==1)
					$sumofcont = $resQry_val["sssEmp"]+$resQry_val["sssEmplr"]+$resQry_val["ec"];
				if($agencyCd==2)
					$sumofcont = $resQry_val["hdmfEmp"]+$resQry_val["hdmfEmplr"];
				if($agencyCd==3)
					$sumofcont = $resQry_val["phicEmp"]+$resQry_val["phicEmplr"];
				
				$this->Cell(25,5,($sumofcont!=0?number_format($sumofcont,2):""),0,'1','R');
				$totamt += $sumofcont;
			}
			$this->Ln();
			$this->SetFont('Arial','B','10');
			$this->Cell(100,6,'',0,'','L');
			$this->Cell(51,5,'TOTAL AMOUNT:',0,'','L');
			$this->Cell(25,5,($totamt!=0?number_format($totamt,2):""),0,'1','R');
			
			if($output==1)
			{
				$this->Cell(190,6,'  ','0','1','C'); 
				
				/*mtdGovHist*/
				$qryMtdGovtHist = "Select * from tblMtdGovtHist where compCode='".$_SESSION["company_code"]."' and convert(datetime,(convert(varchar,pdMonth)+'/30/'+convert(varchar,pdYear)))  between '".$filter_mfr."' and '".$filter_mto."' 
				  $where order by pdYear desc, pdMonth desc";
				$resMtdGovtHist = $this->execQry($qryMtdGovtHist);
				$arrMtdGovtHist = $this->getArrRes($resMtdGovtHist);
				if(count($arrMtdGovtHist)>=1)
				{
					foreach($arrMtdGovtHist as $arrMtdGovtHist_val)
					{
						$sumofcont = 0;
						$pdMonth = $arrMtdGovtHist_val["pdMonth"].'/'.date('d/Y');
						$this->Cell(5,5,'','0','','J');
						$this->Cell(43,5,$arrMtdGovtHist_val["pdYear"].'  '.strtoupper(date('M', strtotime($pdMonth))),0,'','L');
						$arrtblGovPay = $this->gettblGovPay($agencyCd,$arrMtdGovtHist_val["pdYear"],$arrMtdGovtHist_val["pdMonth"]);
						$this->Cell(35,5,$arrtblGovPay["orNo"],0,'','L');
						$this->Cell(35,5,($arrtblGovPay["dateCreated"]!=""?date('M. d, Y', strtotime($arrtblGovPay["dateCreated"])):""),0,'','L');
						$this->Cell(48,5,strtoupper($arrtblGovPay["bnkName"]),0,'','L');
						
						if($agencyCd==1)
							//$sumofcont = $arrMtdGovtHist_val["sssEmp"]+$arrMtdGovtHist_val["sssEmplr"]+$arrMtdGovtHist_val["ec"];
						if($agencyCd==2)
							//$sumofcont = $arrMtdGovtHist_val["hdmfEmp"]+$arrMtdGovtHist_val["hdmfEmplr"];
						if($agencyCd==3)
							//$sumofcont = $arrMtdGovtHist_val["phicEmp"]+$arrMtdGovtHist_val["phicEmplr"];
						
						$this->Cell(25,5,($sumofcont!=0?number_format($sumofcont,2):""),0,'1','R');
					}
				}
			}	
			
			$this->Ln(10);	
			$this->conParagraph();
			
			$this->Ln(25);

			$this->SetFont('Arial','UB','10');
			$this->Cell(20,5,'','0','','J');
			$this->Cell(40,6,'ALMA M. VILLANUEVA','0','','R'); 
			$this->Ln(5);
			$this->SetFont('Arial','B','9');	
			$this->Cell(20,5,'','0','','J');
			$this->Cell(41,6,'Payroll/Admin Department','0','','R');
		//	$this->Ln();
		//	$this->SetFont('Arial','B','9');	
		//	$this->Cell(179,6,"Date Issued : ".date("M. d, Y"),'0','','R');
		}
		
		
		function Footer()
		{
			$this->SetY(-20);
			//$this->Cell(190,1,'','T');
			$this->Ln();
			$this->SetFont('Courier','B',9);
			//$this->Cell(190,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}



	}

		$sqlEmp = "SELECT * FROM tblEmpMast  WHERE (compCode = '{$_SESSION['company_code']}') 
				   and empNo = '{$_GET['empNo']}'
				   AND empStat NOT IN('RS','IN','TR')  ";	   
		$resEmp = $inqTSObj->execQry($sqlEmp);		   
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		
		
			if ($numEmp == 0) {
				echo "<script type='text/javascript'>alert('No record Found');document.location='emp_certification.php'</script>";
			}else{

	$pdf = new PDF('P', 'mm', 'LETTER');
	$pdf->topType		=	$_GET["topType"];
	$payPd      		= 	$_GET['payPd'];
	$chopMonth 			= split("-",$payPd);
	$payPdYear 			= $chopMonth[3];
	$payPdNum 			= $chopMonth[4];
	$payPdMonthName		= $chopMonth[5];
	$catName 			= 	$inqTSObj->getEmpCatArt($_SESSION['company_code'], $_SESSION['pay_category']);
	$tbl				= 	$_GET["tbl"];
	$empNo         		= 	$_GET['empNo'];
	$empDiv        		= 	$_GET['empDiv'];
	$empDept       		= 	$_GET['empDept'];
	$empSect       		= 	$_GET['empSect'];
	$orderBy       		= 	$_GET['orderBy'];
	$topType			= 	$_GET['conType'];
	$monthto 			= $_GET["monthto"];
	$monthfr 			= $_GET["monthfr"];
	


	$filter_mfr = date("m/d/Y", strtotime($monthfr));
	$filter_mto = date("m/d/Y", strtotime($monthto));
	$choptoyearfr = date("Y", strtotime($filter_mfr));
	$choptoyearto = date("Y", strtotime($filter_mto));
	$choptomonthfr = date("m", strtotime($monthfr));
	$choptomonthto = date("m", strtotime($monthto));
	$choptomonthfrint =(int)$choptomonthfr; 
	$choptomonthtoint =(int)$choptomonthto;


	/*

	$chopMonthto 		= split("-",$monthto);
	$chopMonthfr 		= split("-",$monthfr);
	$filter_mfr 		= $chopMonthfr[4];
	$filter_mto 		= $chopMonthto[4];
	$filter_yfr 		= $chopMonthfr[3];
	$filter_yto 		= $chopMonthto[3];

	*/
	

	$pdf->compName		=	$inqTSObj->getCompanyName($_SESSION["company_code"]);
	$arrCompInfo 		= 	$inqTSObj->getCompanyInfo($_SESSION["company_code"]);
	$pdf->compAdd1		= 	$arrCompInfo["compAddr1"];
	$pdf->compAdd2		= 	$arrCompInfo["compAddr2"];

	
	$dispEmp = $inqTSObj->getUserInfo($_SESSION["company_code"] , $empNo, ""); 
	$empName = $dispEmp['empFirstName']." ".$dispEmp['empMidName']." ".$dispEmp['empLastName'];
	$pdf->empName = strtoupper($empName);
	$pdf->empDateHired = ($dispEmp['dateHired']!=""?date('M. d, Y', strtotime($dispEmp['dateHired'])):"   ");
	$pdf->empGender =	($dispEmp['empSex']=='F'?"her":"his");
	
	if($topType=='S')
	{
		$pdf->heading = "SSS";
		$pdf->reportid = "SSSCERT001";
		$pdf->idNo = "SSS ID No. ".$dispEmp['empSssNo'] ;	
		$pdf->comidno = " SSS Number ".$arrCompInfo["compSssNo"].".";
		$remType = 1;
	}
	elseif($topType=='PAG')
	{
		$pdf->heading = "PAG-IBIG";
		$pdf->reportid = "HDMFCERT001";
		$pdf->idNo = "PAG-IBIG ID No. ".$dispEmp['empPagibig'];	
		$pdf->comidno = " PAG-IBIG Number ".$arrCompInfo["compPagibig"].".";
		$remType = 2;
	}
	else
	{
		$pdf->heading = "PHILHEALTH";
		$pdf->reportid = "PHICCERT001";
		$pdf->idNo = "PHILHEALTH ID No. ".$dispEmp['empPhicNo'];
		$pdf->comidno = " PHILHEALTH Number ".$arrCompInfo["compPHealth"].".";
		$remType = 3;
	}
	


	$pdf->topType = $topType;
	/*$filter_mfr = date("m/d/Y",strtotime($filter_mfr."/30/".$filter_yfr));
	$filter_mto = date("m/d/Y",strtotime($filter_mto."/30/".$filter_yto));
	*/
	/*mtdGovt*/
	$where = ($empNo!=""?"and empNo='".$empNo."'":"");
	 $qryMtdGovt = "Select * from tblMtdGovt where compCode='".$_SESSION["company_code"]."' and pdYear BETWEEN '$choptoyearfr' AND '$choptoyearto'
AND pdMonth BETWEEN '$choptomonthfrint' AND '$choptomonthtoint' 
				  and empNo='$empNo' order by pdYear ASC, pdMonth ASC";
	
	$resMtdGovt = $inqTSObj->execQry($qryMtdGovt);
	$arrMtdGovt = $inqTSObj->getArrRes($resMtdGovt);
	if(count($arrMtdGovt)>=1){
		$pdf->AliasNbPages();
		$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrMtdGovt,$remType,$filter_mto,$filter_mfr,$filter_yto,$filter_yfr,$where,1);
	}	
	else{
		/*mtdGovtHist*/
		$qryMtdGovtHist = "Select * from tblMtdGovthist where compCode='".$_SESSION["company_code"]."' and pdYear BETWEEN '$choptoyearfr' AND '$choptoyearto'
AND pdMonth BETWEEN '$choptomonthfrint' AND '$choptomonthtoint' 
				  and empNo='$empNo' order by pdYear ASC, pdMonth ASC";
		$resMtdGovtHist = $inqTSObj->execQry($qryMtdGovtHist);
		$arrMtdGovtHist = $inqTSObj->getArrRes($resMtdGovtHist);
		if(count($arrMtdGovtHist)>=1){
			
			$pdf->AliasNbPages();
			$pdf->printedby = $inqTSObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
			$pdf->AddPage();
			$pdf->displayContent($arrMtdGovtHist,$remType,$filter_mto,$filter_mfr,$filter_yto,$filter_yfr,$where,2);
		}	
	}

				
}
	$pdf->Output();



	
?>
