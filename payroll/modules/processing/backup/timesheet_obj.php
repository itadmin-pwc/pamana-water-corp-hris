<?
class inqTSObj extends commonObj 
{

	var $compCode;
	var $empNo;
	var $empName;
	var $empDiv;
	var $empDept;
	var $empSect;
	var $groupType;
	var $catType;
	var $orderBy;
	
	
	function getAllPeriod($compCode,$groupType,$catType,$modulo) { // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
		$modulo="";
		if ($modulo>"") $moduloNew = " AND (pdNumber % 2) = $modulo "; else $moduloNew = "";
		$qry = "SELECT TOP 100 PERCENT  { fn MONTHNAME(pdPayable) }  + ' ' + CONVERT(varchar, YEAR(pdPayable)) AS perMonth, 
				convert(varchar,MIN(pdNumber))+'-'+convert(varchar,MAX(pdNumber))+'-'+convert(varchar,MAX(pdYear))+'-'+convert(varchar,MAX(pdYear))+'-'+convert(varchar,MAX({ fn MONTH(pdPayable) }))+'-'+convert(varchar,MAX({ fn MONTHNAME(pdPayable) })) AS pdNumber
				FROM tblPayPeriod
				WHERE compCode = '".$_SESSION["company_code"]."' AND payGrp = '".$_SESSION["pay_group"]."' AND payCat = '".$_SESSION["pay_category"]."' 
				GROUP BY  { fn MONTHNAME(pdPayable) } + ' ' + CONVERT(varchar, YEAR(pdPayable))
				ORDER BY MAX(pdPayable)";
		
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	function getEmpInq() {
		if ($this->empNo>"") {$empNo1 = " AND (empNo LIKE '{$this->empNo}%')";} else {$empNo1 = "";}
		if ($this->empName>"") {$empName1 = " AND (empLastName LIKE '{$this->empName}%' OR empFirstName LIKE '{$this->empName}%' OR empMidName LIKE '{$this->empName}%')";} else {$empName1 = "";}
		if ($this->empDiv>"" && $this->empDiv>0) {$empDiv1 = " AND (empDiv = '{$this->empDiv}')";} else {$empDiv1 = "";}
		if ($this->empDept>"" && $this->empDept>0) {$empDept1 = " AND (empDepCode = '{$this->empDept}')";} else {$empDept1 = "";}
		if ($this->empSect>"" && $this->empSect>0) {$empSect1 = " AND (empSecCode = '{$this->empSect}')";} else {$empSect1 = "";}
		if ($this->groupType<3) {$groupType1 = " AND (empPayGrp = '{$this->groupType}')";} else {$groupType1 = "";}
		if ($this->orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ASC ";} 
		if ($this->orderBy==2) {$orderBy1 = " ORDER BY empNo ASC ";} 
		if ($this->orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ASC ";}
		if ($this->catType>0) {$catType1 = " AND (empPayCat = '{$this->catType}')";} else {$catType1 = "";}
		$qry = "SELECT * FROM tblEmpMast 
						 WHERE compCode = '{$this->compCode}'
						 AND empStat NOT IN('RS','IN','TR') 
						 $empNo1 $empDiv1 $empName1 $empDept1 $empSect1 $groupType1 $catType1 $orderBy1 ";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	
	function getEmpTotalByCat($empDiv) {
		$qry = "SELECT TOP 100 PERCENT COUNT(*) AS totRec, tblPayCat.payCat, tblPayCat.payCatDesc
				FROM tblDepartment INNER JOIN
                tblEmpMast ON tblDepartment.divCode = tblEmpMast.empDiv INNER JOIN
                tblPayCat ON tblEmpMast.empPayCat = tblPayCat.payCat
				WHERE (tblDepartment.compCode = '{$this->compCode}') AND (tblDepartment.deptLevel = 1) AND (tblDepartment.deptStat = 'A') AND (tblPayCat.payCatStat = 'A') AND
                (tblEmpMast.compCode = '{$this->compCode}') AND (tblPayCat.compCode = '{$this->compCode}') AND 
				(tblEmpMast.empDiv = '{$empDiv}')  AND (tblDepartment.divCode = '{$empDiv}') AND tblEmpMast.empStat NOT IN('RS','IN','TR') 
				GROUP BY tblDepartment.divCode, tblPayCat.payCat, tblPayCat.payCatDesc
				ORDER BY tblDepartment.divCode, tblPayCat.payCat, tblPayCat.payCatDesc";
		$res = $this->execQry($qry);
		return $this->getArrRes($res);
	}
	
	
	function getOpenPeriod($compCode,$grp,$cat) {
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate FROM tblPayPeriod 
				WHERE pdStat = 'O' AND 
					compCode = '$compCode' ";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
	
	
	function getOpenPer($pdNum,$pdYear)
	{
		$qrypd = "Select * from tblPayPeriod where pdYear = '".$pdYear."' and pdNumber='".$pdNum."' and compCode='".$_SESSION["company_code"]."' and payGrp='".$_SESSION["pay_group"]."' and payCat='".$_SESSION["pay_category"]."'";
		$respd = $this->execQry($qrypd);
		return $this->getSqlAssoc($respd);
	}
	
	function chkCont($pdYear,$pdMonth,$tbl,$con,$chkf)
	{
		if($chkf==1)
			$field = "tblGov.compCode,tblGov.pdYear,tblGov.pdMonth,tblGov.empNo,
					mtdEarnings, sssEmp, sssEmplr, ec, phicEmp,phicEmplr,hdmfEmp,
					hdmfEmplr";
		else
			$field = "count(tblGov.empNo) as cntEmp";
		
		$qryChk = "Select $field from $tbl tblGov, tblEmpMast tblEmp
					where
					tblGov.empNo=tblEmp.empNo 
					and tblGov.compCode='".$_SESSION["company_code"]."'
					and pdYear='".$pdYear."'
					and pdMonth='".$pdMonth."'
					and empPayGrp='".$_SESSION["pay_group"]."'
					and empPayCat='".$_SESSION["pay_category"]."'
					and empStat NOT IN('RS','IN','TR')
					$con
					";
	
		$resChk = $this->execQry($qryChk);
		return $this->getSqlAssoc($resChk);
	}

	function generateGovRemittace($pdYear, $pdMonth)
	{
		 $qryMtdGovt = "SELECT     mtd.empNo, empMast.empSssNo, empMast.dateHired, empMast.dateResigned, mtd.mtdEarnings, mtd.sssEmp, mtd.sssEmplr, mtd.ec, mtd.phicEmp, 
							  mtd.phicEmplr, mtd.hdmfEmp, mtd.hdmfEmplr
					FROM         tblMtdGovtHist mtd INNER JOIN
							  tblEmpMast empMast ON mtd.empNo = empMast.empNo
					WHERE     (mtd.pdYear = '".$pdYear."') AND (mtd.pdMonth = '".$pdMonth."')
					 AND (mtd.compCode = '".$_SESSION["company_code"]."')
					 AND (empMast.compCode = '".$_SESSION["company_code"]."') 
					 and mtd.empNo in (010002408,010001915)
					ORDER BY mtd.empNo
					";
		$resmtdGovt = $this->execQry($qryMtdGovt );
		return $this->getArrRes($resmtdGovt );

	}

	function chkEmptblMtdGovtHist($compCode, $pdYear, $pdMonth)
	{
		$qrymtdGovtHist = "Select * from tblMtdGovtHist where compCode='".$compCode."' and pdYear='".$pdYear."' and pdMonth='".$pdMonth."'";
		return $this->execQry($qrymtdGovtHist);
	}
	
	
	function RemTextfile($compCode, $pdYear, $pdMonth)
	{
		$qrymtdGovtHist = "exec sp_RemittanceGovt $pdYear,$pdMonth,$compCode";
							
		$resmtdGovtHist = $this->execQry($qrymtdGovtHist);
		return $this->getArrRes($resmtdGovtHist);
	}
	
	function getSSSExemptEmployee() {
		$qry = "SELECT empNo FROM tblNonEmpGov WHERE 	(cat = 'sss')"	;
		$arr =  $this->getArrRes($this->execQry($qry));
		$str = "";
		foreach ($arr as $val) {
			if ($str=="")
				$str = $val['empNo'];
			else
				$str = ",".$val['empNo'];
		}
		
		return explode(",",$str);
	}
	function Sss_TxtFile($compCode, $pdYear, $pdMonth)
	{	
		$arrmtdGovtHist = $this->RemTextfile($compCode, $pdYear, $pdMonth);
		$arrcompName = $this->getCompany($compCode);
		
		$txtOut = "00";
		$compName = substr($arrcompName["compName"], 0, 30);
		$txtOut .= $compName.$this->Space(30-strlen($compName));
		$compSssNo = substr(str_replace("-","",$arrcompName["compSssNo"]), 0, 10).$this->Space(69);
		$txtOut .= sprintf('%02d',$pdMonth).$pdYear.$compSssNo.$this->Space(10-strlen($compSssNo))."\r\n";
		
		$tot_Ec = $tot_Cont = 0;
		
		$arrSSSExempt = $this->getSSSExemptEmployee();		
		foreach($arrmtdGovtHist as $arrmtdGovtHist_val)
		{
			
			$EmpShare=$EmpCont = $EmpEc =  0;
			$Emp_mDateHired=$Emp_yDateHired=$Emp_mDateRes=$Emp_yDateRes = $empDRes = $Emp_DateHired = "";
			
			if($arrmtdGovtHist_val["sssEmp"]!=0 && !in_array($arrmtdGovtHist_val["empNo"],$arrSSSExempt))
			{
				$empLname = empty($arrmtdGovtHist_val["empLastName"])?"":trim(substr($arrmtdGovtHist_val["empLastName"], 0, 15));
				$empFname = empty($arrmtdGovtHist_val["empFirstName"])?"":trim(substr($arrmtdGovtHist_val["empFirstName"], 0, 15));
				$empMname = empty($arrmtdGovtHist_val["empMidName"])?" ":substr($arrmtdGovtHist_val["empMidName"], 0, 1);
				
				if(!empty($arrmtdGovtHist_val["empSssNo"]))
					$empSssNo = substr(str_replace('-', '', $arrmtdGovtHist_val["empSssNo"]), 0, 10);
				
				$txtOut .= '20'.$empLname.$this->Space(15-strlen($empLname)).$empFname.$this->Space(15-strlen($empFname)).$empMname;
				$txtOut .= $empSssNo;
				
				$EmpShare = empty($arrmtdGovtHist_val["sssEmp"])?0:$arrmtdGovtHist_val["sssEmp"];
				$EmrShare = empty($arrmtdGovtHist_val["sssEmplr"])?0:$arrmtdGovtHist_val["sssEmplr"];
				$EmpEc    = empty($arrmtdGovtHist_val["ec"])?0:$arrmtdGovtHist_val["ec"];
/*				if (date('Y') == 2012 && date('m') == 2 && in_array($arrmtdGovtHist_val["empNo"],array('010002727','0107000005'))) {				
					if ($arrmtdGovtHist_val["empNo"]=='010002727') {
						$EmpShare = round($EmpShare-500,2);
						$EmrShare = round($EmrShare-1060,2);
						$EmpEc    = number_format($EmpEc-30,2);
					} else {
						$EmpShare = round($EmpShare-333.30,2);
						$EmrShare = round($EmrShare-706.70,2);
						$EmpEc    = number_format($EmpEc-10,2);
					}
				}
*/				$EmpCont = $EmpShare + $EmrShare;
				$EmpCont = sprintf('%.2f', $EmpCont);
				$EmpCont = sprintf('%4.2f', $EmpCont);
				$EmpCont	= sprintf('%7s',$EmpCont);
	
				$EmpEc = sprintf('%5s',$EmpEc);
				
				if($arrmtdGovtHist_val["dateHired"]!="")
				{
					$Emp_mDateHired = date("m", strtotime($arrmtdGovtHist_val["dateHired"]));
					$Emp_yDateHired = date("Y", strtotime($arrmtdGovtHist_val["dateHired"]));
					$Emp_DateHired = date("mdy", strtotime($arrmtdGovtHist_val["dateHired"]));
				}
				else
				{
					$Emp_DateHired = $this->Space(6);
				}
				
				if($arrmtdGovtHist_val["dateResigned"]!="")
				{
					$Emp_mDateRes = date("m", strtotime($arrmtdGovtHist_val["dateResigned"]));
					$Emp_yDateRes = date("Y", strtotime($arrmtdGovtHist_val["dateResigned"]));
				}
				
				if(($Emp_mDateHired==sprintf('%02d',$pdMonth))&&($Emp_yDateHired==$pdYear))
					$empDRes = 1;
				elseif(($Emp_mDateRes==sprintf('%02d',$pdMonth))&&($Emp_yDateRes==$pdYear))
					$empDRes = 2;
				elseif($arrmtdGovtHist_val["mtdEarnings"]==0)
					$empDRes = 3;
				else
					$empDRes = "N";	
					
				
				if($pdMonth==1 or $pdMonth==4 or $pdMonth==7 or $pdMonth==10) 
				{		//Falls on Jan, Apr, Jul, or Oct
					$txtOut .=$this->Space(1).$EmpCont.$this->Space(4)."0.00".$this->Space(4)."0.00".
							$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(1).
							$EmpEc.$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(6).$empDRes.$Emp_DateHired."\r\n";
/*					$txtOut .=$this->Space(1).$EmpCont.$this->Space(4)."0.00".$this->Space(4)."0.00".
							$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(1).
							$EmpEc.$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(6).$empDRes."\r\n";							
*/							
				}
				elseif($pdMonth==2 or $pdMonth==5 or $pdMonth==8 or $pdMonth==11) 
				{		//falls on FEB, MAY, AUG, & NOV
					$txtOut .=$this->Space(4)."0.00".$this->Space(1).$EmpCont.$this->Space(4)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(1).$EmpEc.$this->Space(2)."0.00".$this->Space(6).$empDRes.$Emp_DateHired."\r\n";
					/*$txtOut .=$this->Space(4)."0.00".$this->Space(1).$EmpCont.$this->Space(4)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(1).$EmpEc.$this->Space(2)."0.00".$this->Space(6).$empDRes."\r\n";*/
				}
				else 
				{   // falls on MAR, JUN, SEP, & DEC
					$txtOut .=$this->Space(4)."0.00".$this->Space(4)."0.00".$this->Space(1).$EmpCont.$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(1).$EmpEc.$this->Space(6).$empDRes.$Emp_DateHired."\r\n";
/*					$txtOut .=$this->Space(4)."0.00".$this->Space(4)."0.00".$this->Space(1).$EmpCont.$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(2)."0.00".$this->Space(1).$EmpEc.$this->Space(6).$empDRes."\r\n";
*/				}
				
				$tot_Cont += $EmpCont;
				$tot_Cont= sprintf('%.2f', $tot_Cont);
				$tot_Cont= sprintf('%10.2f', $tot_Cont);
				$tot_Cont= sprintf('%11s',$tot_Cont);
				
				$tot_Ec += $EmpEc;
				$tot_Ec= sprintf('%.2f', $tot_Ec);
				$tot_Ec= sprintf('%8.2f', $tot_Ec);
				$tot_Ec= sprintf('%9s',$tot_Ec);
			}
		}
		
		if($pdMonth==1 or $pdMonth==4 or $pdMonth==7 or $pdMonth==10) 
		{		//Falls on Jan, Apr, Jul, or Oct
			$txtOut .= "99".$this->Space(1).$tot_Cont.$this->Space(1).$this->Space(7)."0.00".$this->Space(1).$this->Space(7)."0.00".
						$this->Space(1).$this->Space(5)."0.00".$this->Space(1).$this->Space(5)."0.00".$this->Space(1).$this->Space(5)."0.00".$this->Space(1).
						$tot_Ec.$this->Space(1).$this->Space(5)."0.00".$this->Space(1).$this->Space(5)."0.00".$this->Space(20);
		}
		elseif($pdMonth==2 or $pdMonth==5 or $pdMonth==8 or $pdMonth==11) 
		{		//falls on FEB, MAY, AUG, & NOV
			$txtOut .= "99".$this->Space(8)."0.00".$this->Space(1).$tot_Cont.$this->Space(8)."0.00".$this->Space(6)."0.00".$this->Space(6)."0.00".$this->Space(6)."0.00".
			  $this->Space(6)."0.00".$this->Space(1).$tot_Ec.$this->Space(6)."0.00".$this->Space(20);
		}
		else 
		{   // falls on MAR, JUN, SEP, & DEC
			$txtOut .= "99".$this->Space(8)."0.00".$this->Space(8)."0.00".$this->Space(1).$tot_Cont.$this->Space(6)."0.00".$this->Space(6)."0.00".$this->Space(6)."0.00".$this->Space(6)."0.00".
			  $this->Space(6)."0.00".$this->Space(1).$tot_Ec.$this->Space(20);
		}
		
		return $txtOut;
	}
	
	function Pag_TxtFile($compCode, $pdYear, $pdMonth)
	{	
		$arrmtdGovtHist = $this->RemTextfile($compCode, $pdYear, $pdMonth);
		$arrcompName = $this->getCompany($compCode);
		$txtOut = "EYERID,EYEENO,LNAME,FNAME,MID,PERCOV1,PFRDATE1,PFRNO1,PERAMT1,PERAMT2,PFRAMT,GOVTYPE,FILLER,HDMFID,BALFWD87,BALFWD88,BALFWD89,COMPANY,BIRTHDATE"."\r\n";
		
		foreach($arrmtdGovtHist as $arrmtdGovtHist_val)
		{
			$txtOut.=$arrcompName["compPagibig"].",".$arrmtdGovtHist_val["empPagibig"].",".strtoupper($arrmtdGovtHist_val["empLastName"]).",".strtoupper($arrmtdGovtHist_val["empFirstName"]).",".strtoupper($arrmtdGovtHist_val["empMidName"]).",".",".",".",".$arrmtdGovtHist_val["hdmfEmp"].",".$arrmtdGovtHist_val["hdmfEmplr"].",".",".",".",".$arrmtdGovtHist_val["empPagibig"].",".",".",".",".strtoupper(str_replace(","," ", $arrcompName["compName"])).",".($arrmtdGovtHist_val["empBday"]!=""?date("m/d/Y", strtotime($arrmtdGovtHist_val["empBday"])):"")."\r\n";
		}
		
		return $txtOut;
	}
	
	function SSSLoan_TxtFile($compCode, $pdYear, $pdMonth)
	{	
		$arrSSSLoan = $this->Loans($compCode,$pdYear,$pdMonth,1);
		$arrSSSLoanAdj = $this->loanAdjustment($pdYear,$pdMonth, '5902');
		
		$date =date('ym') ;
		$arrcompName = $this->getCompany($compCode);
		$txtOut = '00'.str_replace("-","",$arrcompName['compSssNo']).$arrcompName['compName'].$this->Space(5).$date."\r\n";
		$totAmt=0;
		$ctr = 1;
		foreach($arrSSSLoan as $SSSLoan_val)
		{
			$empAmort = 0;
			$empAmort = $SSSLoan_val["Amount"];
			foreach($arrSSSLoanAdj  as $arrSSSLoanAdj_val)
			{
				if($SSSLoan_val["empNo"]==$arrSSSLoanAdj_val["empNo"])
				$empAmort+=$arrSSSLoanAdj_val["trnAmountD"];
			}
			
			
			$Amort = number_format($empAmort,2);
			$Amort = str_replace(",","",$Amort);
			$Amort = str_replace(".","",$Amort);
			
			
			$LoanAmt = round($SSSLoan_val["lonAmt"]);
			$LoanAmt = str_replace(",","",$LoanAmt);
			$LoanAmt = str_replace(".","",$LoanAmt);
			$totAmt += round($SSSLoan_val["Amount"],2);
			$lonStart = date('ymd',strtotime($SSSLoan_val["lonGranted"]));
			$empSssNo = substr(str_replace('-', '', $SSSLoan_val["empSssNo"]), 0, 10);
			
		//	echo $SSSLoan_val["empLastName"]."=".$Amort."\n";
			
			$txtOut .="10".$empSssNo.strtoupper(substr($SSSLoan_val["empLastName"],0,15)).$this->Space(15-strlen(substr($SSSLoan_val["empLastName"],0,15))).strtoupper(substr($SSSLoan_val["empFirstName"],0,15)).$this->Space(15-strlen(substr($SSSLoan_val["empFirstName"],0,15))).strtoupper($SSSLoan_val["empMidName"][0]).$this->Space(1)."S".$lonStart."0".$LoanAmt.$this->Space(10-strlen($LoanAmt),'0').$this->Space(9-strlen($Amort),'0').$Amort.$this->Space(1).($SSSLoan_val["lonStat"]=='T'?"T":"")."\r\n";
			unset($Amort,$LoanAmt);
		}
		
		
		$totAmt = str_replace(".","",$totAmt);
		$txtOut .='99'.$this->Space(4-strlen(count($arrSSSLoan)),'0').count($arrSSSLoan).$this->Space(17-strlen($totAmt),'0').$totAmt."\r\n";
		return $txtOut;
	}		
	
	function Loans($compCode, $pdYear, $pdNumber,$lonCode) {
	  $sqlHDMFLoan = "SELECT tblEmpMast.empNo,tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblEmpLoansDtlHist.ActualAmt AS Amount, tblEmpLoans.lonAmt,  tblEmpLoans.lonWidInterst,
                      tblEmpMast.empSssNo, empStat = CASE empStat WHEN 'TR' THEN 'T' ELSE ' ' END,lonStart,lonGranted,pdNumber, empBday, empPagibig, tblEmpLoansDtlHist.lonRefNo,  tblEmpLoans.lonStat
					  FROM tblEmpMast INNER JOIN
                      tblEmpLoansDtlHist ON tblEmpMast.empNo = tblEmpLoansDtlHist.empNo AND tblEmpMast.compCode = tblEmpLoansDtlHist.compCode INNER JOIN
                      tblEmpLoans ON tblEmpLoansDtlHist.compCode = tblEmpLoans.compCode AND tblEmpLoansDtlHist.empNo = tblEmpLoans.empNo AND 
                      tblEmpLoansDtlHist.lonTypeCd = tblEmpLoans.lonTypeCd AND tblEmpLoansDtlHist.lonRefNo = tblEmpLoans.lonRefNo
						WHERE (tblEmpLoansDtlHist.lonTypeCd like '".$lonCode."%')
						AND tblEmpMast.compCode='$compCode'
						AND pdYear='$pdYear'
						AND dedtag IN ('Y','P')
						AND pdNumber IN (Select pdNumber from tblPayPeriod where compCode='$compCode' AND Month(pdPayable) = '$pdNumber' AND pdYear='$pdYear')
						order by empLastName, empFirstName, empMidName";
		return $this->getArrRes($this->execQry($sqlHDMFLoan));						
	}
	
	function loanAdjustment($pdYear,$pdMonth, $trnCode=NULL)
	{
		if($trnCode!="")
			$trnCode_filter = "and (tblDeductionsHist.trnCode IN (N'5902')) ";
		else
			$trnCode_filter = "and (tblDeductionsHist.trnCode IN (N'5901')) ";
		
		 $sqlLoanAdj = "SELECT     tblDeductionsHist.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblLoanType.lonTypeShortDesc, 
						tblEmpLoans.lonRefNo, tblEmpLoans.lonWidInterst, tblDeductionsHist.trnAmountD, 
						tblEmpLoans.lonPayments + tblDeductionsHist.trnAmountD AS lonPayments, tblEmpLoans.lonCurbal + tblDeductionsHist.trnAmountD * - 1 AS lonCurbal,
						tblDeductionsHist.pdYear, tblEmpLoans.lonGranted, tblDeductionsHist.pdNumber, tblEmpLoans.lonStart,
						empBday, empPagibig
						FROM         tblDeductionsHist LEFT OUTER JOIN
								  tblEmpMast ON tblDeductionsHist.compCode = tblEmpMast.compCode AND tblDeductionsHist.empNo = tblEmpMast.empNo LEFT OUTER JOIN
								  tblLoanType INNER JOIN
								  tblEmpLoans ON tblLoanType.compCode = tblEmpLoans.compCode AND tblLoanType.lonTypeCd = tblEmpLoans.lonTypeCd ON 
								  tblDeductionsHist.compCode = tblEmpLoans.compCode AND tblDeductionsHist.empNo = tblEmpLoans.empNo AND tblEmpLoans.lonTypeCd IN (22) AND
								   tblEmpLoans.lonStat IN ('C', 'T')
						WHERE     tblDeductionsHist.compCode='".$_SESSION["company_code"]."' ".$trnCode_filter." 
						AND pdNumber IN (Select pdNumber from tblPayPeriod where compCode='".$_SESSION["company_code"]."' AND Month(pdPayable) = '$pdMonth' AND pdYear='$pdYear') AND tblEmpLoans.lonRefNo Not IN (
Select lonrefNo from tblLonRefNo)
						AND pdYear='$pdYear'
						";
		return $this->getArrRes($this->execQry($sqlLoanAdj));		
	}
	
	function LoanQuartPayments($compCode, $pdYear, $pdMonth,$lonCode) {
	 $sqlLoanPymnts = "Select * from tblEmpLoansDtlHist 
							where compCode='$compCode'
							AND pdYear='$pdYear'
							AND lonTypeCd LIKE '$lonCode%'
							AND dedtag IN ('Y','P')
							AND pdNumber IN (Select pdNumber from tblPayPeriod where compCode='$compCode' AND Month(pdPayable) IN  ($pdMonth) AND pdYear='$pdYear') order by pdNumber
							";
		return $this->getArrRes($this->execQry($sqlLoanPymnts));								
	}
	function PagLoan_TxtFile($compCode, $pdYear, $pdMonth)
	{	
		$arrHDMFLoan = $this->Loans($compCode,$pdYear,$pdMonth,22);
		$txtOut = "BILLPERIOD,LASTNAME,FIRSTNAME,MIDNAME,AMORT"."\r\n";
		$date =date('ym') ;
		foreach($arrHDMFLoan as $HDMFLoan_val)
		{
			$Amort = number_format($HDMFLoan_val["Amount"],2);
			$Amort = str_replace(",","",$Amort);
			$txtOut.="$date,".strtoupper($HDMFLoan_val["empLastName"]).",".strtoupper($HDMFLoan_val["empFirstName"]).",".strtoupper($HDMFLoan_val["empMidName"][0]).",".$Amort."\r\n";
			unset($Amort);
		}
		return $txtOut;
	}
	
	function Ph_TxtFile($compCode, $pdYear, $pdMonth)
	{
		
	}
}

?>