<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("movement.trans.php");
##################################################

switch ($_GET['action']){
	case "processSummary":
		$type = $_GET['type'];
		if($type==0){
			echo "window.location ='head_count_summary_pdf.php?type=$type'";	
		}
		else{
			echo "window.location ='head_count_summary_branch_pdf.php?type=$type'";		
		}
	exit();
	break;
}
?>
<html>
	<head>
		<title><?=SYS_TITLE;?></title>
        <script type='text/javascript' src='../../../includes/jSLib.js'></script>        
        <script type="text/javascript" src="../../../includes/prototype.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>        
        <style>@import url('../../style/reports.css');</style>
        <STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>    
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
    </head>
  
<body onLoad="setValue();">
<form name="frmsummary" id="frmsummary" method="post" action="<?=$_SERVER['PHP_SELF'];?>">
	<table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    	<tr>
        	<td class="parentGridHdr">&nbsp;<img src="../../../images/grid.png">&nbsp; Head Count Summary Report </td>
        </tr>
        <tr>
        	<td class="parentGridDtl"><table cellspacing="1" cellpadding="1" border="0" width="100%" class="childGrid">
            	<tr>
                	<td class="gridToolbar" colspan="6"><input type="radio" name="inquire" id="inquire" value="1" onClick="refreshObj(this.value);">Inquire&nbsp;&nbsp;<input name="inquire" type="radio" id="inquire" onClick="refreshObj(this.value);" value="0" checked>Refresh</td>
                </tr>
                <tr>
                	<td class="gridDtlLbl" width="18%">Type</td>
                    <td class="gridDtlLbl" width="1%">:</td>
                    <td class="gridDtlVal"><?
						$qryBranch = $inqTSObj->makeArr($inqTSObj->getBrnchArt($_SESSION['company_code']),'brnCode','brnDesc','All');
                    	$inqTSObj->DropDownMenu($qryBranch,'cmbType','','class="inputs"');
					?></td>
                </tr>
            </table>
            <br>
            <table cellpadding="1" cellspacing="1" width="100%" class="childGrid" border="0">
            	<tr>
                	<td align="center"><input type="button" id="headCountSummry" name="headCountSummry" class="inputs" value="Print Head Count Summary Report" onClick="validateValues();"/></td>
                </tr>
            </table>
            </td>
        </tr>
    	<tr>
        	<td class="parentGridHdr"></td>
        </tr>
    
    </table>
</form>
</body>
</html>
<script type="text/javascript">
function refreshObj(id){
	var empInputs = $('frmsummary').serialize(true);
	if(id==1){
		$('headCountSummry').disabled=false;	
		$('cmbType').disabled=false;	
		
	}
	if(id==0){
		$('headCountSummry').disabled=true;
		$('cmbType').disabled=true;	
	}
}

function setValue(){
	var empInputs = $('frmsummary').serialize();	
	$('headCountSummry').disabled=true;
		$('cmbType').disabled=true;	
}

function validateValues(){
	var empInputs = $('frmsummary').serialize();
	var type = $('cmbType').value;
	var params;
	params = '<?=$_SERVER['PHP_SELF']?>?action=processSummary&type='+type;
	new Ajax.Request(params,{
		method	:	'get',
		parameters	:	$('frmsummary').serialize(true),
		onComplete	:	function(req){
				eval(req.responseText);
			}
	})
}
</script>