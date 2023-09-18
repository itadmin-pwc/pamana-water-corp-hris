<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_employee.Obj.php");


$maintEmpObj = new maintEmpObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');

//variable declaration
$company = "";
$dsable = "";
$readOnly= "";

if(isset($_GET['transType']) == 'edit' OR isset($_GET['transType']) == 'view' || isset($_GET['action']) == 'EDIT'){
	
$maintEmpObj->compCode   = (isset($_GET['cmbCompny'])) ? $_GET['cmbCompny'] : 0;
$maintEmpObj->empNo      = (isset( $_GET['txtEmpNo'])) ? $_GET['txtEmpNo'] : "";
$maintEmpObj->lName      = (isset($_GET['txtLname'])) ? $_GET['txtLname'] : "";
$maintEmpObj->fName	   	 = (isset($_GET['txtFname'])) ? $_GET['txtFname'] : "";
$maintEmpObj->mName      = (isset($_GET['txtMname'])) ? $_GET['txtMname'] : "";
$maintEmpObj->loc		 = (isset($_GET['cmbLoc'])) ? $_GET['cmbLoc'] : 0;
$maintEmpObj->branch  	 = (isset($_GET['cmbBranch'])) ? $_GET['cmbBranch'] : 0;
$maintEmpObj->div		 = (isset($_GET['cmbDiv'])) ? $_GET['cmbDiv'] : 0;
$maintEmpObj->dept		 = (isset($_GET['cmbDept'])) ? $_GET['cmbDept'] : 0;
$maintEmpObj->sect		 = (isset($_GET['cmbSect'])) ? $_GET['cmbSect'] : 0;
$maintEmpObj->position	 = (isset($_GET['txtPosition'])) ? $_GET['txtPosition'] : "";
$maintEmpObj->dateHired	 = (isset($_GET['txtDateHired'])) ? $_GET['txtDateHired'] : "";
$maintEmpObj->empStat	 = (isset($_GET['cmbEmpStat'])) ? $_GET['cmbEmpStat'] : 0;	
$maintEmpObj->dateReg	 = (isset($_GET['txtDateReg'])) ? $_GET['txtDateReg'] : "";
$maintEmpObj->resDay	 = (isset($_GET['cmbResDay'])) ? $_GET['cmbResDay'] : 0;
$maintEmpObj->TEU		 = (isset($_GET['cmbTEU'])) ? $_GET['cmbTEU'] : 0;
$maintEmpObj->TIN		 = (isset($_GET['txtTIN'])) ? $_GET['txtTIN'] : "";
$maintEmpObj->SSS		 = (isset($_GET['txtSSS'])) ? $_GET['txtSSS'] : "";
$maintEmpObj->pagIbig	 = (isset($_GET['PagIbig'])) ? $_GET['PagIbig'] : "";
$maintEmpObj->bankCode	 = (isset($_GET['cmbBankCode'])) ? $_GET['cmbBankCode'] : 0;
$maintEmpObj->bankAcctNo = (isset($_GET['txtBankAcctNo'])) ? $_GET['txtBankAcctNo'] : "";
$maintEmpObj->payGrp	 = (isset($_GET['cmbPayGrp'])) ? $_GET['cmbPayGrp'] : 0;
$maintEmpObj->payType 	 = (isset($_GET['cmbPayType'])) ? $_GET['cmbPayType'] : 0;
$maintEmpObj->payCat	 = (isset($_GET['cmbPayCat'])) ? $_GET['cmbPayCat'] : 0;
$maintEmpObj->wageTag	 = (isset($_GET['cmbWageTag'])) ? $_GET['cmbWageTag'] : 0;
$maintEmpObj->prevEmpTag = (isset($_GET['cmbPrevEmpTag'])) ? $_GET['cmbPrevEmpTag'] : 0;
$maintEmpObj->addr1		 = (isset($_GET['txtAddr1'])) ? $_GET['txtAddr1'] : "";
$maintEmpObj->addr2		 = (isset($_GET['txtAddr2'])) ? $_GET['txtAddr2'] : "";
$maintEmpObj->addr3		 = (isset($_GET['txtAddr3'])) ? $_GET['txtAddr3'] : "";
$maintEmpObj->maritalStat= (isset($_GET['cmbMaritalStat'])) ? $_GET['cmbMaritalStat'] : 0;
$maintEmpObj->sex	   	 = (isset($_GET['cmbSex'])) ? $_GET['cmbSex'] : 0;
$maintEmpObj->dateOfBirth= (isset($_GET['txtDateOfBirth'])) ? $_GET['txtDateOfBirth'] : "";
$maintEmpObj->religion	 = (isset($_GET['txtReligion'])) ? $_GET['txtReligion'] : "";
$maintEmpObj->monthlyrate= (isset($_GET['txtMonthlyRate'])) ? $_GET['txtMonthlyRate'] : "";
$maintEmpObj->dailyrate	 = (isset($_GET['txtDailyRate'])) ? $_GET['txtDailyRate'] : "";
$maintEmpObj->hourlyRate = (isset($_GET['txtHourlyRate'])) ? $_GET['txtHourlyRate'] : "";
$maintEmpObj->userLevel  = (isset($_GET['cmbUserLevel'])) ? $_GET['cmbUserLevel'] : 0;
$maintEmpObj->hdnCompCode  = (isset($_GET['hdnCompCode'])) ? $_GET['hdnCompCode']: "";
$maintEmpObj->hdnEmpNo      = (isset($_GET['txtEmpNo'])) ? $_GET['txtEmpNo'] : "";

}

