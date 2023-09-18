<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
if ($_GET['act'] == "EditMinimumWage") {
	$minimumwageinfo = $maintEmpObj->getMinimumWage($_GET['minimumwageid']);
	$brncode=$minimumwageinfo['brnCode'];
	$minimumwagenew=(float)$minimumwageinfo['minimumWage_New'];
	$ecolanew=(float)$minimumwageinfo['eCola_New'];
	$grpminage=$minimumwageinfo['minGroupID'];
	$effectivedate=$maintEmpObj->valDateArt($minimumwageinfo['effectiveDate']);
//	$branchminwage = $maintEmpObj->getBranchMinimumWage($brncode);	
	$minimumwageold=(float)$minimumwageinfo['minimumWage_Old'];
	$ecolaold=(float)$minimumwageinfo['eCola_Old'];	
	
//	$branchminwage = $maintEmpObj->getBranchMinimumWage($brncode);	
//	$minimumwageold=(float)$branchminwage['minWage'];
//	$ecolaold=(float)$branchminwage['ecola'];	
}

switch($_GET['code']) {

	case "AddMinimumWage":
		$sql="Select compCode,brnCode,minimumWage_Old,minimumWage_New,eCola_Old,eCola_New,effectiveDate from tblMinimumWage where compCode='{$_SESSION['company_code']}' and brnCode='{$_GET['cmbbranch']}' and minimumWage_Old='".(float)$_GET['txtMinimumWage_Old']."' and minimumWage_New='".(float)$_GET['txtMinimumNew']."' and eCola_Old='".(float)$_GET['txtecola_Old']."' and eCola_New='".(float)$_GET['txtecolanew']."' and effectiveDate='{$_GET['txtEffectiveDate']}'";	
		$checkaddmin=$maintEmpObj->recordChecker($sql);
		if($checkaddmin){
			echo "alert('Minimum Wage Already Exist!');";
			exit();
			}
		else{	
			if ($maintEmpObj->minimumWage("Add",$_GET)){
				echo "alert('Minimum Wage Successfully Added.');";
				}
			else{
				echo "alert('Error Adding Minimum Wage.');";
				}
		}
		exit();
	break;
	case "EditMinimumWage":
		$sql="Select compCode,brnCode,minimumWage_Old,minimumWage_New,eCola_Old,eCola_New,effectiveDate from tblMinimumWage where compCode='{$_SESSION['company_code']}' and brnCode='{$_GET['cmbbranch']}' and minimumWage_Old='".(float)$_GET['txtMinimumWage_Old']."' and minimumWage_New='".(float)$_GET['txtMinimumNew']."' and eCola_Old='".(float)$_GET['txtecola_Old']."' and eCola_New='".(float)$_GET['txtecolanew']."' and effectiveDate='{$_GET['txtEffectiveDate']}' and minimumWageId!='{$_GET['mCode']}'";	
		$checkaddmin=$maintEmpObj->recordChecker($sql);
		if($checkaddmin){
			echo "alert('Minimum Wage Already Exist!');";
			exit();
			}
		else{	
			if ($maintEmpObj->minimumWage("Edit",$_GET)){
				echo "alert('Minimum Wage Successfully Updated.');";
				}
			else{
				echo "alert('Error Updating Minimum Wage.');";
				}
		}
		exit();
	break;
}


if($_GET['transtype']=="getgroupoldminwage"){
		$_SESSION['groupbranchminimumwage'] = $_GET['groupid'];
		$groupbranchminimumwage = $maintEmpObj->getGroupBranchMinimumWage($_GET['groupid']);
		$branchminimumwage = $maintEmpObj->getBranchMinimumWage($groupbranchminimumwage['brnCode']);	
		if($branchminimumwage['minWage']=='' || $branchminimumwage['minWage']==0){
			echo '--';  
		  }
		  else{
			echo $branchminimumwage['minWage'];  
		  }
		
		echo "<input type=\"hidden\" id=\"txtMinimumWage_Old\" name=\"txtMinimumWage_Old\" value=".$branchminimumwage['minWage'].">";
exit();	
}
if($_GET['transtype']=="getoldminwage"){
		if($_GET['branchid']==0){	
		$groupbranchminimumwage = $maintEmpObj->getGroupBranchMinimumWage($_SESSION['groupbranchminimumwage']);
		$branchminimumwage = $maintEmpObj->getBranchMinimumWage($groupbranchminimumwage['brnCode']);	
		}
		else{
		$branchminimumwage = $maintEmpObj->getBranchMinimumWage($_GET['branchid']);	
		}
		if($branchminimumwage['minWage']=='' || $branchminimumwage['minWage']==0){
			echo '--';  
		  }
		  else{
			echo $branchminimumwage['minWage'];  
		  }
			  
		echo "<input type=\"hidden\" id=\"txtMinimumWage_Old\" name=\"txtMinimumWage_Old\" value=".$branchminimumwage['minWage'].">";
exit();		
}

