<?
##################################################
session_start();
//error_reporting(0);
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("ts_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$compCode 		= $_SESSION['company_code'];
$inputId 		= $_GET['inputId'];
$empNo 			= $_GET['empNo'];
$empDiv			= $_GET['empDiv'];
$empSect 		= $_GET['empSect'];
$empDept 		= $_GET['empDept'];
$empName 		= $_GET['empName'];
$hide_empDept 	= $_GET['hide_empDept'];
$hide_empSect 	= $_GET['hide_empSect'];
$optionId 		= $_GET['optionId'];
$fileName 		= $_GET['fileName'];
$orderBy 		= $_GET['orderBy'];
$thisValue 		= $_GET['thisValue'];
$pafType 		= $_GET['pafType'];
$from 			= date('Y-m-d', strtotime($_GET['from']));
$to 			= date('Y-m-d', strtotime($_GET['to']));
$code 			= $_GET['code'];
$status 		= $_GET['status'];
$statorg 		= $_GET['status'];
$type			= $_GET['type'];
$form			= $_GET['form'];
$group			= $_GET['group'];
if ($thisValue=="new_emp") {
	$tbl_new = "_new";
	if ($status == 'R') {
		if ($from != "" && $to!= "") {
			$filter_from_to = " AND (dateReleased between '$from' AND '$to') ";
		}
	} else {
		if ($from != "" && $to!= "") {
			$filter_from_to = " AND (empdateadded between '$from' AND '$to') ";
		}
	}	
}
if ($thisValue == 'EmpStatus') {
	if ($from != "" && $to!= "") {
		switch($statorg) {
			case "RG":
				$empStatDatefilter = " AND dateReg between '$from' AND '$to'";
			break;
			case "PR":
				$empStatDatefilter = " AND dateResigned between '$from' AND '$to'";
			break;
			case "CR":
				$empStatDatefilter = " AND dateResigned between '$from' AND '$to'";
			break;
			case "RS":
				$empStatDatefilter = " AND dateResigned between '$from' AND '$to'";
			break;
			case "TR":
				$empStatDatefilter = " AND dateResigned between '$from' AND '$to'";
			break;
		}
	}	
}
switch ($inputId) {
	case "empSearch":	
		##################################################
		if ($empNo>"") {
			$empNo1 = " AND (empNo LIKE '{$empNo}%')";
		} else {
			$empNo1 = "";
			if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
		}
		if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
		if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
		if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
		if ($orderBy==1) {$orderBy1 = " ORDER BY empLastName, empFirstName, empMidName ";} 
		if ($orderBy==2) {$orderBy1 = " ORDER BY empNo ";} 
		if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
		if ($orderBy==3) {$orderBy1 = " ORDER BY empDiv, empDepCode, empSecCode ";}
		if ($status != "0" && $status != "") {
			$status = " AND empStat='$status' ";
		} else {
			$status="";
		}
		$sqlEmp = "SELECT * FROM tblEmpMast$tbl_new  WHERE (compCode = '{$compCode}') 
					and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
				  $filter_from_to $empNo1 $status $empStatDatefilter $empName1 $empDiv1 $empDept1 $empSect1 $groupType1 $catType1
				   $orderBy1 ";	
		$resEmp = $inqTSObj->execQry($sqlEmp);		   
		$numEmp = $inqTSObj->getRecCount($resEmp);
		
		if ($thisValue=="verifyEmp") {
			if ($numEmp == 0) {
				echo "alert('No Employee record found...');";
			} elseif ($numEmp == 1) {
				echo "location.href = '$fileName?hide_option=new_&empNo=$empNo&cmbType=$pafType';";
			} elseif ($numEmp > 1) {
				echo "location.href = 'main_emp_list.php?fileName=$fileName&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&from=$from&to=$to';";
			}
				
		}
	 
	break;
	case "eventReport":
		$arrRD = $inqTSObj->evenReport($_GET['empNo'],$_GET['from'],$_GET['to'],$_GET['branch'],$_GET['bio'],$_GET['group'],$_GET['div'],$_GET['dept']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('event_pdf.php?branch={$_GET['branch']}&from={$_GET['from']}&to={$_GET['to']}&empNo={$_GET['empNo']}&bio={$_GET['bio']}&group={$_GET['group']}&div={$_GET['div']}&dept={$_GET['dept']}');";
		}
	break;
	case "TSProoflist":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		if($_GET['group']!=""){
			$group = $_GET['group'];
		}
		else{
			$grp = $inqTSObj->getBrnchInfo($_GET['branch']);
			$group = $grp['brnDefGrp'];	
		}
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->TSProofList($_GET['empNo'],$_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$group,$_GET['cat']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('tsprooflist_pdf.php?branch={$_GET['branch']}&empNo={$_GET['empNo']}&group={$group}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&cat={$_GET['cat']}&hist=$hist');";		
		}
	break;
	case "TSProofListReport":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->TSProofListReport($_GET['empNo'],$_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group'],$_GET['div'],$_GET['dept'],$_GET['cat']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
		$div = $_GET['div'];
		$dept = $_GET['dept'];	
		//$div = (trim($div)!='' && $div!='0') ? ",$div":"";
		//$dept = (trim($dept)!='' && $dept!='0') ? ",$dept":"";
			echo "window.open('tsprooflist_pdf.php?branch={$_GET['branch']}&empNo={$_GET['empNo']}&group={$_GET['group']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&cat={$_GET['cat']}&hist=$hist&divcode=$div&deptcode=$dept');";		
		}
	break;

	case "OB":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->OB($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);

		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('ob_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;	
	case "OverBreak":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		
		if($_GET['breakHr']=="1"){
			$arrRD = $inqTSObj->OverBreaks($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
	
			if (count($arrRD) == 0) {
				echo "alert('No Record found.')";
			} else {
				echo "window.open('overbreak_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
			}
		}
		else{
			$arrRD = $inqTSObj->OverBreaks1($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
	
			if (count($arrRD) == 0) {
				echo "alert('No Record found.')";
			} else {
				echo "window.open('overbreak1_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
			}
		}
	break;		
		
	case "OT":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->OT($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('ot_pdf.php?branch={$_GET['branch']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;	
	case "TS_Adjustment":
		//$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->TS_Adjustment($_GET['frm'],$_GET['to'],$_GET['group'],$_GET['id']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('ts_adjustment_pdf.php?frm={$_GET['frm']}&to={$_GET['to']}&group={$_GET['group']}&id={$_GET['id']}');";
		}
	break;		
	case "TS_Adjustment_with_Amount":
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->TS_Adjustment_with_Amount($_GET['frm'],$_GET['to'],$_GET['group'],$_GET['id']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('ts_adjustment_with_Amount_pdf.php?frm={$_GET['frm']}&to={$_GET['to']}&group={$_GET['group']}&id={$_GET['id']}');";
		}
	break;		
	case "Earnings_Adjustment":
		//$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->Earnings_Adjustment($_GET['frm'],$_GET['to'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('earnings_adjustment_pdf.php?frm={$_GET['frm']}&to={$_GET['to']}&group={$_GET['group']}');";
		}
	break;		
	case "CS":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->CS($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('cs_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;
	case "TS_Corrections":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->TS_Corrections($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} 
		else {
			echo "window.open('tscorrections_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;		
	case "OT_Prooflist":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->OT_Prooflist($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('ot_prooflist_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;			
	case "Deductions":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->Deductions($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('deductions_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;	
	case "Leaves":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->Leaves($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('leaves_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;	
	case "legalPay":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->legalPay($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('legalPay_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;				
	case "offSetHour":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->offSetHour($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('offSetSchedule_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;				
	case "RestDay":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$arrRD = $inqTSObj->RestDay($_GET['branch'],$hist,$arr['pdFrmDate'],$arr['pdToDate'],$_GET['group']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('restday_pdf.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;				
	case "payPd":
		$arrPd = $inqTSObj->getpayPd($_GET['branch'],$_GET['grp']);
		$arrPd = $inqTSObj->makeArr($arrPd,'pdSeries','pdPayable','');
								$inqTSObj->DropDownMenu($arrPd,'cmbpayPd',$empDiv,'"');
		
		
	exit();
	break;
	
	case "openPayPeriod";
		$arrPd = $inqTSObj->getOpenPayPd($_GET['grp']);
		$arrPd = $inqTSObj->makeArr($arrPd,'pdSeries','pdPayable','');
								$inqTSObj->DropDownMenu($arrPd,'cmbpayPd','','"');
	exit();
	break;

	case "department";
		$arrDept = $inqTSObj->getDeptArt($_SESSION['company_code'],$_GET['dpt']);
		$dept = $inqTSObj->makeArr($arrDept,'deptCode','deptDesc','');
								$inqTSObj->DropDownMenu($dept,'cmbDepartment','','"');
	exit();
	break;
	
	case "violationsReport":
		$arrRD = $inqTSObj->violationsReport($_GET['empNo'],$_GET['from'],$_GET['to'],$_GET['branch'],$_GET['bio'],$_GET['violations']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			if($_GET['violations']==0){
				echo "window.open('violations_pdf.php?branch={$_GET['branch']}&from={$_GET['from']}&to={$_GET['to']}&empNo={$_GET['empNo']}&bio={$_GET['bio']}&violations={$_GET['violations']}');";
			}
			else{
				$arrValHeader =  $inqTSObj->violationHeader($_GET['violations']);
				echo"window.open('violations_pdf1.php?branch={$_GET['branch']}&from={$_GET['from']}&to={$_GET['to']}&empNo={$_GET['empNo']}&bio={$_GET['bio']}&violations={$_GET['violations']}&arrValHeader={$arrValHeader}');";	
			}
		}
	break;	

	case "TSSummary":
		$arr = $inqTSObj->getGrpOpenPeriod($_GET['payPd']);
		$hist =  ($arr['pdStat'] !='O') ? 'hist':'';
		$cutfrom=$arr['pdFrmDate'];
		$cutto=$arr['pdToDate'];
		$arrRD = $inqTSObj->TS_Summary($cutfrom,$cutto,$hist);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('tssummary_total.php?branch={$_GET['branch']}&payPd={$_GET['payPd']}&from={$arr['pdFrmDate']}&to={$arr['pdToDate']}&hist=$hist&group={$_GET['group']}');";
		}
	break;	
}

?>