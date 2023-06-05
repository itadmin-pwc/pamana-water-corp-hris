
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

	case 'info':	
		$qryEmpInfo = "SELECT * FROM tblEmpMast 
			WHERE compCode='".$_SESSION["company_code"]."' 
			AND empNo = '{$_GET['empNo']}'";
		$empInfo = $inqTSObj->getSqlAssoc($inqTSObj->execQry($qryEmpInfo));
		$empStatus = $empInfo['empStat'];
		if($empInfo == 0){
			echo 0;
			echo "$('txtVerify').value=''";
		}
		else{
			$arrPosDesc = $inqTSObj->getpositionwil(" where posCode='".$empInfo["empPosId"]."'",2);
			$empPosDesc = $arrPosDesc["posDesc"];
			
			$arrDeptDesc = $inqTSObj->getDeptDescGen($empInfo["compCode"],$arrPosDesc["divCode"],$arrPosDesc["deptCode"]);
			$empDeptDesc = $arrDeptDesc["deptDesc"];
				echo "<TABLE border='0' width='100%' cellpadding='1' cellspacing='1' class='childGrid' >";
				echo "<tr>";
					echo "<td width='18%' class='gridDtlLbl'>Lastname</td>";
					echo "<td width='1%' class='gridDtlLbl'>:</td>";
					echo "<td width='158' colspan='3' class='gridDtlVal'><input name='txtLName' type='text' id='txtLName' size='30' disabled value='".htmlspecialchars(addslashes($empInfo['empLastName']))."'></td>";
				echo "</tr>";            
				echo "<tr>";
					echo "<td width='18%' class='gridDtlLbl'>Firstname</td>";
					echo "<td width='1%' class='gridDtlLbl'>:</td>";
					echo "<td width='158' colspan='3' class='gridDtlVal'><input name='txtFName' type='text' id='txtFName' size='30' value='".htmlspecialchars(addslashes($empInfo['empFirstName']))."' disabled></td>";
				echo "</tr>";            
				echo " <tr>";
					echo "<td width='18%' class='gridDtlLbl'>Middlename</td>";
					echo "<td width='1%' class='gridDtlLbl'>:</td>";
					echo "<td width='158' colspan='3' class='gridDtlVal'><input name='txtMName' type='text' id='txtMName' size='30' value='".htmlspecialchars(addslashes($empInfo['empMidName']))."' disabled></td>";
				echo "</tr>";            
				echo "<tr>";
					echo "<td width='18%' class='gridDtlLbl'>Position</td>";
					echo "<td width='1%' class='gridDtlLbl'>:</td>";
					echo "<td width='158' colspan='3' class='gridDtlVal'><input name='txtPosition' type='text' id='txtPosition' size='50' value='".htmlspecialchars(addslashes($empPosDesc))."' disabled></td>";
			   echo "</tr>";            
			   echo "<tr>";
					echo "<td width='18%' class='gridDtlLbl'>Department</td>";
					echo "<td width='1%' class='gridDtlLbl'>:</td>";
					echo "<td width='158' colspan='3' class='gridDtlVal'><input name='txtDepartment' type='text' id='txtDepartment' size='50' value='".htmlspecialchars(addslashes($empDeptDesc))."' disabled></td>";
			   echo "</tr>"; 
			   echo "</table>";   
		}
		exit();
	break;

	case 'processmoa':
//		$seqno = $_GET['seqno'];
		$empno = $_GET['empno'];
		$compcode = $_GET['compcode'];
		$brncode = $_GET['brncode'];
		$edate = $_GET['edate'];
		echo "location.href = 'moa_pdf.php?empno=$empno&compcode=$compcode&brncode=$brncode&edate=$edate';";
		exit();
	break;	
	
	case 'getCompBranches':
		//echo $_GET['compCode'];
		$inqTSObj->DropDownMenu($inqTSObj->makeArr(
					$inqTSObj->getcompBranches($_GET['compCode']),'brnCode','brnDesc',''),
					'cmbBranch',$allowType,'class="inputs" onChange="checkValues();"' 
				);	
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
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> Memorandum Of Agreement</td>
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
            <td width="18%" class="gridToolbar" style="font-weight:bold">&nbsp;&nbsp;Employee Number : <input name="txtEmpNo" type="text" id="txtEmpNo" onKeyDown="getEmployee(event,this.value);" maxlength="9" <?=$searchTS_dis;?>><FONT color="#FF0000" class="gridDtlLbl" id="hlprMsg">
              
            </font><input type="hidden" name="txtVerify" id="txtVerify">
            <input type="hidden" name="txtStat" id="txtStat"></td>
          </tr>
          <tr><td class="parentGridDtl" colspan="7"><div id="info"><TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >   
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
                <td width="158" colspan="3" class="gridDtlVal"><input name="txtDepartment" type="text" id="txtDepartment" size="50" disabled>
                </td>
              </tr>            
              </TABLE></div>
              <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >   
              <tr>
                <td width="18%" class="gridDtlLbl">New Company</td>
                <td width="1%" class="gridDtlLbl">:</td>
                <td width="158" colspan="3" class="gridDtlVal"><?
							$inqTSObj->DropDownMenu($inqTSObj->makeArr(
								$inqTSObj->getCompanies(),'compCode','compName',''),
								'cmbCompany',$allowType,'class="inputs" onChange="getcompBranches(this.value);"' 
							);
							?></td>
              </tr>            
              <tr>
                <td width="18%" class="gridDtlLbl">New Branch</td>
                <td width="1%" class="gridDtlLbl">:</td>
                <td width="158" colspan="3" class="gridDtlVal"><div id="spNewBranch"><?
							$inqTSObj->DropDownMenu($inqTSObj->makeArr(
								$inqTSObj->getcompBranches(''),'','',''),
								'cmbBranch',$allowType,'class="inputs"' 
							);
							?></div></td>
              </tr>            
              <tr>
                <td width="18%" class="gridDtlLbl">Effectivity Date</td>
                <td width="1%" class="gridDtlLbl">:</td>
                <td width="158" colspan="3" class="gridDtlVal"><input name="txtEffectivity" type="text" id="txtEffectivity" size="10" class="inputs" readonly>
                <a href="#"><img name="imgEffectivityDate" id="imgEffectivityDate" src="../../../images/cal_new.gif" title="Effectivity Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
              </tr>            
              </TABLE>
              
               </td></tr>      
        </table>
	</td>
	</tr> 
	<tr><td class="parentGridDtl" align="center"><TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >   
              <tr>
                <td class="gridDtlVal" align="center"><input type="button" name="btnMaint" id="btnMaint" value="Print MOA" disabled onClick="processMOA();"/></td>
              </tr>            
              </TABLE>     
