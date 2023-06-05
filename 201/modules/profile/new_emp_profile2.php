<?
session_start();
include("new_emp.obj.php");

include("../../../includes/pager.inc.php");
include("profile_userdef.obj.php");
include("profile_content6.obj.php");
	

$mainUserDefObjObj = new  mainUserDefObj();
$maintEmpObj = new ProfileObj();
$mainContent6Obj = new  mainContent6();
if ($_POST['save']!="") {

	//General Tab	
	$maintEmpObj->empNo   	 = (isset($_POST['txtempNo'])) ? $_POST['txtempNo'] : "";
	$maintEmpObj->bio		 = (isset($_POST['txtbio'])) ? $_POST['txtbio'] : "";
	$maintEmpObj->empNo   = (isset($_POST['txtempNo'])) ? $_POST['txtempNo'] : "";
	$maintEmpObj->compCode   = (isset($_POST['cmbcompny'])) ? $_POST['cmbcompny'] : 0;
	$maintEmpObj->lName      = (isset($_POST['txtlname'])) ? $_POST['txtlname'] : "";
	$maintEmpObj->fName	   	 = (isset($_POST['txtfname'])) ? $_POST['txtfname'] : "";
	$maintEmpObj->mName      = (isset($_POST['txtmname'])) ? $_POST['txtmname'] : "";
	$maintEmpObj->branch  	 = (isset($_POST['cmbbranch'])) ? $_POST['cmbbranch'] : 0;
	$maintEmpObj->location   = (isset($_POST['cmblocation'])) ? $_POST['cmblocation'] : 0;

	//Contact Tab
	$maintEmpObj->Addr1	 = (isset($_POST['txtadd1'])) ? $_POST['txtadd1'] : "";
	$maintEmpObj->Addr2	 = (isset($_POST['txtadd2'])) ? $_POST['txtadd2'] : "";
	$maintEmpObj->Addr3	 = (isset($_POST['txtadd3'])) ? $_POST['txtadd3'] : "";
	$maintEmpObj->City   = (isset($_POST['cmbcity'])) ? $_POST['cmbcity'] : 0;

	//Personal Tab
	$maintEmpObj->sex	   	 = (isset($_POST['cmbgender'])) ? $_POST['cmbgender'] : 0;
	$maintEmpObj->NickName	 = (isset($_POST['txtnickname'])) ? $_POST['txtnickname'] : "";
	$maintEmpObj->Bplace	 = (isset($_POST['txtbplace'])) ? $_POST['txtbplace'] : "";
	$maintEmpObj->dateOfBirth= $_POST['Birthday_M'] . "/" . $_POST['Birthday_D'] . "/" . $_POST['Birthday_Y'];
	$maintEmpObj->maritalStat= (isset($_POST['cmbmaritalstatus'])) ? $_POST['cmbmaritalstatus'] : 0;
	$maintEmpObj->Spouse	 = (isset($_POST['txtspouse'])) ? $_POST['txtspouse'] : "";
	$maintEmpObj->Height	 = (isset($_POST['txtheight'])) ? $_POST['txtheight'] : "";
	$maintEmpObj->Weight	 = (isset($_POST['txtweight'])) ? $_POST['txtweight'] : "";
	$maintEmpObj->CitizenCd	 = (isset($_POST['cmbcitizenship'])) ? $_POST['cmbcitizenship'] : 0;
	$maintEmpObj->Religion   = (isset($_POST['cmbreligion'])) ? $_POST['cmbreligion'] : 0;
	$maintEmpObj->Build      = (isset($_POST['cmbbuild'])) ? $_POST['cmbbuild'] : 0;
	$maintEmpObj->Complexion = (isset($_POST['cmbcomplexion'])) ? $_POST['cmbcomplexion'] : 0;
	$maintEmpObj->EyeColor   = (isset($_POST['cmbeyecolor'])) ? $_POST['cmbeyecolor'] : 0;
	$maintEmpObj->Hair       = (isset($_POST['cmbhair'])) ? $_POST['cmbhair'] : 0;
	$maintEmpObj->BloodType  = (isset($_POST['cmbbloodtype'])) ? $_POST['cmbbloodtype'] : 0;
	
	//ID No. Tab
	$maintEmpObj->SSS		 = (isset($_POST['txtsss'])) ? $_POST['txtsss'] : "";
	$maintEmpObj->PhilHealth = (isset($_POST['txtphilhealth'])) ? $_POST['txtphilhealth'] : "";
	$maintEmpObj->TIN		 = (isset($_POST['txttax'])) ? $_POST['txttax'] : "";
	$maintEmpObj->HDMF		 = (isset($_POST['txthdmf'])) ? $_POST['txthdmf'] : "";
	$maintEmpObj->bank       = (isset($_POST['cmbbank'])) ? $_POST['cmbbank'] : 0;
	$maintEmpObj->bankAcctNo = (isset($_POST['txtbankaccount'])) ? $_POST['txtbankaccount'] : "";
	
	//Employment Tab
	$maintEmpObj->position	 = (isset($_POST['cmbposition'])) ? $_POST['cmbposition'] : 0;
	$maintEmpObj->divCode 	= (isset($_POST['txtDiv'])) ? $_POST['txtDiv'] : 0;
	$maintEmpObj->DepCode 	= (isset($_POST['txtDept'])) ? $_POST['txtDept'] : 0;
	$maintEmpObj->secCode 	= (isset($_POST['txtSect'])) ? $_POST['txtSect'] : 0;
	$maintEmpObj->RestDay 	= (isset($_SESSION['empRestDay'])) ? $_SESSION['empRestDay'] : "";
	$maintEmpObj->empRank	= (isset($_POST['txtRank'])) ? $_POST['txtRank'] : 0;
	$maintEmpObj->level	 	 = (isset($_POST['txtLevel'])) ? $_POST['txtLevel'] : 0;
	$maintEmpObj->Shift 	= (isset($_POST['cmbshift'])) ? $_POST['cmbshift'] : 0;
	$maintEmpObj->Status	= (isset($_POST['cmbstatus'])) ? $_POST['cmbstatus'] : 0;
	$maintEmpObj->Effectivity = $_POST['cmbeffectivity_M'] . "/" . $_POST['cmbeffectivity_D'] . "/" . $_POST['cmbeffectivity_Y'];
	$maintEmpObj->Regularization_D = $_POST['cmbregularization_D'];
	$maintEmpObj->Regularization_M = $_POST['cmbregularization_M'];
	$maintEmpObj->Regularization_Y = $_POST['cmbregularization_Y'];
	$maintEmpObj->EndDate_D = $_POST['cmbenddate_D'];
	$maintEmpObj->EndDate_M = $_POST['cmbenddate_M'];
	$maintEmpObj->EndDate_Y = $_POST['cmbenddate_Y'];
	$maintEmpObj->prevtag   = (isset($_POST['chprev'])) ? $_POST['chprev'] : "";
	
	
	//Payroll Tab
	$maintEmpObj->Salary	 = (isset($_POST['txtsalary'])) ? $_POST['txtsalary'] : "";
	$maintEmpObj->PStatus    = (isset($_POST['cmbpstatus'])) ? $_POST['cmbpstatus'] : 0;
	$maintEmpObj->Exemption	 = (isset($_POST['cmbexemption'])) ? $_POST['cmbexemption'] : 0;
	$maintEmpObj->Release	 = (isset($_POST['cmbrelease'])) ? $_POST['cmbrelease'] : 0;
	$maintEmpObj->Group 	= (isset($_POST['cmbgroup'])) ? $_POST['cmbgroup'] : 0;
	$maintEmpObj->paycat 	= (isset($_POST['cmbCategory'])) ? $_POST['cmbCategory'] : 0;
	$maintEmpObj->oldcompCode=$_SESSION['oldcompCode'];
	$maintEmpObj->strprofile=$_SESSION['strprofile'];
	if ($_GET['act']=="Add") {
		if ($_POST['chRelease'] == "") 
			$maintEmpObj->addEmployee('tblEmpMast_new');	
		else
			$maintEmpObj->addEmployee('tblEmpMast');	
	}
	elseif ($_GET['act']=="Edit") {
		$maintEmpObj->updateemployee($_GET['empNo'],$_GET['compCode']);	
		if ($_POST['chRelease'] != "") 
			$maintEmpObj->releaseEmp($_GET['empNo'],$_GET['compCode']);	
	}
	unset($_SESSION['strprofile'],$_SESSION['oldcompCode'],$_SESSION['profile_act'],$_SESSION['empRestDay']);
	//header("Location: new_emp_list.php");
} else {
	unset($_SESSION['empRestDay']);
}

