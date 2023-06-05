<?
//Programmer : vincent c de torres
//Date       : Aug 24 ,2009
//Module     : Log In
session_start();
include("index.obj.php");
include("includes/common.php");

$arrModuleName = array(
					"",
					"Time And Attendance",
					"201",
					"Payroll"
				);
			
$common = new commonObj();			
$indexObj = new indexObj();

$common->validateSessions($_GET['logOut'],'LOGIN');

$indexObj->module   = $_GET['cmbModuleName'];
$indexObj->compCode = $_GET['cmbCompny'];
$indexObj->empNo    = $_GET['txtEmpId'];
$indexObj->userPass = $_GET['txtUserPass'];
$indexObj->payCat = $_GET['cmbPayCategory'];
$arrPayGrp = array("","Group 1","Group 2");
$arrPayCat = array("");
	
if($_GET['action'] == 'populatPayCat'){

	$arrPayCat = $common->getPayCat($_GET['compCode'],'');
	echo $indexObj->DropDownMenu($common->makeArr($arrPayCat,'payCat','payCatDesc',''),'cmbPayCategory','','class="logInInputs"');
	exit();
}

if($_GET['btnLogIn'] == 'LOG IN'){
	$payCatChck = 0;
	$logInRes = $indexObj->validateLogIn();	
	$arrPayCat = explode(',',$logInRes['category']);
	if ($_GET['cmbModuleName'] == 3) {
		if (!in_array((int)$_GET['cmbPayCategory'],explode(',',$logInRes['category']))) {
			$payCatChck = 1;
		}
	}
	if($logInRes == 0 || $logInRes == "" || empty($logInRes) || $payCatChck == 1){
		//failed log in
		echo "$('errMsg').innerHTML='<blink>Access Denied!</blink>';";
		echo "$('txtEmpId').focus();";
	}
	else{
		$getUserInfo = $common->getUserInfo($logInRes['compCode'],$logInRes['empNo'],'');
		
		if($getUserInfo == 0 || $getUserInfo == "" || empty($getUserInfo)){
			//echo "alert('User Doesnt Exist in Employee Master Table');";
			echo "$('errMsg').innerHTML='<blink>User Doesn\'t Exist in Employee Master Table</blink>';";

			echo "$('txtEmpId').focus();";			
		}
		else {
			//successfull log in
			$_SESSION['employee_id']     = $getUserInfo['id'];
		    $_SESSION['user_id']	 	 = $logInRes['userId'];
			$_SESSION['module_id']       = $_GET['cmbModuleName'];
			$_SESSION['company_code']    = $logInRes['compCode'];
			$_SESSION['employee_number'] = $logInRes['empNo'];
			$_SESSION['user_level']      = $logInRes['userLevel'];
			$_SESSION['user_payCat']	 = $logInRes['category'];
					
			//if module is payroll make session for paygroup and pay 
			if($_GET['cmbModuleName'] == 3){
				$_SESSION['pay_group']    = $_GET['cmbPayGroup'];
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
		<LINK rel="SHORTCUT ICON" href="images/logo-pg-si.png">
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
					<td align="center" colspan="3">
						<img src="images/pglogo.jpg">
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">					
						<FONT style="color: #000066" size="5"><b>LOG IN</b></font>
					</td>
				</tr>
				<tr>
					<td class="logInLabel">					
						Module
					</td>
					<td class="logInLabel" width="1">					
						:
					</td>
					<td>					
						<?$indexObj->DropDownMenu($arrModuleName,'cmbModuleName','','class="logInInputs" onchange="viewPayrollInputs(this.value)"'); ?>
					</td>
				</tr>
				<tr>
					<td class="logInLabel">					
						Company
					</td>
					<td class="logInLabel" width="1">					
						:
					</td>
					<td>					
						<?$indexObj->DropDownMenu($common->makeArr($common->getCompany(''),'compCode','compName',''),'cmbCompny','','class="logInInputs" onchange="populatePayCat(this.value)"');?>
					</td>
				</tr>
				<tr>
					<td class="logInLabel">					
						Employee Id
					</td>
					<td class="logInLabel">					
						:
					</td>
					<td >					
						<INPUT type="text" name="txtEmpId" id="txtEmpId" class="logInInputs">
					</td>
				</tr>
				<tr>
					<td class="logInLabel">					
						Password
					</td>
					<td class="logInLabel">					
						:
					</td>
					<td >					
						<INPUT type="password" name="txtUserPass" id="txtUserPass" class="logInInputs">
					</td>
				</tr>
				<tr style="display:none;" id="payGrpRow">
					<td class="logInLabel">					
						Pay Group
					</td>
					<td class="logInLabel">					
						:
					</td>
					<td >					
						<?$indexObj->DropDownMenu($arrPayGrp,'cmbPayGroup','','class="logInInputs"'); ?>
					</td>
				</tr>
				<tr style="display:none;" id="payCatRow">
					<td class="logInLabel">					
						Pay Category
					</td>
					<td class="logInLabel">					
						:
					</td>
					<td >					
						<div id="payCatDiv"><?$indexObj->DropDownMenu($arrPayCat,'cmbPayCategory','','class="logInInputs" onclick="checkComp()" style="width:200px;"'); ?></div>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<br>
						<INPUT type="button" name="btnLogIn" id="btnLogIn" value="LOG IN" class="logInInputs" onClick="return validateLogIn();">
						<INPUT type="button" name="btnReset" id="btnReset" value="CLEAR" class="logInInputs" onClick="resetInputs()">
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
		width: 600,
		height : 350
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
			$('errMsg').innerHTML='<blink>Select Company First</blink>';
			$('cmbCompny').focus();
			return false;			
		}
	}
	
	function resetInputs(){
		$('cmbModuleName').value=0;
		$('cmbCompny').value=0;
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
			$('errMsg').innerHTML='<blink>Select Module</blink>';
			$('cmbModuleName').focus();
			return false;
		}
		
		if(Compny == '' || Compny == 0){
			$('errMsg').innerHTML='<blink>Select Company</blink>';
			$('cmbCompny').focus();
			return false;
		}
		
		if(EmpId == ''){
			$('errMsg').innerHTML='<blink>Input Employee Id</blink>';
			$('txtEmpId').focus();
			return false;			
		}
		else{
			if(!EmpId.match(NumerixExp)){
				$('errMsg').innerHTML='<blink>Invalid Employee Id Numbers Only</blink>';
				$('txtEmpId').focus();
				return false;				
			}	
		}
		
		if(UserPass == ''){
			$('errMsg').innerHTML='<blink>Input User Password</blink>';
			$('txtUserPass').focus();
			return false;				
		}
		
		if(moduleName == 3){
			var PayGroup = $F('cmbPayGroup');
			var PayCategory = $F('cmbPayCategory');
			
			if(PayGroup == "" || PayGroup == 0){
				$('errMsg').innerHTML='<blink>Select Pay Group</blink>';
				$('cmbPayGroup').focus();
				return false;				
			}
			if(PayCategory == "" || PayCategory == 0){
				$('errMsg').innerHTML='<blink>Select Pay Category</blink>';
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
</SCRIPT>