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
	case "getEmployee":
		//echo "alert('Nhomer');";
		$checkEmp = $inqTSObj->empOtherInfos($_GET['empno'],$_SESSION['company_code']);
		if($checkEmp){
			$EmpAllowMonthly = $inqTSObj->empAllowanceMonthly($checkEmp['empNo']);
			$EmpAllowDaily = $inqTSObj->empAllowanceDaily($checkEmp['empNo']);
			if($EmpAllowMonthly!="" and ($EmpAllowDaily=="" or $EmpAllowDaily=="0.00")){
				$allowance = $EmpAllowMonthly;	
			}
			elseif($EmpAllowDaily!="" and ($EmpAllowMonthly==""  or $EmpAllowMonthly=="0.00")){
				$allowance = $EmpAllowDaily;
			}
			elseif(($EmpAllowDaily!="" or $EmpAllowDaily!="0.00")  and ($EmpAllowMonthly!="" or $EmpAllowMonthly!="0.00")){
				$allowance = (float)$EmpAllowDaily + (float)$EmpAllowMonthly;
			}
			else{
				$allowance = "";
			}
			echo "
				var ans=confirm('Would you like to load the data in OTHER CHANGES?');
				if(ans===true){
					$('cmbFromPosition').value='".strtoupper($checkEmp['posDesc'])."';
					$('txtFromSalary').value='".number_format($checkEmp['empMrate'],2)."';
					$('txtFromECOLA').value='".($allowance==""?"":number_format($allowance,2))."';
				}
				else{
					$('cmbFromPosition').value=0;
					$('txtFromSalary').value='';
					$('txtFromECOLA').value='';
				}
			";
			$empname = $checkEmp['empLastName'] . ", " . $checkEmp['empFirstName'] . " " . $checkEmp['empMidName'];	
				echo "$('name').innerHTML='".strtoupper($empname)."';";
				echo "$('hdnName').value='".strtoupper($empname)."';";
				echo "$('hdnLName').value='".strtoupper($checkEmp['empLastName'])."';";
				echo "$('hdnFName').value='".strtoupper($checkEmp['empFirstName'])."';";
				echo "$('hdnMName').value='".strtoupper($checkEmp['empMidName'])."';";
				echo "$('position').innerHTML='".strtoupper($checkEmp['posShortDesc'])."';";
				echo "$('hdnPosition').value='".strtoupper($checkEmp['posShortDesc'])."';";
				echo "$('department').innerHTML='".strtoupper($checkEmp['deptDesc'])."';";
				echo "$('hdnDepartment').value='".strtoupper($checkEmp['deptDesc'])."';";
				echo "$('datehired').innerHTML='".strtoupper(date("F d, Y", strtotime($checkEmp['dateHired'])))."';";
				echo "$('hdnDateHired').value='".strtoupper(date("F d, Y", strtotime($checkEmp['dateHired'])))."';";
				echo "$('empstatus').innerHTML='".strtoupper($checkEmp['employmentTag'])."';";
				echo "$('hdnEmpStatus').value='".strtoupper($checkEmp['employmentTag'])."';";
			
			$branch = $inqTSObj->getBranchName($checkEmp['compCode'],$checkEmp['empBrnCode']);
				echo "$('branch').innerHTML='".strtoupper($branch)."';";
				echo "$('hdnBranch').value='".strtoupper($branch)."';";
			//$location = $inqTSObj->getBranchName($checkEmp['compCode'],$checkEmp['empLocCode']);
			if($checkEmp['empLocCode']=="0001"){
				$location = "HEAD OFFICE BASED";
			}
			else{
				$location = "STORE BASED";	
			}
				echo "$('locationcode').innerHTML='".strtoupper($location)."';";
				echo "$('hdnLocationCode').value='".strtoupper($location)."';";
				
			if($checkEmp['empSex']=="Male"){
				echo "$('pic').innerHTML='<img src=\"../../../images/boy.png\" width=\"143\" height=\"110\" border=\"1\">';";	
			}
			else{
				echo "$('pic').innerHTML='<img src=\"../../../images/lady.png\" width=\"143\" height=\"110\" border=\"1\">';";	
			}

		}
		else{
			echo "alert('No Record Found!');";	
			echo "$('txtEmpNo').value='';";
			echo "$('txtEmpNo').focus();";	
		}
	exit();
	break;	
	
	case "processReport":
	$name = base64_encode($_GET['hdnName']);
	$fname = base64_encode($_GET['hdnFName']);
	$mname = base64_encode($_GET['hdnMName']);
	$lname = base64_encode($_GET['hdnLName']);
	$position = base64_encode($_GET['hdnPosition']);
	$department = base64_encode($_GET['hdnDepartment']);
	$datehired = base64_encode($_GET['hdnDateHired']);
	$empstatus = base64_encode($_GET['hdnEmpStatus']);
	$branch = base64_encode($_GET['hdnBranch']);
	$reclocation = base64_encode($_GET['hdnLocationCode']);
	$tempbranch = base64_encode($_GET['cmbTempBranch']);
	$temprecloc = base64_encode($_GET['cmbRecLocation']);
	$frmdate = base64_encode($_GET['txtFrom']);
	$todate = base64_encode($_GET['txtTo']);
	$lperiod = base64_encode($_GET['hdnLPeriod']);
	$nature = base64_encode($_GET['cmbNature']);
	$permbranch = base64_encode($_GET['cmbPermBranch']);
	$effectivity = base64_encode($_GET['txtEffectivity']);
	$company = base64_encode($_GET['cmbCompany']);
	$permdepartment = base64_encode($_GET['cmbDepartment']);
	$frmposition = base64_encode($_GET['cmbFromPosition']);
	$toposition = base64_encode($_GET['cmbToPosition']);
	$frmsalary = base64_encode($_GET['txtFromSalary']);
	$tosalary = base64_encode($_GET['txtToSalary']);
	$frmecola = base64_encode($_GET['txtFromECOLA']);
	$toecola = base64_encode($_GET['txtToECOLA']);
	$reasontype = base64_encode($_GET['cmbReason']);
	$reason = base64_encode($_GET['txtReason']);
	$prf = base64_encode($_GET['txtPRF']);
	echo "location.href='transfer_employee_pdf.php?name=$name&fname=$fname&mname=$mname&lname=$lname&position=$position&department=$department&datehired=$datehired&empstatus=$empstatus&branch=$branch&reclocation=$reclocation&tempbranch=$tempbranch&temprecloc=$temprecloc&frmdate=$frmdate&todate=$todate&lperiod=$lperiod&nature=$nature&permbranch=$permbranch&effectivity=$effectivity&company=$company&permdepartment=$permdepartment&frmposition=$frmposition&toposition=$toposition&frmsalary=$frmsalary&tosalary=$tosalary&frmecola=$frmecola&toecola=$toecola&reasontype=$reasontype&prf=$prf&reason=$reason'";


	exit();
	break;
}
?>
<html>
	<head>
		<title><?=SYS_TITLE;?></title>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
        <script type="text/javascript" src="../../../includes/prototype.js"></script>
        <!--calendar lib-->
        <script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
        <STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
        <!--end calendar lib-->
        <style>@import url('../../style/reports.css');</style>
    </head>
  
