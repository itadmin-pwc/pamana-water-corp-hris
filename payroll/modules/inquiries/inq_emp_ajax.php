<?
##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_obj.php");
$maintEmpObj = new inqEmpObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$inputId = $_GET['inputId'];
$empNo = $_GET['empNo'];
$empDiv = $_GET['empDiv'];
$empSect = $_GET['empSect'];
$empDept = $_GET['empDept'];
$empName = $_GET['empName'];
$hide_empDept = $_GET['hide_empDept'];
$hide_empSect = $_GET['hide_empSect'];
$optionId = $_GET['optionId'];
$fileName = $_GET['fileName'];
$orderBy = $_GET['orderBy'];
$groupType = $_GET['groupType'];
$catType = $_GET['catType'];

switch ($inputId) {
	case "empSearch":		
		##################################################
		if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
		if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($groupType<3) {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
		if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
		if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
		if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
		if ($groupType<3 && $groupType!="") {$groupType1 = " AND (empPayGrp = '{$groupType}')";} else {$groupType1 = "";}
		if ($catType>0) {$catType1 = " AND (empPayCat = '{$catType}')";} else {$catType1 = "";}
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   $empNo1 $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1
				   AND empStat NOT IN('RS','IN','TR') 
				   $orderBy1 ";	   
		$resEmp = $maintEmpObj->execQry($sqlEmp);		   
		$numEmp = $maintEmpObj->getRecCount($resEmp);
		
		if ($numEmp>0 && $numEmp<2) {
			$dispEmp = $maintEmpObj->getSqlAssoc($resEmp);
			$dispEmpNo = $dispEmp['empNo'];
			echo "document.getElementById('empNo').disabled=false;";
			echo "document.getElementById('empNo').value='$dispEmpNo';";
			echo "document.frmEmp.submit();";
		} else { //////open employee list
			echo "document.getElementById('updateFlag').value='1';";
			if ($numEmp>0) {
				echo "document.frmEmp.action = 'inq_emp_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType';";
			} else {
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No record/s found...');";
			}
			echo "document.frmEmp.submit();";
		}	
	break;
	case "empDiv":
		$empDept_dis = "class=\"inputs\" onChange=\"getEmpSect(this.id);\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrDept = $maintEmpObj->makeArr($maintEmpObj->getDeptArt($compCode,$empDiv),'deptCode','deptDesc','');
		echo $maintEmpObj->DropDownMenu($arrDept,'empDept',$hide_empDept,$empDept_dis);
	break;
	case "empDept":
		$empSect_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrSect = $maintEmpObj->makeArr($maintEmpObj->getSectArt($compCode,$empDiv,$empDept),'sectCode','deptDesc','');
		echo $maintEmpObj->DropDownMenu($arrSect,'empSect',$hide_empSect,$empSect_dis);
	break;
	case "empHistSearch":
		$qryPrev = 	"SELECT * FROM tblPrevEmployer 
			     WHERE compCode = '{$compCode}'
			     AND prevStat = 'A' 
				 AND empNo = '{$empNo}' ";
				
		$resPrev = $maintEmpObj->execQry($qryPrev);		   
		$numPrev = $maintEmpObj->getRecCount($resPrev);
		$numPrev = $numPrev." found...";
		echo "document.getElementById('imgPrevEmp').title='$numPrev';";
	break;
	case "empUpload":		
		##################################################
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   AND empNo = '$empNo'
				   AND empStat NOT IN('RS','IN','TR') ";	   
		$resEmp = $maintEmpObj->execQry($sqlEmp);		   
		$numEmp = $maintEmpObj->getRecCount($resEmp);
		
		if ($numEmp>0 && $numEmp<2) { //only one record found
			$dispEmp = $maintEmpObj->getSqlAssoc($resEmp);
			$dispEmpNo = $dispEmp['empNo'];
			echo "document.getElementById('empNo').disabled=false;";
			echo "document.getElementById('empNo').value='$dispEmpNo';";
			echo "document.getElementById('hideUpload').value='upload';";
			echo "document.frmEmp.submit();";
		} else { //////open employee list
			echo "document.getElementById('updateFlag').value='1';";
			if ($numEmp>0) {
				echo "document.frmEmp.action = 'inq_emp_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType';";
			} else {
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No record/s found...');";
			}
			echo "document.frmEmp.submit();";
		}	
	break;
	case "viewCam":		
		##################################################
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   AND empNo = '$empNo'
				   AND empStat NOT IN('RS','IN','TR') ";	   
		$resEmp = $maintEmpObj->execQry($sqlEmp);		   
		$numEmp = $maintEmpObj->getRecCount($resEmp);
		
		if ($numEmp>0 && $numEmp<2) { //only one record found
			$dispEmp = $maintEmpObj->getSqlAssoc($resEmp);
			$dispEmpNo = $dispEmp['empNo'];
			echo "document.getElementById('empNo').disabled=false;";
			echo "document.getElementById('empNo').value='$dispEmpNo';";
			echo "document.getElementById('hideUpload').value='viewCam';";
			echo "document.frmEmp.submit();";
		} else { //////open employee list
			echo "document.getElementById('updateFlag').value='1';";
			if ($numEmp>0) {
				echo "document.frmEmp.action = 'inq_emp_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType';";
			} else {
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No record/s found...');";
			}
			echo "document.frmEmp.submit();";
		}	
	break;
	case "refresh":		
		##################################################
		$sqlEmp = "SELECT * FROM tblEmpMast 
				   WHERE (compCode = '{$compCode}') 
				   AND empNo = '$empNo'
				   AND empStat NOT IN('RS','IN','TR') ";	   
		$resEmp = $maintEmpObj->execQry($sqlEmp);		   
		$numEmp = $maintEmpObj->getRecCount($resEmp);
		
		if ($numEmp>0 && $numEmp<2) { //only one record found
			$dispEmp = $maintEmpObj->getSqlAssoc($resEmp);
			$dispEmpNo = $dispEmp['empNo'];
			echo "document.getElementById('empNo').disabled=false;";
			echo "document.getElementById('empNo').value='$dispEmpNo';";
			echo "document.getElementById('hideUpload').value='refresh';";
			echo "document.frmEmp.submit();";
		} else { //////open employee list
			echo "document.getElementById('updateFlag').value='1';";
			if ($numEmp>0) {
				echo "document.frmEmp.action = 'inq_emp_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&groupType=$groupType&orderBy=$orderBy&catType=$catType';";
			} else {
				echo "document.getElementById('empNo').disabled=false;";
				echo "document.getElementById('empNo').value='';";
				echo "alert('No record/s found...');";
			}
			echo "document.frmEmp.submit();";
		}	
	break;
}

?>