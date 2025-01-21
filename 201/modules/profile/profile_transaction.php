<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("profile_paf_obj.php");
$_SESSION['oldcompCode'] = $_GET['compCode'];
$_SESSION['strprofile'] = $_GET['empNo'];
$commonEmpObj=new commonObj();
$maintEmpObj = new pafObj($_GET,$_SESSION);
$empNo= $_GET['empNo'];
$compCode = $_GET['compCode'];
$empother_info = $maintEmpObj->empOtherInfos($empNo,$compCode);
$empProf =  $maintEmpObj->getEmployee($compCode,$empNo,'');
$empBioNo =  $maintEmpObj->getBio($empNo,$compCode);
$division_desc = $maintEmpObj->getDivDescArt($compCode, $empProf['empDiv']);
$department_desc = $maintEmpObj->getDeptDescArt($compCode, $empProf['empDiv'],$empProf['empDepCode']);
$section_desc =  $maintEmpObj->getSectDescArt($compCode, $empProf['empDiv'],$empProf['empDepCode'],$empProf['empSecCode']);
$rank_desc = $maintEmpObj->getRank($empProf['empRank']);
$level_desc = "Level " . $empProf['empLevel'];

$userbranch = $maintEmpObj->getEmployee($_SESSION['company_code'],$_SESSION['employee_number'],"");
// if ((($_SESSION['user_level'] == 1)||($_SESSION['user_level'] == 2)&&($userbranch['empBrnCode']=="999"))) 
// if ($_SESSION['Confiaccess'] == "Y" && ($userbranch['empBrnCode']=="999" || $userbranch['empBrnCode']=="0001"))
if ((($_SESSION['user_level'] == 1)||($_SESSION['user_level'] == 2)&&($userbranch['empBrnCode']=="999"))) 
{
	$vis = "";
}
else{
	$vis = "hidden";
}
switch ($_GET['code']) {
	case "round_number":
			$Mrate = number_format(str_replace(",","",$_GET['txtsalary']),2);
			$Drate = number_format(str_replace(",","",$_GET['txtdailyrate']),2);
			$Hrate = number_format(str_replace(",","",$_GET['txthourlyrate']),2);
			echo "$('txtsalary').value='$Mrate';";
			echo "$('txtdailyrate').value='$Drate';";
			echo "$('txthourlyrate').value='$Hrate';";
	exit();
	break;
	case "getposid":
			$pos = $maintEmpObj->getpositionwil("where compCode='$compCode' and posCode='{$_GET['cmbposition']}'",2);
			$division = $maintEmpObj->getDivDescArt($compCode, $pos['divCode']);
			$department = $maintEmpObj->getDeptDescArt($compCode, $pos['divCode'],$pos['deptCode']);
			$section =  $maintEmpObj->getSectDescArt($compCode, $pos['divCode'],$pos['deptCode'],$pos['sectCode']);
			$rank = $maintEmpObj->getRank($pos['rank']);
			$level = "Level " . $pos['level'];
			echo "$('new_divCode').value = {$pos['divCode']};";
			echo "$('new_deptCode').value = {$pos['deptCode']};";
			echo "$('new_secCode').value = {$pos['sectCode']};";
			echo "$('new_cat').value = {$pos['rank']};";
			echo "$('new_level').value = {$pos['level']};";
			echo "$('dvdiv').innerHTML = '{$division['deptDesc']}';";
			echo "$('dvdept').innerHTML = '{$department['deptDesc']}';";
			echo "$('dvsec').innerHTML = '{$section['deptDesc']}';";
			echo "$('dvrank').innerHTML = '{$rank['rankDesc']}';";
			echo "$('dvlevel').innerHTML = '{$level}';";
	exit();
	break;
	case "ecola":
		if ($maintEmpObj->checkECOLA($_GET['drate'],$_GET['empNo'],$_GET['compCode'])) {
			echo "$('txtecola').value = 1";
		} else  {
			echo "$('txtecola').value = 0";
		}
	exit();
	break;
	case "empstat":
		$checkempstat=$maintEmpObj->recordChecker("SELECT * FROM tblPAF_EmpStatus where stat='H' and compCode='{$compCode}' and empNo='{$empNo}'  and refNo!='{$_GET['refno']}'");
		if($checkempstat){
				echo "alert('PAF transaction already exist. Kindly edit the existing PAF transaction.');";
				echo "	location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
		}
		else{
			if ($maintEmpObj->empStatus()) {
				if ($_GET['empstattag'] != "1") {
					echo "alert('PAF Employment Status update queued');";
				} else {
					echo "alert('PAF Employment Status update processed');";
				}
				echo "location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
			} else {
					echo "alert('PAF Employment Status update failed');";
			}
		}
	exit();			
	break;
	case "payroll":
		$checkpayroll=$maintEmpObj->recordChecker("SELECT * FROM tblPAF_PayrollRelated where stat='H' and compCode='{$compCode}' and empNo='{$empNo}' and refNo!='{$_GET['refno']}'");
		if($checkpayroll){
				echo "alert('PAF transaction already exist. Kindly edit the existing PAF transaction.');";
				echo "	location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
		}
		else{
			if ($maintEmpObj->payroll()) {
				if ($_GET['prtag'] != "1") {
					if($_GET['taxstat']=="O"){
						echo "alert('PAF Employee Payroll Related update queued.');";
						echo "location.href='profile_actionlist.php?empNo=$empNo&compCode=$compCode';";	
					exit();	
					}
					else if($_GET['taxstat']=="P" && $_GET['transtype']!="E" && $_GET['oldteu']=="S" && $_GET['cmbexemption']!="S"){
						echo " var a = confirm('PAF Employee Payroll Related queued. Would you like to update the Civil Status and Lastname of the employee?');
						if(a==false){
							location.href='profile_actionlist.php?empNo=$empNo&compCode=$compCode';	
						}
						else{
							location.href = 'profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=P&act=others';
						}";
					}
					else if($_GET['taxstat']=="P" && $_GET['transtype']!="E" && $_GET['oldteu']!="S" && $_GET['cmbexemption']=="S"){
						echo " var a = confirm('PAF Employee Payroll Related queued. Would you like to update the Civil Status and Lastname of the employee?');
						if(a==false){
							location.href='profile_actionlist.php?empNo=$empNo&compCode=$compCode';	
						}
						else{
							location.href = 'profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=P&act=others';
						}";
					}
					else{
						echo "alert('PAF Employee Payroll Related update queued.');";
						echo "location.href='profile_actionlist.php?empNo=$empNo&compCode=$compCode';	";
					}
					exit();
				} 
				else {
					if($_GET['taxstat']=="O"){
						echo "alert('PAF Employee Payroll Related update queued.');";
						echo "location.href='profile_actionlist.php?empNo=$empNo&compCode=$compCode';";	
					exit();	
					}
					else if($_GET['taxstat']=="P" && $_GET['transtype']!="E" && $_GET['oldteu']=="S" && $_GET['cmbexemption']!="S"){
						echo " var a = confirm('PAF Employee Payroll Related queued. Would you like to update the Civil Status and Lastname of the employee?');
						if(a==false){
							location.href='profile_actionlist.php?empNo=$empNo&compCode=$compCode';	
						}
						else{
							location.href = 'profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=P&act=others';
						}";
					}
					else if($_GET['taxstat']=="P" && $_GET['transtype']!="E" && $_GET['oldteu']!="S" && $_GET['cmbexemption']=="S"){
						echo " var a = confirm('PAF Employee Payroll Related queued. Would you like to update the Civil Status and Lastname of the employee?');
						if(a==false){
							location.href='profile_actionlist.php?empNo=$empNo&compCode=$compCode';	
						}
						else{
							location.href = 'profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=P&act=others';
						}";
					}				
					else{
						echo "alert('PAF Employee Payroll Related update queued.');";
						echo "location.href='profile_actionlist.php?empNo=$empNo&compCode=$compCode';	";
					}
					exit();
				}
				if ($_GET['txtecola'] == 1) {
					echo "var checola = confirm('Daily Rate is below the min. wage, Do you want to apply ECOLA allowance?');";
					echo "if (checola==true) {";
					echo "viewDetails('employee_allowance.php?empNo='+empInputs['empNo'],'Add','','contact_list_ajax.php','TSCont',0,0,'txtSrch','cmbSrch');";
					echo "} else {";
					echo "	location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
					echo "}";
				} 
			} else {
				echo "alert('PAF Employee Payroll Related update failed');";
			}	
		}
	exit();			
	break;
	case "others":
		//Check if Bio Number Exist
		$arrCheckBio = $maintEmpObj->checkBioEmpExist(" and bioNumber='".$_GET["txtBioNum"]."' and locCode='".$arrCheckBio ["locCode"]."'");
		if($arrCheckBio["empNo"]!="")
		{
			$arrGetEmpBioBranch = $maintEmpObj->getEmpBranchArt($_SESSION["company_code"],$arrCheckBio ["locCode"]);
			echo "alert('Bio - Number encoded is being used by Employee : ".$arrCheckBio ["empNo"]." of Branch : ".$arrGetEmpBioBranch["brnDesc"].".');";
		}
		else
		{	
			$checkOthers=$maintEmpObj->recordChecker("SELECT * FROM tblPAF_Others where stat='H' and compCode='{$compCode}' and empNo='{$empNo}' and refNo!='{$_GET['refno']}'");
			if($checkOthers){
					echo "alert('PAF transaction already exist. Kindly edit the existing PAF transaction.');";
					echo "	location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
			}
			else{
				if ($maintEmpObj->others()) {
					if ($_GET['othtag'] != "1") {
						if($_GET['taxstat']=="O" && $_GET['taxstat']!="" && $_GET['transtype']!="E"){
							echo "alert('PAF Employee Other Infos update processed. You are required to change TAX Exemption.');";
							echo "location.href='profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=O&act=payroll'";
						}
						elseif($_GET['taxstat']=="" && $_GET['cmbstatus']!="0" && $_GET['cmbstatus']!="SG" && $_GET['old_txtCStat']=="SG" && $_GET['txtlname']=="" && $_GET['transtype']!="E"){
							echo "alert('PAF Employee Other Infos update processed. You are required to change TAX Exemption.');";
							echo "location.href='profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=O&act=payroll&val=val'";
						}
						elseif($_GET['taxstat']=="" && $_GET['cmbstatus']=="" || $_GET['cmbstatus']=="0" && $_GET['txtlname']!="" && $_GET['txtlname']!= $_GET['old_txtlname'] && $_GET['transtype']!="E"){
							echo "alert('PAF Employee Other Infos update processed.');";
							echo "location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";				
						}
						elseif($_GET['taxstat']=="" && $_GET['cmbstatus']!="0" && $_GET['cmbstatus']!="SG" && $_GET['old_txtCStat']=="SG" && $_GET['txtlname']!= $_GET['old_txtlname'] && $_GET['transtype']!="E"){
							echo "alert('PAF Employee Other Infos update processed. You are required to change TAX Exemption.');";
							echo "location.href='profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=O&act=payroll&val=val'";
						}					
						else{
							echo "alert('PAF Employee Other Infos update processed.');";
							echo "location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";				
						}	
					} 
					else {
						if($_GET['taxstat']=="O" && $_GET['taxstat']!="" && $_GET['transtype']!="E"){
							echo "alert('PAF Employee Other Infos update processed. You are required to change TAX Exemption.');";
							echo "location.href='profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=O&act=payroll'";
						}
						elseif($_GET['taxstat']=="" && $_GET['cmbstatus']!="SG" && $_GET['old_txtCStat']=="SG" && $_GET['txtlname']=="" && $_GET['transtype']!="E"){
							echo "alert('PAF Employee Other Infos update processed. You are required to change TAX Exemption.');";
							echo "location.href='profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=O&act=payroll&val=val'";
						}
						elseif($_GET['taxstat']=="" && $_GET['cmbstatus']=="" || $_GET['cmbstatus']=="0" && $_GET['txtlname']!="" && $_GET['txtlname']!= $_GET['old_txtlname'] && $_GET['transtype']!="E"){
							echo "alert('PAF Employee Other Infos update processed.');";
							echo "location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";				
						}
						elseif($_GET['taxstat']=="" && $_GET['cmbstatus']!="SG" && $_GET['old_txtCStat']=="SG" && $_GET['txtlname']!= $_GET['old_txtlname'] && $_GET['transtype']!="E"){
							echo "alert('PAF Employee Other Infos update processed. You are required to change TAX Exemption.');";
							echo "location.href='profile_transaction.php?empNo=$empNo&compCode=$compCode&taxstats=O&act=payroll&val=val'";
						}					
						else{
							echo "alert('PAF Employee Other Infos update processed.');";
							echo "location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";				
						}	
					}
				}
				else {
					echo "alert('PAF Employee Other Infos update failed');";
				}
			}
		}	
	exit();			
	break;
	case "position":
		$checkposition=$maintEmpObj->recordChecker("SELECT * FROM tblPAF_Position where stat='H' and compCode='{$compCode}' and empNo='{$empNo}' and refNo!='{$_GET['refno']}'");
		if($checkposition){
				echo "alert('PAF transaction already exist. Kindly edit the existing PAF transaction.');";
				echo "	location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
		}
		else{
			if ($maintEmpObj->position()) {
				if ($_GET['postag'] != "1") {
					$strproc = "queued";
				} else {
					$strproc = "processed";
				}
				$refno = $_GET['refno'];
				//08-16-2023
				echo "var chebranch = confirm('PAF Employee Position update $strproc, Do you want to change the Rate Mode, Salary and Pay Category?');";
				echo "if (chebranch==true) {";
				echo 	"location.href = 'profile_transaction.php?act=payroll&empNo=$empNo&compCode=$compCode&frmRefNo=$refno';";
				echo "} else {";
				echo "	location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
				echo "}";
				//IF WANT TO CHANGE PAYROLL & ALLOWANCE FOR ACCOUNTING VIEW ONLY
				// echo "var chebranch = confirm('PAF Employee Position update $strproc, Do you want to change the Rate Mode, Salary and Pay Category?');";
				// echo "if (chebranch==true) {";
				// 	if($maintEmpObj->pass2Payroll()) {
				// 		echo 	"alert('PAF Payroll related has been forwarded to Accounting Section.');";}
				// 	else {
				// 		echo 	"alert('There was an error creating PAF Payroll.');";}
				// echo "} else {";
				// echo "	location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
				// echo "}";
			} else {
				echo "alert('PAF Employee Position update failed');";
			}	
		}
	exit();			
	break;
	case "branch":
		$checkbranch=$maintEmpObj->recordChecker("SELECT * FROM tblPAF_Branch where stat='H' and compCode='{$compCode}' and empNo='{$empNo}' and refNo!='{$_GET['refno']}'");
		if($checkbranch){
				echo "alert('PAF transaction already exist. Kindly edit the existing PAF transaction.');";
				echo "	location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
		}
		else{
			if ($maintEmpObj->branch()) {
				if ($_GET['brtag'] != "1") {
					$strproc = "queued";
				} else {
					$strproc = "processed";
				}
				$refno = $_GET['refno'];
				echo "var chebranch = confirm('PAF Employee Branch update $strproc, Do you want to change the position?');";
				echo "if (chebranch==true) {";
				echo 	"location.href = 'profile_transaction.php?act=position&empNo=$empNo&compCode=$compCode&frmRefNo=$refno';";
				echo "} else {";
				echo "	location.href = 'profile_actionlist.php?empNo=$empNo&compCode=$compCode';";
				echo "}";
			} else {
				echo "alert('PAF Employee Branch update failed');";
			}
		}
	exit();			
	break;	
	
	case "getBranchPayGrp":
		$arrBranchDetails = $maintEmpObj->getEmpBranchArt($_SESSION["company_code"],$_GET["empSelBranch"]);	
			if($_GET["empSelBranch"]!='0001')
				echo "$('cmbbrgroup').value=".$arrBranchDetails["brnDefGrp"].";";
			else
				echo "$('cmbbrgroup').value='0';";
		exit();
		
	break;					

}