<body onLoad="setValue();">
<form name="frmtransfer" id="frmtransfer" method="" action="<?=$_SERVER['PHP_SELF'];?>">
	<table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    	<tr>
        	<td class="parentGridHdr">&nbsp;<img src="../../../images/grid.png">&nbsp; Transfer Employee Report </td>
        </tr>
        <tr>
        	<td class="parentGridDtl">
            <table width="100%" cellpadding="1" cellspacing="1" class="parentGrid">
            	<tr>
                	<td width="80%"><table cellspacing="1" cellpadding="1" border="0" width="108%" class="childGrid">
                	  <tr>
                	    <td class="gridToolbar" colspan="14"><input type="radio" name="inquire" id="inquire2" value="1" onClick="refreshObj(this.value);">
                	      Inquire&nbsp;&nbsp;
                	      <input name="inquire" type="radio" id="inquire2" onClick="refreshObj(this.value);" value="0" checked>
                	      Refresh</td>
              	    </tr>
                	  <tr>
                	    <td width="16%" height="20" class="gridDtlLbl">Employee Number</td>
                	    <td width="1%" height="20" class="gridDtlLbl">:</td>
                	    <td width="35%" class="gridDtlVal"><input type="text" name="txtEmpNo" id="txtEmpNo" onKeyPress="return isNumberInputEmpNoOnly(this, event);"></td>
                	    <td width="19%" class="gridDtlLbl">Date Hired</td>
                	    <td width="1%" class="gridDtlLbl">:</td>
                	    <td width="28%" height="20" class="gridDtlVal"><div id="datehired"></div>
                	      <input name="hdnDateHired" type="hidden" id="hdnDateHired" size="70"></td>
               	      </tr>
                	  <tr>
                	    <td width="16%" height="20" class="gridDtlLbl">Name</td>
                	    <td width="1%" height="20" class="gridDtlLbl">:</td>
                	    <td class="gridDtlVal"><div id="name"></div>
               	        <input name="hdnFName" type="hidden" id="hdnFName"><input name="hdnLName" type="hidden" id="hdnLName"><input name="hdnMName" type="hidden" id="hdnMName"><input name="hdnName" type="hidden" id="hdnName"></td>
                	    <td class="gridDtlLbl">Employment Status</td>
                	    <td class="gridDtlLbl">:</td>
                	    <td height="20" class="gridDtlVal"><div id="empstatus"></div>
                	      <input name="hdnEmpStatus" type="hidden" id="hdnEmpStatus" size="70"></td>
               	      </tr>
                	  <tr>
                	    <td width="16%" height="20" class="gridDtlLbl">Position</td>
                	    <td width="1%" height="20" class="gridDtlLbl">:</td>
                	    <td class="gridDtlVal"><div id="position"></div><input name="hdnPosition" type="hidden" id="hdnPosition"></td>
                	    <td class="gridDtlLbl">Branch</td>
                	    <td class="gridDtlLbl">:</td>
                	    <td height="20" class="gridDtlVal"><div id="branch"></div>
                	      <input name="hdnBranch" type="hidden" id="hdnBranch" size="70"></td>
               	      </tr>
                	  <tr>
                	    <td width="16%" height="20" class="gridDtlLbl">Department</td>
                	    <td width="1%" height="20" class="gridDtlLbl">:</td>
                	    <td class="gridDtlVal"><div id="department"></div>
               	        <input name="hdnDepartment" type="hidden" id="hdnDepartment" size="70"></td>
                	    <td class="gridDtlLbl">Records Location Code</td>
                	    <td class="gridDtlLbl">:</td>
                	    <td height="20" class="gridDtlVal"><div id="locationcode"></div>
                	      <input name="hdnLocationCode" type="hidden" id="hdnLocationCode" size="70"></td>
               	      </tr>
              	  </table></td>
                  <td width="20%" align="right" ><div id="pic"></div></td>
                </tr>
            </table>
            <table cellspacing="1" cellpadding="1" border="0" width="100%" class="childGrid">
            	<tr>
                	<td height="20" colspan="6" align="center" class="parentGridHdr">TRANSFER DETAILS</td>
              </tr>
                <tr>
                  <td height="20" colspan="3" class="gridToolbar"><input type="radio" name="transType" id="inquire" onClick="enableTransferType(this.value);" value="tempTrans">
                	      Temporary Transfer Data&nbsp;&nbsp;
                	      <input name="transType" type="radio" id="inquire" onClick="enableTransferType(this.value);" value="pemrTrans">
           	      Permanent Transfer Data</td>
                </tr>
                <tr>
                	<td height="20" colspan="3" align="center" class="parentGridHdr">TEMPORARY TRANSFER</td>
                </tr>
                <tr>
                	<td colspan="3" class=""><table width="100%" border="0" cellspacing="1" cellpadding="1">
                	  <tr>
                	    <td width="18%" class="gridDtlLbl">Branch</td>
                	    <td width="1%" class="gridDtlLbl">:</td>
                	    <td width="38%"><?
						$qryBranch = $inqTSObj->makeArr($inqTSObj->getBranchByCompGrp(" and compCode='".$_SESSION['company_code']."'"),'brnShortDesc','brnShortDesc','');
                    	$inqTSObj->DropDownMenu($qryBranch,'cmbTempBranch','','class="inputs"');
					?></td>
                	    <td width="7%" rowspan="2" class="gridDtlLbl" align="center">Effectivity Date</td>
                	    <td width="7%" class="gridDtlLbl">From</td>
                	    <td width="1%" class="gridDtlLbl">:</td>
                	    <td width="13%"><span class="gridDtlVal">
                	      <input name="txtFrom" type="text" class="inputs" id="txtFrom" onChange="valDateStartEnd(this.value,this.id,document.frmtransfer.txtTo.value); computeDate();" size="10" readonly="readonly">
                	    </span><a href="#"><img name="imgfrmDate" id="imgfrmDate" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                	    <td width="5%" rowspan="2" class="gridDtlLbl" align="center">Loan Period</td>
                	    <td width="5%" rowspan="2"><div id="loanperiod" class="gridDtlVal" align="center"></div>
                	      <input type="hidden" name="hdnLPeriod" id="hdnLPeriod">
                	    </td>
                	    <td width="5%" rowspan="2" class="gridDtlLbl" align="center">Months</td>
              	    </tr>
                	  <tr>
                	    <td class="gridDtlLbl">Records Location Code</td>
                	    <td class="gridDtlLbl">:</td>
                	    <td><?
						$qryBranchRecLoc = array("","Head Office based"=>"Head Office based","Store based"=>"Store based");
                    	$inqTSObj->DropDownMenu($qryBranchRecLoc,'cmbRecLocation','','class="inputs"');						
					?></td>
                	    <td class="gridDtlLbl">To</td>
                	    <td class="gridDtlLbl">:</td>
                	    <td><span class="gridDtlVal">
                	      <input name="txtTo" type="text" class="inputs" id="txtTo" onChange="valDateStartEnd(document.frmtransfer.txtFrom.value,document.frmtransfer.txtFrom.id,this.value); computeDate();" size="10" readonly="readonly">
                	    </span><a href="#"><img name="imgtoDate" id="imgtoDate" src="../../../images/cal_new.png" title="To Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
               	      </tr>
              	  </table></td>
                </tr>
                <tr>
                	<td colspan="3" class="parentGridHdr" align="center" height="20">PERMANENT TRANSFER</td>
                </tr>
                <tr>
                  <td colspan="3" class=""><table width="100%" border="0" cellspacing="1" cellpadding="1">
                    <tr>
                      <td width="18%" class="gridDtlLbl">Nature of Transfer</td>
                      <td width="1%" class="gridDtlLbl">:</td>
                      <td width="31%"><?
						$arrnature = array("","From one branch to another"=>"From one branch to another","From one company to another"=>"From one company to another");
                    	$inqTSObj->DropDownMenu($arrnature,'cmbNature','','class="inputs"');
					?></td>
                      <td width="10%" class="gridDtlLbl">Company</td>
                      <td width="1%" class="gridDtlLbl">:</td>
                      <td width="39%"><?
					  	$comp = $inqTSObj->getArrRes($inqTSObj->execQry("SELECT * FROM tblCompany WHERE compStat = 'A'"));
						$inqTSObj->DropDownMenu($inqTSObj->makeArr($comp,'compShort','compShort',''),'cmbCompany','','class="logInInputs" onchange="populatePayCat(this.value)"');
					?></td>
                    </tr>
                    <tr>
                      <td class="gridDtlLbl">Branch</td>
                      <td class="gridDtlLbl">:</td>
                      <td><?
						$qryBranch = $inqTSObj->makeArr($inqTSObj->getBranchByCompGrp(" and compCode='".$_SESSION['company_code']."'"),'brnShortDesc','brnShortDesc','');
                    	$inqTSObj->DropDownMenu($qryBranch,'cmbPermBranch','','class="inputs"');
					?></td>
                      <td class="gridDtlLbl">Department</td>
                      <td class="gridDtlLbl">:</td>
                      <td><?
						$qrydepartment = $inqTSObj->getArrRes($inqTSObj->execQry("SELECT deptShortDesc FROM tblDepartment WHERE deptLevel = '2' and compCode='{$_SESSION['company_code']}' ORDER BY deptDesc"));
                    	$inqTSObj->DropDownMenu($inqTSObj->makeArr($qrydepartment,'deptShortDesc','deptShortDesc',''),'cmbDepartment','','class="inputs" style="width:100%;"');
					?></td>
                    </tr>
                    <tr>
                      <td colspan="6"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                        <tr>
                          <td width="33%" class="gridDtlLbl">Date of Effectivity (For Permanent Transfer Only)</td>
                          <td width="1%" class="gridDtlLbl">:</td>
                          <td width="66%"><span class="gridDtlVal">
                            <input name="txtEffectivity" type="text" class="inputs" id="txtEffectivity" size="15" readonly="readonly">
                          </span><a href="#"><img name="imgEffectivityDate" id="imgEffectivityDate" src="../../../images/cal_new.png" title="Effectivity Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                        </tr>
                      </table></td>
                    </tr>
                  </table></td>
                </tr>
            </table>   
			<table cellspacing="1" cellpadding="1" border="0" width="100%" class="childGrid">
       	  <tr>
       	    <td class="parentGridHdr" colspan="6" align="center" height="20">OTHER CHANGES, IF ANY</td>
     	    </tr>
       	  <tr>
                	<td colspan="6" align="center"><table width="100%" border="0" cellspacing="1" cellpadding="1">
                	  <tr>
                	    <td width="52%" class="gridDtlLbl" height="20">&nbsp;</td>
                	    <td width="24%" class="gridDtlLbl" align="center">From</td>
                	    <td width="24%" class="gridDtlLbl" align="center">To</td>
              	    </tr>
                	  <tr>
                	    <td class="gridDtlLbl">Position Title (Lateral Transfer only. Unless with approved PR)</td>
                	    <td><?
						$qryFromPosition = $inqTSObj->makeArr($inqTSObj->getPositionDesc(),'posDesc','posDesc','');
                    	$inqTSObj->DropDownMenu($qryFromPosition,'cmbFromPosition','','class="inputs" style="width:100%;"');
					?></td>
                	    <td><?
						$qryToPosition = $inqTSObj->makeArr($inqTSObj->getPositionDesc(),'posDesc','posDesc','');
                    	$inqTSObj->DropDownMenu($qryToPosition,'cmbToPosition','','class="inputs" style="width:100%;"');
					?></td>
              	    </tr>
                	  <tr>
                	    <td class="gridDtlLbl">Salary (Permanent Transfer only. Re-alignment from one region to another) Basic</td>
                	    <td><span class="gridDtlVal">
                	      <input name="txtFromSalary" type="text" id="txtFromSalary" size="12">
                	    </span></td>
                	    <td><span class="gridDtlVal">
                	      <input name="txtToSalary" type="text" id="txtToSalary" size="12">
                	    </span></td>
              	    </tr>
                	  <tr>
                	    <td class="gridDtlLbl">ECOLA/COLA/RIV / ADVANCES</td>
                	    <td><span class="gridDtlVal">
               	        <input name="txtFromECOLA" type="text" id="txtFromECOLA" size="10">
                	    </span></td>
                	    <td><span class="gridDtlVal">
               	        <input name="txtToECOLA" type="text" id="txtToECOLA" size="10">
                	    </span></td>
              	    </tr>
                	  <tr>
                	    <td class="gridDtlLbl">Reason/Justification on employee transfer</td>
                	    <td><?
						$arrreason = array("","Fill in vacancy"=>"Fill in vacancy","Business Expansion"=>"Business Expansion","Others"=>"Others");
                    	$inqTSObj->DropDownMenu($arrreason,'cmbReason','','class="inputs" style="width:100%;"');
					?></td>
                    	<td><table width="100%" cellpadding="1" cellspacing="1">
                       		<tr>
                            	<td class="gridDtlLbl" width="40%">PRF #</td>
                                <td><input type="text" id="txtPRF" name="txtPRF" class="gridDtlVal"></td>
                            </tr>
                        </table></td>
               	      </tr>
                	  <tr>
                	    <td colspan="3"><textarea name="txtReason" id="txtReason" cols="100" rows="1" style="width:100%;"></textarea></td>
               	      </tr>
       	          </table></td>
              </tr>
            </table>   
            <table cellpadding="1" cellspacing="1" width="100%" class="childGrid" border="0">
            	<tr>
                	<td align="center"><input type="button" id="btnTransfer" name="btnTransfer" value="Print Transfer Report" onClick="validateValues();"/></td>
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
	Calendar.setup({
			  inputField  : "txtFrom",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgfrmDate"       // ID of the button
		}
	)	
	Calendar.setup({
			  inputField  : "txtTo",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgtoDate"       // ID of the button
		}
	)	

	Calendar.setup({
			  inputField  : "txtEffectivity",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgEffectivityDate"       // ID of the button
		}
	)	


