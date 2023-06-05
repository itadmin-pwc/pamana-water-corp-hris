<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");

$common = new commonObj();
$common->validateSessions('','MODULES');

if(isset($_GET['btnChnangeComp']) == 'SUBMIT'){
	 $_SESSION['company_code']  = $_GET['cmbCompny'];
	 $usersession=$common->getUserLogInInfoForMenu($_SESSION['employee_number']);
	 $_SESSION['user_id']="";
	 $_SESSION['user_id']=$usersession['userId'];
	echo "parent.location.href='http://$_SERVER[HTTP_HOST]/".SYS_NAME."/".SYS_NAME_201."';";
	exit();
}

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
	</HEAD>
	<BODY>
		<FORM name='frmChangeComp' id="frmChangeComp" action="<?=$_SERVER['PHP_SELF']?>" method="post" >

			<TABLE border="0" cellpadding="1" cellspacing="0" class="parentGrid" width="50%" >
				<tr>
					<td class="parentGridHdr" height="20">
						&nbsp;<img src="../../../images/grid.png">&nbsp;
						Change System Header Information
					</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
					
						<TABLE align="center" cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
							<tr>
								<td class="gridDtlLbl2" align="left" width="30%">
									<font class="gridDtlLblTxt">Company</font>
								</td>
								<td width="1%" class="gridDtlLbl2" align="center">:</td>
								<td class="gridDtlVal" width="50%">
									<font class="gridDtlLblTxt">
										<? $common->DropDownMenu($common->makeArr($common->getChangeCompany(''),'compCode','compName',''),'cmbCompny',$_SESSION['company_code'],'class="inputs" tabindex="1" onchange="populatePayCat(this.value)"');?>
									</font>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="3" class="childGridFooter">
									<INPUT class="inputs" type="button" name="btnChnangeComp" id="btnChnangeComp" value="SUBMIT" onClick="changeComp()" tabindex="2">
									<INPUT class="inputs" type="button" id="btnCancel" value="CANCEL" onClick="parent.document.getElementById('contentFrame').src='';" tabindex="4">
								</td>
							</tr>
						</TABLE>
					</td>
				</tr>
			</TABLE>

		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function changeComp(){
		if($F('cmbCompny') == 0){
			alert('Company is Required');
			$('cmbCompny').focus();
			return false;
		}
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>',{
			method : 'get',
			parameters : $('frmChangeComp').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			},
			onCreate : function (){
				$('btnChnangeComp').value='Loading...';
				$('btnChnangeComp').disabled=true;
				$('btnCancel').disabled=true;				
			},
			onSuccess : function (){
				$('btnChnangeComp').value='SUBMIT';
				$('btnChnangeComp').disabled=false;
				$('btnCancel').disabled=false;					
			}
		});
	}
	
	function checkComp(){
		var Compny = $F('cmbCompny');
		if(Compny == 0 || Compny == ""){
			alert('Select Company First');
			$('cmbCompny').focus();
			return false;			
		}
	}
	
	function populatePayCat(compCode){
		var params = '?action=populatPayCat&compCode='+compCode;
	
		var url = '<?=$_SERVER['PHP_SELF']?>'+params;
		
		new Ajax.Request(url,{
			method : 'get',
			onComplete : function (req){
				$('gridDtlLblTxt').innerHTML=req.responseText;	
			},
			onCreate : function (){
				$('gridDtlLblTxt').innerHTML="<img src='../../../images/wait.gif'>";
			}			
		});		
	}
</SCRIPT>