</td></tr>
                    
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
function viewtransfer(seqno){
	var frmContract = $('frmTS').serialize(true);
	var ans = confirm('Continue printing selected transferred data?');
	var params = 'moa.php?action=moaPrinting&seqno='+seqno;
	if(ans==true){
		new Ajax.Request(params, {
			method	: 'get',
			parameters	:	$('frmTS').serialize(),
			onComplete	:	function (req){
				eval(req.responseText);	
			}	
		});		
	}	
}

function clearFld(){
	$('txtLName').value='';
	$('txtFName').value='';
	$('txtMName').value='';
	$('txtPosition').value='';
	$('txtDepartment').value='';
	$('cmbCompany').value=0;
	$('cmbBranch').value=0;
	$('btnMaint').disabled=true;
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
						setTimeout(function(){
							$('hlprMsg').innerHTML='';
						},50000);
					} 
					else{
						$('info').innerHTML=req.responseText;
					}
				},
				onCreate : function (){
					$('hlprMsg').innerHTML='Searching employee...';
				},
				onSuccess : function (){
					$('hlprMsg').innerHTML='';
				}
			})
			break;
		}
}

function getcompBranches(compCode) {
	if($('txtLName').value==""){
		alert('No employee to transefer!');
		$('cmbCompany').value=0;
		$('cmbBranch').value=0;
		return false;	
	}
	if($('cmbCompany').value==<?=$_SESSION['company_code']?>){
		alert('Error found! Selected company is equal to current company of the employee.');
		$('spNewBranch').innerHTML="Failed to load branches...";
		//$('btnMaint').disabled=true;
		return false;
	}
	else{
		var ans =confirm('Are you sure you want to transfer the employee to '+$('cmbCompany').options[$('cmbCompany').selectedIndex].text+'?');
		if(ans){
			new Ajax.Request('moa.php?action=getCompBranches&compCode='+compCode,{
				  method : 'get',
				  onComplete : function (data){
					  $('spNewBranch').innerHTML=data.responseText;	
					  //$('btnMaint').disabled=true;
				  },
				  onCreate : function (){
					  $('spNewBranch').innerHTML="Loading branches under the selected company...";
					  //$('btnMaint').disabled=true;
				  }			
			  });
		}
		else{
			$('spNewBranch').innerHTML="Failed to load branches...";
			//$('btnMaint').disabled=true;
			return false;			
		}
	}
}

function checkValues(){
	$('frmTS').serialize(true);
	if (trim($('txtEmpNo').value)=='' || $('cmbCompany').value==0 || $('cmbBranch').value==0) {
		$('btnMaint').disabled = true;
	}
	else{
		$('btnMaint').disabled = false;
	}
}

function processMOA(){
	var empInputs = $('frmTS').serialize(true);	
	var compcode = $('cmbCompany').value;
	var branchcode = $('cmbBranch').value;
	var empno = $('txtEmpNo').value;
	var edate = $('txtEffectivity').value;
	if(empInputs['txtEmpNo']==""){
		alert('Employee is required. Enter employee.')	
		return false;
	}
	if(empInputs['cmbCompany']==0){
		alert('Company is required.');
		return false;	
	}
	if(empInputs['cmbBranch']==0){
		alert('Branch is required');
		return false;	
	}
	if(empInputs['txtEffectivity']==""){
		alert('Effectivity Date is required');
		return false;	
	}
	new Ajax.Request('moa.php?action=processmoa&empno='+empno+'&compcode='+compcode+'&brncode='+branchcode+'&edate='+edate,{
		method	: 'get',
		onComplete	: function (req){
			eval(req.responseText);
			}
		})
}

Calendar.setup({
	inputField	: "txtEffectivity",
	ifFormat	: "%m/%d/%Y",
	button		: "imgEffectivityDate"
	})
</script>