function enableTransferType(id){
	if(id=="tempTrans"){
		$('cmbTempBranch').disabled=false;
		$('cmbRecLocation').disabled=false;	
		$('txtFrom').disabled=false;
		$('txtTo').disabled=false;
		$('imgfrmDate').style.visibility='visible';
		$('imgtoDate').style.visibility='visible';
		
		$('cmbNature').disabled=true;
		$('cmbPermBranch').disabled=true;
		$('cmbCompany').disabled=true;
		$('cmbDepartment').disabled=true;
		$('txtEffectivity').disabled=true;
		$('imgEffectivityDate').style.visibility='hidden';
		
		$('cmbNature').value=0;
		$('cmbPermBranch').value=0;
		$('cmbCompany').value=0;
		$('cmbDepartment').value=0;
		$('txtEffectivity').value='';
	}	
	else{
		$('cmbTempBranch').disabled=true;
		$('cmbRecLocation').disabled=true;	
		$('txtFrom').disabled=true;
		$('txtTo').disabled=true;
		$('imgfrmDate').style.visibility='hidden';
		$('imgtoDate').style.visibility='hidden';
		
		$('cmbNature').disabled=false;
		$('cmbPermBranch').disabled=false;
		$('cmbCompany').disabled=false;
		$('cmbDepartment').disabled=false;
		$('txtEffectivity').disabled=false;
		$('imgEffectivityDate').style.visibility='visible';
		
		$('cmbTempBranch').value=0;
		$('cmbRecLocation').value=0;	
		$('txtFrom').value='';
		$('txtTo').value='';
		$('loanperiod').innerHTML = '';
		$('hdnLPeriod').value = '';	
	}
}

