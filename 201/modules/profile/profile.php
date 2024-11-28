<?
session_start();
include("profile.obj.php");

include("../../../includes/pager.inc.php");
include("profile_userdef.obj.php");
include("profile_content6.obj.php");

$mainUserDefObjObj = new  mainUserDefObj();
$maintEmpObj = new ProfileObj();
$mainContent6Obj = new  mainContent6();
if ($_GET['act']=="Edit" || $_GET['act']=="View") {
	$_SESSION['oldcompCode']=$_GET['compCode'];
	$maintEmpObj->oldcompCode=$_SESSION['oldcompCode'];
	$maintEmpObj->viewprofile($_GET['empNo']);
	//$resreasons=$maintEmpObj->getResignReason($_GET['empNo'],$_GET['compCode']);
	$_SESSION['strprofile']=$_GET['empNo'];
	$_SESSION['empRestDay']=$maintEmpObj->RestDay;
	$_SESSION['empPayGrp']=$maintEmpObj->Group;
	$disablematstatus="";
	
	$status = $maintEmpObj->execQry("Select * from tblSeparatedEmployees where empNo='".$_GET['empNo']."'");
	$resStatus = $maintEmpObj->getSqlAssoc($status);
	
	if ($maintEmpObj->maritalStat=="SG") {
		$disablematstatus="disabled";
	}
	
	if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
		if ($maintEmpObj->paycat == 1) 
			$visible = "visibility:hidden;";
	}	
} else {
	unset($_SESSION['oldcompCode']);
}

