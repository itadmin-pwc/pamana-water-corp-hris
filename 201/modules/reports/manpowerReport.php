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
	case "startcutoff":
		$qryStartCutOff = $inqTSObj->makeArr($inqTSObj->getPayPrd($_SESSION['company_code']," and payGrp='".$_GET['paygroup']."' order by  year(pdFrmDate)"),'pdFrmDate','pdFrmDate','');
		$inqTSObj->DropDownMenu($qryStartCutOff,'cmbStart','','class="inputs" onChange="getEndDate(this.value);"');
	exit();
	break;
	
	case "endcutoff":
		$qryStartCutOff = $inqTSObj->makeArr($inqTSObj->getPayPrd($_SESSION['company_code']," and payGrp='".$_GET['paygroup']."' and pdFrmDate='".$_GET['startdate']."' order by year(pdToDate)"),'pdToDate','pdToDate','');
		$inqTSObj->DropDownMenu($qryStartCutOff,'cmbEnd','','class="inputs"');
	exit();
	break;
		
	case "processManpowerReport":
		$pgroup = $_GET['paygroup'];
		$costart = date('Y-m-d',strtotime($_GET['cutoffstart']));
		$coend = date('Y-m-d',strtotime($_GET['cutoffend']));
		$branch = $_GET['branch'];
		
/*		$coendPrev = strtotime('-1 day',$costart);
		$qryPrev = $inqTSObj->getPayPrd($_SESSION['company_code']," and payGrp='".$pgroup."' and pdFrmDate='".$ostartPrev."' and pdToDate='".$coendPrev."' and pdYear='".substr($coendPrev,-4)."'");
*/		
		$qry= $inqTSObj->getPayPrdOne($_SESSION['company_code']," and payGrp='".$pgroup."' and pdFrmDate='".$costart."' and pdToDate='".$coend."' and pdYear='".substr($coend,0,4)."'");
		foreach($qry as $keyval=>$val){
			$pdpyable=date("F j, Y", strtotime($val['pdPayable']));	
			$pdSeries = $val['pdSeries']-1;
		}
		$qryPrev = $inqTSObj->getPayPrd($_SESSION['company_code']," and pdSeries='".$pdSeries."'");
		foreach($qryPrev as $keyvalPrev=>$preVal){
			$costartPrev = date('Y-m-d',strtotime($preVal['pdFrmDate']));		
			$coendPrev = date('Y-m-d',strtotime($preVal['pdToDate']));		
		}
		if($_GET['type']=="manpowerReport"){
			echo "window.location ='manpowerReport_pdf.php?pgroup=$pgroup&costart=$costart&coend=$coend&branch=$branch&pddate=$pdpyable&costartPrev=$costartPrev&coendPrev=$coendPrev'";	
		}
		else{
			echo "window.location ='manpowerReport_batch_pdf.php?pgroup=$pgroup&costart=$costart&coend=$coend&branch=$branch&pddate=$pdpyable&costartPrev=$costartPrev&coendPrev=$coendPrev'";		
		}
		
	exit();
	break;
}
?>
<html>
	<head>
		<title><?=SYS_TITLE;?></title>
        <script type="text/javascript" src="../../../includes/prototype.js"></script>
        <style>@import url('../../style/reports.css');</style>  
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
    </head>
  
