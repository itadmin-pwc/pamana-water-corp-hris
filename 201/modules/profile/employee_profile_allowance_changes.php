<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("employee_profile_allowance.obj.php");
if($_GET['transType'] == 'Edit'){
	$btnMaint = 'EDIT';
	$disabledtype=" disabled ";
}
if($_GET['transType'] == 'Add'){
	$btnMaint = 'ADD';
	$disabled=" disabled ";
}
if ($_GET['effectivitydate'] != '') {
	$effDate=date('Y-m-d',strtotime($_GET['effectivitydate']));
} else {
	$effDate=date('Y-m-d');
}

$empProfileAllowObj = new empProfileAllowanceObj($_GET);
$sessionVars = $empProfileAllowObj->getSeesionVars();
$empProfileAllowObj->validateSessions('','MODULES');
$empProfileAllowObj->compCode = $sessionVars['compCode'];
$empProfileAllowObj->empNo    = $_SESSION['strprofile'];

//$userInfo = $empProfileAllowObj->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');

switch ($_GET['action']){
	case 'ADD':
			$resEmpProfCheck=$empProfileAllowObj->empProfileCheckAllowance($empProfileAllowObj->compCode,$empProfileAllowObj->empNo);
			if($empProfileAllowObj->getRecCount($resEmpProfCheck)>0){
				echo "alert('Allowance for the Employee Already Exist');";
				exit();				
			}
			else{
				$resEmpProf=$empProfileAllowObj->toEmpProfileAllowance($empProfileAllowObj->compCode,$empProfileAllowObj->empNo);
				if($resEmpProf){
					echo "alert('Employee Allownance Successfully Saved.');";
					}
				else{
					echo "alert('Employee Allownance Failed to Saved.');";
					}
			}
		exit();
	break;
	
	case 'EDIT':
			$resChecker=$empProfileAllowObj->recordChecker("Select * from tblAllowance_New where empNo='" . $_GET['empNo'] . "' and compCode='" . $empProfileAllowObj->compCode . "' and allowCode='" . $_GET['AllowType'] . "' and allowSeries<>'" . $_GET['seriesNo'] . "'");
			if($resChecker){
				echo "alert('Allowance for the Employee Already Exist');";
				exit();					
				}
			else{	
				$resEmpProfEdit=$empProfileAllowObj->updateEmpProfileAllowance($empProfileAllowObj->compCode,$_GET['empNo'],$_GET['seriesNo']);
				if($resEmpProfEdit){
					echo "alert('Employee Allownance Successfully Updated.');";
					}
				else{
					echo "alert('Employee Allownance Failed to Update.');";
					}
			}
		exit();
	break;
		
	}

if($_GET['action']=="getPayTransType"){
		$arr_allowType = $empProfileAllowObj->getAllowSked($_GET['val']);
		foreach($arr_allowType as $valAllowType=>$allowType){
			$allwSked = $allowType ["allowSked_type"];
		}
		echo "<span>";
			if($allwSked==1){
				echo "1st Payroll of Month";	
			}
			if($allwSked==2){
				echo "2nd Payroll of Month";	
			}
			if($allwSked==3){
				echo "Attendance Based";	
			}
		echo "</span>";
//		echo $empProfileAllowObj->DropDownMenu(array('','1st Payroll of Month','2nd Payroll of Month','Attendance based'),'cmbAllwSkedDrop',$allwSked,'class="inputs"  onchange="validateSched(this.value)"');
		echo "<input type=\"hidden\" name=\"txtallwsked\" id=\"txtallwsked\" value=".$allwSked.">";
		exit();
	}

if($_GET['action']=="getAllowTag"){
		$arr_allowType = $empProfileAllowObj->getAllowSked($_GET['val']);
		foreach($arr_allowType as $valAllowType=>$allowType){
			$allowTag = $allowType ["allowTag_type"];
		}
		echo $empProfileAllowObj->DropDownMenu(array('','M'=>'MONTHLY','D'=>'DAILY'),'allowTagTxt',$allowTag,'class="inputs" disabled="disabled"');
		echo '<input type="hidden" id="txtallowtag" name="txtallowtag" value="'.$allowTag.'"/>';
		exit();
	}
	
