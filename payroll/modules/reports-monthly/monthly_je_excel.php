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

	$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$payPd = $_GET['payPd'];
	$arrPd = explode("-",$_GET['payPd']);
	$pdYear = $arrPd[2];
	$arrpdPeriod = $inqTSObj->getpdPayDate($pdYear);
	switch($arrPd[0]) {
		case 1:
			$pdMonth = "Jan $pdYear";
		break;
		case 3:
			$pdMonth = "Feb $pdYear";
		break;
		case 5:
			$pdMonth = "Mar $pdYear";
		break;
		case 7:
			$pdMonth = "Apr $pdYear";
		break;
		case 9:
			$pdMonth = "May $pdYear";
		break;
		case 11:
			$pdMonth = "Jun $pdYear";
		break;
		case 13:
			$pdMonth = "Jul $pdYear";
		break;
		case 15:
			$pdMonth = "Aug $pdYear";
		break;
		case 17:
			$pdMonth = "Sep $pdYear";
		break;
		case 19:
			$pdMonth = "Oct $pdYear";
		break;
		case 21:
			$pdMonth = "Nov $pdYear";
		break;
		case 23:
			$pdMonth = "Dec $pdYear";
		break;
	}	
	$filename = ($_GET['report_type']==1) ? "SAW_$pdMonth":"TP_$pdMonth";
	$arrYTD = $inqTSObj->getMonthly_JE($payPd,$_GET['report_type']);
	$workbook->send("$filename.xls");
	$worksheet=&$workbook->addWorksheet(str_replace("_"," ","$filename"));
	$worksheet->setLandscape();
	$worksheet->freezePanes(array(2, 0));
	$worksheet->setRow(0,16);
	$worksheet->write(0, 0, $inqTSObj->getCompanyName($_SESSION['company_code']),$headerFormat);
	for($i=1;$i<3;$i++) {
		$worksheet->write(0, $i, "",$headerFormat);	
	}
	$worksheet->setColumn(0,0,30);
	$worksheet->setColumn(1,8,30);
	$worksheet->setRow(1,16);
	$worksheet->write(1,0,"PAYREG ID",$headerBorder);
	$worksheet->write(1,1,"PAYROLL PERIOD",$headerBorder);
	$worksheet->write(1,2,"ACCOUNT",$headerBorder);
	$worksheet->write(1,3,"DESCRIPTION",$headerBorder);
	$worksheet->write(1,4,"AMOUNT",$headerBorder);

		$branch="";
		$ctr=2;
		$i=0;
		$q=0;
		$totRec = count($arrYTD);
		$GtotGross 		= 0;
		$GtotTax 		= 0;
		$GtotEcola 		= 0;
		$Gtot13thNT 	= 0;
		$Gtot13thT 		= 0;
		$arrDept 		= array();
		$col=0;
		foreach($arrYTD as $valYTD){
			if ($valYTD['brnDesc'] != $branch) {
				if ($ch == 0 && $branch!="") {
					$worksheet->setRow($ctr,16);
					$worksheet->write($ctr,0,"Branch Total",$TotalBorder);
					$worksheet->write($ctr,1,"",$TotalBorder);
					$worksheet->write($ctr,2,"",$TotalBorder);
					$worksheet->write($ctr,3,"",$TotalBorder);
					$worksheet->write($ctr,4,number_format($totAmount,2),$TotalBorder);
					$ch++;
					$ctr++;
					$ctr++;
				}			
				$ch			= 0;
				$totAmount 	= 0;
				$totTax 	= 0;
				$totTaxAdj 	= 0;
				$totEcola 	= 0;
				$tot13thNT 	= 0;
				$tot13thT 	= 0;
				$worksheet->setRow($ctr,16);
				$worksheet->write($ctr,0,$valYTD['brnDesc'],$headerFormat);
				$ctr++;
				$branch = $valYTD['brnDesc'];
				
			}
			$row = ($col==0) ? $detail2:$detail;
			$row2 = ($col==0) ? $Dept2:$Dept;
			$col = ($col==0) ? 1:0;
			$worksheet->setRow($ctr,16);
			$worksheet->write($ctr,0," ".$valYTD['payRegID'],$row2);
			$worksheet->write($ctr,1,getpayDate($valYTD['payGrp'],$valYTD['pdNumber'],$arrpdPeriod),$row2);
			$worksheet->write($ctr,2," ".$valYTD['Account'],$row2);
			$worksheet->write($ctr,3,ucwords(strtolower($valYTD['glCodeDesc'])),$row2);
			$worksheet->write($ctr,4,number_format($valYTD['Amount'],2),$row);
			$ctr++;
			$q++;
			$totAmount 	+= round($valYTD['Amount'],2);
				if ($q == $totRec) {
					$worksheet->setRow($ctr,16);
					$worksheet->write($ctr,0,"Branch Total",$TotalBorder);
					$worksheet->write($ctr,1,"",$TotalBorder);
					$worksheet->write($ctr,2,"",$TotalBorder);
					$worksheet->write($ctr,3,"",$TotalBorder);
					$worksheet->write($ctr,4,number_format($totAmount,2),$TotalBorder);
					$ctr++;
				}		
		}
	$workbook->close();
		
	
function getpayDate($payGrp,$pdNumber,$arrpdPeriod) {
	$payDate = "";
	foreach($arrpdPeriod as $val) {
		if ($val['pdNumber']==$pdNumber && $val['payGrp']==$payGrp) {
			$payDate = $val['pdPayable'];
		}
	}
	return $payDate;
}
?>