if ($_GET['frmRefNo'] == "") {
	$refno = $maintEmpObj->getRefNo($compCode);
} else {
	$refno['refno'] = $_GET['frmRefNo'];
}	
switch ($_GET['act']) {
	case "empstat":
		$action = "PAF Employee Status";
	break;
	case "branch":
		$action = "PAF Branch";
	break;
	case "position":
		$action = "PAF Position";
	break;
	case "payroll":
		$action = "PAF Payroll Related";
	break;
	case "others":
		$action = "PAF Others";
	break;
	
}

if($_GET['action']=="loadMunicipality")
{
	$arrResProv=$commonEmpObj->makeArr($commonEmpObj->getMunicipality(" where provinceCd='{$_GET['provcd']}'"),'municipalityCd','municipalityDesc','');
	$commonEmpObj->DropDownMenu($arrResProv,'cmbMunicipality','','class="inputs" style="width:180px"');
	exit();
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type="text/javascript"  src="../../../includes/calendar.js"></script>
        <STYLE>@import url('../../../includes/calendar.css');</STYLE>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
       	<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url('../../../js/themes/alphacube.css');</STYLE>	
        <STYLE>@import url('../../style/tabs.css');</STYLE>
		<STYLE>@import url('../../../js/themes/alphacube.css');</STYLE>	

	
<STYLE>@import url('style/index_payroll.css');</STYLE>
		<!--calendar lib-->
	<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
	<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
	<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->		
	<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
	<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
	<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
	<link rel="stylesheet" type="text/css" href="../../../js/extjs/resources/css/ext-all.css" />
    <script type="text/javascript">
	function cnclLockSys(){
		Windows.getWindow('winLcok').close();
		$('passLock').style.visibility = 'hidden';
	}	
	function Dolock(){
		var winLock = new Window({
			id : "winLcok",
			className: "alphacube", 
			resizable: false, 
			draggable:false, 
			minimizable : false,
			maximizable : false,
			closable 	: false,
			width: 200,
			height : 80
		});
			$('passLock').style.visibility = 'visible';
			winLock.setContent('passLock', false, false);				
			winLock.setZIndex(500);
			winLock.setDestroyOnClose();
			winLock.showCenter(true);				
	}	
	
	function viewDetails(nUrl,option,recNo,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		option=option+" Employee Allowance";
		wd=880;
		ht=400;
		var viewDtl = new Window({
		id: "viewDtl",
		className : 'mac_os_x',
		width:wd, 
		height:ht, 
		zIndex: 100, 
		resizable: false, 
		minimizable : false,
		title: option, 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		viewDtl.setURL(nUrl);
		viewDtl.show(true);
		viewDtl.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == viewDtl) {
		        viewDtl = null;
				Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}  
	
	function setCheck(id,id2){
		//var cnt = $('chCtr').value;
		var idselected = $(id2).value;
		var ididle = $(id).value;
		if(idselected!=""){
			document.getElementById(id).checked=false;
		}
	}
        </script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
        
	<style type="text/css">
        .headertxt {font-family: verdana; font-size: 11px;}
		.style1 {font-size: 11px}
		.style2 {
			font-family: verdana;
			font-weight: bold;
		}
		.style3 {font-family: verdana; font-size: 11px; font-weight: bold; }
		.style4 {font-family: verdana}
		.style5 {font-family: verdana; font-size: 11px; color: #FF0000; }
		.style6 {font-size: 13px}
		a:link    {color:red; text-decoration:none}
		a:visited {color:red;}
		a:hover   {color:green;}
        </style>        
	</HEAD>
	<BODY onLoad="<? if ($_GET['frmRefNo'] !="") { echo "checkECOLA();"; }?>">
		<FORM name='frmActionType' id="frmActionType" action="" onSubmit="return validateTabs('<?=$_GET['act']?>');" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" width="600" style="margin: 10px;">
			  <tr>
					
      <td class="parentGridHdr" height="30"> &nbsp;<img src="../../../images/grid.png">&nbsp;
        <?=$action?>
        <input name="code" type="hidden" id="code3" value="<?=$_GET['act']?>">
        <input type="hidden" value="<?=$_GET['empNo']?>" name="empNo" id="empNo3">
        <input type="hidden" value="<?=$_GET['compCode']?>" name="compCode" id="compCode3">
        <input type="hidden" value="<?=date('Y-m-d')?>" name="today" id="today"></td>
				</tr>
 <tr>
                  
                  <td colspan="3" height="7" >
                            </td>
              </tr>
              
                <tr>
				<td class="parentGridDtl" valign="top">
                               <?
                               $act = $_GET['act'];
                               if ($act == "empstat") {
							   ?>
                               <div class="tab1">EMPLOYMENT STATUS</div>
                               <?
							   }
							   else{
							   ?>
                                <a href="profile_transaction.php?act=empstat&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>" ><div class="tab1">EMPLOYMENT STATUS</div></a>
                               <?
							   }
                               if ($act == "branch" and $vis!="") {
							   ?>
                               <div class="tab2">BRANCH</div>
                               <?
							   }
							   elseif ($act == "branch" and $vis=="") {
							   ?>
                               <div class="tab2">BRANCH</div>
                               <?	 
							   }
							   elseif ($act != "branch" and $vis==""){
							   ?>
                               <a href="profile_transaction.php?act=branch&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>"><div class="tab2">BRANCH</div></a>
                               <?
							   }
                               if ($act == "position") {
							   ?>
                               <div class="tab3">POSITION</div>
                               <?
							   }
							   else{
							   ?>
                               <a href="profile_transaction.php?act=position&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>"><div class="tab3">POSITION</div></a>
                               <?
							   }
                               if ($act == "payroll" and $vis!="") {
							   ?>
                               <div class="tab4">PAYROLL RELATED</div>
                               <?
							   }
							   elseif ($act == "payroll" and $vis==""){
							   ?>
                               <div class="tab4">PAYROLL RELATED</div>
                               <?
							   }
                               elseif ($act != "payroll" and $vis==""){
							   ?>
                               <a href="profile_transaction.php?act=payroll&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>"><div class="tab4">PAYROLL RELATED</div></a>
							   <?
							   }
                               if ($act == "others") {
							   ?>
                               <div class="tab5">OTHERS</div>
                               <?
							   }
							   else{
							   ?>
                               <a href="profile_transaction.php?act=others&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>"><div class="tab5">OTHERS</div></a>
                               <?
							   }
							   ?>
                               <?
                                if($vis==""){
							   ?>
                               <a href="#" onClick="viewDetails('employee_allowance.php?empNo=<?=$_GET['empNo']?>&refNo='+document.getElementById('refno').value+'&controlNo='+document.getElementById('ctrlno').value+'&effectivitydate='+'<?=$_POST['txtempstatDate']?>','Add','','contact_list_ajax.php','TSCont',0,0,'txtSrch','cmbSrch')"><div class="tab6">ALLOWANCE</div></a>
                               <?
								}
							   ?>
                               <br><br>
                  <table width="580" border="0" align="center" cellpadding="0" cellspacing="0" class="childGrid">
                 
                  <tr>
                  		<td><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                              <td width="21%" height="20"><span class="style3">Ref No</span></td>
                              <td width="3%"><div align="center"><span class="style3">:</span></div></td>
                            <td width="76%" height="20"><span class="headertxt"><?=$refno['refno']?><span class="parentGridHdr">
                              <input type="hidden" value="<?=$refno['refno']?>" name="refno" id="refno">
                            <input type="hidden" name="taxstat" id="taxstat" value="<?=$_GET['taxstats'];?>">
                              <input type="hidden" name="transtype" id="transtype" value="<?=$_GET['trantype'];?>">
                            </span></span></td>
                          </tr>
                            
                            <tr>
                              <td height="20"><span class="style3">Company</span></td>
                              <td><div align="center"><span class="style3">:</span></div></td>
                              <td height="20"><span class="headertxt"><?  $compName = $maintEmpObj->getCompany($compCode); echo $compName['compName'];?></span></td>
                          </tr>
                            <tr>
                              <td height="20"><span class="style3" >Employee No.</span></td>
                              <td><div align="center"><span class="style3">:</span></div></td>
                              <td height="20"><span class="headertxt">
                              <?=$empProf['empNo'];?></span></td>
                          </tr>
                            <tr>
                              <td height="20"><span class="style3" onClick="Dolock()">Name</span></td>
                              <td><div align="center"><span class="style3">:</span></div></td>
                              <td height="20"><span class="headertxt"><?=$empProf['empLastName'] . ", " . $empProf['empFirstName'] . " " . $empProf['empMidName'];?></span></td>
                          </tr>
                    </table></td>
                  </tr>
                  <tr>
                  	<td height="20">
							<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td height="1" bgcolor="#C4E2FF"></td>
                              </tr>
                          </table>                    
                    </td>
                  </tr>
                    <? $act = $_GET['act'];
					if ($act == "empstat") {
						if ($_GET['frmRefNo'] !="" ) {
							$empStatview = $maintEmpObj->getPAFvalue($_GET['frmRefNo'],'tblPAF_EmpStatus',$compCode);
							$empStat_new = $empStatview['new_status'];
							$empEnd_new = $empStatview['new_enddate'];
							$empnso_new = $empStatview['new_nos'];
						}					
					?>
                    <tr>
                      <td><table width="100%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                          
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td colspan="2"><table width="65%" border="0" align="right" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="41%">&nbsp;</td>
                              <td width="59%">
                                
                                <div align="left">
                                  <input name="Save" type="button" onClick="empstat()" class="inputs" id="button" value="Save">
                                  <input name="button6" type="button" onClick="location.href = 'profile_actionlist.php?empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="inputs" id="button6" value="Cancel/Exit">
                                </div></td>
                            </tr>
                            
                          </table></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td><div align="center" class="style1 style2">
                            <div align="left">Old Value</div>
                          </div></td>
                          <td width="35%"><div align="center" class="style3">
                            <div align="left">New Value</div>
                          </div></td>
                        </tr>
                        <tr>
                          <td width="24%" class="style4 style1"><div align="right"><strong>Employment Status </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td width="34%"><div align="center" class="headertxt" >
                            <div align="left">
                              <?=$empother_info['employmentTag'];?>
                            </div>
                          </div></td>
                          <td>
                            <div align="left">
                              <? $maintEmpObj->DropDownMenu(array(""=>"",'RG'=>'Regular','PR'=>'Probationary','CN'=>'Contractual'),'cmbempstatus',$empStat_new,'class="inputs" style="width:180px;" onChange="setNOS(this.value);"' ); ?>
                              <input type="hidden" value="<?=$empProf['employmentTag']?>" name="oldstatus" id="oldstatus">
                            </div></td>
                        </tr>
                         <tr>
                          <td width="24%" class="style4 style1"><div align="right"><strong>Old End Date </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td width="34%"><div align="center" class="headertxt" >
                            <div align="left">
                              <?=$maintEmpObj->valDateArt($empother_info['empEndDate']);?>
                            </div>
                          </div></td>
                          <td>
                            <div align="left">
                              <input type='text'  class='inputs' name='txtenddate' id='txtenddate' maxLength='10' readonly size="10" value="<?=$maintEmpObj->valDateArt($empEnd_new);?>"/>
                            <a href="#"><img name="imgenddate" id="imgenddate" src="../../../images/cal_new.png" title="End Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a>
                              <input type="hidden" value="<?=$maintEmpObj->valDateArt($empother_info['empEndDate'])?>" name="oldenddate" id="oldenddate">
                            </div></td>
                        </tr>
                        <tr>
                          <td width="24%" class="style4 style1"><div align="right"><strong>Nature of Separation </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td width="34%"><div align="center" class="headertxt" >
                            <div align="left">
                              <?=$empother_info['empNOS'];?>
                            </div>
                          </div></td>
                          <td>
                            <div align="left">
                              <? 
							  $sqlNature = "Select * from tblNatures";				
							  $arrNatures = $maintEmpObj->getArrRes($maintEmpObj->execQry($sqlNature));
							  $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($arrNatures,'natureCode','Description',''),'cmbempnos',$empnso_new,'class="inputs" style="width:180px;" onChange="setEMP(this.value);"' ); ?>
                              <input type="hidden" value="<?=$empProf['empNOS']?>" name="oldnos" id="oldnos">
                            </div></td>
                        </tr>
                        <tr>
                          <td colspan="3" class="" height="20"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                              <td height="1" bgcolor="#C4E2FF"></td>
                            </tr>
                          </table></td>
                        </tr>
                        
                        <tr>
                          <td class="style4 style1">&nbsp;</td>
                          <td><span class="style3">
                            <input name="ctrlno" style="width:180px;" type="hidden" class="inputs" id="ctrlno" value="<?=$empStatview['controlNo']?>" size="30">
                          </span></td>
                          <td><input type="hidden" value="H" name="cmbstatus" id="cmbstatus"></td>
                        </tr>
                        
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Effectivity Date </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td><input value="<?=($empStatview['effectivitydate'] !=""? date('m-d-Y',strtotime($empStatview['effectivitydate'])) : "")?>" type='text'  class='inputs' name='txtempstatDate' id='txtempstatDate' maxLength='10' readonly size="10"/><a href="#"><img name="imgempstatDate" id="imgempstatDate" src="../../../images/cal_new.png" title="Effectivity Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                          <td>&nbsp;</td>
                        </tr>
                        
                        <tr>
                          <td class=" style1"><div align="right"><span class="style4"><strong>Remarks </strong><strong>:</strong></span><span class="style4">&nbsp;&nbsp;</span>&nbsp;</div></td>
                          <td height="30" colspan="2"><input name="txtempstatremarks" value="<?=$empStatview['remarks']?>" style="width:180px;" type="text" class="inputs" id="txtempstatremarks" size="30"></td>
                        </tr>
                        <tr>
                          <td height="20" colspan="3" class=" style1">
                          <div align="center">
                          <span style="cursor:pointer"  onClick="location.href='profile_transaction.php?act=position&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Position </span> | 
                          <span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=others&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Others </span> |
                          <?
                          if($vis!="hide"){
						  ?>
                          <span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=branch&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Branch </span> | 
                          <span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=payroll&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Payroll Related </span> | 
                          <span style="cursor:pointer" onClick="viewDetails('employee_allowance.php?empNo=<?=$_GET['empNo']?>&refNo='+document.getElementById('refno').value+'&controlNo='+document.getElementById('ctrlno').value+'&effectivitydate='+document.getElementById('txtempstatDate').value,'Add','','contact_list_ajax.php','TSCont',0,0,'txtSrch','cmbSrch')" class="style5" align="right"> Allowance</span>
                          <?
						  }
						  ?>
                          </div></td>
                        </tr>
                        
                        
                        
                      </table></td>
                    </tr>
                    <? } elseif ($act == "branch") {
						if ($_GET['frmRefNo'] !="" ) {
							$Branchview = $maintEmpObj->getPAFvalue($_GET['frmRefNo'],'tblPAF_Branch',$compCode);
							$Branch_new = $Branchview['new_branchCode'];
							$Group_new = $Branchview['new_payGrp'];
						}						
					
					?>
                    <tr>
                      <td><table width="100%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                          <td>&nbsp;</td>
                          <td colspan="3"><table width="65%" border="0" align="right" cellpadding="0" cellspacing="0">
                          <tr>
                                <td width="41%">&nbsp;</td>
                              <td width="59%"><div align="left">
                                    <input name="button4" type="button" onClick="branch()" class="inputs" id="button5" value="Save">
                                    <input name="button5" type="button" onClick="location.href = 'profile_actionlist.php?empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="inputs" id="button7" value="Cancel/Exit">
                                </div></td>
                            </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="style1 style2">
                            <div align="left">Old Value</div>
                          </div></td>
                          <td width="36%"><div align="center" class="style3">
                            <div align="left">New Value</div>
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Pay Group </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt">
                          <?="Group " . $empProf['empPayGrp'];?></span></td>
                          <td><? $maintEmpObj->DropDownMenu(array('','Group 1'),'cmbbrgroup',$Group_new,'class="inputs" style="width:180px;"'); ?>
                          <input type="hidden" name="old_payGrp" value="<?=$empProf['empPayGrp']?>" id="old_payGrp"></td>
                        </tr>
                         <tr>
                          <td width="27%" class="style4 style1"><div align="right"><strong>Branch </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td width="3%">&nbsp;</td>
                          <td width="34%"><div align="center" class="headertxt" >
                              <div align="left">
                                <?
								$brnch = $maintEmpObj->getEmpBranchArt($compCode,$empProf['empBrnCode']);
								echo $brnch['brnDesc'];?>
                                  </div>
                          </div></td>
                          <td>
                              <div align="left">
                                <?
                               $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranch($compCode),'brnCode','brnDesc',''),'cmbbranch',$Branch_new,'class="inputs" style="width:180px;" onChange=changeBrnGrp();');?>
                               <input type="hidden" value="<?=$empProf['empBrnCode']?>" name="old_branchCode" id="old_branchCode">
                                </div></td></tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Division </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt">
                            <?=$division_desc['deptDesc'] ?>
                          </span></td>
                          <td><span class="headertxt" id="dvdiv2"> </span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Department </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt">
                            <?=$department_desc['deptDesc'] ?>
                          </span></td>
                          <td><span class="headertxt" id="dvdept2"> </span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Section </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt" >
                            <?=$section_desc['deptDesc'] ?>
                          </span></td>
                          <td><span class="headertxt" id="dvsec2"> </span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Rank </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt" >
                            <?=$rank_desc['rankDesc'] ?>
                          </span></td>
                          <td><span class="headertxt" id="dvrank2"> </span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Level </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt" >
                            <?=$level_desc ?>
                          </span></td>
                          <td><span class="headertxt" id="dvlevel2"> </span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Position </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empother_info['posShortDesc'];?>
                            </div>
                          </div></td>
                          <td><div align="left"></div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1">&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td colspan="4" class="" height="20"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td height="1" bgcolor="#C4E2FF"></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"></td>
                          <td>&nbsp;</td>
                          <td><span class="style3">
                            <input name="ctrlno" style="width:180px;" type="hidden" class="inputs" id="ctrlno" value="<?=$Branchview['controlNo']?>" size="30">
                          </span></td>
                          <td><input type="hidden" value="H" name="cmbbrstatus" id="cmbbrstatus"></td>
                        </tr>
                        
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Effectivity Date </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><input value="<?=($Branchview['effectivitydate'] !=""? date('m-d-Y',strtotime($Branchview['effectivitydate'])) : "")?>" type='text'  class='inputs' name='txtbrDate' id='txtbrDate' maxLength='10' readonly size="10"/>
                            <a href="#"><img name="imgbrDate" id="imgbrDate" src="../../../images/cal_new.png" title="Effectivity Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td class=" style1"><div align="right"><span class="style4"><strong>Remarks </strong><strong>:</strong></span><span class="style4">&nbsp;&nbsp;</span>&nbsp;</div></td>
                          <td height="30">&nbsp;</td>
                          <td height="30"><input name="txtbrremarks" value="<?=$Branchview['remarks']?>" style="width:180px;" type="text" class="inputs" id="txtbrremarks" size="30"></td>
                          <td height="30" align="right">&nbsp;</td>
                        </tr>
                        
                        <tr>
                          <td height="20" colspan="4" class=" style1">
                          	<div align="center">
                            <span style="cursor:pointer;"  onClick="location.href='profile_transaction.php?act=empstat&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Employment Status </span> | 
                          	<span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=position&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Position </span> | 
                          	<span style="cursor:pointer<?=$vis?>" onClick="location.href='profile_transaction.php?act=payroll&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Payroll Related </span> | 
                   	        <span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=others&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Others </span> | 
                            <span style="cursor:pointer<?=$vis?>" onClick="viewDetails('employee_allowance.php?empNo=<?=$_GET['empNo']?>&refNo='+document.getElementById('refno').value+'&controlNo='+document.getElementById('ctrlno').value+'&effectivitydate='+document.getElementById('txtbrDate').value,'Add','','contact_list_ajax.php','TSCont',0,0,'txtSrch','cmbSrch')" class="style5" align="right"> Allowance</span>
                            </div></td>
                        </tr>
                      </table></td>
                    </tr>
                    <? } elseif ($act == "position") {
					if ($_GET['frmRefNo'] !="" ) {
						$pos = $maintEmpObj->getPAFvalue($_GET['frmRefNo'],'tblPAF_Position',$compCode);
						$controlNo = $pos['controlNo'];
						$effectivitydate = $pos['effectivitydate'];
						$remarks = $pos['remarks'];
						$posCode = $pos['new_posCode'];
						$divCode = $pos['new_divCode'];
						$deptCode = $pos['new_deptCode'];
						$secCode = $pos['new_secCode'];
						$rank = $pos['new_cat'];
						$level = $pos['new_level'];
						$pos = $maintEmpObj->getpositionwil("where compCode='$compCode' and posCode='{$pos['new_posCode']}'",2);
						$division_new = $maintEmpObj->getDivDescArt($compCode, $pos['divCode']);
						$department_new = $maintEmpObj->getDeptDescArt($compCode, $pos['divCode'],$pos['deptCode']);
						$section_new =  $maintEmpObj->getSectDescArt($compCode, $pos['divCode'],$pos['deptCode'],$pos['sectCode']);
						$rank_new = $maintEmpObj->getRank($pos['rank']);
					}
					?>
                    <tr>
                      <td><table width="100%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                          <td>&nbsp;</td>
                          <td colspan="3"><table width="65%" border="0" align="right" cellpadding="0" cellspacing="0">
                              <tr>
                                <td width="41%">&nbsp;</td>
                                <td width="59%"><div align="left">
                                    <input name="button3" type="button" onClick="position()" class="inputs" id="button4" value="Save">
                                    <input name="button7" type="button" onClick="location.href = 'profile_actionlist.php?empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="inputs" id="button8" value="Cancel/Exit">
                                </div></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="style1 style2">
                            <div align="left">Old Value</div>
                          </div></td>
                          <td width="36%"><div align="center" class="style3">
                            <div align="left">New Value</div>
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Division </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt">
                            <?=$division_desc['deptDesc'] ?>
                            
                            </span></td>
                          <td><span class="headertxt" id="dvdiv"><?=$division_new['deptDesc'];?></span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Department </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt"> <?=$department_desc['deptDesc'] ?>
                            <input name="poscode" type="hidden" id="code" value="getposid">
                          </span></td>
                          <td><span class="headertxt" id="dvdept"><?=$department_new['deptDesc'];?></span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Section </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt" > <?=$section_desc['deptDesc'] ?></span></td>
                          <td><span class="headertxt" id="dvsec"><?=$section_new['deptDesc'];
						?> </span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Rank </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt" > <?=$rank_desc['rankDesc'] ?></span></td>
                          <td><span class="headertxt" id="dvrank"><?=$rank_new['rankDesc'];?></span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Level </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><span class="headertxt" > <?=$level_desc ?></span></td>
                          <td><span class="headertxt" id="dvlevel"><?
                          if ($pos['level'] !="") {
						  	echo "Level " . $pos['level'];
						  }
						  ?>
                          </span></td>
                        </tr>
                        <tr>
                          <td width="26%" class="style4 style1"><div align="right"><strong>Position </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td width="3%">&nbsp;</td>
                          <td width="35%"><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empother_info['posShortDesc'];?>
                                <input type="hidden" value="<?=$empProf['empPosId']?>" name="old_posCode" id="old_posCode">
                                <input type="hidden" value="<?=$empProf['empDiv']?>" name="old_divCode" id="old_divCode">
                                <input type="hidden" value="<?=$empProf['empDepCode']?>" name="old_deptCode" id="old_deptCode">
                                <input type="hidden" value="<?=$empProf['empSecCode']?>" name="old_secCode" id="old_secCode">
                                <input type="hidden" value="<?=$empProf['empRank']?>" name="old_cat" id="old_cat">
                                <input type="hidden" value="<?=$empProf['empLevel']?>" name="old_level" id="old_level">
                                  </div>
                          </div></td>
                          <td>
                              <div align="left">
                                <?
                              $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getpositionwil("where compCode='$compCode'",1),'posCode','posDesc',''),'cmbposition',$posCode,'class="inputs" onChange="getposid()" style="width:180px;"');?>
                                <input type="hidden" value="<?=$divCode?>" name="new_divCode" id="new_divCode">
                                <input type="hidden" value="<?=$deptCode?>" name="new_deptCode" id="new_deptCode">
                                <input type="hidden" value="<?=$secCode?>" name="new_secCode" id="new_secCode">
                                <input type="hidden" value="<?=$rank?>" name="new_cat" id="new_cat">
                                <input type="hidden" value="<?=$level?>" name="new_level" id="new_level">
                              </div></td>
                        </tr>
                        <tr>
                          <td colspan="4" class="" height="20"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td height="1" bgcolor="#C4E2FF"></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td class="style4 style1">&nbsp;</td>
                          <td>&nbsp;</td>
                          <td><span class="style3"><?=$pos['controlNo']?>
                            <input name="ctrlno" style="width:180px;" type="hidden" class="inputs" id="ctrlno" value="<?=$controlNo?>" size="30">
                          </span></td>
                          <td><input type="hidden" value="H" name="cmbposstatus" id="cmbposstatus"></td>
                        </tr>
                        
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Effectivity Date </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><input value="<?=($effectivitydate !=""? date('m-d-Y',strtotime($effectivitydate)) : "")?>" type='text'  class='inputs' name='txtposDate' id='txtposDate' maxLength='10' readonly size="10"/>
                            <a href="#"><img name="imgposDate" id="imgposDate" src="../../../images/cal_new.png" title="Effectivity Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td class=" style1"><div align="right"><span class="style4"><strong>Remarks </strong><strong>:</strong></span><span class="style4">&nbsp;&nbsp;</span>&nbsp;</div></td>
                          <td height="30">&nbsp;</td>
                          <td height="30"><input value="<?=$remarks?>" name="txtposremarks" style="width:180px;" type="text" class="inputs" id="txtposremarks" size="30"></td>
                          <td height="30">&nbsp;</td>
                        </tr>
                        
                        <tr>
                          <td height="20" colspan="4" class=" style1"><div align="center">
                          <span style="cursor:pointer;"  onClick="location.href='profile_transaction.php?act=empstat&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Employment Status </span> | 
                          <span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=others&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Others </span> |   
                          <?
                          if($vis!="hide"){
						  ?>
                          <span style="cursor:pointer<?=$vis?>" onClick="location.href='profile_transaction.php?act=branch&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Branch </span> | 
                          <span style="cursor:pointer<?=$vis?>" onClick="location.href='profile_transaction.php?act=payroll&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Payroll Related </span> | 
                          <span style="cursor:pointer<?=$vis?>" onClick="viewDetails('employee_allowance.php?empNo=<?=$_GET['empNo']?>&refNo='+document.getElementById('refno').value+'&controlNo='+document.getElementById('ctrlno').value+'&effectivitydate='+document.getElementById('txtposDate').value,'Add','','contact_list_ajax.php','TSCont',0,0,'txtSrch','cmbSrch')" class="style5" align="right"> Allowance</span>
                          <?
						  }
						  ?>
                          </div></td>
                        </tr>
                      </table></td>
                    </tr>
                    <? } elseif ($act == "payroll") {
						$payMrate ="0.00";
						$payDrate ="0.00";
						$payHrate ="0.00";
						if ($_GET['frmRefNo'] !="" ) {
							$payroll_view = $maintEmpObj->getPAFvalue($_GET['frmRefNo'],'tblPAF_PayrollRelated',$compCode);
							$payteu = $payroll_view['new_empTeu'];
							$paybankCd = $payroll_view['new_empBankCd'];
							$payAcctNo = $payroll_view['new_empAcctNo'];
							if ($payroll_view['new_empMrate'] !=0) {
								$payMrate = number_format($payroll_view['new_empMrate'],2);
							} 	
							if ($payroll_view['new_empDrate'] !=0) {
								$payDrate = number_format($payroll_view['new_empDrate'],2);
							} 	
							if ($payroll_view['new_empMrate'] !=0) {
								$payHrate = number_format($payroll_view['new_empHrate'],2);
							} 
							$payPayType = $payroll_view['new_empPayType'];
							$payPayGrp = $payroll_view['new_empPayGrp'];
							$payPayCat = $payroll_view['new_category'];
						}					
					?>
                    <tr>
                      <td><table width="100%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                          <td>&nbsp;</td>
                          <td colspan="3"><table width="73%" border="0" align="right" cellpadding="0" cellspacing="0">
                            <tr>
                              <td width="36%">&nbsp;</td>
                              <td width="59%"><div align="left">
                                <input name="Save" type="button" onClick="payroll()" class="inputs" id="Save" value="Save">
                                <input name="button8" type="button" onClick="location.href = 'profile_actionlist.php?empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="inputs" id="button9" value="Cancel/Exit">
                              </div></td>
                            </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td width="38%">&nbsp;</td>
                        </tr>
                        <tr>
                          <td width="37%">&nbsp;</td>
                          <td width="3%">&nbsp;</td>
                          <td width="22%"><div align="center" class="style1 style2">
                            <div align="left">Old Value</div>
                          </div></td>
                          <td><div align="center" class="style3">
                            <div align="left">New Value</div>
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Rate Mode </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empother_info['empPayType'];?>
                                  </div>
                          </div></td>
                          <td>
                            <div align="left">
                                <? $maintEmpObj->DropDownMenu(array('','D'=>'Daily','M'=>'Monthly'),'cmbpstatus',$payPayType,' class="inputs" onChange="checkrate();" style="width:180px;"'); ?>
                              <input type="hidden" value="<?=$empProf['empPayType']?>" name="oldpayrollstatus" id="oldpayrollstatus">
                            </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Monthly Rate </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empMrate'];?>
                                  </div>
                          </div></td>
                          <td>
                              <div align="left">
                                <label>
                                <input name="txtsalary" onBlur="checkECOLA();round_number()" style="width:180px;" type="text" value="<?=$payMrate?>" readonly   class="inputs" onKeyPress="return computePAFRates(this.value,<?=$compCode?>,'1',event);" id="txtsalary">
                                </label>
                                <input type="hidden" value="<?=$empProf['empMrate']?>" name="oldmrate" id="oldstatus4">
                            </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Daily Rate </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empDrate'];?>
                                  </div>
                          </div></td>
                          <td>
                              <div align="left">
                                <input class='inputs' onKeyPress="return computePAFRates(this.value,<?=$compCode?>,'0',event);"  onBlur="checkECOLA();round_number()" type="text" style="width:180px;" value="<?=$payDrate?>"   name="txtdailyrate" id="txtdailyrate" readonly />
                                <input type="hidden" value="<?=$empProf['empDrate']?>" name="olddrate" id="olddrate">
                            </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Hourly Rate </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empHrate'];?>
                                  </div>
                          </div></td>
                          <td>
                              <div align="left">
                                <input class='inputs' style="width:180px;" type="text" value="<?=$payHrate?>"  name="txthourlyrate" readonly id="txthourlyrate" />
                                <input type="hidden" value="<?=$empProf['empHrate']?>" name="oldhrate" id="oldhrate">
                            </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Tax Exemption </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empTeu'];?>
                                  </div>
                          </div></td>
                          <td>
                              <div align="left">
                                <? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getTEU(),'teuCode','teuDesc',''),'cmbexemption',$payteu,'class="inputs" style="width:180px;" onChange="alertStatus(this.value);"'); ?>
                                <input type="hidden" value="<?=$empProf['empTeu']?>" name="oldteu" id="oldstatus5"></div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Bank </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empother_info['bankDesc'];?>
                                  </div>
                          </div></td>
                          <td>
                              <div align="left">
                                <? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getbankwil(),'bankCd','bankDesc',''),'cmbbank',$paybankCd,'class="inputs" style="width:180px;" onChange="checkno(\'empAcctNo\',\'\',\'' . 0 . '\',\'Account No.\',\'dvAcctNo\')"'); ?>
                                <input type="hidden" value="<?=$empProf['empBankCd']?>" name="oldbank" id="oldbank">
                            </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Account No. </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empAcctNo'];?>
                                  </div>
                          </div></td>
                          <td>
                            <div align="left">
                              <input class='inputs' type="text" onKeyDown="return AcctFormat(event);" onKeyPress="return isNumberInput2Decimal(this, event);" value="<?=$payAcctNo?>"  name="txtbankaccount" id="txtbankaccount" onBlur="checkno('empAcctNo',this.value,'0','Account No.','dvAcctNo')" style="width:180px;" /><br><span id="dvAcctNo" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chAcctNo" id="chAcctNo">
                                <input type="hidden" value="<?=$empProf['empAcctNo']?>" name="oldaccountno" id="oldaccountno">
                            </div></td>
                        </tr>
                        
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Pay Category </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                            <div align="left">
                              <?
                              $payCat = $maintEmpObj->getPayCat($_SESSION['company_code']," AND payCat='".$empother_info['empPayCat']."'");
							  echo $payCat['payCatDesc'];?>
                            </div>
                          </div></td>
                          <td><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getPayCat($_SESSION['company_code'],''),'payCat','payCatDesc',''),'cmbCategory',$payPayCat,'class="inputs" style="width:180px;"'); ?>
                          <input type="hidden" value="<?=$empProf['empPayCat']?>" name="oldpaycat" id="oldpaycat">                          </td>
                        </tr>
                        
                        <tr>
                          <td colspan="4" class="" height="20"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td height="1" bgcolor="#C4E2FF"></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td class="style4 style1">&nbsp;</td>
                          <td>&nbsp;</td>
                          <td colspan="2"><span class="style3">
                            <input name="ctrlno" style="width:180px;" type="hidden" class="inputs" id="ctrlno" value="<?=$payroll_view['controlNo']?>" size="30">
                          </span></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Reason for Increase</strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td colspan="2"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getPayReason($compCode),'reasonCd','reasonDesc',''),'cmbreason',$payroll_view['reasonCd'],'class="inputs" style="width:180px;"'); ?>
                          <input type="hidden" value="H" name="cmbprstatus" id="cmbprstatus">
                          <input type="hidden" name="txtecola" id="txtecola"></td>
                        </tr>
                        
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Effectivity Date </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><input value="<?=($payroll_view['effectivitydate'] !=""? date('m-d-Y',strtotime($payroll_view['effectivitydate'])) : "")?>" type='text'  class='inputs' name='txtprDate' id='txtprDate' maxLength='10' readonly size="10"/>
                            <a href="#"><img name="imgprDate" id="imgprDate" src="../../../images/cal_new.png" title="Effectivity Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Remarks </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td colspan="2"><input name="txtprremarks" type="text" class="inputs" id="txtprremarks" style="width:180px;" value="<?=$payroll_view['remarks']?>" size="30"></td>
                        </tr>
                        
                        <tr>
                          <td height="30" colspan="4" class=" style1">
                          <div align="center">
                          <span style="cursor:pointer;" onClick="location.href='profile_transaction.php?act=empstat&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Employment Status </span> | 
                          <span style="cursor:pointer<?=$vis?>" onClick="location.href='profile_transaction.php?act=branch&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Branch </span> | 
                          <span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=position&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Position </span> | 
                          <span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=others&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Others </span> |
                          <span style="cursor:pointer<?=$vis?>" onClick="viewDetails('employee_allowance.php?empNo=<?=$_GET['empNo']?>&refNo='+document.getElementById('refno').value+'&controlNo='+document.getElementById('ctrlno').value+'&effectivitydate='+document.getElementById('txtprDate').value,'Add','','contact_list_ajax.php','TSCont',0,0,'txtSrch','cmbSrch')" class="style5" align="right"> Allowance</span>
                          </div>
                          </td>
                        </tr>
                      </table></td>
                    </tr>
                    <? } elseif ($act == "company") {?>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>
                    <? } elseif ($act == "others") {
						if ($_GET['frmRefNo'] !="" ) {
							$others_view = $maintEmpObj->getPAFvalue($_GET['frmRefNo'],'tblPAF_Others',$compCode);
							$othlname = $others_view['new_empLastName'];
							$othfname = $others_view['new_empFirstName'];
							$othmname = $others_view['new_empMidName'];
							$othadd1 = $others_view['new_empAddr1'];
							$othadd2 = $others_view['new_empAddr2'];
							$othCityCd = $others_view['new_empCityCd'];
							$othtin = $others_view['new_empTin'];
							$othsss = $others_view['new_empSssNo'];
							$othphil = $others_view['new_empPhicNo'];
							$othhdmf = $others_view['new_empPagibig'];
							$othBioNum = $others_view['new_bioNumber'];
							$provcd=$others_view['new_empProvinceCd'];
							$municd=$others_view['new_empMunicipalityCd'];
							$civilstatus=$others_view['new_empMarStat'];
							$ecPerson = $others_view['new_empContactPerson'];
							$ecNumber = $others_view['new_empContactNumber'];
							$bloodType = $others_view['new_empBloodType'];
							$sunline = $others_view['new_empSunLine'];
							$globeline = $others_view['new_empGlobeLine'];
							$smartline = $others_view['new_empSmartLine'];
						}						
					?>
                    <tr>
                      <td><table width="100%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td colspan="2"><table width="72%" border="0" align="right" cellpadding="0" cellspacing="0">
                              <tr>
                                <td width="36%">&nbsp;</td>
                                <td width="59%"><div align="left">
                                  <input name="save" type="button" onClick="others()" class="inputs" id="button3" value="Save">
                                  <input name="cancel" type="button" onClick="location.href = 'profile_actionlist.php?empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="inputs" id="button10" value="Cancel/Exit">
                                </div></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td><input type="hidden" id="txtgender" name="txtgender" value="<?=$empProf['empSex'];?>"></td>
                          <td width="38%">&nbsp;</td>
                        </tr>
                        <tr>
                          <td width="28%">&nbsp;</td>
                          <td width="1%">&nbsp;</td>
                          <td width="33%"><div align="center" class="style1 style2">
                              <div align="left">Old Value</div>
                          </div></td>
                          <td><div align="center" class="style3">
                              <div align="left">New Value</div>
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Last Name </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empLastName'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                            <input name="txtlname" style="width:180px;" type="text" value="<?=$othlname?>"  class="inputs" id="txtlname">
                            <input type="hidden" value="<?=$empProf['empLastName']?>" name="old_txtlname" id="old_txtlname">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>First Name </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empFirstName'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                              <label>
                              <input name="txtfname" style="width:180px;" type="text" value="<?=$othfname?>"   class="inputs" id="txtfname">
                              </label>
                              <input type="hidden" value="<?=$empProf['empFirstName']?>" name="old_txtfname" id="old_txtfname">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Middle Name </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empMidName'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                              <input class='inputs' type="text" style="width:180px;" value="<?=$othmname ?>"  name="txtmname" id="txtmname"  />
                              <input type="hidden" value="<?=$empProf['empMidName']?>" name="old_txtmname" id="old_txtmname">
                          </div></td>
                        </tr>
						
						    <tr>
                          <td class="style4 style1"><div align="right"><strong>Birth Date </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=date("m/d/Y",strtotime($empProf['empBday']));?>
								<input type="hidden" value="<?=date("Y-m-d",strtotime($empProf['empBday']))?>" name="old_bdy" id="old_bdy">
                              </div>
                          </div></td>
                         <td>
						 
						 <input value="<?=($empProf['empBday'] !=""? date('m-d-Y',strtotime($empProf['empBday'])) : "")?>" type='text'  class='inputs' name='txtbdyDate' id='txtbdyDate' maxLength='10' readonly size="10"/>
                            <a href="#"><img name="imgbdyDate" id="imgbdyDate" src="../../../images/cal_new.png" title="Birth Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                         
                        </tr>
						
						
						
						
                         <tr>
                          <td class="style4 style1"><div align="right"><strong>Bio - Number </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empBioNo['bioNumber'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                              <input class='inputs' type="text" style="width:180px;" value="<?=$othBioNum?>"  name="txtBioNum" id="txtBioNum"  />
                              <input type="hidden" value="<?=$empBioNo['bioNumber']?>" name="old_txtBioNum" id="old_txtBioNum">
                             
                          </div></td>
                        </tr>
                        <tr>
                           <td class="style4 style1"><div align="right"><strong>Civil Status  </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                           <td>&nbsp;</td>
                           <td><div align="center" class="headertxt" >
                             <div align="left">
                               <?=$empother_info['empMarStat'];?>
                             </div>
                           </div></td>
                           <td><div align="left">
						   <? 
						   $maintEmpObj->DropDownMenu(array('','SG'=>'Single','ME'=>'Married','SP'=>'Separated','WI'=>'Widow(er)'),'cmbstatus',$civilstatus,'class="inputs" style="width:180px;"');   
						   ?>
                           <?
                            if($empother_info['empMarStat']=="Single"){
							   $civilstat="SG";
							   }
							if($empother_info['empMarStat']=="Married"){
								$civilstat="ME";
								}  
							if($empother_info['empMarStat']=="Separated"){
								$civilstat="SP";
								}  
							if($empother_info['empMarStat']=="Widow(er)"){
								$civilstat="WI";
								}  
						   ?>
                           <input type="hidden" value="<?=$civilstat;?>" name="old_txtCStat" id="old_txtCStat"> 
                           </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Home No, Bldg., Street </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empAddr1'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                              <input class='inputs' style="width:180px;" type="text" value="<?=$othadd1?>"  name="txtadd1" id="txtadd1" />
                              <input type="hidden" value="<?=$empProf['empAddr1']?>" name="old_txtadd1" id="old_txtadd1">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Barangay </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empAddr2'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                            <input class='inputs' style="width:180px;" type="text" value="<?=$othadd2 ?>"  name="txtadd2" id="txtadd2" />
                            <input type="hidden" value="<?=$empProf['empAddr2']?>" name="old_txtadd2" id="old_txtadd2">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Province </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empother_info['provinceDesc'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                              <? 
						$arrResProv=$commonEmpObj->makeArr($commonEmpObj->getProvince(),'provinceCd','provinceDesc','');
						$commonEmpObj->DropDownMenu($arrResProv,'cmbProvince',$provcd,'onChange="popProvince(this.value);" class="inputs" style="width:180px;"');?>
                              <input type="hidden" value="<?=$empProf['empProvinceCd'];?>" name="old_cmbcity" id="old_cmbcity">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Municipality/City </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empother_info['municipalityDesc'];?>
                              </div>
                          </div></td>
                          <td><div id="divMunicipality" align="left">
                             <? //" where provinceCd='{$commonEmpObj->provinceCd}'"
						$arrResMun=$commonEmpObj->makeArr($commonEmpObj->getMunicipality(),'municipalityCd','municipalityDesc','');
						$commonEmpObj->DropDownMenu($arrResMun,'cmbMunicipality',$municd,'class="inputs" style="width:180px"');?>
                              <input type="hidden" value="<?=$empProf['empMunicipalityCd']?>" name="old_municipality" id="old_municipality">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Contact Person  </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empECPerson'];?>
                              </div>
                          </div></td>
                          <td><div id="divMunicipality" align="left">
                            <input name="txtContactPerson" style="width:180px;" type="text" value="<?=$ecPerson?>"  class="inputs" id="txtContactPerson" maxlength="50">
                            <input type="hidden" value="<?=$empProf['empECPerson']?>" name="old_contactPerson" id="old_contactPerson">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Contact Number </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empECNumber'];?>
                              </div>
                          </div></td>
                          <td><div id="divMunicipality" align="left">
                            <input name="txtContactNumber" style="width:180px;" type="text" value="<?=$ecNumber?>"  class="inputs" id="txtContactNumber" maxlength="15">
                            <input type="hidden" value="<?=$empProf['empECNumber']?>" name="old_contactNumber" id="old_contactNumber">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Blood Type </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?
								if($empProf['empBloodType']=="0"){
									echo "";
								}
								else{
									echo $empProf['empBloodType'];	
								}
								?>
                              </div>
                          </div></td>
                          <td><div id="divMunicipality" align="left"><? $maintEmpObj->DropDownMenu(array('','A'=>'A','B'=>'B','AB'=>'A B','O'=>'O','A-'=>'A-','A+'=>'A+','B-'=>'B-','B+'=>'B+','O-'=>'O-','O+'=>'O+'),'cmbbloodtype',$bloodType,'class="inputs" style="width:180px;"'); ?>
                            <input type="hidden" value="<?=$empProf['empBloodType']?>" name="old_bloodType" id="old_bloodType">
                          </div></td>
                        </tr>
                        
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>SSS No. </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empSssNo'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                              <input class='inputs' maxlength="10" type="text" value="<?=$othsss ?>" onKeyDown="javascript:return dFilter (event.keyCode, this, '##-#######-#');" onBlur="checkno('empSssNo',this.value,'0','SSS No.','dvsss')"  name="txtsss" id="txtsss" style="width:180px;" /><input type="hidden" name="chsss"  value="" id="chsss">
                              <input type="hidden" value="<?=$empProf['empSssNo']?>" name="old_txtsss" id="old_txtsss">
                              <input type="hidden" value="<?=$empProf['empPayGrp']?>" name="cmbbank" id="cmbbank">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Phil Health No. </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empPhicNo'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                              <input class='inputs'  maxlength="25" type="text" value="<?=$othphil  ?>" onBlur="checkno('empPhicNo',this.value,'0','Philhealth No.','dvphilhealth')" name="txtphilhealth" style="width:180px;" id="txtphilhealth" /><input type="hidden"  name="chphilhealth" value="" id="chphilhealth">
                              <input type="hidden" value="<?=$empProf['empPhicNo']?>" name="old_txtphilhealth" id="old_txtphilhealth">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Tax ID No. </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empTin'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                              <input class='inputs' maxlength="11" type="text" style="width:180px;" value="<?=$othtin ?>" onKeyDown="javascript:return dFilter (event.keyCode, this, '###-###-###');" onBlur="checkno('empTin',this.value,'0','Tax ID No.','dvtaxid')"  name="txttax" id="txttax" /><input type="hidden" name="chtaxid" value="" id="chtaxid">
                              <input type="hidden" value="<?=$empProf['empTin']?>" name="old_txttax" id="old_txttax">
                          </div></td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>HDMF No. </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <?=$empProf['empPagibig'];?>
                              </div>
                          </div></td>
                          <td><div align="left">
                              <input class='inputs' style="width:180px;" maxlength="25"  type="text" value="<?=$othhdmf ?>"   name="txthdmf" id="txthdmf" /><input type="hidden" name="chhdmf" value="" id="chhdmf">
                              <input type="hidden" value="<?=$empProf['empPagibig']?>" name="old_txthdmf" id="old_txthdmf">
                          </div></td>
                        </tr> 
                        <?
                        if($_SESSION['user_telcoaccess']=="Y"){
						?>  
                        <tr>
                        	<td colspan="4" class="style1 style2" align="center" height="25">Company Issued Mobile Phone Line</td>
                        </tr>
                        <tr>
                          <td height="0" colspan="4">
                                  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                      <td height="1" bgcolor="#C4E2FF"></td>
                                    </tr>
                                </table>                    
                          </td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Sun Line </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                              	<input type="checkbox" id="chkSun1" name="chkSun1" disabled <?=($empProf['empSunLine']=="Y"?"checked":"")?>/>&nbsp;&nbsp;&nbsp;&nbsp; </div>
                          </div></td>
                          <td><div align="left" class="inputs">
                          	  <label><input type="checkbox" id="chkSun_0" name="chkSun_0" value="Y" onClick="setCheck('chkSun_1','chkSun_0')" <?=($sunline=="Y"?"checked":"")?><?=($empProf['empSunLine']=="Y"?' disabled ':'')?>/>&nbsp;Sun</label>&nbsp;&nbsp;<label><input type="checkbox" id="chkSun_1" name="chkSun_1" value="N" onClick="setCheck('chkSun_0','chkSun_1')" <?=($sunline=="N"?"checked":"")?><?=($empProf['empSunLine']=="Y"?'':' disabled ')?>/>&nbsp;Remove Sun</label>
                              <input type="hidden" value="<?=$empProf['old_empSunLine']?>" name="old_sunline" id="old_sunline">
                          </div></td>
                        </tr>                        
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Globe Line </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left"><input type="checkbox" id="chkGlobe1" name="chkGlobe1" disabled <?=($empProf['empGlobeLine']=="Y"?"checked":"")?>/>&nbsp;&nbsp; </div>
                          </div></td>
                          <td><div align="left" class="inputs">
                       	    <label><input type="checkbox" id="chkGlobe_0" name="chkGlobe" value="Y" onClick="setCheck('chkGlobe_1','chkGlobe_0')" <?=($globeline=="Y"?"checked":"")?><?=($empProf['empGlobeLine']=="Y"?" disabled ":"")?>/>&nbsp;Globe</label>&nbsp;&nbsp;<label><input type="checkbox" id="chkGlobe_1" name="chkGlobe" value="N" onClick="setCheck('chkGlobe_0','chkGlobe_1')" <?=($empProf['empGlobeLine']=="Y"?"":" disabled ")?>/>
                       	      &nbsp;Remove Globe</label>
                       	    <input type="hidden" value="<?=$empProf['old_empGlobeLine']?>" name="old_globeline" id="old_globeline">
                          </div></td>
                        </tr>                        
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Smart Line </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><div align="center" class="headertxt" >
                              <div align="left">
                                <input type="checkbox" id="chkSmart1" name="chkSmart1" disabled <?=($empProf['empSmartLine']=="Y"?"checked":"")?>/>
                            </div>
                          </div></td>
                          <td><div align="left" class="inputs"><label><input type="checkbox" id="chkSmart_0" name="chkSmart" value="Y"  onClick="setCheck('chkSmart_1','chkSmart_0')" <?=($smartline=="Y"?"checked":"")?><?=($empProf['empSmartLine']=="Y"?" disabled ":"")?>/>&nbsp;Smart</label>&nbsp;&nbsp;<label>
                            <input type="checkbox" id="chkSmart_1" name="chkSmart" value="N" onClick="setCheck('chkSmart_0','chkSmart_1')" <?=($empProf['empSmartLine']=="Y"?"":" disabled ")?>/>
  &nbsp;Remove Smart</label>
<input type="hidden" value="<?=$empProf['old_empSmartLine']?>" name="old_smartline" id="old_smartline">
                          </div></td>
                        </tr>    
                        <?
						}
						?>                    
                        <tr>
                          <td colspan="4" class="" height="20"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
                              <tr>
                                <td height="1" bgcolor="#C4E2FF"></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td class="style4 style1">&nbsp;</td>
                          <td>&nbsp;</td>
                          <td colspan="2"><span class="style3">
                            <input name="ctrlno" style="width:180px;" type="hidden" class="inputs" id="ctrlno" value="<?=$others_view['controlNo']?>" size="30">
                            <input type="hidden" value="H" name="cmbothstatus" id="cmbothstatus">
                          </span></td>
                        </tr>
                        
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Effectivity Date </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td><input value="<?=($others_view['effectivitydate'] !=""? date('m-d-Y',strtotime($others_view['effectivitydate'])) : "")?>" type='text'  class='inputs' name='txtothDate' id='txtothDate' maxLength='10' readonly size="10"/>
                            <a href="#"><img name="imgothDate" id="imgothDate" src="../../../images/cal_new.png" title="Effectivity Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td class="style4 style1"><div align="right"><strong>Remarks </strong><span class="style2">:</span>&nbsp;&nbsp;&nbsp;</div></td>
                          <td>&nbsp;</td>
                          <td colspan="2"><input name="txtothremarks" type="text" class="inputs" id="txtothremarks" style="width:180px;" value="<?=$others_view['remarks']?>" size="30"></td>
                        </tr>
                        <tr>
                          <td height="20" colspan="4" class="style4 style1"><div align="center">
                          <span style="cursor:pointer;"  onClick="location.href='profile_transaction.php?act=empstat&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Employment Status </span> | 
                          <span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=position&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Position </span> | 
						  <?
                          if($vis!="hide"){
						  ?>	
                          <span style="cursor:pointer<?=$vis?>" onClick="location.href='profile_transaction.php?act=branch&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Branch </span> | 
                          <span style="cursor:pointer<?=$vis?>" onClick="location.href='profile_transaction.php?act=payroll&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"> Payroll Related </span> 
                          <span style="cursor:pointer" onClick="location.href='profile_transaction.php?act=empstat&frmRefNo=<?=$refno['refno']?>&empNo=<?=$empNo?>&compCode=<?=$compCode?>'" class="style5" align="right"></span> | 
                          <span style="cursor:pointer<?=$vis?>" onClick="viewDetails('employee_allowance.php?empNo=<?=$_GET['empNo']?>&refNo='+document.getElementById('refno').value+'&controlNo='+document.getElementById('ctrlno').value+'&effectivitydate='+document.getElementById('txtothDate').value,'Add','','contact_list_ajax.php','TSCont',0,0,'txtSrch','cmbSrch')" class="style5" align="right"> Allowance</span>
                          <?
						  }
						  ?>
                          </div></td>
                        </tr>
                        
                      </table></td>
                    </tr>
                    <?}?>
                    
                  </table></td>
              </tr>
		  </TABLE>
		<div id="passLock" style="visibility:hidden;" >
			<TABLE align="center" border="0" width="100%">
				
				<TR>
				  <td align="center"><img src="../../../images/loading.gif" width="120" height="40"></td>
			  </TR>
				<TR>
					<td align="center">
						<font class='cnfrmLbl style6'><strong>Saving</strong></font></td>
				</TR>
			</TABLE>			
		</div>          
	</FORM>
	</BODY>
</HTML>
<script src="../../../includes/validations.js"></script>
<script>
	function empstat() {
		var empInputs = $('frmActionType').serialize(true);
        
        if(empInputs['cmbempstatus'] == 0 && empInputs['txtempstatDate'] == "" && empInputs['cmbempnos'] == 0){
            alert('No changes made to PAF employee status.');
            return false;
        }
		
		if((empInputs['cmbempstatus']=="PR" || empInputs['cmbempstatus']=="CN") && empInputs['txtenddate']==""){
			alert('New End Date is required.');
			return false;	
		}	
		
       // if(empInputs['txtenddate'] != "" && empInputs['cmbempstatus'] != 0 ){
		//	alert('No changes made to end date.');
		//	return false;
        //}
		
		if(empInputs['txtempstatDate'] == ""){
				alert('Effectivity Date is Required.');
				return false;
		}	
		
		if(empInputs['cmbempstatus'] != 0 && empInputs['cmbempstatus'] == empInputs['oldstatus']){
			alert('No changes made to employee status.');
			return false;
        }
		
        if(empInputs['txtenddate'] != "" && empInputs['txtenddate'] == empInputs['oldenddate']){
			alert('No changes made to end date.');
			return false;
        }
			
		params = 'profile_transaction.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmActionType').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			},
			onCreate : function(){
				Dolock();
			},
			onSuccess: function (){
				cnclLockSys();
			}	
		})		
	}
	
	function branch() {
		var empInputs = $('frmActionType').serialize(true);
//        if(empInputs['ctrlno'] == ""){
//            alert('Control No. is Required.');
//            return false;
//        }
		
        if((empInputs['cmbbrgroup'] == 0 || empInputs['cmbbrgroup'] == empInputs['old_payGrp']) && (empInputs['cmbbranch'] == 0 || empInputs['cmbbranch'] == empInputs['old_branchCode'])){
            alert('No changes made.');
            return false;
        }
		if(empInputs['txtbrDate'] == ""){
				alert('Effectivity Date is Required.');
				return false;
		}
/*		if(empInputs['brtag'] == "1"){
			if(empInputs['cmbbrstatus'] != "R"){
				alert("Status is must be 'Released'.");
				return false;
			}
			var todayDate = empInputs['today'];
			if (empInputs['txtbrDate'] != todayDate) {
				alert('Effectivity date must be equal to current date.');
				return false;
			}
			var update = confirm('Are you sure you want to update now?');
			if (update == false) {
				return false;
			}			
		}
*/		params = 'profile_transaction.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmActionType').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			},
			onCreate : function(){
				Dolock();
			},
			onSuccess: function (){
				cnclLockSys();
			}	
		})		
	}		
	
	function position() {
		var empInputs = $('frmActionType').serialize(true);
//        if(empInputs['ctrlno'] == ""){
//            alert('Control No. is Required.');
//            return false;
//        }
		
/*        if(empInputs['cmbposition'] == 0 || empInputs['cmbposition'] == empInputs['old_posCode']){
            alert('No changes made.');
            return false;
        }
*/   
        if((empInputs['cmbposition'] == 0 || empInputs['cmbposition'] == empInputs['old_posCode']) && (empInputs['new_secCode'] == empInputs['old_secCode']) && (empInputs['new_deptCode'] == empInputs['old_deptCode'])){
            alert('No changes made.');
            return false;
        }

	    if(empInputs['cmbposstatus'] == 0){
            alert('Status is Required.');
            return false;
        }
		if(empInputs['txtposDate'] == ""){
				alert('Effectivity Date is Required.');
				return false;
		}
/*		if(empInputs['postag'] == "1"){
			if(empInputs['cmbposstatus'] != "R"){
				alert("Status is must be 'Released'.");
				return false;
			}
			var todayDate = empInputs['today'];
			if (empInputs['txtposDate'] != todayDate) {
				alert('Effectivity date must be equal to current date.');
				return false;
			}
			var update = confirm('Are you sure you want to update now?');
			if (update == false) {
				return false;
			}			
		}
*/		params = 'profile_transaction.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmActionType').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			},
			onCreate : function(){
				Dolock();
			},
			onSuccess: function (){
				cnclLockSys();
			}	
		})		
	}	
	
	
	function payroll() {
		var empInputs = $('frmActionType').serialize(true);
//        if(empInputs['ctrlno'] == ""){
//            alert('Control No. is Required.');
//            return false;
//        }
       
	   	if (checkpayroll_inputs() == 0) {
			alert('No Changes made.');
            return false;		
		}
		if(empInputs['txtsalary'] !=0) {
			var new_sal=new Number(empInputs['txtsalary'].replace(',',''));
			var old_sal=new Number(empInputs['oldmrate']);
			if (new_sal < old_sal) {
				var sal_ans = confirm('New rate is less than the old rate. Do you want to continue?');
				if (sal_ans == false) {
					$('txtsalary').focus();
					$('txtsalary').select();
					return false;		
				} 
			}
		}	
		if (empInputs['chAcctNo'] == "1") {
			alert('Account No. is already used.');
			$('txtbankaccount').focus();
			$('txtbankaccount').select();
            return false;		
		}        
		if(empInputs['txtprDate'] == ""){
				alert('Effectivity Date is Required.');
				return false;
		}
		
	   if(empInputs['taxstat']!="O"){
		   if(empInputs['cmbexemption']!=0 && empInputs['cmbexemption']!=empInputs['oldteu']){
				$('taxstat').value="P";
		   }
		   else if(empInputs['cmbexemption']!=0 && empInputs['cmbexemption']==empInputs['oldteu']){
				alert('Old TAX Exemption is equal to New Tax Exemption.');
				$('cmbexemption').value="";
				$('cmbexemption').focus();
				$('taxstat').value="";
				return false;   
		   }
	   }
	   if(empInputs['taxstat']=="O"){
		   if(empInputs['cmbexemption']==0){
		    	alert('You are required to change TAX Exemption Status.');
				$('cmbexemption').focus();
				return false;	   	   
		   }
	   }
/*		if(empInputs['prtag'] == "1"){
			if(empInputs['cmbprstatus'] != "R"){
				alert("Status is must be 'Released'.");
				return false;
			}
			var todayDate = empInputs['today'];
			if (empInputs['txtprDate'] != todayDate) {
				alert('Effectivity date must be equal to current date.');
				return false;
			}
			var update = confirm('Are you sure you want to update now?');
			if (update == false) {
				return false;
			}			
		}*/
	
		params = 'profile_transaction.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmActionType').serialize(),
			onComplete : function (req){
				eval(req.responseText);
				
			},
			onCreate : function(){
				Dolock();
			},
			onSuccess: function (){
				cnclLockSys();
			}	
		});
	}			
	
	
	function others() {
		var empInputs = $('frmActionType').serialize(true);
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2})$/;
		//var sssExp     = /[0-9]{10}/;
		var sssExp     = /^[\d]{2,2}\-[\d]{7,7}\-[\d]{1,1}$/;
		var tinExp     = /^[0-9]{3,3}-[0-9]{3,3}-[0-9]{3,3}$/;
		var numericExp = /[0-9]+/;
