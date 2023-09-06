<?
//Programmer : vincent c de torres
//Date       : Aug 24 ,2009
//Module     : Log In
session_start();
$_SESSION['company_code']    = ($_GET['compCode']!=""?$_GET['compCode']:$_GET['cmbCompny']);
include("index.obj.php");
include("includes/common.php");

$arrModuleName = array(
					"",
					"Time and Attendance",
					"201",
					"Payroll"
				);
			
$common = new commonObj();			
$indexObj = new indexObj();

$common->validateSessions($_GET['logOut'],'LOGIN');

$indexObj->module   = $_GET['cmbModuleName'];
$indexObj->compCode = 1;
$indexObj->empNo    = $_GET['txtEmpId'];
$indexObj->userPass = $_GET['txtUserPass'];
$indexObj->payCat = $_GET['cmbPayCategory'];
$arrPayGrp = array("","Group 1","Group 2");
$arrPayCat = array("");
	
if($_GET['action'] == 'populatPayCat'){

	$arrPayCat = $common->getPayCat('1','');
	echo $indexObj->DropDownMenu($common->makeArr($arrPayCat,'payCat','payCatDesc',''),'cmbPayCategory','','class="logInInputs" onKeyPress="return Enter(this, event);"');
	exit();
}

if($_GET['btnLogIn'] == 'LOG IN'){
	$payCatChck = 0;
	$logInRes = $indexObj->validateLogIn();	
	$arrPayCat = explode(',', $logInRes['category']);
	if ($_GET['cmbModuleName'] == 3) {
		if (!in_array((int)$_GET['cmbPayCategory'], explode(',',$logInRes['category']))) {
			$payCatChck = 1;
		}
	}
	
	if($logInRes == 0 || $logInRes == "" || empty($logInRes) || $payCatChck == 1){
		//failed log in
		echo "$('errMsg').innerHTML='USER IS INVALID!';";
		echo "$('txtEmpId').focus();";
	}
	else{
		$getUserInfo = $common->getUserInfo($logInRes['compCode'],$logInRes['empNo'],'');
		
		if($getUserInfo == 0 || $getUserInfo == "" || empty($getUserInfo)){
			//echo "alert('User Doesnt Exist in Employee Master Table');";
			echo "$('errMsg').innerHTML='USER IS INVALID!';";

			echo "$('txtEmpId').focus();";			
		}
		else {
			//successfull log in
			$_SESSION['employee_id']     = $getUserInfo['id'];
		    $_SESSION['user_id']	 	 = $logInRes['userId'];
			$_SESSION['module_id']       = $_GET['cmbModuleName'];
			$_SESSION['company_code']    = 1;//$logInRes['compCode'];
			$_SESSION['employee_number'] = $logInRes['empNo'];
			$_SESSION['user_level']      = $logInRes['userLevel'];
			$_SESSION['user_payCat']	 = $logInRes['category'];
			$_SESSION['user_release']     = $logInRes['releaseTag'];	
			$_SESSION['user_telcoaccess'] =	$logInRes['telcoaccess'];
			$_SESSION['Confiaccess']	= $logInRes['confiaccess'];		
			$_SESSION['branchCode']      = $getUserInfo['empBrnCode'];
			//if module is payroll make session for paygroup and pay 
			if($_GET['cmbModuleName'] == 3){
				$_SESSION['pay_group']    =1; //$_GET['cmbPayGroup'];
				$_SESSION['pay_category'] = $_GET['cmbPayCategory'];
			}			
			
			$indexObj->accessModule();
		}
	}
	exit();
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<LINK rel="SHORTCUT ICON" href="images/logo-si.png">
		<SCRIPT type="text/javascript" src="js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<script type="text/javascript" src="js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="js/extjs/adapter/prototype/window_effects.js"></script>
		<SCRIPT type="text/javascript" src="includes/jSLib.js"></SCRIPT>
			
		<STYLE>@import url('js/themes/default.css');</STYLE>
		<STYLE>@import url('js/themes/alphacube.css');</STYLE>
		<STYLE>@import url('index_style.css');</STYLE>		
	</HEAD>
	<BODY>
		<FORM name='frmLogIn' id="frmLogIn" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div class="logInCont" id="logInCont"></div>
			<TABLE border="0" class="InnerTblLogInCont" align="center" id="InnerTblLogInCont">
				<tr>
					<td align="center" colspan="3" style="vertical-align: middle;">
						<img src="images/pamana-logo.png" width="30" height="30"> <span style="font-size: 30px; color: #000066;"><strong>PAMANA HRIS</strong></span>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">					
						<span style="color:white">...</span>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">					
						<FONT style="color: #000066" size="5"><b>LOG IN YOUR ACCOUNT</b></font>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">					
						<span style="color:white">...</span>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="logInLabel">					
						MODULE<br>		
						<?$indexObj->DropDownMenu($arrModuleName,'cmbModuleName','','class="logInInputs" onchange="viewPayrollInputs(this.value);populatePayCat(1);"'); ?>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="logInLabel">		
						<br>			
						COMPANY <br>
						<INPUT style="width:100%;" type="text" name="cmbCompny"  id="cmbCompny" on onKeyPress="" class="logInInputs" value="PAMANA WATER CORP." readonly  >

						<?//=$indexObj->DropDownMenu($common->makeArr($common->getCompany(''),'compCode','compName',''),'cmbCompny','','class="logInInputs" onchange="populatePayCat(this.value)"');?>
					</td>
				</tr>
				<tr>
					<td class="logInLabel" colspan="3">	
						<br>				
						EMPLOYEE ID <br>				
						<INPUT type="text" style="width:100%;" name="txtEmpId" id="txtEmpId" class="logInInputs">
					</td>
				</tr>
				<tr>
					<td class="logInLabel" colspan="3">			
						<br>		
						PASSWORD <br>			
						<INPUT type="password" name="txtUserPass" style="width:100%;" id="txtUserPass" onKeyPress="return Enter(this, event);" class="logInInputs">
					</td>
				</tr>
				<tr style="display:none;" id="payGrpRow">
					<td class="logInLabel">	
					<br>				
						Pay Group <br>
					<INPUT style="width:100%;" type="text" name="cmbPayGroup"  id="cmbPayGroup" on onKeyPress="" class="logInInputs" value="Group 1" readonly  >
				
						<?//$indexObj->DropDownMenu($arrPayGrp,'cmbPayGroup','','class="logInInputs"'); ?>
					</td>
				</tr>
				<tr style="display:none;" id="payCatRow">
					<td class="logInLabel">		
					<br>			
						Pay Category <br>			
						<div id="payCatDiv"><?$indexObj->DropDownMenu($arrPayCat,'cmbPayCategory','','class="logInInputs"  onclick="checkComp()" style="width:200px;"'); ?></div>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<br>
						<INPUT type="button" name="btnLogIn" id="btnLogIn" value="LOG IN" class="logInInputs" onClick="return validateLogIn();">
						<!-- <INPUT type="button" name="btnReset" id="btnReset" value="CLEAR" class="logInInputs" onClick="resetInputs()"> -->
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center" >
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center" class="errMsg" height="30">
						<div id="errMsg"></div>
					</td>
				</tr>
			</TABLE>
		</form>
		<?$common->disConnect()?>
	</BODY>
</HTML>
<SCRIPT type="text/javascript" >
	
	var win = new Window({
		parent : $('logInCont'),
		id : "logIn",
		className: "alphacube", 
		resizable: false, 
		draggable:false, 
		minimizable : false,
		maximizable : false,
		closable 	: false,
		width: 500,
		height : 580
	});
		win.setContent('InnerTblLogInCont', false, false)		
		win.showCenter();
		win.setDestroyOnClose();
		
	disableRightClick();	

	function viewPayrollInputs(ModuleId){
		if(ModuleId == 3){
			$('payGrpRow').style.display='';
			$('payCatRow').style.display='';
		}
		else{
			$('payGrpRow').style.display='none';
			$('payCatRow').style.display='none';
		}
	}
		
	function checkComp(){
		var Compny = $F('cmbCompny');
		if(Compny == 0 || Compny == ""){
			$('errMsg').innerHTML='COMPANY IS REQUIRED!';
			$('cmbCompny').focus();
			return false;			
		}
	}
	
	function resetInputs(){
		$('cmbModuleName').value=0;
		$('cmbCompny').value="PAMANA WATER CORP.";
		$('txtEmpId').value="";
		$('txtUserPass').value="";	
		$('cmbPayGroup').value=0;
		$('cmbPayCategory').value=0;	
		$('payGrpRow').style.display='none';
		$('payCatRow').style.display='none';
	}	

	function populatePayCat(compCode){
		var params = '?action=populatPayCat&compCode='+compCode;
		var url = '<?=$_SERVER['PHP_SELF']?>'+params;
		
		new Ajax.Request(url,{
			method : 'get',
			onComplete : function (req){
				$('payCatDiv').innerHTML=req.responseText;	
			},
			onCreate : function (){
				$('payCatDiv').innerHTML="<img src='images/wait.gif'>";
			}			
		});		
	}		
	
	
	function viewPage(logInCnt,title,progrm){
		$(logInCnt).style.display=''; 
		pageInfo(title,progrm); 
		return false;
	}
	
	function pageInfo(title,page){
		$('logInTitle').innerHTML = title;
		$('hdnPage').value=page;
		$('cmbCompny').focus();
	}

	
	function validateLogIn() {
		
		var moduleName = $F('cmbModuleName');
		var Compny     = $F('cmbCompny');
		var EmpId      = $F('txtEmpId');
		var UserPass   = $F('txtUserPass');
		var NumerixExp = /^[\d]+$/;
		
		if(moduleName == '' || moduleName == 0){
			$('errMsg').innerHTML='PLEASE SELECT MODULE!';
			$('cmbModuleName').focus();
			return false;
		}
		
		if(Compny == '' || Compny == 0){
			$('errMsg').innerHTML='PLEASE SELECT COMPANY!';
			$('cmbCompny').focus();
			return false;
		}
		
		if(EmpId == ''){
			$('errMsg').innerHTML='EMPLOYEE ID IS REQUIRED!';
			$('txtEmpId').focus();
			return false;			
		}
		else{
			if(!EmpId.match(NumerixExp)){
				$('errMsg').innerHTML='INVALID EMPLOYEE ID!';
				$('txtEmpId').focus();
				return false;				
			}	
		}
		
		if(UserPass == ''){
			$('errMsg').innerHTML='PASSWORD IS REQUIRED!';
			$('txtUserPass').focus();
			return false;				
		}
		
		if(moduleName == 3){
			var PayGroup = document.getElementById("cmbPayGroup");
			var PayCategory = $F('cmbPayCategory');
			
			if(PayGroup == "" || PayGroup == 0){
				$('errMsg').innerHTML='PAYGROUP IS REQUIRED!';
				$('cmbPayGroup').focus();
				return false;				
			}
			if(PayCategory == "" || PayCategory == 0){
				$('errMsg').innerHTML='PAY CATEGORY IS REQUIRED!';
				$('cmbPayCategory').focus();
				return false;				
			}
		
		}
		
		var url = '<?=$_SERVER['PHP_SELF']?>';
		var params = $('frmLogIn').serialize();
		
		new Ajax.Request(url,{
			parameters : params,
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
			},
			onCreate : function (){
				$('errMsg').innerHTML="<img src='images/wait.gif'>";
			},
			onSuccess : function (){
				$('errMsg').innerHTML="";
			}
		});
	}
	function Enter(field, event) {
	  	var key, keyChar;
	  	if (window.event)
			key = window.event.keyCode;
	  	else if (event)
			key = event.which;
	  	else
			return true;	
		if (key == null || key == 0 || key == 8 || key == 27 || key == 13) {
			if (key == 13) {
				validateLogIn();	
			}
		 }	
	}
	
</SCRIPT>