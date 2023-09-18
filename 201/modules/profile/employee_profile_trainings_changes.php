<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("employee_profile_trainings_obj.php");
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

$empProfileTrainingsObj = new empProfileTrainingsObj($_GET);
$sessionVars = $empProfileTrainingsObj->getSeesionVars();
$empProfileTrainingsObj->validateSessions('','MODULES');
$empProfileTrainingsObj->compCode = $sessionVars['compCode'];
$empProfileTrainingsObj->empNo    = $_SESSION['strprofile'];

//$userInfo = $empProfileTrainingsObj->getUserInfo($sessionVars['compCode'],$_GET['empNo'],'');

switch ($_GET['action']){
	case 'ADD':
				$dataChecker=$empProfileTrainingsObj->recordChecker("Select * from tblTrainings where compCode='".$empProfileTrainingsObj->compCode."' and empNo='".$empProfileTrainingsObj->empNo."' and trainingFrom='".$_GET['txtTrainingStart']."' and trainingTo='".$_GET['txtTrainingEnd']."' and trainingTitle='".$_GET['txtTitle']."' and trainingCost='".$_GET['txtCost']."' and trainingBond='".$_GET['cmbBond']."'");
				if($dataChecker){
						echo "alert('Employee Training Already Exist.');";
						exit();
					}
				else{	
					$resEmpTraining=$empProfileTrainingsObj->toTrainings($empProfileTrainingsObj->compCode,$empProfileTrainingsObj->empNo);
					if($resEmpTraining){
						echo "alert('Employee Training Successfully Saved.');";
						}
					else{
						echo "alert('Employee Training Failed to Saved.');";
						}
				}
		exit();
	break;
	
	case 'EDIT':
				$dataChecker=$empProfileTrainingsObj->recordChecker("Select * from tblTrainings where compCode='".$empProfileTrainingsObj->compCode."' and empNo='".$empProfileTrainingsObj->empNo."' and trainingFrom='".$_GET['txtTrainingStart']."' and trainingTo='".$_GET['txtTrainingEnd']."' and trainingTitle='".$_GET['txtTitle']."' and trainingCost='".$_GET['txtCost']."' and trainingBond='".$_GET['cmbBond']."' and training_Id!='{$_GET['tid']}'");
			if($dataChecker){
				echo "alert('Employee Training Already Exist');";
				exit();					
				}
			else{	
				$resEmpTrainingEdit=$empProfileTrainingsObj->updateTrainings($empProfileTrainingsObj->compCode,$_GET['empNo'],$_GET['tid']);
				if($resEmpTrainingEdit){
					echo "alert('Employee Training Successfully Updated.');";
					}
				else{
					echo "alert('Employee Training Failed to Update.');";
					}
			}
		exit();
	break;
		
	}

	
if($_GET['transType']=='Edit'){
	$res=$empProfileTrainingsObj->getSpecificTraining(" where training_Id='{$_GET['trainingid']}'");
	foreach($res as $resqry => $specificTrainings){
			$trainingFrom=$empProfileTrainingsObj->dateFormat($specificTrainings['trainingFrom']);
			$trainingTo=$empProfileTrainingsObj->dateFormat($specificTrainings['trainingTo']);		
			$trainingTitle=$specificTrainings['trainingTitle'];
			$trainingCost=$specificTrainings['trainingCost'];
			$trainingBond=$specificTrainings['trainingBond'];
			$effectiveFrom=$empProfileTrainingsObj->dateFormat($specificTrainings['effectiveFrom']);		
			$effectiveTo=$empProfileTrainingsObj->dateFormat($specificTrainings['effectiveTo']);		
		}
	}	

if($_GET['action']=="computeeffectivity"){
	$y=$_GET['id'];
	$sdate=$_GET['trainStart'];
	$bonddate = date('Y-m-d',strtotime("$sdate +$y year"));
	echo '<input value="'.$bonddate.'" type="text" class="inputs" name="txtEffectivityEnd" id="txtEffectivityEnd" maxLength="10" size="10"/>';
	exit();
	}