//        if(empInputs['ctrlno'] == ""){
//            alert('Control No. is Required.');
//            return false;
//        }
		if (checkothersinputs() == 0) {
			alert('No changes made used');
			return false;
		}

       //add
	   if(empInputs['taxstat']=="" && empInputs['transtype']!="E"){
			if(trim(empInputs['txtlname'])!="" && empInputs['txtlname']!=empInputs['old_txtlname']&& empInputs['old_txtCStat']=="SG" && empInputs['txtgender']=="F"){
				if(empInputs['cmbstatus']==0){
					var askerlname=confirm('You have changed the Lastname of the employee. Would you like to change the civil status?');
					if(askerlname==true ){
						$('taxstat').value="O";
						$('cmbstatus').focus();
						return false;
					}
				}
			}
			
			else if(trim(empInputs['txtlname'])==""){
				if(empInputs['cmbstatus']!=0 && empInputs['cmbstatus']!=empInputs['old_txtCStat'] && empInputs['cmbstatus']=="ME" && empInputs['old_txtCStat']=="SG" && empInputs['txtgender']=="F"){
					var askercs=confirm('You have changed the civil status of the employee. Woul you like to change the lastname?');
					if(askercs==true){
							$('taxstat').value="O";
							$('txtlname').focus();
							return false;
					}
				}
			}
	   }
	   if(empInputs['taxstat']=="P" && empInputs['transtype']!="E"){
			if(trim(empInputs['txtlname'])!="" && empInputs['txtlname']!=empInputs['old_txtlname']&& empInputs['old_txtCStat']=="SG" && empInputs['txtgender']=="F"){
				if(empInputs['cmbstatus']==0){
					var askerlname=confirm('You have changed the Lastname of the employee. Would you like to change the civil status?');
					if(askerlname==true ){
						$('taxstat').value="O";
						$('cmbstatus').focus();
						return false;
					}
				}
			}
			
			else if(trim(empInputs['txtlname'])==""){
				if(empInputs['cmbstatus']!=0 && empInputs['cmbstatus']!=empInputs['old_txtCStat'] && empInputs['cmbstatus']=="ME" && empInputs['old_txtCStat']=="SG" && empInputs['txtgender']=="F"){
					var askercs=confirm('You have changed the civil status of the employee. Woul you like to change the lastname?');
					if(askercs==true){
							$('taxstat').value="O";
							$('txtlname').focus();
							return false;
					}
				}
			}
	   }
	   
		if(empInputs['taxstat']=="" && empInputs['transtype']!="A"){
			if(empInputs['cmbstatus']==empInputs['old_txtCStat']){
				alert('New civil status must not be equal to old status.');
				$('cmbstatus').focus();
				return false;	
			}
			if(empInputs['txtlname']==empInputs['old_txtlname']){
				alert('New lastname must not be equal to old lastname.');
				$('txtlname').focus();
				return false;	
			}			
		}
		if(empInputs['taxstat']!=""){
			if(empInputs['cmbstatus']==0){
				alert('Civil Status is Required.');
				$('cmbstatus').focus();
				return false;	
			}
			if(empInputs['cmbstatus']=="SG"){
				alert('Please change Civil Status to Married.');
				$('cmbstatus').focus();
				return false;
			}
			if(empInputs['cmbstatus']!=0 && empInputs['txtlname']==""){
				alert('Please update Lastname of the employee.');
				$('txtlname').focus();
				return false;	
			}
			if(empInputs['txtlname']==empInputs['old_txtlname']){
				alert('Please update Lastname of the employee.');
				$('txtlname').focus();
				return false;	
			}
		}
		
        if(trim(empInputs['txtsss']) != ""){
			if(!empInputs['txtsss'].match(sssExp)){
				alert('Invalid SSS No.\nvalid : 1212345671');
				$('txtsss').focus();
				$('txtsss').select();
				return false;
			}
		}	
		
        if(trim(empInputs['chsss']) == "1"){
			alert('SSS No. is already used');
			$('txtsss').focus();
			$('txtsss').select();
			return false;
		}        

        if(trim(empInputs['chsss']) == "2"){
			alert('SSS No. is blacklisted');
			$('txtsss').focus();
			$('txtsss').select();
			return false;
		}
		
		if(trim(empInputs['txtphilhealth']) != ""){
			if(!empInputs['txtphilhealth'].match(numericExp)){
				alert('Invalid Phil Health No.\nvalid : Numbers Only');
				$('txtphilhealth').focus();
				$('txtphilhealth').select();
				return false;			
			}
		}		
		if(trim(empInputs['chphilhealth']) == "1"){
			alert('Phil Health No. is already used.');
			$('txtphilhealth').focus();
			$('txtphilhealth').select();
			return false;
		}		
        if(trim(empInputs['txttax']) != ""){
			if(!empInputs['txttax'].match(tinExp)){
				alert('Invalid Tax ID No.\nvalid : 123-123-123');
				$('txttax').focus();
				$('txttax').select();
				return false;			
			}	
		}		
		if(trim(empInputs['chtaxid']) == "1"){
			alert('Tax ID No. is already used.');
			$('txttax').focus();
			$('txttax').select();
			return false;
		}	
		if(trim(empInputs['txthdmf']) != ""){
			if(!empInputs['txthdmf'].match(numericExp)){
				alert('Invalid HDMF No.\nvalid : Numbers Only');
				$('txthdmf').focus();
				$('txthdmf').select();
				return false;			
			}
		}		
		if(trim(empInputs['chhdmf']) == "1"){
            alert('HDMF No. is already used.');
            $('txthdmf').focus();
            $('txthdmf').select();
			return false;
        }			
        if(empInputs['cmbothstatus'] == 0){
            alert('Status is Required.');
            return false;
        }
        if(empInputs['txtothDate'] == ""){
            alert('Effectivity Date is Required.');
            return false;
        }
