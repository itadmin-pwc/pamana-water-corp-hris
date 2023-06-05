<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','D:\wamp\php\PEAR');
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
	$filename = "HDMF_".$_GET['pdMonth'].".DBF";
	//Column titles
	//Data loading
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$workbook->send($filename);
	$worksheet=&$workbook->addWorksheet("HDMFCONT");
	$worksheet->setLandscape();
	
	$txtOut = array('EYERID','EYEENO','LNAME','FNAME','MID','PERCOV1','PFRDATE1','PFRNO1','PERAMT1','PERAMT2','PFRAMT','GOVTYPE','FILLER','HDMFID','BALFWD87','BALFWD88','BALFWD89','COMPANY','BIRTHDATE');
	
	for($ctr=0; $ctr<sizeof($txtOut); $ctr++)
	{
		$worksheet->write(0,$ctr,$txtOut[$ctr],$headerFormat);
	}
	
	$arrGovt = $inqTSObj->RemTextfile($_SESSION["company_code"], $_GET["pdYear"], $_GET["pdMonth"]);
	$arrcompName = $inqTSObj->getCompany($_SESSION["company_code"]);

	$i=1;
	foreach($arrGovt as $index_val)
	{	
		$worksheet->write($i,0,$arrcompName["compPagibig"],$headerFormat);
		$worksheet->write($i,1,str_replace(" ", "", $index_val["empPagibig"]),$headerFormat);
		$worksheet->write($i,2,strtoupper($index_val["empLastName"]),$headerFormat);
		$worksheet->write($i,3,strtoupper($index_val["empFirstName"]),$headerFormat);
		$worksheet->write($i,4,strtoupper($index_val["empMidName"]),$headerFormat);
		$worksheet->write($i,5," ",$headerFormat);
		$worksheet->write($i,6," ",$headerFormat);
		$worksheet->write($i,7," ",$headerFormat);
		$worksheet->write($i,8,$index_val["hdmfEmp"],$headerFormat);
		$worksheet->write($i,9,$index_val["hdmfEmplr"],$headerFormat);
		$worksheet->write($i,10," ",$headerFormat);
		$worksheet->write($i,11," ",$headerFormat);
		$worksheet->write($i,12," ",$headerFormat);
		$worksheet->write($i,13,$index_val["empPagibig"],$headerFormat);
		$worksheet->write($i,14," ",$headerFormat);
		$worksheet->write($i,15," ",$headerFormat);
		$worksheet->write($i,16," ",$headerFormat);
		$worksheet->write($i,17,strtoupper(str_replace(","," ", $arrcompName["compName"])),$headerFormat);
		$worksheet->write($i,18,($index_val["empBday"]!=""?date("m/d/Y", strtotime($index_val["empBday"])):""),$headerFormat);



		$i++;
		
		
	}
	

	
	
	$workbook->close();
		

?>