if($_GET['transtype']=="getgroupoldecola"){
		$groupbranchminimumwage = $maintEmpObj->getGroupBranchMinimumWage($_GET['groupid']);
		$branchecola = $maintEmpObj->getBranchMinimumWage($groupbranchminimumwage['brnCode']);	
		if($branchecola['ecola']=='' || $branchecola['ecola']==0){
			echo '--';  
		  }
		  else{
			echo $branchecola['ecola'];  
		  }
			  
		echo "<input type=\"hidden\" id=\"txtecola_Old\" name=\"txtecola_Old\" value=".$branchecola['ecola'].">";
exit();		
}

if($_GET['transtype']=="getoldecola"){
		if($_GET['branchid']==0){
		$groupbranchminimumwage = $maintEmpObj->getGroupBranchMinimumWage($_GET['groupid']);
		$branchecola = $maintEmpObj->getBranchMinimumWage($groupbranchminimumwage['brnCode']);	
		}
		else{
		$branchecola = $maintEmpObj->getBranchMinimumWage($_GET['branchid']);	
		}
		if($branchecola['ecola']=='' || $branchecola['ecola']==0){
			echo '--';  
		  }
		  else{
			echo $branchecola['ecola'];  
		  }
			  
		echo "<input type=\"hidden\" id=\"txtecola_Old\" name=\"txtecola_Old\" value=".$branchecola['ecola'].">";
exit();		
}

if($_GET['transtype']=="getminimumbranchgroup"){
	$arrcont=$maintEmpObj->makeArr($maintEmpObj->getBranchGroup(" where compCode='".$_SESSION['company_code']."' and minGroupID='".$_GET['id']."'"),'brnCode','brnDesc','');
	if(sizeof($arrcont)>=2){
	$arrconts=$maintEmpObj->makeArr($maintEmpObj->getBranchGroup(" where compCode='".$_SESSION['company_code']."' and minGroupID='".$_GET['id']."'"),'brnCode','brnDesc','All');	
	}
	else{
	$arrconts=$maintEmpObj->makeArr($maintEmpObj->getBranchGroup(" where compCode='".$_SESSION['company_code']."' and minGroupID='".$_GET['id']."'"),'brnCode','brnDesc','No Group of branches');	
	}
	echo  $maintEmpObj->DropDownMenu($arrconts,'cmbbranch',$brncode,'class="inputs" style="width:240px;" onChange="getBranchMinimumWages(this.value); getBranchECOLA(this.value);"'); 	
exit();
}
?>

<HTML>
<head>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>	
<STYLE>@import url('../../style/payroll.css');</STYLE>
<STYLE TYPE="text/css" MEDIA="screen">
@import url("../../../includes/calendar/calendar-blue.css");.style3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
}
</STYLE>
<style type="text/css">
<!--
	.headertxt {font-family: verdana; font-size: 11px;}
.style2 {font-family: verdana}
.style3 {font-size: 11px}
-->
</style>

