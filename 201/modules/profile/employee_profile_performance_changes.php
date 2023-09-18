<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("employee_profile_performance_obj.php");
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

$empProfilePerformanceObj = new empProfilePerformanceObj($_GET);
$sessionVars = $empProfilePerformanceObj->getSeesionVars();
$empProfilePerformanceObj->validateSessions('','MODULES');
$empProfilePerformanceObj->compCode = $sessionVars['compCode'];
$empProfilePerformanceObj->empNo    = $_SESSION['strprofile'];

if($_GET['transType'] == 'Add'){
	$sqlpaf="Select max(refno)as a  from tblPAF_PayrollRelatedhist where empNo='".$empProfilePerformanceObj->empNo."' and old_empDrate Is Not Null";
	$sqlres=$empProfilePerformanceObj->execQry($sqlpaf);
	if($empProfilePerformanceObj->getRecCount($sqlres)>0){
		$oldSalary=$empProfilePerformanceObj->getSqlAssoc($sqlres);
		$sqlSalres="Select * from tblPAF_PayrollRelatedhist where refNo='".$oldSalary['a']."'";
		$sql=$empProfilePerformanceObj->execQry($sqlSalres);
		if($empProfilePerformanceObj->getRecCount($sql)>0){
			$getRate=$empProfilePerformanceObj->getSqlAssoc($sql);
			$showRateOld=$getRate['old_empDrate'];
			$showRateNew=$getRate['new_empDrate'];
		}
	}
}


//$userInfo = $empProfilePerformanceObj->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');

switch ($_GET['action']){
	case 'ADD':
				$dataChecker=$empProfilePerformanceObj->recordChecker("Select * from tblPerformance where compCode='".$empProfilePerformanceObj->compCode."' and empNo='".$empProfilePerformanceObj->empNo."' and performanceFrom='".$_GET['txtPerformanceStart']."' and performanceTo='".$_GET['txtPerformanceEnd']."' and performanceNumerical='".$_GET['cmbNumerical']."' and performancePurpose='".$_GET['cmbPurpose']."'");
				if($dataChecker){
						echo "alert('Employee Performance Already Exist.');";
						exit();
					}
				else{	
					$resEmpPerformance=$empProfilePerformanceObj->toPerformance($empProfilePerformanceObj->compCode,$empProfilePerformanceObj->empNo);
					if($resEmpPerformance){
						echo "alert('Employee Performance Successfully Saved.');";
						}
					else{
						echo "alert('Employee Performance Failed to Saved.');";
						}
				}
		exit();
	break;
	
	case 'EDIT':
				$dataChecker=$empProfilePerformanceObj->recordChecker("Select * from tblPerformance where compCode='".$empProfilePerformanceObj->compCode."' and empNo='".$empProfilePerformanceObj->empNo."' and performanceFrom='".$_GET['txtPerformanceStart']."' and performanceTo='".$_GET['txtPerformanceEnd']."' and performanceNumerical='".$_GET['cmbNumerical']."' and performancePurpose='".$_GET['cmbPurpose']."' and performance_Id!='".$_GET['perid']."'");
			if($dataChecker){
				echo "alert('Employee Performance Already Exist');";
				exit();					
				}
			else{	
				$resEmpPerformanceEdit=$empProfilePerformanceObj->updatePerformance($empProfilePerformanceObj->compCode,$_GET['empNo'],$_GET['perid']);
				if($resEmpPerformanceEdit){
					echo "alert('Employee Performance Successfully Updated.');";
					}
				else{
					echo "alert('Employee Performance Failed to Update.');";
					}
			}
		exit();
	break;
		
	}

	
if($_GET['transType']=='Edit'){
	$res=$empProfilePerformanceObj->getSpecificEmpPerformance(" where performance_Id='{$_GET['performanceid']}'");
	foreach($res as $resqry=>$specificPerformance){
			$performanceFrom=$empProfilePerformanceObj->dateFormat($specificPerformance['performanceFrom']);
			$performanceTo=$empProfilePerformanceObj->dateFormat($specificPerformance['performanceTo']);		
			$performanceNumerical=$specificPerformance['performanceNumerical'];
			$performanceAdjective=$specificPerformance['performanceAdjective'];
			$performancePurpose=$specificPerformance['performancePurpose'];
			$showRateOld=$specificPerformance['old_empDrate'];
			$showRateNew=$specificPerformance['new_empDrate'];
			$performanceRemarks=$specificPerformance['remarks'];
		}
	}	

