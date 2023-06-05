<?
####include files####
session_start();
ini_set('include_path','D:\wamp\php\PEAR');
include_once("Spreadsheet/Excel/Writer.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");

####Initialize object
	$psObj=new inqTSObj();
	
####Get session variables	
	$sessionVars=$psObj->getSeesionVars();
	$psObj->validateSessions('','MODULES');
	
	$compCode = $_SESSION['company_code'];
	$psObj->compCode     = $compCode;
	$compName 		= $psObj->getCompanyName($compCode);

####Query to limit the output to encoder
$qryuser=$psObj->getUserLogInInfo($_SESSION['company_code'],$_SESSION['employee_number']);
if($qryuser['userLevel']==3){
	$userview = " AND userid='{$qryuser['userId']}'";	
}

####Variable passing
$type = ($_GET['type']==1) ? "hist" : "";
$empNo = $_GET['empNo'];
$empName = $_GET['txtEmpName'];
if($_GET['nameType']==1){
	$nameType="empLastName";	
}
elseif($_GET['nameType']==2){
	$nameType="empFirstName";	
}
else{
	$nameType="empMiddleName";	
}
$cmbDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$pafType = $_GET['pafType'];
$group = $_GET['group'];

####Set up to filter data
$empNo1 = ($empNo>""?" AND (tblEmpMast.empNo LIKE '{$empNo}%')":"");
$cmbDiv1 = ($cmbDiv>"" && $cmbDiv>0 ? " AND (empDiv = '{$cmbDiv}')":"");
$empDept1 = ($empDept>"" && $empDept>0 ? " AND (empDepCode = '{$empDept}')":"");
$empSect1 = ($empSect>"" && $empSect>0 ? " AND (empSecCode = '{$empSect}')":"");
$empName1=($empName>""?" AND ($nameType LIKE '{$empName}%')":"");