</head>
	<BODY onLoad="lockgroup();">
	<form action="" method="post" name="frmminimumwage" id="frmminimumwage">
      <table width="430" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td class="gridDtlLbl style2 style3" >Group</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><? 
		  $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranchGroupName($_SESSION['company_code']),'minGroupID','minGroupName',''),'cmbbranchgroup',$grpminage ,'class="inputs" style="width:240px;" onChange="getMinimumGroup(this.value); getGroupBranchMinimumWages(this.value); getGroupBranchECOLA(this.value);"'); 
		  ?></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Branch</td>
          <td width="1%" class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><div id="brnGrp">
          <? 
		  $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getBranch($_SESSION['company_code']),'brnCode','brnDesc',''),'cmbbranch',$brncode,'class="inputs" style="width:240px;" onChange="getBranchMinimumWages(this.value); getBranchECOLA(this.value);"'); 
		  ?></div>
          <input type="hidden" value="<?=$_GET['minimumwageid']?>" name="mCode" id="mCode">
          <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code">
          <input type="hidden" name="ecolacon" id="ecolacon"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt"> Minimum Wage Old</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><div id="minwage">
		  <?
		  if($minimumwageold=="" || $minimumwageold<=0){
			echo "--";  
		  }
		  else{
			echo number_format($minimumwageold,2);  	
		  }
			  
		  ?>
          <input type="hidden" id="txtMinimumWage_Old" name="txtMinimumWage_Old" value="<?=$minimumwageold;?>">
          </div></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Minimum Wage New</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input name="txtMinimumNew" type="text" class="inputs" id="txtMinimumNew" onKeyUp="extractNumber(this,2,true);"  value="<?=number_format($minimumwagenew,2)?>" size="15">
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">ECola Old</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><div id="ecola"><?
          if($ecolaold=="" || $ecolaold==0){
			echo "--";  
		  }
		  else{
			echo number_format($ecolaold,2);  
	      }
		  ?>
          <input type="hidden" id="txtecola_Old" name="txtecola_Old" value="<?=$ecolaold;?>"></div></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">ECola New</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input name="txtecolanew" type="text" class="inputs" id="txtecolanew" onKeyUp="extractNumber(this,2,true);" value="<?=number_format($ecolanew,2)?>" size="15">
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Effective Date</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <input name="txtEffectiveDate" type="text" class="inputs" id="txtEffectiveDate" value="<?=$effectivedate;?>" size="12">
            <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgEffectiveDate" id="imgEffectiveDate" style="cursor: pointer;position:relative;top:3px;border:none;"></a>
          </span></td>
        </tr>
        <tr>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
          <?php if($_SESSION['user_level']==1){ ?>
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="saveminimum();" name="save" id="save" value="Submit">
          <?php } ?>
          </td>
        </tr>
      </table>
    </form>
</BODY>
</HTML>
<script>
	function saveminimum() {
		var empInputs = $('frmminimumwage').serialize(true);
		var an=document.getElementById("cmbbranch").options[document.getElementById("cmbbranch").selectedIndex].text;
		if(empInputs['cmbbranchgroup']==0){
			alert('Minimum Wage Group is Required.');
			$('cmbbranchgroup').focus();
            return false;		
		}
		if(empInputs['cmbbranchgroup']!=0){
			if(an=="NO GROUP OF BRANCHES"){
			alert('No branch/s belongs to the group.');	
			return false;
			}	
		}
		if (trim(empInputs['txtMinimumNew'])=="") {
			alert('Minimum Wage is Required.');
			$('txtMinimumNew').focus();
            return false;		
		}        
		if (parseFloat(empInputs['txtMinimumNew'])<=0 || parseFloat(empInputs['txtMinimumNew'])<= parseFloat(empInputs['txtMinimumWage_Old'])) {
			alert('Minimum wage must be greater than 0 or not less than the old minimum wage.');
			$('txtMinimumNew').focus();
            return false;		
		} 
		if (trim(empInputs['txtecolanew']) != ""  && trim(empInputs['ecolacon']) == "") {
			var ecolaans=confirm('ECOLA amount is '+empInputs['txtecolanew']+ '. Are you sure to this amount?');
			if(ecolaans==true){
				$('ecolacon').value='y';
				//return true;	
			}			
			else{
			$('ecolacon').value="";
			$('txtecolanew').focus();
            return false;
			}
		}
		if (trim(empInputs['txtecolanew']) == "") {
			var ans=confirm('ECOLA is blank. Are you sure you want to save it blank?');
			if(ans==true){
				return true;	
			}
			else{
			$('txtecolanew').focus();
            return false;
			}
		}        
		if (empInputs['txtecolanew'] < 0) {
			alert('ECOLA must not be negative value.');
			$('txtecolanew').focus();
            return false;		
		}        
		if (empInputs['txtEffectiveDate'] == "") {
			alert('Effectivity Date is required.');
			$('txtEffectiveDate').focus();
            return false;		
		}        
       
		params = 'minimumwage_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmminimumwage').serialize(),
			onComplete : function (req){
				eval(req.responseText);				
			}	
		});
	}	

	Calendar.setup({
	  inputField  : "txtEffectiveDate",      // ID of the input field
	  ifFormat    : "%m/%d/%Y",          // the date format
	  button      : "imgEffectiveDate"       // ID of the button
	}
	)
	