if($_GET['transType'] == 'view' || $_GET['transType'] == 'edit'){
	$empNo="";
	if(isset($_GET['empNo'])){
		$empNo = $_GET['empNo'];
	}
	
	$arrEmp = $maintEmpObj->getEmployee($_SESSION['company_code'],$empNo,'');
	$arrUserLogInInfo = $maintEmpObj->getUserLogInInfo($sessionVars['compCode'],$empNo);

	$company        = $arrEmp['compCode'];
	$employeeNumber = $arrEmp['empNo'];
	$firstName      = $arrEmp['empFirstName'];
	$middleName     = $arrEmp['empMidName'];
	$lastName		= $arrEmp['empLastName'];
	$location       = $arrEmp['empLocCode'];
	$branch         = $arrEmp['empBrnCode'];
	$division       = $arrEmp['empDiv'];
	$department		= $arrEmp['empDepCode'];
	$section        = $arrEmp['empSecCode'];
	$position       = $arrEmp['empPosDesc'];
	$dateHired      = date("m/d/Y",strtotime($arrEmp['dateHired']));
	$status         = $arrEmp['empStat'];
	$DateRegular = (date("m/d/Y",strtotime($arrEmp['dateReg'])) == '01/01/1970') ? '' : date("m/d/Y",strtotime($arrEmp['dateReg']));
	$restDay        = $arrEmp['empRestDay'];
	$taxExemption   = $arrEmp['empTeu'];
	$tinNumber      = $arrEmp['empTin'];
	$sssNumber           = $arrEmp['empSssNo']; 
	$pagIbigNumber       = $arrEmp['empPagibig']; 
	$bankCode  			 = $arrEmp['empBankCd']; 	
	$bankAccountNumber   = $arrEmp['empAcctNo']; 
	$payGroup 			 = $arrEmp['empPayGrp']; 
	$payType			 = $arrEmp['empPayType']; 
	$payCategoty    	 = $arrEmp['empPayCat']; 
	$wageTag 			 = $arrEmp['empWageTag']; 
	$previousEmpTag      = $arrEmp['empPrevTag']; 
	$address1			 = $arrEmp['empAddr1']; 
	$address2			 = $arrEmp['empAddr2']; 
	$address3			 = $arrEmp['empAddr3']; 	
	$maritalStatus		 = $arrEmp['empMarStat']; 
	$sex				 = $arrEmp['empSex']; 
	$DateOfBirth		 = date("m/d/Y",strtotime($arrEmp['empBday']));; 
	$religion			 = $arrEmp['empReligion']; 
	$monthlyRate	     = $arrEmp['empMrate']; 
	$dailyRate 			 = $arrEmp['empDrate']; 
	$hourlyRate			 = $arrEmp['empHrate']; 
	$userLevel           = $arrUserLogInInfo['userLevel']; 
	
	$hdnCompCode = $arrEmp['compCode'];
	$hdnEmpNo  = $arrEmp['empNo'];

	$dsable = 'disabled';
	$readOnly = 'readOnly';
}


