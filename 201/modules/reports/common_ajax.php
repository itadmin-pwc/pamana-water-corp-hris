<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Common Trans, js, obj, ajax instead of useing timesheet 
*/

##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("common_obj.php");

$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');


$inputId = $_GET['inputId'];
$thisValue = $_GET["thisValue"];

$empBrnCode = $_GET['empBrnCode'];
$empDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empPos = $_GET['empPos'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$txtSearch = $_GET["txtSearch"];
$srchType = $_GET["srchType"];
$monthfr =  $_GET["monthfr"];
$monthto =  $_GET["monthto"];
$cmbgroup= $_GET['cmbgroup'];
$listType= $_GET["listType"];

$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];





switch ($inputId) {
	case "empSearch_blacklist":		
		
		$and = "AND ";
		
		if(($_GET["srchType"]!="") && ($_GET["srchType"]!=0))
		{
			$where_clause = 1;
			
			if($srchType==1)
				$where = " $and empNo LIKE '".str_replace("'","''",$txtSearch)."%'";
			elseif($srchType==2)
				$where = " $and empLastName LIKE '".str_replace("'","''",$txtSearch)."%' ";
			elseif($srchType==3)
				$where = " $and empFirstName LIKE '".str_replace("'","''",$txtSearch)."%' ";
			elseif($srchType==4)
				$where = " $and empMidName LIKE '".str_replace("'","''",$txtSearch)."%' ";
			elseif($srchType==5)
				$where = " $and empSssNo LIKE '".str_replace("'","''",$txtSearch)."%' ";
			elseif($srchType==6)
				$where = (date("Y-m-d", strtotime($txtSearch))!="1970-01-01"?" $and dateHired LIKE '".date("Y-m-d", strtotime($txtSearch))."%' ":"");
			elseif($srchType==7)
				$where = (date("Y-m-d", strtotime($txtSearch))!="1970-01-01"?" $and dateResigned LIKE '".date("Y-m-d", strtotime($txtSearch))."%' ":"");
			elseif($srchType==8)
				$where = " $and blacklist_No LIKE '".str_replace("'","''",$txtSearch)."%' ";
		}
		
		
			
			
		if ($empBrnCode!="0") {$empBrnCode1 = " where (empBrnCode = '{$empBrnCode}')"; $where_clause = 1;} else {$empBrnCode1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " $and (empDepCode = '{$empDept}')"; $where_clause = 1;} else {$empDept1 = "";}
		if ($empPos>"" && $empPos>0) {$empPos1 = " $and (empPosId = '{$empPos}')"; $where_clause = 1;} else {$empPos1 = "";}
		if (($monthfr!="") && ($monthto!="")) {$dateEncoded1 = " $and dateEncoded between '".date("Y-m-d", strtotime($monthfr))."' and '".date("Y-m-d", strtotime($monthto))."'"; $where_clause = 1;}
		
		$sqlEmp = "SELECT * FROM tblBlacklistedEmp 
				   $empBrnCode1 $where $empDept1 $empPos1 $dateEncoded1
				   ";	 
			
		$resEmp = $inqTSObj->execQry($sqlEmp);	
		$numEmp = $inqTSObj->getRecCount($resEmp);		    
		
		if ($numEmp == 0) {
				echo "alert('No Employee record found...');";
		} 
		elseif ($numEmp >= 1) {
			echo "location.href = 'blacklist_list_ajax.php?&empBrnCode=$empBrnCode&monthto=$monthto&monthfr=$monthfr&srchType=$srchType&txtSearch=$txtSearch&fileName=$fileName&optionId=$optionId&hide_empDept=$hide_empDept&empPos=$empPos&empDept=$empDept&empDiv=$empDiv'";
	}
		
		
	break;
	
	case "empSearch_ra1":	
		if($cmbgroup==3){
			$payGrp="and empPayGrp='0' or empPayGrp='1' or empPayGrp='2' order by empLastName";
			}
		else{
			$payGrp=" and empPayGrp='".$cmbgroup."'";
			}	
		$sqlEmp = "Select * from tblEmpMast where compCode='".$empBrnCode."' and empStat='RG' and employmentTag IN ('RG','PR','CN') and empPayCat<>'0' and dateHired between '".date("Y-m-d", strtotime($monthfr))."' and '".date("Y-m-d", strtotime($monthto))."'".$payGrp;
		$resEmp = $inqTSObj->execQry($sqlEmp);	
		$numEmp = $inqTSObj->getRecCount($resEmp);		    
		
		if ($numEmp == 0) {
				echo "alert('No Employee record found...');";
		} 
		
		elseif ($numEmp >= 1) {
			if($listType=='r1A')
				echo "location.href = 'r1a_list_ajax.php?&empBrnCode=$empBrnCode&monthto=$monthto&monthfr=$monthfr&cmbgroup=$cmbgroup'";
			else
				echo "location.href = 'er2_list_ajax.php?&empBrnCode=$empBrnCode&monthto=$monthto&monthfr=$monthfr&cmbgroup=$cmbgroup'";
		}
	break;
	
	case "empMinWageList":	
			$sqlEmp = "Select * from    tblEmpMinWageHist where compCode='".$empBrnCode."' and dateUpdated between '".date("Y-m-d", strtotime($monthfr))."' and '".date("Y-m-d", strtotime($monthto))."'";
			$resEmp = $inqTSObj->execQry($sqlEmp);	
			$numEmp = $inqTSObj->getRecCount($resEmp);		    
			
			if ($numEmp == 0) {
					echo "alert('No Employee record found...');";
			} 
			
			elseif ($numEmp >= 1) {
				echo "location.href = 'emplist_minWage_ajax.php?&empBrnCode=$empBrnCode&monthto=$monthto&monthfr=$monthfr'";
			}
			
	break;
	
	
	case "empDiv":
		$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrDept = $inqTSObj->makeArr($inqTSObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','');
		echo $inqTSObj->DropDownMenu($arrDept,'empDept',$hide_empDept,$empDept_dis);
	break;
	case "empDept":
		$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrSect = $inqTSObj->makeArr($inqTSObj->getSectArt($compCode,$empDiv,$empDept),'sectCode','deptDesc','');
		echo $inqTSObj->DropDownMenu($arrSect,'empSect',$hide_empSect,$empSect_dis);
	break;
	
}

?>