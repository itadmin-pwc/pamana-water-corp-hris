<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("transferred.obj.php");
$transObj = new transferredObj($_GET,$_SESSION);
$sessionVars = $transObj->getSeesionVars();
$transObj->validateSessions('','MODULES');


//$userInfo = $transObj->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');


switch ($_GET['action']){
	case 'add':
		if($transObj->AddTransEmp($_GET)){
			echo "alert('Employee has been successfully added to transfer queue list.')";
			//echo "alert('Employee Transfer Successfully added');";
//			$arrNewComp = $transObj->getTransCompany($_GET['cmbCompany']);
//			if($transObj->deleteExistingRecord($_GET['txtempNo'],$arrNewComp['db'])){
//				if($transObj->transferOtherInfo($_GET['txtempNo'],$arrNewComp['db'],$_GET['cmbCompany'])){
//					echo "alert('Successfully transferred.');";		
//				}
//				else{
//					echo "alert('Other information failed to transfer. You have to re-encode it manually to new company.');";			
//				}
//			}
		}
		else{
			//$transObj->transferOtherInfo($_GET['txtempNo'],$arrNewComp['db'],$_GET['cmbCompany']);
			echo "alert('Employee Transfer saving failed');";
		}
		exit();
	break;
	case 'delete':
		if($transObj->delTransEmp($_GET['empNo'])){
			echo "alert('Employee Transfer Successfully deleted');";
		}
		else{
			echo "alert('Employee Transfer deletion failed');";
		}
		exit();
	break;
	
	case 'getCompBranches':
		//echo $_GET['compCode'];
		$transObj->DropDownMenu($transObj->makeArr(
					$transObj->getcompBranches($_GET['compCode']),'brnCode','brnDesc',''),
					'cmbBranch',$allowType,'class="inputs" onChange="checkValues();"' 
				);	
		exit();	
	break;
	case 'getEmpInfo':
		$arrEmpInfo = $transObj->getTransEmpInfo($_GET['empNo']);
		if ($arrEmpInfo['empLastName']!='') {
			$name =  $arrEmpInfo['empLastName'] . ", " .$arrEmpInfo['empFirstName'];
			$branch =  $arrEmpInfo['brnDesc'];
			$position =  $arrEmpInfo['posDesc'];
			$salary =  ($arrEmpInfo['empPayType'] == 'M') ? number_format($arrEmpInfo['empMrate'],2) . "/Month": number_format($arrEmpInfo['empDrate'],2)."/Day";
			$sssNo =  $arrEmpInfo['empSssNo'];

			echo "$('spName').innerHTML = '$name';\n";
			echo "$('spBranch').innerHTML = '$branch';\n";
			echo "$('spPosition').innerHTML = '$position';\n";
			echo "$('spSalary').innerHTML = '$salary';\n";
			echo "$('spSSSNo').innerHTML = '$sssNo';\n";
			echo "$('btnMaint').disabled = true;\n";

			echo "$('hdfname').value = '{$arrEmpInfo['empFirstName']}';\n";
			echo "$('hdlname').value = '{$arrEmpInfo['empLastName']}';\n";
			echo "$('hdmname').value = '{$arrEmpInfo['empMidName']}';\n";
			echo "$('hdbranch').value = '$branch';\n";
			echo "$('hdposition').value = '$position';\n";
			echo "$('hdsalary').value = '{$arrEmpInfo['empMrate']}';\n";
			echo "$('hdsssno').value = '$sssNo';\n";
		} else {
			echo "$('btnMaint').disabled = true;\n";
			echo "$('spName').innerHTML = '';\n";
			echo "$('spBranch').innerHTML = '';\n";
			echo "$('spPosition').innerHTML = '';\n";
			echo "$('spSalary').innerHTML = '';\n";
			echo "$('spSSSNo').innerHTML = '';\n";

			echo "$('hdfname').value = '';\n";
			echo "$('hdlname').value = '';\n";
			echo "$('hdmname').value = '';\n";
			echo "$('hdbranch').value = '';\n";
			echo "$('hdposition').value = '';\n";
			echo "$('cmbCompany').value = 0;\n";
			echo "$('cmbBranch').value = 0;\n";
			echo "$('hdsalary').value = '';\n";
			echo "$('hdsssno').value = '';\n";			
			echo "alert('Invalid Employee No.')";
		}
		exit();
	break;
}


	
	
	//echo $disabled_allowType;
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">
		@import url("../../../includes/calendar/calendar-blue.css");.headertxt {font-family: verdana; font-size: 11px;}
        </STYLE>
		<!--end calendar lib-->
</HEAD>
	<BODY>
		<FORM name="frmTransEmp" id="frmTransEmp" onSubmit="return false;" method="post">
					
				<TABLE border="0" width="100%"  cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
					  <td height="21" align="left" class="gridDtlLbl">Employee No.</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td width="78%" class="gridDtlVal"><INPUT type="text" name="txtempNo" onKeyPress="getEmpInfo(this.value,event)" onClick="clearEmpTxt()" id="txtempNo" class="inputs" ></td>
			    </tr>
					<tr>
					  <td height="20" align="left" class="gridDtlLbl">Name</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><span id='spName' name='spName'></span> 
                      <input type="hidden" value="" name="hdfname" id="hdfname">
                      <input type="hidden" value="" name="hdlname" id="hdlname">
                      <input type="hidden" value="" name="hdmname" id="hdmname">
                      </td>
				  </tr>
					<tr>
					  <td height="20" align="left" class="gridDtlLbl">Branch</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><span id='spBranch' name='spBranch'></span>
                      <input type="hidden" value="" name="hdbranch" id="hdbranch">
                      </td>
				  </tr>
					<tr>
					  <td height="20" align="left" class="gridDtlLbl">Position</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><span id='spPosition' name='spPosition'></span>
                      <input type="hidden" value="" name="hdposition" id="hdposition">
                      </td>
				  </tr>
					<tr>
					  <td height="20" align="left" class="gridDtlLbl">Salary</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><span id='spSalary' name='spSalary'></span>
                      <input type="hidden" value="" name="hdsalary" id="hdsalary">
                      </td>
				  </tr>
					<tr>
					  <td height="20" align="left" class="gridDtlLbl">SSS No.</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><span id='spSSSNo' name='spSSSNo'></span>
                      <input type="hidden" value="" name="hdsssno" id="hdsssno">
                      </td>
				  </tr>
					<tr>
						<td width="21%" height="20" align="left" class="gridDtlLbl">
							New Company						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$transObj->DropDownMenu($transObj->makeArr(
								$transObj->getCompanies(),'compCode','compName',''),
								'cmbCompany',$allowType,'class="inputs" onChange="getcompBranches(this.value);"' 
							);
							?>						</td>
				  </tr>
					<tr>
					  <td height="21" align="left" class="gridDtlLbl" >New Branch						</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><span id='spNewBranch'><?
							$transObj->DropDownMenu($transObj->makeArr(
								$transObj->getcompBranches(''),'brnCode','brnDesc',''),
								'cmbBranch',$allowType,'class="inputs"' 
							);
							?></span></td>
				  </tr>

					
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<INPUT type="button" disabled name="btnMaint" id="btnMaint" value="  Save  " class="inputs" onClick="SaveTransEmp();">						</td>
					</tr>
				</TABLE>
		</FORM>
	</BODY>
</HTML>
