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
			echo "$('txtLName').value='".utf8_encode($empInfo['empLastName'])."';";
			echo "$('txtFName').value='".htmlspecialchars(addslashes($empInfo['empFirstName']))."';";
			echo "$('txtMName').value='".htmlspecialchars(addslashes($empInfo['empMidName']))."';";
			echo "$('txtPosition').value='".htmlspecialchars(addslashes($empPosDesc))."';";
			echo "$('txtDepartment').value='".htmlspecialchars(addslashes($empDeptDesc))."';";
			echo "$('txtStat').value='".$empStatus."';";
			echo "$('txtVerify').value='Y';";
			if($empStatus=="CN"){
				echo "$('cmbContract').value='1';";		
			}
			if($empStatus=="PR"){
				echo "$('cmbContract').value='2';";		
			}
			if($empStatus=="RG"){
				echo "$('cmbContract').value='0';";		
			}
		}
		exit();
	break;

	case 'contract':
		$trans=$_GET['trans'];
		$empnumber=$_GET['empno'];
		$sqlEmp = $inqTSObj->getEmpInfo($empnumber);
			if($_GET['trans']==1){
				echo "location.href = 'probationaryAgreement.php?trans=$trans&empno=$empnumber';";
			}
			elseif($_GET['trans']==2){
				echo "location.href = 'noticeOfRegularization.php?trans=$trans&empno=$empnumber';";
			}
			elseif($_GET['trans']==3){
				echo "location.href = 'transportationAgreement.php?trans=$trans&empno=$empnumber';";
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
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> Contract Report</td>
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
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtEmpNo" type="text" id="txtEmpNo" onKeyDown="getEmployee(event,this.value)" maxlength="9" <?=$searchTS_dis;?>><FONT color="#FF0000" class="gridDtlLbl" id="hlprMsg">
              
            </font><input type="hidden" name="txtVerify" id="txtVerify">
            <input type="hidden" name="txtStat" id="txtStat"></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Lastname</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtLName" type="text" id="txtLName" size="30" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Firstname</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtFName" type="text" id="txtFName" size="30" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Middlename</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtMName" type="text" id="txtMName" size="30" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Position</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtPosition" type="text" id="txtPosition" size="50" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Department</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtDepartment" type="text" id="txtDepartment" size="50" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Contract</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><? 
				$arrContract = array('0'=>'Select Contract','1'=>'Probationary Contract','2'=>'Notice of Regularization','3'=>'Agreement');
				$inqTSObj->DropDownMenu($arrContract,'cmbContract','','');	
			?></td>
            </tr>       
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>

						<CENTER>
                <input type="button" name="contractReports" id="contractReports" <? echo $searchTS4_dis; ?> value="Print" onClick="contractReport();">
				    </CENTER></td>
				  </tr>
			  </table> 
	</td>
	</tr> 
	<tr > 
		<td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#c3daf9; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
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
		alert('Contract/Agreement is required!');
		return false;	
	}
//	if(frmContract['cmbContract']==1 && frmContract['txtStat']=="PR"){
//		alert('Not valid report! Employee status is the same to selected report.');
//		return false;	
//	}
//	if(frmContract['cmbContract']==2 && frmContract['txtStat']=="RG"){
//		alert('Not valid report! Employee status is the same to selected report.');
//		return false;	
//	}
//	if(frmContract['cmbContract']==1 && frmContract['txtStat']=="RG"){
//		alert('Not valid report! Employee status is regular.');
//		return false;	
//	}

	var obj = $('cmbContract').value;
	var empnum = $('txtEmpNo').value;
	var params = 'probationaryContract.php?action=contract&trans='+obj+'&empno='+empnum;
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
						$('hlprMsg').innerHTML=' No Record Found.';
						$('contractReports').disabled=true;
						setTimeout(function(){
							$('hlprMsg').innerHTML='';
						},50000);
					} 
					else{
						eval(req.responseText);
						$('contractReports').disabled=false;
					}
				},
				onCreate : function (){
					$('hlprMsg').innerHTML='Searching employee...';
					$('contractReports').disabled=true;
				},
				onSuccess : function (){
					$('hlprMsg').innerHTML='';
				}
			})
			break;
		}
}
</script>