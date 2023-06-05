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
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'merge'));
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
	$filename = "monthly_ytdbypayreg.xls";
	$workbook->send($filename);
	$arrPd = explode(",",$_GET['payPd']);
	$pdYear = $arrPd[1];
	
	switch($arrPd[0]) {
		case 1:
			$pdMonth = "Jan";
		break;
		case 2:
			$pdMonth = "Feb";
		break;
		case 3:
			$pdMonth = "Mar";
		break;
		case 4:
			$pdMonth = "Apr";
		break;
		case 5:
			$pdMonth = "May";
		break;
		case 6:
			$pdMonth = "Jun";
		break;
		case 7:
			$pdMonth = "Jul";
		break;
		case 8:
			$pdMonth = "Aug";
		break;
		case 9:
			$pdMonth = "Sep";
		break;
		case 10:
			$pdMonth = "Oct";
		break;
		case 11:
			$pdMonth = "Nov";
		break;
		case 12:
			$pdMonth = "Dec";
		break;
	}
	$worksheet=&$workbook->addWorksheet("Monthly YTD Report $pdMonth $pdYear");
	$worksheet->setLandscape();
	$worksheet->freezePanes(array(2, 0));
	
	$worksheet->write(0, 0, $inqTSObj->getCompanyName($_SESSION['company_code']),$headerFormat);
	for($i=1;$i<6;$i++) {
		$worksheet->write(0, $i, "",$headerFormat);	
	}
	$worksheet->setColumn(0,0,30);
	$worksheet->setColumn(1,7,20);
	$worksheet->write(1,0,"MONTH",$headerFormat);
	$worksheet->write(1,1,"GROSS INCOME",$headerFormat);
	$worksheet->write(1,2,"W/ TAX",$headerFormat);
	$worksheet->write(1,3,"PREV YR WTAX ADJ",$headerFormat);
	$worksheet->write(1,4,"ECOLA",$headerFormat);
	$worksheet->write(1,5,"13TH MONTH NON TAX",$headerFormat);
	$worksheet->write(1,6,"13TH MONTH TAX",$headerFormat);	
		$ctr			= 1;
		$totGross 	= 0;
		$totTax 	= 0;
		$totTaxAdj 	= 0;
		$totEcola 	= 0;
		$tot13thNT 	= 0;
		$tot13thT 	= 0;

		$arrYTD=$inqTSObj->getYTDMonthlybyPayreg($_GET['payPd']);
			foreach($arrYTD as $valYTD){
				$ctr++;
				$row = ($col==0) ? $detail2:$detail;
				$row2 = ($col==0) ? $Dept2:$Dept;
				$col = ($col==0) ? 1:0;
				$worksheet->setRow($ctr,16);
				$payReg = "Group " .$valYTD['payGrp'] . ": " .$valYTD['payCatDesc'] . " ".$inqTSObj->getCutOffPeriod($valYTD['pdNumber']);
				$worksheet->write($ctr,0,$payReg,$row2);
				$worksheet->write($ctr,1,number_format($valYTD['grossearnings'],2),$row);
				$worksheet->write($ctr,2,number_format($valYTD['tax'],2),$row);
				$worksheet->write($ctr,3,number_format($valYTD['YearEnd'],2),$row);
				$worksheet->write($ctr,4,number_format($valYTD['ecola'],2),$row);
				$worksheet->write($ctr,5,number_format($valYTD['N13thNontax'],2),$row);
				$worksheet->write($ctr,6,number_format($valYTD['N13thTax'],2),$row);					
				$totGross 	+= round($valYTD['grossearnings'],2);
				$totTax 	+= round($valYTD['tax'],2);
				$totTaxAdj 	+= round($valYTD['YearEnd'],2);
				$totEcola 	+= round($valYTD['ecola'],2);
				$tot13thNT 	+= round($valYTD['N13thNontax'],2);
				$tot13thT 	+= round($valYTD['N13thTax'],2);
			}
			$ctr++;
			$worksheet->setRow($ctr,16);
			$worksheet->write($ctr,0,"Total",$TotalBorder);
			$worksheet->write($ctr,1,number_format($totGross,2),$TotalBorder);
			$worksheet->write($ctr,2,number_format($totTax,2),$TotalBorder);
			$worksheet->write($ctr,3,number_format($totTaxAdj,2),$TotalBorder);
			$worksheet->write($ctr,4,number_format($totEcola,2),$TotalBorder);
			$worksheet->write($ctr,5,number_format($tot13thNT,2),$TotalBorder);
			$worksheet->write($ctr,6,number_format($tot13thT,2),$TotalBorder);				
$workbook->close();
?>