function setValue(){
	var empInputs = $('frmtransfer').serialize();	
		$('btnTransfer').disabled=true;
		$('txtEmpNo').disabled=true;	
		$('cmbTempBranch').disabled=true;	
		$('cmbRecLocation').disabled=true;
		$('txtFrom').disabled=true;
		$('txtTo').disabled=true;	
		$('imgfrmDate').style.visibility='hidden';
		$('imgtoDate').style.visibility='hidden';
		$('imgEffectivityDate').style.visibility='hidden';
		$('cmbNature').disabled=true;
		$('cmbPermBranch').disabled=true;	
		$('cmbCompany').disabled=true;
		$('cmbDepartment').disabled=true;
		$('txtEffectivity').disabled=true;	
		$('cmbFromPosition').disabled=true;	
		$('cmbToPosition').disabled=true;	
		$('txtFromSalary').disabled=true;
		$('txtToSalary').disabled=true;	
		$('txtFromECOLA').disabled=true;
		$('txtToECOLA').disabled=true;	
		$('cmbReason').disabled=true;	
		$('txtReason').disabled=true;			
}

function refreshObj(id){
	var empInputs = $('frmtransfer').serialize(true);
	if(id==1){
		$('btnTransfer').disabled=false;
		$('txtEmpNo').disabled=false;	
		$('cmbFromPosition').disabled=false;	
		$('cmbToPosition').disabled=false;	
		$('txtFromSalary').disabled=false;
		$('txtToSalary').disabled=false;	
		$('txtFromECOLA').disabled=false;
		$('txtToECOLA').disabled=false;	
		$('cmbReason').disabled=false;	
		$('txtReason').disabled=false;	
		
	}
	if(id==0){
		window.location.href='transfer_employee.php';
	}
}

