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
$user=$inqTSObj->getUserLogInInfoForMenu($_SESSION['employee_number']);
$ulevel=$user['userLevel'];

if ($_GET['code']=="") {
	$code = $_POST['code'];
} else {
	$code = $_GET['code'];
}

switch ($_GET['action']){

	case 'getEmpInfo':	
		$qryEmpInfo = "SELECT * FROM tblEmpMast 
			WHERE compCode='".$_SESSION["company_code"]."' 
			AND empNo = '{$_GET['empNo']}' 
			AND empStat='RG'";
		$empInfo = $inqTSObj->getSqlAssoc($inqTSObj->execQry($qryEmpInfo));
		$empStatus = $empInfo['employmentTag'];
		if($empInfo == 0){
			echo 0;
			echo "$('txtVerify').value=''";
		}
		else{
			$arrPosDesc = $inqTSObj->getpositionwil(" where posCode='".$empInfo["empPosId"]."'",2);
			$empPosDesc = $arrPosDesc["posDesc"];
			
			$arrDeptDesc = $inqTSObj->getDeptDescGen($empInfo["compCode"],$arrPosDesc["divCode"],$arrPosDesc["deptCode"]);
			$empDeptDesc = $arrDeptDesc["deptDesc"];
			$municipality = $inqTSObj->empMunicipality($empInfo['empMunicipalityCd']);
			$province = $inqTSObj->empProvince($empInfo['empProvinceCd']);
			echo "$('txtLName').value='".utf8_encode($empInfo['empLastName'])."';";
			echo "$('txtFName').value='".htmlspecialchars(addslashes($empInfo['empFirstName']))."';";
			echo "$('txtMName').value='".htmlspecialchars(addslashes($empInfo['empMidName']))."';";
			echo "$('txtPosition').value='".htmlspecialchars(addslashes($empPosDesc))."';";
			echo "$('txtDepartment').value='".htmlspecialchars(addslashes($empDeptDesc))."';";
			echo "$('txtAddress').value='".htmlspecialchars(addslashes($empInfo['empAddr1'].", ".$empInfo['empAddr2'].", ".$municipality.", ".$province))."';";
			echo "$('txtSpouse').value='".$empInfo['empSpouseName']."';";//
			echo "$('txtSSS').value='".$empInfo['empSssNo']."';";//
			echo "$('txtStat').value='".$empStatus."';";
			echo "$('txtVerify').value='Y';";
			$status = $empInfo['empMarStat'];
//			$birthDate  = date("m-d-Y",strtotime($empInfo['empBday']));
//			$birthDate  = explode("-",$birthDate );
//			$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
//    				? ((date("Y") - $birthDate[2]) - 1)
//    				: (date("Y") - $birthDate[2]));
			echo "$('cmbStatus').value='".$status."';";		

		}
		exit();
	break;

	case 'contract':
		$trans=$_GET['trans'];
		$empnumber=$_GET['empno'];
		$lname = $_GET['lname'];
		$fname = $_GET['fname'];
		$mname = $_GET['mname'];
		$position = $_GET['position'];
		$department = $_GET['department'];
		$address = $_GET['address'];
		$spouse = $_GET['spouse'];
		$sss = $_GET['sss'];
		$status = $_GET['status'];
		$sqlEmp = $inqTSObj->getEmpInfo($empnumber);
			if($_GET['trans']==1){
				echo "location.href = 'underTaking.php?trans=$trans&empno=$empnumber&lname=$lname&fname=$fname&mname=$mname&position=$position&department=$department&address=$address&spouse=$spouse&status=$status&sss=$sss';";
			}
			elseif($_GET['trans']==2){
				echo "location.href = 'trainingAgreement.php?trans=$trans&empno=$empnumber&lname=$lname&fname=$fname&mname=$mname&position=$position&department=$department&address=$address&spouse=$spouse&status=$status&sss=$sss';";
			}
		exit();
	break;	
}
?>
<HTML>
	<HEAD>
