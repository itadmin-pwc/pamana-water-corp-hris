<?
##################################################
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
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
$from 			= $_GET['from'];
$to 			= $_GET['to'];
$code 			= $_GET['code'];
$status 		= $_GET['status'];
$statorg 		= $_GET['status'];
$type			= $_GET['type'];
$form			= $_GET['form'];
$group			= $_GET['group'];
$salary			= $_GET['salary'];
$school			= $_GET['school'];
$course			= $_GET['course'];
$signatory		= $_GET['signatory'];
$position		= $_GET['position'];
if ($thisValue=="new_emp" || $thisValue=="new_emp_excel") {
	$tbl_new = "_new";
	if ($status == 'R') {
		if ($from != "" && $to!= "") {
			$filter_from_to = " AND (dateReleased between '".date('Y-m-d',strtotime($from))."' AND '".date('Y-m-d',strtotime($to))."') ";
		}
	} else {
		if ($from != "" && $to!= "") {
			$filter_from_to = " AND (empdateadded between '".date('Y-m-d',strtotime($from))."' AND '".date('Y-m-d',strtotime($to))."') ";
		}
	}	
	if ($group!='' && $group!='0') {
		$filter_from_to .= " AND empPayGrp = '{$_GET['group']}' ";
	}	
}
if ($thisValue == 'EmpStatus') {
	if ($from != "" && $to!= "") {
		switch($statorg) {
			case "RG":
				$empStatDatefilter = " AND dateReg between '".date('Y-m-d',strtotime($from))."' AND '".date('Y-m-d',strtotime($to))."' ";
			break;
			case "PR":
				$empStatDatefilter = " AND empdateadded between '".date('Y-m-d',strtotime($from))."' AND '".date('Y-m-d',strtotime($to))."' ";
			break;
			case "CN":
				$empStatDatefilter = " AND empdateadded between '".date('Y-m-d',strtotime($from))."' AND '".date('Y-m-d',strtotime($to))."' ";
			break;
			case "RS":
				$empStatDatefilter = " AND dateResigned between '".date('Y-m-d',strtotime($from))."' AND '".date('Y-m-d',strtotime($to))."' ";
			break;
			case "TR":
				$empStatDatefilter = " AND empdateadded between '".date('Y-m-d',strtotime($from))."' AND '".date('Y-m-d',strtotime($to))."' ";
			break;
		}
	}	
}

if($_GET['action']=="filterbranches"){
	//echo "location.href = 'inq_emp.php?hide_option=new_&qryBranch='{$_GET['qryBranch']}";
	//header("Location: inq_emp.php");
	//echo "alert('nhomer');";
	echo "location.href = 'inq_emp.php?hide_option=new_&qryBranch={$_GET['qryBranch']}&action=filterbranches';";
	//exit();
	}