if($_GET['transType']=='Edit'){
	$res=$empProfileAllowObj->getSpecificEmpProfAllow(" where allowSeries='{$_GET['allwSeries']}'");
	foreach($res as $resqry=>$specificAllowance){
			//echo $specificAllowance['allowCode'];
			$allwcode=$specificAllowance['allowCode'];
			$allwAmount=$specificAllowance['allowAmt'];
			$AllwPayTag=$specificAllowance['allowPayTag'];
			$allwStart=$empProfileAllowObj->dateFormat($specificAllowance['allowStart']);
			//$allwEnd=$empProfileAllowObj->dateFormat($specificAllowance['allowEnd']);
			$AllwStat=$specificAllowance['allowStat'];
			$allwSked=$specificAllowance['allowSked'];
			$allowTag=$specificAllowance['allowTag'];
		}
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
		<STYLE TYPE="text/css" MEDIA="screen">
		@import url("../../../includes/calendar/calendar-blue.css");.headertxt {font-family: verdana; font-size: 11px;}
        </STYLE>
		<!--end calendar lib-->
</HEAD>
	<BODY>
		<FORM name="frmEmpProfileAllow" id="frmEmpProfileAllow" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" height="95%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td class="gridDtlLbl" align="left" width="31%">
							Allowance Type						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td width="68%" class="gridDtlVal">
							<?
							$empProfileAllowObj->DropDownMenu($empProfileAllowObj->makeArr(
								$empProfileAllowObj->getAllowType($sessionVars['compCode']),'allowCode','allowDesc',''),
								'AllowType',$allwcode,'class="inputs" '.$disabled_allowType.' onChange="GetPayTransTypeData(this.value); getAllowanceTag(this.value);" ' 
							);
							?>						<input type="hidden" value="<?=$_GET['code']?>" name="code" id="code"><input type="hidden" value="<?=$allowType?>" name="allow_edit_code" id="allow_edit_code"></td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Amount						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<INPUT type="text" name="txtAllwAmount" id="txtAllwAmount" class="inputs" value="<?=(float)$allwAmount;?>">						</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Schedule						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><div id="divAllowances">
                        <span>
                        <?
						if($allwSked==1){
							echo "1st Payroll of Month";	
						}
						if($allwSked==2){
							echo "2nd Payroll of Month";	
						}
						if($allwSked==3){
							echo "Attendance Based";	
						}
						?>                        						
					    </span>
						<input type="hidden" name="txtallwsked" id="txtallwsked" value="<?=$allwSked?>">
                        </div>				
                            	</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Allowance Tag
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><div id="divAllowTag">
							<?
							$empProfileAllowObj->DropDownMenu(array('','M'=>'MONTHLY','D'=>'DAILY'),'allowTagTxt',$allowTag,'class="inputs" disabled="disabled"');
							?>
                            <input type="hidden" id="txtallowtag" name="txtallowatg" value="<?=$allowTag;?>"/>
                            </div>			
                      	</td>
					</tr>
<!--					<tr>
						<td class="gridDtlLbl" align="left" >
							Tax Tag
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$empProfileAllowObj->DropDownMenu(array(
									'N'=>'Not Taxable','Y'=>'Taxable',
								),'cmbAllwTaxTag',$AllwTaxTag,'class="inputs"'
							);
							?>									
						</td>
					</tr>-->
					<tr>
						<td class="gridDtlLbl" align="left" >
							 Start Date						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><input value="<?=$allwStart?>" type='text' class='inputs' name='txtAllwStart' id='txtAllwStart' maxLength='10' readonly size="10"/>
						  <a href="#" id="allwStrtDt">
						    	<img class="btnClendar" name="imgAllwStart" id="imgAllwStart" type="image" src="../../../images/cal_new.png" title="Start Date"
									<?
										if($_GET['transType'] == 'edit' && $AllwPayTag == 'T'){
											echo "style='display:'';'";
										}
									?>
						    	>						    </a>						<input type="hidden" value="<?=date('Y-m-d');?>" name="today" id="today">
                                <input type="hidden" value="0" name="prtag" id="prtag"></td>
					</tr>
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateEmpAllw(this.value,'<?=$_GET['empNo'];?>','<?=$_GET['allwSeries'];?>')">						</td>
					</tr>
				</TABLE>
<INPUT type="hidden" name="hdnAllowCode" id="hdnAllowCode" value="<?=$allowType?>">
			<INPUT type="hidden" name="hdnDivideTag" id="hdnDivideTag" value="<?=$empPayType['empPayType']?>">
		</FORM>
	</BODY>
</HTML>
<SCRIPT>

	function GetPayTransTypeData(val){
	new Ajax.Request(
		'<?=$_SERVER['PHP_SELF']?>?action=getPayTransType&val='+val,
		{
		asynchronous	:	true,
		onCreate : function(){
			$('divAllowances').innerHTML='<img src="../../../images/wait.gif">' + ' loading...';
		},
		onComplete		:	function(req){
			$('divAllowances').innerHTML=req.responseText;	
			}
		}
	);
	}

	function getAllowanceTag(val){
	new Ajax.Request(
		'<?=$_SERVER['PHP_SELF']?>?action=getAllowTag&val='+val,
		{
		asynchronous	:	true,
		onCreate : function(){
			$('divAllowTag').innerHTML='<img src="../../../images/wait.gif">' + ' loading...';
		},
		onComplete		:	function(req){
			$('divAllowTag').innerHTML=req.responseText;
			}
		}
	);
	}


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
			  ifFormat    : "%Y-%m-%d",          // the date format
			  button      : "imgAllwStart"       // ID of the button
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
	
	function validateEmpAllw(act,empNo,seriesNo){

		var empAllw = $('frmEmpProfileAllow').serialize(true);
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2})$/;
		
//		if(empNo == ''){
//			alert('Employee is Required');
//			location.href='maintenance.employee.php';
//			return false;
//		}
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
		
		if(empAllw['txtAllwStart'] == ''){
			alert('Allowance Start Date is Required');
			$('txtAllwStart').focus();
			return false;				
		}	
//		if(empAllw['prtag'] == "1"){
//			var todayDate = empAllw['today'];
//			if (empAllw['effetivitydate'] != todayDate) {
//				alert('Effectivity date must be equal to current date.');
//				return false;
//			}			
//			var update = confirm('Are you sure you want to update now?');
//			if (update == false) {
//				return false;
//			}	
//		}	
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+empAllw['btnMaint']+"&empNo="+empNo+'&seriesNo='+seriesNo,{
			method : 'get',
			parameters : $('frmEmpProfileAllow').serialize(),
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
	
</SCRIPT>