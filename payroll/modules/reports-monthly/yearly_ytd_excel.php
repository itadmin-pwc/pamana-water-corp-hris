<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','C:\wamp\php\PEAR');
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	require_once 'Spreadsheet/Excel/Writer.php';
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	

		function getMonth($Month) {
			switch($Month) {
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
			return $pdMonth;		
		}	
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
 	$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
	$filename = "yearly_ytd.xls";
	//Column titles
	//Data loading
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$arrYTD = $inqTSObj->getYTDYearly($Year);
	$workbook->send($filename);
	$worksheet=&$workbook->addWorksheet("Yearly YTD Report $Year");
	$worksheet->setLandscape();
	$worksheet->freezePanes(array(2, 0));
	
	$worksheet->write(0, 0, $inqTSObj->getCompanyName($_SESSION['company_code']),$headerFormat);
	for($i=1;$i<6;$i++) {
		$worksheet->write(0, $i, "",$headerFormat);	
	}
	$worksheet->setColumn(0,7,20);
	$worksheet->write(1,0,"MONTH",$headerFormat);
	$worksheet->write(1,1,"GROSS INCOME",$headerFormat);
	$worksheet->write(1,2,"W/ TAX",$headerFormat);
	$worksheet->write(1,3,"PREV YR WTAX ADJ",$headerFormat);
	$worksheet->write(1,4,"ECOLA",$headerFormat);
	$worksheet->write(1,5,"13TH MONTH NON TAX",$headerFormat);
	$worksheet->write(1,6,"13TH MONTH TAX",$headerFormat);	
		foreach($arrYTD as $valYTD){
			switch($valYTD['pdNumber']) {
				case 1:
					$Gross[1] 		+= $valYTD['grossearnings'];
					$tax[1] 		+= $valYTD['tax'];
					$yetax[1] 		+= $valYTD['YearEnd'];
					$ecola[1]		+= $valYTD['ecola'];
					$N13thNontax[1] += $valYTD['N13thNontax'];
					$N13thTax[1] 	+= $valYTD['N13thTax'];
				break;
				case 2:
					$Gross[1] 		+= $valYTD['grossearnings'];
					$tax[1] 		+= $valYTD['tax'];
					$yetax[1] 		+= $valYTD['YearEnd'];
					$ecola[1]		+= $valYTD['ecola'];
					$N13thNontax[1] += $valYTD['N13thNontax'];
					$N13thTax[1] 	+= $valYTD['N13thTax'];
				break;				
				case 3:
					$Gross[2] 		+= $valYTD['grossearnings'];
					$tax[2] 		+= $valYTD['tax'];
					$yetax[2] 		+= $valYTD['YearEnd'];
					$ecola[2]		+= $valYTD['ecola'];
					$N13thNontax[2] += $valYTD['N13thNontax'];
					$N13thTax[2] 	+= $valYTD['N13thTax'];
				break;
				case 4:
					$Gross[2] 		+= $valYTD['grossearnings'];
					$tax[2] 		+= $valYTD['tax'];
					$yetax[2] 		+= $valYTD['YearEnd'];
					$ecola[2]		+= $valYTD['ecola'];
					$N13thNontax[2] += $valYTD['N13thNontax'];
					$N13thTax[2] 	+= $valYTD['N13thTax'];
				break;
				case 5:
					$Gross[3] 		+= $valYTD['grossearnings'];
					$tax[3] 		+= $valYTD['tax'];
					$yetax[3] 		+= $valYTD['YearEnd'];
					$ecola[3]		+= $valYTD['ecola'];
					$N13thNontax[3] += $valYTD['N13thNontax'];
					$N13thTax[3] 	+= $valYTD['N13thTax'];
				break;
				case 6:
					$Gross[3] 		+= $valYTD['grossearnings'];
					$tax[3] 		+= $valYTD['tax'];
					$yetax[3] 		+= $valYTD['YearEnd'];
					$ecola[3]		+= $valYTD['ecola'];
					$N13thNontax[3] += $valYTD['N13thNontax'];
					$N13thTax[3] 	+= $valYTD['N13thTax'];
				break;
				case 7:
					$Gross[4] 		+= $valYTD['grossearnings'];
					$tax[4] 		+= $valYTD['tax'];
					$yetax[4] 		+= $valYTD['YearEnd'];
					$ecola[4]		+= $valYTD['ecola'];
					$N13thNontax[4] += $valYTD['N13thNontax'];
					$N13thTax[4] 	+= $valYTD['N13thTax'];
				break;
				case 8:
					$Gross[4] 		+= $valYTD['grossearnings'];
					$tax[4] 		+= $valYTD['tax'];
					$yetax[4] 		+= $valYTD['YearEnd'];
					$ecola[4]		+= $valYTD['ecola'];
					$N13thNontax[4] += $valYTD['N13thNontax'];
					$N13thTax[4] 	+= $valYTD['N13thTax'];
				break;
				case 9:
					$Gross[5] 		+= $valYTD['grossearnings'];
					$tax[5] 		+= $valYTD['tax'];
					$yetax[5] 		+= $valYTD['YearEnd'];
					$ecola[5]		+= $valYTD['ecola'];
					$N13thNontax[5] += $valYTD['N13thNontax'];
					$N13thTax[5] 	+= $valYTD['N13thTax'];
				break;
				case 10:
					$Gross[5] 		+= $valYTD['grossearnings'];
					$tax[5] 		+= $valYTD['tax'];
					$yetax[5] 		+= $valYTD['YearEnd'];
					$ecola[5]		+= $valYTD['ecola'];
					$N13thNontax[5] += $valYTD['N13thNontax'];
					$N13thTax[5] 	+= $valYTD['N13thTax'];
				break;
				case 11:
					$Gross[6] 		+= $valYTD['grossearnings'];
					$tax[6] 		+= $valYTD['tax'];
					$yetax[6] 		+= $valYTD['YearEnd'];
					$ecola[6]		+= $valYTD['ecola'];
					$N13thNontax[6] += $valYTD['N13thNontax'];
					$N13thTax[6] 	+= $valYTD['N13thTax'];
				break;
				case 12:
					$Gross[6] 		+= $valYTD['grossearnings'];
					$tax[6] 		+= $valYTD['tax'];
					$yetax[6] 		+= $valYTD['YearEnd'];
					$ecola[6]		+= $valYTD['ecola'];
					$N13thNontax[6] += $valYTD['N13thNontax'];
					$N13thTax[6] 	+= $valYTD['N13thTax'];
				break;	
				case 13:
					$Gross[7] 		+= $valYTD['grossearnings'];
					$tax[7] 		+= $valYTD['tax'];
					$yetax[7] 		+= $valYTD['YearEnd'];
					$ecola[7]		+= $valYTD['ecola'];
					$N13thNontax[7] += $valYTD['N13thNontax'];
					$N13thTax[7] 	+= $valYTD['N13thTax'];
				break;
				case 14:
					$Gross[7] 		+= $valYTD['grossearnings'];
					$tax[7] 		+= $valYTD['tax'];
					$yetax[7] 		+= $valYTD['YearEnd'];
					$ecola[7]		+= $valYTD['ecola'];
					$N13thNontax[7] += $valYTD['N13thNontax'];
					$N13thTax[7] 	+= $valYTD['N13thTax'];
				break;
				case 15:
					$Gross[8] 		+= $valYTD['grossearnings'];
					$tax[8] 		+= $valYTD['tax'];
					$yetax[8] 		+= $valYTD['YearEnd'];
					$ecola[8]		+= $valYTD['ecola'];
					$N13thNontax[8] += $valYTD['N13thNontax'];
					$N13thTax[8] 	+= $valYTD['N13thTax'];
				break;
				case 16:
					$Gross[8] 		+= $valYTD['grossearnings'];
					$tax[8] 		+= $valYTD['tax'];
					$yetax[8] 		+= $valYTD['YearEnd'];
					$ecola[8]		+= $valYTD['ecola'];
					$N13thNontax[8] += $valYTD['N13thNontax'];
					$N13thTax[8] 	+= $valYTD['N13thTax'];
				break;
				case 17:
					$Gross[9] 		+= $valYTD['grossearnings'];
					$tax[9] 		+= $valYTD['tax'];
					$yetax[9] 		+= $valYTD['YearEnd'];
					$ecola[9]		+= $valYTD['ecola'];
					$N13thNontax[9] += $valYTD['N13thNontax'];
					$N13thTax[9] 	+= $valYTD['N13thTax'];
				break;
				case 18:
					$Gross[9] 		+= $valYTD['grossearnings'];
					$tax[9] 		+= $valYTD['tax'];
					$yetax[9] 		+= $valYTD['YearEnd'];
					$ecola[9]		+= $valYTD['ecola'];
					$N13thNontax[9] += $valYTD['N13thNontax'];
					$N13thTax[9] 	+= $valYTD['N13thTax'];
				break;
				case 19:
					$Gross[10] 			+= $valYTD['grossearnings'];
					$tax[10] 			+= $valYTD['tax'];
					$yetax[10] 			+= $valYTD['YearEnd'];
					$ecola[10]			+= $valYTD['ecola'];
					$N13thNontax[10]	+= $valYTD['N13thNontax'];
					$N13thTax[10] 		+= $valYTD['N13thTax'];
				break;
				case 20:
					$Gross[10] 			+= $valYTD['grossearnings'];
					$tax[10] 			+= $valYTD['tax'];
					$yetax[10] 			+= $valYTD['YearEnd'];
					$ecola[10]			+= $valYTD['ecola'];
					$N13thNontax[10]	+= $valYTD['N13thNontax'];
					$N13thTax[10] 		+= $valYTD['N13thTax'];
				break;	
				case 21:
					$Gross[11] 			+= $valYTD['grossearnings'];
					$tax[11] 			+= $valYTD['tax'];
					$yetax[11] 			+= $valYTD['YearEnd'];
					$ecola[11]			+= $valYTD['ecola'];
					$N13thNontax[11] 	+= $valYTD['N13thNontax'];
					$N13thTax[11] 		+= $valYTD['N13thTax'];
				break;
				case 22:
					$Gross[11] 			+= $valYTD['grossearnings'];
					$tax[11] 			+= $valYTD['tax'];
					$yetax[11] 			+= $valYTD['YearEnd'];
					$ecola[11]			+= $valYTD['ecola'];
					$N13thNontax[11] 	+= $valYTD['N13thNontax'];
					$N13thTax[11] 		+= $valYTD['N13thTax'];
				break;	
				case 23:
					$Gross[12] 			+= $valYTD['grossearnings'];
					$tax[12] 			+= $valYTD['tax'];
					$yetax[12] 			+= $valYTD['YearEnd'];
					$ecola[12]			+= $valYTD['ecola'];
					$N13thNontax[12] 	+= $valYTD['N13thNontax'];
					$N13thTax[12] 		+= $valYTD['N13thTax'];
				break;
				case 24:
					$Gross[12] 			+= $valYTD['grossearnings'];
					$tax[12] 			+= $valYTD['tax'];
					$yetax[12] 			+= $valYTD['YearEnd'];
					$ecola[12]			+= $valYTD['ecola'];
					$N13thNontax[12] 	+= $valYTD['N13thNontax'];
					$N13thTax[12] 		+= $valYTD['N13thTax'];
				break;
				case 25:
					$Gross[12] 			+= $valYTD['grossearnings'];
					$tax[12] 			+= $valYTD['tax'];
					$yetax[12] 			+= $valYTD['YearEnd'];
					$ecola[12]			+= $valYTD['ecola'];
					$N13thNontax[12] 	+= $valYTD['N13thNontax'];
					$N13thTax[12] 		+= $valYTD['N13thTax'];
				break;													
			}
		}
			for($i=1; $i<13;$i++) {
					$row = ($col==0) ? $detail2:$detail;
					$row2 = ($col==0) ? $Dept2:$Dept;
					$col = ($col==0) ? 1:0;
					$worksheet->setRow($i,16);
			
					$worksheet->write($i+1,0,getMonth($i),$row2);
					$worksheet->write($i+1,1,number_format($Gross[$i],2),$row);
					$worksheet->write($i+1,2,number_format($tax[$i],2),$row);
					$worksheet->write($i+1,3,number_format($yetax[$i],2),$row);
					$worksheet->write($i+1,4,number_format($ecola[$i],2),$row);
					$worksheet->write($i+1,5,number_format($N13thNontax[$i],2),$row);
					$worksheet->write($i+1,6,number_format($N13thTax[$i],2),$row);
					$totGross 	+= round($Gross[$i],2);
					$totTax 	+= round($tax[$i],2);
					$totYETax 	+= round($yetax[$i],2);
					$totEcola 	+= round($ecola[$i],2);
					$tot13thNT 	+= round($N13thNontax[$i],2);
					$tot13thT 	+= round($N13thTax[$i],2);
			}
					$worksheet->setRow($i+1,16);
					$worksheet->write($i+1,0,'TOTAL',$TotalBorder);
					$worksheet->write($i+1,1,number_format($totGross,2),$TotalBorder);
					$worksheet->write($i+1,2,number_format($totTax,2),$TotalBorder);
					$worksheet->write($i+1,3,number_format($totYETax,2),$TotalBorder);
					$worksheet->write($i+1,4,number_format($totEcola,2),$TotalBorder);
					$worksheet->write($i+1,5,number_format($tot13thNT,2),$TotalBorder);
					$worksheet->write($i+1,6,number_format($tot13thT,2),$TotalBorder);

	$workbook->close();
		

?>