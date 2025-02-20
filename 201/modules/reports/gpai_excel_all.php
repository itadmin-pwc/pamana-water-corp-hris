<?
########## INCLUDE FILE ##########
session_start();
ini_set('include_path','C:\wamp\bin\php\php5.2.6\PEAR\pear');
require_once 'Spreadsheet/Excel/Writer.php';
include_once('../../../includes/db.inc.php');
include_once('../../../includes/common.php');
include_once('timesheet_obj.php');
include_once('../../../includes/pdf/fpdf.php');
define('FPDF_FONTPATH','../../../includes/pdf/font');

	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	$compname = $inqTSObj->getCompanyName($compCode);
	$brnCode = $_GET['branch'];
	$payrollDate = $_GET['pddate'];
	$cutoff = $_GET['costart'] . " - " . $_GET['coend'];	
	$group = $_GET['pgroup'];
	
########## QUERY SET UP ##########
	if($brnCode==0){
		$sqlBr = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' and brnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}') order by brnDesc";	
	}
	else{
		$sqlBr = "SELECT * FROM tblBranch WHERE compCode = '{$_SESSION['company_code']}' and brnCode = '{$brnCode}'";
	}
	$resBr = mysql_query($sqlBr);
	$numBranches = mysql_num_rows($resBr);



########## SET UP EXCEL FORMATS ##########
$workbook = new Spreadsheet_Excel_Writer();
$deptHeader = $workbook->addFormat(array('Size'=>10, 'Color'=>'black', 'Bold'=>1, 'Align'=>'merge'));
$headerFormat = $workbook->addFormat(array('Size'=>10, 'Color'=>'black', 'Bold'=>1, 'Align'=>'left'));
$headerBorder = $workbook->addFormat(array('Border'=>1, 'Size'=>'10', 'Color'=>'red', 'Bold'=>1, 'Align'=>'center'));
$detailBorder = $workbook->addFormat(array('Border'=>1, 'Align'=>'left'));
$subHeaderBorder = $workbook->addFormat(array('Size'=>10, 'Color'=>'black', 'Bold'=>1, 'Align'=>'right'));
$filename = "gpaiupdateform".$todaynewdate.".xls";
$workbook->send($filename);
$worksheet=&$workbook->addWorkSheet("GPAI UPDATE FORM");
$worksheet->setLandscape();


########## SET UP REPORT HEAD ##########
$gmt = time() + (8 * 60 * 60);
$today = date('m/d/Y', $gmt);
$worksheet->write(0,0,'RUN DATE',$headerFormat); for($j=1; $j<=2; $j++){ $worksheet->write(0,$j,"",$headerFormat); }
$worksheet->write(0,2,$today,'');
$worksheet->write(1,0,'COMPANY', $headerFormat); for($j=1; $j<=2; $j++){ $worksheet->write(1,$j,"",$headerFormat); }
$worksheet->write(1,2,$compname,'');
$worksheet->write(2,0,'GROUP',$headerFormat); for($j=1; $j<=2; $j++){ $worksheet->write(3,$j,"",$headerFormat); } 
$worksheet->write(2,2,$group,'');
$worksheet->write(3,0,'PAYROLL DATE',$headerFormat); for($j=1; $j<=2; $j++){ $worksheet->write(4,$j,"",$headerFormat); } 
$worksheet->write(3,2,$payrollDate,'');
$worksheet->write(4,0,'CUT OFF',$headerFormat); for($j=1; $j<=2; $j++){ $worksheet->write(5,$j,"",$headerFormat); } 
$worksheet->write(4,2,$cutoff,'');

######### SET UP DATA HEADER ##########
$worksheet->write(7,0,"GPAI UPDATE FORM",$deptHeader); for($i=1;$i<=9;$i++){ $worksheet->write(7,$i,"",$deptHeader); }
//for($b=0;$b<$numBranches;$b++){

######### ENROLLMENT ##########	
	