if($_GET["action"]=='deleUserDefinedMast')
{
	$res_DelRecord = $mainUserDefObjObj->del_UserDefMstRec($_GET["recNo"]);
	if($res_DelRecord==true)
		echo "alert('Record was sucessfully deleted.');";
	else	
		echo "alert('Record was unsucessfully deleted.');";
	exit();
}

if($_GET["action"]=='deleNewEmp') {
	$res_DelRecord = $maintEmpObj->delNewEmp($_GET["empNo"],$_GET["compCode"]);
	if($res_DelRecord==true)
		echo "alert('New Employee was sucessfully deleted.');";
	else	
		echo "alert('New Employee was unsucessfully deleted.');";
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

unset($_SESSION['strprofile']);
if ($_SESSION['strprofile']=="") {
	$_SESSION['strprofile']=$maintEmpObj->createstrwil();
}
if ($_GET['act']=="Edit" || $_GET['act']=="View") {
	$_SESSION['oldcompCode']=$_GET['compCode'];
	$maintEmpObj->oldcompCode=$_SESSION['oldcompCode'];
	$maintEmpObj->viewprofile($_GET['empNo']);
	$_SESSION['strprofile']=$_GET['empNo'];
	$_SESSION['empRestDay']=$maintEmpObj->RestDay;
	$disablematstatus="";
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
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<STYLE>@import url('../../style/tabs.css');</STYLE>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
        
		<style type="text/css">
        <!--
        .headertxt {font-family: verdana; font-size: 11px;}
        -->
        </style>        
	</HEAD>
	<BODY onLoad="focusTab(1); ">
		<FORM name='frmViewEditEmp' id="frmViewEditEmp" action="" onSubmit="return validateTabs('<?=$_GET['act']?>');" method="post" >
			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" width="100%">
				<tr>
					
      <td class="parentGridHdr" height="30"> &nbsp;<img src="../../../images/grid.png">&nbsp; 
        New Employee  Profile</td>
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

				<div id="tab1" class="tab1" onClick="focusTab(1)">General Tab</div>
				
              <div id="tab2" class="tab2" onClick="focusTab(2)">Contacts</div>
				
              <div id="tab3" class="tab3" onClick="focusTab(3)">Personal</div>
				
              <div id="tab4" class="tab4" onClick="focusTab(4)">ID Nos</div>
				
              <div id="tab5" class="tab5" onClick="focusTab(5)">Employment</div>
              <div id="tab6" class="tab6" <? /*if ($_SESSION["user_level"]!=3) {*/echo 'onClick="focusTab(6);"';/*}*/ ?> >Payroll</div>
              <div id="tab7" class="tab7" onClick="focusTab(7); viewTabEight();">User</div>
				
          <div id="content1" class="content1">
                    <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="300" valign="top">
               		     <table width="90%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					  </tr>
                        <? if ($_GET['act']=="Add") {
								$notype="1";
							}	
							else {
								$notype="0";
							}	
					if ($_GET['act']!="Add") {
						?>
					  <tr> 
						<td width="19%" class="headertxt">Employee No.</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><input readonly class='inputs' size="50" type="text" name="txtempNo" value="<?=$maintEmpObj->empNo?>" onBlur="checkno('empNo',this.value,'<?=$notype?>','Emp No.','dvempNo')" id="txtempNo" maxlength="50">&nbsp;<span id="dvempNo" style="color:#FF0000; font-size:10px"></span><input type="hidden" name="chempNo" value="" id="chempNo"></td>
					  </tr>
                      <?}?>

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
					<? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranch($maintEmpObj->compCode),'brnCode','brnDesc',''),'cmbbranch',$maintEmpObj->branch,'class="inputs" style="width:222px;"'); ?>							</div>						</td>
					  </tr>
