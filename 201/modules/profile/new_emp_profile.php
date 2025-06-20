<?
session_start();
include("new_emp.obj.php");
include("../../../includes/pager.inc.php");
include("profile_userdef.obj.php");
include("profile_content6.obj.php");

$mainUserDefObjObj = new  mainUserDefObj();
$maintEmpObj = new ProfileObj();
$mainContent6Obj = new  mainContent6();

unset($_SESSION['strprofile']);
if ($_SESSION['strprofile']=="") {
	$_SESSION['strprofile']=$maintEmpObj->createstrwil();
}

if ($_GET['act']=="Edit" || $_GET['act']=="View") {
	$payGrp = $maintEmpObj->getProcGrp();
	$_SESSION['oldcompCode']=$_GET['compCode'];
	$maintEmpObj->oldcompCode=$_SESSION['oldcompCode'];
	$maintEmpObj->viewprofile($_GET['empNo']);
	$_SESSION['strprofile']=$_GET['empNo'];
	$_SESSION['empRestDay']=$maintEmpObj->RestDay;
	$disablematstatus="";
	if ($maintEmpObj->maritalStat=="SG") {
		$disablematstatus="disabled";
	}
	
	// echo $_SESSION['user_payCat'];
	// echo "<br>";
	// echo $maintEmpObj->paycat;
	// if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
	// 	if ($maintEmpObj->paycat == 1) 
	// 		$visible = "visibility:hidden;";
	// }
} else {
	unset($_SESSION['oldcompCode']);
}

if($_GET['act']=="Add")
	$maintEmpObj->picture = 'profile.png';

// Base URL generation
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$projectFolder = "/pamana-water-corp-hris";
$imageUrl = $protocol . $host . $projectFolder . "/images/Employee Picture/";
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . 'pamana-water-corp-hris/images/Employee Picture/';

if($_GET["action"] == "updateSalary") {
	$maintEmpObj->branch  	 = (isset($_POST['cmbbranch'])) ? $_POST['cmbbranch'] : 0;
	$maintEmpObj->empNo = (isset($_POST['txtempNo'])) ? $_POST['txtempNo'] : "";
	$maintEmpObj->compCode = 1; 
	$maintEmpObj->Salary = (isset($_POST['txtsalary'])) ? $_POST['txtsalary'] : "";
	$maintEmpObj->bank       = (isset($_POST['cmbbank'])) ? $_POST['cmbbank'] : 0;
	$maintEmpObj->bankAcctNo = (isset($_POST['txtbankaccount'])) ? $_POST['txtbankaccount'] : "";

	if(empty($maintEmpObj->empNo) || $maintEmpObj->empNo == "") {
		echo "alert('No record to be saved.');";
		exit();
	}

	$stmt = $maintEmpObj->updateSalary();
	if($stmt==true) {
		echo "alert('Record sucessfully updated.');";
		echo "window.location.href = 'new_emp_list.php'";
	}
	else
		echo "alert('Error while updating the record.');";
	exit();
}

