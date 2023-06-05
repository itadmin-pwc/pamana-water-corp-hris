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
	$userview = " AND user_created='{$qryuser['userId']}' and user_updated='{$qryuser['userId']}'";	
}

####Variable passing
$type = "hist";
$empNo = $_POST['empNo'];
$empName = $_POST['txtEmpName'];
if ($_POST['nameType']=="1") {
	$nameType="empLastName";
}
elseif ($_POST['nameType']=="2") {
	$nameType="empFirstName";
}	
else {
	$nameType="empMidName";
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
	$fromdt = date('Y-m-d',strtotime($_GET['from']));
	$todt = date('Y-m-d',strtotime($_GET['to']));
	$datefilter1 = " and tblPAF_Others$type.dateupdated >= '$fromdt' and tblPAF_Others$type.dateupdated <='$todt'";
	$datefilter2 = " and tblPAF_EmpStatus$type.dateupdated >= '$fromdt' and tblPAF_EmpStatus$type.dateupdated <='$todt'";
	$datefilter3 = " and tblPAF_Branch$type.dateupdated >= '$fromdt' and tblPAF_Branch$type.dateupdated <='$todt'";
	$datefilter4 = " and tblPAF_Position$type.dateupdated >= '$fromdt' and tblPAF_Position$type.dateupdated <='$todt'";
	$datefilter5 = " and tblPAF_PayrollRelated$type.dateupdated >= '$fromdt' and tblPAF_PayrollRelated$type.dateupdated <='$todt'";
	$datefilter6 = " and tblPAF_Allowance$type.dateupdated >= '$fromdt' and tblPAF_Allowance$type.dateupdated <='$todt'";
	$datefilter  = " and dateupdated >= '$fromdt' and dateupdated <='$todt'";
	
}
if (empty($pafType) || $pafType =="others") {
	$psObj->arrOthers 	= $psObj->convertArr("tblPAF_Others$type", "  $datefilter1 AND empPayGrp='$group' $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="empstat") {
	$psObj->arrEmpStat 	= $psObj->convertArr("tblPAF_EmpStatus$type", "  $datefilter2 AND empPayGrp='$group' $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="branch") {	
	$psObj->arrBranch 	= $psObj->convertArr("tblPAF_Branch$type", "  $datefilter3 AND empPayGrp='$group' $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="position") {	
	$psObj->arrPosition = $psObj->convertArr("tblPAF_Position$type", "	  $datefilter4 AND empPayGrp='$group' $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="payroll") {
	$psObj->arrPayroll 	= $psObj->convertArr("tblPAF_PayrollRelated$type", "  $datefilter5 AND empPayGrp='$group' $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
if (empty($pafType) || $pafType =="allow") {
	$psObj->arrAllow 	= $psObj->convertArr("tblPAF_Allowance$type", "   $datefilter6 AND empPayGrp='$group' $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $userview");
}
$arrPAF = array_unique(array_merge($psObj->arrOthers,$psObj->arrEmpStat,$psObj->arrBranch,$psObj->arrPosition,$psObj->arrPayroll,$psObj->arrAllow ));
$strPAF = implode(",",$arrPAF);
$strPAF = ($strPAF != "" ? " AND empNo IN ($strPAF)" : "");

####SQL Query####
  $qryIntMaxRec = "SELECT tblEmpMast.empNo,tblEmpMast.empLastName,tblEmpMast.empFirstName, 	tblEmpMast.empMidName,tblDepartment.deptShortDesc,
  						tblDepartment.deptDesc
				   FROM tblEmpMast 
				   INNER JOIN tblDepartment ON tblEmpMast.compCode = tblDepartment.compCode 
				   		AND tblEmpMast.empDiv = tblDepartment.divCode 
				   INNER JOIN tblPosition on tblEmpMast.empPosId=tblPosition.posCode	
				   WHERE tblEmpMast.compCode = '{$sessionVars['compCode']}' AND tblDepartment.deptLevel = '1' 
				   		and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' 
							and empNo='{$_SESSION['employee_number']}') 
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
$filename = 'Posted PAF Proof List'.$todaynewdate. '.xls';
$workbook->send($filename);
$worksheet=&$workbook->addWorksheet('Posted PAF Proof List');
$worksheet->setLandscape();
$worksheet->freezePanes(array(7,0));
$worksheet->setColumn(0,1,5);
$worksheet->setColumn(2,9,20);


####Set up header####
$gmt=time() + (8 * 60 * 60);
$today=date("m/d/Y",$gmt);
$worksheet->write(0,0,$compName,$headerFormat); for($j=1;$j<9;$j++){$worksheet->write(0,$j,"",$headerFormat);}
$worksheet->write(1,0,"Posted PAF Proof List",$headerFormat); for($j=1;$j<9;$j++){$worksheet->write(1,$j,"",$headerFormat);}
$worksheet->write(3,0,"Run Date: ".$today);
$worksheet->write(4,0,"Report ID: POSTEDPAF");
$worksheet->write(6,2,"EMP. NO.",$labelFormat);
$worksheet->write(6,3,"EMPLOYEE NAME",$labelFormat);
$worksheet->write(6,4,"MOVEMENT",$labelFormat);
$worksheet->write(6,5,"OLD VALUE",$labelFormat);
$worksheet->write(6,6,"NEW VALUE",$labelFormat);
$worksheet->write(6,7,"POSITION",$labelFormat);
$worksheet->write(6,8,"DEPARTMENT",$labelFormat);
$worksheet->write(6,9,"EFFECTIVITY DATE",$labelFormat);


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
			$pos = "";
			$dept = "";
			$q = "";
			if ($x == 0) {
				$q = $no;
				$no++;
				$name = $empListVal['empLastName']. ", " . $empListVal['empFirstName'] . " " . $empListVal['empMidName'];
				//$name = $empListVal['empLastName']. " " . $empListVal['empFirstName'][0] . "." . $empListVal['empMidName'][0].".";
				$empNo = $empListVal['empNo'];
				$pos = $empListVal['posDesc'];
				$dept = $empListVal['deptDesc'];
			}	
				$worksheet->write($lastRow,2,$empNo,$detailAlignment);
				$worksheet->write($lastRow,3,$name,$detailAlignment);
				$worksheet->write($lastRow,4,$resArrOthers['field'][$x],$detailAlignment);
				$worksheet->write($lastRow,5,$resArrOthers['value1'][$x],$detailAlignment);
				$worksheet->write($lastRow,6,$resArrOthers['value2'][$x],$detailAlignment);
				$worksheet->write($lastRow,7,$pos,$detailAlignment);
				$worksheet->write($lastRow,8,$dept,$detailAlignment);
				$worksheet->write($lastRow,9,date("m/d/Y",strtotime($resArrOthers['effdate'][$x])),$detailAlignment);
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