// Base URL generation
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$projectFolder = "/pamana-water-corp-hris";
$imageUrl = $protocol . $host . $projectFolder . "/images/Employee Picture/";
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . 'pamana-water-corp-hris/images/Employee Picture/';

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
	$maintEmpObj->SSS		 = (isset($_POST['txtsss'])) ? $_POST['txtsss'] : "";
	$maintEmpObj->PhilHealth = (isset($_POST['txtphilhealth'])) ? $_POST['txtphilhealth'] : "";
	$maintEmpObj->TIN		 = (isset($_POST['txttax'])) ? $_POST['txttax'] : "";
	$maintEmpObj->HDMF		 = (isset($_POST['txthdmf'])) ? $_POST['txthdmf'] : "";

	//Contact Tab
	$maintEmpObj->Addr1	 		= (isset($_POST['txtadd1'])) ? $_POST['txtadd1'] : "";
	$maintEmpObj->Addr2	 		= (isset($_POST['txtadd2'])) ? $_POST['txtadd2'] : "";
	//Removed from previous by Nhomer requested by HR with document 
		//$maintEmpObj->Addr3	 = (isset($_POST['txtadd3'])) ? $_POST['txtadd3'] : "";
		//$maintEmpObj->City   = (isset($_POST['cmbcity'])) ? $_POST['cmbcity'] : 0;
	//Added by Nhomer requested by HR with document
	$maintEmpObj->Province		= (isset($_POST['cmbProvince'])) ? $_POST['cmbProvince'] : 0;
	$maintEmpObj->Municipality	= (isset($_POST['cmbMunicipality'])) ? $_POST['cmbMunicipality'] : 0;
	$maintEmpObj->ECPerson		= (isset($_POST['txtECPerson'])) ? $_POST['txtECPerson'] : "";
	$maintEmpObj->ECNumber 		= (isset($_POST['txtECNumber'])) ? $_POST['txtECNumber'] : "";
	
	//Personal Tab
	$maintEmpObj->sex	   	 = (isset($_POST['cmbgender'])) ? $_POST['cmbgender'] : 0;
	$maintEmpObj->NickName	 = (isset($_POST['txtnickname'])) ? $_POST['txtnickname'] : "";
	$maintEmpObj->Bplace	 = (isset($_POST['txtbplace'])) ? $_POST['txtbplace'] : "";
	$maintEmpObj->dateOfBirth= (isset($_POST['txtBDay'])) ? $_POST['txtBDay'] : date('Y-m-d');
	$maintEmpObj->maritalStat= (isset($_POST['cmbmaritalstatus'])) ? $_POST['cmbmaritalstatus'] : 0;
	//Removed from previous by Nhomer requested by HR with document
		//$maintEmpObj->Spouse	 = (isset($_POST['txtspouse'])) ? $_POST['txtspouse'] : "";
		//$maintEmpObj->Build      = (isset($_POST['cmbbuild'])) ? $_POST['cmbbuild'] : 0;
		//$maintEmpObj->Complexion = (isset($_POST['cmbcomplexion'])) ? $_POST['cmbcomplexion'] : 0;
		//$maintEmpObj->EyeColor   = (isset($_POST['cmbeyecolor'])) ? $_POST['cmbeyecolor'] : 0;
		//$maintEmpObj->Hair       = (isset($_POST['cmbhair'])) ? $_POST['cmbhair'] : 0;
	$maintEmpObj->Height	 = (isset($_POST['txtheight'])) ? $_POST['txtheight'] : "";
	$maintEmpObj->Weight	 = (isset($_POST['txtweight'])) ? $_POST['txtweight'] : "";
	$maintEmpObj->CitizenCd	 = (isset($_POST['cmbcitizenship'])) ? $_POST['cmbcitizenship'] : 0;
	$maintEmpObj->Religion   = (isset($_POST['cmbreligion'])) ? $_POST['cmbreligion'] : 0;
	$maintEmpObj->BloodType  = (isset($_POST['cmbbloodtype'])) ? $_POST['cmbbloodtype'] : 0;
	
	//ID No. Tab
	
	//Employment Tab
	$maintEmpObj->position	 = (isset($_POST['cmbposition'])) ? $_POST['cmbposition'] : 0;
	$maintEmpObj->divCode 	= (isset($_POST['txtDiv'])) ? $_POST['txtDiv'] : 0;
	$maintEmpObj->DepCode 	= (isset($_POST['txtDept'])) ? $_POST['txtDept'] : 0;
	$maintEmpObj->secCode 	= (isset($_POST['txtSect'])) ? $_POST['txtSect'] : 0;
	$maintEmpObj->RestDay 	= (isset($_SESSION['empRestDay'])) ? $_SESSION['empRestDay'] : "";
	$maintEmpObj->empRank	= (isset($_POST['txtRank'])) ? $_POST['txtRank'] : 0;
	$maintEmpObj->level	 	= (isset($_POST['txtLevel'])) ? $_POST['txtLevel'] : 0;
	$maintEmpObj->Status;	
	$maintEmpObj->Effectivity 		= $_POST['txtEffDate'];
	$maintEmpObj->Regularization 	= $_POST['txtRegDate'];
	$maintEmpObj->EndDate 			= $_POST['txtEndDate'];
	$maintEmpObj->RSDate 			= $_POST['txtRSDate'];
	$maintEmpObj->prevtag   = (isset($_POST['chprev'])) ? $_POST['chprev'] : "";
	$maintEmpObj->empStat	= (isset($_POST['cmbstatus'])) ? $_POST['cmbstatus'] : 0;
	$maintEmpObj->resReason;
	
	//Payroll Tab
	$maintEmpObj->Salary	 = (isset($_POST['txtsalary'])) ? $_POST['txtsalary'] : "";
	$maintEmpObj->PStatus    = (isset($_POST['cmbpstatus'])) ? $_POST['cmbpstatus'] : 0;
	$maintEmpObj->Exemption	 = (isset($_POST['cmbexemption'])) ? $_POST['cmbexemption'] : 0;
	$maintEmpObj->Release	 = (isset($_POST['cmbrelease'])) ? $_POST['cmbrelease'] : 0;
	$maintEmpObj->Group 	= (isset($_POST['cmbgroup'])) ? $_POST['cmbgroup'] : 0;
	$maintEmpObj->paycat 	= (isset($_POST['cmbCategory'])) ? $_POST['cmbCategory'] : 0;
	$maintEmpObj->oldcompCode=$_SESSION['oldcompCode'];
	$maintEmpObj->strprofile=$_SESSION['strprofile'];
	$maintEmpObj->bank       = (isset($_POST['cmbbank'])) ? $_POST['cmbbank'] : 0;
	$maintEmpObj->bankAcctNo = (isset($_POST['txtbankaccount'])) ? $_POST['txtbankaccount'] : "";

	if ($_GET['act']=="Add") {
		$maintEmpObj->addEmployee();	
	}
	elseif ($_GET['act']=="Edit") {
		$maintEmpObj->updateemployee($_GET['empNo'],$_GET['compCode']);	
	}
	unset($_SESSION['strprofile'],$_SESSION['oldcompCode'],$_SESSION['profile_act'],$_SESSION['empRestDay']);
	header("Location: profile_list.php");
} else {
	unset($_SESSION['empRestDay'],$_SESSION['empPayGrp']);
}

if($_GET["action"]=='deleUserDefinedMast')
{
	$res_DelRecord = $mainUserDefObjObj->del_UserDefMstRec($_GET["recNo"],$_GET["catcode"]);
	if($res_DelRecord==true)
		echo "alert('Record was sucessfully deleted.');";
	else	
		echo "alert('Record was unsucessfully deleted.');";
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
	$arrResProv=$maintEmpObj->makeArr($maintEmpObj->getMunicipality(" where provinceCd='{$_GET['provcd']}'"),'municipalityCd','municipalityDesc','');
	$maintEmpObj->DropDownMenu($arrResProv,'cmbMunicipality','','class="inputs" style="width:222px"');
	exit();
}
 
unset($_SESSION['strprofile']);
if ($_SESSION['strprofile']=="") {
	$_SESSION['strprofile']=$maintEmpObj->createstrwil();
}

$disabled="";
$_SESSION['profile_act']=$_GET['act'];
if ($_GET['act']=="View") {
	$disabled="disabled";
}

$dis_payGrp = $maintEmpObj->getProcGrp();