if ($_POST['save']!="") {
	//General Tab	
	$maintEmpObj->empNo   	 = (isset($_POST['txtempNo'])) ? $_POST['txtempNo'] : "";
	$maintEmpObj->bio		 = (isset($_POST['txtbio'])) ? $_POST['txtbio'] : "";
	$maintEmpObj->compCode   = (isset($_POST['cmbcompny'])) ? $_POST['cmbcompny'] : 0;
	$maintEmpObj->lName      = (isset($_POST['txtlname'])) ? $_POST['txtlname'] : "";
	$maintEmpObj->fName	   	 = (isset($_POST['txtfname'])) ? $_POST['txtfname'] : "";
	$maintEmpObj->mName      = (isset($_POST['txtmname'])) ? $_POST['txtmname'] : "";
	$maintEmpObj->branch  	 = (isset($_POST['cmbbranch'])) ? $_POST['cmbbranch'] : 0;
	$maintEmpObj->location   = (isset($_POST['cmbbranch'])) ? $_POST['cmbbranch'] : 0;

	//Contact Tab
	$maintEmpObj->Addr1	 		= (isset($_POST['txtadd1'])) ? $_POST['txtadd1'] : "";
	$maintEmpObj->Addr2	 		= (isset($_POST['txtadd2'])) ? $_POST['txtadd2'] : "";
	//$maintEmpObj->Addr3	 = (isset($_POST['txtadd3'])) ? $_POST['txtadd3'] : "";
	//$maintEmpObj->City   = (isset($_POST['cmbcity'])) ? $_POST['cmbcity'] : 0;
	$maintEmpObj->provinceCd	=	(isset($_POST['cmbProvince'])) ? $_POST['cmbProvince'] : 0;
	$maintEmpObj->Municipality	=	(isset($_POST['cmbMunicipality'])) ? $_POST['cmbMunicipality'] : 0;
	$maintEmpObj->ECPerson		=	(isset($_POST['txtECPerson'])) ? $_POST['txtECPerson'] : "";
	$maintEmpObj->ECNumber		=	(isset($_POST['txtECNumber'])) ? $_POST['txtECNumber'] : "";
 	
	//Personal Tab
	$maintEmpObj->sex	   	 = (isset($_POST['cmbgender'])) ? $_POST['cmbgender'] : 0;
	$maintEmpObj->NickName	 = (isset($_POST['txtnickname'])) ? $_POST['txtnickname'] : "";
	$maintEmpObj->Bplace	 = (isset($_POST['txtbplace'])) ? $_POST['txtbplace'] : "";
	$maintEmpObj->dateOfBirth= (isset($_POST['txtBDay'])) ? date('Y-m-d', strtotime(str_replace("-", "/", $_POST['txtBDay']))) : date('Y-m-d');
	$maintEmpObj->maritalStat= (isset($_POST['cmbmaritalstatus'])) ? $_POST['cmbmaritalstatus'] : 0;
	$maintEmpObj->Height	 = (isset($_POST['txtheight'])) ? $_POST['txtheight'] : "";
	$maintEmpObj->Weight	 = (isset($_POST['txtweight'])) ? $_POST['txtweight'] : "";
	$maintEmpObj->CitizenCd	 = (isset($_POST['cmbcitizenship'])) ? $_POST['cmbcitizenship'] : 0;
	$maintEmpObj->Religion   = (isset($_POST['cmbreligion'])) ? $_POST['cmbreligion'] : 0;
	$maintEmpObj->BloodType  = (isset($_POST['cmbbloodtype'])) ? $_POST['cmbbloodtype'] : 0;
	$maintEmpObj->Salary	 = (isset($_POST['txtsalary'])) ? $_POST['txtsalary'] : "";
	$maintEmpObj->PStatus    = (isset($_POST['txtratemode'])) ? $_POST['txtratemode'] : "";
	$maintEmpObj->Exemption	 = (isset($_POST['cmbexemption'])) ? $_POST['cmbexemption'] : "";
	$maintEmpObj->Release	 = (isset($_POST['cmbrelease'])) ? $_POST['cmbrelease'] : 0;
	$maintEmpObj->Group 	 = (isset($_POST['cmbgroup'])) ? $_POST['cmbgroup'] : 0;
	$maintEmpObj->paycat 	 = (isset($_POST['txtcat'])) ? $_POST['txtcat'] : "";
	$maintEmpObj->oldcompCode=$_SESSION['oldcompCode'];
	$maintEmpObj->strprofile =$_SESSION['strprofile'];
	$maintEmpObj->bank       = (isset($_POST['cmbbank'])) ? $_POST['cmbbank'] : 0;
	$maintEmpObj->bankAcctNo = (isset($_POST['txtbankaccount'])) ? $_POST['txtbankaccount'] : "";
	$maintEmpObj->SSS		 = (isset($_POST['txtsss'])) ? $_POST['txtsss'] : "";
	$maintEmpObj->PhilHealth = (isset($_POST['txtphilhealth'])) ? $_POST['txtphilhealth'] : "";
	$maintEmpObj->TIN		 = (isset($_POST['txttax'])) ? $_POST['txttax'] : "";
	$maintEmpObj->HDMF		 = (isset($_POST['txthdmf'])) ? $_POST['txthdmf'] : "";
	$maintEmpObj->Drate		 = (isset($_POST['txtdailyrate'])) ? $_POST['txtdailyrate'] : "";
	//ID No. Tab
	
	//Employment Tab
	$maintEmpObj->position	 = (isset($_POST['cmbposition'])) ? $_POST['cmbposition'] : 0;
	$maintEmpObj->divCode 	= (isset($_POST['txtDiv'])) ? $_POST['txtDiv'] : 0;
	$maintEmpObj->DepCode 	= (isset($_POST['txtDept'])) ? $_POST['txtDept'] : 0;
	$maintEmpObj->secCode 	= (isset($_POST['txtSect'])) ? $_POST['txtSect'] : 0;
	$maintEmpObj->RestDay 	= (isset($_SESSION['empRestDay'])) ? $_SESSION['empRestDay'] : "";
	$maintEmpObj->empRank	= (isset($_POST['txtRank'])) ? $_POST['txtRank'] : 0;
	$maintEmpObj->level	 	 = (isset($_POST['txtLevel'])) ? $_POST['txtLevel'] : 0;
	$maintEmpObj->Status	= (isset($_POST['cmbstatus'])) ? $_POST['cmbstatus'] : 0;
	$maintEmpObj->Effectivity = (isset($_POST['txtEffDate']) && strtotime(str_replace("-", "/", $_POST['txtEffDate'])) !== false) ? date('Y-m-d', strtotime(str_replace("-", "/", $_POST['txtEffDate']))) : "";
	$maintEmpObj->Regularization 	= (isset($_POST['txtRegDate']) && strtotime(str_replace("-", "/", $_POST['txtRegDate'])) !== false) ? date('Y-m-d', strtotime(str_replace("-", "/", $_POST['txtRegDate']))) : "";
	$maintEmpObj->EndDate 			= (isset($_POST['txtEndDate']) && strtotime(str_replace("-", "/", $_POST['txtEndDate'])) !== false) ? date('Y-m-d', strtotime(str_replace("-", "/", $_POST['txtEndDate']))) : "";
	$maintEmpObj->RSDate 			= (isset($_POST['txtRSDate']) && strtotime(str_replace("-", "/", $_POST['txtRSDate'])) !== false) ? date('Y-m-d', strtotime(str_replace("-", "/", $_POST['txtRSDate']))) : "";
	$maintEmpObj->prevtag   = (isset($_POST['chprev'])) ? $_POST['chprev'] : "";
	$maintEmpObj->empSunLine = (isset($_POST['chkSun'])) ? $_POST['chkSun'] : "";
	$maintEmpObj->empGlobeLine = (isset($_POST['chkGlobe'])) ? $_POST['chkGlobe'] : "";
	$maintEmpObj->empSmartLine = (isset($_POST['chkSmart'])) ? $_POST['chkSmart'] : "";
	$maintEmpObj->empRegion = (isset($_POST['cmbRegion'])) ? $_POST['cmbRegion'] : "";
	$maintEmpObj->empZipCode = (isset($_POST['txtZipCode'])) ? $_POST['txtZipCode'] : "";
	$maintEmpObj->empECNumber2 = (isset($_POST['txtECNumber2'])) ? $_POST['txtECNumber2'] : "";
	$maintEmpObj->empECRelation = (isset($_POST['txtRelationship'])) ? $_POST['txtRelationship'] : "";
	if(isset($_FILES['file-input']) && $_FILES['file-input']['error'] === UPLOAD_ERR_OK && !empty($_FILES['file-input']['name'])) {
		//die(var_dump($_FILES['file-input']));
		$fileExtension = strtolower(pathinfo($_FILES["file-input"]["name"], PATHINFO_EXTENSION));
		$fileName = time() . "." . $fileExtension;
		$targetFilePath = $uploadDir . $fileName;
		// Check if file is an image
		$check = getimagesize($_FILES["file-input"]["tmp_name"]);
		if ($check === false) {
			echo "alert('File is not an image.');";
			exit;
		}
	
		// Allow only specific file formats
		$allowedTypes = array("jpg", "jpeg", "png", "gif");
		if (!in_array($fileExtension, $allowedTypes)) {
			echo "alert('Only JPG, JPEG, PNG, and GIF files are allowed.')";
			exit;
		}
	
		// Move the uploaded file to the target folder
		if (move_uploaded_file($_FILES["file-input"]["tmp_name"], $targetFilePath)) {
			$maintEmpObj->picture = $fileName; 
		} else {
			echo "alert('Sorry, there was an error uploading your file.')";
			exit;
		}
	}
	//Payroll Tab

	if ($_GET['act']=="Add") {
		if ($_POST['chRelease'] == "") {
			$maintEmpObj->addEmployee('tblEmpMast_new');	
		} else {
			$maintEmpObj->addEmployee('tblEmpMast_new');
			$maintEmpObj->releaseEmp($maintEmpObj->empNo,$maintEmpObj->compCode);	
			$maintEmpObj->releaseAllowance($maintEmpObj->empNo,$maintEmpObj->compCode);
			$maintEmpObj->updateAllowance($maintEmpObj->empNo,$maintEmpObj->compCode);
		}
	}
	elseif ($_GET['act']=="Edit") {
		$old_picture = $maintEmpObj->getPicture($_GET['empNo'], $_GET['compCode']);
		//die(var_dump($maintEmpObj));
		//die($maintEmpObj->dateOfBirth);
		if($maintEmpObj->picture !== $old_picture) {
			if($old_picture !== 'profile.png') {
				unlink($uploadDir . $old_picture);
			}
			$maintEmpObj->picture = $fileName;
		}
		if ($_POST['chRelease'] != "") {
			if($maintEmpObj->PhilHealth)
			$maintEmpObj->updateemployee($_GET['empNo'],$_GET['compCode']);	
			$maintEmpObj->releaseEmp($_GET['empNo'],$_GET['compCode']);	
			$maintEmpObj->releaseAllowance($_GET['empNo'],$_GET['compCode']);
			$maintEmpObj->updateAllowance($_GET['empNo'],$_GET['compCode']);
		}
		else{
			$maintEmpObj->updateemployee($_GET['empNo'],$_GET['compCode']);
		}
	}
	unset($_SESSION['strprofile'],$_SESSION['oldcompCode'],$_SESSION['profile_act'],$_SESSION['empRestDay']);
	if($_GET['act']=="Add")
		header("Location: new_emp_list.php");
	else
		header("Location: new_emp_profile.php?act=Edit&empNo={$_GET['empNo']}&compCode={$_GET['compCode']}");
} else {
	unset($_SESSION['empRestDay']);
}

if($_GET["action"]=='deleUserDefinedMast')
{
	$res_DelRecord = $mainUserDefObjObj->del_UserDefMstRec($_GET["recNo"],$_GET['catcode']);
	if($res_DelRecord==true)
		echo "alert('Record was sucessfully deleted.');";
	else	
		echo "alert('Record was unsucessfully deleted.');";
	exit();
}

if($_GET["action"]=='deleNewEmp') {
	$res_DelRecord = $maintEmpObj->delNewEmp($_GET["empNo"],$_GET["compCode"]);
	if($res_DelRecord==true){
		$resEduc=$maintEmpObj->delEmpOtherInfo($_GET["empNo"]," tblEducationalBackground ");
		$resTra=$maintEmpObj->delEmpOtherInfo($_GET["empNo"]," tblTrainings ");
		$resPer=$maintEmpObj->delEmpOtherInfo($_GET["empNo"]," tblPerformance ");
		$resDis=$maintEmpObj->delEmpOtherInfo($_GET["empNo"]," tblDisciplinaryAction ");
		$resEmp=$maintEmpObj->delEmpOtherInfo($_GET["empNo"]," tblEmployeeDataHistory ");
		$resConList=$maintEmpObj->delEmpOtherInfo($_GET["empNo"]," tblContactMast ");
		$resAllow=$maintEmpObj->delEmpOtherInfo($_GET["empNo"]," tblAllowance_New ");
		echo "alert('New Employee was sucessfully deleted.');";		
	}
	else{	
		echo "alert('New Employee was unsucessfully deleted.');";
	}
	exit();
}

if($_GET["action"]=='delPrevEmplr')
{
	$resDelPrevEmplr = $mainContent6Obj ->delPrevEmplr($_GET["seqNo"]);
	if($resDelPrevEmplr==true)
		echo "alert('Record was sucessfully deleted.');";
	else	
		echo "alert('Record was unsucessfully deleted.');";
	exit();
}