/*		if(empInputs['othtag'] == "1"){
			if(empInputs['cmbothstatus'] != "R"){
				alert("Status is must be 'Released'.");
				return false;
			}
			var todayDate = empInputs['today'];
			if (empInputs['txtothDate'] != todayDate) {
				alert('Effectivity date must be equal to current date.');
				return false;
			}
			var update = confirm('Are you sure you want to update now?');
			if (update == false) {
				return false;
			}			
		}*/		
		params = 'profile_transaction.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmActionType').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			},
			onCreate : function(){
				Dolock();
			},
			onSuccess: function (){
				cnclLockSys();
			}	
		})		
	}	
		
	function valDate(valStart,idStart,valEnd) {
		var todayDate = new Date();
		var parseStart = Date.parse(valStart);
		var parseEnd = Date.parse(valEnd);
		var parseTodayDate = Date.parse(todayDate);
		
		if(parseStart > parseEnd) {
			alert("Invalid Date.");
			document.getElementById(idStart).value="";
			return false;
		}
	}
	
	function checkECOLA(){
		var empInputs = $('frmActionType').serialize(true);
		if (empInputs['txtdailyrate'] != "0.00") {
			params = 'profile_transaction.php?code=ecola&empNo='+empInputs['empNo']+'&compCode='+empInputs['compCode']+'&drate='+empInputs['txtdailyrate'];
			new Ajax.Request(params,{
				method : 'get',
				onComplete : function (req){
				eval(req.responseText);
				}	
			});
		}
	}
	
	function getposid(){
		var empInputs = $('frmActionType').serialize(true);
		if (empInputs['cmbposition'] != 0) {
			params = 'profile_transaction.php?code=getposid&compCode='+empInputs['compCode']+'&cmbposition='+empInputs['cmbposition'];
			new Ajax.Request(params,{
				method : 'get',
/*				parameters : $('frmActionType').serialize(),				
*/				onComplete : function (req){
				eval(req.responseText);
				}	
			});
		} else {
			$('dvdiv').innerHTML = '';
			$('dvdept').innerHTML = '';
			$('dvsec').innerHTML = '';
			$('dvrank').innerHTML = '';
			$('dvlevel').innerHTML = '';		
		}
		
	}	

	function round_number(){
		var empInputs = $('frmActionType').serialize(true);
		if (empInputs['txtsalary'] != 0) {
			params = 'profile_transaction.php?code=round_number&txtsalary='+empInputs['txtsalary']+'&txtdailyrate='+empInputs['txtdailyrate']+'&txthourlyrate='+empInputs['txthourlyrate'];
			new Ajax.Request(params,{
				method : 'get',
				onComplete : function (req){
				eval(req.responseText);
				}	
			});
		} 
	}
	