if($maintEmpObj->Group==$dis_payGrp)
	$dis_empRestday = "Y";

if($_GET['action']=="setpaygroup"){
		$resPayGroup = $maintEmpObj->getBranchPayGroup(" where compCode='{$_SESSION['company_code']}' and brnCode='{$_GET['groupid']}'");	
		if($maintEmpObj->getRecCount($resPayGroup)>0){
			$resQry=$maintEmpObj->getArrRes($resPayGroup);
			foreach($resQry as $PayGroup => $payGroupVal){
					$groupId=$payGroupVal['brnDefGrp'];
				}
			}
		echo $maintEmpObj->DropDownMenu(array('','Group 1'),'cmbgroup',$groupId,'class="inputs" style="width:145px;"');
	exit();
	}



include("../../../includes/calendar.php");
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<STYLE>@import url('../../style/tabs.css');</STYLE>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<script type="text/javascript"  src="../../../includes/calendar.js"></script>
        <STYLE>@import url('../../../includes/calendar.css');</STYLE>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
        
		<style type="text/css">
        *
        .headertxt {font-family: verdana; font-size: 11px;}
        
        </style>        
	</HEAD>
	<BODY onLoad="focusTab2(1);">
		<FORM name='frmViewEditEmp' id="frmViewEditEmp" action="" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" width="100%">
				<tr>
					
      <td class="parentGridHdr" height="30"> &nbsp;<img src="../../../images/grid.png">&nbsp; 
        Personal Profile :&nbsp;<div id="name1" style="color:#FF0000; font-size:12px; width:700px; position:absolute; left:153px; top:19px;"><?
		echo $maintEmpObj->lName . ", " . $maintEmpObj->fName . " " . $maintEmpObj->mName;
		if($maintEmpObj->Status=='RS' && $maintEmpObj->RSDate!=""){
			if($resStatus['natureCode']!=""){
				if($resStatus['natureCode']==1){
					echo "&nbsp;&nbsp;&nbsp;( AWOL )";		
				}	
				elseif($resStatus['natureCode']==3){
					echo "&nbsp;&nbsp;&nbsp;( RESIGNED )";	
				}
				elseif($resStatus['natureCode']==5){
					echo "&nbsp;&nbsp;&nbsp;( TERMINATED )";	
				}
			}
			else{
				echo "&nbsp;&nbsp;&nbsp;( RESIGNED )";		
			}
		}
		elseif($maintEmpObj->Status=='IN'){
			echo "&nbsp;&nbsp;&nbsp;( TRANSFERRED )";
		}
		elseif($maintEmpObj->Status=='AWOL'){
			echo "&nbsp;&nbsp;&nbsp;( AWOL )";
		}
		elseif($maintEmpObj->Status=='TR'){
			echo "&nbsp;&nbsp;&nbsp;( TERMINATED )";
		}
		elseif($maintEmpObj->Status=="RS" && $maintEmpObj->EndDate!="" && $maintEmpObj->RSDate=="" ){
			echo "&nbsp;&nbsp;&nbsp;( END OF CONTRACT )";
		}
		?>
        
        </div></td>
				</tr>
				<tr>
					<td class="parentGridDtl" >
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

			  <div id="tab1" class="tab1" onClick="focusTab2(1)">General Info</div>
              <div id="tab2" class="tab2" onClick="focusTab2(2)">Contacts</div>
              <div id="tab3" class="tab3" onClick="focusTab2(3)">Employment</div>
              <div id="tab4" class="tab4" onClick="focusTab2(4)">Performance</div>
              <div id="tab5" class="tab5" onClick="focusTab2(5)">Employee Profile</div>
              <div id="tab6" class="tab6" onClick="focusTab2(6);">Training</div>
              <div id="tab7" class="tab7" onClick="focusTab2(7); viewTabEight();">Other Info</div>
			  <div id="tab8" class="tab8" onClick="focusTab2(8)">History</div>
          		<div id="content1" class="content1" style="height: 520px;">
                    <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="200" valign="top">
               		     <table width="90%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td height="15" colspan="3"></td>
					  </tr>
                        <? if ($_GET['act']=="Add") {
								$notype="1";
							}	
							else {
								$notype="0";
							}	
						
						?>                        
					  <tr> 
						<td width="19%" class="headertxt">Bio Series No.</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><input class='inputs' size="50" type="text" name="txtbio" value="<?=$maintEmpObj->bio?>" onBlur="checkno('bio',this.value,'<?=$notype?>','Bio Series No.','dvbio')" id="txtbio" maxlength="50">&nbsp;<span id="dvbio" style="color:#FF0000; font-size:10px"></span><input type="hidden" name="chbio" value="" id="chbio"></td>
					  </tr>
					  <tr> 
						<td width="19%" class="headertxt">Employee No.</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><input class='inputs' size="50" type="text" name="txtempNo" value="<?=$maintEmpObj->empNo?>" onBlur="checkno('empNo',this.value,'<?=$notype?>','Emp No.','dvempNo')" id="txtempNo" maxlength="50">&nbsp;<span id="dvempNo" style="color:#FF0000; font-size:10px"></span><input type="hidden" name="chempNo" value="" id="chempNo"></td>
					  </tr>

					  <tr> 
						<td width="19%" class="headertxt">Last Name</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><input class='inputs' size="50" type="text" name="txtlname" value="<?=$maintEmpObj->lName?>" id="txtlname" maxlength="50"></td>
					  </tr>
					  <tr> 
					<td class="headertxt">First Name</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input value="<?=$maintEmpObj->fName?>" size="50" class='inputs' type="text" name="txtfname" id="txtfname" maxlength="50"></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Middle Name</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input value="<?=$maintEmpObj->mName?>" size="50" class='inputs' type="text" name="txtmname"  id="txtmname" maxlength="50"></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Company</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><?
						$salaryamount=$maintEmpObj->Salary;
						 $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getCompany(''),'compCode','compName',''),'cmbcompny',$maintEmpObj->compCode,'class="inputs" style="width:222px;" onchange="getresult(this.value,\'profile.obj.php\',\'cdpaycat\',\'divpaycat\');getresult(this.value,\'profile.obj.php\',\'cdbranch\',\'divbranch\'); getresult(this.value,\'profile.obj.php\',\'cdshift\',\'dvshift\'); getresult(this.value,\'profile.obj.php\',\'cdposition\',\'dvposition\'); getsalary(this.value,\'profile.obj.php\',\'cdsalarycmb\',\'dvsalary\',\''.$maintEmpObj->Salary.'\'); getsalary(this.value,\'profile.obj.php\',\'cddratecmb\',\'dvdailyrate\',\''.$maintEmpObj->Drate.'\'); getcompany(this.value);"'); ?><input type="hidden" value="<?=$maintEmpObj->compCode?>" name="company_code" id="company_code"></td>
					  </tr>
					  <tr> 
						<td class="headertxt" >Branch</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal">
							<div id="divbranch">
					<? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranch($maintEmpObj->compCode),'brnCode','brnDesc',''),'cmbbranch',$maintEmpObj->branch,'class="inputs" style="width:222px;" onchange="loadPayGroup(this.value);"'); ?>							</div>						</td>
					  </tr>
					  <tr>
						<td class="headertxt" >Picture</td>
						<td class="headertxt">:</td>
                        <td class="gridDtlVal">
							<img id="profile-img" src="<?=$imageUrl . $maintEmpObj->picture?>" alt="profile" height="100px" width="100px" style="border: 1px solid #c9c9c9;" onclick="triggerFileInput()">
							<input type="file" id="file-input" name="file-input" accept="image/*" onchange="previewImage(event)" style="display: none;">
						</td>
                      </tr>
					</table>
                        </td>
                      </tr>
                    </TABLE>
                </div>
               <div id="content2" class="content2" style="height: 520px;">
                   <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="420" valign="top">
							<table width="100%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td colspan="3" height="15"></td>
						</tr>
					  <tr> 
						<td width="35%" class="headertxt">Home No, Bldg., Street</td>
						<td width="1%" class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><input value="<?=$maintEmpObj->Addr1?>" size="70" name="txtadd1" type="text" class="inputs" maxlength="150" id="txtadd1" /></td>
						</tr>
					  <tr> 
						<td class="headertxt">Barangay</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><input value="<?=$maintEmpObj->Addr2?>" size="70" name="txtadd2" type="text" class="inputs" maxlength="150" id="txtadd2" /></td>
						</tr>
                      <tr>
                      	<td class="headertxt">Province</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal" valign="top"><? 
						$arrResProv=$maintEmpObj->makeArr($maintEmpObj->getProvince(),'provinceCd','provinceDesc','');
						$maintEmpObj->DropDownMenu($arrResProv,'cmbProvince',$maintEmpObj->provinceCd,'onChange="popProvince(this.value);" class="inputs" style="width:222px;"');?>
                      </tr>  
                      <tr>
                      	<td class="headertxt">Municipality/City</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal" valign="top"><div id="divMunicipality"><? 
						$arrResMun=$maintEmpObj->makeArr($maintEmpObj->getMunicipality(" where provinceCd='{$maintEmpObj->provinceCd}'"),'municipalityCd','municipalityDesc','');
						$maintEmpObj->DropDownMenu($arrResMun,'cmbMunicipality',$maintEmpObj->Municipality,'class="inputs" style="width:222px"');?></div></td>
                      </tr>
                      <tr>
                      	<td class="headertxt">In Case of Emergency Contact</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal" valign="top"><input value="<?=$maintEmpObj->ECPerson;?>" size="50" name="txtECPerson" type="text" class="inputs" maxlength="150"  id="txtECPerson"/></td>
                      </tr>
                      <tr>
                      	<td class="headertxt">Contact Number</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal" valign="top"><input value="<?=$maintEmpObj->ECNumber?>" size="15" name="txtECNumber" type="text" class="inputs" maxlength="15" id="txtECNumber"/></td>
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
              <div id="content3" class="content3" style="height: 520px;">