<tr> 
						<td class="headertxt" >Location</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal">
					<? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getbranchwil(),'brnCode','brnDesc',''),'cmblocation',$maintEmpObj->location,'class="inputs" style="width:222px;"'); ?>
					</td>
					  </tr>  
					  <tr> 
						<td width="19%" class="headertxt">Bio Series No.</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><input class='inputs' size="50" type="text" name="txtbio" value="<?=$maintEmpObj->bio?>" onBlur="checkno('bio',this.value,'<?=$notype?>',document.getElementById('cmblocation').value,'dvbio')" id="txtbio" maxlength="50">&nbsp;<span id="dvbio" style="color:#FF0000; font-size:10px"></span><input type="hidden" name="chbio" value="" id="chbio"></td>
					  </tr>                                                
					</table>
                        </td>
                      </tr>
                    </TABLE>
                </div>
               <div id="content2" class="content2">
                   <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="300" valign="top">
							<table width="100%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td colspan="3">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						</tr>
					  <tr> 
						<td width="26%" class="headertxt">Home No, Bldg., Street</td>
						<td width="1%" class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><input value="<?=$maintEmpObj->Addr1?>" size="70" name="txtadd1" type="text" class="inputs" maxlength="150" id="txtadd1" /></td>
						</tr>
					  <tr> 
						<td class="headertxt">Barangay, Municipality</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><input value="<?=$maintEmpObj->Addr2?>" size="70" name="txtadd2" type="text" class="inputs" maxlength="150" id="txtadd2" /></td>
						</tr>
					  <tr>
					    <td class="headertxt">Other Info.</td> 
						<td class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><input value="<?=$maintEmpObj->Addr3?>" size="70" name="txtadd3" type="text" class="inputs" maxlength="150" id="txtadd3"  /></td>
						</tr>
					  <tr>
					    <td class="headertxt">City</td> 
						<td class="headertxt">:</td>
						<td class="gridDtlVal" valign="top"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getcitywil(),'cityCd','cityDesc',''),'cmbcity',$maintEmpObj->City,'class="inputs" style="width:222px;"'); ?></td>
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
              <div id="content3" class="content3">
                    <TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="300" valign="top">
		                    <table width="90%" border="0" cellspacing="1" cellpadding="2">
					  <tr> 
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					  </tr>
					  <tr> 
						<td width="19%" class="headertxt">Gender</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array('','M'=>'Male','F'=>'Female'),'cmbgender',$maintEmpObj->sex,'class="inputs" style="width:222px;"'); ?></td>
						<td width="80%" class="headertxt">Citizenship</td>
						<td width="1%" class="headertxt">:</td>
						<td width="80%" class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getcitizenshipwil(''),'citizenCd','citizenDesc',''),'cmbcitizenship',$maintEmpObj->CitizenCd,'class="inputs" style="width:222px;"'); ?></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Nick Name</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input class='inputs' maxlength="20" size="40" type="text" value="<?=$maintEmpObj->NickName?>"  name="txtnickname" id="txtnickname" /></td>
						<td class="headertxt">Religion</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getreligionwil(),'relCd','relDesc',''),'cmbreligion',$maintEmpObj->Religion,'class="inputs" style="width:222px;"'); ?></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Birth Place</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input class='inputs' maxlength="50" size="40" type="text" value="<?=$maintEmpObj->Bplace?>"  name="txtbplace" id="txtbplace" /></td>
						<td class="headertxt">Build</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array('','Light'=>'Light','Medium'=>'Medium','Heavy'=>'Heavy'),'cmbbuild',$maintEmpObj->Build,'class="inputs" style="width:222px;"'); ?></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Birthday</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><?	

												DropDate('Birthday',$maintEmpObj->dateOfBirth_M,$maintEmpObj->dateOfBirth_D,$maintEmpObj->dateOfBirth_Y,"onclick=\"validateCalendar('Birthday_M','Birthday_D','Birthday_Y')\" onchange=\"validateCalendar('Birthday_M','Birthday_D','Birthday_Y')\" class=\"myDatePicker\"");
												?></td>
						<td class="headertxt">Complexion</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array('','Light'=>'Light','Fair'=>'Fair','Dark'=>'Dark'),'cmbcomplexion',$maintEmpObj->Complexion,'class="inputs" style="width:222px;"'); ?></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Marital Status</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array('','SG'=>'Single','ME'=>'Married','SP'=>'Separated','WI'=>'Widow(er)'),'cmbmaritalstatus',$maintEmpObj->maritalStat,'class="inputs" style="width:222px;"  onchange="checkmarital();"'); ?></td>
						<td class="headertxt">Eye Color</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array('','Amber'=>'Amber','Blue'=>'Blue','Black'=>'Black','Brown'=>'Brown','Gray'=>'Gray','Green'=>'Green','Hazel'=>'Hazel','Red'=>'Red','Purple'=>'Purple'),'cmbeyecolor',$maintEmpObj->EyeColor,'class="inputs" style="width:222px;"'); ?></td>
					  </tr>
					  <tr> 
						<td class="headertxt">Spouse Name</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><input class='inputs' maxlength="50" size="40" type="text" value="<?=$maintEmpObj->Spouse?>" <?=$disablematstatus?>  name="txtspouse" id="txtspouse" /></td>
						<td class="headertxt">Hair</td>
						<td class="headertxt">:</td>
						<td class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array('','Blonde'=>'Blonde','Brown'=>'Brown','Black'=>'Black'),'cmbhair',$maintEmpObj->Hair,'class="inputs" style="width:222px;"'); ?></td>
					  </tr>
					  <tr>
					    <td class="headertxt">Height</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><input class='inputs' maxlength="4" type="text" value="<?=$maintEmpObj->Height?>"  name="txtheight" id="txtheight" /></td>
					    <td class="headertxt">Blood Type</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array('','A'=>'A','B'=>'B','AB'=>'A B','O'=>'O'),'cmbbloodtype',$maintEmpObj->BloodType,'class="inputs" style="width:222px;"'); ?></td>
				      </tr>
					  <tr>
					    <td class="headertxt">Weight</td>
					    <td class="headertxt">:</td>
					    <td class="gridDtlVal"><input class='inputs' maxlength="4" type="text" value="<?=$maintEmpObj->Weight?>"  name="txtweight" id="txtweight" /></td>
					    <td class="gridDtlVal">&nbsp;</td>
					    <td class="gridDtlVal">&nbsp;</td>
					    <td class="gridDtlVal">&nbsp;</td>
				      </tr>
					</table>                        </td>
                      </tr>
                    </TABLE>
				</div>
                <div id="content4" class="content4">
				<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="300" valign="top">
		                    <table width="90%" border="0" cellspacing="1" cellpadding="2">
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                       </tr>
                      <tr>
                        <td width="26%" class="headertxt">SSS</td>
                        <td width="1%" class="headertxt">:</td>
                        <td class="gridDtlVal"><input class='inputs' maxlength="10" type="text" value="<?=$maintEmpObj->SSS?>" onBlur="checkno('empSssNo',this.value,'<?=$notype?>','SSS No.','dvsss')"  name="txtsss" id="txtsss" />&nbsp;<span id="dvsss" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chsss" value="" id="chsss"></td>
                        </tr>
                      <tr>
                        <td class="headertxt">Phil Health</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><input class='inputs' maxlength="25" type="text" value="<?=$maintEmpObj->PhilHealth?>" onBlur="checkno('empPhicNo',this.value,'<?=$notype?>','Philhealth No.','dvphilhealth')" name="txtphilhealth" id="txtphilhealth" />&nbsp;<span id="dvphilhealth" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chphilhealth" value="" id="chphilhealth"></td>
                        </tr>
                      <tr>
                        <td class="headertxt">Tax ID</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><input class='inputs' maxlength="9" type="text" value="<?=$maintEmpObj->TIN?>" onBlur="checkno('empTin',this.value,'<?=$notype?>','Tax ID No.','dvtaxid')"  name="txttax" id="txttax" />&nbsp;<span id="dvtaxid" style="color:#FF0000; font-size:10px"></span><input type="hidden" name="chtaxid" value="" id="chtaxid"></td>
                        </tr>
                      <tr>
                        <td class="headertxt">HDMF</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><input class='inputs' maxlength="25" type="text" value="<?=$maintEmpObj->HDMF?>" onBlur="checkno('empPagibig',this.value,'<?=$notype?>','HDMF No.','dvhdmf')"  name="txthdmf" id="txthdmf" />&nbsp;<span id="dvhdmf" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chhdmf" value="" id="chhdmf"></td>
                        </tr>
                      <tr>
                        <td class="headertxt">Bank Account Type</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getbankwil(),'bankCd','bankDesc',''),'cmbbank',$maintEmpObj->bank,'class="inputs" style="width:222px;" onChange="checkno(\'empAcctNo\',\'\',\'' .$notype. '\',\'Account No.\',\'dvAcctNo\')"'); ?></td>
                        </tr>
                      <tr>
                        <td class="headertxt">Bank Account</td>
                        <td class="headertxt">:</td>
                        <td class="gridDtlVal"><input class='inputs' maxlength="25" type="text" value="<?=$maintEmpObj->bankAcctNo?>"  name="txtbankaccount" id="txtbankaccount" onBlur="checkno('empAcctNo',this.value,'<?=$notype?>','Account No.','dvAcctNo')" />&nbsp;<span id="dvAcctNo" style="color:#FF0000;font-size:10px"></span><input type="hidden" name="chAcctNo" value="" id="chAcctNo"></td>
                        </tr>
                      <tr>
                        <td colspan="3">                        </td>
                      </tr>                      
                    </table>                        </td>
                      </tr>
                    </TABLE>
                </div>
              <div id="content5" class="content5">