<body onLoad="setValue();">
<form name="frmManpowerReport" id="frmManpowerReport" method="" action="<?=$_SERVER['PHP_SELF'];?>">
	<table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    	<tr>
        	<td class="parentGridHdr">&nbsp;<img src="../../../images/grid.png">&nbsp; MANPOWER REPORT </td>
        </tr>
        <tr>
        	<td class="parentGridDtl"><table cellspacing="1" cellpadding="1" border="0" width="100%" class="childGrid">
            	<tr>
                	<td class="gridToolbar" colspan="6"><input type="radio" name="inquire" id="inquire" value="1" onClick="refreshObj(this.value);">Inquire&nbsp;&nbsp;<input name="inquire" type="radio" id="inquire" onClick="refreshObj(this.value);" value="0" checked>Refresh</td>
                </tr>
                <tr>
                	<td class="gridDtlLbl" width="18%">Group</td>
                    <td class="gridDtlLbl" width="1%">:</td>
                    <td class="gridDtlVal"><?
						$inqTSObj->DropDownMenu(array(""=>"","1"=>"Group 1"),'cmbGroup','','class="inputs" onChange="setCutOff(this.value);"');
					?></td>
                </tr>
                <tr>
                	<td class="gridDtlLbl" width="18%">Start Cut-Off</td>
                    <td class="gridDtlLbl" width="1%">:</td>
                    <td class="gridDtlVal"><div id="startCutOff"><?
                    	$inqTSObj->DropDownMenu('','cmbStart','','class="inputs"');
					?></div></td>
                </tr>
                <tr>
                	<td class="gridDtlLbl" width="18%">End Cut-Off</td>
                    <td class="gridDtlLbl" width="1%">:</td>
                    <td class="gridDtlVal"><div id="endCutOff"><?
                    	$inqTSObj->DropDownMenu('','cmbEnd','','class="inputs"');
					?></div></td>
                </tr>
                <tr>
                	<td class="gridDtlLbl" width="18%">Branch</td>
                    <td class="gridDtlLbl" width="1%">:</td>
                    <td class="gridDtlVal"><?
						$qryBranch = $inqTSObj->makeArr($inqTSObj->getBrnchArt($_SESSION['company_code']),'brnCode','brnDesc','');
                    	$inqTSObj->DropDownMenu($qryBranch,'cmbBranch','','class="inputs"');
					?></td>
                </tr>
            </table>
            <br>
            <table cellpadding="1" cellspacing="1" width="100%" class="childGrid" border="0">
            	<tr>
                	<td align="center"><input type="button" id="manpowerReport" name="manpowerReport" class="inputs" value="Print Manpower Report" onClick="validateValues(this.id);"/>
               	    <input type="button" id="manpowerReportBatch" name="manpowerReportBatch" class="inputs" value="Print Manpower Report (BATCH TOTAL)" onClick="validateValues(this.id);"/></td>
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
function setCutOff(id){
	var empInputs = $('frmManpowerReport').serialize();
	var grp = $('cmbGroup').value;
	var params;
	
	if($('cmbGroup').value==0){
		alert('Pay group must not be blank.');
		$('cmbEnd').value=0;
		$('cmbStart').value=0;
		return false;	
	}
	
	params = '<?=$_SERVER['PHP_SELF']?>?action=startcutoff&paygroup='+grp;
	new Ajax.Request(params,{
		method	: 'get',
		parameters	: $('frmManpowerReport').serialize(true),
		onComplete	: function(req){
			$('startCutOff').innerHTML=req.responseText;
			},	
		onCreate	: function(){
			$('startCutOff').innerHTML="loading.........";
			}	
	})
}

function getEndDate(){
	var empInputs = $('frmManpowerReport').serialize();	
	var start = $('cmbStart').value;
	var grp = $('cmbGroup').value;
	var params;
	
	if($('cmbStart').value==0){
		alert('Start of Cut-Off must not be blank.');
		$('cmbEnd').value=0;
		return false;	
	}
	
	params = '<?=$_SERVER['PHP_SELF']?>?action=endcutoff&startdate='+start+'&paygroup='+grp;
	new Ajax.Request(params,{
		method	: 'get',
		parameters	: $('frmManpowerReport').serialize(true),
		onComplete	: function(req){
			$('endCutOff').innerHTML=req.responseText;	
			},
		onCreate 	: function(){
			$('endCutOff').innerHTML="loading.......";	
			}		
	})
}

function refreshObj(id){
	var empInputs = $('frmManpowerReport').serialize(true);
	if(id==1){
		$('manpowerReport').disabled=false;	
		$('manpowerReportBatch').disabled=false;
		$('cmbGroup').disabled=false;	
		$('cmbBranch').disabled=false;	
		$('cmbStart').disabled=false;	
		$('cmbEnd').disabled=false;	
		
	}
	if(id==0){
		$('cmbGroup').value='';	
		$('cmbStart').value=0;	
		$('cmbEnd').value=0;	
		$('cmbStart').disabled=true;	
		$('cmbEnd').disabled=true;	
		$('manpowerReport').disabled=true;
		$('manpowerReportBatch').disabled=true;
		$('cmbGroup').disabled=true;	
		$('cmbBranch').disabled=true;	
		
	}
}

function setValue(){
	var empInputs = $('frmManpowerReport').serialize();	
	$('manpowerReport').disabled=true;
	$('manpowerReportBatch').disabled=true;
		$('cmbStart').disabled=true;	
		$('cmbEnd').disabled=true;	
		$('manpowerReport').disabled=true;
		$('cmbGroup').disabled=true;	
		$('cmbBranch').disabled=true;	
}

function validateValues(id){
	var empInputs = $('frmManpowerReport').serialize();
	var pgrp = $('cmbGroup').value;
	var costart = $('cmbStart').value;
	var coend = $('cmbEnd').value;
	var brn = $('cmbBranch').value;
	var params;
	if($('cmbGroup').value==0){
		alert('Pay Group is Required.');
		return false;	
	}	
	if($('cmbStart').value==0){
		alert('Start of payroll period is required.');
		return false;	
	}
	if($('cmbEnd').value=="" || $('cmbEnd').value==0){
		alert('End of payroll period is required.');
		return false;	
	}
	if($('cmbBranch').value==0){
		alert('Branch is required.');
		return false;	
	}
	
	params = '<?=$_SERVER['PHP_SELF']?>?action=processManpowerReport&paygroup='+pgrp+'&cutoffstart='+costart+'&cutoffend='+coend+'&branch='+brn+'&type='+id;
	new Ajax.Request(params,{
		method	:	'get',
		parameters	:	$('frmManpowerReport').serialize(true),
		onComplete	:	function(req){
				eval(req.responseText);
			}
	})
}
</script>