if($_GET['action']=="loadMunicipality")
{
	$arrresmun=$maintEmpObj->makearr($maintEmpObj->getMunicipality(" where provinceCd='{$_GET['provcd']}'"),'municipalityCd','municipalityDesc','');
	$maintEmpObj->DropDownMenu($arrresmun,'cmbMunicipality','','class="inputs" style="width:222px";');
	exit();	
}

$view_exempt = array('010000098', '999999999');
$visible = "";
$readisabled = "";
$viewonly = "";
if ($_SESSION['Confiaccess'] !== "Y") {
	$visible = "visibility:hidden;";
} elseif (!in_array($_SESSION['employee_number'], $view_exempt)) {
    $readisabled = "disabled";
    $viewonly = "readonly";
}

// if($_SESSION['user_level'] == 1 || $_SESSION['user_level'] == 2) {
// 	$visible = "";
// 	$readisabled = "";
// 	$viewonly = "";
// }

$disabled="";
$_SESSION['profile_act']=$_GET['act'];
if ($_GET['act']=="View") {
	$disabled="disabled";
}

include("../../../includes/calendar.php");

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
		<STYLE>@import url('../../style/tabs.css');</STYLE>
		<STYLE>@import url('../../../js/themes/alphacube.css');</STYLE>	
        
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<script type="text/javascript"  src="../../../includes/calendar.js"></script>
        <STYLE>@import url('../../../includes/calendar.css');</STYLE>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
        
		<style type="text/css">
        	.headertxt {font-family: verdana; font-size: 11px;}
        </style>        
   	<STYLE>@import url('../../../js/themes/default.css');</STYLE>
	<STYLE>@import url('../../../js/themes/alphacube.css');</STYLE>	
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/datepicker/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
		<link rel="stylesheet" type="text/css" href="../../../includes/datepicker/dhtmlxCalendar/codebase/dhtmlxcalendar.css"></link>
    	<link rel="stylesheet" type="text/css" href="../../../includes/datepicker/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_dhx_skyblue.css"></link>	
    <script type="text/javascript">
    var myCalendar;
    function doOnLoad() {
        myCalendar = new dhtmlXCalendarObject(["txtEffDate","txtEndDate","txtRSDate","txtRegDate","txtBDay"]);
		myCalendar.setDateFormat("%m-%d-%Y");
    }

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
	
        </script>     
	</HEAD>
	<BODY onLoad="focusTab(1);doOnLoad();">
		<FORM name='frmViewEditEmp' id="frmViewEditEmp" action="" method="POST" enctype="multipart/form-data">
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" width="100%">
				<tr>
					
      <td class="parentGridHdr" height="30">&nbsp;<img src="../../../images/grid.png">&nbsp;New Employee Profile :&nbsp; 
      <?
      if($_GET['act']=="Add")
	  {
	  ?>
      <div id="name1" style="color:#FF0000; font-size:12px; width:400px; position:absolute; left:185px; top:19px;"></div>
      <?
	  }
	  else
	  {
	  ?>
      <div id="name1" style="color:#FF0000; font-size:12px; width:400px; position:absolute; left:185px; top:19px;"><?=$maintEmpObj->lName . ", " . $maintEmpObj->fName . " " . $maintEmpObj->mName;?></div>      
      <?
	  }
	  ?>	
        </td>
        
			  </tr>
				<tr>
					<td class="parentGridDtl" style="height: 700px;">
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br><br><br>
				<div id="tab1" class="tab1" onClick="focusTab(1)">General Info</div>
				
              <div id="tab2" class="tab2" onClick="focusTab(2);getname();">Contacts</div>
              <div id="tab3" class="tab3" onClick="focusTab(3);getname();">Employment</div>
              <div id="tab4" class="tab4" onClick="focusTab(4);getname();">Performance</div>              
              <div id="tab5" class="tab5" onClick="focusTab(5);getname();">Employee Profile</div>	
              <div id="tab6" class="tab6" onClick="focusTab(6);getname();">Training</div>
              <div id="tab7" class="tab7" onClick="changeTab(); viewTabEight(); getname();">Other Info</div>
				
          <div id="content1" class="content1" style="height: 620px;">
                    <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="200" valign="top">
               		     <table width="90%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td colspan="3" height="25"></td>
					  </tr>
                        <? if ($_GET['act']=="Add") {
								$notype="1";
							}	
							else {
								$notype="0";
							}	
					//if ($_GET['act']!="Add") {
						?>
					  <tr> 
						<td width="19%" class="headertxt">Bio Series No.</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><input <?=$readisabled?> class='inputs' size="50" type="text" name="txtbio" value="<?=$maintEmpObj->bio?>" onBlur="checkno('bio',this.value,'<?=$notype?>',document.getElementById('cmblocation').value,'dvbio')" id="txtbio" maxlength="50">&nbsp;<span id="dvbio" style="color:#FF0000; font-size:10px"></span><input type="hidden" name="chbio" value="" id="chbio"></td>
					  </tr>   
					  <tr> 
						<td width="19%" class="headertxt">Employee No.</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><input <?=$readisabled?> class='inputs' size="50" type="text" name="txtempNo" value="<?=$maintEmpObj->empNo?>" onBlur="checkno('empNo',this.value,'<?=$notype?>','Emp No.','dvempNo')" id="txtempNo" maxlength="50">&nbsp;<span id="dvempNo" style="color:#FF0000; font-size:10px"></span><input type="hidden" name="chempNo" value="" id="chempNo"></td>
					  </tr>
                      <? //}?>

					  <tr> 
						<td width="19%" class="headertxt">Last Name</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><input class='inputs' size="50" type="text" name="txtlname" value="<?=$maintEmpObj->lName?>" id="txtlname" maxlength="50" <?=$readisabled?>></td>
					  </tr>
					  <tr> 
						<td class="headertxt">First Name</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input value="<?=$maintEmpObj->fName?>" size="50" class='inputs' type="text" name="txtfname" id="txtfname" maxlength="50" <?=$readisabled?>></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Middle Name</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input value="<?=$maintEmpObj->mName?>" size="50" class='inputs' type="text" name="txtmname"  id="txtmname" maxlength="50" <?=$readisabled?>></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Company</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><?
						$salaryamount=$maintEmpObj->Salary;
						//$maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getCompany(''),'compCode','compName',''),'cmbcompny',$maintEmpObj->compCode,'class="inputs" style="width:222px;" onchange="getresult(this.value,\'profile.obj.php\',\'cdpaycat\',\'divpaycat\');getresult(this.value,\'profile.obj.php\',\'cdbranch\',\'divbranch\'); getresult(this.value,\'profile.obj.php\',\'cdshift\',\'dvshift\'); getresult(this.value,\'profile.obj.php\',\'cdposition\',\'dvposition\'); getsalary(this.value,\'profile.obj.php\',\'cdsalarycmb\',\'dvsalary\',\''.$maintEmpObj->Salary.'\'); getsalary(this.value,\'profile.obj.php\',\'cddratecmb\',\'dvdailyrate\',\''.$maintEmpObj->Drate.'\'); getcompany(this.value);"'); 
						$maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getCompany(''),'compCode','compName',''),'cmbcompny',$maintEmpObj->compCode,'class="inputs" style="width:222px;" onchange="getresult(this.value,\'profile.obj.php\',\'cdpaycat\',\'divpaycat\');getresult(this.value,\'profile.obj.php\',\'divbranch\'); getresult(this.value,\'profile.obj.php\',\'cdshift\',\'dvshift\'); getresult(this.value,\'profile.obj.php\',\'cdposition\',\'dvposition\'); getsalary(this.value,\'profile.obj.php\',\'cdsalarycmb\',\'dvsalary\',\''.$maintEmpObj->Salary.'\'); getsalary(this.value,\'profile.obj.php\',\'cddratecmb\',\'dvdailyrate\',\''.$maintEmpObj->Drate.'\'); getcompany(this.value);"' . $readisabled); ?><input type="hidden" value="<?=$maintEmpObj->compCode?>" name="company_code" id="company_code"></td>
					  </tr>
					  <tr> 
						<td class="headertxt" >Branch</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal">
							<div id="divbranch">
						<? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranch($maintEmpObj->compCode),'brnCode','brnDesc',''),'cmbbranch',$maintEmpObj->branch,'class="inputs" style="width:222px;" onchange="loadPayGroup(this.value);"' . $readisabled); ?></div>						</td>
					  </tr>
                      <tr>
						<td class="headertxt" >Picture</td>
						<td class="headertxt">:</td>
                        <td class="gridDtlVal">
							<img id="profile-img" src="<?=$imageUrl . $maintEmpObj->picture?>" alt="profile" height="100px" width="100px" style="border: 1px solid #c9c9c9;" onclick="triggerFileInput()">
							<input type="file" id="file-input" name="file-input" accept="image/*" onchange="previewImage(event)" style="display: none;">
						</td>
                      </tr>
                      <tr>
                        <td class="headertxt"></td>
                        <td class="headertxt"></td>
                        <td class="gridDtlVal"></td>
                      </tr>
                      <tr>
                        <td class="headertxt"></td>
                        <td class="headertxt"></td>
                        <td class="gridDtlVal"></td>
                      </tr>
                      <tr>
                        <td class="headertxt"></td>
                        <td class="headertxt"></td>
                        <td class="gridDtlVal"></td>
                      </tr>                
					</table>
                        </td>
                      </tr>
                    </TABLE>
                </div>
               <div id="content2" class="content2" style="height: 620px;">
                   <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="420" valign="top">
							<table width="100%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td colspan="3" height="25"></td>
						</tr>
					  <tr> 
						<td width="35%" class="headertxt">Home No, Bldg., Street</td>
						<td width="1%" class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><input <?=$readisabled?> value="<?=$maintEmpObj->Addr1?>" size="70" name="txtadd1" type="text" class="inputs" maxlength="150" id="txtadd1" /></td>
						</tr>
					  <tr> 
						<td class="headertxt">Barangay</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><input <?=$readisabled?> value="<?=$maintEmpObj->Addr2?>" size="70" name="txtadd2" type="text" class="inputs" maxlength="150" id="txtadd2" /></td>
						</tr>
					  <tr>
					    <td class="headertxt">Province</td> 
						<td class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><? 
						$arrResProv=$maintEmpObj->makeArr($maintEmpObj->getProvince(),'provinceCd','provinceDesc','');
						$maintEmpObj->DropDownMenu($arrResProv,'cmbProvince',$maintEmpObj->provinceCd,'onChange="popProvince(this.value);" class="inputs" style="width:222px;"' . $readisabled);?>
                      </tr>  
                      <tr>
                      	<td class="headertxt">Municipality/City</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal" valign="top"><div id="divMunicipality"><?  
						$arrresmun=$maintEmpObj->makeArr($maintEmpObj->getMunicipality(),'municipalityCd','municipalityDesc','');
						$maintEmpObj->DropDownMenu($arrresmun,'cmbMunicipality',$maintEmpObj->Municipality,'class="inputs" style="width:222px"' . $readisabled);?></div></td>
                      </tr>
					  <tr> 
						<td class="headertxt">Region</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal" valign="top">
							<select class="inputs" name="cmbRegion" id="cmbRegion" <?php echo $readisabled; ?>>
								<option value=""></option> 
								<option value="NCR" <?php echo ($maintEmpObj->empRegion == "NCR") ? 'selected' : ''; ?>>NCR</option>
								<option value="CAR" <?php echo ($maintEmpObj->empRegion == "CAR") ? 'selected' : ''; ?>>CAR</option>
								<option value="REGION I" <?php echo ($maintEmpObj->empRegion == "REGION I") ? 'selected' : ''; ?>>REGION I</option>
								<option value="REGION II" <?php echo ($maintEmpObj->empRegion == "REGION II") ? 'selected' : ''; ?>>REGION II</option>
								<option value="REGION III" <?php echo ($maintEmpObj->empRegion == "REGION III") ? 'selected' : ''; ?>>REGION III</option> 
								<option value="REGION IV-A" <?php echo ($maintEmpObj->empRegion == "REGION IV-A") ? 'selected' : ''; ?>>REGION IV-A</option>
								<option value="MIMAROPA" <?php echo ($maintEmpObj->empRegion == "MIMAROPA") ? 'selected' : ''; ?>>MIMAROPA</option>
								<option value="REGION V" <?php echo ($maintEmpObj->empRegion == "REGION V") ? 'selected' : ''; ?>>REGION V</option> 
								<option value="REGION VI" <?php echo ($maintEmpObj->empRegion == "REGION VI") ? 'selected' : ''; ?>>REGION VI</option>
								<option value="NIR" <?php echo ($maintEmpObj->empRegion == "NIR") ? 'selected' : ''; ?>>NIR</option>
								<option value="REGION VII" <?php echo ($maintEmpObj->empRegion == "REGION VII") ? 'selected' : ''; ?>>REGION VII</option>
								<option value="REGION VIII" <?php echo ($maintEmpObj->empRegion == "REGION VIII") ? 'selected' : ''; ?>>REGION VIII</option>
								<option value="REGION IX" <?php echo ($maintEmpObj->empRegion == "REGION IX") ? 'selected' : ''; ?>>REGION IX</option>
								<option value="REGION X" <?php echo ($maintEmpObj->empRegion == "REGION X") ? 'selected' : ''; ?>>REGION X</option>
								<option value="REGION XI" <?php echo ($maintEmpObj->empRegion == "REGION XI") ? 'selected' : ''; ?>>REGION XI</option>
								<option value="REGION XII" <?php echo ($maintEmpObj->empRegion == "REGION XII") ? 'selected' : ''; ?>>REGION XII</option>
								<option value="REGION XIII" <?php echo ($maintEmpObj->empRegion == "REGION XIII") ? 'selected' : ''; ?>>REGION XIII</option>
								<option value="BARMM" <?php echo ($maintEmpObj->empRegion == "BARMM") ? 'selected' : ''; ?>>BARMM</option>
							</select>
						</td>
					  </tr>
					  <tr> 
						<td class="headertxt">Zip Code</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><input <?=$readisabled?> value="<?=$maintEmpObj->empZipCode?>" size="10" name="txtZipCode" type="text" class="inputs" maxlength="150" id="txtZipCode" /></td>
					  </tr>
					  <tr>
                      <tr>
                      	<td class="headertxt">In Case of Emergency Contact</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal" valign="top"><input <?=$readisabled?> value="<?=$maintEmpObj->ECPerson;?>" size="50" name="txtECPerson" type="text" class="inputs" maxlength="150"  id="txtECPerson"/></td>
                      </tr>
					  <tr>
                      	<td class="headertxt">Relationship</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal" valign="top"><input <?=$readisabled?> value="<?=$maintEmpObj->empECRelation?>" size="20" name="txtRelationship" type="text" class="inputs" maxlength="150"  id="txtRelationship"/></td>
                      </tr>
                      <tr>
                      	<td class="headertxt">Contact # 1</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal" valign="top"><input <?=$readisabled?> value="<?=$maintEmpObj->ECNumber?>" size="15" name="txtECNumber" type="text" class="inputs" maxlength="15" id="txtECNumber"/></td>
                      </tr>
					  <tr>
                      	<td class="headertxt">Contact # 2</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal" valign="top"><input <?=$readisabled?> value="<?=$maintEmpObj->empECNumber2?>" size="15" name="txtECNumber2" type="text" class="inputs" maxlength="15" id="txtECNumber2"/></td>
                      </tr>
					  <tr>
					    <td colspan="3" height="10" ></td> 
						</tr>
					  <tr>
					    <td colspan="3" >
                        <div id="TSCont"></div>
                        <div id="indicator1" align="center"></div>                        </td> 
						</tr>
					</table>                        </td>
                      </tr>
                    </TABLE>
                </div>
              <div id="content3" class="content3" style="height: 620px;">
