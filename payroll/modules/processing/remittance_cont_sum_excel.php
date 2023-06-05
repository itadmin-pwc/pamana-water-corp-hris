<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','D:\wamp\php\PEAR');
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/config.php");
	include("timesheet_obj.php");
	require_once 'Spreadsheet/Excel/Writer.php';
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$workbook = new Spreadsheet_Excel_Writer();
	$inqTSObj=new inqTSObj();
	
	//Set Font Style
	$headerFormat = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'center',
						  num_format=>0));
	$headerFormat->setFontFamily('Arial Narrow');
	
	$payCatFormat = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'left',
						  num_format=>0));
	$payCatFormat->setFontFamily('Arial Narrow');
	
	
	$dtl_Format = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 0,
									  'border' => 1,
									  'Align' => 'left',
						  num_format=>0));
	$dtl_Format->setFontFamily('Arial Narrow');
	
	$dtl_Format_right = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 0,
									  'border' => 1,
									  'Align' => 'right',
						  num_format=>0));
	$dtl_Format_right->setFontFamily('Arial Narrow');
	$dtl_Format_right->setNumFormat('0.00');
	
	$grand_Format_right = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 0,
									  'border' => 1,
									  'Align' => 'center',
						  num_format=>0));
	$grand_Format_right->setFontFamily('Arial Narrow');
	$grand_Format_right->setNumFormat('0.00');
	
	
 	
	
	
	$filename = "GOVERNMENT REMITTANCE OF ".$arr_Comp_Name["compName"]." FOR THE MONTH OF "." - ".$_GET['pdMonth']." - ".$_GET['pdYear'].".xls";
	
	
	//Set Variables
	$pdYear		= $_GET["pdYear"];
	$pdMonth	= $_GET["pdMonth"];
	$pdNum1 	= $_GET["pdNum1"];
	$pdNum2     = $_GET["pdNum2"];
	
	$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
	$arr_Comp_Name = $inqTSObj->getCompany($_SESSION["company_code"]);
	
	
	//Queries
	
	//Get Summary of Contributions
	$qry_mtdGovt = "SELECT     mtd.empNo,empMast.empLastName, empmast.empFirstName, empMast.empMidName, empMast.empPagibig, empMast.empSssNo, empMast.dateHired, empMast.dateResigned, mtd.mtdEarnings, mtd.sssEmp, mtd.sssEmplr, mtd.ec, mtd.phicEmp, 
                      mtd.phicEmplr, mtd.hdmfEmp, mtd.hdmfEmplr
					FROM         tblMtdGovtHist mtd INNER JOIN
										  tblEmpMast empMast ON mtd.empNo = empMast.empNo
					WHERE     (mtd.pdYear = '".$pdYear."') AND (mtd.pdMonth = '".$pdMonth."') AND (mtd.compCode = '".$_SESSION["company_code"]."') and mtd.compCode='".$_SESSION["company_code"]."'
					ORDER BY mtd.empNo;";	
	$arr_mtdGovt = $inqTSObj->getArrRes($inqTSObj->execQry($qry_mtdGovt));
	
	for($group=1; $group<=2; $group++)
	{
		for($payCat=1; $payCat<=3; $payCat++)
		{
			$qry_mtdGovtHist = "SELECT     sum(mtd.sssEmp) as sssEmp, sum(mtd.sssEmplr) as sssEmplr, sum(mtd.ec) as ec, sum(mtd.phicEmp) as phicEmp, 
								sum(mtd.phicEmplr) as phicEmplr, sum(mtd.hdmfEmp) as hdmfEmp, sum(mtd.hdmfEmplr) as hdmfEmplr
								FROM         tblMtdGovtHist mtd 
								WHERE     (mtd.pdYear = '".$pdYear."') AND (mtd.pdMonth = '".$pdMonth."') AND (mtd.compCode = '".$_SESSION["company_code"]."') 
								and mtd.compCode='".$_SESSION["company_code"]."' 
								and empNo in 
									(Select empNo from tblPayrollSummaryHist
									 where compCode='".$_SESSION["company_code"]."' and payGrp='".$group."' and PayCat='".$payCat."' and pdNumber in ('".$pdNum1."','".$pdNum2."') 
									 and pdYear='".$pdYear."'); ";
			$sum_mtdGovtHist[$group][$payCat] = $inqTSObj->getSqlAssoc($inqTSObj->execQry($qry_mtdGovtHist));
			
			
		}
	}
	
	
	
	$workbook->send($filename);
	
	//Excel Output
	
	//Display the Per Detail of Contribution
	$arr_f_row = array('empNo', 'empLastName','empFirstName','empMidName','empPagibig','empSssNo','dateHired','dateResigned','mtdEarnings','sssEmp','sssEmplr','ec','phicEmp','phicEmplr','hdmfEmp','hdmfEmplr');
	$col_ctr = 0;
	
	$worksheet=&$workbook->addWorksheet($arr_Comp_Name["compShort"]);
	
	
	foreach($arr_f_row as $arr_f_row_val)
	{	
		$worksheet->setColumn(0,$col_ctr,15);
		$worksheet->write(0,$col_ctr,$arr_f_row_val,$headerFormat);
		$col_ctr++;
	}
	
	$ctr_dtl_row = 1;
	
	foreach($arr_mtdGovt as $arr_mtdGovt_val)
	{
		$worksheet->write($ctr_dtl_row,0," ".$arr_mtdGovt_val["empNo"],$dtl_Format);
		$worksheet->write($ctr_dtl_row,1," ".$arr_mtdGovt_val["empLastName"],$dtl_Format);
		$worksheet->write($ctr_dtl_row,2," ".$arr_mtdGovt_val["empFirstName"],$dtl_Format);
		$worksheet->write($ctr_dtl_row,3," ".$arr_mtdGovt_val["empMidName"],$dtl_Format);
		$worksheet->write($ctr_dtl_row,4," ".$arr_mtdGovt_val["empPagibig"],$dtl_Format);
		$worksheet->write($ctr_dtl_row,5,$arr_mtdGovt_val["empSssNo"],$dtl_Format);
		$worksheet->write($ctr_dtl_row,6,date("m/d/Y", strtotime($arr_mtdGovt_val["dateHired"])),$dtl_Format);
		$worksheet->write($ctr_dtl_row,7,($arr_mtdGovt_val["dateResigned"]!=""?date("m/d/Y", strtotime($arr_mtdGovt_val["dateResigned"])):""),$dtl_Format);
		$worksheet->write($ctr_dtl_row,8,$arr_mtdGovt_val["mtdEarnings"],$dtl_Format_right);
		$worksheet->write($ctr_dtl_row,9,$arr_mtdGovt_val["sssEmp"],$dtl_Format_right);
		$worksheet->write($ctr_dtl_row,10,$arr_mtdGovt_val["sssEmplr"],$dtl_Format_right);
		$worksheet->write($ctr_dtl_row,11,$arr_mtdGovt_val["ec"],$dtl_Format_right);
		$worksheet->write($ctr_dtl_row,12,$arr_mtdGovt_val["phicEmp"],$dtl_Format_right);
		$worksheet->write($ctr_dtl_row,13,$arr_mtdGovt_val["phicEmplr"],$dtl_Format_right);
		$worksheet->write($ctr_dtl_row,14,$arr_mtdGovt_val["hdmfEmp"],$dtl_Format_right);
		$worksheet->write($ctr_dtl_row,15,$arr_mtdGovt_val["hdmfEmplr"],$dtl_Format_right);
		
		$ctr_dtl_row++;
		
	}
	
	$col_ctr = 0;
	
	
	
	//Create another Sheet for Summary
	$worksheet=&$workbook->addWorksheet("SUMMARY");
	
	//Display Summary of Contribution Per Group and Per Payroll Category
	$sum_hdr_column = array('GROUP 1', 'SSS EMP.','SSS EMPLR.','EC','PHIC EMP.', 'PHIC EMPLR.','HDMF EMP.','HDMF EMPLR.');
	foreach($sum_hdr_column as $sum_hdr_column_val)
	{
		$worksheet->setColumn(0,$col_ctr,20);
		$worksheet->write(0,$col_ctr,$sum_hdr_column_val,$headerFormat);
		$col_ctr++;
	}
	
	
	$col_ctr = 1;
	$sum_hdr_f_column = array('EXECUTIVE', 'CONFIDENTIAL','NON CONFIDENTIAL');
	foreach($sum_hdr_f_column as $sum_hdr_f_column_val)
	{
		$worksheet->setColumn($col_ctr,0,20);
		$worksheet->write($col_ctr,0,$sum_hdr_f_column_val,$payCatFormat);
		
		$arrSumCont = $sum_mtdGovtHist[1][$col_ctr];
		$worksheet->write($col_ctr ,1,$arrSumCont["sssEmp"],$dtl_Format_right);
		$worksheet->write($col_ctr ,2,$arrSumCont["sssEmplr"],$dtl_Format_right);
		$worksheet->write($col_ctr ,3,$arrSumCont["ec"],$dtl_Format_right);
		$worksheet->write($col_ctr ,4,$arrSumCont["phicEmp"],$dtl_Format_right);
		$worksheet->write($col_ctr ,5,$arrSumCont["phicEmplr"],$dtl_Format_right);
		$worksheet->write($col_ctr ,6,$arrSumCont["hdmfEmp"],$dtl_Format_right);
		$worksheet->write($col_ctr ,7,$arrSumCont["hdmfEmplr"],$dtl_Format_right);
		
		$grandTotal_sss+=$arrSumCont["sssEmp"]+$arrSumCont["sssEmplr"]+$arrSumCont["ec"]; 
		$grandTotal_hdmf+=$arrSumCont["hdmfEmp"]+$arrSumCont["hdmfEmplr"];
		$grandTotal_phic+=$arrSumCont["phicEmp"]+$arrSumCont["phicEmplr"];
		$col_ctr++;
	}
	
	$col_ctr = 0;
	$sum_hdr_column = array('GROUP 2', '','','','', '','','');
	foreach($sum_hdr_column as $sum_hdr_column_val)
	{
		$worksheet->setColumn(5,$col_ctr,20);
		$worksheet->write(5,$col_ctr,$sum_hdr_column_val,$headerFormat);
		
		
		$col_ctr++;
	}
	
	
	$col_ctr = 6;
	$pay_ctr = 1;
	$sum_hdr_f_column = array('EXECUTIVE', 'CONFIDENTIAL','NON CONFIDENTIAL');
	foreach($sum_hdr_f_column as $sum_hdr_f_column_val)
	{
		$worksheet->setColumn($col_ctr,0,20);
		$worksheet->write($col_ctr,0,$sum_hdr_f_column_val,$payCatFormat);
		
		$arrSumCont = $sum_mtdGovtHist[2][$pay_ctr];
		
		$worksheet->write($col_ctr ,1,$arrSumCont["sssEmp"],$dtl_Format_right);
		$worksheet->write($col_ctr ,2,$arrSumCont["sssEmplr"],$dtl_Format_right);
		$worksheet->write($col_ctr ,3,$arrSumCont["ec"],$dtl_Format_right);
		$worksheet->write($col_ctr ,4,$arrSumCont["phicEmp"],$dtl_Format_right);
		$worksheet->write($col_ctr ,5,$arrSumCont["phicEmplr"],$dtl_Format_right);
		$worksheet->write($col_ctr ,6,$arrSumCont["hdmfEmp"],$dtl_Format_right);
		$worksheet->write($col_ctr ,7,$arrSumCont["hdmfEmplr"],$dtl_Format_right);

		$grandTotal_sss+=$arrSumCont["sssEmp"]+$arrSumCont["sssEmplr"]+$arrSumCont["ec"]; 
		$grandTotal_hdmf+=$arrSumCont["hdmfEmp"]+$arrSumCont["hdmfEmplr"];
		$grandTotal_phic+=$arrSumCont["phicEmp"]+$arrSumCont["phicEmplr"];
		
		$col_ctr++;
		$pay_ctr++;
	}
	

	
	$worksheet->write(11,0,'GRAND TOTAL(S)',$headerFormat);
	$worksheet->setMerge(11,1,11,3);
	$worksheet->setMerge(11,4,11,5);
	$worksheet->setMerge(11,6,11,7);
	$worksheet->write(11,1,$grandTotal_sss,$grand_Format_right);
	$worksheet->write(11,2,'',$grand_Format_right);
	$worksheet->write(11,3,'',$grand_Format_right);
	$worksheet->write(11,4,$grandTotal_phic,$grand_Format_right);
	$worksheet->write(11,5,'',$grand_Format_right);
	$worksheet->write(11,6,$grandTotal_hdmf,$grand_Format_right);
	$worksheet->write(11,7,'',$grand_Format_right);
	
	
	
	$worksheet->setMerge(12,1,12,3);
	$worksheet->setMerge(12,4,12,5);
	$worksheet->setMerge(12,6,12,7);
	$worksheet->write(12,1,'SSS GRAND TOTAL',$headerFormat);
	$worksheet->write(12,2,'',$grand_Format_right);
	$worksheet->write(12,3,'',$grand_Format_right);
	$worksheet->write(12,4,'PHIC GRAND TOTAL',$headerFormat);
	$worksheet->write(12,5,'',$grand_Format_right);
	$worksheet->write(12,6,'HDMF GRAND TOTAL',$headerFormat);
	$worksheet->write(12,7,'',$grand_Format_right);
	
	
	//Create another Sheet for Loan Adjustment
	$worksheet=&$workbook->addWorksheet("LOAN");
	
	 $qry_LoanDetail = "SELECT     tblDeductionsHist.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblLoanType.lonTypeShortDesc, 
										  tblEmpLoans.lonRefNo, tblEmpLoans.lonWidInterst, tblDeductionsHist.trnAmountD, 
										  tblEmpLoans.lonPayments + tblDeductionsHist.trnAmountD AS lonPayments, tblEmpLoans.lonCurbal + tblDeductionsHist.trnAmountD * - 1 AS lonCurbal,
										   tblDeductionsHist.pdYear, tblEmpLoans.lonGranted, tblDeductionsHist.pdNumber, tblEmpLoans.lonStart
					FROM         tblDeductionsHist LEFT OUTER JOIN
										  tblEmpMast ON tblDeductionsHist.compCode = tblEmpMast.compCode AND tblDeductionsHist.empNo = tblEmpMast.empNo LEFT OUTER JOIN
										  tblLoanType INNER JOIN
										  tblEmpLoans ON tblLoanType.compCode = tblEmpLoans.compCode AND tblLoanType.lonTypeCd = tblEmpLoans.lonTypeCd ON 
										  tblDeductionsHist.compCode = tblEmpLoans.compCode AND tblDeductionsHist.empNo = tblEmpLoans.empNo AND tblEmpLoans.lonTypeCd IN (22) AND
										   tblEmpLoans.lonStat IN ('C', 'T')
					WHERE     tblDeductionsHist.compCode='".$_SESSION["company_code"]."' and 
								(tblDeductionsHist.trnCode IN (N'5901', '5902')) AND (tblDeductionsHist.pdNumber IN ('".$pdNum1."', '".$pdNum2."')) 
								AND (tblDeductionsHist.pdYear = '".$pdYear."') AND tblEmpLoans.lonRefNo Not IN ('24B 016240 M','24B 089708 M','P24F72152RM')
					";
	
	$arr_LoanDetail = $inqTSObj->getArrRes($inqTSObj->execQry($qry_LoanDetail));
	
	$arr_f_row = array('empNo','empLastName','empFirstName','empMidName','lonTypeShortDesc','lonRefNo','lonWidInterst','trnAmountD','lonPayments','lonCurbal','pdYear','lonGranted','pdNumber','lonStart');
	$col_ctr = 0;
	foreach($arr_f_row as $arr_f_row_val)
	{	
		$worksheet->setColumn(0,$col_ctr,15);
		$worksheet->write(0,$col_ctr,$arr_f_row_val,$headerFormat);
		$col_ctr++;
	}
	
	$col_ctr = 1;
	foreach($arr_LoanDetail as $arr_LoanDetail_val)
	{
		$worksheet->write($col_ctr,0," ".$arr_LoanDetail_val["empNo"],$dtl_Format);
		$worksheet->write($col_ctr,1,$arr_LoanDetail_val["empLastName"],$dtl_Format);
		$worksheet->write($col_ctr,2,$arr_LoanDetail_val["empFirstName"],$dtl_Format);
		$worksheet->write($col_ctr,3,$arr_LoanDetail_val["empMidName"],$dtl_Format);
		$worksheet->write($col_ctr,4,$arr_LoanDetail_val["lonTypeShortDesc"],$dtl_Format);
		$worksheet->write($col_ctr,5,$arr_LoanDetail_val["lonRefNo"],$dtl_Format);
		$worksheet->write($col_ctr,6,$arr_LoanDetail_val["lonWidInterst"],$dtl_Format_right);
		$worksheet->write($col_ctr,7,$arr_LoanDetail_val["trnAmountD"],$dtl_Format_right);
		$worksheet->write($col_ctr,8,$arr_LoanDetail_val["lonPayments"],$dtl_Format_right);
		$worksheet->write($col_ctr,9,$arr_LoanDetail_val["lonCurbal"],$dtl_Format_right);
		$worksheet->write($col_ctr,10,$arr_LoanDetail_val["pdYear"],$dtl_Format);
		$worksheet->write($col_ctr,11,date("m/d/Y", strtotime($arr_LoanDetail_val["lonGranted"])),$dtl_Format);
		$worksheet->write($col_ctr,12,$arr_LoanDetail_val["pdNumber"],$dtl_Format);
		$worksheet->write($col_ctr,13,date("m/d/Y", strtotime($arr_LoanDetail_val["lonStart"])),$dtl_Format);

		
		$col_ctr++;
	}
	
	//Create another Sheet for Loan Adjustment
	$worksheet=&$workbook->addWorksheet("LOAN ADJUSTMENT");
	
	 $qry_LoanAdj = "SELECT     tblEmpLoansDtlHist.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblLoanType.lonTypeShortDesc, 
										  tblEmpLoansDtlHist.lonRefNo, tblEmpLoans.lonWidInterst, tblEmpLoansDtlHist.ActualAmt, tblEmpLoans.lonPayments, tblEmpLoans.lonCurbal, 
										  tblEmpLoansDtlHist.pdYear, tblEmpLoans.lonGranted, tblEmpLoansDtlHist.pdNumber, tblEmpLoans.lonStart
					FROM         tblEmpLoansDtlHist INNER JOIN
										  tblEmpMast ON tblEmpLoansDtlHist.compCode = tblEmpMast.compCode AND tblEmpLoansDtlHist.empNo = tblEmpMast.empNo INNER JOIN
										  tblLoanType ON tblEmpLoansDtlHist.compCode = tblLoanType.compCode AND tblEmpLoansDtlHist.lonTypeCd = tblLoanType.lonTypeCd INNER JOIN
										  tblEmpLoans ON tblEmpLoansDtlHist.compCode = tblEmpLoans.compCode AND tblEmpLoansDtlHist.empNo = tblEmpLoans.empNo AND 
										  tblEmpLoansDtlHist.lonTypeCd = tblEmpLoans.lonTypeCd AND tblEmpLoansDtlHist.lonRefNo = tblEmpLoans.lonRefNo
					WHERE     tblEmpLoansDtlHist.compCode='".$_SESSION["company_code"]."' and 
					(tblEmpLoansDtlHist.pdYear = '".$pdYear."') AND (tblEmpLoansDtlHist.pdNumber IN ('".$pdNum1."', '".$pdNum2."')) AND 
					(tblEmpLoansDtlHist.lonTypeCd IN (11, 12, 21, 22)) and ManualTag IS NULL  
					ORDER BY tblEmpLoansDtlHist.lonTypeCd, tblEmpMast.empLastName, tblEmpMast.empFirstName";
	
	$arr_LoanAdj = $inqTSObj->getArrRes($inqTSObj->execQry($qry_LoanAdj));
	
	$arr_f_row = array('empNo','empLastName','empFirstName','empMidName','lonTypeShortDesc','lonRefNo','lonWidInterst','ActualAmt','lonPayments','lonCurbal','pdYear','lonGranted','pdNumber','lonStart');
	$col_ctr = 0;
	foreach($arr_f_row as $arr_f_row_val)
	{	
		$worksheet->setColumn(0,$col_ctr,15);
		$worksheet->write(0,$col_ctr,$arr_f_row_val,$headerFormat);
		$col_ctr++;
	}
	
	$col_ctr = 1;
	foreach($arr_LoanAdj as $arr_LoanAdj_val)
	{
		$worksheet->write($col_ctr,0," ".$arr_LoanAdj_val["empNo"],$dtl_Format);
		$worksheet->write($col_ctr,1,$arr_LoanAdj_val["empLastName"],$dtl_Format);
		$worksheet->write($col_ctr,2,$arr_LoanAdj_val["empFirstName"],$dtl_Format);
		$worksheet->write($col_ctr,3,$arr_LoanAdj_val["empMidName"],$dtl_Format);
		$worksheet->write($col_ctr,4,$arr_LoanAdj_val["lonTypeShortDesc"],$dtl_Format);
		$worksheet->write($col_ctr,5,$arr_LoanAdj_val["lonRefNo"],$dtl_Format);
		$worksheet->write($col_ctr,6,$arr_LoanAdj_val["lonWidInterst"],$dtl_Format_right);
		$worksheet->write($col_ctr,7,$arr_LoanAdj_val["ActualAmt"],$dtl_Format_right);
		$worksheet->write($col_ctr,8,$arr_LoanAdj_val["lonPayments"],$dtl_Format_right);
		$worksheet->write($col_ctr,9,$arr_LoanAdj_val["lonCurbal"],$dtl_Format_right);
		$worksheet->write($col_ctr,10,$arr_LoanAdj_val["pdYear"],$dtl_Format);
		$worksheet->write($col_ctr,11,date("m/d/Y", strtotime($arr_LoanAdj_val["lonGranted"])),$dtl_Format);
		$worksheet->write($col_ctr,12,$arr_LoanAdj_val["pdNumber"],$dtl_Format);
		$worksheet->write($col_ctr,13,date("m/d/Y", strtotime($arr_LoanAdj_val["lonStart"])),$dtl_Format);

		
		$col_ctr++;
	}
	
	
	$workbook->close();
		

?>