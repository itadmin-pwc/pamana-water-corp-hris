<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_employee.Obj.php");
if($_GET['transType'] == 'Edit'){
	$btnMaint = 'EDIT';
	$disabledtype=" disabled ";
}
if($_GET['transType'] == 'Add'){
	$btnMaint = 'ADD';
	$disabled=" disabled ";
}
if ($_GET['effectivitydate'] != '') {
	$effDate=date('m/d/Y',strtotime($_GET['effectivitydate']));
} else {
	$effDate=date('m/d/Y');
}

$EmpAllowObj = new employeeAllowanceObj($_GET);
$sessionVars = $EmpAllowObj->getSeesionVars();
$EmpAllowObj->validateSessions('','MODULES');

$EmpAllowObj->compCode = $sessionVars['compCode'];
$EmpAllowObj->empNo    = $_GET['empNo'];

//$userInfo = $EmpAllowObj->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');


switch ($_GET['action']){
	case 'ADD':
			
			if($EmpAllowObj->checkEmpAllowance() > 0){
				echo "alert('Allowance Already Exist');";
			}
			else{
				if($EmpAllowObj->addEmpAllowance() == true){
					if ($_GET['prtag']==1) {
							if ($EmpAllowObj->ProcessPAF()) {
								echo "alert('PAF Allowance update processed');";
							} else {
								echo "alert('PAF Allowance update failed');";
							}
					} else {
						echo "alert('PAF Allowance Successfully queud');";
					}		
				}
				else{
					echo "alert('PAF Allowance update failed');";
				}
			}
		exit();
	break;
	case 'EDIT':
				if ($_GET['code'] != "1") {
					$check = $EmpAllowObj->editEmpAllowance();
				} else {
					if($EmpAllowObj->checkEmpAllowance() > 0){
						$check = $EmpAllowObj->editEmpAllowance();					
					} else {
						$check = $EmpAllowObj->addEmpAllowance();
					}	
				}		
			if($check == true){
					if ($_GET['prtag']==1) {
							if ($EmpAllowObj->ProcessPAF()) {
								echo "alert('PAF Allowance update processed');";
							} else {
								echo "alert('PAF Allowance update failed');";
							}
					} else {
						echo "alert('PAF Allowance Successfully queud');";
					}
			}
			else{
				echo "alert('Allowance Update Failed ');";
			}
		exit();
	break;
	
	case "getPayTransType":
		$arr_allowType = $EmpAllowObj->getAllowInfoDetail($_GET["allowCode"]);
		$allow_sked = $arr_allowType ["allowSked_type"];
		$allow_payTag = $arr_allowType ["allowTag_type"];
		
		echo "$('cmbAllwSkedDrop').value=".$allow_sked.";";
		echo "$('cmbAllwSked').value=".$allow_sked.";";
		echo "$('allowTagTxt').value='".$allow_payTag."';";
		echo "$('allowTag').value='".$allow_payTag."';";
		
		//echo $allow_sked."=".$allow_payTag;
		exit();
	break;
	
	
}

$empPayType = $EmpAllowObj->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');
$empPayType['empPayTag'];
$dvdTagEn = 'disabled';
	
$controlNo	  = $_GET['controlNo'];

if($_GET['transType'] == 'Edit'){
	$SpcfcEmpAllow = $EmpAllowObj->getSpecificEmpAllow($sessionVars['compCode'],$_GET['empNo'],$_GET['allwCode'],$_GET['code']);
	if ($_GET['code'] != 1) {
		$allwAmount_old   = $SpcfcEmpAllow['allowAmtold'];
		$allwAmount   = $SpcfcEmpAllow['allowAmt'];
		$controlNo	  = $SpcfcEmpAllow['controlNo'];
		$effDate	  = date('m/d/Y',strtotime($SpcfcEmpAllow['effectivitydate']));
		$refNo		  = $SpcfcEmpAllow['refNo'];
		
	} else {
		$allwAmount_old   = $SpcfcEmpAllow['allowAmt'];
		$allwAmount   =  $SpcfcEmpAllow['allowAmt'];;
	}
	$allowType    = $SpcfcEmpAllow['allowCode'];
	$allwSked     = $SpcfcEmpAllow['allowSked'];
	$AllwTaxTag   = $SpcfcEmpAllow['allowTaxTag'];
	$AllwPayTag   = $SpcfcEmpAllow['allowPayTag'];
	$allowTag     = $SpcfcEmpAllow['allowTag'];
	$allwStart    = (date("m/d/Y",strtotime($SpcfcEmpAllow['allowStart'])) == '01/01/1970') ? '' : date("m/d/Y",strtotime($SpcfcEmpAllow['allowStart']));
	$allwEnd      = (date("m/d/Y",strtotime($SpcfcEmpAllow['allowEnd'])) == '01/01/1970') ? '' : date("m/d/Y",strtotime($SpcfcEmpAllow['allowEnd']));
	$AllwStat	  = $SpcfcEmpAllow['allowStat'];
	if($empPayType['empPayType'] == 'M' && $allwSked == 3){
		$dvdTagEn = '';
	}
	
	$disable = 'disabled';
	$disabled_allowType = "disabled";
}