<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="300" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="62%" valign="top"><table width="99%" border="0" cellspacing="1" cellpadding="2">
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
                                <td class="headertxt">Shift</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><div id="dvshift">
                                    <? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getshiftwil($maintEmpObj->compCode),'shiftId','shiftDesc',''),'cmbshift',$maintEmpObj->Shift,'class="inputs" style="width:222px;"'); ?>
                                </div></td>
                              </tr>
                              <tr>
                                <td class="headertxt">Status</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array("0"=>"",'RG'=>'Regular','PR'=>'Probationary','CN'=>'Contractual','RS'=>'Resigned','TR'=>'Terminated','IN'=>'Inactive','AP'=>'Applicant'),'cmbstatus',$maintEmpObj->Status,'class="inputs" style="width:222px;"'); ?></td>
                              </tr>
                              <tr>
                                <td class="headertxt">Effectivity Date</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><?	

												DropDate('cmbeffectivity',$maintEmpObj->Effectivity_M,$maintEmpObj->Effectivity_D,$maintEmpObj->Effectivity_Y,"onclick=\"validateCalendar('cmbeffectivity_M','cmbeffectivity_D','cmbeffectivity_Y')\" onchange=\"validateCalendar('cmbeffectivity_M','cmbeffectivity_D','cmbeffectivity_Y')\" class=\"myDatePicker\"");
												?></td>
                              </tr>
                              <tr>
                                <td class="headertxt">Regularization</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><?	
									if (!checkdate($maintEmpObj->Regularization_M,$maintEmpObj->Regularization_D,$maintEmpObj->Regularization_Y)) { 
										$reg_D=-1;
										$reg_M=-1;
										$reg_Y=-1;
									}
									else {
										$reg_D=$maintEmpObj->Regularization_D;
										$reg_M=$maintEmpObj->Regularization_M;
										$reg_Y=$maintEmpObj->Regularization_Y;
									
									}
												DropDate('cmbregularization',$reg_M,$reg_D,$reg_Y,"onclick=\"validateCalendar('cmbregularization_M','cmbregularization_D','cmbregularization_Y')\" onchange=\"validateCalendar('cmbregularization_M','cmbregularization_D','cmbregularization_Y')\" class=\"myDatePicker\"");
												?></td>
                              </tr>
                              <tr>
                                <td class="headertxt">End Date</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><?	
								if (!checkdate($maintEmpObj->EndDate_M,$maintEmpObj->EndDate_D,$maintEmpObj->EndDate_Y)) { 
										$ed_D=-1;
										$ed_M=-1;
										$ed_Y=-1;
								}
								else {
										$ed_D=$maintEmpObj->EndDate_D;
										$ed_M=$maintEmpObj->EndDate_M;
										$ed_Y=$maintEmpObj->EndDate_Y;
								}
												DropDate('cmbenddate',$ed_M,$ed_D,$ed_Y,"onclick=\"validateCalendar('cmbenddate_M','cmbenddate_D','cmbenddate_Y')\" onchange=\"validateCalendar('cmbenddate_M','cmbenddate_D','cmbenddate_Y')\" class=\"myDatePicker\"");
												?></td>
                              </tr>
                              
                            </table></td>
                            <td width="38%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td colspan="2"><div id="RDlist"></div><div id="indicator2"></div></td>
                              </tr>
                              <tr>
                                <td width="65%">&nbsp;</td>
                                <td width="35%" class="gridDtlVal">&nbsp;</td>
                              </tr>
                              <tr>
                                <td><span class="headertxt">With Previous Employer</span></td>
                                <td class="gridDtlVal"><label>
                                  <input type="radio" name="chprev" value="Y"   <? if ($maintEmpObj->prevtag=="Y") echo "checked"?> <?=$disabled?> id="chprev" />
                                  Yes</label>
                                    <label>
                                    <input type="radio" name="chprev" <? if ($maintEmpObj->prevtag=="N") echo "checked"?>  value="N" <?=$disabled?> id="chprev" />
                                      No</label></td>
                              </tr>
                            </table></td>
  </tr>
                        </table></td>
                      </tr>
                </TABLE>                     
                </div>
              	<div id="content6" class="content6">
				<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
                      <tr> 
                        <td align="left" class="parentGridDtl" height="300" valign="top"><table width="90%" border="0" cellspacing="1" cellpadding="2">
                          <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td class="headertxt">Payroll Status</td>
                            <td class="headertxt">:</td>
                            <td class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array('','D'=>'Daily','M'=>'Monthly'),'cmbpstatus',$maintEmpObj->PStatus,' class="inputs" onChange="checkrate();" style="width:222px;"'); ?></td>
                          </tr>
                          
                          <tr>
                            <td width="26%" class="headertxt">Salary</td>
                            <td width="1%" class="headertxt">:</td>
                            <td class="gridDtlVal"><div id="dvsalary"><input class='inputs' type="text" value="<?=$maintEmpObj->Salary?>" style="<?=$visible?>"  name="txtsalary" onKeyUp="return computeRates(this.value,<?=$maintEmpObj->compCode?>,'1');" maxlength="9" id="txtsalary" readonly /></div></td>
                          </tr>
                          <tr>
                            <td width="26%" class="headertxt">Daily Rate</td>
                            <td width="1%" class="headertxt">:</td>
                            <td class="gridDtlVal"><div id="dvdailyrate"><input class='inputs' type="text" value="<?=$maintEmpObj->Drate?>" style="<?=$visible?>" onKeyUp="return computeRates(this.value,<?=$maintEmpObj->compCode?>,'0');"  name="txtdailyrate" maxlength="9" id="txtdailyrate" readonly /></DIV></td>
                          </tr>
                          <tr>
                            <td width="26%" class="headertxt">Hourly Rate</td>
                            <td width="1%" class="headertxt">:</td>
                            <td class="gridDtlVal"><input class='inputs' type="text" value="<?=$maintEmpObj->Hrate?>"  name="txthourlyrate" style="<?=$visible?>" readonly maxlength="9" id="txthourlyrate" /></td>
                          </tr>  
                              <tr>
                                <td class="headertxt">Pay Group</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><?$maintEmpObj->DropDownMenu(array('','Group 1','Group 2'),'cmbgroup',$maintEmpObj->Group,'class="inputs" style="width:222px;"'); ?></td>
                              </tr>
                              <tr>
                                <td class="headertxt">Pay Category</td>
                                <td class="headertxt">:</td>
                                <td class="gridDtlVal"><div id="divpaycat"><? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getPayCat($maintEmpObj->compCode,''),'payCat','payCatDesc',''),'cmbCategory',$maintEmpObj->paycat,'class="inputs" style="width:222px; '.$visible.'"'); ?></div></td>
                              </tr>
                                                  
                          <tr>
                            <td class="headertxt">Exemption</td>
                            <td class="headertxt">:</td>
                            <td class="gridDtlVal">
							<? $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getTEU(),'teuCode','teuDesc',''),'cmbexemption',$maintEmpObj->Exemption,'class="inputs" style="width:222px;"'); ?></td>
                          </tr>

                        </table></td>
                      </tr>
                    </TABLE> 
                </div>
                <div id="content7" class="content7">
                        <div id='divCont7'></div>
                 </div>