<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="300" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="76%" valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="2">
                              <tr>
                                <td colspan="3" height="25"></td>
                              </tr>
                                <tr>
                                  <td class="headertxt">Position</td>
                                  <td class="headertxt">:</td>
                                  <td width="69%" class="gridDtlVal"><div id="dvposition">
                                    <?	
                                                        $poswhere=" and tblPosition.compCode='" . $maintEmpObj->compCode . "'";
                                                        
                                                        $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getpositionmer($poswhere,1),'posCode','pp1',''),'cmbposition',$maintEmpObj->position,'class="inputs" style="width:222px;" onchange="getPosInfo(this.value); getPayCats(this.value);"' . $readisabled);
							$pos = $maintEmpObj->getpositionwil(" and posCode='{$maintEmpObj->position}'",2);
							$Div = $maintEmpObj->getDivDescArt($maintEmpObj->compCode,$maintEmpObj->divCode);
							$Dept = $maintEmpObj->getDeptDescArt($maintEmpObj->compCode, $maintEmpObj->divCode,$maintEmpObj->DepCode);
							$Sect = $maintEmpObj->getSectDescArt($maintEmpObj->compCode, $maintEmpObj->divCode,$maintEmpObj->DepCode,$maintEmpObj->secCode);
														
														 ?>
                                  </div></td>
                                </tr>                              
                              <tr>
                                <td width="29%" class="headertxt">Division</td>
                                <td width="2%" class="headertxt">:</td>
                                <td class="gridDtlVal"><div id='divdivision'><?=$Div['deptDesc']?></div>
								<input class='inputs' maxlength="25" type="hidden" value="<?=$maintEmpObj->divCode?>"  name="txtDiv" id="txtDiv" />                                </td>
                              </tr>
