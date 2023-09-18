<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("profile_maintenance_employee.Obj.php");

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
					echo "alert('Allowance Successfully Saved');";
				}
				else{
					echo "alert('Allowance Failed Saved');";
				}
			}
		exit();
	break;
	case 'EDIT':
			if($EmpAllowObj->editEmpAllowance() == true){
				echo "alert('Allowance Successfully Updated');";
			}
			else{
				echo "alert('Allowance Failed Update');";
			}
		exit();
	break;
}

$empPayType = $EmpAllowObj->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');
$empPayType['empPayTag'];
$dvdTagEn = 'disabled';

if($_GET['transType'] == 'Edit'){
	
	$SpcfcEmpAllow = $EmpAllowObj->getSpecificEmpAllow($sessionVars['compCode'],$_GET['empNo'],$_GET['allwCode']);

	$allowType    = $SpcfcEmpAllow['allowCode'];
	$allwAmount   = $SpcfcEmpAllow['allowAmt'];
	$allwSked     = $SpcfcEmpAllow['allowSked'];
	$AllwTaxTag   = $SpcfcEmpAllow['allowTaxTag'];
	$AllwPayTag   = $SpcfcEmpAllow['allowPayTag'];
	$AllwTag 	  = $SpcfcEmpAllow['allowTag'];
	$allwStart    = (date("Y-m-d",strtotime($SpcfcEmpAllow['allowStart'])) == '0000-00-00') ? '' : date("Y-m-d",strtotime($SpcfcEmpAllow['allowStart']));
	$allwEnd      = (date("Y-m-d",strtotime($SpcfcEmpAllow['allowEnd'])) == '0000-00-00') ? '' : date("Y-m-d",strtotime($SpcfcEmpAllow['allowEnd']));
	$AllwStat	  = $SpcfcEmpAllow['allowStat'];
	
	if($AllwTag=='D')
		$lblCaption = "Amount should be in Daily Basis";
	else
		$lblCaption = "Amount should be in Monthly Basis";
	
	
	if($empPayType['empPayType'] == 'M' && $allwSked == 3){
		$dvdTagEn = '';
	}
	
	$disable = 'disabled';
}


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
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	<BODY>
		<FORM name="frmMaintEmpAllow" id="frmMaintEmpAllow" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" height="95%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td class="gridDtlLbl" align="left" width="15%">
							Type
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$EmpAllowObj->DropDownMenu($EmpAllowObj->makeArr2(
								$EmpAllowObj->getAllowType($sessionVars['compCode']),'allowCode','sprtPS','allowDesc',''),
								'cmbAllowType',$allowType,'class="inputs"' . $disable
							);
							?>
						</td>
					</tr>
                    <tr>
						<td class="gridDtlLbl" align="left" >
							Allowance Tag
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
                        	<table border='0' width="100%">
                            	<tr>
                                	<td width="30%">
										<?
                                            $EmpAllowObj->DropDownMenu(array(
                                                    'M'=>'Monthly','D'=>'Daily',
                                                ),'cmbAllwTag',$AllwTag,'class="inputs" onchange="reptType(this.value)"'
                                            );
                                        ?>
                                   	</td>
                                    <td width="70%" id="TRAUBDB" class="gridDtlVal">
                                    	<FONT color="Red" id="lblCaption"><?php  echo ($lblCaption!=""?$lblCaption:"Amount should be in Monthly Basis."); ?></FONT>
                                    </td>
                                </tr>
                             </table>									
						</td>
                        
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Amount
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="txtAllwAmount" id="txtAllwAmount" class="inputs" value="<?=$allwAmount;?>">
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Schedule
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$EmpAllowObj->DropDownMenu(array(
									'','1st Payroll of Month','2nd Payroll of Month','Both Payrolls'
								),'cmbAllwSked',$allwSked,'class="inputs"'
							);
							?>
						</td>
					</tr>