function getMinimumGroup(id){
	var params='minimumwage_act.php?transtype=getminimumbranchgroup&id='+id;
	new Ajax.Request(params,{
		method : 'get',
		onCreate : function(){
				$('brnGrp').innerHTML="loading branches.....";
			},
		onComplete : function(req){
			$('brnGrp').innerHTML=req.responseText;
			}	
		})	
}	
	
function getBranchMinimumWages(branchid){
	var params='minimumwage_act.php?transtype=getoldminwage&branchid='+branchid;
	new Ajax.Request(params,{
		method : 'get',
		onCreate : function(){
			$('minwage').innerHTML="Loading Old Minimum Wage.....";
		},
		onComplete : function(req){
			$('minwage').innerHTML=req.responseText;
		}
		});
}	
function getBranchECOLA(branchid){
	var params='minimumwage_act.php?transtype=getoldecola&branchid='+branchid;
	new Ajax.Request(params,{
		method : 'get',
		onCreate : function(){
			$('ecola').innerHTML="Loading Old ECOLA.....";
		},
		onComplete : function(req){
			$('ecola').innerHTML=req.responseText;	
		}
		});
}	

function getGroupBranchMinimumWages(groupid){
	var params='minimumwage_act.php?transtype=getgroupoldminwage&groupid='+groupid;
	new Ajax.Request(params,{
		method : 'get',
		onCreate : function(){
			$('minwage').innerHTML="Loading Old Minimum Wage.....";
		},
		onComplete : function(req){
			$('minwage').innerHTML=req.responseText;
		}
		});
}	
function getGroupBranchECOLA(groupid){
	var params='minimumwage_act.php?transtype=getgroupoldecola&groupid='+groupid;
	new Ajax.Request(params,{
		method : 'get',
		onCreate : function(){
			$('ecola').innerHTML="Loading Old ECOLA.....";
		},
		onComplete : function(req){
			$('ecola').innerHTML=req.responseText;	
		}
		});
}	

function lockgroup(){
	if(document.getElementById('code').value=="EditMinimumWage"){
		document.getElementById('cmbbranchgroup').disabled=true;	
	}
	else{
		document.getElementById('cmbbranchgroup').disabled=false;	
	}	
}
      <!--
	  //onkeyup="extractNumber(this,2,true); computechange(this.value);"
function extractNumber(obj, decimalPlaces, allowNegative)
{
	var temp = obj.value;
	
	// avoid changing things if already formatted correctly
	var reg0Str = '[0-9]*';
	if (decimalPlaces > 0) {
		reg0Str += '\\.?[0-9]{0,' + decimalPlaces + '}';
	} else if (decimalPlaces < 0) {
		reg0Str += '\\.?[0-9]*';
	}
	reg0Str = allowNegative ? '^-?' + reg0Str : '^' + reg0Str;
	reg0Str = reg0Str + '$';
	var reg0 = new RegExp(reg0Str);
	if (reg0.test(temp)) return true;

	// first replace all non numbers
	var reg1Str = '[^0-9' + (decimalPlaces != 0 ? '.' : '') + (allowNegative ? '-' : '') + ']';
	var reg1 = new RegExp(reg1Str, 'g');
	temp = temp.replace(reg1, '');

	if (allowNegative) {
		// replace extra negative
		var hasNegative = temp.length > 0 && temp.charAt(0) == '-';
		var reg2 = /-/g;
		temp = temp.replace(reg2, '');
		if (hasNegative) temp = '-' + temp;
	}
	
	if (decimalPlaces != 0) {
		var reg3 = /\./g;
		var reg3Array = reg3.exec(temp);
		if (reg3Array != null) {
			// keep only first occurrence of .
			//  and the number of places specified by decimalPlaces or the entire string if decimalPlaces < 0
			var reg3Right = temp.substring(reg3Array.index + reg3Array[0].length);
			reg3Right = reg3Right.replace(reg3, '');
			reg3Right = decimalPlaces > 0 ? reg3Right.substring(0, decimalPlaces) : reg3Right;
			temp = temp.substring(0,reg3Array.index) + '.' + reg3Right;
		}
	}
	
	obj.value = temp;
}
      //-->
</SCRIPT>