<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="470" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="100%" valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="2">
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td width="65%">&nbsp;</td>
                              </tr>
                                <tr>
                                  <td class="headertxt">Position</td>
                                  <td class="headertxt">:</td>
                                  <td class="gridDtlVal"><div id="dvposition">
                                    <?	
                                                        $poswhere=" and compCode='" . $maintEmpObj->compCode . "'";
                                                        
                                                        $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getpositionwil($poswhere,1),'posCode','posDesc',''),'cmbposition',$maintEmpObj->position,'class="inputs" style="width:222px;" onchange="getPosInfo(this.value);"');
							$pos = $maintEmpObj->getpositionwil(" and posCode='{$maintEmpObj->position}'",2);
							$Div = $maintEmpObj->getDivDescArt($maintEmpObj->compCode,$maintEmpObj->divCode);
							$Dept = $maintEmpObj->getDeptDescArt($maintEmpObj->compCode, $maintEmpObj->divCode,$maintEmpObj->DepCode);
							$Sect = $maintEmpObj->getSectDescArt($maintEmpObj->compCode, $maintEmpObj->divCode,$maintEmpObj->DepCode,$maintEmpObj->secCode);
														
														 ?>
                                  </div></td>
                                </tr>                              
                              <tr>
                                <td width="33%" class="headertxt">Division</td>
                                <td width="2%" class="headertxt">:</td>
                                <td class="gridDtlVal"><div id='divdivision'><?=$Div['deptDesc']?></div>
								<input class='inputs' maxlength="25" type="hidden" value="<?=$maintEmpObj->divCode?>"  name="txtDiv" id="txtDiv" />                                </td>
                              </tr>
