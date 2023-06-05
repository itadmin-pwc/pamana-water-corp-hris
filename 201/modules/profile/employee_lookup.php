<?
session_start();
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../../payroll/style/payroll.css');</STYLE>	
	
	</HEAD>
	<BODY bgcolor="White">
		<FORM name='frmEmpLkup' id="frmEmpLkup" action="<?=$_SERVER['../../../includes/PHP_SELF']?>" method="post">
			<div id="empLukupCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>

	//disableRightClick()			
pager('../profile/employee_lookupAjaxResult.php','empLukupCont','load',0,0,'','','&tmpCompCode=<?=$_GET['tmpCompCode']?>','../../../images/');  

function focusHandelr(act){
}	
	
</SCRIPT>