</td>
				</tr>
                <tr><td class="parentGridDtl" >
               
                <label>
                &nbsp;&nbsp;&nbsp;<input type="checkbox" name="chRelease" id="chRelease">
                <span class="headertxt">Release</span>&nbsp;&nbsp;</label>
              
                <input name="save"  <?=$disabled?> type="submit" class="inputs" id="save" value="Save">
                  <INPUT class="inputs" type="button" name="btnBack" id="btnBack" value="BACK" onClick="location.href='new_emp_list.php'">
                  
                   </td></tr>
			</TABLE>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	pager("contact_list_ajax.php","TSCont",'load',0,0,'','','','../../../images/');  
</SCRIPT>
<script src="../../../includes/validations.js"></script>
<script type='text/javascript' src='timesheet_js.js'></script>
<script>
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
			$('imgUsrInfo'+id).src='../../../images/folder-open.gif';
			$('usrInfo'+id).style.display='';
			Effect.SlideDown('divUsrInfo'+id,{duration:1.0}); 

		}
		else{
			$('imgUsrInfo'+id).src='../../../images/folder.gif';
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
	
	function deleUserDefMst(recNo)
	{
		var deleUserDefMst = confirm('Are you sure do you want to delete the selected record? ');
		if(deleUserDefMst == true)
		{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?&action=deleUserDefinedMast&recNo='+recNo,{
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
</script>