if($_GET['act']=="valrate"){
	echo $empProfilePerformanceObj->DropDownMenu(array('','Outstanding','Above Average','Average','Below Average','Poor'),'cmbAdjective',$_GET['rateVal'],'class="inputs" disabled');
	exit();
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
		<FORM name="frmEmpPerformance" id="frmEmpPerformance" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" height="95%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td class="gridDtlLbl" align="left" width="28%">From						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td width="71%" class="gridDtlVal"><input value="<?=$performanceFrom?>" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmEmpPerformance.txtPerformanceEnd.value);" class='inputs' name='txtPerformanceStart' id='txtPerformanceStart' maxLength='10' readonly size="10"/>
						  <a href="#" id="allwStrtDt">
						    	<img class="btnClendar" name="imgPerformanceStart" id="imgPerformanceStart" type="image" src="../../../images/cal_new.png" title="Start Date"></a>	</td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							To						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><input value="<?=$performanceTo?>" type='text' class='inputs' name='txtPerformanceEnd' onChange="valDateStartEnd(document.frmEmpPerformance.txtPerformanceStart.value,document.frmEmpPerformance.txtPerformanceStart.id,this.value);" id='txtPerformanceEnd' maxLength='10' readonly size="10"/>
                        <a href="#" id="allwEndDt"><img  class="btnClendar" name="imgPerformanceEnd" id="imgPerformanceEnd" type="image" src="../../../images/cal_new.png" title="End Date"></a></td>
					</tr>
					<tr>
					  <td align="left" class="gridDtlLbl" >Numeric Rating</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal">
					    <?
							$empProfilePerformanceObj->DropDownMenu(array(
									'','96% - 100%','91% - 95%','85% - 90%','80% - 84%','80% and below'
								),'cmbNumerical',$performanceNumerical,'class="inputs" onchange="validateRating(this.value)"'
							);
							?>
				      </td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Adjective	Rating					</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><div id="divAdjective">
                        	
							<?
							$empProfilePerformanceObj->DropDownMenu(array(
									'','Outstanding','Above Average','Average','Below Average','Poor'
								),'cmbAdjective',$performanceAdjective,'class="inputs" disabled'
							);
							?>	</div>				
                            	</td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Purpose						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$empProfilePerformanceObj->DropDownMenu(array('','Probationary','Regularization','Merit Increase','Salary Alignment','Promotion'),'cmbPurpose',$performancePurpose,'class="inputs"');
							?></td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >Old Daily Rate</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><span><?=$showRateOld? $showRateOld : "0.00" ."/Day";?></span>
                        <input type="hidden" id="txtoldemprate" name="txtoldemprate" value="<?=$showRateOld;?>">
                        </td>
					</tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >
							New	Daily	Rate			</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><?=$showRateNew? $showRateNew : "0.00" ."/Day";?>
                         <input type="hidden" id="txtnewemprate" name="txtnewemprate" value="<?=$showRateNew;?>">
                        </td>
					</tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >Remarks</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><textarea name="txtremarks" cols="30" rows="2" id="txtremarks"><?=$performanceRemarks?>
						</textarea>
                        </td>
					</tr>
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validatePerformance(this.value,'<?=$_GET['empNo'];?>','<?=$_GET['performanceid']?>')">						</td>
					</tr>
				</TABLE>
<INPUT type="hidden" name="hdnAllowCode" id="hdnAllowCode" value="<?=$allowType?>">
			<INPUT type="hidden" name="hdnDivideTag" id="hdnDivideTag" value="<?=$empPayType['empPayType']?>">
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	
	Calendar.setup({
			  inputField  : "txtPerformanceStart",      // ID of the input field
			  ifFormat    : "%Y-%m-%d",          // the date format
			  button      : "imgPerformanceStart"       // ID of the button
		}
	)
	
	Calendar.setup({
			  inputField  : "txtPerformanceEnd",      // ID of the input field
			  ifFormat    : "%Y-%m-%d",          // the date format
			  button      : "imgPerformanceEnd"       // ID of the button
		}
	)
	
	function validateRating(rateVal){
		var params='employee_profile_performance_changes.php?act=valrate&rateVal='+rateVal;
		new Ajax.Request(params,{
			method : 'get',
			asynchronous : true,
			parameters : $('frmEmpPerformance').serialize(true),
			onCreate : function (){
				$('divAdjective').innerHTML='loading........';
				},
			onComplete : function(req){
				$('divAdjective').innerHTML=req.responseText;
				}	
			});
		}
	
	function validatePerformance(act,empNo,perid){

		var empPerformance = $('frmEmpPerformance').serialize(true);
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2})$/;
		
		if(trim(empPerformance['txtPerformanceStart']) == ""){
			alert('Date From is Required');
			$('txtPerformanceStart').focus();
			return false;			
		}
		if(trim(empPerformance['txtPerformanceEnd']) == ""){
			alert('Date To is Required');
			$('txtPerformanceEnd').focus();
			return false;			
		}
		if(empPerformance['cmbNumerical']==0){
			alert('Numeric Rating is Required.');
			$('cmbNumerical').focus();
			return false;
			}		
		if(empPerformance['cmbAdjective']==0){
			alert('Adjective Rating is Required.');
			$('cmbAdjective').focus();
			return false;
			}	
		if(empPerformance['cmbPurpose']==0){
			alert('Purpose is Required.');
			$('cmbPurpose').focus();
			return false
			}	
		new Ajax.Request('employee_profile_performance_changes.php?action='+empPerformance['btnMaint']+"&empNo="+empNo+'&perid='+perid,{
			method : 'get',
			parameters : $('frmEmpPerformance').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}	
	
</SCRIPT>