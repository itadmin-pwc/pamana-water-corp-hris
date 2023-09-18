<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("emp_min_wage_obj.php");

$minObj = new minWageObj($_GET,$_SESSION);
$sessionVars = $minObj->getSeesionVars();
$minObj->validateSessions('','MODULES');



$cnt = $_GET['chCtr'];
switch($_GET['action']) {
	
	case "Update":
			
			if($minObj->UpdateInsMinWage())
				echo "successMinWage();";
			else
				echo "alert('Error processing Minimum Wage .');";	
		
			
		exit();	
	break;	
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
		<script type='text/javascript' src='movement.js'></script>
		<STYLE>@import url('../../style/reports.css');</STYLE>
	</HEAD>
	<BODY>
    
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager("emp_min_wage_ajax.php","TSCont",'load',0,0,'','','&stat=H','../../../images/');  
	function CheckEmpAll()
	{
		var cnt = $('chCtr').value;
		for(i=1;i<=cnt;i++){
			if ($('chAll').checked==false) {
				$("chkMinWage"+i).checked=false;
			} else {
				$("chkMinWage"+i).checked=true;
			}
		}
	}
	
	function UpdateMinWage() 
	{
		var userConfirm = confirm("Are you sure you want to Update the Selected Employee as Minimum Wage Earner?");
		if(userConfirm==true)
		{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=Update',{
				method : 'get',
				parameters : $('frmMinWage').serialize(),
				onComplete : function (req){
					eval(req.responseText);	
				}
			});	
		}		
	}
	
	function successMinWage()
	{
		alert("Update Minimum Wage Earner successfully processed.");
		pager("emp_min_wage_ajax.php","TSCont",'load',0,0,'','','&stat=H','../../../images/');  
	}

</SCRIPT>