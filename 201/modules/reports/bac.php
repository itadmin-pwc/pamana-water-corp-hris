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
if ($_GET['code']=="") {
	$code = $_POST['code'];
} else {
	$code = $_GET['code'];
}

switch ($_GET['action']){
	case 'info':
		$qry="Select * from tblEmpMast where empNo='{$_GET['empNo']}' and empStat='RG'";
		$res = $inqTSObj->getSqlAssoc($inqTSObj->execQry($qry));
		if($res==0){
			echo 0;	
		}
		else{
					echo "<input class='inputs' name='empName' id='empName' type='text' size='35' maxlength='50' disabled value='".utf8_encode($res['empLastName']).", ".$res['empFirstName']." ".$res['empMidName']."'>";					
     	}
	exit();
	break;	
	
	case 'benefitsandcompensations':
		if($_GET['trans']==1){
			$transname = "SICK LEAVE (HDMF)";	
		}
		elseif($_GET['trans']==2){
			$transname = "MATERNITY LEAVE (HDMF)";		
		}
		elseif($_GET['trans']==3){
			$transname = "SICKNESS BENEFIT (SSS)";		
		}
		elseif($_GET['trans']==4){
			$transname = "MATERNITY BENEFIT WITH NOTIF. (SSS)";		
		}
		elseif($_GET['trans']==5){
			$transname = "MATERNITY BENEFIT WITHOUT NOTIF. (SSS)";		
		}

		if($_GET['trans']==1 or $_GET['trans']==2){
//			if($_GET['trans']==1){
//				$leavetype = "('04','16')";	
//			}
//			else{
//				$leavetype = "('06','11')";	
//			}
//			$resData = $inqTSObj->getLeaveApp("PG_TNA_TEST..tblTK_LeaveAppHist lvapp", "PG_TNA_TEST..tblTK_AppTypes lvtype",$_GET['empno'],$leavetype);
			$empno = $_GET['empno'];
			$type = $_GET['trans'];
			$dfrom = $_GET['dfrom'];
			$dto = $_GET['dto'];
//			if($resData==0){
//				echo "alert('NO ".$transname." FOUND.');";	
//			}	
//			else{
				echo "location.href = 'hdmf_certificate.php?empno=$empno&type=$type&dfrom=$dfrom&dto=$dto';";	
//			}	
		}
		else{
			$empno = $_GET['empno'];
			$type = $_GET['trans'];
			echo "location.href = 'sss_certificate.php?empno=$empno&type=$type';";		
		}
	exit();
	break;
}

?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
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
<script type="text/javascript" src="../../../includes/datepicker/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<link rel="stylesheet" type="text/css" href="../../../includes/datepicker/dhtmlxCalendar/codebase/dhtmlxcalendar.css"></link>
<link rel="stylesheet" type="text/css" href="../../../includes/datepicker/dhtmlxCalendar/codebase/skins/dhtmlxcalendar_dhx_skyblue.css"></link>
<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
<script>
var myCalendar;
function doOnLoad() {
	myCalendar = new dhtmlXCalendarObject(["txtDateFrom","txtDateTo"]);
}

function getEmployee(evt,eleVal){	
	var param = '?action=info&empNo='+eleVal;
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
						$('cmdbac').disabled=true;
						setTimeout(function(){
							$('hlprMsg').innerHTML='';
						},50000);
					} 
					else{
						$('bacdata').innerHTML=req.responseText;
						$('cmdbac').disabled=false;
					}
				},
				onCreate : function (){
					$('hlprMsg').innerHTML='Searching employee...';
					$('cmdbac').disabled=true;
				},
				onSuccess : function (){
					$('hlprMsg').innerHTML='';
				}
			})
			break;
		}
}

function clearFld(){
	$('empName').value='';
} 

