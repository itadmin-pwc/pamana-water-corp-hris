<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>	
		
	</HEAD>
	<BODY bgcolor="White">
		<FORM name='frmRefLkup' id="frmRefLkup" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="refLukupCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<?
if($_GET['opnr'] == 'earn'){
	$fileLookup = "earnings_reference_lookupAjaxResult.php";
}
else {
	$fileLookup = "deductions_reference_lookupAjaxResult.php";
}
?>
<SCRIPT>
	//disableRightClick()			
pager('<?=$fileLookup?>','refLukupCont','load',0,0,'','','','../../../images/');  

function focusHandelr(act){
}	
	
</SCRIPT>