<tr>
                                <td width="33%" class="headertxt">Department</td>
                                <td width="2%" class="headertxt">:</td>
                                <td class="gridDtlVal"><div id='divdpt'><?=$Dept['deptDesc']?>
                                
                                </div>
								<input class='inputs' maxlength="25" type="hidden" value="<?=$maintEmpObj->DepCode?>"  name="txtDept" id="txtDept" />                                </td>
                              </tr>
<tr>
                                <td width="33%" class="headertxt">Section</td>
                                <td width="2%" class="headertxt">:</td>
                                <td class="gridDtlVal"><div id='divsection'><?=$Sect['deptDesc']?>
                                </div>
                                <input class='inputs' maxlength="25" type="hidden" value="<?=$maintEmpObj->secCode?>"  name="txtSect" id="txtSect" />                                </td>
                              </tr>
                                <tr>
                                  <td class="headertxt">Rank</td>
                                  <td class="headertxt">:</td>
                                  <td class="gridDtlVal"><div id="dvrank"><?
								  $empRank = $maintEmpObj->getRank($maintEmpObj->empRank);
								  echo $empRank['rankDesc'];
								  
								  ?>
                                  </div>
								<input class='inputs' maxlength="25" type="hidden" value="<?=$maintEmpObj->empRank?>"  name="txtRank" id="txtRank" />                                  </td>
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
                                <td class="gridDtlVal"><? 
