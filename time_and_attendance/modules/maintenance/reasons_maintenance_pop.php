<?
/*
	Date Created	:	072010
	Created By		:	Genarra Arong
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_obj.php");

$reason = new maintenanceObj();
$sessionVars = $reason->getSeesionVars();
$reason->validateSessions('','MODULES');


switch($_GET["modAction"])
{
	case "Add":
		$getLastReasonId = $reason->getShiftInfo("tblTK_Reasons", "", " order by reason_id desc");
		$newReasonId = $getLastReasonId["reason_id"] + 1;
		$resId = ($newReasonId<10?"0".$newReasonId:$newReasonId);
		
		$btnName = "Save";
	break;
	
	case "Edit":
		$arr_Reason = $reason->getShiftInfo("tblTK_Reasons", " and reason_id='".$_GET["resCode"]."'", " ");
		$resId= $arr_Reason["reason_id"];
		$txtResDesc= $arr_Reason["reason"];
		$cmbResStat = $arr_Reason["stat"];
		$changeShift = $arr_Reason['changeShift'];
		$changeRestDay = $arr_Reason['changeRestDay'];
		$leaveApp = $arr_Reason['leaveApp'];
		$obApp = $arr_Reason['obApp'];
		$ovApp = $arr_Reason['ovApp'];
		$underTime = $arr_Reason['underTime'];
		
		$readonly = "readonly";
		$btnName = "Update";
	break;
	
	
}


?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
        <script type="text/javascript" src="../../../includes/calendar.js"></script>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	<BODY>
    	
		<FORM name="frmReasons" id="frmReasons" action="<?=$_SERVER['PHP_SELF']?>" method="post">
		  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
		  <tr>
            <td align='center' colspan='6' class='prevEmpHeader'> Reasons </td>
          </tr>
          <tr>
            <td width='40%' class='gridDtlLbl' align='left'>Reason/Remarks</td>
            <td width='1%' class='gridDtlLbl' align='center'>:</td>
            <td  width='60%' class='gridDtlVal' colspan="3"><input type='text' class='inputs' name='txtReason' id='txtReason' style='width:80%;' <?=$readonly?> value='<?=$txtResDesc?>' maxlength="50">
              <input type="hidden" name="action" id="action" value="<?=$btnName?>">
              <input type='hidden' class='inputs' name='txtResCode' id='txtResCode' style='width:20%;' readonly value=<?=$resId?> ></td>
          </tr>
          <tr>
            <td colspan="3" align='Center' class='gridDtlLbl'>Showed in:</td>
            </tr>
          <tr>
            <td class='gridDtlLbl' align='left'>Change Shift Application</td>
            <td class='gridDtlLbl' align='center'>:</td>
            <td class='gridDtlVal'><input name="chkChangeShift" type="checkbox" id="chkChangeShift" <?=($changeShift!=""?"checked": "Unchecked");?>></td>
          </tr>
          <tr>
            <td class='gridDtlLbl' align='left'>Change Rest Day Application</td>
            <td class='gridDtlLbl' align='center'>:</td>
            <td class='gridDtlVal'><input type="checkbox" name="chkChangeRestDay" id="chkChangeRestDay" <?=($changeRestDay!=""?"checked": "Unchecked");?>></td>
          </tr>
          <tr>
            <td class='gridDtlLbl' align='left'>Leave Application</td>
            <td class='gridDtlLbl' align='center'>:</td>
            <td class='gridDtlVal'><input type="checkbox" name="chkLeaveApp" id="chkLeaveApp" <?=($leaveApp!=""?"checked": "Unchecked");?>></td>
          </tr>
          <tr>
            <td class='gridDtlLbl' align='left'>Official Business Application</td>
            <td class='gridDtlLbl' align='center'>:</td>
            <td class='gridDtlVal'><input type="checkbox" name="chkOB" id="chkOB" <?=($obApp!=""?"checked": "Unchecked");?>></td>
          </tr>
          <tr>
            <td class='gridDtlLbl' align='left'>Overtime Application</td>
            <td class='gridDtlLbl' align='center'>:</td>
            <td class='gridDtlVal'><input type="checkbox" name="chkOT" id="chkOT" <?=($ovApp!=""?"checked": "Unchecked");?>></td>
          </tr>
          <tr>
            <td class='gridDtlLbl' align='left'>Undertime Application</td>
            <td class='gridDtlLbl' align='center'>:</td>
            <td class='gridDtlVal'><input type="checkbox" name="chkUT" id="chkUT" <?=($underTime!=""?"checked": "Unchecked");?>></td>
          </tr>
          <tr>
            <td colspan="3" align='left' class='gridDtlLbl'>&nbsp;  </td>
            </tr>
          <tr>
            <td width='40%' class='gridDtlLbl' align='left'>Status </td>
            <td width='1%' class='gridDtlLbl' align='center'>:</td>
            <td  width='60%' class='gridDtlVal'><?php 
                            $reason->DropDownMenu(array('A'=>'Active','H'=>'Held'),'cmbResStat',$cmbResStat,'class="inputs" style="width:145px;"');
                        ?></td>
          </tr>
          <tr>
            <td colspan="6"  class='childGridFooter' align="center"><input type='button' class= 'inputs' name='btnUserDef' value='<?=$btnName?>' onClick="saveReason();"></td>
          </tr>
          </TABLE>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	function saveReason()
	{
		var attResInputs = $('frmReasons').serialize(true);
		
		
		if(attResInputs["txtResDesc"]=="")
		{
			alert('Reason is required.');
			$('txtResson').focus();
			return false;
		}
		
		if(attResInputs["action"]=='Update')
		{
			var confirmUser = confirm("Are you sure you want to Update the selected Reason?");
			if(confirmUser==true)
			{
				params = 'reasons_maintenance.php';
				new Ajax.Request(params,{
					method : 'get',
					parameters : $('frmReasons').serialize(),
					onComplete : function (req){
						eval(req.responseText);
					}	
				});
			}
			else
			{
				return false;
			}
		}
		else
		{
			params = 'reasons_maintenance.php';
			new Ajax.Request(params,{
				method : 'get',
				parameters : $('frmReasons').serialize(),
				onComplete : function (req){
					eval(req.responseText);
				}	
			});
		}
		
	}
</SCRIPT>