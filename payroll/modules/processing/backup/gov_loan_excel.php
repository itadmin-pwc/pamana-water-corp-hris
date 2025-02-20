<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','C:\wamp\bin\php\php5.2.6\PEAR\pear');
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	require_once 'Spreadsheet/Excel/Writer.php';
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	

		
	$workbook = new Spreadsheet_Excel_Writer();
	$inqTSObj=new inqTSObj();
	$headerFormat = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'black',
                                      'bold'=> 0,
									  'border' => 0,
									  'Align' => 'left',
						  num_format=>0));
	$headerFormat->setFontFamily('Calibri'); 
	$headerBorder    = $workbook->addFormat(array('Size' => 10,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'merge'));
	$headerBorder->setFontFamily('Calibri'); 
	$workbook->setCustomColor(13,155,205,255);
	$TotalBorder    = $workbook->addFormat(array('Align' => 'right','bold'=> 1,'border'=>1,'fgColor' => 'white'));
	$TotalBorder->setFontFamily('Calibri'); 
	$TotalBorder->setTop(5); 
	$detailrBorder   = $workbook->addFormat(array('border' =>1,'Align' => 'right'));
	$detailrBorder->setFontFamily('Calibri'); 
	$detailrBorderAlignRight2   = $workbook->addFormat(array('Align' => 'left'));
	$detailrBorderAlignRight2->setFontFamily('Calibri');
	$workbook->setCustomColor(12,183,219,255);
	$detail   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'right'));
	$detail->setFontFamily('Calibri'); 

	$detail2   = $workbook->addFormat(array('Size' => 10,
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'right'));
	$detail2->setFgColor(12); 
	$detail2->setFontFamily('Calibri'); 
	$Dept   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'left'));
	$Dept->setFontFamily('Calibri'); 
	$Dept2   = $workbook->addFormat(array('Size' => 10,
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'left'));
	$Dept2->setFgColor(12); 
	$Dept2->setFontFamily('Calibri');
 	$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
	$filename = "HDMFLOAN_".$_GET['pdMonth'].".DBF";
	//Column titles
	//Data loading
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$workbook->send($filename);
	$worksheet=&$workbook->addWorksheet("HDMFCONT");
	$worksheet->setLandscape();
	
	$txtOut = array('BILLPERIOD','LASTNAME','FIRSTNAME','MIDNAME','AMORT');
	
	for($ctr=0; $ctr<sizeof($txtOut); $ctr++)
	{
		$worksheet->write(0,$ctr,$txtOut[$ctr],$headerFormat);
	}
	
	$arrmtdGovtHist = $inqTSObj->Loans($_SESSION["company_code"], $_GET["pdYear"], $_GET["pdMonth"],2);
	$arrmtdDeductHist = $inqTSObj->loanAdjustment($_GET["pdYear"], $_GET["pdMonth"]);
	
	foreach($arrmtdGovtHist as $arrmtdGovtHist_val)
		$arrEmpLoans.=$arrmtdGovtHist_val["empPagibig"]."*".$arrmtdGovtHist_val["empBday"]."*".str_replace('*','',$arrmtdGovtHist_val["lonRefNo"])."*".$arrmtdGovtHist_val["empLastName"]."*".$arrmtdGovtHist_val["empFirstName"]."*".$arrmtdGovtHist_val["empMidName"]."*".$arrmtdGovtHist_val["Amount"]."+";
	
	foreach($arrmtdDeductHist as $arrmtdDeductHist_val)
		$arrEmpLoans.=$arrmtdDeductHist_val["empPagibig"]."*".$arrmtdDeductHist_val["empBday"]."*".str_replace('*','',$arrmtdDeductHist_val["lonRefNo"])."*".$arrmtdDeductHist_val["empLastName"]."*".$arrmtdDeductHist_val["empFirstName"]."*".$arrmtdDeductHist_val["empMidName"]."*".$arrmtdDeductHist_val["trnAmountD"]."+";
	
	
	$arrEmpLoans = substr($arrEmpLoans,0,strlen($arrEmpLoans) - 1);
	
	$arrGovt = explode("+", $arrEmpLoans);
	
	
	$arrcompName = $inqTSObj->getCompany($_SESSION["company_code"]);
	$date =date('ym') ;
	$i=1;
	foreach($arrGovt as $index_val)
	{	
	
		$arrmtdGovtHist_val = explode("*", $index_val);
		$worksheet->write($i,0,$date,$headerFormat);
		$worksheet->write($i,1,strtoupper($arrmtdGovtHist_val["3"]),$headerFormat);
		$worksheet->write($i,2,strtoupper($arrmtdGovtHist_val["4"]),$headerFormat);
		$worksheet->write($i,3,strtoupper($arrmtdGovtHist_val["5"]),$headerFormat);
		$worksheet->write($i,4,$arrmtdGovtHist_val["6"],$headerFormat);
		


		$i++;
		
		
	}
	

	
	
	$workbook->close();
		

?>