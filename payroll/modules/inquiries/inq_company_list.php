<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("inq_company_list_obj.php");

$inqCompObj = new inqCompObj();
$sessionVars = $inqCompObj->getSeesionVars();
$inqCompObj->validateSessions('','MODULES');

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript'>
			function printCompanyList() {
				document.frmCompList.action = 'inq_company_list_pdf.php';
				document.frmCompList.target = "_blank";
				document.frmCompList.submit();
				document.frmCompList.action = "inq_company_list_ajax.php";
				document.frmCompList.target = "_self";
			}
		</script>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
	</HEAD>
	<BODY>
		<form name="frmCompList" method="post" action="">
		 
		</form>
		<div id="compListCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager("inq_company_list_ajax.php","compListCont",'load',0,0,'','','','../../../images/');  	
</SCRIPT>