//	function checkempstat(){
//		var empInputs = $('frmActionType').serialize(true);		
//		var ch=0;	
//		if(empInputs['cmbempstatus'] != 0 && empInputs['cmbempstatus'] != empInputs['oldstatus']){
//			ch=1;
//        }
//        if(empInputs['txtenddate'] != "" && empInputs['txtenddate'] != empInputs['oldenddate']){
//			ch=1;
//        }
//        if(empInputs['cmbempnos'] != 0 && empInputs['cmbempnos'] != empInputs['oldnos']){
//			ch=1;
//        }
//		return ch;
//	}
	
	function checkpayroll_inputs() {
		var empInputs = $('frmActionType').serialize(true);		
		var ch=0;
		if(empInputs['cmbpstatus'] != 0 && empInputs['cmbpstatus'] != empInputs['oldpayrollstatus']){
			ch=1;
        }
		if(empInputs['txtsalary'] != 0 && empInputs['txtsalary'] != empInputs['oldmrate']){
			ch=1;
        }
		if(empInputs['txtaction']=="E"){
			ch=1;
		}
		else{	
			if(empInputs['cmbexemption'] != 0 && empInputs['cmbexemption'] != empInputs['oldteu']){
				ch=1;
			}	
		}
		if(empInputs['cmbbank'] != 0 && empInputs['cmbbank'] != empInputs['oldbank']){
			ch=1;
        }
		if(empInputs['txtbankaccount'] != "" && empInputs['txtbankaccount'] != empInputs['oldaccountno']){
			ch=1;
        }
		if(empInputs['cmbgroup'] != 0 && empInputs['cmbgroup'] != empInputs['oldpaygroup']){
			ch=1;
        }
		if(empInputs['cmbCategory'] != 0 && empInputs['cmbCategory'] != empInputs['oldpaycat']){
			ch=1;
        }
		return ch;
	}
	
	function checkothersinputs() {
		var empInputs = $('frmActionType').serialize(true);		
		var ch=0;
		if(empInputs['txtlname'] != "" && empInputs['txtlname'] != empInputs['old_txtlname']){
			ch=1;
        }
		if(empInputs['txtfname'] != "" && empInputs['txtfname'] != empInputs['old_txtfname']){
			ch=1;
        }
		if(empInputs['txtmname'] != "" && empInputs['txtmname'] != empInputs['old_txtmname']){
			ch=1;
        }	
			if(empInputs['txtbdyDate'] != "" && empInputs['txtbdyDate'] != empInputs['old_bdy']){
			ch=1;
        }
		if(empInputs['txtBioNum'] != "" && empInputs['txtBioNum'] != empInputs['old_txtBioNum']){
			ch=1;
        }	

		if(empInputs['txtadd1'] != "" && empInputs['txtadd1'] != empInputs['old_txtadd1']){
			ch=1;
        }
		if(empInputs['txtadd2'] != "" && empInputs['txtadd2'] != empInputs['old_txtadd2']){
			ch=1;
        }
		if(empInputs['cmbProvince'] != 0 && empInputs['cmbProvince'] != empInputs['old_cmbcity']){
			ch=1;
        }
		if(empInputs['old_municipality'] !=0 && empInputs['old_municipality'] != empInputs['old_municipality']){
			ch=1;
		}
		if(empInputs['txtsss'] != "" && empInputs['txtsss'] != empInputs['old_txtsss']){
			ch=1;
        }
		if(empInputs['txtphilhealth'] != "" && empInputs['txtphilhealth'] != empInputs['old_txtphilhealth']){
			ch=1;
        }
		if(empInputs['txttax'] != "" && empInputs['txttax'] != empInputs['old_txttax']){
			ch=1;
        }
		if(empInputs['txthdmf'] != "" && empInputs['txthdmf'] != empInputs['old_txthdmf']){
			ch=1;
        }
		if(empInputs['cmbstatus'] != 0 && empInputs['cmbstatus'] != empInputs['old_txtCStat']){
			ch=1;
        }
		if(empInputs['txtContactPerson'] != "" && empInputs['txtContactPerson'] != empInputs['old_contactPerson']){
			ch=1;
        }
		if(empInputs['txtContactNumber'] != "" && empInputs['txtContactNumber'] != empInputs['old_contactNumber']){
			ch=1;
        }
		if(empInputs['cmbbloodtype'] != 0 && empInputs['cmbbloodtype'] != empInputs['old_bloodType']){
			ch=1;
        }
		<?
		if($_SESSION['user_telcoaccess']=="Y"){
		?>
		var sun_0 = document.getElementById('chkSun_0');
		var sun_1 = document.getElementById('chkSun_1');
		if(sun_0.checked == true && empInputs['chkSun_0'] != empInputs['old_sunline']){
			ch=1;
        }
		if(sun_1.checked == true && empInputs['chkSun_1'] != empInputs['old_sunline']){
			ch=1;
        }
		var globe_0 = document.getElementById('chkGlobe_0');
		var globe_1 = document.getElementById('chkGlobe_1');
		if(globe_0.checked == true && empInputs['chkGlobe_0'] != empInputs['old_globeline']){
			ch=1;
        }
		if(globe_1.checked == true && empInputs['chkGlobe_1'] != empInputs['old_globeline']){
			ch=1;
        }
		var smart_0 = document.getElementById('chkSmart_0');
		var smart_1 = document.getElementById('chkSmart_1');
		if(smart_0.checked == true && empInputs['chkSmart_0'] != empInputs['old_smartline']){
			ch=1;
        }
		if(smart_1.checked == true && empInputs['chkSmart_1'] != empInputs['old_smartline']){
			ch=1;
        }
		<?
		}
		?>
		return ch;
	}	
	
	<? if ($act == "empstat") {?>
	Calendar.setup({
			  inputField  : "txtempstatDate",      // ID of the input field
			  ifFormat    : "%m-%d-%Y",          // the date format
			  button      : "imgempstatDate"       // ID of the button
		}
	)
	Calendar.setup({
			  inputField  : "txtenddate",      // ID of the input field
			  ifFormat    : "%m-%d-%Y",          // the date format
			  button      : "imgenddate"       // ID of the button
		}
	)
	<? } elseif  ($act == "payroll") {?>
	Calendar.setup({
			  inputField  : "txtprDate",      // ID of the input field
			  ifFormat    : "%m-%d-%Y",          // the date format
			  button      : "imgprDate"       // ID of the button
		}
	)	
	<? } elseif  ($act == "others") {?>
	Calendar.setup({
			  inputField  : "txtothDate",      // ID of the input field
			  ifFormat    : "%m-%d-%Y",           // the date format
			  button      : "imgothDate"       // ID of the button
		}
	)	
		Calendar.setup({
			  inputField  : "txtbdyDate",      // ID of the input field
			  ifFormat    : "%m-%d-%Y",           // the date format
			  button      : "imgbdyDate"       // ID of the button
		}
	)
	<? } elseif  ($act == "position") {?>
	Calendar.setup({
			  inputField  : "txtposDate",      // ID of the input field
			  ifFormat    : "%m-%d-%Y",          // the date format
			  button      : "imgposDate"       // ID of the button
		}
	)	
	<? } elseif  ($act == "branch") {?>
	Calendar.setup({
			  inputField  : "txtbrDate",      // ID of the input field
			  ifFormat    : "%m-%d-%Y",           // the date format
			  button      : "imgbrDate"       // ID of the button
		}
	)	
	<?}?>
	function AcctFormat(event) {
		<?
		$sqlPayBank = "Select * from tblPayBank where compCode='{$_SESSION['company_code']}'";
		$resBank = $maintEmpObj->getArrRes($maintEmpObj->execQry($sqlPayBank));
		echo "switch($('cmbbank').value) {\n";

		foreach ($resBank as $valBank) {
				echo "case '{$valBank['bankCd']}':\n";
				echo "return dFilter(event.keyCode, $('txtbankaccount'), '{$valBank['mask']}');\n";
				echo "break;\n";
		}
		echo "}";
		?>	
	}	
	function computePAFRates(Rate,compcode,cat,event){
	  $('Save').disabled = true;
	  if (window.event)
		key = window.event.keyCode;
	  else if (event)
		key = event.which;
	  else
	  	return true
		
		if(key == 13){
			params = 'profile.obj.php?code=cdsalary&Rate='+Rate+'&compcode='+compcode+'&cat='+cat;
			new Ajax.Request(params,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);
					
				},
				onCreate : function(){
					$('Save').disabled = true ;
				},
				onSuccess: function (){
					$('Save').disabled = false ;
				}	
			})
		}
	}	
	
	function changeBrnGrp()
	{
		var empInputs = $('frmActionType').serialize(true);
		if (empInputs['cmbbranch'] != "0") {
			params = 'profile_transaction.php?code=getBranchPayGrp&empNo='+empInputs['empNo']+'&compCode='+empInputs['compCode']+'&empSelBranch='+empInputs['cmbbranch'];
			new Ajax.Request(params,{
				method : 'get',
				onComplete : function (req){
				eval(req.responseText);
				}	
			});
		}
	}	
	
	function popProvince(provcd){
		new Ajax.Request(
		'profile_transaction.php?action=loadMunicipality&provcd='+provcd,
		{
		asynchronous : true,
		onComplete : function(req){
			$('divMunicipality').innerHTML=req.responseText;	
			}	
		}
		);
	}

	function alertStatus(){
		var empInputs=$('frmActionType').serialize(true);
		if(empInputs['taxstat']=="O"){
			if(empInputs['cmbexemption']==empInputs['oldteu']){
				alert('The Tax Exemption you select is equal to old Tax Exemption! Please select other Tax Exemption.');
				$('cmbexemption').value=0;
				$('cmbexemption').focus();
				return false;	
			}	
		}
		
	
	}
	
	function setNOS(id){
		var empInputs=$('frmActionType').serialize(true);
		if(empInputs['cmbempstatus']==""){
			$('cmbempnos').disabled=false;	
			$('imgenddate').style.visibility='visible';
			$('txtenddate').value='';				
		}	
		else{
			$('cmbempnos').disabled=true;	
			$('cmbempnos').value=0;	
			if(empInputs['cmbempstatus']=="RG"){
				$('imgenddate').style.visibility='hidden';
				$('txtenddate').value='';			
			}
			else{
				$('imgenddate').style.visibility='visible';
				$('txtenddate').value='';				
			}
		}
	}
	
	function setEMP(id){
		var empInputs=$('frmActionType').serialize(true);
		if(empInputs['cmbempnos']==0){
			$('cmbempstatus').disabled=false;	
			$('imgenddate').style.visibility='visible';
			$('txtenddate').value='';	
		}	
		else{
			$('cmbempstatus').disabled=true;	
			$('cmbempstatus').value=0;	
			$('imgenddate').style.visibility='hidden';
			$('txtenddate').value='';
		}
	}
	
</script>