<tr>
                                <td width="29%" class="headertxt">Department</td>
                                <td width="2%" class="headertxt">:</td>
                                <td class="gridDtlVal"><div id='divdpt'><?=$Dept['deptDesc']?>
                                
                                </div>
								<input class='inputs' maxlength="25" type="hidden" value="<?=$maintEmpObj->DepCode?>"  name="txtDept" id="txtDept" />                                </td>
                              </tr>
<tr>
                                <td width="29%" class="headertxt">Section</td>
                                <td width="2%" class="headertxt">:</td>
                                <td class="gridDtlVal"><div id='divsection'><?=$Sect['deptDesc']?>
                                </div>
                                <input class='inputs' maxlength="25" type="hidden" value="<?=$maintEmpObj->secCode?>"  name="txtSect" id="txtSect" />
                                </td>
                              </tr>
                                <tr>
                                  <td class="headertxt">Rank</td>
                                  <td class="headertxt">:</td>
                                  <td class="gridDtlVal"><div id="dvrank"><?
									  $empRank = $maintEmpObj->getRank($maintEmpObj->empRank);
									  echo $empRank['rankDesc'];
								  
								  ?>
                                  </div>
								<input class='inputs' maxlength="25" type="hidden" value="<?=$maintEmpObj->empRank?>"  name="txtRank" id="txtRank" />                                  
                                  </td>
                                </tr>
                                <tr>
                                  <td class="headertxt">Level</td>
                                  <td class="headertxt">:</td>
                                  <td class="gridDtlVal"><div id="dvlevel"><?
								  if ($maintEmpObj->level != '') 
								  	echo 'Level '.$maintEmpObj->level;?></div>
								<input class='inputs' maxlength="25" type="hidden" value="<?=$maintEmpObj->level?>"  name="txtLevel" id="txtLevel" />                                  </td>
                                </tr>                                                            
                              <tr>
                                <td class="headertxt">Employment Status</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu(array("0"=>"",'RG'=>'Regular','PR'=>'Probationary','CN'=>'Contractual'),'cmbstatus',$maintEmpObj->Status,'class="inputs" style="width:222px;"' . $readisabled); ?></td>
                              </tr>
                              <tr>
                                <td class="headertxt">Date Hired</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><input <?=$readisabled?> name="txtEffDate" type="text" class='inputs' id="txtEffDate" value="<?=(!empty($maintEmpObj->Effectivity) && strtotime($maintEmpObj->Effectivity) !== false) ? date('m-d-Y',strtotime($maintEmpObj->Effectivity)) : "";?>" size="15" maxlength="10" readonly/></td>
                              </tr>
                              <tr>
                                <td class="headertxt">Regularization</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><input  <?=$readisabled?>  name="txtRegDate" value="<?=(!empty($maintEmpObj->Regularization) && strtotime($maintEmpObj->Regularization) !== false) ? date('m-d-Y',strtotime($maintEmpObj->Regularization)) : "";?>" type="text" class='inputs' id="txtRegDate"   size="15" maxlength="10" readonly /></td>
                              </tr>
                            <tr>
                            	<td class="headertxt">End Date</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><input  <?=$readisabled?> name="txtEndDate" value="<?=(!empty($maintEmpObj->EndDate) && strtotime($maintEmpObj->EndDate) !== false) ? date('m-d-Y',strtotime($maintEmpObj->EndDate)) : "";?>" type="text" class='inputs' id="txtEndDate"  size="15" maxlength="10" readonly /></td>
                            </tr>
                            <tr>
                            	<td class="headertxt">Resigned Date</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><input <?=$readisabled?> value="<?=(!empty($maintEmpObj->RSDate) && strtotime($maintEmpObj->RSDate) !== false) ? date('m-d-Y', strtotime($maintEmpObj->RSDate)) : "";?>" name="txtRSDate" type="text" class='inputs' id="txtRSDate"  size="15" maxlength="10" readonly /></td>
                            </tr>
                            <tr>
                            	<td class="headertxt">With Previous Employer</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><label>
                                  <input type="radio" name="chprev" value="Y"   <? if ($maintEmpObj->prevtag=="Y") echo "checked"?> <?=$disabled?> <?=$readisabled?> id="chprev" />
                                  Yes</label>
                                    <label>
                                    <input type="radio" name="chprev" <? if ($maintEmpObj->prevtag=="N") echo "checked"?>  value="N" <?=$disabled?> <?=$readisabled?> id="chprev" />
                                      No</label></td>
                            </table></td>
                            <td width="24%" valign="top"></td>
  </tr>
                        </table></td>
                      </tr>
                </TABLE>                     
                </div>
                <div id="content4" class="content4" style="height: 620px;">
                    <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="470" valign="top">
               		     <table width="100%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td colspan="3" height="15"></td>
					  </tr>
                      <tr>
                    	<td colspan="3"><div id="Performance"></div>
                        <div id="indicator3" align="center"></div>
                        </td>
                    </tr>               
					</table>
                        </td>
                      </tr>
                    </TABLE>                
                </div>
              <div id="content5" class="content5" style="height: 620px;">
 					<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
					 
                      <tr> 
                        <td align="left" class="parentGridDtl" height="470" valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="2">
						<tr>
					<? 
						  if($maintEmpObj->Salary !== "" && $maintEmpObj->Salary > 0) {
						  ?>
							  <td colspan="6" style="color:#008000; border:1px solid; text-align:center;"><strong>With Basic Rate</strong></td>
						  <?
						  } else {
						  ?>
							  <td colspan="6" style="color:#FF0000; border:1px solid; text-align:center;"><strong>Basic Rate is Not Yet Encoded</strong></td>
						  <?
						  }
					   ?>
					</tr>
					  <tr> 
						<td colspan="6" height="25"></td>
					  </tr>
					  <tr> 
						<td width="19%" class="headertxt">Gender</td>
						<td width="1%" class="headertxt">:</td>
						<td width="40%" class="gridDtlVal"><? $maintEmpObj->DropDownMenu(array('','M'=>'Male','F'=>'Female'),'cmbgender',$maintEmpObj->sex,'class="inputs" style="width:222px;"' . $readisabled); ?></td>
						<td class="headertxt">SSS</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><input <?=$readisabled?> class='inputs' maxlength="12" type="text" value="<?=$maintEmpObj->SSS?>" onKeyDown="javascript:return dFilter (event.keyCode, this, '##-#######-#');" onBlur="checkno('empSssNo',this.value,'<?=$notype?>','SSS No.','dvsss')"  name="txtsss" id="txtsss" /><span id="dvsss" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chsss" value="" id="chsss"></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Nick Name</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input <?=$readisabled?> class='inputs' maxlength="20" size="33" type="text" value="<?=$maintEmpObj->NickName?>"  name="txtnickname" id="txtnickname" /></td>
						<td class="headertxt">Philhealth</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><input <?=$readisabled?> class='inputs' maxlength="25" type="text" value="<?=$maintEmpObj->PhilHealth?>" onKeyDown="javascript:return dFilter (event.keyCode, this, '##-#########-#');" onBlur="checkno('empPhicNo',this.value,'<?=$notype?>','Philhealth No.','dvphilhealth')" name="txtphilhealth" id="txtphilhealth" /><span id="dvphilhealth" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chphilhealth" value="" id="chphilhealth"></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Birth Place</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input <?=$readisabled?> class='inputs' maxlength="50" size="33" type="text" value="<?=$maintEmpObj->Bplace?>"  name="txtbplace" id="txtbplace" /></td>
						<td class="headertxt">Tax ID</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><input <?=$readisabled?> class='inputs' maxlength="11" type="text" value="<?=$maintEmpObj->TIN?>" onKeyDown="javascript:return dFilter (event.keyCode, this, '###-###-###');" onBlur="checkno('empTin',this.value,'<?=$notype?>','Tax ID No.','dvtaxid')"  name="txttax" id="txttax" /><span id="dvtaxid" style="color:#FF0000; font-size:10px"></span><input type="hidden" name="chtaxid" value="" id="chtaxid"><? //$maintEmpObj->DropDownMenu(array('','Light'=>'Light','Medium'=>'Medium','Heavy'=>'Heavy'),'cmbbuild',$maintEmpObj->Build,'class="inputs" style="width:222px;"'); ?></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Birthday</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input <?=$readisabled?> name="txtBDay" type="text" value="<?=(!empty($maintEmpObj->dateOfBirth) && strtotime($maintEmpObj->dateOfBirth) !== false) ? date('m-d-Y', strtotime($maintEmpObj->dateOfBirth)) : "";?>"  class='inputs' id="txtBDay" size="12" readonly></td>
						<td class="headertxt">HDMF</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><input <?=$readisabled?> class='inputs' maxlength="25" type="text" value="<?=$maintEmpObj->HDMF?>" onKeyDown="javascript:return dFilter (event.keyCode, this, '####-####-####');" onBlur="checkno('empPagibig',this.value,'<?=$notype?>','HDMF No.','dvhdmf')"  name="txthdmf" id="txthdmf" /><span id="dvhdmf" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chhdmf" value="" id="chhdmf"><? //$maintEmpObj->DropDownMenu(array('','Light'=>'Light','Fair'=>'Fair','Dark'=>'Dark'),'cmbcomplexion',$maintEmpObj->Complexion,'class="inputs" style="width:222px;"'); ?></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Marital Status</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><? $maintEmpObj->DropDownMenu(array('','SG'=>'Single','ME'=>'Married','SP'=>'Separated','WI'=>'Widow(er)'),'cmbmaritalstatus',$maintEmpObj->maritalStat,'class="inputs" style="width:222px;"  onchange="checkmarital();"' . $readisabled); ?></td>
						
						<td width="23%" style="<?=$visible?>" class="headertxt">Rate Mode</td>
						<td width="1%" style="<?=$visible?>" class="headertxt">:</td>
						<td width="16%" style="<?=$visible?>" class="gridDtlVal"><div id="basicrate"><? $maintEmpObj->DropDownMenu(array('','D'=>'Per Day','M'=>'Per Month'),'cmbpstatus',$maintEmpObj->PStatus,' class="inputs" onChange="checkrate();" style="width:145px;" disabled="disabled"'); ?></div><input type="hidden" name="txtratemode" id="txtratemode" value="<?=$maintEmpObj->PStatus;?>"/>
						</td>
					  </tr>
					  <tr> 
						<td class="headertxt">Height</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input <?=$readisabled?> class='inputs' maxlength="6" type="text" value="<?=$maintEmpObj->Height?>"  name="txtheight" id="txtheight"  size="10" onKeyDown="javascript:return dFilter (event.keyCode, this, '#\'##\'\'');" />&nbsp;ft.</td>
						
						<td class="headertxt" style="<?=$visible?>">Basic Rate</td>
						<td class="headertxt" style="<?=$visible?>">:</td>
						<td class="gridDtlVal" style="<?=$visible?>"><div id="dvsalary"><input class='inputs' type="text" value="<?=$maintEmpObj->Salary?>" style="<?=$visible?>" name="txtsalary" onkeypress="return computeRates(this.value,<?=$maintEmpObj->compCode?>,'1',event);" maxlength="9" id="txtsalary"/></div></td>
					    
					  </tr>
					  <tr>
					    <td class="headertxt">Weight</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><input <?=$readisabled?> class='inputs' maxlength="3" type="text" value="<?=$maintEmpObj->Weight?>"  name="txtweight" id="txtweight" size="10" onKeyDown="javascript:return dFilter (event.keyCode, this, '###');"/>&nbsp;kg.</td>
					    
						<td class="headertxt" style="<?=$visible?>">Daily Rate</td>
						<td class="headertxt" style="<?=$visible?>">:</td>
						<td class="gridDtlVal" style="<?=$visible?>">
							<div id="dvdailyrate">
								<input class='inputs' type="text" value="<?=$maintEmpObj->Drate?>" style="<?=$visible?> " onkeypress="return computeRates(this.value,<?=$maintEmpObj->compCode?>,'0',event);"  name="txtdailyrate" maxlength="9" id="txtdailyrate" />
								
							</div>
						<input class='inputs' type="hidden" value="<?=$maintEmpObj->Hrate?>"  name="txthourlyrate" style="<?=$visible?>" readonly maxlength="9" id="txthourlyrate" /></td>
						
					  </tr>
					  <tr>
					    <td class="headertxt">Citizenship</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getcitizenshipwil(''),'citizenCd','citizenDesc',''),'cmbcitizenship',$maintEmpObj->CitizenCd,'class="inputs" style="width:222px;"' . $readisabled); ?></td>
					   
						<td class="headertxt" style="<?=$visible?>">Pay Group</td>
						<td class="headertxt" style="<?=$visible?>">:</td>
						<td class="gridDtlVal" style="<?=$visible?>"><div id="payGroupId"><? $maintEmpObj->DropDownMenu(array('','Group 1'),'cmbgroup',$maintEmpObj->Group,'class="inputs" style="width:145px;"'); ?></div></td>
				        
					  </tr>
					  <tr>
					    <td class="headertxt">Religion</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getreligionwil(),'relCd','relDesc',''),'cmbreligion',$maintEmpObj->Religion,'class="inputs" style="width:222px;"' . $readisabled); ?></td>
					    
						<td class="headertxt" style="<?=$visible?>">Pay Category</td>
						<td class="headertxt" style="<?=$visible?>">:</td>
						<td class="gridDtlVal" style="<?=$visible?>"><div id="divpaycat"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getPayCat($maintEmpObj->compCode,''),'payCat','payCatDesc',''),'cmbCategory',$maintEmpObj->paycat,'class="inputs" style="width:145px; '.$visible.'" disabled="disabled"'); ?><input type="hidden" name="txtcat" id="txtcat" value="<?=$maintEmpObj->paycat;?>"/></div></td>
					    
					  </tr>
					  <tr>
					    <td class="headertxt">Blood Type</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu(array('','A'=>'A','B'=>'B','AB'=>'AB','AB+'=>'AB+','O'=>'O','A-'=>'A-','A+'=>'A+','B-'=>'B-','B+'=>'B+','O-'=>'O-','O+'=>'O+'),'cmbbloodtype',$maintEmpObj->BloodType,'class="inputs" style="width:222px;"' . $readisabled); ?></td>
						
						<td class="headertxt" style="<?=$visible?>">Bank Account Type</td>
						<td class="headertxt" style="<?=$visible?>">:</td>
						<td class="gridDtlVal" style="<?=$visible?>"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getbankwil(),'bankCd','bankDesc',''),'cmbbank',$maintEmpObj->bank,'class="inputs" style="width:145px;" onChange="checkno(\'empAcctNo\',\'\',\'' .$notype. '\',\'Account No.\',\'dvAcctNo\')" onBlur="checkno(\'empAcctNo\',\'\',\'' .$notype. '\',\'Account No.\',\'dvAcctNo\')"'); ?></td>
						
					 </tr>
					<tr>
                    	<td class="headertxt">Tax Exemption</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getTEU(),'teuCode','teuDesc',''),'cmbexemption',$maintEmpObj->Exemption,'class="inputs" style="width:222px;"'); ?></td>
                        
						<td class="headertxt" style="<?=$visible?>">Bank Account</td>
					    <td class="headertxt" style="<?=$visible?>">:</td>
					    <td class="gridDtlVal" style="<?=$visible?>"><input class='inputs'  maxlength="25" type="text" value="<?=$maintEmpObj->bankAcctNo?>" onKeyDown="return AcctFormat(event);"  name="txtbankaccount" id="txtbankaccount" onBlur="checkno('empAcctNo',this.value,'<?=$notype?>','Account No.','dvAcctNo')" /><span id="dvAcctNo" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chAcctNo" value="" id="chAcctNo"></td>
						
					</tr>
					
                    <?
                    if($_SESSION['user_telcoaccess']=="Y"){
					?>
                    <tr>
                        <td class="gridDtlVal" colspan="4">Company Issued Mobile Phone Line&nbsp;&nbsp;&nbsp;:&nbsp;<input type="checkbox" name="chkSun" id="chkSun" value="Y" <?=($maintEmpObj->sunLine=="Y"?"checked":"" )?>/>&nbsp;<label for="chkSun">Sun</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkGlobe" id="chkGlobe" value="Y" <?=($maintEmpObj->globeLine=="Y"?"checked":"" )?>/>&nbsp;<label for="chkGlobe">Globe&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="checkbox" name="chkSmart" id="chkSmart" value="Y" <?=($maintEmpObj->smartLine=="Y"?"checked":"" )?>/>&nbsp;<label for="chkSmart">Smart</label></td>
					    <td class="headertxt"></td>
					    <td class="gridDtlVal"></td>
                    </tr>
                    <?php 
					}
					?>
					<?
					if($_SESSION['Confiaccess']==="Y" && $maintEmpObj->stat_ !== "R" && $_SESSION['user_level'] != 1) {
					?>
					<tr>
						<td colspan="6" style="text-align:right;">
							<button type="button" class="inputs" name="saveSalary" onClick="return submitSalary()">Save</button>
						</td>
					</tr>
					<? } ?>
					<tr>
					
					<? if ($_SESSION['Confiaccess']=="Y") {  ?>
                    	<td colspan="6"><div id="Allowance"></div>
                        <div id="indicator2" align="center"></div>
                        </td>
					<? } ?>
                    </tr>
					</table></td>
                      </tr>
					  
                    </TABLE>				
                    </div>
                
              	<div id="content6" class="content6" style="height: 620px;">
                    <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="470" valign="top">
               		     <table width="100%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td colspan="3" height="15"></td>
					  </tr>
                      <tr>
                    	<td colspan="3"><div id="Trainings"></div>
                        <div id="indicator4" align="center"></div>
                        </td>
                    </tr>               
					</table>
                        </td>
                      </tr>
                    </TABLE>                                
                </div>
                <div id="content7" class="content7" style="height: 620px;">
                        <div id='divCont7'></div>
                 </div>