if ($_GET['from'] != "" && $_GET['to'] != "") {
	$fromdt = $_GET['from'];
	$todt = $_GET['to'];
	$datefilter = " and dateadded >= '$fromdt' and dateadded <='$todt'";
}
if (empty($pafType) || $pafType =="others") {
	$psObj->arrOthers 		= $psObj->convertArr("tblPAF_Others$type", " AND stat='H' $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="empstat") {
	$psObj->arrEmpStat 		= $psObj->convertArr("tblPAF_EmpStatus$type", " AND stat='H' $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="branch") {	
	$psObj->arrBranch 		= $psObj->convertArr("tblPAF_Branch$type", " AND stat='H' $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="position") {	
	$psObj->arrPosition 		= $psObj->convertArr("tblPAF_Position$type", " AND stat='H' $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="payroll") {
	$psObj->arrPayroll 		= $psObj->convertArr("tblPAF_PayrollRelated$type", " AND stat='H' $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="allow") {
	$psObj->arrAllow 		= $psObj->convertArr("tblPAF_Allowance$type", "  AND stat='H' $datefilter $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
$arrPAF = array_unique(array_merge($psObj->arrOthers,$psObj->arrOthers,$psObj->arrEmpStat,$psObj->arrBranch,$psObj->arrPosition,$psObj->arrPayroll,$psObj->arrAllow ));
$strPAF = implode(",",$arrPAF);
$strPAF = ($strPAF != "" ? " AND empNo IN ($strPAF)" : "");

####SQL Query####	
  $qryIntMaxRec = "SELECT tblEmpMast.empNo,tblEmpMast.empLastName,tblEmpMast.empFirstName,
				tblEmpMast.empMidName,tblDepartment.deptShortDesc, tblDepartment.deptDesc 
				FROM tblEmpMast INNER JOIN tblDepartment ON 
				tblEmpMast.compCode = tblDepartment.compCode 
				AND tblEmpMast.empDiv = tblDepartment.divCode 
			    WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}'
			    AND tblDepartment.deptLevel = '1' 
				and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
				$empNo1 $empName1 $cmbDiv1 $empDept1 $empSect1 $strPAF
				order by empDiv,empLastName,empFirstName,empMidName";
$resGetDealsList = mysql_query($qryIntMaxRec);
$num = mysql_num_rows($resGetDealsList);
				
####Excel set up####
$workbook = new Spreadsheet_Excel_Writer();
$deptheader = $workbook->addFormat(array('size'=>10,'color'=>'blue','bold'=>1));
$headerFormat = $workbook->addFormat(array('size'=>10,'color'=>'blue','bold'=>1,'align'=>'merge'));
$headerBorder = $workbook->addFormat(array('border'=>4));
$detailBorder = $workbook->addFormat(array('border'=>2));
$detailBorderAlignment = $workbook->addFormat(array('align'=>'right'));
$detailAlignment = $workbook->addFormat(array('align'=>'left'));
$labelFormat = $workbook->addFormat(array('align'=>'center','bold'=>1));
$labelParent = $workbook->addFormat(array('bold'=>1,'align'=>'left'));
$filename = 'Held PAF Proof List'.$todaynewdate. '.xls';
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('Held PAF Proof List');
$worksheet->setLandscape();
$worksheet->freezePanes(array(7,0));
$worksheet->setColumn(0,1,5);
$worksheet->setColumn(2,9,20);


####Set up header####
$gmt=time() + (8 * 60 * 60);
$today=date("m/d/Y",$gmt);
$worksheet->write(0,0,$compName,$headerFormat); for($j=1;$j<9;$j++){$worksheet->write(0,$j,"",$headerFormat);}
$worksheet->write(1,0,"Held PAF Proof List",$headerFormat); for($j=1;$j<9;$j++){$worksheet->write(1,$j,"",$headerFormat);}
$worksheet->write(3,0,"Run Date: ".$today);
$worksheet->write(4,0,"Report ID: HELDPAF");
$worksheet->write(6,2,"EMP. NO.",$labelFormat);
$worksheet->write(6,3,"EMPLOYEE NAME",$labelFormat);
$worksheet->write(6,4,"MOVEMENT",$labelFormat);
$worksheet->write(6,5,"OLD VALUE",$labelFormat);
$worksheet->write(6,6,"NEW VALUE",$labelFormat);
$worksheet->write(6,7,"EFFECTIVITY DATE",$labelFormat);

####Set up content####
$lastRow = 7;
for ($i=0;$i<$num;$i++){ 
	while($empListVal=mysql_fetch_array($resGetDealsList)){
		$resArrOthers = $psObj->getPAF_others($empListVal['empNo'],$pafType,$datefilter."",$type);
		$ctr=count($resArrOthers['value1']);
		$empNo = $empListVal['empNo'];
		$divdesc2 = $empListVal['deptShortDesc'];
		if ($divdesc != $divdesc2 || $divdesc =="") {
			$worksheet->write($lastRow,0,$empListVal['deptShortDesc'],$labelParent); for($j=1;$j<9;$j++){$worksheet->write($lastRow,$j,"",$labelParent);}
			$lastRow++;
		}	
		$divdesc = $empListVal['deptShortDesc'];
		for($x=0;$x<$ctr; $x++) {
			$name = "";
			$empNo = "";
			$q = "";
			if ($x == 0) {
				$q = $no;
				$no++;
				$name = $empListVal['empLastName']. " " . $empListVal['empFirstName'][0] . "." . $empListVal['empMidName'][0].".";
				$empNo = $empListVal['empNo'];
			}		
				$worksheet->write($lastRow,2,$empNo,$detailAlignment);
				$worksheet->write($lastRow,3,$name,$detailAlignment);
				$worksheet->write($lastRow,4,$resArrOthers['field'][$x],$detailAlignment);
				$worksheet->write($lastRow,5,$resArrOthers['value1'][$x],$detailAlignment);
				$worksheet->write($lastRow,6,$resArrOthers['value2'][$x],$detailAlignment);
				$worksheet->write($lastRow,7,date("m/d/Y",strtotime($resArrOthers['effdate'][$x])),$detailAlignment);
				$lastRow++;	
		}	
	}
}

####Set up report footer####
$lastRow=$lastRow+1;
$userID=$psObj->getSeesionVars();
$disUser=$psObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$prntBy="Printed By: ".$disUser['empFirstName']." ".$disUser['empLastName'];
$worksheet->write($lastRow,0,"* * * End of report. Nothing follows. * * *",$headerFormat);
for($j=1;$j<9;$j++){
	$worksheet->write($lastRow,$j,"",$headerFormat);	
}
$worksheet->write($lastRow+1,0,$prntBy);
$workbook->close();
?>