<!--					<tr>
						<td class="gridDtlLbl" align="left" >
							Tag
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$EmpAllowObj->DropDownMenu(array('N'=>'NO','Y'=>'YES'),'divideTag',$divideTag,'class="inputs" '.$dvdTagEn);
							?>
						</td>
					</tr>
					<tr>
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
							Pay Tag
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$EmpAllowObj->DropDownMenu(array(
									'P'=>'Permanent','T'=>'Temporary'
								),'cmbAllwPayTag',$AllwPayTag,'class="inputs" onchange="vlidatePayTag(this.value)"'
							);
							?>	
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							 Start Date
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<input value="<?=$allwStart?>" type='text' class='inputs' name='txtAllwStart' id='txtAllwStart' maxLength='10' readonly size="10"/> 
						    <a href="#" id="allwStrtDt">
						    	<img class="btnClendar" name="imgAllwStart" id="imgAllwStart" type="image" src="../../../images/cal_new.png" title="Start Date"
									<?
										if($_GET['transType'] == 'Edit' && $AllwPayTag == 'T'){
											echo "style='display:'';'";
										}
									?>
						    	>
						    </a>									
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							 End Date
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<input value="<?=$allwEnd?>" type='text' class='inputs' name='txtAllwEnd' id='txtAllwEnd' maxLength='10' readonly size="10"/> 
						    <a href="#" id="allwEndDt">
						    	<img  class="btnClendar" name="imgAllwEnd" id="imgAllwEnd" type="image" src="../../../images/cal_new.png" title="End Date" 
									<?
										if($_GET['transType'] == 'Add'){
											echo "style='display:none;'";
										}
										if($_GET['transType'] == 'Edit' && $AllwPayTag == 'T'){
											echo "style='display:'';'";
										}
										else if($_GET['transType'] == 'Edit' && $AllwPayTag == 'P'){
											echo "style='display:none;'";
										}
									?>							    		
						    	>
						    </a>						
						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							 Status
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$EmpAllowObj->DropDownMenu(array(
									'A'=>'Active','H'=>'Held'
								),'cmbAllwStat',$AllwStat,'class="inputs"'
							);
							?>										
						</td>
					</tr>
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<?
								if($_GET['transType'] == 'Edit'){
									$btnMaint = 'EDIT';
								}
								if($_GET['transType'] == 'Add'){
									$btnMaint = 'ADD';
								}
							?>
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateEmpAllw(this.value,'<?=$_GET['empNo']?>')">
						</td>
					</tr>
				</TABLE>
			<INPUT type="hidden" name="hdnAllowCode" id="hdnAllowCode" value="<?=$allowType?>">
			<INPUT type="hidden" name="hdnDivideTag" id="hdnDivideTag" value="<?=$empPayType['empPayType']?>">
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	
	//disableRightClick();
	
	Calendar.setup({
			  inputField  : "txtAllwStart",      // ID of the input field
			  ifFormat    : "%Y-%m-%d",          // the date format
			  button      : "imgAllwStart"       // ID of the button
		}
	)
	
	Calendar.setup({
			  inputField  : "txtAllwEnd",      // ID of the input field
			  ifFormat    : "%Y-%m-%d",          // the date format
			  button      : "imgAllwEnd"       // ID of the button
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
		var todayDate = new Date();
		var parseStart = Date.parse(empAllw['txtAllwStart']);
		var parseEnd = Date.parse(empAllw['txtAllwEnd']);
		
		if(empNo == ''){
			alert('Employee is Required');
			location.href='profile_maintenance.employee.php';
			return false;
		}
		if(empAllw['cmbAllowType'] == 0){
			alert('Allowance Type is Required');
			$('cmbAllowType').focus();
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
		if(empAllw['cmbAllwSked'] == 0){
			alert('Allowance Schedule is Required');
			$('cmbAllwSked').focus();
			return false;			
		}		

		if(empAllw['cmbAllwPayTag'] == 'T'){
			if(empAllw['txtAllwStart'] == ''){
				alert('Allowance Start Date is Required');
				$('allwStrtDt').focus();
				return false;				
			}
			if(empAllw['txtAllwEnd'] == ''){
				alert('Allowance End Date is Required');
				$('allwEndDt').focus();
				return false;				
			}
		}	
		
		if(parseStart > parseEnd) {
			alert("Start Date must not be greater than to End Date.");
			$('allwStrtDt').focus();
			return false;
		}
		
		if(empAllw['cmbAllwPayTag'] == 'P'){
			if(empAllw['txtAllwStart'] == ''){
				alert('Allowance Start Date is Required');
				$('allwStrtDt').focus();
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
	
	function reptType(val){
		if(val == 'D'){
			$('lblCaption').innerHTML='Amount should be in Daily Basis.';
		}
		else{
			$('lblCaption').innerHTML='Amount should be in Monthly Basis.';
		}
		
	}
</SCRIPT>