</td>
				</tr>
                <tr><td class="parentGridDtl" >
			
               <? 
				if (($payGrp == $maintEmpObj->Group && $_SESSION['user_release']=="Y") || $_SESSION['user_level'] == 1 && $_SESSION['user_release']=="Y") {   
			   ?>
                <label>
                &nbsp;&nbsp;&nbsp;<input type="checkbox" name="chRelease" id="chRelease">
                <span class="headertxt">Post</span>&nbsp;&nbsp;</label>
                <?} if($_SESSION['user_release']!=="Y") {
					$hidden = 'visibility:hidden;';
				}?>
                 <? if ($_GET['act']!="View") { ?>
                <input name="save"   type="submit" style="<?=$hidden?>" onClick="return submitProfile()" class="inputs" id="save" value="Save">
                <? } else {?>
                <input name="save" style="visibility:hidden;" type="submit" onClick="return submitProfile()" disabled class="inputs" id="save" value="Save">
                <? } ?>
				<input class="inputs" type="button" name="btnBack" id="btnBack" value="BACK"
				onClick="location.href='new_emp_list.php?back=1&brnCd=<?=$maintEmpObj->branch?>'">
                  
                   </td></tr>
			</TABLE>
		</FORM>
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
	</BODY>
</HTML>
<SCRIPT>
//	Calendar.setup({
//			  inputField  : "txtEffDate",      // ID of the input field
//			  ifFormat    : "%m/%d/%Y",          // the date format
//			  button      : "imgtxtEffDate"       // ID of the button
//		}
//	)

	pager("contact_list_ajax.php","TSCont",'load',0,0,'','','','../../../images/');
	pager("employee_profile_allowance_list_ajax_result.php","Allowance",'load',0,0,'','','','../../../images/'); 
	pager("employee_profile_performance_list_ajax_result.php","Performance",'load',0,0,'','','','../../../images/');  
	pager("employee_profile_trainings_list_ajax_result.php","Trainings",'load',0,0,'','','','../../../images/');  