function isNumberInputEmpNoOnly(field, event) {
	var empno = $('txtEmpNo').value;
	var key, keyChar;
	if (window.event)
		key = window.event.keyCode;
	else if (event)
		key = event.which;
	else
		return true;
	
	// Check for special characters like backspace
	if (key == null || key == 0 || key == 8 || key == 27 || key == 13) {
		if (key == 13) {
		new Ajax.Request(
			   'transfer_employee.php?action=getEmployee&empno='+empno,
			  {
				 asynchronous : true, 
				 parameters	:	$('frmtransfer').serialize(true),    
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);	
		}
		return true;
	  }
	  // Check to see if it's a number
	  keyChar =  String.fromCharCode(key);
	  if (/\d/.test(keyChar)) 
		{
		 window.status = "";
		 return true;
		} 
	  else 
	   {
		window.status = "Field accepts numbers only.";
		return false;
	   }
}

function computeDate(){
	$('frmtransfer').serialize();	
	var frmDate = new Date($('txtFrom').value);
	var toDate =  new Date($('txtTo').value);
	var ansDate = Math.abs(frmDate.getMonth() - toDate.getMonth() + (12 * (frmDate.getFullYear() - toDate.getFullYear())));	
	if(isNaN(ansDate)){
		$('loanperiod').innerHTML = '';
		$('hdnLPeriod').innerHTML = '';
	}
	else{
		$('loanperiod').innerHTML = ansDate;	
		$('hdnLPeriod').value = ansDate;
	}
}

function validateValues(){
	$('frmtransfer').serialize();
	
	if($('txtEmpNo').value==""){
		alert('No employee to be transferred!');
		$('txtEmpNo').focus();
		return false;	
	}
	
	params = '<?=$_SERVER['PHP_SELF']?>?action=processReport';
	new Ajax.Request(params,{
		asynchronous	: true,
		method	:	'get',
		parameters	:	$('frmtransfer').serialize(true),
		onComplete	:	function(req){
				eval(req.responseText);
				$('btnTransfer').disabled=false;	
		},
		onCreate : function(){
			$('btnTransfer').disabled=true;	
		}
			
	})
}
</script>