//								if($maintEmpObj->empStat=='RG' || $maintEmpObj->empStat=='PR' || $maintEmpObj->empStat=='CN'){
//									$stat=$maintEmpObj->empStat;	
//								}
//								else{
//									$stat=$maintEmpObj->Status;
//								}
								//,'RS'=>'Resigned','TR'=>'Terminated','IN'=>'Inactive','AP'=>'Applicant','EOC'=>'End of Contract','AWOL'=>'AWOL'
								$maintEmpObj->DropDownMenu(array("0"=>"",'RG'=>'Regular','PR'=>'Probationary','CN'=>'Contractual'),'cmbstatus',$maintEmpObj->empStat,'class="inputs" style="width:222px;"'); ?></td>
                              </tr>
                              <tr>
                                <td class="headertxt">Date Hired</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><input   name="txtEffDate" type="text" class='inputs' id="txtEffDate"  value="<?=($maintEmpObj->Effectivity !="") ? date('Y-m-d',strtotime($maintEmpObj->Effectivity)) : "";?>" size="15" maxlength="10" readonly /></td>
                              </tr>
                              <tr>
                                <td class="headertxt">Regularization</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><input   name="txtRegDate" value="<?=($maintEmpObj->Regularization !="") ? date('Y-m-d',strtotime($maintEmpObj->Regularization)) : "";?>" type="text" class='inputs' id="txtRegDate"   size="15" maxlength="10" readonly /></td>
                              </tr>
                              <tr>
                                <td class="headertxt">End Date</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><input   name="txtEndDate" value="<?=($maintEmpObj->EndDate !="") ? date('Y-m-d',strtotime($maintEmpObj->EndDate)) : "";?>" type="text" class='inputs' id="txtEndDate"  size="15" maxlength="10" readonly /></td>
                              </tr>
                              <tr>
                                <td class="headertxt">Resigned Date</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><input value="<?=($maintEmpObj->RSDate !="") ? date('Y-m-d',strtotime($maintEmpObj->RSDate)) : "";?>" name="txtRSDate" type="text" class='inputs' id="txtRSDate"  size="15" maxlength="10" readonly /></td>
                              </tr>
                              <tr>
                                <td><span class="headertxt">With Previous Employer</span></td>
                                <td>:</td>
                                <td class="gridDtlVal"><label>
                                  <input type="radio" name="chprev" value="Y"   <? if ($maintEmpObj->prevtag=="Y") echo "checked"?> <?=$disabled?> id="chprev" />
                                  Yes</label>
                                    <label>
                                    <input type="radio" name="chprev" <? if ($maintEmpObj->prevtag=="N") echo "checked"?>  value="N" <?=$disabled?> id="chprev" />
                                      No</label></td>
                              </tr>
                              <tr><td colspan="3"><div id="RDlist"></div><div id="indicator2"></div></td></tr>
                            </table></td>
                            <td width="38%" valign="top"></td>
  </tr>
                        </table></td>
                      </tr>
                </TABLE>                     
                </div>
                
                <div id="content4" class="content4" style="height: 520px;">
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
              <div id="content5" class="content5" style="height: 520px;">
                    <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="470" valign="top">
		                    <table width="100%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td colspan="5" height="15"></td>
					  </tr>
					  <tr> 
						<td width="19%" class="headertxt">Gender</td>
						<td width="1%" class="headertxt">:</td>
						<td width="40%" class="gridDtlVal"><? $maintEmpObj->DropDownMenu(array('','M'=>'Male','F'=>'Female'),'cmbgender',$maintEmpObj->sex,'class="inputs" style="width:222px;"'); ?></td>
						<td width="23%" class="headertxt">SSS</td>
						<td width="1%" class="headertxt">:</td>
						<td width="16%" class="gridDtlVal"><input class='inputs' maxlength="12" onKeyDown="javascript:return dFilter (event.keyCode, this, '##-#######-#');" type="text" value="<?=$maintEmpObj->SSS?>" onBlur="checkno('empSssNo',this.value,'<?=$notype?>','SSS No.','dvsss')"  name="txtsss" id="txtsss" /><span id="dvsss" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chsss" value="" id="chsss"></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Nick Name</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input class='inputs' maxlength="20" size="33" type="text" value="<?=$maintEmpObj->NickName?>"  name="txtnickname" id="txtnickname" /></td>
						<td class="headertxt">Philhealth</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input class='inputs' maxlength="25" onKeyDown="javascript:return dFilter (event.keyCode, this, '##-#########-#');" type="text" value="<?=$maintEmpObj->PhilHealth?>" onBlur="checkno('empPhicNo',this.value,'<?=$notype?>','Philhealth No.','dvphilhealth')" name="txtphilhealth" id="txtphilhealth" /><span id="dvphilhealth" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chphilhealth" value="" id="chphilhealth"></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Birth Place</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input class='inputs' maxlength="50" size="33" type="text" value="<?=$maintEmpObj->Bplace?>"  name="txtbplace" id="txtbplace" /></td>
						<td class="headertxt">Tax ID</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input class='inputs' maxlength="11" type="text" onKeyDown="javascript:return dFilter (event.keyCode, this, '###-###-###');" value="<?=$maintEmpObj->TIN?>" onBlur="checkno('empTin',this.value,'<?=$notype?>','Tax ID No.','dvtaxid')"  name="txttax" id="txttax" /><span id="dvtaxid" style="color:#FF0000; font-size:10px"></span><input type="hidden" name="chtaxid" value="" id="chtaxid"></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Birthday</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input name="txtBDay" type="text" value="<?=($maintEmpObj->dateOfBirth !="") ? date('Y-m-d',strtotime($maintEmpObj->dateOfBirth)) : "";?>"  class='inputs' id="txtBDay" size="12" ></td>
						<td class="headertxt">HDMF</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input class='inputs' maxlength="25" type="text" value="<?=$maintEmpObj->HDMF?>" onBlur="checkno('empPagibig',this.value,'<?=$notype?>','HDMF No.','dvhdmf')"  name="txthdmf" id="txthdmf" /><span id="dvhdmf" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chhdmf" value="" id="chhdmf"></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Marital Status</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><? $maintEmpObj->DropDownMenu(array('','SG'=>'Single','ME'=>'Married','SP'=>'Separated','WI'=>'Widow(er)'),'cmbmaritalstatus',$maintEmpObj->maritalStat,'class="inputs" style="width:222px;"  onchange="checkmarital();"'); ?></td>
						<td class="headertxt">Rate Mode</td>
						<td class="headertxt"></td>
						<td class="gridDtlVal"><? $maintEmpObj->DropDownMenu(array('','D'=>'Per Day','M'=>'Per Month'),'cmbpstatus',$maintEmpObj->PStatus,' class="inputs" onChange="checkrate();" style="width:145px;"'); ?></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Height</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input class='inputs' maxlength="4" type="text" value="<?=$maintEmpObj->Height?>"  name="txtheight" id="txtheight" /></td>
						<td class="headertxt">Basic Rate</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><div id="dvsalary"><input class='inputs' type="text" style="<?=$visible?>" value="<?=$maintEmpObj->Salary?>"  name="txtsalary" onKeyPress="return computeRates(this.value,<?=$maintEmpObj->compCode?>,'1',event);" maxlength="9" id="txtsalary" readonly /></div></td>
					  </tr>
					  <tr>
					    <td class="headertxt">Weight</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><input class='inputs' maxlength="4" type="text" value="<?=$maintEmpObj->Weight?>"  name="txtweight" id="txtweight" /></td>
					    <td class="headertxt">Daily Rate</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><div id="dvdailyrate"><input class='inputs' style="<?=$visible?>" type="text" value="<?=$maintEmpObj->Drate?>" onKeyPress="return computeRates(this.value,<?=$maintEmpObj->compCode?>,'0',event);"  name="txtdailyrate" maxlength="9" id="txtdailyrate" readonly /></DIV></td>
				      </tr>
					  <tr>
					    <td class="headertxt">Citizenship</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getcitizenshipwil(''),'citizenCd','citizenDesc',''),'cmbcitizenship',$maintEmpObj->CitizenCd,'class="inputs" style="width:222px;"'); ?></td>
					    <td class="gridDtlVal">Pay Group</td>
					    <td class="gridDtlVal">:</td>
					    <td class="gridDtlVal"><div id="payGroupId"><? $maintEmpObj->DropDownMenu(array('','Group 1'),'cmbgroup',$maintEmpObj->Group,'class="inputs" style="width:145px;"'); ?></div><input class='inputs' type="hidden" value="<?=$maintEmpObj->Hrate?>" style="<?=$visible?>"  name="txthourlyrate" readonly maxlength="9" id="txthourlyrate" /></td>
				      </tr>
					  <tr>
					    <td class="headertxt">Religion</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getreligionwil(),'relCd','relDesc',''),'cmbreligion',$maintEmpObj->Religion,'class="inputs" style="width:222px;"'); ?></td>
					    <td class="gridDtlVal">Pay Category</td>
					    <td class="gridDtlVal">:</td>
					    <td class="gridDtlVal"><div id="divpaycat">
                              <? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getPayCat($maintEmpObj->compCode,''),'payCat','payCatDesc',''),'cmbCategory',$maintEmpObj->paycat,'class="inputs" style="width:145px;'.$visible.'"'); ?>
                            </div></td>
				      </tr>
					  <tr>
					    <td class="headertxt">Blood Type</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu(array('','A'=>'A','B'=>'B','AB'=>'A B','O'=>'O','A-'=>'A-','A+'=>'A+','B-'=>'B-','B+'=>'B+','O-'=>'O-','O+'=>'O+'),'cmbbloodtype',$maintEmpObj->BloodType,'class="inputs" style="width:222px;"'); ?></td>
					    <td class="gridDtlVal">Bank Account Type</td>
					    <td class="gridDtlVal">:</td>
					    <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getbankwil(),'bankCd','bankDesc',''),'cmbbank',$maintEmpObj->bank,'class="inputs" style="width:145px;" onChange="checkno(\'empAcctNo\',\'\',\'' .$notype. '\',\'Account No.\',\'dvAcctNo\')"'); ?></td>
				      </tr>
                     <tr>
                     	<td class="headertxt">Tax Exemption</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getTEU(),'teuCode','teuDesc',''),'cmbexemption',$maintEmpObj->Exemption,'class="inputs" style="width:145px;"'); ?></td>
                        <td class="headertxt">Bank Account No.</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><input class='inputs' maxlength="25" onKeyDown="return AcctFormat(event);" type="text" value="<?=$maintEmpObj->bankAcctNo?>"  name="txtbankaccount" id="txtbankaccount" onBlur="checkno('empAcctNo',this.value,'<?=$notype?>','Account No.','dvAcctNo')" /><span id="dvAcctNo" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chAcctNo" value="" id="chAcctNo"></td>
                    </tr> 
                    <?
                    if($_SESSION['user_telcoaccess']=="Y"){
					?>
                    <tr>
                        <td class="gridDtlVal" colspan="4">Company Issued Mobile Phone Line&nbsp;&nbsp;&nbsp;:&nbsp;<input type="checkbox" name="chkSun" id="chkSun" value="Y" <?=($maintEmpObj->sunLine=="Y"?"checked":"" )?>/>&nbsp;<label for="chkSun">Sun</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkGlobe" id="chkGlobe" value="Y" <?=($maintEmpObj->globeLine=="Y"?"checked":"" )?>/>&nbsp;<label for="chkGlobe">Globe&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="checkbox" name="chkSmart" id="chkSmart" value="Y" <?=($maintEmpObj->smartLine=="Y"?"checked":"" )?>/>&nbsp;<label for="chkSmart">Smart</label></td>
					    <td class="headertxt"></td>
					    <td class="gridDtlVal"></td>
                    </tr> 
                    <?
					}
					?>                   
					</table><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr><td height="15"></td></tr>
  <tr>
    <td><div id="empAllowList"></div>
	<div id="indicator1" align="center"></div></td>
  </tr>