$worksheet->write(9,1,"ENROLLMENT",$headerFormat);
$worksheet->write(10,2,"SURNAME",$headerBorder);
$worksheet->write(10,3,"FIRSTNAME",$headerBorder);
$worksheet->write(10,4,"MIDDLENAME",$headerBorder);
$worksheet->write(10,5,"POSITION",$headerBorder);
$worksheet->write(10,6,"DATE HIRED",$headerBorder);
$worksheet->write(10,7,"BIRTH DATE",$headerBorder);
$worksheet->write(10,8,"BRANCH",$headerBorder);
$lastrow = 11;

	$qryRank = "Select tblEmpMast_New.empRank, tblRankType.rankDesc
					from tblEmpMast_new 
					Inner Join tblPosition on tblEmpMast_New.empPosId=tblPosition.posCode
					Inner Join tblRankType on tblEmpMast_New.empRank=tblRankType.rankCode
					where tblEmpMast_New.compCode='".$_SESSION['company_code']."' 
					and tblEmpMast_New.empPayGrp='".$_GET['pgroup']."' 
					and tblEmpMast_New.dateReleased between '".date("%Y-%m-%d",strtotime($_GET['costart']))."' 
					and '".date("%Y-%m-%d",strtotime($_GET['coend']))."' 
					and tblEmpMast_New.stat='R'
					group by tblEmpMast_New.empRank, tblRankType.rankDesc
					order by tblEmpMast_New.empRank Desc";
	$rsGetRank = mysql_query($qryRank);
	$numR = mysql_num_rows($rsGetRank);	
	for($rr=0; $rr<$numR; $rr++){
		$worksheet->write($lastrow, 2,mysql_result($rsGetRank,$rr,"rankDesc"),$headerFormat);
		$lastrow=$lastrow+1;
		$qryNew = "Select tblEmpMast_New.empLastName, tblEmpMast_New.empFirstName, tblEmpMast_New.empMidName, tblEmpMast_New.dateHired, 
						tblEmpMast_New.empBday, tblEmpMast_New.empPosId, tblPosition.posShortDesc, tblEmpMast_New.empRank, tblRankType.rankDesc,
						tblBranch.brnShortDesc
						from tblEmpMast_new 
						Inner Join tblPosition on tblEmpMast_New.empPosId=tblPosition.posCode
						Inner Join tblRankType on tblEmpMast_New.empRank=tblRankType.rankCode
						Inner Join tblBranch on tblEmpMast_New.empBrnCode=tblBranch.brnCode
						where tblEmpMast_New.compCode='".$_SESSION['company_code']."' 
						and tblEmpMast_New.empPayGrp='".$_GET['pgroup']."' 
						and tblEmpMast_New.dateReleased between '".date("%Y-%m-%d",strtotime($_GET['costart']))."' 
						and '".date("%Y-%m-%d",strtotime($_GET['coend']))."' 
						and tblEmpMast_New.stat='R'
						and tblEmpMast_New.empRank='".mysql_result($rsGetRank,$rr,"empRank")."'
						order by tblEmpMast_New.empLastName";
		$rsGetNew = mysql_query($qryNew);
		$num = mysql_num_rows($rsGetNew);	
		for($r=0; $r<$num; $r++){
			$cnt=$r+1;
			$worksheet->write($lastrow, 1,$cnt,"");
			$worksheet->write($lastrow, 2,strtoupper(mysql_result($rsGetNew,$r,"empLastName")),$detailBorder);
			$worksheet->write($lastrow, 3,strtoupper(mysql_result($rsGetNew,$r,"empFirstName")),$detailBorder);
			$worksheet->write($lastrow, 4,strtoupper(mysql_result($rsGetNew,$r,"empMidName")),$detailBorder);
			$worksheet->write($lastrow, 5,strtoupper(mysql_result($rsGetNew,$r,"posShortDesc")),$detailBorder);
			$worksheet->write($lastrow, 6,strtoupper(date('F d, Y',strtotime(mysql_result($rsGetNew,$r,"dateHired")))),$detailBorder);
			$worksheet->write($lastrow, 7,strtoupper(date('F d, Y',strtotime(mysql_result($rsGetNew,$r,"empBday")))),$detailBorder);
			$worksheet->write($lastrow, 8,strtoupper(mysql_result($rsGetNew,$r,"brnShortDesc")),$detailBorder);
			$lastrow++;
		}	 
		$ntotal = $ntotal+$cnt;
	}
		
		$worksheet->write($lastrow, 1,($ntotal==0?"0":$ntotal),$subHeaderBorder);
		$worksheet->write($lastrow, 2,($ntotal<=1?"TOTAL NUMBER OF NEWLY HIRED EMPLOYEE":"TOTAL NUMBER OF NEWLY HIRED EMPLOYEES"),$headerFormat);
