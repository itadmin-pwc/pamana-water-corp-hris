<?
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("timesheet_obj.php");
	
	$maintEmpLoanObj = new inqTSObj();
	$sessionVars = $maintEmpLoanObj->getSeesionVars();
	$maintEmpLoanObj->validateSessions('','MODULES');
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
	</HEAD>
	<BODY>
		<form name="frmEmpList" method="post" action="">
        	<input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
            <input type="hidden" name="empName" id="empName" value="<? echo $_GET['empName']; ?>">
            <input type="hidden" name="empDiv" id="empDiv" value="<? echo $_GET['empDiv']; ?>">
            <input type="hidden" name="empDept" id="empDept" value="<? echo $_GET['empDept']; ?>">
            <input type="hidden" name="empSect" id="empSect" value="<? echo $_GET['empSect']; ?>">
            <input type="hidden" name="orderBy" id="orderBy" value="<? echo $_GET['orderBy']; ?>">
             
            <input type="hidden" name="fileName" id="fileName" value="<? echo $_GET['fileName']; ?>">
            <input type="hidden" name="inputId" id="inputId" value="<? echo $_GET['inputId']; ?>">
           
           	<input type="hidden" name="cmbName" id="cmbName" value="<?php echo $_GET['cmbName']; ?>">
            <input type="hidden" name="conType" id="conType" value="<?php echo $_GET['conType']; ?>">
            <input type="hidden" name="monthto" id="monthto" value="<?php echo $_GET['monthto']; ?>">
            <input type="hidden" name="monthfr" id="monthfr" value="<?php echo $_GET['monthfr']; ?>">
            
		</form>
		<div id="empMastCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	var empNo=document.frmEmpList.empNo.value;
	var empName=document.frmEmpList.empName.value;
	var empDiv=document.frmEmpList.empDiv.value;
	var empDept=document.frmEmpList.empDept.value;
	var empSect=document.frmEmpList.empSect.value;
	var orderBy=document.frmEmpList.orderBy.value;
	var fileName=document.frmEmpList.fileName.value;
	var inputId=document.frmEmpList.inputId.value;
	var cmbName=document.frmEmpList.cmbName.value;
	var conType=document.frmEmpList.conType.value;
	var monthto=document.frmEmpList.monthto.value;
	var monthfr=document.frmEmpList.monthfr.value;
	
	pager("main_emp_list_ajax.php","empMastCont",'load',0,0,'','','&fileName='+fileName+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&cmbName='+cmbName+'&conType='+conType+'&monthfr='+monthfr+'&monthto='+monthto,'../../../images/');  
	
</SCRIPT>