if($_GET['act']=="valrate"){
	echo $empProfileTrainingsObj->DropDownMenu(array('','Outstanding','Above Average','Average','Below Average','Poor'),'cmbAdjective',$_GET['rateVal'],'class="inputs" disabled');
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
		<FORM name="frmEmpTrainings" id="frmEmpTrainings" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" height="95%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td class="gridDtlLbl" align="left" width="28%">Training From						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td width="71%" class="gridDtlVal"><input value="<?=$trainingFrom?>" type='text' onChange="valDateStartEnd(this.value,this.id,document.frmEmpTrainings.txtTrainingEnd.value); valdatabond(this.value);computeYears();" class='inputs' name='txtTrainingStart' id='txtTrainingStart' maxLength='10' readonly size="10"/>
						  <a href="#" id="allwStrtDt">
						    	<img class="btnClendar" name="imgTrainingStart" id="imgTrainingStart" type="image" src="../../../images/cal_new.png" title="Start Date"></a>	</td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Training To						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><input value="<?=$trainingTo?>" type='text' class='inputs' name='txtTrainingEnd' onChange="valDateStartEnd(document.frmEmpTrainings.txtTrainingStart.value,document.frmEmpTrainings.txtTrainingStart.id,this.value);" id='txtTrainingEnd' maxLength='10' readonly size="10"/>
                        <a href="#" id="allwEndDt"><img  class="btnClendar" name="imgTrainingEnd" id="imgTrainingEnd" type="image" src="../../../images/cal_new.png" title="End Date"></a></td>
					</tr>
					<tr>
					  <td align="left" class="gridDtlLbl" >Title</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input name="txtTitle" type="text" id="txtTitle" value="<?=$trainingTitle;?>" size="45" maxlength="200"></td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >Training Cost</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><input name="txtCost" type="text" id="txtCost" value="<?=$trainingCost;?>" size="15" maxlength="15" onKeyUp="extractNumber(this,2,true);" onKeyPress="extractNumber(this,2,true);" onKeyDown="extractNumber(this,2,true);"></td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Bond						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<?
							$empProfileTrainingsObj->DropDownMenu(array('','1 year','2 years','3 years','4 years','5 years','6 years','7 years','8 years','9 years','10 years'),'cmbBond',$trainingBond,'class="inputs" onChange="computeYears();"');
							?></td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Effective	From					</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
							<input value="<?=$effectiveFrom?>" type='text'  class='inputs' name='txtEffectivityStart' id='txtEffectivityStart' maxLength='10' size="10"/></td>
					</tr>
					<tr>
						<td class="gridDtlLbl" align="left" >
							Effective	To					</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><div id="cntYear">
							<input value="<?=$effectiveTo?>" type='text' class='inputs' name='txtEffectivityEnd' id='txtEffectivityEnd' maxLength='10' size="10"/></div></td>
					</tr>                    
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateTrainings(this.value,'<?=$_GET['empNo'];?>','<?=$_GET['trainingid']?>')">						</td>
					</tr>
				</TABLE>
<INPUT type="hidden" name="hdnAllowCode" id="hdnAllowCode" value="<?=$allowType?>">
			<INPUT type="hidden" name="hdnDivideTag" id="hdnDivideTag" value="<?=$empPayType['empPayType']?>">
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	
	Calendar.setup({
			  inputField  : "txtTrainingStart",      // ID of the input field
			  ifFormat    : "%Y-%m-%d",          // the date format
			  button      : "imgTrainingStart"       // ID of the button
		}
	)
	
	Calendar.setup({
			  inputField  : "txtTrainingEnd",      // ID of the input field
			  ifFormat    : "%Y-%m-%d",          // the date format
			  button      : "imgTrainingEnd"       // ID of the button
		}
	)
	
//	Calendar.setup({
//			  inputField  : "txtEffectivityStart",      // ID of the input field
//			  ifFormat    : "%m/%d/%Y",          // the date format
//			  button      : "imgEffectivityStart"       // ID of the button
//		}
//	)
//	
//	Calendar.setup({
//			  inputField  : "txtEffectivityEnd",      // ID of the input field
//			  ifFormat    : "%m/%d/%Y",          // the date format
//			  button      : "imgEffectivityEnd"       // ID of the button
//		}
//	)

	function computeYears(){
		var sdate=document.getElementById('txtTrainingStart').value;
		var y=document.getElementById('cmbBond').value;
		var params='employee_profile_trainings_changes.php?action=computeeffectivity&id='+y+'&trainStart='+sdate;
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmEmpTrainings').serialize(true),
			onCreate : function(){
				$('cntYear').innerHTML='computing years.....';
				},
			onComplete : function(req){
				$('cntYear').innerHTML=req.responseText;
				}	
			})
		}
	
	function valdatabond(id){
		document.getElementById('txtEffectivityStart').value=document.getElementById('txtTrainingStart').value;
		}
	function validateTrainings(act,empNo,tid){

		var empTrainings = $('frmEmpTrainings').serialize(true);
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2})$/;
		
		if(trim(empTrainings['txtTrainingStart']) == ""){
			alert('Training From is Required');
			$('txtTrainingStart').focus();
			return false;			
		}
		if(trim(empTrainings['txtTrainingEnd']) == ""){
			alert('Training To is Required');
			$('txtTrainingEnd').focus();
			return false;			
		}
		if(trim(empTrainings['txtTitle'])==""){
			alert('Training Title is Required.');
			$('txtTitle').focus();
			return false;
			}		
		if(trim(empTrainings['txtCost'])==""){
			alert('Training Cost is Required.');
			$('txtCost').focus();
			return false;
			}
		if(empTrainings['cmbBond']==0){
			alert('Training Bond is Required.');
			$('cmbBond').focus();
			return false;
			}	
		if(trim(empTrainings['txtEffectivityStart'])==""){
			alert('Effectivity Start is Required.');
			$('txtEffectivityStart').focus();
			return false;
			}	
		if(trim(empTrainings['txtEffectivityEnd'])==""){
			alert('Effectivity End is Required.');
			$('txtEffectivityEnd').focus();
			return false;
			}
		new Ajax.Request('employee_profile_trainings_changes.php?action='+empTrainings['btnMaint']+"&empNo="+empNo+'&tid='+tid,{
			method : 'get',
			parameters : $('frmEmpTrainings').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}	
	
	function extractNumber(obj, decimalPlaces, allowNegative)
	{
		var temp = obj.value;
		
		// avoid changing things if already formatted correctly
		var reg0Str = '[0-9]*';
		if (decimalPlaces > 0) {
			reg0Str += '\\.?[0-9]{0,' + decimalPlaces + '}';
		} else if (decimalPlaces < 0) {
			reg0Str += '\\.?[0-9]*';
		}
		reg0Str = allowNegative ? '^-?' + reg0Str : '^' + reg0Str;
		reg0Str = reg0Str + '$';
		var reg0 = new RegExp(reg0Str);
		if (reg0.test(temp)) return true;
	
		// first replace all non numbers
		var reg1Str = '[^0-9' + (decimalPlaces != 0 ? '.' : '') + (allowNegative ? '-' : '') + ']';
		var reg1 = new RegExp(reg1Str, '');
		temp = temp.replace(reg1, '');
	
		if (allowNegative) {
			// replace extra negative
			var hasNegative = temp.length > 0 && temp.charAt(0) == '-';
			var reg2 = /-/g;
			temp = temp.replace(reg2, '');
			if (hasNegative) temp = '-' + temp;
		}
		
		if (decimalPlaces != 0) {
			var reg3 = /\./g;
			var reg3Array = reg3.exec(temp);
			if (reg3Array != null) {
				// keep only first occurrence of .
				//  and the number of places specified by decimalPlaces or the entire string if decimalPlaces < 0
				var reg3Right = temp.substring(reg3Array.index + reg3Array[0].length);
				reg3Right = reg3Right.replace(reg3, '');
				reg3Right = decimalPlaces > 0 ? reg3Right.substring(0, decimalPlaces) : reg3Right;
				temp = temp.substring(0,reg3Array.index) + '.' + reg3Right;
			}
		}
		
		obj.value = temp;
	}
</SCRIPT>