<TITLE><?=SYS_TITLE?></TITLE>
<style>@import url('../../style/reports.css');</style>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="" id="frmTS">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> Undertaking and Training Agreement</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'>
              <input type="hidden" name="userlevel" id="userlevel" value="<?=$ulevel;?>"></td>
          </tr>
          
          <tr>
            <td width="18%" class="gridDtlLbl">Employee Number</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input class="inputs" name="txtEmpNo" type="text" id="txtEmpNo" onKeyDown="getEmployee(event,this.value)" maxlength="9" <?=$searchTS_dis;?>><FONT color="#FF0000" class="gridDtlLbl" id="hlprMsg">
              
            </font><input type="hidden" name="txtVerify" id="txtVerify">
            <input type="hidden" name="txtStat" id="txtStat"></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Lastname</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input class="inputs" name="txtLName" type="text" id="txtLName" size="30" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Firstname</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input class="inputs" name="txtFName" type="text" id="txtFName" size="30" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Middlename</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input class="inputs" name="txtMName" type="text" id="txtMName" size="30" disabled></td>
            </tr>            
          <tr>
            <td class="gridDtlLbl">Civil Status</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><? 
				$arrStatus = array('','SG'=>'Single','ME'=>'Married','SP'=>'Separated','WI'=>'Widow(er)');
				$inqTSObj->DropDownMenu($arrStatus,'cmbStatus','','disabled="disabled"');	
			?></td>
          </tr>
          <tr>
            <td width="18%" class="gridDtlLbl">Position</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input class="inputs" name="txtPosition" type="text" id="txtPosition" size="50" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Department</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input class="inputs" name="txtDepartment" type="text" id="txtDepartment" size="50" disabled></td>
            </tr>            
          <tr>
            <td class="gridDtlLbl">Address</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><input name="txtAddress" class="inputs" type="text" id="txtAddress" size="50" disabled></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">Spouse</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><input name="txtSpouse" class="inputs" type="text" id="txtSpouse" size="50" disabled></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">SSS ID No.</td>
            <td class="gridDtlLbl">:</td>
            <td colspan="3" class="gridDtlVal"><input name="txtSSS" class="inputs" type="text" id="txtSSS" size="35" disabled></td>
          </tr>
          <tr>
            <td width="18%" class="gridDtlLbl">Agreement Type</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 
				$arrContract = array(''=>'Select Type','1'=>'Undertaking','2'=>'Training Agreement');
				$inqTSObj->DropDownMenu($arrContract,'cmbContract','','');	
			?></td>
            </tr>       
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>

						<CENTER>
                <input type="button" name="contractReports" class="inputs" id="contractReports" <? echo $searchTS4_dis; ?> value="Print Report" onClick="contractReport();">
				    </CENTER></td>
				  </tr>
			  </table> 
	</td>
	</tr> 
	<tr > 
		<td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#fff; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>
<script>
function contractReport() {
	var frmContract = $('frmTS').serialize(true);
	if(frmContract['txtLName']==""){
		alert('No employee to process.');
		return false;	
	}
	if(frmContract['cmbContract']==0){
		alert('Undertaking/Training Agreement is required!');
		return false;	
	}

	var obj = $('cmbContract').value;
	var empnum = $('txtEmpNo').value;
	var lname = $('txtLName').value;
	var fname = $('txtFName').value;
	var mname = $('txtMName').value;
	var position = $('txtPosition').value;
	var department = $('txtDepartment').value;
	var address = $('txtAddress').value;
	var spouse = $('txtSpouse').value;
	var status = $('cmbStatus').value;
	var sss = $('txtSSS').value;
	var params = 'underTakingTrainingAgreement.php?action=contract&trans='+obj+'&empno='+empnum+'&lname='+lname+'&fname='+fname+'&mname='+mname+'&position='+position+'&department='+department+'&department='+department+'&address='+address+'&spouse='+spouse+'&status='+status+'&sss='+sss;
	new Ajax.Request(params,{
		method	: 'get',
		parameters	: $('frmTS').serialize(),
		onComplete	: function (req){
			eval(req.responseText);
			}
		});
}

function clearFld(){
	$('txtLName').value='';
	$('txtFName').value='';
	$('txtMName').value='';
	$('txtPosition').value='';
	$('txtDepartment').value='';
	$('cmbContract').value=0;
} 

function getEmployee(evt,eleVal){	
	var param = '?action=getEmpInfo&empNo='+eleVal;
	var k = evt.keyCode | evt.which;
		
		switch(k){
			case 8:
				clearFld();
			break;
			case 13:
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){

					if(parseInt(req.responseText) == 0){
						$('hlprMsg').innerHTML=' No Record Found! Please encode employee information';
						$('txtLName').disabled=false;
						$('txtFName').disabled=false;
						$('txtMName').disabled=false;
						$('txtPosition').disabled=false;
						$('txtDepartment').disabled=false;
						$('txtAddress').disabled=false;
						$('txtSpouse').disabled=false;
						$('txtSSS').disabled=false;
						$('cmbStatus').disabled=false;
					} 
					else{
						eval(req.responseText);
						$('contractReports').disabled=false;
					}
				},
				onCreate : function (){
					$('hlprMsg').innerHTML='Searching employee...';
					$('contractReports').disabled=false;
				},
				onSuccess : function (){
					$('hlprMsg').innerHTML='';
					$('txtAddress').disabled=false;
					$('txtSpouse').disabled=false;
					$('txtSSS').disabled=false;
					//$('cmbStatus').disabled=false;
				}
			})
			break;
		}
}
</script>