<?
####include files####
session_start();
ini_set('include_path','D:\wamp\php\PEAR');
include_once("SpreadSheet/Excel/Writer.php");
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

if ($_GET['from'] != "" && $_GET['to'] != "") {
	$fromdt = $_GET['from'];
	$todt = $_GET['to'];
	$date = "$fromdt - $todt";
} 

####Query to limit the output to encoder
$qryuser=$psObj->getUserLogInInfo($_SESSION['company_code'],$_SESSION['employee_number']);
if($qryuser['userLevel']==3){
	$userview = $qryuser['userId'];
	$ulevel="3";
}

####Query to show user
$sqlUsers = "SELECT tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblUsers.userId FROM tblUsers INNER JOIN tblEmpMast ON tblUsers.empNo = tblEmpMast.empNo AND tblUsers.compCode = tblEmpMast.compCode where tblEmpMast.compCode='{$_SESSION['company_code']}'";
$arrUsers = $psObj->getArrResI($psObj->execQryI($sqlUsers));

$grp = ($_GET['group'] !='' && $_GET['group'] !='0') ? ",'{$_GET['group']}'" : "";

####Query to show employee data from stored procedures
$qryIntMaxRec = $psObj->getEmpProoflist($_SESSION['company_code'],$_GET['status'],date('Y-m-d',strtotime($_GET['from'])),date('Y-m-d',strtotime($_GET['to'])),$_SESSION['employee_number'],$_GET['group'],$userview,$ulevel,$cmbDiv,$empDept,$empSect);
$arrEmpList = $psObj->getArrRes($qryIntMaxRec);
//$arrcnt = $psObj->getRecCount($resEmpList);

####Query to show Division,Department,Section
$sqlDept = "SELECT divCode, deptCode, sectCode, deptDesc, deptLevel FROM tblDepartment where compCode='{$_SESSION['company_code']}'";
$arrDept = $psObj->getArrRes($psObj->execQry($sqlDept));


####Set up data format
$workbook = new Spreadsheet_Excel_Writer();
$headerFormat = $workbook->addFormat(array('size'=>10,'color'=>'blue','bold'=>1,'align'=>'merge'));
$headerBorder = $workbook->addFormat(array('border'=>4));
$detailBorder = $workbook->addFormat(array('border'=>2));
$detailLabel = $workbook->addFormat(array('bold'=>1,'align'=>'left'));
$detailData = $workbook->addFormat(array('align'=>'left'));
$filename = 'New Employee Proof List'.$todaynewdate.'.xls';
$workbook->send($filename);
$worksheet =&$workbook->addWorksheet('New Employee Proof List');
$worksheet->setLandscape();
$worksheet->freezePanes(array(5,0));
$worksheet->setColumn(0,1,5);
$worksheet->setColumn(2,9,20);

####Set up header####
$gmt=time() + (8 * 60 * 60);
$today=date('m/d/Y',$gmt);

$worksheet->write(0,0,$compName,$headerFormat); for($i=1;$i<9;$i++){$worksheet->write(0,$i,"",$headerFormat);}
$worksheet->write(1,0,"New Employee Proof List",$headerFormat); for($i=1;$i<9;$i++){$worksheet->write(1,$i,"",$headerFormat);}
$worksheet->write(2,0,"Run Date: ".$today);
$worksheet->write(3,0,"Report ID: NEWEMPPROOFLIST");
$worksheet->write(4,0,"Date Period: ".$date );