</SCRIPT>
<script src="../../../includes/validations.js"></script>
<script type='text/javascript' src='timesheet_js.js'></script>
<script>
	// Trigger file input when the image box is clicked
	function triggerFileInput() {
        document.getElementById('file-input').click();
    }

	// Display the selected image in the box
	function previewImage(event) {
		const file = event.target.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = function(e) {
				document.getElementById('profile-img').src = e.target.result;
			};
			reader.readAsDataURL(file);
		} else {
			console.log("No file selected.");
		}
	}

	function getname(){
		var lname=document.getElementById('txtlname').value + ", ";
		var fname=document.getElementById('txtfname').value + " ";
		var mname=document.getElementById('txtmname').value;
		document.getElementById("name1").innerHTML= " " +lname.toUpperCase()+fname.toUpperCase()+mname.toUpperCase();	
	}

	function viewTabSix(){
		new Ajax.Request('profile_content6.php',{
			method : 'get',
			onComplete : function(req){
				$('divCont6').innerHTML=req.responseText;
			},
			onCreate : function(){
				$('divCont6').innerHTML='<img src="../../../images/wait.gif">';
			},
			onSuccess : function(){
				$('divCont6').innerHTML='';
			}
		});
	
	}
	
		function viewTabEight(){
			new Ajax.Request('profile_content8.php',{
				method : 'get',
				onComplete : function(req){
					$('divCont7').innerHTML=req.responseText;
				},
				onCreate : function(){
					$('divCont7').innerHTML='<img src="../../../images/wait.gif">';
				},
				onSuccess : function(){
					$('divCont7').innerHTML='';
				}
			});
		}
	
	
	function viewUsrInfo(id){
		var swtch = $('usrInfo'+id).style.display;
		if(swtch == 'none'){
			$('imgUsrInfo'+id).src='../../../images/folder-open.png';
			$('usrInfo'+id).style.display='';
			Effect.SlideDown('divUsrInfo'+id,{duration:1.0}); 

		}
		else{
			$('imgUsrInfo'+id).src='../../../images/folder.png';
			Effect.SlideUp('usrInfo'+id,{duration:1.0});
			Effect.SlideUp('divUsrInfo'+id,{duration:1.0});

		}
	}
		
	function maintTerminatedCause(){
		var txtterm=document.getElementById('txttermcause');
		txtterm.disabled=false;
		txtterm.focus();
	}
	function lockmaintTerminatedCause(){
		var txtterm=document.getElementById('txttermcause');
		txtterm.disabled=true;
		txtterm.value="";
	}
	
	function setRadio(){
		 
		var empstat=document.getElementById('cmbstatus').value;
		var radioButtons = document.getElementsByName("radSeparation");
		var txtterm=document.getElementById('txttermcause');
		
	      for (var x = 0; x < radioButtons.length; x ++) {
				if(empstat=="EOC"){
					radioButtons[0].checked=true;	
					txtterm.disabled=true;
					txtterm.value="";
				}
			 	else if(empstat=="RS"){
					radioButtons[1].checked=true;
					txtterm.disabled=true;
					txtterm.value="";	
				}
				else if(empstat=="TR"){
					radioButtons[2].checked=true;	
					txtterm.disabled=false;
					txtterm.focus();
				}
				else{
					radioButtons[x].checked=false;
					txtterm.disabled=true;
					txtterm.value="";
				}
      	  }
	}
