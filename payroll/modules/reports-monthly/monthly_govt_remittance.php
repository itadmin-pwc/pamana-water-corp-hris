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
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'merge'));
	$headerFormat->setFontFamily('Calibri'); 
	$headerBorder    = $workbook->addFormat(array('Size' => 10,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'right'));
	$headerBorder2    = $workbook->addFormat(array('Size' => 10,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 1,
									  'Align' => 'left'));

	$headerBorder->setFontFamily('Calibri'); 
	$workbook->setCustomColor(13,155,205,255);
	$TotalBorder    = $workbook->addFormat(array('Align' => 'right','bold'=> 1,'border'=>1,'fgColor' => 'white'));
	$TotalBorder->setFontFamily('Calibri'); 
	$TotalBorder->setTop(5); 
	$detailrBorder   = $workbook->addFormat(array('border' =>1,'Align' => 'right'));
	$detailrBorder->setFontFamily('Calibri'); 
	$detailrBorderAlignRight2   = $workbook->addFormat(array('Align' => 'left'));
	$detailrBorderAlignRight2->setFontFamily('Calibri');
	$detail   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'right'));
	$detail->setFontFamily('Calibri'); 

	$detail2   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'right'));
	$detail2->setFontFamily('Calibri'); 
	$Dept   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'Pattern' => 1,
										  'border' =>1,
										  'Align' => 'left'));
	$Dept->setFontFamily('Calibri'); 
	$Dept2   = $workbook->addFormat(array('Size' => 10,
										  'fgColor' => 'white',
										  'border' =>1,
										  'Pattern' => 1,
										  'Align' => 'left'));
	$Dept2->setFontFamily('Calibri'); 

	$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$payPd = $_GET['payPd'];
	$arrPd = explode("-",$_GET['payPd']);
	$pdYear = $arrPd[2];
	
	$filename = "Monthly_Govt_Remittance";
	$workbook->send("$filename.xls");