if(isset($_GET['action'])){
	switch ($_GET['action']){
		case 'populateDept':
			switch ($_GET['parentObj']){
				case 'cmbDiv':
					echo	$maintEmpObj->DropDownMenu(
								$maintEmpObj->makeArr(
									$maintEmpObj->getDepartment($sessionVars['compCode'],$_GET['divVal'],'','','2'),
									'deptCode','deptDesc',''
								),
								'cmbDept','','class="inputs" style="width:222px;" onchange="populateDept(this.id,\'sectCont\')"'
						  	);
					exit();
				break;
			}
			switch ($_GET['parentObj']){
				case 'cmbDept':
					echo	$maintEmpObj->DropDownMenu(
								$maintEmpObj->makeArr(
									$maintEmpObj->getDepartment($sessionVars['compCode'],$_GET['divVal'],$_GET['deptVal'],'','3'),
									'sectCode','deptDesc',''
								),
								'cmbSect','','class="inputs" style="width:222px;" onchange="populateDept(this.id,\'\')"'
						  	);				
					exit();
				break;
			}
			exit();
		break; 
		case 'computeRates':
				$getCompInfo = $maintEmpObj->getCompany($sessionVars['compCode']);
				$Drate = sprintf('%01.2f',(float)$_GET['monthRate']/(float)$getCompInfo['compNoDays']);
				$Hrate =  sprintf('%01.2f',$Drate/8);
				echo "$('txtDailyRate').value=$Drate;";
				echo "$('txtHourlyRate').value=$Hrate;";
			exit();
		break;
		case 'ADD':
				if($maintEmpObj->checkEmployee() > 0){
					echo "alert('Employee Already Exist');";
				}
				else{
					if($maintEmpObj->addEmployee() == true){
						$userLogInInfo = $maintEmpObj->getUserLogInInfo($_GET['cmbCompny'],$_GET['txtEmpNo']);
						$userPass = base64_decode($userLogInInfo[userPass]);
						echo "alert('New Employee Successfully Saved');";
						echo "alert('Password : $userPass');";
						//echo "location.href='maintenance_employee.php'";
					}
					else {
						echo "alert('New Employee Failed Saved');";
					}
				}
				exit();
		break;
		case 'EDIT':
				if($maintEmpObj->updateEmployee() == true){
					echo "alert('Employee Successfully Updated');";
					//echo "location.href='maintenance_employee.php'";
				}
				else{
					echo "alert('Employee Failed for Update');";
				}
				
				exit();
		break;
	}
}