/*	function maintTerminatedCause(){
		var winTermCause = new Window({
		id:"terminatedcause",
		classsName:'mac_os_x',
		width:500,
		height:100,
		zIndex:100,
		resizable:false,
		minimizable:true,
		title:"Termination Cause",
		showEffect:Effect.Appear,
		destroyOnClose:true,
		maximizable:false,
		hideEffect:Effect.SwitchOff,
		draggable:true})
		winTermCause.setURL('terminated_cause.php');
		winTermCause.showCenter(true);	
			myObserver={
				onDestroy: function(eventName,win){
					if(win==winTermCause){
						winTermCause=null;
						Windows.removeObserver(this);	
					}	
				}
			}
			Windows.addObserver(myObserver);
	}
*/	
	function maintUserDefMast(obj,act,catCode,recNo,empNo,bType){
		
		if(catCode!=0)
		{
			if(bType==1)
			{
				title = obj.options[obj.selectedIndex].text;
			}
			else
			{
				title = obj;
			}
			
			var winPrevEmp = new Window({
			id: "editPrevEmp",
			className : 'mac_os_x',
			width:500, 
			height:300, 
			zIndex: 100, 
			resizable: false, 
			minimizable : true,
			title: act+" "+title+"", 
			showEffect:Effect.Appear, 
			destroyOnClose: true,
			maximizable: false,
			hideEffect: Effect.SwitchOff, 
			draggable:true })
			winPrevEmp.setURL('user_defined.pop.php?catCode='+catCode+'&act='+act+'&recNo='+recNo+'&empNo='+empNo);
			winPrevEmp.showCenter(true);	
			
			 myObserver = {
				onDestroy: function(eventName, win) {
	
				  if (win == winPrevEmp) {
					winPrevEmp = null;
					viewTabEight();
					Windows.removeObserver(this);
				  }
				}
			  }
			  Windows.addObserver(myObserver);
		}
		else
		{
			alert("Select a Type.");
			return false;
		}
	}
	
	function deleUserDefMst(recNo,catcode)
	{
		var deleUserDefMst = confirm('Are you sure do you want to delete the selected record? ');
		if(deleUserDefMst == true)
		{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?&action=deleUserDefinedMast&recNo='+recNo+'&catcode='+catcode,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					viewTabEight();
				}			
			})
		}
	}
	
	function printEmpInfo()
	{
		document.frmViewEditEmp.action = 'profile_other_info.pdf.php';
		document.frmViewEditEmp.target = "_blank";
		document.frmViewEditEmp.submit();
		document.frmViewEditEmp.target = "_self";
	}
	
	
	function mainContent6(act,seqNo)
	{
		var winPrevEmp = new Window({
		id: "editPrevEmp",
		className : 'mac_os_x',
		width:500, 
		height:460, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act + " Previous Employer", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		winPrevEmp.setURL('prevEmployer.pop.php?act='+act+'&seqNo='+seqNo);
		winPrevEmp.showCenter(true);	
		
		 myObserver = {
			onDestroy: function(eventName, win) {

			  if (win == winPrevEmp) {
				winPrevEmp = null;
				viewTabSix();
				Windows.removeObserver(this);
			  }
			}
		  }
		  Windows.addObserver(myObserver);
		
	}
	
	function delePrevEmplr(seqNo)
	{
		var delPrevEmplr = confirm('Are you sure do you want to delete the selected record? ');
		if(delPrevEmplr == true)
		{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?&action=delPrevEmplr&seqNo='+seqNo,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					viewTabSix();
				}			
			})
		}
	}
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
	function submitProfile() {
		const post = document.getElementById('chRelease');
		const fileInput = document.getElementById('file-input');
		const form = document.forms['frmViewEditEmp'];

		if (post && post.checked) {
			if (validateTabsForPosting('<?=$_GET['act']?>')) {
				form.submit();
				return true;
			} else {
				return false;
			}
		} else {
			if (validateTabs('<?=$_GET['act']?>')) {
				form.submit();
				return true;
			} else {
				return false;
			}
		}
	}	
	function popProvince(provcd){
	new Ajax.Request(
		'new_emp_profile.php?action=loadMunicipality&provcd='+provcd,
		{
		asynchronous	:	true,
		onComplete		:	function(req){
				$('divMunicipality').innerHTML=req.responseText;			
			}
		}
	);
	}
	
	
	function empProfileAllow(act,empNo,allwSeries,URL,ele,allwCode,offset,maxRec,isSearch,txtSrch,cmbSrch){
		var editProfileAllw = new Window({
		id: "editProfileAllw",
		className : 'mac_os_x',
		width:450, 
		height:250, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Allowance", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editProfileAllw.setURL('employee_profile_allowance_changes.php?transType='+act+'&empNo='+empNo+'&allwSeries='+allwSeries);
		editProfileAllw.show(true);
		editProfileAllw.showCenter();	
		
		myObserver = {
		    onDestroy: function(eventName, win) {

				if (win == editProfileAllw) {
					editProfileAllw = null;
					pager(URL,'Allowance',ele,offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&allwCode="+allwCode,'../../../images/');
					Windows.removeObserver(this);

					computeRatesWithAllowance(empNo)
				}
		    }
		}
		Windows.addObserver(myObserver);
	}
	
	function deleEmpAllw(URL,ele,empNo,allwSeries,offset,maxRec,isSearch,txtSrch,cmbSrch,allwDesc){

		var deleEmpAllw = confirm('Are you sure do you want to delete ?\nAllowance Type : '+allwDesc);
		
		if(deleEmpAllw == true){
			
			var param = '?action=delete&empNo='+empNo+"&allwSeries="+allwSeries;
			
			new Ajax.Request('employee_profile_allowance_list_ajax_result.php'+param,{
				asynchronous : true ,
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);
					pager(URL,'Allowance',ele,offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&allwSeries="+allwSeries,'../../../images/');
				},
			});			
		}	
	}	
	
	function empPerformance(act,empNo,performanceid,URL,ele,allwCode,offset,maxRec,isSearch,txtSrch,cmbSrch){
		var editPerformance = new Window({
		id: "editPerformance",
		className : 'mac_os_x',
		width:450, 
		height:250, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Employee Performance", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editPerformance.setURL('employee_profile_performance_changes.php?transType='+act+'&empNo='+empNo+'&performanceid='+performanceid);
		editPerformance.show(true);
		editPerformance.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editPerformance) {
		        editPerformance = null;
		        pager(URL,'Performance',ele,offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&allwCode="+allwCode,'../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}

	function delePerformance(URL,ele,empNo,performanceid,offset,maxRec,isSearch,txtSrch,cmbSrch,empPerformance){

		var delPerformance = confirm('Are you sure do you want to delete the selected performance?');
		
		if(delPerformance == true){
			
			var param = '?action=delete&empNo='+empNo+"&performanceid="+performanceid;
			
			new Ajax.Request('employee_profile_performance_list_ajax_result.php'+param,{
				asynchronous : true ,
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);
					pager(URL,'Performance',ele,offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&performanceid="+performanceid,'../../../images/');
				},
			});			
		}	
	}	
	
	function empTrainings(act,empNo,trainingid,URL,ele,allwCode,offset,maxRec,isSearch,txtSrch,cmbSrch){
		var editTrainings = new Window({
		id: "editTrainings",
		className : 'mac_os_x',
		width:450, 
		height:220, 
		zIndex: 100, 
		resizable: false, 
		minimizable : true,
		title: act+" Employee Training", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		editTrainings.setURL('employee_profile_trainings_changes.php?transType='+act+'&empNo='+empNo+'&trainingid='+trainingid);
		editTrainings.show(true);
		editTrainings.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editTrainings) {
		        editTrainings = null;
		        pager(URL,'Trainings',ele,offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&allwCode="+allwCode,'../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}

	function deleTrainings(URL,ele,empNo,trainingid,offset,maxRec,isSearch,txtSrch,cmbSrch,empTraining){

		var delTrainings = confirm('Are you sure do you want to delete the selected training?');
		
		if(delTrainings == true){
			
			var param = '?action=delete&empNo='+empNo+"&trainingid="+trainingid;
			
			new Ajax.Request('employee_profile_trainings_list_ajax_result.php'+param,{
				asynchronous : true ,
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);
					pager(URL,'Trainings',ele,offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&trainingid="+trainingid,'../../../images/');
				},
			});			
		}	
	}		
	
	function submitSalary() {
		var q = confirm('Sure to update employee salary?');
		
		if(q == true){
			
			var param = '?action=updateSalary';
			
			new Ajax.Request('new_emp_profile.php'+param,{
				method : 'POST',
				parameters : $('frmViewEditEmp').serialize(),
				onComplete : function (req){
					eval(req.responseText);
				},
			});			
		}	
	}
</script>