function loanCertificate(){
	var empInputs = $('frmTS').serialize(true);
	var param = $('cmbType').value;
	var empno = $('empNo').value;
	var dfrom = $('txtDateFrom').value;
	var dto = $('txtDateTo').value;
	
	if(empInputs['empNo']==""){
		alert('Please enter employee number then press enter.');
		$('empNo').focus();
		return false	
	}
	if(empInputs['cmbType']==0){
		alert('Type of Certificate is required!');
		$('cmbType').focus();
		return false;	
	}
	if(empInputs['cmbType']!=0){// && empInputs['txtDateFrom']=="" && empInputs['txtDateFrom']==""){
		if(empInputs['txtDateFrom']==""){
			alert('Start date of '+document.getElementById('cmbType').options[document.getElementById('cmbType').selectedIndex].text+' is required.');	
			return false;
		}
		else if (empInputs['txtDateTo']==""){
			alert('End date of '+document.getElementById('cmbType').options[document.getElementById('cmbType').selectedIndex].text+' is required.');	
			return false;	
		}
	}
	
	if(Date.parse(dfrom)>Date.parse(dto)){
		alert('Invalid date range.');
		return false;	
	}
	
	new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=benefitsandcompensations&trans='+param+'&empno='+empno+'&dfrom='+dfrom+'&dto='+dto,{
		method	: 'get',
		parameters	: $('frmTS').serialize(),
		onComplete 	: function(req){
			eval(req.responseText);
			},
		})
}

function dates(){
	var sel = document.getElementById('cmbType').value;
	if(sel==1 || sel==2){
		document.getElementById('txtDateFrom').disabled=false;	
		document.getElementById('txtDateTo').disabled=false;	
	}
	else{
		document.getElementById('txtDateFrom').disabled=true;	
		document.getElementById('txtDateTo').disabled=true;	
		document.getElementById('txtDateFrom').value="";	
		document.getElementById('txtDateTo').value="";			
	}
}
</script>
</HEAD>
	<BODY onLoad="doOnLoad();">
<form name="frmTS" id="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table  cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Compensation and Benefits</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="6"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
            <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?></td>
          </tr>
          
          <tr> 
            <td colspan="3" class="gridToolbar" style="font-weight:bold">Employee Number :
              <input tabindex='11' class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="20" maxlength="15" onKeyDown="getEmployee(event,this.value);">              <FONT color="#FF0000" class="gridDtlLbl" id="hlprMsg"></font></td>
            </tr>
          <tr><td colspan="3"><table width="100%" cellpadding="1" cellspacing="1" class="childGrid"> 
          <tr>
            <td width="18%" class="gridDtlLbl">Employee Name </td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><div id="bacdata"><input class="inputs" name="empName" id="empName" type="text" size="35" maxlength="50" value="<?=$_POST['empName'];?>" disabled></div></td>
          </tr>
          <tr>
            <td class="gridDtlLbl">Type</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><font class="byOrder">
              <?
				$inqTSObj->DropDownMenu(array(''=>'','1'=>'Sick Leave (HDMF)','2'=>'Maternity Leave (HDMF)','3'=>'Non Advance Sickness Benefit (SSS)','4'=>'Non Advance Maternity Benefit with Notif. (SSS)','5'=>'Non Advance Maternity Benefit without Notif. (SSS)'),'cmbType',$_GET['cmbType'],'class="inputs" onChange="dates();"');
			 ?>
            </font></td>
           </tr>     
          <tr>
            <td width="18%" class="gridDtlLbl">Start date of Sick Leave</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input type="text" name="txtDateFrom" id="txtDateFrom" readonly></td>
           </tr>
          <tr>
            <td class="gridDtlLbl">End date of Sick Leave</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input type="text" name="txtDateTo" id="txtDateTo"  readonly></td>
           </tr>
            </table></td>
        </tr>     
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" class="inputs" name="cmdbac" id="cmdbac" <? echo $searchTS4_dis; ?> value="Print Compensation and Benefits" onClick="loanCertificate();">
              </CENTER>
					</td>
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