if ($_GET['type'] == 1) {
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
	$arrRem = $inqTSObj->getMonthlyGovtRemittance($arrPd[2],$arrPd[0]);
	$worksheet=&$workbook->addWorksheet(str_replace("_"," ","CONTRI ".$arrPd[0]. " ".$arrPd[2]));
	$worksheet->setLandscape();
	$worksheet->freezePanes(array(3, 0));
	$worksheet->setRow(0,16);
	$worksheet->write(0, 0, $inqTSObj->getCompanyName($_SESSION['company_code']),$headerFormat);
	for($i=1;$i<3;$i++) {
		$worksheet->write(0, $i, "",$headerFormat);	
	}
	$worksheet->setColumn(0,0,10);
	$worksheet->setColumn(1,1,25);
	$worksheet->setColumn(2,11,15);
	$worksheet->setRow(1,16);
	$worksheet->write(1, 3, "SSS",$headerFormat);
	for($i=4;$i<7;$i++) {
		$worksheet->write(1, $i, "",$headerFormat);	
	}	
	$worksheet->write(1,6,"PHILHEALTH",$headerBorder);
	for($i=7;$i<9;$i++) {
		$worksheet->write(1, $i, "",$headerFormat);	
	}	
	$worksheet->write(1,9,"HDMF",$headerBorder);
	for($i=10;$i<12;$i++) {
		$worksheet->write(1, $i, "",$headerFormat);	
	}	

	$worksheet->setRow(2,16);
	$worksheet->write(2,0,"GROUP",$headerBorder);
	$worksheet->write(2,1,"BRANCH",$headerBorder);
	$worksheet->write(2,2,"CATEGORY",$headerBorder);
	$worksheet->write(2,3,"EE",$headerBorder);
	$worksheet->write(2,4,"ER",$headerBorder);
	$worksheet->write(2,5,"TOTAL",$headerBorder);
	$worksheet->write(2,6,"EE",$headerBorder);
	$worksheet->write(2,7,"ER",$headerBorder);
	$worksheet->write(2,8,"TOTAL",$headerBorder);
	$worksheet->write(2,9,"EE",$headerBorder);
	$worksheet->write(2,10,"ER",$headerBorder);
	$worksheet->write(2,11,"TOTAL",$headerBorder);

	$branch = "";
	$ctr = 3;
	$grp = "";
	$totsssEE = 0;
	$totsssER = 0;
	$totphicEE = 0;
	$totphicER = 0;
	$tothdmfEE = 0;
	$tothdmfER = 0;
	$GtotsssEE = 0;
	$GtotsssER = 0;
	$GtotphicEE = 0;
	$GtotphicER = 0;
	$GtothdmfEE = 0;
	$GtothdmfER = 0;
	
		foreach($arrRem as $valRem){
			$row = ($col==0) ? $detail2:$detail;
			$row2 = ($col==0) ? $Dept2:$Dept;
			$col = ($col==0) ? 1:0;

			if ($grp!="" && $valRem['payGrp'] != $grp) {

				$worksheet->setRow($ctr,16);
				$worksheet->write($ctr,0,"",$row2);
				$worksheet->write($ctr,1,"GROUP 1 TOTAL",$headerBorder2);
				$worksheet->write($ctr,2,"",$row2);
				$worksheet->write($ctr,3," ".number_format((float)$totsssEE,2),$headerBorder);
				$worksheet->write($ctr,4," ".number_format((float)$totsssER,2),$headerBorder);
				$worksheet->write($ctr,5," ".number_format((float)$totsssEE+(float)$totsssER,2),$headerBorder);
				$worksheet->write($ctr,6," ".number_format((float)$totphicEE,2),$headerBorder);
				$worksheet->write($ctr,7," ".number_format((float)$totphicER,2),$headerBorder);
				$worksheet->write($ctr,8," ".number_format((float)$totphicEE+(float)$totphicER,2),$headerBorder);
				$worksheet->write($ctr,9," ".number_format((float)$tothdmfEE,2),$headerBorder);
				$worksheet->write($ctr,10," ".number_format((float)$tothdmfER,2),$headerBorder);
				$worksheet->write($ctr,11," ".number_format((float)$tothdmfEE+(float)$tothdmfER,2),$headerBorder);
				$totsssEE = 0;
				$totsssER = 0;
				$totphicEE = 0;
				$totphicER = 0;
				$tothdmfEE = 0;
				$tothdmfER = 0;
				$ctr = $ctr + 2;
			}
			if ($valRem['branch'] != $branch) {
				$worksheet->write($ctr,0,$valRem['payGrp'],$row2);
				$worksheet->write($ctr,1,$valRem['branch'],$row2);
			} else {
				$worksheet->write($ctr,0,"",$row2);
				$worksheet->write($ctr,1,"",$row2);
			}
			$branch = $valRem['branch'];
			$grp = $valRem['payGrp'];
			$worksheet->setRow($ctr,16);
			$worksheet->write($ctr,2,category($valRem['payCat']),$row2);
			$worksheet->write($ctr,3," ".number_format((float)$valRem['sssEmp'],2),$row);
			$worksheet->write($ctr,4," ".number_format((float)$valRem['sssEmplr'],2),$row);
			$worksheet->write($ctr,5," ".number_format((float)$valRem['sssEmp']+(float)$valRem['sssEmplr'],2),$row);
			$worksheet->write($ctr,6," ".number_format((float)$valRem['phicEmp'],2),$row);
			$worksheet->write($ctr,7," ".number_format((float)$valRem['phicEmplr'],2),$row);
			$worksheet->write($ctr,8," ".number_format((float)$valRem['phicEmp']+(float)$valRem['phicEmplr'],2),$row);
			$worksheet->write($ctr,9," ".number_format((float)$valRem['hdmfEmp'],2),$row);
			$worksheet->write($ctr,10," ".number_format((float)$valRem['hdmfEmplr'],2),$row);
			$worksheet->write($ctr,11," ".number_format((float)$valRem['hdmfEmp']+(float)$valRem['hdmfEmplr'],2),$row);
			$totsssEE 	= $totsssEE + $valRem['sssEmp'];
			$totsssER 	= $totsssER + $valRem['sssEmplr'];
			$totphicEE 	= $totphicEE + $valRem['phicEmp'];
			$totphicER 	= $totphicER + $valRem['phicEmplr'];
			$tothdmfEE 	= $tothdmfEE + $valRem['hdmfEmp'];
			$tothdmfER 	= $tothdmfER + $valRem['hdmfEmplr'];
			$GtotsssEE 	= $GtotsssEE + $valRem['sssEmp'];
			$GtotsssER 	= $GtotsssER + $valRem['sssEmplr'];
			$GtotphicEE 	= $GtotphicEE + $valRem['phicEmp'];
			$GtotphicER 	= $GtotphicER + $valRem['phicEmplr'];
			$GtothdmfEE 	= $GtothdmfEE + $valRem['hdmfEmp'];
			$GtothdmfER 	= $GtothdmfER + $valRem['hdmfEmplr'];
						
			$ctr++;
		
		}
		$worksheet->setRow($ctr,16);
		$worksheet->write($ctr,0,"",$row2);
		$worksheet->write($ctr,1,"GROUP 2 TOTAL",$headerBorder2);
		$worksheet->write($ctr,2,"",$row2);
		$worksheet->write($ctr,3," ".number_format((float)$totsssEE,2),$headerBorder);
		$worksheet->write($ctr,4," ".number_format((float)$totsssER,2),$headerBorder);
		$worksheet->write($ctr,5," ".number_format((float)$totsssEE+(float)$totsssER,2),$headerBorder);
		$worksheet->write($ctr,6," ".number_format((float)$totphicEE,2),$headerBorder);
		$worksheet->write($ctr,7," ".number_format((float)$totphicER,2),$headerBorder);
		$worksheet->write($ctr,8," ".number_format((float)$totphicEE+(float)$totphicER,2),$headerBorder);
		$worksheet->write($ctr,9," ".number_format((float)$tothdmfEE,2),$headerBorder);
		$worksheet->write($ctr,10," ".number_format((float)$tothdmfER,2),$headerBorder);
		$worksheet->write($ctr,11," ".number_format((float)$tothdmfEE+(float)$tothdmfER,2),$headerBorder);
		$ctr++;
		$ctr++;
		$worksheet->setRow($ctr,16);
		$worksheet->write($ctr,0,"",$row2);
		$worksheet->write($ctr,1,"GRAND TOTAL",$headerBorder2);
		$worksheet->write($ctr,2,"",$row2);
		$worksheet->write($ctr,3," ".number_format((float)$GtotsssEE,2),$headerBorder);
		$worksheet->write($ctr,4," ".number_format((float)$GtotsssER,2),$headerBorder);
		$worksheet->write($ctr,5," ".number_format((float)$GtotsssEE+(float)$GtotsssER,2),$headerBorder);
		$worksheet->write($ctr,6," ".number_format((float)$GtotphicEE,2),$headerBorder);
		$worksheet->write($ctr,7," ".number_format((float)$GtotphicER,2),$headerBorder);
		$worksheet->write($ctr,8," ".number_format((float)$GtotphicEE+(float)$GtotphicER,2),$headerBorder);
		$worksheet->write($ctr,9," ".number_format((float)$GtothdmfEE,2),$headerBorder);
		$worksheet->write($ctr,10," ".number_format((float)$GtothdmfER,2),$headerBorder);
		$worksheet->write($ctr,11," ".number_format((float)$GtothdmfEE+(float)$GtothdmfER,2),$headerBorder);




//SSS LOAN

	$arrSSS = $inqTSObj->getMonthlyLoan('SSS',$arrPd[2],$arrPd[0]);
	$sheetSSS=&$workbook->addWorksheet(str_replace("_"," ","SSS LOAN ".$arrPd[0]. " ".$arrPd[2]));
	$sheetSSS->setLandscape();
	$sheetSSS->freezePanes(array(2, 0));
	$sheetSSS->setRow(0,16);
	$sheetSSS->write(0, 0, $inqTSObj->getCompanyName($_SESSION['company_code']),$headerFormat);
	for($i=1;$i<3;$i++) {
		$sheetSSS->write(0, $i, "",$headerFormat);	
	}
	$sheetSSS->setColumn(0,0,10);
	$sheetSSS->setColumn(1,1,25);
	$sheetSSS->setColumn(2,11,15);
	$sheetSSS->setRow(1,16);
	$sheetSSS->write(1, 0, "GROUP",$headerFormat);
	$sheetSSS->write(1, 1, "BRANCH",$headerFormat);
	$sheetSSS->write(1, 2, "AMOUNT",$headerFormat);
	$branch = "";
	$ctr = 2;
	$grp = "";
	$total = 0;
	$gtotal = 0;
		foreach($arrSSS as $valSSS){
			$row = ($col==0) ? $detail2:$detail;
			$row2 = ($col==0) ? $Dept2:$Dept;
			$col = ($col==0) ? 1:0;

			if ($grp!="" && $valSSS['payGrp'] != $grp) {

				$sheetSSS->setRow($ctr,16);
				$sheetSSS->write($ctr,1,"GROUP 1 TOTAL",$headerBorder2);
				$sheetSSS->write($ctr,2," ".number_format((float)$total,2),$headerBorder);
				$total = 0;
				$ctr = $ctr + 2;
			}
			$branch = $valSSS['branch'];
			$grp = $valSSS['payGrp'];
			$sheetSSS->setRow($ctr,16);
			$sheetSSS->write($ctr,0,$valSSS['payGrp'],$row2);
			$sheetSSS->write($ctr,1,$valSSS['branch'],$row2);
			$sheetSSS->write($ctr,2," ".number_format((float)$valSSS['amount'],2),$row);
			$total 	= $total + $valSSS['amount'];
			$gtotal = $gtotal + $valSSS['amount'];
			$ctr++;
		
		}
		$sheetSSS->setRow($ctr,16);
		$sheetSSS->write($ctr,1,"GROUP 2 TOTAL",$headerBorder2);
		$sheetSSS->write($ctr,2," ".number_format((float)$total,2),$headerBorder);
		$ctr++;
		$ctr++;
		$sheetSSS->setRow($ctr,16);
		$sheetSSS->write($ctr,1,"GRAND TOTAL",$headerBorder2);
		$sheetSSS->write($ctr,2," ".number_format((float)$gtotal,2),$headerBorder);



///HDMF

	$arrHDMF = $inqTSObj->getMonthlyLoan('HDMF',$arrPd[2],$arrPd[0]);
	$sheetHDMF=&$workbook->addWorksheet(str_replace("_"," ","HDMF LOAN ".$arrPd[0]. " ".$arrPd[2]));
	$sheetHDMF->setLandscape();
	$sheetHDMF->freezePanes(array(2, 0));
	$sheetHDMF->setRow(0,16);
	$sheetHDMF->write(0, 0, $inqTSObj->getCompanyName($_SESSION['company_code']),$headerFormat);
	for($i=1;$i<3;$i++) {
		$sheetHDMF->write(0, $i, "",$headerFormat);	
	}
	$sheetHDMF->setColumn(0,0,10);
	$sheetHDMF->setColumn(1,1,25);
	$sheetHDMF->setColumn(2,11,15);
	$sheetHDMF->setRow(1,16);
	$sheetHDMF->write(1, 0, "GROUP",$headerFormat);
	$sheetHDMF->write(1, 1, "BRANCH",$headerFormat);
	$sheetHDMF->write(1, 2, "AMOUNT",$headerFormat);
	$branch = "";
	$ctr = 2;
	$grp = "";
	$total = 0;
	$gtotal = 0;
		foreach($arrHDMF as $valHDMF){
			$row = ($col==0) ? $detail2:$detail;
			$row2 = ($col==0) ? $Dept2:$Dept;
			$col = ($col==0) ? 1:0;

			if ($grp!="" && $valHDMF['payGrp'] != $grp) {

				$sheetHDMF->setRow($ctr,16);
				$sheetHDMF->write($ctr,1,"GROUP 1 TOTAL",$headerBorder2);
				$sheetHDMF->write($ctr,2," ".number_format((float)$total,2),$headerBorder);
				$total = 0;
				$ctr = $ctr + 2;
			}
			$branch = $valHDMF['branch'];
			$grp = $valHDMF['payGrp'];
			$sheetHDMF->setRow($ctr,16);
			$sheetHDMF->write($ctr,0,$valHDMF['payGrp'],$row2);
			$sheetHDMF->write($ctr,1,$valHDMF['branch'],$row2);
			$sheetHDMF->write($ctr,2," ".number_format((float)$valHDMF['amount'],2),$row);
			$total 	= $total + $valHDMF['amount'];
			$gtotal = $gtotal + $valHDMF['amount'];
			$ctr++;
		
		}
		$sheetHDMF->setRow($ctr,16);
		$sheetHDMF->write($ctr,1,"GROUP 2 TOTAL",$headerBorder2);
		$sheetHDMF->write($ctr,2," ".number_format((float)$total,2),$headerBorder);
		$ctr++;
		$ctr++;
		$sheetHDMF->setRow($ctr,16);
		$sheetHDMF->write($ctr,1,"GRAND TOTAL",$headerBorder2);
		$sheetHDMF->write($ctr,2," ".number_format((float)$gtotal,2),$headerBorder);

} else {
//PFI

	$arrpayPd = $inqTSObj->getPayPeriod($_SESSION['company_code']," and pdSeries='{$_GET['payPd']}'");

	$arrPFI = $inqTSObj->getMonthlyLoan('PFI','',$_GET['payPd']);
	$sheetPFI=&$workbook->addWorksheet(str_replace("_"," ","PFI LOAN ".date('m-d-Y',strtotime($arrpayPd['pdPayable']))));
	$sheetPFI->setLandscape();
	$sheetPFI->freezePanes(array(2, 0));
	$sheetPFI->setRow(0,16);
	$sheetPFI->write(0, 0, $inqTSObj->getCompanyName($_SESSION['company_code']),$headerFormat);
	for($i=1;$i<3;$i++) {
		$sheetPFI->write(0, $i, "",$headerFormat);	
	}
	$sheetPFI->setColumn(0,0,10);
	$sheetPFI->setColumn(1,1,25);
	$sheetPFI->setColumn(2,11,15);
	$sheetPFI->setRow(1,16);
	$sheetPFI->write(1, 0, "GROUP",$headerFormat);
	$sheetPFI->write(1, 1, "BRANCH",$headerFormat);
	$sheetPFI->write(1, 2, "AMOUNT",$headerFormat);
	$branch = "";
	$ctr = 2;
	$grp = "";
	$total = 0;
	$gtotal = 0;
		foreach($arrPFI as $valPFI){
			$row = ($col==0) ? $detail2:$detail;
			$row2 = ($col==0) ? $Dept2:$Dept;
			$col = ($col==0) ? 1:0;

			if ($grp!="" && $valPFI['payGrp'] != $grp) {

				$sheetPFI->setRow($ctr,16);
				$sheetPFI->write($ctr,1,"GROUP 1 TOTAL",$headerBorder2);
				$sheetPFI->write($ctr,2," ".number_format((float)$total,2),$headerBorder);
				$total = 0;
				$ctr = $ctr + 2;
			}
			$branch = $valPFI['branch'];
			$grp = $valPFI['payGrp'];
			$sheetPFI->setRow($ctr,16);
			$sheetPFI->write($ctr,0,$valPFI['payGrp'],$row2);
			$sheetPFI->write($ctr,1,$valPFI['branch'],$row2);
			$sheetPFI->write($ctr,2," ".number_format((float)$valPFI['amount'],2),$row);
			$total 	= $total + $valPFI['amount'];
			$gtotal = $gtotal + $valPFI['amount'];
			$ctr++;
		
		}
		$sheetPFI->setRow($ctr,16);
		$sheetPFI->write($ctr,1,"GROUP 2 TOTAL",$headerBorder2);
		$sheetPFI->write($ctr,2," ".number_format((float)$total,2),$headerBorder);
		$ctr++;
		$ctr++;
		$sheetPFI->setRow($ctr,16);
		$sheetPFI->write($ctr,1,"GRAND TOTAL",$headerBorder2);
		$sheetPFI->write($ctr,2," ".number_format((float)$gtotal,2),$headerBorder);
}
		
	$workbook->close();

function category($cat) {
	switch($cat) {
		case "1":
			$cat = "Exec";
		break;
		case "2":
			$cat = "Confi";
		break;
		case "3":
			$cat = "Non Confi";
		break;
	}
	return $cat;

}
?>