</table>                        </td>
                      </tr>
                    </TABLE>
				</div>
                
              	<div id="content6" class="content6" style="height: 520px;">
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

                <div id="content7" class="content7" style="height: 520px;">
                    <div id='divCont7'></div>
                 </div>

			<div id="content8" class="content8" style="height: 520px;">
				<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
					<tr> 
						<td align="left" class="parentGridDtl" height="470" valign="top">
							<table width="100%" border="0" cellspacing="1" cellpadding="2">
								<tr> 
									<td colspan="3" height="15"></td>
								</tr>
								<tr>
									<td colspan="3"><div id="History"></div>
									<div id="indicator3" align="center"></div>
									</td>
								</tr>               
							</table>
						</td>
					</tr>
				</TABLE>   
			</div>
</td>
				</tr>
                <tr><td class="parentGridDtl" >
                <? if ($_GET['act']!="View") {?>
                <input name="save"   type="submit" class="inputs" onClick="return submitProfile()" id="save" value="Save">
                <? } else {?>
                <input name="save" style="visibility:hidden;" disabled onClick="return submitProfile()" type="submit" class="inputs" id="save" value="Save">
                <? }?>
                  <INPUT class="inputs" type="button" name="btnBack" id="btnBack" value="BACK" onClick="location.href='profile_list.php'">
                  
                   </td></tr>
			</TABLE>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	pager("contact_list_ajax.php","TSCont",'load',0,0,'','','','../../../images/');  
	pager("restday_list_ajax.php","RDlist",'load',0,0,'','','&disPayGrp=<?=$dis_empRestday?>','../../../images/');  
	pager("employee_profile_performance_list_ajax_result.php","Performance",'load',0,0,'','','','../../../images/');  	
	pager("employee_profile_trainings_list_ajax_result.php","Trainings",'load',0,0,'','','','../../../images/');  	
	pager("employee_profile_history_ajax_result.php","History",'load',0,0,'','','','../../../images/');  