####Set up details/data	
	$lastrow=6;
	foreach($arrEmpList as $empValue=>$empVal){
		$user = GetUsername($arrUsers,$empVal['userReleased']);
		$div = getDept($empVal['empDiv'],'','',1,$arrDept);
		$dept = getDept($empVal['empDiv'],$empVal['empDepCode'],'',2,$arrDept);
		$sect = getDept($empVal['empDiv'],$empVal['empDepCode'],$empVal['empSecCode'],3,$arrDept);
		
			$worksheet->write($lastrow,2,"DATE RELEASED: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($today),$detailData);
			$worksheet->write($lastrow,4,"USER: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($user),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"EMPLOYEE NUMBER: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['empNo']),$detailData);
			$worksheet->write($lastrow,4,"NAME: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['empLastName']).', '.strtoupper($empVal['empFirstName']).' '.strtoupper($empVal['empMidName']),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"POSITION: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['posShortDesc']),$detailData);
			$worksheet->write($lastrow,4,"DIVISION: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($div),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"DEPARTMENT: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($dept),$detailData);
			$worksheet->write($lastrow,4,"SECTION: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($sect),$detailData);			
			$lastrow++;
			$worksheet->write($lastrow,2,"GROUP: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['empPayGrp']),$detailData);
			$worksheet->write($lastrow,4,"CATEGORY: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['payCatDesc']),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"TEU: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['teuDesc']),$detailData);			
			$worksheet->write($lastrow,4,"EMPLOYEE STATUS: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['employmentTag']),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"REGULARIZATION DATE: ",$detailLabel);
			$worksheet->write($lastrow,3,valDate($empVal['dateReg']),$detailData);
			$worksheet->write($lastrow,4,"RATE MODE: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['empPayType']),$detailData);			
			$lastrow++;
			$worksheet->write($lastrow,2,"LOCATION: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['brnShortDesc']),$detailData);
			$worksheet->write($lastrow,4,"BRANCH: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['brnShortDesc']),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"DATE HIRED: ",$detailLabel);
			$worksheet->write($lastrow,3,valDate($empVal['dateHired']),$detailData);			
			$worksheet->write($lastrow,4,"RATE: ",$detailLabel);
			$worksheet->write($lastrow,5,($empVal['empPayType']=="Monthly") ? number_format($empVal['empMrate'],2)."/MONTH": number_format($empVal['empDrate'],2)."/DAY",$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"EMPLOYMENT TYPE: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['rankDesc']),$detailData);
			$worksheet->write($lastrow,4,"ADDRESS: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['empAddr1']).', '.strtoupper($empVal['empAddr2']).' '.strtoupper($psObj->empMunicipality($empVal['empMunicipalityCd'])).' '.strtoupper($psObj->empProvince($empVal['empProvinceCd'])),$detailData);			
			$lastrow++;
			$worksheet->write($lastrow,2,"GENDER: ",$detailLabel);
			$worksheet->write($lastrow,3,($empVal['empSex'] == "M")? "MALE":"FEMALE",$detailData);
			$worksheet->write($lastrow,4,"NICK NAME: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['empNickName']),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"CIVIL STATUS: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['empMarStat']),$detailData);			
			$worksheet->write($lastrow,4,"BIRTHDAY: ",$detailLabel);
			$worksheet->write($lastrow,5,valDate($empVal['empBday']),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"AGE: ",$detailLabel);
			$worksheet->write($lastrow,3,date('Y') - date('Y',strtotime($empVal['empBday'])),$detailData);
			$worksheet->write($lastrow,4,"BIRTH PLACE: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['empBplace']),$detailData);			
			$lastrow++;
			$worksheet->write($lastrow,2,"SSS NUMBER: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['empSssNo']),$detailData);
			$worksheet->write($lastrow,4,"PHIC NUMBER: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['empPhicNo']),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"TIN NUMBER: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['empTin']),$detailData);			
			$worksheet->write($lastrow,4,"PAG-IBIG NUMBER: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['empPagibig']),$detailData);
			$lastrow++;
			$worksheet->write($lastrow,2,"BANK NAME: ",$detailLabel);
			$worksheet->write($lastrow,3,strtoupper($empVal['bankDesc']),$detailData);
			$worksheet->write($lastrow,4,"BANK ACCOUNT NUMBER: ",$detailLabel);
			$worksheet->write($lastrow,5,strtoupper($empVal['empAcctNo']),$detailData);			
			$lastrow++;
			$lastrow++;
			
			####Set up to get Employee Allowance
			$sqlAllow = "SELECT tblAllowType.allowDesc, tblAllowance_new.allowAmt, tblAllowance_new.allowPayTag, tblAllowance_new.allowTag FROM tblAllowance_new INNER JOIN tblAllowType ON tblAllowance_new.compCode = tblAllowType.compCode AND tblAllowance_new.allowCode = tblAllowType.allowCode where empNo='{$empVal['empNo']}' and allowStat='A'";
			$arrEmpAllow=$psObj->getArrRes($psObj->execQry($sqlAllow));

			if (count($arrEmpAllow) > 0) {
				$worksheet->write($lastrow,2,"ALLOWANCE TYPE",$detailLabel);	
				$worksheet->write($lastrow,3,"AMOUNT",$detailLabel);	
				$worksheet->write($lastrow,4,"ALLOW. TAG",$detailLabel);	
				$worksheet->write($lastrow,5,"REMARKS",$detailLabel);
				$lastrow++;	
				foreach($arrEmpAllow as $valAllow) {
					$allowRem = ($valAllow['allowPayTag']=='P')?"Permanent":"Temporary";
					$allowTag = ($valAllow['allowTag']=='M')?" MONTHLY":" DAILY";
					$worksheet->write($lastrow,2,strtoupper($valAllow['allowDesc']),$detailData);
					$worksheet->write($lastrow,3,$valAllow['allowAmt'],$detailData);
					$worksheet->write($lastrow,4,$allowTag,$detailData);
					$worksheet->write($lastrow,5,strtoupper($allowRem),$detailData);
					$lastrow++;		
				}
			$lastrow++;	
			$lastrow++;		
			}
			else{
			$lastrow++;			
			}

	}

####Function to get Username
	function GetUsername($arrUsers,$uid) {
		if ($uid != "") {
			foreach($arrUsers as $val) {
				if($val['userId'] == $uid)
					$uname = $val['empLastName'] . ", " . $val['empFirstName'];
			}
			return $uname;
		} else {
			return " N/A";
		}
	}
####Function to get Department,Division,Section
	function getDept($divCode,$deptCode,$sectCode,$level,$arrDept) {
		foreach($arrDept as $valDept) {
			switch($level) {
				case 1:
					if ($valDept['divCode']==$divCode && $valDept['deptLevel']==$level) 
						return $valDept['deptDesc'];
				break;
				case 2:
					if ($valDept['divCode']==$divCode && $valDept['deptCode']==$deptCode && $valDept['deptLevel']==$level) 
						return $valDept['deptDesc'];
				break;
				case 3:
					if ($valDept['divCode']==$divCode && $valDept['deptCode']==$deptCode && $valDept['sectCode']==$sectCode && $valDept['deptLevel']==$level) 
						return $valDept['deptDesc'];
				break;
			}
		}
	}
####Function to format date
	function valDate($date) {
		if ($date=="") {
			$newDate = "";
		} else {
			$newDate = date("m/d/Y",strtotime($date));
		}
		return $newDate;
	}	

####Set up report footer####
$userID=$psObj->getSeesionVars();
$disUser=$psObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
$prntBy="Printed By: ".$disUser['empFirstName']." ".$disUser['empLastName'];
$worksheet->write($lastrow,0,"* * * End of report. Nothing follows. * * *",$headerFormat);
for($j=1;$j<9;$j++){
	$worksheet->write($lastrow,$j,"",$headerFormat);	
}
$worksheet->write($lastrow+1,0,$prntBy);
$workbook->close();
?>