//}
		
		
	######### DELETION ##########	
	$lastrow=$lastrow+3;	
	$worksheet->write($lastrow,1,"DELETION",$headerFormat);
	$worksheet->write($lastrow+1,2,"SURNAME",$headerBorder);
	$worksheet->write($lastrow+1,3,"FIRSTNAME",$headerBorder);
	$worksheet->write($lastrow+1,4,"MIDDLENAME",$headerBorder);
	$worksheet->write($lastrow+1,5,"POSITION",$headerBorder);
	$worksheet->write($lastrow+1,6,"REASON FOR SEPARATION",$headerBorder);
	$worksheet->write($lastrow+1,7,"DATE OF SEPARATION",$headerBorder);
	$worksheet->write($lastrow+1,8,"BRANCH",$headerBorder);
	$lastrow=$lastrow+2;
	
	$qryRankDel = "Select tblEmpMast.empRank, tblRankType.rankDesc
					from tblEmpMast 
					Inner Join tblPosition on tblEmpMast.empPosId=tblPosition.posCode
					Inner Join tblRankType on tblEmpMast.empRank=tblRankType.rankCode
					Left Join tblSeparatedEmployees on  tblEmpMast.empNo=tblSeparatedEmployees.empNo
					where tblEmpMast.compCode='".$_SESSION['company_code']."'  
					and tblEmpMast.empPayGrp='".$_GET['pgroup']."' and (tblEmpMast.empStat='RS' or tblEmpMast.empStat='IN')  
					and (tblEmpMast.dateResigned between '".date("%Y-%m-%d",strtotime($_GET['costart']))."' 
					and '".date("%Y-%m-%d",strtotime($_GET['coend']))."' 
					or  tblEmpMast.endDate between '".date("%Y-%m-%d",strtotime($_GET['costart']))."' 
					and '".date("%Y-%m-%d",strtotime($_GET['coend']))."')
					group by tblEmpMast.empRank, tblRankType.rankDesc
					order by tblEmpMast.empRank Desc";
	$rsGetRankDel = mysql_query($qryRankDel);
	$numRank = mysql_num_rows($rsGetRankDel);	
	
	for($rDel=0; $rDel<$numRank; $rDel++){
		$worksheet->write($lastrow, 2,mysql_result($rsGetRankDel,$rDel,"rankDesc"),$headerFormat);
		$lastrow=$lastrow+1;

		$qryDel = "Select tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblEmpMast.dateHired, 
					tblEmpMast.empBday, tblEmpMast.empPosId, tblPosition.posShortDesc, tblEmpMast.empRank, tblRankType.rankDesc, 
					tblEmpMast.empStat, tblEmpMast.dateResigned, tblEmpMast.endDate, tblEmpMast.empNo, 
					tblSeparatedEmployees.reason, tblSeparatedEmployees.natureCode, tblBranch.brnShortDesc
					from tblEmpMast 
					Inner Join tblPosition on tblEmpMast.empPosId=tblPosition.posCode
					Inner Join tblRankType on tblEmpMast.empRank=tblRankType.rankCode
					Inner Join tblBranch on tblEmpMast.empBrnCode=tblBranch.brnCode
					Left Join tblSeparatedEmployees on  tblEmpMast.empNo=tblSeparatedEmployees.empNo
					where tblEmpMast.compCode='".$_SESSION['company_code']."' 
					and tblEmpMast.empPayGrp='".$_GET['pgroup']."' and (tblEmpMast.empStat='RS' or tblEmpMast.empStat='IN')  
					and (tblEmpMast.dateResigned between '".date("%Y-%m-%d",strtotime($_GET['costart']))."' 
					and '".date("%Y-%m-%d",strtotime($_GET['coend']))."' 
					or  tblEmpMast.endDate between '".date("%Y-%m-%d",strtotime($_GET['costart']))."' 
					and '".date("%Y-%m-%d",strtotime($_GET['coend']))."')
					and tblEmpMast.empRank='".mysql_result($rsGetRankDel,$rDel,"empRank")."'
					order by tblEmpMast.empLastName";
		$rsGetDel = mysql_query($qryDel);
		$numDel = mysql_num_rows($rsGetDel);	
		
		for($d=0; $d<$numDel; $d++){
			$cntD=$d+1;
				if(mysql_result($rsGetDel,$d,"dateResigned")!=""){
					$dateSeparated = mysql_result($rsGetDel,$d,"dateResigned");	
				}
				else{
					$dateSeparated = mysql_result($rsGetDel,$d,"endDate");		
				}
				if(mysql_result($rsGetDel,$d,"natureCode")==1){
					$reason = "Absent without leave";	
				}
				elseif(mysql_result($rsGetDel,$d,"natureCode")==2){
					$reason = "End of contract";	
				}
				elseif(mysql_result($rsGetDel,$d,"natureCode")==3){
					$reason = "Resigned";	
				}
				elseif(mysql_result($rsGetDel,$d,"natureCode")==4){
					$reason = "Transferred";	
				}
				elseif(mysql_result($rsGetDel,$d,"natureCode")==5){
					$reason = "Terminated for a cause";	
				}
			
			$worksheet->write($lastrow, 1,$cntD,"");
			$worksheet->write($lastrow, 2,strtoupper(mysql_result($rsGetDel,$d,"empLastName")),$detailBorder);
			$worksheet->write($lastrow, 3,strtoupper(mysql_result($rsGetDel,$d,"empFirstName")),$detailBorder);
			$worksheet->write($lastrow, 4,strtoupper(mysql_result($rsGetDel,$d,"empMidName")),$detailBorder);
			$worksheet->write($lastrow, 5,strtoupper(mysql_result($rsGetDel,$d,"posShortDesc")),$detailBorder);
			$worksheet->write($lastrow, 6,strtoupper($reason) ,$detailBorder);
			$worksheet->write($lastrow, 7,strtoupper(date('F d, Y',strtotime($dateSeparated))),$detailBorder);
			$worksheet->write($lastrow, 8,strtoupper(mysql_result($rsGetDel,$d,"brnShortDesc")),$detailBorder);
			$lastrow++;		
		}
		$ntotalD = $ntotalD+$cntD;
	}
		$worksheet->write($lastrow, 1,($ntotalD==0?"0":$ntotalD),$subHeaderBorder);
		$worksheet->write($lastrow, 2,($ntotalD<=1?"TOTAL NUMBER OF SEPARATED EMPLOYEE":"TOTAL NUMBER OF SEPARATED EMPLOYEES"),$headerFormat);

$lastrow=$lastrow+2;
$userId= $inqTSObj->getSeesionVars();
$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
$worksheet->write($lastrow, 0, "* * * End of report. Nothing follows. * * *",$deptHeader);

for ($j=1; $j<=9; $j++) {
	$worksheet->write($lastrow, $j, "",$deptHeader);
}

$lastrow=$lastrow+2;
$worksheet->write($lastrow, 1, $prntdBy,"");
$workbook->close();
?>