if ($_GET['refNo'] != "" && $refNo=="") {
		
	$refNo		  = $_GET['refNo'];
} elseif ($_GET['refNo'] == "" && $refNo=="") {
	$refNo 		  = $EmpAllowObj->getRefNo($compCode);

}

if($AllwStat=="")
	$AllwStat = "A";
	
	
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
		<FORM name="frmMaintEmpAllow" id="frmMaintEmpAllow" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" height="95%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
					  <td class="gridDtlLbl" align="left">Ref. No.</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td width="78%" class="gridDtlVal"><INPUT type="hidden" name="refNo" id="refNo" class="inputs" value="<?=$refNo;?>"><?=$refNo;?></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Control No.</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><INPUT type="text" name="controlNo" id="controlNo" class="inputs" value="<?=$controlNo;?>"></td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" width="21%">
							Type						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$EmpAllowObj->DropDownMenu($EmpAllowObj->makeArr(
								$EmpAllowObj->getAllowType($sessionVars['compCode']),'allowCode','allowDesc',''),
								'AllowType',$allowType,'class="inputs" '.$disabled_allowType.' onChange="GetPayTransTypeData();" ' 
							);
							?>						<input type="hidden" value="<?=$_GET['code']?>" name="code" id="code"></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >Old Amount						</td>
					  <td class="gridDtlLbl" align="center">&nbsp;</td>
					  <td class="gridDtlVal"><INPUT readonly type="text" name="txtAllwAmountold" id="txtAllwAmountold" class="inputs" value="<?=$allwAmount_old;?>"></td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Amount						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="txtAllwAmount" id="txtAllwAmount" class="inputs" value="<?=$allwAmount;?>">						</td>
					</tr>
					<tr>
					  <td height="44" align="left" class="gridDtlLbl" >Allowance Status</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><table width="200" cellpadding="0" cellspacing="0">
					    <tr>
					      <td class="inputs"><label>
					        <input type="radio" <?=($AllwStat=="A")? "checked":"";?>  name="cmbAllwStat" value="A" id="cmbAllwStat_0">
					        Active</label></td>
				        </tr>
					    <tr>
					      <td class="inputs"><label>
					        <input type="radio" <?=($AllwStat=="H")? "checked":"";?> name="cmbAllwStat" value="H" id="cmbAllwStat_1">
					        Delete</label></td>
				        </tr>
					    </table>
					  </td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Schedule						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
                        	
							<?
							$EmpAllowObj->DropDownMenu(array(
									'','1st Payroll of Month','2nd Payroll of Month','Attendance based'
								),'cmbAllwSkedDrop',$allwSked,'class="inputs" disabled  onchange="validateSched(this.value)"'
							);
							?>					
                            <INPUT type="hidden" name="cmbAllwSked" id="cmbAllwSked" class="inputs" value="">
                            	</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Allowance Tag						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$EmpAllowObj->DropDownMenu(array('M'=>'MONTHLY','D'=>'DAILY'),'allowTagTxt',$allowTag,'class="inputs" disabled');
							?>			
                            <input type="hidden" name="allowTag" id="allowTag" value="">			
                      	</td>
					</tr>