switch ($inputId) {
	case "empSearch":	
		##################################################
		if ($empNo != "") {
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
			//$status = " AND employmentTag='$status' ";
			if($status=="RG" || $status=="PR" || $status=="CN"){
				$status = " AND employmentTag='$status'";
			}
			else{
				$status = " AND empStat='$status'";
			}
			
		} else {
			$status="";
		}

		$confaccess=$_SESSION['Confiaccess'];
		if($confaccess == 'N' || empty($confaccess)){
			$confi = "and tblEmpMast.empPayCat ='3'";
		}elseif ($confaccess == 'Y') {
			$confi = "and tblEmpMast.empPayCat ='2'";
		}

		$sqlEmp = "SELECT * FROM tblEmpMast$tbl_new  WHERE (compCode = '{$compCode}') 
					and empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
				  $filter_from_to $empNo1 $status $empStatDatefilter $empName1 $empDiv1 $empDept1 $empSect1 $confi $groupType1 $catType1
				   $orderBy1 ";
		//echo $sqlEmp;
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
		if ($thisValue=="paf") { ### Employee Movement
		
			if ($numEmp>0) {
					echo "window.open('paf_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&from=$from&to=$to&pafType=$pafType&group=$group');";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		} 
		if ($thisValue=="paf_prooflist") { ### PAF Proof List
		
			if ($numEmp>0) {
					//echo "location.href = 'paf_proof_list_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&pafType=$pafType&from=$from&to=$to&type=$type';";
					echo "window.open('paf_prooflist_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&from=$from&to=$to&pafType=$pafType');";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		} 
		if ($thisValue=="released_paf") { ### Released PAF
		
			if ($numEmp>0) {
					//echo "location.href = 'paf_proof_list_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&pafType=$pafType&from=$from&to=$to&type=$type';";
					echo "window.open('released_paf_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&from=$from&to=$to&pafType=$pafType');";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		} 
		if ($thisValue=="held_paf_prooflist_excel") { ### HELD PAF
		
			if ($numEmp>0) {
					//echo "location.href = 'paf_proof_list_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&pafType=$pafType&from=$from&to=$to&type=$type';";
					echo "window.open('held_paf_excel.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&from=$from&to=$to&pafType=$pafType');";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		} 
		
		if ($thisValue=="released_paf_excel") { ### Released PAF
		
			if ($numEmp>0) {
					//echo "location.href = 'paf_proof_list_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&pafType=$pafType&from=$from&to=$to&type=$type';";
					echo "window.open('released_paf_excel.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&from=$from&to=$to&pafType=$pafType');";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		} 
				
		if ($thisValue=="paf_excel") { ### Released PAF Excel
		
			if ($numEmp>0) {
					//echo "location.href = 'paf_proof_list_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&pafType=$pafType&from=$from&to=$to&type=$type';";
					echo "window.open('posted_paf_excel.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&from=$from&to=$to&pafType=$pafType&group=$group');";
					
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		} 


		if ($thisValue=="salary") { ### Promotions,CBA,MERIT Report
		
			if ($numEmp>0) {
					echo "location.href = 'salary_list.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&code=$code&from=$from&to=$to&type=$type';";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		}
		
		if ($thisValue=="new_emp") { ### new employee proof list
		
			if ($numEmp>0) {
					echo "window.open('new_emp_proof_list_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&code=$code&from=$from&to=$to&status=$type&group=$group');";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		}		

		if($thisValue=="new_emp_excel"){### new employee proof list excel format
			if($numEmp>0){
					echo "window.open('new_emp_proof_list_excel.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&orderBy=$orderBy&code=$code&from=$from&to=$to&status=$type&group=$group');";
				}
			else{
				echo "alert('No Employee record found...');";
				}	
			}
		if ($thisValue=="COE") { ### COE
		
			if ($numEmp>0) {
				$coeType = $_GET['type'];
				echo "window.open('coe_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&type=$coeType&salary=$salary&course=$course&school=$school&signatory=$signatory&position=$position');";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		}		 

		if ($thisValue=="CS") { ### CS
		
			if ($numEmp>0) {
				$csType = $_GET['type'];
					if($csType==1){
						echo "window.open('clearance_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName');";
					}
					else{
						echo "window.open('survey_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName');";
					}
			} 
			else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		}		 

		if ($thisValue=="EmpStatus") { ### Employee Status
		
			if ($numEmp>0) {
					echo "window.open('empStat_pdf.php?inputId=$optionId&empNo=$empNo&empName=$empName&empDiv=$empDiv&empDept=$empDept&empSect=$empSect&status=$statorg&from=$from&to=$to');";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		}		 
		if ($thisValue=="EmpTenure") { ### Employee Tenure
			if ($numEmp>0) {
				if ($form == "Pdf")
					echo "window.open('emptenure_pdf.php?inputId=$optionId&empDiv=$empDiv&empDept=$empDept');";
				else
					echo "location.href='../chart/tenure.php?inputId=$optionId&empDiv=$empDiv&empDept=$empDept';";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
		}		 
		if ($thisValue=="HeadCount") { ### Employee HeadCount
			if ($numEmp>0) {
				if ($form == "Pdf")
					echo "window.open('empHeadCount_pdf.php?inputId=$optionId&empDiv=$empDiv&empDept=$empDept');";
				else
					echo "location.href='../chart/headcount.php?inputId=$optionId&empDiv=$empDiv&empDept=$empDept';";
				//echo "document.frmTS.submit();";
			} else { //////open employee list
				echo "alert('No Employee record found...');";
			}	
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
	case "pdType":
		$payPd_dis = "class=\"inputs\" onKeyPress=\"getEmpSearch(event,'empSearch');\"";
		$arrPayPd = $inqTSObj->makeArr($inqTSObj->getAllPeriod($compCode,$groupType,$catType,"0"),'pdSeries','pdPayable','');
		echo $inqTSObj->DropDownMenu($arrPayPd,'payPd',$hide_payPd,$payPd_dis);
	break;
	case "restday":
		$arrRD = $inqTSObj->restDay($_GET['branch']);
		if (count($arrRD) == 0) {
			echo "alert('No Record found.')";
		} else {
			echo "window.open('restday_pdf.php?branch={$_GET['branch']}&group={$_GET['group']}');";
		}
	break;
}

?>