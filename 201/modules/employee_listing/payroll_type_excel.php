<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','C:\wamp\bin\php\php5.2.6\PEAR\pear');
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("common_obj.php");
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
	$filename = "EMPLOYEE LISTING PER RANK.xls";
	//Column titles
	//Data loading
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$workbook->send($filename);
	$worksheet=&$workbook->addWorksheet("Employee Listing");
	$worksheet->setLandscape();
	
	$arrCompInfo = $inqTSObj->getCompany($_SESSION["company_code"]);	
	$arrBranch = $inqTSObj->getEmpBranchArt($_SESSION["company_code"],$empBrnCode );
	
	$worksheet->write(0,0,$arrCompInfo["compName"],$headerFormat);
	$worksheet->write(1,0,'BRANCH',$headerFormat);
	$worksheet->write(1,1,$arrBranch["brnDesc"],$headerFormat);
	$worksheet->write(2,0,'LIST OF EMPLOYEES',$headerFormat);
	
	
	$txtOut = array('No.','Last Name','First Name','MI','Position Title','Date Hired','Birth Date','Employee Status');
	
	for($ctr=0; $ctr<sizeof($txtOut); $ctr++)
	{
		$worksheet->write(3,$ctr,$txtOut[$ctr],$headerFormat);
	}
	
	$empBrnCode = $_GET['empBrnCode'];
	$empDiv = $_GET['empDiv'];
	$empDept = $_GET['empDept'];
	$empSect = $_GET['empSect'];
	
	
	
	$i=1;
	
	$arrRank = $inqTSObj->getRankType();
	$empCtr = 4;
	$emplistCtr = 1;
	$grandEmpCtr = 0;
	
	foreach($arrRank as $arrRank_val)
	{	
		
		if($lastRankCd!=$arrRank_val["rankCode"])
		{
			$empCtr++;
			$worksheet->write($empCtr,0,$arrRank_val["rankDesc"],$headerFormat);
		
			$emplistCtr=1;
			$empCtr++;
		}
		
		$arrEmp = $inqTSObj->getListofEmp($empDiv, $empDept, $empSect, $empBrnCode, $arrRank_val["rankCode"]);
		
		foreach($arrEmp as $arrEmp_val)
		{	
			$worksheet->write($empCtr,0,$emplistCtr,$headerFormat);
			$worksheet->write($empCtr,1,$arrEmp_val["empLastName"],$headerFormat);
			$worksheet->write($empCtr,2,$arrEmp_val["empFirstName"],$headerFormat);
			$worksheet->write($empCtr,3,$arrEmp_val["empMidName"],$headerFormat);
			
			$arrprintedby_pos = $inqTSObj->getpositionwil(" where divCode='".$arrEmp_val["empDiv"]."' and deptCode='".$arrEmp_val["empDepCode"]."' and sectCode='".$arrEmp_val["empSecCode"]."' and posCode='".$arrEmp_val["empPosId"]."'",2);
			
			
			$worksheet->write($empCtr,4,$arrprintedby_pos["posDesc"],$headerFormat);
			$worksheet->write($empCtr,5,date("m/d/Y", strtotime($arrEmp_val["dateHired"])),$headerFormat);
			$worksheet->write($empCtr,6,date("m/d/Y", strtotime($arrEmp_val["empBday"])),$headerFormat);
			$worksheet->write($empCtr,7,$arrEmp_val["employmentTag"],$headerFormat);
			
			$empCtr++;
			$emplistCtr++;
			$grandEmpCtr++;

			
		}
		
		$lastRankCd = $arrRank_val["rankCode"];
		
	}
	
	$empCtr = $empCtr+1;
	$worksheet->write($empCtr,0,'GRAND TOTAL',$headerFormat);
	$worksheet->write($empCtr,1,$grandEmpCtr,$headerFormat);		
	
	
	$workbook->close();
		

?>