</SCRIPT>
<script src="../../../includes/validations.js"></script>
<script type='text/javascript' src='timesheet_js.js'></script>
<script>

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
			$('imgUsrInfo'+id).src='../../../images/add.gif';
			$('usrInfo'+id).style.display='';
			Effect.SlideDown('divUsrInfo'+id,{duration:1.0}); 

		}
		else{
			$('imgUsrInfo'+id).src='../../../images/delete.png';
			Effect.SlideUp('usrInfo'+id,{duration:1.0});
			Effect.SlideUp('divUsrInfo'+id,{duration:1.0});

		}
	}
	
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
			height:250, 
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
	
	pager('profile_employee_Allowance_listAjaxResult.php','empAllowList','load',0,0,'','','&empNo=<?=$_GET['empNo']?>','../../../images/');  
	
	function deleEmpAllw(URL,ele,empNo,allwCode,offset,maxRec,isSearch,txtSrch,cmbSrch,allwDesc){

		var deleEmpAllw = confirm('Are you sure do you want to delete ?\nAllowance Type : '+allwDesc);
		
		if(deleEmpAllw == true){
			
			var param = '?action=delete&empNo='+empNo+"&allwCode="+allwCode;
			
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				asynchronous : true ,
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);
					pager(URL,ele,'delete',offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&allwCode="+allwCode,'../../../images/');
				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator2').innerHTML="";
				}
			});			
		}	
	}	
	
	function vlidatePayTag(payTagVal){
		if(payTagVal == 'T'){
			$('imgAllwStart').style.display='';
			$('imgAllwEnd').style.display='';
		}
		else{
			$('imgAllwStart').style.display='none';
			$('imgAllwEnd').style.display='none';			
		}
	}
		
	function maintAllow(act,empNo,allwCode,URL,ele,allwCode,offset,maxRec,isSearch,txtSrch,cmbSrch){

		var editAllw = new Window({
		id: "editAllw",
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
		editAllw.setURL('profile_maintain_employee_allowance.php?transType='+act+'&empNo='+empNo+"&allwCode="+allwCode);
		editAllw.show(true);
		editAllw.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == editAllw) {
		        editAllw = null;
		        pager(URL,ele,'maintAllw',offset,isSearch,txtSrch,cmbSrch,'&empNo='+empNo+"&allwCode="+allwCode,'../../../images/');
		        Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
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
		if (validateTabs('<?=$_GET['act']?>')) {
			document.frmViewEditEmp.submit();
			return true;
		} else {
			return false;
		}
	}
	function popProvince(provcd){
		new Ajax.Request(
		'profile.php?action=loadMunicipality&provcd='+provcd,
		{
		asynchronous : true,
		onComplete : function(req){
			$('divMunicipality').innerHTML=req.responseText;	
			}	
		}
		);
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
</script>