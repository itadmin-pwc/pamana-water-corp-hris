<?
//
session_start();
session_destroy();
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
				<tr align="center">
					<td align="center" colspan="3">
                    	<font face="Arial, Helvetica, sans-serif">
                        
                        <b>
						<font color="#990000">PG-HRIS-SYSTEM</font>(201 Module) is temporarily  <font style="text-decoration:underline">OFFLINE.</font>
                        <br>
                        <br>
                        <br>
                        Kindly Email  <font style="text-decoration:underline" color="#000033">Nhomer Cabico IT-HO</font> for Updates.
                        <br>
                        <br>
                        Thank You.
                        </b>
                        </font>
					</td>
				</tr>
				
			</TABLE>
		</form>
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