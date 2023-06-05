<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("inq_company_list_obj.php");

$inqCompObj = new inqCompObj();
$sessionVars = $inqCompObj->getSeesionVars();
$inqCompObj->validateSessions('','MODULES');
$compCodeBranch = $_GET['compCodeBranch'];
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='inq_company_branch_list_js.js'></script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
	</HEAD>
	<BODY>
		<form name="frmCompList" method="post" action="">
		 	<input type="hidden" name="compCodeBranch" id="compCodeBranch" value="<? echo $_GET['compCodeBranch']; ?>">
		</form>
		<div id="compListCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	var compCodeBranch=document.frmCompList.compCodeBranch.value;
	pager("inq_company_branch_list_ajax.php","compListCont",'load',0,0,'','','&compCodeBranch='+compCodeBranch,'../../../images/');  	
</SCRIPT>