//if transtype is add
$cureDate = date("m/d/Y");
$dateHired = $cureDate;
$DateOfBirth = $cureDate;
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
			
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url('../../../js/themes/alphacube.css');</STYLE>		
		<link href="../../../js/themes/alert.css" rel="stylesheet" type="text/css">
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
		
		<STYLE>@import url('../../style/payroll.css');</STYLE>
	</HEAD>
	<BODY>
		<FORM name="frmViewEditEmp" id="frmViewEditEmp" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					<td colspan="4" class="parentGridHdr">
						&nbsp;<img src="../../../images/grid.png">&nbsp;<?=ucfirst($_GET['transType'])?> Employee
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
					
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1">
							<tr>
								<td width="50%">
									
									<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
										<tr>
											<td width="1%" class="infoLabel" align="center" colspan="3">GENERAL INFORMATION</td>
										</tr>
										<tr>
											<td width="28%" class="gridDtlLbl" align="left">Company</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
													
													<?$maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getCompany(''),'compCode','compName',''),'cmbCompny',$company,'class="inputs" '.$dsable); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Employee No.</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtEmpNo" id="txtEmpNo" value="<?=$employeeNumber?>" <?=$readOnly?>>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">First Name</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtFname" id="txtFname" value="<?=htmlspecialchars($firstName)?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Middle Name</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtMname" id="txtMname" value="<?=htmlspecialchars($middleName)?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Last Name</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtLname" id="txtLname" value="<?=htmlspecialchars($lastName)?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Location</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranch($sessionVars['compCode']),'brnCode','brnDesc',''),'cmbLoc',$location,'class="inputs" style="width:260px;"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Branch</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranch($sessionVars['compCode']),'brnCode','brnDesc',''),'cmbBranch',$branch,'class="inputs" style="width:260px;"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Division</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?
														$maintEmpObj->DropDownMenu(
															$maintEmpObj->makeArr(
																$maintEmpObj->getDepartment($sessionVars['compCode'],$division,'','','1'),
																'divCode','deptDesc',''
															),
															'cmbDiv',$division,'class="inputs" style="width:222px;" onchange="populateDept(this.id,\'deptCont\')" '
													  	); 
													?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Department</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												<font id="deptCont">
													<?
														$maintEmpObj->DropDownMenu(
															$maintEmpObj->makeArr(
																$maintEmpObj->getDepartment($sessionVars['compCode'],$division,$department,'','2'),
																'deptCode','deptDesc',''
															),
														'cmbDept',$department,'class="inputs" style="width:222px;" onchange="populateDept(this.id,\'sectCont\')"'
														);
													?>
												</font>
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Section</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												<font id="sectCont">
													<?
														$maintEmpObj->DropDownMenu(
															$maintEmpObj->makeArr(
																$maintEmpObj->getDepartment($sessionVars['compCode'],$division,$department,$section,'3'),
																'sectCode','deptDesc',''
															),
															'cmbSect',$section,'class="inputs" style="width:222px;" onchange="populateDept(this.id,\'\')"'
														);
													?>
												</font>
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Postition</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtPosition" id="txtPosition" value="<?=htmlspecialchars($position)?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Date Hired</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
													<input value="<?=$dateHired?>" type='text' class='inputs' name='txtDateHired' id='txtDateHired' maxLength='10' readonly size="10"/> 
								          			<a href="#"><img name="imgDateHired" id="imgDateHired" src="../../../images/cal_new.png" title="Date Hired" style="cursor: pointer;position:relative;top:3px;border:none;"></a>								          		
								          	</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Status</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu($maintEmpObj->getArrEmpStatus(),'cmbEmpStat',$status,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Reg. Date</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<input value="<?=$DateRegular?>" type='text' class='inputs' name='txtDateReg' id='txtDateReg' maxLength='10' readonly size="10"/> 
								          			<a href="#"><img name="imgDateReg" id="imgDateReg"  src="../../../images/cal_new.png" title="Date Hired" style="cursor: pointer;position:relative;top:3px; border:none;"></a>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Rest Day</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu($maintEmpObj->getArrRestDay(),'cmbResDay',$restDay,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Tax Exemption</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getTEU(),'teuCode','teuDesc',''),'cmbTEU',$taxExemption,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">TIN NO.</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtTIN" id="txtTIN" maxlength="20" value="<?=$tinNumber?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">SSS NO.</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtSSS" id="txtSSS" maxlength="20" value="<?=$sssNumber?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Pag-Ibig NO.</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="PagIbig" id="PagIbig" maxlength="20" value="<?=$pagIbigNumber?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Bank Code</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getPayBank($sessionVars['compCode']),'bankCd','bankDesc',''),'cmbBankCode',$bankCode,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Bank Acct NO.</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtBankAcctNo" id="txtBankAcctNo" maxlength="30" value="<?=$bankAccountNumber?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Pay Group</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu(array('','Group 1','Group 2'),'cmbPayGrp',$payGroup,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Pay Type</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu(array('','D'=>'Daily','M'=>'Monthly'),'cmbPayType',$payType,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Pay Category</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getPayCat($sessionVars['compCode'],''),'payCat','payCatDesc',''),'cmbPayCat',$payCategoty,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Wage Tag</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu(array('','Y'=>'Minimun Wage Earner'),'cmbWageTag',$wageTag,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Prev. Employer Tag</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu(array('','Y'=>'w/ Prev. Employer w/in the Year'),'cmbPrevEmpTag',$previousEmpTag,'class="inputs"'); ?>
												
											</td>
										</tr>
									</TABLE>
									
								</td>
								<td valign="top" align="center">
																
									<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
										<tr>
											<td width="1%" class="infoLabel" align="center" colspan="3">PERSONAL INFORMATION</td>
										</tr>
										<tr>
											<td width="25%" class="gridDtlLbl" align="left">Address 1</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtAddr1" id="txtAddr1" size="40" value="<?=htmlspecialchars($address1)?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Address 2</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtAddr2" id="txtAddr2" size="40" value="<?=htmlspecialchars($address2)?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Address 3</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtAddr3" id="txtAddr3" size="40" value="<?=htmlspecialchars($address3)?>">
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Marital Status</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu(array('','SG'=>'Single','ME'=>'Merried','SP'=>'Separated','WI'=>'Widow(er)'),'cmbMaritalStat',$maritalStatus,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Sex</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu(array('','M'=>'Male','F'=>'Female'),'cmbSex',$sex,'class="inputs"'); ?>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Date Of Birth</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
													<input value="<?=$DateOfBirth?>" type='text' class='inputs' name='txtDateOfBirth' id='txtDateOfBirth' maxLength='10' readonly size="10"/> 
								          			<a href="#"><img name="imgDateOfBirth" id="imgDateOfBirth" type="image" src="../../../images/cal_new.png" title="Date Hired" style="cursor: pointer;position:relative;top:3px; border:none;"></a>											
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Religion</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtReligion" id="txtReligion" maxlength="40" value="<?=htmlspecialchars($religion)?>">
												
											</td>
										</tr>
										<tr>
											<td width="1%" class="infoLabel" align="center" colspan="3">SALRY RELATED INFORMATION</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Monthly Rate</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtMonthlyRate" id="txtMonthlyRate" value="<?=$monthlyRate?>" onkeyup="return computeRates(this.value);">
													<font size="2" color="#6699FF" id="rateIndicator"></font>
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Daily Rate</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtDailyRate" id="txtDailyRate" value="<?=$dailyRate?>" readonly>
												
											</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">Hourly Rate</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<INPUT class="inputs" type="text" name="txtHourlyRate" id="txtHourlyRate" value="<?=$hourlyRate?>" readonly> 
												
											</td>
										</tr>
										<tr>
											<td width="1%" class="infoLabel" align="center" colspan="3">USER LOG IN INFORMATION</td>
										</tr>
										<tr>
											<td width="10%" class="gridDtlLbl" align="left">User Level</td>
											<td width="1%" class="gridDtlLbl" align="center">:</td>
											<td class="gridDtlVal">
												
													<?$maintEmpObj->DropDownMenu(array('','1'=>'Superuser','2'=>'Admin','3'=>'User'),'cmbUserLevel',$userLevel,'class="inputs"'); ?>
												
											</td>
										</tr>
									</TABLE>
									<br>
									<FIELDSET class="childGrid">
										<LEGEND class="gridDtlLbl">Controls</LEGEND>
										
											<?
											$disable ="";
											$btnTrans="";
											 $_GET['transType'];
												if($_GET['transType'] == 'add'){ $btnTrans = "ADD"; }
												if ($_GET['transType'] == 'edit'){$btnTrans = "EDIT";}
												if($_GET['transType'] == 'view'){ $disable = 'disabled'; $btnTrans = 'VIEW';}
											?>										
											<INPUT class="inputs" type="button" name="action" id="action" value="<?=$btnTrans?>" onclick="return validateEmpoyee('AddEmp');" <?=$disable?>>
											<INPUT class="inputs" type="button" name="btnBack" id="btnBack" value="BACK" onclick="location.href='maintenance_employee.php'">
									</FIELDSET>
									<div id="transIndicator"></div>
								</td>
							</tr>
						</TABLE>
							
						
					</td>
				</tr>
			</TABLE>
			<INPUT type="hidden" name="hdnCompCode" id="hdnCompCode" value="<?=$hdnCompCode?>">
			<INPUT type="hidden" name="hdnEmpNo" id="hdnEmpNo" value="<?=$hdnEmpNo?>">
			<?$maintEmpObj->disConnect();?>
		</form>
	</BODY>
</HTML>
<SCRIPT>
		
	Calendar.setup({
			  inputField  : "txtDateHired",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgDateHired"       // ID of the button
		}
	)
	Calendar.setup({
			  inputField  : "txtDateReg",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",       // the date format
			  button      : "imgDateReg"      // ID of the button
		}
	)
	
	Calendar.setup({
			  inputField  : "txtDateOfBirth",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",       // the date format
			  button      : "imgDateOfBirth"       // ID of the button
		}
	)

	function populateDept(parentObjID,ReplaceObjId){

		var divVal = $F('cmbDiv'); 
		var divDept = $F('cmbDept');
		var divSect = $F('cmbSect');
		if(parentObjID == 'cmbDiv'){
			$('cmbSect').value=0;				 	
		}
		if(parentObjID == 'cmbDept'){
			if(divVal == 0){
				$('cmbDept').value=0;
				alert('Select Division First');
				$('cmbDiv').focus();
				return false;
			}
		}
		if(parentObjID == 'cmbSect'){
			if(divDept == 0){
				$('cmbSect').value=0;
				alert('Select Department First');
				$('cmbDept').focus();
				return false;
			}			
		}
		
		var params = '?action=populateDept&parentObj='+parentObjID+"&divVal="+divVal+"&deptVal="+divDept+"&sectVal="+divSect;

		var url = '<?=$_SERVER['PHP_SELF']?>'+params;

		var a = new Ajax.Request(url,{
			method : 'get',
			onComplete : function (req){
				$(ReplaceObjId).innerHTML=req.responseText;	
			},
			onCreate : function (){
				$(ReplaceObjId).innerHTML="<img src='../../../images/wait.gif'>";
			}
		});
	}
	
	function computeRates(mnthRate){
		
		params = '?action=computeRates&monthRate='+mnthRate;
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+params,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);	
			},
			onCreate : function (){
				$('rateIndicator').innerHTML="Loding...";
			},	
			onSuccess : function (){
				$('rateIndicator').innerHTML="";
			}		
		})
	}
	
	function validateEmpoyee(act){
		
		var empInputs = $('frmViewEditEmp').serialize(true);
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2})$/;
		var sssExp     = /[0-9]{2}-[0-9]{7}-[0-9]{1}/;
		var tinExp     = /[0-9]{3}-[0-9]{3}-[0-9]{3}/;
		var numericExp = /[0-9]+/;
		
		if(empInputs['cmbCompny'] == 0){
			alert('Company is Required');
			$('cmbCompny').focus();
			return false;
		}

		if(trim(empInputs['txtEmpNo']) == ""){
			alert('Employee Numbers is Required');
			$('txtEmpNo').focus();
			return false;
		}
		if(!empInputs['txtEmpNo'].match(numericExp)){
			alert('Invalid Employee Numbers\nValid : Numbers Only');
			$('txtEmpNo').focus();
			return false;
		}		
		if(trim(empInputs['txtFname']) == ""){
			alert('Employee First Name is Required');
			$('txtFname').focus();
			return false;
		}
		if(trim(empInputs['txtLname']) == ""){
			alert('Employee Last Name is Required');
			$('txtLname').focus();
			return false;
		}
		if(empInputs['cmbLoc'] == 0){
			alert('Location is Required');
			$('cmbLoc').focus();
			return false;
		}
		if(empInputs['cmbBranch'] == 0){
			alert('Brach is Required');
			$('cmbBranch').focus();
			return false;
		}
		if(empInputs['cmbDiv'] == 0){
			alert('Divesion is Required');
			$('cmbDiv').focus();
			return false;
		}
		if(empInputs['cmbDept'] == 0){
			alert('Department is Required');
			$('cmbDept').focus();
			return false;
		}
		if(empInputs['cmbSect'] == 0){
			alert('Section is Required');
			$('cmbSect').focus();
			return false;
		}
		if(empInputs['cmbEmpStat'] == 0){
			alert('Employee Status is Required');
			$('cmbEmpStat').focus();
			return false;
		}
		if(empInputs['cmbResDay'] == 0){
			alert('Rest Day is Required');
			$('cmbResDay').focus();
			return false;
		}
		if(empInputs['cmbTEU'] == 0){
			alert('Tax Exemption Unit is Required');
			$('cmbTEU').focus();
			return false;
		}
		if(trim(empInputs['txtTIN']) == ""){
			alert('TIN Number is Required');
			$('txtTIN').focus();
			return false;
		}
		if(!empInputs['txtTIN'].match(tinExp)){
			alert('Invalid TIN Number\nvalid : 123-123-123');
			$('txtTIN').focus();
			return false;			
		}
		if(trim(empInputs['txtSSS']) == ""){
			alert('SSS Number is Required');
			$('txtSSS').focus();
			return false;
		}
		if(!empInputs['txtSSS'].match(sssExp)){
			alert('Invalid SSS Number\nvalid : 12-1234567-1');
			$('txtSSS').focus();
			return false;
		}
		if(trim(empInputs['PagIbig']) != ""){
			if(!empInputs['PagIbig'].match(numericExp)){
				alert('Invalid Pagibig Number\nvalid : Numbers Only');
				$('PagIbig').focus();
				return false;			
			}
		}
		if(trim(empInputs['txtBankAcctNo']) != ""){
			if(!empInputs['txtBankAcctNo'].match(numericExp)){
				alert('Invalid Bank Account Number\nvalid : Numbers Only');
				$('txtBankAcctNo').focus();
				return false;			
			}
		}
		if(empInputs['cmbPayGrp'] == 0){
			alert('Pay Group is Required');
			$('cmbPayGrp').focus();
			return false;
		}
		if(empInputs['cmbPayType'] == 0){
			alert('Pay Type is Required');
			$('cmbPayType').focus();
			return false;
		}
		if(empInputs['cmbPayCat'] == 0){
			alert('Pay Category is Required');
			$('cmbPayCat').focus();
			return false;
		}
		if(trim(empInputs['txtAddr1']) == ""){
			alert('Address 1 is Required');
			$('txtAddr1').focus();
			return false;
		}
		if(empInputs['cmbSex'] == 0){
			alert('Sex is Required');
			$('cmbSex').focus();
			return false;
		}
		if(empInputs['cmbSex'] == 0){
			alert('Sex is Required');
			$('cmbSex').focus();
			return false;
		}
		if(trim(empInputs['txtMonthlyRate']) == "" || empInputs['txtMonthlyRate'] == 0){
			alert('Monthly Rate is Required');
			$('txtMonthlyRate').focus();
			return false;
		}
		if(!empInputs['txtMonthlyRate'].match(numericExpWdec)){
			alert('Invalid Monthly Rate\nvalid : Numbers Only with two(2) decimal or without decimal');
			$('txtMonthlyRate').focus();
			return false;
		}
		if(empInputs['cmbUserLevel'] == 0){
			alert('User Level is Required');
			$('cmbUserLevel').focus();
			return false;
		}
	
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>',{
			method : 'get',
			parameters : $('frmViewEditEmp').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			},
			onCreate : function (){
				$('transIndicator').innerHTML="<img src='../../../images/wait.gif'>Loading...";
				$('action').disabled=true;
				$('btnBack').disabled=true;
			},
			onSuccess : function (){
				$('transIndicator').innerHTML="";
				$('action').disabled=false;
				$('btnBack').disabled=false;
			}
		});
	}
	

</SCRIPT>