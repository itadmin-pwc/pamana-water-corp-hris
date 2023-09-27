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
			echo "$('txtVerify').value='Y'";
		}
		exit();
	break;

	case 'printCIDIS':
		$empnumber=$_GET['empno'];
		$sqlEmp = $inqTSObj->getEmpInfo($empnumber);
		echo "location.href = 'cidis_pdf.php?trans=$trans&empno=$empnumber';";
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
		<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
</HEAD>
	<BODY>
<form name="frmTS" method="post" action="" id="frmTS">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> Company Identification Information Sheet</td>
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
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtEmpNo" type="text" class="inputs" id="txtEmpNo" onKeyDown="getEmployee(event,this.value)" maxlength="9" <?=$searchTS_dis;?>><FONT color="#FF0000" class="gridDtlLbl" id="hlprMsg">
              
            </font><input type="hidden" name="txtVerify" id="txtVerify">
            <input type="hidden" name="txtStat" id="txtStat"></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Lastname</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtLName" type="text" class="inputs" id="txtLName" size="30" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Firstname</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtFName" type="text" class="inputs" id="txtFName" size="30" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Middlename</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtMName" type="text" class="inputs" id="txtMName" size="30" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Position</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtPosition" type="text" class="inputs" id="txtPosition" size="50" disabled></td>
            </tr>            
          <tr>
            <td width="18%" class="gridDtlLbl">Department</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="158" colspan="3" class="gridDtlVal"><input name="txtDepartment" type="text" class="inputs" id="txtDepartment" size="50" disabled></td>
            </tr>            
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>

						<CENTER>
                <input type="button" name="cidisReports" class="inputs" id="cidisReports" <? echo $searchTS4_dis; ?> value="Print CIDIS" onClick="processCIDIS();">
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
function processCIDIS() {
	var frmContract = $('frmTS').serialize(true);
	if($('txtLName').value==""){
		alert('No employee to process/print.');
		return false;	
	}
	var empnum = $('txtEmpNo').value;
	var params = 'cidis.php?action=printCIDIS&empno='+empnum;
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
						$('cidisReports').disabled=true;
						setTimeout(function(){
							$('hlprMsg').innerHTML='';
						},50000);
					} 
					else{
						eval(req.responseText);
						$('cidisReports').disabled=false;
					}
				},
				onCreate : function (){
					$('hlprMsg').innerHTML='Searching employee...';
					$('cidisReports').disabled=true;
				},
				onSuccess : function (){
					$('hlprMsg').innerHTML='';
				}
			})
			break;
		}
}
</script>