<!--					<tr>
						<td class="gridDtlLbl" align="left" >
							Tax Tag
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$EmpAllowObj->DropDownMenu(array(
									'N'=>'Not Taxable','Y'=>'Taxable',
								),'cmbAllwTaxTag',$AllwTaxTag,'class="inputs"'
							);
							?>									
						</td>
					</tr>-->
					<tr>
						<td class="gridDtlLbl" align="left" >
							Pay Tag						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal">
							<?
							$EmpAllowObj->DropDownMenu(array(
									'P'=>'Permanent'
								),'cmbAllwPayTag',$AllwPayTag,'class="inputs" onchange="vlidatePayTag(this.value)"'
							);
							?>						<input type="hidden" value="<?=date('m/d/Y');?>" name="today" id="today">
							<input type="hidden" value="0" name="prtag" id="prtag"></td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							 Start Date						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><input value="<?=$allwStart?>" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmMaintEmpAllow.txtAllwEnd.value);" class='inputs' name='txtAllwStart' id='txtAllwStart' maxLength='10' readonly size="10"/>
						  <a href="#" id="allwStrtDt">
						    	<img class="btnClendar" name="imgAllwStart" id="imgAllwStart" type="image" src="../../../images/cal_new.png" title="Start Date"
									<?
										if($_GET['transType'] == 'edit' && $AllwPayTag == 'T'){
											echo "style='display:'';'";
										}
									?>
						    	>						    </a>						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							 End Date						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><input value="<?=$allwEnd?>" type='text' class='inputs' name='txtAllwEnd' onChange="valDateStartEnd(document.frmMaintEmpAllow.txtAllwStart.value,document.frmMaintEmpAllow.txtAllwStart.id,this.value);" id='txtAllwEnd' maxLength='10' readonly size="10"/>
						  <a href="#" id="allwEndDt">
						    	<img  class="btnClendar" name="imgAllwEnd" id="imgAllwEnd" type="image" src="../../../images/cal_new.png" title="End Date" 
									<?
										
											echo "style='display:none;'";
										
									?>							    		
						    	>						    </a>						</td>
					</tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >Effectivity Date</td>
					  <td class="gridDtlLbl" align="center">&nbsp;</td>
					  <td class="gridDtlVal"><input value="<?=$effDate;?>" type='text' class='inputs' name='effetivitydate' id='effetivitydate' maxLength='10' readonly size="10"/>
				      <a href="#" id="allwEndDt2"><img  class="btnClendar" name="imgeffdate" id="imgeffdate" type="image" src="../../../images/cal_new.png" title="Effectivity Date"						    	>
				      <input type="hidden" value="0" name="prtag" id="prtag">
				      </a></td>
				  </tr>
					
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateEmpAllw(this.value,'<?=$_GET['empNo']?>')">						</td>
					</tr>
				</TABLE>
<INPUT type="hidden" name="hdnAllowCode" id="hdnAllowCode" value="<?=$allowType?>">
			<INPUT type="hidden" name="hdnDivideTag" id="hdnDivideTag" value="<?=$empPayType['empPayType']?>">
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function validateSched(sched){
		var payType = $F('hdnDivideTag');
		if(sched == 3 && payType == 'M'){
			$('divideTag').disabled=false;
		}
		else{
			$('divideTag').disabled=true;
		}
	}
	//disableRightClick();
	
	Calendar.setup({
			  inputField  : "txtAllwStart",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgAllwStart"       // ID of the button
		}
	)
	
	Calendar.setup({
			  inputField  : "txtAllwEnd",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgAllwEnd"       // ID of the button
		}
	)
	Calendar.setup({
			  inputField  : "effetivitydate",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgeffdate"       // ID of the button
		}
	)	
	
	function vlidatePayTag(payTagVal){
		if(payTagVal == 'T'){
			$('imgAllwStart').style.display='';
			$('imgAllwEnd').style.display='';
		}
		else{			
			$('imgAllwEnd').style.display='none';		
			$('txtAllwEnd').value="";	
		}
	}
	
	function validateEmpAllw(act,empNo){

		var empAllw = $('frmMaintEmpAllow').serialize(true);
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2})$/;
		
		if(empNo == ''){
			alert('Employee is Required');
			location.href='maintenance.employee.php';
			return false;
		}
		if(empAllw['AllowType'] == 0){
			alert('Allowance Type is Required');
			$('AllowType').focus();
			return false;			
		}
		if(empAllw['txtAllwAmount'] == ""){
			alert('Allowance Amount is Required');
			$('txtAllwAmount').focus();
			return false;			
		}
		if(!empAllw['txtAllwAmount'].match(numericExpWdec)){
			alert('Invalid Allowance Amount\nvalid : Numbers Only with two(2) decimal or without decimal');
			$('txtAllwAmount').focus();
			return false;			
		}
		
		if(empAllw['cmbAllwPayTag'] == 'P'){
			if(empAllw['txtAllwStart'] == ''){
				alert('Allowance Start Date is Required');
				$('allwStrtDt').focus();
				return false;				
			}			
		}
		if(empAllw['prtag'] == "1"){
			var todayDate = empAllw['today'];
			if (empAllw['effetivitydate'] != todayDate) {
				alert('Effectivity date must be equal to current date.');
				return false;
			}			
			var update = confirm('Are you sure you want to update now?');
			if (update == false) {
				return false;
			}	
		}	
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+empAllw['btnMaint']+"&empNo="+empNo,{
			method : 'get',
			parameters : $('frmMaintEmpAllow').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			},
			onCreate : function (){
				$('btnMaint').value='Loading...';
				$('btnMaint').disabled=true;
			},
			onSuccess : function (){
				$('btnMaint').value=act;
				$('btnMaint').disabled=false;
			}
		});	
	}
	
	function GetPayTransTypeData()
	{
		var empInputs = $('frmMaintEmpAllow').serialize(true);
		
		if (empInputs['cmbbranch'] != "0") {
			params = 'maintain_employee_allowance.php?action=getPayTransType&allowCode='+empInputs['AllowType']+'&refNo='+empInputs['refNo'];
			new Ajax.Request(params,{
				method : 'get',
				onComplete : function (req){
				eval(req.responseText);
				}	
			});
		}
	}
</SCRIPT>